<?php
require_once './config.php';
$checkS = "SELECT productID,transcription_job_name FROM cscan_digital_video_ads_text WHERE  conversion_status=1 order by id desc limit 0,20";
$checkS = $DRW->query($checkS, $DRW_read2);
$countS = $DRW->num_rows($checkS);
if ($countS > 0) {
    while ($row_doc = $DRW->fetch_array($checkS)) {
        $productID = $row_doc['productID'];
        $transcription_job_name = $row_doc['transcription_job_name'];
        //$transcription_job_name='firstnmg';
        try {
            if (!empty($transcription_job_name)) {
                $result = $transcribe->getTranscriptionJob([
                    'TranscriptionJobName' => $transcription_job_name, // REQUIRED
                ]);
                //echo '<pre>';
                //print_r($result);
                // die;

                if ($result['TranscriptionJob']['TranscriptionJobStatus'] == 'COMPLETED') {
                    $transcription = $result['TranscriptionJob']['Transcript']['TranscriptFileUri'];
                    $transcription_download = file_get_contents($transcription);
                    $transcribe_final = json_decode($transcription_download, true);

                    $trans = trim($transcribe_final['results']['transcripts'][0]['transcript']);
                    if (!empty($trans)) {
                        $sql_updt_ads = "UPDATE cscan_digital_video_ads_text set conversion_status=2,digital_text='" . $DRW->real_escape_string($trans) . "' WHERE productId='" . $productID . "'";
                        $DRW->query($sql_updt_ads, $DRW_main);
                    }
                } else if ($result['TranscriptionJob']['TranscriptionJobStatus'] == 'FAILED') {
                    $sql_updt_ads = "UPDATE cscan_digital_video_ads_text set conversion_status=3 WHERE productId='" . $productID . "'";
                    $DRW->query($sql_updt_ads, $DRW_main);
                }
            }
        } catch (Exception $e) {
            $sql_updt_ads = "UPDATE cscan_digital_video_ads_text set conversion_status=3 WHERE productId='" . $productID . "'";
            $DRW->query($sql_updt_ads, $DRW_main);

            echo 'There are some errorfound:' . $e;
        }
        // sleep(3);
    }
}
echo '<br>';
echo 'Transcribe output process completed';
die;
