<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set("memory_limit", "-1");
set_time_limit(0);

require '../aws-api/aws-vendor/autoload.php';
require '../includes/dbcon.php';

use Aws\S3\S3Client;	
use Aws\S3\MultipartUploader;
use Aws\Exception\MultipartUploadException;
use Aws\Exception\AwsException;
use Aws\S3\Exception\S3Exception;
use Aws\Rekognition\RekognitionClient;

#################################################
$credentials_tr = new Aws\Credentials\Credentials($IAM_KEY_TR, $IAM_SECRET_TR);
  
$transcribe = new Aws\TranscribeService\TranscribeServiceClient([
    'region'  => 'us-east-1',
    'version' => '2017-10-26',
    //'credentials' => $credentials_tr
    ]);

$rekognitionClient = new Aws\Rekognition\RekognitionClient([
    'version'     => 'latest',
    'region'      => 'us-east-1',
    //'credentials' => $credentials_tr
]);
