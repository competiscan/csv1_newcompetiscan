<?php
require_once('includes/globalSession.php');
require_once('includes/checklogin.php');

if(isset($_REQUEST['companytext'])) {
	$companytext = trim($_REQUEST['companytext']);
}
else {
	$companytext = '';
}
if(isset($_REQUEST['companysel'])) {
	$companysel = $_REQUEST['companysel'];
}
else {
	$companysel = '';
}
if(isset($_REQUEST['showall'])) {
	$showall = true;
}
else {
	$showall = false;
}
if(isset($_REQUEST['parent_field'])) {
	$parent_field = $_REQUEST['parent_field'];
}
else {
	$parent_field = '';
}
if(isset($_REQUEST['cos'])) {
	$cos = $_REQUEST['cos'];
}
else {
	$cos = '';
}

$companyArray = preg_split('/"\\s+or\\s+"/i',$companysel,-1,PREG_SPLIT_NO_EMPTY);

$last = count($companyArray)-1;
if($last>-1){
	$companyArray[$last] = preg_replace('/"\\s+or\\s*$/i','',$companyArray[$last]);
	$companyArray[$last] = preg_replace('/^\\s*or\\s+"/i','',$companyArray[$last]);
}	
$lowArray = array();
array_walk($companyArray, 'removeQuote');

function removeQuote(&$item1, $key){
	$item1 = trim(str_replace('"','',$item1));
	if($item1=='') {
		unset($GLOBALS['companyArray'][$key]);
	}
	else {
		$GLOBALS['lowArray'][$key] = strtolower($item1);
	}
}

$companytext_low = strtolower($companytext);
if(in_array($companytext_low,$lowArray)) {
	$companychecked = true;
}
else {
	$companychecked = false;
}

if(!$companychecked && $companytext!='' && !in_array($companytext,$companyArray)) {
	$companyArray[] = $companytext;
	$lowArray[] = $companytext_low;
}
asort($lowArray);

$searchindex = array_search($companytext_low,$lowArray);
if($searchindex===false) {
	$searchindex = -1;
}

$where = '';
$sect_j = '';
$parent_field;
if($parent_field=='company'){
$sql = "Select DISTINCT competiscan_company from cscan_digital_company WHERE ";
//$sql = "Select DISTINCT companyName from cscan_company WHERE ";
//$sql = "Select DISTINCT companyName from cscan_digital_processed_records cdp  LEFT JOIN cscan_company cmp  on cdp.company_id=cmp.companyID WHERE ";
//$sql = "SELECT DISTINCT companyName FROM cscan_company cc JOIN cscan_company_quick_mc mc ON (cc.companyID=mc.companyID) JOIN cscan_company_quick_mp mp ON (cc.companyID=mp.companyID) JOIN cscan_company_quick_scsc scsc ON (cc.companyID=scsc.companyID) WHERE ";
	if($companytext!='') {
		$val = mysqlLike($companytext);
		$regx = '';
		if(strlen($val)>2) {
			$firstpct = '%';
			if(preg_match('/^[a-zA-Z0-9]+$/',$companytext)){
				$regx = " AND competiscan_company REGEXP '[[:<:]]$companytext'";//[[:>:]]
			}
		}
		else {
			$firstpct = '';
		}
		$sql .= "competiscan_company LIKE '$firstpct$val%'$regx";
	}
	else {
		$sql .= "competiscan_company<>''";
	}
	$sql .= "$where ORDER BY competiscan_company";
}
ob_clean();
$i = 0;
$usedArray = array();
if($companytext!='' || $showall) {
	$rs = $DRW->query($sql,$DRW_biscience_digital);
	$rows = $DRW->num_rows($rs);
}
else{
	$rs = false;
	$rows = 0;
}
if($rows>0 || (is_array($companyArray) && count($companyArray)>0)){
	if($rows>4) {
		$size = 4;
	}
	else {
		$size = $rows;
	}
	
	echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"bodytext\">";
	
	if($searchindex>=0){
		foreach($lowArray as $currkey=>$co){
			if(isset($companyArray[$currkey])){
				$val = $companyArray[$currkey];
				unset($companyArray[$currkey]);
				if($currkey==$searchindex) {
					break;
				}
				echo "<tr><td valign=\"top\"><input type=\"checkbox\" name=\"companysel[]\" value=\"".htmlspecialchars($val, ENT_QUOTES)."\" onclick=\"doCompany();\" id=\"co$i\" checked=\"checked\" /></td><td><label for=\"co$i\">$val</label></td></tr>";
				$usedArray[] = strtolower($val);
				$i++;
			}
		}
	}
	if($companytext!='' || $showall) {
		while($data = $DRW->fetch_row($rs)) {
			if(!in_array(strtolower($data[0]),$usedArray)){
				$key = array_search($data[0],$companyArray);
				if($key!==false || ($companychecked && strtolower($data[0])==$companytext_low)) {
					if(isset($companyArray[$key])) {
						unset($companyArray[$key]);
					}
					echo "<tr><td valign=\"top\"><input type=\"checkbox\" name=\"companysel[]\" value=\"".htmlspecialchars($data[0], ENT_QUOTES)."\" onclick=\"doCompany();\" id=\"co$i\" checked=\"checked\" /></td><td><label for=\"co$i\">$data[0]</label></td></tr>";
				}
				else {
					echo "<tr><td valign=\"top\"><input type=\"checkbox\" name=\"companysel[]\" value=\"".htmlspecialchars($data[0], ENT_QUOTES)."\" onclick=\"doCompany();\" id=\"co$i\" /></td><td><label for=\"co$i\">$data[0]</label></td></tr>";
				}
				$i++;
			}
		}
	}
	foreach($companyArray as $co){
		echo "<tr><td valign=\"top\"><input type=\"checkbox\" name=\"companysel[]\" value=\"".htmlspecialchars($co, ENT_QUOTES)."\" onclick=\"doCompany();\" id=\"co$i\" checked=\"checked\" /></td><td><label for=\"co$i\">$co</label></td></tr>";
		$i++;
	}
	
	if($i>4) {
		echo "<tr><td>&nbsp;</td><td>[<a href=\"#\" class=\"HyperLink\">Top</a>]</td></tr>";
	}
	echo "</table>";
}
?>