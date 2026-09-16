#!/usr/bin/php
<?php //date_default_timezone_set('America/Chicago');
 error_reporting(E_ALL);
ini_set('display_errors', 1); 
ini_set("memory_limit","-1");
set_time_limit(0);

require_once("../includes/ehLog_set.php");
$ehL->start(__FILE__);

require_once("../includes/dbcon.php");
//$DRW->databaseReadWrite_die = 1;
require_once '../includes/functions.php';
require_once '../includes/MailVolumeCalculator_test4.php';
$time = time();
$mvcalc = new MailVolumeCalculator();
$doprint = false;
$factor = 1.88;
$H = (int)date('H',$time); 
$all='0';
echo $H ;
echo 'pppp';
$to_year = (int)date('Y');
$month_name = array('01'=>"January",'02'=>"February",'03'=>"March",'04'=>"April",'05'=>"May",'06'=>"June",'07'=>"July",'08'=>"August",'09'=>"September",'10'=>"October",'11'=>"November",'12'=>"December");
//$H=21;
//echo $H; die;
if($H=='3'){
	$mvcalc->doPreMailVolume();
	 $newts = strtotime('-4 months');
	 $doall=false; 
}else if($H=='7'){
    $newts = strtotime('-0 months');
    $doall=false;
}else if($H=='11'){
    $newts = strtotime('-3 months');
    $doall=false;
}else if($H=='15'){
    $newts = strtotime('-2 months');
    $doall=false;
}else if($H=='19'){
	$newts = strtotime('-1 months');
    $doall=false;
}else if($H=='23'){
	$newts = strtotime('-4 months');
	$start_year = 2009;
    $to_year = date('Y',$newts);
    $start_month=0;       
    $month=date('m',$newts);    
    $doall=true; 
    $all='1';
 }else{
	 $newts = strtotime('-3 months');
    $doall=false;
 }

if($H!='23'){
    $start_year = date('Y',$newts);
    $start_month = date('n',$newts); 
    $findmonth=$start_month;
    if($findmonth<10){
        $findmonth='0'.$findmonth;
    }
    $find_month_name=$month_name[$findmonth]; 
    $month_name=array($findmonth=>$find_month_name);
  }  
        	
	for($i=$start_year;$i<=$to_year;$i++){
		foreach($month_name as $key=>$value){
				//echo $key.'startmonth'.$start_month; die;
			if($i==$start_year && intval($key)<$start_month){
				continue;
			}
			if($i==$to_year && intval($key)==$month && $all=='1'){
				break;
			}
					
			$calc_date = $i.'-'.$key;
			$calc_date_range1 = $calc_date.'-01 00:00:00';
			$ctime = strtotime($calc_date_range1);
			$ctime += 2851200; //33 days
			$calc_date_range2 = date('Y-m',$ctime).'-01 00:00:00';
			echo $sql_c = "SELECT SQL_NO_CACHE COUNT(*) FROM cscan_panelists_product WHERE ppdate>='$calc_date_range1' AND ppdate<'$calc_date_range2' AND ppaddeddate>DATE_SUB(CURDATE(),INTERVAL 5 DAY)";
			 //$sql_c = "SELECT SQL_NO_CACHE COUNT(*) FROM cscan_panelists_product WHERE ppdate>='$calc_date_range1' AND ppdate<'$calc_date_range2'";
			die;
			$result_c = $DRW->query($sql_c,$DRW_read);
			$row_c = $DRW->fetch_row($result_c);
			if(empty($row_c[0])){ 
				//$ehL->write('Skip '.$calc_date);
				continue;
			}
						
			$mvcalc->doMailVolume($i,$key,$factor,$doprint,$doall);
		}
	}
if($H=='23'){
	$mvcalc->doPostMailVolume();
}
/*
if($doall || $dopost){
	$mvcalc->doPostMailVolume();
}
*/
$ehL->stop();
?>
