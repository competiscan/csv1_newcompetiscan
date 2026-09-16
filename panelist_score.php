<?php 
$LOGOUT_PAGE = 'content/index.php';
$ALLOW_GROUPS = array(20);
require_once("auth_auth.php");
include('includes/functions.php');

if(isset($_GET['muid'])) $muid = (int)$_GET['muid'];
else $muid = 0;
if(isset($_POST['panelist_score'])){
	$panelist_score = intval($_POST['panelist_score']);
}
else{
	$panelist_score = 0;
}

if(isset($_REQUEST['hy'])){
	$hy = (int)$_REQUEST['hy'];
}
if(empty($hy)){
	$hy = '';
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Competiscan Score</title>
<link href="includes/competiscan_stylesheet.css" rel="stylesheet" type="text/css" />
<style type="text/css">
<!--
 .likeresults td {
	border-width: 1px;
	padding: 4px;
	border-style: dotted;
	border-bottom-color:#D80000;
	border-left:white;
	border-right:white;
	border-top:white;
}
.section {
	margin-top: 8px;
	padding: 4px;
	border: solid 1px #000000;
}
-->
</style>
<script type="text/javascript">
<!--
function doOnload(){
	if(!window.opener.closed){
		if(window.opener.location.pathname.indexOf('imap.php')>=0){
			window.opener.location.href = 'imap.php<?php if($muid!=0) print "?save=$muid#muid$muid"; ?>';
		}
		var obj = window.opener.document.getElementById('score_link');
		if(obj){
			var kid = obj.childNodes;
			obj.replaceChild(window.opener.document.createTextNode('<?php echo $panelist_score; ?>'), kid[0]);
		}
	}
	self.close();
}
//-->
</script>
</head>
<?php
if(isset($_POST['panelist_score'])) {
	print '<body style="margin:6px;" onload="doOnload();">';
	if($_POST['contact_type_m_c']=='cons_panelist'){
		$panelist_score = intval($_POST['panelist_score']);
		$panelist_score_old = intval($_POST['panelist_score_old']);
		if($panelist_score!=$panelist_score_old){
			$query = "SELECT SUM(`panelist_score`) FROM `cscan_email$hy` WHERE `muid`<>'".$DRW->real_escape_string($muid)."' AND `panelist_id`=".(int)$_POST['panelist_id'];
			$query_result = $DRW->query($query,$DRW_read);
			$data2 = $DRW->fetch_row($query_result);
			$panelist_score_sum = (int)$data2[0];
			
			//this can be changed if panelist_id is saved with score instead of sugar_id
			$result2 = $DRW->query("SELECT SUM(ps_score) FROM cscan_panelist_score cs,cscan_panelists cp WHERE cp.`panelist_id`=".(int)$_POST['panelist_id']." AND cs.panelist_id=sugar_id",$DRW_read);
			$data2 = $DRW->fetch_row($result2);
			$ps_score = (int)$data2[0];
			
			$totalbefore = $panelist_score_sum+$ps_score;
			if($totalbefore>2000){
				$totalbefore = $totalbefore % 2000;
			}
			$total = $panelist_score+$totalbefore;
			
			/*if($totalbefore<2000 && $total>=2000){
				$result = $DRW->query("SELECT first_name,last_name,competi_id,phone, address, city, state, postalcode,email,alt_email FROM cscan_panelists WHERE `panelist_id`=".(int)$_POST['panelist_id'],$DRW_read);
				$data = $DRW->fetch_row($result);
				$first_name = $data[0];
				$last_name = $data[1];
				$competi_id = $data[2];
				$phone = $data[3];
				$address = $data[4];
				$city = $data[5];
				$state = $data[6];
				$postalcode = $data[7];
				$email = $data[8];
				$alt_email = $data[9];
				
				if($competi_id!=''){
					$body = "Panelist 2,000 Points Reminder\n\n$first_name $last_name ($competi_id)\n$address\n$city, $state $postalcode\n\n$phone\n$email\n$alt_email";
					$subject = "Panelist 2,000 Points Reminder ($competi_id) ".date('m/d/Y');
					$to = 'maureen@competiscan.com';
					$headers = "From: $to\n";
					mail($to,$subject,$body,$headers);
				}
			}*/
		}
	}
	$sql = "UPDATE `cscan_email$hy` SET `panelist_score`='".$DRW->real_escape_string($_POST['panelist_score'])."' WHERE `muid`='".$DRW->real_escape_string($muid)."'";
	$DRW->query($sql,$DRW_main);
}
else{
	print '<body style="margin:6px;">';
	
	$query = "SELECT DATE_FORMAT(`email_date`,'%m/%d/%y %h:%i %p'),`email_to`,`email_from`,`email_subject`,`contact_type_m_c`,`panelist_score`,`panelist_id` FROM `cscan_email$hy` WHERE `muid`='".$DRW->real_escape_string($muid)."'";
	$query_result = $DRW->query($query,$DRW_read);
	$data = $DRW->fetch_row($query_result);
	$email_date = $data[0];
	$email_to = $data[1];
	$email_from = $data[2];
	$email_subject = $data[3];
	$contact_type_m_c = $data[4];
	$panelist_score = $data[5];
	$panelist_id = $data[6];
	
	print "<div class=\"section\" style=\"background-color:#E8E8FF;\"><form method=\"post\" name=\"prodForm\" action=\"{$_SERVER['PHP_SELF']}?muid=$muid\">
	<table border=\"0\" cellpadding=\"4\" cellspacing=\"2\">
	<tr><td class=\"bodytext\" valign=\"top\">Subject:</td><td>".htmlspecialchars($email_subject)."</td></tr>
	<tr><td class=\"bodytext\" valign=\"top\">Sender:</td><td>".htmlspecialchars($email_from)."</td></tr>
	<tr><td class=\"bodytext\" valign=\"top\">Panelist Score:</td><td><select name=\"panelist_score\">";
	//also change in imap.php, panelist_report_iframe_month.php and alter table cscan_crm_contacts_data
	if($contact_type_m_c=='cons_panelist') $options = array('0'=>'0','2'=>'2','3'=>'3','5'=>'5','10'=>'10','50'=>'50');
	else $options = array('0'=>'0','1'=>'1');
	foreach($options as $val=>$option){
		print "<option value=\"$val\"";
		if($val==$panelist_score) print ' selected="selected"';
		print ">$option</option>";
	}
	print "</select></td></tr>
	<tr><td>&nbsp;</td><td><input class=\"button\" type=\"submit\" name=\"save\" value=\"Save\" /> &nbsp; <input class=\"button\" type=\"submit\" name=\"cancel\" value=\"Cancel\" onclick=\"self.close(); return false;\" /></td></tr>
	</table>
	<input type=\"hidden\" name=\"panelist_id\" value=\"$panelist_id\" />
	<input type=\"hidden\" name=\"contact_type_m_c\" value=\"$contact_type_m_c\" />
	<input type=\"hidden\" name=\"panelist_score_old\" value=\"$panelist_score\" />
	<input type=\"hidden\" name=\"hy\" value=\"$hy\" /></form>
	</div>";
}
?>
</body>
</html>