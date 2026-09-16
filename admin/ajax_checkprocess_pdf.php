<?php
require_once("../auth_auth.php");
$status=0;
if(isset($_REQUEST['action']) && $_REQUEST['action']=='checkprocesspdf' && $_REQUEST['muid']!='') {
    $muid=$_REQUEST['muid'];   
    $pdf_url='https://api3.competiscan.com/html-pdf/v2/temppdf/'.$muid; 
    $ch = curl_init($pdf_url); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, TRUE);
    curl_setopt($ch, CURLOPT_NOBODY, TRUE);
    $data = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
    $size = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
    //2500
    if ($code == 200 AND $size>=1000) {
        $status =1;
    } else {
        $status =0;
    }
    curl_close($ch);
    echo $status;   die;     
        
}else{
  echo $status;   die; 

}?>
