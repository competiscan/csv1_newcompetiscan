<?php
$ALLOW_GROUPS = array(83);
require_once("../auth_auth.php");
include 'top.php';
require_once("../includes/functions.php");

$limit = 20;
$msg = '';

if(isset($_REQUEST['search_text'])) {
	$_SESSION['search_text'] = $_REQUEST['search_text'];
} 
elseif(isset($_REQUEST['show_All']) || !isset($_SESSION['search_text'])) {
	$_SESSION['search_text'] = '';
}

if(isset($_GET['sort'])) $sort = (int) $_GET['sort'];
else $sort = 0;

if(isset($_GET['p'])) $p = $_GET['p'];
else $p = 0;

/*if(isset($_REQUEST['query']) && $_REQUEST['query'] == 1){
	$sqlaudiencesector = "SELECT ID, ac_mPanelID, ac_sectorID  FROM cscan_agent_communication WHERE ac_mPanelID != '' OR ac_sectorID != ''";
	$rsaudiencesector = $DRW->query($sqlaudiencesector,$DRW_read);
	while($rowaudiencesector = $DRW->fetch_assoc($rsaudiencesector)) {
		$communicationID = $rowaudiencesector['ID'];
	   	$explodeAudience = explode(',',$rowaudiencesector['ac_mPanelID']);
	   	$explodeSector = explode(',',$rowaudiencesector['ac_sectorID']);
		foreach($explodeAudience as $audienceID){
			if(!empty($audienceID)){
				$insertaudience = "INSERT INTO cscan_communication_audience (communicationID,audienceID) VALUES ('".$DRW->real_escape_string($communicationID)."','".$DRW->real_escape_string($audienceID)."')";
				$DRW->query($insertaudience,$DRW_main);
			}
		}
		foreach($explodeSector as $sectorID){
			if(!empty($sectorID)){
				$insertsector = "INSERT INTO cscan_communication_sector (communicationID,sectorID) VALUES ('".$DRW->real_escape_string($communicationID)."','".$DRW->real_escape_string($sectorID)."')";
				$DRW->query($insertsector,$DRW_main);
			}
		}
	}
}*/

?>

<table width="100%" border="0" cellspacing="0" cellpadding="5" class="text">
	<tr><td class="adminhead" align="center">COMMUNICATION TYPE MANAGEMENT</td></tr>
	<tr><td>
		<form method="post" name="searchForm" action="communication-type.php" onsubmit="return check_searchform();" style="display:inline;">
		<strong>Search Communication Type:</strong>
		<input type="text" name="search_text" class="input_box" value="<?php echo $_SESSION['search_text']; ?>" />
		<input class="button" style="width:60px" type="submit" name="search_Submit1" value="Search" />
		<input type="hidden" name="search_Submit" value="1" /><input type="hidden" name="p" value="0" /></form>
		&nbsp;&nbsp;
		<form action="communication-type.php" method="post" style="display:inline;">
		<input class="button" style="width:70px" type="submit" name="show_All1" value="Show All" />
		<input type="hidden" name="show_All" value="1" /><input type="hidden" name="p" value="0" /></form>
	</td></tr>
	<tr>
		<td>
		<form method="post" name="communicationForm" action="communication-type.php">
			<table width="100%" border="0" cellspacing="0" cellpadding="0" class="text">
			<tr>
			<td><strong>Note:</strong> Click on Edit Communication Type to modify the details.</td>
			<td align="right">
			<input class="button" type="button" value="Add Communication Type" onclick="location.href='add-communication-type.php'; return false;" disabled="disabled"/>
	        </td>
			</tr>
			</table>
		</form>
		</td>
	</tr>
</table>
  
<form action="communication-type.php" method="post" name="deleteform">
<table width="100%" border="0" cellspacing="0" cellpadding="5" class="text">
  <tr>
    <!--td width="5%" class="adminhead" height="15"><input type="checkbox" name="setUnset" onclick="setAll();" /></td-->
    <td width="20%" class="adminhead" height="15"><strong>Communication Type</strong><?php if($sort!=1) print " <a href=\"".$_SERVER['PHP_SELF']."?sort=1&p=0\" class=\"blue\">sort</a>"; ?></td>
	<td width="25%" class="adminhead" height="15"><strong>Audience</strong></td>
	<td width="50%" class="adminhead" height="15"><strong>Sector</strong></td>
	<td width="5%" class="adminhead" height="15" align="center"><strong>Action</strong></td> 
  </tr>
  <tr>
	<td colspan="5" class="error" align="center"><?php echo $msg; ?></td>
  </tr> 
<?php
	$sql = "SELECT cac.ID,cac.type,
			GROUP_CONCAT(DISTINCT(cmp.mPanelName) ORDER BY cmp.mPanelName ASC SEPARATOR ', ') AS 'audience',
			GROUP_CONCAT(DISTINCT(csec.sectorName) ORDER BY csec.sectorName ASC SEPARATOR ', ') AS 'sector'
			FROM cscan_agent_communication cac 
			LEFT JOIN cscan_communication_audience cca ON cca.communicationID = cac.ID 
			LEFT JOIN cscan_communication_sector ccs ON ccs.communicationID = cac.ID 
			LEFT JOIN cscan_mpanel cmp ON cmp.mPanelID = cca.audienceID 
			LEFT JOIN cscan_sector csec ON csec.sectorID = ccs.sectorID";
	$rs = $DRW->query($sql,$DRW_read);
	$numquery = "SELECT COUNT(ID) as numrows FROM cscan_agent_communication cac";
	
	if($_SESSION['search_text']!='') { 
		$search_key = mysqlLike($_SESSION['search_text']);
		$and = " WHERE cac.type LIKE '%$search_key%'";
		$sql .= $and;
		$numquery .= $and;
	}
	
	$numquery = $DRW->query($numquery,$DRW_read);
	$nrow = $DRW->fetch_array($numquery);
	$numrows = $nrow[0];
	$sql .= " GROUP BY cac.ID";
	switch($sort){
		case 1:
			$sql .= " ORDER BY cac.type ";
			break;
		default:
			$sql .= " ORDER BY cac.type ";
	}
	$sql .= "LIMIT $p,$limit";
	
	$rs = $DRW->query($sql,$DRW_read);
	$resultCount = $DRW->num_rows($rs);
	
	if( $resultCount > 0 ) {
		$className='';
		while($row = $DRW->fetch_assoc($rs)) {
			$ID = $row['ID'];
			$type = $row['type'];
			$audience = $row['audience'];
			$sector = $row['sector'];
?>
      <tr valign="top" class="white-bg">
        <td><?php echo $type;?></td>
		<td><?php echo !empty($audience) ? $audience : 'N/A';?></td>
		<td><?php echo !empty($sector) ? $sector : 'N/A'; ?></td>
		<td align="center"><a class="hlinks" href="add-communication-type.php?id=<?php echo $ID;?>" title="Click here to edit."><img src="../images/edit.png" border="0" /></a></td>
	  </tr>
      <?php
		}
	}
	else {
    ?>
    <tr>
   		<td colspan="6" class="error" align="center">No communication type found.</td>
   	</tr>
<?php
	}
?>
  <tr>
	<td colspan="5">
		<table border="0" width="100%" cellspacing="0"  cellpadding="5">
			<tr>
				<td>&nbsp; </td>
			</tr>
<?php
			if($sort>0) $sorttext = '&sort='.$_GET['sort'];
			else $sorttext = '';
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
				$firstlink = "[<a href=\"{$_SERVER['PHP_SELF']}?p=0$sorttext\">First</a>]";
				$prevlink = "<a href=\"{$_SERVER['PHP_SELF']}?p=$prev$sorttext\">&laquo; Prev $limiter</a>";
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
					$middlelinks .= "<a href=\"{$_SERVER['PHP_SELF']}?p=$startnum$sorttext\">".($i+1)."</a> ";
				}
				else $middlelinks .= ($i+1).' ';
			}
			//next and last if not on last
			if($limstart<$rowcnt && (($limstart+($limiter*2))<$rowcnt || ($rowcnt - ($limstart + $limiter))>0)){
				$next = $limstart + $limiter;
				$nextlink = "<a href=\"{$_SERVER['PHP_SELF']}?p=$next$sorttext\">Next $limiter &raquo;</a>";
				$lastlink = "[<a href=\"{$_SERVER['PHP_SELF']}?p=".(($numbers-1)*$limiter)."$sorttext\">Last</a>]";
			}
			
			if($middlelinks!='') $middlelinks = "[ $middlelinks ] &nbsp;";
			print "<tr><td align=\"center\" class=\"bodytext\">$firstlink &nbsp; $prevlink &nbsp; $middlelinks $nextlink &nbsp; $lastlink</td></tr>";
			print "<tr><td align=\"center\" class=\"bodytext\">Showing results ".($limstart+1)." to ";
			if($limstart+$limiter < $rowcnt) print ($limstart+$limiter);
			else print $rowcnt;
			print " of $rowcnt</td></tr>";
?>
		</table>
	</td>
	</tr>
</table>
<input type="hidden" name="active" value="0" /><input type="hidden" name="deletebut" value="0" /></form>
<script type="text/javascript">
<!--
function check_searchform(){
	var search = document.searchForm.search_text.value = trimspace(document.searchForm.search_text.value);
	if(search == "") {
		alert("Please enter some value to search");
		document.searchForm.search_text.focus();
		return false;
	}
	return true;
}
</script>
<?php
include 'bottom.php';
?>