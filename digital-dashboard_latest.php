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
require_once('includes/digital-dashboard-function.php');
if(!defined('ENV')){
    define('ENV',getenv('SERVER_NAME'));
}

$page_permission = getPagePermission();
if(!empty($_SESSION['sess_search_page_permission'])){
    $page_permission=$_SESSION['sess_search_page_permission'];
}


// page permission
if(!in_array('digital_dashboard',$page_permission)) {
    ob_end_clean();
    header("Location: fullsearch.php?searchview=2");
    exit;
}

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
        case "DELETE":
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
        if ($data) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        break;
        case "GET":
            curl_setopt($curl, CURLOPT_URL, $url);
            break;
       default:
          if ($data)
             $url = sprintf("%s?%s", $url, http_build_query($data));
    }
    // OPTIONS:
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json','User-Agent:'.$_SERVER['HTTP_USER_AGENT'], 'X-Forwarded-For:'.$_SERVER['REMOTE_ADDR']));
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
//$company="";
$wheresearchtitle='';
$digitalidsarray=array();
$digital_source=array();
$mchannel=array();
$sector_list_array=array();
$category_list_array=array();
$subcategory_list_array=array();
$media_channel="";
$country='';
$state =array();
$postdigita_data=array();

$city=array();
$publisher="";
$advertiser_domain="";
$title="";
$from_date="";
$to_date="";
// $sdate=date("2021-01-01");
// $cdate=date("2021-02-01");
$sdate=date("Y-m-01");
$cdate=date("Y-m-d");
$where="";
$pagelimit=$postdigita_data['page_size']   = 20;
if(isset($_GET['p'])) {
	$p = (int)$_GET['p'];
    //$page_num=(int)$p/20;
    $page_num = ceil($p / $pagelimit);
    if($page_num==0){
        $page_num=1;
    }
    $postdigita_data['page_number'] = $page_num;
}
else {
	$p = 0;
    $postdigita_data['page_number']=1;
}

// $postdigita_data['page_number']=$p;
// $pagelimit=$postdigita_data['page_size']=20;
//$p = isset($_GET['p']) && $_GET['p'] > 0 ? (int)$_GET['p'] : 1;

$pagelimit=$postdigita_data['page_size']   = 20;
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
        //$cdate=date("2021-02-01");
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
    $postdigita_data['from_date']=$from_date;
}else{
     $postdigita_data['from_date']=$sdate;
}
if(isset($_REQUEST['to_date']) AND $_REQUEST['to_date']!="") {
    $to_date =$_REQUEST['to_date'];
    $postdigita_data['to_date']=$to_date;
}else{
    $postdigita_data['to_date']=$cdate;
    
}

############ Company################*/
if(isset($_REQUEST['company']) AND $_REQUEST['company']!='' and !empty($_REQUEST['company'])) {
    $company=$_SESSION['company']=$_REQUEST['company'];
    $postdigita_data['companyName']= explode(' or ', str_replace('"', '', $_REQUEST['company']));
}elseif(isset($_SESSION['company']) AND $_SESSION['company']!="" AND !empty($_SESSION['company']) AND isset($_REQUEST['search_button'])!='Search'){
    $company=$_SESSION['company'];
    $postdigita_data['companyName']= explode(' or ', str_replace('"', '', $_SESSION['company']));
}else{
    $company="";
    $_SESSION['company']="";
    
}
//echo $postdigita_data['companyName'];

if(isset($_REQUEST['mchannel']) AND !empty($_REQUEST['mchannel'])) {
    if($_REQUEST['mchannel'][0]!=''){
      $mchannel = $_SESSION['mchannel']=$_REQUEST['mchannel'];
      $mchannel_id = array_map('intval', $mchannel);
      $postdigita_data['mchannel']=$mchannel_id;
    }else{
       $mchannel = $_SESSION['mchannel']=$_REQUEST['mchannel']; 
       unset($mchannel[0]);
       if(!empty($mchannel)){
       $mchannel_id = array_map('intval', $mchannel);
       $postdigita_data['mchannel']=$mchannel_id;
       
       }
    }
}else if(!empty($_SESSION['mchannel'])){
       if($_SESSION['mchannel']['0']!=""){
        $mchannel=$_SESSION['mchannel'];
        $mchannel_id = array_map('intval', $mchannel);
        $postdigita_data['mchannel']=$mchannel_id;
        
       }
}


if(isset($_REQUEST['digital_source']) AND !empty($_REQUEST['digital_source'])) {
    if($_REQUEST['digital_source'][0]!=''){
      $digital_source =$_SESSION['digital_source']= $_REQUEST['digital_source'];
      $dsid = array_map('intval', $_SESSION['digital_source']);
      $postdigita_data['digital_source']=$dsid;
    }else{
       $digital_source = $_SESSION['digital_source']=$_REQUEST['digital_source']; 
       unset($digital_source[0]);
       if(!empty($digital_source)){
       $dsid = array_map('intval', $_SESSION['digital_source']);
       $postdigita_data['digital_source']=$dsid;
       }
    }
}else if(!empty($_SESSION['digital_source'])){
       if($_SESSION['digital_source'][0]!=""){
        $digital_source=$_SESSION['digital_source'];
        $dsid = array_map('intval', $_SESSION['digital_source']);
        $postdigita_data['digital_source']=$dsid;
       }
}
/*############ START SECTOR LIST################*/
//$whereAndQuerySector="";
if(isset($_REQUEST['sector_list']) AND $_REQUEST['sector_list']!="" AND !empty($_REQUEST['sector_list'])){
    $sector_list_array=$_SESSION['sector_list']=$_REQUEST['sector_list'];
    if($sector_list_array[0]=='0'){
        unset($sector_list_array[0]);
        if(!empty($sector_list_array)){
        $sectorID=array_map('intval',$sector_list_array);
        $postdigita_data['sector_id']=$sectorID;
        }
    }
    else{
        $sectorID=array_map('intval',$sector_list_array);
        $postdigita_data['sector_id']=$sectorID;
    }
    
}elseif(isset($_SESSION['sector_list']) AND $_SESSION['sector_list']!="" AND isset($_REQUEST['search_button'])!='Search'){
    $sector_list_array=$_SESSION['sector_list'];
    if($sector_list_array[0]=='0'){
        unset($sector_list_array[0]);
        if(!empty($sector_list_array)){
            $sectorID=array_map('intval',$sector_list_array);
            $postdigita_data['sector_id']=$sectorID;
        }
    }else{
        $sectorID=array_map('intval',$sector_list_array);
        //$sectorID=implode(',',$sector_list_array);
        $postdigita_data['sector_id']=$sectorID;
    }
    
}else{
    $sector_list_array=array();
    $_SESSION['sector_list']="";
}
/*############ Category LIST################*/
if(isset($_REQUEST['category_list']) AND $_REQUEST['category_list']!="" AND !empty($_REQUEST['category_list'])){
    $category_list_array=$_SESSION['category_list']=$_REQUEST['category_list'];
    if($category_list_array[0]=='0'){
        unset($category_list_array[0]);
        if(!empty($category_list_array)){
        $categoryID=array_map('intval',$category_list_array);
        $postdigita_data['category_id']=$categoryID;
        }
    }
    else{
        $categoryID=array_map('intval',$category_list_array);
        $postdigita_data['category_id']=$categoryID;
    }
    
}elseif(isset($_SESSION['category_list']) AND $_SESSION['category_list']!="" AND isset($_REQUEST['search_button'])!='Search'){
    $category_list_array=$_SESSION['category_list'];
    if($category_list_array[0]=='0'){
        unset($category_list_array[0]);
        if(!empty($category_list_array)){
            $categoryID=array_map('intval',$category_list_array);
            $postdigita_data['category_id']=$categoryID;
        }
    }else{
        $categoryID=array_map('intval',$category_list_array);
        $postdigita_data['category_id']=$categoryID;
    }
    
}else{
    $category_list_array=array();
    $_SESSION['category_list']="";
}
/*############ SUBCategory LIST################*/
if(isset($_REQUEST['subcategory_list']) AND $_REQUEST['subcategory_list']!="" AND !empty($_REQUEST['subcategory_list'])){
    $subcategory_list_array=$_SESSION['subcategory_list']=$_REQUEST['subcategory_list'];
    if($subcategory_list_array[0]=='0'){
        unset($subcategory_list_array[0]);
        if(!empty($subcategory_list_array)){
        $subcategoryId=array_map('intval',$subcategory_list_array);
        $postdigita_data['subcategory_id']=$subcategoryId;
        }
    }
    else{
        $subcategoryId=array_map('intval',$subcategory_list_array);
        $postdigita_data['subcategory_id']=$subcategoryId;
    }
    
}elseif(isset($_SESSION['subcategory_list']) AND $_SESSION['subcategory_list']!="" AND isset($_REQUEST['search_button'])!='Search'){
    $subcategory_list_array=$_SESSION['subcategory_list'];
    if($subcategory_list_array[0]=='0'){
        unset($subcategory_list_array[0]);
        if(!empty($subcategory_list_array)){
            $subcategoryId=array_map('intval',$subcategory_list_array);
            $postdigita_data['subcategory_id']=$subcategoryId;
        }
    }else{
        $subcategoryId=array_map('intval',$subcategory_list_array);
        $postdigita_data['subcategory_id']=$subcategoryId;
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

if (!empty($_REQUEST['state'])) {

    // Save in session
    $_SESSION['state'] = $_REQUEST['state'];
    $state = $_SESSION['state'];

    // Remove empty first element if exists
    if (isset($state[0]) && $state[0] === '') {
        unset($state[0]);
    }

    // Prepare state_code
    if (!empty($state)) {
        //print_r($state);
        $postdigita_data['state'] = $state;
    }

} else if (!empty($_SESSION['state'])) {

    $state = $_SESSION['state'];

    // If first element is not empty
    if (!empty($state[0])) {
        //print_r($state);
        $postdigita_data['state'] = $state;
    }
}

//echo $postdigita_data['state'];
if(isset($_REQUEST['city']) AND !empty($_REQUEST['state'])){
    if($_REQUEST['city'][0]!=''){
      $city =$_SESSION['city']=$_REQUEST['city'];
      $postdigita_data['city'] = $city;
      
    }else{
       $city = $_SESSION['city']=$_REQUEST['city']; 
       unset($city[0]);
       if(!empty($city)){
       $postdigita_data['city'] = $city;
       }
    }
}else if(!empty($_SESSION['city'])){
       if($_SESSION['city']['0']!=""){
        $city=$_SESSION['city'];
        $postdigita_data['city'] = $city;
       }
}

$country_filter="";
if($country!=''){
    
     if($country=='usa'){
        $country_filter='usa'; 
     }
     if($country=='canada'){
        $country_filter='canada'; 
     }
$postdigita_data['country']=$country_filter;
   
}
if (isset($_REQUEST['digital_data_keyword_search']) and $_REQUEST['digital_data_keyword_search']!='') {
    $digital_data_keyword_search= $_SESSION['digital_data_keyword_search'] = trim($_REQUEST['digital_data_keyword_search']);
    //echo "a".$postdigita_data['campaign_title']=$digital_data_keyword_search;
}else if(isset($_SESSION['digital_data_keyword_search']) AND $_SESSION['digital_data_keyword_search']!="" AND isset($_REQUEST['search_button'])!='Search'){
    $digital_data_keyword_search=$_SESSION['digital_data_keyword_search'];  
    //echo "b".$postdigita_data['campaign_title']=$digital_data_keyword_search;
}else{
    $digital_data_keyword_search=$_SESSION['digital_data_keyword_search']='';
    //echo "c".$postdigita_data['campaign_title']=$digital_data_keyword_search;
}
######################### Start OCR Search########################
if ($_SESSION['digital_data_keyword_search'] != '' ) {
    $searchKey = $_SESSION['digital_data_keyword_search'];
    $postdigita_data['campaign_title']=$searchKey;
}
######################### End OCR Search########################
// echo "<pre>";
// print_r($postdigita_data);
// echo "</pre>";
echo $posted_digital_data=json_encode($postdigita_data,JSON_UNESCAPED_SLASHES);
echo $APIDIGITALURL=DIGITAL_DASHBOARD_UAT.'dashboard_data';
$get_digital_data = callAPI('POST', $APIDIGITALURL, $posted_digital_data);
$response_digital_data = json_decode($get_digital_data, true);

$num_of_rows = (int)$response_digital_data['total_records'];
$postdigita_data['export']=true;
$posted_download= json_encode($postdigita_data);
$currenttime=time();
$filename_csv = 'Competiscan_Export_'.$currenttime.'_'.date('Y-m-d').'.csv';
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
                        <iframe name="clist" src="digital_company_iframe1.php?parent_field=company" width="278" height="80" frameborder="0" marginheight="0" marginwidth="0" scrolling="auto"></iframe>
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
                        
                        <select name="state[]" id="state_list" multiple="multiple" size="3"
                                    class="combo_box state_list" tabindex="5" onchange="getAllCity();">
                                <?php 
                        
                         $o_count_country = count($country);
                         $o_count_country_sess = count($_SESSION['country']);
                         $selectany='';
                         if ($o_count_country == 0 || $o_count_country_sess==0 || $country=='') {
                             $selectany='selected=selected';
                         }
                        ?>    
                        <option value="" <?php echo $selectany;?>>Any</option>
                            <?php 
                            //print_r($state);
                               if(!empty($country)){

                                $arrayValue = $country;
                                $postState['country']=$arrayValue;
                                $posted_state_data=json_encode($postState);
                                //echo $posted_state_data;
                                $APISTATEURL=DIGITAL_DASHBOARD_UAT.'country';
                                if(!empty($posted_state_data)){
                                $getstate_data = callAPI('POST', $APISTATEURL, $posted_state_data);
                                $response_state = json_decode($getstate_data, true);
                                //print_r($response_state);
                                if(!empty($response_state['data'])){
                                    foreach($response_state['data'] as $getstateName) {
                                        //echo "<br>state_province=".$getstateName['state_province'];
                                        // if(!in_array($getstateName['state_province'],$_SESSION['state'])){
                                        //     continue;
                                        // }
                                         ?>
                                    <option  <?php if(in_array($getstateName['state_province'],$_SESSION['state'])) { echo "selected"; } ?> value="<?php echo $getstateName['state_province'];?>" ><?php echo htmlspecialchars($getstateName['state_province'], ENT_QUOTES); ?></option> 
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
                    <div>
                        <strong>City</strong>
                    </div>
                    <div>
                        <select name="city[]" id="city_list" multiple="multiple" size="3"
                                class="combo_box city_list" tabindex="6">
                                <?php 
                                    $o_count_state = count($state);
                                    $o_count_state_sess = count($_SESSION['state']);
                                    $selectany='';
                                    if ($o_count_state == 0 || $o_count_state_sess==0 || $state=='') {
                                        $selectany='selected=selected';
                                    }
                                ?>    
                             <option value="" <?php echo $selectany;?>>Any</option>
                            <?php
                            if (!empty($state)) {
                                $post = array('country' => $country, 'state' => $state);
                                echo $posted_data = json_encode($post);
                                $API = DIGITAL_DASHBOARD_UAT . 'country';
                                $resp = callAPI('POST', $API, $posted_data);
                                $data = json_decode($resp, true);

                                $sessionCity = !empty($_SESSION['city']) ? $_SESSION['city'] : array();

                                if (!empty($data['data'])) {
                                    foreach ($data['data'] as $row) {
                                        $city = trim($row['city']);
                                        if ($city == '') continue;
                            ?>
                                        <option value="<?php echo htmlspecialchars($city, ENT_QUOTES); ?>"
                                            <?php echo in_array($city, $sessionCity) ? 'selected="selected"' : ''; ?>>
                                            <?php echo htmlspecialchars($city, ENT_QUOTES); ?>
                                        </option>
                            <?php
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
                <div class="col-md-4">
                    <div class="bodytext">
                    <div><strong>Headline Search</strong></div>
                    <div>
                        <input type="text"  class="input_box" size="45" name="digital_data_keyword_search" id="digital_data_keyword_search" value="<?php if($digital_data_keyword_search!=''){echo htmlspecialchars($digital_data_keyword_search, ENT_QUOTES); }else{echo $digital_data_keyword_search;}?>" tabindex="7"/>
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
                         $o_count_sec_sess = count($_SESSION['sector_list']);
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
                         $o_count_cat_sess = count($_SESSION['category_list']);
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
                        if($num_of_rows > 0) {
                        if ($response_digital_data['total_records'] > 0) {
                            $country_name="";
                            $city_name="";
                            $state_name="";
                            $company1="";
                            foreach($response_digital_data['data'] as $row){
                                $id=$row['digital_processed_records_id'];
                                //$process_record_id=$row['digital_record_id'];
                                $advertiser_domain=$row['advertiser_domain'];
                                $spend=$row['spend'];
                                $impressions=$row['impressions'];
                                $creation_date=$row['creation_date'];
                                $digital_source=$row['digital_source'];
                                $media_channel=$row['mchannel'];
                                $searchVal= array("https://biscience.s3.amazonaws.com", "http://biscience.s3.amazonaws.com","https://ads.adclarity.com","http://ads.adclarity.com");
                                $creative_wrapper=str_replace($searchVal,"https://files2.competiscan.com",$row['creative_wrapper']);
                                $campaign_landing_page=$row['campaign_landing_page'];
                                $company1=$row['company_name'];
                                $publisher=$row['publisher'];
                                $title=$row['campaign_title'];
                                $country_name=$row['country'];
                                $state_name=implode(';',$row['state']);
                                $city_name=implode(';',$row['city']);
                                $sectorName="";

                                if($row['sector_name']!="" && !empty($row['sector_name'])){
                                    foreach($row['sector_name'] as $sector_name){
                                       
                                        $sectorName=$sector_name;
                                    }
                                    //$sectorName=$row['sector_name'];

                                }
                                $categoryName="";
                                if($row['category_name']!="" && !empty($row['category_name'])){
                                    foreach($row['category_name'] as $category_name){
                                       
                                        $categoryName=$category_name;
                                    }
                                    
                                }
                                $subCategoryName="";
                                if($row['subcategory_name']!="" && !empty($row['subcategory_name'])){
                                    foreach($row['subcategory_name'] as $subcategory_name){
                                       
                                        $subCategoryName=$subcategory_name;
                                    }
                                    

                                }
                                
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
                        
                    <?php }}}else { ?>
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
            <?php if($num_of_rows <=100000) {  ?>
        <button class="submitbutton myExcel_download"> Export To CSV</button>
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
    var country = [];
    $('#country option:selected').each(function() {
        if ($(this).val()) country.push($(this).val());
    });

    var state = [];
    $('#state_list option:selected').each(function() {
        if ($(this).val() && $(this).val() !== "0") state.push($(this).val());
    });

    if (country.length > 0) {

        $.ajax({
            type: "POST",
            url: "ajax-get-state-city_latest1.php",
            data: {
                country: country.join(','),
                state: state.join(','),
                action: 'getState'
            },
            success: function(data) {
                $('#state_list').html(data);
                getAllCity();
            }
        });

    } else {
        $('#state_list').html("<option selected='selected' value=''>Any</option>");
        $('#city_list').html("<option selected='selected' value=''>Any</option>");
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
            url: "ajax-get-state-city_latest1.php",
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
function getAllCity() {

    var country = $('#country').val();

    var state = [];
    $('#state_list option:selected').each(function () {
        if ($(this).val() !== "0") state.push($(this).val());
    });

    var city = [];
    $('#city_list option:selected').each(function () {
        if ($(this).val()) city.push($(this).val());
    });

    if (country !== '' && state.length > 0) {

        $.ajax({
            type: "POST",
            url: "ajax-get-state-city_latest1.php",
            data: {
                country: country,
                state: state.join(','),
                city: city.join(','),
                action: 'getCity'
            },
            //console:console.warn(data),
            success: function (data) {
                $('#city_list').html(data);
            }
        });

    } else {
        $('#city_list').html("<option selected='selected' value=''>Any</option>");
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
                        url: "ajax-get-state-city_latest1.php",
                        data: {cat_list:cat_id,subcat_id:subcat_id,action:'getSubCat',},
                        success: function(data){
                         $('#subcategory_list').html(data);
                         getSubSubCategory();
                        }
                });
            } else{
               $('#subcategory_list').html("<option selected value='0'>Any</option>");
               
               
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
<?php if($num_of_rows>0){ ?>
$(document).ready(function() {
$(document).on('click', '.myExcel_download', function (e) {
e.preventDefault();
    var source=<?php  echo $posted_download; ?>;
    var fileName="<?php echo $filename_csv; ?>";
    var startTime = new Date().getTime();
    var total_record="<?php echo $num_of_rows; ?>";
        $.ajax({          
        type: "POST",
        url: "ajax-get-state-city_latest1.php",
        data: {post_data:JSON.stringify(source),action:'express_download'},
        beforeSend: function () {
        $('#overlay').css("display", "block");
        },
        success: function (url) {
        //console.log(url);
        var link = document.createElement("a");
        link.href = url;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        $('#overlay').css("display", "none");
        },
    });
});
});
<?php } ?>
</script>
