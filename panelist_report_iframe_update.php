<?php 
$LOGOUT_PAGE = 'content/index.php';
$ALLOW_GROUPS = array(20);
require_once("auth_auth.php");
require_once('includes/functions.php');

if(isset($_REQUEST['upr'])){
	$Y = date('Y');
	$m = date('n');
	if($_REQUEST['upr']==2){
		$monthago = mktime(0,0,0,(int)$m-1,1,(int)$Y);
		$Y = date('Y',$monthago);
		$m = date('n',$monthago);
	}
	exec("/usr/bin/php update_panelist_report.php $Y $m");
	ob_end_clean();
	header("Location: panelist_report_month.php?updated=1");
	exit;
}

if(isset($_POST['send_dm'])){
	$month_year = "$end_yr-$end_mr";
	if($month_year=='0000-00') {
		$month_year = date('Y-m');
	}
	
	$query = "SELECT COUNT(*) FROM cscan_crm_contacts_data WHERE data_date='$month_year'";
	$query_result = $DRW->query($query,$DRW_read);
	$data = $DRW->fetch_row($query_result);
	if(empty($data[0])){
		list($Y,$m) = explode('-',$month_year);
		exec("/usr/bin/php update_panelist_report.php $Y $m");
	}
	
	$result = $DRW->query("SELECT sugar_id,first_name,last_name,competi_id,phone, address, city, stateID, postalcode,email,alt_email,panelist_id,envelope_count FROM cscan_panelists WHERE active=1$crm_contact_type_m_c",$DRW_read);
	while($data = $DRW->fetch_row($result)){
		$id = $data[0];
		$first_name = $data[1];
		$last_name = $data[2];
		$competi_id = $data[3];
		$phone = $data[4];
		$primary_address_street = $data[5];
		$primary_address_city = $data[6];
		$primary_address_state = stateName($data[7],true);
		$primary_address_postalcode = $data[8];
		$email1 = $data[9];
		$email2 = $data[10];
		$panelist_id = $data[11];
		$old_envelope_count = $envelope_count = $data[12];
		
		if(isset($_POST['used_envelopes'.$panelist_id])){
			$used_envelopes = intval($_POST['used_envelopes'.$panelist_id]);
		}
		else{
			$used_envelopes = 0;
		}
		$envelope_count = $envelope_count - $used_envelopes;
		if($envelope_count<0){
			$envelope_count = 0;
		}
		
		if(isset($_POST['totalpoints'.$panelist_id])){
			$totalpoints = intval($_POST['totalpoints'.$panelist_id]);
		}
		else{
			$totalpoints = 0;
		}
		
		if((isset($_POST['dm'.$panelist_id]) && trim($_POST['dm'.$panelist_id])!=='') || (isset($_POST['dmc'.$panelist_id]) && trim($_POST['dmc'.$panelist_id])!=='')){
			if(isset($_POST['dm'.$panelist_id]) && trim($_POST['dm'.$panelist_id])!=='') {
				$addpoints = intval($_POST['dm'.$panelist_id]);
			}
			else {
				$addpoints = 0;
			}
			
			if(isset($_POST['point_type'])) {
				$ps_score_type = (int)$_POST['point_type'];
			}
			else {
				$ps_score_type = 1;
			}
			
			$updatetext = '';
			if($ctype==2){
				$tmpaddpoints = 0;
				if(isset($_POST['dmc'.$panelist_id]) && trim($_POST['dmc'.$panelist_id])!=='') {
					$addpointsc = intval($_POST['dmc'.$panelist_id]);
				}
				else {
					$addpointsc = 0;
				}
				$time = time();
				$new = 0;
				foreach(array(2=>$addpoints,50=>$addpointsc) as $ps_score=>$ptot){
					$tmpaddpoints += ($ps_score * $ptot);
					for($i=0;$i<$ptot;$i++){
						$sql = "REPLACE INTO cscan_panelist_score (ps_date,panelist_id,ps_score,month_year,ps_score_type) VALUES ('".date('Y-m-d H:i:s',$time+$new)."','".$DRW->real_escape_string($id)."',$ps_score,'".$DRW->real_escape_string($month_year)."','".$DRW->real_escape_string($ps_score_type)."')";
						$DRW->query($sql,$DRW_main);
						$new++;
					}
					//$updatetext .= ',points_'.$ps_score.'=points_'.$ps_score.'+'.$ptot;
				}
				$addpoints = $tmpaddpoints;
				if($ps_score_type==2){
					$updatetext .= ',retrieval_points=retrieval_points+'.$addpoints;
				}
				else{
					$updatetext .= ',envelope_points=envelope_points+'.$addpoints;
				}
			}
			else{
				$sql = "REPLACE INTO cscan_panelist_score (panelist_id,ps_score,month_year,ps_score_type) VALUES ('".$DRW->real_escape_string($id)."','".$DRW->real_escape_string($addpoints)."','".$DRW->real_escape_string($month_year)."','".$DRW->real_escape_string($ps_score_type)."')";
				$DRW->query($sql,$DRW_main);
				
				if($addpoints>0){
					$producer_points = 0;
					$query = "SELECT email_points,directmail_points FROM cscan_crm_contacts_data WHERE panelist_id=$panelist_id AND data_date='$month_year'";
					$query_result = $DRW->query($query,$DRW_read);
					$data = $DRW->fetch_row($query_result);
					if($data[0]>0){
						$producer_points += 10;
					}
					if($data[1] + $addpoints>0){
						$producer_points += 10;
					}
					$updatetext .= ',producer_points='.$producer_points;
				}
			}
			$sql = "UPDATE cscan_crm_contacts_data SET directmail_points=directmail_points+$addpoints$updatetext WHERE panelist_id=$panelist_id AND data_date='$month_year'";
			$DRW->query($sql,$DRW_main);
		}
		if(isset($_POST['ps'.$panelist_id])){
			$query_update = "UPDATE cscan_panelists SET points_sent=(points_sent+".$consumer_max_points."),points_sent_date=NOW() WHERE panelist_id=$panelist_id";
			$DRW->query($query_update,$DRW_main);
			
			$sql = "UPDATE cscan_crm_contacts_data SET points_sent=points_sent+$consumer_max_points WHERE panelist_id=$panelist_id AND data_date='$month_year'";
			$DRW->query($sql,$DRW_main);
		}
		
		if(isset($_POST['old_es'.$panelist_id])){
			if(isset($_POST['old_es'.$panelist_id])) {
				$old_es = (int) $_POST['old_es'.$panelist_id];
			}
			else {
				$old_es = 0;
			}
			if(isset($_POST['es'.$panelist_id])) {
				$es = (int) $_POST['es'.$panelist_id];
			}
			else {
				$es = 0;
			}
			
			if($es==1) {
				$envelope_count = $max_envelopes;
			}
			if($old_es!=$es){
				$query_update = "UPDATE cscan_panelists SET envelope_sent=$es WHERE panelist_id=$panelist_id";
				$DRW->query($query_update,$DRW_main);
			}
		}
		
		if($old_envelope_count!=$envelope_count){
			$query_update = "UPDATE cscan_panelists SET envelope_count=$envelope_count WHERE panelist_id=$panelist_id";
			$DRW->query($query_update,$DRW_main);
		}
		if(isset($_POST['note'.$panelist_id]) && trim($_POST['note'.$panelist_id])!=''){
			$query_update = "REPLACE INTO cscan_panelists_note (note_data,panelist_id) VALUES ('".$DRW->real_escape_string($_POST['note'.$panelist_id])."',$panelist_id)";
			$DRW->query($query_update,$DRW_main);
		}
	}
	ob_end_clean();
	header("Location: {$_SERVER['PHP_SELF']}?updated=1");
	exit;
}
?>
