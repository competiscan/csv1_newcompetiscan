#!/usr/bin/php
<?php
require_once("includes/ehLog_set.php");
$ehL->start(__FILE__);
ini_set("default_charset", "utf-8");
//ini_set("memory_limit", "512M");
ini_set("memory_limit", "-1");
set_time_limit(0);
include_once('includes/dbcon.php');
$DRW->databaseReadWrite_die = 1;
require_once('includes/clean.php');
require_once('includes/functions.php');

$sever1 = '{imap.gmail.com:993/service=imap/ssl}';
$mailbox1 = $sever1 . 'INBOX';
$mailbox1 = $sever1 . '[Gmail]/Trash';
$username1 = 'consumers2@sbkcenter.com';
//$password1 = 'f5iAK3syA';
//$password1 = '69cF205R';
//$password1 = '70bG216K';
$password1 = '47TUp90#yL80';
$movemailbox1 = 'Inbox.CRM';
$forwardnonmatch1 = 'maureen@competiscan.com';
$consumerArray = array($sever1, $mailbox1, $username1, $password1, $movemailbox1, 'cons_panelist', 1);
/*
  $sever2 = '{imap.gmail.com:993/service=imap/ssl}';
  $mailbox2 = $sever2 . 'INBOX';
  $username2 = 'producers@sbkcenter.com';
  //$password2 = '41f2Q934e';
  $password2 = 'Comp1928$';
  $movemailbox2 = 'INBOX.crm';
  $forwardnonmatch2 = 'jennifer@competiscan.com'; //jennifer@sbkcenter.com
  $producerArray = array($sever2, $mailbox2, $username2, $password2, $movemailbox2, 'prod_panelist', 1);
 */
/*
  $testsever = '{mail.highlandsolutions.com:143/service=imap/notls}';
  $testmailbox = $testsever.'INBOX';
  $testusername = 'devtest@highlandsolutions.com';
  $testpassword = 'devtest';
  $testmovemailbox = 'INBOX.crm';
  $testArray = array($testsever, $testmailbox, $testusername, $testpassword, $testmovemailbox, '',0);
 */
$tempmailbox = 'INBOX/tempsave_ignore';

$types = array(0 => 'text', 1 => 'multipart', 2 => 'message', 3 => 'application', 4 => 'audio', 5 => 'image', 6 => 'video', 7 => 'other');
//$encoding_type = array(0=>'7BIT',1=>'8BIT',2=>'BINARY',3=>'BASE64',4=>'QUOTED-PRINTABLE',5=>'OTHER');
//$messageTypes = array(0=>'',1=>'Unused',2=>'Used',3=>'Junk',4=>'Copy');

$query2 = "SELECT `domains`,`emails`,`no_domains`,`no_emails`,`subjects` FROM `cscan_filter` ORDER BY `filterdate` DESC LIMIT 1";
$query_result2 = $DRW->query($query2, $DRW_read);
$data2 = $DRW->fetch_row($query_result2);
$DRW->free_result($query_result2);
$domains = $data2[0];
$emails = $data2[1];
$no_domains = $data2[2];
$no_emails = $data2[3];
$subjects = $data2[4];
$keepHosts = preg_split('/(\r?\n|\r)/', $domains, -1, PREG_SPLIT_NO_EMPTY);
$keepEmails = preg_split('/(\r?\n|\r)/', $emails, -1, PREG_SPLIT_NO_EMPTY);
$skipHosts = preg_split('/(\r?\n|\r)/', $no_domains, -1, PREG_SPLIT_NO_EMPTY);
$skipEmails = preg_split('/(\r?\n|\r)/', $no_emails, -1, PREG_SPLIT_NO_EMPTY);
$keepSubjects = preg_split('/(\r?\n|\r)/', $subjects, -1, PREG_SPLIT_NO_EMPTY);
$nimap_errors = 0;

//$accountArray = array($consumerArray, $producerArray);
$accountArray = array($consumerArray);
//$accountArray = array( $producerArray);
/*
$sever1 = '{imap.gmail.com:993/service=imap/ssl}';
$mailbox1 = $sever1 . 'INBOX';
$mailbox1 = $sever1 . '[Gmail]/Trash';
$username1 = 'arvind.chaurasia.newmediaguru@gmail.com';
$password1 = 'arvind01021985';
$movemailbox1 = 'Inbox.CRM';
$forwardnonmatch1 = 'arvind.chaurasia.newmediaguru@gmail.com';

$testarray = array($sever1, $mailbox1, $username1, $password1, $movemailbox1, 'cons_panelist', 1);
$accountArray = array($testarray);
*/
foreach ($accountArray as $account) {
    $sever = $account[0];
    $mailbox = $account[1];
    $username = $account[2];
    $password = $account[3];
    $movemailbox = $account[4];
    $defaultcontact = $account[5];
    $messageType = $account[6];
    $imap_stream = imap_open($mailbox, $username, $password)or die('Cannot connect to server: ' . imap_last_error());
    //imap_reopen($imap_stream,$mailbox);
    if ($imap_stream === false) {
        $ehL->write("imap_open($mailbox, $username, password...) failed\n" . print_r(imap_errors(), true));
        ++$nimap_errors;
    } else {
        error_log(date("Y-m-d H:i:s") . ' # Logged into machine: ' . $username);
        $folders = imap_list($imap_stream, $sever, "*");
        if (!in_arrayi($sever . $movemailbox, $folders)) {
            //$ehL->write("cant find ".$sever.$movemailbox." in:\n");
            // print_r($folders);exit;
            $ehL->write("missing mailbox for $username: " . $sever . $movemailbox . "\n");
            if (true !== imap_createmailbox($imap_stream, imap_utf7_encode($sever . $movemailbox))) {
                $ehL->write("imap_createmailbox(imap_stream (mailbox $mailbox, username $username), " . imap_utf7_encode($sever . $movemailbox) . ") failed\n" . print_r(imap_errors(), true));
                ++$nimap_errors;
            } else {
                $ehL->write("created mailbox for $username: " . $sever . $movemailbox . "\n");
            }
        }

        //print_r($folders);
        $num_msg = imap_num_msg($imap_stream);
        error_log(date("Y-m-d H:i:s") . ' # Inbox has  ' . $num_msg . ' emails');
        $firstmsg = '';

        ############# for contain all the subject in array ######################
        $subjectsarray = array();
        ############# for contain all the subject in array ######################
        for ($i = 1; $i <= $num_msg; $i++) {
            //for ($i = 1; $i <=11; $i++) {
            if ($imap_stream) {
                $header = imap_headerinfo($imap_stream, $i);
                //print_r($header->Msgno);exit;
                // $mailbox_uid = imap_uid($imap_stream, $i);
                //$structure = imap_fetchstructure($imap_stream, $mailbox_uid, FT_UID);

                if (isset($header->subject)) {
                    $subject = trim($header->subject);
                    if (strpos($subject, '=') !== false) {
                        $newsubject = '';
                        $elements = imap_mime_header_decode($subject);
                        foreach ($elements as $key => $part) {
                            $newsubject .= $part->text;
                        }
                        if ($newsubject != '') {
                            $subject = $newsubject;
                            $is_UTF8 = true;
                        }
                    }
                } else
                    $subject = '';
                if ($subject != '') {
                    // if(strstr(strtolower($subject),'fwd:')<=0 ){
                    if (!(preg_match('/\\bfwd?\\b/i', strtolower($subject))) && !(preg_match('/\\bfw?\\b/i', strtolower($subject)) ) && !(preg_match('/\\brv\\b/i', strtolower($subject))) && !(preg_match('/\\btr\\b/i', strtolower($subject)))) {
                    //if (!(preg_match('/\\bfwd?\\b/i', strtolower($subject))) && !(preg_match('/\\bfw?\\b/i', strtolower($subject)) )) {
                        $movingfolder = 'Inbox.CRM';
                        imap_mail_move($imap_stream, $i, $movingfolder);
                        imap_expunge($imap_stream);
                    }
                }
            }
        }


        if (imap_close($imap_stream, CL_EXPUNGE) !== TRUE) {
            $ehL->write("imap_close(imap_stream (mailbox $mailbox, username $username), CL_EXPUNGE) failed\n" . print_r(imap_errors(), true));
            ++$nimap_errors;
        }
    }
}
if ($nimap_errors) {
    $ehL->write("$nimap_errors errors");
    error_log(date("Y-m-d H:i:s") . ' ? nimap_errors: ' . $nimap_errors);
}
$ehL->stop(false);
error_log(date("Y-m-d H:i:s") . ' # Made it to the end');

function in_arrayi($needle, $haystack) {
    $found = false;
    foreach ($haystack as $value) {
        if (strtolower($value) == strtolower($needle)) {
            $found = true;
        }
    }
    return $found;
}
?>