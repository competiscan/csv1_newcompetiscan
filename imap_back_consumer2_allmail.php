#!/usr/bin/php
<?php
require_once("includes/ehLog_set.php");
//$ehL->start(__FILE__);
ini_set("default_charset", "utf-8");
//ini_set("memory_limit", "512M");
ini_set("memory_limit", "-1");
set_time_limit(0);

include_once('includes/dbcon.php');
require_once("includes/email_functions.php");
$DRW->databaseReadWrite_die = 1;
require_once('includes/clean.php');
require_once('includes/functions.php');
date_default_timezone_set('America/Chicago');
$sever1 = '{imap.gmail.com:993/service=imap/ssl}';
//$sever1 = '{imap.gmail.com:993/imap/ssl/novalidate-cert}';
//$mailbox1 = $sever1 . 'INBOX';
$mailbox1 = $sever1.'[Gmail]/All Mail';
//$mailbox1 = $sever1.'All Mail';
$username1 = 'consumers2@sbkcenter.com';
$password1 = '47TUp90#yL80';
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

//$accountArray = array($consumerArray, $producerArray);
$accountArray = array($consumerArray);
//$accountArray = array( $producerArray);

//$testarray = array($sever1, $mailbox1, $username1, $password1, $movemailbox1,'cons_panelist',1);
//$accountArray=array($testarray);
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
        echo 'connection failed'; die;
        //$ehL->write("imap_open($mailbox, $username, password...) failed\n" . print_r(imap_errors(), true));
        ++$nimap_errors;
    } else {
        echo 'connection made successfully'; //die;
        error_log(date("Y-m-d H:i:s") . ' # Logged into machine: ' . $username);
        $folders = imap_list($imap_stream, $sever, "*");
        //echo '<pre>';
        //print_r($folders); die;
        if (!in_arrayi($sever . $movemailbox, $folders)) {
            //$ehL->write("cant find ".$sever.$movemailbox." in:\n");
            //print_r($folders);
            //$ehL->write("missing mailbox for $username: " . $sever . $movemailbox . "\n");
            if (true !== imap_createmailbox($imap_stream, imap_utf7_encode($sever . $movemailbox))) {
                //$ehL->write("imap_createmailbox(imap_stream (mailbox $mailbox, username $username), " . imap_utf7_encode($sever . $movemailbox) . ") failed\n" . print_r(imap_errors(), true));
                ++$nimap_errors;
            } else {
                //$ehL->write("created mailbox for $username: " . $sever . $movemailbox . "\n");
            }
        }
        
        //print_r($folders);
       echo $num_msg = imap_num_msg($imap_stream); die;
       
        error_log(date("Y-m-d H:i:s") . ' # Inbox has  ' . $num_msg . ' emails');
        //print $num_msg;
        //for ($i = 1; $i <= $num_msg; $i++) {
        for ($i = 1; $i <=701; $i++) {
            date_default_timezone_set('America/Chicago');   
            if(isset($_REQUEST['q'])){
                $q=$_REQUEST['q'];
                if($i>$q){
                    die('kill here');
                }

            }
            $header = imap_headerinfo($imap_stream, $i);
            //echo '<pre>';
            //print_r($header); die;
            $mailbox_uid = imap_uid($imap_stream, $i);
            $structure = imap_fetchstructure($imap_stream, $mailbox_uid, FT_UID);
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
                /* else{
                  $sample = iconv('utf-8', 'utf-8', $subject);
                  if (md5($sample)==md5($subject)){
                  $subject = utf8_decode($subject);
                  $is_UTF8 = true;
                  }
                  } */
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

            error_log(date("Y-m-d H:i:s") . '  FOUND  ' . $email_date . ' : ' . $subject);
            //print $email_date.'||'.$subject.'||'.$fromdb.'||'.$fromdb."\n";
            //print_r($structure);
            //$query = "SELECT SQL_NO_CACHE COUNT(*) FROM `cscan_email` WHERE `mailbox_uid`='".$DRW->real_escape_string($mailbox_uid)."' AND `email_date`='".$DRW->real_escape_string($email_date)."'";
            /* if($subject!=''){
              $query = "SELECT SQL_NO_CACHE COUNT(*) FROM `cscan_email` WHERE email_subject='".$DRW->real_escape_string(strtolower($subject))."'";
              }else{ */
            $query = "SELECT SQL_NO_CACHE COUNT(*) FROM `cscan_email` WHERE `mailbox_uid`='" . $DRW->real_escape_string($mailbox_uid) . "' AND `email_date`='" . $DRW->real_escape_string($email_date) . "'";
            //  }
            if ($defaultcontact != '')
                $query .= " AND `contact_type_m_c`='" . $DRW->real_escape_string($defaultcontact) . "'";
            $query_result = $DRW->query($query, $DRW_read);

            $count = $DRW->fetch_row($query_result);
            $DRW->free_result($query_result);

            $querytemp = "SELECT SQL_NO_CACHE COUNT(*) FROM `cscan_email_tempstore` WHERE `mailbox_uid`='" . $DRW->real_escape_string($mailbox_uid) . "' AND `email_date`='" . $DRW->real_escape_string($email_date) . "'";
            if ($defaultcontact != '')
                $querytemp .= " AND `contact_type_m_c`='" . $DRW->real_escape_string($defaultcontact) . "'";
            $query_result_tempstore = $DRW->query($querytemp, $DRW_read);
            $count_tempstore = $DRW->fetch_row($query_result_tempstore);
            if ($count[0] == 0 && $count_tempstore[0] == 0) {

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

                if ($keep) {
                    $contact_type_m_c = $defaultcontact;
                    if ($contact_type_m_c == 'prod_panelist') {
                        $e_assigned_admin_userID = getEAssignment();
                    } else {
                        $e_assigned_admin_userID = 0; //getEAssignment('2');
                    }
                    $result_c_p = $DRW->query("SELECT panelist_id,stateID FROM cscan_panelists WHERE active=1 AND (email='" . $DRW->real_escape_string($checkemail) . "' OR alt_email='" . $DRW->real_escape_string($checkemail) . "' OR more_email LIKE '%" . mysqlLike($checkemail) . "%') LIMIT 1", $DRW_read);
                    $data_c_p = $DRW->fetch_row($result_c_p);
                    $panelist_id = (int) $data_c_p[0];
                    $email_stateID = (int) $data_c_p[1];
                    $DRW->free_result($result_c_p);
                    $query = "INSERT INTO `cscan_email_tempstore` (e_assigned_admin_userID,`mailbox_uid`,`email_date`,`email_to`,`email_from`,`email_from_one`,`email_subject`,`contact_type_m_c`,`email_cc`,`email_bcc`,`deleted`,`panelist_id`,`email_stateID`)
						VALUES ($e_assigned_admin_userID,'" . $DRW->real_escape_string($mailbox_uid) . "','" . $DRW->real_escape_string($email_date) . "','" . $DRW->real_escape_string($todb) . "','" . $DRW->real_escape_string($fromdb) . "','" . $DRW->real_escape_string($checkemail) . "','" . $DRW->real_escape_string($subject) . "','" . $DRW->real_escape_string($contact_type_m_c) . "','" . $DRW->real_escape_string($ccdb) . "','" . $DRW->real_escape_string($bccdb) . "','" . $DRW->real_escape_string($messageType) . "'," . $panelist_id . "," . $email_stateID . ")";
                    $DRW->query($query, $DRW_main);

                    $muid = $DRW->insert_id($DRW_main);
                    error_log(date("Y-m-d H:i:s") . ' +ADD ' . $muid);

                    $part_array = array();
                    create_part_array($part_array, $structure);
                    //print_r($part_array);
                    //$query = "REPLACE INTO `cscan_email_serial` (`mailbox_uid`,`email_date`,`email_content`)
                    //	VALUES ('".$DRW->real_escape_string($mailbox_uid)."','".$DRW->real_escape_string($email_date)."','".$DRW->real_escape_string(serialize($part_array))."')";
                    //$DRW->query($query,$DRW_main);

                    $RFC822_part1 = '';
                    $RFC822_part2 = '';
                    $is_RFC822 = false;
                    $in_RFC822 = false;
                    $is_UTF8 = false;
                    $totalparts = count($part_array);
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

                            if ($bodydata != '') {
                                if ($type_no == 0 && ($subtype == 'plain' || $subtype == 'html')) {
                                    if ($encoding == 3)
                                        $bodydata = base64_decode($bodydata);
                                    elseif ($encoding == 4)
                                        $bodydata = quoted_printable_decode($bodydata);

                                    if ($charset == 'utf-8') {
                                        $bodydata = utf8_decode($bodydata);
                                       // $bodydata =imap_utf8($bodydata);
                                       // $is_UTF8 = true;
                                    }
                                     ######### latest encode message data #################
                                    //$bodydata= htmlspecialchars($bodydata, ENT_QUOTES | ENT_HTML5);
                                    // $query = "INSERT INTO `cscan_email_text` (`cetpart`,`cettext`,`cettype`,`muid`,`cetidentification`) VALUES ('" . $DRW->real_escape_string($parti) . "','" . $DRW->real_escape_string($bodydata) . "','" . $DRW->real_escape_string($type . '/' . $subtype) . "','" . $DRW->real_escape_string($muid) . "','" . $DRW->real_escape_string($id) . "')";
                                    $query = "INSERT INTO `cscan_email_text_tempstore` (`cetpart`,`cettext`,`cettype`,`muid`,`cetidentification`) VALUES ('" . $DRW->real_escape_string($parti) . "','" . $DRW->real_escape_string(utf8_encode($bodydata)) . "','" . $DRW->real_escape_string($type . '/' . $subtype) . "','" . $DRW->real_escape_string($muid) . "','" . $DRW->real_escape_string($id) . "')";
                                   //// $query = "INSERT INTO `cscan_email_text` (`cetpart`,`cettext`,`cettype`,`muid`,`cetidentification`) VALUES ('" . $DRW->real_escape_string($parti) . "','" . $DRW->real_escape_string($bodydata) . "','" . $DRW->real_escape_string($type . '/' . $subtype) . "','" . $DRW->real_escape_string($muid) . "','" . $DRW->real_escape_string($id) . "')";
                                   ######### end latest encode message data #################

                                    if($DRW->query($query, $DRW_main)){
                                      //extract name, email and date from bodydata
                                      if("{$type}/{$subtype}" == 'text/html'){
                                          $emailDetails = getFromText($bodydata, $muid, "{$type}/{$subtype}");

                                          if($emailDetails['name'] == null){
                              					  }else if(strlen($emailDetails['name']) > 70){
                              					    $emailDetails['name'] = null;
                              					  }
                              					  if($emailDetails['email'] == null){
                              					  }else if(!valid_email($emailDetails['email'])){
                              					    $emailDetails['email'] = null;
                              					  }
                                          if($emailDetails['date'] == null){
                              					  }else if(strlen($emailDetails['date']) > 76){
                              					    $emailDetails['date'] = null;
                              					  }

                                          if(!empty($emailDetails['name'])){
                              							$queryObj['from_sent_name'] = $DRW->real_escape_string($emailDetails['name']);
                              						}else{
                              							$queryObj['from_sent_name'] = null;
                              						}
                              						if(!empty($emailDetails['email'])){
                              							$queryObj['from_sent_email_address'] = $DRW->real_escape_string($emailDetails['email']);
                              						}else{
                              							$queryObj['from_sent_email_address'] = null;
                              						}
                              						if(!empty($emailDetails['email'])){
                              							$queryObj['from_sent_date'] = $DRW->real_escape_string($emailDetails['date']);
                              							$sentdate = strtotime($emailDetails['date']);
                              							$queryObj['from_sent_date_format'] = date('Y-m-d H:i:s', $sentdate);
                              						}else{
                              							$queryObj['from_sent_date'] = null;
                              						}
                              						$queryObj['is_text_file'] = 1;
                                          $queryObj['is_fetch'] = 1;
                              						$updateQuery = [];
                              						foreach ($queryObj as $dataKey => $dataValue) {
                              								$updateQuery[] ="{$dataKey}='{$dataValue}'";
                              						}
                                          $query_em = "UPDATE cscan_email_tempstore SET ".
                              										implode(', ', $updateQuery) .
                              										"	WHERE muid ='" . $muid . "'";
                                        }else{
                                          $query_em = "UPDATE `cscan_email_tempstore` SET is_text_file='1'  WHERE muid=$muid";
                                        }
                                        $DRW->query($query_em, $DRW_main);
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

                                    foreach ($efilesArray as $efile) {
                                        list($parti, $bodydata, $filename, $type, $subtype, $id, $disposition, $encoding) = $efile;

                                        $query = "INSERT INTO `cscan_email_file_tempstore` (`cefpart`,`cefname`,`ceftype`,`muid`,`cefidentification`,`cefdisposition`,`cefencoding`)
											VALUES ('" . $DRW->real_escape_string($parti) . "','" . $DRW->real_escape_string($filename) . "','" . $DRW->real_escape_string($type . '/' . $subtype) . "','" . $DRW->real_escape_string($muid) . "','" . $DRW->real_escape_string($id) . "','" . $DRW->real_escape_string($disposition) . "','" . $DRW->real_escape_string($encoding) . "')";
                                        $DRW->query($query, $DRW_main);
                                        $cefid = $DRW->insert_id($DRW_main);

                                        $query_em = "UPDATE `cscan_email_tempstore` SET is_text_file='1' WHERE muid=$muid";
                                        $DRW->query($query_em, $DRW_main);

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
                                            $query = "UPDATE `cscan_email_file_tempstore` SET cefpath='" . $DRW->real_escape_string('/contentFiles/' . $yearpath . $monthpath . $daypath . $cefid) . "' WHERE cefid=$cefid";
                                            $DRW->query($query, $DRW_main);
                                        }
                                    }
                                }
                            }
                        }
                    }
                    //$is_RFC822
                    //if($checkemail=='lwinslady@aol.com' || $is_UTF8) $checkbool = imap_mail_move($imap_stream, $mailbox_uid, $tempmailbox, CP_UID);
                    //else
                    ################## delete imap email ####################
                    if(siteMode()!='demo'){
                        imap_delete($imap_stream, $mailbox_uid, FT_UID);
                        imap_expunge($imap_stream);
                    }
                    ################## delete imap email ####################

                } else {
                    ################## delete imap email ####################
                    if(siteMode()!='demo'){
                        if (true !== imap_mail_move($imap_stream, $mailbox_uid, $movemailbox, CP_UID)) {
                            //$ehL->write("imap_mail_move(imap_stream (mailbox $mailbox, username $username), $mailbox_uid, $movemailbox, CP_UID) failed\n" . print_r(imap_errors(), true));
                            ++$nimap_errors;
                        }
                    }
                     ################## delete imap email ####################
                    error_log(date("Y-m-d H:i:s") . ' .NUH ' . $header->message_id);


                }
            } else {
                
               ################## delete imap email ####################
               if(siteMode()!='demo'){
                    echo $mailbox_uid;
                  
                  //$check = imap_mailboxmsginfo ($imap_stream);
                   //print "Messages before delete: " . $check->Nmsgs . "<br>\n" ; 
                    imap_delete($imap_stream, $mailbox_uid, FT_UID);
                    imap_expunge($imap_stream);
		  //imap_close($imap_stream);                    
//echo 'ppppppp'; die;
               }
                ################## delete imap email ####################
                //$ehL->write("imap_delete(imap_stream (mailbox $mailbox, username $username), $mailbox_uid, CP_UID) $email_date because of duplicate\n");
                error_log(date("Y-m-d H:i:s") . ' -DEL ' . $email_date . ' : ' . $subject);


            }
            if ($i % 100 == 0) {
             ################## delete imap email ####################
               if(siteMode()!='demo'){
                    imap_expunge($imap_stream);
                    error_log(date("Y-m-d H:i:s") . ' !PURGE ');
                }
               ################## delete imap email ####################
            }
        }
        ################## delete imap email ####################
            if(siteMode()!='demo'){
                if (imap_close($imap_stream, CL_EXPUNGE) !== TRUE) {
                    imap_expunge($imap_stream);
                    //$ehL->write("imap_close(imap_stream (mailbox $mailbox, username $username), CL_EXPUNGE) failed\n" . print_r(imap_errors(), true));
                    ++$nimap_errors;
                }else{
                   imap_expunge($imap_stream);
                }
            }
             ################## delete imap email ####################
    }
}
if ($nimap_errors) {
    //$ehL->write("$nimap_errors errors");
    error_log(date("Y-m-d H:i:s") . ' ? nimap_errors: ' . $nimap_errors);
}
//$ehL->stop(false);
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
############## for recursive call of imapback file ########################
//sleep(50);
//$cmd = "/usr/bin/php ".dirname(__FILE__)."/imap_back_consumer.php";
//        $sql = "INSERT IGNORE INTO imapback_scripts SET command = '".$cmd."'";
//        if($DRW->query($sql,$DRW_main)){
//            //$output = exec($cmd . " > /dev/null &");// do not wait
//           // $output = exec($cmd . " 2>&1", $output);//wait for the response
//            $output = exec($cmd);//wait for the response
//        }
?>