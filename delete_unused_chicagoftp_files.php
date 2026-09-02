<?php
ini_set("default_charset", "utf-8");
//ini_set("memory_limit", "512M");
ini_set("memory_limit", "-1");
set_time_limit(0);
include_once('includes/dbcon.php');
$DRW->databaseReadWrite_die = 1;
require_once('includes/clean.php');
require_once('includes/functions.php');
$sql = "SELECT filename FROM chicagorecords WHERE crm_import_date< '2020-01-01' AND crm_import_date>='2016-02-01' AND filename REGEXP 'dachicagorecordsftp' ORDER BY id LIMIT 50";
$result = $DRW->query($sql, $DRW_read2);
if ($DRW->num_rows($result) > 0) {
    while($resultData =  $DRW->fetch_assoc($result)){
        $filename =  $resultData['filename'];
        if (file_exists($filename)) {
            unlink($filename);
        }
    }
}
?>