<?php
$TITLE = 'Competiscan Filter';
require_once('panelist_top.php');

if(isset($_POST['send'])){
	$sql = "REPLACE INTO `cscan_filter` SET `domains`='".$DRW->real_escape_string($_POST['domains'])."',`emails`='".$DRW->real_escape_string($_POST['emails'])."',
	`no_domains`='".$DRW->real_escape_string($_POST['no_domains'])."',`no_emails`='".$DRW->real_escape_string($_POST['no_emails'])."',
	`subjects`='".$DRW->real_escape_string($_POST['subjects'])."',`forward_to`='".$DRW->real_escape_string(trim($_POST['forward_to']))."',
	`forward_to_alt`='".$DRW->real_escape_string(trim($_POST['forward_to_alt']))."'";
	$DRW->query($sql,$DRW_main);
	
	ob_end_clean();
	header("Location: {$_SERVER['PHP_SELF']}?done=1");
	exit;
}
else{
	$query2 = "SELECT `domains`,`emails`,`no_domains`,`no_emails`,`subjects`,`forward_to`,`forward_to_alt` FROM `cscan_filter` ORDER BY `filterdate` DESC LIMIT 1";
	$query_result2 = $DRW->query($query2,$DRW_read);
	$data2 = $DRW->fetch_row($query_result2);
	$domains = $data2[0];
	$emails = $data2[1];
	$no_domains = $data2[2];
	$no_emails = $data2[3];
	$subjects = $data2[4];
	$forward_to = $data2[5];
	$forward_to_alt = $data2[6];
	
	print "<div class=\"headings\">Filter Rules</div>";
	if(isset($_GET['done'])) print '<div class="error">Saved!</div>';
	print "<a href=\"imap.php\" class=\"bluelink\">Back to List</a>";
	print "<form method=\"post\" name=\"mailForm\" action=\"{$_SERVER['PHP_SELF']}\">
	<table border=\"0\" cellpadding=\"4\" cellspacing=\"2\">";
	print "<tr><td>Forward To<br /><input type=\"text\" name=\"forward_to\" class=\"input_box\" value=\"".htmlspecialchars($forward_to,ENT_QUOTES)."\" size=\"60\" /></td></tr>";
	print "<tr><td>Alternate Forward To<br /><input type=\"text\" name=\"forward_to_alt\" class=\"input_box\" value=\"".htmlspecialchars($forward_to_alt,ENT_QUOTES)."\" size=\"60\" /></td></tr>";
	print "<tr><td>Forward Domains (Regardless of Subject)<br /><textarea name=\"domains\" rows=\"10\" cols=\"60\" class=\"input_box\">".htmlspecialchars($domains,ENT_QUOTES)."</textarea></td></tr>";
	print "<tr><td>Forward Emails (Regardless of Subject)<br /><textarea name=\"emails\" rows=\"10\" cols=\"60\" class=\"input_box\">".htmlspecialchars($emails,ENT_QUOTES)."</textarea></td></tr>";
	print "<tr><td>Forward Subjects (Regardless of \"Ignore Domains\" and \"Ignore Emails\")<br /><textarea name=\"subjects\" rows=\"10\" cols=\"60\" class=\"input_box\">".htmlspecialchars($subjects,ENT_QUOTES)."</textarea></td></tr>";
	print "<tr><td><hr /></td></tr>";
	print "<tr><td>Ignore Domains<br /><textarea name=\"no_domains\" rows=\"10\" cols=\"60\" class=\"input_box\">".htmlspecialchars($no_domains,ENT_QUOTES)."</textarea></td></tr>";
	print "<tr><td>Ignore Emails<br /><textarea name=\"no_emails\" rows=\"10\" cols=\"60\" class=\"input_box\">".htmlspecialchars($no_emails,ENT_QUOTES)."</textarea></td></tr>";
	print "<tr><td><input class=\"button\" type=\"submit\" name=\"save\" value=\"Save\" /> &nbsp; <input class=\"button\" type=\"submit\" name=\"cancel\" value=\"Cancel\" onclick=\"document.location.href='imap.php'; return false;\" /><input type=\"hidden\" name=\"send\" value=\"1\" /></td></tr>";
	print "</table>
	</form>";
}

require_once('panelist_bottom.php');
?>
