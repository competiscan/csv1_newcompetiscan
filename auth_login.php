<?php
$ALLOW_LOGIN = $SHOW_LOGIN = true;

$cook = 'competiscan_admin';
if(!empty($_SERVER['HTTP_HOST'])){
	$host = strtolower($_SERVER['HTTP_HOST']);
}
else{
	$host = '';
}
if(isset($_COOKIE[$cook.'2']) && $_COOKIE[$cook.'2']!='0') {
	$COOKIE_AUTH = true;
	if(isset($_COOKIE[$cook.'1']) && $_COOKIE[$cook.'1']!='0'){
		$COOKIE_UNAME = $_COOKIE[$cook.'1'];
	}
}

require_once "auth_inc.php";
if(isset($a) && $a->checkAuth()){
	//$LOGIN_FORM = $LOGGED_IN_MESSAGE;
	header("Location: $LOGIN_PAGE");
}
?>
