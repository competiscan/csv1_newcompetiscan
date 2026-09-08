<?php
require_once('includes/globalSession.php');
require_once 'includes/functions.php';

if(isset($_REQUEST['pid']) && $_REQUEST['action']=='convert_image') {
    global $s3,$bucket_name,$serverbaseurl,$s3FileUrl;
    $productID = $_REQUEST['pid'];
    $dops=$_REQUEST['dops'];
    $Sql="SELECT document_id,document_filename,document_path FROM cscan_document WHERE productID='".$productID."'";
    $chk_rs = $DRW->query($Sql,$DRW_read);
    if($DRW->num_rows($chk_rs) > 0)
    {
        $rowData = $DRW->fetch_assoc($chk_rs);
        $document_id=$rowData['document_id'];
        $document_filename=$rowData['document_filename'];
        $document_path=$rowData['document_path'];
        $imgpath= $document_path.$document_filename;
        $root = dirname(__FILE__);
        $dir    =   $root.'/PDF/';
        $pdfImgeconvertimagepath="$root$document_path"."preview_single_image/";
        $info = $s3->doesObjectExist($bucket_name,substr($document_path.$document_filename,1));
        if($info){
            $expolde_path=explode('/', $document_path);
            //echo "<pre>";
            //print_r($expolde_path); die;
            $yearpath=$expolde_path[2].'/';
             if (!is_dir($dir.$yearpath)) {
                @mkdir($dir.$yearpath,02755);
                @chmod($dir.$yearpath, 02755);
                @chown($dir.$yearpath, 'apache');
            }
            $yearpath1=$expolde_path[3].'/';
             if (!is_dir($dir.$yearpath.$yearpath1)) {
                @mkdir($dir.$yearpath.$yearpath1,02755);
                @chmod($dir.$yearpath.$yearpath1, 02755);
                @chown($dir.$yearpath.$yearpath1, 'apache');
            }
             if (!is_dir($dir.$yearpath.$yearpath1.$productID)) {
                @mkdir($dir.$yearpath.$yearpath1.$productID,02755);
                @chmod($dir.$yearpath.$yearpath1.$productID, 02755);
                @chown($dir.$yearpath.$yearpath1.$productID, 'apache');
            } 

            if (!is_dir($dir.$yearpath.$yearpath1.$productID.'/preview_single_image/')) {
                @mkdir($dir.$yearpath.$yearpath1.$productID.'/preview_single_image/',02755);
                @chmod($dir.$yearpath.$yearpath1.$productID.'/preview_single_image/', 02755);
                @chown($dir.$yearpath.$yearpath1.$productID.'/preview_single_image/', 'apache');
            }
            if(strpos($document_path,'/')=='0'){
                $s3document_path  = substr($document_path,1);
            } 
            $results = $s3->getObject([
                'Bucket' => $bucket_name,
                'Key'    => $s3document_path.$document_filename,
                'SaveAs' => $pdfImgeconvertimagepath.$document_filename,

            ]); 
           //echo "<pre>";
            //print_r($results); die; 
            ConvertSingleWithPythonCode($pdfImgeconvertimagepath,$document_filename,$productID,$dops);
        }
       
    }
    echo "1";exit;
}

function ConvertSingleWithPythonCode($path,$name,$productID,$dops,$back=false,$document_id=1){
	//echo $path.$name; die;
        $dops = intval($dops);
        $output_image = shell_exec("python3 convert_back_pdftoimage_2.py $path $name $productID $document_id $dops 2>&1");
        //echo $output_image; die;
        $output_image = str_replace(array( "[", "]","'" ), '', $output_image); 
        $output_image_array = explode(",", $output_image);
        /*echo "<pre>";
        print_r($output_image_array);
        echo count($output_image_array);die; */  
        if($document_id!=0){
           savePDFToImageConvertSingle($productID,$document_id,$path,$output_image_array,$dops);
          }
        if (file_exists($path.$name)) {
            @unlink($path.$name);
        }
	
}

function savePDFToImageConvertSingle($productID, $document_id, $pdfPath,$output_image_array,$dops,$maxbytes = 500000) {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    global $s3,$bucket_name,$serverbaseurl;
    $root = dirname(__FILE__);
    if (strpos($root, '/includes') !== false) {
        $root = substr($root, 0, strpos($root, '/includes'));
    }
    if(isset($_SESSION['sess_userID'])){
    $sess_userID=$_SESSION['sess_userID'];
    } else{
       $sess_userID=''; 
    }
    $checkV = "SELECT img_document_path,img_document_filename FROM cscan_img_document_ppt WHERE productID=$productID AND document_id=$document_id";
    $checkV = $DRW->query($checkV, $DRW_read);
    while ($dataV = $DRW->fetch_row($checkV)) {
        $path = $root . $dataV[0] . $dataV[1];
        if(strpos($dataV[0],'/')=='0'){
         $dataV[0]  = substr($dataV[0],1);
        } 
        $info = $s3->doesObjectExist($bucket_name,$dataV[0] . $dataV[1]);
        if($info){
                $result = $s3->deleteObject([
                    'Bucket' => $bucket_name,
                    'Key' => $dataV[0] . $dataV[1],
                ]);
        }
        
        
        if (is_file($path) && $path != $pdfPath . $dataV[1]) {
           //@unlink($path);
        }
    }
    $sql = "DELETE FROM cscan_img_document_ppt WHERE productID=$productID AND document_id=$document_id";
    $DRW->query($sql, $DRW_main);
    $img_document_default = 1;
    $img_document_sort = 0;
    $img_document_path = strstr($pdfPath,'/PDF');
    $length_image = count($output_image_array);
    for($img=0; $img<$length_image; $img++){
        $image_name=trim($output_image_array[$img]);
        $image = $pdfPath.$image_name;
        $img_size_byte = filesize($image);
        $img_document_sort_ins = $img_document_sort + 1;
       $sql = "REPLACE INTO cscan_img_document_ppt (productID,document_id,img_document_sort,img_document_filename,img_document_createddate,img_document_content_type,img_document_size_byte,img_document_createdby,img_document_default,img_document_path)
			VALUES ($productID,$document_id,$img_document_sort_ins,'" . $DRW->real_escape_string($image_name) . "',NOW(),'image/jpeg',$img_size_byte,$sess_userID,$img_document_default,'" . $DRW->real_escape_string($img_document_path) . "')";
        $DRW->query($sql, $DRW_main);
        sendthumbimageons3($img_document_path , $DRW->real_escape_string($image_name) );
        $img_document_sort++;
        $img_document_default = 0;
    }
    $Sql_chk_user="SELECT productID FROM cscan_user_single_image_ppt WHERE productID='".$productID."' and userID='".$sess_userID."'";
    $chk_user_rs = $DRW->query($Sql_chk_user,$DRW_read);
    if($DRW->num_rows($chk_user_rs) > 0)
    {
     $sql_update = "Update cscan_user_single_image_ppt set ppt_status='".$dops."' where productID='".$productID."' and userID='".$sess_userID."'";
      $DRW->query($sql_update, $DRW_main);
    
    }else{
      $sql_insert = "INSERT INTO cscan_user_single_image_ppt (userID,productID,ppt_status)
			VALUES ($sess_userID,$productID,$dops)";
        $DRW->query($sql_insert, $DRW_main); 
    }
}
?>