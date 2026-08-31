<?php
require_once('includes/globalSession.php');
//allow to display on home page with no login
//if(!isset($_SESSION['public_admin_access'])){
//	require_once('includes/checklogin.php');
//}
require_once 'HTTP/Download.php';

if(isset($_REQUEST['id'])) $productID = (int)$_REQUEST['id'];
else $productID = 0;
if(isset($_REQUEST['cid'])) $img_companyID = (int)$_REQUEST['cid'];
else $img_companyID = 0;
if(isset($_REQUEST['iid'])) $iid = (int)$_REQUEST['iid'];
else $iid = 1;

/*##### Added to upload on S3 bucket #####*/
    $isS3=false;
/*##### EndAdded to upload on S3 bucket ##### */

if($productID!=0 || $img_companyID!=0){
	if($img_companyID==0){
		$query2 = "SELECT img_id,img_filename,img_content_type,img_size_byte,UNIX_TIMESTAMP(img_createddate),img_path,img_companyID FROM cscan_img WHERE productID=$productID AND img_id=$iid";
		$query_result2 = $DRW->query($query2,$DRW_read);
		$data2 = $DRW->fetch_row($query_result2);
		$img_id = (float)$data2[0];
                if(!empty($img_id)){
                    $img_filename = $data2[1];
                    $img_content_type = $data2[2];
                    $img_size_byte = $data2[3];
                    $img_createddate = $data2[4];
                    $img_path = $data2[5];
                    $img_companyID = $data2[6];
                    $DRW->free_result($query_result2);
                  /*##### Added to get document from S3 bucket ##### */
                    $s3Keyname=strstr($img_path,'productImages').$img_filename;
                    $isS3=true;                    
                  /*##### End Added to get document from S3 bucket##### */
                    
                }else{
                    $query2 = "SELECT companyID FROM cscan_company_product WHERE productID=$productID AND primary_co=1";
                    $query_result2 = $DRW->query($query2,$DRW_read);
                    $data2 = $DRW->fetch_row($query_result2);
                    $img_companyID = $data2[0];
                }
	}
	else{
		$img_id = 0;
		$img_filename = '';
		$img_content_type = '';
		$img_size_byte = 0;
		$img_createddate = 0;
		$img_path = '';
	}
	$src = dirname(__FILE__)."$img_path$img_filename";
	if($img_companyID!=0){
		$query2 = "SELECT img_co_content_type,img_co_size_byte,UNIX_TIMESTAMP(img_co_createddate),img_co_path,img_co_filename FROM cscan_img_company WHERE companyID=$img_companyID";
		$query_result2 = $DRW->query($query2,$DRW_read);
		$data2 = $DRW->fetch_row($query_result2);
		$img_content_type = $data2[0];
		$img_size_byte = $data2[1];
		$img_createddate = $data2[2];
		$img_path = $data2[3];
		$img_filename = $data2[4];
		$DRW->free_result($query_result2);
		$src = dirname(__FILE__)."$img_path$img_filename";
                $s3Keyname=strstr($img_path,'coImages').$img_filename;
                $isS3=true;
	}
	
        /*##### Added to get document from S3 bucket #####*/
        //echo "Bucket".$bucket_name."Key".$s3Keyname;
	if($img_filename!="" && $isS3) {
                    $results = $s3->getObject([
                        'Bucket' => $bucket_name,
                        'Key'    => $s3Keyname,

                    ]);
               /*echo "Bucket".$bucket_name."Key".$s3Keyname."<br/>"; 
               echo "<pre>";
               print_r($results);die;*/
                if(!empty($results)){
                    header("Content-Type: {$results['ContentType']}");
                    echo $results['Body']; exit;
                }else{
                   @ob_end_clean();
                    $altfile = dirname(__FILE__).'/images/thumbNA.gif';
                    makeCacheable(filemtime($altfile));
                    header("Content-Type: image/gif");
                    readfile($altfile);
                }
                
        /*##### End Added to get document from S3 bucket #####*/
                
        }else if($img_filename!="" && is_file($src)) {
		@ob_end_clean();
		
		$dl = new HTTP_Download();
		$dl->setFile($src);
		$dl->setLastModified($img_createddate);
		$dl->setContentType($img_content_type);
		$dl->setCacheControl('public');
		$dl->setCache(true);
		$dl->setContentDisposition(HTTP_DOWNLOAD_INLINE, $img_filename);
		$dl->send();
		
		/*makeCacheable(filemtime($src));
			
		header("Content-Disposition: inline; filename=\"$img_filename\"");
		header("Content-Type: $img_content_type");
		header("Content-Length: $img_size_byte");
		header("Accept-Ranges: bytes");
		readfile($src);*/
		exit;
	}
}
@ob_end_clean();
$altfile = dirname(__FILE__).'/images/thumbNA.gif';
makeCacheable(filemtime($altfile));
header("Content-Type: image/gif");
readfile($altfile);
?>