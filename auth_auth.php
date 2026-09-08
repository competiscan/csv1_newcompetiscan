<?php
require_once "auth_inc.php";
if(!(isset($a) && $a->checkAuth())){
	@ob_end_clean();
	$_SESSION['redirectURL'] = $_SERVER['REQUEST_URI'];
	header("Location: $LOGOUT_PAGE");
	exit;
}else{
	if($a->checkAuth() && isset($_SESSION['redirectURL'])){
		$redirectURL = $_SESSION['redirectURL'];
		unset($_SESSION['redirectURL']);
		header("Location: {$redirectURL}");
	}
}
?>
