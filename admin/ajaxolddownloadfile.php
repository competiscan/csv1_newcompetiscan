<?php 
$ALLOW_GROUPS = array(47);
ignore_user_abort(true);
set_time_limit(0); // disable the time limit for this script
require_once("../auth_auth.php");
require_once '../includes/functions.php';
if(isset($_REQUEST['id']) && $_REQUEST['id']!=''){
        $id=$_REQUEST['id'];
        $sql = "SELECT file_path FROM cscan_olddownloads where id=$id";
        $result = $DRW->query($sql, $DRW_read);
        $rs = $DRW->fetch_array($result);
        $file_path=$rs['file_path']; 
        $expolde_file=explode("/",$file_path); 
        $filename=$expolde_file[1]; 
      /* ################## Changes for s3 bucket ################## */
    try {
        // Get the object.
        $result = $s3->getObject([
            'Bucket' => $bucket_name,
            'Key'    => $file_path
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
  /*################################## END s3 Bucket#######################*/

}
 ?>
