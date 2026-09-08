#!/usr/bin/php
<?php
require_once("includes/ehLog_set.php");
$ehL->start(__FILE__);

require_once("includes/dbcon.php");
$DRW->databaseReadWrite_die = 1;

if(empty($SPHINX_partitions)){
	$ehL->write("can't determine my mysqlrole!");
	$ehL->stop(false);
	exit;
}

$outArray = array();
$sql_P = "SELECT MAX(max_dts_updated) FROM cscan_delta_counter WHERE counter_id=$SPHINX_partitions";
$result_P = $DRW->query($sql_P,$DRW_read);
$row = $DRW->fetch_row( $result_P );
if(empty($row[0])){
	$max_dts_updated = date('Y-m-d H:i:s');
}
else{
	$max_dts_updated = $row[0];
}
$partition_y = (int)date('Y') - 1;
$partitions = array();
$partitions['2007'] = "addedToDatabase>'0000-00-00 00:00:00' AND addedToDatabase<'2008-01-01 00:00:00'";
$sql_P = "select distinct(left(addedToDatabase,4)) from cscan_product_detail where addedToDatabase>'2007-12-31 23:59:59' AND addedToDatabase<'$partition_y-01-01 00:00:00' ORDER BY addedToDatabase";
$result_P = $DRW->query($sql_P,$DRW_read);
while($row = $DRW->fetch_row( $result_P )){
	$year = (int)$row[0];
	$year_plus = $year+1;
	$partitions[$row[0]] = "addedToDatabase>='$year-01-01 00:00:00' AND addedToDatabase<'$year_plus-01-01 00:00:00'";
}
foreach($partitions as $y=>$p){
	$resultC = $DRW->query("SELECT count(*) FROM cscan_product_detail cp
		WHERE $p",$DRW_read);
	$dataC = $DRW->fetch_row($resultC);
	$product = $dataC[0];
	
	$resultC = $DRW->query("SELECT count(*) FROM cscan_product_detail cp
		WHERE actual_addedToDatabase>='$max_dts_updated' AND $p",$DRW_read);
	$dataC = $DRW->fetch_row($resultC);
	$product_detail = $dataC[0];
	
	$resultC = $DRW->query("SELECT count(*) FROM cscan_document cd JOIN cscan_product_detail cp on (cd.productID=cp.productID)
		WHERE document_createddate>='$max_dts_updated' AND $p",$DRW_read);
	$dataC = $DRW->fetch_row($resultC);
	$document = $dataC[0];
	
	if($product_detail>0){
		$ehL->write($y.': '.$product.' base_index_prod2'.$y.', base_index_prodstar2'.$y.': '.$product_detail);////
		//$outArray[] = shell_exec('indexer --quiet --rotate base_index_prod2'.$y);
		//$outArray[] = shell_exec('indexer --quiet --rotate base_index_prodstar2'.$y);
	}
	if($document>0){
		$ehL->write($y.': '.$product.' base_index_prod'.$y.', base_index_prodstar'.$y.': '.$document);////
		//$outArray[] = shell_exec('indexer --quiet --rotate base_index_prod'.$y);
		//$outArray[] = shell_exec('indexer --quiet --rotate base_index_prodstar'.$y);
	}
}
$savedQ = "REPLACE INTO cscan_delta_counter (counter_id,max_dts_updated) VALUES ($SPHINX_partitions, NOW())";
$rs = $DRW->query($savedQ,$DRW_main);

foreach($outArray as $o){
	if(trim($o)!=''){
		$ehL->write($o);
	}
}
$ehL->stop();
?>