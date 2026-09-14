<?php
$ALLOW_GROUPS = array(40);
require_once("../auth_auth.php");
include 'top.php';
?>
<table width="100%" border="0" cellspacing="0" cellpadding="5" class="text">
  <tr><td class="adminhead" align="center" colspan="3">COMPETISCAN REPORT MANAGEMENT</td></tr>
</table>
<?php 

$root = dirname(__FILE__);
$root = substr($root,0,strpos($root,'/admin'));

if(isset($_REQUEST['del'])){
	$fid = (int)$_REQUEST['del'];
	
	$query2 = "SELECT fid_path FROM cscan_report WHERE fid=".$fid;
	$query_result2 = $DRW->query($query2,$DRW_read);
	$data2 = $DRW->fetch_row($query_result2);
	$document_path = $data2[0];
	#################################### Start S3 Implementation Code ###########################################
	/*if(is_file($root.$document_path) && !empty($document_path) && $document_path!='/'){
		unlink($root.$document_path);
	}*/
	$mystring = $document_path;
	$path = preg_match("/^\//", $mystring);
	if ($path == 1)
	{
	    $full_path = substr($document_path,1);
	}
	else 
	{
	    $full_path = $document_path;
	}
	$result = $s3->deleteObject([
				'Bucket' => $bucket_name,
				'Key' => $full_path,
            ]);
	#################################### End S3 Implementation Code ###########################################
	$emailData = [];
	$sql = "DELETE FROM cscan_report WHERE fid=".$fid;
        if($DRW->query($sql,$DRW_main)){
            $data = [
                'auth_id' => $GLOBALS['AUTH_DATA']['userID'],
                'auth_name' => authUserName($GLOBALS['AUTH_DATA']['userID']),
                'deleted_id' => $fid,
                'sql_query' => $sql,
                'ip_address' => ipAddress(),
                'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                'delete_type' => 'Client Reports',
                'is_mobile' => isMobile(),
                'insert_date' => date("Y-m-d H:i:s")
            ];
            trackDelete($data);
            $emailData[] = $data;
        }
	if(count($emailData)>0){
            $html = '<table width="100%" border="1">';
            $html .= '<tr><td>Auth ID</td><td>User Name</td><td>Deleted ID</td><td>SQL Query</td><td>IP Address</td><td>User Agent</td><td>Module</td><td>Is Mobile</td><td>Deleted Date (CST)</td></tr>';

            foreach($emailData as $tr){
                if(is_array($tr) && count($tr)>0){
                   $html .= '<tr>';
                   foreach($tr as $td){
                       $html .= '<td>'.$td.'</td>'; 
                   }
                   $html .= '</tr>';
                }
            }                    
            $html .= '</table>';

            sendDevAlert('Caution! Data Deleted From Client Reports',$html);
        }
	ob_end_clean();
	header("Location: {$_SERVER['PHP_SELF']}");
	exit;
}

if(isset($_REQUEST['fid'])){
	$fid = (int)$_REQUEST['fid'];
	if(isset($_POST['send'])){
		$maxbytes = 500000;
		$file = '';
		$fid_filename = '';
		$fid_content_type = '';
		$fid_content_length = 0;
		$update = '';
		
		if($_FILES["fidFile"]['error'] == UPLOAD_ERR_OK && is_uploaded_file($_FILES["fidFile"]['tmp_name'])){
			$file = $_FILES['fidFile']['tmp_name'];
			$fid_filename = $_FILES['fidFile']['name'];
			$fid_content_type = $_FILES['fidFile']['type'];
			$fid_content_length = $_FILES['fidFile']['size'];
			$update = ",fid_createddate=NOW(),fid_filename='".$DRW->real_escape_string($fid_filename)."',fid_content_type='".$DRW->real_escape_string($fid_content_type)."',fid_content_length='".$DRW->real_escape_string($fid_content_length)."'";
		}
		
		if($fid ==0){
			$sql = "INSERT INTO cscan_report (fid_createddate,fid_desc,fid_name,fid_createdby,fid_filename,fid_content_type,fid_content_length,fid_sortdate) 
				VALUES (NOW(),'".$DRW->real_escape_string($_POST['fid_desc'])."','".$DRW->real_escape_string($_POST['fid_name'])."','".$DRW->real_escape_string($AUTH_DATA['userID'])."',
				'".$DRW->real_escape_string($fid_filename)."','".$DRW->real_escape_string($fid_content_type)."','".$DRW->real_escape_string($fid_content_length)."','".$DRW->real_escape_string($_POST['fid_sortdate'])."')";
			$DRW->query($sql,$DRW_main);
			$fid = $DRW->insert_id($DRW_main);
		}
		else{
			$sql = "UPDATE cscan_report SET fid_desc='".$DRW->real_escape_string($_POST['fid_desc'])."',fid_name='".$DRW->real_escape_string($_POST['fid_name'])."',fid_createdby='".$DRW->real_escape_string($AUTH_DATA['userID'])."',fid_sortdate='".$DRW->real_escape_string($_POST['fid_sortdate'])."'
				$update
				WHERE fid=".$fid;
			$DRW->query($sql,$DRW_main);
		}

		#################################### Start S3 Implementation Code ###########################################
		if($file!=''){
			$yearpath = date('Y/');
			$fid_path = 'reports/'.$yearpath;
			if(!is_dir($root.$fid_path)){
				mkdir($root.$fid_path,02755);
			}
			$ext = explode('.',$fid_filename);
			$ext = $ext[count($ext)-1];
			//print_r($fid_path.$fid.'.'.$ext);die;
			/*if(!empty($_POST['fid_ext']) && $ext!=$_POST['fid_ext'] && is_file($root.$fid_path.$fid.'.'.$_POST['fid_ext'])){
				@unlink($root.$fid_path.$fid.'.'.$_POST['fid_ext']);
			}
			move_uploaded_file($file, $root.$fid_path.$fid.'.'.$ext))*/
			$result = $s3->putObject([
		        'Bucket' => $bucket_name,
		        'Key'    => $fid_path.$fid.'.'.$ext,
		        'SourceFile' => $file,
		        'ACL'    => 'public-read',
		        'ContentType'   => $fid_content_type,
		        'Metadata'      => array(
		           'string'        => 'string'
		         )
		    ]);
			
			if(isset($result['@metadata']['statusCode']) && $result['@metadata']['statusCode'] == 200){
				$sql = "UPDATE cscan_report SET fid_path='".$DRW->real_escape_string($fid_path.$fid.'.'.$ext)."' WHERE fid=".
				$fid;
				$DRW->query($sql,$DRW_main);
			}
		}
		#################################### End S3 Implementation Code ###########################################
		
		ob_end_clean();
		header("Location: {$_SERVER['PHP_SELF']}");
		exit;
	}
	if($fid!=0){
		$sql_ma = "SELECT DATE_FORMAT(fid_createddate,'%m/%d/%Y %l:%i %p'),fid_desc,fid_name,fid_createdby,fid_filename,fid_content_type,fid_content_length,fid_sortdate FROM cscan_report WHERE fid=".$fid;
		$rs_ma = $DRW->query($sql_ma,$DRW_read);
		$row_ma = $DRW->fetch_row($rs_ma);
		$fid_createddate = $row_ma[0];
		$fid_desc = $row_ma[1];
		$fid_name = $row_ma[2];
		$fid_createdby = $row_ma[3];
		$fid_filename = $row_ma[4];
		$fid_content_type = $row_ma[5];
		$fid_content_length = $row_ma[6];
		$fid_sortdate = $row_ma[7];
		$ext = explode('.',$fid_filename);
		$fid_ext = $ext[count($ext)-1];
	}
	else{
		$fid_createddate = date('Y-m-d');
		$fid_desc = '';
		$fid_name = '';
		$fid_createdby = $AUTH_DATA['userID'];
		$fid_filename = '';
		$fid_content_type = '';
		$fid_content_length = 0;
		$fid_sortdate = date('Y-m-d');
		$fid_ext = '';
	}
	?>
	<form method="post" name="frm1" action="<?php echo $_SERVER['PHP_SELF'].'?fid='.$fid; ?>" enctype="multipart/form-data">
	<table border="0" cellspacing="0" cellpadding="5" class="text">
	    <tr>
			<td class="adminhead">Date</td>
			<td class="bodytext"><input type="text" name="fid_sortdate" size="40" maxlength="255" class="input_box" value="<?php echo htmlspecialchars($fid_sortdate,ENT_QUOTES); ?>" /></td>
		</tr>
	    <tr>
			<td class="adminhead">Name</td>
			<td class="bodytext"><input type="text" name="fid_name" size="40" maxlength="255" class="input_box" value="<?php echo htmlspecialchars($fid_name,ENT_QUOTES); ?>" /></td>
		</tr>
	    <tr>
	    <tr>
			<td class="adminhead" valign="top">Description</td>
			<td class="bodytext"><textarea name="fid_desc" rows="5" cols="60" class="input_box"><?php echo htmlspecialchars($fid_desc); ?></textarea></td>
		</tr>
	<?php
	if($fid_content_length>0){
		echo '<tr><td class="adminhead" valign="top">Current File</td><td class="bodytext">'.htmlspecialchars($fid_filename).' ('.$fid_createddate.')</td></tr>';
	}
	?>
	    <tr>
			<td class="adminhead" valign="top"><?php if($fid_content_length>0) echo 'New '; ?>File</td>
			<td class="bodytext"><input type="file" class="input_box" name="fidFile" size="40" /></td>
		</tr>
	  <tr>
	    <td class="adminhead">&nbsp;</td>
	    <td><input class="button" type="submit" name="submit1" value="Save" /> &nbsp; <input class="button" type="submit" name="submit2" value="Cancel" onclick="document.location.href='<?php echo $_SERVER['PHP_SELF']; ?>'; return false;" /></td>
	  </tr>
	</table>
	<input type="hidden" name="fid_ext" value="<?php echo htmlspecialchars($fid_ext); ?>" /><input type="hidden" name="send" value="1" /></form>
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
			<td class="adminhead">Name/Description</td>
			<td class="adminhead">File</td>
			<td class="adminhead">&nbsp;</td>
		</tr>
	<?php
		$className='';
		$sql = "SELECT fid,DATE_FORMAT(fid_createddate,'%m/%d/%Y %l:%i %p'),fid_name,fid_content_length,fid_filename,fid_sortdate,fid_desc FROM cscan_report ORDER BY fid_sortdate DESC,fid_name ASC";
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
		<td class="bodytext" valign="top"><?php echo htmlspecialchars($row[2]); ?><hr /><?php echo nl2br(htmlspecialchars($row[6])); ?></td>
		<td class="bodytext" valign="top"><?php echo htmlspecialchars($row[4])." ($DisplaySize)<br /><em>{$_SERVER['HTTP_HOST']}/cr.php?fid=$row[0]</em>"; ?></td>
		<td class="bodytext" valign="top" align="right"><?php 
		if($row[3]>0){
			echo "<a href=\"../cr.php?fid=$row[0]\">Download</a><br />";
		}
                echo "<a href=\"{$_SERVER['PHP_SELF']}?fid=$row[0]\">Edit</a><br />";
                    if(checkGroup(72)){
                        echo "<a href=\"{$_SERVER['PHP_SELF']}?del=$row[0]\" onclick=\"return confirm('Delete?');\">Delete</a>";
                    }?>
                    </td>
		</tr>
	<?php
		}
	?>
	</table>
	<?php 
}
include 'bottom.php';
?>