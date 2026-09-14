<?php
$PAGE_HEADING = "Saved Searches";
$TITLE = "Competiscan $PAGE_HEADING";
include 'header_top.php';
require_once('includes/checklogin.php');
require_once('includes/paginator.php');       //paginator class. 
require_once('includes/paginator_html.php');  //paginator_html class.
//require_once('includes/sphinx_function2.php');  //sphinx functions.
$id = $_SESSION['sess_userID'];

/*######## Start for Page permission ########*/ 
if(!defined('ENV')){
    define('ENV',getenv('SERVER_NAME'));
   }
  
//if(ENV == 'localhost' || ENV == 'demo.competiscan.com'){
    if(ENV == 'localhost'){
        $siteUrl='http://localhost/competiscan.com/';
    }elseif(ENV == 'demo.competiscan.com'){
        $siteUrl='http://demo.competiscan.com/';
    }else{
        $siteUrl='https://www.competiscan.com/';
    } 
    $page_permission = getPagePermission();
	if(!empty($_SESSION['sess_search_page_permission'])){
		$page_permission=$_SESSION['sess_search_page_permission'];
	}
    $redirect_page='';
    if(!empty($page_permission)){
        if(!in_array('power_search',$page_permission) AND in_array('trend_reports',$page_permission)){
            $redirect_page=$siteUrl.'trend_reports.php';

        }else if(!in_array('power_search',$page_permission) AND !in_array('trend_reports',$page_permission) AND in_array('retrieval_services',$page_permission)){
            $redirect_page=$siteUrl.'productPickup.php';
        }
        if(!in_array('power_search',$page_permission) AND $redirect_page!=''){
           header("Location: $redirect_page");
            die; 
        }           
    }else{
        if(!empty($_SESSION['sess_dashboard'])) {
            $redirect_page=$siteUrl.'dashboard.php';
        }else{
            $redirect_page=$siteUrl.'quickHelp.php';
        } 
        header("Location: $redirect_page");
        die;
    }

//}    
 /*######## End for Page permission ########*/


if(isset($_GET['delID'])) {
	$delID = $_GET['delID'];
	$sql = "SELECT searchName FROM cscan_search WHERE ID='".$DRW->real_escape_string($delID)."'";
	$result = $DRW->query($sql,$DRW_read);
	$rs = $DRW->fetch_array($result);
	
	$count_result = $DRW->query("SELECT COUNT(*) FROM cscan_search WHERE userID='$id'",$DRW_read);
	$count = $DRW->fetch_row($count_result);
	if($count[0]<=1){
		$sql = "UPDATE cscan_search SET emailAlert=0,saved=0,copied_ID=0,searchName='',lastSentDate='0000-00-00 00:00:00',sendTo='',weekday=7,notify='daily',mail_format='HTML' WHERE ID='".$DRW->real_escape_string($delID)."'";
		$DRW->query($sql,$DRW_main);
	}
	else{
		$sql = "DELETE FROM cscan_search WHERE ID='".$DRW->real_escape_string($delID)."'";
		$DRW->query($sql,$DRW_main);
		$sql = "DELETE FROM cscan_search_product WHERE ID='".$DRW->real_escape_string($delID)."'";
		$DRW->query($sql,$DRW_main);
	}
	ob_end_clean();
	header("Location: {$_SERVER['PHP_SELF']}?del=".urlencode($rs['searchName']));
	exit; 
}
if (isset($_POST['delIDs'])) {
	$delIDs = explode(',', $_POST['delIDs']);
	foreach ($delIDs as $delID) {
		$count_result = $DRW->query("SELECT COUNT(*) FROM cscan_search WHERE userID='$id'",$DRW_read);
	        $count = $DRW->fetch_row($count_result);
		if($count[0] <= 1){
	                $sql =	"UPDATE cscan_search SET emailAlert=0,saved=0,copied_ID=0,searchName='',lastSentDate='0000-00-00 00:00:00',sendTo='',weekday=7,notify='daily',mail_format='HTML' WHERE ID='".
				$DRW->real_escape_string($delID)."'";
                	$DRW->query($sql,$DRW_main);
        	}
	        else{
			$sql = "DELETE FROM cscan_search WHERE ID='".$DRW->real_escape_string($delID)."'";
                	$DRW->query($sql,$DRW_main);
        	        $sql = "DELETE FROM cscan_search_product WHERE ID='".$DRW->real_escape_string($delID)."'";
	                $DRW->query($sql,$DRW_main);
		}
	}
	ob_end_clean(); //where would rogue output even come from?
	header("Location: {$_SERVER['PHP_SELF']}?numdel=".count($delIDs));
        exit;
}

$sql = "SELECT queryDate,ID,searchName,searchType,searchview,DATE_FORMAT(queryDate,'%m/%d/%Y') AS queryDate_f,search_count,search_count_date FROM cscan_search WHERE userID='$id' AND saved=1";

$result = $DRW->query($sql,$DRW_read); 

if(isset($_REQUEST['page'])) {
	$page = (int)$_REQUEST['page'];
}
else {
	$page = 1;
}

if(isset($_POST['send'])) {
	while($row = $DRW->fetch_assoc($result)) {
		if(isset($_POST['name'.$row['ID']])){
			$name = trim($_POST['name'.$row['ID']]);
			
			$insertQuery = "UPDATE cscan_search SET queryDate='{$row['queryDate']}',searchName='".$DRW->real_escape_string($name)."' WHERE ID='".$row['ID']."'";
			$DRW->query($insertQuery,$DRW_main);
		}
	}
	ob_end_clean();
	header("Location: {$_SERVER['PHP_SELF']}?save=1&page=$page");
	exit;
}

$align = '';
if(isset($_GET['save'])) {
	$message = 'Saved Searches have been updated';
}
elseif(isset($_GET['del'])) {
	$message = $_GET['del'].' has been deleted.&nbsp;&nbsp;';
	$align = ' align="right"';
} 
elseif(isset($_GET['numdel'])) {
	$message = $_GET['numdel'].' search(es) deleted.&nbsp;&nbsp;';
        $align = ' align="right"';
}
else {
	$message = '&nbsp;';
}

?> 

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script type="text/JavaScript">
<!--
function delConfirm() {
	return confirm('Delete?');
}
function shareColleague(ssid) {
	var wind = window.open('sendLink.php?send_mode=3&ssid='+ssid,"share","left=20, top=20, scrollbars=yes, resizable=yes, width=625, height=475");
	wind.focus();
}
function chk_search_name(searchName) {
	if(searchName=='') {
		alert('Search Name cannot be blank');
		return false;
	}
	return true;
}
function select_all_checks(check_on) {
	if (check_on) {
		$('.del_checkboxes').prop('checked', true);
	} else {
		$('.del_checkboxes').prop('checked', false);
	}
	return false;
}
function delConfirmAll() {
	var num_checked = 0;
	var delID_arr = [];
	$('.del_checkboxes').each(function() {
		if ($(this).prop('checked')) {
			num_checked++;
			delID_arr.push($(this).attr('id'));
		}
	});
	if (num_checked == 0) {
		alert('None selected');
		return false;
	} else {
		if (confirm('Delete '+num_checked+ ' search(es)?')) {
			var delIDs = delID_arr.join();
			$('#delIDs').val(delIDs);
			$('#delSelectedForm').submit();
		}
	}
}
//-->
</script>

<form method="post" name="delSelectedForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" id="delSelectedForm">
	<input type='hidden' name='delIDs' id='delIDs' value='' />
</form>

<form method="post" name="delForm" action="<?php echo $_SERVER['PHP_SELF'].'?page='.$page; ?>">
<table width="100%" border="0" cellpadding="5" cellspacing="0" class="bodytext">
  <?php 
$num_of_rows = $DRW->num_rows($result);
if($num_of_rows > 0){
	$hours = 4;
	$datecompare = date('Y-m-d H:i:s',time()-(3600*$hours));
	$pagelimit = 10;
	$a = new Paginator_html($page,$num_of_rows);
  	$a->set_Limit($pagelimit);
  	$a->set_Links(3);
	$limit1 = $a->getRange1();
	$limit2 = $a->getRange2();
	$sql .= " ORDER BY priority,queryDate DESC LIMIT $limit1,$limit2";
        $result = $DRW->query($sql,$DRW_read); 
?>
    <tr> 
      <td class="subHead" width="15%"><strong>Search Name</strong></td>
      <td class="subHead" width="11%"><strong>Search Date</strong></td>
      <td class="subHead" width="52%"><strong>Search Criteria</strong></td>
<!--      <td class="subHead" width="7%"><strong>Results</strong></td>-->
      <td width="15%" align="center" class="subHead"><strong>Options</strong></td>
      <td class="subHead" width="7%" align="center"><strong>&#10004;</strong></td>
    </tr>
    <tr>
      <td colspan="4" class="error"<?php echo $align; ?>><?php echo $message; ?></td>
      <td colspan="1" style="text-align: right;">
	<a href='#' class="HyperLink" onClick="return select_all_checks(true);">Select All</a><br />
	<a href='#' class="HyperLink" onClick="return select_all_checks(false);">Un-Select All</a>
      </td>
    </tr>
    <?php
	$className = 'white-bg';
	while($rs = $DRW->fetch_array($result)) {
		list($displayKeywords) = getKeywords($rs['ID']);
		if($className=='selected-bg1') $className = 'white-bg';
		else $className = 'selected-bg1';
?>
    <tr class = "<?php echo $className; ?>"> 
      <td valign="top" class="bodytext"><input class="input_box" type="text" name="<?php echo 'name'.$rs['ID']; ?>" size = "20" value= "<?php echo htmlspecialchars($rs['searchName'], ENT_QUOTES);?>" /><br /><a href="sendLink.php?send_mode=3&amp;ssid=<?php echo $rs['ID']; ?>" onclick="shareColleague(<?php echo $rs['ID']; ?>); return false;" class="HyperLink">Share Search</a></td>
      <td valign="top" class="bodytext"><?php echo $rs['queryDate_f']; ?></td>
      <td class="bodytext" style="margin-bottom:8px;" valign="top"> 
<?php 
	echo $displayKeywords;
?>
      </td>
<!--      <td class="bodytext" valign="top" style="margin-bottom:8px;"> 
<?php
	/*if($rs['search_count_date']>=$datecompare && $rs['search_count_date']>=$rs['queryDate']){
		echo number_format($rs['search_count']);
	}
	else{
		//list($selectQuery) = doQuery($rs['ID'],true,'',false,-1,false,false,false,true);
                list($selectQuery) = doQuerytestsphinx($rs['ID'],true,'',false,-1,false,false,false,true);
                
		$saved_search_result = $DRW->query($selectQuery,$DRW_read);
		$count = $DRW->fetch_row($saved_search_result);
		echo number_format($count[0]);
		
		$uQuery = "UPDATE cscan_search SET queryDate='{$rs['queryDate']}',search_count=".(int)$count[0].",search_count_date=NOW() WHERE ID='".$rs['ID']."'";
		$DRW->query($uQuery,$DRW_main);
	}
         
         */
?>
      </td>-->
      <td class="bodytext" valign="top" align="center" style="margin-bottom:8px;">
      <table border="0" cellpadding="0" cellspacing="4" class="bodytext" style="margin-top: -4px;">
        <tr><td><a href="fullsearch.php?ssid=<?php echo $rs['ID'];?>"><img src="images/edit.png" border="0" /></a></td><td><a class="HyperLink" href ="fullsearch.php?ssid=<?php echo $rs['ID'];?>">Edit</a></td></tr>
        <tr><td><a href="fullresults.php?ssid=<?php echo $rs['ID'];?>"><img src="images/yes.gif" border="0" /></a></td><td><a class="HyperLink" href="fullresults.php?ssid=<?php echo $rs['ID'];?>">Run</a></td></tr>
        <tr><td><a href="savedsearch.php?delID=<?php echo $rs['ID'];?>" onclick="return delConfirm();"><img src = "images/drop.png" border="0" /></a></td><td><a class="HyperLink" href="savedsearch.php?delID=<?php echo $rs['ID'];?>" onclick="return delConfirm();">Delete</a></td></tr>
       </table>
      </td>
      <td style="text-align: center;">
	<input type="checkbox" class="del_checkboxes" id="<?php echo $rs['ID']; ?>" />
      </td>
    </tr>
<?php
	}
	?>
	<tr>
      <td colspan="6">
	<input type="submit" name="submit" value="Update" class="submitbutton" />
	<input type="button" name="delete_selected" value="Delete Selected" class="submitbutton" onClick="return delConfirmAll();" />
	<input type="hidden" name="send" value="1" />
      </td>
    </tr>
    <?php
	if($num_of_rows > $pagelimit){
		echo '<tr><td colspan="6" align="center"><strong>';
		$a->firstPreviousNextLast(); 
		echo '</strong></td></tr>';
	}
}
else{
?>
  <tr><td class="error" align="center">No Saved Searches Found</td></tr>
<?php
}
?>
</table>
</form>
<?php
include 'footer_bottom.php';
?>
