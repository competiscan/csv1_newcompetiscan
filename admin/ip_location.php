<?php 
$ALLOW_GROUPS = array(19);
require_once "../auth_auth.php";
header('Content-Type: text/html; charset=iso-8859-1');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<title>Competiscan Location</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../includes/styleSheet.css" rel="stylesheet" type="text/css" />
</head>
<body style="margin:10px;">
<?php
if(isset($_GET['ip'])) {
	$IPAddress = $_GET['ip'];
	echo "<div style=\"font-weight:bold;\">$IPAddress</div>";
	$query4 = "SELECT location_text,IPAddress FROM cscan_ip_location WHERE IPAddress='".$DRW->real_escape_string($IPAddress)."'";
	$result4 = $DRW->query($query4,$DRW_read);
	$data4 = $DRW->fetch_row($result4);
	$location_text = $data4[0];
	echo "<pre>".htmlspecialchars($location_text)."</pre>";
}
?>
<p><a href="#" onclick="self.close(); return false;">close</a></p>
</body>
</html>