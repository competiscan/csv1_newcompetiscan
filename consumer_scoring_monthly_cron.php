#!/usr/bin/php
<?php
require_once ("includes/dbcon.php");
$DRW->databaseReadWrite_die = 1;
include_once 'includes/functions.php';
calculateMonthlyReport();
function calculateMonthlyReport() {
    global $DRW, $DRW_main, $DRW_read2, $DRW_digital;
    $success = '';
    $sql = "SELECT * FROM cscan_consumer_scoring_report_total";
    $rs = $DRW->query($sql, $DRW_read2);
    $curmonthyear = date('Ym');
    while ($row = $DRW->fetch_assoc($rs)) {
        $ids = $row['id'];
        $panelist_id = $row['panelist_id'];
        $competi_id = $row['competi_id'];
        $direct_mail_piece = $row['direct_mail_piece'];
        $direct_mail_point = $row['direct_mail_point'];
        $email_piece = $row['email_piece'];
        $email_piece_point = $row['email_piece_point'];
        $digital_point = $row['digital_point'];
        $bag_remaining = $row['bag_remaining'];
        $bonus_point = $row['bonus_point'];
        $total_point = $row['total_point'];
        $insert_date = $row['insert_date'];
        $reset_by = $row['reset_by'];
        $reset_date = $row['reset_date'];
        $bagupdate_by = $row['bagupdate_by'];
        $bagupdate_date = $row['bagupdate_date'];
        $add_bonus_point_date = $row['add_bonus_point_date'];
        $add_bonus_point_by = $row['add_bonus_point_by'];
        $sqlchild = "SELECT id FROM cscan_consumer_scoring_monthly_report WHERE DATE_FORMAT(insert_date,'%Y%m')='" . $curmonthyear . "' AND  panelist_id ='" . $panelist_id . "'";
        $rschild = $DRW->query($sqlchild, $DRW_read2);
        $DRW->num_rows($rschild);
        if ($DRW->num_rows($rschild) > 0) {
            $rowchild = $DRW->fetch_assoc($rschild);
            $oldids = $rowchild['id'];
            $sqltotal = "update cscan_consumer_scoring_monthly_report set
                                total_point='" . $total_point . "',
                                direct_mail_piece= '" . $direct_mail_piece . "',
                                direct_mail_point='" . $direct_mail_point . "',
                                email_piece='" . $email_piece . "',
                                email_piece_point='" . $email_piece_point . "',  
                                competi_id          ='".$competi_id."',    
                                digital_point='" . $digital_point . "',
                                bonus_point='" . $bonus_point . "',   
                                bag_remaining  = '" . $bag_remaining . "',
                                reset_by      ='" . $reset_by . "', 
                                reset_date    ='" . $reset_date . "', 
                                bagupdate_by  ='" . $bagupdate_by . "', 
                                bagupdate_date    ='" . $bagupdate_date . "',
                                add_bonus_point_date='" . $add_bonus_point_date . "',  
                                add_bonus_point_by='" . $add_bonus_point_by . "'     
                                where panelist_id = '" . $panelist_id . "' AND id='" . $oldids . "'
                            ";
        } else {
            $sqltotal = "insert into cscan_consumer_scoring_monthly_report set
                                total_point='" . $total_point . "',
                                direct_mail_piece= '" . $direct_mail_piece . "',
                                direct_mail_point='" . $direct_mail_point . "',
                                email_piece='" . $email_piece . "',
                                email_piece_point='" . $email_piece_point . "',                            
                                digital_point='" . $digital_point . "',
                                bonus_point='" . $bonus_point . "',   
                                bag_remaining  = '" . $bag_remaining . "',
                                reset_by      ='" . $reset_by . "', 
                                reset_date    ='" . $reset_date . "', 
                                bagupdate_by  ='" . $bagupdate_by . "', 
                                bagupdate_date    ='" . $bagupdate_date . "',
                                add_bonus_point_date='" . $add_bonus_point_date . "',  
                                add_bonus_point_by='" . $add_bonus_point_by . "', 
                                competi_id          ='".$competi_id."',    
                                panelist_id = '" . $panelist_id . "'
                            ";
        }

        $rstotal = $DRW->query($sqltotal, $DRW_main);
    }
}
?>
