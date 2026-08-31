<?php
$PAGE_HEADING = "Lost Password";
$TITLE = "Competiscan $PAGE_HEADING";
include 'header_top.php';

$mail_sent = -1;
if(isset( $_POST['email'] ) && $_POST['email']!=''){
	$mail_sent = 0;
	$sql = "SELECT firstName,lastName,companyName,emailAddress,password FROM cscan_users WHERE active='y' AND emailAddress='".$DRW->real_escape_string($_POST['email'])."'";
	$result = $DRW->query($sql,$DRW_read);
	$rs = $DRW->fetch_row($result);
	$firstName = $rs[0];
	$lastName = $rs[1];
	$companyName = $rs[2];
	$emailAddress = trim($rs[3]);
	$password = $rs[4];
	
	$name = $firstName;
	if($lastName!='') $name .= ' '.$lastName;
	if($name=='') $name .= ' '.$emailAddress;
	if($companyName!='') $name .= ' ('.$companyName.')';
	
	$namehtml = htmlspecialchars($name);
	$emailhtml = htmlspecialchars($emailAddress);
	$passwordhtml = htmlspecialchars($password);
	
	if($emailAddress!=''){
		require_once('Mail.php');
		require_once('Mail/mime.php');
		
		$bodyhtml = <<< MAILBODY
<html>
<body>
	<p>Hello $namehtml,</p>
	<p>Your username is: <strong>$emailhtml</strong><br />
	Your password is: <strong>$passwordhtml</strong></p>
	<p>Please contact us if you have any questions or if we can assist with anything else.</p>
	<p>Competiscan Helpdesk<br />
	312-488-1810</p>
</body>
</html>
MAILBODY;
	
		$bodytext = "Hello $name,\n\nYour username is: <strong>$emailAddress</strong>\nYour password is: <strong>$password</strong>\n\nPlease contact us if you have any questions or if we can assist with anything else.\n\nCompetiscan Helpdesk\n312-488-1810";      
		
		$crlf = "\n"; //if you want to send the generated MIME message using Mail  then you have to set $crlf to "\n"
		$hdrs = array('From'=>"\"Competiscan\" <share@competiscan.com>",'Bcc'=>$EMAIL_LostPassword,'Subject'=>$emailAddress." Forgot Password");
		
		$mime = new Mail_mime($crlf);
		
		$mime->setTXTBody($bodytext);
		$mime->setHTMLBody($bodyhtml);
		
		$body = $mime->get();
		$headers = $mime->headers($hdrs);
		
		//$mail =& Mail::factory('mail','-f'.$EMAIL_error);
		$params = array(
			'username'=>'',
			'password'=>'',
		);
		$mail = Mail::factory('smtp',$params);
		$send = $mail->send($emailAddress, $headers, $body);
		
		if(!PEAR::isError($send)) {
			$mail_sent = 1;
		}
	}
}
?>
<script type="text/javascript">
<!--
function validate() {
	var email = document.forPassForm.email.value = trimspace(document.forPassForm.email.value);
	
	if( email == "" ) {
		alert("Please enter your email address");
		document.forPassForm.email.focus();
		return false;
	}
	if( !checkmail( email ) ) {
		alert("Please enter a valid email address");
		document.forPassForm.email.focus();
		return false;
	}
	return true;
}
//-->
</script>
<div style="height:300px;">
<form action="<?php print $_SERVER['PHP_SELF']; ?>" name="forPassForm" method="post" onsubmit="return validate();">
<table width="100%" border="0" cellpadding="5" cellspacing="0" class="bodytext">
<?php
if($mail_sent==1){
	?>
	<tr><td class="bodytext"><div style="height:300px;" class="star">Your request has been sent to Competiscan.<br />We will reply to you soon.</div></td></tr>
	<?php
}
elseif($mail_sent==0){
	?>
	<tr><td class="bodytext"><div style="height:300px;" class="error">Unknown Email Address</div></td></tr>
	<?php
}
else {
	?>
	<tr><td colspan="2" class="bodytext"><strong>Please enter your email address and click send. We will send you an email with your password.</strong></td></tr>
	<tr><td colspan="2">&nbsp;</td></tr>
	<tr>
	<td class="bodytext" align="right" valign="top"><span class="star">*</span> <strong>Email Address :</strong></td>
	<td><input type="text" name="email" size="40" maxlength="255" class="input_box" /></td>
	</tr>
	<tr>
	<td>&nbsp;</td>
	<td><input type="submit" name="submit" value="Send" class="button" /><input type="hidden" name="submit" value="1" /></td>
	</tr>
	<?php 
} 
?>
</table>
</form>
</div>
<?php
include 'footer_bottom.php';
?>