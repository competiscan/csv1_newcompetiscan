<?php
require_once('includes/dbcon.php');
if(isset($_POST['totaldownload']) && isset($_POST['userid'])){
    $totaldownload  =   $_POST['totaldownload'];
    $userid         =   $_POST['userid'];
    $query          =   "SELECT activity_id FROM  cscan_search_activity WHERE userID='".$userid."' order by activity_id desc limit 1";
    $query_result   =   $DRW->query($query,$DRW_read);
    $data           =   $DRW->fetch_row($query_result);
    $activity_id    =   $data[0];
    if(!empty($activity_id)){
    $sql = "update cscan_search_activity set total_download='".$totaldownload."' where userID='".$userid."' and activity_id='".$activity_id."'";
    $DRW->query($sql, $DRW_main);
    }
    //echo "success";
}
?>