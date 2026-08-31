#!/usr/bin/php
<?php
require_once("includes/ehLog_set.php");
$ehL->start(__FILE__);
require_once ("includes/dbcon.php");
$DRW->databaseReadWrite_die = 1;
include_once 'includes/functions.php';
include_once 'includes/thumb.php';
ini_set("memory_limit", "-1");
set_time_limit(0);

    //$sqls = "select  ad_md5 from cscan_digital_creative where panelist_id IS NULL OR panelist_id<=0 OR panelist_id='' limit 0,50000";
    /*$sqls="select  distinct ob.ad_md5 from cscan_digital_observation ob 
join cscan_digital_creative c on (c.ad_md5=ob.ad_md5)
 where c.panelist_id IS NULL";*/
    $sqls = "select  distinct ad_md5 from cscan_digital_observation where date_observed>=(Now()-INTERVAL 24 HOUR)";
       
    $results = $DRW->query($sqls, $DRW_digital);
     while ($rows = $DRW->fetch_array($results)) {
            $admd = $rows['ad_md5'];     
     
    $sqlss = "select table_name from cscan_digital_observation_tables";
    $resultss = $DRW->query($sqlss, $DRW_digital);   
    $allpanelist = array();
    while ($rows = $DRW->fetch_array($resultss)) {
        $tblsname = $rows['table_name'];
        $sql= "SELECT DISTINCT t1.panelist_id FROM " . $tblsname . " t1 where t1.ad_md5='".$admd."' AND t1.panelist_id>0";
        $rs = $DRW->query($sql, $DRW_digital);
        $resultCount = $DRW->num_rows($rs);
        if ($resultCount > 0) {
            while ($dataC = $DRW->fetch_row($rs)){            
                 if (in_array($dataC[0], $allpanelist)) {
                    continue;
                }
                if($dataC[0]>0)
                 $allpanelist[] = $dataC[0];
            }
        }
    }
  //  print_r($allpanelist); die;
    if(count($allpanelist)>0){
        $allpanelist= array_unique($allpanelist);
        $pan_str=implode(',',$allpanelist);
        $sqlu= "UPDATE cscan_digital_creative set panelist_id='".$pan_str."' where ad_md5='".$admd."'";
        $rsu = $DRW->query($sqlu, $DRW_digital);
    }
    
}

