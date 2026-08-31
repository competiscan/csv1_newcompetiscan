<?php
require_once('includes/globalSession.php');
require_once ("includes/dbcon.php");
require_once('includes/rpv-dashboard-function.php');

// echo "<pre>";
// print_r($_REQUEST['screenHeight']);
// echo "</pre>";
$user_id=$_SESSION['sess_userID'];
$sess_api_searchID=$_SESSION['sess_api_searchID'];
$datachartArray= $_REQUEST['form_chart_data'];
$chartDataArray=array();
$chartDataArray['user_id']=(int)$user_id; 
$field_name='';
//$chartDataArray['search_keyword']='';
$api_end_point='';
$screenHeight='';
$screenWidth='';
if(isset($_REQUEST['screenHeight']) && $_REQUEST['screenHeight']!=""){
    $chartDataArray['screen_height']=(int)$_REQUEST['screenHeight'];
}if(isset($_REQUEST['screenWidth']) && $_REQUEST['screenWidth']!=""){
    $chartDataArray['screen_width']=(int)$_REQUEST['screenWidth'];
}
foreach ($datachartArray as $item) {
    $key="";
    $value ="";
    $key = $item["name"];
    $value = $item["value"];
    if($key=='ssid'){
        $chartDataArray['sid']=(int)$value; 
        //$chartDataArray['sid']=122089; 
        
    }
    if($key=='page'){
        
        $page_no=(int)$value; 
    }
    
    if($key=='chart_choice'){
        if($value==1){
            $api_end_point='pie';
            //$chartDataArray['fields'][]='Pie'; 
        }
        if($value==2){
            $api_end_point='bar';
            //$chartDataArray['fields'][]='Bar'; 
        }
        if($value==3){
            $api_end_point='excel';
            //$chartDataArray['fields'][]='Excel'; 
        }
        if($value==4){
            $api_end_point='pie';
            $chartDataArray['ppt']=1; 
        }
        if($value==5){
            $api_end_point='bar';
            $chartDataArray['ppt']=1; 
        }
    }
    if($key=='graph_choice'){
        $chartDataArray['fields']=array(
           "secondCompany",
            "credit_vision_range",
            "vantage_range",
            "fico_range",
            "affinityAssociation",
            "affinityAssociationName",
            "affinityAssociationCategory",
            "entryID",
            "company",
            "age",
            "riders",
            "AnnualFee_178",
            "Tier1AnnualFee_178",
            "Tier2AnnualFee_178",
            "mChannelID",
            "delmethid",
            "mTypeID",
            "added_to_database",
            "state",
            "incentive_signon",
            "BankingRewardsProgramEmphasis_87",
            "BankingRewardsProgram_87",
            "PurchaseIntroductoryAPR_178",
            "BalanceTransferIntroductoryAPR_178",
            "PZM_CODE",
            "PZM_FLAG",
            "is_prescreen",
            "MLApplicationType_6",
            "IntroductoryAPR_6",
            "ApplicationType_178",
            "ppdate",
            "income",
            "mPanelID",
            "sectorID",
            "categoryID",
            "productName",
            "RewardsProgram",
            "RewardsProgramEmphasis",
            "subCategoryID",
            "ValueScore_for_Household",
            "ppmv",
            "mailpieces",
            "realtime_mailvolume",
            "ppeve",
            "mailspend",
            "spend_impression",
            "PrimarysubCategoryID",
            "AffinityCategoryID",
        );
        if($value==25){
            $chartDataArray['data_field']='Affinity/Association'; 
            //$chartDataArray['fields'][]='affinityAssociation';
        }
        if($value==18){
            $chartDataArray['data_field']='Affinity Category'; 
            //$chartDataArray['fields'][]='AffinityCategoryID'; 
        }
        if($value==10){
            $chartDataArray['data_field']='Age'; 
            //$chartDataArray['fields'][]='age';
        }
        if($value==19){
            $chartDataArray['data_field']='Annual Fee'; 
            //$chartDataArray['fields'][]='AnnualFeeDetail';
        }
        if($value==20){
            $chartDataArray['data_field']='Application Type'; 
           // $chartDataArray['fields'][]='MLApplicationType';
        }
        if($value==8){
            $chartDataArray['data_field']='Audience'; 
            //$chartDataArray['fields'][]='mPanelID';
        }
        if($value==2){
            $chartDataArray['data_field']='Category'; 
            //$chartDataArray['fields'][]='categoryID';
        }
        if($value==9){
            $chartDataArray['data_field']='Communications Type'; 
            //$chartDataArray['fields'][]='agentCommunicationID';
        }
        if($value==1){
            //$chartDataArray['fields'][]='company'; 
            //$chartDataArray['fields'][]='secondCompany'; 
            $chartDataArray['data_field']='Company'; 
        }
        if($value==12){
            $chartDataArray['data_field']='Income'; 
            //$chartDataArray['fields'][]='income';
        }
        if($value==15){
            $chartDataArray['data_field']='Introductory Pricing'; 
            //$chartDataArray['fields'][]='IntroductoryAPR';
        }
        if($value==13){
            $chartDataArray['data_field']='Mailing Type'; 
            //$chartDataArray['fields'][]='mTypeID';
        }
        if($value==3){
            $chartDataArray['data_field']='Media Channel'; 
            //$chartDataArray['fields'][]='mPanelID'; 
        }
        if($value==14){
            $chartDataArray['data_field']='Month'; 
           // $chartDataArray['fields'][]='ppdate';
        }
        if($value==26){
            $chartDataArray['data_field']='Pre-Screen/Opt-Out'; 
            //$chartDataArray['fields'][]='is_prescreen';
        }
        if($value==30){
            $chartDataArray['data_field']='PRIZM'; 
            //$chartDataArray['fields'][]='PZM_CODE';
        }
        if($value==28){
            $chartDataArray['data_field']='Product'; 
            //$chartDataArray['fields'][]='productName';
        }
        if($value==27){
            $chartDataArray['data_field']='Rewards Program'; 
            //$chartDataArray['fields'][]='RewardsProgram';
        }
        if($value==17){
            $chartDataArray['data_field']='Rewards Program Emphasis'; 
            //$chartDataArray['fields'][]='RewardsProgramEmphasis';
        }
        if($value==29){
            $chartDataArray['data_field']='Riders'; 
            //$chartDataArray['fields'][]='riders';
        }
        if($value==4){
            $chartDataArray['data_field']='Sector'; 
            //$chartDataArray['fields'][]='sectorID';
        }
        if($value==16){
            $chartDataArray['data_field']='Sign-on Incentive'; 
            //$chartDataArray['fields'][]='incentive';
        }
        if($value==6){
            $chartDataArray['data_field']='State/Province'; 
            //$chartDataArray['fields'][]='state';
        }
        if($value==7){
            $chartDataArray['data_field']='Sub-Category'; 
            //$chartDataArray['fields'][]='subCategoryID';
        }
        if($value==24){
            $chartDataArray['data_field']='Sub-Category - Primary'; 
            //$chartDataArray['fields'][]='subCategoryID';
        }
        if($value==31){
            $chartDataArray['data_field']='ValueScore'; 
            //$chartDataArray['fields'][]='ValueScore_for_Household';
        }
        if($value==32){
            $chartDataArray['data_field']='FICO SCORE Range'; 
            //$chartDataArray['fields'][]='fico_range';
        }
        if($value==33){
            $chartDataArray['data_field']='CreditVision Range'; 
            //$chartDataArray['fields'][]='credit_vision_range';
        }
        if($value==34){
            $chartDataArray['data_field']='VantageScore Range'; 
            //$chartDataArray['fields'][]='vantage_range';
        }
    }
    if($key=='top_comp'){
       $chartDataArray['top_value']=(int)$value; 
    }
    if($key=='date_choice'){
        if($value==3){
            $chartDataArray['trend']='year-month'; 
        } if($value==4){
            $chartDataArray['trend']='quarter'; 
        } if($value==2){
            $chartDataArray['trend']='year'; 
        }
    }
    if($key=='total_choice'){
        if($value==1){
            $chartDataArray['unit']='Percent Entry ID'; 
            //$chartDataArray['fields'][]='entryID';
            //$chartDataArray['fields'][]='added_to_database';
        }
        if($value==2){
            $chartDataArray['unit']='Total Entry ID'; 
            //$chartDataArray['fields'][]='entryID';
            //$chartDataArray['fields'][]='added_to_database';
        }
        if($value==8){
            $chartDataArray['unit']='Percent Mail Pieces'; 
            //$chartDataArray['fields'][]='mailpieces';
            //$chartDataArray['fields'][]='added_to_database';
            
        }
        if($value==4){
            $chartDataArray['unit']='Total Mail Pieces'; 
            //$chartDataArray['fields'][]='mailpieces';
            //$chartDataArray['fields'][]='added_to_database';
        }
        if($value==9){
            $chartDataArray['unit']='Percent Estimated Mail Volume'; 
            //$chartDataArray['fields'][]='ppmv';
            //$chartDataArray['fields'][]='added_to_database';
        }
        if($value==5){
            $chartDataArray['unit']='Total Estimated Mail Volume'; 
            //$chartDataArray['fields'][]='ppmv';
            //$chartDataArray['fields'][]='added_to_database';
        }
        if($value==17){
            $chartDataArray['unit']='Percent Real Time Mail Volume'; 
            //$chartDataArray['fields'][]='realtime_mailvolume';
            //$chartDataArray['fields'][]='added_to_database';
        }
        if($value==18){
            $chartDataArray['unit']='Total Real Time Mail Volume'; 
            //$chartDataArray['fields'][]='realtime_mailvolume';
            //$chartDataArray['fields'][]='added_to_database';
        }
        if($value==10){
            $chartDataArray['unit']='Total Email Pieces'; 
            //$chartDataArray['fields'][]='ppeve';
            //$chartDataArray['fields'][]='added_to_database';
        }
        if($value==15){
            $chartDataArray['unit']='Percent Email Volume Estimates'; 
            //$chartDataArray['fields'][]='ppeve';
            //$chartDataArray['fields'][]='added_to_database';
        }
        if($value==16){
            $chartDataArray['unit']='Total Email Volume Estimates'; 
            //$chartDataArray['fields'][]='ppeve';
            //$chartDataArray['fields'][]='added_to_database';
        }
        if($value==13){
            $chartDataArray['unit']='Percent Estimated Digital Spend';
            //$chartDataArray['fields'][]='mailspend'; 
            //$chartDataArray['fields'][]='added_to_database';
        }if($value==11){
            $chartDataArray['unit']='Total Estimated Digital Spend'; 
            //$chartDataArray['fields'][]='mailspend';
            //$chartDataArray['fields'][]='added_to_database';
        }
        if($value==14){
            $chartDataArray['unit']='Percent Estimated Digital Impressions'; 
            //$chartDataArray['fields'][]='spend_impression';
            //$chartDataArray['fields'][]='added_to_database';
        }
        if($value==12){
            $chartDataArray['unit']='Total Estimated Digital Impressions'; 
            //$chartDataArray['fields'][]='mailvolume';
            //$chartDataArray['fields'][]='added_to_database';
        }
    }
    if($key=='title_choice'){
        if($value!=""){
            $chartDataArray['title'][]=$value; 
        }
    }
    /*if($key=='file_choice' AND ($value==1 || $value==3)){
        if($sess_api_searchID>0) {
            list($displayKeywords) = getKeywords($sess_api_searchID);
            $displayKeywords = preg_replace('/(.)(<strong>)/', '$1| $2', trim($displayKeywords));
            $searchtitle = html_entity_decode(strip_tags($displayKeywords));
            $myexcelDataArray['search_keyword']=$searchtitle;
            $myexcelDataArray['file_type']='xlsx';
            unset($displayKeywords);
        }
    }*/
}
//  echo "<pre>";
//  print_r($chartDataArray);
//  echo "</pre>"; die;
 //echo json_encode($chartDataArray);

if($_REQUEST['action']=='display_chart'){
    $API_CHART_URL = APIURL_CHART_PROD."plot-".$api_end_point;
    $posted_jsondata=$chartDataArray; 
    $posted_jsondata=json_encode($chartDataArray);
    if(!empty($posted_jsondata)){
    $ch_chart = curl_init($API_CHART_URL); 
    curl_setopt($ch_chart, CURLOPT_POST, 1);
    curl_setopt($ch_chart, CURLOPT_POSTFIELDS, $posted_jsondata);
    curl_setopt($ch_chart, CURLOPT_RETURNTRANSFER, 1); 
    curl_setopt($ch_chart, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    $result_chart = curl_exec($ch_chart);
    echo $result_chart;exit;
    //$chart_url = 'http://example.com/chart?data=' . urlencode(json_encode($result_chart));
    //echo json_encode(['chart_url' => $chart_url]); die;
    /*if(!empty($result_chart)){
            $data=json_decode($result_download);
            $filelink=$data->filelink;
            echo trim($filelink);exit;

    }*/
    }else{
        echo json_last_error_msg();exit;
    }
}
?>