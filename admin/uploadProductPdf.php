<?php 
error_reporting(E_ALL);
ini_set('max_execution_time', 0);
ini_set("memory_limit", "-1");
set_time_limit(0);
$ALLOW_GROUPS = array(4);
require_once "../auth_auth.php";
require_once '../includes/functions.php';
include 'top.php';
$updID ='';
$msg='';
$upload_document='';
include_once __DIR__ . '/../includes/thumb.php';
require_once __DIR__ . '/../includes/Document.php';
if(isset($_POST['submity'],$_POST['submit']) && ($_POST['submit']=='Save' || $_POST['submit']=='Save & Add More')){   
    if(isset($_FILES)){
    $fileArray = $_FILES;
    }else{
     $fileArray = array();
    }
    if(isset($_REQUEST['product_entry_id'])){
       $entryID= trim($_REQUEST['product_entry_id']);
    }else{
       $entryID=""; 
    }
    $sqlCheckProduct = "SELECT productID FROM cscan_product_detail WHERE entryID = '".$entryID."'";
	$resultCheckProduct = $DRW->query($sqlCheckProduct,$DRW_read);
	$resultCounter = $DRW->num_rows($resultCheckProduct);
        if($resultCounter > 0) {
            $resultcheckData = $DRW->fetch_array($resultCheckProduct);   
            $productID = $resultcheckData['productID'];
            $upload_document = uploadProductPDF($productID, $fileArray);
            if($upload_document==0){
                $msg="File uploaded successfully!";

            }elseif($upload_document==3){
                $msg="Please upload valid file!";
            }elseif($upload_document==2){
                $msg="File not uploaded successfully!";
            }elseif($upload_document==1){
                $msg="File not uploaded successfully!";
            }
         
        }else{
            $msg="Please enter valid entry ID.";
        }
    
}

function uploadProductPDF($productID, $fileArray, $pdfContent='', $productStatus = 0, $filekey = 'PDFFILE') {
    $AUTH_DATA = $GLOBALS['AUTH_DATA'];
    global $s3,$bucket_name,$DRW, $DRW_read, $DRW_main, $DRW_crm;
    if (isset($fileArray[$filekey])) {
        //$pdfNameArr = preg_replace('/[^a-zA-Z0-9_\\.\\-]/', '_', $fileArray[$filekey]['name']);
        $pdfNameArr = preg_replace('!\s+!', ' ', trim($fileArray[$filekey]['name']));
        $pdfNameArr = preg_replace('/[^a-zA-Z0-9_\\.\\-]/', '_', $pdfNameArr);
        $pdfTypeArr = $fileArray[$filekey]['type'];
        $pdfTempNameArr = $fileArray[$filekey]['tmp_name'];
        $pdfSizeArr = $fileArray[$filekey]['size'];
    } else {
        $pdfNameArr = '';
        $pdfTypeArr = '';
        $pdfTempNameArr = '';
        $pdfSizeArr = 0;
    }
    $message = 0;
    $yearpath = date('Y/');
    $monthpath = date('m/');
    $datepath = $yearpath . $monthpath;

    $root = dirname(__FILE__);
    $root = substr($root, 0, strpos($root, '/admin'));
    $pdfpart = $root . '/PDF/';
    $pdfPath = "$pdfpart$datepath$productID/";
    $pdfs3newPath = "PDF/$datepath$productID/";
    if ($pdfNameArr != '') {
        if ($filekey == 'PDFFILE') {
            $valid_types = array("pdf");
        } else {
            $valid_types = array("swf", "gif", "jpg", "png", "jpeg");
        }
        $name_arr = explode(".", $pdfNameArr);
        $ext_name = strtolower(end($name_arr));
        $ext_type = $ext_name;
        if (in_array($ext_type, $valid_types) || in_array($ext_name, $valid_types)) {
            if (is_uploaded_file($pdfTempNameArr)) {
                if (!is_dir($pdfpart . $yearpath)) {
                    mkdir($pdfpart . $yearpath, 02755);
                }
                if (!is_dir($pdfpart . $datepath)) {
                    mkdir($pdfpart . $datepath, 02755);
                }
                if (!is_dir($pdfPath)) {
                    mkdir($pdfPath, 02755);
                }
                $pdfName = substr($pdfNameArr, 0, -4);
                //$pdfName = $pdfName . "_" . $productID . "." . $ext_name;
                $pdfName_old = $pdfName . "_" . $productID . "." . $ext_name;
                
                
                 if ($filekey == 'PDFFILE') {
                      $content_type = "application/pdf";
                        
                    }
                
                 //$content_type = "application/pdf";
                $pdf_info = $s3->doesObjectExist($bucket_name,$pdfs3newPath.$pdfName_old);
                if($pdf_info){
                    $result_del_pdf = $s3->deleteObject([
                        'Bucket' => $bucket_name,
                        'Key' => $pdfs3newPath . $pdfName_old,
                    ]);
                }
                
                $query3 = "SELECT document_filename,document_path,document_id FROM cscan_document WHERE productID=$productID AND document_id=1 limit 0,1";
                $query_result3 = $DRW->query($query3,$DRW_read);
                if($DRW->num_rows($query_result3)>0) {
                    $data3 = $DRW->fetch_row($query_result3);                       
                    $document_filename_old = $data3[0];
                    $document_path_old= $data3[1];
                    $document_path_old=str_replace('/PDF','PDF',$document_path_old);
                    $pdf_info_old = $s3->doesObjectExist($bucket_name,$document_path_old.$document_filename_old);
                    if($pdf_info_old){
                        $result_del_pdf2 = $s3->deleteObject([
                            'Bucket' => $bucket_name,
                            'Key' => $document_path_old . $document_filename_old,
                        ]);
                    }
                }
                
                $pdfName = $pdfName . "_" .rand(100,1000)."_".$productID . "." . $ext_name;
                $result = $s3->putObject([
                    'Bucket' => $bucket_name,
                    'Key'    => $pdfs3newPath . $pdfName,
                    'SourceFile' => $pdfTempNameArr,
                    'ACL'    => 'public-read',
                    'ContentType'	=> $content_type,
                    'Metadata'      => array(
                       'string'        => 'string'
                     )
                ]);
                
                if (move_uploaded_file($pdfTempNameArr, $pdfPath . $pdfName)) {
                    if ($productStatus == 1) {
                        $back = false;
                    } else {
                        $back = false;
                    }
                    if ($filekey == 'PDFFILE') {                        
                        
                            //saveImagefromPdf($pdfs3newPath, $pdfName, $productID); 
                        $cr= createPreviewJPGWithPython($pdfPath, $pdfName, $productID, $back);
                        $content_type = "application/pdf";
                        
                    } else {
                        if ($ext_name == "swf") {
                            $content_type = "application/x-shockwave-flash";
                        } elseif ($ext_name == "gif") {
                            $content_type = "image/gif";
                        } elseif (($ext_name == "jpg") || ($ext_name == "jpeg")) {
                            $content_type = "image/jpeg";
                        } else {
                            $content_type = "image/png";
                        }
                    }
                   // if(siteMode()=='localhost' || siteMode()=='demo'){
                    //      $document_id = savePDFData($productID, $pdfPath, $pdfName, $pdfContent, '/s3test/' . $datepath . $productID . '/', false, $content_type);
                   // }else{
                         $document_id = savePDFData($productID, $pdfPath, $pdfName, $pdfContent, '/PDF/' . $datepath . $productID . '/', false, $content_type);
                    //}
                          $GLOBALS['new_preview'] = true;
                } else
                    $message = 1;
            } else
                $message = 2;
        } else
            $message = 3;
    }
    elseif ($pdfContent != "") {
        savePDFText($productID, 1, $pdfContent);
    }
    if(!empty($pdfName)){
        if (file_exists($pdfPath.$pdfName) ) {
            //@unlink($pdfPath.$pdfName);
        }
    }
    return $message;
}
function createPreviewJPGWithPython($path,$name,$productID,$back=false,$dops=false,$document_id=1){
	$dops = intval($dops);
         $output = passthru("python3 ../convert_pdftoimage.py $path $name $productID $document_id 2>&1");
         //echo $output; 
         if($document_id!=0){
            savePDFImageWithPython($productID,$document_id,$path);
           }
	/*$s = '';
	$pos = strpos(__FILE__,'/includes/');
	if($pos!==false){ 
		$s .= 'cd '.substr(__FILE__,0,$pos).'; ';
	}
	$s .= '/usr/bin/php convert_back.php '.escapeshellarg($path).' '.escapeshellarg($name).' '.escapeshellarg($productID).' '.$dops.' '.$document_id;
	
	if($back){
		$s .= ' > /dev/null 2>&1 &';
	}
	$ex = exec($s);*/
}
function savePDFImageWithPython($productID, $document_id, $pdfPath, $maxbytes = 500000) {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    global $s3,$bucket_name,$serverbaseurl;
    $root = dirname(__FILE__);
    if (strpos($root, '/includes') !== false) {
        $root = substr($root, 0, strpos($root, '/includes'));
    }
    //echo $root."<br/>";
    $checkV = "SELECT img_document_path,img_document_filename FROM cscan_img_document WHERE productID=$productID AND document_id=$document_id";
    $checkV = $DRW->query($checkV, $DRW_read);
    while ($dataV = $DRW->fetch_row($checkV)) {
        $path = $root . $dataV[0] . $dataV[1];
        $info = $s3->doesObjectExist($bucket_name,$dataV[0] . $dataV[1]);
        if($info){
                $result = $s3->deleteObject([
                    'Bucket' => $bucket_name,
                    'Key' => $dataV[0] . $dataV[1],
                ]);
        }
        
        
        if (is_file($path) && $path != $pdfPath . $dataV[1]) {
           // @unlink($path);
        }
    }
    $sql = "DELETE FROM cscan_img_document WHERE productID=$productID AND document_id=$document_id";
    $DRW->query($sql, $DRW_main);
    $img_document_default = 1;
    $img_document_sort = 0;
    $image = $pdfPath . $productID .'-'. $img_document_sort . '.jpg';
    //echo $pdfPath."<br/>";
  // echo $img_document_path = substr($pdfPath, strlen($root)); die;
      $img_document_path = strstr($pdfPath,'/PDF');
    while (is_file($image)) {
        $img_size_byte = filesize($image);
        $img_document_sort_ins = $img_document_sort + 1;
        $sql = "REPLACE INTO cscan_img_document (productID,document_id,img_document_sort,img_document_filename,img_document_createddate,img_document_content_type,img_document_size_byte,img_document_createdby,img_document_default,img_document_path)
			VALUES ($productID,$document_id,$img_document_sort_ins,'" . $DRW->real_escape_string($productID .'-'. $img_document_sort . '.jpg') . "',NOW(),'image/jpeg',$img_size_byte,{$GLOBALS['AUTH_DATA']['userID']},$img_document_default,'" . $DRW->real_escape_string($img_document_path) . "')";
        $DRW->query($sql, $DRW_main);
         sendthumbimageons3($img_document_path , $DRW->real_escape_string($productID .'-'. $img_document_sort . '.jpg') );
        
        $img_document_sort++;
        $image = $pdfPath . $productID .'-'. $img_document_sort . '.jpg';
        $img_document_default = 0;
        
    }
}
?>
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<table width="100%" border="0" cellspacing="0" cellpadding="5" class="text">
    <tr>
	<td class="adminhead" align="center">Upload Product PDF</td>
    </tr> 
    <tr>
	<td align ="right" class="bodytext"><span class="error" style="font-weight:bold;">* required field</span></td>
    </tr>
    <tr>       
        <td align="center">
                <form method="post" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" onsubmit="return validate();" enctype="multipart/form-data" >
                    <table border="0" cellspacing="0" cellpadding="0"> 
                        <tr>
                            <td style="border:solid 1px #14734F;">
                                <table border="0" cellspacing="0" cellpadding="4">
                                <tr>
                                    <td align="center" colspan="2">
                                       <?php if($msg=='File uploaded successfully!'){ ?>
                                       <span class="msg_class" style="color:#14734f;"> 
                                     <?php if(isset($msg))  echo $msg; ?>
                                       </span> 
                                       <?php } else{ ?> 
                                        <span class="error msg_class">  
                                        <?php if(isset($msg))  echo $msg; ?>
                                        </span>
                                        <?php }?>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">&nbsp;</td>
                                 </tr>
                                <tr>
                                    <td class="bodytext" align="right" valign="top"><span class='error'>*</span> Entry ID:</td>
                                    <td>
                                        <input type="text" name="product_entry_id" size="50" onkeypress="return isNumberKey(event);" class="input_box" maxlength="20" value="<?php //echo htmlspecialchars($trendname,ENT_QUOTES);?>" />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="bodytext" align="right" valign="top"><span class="error">*</span> Product PDF:</td>
                                    <td>
                                        <input type="file" name="PDFFILE"  size="60" class="input_file" onchange="check_file_ext(this);"  /><br/>
                                        <span class="error">Hint: Only allowed extension(.pdf).</span>
                                        <input type="hidden" name="product_pdf_hidden" value="<?php //if($trend_file_name!="" && $updID!="") {echo $trend_file_name;} ?>"/>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="bodytext" colspan="2" align="left" ><?php //echo $pdftext; ?></td>
                                </tr>
                                <tr>
                                    <td colspan="2">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td>
                                    <?php if($updID == ''){?>
                                    <input class="button" type="submit" name="submit" value="Save" />
                                    <!--<input class="button" type="submit" name="submit" value="Save &amp; Add More" />-->
                                    <?php } else{ ?>
                                    <input class="button" type="submit" name="submit" value="Update" />
                                    <input type="hidden" name="id" value="<?php echo $updID; ?>" />
                                    <?php }?>
                                    <input class="button" type="button" value="Cancel" onclick="location.href='uploadProductPdf.php'; return false;" />
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
                <input type="hidden" name="submity" value="1" />
            </form>
        </td>
    </tr>
</table>
<script type="text/JavaScript"> 
var _validFileExtensions = [".pdf"];    
function check_file_ext(oInput) {
    if (oInput.type == "file") {
        var sFileName = oInput.value;
         if (sFileName.length > 0) {
            var blnValid = false;
            for (var j = 0; j < _validFileExtensions.length; j++) {
                var sCurExtension = _validFileExtensions[j];
                if (sFileName.substr(sFileName.length - sCurExtension.length, sCurExtension.length).toLowerCase() == sCurExtension.toLowerCase()) {
                    blnValid = true;
                    break;
                }
            }
             
            if (!blnValid) {
                alert("Sorry, " + sFileName + " is invalid, allowed extensions are: " + _validFileExtensions.join(", "));
                oInput.value = "";
                return false;
            }
        }
    }
    return true;
}
function validate()
{
    var entry_id=document.frm1.product_entry_id.value=trimspace(document.frm1.product_entry_id.value);
    var product_pdf=document.forms["frm1"]["PDFFILE"].value;
    var product_pdf_hidden=document.forms["frm1"]["product_pdf_hidden"].value;
  if(entry_id == '')
    {
            alert('Please enter entry ID.');
            document.frm1.product_entry_id.focus();
            return false;
    }
   
   if(product_pdf== '' && product_pdf_hidden=='')
    {
            alert('Please upload product PDF.');
            document.frm1.PDFFILE.focus();
            return false;
    }
}
function isNumberKey(evt)
{
  var charCode = (evt.which) ? evt.which : event.keyCode;
 console.log(charCode);
    if (charCode != 46 && charCode != 45 && charCode > 31
    && (charCode < 48 || charCode > 57))
     return false;

  return true;
}
</script> 
<script type="text/JavaScript">
//When the page has loaded.
$( document ).ready(function(){
    $('.msg_class').fadeIn('slow', function(){
       $('.msg_class').delay(30000).fadeOut(); 
    });
});
       
</script>
<?php include 'bottom.php'; ?>