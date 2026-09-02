#!/usr/bin/php
<?php
// ini_set('display_startup_errors', 1);
// ini_set('display_errors', 1);
// error_reporting(-1);
define('CONTENT_COUNT', 20);
define('PAGE_URL', 'https://competiscan.com/');
$currentDayStartTime = date('Y-m-d H:i:s', strtotime('-1 day'));
require_once("includes/ehLog_set.php");
$ehL->start(__FILE__, true, E_ALL ^ E_DEPRECATED); //smtp.php Assigning the return value of new by reference is deprecated
//ini_set( "default_charset", "iso-8859-1" );
ini_set("default_charset", "utf-8");
require_once("includes/dbcon.php");
require_once("includes/functions.php");
require_once('Mail.php');
require_once('Mail/mime.php');
require_once('includes/sphinx_function2.php');  //sphinx functions.
require_once('includes/functions_latest2.php');  //latest function
ini_set("memory_limit", "-1");
set_time_limit(0);
require_once __DIR__ . '/vendor/autoload.php';
//$search = new \HS\Search($DRW, $DRW_read2);
//$user = new \HS\User($DRW, $DRW_read2);
$testing = false; // Set to true for test, will not send mail or update db.
//$tries = 3;
$tries = 0;
$send_success = 0;
$send_fail = 0;
$weekday = date('w'); //0 (for Sunday) through 6 (for Saturday)
$day = (int) date('j');
//$email_list = $user->getEmailList();
//echo '<pre>';print_r($email_list);die;
$notificationList = getNotificationListData();
$contactTypes = array(0 =>'', 1 => 'prod_panelist', 2 => 'cons_panelist', 3 => 'brok_panelist', 4 => 'prov_panelist');//,'member'=>'Competiscan Members'
// echo '<pre>';
// print_r($notificationList);
// echo count($notificationList);
// die;

/*
  $params["host"] - The server to connect. Default is localhost.
  $params["port"] - The port to connect. Default is 25.
  $params["auth"] - Whether or not to use SMTP authentication. Default is FALSE.
  $params["username"] - The username to use for SMTP authentication.
  $params["password"] - The password to use for SMTP authentication.
  $params["localhost"] - The value to give when sending EHLO or HELO. Default is localhost
  $params["timeout"] - The SMTP connection timeout. Default is NULL (no timeout).
  $params["verp"] - Whether to use VERP or not. Default is FALSE.
  $params["debug"] - Whether to enable SMTP debug mode or not. Default is FALSE.
  $params["persist"] - Indicates whether or not the SMTP connection should persist over multiple calls to the send() method.
 */
$params = array(
    'username' => '',
    'password' => '',
    'persist' => true,
);
$mail = & Mail::factory('smtp', $params);
$crlf = "\n"; //if you want to send the generated MIME message using Mail  then you have to set $crlf to "\n"
foreach ($notificationList as $row) {
    //changes by pradeep
    //echo $row['userID'];
    //print_r($row);
    //exit;
    // if($row['userID']=='31464'){
    if ($row['notify'] == 'weekly' && $row['weekday'] < 7 && $weekday != $row['weekday']) {
        continue;
    } elseif ($row['notify'] == 'monthly' && $row['weekday'] < 7 && ($weekday != $row['weekday'] || $day > 7)) {
        continue;
    }

    if (!isset($row['userID'])) {
        continue;
    }

    $emailAddress = $row['user_email'];
    // echo $emailAddress.'=='.$is_public_user;
    //exit;
    if ($row['lastSentDate'] != '' && $row['lastSentDate'] != '0000-00-00 00:00:00') {
        $usedate = $row['lastSentDate'];
    } else {
        $usedate = $row['createdAt'];
    }

    $weekago = date('Y-m-d', strtotime('-7 days')) . ' 00:00:00';

    if ($usedate < $weekago) {
        $usedate = $weekago;
    }

    if ($row['sendTo'] != '') {
        $to = $row['sendTo'];
    } else {
        $to = $emailAddress;
    }

    //$to='arvind.chaurasia@newmediaguru.org';
    //$to='pradeep.newmediaguru@gmail.com';
    //$to='pradeep.kumar@nmgtechnologies.com';
    $tracking = true;
    $message = '';
    $subject = "E-mail alert from Competiscan: New products added for your search name - " . $row['searchName'];
    $mail_format = $row['mailFormat'];
    $message = '<html><body>
    <table width="100%" align="center" cellspacing="1" cellpadding="4" border="1" rules="rows" style="border-collapse:collapse" bordercolor="#4892F7" style="FONT-FAMILY: verdana, Arial, Helvetica, sans-serif;FONT-SIZE: 10pt;COLOR: #000000;TEXT-DECORATION: none;">
    <tr><td width="100%" align="center">The following is the criteria of your search : ' . $row['searchName'] . '</td></tr>
    <tr><td width="100%" align="center">';
    $tableText = getEmailContent($row['ID']);
    if(!empty(trim($tableText))){
      $text_message = '';
      $sn = 1;
      $message .= $tableText;
      $tracking_id = '';
      if ($tracking) {
          $sqlIfFound = "SELECT * FROM cscan_email_content_search_track WHERE searchID='".$DRW->real_escape_string($row['ID'])."'";
          $ifFoundResource = $DRW->query($sqlIfFound, $DRW_main);
          if($ifFoundResource->num_rows == 0){
            $sql_insert_tarcking = "INSERT INTO cscan_email_content_search_track SET user_id = '" . $DRW->real_escape_string($row['userID']) . "', alert_name = '" . $DRW->real_escape_string($row['searchName']) . "', insert_date = '" . date('Y-m-d H:i:s') . "',searchID='".$DRW->real_escape_string($row['ID'])."'";
            $DRW->query($sql_insert_tarcking, $DRW_main);
            $tracking_id = $DRW->insert_id($DRW_main);
            $tracker = '<img src="https://www.competiscan.com/content_search_tracker.php?trmsg=' . $tracking_id . '" alt=" " width="1px" height="1px">';
            $tracking = false;
          }
      }
      // if (!empty($tracker)) {
      //     $message .= '<tr><td align="right">'.$tracker.'</td></tr>';
      // }
      $message .= '</td></tr></table>';
      $message .= '</body></html>>';

      /////////////////////////////////////////////////////
      // echo $message;
      // $headers = "MIME-Version: 1.0" . "\r\n";
      // $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
      // // Additional headers
      // $headers .= 'From: Competiscan<richard@competiscan.com>' . "\r\n";
      // mail($to, $subject, $message, $headers);
      //////////////////////////////////////////////////////

      $hdrs = array('From' => "\"Competiscan\" <richard@competiscan.com>", 'To' => $to, 'Subject' => $subject);
      $mime = new Mail_mime($crlf);

      if ($mail_format == "HTML") {
          $mime->setHTMLBody($message);
          $body = $mime->get();
      } else {
          $mime->setTXTBody($text_message);
          $body = $mime->get();
      }

      $headers = $mime->headers($hdrs);

      if ($testing) {
          $ehL->write("send(to=$to, subject=$subject)");
          continue;
      }

      $try = 0;
      $sent = false;

      do {
          // check if already sent
          $user_id = $row['userID'];
          $searchID= $row['ID'];
          $alert_name = $row['searchName'];
          $insert_date = date('Y-m-d');
          $sql_chk = "SELECT alert_name FROM cscan_email_content_search_track WHERE is_send=1 AND user_id='" . $user_id . "' AND LOWER(alert_name)='" . strtolower($alert_name) . "' AND DATE_FORMAT(insert_date, '%Y-%m-%d') ='" . $insert_date . "' AND searchID='".$searchID."'";
          $query_chk = $DRW->query($sql_chk, $DRW_read2);

          if ($DRW->num_rows($query_chk) == 0) {
              $send = $mail->send($to, $headers, $body);

              if (PEAR::isError($send)) {
                  $ehL->write("send(to=$to, subject=$subject)\n" . $send->getMessage());
                  $mail->disconnect();
                  $mail = & Mail::factory('smtp', $params);
              } else {
                  $sent = true;
                  $setdate = date("Y-m-d H:i:s");
                  $query = "UPDATE cscan_content_site_search SET lastSentDate='$setdate' WHERE ID='" . $row['ID'] . "'";
                  $DRW->query($query, $DRW_main);
                  ######################
                  if (!empty($tracking_id)) {
                      $sql_update_tarcking = "UPDATE cscan_email_content_search_track SET is_send = 1 WHERE id = '" . $tracking_id . "'";
                      $DRW->query($sql_update_tarcking, $DRW_main);
                  }
                  ##########################
              }

              if (!$sent) {
                  sleep(10);
              }
          }
          $try++;
      } while (!$sent && $try < $tries);

      if ($sent) {
          ++$send_success;
      } else {
          ++$send_fail;
      }
    }
    //echo"hello";exit;
    @$DRW->free_result($result_product);
    //}
}
@$DRW->free_result($result);
$ehL->write("Search: $send_success emails sent, $send_fail emails failed.");


function getNotificationListData()
{
    global $DRW, $DRW_read2, $DRW_main, $DRW_crm;
    $notifyList = array();

    $query = "SELECT SQL_NO_CACHE DISTINCT ID, searchName, s.userID, notify,
            lastSentDate, createdAt, sendTo, mailFormat, weekday, u.user_email
            FROM cscan_content_site_search as s
            JOIN cscan_admin_users as u on (u.userID=s.userID AND u.user_status=1)
            WHERE emailAlert='1' AND searchSave=1 AND
                ((notify='daily' AND LEFT(lastSentDate,10)<=DATE_SUB(CURDATE(),INTERVAL 1 DAY)) OR
                (notify='weekly' AND LEFT(lastSentDate,10)<=DATE_SUB(CURDATE(),INTERVAL 7 DAY)) OR
                (notify='monthly' AND LEFT(lastSentDate,10)<=DATE_SUB(CURDATE(),INTERVAL 1 MONTH))) ";

    $result = $DRW->query($query, $DRW_read2);

    while ($row = $DRW->fetch_assoc($result)) {
        $notifyList[] = $row;
    }

    return $notifyList;
}
function getEmailContent($searchId)
{
    global $DRW, $DRW_read2, $DRW_main, $DRW_crm, $contactTypes, $currentDayStartTime, $SPHINX_name;
    $join = '';
    $mtypes = array(0,1);
    $noread = 0;
    $panelist_core_options = array('C'=>'C','EN'=>'EN','N'=>'N','RL'=>'RL','TC'=>'TC','TL'=>'TL');
    $userWiseEmails = [];
    $hiddentext = '';
    $trTable = '';
    $cType = 0;
    $messageTypes = array(0=>'',1=>'Unused',2=>'Used',3=>'Junk',4=>'Copy');
    $readTypes = array(0=>'Unread',1=>'Read');
    $searchResorce = $DRW->query("SELECT * FROM cscan_content_site_search s WHERE s.ID={$searchId};", $DRW_read2);
    $used = '';
    if (!empty($searchResorce) && $searchResorce->num_rows) {
          $userLastSearch = $DRW->fetch_assoc($searchResorce, $DRW_read2);
          //echo '<pre>';print_r($userLastSearch);die;
          $userId = $userLastSearch['userID'];
          $searchid = $userLastSearch['CUID'];//CUID
          $searchsubj = $userLastSearch['Subject'];//Subject
          $searchsender = $userLastSearch['SenderEmail'];//SenderEmail
          $searchbody = $userLastSearch['Body'];//Body
          $searchtext = $userLastSearch['searchKey'];//searchKey
          $searchstate = $userLastSearch['StateProvince'];//StateProvince
          $searchcountry = $userLastSearch['SelectionCountry'];//SelectionCountry
          $panelist_ids = $userLastSearch['Panelists'];//Panelists
          $searchownbiz = $userLastSearch['Owner'];//Owner
          $mtypes =  explode(';', $userLastSearch['pTypes']);//pTypes
          $noread = $userLastSearch['HideMarkedRead'];//HideMarkedRead
          $panelist_core_search = explode(',', $userLastSearch['Flag']);//Flag
          $start_m = $userLastSearch['FromDateM'];//FromDateM
          $start_d = $userLastSearch['FromDateD'];//FromDateD
          $start_y = $userLastSearch['FromDateY'];//FromDateY
          $end_m = $userLastSearch['ToDateM'];//ToDateM
          $end_d = $userLastSearch['ToDateD'];//ToDateD
          $end_y = $userLastSearch['ToDateY'];//ToDateY
          $hy = $userLastSearch['searchPartition'];//searchPartition
          $cType = $userLastSearch['cType'];
          if ($userLastSearch['cType']==1) {
              $panelist_core_options = array('ID'=>'ID','PT'=>'PT','PN'=>'PN');
          }

            if ($searchtext!='') {
                $searchtext_like = mysqlLike($searchtext);
                $swhere = '';
                if ($searchid) {
                    if ($swhere!='') {
                        $swhere .= ' OR ';
                    }
                    $swhere .= "ce.`muid`='".$DRW->real_escape_string($searchtext)."'";
                }

                if ($searchsender) {
                    $vs = explode(',', $searchtext);
                    $ors = array();
                    foreach ($vs as $v) {
                        $v = trim($v);
                        if (!empty($v)) {
                            if ($swhere!='') {
                                $swhere .= ' OR ';
                            }
                            $v = mysqlLike($v);
                            $swhere .= "`email_from_one` LIKE '".$v."%'";
                        }
                    }
                }
                if ($searchbody || $searchsubj) {
                    $muids = [];
                    if (!empty($SPHINX_name)) {
                        $s = startSphinx('cetactive');
                        if (!empty($hy)) {
                            $inds = 'base_index_'.$SPHINX_name.'_e'.$hy;
                        } else {
                            $inds = 'base_index_'.$SPHINX_name.'_e,delta_index_'.$SPHINX_name.'_e';
                        }
                        $ps = parseSphinx($s, $searchtext);
                        if (trim($ps)!='') {
                            $currcount = 0;
                            $step = $total = 20000;
                            $s->setLimits(0, 1, 1);
                            if ($searchbody && !$searchsubj) {
                                $ps = '@cettext '.$ps;
                            }
                            if (!$searchbody && $searchsubj) {
                                $ps = '@email_subject '.$ps;
                            }
                            $result = $s->query($ps, $inds);
                            // $result['total_found'];
                            // echo $result['total_found'].'total result';exit;
                            if (isset($result['matches'])) {
                                $total = (float)$result['total_found'];
                                $count = 0;
                                $minID = 0;
                                $count_save_sql = "SELECT MAX(cetid) FROM cscan_email_text$hy";
                                $rs = $DRW->query($count_save_sql, $DRW_read2);
                                $data = $DRW->fetch_row($rs);
                                $maxID = $data[0];
                                for ($offset=0;$offset<=$maxID;$offset+=$step) {
                                    $s = startSphinx('cetactive');
                                    $s->setLimits(0, $step, $step);
                                    $s->setIDRange($minID+1, $maxID);
                                    $result = $s->query($ps, $inds);
                                    //echo"<pre>";
                                    //print_r($result['matches']);exit;
                                    if (isset($result['matches'])) {
                                        foreach ($result['matches'] as $dts_id=>$match) {
                                            $muids[] = $match['attrs']['muid'];
                                            $minID = $dts_id;
                                            $currcount++;
                                        }
                                        if ($currcount>=$total) {
                                            break;
                                        }
                                    }
                                    $err = $s->getLastError();
                                    $war = $s->getLastWarning();

                                    if (!empty($err) || !empty($war)) {
                                        break;
                                    }
                                    // note that total_found using setLimits(0,1,1) is not always the same as without limits (bug in Sphinx?)
                                    if (!isset($result['matches'])) {
                                        break;
                                    }
                                }
                            }
                        }
                    }
                    $muids = array_unique($muids);
                    if(count($muids) == 0) $muids = [0];
                    $cwhere .= " AND ce.muid IN ('".implode("','", $muids)."')";
                }
                if ($swhere!='') {
                    $cwhere .= " AND ($swhere)";
                }
            }
            if ($noread) {
                $used .= ' AND `email_read`<>1 ';
            }
            if($cType != 0) $cwhere .= " AND `contact_type_m_c`='{$contactTypes[$cType]}' ";
            if (!empty($searchcountry)) {
                $sqlc = "SELECT stateID FROM cscan_state WHERE countryCode='".$DRW->real_escape_string($searchcountry)."'";
                $rsc = $DRW->query($sqlc, $DRW_read2);
                ########### convert or clause itno in clause #############
                $stateArray =   array();
                while ($rowc = $DRW->fetch_row($rsc)) {
                    ########### convert or clause itno in clause #############
                    $stateArray[]   =   $rowc[0];
                }
                ########### convert or clause itno in clause #############
                if (!empty($stateArray)) {
                    $cwhere .=" AND ce.email_stateID in( ". implode(",", $stateArray). ")";
                }

            }
            if ($searchstate!=0) {
                $cwhere .= " AND ce.email_stateID=$searchstate";
            }

            if ($searchownbiz!=-1 || !empty($panelist_ids)) {
                $join .= ' JOIN cscan_panelists pp ON(ce.panelist_id=pp.panelist_id)';
                if ($searchownbiz!=-1) {
                    $cwhere .= " AND ownbiz=$searchownbiz";
                }
                if (!empty($panelist_ids)) {
                    $vs = explode(',', $panelist_ids);
                    $ors = array();
                    foreach ($vs as $v) {
                        $v = trim($v);
                        if (!empty($v)) {
                            $v = $DRW->real_escape_string($v);
                            $ors[] = "(competi_id LIKE '".$v."%')";
                        }
                    }
                    $cwhere .= " AND (".implode(' OR ', $ors).")";
                }
            }
            $cwhere .= " AND `email_date`>'".$currentDayStartTime."' ";
            if (count($panelist_core_search)>0) {
                $cwhere .= " AND (panelist_core='".implode("' OR panelist_core='", $panelist_core_search)."')";
            }
            if ($hy=='') {
                $cwhere .= ' AND is_text_file=1 ';
            }
            $q = " FROM `cscan_email$hy` ce$join WHERE 1=1$used$cwhere ORDER BY `email_date` DESC";
            // echo '---------';
            // echo "SELECT COUNT(DISTINCT ce.`muid`) $q";
            // echo '---------';
          // exit;
          // echo '---------';
          //echo $query = "SELECT DISTINCT ce.`muid`,DATE_FORMAT(`email_date`,'%m/%d/%y<br />%l:%i %p'),`email_to`,`email_from`,`email_subject`,`contact_type_m_c`,`deleted`,`panelist_score`,`email_read`,`email_from_one`,ce.`panelist_id`,panelist_core $q$orderby$limittext";// SQL_NO_CACHE
          // echo '---------';
          $count_result = $DRW->query("SELECT COUNT(DISTINCT ce.`muid`) $q", $DRW_read2);// SQL_NO_CACHE
          $data = $DRW->fetch_row($count_result);
            $rows = $data[0];

            if ($rows>0) {
                if ($rows > CONTENT_COUNT) {
                    $limittext = ' limit 0,'.CONTENT_COUNT;
                }
                $query = "SELECT DISTINCT ce.`muid`,DATE_FORMAT(`email_date`,'%m/%d/%y<br />%l:%i %p'),`email_to`,`email_from`,`email_subject`,`contact_type_m_c`,`deleted`,`panelist_score`,`email_read`,`email_from_one`,ce.`panelist_id`,panelist_core $q$orderby$limittext";// SQL_NO_CACHE
                $query_result = $DRW->query($query, $DRW_read2);
                $trTable = '<table width="90%" align="center" cellspacing="1" cellpadding="4" border="1" rules="1" style="border-collapse:collapse" style="FONT-FAMILY: verdana, Arial, Helvetica, sans-serif;FONT-SIZE: 10pt;COLOR: #000000;TEXT-DECORATION: none;" bordercolor="4892F7">';
                $trTable .= '<tr style="FONT-FAMILY: Arial, Helvetica, sans-serif;border: 1px #E3E3E3 solid;background-color: #6699FF;font-size: 12pt;COLOR: #FFFFFF;text-align:center;line-height: 14px;padding-bottom: 1px;padding-left: 5px;padding-right: 5px;padding-top: 2px;">';
                $trTable .= '<td>Sr.No.</td>';
                $trTable .= '<td>ID</td>';
                $trTable .= '<td>Subject</td>';
                $trTable .= '<td>Sender Email</td>';
                $trTable .= '<td>Date</td>';
                $trTable .= '</tr>';
                $srNo = 1;
                while ($rData = $DRW->fetch_array($query_result)) {
                    //print_r($data);die;
                    $muid = $rData[0];
                    $query2 = $DRW->query("SELECT COUNT(*) from cscan_email_text$hy WHERE muid='".$DRW->real_escape_string($muid)."'", $DRW_read2);
                    $data2 = $DRW->fetch_row($query2);
                    $found_count = $data2[0];
                    $query3 = $DRW->query("SELECT COUNT(*) from cscan_email_file$hy WHERE muid='".$DRW->real_escape_string($muid)."'", $DRW_read2);
                    $data3 = $DRW->fetch_row($query3);
                    $found_count2 = $data3[0];
                    if ($found_count>0 || $found_count2>0 || $hy==201501) {
                        /* Changes by Pradeep  End */
                        $email_date = $rData[1];
                        $email_to = $rData[2];
                        $email_from = $rData[3];
                        $email_subject = $rData[4];
                        $contact_type_m_c = $rData[5];
                        $deleted = $rData[6];
                        $panelist_score = $rData[7];
                        $email_read = $rData[8];
                        $email_from_one = $rData[9];
                        $panelist_id = $rData[10];
                        $panelist_core = $rData[11];

                        $result = $DRW->query("SELECT SQL_NO_CACHE COUNT(*) FROM cscan_email_file$hy WHERE muid=$muid", $DRW_read2);
                        $data2 = $DRW->fetch_row($result);
                        $count = $data2[0];

                        $result = $DRW->query("SELECT SQL_NO_CACHE COUNT(*) FROM cscan_product_email WHERE muid=$muid AND isTmp=0", $DRW_read2);
                        $data2 = $DRW->fetch_row($result);
                        $countp = $data2[0];

                        if ($count>0) {
                            $attachment = 'Yes';
                        } else {
                            $attachment = 'No';
                        }
                        if ($panelist_id!=0) {
                            $result = $DRW->query("SELECT sugar_id,first_name,last_name,competi_id,email,alt_email,panelist_id FROM cscan_panelists WHERE panelist_id=$panelist_id", $DRW_read2);
                            $data2 = $DRW->fetch_row($result);
                            if ($data2[0]!='') {
                                $id = $data2[0];
                                $first_name = $data2[1];
                                $last_name = $data2[2];
                                $competi_id = $data2[3];
                                $email1 = trim($data2[4]);
                                $email2 = trim($data2[5]);

                                $email_from = $first_name.' '.$last_name;
                                if ($competi_id!='') {
                                    $email_from .= ' ('.$competi_id.')';
                                }
                                $email1 = "{$email1}";
                                $email_from .= ' &lt;'.$email1.'&gt;';
                            } else {
                                $email_from = htmlspecialchars($email_from);
                            }
                        } else {
                            $email_from = htmlspecialchars($email_from);
                        }
                        $idLink = '<a href="'.PAGE_URL.'email.php?muid='.$muid.'&amp;hy='.$hy.'"
                                      class="bluelink"
                                      name="muid'.$muid.'">'.htmlspecialchars($muid).'</a>';
                        $subjectLink = '<a href="'.PAGE_URL.'email.php?muid='.$muid.'&amp;hy='.$hy.'" class="bluelink" name="muid'.$muid.'">'.
                          htmlspecialchars($email_subject).'</a>';

                        $trTable .= '<tr><td align="center" valign="top" style="border:inset #4892F7 1.0pt;">';
                        $trTable .= $srNo++;
                        $trTable .= '</td>';
                        $trTable .= '<td valign="top" style="border:inset #4892F7 1.0pt;">'.$idLink.'</td>';

                        $trTable .= '<td valign="top" style="border:inset #4892F7 1.0pt;">';
                        if (!$email_read) {
                            $trTable .= '<strong>';
                        }
                        $trTable .= $subjectLink;
                        if (!$email_read) {
                            $trTable .= '</strong>';
                        }
                        $trTable .= '</td><td valign="top" style="border:inset #4892F7 1.0pt;">'.$email_from.'</td>';

                        $trTable .= '<td valign="top" style="border:inset #4892F7 1.0pt;">'.$email_date.'</td>';
                        $trTable .= '</tr>';
                    }
                }

                if ($rows > CONTENT_COUNT) {
                    $trTable .= '<tr><td colspan="5" valign="top" style="border:inset #4892F7 1.0pt;text-align:right;font-weight:bold;">
                    <a href="'.PAGE_URL.'imap.php?rid='.$searchId.'&page=0&ctype='.$cType.'">View All</a></td></tr>';
                }
                $trTable .= '</table>';
            }
        return $trTable;
    }
}


?>
