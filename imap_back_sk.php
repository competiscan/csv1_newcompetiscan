#!/usr/bin/php
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once("includes/ehLog_set.php");
$ehL->start(__FILE__);
ini_set("default_charset", "utf-8");
//ini_set("memory_limit", "512M");
ini_set("memory_limit", "-1");
set_time_limit(0);

include_once('includes/dbcon.php');
$DRW->databaseReadWrite_die = 1;
require_once('includes/clean.php');
require_once('includes/functions.php');
$mid='';
$where='';
if(isset($_GET['muid']) && $_GET['muid']!=''){
    $mid=$_GET['muid'];
    if($mid){
        $where=' AND muid>'.$mid;
    }
}

if($_SERVER['argc']>0) {
    $mid = $_SERVER['argv'][1];
    if(!empty($mid)){
        $where=' AND muid>'.$mid;
    }
}
if($mid==''){
    $query_track = "SELECT muid FROM `cscan_email_update_track` order by id desc limit 0,1";                    
    $query_track_res = $DRW->query($query_track, $DRW_read);
    $data_track = $DRW->fetch_row($query_track_res);
    $muid_track =$data_track[0];
     if(!empty($muid_track)){
        $where=' AND muid>'.$muid_track;
    }
} 
$query = "SELECT muid FROM `cscan_email201707` WHERE `email_from_one`='consumers@sbkcenter.com' ".$where." limit 0,2000";                    

$query_result = $DRW->query($query, $DRW_read);
while($data_e = $DRW->fetch_row($query_result)){
$muid = (int) $data_e[0];
    if($muid){ 
         $query_t = "SELECT cettext FROM `cscan_email_text201707` WHERE `muid`='".$muid."' AND `cettype`='text/html' order by cetid desc";                  

         $query_result_t = $DRW->query($query_t, $DRW_read);
         $data_t = $DRW->fetch_row($query_result_t);
         $cettext = strstr(strip_tags($data_t[0]),'Subject:',true);
        if(strstr($cettext,'To:')){
            $cettext=strstr($cettext,'To:');
            $cettext=str_replace('To:','',$cettext);
            $cettext=str_replace('&lt;','',$cettext);
            $cettext_email=strtolower(trim(str_replace('&gt;','',$cettext)));
            if(strstr($cettext_email,'sent:')){
                $cettext_email=trim(strstr($cettext_email,'sent:',true));
            }
            $cettext_email=trim(str_replace('"','',$cettext_email));
            if($cettext_email){
                $result_c_p = $DRW->query("SELECT first_name,last_name,panelist_id,stateID FROM cscan_panelists WHERE active=1 AND (email='" . $DRW->real_escape_string($cettext_email) . "' OR alt_email='" . $DRW->real_escape_string($cettext_email) . "' OR more_email LIKE '%" . mysqlLike($cettext_email) . "%') LIMIT 1", $DRW_read);
                if($DRW->num_rows($result_c_p)>0){
                    $data_c_p = $DRW->fetch_row($result_c_p);
                    $first_name = $data_c_p[0];
                    $last_name = $data_c_p[1];
                    $full_name=$first_name.' '.$last_name;
                    $email_from='"'.$full_name.'"'.' &lt;'.$cettext_email.'&gt;'; 
                    $panelist_id = (int) $data_c_p[2];
                    $email_stateID = (int) $data_c_p[3];
                    $DRW->free_result($result_c_p);
                    if($panelist_id){
                       $query = "UPDATE `cscan_email201707` SET email_from='".addslashes($email_from)."',email_from_one='" . $DRW->real_escape_string($cettext_email)."',panelist_id='".$panelist_id."' WHERE muid=$muid";
                       //echo '<br><br>';
                       $DRW->query($query, $DRW_main);

                    }
                }

            }

        }
        
        $query_ins = "INSERT INTO `cscan_email_update_track` (muid) VALUES (".$muid.")";
        $DRW->query($query_ins, $DRW_main);
    }
}
echo 'END '.$muid;
die;
?>