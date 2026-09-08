#!/usr/bin/php
<?php
date_default_timezone_set('America/Chicago');
$SPHINX_src_prod = 1;
$SPHINX_src_prod2 = 2;
$SPHINX_src_prod_e = 3;
	
require_once("includes/ehLog_set.php");
$ehL->start(__FILE__);

require_once ("includes/dbcon.php");
$DRW->databaseReadWrite_die = 1;

if(empty($SPHINX_src_prod) || empty($SPHINX_src_prod2) || empty($SPHINX_src_prod_e)){
        $ehL->write("can't determine my mysqlrole!");
        $ehL->stop(false);
        exit;
}

$time = time();
$H = (int)date('H',$time);
$i = (int)substr(date('i',$time),0,1);
$doall = 19;
$dosome = 3;
$outArray = array();

if(empty($SPHINX_server)){
	$SPHINX_server = 'localhost';
}
if(empty($SPHINX_port)){
	$SPHINX_port = 9312;
}
$s = new SphinxClient();
if(!$s){
	$ehL->write("SphinxClient Error");
}
else{
	$checks = $s->setServer($SPHINX_server, $SPHINX_port);
	if(!$checks){
		$ehL->write("Sphinx setServer Error");
	}
	else{
	$query2 = "SELECT productID,document_id,delete_sphinx,delete_sphinx2,dts_ids FROM cscan_sphinx_delete WHERE (counter_id=$SPHINX_src_prod OR counter_id=$SPHINX_src_prod2)";
		$query_result2 = $DRW->query($query2,$DRW_read);
		while($data2 = $DRW->fetch_row($query_result2)){
			$productID = $data2[0];
			$document_id = $data2[1];
			$delete_sphinx = $data2[2];
			$delete_sphinx2 = $data2[3];
			if(empty($data2[4])){
				$dts_ids = array();
			}
			else{
				$dts_ids = explode(',',$data2[4]);
			}
			
			if($delete_sphinx){
				$dts_idArray = array();
				foreach($dts_ids as $dts_id){
					if(!empty($dts_id)){
						$dts_idArray[$dts_id] = array(0);
					}
				}
				if(count($dts_idArray)>0){
					if(strpos(__FILE__,'demo')!==false){
						$atts = 'base_index_demo,base_index_demostar';//,base_index_demostemmed
					}
					else{
						$atts = 'base_index_prod,delta_index_prod,base_index_prodstar,delta_index_prodstar';//,base_index_prodstemmed,delta_index_prodstemmed
					}
					$updated_count = $s->updateAttributes($atts,array('dts_active'),$dts_idArray);
					$err = $s->getLastError();
					$war = $s->getLastWarning();
					if(!empty($err) || !empty($war)){
						$ehL->write("delete_sphinx: $err | $war");
					}
				}
			}
			if($delete_sphinx2){
				$dts_idArray = array();
				$dts_idArray[$productID] = array(0);
				if(count($dts_idArray)>0){
					if(strpos(__FILE__,'demo')!==false){
						$atts = 'base_index_demo2,base_index_demostar2';//,base_index_demostemmed2
					}
					else{
						$atts = 'base_index_prod2,delta_index_prod2,base_index_prodstar2,delta_index_prodstar2';//,base_index_prodstemmed2,delta_index_prodstemmed2
					}
					$updated_count = $s->updateAttributes($atts,array('dts_active'),$dts_idArray);
					$err = $s->getLastError();
					$war = $s->getLastWarning();
					if(!empty($err) || !empty($war)){
						$ehL->write("delete_sphinx2: $err | $war");
					}
				}
			}
		}
		$query = "DELETE FROM cscan_sphinx_delete WHERE (counter_id=$SPHINX_src_prod OR counter_id=$SPHINX_src_prod2)";
		$DRW->query($query,$DRW_main);
	}
}

if(strpos(__FILE__,'demo')!==false){
	$outArray[] = shell_exec('indexer --quiet --rotate base_index_demo2');
	$outArray[] = shell_exec('indexer --quiet --rotate base_index_demostar2');
	$outArray[] = shell_exec('indexer --quiet --rotate base_index_demo');
	$outArray[] = shell_exec('indexer --quiet --rotate base_index_demostar');
	$outArray[] = shell_exec('indexer --quiet --rotate base_index_demo_e');
}
else{
	$outArray[] = shell_exec('indexer --quiet --rotate delta_index_prod2');
	$outArray[] = shell_exec('indexer --quiet --rotate delta_index_prod');
	$outArray[] = shell_exec('indexer --quiet --rotate delta_index_prod_e');

	if($i==$dosome){
		$outArray[] = shell_exec('indexer --quiet --rotate delta_index_prodstar2');
		$outArray[] = shell_exec('indexer --quiet --rotate delta_index_prodstar');
		
		if($H==$doall){
			$savedQ = "REPLACE INTO cscan_delta_counter (counter_id,max_dts_updated) ( SELECT $SPHINX_src_prod2, MAX(actual_addedToDatabase) FROM cscan_product_detail )";
			$rs = $DRW->query($savedQ,$DRW_main);
			$outArray[] = shell_exec('indexer --quiet --rotate --merge base_index_prod2 delta_index_prod2 --merge-dst-range dts_active 1 1');
			$outArray[] = shell_exec('indexer --quiet --rotate delta_index_prod2');
			$outArray[] = shell_exec('indexer --quiet --rotate --merge base_index_prodstar2 delta_index_prodstar2 --merge-dst-range dts_active 1 1');
			$outArray[] = shell_exec('indexer --quiet --rotate delta_index_prodstar2');
			
			$savedQ = "REPLACE INTO cscan_delta_counter (counter_id,max_dts_updated) ( SELECT $SPHINX_src_prod, MAX(document_createddate) FROM cscan_document )";
			$rs = $DRW->query($savedQ,$DRW_main);
			$outArray[] = shell_exec('indexer --quiet --rotate --merge base_index_prod delta_index_prod --merge-dst-range dts_active 1 1');
			$outArray[] = shell_exec('indexer --quiet --rotate delta_index_prod');
			$outArray[] = shell_exec('indexer --quiet --rotate --merge base_index_prodstar delta_index_prodstar --merge-dst-range dts_active 1 1');
			$outArray[] = shell_exec('indexer --quiet --rotate delta_index_prodstar');
			
			$savedQ = "REPLACE INTO cscan_delta_counter (counter_id,max_dts_updated) ( SELECT $SPHINX_src_prod_e, MAX(email_date) FROM cscan_email )";
			$rs = $DRW->query($savedQ,$DRW_main);
			$outArray[] = shell_exec('indexer --quiet --rotate --merge base_index_prod_e delta_index_prod_e --merge-dst-range cetactive 1 1');
			$outArray[] = shell_exec('indexer --quiet --rotate delta_index_prod_e');
		}
	}
}

foreach($outArray as $o){
	if(trim($o)!=''){
		$ehL->write($o);
	}
}
if((time() - $time)>60){
	$print_done = true;
}
else{
	$print_done = false;
}
$ehL->stop($print_done);
?>
