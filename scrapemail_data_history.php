<?php

error_reporting(E_ALL ^ E_DEPRECATED);
ini_set('display_errors', 1);
ini_set("memory_limit", "-1");
set_time_limit(0);
require_once ("includes/dbcon.php");
$DRW->databaseReadWrite_die = 1;
include_once 'includes/functions.php';
$dirName = dirname(__FILE__);
require_once("includes/email_functions.php");

$and = '';

$previousdate = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d'))));
//$and	=" AND e.email_date> '".$previousdate."' limit 100 ";
//$and	=" AND  e.is_fetch=1 ";

$database_tables = [
	/*'cscan_email201501' => [
		'min' => 10593116,
		'max' => 12172713,
		'text_table' => 'cscan_email_text201501'
	],
	'cscan_email201507' => [
		'min' => 12172714,
		'max' => 14125930,
		'text_table' => 'cscan_email_text201507'
	],
	'cscan_email201601' => [
		'min' => 14125931,
		'max' => 16539187,
		'text_table' => 'cscan_email_text201601'
	],
	'cscan_email201607' => [
		'min' => 16183124,
		'max' => 18905212,
		'text_table' => 'cscan_email_text201607'
	],
	'cscan_email201701' => [
		'min' => 18905213,
		'max' => 22265357,
		'text_table' => 'cscan_email_text201701'
	],
	'cscan_email201707' => [
		'min' => 22207991,
		'max' => 26712228,
		'text_table' => 'cscan_email_text201707'
	],
	'cscan_email201801' => [
		'min' => 26127099,
		'max' => 29447129,
		'text_table' => 'cscan_email_text201801'
	],
	'cscan_email201807' => [
		'min' => 29346933,
		'max' => 32425148,
		'text_table' => 'cscan_email_text201807'
	],
	'cscan_email201901' => [
		'min' => 32424731,
		'max' => 35266609,
		'text_table' => 'cscan_email_text201901'
	],*/
	'cscan_email' => [
		'min' => 34836217,
		'max' => 44444444,
		'text_table' => 'cscan_email_text'
	],
];
function findTable($muid){
	global $database_tables;
	foreach($database_tables as $table_key => $table_min_max) {
		if($muid >= $table_min_max['min'] && $muid<= $table_min_max['max']){
			return $table_key;
		}
	}
	return null;
}

// $f1 = fopen($dirName."/test-files/text-name.txt", 'a+');
// $f2 = fopen($dirName."/test-files/text-email.txt", 'a+');
// $f3 = fopen($dirName."/test-files/text-date.txt", 'a+');

foreach($database_tables as $dbKey => $dbTable){

  //$product_query = "select muid from cscan_product_email where addedToDatabase > '2015-01-01' AND muid >= 10593116 AND muid>(select max_muid from cscan_scrap_email_log where table_name='cscan_product_email') limit 0, 1000";
  //$product_query = "select p.muid from {$dbKey} as e join cscan_product_email as p on (p.muid=e.muid) where e.is_fetch='0' limit 0,100000";
  echo $product_query = "select e.muid from {$dbKey} as e limit 0,10";
  $product_query_result = $DRW->query($product_query, $DRW_main);
  if ($DRW->num_rows($product_query_result) > 0) {
      while ($productRow = $DRW->fetch_assoc($product_query_result)) {
  			$email_table_name = findTable($productRow['muid']);
  			if(isset($database_tables[$email_table_name])){
  				$email_table = $database_tables[$email_table_name];
  				$tableName = $email_table['text_table'];
  				//$hy=201501;
  				$muid = $productRow['muid'];

  				//$sqlinsert   =  "update cscan_scrap_email_log set max_muid=(select max(muid) from cscan_email$hy where is_fetch=1) where table_name='cscan_email$hy'";
			echo "\n";
			echo $sql = "SELECT et.muid,et.cettext,et.cettype from {$tableName} as et where et.cettype='text/html' AND et.muid={$muid}";
			echo "\n";
  				$nameEmailDate = [ 'name' => 0, 'email' => 0, 'date' => 0];
  				$query = $DRW->query($sql, $DRW_read);
  				if ($DRW->num_rows($query) > 0) {
  				    $row = $DRW->fetch_assoc($query);
  						$queryObj = [];
						  $emailDetails = getFromText($row['cettext'], $row['muid'], $row['cettype']);
              $emailDetails['muid'] = $row['muid'];
  						if($emailDetails['name'] == null){
  					    //fwrite($f1, $emailDetails['muid']."\n");
  					  }else if(strlen($emailDetails['name']) > 70){
  					    //fwrite($f1, "\t".$emailDetails['muid']."\t\t\t\t".$emailDetails['name']."\n");
  					    $emailDetails['name'] = null;
  					  }
  					  if($emailDetails['email'] == null){
  					    //fwrite($f2, $emailDetails['muid']."\n");
  					  }else if(!valid_email($emailDetails['email'])){
  					    //fwrite($f2, "\t".$emailDetails['muid']."\t\t\t\t".$emailDetails['email']."\n");
  					    $emailDetails['email'] = null;
  					  }
  					  if($emailDetails['date'] == null){
  					    //fwrite($f3, $emailDetails['muid']."\n");
  					  }else if(strlen($emailDetails['date']) > 76){
  					    //fwrite($f3, "\t".$emailDetails['muid']."\t\t\t\t".$emailDetails['date']."\n");
  					    $emailDetails['date'] = null;
  					  }
  					  if(strlen(trim($emailDetails['name']))){
  					    $nameEmailDate['name']++;
  					  }
  					  if(strlen(trim($emailDetails['email']))){
  					    $nameEmailDate['email']++;
  					  }
  					  if(strlen(trim($emailDetails['date']))){
  					    $nameEmailDate['date']++;
  					  }

  						if(!empty($emailDetails['name'])){
  							$queryObj['from_sent_name'] = $DRW->real_escape_string($emailDetails['name']);
  						}else{
  							$queryObj['from_sent_name'] = null;
  						}
  						if(!empty($emailDetails['email'])){
  							$queryObj['from_sent_email_address'] = $DRW->real_escape_string($emailDetails['email']);
  						}else{
  							$queryObj['from_sent_email_address'] = null;
  						}
  						if(!empty($emailDetails['date'])){
  							$queryObj['from_sent_date'] = $DRW->real_escape_string($emailDetails['date']);
  							$queryObj['from_sent_date_format'] = date('Y-m-d H:i:s', strtotime($emailDetails['date']));
  						}else{
  							$queryObj['from_sent_date'] = null;
  						}
  						$queryObj['is_fetch'] = 1;
  						$updateQuery = [];
  						foreach ($queryObj as $dataKey => $dataValue) {
  								$updateQuery[] ="{$dataKey}='{$dataValue}'";
  						}

  					  $sql = "UPDATE {$email_table_name} SET ".
  										implode(', ', $updateQuery) .
  										"	WHERE muid ='" . $row['muid'] . "'";
  						if ($DRW->query($sql, $DRW_main)) {
  								$success="Success";
  						}
  			    }else{
              $sql = "UPDATE {$email_table_name} SET is_fetch='1'	WHERE muid ='" . $muid . "'";
              $DRW->query($sql, $DRW_main);
            }
            //$sqlinsert = "update cscan_scrap_email_log set max_muid={$muid}  where table_name='cscan_product_email'";
            //$query = $DRW->query($sqlinsert, $DRW_main);
  			}
  		}
  }
  sleep(5);
}
// fclose($f1);
// fclose($f2);
// fclose($f3);
//echo"jjj";
exit;
?>
