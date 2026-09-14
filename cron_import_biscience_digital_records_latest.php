<?php
//require_once("includes/ehLog_set.php");
//$ehL->start(__FILE__);
require_once ("includes/dbcon.php");
$DRW->databaseReadWrite_die = 1;
//include_once 'includes/functions.php';

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

$sql = "SELECT id,creation_date,location,channel,advertiser_name,compaign_title,creative_wrapper,publisher,impressions,spend,monitored_page,file_id,advertiser_domain,campaign_landing_page,compaign_title FROM cscan_digital_records where productID=0 limit 5000";
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
        $compaign_title   = trim(strip_tags($row[14]));

        $state_code='';
        $country='';
        $updt=0;
        //$record_month= date('m',strtotime(trim($row[1])));
        //$record_year= date('Y',strtotime(trim($row[1])));
        $start_date=date("Y-m-01", strtotime(trim($row[1])));
        $end_date=date("Y-m-t", strtotime(trim($row[1])));
        
        $location_state_arr=explode(',',$location);
        if(!empty(trim(end($location_state_arr)))){
            $location_state_code=trim(end($location_state_arr));            
        }else{
            $location_state_code=$location;
        }
       
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
        $dup_status=false;
        $ins_status=false;

        ## check for duplicate records
        $sqlChkDup = "SELECT id,spend,impressions FROM cscan_digital_processed_records where creative_wrapper='".$DRW->real_escape_string($creative_wrapper)."' AND creation_date>='".$start_date."' AND creation_date<='".$end_date."' limit 1";
        $resultDup = $DRW->query($sqlChkDup,$DRW_read2);
        if($DRW->num_rows($resultDup)>0){
            $dataSp = $DRW->fetch_row($resultDup);
            $old_id=$dataSp[0];
            $spend_old=$dataSp[1];
            $imp_old=$dataSp[2];
            $new_spend=$spend_old+$spend;
            $new_imp=$imp_old+$impressions;

            $sqlU = "UPDATE cscan_digital_processed_records set spend='".$new_spend."',impressions='".$new_imp."' where id='".$old_id."'";
            $DRW->query($sqlU, $DRW_main);
            $dup_status=true;           
            
        }else{
            $sqlp = "INSERT INTO cscan_digital_processed_records (digital_record_id,file_id,spend,impressions,company_id,advertiser_name,advertiser_domain,monitored_page,campaign_landing_page,creative_wrapper,creation_date) values('".$id."','".$file_id."','".$spend."','".$impressions."','".$companyID."','".$DRW->real_escape_string($advertiser_name)."','".$DRW->real_escape_string($advertiser_domain)."','".$DRW->real_escape_string($monitored_page)."','".$DRW->real_escape_string($campaign_landing_page)."','".$DRW->real_escape_string($creative_wrapper)."','".$creation_date."')";
            $DRW->query($sqlp, $DRW_main);
            $old_id = $DRW->insert_id($DRW_main);
            $sqlR = "Update cscan_digital_records set productID=1 where id='".$id."'";
            $DRW->query($sqlR, $DRW_main);
            $ins_status=true;

        }
        if($dup_status || $ins_status){

            // Insert Location 
            $sqlChk_loc= "SELECT id FROM cscan_digital_processed_location where processed_record_id='".$old_id."' AND LOWER(location)='".$DRW->real_escape_string(strtolower($location))."' limit 1";
            $result_loc = $DRW->query($sqlChk_loc,$DRW_read2);
            if($DRW->num_rows($result_loc)<=0){
                $sqlIns_loc = "INSERT INTO cscan_digital_processed_location (processed_record_id,digital_record_id,location,location_state_code) values('".$old_id."','".$id."','".$DRW->real_escape_string($location)."','".$DRW->real_escape_string($location_state_code)."')";
                $DRW->query($sqlIns_loc, $DRW_main);
            }

            // Insert Media Channel 
            $sqlChk_mch= "SELECT id FROM cscan_digital_processed_mchannel where processed_record_id='".$old_id."' AND mchannel_id='".$mChannelID."' limit 1";             
            $result_mch = $DRW->query($sqlChk_mch,$DRW_read2);
            if($DRW->num_rows($result_mch)<=0){                
                $sqlIns_mch = "INSERT INTO cscan_digital_processed_mchannel (processed_record_id,digital_record_id,channel,mchannel_id) values('".$old_id."','".$id."','".$DRW->real_escape_string($channel)."','".$DRW->real_escape_string($mChannelID)."')";
                $DRW->query($sqlIns_mch, $DRW_main);
            }

            // Insert Publisher 
            $sqlChk_pub= "SELECT id FROM cscan_digital_processed_publisher where processed_record_id='".$old_id."' AND publisher='".$DRW->real_escape_string($publisher)."' limit 1";             
            $result_pub = $DRW->query($sqlChk_pub,$DRW_read2);
            if($DRW->num_rows($result_pub)<=0){                
                $sqlIns_pub = "INSERT INTO cscan_digital_processed_publisher (processed_record_id,digital_record_id,publisher) values('".$old_id."','".$id."','".$DRW->real_escape_string($publisher)."')";
                $DRW->query($sqlIns_pub, $DRW_main);
            }

            // Insert period (month and year)
            /*
            $sqlChk_period= "SELECT id FROM cscan_digital_processed_period where processed_record_id='".$old_id."' AND Month(creation_date)='".$record_month."' AND YEAR(creation_date)='".$record_year."'";             
            $result_period = $DRW->query($sqlChk_period,$DRW_read2);
            if($DRW->num_rows($result_period)<=0){                
                $sqlIns_period = "INSERT INTO cscan_digital_processed_period (processed_record_id,digital_record_id,creation_date) values('".$old_id."','".$id."','".$DRW->real_escape_string($creation_date)."')";
                $DRW->query($sqlIns_period, $DRW_main);
            }
            */

            // Insert Digital Source
            $sqlChk_source= "SELECT id FROM cscan_digital_processed_source where processed_record_id='".$old_id."' AND digital_source='".$isdigitalsource."' limit 1";             
            $result_source = $DRW->query($sqlChk_source,$DRW_read2);
            if($DRW->num_rows($result_source)<=0){                
                $sqlIns_source = "INSERT INTO cscan_digital_processed_source (processed_record_id,digital_record_id,digital_source) values('".$old_id."','".$id."','".$isdigitalsource."')";
                $DRW->query($sqlIns_source, $DRW_main);
            } 
            
            // Insert compaign title
            $sqlChk_title= "SELECT id FROM cscan_digital_processed_title where processed_record_id='".$old_id."' AND LOWER(compaign_title)='".$DRW->real_escape_string(strtolower($compaign_title))."' limit 1";             
            $result_title = $DRW->query($sqlChk_title,$DRW_read2);
            if($DRW->num_rows($result_title)<=0){                
                $sqlIns_title = "INSERT INTO cscan_digital_processed_title (processed_record_id,digital_record_id,compaign_title) values('".$old_id."','".$id."','".$DRW->real_escape_string($compaign_title)."')";
                $DRW->query($sqlIns_title, $DRW_main);
            }
        } 

        if($dup_status){
            $sqlR = "Update cscan_digital_records set productID=2 where id='".$id."'";
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