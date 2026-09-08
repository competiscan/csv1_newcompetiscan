#!/usr/bin/php
<?php  
require_once("includes/ehLog_set.php");
$ehL->start(__FILE__);

require_once('includes/dbcon.php');
$DRW->databaseReadWrite_die = 1;
require_once('includes/clean.php');
require_once('includes/functions.php');

$aatd = '';
$qU = "SELECT MAX(actual_addedToDatabase) FROM cscan_variant_check";
$resultU = $DRW->query($qU,$DRW_read);
$dataU = $DRW->fetch_row($resultU);
$actual_addedToDatabase = $dataU[0];
if($actual_addedToDatabase!=''){
	$aatd = " AND actual_addedToDatabase>'$actual_addedToDatabase'";
}
$qU = "SELECT productID FROM cscan_product_detail WHERE isVariant=1 AND productStatus=1$aatd ORDER BY actual_addedToDatabase";
$resultU = $DRW->query($qU,$DRW_read);
while($dataU = $DRW->fetch_row($resultU)){
	$productID = (int)$dataU[0];
	
	$variantArray = array();
	getAllVariantsArray($productID,$variantArray);
	
	foreach($variantArray as $p=>$e){
		$q = "SELECT vid,sectorID,categoryID,subCategoryID,actual_addedToDatabase FROM cscan_product_detail WHERE productID=$p";
		$result = $DRW->query($q,$DRW_read);
		$data = $DRW->fetch_row($result);
		$vid = explode(',',$data[0]);
		sort($vid);
		$vid = implode(',',$vid);
		$sectorID = explode(',',$data[1]);
		sort($sectorID);
		$sectorID = implode(',',$sectorID);
		$categoryID = explode(',',$data[2]);
		sort($categoryID);
		$categoryID = implode(',',$categoryID);
		$subCategoryID = explode(',',$data[3]);
		sort($subCategoryID);
		$subCategoryID = implode(',',$subCategoryID);
		$actual_addedToDatabase = $data[4];
		
		$co = 0;
		$cos = array();
		$resultC = $DRW->query("SELECT companyID,primary_co FROM cscan_company_product WHERE productID=$p ORDER BY companyID",$DRW_read);
		while($dataC = $DRW->fetch_row($resultC)){
			if($dataC[1]==1){
				$co = $dataC[0];
			}
			else{
				$cos[] = $dataC[0];
			}
		}
		$cos = implode(',',$cos);
		
		$resultC = $DRW->query("SELECT groupID FROM cscan_variant_check WHERE productID=$productID",$DRW_read);
		$dataC = $DRW->fetch_row($resultC);
		if(!empty($dataC[0])){
			$groupID = $dataC[0];
		}
		else{
			$groupID = $productID;
		}
		
		$del = "DELETE FROM cscan_variant_check WHERE productID=$p";
		$DRW->query($del,$DRW_main);
		
		$del = "INSERT INTO cscan_variant_check 
		(groupID,productID,sectorID,categoryID,subCategoryID,companyID,secondCompanyID,old_sectorID,old_categoryID,old_subCategoryID,old_companyID,old_secondCompanyID,vid,actual_addedToDatabase) 
		VALUES 
		($groupID,$p,'$sectorID','$categoryID','$subCategoryID',$co,'$cos','$sectorID','$categoryID','$subCategoryID',$co,'$cos','$vid','$actual_addedToDatabase')";
		$DRW->query($del,$DRW_main);
	}
}

$ehL->stop();
?>