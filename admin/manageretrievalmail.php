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
<table width='100%' border='0' cellspacing='0' cellpadding='9' class='text' rules='none' bordercolor='#B9B9B9' align='center' style='border-collapse:collapse'>
<tr><td class="adminhead" align='center' colspan='9'>Retrieval Mail</td></tr>
<!-- search and right buttons start-->
<tr>
<td colspan='9'>
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
    <?php if(checkGroup(79)){?>
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
$capture='';
if(isset($_REQUEST['delID']) && ($_REQUEST['delID'] > 0)) {

  $delID = $_REQUEST['delID'];
        
	if (!is_array($delID)){
		$delID = array($delID);
	}

  #################################### Start S3 Implementation Code ###########################################
  $emailData = array();
  #################################### End S3 Implementation Code ###########################################
  
	for($i=0;$i<count($delID);$i++){
		$delThis = $delID[$i];
		$sql_sel = "SELECT upload_file FROM cscan_retrieval_mail WHERE retrieval_id = '$delThis'";
		$res = $DRW->query($sql_sel,$DRW_read);
    #################################### Start S3 Implementation Code ###########################################
    $dataR = $DRW->fetch_row($res);
    $uploadfile = '';
    if(!empty($dataR[0])){
      $uploadfile = explode(',',$dataR[0]);
    }
    
		if ($DRW->num_rows($res) > 0) {
      $sql="DELETE FROM cscan_retrieval_mail WHERE retrieval_id = '$delThis'";        
      /* Added for track on delete operation */
          
      $track_delete_data=array();       

      $track_delete_data = [
              'auth_id' => $GLOBALS['AUTH_DATA']['userID'],
              'auth_name' => authUserName($GLOBALS['AUTH_DATA']['userID']),
              'deleted_id' => (int)$delThis,
              'sql_query' => $sql,
              'ip_address' => ipAddress(),
              'user_agent' => $_SERVER['HTTP_USER_AGENT'],
              'delete_type' => 'Manage Retrieval Mail',
              'is_mobile' => isMobile(),
              'insert_date' => date("Y-m-d H:i:s")
          ];
      trackDelete($track_delete_data);
      $emailData[] = $track_delete_data;

      /*END  Added for track on delete operation*/
						
			$deleteSql = $DRW->query($sql,$DRW_main);
      if($deleteSql){
        if(!empty($uploadfile)){
          foreach($uploadfile as $deletefile){
            $result = $s3->deleteObject([
              'Bucket' => $bucket_name,
              'Key' => 'retrivalservices/'.$deletefile,
            ]);
          }
        }     
      }
      #################################### End S3 Implementation Code ###########################################
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
                sendDevAlert('Caution! Data Deleted From Manage Retrieval Mail',$html);
            }
        /*END  Added for track on delete */
	if($i > 0) {
		$message="<b>$i</b> Record(s) has been deleted.";
	}
} 


$sql = "SELECT retrieval_id,name,email,phone,company,pickup_discription,upload_file,need_fulfill,area_of_focus_id,inserted_date,retid FROM cscan_retrieval_mail order by retrieval_id DESC  LIMIT $p, $limit";
$rs = $DRW->query($sql,$DRW_read);
$resultCount = $DRW->num_rows($rs);


$numquery = "Select COUNT(retrieval_id) as numrows FROM cscan_retrieval_mail";

$numquery = $DRW->query($numquery,$DRW_read);
$nrow = $DRW->fetch_row($numquery);
$numrows = $nrow[0];

?>
<tr>
<td width='0.5%' class="adminhead" height='15px'>
  <?php if(checkGroup(79)){?>
    <input type='checkbox' name='setUnset' onclick='setAll()'>
  <?php }?>
</td>
<td width='5%' class="adminhead" height='15px' >ID</td>        
<td align="left" width='10%' class="adminhead" height='15px' ><b>Detail</td>
<td align="left" width='10%' class="adminhead" height='15px' ><b>Company </td>
<td align="left" width='10%' class="adminhead" height='15px' ><b>Attached File </td>
<td align="left" width='10%' class="adminhead" height='15px' ><b>Area Of Focus</td>
<td align="left" width='25%' class="adminhead" height='15px' ><b>Message</td>
<td align="left" width='10%' class="adminhead" height='15px' ><b>Need Fulfilled</td>
 
<td align="left" width='10%' class="adminhead" height='15px' ><b>Send Date</td>
</tr>
<tr><td colspan='8' align='center' class='error'><?php echo $message; ?></td></tr>
<?php
if($resultCount > 0) {
	$className='';
	while($row = $DRW->fetch_array($rs)){
		$savefile=array();
		$upload_file='';
		$company='';
		$phone='';		
		$ID 	= $row['retrieval_id'];
		$name 	= $row['name'];
		$email 	= $row['email'];
		$phone 	= $row['phone'];
                $retid  = $row['retid'];
		$company 	= trim($row['company']);      
		$pickup_discription= trim(stripslashes($row['pickup_discription'])); 
		$upload_file= trim($row['upload_file']);
	if($upload_file!=''){
		if(strstr($upload_file,',')){
			$savefile=explode(',',$upload_file);
		}else{
			$savefile[0]=$upload_file;
		}
	  }
                $area_of_focus_id= trim($row['area_of_focus_id']); 
                $area_of_focus =getAreaNameByID($area_of_focus_id);
		$need_fulfill= trim($row['need_fulfill']);                    
		          
		$inserted_date	= $row['inserted_date'];		
		
		if ($className=='selected-bg') $className='white-bg';
		else $className='selected-bg';
		?> 
		<?php $siteurl= 'http://'.$_SERVER['HTTP_HOST'].'/'.str_replace('admin/manageretrievalmail.php','',$_SERVER['SCRIPT_NAME']);?>
		<tr valign=top class="<?php echo $className; ?>">
			 <td width='0.5%'>
                             <?php if(checkGroup(79)){?>
                             <input type='checkbox' name='delID[]' value='<?php echo $ID; ?>'>
                             <?php }?>
                         </td>
                         <td width='5%'>
                             <?php 
                             if($retid!='0' and $retid!=''){
                                 echo $retid;
                              } 
                                 ?></td>  
			 <td align="left" width='10%'>Name: <?php echo $name; ?><br />Email: <a href="mailto:<?php echo trim($email); ?>" target="_top"><?php echo $email; ?></a></br><?php if(trim($phone)!=''){?>Phone: <?php echo $phone;  }?></td>                             
			 <td align="left" width='10%'><?php echo $company; ?></td>
			 <td align="left" width='10%'><?php if(count($savefile)>0){
				 for($i=0;$i<count($savefile);$i++){
            #################################### Start S3 Implementation Code ###########################################
            /*$results = $s3->getObject([
              'Bucket' => $bucket_name,
              'Key'    => 'retrivalservices/'.$savefile[$i],
            ]);*/  
					 echo '<a href="'.$displays3URL.'retrivalservices/'.$savefile[$i].'" target="_blank">'.$savefile[$i].'</a>';
					 echo '<br /><br />';
					#################################### End S3 Implementation Code ###########################################
					 
				 }
				} ?></td>
                         <td align="left" width='5%'><?php echo $area_of_focus; ?></td>
                         <td align="left" style="width:450px;float:left;overflow: hidden;"><?php echo nl2br($pickup_discription); ?></td>
			 <td align="left" width='10%'><?php echo $need_fulfill; ?></td>
			 <td align="left" width='15%'><?php echo $inserted_date; ?></td>
		</tr>
		<?php
	}
	echo "<input type='hidden' name='submit' value='1'>";
        echo "</form>";
        ?>
        
        <tr>
	<td colspan="8">
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
<?php 
function getAreaNameByID($ID) {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    if ($ID != '') {
        $sql = "SELECT area_of_focus FROM cscan_area_of_focus WHERE id = $ID";
        $rs = $DRW->query($sql, $DRW_read);
        $types = array();
        while (list($type) = $DRW->fetch_row($rs)) {
            $types[] = $type;
        }
        $types = @implode(', ', $types);
    } else {
        $types = 'N/A';
    }
    return $types;
}

?>
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
