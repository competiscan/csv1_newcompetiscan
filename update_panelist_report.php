#!/usr/bin/php
<?php //error_reporting(E_ALL);
//ini_set('display_errors', '1');
require_once("includes/ehLog_set.php");
$ehL->start(__FILE__);

require_once('includes/dbcon.php');
$DRW->databaseReadWrite_die = 1;
require_once('includes/clean.php');
require_once('includes/functions.php');

$start_year = 2007;
$year = $end_year = (int)date('Y');
$curr = date('Y-m');
$month_name = array('01'=>"January",'02'=>"February",'03'=>"March",'04'=>"April",'05'=>"May",'06'=>"June",'07'=>"July",'08'=>"August",'09'=>"September",'10'=>"October",'11'=>"November",'12'=>"December");
//$month_name = array('12'=>"December");
if(isset($_SERVER['argv'][1])) {
	$start_year =  $end_year = (int)$_SERVER['argv'][1];
}
if(isset($_SERVER['argv'][2])) {
	$start_month = (int)$_SERVER['argv'][2];
	$month_name = array(str_pad($start_month,2,'0',STR_PAD_LEFT)=>'one');
}

$hy_count = array();

$result = $DRW->query("SELECT sugar_id,panelist_id,contactTypeID FROM cscan_panelists WHERE active=1",$DRW_read2);
while($data = $DRW->fetch_row($result)){
	$id = $data[0];
	$panelist_id = $data[1];
	$ctype = $data[2];
	
	for($y=$start_year;$y<=$end_year;$y++){
		foreach($month_name as $key=>$value){
			$data_date = $y.'-'.$key;
			
			if($year!=$y){
				$hy = $y;
				if($hy>=2013){
					if($key>='01' && $key<='06'){
						$hm = '01';
						$hy = intval($hy.$hm);
					}
					elseif($key>='07' && $key<='12'){
						$hm = '07';
						$hy = intval($hy.$hm);
					}
				}
				if(!isset($hy_count[$hy])){
					$tmp_databaseReadWrite_die = $DRW->databaseReadWrite_die;
					$DRW->databaseReadWrite_die = 0;
					$query = "SELECT COUNT(*) FROM `cscan_email$hy`";
					$query_result = $DRW->query($query,$DRW_read2);
					$DRW->databaseReadWrite_die = $tmp_databaseReadWrite_die;
					if(mysqli_errno($DRW->current_dbh)>0){
						$hy_count[$hy] = 0;
					}
					else{
						$data2 = $DRW->fetch_row($query_result);
						$hy_count[$hy] = $data2[0];
					}
				}
				if(empty($hy_count[$hy])){
					$hy = '';
				}
			}
			else{
				$hy = '';
			}
			
                       
			$result2 = $DRW->query("SELECT SUM(ps_score) FROM cscan_panelist_score WHERE panelist_id='".$DRW->real_escape_string($id)."' AND month_year='$data_date' AND ps_score_type=1 GROUP BY month_year,ps_score_type");
			$data2 = $DRW->fetch_row($result2,$DRW_read2);
			$envelope_points = (int)$data2[0];
			$result2 = $DRW->query("SELECT SUM(ps_score) FROM cscan_panelist_score WHERE panelist_id='".$DRW->real_escape_string($id)."' AND month_year='$data_date' AND ps_score_type=2 GROUP BY month_year,ps_score_type");
			$data2 = $DRW->fetch_row($result2,$DRW_read2);
			$retrieval_points = (int)$data2[0];
			
			$directmail_points = $envelope_points + $retrieval_points;
			
			$query = "SELECT SUM(`panelist_score`) FROM `cscan_email$hy` WHERE panelist_id=$panelist_id and LEFT(email_date,7)='$data_date'";
			$query_result = $DRW->query($query,$DRW_read2);
			$data2 = $DRW->fetch_row($query_result);
			$email_points = (int)$data2[0];
			
			$query = "SELECT COUNT(*) FROM `cscan_email$hy` WHERE panelist_id=$panelist_id and LEFT(email_date,7)='$data_date' and `deleted`=1";
			$query_result = $DRW->query($query,$DRW_read2);
			$data2 = $DRW->fetch_row($query_result);
			$unused = (int)$data2[0];
			
			$query = "SELECT COUNT(*) FROM `cscan_email$hy` WHERE panelist_id=$panelist_id and LEFT(email_date,7)='$data_date' and `deleted`=2";
			$query_result = $DRW->query($query,$DRW_read2);
			$data2 = $DRW->fetch_row($query_result);
			$used = (int)$data2[0];
			
			$query = "SELECT COUNT(*) FROM `cscan_email$hy` WHERE panelist_id=$panelist_id and LEFT(email_date,7)='$data_date' and `deleted`=0";
			$query_result = $DRW->query($query,$DRW_read2);
			$data2 = $DRW->fetch_row($query_result);
			$noaction = (int)$data2[0];
			
			$extrap1 = '';
			$extrap2 = '';
			$extrap3 = '';
			if($ctype==2 && ($unused+$used+$noaction)>0){
				$queryp = "SELECT `panelist_score`,COUNT(*) FROM `cscan_email$hy` ce1 WHERE ce1.panelist_id=$panelist_id and LEFT(email_date,7)='$data_date' GROUP BY `panelist_score`";
				$query_resultp = $DRW->query($queryp,$DRW_read2);
				while($datap = $DRW->fetch_row($query_resultp)){
					if($datap[0]!=''){
                                                if($datap[0]=='0.5'){
                                                    $extrap1 .= ',points_half';
                                                }else{
                                                   $extrap1 .= ',points_'.$datap[0]; 
                                                }
						
						$extrap2 .= ','.(int)$datap[1];
                                                if($datap[0]=='0.5'){
                                                    $extrap3 .= ',points_half'.'='.(int)$datap[1];
                                                }else{
                                                   $extrap3 .= ',points_'.$datap[0].'='.(int)$datap[1];
                                                }
						
					}
				}
			}
			$producer_points = 0;
			if($ctype==1){
				$envelope_points = $retrieval_points = 0;
				if($email_points>0){
					$producer_points += 10;
				}
				if($directmail_points>0){
					$producer_points += 10;
				}
			}
			
			$query = "SELECT COUNT(*) FROM cscan_crm_contacts_data WHERE panelist_id=$panelist_id AND data_date='$data_date'";
			$query_result = $DRW->query($query,$DRW_read2);
			$data2 = $DRW->fetch_row($query_result);
			if(empty($data2[0])){
				 $query = "INSERT INTO cscan_crm_contacts_data (panelist_id,data_date,envelope_points,retrieval_points,directmail_points,email_points,unused,used,noaction,producer_points$extrap1) VALUES ($panelist_id,'$data_date','$envelope_points','$retrieval_points','$directmail_points','$email_points','$unused','$used','$noaction','$producer_points'$extrap2)";
			  	$DRW->query($query,$DRW_main);
                               
			}
			else{
			      $query = "UPDATE cscan_crm_contacts_data SET envelope_points='$envelope_points',retrieval_points='$retrieval_points',directmail_points='$directmail_points',email_points='$email_points',unused='$unused',used='$used',noaction='$noaction',producer_points='$producer_points'$extrap3 WHERE panelist_id=$panelist_id AND data_date='$data_date'";
			      $DRW->query($query,$DRW_main);
                            
			}
		}
		if($data_date>=$curr){
			break;
		}
	}
}

$ehL->stop();
?>