<?php
require_once './config.php';
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>AWS | Object Detail</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <body>
        <?php
        $ext = @end(explode(".",$objectName));
        $resultInfo = $s3Client->getObject([
            'Bucket' => $bucketName,
            'Key' => $objectName
        ]);
        if(in_array(strtolower($ext), ['png','jpeg', 'jpg'])){
            $result = $rekognitionClient->detectText([
                'Image' => [
                    'S3Object' => [
                        'Bucket' => $bucketName,
                        'Name' => $objectName,
                    ],
                ],
            ]);          
            echo '<h1><a href="'.$resultInfo['@metadata']['effectiveUri'].'" target="_blank">'.$objectName.'</a></h1>';
            if(!empty($result['TextDetections'])){
                echo "<table border=1 cellspacing=0>
                        <tr>
                            <th>S.N.</th>
                            <th>DetectedText</th>
                            <th>Type</th>
                            <th>Confidence</th>
                        </tr>";
                $i = 1;
                foreach ($result['TextDetections'] as $phrase) {
                    echo '<tr>
                            <td>'.$i.'</td>
                            <td>"'.$phrase['DetectedText'].'"</td>
                            <td>"'.$phrase['Type'].'"</td>
                            <td>"'.round($phrase['Confidence']).'"%</td>
                        </tr>';
                    $i++;
                }
                echo "</table>";
            }
        }else{
            echo '<h1>'.$objectName.'</h1>';
            echo $resultInfo['@metadata']['effectiveUri'];
        echo '<iframe src="'.$resultInfo['@metadata']['effectiveUri'].'" width="100%" height="100%"></iframe>';
            echo '<embed src="'.$resultInfo['@metadata']['effectiveUri'].'" width="100%" height="100%"></embed>';
                echo '<object src="'.$resultInfo['@metadata']['effectiveUri'].'" width="100%" height="100%"></object>';
        }        
        ?>
    </body>
</html>