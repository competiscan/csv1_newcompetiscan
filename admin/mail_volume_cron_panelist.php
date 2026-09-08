#!/usr/bin/php
<?php date_default_timezone_set('America/Chicago');
ini_set("memory_limit","-1");
set_time_limit(0);
ini_set('mysql.connect_timeout', 5000);
ini_set('default_socket_timeout', 5000);

require_once("../includes/dbcon.php");
$DRW->databaseReadWrite_die = 1;
require_once '../includes/functions.php';
require_once '../includes/MailVolumeCalculator_panelist.php';

$mvcalc = new MailVolumeCalculator();
//$month_before='3';

$time = time();
$H = (int)date('H',$time);
    $dttime=date('Ymd');
    //$sql_drop_tbl="DROP TABLE IF EXISTS cscan_mv_defs$dttime";
    //$DRW->query($sql_drop_tbl, $DRW_main); 
    $sql_create_tbl="CREATE TABLE IF NOT EXISTS cscan_mv_defs$dttime AS SELECT * FROM cscan_mv_defs WHERE 1 =0";
    $DRW->query($sql_create_tbl, $DRW_main); 
    $sql_insert_query="Replace into cscan_mv_defs$dttime select * from cscan_mv_defs";
    $DRW->query($sql_insert_query, $DRW_main);
$calc_date_range1='';
$calc_date_range2='';
for($i=3;$i>0;$i--){

    $newts = strtotime('-'.$i.' months');
    $start_year  = date('Y',$newts); 
    $start_month = date('m',$newts); 

    $calc_date_range1 = $start_year.'-'.$start_month.'-01 00:00:00';

    $newts2 = strtotime('-'.($i-1).' months');
    $to_year  = date('Y',$newts2); 
    $to_month = date('m',$newts2); 

    //$to_year = (int)date('Y');
    //$to_month= date('m');
    $calc_date_range2 = $to_year.'-'.$to_month.'-01 00:00:00';
    $mvcalc->doMailVolume($calc_date_range1,$calc_date_range2,($i+1));       
//echo 'range '.$calc_date_range1.' '.$calc_date_range2;
//echo '<br><br>';
}
 $mvcalc->doPanelistAvgCount();
 $mvcalc->doPanelistShare();       


echo 'Completed';
die;
?>