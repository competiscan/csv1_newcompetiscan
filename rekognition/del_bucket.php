<?php
require_once 'config.php';

if(!empty($bucketName)) 
{
    if(!empty($bucketName)){        

        if(!in_array($bucketName, $doNotDelete)){
            //Delete all Objects when versioning is not enabled
            try {
                $objects = $s3Client->getIterator('ListObjects', ([
                    'Bucket' => $bucketName
                ]));
                echo "Keys retrieved!\n";
                foreach ($objects as $object) {
                    echo $object['Key'] . "\n";
                    $result = $s3Client->deleteObject([
                        'Bucket' => $bucketName,
                        'Key' => $object['Key'],
                    ]);
                }
                $result = $s3Client->deleteBucket([
                    'Bucket' => $bucketName,
                ]);
                header('Location: index.php');
            } catch (S3Exception $e) {
                echo $e->getMessage() . "\n";
            }
            //Delete bucket and all versioned objects inside bucket when versioning is enabled.
            /* try {
                $versions = $s3Client->listObjectVersions([
                    'Bucket' => $bucketName
                ])->getPath('Versions');
                echo "Keys retrieved!\n";
                foreach ($versions as $version) {
                    echo $version['Key'] . "\n";
                    echo $version['VersionId'] . "\n";
                    $result = $s3Client->deleteObject([
                        'Bucket' => $bucket,
                        'Key' => $version['Key'],
                        'VersionId' => $version['VersionId']
                    ]);
                }
                $result = $s3Client->deleteBucket([
                    'Bucket' => $bucketName,
                ]);
            } catch (S3Exception $e) {
                echo $e->getMessage() . "\n";
            } */
        }else{
            $msg =  'Can not delete buckets in use! please remove temporay buckets only.';
            //sleep(5);
            header('Location: index.php?msg='.$msg);
        }        
    }
}
?>