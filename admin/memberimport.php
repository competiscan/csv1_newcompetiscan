<?php
$ALLOW_GROUPS = array(1);
require_once("../auth_auth.php");
include 'top.php';
require_once '../includes/functions.php';
require_once 'TemplateMailer.php';

$page_heading = 'IMPORT MEMBERS';

?>
<table width="100%" border="0" cellspacing="0" cellpadding="5" class="text">
  <tr><td class="adminhead" align="center" colspan="2"><?php echo $page_heading; ?></td></tr>
  <tr>
    <td align="center">
      <table border="0" cellspacing="0" cellpadding="5" class="text">
<?php

if((isset($_POST['send']) && !$_FILES['importfile']['error']) || (isset($_POST['tempfile']) && $_POST['tempfile']!='')) {
	if(isset($_POST['tempfile']) && $_POST['tempfile']!=''){
		$filename = $_POST['tempfile'];
		if($_POST['accept']==1) $action = 2;
		else $action = 3;
	}
	else {
		$uploaddir = substr(dirname(__FILE__),0,-5);
		$filename = $uploaddir . 'tmp_upload/' . basename($_FILES['importfile']['name']);
		
		if (move_uploaded_file($_FILES['importfile']['tmp_name'], $filename)) {
			$action = 1;
		} 
		else {
			$action = 3;
			$filename = '';
		}
	}
	$coltotal = 13;
	$className='';
	if($action==1 || $action==2){
		if($action==1){
			print "<tr><td colspan=\"$coltotal\"><strong>Please verify that all of your Members have been added correctly:</strong></td></tr>";
			print "<tr><td class=\"adminhead\">Email Address</td><td class=\"adminhead\">Password</td><td class=\"adminhead\">First Name</td><td class=\"adminhead\">Last Name</td><td class=\"adminhead\">Company Name</td><td class=\"adminhead\">Client Name</td><td class=\"adminhead\">Street Address</td><td class=\"adminhead\">City</td><td class=\"adminhead\">State/Province</td><td class=\"adminhead\">Division</td><td class=\"adminhead\">Zip/Postal Code</td><td class=\"adminhead\">Phone</td><td class=\"adminhead\">Fax</td></tr>";
		}
		$file = fopen($filename,'r');
		if($file){
			require_once('membersearch_fields.php');
			while (!feof($file)) {
				$line = trim(fgets($file, 4096));
				if($line!=''){
					$lineArray = array();
					$lineArray = preg_split('/,(?=(?:[^"]*"[^"]*")*(?![^"]*"))/',$line);
					$colcount = count($lineArray);
					array_walk($lineArray, 'trim_value');
					if($colcount>$coltotal){
						$lineArray = array_slice($lineArray, 0, $coltotal);
					}
					elseif($colcount<$coltotal){
						$lineArray = array_pad($lineArray, $coltotal, '');
					}
					foreach($lineArray as $key=>$value){
						$lineArray[$key] = preg_replace('/^"(.+)"$/s','$1',$lineArray[$key]);
						$lineArray[$key] = preg_replace('/""/','"',$lineArray[$key]);
					}
					
					$emailAddress = $lineArray[0];
					$password = $lineArray[1];
					$firstName = $lineArray[2];
					$lastName = $lineArray[3];
					$companyName = $lineArray[4];
					$clientName = $lineArray[5];
					$streetAddress = $lineArray[6];
					$city = $lineArray[7];
					$state = $lineArray[8];
					$country = $lineArray[9];
					$zipCode = $lineArray[10];
					$phone = $lineArray[11];
					$fax = $lineArray[12];                                                                               
					
					if(preg_match('/^email\\s*address$/i',trim($emailAddress))){
						continue;
					}
					
					$sql = "SELECT userID from cscan_users where emailAddress = '".$DRW->real_escape_string($emailAddress)."'";
					$result = $DRW->query($sql,$DRW_read);
					
					if( $DRW->num_rows($result) == 0 && $emailAddress!='') {
						$sql = "SELECT ID from cscan_sub_users where emailAddress = '".$DRW->real_escape_string($emailAddress)."'";
						$chk_result = $DRW->query($sql,$DRW_read);
						if($DRW->num_rows($chk_result) == 0) {
							if ($className=='selected-bg') $className='white-bg';
							else $className='selected-bg';
							if($action==1) {
								print "<tr><td class='$className'>$emailAddress</td><td class='$className'>$password</td><td class='$className'>$firstName</td><td class='$className'>$lastName</td><td class='$className'>$companyName</td><td class='$className'>$clientName</td><td class='$className'>$streetAddress</td><td class='$className'>$city</td><td class='$className'>$state</td><td class='$className'>$country</td><td class='$className'>$zipCode</td><td class='$className'>$phone</td><td class='$className'>$fax</td></tr>";
							}
							elseif($action==2){
                                                                if(empty($password)){
                                                                   $password=generateRandomPassword();
                                                                }
								$insert_sql = "insert into cscan_users set password='".$DRW->real_escape_string($password)."', firstName='".$DRW->real_escape_string($firstName)."', lastName='".$DRW->real_escape_string($lastName)."', emailAddress='".$DRW->real_escape_string($emailAddress)."', companyName='".$DRW->real_escape_string($companyName)."', clientName='".$DRW->real_escape_string($clientName)."', streetAddress='".$DRW->real_escape_string($streetAddress)."', city='".$DRW->real_escape_string($city)."', state='".$DRW->real_escape_string($state)."', country='".$DRW->real_escape_string($country)."', zipCode='".$DRW->real_escape_string($zipCode)."', phone='".$DRW->real_escape_string($phone)."', fax='$fax',dateAdded=NOW()"; 
								$DRW->query($insert_sql,$DRW_main);
								$userID = $DRW->insert_id($DRW_main);
								//$search_fields = membersearch_fields.php
								foreach($search_fields as $f=>$v){
									$query = "INSERT IGNORE INTO cscan_search_exclude (userID,search_field) VALUES ($userID,'$f')";
									$DRW->query($query,$DRW_main);
								}
                                                                
                                                                /*### Start Welcome email functionality ###*/
                                                                $email_template='welcome-email';
                                                                $mailer = new TemplateMailer();
                                                                $mailer->setTemplate($email_template);
                                                                $mailer->addToAddress(array("email" => $emailAddress, "name" => "$firstName $lastName"));
                                                                $confirmation = '';
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
                                                                $mailer->send($placeholder_info);
                                                                if(!empty($confirmation) && !empty($AUTH_DATA['user_email'])){
                                                                    $email_template='welcome-email';
                                                                    $mailer = new TemplateMailer();
                                                                    $mailer->setTemplate($email_template.$confirmation);
                                                                    $mailer->addToAddress(array("email" => $AUTH_DATA['user_email'], "name" => $AUTH_DATA['userName']));
                                                                    $mailer->send($placeholder_info);
                                                                }
							}
						}
					}
				}
			}
			fclose($file);
		}
	}
	if($action==2 || $action==3){
		if($filename!='') unlink($filename);
		ob_end_clean();
		header("Location: {$_SERVER['PHP_SELF']}?done=$action");
		exit;
	}
	else{
		print "<tr><td colspan=\"$coltotal\">&nbsp;</td></tr>";
		print "<tr><td colspan=\"$coltotal\"><form method=\"post\" name=\"verify\" action=\"{$_SERVER['PHP_SELF']}\">
		<label><input type=\"radio\" name=\"accept\" value=\"1\" checked=\"checked\" />Accept All</label> &nbsp; <label><input type=\"radio\" name=\"accept\" value=\"0\" />Decline All</label> &nbsp; <input type=\"submit\" name=\"subby\" value=\"Submit\" class=\"button\" />
		<input type=\"hidden\" name=\"tempfile\" value=\"".htmlspecialchars($filename, ENT_QUOTES)."\" /></form></td></tr>";
	}
}
else{
?>
    <tr>
      <td>
      <strong>Upload CSV with one entry per row and fields in order:</strong><br />
       Email Address, Password, First Name, Last Name, Company Name, Client Name, Street Address, City, State/Province, Division, Zip Code, Phone, Fax
		<form method="post" name="userForm" enctype="multipart/form-data" action="<?php print $_SERVER['PHP_SELF']; ?>"><input type="hidden" name="MAX_FILE_SIZE" value="32000000" />
        <table border="0" cellpadding="5" cellspacing="0">
          <tr>
              <td colspan="2" class="error"><?php 
				if(isset($_GET['done'])){
					if($_GET['done']==2){
						print '[Import Accepted]';
					}
					elseif($_GET['done']==3){
						print '[Import Declined]';
					}
				} 
              ?>&nbsp;</td>
          </tr>
          <tr>
            <td class="bodytext" align="right">File:</td>
            <td><input type="file" name="importfile" size="40" class="input_box" /></td>
          </tr>
          <tr>
              <td colspan="2"><input class="button" type="submit" name="upload" value="Upload" /></td>
          </tr>
        </table>
        <input type="hidden" name="send" value="1" /></form>
      </td>
    </tr>
<?php } ?>
</table>
      </td>
    </tr>
</table>
<?php
include 'bottom.php';

function trim_value(&$value){
   $value = trim($value);
}
?>
