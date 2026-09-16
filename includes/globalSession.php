<?php error_reporting( 0 );
set_time_limit(0);
if(ob_get_level()<=1) {
	@ob_start();
}
require_once("competi_def.php");
require_once('dbcon.php');
require_once('dbsess.php');
include_once('email_defs.php');
require_once('functions.php');
/**
 * Composer
 * Added 2016-02-09 by Tyler
 */
require_once('vendor/autoload.php');
session_set_cookie_params('86400');
// session_set_cookie_params(['lifetime' => 86400, 
// 'path' => '/',
// 'domain' => '.competiscan.com', 
// 'secure' => true,
// 'httponly' => false,    
//  'samesite' => 'Lax' 
// ]);
if(isset($_SERVER['SERVER_PORT'])){
	if(session_id()=='') {
		@session_start();
	}
}
ini_set( "memory_limit", "-1" );
ini_set( "default_charset", "iso-8859-1" );
ini_set('save_handler','memcached');
//ini_set('save_path','tcp://dev-ca-vmh85qk0jwy3.0etuir.cfg.use1.cache.amazonaws.com:11211?persistent=1&weight=1&timeout=1&retry_interval=15');
ini_set('save_path','dev-ca-vmh85qk0jwy3.0etuir.cfg.use1.cache.amazonaws.com:11211');
header('Content-Type: text/html; charset=iso-8859-1');
?>
