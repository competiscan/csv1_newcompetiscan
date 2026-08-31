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
//require_once "Mail.php";
//require_once "Mail/mime.php";
########################################
//initialization
$arrLog = array();
$arrLog[2] = date("Y-m-d H:i:s");
#########################################
echo 'Start: '.date("Y-m-d H:i:s").'</br></br>';

$AUTH_DATA['userID'] = 0;
function pr($str){
    echo '<pre>';print_r($str);
}
//update index_input.csv for previous date by passing integer for back date
$day = (!empty($_GET['d']))?trim($_GET['d']):'';
if($_SERVER['argc']>0) {
    $day = $_SERVER['argv'][1];
}
$day = (int)$day;
//if(empty($day))$day=1;
if(!empty($day)){
    $csvdate = date("Y-m-d", strtotime(" -$day day"));
}else{
    $csvdate = date("Y-m-d");
}
#########################################
$x = 1;
$outputData = array();
$importFrom = dirname(__FILE__)."/dacsv/".$csvdate."/".$csvdate."_search_output.csv";

if (($handle = fopen($importFrom, "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        if($x > 1){
            $outputData[] = $data;
        }
        $x++;
    }
    fclose($handle);
}else{
    echo 'Unable to read file: '.$importFrom;
}
$errorFatal = $errorNotice = array();
$flag = true;
//echo '<pre>';
//print_r($outputData); die;
$totalCount = count($outputData);
$dup = $new = 0;
if($totalCount>0){
    ########################
    $yearpath = date('Y/');
    $monthpath = date('m/');
    $daypath = date('d/');
    $pathpart = dirname(__FILE__)."/PDF/crm/";
    ###### Changes for S3 bucket ####################
    $pathpart = "PDF/crm/";
    ###### end Changes for S3 bucket ####################
    if(!is_dir($pathpart.$yearpath)){
        mkdir($pathpart.$yearpath,02755);
        @chmod($pathpart.$yearpath,02755);
        @chown($pathpart.$yearpath,'apache');
        //@chgrp($pathpart.$yearpath,'competiscan_web');
    }
    if(!is_dir($pathpart.$yearpath.$monthpath)){
        mkdir($pathpart.$yearpath.$monthpath,02755);
        @chmod($pathpart.$yearpath.$monthpath,02755);
        @chown($pathpart.$yearpath.$monthpath,'apache');
        //@chgrp($pathpart.$yearpath.$monthpath,'competiscan_web');
    }
    if(!is_dir($pathpart.$yearpath.$monthpath.$daypath)){
        mkdir($pathpart.$yearpath.$monthpath.$daypath,02755);
        @chmod($pathpart.$yearpath.$monthpath.$daypath,02755);
        @chown($pathpart.$yearpath.$monthpath.$daypath,'apache');
        //@chgrp($pathpart.$yearpath.$monthpath,'competiscan_web');
    }
    $local_dir = $pathpart.$yearpath.$monthpath.$daypath;
        
    ######################
    $parr = array();
    $sql = "SELECT age_pID,age_pmin FROM cscan_age_product ORDER BY age_psort";
    $result = $DRW->query($sql,$DRW_read2);
    while( $row = $DRW->fetch_row( $result ) ){
        $parr[$row[0]] = $row[1];
    }
    @$DRW->free_result($result);
    
    ######################
    $root_dirs = array('./dachicagorecordsftp');
    $processed_time = time()-345600;
    
    ######################
    $check=1;
    $n = 0;
    foreach($outputData as $key=>$frow){
        $n++;
        
        $file = $frow[0];    
        $last_mod = $frow[1]; 
        $status = $frow[2]; 
        $da_id = $frow[5]; 
        //echo '$last_mod=> '.$last_mod.'</br>';
        $match_percentage = $frow[3]; 
        $local_dup_file = (!empty($frow[4]))?$frow[4]:"";
        //echo '$local_dup_file=> '.$local_dup_file.'</br>';
        $new_file = str_replace('z:\\dachicagorecordsftp\\', dirname(__FILE__).'/dachicagorecordsftp/', $file);
        //echo '$new_file=> '.$new_file.'</br>'; die;
        $approved_file = (!empty($local_dup_file))?str_replace('z:\\', dirname(__FILE__).'/', $local_dup_file):"";
        $approved_file = str_replace('\\', '/',$approved_file);
        //echo '$approved_file=> '.$approved_file.'</br>'; die;
        if($status == 1 && $approved_file == ''){
            //continue;
        }
       
//        $approvedfilepath   =   str_replace(dirname(__FILE__),'',$approved_file);
//        $info               =   false;
//        if(!empty($approvedfilepath)){
//          $info = $s3->doesObjectExist($bucket_name,substr($approvedfilepath,1) );
//       }

        //echo $approved_file; die;
            if(file_exists($approved_file)){
            $dup++;
            // duplicate file
            $dup_file = basename($approved_file);
            $dup_file = rtrim($dup_file,'_');
            $productID = current(explode(".",end(explode("_",$dup_file))));
            //echo '$productID=> '.$productID.'</br>'; die;
            
            
            if(!empty($productID)){
                //echo "SELECT id FROM cscan_product_detail_duplicate WHERE productID='".$productID."' AND filename='".$DRW->real_escape_string($file)."'"; die;
                 $q_chk_dup = $DRW->query("SELECT id FROM cscan_product_detail_duplicate WHERE productID='".$productID."' AND filename='".$DRW->real_escape_string($file)."'",$DRW_read2);
                if($DRW->num_rows($q_chk_dup) == 0){
                
                    //echo $productID;die;
                    $sql_check = "SELECT productName FROM cscan_product_detail WHERE productID = '".$productID."'";
                    //echo '$sql_check=> '.$sql_check.'</br>';
                    //die;
                   // echo $GLOBALS['da_id']; die;
                    $rs_check = $DRW->query($sql_check, $DRW_read2);
                    if ($DRW->num_rows($rs_check) > 0) { //echo 'aaa'.$file;die;
                        if(!empty($GLOBALS['da_id'])){
                            $sql_da = "SELECT filename FROM da_chicagorecords WHERE id='".$GLOBALS['da_id']."'";
                            $rs_da = $DRW->query($sql_da,$DRW_read2);
                            if($DRW->num_rows($rs_da)>0){
                                $data_da = $DRW->fetch_assoc($rs_da);
                                $ofilename = $data_da['filename'];
                                //echo $ofilename; //die;
                                if(preg_match('/\\/(\\d+\\-\\d+\\-\\d+)\\/([^\\/]+)\\/([^\\/]+)\\//',$ofilename,$matches)){
                                    //pr($matches); die;
                                    $scan_date = $matches[1]; 
                                    $DMSource = $matches[2]; 
                                    $operator = $matches[3];
                                }elseif(preg_match('/\\/([^\\/]+)$/',dirname($ofilename),$matches)){
                                    $DMSource = $matches[1];
                                }else {
                                    $DMSource = basename($ofilename);
                                }
                            }else{
                                $DMSource = basename($file);
                            }
                        }else{
                            $DMSource = basename($file);                    
                        }        
                        $DMSource = preg_replace('/_+/', '_', $DMSource);
                        
                        echo $DMSource; //die;
                        if(preg_match('/(\\d+)_(\\d+)_(\\d+)/', $DMSource, $matches)){ 
                            //pr($matches); die;
                            // or from $DMSource should be from da_chicagorecords's filename
                            $competi_id = trim($matches[1]).'-'.trim($matches[2]).'-'.trim($matches[3]);
                            //pr($competi_id);die;
                            $sql_p = "SELECT panelist_id,parent_panelist_id,DATEDIFF(CURDATE(),birthdate) as agedays,stateID,gender,homeownershipID,incomeID,fico_score,contactTypeID,ownbiz,postalcode FROM cscan_panelists WHERE competi_id='".$competi_id."'";
                            //echo '$sql_p=> '.$sql_p.'</br>';die;
                            $q_p = $DRW->query($sql_p, $DRW_read2);
                            if ($DRW->num_rows($q_p) > 0) { //echo 'mmmmm'; die;
                                $rs_p = $DRW->fetch_assoc($q_p);//pr($rs_p);die;
                                $panelist_id = $rs_p['panelist_id'];
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
                                
                                
                                
                                // fetch ppdate from cscan_panelists_product & compare with current_ftp date
                                $sql_ppdate = "SELECT ppdate FROM cscan_panelists_product WHERE productID='".$productID."' ORDER BY ppdate DESC LIMIT 1";
                                $q_ppdate = $DRW->query($sql_ppdate, $DRW_read2);
                                if ($DRW->num_rows($q_ppdate) > 0) {
                                    $rs_ppdate =  $DRW->fetch_assoc($q_ppdate);
                                    $prev_ppdate = date("Y-m-d", strtotime($rs_ppdate['ppdate']));
                                    //$last_mod = date("Y-m-d", strtotime($prev_ppdate));
                                    $ppdate = $prev_ppdate;
                                    //echo '$ppdate=> '.$ppdate.'</br>';
                                    $diff = dateDiff($last_mod,$prev_ppdate);
                                    if($diff > 20){
                                        $ppdate = date("Y-m-d H:i:s", strtotime($last_mod." -20 day"));
                                    }
                                }
    //                            else{
    //                                $flag = true;
    //                                $errorNotice[$key] = 'MYSQL Error: '.$sql_ppdate;
    //                            }

                                //check duplicay for panelist_id+productID
                                $ppdate = date("Y-m-d H:i:s", strtotime($ppdate));
                                $sql_chk_p = "SELECT ppdate FROM cscan_panelists_product WHERE productID='".$productID."' AND panelist_id='".$panelist_id."' AND ppdate='".$ppdate."' ORDER BY ppaddeddate DESC LIMIT 1";
                                $q_chk_q = $DRW->query($sql_chk_p, $DRW_read2);
                                //echo '$sql_chk_p=> '.$sql_chk_p.'</br>';
                                ### Added for if same panelist and date exist then ppdate will be 1 day before 
                                if ($DRW->num_rows($q_chk_q)>0) {
                                    //da$ppdate = date("Y-m-d", strtotime($ppdate)).' '.date("H:i:s");
//                                    $rs_chk_p =  $DRW->fetch_assoc($q_chk_q);
//                                    $rs_chk_p_ppdate=$rs_chk_p['ppdate'];                                                                     
//                                    $ppdate = date("Y-m-d H:i:s", strtotime($rs_chk_p_ppdate." -1 day")); 
                                }else{
                                 ##### End Added for if same panelist and same date exist then ppdate will be 1 day before ####
                                
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
                                    //echo '$sql_pp=> '.$sql_pp.'</br>';
                                    if($DRW->query($sql_pp,$DRW_main)){
                                        $flag = true;
                                    }else{
                                        $flag = false;
                                        $errorFatal[$key] = 'MYSql Error: '.$sql_pp;
                                    }
                                }

                              
                                //check for digital pending
                                $isDigital = $panelist_sort = 0;
                                $sql_digital = "SELECT mChannelID FROM cscan_product_detail WHERE FIND_IN_SET($panelist_id, panelist_id) AND productID = '".$DRW->real_escape_string($productID)."' AND mChannelID IN(5,9,10)";
                                $query_digital = $DRW->query($sql_digital, $DRW_read2);
                                if($DRW->num_rows($query_digital) > 0){
                                    $isDigital = 1;
                                    $panelist_sort = 1;
                                }
                                // concat panelist_id
                                $arrPanelists = array();
                                $arrAge = $arrAge2 = $arrIncomeID = $arrIncomeID2 = array();
                                $strAge = $ppageID;
                                $strIncomeID = $pincomeID;
                                $strStateID =    $ppstateID;
                                //echo $sql_pp;die;
                                $cpd_panelist = $DRW->query("SELECT panelist_id,age,incomeID,state FROM cscan_product_detail WHERE productID = '".$productID."'",$DRW_read2);
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
                                    
                                    $panres =   $DRW->query("SELECT ppstateID FROM cscan_panelists_product WHERE productID = '".$productID."'",$DRW_read2);
                                    $panarray   =   array();
                                    while ($rowpan = $DRW->fetch_array($panres)) {
                                        $panarray[] = $rowpan['ppstateID'];
                                    }
                                    if(!empty($panarray)){
                                    $strStateID =   implode(",",array_unique($panarray));
                                    }
                                    
                                }
                                array_push($arrPanelists, $panelist_id);
                                $arrPanelists = array_unique($arrPanelists);
                                $panelists = implode(",", $arrPanelists);

                                $sql_pd = "UPDATE cscan_product_detail
                                    SET panelist_id = '".$panelists."',
                                    panelist_sort = '".$panelist_sort."',
                                    is_digital = '".$isDigital."',
                                    age = '". $strAge ."',
                                    state = '". $strStateID ."',    
                                    incomeID = '".$strIncomeID."'
                                    WHERE productID = '".$productID."'";
                                //echo $sql_pd;die;
                                // echo '$sql_pd=> '.$sql_pd.'</br>';
                                if($DRW->query($sql_pd, $DRW_main)){
                                    // make entry in new transaction table                                            
                                    $del_file = str_replace('z:\\dachicagorecordsftp\\', dirname(__FILE__).'/dachicagorecordsftp_duplicate/', $file);
                                    $q_chk_dup = $DRW->query("SELECT id FROM cscan_product_detail_duplicate WHERE productID='".$productID."' AND filename='".$DRW->real_escape_string($file)."'",$DRW_read2);
                                    if($DRW->num_rows($q_chk_dup) == 0){
                                        $sql_trns = "INSERT INTO cscan_product_detail_duplicate SET
                                            productID = '".$productID."',
                                            panelist_id = '".$panelist_id."',
                                            percentage_match = '".$match_percentage."',
                                            filename = '".$DRW->real_escape_string($file)."',
                                            duplicate_filename = '".$DRW->real_escape_string($approved_file)."',
                                            datetime = NOW()";
                                        echo '$sql_trns=> '.$sql_trns.'</br>';
                                        if($DRW->query($sql_trns, $DRW_main)){
                                            // move or delete from dachicagorecordsftp
                                            if(rename($new_file, $del_file)){
                                                $flag = true;
                                            }else{
                                                $flag = false;
                                                $errorFatal[$key] = 'Unable to move '.$new_file.' to '.$del_file;
                                            }
                                        }else{
                                            $flag = false;
                                            $errorFatal[$key] = 'MYSql error '.$sql_trns;
                                        }
                                    }

                                }else{
                                    $flag = false;
                                    $errorFatal[$key] = 'MYSql error '.$sql_pd;
                                }

                            }else{
                                // make entry in new transaction table                                            
                                $del_file = str_replace('z:\\dachicagorecordsftp\\', dirname(__FILE__).'/dachicagorecordsftp_duplicate/', $file);
                                $q_chk_dup = $DRW->query("SELECT id FROM cscan_product_detail_duplicate WHERE productID='".$productID."' AND filename='".$DRW->real_escape_string($file)."'",$DRW_read2);
                                if($DRW->num_rows($q_chk_dup) == 0){
                                    $sql_trns = "INSERT INTO cscan_product_detail_duplicate SET
                                            productID = '".$productID."',
                                            percentage_match = '".$match_percentage."',
                                            filename = '".$DRW->real_escape_string($file)."',
                                            duplicate_filename = '".$DRW->real_escape_string($approved_file)."',
                                            datetime = NOW()";
                                    //echo '$sql_trns=> '.$sql_trns.'</br>';
                                    if($DRW->query($sql_trns, $DRW_main)){
                                        // move or delete from dachicagorecordsftp
                                        if(rename($new_file, $del_file)){
                                            $flag = true;
                                        }else{
                                            $flag = false;
                                            $errorFatal[$key] = '2.Unable to move '.$new_file.' to '.$del_file;
                                        }
                                    }else{
                                        $flag = false;
                                        $errorFatal[$key] = '2.MYSql error '.$sql_trns;
                                    }            
                                }

                                $flag = true;
                                $errorFatal[$key] = 'No panelist exists for competi_id: '.$competi_id.' ('.$sql_p.' )';
                            }
                        }else{
                            $flag = false;
                            $errorFatal[$key] = 'Unable to extract competi_id from DMSource(filename) : '.$file;
                        }
                    }else{ echo 'kkkk'; die;
                        $flag = false;
                        $errorFatal[$key] = 'Unable to find product in database for ID:'.$productID;
                    }
                }
            }else{
                $flag = false;
                $errorFatal[$key] = 'Unable to extract Product ID from existing duplicate file: '.$approved_file;
            }
        }elseif(!file_exists($new_file)){
            $new++;
            //echo $new_file;die;
            //new file
            $actual_new_file=$new_file;
            //check for duplicate filename
//            if(!empty($da_id)){
//                $sql_chk_daid = "SELECT filename FROM da_chicagorecords WHERE id ='".$da_id."'";
//                $query_chk_daid = $DRW->query($sql_chk_daid, $DRW_read2);
//                if ($DRW->num_rows($query_chk_daid)>0) {
//                    $rs_new_filename = $DRW->fetch_assoc($query_chk_daid);
//                    $new_file=$rs_new_filename['filename'];
//
//                }
//            }
            
            $sql_chk_c = "SELECT productID FROM chicagorecords WHERE filename ='".$DRW->real_escape_string($new_file)."' AND productID>0";
            //$last_modified_dt=date('Y-m-d',strtotime($last_mod));
            //$sql_chk_c = "SELECT productID FROM chicagorecords WHERE filename ='".$DRW->real_escape_string($new_file)."' AND DATE(last_modified) ='".$last_modified_dt."'";            
            //echo '$sql_chk_c=> '.$sql_chk_c.'</br>';die;
            $sql_chk_q = $DRW->query($sql_chk_c, $DRW_read2);
            
            if ($DRW->num_rows($sql_chk_q) == 0) {
                //filename should be from da_chicagorecords's filename
                $sql_c = "INSERT INTO chicagorecords SET
                    filename ='".$DRW->real_escape_string($new_file)."',
                    last_modified ='".$last_mod."',
                    local_dir ='".$GLOBALS['local_dir']."'";
                //echo '$sql_c=> '.$sql_c.'</br>';
                if($DRW->query($sql_c,$DRW_main)){
                    $id = $DRW->insert_id($DRW_main);
                    $local_file = "{$GLOBALS['local_dir']}pdf_{$id}.pdf";                    
                    // move or delete from dachicagorecordsftp
//                    if(!rename($actual_new_file, $local_file)){ 
//                        $sqlc = "DELETE FROM chicagorecords WHERE id=$id";
//                        if($DRW->query($sqlc,$DRW_main)){
//                            $flag = true;
//                        }else{
//                            $flag = false;
//                            $errorFatal[$key] = 'MYSql error: '.$sqlc;
//                        }
//                        $flag = false;
//                        $errorFatal[$key] = 'Unable to move file from: '.$new_file. ' to: '.$local_file;
//                    }else{
//                        
                       if(!empty($id)){
                        @chmod($local_file,0777);
                        @chown($local_file,'apache');
                        //echo "startcrmprosess";
                        processCRM($id);
//                        if($check==2){
//                            echo $new_file;die;
//                        }
                    }
                }else{
                    $flag = false;
                    $errorFatal[$key] = 'MYSql error: '.$sql_c;
                }
            }else{
                $flag = true;
                $errorNotice[$key] = 'A product already exists with filename: '.$new_file.' in database, ('.$sql_chk_c.')';
            }            
        }else{
            $flag = false;
            $errorFatal[$key] = 'Either new files: '.$new_file.' or duplicate file '.$approved_file.' does not exists in there respective paths.';
        }
        
        if($n == $totalCount){
            break;
        }
    }
    //LOGS
    $arrLog[0] = 'ImportToComptiscan(c)';  
    $arrLog[1] = date('Y-m-d')."_search_output.csv";
    $arrLog[3] = date("Y-m-d H:i:s");    
    $arrLog[4] = 'Success';
    if(!empty($arrLog)){
        ksort($arrLog);
        $localLog = dirname(__FILE__)."/dacsv/".$csvdate."/log.csv";
        updateLog($arrLog,$localLog);
        $globalLog = dirname(__FILE__)."/dacsv/".date('Y-m',strtotime($csvdate))."_daLog.csv";
        updateLog($arrLog,$globalLog);
    }
}else{
    echo 'Search output file: '.$importFrom.' has no data to be imported.';
}

function processCRM($id){ //echo 'suman'; die;
    global $DRW,$DRW_main,$DRW_read2,$s3,$bucket_name;
    global $errorFatal,$errorNotice,$flag;
    if(!empty($id)){
        $sql = "SELECT SQL_NO_CACHE id,filename,last_modified,local_dir FROM chicagorecords WHERE id = '".$id."' AND productID=0 LIMIT 1";
        $query = $DRW->query($sql,$DRW_read2);
        if($DRW->num_rows($query)>0){
            $row = $DRW->fetch_row($query);
            $id = $row[0];
            $ofilename = $row[1];
            $last_modified = $row[2];
            $local_dir = $row[3];
            $local_filename = "{$local_dir}pdf_{$id}.pdf";

            //error_log("CRONJOB_FTP2: processCRM $local_filename");
            //if(is_file($local_filename)){
            if(!is_file($ofilename)){     
                if(!empty($GLOBALS['da_id'])){
                   echo $sql_da = "SELECT filename FROM da_chicagorecords WHERE id='".$GLOBALS['da_id']."'";
                    die; $rs_da = $DRW->query($sql_da,$DRW_read2);
                    if($DRW->num_rows($rs_da)>0){
                        $data_da = $DRW->fetch_assoc($rs_da);
                        $ofilename2 = $data_da['filename'];
                        if(preg_match('/\\/(\\d+\\-\\d+\\-\\d+)\\/([^\\/]+)\\/([^\\/]+)\\//',$ofilename,$matches)){
                            echo 'sssssss';
                            pr($matches); die;
                            $scan_date = trim($matches[1]);
                            $DMSource = trim($matches[2]);
                            $operator = trim($matches[3]);
			}elseif(preg_match('/\\/([^\\/]+)$/',dirname($ofilename2),$matches)){
                            $DMSource = trim($matches[1]);
			}else {
                            $DMSource = trim(basename($ofilename2));
			}
                    }
                    //update da_chicagorecords for productID
                    $sql_da2= "UPDATE da_chicagorecords SET cid='".$id."' WHERE id='".$GLOBALS['da_id']."'";
                    $rs_da = $DRW->query($sql_da2,$DRW_main);
                }else{
                    $DMSource = trim(basename($ofilename));                    
                }        
                $DMSource = preg_replace('/_+/', '_', $DMSource);
                if((stristr($DMSource, '_CP_') === FALSE) || (stristr($DMSource, '_cp_') === FALSE)) {
                      $pos = strpos($ofilename, '_CP_');
                    if(strstr($DMSource, '_cp_')){
                         $pos = strpos($ofilename, '_cp_'); 
                    }
                  
                    if($pos === false){
                    }else{
                        $str1 = substr($ofilename, $pos, strlen($ofilename));
                        if(strlen($str1)>0){
                            $string = current(explode("/",$str1));
                            if(strlen($string)>0){
                                $end = $pos+strlen($string);
                                $string = substr($ofilename, 0, $end);
                                $start = strripos($string, "/");
                                if($start){
                                    $start += 1;
                                }else{
                                   $start = 0; 
                                }
                                $nc = ($end-$start);
                                $DMSource = substr($ofilename, $start, $nc);
                                $DMSource = trim($DMSource,"/");
                            }
                        }
                    }
                }
                //pr($DMSource);die;
                $sectorID = '';
                $productStatus = 11;
                $offerOrigin = '3';
                $is_citi = 1;
                $lofilename = strtolower($ofilename);
//                if(strpos($lofilename,'telecom')!==false || strpos($lofilename,'_tc_')!==false){
//                    $productStatus = 9;
//                    $sectorID = '9';
//                    $offerOrigin = '3';
//                }
//                elseif(strpos($lofilename,'_tl_')!==false){
//                    $productStatus = 219;
//                    $sectorID = '219';
//                    $offerOrigin = '3';
//                }
//                elseif(strpos($lofilename,'retail')!==false || strpos($lofilename,'_rl_')!==false){
//                    $productStatus = 266;
//                    $sectorID = '266';
//                    $offerOrigin = '3';
//                }
//                elseif(strpos($lofilename,'energy')!==false || strpos($lofilename,'_en_')!==false){
//                    $productStatus = 315;
//                    $sectorID = '315';
//                    $offerOrigin = '3';
//                }
//                elseif(strpos($lofilename,'noncore')!==false|| strpos($lofilename,'_nc_')!==false){
//                    $productStatus = 6;
//                    $offerOrigin = '3';
//                }
//                else {
//                    $productStatus = 5;
//                }
                if(strpos($lofilename,'highpriority')!==false || strpos($lofilename,'_hp_')!==false){
                    $product_priority = 1;
                }
                else {
                    $product_priority = 0;
                }
                if(strpos($lofilename,'affinion')!==false || strpos($lofilename,'_af_')!==false){
                    $is_affinion = 1;
//                    $productStatus = 6;
//                    $offerOrigin = '3';
                }
                else{
                    $is_affinion = 0;
                }
                if(strpos($lofilename,'_sh_')!==false){
                    $special_handling = 1;
                }
                else {
                    $special_handling = 0;
                }
                if(strpos($lofilename,'_ci_')!==false || strpos($lofilename,'consumer_insights')!==false){
                    $consumer_insights = 1;
                }
                else {
                    $consumer_insights = 0;
                }
//                if(strpos($lofilename,'citi')!==false || strpos($lofilename,'_cp_')!==false){
//                    $is_citi = 1;
//                    $productStatus = 11;
//                    $offerOrigin = '3';
//                }
//                else{
//                    $is_citi = 0;
//                }
                    
                $p_id = 0;
                $is_subp = 0;
                $pprimary = 1;
                $ppage = 0;
                $ppstateID = 0;
                $pgender = 'N';
                $homeownershipID = 0;
                $pincomeID = 0;
                $ppageID = 0;
                $ppfico_score = 0;
                $ownbiz = 0;
                $pppostalcode = '';
                $mChannelID = 1;
                $mPanelID = 1;
                $delmethid = 1;
                if(preg_match('/^(\\d+)_(\\d+)_(\\d+)/',$DMSource,$matches)){
                    $competi_id = $matches[1].'-'.$matches[2].'-'.$matches[3];
                    $defs = "SELECT panelist_id,parent_panelist_id FROM cscan_panelists WHERE competi_id='$competi_id'";
                    if(preg_match('/^\\d{3}\\-/',$competi_id)){
                        $defs .= " OR competi_id='0$competi_id'";
                    }
                    $defs .= " ORDER BY competi_id ASC,active DESC LIMIT 1";
                    //echo '$defs=> '.$defs.'</br>';
                    $resultD = $DRW->query($defs,$DRW_read2);
                    if($DRW->num_rows($resultD)>0){
                        $dataD = $DRW->fetch_row($resultD);
                        //pr($dataD);
                        $p_id = (int)$dataD[0];
                        $parent_panelist_id = (int)$dataD[1];
                        if($parent_panelist_id>0){
                            $is_subp = 1;
                            $pprimary = 0;
                        }
                        if(!empty($p_id)){
                            $defs2 = "SELECT DATEDIFF(CURDATE(),birthdate) as agedays,stateID,gender,homeownershipID,incomeID,fico_score,contactTypeID,ownbiz,postalcode
                                    FROM cscan_panelists WHERE panelist_id='".$p_id."'";
                            //echo '$defs2=> '.$defs.'</br>';
                            $resultD2 = $DRW->query($defs2,$DRW_read2);
                            if($DRW->num_rows($resultD2)>0){
                                $dataD = $DRW->fetch_row($resultD2);
                                $ppage = floor($dataD[0]/365);
                                $ppstateID = $dataD[1];
                                $pgender = strtoupper(substr(trim($dataD[2]),0,1));
                                if($pgender=='') $pgender = 'N';
                                $homeownershipID = $dataD[3];
                                $pincomeID = $dataD[4];
                                $ppfico_score = $dataD[5];
                                $contactTypeID = $dataD[6];
                                $ownbiz = (int)$dataD[7];
                                $pppostalcode = trim($dataD[8]);
                                if($contactTypeID==1){
                                    $mPanelID = 4;
                                }elseif($contactTypeID==2){
                                    $mPanelID = 1;
                                }
                                if(count($GLOBALS['parr'])>0){
                                    foreach($GLOBALS['parr'] as $pid=>$min){
                                        if($ppage>=$min){
                                            $ppageID = $pid;
                                        }else{
                                            break;
                                        }
                                    }
                                }
                            }else{
                                $flag = true;
                                $errorNotice[$key] = 'MYSql Error: '.$defs;
                            }                            
                            @$DRW->free_result($resultD2);
                        }else{
                            $flag = true;
                            $errorNotice[$key] = 'Invalid panelist_id: '.$p_id;
                        }
                    }else{
                        $flag = true;
                        $errorNotice[$key] = 'Unable to find panelist having competi_id: '.$competi_id.' in database ('.$defs.')';
                    }                    
                    @$DRW->free_result($resultD);                    
                }else{
                    $flag = true;
                    $errorNotice[$key] = 'Unable to find competi_is on: '.$DMSource;
                }

                $pdffile_arr=array();
                $pdffile_arr[] = $local_filename;
                //pr($pdffile_arr);//die;
                foreach ($pdffile_arr as $filename) {
                    $entryId = '';//generate_entryID(true,$last_modified);
                    $arrDMSource = explode("_",$DMSource);
                    array_pop($arrDMSource);
                    $DMSource = implode("_",$arrDMSource);
                    $sqlc = "INSERT INTO cscan_product_detail
                            SET productStatus='".$productStatus."',
                            firstSeen='".substr($last_modified,0,10)."',
                            lastSeen='".substr($last_modified,0,10)."',
                            actual_addedToDatabase=NOW(),
                            addedToDatabase=NOW(),
                            mChannelID='".$mChannelID."',
                            mPanelID='".$mPanelID."',
                            state='".$ppstateID."',
                            gender='".$pgender."',
                            incomeID='".$pincomeID."',
                            age='".$ppageID."',
                            entryID='".$entryId."',
                            DMSource='".$DRW->real_escape_string(strtoupper($DMSource))."',
                            product_priority='".$product_priority."',
                            delmethid='".$delmethid."',
                            sectorID='".$sectorID."',
                            is_affinion='".$is_affinion."',
                            offerOrigin='".$offerOrigin."',
                            special_handling='".$special_handling."',
                            consumer_insights='".$consumer_insights."',
                            is_citi='".$is_citi."',
                            is_subp='".$is_subp."'";
                    //echo '$sqlc=> '.$sqlc.'</br>';
                    if($DRW->query($sqlc,$DRW_main)){
                        $pdtID = $DRW->insert_id($DRW_main);
                        if(!empty($sectorID)){
                            $sid = $sectorID;
                        }else{
                            $sid = '0';
                        }
                        $sqlU = "INSERT IGNORE INTO cscan_scsc_product (productID,scsc_sectorID,scsc_categoryID,scsc_subCategoryID) VALUES ('".$pdtID."','".$sid."',0,0)";
                        //echo '$sqlc=> '.$sqlU.'</br>';
                        if($DRW->query($sqlU,$DRW_main)){
                            $flag = true;
                        }else{
                            $flag = false;
                            $errorFatal[$key] = 'MYSql error : '.$sqlU;
                        }
                        $sqlp = '';
                        if(!empty($p_id) && !empty($pdtID)){
                            $sqlp = "UPDATE chicagorecords SET panelist_id='".$p_id."', productID=$pdtID WHERE id=$id";
                        }elseif(empty($p_id) && !empty($pdtID)){
                            $sqlp = "UPDATE chicagorecords SET productID='".$pdtID."' WHERE id='".$id."'";
                        }elseif(!empty($p_id) && empty($pdtID)){
                            $sqlp = "UPDATE chicagorecords SET panelist_id='".$p_id."' WHERE id='".$id."'";
                        }
                        if(!empty($sqlp)){
                            //$sqlp = "UPDATE chicagorecords SET panelist_id='".$p_id."', productID=$pdtID WHERE id=$id";
                            //echo '$sqlp=> '.$sqlp.'</br>';
                            if($DRW->query($sqlp,$DRW_main)){
                                $flag = true;
                            }else{
                                $flag = true;
                                $errorFatal[$key] = 'MYSql error : '.$sqlp;
                            }
                        }                    
                        $path = dirname($filename);
                        if(!preg_match('/\\/$/',$path)){
                            $path .= '/';
                        }
                        $fname = basename($filename);
                        $newpath = $path.$pdtID.'/';
                        if(!is_dir($newpath)){
                            mkdir($newpath,02755);
                            @chmod($newpath,02755);
                            @chown($newpath,'apache');
                            //@chgrp($newpath,'competiscan_web');
                        }
                        if(rename($path.$fname,$newpath.$fname)){
                            $filename = $newpath.$fname;
                            @chmod($filename,02755);
                            @chown($filename,'apache');
                            //@chgrp($filename,'competiscan_web');
                        }else{
                            $newpath = $path;
                        }
                        if(!is_dir($pdtID)){
                            mkdir($pdtID,02755);
                            @chmod($pdtID,02755);
                            @chown($pdtID,'apache');
                            //@chgrp($newpath,'competiscan_web');
                        }
                        $pdfPath = '';
                        $pos = strpos($path,'/PDF/crm/');
                        if($pos!==false){
                            $pdfPath = substr($path,$pos);
                        }
                        //createPreviewJPG($newpath,$fname,$pdtID);
                        //$document_id = savePDFData($pdtID,$newpath,$fname,'',$pdfPath.$pdtID.'/',true);
                        $latestpath =  $newpath.$pdtID.'/'.$fname;
                       //echo "<br>ofile==>".$ofilename."<br>latestpath===>".$latestpath;
                       if (copy($ofilename, $latestpath)) {
                            $content_type = "application/pdf";
                            $result = $s3->putObject([
                                'Bucket' => $bucket_name,
                                'Key'    => $latestpath,
                                'SourceFile' => $ofilename,
                                'ACL'    => 'public-read',
                                'ContentType'   => $content_type,
                                'Metadata'      => array(
                                   'string'        => 'string'
                                 )
                            ]); 
                  
                            createPreviewJPG(dirname(__FILE__)."/".$newpath.$pdtID.'/',$fname,$pdtID);
                           // echo"createdjep";
                        }
                      $document_id = savePDFData($pdtID,dirname(__FILE__)."/".$newpath.$pdtID.'/',$fname,'',"/".$newpath.$pdtID.'/',true);
                        if(!empty($p_id)){
                            $sqlU = "INSERT IGNORE INTO cscan_panelists_product (productID,panelist_id,ppdate,ppage,ppstateID,pgender,homeownershipID,pincomeID,ppageID,ppfico_score,isBiz,pppostalcode,ppaddeddate,pprimary) 
                                    VALUES ('".$pdtID."','".$p_id."','".$last_modified."','".$ppage."','".$ppstateID."','".$pgender."','".$homeownershipID."','".$pincomeID."','".$ppageID."','".$ppfico_score."','".$ownbiz."','".$DRW->real_escape_string($pppostalcode)."',NOW(),'".$pprimary."')";
                            //echo '$sqlU=> '.$sqlU.'</br>';
                            if($DRW->query($sqlU,$DRW_main)){
                                $flag = true;
                            }else{
                                $flag = false;
                                $errorFatal[$key] = 'MYSql error : '.$sqlU;
                            }
                        }
                        updateStateLookup($pdtID);

                        $sql = "INSERT IGNORE INTO `cscan_admin_log` SET userID=0,logDate=NOW(),productID='".$pdtID."'";
                        //echo '$sql=> '.$sql.'</br>';
                        if($DRW->query($sql,$DRW_main)){
                            $flag = true;
                        }else{
                            $flag = false;
                            $errorFatal[$key] = 'MYSql error : '.$sql;
                        }
                    }else{
                        $flag = false;
                        $errorFatal[$key] = 'MYSql error : '.$sqlc;
                    }                
                }
            }else{
                $flag = false;
                $errorFatal[$key] = 'Not a regular filename : '.$local_filename;
            }
        }else{
            $flag = false;
            $errorFatal[$key] = 'MYSql error: '.$sql;
        }
        @$DRW->free_result($query);
    }else{
        $flag = false;
        $errorFatal[$key] = 'ID Required: '.$id;
    }    
}
function dateDiff ($d1, $d2) {
// Return the number of days between the two dates:
  return round(abs(strtotime($d1)-strtotime($d2))/86400);
}
//if(count($errorFatal) == 0 && count($errorNotice) == 0){
//    echo '<h1>Successfully Done !</h1>';
//}elseif(count($errorFatal) == 0 && count($errorNotice) > 0){
//    echo '<h1>Successfully Done, with some notices-</h1></br>';
//    foreach($errorNotice as $key=>$notice){
//        echo $key.' => '.$notice.'</br>';
//    }
//}elseif(count($errorFatal) > 0 && count($errorNotice) == 0){
//    echo '<h1>Critical Errors-</h1></br>';
//    foreach($errorFatal as $key=>$fatal){
//        echo $key.' => '.$fatal.'</br>';
//    }
//}else{
//    echo '<h1>C Errors-</h1></br>';
//    foreach($errorFatal as $key=>$fatal){
//        echo $key.' => '.$fatal.'</br>';
//    }
//    echo '</br></br><h1>Notices-</h1></br>';
//    foreach($errorNotice as $key=>$notice){
//        echo $key.' => '.$notice.'</br>';
//    }
//}
//if(!defined('ENV')){
//    define('ENV',getenv('SERVER_NAME'));
//} 
//if(ENV == 'localhost' || ENV == 'demo.competiscan.com'){
    /*######### commented to resolve less data import issue ###########*/
    exec("/usr/bin/php ".dirname(__FILE__)."/cron_update_append_search_csv.php $day> /dev/null 2>/dev/null &");
//} 
$to = "manas@nmgtechnologies.com,devendra.tiwari@nmgtechnologies.com,arvind.chaurasia@nmgtechnologies.com";
$subject = "Citi FTP Import\r\n\r\n";
$html = '<html>
        <head><title>FTP Chicago Records(import)</title></head>
        <body>
            <table width="50%">
                <tr><td colspan="3" align="center"><h3>Citi FTP Import</h3><small>(Automated Email - '.date("Y-m-d H:i:s").')</small></td></tr>
                <tr><td colspan="3" align="center">&nbsp;</td></tr>
                <tr>
                    <td><b>Total Files</b></td>
                    <td><b>Moved To Citi FTP</b></td>
                    <td><b>Duplicate Files</b></td>
                </tr>
                <tr>
                    <td>'.$totalCount.'</td>
                    <td>'.$new.'</td>
                    <td>'.$dup.'</td>
                </tr>
            </table>
        </body>
     </html>>>';
$params = array(
    'username' => '',
    'password' => '',
    'persist' => true,
);
/*
$mail = & Mail::factory('smtp', $params);
$crlf = "\n";
$hdrs = array('From' => "\"Competiscan\" <richard@competiscan.com>", 'To' => $to, 'Subject' => $subject);
$mime = new Mail_mime($crlf);
$mime->setHTMLBody($html);
$body = $mime->get();
$headers = $mime->headers($hdrs);
$send = $mail->send($to, $headers, $body);
*/
die; ?>