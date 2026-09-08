<?php 
include('includes/globalSession.php');
include('includes/sphinx_function_email_alert.php');
//require_once('includes/functions_latest2.php');  //latest function
$ssid    =   $_REQUEST['ssid'];
$alert_dt=  $_REQUEST['dt'];

if($alert_dt=='' || $ssid==''){
    echo 'Invalid request url!'; die;
}

if(strlen($alert_dt)<8){
    $alert_dt='0'.$alert_dt;    
}
//echo $alert_dt; die;
$ssid='46947';
list($selectQuery,$saved) = doQueryEmailAlertsphinx($ssid,$alert_dt);
$usedate='';
//list($selectQuery) = doQuery_latest2(46945, false, '', false, -1, false, false, false, true, 0, array(), $row['userID']);
//list($countQuery) = doQuery_latest2($ssid,true);
echo $selectQuery; die;
$rs = $DRW->query($selectQuery,$DRW_read);
$resultCount = $DRW->num_rows($rs);
$arrExport = array();
if($resultCount > 0) {
    $arrExport['data'][] = array("Month", "Panelist ID","Invitation ID","Last 4 Digits","Zip Code","ATP","Income360");
    while($row = $DRW->fetch_row($rs)) {
        //echo "<pre>";
        //print_r($row);
        //echo "</pre>";
         $arrExport['data'][] = array($row[0], $row[1],$row[2],$row[3],$row[4],$row[5],$row[6]);
    }
    download_send_headers("email_alert_" . date("Y-m-d") .rand(). ".csv");
    echo array2csv($arrExport);
    die();
}

function array2csv(array &$array){
	if (count($array) == 0) {
	  return null;
	}
	ob_start();
	$df = fopen("php://output", 'w');
	foreach ($array['data'] as $row) {
	   fputcsv($df, $row);
	}
	fclose($df);
	return ob_get_clean();
 }
function download_send_headers($filename) {
    $now = gmdate("D, d M Y H:i:s");
    header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
    header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
    header("Last-Modified: {$now} GMT");

    // force download  
    header("Content-Type: application/force-download");
    header("Content-Type: application/octet-stream");
    header("Content-Type: application/download");

    // disposition / encoding on response body
    header("Content-Disposition: attachment;filename={$filename}");
    header("Content-Transfer-Encoding: binary");
}
exit;
?>