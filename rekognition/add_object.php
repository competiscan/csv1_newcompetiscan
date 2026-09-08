<?php
require_once './config.php';

if(!empty($_POST['url']) || !empty($_FILES['image'])) 
{//pr($_POST);die;
    $bucketName = (!empty($_POST['bucket']))?trim($_POST['bucket']):'nmgrekognitiontest';
    $url = (!empty($_POST['url']))?trim($_POST['url']):'';
    $file = (!empty($_FILES['image']))?$_FILES['image']:'';

    if(!empty($bucketName) && (!empty($url) || !empty($file))){        
        try {
            // Send a PutObject request and get the result object.
            if(!empty($url)){
                $filePath = $url;
                //$filePath = 'http://collect.sbkcenter.com/creatives/6ec61d02-33bc-42a6-88a7-6ddb579584a6.png';
                $keyName = basename($filePath);
                // So you need to move the file on $filePath to a temporary place.
                // The solution being used: http://stackoverflow.com/questions/21004691/downloading-a-file-and-saving-it-locally-with-php
                if (!file_exists('/tmp/tmpfile')) {
                    mkdir('/tmp/tmpfile');
                }
                
                // Create temp file
                $tempFilePath = '/tmp/tmpfile/' . basename($filePath);
                $tempFile = fopen($tempFilePath, "w") or die("Error: Unable to open file.");
                $fileContents = file_get_contents($filePath);
                $tempFile = file_put_contents($tempFilePath, $fileContents);
            }else{
                $tempFilePath = $file['tmp_name'];
                $keyName = $file['name'];
            }
            // Put on S3
            $extArr = explode(".", $keyName);
            $ext = strtolower(end($extArr));
            $mimeType = 'application/pdf';
            if($ext == 'png'){
                $mimeType = 'image/png';
            }elseif($ext == 'jpeg' || $ext == 'jpg'){
                $mimeType = 'image/jpeg';
            }//echo $mimeType;die;
            // Put on S3
            $result = $s3Client->putObject(
                array(
                    'Bucket'=>$bucketName,
                    'Key' =>  $keyName,
                    'SourceFile' => $tempFilePath,
                    'Body' => fopen($tempFilePath, 'rb'),
                    'StorageClass' => 'REDUCED_REDUNDANCY',
                    'ContentType'	=> $mimeType,
                    'ACL' => 'public-read',
                    'Metadata'      => array(
                        'string'        => 'string'
                    )
                )
            );
            //remove the file from local folder
            unlink($tempFilePath);
            //pr($result);die;
            #####################
            $resultText = $rekognitionClient->detectText([
                'Image' => [
                    'S3Object' => [
                        'Bucket' => $bucketName,
                        'Name' => $keyName,
                    ],
                ],
            ]);
            $linsArr = [];
            $lineText= '';
            //pr($resultText);
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
                $ObjectURL = addslashes($result['ObjectURL']);
                $sql = "insert into digital_image (product_id, imageUrl, imageText) values (1,'".$ObjectURL."','".$lineText."')";
                /* $conn->query($sql);
                $conn->close(); */
                $DRW->query($sql, $DRW_main);
            }
            ######################
            
            header('Location: objectlist.php?bnm='.$bucketName.'&msg='.$msg);
        } catch (S3Exception $e) {
            echo $e->getMessage();
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        //pr($result);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>AWS | Add Bucket</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <body>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
            
            Image Url: <input type="text" name="url"><br>
            or<br>
            Select image/file to upload:
            <input type="file" name="image" id="fileToUpload"><br>
            <select name="bucket">
                <?php
                $bucketList = $s3Client->listBuckets();
                if(!empty($bucketList['Buckets'])){
                    echo '<option value="">-- select --</option>';
                    foreach($bucketList['Buckets'] as $bucket){
                        if(!in_array($bucket['Name'], $doNotDelete)){
                            echo '<option value="'.$bucket['Name'].'">'.$bucket['Name'].'</option>';
                        }
                    }
                }
                ?>
            </select>
            <br>
            <br>
            <input type="submit" value="Upload Image" name="submit">
        </form>
    </body>
</html>

<!-- <html>
    <head><meta charset="UTF-8"></head>
    <body>
        <h1>S3 upload example</h1>
<?php
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['userfile']) && is_uploaded_file($_FILES['userfile']['tmp_name'])) {
    // FIXME: add more validation, e.g. using ext/fileinfo
    try {
        // FIXME: do not use 'name' for upload (that's the original filename from the user's computer)
        $upload = $s3->upload($bucket, $_FILES['userfile']['name'], fopen($_FILES['userfile']['tmp_name'], 'rb'), 'public-read');
?>
        <p>Upload <a href="<?=htmlspecialchars($upload->get('ObjectURL'))?>">successful</a> :)</p>
<?php } catch(Exception $e) {pr($e);die; ?>
        <p>Upload error :(</p>
<?php } } ?>
        <h2>Upload a file</h2>
        <form enctype="multipart/form-data" action="<?=$_SERVER['PHP_SELF']?>" method="POST">
            <input name="userfile" type="file"><input type="submit" value="Upload">
        </form>
    </body>
</html> -->
