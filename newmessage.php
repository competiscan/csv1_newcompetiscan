<?php 
$LOGOUT_PAGE = 'content/index.php';
$ALLOW_GROUPS = array(20,38);
require_once("auth_auth.php");
include('includes/clean.php');

if(isset($_GET['muid'])) $muid = (int)$_GET['muid'];
else $muid = 0;
if(isset($_GET['cefid'])) $cefid = (int)$_GET['cefid'];
else $cefid = 0;

if(isset($_REQUEST['hy'])){
	$hy = (int)$_REQUEST['hy'];
}
if(empty($hy)){
	$hy = '';
}

$query = "SELECT `mailbox_uid`,`email_date`,`email_to`,`email_from`,`email_subject`,`contact_type_m_c`,`email_cc`,`email_bcc` FROM `cscan_email$hy` WHERE `muid`='".$DRW->real_escape_string($muid)."'";
$query_result = $DRW->query($query,$DRW_read);
$data = $DRW->fetch_row($query_result);
$mailbox_uid = $data[0];
$email_date = $data[1];
$email_to = $data[2];
$email_from = $data[3];
$email_subject = $data[4];
$contact_type_m_c = $data[5];
$email_cc = $data[6];
$email_bcc = $data[7];

$query2 = "SELECT `cefdata`,`cefname`,`ceftype`,`cefencoding`,`cefsplit`,`cefpart`,cefpath FROM `cscan_email_file$hy` WHERE `cefid`='".$DRW->real_escape_string($cefid)."'";
$query_result2 = $DRW->query($query2,$DRW_read);
$data2 = $DRW->fetch_row($query_result2);
$cefdata = $data2[0];
$cefname = $data2[1];
$ceftype = $data2[2];
$cefencoding = $data2[3];
$cefsplit = $data2[4];
$cefpart = $data2[5];
$cefpath = $data2[6];
$file_src = dirname(__FILE__).'/'.$cefpath;


$textArray = array();
$texti = 0;
$fileArray = array();
$filei = 0;

if(!extension_loaded('mailparse')) {
	dl('mailparse.' . PHP_SHLIB_SUFFIX);
}
if(extension_loaded('mailparse') && preg_match('/(.+)\\.(eml|email)$/',$cefname,$match)) {
	if(is_file($file_src)){
		$mimemail = mailparse_msg_parse_file($file_src);
		$array2 = mailparse_msg_get_structure($mimemail);
		foreach($array2 as $mimesection){
			$msg_part = mailparse_msg_get_part($mimemail,$mimesection);
			$array3 = mailparse_msg_get_part_data($msg_part);
			
			if(isset($array3['headers']['subject']) && $array3['headers']['subject']!=''){
				$email_subject = $email_subject.': '.$array3['headers']['subject'];
			}
			
			if(preg_match('/^text/i',$array3['content-type'])){
				ob_start();
				if(is_file($file_src)){
					mailparse_msg_extract_part_file ($msg_part, $file_src);
				}
				else{
					mailparse_msg_extract_part($msg_part, $newmessage);
				}
				$textArray[$texti]['content'] = ob_get_clean();
				$textArray[$texti]['type'] = $array3['content-type'];
				$texti++;
			}
			elseif(!preg_match('/^(multipart|message)/i',$array3['content-type'])){
				ob_start();
				if(is_file($file_src)){
					mailparse_msg_extract_part_file ($msg_part, $file_src);
				}
				else{
					mailparse_msg_extract_part($msg_part, $newmessage);
				}
				$fileArray[$filei]['content'] = ob_get_clean();
				$fileArray[$filei]['type'] = $array3['content-type'];
				$filei++;
			}
		}
		mailparse_msg_free($mimemail);
	}
}

if(!$cefsplit && is_array($textArray) && count($textArray)>0 || is_array($fileArray) && count($fileArray)>0) {
	$query = "INSERT INTO `cscan_email$hy` (`mailbox_uid`,`email_date`,`email_to`,`email_from`,`email_subject`,`contact_type_m_c`,`email_cc`,`email_bcc`) 
		VALUES ('".$DRW->real_escape_string($mailbox_uid)."',NOW(),'".$DRW->real_escape_string($email_to)."','".$DRW->real_escape_string($email_from)."','".$DRW->real_escape_string($email_subject)."','".$DRW->real_escape_string($contact_type_m_c)."','".$DRW->real_escape_string($email_cc)."','".$DRW->real_escape_string($email_bcc)."')";
	$DRW->query($query,$DRW_main);
	
	$newmuid = $DRW->insert_id($DRW_main);
	
	foreach($textArray as $k=>$text){
		$query = "INSERT INTO `cscan_email_text$hy` (`cetpart`,`cettext`,`cettype`,`muid`) 
			VALUES ('".$cefpart.".0".($k+1)."','".$DRW->real_escape_string($text['content'])."','".$DRW->real_escape_string($text['type'])."','".$DRW->real_escape_string($newmuid)."')";
		$DRW->query($query,$DRW_main);
	}
	/*foreach($fileArray as $k=>$file){
		$query = "INSERT INTO `cscan_email_file$hy` (`cefpart`,`cefdata`,`cefname`,`ceftype`,`muid`,`cefencoding`) 
			VALUES ('".($k+1)."','".$DRW->real_escape_string($file['content'])."','".$DRW->real_escape_string($file['type'])."','".$DRW->real_escape_string($file['type'])."','".$DRW->real_escape_string($newmuid)."','5')";
		$DRW->query($query,$DRW_main);
	}*/
	$muid = $newmuid;
	
	$sql = "UPDATE `cscan_email_file$hy` SET `cefsplit`=1 WHERE `cefid`='".$DRW->real_escape_string($cefid)."'";
	$DRW->query($sql,$DRW_main);
}

@ob_end_clean();
header("Location: email.php?muid=$muid&hy=$hy");
?>