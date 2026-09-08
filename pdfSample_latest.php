<?php
require_once('includes/globalSession.php');
if(!isset($_SESSION['public_admin_access'])){
	require_once('includes/checklogin.php');
}
require_once 'includes/thumb.php';
require_once 'HTTP/Download.php';
if(!defined('ENV')){
    define('ENV',getenv('SERVER_NAME'));
}
/*############START CONVERT EMAIL IMAGE############*/
if(isset($_SESSION['sess_userID'])){
    $sess_userID=$_SESSION['sess_userID'];
} else{
   $sess_userID=''; 
}
/*############END CONVERT EMAIL IMAGE############*/
if(isset($_REQUEST['id'])) $productID = (int)$_REQUEST['id'];
else $productID = 0;
if(isset($_REQUEST['did'])) $document_id = (int)$_REQUEST['did'];
else $document_id = 1;

if(isset($_REQUEST['page'])) $page = (int)$_REQUEST['page'];
else $page = 0;

if(isset($_REQUEST['full'])) $full = (int)$_REQUEST['full'];
else $full = 0;
$imagesource='';

if($productID!=0){
	if($page==0){
		$where = ' AND img_document_default=1';
	}
	else{
		$where = ' AND img_document_sort='.$page;
	}
	/*############START CONVERT EMAIL IMAGE############*/
            $pptSql1 = "SELECT ppt_status FROM cscan_user_single_image_ppt WHERE productID='".$productID."' AND userID='".$sess_userID."'";
            $pptQuery1 = $DRW->query($pptSql1,$DRW_read);
            if($DRW->num_rows($pptQuery1) > 0)
            {
                $pptResult1 = $DRW->fetch_array($pptQuery1);
                $pppImageStatus1=$pptResult1['ppt_status'];
            }
            $tablename=' cscan_img_document';
            if($pppImageStatus1==1 || $pppImageStatus1==2){
                $tablename=' cscan_img_document_ppt';
            }
            /*############END CONVERT EMAIL IMAGE############*/
	//$query2 = "SELECT img_document_sort,img_document_filename,img_document_content_type,img_document_size_byte,UNIX_TIMESTAMP(img_document_createddate),document_id,img_document_path FROM cscan_img_document WHERE productID=$productID AND document_id=$document_id$where";
	echo $query2 = "SELECT img_document_sort,img_document_filename,img_document_content_type,img_document_size_byte,UNIX_TIMESTAMP(img_document_createddate),document_id,img_document_path,sample_img_status,bucket_name FROM {$tablename} WHERE productID=$productID AND document_id=$document_id$where"; 
        $query_result2 = $DRW->query($query2,$DRW_read);
	$data2 = $DRW->fetch_row($query_result2);
	$img_document_sort = (int)$data2[0];
	$img_document_filename = $data2[1];
	$img_document_content_type = $data2[2];
	$img_document_size_byte = $data2[3];
	$img_document_createddate = $data2[4];
        if(!empty($data2[5])){
            $document_id = $data2[5];
        }
	$img_document_path = $data2[6];
        $resample_img_status = $data2[7];
        $check_bucket_name = $data2[8];
    ######################## Start for Banner and mobile product ##################    
        
        if(empty($img_document_filename) || empty($img_document_path)){
            
            if($document_id==1){
                $query2 = "SELECT document_id FROM cscan_document WHERE productID=$productID AND document_id=2";
                $query_result2 = $DRW->query($query2,$DRW_read);
                $numrows=$DRW->num_rows($query_result2);
                if($numrows>0) {
                   $document_id=2;
                } 
            }
            
            $query = "SELECT mChannelID FROM cscan_product_detail WHERE productID=$productID";
            $query_result = $DRW->query($query,$DRW_read);
            if($DRW->num_rows($query_result)>0) {
                $data = $DRW->fetch_row($query_result);
                $mchanelId = $data[0];
                if($mchanelId==5){
                    $query2 = "SELECT document_filename,document_path,document_content_type,document_size_byte,UNIX_TIMESTAMP(document_createddate),document_id,bucket_name FROM cscan_document WHERE productID=$productID AND document_id=$document_id";
                    $query_result2 = $DRW->query($query2,$DRW_read);
                    if($DRW->num_rows($query_result2)>0) {
                        $data2 = $DRW->fetch_row($query_result2);
                        $img_document_sort = 1;
                        $img_document_filename = $data2[0];
                        $img_document_path = $data2[1];
                        $img_document_content_type = $data2[2];
                        $img_document_size_byte = $data2[3];
                        $img_document_createddate = $data2[4];
                        $document_id = $data2[5];
                        $check_bucket_name = $data2[6];
                        //$resample_img_status = $data2[7];
                    }
                }
            }
        }
    ######################## END for Banner and mobile product ################## 
        
        
       
        ######################## for the video section ##################
        // if(ENV == 'localhost' || ENV == 'demo.competiscan.com'){
            if($img_document_content_type=='video/mp4'){
                $query_video            =   "SELECT img_id,img_filename,img_content_type,img_size_byte,UNIX_TIMESTAMP(img_createddate),img_path,img_companyID FROM cscan_img WHERE productID=$productID";
                $query_result_video     =   $DRW->query($query_video,$DRW_read);
                $data_video             =   $DRW->fetch_row($query_result_video);
                $img_document_filename  =   $data_video['1'] ;
                $img_document_path      =   $data_video['5'] ;

             }
         
        //}
        ######################## end for the video section ##################
        
        
        
	$DRW->free_result($query_result2);
	//$src = dirname(__FILE__)."$img_document_path$img_document_filename";
        ############# for display data from s3 ############################
    //echo $img_document_path;die;
	if(strpos($img_document_path,'/')=='0'){
            if(!empty($check_bucket_name) AND $check_bucket_name!='' AND $check_bucket_name!='NULL'){
                $img_document_path  = substr($img_document_path,0); 
            }else{
                 $img_document_path  = substr($img_document_path,1);
            }
            
            
        }
    //echo "NO".$img_document_path;
	if($img_document_sort!=0){
            if(ENV == 'localhost' || ENV == 'demo.competiscan.com'){
                if($img_document_content_type!='video/mp4'){
                    $query_sample_img       =   "SELECT default_img_width,default_img_height,sample3_img_width,sample3_img_height FROM cscan_pdf_img_sample WHERE status=1 order by id DESC limit 1";
                    $result_sample_img      =   $DRW->query($query_sample_img,$DRW_read);
                    $data_sample_img        =   $DRW->fetch_row($result_sample_img);
                    $default_img_width      =   $data_sample_img['0'] ;
                    $default_img_height     =   $data_sample_img['1'] ;
                    $sample3_img_width      =   $data_sample_img['2'] ;
                    $sample3_img_height     =   $data_sample_img['3'] ;
                    if($resample_img_status==3){
                        $sample_imgwidth='?width='.$sample3_img_width;
                        if($sample3_img_height>0){
                             $sample_imgwidth .='&height='.$sample3_img_height;
                        }                     

                    }else{
                        $sample_imgwidth='?width='.$default_img_width;
                        if($default_img_height>0){
                             $sample_imgwidth .='&height='.$default_img_height;
                        }

                    }             

                }
                //$imagesource=$s3FileUrl.$img_document_path.$img_document_filename.$sample_imgwidth;
                $imagesource=$displays3URL.$img_document_path.$img_document_filename.$sample_imgwidth;
            }else{
                if(!empty($check_bucket_name) AND $check_bucket_name!='' AND $check_bucket_name!='NULL'){
                    $imagesource  = $displays3CSV2URL.$img_document_path.$img_document_filename;
                }else{ 
                    try{
                        $results = $s3->getObject([
                            'Bucket' => $bucket_name,
                            'Key'    => $img_document_path.$img_document_filename,
    
                        ]);  
                    //echo "<pre>";
                    ///echo $results['ContentType'];
    
                    } catch (Exception $e) {
                       // echo"error";
                       echo $e->getMessage() . PHP_EOL;
                       //var_dump($e); die;
                    }
                   $imagesource=$results['@metadata']['effectiveUri'];
                   if(!empty($imagesource)){
                       $imagesource=$displays3URL.$img_document_path.$img_document_filename; 
                       
                   }

                }
                
          }
           if(!empty($imagesource)){
            //echo $imagesource; 
            //echo $imagesource=$displays3URL.$img_document_path.$img_document_filename; die;
            header("Location: " . $imagesource); exit;
           }else{
            //echo "ELSE".$imagesource; die;
               /*@ob_end_clean();
                $altfile = dirname(__FILE__).'/images/competiscan_logo.jpg';
                makeCacheable(filemtime($altfile));
                header("Content-Type: image/jpeg");
                readfile($altfile);*/
           }
        
         ############# END display data from s3 ############################             
            /*
            
		if($img_document_filename!="" && is_file($src)) {
			if($full){
				$new_src = FULL_sample_img($productID,$document_id,$img_document_path,$img_document_filename);
				if(is_file($new_src)){
					$src = $new_src;
				}
			}
			@ob_end_clean();
			echo $src;exit;
			$dl = new HTTP_Download();
			$dl->setFile($results['@metadata']['effectiveUri']);
			//$dl->setLastModified($img_document_createddate);
			$dl->setContentType($img_document_content_type);
			$dl->setCacheControl('public');
			$dl->setCache(true);
			$dl->setContentDisposition(HTTP_DOWNLOAD_INLINE, $img_document_filename);
			$dl->send();
			
			
//                        makeCacheable(filemtime($src));
//				
//			header("Content-Disposition: inline; filename=\"$img_document_filename\"");
//			header("Content-Type: $img_document_content_type");
//			header("Content-Length: $img_document_size_byte");
//			header("Accept-Ranges: bytes");
//			readfile($src);
                         
			exit;
		}
             
             */
	}
}
@ob_end_clean();
$altfile = dirname(__FILE__).'/images/competiscan_logo.jpg';
makeCacheable(filemtime($altfile));
header("Content-Type: image/jpeg");
readfile($altfile);
?>