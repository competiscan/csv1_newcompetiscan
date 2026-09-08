<?php 
$ALLOW_GROUPS = array(5);
ini_set( "memory_limit", "70M" );//33554432
require_once "../auth_auth.php";
include('../includes/clean.php');

header('Content-Type: text/html; charset=iso-8859-1');

if(isset($_GET['muid'])) $muid = preg_replace('/\\W+/','',$_GET['muid']);
else $muid = '';

if(isset($_REQUEST['hy'])){
	$hy = (int)$_REQUEST['hy'];
}
if(empty($hy)){
	$hy = '';
}

$onload = '';
if(isset($_POST['sendemail'])){
	include('Mail.php');
	include('Mail/mime.php');
	
	$forwardto = $_POST['forwardto'];
	$subjectto = $_POST['subjectto'];
	//$fromaddress = $_POST['from'];
	$fromaddress = 'share@competiscan.com';
	$messageto = "<p>".$_POST['messageto']."</p>";
	
	$attachments = '';
	$crlf = "\n"; //if you want to send the generated MIME message using Mail  then you have to set $crlf to "\n"
	$hdrs = array('From'=>$fromaddress,'Subject'=>$subjectto);
	if(isset($_POST['cc_me']) && $AUTH_DATA['user_email']!='') {
		$hdrs['Cc'] = $AUTH_DATA['user_email'];
		//$forwardto .= ','.$AUTH_DATA['user_email'];
	}
	if(isset($_POST['cc_to_alt'])) {
		foreach($_POST['cc_to_alt'] as $cc2){
			if(isset($hdrs['Cc'])) $hdrs['Cc'] .= ','.$cc2;
			else $hdrs['Cc'] = $cc2;
			//$forwardto .= ','.$_POST['cc_to_alt'];
		}
	}
	
	$mime = new Mail_mime($crlf);
	
	$sql = "SELECT COUNT(*) FROM `cscan_product_email` WHERE `muid`='".$DRW->real_escape_string($muid)."' AND isTmp=1";
	$result = $DRW->query( $sql,$DRW_read );
	$count = $DRW->fetch_row($result);
	$DRW->free_result($result);
	if($count[0]>0){
		$link = "http://{$_SERVER['HTTP_HOST']}/admin/addproduct.php?new=1&muid=$muid&isTmp=1";
		
		$mime->setTXTBody($_POST['messageto']."\n$link\n");
		
		$messageto .= '<p><a href="'.$link.'">'.$link.'</a></p>';
	}
	
	$qf = "SELECT `ceafpath`,`ceaftype` FROM `cscan_email_attach_file$hy` WHERE `muid`='".$DRW->real_escape_string($muid)."' AND isTmp=1";
	$query_resultf = $DRW->query($qf,$DRW_read);
	if($DRW->num_rows($query_resultf)>0){
		$messageto .= "<p>Files:</p>";
		while($dataf = $DRW->fetch_row($query_resultf)){
			//$mime->addAttachment($dataf[0],$dataf[1]);
			$bname = basename($dataf[0]);
			$messageto .= '<p><a href="'.$displays3URL.$dataf[0].'">'.$bname.'</a></p>';
			
			if($attachments!='') $attachments .= ', ';
			$attachments .= $bname;
		}
	}
	$DRW->free_result($query_resultf);
	
	$mime->setHTMLBody("<html><head><title>$subjectto</title></head><body>$messageto</body></html>");
	
	$body = $mime->get();
	$headers = $mime->headers($hdrs);
	
	$mail =& Mail::factory('mail','-f'.$fromaddress);
	$send = $mail->send($forwardto, $headers, $body);
	
	if(!PEAR::isError($send)) {
		$sql = "REPLACE INTO `cscan_email_forward$hy` SET `muid`='".$DRW->real_escape_string($muid)."',
			`forward_to`='".$DRW->real_escape_string($forwardto)."',`forward_from`='".$DRW->real_escape_string($fromaddress)."',
			`forward_subject`='".$DRW->real_escape_string($subjectto)."',`forward_message`='".$DRW->real_escape_string($messageto)."',
			`forward_attachments`='".$DRW->real_escape_string($attachments)."',isTmp=1";
		$DRW->query($sql,$DRW_main);
	}
	$onload = 'onload="doOnload();"';
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<title>Competiscan Email</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<script language="JavaScript" src="../includes/jsFunctions.js" type="text/JavaScript"></script> 
<script src="../includes/ajax.js" type="text/JavaScript"></script>
<link href="../includes/styleSheet.css" rel="stylesheet" type="text/css" />

<script type="text/javascript">
<!--
function doOnload(){
	if(!window.opener.closed){
		//window.opener.location.reload();
		window.opener.location.href = 'manage_tmp_product.php';
	}
	self.close();
}
//-->
</script>

</head>
<body style="margin:10px;" <?php print $onload; ?>>
<?php
if(isset($_POST['sendemail'])){
	print "<a href=\"#\" onclick=\"self.close(); return false;\">Close</a>";
}
else{
	$query2 = "SELECT `forward_to`,`forward_to_alt` FROM `cscan_filter` ORDER BY `filterdate` DESC LIMIT 1";
	$query_result2 = $DRW->query($query2,$DRW_read);
	$data2 = $DRW->fetch_row($query_result2);
	$forward_to = $data2[0];
	$forward_to_alt = $data2[1];
	$forward_to_altArray = explode(',',$forward_to_alt);
	$skey = array_search($AUTH_DATA['user_email'],$forward_to_altArray);
	if($skey!==false){
		unset($forward_to_altArray[$skey]);
		$forward_to_alt = implode(',',$forward_to_altArray);
	}
	
	$query2 = "SELECT `firstSeen`,tmp_priority FROM `cscan_product_email` WHERE `muid`='".$DRW->real_escape_string($muid)."' AND isTmp=1";
	$query_result2 = $DRW->query($query2,$DRW_read);
	$data2 = $DRW->fetch_row($query_result2);
	$firstSeen = $data2[0];
	$tmp_priority = $data2[1];

	$skip_forward_to_alt = false;
	if($forward_to_alt!='' && $firstSeen!='0000-00-00'){
		$firstSplit = explode('-',$firstSeen);
		$firsttime = mktime(0,0,0,$firstSplit[1],$firstSplit[2],$firstSplit[0]);
		$currtime = time();
		$days = 7;
		if($firsttime>$currtime || ($currtime-$firsttime)>(86400*$days)) {
			$forward_to .= ','.$forward_to_alt;
			$skip_forward_to_alt = true;
		}
	}
	
	$subject = "Info for Temp Product #$muid";
	if($tmp_priority) $subject = '[High Priority] '.$subject;
	
	print "<form method=\"post\" name=\"mailForm\" action=\"{$_SERVER['PHP_SELF']}?muid=$muid\" enctype=\"multipart/form-data\" onsubmit=\"return confirm('Send?');\">";
	print "<div class=\"section\"><table border=\"0\" cellpadding=\"4\" cellspacing=\"2\">";
	print "<tr><td valign=\"top\">Forward To:</td><td><input type=\"text\" name=\"forwardto\" size=\"50\" class=\"input_box\" value=\"".htmlspecialchars($forward_to,ENT_QUOTES)."\" />";
	if($AUTH_DATA['user_email']!='') print "<br /><label><input type=\"checkbox\" name=\"cc_me\" value=\"1\" />Cc: ".htmlspecialchars($AUTH_DATA['user_email'])."</label>";
	if(!$skip_forward_to_alt){
		//if($forward_to_alt!=$AUTH_DATA['user_email'] && $forward_to_alt!='') {
		foreach($forward_to_altArray as $ft){
			if(!empty($ft)){
				print "<br /><label><input type=\"checkbox\" name=\"cc_to_alt[]\" value=\"".htmlspecialchars($ft,ENT_QUOTES)."\" />Cc: ".htmlspecialchars($ft)."</label>";
			}
		}
	}
	print "</td></tr>";
	//print "<tr><td valign=\"top\">From:</td><td><input type=\"text\" name=\"from\" size=\"50\" class=\"input_box\" value=\"share@competiscan.com\" /></td></tr>";
	print "<tr><td valign=\"top\">Subject:</td><td><input type=\"text\" name=\"subjectto\" size=\"50\" class=\"input_box\" value=\"".htmlspecialchars($subject,ENT_QUOTES)."\" /></td></tr>";
	print "<tr><td valign=\"top\">Message:</td><td>";
	print "<textarea name=\"messageto\" rows=\"10\" cols=\"60\" class=\"input_box\"></textarea>";
	print "<tr><td>&nbsp;</td><td><input class=\"button\" type=\"submit\" name=\"sender\" value=\"Send\" /> &nbsp; <input class=\"button\" type=\"submit\" name=\"cancel\" value=\"Cancel\" onclick=\"self.close(); return false;\" /></td></tr>";
	print "</table></div>
	<input type=\"hidden\" name=\"sendemail\" value=\"1\" /></form>";
	
	$query2 = "SELECT `efid`,DATE_FORMAT(`efdate`,'%m/%d/%Y'),`forward_to`,`forward_from`,`forward_subject`,`forward_attachments`,`forward_message`	
		FROM `cscan_email_forward$hy` WHERE `muid`='".$DRW->real_escape_string($muid)."' AND isTmp=1 ORDER BY `efdate` ASC";
	$query_result2 = $DRW->query($query2,$DRW_read);
	if($DRW->num_rows($query_result2)>0){
		print "<div>&nbsp;</div><div style=\"border-top:solid 1px #000000;\"><table border=\"0\" cellpadding=\"4\" cellspacing=\"2\">";
		print "<tr><td valign=\"top\" class=\"bodytext\"><b>Date</b></td>
			<td valign=\"top\" class=\"bodytext\"><b>Forward To</b></td>
			<td valign=\"top\" class=\"bodytext\"><b>Subject</b></td>
			<td valign=\"top\" class=\"bodytext\"><b>Message</b></td>
			<td valign=\"top\" class=\"bodytext\"><b>Attachments</b></td></tr>";
		while($data2 = $DRW->fetch_row($query_result2)){
			$efid = $data2[0];
			$efdate = $data2[1];
			$forward_to_ef = $data2[2];
			$forward_from_ef = $data2[3];
			$forward_subject_ef = $data2[4];
			$forward_attachments_ef = $data2[5];
			$forward_message = $data2[6];
			if($forward_attachments_ef=='') $forward_attachments_ef = '&nbsp;';
			if($forward_message=='') $forward_message = '&nbsp;';
			//else $forward_message = htmlspecialchars($forward_message);
			print "<tr><td valign=\"top\" class=\"bodytext\">$efdate</td><td valign=\"top\" class=\"bodytext\">".preg_replace('/(,|;)/','$1 ',$forward_to_ef)."</td><td valign=\"top\" class=\"bodytext\">$forward_subject_ef</td>
			<td valign=\"top\" class=\"bodytext\" style=\"border:dashed 1px #000000;\">$forward_message</td>
			<td valign=\"top\" class=\"bodytext\">$forward_attachments_ef</td></tr>";
		}
		print "</table></div>";
	}
}
?>
</body>
</html>