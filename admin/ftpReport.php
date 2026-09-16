<?php
$ALLOW_GROUPS = array(39);
require_once("../auth_auth.php");
include 'top.php';
require_once('../includes/ehLog.php');
ini_set('memory_limit', '-1');

//if(isset($_REQUEST['run'])){
//	//exec("cd ../; /usr/bin/php cronjob_ftp2.php > /dev/null 2>&1 &");
//        exec("cd ../; /usr/bin/php cronjob_ftp.php > /dev/null 2>&1 &");
//	ob_end_clean();
//	header("Location: {$_SERVER['PHP_SELF']}");
//	exit;
//}
if(isset($_REQUEST['search_text'])) {
	$reportDate = date('Y-m-d',strtotime($_REQUEST['search_text']));
	$reportDate2 = date('Y-m-d',strtotime($_REQUEST['search_text2']));
	if($reportDate>$reportDate2){
		$reportDate2 = $reportDate;
	}
}
else{
	$reportDate2 = $reportDate = date('Y-m-d');
}
$Ym = date('Y-m');
$Ymd = date('Y-m-d',strtotime('-28 days'));
?>
<table width="100%" border="0" cellspacing="0" cellpadding="5" class="text">
<tr><td class="adminhead" align="center" colspan="3">COMPETISCAN FTP</td></tr>
</table>
<div>&nbsp;</div>
<table width="100%" border="0" cellspacing="0" cellpadding="5" class="text">
<tr class="head1">
<td class="adminhead"><strong>Date</strong></td>
<td class="adminhead"><strong>Core</strong></td>
<td class="adminhead"><strong>Non-core</strong></td>
<td class="adminhead"><strong>Telecom</strong></td>
<td class="adminhead"><strong>Travel &amp; Leisure</strong></td>
<td class="adminhead"><strong>Total</strong></td>
<td class="adminhead">&nbsp;</td>
<td class="adminhead"><strong>Highpriority</strong></td>
<td class="adminhead"><strong>Affinion</strong></td>
</tr>
<?php $count = array();
$resultC2 = $DRW->query("SELECT filename,last_modified FROM chicagorecords WHERE last_modified<'$Ym-01 00:00:00' order by last_modified",$DRW_read);

while($dataC2 = $DRW->fetch_row($resultC2)){
	$ofilename = $dataC2[0];
	$last_modified = substr($dataC2[1],0,7);
	if(!isset($count[$last_modified])){
		$count[$last_modified] = array('telecom'=>0,'travel'=>0,'noncore'=>0,'core'=>0,'highpriority'=>0,'affinion'=>0);
	}
	$lofilename = strtolower($ofilename);
	if(strpos($lofilename,'telecom')!==false){ // || strpos($lofilename,' _tc_')!==false
		$count[$last_modified]['telecom']++;
	}
	elseif(strpos($lofilename,'_tl_')!==false){
		$count[$last_modified]['travel']++;
	}
	elseif(strpos($lofilename,'noncore')!==false){ // || strpos($lofilename,' _nc_')!==false
		$count[$last_modified]['noncore']++;
	}
	else {
		$count[$last_modified]['core']++;
	}
	
	if(strpos($lofilename,'highpriority')!==false || strpos($lofilename,'_hp_')!==false){
		$count[$last_modified]['highpriority']++;
	}
	
	if(strpos($lofilename,'affinion')!==false || strpos($lofilename,'_af_')!==false){
		$count[$last_modified]['affinion']++;
	}
}
$count2 = array();
//echo "SELECT filename,last_modified FROM chicagorecords WHERE last_modified>='$Ymd 00:00:00' order by last_modified";
$resultC2 = $DRW->query("SELECT filename,last_modified FROM chicagorecords WHERE last_modified>='$Ymd 00:00:00' order by last_modified",$DRW_read);

while($dataC2 = $DRW->fetch_row($resultC2)){
	$ofilename = $dataC2[0];
	$last_modified = substr($dataC2[1],0,10);
	if(!isset($count2[$last_modified])){
		$count2[$last_modified] = array('telecom'=>0,'travel'=>0,'noncore'=>0,'core'=>0,'highpriority'=>0,'affinion'=>0);
	}
	$lofilename = strtolower($ofilename);
	if(strpos($lofilename,'telecom')!==false){ // || strpos($lofilename,' _tc_')!==false
		$count2[$last_modified]['telecom']++;
	}
	elseif(strpos($lofilename,'_tl_')!==false){
		$count2[$last_modified]['travel']++;
	}
	elseif(strpos($lofilename,'noncore')!==false){ // || strpos($lofilename,' _nc_')!==false
		$count2[$last_modified]['noncore']++;
	}
	else {
		$count2[$last_modified]['core']++;
	}
	
	if(strpos($lofilename,'highpriority')!==false || strpos($lofilename,'_hp_')!==false){
		$count2[$last_modified]['highpriority']++;
	}
	
	if(strpos($lofilename,'affinion')!==false || strpos($lofilename,'_af_')!==false){
		$count2[$last_modified]['affinion']++;
	}
}
$className='selected-bg';
foreach($count as $date=>$data){
	echo '<tr class="'.$className.'"><td>'.$date.'</td><td>'.$data['core'].'</td><td>'.$data['noncore'].'</td><td>'.$data['telecom'].'</td><td>'.$data['travel'].'</td><td><strong>'.($data['core']+$data['noncore']+$data['telecom']+$data['travel']).'</strong></td><td>&nbsp;</td><td>'.$data['highpriority'].'</td><td>'.$data['affinion'].'</td></tr>';
	if($className=='selected-bg') {
		$className='white-bg';
	}
	else {
		$className='selected-bg';
	}
}

echo '<tr class="'.$className.'"><td colspan="9"><hr /></td></tr>';
$tot = 0;
foreach($count2 as $date=>$data){
	$totrow = $data['core']+$data['noncore']+$data['telecom']+$data['travel'];
	echo '<tr class="'.$className.'"><td>'.$date.'</td><td>'.$data['core'].'</td><td>'.$data['noncore'].'</td><td>'.$data['telecom'].'</td><td>'.$data['travel'].'</td><td><strong>'.$totrow.'</strong></td><td>&nbsp;</td><td>'.$data['highpriority'].'</td><td>'.$data['affinion'].'</td></tr>';
	$tot += $totrow;
	if($className=='selected-bg') {
		$className='white-bg';
	}
	else {
		$className='selected-bg';
	}
}
echo '<tr class="'.$className.'"><td colspan="5">&nbsp;</td><td><strong>'.$tot.'</strong></td><td>&nbsp;</td><td colspan="2">&nbsp;</td></tr>';
?>
</table>
<?php 
//if(running_php_cmd('cronjob_ftp2.php')){
//	echo '<div style="margin:10px;"><em>Import In Process . . .</em></div>';
//}
if(running_php_cmd('cronjob_ftp.php')){
	echo '<div style="margin:10px;"><em>Import In Process . . .</em></div>';
}
else{
	?>
	<div style="margin:10px;">
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" name="runner">
	<input class="button" type="submit" name="runb" value="Start Import"  />
	<input type="hidden" name="run" value="1"  />
	</form>
	</div>
	<?php 
}
include 'bottom.php';
?>
