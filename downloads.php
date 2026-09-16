<?php
require_once('includes/globalSession.php');

if(!isset($_SESSION['public_admin_access'])){
	include('includes/checklogin.php');
}

require_once 'HTTP/Download.php';
require_once 'product_doc_tracker.php';
track_user();

if(isset($_REQUEST['id'])) {
	$id = $_REQUEST['id'];
}
else {
	$id = '';
}
if(!empty($id)){
	$query2 = "SELECT dl_id,dl_name,dl_desc,dl_url,dl_sortdate FROM cscan_download WHERE dl_md5_id='".$DRW->real_escape_string($id)."'";
	$query_result2 = $DRW->query($query2,$DRW_read);
	$data2 = $DRW->fetch_row($query_result2);
	$dl_id = (float)$data2[0];
	$dl_name = $data2[1];
	$dl_desc = $data2[2];
	$dl_url = $data2[3];
	$dl_sortdate = $data2[4];
	$DRW->free_result($query_result2);
	
	if($dl_id!=0 && !empty($dl_url)){
		$base_path = '/home/competiscan/downloads/';
		$base_url = 'downloads.competiscan.com/';
		$pos = strpos($dl_url,$base_url);
		if($pos!==false){
			$document_filename = substr($dl_url,$pos+strlen($base_url));
		}
		else{
			$document_filename = '';
		}
		$src = $base_path.$document_filename;
		
		if($document_filename!='' && is_file($src)){
			@ob_end_clean();
			$dl = new HTTP_Download();
			$dl->setFile($src);
			echo $dl->guessContentType();
			//$dl->setLastModified($document_createddate);
			//$dl->setContentType($document_content_type);
			$dl->setCacheControl('public');
			$dl->setCache(true);
			$dl->setContentDisposition(HTTP_DOWNLOAD_ATTACHMENT, $document_filename);
			$dl->send();
			exit;
		}
		else{
			@ob_end_clean();
			header("Location: $dl_url");
			exit;
		}
	}
}
@ob_end_clean();
header("Content-Type: text/plain");
echo "Download has been discontinued.";
?>