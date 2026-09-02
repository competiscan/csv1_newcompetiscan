<?php require_once './config.php'; 
       
    //For uploading files on S3 
    $sqlQuery="DELETE FROM cscan_digital_video_ads_text  WHERE NOT EXISTS (SELECT productID FROM cscan_product_detail where cscan_product_detail.productID=cscan_digital_video_ads_text.productID)";
    $delete = $DRW->query($sqlQuery, $DRW_main);
    $checkS = "SELECT productID,img_document_filename,img_document_path
        FROM cscan_digital_video_ads_text where conversion_status=0 limit 0,20";
    $checkS = $DRW->query($checkS, $DRW_read2);
    $countS = $DRW->num_rows($checkS);
    if($countS>0){
        while ($row_doc = $DRW->fetch_array($checkS)) {
               $productID=$row_doc['productID'];
               $img_document_filename=$row_doc['img_document_filename'];
               $img_document_path=$row_doc['img_document_path'];
               $path=$s3URL.$bucket_name.$img_document_path.$img_document_filename;            

               $result = $transcribe->startTranscriptionJob([
                    'LanguageCode' => 'en-US', // REQUIRED
                    'Media' => [ // REQUIRED
                        'MediaFileUri' => $path,
                    ],
                    'MediaFormat' => 'mp4', // REQUIRED
                    //'MediaSampleRateHertz' => 44100,
                    //'OutputBucketName' => 'nmgtestout',
                    'Settings' => [
                        'ChannelIdentification' => false ,
                        //'MaxSpeakerLabels' => 5,
                        'ShowSpeakerLabels' => false,
                        //'VocabularyName' => 'this',
                    ],
                    'TranscriptionJobName' => $img_document_filename, // REQUIRED
                ]); 
                //echo '<pre>';
                //print_r($result);                
                //die;
                if($result['@metadata']['statusCode']==200 && (($result['TranscriptionJob']['TranscriptionJobStatus']=='IN_PROGRESS') || ($result['TranscriptionJob']['TranscriptionJobStatus']=='COMPLETE'))){
                   $sql_updt_ads = "UPDATE cscan_digital_video_ads_text set conversion_status=1,transcription_job_name='".$img_document_filename."' WHERE productId='".$productID."'";
                   $DRW->query($sql_updt_ads, $DRW_main);                 
                }
                sleep(3);
        }       
    }
    echo '<br>';
    echo 'Transcribe input process completed'; die; 