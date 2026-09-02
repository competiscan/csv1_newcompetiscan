#!/usr/bin/php
<?php date_default_timezone_set('America/Chicago');
//include("../includes/ehLog_set.php");
//$ehL->start(__FILE__);
ini_set("memory_limit","-1");
set_time_limit(0);
ini_set('mysql.connect_timeout', 500);
ini_set('default_socket_timeout', 500);

require_once("../includes/dbcon.php");
$DRW->databaseReadWrite_die = 1;
require_once '../includes/functions.php';
require_once '../includes/MailVolumeCalculator_check.php';

$mvcalc = new MailVolumeCalculator();

$factor = 1.88;

//$result = $DRW->query("SELECT MIN(addedToDatabase) FROM cscan_product_detail WHERE productStatus=1 AND addedToDatabase>'1900-01-01 00:00:00'",$DRW_read2);
//$data = $DRW->fetch_row($result);
//if($data[0]!='') {
//	$start_year = (int)substr($data[0],0,4);
//}
//else {
	//$start_year = 2015;
//}        
 $start_year= date('Y', strtotime('-2 years'));  
 //exit;
        
$to_year = (int)date('Y');
$month_name = array('01'=>"January",'02'=>"February",'03'=>"March",'04'=>"April",'05'=>"May",'06'=>"June",'07'=>"July",'08'=>"August",'09'=>"September",'10'=>"October",'11'=>"November",'12'=>"December");
//$month_name = array('02'=>"February",'03'=>"March",'04'=>"April");
$dopost = false;
$time = time();
$H = (int)date('H',$time);
/*
if($H==11){ //3,7,11,15,19
	$doall = true;
	$start_month = 0;
        $start_year= date('Y', strtotime('-1 years'));  
        
}else if($H==19){ //3,7,11,15,19
	$doall = true;
	$start_month = 0;
        $start_year= date('Y', strtotime('-2 years')); 
        $to_year= (int)date('Y', strtotime('-1 years'));  
}
else{
	$doall = false;
	$newts = strtotime('-6 months');
	$start_year = date('Y',$newts);
	$start_month = date('n',$newts);
}
if($H==19){
	$dopost = true;
}
*/


$l=date('l');
if($l=='Saturday'){
	$doall = true;
	$newts = strtotime('-6 months');
	$start_year = date('Y',$newts);
	$start_month = date('n',$newts);
        $dopost = true;
}
else{
	$doall = false;
	$newts = strtotime('-2 months');
	$start_year = date('Y',$newts);
	$start_month = date('n',$newts);
        $dopost = false;
}

if($doall){
     echo 'pre mail volume start';
	$mvcalc->doPreMailVolume();
     echo 'End pre mail volume';   
}



/* added for running march month data */

//$start_year= (int)date('Y');        
//$to_year = (int)date('Y');
//$newts = strtotime('-2 months');
//$start_month = date('n',$newts);
//$month_name = array('02'=>"February");

/* END to added for running march month data */

for($i=$start_year;$i<=$to_year;$i++){
	foreach($month_name as $key=>$value){
		if($i==$start_year && intval($key)<$start_month){
			continue;
		}
		
		$calc_date = $i.'-'.$key;
		$calc_date_range1 = $calc_date.'-01 00:00:00';
		$ctime = strtotime($calc_date_range1);
		$ctime += 2851200; //33 days
		$calc_date_range2 = date('Y-m',$ctime).'-01 00:00:00';
                $sql_c = "SELECT SQL_NO_CACHE COUNT(*) FROM cscan_panelists_product WHERE ppdate>='$calc_date_range1' AND ppdate<'$calc_date_range2' AND ppaddeddate>DATE_SUB(CURDATE(),INTERVAL 5 DAY)";
                //$sql_c = "SELECT SQL_NO_CACHE COUNT(*) FROM cscan_panelists_product WHERE ppdate>='$calc_date_range1' AND ppdate<'$calc_date_range2' ";
		$result_c = $DRW->query($sql_c,$DRW_read2);
		$row_c = $DRW->fetch_row($result_c);
		if(empty($row_c[0])){
			//$ehL->write('Skip '.$calc_date);		
                    continue;
		}
		
		$mvcalc->doMailVolume($i,$key,$factor);
                
                /* added for running march month data */
                //die;
                /* End to added for running march month data */
	}
    /* added for running march month data */
       //die;
   /* End to added for running march month data */
}
if($doall || $dopost){
        echo  ' post mail volume start ';
        $mvcalc->doPostMailVolume();
        echo ' End post mail volume ';
}

//$ehL->stop();
?>