<?php
$PAGE_HEADING = "Trend Email Alerts";
$TITLE = "Competiscan $PAGE_HEADING";
include 'header_top.php';
require_once('includes/checklogin.php');
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






$id = $_SESSION['sess_userID'];
$sql = "SELECT ID,searchKey,mPanelID,sectorID,categoryID,subCategoryID,subSubCategoryID,from_date,to_date,country,emailAlert,queryDate,alert_type FROM cscan_trend_report_search WHERE userID='$id'  ORDER BY queryDate DESC";
$savedQuery = $DRW->query($sql,$DRW_read); 
if(isset($_POST['send'])) {
	while($row = $DRW->fetch_assoc($savedQuery)) {
		//$name = trim($_POST['name'.$row['ID']]);
		if(isset($_POST['trendemailAlert'.$row['ID']]) && $_POST['trendemailAlert'.$row['ID']]==1) {
			$emailAlert = 1;
			
		}
		else {
			$emailAlert = 0;
		}
		$insertQuery = "UPDATE cscan_trend_report_search SET queryDate='{$row['queryDate']}',emailAlert='$emailAlert' WHERE ID='".$row['ID']."'";
		$DRW->query($insertQuery,$DRW_main);
	}
	ob_end_clean();
	header("Location: {$_SERVER['PHP_SELF']}?save=1");
	exit;
}
if(isset($_GET['save'])) {
	$message = 'Trend Email Alerts have been updated';
        
}
else {
	$message = '&nbsp;';
}
//$monthlyArraytxt = '';
//$monthtext = '* Monthly alerts are sent the first Monday of each month';
?> 
<div>
    <div class="col-md-2 pull-right">
        <span style="float:right;"> <a class="submitbutton" href="trend_reports.php">Back / Cancel</a> </spn>
    </div> 
</div>
<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post" name="alerter">
<table width="100%" border="0" cellpadding="5" cellspacing="0" class="bodytext">
<?php
if($DRW->num_rows($savedQuery) > 0) {
	?>
	<tr class="subHead" valign="top">
	<td style="font-size:14px;"><strong>Search Criteria</strong></td>
        <td align="center" style="font-size:14px;" width="15%"><strong>Alert Type</strong></td>
	<td align="center" style="font-size:14px;" width="10%"><strong>Alert</strong></td>
        <td style="font-size:14px;" width="10%"><strong>Action</strong></td>
	</tr>
	<tr><td width="55%">&nbsp;</td><td colspan="2" width="45%" class="error"><?php echo $message ; ?></td></tr>
	<?php
	//$i = 0;
        $display_search_key = '';
	$className = 'white-bg';
	while($row = $DRW->fetch_assoc($savedQuery)) {
                //echo $i;
		if ($className=='selected-bg1') {
			$className = 'white-bg';
		}
		else {
			$className = 'selected-bg1';
		}
                if(!empty($row['searchKey']) && $row['searchKey']!=''){
                $searchKey = $row['searchKey'];
                $display_search_key.= " ".$searchKey;
                }
                if(!empty($row['mPanelID']) && $row['mPanelID']!=''){
                $audiencename = mediaPanelName($row['mPanelID']);
                $display_search_key.= " <b>Audience:</b> ".$audiencename;
                }
                if(!empty($row['sectorID']) && $row['sectorID']!=''){
                $sectorName= sectorName($row['sectorID']);
                $display_search_key.= " <b>Sector:</b> ".$sectorName;
                }
                if(!empty($row['categoryID']) && $row['categoryID']!=''){
                $categoryName= categoryName($row['categoryID']);
                $display_search_key.= " <b>Category:</b> ".$categoryName;
                }
                if(!empty($row['subCategoryID']) && $row['subCategoryID']!=''){
                $subCategoryName= subCategoryName($row['subCategoryID']);
                $display_search_key.= " <b>Sub Category:</b> ".$subCategoryName;
                }
                if(!empty($row['subSubCategoryID']) && $row['subSubCategoryID']!=''){
                $subtosubCategoryName= subCategoryName($row['subSubCategoryID']);
                $display_search_key.= " <b>Sub sub Category:</b> ".$subtosubCategoryName;
                }
                
                if($row['from_date']!='' && $row['from_date']!='0000-00-00') { 
               
                $display_search_key.= " <b>From Date:</b> ".$row['from_date'];
                 }
                if($row['to_date']!="" && $row['to_date']!='0000-00-00') { 
                        
                        $display_search_key.= " <b>To Date:</b> ".$row['to_date'];
                }
                //if($row['country']!=''){
                if($row['country']==1){
                    $countryName ="UNITED STATES";
                    }
                    elseif($row['country']==3){
                        $countryName ="CANADA";
                    } elseif($row['country']==0){
                       $countryName ="All";  
                    }
                    $display_search_key.= " <b>Country:</b> ".$countryName;
               /* }else{
                   $countryName ="All"; 
                   $display_search_key.= " <b>Country:</b> ".$countryName;
                }*/
		?>
		<tr class="<?php echo $className; ?>">
		<td class="bodytext" valign="top" style="color:#65656b;font-size: 14px;line-height: 21px">
		<?php
		echo $display_search_key;
		?>
		</td>
                <td style="text-align:center;">
                    <?php if($row['alert_type']==1){ echo "Checkbox";} elseif($row['alert_type']==0){ echo "Keyword";} ?>
                </td>
		<td class="bodytext" valign="top" style="text-align: center;">
		<?php 
		if($row['emailAlert']==1) {
			$check = 'checked';
		}
		else {
			$check = '';
		}
		?>
		<input type="checkbox" name="<?php echo 'trendemailAlert'.$row['ID']; ?>" value="1" <?php echo $check; ?> />
		<!--<input type="hidden" name="<?php echo 'old_trendemailAlert'.$row['ID']; ?>" value="<?php echo $row['emailAlert']; ?>" />-->
		</td>
		<td class="bodytext" valign="top">
                    <a href="javascript:void('0');" onclick="myFunction('<?php echo $row['ID']; ?>');" class="delete_alert" title="Delete" data-id="<?php echo $row['ID']; ?>"><img src="images/drop.png" border="0"> Delete</a> 
		</td>
                </tr>
                <?php $display_search_key='';} ?>
                <tr>
		<td colspan="3">
                    <input type="submit" name="submit" value="Update" class="submitbutton" />
                    <input type="hidden" name="send" value="1" />
                </td>
		</tr>
		<?php
                }
                else {
                    ?>
                    <tr><td align="center" class="error">No Trend Email Alerts Found</td></tr>
                    <?php 
                } 
            ?>
</table>
</form>
<script>
function myFunction(id) {
    //alert(id);
  var trendsearch_id=id;
  var cnfbox = confirm("Are you sure want to delete?");
  if (cnfbox == true) {
        $.ajax({          
         type: "POST",
         url: "ajax-trend-email.php",
         data: {trendsearch_id:trendsearch_id,action:'search_alert_delete'},
         success: function(data){
         //alert(data);
             if(data==1){
              location.href='trend_email_alert.php';
            }
        }           
    });
  } else {
    
  }
  //alert(txt);
  //document.getElementById("demo").innerHTML = txt;
}
</script>
<script type="text/JavaScript">

//When the page has loaded.
$( document ).ready(function(){
    $('.error').fadeIn('slow', function(){
       $('.error').delay(5000).fadeOut(); 
    });
});
       
</script>
<?php
include 'footer_bottom.php';
