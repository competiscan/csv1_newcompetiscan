<?php
/*
 * [  ] Log amount of disk space on DB server prior to running script
 * [  ] Log amount of disk space on file server prior to running script
 * [  ] Make sure directories 'deleted' and 'backups' exist and are writable
 * [  ] Files removed will be relocated to ./deleted
 * [  ] Database backups for each archive year will be in ./backups
 * [  ] Run script from the docroot of competiscan
 * [  ] To interrupt script touch file 'stop-archive' -- it will exit as soon as possible.
 */
if (php_sapi_name() != "cli") {
    header("HTTP/1.0 404 Not Found");
    die();
}

$host = '192.168.31.187';
$name = 'competi_competidb';
$user = 'competiscan_db';
$pass = '695r3D29T';

function logLine($fh, $message) {
    fwrite($fh, date('Y-m-d H:i:s').$message."\n\n");
}

$db = new PDO("mysql:host=$host;dbname=$name", $user, $pass);
$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

$tempDeletedDir = 'deleted';
$backupDir = 'backups';
$logFile = 'competiscan_cleanup.log';

$emailDbs = array('cscan_email2007',
                    'cscan_email2008',
                    'cscan_email2009',
                    'cscan_email2010',
                    'cscan_email2011',
                    'cscan_email2012',
                    'cscan_email201301',
                    'cscan_email201307');

$fhLog = fopen($logFile, 'a');
logLine($fhLog, "Initiating script...");

foreach($emailDbs as $table) {
    if(file_exists('stop-archive')) {
        logLine($fhLog, "Script interrupted...");
        logLine($fhLog, "Script exiting...");
        exit;
    }
    $attachmentTable = preg_replace("/cscan_email/", "cscan_email_attach_file", $table);
    $fileTable = preg_replace("/cscan_email/", "cscan_email_file", $table);
    $forwardTable = preg_replace("/cscan_email/", "cscan_email_forward", $table);
    $saveTable = preg_replace("/cscan_email/", "cscan_email_save", $table);
    $textTable = preg_replace("/cscan_email/", "cscan_email_text", $table);

    $backupTimestamp = date('YmdHis');

    passthru("mysqldump --opt -u$user -p$pass -h$host $name $table > $backupDir/$table$backupTimestamp.sql");
    passthru("mysqldump --opt -u$user -p$pass -h$host $name $table > $backupDir/$fileTable$backupTimestamp.sql");
    passthru("mysqldump --opt -u$user -p$pass -h$host $name $table > $backupDir/$attachmentTable$backupTimestamp.sql");
    passthru("mysqldump --opt -u$user -p$pass -h$host $name $table > $backupDir/$forwardTable$backupTimestamp.sql");
    passthru("mysqldump --opt -u$user -p$pass -h$host $name $table > $backupDir/$saveTable$backupTimestamp.sql");
    passthru("mysqldump --opt -u$user -p$pass -h$host $name $table > $backupDir/$textTable$backupTimestamp.sql");

    $emailQuery = $db->prepare("SELECT * FROM $table as a LEFT JOIN $attachmentTable as b ON b.muid = a.muid LEFT JOIN $fileTable as d ON d.muid = a.muid WHERE (a.deleted = 0 OR a.deleted = 1 OR a.deleted = 3)");
    $emailQuery->execute();
    logLine($fhLog, "Query: SELECT * FROM $table as a LEFT JOIN $attachmentTable as b ON b.muid = a.muid LEFT JOIN $fileTable as d ON d.muid = a.muid WHERE (a.deleted = 0 OR a.deleted = 1 OR a.deleted = 3)");

    while($result = $emailQuery->fetchObject()) {
        $return = 0;
        if(!empty($result->ceafpath)) {
            if(substr($result->ceafpath, 0, 1) == '/') {
                $path = substr($result->ceafpath, 1);
            } else {
                $path = $result->ceafpath;
            }
            passthru("mv $path $tempDeletedDir", $return);
            if($return != 0)
                logLine($fhLog, "Error Archiving $path");
            else
                logLine($fhLog, "Successfully Archived $path");
        }
        if(!empty($result->cefpath)) {
            if(substr($result->cefpath, 0, 1) == '/') {
                $path = substr($result->cefpath, 1);
            } else {
                $path = $result->cefpath;
            }
            passthru("mv $path $tempDeletedDir", $return);
            if($return != 0)
                logLine($fhLog, "Error Archiving $path");
            else
                logLine($fhLog, "Successfully Archived $path");
        }
    }

    $query = "DELETE a, b, d, e, f, g
                  FROM $table as a
                  LEFT JOIN $attachmentTable as b ON b.muid = a.muid
                  LEFT JOIN $fileTable as d ON d.muid = a.muid
                  LEFT JOIN $forwardTable as e ON e.muid = a.muid
                  LEFT JOIN $saveTable as f ON f.muid = a.muid
                  LEFT JOIN $textTable as g ON g.muid = a.muid
                  WHERE (a.deleted = 0 OR a.deleted = 1 OR a.deleted = 3)";
    $db->query($query);

    logLine($fhLog, "Query: $query");

}
