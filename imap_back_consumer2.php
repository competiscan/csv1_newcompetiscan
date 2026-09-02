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
$username1 = 'consumers2@sbkcenter.com';
//$password1 = 'f5iAK3syA';
//$password1 = '69cF205R';
$password1 = '70bG216K';
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
            //print_r($folders);
            $ehL->write("missing mailbox for $username: " . $sever . $movemailbox . "\n");
            if (true !== imap_createmailbox($imap_stream, imap_utf7_encode($sever . $movemailbox))) {
                $ehL->write("imap_createmailbox(imap_stream (mailbox $mailbox, username $username), " . imap_utf7_encode($sever . $movemailbox) . ") failed\n" . print_r(imap_errors(), true));
                ++$nimap_errors;
            } else {
                $ehL->write("created mailbox for $username: " . $sever . $movemailbox . "\n");
            }
        }
        /* if(!in_arrayi($sever.$tempmailbox,$folders)){
          $ehL->write("missing tempsave_ignore for $username: ".$sever.$tempmailbox."\n");
          if(true !== imap_createmailbox($imap_stream, imap_utf7_encode($sever.$tempmailbox))){
          $ehL->write("imap_createmailbox(imap_stream (mailbox $mailbox, username $username), ".imap_utf7_encode($sever.$tempmailbox).") failed\n".print_r(imap_errors(),true));
          ++$nimap_errors;
          }else{
          $ehL->write("created mailbox for $username: ".$sever.$tempmailbox."\n");
          }
          } */
        //echo"<pre>";
       // print_r($folders);exit;
        $num_msg = imap_num_msg($imap_stream);
        error_log(date("Y-m-d H:i:s") . ' # Inbox has  ' . $num_msg . ' emails');
        $firstmsg = '';
        ############# for contain all the subject in array ######################
       // $subjectsarray = array();
       /*
          foreach($subjectsarray as $keysubject=>$subjectval){
          if ((preg_match('/\\bfwd?\\b/i', strtolower($subjectval)) ||preg_match('/\\bfw?\\b/i', strtolower($subjectval)) || preg_match('/\\brv\\b/i', strtolower($subjectval)) || preg_match('/\\btr\\b/i', strtolower($subjectval)))) {
          $movingfolder   =   '[Gmail]/Trash';
          }else{
          $movingfolder   =   'Inbox.CRM';
          }

          imap_mail_move($imap_stream, $keysubject, $movingfolder);
          imap_expunge($imap_stream);
          }
         */
        ############# for contain all the subject in array ###################### 
        ############# for delete all the message ###############       
        /*
        if (!empty($firstmsg)) {
            if (empty($i)) {
                $i = 2;
            }
            imap_mail_move($imap_stream, trim($firstmsg) . ':' . $i, '[Gmail]/Trash');
            imap_expunge($imap_stream);
        }
        */
        ############# for contain all the subject in array ######################
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

function emailtotext($obj) {
    $outArray = array();
    $outfullArray = array();
    if (is_string($obj)) {
        $outArray[] = $obj;
        $outfullArray[] = $obj;
    } else {
        if (!is_array($obj)) {
            $tmp = $obj;
            $obj = array($tmp);
        }
        foreach ($obj as $id => $object) {
            if (isset($object->mailbox) && isset($object->host)) {
                $out = '';
                if (isset($object->personal))
                    $out .= '"' . str_replace('"', '', $object->personal) . '" <';
                $email = trim($object->mailbox . "@" . $object->host);
                $outArray[] = $email;
                $out .= $email;
                if (isset($object->personal))
                    $out .= '>';
                $outfullArray[] = $out;
            }
        }
    }
    return array($outArray, $outfullArray);
}

function create_part_array(&$part_array = array(), $structure, $parttext = '') {
    $tmpArray = array();
    $next_obj = false;

    foreach ($structure as $key => $value) {
        if ($key != 'parts') {
            $tmpArray[$key] = $value;
        } else {
            $next_obj = $value;
        }
    }
    if (count($tmpArray) > 0) {
        if ($next_obj === false && count($part_array) == 0)
            $parttext = '1';
        $part_array[$parttext] = $tmpArray;
    }
    if ($next_obj !== false) {
        if ($parttext != '')
            $structurepast = $parttext . '.';
        else
            $structurepast = '';
        $partno = 1;
        foreach ($next_obj as $part) {
            create_part_array($part_array, $part, $structurepast . $partno);
            $partno++;
        }
    }
}

function in_arrayi($needle, $haystack) {
    $found = false;
    foreach ($haystack as $value) {
        if (strtolower($value) == strtolower($needle)) {
            $found = true;
        }
    }
    return $found;
}

function getEAssignment($assign_queue = '') {
    global $DRW, $DRW_read, $DRW_main;
    if ($assign_queue == 2) {
        $ct = " AND contact_type_m_c='cons_panelist' AND email_read=0";
    } else {
        $ct = " AND contact_type_m_c='prod_panelist' AND email_read=0";
    }
    $sql2 = "SELECT SQL_NO_CACHE userID,COUNT(e_assigned_admin_userID) AS emails FROM 
		cscan_admin_users LEFT JOIN 
		(SELECT e_assigned_admin_userID FROM cscan_email pd WHERE e_assigned_admin_userID<>0$ct) AS cpd 
		ON(userID=e_assigned_admin_userID) 
		WHERE is_email_assign_queue$assign_queue=1 AND user_status=1 GROUP BY userID order by emails,RAND() LIMIT 1";
    /* $sql2 = "SELECT userID,COUNT(e_assigned_admin_userID) AS emails FROM 
      cscan_admin_users LEFT JOIN
      (SELECT e_assigned_admin_userID FROM cscan_email pd WHERE email_date>=CONCAT(CURDATE(),' 00:00:00') AND email_date<=CONCAT(CURDATE(),' 23:59:59') AND e_assigned_admin_userID<>0) AS cpd
      ON(userID=e_assigned_admin_userID)
      WHERE is_email_assign_queue$assign_queue=1 AND user_status=1 GROUP BY userID order by emails,RAND() LIMIT 1"; */
    $rs2 = $DRW->query($sql2, $DRW_read);
    $row2 = $DRW->fetch_row($rs2);
    $assigned_admin_userID = (int) $row2[0];

    return $assigned_admin_userID;
    //
}
?>