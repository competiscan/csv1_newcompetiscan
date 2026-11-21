<?php require_once './config.php'; 
   
   //$checkD= "SELECT imd.productID,imd.img_document_filename ,imd.img_document_content_type,imd.img_document_path,imd.img_document_createddate,imd.img_document_createdby,imd.img_document_size_byte FROM cscan_img_document imd Inner JOIN cscan_product_detail pd ON(pd.productID=imd.productID) WHERE pd.mChannelID=5 AND pd.productStatus=1 AND pd.is_digital=1 AND ((imd.img_document_content_type like '%jpeg%') || (imd.img_document_content_type like '%jpg%') || (imd.img_document_content_type like '%png%')) limit 0,50";
   
    $checkD=" SELECT imd.productID,imd.img_document_filename ,imd.img_document_content_type,imd.img_document_path,imd.img_document_createddate,imd.img_document_createdby,imd.img_document_size_byte 
                FROM cscan_img_document imd Inner JOIN cscan_product_detail pd ON(pd.productID=imd.productID) 
                left join cscan_digital_od_ads_text as dt on (dt.productID=pd.productID)
                WHERE pd.mChannelID=5 
                AND pd.productStatus=1 AND pd.is_digital=1";
    //AND dt.productId is null
    //            AND ((imd.img_document_content_type = 'image/jpeg') || (imd.img_document_content_type = 'image/jpg') || (imd.img_document_content_type= 'image/png')) 
      //          limit 0,50 
  

   $checkD = $DRW->query($checkD, $DRW_read2);
   $dcount = $DRW->num_rows($checkD);
    if($dcount>0){
        while ($row_product = $DRW->fetch_array($checkD)) {
           $productID=$row_product['productID'];
           $check = "SELECT productID FROM cscan_digital_od_ads_text WHERE productId='".$productID."'";
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
           $sql_insert_ads = "INSERT INTO cscan_digital_od_ads_text (productID,img_document_filename,img_document_content_type,img_document_path,img_document_createddate,img_document_createdby,img_document_size_byte) values('".$productID."','".$img_document_filename."','".$img_document_content_type."','".$img_document_path."','".$img_document_createddate."','".$img_document_createdby."','".$img_document_size_byte."')";
           $DRW->query($sql_insert_ads, $DRW_main);
        }
    }
    
    
    
   /* 
   $checkD=" SELECT productID,productHeadline FROM cscan_product_detail WHERE mChannelID=5 AND productStatus=1 AND is_digital!=1";
    
   $checkD = $DRW->query($checkD, $DRW_read2);
   $dcount = $DRW->num_rows($checkD);
    if($dcount>0){
        while ($row_product = $DRW->fetch_array($checkD)) {
           $productID=$row_product['productID'];
           $productHeadline=$row_product['productHeadline'];
           $check = "SELECT productID FROM cscan_digital_od_ads_text WHERE productId='".$productID."'";
           $check = $DRW->query($check, $DRW_read2);
           $count = $DRW->num_rows($check);
           if($count>0){
               continue;
           }
    
            $query2 = "SELECT document_filename,document_path,document_content_type,document_size_byte,UNIX_TIMESTAMP(document_createddate),document_id FROM cscan_document WHERE productID=$productID AND document_id=2";
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
                $img_document_createdby=$AUTH_DATA['userID'];
                if(!empty($img_document_filename) || !empty($img_document_path)){ 
                    $sql_insert_ads = "INSERT INTO cscan_digital_od_ads_text (productID,img_document_filename,img_document_content_type,img_document_path,img_document_createddate,img_document_createdby,img_document_size_byte,digital_text) values('".$productID."','".$img_document_filename."','".$img_document_content_type."','".$img_document_path."','".$img_document_createddate."','".$img_document_createdby."','".$img_document_size_byte."','".$DRW->real_escape_string($productHeadline)."')";
                    $DRW->query($sql_insert_ads, $DRW_main);                    
                }
            }
        }
    }
    */
    
    
    echo 'Mooving on cscan_digital_od_ads_text table process have completed';
    echo '<br><br>';
    