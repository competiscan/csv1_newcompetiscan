<?php 
$ALLOW_GROUPS = array(41);
require_once("../auth_auth.php");
require_once '../includes/functions.php';
include 'top.php';

if(isset($_REQUEST['siteID'])) {
	$siteID = $_REQUEST['siteID'];
	$count = count($siteID);
	$sites = implode(',',$siteID);
	$query="UPDATE cscan_sites SET sites_active=0";
	$DRW->query($query,$DRW_main);
	$query="UPDATE cscan_sites SET sites_active=1 WHERE sites_id IN ($sites)";
	$DRW->query($query,$DRW_main);
	
	ob_end_clean();
	header("Location: {$_SERVER['PHP_SELF']}?updated=1");
	exit;
}
if(isset($_REQUEST['sid'])){
	$updID = (int)$_REQUEST['sid'];
	$sites_name = '';
	$sites_url = '';
	$sites_active = 0;
	$sites_category_id = 0;
	
	if(isset($_POST['send'])){
		$sites_name = trim($_POST['sites_name']);
		$sites_url = trim($_POST['sites_url']);
		$sites_url = preg_replace('/^http:\\/\\//i','',$sites_url);
		$sites_category_id = (int)$_POST['sites_category_id'];
		if(isset($_POST['sites_active'])) $sites_active = (int)$_POST['sites_active'];
		
		if($updID == 0) {
			$sql = "INSERT INTO cscan_sites (sites_name,sites_url,sites_active,sites_category_id) VALUES ('".$DRW->real_escape_string($sites_name)."','".$DRW->real_escape_string($sites_url)."',$sites_active,$sites_category_id)";
			$DRW->query($sql,$DRW_main);
		}
		else {
			$sql = "UPDATE cscan_sites SET sites_name='".$DRW->real_escape_string($sites_name)."',sites_url='".$DRW->real_escape_string($sites_url)."',sites_active=$sites_active,sites_category_id=$sites_category_id WHERE sites_id=$updID";
			$DRW->query($sql,$DRW_main);
		}
		if($_POST['submit1'] == 'Save & Add More'){
			ob_end_clean();
			header("Location: {$_SERVER['PHP_SELF']}?sid=0");
			exit;
		}
		else{
			ob_end_clean();
			header("Location: {$_SERVER['PHP_SELF']}");
			exit;
		}
	}
	elseif(!empty($updID)){
		$sql = "SELECT sites_name,sites_url,sites_active,sites_category_id FROM cscan_sites WHERE sites_id=$updID";
		$rs = $DRW->query($sql,$DRW_read);
		$row = $DRW->fetch_row($rs);
		$sites_name = $row[0];
		$sites_url = $row[1];
		$sites_active = $row[2];
		$sites_category_id = $row[3];
	}
?>
	<table width="100%" border="0" cellspacing="0" cellpadding="5" class="text">
	<tr><td class="adminhead" align="center" colspan="2"><?php if($updID!=0) echo 'UPDATE'; else echo 'ADD';?> SITE</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
	<td align="center">
	<form method="post" name="frm1" action="<?php echo $_SERVER['PHP_SELF']."?sid=$updID"; ?>">
	<table width="60%" border="0" cellspacing="0" cellpadding="0"><tr><td style="border:solid 1px #14734F;">
	<table width="100%" border="0" cellspacing="0" cellpadding="4">
	<tr>
	<td class="subhead" align="center" colspan="2">
	<?php 
	if($updID!=0) echo 'UPDATE';
	else echo 'ADD';
	?> SITE
	</td>
	</tr>
	<tr>
	<td class="bodytext" align="right">Site Name:</td>
	<td><input type="text" name="sites_name" size="30" class="combo_box" maxlength="255" value="<?php echo htmlspecialchars($sites_name,ENT_QUOTES); ?>" /></td>
	</tr>
	<tr>
	<td class="bodytext" align="right">Site URL:</td>
	<td><input type="text" name="sites_url" size="30" class="combo_box" maxlength="255" value="<?php echo htmlspecialchars($sites_url,ENT_QUOTES); ?>" /></td>
	</tr>
	<tr>
	<td class="bodytext" align="right">Site Category:</td>
	<td><select name="sites_category_id" class="combo_box" size="1"><option value="0">&nbsp;</option><?php 
	$z = $DRW->query("SELECT sites_category_id,sites_category_name FROM cscan_sites_category ORDER BY sites_category_name",$DRW_read);
	while($z && $zz = $DRW->fetch_assoc($z)) {
		echo '<option value="'.$zz['sites_category_id'].'"'.(($zz['sites_category_id']==$sites_category_id)?' selected="selected"':'').'>'.$zz['sites_category_name'].'</option>';
	}
	?></select></td>
	</tr>
	<tr>
	<td class="bodytext" align="right"><label for="sites_active">Active:</label></td>
	<td><input type="checkbox" name="sites_active" id="sites_active" value="1" <?php if($sites_active==1) echo ' checked="checked"'; ?> /></td>
	</tr>
	<tr><td colspan="2">&nbsp;</td></tr>
	<tr>
	<td>&nbsp;</td>
	<td>
	<?php 
	if($updID == 0){
	?>
	<input class="button" type="submit" name="submit1" value="Save" onclick="return validate();" />
	<input class="button" type="submit" name="submit" value="Save &amp; Add More" style="width:120px;" onclick="return validate();" />
	<?php }else{ ?>
	<input class="button" type="submit" name="submit1" value="Update" onclick="return validate();" />
	<?php }?>
	<input class="button" type="button" value="Cancel" onclick="location.href='<?php echo $_SERVER['PHP_SELF']; ?>'; return false;" />
	</td>
	</tr>
	</table>
	</td></tr></table>
	<input type="hidden" name="send" value="1" /></form>
	</td>
	</tr>
	</table>
<?php
}
else{
	?>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="deleteform">
	<table width="100%" border="0" cellspacing="0" cellpadding="5" class="text">
	<tr><td class="adminhead" align="center" colspan="5">SITE MANAGEMENT</td></tr>
	<tr><td colspan="5"><input class="button" name="activate" value="Active/Inactive" type="submit" /><?php 
	if(isset($_REQUEST['updated'])){
		echo ' &nbsp; <span class="error">Updated</span>';
	}
	?></td></tr>
	<tr><td width="5%" class="adminhead">&nbsp;</td><td class="adminhead">Site</td><td class="adminhead">URL</td><td class="adminhead">Category</td><td class="adminhead">Products</td></tr>
	<?php 
	$className='';
	$stotala = 0;
	$stotali = 0;
	$sctotal = 0;
	$sql = "SELECT sites_name,sites_category_name,sites_id,sites_url,sites_active FROM cscan_sites ss LEFT JOIN cscan_sites_category sc USING(sites_category_id) ORDER BY sites_active desc,sites_name,sites_category_name";
	$rs = $DRW->query($sql,$DRW_read);
	while($row = $DRW->fetch_row($rs)) {
		if($className=='selected-bg') $className='white-bg';
		else $className='selected-bg';
		
		$url = $row[3];
		if(empty($url)){
			$sql2 = "SELECT sp_url FROM cscan_sites_product WHERE sites_id=$row[2] limit 1";
			$rs2 = $DRW->query($sql2,$DRW_read);
			$row2 = $DRW->fetch_row($rs2);
			$url = $row2[0];
		}
		if($row[4] && !empty($url)){
			$url = '<a href="http://'.$url.'" target="_blank">'.$url.'</a>';
		}
		
		$sql2 = "SELECT count(*) FROM cscan_sites_product WHERE sites_id=$row[2]";
		$rs2 = $DRW->query($sql2,$DRW_read);
		$row2 = $DRW->fetch_row($rs2);
		$scount = $row2[0];
		$sctotal += $scount;
		
		if($row[4]){
			$stotala++;
		}
		else{
			$stotali++;
		}
		
		echo '<tr><td class="'.$className.'"><input type="checkbox" name="siteID[]" value="'.$row[2].'"';
		if($row[4]){
			echo ' checked="checked"';
		}
		echo ' /></td><td class="'.$className.'"><a href="'.$_SERVER['PHP_SELF'].'?sid='.$row[2].'">'.$row[0].'</a></td><td class="'.$className.'">'.$url.'</td><td class="'.$className.'">'.$row[1].'</td><td class="'.$className.'">'.number_format($scount).'</td></tr>';
	}
	
	echo '<tr><td colspan="5"><hr /></td></tr><tr><td colspan="4"><strong>Active Total: '.number_format($stotala).' &nbsp; Inactive Total: '.number_format($stotali).'</strong></td><td><strong>'.number_format($sctotal).'</strong></td></tr>';
	
	?>
	</table>
	</form>
	<?php 
}
include 'bottom.php';
?>