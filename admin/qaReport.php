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
<script type="text/javascript" src="js_calendar/calendar.js"></script>
<form method="post" name="prodForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" style="display:inline;">
<div class="text" style="margin:6px;">
	<select class="combo_box" name="userID[]" size="5" multiple="multiple">
	<?php 
	$sql2 = "select userID,userName from cscan_admin_users WHERE user_status=1 AND is_qareport=1 ORDER BY userName";
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
<div class="text"><em>Total times that a user was associated with a Product (or Temp Product) marked as QA Link.</em></div>
<table border="0" cellspacing="0" cellpadding="5" class="text">
<tr class="head1">
<td class="adminhead"><strong>Username</strong></td>
<td class="adminhead"><strong>Products</strong></td>
<td class="adminhead">&nbsp;</td>
<td class="adminhead"><strong>Temp Products</strong></td>
<td class="adminhead">&nbsp;</td>
</tr>
<?php
if($csv==1){
	ob_clean();
	header('Content-Type: text/plain');
	header("Content-Disposition: attachment; filename=\"CompetiscanQALink".date('YmdHis').".csv\"");
	echo "Username,Products,,Temp Products,\r\n";
}
if(count($userIDArray)>0){
	$utext = ' AND userID IN ('.implode(',',$userIDArray).')';
}
else{
	$utext = '';
}
$where = '';
$wheret = '';
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
	$where .= ' AND ((scsc_sectorID=0 AND (DMSource LIKE \'%\\_TL\\_%\')) OR scsc_sectorID=219)';
	$wheret .= ' AND (sectorID LIKE \'%219%\' AND sectorID REGEXP \'[[:<:]]219[[:>:]]\')';
}
elseif($core_id==9){
	$where .= ' AND ((scsc_sectorID=0 AND (DMSource LIKE \'%telecom%\' OR DMSource LIKE \'%\\_TC\\_%\')) OR scsc_sectorID=9)';
	$wheret .= ' AND (sectorID LIKE \'%9%\' AND sectorID REGEXP \'[[:<:]]9[[:>:]]\')';
}
elseif($core_id==6){
	$temp = array();
	$where .= ' AND ((scsc_sectorID=0 AND (DMSource LIKE \'%non%\' OR DMSource LIKE \'%\\_NC\\_%\'))';
	foreach($noncoreArray as $id){
		if($id!=9 && $id!=219){
			$where .= ' OR scsc_sectorID='.$id;
			$temp[] = '(sectorID LIKE \'%'.$id.'%\' AND sectorID REGEXP \'[[:<:]]'.$id.'[[:>:]]\')';
		}
	}
	$where .= ')';
	if(count($temp)>0){
		$wheret .= ' AND ('.implode(' OR ',$temp).')';
	}
}
elseif($core_id==5){
	$temp = array();
	$where .= ' AND ((scsc_sectorID=0 AND DMSource NOT LIKE \'%non%\' AND DMSource NOT LIKE \'%\\_NC\\_%\' AND DMSource NOT LIKE \'%telecom%\' AND DMSource NOT LIKE \'%\\_TC\\_%\' AND DMSource NOT LIKE \'%\\_TL\\_%\')';
	foreach($coreArray as $id){
		$where .= ' OR scsc_sectorID='.$id;
		$temp[] = '(sectorID LIKE \'%'.$id.'%\' AND sectorID REGEXP \'[[:<:]]'.$id.'[[:>:]]\')';
	}
	$where .= ')';
	if(count($temp)>0){
		$wheret .= ' AND ('.implode(' OR ',$temp).')';
	}
}
$className='';
$qU = "SELECT userID,userName FROM cscan_admin_users WHERE user_status=1 AND is_qareport=1$utext ORDER BY userName";
$resultU = $DRW->query($qU,$DRW_read);
while($dataU = $DRW->fetch_row($resultU)){
	$userID = $dataU[0];
	$userName = $dataU[1];
	$u = array($userName);
	foreach(array('<>','=') as $eq){
		if($eq=='<>'){
			$sect_j = ' JOIN cscan_scsc_product as scsc ON (al.productID=scsc.productID) JOIN cscan_product_detail pd ON (al.productID=pd.productID)';
			$wherel = $where;
		}
		else{
			$sect_j = ' JOIN cscan_product_email pd ON (al.muid=pd.muid AND al.isTmp=pd.isTmp)';
			$wherel = $wheret;
		}
		$q = "SELECT COUNT(DISTINCT al.productID,al.muid,al.isTmp) FROM cscan_admin_log al$sect_j WHERE userID=$userID AND al.productID{$eq}0 AND qareport>='".$DRW->real_escape_string($reportDate)." 00:00:00' AND qareport<='".$DRW->real_escape_string($reportDate2)." 23:59:59'$wherel";
		$result = $DRW->query($q,$DRW_read);
		$data = $DRW->fetch_row($result);
		$qa_count = $data[0];
		
		$u[] = $qa_count;
		if($reportDate2==$reportDate){
			$txt = '';
			$q = "SELECT DISTINCT al.productID,al.muid,al.isTmp FROM cscan_admin_log al$sect_j WHERE userID=$userID AND al.productID{$eq}0 AND qareport>='".$DRW->real_escape_string($reportDate)." 00:00:00' AND qareport<='".$DRW->real_escape_string($reportDate2)." 23:59:59'$wherel ORDER BY qareport ASC";
			$result = $DRW->query($q,$DRW_read);
			while($data = $DRW->fetch_row($result)){
				$productID = $data[0];
				$muid = $data[1];
				$isTmp = $data[2];
				if(!empty($productID)){
					$idtxt = 'N/A';
					$q2 = "SELECT entryID FROM cscan_product_detail WHERE productID=$productID";
					$result2 = $DRW->query($q2,$DRW_read);
					$data2 = $DRW->fetch_row($result2);
					if(!empty($data2[0])){
						$idtxt = $data2[0];
					}
					if($csv==1){
						$txt .= $idtxt.'; ';
					}
					else{
						if(!empty($txt)){
							$txt .= '<br />';
						}
						$txt .= '<a href="addproduct.php?id='.$productID.'" target="_blank">'.$idtxt.'</a>';
					}
				}
				else{
					$idtxt = $muid;
					if($isTmp){
						$idtxt .= 'tmp';
						$muid .= '&amp;isTmp=1';
					}
					if($csv==1){
						$txt .= $idtxt.'; ';
					}
					else{
						if(!empty($txt)){
							$txt .= '<br />';
						}
						$q2 = "SELECT history_year FROM cscan_product_email WHERE muid=$muid AND isTmp=$isTmp";
						$result2 = $DRW->query($q2,$DRW_read);
						$data2 = $DRW->fetch_row($result2);
						$hy = $data2[0];
						$txt .= '<a href="../temp_product.php?muid='.$muid.'&amp;hy='.$hy.'" target="_blank">'.$idtxt.'</a>';
					}
				}
			}
			$u[] = $txt;
		}
		else{
			if($csv==1){
				$u[] = '';
			}
			else{
				$u[] = '&nbsp;';
			}
		}
	}
	
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
		echo '<tr class="'.$className.'"><td valign="top">'.implode('</td><td valign="top">',$u).'</td></tr>';
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