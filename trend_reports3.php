<?php  
$PAGE_HEADING = "Trend Reports";
$TITLE = "Competiscan $PAGE_HEADING";
$HEAD = '<script src="includes/trend_reports.js?v=20130924" type="text/javascript"></script><link rel="stylesheet" href="js_calendar/calendar.css" media="screen" type="text/css" /><script type="text/javascript" src="js_calendar/calendar.js?new=20110531"></script>';
if(isset($_REQUEST['trend_id']) && $_REQUEST['trend_id']!=''){
	$BODYTAG = ' onload="showTrend_id('.(int)$_REQUEST['trend_id'].');"';
}
include 'header_top.php';
require_once('includes/checklogin.php');

$savedArray = array();
$trendArray = array();
$sql = "SELECT sectorID,trend_id FROM cscan_sector_users WHERE userID='{$_SESSION['sess_userID']}'";
$savedQuery = $DRW->query($sql,$DRW_read);
while($row = $DRW->fetch_row($savedQuery)) {
	$savedArray[$row[0]] = trend_sectorName($row[0]);
	$trendArray[$row[0]] = $row[1];
}

if(isset($_GET['sid'])){
	$sql = "DELETE FROM cscan_sector_users WHERE sectorID=".(int)$_GET['sid']." AND userID={$_SESSION['sess_userID']}";
	$DRW->query($sql,$DRW_main);
	ob_end_clean();
	header("Location: {$_SERVER['PHP_SELF']}");
	exit; 
}

if(isset($_POST['sendt'])){
	if(isset($_POST['alert'])) {
		$alertArray = $_POST['alert'];
	}
	else {
		$alertArray = array();
	}
	foreach($alertArray as $sectorID){
		if(!key_exists($sectorID,$savedArray)){
			$sql = "INSERT IGNORE INTO cscan_sector_users (sectorID,userID,trendSent) VALUES (".(int)$sectorID.",{$_SESSION['sess_userID']},NOW())";
			$DRW->query($sql,$DRW_main);
		}
	}
	
	$all_alerts = explode(',',$_POST['all_alerts']);
	foreach($all_alerts as $sectorID){
		if(!in_array($sectorID,$alertArray)){
			$sql = "DELETE FROM cscan_sector_users WHERE sectorID=".(int)$sectorID." AND userID={$_SESSION['sess_userID']}";
			$DRW->query($sql,$DRW_main);
		}
	}
	
	ob_end_clean();
	header("Location: {$_SERVER['PHP_SELF']}");
	exit; 
}
?>
<table width="100%" border="0" cellpadding="5" cellspacing="4" class="bodytext">
<tr><td width="45%" valign="top">
<form name="searchForm" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<?php
$javascript = '';
$sectorID = array();
$categoryID = array();
$subCategoryID = array();
$subSubCategoryID = array();
$trend_date_from = '';
$trend_date_to = '';

if(isset($_REQUEST['send'])){
	if(!empty($_REQUEST['sectorID'])){
		$sectorID = $_REQUEST['sectorID'];
	}
	if(!empty($_REQUEST['categoryID'])){
		$categoryID = $_REQUEST['categoryID'];
	}
	if(!empty($_REQUEST['subCategoryID'])){
		$subCategoryID = $_REQUEST['subCategoryID'];
	}
	if(!empty($_REQUEST['subSubCategoryID'])){
		$subSubCategoryID = $_REQUEST['subSubCategoryID'];
	}
	$trend_date_from = $_REQUEST['trend_date_from'];
	$trend_date_to = $_REQUEST['trend_date_to'];
	$_SESSION['tr_search'] = array($sectorID,$categoryID,$subCategoryID,$subSubCategoryID,$trend_date_from,$trend_date_to);
}
elseif(isset($_SESSION['tr_search'])){
	list($sectorID,$categoryID,$subCategoryID,$subSubCategoryID,$trend_date_from,$trend_date_to) = $_SESSION['tr_search'];
}

$sri = 0;
$dap = 0;
$dai = 0;
$displayArray = array();

$displayArray[$dap] = array();
$displayArray[$dap][$dai] = array();

$displayArray[$dap][$dai]['show'] = true;
$displayArray[$dap][$dai]['title'] = 'Date From :';
$displayArray[$dap][$dai]['value'] = '<input type="text" name="trend_date_from" id="trend_date_from" size="15" maxlength="20" class="input_box" value="'.htmlspecialchars($trend_date_from, ENT_QUOTES).'" /> <a href="#" onclick="displayCalendar(document.searchForm.trend_date_from,\'mm/dd/yyyy\',this); return false;"><img name="popcal3" src="js_calendar/images/getcal.gif" border="0" alt="" style="vertical-align:bottom;" /></a> (MM/DD/YYYY)';
$dai++;
$displayArray[$dap][$dai]['show'] = true;
$displayArray[$dap][$dai]['title'] = 'Date To :';
$displayArray[$dap][$dai]['value'] = '<input type="text" name="trend_date_to" id="trend_date_to" size="15" maxlength="20" class="input_box" value="'.htmlspecialchars($trend_date_to, ENT_QUOTES).'" /> <a href="#" onclick="displayCalendar(document.searchForm.trend_date_to,\'mm/dd/yyyy\',this); return false;"><img name="popcal3" src="js_calendar/images/getcal.gif" border="0" alt="" style="vertical-align:bottom;" /></a> (MM/DD/YYYY)';
$dai++;

$displayArray[$dap][$dai]['show'] = true;
$displayArray[$dap][$dai]['title'] = 'Sector :';
$displayArray[$dap][$dai]['value'] = '<select class="combo_box" id="sectorID" name="sectorID[]" size="3" multiple="multiple" onchange="getCat();">';
$ctextArray = array();
$sctextArray = array();
$ssctextArray = array();
$sector = getSector();
$scsc = array();
foreach($sector as $id=>$name){
	$scsc[$id] = $name;
	if(!in_array($id,$_SESSION['sess_sector'])){
		continue;
	}
	$displayArray[$dap][$dai]['value'] .= "<option value=\"$id\"";
	if(in_array($id,$sectorID)) {
		$displayArray[$dap][$dai]['value'] .= " selected=\"selected\"";
	}
	$displayArray[$dap][$dai]['value'] .= ">".htmlspecialchars($name)."</option>";
	$javascript .= "sectorArray['$id'] = new Array();\n";
	$category = getCategory($id);
	if($category!==0){
		foreach( $category as $cid=>$cname ) {
			$scsc[$cid] = $cname;
			if(!in_array($cid,$_SESSION['sess_category'])){
				continue;
			}
			$javascript .= "sectorArray['$id']['$cid'] = '".singleQuoteSafe($cname)."';\n";
			$javascript .= "categoryArray['$cid'] = new Array();\n";
			if(in_array($id,$sectorID)) {
				$ctext = "<option value=\"$cid\"";
				if(in_array($cid,$categoryID)) {
					$ctext .= " selected=\"selected\"";
				}
				$ctext .= ">".htmlspecialchars($cname)."</option>";
				if(count($sectorID)==1 && $sectorID[0]=='90'){
					/*
					(90)
					178: Payment Cards
					179: Credit Access Checks
					231: Ancillary Products/Svc.
					*/
					$reordermap = array('178','179','231');
					$ind = array_search($cid,$reordermap);
					if($ind!==false){
						$cname = 'Credit Cards�'.$ind;
					}
				}
				if(key_exists($cname,$ctextArray)){
					$cname .= $cid;
				}
				$ctextArray[$cname] = $ctext;
			}
			$scats = getSubCategory($cid);
			if($scats!==0){
				foreach( $scats as $scid=>$scname ) {
					$scsc[$scid] = $scname;
					if(!in_array($scid,$_SESSION['sess_subcategory'])){
						continue;
					}
					$javascript .= "categoryArray['$cid']['$scid'] = '".singleQuoteSafe($scname)."';\n";
					$javascript .= "subCategoryArray['$scid'] = new Array();\n";
					if(in_array($cid,$categoryID)){
						$sctext = "<option value=\"$scid\"";
						if(in_array($scid,$subCategoryID)) {
							$sctext .= " selected=\"selected\"";
						}
						$sctext .= ">".htmlspecialchars($scname)."</option>";
						if(count($categoryID)==1 && $categoryID[0]=='178'){
							/*
							(178)
							93: Payment Cards � Credit Cards
							92: Payment Cards � Charge Cards
							212: Payment Cards � Business Cards
							102: Payment Cards � Private Label Cards
							103: Payment Cards - Prepaid Cards
							91: Payment Cards � Corporate Cards
							*/
							$reordermap = array('93','92','212','102','103','91');
							$ind = array_search($scid,$reordermap);
							if($ind!==false){
								$scname = 'Payment Cards�'.$ind;
							}
						}
						if(key_exists($scname,$sctextArray)){
							$scname .= $scid;
						}
						$sctextArray[$scname] = $sctext;
					}
					$sscats = getSubCategory($scid);
					if($sscats!==0){
						foreach( $sscats as $sscid=>$sscname ) {
							$javascript .= "subCategoryArray['$scid']['$sscid'] = '".singleQuoteSafe($sscname)."';\n";
							if(in_array($scid,$subCategoryID)){
								$ssctext = "<option value=\"$sscid\"";
								if(in_array($sscid,$subSubCategoryID)) {
									$ssctext .= " selected=\"selected\"";
								}
								$ssctext .= ">".htmlspecialchars($sscname)."</option>";
								if(key_exists($sscname,$ssctextArray)){
									$sscname .= $sscid;
								}
								$ssctextArray[$sscname] = $ssctext;
							}
						}
					}
				}
			}
		}
	}
}
$displayArray[$dap][$dai]['value'] .= '</select><br />[Hold ctrl key for multiple selection]';
$dai++;

$displayArray[$dap][$dai]['show'] = true;
$displayArray[$dap][$dai]['title'] = 'Category :';
$displayArray[$dap][$dai]['value'] = '<select id="categoryID" name = "categoryID[]" class="combo_box" multiple="multiple" size ="3" onchange="getSubCat();">';
$displayArray[$dap][$dai]['value'] .= "<option value=\"\"";
$o_count = count($categoryID);
if($o_count==0 || ($o_count==1 && $categoryID[0]=='')) {
	$displayArray[$dap][$dai]['value'] .= " selected=\"selected\"";
}
$displayArray[$dap][$dai]['value'] .= ">Any</option>";
ksort($ctextArray);
$displayArray[$dap][$dai]['value'] .= implode('',$ctextArray);
$displayArray[$dap][$dai]['value'] .= '</select><br />[Hold ctrl key for multiple selection]';
$dai++;

$displayArray[$dap][$dai]['show'] = true;
$displayArray[$dap][$dai]['title'] = 'Sub Category :';
$displayArray[$dap][$dai]['value'] = '<select id="subCategoryID" name = "subCategoryID[]" class="combo_box" size ="3" multiple="multiple" onchange="getSubSubCat();">';
$displayArray[$dap][$dai]['value'] .= "<option value=\"\"";
$o_count = count($subCategoryID);
if($o_count==0 || ($o_count==1 && $subCategoryID[0]=='')) {
	$displayArray[$dap][$dai]['value'] .= " selected=\"selected\"";
}
$displayArray[$dap][$dai]['value'] .= ">Any</option>";
ksort($sctextArray);
$displayArray[$dap][$dai]['value'] .= implode('',$sctextArray);
$displayArray[$dap][$dai]['value'] .= '</select><br />[Hold ctrl key for multiple selection]';
$dai++;

if(!in_array('sub_sub',$_SESSION['sess_search_exclude'])){
	$displayArray[$dap][$dai]['show'] = true;
}
else{
	$displayArray[$dap][$dai]['show'] = false;
}
$displayArray[$dap][$dai]['title'] = 'Sub Sub Category :';
$displayArray[$dap][$dai]['value'] = '<select id="subSubCategoryID" name = "subSubCategoryID[]" class="combo_box" size ="3" multiple="multiple">';
$displayArray[$dap][$dai]['value'] .= "<option value=\"\"";
$o_count = count($subSubCategoryID);
if($o_count==0 || ($o_count==1 && $subSubCategoryID[0]=='')) {
	$displayArray[$dap][$dai]['value'] .= " selected=\"selected\"";
}
$displayArray[$dap][$dai]['value'] .= ">Any</option>";
ksort($ssctextArray);
$displayArray[$dap][$dai]['value'] .= implode('',$ssctextArray);
$displayArray[$dap][$dai]['value'] .= '</select><br />[Hold ctrl key for multiple selection]';
$dai++;

$w1 = 520;//620
$partblock = array();
foreach($displayArray as $part=>$displayPart){
	echo '<div id="div'.$part.'" style="width:'.$w1.'px;margin-top:8px;">';
	foreach($displayPart as $order=>$display){
		echo '<div id="div'.$part.'_'.$order.'" style="clear:left;width:'.$w1.'px;';
		if(!$display['show']){
			echo 'display:none;';
		}
		echo '" class="bodytext"><div style="width:150px;float:left;padding:4px;">'.$display['title'].'</div><div style="float:left;padding:4px;">'.$display['value'].'</div></div>';
	}
	echo '</div>';
}
echo '<script type="text/JavaScript">
<!--
	'.$javascript.'
//-->
</script>';
?>
<div style="clear:both;height:1px;">&nbsp;</div>
<div style="width:150px;float:left;padding:4px;">&nbsp;</div>
<div style="float:left;padding:4px;">
<input type="submit" name="submit" value="Search" class="submitbutton" /><input type="hidden" name="send" value="1" />
<input class="submitbutton" type="submit" name="clear_search" value="Clear Search" onclick="clearTrendSearch(); return false;" />
</div>
<div style="clear:both;">&nbsp;</div>
</form>
<?php 
function get_sector_kids($cid,&$category_ids=array()){
	global $DRW,$DRW_read,$DRW_main,$DRW_crm;
	if(in_array($cid,$category_ids)){
		return false;
	}
	$category_ids[] = $cid;
	$sqlc = "SELECT sectorID FROM cscan_sector WHERE parentID=$cid";
	$rsc = $DRW->query($sqlc,$DRW_read);
	while($rowc = $DRW->fetch_row($rsc)){
		get_sector_kids($rowc[0],$category_ids);
	}
	return true;
}
$selections = get_seccatsub(implode(',',$sectorID),implode(',',$categoryID),implode(',',$subCategoryID),implode(',',$subSubCategoryID));
$category_ids = array();
$as = array($subSubCategoryID, $subCategoryID, $categoryID, $sectorID);
foreach($as as $cids){
	$ind = array_search('',$cids);
	if($ind!==false){
		unset($cids[$ind]);
	}
	if(is_array($cids) && count($cids)>0){
		foreach($cids as $cid){
			get_sector_kids($cid,$category_ids);
		}
		break;
	}
}
$available_cats = array();
$where = '';
foreach($category_ids as $cid){
	if(!empty($cid)){
		$where .= ' OR category_id='.$cid;
	}
}
if($where!=''){
	$where  = '('.substr($where,4).')';
	if(!empty($trend_date_from)){
		$trend_date_db = date('Y-m-d',strtotime($trend_date_from));
		$where .= " AND trend_date>='".$trend_date_db."'";
	}
	if(!empty($trend_date_to)){
		$trend_date_db = date('Y-m-d',strtotime($trend_date_to));
		$where .= " AND trend_date<='".$trend_date_db."'";
	}
	$sqltwo = "SELECT trend_name,trend_link,trend_id,category_id,DATE_FORMAT(trend_date,'%m/%d/%Y') as trend_date_f FROM cscan_trend_report WHERE $where ORDER BY trend_date DESC,trend_name ASC";
	$query = $DRW->query($sqltwo,$DRW_read);
	echo '<div style="clear:right;border:solid 1px #000000;padding:4px;background-color:#E8E8FF;height:150px;overflow-y:scroll;">';
	if($DRW->num_rows($query)>0){
		while($row2 = $DRW->fetch_assoc($query)) {
			$trendname = $row2['trend_name'];
			$link = $row2['trend_link'];
			$trend_id = $row2['trend_id'];
			$cats = $row2['category_id'];
			$trend_date = $row2['trend_date_f'];
			$catname = trend_sectorName($cats);
			if(!isset($available_cats[$cats])){
				$available_cats[$cats] = $catname;
			}
			echo '<div class="bodytext" style="padding:4px;border-top:dashed 1px #000000;">
			<div><strong>'.$trend_date.' - '.htmlspecialchars($catname).'</strong></div><div style="padding-left:4px;"><a href="trend_report.php?trend_id='.$trend_id.'" target="_blank" class="HyperLink">'.htmlspecialchars($trendname).'</a></div></div>';
		}
	}
	else{
		echo '<div class="error" style="padding:4px;">No Reports Found</div>';
	}
	echo '</div>';
}
?>
</td><td valign="top">
<?php 
echo '<table width="100%" border="0" cellpadding="5" cellspacing="0" class="bodytext">
<tr class="subHead"><td><strong>Trend Report Email Alerts</strong></td><td><strong>Last Sent</strong></td><td width="15%"><strong>Options</strong></td></tr>
<tr><td colspan="3" class="error">&nbsp;</td></tr>';
$className = 'white-bg';
if(is_array($savedArray) && count($savedArray) > 0) {
	asort($savedArray);
	foreach($savedArray as $sectorID=>$sectorName) {
		if ($className=='selected-bg1') {
			$className = 'white-bg';
		}
		else {
			$className = 'selected-bg1';
		}
		echo " <tr class=\"$className\"><td class=\"bodytext\" valign=\"top\">".htmlspecialchars($sectorName)."</td><td class=\"bodytext\" valign=\"top\">";
		if($trendArray[$sectorID]!=0) {
			$sqltwo = "SELECT trend_name FROM cscan_trend_report WHERE trend_id={$trendArray[$sectorID]}";
			$query = $DRW->query($sqltwo,$DRW_read);
			$row2 = $DRW->fetch_row($query);
			echo "<a href=\"trend_report.php?trend_id={$trendArray[$sectorID]}\" target=\"_blank\">".htmlspecialchars($row2[0])."</a>";
		}
		else {
			echo '&nbsp;';
		}
		echo "</td><td class=\"bodytext\" valign=\"top\" nowrap=\"nowrap\"><a href=\"{$_SERVER['PHP_SELF']}?sid=$sectorID\" onclick=\"return confirm('Delete?');\"><img src = \"images/drop.png\" border=\"0\" style=\"vertical-align:bottom;\" /></a>&nbsp;<a class=\"HyperLink\" href=\"{$_SERVER['PHP_SELF']}?sid=$sectorID\" onclick=\"return confirm('Delete?');\">Delete</a></td></tr>";
	}
}
else{
	echo '<tr><td align="center" class="error" colspan="3">No Email Alerts Found</td></tr>';
}
echo '</table>';

if(is_array($available_cats) && count($available_cats)>0){
	?>
	<div style="padding:4px;border-top:dashed 1px #000000;">&nbsp;</div>
	<form name="trendForm" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<?php 
	asort($available_cats);
	foreach($available_cats as $cat=>$catname){
		echo '<div class="bodytext"><label><input type="checkbox" name="alert[]" value="'.$cat.'"';
		if(key_exists($cat,$savedArray)) {
			echo ' checked="checked"';
		}
		echo ' />'.htmlspecialchars($catname).'</label></div>';
	}
	?>
	<div class="bodytext"><input type="submit" name="submit" value="Receive Alert" class="submitbutton" /><input type="hidden" name="sendt" value="1" /><input type="hidden" name="all_alerts" value="<?php echo implode(',',array_keys($available_cats)); ?>" /></div>
	</form>
	<?php 
}
?>
</td></tr>
</table>
<?php 
include 'footer_bottom.php';

function trend_sectorName($cat){
	global $DRW,$DRW_read,$DRW_main,$DRW_crm;
	
	$sectorName = '';
	$parentID = $cat;
	$times = 0;
	do {
		$sectorQuery = "SELECT sectorName,parentID from cscan_sector where sectorID=".$parentID;
		$sectorQuery = $DRW->query($sectorQuery,$DRW_read);
		$sectorRow = $DRW->fetch_assoc($sectorQuery);
		if($sectorName==''){
			$sectorName = $sectorRow['sectorName'];
		}
		else{
			$sectorName = $sectorRow['sectorName'].' - '.$sectorName;
		}
		$parentID = $sectorRow['parentID'];
		$times++;
	} while(!empty($parentID) && $times<100);
	
	return $sectorName;
}
?>