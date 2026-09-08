<?php  
require_once('includes/globalSession.php');
ob_clean();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Competiscan</title>
<link href="includes/competiscan_stylesheet.css" rel="stylesheet" type="text/css" />
</head>
<?php
$copied_ID = (int)$_GET['c'];
$new_userID = (int)$_GET['u'];
$get = '';

$sql1 = "SELECT ID FROM cscan_search WHERE copied_ID=$copied_ID AND userID=$new_userID";
$result1 = $DRW->query($sql1,$DRW_read); 
$data1 = $DRW->fetch_row($result1);
$newID = (int)$data1[0];

require_once('includes/search_copy.php');//$copyArray

$sql = "SELECT ID,notify,sendTo,mail_format,weekday,lastSentDate,searchName,".implode(',',$copyArray)." FROM cscan_search WHERE ID=$copied_ID AND saved=1";
$result = $DRW->query($sql,$DRW_read); 
$data = $DRW->fetch_row($result);
if(!empty($data)){
	$ID = array_shift($data);
	$notify = array_shift($data);
	$sendTo = array_shift($data);
	$mail_format = array_shift($data);
	$weekday = array_shift($data);
	$lastSentDate = array_shift($data);
	$searchName = array_shift($data);
	
	$sql2 = "SELECT emailAddress FROM cscan_users WHERE userID=$new_userID";
	$result2 = $DRW->query($sql2,$DRW_read); 
	$data2 = $DRW->fetch_row($result2);
	$sendTo = $data2[0];
	if($sendTo!='' && $ID!=0){
		$get = '?accept=1';
		if($newID!=0){
			$sql = "UPDATE cscan_search SET emailAlert=1,notify='".$DRW->real_escape_string($notify)."',sendTo='".$DRW->real_escape_string($sendTo)."',mail_format='".$DRW->real_escape_string($mail_format)."',weekday='".$DRW->real_escape_string($weekday)."',lastSentDate='".$DRW->real_escape_string($lastSentDate)."'
				WHERE ID=$newID";
			$DRW->query($sql,$DRW_main);
		}
		else{
			$q = '';
			foreach($copyArray as $k=>$f){
				$q .= ",'".$DRW->real_escape_string($data[$k])."'";
			}
			$sql = "INSERT INTO cscan_search (copied_ID,userID,userType,saved,emailAlert,notify,sendTo,mail_format,weekday,lastSentDate,searchName,".implode(',',$copyArray).") 
				VALUES ('$ID','$new_userID','a',1,1,'".$DRW->real_escape_string($notify)."','".$DRW->real_escape_string($sendTo)."','".$DRW->real_escape_string($mail_format)."','".$DRW->real_escape_string($weekday)."','".$DRW->real_escape_string($lastSentDate)."','".$DRW->real_escape_string($searchName)."'$q)";
			$DRW->query($sql,$DRW_main);
		}
	}
}

ob_end_clean();
if(!isset($_SESSION['sess_username']) || $_SESSION['sess_username']==''){
	header("Location: login.php$get");
}
else{
	header("Location: emailAlerts.php");
}
exit;
?>
</body>
</html>