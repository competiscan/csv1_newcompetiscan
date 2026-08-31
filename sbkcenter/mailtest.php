<?php

//Obtain Form Submission Data
$name = $_POST['name'];
$visitor_email = $_POST['email'];
$message = $_POST['message'];

//Define Variables
$email_from = 'PRODUCER PANEL';
$email_subject = "New panelist submission";
$email_body = "You have received a new message from the user $name.\n".
    "Here is the message:\n $message";

//Send Email
//mail('jessica.eccles.ambrose@gmail.com', $email_from, $email_subject, $email_body);
mail('arvind.chaurasia@newmediaguru.org', $email_from, $email_subject, $email_body);
//done. redirect to thank-you page.
header('Location: thank-you.html');

?>
