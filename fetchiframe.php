<?php require_once('includes/globalSession.php');
if(!isset($_SESSION['public_admin_access'])){
	require_once('includes/checklogin.php');
}
require_once 'includes/thumb.php';
require_once 'HTTP/Download.php';
$img_link='';
if(isset($_REQUEST['id'])) $productID = (int)$_REQUEST['id'];
else $productID = 0;
if(isset($_REQUEST['filetyp'])) $filetyp = (int)$_REQUEST['filetyp'];
else $filetyp ='';

if(isset($_REQUEST['prevtyp'])) $prevtyp = (int)$_REQUEST['prevtyp'];
else $prevtyp ='';
if($productID!=0){	
	$sql2 = "SELECT SQL_NO_CACHE document_path,document_content_type,document_filename,bucket_name FROM cscan_document WHERE productID=$productID AND document_id=1"; 
                $rs2 = $DRW->query($sql2, $DRW_read);
                $row2 = $DRW->fetch_row($rs2);               
                $document_content_type=$row2[1];
                $document_filename=$row2[2];
                $check_bucket_name=$row2[3];
                
                if($document_content_type=='html'){
                     $img_link=$row2[0];
                }
                //$video_link=$row2[0].$document_filename;
                if($check_bucket_name!='NULL' AND $check_bucket_name!=''){
                    $video_link=$displays3CSV2URL.substr($row2[0],0).$document_filename;
                }else{
                    $video_link=$displays3URL.substr($row2[0],1).$document_filename;
                }
                //$video_link=$displays3URL.substr($row2[0],1).$document_filename;
                $hosts=$_SERVER['HTTP_HOST'];
                if($hosts=='localhost'){
                    $baseurl='http://localhost/uat3.competiscan.com';
                }else if($hosts=='demo.competiscan.com'){
                    $baseurl='http://demo.competiscan.com';
                }elseif($hosts == 'uat3.competiscan.com'){
                    $baseurl='http://uat3.competiscan.com/';
                }else{
                    $baseurl='https://competiscan.com';
                }
	if($filetyp=='' AND $prevtyp==''){
            if($img_link==''){
               // $img_link=$baseurl.$video_link;
               if($check_bucket_name!='NULL' AND $check_bucket_name!=''){
                    $img_link=$displays3CSV2URL.substr($row2[0],1).$document_filename;
                }else{
                    $img_link=$displays3URL.substr($row2[0],1).$document_filename;
                }
                
            }
            $content=$img_link;
        }else if($filetyp=='1' AND $prevtyp=='1'){            
            $content='<iframe border="0" src="' . $img_link . '" >
			 </iframe>';
        }else if($filetyp=='2' AND $prevtyp=='2'){
            $content='<video width="350" height="150" controls>
            <source src="'.$video_link . '" type="video/mp4">
            Your browser does not support the video tag.
            </video>';            
        }
	   echo $content; die;
}?>