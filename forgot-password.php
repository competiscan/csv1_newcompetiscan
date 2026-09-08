<style>
.loader{
    background: rgba(255,255,255,0.9) url(images/loader.gif) no-repeat center 50% ;  
    opacity: 0.9;
    z-index: 1000001;
    width:100%; 
    height:100%; 
    position: fixed; 
    top:0; 
    left:0;
}
</style>
<?php
require_once('includes/globalSession.php');
$PAGE_HEADING = "Forgot Password";
$TITLE = "Competiscan $PAGE_HEADING";
include 'header_top.php';
$loc = "fullsearch.php?searchview=2";
if(isset($_SESSION['sess_userID'])){
	ob_end_clean();
	header("Location: $loc");
	exit;
}
?>
<script type="text/javascript">
$(document).ready(function(){
	$('#send-button').on('click', function(){

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
		}else{
			$(".loader").show();
			$.ajax({
		        type: 'POST',
		        url: 'send-reset-password.php',
		        data: {email: email},
		        success: function (data) {
		            if(data == 0){
		            	$(".loader").hide();
		            	$("#emailAddress").val('');
		            	$("#send-unknown-email-message").show();
		            	setTimeout(function() {
				            $('#send-unknown-email-message').hide();
				        }, 6000);
		            }else if(data == 1){
		            	$(".loader").hide();
		            	$("#emailAddress").val('');
		            	$("#send-success-message").show();
		            	setTimeout(function() {
				            $('#send-success-message').hide();
				        }, 6000);
		            }else if(data == 2){
		            	$(".loader").hide();
		            	$("#emailAddress").val('');
		            	$("#email-not-send-message").show();
		            	setTimeout(function() {
				            $('#email-not-send-message').hide();
				        }, 6000);
		            }
		        },
		        error: function (data) {
		        	$(".loader").hide();
		        	alert("Something went wrong!");
                },             
            });        
        }
	});
});

</script>
<div style="height:300px;">
<form action="" name="forPassForm" method="post">
<table width="100%" border="0" cellpadding="5" cellspacing="0" class="bodytext">

	<tr id="send-success-message" style="height: 50px;display: none;">
        <td colspan="2" style="text-align: center;color:red;">Reset password mail has been sent successfully.</td>
    </tr>

    <tr id="send-unknown-email-message" style="height: 50px;display: none;">
        <td colspan="2" style="text-align: center;color:red;">Invalid email address.</td>
    </tr>

    <tr id="email-not-send-message" style="height: 50px;display: none;">
        <td colspan="2" style="text-align: center;color:red;">Email has not been sent. Please try again!</td>
    </tr>
	
	<tr>
		<td colspan="2" class="bodytext">
			<strong>Please enter your email address and click send. We will send you an email with reset password link.</strong>
		</td>
	</tr>
	<tr><td colspan="2">&nbsp;</td></tr>
	<tr>
		<td class="bodytext" align="right" valign="top">
			<span class="star">*</span> 
			<strong>Email Address :</strong>
		</td>
		<td>
			<input type="text" name="email" size="40" maxlength="255" class="input_box" id="emailAddress"/>
		</td>
	</tr>
	<tr>
	<td>&nbsp;</td>
	<td>
		<input type="button" name="submit" value="Send" class="submitbutton" id="send-button"/>
		<input type="hidden" name="submit" value="1" />
	</td>
	</tr>
</table>
</form>
</div>
<?php
include 'footer_bottom.php';
?>