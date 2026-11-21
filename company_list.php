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

$mChannelID = implode(',',$_SESSION['sess_mchannel']);

$mPanelID = implode(',',$_SESSION['sess_mpanel']);

$sectorID = implode(',',$_SESSION['sess_sector']);

$categoryID = implode(',',$_SESSION['sess_category']);
if($categoryID!=''){
	$categoryID .= ',';
}
$categoryID .= '0';

$subCategoryID = implode(',',$_SESSION['sess_subcategory']);
if($subCategoryID!=''){
	$subCategoryID .= ',';
}
$subCategoryID .= '0';

if($parent_field!='productName' AND $parent_field!='publication_name' AND $parent_field!='company'){
	$multArray = array('mc_ID'=>$mChannelID,'mp_ID'=>$mPanelID,'scsc_ID'=>$sectorID);
	foreach($multArray as $field=>$val){
		if($val!=''){
			$tmpArray = explode(',',$val);
			$where .= " AND (";
			foreach($tmpArray as $v){
				if($v!='') {
					$where .= " $field=$v OR ";
				}
			}
			$where = substr($where,0,-4);
			$where .= ")";
		}
	}
}

if($parent_field=='productName'){
	$sql = "SELECT DISTINCT productName FROM cscan_company_productname pn";
	if($cos!=''){
		$sql .= ',cscan_company co';
	}
	$sql .= ' WHERE ';
	if($companytext!='') {
		$val = mysqlLike($companytext);
		if(strlen($val)>2) {
			$firstpct = '%';
		}
		else {
			$firstpct = '';
		}
		$sql .= "productName LIKE '$firstpct$val%'";
	}
	else {
		$sql .= "productName<>''";
	}
	if($cos!=''){
		$sql .= " AND pn.companyID=co.companyID AND ".doMultCompany($cos,true,'company');
	}
	$sql .= " ORDER BY productName";
}
elseif($parent_field=='affinity_association'){
	$sql = "SELECT DISTINCT affinityName FROM cscan_affinity cc JOIN cscan_affinity_quick_mc mc ON (cc.affinityID=mc.affinityID) JOIN cscan_affinity_quick_mp mp ON (cc.affinityID=mp.affinityID) JOIN cscan_affinity_quick_scsc scsc ON (cc.affinityID=scsc.affinityID) WHERE ";
	if($companytext!='') {
		$val = mysqlLike($companytext);
		$regx = '';
		if(strlen($val)>2) {
			$firstpct = '%';
			if(preg_match('/^[a-zA-Z0-9]+$/',$companytext)){
				$regx = " AND affinityName REGEXP '[[:<:]]$companytext'";//[[:>:]]
			}
		}
		else {
			$firstpct = '';
		}
		$sql .= "affinityName LIKE '$firstpct$val%'$regx";
	}
	else {
		$sql .= "affinityName<>''";
	}
	$sql .= "$where ORDER BY affinityName";
}
elseif($parent_field=='publication_name'){
	$sql = "SELECT DISTINCT publicationName FROM cscan_publication WHERE ";
	if($companytext!='') {
		$val = mysqlLike($companytext);
		$regx = '';
		if(strlen($val)>2) {
			$firstpct = '%';
			if(preg_match('/^[a-zA-Z0-9]+$/',$companytext)){
				$regx = " AND publicationName REGEXP '[[:<:]]$companytext'";//[[:>:]]
			}
		}
		else {
			$firstpct = '';
		}
		$sql .= "publicationName LIKE '$firstpct$val%'$regx";
	}
	else {
		$sql .= "publicationName<>''";
	}
	$sql .= "$where ORDER BY publicationName";
}else{
	$sql = "SELECT DISTINCT companyName FROM cscan_company  WHERE ";
	//$sql = "SELECT DISTINCT companyName FROM cscan_company cc JOIN cscan_company_quick_mc mc ON (cc.companyID=mc.companyID) JOIN cscan_company_quick_mp mp ON (cc.companyID=mp.companyID) JOIN cscan_company_quick_scsc scsc ON (cc.companyID=scsc.companyID) WHERE ";
	if($companytext!='') {
		$val = mysqlLike($companytext);
		$regx = '';
		if(strlen($val)>2) {
			$firstpct = '%';
			if(preg_match('/^[a-zA-Z0-9]+$/',$companytext)){
				$regx = " AND companyName REGEXP '[[:<:]]$companytext'";//[[:>:]]
			}
		}
		else {
			$firstpct = '';
		}
		$sql .= "companyName LIKE '$firstpct$val%'$regx";
	}
	else {
		$sql .= "companyName<>''";
	}
	$sql .= "$where ORDER BY companyName";
}
ob_clean();
$i = 0;
$usedArray = array();
if($companytext!='' || $showall) {
    
	$rs = $DRW->query($sql,$DRW_read);
	$rows = $DRW->num_rows($rs);
}
else{
	$rs = false;
	$rows = 0;
}
if($rows>0 || count($companyArray)>0){
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