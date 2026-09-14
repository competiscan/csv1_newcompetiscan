<?php
require_once('includes/globalSession.php');
require_once('includes/checklogin.php');

$_SESSION['selected_productID'] = array();

$last_search_sql = "SELECT ID FROM cscan_search WHERE userID='".$_SESSION['sess_userID']."' ORDER BY queryDate DESC LIMIT 1";
$rs = $DRW->query($last_search_sql,$DRW_read);
$data = $DRW->fetch_row($rs);
if(!empty($data[0])) {
	$search_id = (float) $data[0];
	ob_end_clean();
	header("Location: fullsearch.php?ssid=$search_id");
	exit;
}
else {
	ob_end_clean();
	header("Location: fullsearch.php?searchview=2");
	exit;
}
?>