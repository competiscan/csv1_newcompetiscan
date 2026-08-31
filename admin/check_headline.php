<?php
require_once("../auth_auth.php");
require_once '../includes/functions.php';
header('Content-Type: text/html; charset=iso-8859-1');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="https://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Check Headline</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<script type="text/javascript" src="../includes/jsFunctions.js"></script>
<link href="../includes/styleSheet.css" rel="stylesheet" type="text/css" />
</head>
<body style="background:#FAF6D2;">
<img src="../images/competiscan-logo.gif" />
<table width="100%" border="0" cellspacing="0" cellpadding="5" class="text">
<?php
if(isset($_REQUEST['pid'])) $pid = (float) $_REQUEST['pid'];
else $pid = 0;
if($pid!=0){
	$where = ' AND productID<>'.$pid;
}
else{
	$where = '';
}
$query="SELECT productID FROM cscan_product_detail WHERE productHeadline='".$DRW->real_escape_string(trim($_REQUEST['hl']))."'$where";
$result=$DRW->query($query,$DRW_read);
if($DRW->num_rows($result)){
	$row=$DRW->fetch_array($result);
	?>
	<tr>
	<td class="error" align="center" style="background:#ffffff;">
	<strong>This headline already exists in the database.<br/>
	&nbsp;
	<br />
	<a href="../productDocuments.php?id=<?php echo $row['productID']; ?>" target="_blank" class="hlinks">View PDF</a>
	<br />
	<br />
	<a href="#" onclick="opener.location='addproduct.php?id=<?php echo $row['productID']; ?>'; self.close(); return false;" class="hlinks">Click here to view the full record</a>
	</strong>
	</td>
	</tr>
	<?php
}
else {
	?>
	<tr>
	<td align="center" class="error" style="background:#ffffff;">
	<strong>You can use this headline.</strong>
	<br />
	<br />
	<a href="#" onclick="window.close(); return false;">Close</a>
	</td>
	</tr>
	<?php
}
?>
</table>
</body>
</html>