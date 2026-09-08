<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set("memory_limit", "-1");
set_time_limit(0);
###########################################
function pr($str){
    echo '<pre>';print_r($str);
}
###########################################
require '../includes/dbcon.php';
require '../includes/functions.php';
