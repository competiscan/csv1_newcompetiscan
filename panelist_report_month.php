<?php 
$TITLE = 'Competiscan Report';
require_once('panelist_top.php');
require_once('includes/ehLog.php');

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

$countryr = '';
$start_mr = '00';
$start_dr = '00';
$start_yr = '0000';
$end_mr = '00';
$end_dr = '00';
$end_yr = '0000';
$start_year = 2007;
$to_year = (int)date('Y');
$orderby = '';
$do_points_sent = 1;

if(isset($_REQUEST['search_text'])) {
	$_SESSION['search_text_ri'] = $_REQUEST['search_text'];
} 
elseif(isset($_REQUEST['show_All']) || !isset($_SESSION['search_text_ri'])) {
	$_SESSION['search_text_ri'] = '';
}

if(isset($_REQUEST['sendsearch'])){
	if(!isset($_REQUEST['clear'])){
		$countryr = $_REQUEST['countryr'];
		$start_mr = $_REQUEST['start_mr'];
		$start_dr = $_REQUEST['start_dr'];
		$start_yr = $_REQUEST['start_yr'];
		$end_mr = $_REQUEST['end_mr'];
		$end_dr = $_REQUEST['end_dr'];
		$end_yr = $_REQUEST['end_yr'];
		if(isset($_REQUEST['do_points_sent'])) $do_points_sent = (int)$_REQUEST['do_points_sent'];
		else $do_points_sent = 0;
	}
	$_SESSION['countryr'] = $countryr;
	$_SESSION['start_mr'] = $start_mr;
	$_SESSION['start_dr'] = $start_dr;
	$_SESSION['start_yr'] = $start_yr;
	$_SESSION['end_mr'] = $end_mr;
	$_SESSION['end_dr'] = $end_dr;
	$_SESSION['end_yr'] = $end_yr;
	$_SESSION['do_points_sent'] = $do_points_sent;
}
else{
	$tstamp = mktime(0,0,0,(int)date('n')-1,1,$to_year);
	$m = date('m',$tstamp);
	$Y = date('Y',$tstamp);
	
	if(isset($_SESSION['countryr'])) $countryr = $_SESSION['countryr'];
	else $_SESSION['countryr'] = $countryr = 'US';
	if(isset($_SESSION['start_mr'])) $start_mr = $_SESSION['start_mr'];
	else $_SESSION['start_mr'] = $start_mr = $m;
	if(isset($_SESSION['start_dr'])) $start_dr = $_SESSION['start_dr'];
	else $_SESSION['start_dr'] = $start_dr = '01';
	if(isset($_SESSION['start_yr'])) $start_yr = $_SESSION['start_yr'];
	else $_SESSION['start_yr'] = $start_yr = $Y;
	
	if(isset($_SESSION['end_mr'])) $end_mr = $_SESSION['end_mr'];
	elseif($ctype==1) $_SESSION['end_mr'] = $end_mr = $m;
	if(isset($_SESSION['end_dr'])) $end_dr = $_SESSION['end_dr'];
	elseif($ctype==1) $_SESSION['end_dr'] = $end_dr = '31';
	if(isset($_SESSION['end_yr'])) $end_yr = $_SESSION['end_yr'];
	elseif($ctype==1) $_SESSION['end_yr'] = $end_yr = $Y;
	
	if(isset($_SESSION['do_points_sent'])) $do_points_sent = $_SESSION['do_points_sent'];
	else $_SESSION['do_points_sent'] = $do_points_sent;
}

print "<div style=\"margin-bottom:4px;\">
<form action=\"{$_SERVER['PHP_SELF']}\" style=\"display:inline;\" method=\"post\" name=\"checker\"><input class=\"button\" type=\"submit\" name=\"checkmail\" value=\" Mail \" onclick=\"document.location.href='imap.php'; return false;\" /></form>";
print " &nbsp; <form action=\"panelist_report_iframe_month.php\" style=\"display:inline;\" method=\"get\" name=\"exer\"><input class=\"button\" type=\"submit\" name=\"exportbutton\" value=\"Export\" />
<input type=\"hidden\" name=\"export\" value=\"1\" />
<input type=\"hidden\" name=\"countryr\" value=\"$countryr\" />
<input type=\"hidden\" name=\"start_mr\" value=\"$start_mr\" />
<input type=\"hidden\" name=\"start_dr\" value=\"$start_dr\" />
<input type=\"hidden\" name=\"start_yr\" value=\"$start_yr\" />
<input type=\"hidden\" name=\"end_mr\" value=\"$end_mr\" />
<input type=\"hidden\" name=\"end_dr\" value=\"$end_dr\" />
<input type=\"hidden\" name=\"end_yr\" value=\"$end_yr\" />
<input type=\"hidden\" name=\"do_points_sent\" value=\"$do_points_sent\" />
</form> &nbsp; ";
if(running_php_cmd('update_panelist_report.php')){
	print "<em>Update In Process . . .</em>";
}
else{
	print "<form action=\"{$_SERVER['PHP_SELF']}\" style=\"display:inline;\" method=\"post\" name=\"counter\"><input class=\"button\" type=\"submit\" name=\"go\" value=\" Update Current Month \" onclick=\"document.location.href='panelist_report_iframe_update.php?upr=1'; return false;\" /> &nbsp; <input class=\"button\" type=\"submit\" name=\"go2\" value=\" Update Previous Month \" onclick=\"document.location.href='panelist_report_iframe_update.php?upr=2'; return false;\" /></form>";
}
print " &nbsp; <form action=\"{$_SERVER['PHP_SELF']}\" style=\"display:inline;\" method=\"post\" name=\"older\"><input class=\"button\" type=\"submit\" name=\"go\" value=\" Report by Day \" onclick=\"document.location.href='panelist_report.php'; return false;\" /></form>";
//print " &nbsp; <form action=\"panelist_report_iframe_month.php\" style=\"display:inline;\" method=\"get\" name=\"uper\"><input class=\"button\" type=\"submit\" name=\"updater\" value=\"Update CRM Points\" /><input type=\"hidden\" name=\"update\" value=\"1\" /></form>";
if(isset($_GET['updated'])){
	print ' &nbsp; &nbsp; <span class="error">Updated';
	if($_GET['updated']==2) print ' CRM';
	print '</span>';
}
print "</div>";
print "<div style=\"margin-top:4px;padding:2px;background-color:#E8E8FF;\">";
print "<form name=\"searcher\" method=\"post\" action=\"{$_SERVER['PHP_SELF']}\"><table border=\"0\" cellpadding=\"4\" cellspacing=\"2\">";
print '<tr><td class="bodytext">Country</td><td colspan="2" class="bodytext"><label><input type="radio" name="countryr" value=""';
if(empty($countryr)) {
	echo " checked=\"checked\"";
}
print ' />All</label>';
$sqlc = "SELECT DISTINCT countryCode,country FROM cscan_state JOIN ISO31661_alpha2code ON (code=countryCode) ORDER BY country";
$rsc = $DRW->query( $sqlc,$DRW_read );
while($rowc = $DRW->fetch_row($rsc) ) {
	print ' <label><input type="radio" name="countryr" value="'.$rowc[0].'"';
	if($countryr==$rowc[0]) {
		echo " checked=\"checked\"";
	}
	print ' />'.htmlspecialchars($rowc[1]).'</label>';
}
echo '</td></tr>';
print "<tr><td class=\"bodytext\">From</td><td><select name=\"start_mr\" size=\"1\" class=\"textinput\"><option value=\"00\">&nbsp;</option>";
$month_name = array('01'=>"January",'02'=>"February",'03'=>"March",'04'=>"April",'05'=>"May",'06'=>"June",'07'=>"July",'08'=>"August",'09'=>"September",'10'=>"October",'11'=>"November",'12'=>"December");
foreach($month_name as $key=>$value){
	print "<option value=\"$key\"";
	if($key==$start_mr) print " selected=\"selected\"";
	print ">$value ($key)</option>";
}
print "</select> <input type=\"hidden\" name=\"start_dr\" value=\"$start_dr\" /> <select name=\"start_yr\" size=\"1\" class=\"textinput\"><option value=\"0000\">&nbsp;</option>";
for($i=$start_year;$i<=$to_year;$i++){
	print "<option value=\"$i\"";
	if($i==$start_yr) print " selected=\"selected\"";
	print ">$i</option>";
}
print "</select></td><td>";
for($i=11;$i>=0;$i--){
	$tstamp = mktime(0,0,0,(int)date('n')-$i,1,$to_year);
	$m = date('m',$tstamp);
	$Y = date('Y',$tstamp);
	print "<a href=\"{$_SERVER['PHP_SELF']}?countryr=$countryr&amp;start_mr=$m&amp;start_dr=01&amp;start_yr=$Y&amp;end_mr=$m&amp;end_dr=31&amp;end_yr=$Y&amp;do_points_sent=0&amp;sendsearch=1\" class=\"HyperLink\">{$month_name[$m]}";
	if($Y!=$to_year) print ' '.substr($Y,2);
	print "</a> &nbsp; ";
}
print "</td></tr>
<tr><td class=\"bodytext\">To</td><td><select name=\"end_mr\" size=\"1\" class=\"textinput\"><option value=\"00\">&nbsp;</option>";
foreach($month_name as $key=>$value){
	print "<option value=\"$key\"";
	if($key==$end_mr) print " selected=\"selected\"";
	print ">$value ($key)</option>";
}
print "</select> <input type=\"hidden\" name=\"end_dr\" value=\"$end_dr\" /> <select name=\"end_yr\" size=\"1\" class=\"textinput\"><option value=\"0000\">&nbsp;</option>";
for($i=$start_year;$i<=$to_year;$i++){
	print "<option value=\"$i\"";
	if($i==$end_yr) print " selected=\"selected\"";
	print ">$i</option>";
}
print "</select></td><td class=\"bodytext\">";
if($ctype==2){
	echo '<label><input type="checkbox" name="do_points_sent" value="1"';
	if($do_points_sent==1){
		echo ' checked="checked"';
	}
	echo ' />Include Offset</label> &nbsp; ';
}
print "<input class=\"button\" type=\"submit\" name=\"search\" value=\"Search\" /> &nbsp; <input class=\"button\" type=\"submit\" name=\"clear\" value=\"Clear\" /></td></tr>";

print "</table>
<input type=\"hidden\" name=\"sendsearch\" value=\"1\" /></form>
</div>";

$n = 0;
$last  = count($contactType) - 1;
foreach($contactType as $contact_type_m_c=>$contact_type_m_cTitle){
	if($ctype==$n){
		print "<span class=\"headings\">$contact_type_m_cTitle</span>";
		if($ctype==2){
			echo ' <span class="text">(Note: April 1, 2009 start)</span>';
		}
	}
	else print "<a href=\"{$_SERVER['PHP_SELF']}?ctype=$n\" class=\"bluelink\">$contact_type_m_cTitle</a>";
	if($n!=$last) print ' &nbsp; | &nbsp; ';
	$n++;
}

if($start_yr!='0000'){
	$month_year_title = "$end_mr/$end_yr";
	$exportitle = date('m/d/Y')." [From $start_yr-$start_mr-$start_dr To $end_yr-$end_mr-$end_dr]";
}
else {
	$month_year_Y = date('Y');
	$month_year_m = date('m');
	$month_year_title = "$month_year_m/$month_year_Y";
	$exportitle = date('m/d/Y');
}

function sortLinks($sel,$dis,$text,$orderby1='',$orderby2=''){
	if($sel==$dis){
		print "<a href=\"{$_SERVER['PHP_SELF']}?sortr=-$dis\" class=\"topLinks\">$text <img src=\"images/down.gif\" border=\"0\" style=\"vertical-align:bottom;\" width=\"15\" height=\"15\" /></a>";//&darr;
		//$GLOBALS['orderby'] = $orderby1;
	}
	else{
		print "<a href=\"{$_SERVER['PHP_SELF']}?sortr=$dis\" class=\"topLinks\">$text";
		if(abs($sel)==$dis){
			print " <img src=\"images/up.gif\" border=\"0\" style=\"vertical-align:bottom;\" width=\"15\" height=\"15\" />";//&uarr;
			//$GLOBALS['orderby'] = $orderby2;
		}
		print "</a>";
	}
}

$colspan = 7;
print "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"950\">";
print "<tr><td style=\"background:#0055E3;color:#ffffff;padding:4px;\" class=\"text\" valign=\"bottom\">";
sortLinks($sortr,1,'First Name');
print " &nbsp; ";
sortLinks($sortr,10,'Last Name');
print " &nbsp; ";
sortLinks($sortr,8,'(ID)');
print "<br />";
sortLinks($sortr,6,'&lt;Email Address&gt;');
print "</td>";
if($ctype!=2){
	print "<td style=\"background:#0055E3;color:#ffffff;padding:4px;\" class=\"text\" valign=\"bottom\" width=\"50\">";
	sortLinks($sortr,2,'Used<br />Emails');
	print "</td><td style=\"background:#0055E3;color:#ffffff;padding:4px;\" class=\"text\" valign=\"bottom\" width=\"55\">";
	sortLinks($sortr,3,'Unused<br />Emails');
	print "</td>";
	$colspan+=2;
}
elseif($sortr==2 || $sortr==3 || $sortr==4){
	$orderby = ' ORDER BY last_name,first_name';
}
print "<td style=\"background:#0055E3;color:#ffffff;padding:4px;\" class=\"text\" valign=\"bottom\" width=\"50\">";
sortLinks($sortr,4,'Total<br />Emails');
print "</td><td style=\"background:#0055E3;color:#ffffff;padding:4px;\" class=\"text\" valign=\"bottom\" width=\"50\">";
sortLinks($sortr,5,'Email<br />Points');
print "</td><td style=\"background:#0055E3;color:#ffffff;padding:4px;\" class=\"text\" valign=\"bottom\"";
if($ctype==2) {
	print  " width=\"60\">";
	$dmtitle = 'Envelope<br />Points';
}
else {
	print  " width=\"50\">";
	$dmtitle = 'Direct<br />Mail<br />Points';
}
sortLinks($sortr,7,$dmtitle);
print "</td>";
if($ctype==2){
	print "<td style=\"background:#0055E3;color:#ffffff;padding:4px;\" class=\"text\" valign=\"bottom\" width=\"60\">";
	sortLinks($sortr,11,'Retrieval<br />Points');
	print "</td>";
	$colspan++;
}
print "<td style=\"background:#0055E3;color:#ffffff;padding:4px;\" class=\"text\" valign=\"bottom\" width=\"50\">";
sortLinks($sortr,9,'Total<br />Points');
print "</td>
<td style=\"background:#0055E3;color:#ffffff;padding:4px;\" class=\"text\" valign=\"bottom\" width=\"75\">";
sortLinks($sortr,12,'Envelopes<br />Left');
print "</td>
<td style=\"background:#0055E3;color:#ffffff;padding:4px;\" class=\"text\" valign=\"bottom\"";
if($ctype==2) {
	print " width=\"90\">";
	$point_typeArray = array(1=>'Envelope',2=>'Retrieval');
	print "<form action=\"{$_SERVER['PHP_SELF']}\" style=\"display:inline;padding:0px;margin:0px;\" method=\"post\" name=\"pointy\"><select name=\"point_type\" size=\"1\" class=\"textinput\" onchange=\"fixIframe();\">";
	foreach($point_typeArray as $k=>$pt){
		print "<option value=\"$k\">$pt</option>";
	}
	print "</select></form> &nbsp; 2 &nbsp; &nbsp; &nbsp; &nbsp; 50";
}
else{
	print " width=\"75\">";
	print '<span class="topLinks">'.$month_year_title.'</span>';
}
print "</td><td style=\"background:#0055E3;color:#ffffff;padding:4px;\" class=\"text\" valign=\"bottom\" width=\"100\">Notes</td></tr>";
print '</table>';
print "<iframe name=\"rlist\" style=\"border: solid 1px #0055E3;padding:0px;margin:0px;\" src=\"panelist_report_iframe_month.php\" width=\"970\" height=\"500\" frameborder=\"0\" marginheight=\"0\" marginwidth=\"0\" scrolling=\"auto\"></iframe>";
?>
<div class="bodytext" style="margin-top:5px;">
<form method="post" name="searchForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" style="display:inline;">
<input type="text" name="search_text" size="120" class="input_box" value="<?php echo htmlspecialchars($_SESSION['search_text_ri'],ENT_COMPAT); ?>" />
<input class="button" style="width:60px" type="submit" name="search_Submit1" value="Search" />
<input type="hidden" name="search_Submit" value="1" /><input type="hidden" name="page" value="0" /></form>
&nbsp;&nbsp;
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" style="display:inline;">
<input class="button" style="width:70px" type="submit" name="show_All1" value="Show All" />
<input type="hidden" name="show_All" value="1" /><input type="hidden" name="page" value="0" /></form>
</div>
<?php
print "<script type=\"text/JavaScript\">
<!--
function fixIframe(){
	var ind = document.pointy.point_type.selectedIndex;
	if(ind>=0 && window.rlist && window.rlist.document.directer && window.rlist.document.directer.point_type){
		window.rlist.document.directer.point_type.value = document.pointy.point_type.options[ind].value;
	}
}
//-->
</script>";

require_once('panelist_bottom.php');
?>