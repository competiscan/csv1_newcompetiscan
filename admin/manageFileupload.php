<?php
$ALLOW_GROUPS = array(46);
require_once("../auth_auth.php");
include 'top.php';
$limit = 20;
if(isset($_REQUEST['p'])) $p = $_SESSION['manageCategory_p'] = $_REQUEST['p'];
elseif(isset($_SESSION['manageCategory_p'])) $p = $_SESSION['manageCategory_p'];
else $p = 0;
?>

<table width="100%" border="0" cellspacing="0" cellpadding="5" class="text">
  <tr><td class="adminhead" align="center" colspan="3">Manage Files</td></tr>
</table>

<table width="100%" border="0" cellspacing="0" cellpadding="5" class="text">
  <tr><td align="center" colspan="5">       
          
          
          <!-- desk box -->
<div class="desk-box"> 
   <!-- desk left box -->
   <div class="desk-lftbox fl">
         <div class="breadcum fl">
         
      </div>
   </div>
  
</div>
<!-- desk box end -->
<div class="cl"></div>

<!-- dashboard box -->
<div class="dashboard-box">
    <h3><div class="message" id="flashMessage"></div></h3>
    <br>
   <div class="pages index  recent-staff-box">
      <form id="upload" method="post" action="upload.php" enctype="multipart/form-data">
    	<div id="drop" >
            Drop Here
            <a>Upload New File</a>
            <input type="file" name="upl" multiple />
        </div>
        <ul>
         <!-- The file uploads will be shown here -->
        </ul>
    </form>
   </div>
</div>    
        
</td></tr>
</table>


<!--	<form method="post" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>">-->
	
<div id="filelist">
<table border="0" cellspacing="0" width="100%" cellpadding="5" class="text">
	  <tr>
	    <td colspan="7">&nbsp</td>
	    
	  </tr>
	    <tr>        <td class="adminhead">&nbsp;</td>
			<td class="adminhead">Added Date</td>
                        <td class="adminhead">Added By</td>
			<td class="adminhead">URL</td>
                        <td class="adminhead">File Size</td>
                        <td class="adminhead">Download</td>
                        <?php  if(checkGroup(80)){?>
                        <td class="adminhead">Action</td>
                        <?php }?>
			
		</tr>
	<?php
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
                if(strstr($siteurl,'?p')){
                  $siteurl=strstr($siteurl,'?p',-1);
                                      
                }
                
                
             
              
                
		while($row = $DRW->fetch_row($rs)) {
                    
			if ($className=='selected-bg') $className='white-bg';
			else $className='selected-bg';
	?>
		<tr class="<?php echo $className;?>">
                <td class="bodytext">&nbsp;</td>
		<td class="bodytext" valign="top"><?php echo date('M j, Y h:i',strtotime($row[4])); ?></td>
		<td class="bodytext" valign="top"><?php echo $row[5]; ?></td>
                <!-- ################## Changes for s3 bucket ##################-->
                
                <!--<td class="bodytext" valign="top"><?php //echo 'https://'.$siteurl.'fileuploads/'. $row[1]; ?></td>-->
                <td class="bodytext" valign="top"><?php echo $displays3URL.'fileuploads/'. $row[1]; ?></td>
		
                <!-- ################## Changes for s3 bucket ################## -->
                
                <td class="bodytext" valign="top"><?php echo formatBytes($row[3]); ?></td>
                <td class="bodytext" valign="top"><a href="javascript:void(0);" onclick="return downloadFile(<?php echo $row[0];?>);">Download</a></td>
                <?php  if(checkGroup(80)){?>
                <td class="bodytext" valign="top"><a href="javascript:void(0);" onclick="if(confirm('Are you sure you want to delete this record?')) return deleteFile(<?php echo $row[0];?>);">Delete</a></td>
                <?php }?>
                </tr>
	<?php
		}
	?>
	  <tr>
	    <td colspan="7" align="right"><?php 
		if(isset($_GET['save'])) print '<span class="error">Updated</span>';
		else print '&nbsp;';
	    ?></td>
	    
	  </tr>
        
        <tr>
	<td colspan="7">
		<table border="0" width="100%" cellspacing = "0"  cellpadding ="5">
			<tr>
				<td>&nbsp;</td>
			</tr>
<?php
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
print "<tr><td align=\"center\" class=\"bodytext\">$firstlink &nbsp; $prevlink &nbsp; $middlelinks $nextlink &nbsp; $lastlink</td></tr>";
print "<tr><td align=\"center\" class=\"bodytext\">Showing results ".($limstart+1)." to ";
if($limstart+$limiter < $rowcnt) print ($limstart+$limiter);
else print $rowcnt;
print " of $rowcnt</td></tr>";
?>
		</table>
	</td></tr>  
     </table>
</div>
	<input type="hidden" name="send" value="1" />
        
       <!--</form>-->
	<script type="text/javascript">
	<!--
	function showHelp(file){
		var win = window.open(file,'fulltext','top=0,left=0,height=650,width=600,resizable=1,scrollbars=yes');
		win.focus();  
	}
	//-->
	</script>
	<?php 
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

include 'bottom.php';
//echo $siteurl.'hh';exit;
if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
    $adminsiteurl='https://'.$siteurl.'admin/';
    $frontsiteurl='https://'.$siteurl.'/';
}else{
    $adminsiteurl='https://'.$siteurl.'admin/';
    $frontsiteurl='https://'.$siteurl.'/';
}
?>
        <script type="text/javascript">
        
        function downloadFile(id){
            location.href='<?php echo $adminsiteurl;?>ajaxdownloadfile.php?id='+id;

        }
    function deleteFile(id){
        jQuery("#filelist").html('<img style="margin: 30px 300px;" src="<?php echo $frontsiteurl;?>images/loader.gif">');  
        jQuery.ajax({
            url: "<?php echo $adminsiteurl;?>ajaxuploadedfile.php",
            type: "post", //send it through get method
            data:{ajaxfor:'deletedfile',id:id},
            success: function(result) {
                //alert(result); return false;
               jQuery("#filelist").html(result);

                }
        });
    }
        
    function FetchUploadedFile(p){ 
           jQuery("#filelist").html('<img style="margin: 30px 300px;" src="<?php echo $frontsiteurl;?>images/loader.gif">'); 
        jQuery.ajax({
            url: "<?php echo $adminsiteurl;?>ajaxuploadedfile.php",
            type: "post", //send it through get method
            data:{ajaxfor:'fetchuploadedfile',p:p},
            success: function(result) {
                //alert(result); return false;
                jQuery("#filelist").html(result);
          }
        });            
        
    }
        
        </script> 
