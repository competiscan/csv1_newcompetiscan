<?php
 //error_reporting( E_ALL ^ E_DEPRECATED );
	//ini_set('display_errors',1);
ini_set("memory_limit","-1");
set_time_limit(0);
require_once('includes/globalSession.php');
require_once('includes/checklogin.php');
//require_once('includes/functions_latest2.php');  //latest function
require_once('includes/functions_latest3.php');  //latest function
if(isset($_REQUEST['graph_choice'])){
	$graph_choice = (int)$_REQUEST['graph_choice'];
}
else {
	$graph_choice = 1;
}
if(isset($_REQUEST['title_choice'])) $title_choice = $_REQUEST['title_choice'];
else $title_choice = '';
if(isset($_REQUEST['total_choice'])) $total_choice = (int)$_REQUEST['total_choice'];
else $total_choice = 1;
if(isset($_REQUEST['date_choice'])) $date_choice = (int)$_REQUEST['date_choice'];
else $date_choice = 1;
if(isset($_REQUEST['chart_choice'])) $chart_choice = (int)$_REQUEST['chart_choice'];
else $chart_choice = 1;
if(isset($_REQUEST['top_comp'])) $top_comp = (int)$_REQUEST['top_comp'];
else $top_comp = 0;
if(isset($_REQUEST['bid'])) $bid = (int)$_REQUEST['bid'];
else $bid = -1;
if(isset($_REQUEST['ssid'])) $ssid = (int)$_REQUEST['ssid'];
else $ssid = 0;
if(isset($_POST['sort'])) $sort = (int)$_POST['sort'];
else $sort = -3;
if(isset($_POST['eb_date1'])) $eb_date1 = $_POST['eb_date1'];
else $eb_date1 = '';
if(isset($_POST['eb_date2'])) $eb_date2 = $_POST['eb_date2'];
else $eb_date2 = '';
if(isset($_POST['eb_date3'])) $eb_date3 = $_POST['eb_date3'];
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

$eb_array = array($eb_date1,$eb_date2,$eb_date3,$eb_gender,$eb_state,$eb_age,$eb_income,$eb_DMA_ID);

if($chart_choice==4 || $chart_choice==5){
	$pp = true;
	if($chart_choice==4){
		$chart_choice = 1;
	}
	elseif($chart_choice==5){
		$chart_choice = 2;
	}
}
else{
	$pp = false;
}

$newArray = array(
	$graph_choice,
	$title_choice,
	$total_choice,
	$date_choice,
	$chart_choice,
	$top_comp,
	$bid,
	$ssid,
	$sort,
	$eb_array
);

$_SESSION['graph_pie_array'] = $newArray;
$quarters = array(1=>3,2=>6,3=>9,4=>12);

if($chart_choice==3){
	require_once('graph_img_test.php');
}
else{ ?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
	<title>Competiscan <?php echo htmlspecialchars($title_choice); ?></title>
	</head>
	<body>
	<?php 
	$imageDataLinks = array();
	if(($date_choice==3 || $date_choice==2 || $date_choice==4) && $graph_choice!=14){         
		if($bid>=0) {
			//list($graphQuery_pre) = doQuery(0, false, '', false, $bid);
                        list($graphQuery_pre) = doQuery_latest2(0, false, '', false, $bid);
		}
		else{
			//list($graphQuery_pre) = doQuery($ssid, false, '', false);
                        list($graphQuery_pre) = doQuery_latest2($ssid, false, '', false);
		}
		/*$graphQuery = "SELECT MIN(DATE_FORMAT(ppdate,'%Y-%m')),MAX(DATE_FORMAT(ppdate,'%Y-%m'))
			FROM cscan_panelists_product cp 
			JOIN ($graphQuery_pre) AS t1 ON(cp.productID=theproductID)
			WHERE ppmv>0";*/
                ####################For display  graph chart by Dev ######################
            echo $graphQuery = "SELECT MIN(DATE_FORMAT(ppdate,'%Y-%m')),MAX(DATE_FORMAT(ppdate,'%Y-%m'))
			FROM cscan_panelists_product cp 
			JOIN ($graphQuery_pre) AS t1 ON(cp.productID=theproductID)";
		$rows = $DRW->query($graphQuery,$DRW_read);
		$rs = $DRW->fetch_row($rows);
		$min = $rs[0];
		$max = $rs[1];
		$start = (int)substr($min,0,4);
		$end = (int)substr($max,0,4);
		if($date_choice==3 && $end>0){//month
			$startm = (int)substr($min,5);
			$endm = (int)substr($max,5);
			for($i=$start;$i<=$end;$i++){
				$start_m = 1;
				$end_m = 12;
				if($i==$start){
					$start_m = $startm;
				}
				if($i==$end){
					$end_m = $endm;
				}
				for($j=$start_m;$j<=$end_m;$j++){
					$imageDataLinks[] = 'graph_img_test.php?new='.date('YmdHis').'&amp;ym='.$i.'-'.str_pad($j,2,'0',STR_PAD_LEFT);
				}
			}
		}
		else{ //if($date_choice==2){//year
			for($i=$start;$i<=$end;$i++){
				if($date_choice==4){
					$max1 = 1;
					foreach($quarters as $q=>$max){
						$imageDataLinks[] = 'graph_img_test.php?new='.date('YmdHis').'&amp;ym='.$i.'-'.str_pad($max1,2,'0',STR_PAD_LEFT).'&amp;ym2='.$i.'-'.str_pad($max,2,'0',STR_PAD_LEFT);
						$max1 = $max + 1;
					}
				}
				else{
					$imageDataLinks[] = 'graph_img_test.php?new='.date('YmdHis').'&amp;ym='.$i;
				}
			}
		}
	}
	else{
		$imageDataLinks[] = 'graph_img_test.php?new='.date('YmdHis');
	}
        echo "<pre>";
        print_r($imageDataLinks); 
	if($pp){
            //echo "okksdkskdks"; die;
		$SKIP_FUNCTION = true;
		require_once('graph_img_test.php');
		$imageDataArray = array();
		foreach($imageDataLinks as $k=>$idl){
			$pieces = explode('&amp;',$idl);
			foreach($pieces as $p){
				$pair = explode('=',$p);
				if(count($pair==2)){
					if($pair[0]=='ym') {
						$_REQUEST['ym'] = $pair[1];
					}
					if($pair[0]=='ym2') {
						$_REQUEST['ym2'] = $pair[1];
					}
				}
			}
			$savefile = '/tmp/exportPowerPointImage_'.$_SESSION['sess_userID'].'_'.$k.'.jpg';
			$fdata = do_graph_img(true);
			$checkf = file_put_contents($savefile, $fdata);
			if($checkf!==false){
				$imageDataArray[] = $savefile;
			}
		}
		require_once('powerpoint_test.php');
		exit;
	}
	else{
		foreach($imageDataLinks as $k=>$idl){
			if($k>0){
				echo '<br />';
			}
			echo '<img src="'.$idl.'" alt="" />';
		}
	}
	?>
	</body>
	</html>
<?php 
}
