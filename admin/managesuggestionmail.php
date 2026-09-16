<?php
$ALLOW_GROUPS = array(41);
require_once("../auth_auth.php");
include 'top.php';
$limit = 30;
if(isset($_REQUEST['p'])) $p = $_SESSION['manageCategory_p'] = $_REQUEST['p'];
elseif(isset($_SESSION['manageCategory_p'])) $p = $_SESSION['manageCategory_p'];
else $p = 0;
$sort='';
?>
<table width='100%' border='0' cellspacing='0' cellpadding='7' class='text' rules='none' bordercolor='#B9B9B9' align='center' style='border-collapse:collapse'>
<tr><td class="adminhead" align='center' colspan='7'>Suggestions Mail</td></tr>
<!-- search and right buttons start-->
<tr>
<td colspan='7'>
<table border='0' width='100%' cellspacing="0" cellpadding="0">
<tr valign='top'>
<td align='right' colspan='2'>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="text">
<form method='post' name='frm1'>
<tr>
<td><!--<b>Note</b>: Click any of the following to manage the online display product. --></td>

<td></td>
<td align='right' width="5%">&nbsp;</td>

<td align='right' width="20%">
 <?php if(checkGroup(77)){?>
    <input class='button' style='width:60px' type='submit' name='submit1' ID='delBt' value='Delete' onclick=' return confirmDel()'>
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
<?php
$message='';
$capture='';
$track_delete_data=array();
$emailData = [];
if(isset($_REQUEST['delID']) && ($_REQUEST['delID'] > 0)) {

    $delID = $_REQUEST['delID'];
       
        
	if (!is_array($delID)){
		$delID = array($delID);
	}
	for($i=0;$i<count($delID);$i++){
		$delThis = $delID[$i];
		$sql_sel = "SELECT suggestion_id FROM cscan_suggestion_mail WHERE suggestion_id = '$delThis'";
		$res = $DRW->query($sql_sel,$DRW_read);
                
		if ($DRW->num_rows($res) > 0) {
			$sql="DELETE FROM cscan_suggestion_mail WHERE suggestion_id = '$delThis'";			
                    /* Added for track on delete operation */
                        
                        $track_delete_data=array();       

                        $track_delete_data = [
                                'auth_id' => $GLOBALS['AUTH_DATA']['userID'],
                                'auth_name' => authUserName($GLOBALS['AUTH_DATA']['userID']),
                                'deleted_id' => (int)$delThis,
                                'sql_query' => $sql,
                                'ip_address' => ipAddress(),
                                'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                                'delete_type' => 'Manage Downloads',
                                'is_mobile' => isMobile(),
                                'insert_date' => date("Y-m-d H:i:s")
                            ];
                        trackDelete($track_delete_data);
                        $emailData[] = $track_delete_data;

                    /*END  Added for track on delete operation*/
                        $DRW->query($sql,$DRW_main);
		}
	}
        /* Added for track on delete */
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
                sendDevAlert('Caution! Data Deleted From Manage Suggestion Mail',$html);
            }
        /*END  Added for track on delete */
	if($i > 0) {
		$message="<b>$i</b> Record(s) has been deleted.";
	}
} 


$sql = "SELECT suggestion_id,name,email,phone,suggestion,inserted_date FROM cscan_suggestion_mail order by suggestion_id DESC  LIMIT $p, $limit";
$rs = $DRW->query($sql,$DRW_read);
$resultCount = $DRW->num_rows($rs);


$numquery = "Select COUNT(suggestion_id) as numrows FROM cscan_suggestion_mail";

$numquery = $DRW->query($numquery,$DRW_read);
$nrow = $DRW->fetch_row($numquery);
$numrows = $nrow[0];

?>
<tr>
<td width='0.5%' class="adminhead" height='15px'>
  <?php if(checkGroup(77)){?>
    <input type='checkbox' name='setUnset' onclick='setAll()'>
  <?php }?>
</td>
<td width='0.5%' class="adminhead" height='15px' >&nbsp;</td>        
<td align="left" width='10%' class="adminhead" height='15px' ><b>Name</td>
<td align="left" width='10%' class="adminhead" height='15px' ><b>Email</td>
<td align="left" width='10%' class="adminhead" height='15px' ><b>Phone</td>
<td align="left" width='35%' class="adminhead" height='15px' ><b>Message</td>
<td align="left" width='15%' class="adminhead" height='15px' ><b>Send Date</td>
</tr>
<tr><td colspan='5' align='center' class='error'><?php echo $message; ?></td></tr>
<?php
if($resultCount > 0) {
	$className='';
	while($row = $DRW->fetch_array($rs)){
		$ID 	= $row['suggestion_id'];
		$name 	= $row['name'];
		$email 	= $row['email'];
		$phone 	= $row['phone'];
		$suggestion 	= trim(stripslashes($row['suggestion']));                
		$inserted_date	= $row['inserted_date'];
		if ($className=='selected-bg') $className='white-bg';
		else $className='selected-bg';
		?> 
		<tr valign=top class="<?php echo $className; ?>">
			 <td>
                        <?php if(checkGroup(77)){?>
                            <input type='checkbox' name='delID[]' value='<?php echo $ID; ?>'>
                        <?php }?>
                         </td>
			 <td>&nbsp;</td>  
			 <td><?php echo $name; ?></td>                             
			 <td><a href="mailto:<?php echo trim($email); ?>" target="_top"><?php echo $email; ?></a></td>
			 <td><?php echo $phone; ?></td>
			 <td><?php echo nl2br($suggestion); ?></td>
			 <td><?php echo $inserted_date; ?></td>
		</tr>
		<?php
	}
	echo "<input type='hidden' name='submit' value='1'>";
        echo "</form>";
        ?>
        
        <tr>
	<td colspan="7">
		<table border="0" width="100%" cellspacing = "0"  cellpadding ="7">
			<tr>
				<td>&nbsp;</td>
			</tr>
<?php
$firstlink = '[First]';
$prevlink = '[Prev]';
$nextlink = '[Next]';
$lastlink = '[Last]';
$middlelinks = '';
$limstart = $p;
$limiter = $limit;
$rowcnt = $numrows;
$show = 10;
//first and previous only if not on first
if($limstart>0){
	if($limstart>=$limiter) $prev = $limstart - $limiter;
	else $prev = 0;
	$firstlink = "[<a href=\"{$_SERVER['PHP_SELF']}?p=0\">First</a>]";
	$prevlink = "<a href=\"{$_SERVER['PHP_SELF']}?p=$prev\">&laquo; Prev $limiter</a>";
}
// middle loop through total results
$numbers = ceil($rowcnt/$limiter);
$loopstart = ceil($limstart/$limiter);
if($loopstart<($show-1)) $loopstart = 0; // begin, do not move until 4
if($numbers<$show) $loopend = $numbers; // loopend is less than $show
else $loopend = $loopstart+$show;
if($loopend>$numbers && $loopstart!=0) { // end, show last $show
	$loopstart = $numbers - $show;
	$loopend = $numbers;
}
for($i=$loopstart; $i<$loopend; $i++){
	$startnum = $limiter * $i;
	if($startnum!=$limstart) {
		$middlelinks .= "<a href=\"{$_SERVER['PHP_SELF']}?p=$startnum\">".($i+1)."</a> ";
	}
	else $middlelinks .= ($i+1).' ';
}
//next and last if not on last
if($limstart<$rowcnt && (($limstart+($limiter*2))<$rowcnt || ($rowcnt - ($limstart + $limiter))>0)){
	$next = $limstart + $limiter;
	$nextlink = "<a href=\"{$_SERVER['PHP_SELF']}?p=$next\">Next $limiter &raquo;</a>";
	$lastlink = "[<a href=\"{$_SERVER['PHP_SELF']}?p=".(($numbers-1)*$limiter)."\">Last</a>]";
}

if($middlelinks!='') $middlelinks = "[ $middlelinks ] &nbsp;";
print "<tr><td align=\"center\" class=\"bodytext\">$firstlink &nbsp; $prevlink &nbsp; $middlelinks $nextlink &nbsp; $lastlink</td></tr>";
print "<tr><td align=\"center\" class=\"bodytext\">Showing results ".($limstart+1)." to ";
if($limstart+$limiter < $rowcnt) print ($limstart+$limiter);
else print $rowcnt;
print " of $rowcnt</td></tr>";
?>
		</table>
	</td></tr>
<?php }
else{
	echo "<tr><td colspan=5 class='error' align=center>No record found.</td></tr>";
	echo "<script>el = document.getElementById('delBt'); el.style.display='none';</script>";
        echo "<script>el2 = document.getElementById('capBt'); el2.style.display='none';</script>";
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

function confirmCap()
{ 
  goAheadFlag2 = 0;
  for(i=0;i<document.frm1.elements.length;i++)
  {
    if(document.frm1.elements[i].checked == true)
    {
      goAheadFlag2 = 1;
    }
  }
  if(goAheadFlag2)
  {
    if(confirm("Are you sure to capture ?"))
    {
      document.frm1.capture.value=1;
        return true;
    }
    else
    {
      document.frm1.capture.value ='';
        return false;
    }
  }
  else
  {
    alert('Please select at least one record to capture !!!');
    document.frm1.capture.value = '';
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
