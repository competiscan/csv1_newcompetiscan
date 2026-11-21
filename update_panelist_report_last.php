#!/usr/bin/php
<?php  
error_reporting(E_ALL);
ini_set('display_errors',1);
$Y = date('Y');
$m = date('m',strtotime('-1 month'));
exec("/usr/bin/php update_panelist_report.php $Y $m");
?>