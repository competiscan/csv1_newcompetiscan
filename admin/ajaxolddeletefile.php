<?php
$ALLOW_GROUPS = array(47);
require_once("../auth_auth.php");
require_once '../includes/functions.php';
if (ENV == 'localhost') {
    $site_urls = 'http://localhost/competiscan.com/';
} elseif (ENV == 'demo.competiscan.com') {
    $site_urls = 'https://demo.competiscan.com/';
} else {
    $site_urls = 'https://competiscan.com/';
}
$className='';
if(isset($_POST['search_name']) and $_POST['search_name']!=''){
   echo  $search_name=trim($_POST['search_name']); 
}else{ 
    $search_name='';
}
if(strstr($_SERVER['REQUEST_URI'],'admin/ajaxolddeletefile.php')){
    $siteurl=$_SERVER['HTTP_HOST'].str_replace('admin/ajaxolddeletefile.php','',$_SERVER['REQUEST_URI']);                                      
  }
  if(strstr($siteurl,'?id')){
    $siteurl=strstr($siteurl,'?id',-1);

  }
 $adminsiteurl=$site_urls.'admin/';
 $frontsiteurl=$site_urls;
if(isset($_REQUEST['ajaxfor']) && $_REQUEST['ajaxfor']=='deletedfile' && $_REQUEST['id']!='') {
    $id=$_REQUEST['id']; 
    //echo $bucket_name; die;
    if(isset($_REQUEST['search_name'])&& $_REQUEST['search_name']!=''){
        $search_name=trim($_REQUEST['search_name']);
    }else{
        $search_name='';
    }
    $sql = "SELECT file_path FROM cscan_olddownloads where id=$id";
        $result = $DRW->query($sql, $DRW_read);
        $rs = $DRW->fetch_array($result);
        $file_path=$rs['file_path']; 
        $expolde_file=explode("/",$file_path); 
        $filename=$expolde_file[1];
        $sqlDelete="Delete FROM cscan_olddownloads where id=$id";
        //$sqlDelete='';
        //$fullPath=$s3URL.$file_path;
 	  //$fullPath=$displays3URL.$file_path;
               $data = [
                    'auth_id' => $GLOBALS['AUTH_DATA']['userID'],
                    'auth_name'=>authUserName($GLOBALS['AUTH_DATA']['userID']),
                    'deleted_id' => 0,
                    'sql_query' => $sqlDelete,
                    'ip_address' => ipAddress(),
                    'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                    'delete_type' => 'Manage Old Downloads',
                    'is_mobile' => isMobile(),
                    'insert_date' => date("Y-m-d H:i:s")
                ];
              // trackDelete($data);
                $emailData[] = $data; 
                /***##################### Start S3 Delete Object #######################*/
                // Delete an object from the bucket.
                $result=$s3->deleteObject([
                    'Bucket' => $bucket_name,
                    'Key'    => $file_path
                ]);
                //echo "<pre>";
                //print_r($result); exit;
                $DRW->query($sqlDelete,$DRW_main);
                /***##################### End S3 Delete Object #######################*/
                //unlink($fullPath);
                if (count($emailData) > 0) {
                    $html = '<table width="100%" border="1">';
                    $html .= '<tr><td>Auth ID</td><td>User Name</td><td>Deleted ID</td><td>Filde Path</td><td>IP Address</td><td>User Agent</td><td>Module</td><td>Is Mobile</td><td>Deleted Date (CST)</td></tr>';
                    foreach ($emailData as $tr) {
                        if (is_array($tr) && count($tr) > 0) {
                            $html .= '<tr>';
                            foreach ($tr as $td) {
                                $html .= '<td>' . $td . '</td>';
                            }
                            $html .= '</tr>';
                        }
                    }
                    $html .= '</table>';
                   //sendDevAlert('Caution! Data Deleted From Manage Old Downloads', $html);
                }
                //echo 1;exit;
	          
    }  
    
    ?>

