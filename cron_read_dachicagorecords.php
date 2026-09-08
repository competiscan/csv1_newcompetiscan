<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set("memory_limit", "-1");
set_time_limit(0);

require_once("includes/ehLog_set.php");
$ehL->start(__FILE__);

require_once ("includes/dbcon.php");
$DRW->databaseReadWrite_die = 1;
include_once 'includes/functions.php';
include_once 'includes/thumb.php';
require_once "Mail.php";
require_once "Mail/mime.php";
##################################################
echo 'Start: '.date("Y-m-d H:i:s").'</br></br>';
//initialization
$arrLog = array();
$arrLog[2] = date("Y-m-d H:i:s");
###################################################
//$ftp_server = "competiscan2.chicagorecords.com"; //competiscan.chicagorecords.com, ftp.chicagorecords.com
$ftp_server = "Competiscan3.chicagorecords.com";  // New FTP Server
//$ftp_server = "12.146.58.20";  // New FTP Server
$ftp_server = "12.146.58.24";  // New FTP Server
//$maxsizebyte=   6000000;
$maxsizebyte=   50000000;
if(!defined('ENV')){
    define('ENV',getenv('SERVER_NAME'));
}
$root_dirs = array('./output');
if(ENV == 'localhost' || ENV == 'uat3.competiscan.com'){
    $ftp_server = "competiscan3.chicagorecords.com";
    //$root_dirs = array('./datest');
    $root_dirs = array('./da');
  //  $ftp_server = "competiscan3.chicagorecords.com";
  //  $root_dirs = array('./output');
}
$ftp_user_name = "output2"; // output | competiscan
$ftp_user_pass = "c0mpet1sc@n"; //GoBears!
ini_set('memory_limit', '-1');
$AUTH_DATA = array();
$AUTH_DATA['userID'] = 0;

$local_dir = dirname(__FILE__)."/dachicagorecordsftp/";
//echo $local_dir; die;
//$root_dirs = array('./output');
//$root_dirs = array('./datest');
$processed_time = time()-345600;
$max_last_modified_ts = 0;

$parr = array();
$sql = "SELECT age_pID,age_pmin FROM cscan_age_product ORDER BY age_psort";
$result = $DRW->query($sql,$DRW_read2);
while( $row = $DRW->fetch_row( $result ) ){
    $parr[$row[0]] = $row[1];
}
@$DRW->free_result($result);

$tries = 10;
$try = 0;
$exec = 0;

$totalFiles = 0;
do{
    $connected = false;
    $conn_id = connectFTP($ftp_server, $ftp_user_name, $ftp_user_pass);
    if($conn_id!==false){
        $connected = true;

        $files = array();
        $filesDate = array();
        $filesRename = array();

        foreach($root_dirs as $rd){
            if(getFiles($rd)){
                $totalFiles++;
            }
        }

        $proc = './PROCESSED';
        if(!ftp_chdir($conn_id, $proc)){
            ftp_mkdir($conn_id, $proc);
        }
        ftp_chdir($conn_id, '/');
        foreach($filesRename as $fr){
            if(!ftp_rename($conn_id,'.'.$fr, $proc.$fr)){
                $ehL->write("Could not rename: ".$fr.' to '.$proc.$fr);
            }
        }

        if($conn_id!==false){
            ftp_close($conn_id);
        }
    }
    if(!$connected){
        sleep(300);
    }
    $try++;
} while(!$connected && $try<$tries);

if($try==$tries){
    $ehL->write("Could not log in.");
}
$ehL->stop();

function connectFTP($ftp_server, $ftp_user_name, $ftp_user_pass){
    $conn_id = ftp_connect($ftp_server,21,90);// or echo("Couldn't connect to $ftp_server\n");
    if($conn_id){
        $login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);// or echo("You do not have access to this ftp server\n");
        if($login_result){
            ftp_pasv($conn_id, true);
            return $conn_id;
        }
    }
    return false;
}
function checkFTPConnection(){
    if($GLOBALS['conn_id']!==false){
        $check = ftp_raw($GLOBALS['conn_id'], "NOOP");
        if(!is_array($check) || !in_array('200 OK',$check)){
            ftp_close($GLOBALS['conn_id']);
            $GLOBALS['conn_id'] = connectFTP($GLOBALS['ftp_server'], $GLOBALS['ftp_user_name'], $GLOBALS['ftp_user_pass']);
            if($GLOBALS['conn_id']===false){
                $GLOBALS['connected'] = false;
            }
        }
    }
}
function getFiles($dir='.'){
    global $DRW,$DRW_main,$DRW_read2,$maxsizebyte;
    checkFTPConnection();
    if($GLOBALS['conn_id']!==false){
        $contents = ftp_nlist($GLOBALS['conn_id'], $dir);

        if($contents!==false){
            $inputFiles = $existInDatabase = array();
            $i=0;
            foreach ($contents as $file) {
                if($GLOBALS['conn_id']!==false){
                    if(strpos($file,'/PROCESSED')===false){
                        if(ftp_size($GLOBALS['conn_id'], $file)==-1){
                            getFiles($file);
                        }
                        else if(ftp_size($GLOBALS['conn_id'], $file)>=$maxsizebyte){
			    $filearray = explode('/', $file);
                            $newfnames = $filearray[count($filearray) - 1];
                            $newdir='/Rejected/'.$newfnames;
                            $sqlc = "INSERT INTO cscan_rejected_chicagorecords SET filepath='".$DRW->real_escape_string($file)."',filename='".$newfnames."'";
                            $DRW->query($sqlc,$DRW_main);
                            if(ftp_rename($GLOBALS['conn_id'],$file, $newdir)){
                                echo"success";
                            }else{
                                echo"failure";
                            }

                        }
                        else{
                            $mod_date = ftp_mdtm($GLOBALS['conn_id'], $file);
                            if($mod_date>=$GLOBALS['max_last_modified_ts'] && preg_match('/\\.pdf$/i',$file)){ //get zip?
                                $file = preg_replace('/^\\.+/','',$file);
                                $query = $DRW->query("SELECT SQL_NO_CACHE COUNT(*) FROM da_chicagorecords WHERE filename='".$DRW->real_escape_string($file)."'",$DRW_read2);

                                $rows = $DRW->fetch_row($query);
                                $numrows = $rows[0];
                                @$DRW->free_result($query);
                                if($numrows==0) {
                                    $GLOBALS['files'][] = $file;
                                    $GLOBALS['filesDate'][] = $mod_date;
                                    if($GLOBALS['conn_id']!==false){
                                       // $newfile = $GLOBALS['local_dir'].end(explode("/",$file));
                                       // $csvfile = "z:\\dachicagorecordsftp\\".end(explode("/",$file));

                                        $fname = end(explode("/",$file));
                                        //check fname for current date
                                        $sql_22 = "SELECT id FROM da_chicagorecords WHERE filename='".$DRW->real_escape_string($fname)."' AND DATE_FORMAT(import_date, '%Y-%m-%d') = '".date("Y-m-d")."'";
                                        $q_22 = $DRW->query($sql_22,$DRW_read2);
                                        if($DRW->num_rows($q_22)>0){
                                            $fname = time().'_'.$fname;
                                        }
                                        $newfile = $GLOBALS['local_dir'].$fname;
                                        $csvfile = "z:\\dachicagorecordsftp\\".$fname;


                                        if (is_file($file) && !copy($file, $newfile)) {
                                            die("failed to copy $file...\n To $newfile");
                                        }
                                        $local_file = $newfile;

                                        $ftp_datetime = date("Y-m-d H:i:s",$mod_date);
                                        $sqlc = "INSERT INTO da_chicagorecords SET filename='".$DRW->real_escape_string($file)."',local_dir='".$DRW->real_escape_string($newfile)."', ftp_date='".$ftp_datetime."'";
                                        $DRW->query($sqlc,$DRW_main);
                                        $id = $DRW->insert_id($DRW_main);
                                        //echo $local_file.'<br/>'.$file;die;
                                        if(!ftp_get($GLOBALS['conn_id'], $local_file, $file, FTP_BINARY, 0)) {
                                            echo "There was a problem transfering file $file to $local_file\n";
                                            $sqlc = "DELETE FROM da_chicagorecords WHERE id=$id";
                                            $DRW->query($sqlc,$DRW_main);
                                           // echo 'kkk'; die;
                                            checkFTPConnection();
                                        }else{
                                            $filetobesized = str_replace('z:\\dachicagorecordsftp\\', dirname(__FILE__).'/dachicagorecordsftp/', $newfile);
                                            $filesize = filesize($filetobesized);
                                            $sql_u = "UPDATE da_chicagorecords set filesize_in_byte='".$filesize."' WHERE id = '".$id."'";
                                            $DRW->query($sql_u,$DRW_main);
                                            //if(chmod($local_file,0644)){
                                            //if($filesize>0){
                                                $inputFiles[$i]['filepath'] = $csvfile;
                                                $inputFiles[$i]['date'] = $ftp_datetime;
                                                $inputFiles[$i]['status'] = 0;
                                                $inputFiles[$i]['match'] = 0;
                                                $inputFiles[$i]['daID'] = $id;
                                                $i++;
                                            //}
                                            //}

                                        }
                                    }
                                    //uncomment below code once ready for live
                                    /*if (ftp_delete($GLOBALS['conn_id'], $file)){
                                        echo "$file deleted";
                                    }else{
                                        echo "Could not delete $file";
                                    }*/

                                }else{
                                    $existInDatabase[] = $file;
                                }
                            }
                        }
                    }
                }
            }
        }
        if(!empty($inputFiles)){
            chicagoftpCsv($inputFiles);
            return true;
        }
        return false;
//        if(!empty($existInDatabase)){
//            echo 'Below files alreay updated into database-</br>';
//            foreach($existInDatabase as $f){
//                echo $f.'</br>';
//            }
//        }
    }
}

//logs
$csv = dirname(__FILE__).'/dacsv/'.date('Y-m-d').'/'.date('Y-m-d').'_search_input.csv';
if(file_exists($csv)){
    $arrLog[0] = 'ReadChicagoFtp(c)';
    $arrLog[1] = date('Y-m-d')."_search_input.csv";
    $arrLog[3] = date("Y-m-d H:i:s");
    $arrLog[4] = 'Success';
    echo 'done';

}else{
    $arrLog[0] = 'ReadChicagoFtp(c)';
    $arrLog[1] = date('Y-m-d')."_search_input.csv";
    $arrLog[3] = date("Y-m-d H:i:s");
    $arrLog[4] = 'No data available';
}
if(!empty($arrLog) && $exec == 0){
    ksort($arrLog);
    $localLog = dirname(__FILE__)."/dacsv/".date('Y-m-d')."/log.csv";
    updateLog($arrLog,$localLog);
    $globalLog = dirname(__FILE__)."/dacsv/".date('Y-m')."_daLog.csv";
    updateLog($arrLog,$globalLog);
    $exec = 1;
}
//automated email
if(!file_exists($csv)){
    $message = ' No data uploaded in citi ftp!';
}else{
    $y =1;
    $x = 0;
    if (($handle = fopen($csv, "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            if($y > 1){
                $x++;
            }
            $y++;
        }
        fclose($handle);
    }
    $message = '
        <table width="100%">
            <tr><td colspan="3" align="center"><h3>Chicago Records FTP</h3><small>(Automated Email - '.date("Y-m-d H:i:s").')</small></td></tr>
            <tr><td colspan="3" align="center">&nbsp;</td></tr>
            <tr>
                <td>Number of records to be search</td>
                <td>:</td>
                <td>'.$x.'</td>
            </tr>
        </table>
    ';
}
$to = "manas@nmgtechnologies.com,devendra.tiwari@nmgtechnologies.com,arvind.chaurasia@nmgtechnologies.com";
$subject = "FTP Chicago Records(read)\r\n\r\n";

sendDevAlert($subject,$message, $to);
//echo '</br></br>End: '.date("Y-m-d H:i:s");
die;
?>
