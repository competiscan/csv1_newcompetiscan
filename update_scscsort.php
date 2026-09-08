<?php include_once('includes/dbcon.php');
//$DRW->databaseReadWrite_die = 1;
set_time_limit(0);
ini_set('memory_limit', '-1');
require_once('includes/clean.php');
require_once('includes/functions.php');
  $sql_ma = "SELECT productID,scsc_sectorID,scsc_categoryID,scsc_subCategoryID,scsc_subSubCategoryID FROM cscan_scsc_product WHERE scsc_sort=1 AND status=0 limit 500000 "; 
	$rs_ma = $DRW->query($sql_ma,$DRW_read);

	while($row_ma = $DRW->fetch_row($rs_ma)){
		 $productID = $row_ma[0];
                 $scsc_sectorID = $row_ma[1];
                 $scsc_categoryID = $row_ma[2];
                 $scsc_subCategoryID = $row_ma[3];
                 $scsc_subSubCategoryID = $row_ma[4];
		$last_prd_sql = "UPDATE cscan_product_detail SET scsc_sort=1,scsc_sectorID_sort='".$scsc_sectorID."', 
                    scsc_categoryID_sort='".$scsc_categoryID."',scsc_subCategoryID_sort='".$scsc_subCategoryID."',
                    scsc_subSubCategoryID_sort='".$scsc_subSubCategoryID."'
                    WHERE productID='$productID'";
                
			$DRW->query($last_prd_sql,$DRW_main);
		$last_scsc_sql = "UPDATE cscan_scsc_product SET status=1 WHERE productID='$productID'";
			$DRW->query($last_scsc_sql,$DRW_main);	
			
	}
?>

