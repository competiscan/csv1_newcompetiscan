<?php
$PAGE_HEADING = "Trend Email Alerts";
$TITLE = "Competiscan $PAGE_HEADING";
include 'header_top.php';
require_once('includes/checklogin.php');
require_once('includes/rpv-dashboard-function.php');
$postdataTrend=array();
$id = $_SESSION['sess_userID'];
if(!empty($id)){
    //echo $postdata=json_encode($postdataTrend);
    $ApiTrendEmailAlert=TREND_REPORT_API_UAT_URL.'manage_trend_email?userid='.$id;
    $GetTrendEmailAlertData = callAPI('GET', $ApiTrendEmailAlert, false);
    $ResTrendEmailAlertData = json_decode($GetTrendEmailAlertData, true);
    // echo "<pre>";
    // print_r($ResTrendEmailAlertData);
    // echo "</pre>"; die;
    
}
if(isset($_POST['send'])) {
    $ids = array();
    foreach ($_POST as $key => $value) {
        if (strpos($key, "trendemailAlert") === 0) { // Check if the key starts with 'trendemailAlert'
            $ids[] = str_replace("trendemailAlert", "", $key); // Extract the numeric part
        }
    }
// Convert array to a comma-separated string
$idString = '['.implode(",", $ids).']';
//echo $idString;
    if(!empty($idString)){
        $ApiTrendUpdateEmail=TREND_REPORT_API_UAT_URL.'manage_trend_email?ids='.$idString.'&userid='.$id;
        $GetTrendUpdateData = callAPI('PUT', $ApiTrendUpdateEmail, false);
        $ResTrendUpdateData = json_decode($GetTrendUpdateData, true);
        // echo "<pre>";
        // print_r($ResTrendUpdateData);
        // echo "<pre>";
        if($ResTrendUpdateData['status_code']=200 && $ResTrendUpdateData['status_code']='Success'){
            ob_end_clean();
            header("Location: {$_SERVER['PHP_SELF']}?save=1");
            exit;

        }
        // echo "<pre>";
        // print_r($ResTrendUpdateData);
        // echo "</pre>"; die;
        // if(!empty($ResTrendSaveData && isset($ResTrendData['data']))){
        // }
    }
	
}
if(isset($_GET['save'])) {
	$message = 'Trend Email Alerts have been updated';
        
}
else {
	$message = '&nbsp;';
}
?> 
<div>
    <div class="col-md-2 pull-right">
        <span style="float:right;"> <a class="submitbutton" href="trend_reports_test.php">Back / Cancel</a> </spn>
    </div> 
</div>
<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post" name="alerter">
<table width="100%" border="0" cellpadding="5" cellspacing="0" class="bodytext">
<?php
if(!empty($ResTrendEmailAlertData)){
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
	foreach($ResTrendEmailAlertData as $value_Data){
		if ($className=='selected-bg1') {
			$className = 'white-bg';
		}
		else {
			$className = 'selected-bg1';
		}
                if(!empty($value_Data['searchKey']) && $value_Data['searchKey']!=''){
                $searchKey = $value_Data['searchKey'];
                $display_search_key.= " ".$searchKey;
                }
                if(!empty($value_Data['mPanelID']) && $value_Data['mPanelID']!=''){
                $audiencename = mediaPanelName($value_Data['mPanelID']);
                $display_search_key.= " <b>Audience:</b> ".$audiencename;
                }
                if(!empty($value_Data['sectorID']) && $value_Data['sectorID']!=''){
                $sectorName= sectorName($value_Data['sectorID']);
                $display_search_key.= " <b>Sector:</b> ".$sectorName;
                }
                if(!empty($value_Data['categoryID']) && $value_Data['categoryID']!=''){
                    $sectorName= sectorName($value_Data['categoryID']);
                    $display_search_key.= " <b>Category:</b> ".$sectorName;
                }
                if(!empty($value_Data['subcategoryID']) && $value_Data['subcategoryID']!=''){
                    $sectorName= sectorName($value_Data['subcategoryID']);
                    $display_search_key.= " <b>SubCategory:</b> ".$sectorName;
                }
                if($value_Data['from_date']!='' && $value_Data['from_date']!='0000-00-00') { 
               
                $display_search_key.= " <b>From Date:</b> ".$value_Data['from_date'];
                 }
                if($value_Data['to_date']!="" && $value_Data['to_date']!='0000-00-00') { 
                        
                        $display_search_key.= " <b>To Date:</b> ".$value_Data['to_date'];
                }
                if($value_Data['country']==1){
                    $countryName ="UNITED STATES";
                    }
                    elseif($value_Data['country']==3){
                        $countryName ="CANADA";
                    } elseif($value_Data['country']==0){
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
                    <?php if($value_Data['alert_type']==1){ echo "Checkbox";} elseif($value_Data['alert_type']==0){ echo "Keyword";} ?>
                </td>
		<td class="bodytext" valign="top" style="text-align: center;">
		<?php 
		if($value_Data['emailAlert']==1) {
			$check = 'checked';
		}
		else {
			$check = '';
		}
		?>
		<input type="checkbox" name="<?php echo 'trendemailAlert'.$value_Data['ID']; ?>" value="1" <?php echo $check; ?> />
		<!--<input type="hidden" name="<?php echo 'old_trendemailAlert'.$value_Data['ID']; ?>" value="<?php echo $value_Data['emailAlert']; ?>" />-->
		</td>
		<td class="bodytext" valign="top">
                    <a href="javascript:void('0');" onclick="myFunction('<?php echo $value_Data['ID']; ?>');" class="delete_alert" title="Delete" data-id="<?php echo $value_Data['ID']; ?>"><img src="images/drop.png" border="0"> Delete</a> 
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
         url: "ajax-trend-email_test.php",
         data: {trendsearch_id:trendsearch_id,action:'search_alert_delete'},
         success: function(data){
         //alert(data);
             if(data==1){
              location.href='trend_email_alert_test.php';
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
