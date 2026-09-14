<?php
require_once "../auth_auth.php";
require_once('../includes/functions.php');

$onload = '';
$save_path = '../tmp_upload/';
$yearpath = date('Y/');
$monthpath = date('m/');
$datepath = $yearpath.$monthpath;
if(isset($_REQUEST['hy'])){
	$hy = (int)$_REQUEST['hy'];
}
if(empty($hy)){
	$hy = '';
}
if(count($_FILES)>0){
	if (file_exists($save_path)) {
		array_walk($_FILES, 'moveFiles');
	}
	
	$onload = ' onload="doOnload();"';
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Upload</title>
<script type="text/JavaScript">
<!--
	function doOnload(){
		if(window.parent){
			//hideBlock(window.parent.document.getElementById('upload_div'));
			//window.parent.document.uploadform.reset();
			var samehref = window.parent.document.location.href;
			window.parent.document.location.href = samehref;
		}
	}
//-->
</script>
<script src="../includes/ajax.js" type="text/JavaScript"></script>
</head>
<body <?php print $onload; ?>>&nbsp;</body>
</html>
<?php 
function moveFiles($fileArray, $key) {
	#################################### Start S3 Implementation Code ##############################
	global $DRW,$DRW_read,$DRW_main,$DRW_crm, $s3, $bucket_name;
	#################################### End S3 Implementation Code ################################
	$save_path = $GLOBALS['save_path'];
	$yearpath = $GLOBALS['yearpath'];
	$datepath = $GLOBALS['datepath'];
	$hy = $GLOBALS['hy'];
	
	$error = $_FILES[$key]['error'];
	$size = $_FILES[$key]['size'];
	if(isset($_REQUEST['isTmp'])) $isTmp = '1';
	else $isTmp = '0';
	if($error == UPLOAD_ERR_OK) {
		################################ Start S3 Implementation Code ################################
		$type = $_FILES[$key]['type'];
		################################ End S3 Implementation Code ##################################
		$tmpname = $_FILES[$key]['tmp_name'];
		$originalfilename = $filename = $_FILES[$key]['name'];
		$filename = preg_replace('/[^a-zA-Z0-9_\\.\\-]/','_', trim($filename));
		if(strpos($filename,'.')===false) $filename .= '.txt';
		$filename = preg_replace('/(\\.[^\\.]+)$/','_'.preg_replace('/[^a-zA-Z0-9_\\.\\-]/','_', $_REQUEST['muid']).'$1', $filename);
		
		if(!is_dir($save_path.$yearpath)){
			mkdir($save_path.$yearpath,02755);
		}
		if(!is_dir($save_path.$datepath)){
			mkdir($save_path.$datepath,02755);
		}
		
		$createdFileName = $save_path.$datepath.$filename;
		
		$i = 1;
		do{
			if($i>1){
				if($i==2) $createdFileName = preg_replace('/(\\.[^\\.]+)$/','_'.$i.'$1', $createdFileName);
				else $createdFileName = preg_replace('/(_\\d+)(\\.[^\\.]+)$/','_'.$i.'$2', $createdFileName);
			}
			//$qc = "SELECT COUNT(*) FROM `cscan_email_attach_file$hy` WHERE `ceafpath`='".$DRW->real_escape_string(substr($createdFileName,3))."' AND `muid`='".$DRW->real_escape_string($_REQUEST['muid'])."' AND isTmp=$isTmp";
			//$query_resultc = $DRW->query($qc,$DRW_read);
			//$count = $DRW->fetch_row($query_resultc);
			//$count = $count[0];
			if(is_file($createdFileName)){
				$count = 1;
			}
			else{
				$count = 0;
			}
			$i++;
		} while($count>0 && $i<100000);
		
		############################ Start S3 Implementation Code ###############################
		$result = $s3->putObject([
            'Bucket' => $bucket_name,
            'Key'    => $createdFileName,
            'SourceFile' => $tmpname,
            'ACL'    => 'public-read',
            'ContentType'   => $type,
            'Metadata'      => array(
               'string'        => 'string'
             )
        ]);
		
		if(isset($result['@metadata']['statusCode']) && $result['@metadata']['statusCode'] == 200){
			//move_uploaded_file($tmpname, $createdFileName);
			$q = "REPLACE INTO `cscan_email_attach_file$hy` (`ceaftype`,`ceafpath`,`muid`,isTmp) VALUES ('".$DRW->real_escape_string($_FILES[$key]['type'])."','".$DRW->real_escape_string(substr($createdFileName,3))."','".$DRW->real_escape_string($_REQUEST['muid'])."',$isTmp)";
			$query_result = $DRW->query($q,$DRW_main);
		}
		############################## End S3 Implementation Code #################################
	}
}
?>
