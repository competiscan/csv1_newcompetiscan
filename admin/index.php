<?php
$LOGGED_IN_MESSAGE = "You are logged in.<br /><a href=\"imap.php\">Main</a>";
require_once "../auth_login.php";
include 'top.php';
?>
<table width="100%" border="0" cellspacing="0" cellpadding="50">
<tr>
<td align="center" height="450">  
<?php 
echo $LOGIN_FORM;
?>
</td>
</tr>
</table>
<script type="text/JavaScript">
<!--
function validateLoginFrm() {
	if(document.loginFrm){
		var loginUser = document.loginFrm.username.value = trimspace(document.loginFrm.username.value);
		var loginPassword = document.loginFrm.password.value = trimspace(document.loginFrm.password.value);
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
<?php 
include 'bottom.php';
?>
