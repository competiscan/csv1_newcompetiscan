<?php
ini_set("default_charset", "utf-8");
//ini_set("memory_limit", "512M");
ini_set("memory_limit", "-1");
set_time_limit(0);
include_once('includes/dbcon.php');
$DRW->databaseReadWrite_die = 1;
require_once('includes/clean.php');
require_once('includes/functions.php');
$sql = "SELECT creative_path FROM cscan_digital_creative where capture_status = 2 AND productID>0 limit 50";
$rs = $DRW->query($sql,$DRW_digital);
$filename=  'collect_sbkcenter.txt';
$myfile = fopen($filename, "w") or die("Unable to open file!");
while($row = $DRW->fetch_array($rs)) {
    $creative_path  =   $row['creative_path'];
    $txtfile       =   str_replace("http://collect.sbkcenter.com/creatives/","",$creative_path);
    $txt = $txtfile."\n";
    fwrite($myfile, $txt);    
}
fclose($myfile);
?>