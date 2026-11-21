<?php
require_once('includes/globalSession.php');
require_once('includes/checklogin.php');
require_once('includes/rpv-dashboard-function.php');
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
if($parent_field=='domainName'){
ob_clean();
$i = 0;
$usedArray = array();
if($companytext!='' || $showall) {
	$DOMAINAPILINK=SENDER_DOMAIN_AUTO_UAT.'domain';
	$postdata = ['query' => $companytext];
	if($showall || $companytext!=''){
		$posteddata = json_encode($postdata);
		$get_data = callAPI('POST', $DOMAINAPILINK, $posteddata);
	}
	
	$response = json_decode($get_data, true);
	$rows=count($response['payload']['suggestions']); 
	//echo "<pre>";
	//print_r($companyArray);die;
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
		foreach($response['payload']['suggestions'] as $allCompanyData){
			if(!in_array(strtolower($allCompanyData),$usedArray)){
				$key = array_search($allCompanyData,$companyArray);
				if($key!==false || ($companychecked && strtolower($allCompanyData)==$companytext_low)) {
					if(isset($companyArray[$key])) {
						unset($companyArray[$key]);
					}
					echo "<tr><td valign=\"top\"><input type=\"checkbox\" name=\"companysel[]\" value=\"".htmlspecialchars($allCompanyData, ENT_QUOTES)."\" onclick=\"doCompany();\" id=\"co$i\" checked=\"checked\" /></td><td><label for=\"co$i\">$allCompanyData</label></td></tr>";
				}
				else {
					echo "<tr><td valign=\"top\"><input type=\"checkbox\" name=\"companysel[]\" value=\"".htmlspecialchars($allCompanyData, ENT_QUOTES)."\" onclick=\"doCompany();\" id=\"co$i\" /></td><td><label for=\"co$i\">$allCompanyData</label></td></tr>";
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
}
?>