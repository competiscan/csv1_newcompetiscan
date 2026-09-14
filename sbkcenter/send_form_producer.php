<?php
if (isset($_POST['email'])) {
    if(!defined('ENV')){
        define('ENV',getenv('SERVER_NAME'));
    }
    function get_client_ip() { 
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if(getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if(getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if(getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if(getenv('HTTP_FORWARDED'))
           $ipaddress = getenv('HTTP_FORWARDED');
        else if(getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }
    //echo $ipaddress ='pp '.getenv("REMOTE_ADDR").' tt' ; die;
   //echo $cleint_ip=get_client_ip(); die;
    $uri=$_SERVER['REQUEST_URI'];
    $referer_url=$_SERVER['HTTP_REFERER'];
    if(!strstr($referer_url,'sbkcenter') && (!strstr($referer_url,'google')) && (!strstr($referer_url,'youtube')) && (!strstr($referer_url,'yahoo.com')) && (!strstr($referer_url,'outlook.live'))  && !isset($_SESSION['referer_url'])){    
        $_SESSION['referer_url']=$referer_url;
    }    

    
    if(ENV == 'localhost'){ 
        $floating_writer_ip = '10.0.0.19';//floating writer (currently on dh08042012) rw
        $conn_hostname=$floating_writer_ip;
        $conn_username="root";
        $conn_password="root@20165";
        $conn_database="competi_competidb";
        $siteUrl='http://localhost/competiscan.com/sbkcenter/';
       
    }
    else if(ENV == 'demo.competiscan.com'){
        $floating_writer_ip ='172.18.4.231';//floating writer (currently on dh08042012) rw
        $conn_hostname=$floating_writer_ip;
        $conn_username="root";
        $conn_password="Xohv3iewotezu8ah";
        $conn_database="competi_competidb";
        $siteUrl='https://demo.competiscan.com/sbkcenter/';
    }else{
        $floating_writer_ip = '34.226.25.177';//floating writer (currently on dh08042012) rw
        $conn_hostname=$floating_writer_ip;
        $conn_username="app_writeuser";
        $conn_password="Ano@11SDFLH@13NMldrf";
        $conn_database="competi_competidb";
        $siteUrl='https://sbkcenter.com/';
    }

    $dbh = mysql_connect($conn_hostname, $conn_username, $conn_password) or die ("Unable to connect to MySQL");
     
    $selected = mysql_select_db($conn_database,$dbh) or die ("Could not select Competiscan database");
    //print_r($_SESSION);
       

    // EDIT THE 2 LINES BELOW AS REQUIRED
    $email_to = "producers@sbkcenter.com,consumers3@sbkcenter.com";
    //$email_to = "pradeep.chaurasia@newmediaguru.org, pradeep.chaurasia.newmediaguru@gmail.com";
    $email_subject = "Producer Panel - New panelist submission";

    function died($error) {
        // your error code can go here
        echo "We are very sorry, but there were error(s) found with the form you submitted. ";
        echo "These errors appear below.<br /><br />";
        echo $error . "<br /><br />";
        echo "Please go back and fix these errors.<br /><br />";
        die();
    }

    // validation expected data exists
    if (!isset($_POST['first_name']) ||
            !isset($_POST['last_name']) ||
            !isset($_POST['email']) ||
            !isset($_POST['company']) ||
            !isset($_POST['address']) ||
            !isset($_POST['city']) ||
            !isset($_POST['state']) ||
            !isset($_POST['zip']) ||
            !isset($_POST['telephone']) ||
            !isset($_POST['referral'])) {

        died('We are sorry, but there appears to be a problem with the form you submitted.');
    }

    $first_name = $_POST['first_name']; // required
    $last_name = $_POST['last_name']; // required
    $email_from = $_POST['email']; // required
    $company = $_POST['company']; // not required
    $address = $_POST['address']; // required
    $city = $_POST['city']; // required
    $state = $_POST['state']; // required
    $zip = $_POST['zip']; // required
    $telephone = $_POST['telephone']; // required
    $referral = $_POST['referral']; // not required

    $error_message = "";
    $email_exp = '/^[A-Za-z0-9._%-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}$/';
    if (!preg_match($email_exp, $email_from)) {
        $error_message .= 'The Email Address you entered does not appear to be valid.<br />';
    }
    $string_exp = "/^[A-Za-z .'-]+$/";
    if (!preg_match($string_exp, $first_name)) {
        $error_message .= 'The First Name you entered does not appear to be valid.<br />';
    }
    if (!preg_match($string_exp, $last_name)) {
        $error_message .= 'The Last Name you entered does not appear to be valid.<br />';
    }
    //$string_exp = "/^[A-Za-z0-9.-]+$/";
    //if(!preg_match($string_exp,$address)) {
    //$error_message .= 'The Address you entered does not appear to be valid.<br />';
    //}
    $string_exp = "/^[A-Za-z .'-]+$/";
    if (!preg_match($string_exp, $city)) {
        $error_message .= 'The City you entered does not appear to be valid.<br />';
    }
    $string_exp = "/^[A-Za-z .'-]+$/";
    if (!preg_match($string_exp, $state)) {
        $error_message .= 'The State you entered does not appear to be valid.<br />';
    }
    $string_exp = "/^[0-9A-Za-z .-]+$/";
    if (!preg_match($string_exp, $zip)) {
        $error_message .= 'The Zip you entered does not appear to be valid.<br />';
    }
    $string_exp = "/^[0-9().-]+$/";
    if (!preg_match($string_exp, $telephone)) {
        $error_message .= 'The Telephone number you entered does not appear to be valid.<br />';
    }
    if (strlen($error_message) > 0) {
        died($error_message);
    }
    $email_message = "Form details below.\n\n <br /><br />";

    function clean_string($string) {
        $bad = array("content-type", "bcc:", "to:", "cc:", "href");
        return str_replace($bad, "", $string);
    }

    $query = "INSERT INTO `cscan_sbkc_producer` (first_name,last_name,email,company,address,city,state,zip,phone,refered_by) VALUES('".mysql_real_escape_string($first_name)."','".mysql_real_escape_string($last_name)."','".mysql_real_escape_string($email_from)."','".mysql_real_escape_string($company)."','".mysql_real_escape_string($address)."','".mysql_real_escape_string($city)."','".mysql_real_escape_string($state)."','".mysql_real_escape_string($zip)."','".mysql_real_escape_string($telephone)."','".mysql_real_escape_string($referral)."')";
    $result = mysql_query($query);// or die("Unable to execute query :'".$query."' due to following error : ".mysql_error());
    $incquery = "SELECT LAST_INSERT_ID()";
    $incquery = mysql_query($incquery) or die(mysql_error());
    $_SESSION['save_id'] = mysql_result($incquery,0);
    $_SESSION['save'] = array();
   
    
    $email_message .= "First Name: " . clean_string($first_name) . "\n <br />";
    $email_message .= "Last Name: " . clean_string($last_name) . "\n <br />";
    $email_message .= "Email: " . clean_string($email_from) . "\n <br />";
    $email_message .= "Company: " . clean_string($company) . "\n <br />";
    $email_message .= "Address: " . clean_string($address) . "\n <br />";
    $email_message .= "City: " . clean_string($city) . "\n <br />";
    $email_message .= "State: " . clean_string($state) . "\n <br />";
    $email_message .= "Zip: " . clean_string($zip) . "\n <br />";
    $email_message .= "Telephone: " . clean_string($telephone) . "\n <br />";
    $email_message .= "Reffered By: " . clean_string($referral) . "\n <br />";
    $email_message .= ">";
    //$email_message .="</body></html>>MAILBODY";
// create email headers
    /*
      $headers = 'From: '.$email_from."\r\n".
      'Reply-To: '.$email_from."\r\n" .
      'X-Mailer: PHP/' . phpversion();
      @mail($email_to, $email_subject, $email_message, $headers);

     */


    require_once('Mail.php');
    require_once('Mail/mime.php');
    $crlf = "\n";
    //$mail =& Mail::factory('mail','-f'.$EMAIL_error);
    $params = array(
        'username' => '',
        'password' => '',
        'persist' => true,
    );
    $mail = & Mail::factory('smtp', $params);

    $hdrs = array('To' => $email_to, 'From' => 'consumers@sbkcenter.com', 'Subject' => $email_subject);
    //$hdrs = array('To' => $email_to, 'From' => 'share@competiscan.com', 'Subject' => $email_subject);
    $mime = new Mail_mime($crlf);
    $mime->setHTMLBody($email_message);
    //$mime->setTXTBody($email_message);
    $body = $mime->get();
    $headers = $mime->headers($hdrs);
    $send = $mail->send($email_to, $headers, $body);
    if (PEAR::isError($send)) {

        echo $send->getdebuginfo();
    }
    $redirect_page=$siteUrl.'send_form_producer.php?succ=1';               
    header("Location: $redirect_page");
    die;
    
}
if(isset($_GET['succ']) AND $_GET['succ']==1){
?>
    <!-- include your own success html here -->


    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
    <!--
    Name       : SBKCENTER
    -->
    <html xmlns="https://www.w3.org/1999/xhtml">
        <head>
            <meta http-equiv="content-type" content="text/html; charset=utf-8" />
            <meta name="description" content="" />
            <meta name="keywords" content="" />
            <title>SBKC</title>
            <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet" type="text/css" />
            <link rel="stylesheet" type="text/css" href="style.css" />

            <script type="text/javascript" src="https://www.sbkcenter.com/devsite/livevalidation_standalone.js"></script>

        </head>
        <body>
            <div id="wrapper">
                <div id="header">
                    <div id="logo">
                        <p><a href="index.html"><img src="images/sbkclogo.gif" width="210"></a></p>
                    </div>
                    <div id="menu">
                        <ul>
                            <li class="first current_page_item"><a href="index.html">Home</a></li>
                            <li><a href="consumer.html">CONSUMER / BUSINESS OWNER</a></li>
                            <li><a href="producer.html">Producer / Advisor</a></li>
                            <li><a href="broker.html">Mortgage Broker</a></li>
                            <li><a href="faq.html">FAQs</a></li>
                            <li class="last"><a href="contact.html">Contact</a></li>
                        </ul>
                        <br class="clearfix" />
                    </div>
                </div>
                <div id="inner">

                    <div id="page">
                        <div id="content">
                            <div class="box"><img class="image alignleft" src="images/email.jpg" width="260" height="234" alt="" />
                                <table cellspacing="10" cellpadding="0">
                                    <tr>
                                        <td colspan="2"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                <tbody>
                                                    <tr valign="top">
                                                        <td><h1>SBKC Producer Panel</h1>
                                                            </br>
                                                            <p></p><h3>Thank you for your submission! </h3></p>

                                                            <div class="section" id="exampleEmail">

                                                                <p>You will receive an email within the next 24hrs with more information on how to participate on the producer panel. Thank you so much for your interest and we look forward to working with you.</p>





                                                            </center></td>
                                                    </tr>
                                                </tbody>
                                            </table></td>
                                    </tr>
                                    <tr>
                                        <td colspan="2"></td>
                                    </tr>
                                    <br />
                                </table>
                                <p><br class="clearfix" />
                                </p></div>
                            <br class="clearfix" />
                        </div>
                    </div>
                </div>
                <div id="page-bottom">
                    <div class="box col3">
                        <p>
                            Consumers:<br />
                            consumers@sbkcenter.com<br />
                            (312) 546-4922<br />
                            SBKC | P.O. Box 1905 | Franklin Park,<br />IL 60131
                        </p>
                    </div>
                    <div class="box col3">
                        <p>
                            Producers:<br />
                            producers@sbkcenter.com <br />
                            (773) 227-7454 <br />
                            SBKC | P.O. Box 1905 | Franklin Park,<br />IL 60131
                        </p>
                    </div>
                    <div class="box">
                        <p>
                            Mortgage Brokers:<br />
                            brokers@sbkcenter.com<br />
                            (773) 227-7454<br />
                            SBKC | P.O. Box 1905 | Franklin Park,<br />IL 60131
                        </p>
                    </div>
                </div>  

            </div>
            <div id="footer"> 
                <center><a target="_blank" id="bbblink" class="sehzbus" href="https://www.bbb.org/chicago/business-reviews/marketing-programs-and-services/small-business-knowledge-center-in-chicago-il-88346842#bbblogo" title="Small Business Knowledge Center, Marketing Programs & Services, Chicago, IL" style="display: block;position: relative;overflow: hidden; width: 100px; height: 38px; margin: 0px; padding: 0px;"><img style="padding: 0px; border: none;" id="bbblinkimg" src="https://seal-chicago.bbb.org/logo/sehzbus/small-business-knowledge-center-88346842.png" width="200" height="38" alt="Small Business Knowledge Center, Marketing Programs & Services, Chicago, IL" /></a>	</center>        
                     <p>
              <a href="SBKC_Terms_and_Conditions.pdf" target="_blank">Terms & Conditions</a>
                </p> 
                        <P>All rights reserved 2006 - <?php echo date("Y"); ?> Small Business Knowledge Center<br />
                            <a href="https://www.sbkcenter.com/">www.sbkcenter.com</a></P>        		        		
            </div>

                
                            
                            <!-- Added for tracking code  -->
<!--                            <iframe src="https://roi-rocket.org/p.ashx?o=19602&e=336&t=TRANSACTION_ID" height="1" width="1" frameborder="0"></iframe>-->
                            <!-- End for tracking code -->
                        </body>
                        </html>
    
<?php } ?>