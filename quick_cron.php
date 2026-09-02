#!/usr/bin/php
<?php
require_once("includes/ehLog_set.php");
$ehL->start(__FILE__);

require_once("includes/dbcon.php");
$DRW->databaseReadWrite_die = 1;
require_once 'includes/functions.php';

$tables = array(
	array('SELECT companyID FROM cscan_company','cscan_company_quick_mc','companyID','mc_ID','mChannelID','SELECT DISTINCT mChannelID from cscan_product_detail pd JOIN cscan_company_product cp ON (pd.productID=cp.productID AND productStatus=1) JOIN cscan_company cc ON (cc.companyID=cp.companyID) WHERE (primary_co=1 OR isApprovedCo=1) AND cp.companyID='),
	array('SELECT companyID FROM cscan_company','cscan_company_quick_mp','companyID','mp_ID','mPanelID','SELECT DISTINCT mPanelID from cscan_product_detail pd JOIN cscan_company_product cp ON (pd.productID=cp.productID AND productStatus=1) JOIN cscan_company cc ON (cc.companyID=cp.companyID) WHERE (primary_co=1 OR isApprovedCo=1) AND cp.companyID='),
	array('SELECT companyID FROM cscan_company','cscan_company_quick_scsc','companyID','scsc_ID','sectorID','SELECT DISTINCT scsc_sectorID from cscan_product_detail pd JOIN cscan_company_product cp ON (pd.productID=cp.productID AND productStatus=1) JOIN cscan_scsc_product as scsc ON (pd.productID=scsc.productID) JOIN cscan_company cc ON (cc.companyID=cp.companyID) WHERE (primary_co=1 OR isApprovedCo=1) AND cp.companyID='),
	array('SELECT affinityID FROM cscan_affinity','cscan_affinity_quick_mc','affinityID','mc_ID','mChannelID','SELECT DISTINCT mChannelID from cscan_product_detail pd JOIN cscan_affinity_product cp ON (pd.productID=cp.productID AND productStatus=1) WHERE cp.affinityID='),
	array('SELECT affinityID FROM cscan_affinity','cscan_affinity_quick_mp','affinityID','mp_ID','mPanelID','SELECT DISTINCT mPanelID from cscan_product_detail pd JOIN cscan_affinity_product cp ON (pd.productID=cp.productID AND productStatus=1) WHERE cp.affinityID='),
	array('SELECT affinityID FROM cscan_affinity','cscan_affinity_quick_scsc','affinityID','scsc_ID','sectorID','SELECT DISTINCT scsc_sectorID from cscan_product_detail pd JOIN cscan_affinity_product cp ON (pd.productID=cp.productID AND productStatus=1) JOIN cscan_scsc_product as scsc ON (pd.productID=scsc.productID) WHERE cp.affinityID='),
);
	
foreach($tables as $data){
	list($q1,$table,$id,$sid,$pid,$q2) = $data;
	$result = $DRW->query($q1,$DRW_read2);
	while($row = $DRW->fetch_row( $result )){
		$cid = $row[0];
		
		$sqlu = "DELETE FROM $table WHERE $id=$cid";
		$DRW->query($sqlu,$DRW_main);
		$resultC = $DRW->query($q2.$cid);
		while($dataC = $DRW->fetch_row($resultC)){
			if(!empty($dataC[0])){
				$sqlu = "REPLACE INTO $table ($id,$sid) VALUES ($cid,$dataC[0])";
				$DRW->query($sqlu,$DRW_main);
			}
		}
	}
}

$sqlu = "UPDATE cscan_edc SET edc_panelists=2 WHERE edc_panelists=1";
$DRW->query($sqlu,$DRW_main);
//$q1 = "SELECT DISTINCT edc_id from cscan_product_detail pd JOIN cscan_panelists_product pp ON (pd.productID=pp.productID AND productStatus=1) JOIN cscan_edc_postalcode edc ON (pp.pppostalcode=edc.pppostalcode)";
$q1 = "SELECT DISTINCT cscan_edc.edc_id from cscan_edc JOIN cscan_edc_postalcode ON(cscan_edc.edc_id=cscan_edc_postalcode.edc_id)";
$result = $DRW->query($q1,$DRW_read2);
while($row = $DRW->fetch_row($result)){
	$edc_id = $row[0];
	$sqlu = "UPDATE cscan_edc SET edc_panelists=1 WHERE edc_id=".$edc_id;
	$DRW->query($sqlu,$DRW_main);
}
$sqlu = "UPDATE cscan_edc SET edc_panelists=0 WHERE edc_panelists=2";
$DRW->query($sqlu,$DRW_main);

$ehL->stop();
?>