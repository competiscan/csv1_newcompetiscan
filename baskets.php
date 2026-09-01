<?php
$PAGE_HEADING = "Export Baskets";
$TITLE = "Competiscan $PAGE_HEADING";
include 'header_top.php';
require_once('includes/checklogin.php');
require_once('includes/paginator.php');       //paginator class. 
require_once('includes/paginator_html.php');  //paginator_html class.

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


$_SESSION['selected_productID'] = array();

if(isset($_GET['del'])){
	$basketid = (int)$_GET['bid'];
	
	if($basketid==0){
		$basket_name = 'Default Basket';
	}
	else{
		$sql = "SELECT basket_name FROM cscan_basket WHERE basket_id=$basketid AND userID={$_SESSION['sess_userID']}";
		$result = $DRW->query($sql,$DRW_read); 
		$rs = $DRW->fetch_array($result);
		$basket_name = $rs['basket_name'];
	}
	
	$sql = "DELETE FROM cscan_product_basket WHERE basket_id=$basketid AND userID={$_SESSION['sess_userID']}";
	$DRW->query($sql,$DRW_main);
	
	$sql = "DELETE FROM cscan_basket WHERE basket_id=$basketid AND userID={$_SESSION['sess_userID']}";
	$DRW->query($sql,$DRW_main);
	
	ob_end_clean();
	header("Location: {$_SERVER['PHP_SELF']}?done=".urlencode($basket_name));
	exit;
}
if (isset($_POST['delIDs'])) {
        $delIDs = explode(',', $_POST['delIDs']);
        foreach ($delIDs as $basketid) {
		$sql = "DELETE FROM cscan_product_basket WHERE basket_id=$basketid AND userID={$_SESSION['sess_userID']}";
	        $DRW->query($sql,$DRW_main);

        	$sql = "DELETE FROM cscan_basket WHERE basket_id=$basketid AND userID={$_SESSION['sess_userID']}";
	        $DRW->query($sql,$DRW_main);
        }
        ob_end_clean(); //where would rogue output even come from?
        header("Location: {$_SERVER['PHP_SELF']}?numdel=".count($delIDs));
        exit;
}


$Q = "SELECT basket_id,basket_name FROM cscan_basket WHERE userID='".$_SESSION['sess_userID']."'";
$rs = $DRW->query($Q,$DRW_read);

if(isset($_REQUEST['page'])) {
	$page = (int)$_REQUEST['page'];
}
else {
	$page = 1;
}

if(isset($_POST['send'])) {
	while($dataB = $DRW->fetch_row($rs)) {
		$basket_id = $dataB[0];
		$basket_name = $dataB[1];
		if(isset($_POST['name'.$basket_id])){
			$name = trim($_POST['name'.$basket_id]);
			if($name!=$basket_name){
				$insertQuery = "UPDATE cscan_basket SET basket_name='".$DRW->real_escape_string($name)."' WHERE basket_id='".$basket_id."' AND userID='".$_SESSION['sess_userID']."'";
				$DRW->query($insertQuery,$DRW_main);
			}
		}
	}
	ob_end_clean();
	header("Location: {$_SERVER['PHP_SELF']}?save=1&page=$page");
	exit;
}

$align = '';
if(isset($_GET['save'])) {
	$message = 'Export Baskets have been updated';
}
elseif(isset($_GET['done'])) {
	$message = $_GET['done'].' has been deleted';
	$align = ' align="right"';
}
elseif(isset($_GET['numdel'])) {
	$message = $_GET['numdel'].' basket(s) deleted.&nbsp;&nbsp;';
        $align = ' align="right"';
}
else {
	$message = '&nbsp;';
}

echo "
<script src='https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js'></script>
<script type=\"text/JavaScript\">
<!--
function delConfirm() {
	return confirm('Delete?');
}
function shareColleague(bid) {
	var wind = window.open('sendLink.php?send_mode=3&bid='+bid,'share','left=20, top=20, scrollbars=yes, resizable=yes, width=625, height=475');
	wind.focus();
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
                if (confirm('Delete '+num_checked+ ' basket(s)?')) {
                        var delIDs = delID_arr.join();
                        $('#delIDs').val(delIDs);
                        $('#delSelectedForm').submit();
                }
        }
}
//-->
</script>";
echo '
<form method="post" name="delSelectedForm" action="'.$_SERVER['PHP_SELF'].'" id="delSelectedForm">
        <input type="hidden" name="delIDs" id="delIDs" value="" />
</form>
<form method="post" name="upForm" action="'.$_SERVER['PHP_SELF'].'?page='.$page.'">
<table width="100%" border="0" cellpadding="5" cellspacing="0" class="bodytext">
<tr class="subHead">
    <td><strong>Name</strong></td>
    <td width="15%"><strong>Change Date</strong></td>
    <td width="9%"><strong>Results</strong></td>
    <td width="10%" align="left"><strong>&nbsp;Options</strong></td>
    <td width="4%" align="center"><strong>&#10004;</strong></td>
  </tr>
<tr>
    <td colspan="3" '.$align.' class="error">'.$message.'</td>
    <td colspan="2" style="text-align: right;">
        <a href="#" class="HyperLink" onClick="return select_all_checks(true);">Select All</a><br />
        <a href="#" class="HyperLink" onClick="return select_all_checks(false);">Un-Select All</a>
      </td>
</tr>';

/*
$bq = "SELECT DATE_FORMAT(MAX(basket_date),'%m/%d/%Y'),COUNT(*) FROM cscan_product_basket WHERE basket_id=0 AND userID={$_SESSION['sess_userID']}";
$rsb = $DRW->query($bq,$DRW_read);
$datab = $DRW->fetch_row($rsb);
$basket_date = $datab[0];
$basket_count = $datab[1];

echo "<tr class=\"selected-bg1\"><td class=\"bodytext\" valign=\"top\">Default Basket</td><td class=\"bodytext\" valign=\"top\">$basket_date</td><td class=\"bodytext\" valign=\"top\">$basket_count</td><td class=\"bodytext\" valign=\"top\"><table border=\"0\" cellpadding=\"0\" cellspacing=\"4\" class=\"bodytext\">
<tr><td><a href=\"fullresults.php?bid=0\"><img src=\"images/edit.png\" border=\"0\" /></a></td><td><a class=\"HyperLink\" href=\"fullresults.php?bid=0\">View</a></td></tr>
</table></td></tr>";
*/
$className = '';
$num_of_rows = $DRW->num_rows($rs);
if($num_of_rows > 0){
	$pagelimit = 10;
	$a = new Paginator_html($page,$num_of_rows);
  	$a->set_Limit($pagelimit);
  	$a->set_Links(3);
	$limit1 = $a->getRange1();
	//if($limit1 < 0) $limit1 = 0;
	$limit2 = $a->getRange2();
	echo $Q .= " ORDER BY basket_name LIMIT $limit1,$limit2";
	$rs = $DRW->query($Q,$DRW_read);
	
	while($dataB = $DRW->fetch_row($rs)){
		$basket_id = $dataB[0];
		$basket_name = $dataB[1];
		if ($className=='selected-bg1') $className = 'white-bg';
		else $className = 'selected-bg1';
		
		$bq = "SELECT DATE_FORMAT(MAX(basket_date),'%m/%d/%Y'),COUNT(*) FROM cscan_product_basket WHERE basket_id=$basket_id AND userID={$_SESSION['sess_userID']}";
		$rsb = $DRW->query($bq,$DRW_read);
		$datab = $DRW->fetch_row($rsb);
		$basket_date = $datab[0];
		$basket_count = $datab[1];
			
		echo "<tr class=\"$className\"><td class=\"bodytext\" valign=\"top\">";
		
		echo "<input class=\"input_box\" type=\"text\" name=\"name{$basket_id}\" size=\"50\" value= \"".htmlspecialchars($basket_name, ENT_QUOTES)."\" /><br /><a href=\"sendLink.php?send_mode=3&amp;bid=$basket_id\" onclick=\"shareColleague($basket_id); return false;\" class=\"HyperLink\">Share Basket</a>";
		
		echo "</td><td class=\"bodytext\" valign=\"top\">$basket_date</td><td class=\"bodytext\" valign=\"top\">$basket_count</td><td class=\"bodytext\" valign=\"top\"><table border=\"0\" cellpadding=\"0\" cellspacing=\"4\" class=\"bodytext\">
		<tr><td><a href=\"fullresults.php?bid=$basket_id\"><img src=\"images/edit.png\" border=\"0\" /></a></td><td><a class=\"HyperLink\" href=\"fullresults.php?bid=$basket_id\">View</a></td></tr>";
		//if($_SESSION['sess_plevel']>0){ //removed 12/12 @ Nate's request, jm
			echo "<tr><td><a class=\"HyperLink\" href=\"productImages.php?pp=1&amp;bid=$basket_id\" target=\"_blank\"><img src=\"images/ppt.jpg\" border=\"0\" /></a></td><td><a class=\"HyperLink\" href=\"productImages.php?pp=1&amp;bid=$basket_id\" target=\"_blank\">PowerPoint</a></td></tr>";
			echo "<tr><td><a  class=\"HyperLink\" href=\"exportDocument.php?pdf=1&amp;bid=$basket_id\"><img src=\"images/pdf.jpg\" border=\"0\" /></a></td><td><a id=\"$basket_id\" class=\"HyperLink\" href=\"exportDocument.php?pdf=1&amp;bid=$basket_id\"><span id='pdf_hide_$basket_id'>PDF</span></a><div class=\"loder_hide\" id=\"loder_$basket_id\"><img src=\"images/searching.gif\" alt=\"\" border=\"0\" height=\"16\" width=\"16\" /></div></td></tr>";
		//}
		echo "<tr><td><a class=\"HyperLink\" href=\"{$_SERVER['PHP_SELF']}?bid=$basket_id&amp;del=1\" onclick=\"return delConfirm();\"><img src=\"images/drop.png\" border=\"0\" /></a></td><td><a class=\"HyperLink\" href=\"{$_SERVER['PHP_SELF']}?bid=$basket_id&amp;del=1\" onclick=\"return delConfirm();\">Delete</a></td></tr>
		</table></td>
		<td style='text-align: center;'>
		        <input type='checkbox' class='del_checkboxes' id='$basket_id' />
      		</td>
		</tr>";
	}
	echo '<tr>
		<td colspan="5">
			<input type="submit" name="submit" value="Update" class="submitbutton" />
			<input type="button" name="delete_selected" value="Delete Selected" class="submitbutton" onClick="return delConfirmAll();" />
			<input type="hidden" name="send" value="1" />
		</td>
	     </tr>';
	
	if($num_of_rows > $pagelimit){
		echo '<tr><td colspan="5" align="center"><strong>';
		//$a->previousNext(); 
                $a->firstPreviousNextLast(); 
		echo '</strong></td></tr>';
	}
}
else {
	echo '<tr><td colspan="5" class="error" align="center">No Export Baskets Found</td></tr>';
}
echo '</table>
</form>';
include 'footer_bottom.php';
?>
<script>
$(document).ready(function(){ 
    $(".loder_hide").hide(); 
    $(".HyperLink").click(function(){
         var id = $(this).attr('id');
         //alert(id);
        $("#pdf_hide_"+id).hide(); 
        $("#loder_"+id).show(); 
        setTimeout(
            function () {
                //alert("ok");
                $("#pdf_hide_"+id).show(); 
                $("#loder_"+id).hide();
            },
            6000);
    });
})
</script>

