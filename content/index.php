<?php 
    if(!defined('ENV')){ 
        define('ENV',getenv('SERVER_NAME'));
    }
    if(ENV != 'localhost' || ENV != 'demo.competiscan.com'){
        $redirect_page='https://cs.competiscan.com/';
        header("Location: $redirect_page");
        die;
    }    
$LOGIN_PAGE = '../imap.php';
require_once "../auth_login.php";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Login</title>
<link href="../includes/competiscan_stylesheet.css" rel="stylesheet" type="text/css" />
<style type="text/css">
<!--
 .likeresults td {
	border-width: 1px;
	padding: 4px;
	border-style: dotted;
	border-bottom-color:#D80000;
	border-left:white;
	border-right:white;
	border-top:white;
}
.section {
	margin-top: 8px;
	padding: 4px;
	border: solid 1px #000000;
}
-->
</style>
<script type="text/JavaScript">
<!--
function validateLoginFrm() {
	if(document.loginFrm){
		var loginUser = document.loginFrm.username.value;
		var loginPassword = document.loginFrm.password.value;
		if( loginUser == '' ) {
			alert('Please enter login name.')
			document.loginFrm.username.focus();
			return false;
		}
		if( loginPassword == '' ) {
			alert('Please enter password.')
			document.loginFrm.password.focus();
			return false;
		}
	}
	return true;
}
//-->
</script>
</head>
<body style="margin:6px;">
<table width="100%" border="0" cellspacing="0" cellpadding="50">
  <tr>
    <td align="center" height="450">
	<?php
	echo $LOGIN_FORM;
	?>
    </td>
  </tr>
</table>
</body>
</html>
