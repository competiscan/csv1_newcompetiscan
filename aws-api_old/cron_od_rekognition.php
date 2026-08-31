<?php require_once './config.php'; 
    //$sqlQuery="DELETE FROM cscan_digital_od_ads_text  WHERE NOT EXISTS (SELECT productID FROM cscan_product_detail where cscan_product_detail.productID=cscan_digital_od_ads_text.productID)";
    //$delete = $DRW->query($sqlQuery, $DRW_main);

    $sqlQuery="update cscan_digital_od_ads_text set conversion_status=1 WHERE (img_document_content_type='html' || img_document_content_type='image/gif') AND conversion_status=0 AND digital_text!='' AND digital_text IS NOT NULL";
    $updt = $DRW->query($sqlQuery, $DRW_main);

    $checkS = "SELECT productID,img_document_filename,img_document_path FROM cscan_digital_od_ads_text WHERE img_document_content_type!='html' AND img_document_content_type!='image/gif' AND conversion_status=0 ORDER BY `id` DESC limit 0,200";
    $checkS = $DRW->query($checkS, $DRW_read2);
    $countS = $DRW->num_rows($checkS);
    if($countS>0){
        while ($row_doc = $DRW->fetch_array($checkS)) {
            $productID=$row_doc['productID'];
            $img_document_filename=$row_doc['img_document_filename'];
            $img_document_path=$row_doc['img_document_path'];
            //$path='https://s3.amazonaws.com/'.$bucket_name_od.'/'.str_replace('/PDF/','',$img_document_path).$img_document_filename;
            $path=substr($img_document_path,1).$img_document_filename;
            $info = $s3->doesObjectExist($bucket_name,$path);
            if($info){
                try{
                    $resultText = $rekognitionClient->detectText([
                         'Image' => [
                             'S3Object' => [
                                 'Bucket' => $bucket_name,                         
                                 'Name' => $path,
                             ],
                         ],
                     ]);
                    $linsArr = [];
                    $lineText= '';            
                    if(!empty($resultText['TextDetections'])){
                        foreach ($resultText['TextDetections'] as $k=>$phrase) {
                            if($phrase['Type'] == 'LINE'){
                                $linsArr[$k] = $phrase['DetectedText'];
                            }else{
                                break;
                            }
                        }
                        $lineText= '';
                        if(!empty($linsArr)){
                            $lineText = implode(" ",$linsArr);
                            $lineText = addslashes($lineText);                    
                        }
                        $lineText=trim($lineText);
                        if(!empty($lineText)){
                            $sql_updt_ads = "UPDATE cscan_digital_od_ads_text set conversion_status=1,digital_text='".$lineText."' WHERE productId='".$productID."'";
                            $DRW->query($sql_updt_ads, $DRW_main);
                        }else{
                            $sql_updt_ads = "UPDATE cscan_digital_od_ads_text set conversion_status=1 WHERE digital_text!='' AND digital_text IS NOT NULL AND productId='".$productID."'";
                            $DRW->query($sql_updt_ads, $DRW_main);
                        }

                       // sleep(3);
                    } 
                }catch (Exception $e) {
                    $sql_updt_ads = "UPDATE cscan_digital_od_ads_text set conversion_status=5 WHERE productId='".$productID."'";
                    $DRW->query($sql_updt_ads, $DRW_main);
                   //echo $e->getMessage();
                }
    
            }
        }       
    }
    echo '<br>';
    echo 'Rekognition process completed'; die; 