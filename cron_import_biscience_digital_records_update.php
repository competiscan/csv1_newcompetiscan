<?php
require_once("includes/ehLog_set.php");
$ehL->start(__FILE__);

require_once ("includes/dbcon.php");
$DRW->databaseReadWrite_die = 1;
include_once 'includes/functions.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set("memory_limit", "-1");
set_time_limit(0);

echo 'Start: '.date("Y-m-d H:i:s").'</br></br>';
if(!defined('ENV')){
    define('ENV',getenv('SERVER_NAME'));
}


$sql="SELECT id,digital_record_id FROM cscan_biscience_digital_fields where advertiser_domain is null or advertiser_domain='' or publisher is null limit 2000";
$result = $DRW->query($sql,$DRW_read2);
if($DRW->num_rows($result)>0){
    while( $row = $DRW->fetch_row($result)){
        $id              = trim($row[0]);
        $digital_record_id= trim($row[1]);
        $sql2="SELECT advertiser_domain,publisher,monitored_page,campaign_landing_page FROM cscan_digital_records where id='".$digital_record_id."' ";
        $result2 = $DRW->query($sql2,$DRW_read2);
        if($DRW->num_rows($result2)>0){  
            $data = $DRW->fetch_row($result2);
            $advertiser_domain=trim($data[0]);
            $publisher=trim($data[1]);
            $monitored_page=trim($data[2]);
            $campaign_landing_page=trim($data[3]); 
            $sqlU = "UPDATE cscan_biscience_digital_fields set advertiser_domain='".$DRW->real_escape_string($advertiser_domain)."',publisher='".$DRW->real_escape_string($publisher)."',monitored_page='".$DRW->real_escape_string($monitored_page)."',campaign_landing_page='".$DRW->real_escape_string($campaign_landing_page)."' where id='".$id."'";
            $DRW->query($sqlU, $DRW_main);
        }
    }
}




/*
$sql="SELECT id,status FROM cscan_digital_records_test where update_status=0 AND status>0 limit 50000";
$result = $DRW->query($sql,$DRW_read2);
if($DRW->num_rows($result)>0){
    while( $row = $DRW->fetch_row($result)){
        $id              = trim($row[0]);
        $status          = trim($row[1]);   
        $sqlU = "UPDATE cscan_digital_records set status='".$status."' where id='".$id."'";
        $DRW->query($sqlU, $DRW_main);
        $sqlU2 = "UPDATE cscan_digital_records_test set update_status=1 where id='".$id."'";
        $DRW->query($sqlU2, $DRW_main);
    }
}
*/

/*
$sql="SELECT id,advertiser_name,location,time_period FROM cscan_biscience_digital_fields where digital_record_id IS NULL  limit 3000";

$result = $DRW->query($sql,$DRW_read2);
if($DRW->num_rows($result)>0){
    while( $row = $DRW->fetch_row($result)){
        $id =              trim($row[0]);
        $advertiser_name = trim($row[1]);
        $location        = trim($row[2]);
        $time_period     = trim($row[3]);
        
        $sqlChkDup = "SELECT id,file_id,channel FROM cscan_digital_records where LOWER(advertiser_name)='". strtolower($advertiser_name)."' AND LOWER(location)='".$DRW->real_escape_string(strtolower($location))."' AND LEFT(creation_date,10)='".$time_period."' AND status=1 limit 1"; 
        $resultDup = $DRW->query($sqlChkDup,$DRW_read2);
        if($DRW->num_rows($resultDup)>0){
            $dataSp = $DRW->fetch_row($resultDup);
            $digital_record_id=trim($dataSp[0]);
            $file_id=trim($dataSp[1]);
            $channel=trim($dataSp[2]);
            $sqlU = "UPDATE cscan_biscience_digital_fields set digital_record_id='".$digital_record_id."',file_id='".$file_id."',channel='".$channel."' where id='".$id."'";
            $DRW->query($sqlU, $DRW_main);
        }
    }
    echo 'completed: ';
    echo 'End: '.date("Y-m-d H:i:s").'</br></br>';
    die;
}
*/
echo 'Completed...';
echo ' End: '.date("Y-m-d H:i:s").'</br></br>';

die;
?>