<?php
$ALLOW_GROUPS = array(30);
require_once("../auth_auth.php");
include 'top.php';
?>
<table width="100%" border="0" cellspacing="0" cellpadding="5" class="text">
  <tr><td class="adminhead" align="center" colspan="3">COMPETISCAN ALERT MANAGEMENT</td></tr>
</table>
<?php 
if(isset($_REQUEST['maID'])){
	if(isset($_POST['sendma'])){
		if($_REQUEST['maID']==0){
			$sql = "INSERT INTO cscan_message_alert (maDate,maContent,maTitle) VALUES ('".$DRW->real_escape_string($_POST['maDate'])."','".$DRW->real_escape_string($_POST['maContent'])."','".$DRW->real_escape_string($_POST['maTitle'])."')";
			$DRW->query($sql,$DRW_main);
		}
		else{
			$sql = "UPDATE cscan_message_alert SET maDate='".$DRW->real_escape_string($_POST['maDate'])."',maContent='".$DRW->real_escape_string($_POST['maContent'])."',maTitle='".$DRW->real_escape_string($_POST['maTitle'])."' WHERE maID=".(int)$_REQUEST['maID'];
			$DRW->query($sql,$DRW_main);
		}
		
		ob_end_clean();
		header("Location: {$_SERVER['PHP_SELF']}");
		exit;
	}
	if($_REQUEST['maID']!=0){
		$sql_ma = "SELECT maDate,maContent,maTitle FROM cscan_message_alert WHERE maID=".(int)$_REQUEST['maID'];
		$rs_ma = $DRW->query($sql_ma,$DRW_read);
		$row_ma = $DRW->fetch_row($rs_ma);
		$maDate = $row_ma[0];
		$maContent = $row_ma[1];
		$maTitle = $row_ma[2];
	}
	else{
		$maDate = date('Y-m-d');
		$maContent = '';
		$maTitle = 'Please click on the Competiscan Alert below for an important message ('.date('m.d.Y').')';
	}
	?>
	<form method="post" name="frm1" action="<?php echo $_SERVER['PHP_SELF'].'?maID='.(int)$_REQUEST['maID']; ?>">
	<table border="0" cellspacing="0" cellpadding="5" class="text">
	    <tr>
			<td class="adminhead">Date</td>
			<td class="bodytext"><input type="text" name="maDate" size="40" maxlength="255" class="input_box" value="<?php echo htmlspecialchars($maDate,ENT_QUOTES); ?>" /></td>
		</tr>
	    <tr>
			<td class="adminhead">Title</td>
			<td class="bodytext"><input type="text" name="maTitle" size="80" maxlength="255" class="input_box" value="<?php echo htmlspecialchars($maTitle,ENT_QUOTES); ?>" /></td>
		</tr>
	    <tr>
			<td class="adminhead" valign="top">Content</td>
			<td class="bodytext"><textarea name="maContent" rows="40" cols="80" class="input_box"><?php echo htmlspecialchars($maContent,ENT_QUOTES); ?></textarea></td>
		</tr>
	  <tr>
	    <td class="adminhead">&nbsp;</td>
	    <td><input class="button" type="submit" name="submit1" value="Save" /> &nbsp; <input class="button" type="submit" name="submit2" value="Cancel" onclick="document.location.href='<?php echo $_SERVER['PHP_SELF']; ?>'; return false;" /></td>
	  </tr>
	</table>
	<input type="hidden" name="sendma" value="1" /></form>
	<?php 
}
else {
	if(isset($_POST['send'])){
		$sql = "UPDATE cscan_message_alert SET maActive=0";
		$DRW->query($sql,$DRW_main);
		
		if(isset($_POST['maActive'])){
			for($i=0;$i<count($_POST['maActive']);$i++) {
				$sql = "UPDATE cscan_message_alert SET maActive=1 WHERE maID=".(int)$_POST['maActive'][$i];
				$DRW->query($sql,$DRW_main);
			}
		}
		
		ob_end_clean();
		header("Location: {$_SERVER['PHP_SELF']}?save=1");
		exit;
	}
	?>
	<form method="post" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<table border="0" cellspacing="0" cellpadding="5" class="text">
	  <tr>
	    <td colspan="2"><strong>Note</strong>: Click any of the following to view the message.</td>
	    <td colspan="2"><input class="button" type="submit" name="submit2" value="Add" onclick="document.location.href='<?php echo $_SERVER['PHP_SELF']; ?>?maID=0'; return false;" /></td>
	  </tr>
	    <tr>
			<td class="adminhead">Date</td>
			<td class="adminhead">Title</td>
			<td class="adminhead">&nbsp;</td>
			<td class="adminhead">Active</td>
		</tr>
	<?php
		$className='';
		$sql = "SELECT maID,DATE_FORMAT(maDate,'%m/%d/%Y'),maActive,maTitle FROM cscan_message_alert ORDER BY maDate DESC";
		$rs = $DRW->query($sql,$DRW_read);
		while($row = $DRW->fetch_row($rs)) {
			
			if ($className=='selected-bg') $className='white-bg';
			else $className='selected-bg';
	?>
		<tr class="<?php echo $className;?>">
		<td class="bodytext" valign="top"><?php echo $row[1]; ?></td>
		<td class="bodytext" valign="top"><?php echo $row[3]; ?></td>
		<td class="bodytext" valign="top"><?php echo "<a href=\"../cma.php?id=$row[0]\" onclick=\"showHelp('../cma.php?id=$row[0]'); return false;\">View</a> &nbsp; <a href=\"{$_SERVER['PHP_SELF']}?maID=$row[0]\">Edit</a>"; ?></td>
		<td class="bodytext" valign="top"><input type="checkbox" name="maActive[]" value="<?php echo $row[0]; ?>"<?php if($row[2]) echo ' checked="checked"'; ?> /></td>
		</tr>
	<?php
		}
	?>
	  <tr>
	    <td colspan="3" align="right"><?php 
		if(isset($_GET['save'])) print '<span class="error">Updated</span>';
		else print '&nbsp;';
	    ?></td>
	    <td><input class="button" type="submit" name="submit1" value="Update" /></td>
	  </tr>
	</table>
	<input type="hidden" name="send" value="1" /></form>
	<script type="text/javascript">
	<!--
	function showHelp(file){
		var win = window.open(file,'fulltext','top=0,left=0,height=650,width=600,resizable=1,scrollbars=yes');
		win.focus();  
	}
	//-->
	</script>
	<?php 
}
include 'bottom.php';
?>
