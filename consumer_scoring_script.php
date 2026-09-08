#!/usr/bin/php
<?php
require_once ("includes/dbcon.php");
$DRW->databaseReadWrite_die = 1;
include_once 'includes/functions.php';
date_default_timezone_set('America/Chicago');
echo 'Start: '.date("Y-m-d H:i:s").'</br></br>';
$seconds=5; //Wait time in second
$start_date = '2021-01-01';
$end_date = '2021-06-21';
if(!empty($_REQUEST['startdate']) && !empty($_REQUEST['enddate'])){
    $start_date =  $_REQUEST['startdate'];
    $end_date =$_REQUEST['enddate'];
}

$dateArray=array();
while (strtotime($start_date) <= strtotime($end_date)) {
       $dateArray[]=$start_date;
       $start_date = date ("Y-m-d", strtotime("+1 day", strtotime($start_date)));
}
//echo "<pre>";
//print_r($dateArray);
//echo "</pre>"; die;
if(!empty($dateArray)){
    foreach ($dateArray as $curdate){
            insertData($curdate);
            sleep($seconds); 
    }
}
function insertData($curdate){
    global $DRW,$DRW_main,$DRW_read,$DRW_read2,$DRW_digital;
    
        $sqlquery = "SELECT p.panelist_id , p.competi_id ,p.parent_panelist_id, count(ppe.panelist_id) as email_piece, SUM(ppe.panelist_score) as email_piece_point
                    FROM cscan_prod_panelist_email as ppe
                    LEFT JOIN cscan_panelists p ON ( p.panelist_id =ppe.panelist_id ) 
                    WHERE DATE_FORMAT(ppe.email_date, '%Y-%m-%d') = '".$curdate."' AND p.panelist_id IS NOT NULL
                    AND p.contact_type='prod_panelist'
                    AND p.active=1
                    GROUP BY p.panelist_id,DATE(ppe.email_date)";
        $sqlqueryres = $DRW->query($sqlquery, $DRW_read2);        
   
    if ($sqlqueryres) {
        while ($row = $DRW->fetch_assoc($sqlqueryres)) {
            $sql        =   '';  
            $panelistid = $row['panelist_id'];
            $competi_id = $row['competi_id'];
            $parent_panelist_id=$row['parent_panelist_id'];
            $email_piece     = $row['email_piece'];
            $email_piece_point     = $row['email_piece_point'];
            $selsql     =   "select id,panelist_id,competi_id from cscan_producer_scoring_daily_basis_reports 
                            where panelist_id = '".$panelistid."' AND competi_id='".$competi_id."' AND DATE_FORMAT(insert_date, '%Y-%m-%d') = '".$curdate."'                           
                         ";
            $ressql     = $DRW->query($selsql,$DRW_read2);  
            if ($DRW->num_rows($ressql) > 0) {  
                $rowdata = $DRW->fetch_assoc($ressql) ;
                $id      = $rowdata['id'];                
                $sql  = "update cscan_producer_scoring_daily_basis_reports set 
                            email_piece = '".$email_piece."',
                            email_piece_point='".$email_piece_point."'
                            where id='".$id."' AND competi_id='".$rowdata['competi_id']."'";    
            } else{
                $sql  = "insert  into cscan_producer_scoring_daily_basis_reports set 
                        panelist_id='".$panelistid."',
                        competi_id ='".$competi_id."',
                        parent_panelist_id='".$parent_panelist_id."',
                        email_piece = '".$email_piece."',
                        email_piece_point='".$email_piece_point."',entry_date='".$curdate."',
                        insert_date='".$curdate."'";  
            }     
            if(!empty($sql)){
                //echo $sql."<br>"; die;
                $DRW->query($sql,$DRW_main);
            }    
        }
    }
}
echo 'completed: ';
echo 'End: '.date("Y-m-d H:i:s").'</br></br>';
die;
?>
