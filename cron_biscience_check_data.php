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
$check_date = date('Y-m-d', strtotime(CHK_DATA_BISCIENCE_SETTIME));
$sql_query = "SELECT insert_date FROM cscan_digital_files where DATE_FORMAT(insert_date, '%Y-%m-%d')>='" . $check_date . "'";
$res_query = $DRW->query($sql_query, $DRW_biscience_digital);
$num_check= $DRW->num_rows($res_query);
if($num_check<1){
    $bodyhtml=<<< MAILBODY
<html>
<body>
    <p>Biscience FTP does not have new records today, as no data was uploaded by  Biscience Team!</p>
    <br/><br/>
    <p>[Note]: It's a system generated email, Please avoid to reply to this email.</p>
</body>
</html>
MAILBODY;
//echo $bodyhtml;die;
    $to="nate@competiscan.com,devendra.tiwari@nmgtechnologies.com,ashok.singh@nmgtechnologies.com,gokul.singh@nmgtechnologies.com";
    //$to="contactus@competiscan.com";
    //$to="passwordreset@competiscan.com";
    $subject = "FTP-Digital-dashboard-data";
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
  
}
echo 'Done';
?>