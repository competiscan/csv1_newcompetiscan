<?php
ini_set( "memory_limit", "70M" );//33554432
$LOGOUT_PAGE = 'content/index.php';
$ALLOW_GROUPS = array(20,38);
require_once("auth_auth.php");
require_once('includes/functions.php');

if(isset($_REQUEST['muid'])) $muid = (int)$_REQUEST['muid'];
else $muid = 0;
if(!empty($_REQUEST['isTmp'])) $isTmp = '1';
else $isTmp = '0';

if(isset($_REQUEST['hy'])){
	$hy = (int)$_REQUEST['hy'];
}
if(empty($hy)){
	$hy = '';
}

$onload = '';
$save_path = 'tmp_upload/';
$yearpath = date('Y/');
$monthpath = date('m/');
$datepath = $yearpath.$monthpath;

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
			var uploads_curr = window.parent.submitArray[0];
			if(window.parent.submitArray.length>1){
				window.parent.submitArray = window.parent.submitArray.slice(1);
			}
			else{
				window.parent.submitArray = new Array();
			}
			var wi = window.parent.document.getElementById('wait_image'+uploads_curr);
			hideBlock(wi);
			
			if(window.parent.submitArray.length==0){
				if(window.parent.document.mailForm){
					window.parent.document.mailForm.sender.disabled = false;
					window.parent.document.mailForm.saver.disabled = false;
					window.parent.document.mailForm.cancel.disabled = false;
				}
			}
			else{
				var uploads_next = window.parent.submitArray[0];
				var uf = window.parent.document.getElementById('uploadform'+uploads_next);
				uf.submit();
			}
			showFiles('files_list.php?muid=<?php echo $muid; ?>&isTmp=<?php echo $isTmp; ?>&hy=<?php echo $hy; ?>',window.parent.document.getElementById('attachment_inputs'));
		}
	}
//-->
</script>
<script src="includes/ajax.js" type="text/JavaScript"></script>
</head>
<body <?php echo $onload; ?>>&nbsp;</body>
</html>
<?php 
function moveFiles($fileArray, $key) {
	#################################### Start S3 Implementation Code ###########################################
	global $DRW,$DRW_read,$DRW_main,$DRW_crm, $s3, $bucket_name;
	#################################### End S3 Implementation Code ###########################################
	$muid = $GLOBALS['muid'];
	$isTmp = $GLOBALS['isTmp'];
	$save_path = $GLOBALS['save_path'];
	$yearpath = $GLOBALS['yearpath'];
	$datepath = $GLOBALS['datepath'];
	$hy = $GLOBALS['hy'];
	
	$multipleFiles = array();
	if(is_array($_FILES[$key]['name'])){
		foreach($_FILES[$key]['name'] as $k=>$n){
			$multipleFiles[] = array('name'=>$_FILES[$key]['name'][$k], 'type'=>$_FILES[$key]['type'][$k], 'tmp_name'=>$_FILES[$key]['tmp_name'][$k], 'error'=>$_FILES[$key]['error'][$k], 'size'=>$_FILES[$key]['size'][$k]);
		}
	}
	else{
		$multipleFiles[] = array('name'=>$_FILES[$key]['name'], 'type'=>$_FILES[$key]['type'], 'tmp_name'=>$_FILES[$key]['tmp_name'], 'error'=>$_FILES[$key]['error'], 'size'=>$_FILES[$key]['size']);
	}
	
	foreach($multipleFiles as $multipleFile){
		$error = $multipleFile['error'];
		$size = $multipleFile['size'];
		if($error == UPLOAD_ERR_OK && !empty($muid)) {
			#################################### Start S3 Implementation Code ###########################################
			$type = $multipleFile['type'];
			#################################### End S3 Implementation Code ###########################################
			$tmpname = $multipleFile['tmp_name'];
			$originalfilename = $filename = $multipleFile['name'];
			$filename = preg_replace('/[^a-zA-Z0-9_\\.\\-]/','_', trim($filename));
			if(strpos($filename,'.')===false) $filename .= '.txt';
			$filename = preg_replace('/(\\.[^\\.]+)$/','_'.$muid.'$1', $filename);
			
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
				//$qc = "SELECT COUNT(*) FROM `cscan_email_attach_file$hy` WHERE `ceafpath`='".$DRW->real_escape_string($createdFileName)."' AND `muid`='".$DRW->real_escape_string($muid)."' AND isTmp=$isTmp";
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
			
			#################################### Start S3 Implementation Code ###########################################
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
				$q = "REPLACE INTO `cscan_email_attach_file$hy` (`ceaftype`,`ceafpath`,`muid`,isTmp) VALUES ('".$DRW->real_escape_string($multipleFile['type'])."','".$DRW->real_escape_string($createdFileName)."','".$DRW->real_escape_string($muid)."',$isTmp)";
				$query_result = $DRW->query($q,$DRW_main);
			}
			#################################### End S3 Implementation Code ###########################################
		}
	}
        //sleep(5);
}
?>