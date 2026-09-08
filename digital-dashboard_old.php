<?php
$PAGE_HEADING = "Digital Dashboard";
$TITLE = "Competiscan $PAGE_HEADING";  
$HEAD = '<link rel="stylesheet" href="includes/jquery/jquery-ui.css" />
        <script type="text/javascript" src="includes/jquery/jquery.min.js">
        </script><script type="text/javascript" src="includes/jquery/jquery-ui.min.js"></script>
        <link rel="stylesheet" href="js_calendar/calendar.css" media="screen" type="text/css" />
        <script type="text/javascript" src="js_calendar/calendar.js"></script>';
include 'header_top.php';
require_once('includes/checklogin.php');
//require_once('includes/paginator.php');       //paginator class. 
//require_once('includes/paginator_html.php');  //paginator_html class.
require_once('includes/digital-dashboard-function.php');
if(!defined('ENV')){
    define('ENV',getenv('SERVER_NAME'));
}

$page_permission = getPagePermission();
if(!empty($_SESSION['sess_search_page_permission'])){
    $page_permission=$_SESSION['sess_search_page_permission'];
}
/*$redirect_page='';
if(!empty($page_permission)){
    if(!in_array('digital_dashboard',$page_permission) AND $redirect_page!=''){
       header("Location: $redirect_page");die; 
    }           
}*/
// page permission
if(!in_array('digital_dashboard',$page_permission)) {
    ob_end_clean();
    header("Location: fullsearch.php?searchview=2");
    exit;
}
//print_r($_SESSION['digital_data_keyword_search']);
// echo "<pre>";
// print_r($_REQUEST);
//print_r($_SESSION);
//echo "</pre>";
/*
 Curl callAPI()
*/
function callAPI($method, $url, $data){
    $curl = curl_init();
    switch ($method){
       case "POST":
          curl_setopt($curl, CURLOPT_POST, 1);
          if ($data)
             curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
          break;
       case "PUT":
          curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
          if ($data)
             curl_setopt($curl, CURLOPT_POSTFIELDS, $data);			 					
          break;
       default:
          if ($data)
             $url = sprintf("%s?%s", $url, http_build_query($data));
    }
    // OPTIONS:
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
       'APIKEY: 111111111111111111111',
       'Content-Type: application/json',
    ));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    // EXECUTE:
    $result = curl_exec($curl);
    if(!$result){die("Connection Failure");}
    curl_close($curl);
    return $result;
 } 
 function getSector1() {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
	$APISECTORURL=RPVAPIURL.'data/sectors';
	$get_data = callAPI('GET', $APISECTORURL, false);
	$response = json_decode($get_data, true);
	$rows_sector_data=$response['data'];

    $arr = array();
	foreach($rows_sector_data['Sectordetails'] as $row_sector){
		$sectorID = $row_sector['sectorID'];
		$sectorName = $row_sector['sectorName'];
		$arr[$sectorID] = $sectorName;

	}
    return $arr;
}

function getCategory1($sectorID) {
    $arr = array();
	$arrayValue[] = $sectorID;
	$postSector['sectors']=$arrayValue;
	$posted_data=json_encode($postSector);
	$APISECTORURL=RPVAPIURL.'data/sectors';
	if(!empty($posted_data)){
	$getSector_data = callAPI('POST', $APISECTORURL, $posted_data);
	$response_category = json_decode($getSector_data, true);
	if(!empty($response_category)){
		foreach($response_category as $row_category){
			$sectorID = $row_category['sectorID'];
			$sectorName = $row_category['sectorName'];
			$arr[$sectorID] = $sectorName;
	
		}
		return $arr;
	}} else {
        return 0;
    }
}


function getSubCategory1($categoryID) {
    $arr = array();
	$arrayValue[] = $categoryID;
	$postSector['sectors']=$arrayValue;
	$posted_data=json_encode($postSector);
	$APISECTORURL=RPVAPIURL.'data/sectors';
	if(!empty($posted_data)){
	$getSector_data = callAPI('POST', $APISECTORURL, $posted_data);
	$response_category = json_decode($getSector_data, true);
	if(!empty($response_category)){
		foreach($response_category as $row_category){
			$sectorID = $row_category['sectorID'];
			$sectorName = $row_category['sectorName'];
			$arr[$sectorID] = $sectorName;
	
		}
		return $arr;
	}} else {
        return 0;
    }
}
$digital_data_keyword_search="";
$company="";
$wheresearchtitle='';
$digitalidsarray=array();
//$company=array();
$digital_source=array();
$mchannel=array();
$sector_list_array=array();
$category_list_array=array();
$subcategory_list_array=array();
$media_channel="";
$country='';
$state =array();
//$state_code="";
$city=array();
$publisher="";
$advertiser_domain="";
$title="";
$from_date="";
$to_date="";
$pagelimit = 20;
//$orderBy=" ORDER BY id DESC";
$orderBy=" GROUP BY pr.id ORDER BY pr.id DESC";
//$sdate=date("2021-01-01");
$sdate=date("Y-m-01");
$cdate=date("Y-m-d");
//$cdate=date("2021-01-31");
$where="";
if(isset($_GET['p'])) {
	$p = (int)$_GET['p'];
}
else {
	$p = 0;
}

if(isset($_REQUEST['clear_search']) and $_REQUEST['clear_search']=='Clear Search') {
	    $_REQUEST['company']="";
        $_REQUEST['mchannel']="";
        $_REQUEST['digital_source']="";
        $_REQUEST['country']="";
        $_REQUEST['state']="";
        $_REQUEST['city']="";
        $_REQUEST['export_csv']="";
        $_REQUEST['from_date']="";
        $_REQUEST['to_date']="";
        //$sdate=date("2021-01-01");
        //$cdate=date("2021-01-31");
        $sdate=date("Y-m-01");
        $cdate=date("Y-m-d");
        $_SESSION['company']=array();
        $_SESSION['mchannel']=array();
        $_SESSION['digital_source']=array();
        $_SESSION['country']='';
        $_SESSION['state']=array();
        $_SESSION['city']=array();
        $_REQUEST['digital_data_keyword_search']='';
        $_SESSION['digital_data_keyword_search']='';
        $_REQUEST['sector_list']="";
        $_SESSION['sector_list']="";
        $_REQUEST['category_list']="";
        $_SESSION['category_list']="";
        $_REQUEST['subcategory_list']="";
        $_SESSION['subcategory_list']="";
        
}

if(!isset($_SESSION['digital_data_keyword_search']) || !isset($_SESSION['company']) ||!isset($_SESSION['mchannel']) || !isset($_SESSION['digital_source']) || !isset($_SESSION['country']) || !isset($_SESSION['state']) || !isset($_SESSION['city'])){
        $_SESSION['company']=array();
        $_SESSION['mchannel']=array();
        $_SESSION['digital_source']=array();
        $_SESSION['country']='';
        $_SESSION['state']=array();
        $_SESSION['city']=array();
        $_SESSION['digital_data_keyword_search']='';
}

if(isset($_REQUEST['from_date']) AND $_REQUEST['from_date']!="") {
    $from_date = $_REQUEST['from_date'];
    $where.=" Where creation_date >='".$from_date."'";
}else{
     $where.=" Where creation_date >='".$sdate."'";
}
if(isset($_REQUEST['to_date']) AND $_REQUEST['to_date']!="") {
    $to_date =$_REQUEST['to_date'];
    $where.=" AND creation_date <='".$to_date."'";
}else{
    $where.=" AND creation_date <='".$cdate."'";
}


if(isset($_REQUEST['company']) AND $_REQUEST['company']!=''){
    $company = $_SESSION['company']=trim($_REQUEST['company']);
    if(!empty($company)){
        $expCompArray=explode(" or ",$company);
        $cmpidArray=array();
        for($cm=0;$cm<count($expCompArray); $cm++){
            $sql_comp = "select companyID,companyName from cscan_company Where companyName =".$expCompArray[$cm];
            $result_comp = $DRW->query($sql_comp, $DRW_read); 
            $row_company = $DRW->fetch_assoc($result_comp);
            $comp_id=$row_company['companyID'];
            $cmpidArray[]=$comp_id;
            
        }
    }
    if(!empty($cmpidArray[0]) && !empty($cmpidArray)){
    $company_id = @implode(",",$cmpidArray);
    $where.=" AND company_id In ($company_id)";  
    }   
}elseif(isset($_SESSION['company']) AND $_SESSION['company']!="" AND isset($_REQUEST['search_button'])!='Search'){
//elseif(!empty($_SESSION['company'])){
    $company = $_SESSION['company'];
    if(!empty($company)){
        $expCompArray=explode(" or ",$company);
        $cmpidArray=array();
        for($cm=0;$cm<count($expCompArray); $cm++){
            $sql_comp = "select companyID,companyName from cscan_company Where companyName =".$expCompArray[$cm];
            $result_comp = $DRW->query($sql_comp, $DRW_read); 
            $row_company = $DRW->fetch_assoc($result_comp);
            $comp_id=$row_company['companyID'];
            $cmpidArray[]=$comp_id;
            
        }
    }
    if(!empty($cmpidArray[0]) && !empty($cmpidArray)){
    $company_id = @implode(",",$cmpidArray);
    $where.=" AND company_id In ($company_id)";  
    } 
}else{ 
    $company = '';
    $_SESSION['company']="";
}

$whereAndQueryMChannel="";
if(isset($_REQUEST['mchannel']) AND !empty($_REQUEST['mchannel'])) {
    if($_REQUEST['mchannel'][0]!=''){
      $mchannel = $_SESSION['mchannel']=$_REQUEST['mchannel'];
      $mchannel_id = @implode(",",$_REQUEST['mchannel']);
      $whereAndQueryMChannel.=" AND mchannel_id In ($mchannel_id)";
    }else{
       $mchannel = $_SESSION['mchannel']=$_REQUEST['mchannel']; 
       unset($mchannel[0]);
       if(!empty($mchannel)){
       $mchannel_id = @implode(",",$mchannel);
       $whereAndQueryMChannel.=" AND mchannel_id In ($mchannel_id)";
       }
    }
}else if(!empty($_SESSION['mchannel'])){
       if($_SESSION['mchannel']['0']!=""){
        $mchannel=$_SESSION['mchannel'];
        $mchannel_id = @implode(",",$_SESSION['mchannel']);
        $whereAndQueryMChannel.=" AND mchannel_id In ($mchannel_id)";
       }
}

$whereAndQueryDigitalSource="";
if(isset($_REQUEST['digital_source']) AND !empty($_REQUEST['digital_source'])) {
    if($_REQUEST['digital_source'][0]!=''){
      $digital_source =$_SESSION['digital_source']= $_REQUEST['digital_source'];
      $dsid = @implode(",",$_REQUEST['digital_source']);
      $whereAndQueryDigitalSource.=" AND digital_source In ($dsid)";
    }else{
       $digital_source = $_SESSION['digital_source']=$_REQUEST['digital_source']; 
       unset($digital_source[0]);
       if(!empty($digital_source)){
       $dsid = @implode(",",$digital_source);
       $whereAndQueryDigitalSource.=" AND digital_source In ($dsid)";
       }
    }
}else if(!empty($_SESSION['digital_source'])){
       if($_SESSION['digital_source'][0]!=""){
        $digital_source=$_SESSION['digital_source'];
        $dsid = @implode(",",$_SESSION['digital_source']);
        $whereAndQueryDigitalSource.=" AND digital_source In ($dsid)";
       }
}
/*############ START SECTOR LIST################*/
$whereAndQuerySector="";
if(isset($_REQUEST['sector_list']) AND $_REQUEST['sector_list']!="" AND !empty($_REQUEST['sector_list'])){
    $sector_list_array=$_SESSION['sector_list']=$_REQUEST['sector_list'];
    if($sector_list_array[0]=='0'){
        unset($sector_list_array[0]);
        if(!empty($sector_list_array)){
        $sectorID=implode(',',$sector_list_array);
        $whereAndQuerySector.=" AND sector_id in($sectorID)";
        }
    }
    else{
        $sectorID=implode(',',$sector_list_array);
        $whereAndQuerySector.=" AND sector_id in($sectorID)";
    }
    
}elseif(isset($_SESSION['sector_list']) AND $_SESSION['sector_list']!="" AND isset($_REQUEST['search_button'])!='Search'){
    $sector_list_array=$_SESSION['sector_list'];
    if($sector_list_array[0]=='0'){
        unset($sector_list_array[0]);
        if(!empty($sector_list_array)){
            $sectorID=implode(',',$sector_list_array);
            $whereAndQuerySector.=" AND sector_id in($sectorID)";
        }
    }else{
        $sectorID=implode(',',$sector_list_array);
        $whereAndQuerySector.=" AND sector_id in($sectorID)";
    }
    
}else{
    $sector_list_array=array();
    $_SESSION['sector_list']="";
}
/*############ Category LIST################*/
$whereAndQuerySectorCat="";
if(isset($_REQUEST['category_list']) AND $_REQUEST['category_list']!="" AND !empty($_REQUEST['category_list'])){
    $category_list_array=$_SESSION['category_list']=$_REQUEST['category_list'];
    if($category_list_array[0]=='0'){
        unset($category_list_array[0]);
        if(!empty($category_list_array)){
        $categoryID=implode(',',$category_list_array);
        $whereAndQuerySectorCat.=" AND category_id in($categoryID)";
        }
    }
    else{
        $categoryID=implode(',',$category_list_array);
        $whereAndQuerySectorCat.=" AND category_id in($categoryID)";
    }
    
}elseif(isset($_SESSION['category_list']) AND $_SESSION['category_list']!="" AND isset($_REQUEST['search_button'])!='Search'){
    $category_list_array=$_SESSION['category_list'];
    if($category_list_array[0]=='0'){
        unset($category_list_array[0]);
        if(!empty($category_list_array)){
            $categoryID=implode(',',$category_list_array);
            $whereAndQuerySectorCat.=" AND category_id in($categoryID)";
        }
    }else{
        $categoryID=implode(',',$category_list_array);
        $whereAndQuerySectorCat.=" AND category_id in($categoryID)";
    }
    
}else{
    $category_list_array=array();
    $_SESSION['category_list']="";
}
/*############ SUBCategory LIST################*/
$whereAndQuerySectorCatSubCat="";
if(isset($_REQUEST['subcategory_list']) AND $_REQUEST['subcategory_list']!="" AND !empty($_REQUEST['subcategory_list'])){
    $subcategory_list_array=$_SESSION['subcategory_list']=$_REQUEST['subcategory_list'];
    if($subcategory_list_array[0]=='0'){
        unset($subcategory_list_array[0]);
        if(!empty($subcategory_list_array)){
        $subcategoryId=implode(',',$subcategory_list_array);
        $whereAndQuerySectorCatSubCat.=" AND subcategory_id in($subcategoryId)";
        }
    }
    else{
        $subcategoryId=implode(',',$subcategory_list_array);
        $whereAndQuerySectorCatSubCat.=" AND subcategory_id in($subcategoryId)";
    }
    
}elseif(isset($_SESSION['subcategory_list']) AND $_SESSION['subcategory_list']!="" AND isset($_REQUEST['search_button'])!='Search'){
    $subcategory_list_array=$_SESSION['subcategory_list'];
    if($subcategory_list_array[0]=='0'){
        unset($subcategory_list_array[0]);
        if(!empty($subcategory_list_array)){
            $subcategoryId=implode(',',$subcategory_list_array);
            $whereAndQuerySectorCatSubCat.=" AND subcategory_id in($subcategoryId)";
        }
    }else{
        $subcategoryId=implode(',',$subcategory_list_array);
        $whereAndQuerySectorCatSubCat.=" AND subcategory_id in($subcategoryId)";
    }
    
}else{
    $subcategory_list_array=array();
    $_SESSION['subcategory_list']="";
}
#######################END SUBCATEGORY LIST############################
if(isset($_REQUEST['country'])){
    $country=$_SESSION['country']=$_REQUEST['country'];
}elseif($_SESSION['country']!=''){
    $country=$_SESSION['country'];
}
$whereAndQueryLocation="";
if(isset($_REQUEST['state']) AND !empty($_REQUEST['state'])) {
    if($_REQUEST['state'][0]!=''){
      $state = $_SESSION['state']=$_REQUEST['state'];
      $state_code = @implode ("', '", $state );
      $whereAndQueryLocation.=" AND location_state_code In ('".$state_code."')";
    }else{
       $state = $_SESSION['state']=$_REQUEST['state']; 
       unset($state[0]);
       if(!empty($state)){
       $state_code = @implode ("', '", $state);
       $whereAndQueryLocation.=" AND location_state_code In ('".$state_code."')";
       }
    }
}else if(!empty($_SESSION['state'])){
       if($_SESSION['state']['0']!=""){
        $state = $_SESSION['state'];
       $state_code = @implode ("', '", $_SESSION['state']);
       $whereAndQueryLocation.=" AND location_state_code In ('".$state_code."')";
       }
}
$whereAndQueryCity="";
if(isset($_REQUEST['city']) AND !empty($_REQUEST['state'])){
    if($_REQUEST['city'][0]!=''){
      $city =$_SESSION['city']=$_REQUEST['city'];
      $city_name = @implode ("|", $city);
      $whereAndQueryLocation.=" AND location REGEXP '".$city_name."'";
    }else{
       $city = $_SESSION['city']=$_REQUEST['city']; 
       unset($city[0]);
       if(!empty($city)){
       $city_name = @implode ("|", $city);
       $whereAndQueryLocation.=" AND location REGEXP '".$city_name."'";
       }
    }
}else if(!empty($_SESSION['city'])){
       if($_SESSION['city']['0']!=""){
        $city=$_SESSION['city'];
        $city_name = @implode ("|", $city);
        $whereAndQueryLocation.=" AND location REGEXP '".$city_name."'";
       }
}

$whereAndQueryCountry="";
if($whereAndQueryLocation=="" AND $country!=''){
    
     if($country=='usa'){
        $country_filter='United States'; 
     }
     if($country=='canada'){
        $country_filter='canada'; 
     }
    $whereAndQueryCountry.=" AND LOWER(location) = '".strtolower($country_filter)."'";
   
}
if (isset($_REQUEST['digital_data_keyword_search']) and $_REQUEST['digital_data_keyword_search']!='') {
    $digital_data_keyword_search= $_SESSION['digital_data_keyword_search'] = trim($_REQUEST['digital_data_keyword_search']);
}else if(isset($_SESSION['digital_data_keyword_search']) AND $_SESSION['digital_data_keyword_search']!="" AND isset($_REQUEST['search_button'])!='Search'){
    $digital_data_keyword_search=$_SESSION['digital_data_keyword_search'];  
}else{
    $digital_data_keyword_search=$_SESSION['digital_data_keyword_search']='';
}
######################### Start OCR Search########################
if ($_SESSION['digital_data_keyword_search'] != '' ) {
    $searchKey = $_SESSION['digital_data_keyword_search'];
    //$display_search_key.= " ".$searchKey;
    $search_id = session_id();
    if (!empty($SPHINX_name)) {
        $s = startSphinx();
        $inds = 'base_index_biscience_digitalreport';
        
        $ps = parseSphinx($s, $searchKey);
        if (trim($ps) != '') {
            $currcount = 0;
            $step = $total = 50000;
            $s->setLimits(0, 1, 1);
            $result = $s->query($ps, $inds);
            //echo "<pre>";
            //print_r($result['matches']);
            if (!empty($result['matches'])) {
                $total = (float) $result['total_found']; 
                $count = 0;
                $minID = 0;
                $count_save_sql = "SELECT MAX(id) FROM cscan_digital_processed_title";
                $rs = $DRW->query($count_save_sql, $DRW_biscience_digital);
                $data = $DRW->fetch_row($rs);
                $maxID = $data[0];
                  $DRW->query('START TRANSACTION', $DRW_biscience_digital); 
                for ($offset = 0; $offset <= $maxID; $offset += $step) {
                    $s = startSphinx();
                    $s->setLimits(0, $step, $step);
                    $s->setIDRange($minID + 1, $maxID);
                    $result = $s->query($ps, $inds);
                   if (isset($result['matches'])) {
                        foreach ($result['matches'] as $dts_id => $match) {
                            $minID = $dts_id;
                            $currcount++;

                             $digitalidsarray[] =   $match['attrs']['processed_record_id'];


                        }
                        if ($currcount >= $total) {
                            break;
                        }
                    }
                    $err = $s->getLastError();
                    $war = $s->getLastWarning();
                    if (!empty($err) || !empty($war)) {
                        //echo "$err | $war"; exit;
                        break;
                    }
                }

                 $DRW->query('COMMIT', $DRW_biscience_digital); 
            }

               if ($search_id != '') {
                     $digitalidsarray   =  array_unique($digitalidsarray);
                    if (!empty($digitalidsarray)) {
                       /* $andUnion = '';
                        $chunkdata=10000;
                        if($total>600000){
                                $chunkdata=50000;
                        }
                        $newarray = array_chunk($digitalidsarray, $chunkdata);
                        for ($u = 2; $u < 100; $u++) {
                            if (count($newarray) >= $u) {

                                $andUnion.=" union ( SELECT tr.trend_id   FROM  cscan_digital_processed_records dpr  WHERE dpr.trend_id IN(" . implode(',', ($newarray[$u - 1])) . "))";
                            }else{
                                continue;
                            }
                        }*/

                        $wheresearchtitle = " AND pr.id in (" .implode(',',$digitalidsarray) . ") ";
                    }
                    if (empty($digitalidsarray)) {
                        $andcond = '-1';
                        $wheresearchtitle = " AND pr.id in (" . $andcond . ") ";
                    }
                }

        }
    }
}
######################### End OCR Search########################


if (!empty($_REQUEST['export_csv']) && trim($_REQUEST['export_csv']) == 'Export To CSV') {//pr($_POST);die;
    $arrExport = array();
    //$exp_sql="select pr.id,pr.digital_record_id,file_id,spend,impressions,company_id,advertiser_name,advertiser_domain,monitored_page,campaign_landing_page,creative_wrapper,creation_date,location_state_code,(CASE WHEN pmc.mchannel_id = 5 THEN 'Online Display' WHEN pmc.mchannel_id = 10 THEN 'Online Video' END) AS 'media_channel',GROUP_CONCAT(DISTINCT CONCAT(CASE ps.digital_source WHEN 1 THEN 'Desktop' WHEN 2 THEN 'Mobile' WHEN 3 THEN 'In App Android' WHEN 4 THEN 'In App Ios' WHEN 5 THEN 'Social' END) SEPARATOR '; ') AS 'digital_source' from cscan_digital_processed_records pr LEFT JOIN cscan_digital_processed_location pdl ON (pr.id=pdl.processed_record_id) LEFT JOIN cscan_digital_processed_mchannel pmc ON (pmc.processed_record_id = pr.id) LEFT JOIN cscan_digital_processed_source ps ON (ps.processed_record_id = pr.id) $where$wheresearchtitle$whereAndQueryLocation$whereAndQueryDigitalSource$whereAndQueryMChannel$whereAndQueryCity$whereAndQueryCountry$orderBy";
    //$exp_sql="select pr.id,pr.digital_record_id,file_id,spend,impressions,company_id,advertiser_name,advertiser_domain,monitored_page,campaign_landing_page,creative_wrapper,creation_date,location_state_code from cscan_digital_processed_records pr LEFT JOIN cscan_digital_processed_location pdl ON (pr.id=pdl.processed_record_id) $where$wheresearchtitle$whereAndQueryLocation$whereAndQueryCity$whereAndQueryCountry$orderBy";
    $exp_sql="select pr.id,pr.digital_record_id,file_id,spend,impressions,company_id,advertiser_name,advertiser_domain,monitored_page,campaign_landing_page,creative_wrapper,creation_date,location_state_code,(CASE WHEN pmc.mchannel_id = 5 THEN 'Online Display' WHEN pmc.mchannel_id = 10 THEN 'Online Video' END) AS 'media_channel',GROUP_CONCAT(DISTINCT CONCAT(CASE ps.digital_source WHEN 1 THEN 'Desktop' WHEN 2 THEN 'Mobile' WHEN 3 THEN 'In App Android' WHEN 4 THEN 'In App Ios' WHEN 5 THEN 'Social' END) SEPARATOR '; ') AS 'digital_source',sector_id,category_id,subcategory_id,subsubcategory_id from cscan_digital_processed_records pr LEFT JOIN cscan_digital_processed_location pdl ON (pr.id=pdl.processed_record_id) LEFT JOIN cscan_digital_processed_mchannel pmc ON (pmc.processed_record_id = pr.id) LEFT JOIN cscan_digital_processed_source ps ON (ps.processed_record_id = pr.id) LEFT JOIN
    cscan_biscience_sector_mapping bsm ON (bsm.processed_record_id = pr.id) $where$wheresearchtitle$whereAndQueryLocation$whereAndQueryDigitalSource$whereAndQueryMChannel$whereAndQueryCity$whereAndQueryCountry$whereAndQuerySector$whereAndQuerySectorCat$whereAndQuerySectorCatSubCat$orderBy";
    $exp_rs = $DRW->query($exp_sql, $DRW_biscience_digital);
    if (!empty($exp_rs)) {
       $arrExport['data'][] = array("Company", "Media Channel", "Date", "Country","State","City",
            "Headline", "Campaign Landing Page", "Creative", "Publisher", "Digital Source","Sector","Category","SubCategory","Spend","Impressions");
        while ($exp_row = $DRW->fetch_assoc($exp_rs)) {
                $id=$exp_row['id'];
                $process_record_id=$exp_row['digital_record_id'];
                $advertiser_domain=$exp_row['advertiser_domain'];
                $spend=$exp_row['spend'];
                $impressions=$exp_row['impressions'];
                $digital_source=$exp_row['digital_source'];
                $media_channel=$exp_row['media_channel'];
                $creation_date1=$exp_row['creation_date'];
                if($creation_date1!='0000-00-00' && $creation_date1!=''){
                    $creation_date= date('Y-m-d',strtotime($creation_date1)); 
                    
                }
                $searchVal= array("https://biscience.s3.amazonaws.com", "http://biscience.s3.amazonaws.com","https://ads.adclarity.com","http://ads.adclarity.com");
                $creative_wrapper=str_replace($searchVal,"https://files2.competiscan.com",$exp_row['creative_wrapper']);
                $campaign_landing_page=$exp_row['campaign_landing_page'];
                $sql_company = "select companyName from cscan_company Where companyID='".$exp_row['company_id']."'";
                $result_comp = $DRW->query($sql_company, $DRW_read); 
                $row_company = $DRW->fetch_assoc($result_comp);
                $company1=$row_company['companyName'];
                 //Media channel
                /*$sql_mchannel = "select mchannel_id from cscan_digital_processed_mchannel Where processed_record_id='".$id."'$whereAndQueryMChannel";
                $result_mchannel = $DRW->query($sql_mchannel, $DRW_biscience_digital); 
                $mchannel_name=array();
                while($row_mchannel = $DRW->fetch_assoc($result_mchannel)){
                    $mchannel_name[]=mediaChannelName($row_mchannel['mchannel_id']);
                }
                if(!empty($mchannel_name)){
                $media_channel=implode("; ",$mchannel_name);
                 // echo $media_channel;
                }*/
                //location
                $sql_location = "select location,location_state_code from cscan_digital_processed_location Where processed_record_id='".$id."'$whereAndQueryLocation$whereAndQueryCity";
                $result_location = $DRW->query($sql_location, $DRW_biscience_digital); 
                $city_data=array();
                $state_data=array();
                $country_data=array();
                while($row_location = $DRW->fetch_assoc($result_location)){
                    $location_name=explode(",",$row_location['location']);
                    $city_name=trim($location_name[0]);
                    $state_code=trim($row_location['location_state_code']);
                    
                     if($city_name!='' AND $state_code!=''){
                        $sql_q = "select DISTINCT city,state_province,country from cscan_digital_city_state where city='".$city_name."' AND state_code='".$state_code."'";
                        $res_query = $DRW->query($sql_q, $DRW_biscience_digital);
                        if ($DRW->num_rows($res_query) > 0) {
                        $row_loc_data = $DRW->fetch_assoc($res_query);
                        $city_data[]=$row_loc_data['city'];
                        $state_data[]=$row_loc_data['state_province'];
                        $country_data[]=$row_loc_data['country'];
                        }else{
                        $countrydata='';
                        $sql_country = "select DISTINCT country from cscan_digital_city_state where state_code='".$state_code."' limit 1";
                        $res_query_country = $DRW->query($sql_country, $DRW_biscience_digital);
                        if ($DRW->num_rows($res_query_country) > 0) {
                        $row_country_data = $DRW->fetch_assoc($res_query_country);
                        $country_data[]=$row_country_data['country'];
                        }
                        if($exp_row['location_state_code']=='United States'){
                            $city_data[]=$exp_row['location_state_code'];
                            $state_data[]=$exp_row['location_state_code'];
                            $countrydata='usa';
                        }
                        if($exp_row['location_state_code']=='Canada'){
                            $city_data[]=$exp_row['location_state_code'];
                            $state_data[]=$exp_row['location_state_code'];
                            $countrydata='canada';
                        }
                        $country_data[]=$countrydata;
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
                  $country_name = implode("; ",array_unique($country_data)); 
                  $country_name=trim($country_name,'; ');
                }
                
                //publisher
                $sql_publisher = "select publisher from cscan_digital_processed_publisher Where processed_record_id='".$id."'";
                $result_publisher = $DRW->query($sql_publisher, $DRW_biscience_digital); 
                $publisher_name=array();
                while($row_publisher = $DRW->fetch_assoc($result_publisher)){
                    $publisher_name[]=$row_publisher['publisher'];
                }
                if(!empty($publisher_name)){
                $publisher=implode("; ",$publisher_name);

                }
                 //title
                $sql_title = "select compaign_title from cscan_digital_processed_title Where processed_record_id='".$id."'";
                $result_title = $DRW->query($sql_title, $DRW_biscience_digital); 
                $title_array=array();
                while($row_title = $DRW->fetch_assoc($result_title)){
                    $title_array[]=$row_title['compaign_title'];
                }
                if(!empty($title_array)){
                $title=implode("; ",$title_array);

                }

                $sectorName="";
                if($exp_row['sector_id']!=""){
                    $sectorName=sectorName($exp_row['sector_id']);

                }
                $categoryName="";
                if($exp_row['category_id']!=""){
                    $categoryName=categoryName($exp_row['category_id']);

                }
                $subCategoryName="";
                if($exp_row['subcategory_id']!=""){
                    $subCategoryName=subCategoryName($exp_row['subcategory_id']);

                }
                //digital source
                /*$sql_digital_source = "select digital_source from cscan_digital_processed_source Where processed_record_id='".$id."'$whereAndQueryDigitalSource";
                $result_digital_source = $DRW->query($sql_digital_source, $DRW_biscience_digital); 
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

                }else{
                  $digital_source='NA';  
                }*/
                $arrExport['data'][] = array($company1, $media_channel,$creation_date,$country_name,$state_name,$city_name,$title,
                $campaign_landing_page, $creative_wrapper, $publisher,
                $digital_source, $sectorName,$categoryName,$subCategoryName, $spend, $impressions,
            );
        }
    }
//    echo "<pre>";
//    print_r($arrExport);
//    echo "</pre>"; die;
    ob_get_clean();
    //download_send_headers("digital_dashboard_report_" . date("Y-m-d") . ".csv");
    echo array2csv($arrExport);die;
    //echo "done";
}
$sql = "select pr.id,pr.digital_record_id,file_id,spend,impressions,company_id,advertiser_name,advertiser_domain,monitored_page,campaign_landing_page,creative_wrapper,creation_date,location_state_code,(CASE WHEN pmc.mchannel_id = 5 THEN 'Online Display' WHEN pmc.mchannel_id = 10 THEN 'Online Video' END) AS 'media_channel',GROUP_CONCAT(DISTINCT CONCAT(CASE ps.digital_source WHEN 1 THEN 'Desktop' WHEN 2 THEN 'Mobile' WHEN 3 THEN 'In App Android' WHEN 4 THEN 'In App Ios' WHEN 5 THEN 'Social' END) SEPARATOR '; ') AS 'digital_source',sector_id,category_id,subcategory_id,subsubcategory_id from cscan_digital_processed_records pr LEFT JOIN cscan_digital_processed_location pdl ON (pr.id=pdl.processed_record_id) LEFT JOIN cscan_digital_processed_mchannel pmc ON (pmc.processed_record_id = pr.id) LEFT JOIN cscan_digital_processed_source ps ON (ps.processed_record_id = pr.id) LEFT JOIN
cscan_biscience_sector_mapping bsm ON (bsm.processed_record_id = pr.id) $where$wheresearchtitle$whereAndQueryLocation$whereAndQueryDigitalSource$whereAndQueryMChannel$whereAndQueryCity$whereAndQueryCountry$whereAndQuerySector$whereAndQuerySectorCat$whereAndQuerySectorCatSubCat$orderBy LIMIT $p, $pagelimit";
$sqlcount = "select pr.id  from cscan_digital_processed_records pr LEFT JOIN cscan_digital_processed_location pdl ON (pr.id=pdl.processed_record_id) LEFT JOIN cscan_digital_processed_mchannel pmc ON (pmc.processed_record_id = pr.id) LEFT JOIN cscan_digital_processed_source ps ON (ps.processed_record_id = pr.id) LEFT JOIN
cscan_biscience_sector_mapping bsm ON (bsm.processed_record_id = pr.id) $where$wheresearchtitle$whereAndQueryLocation$whereAndQueryDigitalSource$whereAndQueryMChannel$whereAndQueryCity$whereAndQueryCountry$whereAndQuerySector$whereAndQuerySectorCat$whereAndQuerySectorCatSubCat$orderBy";
$result = $DRW->query($sql, $DRW_biscience_digital);
$numquery = $DRW->query($sqlcount, $DRW_biscience_digital); 
$nrow=$DRW->num_rows($numquery);
$num_of_rows=$nrow;
/*$sql = "select id,digital_record_id,file_id,spend,impressions,company_id,advertiser_name,advertiser_domain,monitored_page,campaign_landing_page,creative_wrapper,creation_date from cscan_digital_processed_records $where$wheresearchtitle$orderBy LIMIT $p, $pagelimit";
$sqlcount = "select count(id) as cnt from cscan_digital_processed_records $where$wheresearchtitle";
$result = $DRW->query($sql, $DRW_biscience_digital);
$numquery = $DRW->query($sqlcount, $DRW_biscience_digital); 
$nrow = $DRW->fetch_row($numquery);
$num_of_rows = $nrow[0];*/
?>
<div id="page">
    <div>
        <form name="dashboardForm" id="dashboardForm" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <div class="row">
                <div class="col-md-4">
                    <div class="bodytext">
                    <div>
                        <strong>Company</strong>
                    </div>
                    <div>
                    <div style="float:left;padding:0px; margin-bottom:30px;">
                        <div id="cotext">
                            <input type="text" name="company" size="45" class="input_box" value="<?php if($company!="" && !empty($company) ){ echo htmlspecialchars($company, ENT_QUOTES);} ?>" onchange="checkLookup('clist');" tabindex="1">
                        <br>
                        [<a href="#" onclick="showLook('seltext','showhide','clist',document.forms.dashboardForm.company); return false;" id="showhide" class="HyperLink">Show Lookup</a>]
                        </div>
                        <div id="seltext" style="border: 1px solid rgb(0, 0, 0); padding: 4px; display:none; float: left; background-color: rgb(232, 232, 255);">
                        <iframe name="clist" src="digital_company_iframe.php?parent_field=company" width="278" height="80" frameborder="0" marginheight="0" marginwidth="0" scrolling="auto"></iframe>
                        </div>
                    </div>
                        
                    </div>
                    <div>&nbsp;</div>  
                </div>
                </div>
                <div class="col-md-4">
                    <div class="bodytext">
                    <div><strong>Media Channel</strong></div>
                    <div>
                        <select name="mchannel[]" id="mchannel" multiple="multiple" size="3" class="combo_box" tabindex="2">
                            <?php 
                            $o_count = count($mchannel);
                            $selectany='';
                            if ($o_count == 0) {
                                $selectany='selected=selected';
                            }
                            
                            ?>
                            <option value="" <?php echo $selectany; ?>>Any</option>
                            <?php 
                            $channelArray=array('5'=>'Online Display','10'=>'Online Video');
                            foreach($channelArray as $mid=>$mname ) {
                            ?>
                            <option <?php if(in_array($mid,$_SESSION['mchannel'])) { echo "selected"; } ?> value="<?php echo $mid; ?>" ><?php echo $mname; ?></option>
                            <?php } ?>
                            
                        </select>
                    </div>
                    <div>&nbsp;</div>
                </div>
                </div>
                <div class="col-md-4">
                    <div class="bodytext">
                    <div><strong>Digital Source</strong></div>
                    <div>
                        <select name="digital_source[]" id="digital_source" multiple="multiple" size="3" class="combo_box" tabindex="3">
                            <?php 
                            $o_count = count($digital_source);
                            $selectany='';
                            if ($o_count == 0) {
                                $selectany='selected=selected';
                            }
                            
                            ?>
                            <option value="" <?php echo $selectany; ?>>Any</option>
                            <?php 
                            $digitalsourcechannelArray=array('1'=>'Desktop','2'=>'Mobile','3'=>'In App Android','4'=>'In App Ios','5'=>'Social');
                            foreach($digitalsourcechannelArray as $dsid=>$dsname ) {
                            ?>
                            <option <?php if(in_array($dsid,$_SESSION['digital_source'])) { echo "selected"; } ?> value="<?php echo $dsid; ?>" ><?php echo $dsname; ?></option>
                            <?php } ?>
                            
                        </select>
                    </div>
                    <div>&nbsp;</div>
                </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="bodytext">
                    <div><strong>Country</strong>
                    </div>
                    <div>
                        <select name="country" id="country"  class="combo_box" tabindex="4" style="margin-bottom:32px;" onChange ="getAllState();">
                            <?php 
                            $o_count = count($country);
                            $selectany='';
                            if ($o_count == 0) {
                                $selectany='selected=selected';
                            }
                            ?>
                            <option value="" <?php if($_SESSION['country']=='' ){echo 'selected'; }?>>All</option>
                            <option value="canada" <?php if($_SESSION['country']=='canada'){echo 'selected'; }?>>Canada</option>
                            <option value="usa" <?php if($_SESSION['country']=='usa'){echo 'selected';} ?>>USA</option>
                        </select>
                    </div>
                    <div>&nbsp;</div>      
                </div>
                </div>
                <div class="col-md-4">
                    <div class="bodytext">
                    <div>
                        <strong>State/Province</strong>
                    </div>
                    <div>
                        <select name="state[]" id="state_list" multiple="multiple" size="3" class="combo_box state_list" tabindex="5" onChange ="getAllCity();">
                            <?php 
                            $o_count = count($state);
                            $selectany='';
                            if ($o_count == 0) {
                                $selectany='selected=selected';
                            }
                            ?>
                            <option value="" <?php echo $selectany; ?> >Any</option>
                            <?php 
                             $state_province =getStateMulti($country);
                             /*echo "<pre>";
                             print_r($state_province);
                             echo "</pre>";*/
                             if($country!=''){
                             foreach($state_province as $id=>$name){ ?>
                                <option  <?php if(in_array($id,$_SESSION['state'])) { echo "selected"; } ?> value="<?php echo $id;?>" ><?php echo htmlspecialchars($name, ENT_QUOTES); ?></option> 
                            <?php 
                             }}
                            ?>
                        </select>
                    </div>
                    <div>&nbsp;</div>  
                </div>
                </div>
                <div class="col-md-4">
                    <div class="bodytext">
                    <div>
                        <strong>City</strong>
                    </div>
                    <div>
                        <select name="city[]" id="city_list" multiple="multiple" size="3" class="combo_box city_list" tabindex="6">
                            <?php 
                            //print_r($city);
                            $o_count = count($city);
                            $selectany='';
                            if ($o_count == 0 || $city[0]=='0') {
                                $selectany='selected=selected';
                            }
                            ?>
                            <option value="" <?php echo $selectany; ?> >Any</option>
                            <?php 
                            if(!empty($state_code)){
                             $city_array =getCityMulti($state_code,$country);
                             /*echo "<pre>";
                             print_r($city_array);
                             echo "</pre>";*/
                             foreach($city_array as $id=>$name){ ?>
                                <option  <?php if(in_array($id,$_SESSION['city'])) { echo "selected"; } ?> value="<?php echo $id;?>" ><?php echo htmlspecialchars($name, ENT_QUOTES); ?></option> 
                            <?php 
                            }
                            }
                            ?>
                        </select>
                    </div>
                    <div>&nbsp;</div>  
                </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="bodytext">
                    <div><strong>Headline Search</strong></div>
                    <div>
                        <input type="text"  class="input_box" size="45" name="digital_data_keyword_search" id="digital_data_keyword_search" value="<?php if($digital_data_keyword_search!=''){echo htmlspecialchars($digital_data_keyword_search,ENT_QUOTES); }else{echo $digital_data_keyword_search;}?>" tabindex="7"/>
                    </div><div>&nbsp;</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="bodytext">
                    <div><strong>From Date</strong></div>
                    <div>
                      <input placeholder="YYYY-MM-DD" type="text" name="from_date" id="from_date" size="35" maxlength="20" readonly class="input_box" value="<?php if(isset($_REQUEST['from_date']) AND $_REQUEST['from_date']!="") { echo $_REQUEST['from_date']; }else{echo $sdate; } ?>" tabindex="8"/> 
                      <a href="#" onclick="displayCalendar(document.dashboardForm.from_date,'yyyy-mm-dd',this); return false;">
                          <img name="popcal3" src="js_calendar/images/getcal.gif" border="0" alt="" style="vertical-align:bottom;" />
                      </a>  
                    </div>
                    <div>&nbsp;</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="bodytext">
                    <div><strong>To Date</strong></div>
                    <div>
                       <input placeholder="YYYY-MM-DD" type="text" name="to_date" id="to_date" size="35" maxlength="20" readonly class="input_box" value="<?php if(isset($_REQUEST['to_date']) AND $_REQUEST['to_date']!="") { echo $_REQUEST['to_date']; }else{echo $cdate; } ?>" tabindex="9" /> 
                       <a href="#" onclick="displayCalendar(document.dashboardForm.to_date,'yyyy-mm-dd',this); return false;">
                           <img name="popcal3" src="js_calendar/images/getcal.gif" border="0" alt="" style="vertical-align:bottom;" />
                       </a> 
                    </div>
                    <div>&nbsp;</div>
                </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="bodytext">
                    <div><strong>Sector</strong>
                    </div>
                    <div>
                    <select name="sector_list[]" id="sector_list" multiple="multiple" size="3" class="combo_box" onchange="getCategory();" tabindex="5">
                        <?php 
                            $APISECTORURL=RPVAPIURL.'data/sectors';
                            $get_data = callAPI('GET', $APISECTORURL, false);
                            $response = json_decode($get_data, true);
                            $rows_sector_data=$response['data'];
                            $selectany='selected=selected';
                             $o_count = count($sector_list_array);
                            $selectany='';
                            if ($o_count == 0) {
                                $selectany='selected=selected';
                            }
                            ?>
                            <option value="0" <?php echo $selectany; ?>>Any</option>
                            <?php 
                            foreach($rows_sector_data['Sectordetails'] as $sectorData){ 
                                if(!in_array($sectorData['sectorID'],$_SESSION['sess_sector'])){
                                    continue;
                                }
                                ?>
                                <option  <?php if(in_array($sectorData['sectorID'],$sector_list_array)) { echo "selected"; } ?> value="<?php echo $sectorData['sectorID'];?>" ><?php echo htmlspecialchars($sectorData['sectorName'], ENT_QUOTES); ?></option> 
                            <?php 
                            }
                            ?>
                        </select>
                    </div>
                    <div>&nbsp;</div>      
                </div>
                </div>
                <div class="col-md-4">
                    <div class="bodytext">
                    <div><strong>Category</strong>
                    </div>
                    <div>
                    <select name="category_list[]" id="category_list_data_htm" onchange="getSubCategory();"  multiple="multiple" size="3" class="combo_box" tabindex="6">
                        <?php 
                        
                         $o_count_sec = count($sector_list_array);
                         if(is_array($_SESSION['sector_list'])) { $o_count_sec_sess = count($_SESSION['sector_list']); }
                         $selectany='';
                         if ($o_count_sec == 0 || $o_count_sec_sess==0 || $category_list_array[0]=='') {
                             $selectany='selected=selected';
                         }
                        ?>    
                        <option value="0" <?php echo $selectany;?>>Any</option>
                            <?php 
                               if(!empty($sector_list_array)){

                                $arrayValue = $sector_list_array;
                                $postSector['sectors']=$arrayValue;
                                $posted_data=json_encode($postSector);
                                //echo $posted_data;
                                $APISECTORURL=RPVAPIURL.'data/sectors';
                                if(!empty($posted_data)){
                                $getSector_data = callAPI('POST', $APISECTORURL, $posted_data);
                                $response_category = json_decode($getSector_data, true);
                                //print_r($response_category);
                                if(!empty($response_category)){
                                    foreach($response_category as $getCateName) {
                                        if(!in_array($getCateName['sectorID'],$_SESSION['sess_category'])){
                                            continue;
                                        }
                                         ?>
                                    <option  <?php if(in_array($getCateName['sectorID'],$_SESSION['category_list'])) { echo "selected"; } ?> value="<?php echo $getCateName['sectorID'];?>" ><?php echo htmlspecialchars($getCateName['sectorName'], ENT_QUOTES); ?></option> 
                               <?php 
                                }
                                }
                               }
                               }
                            ?>
                        </select>
                    </div>
                    <div>&nbsp;</div>      
                </div>
                </div>
                <div class="col-md-4">
                    <div class="bodytext">
                    <div><strong>Sub Category</strong>
                    </div>
                    <div>
                    <select name="subcategory_list[]" id="subcategory_list" onchange="getSubSubCategory();" multiple="multiple" size="3" class="combo_box" tabindex="7">
                        <?php 
                         $o_count_cat = count($category_list_array);
                         if(is_array($_SESSION['category_list'])) { $o_count_cat_sess = count($_SESSION['category_list']); }
                         $selectany='';
                         if ($o_count_cat == 0 || $o_count_cat_sess==0 || $subcategory_list_array[0]=='') {
                             $selectany='selected=selected';
                         }
                        ?>    
                        <option value="0" <?php echo $selectany;?>>Any</option>
                            <?php 
                               if(!empty($category_list_array)){

                                $arrayValue = $category_list_array;
                                $postSector['sectors']=$arrayValue;
                                $posted_data=json_encode($postSector);
                                //echo $posted_data;
                                $APISECTORURL=RPVAPIURL.'data/sectors';
                                if(!empty($posted_data)){
                                $getSector_data = callAPI('POST', $APISECTORURL, $posted_data);
                                $response_subcategory = json_decode($getSector_data, true);
                                //print_r($response_subcategory);
                                if(!empty($response_subcategory)){
                                    foreach($response_subcategory as $getSubCateName) {
                                            if(!in_array($getSubCateName['sectorID'],$_SESSION['sess_subcategory'])){
                                                continue;
                                            }
                                         ?>
                                    <option  <?php if(in_array($getSubCateName['sectorID'],$_SESSION['subcategory_list'])) { echo "selected"; } ?> value="<?php echo $getSubCateName['sectorID'];?>" ><?php echo htmlspecialchars($getSubCateName['sectorName'], ENT_QUOTES); ?></option> 
                               <?php 
                                }
                                }
                               }
                               }
                            ?>
                        </select>
                    </div>
                    <div>&nbsp;</div>      
                </div>
                </div>
            </div>
        
        <div class="clearfix"></div>  
        <div>
          <input class="submitbutton" type="submit" name="search_button"  value="Search" onclick="export_to_csv_file('');"/> &nbsp; 
          <input class="submitbutton" type="submit" name="clear_search"  value="Clear Search" />
        </div>
        <input type="hidden" id="export_csv" name="export_csv" value="" />
        </form>
    </div>
    <div>
        <a name="info_container_top" style="visibility:hidden;">&nbsp;</a>
    </div>
    
    <div id="info_container222">
        <form name="resultForm" id="resultForm" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" onsubmit="return false;">
            <div style="border:solid 1px #0055E3;">
                <table width="100%" cellpadding="4" cellspacing="0" class="sortable">
                    <thead>
                        <tr>
                            <th class="toptable" nowrap="nowrap">Expand/Collapse</th>
                            <th class="toptable" nowrap="nowrap">
                                Company
                                <img src="images/spacer.gif" border="0" style="vertical-align:bottom;" width="15" height="15">
                            </th>
                            <th class="toptable" nowrap="nowrap">
                                Media Channel
                                <img src="images/spacer.gif" border="0" style="vertical-align:bottom;" width="15" height="15">
                            </th>
                            <!--<th class="toptable" nowrap="nowrap">
                                Digital Source
                                <img src="images/spacer.gif" border="0" style="vertical-align:bottom;" width="15" height="15">
                            </th>-->
                            <th class="toptable" nowrap="nowrap">
                                Date
                                <img src="images/spacer.gif" border="0" style="vertical-align:bottom;" width="15" height="15">
                            </th>

                            <th class="toptable" nowrap="nowrap">
                                Advertiser Domain
                                <img src="images/spacer.gif" border="0" style="vertical-align:bottom;" width="15" height="15">
                            </th>
                            <th class="toptable" nowrap="nowrap">
                                Spend
                            </th>
                            <th class="toptable" nowrap="nowrap">
                                Impressions
                            </th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if ($DRW->num_rows($result) > 0) {
                            $country_name="";
                            $city_name="";
                            $state_name="";
                            $company1="";
                            while ($row = $DRW->fetch_array($result)) {
                                $id=$row['id'];
                                $process_record_id=$row['digital_record_id'];
                                $advertiser_domain=$row['advertiser_domain'];
                                $spend=$row['spend'];
                                $impressions=$row['impressions'];
                                $creation_date=$row['creation_date'];
                                $digital_source=$row['digital_source'];
                                $media_channel=$row['media_channel'];
                                $searchVal= array("https://biscience.s3.amazonaws.com", "http://biscience.s3.amazonaws.com","https://ads.adclarity.com","http://ads.adclarity.com");
                                $creative_wrapper=str_replace($searchVal,"https://files2.competiscan.com",$row['creative_wrapper']);
                                $campaign_landing_page=$row['campaign_landing_page'];
                                $sql_company = "select companyName from cscan_company Where companyID='".$row['company_id']."'";
                                $result_comp = $DRW->query($sql_company, $DRW_read); 
                                $row_company = $DRW->fetch_assoc($result_comp);
                                $company1=$row_company['companyName'];
                                 //Media channel
                                /*$sql_mchannel = "select mchannel_id from cscan_digital_processed_mchannel Where processed_record_id='".$id."'$whereAndQueryMChannel";
                                $result_mchannel = $DRW->query($sql_mchannel, $DRW_biscience_digital); 
                                $mchannel_name=array();
                                while($row_mchannel = $DRW->fetch_assoc($result_mchannel)){
                                    $mchannel_name[]=mediaChannelName($row_mchannel['mchannel_id']);
                                }
                                if(!empty($mchannel_name)){
                                $media_channel=implode("; ",$mchannel_name);
                                 // echo $media_channel;
                                }*/
                                //location
                                $sql_location = "select location,location_state_code from cscan_digital_processed_location Where processed_record_id='".$id."'$whereAndQueryLocation$whereAndQueryCity";
                                $result_location = $DRW->query($sql_location, $DRW_biscience_digital); 
                                $city_data=array();
                                $state_data=array();
                                $country_data=array();
                                while($row_location = $DRW->fetch_assoc($result_location)){
                                    $location_name=explode(",",$row_location['location']);
                                    $city_name=trim($location_name[0]);
                                    $state_code=trim($row_location['location_state_code']);
                                    if($city_name!='' AND $state_code!=''){
                                        $sql_q = "select DISTINCT city,state_province,country from cscan_digital_city_state where city='".$city_name."' AND state_code='".$state_code."'";
                                        $res_query = $DRW->query($sql_q, $DRW_biscience_digital);
                                        if ($DRW->num_rows($res_query) > 0) {
                                        $row_loc_data = $DRW->fetch_assoc($res_query);
                                        $city_data[]=$row_loc_data['city'];
                                        $state_data[]=$row_loc_data['state_province'];
                                        $country_data[]=$row_loc_data['country'];
                                        }else {
                                        $countrydata='';
                                        $sql_country = "select DISTINCT country from cscan_digital_city_state where state_code='".$state_code."' limit 1";
                                        $res_query_country = $DRW->query($sql_country, $DRW_biscience_digital);
                                        if ($DRW->num_rows($res_query_country) > 0) {
                                        $row_country_data = $DRW->fetch_assoc($res_query_country);
                                        $country_data[]=$row_country_data['country'];
                                        }
                                        if($row['location_state_code']=='United States'){
                                            $city_data[]=$row['location_state_code'];
                                            $state_data[]=$row['location_state_code'];
                                            $countrydata='usa';
                                        }
                                        if($row['location_state_code']=='Canada'){
                                            $city_data[]=$row['location_state_code'];
                                            $state_data[]=$row['location_state_code'];
                                            $countrydata='canada';
                                        }
                                        $country_data[]=$countrydata;
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
                                  $country_name = implode("; ",array_unique($country_data)); 
                                  $country_name=trim($country_name,'; ');
                                }
                                
                                //publisher
                                $sql_publisher = "select publisher from cscan_digital_processed_publisher Where processed_record_id='".$id."'";
                                $result_publisher = $DRW->query($sql_publisher, $DRW_biscience_digital); 
                                $publisher_name=array();
                                while($row_publisher = $DRW->fetch_assoc($result_publisher)){
                                    $publisher_name[]=$row_publisher['publisher'];
                                }
                                if(!empty($publisher_name)){
                                $publisher=implode("; ",$publisher_name);

                                }
                                 //title
                                $sql_title = "select compaign_title from cscan_digital_processed_title Where processed_record_id='".$id."'";
                                $result_title = $DRW->query($sql_title, $DRW_biscience_digital); 
                                $title_array=array();
                                while($row_title = $DRW->fetch_assoc($result_title)){
                                    $title_array[]=$row_title['compaign_title'];
                                }
                                if(!empty($title_array)){
                                $title=implode("; ",$title_array);

                                }
                                $sectorName="";
                                if($row['sector_id']!=""){
                                    $sectorName=sectorName($row['sector_id']);

                                }
                                $categoryName="";
                                if($row['category_id']!=""){
                                    $categoryName=categoryName($row['category_id']);

                                }
                                $subCategoryName="";
                                if($row['subcategory_id']!=""){
                                    $subCategoryName=subCategoryName($row['subcategory_id']);

                                }
                                //digital source
                               /* $sql_digital_source = "select digital_source from cscan_digital_processed_source Where processed_record_id='".$id."'$whereAndQueryDigitalSource";
                                $result_digital_source = $DRW->query($sql_digital_source, $DRW_biscience_digital); 
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

                                }else{
                                   $digital_source='NA'; 
                                }*/
                            ?>
                        <tr>
                            <td class="bodytext" valign="top">
                                <a href="#" onclick="show_result_detail('<?php echo $id;?>'); return false;">
                                    <img name="detail_img_<?php echo $id;?>" id="detail_img_<?php echo $id;?>" src="images/plus.jpg" border="0"></a>
                            </td>
                            <td class="bodytext" valign="top"><?php echo $company1; ?></td>
                            <td class="bodytext" valign="top"><?php if($media_channel!=""){echo $media_channel;}else{ echo "NA";} ?></td>
                            <td class="bodytext" valign="top"><?php if($creation_date!='0000-00-00' && $creation_date!=''){echo date('Y-m-d',strtotime($creation_date)); }?></td>
                            <td class="bodytext" valign="top"><?php echo $advertiser_domain; ?></td>
                            <td class="bodytext" valign="top"><?php echo $spend; ?></td>
                            <td class="bodytext" valign="top"><?php echo $impressions; ?></td>

                        </tr>
                        <tr style="display:none;" id="detail_<?php echo $id;?>">
                            <td colspan="9" class="bodytext" valign="top">
                                <div><b>Media Channel</b>: <?php if($media_channel!=""){echo $media_channel;}else{ echo "NA";} ?></div>
                                <div><b>Country</b>: <?php if($country_name!=""){echo strtoupper($country_name); }else{ echo "NA";} ?> </div>
                                <div><b>State</b>: <?php if($state_name!=""){echo $state_name; }else{ echo "NA";} ?> </div>
                                <div><b>City</b>: <?php if($city_name!=""){echo $city_name; }else{ echo "NA";} ?> </div>
                                <div><b>Headline</b>: <?php if($title!=""){echo $title;}else{echo "NA";} ?></div>
                                <div><b>Campaign Landing Page</b>: <a href="javascript:var w=window.open('<?php if($campaign_landing_page!=""){echo $campaign_landing_page;}else{echo "NA";} ?>','Biscience', 'width=400,height=600','scrollbars=yes');"> View</a></div>
                                <div><b>Creative</b>: <a href="javascript:var w=window.open('<?php if($creative_wrapper!=""){echo $creative_wrapper;}else{echo "NA";} ?>','Biscience', 'width=400,height=600','scrollbars=yes');"> <?php if($creative_wrapper!=""){echo $creative_wrapper;}else{echo "NA";} ?></a></div>
                                <div><b>Publisher</b>: <?php if($publisher!=""){echo $publisher;}else{echo "NA";} ?></div>
                                <div><b>Digital Source</b>: <?php if($digital_source!=""){echo $digital_source;}else{ echo "NA";} ?></div>
                                <div><b>Sector</b>: <?php if($sectorName!=""){echo $sectorName;}else{ echo "NA";} ?></div>
                                <div><b>Category</b>: <?php if($categoryName!=""){echo $categoryName;}else{ echo "NA";} ?></div>
                                <div><b>SubCategory</b>: <?php if($subCategoryName!=""){echo $subCategoryName;}else{ echo "NA";} ?></div>

                            </td>
                        </tr>
                        
                    <?php }}else { ?>
                        <tr>
                            <td colspan="8" class="bodytext" valign="top" align="center">
                                <span class="error" > No Record Found!</span>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
                        
            </div>
                <div>
		<div class="error" style="float:left;"></div>
                 <div class="bodytext" style="float:right;font-style:italic;font-size:smaller;"></div>
                 <?php if($num_of_rows > $pagelimit){ ?>
                 <table border="0" width="100%" cellspacing = "0"  cellpadding ="4">
                    <tr><td>&nbsp;</td></tr>
                    <?php
                    $firstlink = '[First]';
                    $prevlink = '[Prev]';
                    $nextlink = '[Next]';
                    $lastlink = '[Last]';
                    $middlelinks = '';
                    $limstart = $p;
                    $limiter = $pagelimit;
                    $rowcnt = $num_of_rows;
                    $show = 10;
                    
                    //first and previous only if not on first
                    if ($limstart > 0) {
                        if ($limstart >= $limiter)
                            $prev = $limstart - $limiter;
                        else
                            $prev = 0;
                        $firstlink = "[<a href=\"{$_SERVER['PHP_SELF']}?p=0&from_date={$from_date}&to_date={$to_date}\">First</a>]";
                        $prevlink = "<a href=\"{$_SERVER['PHP_SELF']}?p=$prev&from_date={$from_date}&to_date={$to_date}\">&laquo; Prev $limiter</a>";
                    }
                    // middle loop through total results
                    $numbers = ceil($rowcnt / $limiter);
                    $loopstart = ceil($limstart / $limiter);
                    if ($loopstart < ($show - 1))
                        $loopstart = 0; // begin, do not move until 4
                    if ($numbers < $show)
                        $loopend = $numbers; // loopend is less than $show
                    else
                        $loopend = $loopstart + $show;
                    if ($loopend > $numbers && $loopstart != 0) { // end, show last $show
                        $loopstart = $numbers - $show;
                        $loopend = $numbers;
                    }
                    for ($i = $loopstart; $i < $loopend; $i++) {
                        $startnum = $limiter * $i;
                        if ($startnum != $limstart) {
                            $middlelinks .= "<a href=\"{$_SERVER['PHP_SELF']}?p=$startnum&from_date={$from_date}&to_date={$to_date}\">" . ($i + 1) . "</a> ";
                        } else
                            $middlelinks .= ($i + 1) . ' ';
                    }
                    //next and last if not on last
                    if ($limstart < $rowcnt && (($limstart + ($limiter * 2)) < $rowcnt || ($rowcnt - ($limstart + $limiter)) > 0)) {
                        $next = $limstart + $limiter;
                        $nextlink = "<a href=\"{$_SERVER['PHP_SELF']}?p=$next&from_date={$from_date}&to_date={$to_date}\">Next $limiter &raquo;</a>";
                        $lastlink = "[<a href=\"{$_SERVER['PHP_SELF']}?p=" . (($numbers - 1) * $limiter) . "&from_date={$from_date}&to_date={$to_date}\">Last</a>]";
                    }
                    if ($middlelinks != '')
                        $middlelinks = "[ $middlelinks ] &nbsp;";
                    print "<tr><td align=\"center\" class=\"bodytext\">$firstlink &nbsp; $prevlink &nbsp; $middlelinks $nextlink &nbsp; $lastlink</td></tr>";
                    print "<tr><td align=\"center\" class=\"bodytext\">Showing results " . ($limstart + 1) . " to ";
                    if ($limstart + $limiter < $rowcnt)
                        print ($limstart + $limiter);
                    else
                        print $rowcnt;
                    print " of $rowcnt</td></tr>";
                    ?>
                </table>
                 <?php } ?>
                </div>
            
        </form>
    </div>
    <div>&nbsp;</div>
    <div>
        
        <form name="outputForm" id="outputForm" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <?php if($num_of_rows <=50000) { ?>
            <input class="submitbutton" type="button" id="export_csvfile" value="Export To CSV" onclick="export_to_csv_file('Export To CSV');return false;"/> &nbsp; 
            <?php } ?>
            <input class="submitbutton" type="submit" name="top_button1" id="top_button1" value="Top" onclick="move_page_top();
                return false;" /><br/>
            <div id="loading_image" style="display: none;">
                <img src="images/ajax-loader.gif" alt="" border="0"/>
            </div>
        </form>
        
    </div>	
</div>                                                                 
<?php include 'footer_bottom.php';?> 
<script>
function export_to_csv_file(val) {
   document.getElementById("export_csv").value=val;
   //alert(val);
   if(val!==''){
    var dt = new Date();
    var curr_date=dt.getFullYear() + "/" + (dt.getUTCMonth() + 1) + "/" + dt.getDate()
    $.ajax({
             type: 'post',
             url: '<?php echo $_SERVER['PHP_SELF']; ?>',
             data: $('form#dashboardForm').serialize(),
             //data: {export_csv:val},
             beforeSend: function() {  
               $('#loading_image').show();
               //$('#export_csvfile').hide();
               $('#export_csvfile').attr("disabled", true);
             },
             success: function(response){
                   //alert(response)
                   if (response !== "error") {
                   let blob = new Blob([response], { type: "application/octetstream" });
                   var a = document.createElement('a');
                   var url = window.URL.createObjectURL(blob);
                   a.href = url;
                   a.download = 'digital_dashboard_report_'+curr_date+'.csv';
                   document.body.append(a);
                   a.click();
                   a.remove();
                   window.URL.revokeObjectURL(url);
                   $('#loading_image').hide();
                   $('#export_csvfile').removeAttr("disabled");
                   //$('#export_csvfile').show(); 
                   }
             }  

         });
    }
}
function show_result_detail(row_num) {
	var element = $("#detail_"+row_num);
	var element2 = $("#detail_img_"+row_num);
	if(element && element2){
		if(element.css("display")=="none") { 
			element.css("display","table-row");
			element2.attr("src","images/minus.jpg");
		} 
		else { 
			element.css("display","none"); 
			element2.attr("src","images/plus.jpg");
		}
	}
}
function move_page_top(){
	document.location.href = "#";
}
function getAllState() {
    var str='';
    var state='';
    var country='';
    var val=document.getElementById('country');
    for (i=0;i< val.length;i++) { 
        if(val[i].selected){
            str += val[i].value + ','; 
        }
    }

    if(str.length>0){

        country=str.slice(0,str.length -1);
    }

    var state_val=document.getElementById('state_list');
    var str2='';
    if(state_val.length >0){
        for (i=0;i< state_val.length;i++) { 
                if(state_val[i].selected){
                    str2 += state_val[i].value + ','; 
                }
            }
        state=str2.slice(0,str2.length -1);    
     }


    if(country!=''){               
        $.ajax({          
                type: "POST",
                url: "ajax-get-state-city_latest.php",
                data: {country:country,state:state,action:'getState'},
                success: function(data){
                        $('.state_list').html(data);
                         getAllCity();
                        //getSubToSubCategory();
                        }
            });

    }else{
       $('#state_list').html("<option selected value=''>Any</option>");  
       $('#city_list').html("<option selected value=''>Any</option>");
    }
}

function getAllCity() {
    var str='';
    var city='';
    var state='';
    var val=document.getElementById('state_list');
    var country=document.getElementById('country').value;
   for (i=0;i< val.length;i++) { 
        if(val[i].selected){
            str+="'" + val[i].value + "',";
            //str += '" + val[i].value + "';
            //val[i].value + ','; 
        }
    }

    if(str.length>0){
        str_rem=str.slice(1,-1);
        state=str_rem.slice(0,str_rem.length -1);
    }

    var city_val=document.getElementById('city_list');
    var str2='';
    if(city_val.length >0){
        for (i=0;i< city_val.length;i++) { 
                if(city_val[i].selected){
                    str2 += city_val[i].value + '',''; 
                }
            }
        city=str2.slice(0,str2.length -1);    
     }


    if(country!='' && state!=''){               
        $.ajax({          
                type: "POST",
                url: "ajax-get-state-city_latest.php",
                data: {country:country,state:state,city:city,action:'getCity'},
                success: function(data){
                        $('.city_list').html(data);
                }
            });

    }else{
       $('#city_list').html("<option selected value=''>Any</option>");  
    }
}

function getCategory() {
        var str='';
        var str_cat='';
        var cat_id='';
        var val=document.getElementById('sector_list');
        for (i=0;i< val.length;i++) { 
            if(val[i].selected){
                str += val[i].value + ','; 
            }
        }         
        var str=str.slice(0,str.length -1);
          
        var val_cat=document.getElementById('category_list_data_htm');
        //alert(val);
        for (i=0;i< val_cat.length;i++) { 
            if(val_cat[i].value!=0){
                if(val_cat[i].selected){
                    str_cat += val_cat[i].value + ','; 
                }
            }
        }
        if(str_cat.length>0){
            var cat_id=str_cat.slice(0,str_cat.length -1);
        }  
    //console.log(str);
    if(str!='0'){
        $.ajax({          
            type: "POST",
            url: "ajax-get-state-city_latest.php",
            data: {sector_list:str,cat_list:cat_id,action:'getCategoryData'},
            success: function(data){
                //console.log(data);
                $("#category_list_data_htm").html(data);
                getSubCategory();
            }
        });
    }else{
        $('#category_list_data_htm').html("<option selected value='0'>Any</option>");  
        $('#subcategory_list').html("<option selected value='0'>Any</option>");
        //$('#subsubcategory_list_htm').html("<option selected value=''>Any</option>"); 
    }
}
function getSubCategory() {
            var str='';
            var strsubcat='';
            var cat_id='';
            var subcat_id='';
            var val=document.getElementById('category_list_data_htm');
            //alert(val);
            for (i=0;i< val.length;i++) { 
                if(val[i].value!=0){
                    if(val[i].selected){
                        str += val[i].value + ','; 
                    }
                }
            }
            if(str.length>0){
                var cat_id=str.slice(0,str.length -1);
            }          
           
            var val_sub_cat=document.getElementById('subcategory_list');
            for (i=0;i< val_sub_cat.length;i++) { 
                 if(val_sub_cat[i].value!=0){
                    if(val_sub_cat[i].selected){
                        strsubcat += val_sub_cat[i].value + ','; 
                    }
                } 
            }
            if(strsubcat.length>0){
                var subcat_id=strsubcat.slice(0,strsubcat.length -1);
            }
           //console.log(cat_id);
           //console.log(subcat_id);
            if(cat_id!=""){
                $.ajax({          
                        type: "POST",
                        url: "ajax-get-state-city_latest.php",
                        data: {cat_list:cat_id,subcat_id:subcat_id,action:'getSubCat',},
                        success: function(data){
                         $('#subcategory_list').html(data);
                         getSubSubCategory();
                        }
                });
            } else{
               $('#subcategory_list').html("<option selected value='0'>Any</option>");
               //$('#subsubcategory_list_htm').html("<option selected value=''>Any</option>"); 
               
            }
     }

function showLook(iframe_div,iframe_link,iframe_id,focus_obj){
        var obj = document.getElementById(iframe_div);
	var obj2 = document.getElementById(iframe_link);
	if(obj){
		var ltext = '';
		if(obj.style.display!='none'){
			obj.style.display = 'none';
			ltext = 'Show Lookup';
			if(top.frames && top.frames[iframe_id] && top.frames[iframe_id].document && top.frames[iframe_id].document.forms && top.frames[iframe_id].document.forms.companyForm && top.frames[iframe_id].document.forms.companyForm.companylook){
				top.frames[iframe_id].document.forms.companyForm.companylook.value = '';
			}
			focus_obj.focus();
		}
		else{
			obj.style.display = 'block';
			ltext = 'Hide Lookup';
			
			if(top.frames && top.frames[iframe_id] && top.frames[iframe_id].document){
				top.frames[iframe_id].doSel();
			}
			
			window.setTimeout("doFocus('"+iframe_id+"')", 500);	
		}
		my_innerHTML_text(obj2,ltext);
	}
}
function doFocus(iframe_id){
	if(top.frames && top.frames[iframe_id] && top.frames[iframe_id].document && top.frames[iframe_id].document.forms && top.frames[iframe_id].document.forms.companyForm && top.frames[iframe_id].document.forms.companyForm.companylook){
		top.frames[iframe_id].document.forms.companyForm.companylook.focus();
	}
}
function checkLookup(iframe_id){
        if(top.frames && top.frames[iframe_id] && top.frames[iframe_id].document){
               top.frames[iframe_id].doSel();
	}
}
/*function export_to_csv_file(val) {
   document.getElementById("export_csv").value=val;
   document.getElementById("dashboardForm").submit(); 
}*/
</script>
