<?php
$ALLOW_GROUPS = array(39);
require_once("../auth_auth.php");
include 'top.php';
require_once('../includes/ehLog.php');

$q = "SELECT MAX(ymdHis) FROM cscan_admin_log_combined";
$result = $DRW->query($q,$DRW_read);
$data = $DRW->fetch_row($result);
$max_combined = $data[0];

if(isset($_REQUEST['upr'])){
	exec("/usr/bin/php productPerformance_back.php > /dev/null 2>&1 &");
	ob_end_clean();
	header("Location: {$_SERVER['PHP_SELF']}");
	exit;
}

if(isset($_REQUEST['search_text'])) {
	$reportDate = date('Y-m-d',strtotime($_REQUEST['search_text']));
	$reportDate2 = date('Y-m-d',strtotime($_REQUEST['search_text2']));
	if($reportDate>$reportDate2){
		$reportDate2 = $reportDate;
	}
}
else{
	$reportDate2 = $reportDate = date('Y-m-d');
}
if(isset($_REQUEST['csv_Submit'])){
	$csv = 1;
}
else{
	$csv = 0;
}
if(isset($_REQUEST['userID'])) {
	$userIDArray = $_REQUEST['userID'];
}
else {
	$userIDArray = array();
}
if(isset($_REQUEST['core_id'])) {
	$core_id = $_REQUEST['core_id'];
}
else {
	$core_id = '';
}
?>
<table width="100%" border="0" cellspacing="0" cellpadding="5" class="text">
<tr><td class="adminhead" align="center" colspan="3">COMPETISCAN PRODUCT PERFORMANCE</td></tr>
</table>
<script type="text/javascript" src="js_calendar/calendar.js?new=20110531"></script>
<div style="margin:10px;">
<?php
if(running_php_cmd('productPerformance_back.php')){
	echo '<em>Update In Process . . .</em>';
}
else{
	?>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" name="runner" style="display:inline;">
	<input class="button" type="submit" name="runb" value="Update Report"  />
	<input type="hidden" name="upr" value="1"  />
	</form>
	<?php 
	echo "Last Update: ".date('n/j/Y g:i A',strtotime($max_combined));
}
?>
	</div>
<form method="post" name="prodForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" style="display:inline;">
<div class="text" style="margin:6px;">
	<select class="combo_box" name="userID[]" size="5" multiple="multiple">
	<?php 
	$sql2 = "select userID,userName from cscan_admin_users WHERE user_status=1 ORDER BY userName";
	$rs2 = $DRW->query($sql2,$DRW_read);
	while($row2 = $DRW->fetch_row($rs2)){
		echo "<option value=\"$row2[0]\"";
		if(in_array($row2[0],$userIDArray)) {
			echo " selected=\"selected\"";
		}
		echo ">".htmlspecialchars($row2[1])."</option>";
	}
	?>
	</select>
</div>
<div class="text" style="margin:6px;">
	<select class="input_box" name="core_id" size="1">
	<option value="0">All</option>
	<?php 
	$ctypeArray = array();
	$ctypeArray[5] = 'Core';
	if(checkGroup(37)){
		$ctypeArray[6] = 'Non-core';
		$ctypeArray[9] = 'Telecom';
		$ctypeArray[219] = 'Travel & Leisure';
	}
	foreach($ctypeArray as $k=>$v){
		echo "<option value=\"$k\"";
		if($core_id==$k) {
			echo " selected=\"selected\"";
		}
		echo ">".htmlspecialchars($v)."</option>";
	}
	?>
	</select>
	&nbsp;
	<input type="text" name="search_text" class="input_box" size="15" readonly="readonly" value="<?php echo htmlspecialchars($reportDate,ENT_QUOTES); ?>" />
	<a href="#" onclick="displayCalendar(document.prodForm.search_text,'yyyy-mm-dd',this); return false;"><img name="popcal1" src="js_calendar/images/getcal.gif" border="0" alt="" style="vertical-align:bottom;" /></a>
	to
	<input type="text" name="search_text2" class="input_box" size="15" readonly="readonly" value="<?php echo htmlspecialchars($reportDate2,ENT_QUOTES); ?>" />
	<a href="#" onclick="displayCalendar(document.prodForm.search_text2,'yyyy-mm-dd',this); return false;"><img name="popcal2" src="js_calendar/images/getcal.gif" border="0" alt="" style="vertical-align:bottom;" /></a>
	&nbsp;
	<input class="button" style="width:60px;" type="submit" name="search_Submit" value="Show" />
	&nbsp;
	<input class="button" style="width:60px;" type="submit" name="csv_Submit" value="Get CSV" />
</div>
</form>
<div class="text"><em>Total times that a unique Product was saved or updated.</em></div>
<table width="100%" border="0" cellspacing="0" cellpadding="5" class="text">
<tr class="head1">
<td class="adminhead" valign="bottom"><strong>Username</strong></td>
<td class="adminhead"><strong>Approved<br />Saves</strong></td>
<td class="adminhead"><strong>Problem<br />Saves</strong></td>
<td class="adminhead"><strong>Reprocessed<br />Saves</strong></td>
<td class="adminhead"><strong>Unapproved<br />Saves</strong></td>
<td class="adminhead"><strong>Temp Product<br />Saves</strong></td>
<td class="adminhead"><strong>Deleted<br />Products</strong></td>
</tr>
<?php
if($csv==1){
	ob_clean();
	header('Content-Type: text/plain');
	header("Content-Disposition: attachment; filename=\"CompetiscanProductPerformance".date('YmdHis').".csv\"");
	echo "Username,Approved,Problem,Reprocessed,Unapproved,Temp Product,Deleted\r\n";
}
if(count($userIDArray)>0){
	$utext = ' AND userID IN ('.implode(',',$userIDArray).')';
}
else{
	$utext = '';
}
$where = '';
$coreArray = array();
$noncoreArray = array();
$sql = "SELECT sectorID,sectorName,is_core FROM cscan_sector WHERE sectorSearchActive=1 AND parentID=0 ORDER BY sectorName";
$rs = $DRW->query($sql,$DRW_read);
while($row = $DRW->fetch_array($rs)) {
	if($row[2]){
		$coreArray[] = $row[0];
	}
	else{
		$noncoreArray[] = $row[0];
	}
}
if($core_id==219){
	$where .= ' AND sectorID=219';
}
elseif($core_id==9){
	$where .= ' AND sectorID=9';
}
elseif($core_id==6){
	$temp = array();
	foreach($noncoreArray as $id){
		if($id!=9 && $id!=219){
			$temp[] = 'sectorID='.$id;
		}
	}
	if(count($temp)>0){
		$where .= ' AND ('.implode(' OR ',$temp).')';
	}
}
elseif($core_id==5){
	$temp = array();
	foreach($coreArray as $id){
		$temp[] = 'sectorID='.$id;
	}
	if(count($temp)>0){
		$where .= ' AND ('.implode(' OR ',$temp).')';
	}
}
$className='';
$qU = "SELECT userID,userName FROM cscan_admin_users WHERE user_status=1$utext ORDER BY userName";
$resultU = $DRW->query($qU,$DRW_read);
while($dataU = $DRW->fetch_row($resultU)){
	$userID = $dataU[0];
	$userName = $dataU[1];
	$q = "SELECT SUM(approved),SUM(problem),SUM(reprocessed),SUM(unapproved),SUM(temp_product),SUM(deleted),SUM(first_approved) FROM cscan_admin_log_combined WHERE userID=$userID AND ymd>='".$DRW->real_escape_string($reportDate)." 00:00:00' AND ymd<='".$DRW->real_escape_string($reportDate2)." 23:59:59'$where";
	$result = $DRW->query($q,$DRW_read);
	$data = $DRW->fetch_row($result);
	$approved = (int)$data[0];
	$problem = (int)$data[1];
	$reprocessed = (int)$data[2];
	$unapproved = (int)$data[3];
	$temp_product = (int)$data[4];
	$deleted = (int)$data[5];
	$first_approved = (int)$data[6];
	
	$u = array($userName,$approved.' ('.$first_approved.')',$problem,$reprocessed,$unapproved,$temp_product,$deleted);
	
	if($csv==1){
		echo implode(',',$u)."\r\n";
	}
	else{
		if($className=='selected-bg') {
			$className='white-bg';
		}
		else {
			$className='selected-bg';
		}
		echo '<tr class="'.$className.'"><td>'.implode('</td><td>',$u).'</td></tr>';
	}
}
if($csv==1){
	exit;
}
echo '</table>';

function csvExcape($in,$delim = ','){
	$out = $in;
	if(strpos($out, $delim)!==false || strpos($out, '"')!==false || strpos($out, "\r\n")!==false || strpos($out, "\n")!==false || strpos($out, "\r")!==false || preg_match('/^0+\\d+$/',$out)>0){
		$out = '"'.str_replace('"', '""', $out).'"';
	}
	return $out;
}

include 'bottom.php';
?>
