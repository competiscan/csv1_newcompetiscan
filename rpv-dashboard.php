<?php
$start_time = microtime(true);
$PAGE_HEADING = " Relative Promotional Value Dashboard";
$TITLE = "Competiscan $PAGE_HEADING";  
$HEAD = '<link rel="stylesheet" href="includes/jquery/jquery-ui.css" /><script type="text/javascript" src="includes/jquery/jquery.min.js"></script><script type="text/javascript" src="includes/jquery/jquery-ui.min.js"></script>
    <script type="text/javascript" src="includes/google/jsapi.js"></script>
<script type="text/javascript" src="includes/jquery/jquery.tokeninput.js"></script><link rel="stylesheet" href="includes/jquery/token-input.css" />
<link rel="stylesheet" href="js_calendar/calendar.css" media="screen" type="text/css" />
<script type="text/javascript" src="js_calendar/calendar.js"></script><script type="text/javascript" src="js_calendar/calendar.js"></script>';
//include 'header_top.php';
require_once('includes/globalSession.php');
require_once('includes/checklogin.php');
require_once('includes/rpv-dashboard-function.php');
if(!defined('ENV')){
    define('ENV',getenv('SERVER_NAME'));
}
// page permission
$page_permission = getPagePermission();
if(!empty($_SESSION['sess_search_page_permission'])){
    $page_permission=$_SESSION['sess_search_page_permission'];
}
if(!in_array('rpv_dashboard',$page_permission)) {
    ob_end_clean();
    header("Location: fullsearch.php?searchview=2");
    exit;
}
$currenttime=time();
$filename_csv = 'Competiscan_Export_'.$currenttime.'_'.date('Y-m-d').'.csv';
/*echo "<pre>";
print_r($_SESSION);
echo "</pre>";
echo "<pre>";
print_r($_REQUEST);
echo "</pre>";*/
if(isset($_GET['p'])) {
	$p = (int)$_GET['p'];
}
else {
	$p = 0;
}
if(isset($_REQUEST['sid'])) {
    $sid = $_REQUEST['sid'];
}else{
$sid='';
}
if(isset($_REQUEST['page_type'])) {
    $page_type = $_REQUEST['page_type'];
}else{
$page_type=1;
}
if(isset($_REQUEST['dct'])) {
    $_REQUEST['dct'];
    $dct = $_REQUEST['dct'];
}else{
$dct="fwd";
}
if (isset($_GET['page_no']) && $_GET['page_no']!="") {
    $page_no = $_GET['page_no'];
    
    } else {
    $page_no = 1;
}
$previous_page = $page_no - 1;
$next_page = $page_no + 1;
$productids_array="";
$postedData="";
$income_list_array=array();
$promo_list_array=array();
$age_list_array=array();
$country_list="0";
$state_list_array=array();
$sector_list_array=array();
$category_list_array=array();
$subcategory_list_array=array();
$subsubcategory_list_array=array();
$numRows='0';
$responseSearch="";
$rpv="0";
$private_label_val="0";
$rpv_data_keyword_search="";
$cdate=date("Y-m-d");

$sdate=date('Y-m-d', strtotime($cdate ." - 1 day"));
//$sdate="2023-01-01";
$postdata="";
if(isset($_REQUEST['clear_search']) and $_REQUEST['clear_search']=='Clear Search') {
        $_REQUEST['retail_company']="";
        $_SESSION['retail_company']="";
        $_REQUEST['retail_pcompany']="";
        $_SESSION['retail_pcompany']="";
        $_SESSION['brand_company']="";
        $_REQUEST['brand_company']="";
        $_REQUEST['brand_pcompany']="";
        $_SESSION['brand_pcompany']="";
        $_REQUEST['sector_list']="";
        $_SESSION['sector_list']="";
        $_REQUEST['category_list']="";
        $_SESSION['category_list']="";
        $_REQUEST['subcategory_list']="";
        $_SESSION['subcategory_list']="";
        $_REQUEST['subsubcategory_list']="";
        $_SESSION['subsubcategory_list']="";
        $_REQUEST['country_list']="";
        $_SESSION['country_list']="";
        $_REQUEST['rpv_data_keyword_search']="";
        $_SESSION['rpv_data_keyword_search']="";
        $_REQUEST['state_list']='';
        $_SESSION['state_list']='';
        $_REQUEST['age_list']="";
        $_SESSION['age_list']="";
        $_REQUEST['income_list']="";
        $_SESSION['income_list']="";
        $_REQUEST['promo_list']="";
        $_SESSION['promo_list']="";
        $_REQUEST['private_label']=''; 
        $_SESSION['private_label']='';
        $_REQUEST['from_date']="";
        $_REQUEST['to_date']="";
        $_SESSION['from_date']="";
        $_SESSION['to_date']="";
        $_REQUEST['rpv']="";
        $_SESSION['rpv']="";
        $to_date=$cdate;
        $from_date=$sdate;      
}
$postdata_all=array();
############ Retail Company################*/
if(isset($_REQUEST['retail_company']) AND $_REQUEST['retail_company']!=''){
    $retail_company=$_SESSION['retail_company']=$_REQUEST['retail_company'];
    $postdata_all['retail_company']=str_replace('"',"",$retail_company);
}elseif(isset($_SESSION['retail_company']) AND $_SESSION['retail_company']!="" AND isset($_REQUEST['search_button'])!='Search'){
    $retail_company=$_SESSION['retail_company'];
    $postdata_all['retail_company']=str_replace('"',"",$retail_company);
}else{
    $retail_company="";
    $_SESSION['retail_company']="";
}
/*############ Brand Company#######################*/
if(isset($_REQUEST['brand_company']) AND $_REQUEST['brand_company']!=''){
    $brand_company=$_SESSION['brand_company']=$_REQUEST['brand_company'];
    $postdata_all['brand_company']=str_replace('"',"",$brand_company);
}elseif(isset($_SESSION['brand_company']) AND $_SESSION['brand_company']!="" AND isset($_REQUEST['search_button'])!='Search'){
    $brand_company=$_SESSION['brand_company'];
    $postdata_all['brand_company']=str_replace('"',"",$brand_company);
}else{
    $brand_company="";
    $_SESSION['brand_company']="";
}
############ Retail Parent Company################*/
if(isset($_REQUEST['retail_pcompany']) AND $_REQUEST['retail_pcompany']!=''){
    $retail_pcompany=$_SESSION['retail_pcompany']=$_REQUEST['retail_pcompany'];
    $postdata_all['retail_company']=$retail_pcompany;
}elseif(isset($_SESSION['retail_pcompany']) AND $_SESSION['retail_pcompany']!="" AND isset($_REQUEST['search_button'])!='Search'){
    $retail_pcompany=$_SESSION['retail_pcompany'];
    $postdata_all['retail_company']=$retail_pcompany;
}else{
    $retail_pcompany="";
    $_SESSION['retail_pcompany']="";
}
/*############ Brand Parent Company#######################*/
if(isset($_REQUEST['brand_pcompany']) AND $_REQUEST['brand_pcompany']!=''){
    $brand_pcompany=$_SESSION['brand_pcompany']=$_REQUEST['brand_pcompany'];
    $postdata_all['brand_company']=$brand_pcompany;
}elseif(isset($_SESSION['brand_pcompany']) AND $_SESSION['brand_pcompany']!="" AND isset($_REQUEST['search_button'])!='Search'){
    $brand_pcompany=$_SESSION['brand_pcompany'];
    $postdata_all['brand_company']=$brand_pcompany;
}else{
    $brand_pcompany="";
    $_SESSION['brand_pcompany']="";
}
/*############ DATE ADDEDTODATABASE################*/
if(isset($_REQUEST['from_date']) AND $_REQUEST['from_date']!="") {
    $from_date =$_SESSION['from_date']=$_REQUEST['from_date'];
    $postdata_all['from_date']=$from_date;
}elseif(isset($_SESSION['from_date']) AND $_SESSION['from_date']!="" AND isset($_REQUEST['search_button'])!='Search'){
    $from_date=$_SESSION['from_date'];
    $postdata_all['from_date']=$from_date;
}else{
    $from_date=$sdate;
    $_SESSION['from_date']="";
}
if(isset($_REQUEST['to_date']) AND $_REQUEST['to_date']!="") {
    $to_date =$_SESSION['to_date']=$_REQUEST['to_date'];
    $postdata_all['to_date']=$to_date;
}elseif(isset($_SESSION['to_date']) AND $_SESSION['to_date']!="" AND isset($_REQUEST['search_button'])!='Search'){
    $to_date=$_SESSION['to_date'];
    $postdata_all['to_date']=$to_date;
}else{
    $to_date=$cdate;
    $_SESSION['from_date']="";
}
/*############ SECTOR LIST################*/
if(isset($_REQUEST['sector_list']) AND $_REQUEST['sector_list']!='' AND !empty($_REQUEST['sector_list'])){
    $sector_list_array=$_SESSION['sector_list']=$_REQUEST['sector_list'];
    if($sector_list_array[0]=='0'){
        unset($sector_list_array[0]);
        if(!empty($sector_list_array)){
        $postdata_all['sectorID']=implode(',',$sector_list_array);
        }
    }
    else{
        $postdata_all['sectorID']=implode(',',$sector_list_array);
    }
    
}elseif(isset($_SESSION['sector_list']) AND $_SESSION['sector_list']!="" AND isset($_REQUEST['search_button'])!='Search'){
    $sector_list_array=$_SESSION['sector_list'];
    if($sector_list_array[0]=='0'){
        unset($sector_list_array[0]);
        if(!empty($sector_list_array)){
        $postdata_all['sectorID']=implode(',',$sector_list_array);
        }
    }else{
        $postdata_all['sectorID']=implode(',',$sector_list_array);
    }
    
}else{
    $sector_list_array=array();
    $_SESSION['sector_list']="";
}
/*############ Category LIST################*/
if(isset($_REQUEST['category_list']) AND $_REQUEST['category_list']!='' AND !empty($_REQUEST['category_list'])){
    $category_list_array=$_SESSION['category_list']=$_REQUEST['category_list'];
    if($category_list_array[0]=='0'){
        unset($category_list_array[0]);
        if(!empty($category_list_array)){
        $postdata_all['category']=implode(',',$category_list_array);
        }
    }
    else{
        $postdata_all['category']=implode(',',$category_list_array);
    }
    
}elseif(isset($_SESSION['category_list']) AND $_SESSION['category_list']!="" AND isset($_REQUEST['search_button'])!='Search'){
    $category_list_array=$_SESSION['category_list'];
    if($category_list_array[0]=='0'){
        unset($category_list_array[0]);
        if(!empty($category_list_array)){
        $postdata_all['category']=implode(',',$category_list_array);
        }
    }else{
        $postdata_all['category']=implode(',',$category_list_array);
    }
    
}else{
    $category_list_array=array();
    $_SESSION['category_list']="";
}
/*############ SUBCategory LIST################*/
if(isset($_REQUEST['subcategory_list']) AND $_REQUEST['subcategory_list']!='' AND !empty($_REQUEST['subcategory_list'])){
    $subcategory_list_array=$_SESSION['subcategory_list']=$_REQUEST['subcategory_list'];
    if($subcategory_list_array[0]=='0'){
        unset($subcategory_list_array[0]);
        if(!empty($subcategory_list_array)){
        $postdata_all['sub_category']=implode(',',$subcategory_list_array);
        }
    }
    else{
        $postdata_all['sub_category']=implode(',',$subcategory_list_array);
    }
    
}elseif(isset($_SESSION['subcategory_list']) AND $_SESSION['subcategory_list']!="" AND isset($_REQUEST['search_button'])!='Search'){
    $subcategory_list_array=$_SESSION['subcategory_list'];
    if($subcategory_list_array[0]=='0'){
        unset($subcategory_list_array[0]);
        if(!empty($subcategory_list_array)){
        $postdata_all['sub_category']=implode(',',$subcategory_list_array);
        }
    }else{
        $postdata_all['sub_category']=implode(',',$subcategory_list_array);
    }
    
}else{
    $subcategory_list_array=array();
    $_SESSION['subcategory_list']="";
}
/*############ SUBTOSUBCategory LIST################*/
if(isset($_REQUEST['subsubcategory_list']) AND $_REQUEST['subsubcategory_list']!='' AND !empty($_REQUEST['subsubcategory_list'])){
    $subsubcategory_list_array=$_SESSION['subsubcategory_list']=$_REQUEST['subsubcategory_list'];
    if($subsubcategory_list_array[0]=='0'){
        unset($subsubcategory_list_array[0]);
        if(!empty($subsubcategory_list_array)){
        $postdata_all['sub_sub_category']=implode(',',$subsubcategory_list_array);
        }
    }
    else{
        $postdata_all['sub_sub_category']=implode(',',$subsubcategory_list_array);
    }
   
}elseif(isset($_SESSION['subsubcategory_list']) AND $_SESSION['subsubcategory_list']!="" AND isset($_REQUEST['search_button'])!='Search'){
    $subsubcategory_list_array=$_SESSION['subsubcategory_list'];
    if($subsubcategory_list_array[0]=='0'){
        unset($subsubcategory_list_array[0]);
        if(!empty($subsubcategory_list_array)){
        $postdata_all['sub_sub_category']=implode(',',$subsubcategory_list_array);
        }
    }else{
        $postdata_all['sub_sub_category']=implode(',',$subsubcategory_list_array);
    }
    
}else{
    $subsubcategory_list_array=array();
    $_SESSION['subsubcategory_list']="";
}
/*####################OCR SEARCH#######################*/
if(isset($_REQUEST['rpv_data_keyword_search']) AND $_REQUEST['rpv_data_keyword_search']!="") {
    $rpv_data_keyword_search =$_SESSION['rpv_data_keyword_search']=$_REQUEST['rpv_data_keyword_search'];
    $postdata_all['dts_val']=$rpv_data_keyword_search;
}elseif(isset($_SESSION['rpv_data_keyword_search']) AND $_SESSION['rpv_data_keyword_search']!="" AND isset($_REQUEST['search_button'])!='Search'){
    $rpv_data_keyword_search=$_SESSION['rpv_data_keyword_search'];
    $postdata_all['dts_val']=$rpv_data_keyword_search;
}else{
    $rpv_data_keyword_search="";  
    $_SESSION['rpv_data_keyword_search']="";
}
/*############ COUNTRY LIST################*/
if(isset($_REQUEST['country_list']) AND $_REQUEST['country_list']!='' AND !empty($_REQUEST['country_list'])){
    $country_list=$_SESSION['country_list']=$_REQUEST['country_list'];
    if($country_list=='0'){
        $postdata_all['countryID']=$country_list;
        $country_name='CA';
        $postdata_all['country']='CA';
        if($country_list=='1'){
            $country_name='US';
            $postdata_all['country']=$country_name;
        }
    }
    else{
        $postdata_all['countryID']=$country_list;
        $postdata_all['country']='CA';
        if($country_list=='1'){
            $country_name='US';
            $postdata_all['country']=$country_name;
        }
    }
    
}elseif(isset($_SESSION['country_list']) AND $_SESSION['country_list']!="" AND isset($_REQUEST['search_button'])!='Search'){
    $country_list=$_SESSION['country_list'];
    if($country_list=='0'){
        $postdata_all['countryID']=$country_list;
        $postdata_all['country']='CA';
        if($country_list=='1'){
            $country_name='US';
            $postdata_all['country']=$country_name;
        }
    }else{
        $postdata_all['countryID']=$country_list;
        $postdata_all['country']='CA';
        if($country_list=='1'){
            $country_name='US';
            $postdata_all['country']=$country_name;
        }
    }
    
}else{
    $country_list='0';
    $_SESSION['country_list']="";
}
/*############ STATE LIST################*/
if(isset($_REQUEST['state_list']) AND $_REQUEST['state_list']!='' AND !empty($_REQUEST['state_list'])){
    $state_list_array=$_SESSION['state_list']=$_REQUEST['state_list'];
    if($state_list_array[0]=='0'){
        unset($state_list_array[0]);
       if(!empty($state_list_array)){
        $postdata_all['stateID']=implode(',',$state_list_array);
        }
    }
    else{
        
        $postdata_all['stateID']=implode(',',$state_list_array);
    }
    
}elseif(isset($_SESSION['state_list']) AND $_SESSION['state_list']!="" AND isset($_REQUEST['search_button'])!='Search'){
    $state_list_array=$_SESSION['state_list'];
    if($state_list_array[0]=='0'){
        unset($state_list_array[0]);
        if(!empty($state_list_array)){
            $postdata_all['stateID']=implode(',',$state_list_array);
        }
        
    }else{
       $postdata_all['stateID']=implode(',',$state_list_array);
    }
    
}else{
    $state_list_array=array();
    $_SESSION['state_list']="";
}
/*############ AGE LIST################*/
if(isset($_REQUEST['age_list']) AND $_REQUEST['age_list']!='' AND !empty($_REQUEST['age_list'])){
    $age_list_array=$_SESSION['age_list']=$_REQUEST['age_list'];
    if($age_list_array[0]=='0'){
        unset($age_list_array[0]);
        if(!empty($age_list_array)){
        $postdata_all['ageID']=implode(',',$age_list_array);
        }
    }
    else{
        $postdata_all['ageID']=implode(',',$age_list_array);
    }
    
}elseif(isset($_SESSION['age_list']) AND $_SESSION['age_list']!="" AND isset($_REQUEST['search_button'])!='Search'){
    $age_list_array=$_SESSION['age_list'];
    if($age_list_array[0]=='0'){
        unset($age_list_array[0]);
        if(!empty($age_list_array)){
        $postdata_all['ageID']=implode(',',$age_list_array);
        }
    }else{
        $postdata_all['ageID']=implode(',',$age_list_array);
    }
    
}else{
    $age_list_array=array();
    $_SESSION['age_list']="";
}
/*############ INCOME LIST################*/
if(isset($_REQUEST['income_list']) AND $_REQUEST['income_list']!='' AND !empty($_REQUEST['income_list'])){
    $income_list_array=$_SESSION['income_list']=$_REQUEST['income_list'];
    if($income_list_array[0]=='0'){
        unset($income_list_array[0]);
        if(!empty($income_list_array)){
        $postdata_all['incomeID']=implode(',',$income_list_array);
        }
    }else{
        $postdata_all['incomeID']=implode(',',$income_list_array);
    }
    
}elseif(isset($_SESSION['income_list']) AND $_SESSION['income_list']!="" AND isset($_REQUEST['search_button'])!='Search'){
    $income_list_array=$_SESSION['income_list'];
    if($income_list_array[0]=='0'){
        unset($income_list_array[0]);
        if(!empty($income_list_array)){
        $postdata_all['incomeID']=implode(',',$income_list_array);
        }
    }else{
        $postdata_all['incomeID']=implode(',',$income_list_array);
    }
    
}else{
    $income_list_array=array();
    $_SESSION['income_list']="";
}
/*#######################RPV AND NON-RPV#############*/
if(isset($_REQUEST['rpv']) AND $_REQUEST['rpv']!='' AND !empty($_REQUEST['rpv'])){
    $rpv=$_SESSION['rpv']=$_REQUEST['rpv'];
    $postdata_all['Check_rpv']=1; 
    if($rpv=='2'){
      $postdata_all['Check_rpv']=2; 
    }
}elseif(isset($_SESSION['rpv']) AND $_SESSION['rpv']!="" AND isset($_REQUEST['search_button'])!='Search'){
    $rpv=$_SESSION['rpv'];
    $postdata_all['Check_rpv']=1; 
    if($rpv=='2'){
      $postdata_all['Check_rpv']=2; 
    }
       
}else{
    $rpv = 0;
    $_SESSION['rpv']="";
}
/*#######################Private label checkbox#############*/
if(isset($_REQUEST['private_label']) AND $_REQUEST['private_label']!='' AND !empty($_REQUEST['private_label'])){
    $private_label_val=$_SESSION['private_label']=$_REQUEST['private_label'];
    $postdata_all['Check_private_label']=1; 
    if($private_label_val=='2'){
      $postdata_all['Check_private_label']=2; 
    }
}elseif(isset($_SESSION['private_label']) AND $_SESSION['private_label']!="" AND isset($_REQUEST['search_button'])!='Search'){
    $private_label_val=$_SESSION['private_label'];
    $postdata_all['Check_private_label']=1;
    if($private_label_val=='2'){
      $postdata_all['Check_private_label']=2; 
    }
}else{
    $private_label_val = 0;
    $_SESSION['private_label']="";
}

/*############ Promo LIST################*/
if(isset($_REQUEST['promo_list']) AND $_REQUEST['promo_list']!='' AND !empty($_REQUEST['promo_list'])){
    $promo_list_array=$_SESSION['promo_list']=$_REQUEST['promo_list'];
    if($promo_list_array[0]=='0'){
        unset($promo_list_array[0]);
        if(!empty($promo_list_array)){
        $postdata_all['promo_id']=implode(',',$promo_list_array);
        }
    }else{
        $postdata_all['promo_id']=implode(',',$promo_list_array);
    }
    
}elseif(isset($_SESSION['promo_list']) AND $_SESSION['promo_list']!="" AND isset($_REQUEST['search_button'])!='Search'){
    $promo_list_array=$_SESSION['promo_list'];
    if($promo_list_array[0]=='0'){
        unset($promo_list_array[0]);
        if(!empty($promo_list_array)){
        $postdata_all['promo_id']=implode(',',$promo_list_array);
        }
    }else{
        $postdata_all['promo_id']=implode(',',$promo_list_array);
    }
    
}else{
    $promo_list_array=array();
    $_SESSION['promo_list']="";
}

if($page_type!=3 && $sid!=''){
    $postdata_all['current_page']=$page_no;
    $postdata_all['direction']=$dct;  
    $postdata_all['sid']="[".$sid."]";
    $postdata_all['page_type']=$page_type;
}elseif(!empty($postdata_all)){
    $postdata_all['page_type']=$page_type;
}

if(!empty($postdata_all)){
    $postdata=json_encode($postdata_all);
    //echo $postdata;
    //$APISEARCHURL="https://dev02.competiscan.com:5428/es-rpv-search";
    $APISEARCHURL=RPVAPIURL."es-rpv-advance-search";
    $get_data = callAPI('POST', $APISEARCHURL, $postdata);
    $responseSearch = json_decode($get_data, true);
    //print_r($responseSearch);die;
    if(!empty($responseSearch) AND $responseSearch['Status_code']=="200" AND $responseSearch['data']!="No Search returned no results"){
        $numRows=$responseSearch['total_records'];
        //echo json_encode($responseSearch['internal_payload']);
    }
}
$result_show="";
if($numRows > 0) {
    $result_show= '<div class="error" style="float:left;padding-bottom: 10px;">'.$numRows .' Result';
    if($numRows!=1) {
        $result_show.='s';
    }
    $result_show.=' Found in ('.number_format((microtime(true) - $start_time),3).' Seconds)</div>';
}
$postdata_all['records_per_page']=2500;
$posted_download= json_encode($postdata_all);
?>
<!--############################## Start HEADER##################################-->
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
<?php /* to-do: update app to newer jQ using file below
    <script src="js/jquery.js"></script>
*/ ?>
	<script>window.jQuery || document.write('<script src="js/jquery.js">\x3C/script>')</script>
<?php echo isset($HEAD) ? $HEAD : '' ?>
<style>
.combo_boxrpv {
font-size: 11px;
color: #000000;
border: 1px black solid;
width: 210px;
}
</style>
</head>
<body>
<?php 
    include_once("includes/analyticstracking.php");
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
        
        $page_permissions = getPagePermission();        
        if(!empty($page_permissions) AND in_array('power_search',$page_permissions)){
            $show_header_top=true;
        }else{
            $show_header_top=false;
        }

    //}else{
       // $show_header_top=true;
    //}    
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
//echo 'total after TIME Found in ('.number_format((microtime(true) - $start_time),3).' Seconds)';
 }?>
<!--############################## END HEADER##################################-->
<div id="page">
        <div>
        <form name="rpvdashboardForm" id="rpvdashboardForm" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <div class="row">
                <div class="col-sm-3">
                    <div class="bodytext">
                    <div><strong>Retailer</strong></div>
                    <div>
                    <div style="float:left;padding:0px; margin-bottom:30px;">
                        <div id="cotext">
                            <input type="text" id="retail_company" name="retail_company" size="30" class="input_box" value="<?php echo htmlspecialchars($retail_company, ENT_QUOTES); ?>" onchange="checkLookup('clist');" tabindex="1">
                        <br>
                        [<a href="#" onclick="showLook('seltext','showhide','clist',document.forms.rpvdashboardForm.retail_company); return false;" id="showhide" class="HyperLink">Show Lookup</a>]
                        </div>
                        <div id="seltext" style="border: 1px solid rgb(0, 0, 0); padding: 4px; display:none; float: left; background-color: rgb(232, 232, 255);">
                        <iframe name="clist" src="rpv_dashboard_iframe.php?parent_field=retail_company" width="200" height="250" frameborder="0" marginheight="0" marginwidth="0" scrolling="auto"></iframe>
                        </div>
                    </div>
                    </div>
                    <div>&nbsp;</div>
                </div>
                </div>
                <div class="col-sm-3">
                    <div class="bodytext">
                    <div>
                        <strong>Retailer Parent</strong>
                    </div>
                    <div>
                    <div style="float:left;padding:0px; margin-bottom:30px;">
                        <div id="cotextretailparent">
                            <input type="text" name="retail_pcompany" disabled size="30" class="input_box" value="<?php echo htmlspecialchars($retail_pcompany, ENT_QUOTES); ?>" onchange="checkLookup('clistretailparent');" tabindex="2">
                        <br>
                        [<a href="#" onclick="showLook('seltextretailparent','showhide_retailparent','clistretailparent',document.forms.rpvdashboardForm.retail_pcompany); return false;" id="showhide_retailparent" class="HyperLink">Show Lookup</a>]
                        </div>
                        <div id="seltextretailparent" style="border: 1px solid rgb(0, 0, 0); padding: 4px; display:none; float: left; background-color: rgb(232, 232, 255);">
                        <iframe name="clistretailparent" src="rpv_dashboard_retailparent_iframe.php?parent_field=retail_pcompany" width="200" height="250" frameborder="0" marginheight="0" marginwidth="0" scrolling="auto"></iframe>
                        </div>
                    </div>
                    </div>
                    <div>&nbsp;</div>  
                </div>
                </div>
                <div class="col-sm-3">
                    <div class="bodytext">
                    <div><strong>Brand/Company</strong></div>
                    <div>
                    <div style="float:left;padding:0px; margin-bottom:30px;">
                        <div id="cotextbrandcompany">
                            <input type="text" id="brand_company" <?php if($rpv>0){ echo 'disabled'; } ?> name="brand_company" size="30" class="input_box" value="<?php echo htmlspecialchars($brand_company, ENT_QUOTES); ?>" onchange="checkLookup('clistbrandcompany');" tabindex="3">
                        <br>
                        [<a href="#" onclick="showLook('seltextbrandcompany','showhidebrandcompany','clistbrandcompany',document.forms.rpvdashboardForm.brand_company); return false;" id="showhidebrandcompany" class="HyperLink <?php if($rpv>0){ echo 'disabled-link'; } ?>">Show Lookup</a>]
                        </div>
                        <div id="seltextbrandcompany" style="border: 1px solid rgb(0, 0, 0); padding: 4px; <?php if($rpv>0){ echo "display:none;"; }else{ echo "display:none;";} ?> float: left; background-color: rgb(232, 232, 255);">
                        <iframe name="clistbrandcompany" src="rpv_dashboard_brandcompany_iframe.php?parent_field=brand_company" width="200" height="250" frameborder="0" marginheight="0" marginwidth="0" scrolling="auto"></iframe>
                        </div>
                    </div>
                    </div>
                    <div>&nbsp;</div>
                </div>
                </div>
                <div class="col-sm-3">
                    <div class="bodytext">
                    <div><strong>Brand/Company Parent</strong></div>
                    <div>
                   <div style="float:left;padding:0px; margin-bottom:30px;">
                        <div id="cotextbrandcompanyparent">
                            <input type="text" name="brand_pcompany" disabled size="30" class="input_box" value="<?php echo htmlspecialchars($brand_pcompany, ENT_QUOTES); ?>" onchange="checkLookup('clist');" tabindex="3">
                        <br>
                        [<a href="#" onclick="showLook('seltextbrandcompanyparent','showhidebrandcompanyparent','clistbrandcompanyparent',document.forms.rpvdashboardForm.brand_pcompany); return false;" id="showhidebrandcompanyparent" class="HyperLink">Show Lookup</a>]
                        </div>
                        <div id="seltextbrandcompanyparent" style="border: 1px solid rgb(0, 0, 0); padding: 4px; display:none; float: left; background-color: rgb(232, 232, 255);">
                        <iframe name="clistbrandcompanyparent" src="rpv_dashboard_brandcompanyparent_iframe.php?parent_field=brand_pcompany" width="200" height="250" frameborder="0" marginheight="0" marginwidth="0" scrolling="auto"></iframe>
                        </div>
                    </div>
                    </div>
                    <div>&nbsp;</div>
                </div>
                </div>
                
            </div>
            
            <div class="row">
               <div class="col-sm-3">
                    <div class="bodytext">
                    <div><strong>Sector</strong>
                    </div>
                    <div>
                       <select name="sector_list[]" id="sector_list" multiple="multiple" size="3" class="combo_boxrpv" onchange="getCategory();" tabindex="5">
                        <?php 
                            $APISECTORURL=RPVAPIURL.'data/sectors';
                            $get_data = callAPI('GET', $APISECTORURL, false);
                            $response = json_decode($get_data, true);
                            $rows_sector_data=$response['data'];
                            $selectany='selected=selected';
                             $o_count = is_countable($sector_list_array);
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
                <div class="col-sm-3">
                    <div class="bodytext">
                    <div><strong>Category</strong>
                    </div>
                    <div>
                        <select name="category_list[]" id="category_list_data_htm" onchange="getSubCategory();"  multiple="multiple" size="3" class="combo_boxrpv" tabindex="6">
                        <?php 
                        
                         $o_count_sec = is_countable($sector_list_array);
                         $o_count_sec_sess = is_countable($_SESSION['sector_list']);
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
                <div class="col-sm-3">
                    <div class="bodytext">
                    <div><strong>Sub Category</strong>
                    </div>
                    <div>
                        <select name="subcategory_list[]" id="subcategory_list" onchange="getSubSubCategory();" multiple="multiple" size="3" class="combo_boxrpv" tabindex="7">
                        <?php 
                         $o_count_cat = is_countable($category_list_array);
                         $o_count_cat_sess = is_countable($_SESSION['category_list']);
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
                <div class="col-sm-3">
                    <div class="bodytext">
                    <div><strong>Sub Sub Category</strong>
                    </div>
                    <div>
                        <select name="subsubcategory_list[]" id="subsubcategory_list_htm" multiple="multiple" size="3" class="combo_boxrpv" tabindex="8">
                        <?php 
                         $o_count_subsub = is_countable($subcategory_list_array);
                         $o_count_sub_sess = is_countable($_SESSION['subcategory_list']);
                         $selectany='';
                         if ($o_count_subsub == 0 || $o_count_sub_sess==0 || $subsubcategory_list_array[0]=='') {
                             $selectany='selected=selected';
                         }
                        ?>    
                        <option value="0" <?php echo $selectany;?>>Any</option>
                            <?php 
                               if(!empty($subcategory_list_array)){

                                $arrayValue = $subcategory_list_array;
                                $postSector['sectors']=$arrayValue;
                                $posted_data=json_encode($postSector);
                                //echo $posted_data;
                                $APISECTORURL=RPVAPIURL.'data/sectors';
                                if(!empty($posted_data)){
                                $getSector_data = callAPI('POST', $APISECTORURL, $posted_data);
                                $response_subsubcategory = json_decode($getSector_data, true);
                                //echo "<pre>";
                                //print_r($response_subcategory);
                                if(!empty($response_subsubcategory)){
                                    foreach($response_subsubcategory as $getSubSubCateName) {
                                    /*if(!in_array($getCateName['sectorID'],$_SESSION['category_list'])){
                                            continue;
                                        }*/
                                         ?>
                                    <option  <?php if(in_array($getSubSubCateName['sectorID'],$_SESSION['subsubcategory_list'])) { echo "selected"; } ?> value="<?php echo $getSubSubCateName['sectorID'];?>" ><?php echo htmlspecialchars($getSubSubCateName['sectorName'], ENT_QUOTES); ?></option> 
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
            <div class="row">
                <div class="col-sm-3">
                    <div class="bodytext">
                    <div><strong>OCR Search</strong></div>
                    <div>
                        <input type="text"  class="input_box" size="30" name="rpv_data_keyword_search" id="rpv_data_keyword_search" value="<?php if($rpv_data_keyword_search!="") { echo htmlspecialchars($_SESSION['rpv_data_keyword_search'],ENT_QUOTES);}else{echo $rpv_data_keyword_search; } ?>" tabindex="9"/>
                    </div><div>&nbsp;</div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="bodytext">
                    <div><strong>Country</strong>
                    </div>
                    <div>
                        <select name="country_list"  id="country_list" class="combo_boxrpv" tabindex="9" style="margin-bottom:32px;" onChange ="getAllState();">
                            <?php 
                             $APIAgeURL=RPVAPIURL.'country';
                             $getCountry_data = callAPI('GET', $APIAgeURL, false);
                             $response_country = json_decode($getCountry_data, true);
                             $rowcountry_data=$response_country['data'];
                             $selectany='';
                            if ($country_list == 0) {
                                $selectany='selected=selected';
                            }
                            ?>
                            <option value="0" <?php echo $selectany; ?> >All</option>
                            <?php 
                            foreach($rowcountry_data as $countryData){ ?>
                                <option  <?php if($countryData['countryID']==$country_list) { echo "selected"; } ?> value="<?php echo $countryData['countryID'];?>" ><?php echo htmlspecialchars($countryData['countryName'], ENT_QUOTES); ?></option> 
                            <?php 
                            
                            }
                            ?>
                        </select>
                    </div>
                    <div>&nbsp;</div>      
                </div>
                </div>
                <div class="col-sm-3">
                    <div class="bodytext">
                    <div>
                        <strong>State/Province</strong>
                    </div>
                    <div>
                        <select name="state_list[]" id="state_list_html"  multiple="multiple" size="3" class="combo_boxrpv" tabindex="10"">
                            <?php 
                            $country=$country_list;
                            if($country=='1'){
                                $country_data='US';
                            }elseif($country=='3'){
                                $country_data='CA';
                            }else{
                                $country_data=$country;
                            }
                            $postCountry['countryid']=$country_data;
                            $posted_data=json_encode($postCountry);
                            //echo $posted_data; die;
                            $APIStateURL=RPVAPIURL.'state';
                            if($country_data=='0'){
                                $getState_data = callAPI('GET', $APIStateURL, false); 
                            }else{
                                $getState_data = callAPI('POST', $APIStateURL, $posted_data); 
                            }
                             $response_state = json_decode($getState_data, true);
                             $rowstate_data=$response_state['data'];
                             $selectany='selected=selected';
                             $o_count = count($state_list_array);
                            $selectany='';
                            if ($o_count == 0) {
                                $selectany='selected=selected';
                            }
                            ?>
                            <option value="0" <?php echo $selectany; ?> >Any</option>
                            <?php 
                            foreach($rowstate_data as $stateData){ ?>
                                <option  <?php if(in_array($stateData['stateID'],$state_list_array)) { echo "selected"; } ?> value="<?php echo $stateData['stateID'];?>" ><?php echo htmlspecialchars($stateData['stateName'], ENT_QUOTES); ?></option> 
                            <?php 
                            
                            }
                            ?>
                        </select>
                    </div>
                    <div>&nbsp;</div>  
                </div>
                </div>
                <div class="col-sm-3">
                    <div class="bodytext">
                    <div>
                        <strong>Age</strong>
                    </div>
                    <div>
                        <select name="age_list[]"  id="age_list" multiple="multiple" size="3" class="combo_boxrpv city_list" tabindex="11">
                            <?php 
                             $APIAgeURL=RPVAPIURL.'age';
                             $getage_data = callAPI('GET', $APIAgeURL, false);
                             $response_age = json_decode($getage_data, true);
                             $rowsage_data=$response_age['data'];
                             $selectany='selected=selected';
                             //echo "<pre>";
                             //print_r($rowsage_data);
                             //echo "</pre>";
                            $o_count = count($age_list_array);
                            $selectany='';
                            if ($o_count == 0) {
                                $selectany='selected=selected';
                            }
                            ?>
                            <option value="0" <?php echo $selectany; ?> >Any</option>
                            <?php 
                            foreach($rowsage_data as $ageData){ ?>
                                <option  <?php if(in_array($ageData['ageid'],$age_list_array)) { echo "selected"; } ?> value="<?php echo $ageData['ageid'];?>" ><?php echo htmlspecialchars($ageData['age'], ENT_QUOTES); ?></option> 
                            <?php 
                            
                            }
                            ?>
                        </select>
                    </div>
                    <div>&nbsp;</div>  
                </div>
                </div>
                
            </div>
            <div class="row">
            <div class="col-sm-3">
                <div class="bodytext">
                <div>
                    <strong>Income</strong>
                </div>
                <div>
                    <select name="income_list[]"  id="income_list" multiple="multiple" size="3" class="combo_boxrpv city_list" tabindex="12">
                        <?php 
                        $APIIncomeURL=RPVAPIURL.'income';
                        $get_income_data = callAPI('GET', $APIIncomeURL, false);
                        $response_income = json_decode($get_income_data, true);
                        $rows_income_data=$response_income['data'];
                        $income_count=count($income_list_array);
                        $selectany="";
                        if ($income_count=='0') {
                            $selectany='selected=selected';
                        }
                        ?>
                        <option value="0" <?php echo $selectany; ?> >Any</option>
                        <?php 
                        if(!empty($rows_income_data)){
                            foreach($rows_income_data as $getIncomeData ){ ?>
                            <option  <?php if(in_array($getIncomeData['incomeID'],$income_list_array)) { echo "selected"; } ?> value="<?php echo $getIncomeData['incomeID'];?>" ><?php echo htmlspecialchars($getIncomeData['incomeName'], ENT_QUOTES); ?></option> 
                            <?php 
                            }
                        }
                        ?>
                    </select>
                </div>
                <div>&nbsp;</div>  
            </div>
            </div>
            <div class="col-sm-3">
                    <div class="bodytext">
                    <div><strong>From Date</strong></div>
                    <div>
                      <input placeholder="YYYY-MM-DD" type="text" name="from_date" id="from_date" size="28" maxlength="20" readonly class="input_box" value="<?php if($from_date!="") { echo $from_date;}else{echo $from_date; } ?>" tabindex="13"/> 
                      <a href="#" onclick="displayCalendar(document.rpvdashboardForm.from_date,'yyyy-mm-dd',this); return false;">
                          <img name="popcal3" src="js_calendar/images/getcal.gif" border="0" alt="" style="vertical-align:bottom;" />
                      </a>  
                    </div>
                    <div>&nbsp;</div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="bodytext">
                    <div><strong>To Date</strong></div>
                    <div>
                       <input placeholder="YYYY-MM-DD" type="text" name="to_date" id="to_date" size="28" maxlength="20" readonly class="input_box" value="<?php if($to_date!="") { echo $to_date;}else{echo $to_date; } ?>" tabindex="14" /> 
                       <a href="#" onclick="displayCalendar(document.rpvdashboardForm.to_date,'yyyy-mm-dd',this); return false;">
                           <img name="popcal3" src="js_calendar/images/getcal.gif" border="0" alt="" style="vertical-align:bottom;" />
                       </a> 
                    </div>
                    <div>&nbsp;</div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div style="display: flex;   justify-content: space-around;     font-size: 12px; color: #505050; text-decoration: none;line-height: 18px;">
                        <div>
                        <?php $private_label_text = 'Private Label';
                        if($private_label_val=='2'){
                            $private_label_text = 'Non-Private Label';
                            $private_label_value = '2';
                        }
                        else{
                            $private_label_value = '1';
                        } ?>
                        <div><strong><span id="pri_label" onclick="changePrivateLabel(); return true;" style="cursor: pointer;"><?php echo $private_label_text; ?></span></strong></div>
                        <div>
                            <input type="checkbox" id="private_label"  <?php if($private_label_val==$private_label_value){ echo "checked";} ?> name="private_label" value="<?php echo $private_label_value; ?>" tabindex="16">
                        </div>
                        </div>
                        <?php $rpvtext = 'RPV';
                        if($rpv=='2'){
                            $rpvtext = 'Non-RPV';
                            $rpv_val = '2';
                        }
                        else{
                            $rpv_val = '1';
                        } ?>
                        <div>
                            <div><strong><span id="rpv_id" onclick="changeRPV(); return true;" style="cursor: pointer;"><?php echo $rpvtext;?></span></strong></div>
                            <div>
                                <input type="checkbox" <?php if($rpv==$rpv_val){ echo "checked";} ?> id="rpv" name="rpv" value="<?php echo $rpv_val;?>" tabindex="17">
                            </div>
                    </div>
                    </div>
                </div>
            </div>
            </div>
            <div class="row">
                <div class="col-sm-3">
                    <div class="bodytext">
                    <div>
                        <strong>Promo Type</strong>
                    </div>
                    <div>
                        <select name="promo_list[]"  id="promo_list" multiple="multiple" size="3" class="combo_boxrpv" tabindex="12">
                            <?php 
                            $APIPromoURL=RPVAPIURL_UAT.'company/promoid';
                            $get_promo_data = callAPI('GET', $APIPromoURL, false);
                            $response_promo = json_decode($get_promo_data, true);
                            $rows_promo_data=$response_promo['data'];
                            $promo_count=count($promo_list_array);
                            $selectany="";
                            if ($promo_count=='0') {
                                $selectany='selected=selected';
                            }
                            ?>
                            <option value="0" <?php echo $selectany; ?> >Any</option>
                            <?php 
                            if(!empty($rows_promo_data)){
                                foreach($rows_promo_data as $getPromoData ){ ?>
                                <option  <?php if(in_array($getPromoData['id'],$promo_list_array)) { echo "selected"; } ?> value="<?php echo $getPromoData['id'];?>" ><?php echo htmlspecialchars($getPromoData['promo_name'], ENT_QUOTES); ?></option> 
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
        <div class="clearfix"></div>  
        <div>
          <input class="submitbutton" type="submit" name="search_button"  value="Search"/> &nbsp; 
          <input class="submitbutton" type="submit" name="clear_search"  value="Clear Search" />
        </div>
        </form>
    </div>
    <div>
        <a name="info_container_top" style="visibility:hidden;">&nbsp;</a>
    </div>
    <div class="clearfix"></div>
    <?php if($result_show!=''){echo $result_show;} ?>
    <!--<div class="error" style="float:left;">11507 Results Found in (1.641 Seconds)</div>-->
    <div class="clearfix"></div>
    <div id="info_container222">
        <form name="resultForm" id="resultForm" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" onsubmit="return false;">
            <div style="border:solid 1px #0055E3;">
                <table width="100%" cellpadding="4" cellspacing="0" class="sortable">
                    <thead>
                        <tr>
                            <th class="toptable" nowrap="nowrap">Expand/Collapse</th>
                            <th class="toptable" nowrap="nowrap">
                                Retailer
                                <img src="images/spacer.gif" border="0" style="vertical-align:bottom;" width="15" height="15">
                            </th>
                            <th class="toptable" nowrap="nowrap">
                                Entry ID
                                <img src="images/spacer.gif" border="0" style="vertical-align:bottom;" width="15" height="15">
                            </th>
                            <!--<th class="toptable" nowrap="nowrap">
                                EVE
                            </th>-->
                            <th class="toptable" nowrap="nowrap">
                                RPV
                            </th>

                        </tr>
                    </thead>
                    <tbody>
                    <?php 
                    //echo "<pre>";
                    //print_r($responseSearch);
                    if(!empty($responseSearch) AND $responseSearch['Status_code']=="200" AND $responseSearch['data']!='No Search returned no results'){
                    $numRows=$responseSearch['total_records'];
                    $allbrand='';
                    $sumEVE="";
                    $sumRPV="";
                    $Country="";
                    $StateName="";
                    $promo_type="";
                    $productIDArray=Array();
                    if($numRows>0){
                        $sumEVE="";
                        $sumRPV="";
                        $rpv_primary="";
                        $productids_array=array();
                        foreach($responseSearch['data'] as $searchResultData){
                            $productids_array[]=$searchResultData['ProductID'];
                            $EntryID=$searchResultData['EntryID'];
                            $ProductID=$searchResultData['ProductID'];
                            $PrimaryCompany=$searchResultData['PrimaryCompany'];
                            $AdditionalCompany=$searchResultData['AdditionalCompany'];
                            $ProductHeadline=$searchResultData['ProductHeadline'];
                            $Country=$searchResultData['country'];
                            $StateName=$searchResultData['state'];
                            $allbrand=$searchResultData['brandcompany'];
                            $sumEVE=$searchResultData['EVE'];
                            $sumRPV=$searchResultData['RPV'];
                            $promo_type=$searchResultData['promo_id'];
                            $rpv_primary=$searchResultData['primary_company_rpv'];
                            ############## PRIVATE LABEL TO COMPANY######################
                            $private_label_company=$searchResultData['private_label_company'];
                            if(ctype_upper($private_label_company)){
                                $private_label_company=ucwords(strtolower($private_label_company));
                            }
                            if($private_label_company!="" && $allbrand!='' && $private_label_val==1){
                                $allbrand = highlightAndReplaceStrings(ucwords(strtolower($allbrand)), ucwords(strtolower($private_label_company))); 
                            }
                            ##########START PEVE from csv1 table cscan_panelist_product########
                            if($searchResultData['is_annotated']!=1 && $sumEVE==""){
                               $queryPanelistProd = "select SUM(ppeve) as ppeve from cscan_panelists_product where ppeve>0 AND productID=$ProductID";   
                               $query_result_prod_panelist = $DRW->query($queryPanelistProd,$DRW_read);
                               $numrows_pp=$DRW->num_rows($query_result_prod_panelist);                            
                               if($numrows_pp>0){
                               $prod_panelistData=$DRW->fetch_row($query_result_prod_panelist);
                               $sumEVE=$prod_panelistData[0];
                                }
                            }
                            ##########END PEVE from csv1 table cscan_panelist_product########
                            $displayAnotationLink='';
                            $imgSRC="";
                            $queryProdEmail = "select muid from cscan_product_email where productID=$ProductID";   
                            $query_result_prod_email = $DRW->query($queryProdEmail,$DRW_read);
                            $numrows2=$DRW->num_rows($query_result_prod_email);                            
                            if($numrows2>0){
                            $prodEmailData=$DRW->fetch_row($query_result_prod_email);
                            $muID=$prodEmailData[0];
                            $displayAnotationLink=ANNOTATIONTOOLDATAURL.$muID;
                            $styleType='vertical-align:top;';
                                $imgSRC='settings-anotation.png';
                                if($searchResultData['is_annotated']==1){
                                    $styleType='color:red;vertical-align:top;';
                                    $imgSRC='annotaion-img.png';
                                }
                            }    
                    ?>
                        <tr>
                            <td class="bodytext" valign="top">
                                <a href="#" onclick="show_result_detail('<?php echo $ProductID; ?>'); return false;">
                                    <img name="detail_img_<?php echo $ProductID; ?>" id="detail_img_<?php echo $ProductID; ?>" src="images/plus.jpg" border="0"></a>
                            </td>
                            <td class="bodytext" valign="top"><?php echo htmlspecialchars($PrimaryCompany, ENT_QUOTES); ?></td>
                            <td class="bodytext" valign="top"><?php echo $EntryID; ?></td>
                            <!--<td class="bodytext" valign="top"><span id="sum_eve_<?php //echo $ProductID;?>"><?php //if($sumEVE!=""){echo number_format($sumEVE);} ?></span></td>-->
                            <td class="bodytext" valign="top"><span id="sum_rpv_<?php echo $ProductID;?>"><?php if($sumRPV!=""){echo number_format($sumRPV);} ?></span></td>
                        </tr>
                        <tr style="display:none;" id="detail_<?php echo $ProductID; ?>">
                            <td colspan="9" class="bodytext" valign="top">
                                <div><b>Parent Retailer</b>:</div>
                                <div><b>Retailer</b>:<span id="retail_cmp_<?php echo $ProductID; ?>"><?php if($rpv_primary!=""){ echo $rpv_primary; }else{echo htmlspecialchars($PrimaryCompany, ENT_QUOTES);} ?></span></div>
                                <div><b>Parent Brand/Company</b>:</div>
                                <div><b>Brand/Company</b>:<span id="brand_cm_<?php echo $ProductID;?>"> <?php echo $allbrand;?></span></div>
                                <div><b>Headline</b>: <?php echo mb_convert_encoding(html_entity_decode( str_replace("\xE2\x80\x8B", "",$ProductHeadline),ENT_QUOTES, "UTF-8"), "HTML-ENTITIES","UTF-8"); ?></div>
                                <div><b>Private Label</b>: <?php if($private_label_company!=''){ echo $private_label_company; } ?> </div>
                                <div><b>Promo Type</b>: <?php if($promo_type!=''){ echo $promo_type; } ?> </div>
                                <div><b>Country</b>: <?php if($Country!="" && $Country!='null'){echo $Country; } ?></div>
                                <div><b>State</b>: <?php if($StateName!="" && $StateName!='null'){echo $StateName; } ?></div>
                                <div><b>Creative</b>: <a class="bluelink" target="_blank" href="<?php echo $displayAnotationLink; ?>"  title="Anotation Tool Link">
                                <img width="15" height="15" src="<?php echo $imgSRC; ?>" border="0" style="<?php echo $styleType; ?>" /></a></div>
                                <div><b>PDF Content</b>: <div style="display:inline-block;"><a class="bluelink" target="_blank" href="<?php echo 'productDocuments.php?id='.$ProductID; ?>" onclick="pdfWin(<?php echo $ProductID; ?>); return false;" title="PDF Content"><img src="images/pdf.jpg" border="0" style="vertical-align:top;" />PDF</a></div></div>
                                
                            </td>
                        </tr>
                <?php }
                    }}else{
                        
                    ?>   
                        <tr>
                            <td colspan="6" class="bodytext" valign="top" align="center">
                                <span class="error" > No Record Found!</span>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
                        
            </div>
            <div class="clearfix"></div>
                <div class="text-center">
                    <?php if($numRows > 0) { 
                    $current_page= $responseSearch['current_page'];
                    $total_records = $numRows;
                    $total_pages = $responseSearch['total_pages'];
                    $sidNextArray=array();
                    $sidPreviousArray=array();
                    foreach($responseSearch['pages'] as $resultFindSearchId){
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
                            echo "<li><a href='?page_no=1&page_type=1'>First Page</a></li>";
                            } ?>
                        <li <?php if($current_page <= 1){ echo "class='disabled'"; } ?>>
                            <a <?php if($current_page > 1){
                            echo "href='?page_no=$previous_page&sid=$firstsid&page_type=2'";
                            } ?>>Previous</a>
                        <?php  //echo "<a href='?page_no=$previous_page&sid=$firstsid&page_type=2'><< Previous</a>"; ?>
                        </li>
                        <?php foreach($responseSearch['pages'] as $resultPagination){
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
                                echo "<li><a href='?page_no=$pg_no&sid=$sid&dct=$dct$page_type'>$pg_no</a></li>";
                                }
                            }
                        ?>
                        <li <?php if($current_page >= $total_pages){
                        echo "class='disabled'";
                        } ?>>
                        <a <?php if($current_page < $total_pages) {
                            $page_type='&page_type=2';
                            if($current_page == $total_pages){
                            
                                $page_type='&page_type=2';
                            }
                        echo "href='?page_no=$next_page&sid=$nextsid$page_type'";
                        } ?>>Next</a>
                        </li>
                        <?php if($total_pages >=11){
                        ?>
                        <li <?php if($current_page >= $total_pages){
                        echo "class='disabled'";
                        } ?>>
                        <?php echo "<a href='?page_no=$total_pages&page_type=3'>Last &rsaquo;&rsaquo;</a>"; ?>
                        </li>
                        <?php } ?>      
                </ul>
                    <?php } ?>
            </div>
            <div class="clearfix"></div>
       </form>
    </div>
    <div>&nbsp;</div>
    <div>
        <input class="submitbutton" type="submit" name="top_button1" id="top_button1" value="Top" onclick="move_page_top();
                return false;" />
        <?php if($numRows > 0) {  ?>
        <button class="submitbutton myExcel_download"> Export To CSV</button>
        <?php } ?>
       
    </div>	
</div>                                                                 
<?php include 'footer_bottom.php';
function highlightAndReplaceStrings($str1, $str2) {
    // Step 1: Extract company names from $str and store in an array
    $companiesStr = explode("; ", $str1);
    $companyList = [];
    foreach ($companiesStr as $companyStr) {
        $companyList[] = $companyStr;
    }

    // Step 2: Extract company names from $str2
    $companiesStr2 = explode(", ", $str2);
    $companyNamesToHighlight = [];
    foreach ($companiesStr2 as $companyStr2) {
        $companyNamesToHighlight[] = $companyStr2;
    }
		//print_r($companyNamesToHighlight); die;
    // Step 3: Compare and find matches, then replace with highlighted versions
    foreach ($companyNamesToHighlight as $company) {
        if (isset($companyList)) {
            $str1 = str_replace($company, '<b>' . $company . '</b>', $str1);
        }
    }

    return $str1;
}
/*$str = "Adidas $972.75; Asics $34.24; Billabong $54.56; Brooks $369.23; Callaway Golf Company $43.4; Crocs $89.68; Footjoy $149.87; Gci Outdoor $131.13; Hydro Flask $39.41; Igloos Outdoor $35.81; Mlb $10.25; Ncaa $329.85; Nfl Shop $13.12; Nike $1,566.33; Nordictrack $321.84; Patagonia $482.44; Quest Outdoors $340.7; Rawlings Sporting Goods $51.77; Stanley $39.97; The North Face $96.44; Under Armour $772.17; Wilson Sporting Goods $50.28";
$str2 = "NCAA,Patagonia";

$updatedStr = highlightAndReplaceStrings(ucwords(strtolower($str)), ucwords(strtolower($str2)));
echo $updatedStr;*/
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
.disabled-link {
  pointer-events: none;
}
</style>
<div id="overlay" style="display:none;">
    <div class="spinner"></div>
</div>
<script>
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
            url: "load_ajax_rpvdata.php",
            data: {sector_list:str,cat_list:cat_id,action:'getCategoryData'},
            success: function(data){
                //console.log(data);
                $("#category_list_data_htm").html(data);
                getSubCategory();
            }
        });
    }else{
        $('#category_list_data_htm').html("<option selected value=''>Any</option>");  
        $('#subcategory_list').html("<option selected value=''>Any</option>");
        $('#subsubcategory_list_htm').html("<option selected value=''>Any</option>"); 
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
                        url: "load_ajax_rpvdata.php",
                        data: {cat_list:cat_id,subcat_id:subcat_id,action:'getSubCat',},
                        success: function(data){
                         $('#subcategory_list').html(data);
                         getSubSubCategory();
                        }
                });
            } else{
               $('#subcategory_list').html("<option selected value=''>Any</option>");
               $('#subsubcategory_list_htm').html("<option selected value=''>Any</option>"); 
               
            }
     }
     function getSubSubCategory() {
            var str='';
            var strsubsubcat='';
            var subcat_id='';
            var val=document.getElementById('subcategory_list');
            //alert(val);
            for (i=0;i< val.length;i++) { 
                if(val[i].value!=0){
                    if(val[i].selected){
                        str += val[i].value + ','; 
                    }
                }
            }
            if(str.length>0){
                var subcat_id=str.slice(0,str.length -1);
            }          
           
            var val_sub_cat=document.getElementById('subsubcategory_list_htm');
            for (i=0;i< val_sub_cat.length;i++) { 
                 if(val_sub_cat[i].value!=0){
                    if(val_sub_cat[i].selected){
                        strsubsubcat += val_sub_cat[i].value + ','; 
                    }
                } 
            }
            if(strsubsubcat.length>0){
                var subsubcat_id=strsubsubcat.slice(0,strsubsubcat.length -1);
            }
           //console.log(subcat_id);
           //console.log(subsubcat_id);
            if(subcat_id!=""){
                $.ajax({          
                        type: "POST",
                        url: "load_ajax_rpvdata.php",
                        data: {subcat_id:subcat_id,subsubcat_id:subsubcat_id,action:'getSubSubCat',},
                        success: function(data){
                         $('#subsubcategory_list_htm').html(data);
                        }
                });
            } else{
               $('#subsubcategory_list_htm').html("<option selected value=''>Any</option>");
               
            }
     }
</script>
<script> 
function getAllState(){
    var country=document.getElementById('country_list').value;
    var str_state='';
    var val=document.getElementById('state_list_html');
    for (i=0;i< val.length;i++) { 
        if(val[i].selected){
            str_state += val[i].value + ','; 
        }
    }         
    var str_state=str_state.slice(0,str_state.length -1);
    $.ajax({          
            type: "POST",
            url: "load_ajax_rpvdata.php",
            data: {country:country,state_list:str_state,action:'getStateData'},
            success: function(data){
                //console.log(data);
                $("#state_list_html").html(data);
            }
        });
}
<?php if($numRows>0){ ?>
$('.myExcel_download').click(function() {
    var source=<?php  echo $posted_download; ?>;
    var fileName="<?php echo $filename_csv; ?>";
    var startTime = new Date().getTime();
    var total_record="<?php echo $numRows; ?>";
        $.ajax({          
        type: "POST",
        url: "load_ajax_rpvdata.php",
        data: {post_data:JSON.stringify(source),action:'express_download'},
        beforeSend: function () {
        $('#overlay').css("display", "block");
        },
        complete: function (resp) {
        console.log(resp);
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
<?php } ?>
</script>
<script>
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
//Show Retail company Lookup
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
        console.log(iframe_id);
        if(top.frames && top.frames[iframe_id] && top.frames[iframe_id].document){
               top.frames[iframe_id].doSel();
	}
}
function pdfWin(pid) {
	var winy = window.open(from_admin+'productDocuments.php?id='+pid+pdfsearch,"pdf"+pid,"menubar=yes, status=no, location=no, toolbar=yes, left=0, top=0, scrollbars=yes, resizable=yes");
	winy.focus();
}

function changePrivateLabel(){
	var obj = document.getElementById('pri_label');
	var ws_val = document.forms.rpvdashboardForm.private_label.value;
	if(ws_val==1){
		document.forms.rpvdashboardForm.private_label.value = 2;
		my_innerHTML_text(obj,'Non-Private Label');
	}
	else{
		document.forms.rpvdashboardForm.private_label.value = 1;
		my_innerHTML_text(obj,'Private Label');
	}
}
function changeRPV(){
	var obj = document.getElementById('rpv_id');
	var ws_val = document.forms.rpvdashboardForm.rpv.value;
	if(ws_val==1){
		document.forms.rpvdashboardForm.rpv.value = 2;
		my_innerHTML_text(obj,'Non-RPV');
	}
	else{
		document.forms.rpvdashboardForm.rpv.value = 1;
		my_innerHTML_text(obj,'RPV');
	}
}
var checker = document.getElementById('rpv');
var sendbtn = document.getElementById('brand_company');
var showhidebrandcompany = document.getElementsByClassName('companylook_disable');
checker.onchange = function() {
sendbtn.disabled = !!this.checked;
//console.log("check", !!this.checked);
if(!!this.checked){
    document.getElementById("seltextbrandcompany").style.display = "none";
    $("#showhidebrandcompany").addClass("disabled-link");
    }else{
        $("#showhidebrandcompany").removeClass("disabled-link");   
    }
};
</script>