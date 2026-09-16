<?php
$PAGE_HEADING = "Login";
$TITLE = "Welcome To Competiscan";
$HEAD = '<script src="includes/index.js" type="text/JavaScript"></script>';
include 'header_top.php';
if(!defined('ENV')){
define('ENV',getenv('SERVER_NAME'));
}
// echo "<pre>";
// print_r($_POST);
// echo "</pre>";
// echo "sdsdsdsds"; 
if(!empty($_POST['byadmin'])){
    $_POST['login']='1';
    $_SESSION['sess_username']  = '';
    $_SESSION['sess_userID']    = ''; 
    $_SESSION['sess_userType']  = '';
    $_SESSION['sess_companyName'] = '';
    $_SESSION['sess_plevel'] = 0;
    $_SESSION['sess_mchannel'] = array();
    $_SESSION['sess_mpanel'] = array();
    $_SESSION['sess_sector'] = array();
    $_SESSION['sess_category'] = array();
    $_SESSION['sess_subcategory'] = array();
    $_SESSION['sess_search_exclude'] = array();
    $_SESSION['sess_search_additional_field'] = array();
    ################# Start for Anotation Tool Link Permission################
    $_SESSION['sess_anotation_tool_link'] = array();
    $uid    =   $_POST['userID'];
    
    $sqlsel = sprintf("SELECT userID,emailAddress,password FROM cscan_users WHERE active='y' AND userID='".$uid."'" );
	
    $resultsel              = $DRW->query($sqlsel,$DRW_read);
    $resultselrow              = $DRW->fetch_assoc($resultsel);
    $_POST['userName']      = $resultselrow['emailAddress'];
    $_POST['password']      = $resultselrow['password'];
    
}        
//exit;
$message = '';
$loc = "fullsearch.php?searchview=2";
$number_machines = 1;
$bypass = 0;

##### Start: Email Tracking ######
$tracking_id = (!empty($_REQUEST['trmsg']))?trim($_REQUEST['trmsg']):'';
##### End: Email Tracking ######

if (isset($_GET['product']) && $_GET['product']!='') {
    $direct_request_type = 'product';
    $direct_request_id = (float)$_GET['product'];
} elseif(isset($_GET['trend_id']) && $_GET['trend_id']!='') {
    $direct_request_type = 'trend_id';
    $direct_request_id = (int)$_GET['trend_id'];
    //############### ADD ENCODE TREND ID############
    /*if(ENV == 'localhost' || ENV == 'demo.competiscan.com'){
     $direct_request_id = trim($_GET['trend_id']);
    }*/
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
if(isset($_POST['login']) || isset($_SESSION['sess_username'])){
	$username  = $_POST['userName'];
	$password  = $_POST['password']; 
	$IPAddress = $_SERVER['REMOTE_ADDR'];
	$sql = sprintf("SELECT userID,number_machines,bypass,companyName,plevel FROM cscan_users WHERE active='y' AND emailAddress=%s AND password=%s", quote_smart( $username ), quote_smart( $password ) );
	if($_SESSION['sess_username']!=""){
		$username  = $_SESSION['sess_username'];
		$sql="SELECT userID,number_machines,bypass,companyName,plevel FROM cscan_users WHERE active='y' AND emailAddress='".$username."'";
	}
	$result = $DRW->query($sql,$DRW_read);
	$rs        = $DRW->fetch_assoc($result);
	$userID    = $rs['userID'];
	$count    = $DRW->num_rows($result);
	$number_machines = $rs['number_machines'];
	$bypass = $rs['bypass'];
	$companyName = $rs['companyName'];
	$plevel = $rs['plevel'];
	
	if($count > 0) {
		$_SESSION['sess_username'] = $username;
		$_SESSION['sess_userID']   = $userID; 
		$_SESSION['sess_userType'] = 'a';
		$_SESSION['sess_companyName'] = $companyName;
		$_SESSION['sess_plevel'] = $plevel;
		$parentID = 0;
		$_SESSION['sess_mchannel'] = array();
		$result = $DRW->query("SELECT mu.mChannelID FROM cscan_mc_users_allow mu,cscan_mchannel mc WHERE userID=$userID AND mu.mChannelID=mc.mChannelID",$DRW_read);
		while($data2 = $DRW->fetch_row($result)){
			$_SESSION['sess_mchannel'][] = $data2[0];
		}
		$_SESSION['sess_mpanel'] = array();
		$result = $DRW->query("SELECT mu.mPanelID FROM cscan_mp_users_allow mu,cscan_mpanel mc WHERE userID=$userID AND mu.mPanelID=mc.mPanelID",$DRW_read);
		while($data2 = $DRW->fetch_row($result)){
			$_SESSION['sess_mpanel'][] = $data2[0];
		}
		$_SESSION['sess_sector'] = array();
		$_SESSION['sess_category'] = array();
		$_SESSION['sess_subcategory'] = array();
		$result = $DRW->query("SELECT su.sectorID,parentID FROM cscan_sector_users_allow su,cscan_sector cs WHERE userID=$userID AND su.sectorID=cs.sectorID",$DRW_read);
		while($data2 = $DRW->fetch_row($result)){
			if($data2[1]==0){
				$_SESSION['sess_sector'][] = $data2[0];
			}
			else{
				$result2 = $DRW->query("SELECT parentID FROM cscan_sector WHERE sectorID=$data2[1]",$DRW_read);
				$data3 = $DRW->fetch_row($result2);
				if($data3[0]==0){
					$_SESSION['sess_category'][] = $data2[0];
				}
				else{
					$_SESSION['sess_subcategory'][] = $data2[0];
				}
			}
		}
		$_SESSION['sess_search_exclude'] = array();
		$result = $DRW->query("SELECT search_field FROM cscan_search_exclude WHERE userID=$userID",$DRW_read);
		while($data2 = $DRW->fetch_row($result)){
			$_SESSION['sess_search_exclude'][] = $data2[0];
		}
                
                $_SESSION['sess_search_additional_field']= array();
                $result = $DRW->query("SELECT field_name FROM cscan_users_additional_fields_allow WHERE userID=$userID",$DRW_read);
		while($data2 = $DRW->fetch_row($result)){
                    $_SESSION['sess_search_additional_field'][] = $data2[0];
		}
                ################# Start for Anotation Tool Link Permission################
                $_SESSION['sess_anotation_tool_link'] = array();
                $result = $DRW->query("SELECT name FROM cscan_user_anotation_tool_allow WHERE userID=$userID",$DRW_read);
		while($data2 = $DRW->fetch_row($result)){
                    $_SESSION['sess_anotation_tool_link'][] = $data2[0];
		}
                ################# END for Anotation Tool Link Permission################
	}
	else {
		$sql = sprintf("SELECT ID,parentID FROM cscan_sub_users WHERE active='y' AND emailAddress=%s and password=%s", quote_smart( $username ), quote_smart( $password ) );
		$result = $DRW->query($sql,$DRW_read);
		$rs        = $DRW->fetch_assoc($result);
		$userID    = $rs['ID'];          // Sub user ID;
		$parentID = $rs['parentID'];    // Parent ID;
		$count    = $DRW->num_rows($result);
		
		if($count > 0) {
			$_SESSION['sess_username']  = $username;
			$_SESSION['sess_userID']    = $userID; 
			$_SESSION['sess_userType']  = 'u';
			$_SESSION['sess_companyName'] = '';
			$_SESSION['sess_plevel'] = 0;
			$_SESSION['sess_mchannel'] = array();
			$_SESSION['sess_mpanel'] = array();
			$_SESSION['sess_sector'] = array();
			$_SESSION['sess_category'] = array();
			$_SESSION['sess_subcategory'] = array();
			$_SESSION['sess_search_exclude'] = array();
                        $_SESSION['sess_search_additional_field']= array();
                        ################# Start for Anotation Tool Link Permission################
                        $_SESSION['sess_anotation_tool_link']= array();
		}
		else {
			if($_POST['login']=='index'){
				ob_end_clean();
				header("Location: index.php?id=1");
				exit;
			}
			$message = "<ul><li style='text-align:center'>Incorrect Username or Password</li></ul>";//"Sorry! you are not an authorized user";
		}
	}
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
		setcookie('competiscaner',implode(":",$cookieArray),$time+(86400*364),$COOKIEPATH,$COOKIEDOMAIN);
		
		if($_SESSION['sess_userType']=='a'){
			$sql = "INSERT INTO cscan_user_tracker SET userID = '".$DRW->real_escape_string($_SESSION['sess_userID'])."', loginTime = curtime(), logoutTime=curtime(), IPAddress = '".$DRW->real_escape_string($IPAddress)."' , date = curdate(),cookie_code='".$DRW->real_escape_string($secretcode)."'";
		}
		else{
			$sql = "INSERT INTO cscan_user_tracker SET userID = '".$DRW->real_escape_string($parentID)."', subUserID = '".$DRW->real_escape_string($_SESSION['sess_userID'])."', loginTime = curtime(), logoutTime=curtime(), IPAddress = '".$DRW->real_escape_string($IPAddress)."' , date = curdate(),cookie_code='".$DRW->real_escape_string($secretcode)."'";
		}
		$rs = $DRW->query($sql,$DRW_main);
		$_SESSION['trackerID'] = $DRW->insert_id($DRW_main);
		
		$_SESSION['sess_dashboard'] = false;
		$query_c ="SELECT count(*) FROM cscan_edc_user WHERE userID='".$DRW->real_escape_string($_SESSION['sess_userID'])."'";
		$result_c = $DRW->query($query_c,$DRW_read);
		$row_c = $DRW->fetch_row($result_c);
		if(!empty($row_c[0])){
			$_SESSION['sess_dashboard'] = true;
		}
	}
}
if(isset($_SESSION['sess_userID'])){
	if(isset($_REQUEST['product']) && $_REQUEST['product']!=''){
            ############## Start: Email Tracking ################
            if(!empty($tracking_id)){
                $loc = "productDetail.php?id=".(float)$_REQUEST['product'].'&trmsg='.$tracking_id;
            }else{
                $loc = "productDetail.php?id=".(float)$_REQUEST['product'];
            }
            ################ End: Email Tracking ###############
	}
	elseif(isset($_REQUEST['trend_id']) && $_REQUEST['trend_id']!=''){
		$loc = "trend_reports.php?trend_id=".(int)$_REQUEST['trend_id'];
                //############### ADD ENCODE TREND ID############
                /*if(ENV == 'localhost' || ENV == 'demo.competiscan.com'){
                $loc = "trend_reports.php?trend_id=".trim($_REQUEST['trend_id']);
                }*/
	}
	elseif(isset($_REQUEST['document']) && $_REQUEST['document']!=''){
		$loc = "productDocuments.php?id=".(float)$_REQUEST['document'];
	}
	elseif(isset($_REQUEST['download']) && $_REQUEST['download']!=''){
		$loc = "downloads.php?id=".preg_replace('/\\W+/','',$_REQUEST['download']);
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


if(isset($_POST['us']) && !empty($_POST['us'])){
	$successMessage = 0;
	$decryptedID = convert_uudecode(base64_decode($_POST['us']));
	$query = "SELECT userID FROM cscan_users WHERE userID='".$decryptedID."' AND reset_password_token='".$_POST['token']."'" ;
	$rs = $DRW->query($query,$DRW_read);
	$data = $DRW->fetch_row($rs);
	if(!empty($data[0])){
	    $query = "UPDATE `cscan_users` SET password='" . $_POST['newPassword'] . "', reset_password_token='".NULL."' WHERE userID='".$data[0]."'";
	    if($DRW->query($query, $DRW_main)){
	        $successMessage = 1;
	    }
	}
}

?>


			<form name="loginForm" action="login.php" method="post" onsubmit="return validate();">

				<?php if(isset($_POST['us']) && !empty($_POST['us'])){
						if($successMessage == 1){ ?>
			        <div class="row success" id="alert-message" >
					    <div class="col-md-10 col-md-offset-1 bg-success" style="text-align:center;color:green;">Password has been changed successfully.</div>
					</div>
			    <?php }else{ ?>
			    	<div class="row error" id="alert-message">
					    <div class="col-md-10 col-md-offset-1 bg-danger" style="text-align: center">Your reset password token has been expired.</div>
					</div>
			    <?php } }?>
				<div class="row error" id="login-alerts" style="<?php echo !$message ? 'display:none' : '' ?>">
				    <div class="col-md-10 col-md-offset-1 bg-danger"><?php echo $message; ?></div>
				</div>

                <div class="row form-row" style="padding-top: 30px;">
                  <div class="col-md-4 col-md-offset-4">
                    <div class="form-group" style="margin-bottom: 5px;">
                        <label for='username'>Email</label>
                        <input type="hidden" name="trmsg" value="<?php echo $tracking_id; ?>" />
                        <input class="form-control" type='username' id='username' name='userName' value="<?php echo htmlspecialchars($oldusername, ENT_QUOTES);?>" />
                    </div>
                    <div class="checkbox pull-right" style="margin-top: 0">
                        <label style="font-size: smaller"><input type="checkbox" name="rememberMe" value="1" style="position:relative;top:-2px;" <?php echo $checked; ?> /> Remember Me</label>
                    </div>
                  </div>
                </div><!-- /.form-row -->

                <div class="row form-row">
                  <div class="col-md-4 col-md-offset-4">
                    <div class="form-group" style="margin-bottom: 5px;">
                        <label for='password'>Password</label>
                        <input class="form-control" type='password' id='password' name='password' value="<?php echo htmlspecialchars($oldpassword, ENT_QUOTES);?>" />
                    </div>
                    <a href="forgot-password.php" id="forgot-password" class="red pull-right" style="font-size: smaller;">Forgot Password?</a>
                  </div>
                </div><!-- /.form-row -->

                <div class="row form-row button-container">
                    <div class="col-md-12 text-center">
						<input name="login" type="hidden" value="login" />
                        <input type="submit" name="Submit" value="Submit" id="submit-login" class="btn btn-primary" />&nbsp;&nbsp;
						<!--<a class="btn btn-primary" href="<?php echo AUTH_URL_DIRECT_LINK; ?>">Login with SSO</a>-->
						<a class="btn btn-primary" href="<?php echo AUTH_URL_PROD; ?>response_type=code&client_id=<?php echo CLIENT_ID_PROD; ?>&redirect_uri=https://competiscan.com/sso_auth_prod.php">Login with SSO</a>
                    </div>
                </div>

<?php if (isset($direct_request_type) && isset($direct_request_id)) { ?>
				<input name="<?php echo $direct_request_type ?>" type="hidden" value="<?php echo $direct_request_id ?>">
<?php } ?>

            </form>


<script>
    (function($){
		$('#submit-login').click(function(e) {
			e.preventDefault();
			$('#login-alerts').children('div').html('');
			if($('#login-alerts').is(":visible")) $('#login-alerts').toggle(100);
			var username = $('#username').val();
			var password = $('#password').val();
			if (!username || !password) {
				$('#login-alerts').toggle(400);
				$('#login-alerts').children('div').html('<ul><li>Please enter your '+(!username ? 'email address' : '')+(!username && !password ? ' and ' : '')+(!password ? 'password' : '')+'.</li></ul>');
			}
			else {
				loginForm.submit();
			}
		});

		setTimeout(function() {
            $('#alert-message').hide();
        }, 6000);
    })(jQuery);
</script>



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
