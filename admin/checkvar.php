<?php 
require_once("../auth_auth.php");
require_once '../includes/functions.php';

@ob_clean();
if(isset($_REQUEST['eid']) && trim($_REQUEST['eid'])!=''){
	$resultC = $DRW->query("SELECT pd.productID,sectorID,categoryID,subCategoryID,companyName,cp.companyID FROM cscan_product_detail pd join cscan_company_product cp on (pd.productID=cp.productID and primary_co=1) join cscan_company cc on (cp.companyID=cc.companyID)
		WHERE entryID='".$DRW->real_escape_string($_REQUEST['eid'])."'",$DRW_read);
	$dataC = $DRW->fetch_row($resultC);
	if(!empty($dataC[0])){
		$productID = $dataC[0];
		$sectorID = $dataC[1];
		$categoryID = $dataC[2];
		$subCategoryID = $dataC[3];
		$company = $dataC[4];
		$companyID = $dataC[5];
		echo "Sector: ".sectorName($sectorID)."<br />";
		echo "Category: ".categoryName($categoryID)."<br />";
		echo "Sub Category: ".subCategoryName($subCategoryID)."<br />";
		echo "Company: ".htmlspecialchars($company);
		echo '<input type="hidden" name="sectorID_v" value="'.$sectorID.'" /><input type="hidden" name="categoryID_v" value="'.$categoryID.'" /><input type="hidden" name="subCategoryID_v" value="'.$subCategoryID.'" /><input type="hidden" name="companyID_v" value="'.$companyID.'" />';
	}
	else{
		echo 'Not Found<input type="hidden" name="sectorID_v" value="0" /><input type="hidden" name="categoryID_v" value="0" /><input type="hidden" name="subCategoryID_v" value="0" /><input type="hidden" name="companyID_v" value="0" />';
	}
}
?>