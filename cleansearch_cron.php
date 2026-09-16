#!/usr/bin/php
<?php
require_once("includes/ehLog_set.php");
$ehL->start(__FILE__);

require_once("includes/dbcon.php");
$DRW->databaseReadWrite_die = 1;
$hours = 2;
$weekday = date('w'); //0 (for Sunday) through 6 (for Saturday)
$hour = date('G');
$sql = "select SQL_NO_CACHE DISTINCT ID from cscan_search_product";
$result = $DRW->query($sql,$DRW_read2);
$s=0;
while( $row = $DRW->fetch_row( $result ) ){
	$sql2 = "select SQL_NO_CACHE DISTINCT COUNT(*) from cscan_search where ID=$row[0] and (lastSentDate>DATE_SUB(NOW(),INTERVAL $hours HOUR) OR queryDate>DATE_SUB(NOW(),INTERVAL $hours HOUR))";
	$result2 = $DRW->query($sql2,$DRW_read2);
	$row2 = $DRW->fetch_row( $result2 );
	if(empty($row2[0])){
		$sql = "delete from cscan_search_product where ID=$row[0]";
		$DRW->query($sql,$DRW_main);
                $s=1;
	}
}
//if($weekday==6 && $hour==22){
//if($s=='1'){
if($s!=''){    
	$sql = "delete from cscan_search_email";
	$DRW->query($sql,$DRW_main);
	$sql = "delete from cscan_search_adminproduct";
	$DRW->query($sql,$DRW_main);
}

$ehL->stop();
?>