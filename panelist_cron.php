#!/usr/bin/php
<?php
require_once("includes/ehLog_set.php");
$ehL->start(__FILE__);

require_once("includes/dbcon.php");
$DRW->databaseReadWrite_die = 1;
require_once 'includes/functions.php';

$sql_P = "SELECT DISTINCT competi_id,cp.panelist_id FROM cscan_panelists cp JOIN cscan_panelists_product pp ON (cp.panelist_id=pp.panelist_id) JOIN cscan_product_detail pd ON (pp.productID=pd.productID)";
$result_P = $DRW->query($sql_P,$DRW_read);
while($row = $DRW->fetch_row( $result_P )){
	$cid = $row[0];
	$pid = $row[1];
	
	//same as addProductInclude.php
	
	$sqlu = "DELETE FROM cscan_panelist_affinity WHERE panelist_id=$pid";
	$DRW->query($sqlu,$DRW_main);
	$resultC = $DRW->query("SELECT DISTINCT pp.affinityID FROM cscan_affinity_product pp JOIN cscan_panelists_product as pa ON (pp.productID=pa.productID) JOIN cscan_product_detail pd ON (pp.productID=pd.productID)
		WHERE pa.panelist_id=$pid AND pd.sectorID NOT LIKE '%219%' AND pd.sectorID NOT REGEXP '[[:<:]]219[[:>:]]'",$DRW_read);// AND pd.sectorID NOT LIKE '%266%' AND pd.sectorID NOT REGEXP '[[:<:]]266[[:>:]]'
	while($dataC = $DRW->fetch_row($resultC)){
		$sqlu = "REPLACE INTO cscan_panelist_affinity (panelist_id,affinityID) VALUES ($pid,$dataC[0])";
		$DRW->query($sqlu,$DRW_main);
	}
	
	$sqlu = "DELETE FROM cscan_panelist_company WHERE panelist_id=$pid";
	$DRW->query($sqlu,$DRW_main);
	$resultC = $DRW->query("SELECT DISTINCT pp.companyID FROM cscan_company_product pp JOIN cscan_panelists_product as pa ON (pp.productID=pa.productID) JOIN cscan_product_detail pd ON (pa.productID=pd.productID) 
		WHERE pa.panelist_id=$pid AND primary_co=1 AND ((mChannelID=1 AND delmethid=1) OR mChannelID=3) AND (mTypeID='1' OR mTypeID='3') AND pd.sectorID NOT LIKE '%219%' AND pd.sectorID NOT REGEXP '[[:<:]]219[[:>:]]'",$DRW_read);// AND pd.sectorID NOT LIKE '%266%' AND pd.sectorID NOT REGEXP '[[:<:]]266[[:>:]]'
	while($dataC = $DRW->fetch_row($resultC)){
		$sqlu = "REPLACE INTO cscan_panelist_company (panelist_id,companyID) VALUES ($pid,$dataC[0])";
		$DRW->query($sqlu,$DRW_main);
	}
}

$ehL->stop();
?>