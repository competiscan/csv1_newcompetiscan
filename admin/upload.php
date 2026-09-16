<?php $ALLOW_GROUPS = array(46);
require_once("../auth_auth.php");
//include 'top.php';
require_once '../includes/functions.php';
// A list of permitted file extensions
$allowed = array('png','jpg','gif','doc','xls','xlsx','docx','odt','zip','tar','pdf','jpeg','csv','txt','ppt','pptx','PPT','PPTX','mp4','htm','html','HTM','HTML');
if(isset($_FILES['upl']) && $_FILES['upl']['error'] == 0){
    //$path = $_SERVER['DOCUMENT_ROOT'].'/fileuploads'; 
   // chmod($path, 0777);
        $file = (!empty($_FILES['upl']))?$_FILES['upl']:'';
        $filename=$file['name'];
        $randomnim=rand ( 10000 , 99999 );
        $filename=$randomnim.$filename;
	$extension = pathinfo($file['name'], PATHINFO_EXTENSION);

	if(!in_array(strtolower($extension), $allowed)){
		echo '{"status":"error"}';
		exit;
	}
        
                
        $extArr = explode(".", $filename);
        $ext = strtolower(end($extArr));
        //$mimeType = 'application/pdf';
        $mimeType = '';
        if($ext == 'png'){
            $mimeType = 'image/png';
        }elseif($ext == 'jpeg' || $ext == 'jpg' || $ext == 'gif'){
            $mimeType = 'image/jpeg';
        }elseif($ext == 'xls' || $ext == 'xlsx' ){
            $mimeType = 'application/vnd.ms-excel';
        }  
        elseif($ext == 'csv' ){
            $mimeType = 'application/vnd.ms-excel';
        }
        elseif($ext == 'pptx' || $ext == 'PPTX' ){
            $mimeType = 'application/vnd.openxmlformats-officedocument.presentationml.presentation';
        }elseif($ext == 'ppt' || $ext == 'PPT' ){
            $mimeType = 'application/vnd.ms-powerpoint';
        }
        $result = $s3->putObject(                    
                array(
                        'Bucket'=>$bucket_name,
                        'Key' =>  'fileuploads/'.$filename,
                        'SourceFile' => $_FILES['upl']['tmp_name'],
                        //'Body' => fopen($_FILES['upl']['tmp_name'], 'rb'),
                        'ContentType'	=> $mimeType,
                        'ACL' => 'public-read',
                        'Metadata'      => array(
                            'string'        => 'string'
                        )
                    )
                );
        
            $size       = $_FILES['upl']['size'];
            $userID     = $_SESSION['_auth_COMPETI']['data']['userID'];
            $date       = date('Y-m-d h:i:s');
            $sql       = "INSERT INTO cscan_file_upload (fileName ,userID,fileSize,created_date) VALUES ('".$filename."','".$userID."','".$size."','".$date."')";
            if($DRW->query($sql,$DRW_main)){            
                 echo   '{"status":"success"}';
                exit;
            }
            //print_r($result); die;
            /* 
            $result = $s3->deleteObject([
                        'Bucket' => $bucket_name,
                        'Key' => $dataV[0] . $dataV[1],
                    ]);
            */
        
        
        
        
      /* if(move_uploaded_file($_FILES['upl']['tmp_name'], '../fileuploads/'.$filename)){
            
          //  try {
                
                
                $tempFilePath = $file['tmp_name'];
                
                $extArr = explode(".", $filename);
                $ext = strtolower(end($extArr));
                $mimeType = 'application/pdf';
                if($ext == 'png'){
                    $mimeType = 'image/png';
                }elseif($ext == 'jpeg' || $ext == 'jpg'){
                    $mimeType = 'image/jpeg';
                } 
                print_r($_FILES);
                echo $_FILES['upl']['tmp_name'];
                $result = $s3->putObject(                    
                        array(
                                'Bucket'=>$bucket_name,
                                'Key' =>  'fileuploads/'.$filename,
                                'SourceFile' => $_FILES['upl']['tmp_name'],
                                //'Body' => fopen($_FILES['upl']['tmp_name'], 'rb'),
                                'ContentType'	=> $mimeType,
                                'ACL' => 'public-read',
                                'Metadata'      => array(
                                    'string'        => 'string'
                                )
                            )
                        );
                
                
                
                
                print_r($result); die;
            } catch (S3Exception $e) {
                echo $e->getMessage() . "\n";
            } 
            
            //$filename   = $_FILES['upl']['name'];
            $size       = $_FILES['upl']['size'];
            $userID     = $_SESSION['_auth_COMPETI']['data']['userID'];
            $date       = date('Y-m-d h:i:s');
            $sql       = "INSERT INTO cscan_file_upload (fileName ,userID,fileSize,created_date) VALUES ('".$filename."','".$userID."','".$size."','".$date."')";
            if($DRW->query($sql,$DRW_main)){            
            echo   '{"status":"success"}';
            exit;
            }
	} */
}
echo '{"status":"error"}';
exit; 
?>