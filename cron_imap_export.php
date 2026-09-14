<?php
require_once("includes/dbcon.php");
$today_date = date('Y-m-d');
//$today_date='2019-07-08';
if(!empty($_REQUEST['date'])){
 $today_date=$_REQUEST['date'];
}
$previous_date = date('Y-m-d', strtotime($today_date . " - 1 day"));
$root = dirname(__FILE__);
$dir = $root . '/imapcsv/';
//contact_type_m_c='cons_panelist' and
$query = "SELECT email_subject, from_sent_name,from_sent_email_address,from_sent_date_format FROM cscan_email where contact_type_m_c='cons_panelist' and DATE_FORMAT(from_sent_date_format,'%Y-%m-%d') = '".$previous_date."'";
//echo $query; die;
$query_result = $DRW->query($query, $DRW_read);
$num = $DRW->num_rows($query_result);
if($num >0 ){
$delimiter = ",";  
$cyear = date("Y") . "/";
$cmonth = date('m') . "/";
$cdate = date('d') . "/";
if (!is_dir($dir . $cyear)) {
    @mkdir($dir . $cyear, 02755);
    @chmod($dir . $cyear, 02755);
    @chown($dir . $cyear, 'apache');
}
if (!is_dir($dir . $cyear . $cmonth)) {
    @mkdir($dir . $cyear . $cmonth, 02755);
    @chmod($dir . $cyear . $cmonth, 02755);
    @chown($dir . $cyear . $cmonth, 'apache');
}
if (!is_dir($dir . $cyear . $cmonth . $cdate)) {
    @mkdir($dir . $cyear . $cmonth . $cdate, 02755);
    @chmod($dir . $cyear . $cmonth . $cdate, 02755);
    @chown($dir . $cyear . $cmonth . $cdate, 'apache');
}
$csv_path = 'imapcsv/' . $cyear . $cmonth . $cdate;
$ymd = $dir . $cyear . $cmonth . $cdate;
$csv_file_name = "Competiscan_Email_Export_" .date('Y-m-d')."_".rand().".csv";
$filename = $ymd . "Competiscan_Email_Export_" . date('Y-m-d')."_".rand(). ".csv";
$f = fopen($filename, 'w');
$fields = array('Subject', 'Sender Name', 'Sender Email', 'Sender Date');
fputcsv($f, $fields, $delimiter);
while ($data = $DRW->fetch_row($query_result)) {
    if(!empty($data[0])){
    $email_subject = html_entity_decode($data[0]);
    }else{
      $email_subject = ' ';   
    }
    $send_name=$DRW->real_escape_string(utf8_decode($data[1]));
    if(!empty($send_name)){
    $from_sent_name = html_entity_decode(htmlspecialchars_decode($send_name,ENT_QUOTES));
    } else{
        $from_sent_name=' ';
    }
    //$from_sent_name = htmlspecialchars_decode($data[1],ENT_QUOTES);
    if(!empty($data[2])){
    $from_sent_email_address = html_entity_decode($data[2]);
    } else{
       $from_sent_email_address=' '; 
    }
    $from_sent_date_format = $data[3];
    $lineData = array(utf8_decode($email_subject), $from_sent_name, $from_sent_email_address, $from_sent_date_format);
    fputcsv($f, $lineData, $delimiter);
}
fclose($f); 
$result = $s3->putObject([
    'Bucket' => $bucket_name,
    'Key' => $csv_path . $csv_file_name,
    'SourceFile' => $filename,
    'ACL' => 'public-read',
    'ContentType' => 'text/csv',
    'Metadata' => array(
        'string' => 'string'
    )
 ]);
if (isset($result['@metadata']['statusCode']) && $result['@metadata']['statusCode'] == 200) {
    $sql_imap = "insert into cscan_imapcsv set document_path='" . $DRW->real_escape_string($csv_path) . "',file_name='" . $DRW->real_escape_string($csv_file_name) . "'";
    $DRW->query($sql_imap, $DRW_main);
    @unlink($filename);
    echo "Csv data export successfully!";
    exit;
}

}
echo "No record found!";exit;
?>