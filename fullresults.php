<?php $start_time = microtime(true);
require_once('includes/globalSession.php');
require_once('includes/checklogin.php');
$currenttime=time();
$filename = 'Competiscan_Export_'.$currenttime.'_'.date('Y-m-d').'.csv';
/*require_once 'product_doc_tracker.php';
track_user();
@ob_clean();
$client_ip_address=get_client_ip_address(); 
//############# Start User visits Count ##################/
$today_date = date('Y-m-d');
//print_r($_SESSION);die;
if(isset($_SESSION['sess_userID'])){
 $loggin_user_id =  $_SESSION['sess_userID'];
  $session_user_id  ='';
  $andQuery_vist = " and loggin_user_id ='".$loggin_user_id."'";
}else{
    $loggin_user_id=''; 
    $session_user_id  = session_id();
    $andQuery_vist = " AND client_ip_address='".$client_ip_address."'";
} 
//echo $session_user_id; die;
$sql_visit = "SELECT id,session_user_id,loggin_user_id,visit_count FROM cscan_visitor_counter where  DATE_FORMAT(visit_date,'%Y-%m-%d') = '".$today_date ."' $andQuery_vist";
$result_visit = $DRW->query($sql_visit, $DRW_read);
$num_row=$DRW->num_rows($result_visit);
if($num_row > 0){
} else{
    $sql = "INSERT INTO cscan_visitor_counter SET session_user_id='".$session_user_id."', loggin_user_id='" . $loggin_user_id. "',visit_count='1',client_ip_address='".$client_ip_address."'";
    $DRW->query($sql, $DRW_main);    
}
if($loggin_user_id!=''){
        $sql_del_guest = "DELETE FROM cscan_visitor_counter where  DATE_FORMAT(visit_date,'%Y-%m-%d') = '".$today_date ."' and loggin_user_id<1 AND client_ip_address='".$client_ip_address."'";
        $result_visit = $DRW->query($sql_del_guest, $DRW_main);
}
function get_client_ip_address() {
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
       $ipaddress = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';
    return trim($ipaddress);
}
*/
//############# End User visits Count ##################/
/*######## Start for Page permission ########*/ 
if(!defined('ENV')){
    define('ENV',getenv('SERVER_NAME'));
   }
//if(ENV == 'localhost' || ENV == 'demo.competiscan.com'){
    if(ENV == 'localhost'){
        $siteUrl='http://localhost/competiscan.com/';
    }elseif(ENV == 'demo.competiscan.com'){
        $siteUrl='http://demo.competiscan.com/';
    }else{
        $siteUrl='https://www.competiscan.com/';
    } 
    $page_permission = getPagePermission();
    if(!empty($_SESSION['sess_search_page_permission'])){
        $page_permission=$_SESSION['sess_search_page_permission'];
    }
    $redirect_page='';
    if(!empty($page_permission)){
        if(!in_array('power_search',$page_permission) AND in_array('trend_reports',$page_permission)){
            $redirect_page=$siteUrl.'trend_reports.php';

        }else if(!in_array('power_search',$page_permission) AND !in_array('trend_reports',$page_permission) AND in_array('retrieval_services',$page_permission)){
            $redirect_page=$siteUrl.'productPickup.php';
        }
        if(!in_array('power_search',$page_permission) AND $redirect_page!=''){
           header("Location: $redirect_page");
            die; 
        }           
    }else{
        if(!empty($_SESSION['sess_dashboard'])) {
            $redirect_page=$siteUrl.'dashboard.php';
        }else{
            $redirect_page=$siteUrl.'quickHelp.php';
        } 
        header("Location: $redirect_page");
        die;
    }

//}    
 /*######## End for Page permission ########*/
$companySearch='';
$javascript = '';
$basket_name = '';
$pdf_key = '';
$pdfsearchKeyword = '';
$dorelev = false;
$doexpans = false;
//$pagelimit = 50;
$bid = -1;
$ssid = 0;
$page = 1;
$sort = '-3';
$pagesort='';
if(isset($_GET['sort'])) {
        $sort = (int)$_GET['sort'];
}
$save_msg = '';
$saved = '';
$name = array();
$basket_action_text = '';
$sectorExactFlag='';
$sectorOnlyFlag='';
$selectVals = array();
//$postdata['target_market_id']='';
/*if(isset($_REQUEST['myExcel_download']) AND $_REQUEST['myExcel_download']=='Express Download'){
 //echo "<pre>";
//print_r($_REQUEST);
 $API_DOWNLOADURL =  API_DOWNLOADURL;
 //$API_DOWNLOADURL = "https://api7.competiscan.com/elasticsearch-service/v1/search/download";
 $posted_jsondata=$_REQUEST['post_json']; 
 if(!empty($posted_jsondata)){
    $ch_download = curl_init($API_DOWNLOADURL); 
    curl_setopt($ch_download, CURLOPT_POST, 1);
    curl_setopt($ch_download, CURLOPT_POSTFIELDS, $posted_jsondata);
    curl_setopt($ch_download, CURLOPT_RETURNTRANSFER, 1); 
    curl_setopt($ch_download, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    $result_download = curl_exec($ch_download);
    $currenttime=time();
    $filename = 'Competiscan_Export_'.$currenttime.'_'.date('Y-m-d').'.csv';
    $fp = fopen('php://output', 'w');
    header('Content-type: application/csv');
    header('Content-Disposition: attachment; filename=' . $filename);
    fwrite($fp, $result_download);
    #fputcsv($fp, $result_download);
    fclose($fp);die;
    }else{
        echo json_last_error_msg();die;
    } 
}*/
$currentpage_url='http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$API_URL =API_URL;
//$API_URL = "https://api7.competiscan.com/elasticsearch-service/v1/search/";

//echo "<pre>";
//print_r($_REQUEST); die;

if(isset($_GET['updated'])) {
    //sleep(5);
}

if(isset($_REQUEST['bid'])) {
        $bid = (int)$_REQUEST['bid'];
}
if(isset($_REQUEST['ssid'])) {
        $ssid = (int)$_REQUEST['ssid'];
}
if(isset($_REQUEST['sid'])) {
        $sid = $_REQUEST['sid'];
}else{
    $sid='';
}

if(isset($_REQUEST['page_type'])) {
        $_REQUEST['page_type'];
        $page_type = $_REQUEST['page_type'];
}else{
    //echo "else";
    $page_type=1;
}
if(isset($_REQUEST['dct'])) {
        $_REQUEST['dct'];
        $dct = $_REQUEST['dct'];
}else{
    //echo "else";
    $dct="fwd";
}
if($ssid==0 && $bid<0) {
        ob_end_clean();
        header("Location: fullsearch.php");
        exit;
}
$gets = "&amp;ssid=".$ssid;
if($bid>=0) {
        $gets .= '&amp;bid='.$bid;
}
if(isset($_GET['sort'])) {
        $sort = (int)$_GET['sort'];
        
}
if (isset($_GET['page_no']) && $_GET['page_no']!="") {
    $page_no = $_GET['page_no'];
    } else {
    $page_no = 1;
}
$displayKeywords = '';
$search_num_of_rows=0;
$previous_page = $page_no - 1;
$next_page = $page_no + 1;
if(isset($_GET['sname'])) {
        $save_msg = "Your search has been saved as {$_GET['sname']}";
}
else {
    if(isset($_POST['sendsave']) && trim($_POST['searchName']) != '') {
            $count_save_sql = "SELECT COUNT(*) FROM cscan_search where userID =".$_SESSION['sess_userID']." AND saved=1";
            $rs = $DRW->query($count_save_sql,$DRW_read);
            $data = $DRW->fetch_row($rs);
            $numrow = (int) $data[0];

            if($numrow < 100 || $_SESSION['sess_plevel']==1 || $_SESSION['sess_plevel']==2) {
                    $searchName = trim($_POST['searchName']);
                    $last_search_sql = "UPDATE cscan_search SET saved=1,searchName='".$DRW->real_escape_string($searchName)."' WHERE ID='$ssid' AND userID='".$_SESSION['sess_userID']."'";
                    $DRW->query($last_search_sql,$DRW_main);
                    ########################## START ELASTIC SAVE SEARCH#####################
                    if($searchName!=''){
                        $chcsv2_save = curl_init(ELASTIC_SAVE_SEARCH_NAME_PROD);
                        $postdataSave['searchName']=$searchName;
                        $postdataSave['userID']=$_SESSION['sess_userID'];
                        //$postdataSave['ID']=111684;
                        $postdataSave['ID']=$ssid;
                        $posteddataSaveSearch = json_encode($postdataSave);
                        //$posteddataSaveSearch;
                        curl_setopt($chcsv2_save, CURLOPT_SSL_VERIFYPEER, FALSE);
                        curl_setopt($chcsv2_save, CURLOPT_FOLLOWLOCATION, TRUE);
                        curl_setopt($chcsv2_save, CURLOPT_POST, 1);
                        curl_setopt($chcsv2_save, CURLOPT_POSTFIELDS, $posteddataSaveSearch);
                        curl_setopt($chcsv2_save, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($chcsv2_save, CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
                        curl_setopt($chcsv2_save, CURLOPT_TIMEOUT, 80);
                        $response_searchName = curl_exec($chcsv2_save);
                        //print_r($response_searchName);
                        if(curl_error($chcsv2_save)){
                                echo 'Request Error:' . curl_error($chcsv2_save);
                        }else{
                                $jsonResponseAPIDataSaveSearch = json_decode($response_searchName, JSON_UNESCAPED_UNICODE);
                        }
                        curl_close($chcsv2_save);
                            

                    }
                    ########################## END ELASTIC SAVE SEARCH#####################

                    ob_end_clean();
                    header("Location: {$_SERVER['PHP_SELF']}?ssid=$ssid&sname=".urlencode($searchName));
                    exit;
            }
            else {
                    $save_msg = "You can save only one-hundred (100) searches";
            }
    }
}
#############################START BASKET SECTION######################
if(isset($_POST['basket_action'])){
    $basket_action = (int)$_POST['basket_action'];
    //echo "<pre>";
    //print_r($_POST); 
    //$basket_action;
    $basket = array();
    if($basket_action>0){
            if($basket_action==7 || $basket_action==8 || $basket_action==9 || $basket_action==10){//Copy To/Add page/all || Move To page/all
                if($bid<0){
                            $chcsv2 = curl_init(API_URL_EMAIL_ALERT); 
                            if($basket_action==7){
                                $chcsv2 = curl_init(API_URL);
                            }
                            $posteddata = $_POST['save_json_query'];
                            //echo $posteddata; 
                            curl_setopt($chcsv2, CURLOPT_SSL_VERIFYPEER, FALSE);
                            curl_setopt($chcsv2, CURLOPT_FOLLOWLOCATION, TRUE);
                            curl_setopt($chcsv2, CURLOPT_POST, 1);
                            curl_setopt($chcsv2, CURLOPT_POSTFIELDS, $posteddata);
                            curl_setopt($chcsv2, CURLOPT_RETURNTRANSFER, 1);
                            curl_setopt($chcsv2, CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
                            curl_setopt($chcsv2, CURLOPT_TIMEOUT, 80);
                            $response_api = curl_exec($chcsv2);
                            if(curl_error($chcsv2)){
                                    echo 'Request Error:' . curl_error($chcsv2);
                            }else{
                                    //$curl_status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                                    $jsonResponseAPIData = json_decode($response_api, JSON_UNESCAPED_UNICODE);
                                    //echo $curl_status_code;
                                    //echo ' Total time in ('.number_format((microtime(true) - $start_time),3).' Seconds)';
                                    //echo "<pre>";
                                    //print_r($jsonResponseAPIData); 



                            }
                            curl_close($chcsv2);
                            if($basket_action==7){
                                foreach($jsonResponseAPIData['results'] as $resultProdData){
                                    $productID=$resultProdData['product_id'];
                                    $basket[]=$productID;
                                }
                                
                            }else{
                                foreach($jsonResponseAPIData['product_ids'] as $resultProdId){
                                    $productID=$resultProdId;
                                    $basket[]=$productID;
                                }
                            }
                        //}
                   // echo "<pre>";
                    //print_r($basket);
                    //$search_num_of_rows =$jsonResponseAPIData['pid_count'];
                    $search_num_of_rows = count($basket);
                    }
                    else{
                        $sqlbasket = "SELECT productID FROM cscan_product_basket WHERE basket_id=$bid AND userID={$_SESSION['sess_userID']}";
                        $query_basket = $DRW->query($sqlbasket,$DRW_read);
                        while($resultBasketData = $DRW->fetch_array($query_basket)){
                                $productID = $resultBasketData['productID'];
                                $basket[] = $productID;
                        }
                        
                    }
            }
            elseif(isset($_POST['basket']) && count($_POST['basket'])>0) {
                    if(isset($_SESSION['selected_productID'])){
                            $basket = $_SESSION['selected_productID'];
                            foreach($_POST['basket'] as $b){
                                    if(!in_array($b,$basket)){
                                            $basket[] = $b;
                                    }
                            }
                    }
                    else{
                            $basket = $_POST['basket'];
                    }
            }
            elseif(isset($_SESSION['selected_productID'])){
                    $basket = $_SESSION['selected_productID'];
            }

            if(isset($_POST['basketid'])) $basketid = (int)$_POST['basketid'];
            else $basketid = 0;

            if($basket_action==1){ //Change Basket Name
                    $sql = "UPDATE cscan_basket SET basket_name='".$DRW->real_escape_string(trim($_POST['curr_basket_name']))."' WHERE basket_id=$bid AND userID={$_SESSION['sess_userID']}";
                    $DRW->query($sql,$DRW_main);
                    $bidredir = $bid;
            }
            elseif($basket_action==6){ //Save Annotations
                    $sqlb = "SELECT productID FROM cscan_product_basket WHERE basket_id=$bid AND userID={$_SESSION['sess_userID']}";
                    $rsb = $DRW->query($sqlb,$DRW_read);
                    while($datab = $DRW->fetch_row($rsb)){
                            if(isset($_POST["basket_note_$datab[0]"])) {
                                    $sql = "UPDATE cscan_product_basket SET basket_note='".$DRW->real_escape_string($_POST["basket_note_$datab[0]"])."' WHERE basket_id=$bid AND userID={$_SESSION['sess_userID']} AND productID=$datab[0]";
                                    $DRW->query($sql,$DRW_main);
                            }
                    }
                    $bidredir = $bid;
            }
            elseif($basket_action==5){//Remove Selected
                    foreach($basket as $pid){
                            $sql = "DELETE FROM cscan_product_basket WHERE basket_id=$bid AND userID={$_SESSION['sess_userID']} AND productID=$pid";
                            $DRW->query($sql,$DRW_main);
                    }
                    $bidredir = $bid;
            }
            elseif($basket_action==3 && $basketid!=0){ //Delete Basket
                    $sql = "DELETE FROM cscan_product_basket WHERE basket_id=$basketid AND userID={$_SESSION['sess_userID']}";
                    $DRW->query($sql,$DRW_main);

                    $sql = "DELETE FROM cscan_basket WHERE basket_id=$basketid AND userID={$_SESSION['sess_userID']}";
                    $DRW->query($sql,$DRW_main);
                    $bidredir = $bid;
            }
            elseif($basket_action==2 || $basket_action==4 || $basket_action==7 || $basket_action==8 || $basket_action==9 || $basket_action==10){ //Copy Selected To/Add Selected To || Move Selected To || page || all
                    if($basketid==-2){ //new basket
                            $sql = "INSERT INTO cscan_basket (basket_name,userID,basket_created) VALUES ('".$DRW->real_escape_string(trim($_POST['basket_name']))."',{$_SESSION['sess_userID']},NOW())";
                            $DRW->query($sql,$DRW_main);
                            $basketid = (float)$DRW->insert_id($DRW_main);
                    }
                    if($bid<0){
                            $sqlb = "SELECT mPanelID,sectorID,mChannelID,categoryID,subCategoryID FROM cscan_search WHERE ID=$ssid";
                            $rsb = $DRW->query($sqlb,$DRW_read);
                            $datab = $DRW->fetch_row($rsb);
                            $b_mPanelID = (string)$datab[0];
                            $b_sectorID = (string)$datab[1];
                            $b_mChannelID = (string)$datab[2];
                            $b_categoryID = (string)$datab[3];
                            $b_subCategoryID = (string)$datab[4];
                            $b_basket_note = '';
                    }
                    foreach($basket as $pid){
                            if($bid>=0){
                                    $sqlb = "SELECT b_mPanelID,b_sectorID,b_mChannelID,b_categoryID,b_subCategoryID,basket_note FROM cscan_product_basket WHERE basket_id=$bid AND userID={$_SESSION['sess_userID']} AND productID=$pid";
                                    $rsb = $DRW->query($sqlb,$DRW_read);
                                    $datab = $DRW->fetch_row($rsb);
                                    $b_mPanelID = (string)$datab[0];
                                    $b_sectorID = (string)$datab[1];
                                    $b_mChannelID = (string)$datab[2];
                                    $b_categoryID = (string)$datab[3];
                                    $b_subCategoryID = (string)$datab[4];
                                    $b_basket_note = (string)$datab[5];
                            }
                            if(isset($_POST["basket_note_$pid"])) {
                                    $b_basket_note = $_POST["basket_note_$pid"];
                            }
                            $sql = "REPLACE INTO cscan_product_basket (basket_id,userID,productID,b_mPanelID,b_sectorID,b_mChannelID,b_categoryID,b_subCategoryID,basket_note) 
                                    VALUES ($basketid,{$_SESSION['sess_userID']},$pid,'$b_mPanelID','$b_sectorID','$b_mChannelID','$b_categoryID','$b_subCategoryID','".$DRW->real_escape_string($b_basket_note)."')";
                            $DRW->query($sql,$DRW_main);
                            if($basket_action==4 || $basket_action==9 || $basket_action==10){//move selected || page || all
                                    $sql = "DELETE FROM cscan_product_basket WHERE basket_id=$bid AND userID={$_SESSION['sess_userID']} AND productID=$pid";
                                    $DRW->query($sql,$DRW_main);
                            }
                    }
                    $bidredir = $basketid;
            }
    }
    $_SESSION['selected_productID'] = array();
    ob_end_clean();
    if($basket_action==3 && $basketid==$bid) {
            header("Location: {$_SERVER['PHP_SELF']}?ssid=$ssid");
    }
    else {
            header("Location: {$_SERVER['PHP_SELF']}?ssid=$ssid&bid=$bidredir&updated=1");
    }
    exit;
}
#############################START API IMPLEMENTATION##################
if(($bid >0 && $ssid==0) OR ($bid >0 && $ssid > 0) ){
        $sqlbasket = "SELECT productID FROM cscan_product_basket WHERE basket_id=$bid AND userID={$_SESSION['sess_userID']}";
        $resultsbasket = $DRW->query($sqlbasket,$DRW_read);
        if($DRW->num_rows($resultsbasket) > 0){
            $bktProductIDArray=array();
            while($row_basket_data = $DRW->fetch_row($resultsbasket)){
                $bktProductIDArray[]=$row_basket_data[0];
            }
        $postdata['product_id']=implode(',',$bktProductIDArray);
        $chcsv2 = curl_init($API_URL);
    if($page_type!=3 && $sid!=''){
        $postdata['current_page']=$page_no;
        $postdata['direction']=$dct;  
        $postdata['sid']="[".$sid."]";
        $postdata['page_type']=$page_type;
    }else{
        $postdata['page_type']=$page_type;
    }
    $postdata['product_status']="1";
    $chcsv2 = curl_init($API_URL);
    $postdata['sortby'] ='entry_id_sort, desc';
    if($sort!='' && $sort==1){
     $postdata['sortby'] ='product_headline_sort, desc';   
    }
    if($sort!='' && $sort==-1){
     $postdata['sortby'] ='product_headline_sort, asc';   
    }
    if($sort!='' && $sort==2){
     $postdata['sortby'] ='primary_company_name, desc';   
    }
    if($sort!='' && $sort==-2){
     $postdata['sortby'] ='primary_company_name, asc';   
    }
    if($sort!='' && $sort==3){
     $postdata['sortby'] ='entry_id_sort, asc';   
    }
    if($sort!='' && $sort==-3){
     $postdata['sortby'] ='entry_id_sort, desc';   
    }
    if($sort!='' && $sort==11){
     $postdata['sortby'] ='is_digital, desc';   
    }
    if($sort!='' && $sort==-11){
     $postdata['sortby'] ='is_digital, asc';   
    }
    if($sort!='' && $sort==12){
     $postdata['sortby'] ='is_panelist_score, desc';   
    }
    if($sort!='' && $sort==-12){
     $postdata['sortby'] ='is_panelist_score, asc';   
    }
    if($sort!='' && $sort==13){
     $postdata['sortby'] ='is_animation, desc';   
    }
    if($sort!='' && $sort==-13){
     $postdata['sortby'] ='is_animation, asc';   
    }
    $postdata['tiebreaker'] ='product_id, desc';
    if($sort!='' && $sort==11 || $sort!='' && $sort==13 || $sort!='' && $sort==-12){
        $postdata['tiebreaker'] ='entry_id_sort, desc';   
    }
    if($sort!='' && $sort==-11 || $sort!='' && $sort==-13 || $sort!='' && $sort==-12){
        $postdata['tiebreaker'] ='entry_id_sort, asc';  
    }
    $posteddata = json_encode($postdata);
    //echo $posteddata; 
    curl_setopt($chcsv2, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($chcsv2, CURLOPT_FOLLOWLOCATION, TRUE);
    curl_setopt($chcsv2, CURLOPT_POST, 1);
    curl_setopt($chcsv2, CURLOPT_POSTFIELDS, $posteddata);
    curl_setopt($chcsv2, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($chcsv2, CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
    curl_setopt($chcsv2, CURLOPT_TIMEOUT, 80);
    $response_api = curl_exec($chcsv2);
    if(curl_error($chcsv2)){
            echo 'Request Error:' . curl_error($chcsv2);
    }else{
            $jsonResponseAPIData = json_decode($response_api, JSON_UNESCAPED_UNICODE);
            //echo "<pre>";
            //print_r($jsonResponseAPIData); die;
           
            
    }
    curl_close($chcsv2);
    if(!empty($jsonResponseAPIData)){
        $search_num_of_rows =$jsonResponseAPIData['total_records']; 
    }
    ###################START IMPLEMENT SAVE SEARCH#########################
    $API_URL_SAVE_SEARCH=ELASTIC_SAVE_SEARCH_PROD;
    if($_SESSION['sess_userID']!=''){
    $postdata['userID']=$_SESSION['sess_userID'];
    }
    $posteddata_save = json_encode($postdata);
    $chcsv2_save = curl_init($API_URL_SAVE_SEARCH);
    curl_setopt($chcsv2_save, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($chcsv2_save, CURLOPT_FOLLOWLOCATION, TRUE);
    curl_setopt($chcsv2_save, CURLOPT_POST, 1);
    curl_setopt($chcsv2_save, CURLOPT_POSTFIELDS, $posteddata_save);
    curl_setopt($chcsv2_save, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($chcsv2_save, CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
    curl_setopt($chcsv2_save, CURLOPT_TIMEOUT, 80);
    $response_api_save = curl_exec($chcsv2_save);
    //print_r($response_api); die;
    if(curl_error($chcsv2_save)){
            echo 'Request Error:' . curl_error($chcsv2_save);
    }else{
        $jsonResponseAPIDataSave = json_decode($response_api_save, JSON_UNESCAPED_UNICODE);
        // echo "<pre>";
        // print_r($jsonResponseAPIDataSave); 
        //die;
        if($jsonResponseAPIDataSave['statusMsg']=='OK' && $jsonResponseAPIDataSave['statusCode']=='200'){
            $sess_api_searchID=$jsonResponseAPIDataSave['payload']['0']; 
            $_SESSION["sess_api_searchID"] = $sess_api_searchID;
        }
    }
    curl_close($chcsv2_save);   
    ###################END IMPLEMENT SAVE SEARCH########################
}
}
if($bid<0){
$search_rulesArray = array();
$search_rulesArrayf = array();
$check_search_sql = "SELECT * FROM cscan_search where userID =".$_SESSION['sess_userID']." AND ID='".(int)$_REQUEST['ssid']."'";
$resultCheck = $DRW->query($check_search_sql,$DRW_read);
if($DRW->num_rows($resultCheck) > 0){
    $dataSearchCheck = $DRW->fetch_array($resultCheck);
      //print_r($dataSearchCheck); die;
        //echo $dataSearchCheck['IntroPricing_mult'];
   if($dataSearchCheck['search_rules']!='' && $dataSearchCheck['search_rules']!='NULL'){
        $nots='';
        $search_rulesArray = explode(',',$dataSearchCheck['search_rules']);
        foreach($search_rulesArray as $sr){
            if(!empty($sr)){
                    list($f,$ao,$ex,$no) = explode(':',$sr);
                    $search_rulesArrayf[$f] = array($ao,$ex,$no);
            }
        }
        //echo "<pre>";
        //print_r($search_rulesArrayf); 
        $sectorExactFlag='';
        if($search_rulesArrayf['sectorID'][0]=='1' AND $search_rulesArrayf['sectorID'][1]=='1' AND $search_rulesArrayf['sectorID'][2]=='0'){
           $postdata['multi_match_op'] ="sector_id,exact";
          $sectorExactFlag='1'; 
         
        }
        $sectorOnlyFlag=''; 
        if($search_rulesArrayf['sectorID'][0]=='1' AND $search_rulesArrayf['sectorID'][1]=='0' AND $search_rulesArrayf['sectorID'][2]=='0'){
          $postdata['multi_match_op'] ='sector_id,only'; 
          $sectorOnlyFlag='1';
        }
        if($search_rulesArrayf['mTypeID'][0]=='0' AND $search_rulesArrayf['mTypeID'][1]=='0' AND $search_rulesArrayf['mTypeID'][2]=='1'){
            $nots='mtype_id'; 
            /*if($dataSearchCheck['mTypeID']==''){
              $nots.=',mtype_id(null)';
              $postdata['mtype_id']="1";
            }*/
        }
       if($search_rulesArrayf['agentCommunicationID'][0]=='0' AND $search_rulesArrayf['agentCommunicationID'][1]=='0' AND $search_rulesArrayf['agentCommunicationID'][2]=='1'){
              $nots.=',agent_communication_id';
              if($dataSearchCheck['agentCommunicationID']==''){
                $nots.=',agent_communication_id(null)';
                $postdata['agent_communication_id']="1";
              }

        }
        if($nots!=''){
           $postdata['nots']=trim($nots,',');
        }
       
    }
    
    if($dataSearchCheck['sectorID']!=''){
       $postdata['sector_id']=$dataSearchCheck['sectorID']; 
    }
    if($sectorOnlyFlag==1){
    //if($sectorExactFlag==1 || $sectorOnlyFlag==1){
        if($dataSearchCheck['categoryID']!=''){
            $postdata['sector_id'].=",".$dataSearchCheck['categoryID'];
        }
        if($dataSearchCheck['subCategoryID']!=''){
            $postdata['sector_id'].=",".$dataSearchCheck['subCategoryID'];
        }

        if($dataSearchCheck['scsc_primary']!='0' AND $postdata['sector_id']!=''){
            $postdata['primary_only']="sector_id";
        }   
    }else{
        $categoryID='';
        if($dataSearchCheck['categoryID']!=''){
           
            $categoryID=$dataSearchCheck['categoryID'];
        }
        $subcateID='';
        if($dataSearchCheck['subCategoryID']!=''){
            $subcateID=$dataSearchCheck['subCategoryID'];
        }
        $subtosubcateID='';
        if($dataSearchCheck['subSubCategoryID']!=''){
            $subtosubcateID=$dataSearchCheck['subSubCategoryID'];
        }
        $appendSectCategory='';
        if($categoryID!=''){
            $appendSectCategory.='cat_:'.$categoryID.";";
        }if($subcateID!=''){
            $appendSectCategory.='cat_sub:'.$subcateID.";";
        }if($subtosubcateID!=''){
            $appendSectCategory.='cat_ssub:'.$subtosubcateID.";";
        }
        if($appendSectCategory!=''){
            $postdata['categories']='field:sector_id;'.rtrim($appendSectCategory,';');
        }
        if($dataSearchCheck['scsc_primary']!='0' AND $postdata['sector_id']!=''){
            $postdata['primary_only']="sector_id";
        }
    }
    
    $searchKey=$dataSearchCheck['searchKey'];
    //$searchKey = str_replace ("'","\"",$searchKey); 
    $searchType=$dataSearchCheck['searchType'];
    $searchKey2=$dataSearchCheck['searchKey2'];
    //$searchKey2 = str_replace ("'","\"",$searchKey2);
    $search_type_and=$dataSearchCheck['search_type_and'];
    if($searchKey!='' && $searchKey!='NULL' && $searchType=='fulltext2'){
        if (strpos ($searchKey, '"') !== false || strpos ($searchKey, "'") !== false) {
            $postdata['product_headline'] =strtoupper($searchKey); 

        }else{
            $postdata['product_headline'] =strtoupper($searchKey);
            $postdata["text_match_op"]="product_headline,and";
        }
    }elseif($searchKey!=''  && $searchKey!='NULL' && $searchType=='ocr2'){
        if (strpos ($searchKey, '"') !== false || strpos ($searchKey, "'") !== false) {
            $postdata['dts_val'] = strtoupper($searchKey);
            //$postdata['product_headline'] =strtoupper($searchKey); 
         }else{
             $postdata['dts_val'] = strtoupper($searchKey);
             $postdata["text_match_op"]="dts_val,and";
         }
    }elseif(($searchKey!='' && $searchKey!='NULL' && $searchType=='ocr_fulltext2') AND ($searchKey2!='' && $searchKey2!='NULL' && $search_type_and=='1')){
       if (strpos ($searchKey, '"') !== false || strpos ($searchKey, "'") !== false || strpos ($searchKey2, '"') !== false || strpos ($searchKey2, "'") !== false) {
            $postdata['dts_val'] = strtoupper($searchKey);
            $postdata['product_headline'] =$searchKey2;
            //$postdata['product_headline'] =strtoupper($searchKey); 
         }else{
             $postdata['dts_val'] = strtoupper($searchKey);
             $postdata['product_headline'] =$searchKey2;
             $postdata["text_match_op"]="dts_val,and;product_headline,and";
         }
       
    }elseif(($searchKey!='' && $searchKey!='NULL' && $searchType=='fulltext2') OR ($searchKey2!='' && $searchKey2!='NULL' && $search_type_and=='0')){
       $postdata['dts_val'] = strtoupper($searchKey);
       $postdata['product_headline'] =strtoupper($searchKey2);
       $postdata['text_search_join']="or";
    }
    //echo addslashes($searchKey); die;
    if($dataSearchCheck['mChannelID']!=''){
       $postdata['mchanne_id']=$dataSearchCheck['mChannelID'];
    }
    if($dataSearchCheck['mPanelID']!=''){
        $postdata['mpanel_id']=$dataSearchCheck['mPanelID'];
    }
    if($dataSearchCheck['ApplicationType_mult']!=''){
        $postdata['application_type']=$dataSearchCheck['ApplicationType_mult'];
    }
    if($dataSearchCheck['mTypeID']!=''){
        $postdata['mtype_id']=$dataSearchCheck['mTypeID'];
    }
    if($dataSearchCheck['IssueTypeID_mult']!=''){
        $postdata['issue_type_id']=$dataSearchCheck['IssueTypeID_mult'];
    }
    if($dataSearchCheck['personalization']!=''){
        $postdata['personalization']=$dataSearchCheck['personalization'];
    }
    
    if($dataSearchCheck['worksiteVoluntary']!='0' AND $dataSearchCheck['worksiteVoluntary']!=''){
        $postdata['worksite_voluntary']=$dataSearchCheck['worksiteVoluntary']==2 ? "0": $dataSearchCheck['worksiteVoluntary'];
    }
    if($dataSearchCheck['siteCatID_mult']!=''){
        $postdata['sites_category_id']=$dataSearchCheck['siteCatID_mult'];
    }
    
    if($dataSearchCheck['is_incentive']!='0'){
        $postdata['is_incentive']=$dataSearchCheck['is_incentive'];
    }
    if($dataSearchCheck['electronicID_mult']!=''){
        $postdata['electronic_id']=$dataSearchCheck['electronicID_mult'];
    }
    if($dataSearchCheck['creditUnion']!='0'){
        $postdata['is_credit_union']=$dataSearchCheck['creditUnion']==2 ? "0":$dataSearchCheck['creditUnion'];
    }
    if($dataSearchCheck['usda']!='0'){
        $postdata['usda']=$dataSearchCheck['usda'];
    }
    if($dataSearchCheck['conventional']!='0'){
        $postdata['conventional']=$dataSearchCheck['conventional'];
    }
    
    if($dataSearchCheck['fha']!='0'){
        $postdata['fha']=$dataSearchCheck['fha'];
    }
    if($dataSearchCheck['va']!='0'){
        $postdata['va']=$dataSearchCheck['va'];
    }
    if($dataSearchCheck['refinance']!='0'){
        //$postdata['refinance']=$dataSearchCheck['refinance'];
        $postdata['refinance']= $dataSearchCheck['refinance']==2 ? "0": $dataSearchCheck['refinance'];
    }
    if($dataSearchCheck['jumbo_ncnfg']!='0'){
        $postdata['jumbo_ncnfg']=$dataSearchCheck['jumbo_ncnfg'];
    }
    if($dataSearchCheck['faux_check']!='0'){
        $postdata['faux_check']=$dataSearchCheck['faux_check'];
    }
    //  faux_check
    if(trim($dataSearchCheck['fa_id_mult'])!=''){
        $postdata['face_id']=trim($dataSearchCheck['fa_id_mult'],',');
    }
    if($dataSearchCheck['is_hphsa']!='0'){
        $postdata['is_hphsa']=$dataSearchCheck['is_hphsa'];
    }
    if($dataSearchCheck['delmethid_mult']!=''){
        $postdata['delivery_method_id']=$dataSearchCheck['delmethid_mult'];
    }
    if($dataSearchCheck['deliveryTypeId']!=''){
        $postdata['delivery_type_id']=$dataSearchCheck['deliveryTypeId'];
    }
    if($dataSearchCheck['postageId']!=''){
        $postdata['postage_id']=$dataSearchCheck['postageId'];
    }
    if($dataSearchCheck['presortedId']!=''){
        $postdata['presorted_id']=$dataSearchCheck['presortedId'];
    }
    if($dataSearchCheck['packageTypeId']!=''){
        $postdata['package_type_id']=$dataSearchCheck['packageTypeId'];
    }
    if($dataSearchCheck['agentCommunicationID']!=''){
        $postdata['agent_communication_id']=$dataSearchCheck['agentCommunicationID'];
    }
    
    if($dataSearchCheck['productName']!='' && $dataSearchCheck['productName']!='NULL'){
        $postdata['product_name']= str_replace (" or ",",",$dataSearchCheck['productName']);
        
    }
    
    if($dataSearchCheck['socialmedia_adtype']!=0 && $dataSearchCheck['socialmedia_adtype']!=NULL){
        $postdata['socialmedia_adtype']=$dataSearchCheck['socialmedia_adtype'];
    }
    if($dataSearchCheck['is_CreditCardMentioned']!='0'){
        $postdata['r_credit_card_mentioned']=$dataSearchCheck['is_CreditCardMentioned'];
    }
    if($dataSearchCheck['groupSize']!=''){
        $postdata['group_size_id']=$dataSearchCheck['groupSize'];
    }
    if($dataSearchCheck['offerOrigin']!=''){
        $postdata['offer_origin_id']=$dataSearchCheck['offerOrigin'];
    }
    if($dataSearchCheck['income_mult']!=''){
        $postdata['income_id']=$dataSearchCheck['income_mult'];
    }
    
    if($dataSearchCheck['CardNetwork_mult']!=''){
        $postdata['card_network']=$dataSearchCheck['CardNetwork_mult'];
    }
    if($dataSearchCheck['FeeProduct']!='0'){
        $postdata['Fee_Product']=$dataSearchCheck['FeeProduct']==2?"0":$dataSearchCheck['FeeProduct'];
    }
    
    if($dataSearchCheck['IntroPricing_mult']!=''){
        $IntroductoryPriceMultiArray = explode(',',$dataSearchCheck['IntroPricing_mult']);
        #print_r($IntroductoryPriceMultiArray); die;
        
        $IntroductoryPricing=IntroductoryAPR($dataSearchCheck['IntroPricing_mult']);
        if (in_array("1", $IntroductoryPriceMultiArray) || in_array("2", $IntroductoryPriceMultiArray)){
        $postdata['purchase_introductory_apr'] ="1";
        }
        if (in_array("3", $IntroductoryPriceMultiArray) || in_array("4", $IntroductoryPriceMultiArray)){
        $postdata['balance_transfer_introductory_apr'] ="1";
        }
        $postdata['null_search'] =$IntroductoryPricing;
    }
    if($dataSearchCheck['Income_Producing_Assets_Segment_Code_mult']!=''){
        $postdata['income_producing_assets_segment_code']=$dataSearchCheck['Income_Producing_Assets_Segment_Code_mult'];
    }
    if($dataSearchCheck['is_rewards']!='0'){
        $postdata['rewards_program']=$dataSearchCheck['is_rewards'];
    }
    if($dataSearchCheck['RewardsProgramEmphasis_mult']!=''){
        $postdata['rewards_program_emphasis']=$dataSearchCheck['RewardsProgramEmphasis_mult'];
    }
    if($dataSearchCheck['edc_id_mult']!=''){
        $postdata['edc_id']=$dataSearchCheck['edc_id_mult'];
    }
   
    if($dataSearchCheck['DMA_ID_mult']!=''){
        $postdata['dma_code']=$dataSearchCheck['DMA_ID_mult'];
    }
    if($dataSearchCheck['riders_mult']!=''){
        $postdata['rider_id']=$dataSearchCheck['riders_mult'];
    }
    if($dataSearchCheck['ERateType_mult']!=''){
        $postdata['e_rate_type']=$dataSearchCheck['ERateType_mult'];
    }
    if($dataSearchCheck['ETermLength_mult']!=''){
        $postdata['e_term_length']=$dataSearchCheck['ETermLength_mult'];
        /*$expoETermslengthArray= explode(',', $dataSearchCheck['ETermLength_mult']);
        $expoETermslengthData='';
        if(!empty($expoETermslengthArray)){
            if(in_array(1,$expoETermslengthArray)){
               $expoETermslengthData.='0,6;'; 
            }if(in_array(2,$expoETermslengthArray)){
               $expoETermslengthData.='7,12;'; 
            }if(in_array(3,$expoETermslengthArray)){
               $expoETermslengthData.='13,18;'; 
            }if(in_array(4,$expoETermslengthArray)){
               $expoETermslengthData.='19,24;'; 
            }if(in_array(5,$expoETermslengthArray)){
               $expoETermslengthData.='25,'; 
            }
        }
        if($expoETermslengthData!=''){
          $postdata['e_term_length']=trim($expoETermslengthData,';');
          $postdata['range_search']= "e_term_length";
        }*/
    }
    if($dataSearchCheck['EOfferPrice_mult']!=''){
        $expoEOfferPriceArray= explode(',', $dataSearchCheck['EOfferPrice_mult']);
        $expoEOfferPriceData='';
        if(!empty($expoEOfferPriceArray)){
            if(in_array(1,$expoEOfferPriceArray)){
               $expoEOfferPriceData.='0.0,5.0;'; 
            }if(in_array(2,$expoEOfferPriceArray)){
               $expoEOfferPriceData.='5.01,8.50;'; 
            }if(in_array(3,$expoEOfferPriceArray)){
               $expoEOfferPriceData.='8.51,10.0;'; 
            }if(in_array(4,$expoEOfferPriceArray)){
               $expoEOfferPriceData.='10.01,12.50;'; 
            }if(in_array(5,$expoEOfferPriceArray)){
               $expoEOfferPriceData.='12.51,'; 
            }
        }
        if($expoEOfferPriceData!=''){
          $postdata['e_offer_price']=trim($expoEOfferPriceData,';');
          $postdata['range_search']= "e_offer_price";
        }/*if($expoEOfferPriceData!='' && $expoETermslengthData!=''){
            $postdata['range_search']= "e_offer_price,e_term_length";
        }*/

    }
    if($dataSearchCheck['is_ECancelFee']!='0'){
        $postdata['e_cancel_fee']=$dataSearchCheck['is_ECancelFee'];
    }
    if($dataSearchCheck['tl_id_mult']!=''){
        $postdata['term_id']=$dataSearchCheck['tl_id_mult'];
    }
    if($dataSearchCheck['correspondent_lending']!='0'){
        $postdata['correspondent_lending']=$dataSearchCheck['correspondent_lending'];
    }
    if($dataSearchCheck['responseMechID_mult']!=''){
        $postdata['response_mechanism_id']=$dataSearchCheck['responseMechID_mult'];
    }
    
    if($dataSearchCheck['FeeProductType']!=''){
        $postdata['ancillary_product_id']=$dataSearchCheck['FeeProductType'];
    }
    /*if($dataSearchCheck['is_multicultural']!='0'){
        $postdata['is_multicultural']=$dataSearchCheck['is_multicultural'];
        
    }*/
    if($dataSearchCheck['multiculturalmarkets_mult']!=''){
        $postdata['target_market_id']=$dataSearchCheck['multiculturalmarkets_mult'];
        
        
    }elseif($dataSearchCheck['multiculturalmarkets_mult']=='' AND $dataSearchCheck['is_multicultural']==1) {
         $postdata['target_market_id']='1';
         $postdata['null_search']='target_market_id,notnull';
    }
    
    if($dataSearchCheck['OptOutFirmOffer']!='0'){
        $postdata['is_prescreen']=$dataSearchCheck['OptOutFirmOffer']==2 ? "0":$dataSearchCheck['OptOutFirmOffer'] ;
        
    }
    if($dataSearchCheck['is_affinion']!='0'){
        $postdata['is_affinion']=$dataSearchCheck['is_affinion'];
    }
    
    if($dataSearchCheck['is_military']!='0'){
        $postdata['is_military']=$dataSearchCheck['is_military'];
    }
    if($dataSearchCheck['prescription']!='0'){
        $postdata['prescription']=$dataSearchCheck['prescription'];
    }
    if($dataSearchCheck['compaignLanguage']!=''){
        if($dataSearchCheck['compaignLanguage']=='English'){
         $postdata['language_id']="1";   
        }
        if($dataSearchCheck['compaignLanguage']=='Bilingual'){
          $postdata['language_id']="2";    
        }
        
    }
    if($dataSearchCheck['businessContent_mult']!=''){
        $postdata['business_content']=$dataSearchCheck['businessContent_mult'];
    }
    if($dataSearchCheck['is_citi']!='0'){
        $postdata['is_citi']=$dataSearchCheck['is_citi']==2?"0":$dataSearchCheck['is_citi'];
    }
    
    if($dataSearchCheck['minmaxmortgage']!='' && $dataSearchCheck['minmaxmortgage']!='0-2000000' && $dataSearchCheck['minmaxmortgage']!='NULL'){
        $exp_loanAmount=explode('-',$dataSearchCheck['minmaxmortgage']);
    }
    //print_r($exp_loanAmount);die;
    if(!empty($exp_loanAmount)){
        $postdata['minimum_loan_amount']=$exp_loanAmount[0].",";
        $postdata['maximum_loan_amount']="0,".$exp_loanAmount[1];
        $postdata['offered_loan_amount']=$exp_loanAmount[0].",".$exp_loanAmount[1];
        $postdata['range_search']="minimum_loan_amount, maximum_loan_amount,offered_loan_amount;rel: (minimum_loan_amount,maximum_loan_amount)|(offered_loan_amount)";
    }
    
    
    if($dataSearchCheck['addedToDatabase']!=''){
        if($dataSearchCheck['addedToDatabase']=='week'){
            $new_date='1 week';
        }
        if($dataSearchCheck['addedToDatabase']=='2week'){
            $new_date='2 week';
        }
        if($dataSearchCheck['addedToDatabase']=='1month'){
            $new_date='';
        }
        if($dataSearchCheck['addedToDatabase']=='3month'){
            $new_date='3 month';
        }
        if($dataSearchCheck['addedToDatabase']=='6month'){
            $new_date='6 month';
        }
        if($dataSearchCheck['addedToDatabase']=='1year'){
            $new_date='1 year';
        }
        $date = date('Y-m-d');
        $newAddedToDatabase = date('Y-m-d', strtotime($date. ' - '.$new_date));
        if($new_date==''){
            $newAddedToDatabase = get_python_like_previous_month(date('Y-m-d'));
        }
        $end_add_to_db="E".$date."T23:59:59";
        $st_add_to_db="S".$newAddedToDatabase."T00:00:00";
        $postdata['added_to_database']=$st_add_to_db.$end_add_to_db;
     }
    if($dataSearchCheck['month1']!='' && $dataSearchCheck['month2']!=''){
        $st_month="S".$dataSearchCheck['month1']."-01T00:00:00";
        
        $end_month=$dataSearchCheck['month2'];
        $lastDateOfMonth = date("Y-m-t", strtotime($end_month));
        $end_month="E".$lastDateOfMonth."T23:59:59";
        //"S2022-01-01T00:00:00E2022-01-31T59:59:59"
        $postdata['added_to_database']=$st_month.$end_month;
    }
    if($dataSearchCheck['approved_date']!='0000-00-00 00:00:00' && $dataSearchCheck['approved_date_to']!='0000-00-00 00:00:00'){
        $exp_from_approved_date= explode(' ', $dataSearchCheck['approved_date']);
        $from_approved_date="S".$exp_from_approved_date[0]."T".$exp_from_approved_date[1];
        $exp_to_approved_date= explode(' ', $dataSearchCheck['approved_date_to']);
        $to_approved_date="E".$exp_to_approved_date[0]."T".$exp_to_approved_date[1];
        $postdata['approved_date']=$from_approved_date.$to_approved_date;
    }
    /*$chkstate='';
    if($dataSearchCheck['state']!=''){
      $chkstate=trim($dataSearchCheck['state']);  
    }
    if($dataSearchCheck['pcountry']!=''){
        $chkCountry=trim($dataSearchCheck['pcountry']);
        $stateidArray=array();
        if($chkCountry=='US'){
            $AndState='';
            if($chkCountry=='US' AND $chkstate!=''){
                $AndState= " AND stateID in ($chkstate)"; 
            }
            $sql_state = "select stateID,stateName from cscan_state Where countryCode='".$chkCountry."'".$AndState;
            $result_state = $DRW->query($sql_state, $DRW_read); 
            while($row_state_data = $DRW->fetch_assoc($result_state)){
                $stateid=$row_state_data['stateID'];
                $stateidArray[]=$stateid;
            }  
        }elseif($chkCountry=='CA'){
            $AndState='';
            if($chkCountry=='CA' AND $chkstate!=''){
                $AndState = " AND stateID in ($chkstate)";
            }
          $sql_state = "select stateID,stateName from cscan_state Where countryCode='".$chkCountry."'".$AndState; 
          $result_state = $DRW->query($sql_state, $DRW_read); 
            while($row_state_data = $DRW->fetch_assoc($result_state)){
                  $stateid=$row_state_data['stateID'];
                  $stateidArray[]=$stateid;
             }  
        }
        if(!empty($stateidArray)){
          $postdata['state_id']= @implode(",", $stateidArray); 
        }
        
    }
    if($chkstate!='' AND $dataSearchCheck['pcountry']==''){
        $postdata['state_id']= $chkstate;
    }
    
    if($dataSearchCheck['state']!=''){
        $postdata['state_id']= trim($dataSearchCheck['state']);;
    }*/
    if($dataSearchCheck['state']!=''){
        $postdata['state_id']=trim($dataSearchCheck['state']); 
        
    }
    if($dataSearchCheck['pcountry']!=''){
        $postdata['country']= $dataSearchCheck['pcountry'];
    }
    if(!empty($dataSearchCheck['company']) && $dataSearchCheck['company']!='NULL') {
        $companySearch =trim($dataSearchCheck['company']);
        $cmpidArray=array();
        $sql_comp = "select companyID,companyName from cscan_company Where " . doMultCompany($companySearch, true, 'company');
        $result_comp = $DRW->query($sql_comp, $DRW_read); 
        while($row_company = $DRW->fetch_assoc($result_comp)){
            $comp_id=$row_company['companyID'];
            $cmpidArray[]=$comp_id;
        }
        if(!empty($cmpidArray)){
        $postdata['company_id'] = @implode(",",$cmpidArray); 
        } 
    }
    if($dataSearchCheck['affinityAssociation']!='0'){
        $postdata['affinity_association']=$dataSearchCheck['affinityAssociation']==2 ? "0":$dataSearchCheck['affinityAssociation'];
        
    }
    if($dataSearchCheck['AffinityCategoryID_mult']!=''){
        $postdata['affinity_category']=$dataSearchCheck['AffinityCategoryID_mult'];
    }
    if($dataSearchCheck['AffinitySubCategoryID_mult']!=''){
        $postdata['affinity_sub_category']=$dataSearchCheck['AffinitySubCategoryID_mult'];
    }
    
    //echo $dataSearchCheck['affinity_association']; 
    if(!empty($dataSearchCheck['affinity_association']) && $dataSearchCheck['affinity_association']!='NULL') {
        $affninityAssocationSearch =trim($dataSearchCheck['affinity_association']);
        $affinityIdArray=array();
        $sql_affinity = "select affinityID,affinityName from cscan_affinity Where " . doMultCompany($affninityAssocationSearch, true, 'affinity');
        $result_affinity = $DRW->query($sql_affinity, $DRW_read); 
        while($row_affinity = $DRW->fetch_assoc($result_affinity)){
            $affinityId=$row_affinity['affinityID'];
            $affinityIdArray[]=$affinityId;
        }
        if(!empty($affinityIdArray)){
        $postdata['afinity_id'] = @implode(",",$affinityIdArray); 
        } 
    }
    
    if(trim($dataSearchCheck['pubTypeID_mult'])!=''){
        $postdata['publication_type_id']=$dataSearchCheck['pubTypeID_mult'];
    }
     
    if(!empty($dataSearchCheck['publication_name']) && $dataSearchCheck['publication_name']!='NULL') {
        $publicationSearch =trim($dataSearchCheck['publication_name']);
        $publicationIdArray=array();
        $sql_publication = "select * from cscan_publication Where " . doMultCompany($publicationSearch, true, 'publication');
        $result_publication = $DRW->query($sql_publication, $DRW_read); 
        while($row_publication = $DRW->fetch_assoc($result_publication)){
            $publicationID=$row_publication['publicationID'];
            $publicationIdArray[]=$publicationID;
        }
        if(!empty($publicationIdArray)){
        $postdata['publication_id'] = @implode(",",$publicationIdArray); 
        } 
    }
    // fico and creditVision,vantageScore credit_vision_score
    if($dataSearchCheck['fico_score']!='' && $dataSearchCheck['fico_score']!='NULL'){
        $postdata['fico_range_id']=$dataSearchCheck['fico_score'];
        
    }
    if($dataSearchCheck['credit_vision_score']!='' && $dataSearchCheck['credit_vision_score']!='NULL'){
        $postdata['creditvision_range_id']=$dataSearchCheck['credit_vision_score'];
        
    }
    if($dataSearchCheck['vantage_score']!='' && $dataSearchCheck['vantage_score']!='NULL'){
        $postdata['vantage_range_id']=$dataSearchCheck['vantage_score'];
        
    }
    if($dataSearchCheck['is_Reloadable']!='0'){
        $postdata['is_reloadable']=$dataSearchCheck['is_Reloadable'];
        
    }
    if($dataSearchCheck['spanelist_filter']=='1' && $dataSearchCheck['search_competi_id']!='' && $dataSearchCheck['search_competi_id']!='NULL'){
        $postdata['competi_id']=$dataSearchCheck['search_competi_id'];                           
        $postdata['competi_id_filter']='primary'; 
    }
    if($dataSearchCheck['spanelist_filter']=='2' && $dataSearchCheck['search_competi_id']!='' && $dataSearchCheck['search_competi_id']!='NULL'){
        $postdata['competi_id']=$dataSearchCheck['search_competi_id'];
        $postdata['competi_id_filter']='non-primary'; 
        /*$nots.=",primary_competi_id";
        if($nots!=''){
         $postdata['nots']=trim($nots,',');    
        }*/
    }
    if($dataSearchCheck['spanelist_filter']=='' && $dataSearchCheck['search_competi_id']!='' && $dataSearchCheck['search_competi_id']!='NULL'){
        
        $postdata['competi_id']=$dataSearchCheck['search_competi_id']; 
    }
    if($dataSearchCheck['external_link']!=''){
        $postdata['external_link']=$dataSearchCheck['external_link'];
        
    }
    if($dataSearchCheck['value_score']!=''){
        $postdata['valuescore_for_household']=$dataSearchCheck['value_score'];
        
    }
    if($dataSearchCheck['age']!=''){
        $postdata['age_id']=$dataSearchCheck['age'];
        
    }
    if($dataSearchCheck['tip_on_card']!='0'){
        $postdata['tip_on_card']=$dataSearchCheck['tip_on_card'];
    }
    if($page_type!=3 && $sid!=''){
        $postdata['current_page']=$page_no;
        $postdata['direction']=$dct;  
        $postdata['sid']="[".$sid."]";
        $postdata['page_type']=$page_type;
    }else{
        $postdata['page_type']=$page_type;
    }
    $postdata['product_status']="1";
    $chcsv2 = curl_init($API_URL);
    $postdata['sortby'] ='entry_id_sort, desc';
    $pagesort='';
    if($sort!='' && $sort==1){
     $postdata['sortby'] ='product_headline_sort, desc';
     $pagesort .= '&amp;sort='.$sort;
    }
    if($sort!='' && $sort==-1){
     $postdata['sortby'] ='product_headline_sort, asc';
     $pagesort .= '&amp;sort='.$sort;
    }
    if($sort!='' && $sort==2){
     $postdata['sortby'] ='primary_company_name, desc'; 
     $pagesort .= '&amp;sort='.$sort;
    }
    if($sort!='' && $sort==-2){
     $postdata['sortby'] ='primary_company_name, asc';
     $pagesort .= '&amp;sort='.$sort;
    }
    if($sort!='' && $sort==3){
     $postdata['sortby'] ='entry_id_sort, asc';
     $pagesort .= '&amp;sort='.$sort;
    }
    if($sort!='' && $sort==-3){
     $postdata['sortby'] ='entry_id_sort, desc';
     $pagesort .= '&amp;sort='.$sort;
    }
    if($sort!='' && $sort==11){
     $postdata['sortby'] ='is_digital, desc';
     $pagesort .= '&amp;sort='.$sort;
    }
    if($sort!='' && $sort==-11){
     $postdata['sortby'] ='is_digital, asc';   
    }
    if($sort!='' && $sort==12){
     $postdata['sortby'] ='is_panelist_score, desc';
     $pagesort .= '&amp;sort='.$sort;
    }
    if($sort!='' && $sort==-12){
     $postdata['sortby'] ='is_panelist_score, asc';
     $pagesort .= '&amp;sort='.$sort;
    }
    if($sort!='' && $sort==13){
     $postdata['sortby'] ='is_animation, desc'; 
     $pagesort .= '&amp;sort='.$sort;
    }
    if($sort!='' && $sort==-13){
     $postdata['sortby'] ='is_animation, asc';
     $pagesort .= '&amp;sort='.$sort;
    }
    
    $postdata['tiebreaker'] ='product_id, desc';
    if($sort!='' && $sort==11 || $sort!='' && $sort==13 || $sort!='' && $sort==-12){
        $postdata['tiebreaker'] ='entry_id_sort, desc';   
    }
    if($sort!='' && $sort==-11 || $sort!='' && $sort==-13 || $sort!='' && $sort==-12){
        $postdata['tiebreaker'] ='entry_id_sort, asc';  
    }
    $posteddata = json_encode($postdata);
    //echo $posteddata;
    curl_setopt($chcsv2, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($chcsv2, CURLOPT_FOLLOWLOCATION, TRUE);
    curl_setopt($chcsv2, CURLOPT_POST, 1);
    curl_setopt($chcsv2, CURLOPT_POSTFIELDS, $posteddata);
    curl_setopt($chcsv2, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($chcsv2, CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
    curl_setopt($chcsv2, CURLOPT_TIMEOUT, 80);
    $response_api = curl_exec($chcsv2);
    if(curl_error($chcsv2)){
            echo 'Request Error:' . curl_error($chcsv2); die;
    }else{
            //$curl_status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $jsonResponseAPIData = json_decode($response_api, JSON_UNESCAPED_UNICODE);
            //echo $curl_status_code;
            //echo ' Total time in ('.number_format((microtime(true) - $start_time),3).' Seconds)';
            //echo "<pre>";
            //print_r($jsonResponseAPIData); 
          
        ###################START IMPLEMENT SAVE SEARCH#########################
        $API_URL_SAVE_SEARCH=ELASTIC_SAVE_SEARCH_PROD;
        if($dataSearchCheck['userID']!=''){
        $postdata['userID']=$dataSearchCheck['userID'];
        }
        $posteddata_save = json_encode($postdata);
        $chcsv2_save = curl_init($API_URL_SAVE_SEARCH);
        curl_setopt($chcsv2_save, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($chcsv2_save, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($chcsv2_save, CURLOPT_POST, 1);
        curl_setopt($chcsv2_save, CURLOPT_POSTFIELDS, $posteddata_save);
        curl_setopt($chcsv2_save, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($chcsv2_save, CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
        curl_setopt($chcsv2_save, CURLOPT_TIMEOUT, 80);
        $response_api_save = curl_exec($chcsv2_save);
        //print_r($response_api); die;
        if(curl_error($chcsv2_save)){
                echo 'Request Error:' . curl_error($chcsv2_save);
        }else{
            $jsonResponseAPIDataSave = json_decode($response_api_save, JSON_UNESCAPED_UNICODE);
            // echo "<pre>";
            // print_r($jsonResponseAPIDataSave); 
            //die;
            if($jsonResponseAPIDataSave['statusMsg']=='OK' && $jsonResponseAPIDataSave['statusCode']=='200'){
                $sess_api_searchID=$jsonResponseAPIDataSave['payload']['0']; 
                $_SESSION["sess_api_searchID"] = $sess_api_searchID;
            }
        }
        curl_close($chcsv2_save);   
        ###################START IMPLEMENT SAVE SEARCH########################

    }
    curl_close($chcsv2);

}
$search_num_of_rows =$jsonResponseAPIData['total_records'];
}
#############################END API IMPLEMENTATION##################
 if($bid<0){
 list($displayKeywords,$name) = getKeywords($ssid);
}
$Q = "SELECT basket_id,basket_name FROM cscan_basket WHERE userID='".$_SESSION['sess_userID']."' ORDER BY basket_name";
$rs = $DRW->query($Q,$DRW_read);
$bids = $DRW->num_rows($rs);
if($bid>0) {
        $selectVals[1] = 'Change Basket Name';
}
if($search_num_of_rows>0) {
        if($bid>=0) {
                $ac = 'Copy';
        }
        else {
                $ac = 'Add';
        }
        $selectVals[2] = $ac.' Selected To';
        $selectVals[7] = $ac.' Current Page To';
        $selectVals[8] = $ac.' All Results To';
}
if($bid>0 || ($bid==0 && $bids>0)) {
        $selectVals[3] = 'Delete Basket';
}
if($search_num_of_rows>0 && $bid>=0){
        $selectVals[4] = 'Move Selected To';
        $selectVals[9] = 'Move Current Page To';
        $selectVals[10] = 'Move All Results To';
        $selectVals[5] = 'Remove Selected';
        $selectVals[6] = 'Save Annotations';
}
if($bid!=0) {
        //$basket_action_text .= '<option value="0">Default Basket</option>';
        //$javascript .= "defArrayID[defArrayID.length] = '0';\ndefArray[defArray.length] = 'Default Basket';\n";
}
$basket_action_text .= '<option value="-2">New Basket</option>';
$javascript .= "defArrayID[defArrayID.length] = '-2';\ndefArray[defArray.length] = 'New Basket';\n";
while($dataB = $DRW->fetch_row($rs)){
        $basket_id = $dataB[0];
        $basket_name_tmp = $dataB[1];
        if($basket_id!=$bid){
                $basket_action_text .= '<option value="'.$basket_id.'">'.htmlspecialchars($basket_name_tmp).'</option>';
                $javascript .= "bidArrayID[bidArrayID.length] = '$basket_id';\nbidArray[bidArray.length] = '".singleQuoteSafe($basket_name_tmp)."';\n";
        }
        else {
                $javascript .= "currbid = '$basket_id';\ncurrbname = '".singleQuoteSafe($basket_name_tmp)."';\n";
        }
}

$PAGE_HEADING = "Search Results";
$TITLE = "Competiscan $PAGE_HEADING";
$HEAD = '<script src="includes/fullresults.js?v=20100126" type="text/JavaScript"></script>
<script src="includes/swfobject.js" type="text/javascript"></script>
<script src="includes/preview.js?v=20131201" type="text/JavaScript"></script>
<script type="text/JavaScript">
<!--
function intitalizeVars(){
        '.$javascript.'
        pdfsearch = \''.singleQuoteSafe($pdfsearchKeyword).'\';';
        if(isset($_SESSION['selected_productID']) && count($_SESSION['selected_productID'])>0){
                $HEAD .= '
                do_doDis = false;';
        }

        $HEAD .= '
        is_admin = ';
        if($_SESSION['sess_plevel']>0){
                $HEAD .= 'true';
        }
        else{
                $HEAD .= 'false';
        }
        $HEAD .= ';
}
//-->
</script>';
$BODYTAG = ' onload="intitalizeVars();"';
#include('header_top.php');

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Competiscan">
    <title><?php echo isset($TITLE) && $TITLE ? $TITLE : 'Competiscan' ?></title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/custom.css" rel="stylesheet">
    
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,400,300,600,700|Open+Sans+Condensed:300,700,300italic' rel='stylesheet' type='text/css'>
    <link rel="shortcut icon" href="/favicon.ico" />
    <link href="includes/competiscan_stylesheet.css?v=20100318" rel="stylesheet" type="text/css" />
    <script src="includes/jsFunctions.js?v=20090601"></script>
    <script src="includes/ajax.js?v=20090601"></script>
    <script>window.jQuery || document.write('<script src="js/jquery.js">\x3C/script>')</script>
        <?php echo isset($HEAD) ? $HEAD : '' ?>
<style>
.pagination {
  display: inline-block;
}

.pagination a {
  color: black;
  float: left;
  padding: 8px 16px;
  text-decoration: none;
}
</style>
</head>
<body <?php echo isset($BODYTAG) ? $BODYTAG : '' ?>>
<div class="loader" style="display:none;"></div>
<?php 
/*$request_uri = basename($_SERVER['REQUEST_URI'], '?' . $_SERVER['QUERY_STRING']);
if(!in_array($request_uri, ['imap.php'])){
    include_once("includes/analyticstracking.php");
}*/
?>
<!-- Navigation -->
<nav class="navbar navbar-default navbar-fixed-top" role="navigation">
    <div class="container">
        <div class="navbar-header page-scroll">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand page-scroll" id="logo" href="/">
                                <img src="/images/competiscan-logo.png" alt="Competiscan logo">
            </a>
        </div>
        <div class="collapse navbar-collapse navbar-ex1-collapse">
            <ul class="nav navbar-nav">
<?php include('./nav-common.php') ?>
            </ul>
        </div>
        <!-- /.navbar-collapse -->
    </div>
    <!-- /.container -->
</nav>
<div id="titlebar">
        <div class="container">
                <h1><?php echo isset($PAGE_HEADING) ? $PAGE_HEADING : '' ?></h1>
                <div id="breadcrumbs">
                        <span><a href="/">Competiscan</a></span>
                        <span class="separator">/</span>
                        <span class="current"><?php echo isset($PAGE_HEADING) ? $PAGE_HEADING : '' ?></span>
                </div>
        </div>
</div>
<div id="content" class="container">
<?php 
if(!empty($_SESSION['sess_username'])) {
        $show_header_top=true;
    
    /*######## Start for Page permission ########*/ 
    if(!defined('ENV')){
        define('ENV',getenv('SERVER_NAME'));
    }  
    //if(ENV == 'localhost' || ENV == 'demo.competiscan.com'){
        
        $page_permission = getPagePermission(); 
        if(!empty($_SESSION['sess_search_page_permission'])){
            $page_permission=$_SESSION['sess_search_page_permission'];
        }       
        if(!empty($page_permission) AND in_array('power_search',$page_permission)){
            $show_header_top=true;
        }else{
            $show_header_top=false;
        }

    
     /*######## End for Page permission ########*/
    if($show_header_top){    
    ?>
        <table cellspacing="0" class="searchMenutab">
            <tr> 
                <td><a href="emailAlerts.php" title="Your Search Settings">Email Alerts</a></td>
                <td><a href="savedsearch.php" title="Your Saved Search">Saved Searches</a></td>
                <td><a href="baskets.php" title="Export Baskets">Export Baskets</a></td>
                <td><a href="fullsearch.php?searchview=2" title="Power Search">Power Search</a></td>
                <td><a href="lastSearch.php" title="View the last search performed by you">Last Search</a></td>
                <td><a href="lastResult.php" title="View results of the last search performed by you">Last Results</a></td>
            </tr>
        </table>
<?php }
 }?>
<div id="page">
<div class="headings" id="pcontainer"><strong>Welcome: <?php echo $_SESSION['sess_username']; ?></strong></div>
<hr />
<?php
//echo 'AFTER header file Total time in ('.number_format((microtime(true) - $start_time),3).' Seconds)'."<br/>";
ob_flush();// this is where the results get printed

if($search_num_of_rows > 0) {
        if($save_msg!='') {
                echo '<div class="error" style="float:right;">'.htmlspecialchars($save_msg).'</div>';
        }
        elseif($saved!=1) {
                ?>
                <div class="bodytext" style="float:right;">
                <form name="nameForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" onSubmit="return chk_search_name(document.nameForm.searchName.value);">
                <strong>Enter search name</strong>
                <input type="text" class="input_box" size="20" maxlength="40" name="searchName" />
                <input type="submit" name="submit" value="save" class="submitbutton" />
                <input type="hidden" name="ssid" value="<?php echo $ssid; ?>" />
                <input type="hidden" name="sendsave" value="1" />
                </form>
                </div>
                <?php
        }
}
?>
<div style="clear:both;height:5px;">&nbsp;</div>
<?php
if($bid<0) {
        echo '<div class="bodytext"><strong>Your Search Criteria:</strong><br />'.utf8_decode($displayKeywords).'</div>';
}
else {
        if($bid==0){
                $basket_name = 'Default Basket';
        }
        else{
                $Q = "SELECT basket_name FROM cscan_basket WHERE userID=".$_SESSION['sess_userID']." AND basket_id=$bid";
                $rs = $DRW->query($Q,$DRW_read);
                $dataB = $DRW->fetch_row($rs);
                $basket_name = $dataB[0];
        }
        if($search_num_of_rows > 0){
                echo '<div class="bodytext"><strong>Your Basket: '.htmlspecialchars($basket_name).'</strong>';
                if(isset($_GET['updated'])) {
                        echo ' &nbsp; <span class="error">UPDATED</span>';
                }
                echo '</div>';
        }
}
$buttons = '<div style="clear:both;height:5px;">&nbsp;</div><div>';
/*if($search_num_of_rows > 0) {
        $buttons .= '<div style="float:left;margin-left:0px;"><form action="myExcel1.php" method="post" name="myexceller"><input type="hidden" name="ssid" value="'.$ssid.'" /><input type="hidden" name="bid" value="'.$bid.'" /><input type="hidden" name="page" value="'.$page.'" /><input type="hidden" name="num_rec" value="'.$search_num_of_rows.'" /><input type="hidden" name="sort" value="'.$sort.'" /><input class="submitbutton" type="submit" name="myExcel" value="My Excel" /></form>';
        $buttons .='<div style="float:left;margin-left: 91px;margin-top: -36px;">
            <form action="" method="post" name="express_download">';
         $buttons .="<input type ='hidden' name='post_json' value='".trim($posteddata)."'/>";
         $buttons .='<input class="submitbutton" type="submit" name="myExcel_download" value="Express Download" />
            </form></div>';
       $buttons .='</div><!--search--><!--paging-->';
}*/
if($search_num_of_rows > 0) {
    $buttons .= '<div style="float:left;margin-left:0px;"><form action="myExcel1.php" method="post" name="myexceller"><input type="hidden" name="ssid" value="'.$ssid.'" /><input type="hidden" name="bid" value="'.$bid.'" /><input type="hidden" name="page" value="'.$page.'" /><input type="hidden" name="num_rec" value="'.$search_num_of_rows.'" /><input type="hidden" name="sort" value="'.$sort.'" /><input class="submitbutton" type="submit" name="myExcel" value="My Excel" /></form>';
    $buttons .='<div style="float:left;margin-left: 91px;margin-top: -36px;">';
    $buttons .='<button class="submitbutton myExcel_download">Express Download</button>
    </div>';
   $buttons .='</div><!--search--><!--paging-->';
}
else {
        $buttons .= '<div class="error" style="text-align:center;">No Results Found</div><div>&nbsp;</div>';
}
$buttons .= '<div style="clear:both;height:5px;">&nbsp;</div></div>';
if($search_num_of_rows > 0) {
        if($search_num_of_rows > 3){
                echo $buttons;
        }
        $buttons = str_replace('exceller','exceller2',$buttons);
        $buttons = str_replace('<!--search-->','<div style="float:left;margin-left:10px;"><form action="fullsearch.php" method="get" name="fullsearcher"><input class="submitbutton" type="submit" name="back" value="Go To Search" /><input type="hidden" name="ssid" value="'.$ssid.'" /></form></div>',$buttons);
}
//$buttons='';
?>
<form name="basketer" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<?php
if($search_num_of_rows > 0) {
?>
<div style="border:solid 1px #0055E3;">
<table width="100%" cellpadding="4" cellspacing="0" class="sortable" style="word-break:break-word;">
        <tr>
        <td width="18%" class="toptable">Sort By:</td>
        <td width="40%" class="toptable"><?php 
                sortLinks($sort,1,'Headline');
        ?></td>
        <td width="18%" class="toptable"><?php 
                sortLinks($sort,2,'Company');
        ?></td>
        <td width="24%" class="toptable"><?php 

                sortLinks($sort,3,'Entry ID');
                echo ' &nbsp; ';
                echo '&nbsp;';
                sortLinks($sort,11,'A');
                echo '&nbsp;';
                sortLinks($sort,12,'S');
                //for identify animation
                echo '&nbsp;';
                sortLinks($sort,13,'G');
        ?></td>
        </tr>
<?php
        $countbasket = 0;
        foreach($jsonResponseAPIData['results'] as $resultProdData){
            //echo "<pre>";
            //print_r($resultProdData);
            $productID=$resultProdData['product_id'];
            $entryID=$resultProdData['entry_id'];
            $mid = $resultProdData['mchanne_id'];
            $productHeadline=highlight($name,$resultProdData['product_headline']);
            //highlight($name,$productHeadline);
            if($productHeadline=='') {
                if($mid==5 || $mid==7){
                        $productHeadline = 'An Online Ad from '.htmlspecialchars($company_primary);
                }
                else{
                        $productHeadline = 'See complete product details';
                }
            }
            
            $sectorName = "";
            $uniqueSectors = array();
            if (isset($resultProdData['sector_categories']['category']) &&
                !empty($resultProdData['sector_categories']['category'])) {

                foreach ($resultProdData['sector_categories']['category'] as $sectName) {

                    $sectName = trim($sectName);

                    if (!in_array($sectName, $uniqueSectors)) {
                        $uniqueSectors[] = $sectName;
                    }
                }

                $sectorName = implode(", ", $uniqueSectors);
            }
            $category = "";
            $uniqueCategories = array();
            if (isset($resultProdData['sector_categories']['sub_category']) &&
                !empty($resultProdData['sector_categories']['sub_category'])) {

                foreach ($resultProdData['sector_categories']['sub_category'] as $categoryName) {

                    $categoryName = trim($categoryName);

                    if (!in_array($categoryName, $uniqueCategories)) {
                        $uniqueCategories[] = $categoryName;
                    }
                }

                $category = implode(", ", $uniqueCategories);
            }

            if ($category == '') {
                $category = 'Not Mentioned';
            }
            $subCat = "";
            $uniqueSubCategories = array();

            if (isset($resultProdData['sector_categories']['sub_sub_category']) &&
                !empty($resultProdData['sector_categories']['sub_sub_category'])) {

                foreach ($resultProdData['sector_categories']['sub_sub_category'] as $subcategoryName) {

                    $subcategoryName = trim($subcategoryName);

                    if (!in_array($subcategoryName, $uniqueSubCategories)) {
                        $uniqueSubCategories[] = $subcategoryName;
                    }
                }

                $subCat = implode(", ", $uniqueSubCategories);
            }

            if ($subCat == '') {
                $subCat = 'Not Mentioned';
            }
            $mpannelid = $resultProdData['mpanel_id'];
            //$addedToDatabase = $resultProdData['actual_added_to_database'];
            $mediaPanel = mediaPanelName($mpannelid);
            $second_company="";
            $company_primary=$resultProdData['primary_company_name'];
            $cpcount=1;
            $company='';
            foreach($resultProdData['company_names'] as $cmpName){
                if($cmpName!=$company_primary){
                    $second_company.=$cmpName.", "; 
                 } 
                 
            $cpcount++;}
            $second_company=rtrim($second_company,", ");
            //$variantID = $resultProdData['variant_id'];
            //$isVariant = $resultProdData['is_variant'];
            //$isDemographic = $resultProdData['is_demographic'];
            //$isInsight = $resultProdData['is_insight'];
            //$isSurvey = $rs['isSurvey'];
            $isFICO='';
            //$isFICO = $rs['isFICO'];
            $isdigital=$resultProdData['is_digital'];
            //$default_image_url=$resultProdData['default_image_url'];
            $isdigital_pan=$resultProdData['panelist_sort'];
            //$second_company=$rs['secondCompany'];
            //$is_panelist_score='';
            $is_panelist_score=$resultProdData['is_panelist_score'];
            
            $queryI = "SELECT UNIX_TIMESTAMP(img_document_createddate) FROM cscan_img_document WHERE productID=$productID AND document_id=1 AND img_document_default=1";
            $query_resultI = $DRW->query($queryI,$DRW_read);
            $dataI = $DRW->fetch_row($query_resultI);
            $img_createddate_ts = 0;
            if (is_array($dataI) && isset($dataI[0]) && is_numeric($dataI[0])) {
                $img_createddate_ts = (float)$dataI[0];
            }
            $queryI = "SELECT img_companyID FROM cscan_img WHERE productID=$productID AND img_id=1";
            $query_resultI = $DRW->query($queryI,$DRW_read);
            $dataI = $DRW->fetch_row($query_resultI);
            $img_companyID = 0;
            if (is_array($dataI) && isset($dataI[0])) {
                $img_companyID = (float)$dataI[0];
            }
            if(!empty($img_companyID)){
                    $pi = 'cid='.$img_companyID;
            }
            else{
                    $pi = 'id='.$productID;
            }
            $is_flash = false;
            $is_image = false;
            $sample_javascript = '';
            if($mid==5 || $mid==7){
                    $query2 = "SELECT document_size_byte,document_filename,document_path,document_content_type,document_placement FROM cscan_document WHERE productID=$productID AND document_id=2";
                    $query_result2 = $DRW->query($query2,$DRW_read);
                    $data2 = $DRW->fetch_row($query_result2);
                    $document_size_byte = 0;
                    if (is_array($data2) && isset($data2[0]) && is_numeric($data2[0])) {
                        $document_size_byte = (int)$data2[0];
                        $document_filename = $data2[1];
                        $document_path = $data2[2];
                        $document_content_type = $data2[3];
                        $document_placement = $data2[4];
                    }
                    
                    if(empty($document_placement)){
                            $document_placement = '200x200';
                    }
                    list($fwidth,$fheight) = explode('x',$document_placement);
                    list($fwidth,$fheight) = setWidthHeight($fwidth,$fheight, 400, 200);

                    $sample_javascript .= " sample_widths['".$productID."'] = $fwidth; sample_heights['".$productID."'] = $fheight; ";

                    if(preg_match('/flash/i',$document_content_type)){
                            $is_flash = true;
                            $sample_javascript .= " sample_types['".$productID."'] = 'flash'; ";
                    }
                    elseif(preg_match('/image/i',$document_content_type)){
                            $is_image = true;
                            $sample_javascript .= " sample_types['".$productID."'] = 'image'; ";
                    }
            }
            $sample_javascript='';
            
?>
        <tr>
         
        <td rowspan="3" class="bodytext" valign="top"><?php 
        if($sample_javascript!=''){
                echo '<script type="text/javascript">'.$sample_javascript.'</script>';
        }
        //echo 'Before START IMAGE SECTION Total time in ('.number_format((microtime(true) - $start_time),3).' Seconds)'."<br/>";
        $query2 = "SELECT document_content_type FROM cscan_document WHERE productID=$productID AND document_id=1";
        $query_result2 = $DRW->query($query2,$DRW_read);
        $data2 = $DRW->fetch_row($query_result2);
        $document_content_type = '';
        if (is_array($data2) && isset($data2[0])) {
            $document_content_type = $data2[0];
        }
        if($document_content_type=='html'){
            $showprev='1';
            $filetyp='1';
        }else if($document_content_type=='video/mp4' || $document_content_type=='video/mpeg-4' || $document_content_type=='image/mpeg-4' || $document_content_type=='image/mp4'){
            $showprev='2';
            $filetyp='2';
        }else{
            $showprev='0';
            $filetyp='0';                            
        }
        if($isdigital=='1'){
            $showdig='1';
        }else{
            $showdig='0';
        }
        //$default_image_urlproductImg.php?<?php echo $pi; 
        ?><div>
            
            <img src="productImg.php?<?php echo $pi; ?>" id="<?php echo 'pimg'.$productID; ?>" alt="" title="Preview this Product" class="tableimg" onclick="<?php if($showprev>0){?> doPreview_digital('<?php echo $productID; ?>',<?php echo $img_createddate_ts; ?>,<?php echo $showprev;?>,<?php echo $filetyp;?>,<?php echo $showdig; ?>);<?php }else{ ?> doPreview('<?php echo $productID; ?>',<?php echo $img_createddate_ts; ?>,<?php echo $showdig; ?>);<?php } ?>" onmouseover="<?php if($showprev>0){ ?>showPreview_digital(<?php echo $productID; ?>,<?php echo $img_createddate_ts; ?>,<?php echo $showprev;?>,<?php echo $filetyp;?>,<?php echo $showdig; ?>);<?php }else{ ?> showPreview(<?php echo $productID; ?>,<?php echo $img_createddate_ts; ?>,<?php echo $showdig; ?>);<?php } ?> return true;" onmouseout="hidePreview(<?php echo $productID; ?>,<?php echo $showdig; ?>); return true;" />
        </div>
        <div style="float:left;padding:2px;"><label><input type="checkbox" name="basket[]" value="<?php echo $productID; ?>" onclick="doExportBasketSelect(<?php echo $countbasket; ?>);"<?php 
        if(isset($_SESSION['selected_productID']) && in_array($productID,$_SESSION['selected_productID'])) {
                echo ' checked="checked"'; 
        }
        ?> /><?php 
        if($bid>=0) {
                $bq = "SELECT basket_note,DATE_FORMAT(basket_date,'%m/%d/%Y') FROM cscan_product_basket WHERE basket_id=$bid AND userID={$_SESSION['sess_userID']} AND productID=$productID";
                $rsb = $DRW->query($bq,$DRW_read);
                $datab = $DRW->fetch_row($rsb);
                $basket_note = $datab[0];
                $basket_date = $datab[1];
                echo $basket_date.'</label><br />Annotation:<br /><textarea name="basket_note_'.$productID.'" rows="3" cols="20" class="input_box">'.htmlspecialchars($basket_note,ENT_QUOTES).'</textarea>';
        }
        else {
                $baskimg = '<img src="images/cart_blue.png" alt="Add" title="Add to Export Basket" border="0" />';
                $bq= "SELECT DISTINCT basket_name FROM cscan_basket cb left JOIN cscan_product_basket cpb ON (cb.basket_id = cpb.basket_id) AND (cb.userID = cpb.userID) Where cpb.productID='".$productID."' AND cb.userID={$_SESSION['sess_userID']}";
                    $rsb = $DRW->query($bq,$DRW_read);
                    if($DRW->num_rows($rsb) > 0){
                        $bkt_name=array();
                        $basketname='';
                        while($datab = $DRW->fetch_row($rsb)){
                           $bkt_name[]= $datab[0]; 
                        }
                        $basketname=implode(', ',$bkt_name);
                            $baskimg = '<img src="images/cart_blue_full.png" alt="Add" title="'.$basketname.'" border="0" />';
                    }
                //}
                echo '</label><a href="#" onclick="doCheck('.$countbasket.'); return false;">'.$baskimg.'</a>';
        }
        $countbasket++;
        ?></div>
        <?php
        if($mid!=5 && $mid!=7){
                $query2 = "SELECT document_size_byte FROM cscan_document WHERE productID=$productID AND document_id=1";
                $query_result2 = $DRW->query($query2,$DRW_read);
                $data2 = $DRW->fetch_row($query_result2);
                $document_size_byte = 0;

                if (is_array($data2) && isset($data2[0]) && is_numeric($data2[0])) {
                    $document_size_byte = (int)$data2[0];
                }
                $sizeofPDFinKB=$document_size_byte/1024;
                $sizeofPDFinMB=$sizeofPDFinKB/1024;
                if($sizeofPDFinMB<1) {
                        $DisplaySize=round($sizeofPDFinKB,2)." KB";  
                }
                else {
                        $DisplaySize=round($sizeofPDFinMB,2)." MB";  
                }
                if($document_size_byte>=0 && $mid!=10 && $mid!=9 && $mid!=5){
                        ?>
                        <div style="float:left;padding:2px 0px 0px 14px;"><a class="bluelink" href="<?php echo 'productDocuments.php?id='.$productID.$pdfsearchKeyword; ?>" onclick="pdfWin(<?php echo $productID; ?>); return false;" title="PDF Content"><img src="images/pdf.jpg" border="0" style="vertical-align:top;" /> <?php echo $DisplaySize; ?></a><br />
                        <?php 
                        if($_SESSION['sess_plevel']>0){
                                ?>
                                <a class="bluelink" href="<?php echo 'productImages.php?pp=1&amp;id='.$productID; ?>" target="_blank" title="PowerPoint Content"><img src="images/ppt.jpg" border="0" style="vertical-align:top;" /></a>
                                <?php
                        }
                        if(!in_array('jpeg',$_SESSION['sess_search_exclude'])){
                                ?>
                                <a class="bluelink" href="<?php echo 'productImages.php?id='.$productID; ?>" target="_blank" title="JPEG Content"><img src="images/jpg.jpg" border="0" style="vertical-align:top;" /></a>
                                <?php 
                        }
            $mpannelid_arr=@explode(',',$mpannelid);
                        if($mid==3 AND (in_array(1,$mpannelid_arr) OR in_array(2,$mpannelid_arr))){
                //approved_date,addedToDatabase,addedToDatabase>='2020-12-01 00:00:00' AND 
                $query3 = "select productID,electronicID from cscan_product_detail where addedToDatabase>='2020-12-01 00:00:00' AND productID=$productID";   
                $query_result3 = $DRW->query($query3,$DRW_read);
                $numrows3=$DRW->num_rows($query_result3);
                $resultData=$DRW->fetch_array($query_result3);
                $electronicID = '';
                if (is_array($resultData) && isset($resultData['electronicID'])) {
                    $electronicID = $resultData['electronicID'];
                }
                if($numrows3>0 && $electronicID==1){
                    $query2 = "select muid from cscan_product_email where productID=$productID";   
                    $query_result2 = $DRW->query($query2,$DRW_read);
                    $numrows2=$DRW->num_rows($query_result2);                            
                    if($numrows2>0){?>                        
                        &nbsp;<a class="bluelink" href="<?php //echo 'processed-html.php?id='.$productID; ?>" onclick="htmlWin(<?php echo $productID; ?>); return false;" title="Processed HTML"><img width="17" height="17" src="processed-html-2.png" border="0" style="vertical-align:top;" /></a>
                <?php }
                }
                }
                        ?>
                        </div>
                        <?php 
                }
        }
        ?>
        <?php 
         ################# Start for Anotation Tool Link Permission################
        //if(ENV == 'localhost' || ENV == 'demo.competiscan.com'){
            if(isset($_SESSION['sess_anotation_tool_link']) AND in_array('anotation_tool_link',$_SESSION['sess_anotation_tool_link'])){ 
               if(($mid==3) AND ($resultProdData['mpanel_id']==1|| $resultProdData['mpanel_id']==2)) { 
                   $queryProdEmail = "select muid from cscan_product_email where productID=$productID";   
                   $query_result_prod_email = $DRW->query($queryProdEmail,$DRW_read);
                    $numrows2=$DRW->num_rows($query_result_prod_email);                            
                    if($numrows2>0){
                       $prodEmailData=$DRW->fetch_row($query_result_prod_email);
                       $muID=$prodEmailData[0];
                       $displayAnotationLink=ANNOTATIONTOOLDATAURL.$muID;
                       $styleType='vertical-align:top;';
                        $imgSRC='settings-anotation.png';
                        if($resultProdData['is_annotated']==1){
                            $styleType='color:red;vertical-align:top;';
                            $imgSRC='annotaion-img.png';
                        }if($resultProdData['is_annotated']==2){
                            $styleType='vertical-align:top;';
                            $imgSRC='red_annotation.png';
                        }
                       ?>
                        <div style="float:left;padding:2px 0px 0px 14px; margin: -20px 0px 0px 115px;">&nbsp;<a class="bluelink" target="_blank" href="<?php echo $displayAnotationLink; ?>"  title="Anotation Tool Link">
                                <img width="15" height="15" src="<?php echo $imgSRC; ?>" border="0" style="<?php echo $styleType; ?>" /></a>
                        </div>
                   <?php }
               }

            }
        ?>
         <!--Product Analysis-->
         <?php 
        if(isset($_SESSION['sess_ai_analysis_link']) AND in_array('ai_analysis_link',$_SESSION['sess_ai_analysis_link']) AND ($resultProdData['is_digital']!= '1') and ($resultProdData['mchanne_id']!= '5'|| $resultProdData['mchanne_id']!= '10' || $resultProdData['mchanne_id']!= '9')){ 
        ?>
         <div style="padding:2px 0px 0px 14px;">&nbsp;<a class="bluelink" target="_blank" href="https://ai-analysis.competiscan.com/?productid=<?php echo $productID;?>"  title="Product Analysis">
            <img width="15" height="15" src="product_analysis.png" /></a>
        </div>
        <?php } ?>
        <!--Product Analysis-->
        </td>
        <td class="bodytext" valign="top" style="border-bottom:none;"><strong>Headline:</strong><br /><a href="https://cp.competiscan.com/productdetail?id=<?php echo $productID.$pdf_key; ?>" onclick="productDescription_digital_testing(<?php echo $productID; ?>,'<?php echo singleQuoteSafe($pdf_key.'&amp;ssid='.$ssid); ?>'); return false;" class="HyperLink" title="See complete product details"><?php echo mb_convert_encoding(html_entity_decode( str_replace("\xE2\x80\x8B", "",$productHeadline),ENT_QUOTES, "UTF-8"), "HTML-ENTITIES","UTF-8"); ?></a></td>
        <!--<td class="bodytext" valign="top" style="border-bottom:none;"><strong>Headline:</strong><br /><a href="productDetail.php?id=<?php echo $productID.$pdf_key.'&amp;ssid='.$ssid; ?>" onclick="productDescription_digital(<?php echo $productID; ?>,'<?php echo singleQuoteSafe($pdf_key.'&amp;ssid='.$ssid); ?>'); return false;" class="HyperLink" title="See complete product details"><?php echo mb_convert_encoding(html_entity_decode( str_replace("\xE2\x80\x8B", "",$productHeadline),ENT_QUOTES, "UTF-8"), "HTML-ENTITIES","UTF-8"); ?></a></td>-->
        <td class="bodytext" valign="top" style="border-bottom:none;"><strong>Primary Company:</strong><br /><?php echo htmlspecialchars(($company_primary) ? ($company_primary) :'Not mentioned'); ?></td>
        <td class="bodytext" valign="top" style="border-bottom:none;"><strong>EntryID:</strong><br /><?php 
        if ($_SESSION['sess_plevel'] > 0) {
            if ($resultProdData['is_digital'] == '1') {
                if ($resultProdData['mchanne_id'] == '5') {
                    $add = '&add=1';
                    $ec_content_type='mobiledigital&product_status=1';
                } else if ($resultProdData['mchanne_id'] == '9') {
                    $add = '&add=2';
                    $ec_content_type='mobiledigital&product_status=1';
                } else if ($resultProdData['mchanne_id'] == '10') {
                    $add = '&add=3';
                    $ec_content_type='mobiledigital&product_status=1';
                }
                $adminProductDigitalUrl="https://cs.competiscan.com/addproduct/v2/".$productID."?ec_content_type=".$ec_content_type;
                echo '<a href="'.$adminProductDigitalUrl.'" target="_blank" class="bluelink">'.$entryID.'</a>';
                //echo '<a href="admin/addproduct-digital.php?id='.$productID.$add.'" target="_blank" class="bluelink">'.$entryID.'</a>';
            } elseif ($resultProdData['mchanne_id']==3 || $resultProdData['mchanne_id']==1 || $resultProdData['mchanne_id']==2 || $resultProdData['mchanne_id']==6 || $resultProdData['mchanne_id']==14 ||$resultProdData['mchanne_id']==19 ||$resultProdData['mchanne_id']==20) {
                   $ec_content_type='email';
                  if($resultProdData['mchanne_id']==1){
                   $ec_content_type='direct';  
                  }if($resultProdData['mchanne_id']==2){
                    $ec_content_type='print_media';  
                   }if($resultProdData['mchanne_id']==6){
                    $ec_content_type='social_media';  
                   }if($resultProdData['mchanne_id']==14){
                    $ec_content_type='website_url';  
                   }if($resultProdData['mchanne_id']==19){
                    $ec_content_type='uxmedia';  
                   }if($resultProdData['mchanne_id']==20){
                    $ec_content_type='uxmedia';  
                   }
                $adminProductUrl="https://cs.competiscan.com/addproduct/v2/".$productID."?ec_content_type=".$ec_content_type;  
                echo '<a href="'.$adminProductUrl.'" target="_blank" class="bluelink">'.$entryID.'</a>';
            }  
            else {
                echo '<a href="admin/addproduct.php?id='.$productID.'" target="_blank" class="bluelink">'.$entryID.'</a>';
            }
        }else{
            echo $entryID;
        }
  /* ######  For digital media channel panelist ###### */
     //$isdigital_pan= CheckPanelistDigital($productID);
     if($isdigital=='1'){
    
        echo '<br /><span style="color:#cccccc;">A</span>&nbsp;&nbsp;'; 
     }       
        
   /* ###### End For digital media channel panelist ###### */
     
   /* ######  For FICO Vantage and Credit Score availability identification ###### */
     if($is_panelist_score=='1'){
        if($isdigital!='1'){
            echo '<br />';
        }
        echo '<span style="color:#cccccc;">S</span>'; 
     }
     
    /* ###### END For FICO Vantage and Credit Score availability identification ###### */ 
     //for identify animation
      $query_chk = "select productID from cscan_isanimation_inprocesshtml where is_animation_available=1 AND productID='".$productID."'";   
        $query_result_chk = $DRW->query($query_chk,$DRW_read);
        $numrows_chk=$DRW->num_rows($query_result_chk);
        if($numrows_chk>0){
            if($isdigital!='1' AND $is_panelist_score!='1'){
                echo '<br />';
            }
            echo '<span style="color:#cccccc;">G</span>'; 
        }
        ?></td>
        </tr>
        <tr>
        <td class="bodytext" valign="top" style="border-bottom:none;"><strong>Media Channel:</strong><br /><?php echo htmlspecialchars(mediaChannelName($mid)); ?></td>
        <td class="bodytext" valign="top" style="border-bottom:none;">
            <?php            
                if(!empty($second_company)){
                /*if(strlen($second_company) >120){
                    $second_company = substr($second_company, 0, 120) . '...';
                }else{
                     $second_company;
                }*/ ?>
                <strong>Additional Companies:</strong><br /><?php echo htmlspecialchars(($second_company)? trim($second_company):'Not mentioned');
                }else{?>
                 <strong>Sector:</strong><br /><?php echo htmlspecialchars(rtrim($sectorName,", "));
                }
             ?>           
        
        </td>
        <td class="bodytext" valign="top" style="border-bottom:none;"><strong>Category:</strong><br /><?php echo htmlspecialchars((($category) ? (rtrim($category,", ")) :'Not mentioned')); ?></td>
        </tr>
        <tr>
        <td class="bodytext" valign="top"><strong>Audience:</strong><br /><?php echo htmlspecialchars($mediaPanel); ?></td>
        <td class="bodytext" valign="top">
            <?php            
                if(!empty($second_company)){?>
                    <strong>Sector:</strong><br /><?php echo htmlspecialchars(rtrim($sectorName,", ")); 
                }else{
                    echo '&nbsp;';                    
                }
            ?>
        </td>
        <td class="bodytext" valign="top"><strong>Sub Category:</strong><br /><?php echo htmlspecialchars((($subCat) ? (rtrim($subCat,", ")) :'Not mentioned')); ?></td>
        </tr>
<?php
    }
    //echo 'AFTER LOOP Total time in ('.number_format((microtime(true) - $start_time),3).' Seconds)'."<br/>"; 
?>
</table>
</div>
<?php 
}
if($bid>=0) {
        ?>
        <table border="0" cellspacing="4" cellpadding="0">
        <tr>
                <td class="bodytext" valign="top"><strong>Your Basket: <?php echo $basket_name; ?></strong>
                <?php 
                if(isset($_GET['updated'])) {
                        echo ' &nbsp; <span class="error">UPDATED</span>';
                }
                ?>
                </td>
        </tr>
        </table>
<?php 
}
if($search_num_of_rows>0 || $bid>0){
        ?>
        <table border="0" cellspacing="0" cellpadding="0"><tr><td class="bodytext" valign="bottom"><a href="baskets.php" title="Export Baskets"><img src="images/cart_blue.png" alt="Export Basket" border="0" style="margin-right:4px;" /></a></td>
                <td class="bodytext" valign="bottom"><select name="basket_action" size="1" style="font-family: verdana, Helvetica, sans-serif;font-size: 11px;color: #000000;border: 1px #000000 solid;margin-right:4px;" onchange="checkAction();"><option value="0" selected="selected">&nbsp;</option>
                <?php 
                foreach($selectVals as $k=>$val){
                        echo '<option value="'.$k.'">'.htmlspecialchars($val).'</option>';
                }
                ?>
                </select></td>
                <?php 
                if($bid>0) {
                        echo '<td class="bodytext" valign="bottom"><input type="text" class="input_box" size="20" maxlength="200" name="curr_basket_name" style="display:none;margin-right:4px;" value="'.htmlspecialchars($basket_name,ENT_QUOTES).'" /></td>';
                }
                ?>
                <td class="bodytext" valign="bottom"><select name="basketid" size="1" style="display:none;font-family: verdana, Helvetica, sans-serif;font-size: 11px;color: #000000;border: 1px #000000 solid;margin-right:4px;" onchange="checkNew();">
                <?php 
                echo $basket_action_text;
                ?>
                </select></td>
                <td class="bodytext" valign="bottom"><input type="text" class="input_box" size="20" maxlength="200" name="basket_name" style="display:none;margin-right:4px;" /></td>
                <td class="bodytext" valign="bottom"><input class="submitbutton" type="submit" name="basketbutton_submit" value="Submit" style="display:none;" onclick="return checkChex();" /></td>
        </tr>
        </table>
        <div>&nbsp;</div>
        <?php
} 
?>

<div class="clearfix"></div>
<div class="text-center">
    <?php if($search_num_of_rows > 0) { 
     $current_page= $jsonResponseAPIData['current_page'];
     $total_records = $search_num_of_rows;
     $total_pages = $jsonResponseAPIData['total_pages'];
     $sidNextArray=array();
     $sidPreviousArray=array();
     foreach($jsonResponseAPIData['pages'] as $resultFindSearchId){
         if ($next_page==$resultFindSearchId['pg_no']) {
             $sidNextArray['sid']=$resultFindSearchId['sid'];
         }
         if ($previous_page==$resultFindSearchId['pg_no']) {
             $sidPreviousArray['sid']=$resultFindSearchId['sid'];
         }
     }
     //print_r($sidArray);
     $nextsid='';
     if(!empty($sidNextArray)){
//         $nextsid=implode(",",$sidNextArray['sid']);
         $nextsid='"'.$sidNextArray['sid'][0].'",'.$sidNextArray['sid'][1];
         $nextsid = str_replace(  '\'', '&#x27;', $nextsid);
     }
    
     $firstsid='';
     if(!empty($sidPreviousArray)){
         $firstsid='"'.$sidPreviousArray['sid'][0].'",'.$sidPreviousArray['sid'][1];
         $firstsid = str_replace(  '\'', '&#x27;', $firstsid);
     }
     /*echo $firstsid."<br/>";
     echo $nextsid."<br/>";
     //die;
     echo "<pre>";
     print_r($jsonResponseAPIData['pages']);*/
    ?>
    <ul class="pagination "> 
        <?php if($current_page >=11){
            echo "<li><a href='?page_no=1$gets$pagesort&page_type=1'>First Page</a></li>";
            } ?>
        <li <?php if($current_page <= 1){ echo "class='disabled'"; } ?>>
            <a <?php if($current_page > 1){
            echo "href='?page_no=$previous_page$gets$pagesort&sid=$firstsid&page_type=2'";
            } ?>>Previous</a>
           <?php  //echo "<a href='?page_no=$previous_page$gets&sid=$firstsid&page_type=2'><< Previous</a>"; ?>
        </li>
        <?php foreach($jsonResponseAPIData['pages'] as $resultPagination){
            $pg_no=$resultPagination['pg_no'];
            $dct=$resultPagination['direction'];
            $sid=implode(",",$resultPagination['sid']);
            $sid='"'.$resultPagination['sid'][0].'",'.$resultPagination['sid'][1];
            $sid = str_replace(  '\'', '&#x27;', $sid);
                if ($current_page==$pg_no) {
                    //echo $current_page."######".$page_no;
                echo "<li class='active'><a>$current_page</a></li>";
                }else{
                 $page_type='&page_type=2';  
                if($pg_no==$total_pages){
                  $page_type='&page_type=2';
                }
                echo "<li><a href='?page_no=$pg_no$gets$pagesort&sid=$sid&dct=$dct$page_type'>$pg_no</a></li>";
                }
            }
        ?>
        <li <?php if($current_page >= $total_pages){
        echo "class='disabled'";
        } ?>>
        <a <?php if($current_page < $total_pages) {
            $page_type='&page_type=2';
            if($current_page == $total_pages){
                //$page_type='';
                $page_type='&page_type=2';
            }
        echo "href='?page_no=$next_page$gets$pagesort&sid=$nextsid$page_type'";
        } ?>>Next</a>
        </li>
        <?php if($total_pages >=11){
            //if($current_page < $total_pages){
        ?>
        <li <?php if($current_page >= $total_pages){
        echo "class='disabled'";
        } ?>>
        <?php echo "<a href='?page_no=$total_pages$gets$pagesort&page_type=3'>Last &rsaquo;&rsaquo;</a>"; ?>
        </li>
        <?php } ?>

          
   </ul>
    <?php } ?>
</div>
<div class="clearfix"></div>
<input type="hidden" name="ssid" value="<?php echo $ssid; ?>" />
<input type="hidden" name="bid" value="<?php echo $bid; ?>" />
<input type="hidden" name="page" value="<?php echo $page; ?>" />
<input type="hidden" name="sort" value="<?php echo $sort; ?>" />
<input type='hidden' name='save_json_query' value='<?php echo $posteddata; ?>'/>
</form>
<?php
echo $buttons;
$out = ob_get_contents();
ob_clean();
if($search_num_of_rows > 0) {
        echo '<div class="error" style="float:left;">'.$search_num_of_rows .' Result';
        if($search_num_of_rows!=1) echo 's';
        echo ' Found in ('.number_format((microtime(true) - $start_time),3).' Seconds)</div>';
}
echo $out;
if($search_num_of_rows>0){
    $_SESSION['total_searchedcount']    =   $search_num_of_rows;
}else{
    $_SESSION['total_searchedcount']    =   0; 
}

include 'footer_bottom.php';

function sortLinks($sel,$dis,$text){
        $gets = $GLOBALS['gets'];

        if($sel==$dis){
                echo '<a href="'.$_SERVER['PHP_SELF'].'?sort=-'.$dis.$gets.'" class="topLinks">'.$text.'<img src="images/down.gif" border="0" style="vertical-align:bottom;" width="15" height="15" /></a>';//&darr;
        }
        else{
                echo '<a href="'.$_SERVER['PHP_SELF'].'?sort='.$dis.$gets.'" class="topLinks">'.$text;
                if(abs($sel)==$dis){
                        echo '<img src="images/up.gif" border="0" style="vertical-align:bottom;" width="15" height="15" /></a>';//&uarr;
                }
                else {
                        echo '</a><img src="images/spacer.gif" border="0" style="vertical-align:bottom;" width="15" height="15" />';
                }
        }
}
function setWidthHeight($width, $height, $maxWidth, $maxHeight){
        $ret = array($width, $height);
        $ratio = $width / $height;
        if($width>$maxWidth || $height>$maxHeight){
                $ret[0] = $maxWidth;
                $ret[1] = ceil($ret[0] / $ratio);

                if($ret[1]>$maxHeight){
                        $ret[1] = $maxHeight;
                        $ret[0] = ceil($ret[1]*$ratio);
                }
        }
        return $ret;
}
$currenttime=time();
$filename_csv = 'Competiscan_Export_'.$currenttime.'_'.date('Y-m-d').'.csv';
?>
<style>
#overlay {
  background: #ffffff;
  color: #666666;
  position: fixed;
  height: 100%;
  width: 100%;
  z-index: 5000;
  top: 0;
  left: 0;
  float: left;
  text-align: center;
  padding-top: 25%;
  opacity: .80;
}

.spinner {
    margin: 0 auto;
    height: 64px;
    width: 64px;
    animation: rotate 0.8s infinite linear;
    /*border: 5px solid firebrick;*/
    border: 5px solid #00a4e4;
    border-right-color: transparent;
    border-radius: 50%;
}
@keyframes rotate {
    0% {
        transform: rotate(0deg);
    }
    100% {
        transform: rotate(360deg);
    }
}
</style>
<div id="overlay" style="display:none;">
    <div class="spinner"></div>
    <br/>
    <div class = "progressCounter"></div><br/>
    <div class = "progressCounter1"></div>
</div>
<script> 
$('.myExcel_download').click(function() {
    var source=<?php  echo $posteddata; ?> 
    var fileName="<?php echo $filename_csv; ?>";
    var startTime = new Date().getTime();
    var total_record="<?php echo $search_num_of_rows; ?>";
        $.ajax({          
        type: "POST",
        url: "express_download.php",
        data: {post_data:JSON.stringify(source),action:'express_download'},
        beforeSend: function () {
        $('#overlay').css("display", "block");
        },
        complete: function (resp) {
        //console.log(resp);
        let blob = new Blob([resp.responseText], { type: "text/csv" });
        var a = document.createElement('a');
        var url = window.URL.createObjectURL(blob);
        a.href = url;
        a.download = fileName;
        document.body.append(a);
        a.click();
        a.remove();
        window.URL.revokeObjectURL(url);
        $('#overlay').css("display", "none");
        },
    });
});
/*$('.myExcel_download').click(function() {  
    var source=<?php  echo $posteddata; ?> 
    var fileName="<?php echo $filename_csv; ?>";
    var startTime = new Date().getTime();
    var total_record="<?php echo $search_num_of_rows; ?>";
    var URL = "https://api1.competiscan.com/elasticsearch/v1/search/download";
    $.ajax({
    type: 'POST',
    data: JSON.stringify(source),
    url: URL,
    contentType: "application/json; charset=utf-8",
    cache: false,
    xhr:function() {
        var XHR = new window.XMLHttpRequest();
        XHR.addEventListener('loadstart', HandleXHR); // Upload has begun
        XHR.addEventListener('progress', HandleXHR); // Periodically indicates the amount of progress made so far
        XHR.addEventListener('error', HandleXHR); // Upload failed due to an error
        XHR.addEventListener('abort', HandleXHR); // Upload operation was aborted
        XHR.addEventListener('load', HandleXHR); // Upload completed successfully
        XHR.addEventListener('loadend', HandleXHR); // Upload finished
        function HandleXHR(event) {
           //console.log(event);
            //console.log((cnt/total_record)*100);
            //if(event.lengthComputable) {
                var duration = (new Date().getTime() - startTime) / 1000;
                //console.log(duration);
                var Bps = ((event.loaded)/ duration);
                var KBps = Bps / 1024;
               // console.log(KBps);
                this.transfer_rate= Math.floor(KBps)+'KB/s';
                //console.log(this.transfer_rate);
                var total=parseInt(total_record)*735;
                //console.log("total",total,typeof(total));
                var PercentProgress = Math.round((event.loaded * 100) / total) ;
               // console.log(PercentProgress);
                //$progress.value = percentage+'%';
                if (PercentProgress >70) {
                    PercentProgress=80; 
                    //console.log(PercentProgress);
                $('.progressCounter').text(PercentProgress+'%');
                $('.progressCounter1').text(this.transfer_rate);
                }if (PercentProgress>=80) {
                     PercentProgress=90; 
                $('.progressCounter').text(PercentProgress+'%');
                $('.progressCounter1').text(this.transfer_rate);
                }if (PercentProgress>=90) {
                    PercentProgress=100; 
                    console.log("Total percent",PercentProgress);
                $('.progressCounter').text(PercentProgress+'%');
                $('.progressCounter1').text(this.transfer_rate);
                }else{
                    PercentProgress=PercentProgress+'%';
                    console.log(" compelete",PercentProgress);
                    $('.progressCounter').text(PercentProgress);
                    $('.progressCounter1').text(this.transfer_rate);
                }
        }
        return XHR;
    
    },
    beforeSend: function () {
        $('#overlay').css("display", "block");
    },
    complete: function (resp) {
        //console.log(resp);
       let blob = new Blob([resp.responseText], { type: "text/csv" });
        var a = document.createElement('a');
        var url = window.URL.createObjectURL(blob);
        a.href = url;
        a.download = fileName;
        document.body.append(a);
        a.click();
        a.remove();
        window.URL.revokeObjectURL(url);
        $('#overlay').css("display", "none");
    },
    error: function(resp) {
        console.log(resp);
      },
});
});*/
function productDescription_digital_testing(ID,pdf_key) {
    //console.log(pdf_key,"###############################")
	var win = window.open('https://cp.competiscan.com/productdetail?id='+ID,"detail","toolbar=no, menubar=no, location=no, status=yes, scrollbars=yes, resizable=yes, width=700, height=600");
	win.focus();
}
</script>