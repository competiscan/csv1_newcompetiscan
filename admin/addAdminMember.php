<?php
$ALLOW_GROUPS = array(3);
require_once("../auth_auth.php");
include 'top.php'; 
require_once '../includes/functions.php';

if(isset($_POST['savep'])) {
	$userID = (int)$_POST['userID'];
	$sql = "DELETE FROM cscan_sector_admin_users_allow WHERE userID=$userID";
	$DRW->query($sql,$DRW_main);
			
	$sql = "SELECT sectorID FROM cscan_sector";
	$rs = $DRW->query($sql,$DRW_read);
	while($row = $DRW->fetch_array($rs)) {
		if(isset($_POST['sectorid_'.$row[0]]) && $_POST['sectorid_'.$row[0]]==1){
			$sql = "INSERT INTO cscan_sector_admin_users_allow (sectorID,userID) VALUES ($row[0],$userID)";
			$DRW->query($sql,$DRW_main);
		}
	}
	ob_end_clean();
	header("Location: addAdminMember.php?id=$userID&done=1#perms");
	exit;
}

$updID = '';
$page_heading = 'ADD NEW ADMIN MEMBER';
$page_message = 'Please fill following details to add new admin user';
$userName = '';
$email='';
$status=1;
$is_assign_queue = 0;
$is_email_assign_queue = 0;
$is_email_assign_queue2 = 0;
$a_number_machines = 1;
$a_bypass = 1;
$permission=array();
if(isset($_REQUEST['id']) && $_REQUEST['id']!='') {
	$updID = $_REQUEST['id'];
	$page_heading = 'UPDATE ADMIN MEMBER';
	$page_message = 'Please fill following details to update this member';

	$sql = "select userName, user_email ,user_status,is_assign_queue,a_number_machines,a_bypass,is_email_assign_queue,is_email_assign_queue2 from cscan_admin_users where userID='$updID' ";
	$result = $DRW->query( $sql,$DRW_read );

	$row = $DRW->fetch_row( $result );
	if(!empty($row[0])){
		$userName = $row[0];
		$email = $row[1];
		$status = $row[2];
		$is_assign_queue = $row[3];
		$a_number_machines = $row[4];
		$a_bypass = $row[5];
		$is_email_assign_queue = $row[6];
		$is_email_assign_queue2 = $row[7];
	}

	$sql = "select permissionID from cscan_admin_users au,cscan_user_permission up where au.userID='$updID' and au.userID=up.userID";
	$rs = $DRW->query($sql,$DRW_read);
	while($row = $DRW->fetch_array($rs)) {
		$permission[]=$row['permissionID'];
	}
}
?>
<script type="text/javascript" src="jquery.min.js"></script>
<table width="100%" border="0" cellspacing="0" cellpadding="5" class="text">
  <tr><td class="adminhead" align="center" colspan="2"><?php echo $page_heading; ?></td></tr>
  <tr>
    <td align="center">
      <table width="70%" border="0" cellspacing="0" cellpadding="5" class="text">
        <tr><td colspan="2" align="right"><strong><span class="error">* required field</span></strong></td></tr>
<?php
$actMsg = '';
$err_msg = '';
if( isset($_REQUEST['save'])) {
	$userName = $_REQUEST['loginName'];
	$password = $_REQUEST['password'];
	$email=$_REQUEST['email'];
	$status=$_REQUEST['status'];
	if(isset($_REQUEST['is_assign_queue'])) $is_assign_queue = (int)$_REQUEST['is_assign_queue'];
	else $is_assign_queue = 0;
	
	if(isset($_REQUEST['is_email_assign_queue'])) $is_email_assign_queue = (int)$_REQUEST['is_email_assign_queue'];
	else $is_email_assign_queue = 0;
	if(isset($_REQUEST['is_email_assign_queue2'])) $is_email_assign_queue2 = (int)$_REQUEST['is_email_assign_queue2'];
	else $is_email_assign_queue2 = 0;
	
	if($_POST['a_number_machines']<$a_number_machines || isset($_POST['resetcodes'])){
		$del = "DELETE FROM cscan_admin_user_code WHERE userID='$updID'";
		$DRW->query($del,$DRW_main);
	}
	$a_number_machines = (int)$_POST['a_number_machines'];
	
	if(isset($_REQUEST['a_bypass'])) $a_bypass = (int)$_REQUEST['a_bypass'];
	else $a_bypass = 0;

	$sql = "select userName from cscan_admin_users where userName='".$DRW->real_escape_string($userName)."' and userID<>'$updID'";

	$result = $DRW->query( $sql,$DRW_read );
	if( $DRW->num_rows($result) == 0 ) {
		if($password!='') $passwordtxt = "password=MD5('".$DRW->real_escape_string($password)."'),";
		else  $passwordtxt = '';
		
		if( $updID == '' ) {
			$sql1 = "insert into cscan_admin_users set userName='".$DRW->real_escape_string($userName)."',
                     $passwordtxt
                     user_email='".$DRW->real_escape_string($email)."',
                     user_status='".$DRW->real_escape_string($status)."',
					 is_assign_queue=$is_assign_queue,
					a_number_machines='$a_number_machines',
					a_bypass='$a_bypass',
					is_email_assign_queue=$is_email_assign_queue,
					is_email_assign_queue2=$is_email_assign_queue2";
			$DRW->query($sql1,$DRW_main);
			$updID=$DRW->insert_id($DRW_main);
			if(isset($_REQUEST['permission'])){
				foreach($_REQUEST['permission'] as $per) {
					$sql1 = "replace into cscan_user_permission set permissionID='".$DRW->real_escape_string($per)."', userID='".$DRW->real_escape_string($updID)."'";
					$DRW->query($sql1,$DRW_main);
				}
			}
			$actMsg = 'added';
		}
		else {
			$sql3 = "update  cscan_admin_users set userName='".$DRW->real_escape_string($userName)."',
                     $passwordtxt
                     user_email='".$DRW->real_escape_string($email)."',
                     user_status='".$DRW->real_escape_string($status)."',
					 is_assign_queue=$is_assign_queue,
					a_number_machines='$a_number_machines',
					a_bypass='$a_bypass',
					is_email_assign_queue=$is_email_assign_queue,
					is_email_assign_queue2=$is_email_assign_queue2 where userID = $updID "; 

			$DRW->query($sql3,$DRW_main);

			$sql = "DELETE FROM cscan_user_permission where  userID='$updID'";

			$DRW->query($sql,$DRW_main);
			if(isset($_REQUEST['permission'])){
				foreach($_REQUEST['permission'] as $per) {
					$sql = "insert into cscan_user_permission set permissionID='".$DRW->real_escape_string($per)."', userID='".$DRW->real_escape_string($updID)."'";
					$DRW->query($sql,$DRW_main);
				}
			}
			$actMsg = 'updated';
		}
?>
      <tr><td align="center" colspan="2">New admin user has been <?php echo $actMsg; ?> sucessfully.</td></tr>
<?php
if(isset($_REQUEST['saveAndAdd'])) {
	ob_end_clean();
	header("Location: addAdminMember.php?a=1");
	exit;
}
else {
	ob_end_clean();
	header("Location: manageAdminMember.php");
	exit;
}
	}
	else {
		$err_msg = 1;
?>
      <tr><td class="error" align="center" colspan="2">Login name already exist. <br />Please change the login name.</td></tr>
<?php
	}
}
?>
    <tr>
      <td align="center">
      <form method="post" name="userForm" onsubmit="return validate();" action="<?php print $_SERVER['PHP_SELF']; ?>">
        <table border="0" width='100%' cellpadding="5" cellspacing="0">
<?php 
if(isset($_REQUEST['a']) && $err_msg=='') {
?>
		<tr><td colspan="2" class="error" align="center">Admin user has been added successfully</td></tr>
<?php
}
?>
          <!-- Login Name -->
          <tr>
            <td width="40%" class="bodytext" align="right">Login Name<span class="error">*</span>:</td>
            <td><input type="text" name="loginName" size="40" maxlength="40" class="input_box" value="<?php echo htmlspecialchars($userName,ENT_QUOTES);?>" /></td>
          </tr>
          <!-- Password -->
          <tr>
            <td class="bodytext" align="right">Password:</td>
            <td class="bodytext"><input type="password" name="password" size="40" maxlength="40" class="input_box" id="newPassword"/>&nbsp;(Do not use: $;"')</td>
          </tr>
           <!-- Confirm Password -->
          <tr>
            <td class="bodytext" align="right">Confirm Password:</td>
            <td><input type="password" name="confirmpassword" size="40" maxlength="40" class="input_box" id="confpassword" /></td>
          </tr>
          <!-- Email -->
          <tr>
            <td class="bodytext" align="right">Email Address:</td>
            <td><input type="text" name="email" size="40" maxlength="40" class="input_box" value="<?php echo htmlspecialchars($email,ENT_QUOTES);?>" /></td>
          </tr>
          <!--Status-->
          <tr>
            <td class="bodytext" align="right">Status:</td>
            <td class="bodytext"><label><input type="radio" name="status" value="1" <?php if($status==1) print ' checked="checked"';?> />Active</label>
            <label><input type="radio" name="status" value="0" <?php if($status==0) print ' checked="checked"';?> />Inactive</label></td>
          </tr>
          
		  <tr>
			<td class="bodytext" align="right">Number of Machines:</td>
			<td><input type="text" name="a_number_machines" size="4" maxlength="4" class="input_box" value="<?php echo htmlspecialchars($a_number_machines,ENT_QUOTES);?>" /><?php 
			$count_save_sql = "SELECT COUNT(*) FROM cscan_admin_user_code where userID='$updID'";
			$rs = $DRW->query($count_save_sql,$DRW_read);
			$data = $DRW->fetch_row($rs);
			$codecount = (int) $data[0];
			print " <span class=\"bodytext\">($codecount in use)";
			if($codecount>0) print " &nbsp; <input type=\"checkbox\" name=\"resetcodes\" value=\"1\" />Reset Machines";
			print "</span>";
			?></td>
		  </tr>
		  <tr>
			<td class="bodytext" align="right">Bypass Security:</td>
			<td><input type="checkbox" name="a_bypass" value="1"<?php if($a_bypass==1) print ' checked="checked"'; ?> /></td>
		  </tr>
          
			<tr>
			<td class="bodytext" align="right" valign="top">Assignment Queue:</td><td class="bodytext">
			<label><input type="radio" name="is_assign_queue" value="0" <?php if($is_assign_queue==0) print ' checked="checked"'; ?> />None</label> &nbsp; <label><input type="radio" name="is_assign_queue" value="1" <?php if($is_assign_queue==1) print ' checked="checked"'; ?> />Core</label> &nbsp; <label><input type="radio" name="is_assign_queue" value="2" <?php if($is_assign_queue==2) print ' checked="checked"'; ?> />Non-core</label>
			</td>
			</tr>
			<tr>
			<td class="bodytext" align="right" valign="top">Producer Email Assignment Queue:</td><td class="bodytext">
			<label><input type="checkbox" name="is_email_assign_queue" value="1" <?php if($is_email_assign_queue==1) print ' checked="checked"'; ?> />Yes</label>
			</td>
			</tr>
			<tr>
			<td class="bodytext" align="right" valign="top">Consumer Email Assignment Queue:</td><td class="bodytext">
			<label><input type="checkbox" name="is_email_assign_queue2" value="1" <?php if($is_email_assign_queue2==1) print ' checked="checked"'; ?> />Yes</label>
			</td>
			</tr>
            <!--Permission-->
          <tr>
            <td class="bodytext" align="right" valign="top">Permissions:</td><td class="bodytext">
            <?php
            $permissionGrouping = -1;
            $permissionGroupingName = array(1=>'User Admin',2=>'Product Admin',3=>'Definitions');
            $sql = "SELECT permissionID, permissionName,permissionGrouping FROM cscan_permission ORDER BY permissionGrouping,permissionName";
            $rs = $DRW->query($sql,$DRW_read);
            $resultCount = $DRW->num_rows($rs);
            if( $resultCount > 0 ) {
            	while($row = $DRW->fetch_array($rs)) {
            		if($permissionGrouping!=$row['permissionGrouping'] && isset($permissionGroupingName[$row['permissionGrouping']])){
            			print "<strong>{$permissionGroupingName[$row['permissionGrouping']]}</strong><br />";
            			$permissionGrouping = $row['permissionGrouping'];
            		}
        			print " <label><input type=\"checkbox\" name=\"permission[]\" value=\"{$row['permissionID']}\"";
        			if(in_array($row['permissionID'],$permission)) print ' checked="checked"';
        			print " />{$row['permissionName']}</label><br />";
            	}
            }
            ?>
            </td>
              </tr>
          <!-- Button -->
          <tr>
<?php
if($updID == '') {
?>
              <td align="right"><input class="button" type="submit" name="save1" value="Save" onClick="return validate();" />&nbsp;</td>
              <td><input class="button" style="width:120px;" type="submit" name="saveAndAdd" value="Save &amp; Add More" onclick="return validate();" />
              <input class="button" type="reset" value="Reset" /></td>
<?php
}
else {
?>
              <td align="right"><input class="button" type="submit" name="save_button" value="Update" onclick="return validate();" />&nbsp;</td>
              <td><input type="hidden" name="id" value="<?php echo $updID; ?>" />
              <input class="button" type="button" value="Cancel" onclick="location.href='manageAdminMember.php'; return false;" /></td>
<?php
}
?>
          </tr>
        </table>
        <input type="hidden" name="save" value="1" /></form>
      </td>
    </tr>
</table>
<div align="left"><a name="perms" href="#">Top</a></div>
<table width="100%" border="0" cellspacing="0" cellpadding="5" class="text">
<tr><td class="adminhead" align="center">Sector/Category Permissions</td></tr>
</table>
<div align="left">
<form method="post" name="pForm" action="<?php print $_SERVER['PHP_SELF']; ?>">
<?php 
$sectorAllow = array();
$javascript = '';
if($updID != '') {
	$sql = "SELECT sectorID FROM cscan_sector_admin_users_allow WHERE userID=$updID";
	$rs = $DRW->query($sql,$DRW_read);
	while($row = $DRW->fetch_array($rs)) {
		$sectorAllow[] = $row[0];
	}
	getSects();
	?>
	<div>&nbsp;</div>
	<input class="button" type="submit" name="savep_button" value="Update Permissions" />
	<?php 
}
?>
<input type="hidden" name="userID" value="<?php echo $updID; ?>" />
<input type="hidden" name="savep" value="1" /></form>
</div>
      </td>
    </tr>
</table>
<script type="text/javascript">
<!--
function validate()
{
	var loginName = document.userForm.loginName.value = trimspace(document.userForm.loginName.value);
	var password = document.userForm.password.value = trimspace(document.userForm.password.value);
    var confirmpassword = document.userForm.confirmpassword.value = trimspace(document.userForm.confirmpassword.value);
   
	if( loginName == '' )
	{
		alert('Please enter the login name.');
		document.userForm.loginName.focus();
		return false;
	}
	 if((confirmpassword != password))
	{
		alert('Your confirm password field doesn\'t match with the password field');
		document.userForm.confirmpassword.focus();
		return false;
	}
	return true;
}
var pidArray = new Array();
var cidArray = new Array();
<?php echo $javascript; ?>
function checkParent(sid,pid){
	if(pid!=0){
		var obj1 = document.pForm['sectorid_'+sid];
		var obj2 = document.pForm['sectorid_'+pid];
		
		if(obj1.checked && !obj2.checked){
			obj2.checked = true;
		}
		checkParent(pid,pidArray[pid]);
	}
}
function checkChildren(sid,chex){
	//document.pForm['sectorid_'+sid].checked = true;
	if(cidArray[sid]){
		for(var i in cidArray[sid]){
			var obj = document.pForm['sectorid_'+cidArray[sid][i]];
			if(obj){
				obj.checked = chex;
				checkChildren(cidArray[sid][i],chex);
			}
		}
	}
}
function checkP_C(sid,pid){
	checkParent(sid,pid);
	var obj = document.pForm['sectorid_'+sid];
	if(cidArray[sid]){
		if(obj.checked){
			if(confirm('Select All?')){
				checkChildren(sid,true);
			}
		}
		else if(confirm('Remove All?')){
			checkChildren(sid,false);
		}
	}
}
//-->
</script>
<script>
  $("input#newPassword").keypress(
  function(event){
    if ((event.which == '36') || (event.which == '34') || (event.which == '39') || (event.which == '59')) {
        if(event.which == '36'){
            alert('Dollar($) key is not allowed!');
        }else if(event.which == '34'){
            alert('Double quotation(") key is not allowed!');
        }else if(event.which == '39'){
            alert("Apostrophe(') key is not allowed!");
        }else if(event.which == '59'){
            alert('Semicolon(;) key is not allowed!');
        }
        event.preventDefault();
    }
    
});

$("input#confpassword").keypress(
  function(event){
    if ((event.which == '36') || (event.which == '34') || (event.which == '39') || (event.which == '59')) {
        if(event.which == '36'){
            alert('Dollar($) key is not allowed!');
        }else if(event.which == '34'){
            alert('Double quotation(") key is not allowed!');
        }else if(event.which == '39'){
            alert("Apostrophe(') key is not allowed!");
        }else if(event.which == '59'){
            alert('Semicolon(;) key is not allowed!');
        }
        event.preventDefault();
    }
    
});
window.onload = () => {
 const myInput = document.getElementById('newPassword');
 const myInput1 = document.getElementById('confpassword');
 myInput.onpaste = e => e.preventDefault();
 myInput1.onpaste = e => e.preventDefault();
}
</script>
<?php
function getSects($parentID=0,$level=0){
	global $DRW,$DRW_read,$DRW_main,$DRW_crm;
	if($level<3){
		$sql = "SELECT sectorID,sectorName,sectorSearchActive FROM cscan_sector WHERE parentID=$parentID ORDER BY sectorName";
		$rs = $DRW->query($sql,$DRW_read,$DRW_read);
		$resultCount = $DRW->num_rows($rs);
		if($resultCount>0){
			$GLOBALS['javascript'] .= "cidArray[$parentID] = new Array();\n";
			while($row = $DRW->fetch_array($rs)) {
				$ID = $row['sectorID'];
				$sectorName = $row['sectorName'];
				$sectorSearchActive = $row['sectorSearchActive'];
				echo "<div>";
				for($i=0;$i<$level;$i++){
					echo ' &nbsp; &nbsp; ';
				}
				echo "<label><input type=\"checkbox\" name=\"sectorid_$ID\" value=\"1\" onclick=\"checkP_C($ID,$parentID);\"";
				if(in_array($ID,$GLOBALS['sectorAllow'])){
					echo ' checked="checked"';	
				}
				echo " />".htmlspecialchars($sectorName).'</label>';
				if(!$sectorSearchActive) echo ' <em>[non-search]</em>';
				echo '</div>';
				$GLOBALS['javascript'] .= "pidArray[$ID] = $parentID;\n";
				$GLOBALS['javascript'] .= "cidArray[$parentID][cidArray[$parentID].length] = $ID;\n";
				getSects($ID,$level+1);
			}
		}
	}
}
include 'bottom.php';
?>
