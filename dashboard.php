<?php 
$PAGE_HEADING = "Retail Energy Pricing Dashboard";
$TITLE = "Competiscan $PAGE_HEADING";  
$HEAD = '<link rel="stylesheet" href="includes/jquery/jquery-ui.css" /><script type="text/javascript" src="includes/jquery/jquery.min.js"></script><script type="text/javascript" src="includes/jquery/jquery-ui.min.js"></script><script type="text/javascript" src="includes/google/jsapi.js"></script>
<script type="text/javascript" src="includes/jquery/jquery.tokeninput.js"></script><link rel="stylesheet" href="includes/jquery/token-input.css" />
<script type="text/javascript" src="includes/dashboard.js?v=20140515"></script><style type="text/css">.no-close .ui-dialog-titlebar {display: none;}</style>';

include 'header_top.php';
require_once('includes/checklogin.php');

if(empty($_SESSION['sess_dashboard'])) {
	ob_end_clean();
	header("Location: fullsearch.php?searchview=2");
	exit;
}

require_once('includes/dashboardData.php');
$DASH = new dashboardData();

$search_layout = array(
	1 => array(
		'electricitynaturalgas',
		'state',
		'edc',
	),
	2 => array(
		'retailmarketer',
		'producttype',
		'offerrate',
	),
	3 => array(
		'term',
		'renewable',
		'earlyterminationfee',
		'monthlyfee',
	),
);
//move this into dashboardData?
//$sql = "SELECT DATE_FORMAT(MAX(deh_date),'%m/%d/%Y') FROM dashboard_export_history where userID =".$_SESSION['sess_userID'];
$sql = "SELECT DATE_FORMAT(MAX(import_file_date),'%m/%d/%Y')  FROM cscan_import_file WHERE import_file_table='".$DASH->map_table."' AND import_file_inactive=0";
$rs = $DRW->query($sql,$DRW_read);
$data = $DRW->fetch_row($rs);
if(!empty($data[0])){
	echo '<div id="export_search" class="bodytext" style="float:left;"><span class="error">Date of most recent import: '.$data[0].'</span></div>';
}
echo '<div id="save_search" class="bodytext" style="float:right;"><form name="dsave_search" action="'.$_SERVER['PHP_SELF'].'" method="post" onsubmit="return false;">
<img name="waitss" id="waitss" src="images/searching.gif" border="0" style="display:none;" />
<strong>Saved Search</strong>
<input type="text" class="input_box" size="20" maxlength="40" name="dsave_search_name" id="dsave_search_name" value="" /><img name="show_dsave_search" id="show_dsave_search" src="images/plus.jpg" border="0" style="cursor:pointer;" title="show all" />
<input type="button" name="save_dsave_search" id="save_dsave_search" value="save new" class="submitbutton" style="display:none;" />
<input type="button" name="clear_dsave_search" id="clear_dsave_search" value="clear" class="submitbutton" style="display:none;" />
<input type="button" name="delete_dsave_search" id="delete_dsave_search" value="delete" class="submitbutton" style="display:none;" />
<input type="hidden" name="dsave_search_id" id="dsave_search_id" value="" />
</form></div><div style="clear:both;"></div>';
echo '<div><form name="searchForm" id="searchForm" method="post" action="'.$_SERVER['PHP_SELF'].'" onsubmit="return false;">';
foreach($search_layout as $col=>$sl){
	echo '<div style="width:33%;float:left;">';
	foreach($sl as $sk){
		echo '<div class="bodytext"><div><strong>'.htmlspecialchars($DASH->get_label($sk,'key')).'</strong></div><div>'.$DASH->search_field($sk).'</div><div>&nbsp;</div></div>';
	}
	echo '</div>';
}
echo '<div class="bodytext" style="clear:left;"><div><strong>Show <select name="entries" id="entries" size="1" class="combo_box" style="width:100px;">';
$ents = array(10,25,50,100);
foreach($ents as $ent){
	echo '<option value="'.$ent.'"';
	if(!empty($entries) && $ent==$entries){
		echo ' selected="selected"';
	}
	echo '>'.$ent.'</option>';
}
echo '</select> Entries</strong></div><div>&nbsp;</div></div>';
echo '<div><input class="submitbutton" type="submit" name="search_button" id="search_button" value="Search" onclick="search_info_container(); return false;" /> &nbsp; <input class="submitbutton" type="submit" name="clear_button" id="clear_button" value="Clear Search" onclick="clear_info_container(); return false;" /></div>
<input type="hidden" name="start_entries" id="start_entries" value="" /><input type="hidden" name="sort_entries" id="sort_entries" value="" /></form></div>';

echo '<div><a name="info_container_top" style="visibility:hidden;">&nbsp;</a></div><div id="info_container"><div class="error"><img name="wait" id="wait" src="images/searching.gif" border="0" /></div></div>';

echo '<div>&nbsp;</div><div><form name="outputForm" id="outputForm" method="post" action="'.$_SERVER['PHP_SELF'].'" onsubmit="return false;"><input class="submitbutton" type="submit" name="csv_button" id="csv_button" value="Export All To CSV" onclick="show_csv(); return false;" /> &nbsp; <input class="submitbutton" type="submit" name="top_button1" id="top_button1" value="Top" onclick="move_page_top(); return false;" /></form></div>';

echo '<div><a name="dashboard_chart_top" style="visibility:hidden;">&nbsp;</a></div><div id="dashboard_chart" style="display:none;"><div id="chart"></div><div id="control"></div><div>&nbsp;</div><div><form name="buttonForm" id="buttonForm" method="post" action="'.$_SERVER['PHP_SELF'].'" onsubmit="return false;"><input class="submitbutton" type="submit" name="hide_button" id="hide_button" value="Hide Graph" onclick="hideRangeVisualization(); return false;" /> &nbsp; <input class="submitbutton" type="submit" name="top_button2" id="top_button2" value="Top" onclick="move_page_top(); return false;" /></form></div></div>';

echo '<div id="waitdiv" style="display:none;"><img name="wait" id="wait" src="images/searching.gif" border="0" style="display:block;margin-left:auto;margin-right:auto;margin-top:50px;" /></div>';

include 'footer_bottom.php';
?>