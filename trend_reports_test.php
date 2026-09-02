<?php 
$PAGE_HEADING = "Trend Reports";
$TITLE = "Competiscan $PAGE_HEADING";
$start_time = microtime(true);
require_once('includes/globalSession.php');
require_once('includes/checklogin.php');
require_once('includes/functions.php');  //latest function
require_once 'class/Trend.php';
require_once('includes/rpv-dashboard-function.php');
if(!defined('ENV')){
    define('ENV',getenv('SERVER_NAME'));
}
/*######## Start for Page permission ########*/ 
// echo "<pre>";
// print_r($_REQUEST);
// echo "</pre>";die;




  
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
        if(!in_array('trend_reports',$page_permission) AND in_array('power_search',$page_permission)){
            $redirect_page=$siteUrl.'fullsearch.php?searchview=2';

        }else if(!in_array('trend_reports',$page_permission) AND !in_array('power_search',$page_permission) AND in_array('retrieval_services',$page_permission)){
            $redirect_page=$siteUrl.'productPickup.php';
        }
        if(!in_array('trend_reports',$page_permission) AND $redirect_page!=''){
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
// echo "<pre>";
// print_r($_SESSION);
// echo "<pre>";
// die;
//############### ADD ENCODE TREND ID############
//if(ENV == 'localhost' || ENV == 'demo.competiscan.com'){
    if(isset($_REQUEST['trend_id']) && $_REQUEST['trend_id']!=''){
        $loc="trendDocument_test.php?id=".trim($_REQUEST['trend_id']);
        ob_end_clean();
        header("Location: $loc");
        exit;
    }

$selectSqlcolumn='';
//if(ENV == 'localhost' || ENV == 'demo.competiscan.com'){
   $selectSqlcolumn ='ctr.rndtrend_id,';
//}
$postdataTrend=array();
$audience_id = array();
$sector_id =array();
//$sector_list_array=array();

$fdt = '';
$tdt = '';
$wheresearchtrend='';
$trendidsarray=array();
$offset1='0';
$display_search_key="";
$sql_country = "SELECT DISTINCT country_id FROM cscan_country_users_allow where userID='".$_SESSION['sess_userID']."'";
$rs_country  = $DRW->query( $sql_country,$DRW_read );
$row_country = $DRW->fetch_array($rs_country);
// echo "<pre>";
// print_r($row_country);
// echo "</pre>"; 
if(isset($_REQUEST['audience_id'])) {
        $audience_id =$_SESSION['sess_audience_id']= $_REQUEST['audience_id'];
        
}
if(isset($_REQUEST['clear_search']) and $_REQUEST['clear_search']=='Clear Search') {
	   
    $audience_id = array();
    $country_id="0";
    $_REQUEST['ocr']='';
    $_REQUEST['search_type_set']='trend_ocr';
    $_SESSION['ocr_search_trend']='';
    $_SESSION['ocr_search_radio']='trend_ocr';
    $_SESSION['sess_audience_id']=array();
    $_REQUEST['sector_list']="";
    $_SESSION['sector_list']="";
    $_SESSION['sess_country_id']= '0';
    $_SESSION['sess_fdt'] ='';
    $_REQUEST['fdt']='';
    $_SESSION['sess_tdt'] =''; 
    $_REQUEST['tdt']='';
    $_REQUEST['country']='0';
    $_SESSION['sess_country_id']='0';
    // echo "<pre>";
    // print_r($_REQUEST);
    // echo "</pre>";
    // echo "<pre>";
    // print_r($_SESSION);
    // echo "</pre>";
    //echo "<pre>";
    //print_r($_SESSION); die;
}
if (!isset($_SESSION['ocr_search_trend']) ||!isset($_SESSION['ocr_search_radio']) || !isset($_SESSION['sess_audience_id']) || !isset($_SESSION['sector_list'])){
    $_SESSION['sess_audience_id']=array();
    $_REQUEST['sector_list']="";
    $_SESSION['sector_list']="";
    $_SESSION['sess_country_id']= '0';
    $_SESSION['sess_fdt'] ='';
    $_REQUEST['fdt']='';
    $_SESSION['sess_tdt'] =''; 
    $_REQUEST['tdt']='';
    $_SESSION['ocr_search_trend']='';
    $_SESSION['ocr_search_radio']='trend_ocr';
    
}
/*############ SECTOR LIST################*/
if(isset($_REQUEST['sector_list']) AND $_REQUEST['sector_list']!='' AND !empty($_REQUEST['sector_list'])){
    $sector_id=$_SESSION['sector_list']=$_REQUEST['sector_list'];
    if($sector_id[0]=='0'){
        unset($sector_id[0]);
        if(!empty($sector_id)){
        $postdataTrend['sector_id']=$sector_id;
        }
    }
    else{
        $postdataTrend['sector_id']=$sector_id;
    }
    
}elseif(isset($_SESSION['sector_list']) AND $_SESSION['sector_list']!="" AND isset($_REQUEST['search'])!='Search'){
    $sector_id=$_SESSION['sector_list'];
    if($sector_id[0]=='0'){
        unset($sector_id[0]);
        if(!empty($sector_id)){
            $postdataTrend['sector_id']=$sector_id;
        }
    }else{
            $postdataTrend['sector_id']=$sector_id;
    }
    
}else{
    $sector_id=array();
    $_SESSION['sector_list']="";
}

if(isset($_REQUEST['country'])) {
	$country_id = $_SESSION['sess_country_id'] = $_REQUEST['country'];
    $postdataTrend['country_id']=$country_id;
}
if(isset($_REQUEST['fdt'])){
         $fdt = $_SESSION['sess_fdt']=trim($_REQUEST['fdt']);
         $postdataTrend['from_date']=$fdt;
    }
if(isset($_REQUEST['tdt'])){
        $tdt = $_SESSION['sess_tdt']=trim($_REQUEST['tdt']);
        $postdataTrend['to_date']=$tdt;
}

if (isset($_REQUEST['ocr'])) {
   $_SESSION['ocr_search_trend'] = trim($_REQUEST['ocr']);
   //$postdataTrend['ocr_search_trend']=$_SESSION['ocr_search_trend'];
}
if (isset($_REQUEST['search_type_set'])) {
   $_SESSION['ocr_search_radio'] = trim($_REQUEST['search_type_set']);
   if($_SESSION['ocr_search_radio']=='trend_fulltext'){
    $postdataTrend['trend_name']=$_SESSION['ocr_search_trend'];
   }else{
    $postdataTrend['dts_val']=$_SESSION['ocr_search_trend'];
   }
   

}
if(isset($_GET['sort'])) $sort = (int) $_GET['sort'];
else $sort = 0;
if (isset($_GET['page_no']) && $_GET['page_no']!="") {
    $page_no = $_GET['page_no'];
    } else {
    $page_no = 1;
    }
$total_records_per_page = 20;
$previous_page = $page_no - 1;
$next_page = $page_no + 1;
$adjacents = "2";
 #################################INTGRATION API################################
 
 $postdataTrend["page_number"]=(int)$page_no;
 $postdataTrend["page_size"]=20;
 $resultCount="";
//  echo "<pre>";
//  print_r($postdataTrend);
//  echo "<pre>";
 if(!empty($postdataTrend)){
     $postdata=json_encode($postdataTrend);
     $ApiTrendSearch=TREND_REPORT_API_UAT_URL.'trend_search_client';
     $GetTrendData = callAPI('POST', $ApiTrendSearch, $postdata);
     $ResTrendData = json_decode($GetTrendData, true);
    //  echo "<pre>";
    //  print_r($ResTrendData);
    //  echo "</pre>"; die;
     if(!empty($ResTrendData && isset($ResTrendData['data']))){
        //  echo "<pre>";
        //  print_r($ResTrendData['data']);
        //  echo "</pre>"; die;
         $resultCount=$ResTrendData['total_records'];
         $total_records = $resultCount;
         $total_no_of_pages = ceil($total_records / $total_records_per_page);
 
         $second_last = $total_no_of_pages - 1; 
         //echo json_encode($responseSearch['internal_payload']);
     }
 }
?>
<?php include 'header_top.php';?>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<style>
    .form-control{
        font-size:12px !important;
    }
    select[size] {
    height: 18px;
    }
</style>
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<div class="headings" id="pcontainer">
    
    <div class="col-md-12 pull-right" style="font-size:14px;padding:14px;margin-top:-34px !important;">
        <span style="float:right;"><a style="text-decoration:underline;" class="submitbutton12" href="#" data-toggle="modal" data-target="#myModal">How It Works</a> </span>
    </div>
    
    <div class="col-md-7">
        <strong>Welcome: <?php echo $_SESSION['sess_username']; ?></strong>
    </div>
    <div class="col-md-5 pull-right" style="font-size:14px;">
        <span style="float:right;"> <a style="text-decoration:underline;" class="submitbutton12" href="trend_email_alert_test.php">Manage Trend Report Email Alerts</a></span>
    </div>   
</div>
<hr style="float:left; width:100%" />
<div class="error" style="float:right;"></div>		
<div  class="left_box" >
<form  name="searchForm" action="trend_reports_test.php" onsubmit="return check_searchform();" method="post" style="border:1px solid #a4a4a4; padding:10px 10px; margin-bottom:10px;">
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
             Keywords:
            </div>
        </div>
        <div class="col-md-9">
            <div class="form-group">
                <input class="form-control" placeholder="Search by Keywords" type="text" name="ocr" value="<?php echo htmlspecialchars($_SESSION['ocr_search_trend'], ENT_QUOTES); ?>">
            </div>
        </div>
    </div>
     <div class="row">
         <div class="col-md-3">
            <div class="form-group">
              
            </div>
        </div>
        <div class="col-md-9">
          <div class="form-group">
            <label><input type="radio" name="search_type_set" <?php if($_SESSION['ocr_search_radio']=='trend_fulltext'){echo 'checked="checked"';} ?> value="trend_fulltext">Full Text<br><span style="font-size:xx-small;">(headlines only)</span></label>&nbsp; &nbsp; &nbsp;
            <label><input type="radio" name="search_type_set" <?php if($_SESSION['ocr_search_radio']=='trend_ocr'){echo 'checked="checked"';} ?> value="trend_ocr">OCR<br><span style="font-size:xx-small;">&nbsp;(all text)</span></label>
          </div>
        </div>
     </div>
   <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                Sector:
            </div>
        </div>
        <div class="col-md-9">
            <div class="form-group">
                <select class="form-control" name ="sector_list[]" id ="sector_list"  size="3" multiple="multiple">
                    <?php 
                        $sector = getSector();
                        foreach($sector as $id=>$name){
                            if(!in_array($id,$_SESSION['sess_sector'])){
                                continue;
                            }
                         if(!empty($id)){ print_r($sector_id); ?>

                        <option  <?php if(in_array($id,$sector_id)) { echo "selected"; } ?> value="<?php echo $id;?>" >
                            <?php echo htmlspecialchars($name, ENT_QUOTES); ?>
                        </option> 
                       <?php }
                        }
                        ?> 
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                 From Date:
            </div>
        </div>
        <div class="col-md-9">
            <div class="form-group">
                 <input type="text" id="fdt" readonly='true' placeholder="From Date" name="fdt" size="20" maxlength="10" class="form-control" value="<?php echo $_SESSION['sess_fdt']; ?>" />
                
            </div>
        </div> 
    </div>
  <div class="row">
         <div class="col-md-3">
            <div class="form-group">
                 To Date:
            </div>
        </div>
        <div class="col-md-9">
            <div class="form-group">
                 <input type="text" id="tdt" readonly='true' placeholder="To Date" name="tdt" size="20" maxlength="10" class="form-control" value="<?php echo $_SESSION['sess_tdt']; ?>" />
                
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                 Country:
            </div>
        </div>
        <div class="col-md-9">
            <div class="form-group">
                <!--add country permission-->
                <?php //if(ENV == 'localhost' || ENV == 'demo.competiscan.com'){ ?>
                <?php if($row_country[0]=='BOTH'){ ?>
                <input type="radio" <?php if(!empty($_SESSION['sess_country_id']) && $_SESSION['sess_country_id']=='1'){ echo "checked";} ?> name="country" value="1">US
                <input type="radio" <?php if(!empty($_SESSION['sess_country_id']) && $_SESSION['sess_country_id']=='3'){ echo "checked";} ?> name="country" value="3">CANADA
                <input type="radio" <?php if($_SESSION['sess_country_id']=='0'){ echo "checked";} ?> name="country" value="0">All
                <?php }elseif($row_country[0]=='US'){  ?>
                <input type="radio" checked name="country" value="1">US Only
                <?php }else{ ?>
                <input type="radio" checked name="country" value="3">CANADA Only
                <?php } ?>
            </div>
        </div>
    </div>
    
   <div class="row">
        <div class="col-md-3">
            <div class="form-group">
               	
            </div>
        </div>
        <div class="col-md-9">
            <div class="form-group">
               <input class="submitbutton" type="submit" name="search" value="Search">
               <input class="submitbutton" type="submit" name="clear_search" value="Clear Search">
               
            </div>
        </div>
    </div>
    
</form>
<form name="trendForm" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<?php 
$sess_userid = $_SESSION['sess_userID'];
$ApiTrendSectorURL=TREND_REPORT_API_UAT_URL.'trends-sector?userid='.$sess_userid;
$catTrendData = callAPI('GET', $ApiTrendSectorURL, null);
$catTrendData = json_decode($catTrendData, true);
if(!empty($catTrendData && isset($catTrendData['data']))){
    foreach($catTrendData['data'] as $sectorData){ 
        // echo "<pre>";
        // print_r($sectorData);
        // echo "</pre>";
        //echo $sectorData['emailAlert'];
        ?>
<div style="text-align:left; font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 13px; font-weight: bold;text-decoration: none;margin:0px 0px 10px 0px;padding:10px 55px;border:solid 1px #a4a4a4;"><?php echo $sectorData['sector_name'];?><br>
    <div style="display: flex; justify-content: space-between; width: 100%;">
     <?php if(!empty($sectorData['trends_data'])){ ?>    
    <select class="form-control" style="font-weight: normal;color:#000000;margin-bottom:-16px;" name="trend<?php echo $sectorData['sector_id']; ?>" size="1" onchange="showTrend(this)">
       
        <option value="">&nbsp;</option>
        
        <?php 
           foreach($sectorData['trends_data'] as $trendlinkData){ ?>
            <option value="<?php echo $trendlinkData['trend_link']; ?>"><?php echo $trendlinkData['trend_name']; ?></option>
            <?php 
           } 
        
        ?>
        </select>
        <input type="checkbox" style="margin-left:12px;" class="form-check-input" name="sectorID" value="<?php echo $sectorData['sector_id']; ?>" <?php if($sectorData['emailAlert']>0){ echo "checked";} ?>>
        <?php } ?>
        
    </div>

<?php 
if(!empty($sectorData['categories'])){
    foreach($sectorData['categories'] as $catData){  ?>
    <div style="margin:8px 0px 0px 0px;text-align:left;font-family: arial;font-size: 12px; text-decoration: none;line-height: 18px;"><?php echo $catData['category_name'];?><br>
    <div style="display: flex; justify-content: space-between; width: 100%;">
     <?php if(!empty($catData['trends_data'])){ ?>        
    <select class="form-control" style="font-weight: normal;color:#000000;margin-bottom:-16px;" name="trend<?php echo $catData['category_id']; ?>" size="1" onchange="showTrend(this)">
           
            <option value="">&nbsp;</option>
            <?php 
            foreach($catData['trends_data'] as $trendlinkcatData){ ?>
                <option value="<?php echo $trendlinkcatData['trend_link']; ?>"><?php echo $trendlinkcatData['trend_name']; ?></option>
                <?php 
            } 
      
            ?>
            </select>
            <input type="checkbox" style="margin-left:12px;" class="form-check-input" name="categoryID" value="<?php echo $catData['category_id']; ?>" <?php if($catData['emailAlert']>0){ echo "checked"; }?>>
            <?php   } ?>
        </div>
    </div>
   <?php if(!empty($catData['subcategories'])){
    foreach($catData['subcategories'] as $catsubData){  ?>
    <div style="margin:8px 0px 0px 0px;font-weight: normal;color: #0055E3;text-align:left;font-family: arial;font-size: 12px; text-decoration: none;line-height: 18px;"><?php echo $catsubData['subcategory_name'];?><br>
    <div style="display: flex; justify-content: space-between; width: 100%;">
    <?php if(!empty($catsubData['trends_data'])){ ?>        
    <select class="form-control" style="font-weight: normal;color:#000000;margin-bottom:-16px;" name="trend<?php echo $catsubData['subcategory_id']; ?>" size="1" onchange="showTrend(this)">
            
            <option value="">&nbsp;</option>
            <?php 
            foreach($catsubData['trends_data'] as $trendlinkcatsubData){ ?>
                <option value="<?php echo $trendlinkcatsubData['trend_link']; ?>"><?php echo $trendlinkcatsubData['trend_name']; ?></option>
                <?php 
            } 
       
            ?>
            </select>
            <input type="checkbox" style="margin-left:12px;" class="form-check-input" name="subCategoryID" value="<?php echo $catsubData['subcategory_id']; ?>" <?php if($catsubData['emailAlert']>0){ echo "checked";} ?>>
        <?php   } ?>
        </div>
    </div>
<?php
    } 
}
   
}
}
?>
</div>
<?php
    }
}
?>
    <div align="left"><input type="button" name="submit" id ="saveindustryalrt" value="Save Selected Alert" class="submitbutton" />
    <input type="hidden" name="userID" value="<?php echo $_SESSION['sess_userID']; ?>" />
    </div>
</form>
</div>
<div class="right_box"> 
    <?php 
    if ($_SESSION['ocr_search_trend'] != '') {
        $searchKey = $_SESSION['ocr_search_trend'];
        $display_search_key.= " ".$searchKey;
    }
     if(!empty($sector_id)) { 
                $sector_id = @implode(",",$sector_id);
                $sectorName= sectorName($sector_id);
                $display_search_key.= " <b>Sector:</b> ".$sectorName;
        }
         if($_SESSION['sess_fdt']!="") { 
                $display_search_key.= " <b>From Date:</b> ".$_SESSION['sess_fdt'];
        }

        if($_SESSION['sess_tdt']!="") { 
                $display_search_key.= " <b>To Date:</b> ".$_SESSION['sess_tdt'];
        }
        if($_SESSION['sess_country_id']) { 
                $country_id = $_SESSION['sess_country_id'];
                if($country_id==1){
                    $countryName ="UNITED STATES";
                    }
                    elseif($country_id==3){
                        $countryName ="CANADA";
                    } else{
                        $countryName ="All";
                    }
                    $display_search_key.= " <b>Country:</b> ".$countryName;
        }else{
                if($row_country[0]=='US'){
                   $country_id='1';
                   $countryName ="UNITED STATES";
                }elseif($row_country[0]=='CA'){
                    $country_id='3';
                    $countryName ="CANADA";
                }else{
                    $countryName ="All";
                }
                $display_search_key.= " <b>Country:</b> ".$countryName;       
        }
         ?>
     <div class=" pull-right">
        <?php  if(!empty($audience_id) || !empty($_SESSION['sess_audience_id'])  || !empty($sector_id) || !empty($_SESSION['sector_list']) || $fdt!='' || $tdt!='' || $_SESSION['ocr_search_trend']!='') {   ?> <div class="error" style="float:right;"><span id="set_msg"></span></div>
        <span style="text-align:right;"><a id="trend_report_email_alert" class="submitbutton" href="javascript:void(0);">Save Search Criteria</a> </span> 
            <?php } ?>
        <input type="hidden" name="h_ocr_search"  id="h_ocr_search" value="<?php echo htmlspecialchars($_SESSION['ocr_search_trend'], ENT_QUOTES); ?>">
        <input type="hidden" name="h_ocr_search_type"  id="h_search_type" value="<?php if($_SESSION['ocr_search_trend']!=''){ echo htmlspecialchars($_SESSION['ocr_search_radio'], ENT_QUOTES); }?>">
        <input type="hidden" name="h_audience"  id="h_audience" value="<?php if(!empty($audience_id)){echo $audience_id; } ?>">
        <input type="hidden" name="h_sector" id="h_sector" value="<?php if(!empty($sector_id) && $sector_id[0]!=''){echo $sector_id; } ?>">
        <input type="hidden" name="h_fdt" id="h_fdt" value="<?php if(!empty($fdt) && $fdt!=""){ echo $fdt; }?>">
        <input type="hidden" name="h_tdt" id="h_tdt" value="<?php if(!empty($tdt) && $tdt!=""){ echo $tdt; }?>">
         <input type="hidden" name="h_country" id="h_country" value="<?php if(!empty($country_id)){ echo $country_id; }?>">
     </div> 
   <?php  if(!empty($audience_id) || !empty($_SESSION['sess_audience_id']) || !empty($sector_id) || !empty($_SESSION['sector_list']) || $fdt!='' || $tdt='' || $_SESSION['ocr_search_trend']!='') {   ?><div class="bodytext search_cat"><strong>Your Search Criteria:</strong><br/><?php echo $display_search_key; ?></div> <?php } ?>
    <div style="border-top:solid 1px #ededec; padding: 5px; ">
        <table width="100%" cellpadding="4" cellspacing="0" class="sortable">
            <tbody>
                <?php
            if( $resultCount > 0 ) {
                    $className = 'white-bg';
                    $audience='';
                    $ID= '';
                    foreach($ResTrendData['data'] as $resultTrendData){
                        // echo "<pre>";
                        // print_r($resultTrendData);
                        // echo "</pre>";
                        $ID = $resultTrendData['trend_id'];
                        $trend_name = $resultTrendData['trend_name'];
                        //$audience = $resultTrendData['audience_id']; 
                        $country_id = $resultTrendData['country_id']; 
                        $trend_date = $resultTrendData['trend_date']; 
                        $trend_link = $resultTrendData['trend_link'];
                        if(!empty($resultTrendData['sector_names'])){
                            $sectorName="";
                            $categoryName="";
                            $subcategoryName="";
                            foreach($resultTrendData['sector_names'] as $resSectData){
                                //$sectorName=$resSectData['sector_name'];
                                if($resSectData['sector_name']!="" && $resSectData['sector_name']!='0'){
                                    $sectorName.= $resSectData['sector_name'].", ";
                                }
                                if($resSectData['category_name']!="" && $resSectData['category_name']!='0'){
                                    $categoryName.=$resSectData['category_name'].", ";
                                }
                                if($resSectData['subcategory_name']!="" && $resSectData['subcategory_name']!='0'){
                                    $subcategoryName.=$resSectData['subcategory_name'].", ";
                                }
                                //$subtosubcategoryName[]=$resSectData['subtosubcategory_name'];
                            }
                            

                        }

                   
                   ?>
                <tr class="repeat_row">
                <td style="width: 100%;">
                <table style="width: 100%;">
                <tr>
                    <td colspan="2"  width="60%" class="bodytext" valign="top">
                     <!--############### ADD ENCODE TREND ID############-->
                     <?php if(ENV == 'localhost' || ENV == 'demo.competiscan.com'){ ?>
                     <a href="<?php echo $trend_link; ?>" target="_blank" style="display: block; padding: 5px 0; color:#4f7fbd !important; font-size: 17px; font-weight:400; border-bottom: 1px solid #ededec;  "><?php if($trend_name!=""){ echo ucfirst($trend_name); } ?></a>
                     <?php }else{ ?>
                     <a href="<?php echo $trend_link; ?>" target="_blank" style="display: block; padding: 5px 0; color:#4f7fbd !important; font-size: 17px; font-weight:400; border-bottom: 1px solid #ededec;  "><?php if($trend_name!=""){ echo ucfirst($trend_name); } ?></a>
                     <?php } ?>
                       <!--<a href="trendDocument_test.php?id=<?php echo trim($ID); ?>" target="_blank" style="padding:20px 0 0; display: block; color:#00A4E4; font-size: 15px">
                            <img src="images/pdf.jpg" border="0" style="vertical-align:middle; margin-right: 5px"> 
                            View PDF Content
                        </a>-->
                    </td>
                </tr>
                <tr>
                     <td class="bodytext" valign="top" width="50%" style="padding:2px;" ><span style="color: grey;font-size: 11px;">Upload Date: </span><b><?php echo date('Y-m-d',strtotime($trend_date));?></b></td>
                     <td class="bodytext" valign="top" width="50%"  style="padding:2px;" ><span style="color: grey;font-size: 11px;">Sector: </span><b><?php echo htmlspecialchars((($sectorName) ? (rtrim($sectorName,", ")) :'N/A')); ?></b></td>
                </tr>
                <tr>
                    <td class="bodytext" valign="top" width="50%"  style="padding:2px;"><span style="color: grey;font-size: 11px;">Category: </span><b><?php echo htmlspecialchars((($categoryName) ? (rtrim($categoryName,", ")) :'N/A')); ?></b></td>
                    <td class="bodytext" valign="top" width="50%"  style="padding:2px;"  ><span style="color: grey;font-size: 11px;">Sub Category: </span><b><?php echo htmlspecialchars((($subcategoryName) ? (rtrim($subcategoryName,", ")) :'N/A')); ?></b></td>
                </tr>
                </table>
                </td>
                </tr>
                <?php
		}
    }
	
        else { 
            ?>
        <tr>
            <div  colspan="6" class="error" align="center">No Result Found.</div>
   	</tr>
    <?php
	}
        ?>         
          </tbody>
        </table>
    </div>


<div class="clearfix"></div>
<div class="text-center">
    <?php  if($resultCount >0){ ?>
    <ul class="pagination "> 
            <?php /*if($page_no > 1){
            echo "<li><a href='?page_no=1'>First Page</a></li>";
            } */?>
            <li <?php if($page_no <= 1){ echo "class='disabled'"; } ?>>
            <a <?php if($page_no > 1){
            echo "href='?page_no=$previous_page'";
            } ?>>Previous</a>
            </li>
            <?php 
            if ($total_no_of_pages <= 10){  	 
                    for ($counter = 1; $counter <= $total_no_of_pages; $counter++){
                    if ($counter == $page_no) {
                    echo "<li class='active'><a>$counter</a></li>";	
                            }else{
                    echo "<li><a href='?page_no=$counter'>$counter</a></li>";
                            }
                    }
            }elseif ($total_no_of_pages > 10){
            if($page_no <= 4) {			
             for ($counter = 1; $counter < 8; $counter++){		 
                    if ($counter == $page_no) {
                       echo "<li class='active'><a>$counter</a></li>";	
                            }else{
                       echo "<li><a href='?page_no=$counter'>$counter</a></li>";
                            }
            }
            echo "<li><a>...</a></li>";
            echo "<li><a href='?page_no=$second_last'>$second_last</a></li>";
            echo "<li><a href='?page_no=$total_no_of_pages'>$total_no_of_pages</a></li>";
            }elseif($page_no > 4 && $page_no < $total_no_of_pages - 4) { 
            echo "<li><a href='?page_no=1'>1</a></li>";
            echo "<li><a href='?page_no=2'>2</a></li>";
            echo "<li><a>...</a></li>";
            for (
                 $counter = $page_no - $adjacents;
                 $counter <= $page_no + $adjacents;
                 $counter++
                 ) { 
                 if ($counter == $page_no) {
             echo "<li class='active'><a>$counter</a></li>"; 
             }else{
                    echo "<li><a href='?page_no=$counter'>$counter</a></li>";
                      }                  
                   }
            echo "<li><a>...</a></li>";
            echo "<li><a href='?page_no=$second_last'>$second_last</a></li>";
            echo "<li><a href='?page_no=$total_no_of_pages'>$total_no_of_pages</a></li>";
            }else {
            echo "<li><a href='?page_no=1'>1</a></li>";
            echo "<li><a href='?page_no=2'>2</a></li>";
            echo "<li><a>...</a></li>";
            for (
                 $counter = $total_no_of_pages - 6;
                 $counter <= $total_no_of_pages;
                 $counter++
                 ) {
                 if ($counter == $page_no) {
             echo "<li class='active'><a>$counter</a></li>"; 
             }else{
                    echo "<li><a href='?page_no=$counter'>$counter</a></li>";
             }                   
                 }
            }
            }

            ?> 
            <li <?php if($page_no >= $total_no_of_pages){
            echo "class='disabled'";
            } ?>>
            <a <?php if($page_no < $total_no_of_pages) {
            echo "href='?page_no=$next_page'";
            } ?>>Next</a>
            </li>

            <?php if($page_no < $total_no_of_pages){
            echo "<li><a href='?page_no=$total_no_of_pages'>Last &rsaquo;&rsaquo;</a></li>";
            } 

            ?>
        </ul>
    <?php } ?>
</div>
<div class="clearfix"></div>
</div>
<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">How It Works-</h4>
      </div>
      <div class="modal-body">
        <p><ul>
            <li>Apply filters by keywords, sector, from and to date, or country.</li>
            <li>After the results display, a <b>'Save Search Criteria'</b> button will appear in the top right corner.</li>
            <li>Click on the <b>'Save Search Criteria'</b> button to save the trend report email alert into your profile.</li>
            <!--<li>Click on <b>'Manage Trend Report Email Alerts'</b> link placed in right top corner and then you can see your saved search criteria.</li>-->
            <li>If you want to receive a trend report email alert at the Sector/Category/Sub Category level, please select the appropriate checkbox next to the area of interest and click on the bottom left corner <b>'Save Selected Alert'</b> button to set the trend report email alert.</li>
            <li>You will receive an email alert any time a new trend report is uploaded to Competiscan matching your saved criteria.</li>
        </ul>
        </p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>
<!-- myModal1 -->
<div id="myModal1" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button style="margin-top:-10px;padding-right: 18px;" type="button" class="close" data-dismiss="modal">&times;</button>
        <!--<h4 class="modal-title">Message-</h4>-->
      </div>
      <div class="modal-body">
        <p>
            Your selected alert has been saved.
        </p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Ok</button>
      </div>
    </div>

  </div>
</div>
<?php
include 'footer_bottom.php';
?>
<script type="text/JavaScript">
<!--
function showTrend(selectElement) {
    var trend_link = selectElement.value;
    if (trend_link !== '') {
        window.open(trend_link, '_blank'); // ✅ open in new tab
    } else {
        alert('Please select a Trend Report');
    }
}
function showTrend_id(trend_id){
	//document.location.href = 'trendDocument_test.php?id='+trend_id;
         window.open('trendDocument.php?id='+trend_id,"_blank");
}
//-->
</script>
<script type="text/JavaScript">
        $( function() {
            $("#fdt").datepicker({
                autoclose: true,  
                dateFormat: 'yy-mm-dd'
                /*showOn: "button",
                buttonImage: "images/calendar.gif",
                buttonImageOnly: true,
                buttonText: "Select from date",
                maxDate: new Date()*/
            });
            
           $("#tdt").datepicker({
                autoclose: true,  
                dateFormat: 'yy-mm-dd'
               /* showOn: "button",
                buttonImage: "images/calendar.gif",
                buttonImageOnly: true,
                buttonText: "Select from date",
                maxDate: new Date()*/
            });
        });
</script> 
<script>
     $(document).ready(function(){
        $('#trend_report_email_alert').click(function() { 
          h_ocr_search=  $("#h_ocr_search").val();
          h_search_type =  $("#h_search_type").val();
          h_audience=  $("#h_audience").val();
          h_sector=  $("#h_sector").val();
          h_fdt=  $("#h_fdt").val();
          h_tdt=  $("#h_tdt").val();
          h_country=  $("#h_country").val();
          // alert(h_subtosubcategory); 
            $.ajax({          
                    type: "POST",
                    url: "ajax-trend-email_test.php",
                    data: {h_ocr_search:h_ocr_search,h_search_type:h_search_type,h_audience:h_audience,h_sector:h_sector,h_fdt:h_fdt,h_tdt:h_tdt,h_country:h_country,action:'save_search',},
                    success: function(data){
                        //alert(data);
                            if(data!=''){
                            $('#set_msg').html(data);
                            $('#trend_report_email_alert').hide();
                            location.reload();
                            }
                            
                    }
            });
        });
        
    });
</script>
<script>
$(document).ready(function(){
  // Function to get all selected values from sectors, categories, and subcategories
function getSelectedTrends() {
    const form = document.forms['trendForm'];
    const selectedTrends = {};

    // Collect sector, category, and subcategory dropdowns
    const selects = form.querySelectorAll('select');
    selects.forEach(select => {
        const trendName = select.getAttribute('name');
        const trendValue = select.value;

        if (trendValue) {
            selectedTrends[trendName] = trendValue;
        }
    });

    // Collect checked checkboxes for alerts
    const checkboxes = form.querySelectorAll('input[type="checkbox"]:checked');
    checkboxes.forEach(checkbox => {
        const alertName = checkbox.getAttribute('name');
        const alertValue = checkbox.value;

        if (!selectedTrends[alertName]) {
            selectedTrends[alertName] = [];
        }

        selectedTrends[alertName].push(alertValue);
    });

    //console.log('Selected Trends:', selectedTrends);

    return selectedTrends;
}
// Attach event listener to the "Save Selected Alert" button
document.getElementById('saveindustryalrt').addEventListener('click', function() {
    const selectedTrends = getSelectedTrends();
    // Here you can send selectedTrends via AJAX or process them as needed
    //alert('Collected Trends: ' + JSON.stringify(selectedTrends));
        $.ajax({          
                    type: "POST",
                    url: "ajax-trend-email_test.php",
                    data: {check_category:selectedTrends,action:'save_industry_alert'},
                    success: function(data){
                        //alert(data);
                        if(data!=''){
                        $('#myModal1').modal('show'); 
                            location.reload();
                        }
                    }
                });
        });      
    });
</script>