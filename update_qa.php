<?php
require_once('includes/globalSession.php');
require_once('includes/checklogin.php');

if(isset($_REQUEST['pid'])) {
	$pid = (float)$_REQUEST['pid'];
}
else {
	$pid = 0;
}
if(isset($_REQUEST['chex'])) {
	$chex = (int)$_REQUEST['chex'];
}
else {
	$chex = 0;
}

@ob_clean();
if($pid!=0){
	$query_result2 = $DRW->query("UPDATE cscan_product_detail SET is_qa=$chex WHERE productID=$pid",$DRW_main);
	echo 1;
}
else {
	echo 0;
}
?>