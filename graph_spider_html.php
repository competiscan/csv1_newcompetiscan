<?php
require_once('includes/globalSession.php');
if(!isset($_SESSION['public_admin_access'])){
	require_once('includes/checklogin.php');
}

if(isset($_REQUEST['productID'])) {
	$productID = (float) $_REQUEST['productID'];
}
else {
	$productID = 0;
}
if(isset($_REQUEST['ci_id'])) {
	$ci_id = (float) $_REQUEST['ci_id'];
}
else {
	$ci_id = 0;
}

if(isset($_REQUEST['avg'])) {
	$avg = '&amp;avg='.(int)$_REQUEST['avg'];
}
else {
	$avg = '';
}

$caption = '';
if(!empty($ci_id)){
	$productQuery = "SELECT ci_title FROM cscan_insight WHERE ci_id=$ci_id";
}
else{
	$productQuery = "SELECT ci_title FROM cscan_insight WHERE productID=$productID";
}
$productQuery = $DRW->query($productQuery,$DRW_read);
$productRs = $DRW->fetch_array($productQuery);
$caption = $productRs['ci_title'];
if(empty($caption)){
	$productQuery = "SELECT companyName,entryID FROM cscan_product_detail pd,cscan_company_product pp,cscan_company pa WHERE pd.productID=$productID AND pd.productID=pp.productID AND pa.companyID=pp.companyID AND primary_co=1";
	$productQuery = $DRW->query($productQuery,$DRW_read);
	$productRs = $DRW->fetch_array($productQuery);
	$caption = $productRs['companyName'].', Entry ID: '.$productRs['entryID'];
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Competiscan</title>
<link href="includes/competiscan_stylesheet.css?v=20100318" rel="stylesheet" type="text/css" />
</head>
<body>
<div class="headings" style="color:#000000;font-size: 14px;"><?php echo $caption; ?></div>
<img src="graph_spider.php?productID=<?php echo $productID.$avg.'&amp;ci_id='.$ci_id; ?>" alt="" />
</body>
</html>