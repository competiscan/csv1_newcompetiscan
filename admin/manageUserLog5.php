<?php
$ALLOW_GROUPS = array(19);
require_once("../auth_auth.php");
include 'top.php';
require_once '../includes/functions.php';

if(isset($_REQUEST['p'])) $p = (int)$_REQUEST['p'];  
else $p = 0;
$limit = 100;
$resultCount = 0;
$numrows = 0;
?>
<table width="100%" border="0" cellspacing="0" cellpadding="5" class="text">
  <tr>
  <td class="adminhead" align="center">USER LOG</td>
  </tr>
</table>
[<a href="manageUserAccounts.php">Manage IP Addresses by Count</a>] &nbsp; [<a href="manageUserLog.php">Manage IP Addresses by Date</a>] &nbsp; [<a href="manageUserLog3.php">Logins by Browser</a>] &nbsp; [<a href="manageUserLog4.php">Logins by IP Address and Browser</a>]
<?php
print '<div style="margin:6px 0px 6px 0px;">Current IP Address: '.$_SERVER['REMOTE_ADDR'].'</div>';
print '<table border="0" cellspacing="2" cellpadding="4">';
print '<tr><td valign="top" class="adminhead">Name</td><td valign="top" class="adminhead">&nbsp;</td></tr>';

$query3 = "SELECT DISTINCT IPAddress FROM cscan_user_tracker";
$result3 = $DRW->query($query3,$DRW_read);
while($data3 = $DRW->fetch_row($result3)){
	$IPAddress = $data3[0];
	
	$query4 = "SELECT location_text,IPAddress,short_location_text FROM cscan_ip_location WHERE IPAddress='".$DRW->real_escape_string($IPAddress)."'";
	$result4 = $DRW->query($query4,$DRW_read);
	$data4 = $DRW->fetch_row($result4);
	$location_text = $data4[0];
	$check = $data4[1];
	$short_location_text = $data4[2];
	
	if($short_location_text==''){
		if($check==''){
			$location_text = shell_exec('whois -a '.escapeshellarg($IPAddress));
		}
		if(preg_match('/(\\r?\\n|\\r)[^\\n\\r]*city:?([^\\n\\r]+)(\\r?\\n|\\r)/i',$location_text,$matches)){
			$temp = trim($matches[2]);
			if($temp!='' && $temp!=':'){
				$short_location_text = $temp;
			}
		}
		if($short_location_text==''){
			//if(preg_match('/HIGHLAND\\s+GROUP\\s+INC/i',$location_text,$matches)){
			if(preg_match('/\\[whois.arin.net\\](\\r?\\n|\\r)([^\\n\\r]+)(\\r?\\n|\\r)([^\\n\\r]+)(\\r?\\n|\\r)([^\\n\\r]+)\\s*\\(/i',$location_text,$matches)){
				$short_location_text = trim($matches[6]);
			}
			elseif(preg_match('/\\[whois.arin.net\\](\\r?\\n|\\r)([^\\n\\r]+)\\s*\\(/i',$location_text,$matches)){
				$short_location_text = trim($matches[2]);
			}
			elseif(preg_match('/(\\r?\\n|\\r)[^\\n\\r]*org-name:?([^\\n\\r]+)(\\r?\\n|\\r)/i',$location_text,$matches)){
				$short_location_text = trim($matches[2]);
			}
			elseif(preg_match('/(\\r?\\n|\\r)[^\\n\\r]*organization;i:?([^\\n\\r]+)(\\r?\\n|\\r)/i',$location_text,$matches)){
				$short_location_text = trim($matches[2]);
			}
			else {
				$short_location_text = '???';
			}
		}
		if($check==''){
			$last_search_sql = "REPLACE INTO cscan_ip_location (IPAddress,location_text,ipl_date,short_location_text) VALUES ('".$DRW->real_escape_string($IPAddress)."','".$DRW->real_escape_string($location_text)."',NOW(),'".$DRW->real_escape_string($short_location_text)."')";
			$DRW->query($last_search_sql,$DRW_main);
		}
		else{
			$last_search_sql = "UPDATE cscan_ip_location SET short_location_text='".$DRW->real_escape_string($short_location_text)."' WHERE IPAddress='".$DRW->real_escape_string($IPAddress)."'";
			$DRW->query($last_search_sql,$DRW_main);
		}
	}
}

$query = "SELECT DISTINCT cu.userID,firstName,lastName,emailAddress,companyName
FROM 
(SELECT t1.userID,COUNT(*) as cnt FROM (SELECT DISTINCT userID,short_location_text FROM cscan_user_tracker ut, cscan_ip_location il WHERE ut.IPAddress=il.IPAddress 
AND short_location_text<>'DOBRIN ASSOCIATES LTD (2134745-P)' AND short_location_text<>'HIGHLAND GROUP INC-070103115824 SBC07506105616829070103115835' AND short_location_text<>'CHI BRIDGED CIRCUITS SPEK-CHI-BR-32') as t1
GROUP BY t1.userID HAVING cnt>1) as t2
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
$ipLocation = array();
while($data = $DRW->fetch_row($result)){
	$userID = $data[0];
	$firstName = $data[1];
	$lastName = $data[2];
	$emailAddress = $data[3];
	$companyName = $data[4];
	
	if($lastName!='' || $firstName!='') $name = "$lastName, $firstName<br />";
	else $name = '';
	print '<tr><td valign="top" class="bodytext" style="border-top:solid 1px #000000;">'."$name($emailAddress)".'</td><td valign="top" class="bodytext" style="border-top:solid 1px #000000;">';
	print '<table border="0" cellspacing="0" cellpadding="0" width="400">';
	
	$query2 = "SELECT short_location_text,ut.IPAddress,COUNT(*) FROM cscan_user_tracker ut, cscan_ip_location il WHERE ut.IPAddress=il.IPAddress AND userID=$userID AND short_location_text<>'???'AND short_location_text<>'DOBRIN ASSOCIATES LTD (2134745-P)' AND short_location_text<>'HIGHLAND GROUP INC-070103115824 SBC07506105616829070103115835' AND short_location_text<>'CHI BRIDGED CIRCUITS SPEK-CHI-BR-32' GROUP BY short_location_text ORDER BY short_location_text";
	$result2 = $DRW->query($query2,$DRW_read);
	$className = '';
	while($data2 = $DRW->fetch_row($result2)){
		$short_location_text = $data2[0];
		$IPAddress = $data2[1];
		$count = $data2[2];
		
		if($className=='selected-bg') $className='white-bg';
		else $className='selected-bg';
			
		print '<tr class="'.$className.'"><td valign="top" class="bodytext"><a href="#" onclick="var win = window.open(\'ip_location.php?ip='.urlencode($IPAddress).'\',\'ipdetail\',\'toolbar=no, menubar=no, location=no, status=no, scrollbars=yes, resizable=yes, width=500, height=400\'); win.focus(); return false;">'.$short_location_text.'</a></td><td valign="top" class="bodytext" width="10%">'.$count.'</td></tr>';
	}
	$query2 = "SELECT short_location_text,ut.IPAddress,COUNT(*) FROM cscan_user_tracker ut, cscan_ip_location il WHERE ut.IPAddress=il.IPAddress AND userID=$userID AND short_location_text='???' GROUP BY ut.IPAddress ORDER BY date";
	$result2 = $DRW->query($query2,$DRW_read);
	$className = '';
	while($data2 = $DRW->fetch_row($result2)){
		$short_location_text = $data2[0];
		$IPAddress = $data2[1];
		$count = $data2[2];
		
		if($className=='selected-bg') $className='white-bg';
		else $className='selected-bg';
			
		print '<tr class="'.$className.'"><td valign="top" class="bodytext"><a href="#" onclick="var win = window.open(\'ip_location.php?ip='.urlencode($IPAddress).'\',\'ipdetail\',\'toolbar=no, menubar=no, location=no, status=no, scrollbars=yes, resizable=yes, width=500, height=400\'); win.focus(); return false;">'.$short_location_text.'</a></td><td valign="top" class="bodytext" width="10%">'.$count.'</td></tr>';
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