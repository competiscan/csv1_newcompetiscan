<?php
echo phpinfo();
exit;

require_once "Mail.php";
require_once "Mail/mime.php";
 
// see http://pear.php.net/manual/en/package.mail.mail-mime.php
// for further extended documentation on Mail_Mime
$inputFiles = array(1,2);
//$from = "pradeep.rauthan.newmediaguru@gmail.com";
$to = "pradeep.rauthan@nmgtechnologies.com,pradeep.rauthan.newmediaguru@gmail.com";
$subject = "FTP Chicago Records(read)\r\n\r\n";
//$text = "This is a text test email message";
$html = '
<html>
    <head><title>FTP Chicago Records(read)</title></head>
    <body>
        <table width="50%">
            <tr><td colspan="3" align="center"><h3>Chicago Records FTP</h3><small>(Automated Email - '.date("Y-m-d H:i:s").')</small></td></tr>
            <tr><td colspan="3" align="center">&nbsp;</td></tr>
            <tr>
                <td>Number of records to be search</td>
                <td>:</td>
                <td>'.count($inputFiles).'</td>
            </tr>
        </table>
    </body>
</html>
';
//$crlf = "\n";

// create a new Mail_Mime for use
//$mime = new Mail_mime($crlf); 
// define body for Text only receipt
//$mime->setTXTBody($text); 
// define body for HTML capable recipients
//$mime->setHTMLBody($html);
//$file = "attachment.jpg";
//$mimetype = "image/jpeg";
//$mime->addAttachment($file, $mimetype); 

//$host = "smtp.gmail.com";
//$username = "pradeep.rauthan.newmediaguru@gmail.com";
//$password = "pradeep@1234";
//
//$smtp = Mail::factory('smtp',
//    array (
//        'host' => $host,
//        'auth' => true,
//        'username' => $username,
//        'password' => $password
//    )
//);
//$headers = array ('From' => $from, 'To' => $to, 'Subject' => $subject);
//$body = $mime->get();
//$headers = $mime->headers($headers); 
//$mail = $smtp->send($to, $headers, $body);
//$s = PEAR::isError($mail);
//if ($s) {
//    echo("" . $mail->getMessage() . "");
//} else {
//    echo("Message successfully sent!");
//}


$params = array(
    'username' => '',
    'password' => '',
    'persist' => true,
);
$mail = & Mail::factory('smtp', $params);
$crlf = "\n";
$hdrs = array('From' => "\"Competiscan\" <richard@competiscan.com>", 'To' => $to, 'Subject' => $subject);
$mime = new Mail_mime($crlf);
$mime->setHTMLBody($html);
$body = $mime->get();
$headers = $mime->headers($hdrs);
$send = $mail->send($to, $headers, $body);
die;
die;


require_once "Mail.php";
require_once "Mail/mime.php";
 
// see http://pear.php.net/manual/en/package.mail.mail-mime.php
// for further extended documentation on Mail_Mime

$from = "pradeep.rauthan.newmediaguru@gmail.com";
$to = "pradeep.rauthan@nmgtechnologies.com,pradeep.rauthan.newmediaguru@gmail.com";
$subject = "Import Chicago Records FTP(Cron)\r\n\r\n";
$text = "This is a text test email message";
$html = '
<html>
    <head><title>Chicago Records FTP</title></head>
    <body>
        <table width="50%">
            <tr><td colspan="3" align="center"><h3>Chicago Records FTP</h3><small>(Automated Email - '.date("Y-m-d H:i:s").')</small></td></tr>
            <tr><td colspan="3" align="center">&nbsp;</td></tr>
            <tr>
                <td><b>Total Files</b></td>
                <td><b>Moved To Citi FTP</b></td>
                <td><b>Duplicate Files</b></td>
            </tr>
            <tr>
                <td>1000</td>
                <td>700</td>
                <td>300</td>
            </tr>
        </table>
    </body>
</html>
';
$crlf = "\n";

// create a new Mail_Mime for use
$mime = new Mail_mime($crlf); 
// define body for Text only receipt
$mime->setTXTBody($text); 
// define body for HTML capable recipients
$mime->setHTMLBody($html);
 
// specify a file to attach below, relative to the script's location
// if not using an attachment, comment these lines out
// set appropriate MIME type for attachment you are using below, if applicable
// for reference see http://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types

$file = "attachment.jpg";
$mimetype = "image/jpeg";
$mime->addAttachment($file, $mimetype); 

// specify the SMTP server credentials to be used for delivery
// if using a third party mail service, be sure to use their hostname
if(!defined('ENV')){
    define('ENV',getenv('SERVER_NAME'));
} 
if(ENV == 'localhost' || ENV == 'demo.competiscan.com'){ 
    $host = "smtp.gmail.com";
    $username = "pradeep.rauthan.newmediaguru@gmail.com";
    $password = "pradeep@1234";
}else{
    $host = "smtp.us-east-1.amazonaws.com";
    $username = "AKIAJV35CMSLLUVEZILQ";
    $password = "AtimkwLY7NOoxG0ulkirVWsYqN97eRIPaLeNn0LvqCJj";
}

//
$smtp = Mail::factory('smtp',
    array (
        'host' => $host,
        'auth' => true,
        'username' => $username,
        'password' => $password
    )
);
//
$headers = array ('From' => $from, 'To' => $to, 'Subject' => $subject);
//$smtp = Mail::factory('smtp',$params);

$body = $mime->get();
$headers = $mime->headers($headers); 

$mail = $smtp->send($to, $headers, $body);
$s = PEAR::isError($mail);
if ($s) {
    echo("" . $mail->getMessage() . "");
} else {
    echo '<pre>';print_r($s);print_r($mail);
    echo("Message successfully sent!");
}

//require_once('Mail.php');
//require_once('Mail/mime.php');
//    $crlf = "\n";
//    //$mail =& Mail::factory('mail','-f'.$EMAIL_error);
//    
//    $params = array(
//        'username' => '',
//        'password' => '',
//        'persist' => true,
//    );
//    $mail = & Mail::factory('smtp', $params);
//
//    $hdrs = array('To' => $to, 'From' => 'brokers@sbkcenter.com', 'Subject' => $subject);
//    $mime = new Mail_mime($crlf);
//    $mime->setHTMLBody($body);
//    //$mime->setTXTBody($email_message);
//    $body = $mime->get();
//    $headers = $mime->headers($headers);
//    $send = $mail->send($to, $headers, $body);
//    if (PEAR::isError($send)) {
//        echo $send->getdebuginfo();
//    }
    
die;
?>
<?php
die;
$output = shell_exec('sh /srv/import/digital.sh');
echo $output;
die;
?>
<?php
die;
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set("memory_limit", "-1");
set_time_limit(0);
require_once("includes/ehLog_set.php");
$ehL->start(__FILE__);
require_once ("includes/dbcon.php");
$DRW->databaseReadWrite_die = 1;
include_once 'includes/functions.php';
include_once 'includes/thumb.php';
########################################

$sql = "SELECT o.productID,o.document_path as document_path_orig,o.document_filename as orig_filename,d.document_path,d.document_filename,o.document_size_byte,o.document_createddate
FROM `cscan_document_orig` o 
INNER JOIN cscan_document d ON(o.productID=d.productID) 
WHERE o.`document_createddate` >= '2018-03-26'
AND o.`document_size_byte` = '0'";

$query = $DRW->query($sql ,$DRW_read);
if($DRW->num_rows($query )>0){
	while($row = $DRW->fetch_assoc($query)){
		$productID = $row['productID'];
		$root = $row['document_path_orig'];
		if (strpos($root, '/orig') !== false) {
        		$root = substr($root, 0, strpos($root, '/orig'));
    		}
		$existing_file = dirname(__FILE__).$root.'/'.$row['document_filename']; 
		$orig_path = dirname(__FILE__).$row['document_path_orig'].$row['orig_filename'];
		echo $existing_file.' to be copied '.$orig_path.'</br>';
		if(file_exists($existing_file )){
			echo $existing_file.' exists </br>';
			if(copy($existing_file, $orig_path)){
				$file_size = filesize($existing_file);
				if($file_size > 0){
					$sql2 = "UPDATE cscan_document_orig SET document_size_byte = '".$file_size."' WHERE productID ='".$productID ."'";
					$DRW->query($sql2, $DRW_main);

					$sql2 = "UPDATE cscan_document SET document_size_byte = '".$file_size."' WHERE productID ='".$productID ."'";
					$DRW->query($sql2, $DRW_main);
				}
				echo $existing_file.' copied to '.$orig_path.'</br></br></br>';
				//die;
			}
		}
	}
}
?>

