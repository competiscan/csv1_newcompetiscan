<?php 
error_reporting( E_ALL );
ini_set('display_errors',1);

require_once('ehLog.php');

$ehL = new ehLog();
function ehLog_error($errno, $errstr, $errfile, $errline){
	global $ehL;
	$ehL->write_error($errno, $errstr, $errfile, $errline);
	
	return true;
}
?>