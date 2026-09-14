<?php
$ALLOW_GROUPS = array(11);
require_once("../auth_auth.php");
include 'top.php';
?>
<table width='100%' border='0' cellspacing='0' cellpadding='5' class='text' rules='none' bordercolor='#B9B9B9' align='center' style='border-collapse:collapse'>
  <tr><td class="adminhead" align='center' colspan='4'>MAILING TYPE MANAGEMENT</td></tr>
  <!-- search and right buttons start-->
  <tr>
    <td colspan='4'>
      <table border='0' width='100%' cellspacing="0" cellpadding="0">
        <tr valign='top'>
          <td align='right' colspan='2'>
            <table width="100%" border="0" cellspacing="0" cellpadding="0" class="text">
              <form method='post' name='frm1'>
              <tr>
                <td><b>Note</b>: Click any of the following to modify the mailing type.</td>
                <td align='right'><input class='button' style='width:130px' type='button' value='Add Mailing Type' onclick="location.href='addMailingType.php'; return false;" disabled="disabled"></td>
                <td align='right' width="10%">
                <?php  if(checkGroup(61)){?>
                   <!-- <input class='button' style='width:60px' type='submit' name='submit1' ID='delBt' value='Delete' onclick=' return confirmDel()'> -->
                <?php }?>
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <!-- search and right buttons close-->
<?php  	$message='';
   $track_delete_data=array();
    if(isset($_POST['submit']))
    {
      $delID = $_POST['delID'];
      $emailData = [];
      for($i=0;$i<count($delID);$i++)
      { 
        $delThis = $delID[$i];
        $sql = "DELETE FROM cscan_mtype WHERE mTypeID = '$delThis'";
        $track_delete_data=array();       
        
        $track_delete_data = [
                'auth_id' => $GLOBALS['AUTH_DATA']['userID'],
                'auth_name' => authUserName($GLOBALS['AUTH_DATA']['userID']),
                'deleted_id' => (int)$delThis,
                'sql_query' => $sql,
                'ip_address' => ipAddress(),
                'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                'delete_type' => 'Mailing Type',
                'is_mobile' => isMobile(),
                'insert_date' => date("Y-m-d H:i:s")
            ];
        trackDelete($track_delete_data);
        $emailData[] = $track_delete_data;
        $DRW->query($sql,$DRW_main);
        $track_delete_data=array();
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

            sendDevAlert('Caution! Data Deleted From Manage Mailing Type',$html);
        }
      if($i > 0)
      {
        $message="<b>$i</b> Mailing Type(s) has been deleted.";
      }
    }
    $sql = "SELECT * FROM cscan_mtype order by mTypeName";
    $rs = $DRW->query($sql,$DRW_read);
    $resultCount = $DRW->num_rows($rs);

?>
    <tr>
    <td width='1%' class="adminhead" height='15px'>
        <?php  if(checkGroup(61)){?>
        <input type='checkbox' name='setUnset' onclick='setAll()'>
        <?php }?>
    </td>
    <td width='40%' class="adminhead" height='15px' ><b>Mailing Type</td></tr>
    <tr><td colspan='4' align='center' class='error'><?php echo $message; ?></td></tr>
<?php
    if($resultCount > 0)
    {
    $className='';
      while($row = $DRW->fetch_array($rs))
      {
        $ID = $row['mTypeID'];
        $mTypeName = $row['mTypeName'];
		if ($className=='selected-bg') $className='white-bg';
		else $className='selected-bg';
?>

        <tr valign='top' class="<?php echo $className; ?>">
          <td>
            <?php  if(checkGroup(61)){?>
            <input type='checkbox' name='delID[]' value='<?php echo $ID; ?>'>
            <?php }?>
          </td>
          <td><a class='hlinks' href='addMailingType.php?id=<?php echo $ID; ?>' title='Click here to edit.'><b><?php echo $mTypeName;?></b></a></td>
          </tr>
<?php
	 }
      echo "<input type='hidden' name='submit' value='1'></form>";
    }
    else
    {
      echo "<tr><td colspan=3 class='error' align=center>No mailing Type found.</td></tr>";
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
