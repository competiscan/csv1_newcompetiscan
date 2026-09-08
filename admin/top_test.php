<?php 
header('Content-Type: text/html; charset=utf-8');
?>
<html>
<head>
<title>Competiscan: Enhance your competitive skill</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<!--<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />-->
<script src="../includes/jsFunctions.js" type="text/JavaScript"></script> 
<script src="../includes/ajax.js" type="text/JavaScript"></script>
<script src="../includes/preview.js" type="text/JavaScript"></script>
<?php if(!empty($JQUERY)) {?>
<script src="../includes/jquery-1.6.4.min.js" type="text/JavaScript"></script>
<?php } ?>
<link href="../includes/styleSheet.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="js_calendar/calendar.css" media="screen" type="text/css" />
<meta name="robots" content="noindex, nofollow" />
<style type="text/css">
<!--
.bgx {
background-repeat: repeat-x;
}
.bgy {
background-repeat: repeat-y;
}
-->
</style>
<?php 
if(isset($HEAD)) {
	echo $HEAD; 
}
?>

<?php
if(strstr($_SERVER['REQUEST_URI'],'admin/manageFileupload.php')){ ?>
   <!-- <link href="http://fonts.googleapis.com/css?family=PT+Sans+Narrow:400,700" rel='stylesheet' />-->
    <!-- The main CSS file -->
    <link href="../includes/minifileupload/assets/css/styleupload.css" rel="stylesheet" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script src="../includes/minifileupload/assets/js/jquery.knob.js"></script>
    <!-- jQuery File Upload Dependencies -->
    <script src="../includes/minifileupload/assets/js/jquery.ui.widget.js"></script>
    <script src="../includes/minifileupload/assets/js/jquery.iframe-transport.js"></script>
    <script src="../includes/minifileupload/assets/js/jquery.fileupload.js"></script>
    <!-- Our main JS file -->
    <script src="../includes/minifileupload/assets/js/script.js"></script>
 
<?php } ?>


</head>
<body style="background:#FAF6D2;padding:8px;" onload="setAdmin();<?php if(!empty($ONLOAD)) echo $ONLOAD; ?>">
<?php include_once("../includes/analyticstracking.php") ?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr><td><img src="../images/competiscan-logo.gif" /></td></tr>
</table>
<table width="100%" border="0" cellpadding="0" cellspacing="0" style="background:#FFFFFF;">
<tr>
<td width="21" align="left" height="16" valign="top" style="background:#FFFFFF;background-image:url('../images/top1.jpg');background-repeat: no-repeat;"><img src="../images/spacer.gif" width="21" height="16" /></td>
<td colspan="3" height="16" valign="top" style="background:#FFFFFF;background-image:url('../images/topbg.jpg');background-repeat: repeat-x;"><img src="../images/spacer.gif" width="21" height="16" /></td>
<td width="21" align="right" height="16" valign="top" style="background:#FFFFFF;background-image:url('../images/top2.jpg');background-repeat: no-repeat;"><img src="../images/spacer.gif" width="21" height="16" /></td>
</tr>
<?php 
if(basename($_SERVER['PHP_SELF']) != 'main_test.php' && basename($_SERVER['PHP_SELF']) != 'index.php') {
   ?>
<div style="text-align:right;font-weight:bold;margin-right:20px;"><a href="main_test.php">Back To Menu Test</a></div>
<?php
}
?>
<tr>
<td align="left" valign="top" style="background:#FFFFFF;background-image:url('../images/1.jpg');background-repeat: repeat-y;"><img src="../images/spacer.gif" width="21" height="16" /></td>
<?php
if(basename($_SERVER['PHP_SELF']) != 'index.php') {
	if(basename($_SERVER['PHP_SELF']) == 'main_test.php') {
        echo "<td width=\"20%\" valign=\"top\" style=\"background:#DDF9EE;\">";
	
        include 'sidelinks_test.php';
	//echo "</td><td style=\"background:#FFFFFF;background-image:url('../images/bullet1.jpg');background-repeat: repeat-y;\" width=\"4\"><img src=\"../images/spacer.gif\" width=\"4\" height=\"8\" /></td>";
        echo "</td><td style=\"background:#FFFFFF;\" width=\"4\"><img src=\"../images/spacer.gif\" width=\"4\" height=\"8\" /></td>";
        }else{
            //echo "</td><td style=\"background:#FFFFFF;background-image:url('../images/bullet1.jpg');background-repeat: repeat-y;\" width=\"4\"><img src=\"../images/spacer.gif\" width=\"4\" height=\"8\" /></td>";
            echo "</td><td style=\"background:#FFFFFF;\" width=\"4\"><img src=\"../images/spacer.gif\" width=\"4\" height=\"8\" /></td>";

        }
        }
else {
?>
	<td valign="top" colspan="2">
<?php 
}
?>
<td valign="top">
