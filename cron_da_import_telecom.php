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

echo 'Start: '.date("Y-m-d H:i:s").'</br></br>';
function pr($str){
    echo '<pre>';print_r($str);
}
die("One Time Cron");
die;
##############################################################
$outputData = array();
$importFrom = dirname(__FILE__)."/dacsv/".date("Y-m-d")."_search_telecom_output.csv";
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
//pr($outputData);die;
if(count($outputData)>0){
    $newData = array();
    foreach($outputData as $key=>$frow){
        $file = $frow[0];        
        $last_mod = $frow[1]; 
        $status = $frow[2]; 
        $match_percentage = $frow[3]; 
        $local_dup_file = (!empty($frow[4]))?$frow[4]:"";
        //echo '$local_dup_file=> '.$local_dup_file.'</br>';
        $new_file = str_replace('z:\\dachicagorecordsftp\\telecomftp\\', dirname(__FILE__).'/dachicagorecordsftp/telecomftp/', $file);
        //echo '$new_file=> '.$new_file.'</br>';
        $approved_file = (!empty($local_dup_file))?str_replace('z:\\dmapprovedpdf\\', dirname(__FILE__).'/dmapprovedpdf/', $local_dup_file):"";
        //echo '$approved_file=> '.$approved_file.'</br>';
        if($status == 1 && $approved_file == ''){
            continue;
        }
        // check if file approved between the proccess  
        $del_file = basename($file);
        $del_file = rtrim($del_file,'_');
        $deleted_productID = current(explode(".",end(explode("_",$del_file))));
        $chk_approved = "SELECT productName FROM cscan_product_detail WHERE productStatus=1 AND productID = '".$DRW->real_escape_string($deleted_productID)."'";
        $rs_check_approved = $DRW->query($chk_approved, $DRW_read);
        if ($DRW->num_rows($rs_check_approved) > 0){
            continue;
        }
        if(file_exists($approved_file)){
//        if($status == 1){
            // duplicate file
            $dup_file = basename($approved_file);
            $dup_file = rtrim($dup_file,'_');
            $productID = current(explode(".",end(explode("_",$dup_file))));
            //echo '$productID=> '.$productID.'</br>';die;
            if(!empty($productID)){
                //echo $productID;die;
                $sql_check = "SELECT productName FROM cscan_product_detail WHERE productID = '".$DRW->real_escape_string($productID)."'";
                //echo '$sql_check=> '.$sql_check.'</br>';
                $rs_check = $DRW->query($sql_check, $DRW_read);
                if ($DRW->num_rows($rs_check) > 0){
//                if(1==1){
                    $DMSource = basename($file);
                    $DMSource = preg_replace('/_+/', '_', $DMSource);
                    //echo $DMSource;die;
                    //echo $file;die;
                    if(preg_match('/(\\d+)_(\\d+)_(\\d+)/', $DMSource, $matches)){
                        //pr($matches);die;
                        $competi_id = $matches[1].'-'.$matches[2].'-'.$matches[3];
                        //pr($competi_id);die;
                        $sql_p = "SELECT panelist_id,parent_panelist_id,DATEDIFF(CURDATE(),birthdate) as agedays,stateID,gender,homeownershipID,incomeID,fico_score,contactTypeID,ownbiz,postalcode FROM cscan_panelists WHERE competi_id='".$DRW->real_escape_string($competi_id)."'";
                        //echo '$sql_p=> '.$sql_p.'</br>';
                        $q_p = $DRW->query($sql_p, $DRW_read);
                        if ($DRW->num_rows($q_p) > 0) {
                            $rs_p = $DRW->fetch_assoc($q_p);
                            //pr($rs_p);die;
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
                            // fetch ppdate from cscan_panelists_product & compare with current_ftp date
                            $sql_ppdate = "SELECT ppdate FROM cscan_panelists_product WHERE productID='".$DRW->real_escape_string($productID)."' ORDER BY ppdate DESC LIMIT 1";
                            $q_ppdate = $DRW->query($sql_ppdate, $DRW_read);
                            if ($DRW->num_rows($q_ppdate) > 0) {
                                $rs_ppdate =  $DRW->fetch_assoc($q_ppdate);
                                $prev_ppdate = date("Y-m-d", strtotime($rs_ppdate['ppdate']));
                                //$last_mod = date("Y-m-d", strtotime($prev_ppdate));
                                $ppdate = $prev_ppdate;
                                //echo '$ppdate=> '.$ppdate.'</br>';
                                $diff = dateDiff($last_mod,$prev_ppdate);
                                if($diff > 10){
                                    $ppdate = date("Y-m-d H:i:s", strtotime($last_mod." -10 day"));
                                    //echo '$ppdate2=> '.$ppdate.'</br>';
                                }
                            }
                            
                            //check duplicay for panelist_id+productID
                            $sql_chk_p = "SELECT ppdate FROM cscan_panelists_product WHERE productID='".$DRW->real_escape_string($productID)."' AND panelist_id='".$DRW->real_escape_string($panelist_id)."' AND ppdate = '".$DRW->real_escape_string($ppdate)."'";
                            $q_chk_q = $DRW->query($sql_chk_p, $DRW_read);
                            //echo '$sql_chk_p=> '.$sql_chk_p.'</br>';
                            if ($DRW->num_rows($q_chk_q) == 0) {
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
//                                echo '$sql_pp=> '.$sql_pp.'</br>';//die;
                                if($DRW->query($sql_pp,$DRW_main)){
//                                if(1==1){
                                    $flag = true;
                                }else{
                                    $flag = false;
                                    $errorFatal[$key] = 'MYSql Error: '.$sql_pp;
                                }
                            }else{
                                $rs_ppdate =  $DRW->fetch_assoc($q_chk_q);
                                $prev_ppdate = $rs_ppdate['ppdate'];
                                //update panelist_product table 
                                $sql_pp = "UPDATE cscan_panelists_product 
                                    SET ppdate = '".$DRW->real_escape_string($ppdate)."',
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
                                        pprimary = '".$pprimary."'
                                        WHERE productID = '".$productID."' AND panelist_id='".$DRW->real_escape_string($panelist_id)."' AND ppdate = '".$prev_ppdate."'";
                                
                                //echo '$sql_pp=> '.$sql_pp.'</br>';die;
                                if($DRW->query($sql_pp,$DRW_main)){
//                                if(1==1){
                                    $flag = true;
                                }else{
                                    $flag = false;
                                    $errorFatal[$key] = 'MYSql Error: '.$sql_pp;
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
                            //echo $sql_pp;die;
                            $cpd_panelist = $DRW->query("SELECT panelist_id FROM cscan_product_detail WHERE productID = '".$productID."'",$DRW_read);
                            if($DRW->num_rows($cpd_panelist) > 0){
                                $rs_cpd_panelist = $DRW->fetch_assoc($cpd_panelist);
                                $arrPanelists = explode(",", $rs_cpd_panelist['panelist_id']);                                            
                            }
                            array_push($arrPanelists, $panelist_id);
                            $arrPanelists = array_unique($arrPanelists);
                            $arrPanelists = array_filter($arrPanelists, function($value) { return $value !== ''; });
                            $panelists = implode(",", $arrPanelists);
                            
                            //temporary arrangement for testing
//                            $ftp_pId = (int)current(explode(".",end(explode("_",basename($new_file)))));
                            //echo $ftp_pId;die;
//                            if(!empty($ftp_pId)){
//                                $sql_pd = "UPDATE cscan_product_detail
//                                SET new_panelists = '".$panelists."',
//                                matched_with = '".$productID."',
//                                match_percentage = '".$match_percentage."'
//                                WHERE productID = '".$ftp_pId."'";
//                                if($DRW->query($sql_pd, $DRW_main)){
//                                    echo '$sql_pd => '.$sql_pd.'  :Done</br>';
//                                }
//                            }
                            

                            $sql_pd = "UPDATE cscan_product_detail
                                SET panelist_id = '".$panelists."',
                                panelist_sort = '".$panelist_sort."',
                                is_digital = '".$isDigital."'
                                WHERE productID = '".$productID."'";
                            //echo '$sql_pd=> '.$sql_pd.'</br>';//die;
                            if($DRW->query($sql_pd, $DRW_main)){
//                            if(1==1){
                                // make entry in new transaction table   
//                                $del_file = basename($file);
//                                $del_file = rtrim($del_file,'_');
//                                $deleted_productID = current(explode(".",end(explode("_",$del_file))));
                                $del_file = str_replace('z:\\dachicagorecordsftp\\telecomftp\\', dirname(__FILE__).'/dachicagorecordsftp_duplicate/', $file);
                                $sql_trns = "INSERT INTO cscan_product_detail_duplicate SET
                                        is_telecom = 1,
                                        deleted_productID = '".$deleted_productID."',
                                        productID = '".$productID."',
                                        panelist_id = '".$panelist_id."',
                                        percentage_match = '".$match_percentage."',
                                        filename = '".$DRW->real_escape_string($file)."',
                                        duplicate_filename = '".$DRW->real_escape_string($approved_file)."',
                                        datetime = NOW()";
                                //echo '$sql_trns=> '.$sql_trns.'</br>';
                                if($DRW->query($sql_trns, $DRW_main)){
                                    // move or delete from dachicagorecordsftp
                                    if(copy($new_file, $del_file)){
                                        $flag = true;
                                        // copy the duplicate data in new table
                                        //$DRW->query("CREATE TABLE IF NOT EXISTS cscan_product_detail_telecom", $DRW_main);
                                        $sql_copy = "INSERT INTO cscan_product_detail_telecom SELECT * FROM cscan_product_detail WHERE productID = '".$deleted_productID."'";
                                        if($DRW->query($sql_copy, $DRW_main)){
                                          //delete telecom product if treated as duplicated
                                            $sql_del = "DELETE FROM cscan_product_detail WHERE productID = '".$deleted_productID."'";
                                            if($DRW->query($sql_del, $DRW_main)){
                                                $sql_doc = "DELETE FROM cscan_document";
                                                $sql_doc = "DELETE FROM cscan_document_orig";
                                                $sql_doc = "DELETE FROM cscan_document_text_search";
                                                //unlink($file);
                                                $flag = true;
                                            }else{
                                                $flag = false;
                                                $errorFatal[$key] = 'Unable to delete duplicate record from telecom ftp ('.$sql_del.' )';
                                            }
                                        }else{
                                            $flag = false;
                                            $errorFatal[$key] = 'Unable to copy data into cscan_product_detail_telecom ('.$sql_copy.' )';
                                        }
                                        
                                    }else{
                                        $flag = false;
                                        $errorFatal[$key] = 'Unable to move '.$new_file.' to '.$del_file;
                                    }
                                }else{
                                    $flag = false;
                                    $errorFatal[$key] = 'MYSql error '.$sql_trns;
                                }
                            }else{
                                $flag = false;
                                $errorFatal[$key] = 'MYSql error '.$sql_pd;
                            }
                            
                        }else{
                            $flag = true;
                            $errorFatal[$key] = 'No panelist exists for competi_id: '.$competi_id.' ('.$sql_p.' )';
                        }
                    }
                }else{
                    $flag = false;
                    $errorFatal[$key] = 'Unable to extract competi_id from DMSource(filename) : '.$file;
                }
            }else{
                $flag = false;
                $errorFatal[$key] = 'Unable to extract Product ID from existing duplicate file: '.$approved_file;
            }
            $productID = '';
        }else{
            $newData[] = $frow;
        }
    }    
}
//pr($newData);
function dateDiff ($d1, $d2) {
// Return the number of days between the two dates:
  return round(abs(strtotime($d1)-strtotime($d2))/86400);
}

if(count($errorFatal) == 0 && count($errorNotice) == 0){
    echo '<h1>Successfully Done !</h1>';
}elseif(count($errorFatal) == 0 && count($errorNotice) > 0){
    echo '<h1>Successfully Done, with some notices-</h1></br>';
    foreach($errorNotice as $key=>$notice){
        echo $key.' => '.$notice.'</br>';
    }
}elseif(count($errorFatal) > 0 && count($errorNotice) == 0){
    echo '<h1>Critical Errors-</h1></br>';
    foreach($errorFatal as $key=>$fatal){
        echo $key.' => '.$fatal.'</br>';
    }
}else{
    echo '<h1>C Errors-</h1></br>';
    foreach($errorFatal as $key=>$fatal){
        echo $key.' => '.$fatal.'</br>';
    }
    echo '</br></br><h1>Notices-</h1></br>';
    foreach($errorNotice as $key=>$notice){
        echo $key.' => '.$notice.'</br>';
    }
}
###############################################################
echo '</br></br>End: '.date("Y-m-d H:i:s");