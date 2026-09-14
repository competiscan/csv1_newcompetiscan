<?php
$PAGE_HEADING = "Login";
$TITLE = "Welcome To Competiscan";
$HEAD = '<script src="includes/index.js" type="text/JavaScript"></script>';
include 'header_top_test.php';
if(!defined('ENV')){
define('ENV',getenv('SERVER_NAME'));
}
$message = '';
$loc = "fullsearch.php?searchview=2";
$number_machines = 1;
$bypass = 0;
$successMessage = 0;
$_SESSION['sess_access_token'] = '';
$new_msg='If you have not logged in or reset your credentials since <b>June 7, 2025</b>, you will need to <br/> reset your password. Select the Forgot Password link to receive a reset code via email. <br/><br/>Still having issues logging in? <br/><br/>Reach us at <b>contactus@competiscan.com.</b>';
if (isset($_GET['product']) && $_GET['product']!='') {
    $direct_request_type = 'product';
    $direct_request_id = (float)$_GET['product'];
} elseif(isset($_GET['trend_id']) && $_GET['trend_id']!='') {
    $direct_request_type = 'trend_id';
    $direct_request_id = (int)$_GET['trend_id'];
    //############### ADD ENCODE TREND ID############
} elseif(isset($_GET['document']) && $_GET['document']!='') {
    $direct_request_type = 'document';
    $direct_request_id = (float)$_GET['document'];
} elseif(isset($_GET['download']) && $_GET['download']!='') {
    $direct_request_type = 'download';
    $direct_request_id = preg_replace('/\\W+/','',$_GET['download']);
}

if(isset($_COOKIE['competiscaner'])){
	$cookieArray = explode(':',$_COOKIE['competiscaner']);
	$cookieArray = array_map('urldecode',$cookieArray);
	$oldusername = $cookieArray[0];
	$oldpassword = $cookieArray[1];
	$oldIPAddress = $cookieArray[2];
	$olduserID = $cookieArray[3];
	$secretcode = $cookieArray[4];
	$uctimestamp = $cookieArray[5];
}
else{
	$oldusername = '';
	$oldpassword = '';
	$oldIPAddress = '';
	$olduserID = '';
	$secretcode = 0;
	$uctimestamp = 0;
}
function callAPI($method, $url, $data){
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    curl_setopt($curl, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
		'User-Agent: Mozilla/5.0',
		'Authorization:'.$_SESSION['sess_access_token'],
        'Content-Length: ' . strlen($data)
    ]);
    $result = curl_exec($curl);
    curl_close($curl);
    return $result;
}
//'Authorization: Bearer '.isset($_GET['sess_access_token']),
$postuserdata=array();
$otp_input_field=0;
$otp_input_pwdfield=1;
$session_email_otp_v='';

// ===============================
// HANDLE LOGIN or OTP VERIFY
// ===============================
if (isset($_POST['login']) || isset($_POST['verify_opt_seession']) || (isset($_GET['resend_otp']) && $_GET['resend_otp']==1) || (isset($_GET['is_admin']) and $_GET['is_admin']==true)) {
    $postuserdata = [];
    $otp_input_field = 0;
    $otp_input_pwdfield = 1;
    // VERIFY OTP REQUEST
    if (!empty($_POST['verify_opt_seession']) && !empty($_POST['v_otp'])) {
        $postuserdata['email'] = $_POST['userName'];
        $postuserdata['session'] = $_POST['verify_opt_seession'];
        $postuserdata['otp'] = $_POST['v_otp'];
    } elseif(!empty($_GET['auth_id']) && !empty($_GET['key'])){
		$postuserdata['email']  = base64_decode($_GET['auth_id']);
		$postuserdata['password']  = base64_decode($_GET['key']);
		$otp_input_field = 1;
		$otp_input_pwdfield = 0;
	}elseif(!empty($_GET['is_admin']) and $_GET['is_admin']==true){
		$postuserdata['email']  = base64_decode($_GET['id']);
		$_SESSION['sess_access_token']  = trim($_GET['token']);
		$postuserdata['is_admin']  = True;
		
	}
	else {
        $postuserdata['email'] =$_SESSION['userName'] = $_POST['userName'];
        $postuserdata['password']=$_SESSION['password']= $_POST['password'];
    }

    $postdata = json_encode($postuserdata);
    $apiuserurl = USER_LOGIN_API_URL_PROD . 'sign-in-aws';
    $getuserdata = callAPI('POST', $apiuserurl, $postdata);
    $resuserdata = json_decode($getuserdata, true);

    // ===============================
    // RESPONSE HANDLING
    // ===============================
    if (isset($resuserdata['code'])) {
        switch ($resuserdata['code']) {
            case 200:
                if (isset($resuserdata['data']['challenge']) && $resuserdata['data']['challenge'] == 'EMAIL_OTP') {
                    // OTP CHALLENGE
                    $otp_input_field = 1;
                    $otp_input_pwdfield = 0;
                    $_SESSION['session'] = $resuserdata['data']['session'];
                    $message = "<ul><li style='text-align:center;'>OTP sent to your email.</li></ul>";
                } else {
                    // LOGIN SUCCESS - SET SESSION DATA
                    $_SESSION['sess_access_token']=$resuserdata['data']['access_token'];
					$_SESSION['sess_refresh_token']=$resuserdata['data']['refresh_token'];
					$apiusergetpermission=USER_PERMISSION_API_URL_PROD.'rolepermissionclientdata?userID='.$resuserdata['data']['user_id'];
					$getuserpermissiondata= callAPI('GET', $apiusergetpermission,null);
					$resuserpermissiondata = json_decode($getuserpermissiondata, true);
					// echo "<pre>";
					// print_r($resuserpermissiondata);
					// echo "<pre>";
					//die;
					$_SESSION['sess_username']=$resuserpermissiondata['sess_username'];
					$_SESSION['sess_userID']   = $resuserpermissiondata['sess_userID']; 
					$_SESSION['sess_userType'] = $resuserpermissiondata['sess_userType'];
					$_SESSION['sess_userType'] = 'a';
					$_SESSION['sess_companyName'] = $resuserpermissiondata['sess_companyName'];
					$_SESSION['sess_plevel'] = $resuserpermissiondata['sess_plevel'];
					$_SESSION['sess_mchannel'] = $resuserpermissiondata['sess_mchannel'];
					$_SESSION['sess_mpanel'] = $resuserpermissiondata['sess_mpanel'];
					$_SESSION['sess_sector'] = $resuserpermissiondata['sess_sector'];
					$_SESSION['sess_category'] = $resuserpermissiondata['sess_category'];
					$_SESSION['sess_subcategory'] = $resuserpermissiondata['sess_subcategory'];
					$_SESSION['sess_subtosubcategory'] = $resuserpermissiondata['sess_subtosubcategory'];
					$_SESSION['sess_search_exclude'] = $resuserpermissiondata['sess_search_exclude'];
					$_SESSION['sess_search_additional_field'] = $resuserpermissiondata['sess_search_additional_field'];
					$_SESSION['sess_anotation_tool_link'] = $resuserpermissiondata['sess_search_annotation_tool'];
					$_SESSION['sess_ai_analysis_link'] = $resuserpermissiondata['sess_ai_analysis_link'];
					$_SESSION['sess_sender_domain'] = $resuserpermissiondata['sess_sender_domain'];
					$_SESSION['sess_search_page_permission'] = $resuserpermissiondata['sess_search_page_permission'];
					$_SESSION['sess_dashboard'] = false;
					if(!empty($resuserpermissiondata['sess_search_edc'])){
						$_SESSION['sess_dashboard'] = true;
					}
					if(!empty($_SESSION['sess_search_page_permission'])){
						$show_header_top=true;
					}
                    // redirect after login
                   if(isset($_SESSION['sess_userID'])){
						$time = time();
						if($bypass!=1){
							if(preg_match('/^(\\d[^\\.]+\\.\\d[^\\.]+\\.)/',$IPAddress,$matches)){
								$check_ip = $matches[1];
							}
							else{
								$check_ip = substr($IPAddress,0,strrpos($IPAddress,'.'));
							}
							$count_save_sql = "SELECT COUNT(*) FROM cscan_user_code where userID={$_SESSION['sess_userID']} AND (code='".$DRW->real_escape_string($secretcode)."' OR initial_IP LIKE '".$check_ip."%')";
							$rs = $DRW->query($count_save_sql,$DRW_read);
							$data = $DRW->fetch_row($rs);
							$secretcodecount = (int) $data[0];
							if($secretcodecount==0){
								$count_save_sql = "SELECT COUNT(*) FROM cscan_user_code where userID={$_SESSION['sess_userID']}";
								$rs = $DRW->query($count_save_sql,$DRW_read);
								$data = $DRW->fetch_row($rs);
								$codecount = (int) $data[0];
								
								if($codecount>=$number_machines){
									ob_end_clean();
									header("Location: logout.php?auth=1");
									exit;
								}
								elseif($codecount<$number_machines){
									$secretcode = $time;//mt_rand(100,1000000);
									$sql = "REPLACE INTO cscan_user_code (userID,code,initial_IP) VALUES ({$_SESSION['sess_userID']},$secretcode,'{$IPAddress}')";
									$DRW->query($sql,$DRW_main);
								}
							}
						}
						
						if($secretcode==0){
							$secretcode = $time;
						}
						if(!isset($_POST['rememberMe'])) {
							$username = '';
							$password = '';
						}
						$cookieArray = array($username,$password,$IPAddress,$_SESSION['sess_userID'],$secretcode,$time);
						$cookieArray = array_map('urlencode',$cookieArray);
						$COOKIEDOMAIN='.competiscan.com';
						setcookie('competiscaner',implode(":",$cookieArray),$time+(3600*1),$COOKIEPATH,$COOKIEDOMAIN);
						//setcookie('competiscaner',implode(":",$cookieArray),$time+(86400*364),$COOKIEPATH,$COOKIEDOMAIN,true, true,'SameSite=None');
						
						if($_SESSION['sess_userType']=='a'){
							$sql = "INSERT INTO cscan_user_tracker SET userID = '".$DRW->real_escape_string($_SESSION['sess_userID'])."', loginTime = curtime(), logoutTime=curtime(), IPAddress = '".$DRW->real_escape_string($IPAddress)."' , date = curdate(),cookie_code='".$DRW->real_escape_string($secretcode)."'";
						}
						else{
							$sql = "INSERT INTO cscan_user_tracker SET userID = '".$DRW->real_escape_string($parentID)."', subUserID = '".$DRW->real_escape_string($_SESSION['sess_userID'])."', loginTime = curtime(), logoutTime=curtime(), IPAddress = '".$DRW->real_escape_string($IPAddress)."' , date = curdate(),cookie_code='".$DRW->real_escape_string($secretcode)."'";
						}
						$rs = $DRW->query($sql,$DRW_main);
						$_SESSION['trackerID'] = $DRW->insert_id($DRW_main);
					}
                }
                break;

            case 400:
                if ($resuserdata['message'] == "New password is required to complete authentication.") {
                    header("Location: temp_password.php?user=" . base64_encode($_POST['userName']));
                    exit;
                }
                $message = "<ul><li style='text-align:center;'>" . $resuserdata['message'] . "</li></ul>";
                break;

            case 401:
				$message="<ul><li style='text-align:center'>".$resuserdata['message']."</li></ul>";
				break;
			 case 403:
				 if ($resuserdata['message'] == "SSO login required.") {
					header("Location: login_sso.php");
					exit;
				 }
				$message="<ul><li style='text-align:center'>".$resuserdata['message']."</li></ul>";
				break;
            case 404:
				$message="<ul><li style='text-align:center'>".$resuserdata['message']."</li></ul>";
				break;
            case 500:
				$otp_input_field = 1;
				$otp_input_pwdfield = 0;
				$session_email_otp_v = $resuserdata['data']['session'];
				$validmsg='Please enter a valid OTP';
                $message = "<ul><li style='text-align:center;'>" . $validmsg . "</li></ul>";
                if ($resuserdata['code'] == 500) {
                    $otp_input_pwdfield = 0;
                    $otp_input_field = 1;
                }
                break;

            default:
                $message = "<ul><li style='text-align:center;'>Unexpected error occurred.</li></ul>";
                break;
        }
    }
}
//if(isset($_POST['login']) or ((isset($_GET['is_admin']) and $_GET['is_admin']==1))){
if(isset($_SESSION['sess_userID'])){
	if(isset($_REQUEST['product']) && $_REQUEST['product']!=''){
		############## Start: Email Tracking ################
		if(!empty($tracking_id)){
			$loc = "productDetail.php?id=".(float)$_REQUEST['product'].'&trmsg='.$tracking_id;
		}else{
			$loc = "productDetail.php?id=".(float)$_REQUEST['product'];
		}
		// if (isset($_COOKIE['lastURL'])) {
		// 	$loc = $_COOKIE['lastURL'];
		// }
		################ End: Email Tracking ###############
	}
	elseif(isset($_REQUEST['trend_id']) && $_REQUEST['trend_id']!=''){
		$loc = "trend_reports.php?trend_id=".(int)$_REQUEST['trend_id'];
                //############### ADD ENCODE TREND ID############
	}
	elseif(isset($_REQUEST['document']) && $_REQUEST['document']!=''){
		$loc = "productDocuments.php?id=".(float)$_REQUEST['document'];
	}
	elseif(isset($_REQUEST['download']) && $_REQUEST['download']!=''){
		$loc = "downloads.php?id=".preg_replace('/\\W+/','',$_REQUEST['download']);
	}
	if (isset($_COOKIE['lastURL']) AND $_COOKIE['lastURL']!='null') {
	$loc = $_COOKIE['lastURL'];
    }
	ob_end_clean();
	header("Location: $loc");
	exit;
}
if($oldusername!='') {
	$checked = 'checked="checked"';
}
else {
	$checked = '';
}

?>

			<form name="loginForm1" action="login.php" method="post">
			
				<?php if(isset($_POST['us']) && !empty($_POST['us'])){
						if($successMessage){ ?>
			        <div class="row success" id="alert-message" >
					    <div class="col-md-10 col-md-offset-1 bg-success" style="text-align:center;color:green;">Password has been changed successfully.</div>
					</div>
			    <?php }else{ ?>
			    	<div class="row error" id="alert-message">
					    <div class="col-md-10 col-md-offset-1 bg-danger" style="text-align: center">Your reset password token has been expired.</div>
					</div>
			    <?php } }?>
				<?php if (isset($_GET['msg']) && $_GET['msg']!==1) { ?>
					<div class="row success" id="alert-message" >
					    <div class="col-md-10 col-md-offset-1" style="text-align:center;color:green;">Your password has been changed successfully.</div>
				</div>
                <?php } ?>
				<div class="row error" id="login-alerts" style="text-altext-align:center;<?php echo !$message ? 'display:none' : '' ?>">
				    <div class="col-md-10 col-md-offset-1 bg-danger"><?php echo $message; ?></div>
				</div>

                <div class="row form-row" style="padding-top: 30px;">
                  <div class="col-md-4 col-md-offset-4">
                    <div class="form-group" style="margin-bottom: 5px;">
                        <label for='username'>Email</label>
                        <!--<input type="hidden" name="trmsg" value="<?php echo $tracking_id; ?>" />-->
                        <input class="form-control" type='username' id='username' name='userName' value="<?php if($oldusername!=''){echo htmlspecialchars($oldusername, ENT_QUOTES);}elseif(isset($_SESSION['userName']) and $_SESSION['userName']!='') {echo $_SESSION['userName'];}?>" />
                    </div>
                    <div class="checkbox pull-right" style="margin-top: 0">
                        <label style="font-size: smaller"><input type="checkbox" name="rememberMe" value="1" style="position:relative;top:-2px;" <?php echo $checked; ?> /> Remember Me</label>
                    </div>
                  </div>
                </div>
				<!-- /.form-row -->

                <div class="row form-row" id ="password-row" style="<?php if($otp_input_pwdfield==1) { echo 'display:block;'; } else { echo 'display:none;'; }?>">
                  <div class="col-md-4 col-md-offset-4">
                    <div class="form-group" style="margin-bottom: 5px;">
                        <label for='password'>Password</label>
                        <input class="form-control" type='password' id='password' name='password' value="<?php echo htmlspecialchars($oldpassword, ENT_QUOTES);?>" />
                    </div>
                    <a href="forgot_password.php" id="forgot-password" class="red pull-right" style="font-size: smaller;">Forgot Password?</a>
                  </div>
                </div>
				<!-- /.form-row -->
				 <!-- /.form-row -->
				
                <div class="row form-row" id="verify-otp" style="<?php if($otp_input_field==1) { echo 'display:block;';} else{ echo 'display:none;'; }?>">
                  <div class="col-md-4 col-md-offset-4">
                    <div class="form-group" style="margin-bottom: 5px;">
                        <label for='password'>Verify OTP</label>
                        <input class="form-control" type='password' id='v_otp' name='v_otp' value="" maxlength="6"/>
                    </div>
					<input class="form-control" type='hidden' id='v_otp' name='verify_opt_seession' value="<?php echo $_SESSION['session']; ?>"/>
					<a href="login.php?resend_otp=1&auth_id=<?php echo base64_encode($_SESSION['userName']);?>&key=<?php echo base64_encode($_SESSION['password']);?>"
						id="resend_otp"
						class="red pull-right"
						style="font-size: smaller; pointer-events: auto;">Resend OTP</a>
						<span id="resend_timer" style="font-size: smaller; color: gray; margin-left: 10px;"></span>
                  </div>
                </div>
				<!-- /.form-row -->

                <div class="row form-row button-container">
                    <div class="col-md-12 text-center">
						<input name="login" type="hidden" value="login" />
                        <input type="submit" name="Submit" value="Submit" id="submit-login1" class="btn btn-primary" />
                    </div>
                </div>

			<?php if (isset($direct_request_type) && isset($direct_request_id)) { ?>
							<input name="<?php echo $direct_request_type ?>" type="hidden" value="<?php echo $direct_request_id ?>">
			<?php } ?>
			<br/>
			<br/>
			
			<div style="display: flex; justify-content: center;">
				<div style="text-align:center;font-size:11px;float:left;background-color: #f0f0f0; padding: 15px;"><div>If you have not logged in or reset your credentials since <b>June 7, 2025</b>, you will need to <br/> reset your password. Select the Forgot Password link to receive a reset code via email. <br/><br/>Still having issues logging in? <br/>Reach us at <b>contactus@competiscan.com.</b></div></div>
			</div>
			
			<br/>
			


</form>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const resendLink = document.getElementById('resend_otp');
    const timerText = document.getElementById('resend_timer');

    // Set the countdown time in seconds (AWS recommends ~30s)
    let cooldownTime = 60; 

    // Function to start countdown
    function startCountdown() {
        resendLink.style.pointerEvents = 'none'; // disable click
        resendLink.style.color = 'gray';
        timerText.textContent = ` (wait ${cooldownTime}s)`;

        const interval = setInterval(() => {
            cooldownTime--;
            timerText.textContent = ` (wait ${cooldownTime}s)`;

            if (cooldownTime <= 0) {
                clearInterval(interval);
                resendLink.style.pointerEvents = 'auto'; // enable again
                resendLink.style.color = 'red';
                timerText.textContent = ''; // clear text
            }
        }, 1000);
    }

    // Automatically start countdown if OTP field is visible
    const otpSection = document.getElementById('verify-otp');
    if (otpSection && otpSection.style.display === 'block') {
        startCountdown();
    }

    // Optional: If user clicks resend, restart timer after click
    resendLink.addEventListener('click', function() {
        startCountdown();
    });
});
</script>
<script>
(function($){
    $('#submit-login1').click(function(e) {
        e.preventDefault();
        $('#login-alerts').children('div').html('');
        if ($('#login-alerts').is(":visible")) $('#login-alerts').hide();

        var username = $('#username').val().trim();
		var v_otp = $('#v_otp').val().trim();
        var password = $('#password').val().trim();
        var otpVisible = $('#verify-otp').is(':visible');

        // If OTP flow -> password not required, but OTP required later by server
        if (!username) {
            $('#login-alerts').show();
            $('#login-alerts').children('div').html('<ul><li style="text-align:center;">Please enter your email address.</li></ul>');
            return;
        }
		if (!v_otp && otpVisible) {
            $('#login-alerts').show();
            $('#login-alerts').children('div').html('<ul><li style="text-align:center;">Please enter otp.</li></ul>');
            return;
        }
        if (!otpVisible && !password) {
            $('#login-alerts').show();
            $('#login-alerts').children('div').html('<ul><li style="text-align:center;">Please enter your password.</li></ul>');
            return;
        }
        // submit normally
        $('form[name="loginForm1"]')[0].submit();
    });

    setTimeout(function() {
        $('#alert-message').hide();
    }, 6000);
})(jQuery);
</script>

<!--<script>
    (function($){
		$('#submit-login1').click(function(e) {
			e.preventDefault();
			$('#login-alerts').children('div').html('');
			if($('#login-alerts').is(":visible")) $('#login-alerts').toggle(100);
			var username = $('#username').val();
			var password = $('#password').val();
			if (!username || !password) {
				$('#login-alerts').toggle(400);
				$('#login-alerts').children('div').html('<ul><li style=text-align:center;>Please enter your '+(!username ? 'email address' : '')+(!username && !password ? ' and ' : '')+(!password ? 'password' : '')+'.</li></ul>');
			}
			else {
				loginForm1.submit();
			}
		});

		setTimeout(function() {
            $('#alert-message').hide();
			//$('#login-alerts').hide();
        }, 6000);
    })(jQuery);
</script>-->
<?php
include 'footer_bottom.php';
# quote variable to make safe
function quote_smart($value) {
	global $DRW,$DRW_main,$DRW_read;
	// stripslashes
	if( get_magic_quotes_gpc() ) {
		$value = stripslashes( $value );
	}
	// quote if not integer
	if( !is_numeric( $value ) ) {
		$value = "'" . $DRW->real_escape_string( $value ) . "'";
	}
	return $value;
}
?>
