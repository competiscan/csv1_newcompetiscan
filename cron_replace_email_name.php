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


function pr($str){
    echo '<pre>';print_r($str);
}
##############################################################
$hy = '';
$sql = "SELECT ce.muid,cp.first_name,cp.last_name,ce.email_subject
        FROM cscan_email$hy ce
        LEFT JOIN cscan_panelists cp ON cp.panelist_id = ce.panelist_id
        WHERE ce.panelist_id > 0 AND ce.isnamereplace=0 
        ORDER BY ce.muid DESC LIMIT 10000";

$query = $DRW->query($sql,$DRW_read2);
$num = $DRW->num_rows( $query );

if($num > 0){
    while( $row = $DRW->fetch_assoc( $query ) ){
        //pr($row);
        $first_name = $row['first_name'];
        $last_name = $row['last_name'];
        $email_subject = addslashes($row['email_subject']);
        $email_subject1 = addslashes($row['email_subject']);
        $muid = $row['muid'];
        /*$first_name = "Nishant";
        $last_name = "Garg";
        $email_subject = addslashes("Fwd: nishant's: ?? garg: ?? Big Nishant in You've Garg Ad");*/
        $isupdate=false;
        if(!empty($email_subject)){
            if(!empty($first_name)){
                $firstNameLength = strlen($first_name);
                if($firstNameLength >= 3){
                    if (stripos($email_subject, $first_name.' ') !== false) {
                        $email_subject = str_ireplace($first_name.' ', '', $email_subject);
                        $isupdate=true;
                    }
                    if (stripos($email_subject, $first_name.':') !== false) {
                        $email_subject = str_ireplace($first_name.':', '', $email_subject);
                        $isupdate=true;
                    }
                    if (stripos($email_subject, $first_name.',') !== false) {
                        $email_subject = str_ireplace($first_name.',', '', $email_subject);
                        $isupdate=true;
                    }
                    if (stripos($email_subject, $first_name.'!') !== false) {
                        $email_subject = str_ireplace($first_name.'!', '', $email_subject);
                        $isupdate=true;
                    }
                    if (stripos($email_subject, $first_name.'-') !== false) {
                        $email_subject = str_ireplace($first_name.'-', '', $email_subject);
                        $isupdate=true;
                    }
                    if (stripos($email_subject, $first_name."\'s") !== false) {
                        $email_subject = str_ireplace($first_name."\'s", "", $email_subject);
                        $isupdate=true;
                    }
                    if (stripos($email_subject, $first_name.'.') !== false) {
                        $email_subject = str_ireplace($first_name.'.', '', $email_subject);
                        $isupdate=true;
                    }
                    $splitFirstName = explode(" ", $email_subject);
                    $firstName = $splitFirstName[count($splitFirstName)-1];
                    $firstNameComp = strcasecmp($first_name,$firstName);
                    if($firstNameComp == 0){
                        if (stripos($email_subject, $first_name.'') !== false) {
                            $email_subject = str_ireplace($first_name.'', '', $email_subject);
                            $isupdate=true;
                        }
                    }
                    else{
                        $email_subject;
                        $isupdate=false;
                    }
                }
            }

            if(!empty($last_name)){
                $lastNameLength = strlen($last_name);
                if($lastNameLength >= 3){
                    if (stripos($email_subject, $last_name.' ') !== false) {
                        $email_subject = str_ireplace($last_name.' ', '', $email_subject);
                        $isupdate=true;
                    }
                    if (stripos($email_subject, $last_name.':') !== false) {
                        $email_subject = str_ireplace($last_name.':', '', $email_subject);
                        $isupdate=true;
                    }
                    if (stripos($email_subject, $last_name.',') !== false) {
                        $email_subject = str_ireplace($last_name.',', '', $email_subject);
                        $isupdate=true;
                    }
                    if (stripos($email_subject, $last_name.'!') !== false) {
                        $email_subject = str_ireplace($last_name.'!', '', $email_subject);
                        $isupdate=true;
                    }
                    if (stripos($email_subject, $last_name.'-') !== false) {
                        $email_subject = str_ireplace($last_name.'-', '', $email_subject);
                        $isupdate=true;
                    }if (stripos($email_subject, $last_name."\'s") !== false) {
                        $email_subject = str_ireplace($last_name."\'s", "", $email_subject);
                        $isupdate=true;
                    }
                    if (stripos($email_subject, $last_name.".") !== false) {
                        $email_subject = str_ireplace($last_name.".", "", $email_subject);
                        $isupdate=true;
                    }
                    $splitLastName = explode(" ", $email_subject);
                    $lastName = $splitLastName[count($splitLastName)-1];
                    $lastNameComp = strcasecmp($last_name,$lastName);
                    if($lastNameComp == 0){
                        if (stripos($email_subject, $last_name.'') !== false) {
                            $email_subject = str_ireplace($last_name.'', '', $email_subject);
                            $isupdate=true;
                        }
                    }
                    if($email_subject !== $email_subject1){
                        $email_subject;
                        $isupdate=true;
                    }
                    else{
                        $email_subject;
                        $isupdate=false;
                    }
                }
            }
            //echo $email_subject;die;
            if($isupdate){
                $updateSql = "UPDATE cscan_email$hy SET email_subject = '".$email_subject."', isnamereplace = '1' WHERE muid = '".$muid."' ";
                $DRW->query($updateSql,$DRW_main);
            }
        }
    }
    echo "Replace Email Name completed";die;
}else{
   echo "No email panelist matched";die; 
}
