<?php 
$PAGE_HEADING = "Trend Reports Email Alert";
$start_time = microtime(true);
//require_once('includes/checklogin.php');
require_once('includes/globalSession.php');
require_once('includes/checklogin.php');
require_once('includes/functions.php');  //latest function
require_once 'class/Trend.php';
$trendObj = new Trends($DRW, $DRW_read,$DRW_main);
$audience_id = array();
$sector_id =array();
$category_id =array();
$subcategory_id=array();
$subtosubcategory_id = array();
$country_id='';
$fdt = '';
$tdt = '';
$offset='0';
$savedArray = array();
$trendArray = array();
$sql = "SELECT ss.sectorID,sectorName,trend_id FROM cscan_sector ss,cscan_sector_users su WHERE su.userID='{$_SESSION['sess_userID']}' AND su.sectorID=ss.sectorID ORDER BY sectorName ASC";
$savedQuery = $DRW->query($sql,$DRW_read);
while($row = $DRW->fetch_row($savedQuery)) {
	$savedArray[$row[0]] = $row[1];
	$trendArray[$row[0]] = $row[2];
} 

if(isset($_REQUEST['audience_id'])) {
	$audience_id = $_REQUEST['audience_id'];
}
if(isset($_REQUEST['sector'])) {
	$sector_id = $_REQUEST['sector'];
}
if(isset($_REQUEST['category'])) {
	$category_id = $_REQUEST['category'];
}
if(isset($_REQUEST['subcategory'])) {
	$subcategory_id = $_REQUEST['subcategory'];
}
if(isset($_REQUEST['subtosubcategory'])) {
	$subtosubcategory_id = $_REQUEST['subtosubcategory'];
}
if(isset($_REQUEST['country'])) {
	$country_id = $_REQUEST['country'];
}
if(isset($_REQUEST['country'])) {
	$country_id = $_REQUEST['country'];
}
if(isset($_REQUEST['fdt'])){
         $fdt = trim($_REQUEST['fdt']);
    }
if(isset($_REQUEST['tdt'])){
        $tdt = trim($_REQUEST['tdt']);
}
if(isset($_REQUEST['clear_search']) and $_REQUEST['clear_search']=='Clear Search') {
	$audience_id = array();
        $sector_id =array();
        $category_id =array();
        $subcategory_id=array();
        $subtosubcategory_id =array();
        $country_id="";
        $fdt = '';
        $tdt = '';
}

if(isset($_POST['send'])){
	if(isset($_POST['alert'])) {
		$alertArray = $_POST['alert'];
	}
	else {
		$alertArray = array();
	}
	$currArray = $savedArray;
	
	foreach($alertArray as $sectorID){
		if(!key_exists($sectorID,$savedArray)){
			$sql = "INSERT IGNORE INTO cscan_sector_users (sectorID,userID,trendSent) VALUES (".(int)$sectorID.",{$_SESSION['sess_userID']},NOW())";
			$DRW->query($sql,$DRW_main);
		}
		else {
			unset($currArray[$sectorID]);
		}
	}
	foreach($currArray as $sectorID=>$name){
		$sql = "DELETE FROM cscan_sector_users WHERE sectorID=".(int)$sectorID." AND userID={$_SESSION['sess_userID']}";
		$DRW->query($sql,$DRW_main);
	}
	
	ob_end_clean();
	header("Location: {$_SERVER['PHP_SELF']}");
	exit; 
}
if(isset($_GET['sort'])) $sort = (int) $_GET['sort'];
else $sort = 0;
if (isset($_GET['page_no']) && $_GET['page_no']!="") {
    $page_no = $_GET['page_no'];
    } else {
    $page_no = 1;
    }
$total_records_per_page = 20;
$offset = ($page_no-1) * $total_records_per_page;
$previous_page = $page_no - 1;
$next_page = $page_no + 1;
$adjacents = "2";
?>
<?php include('header_top_trend.php');?>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<div class="headings" id="pcontainer">
    <div class="col-md-8">
        <strong>Welcome: <?php echo $_SESSION['sess_username']; ?></strong>
    </div>

<div class="col-md-2 pull-right">
        <span style="text-align:right;"> <a class="submitbutton" href="trend_reports_test.php">Trend Reports</a> </spn>
    </div>

</div>
<hr style="float:left; width:100%" />
<div class="error" style="float:right;"></div>		
<div  class="left_box">

<form style="max-width:750px" action="trend_reports_email_alert.php" method="post">
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                Audience:
            </div>
        </div>
        <div class="col-md-9">
            <div class="form-group">
                <select class="form-control" name ="audience_id[]" multiple="multiple" size="3"><!--<option selected value="">--Any--</option>-->
                    <?php 
                        $mailing_panel = getMailingPanel();
                       // print_r($audience_id);die;
                        foreach($mailing_panel as $mid=>$name){ 

                            ?>
                        <option  <?php if(in_array($mid,$audience_id)) { echo "selected"; } ?> value="<?php echo $mid;?>" ><?php echo htmlspecialchars($name, ENT_QUOTES); ?></option>
                        <?php }  ?>
                    </select> 
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
                <select class="form-control" name ="sector[]" id ="sector_list" onChange ="getCategory();" size="3" multiple="multiple">
                    <?php 
                        $sector = getSector();
                        foreach($sector as $id=>$name){
                                if(!empty($id)){ ?>
                                    <option  <?php if(in_array($id,$sector_id)) { echo "selected"; } ?> value="<?php echo $id;?>" ><?php echo htmlspecialchars($name, ENT_QUOTES); ?></option> 
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
                Category:
            </div>
        </div>
        <div class="col-md-9">
            <div class="form-group">
                 <select id ="category_list" name ="category[]" onChange ="getSubCategory();" multiple="multiple" size="3" class="form-control cat_id"><option <?php $o_count = count($category_id); if($o_count==0 || $o_count==1 && $category_id[0]=="" ) { echo "selected"; } ?> value="">--Any--</option>
                    <?php 
                    
                      if(!empty($sector_id)){
                      $sector_cat_id = @implode(',',$sector_id);
                      $category = $trendObj->getCategoryMulti($sector_cat_id);
                       foreach($category as $id=>$name){
                               if(!empty($id)){ ?>
                                   <option  <?php if(in_array($id,$category_id)) { echo "selected"; } ?> value="<?php echo $id;?>" ><?php echo htmlspecialchars($name, ENT_QUOTES); ?></option> 
                      <?php }
                       }
                      }
                       ?>
                </select>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                Sub Category:
            </div>
        </div>
        <div class="col-md-9">
            <div class="form-group">
                 <select id ="subcategory_list" name ="subcategory[]" onchange="getSubToSubCategory();" multiple="multiple" size="3" class="form-control"><option <?php $o_count = count($subcategory_id); if($o_count==0 || $o_count==1 && $subcategory_id[0]==""){ echo "selected";} ?> value="" >--Any--</option>
                    <?php 
                    
                   //if(count($category_id) == 0) 
                    if(!empty($category_id) && $category_id[0]!="" && !in_array(0,$category_id)){
                      $sector_subcat_id = @implode(',',$category_id); 
                      $sub_category = $trendObj->getSubCategoryMulti($sector_subcat_id,false);
                       foreach($sub_category as $id=>$name){
                               if(!empty($id)){ ?>
                                   <option  <?php if(in_array($id,$subcategory_id)) { echo "selected"; } ?> value="<?php echo $id;?>" ><?php echo htmlspecialchars($name, ENT_QUOTES); ?></option> 
                      <?php }
                       }
                    }
                   ?>
                  </select>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                Sub Sub Category:	
            </div>
        </div>
        <div class="col-md-9">
            <div class="form-group">
                <select id ="subtosubcategory_list" name ="subtosubcategory[]"  multiple="multiple" size="3" class="form-control"><option <?php $o_count = count($subtosubcategory_id); if($o_count==0 || $o_count==1 && $subtosubcategory_id[0]==""){ echo "selected";} ?> value="">--Any--</option>
                    <?php 
                       
                       if(!empty($subcategory_id) && !in_array(0,$subcategory_id) && !empty($subtosubcategory_id)){
                          
                       $sector_subtosubcat_id = @implode(',',$subcategory_id);
                       $sub_tosubcategory = $trendObj->getSubCategoryMulti($sector_subtosubcat_id,false);
                       foreach($sub_tosubcategory as $id=>$name){
                                if(!empty($id)){ ?>
                                    <option  <?php if(in_array($id,$subtosubcategory_id)) { echo "selected"; } ?> value="<?php echo $id;?>" ><?php echo htmlspecialchars($name, ENT_QUOTES); ?></option> 
                       <?php }
                       }}
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
                
                 <input type="text" id="fdt" readonly='true' name="fdt" maxlength="10" class="form-control ui-datepicker-trigger" value="<?php echo $fdt; ?>" />
                
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
                 <input type="text" id="tdt" readonly='true' name="tdt" maxlength="10" class="form-control" value="<?php echo $tdt; ?>" />
                
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
                <label><input type="radio" <?php if(!empty($country_id) && $country_id=='1'){ echo "checked";} ?> name="country" value="1">UNITED STATES</label> 
                <label><input type="radio" <?php if(!empty($country_id) && $country_id=='3'){ echo "checked";} ?> name="country" value="3">CANADA</label> 
                <label><input type="radio" <?php if(!empty($country_id) && $country_id=='0'){ echo "checked";}elseif($country_id==""){ echo "checked"; } ?> name="country" value="">All</label>
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
               <input class="submitbutton" type="submit" name="search" value="Clear Search">
            </div>
        </div>
    </div>
</form>
</div>
<div class="right_box">
<form name="trendForm" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
    <div class="trend-reports-tabla">
        <table width="100%" cellpadding="4" cellspacing="0" class="sortable">
            <tbody>
                <tr>
                    <td width="5%" class="toptable" style="border:none; padding-left: 15px;"> Sr.No</td>
                    <td width="20%" class="toptable" style="border:none; padding-left: 15px;">Sector/Category/Subcategory</td>
                    <td width="15%" class="toptable" style="border:none;">Audience<img src="images/spacer.gif" border="0" style="vertical-align:bottom;" width="15" height="15"></td>
                    <td width="15%" class="toptable" style="border:none;">Country</td>
                    <td width="15%" class="toptable" style="border:none;">Last Sent</td>
                    <td width="15%" class="toptable" style="border:none;">All Document</td>
                    <td width="5%" class="toptable" style="border:none;">Email Alert</td>
                </tr>
                <?php
	               $sql = "SELECT COUNT(*) as file_count,ctr.trend_id,trend_name,trend_link,ctr.category_id,file_path,file_name,audience_id,country_id,trend_date,ctc.sector_id,ctc.category_id,ctc.subcategory_id,ctc.subtosubcategory_id FROM cscan_trends_category ctc  LEFT JOIN cscan_trend_report ctr ON ctr.trend_id = ctc.trend_id";
                        $rs = $DRW->query($sql,$DRW_read);
                        $numquery = "SELECT COUNT(ctr.trend_id) as numrows  FROM cscan_trends_category ctc LEFT JOIN cscan_trend_report ctr ON 
                               ctr.trend_id = ctc.trend_id";
                        
                        /*$sql = "SELECT DISTINCT(ctr.trend_id),trend_name,trend_link,ctr.category_id,file_path,file_name,audience_id,country_id,trend_date,ctc.sector_id,ctc.category_id,ctc.subcategory_id,ctc.subtosubcategory_id FROM cscan_trend_report ctr LEFT JOIN cscan_trends_category ctc ON 
                               ctc.trend_id = ctr.trend_id"; 
                        $rs = $DRW->query($sql,$DRW_read);
                        $numquery = "SELECT COUNT(ctr.trend_id) as numrows  FROM cscan_trend_report ctr LEFT JOIN cscan_trends_category ctc ON 
                               ctc.trend_id = ctr.trend_id";*/
                        $where ='';
                        if(!empty($audience_id) || $country_id!='' || !empty($audience_id) || !empty($sector_id) ||!empty($category_id) || !empty($subcategory_id) || !empty($subtosubcategory_id)){
                            $where =" WHERE 1=1";
                                $sql .= $where;
                                $numquery .=$where;
                        }
                       if(!empty($audience_id)) { 
                                $audience_id = @implode(" ,",$audience_id);
                                $and = " and audience_id In ($audience_id)";
                                $sql .= $and;
                                $numquery .= $and;
                        }
                      if($country_id!="") { 
                                $and = " and ctr.country_id = $country_id";
                                $sql .= $and;
                                $numquery .= $and;
                        }


                     if(!empty($sector_id)) { 
                                $sector_id = @implode(" ,",$sector_id);
                                $and = " and ctc.sector_id In ($sector_id)";
                                $sql .= $and;
                                $numquery .= $and;
                               // $groupby .= " 
                        }
                        if(!empty($category_id) && $subcategory_id[0]!=0) { 
                                $category_id = @implode(" ,",$category_id);
                                $and = " and ctc.category_id In ($category_id)";
                                $sql .= $and;
                                $numquery .= $and;
                        }
                        if(!empty($subcategory_id) && $subcategory_id[0]!=0) { 
                                $subcategory_id = @implode(" ,",$subcategory_id);
                                $and = " and ctc.subcategory_id In ($subcategory_id)";
                                $sql .= $and;
                                $numquery .= $and;
                        }
                       
                        if(!empty($subtosubcategory)) { 
                                $subtosubcategory = @implode(" ,",$subtosubcategory);
                                $and = " and ctc.subtosubcategory_id In ($subtosubcategory)";
                                $sql .= $and;
                                $numquery .= $and;
                        }
                         if($fdt!="") { 

                                $and = " and DATE_FORMAT(trend_date, '%Y-%m-%d') >= '".$fdt."'";
                                $sql .= $and;
                                $numquery .= $and;
                        }
        
                        if($tdt!="") { 
                                $and = " And DATE_FORMAT(trend_date, '%Y-%m-%d') <= '".$tdt."'";
                                $sql .= $and;
                                $numquery .= $and;
                        }
                       
                        $numquery = $DRW->query($numquery,$DRW_read);
                        $nrow = $DRW->fetch_array($numquery);
                        $numrows = $nrow[0];
                        $total_records = $numrows;
                        $total_no_of_pages = ceil($total_records / $total_records_per_page);
                       
                        $second_last = $total_no_of_pages - 1; // total pages minus 1
                        $sql .= " GROUP BY ctc.sector_id,ctc.category_id,ctc.subcategory_id,ctc.subtosubcategory_id,ctr.country_id,ctr.audience_id";
                        switch($sort){
                                case 1:
                                        $sql .= " ORDER BY ctr.trend_id ASC ";
                                        break;
                                default:
                                        $sql .= " ORDER BY  ctr.trend_id DESC ";
                        }
                        $sql .= "LIMIT $offset, $total_records_per_page";
                        $rs = $DRW->query($sql,$DRW_read);
                        $resultCount = $DRW->num_rows($rs);
                        if( $resultCount > 0 ) {
                                $className = 'white-bg';
                                $audience='';
                                 $ID= '';
                                 $cnt=1;
                                 $scsc_combo_text="";
                                 $s="";
                                 $c="";
                                 $sc ="";
                                 $ssc ="";
                                while($row = $DRW->fetch_assoc($rs)) {
                                        $ID = $row['trend_id']."<br/>";
                                        $trend_name = $row['trend_name'];
                                        $audience = $row['audience_id']; 
                                        $country_id = $row['country_id']; 
                                        $trend_date = $row['trend_date']; 
                                         $trend_file_path = $row['file_path'];
                                         $trend_file_name = $row['file_name'];
                                         $audience_name = mediaPanelName($audience);
                                         $comboIDs = $trendObj->getAllCategoryByTrendId($ID);
                                        /*if ($className=='selected-bg') $className='white-bg';
                                                        else $className='selected-bg';
                                       /* if(!empty($comboIDs)) {
                                        $comboIDs_split = @explode('|',$comboIDs);
                                        $sectorName='';
                                        $categoryName='';
                                        $subcategoryName ='';
                                        $subtosubcategoryName='';
                                        $tt=1;
                                        foreach($comboIDs_split as $scsc_combo){
                                                        if(!empty($scsc_combo)){
                                                        list($s,$c,$sc,$ssc) = explode('_',$scsc_combo);
                                                        if(!empty($s) || !empty($c) || !empty($sc) || !empty($ssc)){
                                                            $sectorName .= sectorName($s).",";
                                                            if($c!=""){
                                                            $categoryName .= sectorName($c).",";
                                                            }
                                                            if($sc!=""){
                                                             $subcategoryName .= sectorName($sc).",";
                                                             }
                                                              $subtosubcategoryName .= sectorName($ssc).",";
                                                                $scsc_combo_text = sectorName($s).' / '.sectorName($c).' / '.sectorName($sc).' / '.sectorName($ssc);
                                                                //$scsc_combo_text;


                                                       }
                                                    }   
                                            }       
                                        }*/
                                       
                ?>
                
                <tr>
             
                    <td class="bodytext" valign="top" style="border:none; padding-top:15px;padding-bottom: 15px;padding-left:15px;">
                        <?php echo $cnt; ?>-<?php echo $ID;?>
                    </td>
                    <!--<td  class="bodytext" valign="top" style="border:none; padding-top:15px;"><?php echo !empty($scsc_combo_text) ? $scsc_combo_text : 'N/A';?></td>-->
                    <td  class="bodytext" valign="top" style="border:none; padding-top:15px;"><?php  if(!empty($comboIDs)) {
                                        $comboIDs_split = @explode('|',$comboIDs);
                                        $sectorName='';
                                        $categoryName='';
                                        $subcategoryName ='';
                                        $subtosubcategoryName='';
                                        $tt=1;
                                        foreach($comboIDs_split as $scsc_combo){
                                                        if(!empty($scsc_combo)){
                                                        list($s,$c,$sc,$ssc) = explode('_',$scsc_combo);
                                                        if(!empty($s) || !empty($c) || !empty($sc) || !empty($ssc)){
                                                            $sectorName .= sectorName($s).",";
                                                            if($c!=""){
                                                            $categoryName .= sectorName($c).",";
                                                            }
                                                            if($sc!=""){
                                                             $subcategoryName .= sectorName($sc).",";
                                                             }
                                                              $subtosubcategoryName .= sectorName($ssc).",";
                                                               echo $scsc_combo_text = sectorName($s).' / '.sectorName($c).' / '.sectorName($sc).' / '.sectorName($ssc)."<br/>";
                                                                //$scsc_combo_text;


                                                       }
                                                    }   
                                            }       
                                        }?></td>
                    <td class="bodytext" valign="top" style="border:none;  padding-top:15px; padding-right:15px;"><?php echo !empty($audience_name) ? $audience_name : 'N/A';?></td>
                    <td class="bodytext" valign="top" style="border:none;  padding-top:15px; padding-right:15px;"><?php if(!empty($country_id) and $country_id =='1') { echo "UNITED STATES";}elseif(!empty($country_id) and $country_id =='3') { echo "CANADA";}elseif($country_id =='') { echo "ALL";} ?></td>
                    <td class="bodytext" valign="top" style="border:none;  padding-top:15px; padding-right:15px;"><?php echo date('Y-m-d',strtotime($trend_date));?></td>
                    <td class="bodytext" valign="top" style="border:none;  padding-top:15px; padding-right:15px;"><a href="javascript:void(0);" data-id="<?php echo (int)$ID; ?>" class="submitbutton get_model" data-toggle="modal" data-target="#myModal<?php echo (int)$ID;?>">View<?php echo $row['file_count']; ?></a></td>
                    <td class="bodytext" valign="top" style="border:none;  padding-top:15px; padding-right:15px;"><input type="checkbox" name="alert[]" value="<?php echo $comboIDs; ?>"><label></label></td>
                </tr>
                <tr>
                    <td colspan="7" class="border-bottom">&nbsp;</td>
                </tr> 
                <!-----Start Modal------->
                <div class="modal fade" id="myModal<?php echo (int)$ID;?>" role="dialog">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Show Link</h4>
                      </div>
                      <?php $sqlQuery = "SELECT trend_id,trend_link,file_path,file_name FROM cscan_trend_report where trend_id='".(int)$ID."'";
                        $result = $DRW->query($sqlQuery,$DRW_read);
                        while($rowFileData = $DRW->fetch_assoc($result)) {
                            
                        ?>
                      <div class="modal-body">
                        <p><?php echo $rowFileData['file_name']; ?></p>
                      </div>
                      <?php }?>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                      </div>
                    </div>
                  </div>
                </div>
                <!---------End Modal-------->
                <?php
		$cnt++;}
              }
           else { 
            ?>
            <tr>
                <td colspan="7" class=" border-bottom error" align="center">No Result Found.</td>
            </tr>
        <?php
            }
        ?>  
            </tbody>
        </table>       
    </div>
    <div class="row">
       <div class="col-md-12">
           <input type="submit" name="submit" value="Receive Alert" class="submitbutton btn-blue  pull-right" />
           <input type="hidden" name="send" value="1" />
       </div>
   </div>
</form>

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
 
<?php
include 'footer_bottom.php';
?>

<script type="text/JavaScript">
        $( function() {
            $("#fdt").datepicker({
                autoclose: true,  
                dateFormat: 'yy-mm-dd'
               /* showOn: "button",
                buttonImage: "images/calendar.gif",
                buttonImageOnly: true,
                buttonText: "Select from date"
                //maxDate: new Date()*/
            });
            
           $("#tdt").datepicker({
                autoclose: true,  
                dateFormat: 'yy-mm-dd',
               /* showOn: "button",
                buttonImage: "images/calendar.gif",
                buttonImageOnly: true,
                buttonText: "Select from date"
               // maxDate: new Date()*/
            });
        });
/*$('.get_model').click(function() {
    event_id =$(this).data("id");
    alert(event_id);
});*/
</script>
<script type="text/javascript">
    function getCategory() {
            var str='';
            var val=document.getElementById('sector_list');
            for (i=0;i< val.length;i++) { 
                if(val[i].selected){
                    str += val[i].value + ','; 
                }
            }         
            var sect_id=str.slice(0,str.length -1);
            //alert(sect_id);
            $.ajax({          
                    type: "POST",
                    url: "ajax-get-scsc.php",
                    data: {sid:sect_id,action:'sector',},
                    success: function(data){
                            $('.cat_id').html(data);
                            
                    }
            });
    }
    
     function getSubCategory() {
            var str='';
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
           if(cat_id!=""){
            $.ajax({          
                    type: "POST",
                    url: "ajax-get-scsc.php",
                    data: {cid:cat_id,action:'cat',},
                    success: function(data){
                            $('#subcategory_list').html(data);
                            
                    }
            });
        }
     }
    
    function getSubToSubCategory() {
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
                            $('#subtosubcategory_list').html(data);
                            
                    }
            });
        }
    }
</script>

