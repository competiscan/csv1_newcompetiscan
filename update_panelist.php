<?php include_once('includes/dbcon.php');
//$DRW->databaseReadWrite_die = 1;
require_once('includes/clean.php');
require_once('includes/functions.php');
ini_set("memory_limit","-1");
set_time_limit(0);
    $sql_ma = "SELECT productID FROM cscan_product_detail WHERE productStatus=1 AND addedToDatabase>='2017-07-27'";  
	$rs_ma = $DRW->query($sql_ma,$DRW_read);
	
	while($row_ma = $DRW->fetch_row($rs_ma)){
		 $productID = $row_ma[0];
		
		 $sql_pa = "SELECT distinct panelist_id FROM cscan_panelists_product WHERE productID='".$productID."'"; 
		
                $rs_pa = $DRW->query($sql_pa,$DRW_read);
		$panelistarr=array();
            if ($DRW->num_rows($rs_pa) > 0) {
		while($row_pa = $DRW->fetch_row($rs_pa)){ 
		$panelistarr[]= $row_pa[0];		 

		}
                if(!empty($panelistarr)){
                    $panelist_ins= implode(',',$panelistarr);				
                    $last_prd_sql = "UPDATE cscan_product_detail SET panelist_id='".$panelist_ins."' WHERE productID='".$productID."'";
                    $DRW->query($last_prd_sql,$DRW_main);
                }        
            }
			
    }
?>

