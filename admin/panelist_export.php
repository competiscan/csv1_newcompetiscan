<?php
require_once("../auth_auth.php");
require_once '../includes/functions.php';

ob_end_clean();
header("Content-Type: text/plain");//application/excel,application/vnd.ms-excel
header("Content-Disposition: attachment; filename=Competiscan_Panelists_".date('Y-m-d').".txt");
header("Pragma: no-cache");
header("Expires: 0");

echo '"SugarCRM ID","Unique ID","Panel ID","Last name","First name","Street address","City","State","Zip"'."\n";
$sql = "select sugar_id,panelist_id,competi_id,first_name,last_name,address,city,state,postalcode from cscan_panelists where active=1 AND contactTypeID=2 order by abs(LEFT(competi_id,5)),RIGHT(competi_id,5)";
$result = $DRW->query($sql,$DRW_read);
while($row = $DRW->fetch_row($result)) {
	if(!empty($row[3])){
		foreach($row as $k=>$v){
			$row[$k] = csvExcape(preg_replace('/\\r?\\n|\\r/',', ',trim($v)));
		}
		echo implode(',',$row)."\n";
	}
}

function csvExcape($in,$delim = ','){
	$out = $in;
	//if(strpos($out, $delim)!==false || strpos($out, '"')!==false || strpos($out, "\r\n")!==false || strpos($out, "\n")!==false || strpos($out, "\r")!==false || preg_match('/^0+\\d+$/',$out)>0){
		$out = '"'.str_replace('"', '""', $out).'"';
	//}
	return $out;
}
?>