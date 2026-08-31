#!/usr/bin/php
<?php
require_once("includes/ehLog_set.php");
$ehL->start(__FILE__, true, E_ALL ^ E_DEPRECATED); //smtp.php Assigning the return value of new by reference is deprecated
//ini_set( "default_charset", "iso-8859-1" );
ini_set("default_charset", "utf-8");
require_once("includes/dbcon.php");
require_once("includes/functions.php");
require_once('Mail.php');
require_once('Mail/mime.php');
require_once('includes/functions_latest2_csv2.php');  //latest function
ini_set("memory_limit", "-1");
set_time_limit(0);
require_once __DIR__ . '/vendor/autoload.php';
//$search = new \HS\Search($DRW, $DRW_read2);
$user = new \HS\User($DRW, $DRW_read2);
$testing = false; // Set to true for test, will not send mail or update db.
//$tries = 3;
$tries = 0;
$send_success = 0;
$send_fail = 0;
$weekday = date('w'); //0 (for Sunday) through 6 (for Saturday)
$day = (int) date('j');
$email_list = $user->getEmailList();
$notificationList = getNotificationListData();

/*echo '<pre>';
print_r($email_list)."<br/>";
print_r($notificationList);
echo count($notificationList);
die;*/
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
    //echo $row['userID'];
    //print_r($row); 
    //exit;
  // if($row['userID']=='31464'){
    if ($row['notify'] == 'weekly' && $row['weekday'] < 7 && $weekday != $row['weekday']) {
        continue;
    } elseif ($row['notify'] == 'monthly' && $row['weekday'] < 7 && ($weekday != $row['weekday'] || $day > 7)) {
        continue;
    }

   if (!isset($row['userID']))
        continue;

    $emailAddress = $row['emailAddress'];
    $is_public_user = $row['is_public_user'];
    $added_mchannelId=$row['mChannelID'];
    $is_directmail_channel=false;
    $is_other_channel=false;
    $alert_user_id=$row['userID'];
    $mchannelid=array();
    if(strstr($added_mchannelId,',')){
        $mchannelid=explode(',',$added_mchannelId);
    }else{
        $mchannelid[]=$added_mchannelId;
    }
    $mchannelid=array_filter($mchannelid);
    if(empty($mchannelid)){
        $mchannelid=array();
        $result_mc = $DRW->query("SELECT mu.mChannelID FROM cscan_mc_users_allow mu,cscan_mchannel mc WHERE userID=$alert_user_id AND mu.mChannelID=mc.mChannelID", $DRW_read);
        while ($data_mc = $DRW->fetch_row($result_mc)) {
            $mchannelid[] = $data_mc[0];
        }
    }
    $mchannelid=array_filter($mchannelid);
    if(in_array(1,$mchannelid)){
        if(count($mchannelid)>1){
          $is_other_channel=true;            
        }        
        $is_directmail_channel=true;
        if(($key = array_search(1,$mchannelid)) !== false) {
            unset($mchannelid[$key]);
        }
        $mchannelid= array_values($mchannelid);            
    }
    
    
    
   // echo $emailAddress.'=='.$is_public_user;
    //exit;
    if ($row['lastSentDate'] != '' && $row['lastSentDate'] != '0000-00-00 00:00:00') {
        $usedate = $row['lastSentDate'];
    } else {
        $usedate = $row['queryDate'];
    }

    $weekago = date('Y-m-d', strtotime('-7 days')) . ' 00:00:00';

    if ($usedate < $weekago) {
        $usedate = $weekago;
    }

    // list($query) = doQuery($row['ID'],false,$usedate,false,-1,false,false,false,true,0,array(),$row['userID']);
    //list($query) = doQuerytestsphinx($row['ID'],false,$usedate,false,-1,false,false,false,true,0,array(),$row['userID']);
    list($query) = doQuery_latest_csv2($row['ID'], false, $usedate, false, -1, false, false, false, true, 0, array(), $row['userID']);
    //echo $query."<br/>"; 
    //$query . ' ORDER BY company ASC, productHeadline ASC LIMIT 0,5000';
    if($is_directmail_channel){
        //echo $row['ID'];
        $query_second='';
        $query_arr=array();
        $query2='';
        //echo $query;
        ///echo '<br><br>ppppppp<br><br><br>\n\n';
        $query_arr=explode('AND mChannelID in',$query);        
        $query1=$query_arr[0];
        if(!empty($query_arr[1])){
            $query2=$query_arr[1];
            $query2=explode('AND',$query2);
            array_shift($query2);
            $query2 = join(' AND ',$query2);
        }else{
            $query2='';
        }      
       
        if(strstr($query1,'addedToDatabase>=')){
           $query2_arr=array(); 
           $query2_arr=explode('addedToDatabase>=',$query1);
           $query_first_1=$query2_arr[0];
           
           $query_first_2=substr($query2_arr[1],21); 
           $approveddate_condition=" AND approved_date>='".$usedate."' ";
           if(trim($query2)!=''){
               $query_first=$query_first_1.' LEFT(entryID,10)>= DATE_SUB(CURDATE(), INTERVAL 45 DAY) '.$approveddate_condition.' AND mChannelID in (1) '.$query_first_2.' AND '.$query2;
           }else{
               $query_first=$query_first_1.' LEFT(entryID,10)>= DATE_SUB(CURDATE(), INTERVAL 45 DAY) '.$approveddate_condition.' AND mChannelID in (1) '.$query_first_2.' ';
           }
           
        }
        if($is_other_channel){
            if(trim($query2)!=''){
                $query_second=$query1.' AND mChannelID in ('.implode(',',$mchannelid).') AND '.$query2;
            }else{
                $query_second=$query1.' AND mChannelID in ('.implode(',',$mchannelid).') ';
            }
           $query_second='UNION ('.$query_second.') ';
        }        
        $query=$query_first.' '.$query_second;       
               
    }
    
    
    
    //echo $query . ' ORDER BY company ASC, productHeadline ASC LIMIT 0,5000'; die;
    $setdate = date("Y-m-d H:i:s");
    $result_product = $DRW->query($query . ' ORDER BY company ASC, productHeadline ASC LIMIT 0,5000', $DRW_read2);

    if (!$result_product) {
        $ehL->write($query . ' ORDER BY company ASC, productHeadline ASC LIMIT 0,5000: ' . $DRW->error());
        continue;
    }
    /*print_r($result_product);
    $DRW->num_rows($result_product);
    exit;*/
    if ($DRW->num_rows($result_product) > 0) {
        if ($row['sendTo'] != '') {
            $to = $row['sendTo'];
        } else {
            $to = $emailAddress;
        }
      //echo $to;
       // exit;
        //$to='devendra.tiwari@nmgtechnologies.com,gokul.singh@nmgtechnologies.com,manas@nmgtechnologies.com,gaurav.yadav@nmgtechnologies.com,shine.jayakumar@nmgtechnologies.com';
        $message = '';
        $subject = "E-mail alert from Competiscan: New products added for your search name - " . $row['searchName'];
        $mail_format = $row['mail_format'];
        $message = '<html><body>
        <table width="100%" align="center" cellspacing="1" cellpadding="4" border="1" rules="rows" style="border-collapse:collapse" bordercolor="#4892F7" style="FONT-FAMILY: verdana, Arial, Helvetica, sans-serif;FONT-SIZE: 10pt;COLOR: #000000;TEXT-DECORATION: none;">
        <tr><td width="100%" align="center">The following products have been added according to the criteria of your search : ' . $row['searchName'] . '</td></tr>
        <tr><td width="100%" align="center">';
        $message .= '<table width="90%" align="center" cellspacing="1" cellpadding="4" border="1" rules="1" style="border-collapse:collapse" style="FONT-FAMILY: verdana, Arial, Helvetica, sans-serif;FONT-SIZE: 10pt;COLOR: #000000;TEXT-DECORATION: none;" bordercolor="4892F7">
        <tr style="FONT-FAMILY: Arial, Helvetica, sans-serif;border: 1px #E3E3E3 solid;background-color: #6699FF;font-size: 12pt;COLOR: #FFFFFF;text-align:center;line-height: 14px;padding-bottom: 1px;padding-left: 5px;padding-right: 5px;padding-top: 2px;">
        <th width="4%">Alert</th>
        <th width="31%" align="left">Company</th>
        <th width="50%" align="left">Headline</th>
        <th width="15%" align="left">Media Channel</th>
        </tr>';

        $text_message = '';
        $sn = 1;
        $checkarray = array();
        $tracking = true;
        $tracking_id = '';
        while ($row_product = $DRW->fetch_array($result_product)) {
            $variant_html = '';
            $variant_text = '';
            $row_product['productHeadline'] = mb_convert_encoding(html_entity_decode(str_replace("\xE2\x80\x8B", "", $row_product['productHeadline']), ENT_QUOTES, "UTF-8"), "HTML-ENTITIES", "UTF-8");

            if (trim($row_product['productHeadline']) != '') {
                if (!in_array($row_product['theproductID'], $checkarray)) {
                    if ($row_product['variantID'] != 0) {
                        $checkV = "SELECT entryID FROM cscan_product_detail WHERE productID='" . $DRW->real_escape_string($row_product['variantID']) . "' AND productStatus=1";
                        $checkV = $DRW->query($checkV, $DRW_read2);
                        $vcount = $DRW->num_rows($checkV);

                        if ($vcount > 0) {
                            $variant_html = '<br /><em style="font-size:10pt;">(Variant)</em>';
                            $variant_text = " (Variant)";
                        }

                        @$DRW->free_result($checkV);
                    }
                    ########## Start: Insert Into cscan_email_track_csv2 ############
                    if ($tracking) {
                        $rs_company = $DRW->query("SELECT companyName FROM cscan_users WHERE userID='" . $row['userID'] . "'", $DRW_read2);
                        $rs_comp = $DRW->fetch_array($rs_company);
                        $company_name = trim($rs_comp['companyName']);
                        $sql_insert_tarcking = "INSERT INTO cscan_email_track SET user_id = '" . $DRW->real_escape_string($row['userID']) . "', company_name = '" . $DRW->real_escape_string($company_name) . "', alert_name = '" . $DRW->real_escape_string($row['searchName']) . "', insert_date = '" . date('Y-m-d H:i:s') . "',searchID='".$DRW->real_escape_string($row['ID'])."'";
                        //echo $sql_insert_tarcking;die;
                        $DRW->query($sql_insert_tarcking, $DRW_main);
                        $tracking_id = $DRW->insert_id($DRW_main);
                        $tracker = '<img src="https://www.competiscan.com/tracker.php?trmsg=' . $tracking_id . '" alt=" " width="1px" height="1px">';
                        $tracking = false;
                    }
                    if (empty($tracking_id)) {
                        //send an info email to us if error comes
                        $error_message = $sql_insert_tarcking;
                        $error_to = 'devendra.tiwari@nmgtechnologies.com';
                        $error_subject = 'some error email-alert'; 
                    }

                    ########### for track the mchannel name by id ################
                    $rs_mChannelID = $DRW->query("SELECT mChannelName FROM cscan_mchannel WHERE mChannelID='" . $row_product['mChannelID'] . "'", $DRW_read2);
                    $rs_mChannel = $DRW->fetch_array($rs_mChannelID);
                    $mChannelName = trim($rs_mChannel['mChannelName']);

                    ########### for track the mchannel name by id ################

                    ########### End: Insert Into cscan_email _track #############
                    if ($row['is_public'] || $is_public_user) {
                        if (in_array($row['mChannelID'], array(5, 7))) {
                            $did = '&did=2';
                        } else {
                            $did = '&did=1';
                        }
                        $link = 'https://www.competiscan.com/productDocuments.php?id=' . $row_product['theproductID'] . $did . '&trmsg=' . $tracking_id;
                    } else {
                        //$link = 'https://www.competiscan.com/index.php?product=' . $row_product['theproductID'] . '&trmsg=' . $tracking_id;
                        $link = 'https://cp.competiscan.com/productdetail?id='. $row_product['theproductID'];
                    }

                    $message .= '<tr>
            <td align="center" valign="top" style="border:inset #4892F7 1.0pt;">' . $sn . '.</td>
            <td valign="top" style="border:inset #4892F7 1.0pt;">' . htmlspecialchars($row_product['company']) . $variant_html . '</td>
            <td valign="top" style="border:inset #4892F7 1.0pt;"><a href="' . $link . '" target="_new" title="Click here to view product details on Competiscan">' . ($row_product['productHeadline']) . '</a></td>
            <td valign="top" style="border:inset #4892F7 1.0pt;">' . $mChannelName . '</td>
            </tr>';
                    $headline = wordwrap($row_product['productHeadline'], 52, "\n          \t\t\t");
                    $company = wordwrap($row_product['company'] . $variant_text, 52, "\n          \t\t\t");
                    //$text_message .= "\nAlert #  :\t\t\t" . $sn++ . "\nCompany  :\t\t\t" . $company . "\nHeadline :\t\t\t" . $headline . "\nURL      :\t\t\thttps://www.competiscan.com/index.php?product=" . $row_product['theproductID'] . '&trmsg=' . $tracking_id . "\n";
                    $text_message .= "\nAlert #  :\t\t\t" . $sn++ . "\nCompany  :\t\t\t" . $company . "\nHeadline :\t\t\t" . $headline . "\nURL      :\t\t\thttps://cp.competiscan.com/productdetail?id=" . $row_product['theproductID'] ."\n";
                    $text_message .= "--------------------------------------------------------------------------------------";
                }
            }
            $checkarray[] = $row_product['theproductID'];
        }
        $message .= '</table></td></tr></table>';
        $message .= '</body></html>>';
        //echo $message; die;
        $hdrs = array('From' => "\"Competiscan\" <share@competiscan.com>", 'To' => $to, 'Subject' => $subject);
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
            $rs_company = $DRW->query("SELECT companyName FROM cscan_users WHERE userID='" . $user_id . "'", $DRW_read2);
            $rs_comp = $DRW->fetch_array($rs_company);
            $company_name = trim($rs_comp['companyName']);
            $alert_name = $row['searchName'];
            $insert_date = date('Y-m-d');

            $sql_chk = "SELECT alert_name FROM cscan_email_track WHERE is_send=1 AND user_id='" . $user_id . "' AND LOWER(company_name)='" . strtolower($company_name) . "' AND LOWER(alert_name)='" . strtolower($alert_name) . "' AND DATE_FORMAT(insert_date, '%Y-%m-%d') ='" . $insert_date . "' AND searchID='".$searchID."'";
            //echo $sql_chk;
            //die;
            $query_chk = $DRW->query($sql_chk, $DRW_main);
            if ($DRW->num_rows($query_chk) == 0) {
                $send = $mail->send($to, $headers, $body);

                if (PEAR::isError($send)) {
                    $ehL->write("send(to=$to, subject=$subject)\n" . $send->getMessage());
                    $mail->disconnect();
                    $mail = & Mail::factory('smtp', $params);
                } else {
                    $sent = true;
                    $query = "UPDATE cscan_search SET lastSentDate='$setdate',queryDate='{$row['queryDate']}' WHERE ID='" . $row['ID'] . "'";
                    $DRW->query($query, $DRW_main);
                    ######################
                    if (!empty($tracking_id)) {
                        $sql_update_tarcking = "UPDATE cscan_email_track SET is_send = 1 WHERE id = '" . $tracking_id . "'";
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

function getNotificationListData(){
        global $DRW, $DRW_read2, $DRW_main, $DRW_crm;
        $notifyList = array();

        $queryupdate = "UPDATE cscan_users SET ordering=userID WHERE ordering=0";
        $DRW->query($queryupdate, $DRW_main);

        $query = "SELECT SQL_NO_CACHE DISTINCT ID, searchName, s.userID, notify,
            lastSentDate, queryDate, sendTo, mail_format, addedToDatabase, weekday, is_public,u.emailAddress, u.is_public_user,u.ordering,s.mChannelID
            FROM cscan_search as s
            JOIN cscan_users as u on (u.userID=s.userID)
            WHERE emailAlert='1' AND 
                  active='y' AND 
                ((notify='daily' AND LEFT(lastSentDate,10)<=DATE_SUB(CURDATE(),INTERVAL 1 DAY)) OR
                (notify='weekly' AND LEFT(lastSentDate,10)<=DATE_SUB(CURDATE(),INTERVAL 7 DAY)) OR
                (notify='monthly' AND LEFT(lastSentDate,10)<=DATE_SUB(CURDATE(),INTERVAL 1 MONTH)))
                order by u.ordering ASC ";
        //AND u.userID In(67469)  AND
        //cscan_search_v2
        //cscan_email_track_csv2
        $result = $DRW->query($query, $DRW_read2);

        while ($row = $DRW->fetch_assoc($result)) {
            $notifyList[] = $row;
        }

        return $notifyList;
}
?>