<?php 
$ALLOW_GROUPS = array(87);
require_once("../auth_auth.php");
include 'top.php'; 
$search_project='';
if(isset($_POST['search_Submit']) && isset($_POST['search_name']) && $_POST['search_name']!='')
    { 
      $search_project = $_POST['search_name'];      
    }
?>
<table width='100%' border='0' cellspacing='0' cellpadding='5' class='text' rules='none' bordercolor='#B9B9B9' align='center' style='border-collapse:collapse'>
  <tr><td class="adminhead" align='center' colspan='4'>YOUTUBE PROJECTS MANAGEMENT</td></tr>
  <!-- search and right buttons start-->
  <tr>
    <td colspan='4'>
      <table border='0' width='100%' cellspacing="0" cellpadding="0">
        <tr valign='top'>
          <td align='right' colspan='2'>
            <table width="100%" border="0" cellspacing="0" cellpadding="0" class="text">                          
              <tr>
                <td colspan="<?php echo $colspan; ?>">
                <form method="post" name="frm2" action="<?php echo $_SERVER['PHP_SELF'];?>">
                Search <input type="text" name="search_name" size="40" maxlength="100" class="input_box" value="<?php echo $search_project; ?>" />
                <input class="button" style="width:60px;" type="submit" name="search_Submit" value="Search" /> 
                &nbsp;&nbsp;&nbsp;&nbsp;
                <input class="button" style="width:70px" type="submit" name="show_All" value="Show All" onclick="document.location.href='<?php echo "{$_SERVER['PHP_SELF']}"; ?>'; return false;" />
                </form>
                </td>    
            </tr>
            <tr>
                <td colspan="3">&nbsp;</td>                
            </tr>
            <tr>
                <td><b>Note</b>: Click any of the following to modify the project.</td>
                <td align='right'><input class='button' style='width:130px' type='button' value='Add Project' onclick="location.href='addYoutubeProject.php'; return false;" ></td>
                <td align='right' width="10%">
                <?php  if(checkGroup(94)){?>
                    <form method="post" name="frm1" action="<?php echo $_SERVER['PHP_SELF'];?>">
                        <input class='button' style='width:60px' type='submit' name='submit1' ID='delBt' value='Delete' onclick=' return confirmDel()'>
                     
                <?php } ?>
                </td>
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
        $track_delete_data=array();
    if(isset($_POST['submit1']) && isset($_POST['delID']))
    { 
      $delID = $_POST['delID'];      
      $emailData = [];
      for($i=0;$i<count($delID);$i++)
        {
            $delThis = $delID[$i];
            $sql = "DELETE FROM cscan_youtube_projects WHERE id = '$delThis'";

            /* Add for track on delete operation */
            $track_delete_data=array();     

            $track_delete_data = [
                    'auth_id' => $GLOBALS['AUTH_DATA']['userID'],
                    'auth_name' => authUserName($GLOBALS['AUTH_DATA']['userID']),
                    'deleted_id' => (int)$delThis,
                    'sql_query' => $sql,
                    'ip_address' => ipAddress(),
                    'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                    'delete_type' => 'Manage Youtube Projects',
                    'is_mobile' => isMobile(),
                    'insert_date' => date("Y-m-d H:i:s")
                ];
            trackDelete($track_delete_data);
            $emailData[] = $track_delete_data;
            /* END Add for track on delete operation */
            $DRW->query($sql,$DRW_main);
            $track_delete_data=array();
            $DRW->query($sql,$DRW_main);            
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

            sendDevAlert('Caution! Data Deleted From Manage YouTube Projects ',$html);
        }
      if($i > 0)
      {
        $message="<b>$i</b> YouTube Project(s) has been deleted.";
      }
    }
    $where='';
    if(!empty($search_project)){
        $where=" where project_name like '%".$search_project."%'";
    }
    $sql = "SELECT * FROM cscan_youtube_projects ".$where." order by created_date desc";
    $rs = $DRW->query($sql,$DRW_read);
    $resultCount = $DRW->num_rows($rs);

?>
    <tr><td width='1%' class="adminhead" height='15px'>
      <?php  if(checkGroup(88)){?>   
        <input type='checkbox' name='setUnset' onclick='setAll()'>
      <?php }?>
        </td>
    <td width='40%' class="adminhead" height='15px' ><b>Project name</td>
    <td width='20%' class="adminhead" height='15px' ><b>Created Date</td>
    </tr>
    <tr><td colspan='4' align='center' class='error'><?php echo $message; ?></td></tr>
<?php
    if($resultCount > 0)
    {
    $className='';
      while($row = $DRW->fetch_array($rs))
      {
        $ID = $row['id'];
        $project_name = $row['project_name'];
		if ($className=='selected-bg') $className='white-bg';
		else $className='selected-bg';
?>
        <tr valign=top class="<?php echo $className; ?>" >
            <td>
            <?php if(checkGroup(88)){?>  
                <input type='checkbox' name='delID[]' value='<?php echo $ID; ?>'>
            <?php } ?>
            </td>
            <td><a class='hlinks' href='addYoutubeProject.php?id=<?php echo $ID; ?>' title='Click here to edit.'><b><?php echo $project_name;?></b></a></td>
            <td >&nbsp;<?php echo date("m/d/Y h:i:s", strtotime($row['created_date']));?></td>
          </tr>
<?php
      }
      echo "<input type='hidden' name='submit' value='1'></form>";
    }
    else
    {
      echo "<tr><td colspan=3 class='error' align=center>No project found.</td></tr>";
      echo "<script>el = document.getElementById('delBt'); el.style.display='none';</script>";
    }

?>
</table>
<?php include 'bottom.php'; ?>

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
