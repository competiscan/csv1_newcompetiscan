#!/usr/bin/php
<?php 
require_once("includes/ehLog_set.php");
$ehL->start(__FILE__);

ini_set( "default_charset", "iso-8859-1" );
ini_set( "memory_limit", "128M" );
include_once('includes/dbcon.php');
$DRW->databaseReadWrite_die = 1;
require_once('includes/clean.php');
require_once('includes/functions.php');

	$sql = $DRW->query("select count(muid) as so_far from cscan_email_text201501");
	$res = $DRW->fetch_row($sql);
	$DRW->free_result($sql);
	error_log(print_r($sql, 1));
/*
DELETE email_a FROM cscan_email_text201501 as email_a, cscan_email_text201501 as email_b WHERE (email_a.muid = email_b.muid) AND email_a.cetid < email_b.cetid and email_a.cetid>='31950500' and email_a.cetid<='32000500';
*/

for ($i = 32000500; $i <= 62000500;) {
	$j = $i + 50000;
	$sql = $DRW->query("DELETE email_a FROM cscan_email_text201501 as email_a, cscan_email_text201501 as email_b WHERE (email_a.muid = email_b.muid) AND email_a.cetid < email_b.cetid and email_a.cetid>='$i' and email_a.cetid<='$j';");
	$res = $DRW->fetch_row($sql);
	$DRW->free_result($sql);
	error_log($i.' => '. $j);
	$i = $j;
}
	$sql = $DRW->query("select count(muid) as so_far from cscan_email_text201501");
	$res = $DRW->fetch_row($sql);
	$DRW->free_result($sql);
	error_log(print_r($sql, 1));
	
