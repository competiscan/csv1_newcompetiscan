<?php
$PAGE_HEADING = "Login";
$TITLE = "Welcome To Competiscan";
$HEAD = '<script src="includes/index.js" type="text/JavaScript"></script>';
include 'header_top.php';
if(!defined('ENV')){
define('ENV',getenv('SERVER_NAME'));
}
/*if (!isset($_SESSION)) {
    echo "Session not started or session data is missing.";
} else {
    echo "Session started successfully.";
    // print_r($_SESSION);
}
echo $session_id = session_id(); // Or a custom session ID
*/
$message='';
$successMessage=0;
$_SESSION['sess_client_id']='';
$_SESSION['sso_cleint_secret']='';
$_SESSION['sso_domain_name']='';
if(isset($_REQUEST['sso_company']) and $_REQUEST['sso_company']!=''){
	$sql_query="SELECT * FROM cscan_sso_authorisation WHERE sso_company_name='".$_REQUEST['sso_company']."'";
	$result_sso = $DRW->query($sql_query,$DRW_read);
	$count    = $DRW->num_rows($result_sso);
	
	if($count>0){
		$data_sso =   $DRW->fetch_row($result_sso);
		$sso_company=$data_sso[1];
		$sso_cleint_id=$_SESSION['sess_client_id']=$data_sso[3];
		$sso_cleint_secret=$_SESSION['sso_cleint_secret']=$data_sso[4];
		$sso_domain_name=$_SESSION['sso_domain_name']=$data_sso[5];
		$state_param=$sso_cleint_id;
		// echo "https://smalclient.competiscan.com/oauth2/authorize?client_id=$sso_cleint_id&response_type=code&scope=aws.cognito.signin.user.admin+email+openid+phone&redirect_uri=https%3A%2F%2Fcompetiscan.com%2Fsso_auth_main.php";
		// die;
		header("Location: $sso_domain_name/oauth2/authorize?client_id=$sso_cleint_id&response_type=code&scope=aws.cognito.signin.user.admin+email+openid+phone&redirect_uri=https%3A%2F%2Fcompetiscan.com%2Fsso_auth_main.php&state=$state_param");
		//header("Location: $sso_domain_name/oauth2/authorize?client_id=$sso_cleint_id&response_type=code&scope=aws.cognito.signin.user.admin+email+openid+phone&redirect_uri=https%3A%2F%2Fdemo.competiscan.com%2Fsso_auth_main.php&state=$state_param");
		exit();
	}else{
		$successMessage=1;
	}

} 
?>
<form name="SSOloginForm" action="" method="post" onsubmit="return validate();">
	<?php 
		if($successMessage == 1){ ?>
		<div class="row error" id="alert-message" >
			<div class="col-md-10 col-md-offset-1  bg-danger" style="text-align:center;color:red;">The company name you entered is invalid, Please check and try again.</div>
		</div>
	<?php } ?>
	<div class="row error" id="login-alerts" style="<?php echo !$message ? 'display:none' : '' ?>">
		<div class="col-md-10 col-md-offset-1 bg-danger"><?php echo $message; ?></div>
	</div>

	<div class="row form-row" style="padding-top: 30px;">
		<div class="col-md-4 col-md-offset-4">
		<div class="form-group" style="margin-bottom: 5px;">
			<label for='username'>Company</label>
			<input class="form-control" type='text' id='sso_company' name='sso_company' value="<?php //echo htmlspecialchars($oldusername, ENT_QUOTES);?>" />
		</div>
		</div>
	</div>
	<div class="row form-row button-container">
		<div class="col-md-12 text-center">
			<input type="submit" name="submit_login_sso" value="Login" id="submit_login_sso" class="btn btn-primary" />&nbsp;&nbsp;
		</div>
	</div>
</form>

<script>
(function($){
	$('#submit_login_sso').click(function(e) {
		e.preventDefault();
		$('#login-alerts').children('div').html('');
		if($('#login-alerts').is(":visible")) $('#login-alerts').toggle(100);
		var username = $('#sso_company').val();
		if (!username) {
			$('#login-alerts').toggle(400);
			$('#login-alerts').children('div').html('<ul><li>Please enter your sso company name.</li></ul>');
		}
		else {
			SSOloginForm.submit();
		}
	});
setTimeout(function() {
	$('#alert-message').hide();
}, 6000);
})(jQuery);
</script>
<?php
include 'footer_bottom.php';
?>
