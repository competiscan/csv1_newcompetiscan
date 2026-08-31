<?php 

require_once('includes/globalSession.php');
require_once('includes/checklogin.php');
require_once 'HTTP/Download.php';
require_once('includes/ehLog.php');
if(isset($_REQUEST['submit2'])){
    if($_SESSION['total_searchedcount']>30){
        $senddownload=30;
    }else{
        $senddownload   =   $_SESSION['total_searchedcount'];
    }
}else{
   $senddownload= $_SESSION['total_searchedcount'];
}
$userid=$_SESSION['sess_userID'];

//print_r($_REQUEST);exit;
$filepath = '/srv/httpd/competiscan.com/html/export_excel/exportExcel_'.$_SESSION['sess_userID'];
if(isset($_REQUEST['file_choice'])) $file_choice = (int) $_REQUEST['file_choice'];
else $file_choice = 1;
if(isset($_REQUEST['out'])) $out = (int) $_REQUEST['out'];
else $out = 0;
if(isset($_REQUEST['noback'])) $noback = (int) $_REQUEST['noback'];
else $noback = 0;
if(isset($_REQUEST['check'])) $check = (int) $_REQUEST['check'];
else $check = 0;
if(isset($_REQUEST['cancel'])) $cancel= (int) $_REQUEST['cancel'];
else $cancel = 0;

if(!empty($cancel)){
	$proc_pid = running_php_cmd_pid('exportExcel_back1.php '.$_SESSION['sess_userID']);
	if(!empty($proc_pid)){
		$savedQ = "SELECT unix_pid FROM cscan_progress WHERE userID={$_SESSION['sess_userID']}";
		$rs = $DRW->query($savedQ,$DRW_read);
		$data = $DRW->fetch_row($rs);
		$unix_pid = $data[0];
		if(!empty($unix_pid) && $unix_pid==$proc_pid){
			$savedQ = "DELETE FROM cscan_progress WHERE userID={$_SESSION['sess_userID']}";
			$rs = $DRW->query($savedQ,$DRW_main);
			exec("kill ".$unix_pid);
		}
	}
}
elseif(!empty($check)){
	$pct = -1;
	if($out){
		if(is_file($filepath)){
			$savedQ = "DELETE FROM cscan_progress WHERE userID={$_SESSION['sess_userID']}";
			$rs = $DRW->query($savedQ,$DRW_main);
			
			$_SESSION['exportExcel_out'] = 0;
                        $currenttime=time();
			@ob_end_clean();
			if($file_choice==1){
				$filename = 'Competiscan_Export_'.$currenttime.'_'.date('Y-m-d').'.xls';
				$content_type= 'application/vnd.ms-excel';
			}
			elseif($file_choice==3){
				$filename = 'Competiscan_Export_'.$currenttime.'_'.date('Y-m-d').'.xlsx';
				$content_type= 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
			}
			else{
				$filename = "Competiscan_Export_".$currenttime.'_'.date('Y-m-d').".csv";
				$content_type = 'text/plain';
			}
			$dl = new HTTP_Download();
			$dl->setFile($filepath);
			$dl->setContentType($content_type);
			$dl->setCacheControl('public');
			$dl->setCache(true);
			$dl->setContentDisposition(HTTP_DOWNLOAD_ATTACHMENT, $filename);
			$dl->send();
			exit;
		}
	}
	else{
		$bname = basename(__FILE__);
		$savedQ = "SELECT pct,userID FROM cscan_progress WHERE userID={$_SESSION['sess_userID']}";
		$rs = $DRW->query($savedQ,$DRW_read);
		$data = $DRW->fetch_row($rs);
		$pct = (int)$data[0];
		if($pct<300 && !running_php_cmd('exportExcel_back1.php '.$_SESSION['sess_userID'])){
			if($pct==250){
				$pct = -1;
			}
			else{
				if(empty($data[1])){
					$sql = "REPLACE INTO cscan_progress (userID,pct,file_choice) VALUES ({$_SESSION['sess_userID']},250,$file_choice)";
					$DRW->query($sql,$DRW_main);
				}
				else{
					$sqlu = "UPDATE cscan_progress SET pct=250 where userID={$_SESSION['sess_userID']} AND pct<>300";
					$DRW->query($sqlu,$DRW_main);
				}
			}
		}
	}
	ob_end_clean();
	echo $pct;
	exit;
}
else{
	if(isset($_POST['bid'])) $bid = (int)$_POST['bid'];
	else $bid = -1;
	if(isset($_POST['ssid'])) $ssid = (int)$_POST['ssid'];
	else $ssid = 0;
	if(isset($_POST['sort'])) $sort = (int)$_POST['sort'];
	else $sort = -3;

	if(isset($_POST['more'])) $more = (int)$_POST['more'];
	else $more = 0;

	if(isset($_POST['eb_date1'])) $eb_date1= $_POST['eb_date1'];
	else $eb_date1 = '';
	if(isset($_POST['eb_date2'])) $eb_date2= $_POST['eb_date2'];
	else $eb_date2 = '';
	if(isset($_POST['eb_date3'])) $eb_date3= $_POST['eb_date3'];
	else $eb_date3 = '';
	if(isset($_POST['eb_gender'])) $eb_gender = $_POST['eb_gender'];
	else $eb_gender = '';
	if(isset($_POST['eb_state'])) $eb_state = $_POST['eb_state'];
	else $eb_state = '';
	if(isset($_POST['eb_age'])) $eb_age = $_POST['eb_age'];
	else $eb_age = '';
	if(isset($_POST['eb_income'])) $eb_income = $_POST['eb_income'];
	else $eb_income = '';
	if(isset($_POST['eb_DMA_ID'])) $eb_DMA_ID = $_POST['eb_DMA_ID'];
	else $eb_DMA_ID = '';
	$do_bid = false;

	if(isset($_POST['field'])){
		$post_field = $_POST['field'];
	}
	else{
		$post_field = array();
	}
	$sess_sector = $_SESSION['sess_sector'];
	$sess_search_exclude = $_SESSION['sess_search_exclude'];
	$sess_plevel = $_SESSION['sess_plevel'];
	$sess_userID = $_SESSION['sess_userID'];
	if(isset($_POST['page'])) $page = (int)$_POST['page'];
	else $page = 0;
	if(isset($_POST['topCompany'])) $topCompany = (int)$_POST['topCompany'];
	else $topCompany = 0;

	$argsArray = array($filepath,$file_choice,$bid,$ssid,$sort,$more,$eb_date1,$eb_date2,$eb_date3,$eb_gender,$eb_state,$eb_age,$eb_income,$eb_DMA_ID,$post_field,$sess_sector,$sess_search_exclude,$sess_plevel,$sess_userID,$page,$topCompany,$_SESSION['sess_userID']);

	if($noback){
		$exec = '';
	}
	else{
		//$exec = ' > /dev/null 2>&1 &';
		$exec = ' > cron_logs/exportExcel.log 2>&1 &';
	}
	
	if(!running_php_cmd('exportExcel_back1.php '.$_SESSION['sess_userID'])){
		if(is_file($filepath)){
			@unlink($filepath);
		}
		exec("/usr/bin/php exportExcel_back1.php ".escapeshellarg($_SESSION['sess_userID'])." ".escapeshellarg(serialize($argsArray))."".$exec);
	}
	
	if($noback){
		ob_end_clean();
		if(is_file($filepath)){
			header("Location: {$_SERVER['PHP_SELF']}?file_choice=$file_choice&check=1&out=1");
		}
		exit;
	}
	else{
		?>
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"><head><title>Competiscan</title>
		<link rel="shortcut icon" href="favicon.ico" />
		<link href="includes/competiscan_stylesheet.css?v=20100318" rel="stylesheet" type="text/css" />
		<script src="includes/jsFunctions.js?v=20090601" type="text/javascript"></script>
		<script src="includes/ajax.js?v=20090601" type="text/javascript"></script>
		<script type="text/javascript">
		<!--
		function fixer(){
			var obj = parent.window.document.getElementById('loading');
			var obj2 = parent.window.document.getElementById('submits');
			if(obj && obj2){
                       // alert('hiiii');return false;
				obj.style.display = 'block';
				obj2.style.display = 'none';
				var pct = processajax('<?php echo $_SERVER['PHP_SELF']; ?>', false, 'POST', '<?php echo "file_choice=$file_choice&check=1"; ?>', false, '');
				if(pct=='300'){
					
                                        var pct2 = processajax('downloaddata.php', false, 'POST', '<?php echo "totaldownload=$senddownload&userid=$userid"; ?>', false, '');
				
                                        obj.style.display = 'none';
					obj2.style.display = 'block';
					document.location.href = '<?php echo $_SERVER['PHP_SELF']."?file_choice=$file_choice&check=1&out=1"; ?>';
				}
				else{
					var t = 3000;
					if(pct=='200' || pct=='250'){
						pct = 'Finalizing...';
						var t = 1000;
					}
					else if(pct=='-1'){
						pct = 'Progress';
					}
					else{
						pct = pct + '%';
					}
					var obj3 = document.getElementById('changetxt');
					my_innerHTML_text(obj3,pct);
					if(pct!='-1'){
						window.setTimeout('fixer()', t);
					}
				}
			}
		}
		function canceler(){
			var out = processajax('<?php echo $_SERVER['PHP_SELF']; ?>', false, 'POST', '<?php echo "file_choice=$file_choice&cancel=1"; ?>', false, '');
			var obj = parent.window.document.getElementById('loading');
			var obj2 = parent.window.document.getElementById('submits');
			if(obj && obj2){
				obj.style.display = 'none';
				obj2.style.display = 'block';
				document.location.href = 'blank.html';
			}
		}
		//-->
		</script>
		</head>
		<body onload="fixer();" style="padding:0px;margin:0px;">
		<div><span id="changetxt">0%</span> <img src="images/searching.gif" alt="" border="0" height="16" width="16" /></div>
		<div><a href="#" class="bluelink" onclick="canceler();">Cancel</a></div>
		</body>
		</html>
		<?php
	}
}
