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
$seconds=5; //Wait time in second
$search_date='';
/*
$selectcriteriasql = "SELECT id,entry_date FROM cscan_consumer_scoring_date where entry_type=0 order by id desc limit 1 ";
$selectcriteriares = $DRW->query($selectcriteriasql, $DRW_read2);     
$selectcriteria = $DRW->fetch_assoc($selectcriteriares);
if(!empty($selectcriteria)){
    $search_date = $selectcriteria['entry_date'];   
}
if($search_date<$curdate){
//$mailtype=array('directmail'=>1,'email'=>2,'mobildedigital'=>3);
$mailtype=array('email'=>2);
//insertData($mailtype['directmail'],$curdate); 
//sleep($seconds);
insertData($mailtype['email'],$curdate);
//sleep($seconds);
//insertData($mailtype['mobildedigital'],$curdate);
//sleep($seconds);
calculateTotal($curdate);
}
*/
$mailtype=array('email'=>2);
//$datelist=array('01'=>31,'02'=>28,'03'=>31,'04'=>30,'05'=>31,'06'=>30,'07'=>31,'08'=>31,'09'=>30);
$datelist=array('09'=>30);
foreach ($datelist as $key=>$val){
	
	for ($i=1;$i<=$val;$i++){		
		$dt=$i;
		if($i<=9){
			$dt='0'.$i;
		}
		$curdate=date('Y')."-".$key."-".$dt;
		//print_r($date);
		insertData($mailtype['email'],$curdate);
		//echo"\n";
		calculateTotal($curdate);
	}
}



function insertData($type,$curdate){
    global $DRW,$DRW_main,$DRW_read2,$DRW_digital;
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
            $selsql     =   "select id,panelist_id,competi_id, digital_update_date, digital_point from cscan_consumer_scoring_report 
                            where 
                            panelist_id='".$panelistid."'                            
                         ";
            $ressql     = $DRW->query($selsql,$DRW_read2);  
            if ($DRW->num_rows($ressql) > 0) {  
                $rowdata = $DRW->fetch_assoc($ressql) ;
                $id      = $rowdata['id'];                
                  if($type=='1'){
                        $direct_mail_point    = ($pieces*DIRECT_EMAIL_PIECE_MULTIPLIER);
                        $sql  = "update cscan_consumer_scoring_report set 
                                 parent_panelist_id='".$parent_panelist_id."',
                                 direct_mail_piece = (direct_mail_piece+".$pieces."),
                                 direct_mail_point=(direct_mail_point+".$direct_mail_point."),
                                 competi_id = '".$competi_id."'
                                 where id='".$id."'
                                "; 
                    }
                    else if($type=='2'){
                        $email_piece_point    = ($pieces*EMAIL_PIECE_MULTIPLIER);
                        $sql  = "update cscan_consumer_scoring_report set 
                                 email_piece = (email_piece+".$pieces."),
                                 parent_panelist_id='".$parent_panelist_id."',
                                 email_piece_point=(email_piece_point+".$email_piece_point."),
                                 competi_id = '".$competi_id."'    
                                 where id='".$id."'
                                "; 
                                
                    }                    
                    else if($type=='3'){
                        $digital_update_date = $rowdata['digital_update_date'];
                        $digital_point = $rowdata['digital_point'];
                        if(date("my", strtotime($curdate)) != date("my", strtotime($digital_update_date)) || $digital_point == 0){
                            $sql  = "update cscan_consumer_scoring_report set 
                                 digital_point  = (digital_point+".$pieces."),
                                 parent_panelist_id='".$parent_panelist_id."',
                                 digital_update_date= '".$curdate."',
                                 competi_id = '".$competi_id."'    
                                 where id='".$id."'
                                ";
                        }
                    }
                } else{
                    if($type=='1'){
                        $direct_mail_point    = ($pieces*DIRECT_EMAIL_PIECE_MULTIPLIER);
                        $sql  = "insert  into cscan_consumer_scoring_report set 
                                panelist_id='".$panelistid."',
                                competi_id ='".$competi_id."',
                                parent_panelist_id='".$parent_panelist_id."',
                                direct_mail_piece = '".$pieces."',
                                direct_mail_point='".$direct_mail_point."',entry_date='".$curdate."'
                                "; 
                    }
                    if($type=='2'){
                        $email_piece_point    = ($pieces*EMAIL_PIECE_MULTIPLIER);
                        $sql  = "insert  into cscan_consumer_scoring_report set 
                                panelist_id='".$panelistid."',
                                competi_id ='".$competi_id."',
                                parent_panelist_id='".$parent_panelist_id."',
                                email_piece = '".$pieces."',
                                email_piece_point='".$email_piece_point."',entry_date='".$curdate."'
                                "; 
                    }
                    else if($type=='3'){
                        $sql  = "insert  into cscan_consumer_scoring_report set 
                                panelist_id='".$panelistid."',
                                competi_id ='".$competi_id."',
                                parent_panelist_id='".$parent_panelist_id."',
                                digital_point = '".$pieces."',
                                entry_date='".$curdate."',
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
    global $DRW,$DRW_main,$DRW_read2,$DRW_digital;
    $success='';
    $selsql     =   "select id from cscan_consumer_scoring_report ";                
    $ressql     = $DRW->query($selsql,$DRW_read2);  
    if ($DRW->num_rows($ressql) > 0) {  
        while($rowdata = $DRW->fetch_assoc($ressql) ){
            $id     =   $rowdata['id'];
            $sql    = "update cscan_consumer_scoring_report set 
                        total_point = (direct_mail_point+email_piece_point+digital_point)
                        where id='".$id."'
                    "; 
            $res = $DRW->query($sql,$DRW_main); 
            $success=1;         
        }
        if($success==1){
            $sql    = "insert into cscan_consumer_scoring_date set 
                            entry_date = '".$curdate."'                            
                        "; 
            //$res = $DRW->query($sql,$DRW_main);
        }    
    }
    
    
    $sql = "SELECT * FROM cscan_consumer_scoring_report where parent_panelist_id=0";
    // echo $sql;       
    $rs = $DRW->query($sql,$DRW_read2);
    while($row = $DRW->fetch_assoc($rs)){
        $ids                =       $row['id'];
        $panelist_id        =       $row['panelist_id'];
        $competi_id         =       $row['competi_id'];
        $direct_mail_piece  =       $row['direct_mail_piece'];
        $direct_mail_piece  =       $row['direct_mail_piece'];
        $direct_mail_point  =       $row['direct_mail_point'];
        $email_piece        =       $row['email_piece'];  
        $email_piece_point  =       $row['email_piece_point'];
        $digital_point      =       $row['digital_point'];
        $bag_remaining      =       $row['bag_remaining'];
        $bonus_point        =       $row['bonus_point'];
        $total_point        =       ($row['total_point']+$bonus_point);                    
        $sqlchild = "SELECT direct_mail_piece, direct_mail_point, email_piece, email_piece_point, 
                digital_point, total_point, bag_remaining FROM cscan_consumer_scoring_report WHERE parent_panelist_id='".$row['panelist_id']."'";
        $rschild = $DRW->query($sqlchild,$DRW_read2);
        $DRW->num_rows($rschild);
        if($DRW->num_rows($rschild)>0){                        
             while($rowchild = $DRW->fetch_assoc($rschild)){
                //$direct_mail_piece+=$rowchild['direct_mail_piece'];
                //$direct_mail_point+=$rowchild['direct_mail_point'];
                $email_piece+=$rowchild['email_piece'];  
                $email_piece_point+=$rowchild['email_piece_point'];
                //$digital_point+=$rowchild['digital_point']; 
                $total_point+=$rowchild['total_point'];  
                //$bag_remaining+=$rowchild['bag_remaining']; 
             }                        
        }
        
       
        
        $sqlcond = "SELECT * FROM cscan_consumer_scoring_report_total WHERE panelist_id='".$panelist_id."' AND competi_id='".$competi_id."'";
        $rscond  = $DRW->query($sqlcond,$DRW_read2);
        if ($DRW->num_rows($rscond) > 0) {  
                     
            $sqltotal   =   "update cscan_consumer_scoring_report_total set
                             total_point='".$total_point."',
                             //direct_mail_piece= '".$direct_mail_piece."',
                             //direct_mail_point='".$direct_mail_point."',
                             email_piece='".$email_piece."',
                             email_piece_point='".$email_piece_point."',                            
                             //digital_point='".$digital_point."',
                             //bonus_point='".$bonus_point."',   
                             entry_date = '".$curdate."' 
                             where panelist_id = '".$panelist_id."'
                           ";
             $sqltotal   =   "update cscan_consumer_scoring_report_total set
                             total_point='".$total_point."',
                             email_piece='".$email_piece."',
                             email_piece_point='".$email_piece_point."',                            
                             entry_date = '".$curdate."' 
                             where panelist_id = '".$panelist_id."'";
 
            $sqlhistory ="INSERT INTO cscan_consumer_scoring_report_history (id,panelist_id,competi_id,parent_panelist_id,direct_mail_piece,direct_mail_point,
                                email_piece,email_piece_point,digital_point,total_point,bag_remaining,entry_date,bagupdate_by,bagupdate_date,reset_by,reset_date,add_bonus_point_date,add_bonus_point_by
                                )
                                SELECT id,panelist_id,competi_id,parent_panelist_id,direct_mail_piece,direct_mail_point,
                                email_piece,email_piece_point,digital_point,total_point,bag_remaining,entry_date,bagupdate_by,bagupdate_date,reset_by,reset_date,add_bonus_point_date,add_bonus_point_by
                                FROM   cscan_consumer_scoring_report_total
                                where panelist_id = '".$panelist_id."'
                             ";                
                $DRW->query($sqlhistory,$DRW_main);
            
            
        }else{
             $bag_remaining= 4;
            if($email_piece>0){
                $bag_remaining= 3;
            }
            $sqltotal   =   "insert into cscan_consumer_scoring_report_total set
                             total_point='".$total_point."',
                             //direct_mail_piece= '".$direct_mail_piece."',
                             //direct_mail_point='".$direct_mail_point."',
                             email_piece='".$email_piece."',
                             email_piece_point='".$email_piece_point."',                            
                             //digital_point='".$digital_point."',
                             //bag_remaining='".$bag_remaining."',   
                             //bonus_point='".$bonus_point."',    
                             panelist_id = '".$panelist_id."',
                             competi_id = '".$competi_id."',
                             entry_date = '".$curdate."'    
                           "; 

	  $sqltotal   =   "insert into cscan_consumer_scoring_report_total set
                             total_point='".$total_point."',
                             email_piece='".$email_piece."',
                             email_piece_point='".$email_piece_point."',
                             panelist_id = '".$panelist_id."',
                             competi_id = '".$competi_id."',
                             entry_date = '".$curdate."'
                           "; 

        }
        //echo $sqltotal."<br>";
        $rstotal = $DRW->query($sqltotal,$DRW_main);
    }
    
    $sqldelete  =   " DELETE FROM cscan_consumer_scoring_report_total 
                        WHERE competi_id NOT REGEXP '-12-'
                    ";
    $rsdelete   =   $DRW->query($sqldelete,$DRW_main);        
    
}
?>
