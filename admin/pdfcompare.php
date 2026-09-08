<?php
require_once("../includes/ehLog_set.php");
$ehL->start(__FILE__);
ini_set("default_charset", "utf-8");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set("memory_limit", "-1");
set_time_limit(0);

include_once('../includes/dbcon.php');
$DRW->databaseReadWrite_die = 1;
require_once('../includes/clean.php');
require_once('../includes/functions.php');
########################################
function baseUrl()
{
    /* First we need to get the protocol the website is using */
    //$protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"], 0, 5)) == 'https' ? 'https://' : 'http://';
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    /* returns /myproject/index.php */
    $path = $_SERVER['PHP_SELF'];
    /*
     * returns an array with:
     * Array (
     *  [dirname] => /myproject/
     *  [basename] => index.php
     *  [extension] => php
     *  [filename] => index
     * )
     */
    $path_parts = pathinfo($path);
    $arrDir = array();
    $arrDir = explode('/',$path_parts['dirname']);
    $directory = next($arrDir);
    //$directory = $path_parts['dirname'];
    /*
     * If we are visiting a page off the base URL, the dirname would just be a "/",
     * If it is, we would want to remove this
     */
    $directory = ($directory == "") ? "/" : '/'.$directory;
    /* Returns localhost OR mysite.com */
    $host = $_SERVER['HTTP_HOST'];
    /*
     * Returns:
     * http://localhost/mysite
     * OR
     * https://mysite.com
     */
    return '//' . $host . $directory;
}
$u1 = $u2 = $error = '';
if(!empty($_GET['type']) && trim($_GET['type'])=='maxmail'){
    if(!empty($_GET['u1'])){
        $baseUrl = str_replace("admin","",baseUrl());
        $path = str_replace("admin","",dirname(__FILE__));
        if(file_exists($path.'/'.urldecode(trim($_GET['u1'])))){
            $u1= $baseUrl.urldecode(trim($_GET['u1']));
        }
        if(!empty($path.urldecode(trim($_GET['u1'])))){
        $info = $s3->doesObjectExist($bucket_name,trim($_GET['u1']));
            if($info){
               $u1= $displays3URL.trim($_GET['u1']);
            }
        }
        if(file_exists($path.'/'.urldecode(trim($_GET['u2'])))){
            $u2= $baseUrl.urldecode(trim($_GET['u2']));
        }  
        if(empty($u1) && empty($u2)){
            $error = 'Files does not exits!';
        }elseif(empty($u1) && !empty($u2)){
            $error = 'Approved file does not exits!';
        }elseif(!empty($u1) && empty($u2)){
            $error = 'Duplicate file does not exits!';
        }
    }
}elseif(!empty($_GET['type']) && trim($_GET['type'])=='maxmail_tmp'){
    if(!empty($_GET['u1'])){
        $baseUrl = str_replace("admin","",baseUrl());
        $path = str_replace("admin","",dirname(__FILE__));
        if(file_exists($path.'/'.urldecode(trim($_GET['u1'])))){
            $u1= $baseUrl.urldecode(trim($_GET['u1']));
        }else{
            $duplicate_with = basename(urldecode(trim($_GET['u1'])));
            $filePart = explode("_",$duplicate_with); 
            $temp_muid = current($filePart);
            $temp_muid = (int)$temp_muid;
            $mailbox_uid = (!empty($filePart[1]))?trim($filePart[1]):0;
            if($mailbox_uid){
                $sql = "SELECT muid FROM cscan_email WHERE mailbox_uid = '".$mailbox_uid."'";
                $query23 = $DRW->query($sql, $DRW_read2);
                if($DRW->num_rows($query23) > 0){
                    $row23 = $DRW->fetch_assoc($query23);
                    $file_duplicate_with_muid = $row23['muid'];
                    $indexed_muid = (int)$file_duplicate_with_muid;
                }
            }
            $panelist_id = current(explode(".",end($filePart)));
            if(!empty($indexed_muid)){
                $sql = "SELECT cettext FROM cscan_email_text WHERE muid='".$indexed_muid."' AND cettype='text/html'";
                $query = $DRW->query($sql, $DRW_read2);
                if($DRW->num_rows($query) > 0){
                    $row = $DRW->fetch_assoc($query);
                    $html = $row['cettext'];
                    if(!empty($html)){
                        $path2 = str_replace("admin","",dirname(__FILE__)).'/damaxmailhtml';
                        $myfile = fopen($path2."/indexed.html", "w") or die("Unable to open file!");
                        if(file_exists($path2."/indexed.html")){
                            $u1 = $baseUrl.'/damaxmailhtml/indexed.html';
                        }
                        fwrite($myfile, $html);
                        fclose($myfile);
                    }
                }
            }
        }
        if(file_exists($path.'/'.urldecode(trim($_GET['u2'])))){
            $u2= $baseUrl.urldecode(trim($_GET['u2']));
        }  
        if(empty($u1) && empty($u2)){
            $error = 'Files does not exits!';
        }elseif(empty($u1) && !empty($u2)){
            $error = 'Indexed file does not exits!';
        }elseif(!empty($u1) && empty($u2)){
            $error = 'Duplicate file does not exits!';
        }
    }
}else{
    if(!empty($_GET['u1'])){
        $baseUrl = str_replace("admin","",baseUrl());
        $path = str_replace("admin","",dirname(__FILE__));
        if(file_exists($path.urldecode(trim($_GET['u1'])))){
            $u1= $baseUrl.urldecode(trim($_GET['u1']));
        }
        if(!empty($path.urldecode(trim($_GET['u1'])))){
            $info = $s3->doesObjectExist($bucket_name,substr(trim($_GET['u1']),1) );
            if($info){
               $u1= $displays3URL.substr(trim($_GET['u1']),1);
            }
            
        }
       
        if(file_exists($path.'/dachicagorecordsftp_duplicate/'.urldecode(trim($_GET['u2'])))){
            $u2= $baseUrl.'dachicagorecordsftp_duplicate/'.urldecode(trim($_GET['u2']));
        }  
        if(empty($u1) && empty($u2)){
            $error = 'Files does not exits!';
        }elseif(empty($u1) && !empty($u2)){
            $error = 'Approved file does not exits!';
        }elseif(!empty($u1) && empty($u2)){
            $error = 'Duplicate file does not exits!';
        }
    }
}
?>
<html>
    <head>
        <title></title>
        <style>
            .body {
                background-color: #444;
                margin: 0;
            }    
            #wrapper {
                 width: 100%;
                 margin: 0 auto;
            }
            #leftcolumn, #rightcolumn {
                border: 1px solid white;                
                min-height: 450px;
                color: white;
                overflow: scroll;
    -webkit-overflow-scrolling: touch;
            }
            #leftcolumn {
                float: left;
                width: 48%;
                background-color: #777;
            }
            #rightcolumn {
                float: right;
                width: 48%;
                background-color: #777;
            }
            .error{
                margin: 10;
                color: red;
                text-align: center;
            }
        </style>
    </head>
    <body <?=(empty($error))?'class="body"':''?>>
        <?php if(!empty($error)){echo '<p class="error">'.$error.'</p>';exit;}?>
        <div id="wrapper">            
            <div id="leftcolumn">
                <p>Approved File:</p>
                <iframe src="<?= $u1;?>" width="100%" height="600" scrolling="yes"></iframe>
            </div>
            <div id="rightcolumn">
               <p>Duplicate File:</p>
                <iframe src="<?= $u2;?>" width="100%" height="600" scrolling="yes"></iframe>
            </div>
        </div>
    </body>
</html>