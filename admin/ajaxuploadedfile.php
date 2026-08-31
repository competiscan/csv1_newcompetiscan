<?php
$ALLOW_GROUPS = array(46);
require_once("../auth_auth.php");
//include 'top.php';
require_once '../includes/functions.php';
$limit = 20;
if(isset($_REQUEST['p'])) $p = $_SESSION['manageCategory_p'] = $_REQUEST['p'];
elseif(isset($_SESSION['manageCategory_p'])) $p = $_SESSION['manageCategory_p'];
else $p = 0;
function formatBytes($size) {
  # size smaller then 1kb
  if ($size < 1024) return $size . ' Byte';
  # size smaller then 1mb
  if ($size < 1048576) return sprintf("%4.2f KB", $size/1024);
  # size smaller then 1gb
  if ($size < 1073741824) return sprintf("%4.2f MB", $size/1048576);
  # size smaller then 1tb
  if ($size < 1099511627776) return sprintf("%4.2f GB", $size/1073741824);
  # size larger then 1tb
  else return sprintf("%4.2f TB", $size/1073741824);
}

if(isset($_REQUEST['ajaxfor']) && $_REQUEST['ajaxfor']=='deletedfile' && $_REQUEST['id']!='') {
    $id=$_REQUEST['id'];
	
	$sel_sql = "select fileName from cscan_file_upload where fileID='".$id."'";                
	  $res 	   = $DRW->query($sel_sql,$DRW_read);
	  $rows    = $DRW->fetch_row($res);
	   $filename=$rows[0];
	  $path = '../fileuploads/'; 
 	  $fullPath=$path.$filename;
	if (file_exists($fullPath) ){ 
		unlink($fullPath);
	   }
        /* ################## Changes for s3 bucket ################## */
           
        $result = $s3->deleteObject([
                    'Bucket' => $bucket_name,
                    'Key' => 'fileuploads/'. $filename,
                ]);   
	/* ################## End Changes for s3 bucket ################## */   
    //print_r($result);
        $sql = "delete from cscan_file_upload where fileID='".$id."'";            
        //$rs = $DRW->query($sql,$DRW_main);
        if($DRW->query($sql, $DRW_main)){
        $data = [
            'auth_id' => $GLOBALS['AUTH_DATA']['userID'],
            'auth_name'=>authUserName($GLOBALS['AUTH_DATA']['userID']),
            'deleted_id' => $id,
            'sql_query' => $sql,
            'ip_address' => ipAddress(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'delete_type' => 'Manage Files',
            'is_mobile' => isMobile(),
            'insert_date' => date("Y-m-d H:i:s")
        ];
        trackDelete($data);
        $emailData[] = $data;
    }
        if (count($emailData) > 0) {
        $html = '<table width="100%" border="1">';
        $html .= '<tr><td>Auth ID</td><td>User Name</td><td>Deleted ID</td><td>SQL Query</td><td>IP Address</td><td>User Agent</td><td>Module</td><td>Is Mobile</td><td>Deleted Date (CST)</td></tr>';
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

        sendDevAlert('Caution! Data Deleted From Manage Files', $html);
    }

        
        
        
        
    }

// A list of permitted file extensions
$allowed = array('png','jpg','gif','doc','xls','docx','odt','zip','tar','odt','pdf','jpeg','csv','txt');
$content='';
$siteurl='';
$content='<table border="0" cellspacing="0" width="100%" cellpadding="5" class="text">
	  <tr>
	    <td colspan="7">&nbsp</td>
	    
	  </tr>
	    <tr>        <td class="adminhead">&nbsp;</td>
			<td class="adminhead">Added Date</td>
                        <td class="adminhead">Added By</td>
			<td class="adminhead">URL</td>
                        <td class="adminhead">File Size</td>
                        <td class="adminhead">Download</td>
                        <td class="adminhead">Action</td>
			
		</tr>';
	
		$className='';
                $numquery= "select COUNT(fileID) as numrows from cscan_file_upload";
                $numquery = $DRW->query($numquery,$DRW_read);
                $nrow = $DRW->fetch_row($numquery);
                $numrows = $nrow[0];
                
                 $sql = "select f.fileID,f.fileName,f.userID,f.fileSize,f.created_date,u.userName,u.user_email from cscan_file_upload as f LEFT JOIN cscan_admin_users as u on f.userID=u.userID order by f.fileID DESC LIMIT $p, $limit";                 
		
		$rs = $DRW->query($sql,$DRW_read);
                if(strstr($_SERVER['REQUEST_URI'],'?p')){
                  $siteurl=$_SERVER['HTTP_HOST'].strstr($_SERVER['REQUEST_URI'],'?p',-1);
                                      
                }
                if(strstr($_SERVER['REQUEST_URI'],'admin/manageFileupload.php')){
                  $siteurl=$_SERVER['HTTP_HOST'].str_replace('admin/manageFileupload.php','',$_SERVER['REQUEST_URI']);
                                      
                }
             if(strstr($_SERVER['REQUEST_URI'],'admin/ajaxuploadedfile.php')){
                  $siteurl=$_SERVER['HTTP_HOST'].str_replace('admin/ajaxuploadedfile.php','',$_SERVER['REQUEST_URI']);
                                      
                }                         
                
                if(strstr($siteurl,'?p')){
                  $siteurl=strstr($siteurl,'?p',-1);
                                      
                }
                
		while($row = $DRW->fetch_row($rs)) {
                    
			if ($className=='selected-bg') $className='white-bg';
			else $className='selected-bg';
	$content .='<tr class="'.$className.'">
                <td class="bodytext">&nbsp;</td>
		<td class="bodytext" valign="top">'.date('M j, Y h:i',strtotime($row[4])).'</td>
		<td class="bodytext" valign="top">'.$row[5].'</td>
                <td class="bodytext" valign="top">'.$displays3URL.'fileuploads/'. $row[1].'</td>
		<td class="bodytext" valign="top">'.formatBytes($row[3]).'</td>
                <td class="bodytext" valign="top"><a href="javascript:void(0);" onclick="return downloadFile('.$row[0].');">Download</a></td>
                 <td class="bodytext" valign="top"><a href="javascript:void(0);" onclick="if(confirm(';
     $content .="'Are you sure you want to delete this record?'";
     $content .=')) return deleteFile('.$row[0].');">Delete</a></td>   
		</tr>';
	
		}
               // '.$siteurl.'fileuploads/'. $row[1].'</td>
	$content .='<tr>
	    <td colspan="7" align="right">';
        if(isset($_GET['save'])) $content .='<span class="error">Updated</span>';
		else $content .= '&nbsp;';
	    $content .='</td>
	  </tr>
          
     <tr>
	<td colspan="7">
		<table border="0" width="100%" cellspacing = "0"  cellpadding ="5">
			<tr>
				<td>&nbsp;</td>
			</tr>';

$firstlink = '[First]';
$prevlink = '[Prev]';
$nextlink = '[Next]';
$lastlink = '[Last]';
$middlelinks = '';
$limstart = $p;
$limiter = $limit;
$rowcnt = $numrows;
$show = 10;
//first and previous only if not on first
if($limstart>0){
	if($limstart>=$limiter) $prev = $limstart - $limiter;
	else $prev = 0;
	$firstlink = "[<a href=\"javascript:void(0);\" onclick=\"return FetchUploadedFile(0);\">First</a>]";
	$prevlink = "<a href=\"javascript:void(0);\" onclick=\"return FetchUploadedFile($prev);\">&laquo; Prev $limiter</a>";
}
// middle loop through total results
$numbers = ceil($rowcnt/$limiter);
$loopstart = ceil($limstart/$limiter);
if($loopstart<($show-1)) $loopstart = 0; // begin, do not move until 4
if($numbers<$show) $loopend = $numbers; // loopend is less than $show
else $loopend = $loopstart+$show;
if($loopend>$numbers && $loopstart!=0) { // end, show last $show
	$loopstart = $numbers - $show;
	$loopend = $numbers;
}
for($i=$loopstart; $i<$loopend; $i++){
	$startnum = $limiter * $i;
	if($startnum!=$limstart) {
		$middlelinks .= "<a href=\"javascript:void(0);\" onclick=\"return FetchUploadedFile($startnum);\">".($i+1)."</a> ";
	}
	else $middlelinks .= ($i+1).' ';
}
//next and last if not on last
if($limstart<$rowcnt && (($limstart+($limiter*2))<$rowcnt || ($rowcnt - ($limstart + $limiter))>0)){
	$next = $limstart + $limiter;
	$nextlink = "<a href=\"javascript:void(0);\" onclick=\" return FetchUploadedFile($next);\">Next $limiter &raquo;</a>";
	$lastlink = "[<a href=\"javascript:void(0);\" onclick=\" return FetchUploadedFile(($numbers-1)*$limiter);\">Last</a>]";
}

if($middlelinks!='') $middlelinks = "[ $middlelinks ] &nbsp;";
$content .= '<tr><td align="center" class="bodytext">'.$firstlink.' &nbsp;'.$prevlink.' &nbsp;'. $middlelinks. $nextlink.' &nbsp;'.$lastlink.'</td></tr>';
$content.='<tr><td align="center" class="bodytext">Showing results'.($limstart+1).' to ';
if($limstart+$limiter < $rowcnt) $content .=($limstart+$limiter);
else $content .= $rowcnt;
$content .=' of '.$rowcnt.'</td></tr>

		</table>
	</td></tr> 
	</table>';
            
echo $content; die;
?>
