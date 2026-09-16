<?php
require_once("includes/dbcon.php");
if(!empty($_GET) && !empty($_GET['trmsg'])){
    $tracking_id = trim($_GET['trmsg']);
    if(!is_numeric($tracking_id)){
        $tracking_id = base64_decode(base64_decode(base64_decode($tracking_id)));
    }
    if(empty($tracking_id)){
        header("location: https://www.competiscan.com");
        die;
    }
    $sql = "UPDATE cscan_email_content_search_track SET is_opened = 1 WHERE id = '".$tracking_id."'";
    $DRW->query($sql,$DRW_main);
}
