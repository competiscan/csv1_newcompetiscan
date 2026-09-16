<?php 
require_once('includes/globalSession.php');
require_once('includes/checklogin.php');

require_once('includes/dashboardData.php');
$DASH = new dashboardData();

if(isset($_REQUEST['look'])) {
	$look = $_REQUEST['look'];
}
else {
	$look = '';
}
if(isset($_REQUEST['field'])) {
	$field = $_REQUEST['field'];
}
else {
	$field = '';
}
if(isset($_REQUEST['csv'])){
	$csv = (int)$_REQUEST['csv'];
}
else{
	$csv = 0;
}
if(isset($_REQUEST['json'])){
	$json = (int)$_REQUEST['json'];
}
else{
	$json = 0;
}
if(!empty($_REQUEST['sort_entries'])){
	$sort_entries = $_REQUEST['sort_entries'];
}
else{
	$sort_entries = 'retailmarketer';
}
if(!empty($_REQUEST['entries'])){
	$entries = (int)$_REQUEST['entries'];
}
else{
	$entries = 10;
}
if(!empty($_REQUEST['start_entries'])){
	$start_entries = (int)$_REQUEST['start_entries'];
}
else{
	$start_entries = 0;
}
if($csv){
	$entries = 0;
}
$search = array(
	'electricitynaturalgas',
	'state',
	'edc',
	'retailmarketer',
	'producttype',
	'offerrate',
	'term',
	'renewable',
	'earlyterminationfee',
	'monthlyfee',
);
$display = array(
	'retailmarketer',
	'producttype',
	'offerrate',
	'term',
	'renewable',
	'unitofenergy',
	'edc',
);
$no_display = array(
	'discontinueddate',
);

$out = array();
$out_text = '';
if(!empty($field) && !empty($look)){
	if($field=='edc'){
		$electricitynaturalgas = explode(',',$look);
		$dashboard_type_energy_id_txt = '';
		foreach($electricitynaturalgas as $en){
			if(!empty($en) && $en!='-1'){
				if($dashboard_type_energy_id_txt!=''){
					$dashboard_type_energy_id_txt .= ' OR ';
				}
				$dashboard_type_energy_id_txt .= 'dashboard_type_energy_id='.$en;
			}
		}
		if($dashboard_type_energy_id_txt!=''){
			$dashboard_type_energy_id_txt = ' AND ('.$dashboard_type_energy_id_txt.')';
		}
		$out_text .= '<option value="">Any</option>';
		$pvalues = $DASH->get_edc_permissions($_SESSION['sess_username']);
		$sqlL = "SELECT DISTINCT ".$DASH->maps[$field]['list_data']['table'].".".$DASH->maps[$field]['list_data']['id_field'].",".$DASH->maps[$field]['list_data']['name_field']." FROM ".$DASH->maps[$field]['list_data']['table']." JOIN cscan_edc_dashboard_type_energy ON (".$DASH->maps[$field]['list_data']['table'].".".$DASH->maps[$field]['list_data']['id_field']."=cscan_edc_dashboard_type_energy.edc_id) WHERE ".$DASH->maps[$field]['list_data']['table'].".".$DASH->maps[$field]['list_data']['id_field']." IN (".implode(',',$pvalues).") AND ".$DASH->maps[$field]['list_data']['name_field']."<>'' $dashboard_type_energy_id_txt ORDER BY ".$DASH->maps[$field]['list_data']['sort_field'];
		$rsL = $DASH->DRW->query($sqlL,$DASH->DRW_read);
		while($rowL = $DASH->DRW->fetch_row($rsL)){
			$out_text .= '<option value="'.$rowL[0].'">'.htmlspecialchars($rowL[1]).'</option>';
		}
	}
	else{
		$out = $DASH->get_lookup_array($field,$look);
	}
}
elseif(!empty($_REQUEST['graph_id'])){
	$data = $DASH->get_dashboard_assoc($_REQUEST['graph_id']);
	if(!empty($data['dashboard_id'])){
		$dateArray = array();
		$sql = $DASH->get_dashboard_query(array(),0,0,'date',false,$data['dashboard_id']);
		$rs = $DRW->query($sql,$DRW_read);
		while($row = $DRW->fetch_assoc($rs)) {
			$datah = $DASH->get_dashboard_assoc($row['dashboard_id'],$row['dashboard_date']);
			$dateArray[$row['dashboard_date']] = (float)$datah[$DASH->maps['offerrate']['field']];//$DASH->format_field($datah[$DASH->maps['offerrate']['field']],'offerrate','key');
		}
		$historyArray = array();
		foreach($dateArray as $d=>$v){
			$ts = strtotime($d);
			$y = (int)date('Y',$ts);
			$m = (int)date('n',$ts)-1;
			$d = (int)date('j',$ts);
			$Ymd = (int)date('Ymd',$ts);
			$historyArray[] = array('Ymd'=>$Ymd,'y'=>$y,'m'=>$m,'d'=>$d,'v'=>$v);
		}
		$out = array(
			'name'=>$DASH->format_field($data[$DASH->maps['retailmarketer']['field']],'retailmarketer','key').', '.$DASH->format_field($data[$DASH->maps['producttype']['field']],'producttype','key').', '.$DASH->format_field($data[$DASH->maps['term']['field']],'term','key').', '.$DASH->format_field($data[$DASH->maps['renewable']['field']],'renewable','key'),
			'history' =>$historyArray,
		);
	}
}
else{
	$out_text .= '<form name="resultForm" id="resultForm" method="post" action="'.$_SERVER['PHP_SELF'].'" onsubmit="return false;">';
	$out_text .= '<div style="border:solid 1px #0055E3;">
	<table width="100%" cellpadding="4" cellspacing="0" class="sortable">';
	$data_array = array();
	$data_detail_array = array();
	$row_num = 0;
	$first = true;
	$search_array = array();
	foreach($search as $k){
		if(isset($_REQUEST[$k])){
			$search_array[$k] = $_REQUEST[$k];
		}
	}
	$search_array['discontinueddate'] = '_1969-12-30';
	$total_entries = $DASH->get_dashboard_query_count($search_array,$start_entries,$entries,$sort_entries);
	if($total_entries==0){
		$out_text .= '<tr><td><div>&nbsp;</div><div class="error">No Results Found</div><div>&nbsp;</div></td></tr>';
	}
	else{
		$sql = $DASH->get_dashboard_query($search_array,$start_entries,$entries,$sort_entries);
		$rs = $DRW->query($sql,$DRW_read);
		while($row = $DRW->fetch_row($rs)) {
			$data_array[$row_num] = array();
			$data_detail_array[$row_num] = array();
			$data = $DASH->get_dashboard_assoc($row[0]);
			if($first){
				$out_text .= '<thead><tr><th class="toptable">Expand/Collapse</th>';
				foreach($display as $k){
					$out_text .= '<th class="toptable" nowrap="nowrap">'.show_sort($DASH->get_label($k,'key'),$k,$sort_entries).'</th>';
					$data_array[$row_num][] = $DASH->get_label($k,'key');
				}
				$out_text .= '</tr></thead><tbody>';
				foreach($DASH->maps as $k=>$v){
					if(!in_array($k,$display) && !in_array($k,$no_display)){
						$data_detail_array[$row_num][] = $DASH->get_label($k,'key');
					}
				}
				$first = false;
				$row_num++;
			}
			/*$out_text .= '<tr><td class="bodytext" valign="top">';
			$history_count = $DASH->get_dashboard_query_count(array(),0,0,'',$data['dashboard_id']);
			if($history_count>1){
				$out_text .= '<input type="checkbox" name="graph_'.$row_num.'" id="graph_'.$row_num.'" value="'.$data['dashboard_id'].'" />';
			}
			else{
				$out_text .= '&nbsp;';
			}</td>*/
			$out_text .= '<tr><td class="bodytext" valign="top"><a href="#" onclick="show_result_detail(\''.$row_num.'\'); return false;"><img name="detail_img_'.$row_num.'" id="detail_img_'.$row_num.'" src="images/plus.jpg" border="0" /></a></td>';
			foreach($display as $k){
				$out_text .= '<td class="bodytext" valign="top">'.htmlspecialchars($DASH->format_field($data[$DASH->maps[$k]['field']],$k,'key')).'</td>';
				$data_array[$row_num][] = $DASH->format_field($data[$DASH->maps[$k]['field']],$k,'key');
			}
			$out_text .= '</tr>';
			$out_text .= '<tr style="display:none;" id="detail_'.$row_num.'"><td colspan="'.(count($display)+1).'" class="bodytext" valign="top">';
			foreach($DASH->maps as $k=>$v){
				if(!in_array($k,$display) && !in_array($k,$no_display)){
					$out_text .= '<div>'.htmlspecialchars($DASH->get_label($k,'key')).': '.htmlspecialchars($DASH->format_field($data[$DASH->maps[$k]['field']],$k,'key')).'</div>';
					$data_detail_array[$row_num][] = $DASH->format_field($data[$DASH->maps[$k]['field']],$k,'key');
				}
			}
			$out_text .= '</td></tr>';
			$row_num++;
		}
	}
	$entries_show = $entries + $start_entries;
	if($total_entries<$entries_show){
		$entries_show = $total_entries;
	}
	$entries_start_show = $start_entries + 1;
	if($entries_start_show>$total_entries){
		$entries_start_show = $total_entries;
	}
	$out_text .= '</tdbody></table></div>';
	if($total_entries>0){
		$out_text .= '<div>
		<div class="error" style="float:left;">Showing '.$entries_start_show.' to '.$entries_show.' of '.$total_entries.' entries</div><div class="bodytext" style="float:right;font-style:italic;font-size:smaller;">Duplicate products may display but were observed from unique sources</div>
		<div style="clear:both;">'.show_limit($total_entries,$start_entries,$entries).'</div>
		</div>';
	}
	$out_text .= '<input type="hidden" name="total_rows" id="total_rows" value="'.$row_num.'" /></form>';
}
if($csv){
	$sql = "REPLACE INTO dashboard_export_history (userID,deh_date) VALUES ('".$_SESSION['sess_userID']."',NOW())";
	$DRW->query($sql,$DRW_main);
}
ob_end_clean();
if($csv){
	header("Content-Type: text/csv");
	header('Content-Disposition: attachment;filename="dashboard.csv"');
	foreach($data_array as $k=>$row){
		$full_row = array_merge($row,$data_detail_array[$k]);
		echo $DASH->csvRow($full_row);
	}
}
elseif($json){
	//header("Content-Type: application/json");
	echo json_encode($out);
}
else{
	//header("Content-Type: text/plain");
	echo $out_text;
}
exit;

function show_limit($total_entries=0,$start_entries=0,$entries=10){
	$out = '';
	if($total_entries>0){
		if($start_entries>=$entries){
			$prev = $start_entries - $entries;
			$out .= '<a href="#" onclick="page_info_container(\''.$prev.'\'); return false;" class="bluelink">Previous</a>';
		}
		if($start_entries<$total_entries && (($start_entries+($entries*2))<$total_entries || ($total_entries - ($start_entries + $entries))>0)){
			if($out!=''){
				$out .= ' &nbsp; ';
			}
			$next = $start_entries + $entries;
			$out .= '<a href="#" onclick="page_info_container(\''.$next.'\'); return false;" class="bluelink">Next</a>';
		}
	}
	return $out;
}

function show_sort($label='',$key='',$sort_entries=''){
	$out = '';
	$desc = false;
	if(strlen($sort_entries)>0){
		$firstchar = substr($sort_entries,0,1);
		if($firstchar=='-'){
			$sort_entries = substr($sort_entries,1);
			if($key==$sort_entries){
				$desc = true;
			}
		}
	}
	if($key!=$sort_entries || $desc){
		$out .= '<a href="#" onclick="sort_info_container(\''.$key.'\'); return false;" class="topLinks">'.htmlspecialchars($label);
		if($desc){
			$out .= '<img src="images/up.gif" border="0" style="vertical-align:bottom;" width="15" height="15" /></a>';//&uarr;
		}
		else {
			$out .= '</a><img src="images/spacer.gif" border="0" style="vertical-align:bottom;" width="15" height="15" />';
		}
	}
	else{
		$out .= '<a href="#" onclick="sort_info_container(\'-'.$key.'\'); return false;" class="topLinks">'.htmlspecialchars($label).'<img src="images/down.gif" border="0" style="vertical-align:bottom;" width="15" height="15" /></a>';//&darr;
	}
	return $out;
}
?>