<?php
require_once('includes/globalSession.php');

if(!isset($_SESSION['public_admin_access'])){
	//include('includes/checklogin.php');
	ob_end_clean();
	header("Location: login.php");
	exit;
}

require_once 'HTTP/Download.php';

if(isset($_REQUEST['fid'])) {
	$fid = (int)$_REQUEST['fid'];
}
else {
	$fid = 0;
}

if($fid!=0){
	$query2 = "SELECT fid,fid_filename,fid_content_type,fid_content_length,UNIX_TIMESTAMP(fid_createddate),fid_path FROM cscan_report WHERE fid=$fid";
	$query_result2 = $DRW->query($query2,$DRW_read);
	$data2 = $DRW->fetch_row($query_result2);
	$fid = (float)$data2[0];
	$document_filename = $data2[1];
	$document_content_type = $data2[2];
	$document_size_byte = $data2[3];
	$document_createddate = $data2[4];
	$document_path = $data2[5];
	$DRW->free_result($query_result2);
	
	#################################### Start S3 Implementation Code ###########################################
	if($fid!=0){
		//$src = dirname(__FILE__).$document_path;
		if($document_filename!=''){
			@ob_end_clean();
			$mystring = $document_path;
			$path = preg_match("/^\//", $mystring);
			if ($path == 1)
			{
			    $full_path = substr($document_path,1);
			}
			else 
			{
			    $full_path = $document_path;
			}
			$keyname=$full_path;
			try {
				// Get the object.
				$result = $s3->getObject([
				'Bucket' => $bucket_name,
				'Key' => $keyname
				]);

				header('Content-Description: File Transfer');
				//this assumes content type is set when uploading the file.
				//header('Content-Type: ' . $result->ContentType);
				header("Content-Type: {$result['ContentType']}");
				header('Content-Disposition: attachment; filename=' . $keyname);
				header('Expires: 0');
				header('Cache-Control: must-revalidate');
				header('Pragma: public');

				// Display the object in the browser.
				// header("Content-Type: {$result['ContentType']}");
			 	echo $result['Body'];
			} catch (S3Exception $e) {
				echo $e->getMessage() . PHP_EOL;
			}
			exit;



			
			/*$dl = new HTTP_Download();
			$dl->setFile($src);
			$dl->setLastModified($document_createddate);
			$dl->setContentType($document_content_type);
			$dl->setCacheControl('public');
			$dl->setCache(true);
			$dl->setContentDisposition(HTTP_DOWNLOAD_ATTACHMENT, $document_filename);
			$dl->send();*/
			
			/*
			makeCacheable(filemtime($src));
			
			header("Content-Disposition: inline; filename=\"CompetiscanProduct_$productID.pdf\"");
			header("Content-Type: $document_content_type");
			header("Content-Length: $document_size_byte");
			header("Accept-Ranges: bytes");
			readfile($src);
			*/
			//exit;
		}
	}
	#################################### End S3 Implementation Code ###########################################
}
?>