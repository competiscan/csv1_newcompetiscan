<?php
$ALLOW_GROUPS = array(44);
require_once("../auth_auth.php");
include 'top.php';
set_time_limit(0);
ini_set( "memory_limit", "-1" );
require_once('../includes/functions.php');
require_once('../includes/dashboardData.php');

$DASH = new dashboardData();
if(isset($_REQUEST['del'])){
	$fid = (int)$_REQUEST['del'];
	
	$DASH->remove_dashboard_import($fid);
	$sql = "UPDATE cscan_import_file SET import_file_inactive=1 WHERE import_file_id=".$fid;
	$DRW->query($sql,$DRW_main);
	
	ob_end_clean();
	header("Location: {$_SERVER['PHP_SELF']}");
	exit;
}
?>
<table width="100%" border="0" cellspacing="0" cellpadding="5" class="text">
<tr><td class="adminhead" align="center">COMPETISCAN DASHBOARD MANAGEMENT</td></tr>
</table>
<?php 
if(isset($_REQUEST['fid'])){
	$fid = (int)$_REQUEST['fid'];
	if(isset($_POST['send'])){
		$file = '';
		$fid_filename = '';
		$fid_content_type = '';
		$fid_content_length = 0;
		$ferrors = '';
		
		if($_FILES["fidFile"]['error'] == UPLOAD_ERR_OK && is_uploaded_file($_FILES["fidFile"]['tmp_name'])){
			$file = $_FILES['fidFile']['tmp_name'];
			$fid_filename = $_FILES['fidFile']['name'];
			$fid_content_type = $_FILES['fidFile']['type'];
			$fid_content_length = $_FILES['fidFile']['size'];
		}
		
		if($file!=''){
			$sql = "INSERT INTO cscan_import_file (import_file_date,import_file_table,import_file_name,admin_userID) VALUES (NOW(),'".$DASH->map_table."','".$DRW->real_escape_string($fid_filename)."','".$DRW->real_escape_string($AUTH_DATA['userID'])."')";
			$DRW->query($sql,$DRW_main);
			$fid = $DRW->insert_id($DRW_main);
			
			$labelMatch = array();
			foreach($DASH->maps as $match=>$map){
				$label = strtolower(preg_replace('/[^a-zA-Z0-9]+/','',$DASH->maps[$match]['label']));
				$labelMatch[$label] = $match;
			}
			
			require_once 'delimitedFile.php';
			$df = new delimitedFile($file);
			$df->setIncludeHeadings();
			$headings = array();
			$row = 0;
			while($contents = $df->getFile()){
				if(count($contents)>0){
					$row++;
					if(count($headings)==0){
						foreach($contents as $content){
							$label = strtolower(preg_replace('/[^a-zA-Z0-9]+/','',$content));
							if(isset($labelMatch[$label])){
								$label = $labelMatch[$label];
							}
							$headings[] = $label;
						}
					}
					else{
						$empty_row = true;
						$data = array();
						foreach($headings as $k=>$head){
							$stringed = trim((string) $contents[$k]);
							if($stringed!=''){
								$empty_row = false;
							}
							$data[$head] = $stringed;
						}
						if($empty_row){
							continue;
						}
						if(empty($data['date'])){
							$ferrors .= "Row $row missing: ".$DASH->maps['date']['label']."\n";
							$data['date'] = date('Y-m-d');
						}
						foreach($DASH->maps as $match=>$map){
							if(!isset($data[$match]) && $DASH->maps[$match]['pk']){
								$ferrors .= "Row $row missing: ".$DASH->maps[$match]['label']."\n";
								$data[$match] = '';
							}
							$dbvalue = $DASH->database_value($data[$match],$match);
							if(!empty($data[$match]) && empty($dbvalue) && $DASH->maps[$match]['type']=='list'){
								$ferrors .= "Row $row no match: ".$DASH->maps[$match]['label']." = ".$data[$match]."\n";
							}
						}
						$DASH->add_dashboard_data($data,$fid);
						
						$sql = "UPDATE cscan_import_file SET import_file_errors='".$DRW->real_escape_string($ferrors)."' WHERE import_file_id=".$fid;
						$DRW->query($sql,$DRW_main);
					}
				}
			}
		}
		
		ob_end_clean();
		header("Location: {$_SERVER['PHP_SELF']}");
		exit;
	}
	?>
	<form method="post" name="frm1" action="<?php echo $_SERVER['PHP_SELF'].'?fid='.$fid; ?>" enctype="multipart/form-data">
	<table border="0" cellspacing="0" cellpadding="5" class="text">
	<tr>
	<td class="adminhead" valign="top">File</td>
	<td class="bodytext"><input type="file" class="input_box" name="fidFile" size="40" /></td>
	</tr>
	<tr>
	<td class="adminhead">&nbsp;</td>
	<td><input class="button" type="submit" name="submit1" value="Import" /> &nbsp; <input class="button" type="submit" name="submit2" value="Cancel" onclick="document.location.href='<?php echo $_SERVER['PHP_SELF']; ?>'; return false;" /></td>
	</tr>
	</table>
	<div>&nbsp;</div>
	<div class="bodytext"><strong>Possible Field Headings:</strong></div>
	<table border="0" cellspacing="0" cellpadding="5" class="text">
	<?php 
	$className = '';
	foreach($DASH->maps as $match=>$map){
		if($map['type']=='list' && $map['search_type']!='lookup'){
			$info = implode(', ',$DASH->get_list_array($match));
		}
		else{
			$info = '('.$map['type'].')';
		}
		if ($className=='selected-bg') {
			$className='white-bg';
		}
		else {
			$className='selected-bg';
		}
		echo '<tr class="'.$className.'"><td class="bodytext" valign="top" width="30%">'.$map['label'].'</td><td class="bodytext" valign="top">'.$info.'</td></tr>';
	}
	?>
	</table>
	<input type="hidden" name="send" value="1" /></form>
	<?php 
}
else {
	?>
	<table border="0" cellspacing="0" cellpadding="5" class="text">
	<tr>
	<td colspan="5">&nbsp;</td>
	<td align="right"><input class="button" type="submit" name="submit2" value="Add" onclick="document.location.href='<?php echo $_SERVER['PHP_SELF']; ?>?fid=0'; return false;" /></td>
	</tr>
	<tr>
	<td class="adminhead">Date</td>
	<td class="adminhead">File</td>
	<td class="adminhead">Log</td>
	<td class="adminhead">User</td>
	<td class="adminhead">Active</td>
	<td class="adminhead">&nbsp;</td>
	</tr>
	<?php
	$className = '';
	$sql = "SELECT import_file_id,DATE_FORMAT(import_file_date,'%m/%d/%Y %l:%i %p'),import_file_name,import_file_inactive,admin_userID,import_file_errors FROM cscan_import_file WHERE import_file_table='".$DASH->map_table."' ORDER BY import_file_date DESC";
	$rs = $DRW->query($sql,$DRW_read);
	while($row = $DRW->fetch_row($rs)) {
		$import_file_id = $row[0];
		$import_file_date = $row[1];
		$import_file_name = $row[2];
		$import_file_inactive = $row[3];
		if($import_file_inactive){
			$active = 'No';
		}
		else{
			$active = 'Yes';
		}
		$admin_userID = $row[4];
		$import_file_errors = $row[5];
		if($import_file_inactive) {
			$import_file_errors_html = '';
		}
		else{
			$import_file_errors_html = nl2br(htmlspecialchars($import_file_errors));
		}
		
		$sql2 = "select userName from cscan_admin_users WHERE userID=$admin_userID";
		$rs2 = $DRW->query($sql2,$DRW_read);
		$row2 = $DRW->fetch_row($rs2);
		$adminusername = $row2[0];
		
		if ($className=='selected-bg') {
			$className='white-bg';
		}
		else {
			$className='selected-bg';
		}
		?>
		<tr class="<?php echo $className;?>">
		<td class="bodytext" valign="top"><?php echo $import_file_date; ?></td>
		<td class="bodytext" valign="top"><?php echo htmlspecialchars($import_file_name); ?></td>
		<td class="bodytext" valign="top"><?php echo $import_file_errors_html; ?></td>
		<td class="bodytext" valign="top"><?php echo htmlspecialchars($adminusername); ?></td>
		<td class="bodytext" valign="top"><?php echo htmlspecialchars($active); ?></td>
		<td class="bodytext" valign="top" align="right"><?php 
		if(!$import_file_inactive) {
                    if(checkGroup(74)){
			echo "<a href=\"{$_SERVER['PHP_SELF']}?del=$import_file_id\" onclick=\"return confirm('Remove Data?');\">Remove Data</a>";
                    }
		}
		else{
			echo '&nbsp;';
		}
		?></td>
		</tr>
		<?php
	}
	?>
	</table>
	<?php 
}
include 'bottom.php';
?>