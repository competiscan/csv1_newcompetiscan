<?php
ob_start();
require_once('includes/globalSession.php');
//require_once('includes/rpv-dashboard-function.php');
ini_set("default_charset", "utf-8");
if(!isset($_SESSION['public_admin_access'])){
	require_once('includes/checklogin.php');
	$is_admin = false;
}
else{
	$is_admin = true;
}
require_once 'HTTP/Download.php';
require_once 'product_doc_tracker.php';
if(!defined('ENV')){
define('ENV',getenv('SERVER_NAME'));
}
track_user();
//############### ADD ENCODE TREND ID############
if(isset($_REQUEST['id'])) $trendID = trim($_REQUEST['id']);
    else $trendID = 0; 

if(!empty($trendID)){
    //echo "trendID: ".$trendID; 
    echo $docURL = TREND_REPORT_DOC_API_UAT_URL . $trendID; die;
    header('Location: '.$docURL);die;
    
}
/*if($trendID){           
            
            $len="$trendID";
            $numlength = strlen((string)$len);
            if($numlength==9){
              $trendID='0'.$trendID;  
            }else if($numlength==8){
              $trendID='00'.$trendID;  
            }else if($numlength==7){
              $trendID='000'.$trendID;  
            }           
           $query2 = "SELECT trend_id,file_name,file_path FROM cscan_trend_report WHERE rndtrend_id='".$trendID."'";  
         $query_result2 = $DRW->query($query2,$DRW_read);
         if($DRW->num_rows($query_result2) > 0) { 
            $trend_data = $DRW->fetch_assoc($query_result2);
            $document_filename=$trend_data['file_name'];
            $document_path=$trend_data['file_path'];
            $trendid=$trend_data['trend_id'];
         }
         track_trend($trendid);
        ############# for display data from s3 ############################
        if(strpos($document_path,'/')=='0'){
            $document_path  = substr($document_path,1);
        }
        try {
            // Get the object.
            $result = $s3->getObject([
                'Bucket' => $bucket_name,
                'Key'    => $document_path.$document_filename,
            ]);
            if($result['ContentType']=='application/pdf'){
            header("Content-Type: {$result['ContentType']}");
            echo $result['Body'];
           }else{
                //echo "<pre>";
                //echo $result['ContentType']; die;
              //print_r($result); die; 
              header('Content-Description: File Transfer');
              header("Content-Type: {$result['ContentType']}");
              header('Content-Disposition: attachment; filename=' . $document_filename);
              header('Expires: 0');
              //header('Cache-Control: must-revalidate');
              header("Cache-Control: no-cache");
              header("Pragma: no-cache");
              //header("Content-Type: application/download");
                echo $result['Body'];
                }
            } catch (S3Exception $e) {
            echo $e->getMessage() . PHP_EOL;
            }
        }*/
?>