<?php 
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);

	ini_set('upload_max_filesize', '200M');
	ini_set('post_max_size', '200M');	
	
   require 'aws-vendor/autoload.php';   
   use Aws\S3\S3Client;	
   use Aws\S3\MultipartUploader;
   use Aws\Exception\MultipartUploadException;
   $credentials = new Aws\Credentials\Credentials('AKIAIKJCVVD3YLAMLERQ', 'SgZJKTMj0Otse6/akg1wIY8Pdt05PX3v/V70aild');

  /* 
   $filename = 'https://s3.amazonaws.com/nmgtest2/31271030.pdf';
   file_put_contents(
    'testimg/' . basename($filename), // where to save file
    file_get_contents($filename)
);
*/
//die;

   $s3 = new Aws\S3\S3Client([
    'version'     => 'latest',
    'region'      => 'us-east-1',
    'credentials' => $credentials
]);
// Perform an operation to see the debug output

// List of all buckets
//$buckets=$s3->listBuckets();
//echo '<pre>';
//print_r($buckets);
//die;
//Create New bucket 
$bucket_name = 'nmgtest2';
/*
$result = $s3->createBucket([
	'Bucket' => $bucket_name,
]);

echo '<pre>';
print_r($result);
die;
*/
/*
$result=$s3->putObject(array( 
   'Bucket' => $bucket_name,
   'Key'    => "test/",
   'Body'   => "",
   'ACL'    => 'public-read'
  ));
  echo '<pre>';
print_r($result);
die;
*/
// Upload File from local 
/*
$result=$s3->putObject(array( 
   'Bucket' => $bucket_name,
   'Key'    => "test3/",
   'Body'   => "",
   'ACL'    => 'public-read'
  ));
*/
/*
$uploader = new MultipartUploader($s3, '31271030.pdf', [
    'bucket' => 'nmgtest2',
    'key'    => '31271030.pdf',
    'ACL' => 'public-read'
]);

try {
    $result = $uploader->upload();
    echo "Upload complete: {$result['ObjectURL']}\n";
    echo 'success';
} catch (MultipartUploadException $e) {
    echo $e->getMessage() . "\n";
    echo 'error';
}
exit;
*/
// Start get file from S3
/*
$result = $s3->getObject([
    'Bucket' => 'testfortranscribenmg2',
    'Key' => 'test2.mp4'
]);
echo '<pre>';
print_r($result);
die;
*/
// Get transcribed file which are already transcribe
$transcribe = new Aws\TranscribeService\TranscribeServiceClient([
'region'  => 'us-east-1',
'version' => '2017-10-26',
'credentials' => $credentials
]);

// Get all transcription job list
/*
$result = $transcribe->listTranscriptionJobs([]);
echo '<pre>';
print_r($result); die;

*/
/*

$transcription_name='fourthnmg';
$result = $transcribe->getTranscriptionJob([
    'TranscriptionJobName' => $transcription_name, // REQUIRED
]);


$transcription = $result['TranscriptionJob']['Transcript']['TranscriptFileUri'];

$transcription_download = file_get_contents($transcription);
$transcribe_final = json_decode($transcription_download, true);
echo '<pre>';
print_r($transcribe_final);
$trans = $transcribe_final['results']['transcripts'][0]['transcript'];
echo $trans;
*/
// Do transcribe for file stored on S3
/*
$result = $transcribe->startTranscriptionJob([
    'LanguageCode' => 'en-US', // REQUIRED
    'Media' => [ // REQUIRED
        'MediaFileUri' => 'https://testfortranscribenmg2.s3.amazonaws.com/test2.mp4',
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
    'TranscriptionJobName' => 'fourthnmg', // REQUIRED
]); 
print_r($result);
*/

?>
<?php //echo $_SERVER['DOCUMENT_ROOT']; 
 
//echo $destination_path = getcwd().DIRECTORY_SEPARATOR; die;

 if($_FILES['userfile']['tmp_name']!=''){
     $temp_file_location=$_FILES['userfile']['tmp_name'];
     $file_name=$_FILES['userfile']['name'];
    /* Upload file in local directory */ 
     //$uploaddir = '/var/www/html/competiscan.com/aws-api/testimg/';
     $uploaddir='/srv/httpd/competiscan.com/html/aws-api/testimg/';
     $uploaddir='/srv/httpd/competiscan.com/html/s3test/2018/12/';
    
    echo $uploadfile = $_SERVER['DOCUMENT_ROOT'].'/s3test/'.basename($_FILES['userfile']['name']);
        
    if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
        echo "File is valid, and was successfully uploaded.\n";
    } else {
         echo "Upload failed";
    }
    die;
    /* End Upload file in local directory */ 
      
/*	$uploader = new MultipartUploader($s3, '31271030.pdf', [
                    'bucket' => 'nmgtest2',
                    'key'    => 'abc.pdf',
                    'ACL' => 'public-read'
                    ]);
        $result = $uploader->upload();
 */
 $result = $s3->putObject([
            'Bucket' => 'nmgtest2',
            'Key'    => $file_name,
            'SourceFile' => $temp_file_location,
            'ACL'    => 'public-read',
            ]);
 
 
 
 
    echo '<pre>';
    print_r($result); die;
    if($result )
    {
        $message = "S3 Upload Successful.";
        $s3file='http://'.$bucket_name.'.s3.amazonaws.com/'.$file_name;
        echo "<img src='$s3file'/>";
        echo 'S3 File URL:'.$s3file;
    }
    else{
        $message = "S3 Upload Fail.";
    }
//print_r($result);
	 
	 
	 
	/* 
	 
	 $uploader = new MultipartUploader($s3, '31271030.pdf', [
    'bucket' => 'nmgtest2',
    'key'    => '31271030.pdf',
    'ACL' => 'public-read'
	]);
	
	try {
		$result = $uploader->upload();
		echo "Upload complete: {$result['ObjectURL']}\n";
		echo 'success';
	} catch (MultipartUploadException $e) {
		echo $e->getMessage() . "\n";
		echo 'error';
	}
	 
     */
}
 

    ?>

<html>
	<header>
	</header>
<body>
	<form name="frmupload" enctype="multipart/form-data" action="" method="POST">
     <input name="userfile" type="file" />     
    <input type="submit" value="Send File" />
</form>
</body>
</html>

