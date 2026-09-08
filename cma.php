<?php
require_once('includes/globalSession.php');

if(!isset($_SESSION['public_admin_access'])){
	require_once('includes/checklogin.php');
}

$query2 = "SELECT maContent FROM cscan_message_alert WHERE maID='".$DRW->real_escape_string($_GET['id'])."'";
$query_result2 = $DRW->query($query2,$DRW_read);
$data2 = $DRW->fetch_row($query_result2);

echo $data2[0];
?>