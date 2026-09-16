<?php  
$PAGE_HEADING = "Trend Reports";
$TITLE = "Competiscan $PAGE_HEADING";
if(isset($_REQUEST['trend_id']) && $_REQUEST['trend_id']!=''){
	$BODYTAG = ' onload="showTrend_id('.(int)$_REQUEST['trend_id'].');"';
}
include 'header_top.php';
require_once('includes/checklogin.php');

$savedArray = array();
$trendArray = array();
$sql = "SELECT ss.sectorID,sectorName,trend_id FROM cscan_sector ss,cscan_sector_users su WHERE su.userID='{$_SESSION['sess_userID']}' AND su.sectorID=ss.sectorID ORDER BY sectorName ASC";
$savedQuery = $DRW->query($sql,$DRW_read);
while($row = $DRW->fetch_row($savedQuery)) {
	$savedArray[$row[0]] = $row[1];
	$trendArray[$row[0]] = $row[2];
}

if(isset($_GET['sid'])){
	$sql = "DELETE FROM cscan_sector_users WHERE sectorID=".(int)$_GET['sid']." AND userID={$_SESSION['sess_userID']}";
	$DRW->query($sql,$DRW_main);
	ob_end_clean();
	header("Location: {$_SERVER['PHP_SELF']}");
	exit; 
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
?>
<script type="text/JavaScript">
<!--
function showTrend(selname,fromlink){
	var trend_id = document.trendForm[selname].options[document.trendForm[selname].selectedIndex].value;
	if(trend_id!=''){
		document.location.href = 'trend_report.php?trend_id='+trend_id;
	}
	else if(fromlink){
		alert('Please select a Trend Report');	
	}
}
function showTrend_id(trend_id){
	document.location.href = 'trend_report.php?trend_id='+trend_id;
}
//-->
</script>
<table width="100%" border="0" cellpadding="5" cellspacing="4" class="bodytext">
<tr><td width="45%" valign="top">
<form name="trendForm" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<?php

echo displayCategory(0);

function displayCategory($ID,$level=0) {
	global $DRW,$DRW_read,$DRW_main,$DRW_crm;
	$savedArray = $GLOBALS['savedArray'];
	$print = '';
	$sqla = "SELECT sectorID,sectorName FROM cscan_sector WHERE parentID='$ID' ORDER BY sectorName ASC";
	$rsa = $DRW->query($sqla,$DRW_read);
	while($rowa = $DRW->fetch_array($rsa)) {
		$name = $rowa['sectorName'];
		$catid = $rowa['sectorID'];
		$innertext = '';
		if(!in_array($catid,$_SESSION['sess_sector']) && !in_array($catid,$_SESSION['sess_category']) && !in_array($catid,$_SESSION['sess_subcategory'])){
			continue;
		}
		
		$sqltwo = "SELECT trend_name,trend_link,trend_id FROM cscan_trend_report WHERE category_id=$catid ORDER BY trend_date DESC";
		$query = $DRW->query($sqltwo,$DRW_read);
		if($DRW->num_rows($query)>0){
			$innertext .= "<div style=\"float:right;\"><input type=\"checkbox\" name=\"alert[]\" value=\"$catid\"";
			if(key_exists($catid,$savedArray)) {
				$innertext .= ' checked="checked"';
			}
			$innertext .= ' /></div><div style="clear:right;border:solid 1px #000000;padding:4px;background-color:#E8E8FF;height:50px;overflow-y:scroll;">';
			//$innertext .= "<br /><select class=\"combo_box\" style=\"font-weight: normal;color:#000000;\" name=\"trend{$catid}\" size=\"1\" onchange=\"showTrend('trend{$catid}',false);\"><option value=\"\">&nbsp;</option>";
			while($row2 = $DRW->fetch_assoc($query)) {
				$trendname = $row2['trend_name'];
				$link = $row2['trend_link'];
				$trend_id = $row2['trend_id'];
				
				$innertext .= "<div class=\"bodytext\" style=\"padding:4px;border-top:dashed 1px #000000;\"><a href=\"trend_report.php?trend_id=$trend_id\" target=\"_blank\" class=\"HyperLink\">".htmlspecialchars($trendname)."</a></div>";
				//$innertext .= "<option value=\"$trend_id\">".htmlspecialchars($trendname, ENT_QUOTES)."</option>";
			}
			$innertext .= "</div>";
			/*$innertext .= "</select> &nbsp; <input type=\"checkbox\" name=\"alert[]\" value=\"$catid\"";
			if(key_exists($catid,$savedArray)) {
				$innertext .= ' checked="checked"';
			}
			$innertext .= " />";// <a href=\"#\" onclick=\"showTrend('trend{$catid}',true); return false;\" class=\"bluelink\">Download</a>*/
		}
			
		$innertext .= displayCategory($catid,($level+1));
		
		if($innertext!='') {
			$print .= "<div";
			if($level==0) {
				$print .= " style=\"text-align:left; font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 13px; font-weight: bold;text-decoration: none;margin:0px 0px 16px 0px;padding:4px;border:solid 1px #cccccc;\"";
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
			".htmlspecialchars($name)."$innertext
			</div>";
		}
	}
	return $print;
}

?>
<div align="right"><input type="submit" name="submit" value="Receive Alert" class="submitbutton" /><input type="hidden" name="send" value="1" /></div>
</form>
</td><td valign="top">
<?php 
echo '<table width="100%" border="0" cellpadding="5" cellspacing="0" class="bodytext">
<tr class="subHead"><td><strong>Trend Report Email Alerts</strong></td><td><strong>Last Sent</strong></td><td width="15%"><strong>Options</strong></td></tr>
<tr><td colspan="3" class="error">&nbsp;</td></tr>';
$className = 'white-bg';
if(count($savedArray) > 0) {
	foreach($savedArray as $sectorID=>$sectorName) {
		if ($className=='selected-bg1') {
			$className = 'white-bg';
		}
		else {
			$className = 'selected-bg1';
		}
		echo " <tr class=\"$className\"><td class=\"bodytext\" valign=\"top\">".htmlspecialchars($sectorName)."</td><td class=\"bodytext\" valign=\"top\">";
		if($trendArray[$sectorID]!=0) {
			$sqltwo = "SELECT trend_name FROM cscan_trend_report WHERE trend_id={$trendArray[$sectorID]}";
			$query = $DRW->query($sqltwo,$DRW_read);
			$row2 = $DRW->fetch_row($query);
			echo "<a href=\"trend_report.php?trend_id={$trendArray[$sectorID]}\" target=\"_blank\">".htmlspecialchars($row2[0])."</a>";
		}
		else {
			echo '&nbsp;';
		}
		echo "</td><td class=\"bodytext\" valign=\"top\"><a href=\"{$_SERVER['PHP_SELF']}?sid=$sectorID\" onclick=\"return confirm('Delete?');\"><img src = \"images/drop.png\" border=\"0\" style=\"vertical-align:bottom;\" /></a> <a class=\"HyperLink\" href=\"{$_SERVER['PHP_SELF']}?sid=$sectorID\" onclick=\"return confirm('Delete?');\">Delete</a></td></tr>";
	}
}
else{
	echo '<tr><td align="center" class="error" colspan="3">No Email Alerts Found</td></tr>';
}
echo '</table>';
?>
</td></tr>
</table>
<?php 
include 'footer_bottom.php';
?>