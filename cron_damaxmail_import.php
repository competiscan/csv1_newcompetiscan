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
################### Local functions #####################
function pr($str){
    echo '<pre>';
    print_r($str);
}
$serverMode = siteMode();
$arrOutput = $arrGlobalOutput = [];
$arrGlobalOutput['sitemode'] = $serverMode;
################### Parameters ######################
//initialization
$arrLog = array();
$arrLog[2] = date("Y-m-d H:i:s");

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
#########################################
$x = 1;
$outputData = $error = array();
$importFrom = dirname(__FILE__)."/damaxmailcsv/".$csvdate."/".$csvdate."_search_output.csv";
if (($handle = fopen($importFrom, "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        if($x > 1){
            $outputData[] = $data;
        }
        $x++;
    }
    fclose($handle);
}else{
    $error[] = 'Unable to read file: '.$importFrom;
}
$errorFatal = $errorNotice = array();
$flag = true;
//pr($outputData);die;
$totalCount = count($outputData);
if($totalCount>0){
    $check=1;
    $n = 0;
    $arrMuId = array();
    $counter = 0;
    foreach($outputData as $key=>$frow){
        $n++;
        $file = $frow[0];
        $last_mod = $frow[1];
        $status = $frow[2];
        $match_percentage = $frow[3];
        $dup_file = $frow[4];
        $new_file = str_replace('z:\\damaxmailhtml\\', dirname(__FILE__).'/damaxmailhtml/', $file);
        $new_file = str_replace("\\",'/',$new_file);

        if($status == 1 && !empty($dup_file)){
            //if($serverMode == 'demo'){
                //duplicate records
                $fileName = basename(str_replace("\\","/",$file));
                $filePart = explode("_",$fileName);
                $muid = current($filePart);
                $mailbox_uid = (!empty($filePart[1]))?trim($filePart[1]):0;
                $panelist_id = current(explode(".",end($filePart)));

                $dupfileName = end(explode("\\",$dup_file));
                $productID = end(explode("_",current(explode(".", $dupfileName))));

                if(!empty($panelist_id) && !empty($productID)){
                    if($mailbox_uid){
                        $sql = "SELECT id FROM cscan_damaxmail_duplicate WHERE productID='".$productID."' AND `filename` = '".$DRW->real_escape_string($new_file)."'";
                    }else{
                        $sql = "SELECT id FROM cscan_damaxmail_duplicate WHERE productID='".$productID."' AND `filename` = '".$DRW->real_escape_string($new_file)."'";
                    }
                    $q_chk_dup = $DRW->query($sql,$DRW_read);
                    if($DRW->num_rows($q_chk_dup) == 0){
                        $sql_p = "SELECT competi_id,parent_panelist_id,DATEDIFF(CURDATE(),birthdate) as agedays,stateID,gender,homeownershipID,incomeID,fico_score,contactTypeID,ownbiz,postalcode FROM cscan_panelists WHERE panelist_id='".$panelist_id."'";
                        //echo '$sql_p=> '.$sql_p.'</br>';
                        $q_p = $DRW->query($sql_p, $DRW_read);
                        if ($DRW->num_rows($q_p) > 0) {
                            $rs_p = $DRW->fetch_assoc($q_p);
                            //pr($rs_p);die;
                            $pprimary = (!empty($rs_p['parent_panelist_id']))?0:1;
                            $ppage = floor($rs_p['agedays']/365);
                            $ppstateID = $rs_p['stateID'];
                            $pgender = (!empty($rs_p['gender']))?strtoupper(substr(trim($rs_p['gender']),0,1)):'N';
                            $homeownershipID = $rs_p['homeownershipID'];
                            $pincomeID = $rs_p['incomeID'];
                            $ppfico_score = $rs_p['fico_score'];
                            $contactTypeID = $rs_p['contactTypeID'];
                            $ownbiz = (int)$rs_p['ownbiz'];
                            $pppostalcode = trim($rs_p['postalcode']);
                            if($contactTypeID==1){
                                $mPanelID = 4;
                            }elseif($contactTypeID==2){
                                $mPanelID = 1;
                            }
                            $ppdate = $last_mod;
                            ########## for add panelist ageid ################
                            $ageObj = new \HS\Age($DRW);
                            $ageObj->setAge($ppage);
                            $ppageID = $ageObj->getGroupsAsCommaDelimitedString($ppage);
                            $ppageID = trim($ppageID,"'");
                            ########## for add panelist ageid ################
                            //check duplicay for panelist_id+productID
                            $ppdate = date("Y-m-d H:i:s", strtotime($ppdate));
                            $sql_chk_p = "SELECT ppdate FROM cscan_panelists_product WHERE productID='".$productID."' AND panelist_id='".$panelist_id."' AND ppdate='".$ppdate."' ORDER BY ppaddeddate DESC LIMIT 1";
                            $q_chk_q = $DRW->query($sql_chk_p, $DRW_read);
                            /* ### Added for if same panelist and date exist then ppdate will be 1 day before */
                            if ($DRW->num_rows($q_chk_q)>0) {
                                //$ppdate = date("Y-m-d", strtotime($ppdate)).' '.date("H:i:s");
                            }else{
                                $sql_pp = "INSERT IGNORE INTO cscan_panelists_product
                                    SET productID = '".$productID."',
                                        panelist_id = '".$panelist_id."',
                                        ppdate = '".$DRW->real_escape_string($ppdate)."',
                                        ppage = '".$ppage."',
                                        ppstateID = '".$ppstateID."',
                                        pgender = '".$pgender."',
                                        homeownershipID = '".$homeownershipID."',
                                        pincomeID = '".$pincomeID."',
                                        ppageID = '".$ppageID."',
                                        ppfico_score = '".$ppfico_score."',
                                        isBiz = '".$ownbiz."',
                                        pppostalcode = '".$DRW->real_escape_string($pppostalcode)."',
                                        ppaddeddate = NOW(),
                                        pprimary = '".$pprimary."'";

                                if(!$DRW->query($sql_pp,$DRW_main)){
                                    $error[] = 'Unable to to execute query: '.$sql_pp;
                                }
                            }

                            //check for digital pending
                            $isDigital = $panelist_sort = 0;
                            $sql_digital = "SELECT mChannelID FROM cscan_product_detail WHERE FIND_IN_SET($panelist_id, panelist_id) AND productID = '".$DRW->real_escape_string($productID)."' AND mChannelID IN(5,9,10)";

                            $query_digital = $DRW->query($sql_digital, $DRW_read);
                            if($DRW->num_rows($query_digital) > 0){
                                $isDigital = 1;
                                $panelist_sort = 1;
                            }
                            // concat panelist_id
                            $arrPanelists = array();
                            $arrAge = $arrAge2 = $arrIncomeID = $arrIncomeID2 = array();
                            $strAge = $ppageID;
                            $strIncomeID = $pincomeID;

                            $panelists  =    $panelist_id;
                            $strStateID =    $ppstateID;
                            //echo $sql_pp;die;

                            /*
                            $cpd_panelist = $DRW->query("SELECT panelist_id,age,incomeID FROM cscan_product_detail WHERE productID = '".$productID."'",$DRW_read);

                            if($DRW->num_rows($cpd_panelist) > 0){
                                $rs_cpd_panelist = $DRW->fetch_assoc($cpd_panelist);
                                $arrPanelists = explode(",", $rs_cpd_panelist['panelist_id']);
                                $arrAge2 = explode(",",$rs_cpd_panelist['age']);
                                $arrPpage = explode(",", $ppageID);
                                $arrAge = array_merge($arrAge2, $arrPpage);
                                $arrAge = array_unique($arrAge);
                                $strAge = implode(",", $arrAge);

                                $arrIncomeID2 = explode(",",$rs_cpd_panelist['incomeID']);
                                $arrPincomeID = explode(",", $pincomeID);
                                $arrIncomeID = array_merge($arrIncomeID2, $arrPincomeID);
                                $arrIncomeID=array_unique($arrIncomeID);
                                $strIncomeID = implode(",", $arrIncomeID);
                            }
                            array_push($arrPanelists, $panelist_id);
                            $arrPanelists = array_unique($arrPanelists);
                            $panelists = implode(",", $arrPanelists);
                            */



                            #################### for duplicate productid #############################3
                            $cpd_panelist = $DRW->query("SELECT panelist_id,age,incomeID,state FROM cscan_product_detail WHERE productID = '".$productID."'",$DRW_read);
                            if($DRW->num_rows($cpd_panelist) > 0){
                                $rs_cpd_panelist = $DRW->fetch_assoc($cpd_panelist);
                                if(!empty($rs_cpd_panelist['panelist_id'])){
                                    $arrPanelists = explode(",", $rs_cpd_panelist['panelist_id']);
                                    if(!empty($panelist_id))
                                        array_push($arrPanelists, $panelist_id);
                                    $arrPanelists = array_unique($arrPanelists);
                                    $panelists = implode(",", $arrPanelists);
                                }
                                if(!empty($rs_cpd_panelist['age'])){
                                    $arrAge2 = explode(",",$rs_cpd_panelist['age']);
                                    if(!empty($ppageID))
                                        array_push($arrAge2,$ppageID);
                                    $arrAge2 = array_unique($arrAge2);
                                    $strAge = implode(",", $arrAge2);
                                }
                                if(!empty($rs_cpd_panelist['incomeID'])){
                                    $arrIncomeID2 = explode(",",$rs_cpd_panelist['incomeID']);
                                    if(!empty($pincomeID))
                                         array_push($arrIncomeID2,$pincomeID);
                                    $arrIncomeID2 = array_unique($arrIncomeID2);
                                    $strIncomeID = implode(",", $arrIncomeID2);
                                }
                                //if(!empty($rs_cpd_panelist['state'])){
                                    $panres =   $DRW->query("SELECT ppstateID FROM cscan_panelists_product WHERE productID = '".$productID."'",$DRW_read);
                                    $panarray   =   array();
                                    while ($row = $DRW->fetch_array($panres)) {
                                        $panarray[] = $row['ppstateID'];
                                    }
                                    if(!empty($panarray)){
                                    $strStateID =   implode(",",array_unique($panarray));
                                    }
//                                    $arrStateID2 = explode(",",$rs_cpd_panelist['state']);
//                                    if(!empty($ppstateID))
//                                        array_push($arrStateID2,$ppstateID);
//                                    $arrStateID2 = array_unique($arrStateID2);
//                                    $strStateID = implode(",", $arrStateID2);
                               // }

                            }



                            $sql_pd = "UPDATE cscan_product_detail
                                SET panelist_id = '".$panelists."',
                                panelist_sort = '".$panelist_sort."',
                                age = '". $strAge ."',
                                state='".$strStateID."',
                                incomeID = '".$strIncomeID."'
                                WHERE productID = '".$productID."'";
                            //echo '$sql_pd => '.$sql_pd.'</br>';
                            if($DRW->query($sql_pd, $DRW_main)){
                                // make entry in new transaction table
                                $del_file = str_replace('z:\\damaxmailhtml\\', dirname(__FILE__).'/damaxmailhtml_duplicate/', $file);
                                $del_file = str_replace("\\",'/',$del_file);

                                $sql = "SELECT id FROM cscan_damaxmail_duplicate WHERE productID='".$productID."' AND filename='".$DRW->real_escape_string($new_file)."'";
                                $q_chk_dup = $DRW->query($sql,$DRW_read);
                                if($DRW->num_rows($q_chk_dup) > 0){
                                    $error[] = 'Duplicate already exit: '.$sql;
                                }else{
                                    $arr1 = explode("/",$del_file);
                                    array_pop($arr1);
                                    $dup_path = implode("/",$arr1);
                                    if(!is_dir($dup_path)){
                                        if(mkdir($dup_path,0777,true)){
                                        }else{
                                            echo $dup_path;//die;
                                        }
                                        @chmod($dup_path,0777);
                                        @chown($dup_path,'apache');
                                    }
                                    // move or delete from damaxmailhtml to damaxmailhtml_duplicte
                                    if(!rename($new_file, $del_file)){
                                        $error[] = 'Unable to move file from: '.$new_file.' to: '.$del_file;
                                    }else{
                                        $approved_file = str_replace("\\","/",str_replace("z:\\", dirname(__FILE__).'/', $dup_file));
                                        $sql = "SELECT id FROM cscan_damaxmail_duplicate WHERE muid = '".$muid."'";
                                        $query1 = $DRW->query($sql,$DRW_read);
                                        if($DRW->num_rows($query1) == 0){
                                            $sql_trns = "INSERT IGNORE INTO cscan_damaxmail_duplicate SET
                                            muid = '".$muid."',
                                            mailbox_uid = '".$mailbox_uid."',
                                            productID = '".$productID."',
                                            panelist_id = '".$panelist_id."',
                                            percentage_match = '".$match_percentage."',
                                            `filename` = '".$DRW->real_escape_string($new_file)."',
                                            duplicate_filename = '".$DRW->real_escape_string($del_file)."',
                                            approved_file = '".$DRW->real_escape_string($approved_file)."',
                                            `datetime` = NOW()";
                                            if($DRW->query($sql_trns, $DRW_main)){
                                                if($mailbox_uid){
                                                    $sql = "SELECT email_date FROM cscan_email_sameday_search WHERE mailbox_uid = '".$mailbox_uid."'";
                                                }else{
                                                    $sql = "SELECT email_date FROM cscan_email_sameday_search WHERE muid = '".$muid."'";
                                                }
                                                $query2 = $DRW->query($sql,$DRW_read);
                                                if($DRW->num_rows($query2) > 0){
                                                    if($mailbox_uid){
                                                        $sql = "INSERT IGNORE INTO cscan_email_dup (SELECT * FROM cscan_email_sameday_search WHERE mailbox_uid = '".$mailbox_uid."')";
                                                        if($DRW->query($sql,$DRW_main)){
                                                            $DRW->query("DELETE FROM cscan_email_sameday_search WHERE mailbox_uid = '".$mailbox_uid."'",$DRW_main);
                                                        }
                                                    }else{
                                                        $sql = "INSERT IGNORE INTO cscan_email_dup (SELECT * FROM cscan_email_sameday_search WHERE muid = '".$muid."')";
                                                        if($DRW->query($sql,$DRW_main)){
                                                            $DRW->query("DELETE FROM cscan_email_sameday_search WHERE muid = '".$muid."'",$DRW_main);
                                                        }
                                                    }
                                                }
                                                if(!empty($muid)){
                                                    $sql = "SELECT cetid FROM cscan_email_text_sameday_search WHERE muid = '".$muid."'";
                                                    $query3 = $DRW->query($sql,$DRW_read);
                                                    if($DRW->num_rows($query3) > 0){
                                                        $sql = "INSERT IGNORE INTO cscan_email_text_dup (SELECT * FROM cscan_email_text_sameday_search WHERE muid = '" . $muid . "')";
                                                        $DRW->query($sql,$DRW_main);
                                                        $DRW->query("DELETE FROM cscan_email_text_sameday_search WHERE muid = '".$muid."'",$DRW_main);
                                                    }

                                                    $sql = "SELECT cefid FROM cscan_email_file_sameday_search WHERE muid = '".$muid."'";
                                                    $query4 = $DRW->query($sql,$DRW_read);
                                                    if($DRW->num_rows($query4) > 0){
                                                        $sql = "INSERT IGNORE INTO cscan_email_file_dup (SELECT * FROM cscan_email_file_sameday_search WHERE muid = '".$muid."')";
                                                        $DRW->query($sql,$DRW_main);
                                                        $DRW->query("DELETE FROM cscan_email_file_sameday_search WHERE muid = '".$muid."'",$DRW_main);
                                                    }
                                                }else{
                                                    $error[] = 'muid not available';
                                                }
                                            }
                                        }
                                        //remove old file
                                        if(file_exists($new_file)){
                                            unlink($new_file);
                                        }
                                    }
                                }
                            }else{
                                $error[] = 'Unable to to execute query: '.$sql;
                            }
                        }else{
                            $error[] = 'Panelist not found: '.$sql_p;
                        }
                    }else{
                        $error[] = 'Already exits duplicate: '.$sql;
                    }
                }elseif(!empty($productID)){
                    // make entry in new transaction table
                    $del_file = str_replace('z:\\damaxmailhtml\\', dirname(__FILE__).'/damaxmailhtml_duplicate/', $file);
                    $del_file = str_replace("\\",'/',$del_file);
                    $q_chk_dup = $DRW->query("SELECT id FROM cscan_damaxmail_duplicate WHERE productID='".$productID."' AND filename='".$DRW->real_escape_string($new_file)."'",$DRW_read);
                    if($DRW->num_rows($q_chk_dup) == 0){
                        $arr1 = explode("/",$del_file);
                        array_pop($arr1);
                        $dup_path = implode("/",$arr1);
                        if(!is_dir($dup_path)){
                            if(mkdir($dup_path,0777,true)){
                            }else{
                                echo $dup_path;//die;
                            }
                            @chmod($dup_path,0777);
                            @chown($dup_path,'apache');
                        }
                        // move or delete from dachicagorecordsftp
                        if(!rename($new_file, $del_file)){
                            $error[] = 'Unable to move file from: '.$new_file.' to: '.$del_file;
                        }else{
                            $approved_file = str_replace("\\","/",str_replace("z:\\", dirname(__FILE__).'/', $dup_file));
                            $sql = "SELECT id FROM cscan_damaxmail_duplicate WHERE muid = '".$muid."'";
                            $query1 = $DRW->query($sql,$DRW_read);
                            if($DRW->num_rows($query1) == 0){
                                $sql_trns = "INSERT INTO cscan_damaxmail_duplicate SET
                                muid = '".$muid."',
                                mailbox_uid = '".$mailbox_uid."',
                                productID = '".$productID."',
                                panelist_id = '".$panelist_id."',
                                percentage_match = '".$match_percentage."',
                                `filename` = '".$DRW->real_escape_string($new_file)."',
                                duplicate_filename = '".$DRW->real_escape_string($del_file)."',
                                approved_file = '".$DRW->real_escape_string($approved_file)."',
                                `datetime` = NOW()";
                                if($DRW->query($sql_trns, $DRW_main)){
                                    if($mailbox_uid){
                                        $sql = "SELECT email_date FROM cscan_email_sameday_search WHERE mailbox_uid = '".$mailbox_uid."'";
                                    }else{
                                        $sql = "SELECT email_date FROM cscan_email_sameday_search WHERE muid = '".$muid."'";
                                    }
                                    $query2 = $DRW->query($sql,$DRW_read);
                                    if($DRW->num_rows($query2) > 0){
                                        if($mailbox_uid){
                                            $sql = "INSERT IGNORE INTO cscan_email_dup (SELECT * FROM cscan_email_sameday_search WHERE mailbox_uid = '".$mailbox_uid."')";
                                            if($DRW->query($sql,$DRW_main)){
                                                $DRW->query("DELETE FROM cscan_email_sameday_search WHERE mailbox_uid = '".$mailbox_uid."'",$DRW_main);
                                            }
                                        }else{
                                            $sql = "INSERT IGNORE INTO cscan_email_dup (SELECT * FROM cscan_email_sameday_search WHERE muid = '".$muid."')";
                                            if($DRW->query($sql,$DRW_main)){
                                                $DRW->query("DELETE FROM cscan_email_sameday_search WHERE muid = '".$muid."'",$DRW_main);
                                            }
                                        }
                                    }
                                    if(!empty($muid)){
                                        $sql = "SELECT cetid FROM cscan_email_text_sameday_search WHERE muid = '".$muid."'";
                                        $query3 = $DRW->query($sql,$DRW_read);
                                        if($DRW->num_rows($query3) > 0){
                                            $sql = "INSERT IGNORE INTO cscan_email_text_dup (SELECT * FROM cscan_email_text_sameday_search WHERE muid = '" . $muid . "')";
                                            $DRW->query($sql,$DRW_main);
                                            $DRW->query("DELETE FROM cscan_email_text_sameday_search WHERE muid = '".$muid."'",$DRW_main);
                                        }

                                        $sql = "SELECT cefid FROM cscan_email_file_sameday_search WHERE muid = '".$muid."'";
                                        $query4 = $DRW->query($sql,$DRW_read);
                                        if($DRW->num_rows($query4) > 0){
                                            $sql = "INSERT IGNORE INTO cscan_email_file_dup (SELECT * FROM cscan_email_file_sameday_search WHERE muid = '".$muid."')";
                                            $DRW->query($sql,$DRW_main);
                                            $DRW->query("DELETE FROM cscan_email_file_sameday_search WHERE muid = '".$muid."'",$DRW_main);
                                        }
                                    }else{
                                        $error[] = 'muid not available';
                                    }
                                }
                            }
                            if(file_exists($new_file)){
                                unlink($new_file);
                            }
                        }
                    }else{
                        $error[] = 'Already exits duplicate: '.$sql;
                    }
                }else{
                    $error[] = 'ProductID not available';
                }
            //}
        }else{
            $fileName = basename(str_replace("\\","/",$file));
            $filePart = explode("_",$fileName);
            $muid = current($filePart);
            $mailbox_uid = (!empty($filePart[1]))?$filePart[1]:0;

            if(!empty($muid)){
                    $sql = "SELECT mailbox_uid FROM cscan_email_sameday_search WHERE muid = '".$muid."'";
                    $query1 = $DRW->query($sql,$DRW_read);
                    if($DRW->num_rows($query1) > 0){
                        $fetch = $DRW->fetch_assoc($query1);
                        $mailbox_uid = $fetch['mailbox_uid'];

                        $sql = "SELECT email_date FROM cscan_email WHERE mailbox_uid = '".$mailbox_uid."'";
                        $query2 = $DRW->query($sql,$DRW_read);
                        if($DRW->num_rows($query2) == 0){
                            $sql = "INSERT INTO cscan_email(email_date,email_to,email_from,email_subject,contact_type_m_c,deleted,email_cc,email_bcc,panelist_score,mailbox_uid,email_read,email_from_one,panelist_id,e_assigned_admin_userID,panelist_core,email_stateID,is_text_file,from_sent_name,from_sent_email_address,from_sent_date,from_sent_date_format,is_fetch,isnamereplace) (SELECT email_date,email_to,email_from,email_subject,contact_type_m_c,deleted,email_cc,email_bcc,panelist_score,mailbox_uid,email_read,email_from_one,panelist_id,e_assigned_admin_userID,panelist_core,email_stateID,is_text_file,from_sent_name,from_sent_email_address,from_sent_date,from_sent_date_format,is_fetch,isnamereplace FROM cscan_email_sameday_search WHERE mailbox_uid = '".$mailbox_uid."')";
                            if($DRW->query($sql,$DRW_main)){
                                $origional_muid = $DRW->insert_id($DRW_main);
                                $sql = "SELECT cetpart,cettext,cettype,muid,cetidentification FROM cscan_email_text_sameday_search WHERE muid = '".$muid."'";
                                $query3 = $DRW->query($sql,$DRW_read);
                                if($DRW->num_rows($query3) > 0){
                                    while($row = $DRW->fetch_assoc($query3)){
                                        $sql = "INSERT INTO cscan_email_text SET
                                                cetpart = '".$DRW->real_escape_string($row['cefpart'])."',
                                                cettext = '".$DRW->real_escape_string($row['cettext'])."',
                                                cettype = '".$DRW->real_escape_string($row['cettype'])."',
                                                muid = '".$origional_muid."',
                                                cetidentification = '".$DRW->real_escape_string($row['cetidentification'])."'";
                                        if($DRW->query($sql,$DRW_main)){
                                            $sql = "SELECT cefpart,cefdata,cefname,ceftype,muid,cefidentification,cefdisposition,cefencoding,cefsplit,cefpath FROM cscan_email_file_sameday_search WHERE muid = '".$muid."'";
                                            $query4 = $DRW->query($sql,$DRW_read);
                                            if($DRW->num_rows($query4) > 0){
                                                while($row = $DRW->fetch_assoc($query4)){
                                                    $sql = "INSERT INTO cscan_email_file SET
                                                        cefpart = '".$DRW->real_escape_string($row['cefpart'])."',
                                                        cefdata = '".$DRW->real_escape_string($row['cefdata'])."',
                                                        cefname = '".$DRW->real_escape_string($row['cefname'])."',
                                                        ceftype = '".$DRW->real_escape_string($row['ceftype'])."',
                                                        muid = '".$origional_muid."',
                                                        cefidentification = '".$DRW->real_escape_string($row['cefidentification'])."',
                                                        cefdisposition = '".$DRW->real_escape_string($row['cefdisposition'])."',
                                                        cefencoding = '".$DRW->real_escape_string($row['cefencoding'])."',
                                                        cefsplit = '".$DRW->real_escape_string($row['cefsplit'])."',
                                                        cefpath = '".$DRW->real_escape_string($row['cefpath'])."'";
                                                    if($DRW->query($sql,$DRW_main)){
                                                        $DRW->query("DELETE FROM cscan_email_file_sameday_search WHERE muid = '".$muid."'",$DRW_main);

                                                    }
                                                }
                                            }
                                        }
                                    }
                                    $DRW->query("DELETE FROM cscan_email_text_sameday_search WHERE muid = '".$muid."'",$DRW_main);
                                    if($DRW->query("DELETE FROM cscan_email_sameday_search WHERE muid = '".$muid."'",$DRW_main)){
                                        $counter++;
                                    }
                                }
                            }
                        }
                    }
                    if(file_exists($new_file)){
                        unlink($new_file);
                    }
            }else{
                $error[] = 'muid not available';
            }

            //import to live site for files only: when not included in output csv
            $sql = "SELECT muid FROM cscan_email_file_sameday_search";
            $query1 = $DRW->query($sql,$DRW_read);
            if($DRW->num_rows($query1) > 0){
                while($res = $DRW->fetch_assoc($query1)){
                    $muid = $res['muid'];
                    $sql = "SELECT mailbox_uid FROM cscan_da_maxmail_html_sameday_search WHERE muid = '".$muid."'";
                    $query2 = $DRW->query($sql,$DRW_read);
                    if($DRW->num_rows($query2) > 0){
                        $res = $DRW->fetch_assoc($query2);
                        $mailbox_uid = $res['mailbox_uid'];

                        $sql = "SELECT muid FROM cscan_email WHERE mailbox_uid = '".$mailbox_uid."'";
                        $query3 = $DRW->query($sql,$DRW_read);
                        if($DRW->num_rows($query3) > 0){
                            $res2 = $DRW->fetch_assoc($query3);
                            $origional_muid = $res2['muid'];
                            $sql = "SELECT cefpart,cefdata,cefname,ceftype,muid,cefidentification,cefdisposition,cefencoding,cefsplit,cefpath FROM cscan_email_file_sameday_search WHERE muid = '".$muid."'";
                            $query4 = $DRW->query($sql,$DRW_read);
                            if($DRW->num_rows($query4) > 0){
                                while($row = $DRW->fetch_assoc($query4)){
                                    $sql = "INSERT INTO cscan_email_file SET
                                            cefpart = '".$DRW->real_escape_string($row['cefpart'])."',
                                            cefdata = '".$DRW->real_escape_string($row['cefdata'])."',
                                            cefname = '".$DRW->real_escape_string($row['cefname'])."',
                                            ceftype = '".$DRW->real_escape_string($row['ceftype'])."',
                                            muid = '".$DRW->real_escape_string($origional_muid)."',
                                            cefidentification = '".$DRW->real_escape_string($row['cefidentification'])."',
                                            cefdisposition = '".$DRW->real_escape_string($row['cefdisposition'])."',
                                            cefencoding = '".$DRW->real_escape_string($row['cefencoding'])."',
                                            cefsplit = '".$DRW->real_escape_string($row['cefsplit'])."',
                                            cefpath = '".$DRW->real_escape_string($row['cefpath'])."'";
                                    $DRW->query($sql,$DRW_main);
                                }
                                $DRW->query("DELETE FROM cscan_email_file_sameday_search WHERE muid = '".$muid."'",$DRW_main);
                            }
                        }

                    }

                    $DRW->query("DELETE FROM cscan_email_file_sameday_search WHERE muid = '".$muid."'",$DRW_main);
                    $DRW->query("DELETE FROM cscan_email_sameday_search WHERE muid = '".$muid."'",$DRW_main);
                }
            }
        }
    }
    if($counter>0){
        $sql = "SELECT id FROM cscan_maxmail_sameday_log WHERE `status`='2' ORDER BY id DESC LIMIT 1";
        $querychk1 = $DRW->query($sql,$DRW_read);
        if($DRW->num_rows($querychk1) > 0){
            $lRes = $DRW->fetch_assoc($querychk1);
            $msd_logid = $lRes['id'];
            if($msd_logid){
                $sql = "UPDATE cscan_maxmail_sameday_log SET `email_imported`='".$counter."', `status`='3' WHERE id='".$msd_logid."'";
                $DRW->query($sql, $DRW_main);
            }
        }
    }
}else{
    $error[] = 'Search output file: '.$importFrom.' has no data to be imported.';
}
if(count($error)>0){
    //LOGS
    $arrLog[0] = 'ImportToComptiscan(c)';
    $arrLog[1] = date('Y-m-d')."_search_output.csv";
    $arrLog[3] = date("Y-m-d H:i:s");
    $arrLog[4] = current($error);
    if(!empty($arrLog)){
        ksort($arrLog);
        $localLog = dirname(__FILE__)."/damaxmailcsv/".$csvdate."/log.csv";
        updateLog($arrLog,$localLog);
        $globalLog = dirname(__FILE__)."/damaxmailcsv/".date('Y-m',strtotime($csvdate))."_daLog.csv";
        updateLog($arrLog,$globalLog);
    }
    pr($error);
}else{
    //LOGS
    $arrLog[0] = 'ImportToComptiscan(c)';
    $arrLog[1] = date('Y-m-d')."_search_output.csv";
    $arrLog[3] = date("Y-m-d H:i:s");
    $arrLog[4] = 'Success';
    if(!empty($arrLog)){
        ksort($arrLog);
        $localLog = dirname(__FILE__)."/damaxmailcsv/".$csvdate."/log.csv";
        updateLog($arrLog,$localLog);
        $globalLog = dirname(__FILE__)."/damaxmailcsv/".date('Y-m',strtotime($csvdate))."_daLog.csv";
        updateLog($arrLog,$globalLog);
    }
    echo '<h2>Success</h2>';
}
echo '</br></br>End: '.date("Y-m-d H:i:s");
die;
