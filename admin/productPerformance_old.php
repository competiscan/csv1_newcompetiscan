<?php
$ALLOW_GROUPS = array(39);
require_once("../auth_auth.php");
include 'top.php';

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
if(isset($_REQUEST['core_id'])) {
	$core_id = $_REQUEST['core_id'];//$_SESSION['pp_core_id'] = 
}
/*elseif(isset($_SESSION['pp_core_id'])) {
	$core_id = $_SESSION['pp_core_id'];
}*/
else {
	$core_id = '';
}
?>
<table width="100%" border="0" cellspacing="0" cellpadding="5" class="text">
<tr><td class="adminhead" align="center" colspan="3">COMPETISCAN PRODUCT PERFORMANCE</td></tr>
</table>
<script type="text/javascript" src="js_calendar/calendar.js?new=200812"></script>
<form method="post" name="prodForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" style="display:inline;">
<table border="0" cellspacing="0" cellpadding="4" class="text">
	<tr>
	<td><select class="input_box" name="core_id" size="1"><option value="0">All</option>
	<option value="C"<?php if($core_id=='C') echo ' selected="selected"'; ?>>Core</option>
	<option value="N"<?php if($core_id=='N') echo ' selected="selected"'; ?>>Non-Core</option>
	</select></td>
	<td><strong>Date:</strong></td>
	<td><input type="text" name="search_text" class="input_box" size="15" readonly="readonly" value="<?php echo htmlspecialchars($reportDate,ENT_QUOTES); ?>" />
	<a href="#" onclick="displayCalendar(document.prodForm.search_text,'yyyy-mm-dd',this); return false;"><img name="popcal1" src="js_calendar/images/getcal.gif" border="0" alt="" style="vertical-align:bottom;" /></a>
	-
	<input type="text" name="search_text2" class="input_box" size="15" readonly="readonly" value="<?php echo htmlspecialchars($reportDate2,ENT_QUOTES); ?>" />
	<a href="#" onclick="displayCalendar(document.prodForm.search_text2,'yyyy-mm-dd',this); return false;"><img name="popcal2" src="js_calendar/images/getcal.gif" border="0" alt="" style="vertical-align:bottom;" /></a></td>
	<td>&nbsp;</td>
	<td><input class="button" style="width:60px;" type="submit" name="search_Submit" value="Update" /></td>
	<td>&nbsp;</td>
	<td><input class="button" style="width:60px;" type="submit" name="csv_Submit" value="Get CSV" /></td>
	</tr>
</table>
</form>
<div class="text"><em>Total times that a unique Product was saved or updated. Parentheses show counts for status change.</em></div>
<table width="100%" border="0" cellspacing="0" cellpadding="5" class="text">
<tr class="head1">
<td class="adminhead"><strong>Username</strong></td>
<td class="adminhead"><strong>Approved</strong></td>
<td class="adminhead"><strong>Problem</strong></td>
<td class="adminhead"><strong>Reprocessed</strong></td>
<td class="adminhead"><strong>Unapproved</strong></td>
<td class="adminhead"><strong>Temp Product</strong></td>
<td class="adminhead"><strong>Deleted</strong></td>
</tr>
<?php
if($csv==1){
	ob_clean();
	header('Content-Type: text/plain');
	header("Content-Disposition: attachment; filename=\"CompetiscanProductPerformance".date('YmdHis').".csv\"");
	echo "Username,Approved,Problem,Reprocessed,Unapproved,Temp Product,Deleted\r\n";
}

$where_core = '';
$where_coreArray = array();
$sql = "SELECT sectorID FROM cscan_sector WHERE sectorSearchActive=1 AND parentID=0 AND is_core=1";
$rs = $DRW->query($sql,$DRW_read);
while($row = $DRW->fetch_array($rs)) {
	$id = $row[0];
	if($core_id=='N'){
		$where_coreArray[] = "pd.sectorID NOT regexp '[[:<:]]{$id}[[:>:]]'";
	}
	elseif($core_id=='C'){
		$where_coreArray[] = "(pd.sectorID like '%{$id}%' AND pd.sectorID regexp '[[:<:]]{$id}[[:>:]]')";
	}
}
if(count($where_coreArray)>0){
	if($core_id=='N'){
		$where_coreArray[] = 'pd.sectorID<>\'0\'';
		$where_coreArray[] = 'pd.sectorID<>\'\'';
		$where_core = ' AND (';
		$where_core .= '((pd.sectorID=\'0\' OR pd.sectorID=\'\') AND DMSource LIKE \'%non%\') OR ';
		$where_core .= '('.implode(' AND ',$where_coreArray).'))';
	}
	elseif($core_id=='C'){
		$where_core = ' AND (';
		$where_core .= '((pd.sectorID=\'0\' OR pd.sectorID=\'\') AND DMSource NOT LIKE \'%non%\') OR ';
		$where_core .= '('.implode(' OR ',$where_coreArray).'))';
	}
}
$className='';
$typeArray = array(1=>'Approved',4=>'Problem',3=>'Reprocessed',2=>'Unapproved');
$qU = "SELECT userID,userName FROM cscan_admin_users WHERE user_status=1 ORDER BY userName";
$resultU = $DRW->query($qU,$DRW_read);
while($dataU = $DRW->fetch_row($resultU)){
	$userID = $dataU[0];
	$userName = $dataU[1];
	$u = array($userName);
	/*
	// AND logDate>='".$DRW->real_escape_string($reportDate)." 00:00:00' AND logDate<='".$DRW->real_escape_string($reportDate2)." 23:59:59'
	$q = "SELECT COUNT(DISTINCT pd.productID)
		FROM cscan_product_detail pd, cscan_admin_log al
		WHERE pd.productStatus=1 AND approved_date>='".$DRW->real_escape_string($reportDate)." 00:00:00' AND approved_date<='".$DRW->real_escape_string($reportDate2)." 23:59:59'
		AND pd.productID=al.productID AND al.userID=$userID AND logDate>=approved_date $where_core";
	$result = $DRW->query($q,$DRW_read);
	$data = $DRW->fetch_row($result);
	$u[] = $data[0];
	*/
	foreach($typeArray as $t=>$tname){
		// AND actual_addedToDatabase>='".$DRW->real_escape_string($reportDate)." 00:00:00' AND actual_addedToDatabase<='".$DRW->real_escape_string($reportDate2)." 23:59:59'
		$q = "SELECT COUNT(DISTINCT pd.productID)
			FROM cscan_product_detail pd, cscan_admin_log al
			WHERE pd.productStatus=$t
			AND al.muid=0 AND al.isTmp=0 AND pd.productID=al.productID AND al.userID=$userID AND logDate>='".$DRW->real_escape_string($reportDate)." 00:00:00' AND logDate<='".$DRW->real_escape_string($reportDate2)." 23:59:59'$where_core";
		$result = $DRW->query($q,$DRW_read);
		$data = $DRW->fetch_row($result);
		$tmp = $data[0];
		
		$q = "SELECT COUNT(DISTINCT pd.productID)
			FROM cscan_product_detail pd, cscan_admin_log al
			WHERE pd.productStatus=$t AND pd.productStatus=al.productStatus
			AND al.muid=0 AND al.isTmp=0 AND pd.productID=al.productID AND al.userID=$userID AND logDate>='".$DRW->real_escape_string($reportDate)." 00:00:00' AND logDate<='".$DRW->real_escape_string($reportDate2)." 23:59:59'$where_core";
		$result = $DRW->query($q,$DRW_read);
		$data = $DRW->fetch_row($result);
		if($data[0]>0){
			$u[] = "$tmp ($data[0])";
		}
		else{
			$u[] = $tmp;
		}
	}
	
	$q = "SELECT COUNT(DISTINCT pd.muid,pd.isTmp)
		FROM cscan_product_email pd, cscan_admin_log al
		WHERE al.productID=0 AND pd.muid=al.muid AND pd.isTmp=al.isTmp AND al.userID=$userID AND logDate>='".$DRW->real_escape_string($reportDate)." 00:00:00' AND logDate<='".$DRW->real_escape_string($reportDate2)." 23:59:59'$where_core";
	$result = $DRW->query($q,$DRW_read);
	$data = $DRW->fetch_row($result);
	$u[] = $data[0];
	
	$q = "SELECT COUNT(DISTINCT al.productID)
		FROM cscan_admin_log al LEFT JOIN cscan_product_detail pd ON(al.productID=pd.productID)
		WHERE muid=0 AND isTmp=0 AND al.userID=$userID AND logDate>='".$DRW->real_escape_string($reportDate)." 00:00:00' AND logDate<='".$DRW->real_escape_string($reportDate2)." 23:59:59' AND pd.productID IS NULL";
	$result = $DRW->query($q,$DRW_read);
	$data = $DRW->fetch_row($result);
	$tmp = $data[0];
	$q = "SELECT COUNT(DISTINCT al.muid,al.isTmp)
		FROM cscan_admin_log al LEFT JOIN cscan_product_email pd ON(pd.muid=al.muid AND pd.isTmp=al.isTmp)
		WHERE al.productID=0 AND al.userID=$userID AND logDate>='".$DRW->real_escape_string($reportDate)." 00:00:00' AND logDate<='".$DRW->real_escape_string($reportDate2)." 23:59:59' AND pd.muid IS NULL";
	$result = $DRW->query($q,$DRW_read);
	$data = $DRW->fetch_row($result);
	$u[] = $data[0] + $tmp;
	
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
		echo '<tr class="'.$className.'"><td>'.$u[0].'</td><td>'.$u[1].'</td><td>'.$u[2].'</td><td>'.$u[3].'</td><td>'.$u[4].'</td><td>'.$u[5].'</td><td>'.$u[6].'</td></tr>';
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
