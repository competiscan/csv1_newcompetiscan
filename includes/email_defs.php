<?php
$EMAIL_error = 'competiscanerror@competiscan.com';
$EMAIL_RetrievalService = 'retrievalservice@competiscan.com';
$EMAIL_Suggestion = 'suggestion@competiscan.com';
$EMAIL_ContactUs = 'contactus@competiscan.com';
$EMAIL_LostPassword = 'lostpassword@competiscan.com';
$EMAIL_noreply = 'no-reply@competiscan.com';
if(!empty($_SERVER['HTTP_HOST'])){
    $host = strtolower($_SERVER['HTTP_HOST']);
}
else{
    $host = '';
}
?>