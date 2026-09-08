<?php
$ALLOW_GROUPS = array(41);
require_once("../auth_auth.php");
include 'top.php';
?>
<table width='100%' border='0' cellspacing='0' cellpadding='5' class='text' rules='none' bordercolor='#B9B9B9' align='center' style='border-collapse:collapse'>
<tr><td class="adminhead" align='center' colspan='4'>ONLINE PRODUCT OBSERVATIONS</td></tr>
<!-- search and right buttons start-->
<tr>
<td colspan='3'>
<table border='0' width='100%' cellspacing="0" cellpadding="0">
<tr valign='top'>
<td align='right' colspan='2'>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="text">
<form method='post' name='frm1'>
<tr>
<td><b>Note</b>: Click any of the following to manage the online product.</td>
<td align='right' width="10%"><input class='button' style='width:60px' type='submit' name='submit1' ID='delBt' value='Delete' onclick=' return confirmDel()'></td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>
<!-- search and right buttons close-->
<?php
$message='';
if(isset($_REQUEST['delID']) && ($_REQUEST['delID'] > 0)) {
	$delID = $_REQUEST['delID'];
	if (!is_array($delID)){
		$delID = array($delID);
	}
	for($i=0;$i<count($delID);$i++){
		$delThis = $delID[$i];
		$sql = "SELECT local_path FROM cscan_observation WHERE observationID = '$delThis'";
		$res = $DRW->query($sql,$DRW_read);
		if ($DRW->num_rows($res) > 0) {
			list($delete_path) = $DRW->fetch_array($res);
			$current_path = getcwd();
			$delete_path_arr = explode("/", $delete_path);
			if(count($delete_path_arr)>=4){
				$real_delete_path = $delete_path_arr[0]."/".$delete_path_arr[1]."/".$delete_path_arr[2]."/".$delete_path_arr[3];
				system("rm -rf $current_path/../$real_delete_path");
			}
			$DRW->query("DELETE FROM cscan_observation WHERE observationID = '$delThis'",$DRW_main);
		}
	}
	if($i > 0) {
		$message="<b>$i</b> Observation(s) has been deleted.";
	}
} 
elseif (isset($_REQUEST['error']) && ($_REQUEST['error'] > 0)) {
	$errorObs = $_REQUEST['error'];
	$DRW->query("UPDATE cscan_observation SET status=0 WHERE observationID = '$errorObs'",$DRW_main);
	$message="Observation error has been reported.";
}
elseif (isset($_REQUEST['added']) && ($_REQUEST['added'] == 1)) {
	$message = "Observation saved.";
}
$sql = "SELECT observationID, DATE_FORMAT(date_observed,'%m/%d/%Y - %h:%i %p') AS date_observed, simple_domain FROM cscan_observation WHERE status=1 ORDER BY observationID DESC";
$rs = $DRW->query($sql,$DRW_read);
$resultCount = $DRW->num_rows($rs);
?>
<tr>
<td width='1%' class="adminhead" height='15px'><b><input type='checkbox' name='setUnset' onclick='setAll()'></td>
<td width='20%' class="adminhead" height='15px' ><b>Site Name</td>
<td width='79%' class="adminhead" height='15px' ><b>Date Captured</td>
</tr>
<tr><td colspan='3' align='center' class='error'><?php echo $message; ?></td></tr>
<?php
if($resultCount > 0) {
	$className='';
	while($row = $DRW->fetch_array($rs)){
		$ID 			= $row['observationID'];
		$observationDomain 	= $row['simple_domain'];
		$dateCaptured		= $row['date_observed'];
		if ($className=='selected-bg') $className='white-bg';
		else $className='selected-bg';
		?> 
		<tr valign=top class="<?php echo $className; ?>"><td><input type='checkbox' name='delID[]' value='<?php echo $ID; ?>'></td>
		<td><a class='hlinks' href='ad_observation_edit.php?oid=<?php echo $ID; ?>' title='Click here to edit.'><b><?php echo $observationDomain; ?></b></a></td>
		<td><?php echo $dateCaptured; ?></td>
		</tr>
		<?php
	}
	echo "<input type='hidden' name='submit' value='1'></form>";
}
else{
	echo "<tr><td colspan=3 class='error' align=center>No observations found.</td></tr>";
	echo "<script>el = document.getElementById('delBt'); el.style.display='none';</script>";
}
?>
</table>
<script type="text/JavaScript">
<!--
function confirmDel()
{
  goAheadFlag = 0;
  for(i=0;i<document.frm1.elements.length;i++)
  {
    if(document.frm1.elements[i].checked == true)
    {
      goAheadFlag = 1;
    }
  }
  if(goAheadFlag)
  {
    if(confirm("Are you sure to delete ?"))
    {
      return true;
    }
    else
    {
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
    for(i=1;i<document.frm1.elements.length;i++)
    {
      document.frm1.elements[i].checked = true;
    }
    document.frm1.setUnset.value = '';
  }
  else
  {
    for(i=1;i<document.frm1.elements.length;i++)
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