#!/usr/bin/php
<?php
require_once ("includes/dbcon.php");
$DRW->databaseReadWrite_die = 1;
include_once 'includes/functions.php';
ini_set("memory_limit", "-1");
set_time_limit(0);
date_default_timezone_set('America/Chicago');
echo "Start Date Time : ".date('Y-m-d h:i:s');
$todaydate    =    date('Y-m-d');
$todaydatetime = date('Y-m-d h:i:s');
$max_difference_days=181;

$sql_updt_panelist_prod= "UPDATE cscan_panelists_product set real_time_ppmv=ppmv where real_time_ppmv<>ppmv";
$DRW->query($sql_updt_panelist_prod,$DRW_main);

$record_fetch_date    =  date('Y-m-d', strtotime('-'.$max_difference_days.' day', strtotime($todaydate)));
$sql  ="delete from cscan_real_time_emv where ppdate<'".$record_fetch_date."'";
$DRW->query($sql,$DRW_main);
$selectsql = "SELECT pp.panelist_id,pp.productID,pp.ppdate,pp.ppmv,LEFT(pd.entryID,10) as entryIdDate FROM cscan_panelists_product pp left join cscan_product_detail pd on(pd.productID=pp.productID) where LEFT(pd.entryID,10)>='".$record_fetch_date."' AND pp.ppmv>0"; 

$ressql = $DRW->query($selectsql, $DRW_read2);
if ($DRW->num_rows($ressql) > 0) { 
    while ($row = $DRW->fetch_assoc($ressql)) {
        $panelistid  = $row['panelist_id'];
        $productID   = $row['productID'];
        $ppdate      = $row['ppdate'];
        $ppmv        = $row['ppmv'];
        $entryIdDate = $row['entryIdDate'];
        $checkSql = "SELECT count(*) from cscan_real_time_emv where panelist_id='".$panelistid."' AND productID='".$productID."' AND ppdate='".$ppdate."' AND update_date>='".$todaydate."'";
        $checkRes = $DRW->query($checkSql, $DRW_read2);
        $dataRes = $DRW->fetch_row($checkRes);
        if(empty($dataRes[0])) {
            $days_difference = dateDiffInDays($todaydate,$entryIdDate);
            if($days_difference==0){
                $days_difference=1;
            }
            
            if($days_difference<=$max_difference_days) {
                
                $factorsql = "SELECT multiplier from cscan_real_time_emv_factor where days_left='".$days_difference."'";
                $resfactor = $DRW->query($factorsql, $DRW_read2);
                if($DRW->num_rows($resfactor)>0){
                   $row_factor = $DRW->fetch_assoc($resfactor);
                   $factor=$row_factor['multiplier'];
                   $real_time_ppmv =  round(($ppmv*$factor),2);
                   
                   $checkSql2 = "SELECT count(*) from cscan_real_time_emv where panelist_id='".$panelistid."' AND productID='".$productID."'";
                   $checkRes2 = $DRW->query($checkSql2, $DRW_read2);
                   $dataRes2 = $DRW->fetch_row($checkRes2);
                    if(!empty($dataRes2[0])) {
                        $sql_updt= "UPDATE cscan_real_time_emv set ppmv='".$ppmv."',real_time_ppmv='".$real_time_ppmv."',update_date='".$todaydatetime."' where panelist_id='".$panelistid."' AND productID='".$productID."'";
                        $DRW->query($sql_updt,$DRW_main);                      

                    }else{
                        $sql_ins= "INSERT into cscan_real_time_emv (panelist_id,productID,ppdate,ppmv,real_time_ppmv,insert_date,update_date) values('".$panelistid."','".$productID."','".$ppdate."','".$ppmv."','".$real_time_ppmv."','".$todaydatetime."','".$todaydatetime."')";
                        $DRW->query($sql_ins,$DRW_main);                       
                    }
                }
            }else{
              $sql  ="delete from cscan_real_time_emv where panelist_id='".$panelistid."' AND productID='".$productID."'";
              $DRW->query($sql,$DRW_main);  
            }
        }        
    }
} 

$selectsql_realtime = "SELECT panelist_id,productID,ppdate,ppmv,real_time_ppmv FROM cscan_real_time_emv";
$ressql_query = $DRW->query($selectsql_realtime, $DRW_read2);
if ($DRW->num_rows($ressql_query) > 0) {
    while ($rowData = $DRW->fetch_assoc($ressql_query)) {
           $panelistid = $rowData['panelist_id'];
           $productID  = $rowData['productID'];
           $ppdate     = $rowData['ppdate'];
           $real_time_ppmv = $rowData['real_time_ppmv'];
           $sql_updt_real_time= "UPDATE cscan_panelists_product set real_time_ppmv='".$real_time_ppmv."' where panelist_id='".$panelistid."' AND productID='".$productID."' AND ppdate='".$ppdate."'";
           $DRW->query($sql_updt_real_time,$DRW_main);           
    }
}
echo 'Completed';  
echo "End Date Time : ".date('Y-m-d h:i:s');

function dateDiffInDays($date1, $date2)  
{ 
    // Calculating the difference in timestamps 
    $diff = strtotime($date2) - strtotime($date1);      
    // 1 day = 24 hours 
    // 24 * 60 * 60 = 86400 seconds 
    return abs(round($diff / 86400)); 
}
?>
