<?php 
$ALLOW_GROUPS = array(10);
require_once "../auth_auth.php";
include 'top.php';
?>
<table width='100%' border="0" align='center' cellspacing='0' cellpadding='5' class="text"> 
  <tr><td class="adminhead" align='center' colspan='2'>MANAGE AUDIENCE</td></tr>
  <?php
  if(isset($_GET['id'])) $updID = $_GET['id'];
  else $updID = '';
  $error_msg = '';
  $mPanelName = '';
  if(isset($_POST['submit']))
  {
    $mPanelName = $DRW->real_escape_string($_POST['mPanelName']);
  
    $chk = "SELECT * FROM cscan_mpanel WHERE mPanelName='$mPanelName' AND mPanelID!='$updID'";
    $rs = $DRW->query($chk,$DRW_read);
    if($DRW->num_rows($rs) == 0)
    {
      if($updID == '')
      {
        $sql = "INSERT INTO cscan_mpanel SET mPanelName='$mPanelName'";
        $actMsg = 'added';
        $DRW->query($sql,$DRW_main);
      }
      else
      {
        $sql = "UPDATE cscan_mpanel SET mPanelName='$mPanelName' WHERE mPanelID='$updID'";
        $actMsg = 'updated';
        $DRW->query($sql,$DRW_main);
      }
      echo "<tr><td align=center colspan='2'>New Audience has been $actMsg sucessfully.</td></tr>";
      if($_POST['submit'] == 'Save & Add More'){
      		ob_end_clean();
			header("Location: addMailingPnl.php?a=1");
			exit;
      }
      else{
      		ob_end_clean();
			header("Location: manageMailingPnl.php");
			exit;
      }
    }
    else
    {
      $error_msg = 'Audience already exists.';
	  //echo "<tr><td class='error' align='center' colspan='2'>Audience already exists.</td></tr>";
    }
  }
  else
  {
    if($updID!='')
    {
      $sql = "SELECT * FROM cscan_mpanel WHERE mPanelID='$updID'";
      $editRS = $DRW->query($sql,$DRW_read);
      $editRS = $DRW->fetch_array($editRS);
	  $mPanelName = $editRS['mPanelName'];
    }
  }
?>
  <tr><td colspan='100%'>&nbsp;</td></tr>
  <tr>
    <td>
      <table border="1" rules="none" bordercolor="#14734F" width='60%' align='center' cellspacing='0' cellpadding="5" style="border-collapse:collapse">
        <tr>
			<td class="subhead" align='center' colspan='2'>
				<?php if($updID!='')  echo 'UPDATE'; 
					  else echo 'ADD';
			    ?> AUDIENCE
			</td>
		</tr>
        <tr>
			<td colspan='2' align='right' class="bodytext">
				<b><font class='error'>* required field<br><br></font></b>
			</td>
		</tr>
		<tr><td colspan='100%' class='error' align='center'><?php echo $error_msg ;?></td></tr>
        <form method='post' name='frm1' onsubmit='return validate()'>
        <tr>
			<td class="bodytext" align='right'>
				<b>Audience </b><font class='error'>*</font>:
			</td>
			<td>
				<input type="text" name="mPanelName" size='40' class="input_box" maxlength='40' value="<?php echo htmlspecialchars($mPanelName,ENT_QUOTES); ?>" >
			</td>
		</tr>
        <tr><td colspan='100%'>&nbsp;</td></tr>
        <tr>
          <td>&nbsp;</td>
          <td  align=''>
        <?php if($updID == ''){?>
          <input class='button' type='submit' name='submit' value='Save' onClick='return validate();' disabled='disabled'>
          <input class='button' type='submit' name='submit' value='Save &amp; Add More' style='width:120' onClick='return validate();' disabled='disabled'>
          <input class='button' type='reset' value='Reset'>
        <?php }else{ ?>
          <input class='button' type='submit' name='submit' value='Update' onClick='return validate();' disabled='disabled'>
          <input type='hidden' name='id' value="<?php echo $updID; ?>" >
          <input class='button' type='button' value='Cancel' onclick="location.href='manageMailingPnl.php'; return false;">
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
  mPanelName=document.frm1.mPanelName.value=trimspace(document.frm1.mPanelName.value);
  if(mPanelName == '')
  {
    alert('Please enter Audience name.');
    document.frm1.mPanelName.focus();
    return false;
  }
}
//-->
</script>