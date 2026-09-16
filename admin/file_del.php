<?php
require_once "../auth_auth.php";
require_once('../includes/functions.php');

if(isset($_REQUEST['hy'])){
	$hy = (int)$_REQUEST['hy'];
}
if(empty($hy)){
	$hy = '';
}
@ob_clean();
if(isset($_REQUEST['ceafid'])){
	$q = "SELECT `ceafpath` FROM `cscan_email_attach_file$hy` WHERE `ceafid`=".(int)$_REQUEST['ceafid'];
	$query_result = $DRW->query($q,$DRW_read);
	$data = $DRW->fetch_row($query_result);
	#################################### Start S3 Implementation Code ###########################################
	if(!empty($data[0])){
		$result = $s3->deleteObject([
			'Bucket' => $bucket_name,
			'Key' => $data[0],
	    ]);
		/*if(is_file('../'.$data[0])){
			@unlink('../'.$data[0]);
		}*/
		$query_result2 = $DRW->query("DELETE FROM `cscan_email_attach_file$hy` WHERE `ceafid`=".(int)$_REQUEST['ceafid'],$DRW_main);
		echo 1;
	}
	#################################### End S3 Implementation Code ###########################################
}
else {
	echo 0;
}
?>