#!/usr/bin/php
<?php
require_once ("includes/dbcon.php");
$DRW->databaseReadWrite_die = 1;
include_once 'includes/functions.php';
date_default_timezone_set('America/Chicago');
echo 'Start: '.date("Y-m-d H:i:s").'</br></br>';
$seconds=5; //Wait time in second
$start_date = '2022-06';
//$end_date = '2021-06';
$end_date=date("Y-m");
$dateArray=array();
while (strtotime($start_date) <= strtotime($end_date)) {
       $dateArray[]=$start_date;
       $start_date = date ("Y-m", strtotime("+1 month", strtotime($start_date)));
}
//echo "<pre>";
//print_r($dateArray);
//echo "</pre>"; die;
if(!empty($dateArray)){
    foreach ($dateArray as $curdate){
        insertMonthData($curdate);
            sleep($seconds); 
    }
}
function insertMonthData($curdate){
    global $DRW,$DRW_main,$DRW_read,$DRW_read2,$DRW_digital;
    
        $sqlquery = "select id,panelist_id,competi_id,parent_panelist_id,SUM(email_piece) as email_piece ,SUM(email_piece_point) as email_piece_point,entry_date,insert_date from cscan_provider_scoring_daily_basis_reports 
        where  DATE_FORMAT(insert_date, '%Y-%m') = '".$curdate."' GROUP BY panelist_id,MONTH(insert_date)";
        //echo $sqlquery; 
        $sqlqueryres = $DRW->query($sqlquery, $DRW_read2);        
   
    if ($DRW->num_rows($sqlqueryres) > 0) {   
        while ($row = $DRW->fetch_assoc($sqlqueryres)) {
            $sql        =   '';  
            $panelistid = $row['panelist_id'];
            $competi_id = $row['competi_id'];
            $parent_panelist_id=$row['parent_panelist_id'];
            $email_piece     = $row['email_piece'];
            $email_piece_point     = $row['email_piece_point'];
            $entry_date     = $row['entry_date'];
            $insert_date     = $row['insert_date'];
            $selsql     =   "select id,panelist_id,competi_id from cscan_provider_scoring_monthly_reports 
                            where panelist_id = '".$panelistid."' AND competi_id='".$competi_id."' AND DATE_FORMAT(insert_date, '%Y-%m') = '".$curdate."'                           
                         ";
            $ressql     = $DRW->query($selsql,$DRW_read2);  
            if ($DRW->num_rows($ressql) > 0) {  
                $rowdata = $DRW->fetch_assoc($ressql) ;
                $id      = $rowdata['id'];                
                $sql  = "update cscan_provider_scoring_monthly_reports set 
                            email_piece = '".$email_piece."',
                            email_piece_point='".$email_piece_point."'
                            where id='".$id."' AND competi_id='".$rowdata['competi_id']."'";    
            } else{
                $sql  = "insert into cscan_provider_scoring_monthly_reports set 
                        panelist_id='".$panelistid."',
                        competi_id ='".$competi_id."',
                        parent_panelist_id='".$parent_panelist_id."',
                        email_piece = '".$email_piece."',
                        email_piece_point='".$email_piece_point."',entry_date='".$entry_date."',
                        insert_date='".$insert_date."'";  
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
