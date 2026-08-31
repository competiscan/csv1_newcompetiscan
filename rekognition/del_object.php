<?php
require_once './config.php';

if(!empty($bucketName) || !empty($objectName)) 
{
    if(!empty($bucketName) && !empty($objectName)){

        if(!in_array($bucketName, $doNotDelete)){
            //Delete all Objects when versioning is not enabled
            try {
                try {
                    $result = $s3Client->deleteObject([
                        'Bucket' => $bucketName,
                        'Key' => $objectName,
                    ]);
                } catch (S3Exception $e) {
                    echo $e->getMessage() . "\n";
                }
                header('Location: objectlist.php?bnm='.$bucketName);
            } catch (S3Exception $e) {
                echo $e->getMessage() . "\n";
            }
        }else{
            $msg =  'Can not delete object of restricted bucket!';
            //sleep(5);
            header('Location: objectlist.php?bnm='.$bucketName.'&msg='.$msg);
        }        
    }else{
        $msg =  'Invalid bucket name or object name!';
        header('Location: objectlist.php?bnm='.$bucketName.'&msg='.$msg);
    }
}
?>