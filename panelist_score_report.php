<?php 
$TITLE = 'Competiscan Report';
require_once('panelist_top.php');

if(isset($_GET['ctype'])) {
	$_SESSION['ctype'] = $ctype = (int)$_GET['ctype'];
	setcookie('competiscan_content_contact',$ctype,time()+(86400*364),$COOKIEPATH,$COOKIEDOMAIN);
}
else {
	if(isset($_SESSION['ctype'])) $ctype = $_SESSION['ctype'];
	else $_SESSION['ctype'] = $ctype = 2;
}

if(isset($_GET['sortr'])) $_SESSION['sortr'] = $sortr = (int)$_GET['sortr'];
else {
	if(isset($_SESSION['sortr'])) $sortr = $_SESSION['sortr'];
	else $_SESSION['sortr'] = $sortr = 1;
}

print "<div style=\"margin-bottom:4px;\"><form action=\"{$_SERVER['PHP_SELF']}\"><input class=\"button\" type=\"submit\" name=\"checkmail\" value=\"Check Mail\" onclick=\"document.location.href='imap.php'; return false;\" /></form></div>";

$start_mr = '00';
$start_dr = '00';
$start_yr = '0000';
$end_mr = '00';
$end_dr = '00';
$end_yr = '0000';

if(isset($_POST['sendsearch'])){
	if(!isset($_POST['clear'])){
		$start_mr = $_POST['start_mr'];
		$start_dr = $_POST['start_dr'];
		$start_yr = $_POST['start_yr'];
		$end_mr = $_POST['end_mr'];
		$end_dr = $_POST['end_dr'];
		$end_yr = $_POST['end_yr'];
	}
	$_SESSION['start_mr'] = $start_mr;
	$_SESSION['start_dr'] = $start_dr;
	$_SESSION['start_yr'] = $start_yr;
	$_SESSION['end_mr'] = $end_mr;
	$_SESSION['end_dr'] = $end_dr;
	$_SESSION['end_yr'] = $end_yr;
}
else{
	if(isset($_SESSION['start_mr'])) $start_mr = $_SESSION['start_mr'];
	if(isset($_SESSION['start_dr'])) $start_dr = $_SESSION['start_dr'];
	if(isset($_SESSION['start_yr'])) $start_yr = $_SESSION['start_yr'];
	if(isset($_SESSION['end_mr'])) $end_mr = $_SESSION['end_mr'];
	if(isset($_SESSION['end_dr'])) $end_dr = $_SESSION['end_dr'];
	if(isset($_SESSION['end_yr'])) $end_yr = $_SESSION['end_yr'];
}
	
print "<div style=\"margin-top:4px;padding:2px;background-color:#E8E8FF;\">";
print "<form name=\"searcher\" method=\"post\" action=\"{$_SERVER['PHP_SELF']}\"><table border=\"0\" cellpadding=\"4\" cellspacing=\"2\">";
print "<tr><td class=\"bodytext\">From</td><td><select name=\"start_mr\" size=\"1\" class=\"textinput\" onchange=\"checkStart();\"><option value=\"00\"></option>";
$month_name = array('01'=>"January",'02'=>"February",'03'=>"March",'04'=>"April",'05'=>"May",'06'=>"June",'07'=>"July",'08'=>"August",'09'=>"September",'10'=>"October",'11'=>"November",'12'=>"December");
foreach($month_name as $key=>$value){
	print "<option value=\"$key\"";
	if($key==$start_mr) print " selected=\"selected\"";
	print ">$value ($key)</option>";
}
print "</select> <select name=\"start_dr\" size=\"1\" class=\"textinput\" onchange=\"checkStart();\"><option value=\"00\"></option>";
for($i=1;$i<=31;$i++){
	$day = str_pad($i,2,'0',STR_PAD_LEFT);
	print "<option value=\"$day\"";
	if($day==$start_dr) print " selected=\"selected\"";
	print ">$day</option>";
}
$start_year = 2007;
$to_year = (int)date('Y');
print "</select> <select name=\"start_yr\" size=\"1\" class=\"textinput\" onchange=\"checkStart();\"><option value=\"0000\"></option>";
for($i=$start_year;$i<=$to_year;$i++){
	print "<option value=\"$i\"";
	if($i==$start_yr) print " selected=\"selected\"";
	print ">$i</option>";
}
print "</select></td><td rowspan=\"2\" valign=\"bottom\"><input class=\"button\" type=\"submit\" name=\"search\" value=\"Search\" /> &nbsp; <input class=\"button\" type=\"submit\" name=\"clear\" value=\"Clear\" /></td></tr>
<tr><td class=\"bodytext\">To</td><td><select name=\"end_mr\" size=\"1\" class=\"textinput\" onchange=\"checkStart();\"><option value=\"00\"></option>";
foreach($month_name as $key=>$value){
	print "<option value=\"$key\"";
	if($key==$end_mr) print " selected=\"selected\"";
	print ">$value ($key)</option>";
}
print "</select> <select name=\"end_dr\" size=\"1\" class=\"textinput\" onchange=\"checkStart();\"><option value=\"00\"></option>";
for($i=1;$i<=31;$i++){
	$day = str_pad($i,2,'0',STR_PAD_LEFT);
	print "<option value=\"$day\"";
	if($day==$end_dr) print " selected=\"selected\"";
	print ">$day</option>";
}
print "</select> <select name=\"end_yr\" size=\"1\" class=\"textinput\" onchange=\"checkStart();\"><option value=\"0000\"></option>";
for($i=$start_year;$i<=$to_year;$i++){
	print "<option value=\"$i\"";
	if($i==$end_yr) print " selected=\"selected\"";
	print ">$i</option>";
}
print "</select></td></tr>";

print "</table>
<input type=\"hidden\" name=\"sendsearch\" value=\"1\" /></form>
</div>";

$n = 0;
$cwhere = '';
$last  = count($contactType) - 1;
foreach($contactType as $contact_type_m_c=>$contact_type_m_cTitle){
	if($ctype==$n){
		if($n>0) $cwhere .= " WHERE ce1.`contact_type_m_c`='{$contact_type_m_c}'";
		print "<span class=\"headings\">$contact_type_m_cTitle</span>";
	}
	else print "<a href=\"{$_SERVER['PHP_SELF']}?ctype=$n\" class=\"bluelink\">$contact_type_m_cTitle</a>";
	if($n!=$last) print ' &nbsp; | &nbsp; ';
	$n++;
}

if($start_yr!='0000'){
	if($cwhere=='') $cwhere .= " WHERE";
	else $cwhere .= " AND";
	$cwhere .= " ce1.`email_date`>='".$DRW->real_escape_string("$start_yr-$start_mr-$start_dr")." 00:00:00'";
	$cwhere .= " AND ce1.`email_date`<='".$DRW->real_escape_string("$end_yr-$end_mr-$end_dr")." 23:59:59'";
}

print "<table border=\"1\" cellpadding=\"4\" cellspacing=\"0\" bordercolor=\"#0055E3\" class=\"likeresults\">";
print "<tr><td style=\"background:#0055E3;color:#ffffff;\" class=\"text\">";
if($sortr!=1) print " <a href=\"{$_SERVER['PHP_SELF']}?sortr=1\" class=\"topLinks\">Contact</a>";
else {
	print '<span style="text-decoration:underline;" class="topLinks">Contact</span>';
	$orderby = '';
}

if($sortr!=6) print " &nbsp; &lt;<a href=\"{$_SERVER['PHP_SELF']}?sortr=6\" class=\"topLinks\">Email</a>&gt;";
else {
	print ' &nbsp; &lt;<span style="text-decoration:underline;" class="topLinks">Email</span>&gt;';
	$orderby = ' ORDER BY ce1.`email_from_one`';
}
print "</td><td style=\"background:#0055E3;color:#ffffff;\" class=\"text\">";
if($sortr!=2) print " <a href=\"{$_SERVER['PHP_SELF']}?sortr=2\" class=\"topLinks\">Used</a>";
else {
	print '<span style="text-decoration:underline;" class="topLinks">Used</span>';
	$orderby = ' ORDER BY used DESC,ce1.`email_from_one`';
}
print "</td><td style=\"background:#0055E3;color:#ffffff;\" class=\"text\">";
if($sortr!=3) print " <a href=\"{$_SERVER['PHP_SELF']}?sortr=3\" class=\"topLinks\">Unused</a>";
else {
	print '<span style="text-decoration:underline;" class="topLinks">Unused</span>';
	$orderby = ' ORDER BY unused DESC,ce1.`email_from_one`';
}
print "</td><td style=\"background:#0055E3;color:#ffffff;\" class=\"text\">";
if($sortr!=4) print " <a href=\"{$_SERVER['PHP_SELF']}?sortr=4\" class=\"topLinks\">Total</a>";
else {
	print '<span style="text-decoration:underline;" class="topLinks">Total</span>';
	$orderby = ' ORDER BY totalu DESC,ce1.`email_from_one`';
}
print "</td><td style=\"background:#0055E3;color:#ffffff;\" class=\"text\">";
if($sortr!=5) print " <a href=\"{$_SERVER['PHP_SELF']}?sortr=5\" class=\"topLinks\">Points</a>";
else {
	print '<span style="text-decoration:underline;" class="topLinks">Points</span>';
	if($ctype==2) $orderby = ' ORDER BY points2 DESC,ce1.`email_from_one`';
	else $orderby = ' ORDER BY points DESC,ce1.`email_from_one`';
}
print "</td></tr>";

$idArray = array();
$orderidArray = array();

$query = "SELECT ce1.`email_from_one`,SUM(ce1.`panelist_score`) as points,COUNT(ce2.`deleted`) as unused,COUNT(ce3.`deleted`) as used,
(COUNT(ce3.`deleted`)+COUNT(ce2.`deleted`)) as totalu,IF(SUM(ce1.`panelist_score`)>2000,SUM(ce1.`panelist_score`) % 2000,SUM(ce1.`panelist_score`)) as points2 
FROM `cscan_email` ce1 
LEFT JOIN `cscan_email` ce2 ON(ce1.`muid`=ce2.`muid` AND ce2.`deleted`=1) 
LEFT JOIN `cscan_email` ce3 ON(ce1.`muid`=ce3.`muid` AND ce3.`deleted`=2)
$cwhere GROUP BY ce1.`email_from_one`$orderby";
$query_result = $DRW->query($query,$DRW_read);
while($data = $DRW->fetch_row($query_result)){
	$email_from = $data[0];
	$points = $data[1];
	$unused = $data[2];
	$used = $data[3];
	$totalu = $data[4];
	
	if(preg_match('/<([^<>]+)>/',$email_from,$match)){
		$email_from_email = $match[1];
	}
	else $email_from_email = $email_from;
	$result = $DRW->query("SELECT DISTINCT ct.id,first_name,last_name,title,department,phone_work,phone_other,
		primary_address_street,primary_address_city,primary_address_state,primary_address_postalcode FROM contacts ct,email_addresses ea, email_addr_bean_rel eb
		WHERE ct.deleted<>1 AND bean_id=ct.id AND ea.id=eb.email_address_id AND bean_module='Contacts' AND email_address LIKE '%".$DRW->real_escape_string($email_from_email)."%'",$DRW_crm);
	$data2 = $DRW->fetch_row($result);
	if($data2[0]!=''){
		$id = $data2[0];
		$first_name = $data2[1];
		$last_name = $data2[2];
		$title = $data2[3];
		$department = $data2[4];
		$phone_work = $data2[5];
		$phone_other = $data2[6];
		$primary_address_street = $data2[7];
		$primary_address_city = $data2[8];
		$primary_address_state = $data2[9];
		$primary_address_postalcode = $data2[10];
		
		if(!in_array($id,$idArray)) {
			$idArray[$id] = array();
			$orderidArray[$id] = $last_name.$first_name;
			$idArray[$id]['points'] = 0;
			$idArray[$id]['unused'] = 0;
			$idArray[$id]['used'] = 0;
			$idArray[$id]['totalu'] = 0;
			
			$email_from_txt = $first_name.' '.$last_name;
			if($department!='') {
				$email_from_txt .= ' ('.$department.')';
			}
			
			$q2 = "SELECT email_address FROM email_addresses ea, email_addr_bean_rel eb WHERE bean_id='".$DRW->real_escape_string($id)."' AND ea.id=eb.email_address_id AND bean_module='Contacts' AND primary_address=1";
			$result2 = $DRW->query($q2,$DRW_crm);
		  	$data2 = $DRW->fetch_row($result2);
		    $email1 = $data2[0];
		    
			$email1 = "<a href=\"http://crm.competiscan.com/index.php?action=DetailView&module=Contacts&record=$id\" class=\"bluelink\" target=\"_blank\">$email1</a>";
			$email_from_txt .= ' &lt;'.$email1.'&gt;';
		
			$idArray[$id]['contact'] = $email_from_txt;
		}
		$idArray[$id]['points'] += $points;
		$idArray[$id]['unused'] += $unused;
		$idArray[$id]['used'] += $used;
		$idArray[$id]['totalu'] += $totalu;
	}
}

$tot = 0;
$used_tot = 0;
$unused_tot = 0;
$points_tot = 0;
$totalu_tot = 0;

if($sortr==1) asort($orderidArray);

foreach($orderidArray as $id=>$name){
	$used = $idArray[$id]['used'];
	$unused = $idArray[$id]['unused'];
	$points = $idArray[$id]['points'];
	$totalu = $idArray[$id]['totalu'];
	$email_from_txt = $idArray[$id]['contact'];
	
	if($ctype==2 && $points>2000){
		$actual = " ($points)";
		$points = $points%2000;
	}
	else $actual = '';
	
	if($used>0 || $unused>0 || $points>0) {
		$tot++;
		$used_tot += $used;
		$unused_tot += $unused;
		$points_tot += $points;
		$totalu_tot += $totalu;
		print "<tr><td valign=\"top\" class=\"bodytext\">$email_from_txt</td><td valign=\"top\" class=\"bodytext\">$used</td><td valign=\"top\" class=\"bodytext\">$unused</td><td valign=\"top\" class=\"bodytext\">$totalu</td><td valign=\"top\" class=\"bodytext\">$points$actual</td></tr>";
	}
}
print "<tr><td valign=\"top\" class=\"bodytext\" style=\"border-top:solid 1px #000000;\"><strong>$tot</strong></td><td valign=\"top\" class=\"bodytext\" style=\"border-top:solid 1px #000000;\"><strong>$used_tot</strong></td><td valign=\"top\" class=\"bodytext\" style=\"border-top:solid 1px #000000;\"><strong>$unused_tot</strong></td><td valign=\"top\" class=\"bodytext\" style=\"border-top:solid 1px #000000;\"><strong>$totalu_tot</strong></td><td valign=\"top\" class=\"bodytext\" style=\"border-top:solid 1px #000000;\"><strong>$points_tot</strong></td></tr>";
print '</table>';

print "<script type=\"text/JavaScript\">
<!--
function checkStart(){
	var startindex_d = document.searcher.start_dr.selectedIndex;
	var startindex_m = document.searcher.start_mr.selectedIndex;
	var startindex_y = document.searcher.start_yr.selectedIndex;
	var endindex_d = document.searcher.end_dr.selectedIndex;
	var endindex_m = document.searcher.end_mr.selectedIndex;
	var endindex_y = document.searcher.end_yr.selectedIndex;

	if(startindex_y>endindex_y){
		document.searcher.end_yr.selectedIndex = startindex_y;
		endindex_y = startindex_y;
	}
	if(startindex_m>endindex_m && startindex_y==endindex_y){
		document.searcher.end_mr.selectedIndex = startindex_m;
		endindex_m = startindex_m;
	}
	if(startindex_d>endindex_d && startindex_m==endindex_m && startindex_y==endindex_y){
		document.searcher.end_dr.selectedIndex = startindex_d;
	}
}
//-->
</script>";

require_once('panelist_bottom.php');
?>
