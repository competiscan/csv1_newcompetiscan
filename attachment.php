<?php
$LOGOUT_PAGE = 'content/index.php';
$ALLOW_GROUPS = array(5,20);
require_once("auth_auth.php");
require_once('includes/clean.php');
require_once 'HTTP/Download.php';

if(isset($_REQUEST['hy'])){
	$hy = (int)$_REQUEST['hy'];
}
if(empty($hy)){
	$hy = '';
}

$query2 = "SELECT `cefdata`,`cefname`,`ceftype`,`cefencoding`,cefpath FROM `cscan_email_file$hy` WHERE `cefid`='".$DRW->real_escape_string($_GET['cefid'])."'";
$query_result2 = $DRW->query($query2,$DRW_read);
$data2 = $DRW->fetch_row($query_result2);
$cefdata = $data2[0];
$cefname = $data2[1];
$ceftype = $data2[2];
$cefencoding = $data2[3];
$cefpath = $data2[4];
$DRW->free_result($query_result2);

@ob_end_clean();

if($ceftype=='application/octet-stream' || $ceftype=='image/unknown'){
	if(preg_match('/\\.pdf$/',$cefname)){
		$ceftype = 'application/pdf';
	}
	elseif(preg_match('/\\.gif$/',$cefname)){
		$ceftype = 'image/gif';
	}
	elseif(preg_match('/\\.png$/',$cefname)){
		$ceftype = 'image/png';
	}
	elseif(preg_match('/\\.(jpg|jpeg)$/',$cefname)){
		$ceftype = 'image/jpeg';
	}
	elseif(preg_match('/\\.txt$/',$cefname)){
		$ceftype = 'text/plain';
	}
	/*
	application/msexcel
	application/msword
	application/vnd.ms-excel
	application/vnd.ms-powerpoint
	image/x-citrix-gif
	image/x-citrix-jpeg
	*/
}

$file_src = dirname(__FILE__).'/'.$cefpath;
if(!empty($cefpath) && is_file($file_src)){
	$dl = new HTTP_Download();
	$dl->setFile($file_src);
	$dl->setContentType($ceftype);
	$dl->setCacheControl('public');
	$dl->setCache(true);
	$dl->setContentDisposition(HTTP_DOWNLOAD_INLINE, trim($cefname));
	$dl->send();
	exit;
}
?>