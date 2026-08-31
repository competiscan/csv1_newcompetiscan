<?php
require_once('includes/globalSession.php');
require_once('includes/checklogin.php');
require_once 'HTTP/Download.php';
require_once 'product_doc_tracker.php';
track_user();
if(isset($_GET['trend_id'])){
	$trend_id = (int)$_GET['trend_id'];
	track_trend($trend_id);

	$sqltwo = "SELECT trend_name,trend_link FROM cscan_trend_report WHERE trend_id=$trend_id";
	$query = $DRW->query($sqltwo,$DRW_read);
	$row2 = $DRW->fetch_assoc($query);
	$trendname = $row2['trend_name'];
	$dl_url = $row2['trend_link'];
	
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
	
	/*if($document_filename!='' && is_file($src)){
		@ob_end_clean();
		$dl = new HTTP_Download();
		$dl->setFile($src);
		$dl->guessContentType();
		//$dl->setLastModified($document_createddate);
		//$dl->setContentType($document_content_type);
		$dl->setCacheControl('public');
		$dl->setCache(true);
		$dl->setContentDisposition(HTTP_DOWNLOAD_ATTACHMENT, $document_filename);
		$dl->send();
		exit;
	}
	else{*/
		@ob_end_clean();
		header("Location: $dl_url");
		exit;
	////}
}
?>
