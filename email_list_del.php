<?php
require_once('includes/globalSession.php');
require_once('includes/checklogin.php');

@ob_clean();
if(isset($_REQUEST['emailto_id'])){
	$query_result2 = $DRW->query("DELETE FROM cscan_emailto_list WHERE userID='{$_SESSION['sess_userID']}' AND emailto_id=".(int)$_REQUEST['emailto_id'],$DRW_main);
	echo 1;
}
else {
	echo 0;
}
?>