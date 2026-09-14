<?php
$ALLOW_GROUPS = array(19);
require_once("../auth_auth.php");
include 'top.php';
require_once '../includes/functions.php';

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
[<a href="manageUserAccounts.php">Manage IP Addresses by Count</a>] &nbsp; [<a href="manageUserLog.php">Manage IP Addresses by Date</a>] &nbsp; [<a href="manageUserLog2.php">Logins by IP Address</a>] &nbsp; [<a href="manageUserLog4.php">Logins by IP Address and Browser</a>] &nbsp; [<a href="manageUserLog5.php">Logins by IP Location</a>]
<?php
print '<div style="margin:6px 0px 6px 0px;">Current IP Address: '.$_SERVER['REMOTE_ADDR'].'</div>';
print '<table border="0" cellspacing="2" cellpadding="4">';
print '<tr><td valign="top" class="adminhead">Name</td><td valign="top" class="adminhead">&nbsp;</td></tr>';
$query = "SELECT DISTINCT cu.userID,firstName,lastName,emailAddress,companyName
FROM 
(SELECT t1.userID,COUNT(*) as cnt FROM (SELECT DISTINCT userID,cookie_code,LEFT(date,10) as ldate FROM cscan_user_tracker WHERE cookie_code<>0) as t1
GROUP BY t1.userID,t1.ldate HAVING cnt>1) as t2
JOIN cscan_users cu USING(userID) 
WHERE emailAddress NOT LIKE '%@competiscan.com' AND emailAddress NOT LIKE '%@suntecindia.com' AND emailAddress NOT LIKE '%@highlandsolutions.com' AND emailAddress NOT LIKE '%@chicagorecords.com'
ORDER BY lastName,firstName,emailAddress";
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
	
	if($lastName!='' || $firstName!='') $name = "$lastName, $firstName ";
	else $name = '';
	print '<tr><td valign="top" class="bodytext">'."$name($emailAddress)".'</td><td valign="top" class="bodytext">';
	
	$query2 = "SELECT t1.ldate,COUNT(*) as cnt FROM (SELECT DISTINCT LEFT(date,10) as ldate,cookie_code FROM cscan_user_tracker WHERE userID=$userID AND cookie_code<>0) as t1
		GROUP BY t1.ldate HAVING cnt>1 ORDER BY t1.ldate";
	$result2 = $DRW->query($query2,$DRW_read);
	$currip = '';
	$className = '';
	$usedArray = array();
	print '<table border="0" cellspacing="0" cellpadding="0" width="300">';
	while($data2 = $DRW->fetch_row($result2)){
		$ldate = $data2[0];
		
		if($className=='selected-bg') $className='white-bg';
		else $className='selected-bg';
			
		print '<tr class="'.$className.'"><td valign="top" class="bodytext" width="30%">'.$ldate.'</td><td>&nbsp;</td><td valign="top" class="bodytext">';
		$currip = '';
		$query3 = "SELECT DISTINCT cookie_code,DATE_FORMAT(loginTime,'%l:%i %p') FROM cscan_user_tracker WHERE cookie_code<>0 AND userID=$userID AND date='$ldate' ORDER BY loginTime";
		$result3 = $DRW->query($query3,$DRW_read);
		while($data3 = $DRW->fetch_row($result3)){
			$cookie_code = $data3[0];
			$time = $data3[1];
			
			if($cookie_code!=$currip){
				$currip = $cookie_code;
				$cnum = array_search($cookie_code,$usedArray);
				if($cnum===false){
					$usedArray[] = $cookie_code;
					$cnum = count($usedArray);
				}
				else{
					$cnum++;
				}
				$pt = " Browser #$cnum";
			}
			else continue;
			
			print "$time$pt<br />";
		}
		print '</td></tr>';
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
		print "<td align=\"right\" style= \"margin-right:5px;\" width=\"50%\"><a href=\"{$_SERVER['PHP_SELF']}?p=$prevs\" class=\"sidehead\">&laquo; Prev $limit</a></td>";
	}
	else {
		echo "<td width=\"50%\">&nbsp;</td>";
	}
	
	$pages = ceil($numrows/$limit);
	$news = $p + $limit;
	if($news<$numrows) {
		echo "<td  width=\"50%\" style=\"margin-left:10px;\"><a href=\"{$_SERVER['PHP_SELF']}?p=$news\" class=\"sidehead\">Next $limit &raquo;</a></td>";
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