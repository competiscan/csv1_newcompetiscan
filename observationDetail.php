<?php
require_once('includes/globalSession.php');
require_once('includes/checklogin.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Product Observation</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="includes/competiscan_stylesheet.css" rel="stylesheet" type="text/css" />
</head>
<body>
<table border="0" cellspacing="0" cellpadding="4" class="bodytext">
<?php

$productID = (float)$_REQUEST['pid'];
$sites_id = (float)$_REQUEST['sid'];
$sp_observation = $_REQUEST['date'];

$resultC = $DRW->query("SELECT entryID,sites_name,DATE_FORMAT(sp_observation,'%m/%d/%Y %h:%i %p'),sp_url,sites_category_name,sp_image,sp_image_path 
	FROM cscan_product_detail pd,cscan_sites_product sp,cscan_sites ss LEFT JOIN cscan_sites_category sc USING(sites_category_id)
	WHERE pd.productID=$productID AND pd.productID=sp.productID AND sp.sites_id=$sites_id AND sp_observation='$sp_observation' AND ss.sites_id=sp.sites_id ORDER BY sp_observation DESC",$DRW_read);
$dataC = $DRW->fetch_row($resultC);
$entryID = $dataC[0];
$sites_name = $dataC[1];
$sp_observationf = $dataC[2];
$sp_url = $dataC[3];
$sites_category_name = $dataC[4];
$sp_image = $dataC[5];
$sp_image_path = $dataC[6];
if(!empty($sp_url)){
	$sp_url = '<a href="http://'.preg_replace('/^https?:\\/\\//i','',$sp_url).'" target="_blank">'.$sp_url.'</a>';
}
if($entryID!=''){
	echo '<tr><td><strong>Entry ID:</strong></td><td>'.$entryID.'</td></tr>';
	echo '<tr><td><strong>Date:</strong></td><td>'.$sp_observationf.'</td></tr>';
	echo '<tr><td><strong>Category:</strong></td><td>'.$sites_category_name.'</td></tr>';
	echo '<tr><td><strong>Site:</strong></td><td>'.$sites_name.'</td></tr>';
	echo '<tr><td><strong>URL:</strong></td><td>'.$sp_url.'</td></tr>';
	echo '<tr><td colspan="2"><img src="'.$sp_image_path.$sp_image.'" style="border: solid 1px #000000;" alt="" /></td></tr>';
}
else {
	echo "<tr><td>Observation has been discontinued.</td></tr>";
}
?>
</table>
</body>
</html>