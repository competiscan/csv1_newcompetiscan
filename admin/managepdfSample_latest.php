<?php
$ALLOW_GROUPS = array(4);
require_once("../auth_auth.php");
require_once '../includes/functions.php';
require_once '../includes/thumb.php';
header('Content-Type: text/html; charset=iso-8859-1');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<title>Competiscan: Enhance your competitive skill</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../includes/styleSheet.css" rel="stylesheet" type="text/css" />
</head>
<body style="background:#FAF6D2;margin:10px;">
<?php
if(isset($_REQUEST['productID'])) $productID = (int)$_REQUEST['productID'];
else $productID = 0;
if(isset($_REQUEST['did'])) $document_id = (int)$_REQUEST['did'];
else $document_id = 1;

$maxbytes = 500000;
$root = dirname(__FILE__);
$root = substr($root,0,strpos($root,'/admin'));
if(!defined('ENV')){
    define('ENV',getenv('SERVER_NAME'));
} 


if(isset($_GET['next'])){
	$sql2 = "SELECT document_size_byte FROM cscan_document WHERE productID=$productID AND document_id=$document_id";
	$rs2 = $DRW->query($sql2,$DRW_read);
	$row2 = $DRW->fetch_row($rs2);
	$document_size_byte = (int)$row2[0];
	$sizeofPDFinKB=$document_size_byte/1024;
	$sizeofPDFinMB=$sizeofPDFinKB/1024;
	if($sizeofPDFinMB<1) {
		$DisplaySize=round($sizeofPDFinKB,2)." KB";  
	}
	else {
		$DisplaySize=round($sizeofPDFinMB,2)." MB";  
	}
	echo '<div><strong>Product File Size:</strong> '.$DisplaySize.'</div>
	<div>&nbsp;</div>
	<div><div><strong>Product Image:</strong></div><img src="../productImg_latest.php?id='.$productID.'&amp;new='.date('YmdHis').'" border="0" style="border:solid 1px #000000;" id="prod_img" width="150" height="100" /></div>
	<div>&nbsp;</div>';
	
	$button = '<div><a href="addproduct.php?id='.$productID.'">Back</a> &nbsp; | &nbsp; <a href="'.$_GET['next'].'">Continue</a><div>';
	echo $button.'<div>&nbsp;</div>';
	$eget = '&next='.$_GET['next'];
}
else{
	$button = '<div><a href="#" onclick="self.close(); return false;">close</a></div>';
	$eget = '';
}
if($productID!=0){
	/*$pdfQuery = "SELECT img_document_sort,do.document_id,document_path,document_filename,document_content_type  FROM cscan_document do LEFT JOIN cscan_img_document im ON(do.productID=im.productID AND do.document_id=im.document_id AND img_document_default=1)
		WHERE do.productID=$productID AND do.document_id=$document_id";
         */
         
        $pdfQuery = "SELECT img_document_sort,do.document_id,document_path,document_filename,document_content_type,img_document_filename,img_document_path,img_document_content_type  FROM cscan_document do LEFT JOIN cscan_img_document im ON(do.productID=im.productID AND do.document_id=im.document_id AND img_document_default=1)
		WHERE do.productID=$productID AND do.document_id=$document_id";
	$pdfQuery = $DRW->query($pdfQuery,$DRW_read);
	$pdf_rs = $DRW->fetch_row($pdfQuery);
	$img_document_sort = (int)$pdf_rs[0];
	$document_id = (int)$pdf_rs[1];
	$document_path = $pdf_rs[2];
	$document_filename = $pdf_rs[3];
        $document_content_type = $pdf_rs[4];
	$imagePath = $root.$document_path;
        $img_document_filename = $pdf_rs[5];
        $img_document_path = $pdf_rs[6];
        $img_document_content_type = $pdf_rs[7];
	
	if($document_id==1){
		echo '<div class="bodytext" style="font-weight:bold;">Click on an image to select Preview:</div>';
		if(isset($_GET['re'])){
                    
                    $dir    =   $root.'/PDF/';
                    $info = $s3->doesObjectExist($bucket_name,substr($document_path.$document_filename,1));
                        if($info){
                            $expolde_path=explode('/', $document_path);
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
                           $dest = $imagePath.$document_filename;
                           $pdfs3newPath = "PDF/".$yearpath.$yearpath1.$productID."/".$document_filename."/";
                             // Where the files will be transferred to
                           // $source = $s3URL.$bucket_name.'/'.substr($document_path,1).$document_filename; 
                          $source = $displays3URL.substr($document_path,1).$document_filename; 
                           if($info && copy($source, $dest)){
                                 echo"success"."<br>";
                             }                   

                        } 
                    
			if($_GET['re']==3){ 
                             /*############### Create image from html file and upload at s3 ###############*/
                               $htmls3newPath = substr($document_path,1);
                               if($document_content_type=='text/html'){
                                  /*############### Create image from html file and upload at s3 ###############*/
                                   $html_url=$serverbaseurl.'/'.substr($document_path,1).$document_filename; 
                                   $output_path=$serverbaseurl.'/'.substr($document_path,1).$productID.'0.jpg'; 
                                   $s=exec("wkhtmltoimage --zoom 0.2 --height 400 --width 500 ".$html_url.' '.$output_path." 2>&1", $output);
                                   if (((stripos(json_encode($output),'Done') !== false) && (stripos(json_encode($output),'Error') == false) && (stripos(json_encode($output),'100%') !== false) ) || file_exists($output_path))
                                     {
                                       //echo 'Success';
                                       $result_s3 = $s3->putObject([
                                           'Bucket' => $bucket_name,
                                           'Key'    => $htmls3newPath . $productID.'0.jpg',
                                           'SourceFile' => $output_path,
                                           'ACL'    => 'public-read',
                                           'ContentType'	=> 'image/jpeg',
                                           'Metadata'      => array(
                                              'string'        => 'string'
                                            )
                                       ]);
                                       $img_size_byte = filesize($output_path);
                                       $img_document_sort_ins = 1;
                                       $img_document_default = 1;
                                       $document_id=1;
                                       $sql = "REPLACE INTO cscan_img_document (productID,document_id,img_document_sort,img_document_filename,img_document_createddate,img_document_content_type,img_document_size_byte,img_document_createdby,img_document_default,img_document_path)
                                                       VALUES ($productID,$document_id,$img_document_sort_ins,'" . $DRW->real_escape_string($productID .'0.jpg') . "',NOW(),'image/jpeg',$img_size_byte,{$GLOBALS['AUTH_DATA']['userID']},$img_document_default,'" . $DRW->real_escape_string('/'.$htmls3newPath) . "')";
                                       $DRW->query($sql, $DRW_main);

                                       if (is_file($output_path)) {            
                                          unlink($output_path);
                                       }

                                     }

                                   /*############### End create image from html file and upload at s3 ###############*/ 
                               } else{
                               
                                /*if(ENV == 'localhost' || ENV == 'demo.competiscan.com'){    
                                     resizeS3ImageLambda($productID,3);
                                }else{*/
                                   // for using social media image conversion to old process
                                    $sql_query = "select mChannelID from  cscan_product_detail where productID=$productID";   
                                    $sql_query_result = $DRW->query($sql_query,$DRW_read);
                                    $numrows2=$DRW->num_rows($sql_query_result); 
                                    $mChannelID='';
                                    if($numrows2>0){
                                        $MChannelData=$DRW->fetch_array($sql_query_result);
                                        $mChannelID=$MChannelData['mChannelID'];
                                    }
                                    if($mChannelID==6){
                                        createPreviewJPG1($imagePath,$document_filename,$productID,false,2,$document_id); 
                                     }else{
                                      createPreviewJPGWithPythonCode($imagePath,$document_filename,$productID,false,2,$document_id);
                                     }
                                   //createPreviewJPG($imagePath,$document_filename,$productID,false,2,$document_id);
                                //}
                               }
			}
			elseif($_GET['re']==2){
				//createPreviewJPG($imagePath,$document_filename,$productID,false,true,$document_id);
			}
			else{
                             /*############### Create image from html file and upload at s3 ###############*/
                               $htmls3newPath = substr($document_path,1);
                               if($document_content_type=='text/html'){
                                 
                                   $html_url=$serverbaseurl.'/'.substr($document_path,1).$document_filename; 
                                   $output_path=$serverbaseurl.'/'.substr($document_path,1).$productID.'0.jpg'; 
                                    $s=exec("wkhtmltoimage --zoom 0.2 --height 400 --width 400 ".$html_url.' '.$output_path." 2>&1", $output);
                                   if (((stripos(json_encode($output),'Done') !== false) && (stripos(json_encode($output),'Error') == false) && (stripos(json_encode($output),'100%') !== false) ) || file_exists($output_path))
                                     {
                                       //echo 'Success';
                                       $result_s3 = $s3->putObject([
                                           'Bucket' => $bucket_name,
                                           'Key'    => $htmls3newPath . $productID.'0.jpg',
                                           'SourceFile' => $output_path,
                                           'ACL'    => 'public-read',
                                           'ContentType'	=> 'image/jpeg',
                                           'Metadata'      => array(
                                              'string'        => 'string'
                                            )
                                       ]);

                                       $img_size_byte = filesize($output_path);
                                       $img_document_sort_ins = 1;
                                       $img_document_default = 1;
                                       $document_id=1;
                                       $sql = "REPLACE INTO cscan_img_document (productID,document_id,img_document_sort,img_document_filename,img_document_createddate,img_document_content_type,img_document_size_byte,img_document_createdby,img_document_default,img_document_path)
                                                       VALUES ($productID,$document_id,$img_document_sort_ins,'" . $DRW->real_escape_string($productID .'0.jpg') . "',NOW(),'image/jpeg',$img_size_byte,{$GLOBALS['AUTH_DATA']['userID']},$img_document_default,'" . $DRW->real_escape_string('/'.$htmls3newPath) . "')";
                                       $DRW->query($sql, $DRW_main);

                                       if (is_file($output_path)) {            
                                          unlink($output_path);
                                       }

                                     }

                                   /*############### End create image from html file and upload at s3 ###############*/ 
                               } else{
                                    /*if(ENV == 'localhost' || ENV == 'demo.competiscan.com'){    
                                        resizeS3ImageLambda($productID,1);
                                    }else{*/
                                   // for using social media image conversion to old process
                                    $sql_query = "select mChannelID from  cscan_product_detail where productID=$productID";   
                                    $sql_query_result = $DRW->query($sql_query,$DRW_read);
                                    $numrows2=$DRW->num_rows($sql_query_result); 
                                    $mChannelID='';
                                    if($numrows2>0){
                                        $MChannelData=$DRW->fetch_array($sql_query_result);
                                        $mChannelID=$MChannelData['mChannelID'];
                                    }
                                    if($mChannelID==6){
                                        createPreviewJPG1($imagePath,$document_filename,$productID,false,false,$document_id);
                                    }else{
                                        createPreviewJPGWithPythonCode($imagePath,$document_filename,$productID,false,false,$document_id);
                                   }
				
                               }
				
			}
			if (file_exists($dest) ) {
                                @unlink($dest);
                            }
			ob_end_clean();
			header("Location: {$_SERVER['PHP_SELF']}?productID=$productID$eget");
			exit;
		}
		if(isset($_GET['page'])){
			$new_img_document_sort = (int)$_GET['page'];
			
			if(isset($_GET['rotate'])){
                            ####################### START ADD PRODUCT PDF OPTMIZIZE ######################
                           // if(ENV == 'localhost' || ENV == 'demo.competiscan.com'){    
                                $s3document_path= $document_path;
                                $s3document_path.$productID.($new_img_document_sort-1).'.jpg';                     
                                //rotateS3ImageLambda($img_document_path,$img_document_filename,$img_document_content_type,$productID);
                               rotateS3ImageWithPython($img_document_path,$img_document_filename,$img_document_content_type,$productID);
                            /*}else{
                            
                                echo $document_path.$productID.($new_img_document_sort-1).'.jpg';
                                $s3document_path= $document_path;
                                
                                if(strpos($document_path,'/')=='0'){
                                    $s3document_path  = substr($document_path,1);
                                }                       
				 $img_document_filename = $imagePath.$productID.($new_img_document_sort-1).'.jpg';			
                                $s3document_path.$productID.($new_img_document_sort-1).'.jpg';                      
                                rotates3image($s3document_path,$productID.($new_img_document_sort-1).'.jpg',$imagePath);
                               // exit;
				//rotateImage($img_document_filename);
				//exit;
				//$img_size_byte = filesize($img_document_filename);
                            }*/	
                            ####################### End ADD PRODUCT PDF OPTMIZIZE ######################
                                $img_size_byte=0;
				$sql = "UPDATE cscan_img_document SET img_document_createddate=NOW(),img_document_size_byte=$img_size_byte,img_document_createdby={$GLOBALS['AUTH_DATA']['userID']} WHERE productID=$productID AND document_id=$document_id AND img_document_sort=$new_img_document_sort";
				$DRW->query($sql,$DRW_main);
                                //ob_end_clean();
                                header("Location: {$_SERVER['PHP_SELF']}?productID=$productID$eget#p$new_img_document_sort");
                                exit;
			}
			else{
				$sql = "UPDATE cscan_img_document SET img_document_default=0 WHERE productID=$productID AND document_id=$document_id";
				$DRW->query($sql,$DRW_main);
				$sql = "UPDATE cscan_img_document SET img_document_default=1,img_document_createddate=NOW() WHERE productID=$productID AND document_id=$document_id AND img_document_sort=$new_img_document_sort";
				$DRW->query($sql,$DRW_main);
                                
                               // ob_end_clean();
                                header("Location: {$_SERVER['PHP_SELF']}?productID=$productID$eget#p$new_img_document_sort");
                                exit;
			}
			
			//ob_end_clean();
			//header("Location: {$_SERVER['PHP_SELF']}?productID=$productID$eget#p$new_img_document_sort");
			//exit;
		}
		
		$page = 0;
		$query2 = "SELECT img_document_sort,UNIX_TIMESTAMP(img_document_createddate) FROM cscan_img_document WHERE productID=$productID AND document_id=$document_id ORDER BY img_document_sort";
		$query_result2 = $DRW->query($query2,$DRW_read);
		while($data2 = $DRW->fetch_row($query_result2)){
			$page = $data2[0];
			$new = $data2[1];
			
			print '<div style="height:1px;"><a name="p'.$page.'" style="text-decoration:none;">&nbsp;</a></div><div style="margin:5px;';
			if($page==$img_document_sort) {
				print 'border:solid 4px #009933;padding:8px;float:left;';
			}
			print '"><a href="'.$_SERVER['PHP_SELF'].'?productID='.$productID.$eget.'&amp;page='.$page.'"><img src="../pdfSample_latest.php?id='.$productID.'&amp;page='.$page.'&amp;new='.$new.'" style="border:none;" /></a>';
			if($page==$img_document_sort) {
				print '<div style="text-align:center;margin-top:5px;"><a href="'.$_SERVER['PHP_SELF'].'?productID='.$productID.$eget.'&amp;page='.$page.'&amp;rotate=1">Rotate 90&deg;</a></div>';
			}
			print '</div><div style="clear:both;">&nbsp;</div>';
		}
		if($page==0){
			print 'There was an error while converting this document';
		}
		print '<div style="margin:5px;">Blank or black background? <a href="'.$_SERVER['PHP_SELF'].'?productID='.$productID.$eget.'&amp;re=1">Resample Try #1</a> | <a href="'.$_SERVER['PHP_SELF'].'?productID='.$productID.$eget.'&amp;re=2">Resample Try #2</a> | <a href="'.$_SERVER['PHP_SELF'].'?productID='.$productID.$eget.'&amp;re=3">Resample Try #3</a></div><div>&nbsp;</div>';	
		echo $button;
                
                
	}
}
####################### new functon for rotate image ######################
function rotates3image($s3document_path,$filename,$document_path){
    global $s3,$bucket_name,$serverbaseurl;
    $imgpath= $document_path.$filename;
    $results = $s3->getObject([
        'Bucket' => $bucket_name,
        'Key'    => $s3document_path.$filename,
        'SaveAs' => $imgpath,

    ]);  
    $rotate = 'convert -rotate 90 '.escapeshellarg($imgpath).' '.escapeshellarg($imgpath);
    exec($rotate); 
    
    $mimeType='image/jpg';
    $result = $s3->putObject(
            array(
                'Bucket' => $bucket_name,
                'Key' => $s3document_path.$filename,
                'SourceFile' => $imgpath,
                // 'Body' => fopen($tempFilePath, 'rb'),
               // 'StorageClass' => 'REDUCED_REDUNDANCY',
                'ContentType' => $mimeType,
                'ACL' => 'public-read',
                'Metadata' => array(
                    'string' => 'string'
                )
            )
        );
    ########### delete rotated image from local folder############
    if (file_exists($imgpath) ) {
        @unlink($imgpath);
    }
}
####################### end new functon for rotate image ######################

function rotateS3ImageLambda($s3document_path,$filename,$mimeType,$productID){
    global $s3,$bucket_name,$serverbaseurl,$s3FileUrl;
    $imgpath= $s3document_path.$filename;
    $root = dirname(__FILE__);
    $root = substr($root, 0, strpos($root, '/admin'));
    
    //echo $s3document_path.'====='.$filename.'======'.$document_path; //die;
    
    $pdfpart = $root . '/PDF/';
    $yearpath = date('Y/');
    $monthpath = date('m/');
    $datepath = $yearpath . $monthpath;
    $pdfPath = "$pdfpart$datepath$productID/";
    $pdfImgPath="$root/$s3document_path";    
    
   //echo 'ppppp '.$pdfImgPath.' ppppppppp';
   //die;
    if (!is_dir($pdfpart . $yearpath)) {
        mkdir($pdfpart . $yearpath, 02755);
    }
    if (!is_dir($pdfpart . $datepath)) {
        mkdir($pdfpart . $datepath, 02755);
    }
    if (!is_dir($pdfPath)) {
        mkdir($pdfPath, 02755);
    }
    
    if (!is_dir($pdfImgPath)) {
        mkdir($pdfImgPath, 02755);
    }
    //echo $root.'/'.$imgpath; die;
    $results = $s3->getObject([
        'Bucket' => $bucket_name,
        'Key'    => $s3document_path.$filename,
        'SaveAs' => $root.'/'.$imgpath,

    ]);  
    $rotate = 'convert -rotate 90 '.escapeshellarg($root.'/'.$imgpath).' '.escapeshellarg($root.'/'.$imgpath);
    exec($rotate); 
    
    //$mimeType='image/jpg';
    $result = $s3->putObject(
            array(
                'Bucket' => $bucket_name,
                'Key' => $s3document_path.$filename,
                'SourceFile' => $root.'/'.$imgpath,
                // 'Body' => fopen($tempFilePath, 'rb'),
               // 'StorageClass' => 'REDUCED_REDUNDANCY',
                'ContentType' => $mimeType,
                'ACL' => 'public-read',
                'Metadata' => array(
                    'string' => 'string'
                )
            )
        );
    ########### delete rotated image from local folder############
    if (file_exists($root.'/'.$imgpath) ) {
        @unlink($imgpath);
    }
}
####################### end new functon for rotate image ######################

####################### new functon for resize image ######################

function resizeS3ImageLambda($productID,$resample=1){    
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    
    $sql = "Update cscan_img_document set sample_img_status='".$resample."' where productID='".$productID."'";
    $DRW->query($sql, $DRW_main);
}

####################### end new functon for resize image ######################
####################### START ADD PRODUCT PDF OPTMIZIZE ######################

function rotateS3ImageWithPython($s3document_path,$filename,$mimeType,$productID){
    global $s3,$bucket_name,$serverbaseurl,$s3FileUrl;
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    $imgpath= $s3document_path.$filename;
    $root = dirname(__FILE__);
    $root = substr($root, 0, strpos($root, '/admin'));
    $pdfpart = $root . '/PDF/';
    $yearpath = date('Y/');
    $monthpath = date('m/');
    $datepath = $yearpath . $monthpath;
    $pdfPath = "$pdfpart$datepath$productID/";
    $pdfImgPath="$root$s3document_path";  
    
   //echo 'ppppp '.$pdfImgPath.' ppppppppp';
   //die;
    if (!is_dir($pdfpart . $yearpath)) {
        mkdir($pdfpart . $yearpath, 02755);
    }
    if (!is_dir($pdfpart . $datepath)) {
        mkdir($pdfpart . $datepath, 02755);
    }
    if (!is_dir($pdfPath)) {
        mkdir($pdfPath, 02755);
    }
    
    if (!is_dir($pdfImgPath)) {
        mkdir($pdfImgPath, 02755);
    }
    //echo $s3document_path.$filename; die;
    //echo $root.'/'.$imgpath; die;
    if(strpos($s3document_path,'/')=='0'){
        $s3document_path  = substr($s3document_path,1);
    } 
    $results = $s3->getObject([
        'Bucket' => $bucket_name,
        'Key'    => $s3document_path.$filename,
        'SaveAs' => $root.$imgpath,

    ]); 
    $rotate = 'convert -rotate 90 '.escapeshellarg($root.$imgpath).' '.escapeshellarg($root.$imgpath);
    exec($rotate); 
    $exp_filename=explode('_',$filename);
    //print_r($exp_filename);
    $new_filename=$exp_filename[0]."_".rand(100,1000).$exp_filename[1].'_'.$exp_filename[2];
    $old_filenamepath=$root.$imgpath;  
    $new_filenamepath=$root.'/'.$s3document_path.$new_filename;
    if(file_exists($old_filenamepath)) 
       {
        if(rename($old_filenamepath, $new_filenamepath)) 
            {  
            //echo "Successfully Renamed $old_filenamepath to $new_filenamepath" ; die;
             $sqlUpdate = "Update cscan_img_document set img_document_filename='".$new_filename."' where productID='".$productID."' and img_document_default='1'";
            $DRW->query($sqlUpdate, $DRW_main);
            } 
          
        }
        
     $info = $s3->doesObjectExist($bucket_name,$s3document_path.$filename);
        if($info){
                $result = $s3->deleteObject([
                    'Bucket' => $bucket_name,
                    'Key' => $s3document_path.$filename,
                ]);
        }
    $result = $s3->putObject(
            array(
                'Bucket' => $bucket_name,
                'Key' => $s3document_path.$new_filename,
                'SourceFile' => $new_filenamepath,
                // 'Body' => fopen($tempFilePath, 'rb'),
               // 'StorageClass' => 'REDUCED_REDUNDANCY',
                'ContentType' => $mimeType,
                'ACL' => 'public-read',
                'Metadata' => array(
                    'string' => 'string'
                )
            )
        );
    ########### delete rotated image from local folder############
    if (file_exists($new_filenamepath) ) {
        @unlink($new_filenamepath);
    }
}
####################### END ADD PRODUCT PDF OPTMIZIZE ######################

?>
</body>
</html>