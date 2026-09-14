#!/usr/bin/php
<?php
require_once ("includes/dbcon.php");
$DRW->databaseReadWrite_die = 1;
include_once 'includes/functions.php';
$todaydate    =    date('Y-m-d');

$curdate    =   date('Y-m-d', strtotime('-1 day', strtotime($todaydate)));

if(!empty($_REQUEST['date'])){
    $curdate    =   $_REQUEST['date'];
}
$search_date='';
$selectcriteriasql = "SELECT id,entry_date FROM cscan_consumer_scoring_date where entry_type=1 order by id desc limit 1 ";
$selectcriteriares = $DRW->query($selectcriteriasql, $DRW_read2);     
$selectcriteria    = $DRW->fetch_assoc($selectcriteriares);
if(!empty($selectcriteria)){
    $search_date = $selectcriteria['entry_date'];   
}
if($search_date<$curdate){
    calculateBags($curdate); 
}
function calculateBags($curdate){
    global $DRW,$DRW_main,$DRW_read2,$DRW_digital;
    $sqlquery = "SELECT p.panelist_id , p.competi_id ,p.parent_panelist_id, count(*) as pieces
                    FROM chicagorecords c
                    LEFT JOIN cscan_panelists p ON ( p.panelist_id = c.panelist_id ) 
                    WHERE DATE_FORMAT(c.crm_import_date, '%Y-%m-%d') = '".$curdate."' AND p.panelist_id IS NOT NULL
                    GROUP BY p.panelist_id
                ";
    $sqlqueryres = $DRW->query($sqlquery, $DRW_read2); 
    $success='';      
    if ($sqlqueryres) {
        while ($row = $DRW->fetch_assoc($sqlqueryres)) {
            $sql        =   '';  
            $panelistid = $row['panelist_id'];
            $competi_id = $row['competi_id'];
            $parent_panelist_id=$row['parent_panelist_id'];
            $pieces     = $row['pieces'];
                if($pieces>0 ){
                    $selsql     =   "select id,panelist_id,competi_id,bag_remaining from cscan_consumer_scoring_report_total 
                                    where 
                                    panelist_id='".$panelistid."'
                                    AND competi_id ='".$competi_id."'
                                    
                                    ";
                    $ressql     = $DRW->query($selsql,$DRW_read2); 
                        if ($DRW->num_rows($ressql) > 0) {  
                            $rowdata        =   $DRW->fetch_assoc($ressql) ;
                            $id             =   $rowdata['id'];
                            $bag_remaining  =   $rowdata['bag_remaining'];
                            $new_bag_remaining= ($bag_remaining-1);
                                if($new_bag_remaining<=0){
                                    $new_bag_remaining  =   4;
                                }
                            $sql  = "update cscan_consumer_scoring_report_total set 
                                        bag_remaining='".$new_bag_remaining."'
                                        where id='".$id."'
                                    "; 
                            $DRW->query($sql,$DRW_main); 
                            $success=1;       
                        }
                }  
            }
            if($success==1){
                $sql    = "insert into cscan_consumer_scoring_date set 
                                entry_date = '".$curdate."' ,
                                entry_type=1

                            "; 
                $res = $DRW->query($sql,$DRW_main);
            }    
    }
}
?>
