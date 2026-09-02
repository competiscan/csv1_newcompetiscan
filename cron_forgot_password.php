<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set("memory_limit", "-1");
set_time_limit(0);
require_once("includes/dbcon.php");
require_once "Mail.php";
require_once "Mail/mime.php";
date_default_timezone_set('America/Chicago');
$today_date = date('Y-m-d');
$one_houre = date('Y-m-d H:i:s', strtotime(' -15 minutes'));
//$one_houre = date('Y-m-d H:i:s', strtotime(' -1 hours'));
//$previous_date = date('Y-m-d', strtotime($today_date . " - 1 day"));
$sql_query = "SELECT userID,insert_date FROM cscan_user_forgot_password_track where DATE_FORMAT(insert_date, '%Y-%m-%d %H:%i:%s')>='" . $one_houre . "'";
$res_query = $DRW->query($sql_query, $DRW_read);
$num_check= $DRW->num_rows($res_query);
if($num_check > 0){
    $mail_array=array();    
    while($row_Check_Data = $DRW->fetch_array($res_query)){
        $userID=$row_Check_Data['userID'];
        $check_date=$row_Check_Data['insert_date'];
        $sql_query_user = "SELECT userID,firstName,companyName,lastName,emailAddress,insert_update_date,reset_password_token FROM cscan_users where userID='".$userID."' AND active='y'";
        $res_query_user = $DRW->query($sql_query_user, $DRW_read);
        if($DRW->num_rows($res_query_user) >0){
            $row_Check_user_Data = $DRW->fetch_assoc($res_query_user);
            $insert_update_date=$row_Check_user_Data['insert_update_date'];
            if($insert_update_date<=$check_date){
                $userID=$row_Check_user_Data['userID'];
                $reset_password_token=$row_Check_user_Data['reset_password_token'];
                $firstName=$row_Check_user_Data['firstName'].' '.$row_Check_user_Data['lastName'];
                //$lastName=$row_Check_user_Data['lastName'];
                //$companyName=$row_Check_user_Data['companyName'];
                $emailAddress=$row_Check_user_Data['emailAddress'];
                //$name = $firstName;
                //if($lastName!='') $name .= ' '.$lastName;
                //if($name=='') $name .= ' '.$emailAddress;
                //if($companyName!='') $name .= ' ('.$companyName.')';
                //$reset_password_token = md5(uniqid(rand(), true));
                $encryptedID = base64_encode(convert_uuencode($userID));
                $resetPasswordLink = 'https://competiscan.com/reset-password.php?us='.$encryptedID.'&token='.$reset_password_token;
                $mail_array[]=array($userID,$firstName,$emailAddress,$resetPasswordLink);
            }
        }

    }
    //echo "<pre>";
    //print_r($mail_array);
    $user_info='';
    foreach($mail_array as $mail_data){
      $user_info.='<b>Name: </b>'.$mail_data['1'].'<br/>';
      $user_info.='<b>Email: </b>'.$mail_data['2'].'<br/>';
      $user_info.='<b>Password reset link: </b>'.$mail_data['3'].'<br/><br/>';
    }
    $bodyhtml=<<< MAILBODY
<html>
<body>
    <p>Hi Team,</p>
    <p>It seems following users haven`t received their reset password link yet as our watcher detects no password update on elapse of 15 minutes of their attempt.</p>
    $user_info<br/><br/>
    <p>Please contact us if you have any questions or if we can assist with anything else.</p>
    <p>Competiscan Helpdesk<br />
    312-488-1810</p>
    <p>[Note]: It's a system generated email . please avoid to reply to this email.</p>
</body>
</html>
MAILBODY;
//echo $bodyhtml;die;
if(!empty($mail_array)){
    //$to="devendra.tiwari@nmgtechnologies.com";
    //$to="contactus@competiscan.com";
    $to="passwordreset@competiscan.com";
    $subject = "Forgot Password";
    $params = array(
        'username' => '',
        'password' => '',
        'persist' => true,
    );
    $mail = Mail::factory('smtp', $params);
    $crlf = "\n";
    $hdrs = array('From' => "\"Competiscan\" <share@competiscan.com>", 'To' => $to, 'Subject' => $subject);
    $mime = new Mail_mime($crlf);
    //$mime->setTXTBody($bodytext);
    $mime->setHTMLBody($bodyhtml);
    $body = $mime->get();
    $headers = $mime->headers($hdrs);
    $send = $mail->send($to, $headers, $body);
    /*if($send){
        $query = "UPDATE `cscan_users` SET reset_password_token='" . $reset_password_token . "' WHERE userID='".$userID."'";
        $DRW->query($query, $DRW_main); 
    }*/
    }   
}
echo '</br></br>End: '.date("Y-m-d H:i:s");
?>