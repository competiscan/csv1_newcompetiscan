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
//$img_arr=array('png','jpg','gif','jpeg');
//$vid_arr=array('mp4','mov','avi','mkv','webm');

$sql = "SELECT id,digital_record_id FROM cscan_biscience_digital_fields where status=0 limit 100 ";
$result = $DRW->query($sql,$DRW_read2);
if($DRW->num_rows($result)>0){
    while( $row = $DRW->fetch_row($result)){
        $fields_id              = trim($row[0]);
        $digital_record_id =trim($row[1]);
        ## check for digital records
        $sqlChkDigtal = "SELECT creation_date,location,channel,advertiser_name,creative_wrapper FROM cscan_digital_records202101 where id='".$digital_record_id."'";
        $resultDigital = $DRW->query($sqlChkDigtal,$DRW_read2);
        if($DRW->num_rows($resultDigital)>0){
            $rowDigitailData = $DRW->fetch_row($resultDigital);
            $creation_date   = date('Y-m',strtotime(trim($rowDigitailData[0])));
            $location        = trim($rowDigitailData[1]);
            $channel         = trim($rowDigitailData[2]);
            $advertiser_name = trim($rowDigitailData[3]);
             ## check for digital duplicate records
            $sqlChkDigtalSql = "SELECT id,advertiser_domain,publisher,monitored_page,campaign_landing_page FROM cscan_digital_records202101 where status=2 AND advertiser_name='".$advertiser_name."' AND LOWER(location)='".$DRW->real_escape_string($location)."' AND LEFT(creation_date,7)='".$creation_date."' AND channel='".$channel."'";
            $resultDigitalQuery = $DRW->query($sqlChkDigtalSql,$DRW_read2);
            if($DRW->num_rows($resultDigitalQuery)>0){
                while($rowData = $DRW->fetch_row($resultDigitalQuery)){
                    $digital_id           = trim($rowData[0]);
                    $advertiser_domain    = trim($rowData[1]);
                    $publisher            = trim($rowData[2]);
                    $monitored_page       = trim($rowData[3]);
                    $campaign_landing_page=trim($rowData[4]); 
                    $sqlCheck = "SELECT id FROM cscan_digital_records_duplicate_fields where digital_fields_id='".$fields_id."' AND advertiser_domain='".$advertiser_domain."' AND publisher='".$publisher."' AND monitored_page='".$monitored_page."' AND campaign_landing_page='".$campaign_landing_page."'";
                    $resultQuery = $DRW->query($sqlCheck,$DRW_read2);
                    if($DRW->num_rows($resultQuery)<1){
                       $sqlp = "INSERT INTO cscan_digital_records_duplicate_fields (digital_record_id,digital_fields_id,advertiser_domain,publisher,monitored_page,campaign_landing_page) values('".$digital_id."','".$fields_id."','".$DRW->real_escape_string($advertiser_domain)."','".$DRW->real_escape_string($publisher)."','".$DRW->real_escape_string($monitored_page)."','".$DRW->real_escape_string($campaign_landing_page)."')";
                        $DRW->query($sqlp, $DRW_main);
                        
                    }
                }
            }

        }
        $sqlUpdate = "Update cscan_biscience_digital_fields set status=1 where id='".$fields_id."'";
        $DRW->query($sqlUpdate, $DRW_main);
        
    }
    
    echo 'completed: ';
    echo 'End: '.date("Y-m-d H:i:s").'</br></br>';
    die;
}

echo 'Completed...';
die;
?>