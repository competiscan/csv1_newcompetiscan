<?php 
$ALLOW_GROUPS = array(18);
require_once("../auth_auth.php");
include 'top.php';
require_once '../includes/functions.php';
require_once 'mandrillMailer.php';
require_once 'manageUserTracker_report.php';
?>
<script type="text/javascript" src="js_calendar/calendar.js?new=20110531"></script>
<table width="100%" border="0" cellspacing="0" cellpadding="5" class="text">
	<tr>
		<td class="adminhead" align="center">USAGE REPORT</td>
	</tr>
</table>
<div>&nbsp;</div>
<form name="searchForm" method="get" action="<?php $_SERVER['PHP_SELF']; ?>">
<table border="0" cellspacing="0" cellpadding="4" class="text">
	<tr>
		<td>Company Name</td><td><select name="companyName" class ="combo_box"><option value="">All</option><?php 
		$sql = "select distinct companyName from cscan_users order by companyName";//where active='y' 
		$rs  = $DRW->query($sql,$DRW_read);
		while($data = $DRW->fetch_row( $rs )) {
			echo '<option value="'.htmlspecialchars($data[0],ENT_QUOTES).'"';
			if(isset($_REQUEST['companyName']) && $_REQUEST['companyName']==$data[0]) {
				echo ' selected="selected"';
			}
			echo '>'.htmlspecialchars($data[0]).'</option>';
		}
		?></select></td>
	</tr>
	<tr>
		<td>Start Date</td>
		<td><input type="text" name="StartDate" size="20" class="input_box" value="<?php 
		if(isset($_REQUEST['StartDate'])) {
			echo htmlspecialchars($_REQUEST['StartDate'],ENT_QUOTES); 
		}
		?>" />
		<a href="#" onclick="displayCalendar(document.searchForm.StartDate,'yyyy-mm-dd',this); return false;"><img name="popcal1" src="js_calendar/images/getcal.gif" border="0" alt="" style="vertical-align:bottom;"></a></td>
	</tr>
	<tr>
		<td>End Date</td>
		<td><input type="text" name="EndDate" size="20" class="input_box" value="<?php 
		if(isset($_REQUEST['EndDate'])){
			echo htmlspecialchars($_REQUEST['EndDate'],ENT_QUOTES); 
		}
		?>" />
		<a href="#" onclick="displayCalendar(document.searchForm.EndDate,'yyyy-mm-dd',this); return false;"><img name="popcal2" src="js_calendar/images/getcal.gif" border="0" alt="" style="vertical-align:bottom;" /></a></td>
	</tr>
	<tr>	
		<td>&nbsp;</td><td><input type="submit" name="submit" value="Search" class="button" /> &nbsp; <input type="submit" name="clear" value="Clear" class="button" onclick="location.href='<?php print $_SERVER['PHP_SELF']; ?>'; return false;" /></td>
	</tr>
</table>
<div>&nbsp;</div>
<?php
if(isset($_REQUEST['send'])) {
	$report_type = 0;
	if(!empty($_REQUEST['export'])){
		$report_type = 2;
		ob_end_clean();
		header("Content-Type: application/vnd.ms-excel");
		header("Content-Disposition: attachment; filename=usage_report.csv");
		header("Pragma: no-cache");
		header("Expires: 0");
	}
	echo manageUserTracker_report($_REQUEST['StartDate'], $_REQUEST['EndDate'], $_REQUEST['companyName'],$report_type);
	if($report_type==2){
		exit;
	}
	echo '<div>&nbsp;</div><div style="margin-left:10px;"><input type="submit" name="export" value="Export" class="button" /></div>';
}
echo '<input type="hidden" name="send" value="send" /></form>';

include 'bottom.php';
?>