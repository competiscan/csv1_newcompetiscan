<?php 
$LOGOUT_PAGE = 'content/index.php';
$ALLOW_GROUPS = array(20);
require_once("auth_auth.php");
require_once('includes/functions.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Competiscan Consumer ID</title>
<link href="includes/competiscan_stylesheet.css" rel="stylesheet" type="text/css" />
</head>
<body style="margin:6px;">
<?php
$sql = "SELECT MAX(consumer_inc) FROM cscan_consumer_inc";
$incquery = $DRW->query($sql,$DRW_main);
$incquery = $DRW->fetch_row($incquery);
$consumer_inc = $incquery[0];
if(isset($_POST['id'])){
	$id = (int)$_POST['id'];
	if($id>$consumer_inc){
		$sql = "INSERT IGNORE INTO cscan_consumer_inc (consumer_inc) VALUES (".$id.")";
		$DRW->query($sql,$DRW_main);
		$updated = 1;
	}
	else{
		$updated = 2;
	}
	
	ob_end_clean();
	header("Location: {$_SERVER['PHP_SELF']}?updated=$updated");
	exit;
}
echo "<div class=\"section\" style=\"background-color:#E8E8FF;\"><form method=\"post\" name=\"prodForm\" action=\"{$_SERVER['PHP_SELF']}\">
<table border=\"0\" cellpadding=\"4\" cellspacing=\"2\">";
if(isset($_GET['updated'])){
	if($_GET['updated']==2){
		echo "<tr><td class=\"bodytext\" valign=\"top\" colspan=\"2\"><div class=\"error\">Invalid ID</div></td></tr>";
	}
	else{
		echo "<tr><td class=\"bodytext\" valign=\"top\" colspan=\"2\"><div class=\"error\">Updated!</div></td></tr>";
	}
}
echo "<tr><td class=\"bodytext\" valign=\"top\">Last ID:</td><td>$consumer_inc</td></tr>
<tr><td class=\"bodytext\" valign=\"top\">New Last ID:</td><td><input type=\"text\" name=\"id\" size=\"10\" /></td></tr>
<tr><td>&nbsp;</td><td><input class=\"button\" type=\"submit\" name=\"save\" value=\"Save\" /> &nbsp; <input class=\"button\" type=\"submit\" name=\"cancel\" value=\"Close\" onclick=\"self.close(); return false;\" /></td></tr>
</table></form>
</div>";
?>
</body>
</html>