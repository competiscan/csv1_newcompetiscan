<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set("memory_limit", "-1");
set_time_limit(0);
###########################################
function pr($str){
    echo '<pre>';print_r($str);
}
###########################################
require '../aws-api/aws-vendor/autoload.php';
require '../includes/dbcon.php';
require '../includes/functions.php';

use Aws\S3\S3Client;
use Aws\Exception\AwsException;
use Aws\S3\Exception\S3Exception;
use Aws\Rekognition\RekognitionClient;


#######################################################
/* $servername = "localhost";
$username = "root";
$password = "root";
$dbname = "rekognition"; */

// Create connection
/* $conn = new mysqli($servername, $username, $password, $dbname); */

// Check connection
/* if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} */

#################################################
$IAM_KEY = 'AKIAIKJCVVD3YLAMLERQ';
$IAM_SECRET = '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';
$bucketName = 'nmg-image-rekognition';
$region = 'us-east-1';

if(!empty($_GET['bnm'])){
    $bucketName = $_GET['bnm'];
}
$objectName = '';
if(!empty($_GET['onm'])){
    $objectName = $_GET['onm'];
}
    
// Instantiate an Amazon S3 client.
$credentials = new Aws\Credentials\Credentials($IAM_KEY, $IAM_SECRET);

$s3 = Aws\S3\S3Client::factory([
    'version'     => 'latest',
    'region'      => $region,
    'credentials' => $credentials,
    'scheme'  => 'http'
]);

$s3Client = new Aws\S3\S3Client([
    'version'     => 'latest',
    'region'      => $region,
    'credentials' => $credentials,
    'scheme'  => 'http'
]);
$rekognitionClient = new Aws\Rekognition\RekognitionClient([
    'version'     => 'latest',
    'region'      => $region,
    'credentials' => $credentials
]);
$doNotDelete = [
    'cf-templates-1j1vutrvxv6bx-us-east-1',
    'cf-templates-competiscan-dev',
    'comp-testbucket',
    'db63d47cd1184ead96034afbfc12dee3-logs',
    'dev-competiscan-codedeploy',
    'dev-competiscan-static-content',
    'dev-competiscan-web-elb-logs',
    'ebs-snapper-399550422280',
    'csbucket007'
];