<?php require_once('includes/globalSession.php');
ini_set("default_charset", "utf-8");

require_once 'HTTP/Download.php';
require_once 'product_doc_tracker.php';
if(!defined('ENV')){
    define('ENV',getenv('SERVER_NAME'));
}

if(isset($_REQUEST['id'])) $productID = (int)$_REQUEST['id'];
else $productID = 0;
if(isset($_REQUEST['muid'])) $muID = (int)$_REQUEST['muid'];
else $muID = 0;

$content='';
if($productID!=0){

    $query = "select muid from cscan_product_email where productID=$productID";
   
    $query_result = $DRW->query($query,$DRW_read);
    $numrows=$DRW->num_rows($query_result);
    //die;
    if($numrows>0) {
        $data = $DRW->fetch_row($query_result);
        $muid=$data[0];
        //$muid=44780208;

        if(!empty($muid) && ($muid>0)){
            $content_type='text/html';
            $filename='';
            //$htmlurl='https://html-prod.competiscan.com:5447/processedhtml/'.$muid;
            //$htmlurl='https://html-pdf.competiscan.com/processedhtml/'.$muid;
            $htmlurl='https://api3.competiscan.com/html-pdf/v2/processedhtml/'.$muid;           
            header("Content-Type: ".$content_type);
            header('Content-Disposition: inline; filename="' . $filename . '"'); 
            header('Content-Transfer-Encoding: binary');            
            header('Accept-Ranges: bytes');            
            @readfile($htmlurl);
        }else{
            header("Content-Type: text/plain");
            print "Product has been discontinued.";
        }
        exit; 

        
    }
    echo $content; die;
}elseif($muID!=0){
    
    if(!empty($muID) && ($muID>0)){
        $content_type='application/pdf';
        $filename='';
        //$htmlurl='https://html-prod.competiscan.com:5447/temppdf/'.$muID;
        //$htmlurl='https://html-pdf.competiscan.com/temppdf/'.$muID;
        $htmlurl='https://api3.competiscan.com/html-pdf/v2/temppdf/'.$muID;
        header("Content-Type: ".$content_type);
        header('Content-Disposition: inline; filename="' . $filename . '"'); 
        header('Content-Transfer-Encoding: binary');            
        header('Accept-Ranges: bytes');            
        @readfile($htmlurl);
    }else{
        header("Content-Type: text/plain");
        print "Product has been discontinued.";
    }
    exit;  
    
    echo $content; die;
}   
?>
