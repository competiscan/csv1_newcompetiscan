#!/usr/bin/php
<?php
require_once ("includes/dbcon.php");
$DRW->databaseReadWrite_die = 1;
include_once 'includes/functions.php';
ini_set("memory_limit", "-1");
set_time_limit(0);
date_default_timezone_set('America/Chicago');
echo "Start Date Time : ".date('Y-m-d h:i:s');

$selectsql = "SELECT productID,entryID,COUNT(entryID) FROM cscan_product_detail where entryID !='' and entryID IS NOT NULL GROUP BY entryID HAVING COUNT(entryID) > 1"; 
$ressql = $DRW->query($selectsql, $DRW_read2);
if ($DRW->num_rows($ressql) > 0) { 
    while ($row = $DRW->fetch_assoc($ressql)) {        
        $productID   = $row['productID'];
        $entryID     = $row['entryID']; 
        $entryDate   = substr($entryID,0,10);
        $checkSql = "SELECT incID from cscan_entry_inc where entryDate='".$entryDate."'";
        $checkRes = $DRW->query($checkSql, $DRW_read2);
        $dataRes = $DRW->fetch_row($checkRes);
        if(!empty($dataRes[0])) {
            $incID=$dataRes[0];
            $new_incID=$incID+1;
            $new_entryID=$entryDate.'-'.$new_incID;            
            $sql_updt= "UPDATE cscan_entry_inc set incID='".$new_incID."' where entryDate='".$entryDate."'";
            $DRW->query($sql_updt,$DRW_main);
            
            $sql_updt= "UPDATE cscan_product_detail set entryID='".$new_entryID."',entryID_sort2='".$new_incID."' where productID='".$productID."'";
            $DRW->query($sql_updt,$DRW_main);
        }        
    }
}

echo ' Completed';  
echo " End Date Time : ".date('Y-m-d h:i:s');
?>
