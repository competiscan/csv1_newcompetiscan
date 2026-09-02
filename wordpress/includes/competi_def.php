<?php
$show_errors = true; // Set to true to show errors, false to hide them
if($show_errors){
	error_reporting( E_ALL ^ E_DEPRECATED );
	ini_set('display_errors',1);
	$DRW_die = 1;
}
else{
	$DRW_die = 0;
}

/**
 * Safe count function - handles both arrays and strings
 * Returns 0 for strings and non-countable values, count for arrays
 * 
 * @param mixed $value The value to count
 * @return int The count of elements, or 0 if not an array
 */
if(!function_exists('safe_count')) {
	function safe_count($value) {
		if (is_array($value) || ($value instanceof Countable)) {
			return count($value);
		}
		return 0;
	}
}

// Array of possible hosts
$hosts = array();
$hosts['main_db'] = 'localhost'; // change this if necessary
//$hosts['main_db']	=	'10.0.0.19';
//$hosts['main_db'] = '34.227.163.22';
// Array of possible ports
$ports = array();
$ports['main_db'] = 3306; // change this if necessary
$ports['main_sphinx'] = 9312; // change this if necessary

// Array of possible connections
$DRW_connections = array();

// Now a single connection
$conn_hostname = $hosts['main_db'];
$conn_port = $ports['main_db'];
//$conn_username = "conn_username"; // change this
//$conn_password = "conn_password"; // change this
//$conn_database = "conn_database"; // change this

$conn_username = "root"; // change this
//$conn_password = "root@20165"; // change this
$conn_password = "Password#!@96"; // change this
 //$conn_password = "Xohv3iewotezu8ah";
//$conn_database = "competi_competidbnew"; // change this
//$conn_database = "competi_demo"; // change this
//$conn_database = "competi_competidblatest"; // change this
$conn_database = "competi_competidb";
$DRW_main = 'main';
$DRW_read = $DRW_main;
$DRW_read2 = $DRW_main;
$DRW_digital=$DRW_main;
$DRW_biscience_digital=$DRW_main;
$DRW_connections[$DRW_main] = array($conn_hostname,$conn_username,$conn_password,$conn_database,false,$conn_port);

// This is the SugarCRM connection
$crm_conn_hostname= $hosts['main_db'];
$crm_conn_port = $ports['main_db'];
$crm_conn_username = "crm_conn_username"; // change this
$crm_conn_password = "crm_conn_password"; // change this
$crm_conn_database = "crm_conn_database"; // change this
$DRW_crm = 'crm';
$DRW_connections[$DRW_crm] = array($crm_conn_hostname,$crm_conn_username,$crm_conn_password,$crm_conn_database,false,$crm_conn_port);

// Sphinx definitions for port and database IDs for merging
$SPHINX_port = $ports['main_sphinx'];
$SPHINX_server = $hosts['main_db'];
$SPHINX_server = 'localhost';
$SPHINX_server = '172.19.40.197';
$SPHINX_ids = array();
$SPHINX_ids[$DRW_main] = array('name'=>'prod','src'=>1,'src2'=>2,'src_e'=>3);
$SPHINX_src = $SPHINX_ids[$DRW_main]['src'];
$SPHINX_src2 = $SPHINX_ids[$DRW_main]['src2'];
$SPHINX_src_e = $SPHINX_ids[$DRW_main]['src_e'];
$SPHINX_name = $SPHINX_ids[$DRW_main]['name'];

// All other definitions
if(!empty($_SERVER['HTTP_HOST'])){ //$_SERVER['SERVER_ADDR']
	$host = strtolower($_SERVER['HTTP_HOST']);
}
else{
	$host = '';
}
$FCKEDITORNAME = 'FCKeditor2_6_6';
$COOKIEDOMAIN = preg_replace('/^www(.+)/','$1',$host);
if(strpos($COOKIEDOMAIN, '.')!==0) {
	$COOKIEDOMAIN = '.'.$COOKIEDOMAIN;
}
if(!empty($_SERVER['PHP_SELF'])){
	$COOKIEPATH = dirname($_SERVER['PHP_SELF']);
	if(substr($COOKIEPATH,-1,1)!='/') {
		$COOKIEPATH .= '/';
	}
}
else{
	$COOKIEPATH = '';
}

############### s3 for s3 credential ###################
//echo $host;

if(!defined('ENV')){
	define('ENV',getenv('SERVER_NAME'));
} 
if(ENV=='localhost'){
	$serverbaseurl	=	$_SERVER['DOCUMENT_ROOT'].'/competiscan.com';
}else if(ENV=='uat3.competiscan.com'){
	$serverbaseurl	=	'/srv/httpd/competiscan.com/wpuat3';
}else{
	$serverbaseurl	=	'/srv/httpd/competiscan.com';
}
//$serverbaseurl	=	'/srv/httpd/competiscan.com/wpuat3';
$serverbaseurl	=	'/var/www/html/competiscan.com';
require $serverbaseurl.'/aws-api/aws-vendor/autoload.php';   
   use Aws\S3\S3Client;	
   use Aws\S3\MultipartUploader;
   use Aws\Exception\MultipartUploadException;
   $credentials = new Aws\Credentials\Credentials('AKIAIKJCVVD3YLAMLERQ', 'SgZJKTMj0Otse6/akg1wIY8Pdt05PX3v/V70aild');
   //production IAM USER AND KEY
   //$credentials = new Aws\Credentials\Credentials('AKIAREQHN3EAZQBSPVHY', 'Dlc0s+znP+mKoiMNYtvJxCS+gKb2bSTbmD3eCwgr');
   $s3 = new Aws\S3\S3Client([
    'version'     => 'latest',
    'region'      => 'us-east-1',
    'credentials' => $credentials
]);
$bucket_name = 'competiscan-files';
$s3URL = 'https://s3.amazonaws.com/';
$displays3URL = 'https://files.competiscan.com/';
$s3FileUrl='https://e6vze45r56.execute-api.us-east-1.amazonaws.com/Stage/';
############### end for s3 credential ################## 
?>





