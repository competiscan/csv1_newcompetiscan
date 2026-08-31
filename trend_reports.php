<?php 
$PAGE_HEADING = "Trend Reports";
$TITLE = "Competiscan $PAGE_HEADING";
$start_time = microtime(true);
require_once('includes/globalSession.php');
require_once('includes/checklogin.php');
require_once('sphinxapi2.php');
require_once('includes/functions.php');  //latest function
require_once 'class/Trend.php';
if(!defined('ENV')){
    define('ENV',getenv('SERVER_NAME'));
}
/*######## Start for Page permission ########*/ 
  
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


//############### ADD ENCODE TREND ID############
//if(ENV == 'localhost' || ENV == 'demo.competiscan.com'){
    if(isset($_REQUEST['trend_id']) && $_REQUEST['trend_id']!=''){
        $loc="trendDocument.php?id=".trim($_REQUEST['trend_id']);
        ob_end_clean();
        header("Location: $loc");
        exit;
    }
/*}else{
    if(isset($_REQUEST['trend_id']) && $_REQUEST['trend_id']!=''){
	$BODYTAG = ' onload="showTrend_id('.(int)$_REQUEST['trend_id'].');"';
    }
}*/

$selectSqlcolumn='';
//if(ENV == 'localhost' || ENV == 'demo.competiscan.com'){
   $selectSqlcolumn ='ctr.rndtrend_id,';
//}
$audience_id = array();
$sector_id =array();
$category_id =array(); 
$subcategory_id=array();
$subtosubcategory_id = array();
//$country_id='0';
//$_SESSION['sess_country_id']= '0';
$fdt = '';
$tdt = '';
$wheresearchtrend='';
$trendidsarray=array();
//$_SESSION['ocr_search_trend']='';
//$_SESSION['ocr_search_radio']="trend_ocr";
$offset1='0';
//echo "<pre>";
//print_r($_SESSION); die;
//add country permission
$sql_country = "SELECT DISTINCT country_id FROM cscan_country_users_allow where userID='".$_SESSION['sess_userID']."'";
$rs_country  = $DRW->query( $sql_country,$DRW_read );
$row_country = $DRW->fetch_array($rs_country);
if(isset($_REQUEST['audience_id'])) {
        $audience_id =$_SESSION['sess_audience_id']= $_REQUEST['audience_id'];
        
}
if(isset($_REQUEST['sector'])) {
	$sector_id = $_SESSION['sess_sector_id'] = $_REQUEST['sector'];
}
if(isset($_REQUEST['category'])) {
	$category_id = $_REQUEST['category'];
        $category_id = $_SESSION['sess_cat_id']=array_values(array_filter($category_id));
       
}
if(isset($_REQUEST['subcategory'])) {
	$subcategory_id = $_REQUEST['subcategory'];
        $subcategory_id=$_SESSION['sess_subcat_id'] = array_values(array_filter($subcategory_id));
}
if(isset($_REQUEST['subtosubcategory'])) {
	$subtosubcategory_id = $_REQUEST['subtosubcategory'];
        $subtosubcategory_id=$_SESSION['sess_subsubcat_id']=array_values(array_filter($subtosubcategory_id));
}
if(isset($_REQUEST['country'])) {
	$country_id = $_SESSION['sess_country_id'] = $_REQUEST['country'];
}
if(isset($_REQUEST['fdt'])){
         $fdt = $_SESSION['sess_fdt']=trim($_REQUEST['fdt']);
    }
if(isset($_REQUEST['tdt'])){
        $tdt = $_SESSION['sess_tdt']=trim($_REQUEST['tdt']);
}
if(isset($_REQUEST['clear_search']) and $_REQUEST['clear_search']=='Clear Search') {
	$audience_id = array();
        $sector_id =array();
        $category_id =array();
        $subcategory_id=array();
        $subtosubcategory_id =array();
        $country_id="0";
        $fdt = '';
        $tdt = '';
        $_REQUEST['ocr']='';
        $_REQUEST['search_type_set']='trend_ocr';
        $_SESSION['ocr_search_trend']='';
        $_SESSION['ocr_search_radio']='trend_ocr';
        $_SESSION['sess_audience_id']=array();
        $_SESSION['sess_sector_id']=array();
        $_SESSION['sess_cat_id']=array();
        $_SESSION['sess_subcat_id']=array();
        $_SESSION['sess_subsubcat_id']=array();
        $_SESSION['sess_country_id']= '0';
        $_SESSION['sess_fdt'] ='';
        $_SESSION['sess_tdt'] ='';
        //echo "<pre>";
        //print_r($_SESSION); die;
}
if (!isset($_SESSION['ocr_search_trend']) ||!isset($_SESSION['ocr_search_radio']) || !isset($_SESSION['sess_audience_id']) || !isset($_SESSION['sess_sector_id']) || !isset($_SESSION['sess_cat_id']) || !isset($_SESSION['sess_subcat_id']) || !isset($_SESSION['sess_subsubcat_id'])){
        $_SESSION['sess_audience_id']=array();
        $_SESSION['sess_sector_id']=array();
        $_SESSION['sess_cat_id']=array();
        $_SESSION['sess_subcat_id']=array();
        $_SESSION['sess_subsubcat_id']=array();
        $_SESSION['sess_country_id']= '0';
        $_SESSION['sess_fdt'] ='';
        $_SESSION['sess_tdt'] =''; 
        $_SESSION['ocr_search_trend']='';
        $_SESSION['ocr_search_radio']='trend_ocr';
        
}
/*if (!isset($_SESSION['ocr_search_trend']) || $_SESSION['ocr_search_trend'] != $_REQUEST['ocr']) {
     $_SESSION['ocr_search_trend'] = trim($_REQUEST['ocr']);  
    }*/
    
  
/*if (!isset($_SESSION['ocr_search_radio']) || $_SESSION['ocr_search_radio'] != $_REQUEST['search_type_set']) {
    $_SESSION['ocr_search_radio'] = trim($_REQUEST['search_type_set']); 
}*/
if (isset($_REQUEST['ocr'])) {
   $_SESSION['ocr_search_trend'] = trim($_REQUEST['ocr']);
}
if (isset($_REQUEST['search_type_set'])) {
   $_SESSION['ocr_search_radio'] = trim($_REQUEST['search_type_set']);
}
if(isset($_GET['sort'])) $sort = (int) $_GET['sort'];
else $sort = 0;
if (isset($_GET['page_no']) && $_GET['page_no']!="") {
    $page_no = $_GET['page_no'];
    } else {
    $page_no = 1;
    }
$total_records_per_page = 20;
$offset1 = ($page_no-1) * $total_records_per_page;
$previous_page = $page_no - 1;
$next_page = $page_no + 1;
$adjacents = "2";
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
        <span style="float:right;"> <a style="text-decoration:underline;" class="submitbutton12" href="trend_email_alert.php">Manage Trend Report Email Alerts</a></span>
    </div>   
</div>
<hr style="float:left; width:100%" />
<div class="error" style="float:right;"></div>		
<div  class="left_box" >
<form  name="searchForm" action="trend_reports.php" onsubmit="return check_searchform();" method="post" style="border:1px solid #a4a4a4; padding:10px 10px; margin-bottom:10px;">
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
    <!-- <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                Audience:
            </div>
        </div>
        <div class="col-md-9">
            <div class="form-group">
                <select class="form-control" id ="audience_id" name ="audience_id[]" multiple="multiple" size="3">
                    <?php 
                        $mailing_panel = getMailingPanel();
                       // print_r($audience_id);die;
                        foreach($mailing_panel as $mid=>$name){ 
                            if(!in_array($mid,$_SESSION['sess_mpanel'])){
                                    continue;
                            }
                            ?>
                        <option  <?php if(in_array($mid,$_SESSION['sess_audience_id']) ) { echo "selected"; } ?> value="<?php echo $mid;?>" ><?php echo htmlspecialchars($name, ENT_QUOTES); ?></option>
                        <?php }  ?>
                    </select> 
                
            </div>
        </div>
    </div>-->
    
   <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                Sector:
            </div>
        </div>
        <div class="col-md-9">
            <div class="form-group">
                <select class="form-control" name ="sector[]" id ="sector_list"  size="3" multiple="multiple">
                    <?php 
                        $sector = getSector();
                        foreach($sector as $id=>$name){
                            if(!in_array($id,$_SESSION['sess_sector'])){
                                continue;
                            }
                         if(!empty($id)){ ?>
                        <option  <?php if(in_array($id,$_SESSION['sess_sector_id'])) { echo "selected"; } ?> value="<?php echo $id;?>" >
                            <?php echo htmlspecialchars($name, ENT_QUOTES); ?>
                        </option> 
                       <?php }
                        }
                        ?> 
                </select>
            </div>
        </div>
    </div>
    
   <!--  <div class="row" id="div_hide_category">
        <div class="col-md-3">
            <div class="form-group">
                Category:
            </div>
        </div>
        <div class="col-md-9">
            <div class="form-group">
                 <select id ="category_list" name ="category[]" onChange ="getSubCategory();" multiple="multiple" size="3" class="form-control cat_id"><option <?php $o_count = count($_SESSION['sess_sector_id']); if($o_count ==0 || count($_SESSION['sess_cat_id'])==0) { echo "selected"; } ?> value="">--Any--</option>
                    <?php 
                      if(!empty($_SESSION['sess_sector_id'])){
                      $sector_cat_id = @implode(',',$_SESSION['sess_sector_id']);
                      $category = getCategoryMulti($sector_cat_id);
                       foreach($category as $id=>$name){
                           if(!in_array($id,$_SESSION['sess_category'])){
				continue;
                            }
                          if(!empty($id)){ ?>
                            <option  <?php if(in_array($id,$_SESSION['sess_cat_id'])) { echo "selected"; } ?> value="<?php echo $id;?>" >
                                <?php echo htmlspecialchars($name, ENT_QUOTES); ?>
                            </option> 
                      <?php }
                       }
                      }
                       ?>
                </select>
            </div>
        </div>
    </div>
    
    <div class="row" id="div_hide_subcategory">
        <div class="col-md-3">
            <div class="form-group">
                Sub Category:
            </div>
        </div>
        <div class="col-md-9">
            <div class="form-group">
                <select id ="subcategory_list" name ="subcategory[]" onchange="getSubToSubCategory();" multiple="multiple" size="3" class="form-control"><option <?php $o_count = count($_SESSION['sess_cat_id']); if($o_count==0 ||count($_SESSION['sess_subcat_id'])==0){ echo "selected";} ?> value="" >--Any--</option>
                    <?php                       
                    if(!empty($_SESSION['sess_cat_id']) && count($_SESSION['sess_cat_id'])>0) {
                        
                      $sector_subcat_id = @implode(',',$_SESSION['sess_cat_id']);
                      $sub_category = getSubCategoryMulti($sector_subcat_id,false);
                       foreach($sub_category as $id=>$name){
                        if(!in_array($id,$_SESSION['sess_subcategory'])){
                                             continue;
                                     }
                               if(!empty($id)){ ?>
                                   <option  <?php if(in_array($id,$_SESSION['sess_subcat_id'])) { echo "selected"; } ?> value="<?php echo $id;?>" ><?php echo htmlspecialchars($name, ENT_QUOTES); ?></option> 
                      <?php }
                       }
                    }
                   ?>
                  </select>
            </div>
        </div>
    </div>
    
    <div class="row" id="div_hide_sub_subcategory">
        <div class="col-md-3">
            <div class="form-group">
                Sub Sub Category:
            </div>
        </div>
        <div class="col-md-9">
            <div class="form-group">
                <select id ="subtosubcategory_list" name ="subtosubcategory[]"  multiple="multiple" size="3" class="form-control"><option <?php $o_count = count($_SESSION['sess_subcat_id']); if($o_count==0 || count($_SESSION['sess_subsubcat_id'])==0){ echo "selected";} ?> value="">--Any--</option>
                    <?php 
                       
                       if(!empty($_SESSION['sess_subcat_id']) && !in_array(0,$_SESSION['sess_subcat_id'])){
                          
                       $sector_subtosubcat_id = @implode(',',$_SESSION['sess_subcat_id']);
                       $sub_tosubcategory = getSubCategoryMulti($sector_subtosubcat_id,false);
                       foreach($sub_tosubcategory as $id=>$name){
                                if(!empty($id)){ ?>
                                    <option  <?php if(in_array($id,$_SESSION['sess_subsubcat_id'])) { echo "selected"; } ?> value="<?php echo $id;?>" ><?php echo htmlspecialchars($name, ENT_QUOTES); ?></option> 
                       <?php }
                       }}
                        ?>
                   </select>
               
            </div>
        </div>
    </div>-->
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
                <?php// }else{ ?>
                 <!--<input type="radio" <?php if(!empty($_SESSION['sess_country_id']) && $_SESSION['sess_country_id']=='1'){ echo "checked";} ?> name="country" value="1">US
                <input type="radio" <?php if(!empty($_SESSION['sess_country_id']) && $_SESSION['sess_country_id']=='3'){ echo "checked";} ?> name="country" value="3">CANADA
                <input type="radio" <?php if($_SESSION['sess_country_id']=='0'){ echo "checked";} ?> name="country" value="0">All-->
                <?php //} ?>
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
$savedArray = array();
$perm_country='';
if($row_country[0]=='US'){
    $perm_country=1;
}elseif($row_country[0]=='CA'){
  $perm_country=3;  
}
$sql = "SELECT sectorID,categoryID,subCategoryID,subSubCategoryID FROM cscan_trend_report_search WHERE userID='{$_SESSION['sess_userID']}' AND emailAlert=1 AND country='".$perm_country."' AND alert_type=1";
$savedQuery = $DRW->query($sql,$DRW_read);
while($row = $DRW->fetch_row($savedQuery)) {
	$savedArray[$row[0]] = $row[0];
	$savedArray[$row[1]] = $row[1];
        $savedArray[$row[2]] = $row[2];
        $savedArray[$row[3]] = $row[3];
}

$sql_filter = "SELECT sectorID FROM cscan_trend_report_search WHERE userID='{$_SESSION['sess_userID']}' AND emailAlert=1 AND country='".$perm_country."' AND alert_type=0 AND (searchKey='' OR searchKey IS NULL) AND categoryID='' AND subCategoryID='' AND subSubCategoryID=''";
$savedQuery1 = $DRW->query($sql_filter,$DRW_read);
$filterSavedArray=array();
$expl_sector=array();
//$merge_saved_array=array();
while($row1 = $DRW->fetch_row($savedQuery1)) {
        if(strstr($row1['0'],',')){
          $expl_sector=explode(',',$row1['0']); 
          
        }else{
            $expl_sector[]=$row1[0];
            //$filterSavedArray[]=$row1['0'];
        }
	 
	$filterSavedArray=array_merge($expl_sector,$filterSavedArray);
        $expl_sector=array();
}
//echo "<pre>";
//print_r(array_unique($filterSavedArray));
echo displayCategory(0);
function displayCategory($ID,$level=0) {
	global $DRW,$DRW_read,$DRW_main,$DRW_crm;
	$savedArray = $GLOBALS['savedArray'];
        $filterSavedArray = $GLOBALS['filterSavedArray'];
	$print = '';
        $andCountryCond='';
	$sqla = "SELECT sectorID,sectorName FROM cscan_sector WHERE parentID='$ID' ORDER BY sectorName ASC";
	$rsa = $DRW->query($sqla,$DRW_read);
        $andcond='';
	while($rowa = $DRW->fetch_array($rsa)) {
		$name = $rowa['sectorName'];
		$catid = $rowa['sectorID'];
		$innertext = '';
		if(!in_array($catid,$_SESSION['sess_sector']) && !in_array($catid,$_SESSION['sess_category']) && !in_array($catid,$_SESSION['sess_subcategory'])){
			continue;
		}
                //echo $level."<br/>";
                /*if($level==0) {
                $andcond .= " and ctc.sector_id=$catid";
                }else{
                        if($level<2){
                        $andcond .= " and ctc.category_id=$catid";
                    }else{
                        $andcond .= " and ctc.subcategory_id = $catid";
                        //$andcond .= " and ctc.subtosubcategory_id = $catid"; 
                    }
                
                } */
                //add country permission
               // if(ENV == 'localhost' || ENV == 'demo.competiscan.com'){
                    $sql_country = "SELECT DISTINCT country_id FROM cscan_country_users_allow where userID='".$_SESSION['sess_userID']."'";
                    $rs_country  = $DRW->query( $sql_country,$DRW_read );
                    $row_country = $DRW->fetch_array($rs_country);
                    if($row_country[0]=='US'){
                       $country_id='1';
                       $andCountryCond = " and (ctr.country_id = $country_id OR ctr.country_id =0)";
                       
                    }elseif($row_country[0]=='CA'){
                        $country_id='3';
                        $andCountryCond = " and  (ctr.country_id = $country_id OR ctr.country_id =0)";
                        
                    }
                   
                // }
                $selectSqlcolumn='';
                //if(ENV == 'localhost' || ENV == 'demo.competiscan.com'){
                   $selectSqlcolumn ='ctr.rndtrend_id,';
                //}
                if($level==0) {
                 $sqltwo = "SELECT ctr.trend_id,$selectSqlcolumn trend_name,trend_link,file_path,file_name,ctc.sector_id,ctc.category_id,ctc.subcategory_id,ctc.subtosubcategory_id FROM cscan_trend_report ctr JOIN cscan_trends_category ctc ON 
                               ctc.trend_id = ctr.trend_id WHERE 1=1 and ctc.sector_id=$catid and ctc.category_id=0 $andCountryCond GROUP BY ctr.trend_id ORDER BY trend_date DESC";
                }else{
                    if($level<2){
                        $sqltwo = "SELECT ctr.trend_id,$selectSqlcolumn trend_name,trend_link,file_path,file_name,ctc.sector_id,ctc.category_id,ctc.subcategory_id,ctc.subtosubcategory_id FROM cscan_trend_report ctr JOIN cscan_trends_category ctc ON 
                               ctc.trend_id = ctr.trend_id WHERE 1=1 and ctc.category_id=$catid  and ctc.subcategory_id=0 $andCountryCond GROUP BY ctr.trend_id ORDER BY trend_date DESC";
                        
                        //$andcond .= " and ctc.category_id=$catid";
                    }else{
                        $sqltwo = "SELECT ctr.trend_id,$selectSqlcolumn trend_name,trend_link,file_path,file_name,ctc.sector_id,ctc.category_id,ctc.subcategory_id,ctc.subtosubcategory_id FROM cscan_trend_report ctr JOIN cscan_trends_category ctc ON 
                               ctc.trend_id = ctr.trend_id WHERE 1=1 and ctc.subcategory_id=$catid and ctc.subtosubcategory_id=0 $andCountryCond GROUP BY ctr.trend_id ORDER BY trend_date DESC";
                        
                        //$andcond .= " and ctc.subcategory_id = $catid";
                        //$andcond .= " and ctc.subtosubcategory_id = $catid"; 
                    }
                }
                //echo "<br/>";
                //$sqltwo = "SELECT trend_name,trend_link,trend_id FROM cscan_trend_report WHERE category_id=$catid ORDER BY trend_date DESC"; 
		$query = $DRW->query($sqltwo,$DRW_read);
		if($DRW->num_rows($query)>0){
			$innertext .= "<br /><div style='display: flex; justify-content: space-between; width: 100%;'><select class=\"form-control\" style=\"font-weight: normal;color:#000000;margin-bottom:-16px;\" name=\"trend{$catid}\" size=\"1\" onchange=\"showTrend('trend{$catid}',false);\"><option value=\"\">&nbsp;</option>";
			while($row2 = $DRW->fetch_assoc($query)) {
				$trendname = $row2['trend_name'];
				$link = $row2['trend_link'];
                                $trend_id = $row2['trend_id'];
                                //############### ADD ENCODE TREND ID############
                                //if(ENV == 'localhost' || ENV == 'demo.competiscan.com'){
                                    //$trend_id = convert_string('encrypt',$row2['trend_id']);
                                    $trend_id = $row2['rndtrend_id'];
                                //}
				$innertext .= "<option value=\"$trend_id\">".htmlspecialchars($trendname, ENT_QUOTES)."</option>";
			}
			$innertext .= "</select><input type=\"checkbox\" style=\"margin-left:12px;\" class=\"form-check-input\"  name=\"alert[]\" value=\"$catid\"";
			if(key_exists($catid,$savedArray) || in_array($catid,array_unique($filterSavedArray))) {
				$innertext .= ' checked="checked"';
			}
			$innertext .= " /></div>";// <a href=\"#\" onclick=\"showTrend('trend{$catid}',true); return false;\" class=\"bluelink\">Download</a>
                         
		}
			
		$innertext .= displayCategory($catid,($level+1));
		
		if($innertext!='') {
			$print .= "<div";
			if($level==0) {
				$print .= " style=\"text-align:left; font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 13px; font-weight: bold;text-decoration: none;margin:0px 0px 10px 0px;padding:10px 55px;border:solid 1px #a4a4a4;\"";
			}
			else {
				$print .= " style=\"";
				if($level<2) {
					$print .= "margin:8px 0px 0px 0px;";
				}
				else {
					$print .= "margin:8px 0px 0px 0px;font-weight: normal;color: #0055E3;";
				}
				$print .= "text-align:left;font-family: arial;font-size: 12px; text-decoration: none;line-height: 18px;\"";
			}
			$print .= ">
			".htmlspecialchars($name,ENT_QUOTES)."$innertext
			</div>";
		}
	}
	return $print;
}

?>
<div align="left"><input type="button" name="submit" id ="saveindustryalrt" value="Save Selected Alert" class="submitbutton" /><input type="hidden" name="userID" value="<?php echo $_SESSION['sess_userID']; ?>" /></div>
</form>
</div>

<div class="right_box"> 
    <?php 
    $whereIn='';
    $AndQuery='';
    $and='';
    $display_search_key=""; 
    $exclude_category=array();
    $exclude_sector=array();
    $exclude_subcategory=array();
    $sqlSect = "SELECT sectorID FROM cscan_sector WHERE parentID='0'";
    $result_sect = $DRW->query($sqlSect,$DRW_read);
    $sectorIDArray=array();
    $catIDArray=array();
    $subcatIDArray=array();
        while($rowSect = $DRW->fetch_array($result_sect)) {
            $sectorIDArray[] =$rowSect['sectorID'];
            $sqlCat = "SELECT sectorID FROM cscan_sector WHERE parentID='".$rowSect['sectorID']."'";
            $result_Cat = $DRW->query($sqlCat,$DRW_read);
            while($rowCat=$DRW->fetch_array($result_Cat)) {
                 $catIDArray[]=$rowCat['sectorID'];
                 $sqlSubCat = "SELECT sectorID FROM cscan_sector WHERE parentID='".$rowCat['sectorID']."'";
                $result_subCat = $DRW->query($sqlSubCat,$DRW_read);
                while($rowSubCat=$DRW->fetch_array($result_subCat)) {
                     $subcatIDArray[]=$rowSubCat['sectorID'];

                }
            }
        
        } 
        
     $exclude_sector=array_diff($sectorIDArray,$_SESSION['sess_sector']);
     $exclude_category=array_diff($catIDArray,$_SESSION['sess_category']);
     $exclude_subcategory=array_diff($subcatIDArray,$_SESSION['sess_subcategory']);
     //print_r($exclude_category);
     if(!empty($exclude_sector)){
       $exclude_sector = @implode(",",$exclude_sector); 
       $whereIn .=" WHERE ctc.sector_id Not In ($exclude_sector)";
     }
     /*else{
       $sectorIDArray = @implode(",",$sectorIDArray);  
       $whereIn .=" WHERE ctc.sector_id In ($sectorIDArray)";
     } */
     /*if(!empty($exclude_category)){
       $exclude_category = @implode(",",$exclude_category); 
       $whereIn .=" And ctc.category_id Not In ($exclude_category)";
     }*/
     /*
     else{
       $catIDArray = @implode(" ,",$catIDArray);
       $whereIn .=" And ctc.category_id In ($catIDArray)";
     }*/
     /*if(!empty($exclude_subcategory)){
       $exclude_subcategory = @implode(",",$exclude_subcategory); 
       $whereIn .=" And ctc.subcategory_id Not In ($exclude_subcategory)";
     }*/
     /*else{
       $subcatIDArray = @implode(" ,",$subcatIDArray);  
       $whereIn .=" And ctc.subcategory_id In ($subcatIDArray)";
     }*/
    /* $sess_mpanelid=$_SESSION['sess_mpanel'];
     if(!empty($sess_mpanelid)){
         $AndQuery =" And (";
         for($i=0; $i<count($sess_mpanelid); $i++){
             if($i<1){
                 $AndQuery .=" FIND_IN_SET($sess_mpanelid[$i],audience_id)";
             }else{
                 $AndQuery .=" OR FIND_IN_SET($sess_mpanelid[$i],audience_id)";
             }           
             
         }
         $AndQuery .=")";
     }*/
    
     //$mediaPanelID = @implode(" ,",$mediaPanelID);  
     $whereIn .= $AndQuery;
    $sql = "SELECT DISTINCT(ctr.trend_id),$selectSqlcolumn trend_name,trend_link,ctr.category_id,file_path,file_name,audience_id,country_id,trend_date,ctc.sector_id,ctc.category_id,ctc.subcategory_id,ctc.subtosubcategory_id FROM cscan_trend_report ctr  JOIN cscan_trends_category ctc ON 
                       ctc.trend_id = ctr.trend_id"; 
        $rs = $DRW->query($sql,$DRW_read);
        $numquery = "SELECT COUNT(ctr.trend_id) as numrows  FROM cscan_trend_report ctr  JOIN cscan_trends_category ctc ON 
               ctc.trend_id = ctr.trend_id";
        $where ='';
        if(!empty($_SESSION['sess_audience_id']) || !empty($audience_id) || !empty($sector_id) ||!empty($category_id) || !empty($subcategory_id) || !empty($subtosubcategory_id) || $_SESSION['ocr_search_trend'] != ''){
            $where =$whereIn;
            $sql .= $where;
            $numquery .=$where;
        }else{
            $where =$whereIn;
            $sql .= $where;
            $numquery .=$where;
        }
         if ($_SESSION['ocr_search_trend'] != '') {
            $searchKey = $_SESSION['ocr_search_trend'];
            $display_search_key.= " ".$searchKey;
	    $search_id = session_id();
	    // echo "ok.ok".$SPHINX_name;
	     #$s = startSphinx();

            #$result = $s->Query("Commentary: Nov 2021 Holiday Shopping Insights", "base_index_prod_trendreport_fulltext");

            #echo "<pre>";
            #print_r($result);
            #echo "</pre>";

            #echo "Error=".$s->GetLastError()."<br>";
            #echo "Warning=".$s->GetLastWarning()."<br>";
            #die;
                if (!empty($SPHINX_name)) {
			$s = startSphinx();
			//print_r ($s);die;
                    //echo $_SESSION['ocr_search_radio']."<br/>";

                    if($_SESSION['ocr_search_radio']=='trend_fulltext' ){
                         $inds = 'base_index_prod_trendreport_fulltext';

                    }else{
                        $inds = 'base_index_prod_trendreport';
                    }


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
                            $count_save_sql = "SELECT MAX(trend_id) FROM cscan_trend_document_text";
                            $rs = $DRW->query($count_save_sql, $DRW_read);
                            $data = $DRW->fetch_row($rs);
                            $maxID = $data[0];
                              $DRW->query('START TRANSACTION', $DRW_main); 
                            for ($offset = 0; $offset <= $maxID; $offset += $step) {
                                $s = startSphinx();
                                $s->setLimits(0, $step, $step);
                                $s->setIDRange($minID + 1, $maxID);
                                $result = $s->query($ps, $inds);
                               if (isset($result['matches'])) {
                                    foreach ($result['matches'] as $dts_id => $match) {
                                        $minID = $dts_id;
                                        $currcount++;

                                         $trendidsarray[] =   $match['attrs']['trend_id'];


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

                             $DRW->query('COMMIT', $DRW_main); 
                        }

                           if ($search_id != '') {
                                 $trendidsarray   =  array_unique($trendidsarray);
                                if (!empty($trendidsarray)) {
                                    $andUnion = '';
                                    $chunkdata=10000;
                                    if($total>600000){
                                            $chunkdata=50000;
                                    }
                                    $newarray = array_chunk($trendidsarray, $chunkdata);
                                    for ($u = 2; $u < 100; $u++) {
                                        if (count($newarray) >= $u) {

                                            $andUnion.="union ( SELECT tr.trend_id   FROM  cscan_trend_report tr  WHERE tr.trend_id IN(" . implode(',', ($newarray[$u - 1])) . "))";
                                        }else{
                                            continue;
                                        }
                                    }

                                    //$andcond = " select B.trend_id FROM  (SELECT tr.trend_id FROM  cscan_trend_report tr  WHERE tr.trend_id IN(" . implode(',', $newarray[0]) . ") " . $andUnion . ")B";
                                    $wheresearchtrend = " AND ctr.trend_id in (" .implode(',',$trendidsarray) . ") ";
                                }
                                if (empty($trendidsarray)) {
                                    $andcond = '-1';
                                    $wheresearchtrend = " AND ctr.trend_id in (" . $andcond . ") ";
                                }
                            }

                    }
                }
            }
        $sql2 = $wheresearchtrend;
        
      /* if(!empty($_SESSION['sess_audience_id'])) { 
                //echo "sdsgdsgd"; die;
                $audience_id = @implode(",",$_SESSION['sess_audience_id']);
                //print_r($audience_id); die; 
                $and = " and audience_id In ($audience_id)";
                $sql .= $and;
                $numquery .= $and;
                $audiencename = mediaPanelName($audience_id);
                $display_search_key.= " <b>Audience:</b> ".$audiencename;
        }*/

     if(!empty($_SESSION['sess_sector_id'])) { 
                $sector_id = @implode(",",$_SESSION['sess_sector_id']);
                $and = " and ctc.sector_id In ($sector_id)";
                $sql .= $and;
                $numquery .= $and;
                $sectorName= sectorName($sector_id);
                $display_search_key.= " <b>Sector:</b> ".$sectorName;
        }
        if(!empty($_SESSION['sess_cat_id'])) { 
                $category_id = @implode(",",$_SESSION['sess_cat_id']);
               // print_r($category_id); die;
                $and = " and ctc.category_id In ($category_id)";
                $sql .= $and;
                $numquery .= $and;
                $categoryName= categoryName($category_id);
                $display_search_key.= " <b>Category:</b> ".$categoryName;
        }
        if(!empty($_SESSION['sess_subcat_id'])) { 
                $subcategory_id = @implode(",",$_SESSION['sess_subcat_id']);
                $and = " and ctc.subcategory_id In ($subcategory_id)";
                $sql .= $and;
                $numquery .= $and;
                 $subCategoryName= subCategoryName($subcategory_id);
                $display_search_key.= " <b> Sub Category:</b> ".$subCategoryName;
        }
        if(!empty($_SESSION['sess_subsubcat_id'])) { 
                $subtosubcategory = @implode(",",$subtosubcategory_id);
                $and = " and ctc.subtosubcategory_id In ($subtosubcategory)";
                $sql .= $and;
                $numquery .= $and;
                $subtosubCategoryName= subCategoryName($subtosubcategory);
                $display_search_key.= " <b> Sub sub Category:</b> ".$subtosubCategoryName;
        }
         if($_SESSION['sess_fdt']!="") { 

                $and = " and DATE_FORMAT(trend_date, '%Y-%m-%d') >= '".$_SESSION['sess_fdt']."'";
                $sql .= $and;
                $numquery .= $and;
                $display_search_key.= " <b>From Date:</b> ".$_SESSION['sess_fdt'];
        }

        if($_SESSION['sess_tdt']!="") { 
                $and = " And DATE_FORMAT(trend_date, '%Y-%m-%d') <= '".$_SESSION['sess_tdt']."'";
                $sql .= $and;
                $numquery .= $and;
                $display_search_key.= " <b>To Date:</b> ".$_SESSION['sess_fdt'];
        }
        if($_SESSION['sess_country_id']) { 
                $country_id = $_SESSION['sess_country_id'];
                $and='';
                if($country_id!=0){
                    $and = " and (ctr.country_id = $country_id OR ctr.country_id =0)";
                }else{
                    //$and = " and ctr.country_id = $country_id";
                }
                
                $sql .= $and;
                $numquery .= $and;
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
            //add country permission
            //if(ENV == 'localhost' || ENV == 'demo.competiscan.com'){
                if($row_country[0]=='US'){
                   $country_id='1';
                   $and = " and (ctr.country_id = $country_id OR ctr.country_id =0)";
                   $countryName ="UNITED STATES";
                }elseif($row_country[0]=='CA'){
                    $country_id='3';
                    $and = " and (ctr.country_id = $country_id OR ctr.country_id =0)";
                    $countryName ="CANADA";
                }else{
                    //$country_id='0';
                    $countryName ="All";
                }
                $display_search_key.= " <b>Country:</b> ".$countryName;
                $sql .= $and;
                $numquery .= $and;
                
            /*}else{
                $countryName ="All";
                $display_search_key.= " <b>Country:</b> ".$countryName;
            }*/
        }
        $sql .= $sql2;
        $numquery .= $sql2;
        $sql .= " GROUP BY ctr.trend_id"; 
        $numquery .= " GROUP BY ctr.trend_id";
        $numquery = $DRW->query($numquery,$DRW_read);
        //$nrow = $DRW->fetch_array($numquery);
        //$DRW->num_rows($rs);
        $numrows = $DRW->num_rows($numquery);
        $total_records = $numrows;
        $total_no_of_pages = ceil($total_records / $total_records_per_page);

        $second_last = $total_no_of_pages - 1; // total pages minus 1

        switch($sort){
                case 1:
                        $sql .= " ORDER BY ctr.trend_date ASC ";
                        break;
                default:
                        $sql .= " ORDER BY  ctr.trend_date DESC ";
        }
        $sql .= "LIMIT $offset1, $total_records_per_page";
        $rs = $DRW->query($sql,$DRW_read);
        $resultCount = $DRW->num_rows($rs);
         ?>
     <div class=" pull-right">
        <?php  if(!empty($audience_id) || !empty($_SESSION['sess_audience_id'])  || !empty($sector_id) || !empty($_SESSION['sess_sector_id']) || $fdt!='' || $tdt='' || $_SESSION['ocr_search_trend']!='') {   ?> <div class="error" style="float:right;"><span id="set_msg"></span></div>
        <span style="text-align:right;"><a id="trend_report_email_alert" class="submitbutton" href="javascript:void(0);">Save Search Criteria</a> </span> 
            <?php } ?>
        <input type="hidden" name="h_ocr_search"  id="h_ocr_search" value="<?php echo htmlspecialchars($_SESSION['ocr_search_trend'], ENT_QUOTES); ?>">
        <input type="hidden" name="h_ocr_search_type"  id="h_search_type" value="<?php if($_SESSION['ocr_search_trend']!=''){ echo htmlspecialchars($_SESSION['ocr_search_radio'], ENT_QUOTES); }?>">
        <input type="hidden" name="h_audience"  id="h_audience" value="<?php if(!empty($audience_id)){echo $audience_id; } ?>">
        <input type="hidden" name="h_sector" id="h_sector" value="<?php if(!empty($sector_id) && $sector_id[0]!=''){echo $sector_id; } ?>">
        <input type="hidden" name="h_category" id="h_category" value="<?php if(!empty($category_id && $category_id[0]!='')){echo $category_id;} ?>">
        <input type="hidden" name="h_subcategory"  id="h_subcategory" value="<?php if(!empty($subcategory_id) && $subcategory_id[0]!=''){echo $subcategory_id; } ?>">
        <input type="hidden" name="h_subtosubcategory" id="h_subtosubcategory" value="<?php if(!empty($subtosubcategory_id) && $subtosubcategory_id[0]!=''){ echo $subtosubcategory; }?>">
        <input type="hidden" name="h_fdt" id="h_fdt" value="<?php if(!empty($fdt) && $fdt!=""){ echo $fdt; }?>">
        <input type="hidden" name="h_tdt" id="h_tdt" value="<?php if(!empty($tdt) && $tdt!=""){ echo $tdt; }?>">
         <input type="hidden" name="h_country" id="h_country" value="<?php if(!empty($country_id)){ echo $country_id; }?>">
     </div> 
   <?php  if(!empty($audience_id) || !empty($_SESSION['sess_audience_id']) || !empty($sector_id) || !empty($_SESSION['sess_sector_id']) || $fdt!='' || $tdt='' || $_SESSION['ocr_search_trend']!='') {   ?><div class="bodytext search_cat"><strong>Your Search Criteria:</strong><br/><?php echo $display_search_key; ?></div> <?php } ?>
    <div style="border-top:solid 1px #ededec; padding: 5px; ">
        <table width="100%" cellpadding="4" cellspacing="0" class="sortable">
            <tbody>
                <?php
            if( $resultCount > 0 ) {
                    $className = 'white-bg';
                    $audience='';
                     $ID= '';
                    while($row = $DRW->fetch_assoc($rs)) {
                            $ID = $row['trend_id'];
                            $trend_name = $row['trend_name'];
                            $audience = $row['audience_id']; 
                            $country_id = $row['country_id']; 
                            $trend_date = $row['trend_date']; 
                             $trend_file_path = $row['file_path'];
                             $trend_file_name = $row['file_name'];
                             //$audience_name = mediaPanelName($audience);
                             $comboIDs = getAllCategoryByTrendId($ID);
                            if ($className=='selected-bg') $className='white-bg';
                                            else $className='selected-bg';
                            if(!empty($comboIDs)) {
                            $comboIDs_split = @explode('|',$comboIDs);
                            $sectorName='';
                            $categoryName='';
                            $subcategoryName ='';
                            $subtosubcategoryName='';
                            foreach($comboIDs_split as $scsc_combo){
                                           //print_r($scsc_combo);
                                            if(!empty($scsc_combo)){
                                            list($s,$c,$sc,$ssc) = explode('_',$scsc_combo);
                                            if(!empty($s) || !empty($c) || !empty($sc) || !empty($ssc)){
                                               
                                                if(!in_array($s,$_SESSION['sess_sector'])){
                                                  continue;  
                                                }
                                                $sectorName .= sectorName($s).", ";
                                               if($c!='' && $c!='0'){
                                                    if(!in_array($c,$_SESSION['sess_category'])){
                                                      continue;  
                                                    }
                                                    $categoryName .= sectorName($c).", ";
                                                }
                                                if($sc!='' && $sc!='0'){
                                                    if(!in_array($sc,$_SESSION['sess_subcategory'])){
                                                     continue;  
                                                       }
                                                    $subcategoryName .= sectorName($sc).", ";
                                                 }
                                                 if($ssc!='' && $ssc!='0'){
                                                  $subtosubcategoryName .= sectorName($ssc).",";
                                                   // $scsc_combo_text = sectorName($s).' / '.sectorName($c).' / '.sectorName($sc).' / '.sectorName($ssc);
                                                    //$scsc_combo_text;
                                                 }

                                           }
                                        }   
                                }       
                            }
                            //echo $str = implode(", ",array_unique(explode(", ", $sectorName)))."<br/>";
                         ?>
                <tr class="repeat_row">
                <td style="width: 100%;">
                <table style="width: 100%;">
                <tr>
                    <td colspan="2"  width="60%" class="bodytext" valign="top">
                     <!--############### ADD ENCODE TREND ID############-->
                     <?php if(ENV == 'localhost' || ENV == 'demo.competiscan.com'){ ?>
                     <a href="trendDocument.php?id=<?php echo $row['rndtrend_id']; ?>" target="_blank" style="display: block; padding: 5px 0; color:#4f7fbd !important; font-size: 17px; font-weight:400; border-bottom: 1px solid #ededec;  "><?php if($trend_name!=""){ echo ucfirst($trend_name); } ?></a>
                     <?php }else{ ?>
                     <a href="trendDocument.php?id=<?php echo $row['rndtrend_id']; ?>" target="_blank" style="display: block; padding: 5px 0; color:#4f7fbd !important; font-size: 17px; font-weight:400; border-bottom: 1px solid #ededec;  "><?php if($trend_name!=""){ echo ucfirst($trend_name); } ?></a>
                     <?php } ?>
                       <!--<a href="trendDocument_test.php?id=<?php echo trim($ID); ?>" target="_blank" style="padding:20px 0 0; display: block; color:#00A4E4; font-size: 15px">
                            <img src="images/pdf.jpg" border="0" style="vertical-align:middle; margin-right: 5px"> 
                            View PDF Content
                        </a>-->
                    </td>
                </tr>
                <tr>
                     <td class="bodytext" valign="top" width="50%" style="padding:2px;" ><span style="color: grey;font-size: 11px;">Upload Date: </span><b><?php echo date('Y-m-d',strtotime($trend_date));?></b></td>
                     <td class="bodytext" valign="top" width="50%"  style="padding:2px;" ><span style="color: grey;font-size: 11px;">Sector: </span><b><?php $sectorName = implode(", ",array_unique(explode(", ",$sectorName))); echo !empty($sectorName) ? substr($sectorName, 0, -2) : 'N/A'; ?></b></td>
                </tr>
                <tr>
                    <td class="bodytext" valign="top" width="50%"  style="padding:2px;"><span style="color: grey;font-size: 11px;">Category: </span><b><?php $categoryName = implode(", ",array_unique(explode(", ",$categoryName))); echo !empty($categoryName) ? substr($categoryName, 0, -2) : 'N/A'; ?></b></td>
                    <td class="bodytext" valign="top" width="50%"  style="padding:2px;"  ><span style="color: grey;font-size: 11px;">Sub Category: </span><b><?php $subcategoryName = implode(", ",array_unique(explode(", ",$subcategoryName))); echo !empty($subcategoryName) ? substr($subcategoryName, 0, -2) : 'N/A'; ?></b></td>
                </tr>
                <!--<tr>
                     <td class="bodytext" valign="top" ><strong>Sub Sub Category:</strong><br><?php echo !empty($subtosubcategoryName) ? substr($subtosubcategoryName, 0, -2) : 'N/A'; ?></td>
                     <td  class="bodytext" valign="top" ><strong>Audience:</strong><br><?php //echo !empty($audience_name) ? $audience_name : 'N/A';?></td>
                     <td class="bodytext" valign="top" ><strong>Country:</strong><br><?php if(!empty($country_id) and $country_id =='1') { echo "UNITED STATES";}elseif(!empty($country_id) and $country_id =='3') { echo "CANADA";}elseif(!empty($country_id) and $country_id =='') { echo "ALL";} ?></td>
                </tr>-->
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
function showTrend(selname,fromlink){
	var trend_id = document.trendForm[selname].options[document.trendForm[selname].selectedIndex].value;
	if(trend_id!=''){
		//document.location.href = 'trendDocument_test.php?id='+trend_id;
                 window.open('trendDocument.php?id='+trend_id,"_blank");
	}
	else if(fromlink){
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
          h_category=  $("#h_category").val();
          h_subcategory=  $("#h_subcategory").val();
          h_subtosubcategory=  $("#h_subtosubcategory").val();
          h_fdt=  $("#h_fdt").val();
          h_tdt=  $("#h_tdt").val();
          h_country=  $("#h_country").val();
          // alert(h_subtosubcategory); 
            $.ajax({          
                    type: "POST",
                    url: "ajax-trend-email.php",
                    data: {h_ocr_search:h_ocr_search,h_search_type:h_search_type,h_audience:h_audience,h_sector:h_sector,h_category:h_category,h_subcategory:h_subcategory,h_subtosubcategory:h_subtosubcategory,h_fdt:h_fdt,h_tdt:h_tdt,h_country:h_country,action:'save_search',},
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
<script type="text/javascript">
     $('#div_hide_category').hide();
     $('#div_hide_subcategory').hide();
     $('#div_hide_sub_subcategory').hide();
    <?php
    if(!empty($_SESSION['sess_sector_id'])){ ?>
    //$('#div_hide_category').show();
    <?php }
     if(!empty($_SESSION['sess_cat_id']) || !empty($_SESSION['sess_subcat_id'])) { ?>
     //$('#div_hide_subcategory').show();
    <?php  } 
    if(!empty($_SESSION['sess_subcat_id'])) {  ?>
        // $('#div_hide_sub_subcategory').show();
    <?php } ?>
    function getCategory() {
            var str='';
            var str2='';
            var val=document.getElementById('sector_list');
            for (i=0;i< val.length;i++) { 
                if(val[i].selected){
                    str += val[i].value + ','; 
                }
            }         
            var sect_id=str.slice(0,str.length -1);
            var cat_val=document.getElementById('category_list');
            if(cat_val.length >0){
                for (i=0;i< cat_val.length;i++) { 
                        if(cat_val[i].selected){
                            str2 += cat_val[i].value + ','; 
                        }
                    }
             }
            var cate_id=str2.slice(0,str.length -1);
            //alert(cate_id);
            //console.log('kkkkkk'+cate_id);
                if(sect_id!=''){
            $.ajax({          
                    type: "POST",
                    url: "ajax-get-scsc.php",
                    data: {sid:sect_id,cate_id:cate_id,action:'sector',},
                    success: function(data){
                            //alert(data);
                            $('.cat_id').html(data);
                            $('#div_hide_category').show();
                            getSubCategory();
                            getSubToSubCategory();
                            
                            
                    }
            });
           
    } else{
            $('#div_hide_category').hide();
            clearSelected('category_list');
            $('#div_hide_subcategory').hide(); 
            clearSelected('subcategory_list');
            $('#div_hide_sub_subcategory').hide();
            clearSelected('subtosubcategory_list');
        }
    }
    
     function getSubCategory() {
        
            var str='';
            var strsubcat='';
            var val=document.getElementById('category_list');
            //alert(val);
            for (i=0;i< val.length;i++) { 
                if(val[i].value!=0){
                    if(val[i].selected){
                        str += val[i].value + ','; 
                    }
                }
            }         
            var cat_id=str.slice(0,str.length -1);
            var val_sub_cat=document.getElementById('subcategory_list');
            for (i=0;i< val_sub_cat.length;i++) { 
                 if(val_sub_cat[i].value!=0){
                    if(val_sub_cat[i].selected){
                        strsubcat += val_sub_cat[i].value + ','; 
                    }
                } 
            } 
            var subcat_id=strsubcat.slice(0,strsubcat.length -1);
           //alert(subcat_id);
           if(cat_id!=""){
            $.ajax({          
                    type: "POST",
                    url: "ajax-get-scsc.php",
                    data: {cid:cat_id,subcat_id:subcat_id,action:'cat',},
                    success: function(data){
                             $('#subcategory_list').html(data);
                                 getSubToSubCategory();
                             $('#div_hide_subcategory').show();
                           
                            
                    }
            });
        }else{
            $('#div_hide_subcategory').hide();
            clearSelected('subcategory_list');
            $('#div_hide_sub_subcategory').hide();
            clearSelected('subtosubcategory_list');
        }
     }
    
    /*function getSubToSubCategory() {
            var str='';
            var val=document.getElementById('subcategory_list');
            for (i=0;i< val.length;i++) { 
                 if(val[i].value!=0){
                    if(val[i].selected){
                        str += val[i].value + ','; 
                    }
            }    }     
            var subcat_id=str.slice(0,str.length -1);
            if(subcat_id!=""){
                $.ajax({          
                        type: "POST",
                        url: "ajax-get-scsc.php",
                        data: {scid:subcat_id,action:'subcat',},
                        success: function(data){
                                //alert(data);
                                $('#subtosubcategory_list').html(data);
                                $('#div_hide_sub_subcategory').show();

                        }
                });
            }else{
                $('#div_hide_sub_subcategory').hide();
                clearSelected('subtosubcategory_list');
            }
    }*/
    function getSubToSubCategory() {
             //alert("subtosubcat");
            var str='';
            var subcat_id='';
            var subsubcat_id='';
            var val=document.getElementById('subcategory_list');
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
            var str2='';
            var val_subsub=document.getElementById('subcategory_list');
            for (i=0;i< val_subsub.length;i++) { 
                 if(val_subsub[i].value!=0){
                    if(val[i].selected){
                        str2 += val_subsub[i].value + ','; 
                    }
                }
            }
            if(str2.length>0){
                var subsubcat_id=str2.slice(0,str2.length -1);
            }
            //alert(subcat_id);
            if(subcat_id!=""){
                $.ajax({          
                        type: "POST",
                        url: "ajax-get-scsc.php",
                        data: {scid:subcat_id,subsubcat_id:subsubcat_id,action:'subcat',},
                        success: function(data){
                                $('#subtosubcategory_list').html(data);
                                $('#div_hide_sub_subcategory').show();
                        }
                });
            } else{
              $('#div_hide_sub_subcategory').hide();
                clearSelected('subtosubcategory_list'); 
            }
    }
    
    function clearSelected(dd_id){
    var elements = document.getElementById(dd_id).options;

    for(var i = 0; i < elements.length; i++){
      elements[i].selected = false;
    }
  }
    /*function check_searchform(){
        var audience= document.searchForm.audience_id.selectedIndex;
       // alert(audience);
        var sector=document.searchForm.sector_list.selectedIndex;
        //alert(sector);
	//var search = document.searchForm.search_text.value = trimspace(document.searchForm.search_text.value);
	if(audience=='-1'){
		alert("Please select at least one audience.");
		document.searchForm.audience_id.focus();
		return false;
	}
       if(sector =='-1'){
		alert("Please select at least one sector.");
		document.searchForm.sector.focus();
		return false;
	}
	return true;
    } */
</script>
<script>
$(document).ready(function(){
    $(function(){
       $('#saveindustryalrt').click(function(){
         var val = [];
         $(':checkbox:checked').each(function(i){
           val[i] = $(this).val();
           
         });
         //alert(val);
        $.ajax({          
                type: "POST",
                url: "ajax-trend-email.php",
                data: {check_category:val,action:'save_industry_alert'},
                success: function(data){
                    //alert(data);
                    if(data!=''){
                    $('#myModal1').modal('show'); 
                     //location.reload();
                    }
                }
           });
       });
     });
        
    });
</script>
