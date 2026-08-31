#!/usr/bin/php
<?php
$time = time();

include_once 'includes/functions.php';
require_once("includes/dbcon.php");
$DRW->databaseReadWrite_die = 1;

$time_back = mktime(0,0,0,(int)date('n')-6,(int)date('j'),(int)date('Y'));
$time_end = mktime(0,0,0,(int)date('n')-3,(int)date('j'),(int)date('Y'));
$cy = (int)date('Y',$time_back);
$ymd = date('Y-m-d',$time_back);
ini_set("memory_limit","-1");
set_time_limit(0);
$limit = ' LIMIT 1500000';
//cscan_admin_log
$emailsArray = array('cscan_email_file','cscan_email_save','cscan_email_text');
$tempsArray = array('cscan_email_attach_file','cscan_email_forward');
$prodArray = array('cscan_product_email','cscan_banking_temp','cscan_credit_access_checks_temp','cscan_mortgage_loan_temp','cscan_payment_cards_temp','cscan_telecom_temp','cscan_travel_leisure_temp','cscan_retail_temp','cscan_energy_temp');
for($i=2016;$i<=$cy;$i++){
	$startdate1 = "$i-01-01 00:00:00";
	$enddate1 = "$i-06-30 23:59:59";
	$startdate2 = "$i-07-01 00:00:00";
	$enddate2 = "$i-12-31 23:59:59";
	$moreArray = array();
	if($i>=2013){
		$i1 = intval($i.'01');
		$moreArray[$i1] = array($startdate1,$enddate1);
		$i2 = intval($i.'07');
		$moreArray[$i2] = array($startdate2,$enddate2);
	}
	else{
		$moreArray[$i] = array($startdate1,$enddate2);
	}
	foreach($moreArray as $yp=>$a){
		list($startdate,$enddate) = $a;
		
		$DRW->databaseReadWrite_die = 0;
		$savedQ = "SHOW COLUMNS FROM cscan_email$yp";
		$rs = $DRW->query($savedQ);
		$DRW->databaseReadWrite_die = 1;
		if(mysqli_errno($DRW->current_dbh)>0){
			$savedQ = "CREATE TABLE cscan_email$yp LIKE cscan_email";
			$rs = $DRW->query($savedQ);
		}
		foreach(array(0=>$emailsArray,1=>$tempsArray) as $k=>$a){
			foreach($a as $t){
				$DRW->databaseReadWrite_die = 0;
				$savedQ2 = "SHOW COLUMNS FROM $t$yp";
				$rs2 = $DRW->query($savedQ2);
				$DRW->databaseReadWrite_die = 1;
				if(mysqli_errno($DRW->current_dbh)>0){
					$savedQ2 = "CREATE TABLE $t$yp LIKE $t";
					$rs2 = $DRW->query($savedQ2);
				}
			}
		}
		
		$enddate_time = strtotime($enddate);
//                echo $i.'====='.$cy.' && '.strlen($yp).' enfdtime '.$enddate_time.'time end>'.$time_end;
//		if($i==$cy && strlen($yp)>1 && $enddate_time>$time_end) {
//			echo "skip latest: $yp\n";
//			continue;
//		}
		
		//$savedQ = "SELECT * FROM cscan_email WHERE email_date>='$startdate' AND email_date<='$enddate'$limit";
		$savedQ = "SELECT * FROM cscan_email WHERE email_date>='$startdate' AND isdelete=0 AND email_date<='$enddate'$limit";
		
                $rs = $DRW->query($savedQ);
		while($data = $DRW->fetch_row($rs)){
			$muid = $data[0];
			$vals = array();
			foreach($data as $v){
				$vals[] = "'".$DRW->real_escape_string($v)."'";
			}
			$DRW->query("REPLACE INTO cscan_email$yp VALUES (".implode(',',$vals).")");
			//$DRW->query("DELETE FROM cscan_email WHERE muid=$muid");
                        $DRW->query("update cscan_email set isdelete=1 WHERE muid=$muid");
			
			foreach(array(0=>$emailsArray,1=>$tempsArray) as $k=>$a){
				if($k==1){
					$w = ' AND isTmp=0';
				}
				else{
					$w = '';
				}
				foreach($a as $t){
					$savedQ2 = "SELECT * FROM $t WHERE muid=$muid$w";
					$rs2 = $DRW->query($savedQ2);
					while($data2 = $DRW->fetch_row($rs2)){
						$vals = array();
						foreach($data2 as $v){
							$vals[] = "'".$DRW->real_escape_string($v)."'";
						}
						$DRW->query("REPLACE INTO $t$yp VALUES (".implode(',',$vals).")");
					}
					//$DRW->query("DELETE FROM $t WHERE muid=$muid$w");
                                        $DRW->query("update $t set isdelete=1 WHERE muid=$muid$w");
				}
			}
			$DRW->query("UPDATE cscan_product_email SET history_year=$yp WHERE muid=$muid and isTmp=0");
			
			usleep(20000);
			
		}
		echo $yp."\n";
	}
}
echo "done (but make sure that sphinx.conf is correct and updated)\n"
?>
