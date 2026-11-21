<?php
require_once("includes/ehLog_set.php");
$ehL->start(__FILE__);

require_once ("includes/dbcon.php");
$DRW->databaseReadWrite_die = 1;
include_once 'includes/functions.php';
//include_once 'includes/thumb.php';
//require_once "Mail.php";
//require_once "Mail/mime.php";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set("memory_limit", "-1");
set_time_limit(0);

echo 'Start: '.date("Y-m-d H:i:s").'</br></br>';
if(!defined('ENV')){
    define('ENV',getenv('SERVER_NAME'));
}
$img_arr=array('png','jpg','gif','jpeg');
$vid_arr=array('mp4','mov','avi','mkv','webm');

$sql = "SELECT id,creation_date,location,channel,advertiser_name,compaign_title,creative_wrapper,publisher,impressions,spend,monitored_page,file_id,advertiser_domain,campaign_landing_page FROM cscan_digital_records where status=0 ORDER BY id limit 4500";
$result = $DRW->query($sql,$DRW_read2);
if($DRW->num_rows($result)>0){
    while( $row = $DRW->fetch_row($result)){
        $id              = trim($row[0]);
        $creation_date   = date('Y-m-d',strtotime(trim($row[1])));
        $location        = trim($row[2]);
        $channel         = trim($row[3]);
        $advertiser_name = trim($row[4]);
        $compaign_title  = trim($row[5]);
        $creative_wrapper= trim($row[6]);
        $publisher       = trim($row[7]);
        $impressions     = trim($row[8]);
        $spend           = trim($row[9]);
        $monitored_page  = trim($row[10]);
        $file_id         = trim($row[11]);
        $advertiser_domain=trim($row[12]);
        $campaign_landing_page=trim($row[13]);

        $state_code='';
        $country='';
        $updt=0;
        $record_month= date('m',strtotime(trim($row[1])));
        $record_year= date('Y',strtotime(trim($row[1])));
       
        $companyID='0';
        $companyName='';
        if($advertiser_name!=''){
            $qcp = "SELECT competiscan_company from cscan_digital_company where LOWER(advertiser_name)='".strtolower($advertiser_name)."' limit 1";
            $resultCP = $DRW->query($qcp, $DRW_read2);
            if($DRW->num_rows($resultCP)>0){
                $dataCP = $DRW->fetch_row($resultCP);
                $cmpName=$dataCP[0];            
            }else{
                $cmpName=$advertiser_name;
            }     
            $qc = "SELECT companyID,companyName FROM cscan_company WHERE LOWER(companyName)='".$DRW->real_escape_string(strtolower($cmpName))."' limit 1";
            $resultC = $DRW->query($qc, $DRW_read2);
            if($DRW->num_rows($resultC)>0){
                $dataC = $DRW->fetch_row($resultC);
                $companyID=$dataC[0]; 
                $companyName=$dataC[1];
            } 
        }
        
        
        //Find Media channel
        $ext= pathinfo($creative_wrapper, PATHINFO_EXTENSION);
        if(in_array(strtolower($ext),$img_arr)){
            $mChannelID = 5;
        }elseif(in_array(strtolower($ext),$vid_arr)){
            $mChannelID = 10;
        }else{
            if(strstr(strtolower($channel),'display')){
                $mChannelID=5;
            }else if(strstr(strtolower($channel),'video')){
                $mChannelID=10;
            }else{
                $mChannelID=0;
            }
            
        }
        //Find digital soource
        $isdigitalsource=1;
        if(strstr(strtolower($channel),'mobile')){
            $isdigitalsource=2;
        }else if(strstr(strtolower($channel),'in app android')){
            $isdigitalsource=3;
        }else if(strstr(strtolower($channel),'in app ios')){
            $isdigitalsource=4;
        }else if(strstr(strtolower($channel),'social')){
            $isdigitalsource=5;
        }

        ## check for duplicate records
        $sqlChkDup = "SELECT id,spend,impressions FROM cscan_biscience_digital_fields where company_id='".$companyID."' AND LOWER(location)='".$DRW->real_escape_string($location)."' AND Month(time_period)='".$record_month."' AND YEAR(time_period)='".$record_year."' AND mchannel_id='".$mChannelID."' AND digital_source_id='".$isdigitalsource."'";
        $resultDup = $DRW->query($sqlChkDup,$DRW_read2);
        if($DRW->num_rows($resultDup)>0){
            $dataSp = $DRW->fetch_row($resultDup);
            $old_id=$dataSp[0];
            $spend_old=$dataSp[1];
            $imp_old=$dataSp[2];
            $new_spend=$spend_old+$spend;
            $new_imp=$imp_old+$impressions;
            
            $sqlU = "UPDATE cscan_biscience_digital_fields set spend='".$new_spend."',impressions='".$new_imp."' where id='".$old_id."'";
            $DRW->query($sqlU, $DRW_main);
            $sqlR = "Update cscan_digital_records set status=2 where id='".$id."'";
            $DRW->query($sqlR, $DRW_main); 
            //continue;

        }else{
            $sqlp = "INSERT INTO cscan_biscience_digital_fields (digital_record_id,file_id,channel,mchannel_id,digital_source_id,spend,impressions,company_id,advertiser_name,location,time_period,advertiser_domain,publisher,monitored_page,campaign_landing_page) values('".$id."','".$file_id."','".$channel."','".$mChannelID."','".$isdigitalsource."','".$spend."','".$impressions."','".$companyID."','".$DRW->real_escape_string($advertiser_name)."','".$DRW->real_escape_string($location)."','".$creation_date."','".$DRW->real_escape_string($advertiser_domain)."','".$DRW->real_escape_string($publisher)."','".$DRW->real_escape_string($monitored_page)."','".$DRW->real_escape_string($campaign_landing_page)."')";
            $DRW->query($sqlp, $DRW_main);
            $sqlR = "Update cscan_digital_records set status=1 where id='".$id."'";
            $DRW->query($sqlR, $DRW_main);
        }

        //echo 'completed: ';        
        //die;
        
    }
    
    echo 'completed: ';
    echo 'End: '.date("Y-m-d H:i:s").'</br></br>';
    die;
}

echo 'Completed...';
die;
?>