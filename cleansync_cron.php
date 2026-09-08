#!/usr/bin/php
<?php
require_once("includes/ehLog_set.php");
$ehL->start(__FILE__);

require_once("includes/dbcon.php");
$DRW->databaseReadWrite_die = 1;

$deltime = time() - 1800;
$sql = "DELETE FROM drw_sync WHERE drw_sync_time<'".$deltime."'";
$DRW->query($sql,$DRW_main);

$ehL->stop(false);
?>