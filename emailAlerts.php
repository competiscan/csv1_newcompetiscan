<?php
$PAGE_HEADING = "Email Alerts";
$TITLE = "Competiscan $PAGE_HEADING";
include 'header_top.php';
require_once('includes/checklogin.php');

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



$id = $_SESSION['sess_userID'];
$sql = "SELECT ID,searchName,priority,emailAlert,notify,sendTo,mail_format,queryDate,weekday FROM cscan_search WHERE userID='$id' AND saved=1 ORDER BY priority,queryDate DESC";
$savedQuery = $DRW->query($sql,$DRW_read); 

if(isset($_POST['send'])) {
	while($row = $DRW->fetch_assoc($savedQuery)) {
		$name = trim($_POST['name'.$row['ID']]);
		
		if(isset($_POST['emailAlert'.$row['ID']]) && $_POST['emailAlert'.$row['ID']]==1) {
			$emailAlert = 1;
			$notify = $_POST['notify'.$row['ID']];
			$sendTo = '';//trim($_POST['sendto'.$row['ID']]);
			$mail_format = $_POST['mail_format'.$row['ID']];
			if(isset($_POST['weekday'.$row['ID']])) {
				$weekday = $_POST['weekday'.$row['ID']];
			}
			elseif($notify=='monthly') {
				$weekday = 1;
			}
			else {
				$weekday = 7;
			}
			if($notify=='weekly' && $weekday==7) {
				$weekday = 1;
			}
			
			if($_POST['old_emailAlert'.$row['ID']]==0) {
				$lasSentText = ",lastSentDate=NOW()";
			}
			else {
				$lasSentText = '';
			}
		}
		else {
			$emailAlert = 0;
			$notify = 'daily';
			$sendTo = '';
			$mail_format = 'HTML';
			$weekday = 7;
			$lasSentText = ",lastSentDate='0000-00-00 00:00:00'";
		}
		
		if($sendTo=='' && $emailAlert==1) {
			$query = "SELECT emailAddress FROM cscan_users WHERE userID='".$_SESSION['sess_userID']."'";
			$result_email = $DRW->query($query,$DRW_read);
			$em = $DRW->fetch_row($result_email);
			$sendTo = $em[0];
		}
		elseif($emailAlert!=1) {
			$sendTo = '';
		}
		
		$insertQuery = "UPDATE cscan_search SET queryDate='{$row['queryDate']}',searchName='".$DRW->real_escape_string($name)."',emailAlert='$emailAlert',notify='".$DRW->real_escape_string($notify)."',sendTo='".$DRW->real_escape_string($sendTo)."',mail_format='".$DRW->real_escape_string($mail_format)."',weekday='".$DRW->real_escape_string($weekday)."'$lasSentText WHERE ID='".$row['ID']."'";
		$DRW->query($insertQuery,$DRW_main);
	}
	ob_end_clean();
	header("Location: {$_SERVER['PHP_SELF']}?save=1");
	exit;
}
if(isset($_GET['save'])) {
	$message = 'Email Alerts have been updated';
}
else {
	$message = '&nbsp;';
}
$monthlyArraytxt = '';
$monthtext = '* Monthly alerts are sent the first Monday of each month';
?> 
<!-- Start of main td in which all contained will be displayed -->
<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post" name="alerter">
<table width="100%" border="0" cellpadding="5" cellspacing="0" class="bodytext">
<?php
if($DRW->num_rows($savedQuery) > 0) {
	?>
	<tr class="subHead" valign="top">
	<td><strong>Search Criteria</strong></td>
	<td width="15%"><strong>Search Name</strong></td>
	<td align="center" width="4%"><strong>Alert</strong></td>
	<td width="19%"><strong>Send Email To</strong></td>
	<td width="9%"><strong>Notify</strong></td>
	<td width="7%"><strong>Every</strong></td>
	<td width="8%"><strong>Format</strong></td>
	</tr>
	<tr><td>&nbsp;</td><td colspan="6" class="error"><?php echo $message ; ?></td></tr>
	<?php
	$i = 0;
	$className = 'white-bg';
	$monthbool = false;
	while($row = $DRW->fetch_assoc($savedQuery)) {
		$mail_format = $row['mail_format'];
		if ($className=='selected-bg1') {
			$className = 'white-bg';
		}
		else {
			$className = 'selected-bg1';
		}
		?>
		<tr class="<?php echo $className; ?>">
		<td class="bodytext" valign="top">
		<?php 
		list($displayKeywords) = getKeywords($row['ID']);
		echo $displayKeywords;
		?>
		</td>
		<td class="bodytext" valign="top"><input class="input_box" type="text" name="<?php echo 'name'.$row['ID']; ?>" size = "20" value= "<?php echo htmlspecialchars($row['searchName'], ENT_QUOTES);?>" /><br /><?php
		if($row['emailAlert'] == 1) {
			echo '<a href="sendLink.php?send_mode=3&amp;ssid='.$row['ID'].'&amp;alert=1" onclick="shareColleague('.$row['ID'].',1); return false;" class="HyperLink">Share Alert';
		}
		else {
			echo '<a href="sendLink.php?send_mode=3&amp;ssid='.$row['ID'].'" onclick="shareColleague('.$row['ID'].',0); return false;" class="HyperLink">Share Search';
		}
		?></a>
		</td>
		<td class="bodytext" valign="top">
		<?php 
		if($row['emailAlert'] == 1) {
			$check = 'checked';
			$dis2 = $dis = '';
		}
		else {
			$check = '';
			$dis2 = $dis = ' disabled="disabled"';
		}
		?>
		<input type="checkbox" name="<?php echo 'emailAlert'.$row['ID']; ?>" value="1" <?php echo $check; ?> onclick="greyOut(<?php echo $row['ID']; ?>);" />
		<input type="hidden" name="<?php echo 'old_emailAlert'.$row['ID']; ?>" value="<?php echo $row['emailAlert']; ?>" />
		</td>
		<td class="bodytext" valign="top">
		<input type="text" name="<?php echo "sendto".$row['ID']; ?>" value="<?php echo htmlspecialchars($row['sendTo'], ENT_QUOTES); ?>" size="25" class="input_box" disabled="disabled" />
		</td>
		<td class="bodytext" valign="top">
		<select name="<?php echo "notify".$row['ID']; ?>" class="input_box"<?php echo $dis; ?> onchange="greyOutWeek(<?php echo $row['ID']; ?>);">
		<option value="daily" <?php 
		if($row['notify']=="daily") {
			echo 'selected="selected"';  
			$dis2 = ' disabled="disabled"';
		}
		?>>Daily</option>
		<option value="weekly" <?php 
		if($row['notify']=="weekly") {
			echo 'selected="selected"';
		}
		?>>Weekly</option>
		<option value="monthly" <?php 
		if($row['notify']=="monthly") {
			echo 'selected="selected"';
			$monthbool = true;
			$monthlyArraytxt .= "monthlyArray[{$row['ID']}] = 1;\n";
			$dis2 = ' disabled="disabled"';
		}
		else{
			$monthlyArraytxt .= "monthlyArray[{$row['ID']}] = 0;\n";
		}
		?>>Monthly</option>
		</select>
		</td>
		<td class="bodytext" valign="top">
		<select name="<?php echo "weekday".$row['ID']; ?>" class="input_box"<?php echo $dis2; ?>>
		<?php
		$weekArray = array(7=>'&nbsp;',0=>'Sun',1=>'Mon',2=>'Tue',3=>'Wed',4=>'Thu',5=>'Fri',6=>'Sat');
		foreach($weekArray as $k=>$v){
			echo " <option value=\"$k\"";
			if($k==$row['weekday']) {
				echo ' selected="selected"';
			}
			echo ">$v</option>";
		}
		?>
		</select>
		</td>
		<td class="bodytext" valign="top">
		<select name="<?php echo "mail_format".$row['ID']; ?>" class="input_box"<?php echo $dis; ?>>
		<option value="HTML" <?php if($mail_format=="HTML") echo 'selected="selected"'; ?>>HTML</option>
		<option value="text" <?php if($mail_format=="text") echo 'selected="selected"'; ?>>Text</option>
		</select>
		</td>
		</tr>
		<?php
        }
		?>
		<tr>
		<td class="bodytext">&nbsp;</td>
		<td colspan="2"><input type="submit" name="submit" value="Update" class="submitbutton" /><input type="hidden" name="send" value="1" /></td>
		<td colspan="4"><span id="monthtext" class="bodytext" style="font-style:italic;"><?php 
		if($monthbool) {
			echo $monthtext;
		}
		?>&nbsp;</span></td> 
		</tr>
		<?php
}
else {
	?>
	<tr><td align="center" class="error">No Email Alerts Found</td></tr>
	<?php 
} 
?>
</table>
</form>
<script type="text/JavaScript">
<!--
var monthlyArray = new Array();
<?php echo $monthlyArraytxt; ?>
function greyOut(id) {
	var emailAlerttext = 'emailAlert'+id;
	var notifytext = 'notify'+id;
	var sendtotext = 'sendto'+id;
	var mail_formattext = 'mail_format'+id;
	var weekdaytext = 'weekday'+id;
	if(document.alerter[emailAlerttext].checked) {
		document.alerter[notifytext].disabled = false;
		//document.alerter[sendtotext].disabled = false;
		document.alerter[mail_formattext].disabled = false;
		greyOutWeek(id);
		document.alerter[sendtotext].value = '<?php echo addslashes($_SESSION['sess_username']); ?>';
	}
	else {
		document.alerter[notifytext].disabled = true;
		//document.alerter[sendtotext].disabled = true;
		document.alerter[mail_formattext].disabled = true;
		document.alerter[weekdaytext].disabled = true;
		document.alerter[sendtotext].value = '';
	}
}
function greyOutWeek(id) {
	var notifytext = 'notify'+id;
	var weekdaytext = 'weekday'+id;
	if(document.alerter[notifytext].selectedIndex==1) {
		document.alerter[weekdaytext].disabled = false;
		if(document.alerter[weekdaytext].selectedIndex==0){
			document.alerter[weekdaytext].selectedIndex = 2;
		}
		monthlyArray[id] = 0;
	}
	else {
		if(document.alerter[notifytext].selectedIndex==2){
			document.alerter[weekdaytext].selectedIndex = 2;
			monthlyArray[id] = 1;
		}
		else{
			document.alerter[weekdaytext].selectedIndex = 0;
			monthlyArray[id] = 0;
		}
		document.alerter[weekdaytext].disabled = true;
	}
	showMonthlyText();
}
function showMonthlyText(){
	var monthtext_id = document.getElementById('monthtext');
	var kid = monthtext_id.childNodes;
	var new_monthtext = document.createTextNode('');
	for(var i=0;i<monthlyArray.length;i++){
		if(monthlyArray[i] && monthlyArray[i]==1){
			new_monthtext = document.createTextNode('<?php echo $monthtext; ?>');
			break;
		}
	}
	monthtext_id.replaceChild(new_monthtext, kid[0]);
}
function shareColleague(ssid,alert) {
	var alertget = '';
	if(alert){
		alertget = '&alert=1';
	}
	var wind = window.open('sendLink.php?send_mode=3&ssid='+ssid+alertget,"share","left=20, top=20, scrollbars=yes, resizable=yes, width=625, height=475");
	wind.focus();
}
//-->
</script>
<?php
include 'footer_bottom.php';
