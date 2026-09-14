#!/usr/bin/php
<?php
require_once("../includes/ehLog_set.php");
$ehL->start(__FILE__);

require_once("../includes/dbcon.php");
$DRW->databaseReadWrite_die = 1;
require_once("../includes/functions.php");

$reportDate_ts2 = time();
$ymdHis = date('Y-m-d H:i:s',$reportDate_ts2);
$typeArray = array('1'=>'Approved','4'=>'Problem','3'=>'Reprocessed','2'=>'Unapproved');
$qU = "SELECT userID,userName FROM cscan_admin_users WHERE user_status=1 ORDER BY userName";
$resultU = $DRW->query($qU,$DRW_read);
while($dataU = $DRW->fetch_row($resultU)){
	$userID = $dataU[0];
	$userName = $dataU[1];
	
	$q = "SELECT MAX(ymdHis) from cscan_admin_log_combined where userID=$userID";
	$result = $DRW->query($q,$DRW_read);
	$data = $DRW->fetch_row($result);
	$max_combined = $data[0];
	if(!empty($max_combined)){
		$q = "SELECT MAX(logDate) from cscan_admin_log where userID=$userID";
		$result = $DRW->query($q,$DRW_read);
		$data = $DRW->fetch_row($result);
		$max = $data[0];
		if($max_combined>=$max){
			continue;
		}
		$reportDate = substr($max_combined,0,10).' 00:00:00';
	}
	else{
		$q = "SELECT LEFT(MIN(logDate),10) from cscan_admin_log where userID=$userID";
		$result = $DRW->query($q,$DRW_read);
		$data = $DRW->fetch_row($result);
		if(!empty($data[0])){
			$reportDate = $data[0].' 00:00:00';
		}
		else{
			continue;
		}
	}
	$reportDate_ts = strtotime($reportDate);
	
	for($i=$reportDate_ts;$i<=$reportDate_ts2;$i+=86400){
		$Date = date('Y-m-d',$i);
		$q = "SELECT COUNT(DISTINCT al.productID)
			FROM cscan_admin_log al LEFT JOIN cscan_product_detail pd ON(al.productID=pd.productID)
			WHERE muid=0 AND isTmp=0 AND al.userID=$userID AND logDate>='".$DRW->real_escape_string($Date)." 00:00:00' AND logDate<='".$DRW->real_escape_string($Date)." 23:59:59' AND pd.productID IS NULL";
		$result = $DRW->query($q,$DRW_read);
		$data = $DRW->fetch_row($result);
		$tmp = $data[0];
		$q = "SELECT COUNT(DISTINCT al.muid,al.isTmp)
			FROM cscan_admin_log al LEFT JOIN cscan_product_email pd ON(pd.muid=al.muid AND pd.isTmp=al.isTmp)
			WHERE al.productID=0 AND al.userID=$userID AND logDate>='".$DRW->real_escape_string($Date)." 00:00:00' AND logDate<='".$DRW->real_escape_string($Date)." 23:59:59' AND pd.muid IS NULL";
		$result = $DRW->query($q,$DRW_read);
		$data = $DRW->fetch_row($result);
		$deleted = $data[0] + $tmp;
		
		$sects = array();
		$stots = array();
		$stots['d'] = array();
		$stots['d']['0'] = $deleted;
		$sects[] = '0';
		$stots['t'] = array();
		$stots['f'] = array();
		foreach($typeArray as $t=>$tname){
			if(!isset($stots[$t])){
				$stots[$t] = array();
			}
			$q = "SELECT DISTINCT al.productID,pd.sectorID
				FROM cscan_admin_log al LEFT JOIN cscan_product_detail pd ON (pd.productID=al.productID)
				WHERE al.productStatus=$t
				AND al.muid=0 AND al.isTmp=0 AND al.userID=$userID AND logDate>='".$DRW->real_escape_string($Date)." 00:00:00' AND logDate<='".$DRW->real_escape_string($Date)." 23:59:59'";
			$result = $DRW->query($q,$DRW_read);
			while($data = $DRW->fetch_row($result)){
				$ss = explode(',',$data[1]);
				if(count($ss)>0){
					foreach($ss as $s){
						if(!empty($s)){
							if(!isset($stots[$t][$s])){
								$stots[$t][$s] = 0;
								if(!in_array($s,$sects)){
									$sects[] = $s;
								}
							}
							$stots[$t][$s]+=1;
						}
					}
				}
				else{
					if(!isset($stots[$t]['0'])){
						$stots[$t]['0'] = 0;
					}
					$stots[$t]['0']+=1;
				}
				if($t=='1'){
					$q2 = "SELECT userID
						FROM cscan_admin_log
						WHERE productStatus=$t
						AND muid=0 AND isTmp=0 AND productID=$data[0] ORDER BY logDate ASC LIMIT 1";
					$result2 = $DRW->query($q2,$DRW_read);
					$data2 = $DRW->fetch_row($result2);
					if($data2[0]==$userID){
						if(count($ss)>0){
							foreach($ss as $s){
								if(!empty($s)){
									if(!isset($stots['f'][$s])){
										$stots['f'][$s] = 0;
										if(!in_array($s,$sects)){
											$sects[] = $s;
										}
									}
									$stots['f'][$s]+=1;
								}
							}
						}
						else{
							if(!isset($stots['f']['0'])){
								$stots['f']['0'] = 0;
							}
							$stots['f']['0']+=1;
						}
					}
				}
			}
		}
		$q = "SELECT DISTINCT al.muid,al.isTmp,pd.sectorID
			FROM cscan_admin_log al LEFT JOIN cscan_product_email pd on (al.productID=0 AND pd.muid=al.muid AND pd.isTmp=al.isTmp)
			WHERE al.userID=$userID AND logDate>='".$DRW->real_escape_string($Date)." 00:00:00' AND logDate<='".$DRW->real_escape_string($Date)." 23:59:59'";
		$result = $DRW->query($q,$DRW_read);
		while($data = $DRW->fetch_row($result)){
			$ss = explode(',',$data[2]);
			if(count($ss)>0){
				foreach($ss as $s){
					if(!empty($s)){
						if(!isset($stots['t'][$s])){
							$stots['t'][$s] = 0;
						}
						$stots['t'][$s]+=1;
						if(!in_array($s,$sects)){
							$sects[] = $s;
						}
					}
				}
			}
			else{
				if(!isset($stots[$t]['0'])){
					$stots['t']['0'] = 0;
				}
				$stots['t']['0']+=1;
			}
		}
		foreach($sects as $s){
			$first_approved = $approved = $problem = $reprocessed = $unapproved = $temp_product = $deleted = 0;
			$in = false;
			if(!empty($stots['t'][$s])){
				$in = true;
				$temp_product = $stots['t'][$s];
			}
			if(!empty($stots['d'][$s])){
				$in = true;
				$deleted = $stots['d'][$s];
			}
			if(!empty($stots['1'][$s])){
				$in = true;
				$approved = $stots['1'][$s];
			}
			if(!empty($stots['2'][$s])){
				$in = true;
				$unapproved = $stots['2'][$s];
			}
			if(!empty($stots['3'][$s])){
				$in = true;
				$reprocessed = $stots['3'][$s];
			}
			if(!empty($stots['4'][$s])){
				$in = true;
				$problem = $stots['4'][$s];
			}
			if(!empty($stots['f'][$s])){
				$in = true;
				$first_approved = $stots['f'][$s];
			}
			if($in){
				$result = $DRW->query("REPLACE INTO cscan_admin_log_combined (ymdHis,ymd,userID,approved,problem,reprocessed,unapproved,temp_product,deleted,sectorID,first_approved) 
					VALUES ('$ymdHis','$Date',$userID,$approved,$problem,$reprocessed,$unapproved,$temp_product,$deleted,$s,$first_approved)",$DRW_main);
			}
		}
	}
}

$ehL->stop();
?>