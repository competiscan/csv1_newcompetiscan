<?php ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set("memory_limit", "-1");
set_time_limit(0);
require_once("includes/dbcon.php");
$chkparam='';
$limit='';
if($_SERVER['argc']>0) {
    $chkparam = $_SERVER['argv'][1];
}
$chkparam = (int)$chkparam;
//echo $chkparm; die;
if($chkparam==1){
    $limit=' limit 0,7';
}elseif($chkparam==2){
    $limit=' limit 7,7';
}elseif($chkparam==3){
    $limit=' limit 14,10';
}
//ALTER TABLE `cscan_product_detail` ADD `is_panelist_score` TINYINT NOT NULL DEFAULT '0' AFTER `packageTypeId`;
$sql_sel="select id,score_min,score_max from cscan_score_range $limit";
$query = $DRW->query($sql_sel,$DRW_read2);
$num = $DRW->num_rows( $query );
if($num > 0){
    echo 'start time: '.date('Y-m-d h:i:s');
    while( $row = $DRW->fetch_assoc($query)){
        $id = $row['id'];
        $score_min=$row['score_min'];
        $score_max=$row['score_max'];
        
        $sql_sel2="select panelist_id,score_date from cscan_panelists_additional_score where CAST(fico_score AS SIGNED)>='".$score_min."' AND CAST(fico_score AS SIGNED)<='".$score_max."'";
        $query2 = $DRW->query($sql_sel2,$DRW_read2);
        $num2 = $DRW->num_rows($query2);
        if($num2 > 0){    
            while( $row = $DRW->fetch_assoc($query2)){               
                $panelist_id=$row['panelist_id'];                
                $score_date=substr($row['score_date'],0,7);
                $sql_update="Update cscan_panelists_product set fico_range_id='".$id."' where panelist_id='".$panelist_id."' AND LEFT(ppdate, 7)='".$score_date."' AND fico_range_id<=0";
                $result=$DRW->query($sql_update, $DRW_main);
            }
        }
        
        $sql_sel3="select panelist_id,score_date from cscan_panelists_additional_score where CAST(vantage_score AS SIGNED)>='".$score_min."' AND CAST(vantage_score AS SIGNED)<='".$score_max."'";
        $query3 = $DRW->query($sql_sel3,$DRW_read2);
        $num3 = $DRW->num_rows($query3);
        if($num3 > 0){    
            while( $row = $DRW->fetch_assoc($query3)){               
                $panelist_id=$row['panelist_id'];                
                $score_date=substr($row['score_date'],0,7);
                $sql_update="Update cscan_panelists_product set vantage_range_id='".$id."' where panelist_id='".$panelist_id."' AND LEFT(ppdate, 7)='".$score_date."' AND vantage_range_id<=0";
                $result=$DRW->query($sql_update, $DRW_main);
            }
        }
       
        $sql_sel4="select panelist_id,score_date from cscan_panelists_additional_score where CAST(credit_vision AS SIGNED)>='".$score_min."' AND CAST(credit_vision AS SIGNED)<='".$score_max."'";
        $query4 = $DRW->query($sql_sel4,$DRW_read2);
        $num4 = $DRW->num_rows($query4);
        if($num4 > 0){    
            while( $row = $DRW->fetch_assoc($query4)){               
                $panelist_id=$row['panelist_id'];                
                $score_date=substr($row['score_date'],0,7);
                $sql_update="Update cscan_panelists_product set creditVision_range_id='".$id."' where panelist_id='".$panelist_id."' AND LEFT(ppdate, 7)='".$score_date."' AND creditVision_range_id<=0";
                $result=$DRW->query($sql_update, $DRW_main);
            }
        }
         
    }
    echo ' End time: '.date('Y-m-d h:i:s');
    
}
echo ' Completed to update score range id';
?>