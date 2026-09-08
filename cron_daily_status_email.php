<?php ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set("memory_limit", "-1");
set_time_limit(0);
require_once("includes/dbcon.php");
require_once "Mail.php";
require_once "Mail/mime.php";
date_default_timezone_set('America/Chicago');
$today_date = date('Y-m-d');
if (!empty($_REQUEST['request_date'])) {
  $today_date = $_REQUEST['request_date']; 
}
$previous_date = date('Y-m-d', strtotime($today_date . " - 1 day"));
$imap_ct_count='';
$imap_cp_count='';
$imap_pdp_count='';
$imap_bp_count='';
$imap_pp_count='';
$total_od_count='';
$total_sem_count='';
$total_vd_count='';
$citi_ftp_count='';
$citi_ftp_dup_count='';
//imap email
/*
$sql_tc = "SELECT count(*) as total_count FROM cscan_email where  DATE_FORMAT(email_date,'%Y-%m-%d') = '" . $previous_date . "' and is_text_file=1";
$result_tc = $DRW->query($sql_tc, $DRW_read);
$row_tc = $DRW->fetch_array($result_tc);
$imap_ct_count = $row_tc['total_count'];
//imap consumer panelist
//echo "SELECT count(*) as cp_count,contact_type_m_c FROM cscan_email where DATE_FORMAT(email_date,'%Y-%m-%d') = '" . $previous_date . "' group by contact_type_m_c"; die;
$sql_cp = "SELECT count(*) as cp_count FROM cscan_email where contact_type_m_c='cons_panelist' and DATE_FORMAT(email_date,'%Y-%m-%d') = '" . $previous_date . "' and is_text_file=1";
$result_cp = $DRW->query($sql_cp, $DRW_read);
$row_cp = $DRW->fetch_array($result_cp);
$imap_cp_count = $row_cp['cp_count'];
//imap prod_panelist
$sql_pdp = "SELECT count(*) as pdp_count FROM cscan_email where contact_type_m_c='prod_panelist' and DATE_FORMAT(email_date,'%Y-%m-%d') = '" . $previous_date . "' and is_text_file=1";
$result_pdp = $DRW->query($sql_pdp, $DRW_read);
$row_pdp = $DRW->fetch_array($result_pdp);
$imap_pdp_count = $row_pdp['pdp_count'];
//imap brok_panelist
$sql_bp = "SELECT count(*) as bp_count FROM cscan_email where contact_type_m_c='brok_panelist' and DATE_FORMAT(email_date,'%Y-%m-%d') = '" . $previous_date . "' and is_text_file=1";
$result_bp = $DRW->query($sql_bp, $DRW_read);
$row_bp = $DRW->fetch_array($result_bp);
$imap_bp_count = $row_bp['bp_count'];
//imap prov_panelist
$sql_pp = "SELECT count(*) as pp_count FROM cscan_email where contact_type_m_c='prov_panelist' and DATE_FORMAT(email_date,'%Y-%m-%d') = '" . $previous_date . "' and is_text_file=1";
$result_pp = $DRW->query($sql_pp, $DRW_read);
$row_pp = $DRW->fetch_array($result_pp);
$imap_pp_count = $row_pp['pp_count'];
 
// citi ftp count
$sql_citi_ftp = "SELECT count(*) as citi_ftp_count FROM chicagorecords where  DATE_FORMAT(last_modified,'%Y-%m-%d') = '" . $previous_date . "'";
$result_citi_ftp = $DRW->query($sql_citi_ftp, $DRW_read);
$row_citi_ftp = $DRW->fetch_array($result_citi_ftp);
$citi_ftp_count = $row_citi_ftp['citi_ftp_count'];
// citi ftp duplicate count
$sql_citi_dup_ftp = "SELECT count(*) as citi_dup_ftp_count
        FROM cscan_product_detail_duplicate pdd
        INNER JOIN cscan_product_detail pd ON(pdd.productID=pd.productID)
        LEFT JOIN cscan_panelists pp ON(pdd.panelist_id=pp.panelist_id)
        WHERE DATE_FORMAT(datetime, '%Y-%m-%d') = '" . $previous_date . "'";
$result_citi_dup_ftp = $DRW->query($sql_citi_dup_ftp, $DRW_read);
$row_citi__dup_ftp = $DRW->fetch_array($result_citi_dup_ftp);
$citi_ftp_dup_count = $row_citi__dup_ftp['citi_dup_ftp_count'];
*/
// mChannelID
$sql_mc_prod = "SELECT count(*) as total_entryId FROM cscan_product_detail where DATE_FORMAT(approved_date, '%Y-%m-%d')='" . $previous_date . "' and productStatus=1";
$result_prod = $DRW->query($sql_mc_prod, $DRW_read);
$row_prod = $DRW->fetch_array($result_prod);
$total_prod_count = $row_prod['total_entryId'];
//Client alert engagement 
$query_email_engage = "SELECT cscan_users.userID,firstName,lastName,emailAddress,COUNT(emailAlert) FROM cscan_users,cscan_search 
WHERE cscan_users.userID=cscan_search.userID AND emailAlert='1'
GROUP BY cscan_search.userID";
$client_email_alert_count = $DRW->num_rows($DRW->query($query_email_engage, $DRW_read));
//Client email alerts triggered
$query_email_triggred = "SELECT count(*) as count_email_triggred FROM cscan_search WHERE DATE_FORMAT(lastSentDate, '%Y-%m-%d')='" . $previous_date . "' and emailAlert='1'";
$result_email_triggred = $DRW->query($query_email_triggred, $DRW_read);
$row_email_triggred = $DRW->fetch_array($result_email_triggred);
$email_triggered_count= $row_email_triggred['count_email_triggred'];
//Digital online display ads 
/*
$query_od = "SELECT count(*) as total_od_count FROM cscan_digital_creative where DATE_FORMAT(created_date, '%Y-%m-%d')='" . $previous_date . "' and digital_channel='Online Display'";
$result_od = $DRW->query($query_od, $DRW_digital);
$row_od = $DRW->fetch_array($result_od);
$total_od_count = $row_od['total_od_count'];
//Digital SEM ads 
$query_sem = "SELECT count(*) as total_sem_count FROM cscan_digital_creative where DATE_FORMAT(created_date, '%Y-%m-%d')='" . $previous_date . "' and digital_channel='Search Engine Marketing'";
$result_sem = $DRW->query($query_sem, $DRW_digital);
$row_sem = $DRW->fetch_array($result_sem);
$total_sem_count = $row_sem['total_sem_count'];
//Digital online video ads 
$query_vd = "SELECT count(*) as total_vd_count FROM cscan_digital_creative where DATE_FORMAT(created_date, '%Y-%m-%d')='" . $previous_date . "' and digital_channel='Online Video'";
$result_vd = $DRW->query($query_vd, $DRW_digital);
$row_vd = $DRW->fetch_array($result_vd);
$total_vd_count = $row_vd['total_vd_count'];
*/
// website visitor Without login
$query_withoutlog_visit = "SELECT count(loggin_user_id) as guest_user_count FROM `cscan_visitor_counter` where DATE_FORMAT(visit_date,'%Y-%m-%d') = '" . $previous_date . "' and loggin_user_id<1";
$result_withoutlog_visit = $DRW->query($query_withoutlog_visit, $DRW_read);
$row_withoutlog_visit = $DRW->fetch_array($result_withoutlog_visit);
$total_withoutlog_user_count = $row_withoutlog_visit['guest_user_count'];
// website visitor with login
$query_log_visit = "SELECT count(loggin_user_id) as logg_user_count FROM `cscan_visitor_counter` where DATE_FORMAT(visit_date,'%Y-%m-%d') = '" . $previous_date . "' and loggin_user_id>0";
$result_log_visit = $DRW->query($query_log_visit, $DRW_read);
$row_log_visit = $DRW->fetch_array($result_log_visit);
$total_log_user_count = $row_log_visit['logg_user_count'];
$sql_last = "SELECT id FROM cscan_daily_status_emails Where status=1 and DATE_FORMAT(back_date, '%Y-%m-%d')='" . $previous_date . "' ORDER BY back_date DESC LIMIT 1";
$q_last = $DRW->query($sql_last,$DRW_read);
if($DRW->num_rows($q_last) > 0){
    $row = $DRW->fetch_array($q_last);
    $id = $row['id'];
  $sql_insert=  "Update cscan_daily_status_emails SET status=1, website_visit_count='".$total_log_user_count."',"
        . "website_visit_guest_count='".$total_withoutlog_user_count."',imap_total_count='" . $imap_ct_count . "',imap_cp_count='" . $imap_cp_count . "',"
        . "imap_pp_count='" . $imap_pp_count . "',imap_mbp_count='" . $imap_bp_count . "',"
        . "imap_pdp_count='" . $imap_pdp_count . "',moved_citi_ftp_count='" . $citi_ftp_count . "',"
        . "citi_duplicate_file_count='" . $citi_ftp_dup_count . "',total_entryId_count='" . $total_prod_count . "',"
        . "client_email_alert_count='" . $client_email_alert_count . "',email_alert_trigger_count='".$email_triggered_count."',digital_od_count='" . $total_od_count . "',"
        . "digital_sem_count='" . $total_sem_count . "',digital_video_count='" . $total_vd_count . "',back_date='".$previous_date."' where id ='".$id."'";
}else{
$sql_insert = "INSERT INTO cscan_daily_status_emails SET status=1, website_visit_count='".$total_log_user_count."',"
        . "website_visit_guest_count='".$total_withoutlog_user_count."',imap_total_count='" . $imap_ct_count . "',imap_cp_count='" . $imap_cp_count . "',"
        . "imap_pp_count='" . $imap_pp_count . "',imap_mbp_count='" . $imap_bp_count . "',"
        . "imap_pdp_count='" . $imap_pdp_count . "',moved_citi_ftp_count='" . $citi_ftp_count . "',"
        . "citi_duplicate_file_count='" . $citi_ftp_dup_count . "',total_entryId_count='" . $total_prod_count . "',"
        . "client_email_alert_count='" . $client_email_alert_count . "',email_alert_trigger_count='".$email_triggered_count."',digital_od_count='" . $total_od_count . "',"
        . "digital_sem_count='" . $total_sem_count . "',digital_video_count='" . $total_vd_count . "',back_date='".$previous_date."'";
}
$result=$DRW->query($sql_insert, $DRW_main);
if($result){ 
$to="nate@competiscan.com,ashok.singh@nmgtechnologies.com,devendra.tiwari@nmgtechnologies.com,competiscan@suntecindia.com,jason@competiscan.com";
$subject = "Daily Status Reports";
$html = '<html>
            <head><title>Daily Status Reports</title></head>
            <body>
                <table width="100%" border="1" cellspacing="0" cellpadding="0" bgcolor="white" style="border-collapse:collapse; border:1px solid #ccc;" bordercolor="#14734F">
                    <tr style="background-color:#0e758cc2;color: #FFFFFF;font-weight: bold;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 11px;"><td colspan="14" align="center"><h3>Daily Status Reports</h3><small>(Automated Email - '.date("Y-m-d H:i:s").')</small><br><br></td></tr>                       
                     <tr style="background-color:#1399c6;color: #FFFFFF;font-weight: bold;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 11px;">
                            <th colspan="2" align="center" height="25" style="border-right: 2px solid #ccc;">
                              Email Alert
                            </th>
                             <th colspan="2" align="center" height="25" style="border-right: 2px solid #ccc;">
                                Entry ID
                            </th>
                            <th colspan="2" align="center" height="25" style="border-right: 2px solid #ccc;">
                                Visitors
                            </th> 
                            <th colspan="1" align="center" height="25" style="border-right: 2px solid #ccc;">
                                Date
                            </th>
                        </tr>                                
                        <tr style="font-weight: bold;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 11px;">
                            <td style="background-color:#ffd9cc;">Engagement</td><td style="background-color:#ffd9cc;">'.$client_email_alert_count.'</td>
                            <td style="background-color:#ffe6cc;" valign="top" rowspan="5">Total Entry ID</td>
                            <td style="background-color:#ffe6cc;" valign="top" rowspan="5">'.$total_prod_count.'</td>
                            <td style="background-color:#f2ffe6;">User Visitors</td><td style="background-color:#f2ffe6;">'.$total_log_user_count.'</td>
                            <td style="background-color:#e6f2ff;" valign="top" rowspan="5">'.$previous_date.'</td>
                        </tr>
                        <tr style="font-weight: bold;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 11px;">
                            <td style="background-color:#ffd9cc;" valign="top" rowspan="4">Triggered</td>
                            <td style="background-color:#ffd9cc;" valign="top" rowspan="4">'.$email_triggered_count.'</td>
                            <td style="background-color:#f2ffe6;" valign="top" rowspan="4">Guest Visitors</td>
                            <td style="background-color:#f2ffe6;" valign="top" rowspan="4">'.$total_withoutlog_user_count.'</td>
                       </tr>
                </table>
            </body></html>>>';
$params = array(
    'username' => '',
    'password' => '',
    'persist' => true,
);
$mail = Mail::factory('smtp', $params);
$crlf = "\n";
$hdrs = array('From' => "\"Competiscan\" <share@competiscan.com>", 'To' => $to, 'Subject' => $subject);
$mime = new Mail_mime($crlf);
$mime->setHTMLBody($html);
$body = $mime->get();
$headers = $mime->headers($hdrs);
$send = $mail->send($to, $headers, $body);
echo "Cron file run successfully!"; die;
}
?>