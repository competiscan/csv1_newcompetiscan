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
[<a href="manageUserAccounts.php">Manage IP Addresses by Count</a>] &nbsp; [<a href="manageUserLog.php">Manage IP Addresses by Date</a>] &nbsp; [<a href="manageUserLog2.php">Logins by IP Address</a>] &nbsp; [<a href="manageUserLog3.php">Logins by Browser</a>] &nbsp; [<a href="manageUserLog5.php">Logins by IP Location</a>]
<?php
print '<div style="margin:6px 0px 6px 0px;">Current IP Address: '.$_SERVER['REMOTE_ADDR'].'</div>';
print '<table border="0" cellspacing="2" cellpadding="4">';
print '<tr><td valign="top" class="adminhead">Name</td><td valign="top" class="adminhead">&nbsp;</td></tr>';
$query = "SELECT DISTINCT cu.userID,firstName,lastName,emailAddress,companyName
FROM cscan_users cu
JOIN
(SELECT t1.userID,COUNT(*) as cnt1 FROM (SELECT DISTINCT userID,LEFT(date,10) as ldate,LEFT(IPAddress,9) FROM cscan_user_tracker) as t1 GROUP BY t1.userID,t1.ldate HAVING cnt1>1) as t2
ON(cu.userID=t2.userID)
JOIN
(SELECT t3.userID,COUNT(*) as cnt3 FROM (SELECT DISTINCT userID,LEFT(date,10) as ldate,cookie_code FROM cscan_user_tracker WHERE cookie_code<>0) as t3 GROUP BY t3.userID,t3.ldate HAVING cnt3>1) as t4
ON(cu.userID=t4.userID)
WHERE emailAddress NOT LIKE '%@competiscan.com' AND emailAddress NOT LIKE '%@suntecindia.com' AND emailAddress NOT LIKE '%@highlandsolutions.com' AND emailAddress NOT LIKE '%@chicagorecords.com'
ORDER BY lastName,firstName,emailAddress";
$result = $DRW->query($query,$DRW_read);

$numrows = $DRW->num_rows($result);   
$query .= " LIMIT $p,$limit";
$result = $DRW->query($query,$DRW_read);
$resultCount = $DRW->num_rows($result);

$ipLocation = array();
$currdate = '';
$datecount = 0;
while($data = $DRW->fetch_row($result)){
	$userID = $data[0];
	$firstName = $data[1];
	$lastName = $data[2];
	$emailAddress = $data[3];
	$companyName = $data[4];
	
	if($lastName!='' || $firstName!='') $name = "$lastName, $firstName<br />";
	else $name = '';
	print '<tr><td valign="top" class="bodytext">'."$name($emailAddress)".'</td><td valign="top" class="bodytext">';
	
	$query2 = "SELECT t1.ldate,COUNT(*) as cnt FROM (SELECT DISTINCT LEFT(date,10) as ldate,LEFT(IPAddress,9),cookie_code FROM cscan_user_tracker WHERE userID=$userID) as t1
		GROUP BY t1.ldate HAVING cnt>1 ORDER BY t1.ldate";
	$result2 = $DRW->query($query2,$DRW_read);
	$currip = '';
	$className = '';
	$usedIP = array();
	$usedCookie = array();
	$ip = '';
	$cook = '';
	print '<table border="0" cellspacing="0" cellpadding="0" width="500">';
	while($data2 = $DRW->fetch_row($result2)){
		$ldate = $data2[0];
		
		if($className=='selected-bg') $className='white-bg';
		else $className='selected-bg';
			
		print '<tr class="'.$className.'"><td valign="top" class="bodytext" width="20%">'.$ldate.'</td><td valign="top" class="bodytext"><table border="0" cellspacing="0" cellpadding="0" width="100%">';
		$currip = '';
		$query3 = "SELECT DISTINCT IPAddress,DATE_FORMAT(loginTime,'%l:%i %p'),cookie_code FROM cscan_user_tracker WHERE userID=$userID AND date='$ldate' ORDER BY loginTime";
		$result3 = $DRW->query($query3,$DRW_read);
		while($data3 = $DRW->fetch_row($result3)){
			$IPAddress = $data3[0];
			$time = $data3[1];
			$cookie_code = $data3[2];
			
			$combo = $IPAddress.$cookie_code;
			
			if($combo!=$currip){
				$currip = $combo;
				if(in_array($IPAddress,$usedIP)){
					$ip = "$IPAddress *";
				}
				else {
					$usedIP[] = $IPAddress;
					$ip = $IPAddress;
					
					$inum = array_search($IPAddress,$ipLocation);
					if($inum===false){
						$query4 = "SELECT location_text,IPAddress,short_location_text FROM cscan_ip_location WHERE IPAddress='".$DRW->real_escape_string($IPAddress)."'";
						$result4 = $DRW->query($query4,$DRW_read);
						$data4 = $DRW->fetch_row($result4);
						$location_text = $data4[0];
						$check = $data4[1];
						$short_location_text = $data4[2];
						
						if($short_location_text!=''){
							$ipLocation[$IPAddress] = $short_location_text;
						}
						else{
							if($check==''){
								$location_text = shell_exec('whois -a '.escapeshellarg($IPAddress));
							}
							if(preg_match('/(\\r?\\n|\\r)[^\\n\\r]*city:?([^\\n\\r]+)(\\r?\\n|\\r)/i',$location_text,$matches)){
								$temp = trim($matches[2]);
								if($temp!='' && $temp!=':'){
									$ipLocation[$IPAddress] = $temp;
								}
							}
							if(!isset($ipLocation[$IPAddress])){
								//if(preg_match('/HIGHLAND\\s+GROUP\\s+INC/i',$location_text,$matches)){
								if(preg_match('/\\[whois.arin.net\\](\\r?\\n|\\r)([^\\n\\r]+)(\\r?\\n|\\r)([^\\n\\r]+)(\\r?\\n|\\r)([^\\n\\r]+)\\s*\\(/i',$location_text,$matches)){
									$ipLocation[$IPAddress] = trim($matches[6]);
								}
								elseif(preg_match('/\\[whois.arin.net\\](\\r?\\n|\\r)([^\\n\\r]+)\\s*\\(/i',$location_text,$matches)){
									$ipLocation[$IPAddress] = trim($matches[2]);
								}
								elseif(preg_match('/(\\r?\\n|\\r)[^\\n\\r]*org-name:?([^\\n\\r]+)(\\r?\\n|\\r)/i',$location_text,$matches)){
									$ipLocation[$IPAddress] = trim($matches[2]);
								}
								elseif(preg_match('/(\\r?\\n|\\r)[^\\n\\r]*organization;i:?([^\\n\\r]+)(\\r?\\n|\\r)/i',$location_text,$matches)){
									$ipLocation[$IPAddress] = trim($matches[2]);
								}
								else {
									$ipLocation[$IPAddress] = '???';
								}
							}
							if($check==''){
								$last_search_sql = "REPLACE INTO cscan_ip_location (IPAddress,location_text,ipl_date,short_location_text) VALUES ('".$DRW->real_escape_string($IPAddress)."','".$DRW->real_escape_string($location_text)."',NOW(),'".$DRW->real_escape_string($ipLocation[$IPAddress])."')";
								$DRW->query($last_search_sql,$DRW_main);
							}
							else{
								$last_search_sql = "UPDATE cscan_ip_location SET short_location_text='".$DRW->real_escape_string($ipLocation[$IPAddress])."' WHERE IPAddress='".$DRW->real_escape_string($IPAddress)."'";
								$DRW->query($last_search_sql,$DRW_main);
							}
						}
					}
				}
				$ip = '<a href="#" onclick="var win = window.open(\'ip_location.php?ip='.urlencode($IPAddress).'\',\'ipdetail\',\'toolbar=no, menubar=no, location=no, status=no, scrollbars=yes, resizable=yes, width=500, height=400\'); win.focus(); return false;">'.$ipLocation[$IPAddress].'</a><br />'.$ip;
				
				if($cookie_code==0){
					$cook = "&nbsp;";
				}
				else{
					$cnum = array_search($cookie_code,$usedCookie);
					if($cnum===false){
						$usedCookie[] = $cookie_code;
						$cnum = count($usedCookie);
						$cook = "Browser #$cnum";
					}
					else{
						$cnum++;
						$cook = "Browser #$cnum";
					}
				}
			}
			else continue;
			
			print "<tr><td width=\"15%\" valign=\"top\" class=\"bodytext\">$time</td><td width=\"40%\" valign=\"top\" class=\"bodytext\">$ip</td><td width=\"20%\" valign=\"top\" class=\"bodytext\">$cook</td></tr>";
		}
		print '</table></td></tr>';
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