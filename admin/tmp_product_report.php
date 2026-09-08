<?php 
$ALLOW_GROUPS = array(5);
require_once("../auth_auth.php");
include 'top.php';
require_once '../includes/functions.php';

$limit = 20 ;
if(isset($_REQUEST['p'])) $p = $_SESSION['manage_tmp_product_r_p'] = $_REQUEST['p'];
elseif(isset($_SESSION['manage_tmp_product_r_p'])) $p = $_SESSION['manage_tmp_product_r_p'];
else $p = 0;

if(isset($_REQUEST['sort'])) $sort = $_SESSION['manage_tmp_product_r_p_sort'] = (int)$_REQUEST['sort'];
elseif(isset($_SESSION['manage_tmp_product_r_p_sort'])) $sort = $_SESSION['manage_tmp_product_r_p_sort'];
else $sort = 1;

if(!isset($_SESSION['tmp_product_reportDate']) || isset($_REQUEST['show_All'])){
	$_SESSION['tmp_product_reportDate'] = '';
}
elseif(isset($_REQUEST['search_text'])) {
	$_SESSION['tmp_product_reportDate'] = trim($_REQUEST['search_text']);
}

if(isset($_REQUEST['nont'])) {
	$_SESSION['nont_r_p'] = (int) $_REQUEST['nont'];
	$p = $_SESSION['manage_tmp_product_r_p'] = 0;
}

if(isset($_SESSION['nont_r_p']) && $_SESSION['nont_r_p']==1) {
	$addnon = " AND pd.productID IS NULL";
	$newnont = 0; 
}
else {
	$addnon = "";
	$newnont = 1;
}

?>
<table width="100%" border="0" cellspacing="0" cellpadding="5" class="text">
  <tr><td class="adminhead" align="center">TEMP PRODUCT REPORT</td></tr>
</table>
<?php
	$addq = '';
	if($_SESSION['tmp_product_reportDate']!='') { 
		$searchtext = $_SESSION['tmp_product_reportDate'];
		$search_key = mysqlLike($searchtext);
		$addq .= " AND pe.addedToDatabase>='".$DRW->real_escape_string($search_key)." 00:00:00' AND pe.addedToDatabase<='".$DRW->real_escape_string($search_key)." 23:59:59'";
	}
	
	$sql = "SELECT pe.muid,pe.isTmp,DATE_FORMAT(pe.addedToDatabase,'%m/%d/%Y %h:%i %p') as addedToDatabase_pe,pe.productID,UNIX_TIMESTAMP(pe.addedToDatabase),UNIX_TIMESTAMP(pd.actual_addedToDatabase),pd.entryID,pd.productStatus FROM cscan_product_email pe
		LEFT JOIN cscan_product_detail pd ON(pe.productID=pd.productID)
		WHERE 1=1$addq$addnon";
	
	$numquery = "SELECT count(*) as numrows FROM cscan_product_email pe LEFT JOIN cscan_product_detail pd ON(pe.productID=pd.productID) WHERE 1=1$addq$addnon";
	
	$numquery = $DRW->query($numquery,$DRW_read);
	$nrow = $DRW->fetch_row($numquery);
	$numrows = $nrow[0];
	
	if($sort<0) {
		$ascdesc = 'DESC';
		$ascdesc2 = 'ASC';
	}
	else {
		$ascdesc = 'ASC';
		$ascdesc2 = 'DESC';
	}
	switch(abs($sort)){
		case 2:
			$sql .= " ORDER BY pd.entryID_sort1 $ascdesc2, pd.entryID_sort2 $ascdesc2";
			break;
		default: //1
			$sql .= " ORDER BY pe.addedToDatabase $ascdesc2";
	}
	
	$sql .= " limit $p,$limit";
	
	$rs = $DRW->query( $sql,$DRW_read );
	$resultCount = $DRW->num_rows( $rs );
	$count = 1 + $p ;
	$currPage = (($p/$limit) + 1);
	
	function doSort($sort,$dosort,$spacer='<br />'){
		if($sort==($dosort*-1) || $sort!=$dosort) {
			print "$spacer<a href=\"".$_SERVER['PHP_SELF']."?sort=$dosort&p=0\" class=\"blue\">sort</a>";
		}
		else{
			print "$spacer<a href=\"".$_SERVER['PHP_SELF']."?sort=-$dosort&p=0\" class=\"blue\">sort</a>";
		}
	}
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="text">
<tr><td>
	<form method="post" name="prodForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" style="display:inline;">
	<table border="0" cellspacing="0" cellpadding="1" class="text">
		<tr>
		<td><strong>Search Product by Temp Date:</strong></td>
		<td><input type="text" name="search_text" class="input_box" size="15" readonly="readonly" value="<?php echo htmlspecialchars($_SESSION['tmp_product_reportDate'],ENT_QUOTES); ?>" />
		<a href="#" onclick="displayCalendar(document.prodForm.search_text,'yyyy-mm-dd',this); return false;"><img name="popcal1" src="js_calendar/images/getcal.gif" border="0" alt="" style="vertical-align:bottom;" /></a></td>
		</tr>
		<tr><td>&nbsp;</td>
		<td><input class="button" style="width:60px;" type="submit" name="search_Submit" value="Search" />
			&nbsp;&nbsp;&nbsp;&nbsp;
			<input class="button" style="width:70px" type="submit" name="show_All" value="Show All" /></td>
		</tr>
	</table>
	<input type="hidden" name="p" value="0" />
	</form>
</td><td align="right" valign="bottom"><form method="get" name="nonbox" action="<?php echo $_SERVER['PHP_SELF']; ?>"><label><input type="checkbox" name="nont" value="<?php echo $newnont; ?>" onclick="document.location = '<?php echo $_SERVER['PHP_SELF'].'?nont='.$newnont; ?>';" <?php if(!$newnont) print ' checked="checked"'; ?> />Show Non Products Only</label></form></td>
</tr>
</table>
<div>&nbsp;</div>
<table width="100%" border="0" cellspacing="0" cellpadding="5" class="text">
  <tr class="head1">
		<td class="adminhead"><strong>Temp ID</strong></td>
    	<td class="adminhead"><strong>Temp Date</strong><?php doSort($sort,1,'&nbsp;'); ?></td>
	    <td class="adminhead"><strong># Days</strong></td>
		<td class="adminhead"><strong>Date - Record Added</strong></td>
		<td class="adminhead"><strong>Entry ID</strong><?php doSort($sort,2,'&nbsp;'); ?></td>
		<td class="adminhead"><strong>Current Status</strong></td>
		<td class="adminhead"><strong>Users</strong></td>
  </tr>
<?php
	$time = time();
	if( $resultCount > 0 ) {
		$className='';
		while( $row = $DRW->fetch_array($rs) ) {
			$muid = $row[0];
			$isTmp = $row[1];
			$addedToDatabase_pe = $row[2];
			$pe_productID = (float)$row[3];
			$pe_addedToDatabase = $row[4];
			$pd_actual_addedToDatabase = $row[5];
			$entryID = $row[6];
			$productStatus = $row[7];
			
			if($entryID!=''){
				$entryID = '<a href="http://'.$_SERVER['HTTP_HOST'].'/index.php?product='.$pe_productID.'" target="_blank" title="Click here to view product details on Competiscan">'.$entryID.'</a>';
				$addedToDatabase_pe = "<a href=\"#\" onclick=\"logPop(0,$pe_productID,0); return false;\">".$addedToDatabase_pe.'</a>';
			}
			else{
				$addedToDatabase_pe = "<a href=\"#\" onclick=\"logPop($muid,0,$isTmp); return false;\">".$addedToDatabase_pe.'</a>';
				$entryID = '&nbsp;';
			}
			if($pe_productID!=0){
				$sql2 = "SELECT productID,UNIX_TIMESTAMP(logDate) FROM cscan_admin_log WHERE productID=$pe_productID AND muid=0 AND isTmp=0 ORDER BY logDate ASC LIMIT 1";
				$rs2 = $DRW->query( $sql2,$DRW_read );
				$row2 = $DRW->fetch_array($rs2);
				if($row2[0]!=''){
					$pd_actual_addedToDatabase = $row2[1];
				}
				$addedToDatabase_pd = "<a href=\"addproduct.php?id=$pe_productID\">".date('m/d/Y h:i a',$pd_actual_addedToDatabase).'</a>';
			}
			else{
				$pd_actual_addedToDatabase = $time;
				$addedToDatabase_pd = '&nbsp;';
			}
			
			if ($className=='selected-bg') $className='white-bg';
			else $className='selected-bg';
?>
	 <tr class="<?php echo $className;?>">
       	<td valign="top"><?php 
			if($isTmp==1) {
				echo "<a href=\"manage_tmp_product.php?search_text={$muid}tmp&state=0&company=\">{$muid}tmp</a> ";
			}
			else {
				echo "<a href=\"/email.php?muid=$muid\" target=\"_blank\">$muid</a> ";
			}
       	?></td>
       	<td valign="top"><?php echo $addedToDatabase_pe; ?></td>
       	<td valign="top"><?php echo number_format(ceil(($pd_actual_addedToDatabase-$pe_addedToDatabase)/86400)); ?></td>
       	<td valign="top"><?php echo $addedToDatabase_pd; ?></td>
       	<td valign="top"><?php echo $entryID; ?></td>
       	<td valign="top"><?php 
       		switch($productStatus){
				case 1:
					print 'Approved';
					break;
				case 2:
					print 'Unapproved';
					break;
				case 3:
					print 'Reprocessed';
					break;
				case 4:
					print 'Problem';
					break;
				default:
					print '&nbsp;';
			}
       	?></td>
       	<td valign="top"><?php 
		if($pe_productID!=0) {
			$where = "(productID=$pe_productID AND muid=0 AND isTmp=0)";
			
			$sql2 = "SELECT muid,isTmp FROM cscan_product_email WHERE productID=$pe_productID ORDER BY addedToDatabase DESC LIMIT 1";
			$rs2 = $DRW->query( $sql2,$DRW_read );
			$row2 = $DRW->fetch_row($rs2);
			$old_mid = (float)$row2[0];
			$old_istmp = (int)$row2[1];
			if($old_mid!=0){
				$where .= " OR (productID=0 AND muid=$old_mid AND isTmp=$old_istmp)";
			}
		}
		else{
			$where = "(productID=0 AND muid=$muid AND isTmp=$isTmp)";
		}
		$users = '';
		$sql3 = "SELECT DISTINCT userName FROM cscan_admin_log al,cscan_admin_users au WHERE ($where) AND al.userID=au.userID ORDER BY userName";
		$rs3 = $DRW->query( $sql3,$DRW_read );
		while($row3 = $DRW->fetch_array($rs3) ) {
			$userName = $row3[0];
			if($users!=''){
				$users .= ', ';
			}
			$users .= $userName;
		}
		echo $users;
       	?></td>
    </tr>
<?php
		}
	}
	else {
?>
    <tr><td colspan="7" class="error" align="center">No Products Found.</td></tr>
<?php
	}
?>
  <tr>
	<td colspan="7">
		<table border="0" width="100%" cellspacing = "0"  cellpadding ="5">
			<tr>
				<td colspan = "2"> &nbsp;</td>
			</tr>
<?php
		//if($sort>0) $sorttext = '&sort='.$_GET['sort'];
		//else 
		$sorttext = '';
		$firstlink = '[First]';
		$prevlink = '[Prev]';
		$nextlink = '[Next]';
		$lastlink = '[Last]';
		$middlelinks = '';
		$limstart = $p;
		$limiter = $limit;
		$rowcnt = $numrows;
		$show = 10;
		if($rowcnt>0){
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
		}
?>
	</table></td></tr>
</table>
<script type="text/javascript" src="js_calendar/calendar.js?new=200812"></script>
<script type="text/javascript">
<!--
function logPop(mid,pid,istmp) {
	var wind = window.open('admin_log.php?mid='+mid+'&pid='+pid+'&istmp='+istmp,"winpop","left=0, top=0, scrollbars=yes, resizable=yes, width=400, height=300");
	wind.focus();
}
//-->
</script>
<?php include 'bottom.php'; ?>
