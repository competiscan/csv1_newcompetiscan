#!/usr/bin/php
<?php 
require_once("includes/ehLog_set.php");
$ehL->start(__FILE__);

require_once('includes/dbcon.php');
$DRW->databaseReadWrite_die = 1;
require_once('includes/clean.php');
require_once('includes/functions.php');

//$sqlu = "UPDATE cscan_product_detail SET isVariant=2";
//$DRW->query($sqlu,$DRW_main);

$qU = "SELECT productID,variantID FROM cscan_product_detail WHERE variantID<>0 ORDER BY actual_addedToDatabase LIMIT 10";
$resultU = $DRW->query($qU,$DRW_read);
while($dataU = $DRW->fetch_row($resultU)){
	$productID = (int)$dataU[0];
	
	$variantArray = array();
	getAllVariantsArray($productID,$variantArray);
	
	foreach($variantArray as $p=>$e){
		$sqlu = "UPDATE cscan_product_detail SET isVariant=1 WHERE productID=$p";
		echo $sqlu."\n";
		//$DRW->query($sqlu,$DRW_main);
	}
}

//$sqlu = "UPDATE cscan_product_detail SET isVariant=0 WHERE isVariant=2";
//$DRW->query($sqlu,$DRW_main);

$ehL->stop();
?>