<?php
$ALLOW_GROUPS = array(14);
require_once("../auth_auth.php");
include 'top.php'; 
?>

<form method="post" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<table width="100%" border="0" cellspacing="0" cellpadding="5" class="text">
<tr><td class="adminhead" align="center" colspan="5">STATE/PROVINCE MANAGEMENT</td></tr>
<!-- search and right buttons start-->
<tr>
<td colspan="5">
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="text">
<tr>
<td><strong>Note:</strong> Click any of the following to modify the State/Province name.</td>
<td align="right"><input class="button" style="width:80px" type="button" value="Add" onclick="location.href='addState.php'; return false;" disabled="disabled"/></td>
<td align="right" width="10%"><?php if(checkGroup(66)){?><input class="button" style="width:60px" type="submit" name="submit1" ID="delBt" value="Delete" onclick="return confirmDel();" disabled="disabled"/><?php }?></td>
</tr>
</table>
</td>
</tr>
<?php
$message='';
if(isset($_POST['submit'])) {
    $delID = $_POST['delID'];
    $count = count($delID);
    $delThis = implode(',',$delID);
    $sql = "DELETE FROM cscan_state WHERE stateID IN ($delThis) AND stateID<>99";
    $emailData = [];
    if($DRW->query($sql,$DRW_main)){        
        foreach($delID as $id){
            $data = [
                'auth_id' => $GLOBALS['AUTH_DATA']['userID'],
                'auth_name' => authUserName($GLOBALS['AUTH_DATA']['userID']),
                'deleted_id' => $id,
                'sql_query' => $sql,
                'ip_address' => ipAddress(),
                'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                'delete_type' => 'States/Provinces',
                'is_mobile' => isMobile(),
                'insert_date' => date("Y-m-d H:i:s")
            ];
            trackDelete($data);
            $emailData[] = $data;
        }    
        if(count($emailData)>0){
            $html = '<table width="100%" border="1">';
            $html .= '<tr><td>Auth ID</td><td>User Name</td><td>Deleted ID</td><td>SQL Query</td><td>IP Address</td><td>User Agent</td><td>Module</td><td>Is Mobile</td><td>Deleted Date (CST)</td></tr>';

            foreach($emailData as $tr){
                if(is_array($tr) && count($tr)>0){
                   $html .= '<tr>';
                   foreach($tr as $td){
                       $html .= '<td>'.$td.'</td>'; 
                   }
                   $html .= '</tr>';
                }
            }                    
            $html .= '</table>';

            sendDevAlert('Caution! Data Deleted From States/Provinces',$html);
        }
    }

    if($count > 0) {
            $message="<strong>$count</strong> State/Province Name(s) has been deleted.";
    }
}
$sql = "SELECT * FROM cscan_state ORDER BY countryCode,stateName";
$rs = $DRW->query($sql,$DRW_read);
$resultCount = $DRW->num_rows($rs);
?>
<tr>
<td width="1%" class="adminhead" height="15"><?php if(checkGroup(66)){?><input type="checkbox" name="setUnset" onclick="setAll();" /><?php }?></td>
<td width="40%" class="adminhead" height="15"><strong>State/Province Name</strong></td>
<td width="20%" class="adminhead" height="15"><strong>State/Province Code</strong></td>
<td width="20%" class="adminhead" height="15"><strong>Country</strong></td>
<td width="19%" class="adminhead" height="15"><strong>Panelist Code</strong></td>
</tr>
<tr>
<td colspan="5" align="center" class="error">
<?php echo $message; ?>
</td>
</tr>
<?php
if($resultCount > 0) {
	$className='';
	while($row = $DRW->fetch_array($rs)) {
		$ID = $row['stateID'];
		$stateName = $row['stateName'];
		$stateCode = $row['stateCode'];
		$panelist_stateID = $row['panelist_stateID'];
		$countryCode = $row['countryCode'];
		if ($className=='selected-bg') $className='white-bg';
		else $className='selected-bg';
		?>
                <tr valign="top" class="<?php echo $className ;?>"><td><?php if(checkGroup(66)){?><input type="checkbox" name="delID[]" value="<?php echo $ID; ?>" /><?php }?></td>
		<td><a class="hlinks" href="addState.php?id=<?php echo $ID; ?>" title="Click here to edit."><strong><?php echo htmlspecialchars($stateName); ?></strong></a></td>
		<td><a class="hlinks" href="addState.php?id=<?php echo $ID; ?>" title="Click here to edit."><?php echo htmlspecialchars($stateCode); ?></a></td>
		<td><a class="hlinks" href="addState.php?id=<?php echo $ID; ?>" title="Click here to edit."><?php echo htmlspecialchars($countryCode); ?></a></td>
		<td><a class="hlinks" href="addState.php?id=<?php echo $ID; ?>" title="Click here to edit."><?php echo htmlspecialchars($panelist_stateID); ?></a></td>
		</tr>
		<?php
	}
}
else {
	echo "<tr><td colspan=\"5\" class=\"error\" align=\"center\">No State/Province found.</td></tr>";
	echo "<script type=\"text/javascript\">
	<!--
	var el = document.getElementById('delBt');
	el.style.display='none';
	//-->
	</script>";
}
?>
</table>
<input type='hidden' name='submit' value='1'></form>
<script type="text/JavaScript">
<!--
function confirmDel()
{
	var goAheadFlag = 0;
	for(var i=0;i<document.frm1.elements.length;i++)
	{
		if(document.frm1.elements[i].checked == true) {
			goAheadFlag = 1;
		}
	}
	if(goAheadFlag)
	{
		if(confirm("Are you sure to delete ?")) {
			return true;
		}
		else {
			return false;
		}
	}
	else
	{
		alert('Please select at least one record to delete !!!');
		return false;
	}
}

function setAll()
{
	if(document.frm1.setUnset.value == 'on')
	{
		for(var i=1;i<document.frm1.elements.length;i++)
		{
			if(document.frm1.elements[i].disabled == false)
			{
				document.frm1.elements[i].checked = true;
			}
		}
		document.frm1.setUnset.value = '';
	}
	else
	{
		for(var i=1;i<document.frm1.elements.length;i++)
		{
			document.frm1.elements[i].checked = false;
		}
		document.frm1.setUnset.value = 'on';
	}
}
//-->
</script>
<?php 
include 'bottom.php';
?>