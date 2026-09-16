<?php
$ALLOW_GROUPS = array(84);
require_once("../auth_auth.php");
include 'top.php';
require_once("../includes/functions.php");

if(!defined('ENV')){
    define('ENV',getenv('SERVER_NAME'));
} 
if(ENV == 'localhost'){
    $site_urls='http://localhost/competiscan.com/';
}elseif(ENV == 'demo.competiscan.com'){
    $site_urls='http://demo.competiscan.com/';
}else{
    $site_urls='https://competiscan.com/';
}

$FolderPath = dirname(__DIR__);

function rrmdir($dir) {
	if (is_dir($dir)) {
	$objects = scandir($dir);
	foreach ($objects as $object) {
	  if ($object != "." && $object != "..") {
	    if (filetype($dir."/".$object) == "dir") 
	       rrmdir($dir."/".$object); 
	    else unlink   ($dir."/".$object);
	  }
	}
	reset($objects);
	rmdir($dir);
	return true;
	}
}

$limit = 20;
$msg = '';

/*if(isset($_REQUEST['search_text'])) {
	$_SESSION['search_text'] = $_REQUEST['search_text'];
} 
elseif(isset($_REQUEST['show_All']) || !isset($_SESSION['search_text'])) {
	$_SESSION['search_text'] = '';
}*/


if(isset($_GET['sort'])) $sort = (int) $_GET['sort'];
else $sort = 0;

if(isset($_GET['p'])) $p = $_GET['p'];
else $p = 0;

if(isset($_REQUEST['deletebut']) && $_REQUEST['deletebut']==1 && isset($_REQUEST['delID'])) {
	$delID = $_REQUEST['delID'];
	if(is_array($delID)){
		foreach ($delID as $id) {
			$SelectSql = "SELECT folder_name FROM cscan_presentation where ID = $id";
			$rs = $DRW->query($SelectSql,$DRW_read);
			$row = $DRW->fetch_assoc($rs);
			$RemoveDirectory = $FolderPath.'/presentation/'.$row['folder_name'];
			$return = rrmdir($RemoveDirectory);
			if($return){
				$sql = "DELETE FROM cscan_presentation where ID =$id";
				$DRW->query($sql,$DRW_main);
			}
		}
	}else{
		$SelectSql = "SELECT folder_name FROM cscan_presentation where ID = $delID";
		$rs = $DRW->query($SelectSql,$DRW_read);
		$row = $DRW->fetch_assoc($rs);
		$RemoveDirectory = $FolderPath.'/presentation/'.$row['folder_name'];
		$return = rrmdir($RemoveDirectory);
		if($return){
			$sql = "DELETE FROM cscan_presentation where ID =$delID";
			$DRW->query($sql,$DRW_main);
		}
	}
	ob_end_clean();
	header("Location: manage-presentation.php");
	exit;
}

?>
<script type="text/javascript" src="https://www.competiscan.com/admin/jquery.min.js"></script>

<?php if(isset($_FILES["zip_file"]) && !empty($_FILES["zip_file"]["name"])) {
	$filename = $_FILES["zip_file"]["name"];
	$name = explode(".", $filename);
	if($name[1] == 'zip'){
		$sql = "INSERT INTO cscan_presentation(folder_name) VALUES ('NULL')";
		$DRW->query($sql,$DRW_main);
		$ID = $DRW->insert_id($DRW_main);
		$time = time();
		$ext = substr($filename,strpos($filename,'.'));
		$uploadfilename = $time.$ext;
		$source = $_FILES["zip_file"]["tmp_name"];
		$type = $_FILES["zip_file"]["type"];
		
		
		$accepted_types = array('application/zip', 'application/x-zip-compressed', 'multipart/x-zip', 'application/x-compressed');
		foreach($accepted_types as $mime_type) {
			if($mime_type == $type) {
				$okay = true;
				break;
			} 
		}
		
		$continue = strtolower($name[1]) == 'zip' ? true : false;
		if(!$continue) {
			$message = "The file you are trying to upload is not a .zip file. Please try again.";
		}

		$main_folder_path = "../presentation/";
		$target_path = $main_folder_path.$uploadfilename;
		$folder_path = $main_folder_path.$time.'/';
		
		if(move_uploaded_file($source, $target_path)) {
			$zip = new ZipArchive();
			$x = $zip->open($target_path);
			if ($x === true) {
				$zip->extractTo($folder_path); // change this to the correct site path
				if(@chmod($folder_path, 0777)) {
				    @chmod($folder_path, 0777);
				    @chown($folder_path, 'apache');
				}
				else{
					echo "Couldn't do it."; die;
				}
				$zip->close();
				unlink($target_path);
				$sql = "UPDATE cscan_presentation set folder_name = '$time' where ID = '$ID'";
				$DRW->query($sql,$DRW_main);
			}
			$message = "Your .zip file was uploaded and unpacked.";
		} else {	
			$message = "There was a problem with the upload. Please try again.";
		}
	}else{
		$message = "Error... Only ZIP file can be allowed.";
	}
	
	echo '<script type="text/javascript">
			$(document).ready(function(){
				setTimeout(function(){
					$("#displayMessage").hide()
				}, 5000);
			});
		</script>';
	echo "<p id='displayMessage' style='text-align: center;margin: 10px 0px 10px 0px; color:red'>".$message."</p>";
}
?>
<table width="100%" border="0" cellspacing="0" cellpadding="5" class="text">
	<tr><td class="adminhead" align="center">Manage Presentation</td></tr>
	<tr>
		<td>
			<form enctype="multipart/form-data" method="post" action="">
				<label>Choose a zip file to upload: <input type="file" name="zip_file" /></label>
				<input type="submit" name="submit" value="Upload" style="margin:15px 0px 10px 0px;"/>
			</form>
		</td>
	</tr>
	<!--tr><td>
		<form method="post" name="searchForm" action="communication-type.php" onsubmit="return check_searchform();" style="display:inline;">
		<strong>Search Communication Type:</strong>
		<input type="text" name="search_text" class="input_box" value="<?php //echo $_SESSION['search_text']; ?>" />
		<input class="button" style="width:60px" type="submit" name="search_Submit1" value="Search" />
		<input type="hidden" name="search_Submit" value="1" /><input type="hidden" name="p" value="0" /></form>
		&nbsp;&nbsp;
		<form action="communication-type.php" method="post" style="display:inline;">
		<input class="button" style="width:70px" type="submit" name="show_All1" value="Show All" />
		<input type="hidden" name="show_All" value="1" /><input type="hidden" name="p" value="0" /></form>
	</td></tr-->
	<tr>
		<td>
		<form method="post" name="communicationForm" action="communication-type.php">
			<table width="100%" border="0" cellspacing="0" cellpadding="0" class="text">
			<tr>
			<!--td><strong>Note:</strong> Click to upload zip file.</td-->
			<td align="right">
			<!--input class="button" type="button" value="Upload Zip File" onclick="location.href='upload-zip-file.php'; return false;" /-->
			<input class="button" style="width:60px" type="button" name="delete1" value="Delete" id="delBt" onclick="deleteCheck(); return false;" />
	        </td>
			</tr>
			</table>
		</form>
		</td>
	</tr>
</table>
  
<form action="manage-presentation.php" method="post" name="deleteform">
<table width="100%" border="0" cellspacing="0" cellpadding="5" class="text">
  <tr>
    <td width="5%" class="adminhead" height="15"><input type="checkbox" name="setUnset" onclick="setAll();" /></td>
    <td width="5%" class="adminhead" height="15"><strong>Sr No.</strong></td>
    <td width="20%" class="adminhead" height="15"><strong>Url</strong><?php if($sort!=1) print " <a href=\"".$_SERVER['PHP_SELF']."?sort=1&p=0\" class=\"blue\">sort</a>"; ?></td>
	<td width="5%" class="adminhead" height="15" align="center"><strong>Action</strong></td> 
  </tr>
  <tr>
	<td colspan="5" class="error" align="center"><?php echo $msg; ?></td>
  </tr> 
<?php
	$sql = "SELECT * FROM cscan_presentation";
	$rs = $DRW->query($sql,$DRW_read);
	$numquery = "SELECT COUNT(ID) as numrows FROM cscan_presentation";
	
	/*if($_SESSION['search_text']!='') { 
		$search_key = mysqlLike($_SESSION['search_text']);
		$and = " WHERE cac.type LIKE '%$search_key%'";
		$sql .= $and;
		$numquery .= $and;
	}*/
	
	$numquery = $DRW->query($numquery,$DRW_read);
	$nrow = $DRW->fetch_array($numquery);
	$numrows = $nrow[0];

	switch($sort){
		case 1:
			$sql .= " ORDER BY ID ";
			break;
		default:
			$sql .= " ORDER BY ID ";
	}
	$sql .= "LIMIT $p,$limit";
	
	$rs = $DRW->query($sql,$DRW_read);
	$resultCount = $DRW->num_rows($rs);
	$folderName = 'presentation/';
	if( $resultCount > 0 ) {
		$className='';
                $i=1;
		while($row = $DRW->fetch_assoc($rs)) {
			$ID = $row['ID'];
			$folder_name = $row['folder_name'];
?>
      <tr valign="top" class="white-bg">
      	<td><input type='checkbox' name='delID[]' value='<?php echo $ID; ?>'></td>
      	<td><?php echo $i++; ?></td>
        <td><?php echo $site_urls.$folderName.$folder_name;?></td>
		<td align="center">
			<a class="hlinks" target="_blank" href="<?php echo $site_urls.$folderName.$folder_name;?>" title="Show">Show</a>
			<span style="font-family: Tahoma;font-size: 12px;color: #14734F;text-decoration: none;">/</span>
			<a class="hlinks deleteFile" href="javascript:void(0)" title="Delete" value="<?php echo $ID; ?>">Delete</a>
		</td>
	  </tr>
      <?php
		}
	}
	else {
    ?>
    <tr>
   		<td colspan="6" class="error" align="center">No file found.</td>
   	</tr>
<?php
	}
?>
  <tr>
	<td colspan="5">
		<table border="0" width="100%" cellspacing="0"  cellpadding="5">
			<tr>
				<td>&nbsp; </td>
			</tr>
<?php
			if($sort>0) $sorttext = '&sort='.$_GET['sort'];
			else $sorttext = '';
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
				$firstlink = "[<a href=\"{$_SERVER['PHP_SELF']}?p=0$sorttext\">First</a>]";
				$prevlink = "<a href=\"{$_SERVER['PHP_SELF']}?p=$prev$sorttext\">&laquo; Prev $limiter</a>";
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
					$middlelinks .= "<a href=\"{$_SERVER['PHP_SELF']}?p=$startnum$sorttext\">".($i+1)."</a> ";
				}
				else $middlelinks .= ($i+1).' ';
			}
			//next and last if not on last
			if($limstart<$rowcnt && (($limstart+($limiter*2))<$rowcnt || ($rowcnt - ($limstart + $limiter))>0)){
				$next = $limstart + $limiter;
				$nextlink = "<a href=\"{$_SERVER['PHP_SELF']}?p=$next$sorttext\">Next $limiter &raquo;</a>";
				$lastlink = "[<a href=\"{$_SERVER['PHP_SELF']}?p=".(($numbers-1)*$limiter)."$sorttext\">Last</a>]";
			}
			
			if($middlelinks!='') $middlelinks = "[ $middlelinks ] &nbsp;";
			print "<tr><td align=\"center\" class=\"bodytext\">$firstlink &nbsp; $prevlink &nbsp; $middlelinks $nextlink &nbsp; $lastlink</td></tr>";
			print "<tr><td align=\"center\" class=\"bodytext\">Showing results ".($limstart+1)." to ";
			if($limstart+$limiter < $rowcnt) print ($limstart+$limiter);
			else print $rowcnt;
			print " of $rowcnt</td></tr>";
?>
		</table>
	</td>
	</tr>
</table>
<input type="hidden" name="active" value="0" />
<input type="hidden" name="deletebut" value="0" />
</form>
<script type="text/javascript">
function setAll(){
	if(document.deleteform.setUnset.value == 'on'){
		for(i=1;i<document.deleteform.elements.length;i++){
			document.deleteform.elements[i].checked = true;
		}
		document.deleteform.setUnset.value = '';
	}
	else{
		for(i=1;i<document.deleteform.elements.length;i++){
			document.deleteform.elements[i].checked = false;
		}
		document.deleteform.setUnset.value = 'on';
	}
}

function deleteCheck(){
	var x = 0;
	for(var i=0; i<document.deleteform.elements.length;i++) {
		if(document.deleteform.elements[i].checked) {
			x = 1;
			break;
		}
	}
	if(x==0) {
		alert("Please select at least one record to delete.");
	}
	else {
		if(confirm('Are you sure you want to delete?')){
			document.deleteform.deletebut.value = 1;
			document.deleteform.submit();
		}
	}
}

$(document).ready(function(){
	$('.deleteFile').on('click', function(){
		var ID = $(this).attr('value');
		if(confirm('Are you sure you want to delete?')){
			window.location.href = "<?php echo $_SERVER['PHP_SELF'];?>"+'?deletebut=1&delID='+ID;
		}
	});
})

/*function check_searchform(){
	var search = document.searchForm.search_text.value = trimspace(document.searchForm.search_text.value);
	if(search == "") {
		alert("Please enter some value to search");
		document.searchForm.search_text.focus();
		return false;
	}
	return true;
}*/
</script>
<?php
include 'bottom.php';
?>