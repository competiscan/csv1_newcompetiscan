<?php
$LOGOUT_PAGE = 'content/index.php';
$ALLOW_GROUPS = array(20,38);
require_once("auth_auth.php");

if(isset($_REQUEST['hy'])){
	$hy = (int)$_REQUEST['hy'];
}
if(empty($hy)){
	$hy = '';
}

$query2 = "SELECT DATE_FORMAT(`efdate`,'%m/%d/%Y'),`forward_to`,`forward_from`,`forward_subject`,`forward_message`,`forward_attachments`,`forward_product`	
	FROM `cscan_email_forward$hy` WHERE `efid`='".$DRW->real_escape_string($_GET['efid'])."' ORDER BY `efdate` DESC";
$query_result2 = $DRW->query($query2,$DRW_read);
$data2 = $DRW->fetch_row($query_result2);
$efdate = $data2[0];
$forward_to_ef = $data2[1];
$forward_from_ef = $data2[2];
$forward_subject_ef = $data2[3];
$forward_message_ef = $data2[4];
$forward_attachments_ef = $data2[5];
$forward_product_ef = $data2[6];
if(isset($_GET['product'])) {
	$data = $forward_product_ef;
}
else {
	$data = $forward_message_ef;
}

@ob_end_clean();
header('Content-Type: text/html; charset=iso-8859-1');
echo '<html><body>';
echo $data;
echo '</body></html>';
?>