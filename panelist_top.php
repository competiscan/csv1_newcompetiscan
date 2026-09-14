<?php
$LOGOUT_PAGE = 'content/index.php';
$ALLOW_GROUPS = array(20,38);
require_once("auth_auth.php");
require_once('includes/functions.php');
//header('Content-Type: text/html; charset=iso-8859-1');
header('Content-Type: text/html; charset=utf-8');
$contactType = array(0=>'All','prod_panelist'=>'Producer Panelists','cons_panelist'=>'Consumer Panelists','brok_panelist'=>'Mortgage Broker Panelists','prov_panelist'=>'Provider Panelists');//,'member'=>'Competiscan Members'
$contactType_email = array(0=>'',1=>'jennifer@competiscan.com',2=>'maureen@competiscan.com');
$messageTypes = array(0=>'',1=>'Unused',2=>'Used',3=>'Junk',4=>'Copy');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title><?php (isset($TITLE)) ? print $TITLE : print 'Competiscan Panelists'; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<!--<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />-->
<link href="includes/competiscan_stylesheet.css" rel="stylesheet" type="text/css" />
<style type="text/css">
<!--
 .likeresults td {
	border-width: 1px;
	padding: 4px;
	border-style: dotted;
	border-bottom-color:#D80000;
	border-left:white;
	border-right:white;
	border-top:white;
}
.section {
	margin-top: 8px;
	padding: 4px;
	border: solid 1px #000000;
}
-->
</style>
<?php 
if(isset($HEAD)) {
	echo $HEAD; 
}
?>
</head>
<body style="margin:6px;"<?php if(isset($ONLOAD)) print " onload=\"$ONLOAD\""; ?>>
<?php //include_once("includes/analyticstracking.php") ?>
<div align="right"><a href="content/logout.php" class="bottomLinks">Logout</a></div>