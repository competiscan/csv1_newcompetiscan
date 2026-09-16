<?php
if (isset($_POST['email'])) {

    // EDIT THE 2 LINES BELOW AS REQUIRED
    $email_to = "producers@sbkcenter.com";
    //$email_to = "arvind.chaurasia@newmediaguru.org; pradeep.chaurasia@newmediaguru.org";
    $email_subject = "Producer Submission";

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
            !isset($_POST['telephone']) ||
            !isset($_POST['comments'])) {


        died('We are sorry, but there appears to be a problem with the form you submitted.');
    }

    $first_name = $_POST['first_name']; // required
    $last_name = $_POST['last_name']; // required
    $email_from = $_POST['email']; // required
    $telephone = $_POST['telephone']; // not required
    $comments = $_POST['comments']; // required

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
    if (strlen($comments) < 2) {
        $error_message .= 'The Comments you entered do not appear to be valid.<br />';
    }
    if (strlen($error_message) > 0) {
        died($error_message);
    }
    $email_message = "Form details below.\n\n";

    function clean_string($string) {
        $bad = array("content-type", "bcc:", "to:", "cc:", "href");
        return str_replace($bad, "", $string);
    }

    $email_message .= "First Name: " . clean_string($first_name) . "\n";
    $email_message .= "Last Name: " . clean_string($last_name) . "\n";
    $email_message .= "Email: " . clean_string($email_from) . "\n";
    $email_message .= "Telephone: " . clean_string($telephone) . "\n";
    $email_message .= "Comments: " . clean_string($comments) . "\n";


// create email headers
    $headers = 'From: ' . $email_from . "\r\n" .
            'Reply-To: ' . $email_from . "\r\n" .
            'X-Mailer: PHP/' . phpversion();
    @mail($email_to, $email_subject, $email_message, $headers);
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

            <script type="text/javascript" src="https://www.sbkcenter.com/livevalidation_standalone.js"></script>

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
                            <li><a href="consumer.html">Consumer</a></li>
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

                                                            <div class="section" id="exampleEmail"></div>

                                                            <p>You will receive an email within the next 24 hrs with more information on how to particiapte on the producer panel. Thank you so much for your interest and we look forward to working with you. </p>





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
                            <a href="https://www.sbkcenter.com/">www.sbkcenter.com</a></P>        		        		</div>




                        </body>
                        </html>

                        <?php
                    }
                    ?>
