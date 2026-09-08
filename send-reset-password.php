<?php
require_once('includes/globalSession.php');
if(isset( $_POST['email'] ) && $_POST['email']!=''){
    $sql = "SELECT firstName,lastName,companyName,emailAddress,userID FROM cscan_users WHERE active='y' AND emailAddress='".$DRW->real_escape_string($_POST['email'])."'";
    $result = $DRW->query($sql,$DRW_read);
    $rs = $DRW->fetch_row($result);
    if(!empty($rs)){
        $firstName = $rs[0];
        $lastName = $rs[1];
        $companyName = $rs[2];
        $emailAddress = trim($rs[3]);
        $id = $rs[4];
        
        $name = $firstName;
        if($lastName!='') $name .= ' '.$lastName;
        if($name=='') $name .= ' '.$emailAddress;
        if($companyName!='') $name .= ' ('.$companyName.')';
        
        $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
        
        $reset_password_token = md5(uniqid(rand(), true));
        $encryptedID = base64_encode(convert_uuencode($id));
        if($_SERVER['HTTP_HOST'] == 'localhost'){
            $resetPasswordLink = $actual_link.'/competiscan.com/reset-password.php?us='.$encryptedID.'&token='.$reset_password_token;
        }else{
            $resetPasswordLink = $actual_link.'/reset-password.php?us='.$encryptedID.'&token='.$reset_password_token;
        }

        $name = ucwords($name);
        $namehtml = htmlspecialchars($name);
        $emailhtml = htmlspecialchars($emailAddress);
        //Track forget Password
        if($emailAddress!=''){
            $query_insert = "INSERT INTO cscan_user_forgot_password_track SET userID='" .$id. "'";
            $DRW->query($query_insert, $DRW_main);
            $decryptedID = convert_uudecode(base64_decode($encryptedID));
            $query = "UPDATE `cscan_users` SET reset_password_token='" . $reset_password_token . "' WHERE userID='".$decryptedID."'";
            $DRW->query($query, $DRW_main);
                   
        }
        if($emailAddress!=''){
            require_once('Mail.php');
            require_once('Mail/mime.php');
            
            $bodyhtml = <<< MAILBODY
<html>
<body>
    <p>Hello <b>$namehtml</b>,</p>
    <p><a href='$resetPasswordLink'>Click Here</a> to reset your password or copy below link to your browser-</p>
    <a href='$resetPasswordLink'>$resetPasswordLink</a><br/><br/>
    <p>Please contact us if you have any questions or if we can assist with anything else.</p>
    <p>Competiscan Helpdesk<br />
    312-488-1810</p>
</body>
</html>
MAILBODY;
    
            $bodytext = "Hello <b>$name</b>,\n\n<a href='$resetPasswordLink'>Click Here</a> to reset your password or copy below link to your browser- \n<a href='$resetPasswordLink'>$resetPasswordLink</a>\n\nPlease contact us if you have any questions or if we can assist with anything else.\n\nCompetiscan Helpdesk\n312-488-1810";      
            
            $crlf = "\n"; //if you want to send the generated MIME message using Mail  then you have to set $crlf to "\n"
            $hdrs = array('From'=>"\"Competiscan\" <share@competiscan.com>",'To'=>$emailAddress,'Subject'=>$emailAddress." Forgot Password");
            
            $mime = new Mail_mime($crlf);
            $mime->setTXTBody($bodytext);
            $mime->setHTMLBody($bodyhtml);
            $body = $mime->get();
            $headers = $mime->headers($hdrs);
            
            //$mail =& Mail::factory('mail','-f'.$EMAIL_error);
            $params = array(
                /*'host' => 'ssl://smtp.gmail.com',
                'port' => '465',
                'auth' => true,
                'username'=>'nishant.garg.newmediaguru@gmail.com',
                'password'=>'nishant@1234',*/
                /*'host' => 'smtp.sendgrid.net',
                'port' => '587',
                'auth' => true,
                'username'=>'nishant_garg88',
                'password'=>'nishantgarg88',*/
                'username'=>'',
                'password'=>'',
            );
            $mail = Mail::factory('smtp',$params);
            $send = $mail->send($emailAddress, $headers, $body);
            //$send=0;
            //if(($send)) {
            if(!PEAR::isError($send)) {
                /*$decryptedID = convert_uudecode(base64_decode($encryptedID));
                $query = "UPDATE `cscan_users` SET reset_password_token='" . $reset_password_token . "' WHERE userID='".$decryptedID."'";
                if($DRW->query($query, $DRW_main)){
                    echo 1;die;
                }*/
                echo 1;die;
            }else{
                echo 2;die;
                
            }
        }
    }else{
        echo 0;die;
    }
}
?>