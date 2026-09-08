<?php 
require_once ("includes/dbcon.php");
//require_once('includes/digital-dashboard-function.php');
include_once 'includes/functions.php';
$DRW->databaseReadWrite_die = 1;
ini_set('display_errors', 0);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set("memory_limit", "-1");
set_time_limit(0);
//echo 'Start: '.date("Y-m-d H:i:s").'</br></br>';
$arrExport = array();
$exp_sql="select SQL_CALC_FOUND_ROWS id,digital_record_id,file_id,spend,impressions,company_id,advertiser_name,advertiser_domain,monitored_page,campaign_landing_page,creative_wrapper,creation_date from cscan_digital_processed_records limit 10";
$exp_rs = $DRW->query($exp_sql, $DRW_read);
if (!empty($exp_rs)) {
    $arrExport['data'][] = array("Company", "Media Channel", "Date", "Country","State","City",
        "Headline", "Campaign Landing Page", "Creative", "Publisher", "Digital Source", "Spend","Impressions");
    while ($exp_row = $DRW->fetch_assoc($exp_rs)) {
            $id=$exp_row['id'];
            $process_record_id=$exp_row['digital_record_id'];
            $advertiser_domain=$exp_row['advertiser_domain'];
            $spend=$exp_row['spend'];
            $impressions=$exp_row['impressions'];
            $creation_date1=$exp_row['creation_date'];
            if($creation_date1!='0000-00-00' && $creation_date1!=''){
                $creation_date= date('Y-m-d',strtotime($creation_date1)); 

            }
            $searchVal= array("https://biscience.s3.amazonaws.com", "http://biscience.s3.amazonaws.com");
            $creative_wrapper=str_replace($searchVal,"https://files2.competiscan.com",$exp_row['creative_wrapper']);
            $campaign_landing_page=$exp_row['campaign_landing_page'];
            $sql_company = "select companyName from cscan_company Where companyID='".$exp_row['company_id']."'";
            $result_comp = $DRW->query($sql_company, $DRW_read); 
            $row_company = $DRW->fetch_assoc($result_comp);
            $company=$row_company['companyName'];
             //Media channel
            $sql_mchannel = "select mchannel_id from cscan_digital_processed_mchannel Where processed_record_id='".$id."'";
            $result_mchannel = $DRW->query($sql_mchannel, $DRW_read); 
            $mchannel_name=array();
            while($row_mchannel = $DRW->fetch_assoc($result_mchannel)){
                $mchannel_name[]=mediaChannelName($row_mchannel['mchannel_id']);
            }
            if(!empty($mchannel_name)){
            $media_channel=implode("; ",$mchannel_name);
             // echo $media_channel;
            }
            //location
            $sql_location = "select location,location_state_code from cscan_digital_processed_location Where processed_record_id='".$id."'";
            $result_location = $DRW->query($sql_location, $DRW_read); 
            $city_data=array();
            $state_data=array();
            $country_data=array();
            while($row_location = $DRW->fetch_assoc($result_location)){
                $location_name=explode(",",$row_location['location']);
                $city_name=trim($location_name[0]);
                $state_code=trim($row_location['location_state_code']);
                if($city_name!='' AND $state_code!=''){
                    if($city_name=='United States'||$city_name=='Canada'){
                        $city_data[]=$city_name;
                        $state_data[]=$city_name;
                        $country_data[]=$city_name;
                    }else{
                        $sql_q = "select DISTINCT city,state_province,country from cscan_digital_city_state where city='".$city_name."' AND state_code='".$state_code."'";
                        $res_query = $DRW->query($sql_q, $DRW_read);
                        $row_loc_data = $DRW->fetch_assoc($res_query);
                        $city_data[]=$row_loc_data['city'];
                        $state_data[]=$row_loc_data['state_province'];
                        $country_data[]=$row_loc_data['country'];
                    }
                
                }

            }
            if(!empty($city_data)){
            $city_name=implode("; ",array_unique($city_data));

            }
            if(!empty($state_data)){
            $state_name=implode("; ",array_unique($state_data));

            }
            if(!empty($country_data)){
                //print_r($country_data);
              $country_name = implode("; ",array_unique($country_data)); 
              $country_name=str_replace(';', '', $country_name);
            }

            //publisher
            $sql_publisher = "select publisher from cscan_digital_processed_publisher Where processed_record_id='".$id."'";
            $result_publisher = $DRW->query($sql_publisher, $DRW_read); 
            $publisher_name=array();
            while($row_publisher = $DRW->fetch_assoc($result_publisher)){
                $publisher_name[]=$row_publisher['publisher'];
            }
            if(!empty($publisher_name)){
            $publisher=implode("; ",$publisher_name);

            }
             //title
            $sql_title = "select compaign_title from cscan_digital_processed_title Where processed_record_id='".$id."'";
            $result_title = $DRW->query($sql_title, $DRW_read); 
            $title_array=array();
            while($row_title = $DRW->fetch_assoc($result_title)){
                $title_array[]=$row_title['compaign_title'];
            }
            if(!empty($title_array)){
            $title=implode("; ",$title_array);

            }

            //digital source
            $sql_digital_source = "select digital_source from cscan_digital_processed_source Where processed_record_id='".$id."'";
            $result_digital_source = $DRW->query($sql_digital_source, $DRW_read); 
            $digital_source_array=array();
            while($row_digital_source = $DRW->fetch_assoc($result_digital_source)){
                if($row_digital_source['digital_source']=='1'){
                  $digital_source_array[]='Desktop';  
                }
                if($row_digital_source['digital_source']=='2'){
                  $digital_source_array[]='Mobile';  
                }
                if($row_digital_source['digital_source']=='3'){
                  $digital_source_array[]='In App Android';  
                }
                if($row_digital_source['digital_source']=='4'){
                  $digital_source_array[]='In App Ios';  
                }
                if($row_digital_source['digital_source']=='5'){
                  $digital_source_array[]='Social';  
                }

            }
            if(!empty($digital_source_array)){
            $digital_source=implode("; ",$digital_source_array);

            }
            $arrExport['data'][] = array($company, $media_channel,$creation_date,$country_name,$state_name,$city_name,$title,
            $campaign_landing_page, $creative_wrapper, $publisher,
            $digital_source, $spend, $impressions,
        );
    }
}
echo "<pre>";
print_r($arrExport);die;
ob_get_clean();
//download_send_headers("digital_dashboard_report_" . date("Y-m-d") . ".csv");
echo array2csv($arrExport); 
echo "Done Process";exit;
function array2csv(array $array) {
    if (count($array) == 0) {
        return null;
    }
    //ob_start();
    $df = fopen("tmpPDF/digital_dashboard_report_" .date("Y-m-d").".csv" , 'w');
    foreach ($array['data'] as $row) {
        fputcsv($df, $row);
    }
    fclose($df);
    return ob_get_clean();
}
?>