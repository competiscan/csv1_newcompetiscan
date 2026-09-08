<?php ob_start(); $ALLOW_GROUPS = array(46);
ignore_user_abort(true);
set_time_limit(0); // disable the time limit for this script
require_once("../auth_auth.php");
require_once '../includes/functions.php';
if(isset($_REQUEST['id']) && $_REQUEST['id']!=''){
$id=$_REQUEST['id'];
    $sqldwn = "select fileName from cscan_file_upload where fileID='".$id."' order by fileID DESC";                 
    $resultdwn = $DRW->query($sqldwn,$DRW_read);
while($row = $DRW->fetch_row($resultdwn)) {
$filename=$row[0];
}

/* ################## Changes for s3 bucket ################## */

try {
    // Get the object.
    $result = $s3->getObject([
        'Bucket' => $bucket_name,
        'Key'    => 'fileuploads/'.$filename
    ]);

    
    header('Content-Description: File Transfer');
    //this assumes content type is set when uploading the file.
    //header('Content-Type: ' . $result->ContentType);
    header("Content-Type: {$result['ContentType']}");
    header('Content-Disposition: attachment; filename=' . $filename);
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    
    // Display the object in the browser.
   // header("Content-Type: {$result['ContentType']}");
    echo $result['Body'];
} catch (S3Exception $e) {
    echo $e->getMessage() . PHP_EOL;
}
exit;

/* ################## End Changes for s3 bucket ################## */

 //echo  $filename; die;
$path = $_SERVER['DOCUMENT_ROOT'].'/fileuploads/'; 
 $fullPath=$path.$filename; 
//echo output_file($path, $filename, '');
// $filename='601321540402729223.jpeg';
 $fullPath=$displays3URL.'fileuploads/'.$filename;
 $result = $s3->getObject([
        'Bucket' => $bucket_name,
        'Key'    => 'fileuploads/'.$filename
    ]);
    
     $fsize=$result['@metadata']['headers']['content-length'];
   

//if ($fd = is_readable($fullPath)) {
    
    $fsize = filesize($fullPath);
    $path_parts = pathinfo($fullPath);
    $ext = strtolower($path_parts["extension"]);
    
    switch ($ext) {
        case "pdf":
        header("Content-type: application/pdf");
      header("Content-Disposition: attachment; filename=\"".$path_parts["basename"]."\""); // use 'attachment' to force a file download
        break;
    case "csv":
        header("Content-type: application/csv");
      header("Content-Disposition: attachment; filename=\"".$path_parts["basename"]."\""); // use 'attachment' to force a file download
        break;
    case "doc":
        header("Content-type: application/msword");
      header("Content-Disposition: attachment; filename=\"".$path_parts["basename"]."\""); // use 'attachment' to force a file download
        break;
    case "xls":
        header("Content-type: application/vnd.ms-exce");
      header("Content-Disposition: attachment; filename=\"".$path_parts["basename"]."\""); // use 'attachment' to force a file download
        break;
    case "gif":
        header("Content-type: image/gif");
      header("Content-Disposition: attachment; filename=\"".$path_parts["basename"]."\""); // use 'attachment' to force a file download
        break;
    case "png":
        header("Content-type: image/png");
      header("Content-Disposition: attachment; filename=\"".$path_parts["basename"]."\""); // use 'attachment' to force a file download
        break;
     case "jpeg":
        header("Content-type: image/jpeg");
      header("Content-Disposition: attachment; filename=\"".$path_parts["basename"]."\""); // use 'attachment' to force a file download
        break;
     case "jpg":
        header("Content-type: image/jpeg");
      header("Content-Disposition: attachment; filename=\"".$path_parts["basename"]."\""); // use 'attachment' to force a file download
        break;
    case "txt":
        header("Content-type: text/plain");
      header("Content-Disposition: attachment; filename=\"".$path_parts["basename"]."\""); // use 'attachment' to force a file download
        break;
case "mp4":
        header('Content-Type: video/mp4');
      header("Content-Disposition: attachment; filename=\"".$path_parts["basename"]."\""); // use 'attachment' to force a file download
        break;
 case "mp3":
       header('Content-Type: audio/mpeg');
      header("Content-Disposition: attachment; filename=\"".$path_parts["basename"]."\""); // use 'attachment' to force a file download
        break;
case "ppt":
       header('Content-Type: application/vnd.ms-powerpoint');
      header("Content-Disposition: attachment; filename=\"".$path_parts["basename"]."\""); // use 'attachment' to force a file download
        break;
case "pptx":
       header('Content-Type: application/vnd.openxmlformats-officedocument.presentationml.presentation');
      header("Content-Disposition: attachment; filename=\"".$path_parts["basename"]."\""); // use 'attachment' to force a file download
        break;
case "docx":
       header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
      header("Content-Disposition: attachment; filename=\"".$path_parts["basename"]."\""); // use 'attachment' to force a file download
        break;

        default;
        header("Content-type: application/octet-stream");
        header("Content-Disposition: filename=\"".$path_parts["basename"]."\"");
        break;
    }
    header("Content-length: $fsize");
    header("Cache-control: private"); //use this to open files directly
   ob_clean(); #THIS!
    flush();
    readfile($fullPath);
    exit;
    
//}
//fclose ($fd);
exit;


}
 ?>


<?php
function output_file($Source_File, $Download_Name, $mime_type='')
{
 /*
$Source_File = path to a file to output
$Download_Name = filename that the browser will see 
$mime_type = MIME type of the file (Optional)
*/
 if(!is_readable($Source_File.$Download_Name)) die('File not found or inaccessible!');

  $size = filesize($Source_File);
 
 $Download_Name = rawurldecode($Download_Name);

 /* Figure out the MIME type (if not specified) */
 $known_mime_types=array(
    "pdf" => "application/pdf",
    "csv" => "application/csv",
    "txt" => "text/plain",
    "html" => "text/html",
    "htm" => "text/html",
    "exe" => "application/octet-stream",
    "zip" => "application/zip",
    "doc" => "application/msword",
    "xls" => "application/vnd.ms-excel",
    "ppt" => "application/vnd.ms-powerpoint",
    "gif" => "image/gif",
    "png" => "image/png",
    "jpeg"=> "image/jpg",
    "jpg" =>  "image/jpg",
    "php" => "text/plain"
 );

 if($mime_type==''){ 
      $file_extension = strtolower(substr(strrchr($Source_File.$Download_Name,"."),1));
     if(array_key_exists($file_extension, $known_mime_types)){
         
        $mime_type=$known_mime_types[$file_extension];
     } else { 
        $mime_type="application/force-download";
     };
 };

 @ob_end_clean(); //off output buffering to decrease Server usage

//die;
 // if IE, otherwise Content-Disposition ignored
 if(ini_get('zlib.output_compression'))
  ini_set('zlib.output_compression', 'Off');

 header('Content-Type: ' . $mime_type);
 header('Content-Disposition: attachment; filename="'.$Download_Name.'"');
 header("Content-Transfer-Encoding: binary");
 header('Accept-Ranges: bytes');

 header("Cache-control: private");
 header('Pragma: private');
 header("Expires: Thu, 20 DEC 2016 05:00:00 GMT");

 // multipart-download and download resuming support
 if(isset($_SERVER['HTTP_RANGE']))
 { echo 'llll'; die;
    list($a, $range) = explode("=",$_SERVER['HTTP_RANGE'],2);
    list($range) = explode(",",$range,2);
    list($range, $range_end) = explode("-", $range);
    $range=intval($range);
    if(!$range_end) {
        $range_end=$size-1;
    } else {
        $range_end=intval($range_end);
    }

    $new_length = $range_end-$range+1;
    header("HTTP/1.1 206 Partial Content");
    header("Content-Length: $new_length");
    header("Content-Range: bytes $range-$range_end/$size");
 } else { echo 'kkkk1'; die;
    $new_length=$size;
    header("Content-Length: ".$size);
 }

 /* output the file itself */
 $chunksize = 1*(1024*1024); //you may want to change this
 $bytes_send = 0;
 if ($Source_File = fopen($Source_File.$Download_Name, 'r'))
 {
    if(isset($_SERVER['HTTP_RANGE']))
    fseek($Source_File, $range);

    while(!feof($Source_File.$Download_Name) && 
        (!connection_aborted()) && 
        ($bytes_send<$new_length)
          )
    {
        $buffer = fread($Source_File.$Download_Name, $chunksize);
        print($buffer); //echo($buffer); // is also possible
        flush();
        $bytes_send += strlen($buffer);
    }
 fclose($Source_File.$Download_Name);
 } else die('Error - can not open file.');

 
 
 
die();
}
?>
