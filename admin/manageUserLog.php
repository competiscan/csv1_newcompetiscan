<?php
$ALLOW_GROUPS = array(19);
require_once("../auth_auth.php");
include 'top.php';
require_once '../includes/functions.php';

$datetext = '';
$today = date('Y-m-d');
$StartDate = '';
$EndDate = '';
$clear = '';
if(!isset($_REQUEST['clear'])){
	if(isset($_REQUEST['StartDate']) && $_REQUEST['StartDate']!=''){
		$val=$DRW->real_escape_string($_REQUEST['StartDate']);
		$StartDate = $_REQUEST['StartDate'];
		$datetext .= " AND date>='$val'";
	}
	else {
		$StartDate = $today;
		$datetext .= " AND date>='$StartDate'";
	}
	if(isset($_REQUEST['EndDate']) && $_REQUEST['EndDate']!=''){
		$val=$DRW->real_escape_string($_REQUEST['EndDate']);
		$EndDate = $_REQUEST['EndDate'];
		$datetext .= " AND date<='$val'";
	}
	else{
		$EndDate = $today;
		$datetext .= " AND date<='$EndDate'";
	}
}
else {
	$clear = '&clear=1';
}

if(isset($_REQUEST['p'])) $p = (int)$_REQUEST['p'];  
else $p = 0;
$limit = 20;
$resultCount = 0;
$numrows = 0;
?>
<table width="100%" border="0" cellspacing="0" cellpadding="5" class="text">
  <tr>
  <td class="adminhead" align="center">USER LOG</td>
  </tr>
</table>
[<a href="manageUserAccounts.php">Manage IP Addresses by Count</a>] &nbsp; [<a href="manageUserLog2.php">Logins by IP Address</a>] &nbsp; [<a href="manageUserLog3.php">Logins by Browser</a>] &nbsp; [<a href="manageUserLog4.php">Logins by IP Address and Browser</a>] &nbsp; [<a href="manageUserLog5.php">Logins by IP Location</a>]
<?php 
print '<div style="margin:6px 0px 6px 0px;">Current IP Address: '.$_SERVER['REMOTE_ADDR'].'</div>';
?>
<form name="searchForm" method="get" action="<?php $_SERVER['PHP_SELF']; ?>">
  <table border="0" cellspacing="0" cellpadding="4" class="text">
	    <tr>
		<td >Start Date </td>
		<td><input type="text" name="StartDate" size="20" class="input_box" value="<?php 
	    	print htmlspecialchars($StartDate,ENT_QUOTES);
		?>" />
        <a href="#" onclick="displayCalendar(document.searchForm.StartDate,'yyyy-mm-dd',this); return false;"><img name="popcal1" src="js_calendar/images/getcal.gif" border="0" alt="" style="vertical-align:bottom;"></a></td>
        </tr>
							
        <tr>
		<td >End Date </td>
		<td><input type="text" name="EndDate" size="20" class="input_box" value="<?php 
	    	print htmlspecialchars($EndDate,ENT_QUOTES);
		?>" />
      <a href="#" onclick="displayCalendar(document.searchForm.EndDate,'yyyy-mm-dd',this); return false;"><img name="popcal2" src="js_calendar/images/getcal.gif" border="0" alt="" style="vertical-align:bottom;" /></a></td>
        </tr>
							
        <tr>	
		<td align="right"><input type="submit" name="submit" value="Search" class="button" /></td><td><input type="submit" name="clear" value="Clear" class="button" /></td>
		</tr>
		</table>			
	<input type="hidden" name="send" value="send" /></form>
<?php
print '<table border="0" cellspacing="2" cellpadding="4">';
$query = "SELECT t1.userID,firstName,lastName,emailAddress,companyName,t1.ldate,DATE_FORMAT(t1.ldate,'%m/%d/%Y'),COUNT(*) as cnt FROM (SELECT DISTINCT userID,LEFT(date,10) as ldate,LEFT(IPAddress,9) FROM cscan_user_tracker WHERE 1=1$datetext) as t1
	JOIN cscan_users cu USING(userID)
	GROUP BY t1.userID,t1.ldate HAVING cnt>1 ORDER BY t1.ldate DESC,lastName,firstName,emailAddress";
$result = $DRW->query($query,$DRW_read);
$numrows = $DRW->num_rows($result);
$query .= " LIMIT $p,$limit";
$result = $DRW->query($query,$DRW_read);
$resultCount = $DRW->num_rows($result);
$currdate = '';
$datecount = 0;
while($data = $DRW->fetch_row($result)){
	$userID = $data[0];
	$firstName = $data[1];
	$lastName = $data[2];
	$emailAddress = $data[3];
	$companyName = $data[4];
	$ldate = $data[5];
	$ldatef = $data[6];
	
	if($ldate!=$currdate){
		if($datecount>=10) break;
		$datecount++;
		$currdate = $ldate;
		print '<tr><td valign="top" class="adminhead" colspan="2">'.$ldatef.'</td>';
	}
	if($lastName!='' || $firstName!='') $name = "$lastName, $firstName ";
	else $name = '';
	print '<tr><td valign="top" class="bodytext">'."$name($emailAddress)".'</td><td valign="top" class="bodytext">';
	$query2 = "SELECT IPAddress,DATE_FORMAT(loginTime,'%l:%i %p'),loginTime FROM cscan_user_tracker WHERE userID=$userID AND LEFT(date,10)='$ldate' ORDER BY loginTime";
	$result2 = $DRW->query($query2,$DRW_read);
	$currip = '';
	$className = '';
	$usedIP = array();
	print '<table border="0" cellspacing="0" cellpadding="0" width="200">';
	while($data2 = $DRW->fetch_row($result2)){
		$IPAddress = $data2[0];
		$loginTime = $data2[1];
		
		if($IPAddress!=$currip){
			$currip = $IPAddress;
			if(in_array($IPAddress,$usedIP)){
				$ip = "$IPAddress *";
			}
			else {
				$usedIP[] = $IPAddress;
				$ip = $IPAddress;
			}
			if($className=='selected-bg') $className='white-bg';
			else $className='selected-bg';
		}
		else $ip = '&nbsp;';
		print '<tr class="'.$className.'"><td valign="top" class="bodytext" width="40%">'.$loginTime.'</td><td>&nbsp;</td><td valign="top" class="bodytext">'.$ip.'</td></tr>';
	}
	print '</table>';
	print '</td></tr>';
}
print '</table>';

if($resultCount > 0){
	print '<table border="0" width="100%" cellspacing="0" cellpadding ="5">
	<tr><td colspan = "2">&nbsp;</td></tr>
	<tr>';
	if ($p > 0) {
		$prevs = $p - $limit;
		print "<td align=\"right\" style= \"margin-right:5px;\" width=\"50%\"><a href=\"{$_SERVER['PHP_SELF']}?p=$prevs&StartDate=$StartDate&EndDate=$EndDate$clear\" class=\"sidehead\">&laquo; Prev $limit</a></td>";
	}
	else {
		echo "<td width=\"50%\">&nbsp;</td>";
	}
	
	$pages = ceil($numrows/$limit);
	$news = $p + $limit;
	if($news<$numrows) {
		echo "<td  width=\"50%\" style=\"margin-left:10px;\"><a href=\"{$_SERVER['PHP_SELF']}?p=$news&StartDate=$StartDate&EndDate=$EndDate$clear\" class=\"sidehead\">Next $limit &raquo;</a></td>";
	}
	else {
		echo "<td width=\"50%\">&nbsp;</td>";
	}
	echo "</tr>";
	
	$a = $p + $limit;
	if($a>$numrows) $a = $numrows;
	
	echo "<tr><td class=\"bodytext\" colspan=\"2\" align=\"center\">Showing results ".($p+1)." to $a of $numrows</td></tr>
	</table>";
}
			
print '<script type="text/javascript" src="js_calendar/calendar.js?new=20110531"></script>';

include 'bottom.php';
?> 