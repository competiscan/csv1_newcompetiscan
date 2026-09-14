<?php

require_once("includes/dbcon.php");
require_once("includes/functions.php");
$today_date = date('Y-m-d');
if (!empty($_REQUEST['date'])) {
    $today_date = $_REQUEST['date'];
}
$previous_date = date('Y-m-d', strtotime($today_date . " - 1 day"));
$sql = "SELECT email_id FROM cscan_sent_imapcsv_link where status=1";
$query = $DRW->query($sql, $DRW_read);
while ($row = $DRW->fetch_assoc($query)) {
    $email_id = $row['email_id'];
    $sqlQuery = "SELECT id,document_path,file_name FROM cscan_imapcsv where  DATE_FORMAT(created_on,'%Y-%m-%d') = '" . $today_date . "' ORDER BY id DESC";
    $result = $DRW->query($sqlQuery, $DRW_read);
    $rs = $DRW->fetch_array($result);
    $document_path = $rs['document_path'];
    $filename = $rs['file_name'];
//$s3_link='https://csbucket007.s3.amazonaws.com/'.$document_path.$filename;
    $s3_link = $displays3URL.$document_path.$filename;
    $to = "devendra.tiwari@nmgtechnologies.com,arvind.chaurasia@nmgtechnologies.com";
    $subject = "Today's email data form competiscan";
    $html = '<table width="100%" border="0">';
    $html .= '<tr><td>Hi,<br/><br/></td></tr>';
    $html .= '<tr><td>Please find the daily imap email data in csv format which link is as belows. <br/><br/></td></tr>';
    $html .= '<tr><td>URL : ' . $s3_link . '</td></tr>';
    $html .= '</table>';
   // echo $html; die;
    sendDevAlert($subject, $html, $to);
}
?>