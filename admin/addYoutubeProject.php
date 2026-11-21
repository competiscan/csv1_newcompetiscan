<?php 
$ALLOW_GROUPS = array(9);
require_once "../auth_auth.php";
include 'top.php';
?>
<table width='100%' border="0" align='center' cellspacing='0' cellpadding='5' class="text">
  <tr>
	<td class="adminhead" align='center' colspan='2'>YOUTUBE PROJECTS MANAGEMENT</td>
  </tr>
  <tr>
	<td colspan='2' align ='right' class="bodytext"><b><font class='error'>* required field<br><br></font></b></td>
  </tr>
  <?php
  if(isset($_GET['id'])) $updID = $_GET['id'];
  else $updID = '';
  $error_msg = '';
  $project_name = '';
  if(isset($_POST['submit']))
  {
    $project_name = $DRW->real_escape_string($_POST['project_name']);
  
    $chk = "SELECT * FROM cscan_youtube_projects WHERE project_name = '$project_name' AND id!='$updID'";
    $rs = $DRW->query($chk,$DRW_read);
    if($DRW->num_rows($rs) == 0)
    {
      if($updID == '')
      {
        $sql = "INSERT INTO cscan_youtube_projects SET project_name='$project_name'";
        $actMsg = 'added';
        $DRW->query($sql,$DRW_main);
      }
      else
      {
        $sql = "UPDATE cscan_youtube_projects SET project_name='$project_name' WHERE id='$updID'";
        $actMsg = 'updated';
        $DRW->query($sql,$DRW_main);
      }
      echo "<tr><td align=center colspan='2'>New search project_name has been $actMsg sucessfully.</td></tr>";
      if($_POST['submit'] == 'Save & Add More'){
            ob_end_clean();
            header("Location: addYoutubeProject.php?a=1");
            exit;
      }
      else{
            ob_end_clean();
            header("Location: manageYoutubeProjects.php");
            exit;
      }
    }
    else
    {
      $error_msg = 'Project name already exist.';
    }
  }
  else
  {
    if($updID!='')
    {
      $sql = "SELECT * FROM cscan_youtube_projects WHERE id='$updID'";
      $editSK = $DRW->query($sql,$DRW_read);
      $editSK = $DRW->fetch_array($editSK);
      $project_name = $editSK['project_name']; 
    }
  }
?>
    <tr>
        <td>
            <table width='60%' style='border-collapse:collapse' rules='none' bordercolor='#14734F' border='1' align='center' cellspacing='0' cellpadding='4'>
                <tr>
                    <td class="subhead" align='center' colspan='2'>
                    <?php if($updID!='')  echo 'UPDATE'; 
                              else echo 'ADD'; ?> PROJECT
                    </td>
                </tr>
                <tr><td colspan='100%' class='error' align='center'><?php echo $error_msg ; ?><br><br></td></tr>
                <form method='post' name='frm1' onsubmit='return validate()'>
                    <tr>
                        <td class="bodytext" align='right'>
                            Project<font class='error'>*</font>:
                        </td>
                        <td>
                            <input type="text" name="project_name" size='40' class="input_box" maxlength='40' value="<?php echo htmlspecialchars($project_name,ENT_QUOTES);?>" >
                        </td>
                    </tr>

                    <tr><td colspan='100%'> &nbsp;</td></tr>
                    <tr>
                        <td> </td>
                        <td  align=''>
                            <?php if($updID == ''){?>
                            <input class=button type='submit' name='submit' value='Save' onClick='return validate();'>
                            <input class=button type='submit' name='submit' value='Save &amp; Add More' style='width:120' onClick='return validate();'>
                            <input class=button type='reset' value='Reset'>
                            <input class=button type='button' value='Cancel' onclick="location.href='manageYoutubeProjects.php'; return false;">
                            <?php }else{ ?>
                            <input class=button type='submit' name='submit' value='Update' onClick='return validate();'>
                            <input type='hidden' name='id' value="<?php echo $updID; ?>" >
                            <input class=button type='button' value='Cancel' onclick="location.href='manageYoutubeProjects.php'; return false;">
                            <?php }?>
                        </td>
                    </tr>
                </form>
            </table>
        </td>
    </tr>
     <?php //} ?>
</table>
<?php include 'bottom.php'; ?>
<script type="text/JavaScript">
<!--
function validate()
{
  project_name=document.frm1.project_name.value=trimspace(document.frm1.project_name.value);
  if(project_name == '')
  {
    alert('Please enter the project name.');
    document.frm1.project_name.focus();
    return false;
  }
}
//-->
</script>
