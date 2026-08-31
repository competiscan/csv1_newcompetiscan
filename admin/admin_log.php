<?php 
$ALLOW_GROUPS = array(5);
require_once "../auth_auth.php";
header('Content-Type: text/html; charset=iso-8859-1');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<title>Competiscan Email</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<script language="JavaScript" src="../includes/jsFunctions.js" type="text/JavaScript"></script>
<link href="../includes/styleSheet.css" rel="stylesheet" type="text/css" />
</head>
<body style="margin:10px;">
<?php 
if(isset($_GET['mid'])) $mid = (float)$_GET['mid'];
else $mid = 0;
if(isset($_GET['pid'])) $pid = (float)$_GET['pid'];
else $pid = 0;
if(isset($_GET['istmp'])) $istmp = (int)$_GET['istmp'];
else $istmp = 0;
$allArray = array();
$allArray[] = array($pid,$mid,$istmp);
if($pid!=0) {
	$sql = "SELECT muid,isTmp FROM cscan_product_email WHERE productID=$pid ORDER BY addedToDatabase DESC LIMIT 1";
	$rs = $DRW->query( $sql,$DRW_read );
	$row = $DRW->fetch_row($rs);
	$old_mid = (float)$row[0];
	$old_istmp = (int)$row[1];
	if($old_mid!=0){
		$allArray[] = array(0,$old_mid,$old_istmp);
	}
}
foreach($allArray as $inArray){
	$className='';
	list($pid,$mid,$istmp) = $inArray;
	
	echo '<div style="font-weight:bold;">';
	if($mid!=0) {
		echo $mid;
		if($istmp) echo 'tmp';
	}
	else {
		echo $pid;
	}
	echo '</div>';
	?>
	<table width="100%" border="0" cellspacing="0" cellpadding="5" class="text">
	 <tr>
	 	<td class="adminhead"><strong>Date</strong></td>
	    <td class="adminhead"><strong>User</strong></td>
	  </tr>
	<?php
	$sql = "SELECT userID,DATE_FORMAT(logDate,'%m/%d/%y %r') FROM cscan_admin_log WHERE productID=$pid AND muid=$mid AND isTmp=$istmp ORDER BY logDate DESC";
	$rs = $DRW->query( $sql,$DRW_read );
	if($DRW->num_rows($rs)>0){
		while($row = $DRW->fetch_array($rs) ) {
			if ($className=='selected-bg') $className='white-bg';
			else $className='selected-bg';
			$userID = $row[0];
			$logDate = $row[1];
			
			$userquery = "SELECT userName FROM cscan_admin_users WHERE userID=$userID";
			$userquery = $DRW->query($userquery,$DRW_read);
			if($DRW->num_rows($userquery)>0) {
				$unam = $DRW->fetch_row($userquery);
				$userName = $unam[0];
			}
			else $userName = $userID;
			?>
			<tr class="<?php echo $className;?>">
		        <td valign="top"><?php echo $logDate; ?></td>
		        <td valign="top"><?php echo $userName; ?></td>
		    </tr>
			<?php
		}
	}
	else echo '<tr><td colspan="2">No Entries</td></tr>';
	?>
	</table>
	<div>&nbsp;</div>
<?php 
}
?>
<a href="#" onclick="self.close(); return false;">close</a>
</body>
</html>