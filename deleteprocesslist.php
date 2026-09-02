#!/usr/bin/php
<?php
//exit;
require_once ("includes/dbcon.php");
$DRW->databaseReadWrite_die = 1;
include_once 'includes/functions.php';
$sql = "SELECT ID FROM information_schema.processlist WHERE INFO is null and USER='app_writeuser' AND TIME>1000";

//echo $sql = "SELECT ID FROM information_schema.processlist WHERE INFO is null and USER='root' AND TIME>10";
$result = $DRW->query($sql, $DRW_main);
if ($DRW->num_rows($result) > 0) {
    while($resultData =  $DRW->fetch_assoc($result)){
        $process_id =  $resultData['ID'];
        $sqlkill    =   "KILL $process_id";
        $killresult =   $DRW->query($sqlkill, $DRW_main);
    }
}

$sqlread = "SELECT ID FROM information_schema.processlist WHERE INFO is null and USER='app_readuser' AND TIME>1000";
$resultread = $DRW->query($sqlread, $DRW_read);
if ($DRW->num_rows($resultread) > 0) {
    while($resultreadData =  $DRW->fetch_assoc($resultread)){
        $readprocess_id =  $resultreadData['ID'];
        $readsqlkill    =   "KILL $readprocess_id";
        $readkillresult =   $DRW->query($readsqlkill, $DRW_read);
    }
}
?>