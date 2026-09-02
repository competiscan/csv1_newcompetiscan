<?php
require_once("includes/ehLog_set.php");
$ehL->start(__FILE__);
ini_set("default_charset", "utf-8");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set("memory_limit", "-1");
set_time_limit(0);

require_once("includes/ehLog_set.php");
$ehL->start(__FILE__);
require_once ("includes/dbcon.php");
$DRW->databaseReadWrite_die = 1;
include_once 'includes/functions.php';
include_once 'includes/thumb.php';
########################################
function pr($str){
    echo '<pre>';print_r($str);
}
function clean($string) {
   $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
   return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
}
############################################
$backday = (!empty($_REQUEST['p1']))?trim($_REQUEST['p1']):0;
$action = (!empty($_REQUEST['p2']))?trim($_REQUEST['p2']):'';
if(isset($_SERVER['argc']) && $_SERVER['argc']>0) {
    $backday = (!empty($_SERVER['argv'][1]))?trim($_SERVER['argv'][1]):$backday;
    $action = !empty($_SERVER['argv'][2])?trim($_SERVER['argv'][2]):$action;
}

$cmd = '';
$backday = clean($backday);
$action = clean($action);
if($action=='index'){
    $cmd = "/usr/bin/php ".dirname(__FILE__)."/cron_update_maxmail_csv.php $backday $action";
}elseif($action=='search'){
    $cmd = "/usr/bin/php ".dirname(__FILE__)."/cron_update_maxmail_csv.php $backday $action";
}elseif($action=='import'){
    $cmd = "/usr/bin/php ".dirname(__FILE__)."/imap_cunsumer_tmp.php $backday";
}elseif($action=='sameday'){
    //$cmd = "/usr/bin/php ".dirname(__FILE__)."/imap_cunsumer_sameday.php $backday";
    $cmd = "/usr/bin/php ".dirname(__FILE__)."/imap_cunsumer_sameday_tempstore.php $backday";
}else{
    $cmd =  'No parameters passed!';
}
if(!empty($cmd)){
    $sql = "INSERT IGNORE INTO da_maxmail_bg_scripts SET command = '".$cmd."'";                        
    if($DRW->query($sql,$DRW_main)){
        $output = exec($cmd . " 2>&1", $output);//wait for the response
    }
}