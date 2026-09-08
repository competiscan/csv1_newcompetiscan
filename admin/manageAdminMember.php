<?php
$ALLOW_GROUPS = array(3);
require_once("../auth_auth.php");
include 'top.php'; 
?>
<form method="post" name="memberForm" action="<?php print $_SERVER['PHP_SELF']; ?>">
<table width="100%" border="0" cellspacing="0" cellpadding="5" class="text">
  <tr><td class="adminhead" align="center" colspan="5">ADMIN MEMBER MANAGEMENT</td></tr>
  <!-- search and right buttons start-->
  <tr>
    <td colspan="5">
      <table width="100%" border="0" cellspacing="0" cellpadding="0" class="text">

		<tr><td colspan="3">&nbsp;</td></tr>
        <tr>
          <td width="75%"><strong>Note</strong>: Click on admin member name to modify the details.</td>
          <td align="right" width="15%"><input class="button" style="width:130px;" type="button" value="Add Member" onclick="location.href='addAdminMember.php'; return false;" /></td>
          <td align="right" width="10%">&nbsp;&nbsp;<input class="button" style="width:60px;" type="submit" name="submit1" ID="delBt" value="Block" onclick="return confirmDel();" /></td>
        </tr>
      </table>
    </td>
  </tr>
  <!-- search and right buttons close-->
  
<?php
	$message='';
	# Starting of block of code to delete users
	if(isset($_POST['submit']) && isset($_POST['delID'])) {
		$delID = $_POST['delID'];
		$delThis = implode(",",$delID);
		/*$sql = "DELETE FROM cscan_admin_users where userID IN ($delThis)";
		$DRW->query($sql,$DRW_main);
		$sql2 = "DELETE FROM cscan_user_permission where userID IN ($delThis)";
		$DRW->query($sql2,$DRW_main);
		*/
		$sql = "UPDATE cscan_admin_users SET user_status=0 where userID IN ($delThis)";
		$DRW->query($sql,$DRW_main);
	
		if(count($delID) > 0) {
			$message="<strong>".count($delID)." Admin Member(s) has been blocked.</strong>";
		}
	}
  # Ending of block of code to delete users
?>
  	
  <tr>
    <td width="5%" class="adminhead" height="15"><input type="checkbox" name="setUnset" onclick="setAll();" /></td>
    <td class="adminhead" height="15"><strong>Member Name</strong></td>
    <td class="adminhead" height="15"><strong>Status</strong></td>
    <td class="adminhead" height="15"><strong>Assign</strong></td>
    <td class="adminhead" height="15"><strong>User Permissions</strong></td>
  </tr>
  <tr><td colspan="5" align="center" class="error"> <?php echo $message ;?> </td></tr>
<?php
	$sql = "select userID, userName,userType,user_status,is_assign_queue,is_email_assign_queue,is_email_assign_queue2 from cscan_admin_users order by userName";
	$rs = $DRW->query($sql,$DRW_read);
	$resultCount = $DRW->num_rows($rs);
	$className = '';
	if( $resultCount > 0 ) {
		while($row = $DRW->fetch_array($rs)) {
			$ID = $row['userID'];
			$userName = $row['userName'];
			$user_status = $row['user_status'];
			$is_assign_queue = $row['is_assign_queue'];
			$is_email_assign_queue = $row['is_email_assign_queue']+$row['is_email_assign_queue2'];
			if($user_status==1) $user_status = 'Active';
			else $user_status = 'Inactive';
			$loginType="User"; 
			if ($className=='selected-bg') $className='white-bg';
			else $className='selected-bg';
?>
      <tr valign="top" class="<?php echo $className;?>">
        <td><input type="checkbox" name="delID[]" value="<?php echo $ID; ?>" /></td>
       <td><a class="hlinks" href="addAdminMember.php?id=<?php echo $ID; ?>" title="Click here to edit."><strong><?php echo $userName; ?></strong></a></td>
         <td><?php echo $user_status; ?></td>
         <td><?php 
	if($is_assign_queue) {
		echo 'Yes';
	}
	if($is_email_assign_queue>0) {
		if($is_assign_queue) {
			echo '/';
		}
		echo 'Email';
	}
	elseif(!$is_assign_queue) {
		echo '&nbsp;';
	}
         ?></td>
        <?php
        $sql2 = "select p.permissionID,p.permissionName from cscan_admin_users au,cscan_user_permission up, cscan_permission p where au.userID='$ID' and au.userID=up.userID and p.permissionID=up.permissionID order by p.permissionSort";
        $rs2 = $DRW->query($sql2,$DRW_read);
		$resultCount2 = $DRW->num_rows($rs2);
		print "<td>";
		while($row2 = $DRW->fetch_array($rs2)) {
			$resultCount2--;	
			print $row2[1];
			if($resultCount2!=0) print ", ";
		}
		print"</td>";
        ?>
        
      </tr>
<?php
		}
	}
	else {
    ?>
    <tr><td colspan="5" class="error" align="center">No user found.</td></tr>
    <script type="text/javascript">
	<!--
      var el = document.getElementById('delBt');
      el.style.display='none';
	//-->
    </script>
<?php
	}
?>
  <tr>
	<td colspan="5">&nbsp;</td>
  </tr>
</table>
<input type="hidden" name="submit" value="1" /></form>
<script type="text/javascript">
<!--
function confirmDel()
{
  var goAheadFlag = 0;
  for(i=0;i<document.memberForm.elements.length;i++)
  {
    if(document.memberForm.elements[i].checked == true)
    {
      goAheadFlag = 1;
    }
  }
  if(goAheadFlag)
  {
    if(confirm("Are you sure to block?"))
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
  if(document.memberForm.setUnset.value == 'on')
  {
    for(i=1;i<document.memberForm.elements.length;i++)
    {
      document.memberForm.elements[i].checked = true;
    }
    document.memberForm.setUnset.value = '';
  }
  else
  {
    for(i=1;i<document.memberForm.elements.length;i++)
    {
      document.memberForm.elements[i].checked = false;
    }
    document.memberForm.setUnset.value = 'on';
  }
}
//-->
</script>
<?php
include 'bottom.php';
?>
