<?php
error_reporting(0);
ob_start();
session_start();

require_once('sbkc_def.php');

$dbh = mysql_connect($conn_hostname, $conn_username, $conn_password);// or die ("Unable to connect to MySQL");
$selected = mysql_select_db($conn_database,$dbh);// or die ("Could not select Competiscan database");

$name = trim($_GET['name']);
$name2 = trim($_GET['name2']);
$email = trim($_GET['email']);
$address = trim($_GET['address']);
$apt = trim($_GET['apt']);
$city = trim($_GET['city']);
$state = trim($_GET['state']);
$zip = trim($_GET['zip']);
$phone = trim($_GET['phone']);
$extension = trim($_GET['extension']);
$contact_method = trim($_GET['contact_method']);
$month = trim($_GET['month']);
$day = trim($_GET['day']);
$year = trim($_GET['year']);
$gender = trim($_GET['gender']);
$income = trim($_SESSION['save']['income']);
$ownbiz = trim($_GET['ownbiz']);
$rentorown = trim($_SESSION['save']['rentorown']);
$address = $address;
$apt = $apt;
if($apt!='') $address .= " Apt # $apt";

$phone = $phone;
$phonein = preg_replace('/[^0-9]/','',$phone);

$hearSBKC = '2';
$hearSBKCInsert = $_SESSION['save']['name'].' '.$_SESSION['save']['name2'];

$bizname = trim($_GET['bizname']);

$phoneout = '';
$offset = 0;
$len = strlen($phonein);
if($len>=7){
	if($len>=10) {
		if($len>10 && substr($phonein,$offset,1)=='1'){
			$offset+=1;
		}
		$phoneout .= substr($phonein,$offset,3);
		$offset+=3;
		$phoneout .= '-';
	}
	$phoneout .= substr($phonein,$offset,3);
	$offset+=3;
	$phoneout .= '-';
	$phoneout .= substr($phonein,$offset,4);
	$offset+=4;
	if($len>$offset) {
		$phoneout .= ' ';
		$phoneout .= substr($phonein,$offset);
	}
	$phone = $phoneout;
}
$ext = $extension;
if($ext!="") $phone .= " x".$ext;

//$birthdate = $year.'-'.$month.'-'.$day;
$birthdate = $_GET['birthday'];


$familyContactID = $_SESSION['save_id'];

$query = "INSERT INTO `cscan_contacts_pre` SET
	date_modified = NOW(),
	first_name ='".mysql_real_escape_string($name)."',
	last_name='".mysql_real_escape_string($name2)."',
	birthdate='".mysql_real_escape_string($birthdate)."',
	phone='".mysql_real_escape_string($phone)."',
	email='".mysql_real_escape_string($email)."',
	primary_address_street='".mysql_real_escape_string($address)."',
	primary_address_city='".mysql_real_escape_string($city)."',
	primary_address_state='".mysql_real_escape_string($state)."',
	primary_address_postalcode='".mysql_real_escape_string($zip)."',
	gender='".mysql_real_escape_string($gender)."',
	income='".mysql_real_escape_string($income)."',
	ownbiz='".mysql_real_escape_string($ownbiz)."',
	contact_method='".mysql_real_escape_string($contact_method)."',
	rentorown='".mysql_real_escape_string($rentorown)."',
	hearSBKC = '".$hearSBKC."',
	hearSBKCInsert = '".$hearSBKCInsert."',
	familyContactID = '".$familyContactID."',
	bizname = '".mysql_real_escape_string($bizname)."'";

$result = mysql_query($query);// or die("Unable to execute query :'".$query."' due to following error : ".mysql_error());
//print mysql_error();
//print_r($_SESSION);
//print $query;
print 'Family member '.$name.' '.$name2.' has been added...';
?>