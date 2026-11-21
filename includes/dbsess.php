<?php
//ini_set( "session.gc_probability", "100" );//defaults 1
//ini_set( "session.gc_divisor", "100" );//defaults 100
//ini_set( "session.gc_maxlifetime", "14400" );//defaults 14400
if(!isset($DRW)){
	require("dbcon.php");
}
function get_databaseReadWrite(){
	global $DRW,$DRW_read,$DRW_main,$DRW_crm,$DRW_connections,$DRW_die;
	if(!is_object($DRW)){//$DRW is gone for _sess_write
		$databaseReadWrite_exists = false;
		try{
			$databaseReadWrite_exists = class_exists('databaseReadWrite');
		}
		catch (Exception $e) {
			exit;
		}
		if(!$databaseReadWrite_exists){
			require("dbcon.php");
		}
		$DRW = new databaseReadWrite($DRW_connections,$DRW_main,$DRW_die);
	}
	return array($DRW,$DRW_read,$DRW_main,$DRW_crm);
}
function _sess_open($sess_path, $sess_name) {
	return true;
}
function _sess_close() {
	return true;
}
function _sess_read($sess_id) {
	list($DRW,$DRW_read,$DRW_main,$DRW_crm) = get_databaseReadWrite();
	$time = time();
	$result = $DRW->query("SELECT session_data FROM cscan_sessions WHERE session_id='".$DRW->real_escape_string($sess_id)."'",$DRW_main);
	if($DRW->num_rows($result)>0) {
		$record = $DRW->fetch_assoc($result);
		//$DRW->query("UPDATE cscan_sessions SET session_time=$time WHERE session_id='".$DRW->real_escape_string($sess_id)."'",$DRW_main);
		return $record['session_data'];
	}
	else {
		//$DRW->query("INSERT INTO cscan_sessions (session_id,session_time) VALUES ('".$DRW->real_escape_string($sess_id)."',$time)",$DRW_main);
		return '';
	}
}
function _sess_write($sess_id, $data) {
	list($DRW,$DRW_read,$DRW_main,$DRW_crm) = get_databaseReadWrite();
	//$DRW->query("UPDATE cscan_sessions SET session_time=".time().",session_data='".$DRW->real_escape_string($data)."' WHERE session_id='".$DRW->real_escape_string($sess_id)."'",$DRW_main);
	$DRW->query("REPLACE INTO cscan_sessions (session_id,session_time,session_data) VALUES ('".$DRW->real_escape_string($sess_id)."',".time().",'".$DRW->real_escape_string($data)."')",$DRW_main);
	return true;
}
function _sess_destroy($sess_id) {
	list($DRW,$DRW_read,$DRW_main,$DRW_crm) = get_databaseReadWrite();
	$DRW->query("DELETE FROM cscan_sessions WHERE session_id='".$DRW->real_escape_string($sess_id)."'",$DRW_main);
	return true;
}
function _sess_gc($sess_maxlifetime) {
	list($DRW,$DRW_read,$DRW_main,$DRW_crm) = get_databaseReadWrite();
	$DRW->query("DELETE FROM cscan_sessions WHERE session_time + $sess_maxlifetime < ".time(),$DRW_main);
	return true;
}
