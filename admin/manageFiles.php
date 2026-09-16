<?php
$ALLOW_GROUPS = array(30);
require_once("../auth_auth.php");
include 'top.php';
?>
<table width="100%" border="0" cellspacing="0" cellpadding="5" class="text">
  <tr><td class="adminhead" align="center" colspan="3">COMPETISCAN FILE MANAGEMENT</td></tr>
</table>
<?php 

$TEMP_UPLOAD_PATH = '/home/competiscan/temp/';
$TARGET_UPLOAD_PATH = '/home/competiscan/downloads/';

if(isset($_REQUEST['fid'])){
	$fid = (int)$_REQUEST['fid'];
	if(!empty($_POST['upload_id'])){
		$upload_id = (float)$_REQUEST['upload_id'];
		if(!empty($upload_id)){
			$tempdir = $TEMP_UPLOAD_PATH.$upload_id;
			if(is_dir($tempdir)) {
				if($handle = opendir($tempdir)) {
					while (false !== ($file = readdir($handle))) {
						if ($file != "." && $file != ".." && $file!='flength' && $file!='fname' && $file!='ferror') {
							$document_filename = $file;
							$tmp_filename = $tempdir.'/'.$document_filename;
							break;
						}
					}
					closedir($handle);
				}
			}
			if(!empty($tmp_filename) && is_file($tmp_filename)){
				rename ($tmp_filename, $TARGET_UPLOAD_PATH.$document_filename);
			}
		}
		
		ob_end_clean();
		header("Location: {$_SERVER['PHP_SELF']}");
		exit;
	}
	$xuploadUID = $AUTH_DATA['userID'].time();
	?>
	<form method="post" name="frm1" action="../cgi-bin/large_upload.cgi?upload_id=<?php echo $xuploadUID; ?>" enctype="multipart/form-data" onsubmit="StartUpload();" target="xupload">
	<table border="0" cellspacing="0" cellpadding="5" class="text">
	    <tr>
			<td class="adminhead" valign="top"><?php if($fid_content_length>0) echo 'New '; ?>File</td>
			<td class="bodytext"><input type="file" class="input_box" name="fidFile" size="40" />
			
			<div id="statbarouter" style="display:none;width:100px; height:20px; border:solid 1px #434d5c;" ><div id="statbar" style="width:1%;height:100%; background-color: #434d5c;">&nbsp;</div></div>
			
			<iframe id="xupload" name="xupload" src="../blank.html" style="display: none;"></iframe>
			<script type="text/javascript">
			<!--
			var fname = '';
			var total = '0';
			var ftime = '0';
			var size = '0';
			var tries = 15;
			var tryer = 0;
			function StartUpload(){
				startTimer('updater()',1000);
				$("#statbar").css('width','1%');
				$("#statbarouter").css('display','block');
			}
			function updater(){
				$.post("large_upload_check.php", { upload_id: <?php echo $xuploadUID; ?> },
				function(data){
					var a = data.split('|');
					var new_fname = a[0];
					var new_total = a[1];
					var new_ftime = a[2];
					var new_size = a[3];
					var tmpfile = a[4];
					var is_error = a[5];
					if(new_ftime==ftime){
						tryer++;
					}
					else{
						tryer = 0;
					}
					if(new_fname==tmpfile || tryer==tries){
						$("#statbarouter").css('display','none');
						$("#file_link").css('display','inline');
						if(new_fname!=tmpfile || is_error==1){
							alert('Error: Please Try Again.');
							if(is_error==1){
								//$("#xupload").attr('src','blank.html');
								document.location.reload();
							}
						}
						else{
							$("#upload_id").val('<?php echo $xuploadUID; ?>');
							
							//// submit to self
						}
					}
					else{
						var percent = 0;
						if(total>0){
							percent = parseInt((size/total)*100);
						}
						if(percent<1){
							percent = 1;
						}
						else if(percent>100){
							percent = 100;
						}
						$("#statbar").css('width',percent+'%');
						fname = new_fname;
						total = new_total;
						ftime = new_ftime;
						size = new_size;
						startTimer('updater()',500);
					}
				});
			}
			//-->
			</script>
			</td>
		</tr>
	  <tr>
	    <td class="adminhead">&nbsp;</td>
	    <td><input class="button" type="submit" name="submit1" value="Save" /> &nbsp; <input class="button" type="submit" name="submit2" value="Cancel" onclick="document.location.href='<?php echo $_SERVER['PHP_SELF']; ?>'; return false;" /></td>
	  </tr>
	</table>
	<input type="hidden" name="upload_id" id="upload_id" value="<?php echo $xuploadUID; ?>" /></form>
	<?php 
}
else {
	?>
	<table border="0" cellspacing="0" cellpadding="5" class="text">
	  <tr>
	    <td colspan="3"><strong>Note</strong>: Click any of the following to manage the report.</td>
	    <td align="right"><input class="button" type="submit" name="submit2" value="Add" onclick="document.location.href='<?php echo $_SERVER['PHP_SELF']; ?>?fid=0'; return false;" /></td>
	  </tr>
	    <tr>
			<td class="adminhead">Date</td>
			<td class="adminhead">Name</td>
			<td class="adminhead">File</td>
			<td class="adminhead">&nbsp;</td>
		</tr>
	<?php
		$className='';
		$sql = "SELECT fid,DATE_FORMAT(fid_createddate,'%m/%d/%Y %l:%i %p'),fid_name,fid_content_length,fid_filename,fid_sortdate FROM cscan_report ORDER BY fid_sortdate DESC,fid_name ASC";
		$rs = $DRW->query($sql,$DRW_read);
		while($row = $DRW->fetch_row($rs)) {
			$sizeofPDFinKB=(int)$row[3]/1024;
			$sizeofPDFinMB=$sizeofPDFinKB/1024;
			if($sizeofPDFinMB<1) {
				$DisplaySize=round($sizeofPDFinKB,2)." KB";  
			}
			else {
				$DisplaySize=round($sizeofPDFinMB,2)." MB";  
			}
			
			if ($className=='selected-bg') $className='white-bg';
			else $className='selected-bg';
	?>
		<tr class="<?php echo $className;?>">
		<td class="bodytext" valign="top"><?php echo $row[5]; ?></td>
		<td class="bodytext" valign="top"><?php echo htmlspecialchars($row[2]); ?></td>
		<td class="bodytext" valign="top"><?php echo htmlspecialchars($row[4])." ($DisplaySize)<br /><em>{$_SERVER['HTTP_HOST']}/cr.php?fid=$row[0]</em>"; ?></td>
		<td class="bodytext" valign="top" align="right"><?php 
		if($row[3]>0){
			echo "<a href=\"../cr.php?fid=$row[0]\" target=\"_blank\">Download</a> &nbsp; ";
		}
		echo "<a href=\"{$_SERVER['PHP_SELF']}?fid=$row[0]\">Edit</a> &nbsp; <a href=\"{$_SERVER['PHP_SELF']}?del=$row[0]\" onclick=\"return confirm('Delete?');\">Delete</a>"; ?></td>
		</tr>
	<?php
		}
	?>
	</table>
	<?php 
}
include 'bottom.php';
?>
