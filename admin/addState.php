<?php
$ALLOW_GROUPS = array(14);
require_once("../auth_auth.php");
include 'top.php';
?>
<table width="100%" border="0" cellspacing="0" cellpadding="5" class="text">
<tr><td class="adminhead" align="center">STATE/PROVINCE MANAGEMENT</td></tr>
<tr><td align="right" class="bodytext"><strong><span class="error">* required field</span></strong></td></tr>
<?php
if(isset($_GET['id'])) $updID = $_GET['id'];
else $updID = '';
$error_msg = '';
$stateName = '';
$stateCode = '';
$countryCode = '';
$panelist_stateID = '';
if(isset($_POST['submit'])) {
	$stateName = $DRW->real_escape_string($_POST['stateName']);
	$stateCode = $DRW->real_escape_string($_POST['stateCode']);
	$countryCode = $DRW->real_escape_string($_POST['countryCode']);
	$panelist_stateID = $DRW->real_escape_string($_POST['panelist_stateID']);
	
	$chk = "SELECT * FROM cscan_state WHERE (stateName='".$stateName."' OR  stateCode='".$stateCode."' OR panelist_stateID='".$panelist_stateID."') AND stateID!='$updID'";
	$rs = $DRW->query($chk,$DRW_read);
	if($DRW->num_rows($rs) == 0) {
		if($updID == '') {
			$sql = "INSERT INTO cscan_state SET stateName='".$stateName."',stateCode='".$stateCode."',countryCode='".$countryCode."',panelist_stateID='".$panelist_stateID."'";
			$DRW->query($sql,$DRW_main);
		}
		else {
			$sql = "UPDATE cscan_state SET stateName='".$stateName."',stateCode='".$stateCode."',countryCode='".$countryCode."',panelist_stateID='".$panelist_stateID."' WHERE stateID='$updID'";
			$DRW->query($sql,$DRW_main);
		}
		
		if($_POST['submit'] == 'Save & Add More'){
			ob_end_clean();
			header("Location: addState.php?a=1");
			exit;
		}
		else{
			ob_end_clean();
			header("Location: manageState.php");
			exit;
		}
	}
	else {
		$error_msg = 'State/Province Name already exists';
	}
}
else{
	if($updID!='') {
		$sql = "SELECT * FROM cscan_state WHERE stateID='$updID'";
		$editRS = $DRW->query($sql,$DRW_read);
		$editRS = $DRW->fetch_array($editRS);
		$stateName = $editRS['stateName'];
		$stateCode = $editRS['stateCode'];
		$panelist_stateID = $editRS['panelist_stateID'];
		$countryCode = $editRS['countryCode'];
	}
}
?>
<tr><td align="center">
<div style="border:solid 1px #14734F;width:60%;">
	<form method="post" name="frm1" onsubmit="return validate();">
	<table width="100%" border="0" cellspacing="0" cellpadding="4">
	<tr><td class="subhead" align="center" colspan="2"><?php if($updID!='') echo 'UPDATE'; else echo 'ADD';?> State/Province Name</td></tr>
	<?php 
	echo "<tr><td class='error' align='center' colspan='2'>$error_msg</td></tr>";
	?>
	<?php 
	if(isset($_GET['a'])) {
		echo "<tr><td align=\"center\" colspan=\"2\" class=\"error\">New State/Province has been added sucessfully.</td></tr>";
	}
	?>
	<tr><td colspan="2">&nbsp;</td></tr>
	<tr>
	<td class="bodytext" align="right">State/Province Name<font class="error">*</font>:</td>
	<td><input type="text" name="stateName" size="40" class="input_box" value="<?php echo htmlspecialchars($stateName,ENT_QUOTES); ?>" /></td></tr>
	<tr>
	<td class="bodytext" align="right">State/Province Code<font class="error"></font>:</td>
	<td><input type="text" name="stateCode" size="10" class="input_box" value="<?php echo htmlspecialchars($stateCode,ENT_QUOTES); ?>" /></td></tr>
	<tr>
	<td class="bodytext" align="right">Country<font class="error"></font>:</td>
	<td><select class="combo_box" name="countryCode"><option value="">&nbsp;</option>
	<?php
	$sql = "SELECT code,country FROM ISO31661_alpha2code ORDER BY country";
	$rs = $DRW->query( $sql,$DRW_read );
	while($row = $DRW->fetch_row($rs) ) {
		echo '<option value="'.$row[0].'"';
		if($row[0]==$countryCode){
			echo ' selected="selected"';
		}
		echo '>'.htmlspecialchars($row[1]).'</option>';
	}
	?>
	</select></td></tr>
	<tr>
	<td class="bodytext" align="right">Panelist Code<font class="error"></font>:</td>
	<td><input type="text" name="panelist_stateID" size="10" class="input_box" value="<?php echo htmlspecialchars($panelist_stateID,ENT_QUOTES); ?>" /></td></tr>
	<tr><td colspan="2">&nbsp;</td></tr>
	<tr>
	<td>&nbsp;</td>
	<td>
	<?php if($updID == ''){?>
		<input class="button" type="submit" name="submit" value="Save" onClick="return validate();" disabled="disabled"/>
		<input class="button" type="submit" name="submit" value="Save &amp; Add More" style="width:120px;" onClick="return validate();" disabled="disabled"/>
	<?php }
	else{ ?>
		<input class="button" type="submit" name="submit" value="Update" onClick="return validate();" disabled="disabled"/>
		<input type="hidden" name="id" value="<?php echo $updID; ?>" />
	<?php }?>
	<input class="button" type="button" value="Cancel" onclick="location.href='manageState.php'; return false;" /></td>
	</tr>
	</table>
	</form>
</div>
</td></tr>
</table>
<script type="text/JavaScript">
<!--
function validate()
{
	var stateName=document.frm1.stateName.value=trimspace(document.frm1.stateName.value);
	if(stateName == '')
	{
		alert('Please enter the State/Province Name.');
		document.frm1.stateName.focus();
		return false;
	}
}
//-->
</script>
<?php 
include 'bottom.php';
?>
