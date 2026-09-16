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

$backday = (!empty($_REQUEST['p1']))?trim($_REQUEST['p1']):0;
if(isset($_SERVER['argc']) && $_SERVER['argc']>0) {
    $backday = (!empty($_SERVER['argv'][1]))?trim($_SERVER['argv'][1]):$backday;
}

$day = (int)$backday;

if(!empty($day)){
    $csvdate = date("Y-m-d", strtotime(" -$day day"));
}else{
    $csvdate = date("Y-m-d");
}
$arrGlobalOutput['csvdate'] = $csvdate;
#########################################
$x = 1;
$outputData = array();
$error = '';
$importCSV = dirname(__FILE__)."/damaxmailcsv/".$csvdate."/".$csvdate."_import.csv";

if (($handle = fopen($importCSV, "r")) !== FALSE) {
    $inputName = $csvdate.'_search_input.csv';
    //remove search_input & generate new one
    if(file_exists(dirname(__FILE__)."/damaxmailcsv/".$csvdate."/".$inputName)){
        unlink(dirname(__FILE__)."/damaxmailcsv/".$csvdate."/".$inputName);
    }
    $total=0;
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        if($x > 1){
            print_r($data);
            $filename = basename(str_replace("\\","/",$data[0]));
            $filePart = explode("_",$filename); 
            $muid = current($filePart);
            $mailbox_uid = (!empty($filePart[1]))?trim($filePart[1]):0;
            $panelist_id = current(explode(".",end($filePart)));

            if($mailbox_uid){
                $sql = "SELECT email_date FROM cscan_email_sameday_search WHERE mailbox_uid = '".$mailbox_uid."'";
            }else{
                $sql = "SELECT email_date FROM cscan_email_sameday_search WHERE muid = '".$muid."'";
            } 
            echo $sql;
            $query1 = $DRW->query($sql,$DRW_read);            
            if($DRW->num_rows($query1) > 0){
                $arrOutput['exec']['sql1'] = $sql;

                if($mailbox_uid){
                    $sql = "SELECT `filename` FROM cscan_da_maxmail_html_sameday_search WHERE mailbox_uid='".$mailbox_uid."'";
                }else{
                    $sql = "SELECT `filename` FROM cscan_da_maxmail_html_sameday_search WHERE muid='".$muid."'";
                }                
                $query2 = $DRW->query($sql, $DRW_read);                
                if($DRW->num_rows($query2) > 0){
                    $arrOutput['exec']['sql2'] = $sql;

                    $rs = $DRW->fetch_assoc($query2);
                    $file = $rs['filename'];
                    /* $arrPath = explode("/",$file);
                    array_pop($arrPath);
                    $path = implode("/", $arrPath);
                    if(!is_dir($path)){
                        mkdir($path,0777, true);
                        @chmod($path,0777);
                        @chown($path,'apache');
                    } */
                    if(file_exists($file)){
                        if($mailbox_uid){
                            $sql = "SELECT id FROM cscan_da_maxmail_html WHERE mailbox_uid = '".$mailbox_uid."'";
                        }else{
                            $sql = "SELECT id FROM cscan_da_maxmail_html WHERE muid = '".$muid."'";
                        }                                                                
                        $query3 = $DRW->query($sql,$DRW_read);                            
                        if($DRW->num_rows($query3) == 0){
                            $arrOutput['exec']['sql3'] = $sql;

                            if($mailbox_uid){
                                $sql = "INSERT INTO cscan_da_maxmail_html(`muid`,`mailbox_uid`,`filename`,`date_created`) (SELECT `muid`,`mailbox_uid`,`filename`,`date_created` FROM cscan_da_maxmail_html_sameday_search WHERE mailbox_uid='".$mailbox_uid."')";
                            }else{
                                $sql = "INSERT INTO cscan_da_maxmail_html(`muid`,`mailbox_uid`,`filename`,`date_created`) (SELECT `muid`,`mailbox_uid`,`filename`,`date_created` FROM cscan_da_maxmail_html_sameday_search WHERE muid='".$muid."')";
                            }
                            
                            echo $sql;
                            if($DRW->query($sql,$DRW_main)){
                                $arrOutput['exec']['sql4'] = $sql;
                                
                                $csvfile = str_replace(dirname(__FILE__).'/', 'z:\\', $file);
                                $csvfile = str_replace("/", "\\", $csvfile);
                                $inputFiles = array();

                                $inputFiles[$i]['filepath'] = $csvfile;
                                $inputFiles[$i]['date'] = date('Y-m-d H:i');
                                $inputFiles[$i]['status'] = 0;
                                if(!empty($inputFiles)){
                                    $total++;
                                    //$inputName = $csvdate.'_search_input.csv';
                                    
                                    echo 'input files--->'. $inputFiles.'input name=====>'. $inputName.'csvdate===>'. $csvdate;
                                    $error = dmMaxmailCsv($inputFiles, $inputName, $csvdate);
                                }
                            }else{
                                $remove = true;
                                $arrOutput['not_exec']['sql3'] = $sql;
                            }
                        }else{
                            $remove = true;
                            $arrOutput['not_exec']['sql2'] = $sql;
                        }      
                    }else{
                        $remove = true;
                        $arrOutput['not_exec']['file2'] = 'File not exists: '.$file;
                    }                    
                }else{
                    $remove = true;
                    $arrOutput['not_exec']['sql1'] = $sql;
                }  
                // remove
                if($remove){
                    $DRW->query("DELETE FROM cscan_email_sameday_search WHERE muid = '".$muid."'",$DRW_main);
                    $DRW->query("DELETE FROM cscan_email_text_sameday_search WHERE muid = '".$muid."'",$DRW_main);
                    $DRW->query("DELETE FROM cscan_email_file_sameday_search WHERE muid = '".$muid."'",$DRW_main);
                }                
            }                      
        }
        $arrGlobalOutput[$x] = $arrOutput;
        $x++;
    }
    if($total>0){
        $sql = "SELECT id FROM cscan_maxmail_sameday_log WHERE `status`='1' ORDER BY id DESC LIMIT 1";
        $querychk1 = $DRW->query($sql,$DRW_read);            
        if($DRW->num_rows($querychk1) > 0){
            $lRes = $DRW->fetch_assoc($querychk1);
            $msd_logid = $lRes['id'];
            if($msd_logid){
                $sql = "UPDATE cscan_maxmail_sameday_log SET `email_indexed`='".$total."', `status`='2' WHERE id='".$msd_logid."'";
                $DRW->query($sql, $DRW_main);
            }
        }
    }
    fclose($handle);
}else{
    $error = 'Unable to read file: '.$importCSV;
    $arrGlobalOutput['open']['file1'] = $error;
    $sql = "INSERT IGNORE INTO da_maxmail_bg_scripts SET command = '".$DRW->real_escape_string($error)."'";                         
    $DRW->query($sql,$DRW_main);
}
//pr($arrGlobalOutput);
################ Sameday duplicates ######
$y = 1;
$duplicateCSV = dirname(__FILE__)."/damaxmailcsv/".$csvdate."/".$csvdate."_duplicate.csv";
if (($handle = fopen($duplicateCSV, "r")) !== FALSE) {    
    $remove = true;
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        if($y > 1){
            $file = $data[0];
            $filename = basename(str_replace("\\","/",$file));
            $filePart = explode("_",$filename); 
            $muid = current($filePart);
            $muid = (int)$muid;
            $mailbox_uid = (!empty($filePart[1]))?trim($filePart[1]):0;
            $panelist_id = current(explode(".",end($filePart)));
            $date = $data[1];
            $match_percentage = $data[3];
            $match_with = $data[4];
            $filename2 = basename(str_replace("\\","/",$match_with));
            $filePart2 = explode("_",$filename2); 
            $duplicate_muid = current($filePart2);
            $match_with = str_replace("z:\\", dirname(__FILE__).'/', $match_with);
            $match_with = str_replace('\\', '/', $match_with);            
            
            $file = str_replace("z:\\", dirname(__FILE__).'/', $file);
            $file = str_replace('\\', '/', $file);
            $duplicate_file = str_replace("damaxmailhtml", "damaxmailhtml_sd_duplicate", $file);
            
            if($mailbox_uid){
                $sql = "SELECT id FROM cscan_damaxmail_sameday_duplicate WHERE mailbox_uid='".$mailbox_uid."'";
            }else{
                $sql = "SELECT id FROM cscan_damaxmail_sameday_duplicate WHERE muid='".$muid."'";
            }            
            
            $query1 = $DRW->query($sql, $DRW_read);
            if($DRW->num_rows($query1) == 0){
                $arrPath = explode("/",$duplicate_file);
                array_pop($arrPath);
                $newpath = implode("/", $arrPath);
                if(!is_dir($newpath)){
                    mkdir($newpath,0777, true);
                    @chmod($newpath,0777);
                    @chown($newpath,'apache');
                }                
                //echo $duplicate_file;
                if(rename($file, $duplicate_file)){
                    $email_subject = '';
                    if($mailbox_uid){
                        $sql = "SELECT email_subject FROM cscan_email_sameday_search WHERE mailbox_uid='".$mailbox_uid."'";
                    }else{
                        $sql = "SELECT email_subject FROM cscan_email_sameday_search WHERE muid='".$muid."'";
                    }                    
                    $query2 = $DRW->query($sql, $DRW_read);
                    if($DRW->num_rows($query2) > 0){
                        $row = $DRW->fetch_assoc($query2);
                        $email_subject = $row['email_subject'];
                    }
                    if($mailbox_uid){
                        $sql = "SELECT id FROM cscan_damaxmail_sameday_duplicate WHERE mailbox_uid = '".$mailbox_uid."' AND `subject` = '".$DRW->real_escape_string($email_subject)."'";
                    }else{
                        $sql = "SELECT id FROM cscan_damaxmail_sameday_duplicate WHERE muid = '".$muid."' AND `subject` = '".$DRW->real_escape_string($email_subject)."'";
                    }
                    $query3 = $DRW->query($sql, $DRW_read);
                    if($DRW->num_rows($query3) == 0){
                        $sql = "INSERT INTO cscan_damaxmail_sameday_duplicate SET
                            muid = '".$muid."',
                            mailbox_uid = '".$mailbox_uid."',
                            `subject` = '".$DRW->real_escape_string($email_subject)."',
                            panelist_id = '".$panelist_id."',
                            percentage_match = '".$match_percentage."',
                            `filename` = '".$file."',
                            duplicate_file = '".$duplicate_file."',
                            duplicate_with = '".$match_with."',
                            duplicate_muid = '".$duplicate_muid."',
                            datetime = '".$date."'";
                            
                        if($DRW->query($sql, $DRW_main)){
                            unlink($file);                            
                        }
                    }                    
                }else{
                    echo 'Unable to copy '.$file.' to '.$duplicate_file;//die;
                }                  
            }
            //remove from step1
            $sql = "DELETE FROM cscan_email_sameday_search WHERE muid = '".$muid."'";
            $DRW->query($sql,$DRW_main);

            $sql = "DELETE FROM cscan_email_text_sameday_search WHERE muid = '".$muid."'";
            $DRW->query($sql,$DRW_main);

            $sql = "DELETE FROM cscan_email_file_sameday_search WHERE muid = '".$muid."'";
            $DRW->query($sql,$DRW_main);
        }
        $y++;
    }
    
}else{
    $error[] = 'Unable to read file: '.$duplicateCSV;
}