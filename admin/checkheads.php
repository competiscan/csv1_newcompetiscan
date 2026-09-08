<?php 
require_once("../auth_auth.php");
require_once '../includes/functions.php';

@ob_clean();
if(isset($_REQUEST['findval']) && trim($_REQUEST['findval'])!=''){
	$productHeadline = ltrim($_REQUEST['findval']);
	$query = "SELECT productHeadline FROM cscan_product_detail WHERE productHeadline LIKE '".mysqlLike($productHeadline)."%' ORDER BY productHeadline LIMIT 1";
	$result = $DRW->query($query,$DRW_read);
	$data = $DRW->fetch_row($result);
	if($data[0]!='') {
		echo substr($data[0],0,50);
		if(strlen($data[0])>50) {
			echo '...';
		}
	}
}
?>