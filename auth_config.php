<?php
error_reporting( 0 );
$DATABASEDEBUG = false;

if(ob_get_level()<=1) {
	@ob_start();
}

require_once("includes/competi_def.php");
require_once('includes/dbcon.php');
require_once('includes/dbsess.php');
include_once('includes/email_defs.php');

if(isset($_SERVER['SERVER_PORT'])){
	if(session_id()=='') {
		@session_start();
	}
}
    $hosts_admin=$_SERVER['HTTP_HOST'];
    if($hosts_admin=='localhost'){
        $baseurl_admin='';
    }else if($hosts_admin=='demo1.competiscan.com'){
        $baseurl_admin='https://demo1.competiscan.com/admin/';
    }else{
        $baseurl_admin='https://www.competiscan.com/admin/';
    }
$SESSIONNAME = 'COMPETI'; //sets unique session name for your project
$DEFAULT_LOGIN_PAGE = $baseurl_admin.'main.php'; //sets default page to go to after login
$DEFAULT_LOGOUT_PAGE = 'index.php'; //sets default page to go to after logout or failed login
$DEFAULT_EXPIRE_TIME = 0; // set to 0 for no expire, can be overridden by $EXPIRE_TIME
$DEFAULT_IDLE_TIME = 0; // set to 0 for no idle, can be overridden by $IDLE_TIME
$LOGGED_IN_MESSAGE = "You are logged in.<br /><a href=\"main.php\">Main Menu</a>"; // this string replaces the form on the login screen if already authenticated

if(!empty($_SERVER['HTTP_HOST'])){
	$host = strtolower($_SERVER['HTTP_HOST']);
}
else{
	$host = '';
}

$AUTH_HOST = $conn_hostname; // Hostname or IP of database
$AUTH_USER = $conn_username; // Username for accessing the database
$AUTH_PASSWORD = $conn_password; // Password for accessing the database (case-sensitive)
$AUTH_DATAB = $conn_database; // Name of the database to use

$OPTIONS = array(
    'host'        => $AUTH_HOST,
    'user'        => $AUTH_USER,
    'password'    => $AUTH_PASSWORD,
    'database'    => $AUTH_DATAB,
    'table'       => 'cscan_admin_users', // The database table storing authorization data
    'usernamecol' => 'userName',          // Column storing the username
    'passwordcol' => 'password',          // Column storing the encrypted password
    'cryptType'   => 'md5',              // Encryption type used for the password
);

$STORAGEDRIVER = 'DB'; //sets Auth storage driver

//define normal storage driver if you want to get extra info from database
//require_once('DB.php');
//$MYSQL =& DB::connect($DSN);
//databaseError($MYSQL,false);

//define any other globals or includes here
function databaseError($ref,$debug=true,$die=true){
	if(isset($GLOBALS['DATABASEDEBUG'])) {
		$DATABASEDEBUG = $GLOBALS['DATABASEDEBUG'];
		
		if($DATABASEDEBUG && DB::isError($ref)) {
			if($debug) $print = $ref->getDebugInfo();
			else $print = $ref->getMessage();
			
			if($die) die($print);
			else print $print;
		}
	}
}
?>
