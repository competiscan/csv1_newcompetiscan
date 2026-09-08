<?php
$LOGOUT_PAGE = 'content/index.php';
$ALLOW_GROUPS = array(20,38);
require_once("auth_auth.php");
require_once('includes/clean.php');
ini_set("default_charset", "utf-8");
if(isset($_GET['muid'])) $muid = (int)$_GET['muid'];
else $muid = 0;

if(isset($_REQUEST['hy'])){
	$hy = (int)$_REQUEST['hy'];
}
if(empty($hy)){
	$hy = '';
}
/*
$sql = "UPDATE `cscan_email$hy` SET `email_read`=1 WHERE `muid`='".$DRW->real_escape_string($muid)."'";
$DRW->query($sql,$DRW_main);
*/
$cidArray = array();
$query2 = "SELECT `cefid`,`cefname`,`ceftype`,`cefidentification` FROM `cscan_email_file$hy` WHERE `muid`='".$DRW->real_escape_string($muid)."' ORDER BY `cefpart` ASC";
$query_result2 = $DRW->query($query2,$DRW_read2);
while($data2 = $DRW->fetch_row($query_result2)){
	$cefid = $data2[0];
	$cefname = $data2[1];
	$ceftype = $data2[2];
	$cefidentification = $data2[3];
	
	if($cefidentification!=''){
		if(preg_match('/^<([^<].+[^>])>$/',$cefidentification,$match)){
			$cefidentification = $match[1];
		}
		$cidArray[$cefid] = $cefidentification;
	}
}
//$query2 = "SELECT `cettext`,`cetid` FROM `cscan_email_text$hy` WHERE `cettype`='text/html' AND `muid`='".$DRW->real_escape_string($muid)."' ORDER BY `cetpart` ASC";
$query2 = "SELECT `cettext`,`cetid` FROM `cscan_email_text$hy` WHERE `cettype`='text/html' AND `muid`='".$DRW->real_escape_string($muid)."' ORDER BY cetid DESC limit 0,1";
$query_result2 = $DRW->query($query2,$DRW_read2);
$cefdata = '';
while($data2 = $DRW->fetch_row($query_result2)){
        $srchd=array("â","¢","Â","Ã","Â","Â","€","Â€","","");
        $repstr=array("","","","","","","","","","");
	$cettext = $data2[0];
       // $cettext = utf8_decode(str_replace( $srchd, $repstr,$cettext));
         ######### latest encode message data #################
        $cettext=html_entity_decode($cettext);
	$cetid = $data2[1];
	foreach($cidArray as $cefid=>$cefidentification){
		$cettext = str_replace("cid:$cefidentification","attachment.php?cefid=$cefid&hy=$hy",$cettext);
	}
	if($cefdata!='') {
		$cefdata .= ' <br /> <hr /> <hr /> <hr /> <br /> ';
	}
	$cefdata .= $cettext;
}

@ob_end_clean();
######### latest encode message data #################
//header('Content-Type: text/html; charset=iso-8859-1');
header('Content-Type: text/html; charset=utf-8');
if($cefdata!='') {
	echo cleanHTML($cefdata);
}
else {
	echo '<html><body>';
}
//$query2 = "SELECT `cettext`,`cetid` FROM `cscan_email_text$hy` WHERE `cettype`='text/plain' AND `muid`='".$DRW->real_escape_string($muid)."' ORDER BY `cetpart` ASC";
$query2 = "SELECT `cettext`,`cetid` FROM `cscan_email_text$hy` WHERE `cettype`='text/plain' AND `muid`='".$DRW->real_escape_string($muid)."' ORDER BY cetid DESC limit 0,1";
$query_result2 = $DRW->query($query2,$DRW_read2);
$textnum = 0;
while($data2 = $DRW->fetch_row($query_result2)){
	if($cefdata!='') {
		echo ' <br /> <hr /> <hr /> <hr /> <br /> ';
	}
	echo '<pre>';
        $srchd=array("â","¢","Â","Ã","Â","Â","€","Â€","","");
        $repstr=array("","","","","","","","","","");
	$cettext = $data2[0];
          ######### latest encode message data #################
        $cettext=html_entity_decode($cettext);
        //$cettext = utf8_decode(str_replace( $srchd, $repstr,$cettext));
        
	//$cettext = $data2[0];
	$cetid = $data2[1];
	
	echo wordwrap(preg_replace('/(\\r?\\n|\\r)/',"\r\n",cleanHTML($cettext)),100,"\r\n");
	echo '</pre>';
}
if($cefdata=='') {
	echo '</body></html>';
}
?>