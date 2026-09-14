#!/usr/bin/php
<?php
require_once ("includes/dbcon.php");
$DRW->databaseReadWrite_die = 1;
include_once 'includes/functions.php';
date_default_timezone_set('America/Chicago');
$todaydate    =    date('Y-m-d');
$curdate    =   date('Y-m-d', strtotime('-1 day', strtotime($todaydate)));
if(!empty($_REQUEST['date'])){
    $curdate    =   $_REQUEST['date'];
}
//echo $curdate; die;
$seconds=5; //Wait time in second
$search_date='';
$selectcriteriasql = "SELECT id,entry_date FROM cscan_consumer_scoring_date where entry_type=2 order by id desc limit 1 ";
$selectcriteriares = $DRW->query($selectcriteriasql, $DRW_read2);     
$selectcriteria = $DRW->fetch_assoc($selectcriteriares);
if(!empty($selectcriteria)){
    $search_date = $selectcriteria['entry_date'];   
}
if($search_date<$curdate){
$mailtype=array('directmail'=>1,'email'=>2,'mobildedigital'=>3);
insertData($mailtype['directmail'],$curdate); 
sleep($seconds);
insertData($mailtype['email'],$curdate);
sleep($seconds);
insertData($mailtype['mobildedigital'],$curdate);
sleep($seconds);
calculateTotal($curdate);
}
function insertData($type,$curdate){
    global $DRW,$DRW_main,$DRW_read,$DRW_read2,$DRW_digital;
    if($type=='1'){
        $sqlquery = "SELECT p.panelist_id , p.competi_id ,p.parent_panelist_id, count(*) as pieces
                    FROM chicagorecords c
                    LEFT JOIN cscan_panelists p ON ( p.panelist_id = c.panelist_id ) 
                    WHERE DATE_FORMAT(c.crm_import_date, '%Y-%m-%d') = '".$curdate."' AND p.panelist_id IS NOT NULL
                    AND p.contact_type='cons_panelist'
                    AND p.active=1
                    GROUP BY p.panelist_id
                "; 
        $sqlqueryres = $DRW->query($sqlquery, $DRW_read2);       
    }
    else if($type=='2'){
       /*$sqlquery = "SELECT p.panelist_id , p.competi_id ,p.parent_panelist_id,  count(*) as pieces
                    FROM cscan_email as e
                    JOIN cscan_email_text as et on (et.muid=e.muid)
                    LEFT JOIN cscan_panelists p ON ( p.panelist_id = e.panelist_id ) 
                    WHERE DATE_FORMAT(e.email_date, '%Y-%m-%d') = '".$curdate."' AND p.panelist_id IS NOT NULL
                    AND p.contact_type='cons_panelist'
                    AND p.active=1
                    GROUP BY p.panelist_id
                    ";*/
        $sqlquery = "SELECT p.panelist_id , p.competi_id ,p.parent_panelist_id,  ec.total_email as pieces
                    FROM cscan_consumer_entry as ec
                    LEFT JOIN cscan_panelists p ON ( p.panelist_id =ec.panelist_id ) 
                    WHERE DATE_FORMAT(ec.entrydate, '%Y-%m-%d') = '".$curdate."' AND p.panelist_id IS NOT NULL
                    AND p.contact_type='cons_panelist'
                    AND p.active=1
                    AND ((ec.contact_type_m_c IS NULL) OR (ec.contact_type_m_c='cons_panelist'))
                    GROUP BY p.panelist_id
                    "; 
        $sqlqueryres = $DRW->query($sqlquery, $DRW_read2);        
    }
    else if($type=='3'){
        $sqlquery = "SELECT b.panelist_id, b.competi_id ,b.parent_panelist_id, ".DIGITAL_MAIL_POINT." AS pieces 
                
                     FROM cscan_digital_observation a 
                     INNER JOIN cscan_panelists b ON(a.panelist_id = b.panelist_id) 
                     where DATE_FORMAT(a.date_observed, '%Y-%m-%d')= '".$curdate."' AND b.panelist_id IS NOT NULL
                     AND b.contact_type='cons_panelist'  
                     AND b.active=1
                     GROUP BY panelist_id
                    ";
        $sqlqueryres = $DRW->query($sqlquery, $DRW_digital);           
    }
    if ($sqlqueryres) {
        while ($row = $DRW->fetch_assoc($sqlqueryres)) {
            $sql        =   '';  
            $panelistid = $row['panelist_id'];
            $competi_id = $row['competi_id'];
            $parent_panelist_id=$row['parent_panelist_id'];
            $pieces     = $row['pieces'];
            $selsql     =   "select id,panelist_id,competi_id, digital_update_date, digital_point from cscan_consumer_scoring_daily_report 
                            where panelist_id = '".$panelistid."' AND competi_id='".$competi_id."' AND DATE_FORMAT(insert_date, '%Y-%m-%d') = '".$curdate."'                           
                         ";
            $ressql     = $DRW->query($selsql,$DRW_read2);  
            if ($DRW->num_rows($ressql) > 0) {  
                $rowdata = $DRW->fetch_assoc($ressql) ;
                $id      = $rowdata['id'];                
                  if($type=='1'){
                        $direct_mail_point    = ($pieces*DIRECT_EMAIL_PIECE_MULTIPLIER);
                        $sql  = "update cscan_consumer_scoring_daily_report set 
                                 direct_mail_piece = '".$pieces."',
                                 direct_mail_point='".$direct_mail_point."'
                                 where id='".$id."' AND competi_id='".$rowdata['competi_id']."'
                                "; 
                    }
                    else if($type=='2'){
                        $email_piece_point    = ($pieces*EMAIL_PIECE_MULTIPLIER);
                        $sql  = "update cscan_consumer_scoring_daily_report set 
                                 email_piece = '".$pieces."',
                                 email_piece_point='".$email_piece_point."'
                                 where id='".$id."' AND competi_id='".$rowdata['competi_id']."' 
                                "; 
                    }                    
                    else if($type=='3'){
                        $digital_update_date = $rowdata['digital_update_date'];
                        $digital_point = $rowdata['digital_point'];
                        if(date("my", strtotime($curdate)) != date("my", strtotime($digital_update_date)) || $digital_point == 0){
                            $sql  = "update cscan_consumer_scoring_daily_report set 
                                 digital_point  = '".$pieces."',
                                 digital_update_date= '".$curdate."'  
                                 where id='".$id."' AND competi_id='".$rowdata['competi_id']."'
                                ";
                        }
                    }
                } else{
                    if($type=='1'){
                        $direct_mail_point    = ($pieces*DIRECT_EMAIL_PIECE_MULTIPLIER);
                        $sql  = "insert  into cscan_consumer_scoring_daily_report set 
                                panelist_id='".$panelistid."',
                                competi_id ='".$competi_id."',
                                parent_panelist_id='".$parent_panelist_id."',
                                direct_mail_piece = '".$pieces."',
                                direct_mail_point='".$direct_mail_point."',entry_date='".$curdate."',
                                insert_date='".$curdate."'
                                "; 
                    }
                    if($type=='2'){
                        $email_piece_point    = ($pieces*EMAIL_PIECE_MULTIPLIER);
                        $sql  = "insert  into cscan_consumer_scoring_daily_report set 
                                panelist_id='".$panelistid."',
                                competi_id ='".$competi_id."',
                                parent_panelist_id='".$parent_panelist_id."',
                                email_piece = '".$pieces."',
                                email_piece_point='".$email_piece_point."',entry_date='".$curdate."',
                                insert_date='".$curdate."'
                                "; 
                    }
                    else if($type=='3'){
                        $sql  = "insert  into cscan_consumer_scoring_daily_report set 
                                panelist_id='".$panelistid."',
                                competi_id ='".$competi_id."',
                                parent_panelist_id='".$parent_panelist_id."',
                                digital_point = '".$pieces."',
                                entry_date='".$curdate."',
                                insert_date='".$curdate."',
                                digital_update_date= '".$curdate."'
                                "; 
                    }
                }     
                if(!empty($sql)){
                   // echo $sql."<br>";
                    $DRW->query($sql,$DRW_main);
                }    
            }
    }
}
function calculateTotal($curdate){
    global $DRW,$DRW_main,$DRW_read,$DRW_read2,$DRW_digital;
    $success='';
    $selsql     =   "select id from cscan_consumer_scoring_daily_report where DATE_FORMAT(insert_date, '%Y-%m-%d') = '".$curdate."'";                
    $ressql     = $DRW->query($selsql,$DRW_read2);  
    if ($DRW->num_rows($ressql) > 0) {  
        while($rowdata = $DRW->fetch_assoc($ressql) ){
            $id     =   $rowdata['id'];
            $sql    = "update cscan_consumer_scoring_daily_report set 
                        total_point = (direct_mail_point+email_piece_point+digital_point)
                        where id='".$id."'
                    "; 
            $res = $DRW->query($sql,$DRW_main); 
            $success=1;         
        }
        if($success==1){
            $sql    = "insert into cscan_consumer_scoring_date set 
                            entry_date = '".$curdate."',entry_type=2";
            $res = $DRW->query($sql,$DRW_main);
        }    
    } 
    /*$sqldelete  =   " DELETE FROM cscan_consumer_scoring_daily_report 
                        WHERE competi_id NOT REGEXP '-12-'
                    ";
    $rsdelete   =   $DRW->query($sqldelete,$DRW_main);*/ 
}
?>
