<?php
require_once('auth_config.php');
require_once('Auth.php');

$LOGIN_FORM = '';
$AUTH_USERNAME = '';
$AUTH_DATA = array();
if(!isset($ALLOW_GROUPS)) $ALLOW_GROUPS = array();
if(!isset($LOGIN_PAGE)) $LOGIN_PAGE = $DEFAULT_LOGIN_PAGE;
if(!isset($LOGOUT_PAGE)) $LOGOUT_PAGE = $DEFAULT_LOGOUT_PAGE;
if(!isset($EXPIRE_TIME)) $EXPIRE_TIME = $DEFAULT_EXPIRE_TIME;
if(!isset($IDLE_TIME)) $IDLE_TIME = $DEFAULT_IDLE_TIME;
if(!isset($SESSIONNAME)) $SESSIONNAME = 'my_session';
if(!isset($LOGIN_FUNCTION)) $LOGIN_FUNCTION = "loginFunction";
if(!isset($SHOW_LOGIN)) $SHOW_LOGIN = false;
if(!isset($COOKIE_AUTH)) $COOKIE_AUTH = false;
if(!isset($COOKIE_UNAME)) $COOKIE_UNAME = '';

$cook = 'competiscan_admin';
if(!empty($_SERVER['HTTP_HOST'])){
	$host = strtolower($_SERVER['HTTP_HOST']);
}
else{
	$host = '';
}

//$STORAGEDRIVER and $OPTIONS are required to continue
if(isset($STORAGEDRIVER) && isset($OPTIONS)) {
	// echo "<pre>";	
	// print_r($OPTIONS);
	// print_r($STORAGEDRIVER);
	// echo "</pre>";	
	$a = new Auth($STORAGEDRIVER, $OPTIONS, $LOGIN_FUNCTION, $SHOW_LOGIN);
	
	$a->setSessionName($SESSIONNAME);
		
	if(!isset($FAILED_LOGIN_CALLBACK)) $FAILED_LOGIN_CALLBACK = "doFailedLogin";
	$a->setFailedLoginCallback($FAILED_LOGIN_CALLBACK);
	
	if(!isset($LOGOUT_CALLBACK)) $LOGOUT_CALLBACK = "doLogout";
	$a->setLogoutCallback($LOGOUT_CALLBACK);
	
	if(!isset($LOGIN_CALLBACK)) $LOGIN_CALLBACK = "doLogin";
	$a->setLoginCallback($LOGIN_CALLBACK);
	
	if(!isset($CHECK_AUTH_CALLBACK)) $CHECK_AUTH_CALLBACK = "doCheck";
	$a->setCheckAuthCallback($CHECK_AUTH_CALLBACK);
	// echo "<pre>";
	// print_r($a);
	// echo "</pre>";
	if(isset($ADVANCED_SECURITY)){
		/*
		Detection of client ip address change or User-Agent header change if such a change is detected the user will be logged out
		
		Each client request a special unique cookie is given to the client. He must present this cookie on his next request. 
		This cookie changes on every request. If client does not present the valid cookie he will be logged out.
		
		Enables challenge response for the default login screen of auth. 
		The user password will be hashed with javascript before sent back to the server. 
		Prevents the user password being stolen using password sniffing tools. 
		Password is hashed with a random key so the md5 hash is not subject to brute force password cracking. 
		This will only work for storage containers which support challenge responce password authenthication. 
		Currently only the DB, MDB and MDB2 containers support this for md5 and clear text passwords 
		*/
		$a->setAdvancedSecurity($ADVANCED_SECURITY);
	}
	
	if(!isset($IDLE_TIME) && $DEFAULT_IDLE_TIME>0) $IDLE_TIME = $DEFAULT_IDLE_TIME;
	if(isset($IDLE_TIME)){
		if(isset($IDLE_TIME_ADD)) $add = $IDLE_TIME_ADD;
		else $add = false; //if $add==true $time is added to the existing expiration time, if FALSE the existing time value will be replaced
		$a->setIdle($IDLE_TIME,$add);
	}
	if(!isset($EXPIRE_TIME) && $DEFAULT_EXPIRE_TIME>0) $EXPIRE_TIME = $DEFAULT_EXPIRE_TIME;
	if(isset($EXPIRE_TIME)){
		if(isset($EXPIRE_TIME_ADD)) $add = $EXPIRE_TIME_ADD;
		else $add = false; //if $add==true $time is added to the existing expiration time, if FALSE the existing time value will be replaced
		$a->setExpire($EXPIRE_TIME,$add);
	}
	
	if(!isset($ALLOW_LOGIN)) $a->setAllowLogin(false);
	else $a->setAllowLogin($ALLOW_LOGIN);
	
	if($COOKIE_AUTH && $COOKIE_UNAME!='' && !defined('AUTH_SKIP_COOKIE_AUTH')) {
		$a->setAuth($COOKIE_UNAME);
		doLogin($COOKIE_UNAME,$a);
	}
	else {
		$a->start();
	}
}

function doFailedLogin($username, $a){
	$_SESSION = array();
	@session_destroy();
	if(strpos($GLOBALS['LOGOUT_PAGE'],'?')===false) $GLOBALS['LOGOUT_PAGE'] .= '?';
	else $GLOBALS['LOGOUT_PAGE'] .= '&';
	$GLOBALS['LOGOUT_PAGE'] .= "username=".urlencode($username);
	$GLOBALS['LOGOUT_PAGE'] .= "&status=".$a->getStatus();
	if(isset($_REQUEST['redir']) && $_REQUEST['redir']!='') $GLOBALS['LOGOUT_PAGE'] .= "&redir=".urlencode($_REQUEST['redir']);
	
	@ob_end_clean();
	header("Location: {$GLOBALS['LOGOUT_PAGE']}");
}

function doLogout($username, $a){
	// Ensure the session is active so we can clean it up.
	if(session_id() == '') {
		session_start();
	}

	$_SESSION = array();
	if (ini_get('session.use_cookies')) {
		$params = session_get_cookie_params();
		setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
	}
	@session_destroy();

	$time = time();

	if(isset($_COOKIE[$GLOBALS['cook'].'1'])) {
		setcookie($GLOBALS['cook'].'1', '', $time - 3600, $GLOBALS['COOKIEPATH'], $GLOBALS['COOKIEDOMAIN']);
	}
	if(isset($_COOKIE[$GLOBALS['cook'].'2'])) {
		setcookie($GLOBALS['cook'].'2', '', $time - 3600, $GLOBALS['COOKIEPATH'], $GLOBALS['COOKIEDOMAIN']);
	}
	if(isset($_COOKIE['competiscaner_a'])) {
		setcookie('competiscaner_a', '', $time - 3600, $GLOBALS['COOKIEPATH'], $GLOBALS['COOKIEDOMAIN']);
	}

	$logoutPage = $GLOBALS['LOGOUT_PAGE'];
	if(strpos($logoutPage,'?')===false) $logoutPage .= '?';
	else $logoutPage .= '&';
	$logoutPage .= 'status=0';

	if (ob_get_level() > 0) {
		@ob_end_clean();
	}
	header("Location: {$logoutPage}");
	exit;
}

function getStatusText($status){
	switch($status){
		case AUTH_IDLED: // -1
			return 'Session exceeded idle time.';
			break;
		case AUTH_EXPIRED: // -2
			return 'Session expired.';
			break;
		case AUTH_WRONG_LOGIN: // -3
			return 'Invalid username or password.';
			break;
		case AUTH_METHOD_NOT_SUPPORTED: // -4
			return 'Requested function is not implemented.';
			break;
		case AUTH_SECURITY_BREACH: // -5
			return 'Unauthorized';
			break;
                  //disable for the automatic logout issue.
		//case AUTH_CALLBACK_ABORT: // -6
			//return 'Unauthorized';
                        //return 'We see that you are having trouble logging in. Please call the Competiscan Help Desk (312) 488-1810 or email us: <a href="mailto:contactus@competiscan.com"> contactus@competiscan.com</a> and we will help reset your access as soon as possible.';
			//break;
		default:
			return 'Authorized';
	}
}

function getData(&$a){
	$dataArray = array();
	$keys = $a->getAuthData('_AUTH_DATA_KEYS');
	if(is_array($keys)){
		foreach($keys as $name=>$value){
			$dataArray[$value] = $a->getAuthData($value);
		}
	}
	return $dataArray;
}

//the functions below are all customizable
function loginFunction(){
	/*
	Change the HTML output so that it fits to your application.
	Or write a new funtion and call it with $LOGIN_FUNCTION
	*/
	
	global $cook;
	
	$username = '';
	$checked = '';
	$cookieName = $cook . '1';
	$isLogout = isset($_REQUEST['status']) && $_REQUEST['status'] === '0';
	
	if (!$isLogout && isset($_COOKIE[$cookieName]) && $_COOKIE[$cookieName] !== '' && $_COOKIE[$cookieName] !== '0') {
		$checked = ' checked="checked"';
		$username = $_COOKIE[$cookieName];
	}
	
	if (isset($_REQUEST['username']) && $_REQUEST['username'] !== '') {
		$username = $_REQUEST['username'];
	}
	
	$GLOBALS['LOGIN_FORM'] .= "<form action=\"{$_SERVER['PHP_SELF']}\" name=\"loginFrm\" method=\"post\" onsubmit=\"return validateLoginFrm();\">";
	$GLOBALS['LOGIN_FORM'] .= "<table border=\"0\" width=\"40%\" cellspacing=\"0\" cellpadding=\"0\" style=\"border:solid 1px #000000;\"><tr><td><table border=\"0\" width=\"100%\" class=\"text\" cellspacing=\"0\" cellpadding=\"8\">";
	$GLOBALS['LOGIN_FORM'] .= "<tr class=\"adminhead\"><td align=\"center\" colspan=\"2\">LOGIN INFORMATION</td></tr>";
	if(isset($_REQUEST['status']) && $_REQUEST['status']<0) {
		$GLOBALS['LOGIN_FORM'] .= "<tr><td align=\"center\" class=\"error\" colspan=\"2\">".getStatusText($_REQUEST['status'])."</td></tr>";
	}
	else $GLOBALS['LOGIN_FORM'] .= "<tr><td colspan=\"2\">&nbsp;</td></tr>";
	$GLOBALS['LOGIN_FORM'] .= "<tr><td height=\"50\" align=\"right\"><strong>Login:</strong> &nbsp;</td><td><input type=\"text\" name=\"username\" value=\"";
	$GLOBALS['LOGIN_FORM'] .= htmlspecialchars($username,ENT_COMPAT);
	$GLOBALS['LOGIN_FORM'] .= "\" /></td></tr>";
	$GLOBALS['LOGIN_FORM'] .= "<tr><td height=\"50\" align=\"right\"><strong>Password:</strong> &nbsp;</td><td><input type=\"password\" name=\"password\" /></td></tr>";
	$GLOBALS['LOGIN_FORM'] .= "<tr><td>&nbsp;</td><td><label><input type=\"checkbox\" name=\"rememberMe\" value=\"1\"$checked />Remember Me</label></td></tr>";
	$GLOBALS['LOGIN_FORM'] .= "<tr><td height=\"50\">&nbsp;</td><td><input class=\"button\" type=\"submit\" name=\"submit1\" value=\"Submit\" /></td></tr>";
	$GLOBALS['LOGIN_FORM'] .= "</table></td></tr></table>";
	if(isset($_REQUEST['redir']) && $_REQUEST['redir']!='') $GLOBALS['LOGIN_FORM'] .= "<input type=\"hidden\" name=\"redir\" value=\"".htmlspecialchars($_REQUEST['redir'],ENT_COMPAT)."\" />";
	$GLOBALS['LOGIN_FORM'] .= "<input type=\"hidden\" name=\"submit\" value=\"1\" /></form>";
}

/*function doLogin($username, $a){
	global $DRW,$DRW_main,$DRW_read;
	$overwrite = true;
	$keyArray = array();
	
	//the data below is application specific
	//add any key to the $keyArray (_AUTH_DATA_KEYS) for retrieval in getData
	
	$result = $DRW->query("SELECT userID,userName,user_email,user_status,a_number_machines,a_bypass FROM cscan_admin_users WHERE userName='".$DRW->real_escape_string($username)."'",$DRW_read);
	$row = $DRW->fetch_row($result);
	echo "<pre>";
	print_r($row);
	echo "</pre>";
	$userID = $row[0];
	$userName = $row[1];
	$user_email = $row[2];
	$user_status = $row[3];
	$a_number_machines = $row[4];
	$a_bypass = $row[5];
	
	$IPAddress = $_SERVER['REMOTE_ADDR'];
	$time = time();
	
	if(isset($_COOKIE['competiscaner_a'])){
		// echo "<pre>";
		// print_r($_COOKIE['competiscaner_a']);
		// echo "</pre>"; die;
		$cookieArray = explode(':',$_COOKIE['competiscaner_a']);
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
	}
	if($a_bypass!=1){
		if(preg_match('/^(\\d[^\\.]+\\.\\d[^\\.]+\\.)/',$IPAddress,$matches)){
			$check_ip = $matches[1];
		}
		else{
			$check_ip = substr($IPAddress,0,strrpos($IPAddress,'.'));
		}
		$count_save_sql = "SELECT COUNT(*) FROM cscan_admin_user_code where userID={$userID} AND (code='".$DRW->real_escape_string($secretcode)."' OR initial_IP LIKE '".$check_ip."%')";
		$rs = $DRW->query($count_save_sql,$DRW_read);
		$data = $DRW->fetch_row($rs);
		$secretcodecount = (int) $data[0];
		if($secretcodecount==0){
			$count_save_sql = "SELECT COUNT(*) FROM cscan_admin_user_code where userID={$userID}";
			$rs = $DRW->query($count_save_sql,$DRW_read);
			$data = $DRW->fetch_row($rs);
			$codecount = (int) $data[0];
			
			if($codecount>=$a_number_machines){
				$user_status = 0;
				//@ob_end_clean();
				//header("Location: {$GLOBALS['LOGOUT_PAGE']}");
			}
			elseif($codecount<$a_number_machines){
				$secretcode = $time;//mt_rand(100,1000000);
				$sql = "REPLACE INTO cscan_admin_user_code (userID,code,initial_IP) VALUES ({$userID},$secretcode,'{$IPAddress}')";
				$DRW->query($sql,$DRW_main);
			}
		}
	}
	
	$_SESSION['public_admin_access'] = $userID;
	
	$a->setAuthData('userID', $userID, $overwrite);//$_SESSION['sess_user_id']
	$keyArray[] = 'userID';
	$a->setAuthData('userName', $userName, $overwrite);//$_SESSION['sess_user_name']
	$keyArray[] = 'userName';
	$a->setAuthData('user_email', $user_email, $overwrite);//$_SESSION['sess_user_email']
	$keyArray[] = 'user_email';
	$a->setAuthData('user_status', $user_status, $overwrite);
	$keyArray[] = 'user_status';
	
	$groups = array();
	echo "SELECT permissionID FROM cscan_user_permission WHERE userID='".$DRW->real_escape_string($userID)."'"; die;
	$result = $DRW->query("SELECT permissionID FROM cscan_user_permission WHERE userID='".$DRW->real_escape_string($userID)."'",$DRW_read);
	
	while($data2 = $DRW->fetch_row($result)){
		echo "<pre>";
	print_r($data2);
	echo "</pre>"; die;
		$groups[] = $data2[0];
	}
	$a->setAuthData('GID', $groups, $overwrite);
	$keyArray[] = 'GID';
	
	$sectors = array();
	$categorys = array();
	$subcategorys = array();
	$result = $DRW->query("SELECT su.sectorID,parentID FROM cscan_sector_admin_users_allow su,cscan_sector cs WHERE userID=$userID AND su.sectorID=cs.sectorID",$DRW_read);
	while($data2 = $DRW->fetch_row($result)){
		if($data2[1]==0){
			$sectors[] = $data2[0];
		}
		else{
			$result2 = $DRW->query("SELECT parentID FROM cscan_sector WHERE sectorID=$data2[1]",$DRW_read);
			$data3 = $DRW->fetch_row($result2);
			if($data3[0]==0){
				$categorys[] = $data2[0];
			}
			else{
				$subcategorys[] = $data2[0];
			}
		}
	}
	
	$a->setAuthData('SID', $sectors, $overwrite);
	$keyArray[] = 'SID';
	$a->setAuthData('CID', $categorys, $overwrite);
	$keyArray[] = 'CID';
	$a->setAuthData('SCID', $subcategorys, $overwrite);
	$keyArray[] = 'SCID';

	//the data above is application specific
	
	$a->setAuthData('_AUTH_DATA_KEYS', $keyArray, $overwrite);
	
	if(isset($_REQUEST['redir']) && $_REQUEST['redir']!='') $GLOBALS['LOGIN_PAGE'] = $_REQUEST['redir'];
		
	if(isset($_REQUEST['rememberMe'])){
		if(!isset($_COOKIE[$GLOBALS['cook'].'1']) || $_COOKIE[$GLOBALS['cook'].'1']!=$userName) {
			setcookie($GLOBALS['cook'].'1',$userName,$time+(86400*364),$GLOBALS['COOKIEPATH'],$GLOBALS['COOKIEDOMAIN']);
		}
		if(in_array(31,$groups) && !isset($_COOKIE[$GLOBALS['cook'].'2'])) {
			setcookie($GLOBALS['cook'].'2','1',$time+(86400*7),$GLOBALS['COOKIEPATH'],$GLOBALS['COOKIEDOMAIN']);
		}
	}
	elseif(isset($_COOKIE[$GLOBALS['cook'].'1']) && !$GLOBALS['COOKIE_AUTH']){
		setcookie($GLOBALS['cook'].'1','0',$time - 3600,$GLOBALS['COOKIEPATH'],$GLOBALS['COOKIEDOMAIN']);
		setcookie($GLOBALS['cook'].'2','0',$time - 3600,$GLOBALS['COOKIEPATH'],$GLOBALS['COOKIEDOMAIN']);
	}
	
	$cookieArray = array($userName,'',$IPAddress,$userID,$secretcode,$time);
	$cookieArray = array_map('urlencode',$cookieArray);
	setcookie('competiscaner_a',implode(":",$cookieArray),$time+(86400*364),$GLOBALS['COOKIEPATH'],$GLOBALS['COOKIEDOMAIN']);
	
	@ob_end_clean();
	header("Location: {$GLOBALS['LOGIN_PAGE']}");
}*/
function doLogin($username, $a) {
    global $DRW, $DRW_main, $DRW_read;
    $overwrite = true;
    $keyArray = [];

    $result = $DRW->query(
        "SELECT userID, userName, user_email, user_status, a_number_machines, a_bypass 
         FROM cscan_admin_users 
         WHERE userName='" . $DRW->real_escape_string($username) . "'",
        $DRW_read
    );
    $row = $DRW->fetch_row($result);

    // FIX 1: Guard against empty/null result row
    if (empty($row)) {
        header("Location: {$GLOBALS['LOGOUT_PAGE']}");
        exit;
    }

    $userID           = $row[0] ?? 0;
    $userName         = $row[1] ?? '';
    $user_email       = $row[2] ?? '';
    $user_status      = (int)($row[3] ?? 0);
    $a_number_machines = (int)($row[4] ?? 0);
    $a_bypass         = (int)($row[5] ?? 0);

    $IPAddress = $_SERVER['REMOTE_ADDR'];
    $time      = time();

    // FIX 2: isset() guard before accessing $_COOKIE
    $secretcode = 0;
    if (isset($_COOKIE['competiscaner_a'])) {
        $cookieArray  = explode(':', $_COOKIE['competiscaner_a']);
        $cookieArray  = array_map('urldecode', $cookieArray);
        $oldusername  = $cookieArray[0] ?? '';
        $oldpassword  = $cookieArray[1] ?? '';
        $oldIPAddress = $cookieArray[2] ?? '';
        $olduserID    = $cookieArray[3] ?? '';
        $secretcode   = $cookieArray[4] ?? 0;
        $uctimestamp  = $cookieArray[5] ?? 0;
    }

    if ($a_bypass != 1) {
        if (preg_match('/^(\d[^\.]+\.\d[^\.]+\.)/', $IPAddress, $matches)) {
            $check_ip = $matches[1];
        } else {
            $check_ip = substr($IPAddress, 0, strrpos($IPAddress, '.'));
        }

        $count_save_sql = "SELECT COUNT(*) FROM cscan_admin_user_code 
                           WHERE userID={$userID} 
                           AND (code='" . $DRW->real_escape_string($secretcode) . "' 
                                OR initial_IP LIKE '" . $DRW->real_escape_string($check_ip) . "%')";
        $rs   = $DRW->query($count_save_sql, $DRW_read);
        $data = $DRW->fetch_row($rs);
        $secretcodecount = (int)($data[0] ?? 0);

        if ($secretcodecount == 0) {
            $count_save_sql = "SELECT COUNT(*) FROM cscan_admin_user_code WHERE userID={$userID}";
            $rs   = $DRW->query($count_save_sql, $DRW_read);
            $data = $DRW->fetch_row($rs);
            $codecount = (int)($data[0] ?? 0);

            if ($codecount >= $a_number_machines) {
                $user_status = 0;
            } elseif ($codecount < $a_number_machines) {
                $secretcode = $time;
                $sql = "REPLACE INTO cscan_admin_user_code (userID, code, initial_IP) 
                        VALUES ({$userID}, {$secretcode}, '" . $DRW->real_escape_string($IPAddress) . "')";
                $DRW->query($sql, $DRW_main);
            }
        }
    }

    $_SESSION['public_admin_access'] = $userID;

    $a->setAuthData('userID',      $userID,      $overwrite);
    $keyArray[] = 'userID';
    $a->setAuthData('userName',    $userName,    $overwrite);
    $keyArray[] = 'userName';
    $a->setAuthData('user_email',  $user_email,  $overwrite);
    $keyArray[] = 'user_email';
    $a->setAuthData('user_status', $user_status, $overwrite);
    $keyArray[] = 'user_status';

    $groups = [];
    $result = $DRW->query(
        "SELECT permissionID FROM cscan_user_permission 
         WHERE userID='" . $DRW->real_escape_string($userID) . "'",
        $DRW_read
    );
    while ($data2 = $DRW->fetch_row($result)) {
        $groups[] = $data2[0];
    }
    $a->setAuthData('GID', $groups, $overwrite);
	// echo "<pre>";
	// print_r($groups);
	// echo "</pre>"; die;
    $keyArray[] = 'GID';

    $sectors      = [];
    $categorys    = [];
    $subcategorys = [];

    $result = $DRW->query(
        "SELECT su.sectorID, parentID 
         FROM cscan_sector_admin_users_allow su, cscan_sector cs 
         WHERE userID={$userID} AND su.sectorID=cs.sectorID",
        $DRW_read
    );
    while ($data2 = $DRW->fetch_row($result)) {
        if ($data2[1] == 0) {
            $sectors[] = $data2[0];
        } else {
            $result2 = $DRW->query(
                "SELECT parentID FROM cscan_sector WHERE sectorID={$data2[1]}",
                $DRW_read
            );
            $data3 = $DRW->fetch_row($result2);
            if (($data3[0] ?? 0) == 0) {
                $categorys[] = $data2[0];
            } else {
                $subcategorys[] = $data2[0];
            }
        }
    }

    $a->setAuthData('SID',  $sectors,      $overwrite);
    $keyArray[] = 'SID';
    $a->setAuthData('CID',  $categorys,    $overwrite);
    $keyArray[] = 'CID';
    $a->setAuthData('SCID', $subcategorys, $overwrite);
    $keyArray[] = 'SCID';

    $a->setAuthData('_AUTH_DATA_KEYS', $keyArray, $overwrite);

    if (isset($_REQUEST['redir']) && $_REQUEST['redir'] != '') {
        $GLOBALS['LOGIN_PAGE'] = $_REQUEST['redir'];
    }

    if (isset($_REQUEST['rememberMe'])) {
        if (!isset($_COOKIE[$GLOBALS['cook'] . '1']) || $_COOKIE[$GLOBALS['cook'] . '1'] !== $userName) {
            setcookie($GLOBALS['cook'] . '1', $userName, $time + (86400 * 364), $GLOBALS['COOKIEPATH'], $GLOBALS['COOKIEDOMAIN']);
        }
        if (in_array(31, $groups) && !isset($_COOKIE[$GLOBALS['cook'] . '2'])) {
            setcookie($GLOBALS['cook'] . '2', '1', $time + (86400 * 7), $GLOBALS['COOKIEPATH'], $GLOBALS['COOKIEDOMAIN']);
        }
    } elseif (isset($_COOKIE[$GLOBALS['cook'] . '1']) && !$GLOBALS['COOKIE_AUTH']) {
        setcookie($GLOBALS['cook'] . '1', '0', $time - 3600, $GLOBALS['COOKIEPATH'], $GLOBALS['COOKIEDOMAIN']);
        setcookie($GLOBALS['cook'] . '2', '0', $time - 3600, $GLOBALS['COOKIEPATH'], $GLOBALS['COOKIEDOMAIN']);
    }

    $cookieArray = [$userName, '', $IPAddress, $userID, $secretcode, $time];
    $cookieArray = array_map('urlencode', $cookieArray);
    setcookie('competiscaner_a', implode(':', $cookieArray), $time + (86400 * 364), $GLOBALS['COOKIEPATH'], $GLOBALS['COOKIEDOMAIN']);

    // FIX 3: Safe output buffer flush — no @ suppression needed
    if (ob_get_level() > 0) {
        ob_end_clean();
    }
    header("Location: {$GLOBALS['LOGIN_PAGE']}");
    exit;
}

function doCheck($username, $a){
	$GLOBALS['AUTH_USERNAME'] = $username;
	$GLOBALS['AUTH_DATA'] = getData($a);
	
	$return = true;
	
	//the data below is application specific
	
	if(isset($GLOBALS['AUTH_DATA']) && $GLOBALS['AUTH_DATA']['user_status']!=1){
		$return = false;
	}
	elseif(count($GLOBALS['ALLOW_GROUPS'])>0){
		$return = false;
		foreach($GLOBALS['ALLOW_GROUPS'] as $check_gid){
			if(in_array($check_gid,$GLOBALS['AUTH_DATA']['GID'])) {
				$return = true;
				break;
			}
		}
	}
	
	//the data above is application specific
	
	return $return;
	
}

//this function is application specific for checking if group is allowed within a page
function checkGroup($gid){
	if(isset($GLOBALS['AUTH_DATA']['GID'])){
		if(in_array($gid,$GLOBALS['AUTH_DATA']['GID'])){
			return true;
		}
	}
	return false;
}
function checkSector($sid){
	if(isset($GLOBALS['AUTH_DATA']['SID'])){
		if(intval($sid)==0 || in_array($sid,$GLOBALS['AUTH_DATA']['SID'])){
			return true;
		}
	}
	return false;
}
function checkCategory($cid){
	if(isset($GLOBALS['AUTH_DATA']['CID'])){
		if(intval($cid)==0 || in_array($cid,$GLOBALS['AUTH_DATA']['CID'])){
			return true;
		}
	}
}
function checkSubCategory($scid){
	if(isset($GLOBALS['AUTH_DATA']['SCID'])){
		if(intval($scid)==0 || in_array($scid,$GLOBALS['AUTH_DATA']['SCID'])){
			return true;
		}
	}
	return false;
}
?>