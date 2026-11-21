#!/usr/bin/php
<?php
date_default_timezone_set('America/Chicago');
//include("../includes/ehLog_set.php");
//$ehL->start(__FILE__);
ini_set("memory_limit","-1");
set_time_limit(0);
ini_set('mysql.connect_timeout', 5000);
ini_set('default_socket_timeout', 5000);
require_once("../includes/dbcon.php");
$DRW->databaseReadWrite_die = 1;
require_once '../includes/functions.php';
require_once '../includes/MailVolumeCalculator.php';

$mvcalc = new MailVolumeCalculator();
$factor = 1.88;
$start_year = date('Y', strtotime('-1 years'));
$to_year = (int) date('Y');
$month_name = array('01' => "January", '02' => "February", '03' => "March", '04' => "April", '05' => "May", '06' => "June", '07' => "July", '08' => "August", '09' => "September", '10' => "October", '11' => "November", '12' => "December");
//$month_name = array('02'=>"February",'03'=>"March",'04'=>"April");
//$month_name = array('10' => "October");
$dopost = false;
$time = time();
$l = date('l');
if ($l == 'Saturday') {
    $doall = true;
    $newts = strtotime('-6 months');
    $start_year = date('Y', $newts);
    $start_month = date('n', $newts);
    $dopost = true;
} else {
    $doall = false;
    $newts = strtotime('-2 months');
    $start_year = date('Y', $newts);
    $start_month = date('n', $newts);
    $dopost = false;
    //For all 2020 records update  
    $start_year = '2020';
    $start_month = 1;
    $to_year = '2020';
//        $newts = strtotime('-5 months');
////	$start_year = date('Y',$newts);
////	$start_month = date('n',$newts);
    $dopost = false;
//        
}
/* END to added for running march month data */
for ($i = $start_year; $i <= $to_year; $i++) {
    foreach ($month_name as $key => $value) {
        if ($i == $start_year && intval($key) < $start_month) {
            continue;
        }
        $calc_date = $i . '-' . $key;
        $calc_date_range1 = $calc_date . '-01 00:00:00';
        $ctime = strtotime($calc_date_range1);
        $ctime += 2851200; //33 days
        $calc_date_range2 = date('Y-m', $ctime) . '-01 00:00:00';
        // $sql_c = "SELECT SQL_NO_CACHE COUNT(*) FROM cscan_panelists_product WHERE ppdate>='$calc_date_range1' AND ppdate<'$calc_date_range2' AND ppaddeddate>DATE_SUB(CURDATE(),INTERVAL 100 DAY)";
        $sql_c = "SELECT SQL_NO_CACHE COUNT(*) FROM cscan_panelists_product WHERE ppdate>='$calc_date_range1' AND ppdate<'$calc_date_range2' ";
        $result_c = $DRW->query($sql_c, $DRW_read2);
        $row_c = $DRW->fetch_row($result_c);
        if (empty($row_c[0])) {
            continue;
        }
        $mvcalc->doEMailEstimateVolumeForHistoricalData($i, $key, $factor); // for eve calculation
        /* End to added for running march month data */
    }
}
?>