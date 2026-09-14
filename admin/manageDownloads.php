<?php
$ALLOW_GROUPS = array(22);
require_once("../auth_auth.php");
include 'top.php';
?>
<table width="100%" border="0" cellspacing="0" cellpadding="5" class="text">
<tr><td class="adminhead" align="center" colspan="3">COMPETISCAN DOWNLOAD MANAGEMENT</td></tr>
</table>
<?php 

$root = dirname(__FILE__);
$root = substr($root,0,strpos($root,'/admin'));
$track_delete_data=array();
$emailData = [];
if(isset($_REQUEST['del'])){
	$dl_id = (int)$_REQUEST['del'];
	
	$sql = "DELETE FROM cscan_download WHERE dl_id=".$dl_id;
        /* Added for track on delete operation */
                        
            $track_delete_data=array();       

            $track_delete_data = [
                    'auth_id' => $GLOBALS['AUTH_DATA']['userID'],
                    'auth_name' => authUserName($GLOBALS['AUTH_DATA']['userID']),
                    'deleted_id' => (int)$dl_id,
                    'sql_query' => $sql,
                    'ip_address' => ipAddress(),
                    'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                    'delete_type' => 'Manage Downloads',
                    'is_mobile' => isMobile(),
                    'insert_date' => date("Y-m-d H:i:s")
                ];
            trackDelete($track_delete_data);
            $emailData[] = $track_delete_data;

        /*END  Added for track on delete operation*/
	$DRW->query($sql,$DRW_main);
        /* Added for track on delete */
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
            sendDevAlert('Caution! Data Deleted From Manage Downloads',$html);
        }
    /*END  Added for track on delete */
	
	ob_end_clean();
	header("Location: {$_SERVER['PHP_SELF']}");
	exit;
}


if(isset($_REQUEST['dl_id'])){
	$dl_id = (int)$_REQUEST['dl_id'];
	if(isset($_POST['send'])){
		$dl_url = trim($_POST['dl_url']);
		if(strpos($dl_url,'http://')===false && strpos($dl_url,'https://')===false) {
			$dl_url = 'http://'.$dl_url;
		}
		if($dl_id ==0){
			$sql = "INSERT INTO cscan_download (dl_sortdate,dl_desc,dl_name,dl_url) 
				VALUES ('".$DRW->real_escape_string($_POST['dl_sortdate'])."','".$DRW->real_escape_string($_POST['dl_desc'])."','".$DRW->real_escape_string($_POST['dl_name'])."','".$DRW->real_escape_string($dl_url)."')";
			$DRW->query($sql,$DRW_main);
			$dl_id = $DRW->insert_id($DRW_main);
			$sql = "UPDATE cscan_download SET dl_md5_id='".$DRW->real_escape_string(md5($dl_id))."' WHERE dl_id=".$dl_id;
			$DRW->query($sql,$DRW_main);
		}
		else{
			$sql = "UPDATE cscan_download SET dl_sortdate='".$DRW->real_escape_string($_POST['dl_sortdate'])."',dl_desc='".$DRW->real_escape_string($_POST['dl_desc'])."',dl_name='".$DRW->real_escape_string($_POST['dl_name'])."',dl_url='".$DRW->real_escape_string($dl_url)."'
				WHERE dl_id=".$dl_id;
			$DRW->query($sql,$DRW_main);
		}
		
		ob_end_clean();
		header("Location: {$_SERVER['PHP_SELF']}");
		exit;
	}
	if($dl_id!=0){
		$sql_ma = "SELECT dl_name,dl_desc,dl_url,dl_sortdate FROM cscan_download WHERE dl_id=".$dl_id;
		$rs_ma = $DRW->query($sql_ma,$DRW_read);
		$row_ma = $DRW->fetch_row($rs_ma);
		$dl_name = $row_ma[0];
		$dl_desc = $row_ma[1];
		$dl_url = $row_ma[2];
		$dl_sortdate = $row_ma[3];
	}
	else{
		$dl_name = '';
		$dl_desc = '';
		$dl_url = '';
		$dl_sortdate = date('Y-m-d');
	}
	?>
	<form method="post" name="frm1" action="<?php echo $_SERVER['PHP_SELF'].'?dl_id='.$dl_id; ?>">
	<table border="0" cellspacing="0" cellpadding="5" class="text">
	<tr>
	<td class="adminhead">Date</td>
	<td class="bodytext"><input type="text" name="dl_sortdate" size="40" maxlength="255" class="input_box" value="<?php echo htmlspecialchars($dl_sortdate,ENT_QUOTES); ?>" /></td>
	</tr>
	<tr>
	<td class="adminhead">Name</td>
	<td class="bodytext"><input type="text" name="dl_name" size="40" maxlength="255" class="input_box" value="<?php echo htmlspecialchars($dl_name,ENT_QUOTES); ?>" /></td>
	</tr>
	<tr>
	<tr>
	<td class="adminhead" valign="top">Description</td>
	<td class="bodytext"><textarea name="dl_desc" rows="5" cols="60" class="input_box"><?php echo htmlspecialchars($dl_desc); ?></textarea></td>
	</tr>
	<tr>
	<td class="adminhead">URL</td>
	<td class="bodytext"><input type="text" name="dl_url" size="80" class="input_box" value="<?php echo htmlspecialchars($dl_url,ENT_QUOTES); ?>" /></td>
	</tr>
	<tr>
	<td class="adminhead">&nbsp;</td>
	<td><input class="button" type="submit" name="submit1" value="Save" /> &nbsp; <input class="button" type="submit" name="submit2" value="Cancel" onclick="document.location.href='<?php echo $_SERVER['PHP_SELF']; ?>'; return false;" /></td>
	</tr>
	</table>
	<input type="hidden" name="send" value="1" /></form>
	<?php 
}
else {
	?>
	<table border="0" cellspacing="0" cellpadding="5" class="text">
	<tr>
	<td colspan="3"><strong>Note</strong>: Click any of the following to manage the download.</td>
	<td align="right"><input class="button" type="submit" name="submit2" value="Add" onclick="document.location.href='<?php echo $_SERVER['PHP_SELF']; ?>?dl_id=0'; return false;" /></td>
	</tr>
	<tr>
	<td class="adminhead">Date</td>
	<td class="adminhead">Name/Description</td>
	<td class="adminhead">File</td>
	<td class="adminhead">&nbsp;</td>
	</tr>
	<?php
        $className='';
        $sql = "SELECT dl_id,dl_name,dl_desc,dl_url,dl_sortdate,dl_md5_id FROM cscan_download ORDER BY dl_sortdate DESC,dl_name ASC";
        $rs = $DRW->query($sql,$DRW_read);
        while($row = $DRW->fetch_row($rs)) {
            if ($className=='selected-bg'){
                    $className='white-bg';
            }
            else {
                 $className='selected-bg';
            }
	?>
            <tr class="<?php echo $className;?>">
                <td class="bodytext" valign="top"><?php echo $row[4]; ?></td>
                <td class="bodytext" valign="top"><?php echo htmlspecialchars($row[1]); ?><hr /><?php echo nl2br(htmlspecialchars($row[2])); ?></td>
                <td class="bodytext" valign="top"><?php echo '<em>'.htmlspecialchars($row[3])."</em><hr /><strong>http://{$_SERVER['HTTP_HOST']}/downloads.php?id=$row[5]</strong>"; ?></td>
                <td class="bodytext" valign="top" align="right"><?php 
                if(!empty($row[3])){
                    echo "<a href=\"../downloads.php?id=$row[5]\" target=\"_blank\">Download</a><br />";
                }
                echo "<a href=\"{$_SERVER['PHP_SELF']}?dl_id=$row[0]\">Edit</a><br />";
                if(checkGroup(76)){
                    echo "<a href=\"{$_SERVER['PHP_SELF']}?del=$row[0]\" onclick=\"return confirm('Delete?');\">Delete</a>"; 
                }
                ?>
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
