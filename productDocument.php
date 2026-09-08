<?php require_once('includes/globalSession.php');
ini_set("default_charset", "utf-8");
if(!isset($_SESSION['public_admin_access'])){
    $is_admin = false;
}
else{
    $is_admin = true;
}
require_once 'HTTP/Download.php';
require_once 'product_doc_tracker.php';
track_user();
if(!defined('ENV')){
    define('ENV',getenv('SERVER_NAME'));
} 
if(ENV == 'localhost' || ENV == 'demo.competiscan.com'){
    $displayS3PdfUrl='http://files1.competiscan.com/';
}else{
    $displayS3PdfUrl='https://files.competiscan.com/';
}
if(isset($_REQUEST['id'])) $productID = (int)$_REQUEST['id'];
else $productID = 0;
if(isset($_REQUEST['did'])) $document_id = (int)$_REQUEST['did'];
else $document_id = 1;
$content='';
if($productID!=0){
    if(isset($_REQUEST['orig']) && $is_admin){
        $table = 'cscan_document_orig';
        $orig = 1;
    }
    else{
        $table = 'cscan_document';
        $orig = 0;
    }
    track_document($productID,$document_id,$orig);
    if($document_id==1 && $productID>0){
        $query2 = "SELECT document_id FROM $table WHERE productID=$productID AND document_id=2";
        $query_result2 = $DRW->query($query2,$DRW_read);
        $numrows=$DRW->num_rows($query_result2);
        if($numrows>0) {
           $document_id=2;
        } 
    }    
    $query_prod = "SELECT is_mobile FROM cscan_product_detail WHERE productID='".$productID."'";
    $query_result_prod = $DRW->query($query_prod,$DRW_read);
    $data_prod = $DRW->fetch_row($query_result_prod);
    $is_digital=$data_prod[0];
    if($is_digital=='1' && $document_id!=''){       
        $query_mob = "SELECT max(document_id),document_filename,document_content_type,document_size_byte,UNIX_TIMESTAMP(document_createddate),document_path FROM $table WHERE productID=$productID ";
        $query_result_mob = $DRW->query($query_mob,$DRW_read);
        $data_prod_mob = $DRW->fetch_row($query_result_mob);
    }     
    if($document_id > 1 ){
        $query2 = "SELECT document_id,document_filename,document_content_type,document_size_byte,UNIX_TIMESTAMP(document_createddate),document_path FROM $table WHERE productID=$productID AND document_id=$document_id";
        $query_result2 = $DRW->query($query2,$DRW_read);
        if($DRW->num_rows($query_result2) ==0) { 
           $document_id=1;
           $query2 = "SELECT document_id,document_filename,document_content_type,document_size_byte,UNIX_TIMESTAMP(document_createddate),document_path FROM $table WHERE productID=$productID AND document_id=$document_id"; 
        } else{ 
           $query2 = "SELECT document_id,document_filename,document_content_type,document_size_byte,UNIX_TIMESTAMP(document_createddate),document_path FROM $table WHERE productID=$productID AND document_id=$document_id"; 
           
        }
    } else{ 
        $query2 = "SELECT document_id,document_filename,document_content_type,document_size_byte,UNIX_TIMESTAMP(document_createddate),document_path FROM $table WHERE productID=$productID AND document_id=$document_id";        
    }
    $query_result2 = $DRW->query($query2,$DRW_read);
    $data2 = $DRW->fetch_row($query_result2);
    $document_id = (float)$data2[0];
    $document_filename = $data2[1];
    $document_content_type = $data2[2];
    $document_size_byte = $data2[3];
    $document_createddate = $data2[4];
    $document_path = $data2[5];
    $DRW->free_result($query_result2);        
    $pdf_src = dirname(__FILE__)."$document_path$document_filename";//exit;
    if($document_content_type=='html' && $document_path!=''){
        $pdf_src=$document_path;
        $content='<iframe style="overflow: hidden; height: 100%; width: 100%; position: absolute; border:0;" src="' . $pdf_src . '" ></iframe>';
    }else if(($document_content_type=='video/mp4' || $document_content_type=='image/mp4') && $document_filename!=''){
        $video_link=$document_path.$document_filename;
        $s3VideoURL = $displays3URL.substr($video_link,1);
        $content='<video width="900" height="800" controls><source src="' .$s3VideoURL. '" type="video/mp4" codecs="avc1.42E01E, mp4a.40.2">Your browser does not support the video tag.</video>'; 
    }else{
        if(strpos($document_path,'/')=='0'){
            $document_path  = substr($document_path,1);
        }        
        if(!empty($document_filename) && !empty($document_path)){ 
            header("Content-Type: ".$document_content_type);
            header('Content-Disposition: inline; filename="' . $document_filename . '"'); 
            header('Content-Transfer-Encoding: binary');            
            header('Accept-Ranges: bytes');            
            @readfile($displayS3PdfUrl.$document_path.$document_filename);
        }else{
            header("Content-Type: text/plain");
            print "Product has been discontinued.";
        }
        exit;       
    }
}
echo $content;die;
?>