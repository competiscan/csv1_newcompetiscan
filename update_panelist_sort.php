<?php include_once('includes/dbcon.php');
//$DRW->databaseReadWrite_die = 1;
require_once('includes/clean.php');
require_once('includes/functions.php');
ini_set("memory_limit","-1");
set_time_limit(0);

  
    $sql_ma2 = "SELECT panelist_id FROM cscan_product_detail WHERE productStatus=1 AND panelist_sort=1 AND mChannelID IN (5,7,9,10)";  
    $rs_ma2 = $DRW->query($sql_ma2,$DRW_read);
    $allpan=array();
    while($row_ma2 = $DRW->fetch_row($rs_ma2)){        
        if($row_ma2[0]!='' && $row_ma2[0]!='NULL'){
            $allpan[]=$row_ma2[0];
            
        }
        
    }


    $sql_ma = "SELECT productID,panelist_id FROM cscan_product_detail WHERE productStatus=1 AND panelist_sort!=1 AND panelist_id!=''";  
    $rs_ma = $DRW->query($sql_ma,$DRW_read);
	
	while($row_ma = $DRW->fetch_row($rs_ma)){
		 $productID = $row_ma[0];
                 $panelist_id=$row_ma[1];
                 		
		//$sql_pa = "SELECT productID FROM cscan_product_detail WHERE productID='".$productID."'"; 
		$sql_pa = "select productID from cscan_product_detail where mChannelID IN (5,9,10) and panelist_id IN ($panelist_id)";
                $rs_pa = $DRW->query($sql_pa,$DRW_read);
		//$panelistarr=array();
            if ($DRW->num_rows($rs_pa) > 0) {
                
                $last_prd_sql = "UPDATE cscan_product_detail SET panelist_sort='1' WHERE productID='".$productID."'";
                $DRW->query($last_prd_sql,$DRW_main);
                
            }			
    }
?>