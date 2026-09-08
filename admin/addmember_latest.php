<?php
$ALLOW_GROUPS = array(1);
require_once("../auth_auth.php");
include 'top.php';
require_once '../includes/functions.php';
//require_once 'TemplateMailer.php';
$page_heading = 'ADD NEW MEMBER';
$page_message = 'Please fill following details to add new user';
$updID = '';
$password = '';
$firstName = '';
$lastName = '';
$emailAddress = '';
$companyName = '';
$clientName = '';
$streetAddress = '';
$city = '';
$state = '';
$country = '';
$zipCode = '';
$phone = '';
$fax = '';
$plevel = 0;
$active = '';
$msg = '';
$number_machines = 1;
$bypass = 0;
$sectorAllow = array();
$mcAllow = array();
$mpAllow = array();
$searchExclude = array();
$retailstate_user = array();
$edc_user = array();
$additionaldataArray=array();
require_once('membersearch_fields.php');
//$search_fields = membersearch_fields.php
$javascript = '';
if(isset($_REQUEST['id'])) {
	$updID = $_REQUEST['id'];
	if( $updID != '' ) {
		$page_heading = 'UPDATE MEMBER';
		$page_message = 'Please fill following details to update this member';
		
		// fetch existing product information
		$sql = "SELECT password, firstName, lastName, emailAddress, companyName, clientName, streetAddress, city, state, country, zipCode, phone, fax,number_machines,bypass,plevel,active FROM cscan_users WHERE userID='$updID'";
		$result = $DRW->query( $sql,$DRW_read );
		
		if( $DRW->num_rows( $result ) > 0 ) {
			$row = $DRW->fetch_array($result);
			//$userName = $row['userName'];
			$password = $row['password'];
			$firstName = $row['firstName'];
			$lastName = $row['lastName'];
			$emailAddress = $row['emailAddress'];
			$companyName = $row['companyName'];
			$clientName = $row['clientName'];
			$streetAddress = $row['streetAddress'];
			$city = $row['city'];
			$state = $row['state'];
			$country = $row['country'];
			$zipCode = $row['zipCode'];
			$phone = $row['phone'];
			$fax = $row['fax'];
			$number_machines = $row['number_machines'];
			$bypass = $row['bypass'];
			if($zipCode==0) $zipCode = '';
			if($phone==0) $phone = '';
			$plevel = $row['plevel'];
                        $active = $row['active'];
		}
	}
	//else{
	//	$mcAllow = array(1,3,2);//Direct Mail,Electronic,Print
	//}
}
elseif(isset($_REQUEST['updID'])) {
	$updID = $_REQUEST['updID'];
}

if(isset($_POST['save'])) {
	
	if (isset($_POST['password'])) {
		$password = $_POST['password'];
	} else {
		// Handle missing password
		$password ='';
	}
	$firstName = $_POST['firstName'];
	$lastName = $_POST['lastName'];
	$emailAddress = trim($_POST['emailAddress']);
	$old_emailAddress = $_POST['old_emailAddress'];
	$companyName = trim($_POST['companyName']);
	$clientName = $_POST['clientName'];
	$streetAddress = $_POST['streetAddress'];
	$city = $_POST['city'];
	$state = $_POST['state'];
	$country = $_POST['country'];
	$zipCode = $_POST['postalCode'];
	if($zipCode == '') $zipCode = 0;
	$phone = $_POST['phone'];
	if($phone =='') $phone = 0;
	$fax = $_POST['fax'];
	$plevel = (int)$_POST['plevel'];
	################# for add country permission ################
	if(isset($_POST['country_allow'])){
		$country_allow = $_POST['country_allow'];
	}
        ################# for add country permission ################
	if(isset($_POST['mc_allow'])){
		$mcAllow = $_POST['mc_allow'];
	}
	if(isset($_POST['mp_allow'])){
		$mpAllow = $_POST['mp_allow'];
	}
        ################# Start for Additional Fields ################
        $additionalAllow=array();
	if(isset($_POST['additional_allow'])){
		$additionalAllow = $_POST['additional_allow'];
	}
        ################# end for Additional Fields################
        
         ################# Start for Additional Fields ################
        $pageAllow=array();
	if(isset($_POST['page_allow'])){
		$pageAllow = $_POST['page_allow'];
	}
        ################# end for Additional Fields################        
        ################# Start for Anotation Tool Link Permission################
        $anotationToolAllow=array();
	if(isset($_POST['anotation_tool_allow'])){
		$anotationToolAllow = $_POST['anotation_tool_allow'];
	}
        ################# End Start for Anotation Tool Link Permission################  
     ################# Start for AI ANALYSIS Link Permission################
	 $ai_analysis_allow=array();
	 if(isset($_POST['ai_analysis_allow'])){
		 $ai_analysis_allow = $_POST['ai_analysis_allow'];
	 }
	################# End Start for ANALYSIS Tool Link Permission################  
	$sql = "SELECT sectorID FROM cscan_sector";
	$rs = $DRW->query($sql,$DRW_read);
	while($row = $DRW->fetch_array($rs)) {
		if(isset($_POST['sectorid_'.$row[0]]) && $_POST['sectorid_'.$row[0]]==1){
			$sectorAllow[] = $row[0];
		}
	}
	if(isset($_POST['searchExclude'])){
		$searchExclude = $_POST['searchExclude'];
	}
	if(isset($_POST['retailstate_user'])){
		$retailstate_user = $_POST['retailstate_user'];
	}
	if(isset($_POST['edc_user'])){
		$edc_user = $_POST['edc_user'];
	}
	
	$old_number_machines = (int)$_POST['old_number_machines'];
	$number_machines = (int)$_POST['number_machines'];
	if($number_machines<$old_number_machines || isset($_POST['resetcodes'])){
		$del = "DELETE FROM cscan_user_code WHERE userID='$updID'";
		$DRW->query($del,$DRW_main);
	}
	if(isset($_POST['bypass'])) $bypass = (int)$_POST['bypass'];
	else $bypass = 0;
	
	if($updID!='')  $sql = "SELECT userID FROM cscan_users WHERE emailAddress='".$DRW->real_escape_string($emailAddress)."' AND userID<>'$updID'";
	else $sql = "SELECT userID FROM cscan_users WHERE emailAddress='".$DRW->real_escape_string($emailAddress)."'";  
	
	$result = $DRW->query($sql,$DRW_read);
	
	if( $DRW->num_rows($result) == 0 ) {
		$sql = "SELECT ID FROM cscan_sub_users WHERE emailAddress='".$DRW->real_escape_string($emailAddress)."' AND password='".$DRW->real_escape_string($password)."'";
		$chk_result = $DRW->query($sql,$DRW_read);
		if($DRW->num_rows($chk_result) == 0) {
			if($updID == '') {
				$insert_sql = "INSERT INTO cscan_users (password,firstName,lastName,emailAddress,companyName,clientName,streetAddress,city,state,country,zipCode,phone,fax,number_machines,bypass,plevel,dateAdded)
					VALUES ('".$DRW->real_escape_string($password)."','".$DRW->real_escape_string($firstName)."','".$DRW->real_escape_string($lastName)."','".$DRW->real_escape_string($emailAddress)."','".$DRW->real_escape_string($companyName)."','".$DRW->real_escape_string($clientName)."','".$DRW->real_escape_string($streetAddress)."','".$DRW->real_escape_string($city)."','".$DRW->real_escape_string($state)."','".$DRW->real_escape_string($country)."','".$DRW->real_escape_string($zipCode)."','".$DRW->real_escape_string($phone)."','".$DRW->real_escape_string($fax)."','$number_machines','$bypass',$plevel,NOW())"; 
				$DRW->query($insert_sql,$DRW_main);
				$updID = $DRW->insert_id($DRW_main);
                                //ADD CLIENT LOG
				$sql_client_log = "insert INTO cscan_client_profile_log (adminID,userID) VALUES ('".$GLOBALS['AUTH_DATA']['userID']."','".$updID."')";
                                $DRW->query($sql_client_log,$DRW_main);
				#############Sync User_CSV2_log#############
				$sql_syn_user_log = "insert INTO cscan_users_sync (userID) VALUES ('".$updID."')";
				$DRW->query($sql_syn_user_log,$DRW_main);
			}
			else {
				$update_sql = "UPDATE cscan_users SET firstName='".$DRW->real_escape_string($firstName)."', password='".$password."',lastName='".$DRW->real_escape_string($lastName)."', emailAddress='".$DRW->real_escape_string($emailAddress)."', companyName='".$DRW->real_escape_string($companyName)."', clientName='".$DRW->real_escape_string($clientName)."', streetAddress='".$DRW->real_escape_string($streetAddress)."', city='".$DRW->real_escape_string($city)."', state='".$DRW->real_escape_string($state)."', country='".$DRW->real_escape_string($country)."', zipCode='".$DRW->real_escape_string($zipCode)."', phone='".$DRW->real_escape_string($phone)."', fax='$fax',number_machines='$number_machines',bypass='$bypass',plevel=$plevel
					WHERE userID='$updID'";  
				$DRW->query($update_sql,$DRW_main);
                                //ADD CLIENT LOG
				$sql_client_log = "insert INTO cscan_client_profile_log (adminID,userID) VALUES ('".$GLOBALS['AUTH_DATA']['userID']."','".$updID."')";
                                $DRW->query($sql_client_log,$DRW_main);
				#############Sync User_CSV2_log#############
				$sql_syn_user_log = "insert INTO cscan_users_sync (userID) VALUES ('".$updID."')";
				$DRW->query($sql_syn_user_log,$DRW_main);
                                    
				if($old_emailAddress!=$emailAddress){
					$update_sql = "UPDATE cscan_search SET sendTo='".$DRW->real_escape_string($emailAddress)."' WHERE sendTo='".$DRW->real_escape_string($old_emailAddress)."' AND userID='$updID'";  
					$DRW->query($update_sql,$DRW_main);
				}
			}
			
			$userArray = array($updID);
			if(isset($_REQUEST['updateInterest']) && $_REQUEST['updateInterest']!=''){
				$sql2 = "SELECT userID FROM cscan_users WHERE loginType<>'A' AND companyName='".$DRW->real_escape_string($_REQUEST['updateInterest'])."'";
				$rs2 = $DRW->query($sql2,$DRW_read);
				while($row2 = $DRW->fetch_row($rs2)) {
					$userArray[] = $row2[0];
				}
			}
			foreach($userArray as $u){
				$sql = "DELETE FROM cscan_mc_users_allow WHERE userID=$u";
				$DRW->query($sql,$DRW_main);
				foreach($mcAllow as $m){
					$sql = "INSERT INTO cscan_mc_users_allow (mChannelID,userID) VALUES ($m,$u)";
					$DRW->query($sql,$DRW_main);
				}
                                ################# for add country permission ################
                                if(!empty($country_allow)){
                                    $sql = "DELETE FROM cscan_country_users_allow WHERE userID=$u";
                                    $DRW->query($sql,$DRW_main);
                                    $sql = "insert INTO cscan_country_users_allow (country_id,userID) VALUES ('".$country_allow."',$u)";
                                    $DRW->query($sql,$DRW_main);
                                }                                
                                ################# end for add country permission ################
				$sql = "DELETE FROM cscan_mp_users_allow WHERE userID=$u";
				$DRW->query($sql,$DRW_main);
				foreach($mpAllow as $m){
					$sql = "INSERT INTO cscan_mp_users_allow (mPanelID,userID) VALUES ($m,$u)";
					$DRW->query($sql,$DRW_main);
				}
                                ################# Start for Additional Fields ################
                                if(!empty($additionalAllow)){
                                    $sql = "DELETE FROM cscan_users_additional_fields_allow WHERE userID=$u";
                                    $DRW->query($sql,$DRW_main);
                                    foreach($additionalAllow as $addfiled){
                                    $sqladditional = "insert INTO cscan_users_additional_fields_allow (field_name,userID) VALUES ('".$addfiled."',$u)";
                                    $DRW->query($sqladditional,$DRW_main);
                                    }
                                }else{
                                    $sql = "DELETE FROM cscan_users_additional_fields_allow WHERE userID=$u";
                                    $DRW->query($sql,$DRW_main);
                                }                                
                               ################# End for Additional Fields ################
                                
                                ################# Start for Page Permission ################
                                if(!empty($pageAllow)){
                                    $sql = "DELETE FROM cscan_users_page_permission WHERE userID=$u";
                                    $DRW->query($sql,$DRW_main);
                                    foreach($pageAllow as $addpage){
                                        $sqlpage = "insert INTO cscan_users_page_permission (field_name,userID) VALUES ('".$addpage."',$u)";
                                        $DRW->query($sqlpage,$DRW_main);
                                    }
                                }else{
                                    $sql = "DELETE FROM cscan_users_page_permission WHERE userID=$u";
                                    $DRW->query($sql,$DRW_main);
                                }                                
                               ################# End for Additional Fields ################
                                
                                ################# Start for Anotation Tool Link Permission ################
                                if(!empty($anotationToolAllow)){
                                    $sql = "DELETE FROM cscan_user_anotation_tool_allow WHERE userID=$u";
                                    $DRW->query($sql,$DRW_main);
                                    foreach($anotationToolAllow as $addanotationtool){
                                        $sqlpage = "insert INTO cscan_user_anotation_tool_allow (name,userID) VALUES ('".$addanotationtool."',$u)";
                                        $DRW->query($sqlpage,$DRW_main);
                                    }
                                }else{
                                    $sql = "DELETE FROM cscan_user_anotation_tool_allow WHERE userID=$u";
                                    $DRW->query($sql,$DRW_main);
                                }                                
                                ################# End for Anotation Tool Link Permission ################


                                ################# Start for AI ANALYSIS Link Permission ################
                                if(!empty($ai_analysis_allow)){
                                    $sql = "DELETE FROM cscan_user_ai_analysis_allowed WHERE userID=$u";
                                    $DRW->query($sql,$DRW_main);
                                    foreach($ai_analysis_allow as $addai_analysis){
                                        $sqlpage = "insert INTO cscan_user_ai_analysis_allowed (name,userID) VALUES ('".$addai_analysis."',$u)";
                                        $DRW->query($sqlpage,$DRW_main);
                                    }
                                }else{
                                    $sql = "DELETE FROM cscan_user_ai_analysis_allowed WHERE userID=$u";
                                    $DRW->query($sql,$DRW_main);
                                }                                
                                ################# End for AI ANALYSIS Link Permission ################

                                
				$sql = "DELETE FROM cscan_sector_users_allow WHERE userID=$u";
				$DRW->query($sql,$DRW_main);
				foreach($sectorAllow as $s){
					$sql = "INSERT INTO cscan_sector_users_allow (sectorID,userID) VALUES ($s,$u)";
					$DRW->query($sql,$DRW_main);
				}
				
				$sql = "DELETE FROM cscan_search_exclude WHERE userID=$u";
				$DRW->query($sql,$DRW_main);
				foreach($searchExclude as $s){
					$sql = "INSERT INTO cscan_search_exclude (search_field,userID) VALUES ('".$DRW->real_escape_string($s)."',$u)";
					$DRW->query($sql,$DRW_main);
				}
				
				/*$sql = "DELETE FROM cscan_retailstate_user WHERE userID=$u";
				$DRW->query($sql,$DRW_main);
				foreach($retailstate_user as $s){
					$sql = "INSERT INTO cscan_retailstate_user (stateID,userID) VALUES ('".$DRW->real_escape_string($s)."',$u)";
					$DRW->query($sql,$DRW_main);
				}*/
				$sql = "DELETE FROM cscan_edc_user WHERE userID=$u";
				$DRW->query($sql,$DRW_main);
				foreach($edc_user as $s){
					$sql = "INSERT INTO cscan_edc_user (edc_id,userID) VALUES ('".$DRW->real_escape_string($s)."',$u)";
					$DRW->query($sql,$DRW_main);
				}
			}
			if(isset($_REQUEST['updateSec']) && $_REQUEST['updateSec']!=''){
				$sql2 = "SELECT userID FROM cscan_users WHERE loginType<>'A' AND companyName='".$DRW->real_escape_string($_REQUEST['updateSec'])."'";
				$rs2 = $DRW->query($sql2,$DRW_read);
				while($row2 = $DRW->fetch_row($rs2)) {
					if($number_machines<$old_number_machines){
						$del = "DELETE FROM cscan_user_code WHERE userID='$row2[0]'";
						$DRW->query($del,$DRW_main);
					}
					$update_sql = "UPDATE cscan_users SET number_machines='$number_machines',bypass='$bypass',plevel=$plevel WHERE userID='$row2[0]'";  
					$DRW->query($update_sql,$DRW_main);
				}
			}
			
			if(!empty($_POST['email_template'])){
				require_once 'manageUserTracker_report.php';
				$mailer = new TemplateMailer();
				$mailer->setTemplate($_POST['email_template']);
				$mailer->addToAddress(array("email" => $emailAddress, "name" => "$firstName $lastName"));
				$confirmation = '';
				if($_POST['email_template']=='usage-report'){
					$StartDate = '';
					if(!empty($_REQUEST['StartDate'])){
						$StartDate = $_REQUEST['StartDate'];
					}
					$EndDate = '';
					if(!empty($_REQUEST['EndDate'])){
						$EndDate = $_REQUEST['EndDate'];
					}
					//$report = manageUserTracker_report($StartDate, $EndDate, $companyName, 1);
					$report2 = manageUserTracker_report($StartDate, $EndDate, $companyName, 2);
					$placeholder_info = array(
						array("name" => "COMPANY", "content" => $companyName),
						array("name" => "USER_FNAME", "content" => $firstName),
						array("name" => "USER_EMAIL", "content" => $emailAddress),
                        array("name" => 'FROM_DATE', "content" => $StartDate),
                        array("name" => 'TO_DATE', "content" => $StartDate),

						//array("name" => "REPORT", "content" => $report),
					);
					$mailer->addAttachment('text/csv', 'usage_report.csv', $report2);
					$confirmation = '-confirmation';
				}
				else{
                                        if(empty($password)){
                                            $sql_pwd = "SELECT password FROM cscan_users WHERE userID='".$updID."'";
                                            $r_pwd = $DRW->query($sql_pwd,$DRW_read);
                                            while($row_pwd = $DRW->fetch_row($r_pwd)) {
                                                $password = $row_pwd[0];
                                            }                                           
                                            
                                        }
                                        
					$greeting = (date('H') < 12 ? "Morning" : "Afternoon");
					$greeting = (date('H') > 17 ? "Evening" : $greeting);
					$greeting = "Good $greeting"; 
					$placeholder_info = array(
						array("name" => "USER_FNAME", "content" => $firstName),
						array("name" => "USER_EMAIL", "content" => $emailAddress),
						array("name" => "USER_PASS", "content" => $password), 
						array("name" => "GREETING", "content" => $greeting)
					);
					$confirmation = '-confirmation';
				}
                                //echo '<pre>';
                                //print_r($mailer); die;
				$mailer->send($placeholder_info);
				if(!empty($confirmation) && !empty($AUTH_DATA['user_email'])){
					$mailer = new TemplateMailer();
					$mailer->setTemplate($_POST['email_template'].$confirmation);
					$mailer->addToAddress(array("email" => $AUTH_DATA['user_email'], "name" => $AUTH_DATA['userName']));
					$mailer->send($placeholder_info);
				}
			}

			if( isset($_POST['saveAndAdd']) ){
				ob_end_clean();
				header("Location: addmember.php?a=1");
				exit;
			}
			else{
				ob_end_clean();
				header("Location: managemember.php");
				exit;
			}
		}
		else {
			$msg = "Login email address already exists";
		} 
	}
	else {
		$msg = "Login email address already exists";
	}
}
?>
<script type="text/javascript" src="js_calendar/calendar.js"></script>
<script type="text/javascript" src="jquery.min.js"></script>
<table width="100%" border="0" cellspacing="0" cellpadding="5" class="text">
  <tr><td class="adminhead" align="center"><?php echo $page_heading; ?></td></tr>
  <tr><td align="right"><strong><span class="error">* required field</span></strong></td></tr>
   <?php
   //************************** Start Click here login this user*******************/
  // if(!defined('ENV')){ 
   //     define('ENV',getenv('SERVER_NAME'));
  // }
  //if(ENV == 'localhost' || ENV == 'demo.competiscan.com'){

   if($updID!=''){ 
       ?>
    <tr><td align="right">
            <form id="myFormLogin" action="../login.php" method="post" target="_blank">
        <input type="hidden" name="userID" value="<?php echo htmlspecialchars($updID,ENT_QUOTES);?>" />
         <input type="hidden" name="byadmin" value="1" />
       
        <a href="javascript:void(0)" onclick="document.getElementById('myFormLogin').submit();" > <?php if($active=='y'){ ?>Click here to log in as this user <?php } ?></a>
    </form
      </td>
    </tr>
   <?php } 
    //}
    //************************** Start Click here login this user*******************//
   ?>
    
  	<tr id="send-success-message" style="display: none;">
        <td colspan="2" style="text-align: center;color:red;font-size:15px;">Reset password mail has been sent successfully.</td>
    </tr>

    <tr id="send-unknown-email-message" style="display: none;">
        <td colspan="2" style="text-align: center;color:red;font-size:15px;">Invalid email address.</td>
    </tr>

    <tr id="email-not-send-message" style="display: none;">
        <td colspan="2" style="text-align: center;color:red;font-size:15px;">Email has not been sent. Please try again!</td>
    </tr>
    <tr>
      <td align="center">
		<form method="post" name="userForm" onsubmit="return validate();" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <table border="0" cellpadding="5" cellspacing="0">
        <tr><td class="error" align="center" colspan="2">
		<?php
		if(isset($_GET['a'])) {
			echo 'New user has been added successfully';
		}
		else {
			echo $msg;
		}
		?>
		</td></tr>
		    <!-- Email Address -->
          <tr>
            <td class="bodytext" align="right">Email Address:<span class="error">*</span></td>
            <td><input type="text" id ="get_user_email" name="emailAddress" size="40" maxlength="255" class="input_box" value="<?php echo htmlspecialchars($emailAddress,ENT_QUOTES);?>" /><input type="hidden" name="old_emailAddress" value="<?php echo htmlspecialchars($emailAddress,ENT_QUOTES);?>" /></td>
          </tr>
          <!-- Password -->
         
		  <?php if($updID!=''){ ?>
		  <tr>
			<td class="bodytext" align="right">Password:<span class="error"></span></td>
				<td>
					<!-- Reset link -->
					<a href="javascript:void(0)" id="reset-password_v2" style="font-size:13px; color:red;">Click Here to reset password</a>
					<!--<span style="font-size:15px;"> to reset password</span>-->
					<span id="status_message" style="font-size:13px;color:#14734F;"></span>
					<!-- Hidden password input initially -->
					<div id="password-input-wrapper" style="display:none; margin-top:10px;">
					
					<input type="text" id="input_password" name="password" size="40" maxlength="40" readonly class="input_box" value="<?php echo htmlspecialchars($password,ENT_QUOTES);?>" />
					<a href="#" id="generatePassword" style="margin-left:10px; font-size:13px; color:#14734F; cursor:pointer;">Reset Password</a><br/><br/>
					</div>
				</td>
		</tr>
		<?php } ?>
          <tr>
            <td class="bodytext" align="right">Group:</td>
            <td>
            <select name="plevel" class="combo_box">
				<option value="0">None</option>
<?php
			$sql = "SELECT ugid,group_name FROM cscan_user_group ORDER BY group_name";
			$rs = $DRW->query($sql,$DRW_read);
			while($data = $DRW->fetch_row($rs)){
				print "<option value=\"$data[0]\"";
				if($data[0]==$plevel) print ' selected="selected"';
				print ">$data[1]</option>";
			}
?>
		</select></td>
          </tr>
		  <tr>
			<td class="bodytext" align="right">Number of Machines:</td>
			<td><input type="text" name="number_machines" size="4" maxlength="4" class="input_box" value="<?php echo htmlspecialchars($number_machines,ENT_QUOTES);?>" /><input type="hidden" name="old_number_machines" value="<?php echo htmlspecialchars($number_machines,ENT_QUOTES);?>" /><?php 
			$count_save_sql = "SELECT COUNT(*) FROM cscan_user_code where userID='$updID'";
			$rs = $DRW->query($count_save_sql,$DRW_read);
			$data = $DRW->fetch_row($rs);
			$codecount = (int) $data[0];
			print " <span class=\"bodytext\">($codecount in use)";
			if($codecount>0) print " &nbsp; <input type=\"checkbox\" name=\"resetcodes\" value=\"1\" />Reset Machines";
			print "</span>";
			?></td>
		  </tr>
		  <tr>
			<td class="bodytext" align="right">Bypass Security:</td>
			<td><input type="checkbox" name="bypass" value="1"<?php if($bypass==1) print ' checked="checked"'; ?> /></td>
		  </tr>
		  
          <!-- First Name -->
          <tr>
            <td class="bodytext" align="right">First Name:</td>
            <td><input type="text" name="firstName" size="40" maxlength="255" class="input_box" value="<?php echo htmlspecialchars($firstName,ENT_QUOTES);?>" /></td>
          </tr>
          <!-- Last Name -->
          <tr>
            <td class="bodytext" align="right">Last Name:</td>
            <td><input type="text" name="lastName" size="40" maxlength="255" class="input_box" value="<?php echo htmlspecialchars($lastName,ENT_QUOTES);?>" /></td>
          </tr>
          <!-- Company Name -->
          <tr>
            <td class="bodytext" align="right">Company Name:<span class="error">*</span></td>
            <td><input type="text" name="companyName" size="40" maxlength="255" class="input_box" value="<?php echo htmlspecialchars($companyName,ENT_QUOTES);?>" /></td>
          </tr>
          <!-- Client Name (Agencies Only) -->
          <tr>
            <td class="bodytext" align="right">Client Name (Agencies Only):</td>
            <td><input type="text" name="clientName" size="40" maxlength="255" class="input_box" value="<?php echo htmlspecialchars($clientName,ENT_QUOTES);?>" /></td>
          </tr>
          <!-- Street Address -->
          <tr>
            <td class="bodytext" align="right" valign="top">Street Address:</td>
            <td><textarea name="streetAddress" rows="5" cols="39" class="input_box"><?php echo htmlspecialchars($streetAddress,ENT_QUOTES);?></textarea></td>
          </tr>
          <!-- City -->
          <tr>
            <td class="bodytext" align="right">City:</td>
            <td><input type="text" name="city" size="40"  maxlength="255" class="input_box" value="<?php echo htmlspecialchars($city,ENT_QUOTES);?>" /></td>
          </tr>
          <!-- State/Province -->
          <tr>
            <td class="bodytext" align="right">State/Province:</td>
            <td>
            <select name="state" class="combo_box">
				<option value=""> --Select One --</option>
<?php
				getStates($state,true); 
?>
		</select></td>
          </tr>
          <!-- Division -->
          <tr>
            <td class="bodytext" align="right">Division:</td>
            <td><input type="text" name="country" size="40" maxlength="255" class="input_box" value="<?php echo htmlspecialchars($country,ENT_QUOTES);?>" /></td>
          </tr>
          <!-- Zip Code -->
          <tr>
            <td class="bodytext" align="right">Zip/Postal Code:</td>
            <td><input type="text" name="postalCode" size="40" maxlength="15" class="input_box" value="<?php echo htmlspecialchars($zipCode,ENT_QUOTES);?>" /></td>
          </tr>
          <!-- Phone -->
          <tr>
            <td class="bodytext" align="right">Phone:</td>
            <td><input type="text" name="phone" size="40" maxlength="255" class="input_box" value="<?php echo htmlspecialchars($phone,ENT_QUOTES);?>" /></td>
          </tr>
          <!-- Fax -->
          <tr>
            <td class="bodytext" align="right">Fax:</td>
            <td><input type="text" name="fax" size="40" maxlength="255" class="input_box" value="<?php echo htmlspecialchars($fax,ENT_QUOTES);?>" /></td>
          </tr>
          <!-- for add country permission -->
           <tr>
			<td>&nbsp;</td>
			<td class="bodytext">
				<fieldset style="border-color:#000000;border-width:1px;">
				<legend><strong>Country</strong></legend>
				<div>
				<?php 
                                if($updID!=''){
                                    $sql = "SELECT DISTINCT country_id,userID FROM cscan_country_users_allow where userID='".$updID."'";
                                    $rs = $DRW->query( $sql,$DRW_read );
                                    $countryAllow    =   array();
                                   
                                    while($row = $DRW->fetch_array($rs)) {
                                         $countryAllow[] = $row[0];
                                         
                                     } 
                                }
                                if(empty($countryAllow)){
                                    $countryAllow[]='BOTH';
                                }
                               // print_r($countryAllow);
                                $countrydataArray = array('US'=>'US Only','CA'=>'Canada Only','BOTH'=>'US and Canada Both');
                                
                                foreach( $countrydataArray as $cid=>$cname ) {
                                    echo "<div><label><input type=\"radio\" name=\"country_allow\" value=\"$cid\"";
					if(in_array($cid,$countryAllow)){
						echo ' checked="checked"';	
					}
					echo " />".htmlspecialchars($cname).'</label></div>';
                                    
                                    
                                }
                                
                                
				?>
				</div>
				</fieldset>
			</td>
		</tr>
                
           <!-- end for add country permission -->
          
		  <tr>
			<td>&nbsp;</td>
			<td class="bodytext">
				<fieldset style="border-color:#000000;border-width:1px;">
				<legend><strong>Media Channels</strong></legend>
				<div>
				<?php 
				if($updID!=''){
					$sql = "SELECT mChannelID FROM cscan_mc_users_allow WHERE userID=$updID";
					$rs = $DRW->query($sql,$DRW_read);
					while($row = $DRW->fetch_array($rs)) {
						$mcAllow[] = $row[0];
					}
				}
				$media_channel = getMediaChannel();
				foreach( $media_channel as $id=>$name ) {
					echo "<div><label><input type=\"checkbox\" name=\"mc_allow[]\" value=\"$id\"";
					if(in_array($id,$mcAllow)){
						echo ' checked="checked"';	
					}
					echo " />".htmlspecialchars($name).'</label></div>';
				}
				?>
				</div>
				</fieldset>
			</td>
		</tr>
		  <tr>
			<td>&nbsp;</td>
			<td class="bodytext">
				<fieldset style="border-color:#000000;border-width:1px;">
				<legend><strong>Audience</strong></legend>
				<div>
				<?php 
				if($updID!=''){
					$sql = "SELECT mPanelID FROM cscan_mp_users_allow WHERE userID=$updID";
					$rs = $DRW->query($sql,$DRW_read);
					while($row = $DRW->fetch_array($rs)) {
						$mpAllow[] = $row[0];
					}
				}
				$mailing_panel = getMailingPanel();
				foreach( $mailing_panel as $id=>$name ) {
					echo "<div><label><input type=\"checkbox\" name=\"mp_allow[]\" value=\"$id\"";
					if(in_array($id,$mpAllow)){
						echo ' checked="checked"';	
					}
					echo " />".htmlspecialchars($name).'</label></div>';
				}
				?>
				</div>
				</fieldset>
			</td>
		</tr>
		  <tr>
			<td>&nbsp;</td>
			<td class="bodytext">
				<fieldset style="border-color:#000000;border-width:1px;">
				<legend><strong>Sector/Category/Sub Category</strong></legend>
				<div style="height:300px;overflow-y:scroll;">
				<?php 
				if($updID!=''){
					$sql = "SELECT sectorID FROM cscan_sector_users_allow WHERE userID=$updID";
					$rs = $DRW->query($sql,$DRW_read);
					while($row = $DRW->fetch_array($rs)) {
						$sectorAllow[] = $row[0];
					}
				}
				getSects();
				?>
				</div>
				</fieldset>
			</td>
		</tr>
		  <tr>
			<td>&nbsp;</td>
			<td class="bodytext">
				<fieldset style="border-color:#000000;border-width:1px;">
				<legend><strong><em>Exclude</em> from Power Search</strong></legend>
				<div>
				<?php 
				if($updID!=''){
					$sql = "SELECT search_field FROM cscan_search_exclude WHERE userID=$updID";
					$rs = $DRW->query($sql,$DRW_read);
					while($row = $DRW->fetch_array($rs)) {
						$searchExclude[] = $row[0];
					}
				}
				else{
					$searchExclude = array();
					foreach($search_fields as $f=>$v){
						$searchExclude[] = $f;
					}
				}
				foreach($search_fields as $field=>$name){
					echo "<div>";
					echo "<label><input type=\"checkbox\" name=\"searchExclude[]\" value=\"$field\"";
					if(in_array($field,$searchExclude)){
						echo ' checked="checked"';	
					}
					echo " />".htmlspecialchars($name).'</label>';
					echo '</div>';
				}
				?>
				</div>
				</fieldset>
			</td>
		</tr>
		  <tr>
			<td>&nbsp;</td>
			<td class="bodytext">
				<fieldset style="border-color:#000000;border-width:1px;">
				<legend><strong>Dashboard Permissions</strong></legend>
				<div>
				<?php 
				if($updID!=''){
					/*$sql = "SELECT stateID FROM cscan_retailstate_user WHERE userID=$updID";
					$rs = $DRW->query($sql,$DRW_read);
					while($row = $DRW->fetch_array($rs)) {
						$retailstate_user[] = $row[0];
					}*/
					$sql = "SELECT edc_id FROM cscan_edc_user WHERE userID=$updID";
					$rs = $DRW->query($sql,$DRW_read);
					while($row = $DRW->fetch_array($rs)) {
						$edc_user[] = $row[0];
					}
				}
				
				/*echo '<div>State/Province:<br /><select name="retailstate_user[]" multiple="multiple" size="5" class="combo_box">';
				$selstates = implode(',',$retailstate_user);
				getStates($selstates,false);
				echo '</select></div>
				<div>&nbsp;</div>*/
				echo '<div>EDC / LDC / TDSP:<br /><select name="edc_user[]" class="combo_box" multiple="multiple" size="5">';
				$query_ac ="select edc_id,edc_name from cscan_edc ORDER BY edc_name";
				$result_ac = $DRW->query($query_ac,$DRW_read);
				while($row_ac = $DRW->fetch_row($result_ac)){
					$selvalue = $row_ac[0];
					$seltext = $row_ac[1];
					echo "<option value=\"$selvalue\"";
					if(in_array($selvalue,$edc_user)) {
						echo " selected=\"selected\"";
					}
					echo ">".htmlspecialchars($seltext)."</option>";
				}   
				echo '</select></div>';
				?>
				</div>
				</fieldset>
			</td>
		</tr>
                
                <!-- Start for Additional Fields -->
                <?php 
                if(!defined('ENV')){
                    define('ENV',getenv('SERVER_NAME'));
                  } 
                //if(ENV == 'localhost' || ENV == 'demo.competiscan.com' || $AUTH_DATA['userID']=='447' || $AUTH_DATA['userID']=='357'){ ?>
                <tr>
                    <td>&nbsp;</td>
                    <td class="bodytext">
                    <fieldset style="border-color:#000000;border-width:1px;">
                    <legend><strong>Additional Fields For Power Search</strong></legend>
                    <div>
                    <?php
                    $additionalFieldsAllow = array();
                    if($updID!=''){
                        $sql = "SELECT DISTINCT field_name ,userID FROM cscan_users_additional_fields_allow where userID='".$updID."'";
                        $rs = $DRW->query( $sql,$DRW_read );
                        while($row = $DRW->fetch_array($rs)) {
                        $additionalFieldsAllow[] = $row[0];

                        }
                    }
                    //if(ENV == 'localhost' || ENV == 'demo.competiscan.com'){
                        $additionaldataArray = array('fico'=>'FICO','fico_range'=>'FICO Range','vantage_score'=>'VantageScore','vantage_range'=>'Vantage Range','credit_vision'=>'CreditVision','credit_vision_range'=>'CreditVision Range','real_time_mail_volume'=>'Real Time Mail Volume');
                    //}else{
                        //$additionaldataArray = array('fico'=>'FICO','vantage_score'=>'VantageScore','credit_vision'=>'CreditVision');
                    //}
                    
                    foreach($additionaldataArray as $aid=>$adname ) {
                    echo "<div><label><input type=\"checkbox\" name=\"additional_allow[]\" value=\"$aid\"";
                    if(in_array($aid,$additionalFieldsAllow)){
                    echo ' checked="checked"';
                    }
                    echo " />".htmlspecialchars($adname).'</label></div>';
                    }
                    ?>
                    </div>
                    </fieldset>
                    </td>
                </tr>
             <?php // } ?>
            <!-- End Additional Fields -->
            
            <!-- Start for Page Permission -->
                <?php 
                if(!defined('ENV')){
                    define('ENV',getenv('SERVER_NAME'));
                  } 
                //if(ENV == 'localhost' || ENV == 'demo.competiscan.com'){ ?>
                <tr>
                    <td>&nbsp;</td>
                    <td class="bodytext">
                    <fieldset style="border-color:#000000;border-width:1px;">
                    <legend><strong>Page Permission</strong></legend>
                    <div>
                    <?php
                    $pagePermissionAllow = array();
                    if($updID!=''){
                        $sql = "SELECT DISTINCT field_name FROM cscan_users_page_permission where userID='".$updID."'";
                        $rs = $DRW->query( $sql,$DRW_read );
                        while($row = $DRW->fetch_array($rs)) {
                            $pagePermissionAllow[] = $row[0];
                        }
                    }
                    
                    //$pagedataArray = array('power_search'=>'Power Search','trend_reports'=>'Trend Reports','retrieval_services'=>'Retrieval Services');
                    //ADD DIGITAL DASHBOARD PERMISSION
                    //if(ENV == 'localhost' || ENV == 'demo.competiscan.com'){
                     $pagedataArray = array('power_search'=>'Power Search','trend_reports'=>'Trend Reports','retrieval_services'=>'Retrieval Services','digital_dashboard'=>'Digital Dashboard','rpv_dashboard'=>'RPV Dashboard');   
                    //}
                    
                    
                    foreach($pagedataArray as $pgid=>$pgname ) {
                        echo "<div><label><input type=\"checkbox\" name=\"page_allow[]\" value=\"$pgid\"";
                        if(in_array($pgid,$pagePermissionAllow) OR $updID==''){
                        echo ' checked="checked"';
                        }
                        echo " />".htmlspecialchars($pgname).'</label></div>';
                    }
                    ?>
                    </div>
                    </fieldset>
                    </td>
                </tr>
             <?php //} ?>
            <!-- End Page Permission -->
            
            <!-- Start for Anotation Tool Link Permission -->
                <?php 
                if(!defined('ENV')){
                    define('ENV',getenv('SERVER_NAME'));
                  } 
                //if(ENV == 'localhost' || ENV == 'demo.competiscan.com'){ ?>
                <tr>
                    <td>&nbsp;</td>
                    <td class="bodytext">
                    <fieldset style="border-color:#000000;border-width:1px;">
                    <legend><strong>Anotation Tool Link Permission</strong></legend>
                    <div>
                    <?php
                    $AnoToolAllow = array();
                    if($updID!=''){
                        $sql = "SELECT DISTINCT name FROM cscan_user_anotation_tool_allow where userID='".$updID."'";
                        $rs = $DRW->query( $sql,$DRW_read );
                        while($row = $DRW->fetch_array($rs)) {
                            $AnoToolAllow[] = $row[0];
                        }
                    }
                    
                    
                    $anotationdataArray = array('anotation_tool_link'=>'Anotation Tool Link');   
                    foreach($anotationdataArray as $atid=>$atname ) {
                        echo "<div><label><input type=\"checkbox\" name=\"anotation_tool_allow[]\" value=\"$atid\"";
                        if(in_array($atid,$AnoToolAllow) OR $updID==''){
                        echo ' checked="checked"';
                        }
                        echo " />".htmlspecialchars($atname).'</label></div>';
                    }
                    ?>
                    </div>
                    </fieldset>
                    </td>
                </tr>
             <?php //} ?>
            <!-- End Anotation Tool Link Permission -->
			 <!-- Start for AI ANALYSIS Link Permission -->
			 <?php 
                if(!defined('ENV')){
                    define('ENV',getenv('SERVER_NAME'));
                  } 
                //if(ENV == 'localhost' || ENV == 'demo.competiscan.com'){ ?>
                <tr>
                    <td>&nbsp;</td>
                    <td class="bodytext">
                    <fieldset style="border-color:#000000;border-width:1px;">
                    <legend><strong>AI Analysis Link Permission</strong></legend>
                    <div>
                    <?php
                    $AIToolAllow = array();
                    if($updID!=''){
                        $sql = "SELECT DISTINCT name FROM cscan_user_ai_analysis_allowed where userID='".$updID."'";
                        $rs = $DRW->query( $sql,$DRW_read );
                        while($row = $DRW->fetch_array($rs)) {
                            $AIToolAllow[] = $row[0];
                        }
                    }
                    
                    
                    $anotationdataArray = array('ai_analysis_link'=>'AI Analysis Link');   
                    foreach($anotationdataArray as $aitid=>$atname ) {
                        echo "<div><label><input type=\"checkbox\" name=\"ai_analysis_allow[]\" value=\"$aitid\"";
                        if(in_array($aitid,$AIToolAllow) OR $updID==''){
                        echo ' checked="checked"';
                        }
                        echo " />".htmlspecialchars($atname).'</label></div>';
                    }
                    ?>
                    </div>
                    </fieldset>
                    </td>
                </tr>
             <?php //} ?>
            <!-- End AI ANALYSIS Link Permission -->
            
		<?php /*
		  <tr>
			<td>&nbsp;</td>
			<td class="bodytext">
				<fieldset style="border-color:#000000;border-width:1px;">
				<legend><strong>Available Fields</strong></legend>
				<div style="height:300px;overflow-y:scroll;">
				<?php 
				$showArray = array();
				$showArray[] = array("company","Company");
				$showArray[] = array("secondCompany","Second Company");
				$showArray[] = array("sectorID","Sector");
				$showArray[] = array("categoryID","Category");
				$showArray[] = array("subCategoryID","Sub Category");
				$showArray[] = array("subSubCategoryID","Sub Sub Category");
				$showArray[] = array("mTypeID","Mailing Type");
				$showArray[] = array("entryID","Entry ID");
				$showArray[] = array("productHeadline","Headline");
				$showArray[] = array("mChannelID","Media Channel");
				$showArray[] = array("mPanelID","Audience");
				$showArray[] = array("agentCommunicationID","Communication Type");
				$showArray[] = array("affinityAssociation","Affinity/Association");
				$showArray[] = array("AffinityCategoryID","Affinity/Association Category");
				$showArray[] = array('productName','Product');
				$showArray[] = array("age","Age");
				$showArray[] = array("income","Income");
				$showArray[] = array("gender","Gender");
				$showArray[] = array("DMA_ID","Metropolitan Area");
				$showArray[] = array("state","State");
				$showArray[] = array("compaignLanguage","Campaign Language");
				$showArray[] = array("mailpieces","Mail Pieces");
				$showArray[] = array("mailvolume","Estimated Mail Volume");
				$showArray[] = array("ppdate","Mail Piece Months");
				$showArray[] = array("firstSeen","First Seen");
				$showArray[] = array("lastSeen","Last Seen");
				$showArray[] = array("OfferExpiryDate","Offer Expiry Date");
				$showArray[] = array("filesize","File Size");
				$showArray[] = array('fa','Face Amount');
				$showArray[] = array('tl','Term Length');
				$showArray[] = array('delmethid','Delivery Method');
				$showArray[] = array('responseMechID','Response Mechanism');
				$showArray[] = array('FeeProductType','Ancillary Products');
				$showArray[] = array('external_link','Network Name');
				$showArray[] = array('external_updates','Number of Updates/Tweets');
				$showArray[] = array('external_fans','Number of Fans/Followers');
				$showArray[] = array("doclink","PDF Content");
				$showArray[] = array("prescription","Rx");
				$showArray[] = array("is_hphsa","CDHP/HDHP/HSA");
				$showArray[] = array('incentive','Incentive');
				$showArray[] = array('incentive_ongoing','Ongoing Incentive');
				require_once('additionalDetails.php');
				foreach($addlArray as $o){
					while($o->getNext()){
						$tempfieled = $o->getField();
						$temptitle = $o->getTitle();
						if(!empty($tempfieled)){
							$tempfieled .= '_'.$o->id;
						}
						else{
							//$temptitle =  $o->label.' - '.$temptitle;
							$showArray[] = array('-1',$o->label);
						}
						$showArray[] = array($tempfieled,$temptitle);
					}
					$o->doReset();
				}
				$showArray[] = array('','Consumer Report');
				$showArray[] = array('pi2','Panelist Affinities');
				$showArray[] = array('pi3','Panelist Loyalty/Retention, Statement Companies');
				$showArray[] = array('invitationID','Invitation ID');
				$showArray[] = array('trackingID','Last 4 Digits');
				$showArray[] = array('','Demographics');
				$showArray[] = array('ATP','ATP');
				$showArray[] = array('Income360','Income360');
				$showArray[] = array('DSDollar','DSDollar');
				$showArray[] = array('DSI','DSI');
				$showArray[] = array('ET_ETHNICITY','ETHNICITY');
				$showArray[] = array('ET_RELIGION','RELIGION');
				$showArray[] = array('ET_LANGUAGE','LANGUAGE');
				$showArray[] = array('ET_GROUP','GROUP');
				$showArray[] = array('ET_COUNTRY','COUNTRY');
				$showArray[] = array('ET_ASSIMILATION','ASSIMILATION');
				$showArray[] = array('HH_Income_Index','HH Income Index');
				$showArray[] = array('Birth_date_of_person_for_first_person_in_household','Birth date of person for first person in household');
				$showArray[] = array('Income_Producing_Assets_Segment_Code','Income Producing Assets Segment Code      *R*');
				$showArray[] = array('Advantage_Home__Owner_Renter_Code','Advantage Home  Owner / Renter Code');
				$showArray[] = array('Advantage_Home_Owner_Renter_Level','Advantage Home Owner / Renter Level');
				$showArray[] = array('DMA_CODE','DMA CODE');
				$showArray[] = array('','Segmentation');
				$showArray[] = array('ECohort_Code','ECohort_Code');
				$showArray[] = array('ECohort_Desc','ECohort_Desc');
				$showArray[] = array('ECohort_Flag','ECohort_Flag');
				$showArray[] = array('PSY_FLAG','PSY_FLAG');
				$showArray[] = array('PSY_CODE','PSY_CODE');
				$showArray[] = array('PZM_FLAG','PZM_FLAG');
				$showArray[] = array('PZM_CODE','PZM_CODE');
				$showArray[] = array('CNX_FLAG','CNX_FLAG');
				$showArray[] = array('CNX_CODE','CNX_CODE');
				$showArray[] = array('','Credit Proxy variables');
				$showArray[] = array('ValueScore_for_Household','ValueScore for Household');
				$showArray[] = array('inq_win_past_6_mnths_except_promo_and_eval','# inq w/in past 6 mnths except promo and eval');
				$showArray[] = array('Age_of_oldest_account_months','Age of oldest account (months)');
				$showArray[] = array('Age_of_newest_account_months','Age of newest account (months)');
				$showArray[] = array('of_accounts_opened_in_the_last_6_months','# of accounts opened in the last 6 months');
				$showArray[] = array('of_accounts_opened_in_the_last_12_months','# of accounts opened in the last 12 months');
				$showArray[] = array('of_accounts_opened_in_the_last_24_months','# of accounts opened in the last 24 months');
				$showArray[] = array('of_accounts','# of accounts');
				$showArray[] = array('of_active_accounts','# of active accounts');
				$showArray[] = array('Total_credit_limit_for_active_accounts','Total credit limit for active accounts');
				$showArray[] = array('of_accounts_currently_rated_satisfactory','# of accounts currently rated satisfactory');
				$showArray[] = array('of_accounts_currently_bad_debt','# of accounts currently bad debt');
				$showArray[] = array('Average_of_months_opened','Average # of months opened');
				$showArray[] = array('of_active_accts_with_balance_50_limit','# of active accts with balance >= 50% limit');
				$showArray[] = array('of_bank_revolving_accounts','# of bank revolving accounts');
				$showArray[] = array('of_department_store_accounts','# of department store accounts');
				$showArray[] = array('of_active_bank_revolving_accounts','# of active bank revolving accounts');
				$showArray[] = array('active_dept_store_accts_wo_closed_narratives','# active dept store accts w/o closed narratives');
				$showArray[] = array('Total_limit_for_active_bank_revolving_accts','Total limit for active bank revolving accts');
				$showArray[] = array('Total_credit_limit_for_active_dept_store_accounts','Total credit limit for active dept store accounts');
				$showArray[] = array('of_total_credit_union_accounts','# of total credit union accounts');
				$showArray[] = array('Presence_of_Bankruptcy','Presence of Bankruptcy');
				$showArray[] = array('accts_rated_bad_debt_of_derogatory24_mnths','# accts rated bad debt + # of derogatory-24 mnths');
				$showArray[] = array('Age_of_oldest_active_mortgage','Age of oldest active mortgage');
				$showArray[] = array('Balance_for_active_mortgage_accounts','Balance for active mortgage accounts');
				$showArray[] = array('High_credit_for_active_mortgage_accounts','High credit for active mortgage accounts');
				$showArray[] = array('Number_of_active_mortgage_accounts','Number of active mortgage accounts');
				$showArray[] = array('Household_Income_Identifier_Narrow_Band','Household Income Identifier Narrow Band');
				$showArray[] = array('Gender_code','Gender code');
				foreach($showArray as $a){
					list($field,$name) = $a;
					echo "<div>";
					if($field=='-1'){
						echo '<strong>'.htmlspecialchars($name).'</strong>';
					}
					elseif($field==''){
						echo '<strong>'.htmlspecialchars($name).'</strong>';// <input type="hidden" name="chexEnd'.$chexblock.'" value="'.$chex.'" /> <a href="#" class="bluelink" onclick="chexStart('.$chexblock.','.$chex.'); return false;">Select All</a>';
					}
					else{
						echo "<label><input type=\"checkbox\" name=\"displayFields[]\" value=\"$field\"";
						//if(in_array($field,$searchExclude)){
						//	echo ' checked="checked"';	
						//}
						echo " />".htmlspecialchars($name).'</label>';
					}
					echo '</div>';
				}
				?>
				</div>
				</fieldset>
			</td>
		</tr>
		*/ ?>
		<tr>
	        	<td class="bodytext" align="right" valign="top">Send Email:</td>
        		<td class="bodytext" style="line-height: 24px;">
			<label><input type="radio" name="email_template" value="" style="vertical-align: -1px;" checked="checked" />None</label> &nbsp;&nbsp; 
			<label><input type="radio" name="email_template" value="welcome-email" style="vertical-align: -1px;" />Welcome</label> &nbsp;&nbsp;
			<label><input type="radio" name="email_template" value="ip-limit-email" style="vertical-align: -2px;" />IP Limit</label>
			
			
			<div><label><input type="radio" name="email_template" value="usage-report" style="vertical-align: -2px;" />Company Usage Report</label> &nbsp;&nbsp;
			<input type="text" name="StartDate" size="20" class="input_box" value="<?php 
			$lastt = strtotime('-1 month');
			$lastYm = date('Y-m',$lastt);
			$lastd = date('t',$lastt);
			echo $lastYm.'-01'; ?>" />
			<a href="#" onclick="displayCalendar(document.userForm.StartDate,'yyyy-mm-dd',this); return false;"><img name="popcal1" src="js_calendar/images/getcal.gif" border="0" alt="" style="vertical-align:bottom;"></a>
			<input type="text" name="EndDate" size="20" class="input_box" value="<?php echo $lastYm.'-'.$lastd; ?>" />
			<a href="#" onclick="displayCalendar(document.userForm.EndDate,'yyyy-mm-dd',this); return false;"><img name="popcal2" src="js_calendar/images/getcal.gif" border="0" alt="" style="vertical-align:bottom;" /></a></div>
		</td>
          	</tr>
          <!-- Button -->
		<?php
		if( $updID == '' ) {
		?>
		<tr><td colspan="2">&nbsp;</td></tr>
          <tr>
			<td>&nbsp;</td><td><input class="button" type="submit" name="saveb" value="Save" onClick="return validate();" /> &nbsp; <input class="button" type="submit" name="saveAndAdd" value="Save &amp; Add More" onclick="return validate();" /></td>
          </tr>
		<?php
		}
		else {
		?>
          <tr>
            <td>&nbsp;</td>
            <td class="bodytext"><label><input type="checkbox" name="updateInterest" value="<?php echo htmlspecialchars($companyName,ENT_QUOTES);?>" />Update for all <strong><?php echo htmlspecialchars($companyName);?></strong> users<br /> &nbsp; &nbsp; &nbsp;[Media Channels, Audience,<br />&nbsp; &nbsp; &nbsp;Sector/Category/Sub Category, Power Search, Dashboard Permissions]</label></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td class="bodytext"><label><input type="checkbox" name="updateSec" value="<?php echo htmlspecialchars($companyName,ENT_QUOTES);?>" />Update for all <strong><?php echo htmlspecialchars($companyName);?></strong> users<br /> &nbsp; &nbsp; &nbsp;[Group, Number of Machines, Bypass Security]</label></td>
          </tr>
			<tr><td colspan="2">&nbsp;</td></tr>
          <tr>
			<td>&nbsp;</td><td><input class="button" type="submit" name="saveb" value="Update" onclick="return validate();" /> &nbsp; <input class="button" type="button" value="Cancel" onclick="location.href='managemember.php';return false;" /><input type="hidden" name="updID" value="<?php echo $updID;?>" /></td>
          </tr>
          
         
		<?php
		}
		?>
        </table>
        <input type="hidden" name="save" value="1" />
       </form>
        
      </td>
    </tr>
</table>
<style>
.loader{
    background: rgba(255,255,255,0.9) url(../images/loader.gif) no-repeat center 50% ;  
    opacity: 0.9;
    z-index: 1000001;
    width:100%; 
    height:100%; 
    position: fixed; 
    top:0; 
    left:0;
}
</style>
<!-- JavaScript to toggle input field -->
<script>
document.addEventListener('DOMContentLoaded', function () {
  const resetBtn = document.getElementById('reset-password_v2');
  const inputWrapper = document.getElementById('password-input-wrapper');
  
  if (resetBtn && inputWrapper) {
    resetBtn.addEventListener('click', function () {
      resetBtn.style.display = 'none'; // Hide the "Click Here" link
      inputWrapper.style.display = 'block'; // Show the password field
    });
  }
});

$("#generatePassword").on("click", function (e) {
    e.preventDefault();
    var get_user_email = $("#get_user_email").val();
    console.log("User email:", get_user_email);
    $.post("generate_password.php", { email: get_user_email }, function (data) {
        console.log("Server response:", data);
		var parts = data.split("##");
		var password = parts[0];
		var message = parts[1];
		console.log("Password:", password);
		console.log("Message:", message);
		$("#input_password").val(password);
		$("#status_message").text(message);  
    //$("#input_password").val(data);
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.error("Error generating password:", textStatus, errorThrown);
    });
	setTimeout(function() {
            $('#status_message').hide();
        }, 6000);
});

</script>
<script>
/*$("input#input_password").keypress(
  function(event){
    if ((event.which == '36') || (event.which == '34') || (event.which == '39') || (event.which == '59')) {
        if(event.which == '36'){
            alert('Dollar($) key is not allowed!');
        }else if(event.which == '34'){
            alert('Double quotation(") key is not allowed!');
        }else if(event.which == '39'){
            alert("Apostrophe(') key is not allowed!");
        }else if(event.which == '59'){
            alert('Semicolon(;) key is not allowed!');
        }
        event.preventDefault();
    }
    
});

window.onload = () => {
 const myInput = document.getElementById('input_password');
 myInput.onpaste = e => e.preventDefault();
}*/
</script>
<script type="text/javascript">

function validate(){
	var password = document.userForm.password.value = trimspace(document.userForm.password.value);
	var emailAddress = document.userForm.emailAddress.value = trimspace(document.userForm.emailAddress.value);
	var companyName = document.userForm.companyName.value = trimspace(document.userForm.companyName.value);
	
	if( emailAddress == '' ){
		alert("Please enter email address");
		document.userForm.emailAddress.focus();
		return false;
	}
	if( !checkmail(emailAddress) ){
		alert("Please enter valid email address");
		document.userForm.emailAddress.focus();
		return false;
	}
	// if( password == '' ){
	// 	alert('Please enter the password');
	// 	document.userForm.password.focus();
	// 	return false;
	// }
	// if(password.length < 6){
	// 	alert("Password must be of six character");
	// 	document.userForm.password.focus();
	// 	return false;
	// }
	if( companyName == '' ){
		alert("Please enter Company Name");
		document.userForm.companyName.focus();
		return false;
	}
	var chexm = false;
	for(var j=0;j<document.userForm['mc_allow[]'].length;j++){
		if(document.userForm['mc_allow[]'][j].checked){
			chexm = true;
			break;
		}
	}
	if(!chexm){
		alert("Please enter Media Channels");
		return false;
	}
	var chexm = false;
	for(var j=0;j<document.userForm['mp_allow[]'].length;j++){
		if(document.userForm['mp_allow[]'][j].checked){
			chexm = true;
			break;
		}
	}
	if(!chexm){
		alert("Please enter Audience");
		return false;
	}
	var chex = false;
	for(var k in pidArray){
		if(document.userForm['sectorid_'+k].checked){
			chex = true;
			break;
		}
	}
	if(!chex){
		alert("Please enter Area of Interest");
		return false;
	}
	
	return true;
}
var pidArray = new Array();
var cidArray = new Array();
<?php echo $javascript; ?>
function checkParent(sid,pid){
	if(pid!=0){
		var obj1 = document.userForm['sectorid_'+sid];
		var obj2 = document.userForm['sectorid_'+pid];
		
		if(obj1.checked && !obj2.checked){
			obj2.checked = true;
		}
		checkParent(pid,pidArray[pid]);
	}
}
function checkChildren(sid,chex){
	//document.userForm['sectorid_'+sid].checked = true;
	if(cidArray[sid]){
		for(var i in cidArray[sid]){
			var obj = document.userForm['sectorid_'+cidArray[sid][i]];
			if(obj){
				obj.checked = chex;
				checkChildren(cidArray[sid][i],chex);
			}
		}
	}
}
function checkP_C(sid,pid){
	checkParent(sid,pid);
	var obj = document.userForm['sectorid_'+sid];
	if(cidArray[sid]){
		if(obj.checked){
			if(confirm('Select All?')){
				checkChildren(sid,true);
			}
		}
		else if(confirm('Remove All?')){
			checkChildren(sid,false);
		}
	}
}

$('#reset-password').on('click', function(){
	var emailAddress = $('#reset-us').val();
	if (window.location.host == "localhost") {
        var origin = window.location.origin+'/competiscan.com/send-reset-password.php';
    } else {
        var origin = window.location.origin+'/send-reset-password.php';
    }
    $(".loader").show();
	$.ajax({
        type: 'POST',
        url: origin,
        data: {email: emailAddress},
        success: function (data) {
        	if(data == 0){
        		$(".loader").hide();
            	$("#send-unknown-email-message").show();
            	setTimeout(function() {
		            $('#send-unknown-email-message').hide();
		        }, 6000);
            }else if(data == 1){
            	$(".loader").hide();
            	$("#send-success-message").show();
            	setTimeout(function() {
		            $('#send-success-message').hide();
		        }, 6000);
            }else if(data == 2){
            	$(".loader").hide();
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
});
//-->
</script>
<?php
function getSects($parentID=0,$level=0){
	global $DRW,$DRW_read,$DRW_main,$DRW_crm;
	if($level<3){
		$sql = "SELECT sectorID,sectorName,sectorSearchActive FROM cscan_sector WHERE parentID=$parentID ORDER BY sectorName";
		$rs = $DRW->query($sql,$DRW_read);
		$resultCount = $DRW->num_rows($rs);
		if($resultCount>0){
			$GLOBALS['javascript'] .= "cidArray[$parentID] = new Array();\n";
			while($row = $DRW->fetch_array($rs)) {
				$ID = $row['sectorID'];
				$sectorName = $row['sectorName'];
				$sectorSearchActive = $row['sectorSearchActive'];
				echo "<div>";
				for($i=0;$i<$level;$i++){
					echo ' &nbsp; &nbsp; ';
				}
				echo "<label><input type=\"checkbox\" name=\"sectorid_$ID\" value=\"1\" onclick=\"checkP_C($ID,$parentID);\"";
				if(in_array($ID,$GLOBALS['sectorAllow'])){
					echo ' checked="checked"';	
				}
				echo " />".htmlspecialchars($sectorName).'</label>';
				if(!$sectorSearchActive) echo ' <em>[non-search]</em>';
				echo '</div>';
				$GLOBALS['javascript'] .= "pidArray[$ID] = $parentID;\n";
				$GLOBALS['javascript'] .= "cidArray[$parentID][cidArray[$parentID].length] = $ID;\n";
				getSects($ID,$level+1);
			}
		}
	}
}
include 'bottom.php';
?>