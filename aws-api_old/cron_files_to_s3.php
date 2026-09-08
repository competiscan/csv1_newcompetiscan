<?php require_once './config.php'; 

   $checkD = "SELECT imd.productID,imd.img_document_filename ,imd.img_document_content_type,imd.img_document_path,imd.img_document_createddate,imd.img_document_createdby,imd.img_document_size_byte FROM cscan_img_document imd Inner JOIN cscan_product_detail pd ON(pd.productID=imd.productID) WHERE pd.mChannelID=10 AND pd.productStatus=1 AND imd.img_document_content_type like '%mp4%'";
   $checkD = $DRW->query($checkD, $DRW_read2);
   $dcount = $DRW->num_rows($checkD);
    if($dcount>0){
        while ($row_product = $DRW->fetch_array($checkD)) {
           $productID=$row_product['productID'];
           $check = "SELECT productID FROM cscan_digital_video_ads_text WHERE productId='".$productID."'";
           $check = $DRW->query($check, $DRW_read2);
           $count = $DRW->num_rows($check);
           if($count>0){
               continue;
           }
           $img_document_filename=$row_product['img_document_filename'];
           $img_document_content_type=$row_product['img_document_content_type'];
           $img_document_path=$row_product['img_document_path'];
           $img_document_createddate=$row_product['img_document_createddate'];
           $img_document_createdby=$row_product['img_document_createdby'];
           $img_document_size_byte=$row_product['img_document_size_byte'];
           $sql_insert_ads = "INSERT INTO cscan_digital_video_ads_text (productID,img_document_filename,img_document_content_type,img_document_path,img_document_createddate,img_document_createdby,img_document_size_byte) values('".$productID."','".$img_document_filename."','".$img_document_content_type."','".$img_document_path."','".$img_document_createddate."','".$img_document_createdby."','".$img_document_size_byte."')";
           $DRW->query($sql_insert_ads, $DRW_main);
        }
    }
    echo 'Mooving on cscan_digital_ads_text table process have completed';
    echo '<br><br>';
    
    //For uploading files on S3 
/*
    $checkS = "SELECT productID,img_document_filename,img_document_path FROM cscan_digital_video_ads_text WHERE is_uploaded_s3=0 limit 0,10";
    $checkS = $DRW->query($checkS, $DRW_read2);
    $countS = $DRW->num_rows($checkS);
    if($countS>0){
        while ($row_doc = $DRW->fetch_array($checkS)) {
               $productID=$row_doc['productID'];
               $img_document_filename=$row_doc['img_document_filename'];
               $img_document_path=$row_doc['img_document_path'];
               $path=str_replace('/PDF/','',$img_document_path);
               $result = $s3->putObject([
                    'Bucket' => $bucket_name,
                    'Key'    => $path.$img_document_filename,
                    'SourceFile' => '..'.$img_document_path.$img_document_filename,
                    'ACL'    => 'public-read',
                ]);               
            if($result['@metadata']['statusCode']==200 && $result['ObjectURL']!=''){
                $sql_updt_ads = "UPDATE cscan_digital_video_ads_text set is_uploaded_s3=1 WHERE productId='".$productID."'";
                $DRW->query($sql_updt_ads, $DRW_main);                 
            }
            sleep(3);
        }       
    }
    
   echo 'Uploading process on S3 have completed'; die; 
   */