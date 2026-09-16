<?php
require_once('includes/globalSession.php');
require_once('includes/checklogin.php');

@ob_clean();
$sendTypes = array('to','cc','bcc');
if(isset($_REQUEST['emailto_name'])){
	if(isset($_REQUEST['emailto_id'])) {
		$emailto_id = (int)$_REQUEST['emailto_id'];
	}
	else {
		$emailto_id = 0;
	}
	$in = false;
	$email_list_valid = array();
	foreach($sendTypes as $st){
		$email_list_valid[$st] = array();
		$emailArray = getEmailParse($_REQUEST['email'.$st.'_list']);
		foreach ($emailArray as $id => $arry) {
			$count = 0;
			if($_SESSION['sess_plevel']==2){
				$query2 = "SELECT COUNT(*) FROM cscan_users WHERE (companyName='".$DRW->real_escape_string($_SESSION['sess_companyName'])."' OR plevel>0) AND active='y' AND emailAddress='".$DRW->real_escape_string($emailArray[$id]['address'])."'";
				$result2 = $DRW->query($query2,$DRW_read);
				$data2 = $DRW->fetch_row($result2);
				$count = $data2[0];
			}
			elseif($_SESSION['sess_plevel']==1){
				$count = 1;
			}
			else{
				$query2 = "SELECT COUNT(*) FROM cscan_users WHERE companyName='".$DRW->real_escape_string($_SESSION['sess_companyName'])."' AND active='y' AND emailAddress='".$DRW->real_escape_string($emailArray[$id]['address'])."'";
				$result2 = $DRW->query($query2,$DRW_read);
				$data2 = $DRW->fetch_row($result2);
				$count = $data2[0];
			}
			if($count>0){
				$email_list_valid[$st][] = $emailArray[$id]['address'];
				$in = true;
			}
		}
	}
	if($in){
		if($emailto_id!=0){
			$in1 = ',emailto_id';	
			$in2 = ','.$emailto_id;	
		}
		else{
			$in1 = '';	
			$in2 = '';	
		}
		$q = "REPLACE INTO cscan_emailto_list (emailto_name,emailto_list,emailcc_list,emailbcc_list,userID$in1) VALUES ('".$DRW->real_escape_string($_REQUEST['emailto_name'])."','".$DRW->real_escape_string(implode(',',$email_list_valid['to']))."','".$DRW->real_escape_string(implode(',',$email_list_valid['cc']))."','".$DRW->real_escape_string(implode(',',$email_list_valid['bcc']))."','".$DRW->real_escape_string($_SESSION['sess_userID'])."'$in2)";
		$query_result = $DRW->query($q,$DRW_main);
	}
	echo 1;
}
else {
	echo 0;
}
?>