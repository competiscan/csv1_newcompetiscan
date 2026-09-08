#!/usr/bin/php
<?php
require_once("includes/ehLog_set.php");
$ehL->start(__FILE__);
ini_set("default_charset", "utf-8");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set("memory_limit", "-1");
set_time_limit(0);

include_once('includes/dbcon.php');
$DRW->databaseReadWrite_die = 1;
require_once('includes/clean.php');
require_once('includes/functions.php');
################### Local functions #####################
function pr($str){
    echo '<pre>';
    print_r($str);    
}
$serverMode = siteMode();
$arrOutput = $arrGlobalOutput = [];
$arrGlobalOutput['sitemode'] = $serverMode;
################### Parameters ######################
//update index_input.csv for previous date by passing integer for back date
$backday = (!empty($_REQUEST['p1']))?trim($_REQUEST['p1']):0;
if(isset($_SERVER['argc']) && $_SERVER['argc']>0) {
    $backday = (!empty($_SERVER['argv'][1]))?trim($_SERVER['argv'][1]):$backday;
}

$day = (int)$backday;
//if(empty($day))$day=1;
if(!empty($day)){
    $csvdate = date("Y-m-d", strtotime(" -$day day"));
}else{
    $csvdate = date("Y-m-d");
}
$arrGlobalOutput['csvdate'] = $csvdate;
##################################################################

//empty temporary tables

//$DRW->query("DELETE FROM `cscan_email_sameday_search`",$DRW_main);
//$DRW->query("DELETE FROM `cscan_email_text_sameday_search`",$DRW_main);
//$DRW->query("DELETE FROM `cscan_email_file_sameday_search`",$DRW_main);



//$sql = "SELECT `emails_picked`,`email_indexed`,`email_imported`,`status` FROM cscan_maxmail_sameday_log ORDER BY id DESC LIMIT 1";
//$querychk = $DRW->query($sql,$DRW_read);            
//if($DRW->num_rows($querychk) > 0){
//    $resSt = $DRW->fetch_assoc($querychk);
//    if(($resSt['emails_picked']==0 || $resSt['email_indexed']==0 || $resSt['email_imported']==0) && $resSt['status']!=3){
//        //sleep(300);
//        //die;
//    }
//}


################## update for fetch temp data ###################

$sql = "INSERT INTO cscan_maxmail_sameday_log SET start_time='".date("Y-m-d H:i:s")."'";
if($DRW->query($sql, $DRW_main)){
    $msd_logid = $DRW->insert_id($DRW_main);    
    $sql = "UPDATE cscan_email_tempstore SET isprocess='1' limit 500";
    $DRW->query($sql, $DRW_main);
    $sql = "UPDATE cscan_email_text_tempstore as et INNER JOIN cscan_email_tempstore as e ON (e.muid=et.muid) SET et.isprocess='1' where e.isprocess='1'";
    $DRW->query($sql, $DRW_main);
    $sql = "UPDATE cscan_email_file_tempstore ef INNER JOIN cscan_email_tempstore as e ON (e.muid=ef.muid) SET ef.isprocess='1' where e.isprocess='1'";
    $DRW->query($sql, $DRW_main);       
    
    $sql = "INSERT INTO cscan_email_sameday_search
            (muid,email_date,email_to,email_from,email_subject,contact_type_m_c,deleted,email_cc,email_bcc,panelist_score,mailbox_uid,email_read,email_from_one,panelist_id,e_assigned_admin_userID,panelist_core,email_stateID,is_text_file,from_sent_name,from_sent_email_address,from_sent_date,from_sent_date_format,is_fetch,isnamereplace)
            select 
            muid,email_date,email_to,email_from,email_subject,contact_type_m_c,deleted,email_cc,email_bcc,panelist_score,mailbox_uid,email_read,email_from_one,panelist_id,e_assigned_admin_userID,panelist_core,email_stateID,is_text_file, from_sent_name,from_sent_email_address,from_sent_date,from_sent_date_format,is_fetch,isnamereplace
            from cscan_email_tempstore where isprocess=1";
    $DRW->query($sql, $DRW_main);
    $sql = "INSERT INTO cscan_email_text_sameday_search
            (cetid,cetpart,cettext,cettype,muid,cetidentification)
            select 
            cetid,cetpart,cettext,cettype,muid,cetidentification
            from cscan_email_text_tempstore where isprocess=1";
    $DRW->query($sql, $DRW_main);
    $sql = "INSERT INTO cscan_email_file_sameday_search
            (cefid,cefpart,cefdata,cefname,ceftype,muid,cefidentification,cefdisposition,cefencoding,cefsplit,cefpath )
            select 
            cefid,cefpart,cefdata,cefname,ceftype,muid,cefidentification,cefdisposition,cefencoding,cefsplit,cefpath
            from cscan_email_file_tempstore where isprocess=1";
    $DRW->query($sql, $DRW_main);
    
    $sql = "delete from cscan_email_tempstore where isprocess='1'";
    $DRW->query($sql, $DRW_main);
    $sql = "delete from cscan_email_text_tempstore where isprocess='1'";
    $DRW->query($sql, $DRW_main);
    $sql = "delete from cscan_email_file_tempstore where isprocess='1'";
    $DRW->query($sql, $DRW_main);     
    copystoredaMaxmailHtmlSameday($csvdate);
    if($msd_logid){
        $sql = "select count(*) as cnt from cscan_email_sameday_search";
        $res=   $DRW->query($sql, $DRW_read);
        $dataRes = $DRW->fetch_assoc($res);
        $counter=   $dataRes['cnt'];
        $deleted=   1;
        $sql = "UPDATE cscan_maxmail_sameday_log SET emails_picked='".$counter."',  end_time='".date("Y-m-d H:i:s")."', `status`='1' WHERE id='".$msd_logid."'";
        if($DRW->query($sql, $DRW_main)){
            error_log(date("Y-m-d H:i:s") . '  DONE');
        }
    }
}

exit;


##################################################################
/*
$sever1 = '{imap.gmail.com:993/service=imap/ssl}';
$mailbox1 = $sever1 . 'INBOX';
//$mailbox1 = $sever1 . '[Gmail]/All Mail';
$username1 = 'consumers2@sbkcenter.com';
$password1 = '70bG216K';
$movemailbox1 = 'Inbox.CRM';
$forwardnonmatch1 = 'maureen@competiscan.com';
$consumerArray = array($sever1, $mailbox1, $username1, $password1, $movemailbox1, 'cons_panelist', 1);

$tempmailbox = 'INBOX/tempsave_ignore';

$types = array(0 => 'text', 1 => 'multipart', 2 => 'message', 3 => 'application', 4 => 'audio', 5 => 'image', 6 => 'video', 7 => 'other');
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
error_log(date("Y-m-d H:i:s") . '  START');
$accountArray = array($consumerArray);
foreach ($accountArray as $account) {
    $sever = $account[0];
    $mailbox = $account[1];
    $username = $account[2];
    $password = $account[3];
    $movemailbox = $account[4];
    $defaultcontact = $account[5];
    $messageType = $account[6];

    $imap_stream = imap_open($mailbox, $username, $password);
    //imap_reopen($imap_stream,$mailbox);
    if ($imap_stream === false) {
        $ehL->write("imap_open($mailbox, $username, password...) failed\n" . print_r(imap_errors(), true));
        ++$nimap_errors;
    } else {
        error_log(date("Y-m-d H:i:s") . ' # Logged into machine: ' . $username);
        $folders = imap_list($imap_stream, $sever, "*");
        if (!in_arrayi($sever . $movemailbox, $folders)) {
            $ehL->write("missing mailbox for $username: " . $sever . $movemailbox . "\n");
            if (true !== imap_createmailbox($imap_stream, imap_utf7_encode($sever . $movemailbox))) {
                $ehL->write("imap_createmailbox(imap_stream (mailbox $mailbox, username $username), " . imap_utf7_encode($sever . $movemailbox) . ") failed\n" . print_r(imap_errors(), true));
                ++$nimap_errors;
            } else {
                $ehL->write("created mailbox for $username: " . $sever . $movemailbox . "\n");
            }
        }
        $num_msg = imap_num_msg($imap_stream);
        error_log(date("Y-m-d H:i:s") . ' # Inbox has  ' . $num_msg . ' emails');
        $arrGlobalOutput['num_msg'] = $num_msg;
        //error_log(date("Y-m-d H:i:s") . '  CONNECTED');
        $sql = "INSERT INTO cscan_maxmail_sameday_log SET start_time='".date("Y-m-d H:i:s")."'";
        if($DRW->query($sql, $DRW_main)){
            $msd_logid = $DRW->insert_id($DRW_main);
            #######
            $counter = $deleted = 0;
           
            for ($i = 1; $i <= 250; $i++) {
                
                if(isset($_REQUEST['q'])){
                    $q=$_REQUEST['q'];
                    if($i>$q){
                        die('kill here');
                    }
                    
                }
                //error_log(date("Y-m-d H:i:s") . '  LOOPSTART');
                $header = imap_headerinfo($imap_stream, $i);
                //error_log(date("Y-m-d H:i:s") . '  PROCESSING1');
                $mailbox_uid = imap_uid($imap_stream, $i);
                //error_log(date("Y-m-d H:i:s") . '  PROCESSING1');
                $structure = imap_fetchstructure($imap_stream, $mailbox_uid, FT_UID);
                //error_log(date("Y-m-d H:i:s") . '  PROCESSING2');
                //print_r($structure);
                if (isset($header->from))
                    $from = $header->from;
                else
                    $from = '';
                if (isset($header->reply_to))
                    $replyto = $header->reply_to;
                else
                    $replyto = '';
                if (isset($header->to))
                    $to = $header->to;
                else
                    $to = '';
                if (isset($header->cc)) {
                    $cc = $header->cc; //ccaddress
                    list($ccdbA, $ccdbfullA) = emailtotext($cc);
                    $ccdb = implode(',', $ccdbfullA);
                } else
                    $ccdb = '';
                if (isset($header->bcc)) {
                    $bcc = $header->bcc; //bccaddress
                    list($bccdbA, $bccdbfullA) = emailtotext($bcc);
                    $bccdb = implode(',', $bccdbfullA);
                } else
                    $bccdb = '';
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
                if (isset($header->udate))
                    $email_date = date('Y-m-d H:i:s', $header->udate);
                else
                    $email_date = date('Y-m-d H:i:s');
                list($fromdbA, $fromdbfullA) = emailtotext($from);
                $fromdb = implode(',', $fromdbfullA);
    
                list($todbA, $todbfullA) = emailtotext($to);
                $todb = implode(',', $todbfullA);
    
                if (isset($fromdbA[0]))
                    $checkemail = strtolower(trim($fromdbA[0]));
                else
                    $checkemail = '';
                
                if($checkemail!='' && $checkemail=='consumers@sbkcenter.com'){
                    if(!empty($replyto)){
                        list($replytodbA, $replytodbfullA) = emailtotext($replyto);
                        $fromdb = implode(',', $replytodbfullA);
                        if (isset($replytodbA[0]))
                        $checkemail = strtolower(trim($replytodbA[0]));
                    }                
                }
                
                $query = "SELECT SQL_NO_CACHE COUNT(*) FROM `cscan_email` WHERE `mailbox_uid`='".$mailbox_uid."' AND `email_date`='".$DRW->real_escape_string($email_date)."'";
                if ($defaultcontact != '')
                    $query .= " AND `contact_type_m_c`='".$DRW->real_escape_string($defaultcontact)."'";
                //echo $query;
                $query_result = $DRW->query($query, $DRW_read);
                $count = $DRW->fetch_row($query_result);
                $DRW->free_result($query_result);
                $arrOutput['check']['sql'] = $query;
    
                if ($count[0] == 0) {
                    error_log(date("Y-m-d H:i:s") . '  FOUND  ' . $email_date . ' : ' . $subject);
                    //error_log(date("Y-m-d H:i:s") . '  KEEP1');
                    $keep = false;
                    foreach ($keepSubjects as $subj) {
                        if (trim($subj) == $subject) {
                            $keep = true;
                            break;
                        }
                    }
                    if (!$keep) {
                        foreach ($keepHosts as $host) {
                            if (strpos($fromdb, trim($host)) !== false) {
                                $keep = true;
                                break;
                            }
                        }
                        if (!$keep) {
                            foreach ($keepEmails as $host) {
                                if (strpos($fromdb, trim($host)) !== false) {
                                    $keep = true;
                                    break;
                                }
                            }
                            if (!$keep && (preg_match('/\\bfwd?\\b/i', $subject) || preg_match('/\\brv\\b/i', $subject) || preg_match('/\\btr\\b/i', $subject))) {
                                $keep = true;
                                foreach ($skipHosts as $host) {
                                    if (strpos($fromdb, trim($host)) !== false) {
                                        $keep = false;
                                        break;
                                    }
                                }
                                if ($keep) {
                                    foreach ($skipEmails as $host) {
                                        if (strpos($fromdb, trim($host)) !== false) {
                                            $keep = false;
                                            break;
                                        }
                                    }
                                }
                            }
                        }
                    }
                    //error_log(date("Y-m-d H:i:s") . '  KEEP DONE');
                    if ($keep) {    
                        $sql = "SELECT muid FROM cscan_email_sameday_search WHERE mailbox_uid = '".$mailbox_uid."' LIMIT 1";
                        $query1 = $DRW->query($sql, $DRW_read);                    
                        if ($DRW->num_rows($query1) == 0) {
                            //error_log(date("Y-m-d H:i:s") . '  INSIDEKEEP1');
                            $DRW->free_result($query1);
    
                            $contact_type_m_c = $defaultcontact;
                            if ($contact_type_m_c == 'prod_panelist') {
                                $e_assigned_admin_userID = getEAssignment();
                            } else {
                                $e_assigned_admin_userID = 0;
                            }                
                            $sql = "SELECT panelist_id,stateID FROM cscan_panelists 
                                    WHERE active=1 
                                    AND (email='".$DRW->real_escape_string($checkemail)."'
                                            OR alt_email='".$DRW->real_escape_string($checkemail)."'
                                            OR more_email LIKE '%".$DRW->real_escape_string($checkemail)."%'
                                        )
                                    LIMIT 1";
                            $query2 = $DRW->query($sql, $DRW_read);                        
                            if ($DRW->num_rows($query2) > 0) {
                                //error_log(date("Y-m-d H:i:s") . '  INSIDEKEEP2');
                                $row = $DRW->fetch_assoc($query2);
                                $panelist_id = $row['panelist_id'];
                                $email_stateID = $row['stateID'];
                                $DRW->free_result($query2);
                                
                                $remove = false;
                                //note: muid of temporary tables completly independent from original table
                                $sql = "INSERT INTO `cscan_email_sameday_search` SET
                                        e_assigned_admin_userID = '".$e_assigned_admin_userID."',
                                        mailbox_uid = '".$mailbox_uid."',
                                        email_date = '".$DRW->real_escape_string($email_date)."',
                                        email_to = '".$DRW->real_escape_string($todb)."',
                                        email_from = '".$DRW->real_escape_string($fromdb)."',
                                        email_from_one = '".$DRW->real_escape_string($checkemail)."',
                                        email_subject = '".$DRW->real_escape_string($subject)."',
                                        contact_type_m_c = '".$DRW->real_escape_string($contact_type_m_c)."',
                                        email_cc = '".$DRW->real_escape_string($ccdb)."',
                                        email_bcc = '".$DRW->real_escape_string($bccdb)."',
                                        deleted = '".$DRW->real_escape_string($messageType)."',
                                        panelist_id = '".$panelist_id."',
                                        email_stateID = '".$email_stateID."'";
                                if($DRW->query($sql, $DRW_main)){                               
                                    $temp_muid = $DRW->insert_id($DRW_main);
                                    //error_log(date("Y-m-d H:i:s") . ' +ADD ' . $temp_muid);
                                    $arrOutput['exec']['sql1'] = $sql;
    
                                    $part_array = array();
                                    //pr($structure);
                                    create_part_array($part_array, $structure);
                                    $RFC822_part1 = '';
                                    $RFC822_part2 = '';
                                    $is_RFC822 = false;
                                    $in_RFC822 = false;
                                    $is_UTF8 = false;
                                    $totalparts = count($part_array);
                                    //pr($part_array);die;
                                    if($totalparts > 0){
                                        //error_log(date("Y-m-d H:i:s") . '  INSIDEKEEP3');
                                        foreach ($part_array as $parti => $party) {
                                            if (isset($party['ifid']) && $party['ifid'])
                                                $id = trim($party['id']);
                                            else
                                                $id = '';
                                            if (isset($party['type']))
                                                $type_no = $party['type'];
                                            else
                                                $type_no = 7;
                                            if (isset($types[$type_no]))
                                                $type = $types[$type_no];
                                            else
                                                $type = '';
                                            if (isset($party['subtype']))
                                                $subtype = strtolower($party['subtype']);
                                            else
                                                $subtype = '';
                                            if (isset($party['disposition']))
                                                $disposition = strtolower($party['disposition']);
                                            else
                                                $disposition = '';
                                            if (isset($party['encoding']))
                                                $encoding = $party['encoding'];
                                            else
                                                $encoding = 5;
                                            if (isset($party['bytes']))
                                                $bytes = (int) $party['bytes'];
                                            else
                                                $bytes = 0;
                                            //description,lines
        
                                            $filename = '';
                                            $tmpfilename = '';
                                            $charset = '';
                                            $paramArray = array();
                                            if (isset($party['dparameters']))
                                                $paramArray[] = $party['dparameters'];
                                            if (isset($party['parameters']))
                                                $paramArray[] = $party['parameters'];
                                            foreach ($paramArray as $param_o) {
                                                foreach ($param_o as $parameter) {
                                                    if ($parameter->value != '') {
                                                        //[attribute] => FILENAME
                                                        //[attribute] => NAME
                                                        $pname = strtolower(trim($parameter->attribute));
                                                        if (strpos($pname, 'name') !== false) {
                                                            if ($filename == '')
                                                                $filename = trim($parameter->value);
                                                            break;
                                                        }
                                                        elseif (strpos($pname, 'charset') !== false) {
                                                            $charset = strtolower(trim($parameter->value));
                                                        } elseif (strpos($pname, 'boundary') !== false) {
                                                            if ($in_RFC822 && $RFC822_part2 == '')
                                                                $RFC822_part2 = $parti;
                                                            continue 3;
                                                        }
                                                        else {
                                                            $tmpfilename = trim($parameter->value); //." (".$parameter->attribute.")";
                                                        }
                                                    }
                                                }
                                            }
                                            if ($filename == '' && $tmpfilename != '')
                                                $filename = $tmpfilename;
        
                                            if ($parti != '') {
                                                if ($subtype == 'rfc822') {
                                                    $in_RFC822 = $is_RFC822 = true;
                                                    $RFC822_part1 = $parti;
                                                    $RFC822_part2 = '';
                                                    if (strpos($filename, '.') === false)
                                                        continue;
                                                }
                                                elseif ($in_RFC822 && $RFC822_part2 != '' && preg_match('/^' . $RFC822_part2 . '/', $parti)) {
                                                    //$parti = preg_replace('/\\.[^\\.]+\\.([^\\.]+)$/','.$1',$parti);
                                                    $parti = preg_replace('/^' . $RFC822_part2 . '/', $RFC822_part1, $parti);
                                                } else {
                                                    $RFC822_part1 = '';
                                                    $RFC822_part2 = '';
                                                    $in_RFC822 = false;
                                                }
                    
                                                $bodydata = imap_fetchbody($imap_stream, $mailbox_uid, $parti, FT_UID | FT_PEEK);
                                                //error_log(date("Y-m-d H:i:s") . '  INSIDEKEEP4');
                                                if ($bodydata != '') {
                                                    if ($type_no == 0 && ($subtype == 'plain' || $subtype == 'html')) {
                                                        if ($encoding == 3)
                                                            $bodydata = base64_decode($bodydata);
                                                        elseif ($encoding == 4)
                                                            $bodydata = quoted_printable_decode($bodydata);
        
                                                        if ($charset == 'utf-8') {
                                                            $bodydata = utf8_decode($bodydata); 
                                                        }
                                                        $sql = "INSERT INTO `cscan_email_text_sameday_search` SET
                                                                cetpart = '".$DRW->real_escape_string($parti)."',
                                                                cettext = '".$DRW->real_escape_string(utf8_encode($bodydata))."',
                                                                cettype = '".$DRW->real_escape_string($type . '/' . $subtype)."',
                                                                muid = '".$DRW->real_escape_string($temp_muid)."',
                                                                cetidentification = '".$DRW->real_escape_string($id)."'";   
                                                        if($DRW->query($sql, $DRW_main)){
                                                            //error_log(date("Y-m-d H:i:s") . '  INSIDEKEEP5');
                                                            $arrOutput['exec']['sql2'] = $sql;
                                                            if($subtype == 'html'){
                                                                //error_log(date("Y-m-d H:i:s") . '  +ADDPREP TO CSV  ' . $email_date . ' : ' . $subject);
                                                                if(copydaMaxmailHtmlSameday($temp_muid,$mailbox_uid,$bodydata,$panelist_id,$csvdate)){
                                                                    $sql = "UPDATE `cscan_email_sameday_search` SET is_text_file='1' WHERE muid=$temp_muid";
                                                                    if($DRW->query($sql, $DRW_main)){
                                                                        $counter++;
                                                                        $arrOutput['exec']['sql3'] = $sql;
                                                                        //error_log(date("Y-m-d H:i:s") . '  +ADDED TO CSV  ' . $email_date . ' : ' . $subject);
                                                                    }else{
                                                                        $remove = true;
                                                                        $arrOutput['not_exec']['sql4'] = $sql;
                                                                    } 
                                                                    //error_log(date("Y-m-d H:i:s") . '  INSIDEKEEP6');   
                                                                }else{
                                                                    $remove = true;
                                                                    $arrOutput['not_exc']['file'] = 'html not created';
                                                                }                                                                                                                    
                                                            }                                                    
                                                        }else{
                                                            $remove = true;
                                                            $arrOutput['not_exc']['sql4'] = $sql;                                                        
                                                        }  
        
                                                    } elseif (($type_no != 1 && $type_no != 2) || $subtype == 'rfc822') {
                                                        $efilesArray = array();
                                                        $efilesArray[] = array($parti, $bodydata, $filename, $type, $subtype, $id, $disposition, $encoding);
        
                                                        if (strpos($subtype, 'tnef') !== false || strpos($subtype, 'ms-tnef') !== false || $filename == 'winmail.dat') {
                                                            $tmpdirname = tempnam('tmp_upload', "tnefhandler");
                                                            unlink($tmpdirname);
                                                            //tempnam() created the file, however, we need a directory of this name.
                                                            mkdir($tmpdirname, 0755);
                    
                                                            if (empty($filename)) {
                                                                $filename = 'winmail';
                                                            }
                    
                                                            $tmpdatname = $tmpdirname . '/' . $filename;
                    
                                                            if (file_put_contents($tmpdatname, emailDecode($bodydata, $encoding)) !== false) {
                                                                $output = shell_exec("/usr/bin/tnef --overwrite --save-body --directory=" . escapeshellarg($tmpdirname) . " --file=" . escapeshellarg($tmpdatname));
                                                                $tnefs = array();
                                                                if ($handle = opendir($tmpdirname)) {
                                                                    while (false !== ($entry = readdir($handle))) {
                                                                        if ($entry != "." && $entry != "..") {
                                                                            $entry = basename($entry);
                                                                            if ($entry != $filename) {
                                                                                $tnefs[] = basename($entry);
                                                                            }
                                                                        }
                                                                    }
                                                                    closedir($handle);
                                                                }
                                                                foreach ($tnefs as $k => $tnef) {
                                                                    if (is_file($tmpdirname . '/' . $tnef)) {
                                                                        $filetype = trim(shell_exec("/usr/bin/file -bi " . escapeshellarg($tmpdirname . '/' . $tnef)));
                                                                        $filetypeArray = explode('/', $filetype);
                                                                        if (isset($filetypeArray[0]))
                                                                            $type2 = $filetypeArray[0];
                                                                        else
                                                                            $type2 = '';
                                                                        if (isset($filetypeArray[1]))
                                                                            $subtype2 = $filetypeArray[1];
                                                                        else
                                                                            $subtype2 = '';
                                                                        $efilesArray[] = array($parti . ".0" . ($k + 1), file_get_contents($tmpdirname . '/' . $tnef), $tnef, $type2, $subtype2, '', '', '');
                                                                        unlink($tmpdirname . '/' . $tnef);
                                                                    }
                                                                }
                                                            }
                                                            unlink($tmpdatname);
                                                            rmdir($tmpdirname);
                                                        }
                                                        if(count($efilesArray)>0){
                                                            //error_log(date("Y-m-d H:i:s") . '  INSIDEKEEP7');
                                                            foreach ($efilesArray as $efile) {
                                                                list($parti, $bodydata, $filename, $type, $subtype, $id, $disposition, $encoding) = $efile;
                                                                
                                                                $sql = "INSERT INTO `cscan_email_file_sameday_search` SET
                                                                    cefpart= '".$DRW->real_escape_string($parti)."',
                                                                    cefname= '".$DRW->real_escape_string($filename)."',
                                                                    ceftype= '".$DRW->real_escape_string($type . '/' . $subtype)."',
                                                                    muid= '".$DRW->real_escape_string($temp_muid)."',
                                                                    cefidentification= '".$DRW->real_escape_string($id)."',
                                                                    cefdisposition= '".$DRW->real_escape_string($disposition)."',
                                                                    cefencoding = '".$DRW->real_escape_string($encoding)."'";
                                                                if($DRW->query($sql, $DRW_main)){  
                                                                    //error_log(date("Y-m-d H:i:s") . '  INSIDEKEEP8');                                                              
                                                                    $cefid = $DRW->insert_id($DRW_main);
                                                                    $arrOutput['exec']['sql4'] = $sql;
            
                                                                    $sql = "UPDATE `cscan_email_sameday_search` SET is_text_file='1' WHERE muid=$temp_muid";
                                                                    if($DRW->query($sql, $DRW_main)){
                                                                        $arrOutput['exec']['sql5'] = $sql;
                                                                    }else{
                                                                        $remove = true;
                                                                        $arrOutput['not_exec']['sql5'] = $sql;
                                                                    }                                                                
            
                                                                    list($fyear, $fmonth, $fdaytime) = explode('-', $email_date);
                        
                                                                    $yearpath = $fyear . '/';
                                                                    $monthpath = $fmonth . '/';
                                                                    $daypath = substr($fdaytime, 0, 2) . '/';
                                                                    $pathpart = dirname(__FILE__) . '/contentFiles/';
                            
                                                                    if (!is_dir($pathpart . $yearpath)) {
                                                                        mkdir($pathpart . $yearpath, 02755);
                                                                        @chmod($pathpart . $yearpath, 02755);
                                                                        @chown($pathpart . $yearpath, 'apache');
                                                                        //@chgrp($pathpart.$yearpath,'competiscan_web);
                                                                    }
                                                                    if (!is_dir($pathpart . $yearpath . $monthpath)) {
                                                                        mkdir($pathpart . $yearpath . $monthpath, 02755);
                                                                        @chmod($pathpart . $yearpath . $monthpath, 02755);
                                                                        @chown($pathpart . $yearpath . $monthpath, 'apache');
                                                                        //@chgrp($pathpart.$yearpath.$monthpath,'competiscan_web');
                                                                    }
                                                                    if (!is_dir($pathpart . $yearpath . $monthpath . $daypath)) {
                                                                        mkdir($pathpart . $yearpath . $monthpath . $daypath, 02755);
                                                                        @chmod($pathpart . $yearpath . $monthpath . $daypath, 02755);
                                                                        @chown($pathpart . $yearpath . $monthpath . $daypath, 'apache');
                                                                        //@chgrp($pathpart.$yearpath.$monthpath,'competiscan_web');
                                                                    }
                                                                    $cefpath = $pathpart . $yearpath . $monthpath . $daypath . $cefid;
                            
                                                                    $fp = fopen($cefpath, "w");
                                                                    if ($fp) {
                                                                        fwrite($fp, emailDecode($bodydata, $encoding));
                                                                        fclose($fp);
                                                                        @chmod($cefpath, 0644);
                                                                        @chown($cefpath, 'apache');
                                                                        //@chgrp($cefpath,'competiscan_web');
                                                                        $sql = "UPDATE `cscan_email_file_sameday_search` SET cefpath='" . $DRW->real_escape_string('/contentFiles/' . $yearpath . $monthpath . $daypath . $cefid) . "' WHERE cefid=$cefid";
                                                                        if($DRW->query($sql, $DRW_main)){
                                                                            $arrOutput['exec']['sql6'] = $sql;
                                                                        }else{
                                                                            $remove = true;
                                                                            $arrOutput['not_exec']['sql6'] = $sql;
                                                                        }                                                                    
                                                                    }    
                                                                    //error_log(date("Y-m-d H:i:s") . '  INSIDEKEEP9');                                                    
                                                                }else{
                                                                    $remove = true;
                                                                    $arrOutput['not_exec']['sql3'] = $sql;
                                                                }
                                                            }
                                                            //error_log(date("Y-m-d H:i:s") . '  INSIDEKEEP10');
                                                        }
                                                    }else{
                                                        $remove = true;
                                                        $arrOutput['not_exc']['subtype'] = $subtype;
                                                    }
                                                    //error_log(date("Y-m-d H:i:s") . '  INSIDEKEEP11');
                                                }else{
                                                    $remove = true;
                                                    $arrOutput['not_exc']['bodydata'] = $bodydata;
                                                }
                                            }else{
                                                $remove = true;
                                                $arrOutput['not_exc']['parti'] = $parti;
                                            }
                                        }
                                        //error_log(date("Y-m-d H:i:s") . '  INSIDEKEEP12');
                                    }else{
                                        $remove = true;
                                        $arrOutput['not_exc']['totalparts'] = $totalparts;
                                    }
                                    
                                }else{
                                    $arrOutput['not_exec']['sql3'] = $sql;
                                }
                                if($remove){
                                    //remove from cscan_email_sameday_search
                                    $DRW->query("DELETE FROM cscan_email_sameday_search WHERE muid='".$temp_muid."'", $DRW_main);
                                    $DRW->query("DELETE FROM cscan_email_text_sameday_search WHERE muid='".$temp_muid."'", $DRW_main);
                                    $DRW->query("DELETE FROM cscan_email_file_sameday_search WHERE muid='".$temp_muid."'", $DRW_main);
                                }
                            }else{
                                $arrOutput['not_exec']['sql2'] = $sql;
                            }
                        }else{
                            $arrOutput['not_exec']['sql1'] = $sql;
                        }
                        // delete emails which are processed
                        if($serverMode == 'live'){
                            $arrOutput['del1'] = 'Yes';
                            $deleted++;
                            imap_delete($imap_stream, $mailbox_uid, FT_UID);
                            imap_expunge($imap_stream);
                            error_log(date("Y-m-d H:i:s") . ' -DEL ' . $email_date . ' : ' . $subject);
                        }
                    } else {
                        if($serverMode == 'live'){
                            $arrOutput['del2'] = 'Yes';
                            if (true !== imap_mail_move($imap_stream, $mailbox_uid, $movemailbox, CP_UID)) {
                                $ehL->write("imap_mail_move(imap_stream (mailbox $mailbox, username $username), $mailbox_uid, $movemailbox, CP_UID) failed\n" . print_r(imap_errors(), true));
                                ++$nimap_errors;
                            }
                            error_log(date("Y-m-d H:i:s") . ' .NUH ' . $header->message_id);
                        }
                    }
                } else {
                    if($serverMode == 'live'){
                        $arrOutput['del3'] = 'Yes';
                        $deleted++;
                        imap_delete($imap_stream, $mailbox_uid, FT_UID);
                        imap_expunge($imap_stream);
                        $ehL->write("imap_delete(imap_stream (mailbox $mailbox, username $username), $mailbox_uid, CP_UID) $email_date because of duplicate\n");
                        error_log(date("Y-m-d H:i:s") . ' -DEL ' . $email_date . ' : ' . $subject);
                    }
                }
                if ($i % 100 == 0) {
                    if($serverMode == 'live'){
                        imap_expunge($imap_stream);
                        error_log(date("Y-m-d H:i:s") . ' !PURGE ');
                    }
                } 
                $arrGlobalOutput[$i] =  $arrOutput;
            }
            if($msd_logid){
                $sql = "UPDATE cscan_maxmail_sameday_log SET emails_picked='".$counter."', email_deleted= '".$deleted."', end_time='".date("Y-m-d H:i:s")."', `status`='1' WHERE id='".$msd_logid."'";
                if($DRW->query($sql, $DRW_main)){
                    error_log(date("Y-m-d H:i:s") . '  DONE');
                }
            }
            #######            
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
//pr($arrGlobalOutput);//die;
 
 */
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
    
    $rs2 = $DRW->query($sql2, $DRW_read);
    $row2 = $DRW->fetch_row($rs2);
    $assigned_admin_userID = (int) $row2[0];

    return $assigned_admin_userID;
}
?>