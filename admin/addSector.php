<?php 
$ALLOW_GROUPS = array(7);
require_once("../auth_auth.php");
include 'top.php';
require_once '../includes/functions.php';

if(isset($_REQUEST['id'])) $updID = (int)$_REQUEST['id'];
else $updID = 0;
if(isset($_REQUEST['pid'])) $pid = (int)$_REQUEST['pid'];
else $pid = 0;
if(isset($_REQUEST['level'])) $level = (int)$_REQUEST['level'];
else $level = 0;
?>
<table width="100%" border="0" cellspacing="0" cellpadding="5" class="text">
<tr><td class="adminhead" align="center" colspan="2">SECTOR MANAGEMENT</td></tr>
<tr><td colspan="2" align="right" class="bodytext"><strong><span class="error">* required field</span></strong></td></tr>
<?php
if(isset($_POST['submit'])) {
	if(isset($_POST['sectorName'])) $sectorName = $DRW->real_escape_string($_POST['sectorName']);
	else $sectorName = '';
	if(isset($_POST['sectorSearchActive'])) $sectorSearchActive = (int)$_POST['sectorSearchActive'];
	else $sectorSearchActive = '0';
	if(isset($_POST['sectorWorksiteVoluntary'])) $sectorWorksiteVoluntary = (int)$_POST['sectorWorksiteVoluntary'];
	else $sectorWorksiteVoluntary = '0';
	
	$sectorText = '';
	if($sectorName!='') {
		if($sectorText!='') $sectorText .= ' OR ';
		$sectorText .= "(sectorName='$sectorName' AND parentID=$pid)";
	}
	if($sectorText=='') $sectorText = '1=1';
	$chk = "SELECT sectorName FROM cscan_sector WHERE ($sectorText) AND sectorID<>$updID";
	$rs = $DRW->query($chk,$DRW_read);
	if($DRW->num_rows($rs) == 0) {
		if($updID == 0) {
			$sql = "INSERT INTO cscan_sector SET sectorName='$sectorName',parentID=$pid,sectorSearchActive=$sectorSearchActive,sectorWorksiteVoluntary=$sectorWorksiteVoluntary";
			$actMsg = 'added';
			$DRW->query($sql,$DRW_main);
		}
		else {
			$sql = "UPDATE cscan_sector SET sectorName='$sectorName',parentID=$pid,sectorSearchActive=$sectorSearchActive,sectorWorksiteVoluntary=$sectorWorksiteVoluntary WHERE sectorID=$updID";
			$actMsg = 'updated';
			$DRW->query($sql,$DRW_main);
		}
		echo "<tr><td align=center colspan='2'>New sector has been $actMsg sucessfully.</td></tr>";
		if($_POST['submit'] == 'Save & Add More') {
			ob_end_clean();
			header("Location: addSector.php");
			exit;
		}
		else{
			ob_end_clean();
			header("Location: managesector.php");
			exit;
		}
	}
	else {
		echo "<tr><td class=\"error\" align=\"center\" colspan=\"2\">Name already exists.</td></tr>";
	}
}
else {
	if($updID != 0) {
		$sql = "SELECT * FROM cscan_sector WHERE sectorID=$updID";
		$editRS = $DRW->query($sql,$DRW_read);
		$editRS = $DRW->fetch_array($editRS);
		$sectorName = $editRS['sectorName'];
		$sectorSearchActive = $editRS['sectorSearchActive'];
		$sectorWorksiteVoluntary = $editRS['sectorWorksiteVoluntary'];
		$pid = $editRS['parentID'];
	}
	else{
		$sectorName = '';
		$sectorSearchActive = 1;
		$sectorWorksiteVoluntary = 0;
	}
	switch($level){
		case 1:
			$type_name = 'Category';
			$parent_name = 'Sector';
			break;
		case 2:
			$type_name = 'Sub Category';
			$parent_name = 'Category';
			break;
		case 3:
			$type_name = 'Sub Sub Category';
			$parent_name = 'Sub Category';
			break;
		default:
			$type_name = 'Sector';
			$parent_name = 'Sector';
	}
	$parents = '';
	if(!empty($pid)){
		$p_id = $pid;
		for($i=0;$i<$level;$i++){
			$sql = "SELECT sectorName,parentID FROM cscan_sector WHERE sectorID=$p_id";
			$editRS = $DRW->query($sql,$DRW_read);
			$editRS = $DRW->fetch_array($editRS);
			$sname = $editRS['sectorName'];
			$p_id = $editRS['parentID'];
			if($parents!=''){
				$parents = ' / '  . $parents;
			}
			$parents = htmlspecialchars($sname) . $parents;
		}
	}
	?>
	<tr>
	<td align="center">
	<div style="border:solid 1px #14734F;width:60%;">
	<form method="post" name="frm1" onsubmit="return validate();" action="addSector.php">
	<table width="100%" border="0" cellspacing="0" cellpadding="4">
	<tr><td class="subhead" align="center" colspan="2"><?php
	if($updID!= 0) echo 'UPDATE';
	else echo 'ADD';
	?> SECTOR</td></tr>
	<tr><td colspan="2">&nbsp;</td></tr>
	<?php 
	if($parents!=''){
		echo '<tr><td class="bodytext" align="right">&nbsp;</td><td class="bodytext"><strong>'.$parents.'</strong></td></tr>';
	}
	if($updID==0){
		?>
		<tr>
		<td class="bodytext" align="right"><?php echo $type_name; ?>:</td>
		<td width="65%">
		<select class="combo_box" name="sectorID" onchange="location.href='addSector.php?pid='+this.value+'&amp;level=<?php echo $level+1; ?>'">
		<option value="0">New</option>
		<?php
		if($level<3){
			if($pid==0){
				$sector = getSector();
			}
			else{
				$sector = getCategory($pid);
			}
			foreach( $sector as $id=>$name ) {
				echo "<option value=\"$id\">".htmlspecialchars($name)."</option>";
			}
		}
		?>
		</select>
		</td>
		</tr>
		<?php 
	}
	?>
	<tr>
	<td class="bodytext" align="right"><?php echo $type_name; ?> Name:<span class="error">*</span></td>
	<td width="65%"><input type="text" name="sectorName" size="34" maxlength="255" class="input_box" value="<?php echo htmlspecialchars($sectorName,ENT_QUOTES); ?>" /></td>
	</tr>
	<tr>
	<td class="bodytext" align="right"><label for="sectorSearchActive">Power Search:</label></td>
	<td width="65%"><input type="checkbox" id="sectorSearchActive" name="sectorSearchActive" value="1"<?php if($sectorSearchActive) print ' checked="checked"'; ?> /></td>
	</tr>
	<tr>
	<td class="bodytext" align="right"><label for="sectorWorksiteVoluntary">Worksite/Voluntary:</label></td>
	<td width="65%"><input type="checkbox" id="sectorWorksiteVoluntary" name="sectorWorksiteVoluntary" value="1"<?php if($sectorWorksiteVoluntary) print ' checked="checked"'; ?> /></td>
	</tr>
	<?php 
	if($updID==0){
		?>
		<tr>
		<td class="bodytext" align="right">&nbsp;</td>
		<td class="bodytext"><em>Note: Admin and Client Profile permissions need to be updated after saving.</em></td>
		</tr>
		<?php 
	}
	?>
	<tr><td colspan="2">			 
	<table width="100%" border="0" cellspacing="0" cellpadding="4">
	<tr>
	<td>&nbsp;</td>
	<td width="70%">
	<?php
	if($updID==0){
		?>
		<input class="button" type="submit" name="submit" value="Save" onclick="return validate();" disabled="disabled"/>
		<input class="button" type="submit" name="submit" value="Save &amp; Add More" style="width:120px;" onclick="return validate();" disabled="disabled"/>
		<?php 
	} 
	else{ 
		?>
		<input class="button" type="submit" name="submit" value="Update" onclick="return validate();" disabled="disabled"/>
		<input type="hidden" name="id" value="<?php echo $updID; ?>" />
		<?php 
	}
	?>
	<input type="hidden" name="level" value="<?php echo $level; ?>" /><input type="hidden" name="pid" value="<?php echo $pid; ?>" /><input class="button" type="button" value="Cancel" onclick="location.href='managesector.php'; return false;">
	</td>
	</tr>
	</table>
	</td></tr>  
	</table>
	</form>
	</div>
	</td>
	</tr>
	<?php
}
?>
</table>
<script type="text/javascript">
<!--
function validate()
{
	var ele1 = MM_findObj('sectorName');
	
	if(ele1 && ele1.value =='' )
	{
		alert("Please Enter Name");
		return false;
	}
	
	return true;
}
//-->
</script>
<?php 
include 'bottom.php';
?>