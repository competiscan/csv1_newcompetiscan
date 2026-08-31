<?php 
$ALLOW_GROUPS = array(9);
require_once "../auth_auth.php";
include 'top.php';
?>
<table width='100%' border="0" align='center' cellspacing='0' cellpadding='5' class="text">
  <tr>
	<td class="adminhead" align='center' colspan='2'>MEDIA CHANNEL MANAGEMENT</td>
  </tr>
  <tr>
	<td colspan='2' align ='right' class="bodytext"><b><font class='error'>* required field<br><br></font></b></td>
  </tr>
  <?php
  if(isset($_GET['id'])) $updID = $_GET['id'];
  else $updID = '';
  $error_msg = '';
  $mChannelName = '';
  if(isset($_POST['submit']))
  {
    $mChannelName = $DRW->real_escape_string($_POST['mChannelName']);
  
    $chk = "SELECT * FROM cscan_mchannel WHERE mChannelName = '$mChannelName' AND mChannelID!='$updID'";
    $rs = $DRW->query($chk,$DRW_read);
    if($DRW->num_rows($rs) == 0)
    {
      if($updID == '')
      {
        $sql = "INSERT INTO cscan_mchannel SET mChannelName='$mChannelName'";
        $actMsg = 'added';
        $DRW->query($sql,$DRW_main);
      }
      else
      {
        $sql = "UPDATE cscan_mchannel SET mChannelName='$mChannelName' WHERE mChannelID='$updID'";
        $actMsg = 'updated';
        $DRW->query($sql,$DRW_main);
      }
      echo "<tr><td align=center colspan='2'>New media channel has been $actMsg sucessfully.</td></tr>";
      if($_POST['submit'] == 'Save & Add More'){
      		ob_end_clean();
			header("Location: addMedia.php?a=1");
			exit;
      }
      else{
      		ob_end_clean();
			header("Location: manageMedia.php");
			exit;
      }
    }
    else
    {
      $error_msg = 'Media Channel already exist.';
    }
  }
  else
  {
    if($updID!='')
    {
      $sql = "SELECT * FROM cscan_mchannel WHERE mChannelID='$updID'";
      $editRS = $DRW->query($sql,$DRW_read);
      $editRS = $DRW->fetch_array($editRS);
	  $mChannelName = $editRS['mChannelName']; 
    }
  }
?>
    <tr>
		<td>
			<table width='60%' style='border-collapse:collapse' rules='none' bordercolor='#14734F' border='1' align='center' cellspacing='0' cellpadding='4'>
			<tr>
				<td class="subhead" align='center' colspan='2'>
				<?php if($updID!='')  echo 'UPDATE'; 
					  else echo 'ADD'; ?> MEDIA CHANNEL</td>
			</tr>
			<tr><td colspan='100%' class='error' align='center'><?php echo $error_msg ; ?><br><br></td></tr>
			<form method='post' name='frm1' onsubmit='return validate()'>
			<tr>
				<td class="bodytext" align='right'>
					Media Channel Name<font class='error'>*</font>:
				</td>
				<td>
					<input type="text" name="mChannelName" size='40' class="input_box" maxlength='40' value="<?php echo htmlspecialchars($mChannelName,ENT_QUOTES);?>" >
				</td>
			</tr>
  
			<tr><td colspan='100%'> &nbsp;</td></tr>
			<tr>
				<td> </td>
				<td  align=''>
				<?php if($updID == ''){?>
				<input class=button type='submit' name='submit' value='Save' onClick='return validate();' disabled='disabled'>
				<input class=button type='submit' name='submit' value='Save &amp; Add More' style='width:120' onClick='return validate();' disabled='disabled'>
				<input class=button type='reset' value='Reset'>
				<?php }else{ ?>
				<input class=button type='submit' name='submit' value='Update' onClick='return validate();' disabled='disabled'>
				<input type='hidden' name='id' value="<?php echo $updID; ?>" >
				<input class=button type='button' value='Cancel' onclick="location.href='manageMedia.php'; return false;">
				<?php }?>
				</td>
			</tr>
		</form>
	</table>
    </td></tr>
     <?php //} ?>
</table>
<?php include 'bottom.php'; ?>
<script type="text/JavaScript">
<!--
function validate()
{
  mChannelName=document.frm1.mChannelName.value=trimspace(document.frm1.mChannelName.value);
  if(mChannelName == '')
  {
    alert('Please enter the media channel name.');
    document.frm1.mChannelName.focus();
    return false;
  }
}
//-->
</script>
