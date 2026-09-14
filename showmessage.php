<?php
$LOGOUT_PAGE = 'content/index.php';
$ALLOW_GROUPS = array(20,38);
require_once("auth_auth.php");
require_once('includes/clean.php');
ini_set("default_charset", "utf-8");
if(isset($_REQUEST['hy'])){
	$hy = (int)$_REQUEST['hy'];
}
if(empty($hy)){
	$hy = '';
}

if(isset($_REQUEST['cetid'])){
	$cetid = $_REQUEST['cetid'];
}
elseif(isset($_REQUEST['muid'])){
	$query2 = "SELECT `cetid` FROM `cscan_email_text$hy` WHERE `cettype`='text/html' AND `muid`='".$DRW->real_escape_string($_REQUEST['muid'])."' ORDER BY ABS(`cetpart`) ASC LIMIT 1";
	$query_result2 = $DRW->query($query2,$DRW_read2);
	$data2 = $DRW->fetch_row($query_result2);
	$cetid = $data2[0];
}
else{
	$cetid = '';
}

$query2 = "SELECT `cettext`,`cettype`,`muid` FROM `cscan_email_text$hy` WHERE `cetid`='".$DRW->real_escape_string($cetid)."'";
$query_result2 = $DRW->query($query2,$DRW_read2);
$data2 = $DRW->fetch_row($query_result2);
$srchd=array("â","¢","Â","Ã","Â","Â","€","Â€","","");
$repstr=array("","","","","","","","","","");

$cefdata = $data2[0];

######### latest encode message data #################
$cefdata=html_entity_decode($cefdata);
//$cefdata= utf8_decode(str_replace($srchd,$repstr,$cefdata));
$ceftype = $data2[1];
$muid = $data2[2];

$query3 = "SELECT `cefid`,`cefidentification` FROM `cscan_email_file$hy` WHERE `muid`='".$DRW->real_escape_string($muid)."' AND `cefidentification`<>'' AND `cefidentification` IS NOT NULL";
$query_result3 = $DRW->query($query3,$DRW_read2);
while($data3 = $DRW->fetch_row($query_result3)){
	$cefid = $data3[0];
	$cefidentification = $data3[1];
	if(preg_match('/^<([^<].+[^>])>$/',$cefidentification,$match)){
		$cefidentification = $match[1];
	}
	$cefdata = str_replace("cid:$cefidentification","attachment.php?cefid=$cefid&hy=$hy",$cefdata);
}
@ob_end_clean();

######### latest encode message data #################
header('Content-Type: text/html; charset=utf-8');
//header('Content-Type: text/html; charset=iso-8859-1');
if($ceftype!='text/html') {
	echo '<html><body><pre>';
	echo wordwrap(preg_replace('/(\\r?\\n|\\r)/',"\r\n",cleanHTML($cefdata)),100,"\r\n");
	echo '</pre></body></html>';
}
else{
	echo cleanHTML($cefdata);
}
?>
