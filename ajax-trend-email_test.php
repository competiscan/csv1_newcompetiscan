<?php
require_once('includes/globalSession.php');
require_once 'includes/functions.php';
require_once('includes/rpv-dashboard-function.php');
//echo "dsfhshfshfhshfhs";
$searchQuery='';
$savePostdata=array();
if(isset($_REQUEST['h_ocr_search'])  && $_REQUEST['action']=='save_search') {
	$h_ocr_search = $_REQUEST['h_ocr_search'];
    $savePostdata['searchKey']=$h_ocr_search;    
}else{
	$h_ocr_search = '';
    $savePostdata['searchKey']=$h_ocr_search;
}
if(isset($_REQUEST['h_search_type']) && $_REQUEST['action']=='save_search') {
	$h_search_type = $_REQUEST['h_search_type'];
    $savePostdata['searchType']=$h_search_type;
}
else {
	$h_search_type = '';
    $savePostdata['searchType']=$h_search_type;
}

if(isset($_REQUEST['h_sector']) and $_REQUEST['action']=='save_search') {
       
	$h_sector = $_REQUEST['h_sector'];
    $savePostdata['sectorID']=$h_sector;
}
else {
	$h_sector = '';
    $savePostdata['sectorID']=$h_sector;
}
if(isset($_REQUEST['h_fdt']) and $_REQUEST['h_fdt']!='' and $_REQUEST['action']=='save_search') {
	$h_fdt = $_REQUEST['h_fdt'];
    $savePostdata['from_date']=$h_fdt;
}
// else {
// 	$h_fdt = '';
//     $savePostdata['from_date']=$h_fdt;
// }
if(isset($_REQUEST['h_tdt']) and $_REQUEST['h_tdt']!='' and $_REQUEST['action']=='save_search') {
	$h_tdt = $_REQUEST['h_tdt'];
    $savePostdata['to_date']=$h_tdt;
}
// else {
// 	$h_tdt = '';
//     $savePostdata['to_date']=$h_fdt;
// }
if(isset($_REQUEST['h_country']) AND $_REQUEST['h_country']!='' AND $_REQUEST['action']=='save_search') {
	$h_country = $_REQUEST['h_country'];
    $savePostdata['country']=$h_country;
}
// else {
// 	$h_country = '0';
//     $savePostdata['country']=$h_country;
// }
if(isset($_SESSION['sess_userID'])){
    $sess_userID=$_SESSION['sess_userID'];
    $savePostdata['userID']=$sess_userID;
} else{
   $sess_userID=''; 
   $savePostdata['userID']=$sess_userID;
}
if(isset($_SESSION['sess_userType'])){
    $sess_userType=$_SESSION['sess_userType'];
} else{
   $sess_userType=''; 
}
if($_REQUEST['action']=='save_search'){
    if(!empty($savePostdata)){
        $postSaveData=json_encode($savePostdata);
        $ApiTrendSaveSearch=TREND_REPORT_API_UAT_URL.'save_trend_search';
        $GetTrendSaveData = callAPI('POST', $ApiTrendSaveSearch, $postSaveData);
        $ResTrendSaveData = json_decode($GetTrendSaveData, true);
        echo $ResTrendSaveData['message']; exit;
        // echo "<pre>";
        // print_r($ResTrendSaveData[]);
        // echo "</pre>"; die;
        // if(!empty($ResTrendSaveData && isset($ResTrendData['data']))){
        // }
    }
}
 
    if(isset($_REQUEST['trendsearch_id']) && $_REQUEST['action']=='search_alert_delete') {
	$trendsearch_id = $_REQUEST['trendsearch_id'];
        if(!empty($trendsearch_id)){
            //echo $postSaveData=json_encode($savePostdata);
            $ApiTrendDeleteSearch=TREND_REPORT_API_UAT_URL.'manage_trend_email?ID='.$trendsearch_id;
            $GetTrendDeleteData = callAPI('DELETE', $ApiTrendDeleteSearch, false);
            $ResTrendDeleteData = json_decode($GetTrendDeleteData, true);
            // echo "<pre>";
            // print_r($ResTrendDeleteData);
            // echo "</pre>"; die;
            // if(!empty($ResTrendSaveData && isset($ResTrendData['data']))){
            // }
            if($ResTrendDeleteData){
                echo "1"; exit;
            }else{
                echo "0"; exit;
            }
    
        }
    }

    $savePostdataAlert=array();
    if(isset($_REQUEST['action']) && $_REQUEST['action']=='save_industry_alert') {
        if(isset($_REQUEST['check_category'])){
            $sectData=$_REQUEST['check_category'];
            // echo "<pre>";
            // print_r($sectData);
            // echo "</pre>";
            if(!empty($sectData)){
                if (isset($sectData['sectorID']) && is_array($sectData['sectorID'])) {
                    $savePostdataAlert['sectorID'] = array_map('intval', $sectData['sectorID']);
                }
                if (isset($sectData['subCategoryID']) && is_array($sectData['subCategoryID'])) {
                    $savePostdataAlert['subCategoryID'] = array_map('intval', $sectData['subCategoryID']);
                }
                if (isset($sectData['categoryID']) && is_array($sectData['categoryID'])) {
                    $savePostdataAlert['categoryID'] = array_map('intval', $sectData['categoryID']);
                }
            }
          }
        if(isset($_SESSION['sess_userID'])){
            $sess_userID=$_SESSION['sess_userID'];
            $savePostdataAlert['userID']=$sess_userID;
        } else{
           $sess_userID=''; 
           $savePostdataAlert['userID']=$sess_userID;
        }
        $postSaveAlertData=json_encode($savePostdataAlert);
        $ApiTrendSaveSearch=TREND_REPORT_API_UAT_URL.'save_trend_alert';
        $GetTrendSaveAlertData = callAPI('POST', $ApiTrendSaveSearch, $postSaveAlertData);
        $ResTrendSaveAlertData = json_decode($GetTrendSaveAlertData, true);
        echo $ResTrendSaveAlertData['message']; exit;
        // echo "<pre>";
        // print_r($ResTrendSaveAlertData);
        // echo "</pre>"; die;
    }
    
?>