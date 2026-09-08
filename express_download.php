<?php
require_once ("includes/dbcon.php");
if($_REQUEST['action']=='express_download'){
    $API_DOWNLOADURL =  API_DOWNLOADURL;
    //$API_DOWNLOADURL = "https://api.competiscan.com/elasticsearch-service/v1/search/download";
    $posted_jsondata=$_REQUEST['post_data'];
    if(!empty($posted_jsondata)){
    //echo $posted_jsondata;
    $ch_download = curl_init($API_DOWNLOADURL); 
    curl_setopt($ch_download, CURLOPT_POST, 1);
    curl_setopt($ch_download, CURLOPT_POSTFIELDS, $posted_jsondata);
    curl_setopt($ch_download, CURLOPT_RETURNTRANSFER, 1); 
    curl_setopt($ch_download, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    $result_download = curl_exec($ch_download);
    echo $result_download; exit;
    }else{
        echo json_last_error_msg();exit;
    } 
}
?>