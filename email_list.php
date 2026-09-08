<?php
require_once('includes/globalSession.php');
require_once('includes/checklogin.php');

if(isset($_REQUEST['emailtext'])) {
	$emailtext = trim($_REQUEST['emailtext']);
}
else {
	$emailtext = '';
}

$send_mode = (isset($_REQUEST['send_mode'])) ? (int)$_REQUEST['send_mode'] : 1;

if (($_SESSION['sess_plevel']==1 || $_SESSION['sess_plevel']==2) && $send_mode == 2) {//also change in sendLink.php
	$show_cc = true;
}
else{
	$show_cc = false;
}
if($show_cc) {
	$sendTypes = array('to','cc','bcc');//change in emails_iframe.js sendLink.js and email_list_save.php
}
else{
	$sendTypes = array('to');
}
$emailsel = array();
foreach($sendTypes as $st){
	if(isset($_REQUEST['emailsel'.$st])) {
		$emailsel[$st] = $_REQUEST['emailsel'.$st];
	}
	else {
		$emailsel[$st] = '';
	}
}
if(isset($_REQUEST['showall'])) {
	$showall = true;
}
else {
	$showall = false;
}
if(isset($_REQUEST['showlist'])) {
	$showlist = true;
}
else {
	$showlist = false;
}

if($showlist){
	$sql = "SELECT emailto_name,emailto_list,emailto_id,emailcc_list,emailbcc_list FROM cscan_emailto_list WHERE userID='{$_SESSION['sess_userID']}' ORDER BY emailto_name";	
	$rs = $DRW->query($sql,$DRW_read);
	$rows = $DRW->num_rows($rs);
	if($rows>0){
		$i = 0;
		print "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"bodytext\">";
		while($data = $DRW->fetch_row($rs)){
			$emailto_list = array();
			$emailto_name = $data[0];
			$emailto_list['to'] = $data[1];
			$emailto_list_valid['to'] = array();
			$emailto_id = $data[2];
			$emailto_list['cc'] = $data[3];
			$emailto_list_valid['cc'] = array();
			$emailto_list['bcc'] = $data[4];
			$emailto_list_valid['bcc'] = array();
			$in = false;
			foreach($sendTypes as $st){
				$emailArray = getEmailParse($emailto_list[$st]);
				foreach ($emailArray as $id => $arry) {
					$count = 0;
					if ($_SESSION['sess_plevel']==2 || $send_mode == 2) {
						$query2 = "SELECT COUNT(*) FROM cscan_users WHERE (companyName='".$DRW->real_escape_string($_SESSION['sess_companyName'])."' OR plevel>0) AND active='y' AND emailAddress='".$DRW->real_escape_string($emailArray[$id]['address'])."'";
						$result2 = $DRW->query($query2,$DRW_read);
						$data2 = $DRW->fetch_row($result2);
						$count = $data2[0];
					}
					elseif($_SESSION['sess_plevel']==1){
						$count = 1;
					}
					else{
						$query2 = "SELECT COUNT(*) FROM cscan_users WHERE companyName='".$DRW->real_escape_string($_SESSION['sess_companyName'])."' AND active='y' AND emailAddress='".$DRW->real_escape_string($emailArray[$id]['address'])."'";
						$result2 = $DRW->query($query2,$DRW_read);
						$data2 = $DRW->fetch_row($result2);
						$count = $data2[0];
					}
					if($count>0){
						$emailto_list_valid[$st][] = $emailArray[$id]['address'];
						$in = true;
					}
				}
			}
			if($in){
				print "<tr><td valign=\"top\">".htmlspecialchars($emailto_name, ENT_QUOTES)."&nbsp;</td><td valign=\"top\">(<a href=\"#\" onclick=\"doSavedEmails('".htmlspecialchars(addslashes(implode(',',$emailto_list_valid['to'])), ENT_QUOTES)."','".htmlspecialchars(addslashes(implode(',',$emailto_list_valid['cc'])), ENT_QUOTES)."','".htmlspecialchars(addslashes(implode(',',$emailto_list_valid['bcc'])), ENT_QUOTES)."'); return false;\" class=\"HyperLink\">Insert</a> | <a href=\"#\" onclick=\"iframe_saveList('$emailto_id','".htmlspecialchars(addslashes($emailto_name), ENT_QUOTES)."','".htmlspecialchars(addslashes(implode(',',$emailto_list_valid['to'])), ENT_QUOTES)."','".htmlspecialchars(addslashes(implode(',',$emailto_list_valid['cc'])), ENT_QUOTES)."','".htmlspecialchars(addslashes(implode(',',$emailto_list_valid['bcc'])), ENT_QUOTES)."'); return false;\" class=\"HyperLink\">Edit</a> | <a href=\"#\" onclick=\"deleteName($emailto_id); return false;\" class=\"HyperLink\">Delete</a>)</td></tr>";
				$i++;
			}
		}
		if($i>4) print "<tr><td>&nbsp;</td><td>[<a href=\"#\" class=\"HyperLink\">Top</a>]</td></tr>";
		print "</table>";
	}
}
else{
	$emailArray = array();
	$emailshowArray = array();
	foreach($sendTypes as $st){
		$emailselArray = getEmailParse($emailsel[$st]);
		foreach($emailselArray as $id => $arry){
			$lemail = strtolower($emailselArray[$id]['address']);
			$ind = array_search($lemail,$emailArray);
			if($ind===false){
				if($emailselArray[$id]['name']!=''){
					$name = $lemail.' ('.str_replace('"','\\"',$emailselArray[$id]['name']).')';
				}
				else{
					$sql = "SELECT firstName,lastName FROM cscan_users WHERE active='y' AND emailAddress='".$DRW->real_escape_string($lemail)."'";	
					$rs = $DRW->query($sql,$DRW_read);
					$data = $DRW->fetch_row($rs);
					if($data[0]!='' || $data[1]!=''){
						$name = $data[0];
						if($data[1]!=''){
							if($name!='') {
								$name .= ' ';
							}
							$name .= $data[1];
						}
						$name = $lemail.' ('.str_replace('"','\\"',$name).')';
					}
					else{
						$name = $lemail;
					}
				}
				$emailshowArray[] = array('name'=>$name,'types'=>array($st));
				$emailArray[] = $lemail;
			}
			else{
				$emailshowArray[$ind]['types'][] = $st;
			}
		}
	}
	
	$sql = "SELECT emailAddress,firstName,lastName FROM cscan_users WHERE active='y'";// AND userID<>'".$_SESSION['sess_userID']."'
	if($_SESSION['sess_plevel']==2 || $send_mode == 2) {
		$sql .= " AND (companyName='".$DRW->real_escape_string($_SESSION['sess_companyName'])."' OR plevel>0)";
	}
	elseif($_SESSION['sess_plevel']!=1){
		$sql .= " AND companyName='".$DRW->real_escape_string($_SESSION['sess_companyName'])."'";
	}
	if($emailtext!='') {
		$val = mysqlLike($emailtext);
		if(strlen($val)>2) {
			$firstpct = '%';
		}
		else {
			$firstpct = '';
		}
		$sql .= " AND (emailAddress LIKE '$firstpct$val%' OR firstName LIKE '$firstpct$val%' OR lastName LIKE '$firstpct$val%')";
	}
	$sql .= " ORDER BY emailAddress";
	
	ob_clean();
	$i = 0;
	$rs = $DRW->query($sql,$DRW_read);
	$rows = $DRW->num_rows($rs);
	if((($emailtext!='' || $showall) && $rows>0) || count($emailArray)>0){
		if($rows>4) {
			$size = 4;
		}
		else {
			$size = $rows;
		}
		
		echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"bodytext\">";
		if($show_cc) {
			echo "<tr><td valign=\"top\" style=\"font-size:smaller;\">To&nbsp;</td><td valign=\"top\" style=\"font-size:smaller;\">Cc&nbsp;</td><td valign=\"top\" style=\"font-size:smaller;\">Bcc&nbsp;</td><td>&nbsp;</td></tr>";
		}
		
		if($emailtext!='' || $showall) {
			while($data = $DRW->fetch_row($rs)) {
				$newemail_address = strtolower($data[0]);
				$newemail_name = $data[1];
				if(!in_array($newemail_address,$emailArray)){
					if($data[2]!=''){
						if($newemail_name!='') {
							$newemail_name .= ' ';
						}
						$newemail_name .= $data[2];
					}
					if($newemail_name!=''){
						//$show = '"'.str_replace('"','\\"',$newemail_name).'" <'.$newemail_address.'>';
						$show = $newemail_address.' ('.$newemail_name.')';
					}
					else {
						$show = $newemail_address;
					}
					echo '<tr>';
					foreach($sendTypes as $st){
						echo "<td valign=\"top\"><input type=\"checkbox\" name=\"emailsel".$st."[]\" value=\"".htmlspecialchars($newemail_address, ENT_QUOTES)."\" onclick=\"doEmails();\" id=\"em".$st."$i\" /></td>";
					}
					echo "<td";
					if($data[0]==$_SESSION['sess_username']) {
						echo ' style="font-style:italic;"';
					}
					echo "><label for=\"emto$i\">".htmlspecialchars($show, ENT_QUOTES)."</label></td></tr>";
					$i++;
				}
			}
		}
		
		foreach($emailArray as $currkey=>$em){
			echo "<tr>";
			foreach($sendTypes as $st){
				echo "<td valign=\"top\"><input type=\"checkbox\" name=\"emailsel".$st."[]\" value=\"".htmlspecialchars($em, ENT_QUOTES)."\" onclick=\"doEmails();\" id=\"em".$st."$i\"";
				if(in_array($st,$emailshowArray[$currkey]['types'])){
					echo " checked=\"checked\"";
				}
				echo " /></td>";
			}
			echo "<td";
			if($em==$_SESSION['sess_username']) {
				echo ' style="font-style:italic;"';
			}
			echo "><label for=\"emto$i\">".htmlspecialchars($emailshowArray[$currkey]['name'], ENT_QUOTES)."</label></td>";
			echo "</tr>";
			$i++;
		}
		
		if($i>4) {
			echo "<tr><td colspan=\"3\">&nbsp;</td><td>[<a href=\"#\" class=\"HyperLink\">Top</a>]</td></tr>";
		}
		echo "</table>";
	}
}
