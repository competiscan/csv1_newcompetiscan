<?php
if(!isset($_SESSION['sess_username']) || $_SESSION['sess_username']==''){
	if(isset($_REQUEST['id']) && strpos($_SERVER['PHP_SELF'],'productDocuments.php')!==false){
		$extra = '?document='.(float)$_REQUEST['id'];
	}
	elseif(isset($_REQUEST['id']) && strpos($_SERVER['PHP_SELF'],'downloads.php')!==false){
		$extra = '?download='.preg_replace('/\\W+/','',$_REQUEST['id']);
	}
	else{
		$extra = '';
	}
	ob_end_clean();
	header("Location: login.php$extra");
	exit;
}
elseif(trim(basename($_SERVER['PHP_SELF'])=='fullresults.php')){
	if(isset($_SESSION['COMPETI_activecount']) && $_SESSION['COMPETI_activecount']>=4){
		if($_SESSION['sess_userType']=='u') {
			$query = "SELECT COUNT(*) FROM cscan_sub_users WHERE active='y' AND ID='{$_SESSION['sess_userID']}'";
		}
		else  {
			$query = "SELECT COUNT(*) FROM cscan_users WHERE active='y' AND userID='{$_SESSION['sess_userID']}'";
		}
		$result = $DRW->query($query,$DRW_read);
		$rs = $DRW->fetch_row($result);
		if($rs[0]==0){
			ob_end_clean();
			header("Location: logout.php?inactive=1");
			exit;
		}
		$_SESSION['COMPETI_activecount'] = 1;
	}
	elseif(!isset($_SESSION['COMPETI_activecount'])){
		$_SESSION['COMPETI_activecount'] = 1;
	}
	else{
		$_SESSION['COMPETI_activecount']++;
	}
}
?>