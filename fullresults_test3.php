<?php  
$start_time = microtime(true);
require_once('includes/globalSession.php');
require_once('includes/checklogin.php');
require_once('includes/paginator.php');       //paginator class. 
require_once('includes/paginator_html.php');  //paginator_html class.
require_once('includes/sphinx_function.php');  //sphinx functions.




$javascript = '';
$basket_name = '';
$pdf_key = '';
$pdfsearchKeyword = '';
$dorelev = false;
$doexpans = false;
$pagelimit = 30;
$bid = -1;
$ssid = 0;
$page = 1;
$sort = -3;
$save_msg = '';
$name = array();
$basket_action_text = '';
$selectVals = array();

if(isset($_REQUEST['bid'])) {
	$bid = (int)$_REQUEST['bid'];
}
if(isset($_REQUEST['ssid'])) {
	$ssid = (int)$_REQUEST['ssid'];
}
if($ssid==0 && $bid<0) {
	ob_end_clean();
	header("Location: fullsearch.php");
	exit;
}
$gets = "&amp;ssid=".$ssid;
if($bid>=0) {
	$gets .= '&amp;bid='.$bid;
}
if(isset($_REQUEST['page'])) {
	$page = (int)$_REQUEST['page'];
}
if(isset($_GET['sort'])) {
	$sort = (int)$_GET['sort'];
}
if(isset($_GET['sname'])) {
	$save_msg = "Your search has been saved as {$_GET['sname']}";
}
else {
	if(isset($_POST['sendsave']) && trim($_POST['searchName']) != '') {
		$count_save_sql = "SELECT COUNT(*) FROM cscan_search where userID =".$_SESSION['sess_userID']." AND saved=1";
		$rs = $DRW->query($count_save_sql,$DRW_read);
		$data = $DRW->fetch_row($rs);
		$numrow = (int) $data[0];
		
		if($numrow < 100 || $_SESSION['sess_plevel']==1 || $_SESSION['sess_plevel']==2) {
			$searchName = trim($_POST['searchName']);
			$last_search_sql = "UPDATE cscan_search SET saved=1,searchName='".$DRW->real_escape_string($searchName)."' WHERE ID='$ssid' AND userID='".$_SESSION['sess_userID']."'";
			$DRW->query($last_search_sql,$DRW_main);
			
			ob_end_clean();
			header("Location: {$_SERVER['PHP_SELF']}?ssid=$ssid&sname=".urlencode($searchName));
			exit;
		}
		else {
			$save_msg = "You can save only one-hundred (100) searches";
		}
	}
}

list($orderby,$dorelev,$doexpans) = doQuerySort($sort);

//list($orderby,$dorelev,$doexpans) = doQuerySort_test3($sort);

//$DRW->query('SET SESSION sort_buffer_size=1000000',$DRW_main);
//SET SESSION read_rnd_buffer_size=1000000;

if(isset($_POST['basket_action'])){
	$basket_action = (int)$_POST['basket_action'];
	$basket = array();
        
	if($basket_action>0){
		if($basket_action==7 || $basket_action==8 || $basket_action==9 || $basket_action==10){//Copy To/Add page/all || Move To page/all
			if($bid<0){
				list($selectQuery,$saved,$sortby) = doQuerytestsphinx($ssid,false,'',false,-1,$dorelev,$doexpans);
				list($countQuery) = doQuerytestsphinx($ssid,true);
			}
			else{
				list($selectQuery,$saved,$sortby) = doQuerytestsphinx(0, false, '', false, $bid);
				list($countQuery) = doQuerytestsphinx(0, true, '', false, $bid);
			}
			$selectQuery .= $orderby;
			
			if($basket_action==7 || $basket_action==9){ //Copy To/Add page || Move To page
				$count_result = $DRW->query($countQuery,$DRW_read);
				$count = $DRW->fetch_row($count_result);
				$search_num_of_rows = $count[0];
				$a = new Paginator_html($page,$search_num_of_rows);
				$a->set_Limit($pagelimit);
				$limit1 = $a->getRange1();
				$limit2 = $a->getRange2();
				$selectQuery .= " LIMIT $limit1,$limit2";
			}
			$query = $DRW->query($selectQuery,$DRW_read);
			while($rs = $DRW->fetch_array($query)){
				$productID = $rs['theproductID'];
				$basket[] = $productID;
			}
		}
		elseif(isset($_POST['basket']) && is_array($_POST['basket']) && count($_POST['basket'])>0) {
			if(isset($_SESSION['selected_productID'])){
				$basket = $_SESSION['selected_productID'];
				foreach($_POST['basket'] as $b){
					if(!in_array($b,$basket)){
						$basket[] = $b;
					}
				}
			}
			else{
				$basket = $_POST['basket'];
			}
		}
		elseif(isset($_SESSION['selected_productID'])){
			$basket = $_SESSION['selected_productID'];
		}
		
		if(isset($_POST['basketid'])) $basketid = (int)$_POST['basketid'];
		else $basketid = 0;
		
		if($basket_action==1){ //Change Basket Name
			$sql = "UPDATE cscan_basket SET basket_name='".$DRW->real_escape_string(trim($_POST['curr_basket_name']))."' WHERE basket_id=$bid AND userID={$_SESSION['sess_userID']}";
			$DRW->query($sql,$DRW_main);
			$bidredir = $bid;
		}
		elseif($basket_action==6){ //Save Annotations
			$sqlb = "SELECT productID FROM cscan_product_basket WHERE basket_id=$bid AND userID={$_SESSION['sess_userID']}";
			$rsb = $DRW->query($sqlb,$DRW_read);
			while($datab = $DRW->fetch_row($rsb)){
				if(isset($_POST["basket_note_$datab[0]"])) {
					$sql = "UPDATE cscan_product_basket SET basket_note='".$DRW->real_escape_string($_POST["basket_note_$datab[0]"])."' WHERE basket_id=$bid AND userID={$_SESSION['sess_userID']} AND productID=$datab[0]";
					$DRW->query($sql,$DRW_main);
				}
			}
			$bidredir = $bid;
		}
		elseif($basket_action==5){//Remove Selected
			foreach($basket as $pid){
				$sql = "DELETE FROM cscan_product_basket WHERE basket_id=$bid AND userID={$_SESSION['sess_userID']} AND productID=$pid";
				$DRW->query($sql,$DRW_main);
			}
			$bidredir = $bid;
		}
		elseif($basket_action==3 && $basketid!=0){ //Delete Basket
			$sql = "DELETE FROM cscan_product_basket WHERE basket_id=$basketid AND userID={$_SESSION['sess_userID']}";
			$DRW->query($sql,$DRW_main);
			
			$sql = "DELETE FROM cscan_basket WHERE basket_id=$basketid AND userID={$_SESSION['sess_userID']}";
			$DRW->query($sql,$DRW_main);
			$bidredir = $bid;
		}
		elseif($basket_action==2 || $basket_action==4 || $basket_action==7 || $basket_action==8 || $basket_action==9 || $basket_action==10){ //Copy Selected To/Add Selected To || Move Selected To || page || all
			if($basketid==-2){ //new basket
				$sql = "INSERT INTO cscan_basket (basket_name,userID,basket_created) VALUES ('".$DRW->real_escape_string(trim($_POST['basket_name']))."',{$_SESSION['sess_userID']},NOW())";
				$DRW->query($sql,$DRW_main);
				$basketid = (float)$DRW->insert_id($DRW_main);
			}
			if($bid<0){
				$sqlb = "SELECT mPanelID,sectorID,mChannelID,categoryID,subCategoryID FROM cscan_search WHERE ID=$ssid";
				$rsb = $DRW->query($sqlb,$DRW_read);
				$datab = $DRW->fetch_row($rsb);
				$b_mPanelID = (string)$datab[0];
				$b_sectorID = (string)$datab[1];
				$b_mChannelID = (string)$datab[2];
				$b_categoryID = (string)$datab[3];
				$b_subCategoryID = (string)$datab[4];
				$b_basket_note = '';
			}
			foreach($basket as $pid){
				if($bid>=0){
					$sqlb = "SELECT b_mPanelID,b_sectorID,b_mChannelID,b_categoryID,b_subCategoryID,basket_note FROM cscan_product_basket WHERE basket_id=$bid AND userID={$_SESSION['sess_userID']} AND productID=$pid";
					$rsb = $DRW->query($sqlb,$DRW_read);
					$datab = $DRW->fetch_row($rsb);
					$b_mPanelID = (string)$datab[0];
					$b_sectorID = (string)$datab[1];
					$b_mChannelID = (string)$datab[2];
					$b_categoryID = (string)$datab[3];
					$b_subCategoryID = (string)$datab[4];
					$b_basket_note = (string)$datab[5];
				}
				if(isset($_POST["basket_note_$pid"])) {
					$b_basket_note = $_POST["basket_note_$pid"];
				}
				$sql = "REPLACE INTO cscan_product_basket (basket_id,userID,productID,b_mPanelID,b_sectorID,b_mChannelID,b_categoryID,b_subCategoryID,basket_note) 
					VALUES ($basketid,{$_SESSION['sess_userID']},$pid,'$b_mPanelID','$b_sectorID','$b_mChannelID','$b_categoryID','$b_subCategoryID','".$DRW->real_escape_string($b_basket_note)."')";
				$DRW->query($sql,$DRW_main);
				if($basket_action==4 || $basket_action==9 || $basket_action==10){//move selected || page || all
					$sql = "DELETE FROM cscan_product_basket WHERE basket_id=$bid AND userID={$_SESSION['sess_userID']} AND productID=$pid";
					$DRW->query($sql,$DRW_main);
				}
			}
			$bidredir = $basketid;
		}
	}
	$_SESSION['selected_productID'] = array();
	ob_end_clean();
	if($basket_action==3 && $basketid==$bid) {
		header("Location: {$_SERVER['PHP_SELF']}?ssid=$ssid");
	}
	else {
		header("Location: {$_SERVER['PHP_SELF']}?ssid=$ssid&bid=$bidredir&updated=1");
	}
	
}

if($bid<0){
	$savedQ = "SELECT searchKey,searchType FROM cscan_search WHERE ID='".$ssid."'";
	$rs = $DRW->query($savedQ,$DRW_read);
	$data = $DRW->fetch_row($rs);
	if($data[0]!='' && ($data[1]=='ocr' || $data[1]=='ocr2' || $data[1]=='ocr_fulltext2')){
		$fixedkey = preg_replace('/["\\(\\)\\*]/','', $data[0]);
		$fixedkey = preg_replace('/\\b(and|or|not)\\b/i',' ',$fixedkey);
		$fixedkey = trim(preg_replace('/\\s+/',' ',$fixedkey));
		$pdf_key = '&amp;pdf_key='.rawurlencode($fixedkey);
		
		$pdfsearchKeyword = '#search='.rawurlencode('"'.$fixedkey.'"');
	}
	
	list($displayKeywords,$name) = getKeywords($ssid);
	$last_search_sql = "UPDATE cscan_search SET queryDate=NOW() WHERE ID='$ssid' AND userID='".$_SESSION['sess_userID']."'";
	$DRW->query($last_search_sql,$DRW_main);

	if(false && $_SESSION['sess_plevel']==2){
		$unapproved = true;
	}
	else{
		$unapproved = false;
	}
        
        //echo $ssid;exit;
	list($selectQuery,$saved,$sortby) = doQuerytestsphinx($ssid,false,'',false,-1,$dorelev,$doexpans,$unapproved);
	
	list($countQuery) = doQuerytestsphinx($ssid,true,'',false,-1,false,false,$unapproved);
}
else{
	$displayKeywords = '';
	list($selectQuery,$saved,$sortby) = doQuerytestsphinx(0, false, '', false, $bid);
	
	list($countQuery) = doQuerytestsphinx(0, true, '', false, $bid);
}




$count_result = $DRW->query($countQuery,$DRW_read);
$count = $DRW->fetch_row($count_result);
$search_num_of_rows = $count[0];
//$search_num_of_rows=0;

/*
if($totalfetchid!='')
{
   $search_num_of_rows  = $totalfetchid;
}
*/
$a = new Paginator_html($page,$search_num_of_rows);
$a->set_Limit($pagelimit);  
$a->set_Links(9);
$limit1 = $a->getRange1(); 
$limit2 = $a->getRange2(); 
//$selectQuery .= " LIMIT $limit1,$limit2 ) D ".$orderby; 
$selectQuery .= $orderby." LIMIT $limit1,$limit2  "; 
//$selectQuery .= "$orderby LIMIT 0,30"; 


$query = $DRW->query($selectQuery,$DRW_read);



 





$Q = "SELECT basket_id,basket_name FROM cscan_basket WHERE userID='".$_SESSION['sess_userID']."' ORDER BY basket_name";
$rs = $DRW->query($Q,$DRW_read);
$bids = $DRW->num_rows($rs);
//if change $selectVals also check fullresults.js
if($bid>0) {
	$selectVals[1] = 'Change Basket Name';
}
if($search_num_of_rows>0) {
	if($bid>=0) {
		$ac = 'Copy';
	}
	else {
		$ac = 'Add';
	}
	$selectVals[2] = $ac.' Selected To';
	$selectVals[7] = $ac.' Current Page To';
	$selectVals[8] = $ac.' All Results To';
}
if($bid>0 || ($bid==0 && $bids>0)) {
	$selectVals[3] = 'Delete Basket';
}
if($search_num_of_rows>0 && $bid>=0){
	$selectVals[4] = 'Move Selected To';
	$selectVals[9] = 'Move Current Page To';
	$selectVals[10] = 'Move All Results To';
	$selectVals[5] = 'Remove Selected';
	$selectVals[6] = 'Save Annotations';
}
if($bid!=0) {
	//$basket_action_text .= '<option value="0">Default Basket</option>';
	//$javascript .= "defArrayID[defArrayID.length] = '0';\ndefArray[defArray.length] = 'Default Basket';\n";
}
$basket_action_text .= '<option value="-2">New Basket</option>';
$javascript .= "defArrayID[defArrayID.length] = '-2';\ndefArray[defArray.length] = 'New Basket';\n";
while($dataB = $DRW->fetch_row($rs)){
	$basket_id = $dataB[0];
	$basket_name_tmp = $dataB[1];
	if($basket_id!=$bid){
		$basket_action_text .= '<option value="'.$basket_id.'">'.htmlspecialchars($basket_name_tmp).'</option>';
		$javascript .= "bidArrayID[bidArrayID.length] = '$basket_id';\nbidArray[bidArray.length] = '".singleQuoteSafe($basket_name_tmp)."';\n";
	}
	else {
		$javascript .= "currbid = '$basket_id';\ncurrbname = '".singleQuoteSafe($basket_name_tmp)."';\n";
	}
}


$PAGE_HEADING = "Search Results";
$TITLE = "Competiscan $PAGE_HEADING";
$HEAD = '<script src="includes/fullresults.js?v=20100126" type="text/JavaScript"></script>
<script src="includes/swfobject.js" type="text/javascript"></script>
<script src="includes/preview.js?v=20131201" type="text/JavaScript"></script>
<script type="text/JavaScript">
<!--
function intitalizeVars(){
	'.$javascript.'
	pdfsearch = \''.singleQuoteSafe($pdfsearchKeyword).'\';';
	if(isset($_SESSION['selected_productID']) && is_array($_SESSION['selected_productID']) && count($_SESSION['selected_productID'])>0){
		$HEAD .= '
		do_doDis = false;';
	}

	$HEAD .= '
	is_admin = ';
	if($_SESSION['sess_plevel']>0){
		$HEAD .= 'true';
	}
	else{
		$HEAD .= 'false';
	}
	$HEAD .= ';
}
//-->
</script>';
$BODYTAG = ' onload="intitalizeVars();"';
include('header_top.php');

//echo "<!--$selectQuery-->";
?>
<div class="headings" id="pcontainer"><strong>Welcome: <?php echo $_SESSION['sess_username']; ?></strong></div>
<hr />
<?php

ob_flush();// this is where the results get printed

//if($search_num_of_rows > 0) {
	if($save_msg!='') {
		echo '<div class="error" style="float:right;">'.htmlspecialchars($save_msg).'</div>';
	}
	elseif($saved!=1) {
		?>
		<div class="bodytext" style="float:right;">
		<form name="nameForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" onSubmit="return chk_search_name(document.nameForm.searchName.value);">
		<strong>Enter search name</strong>
		<input type="text" class="input_box" size="20" maxlength="40" name="searchName" />
		<input type="submit" name="submit" value="save" class="submitbutton" />
		<input type="hidden" name="ssid" value="<?php echo $ssid; ?>" />
		<input type="hidden" name="sendsave" value="1" />
		</form>
		</div>
		<?php
	}
//}
?>
<div style="clear:both;height:5px;">&nbsp;</div>
<?php
if($bid<0) {
	echo '<div class="bodytext"><strong>Your Search Criteria:</strong><br />'.$displayKeywords.'</div>';
	//if($pdf_key!='') {
	//	echo '<div class="bodytext">Show <a href="'.$_SERVER['PHP_SELF'].'?sort=-4'.$gets.'" class="HyperLink">Most</a> | <a href="'.$_SERVER['PHP_SELF'].'?sort=4'.$gets.'" class="HyperLink">Least</a> Relevant &nbsp; or &nbsp; Show <a href="'.$_SERVER['PHP_SELF'].'?sort=-5'.$gets.'" class="HyperLink">Most</a> | <a href="'.$_SERVER['PHP_SELF'].'?sort=5'.$gets.'" class="HyperLink">Least</a> Relevant with Expansion</div>';
	//}
}
else {
	if($bid==0){
		$basket_name = 'Default Basket';
	}
	else{
		$Q = "SELECT basket_name FROM cscan_basket WHERE userID=".$_SESSION['sess_userID']." AND basket_id=$bid";
		$rs = $DRW->query($Q,$DRW_read);
		$dataB = $DRW->fetch_row($rs);
		$basket_name = $dataB[0];
	}
	if($search_num_of_rows > 0){
		echo '<div class="bodytext"><strong>Your Basket: '.htmlspecialchars($basket_name).'</strong>';
		if(isset($_GET['updated'])) {
			echo ' &nbsp; <span class="error">UPDATED</span>';
		}
		echo '</div>';
	}
}

$buttons = '<div style="clear:both;height:5px;">&nbsp;</div><div>';
if($search_num_of_rows > 0) {
	/*
	$buttons .= '<div style="float:left;"><form action="exportExcel_out.php" method="post" name="pageexceller"><input class="submitbutton" type="submit" name="submit1" value="Export This Page To Excel" /><input type="hidden" name="ssid" value="'.$ssid.'" /><input type="hidden" name="bid" value="'.$bid.'" /><input type="hidden" name="page" value="'.$page.'" /><input type="hidden" name="sort" value="'.$sort.'" /><input type="hidden" name="noback" value="1" /></form></div>
	<div style="float:left;margin-left:10px;"><form action="exportExcel_out.php" method="post" name="exceller"><input class="submitbutton" type="submit" name="submit2" value="Export All To Excel" /><input type="hidden" name="ssid" value="'.$ssid.'" /><input type="hidden" name="bid" value="'.$bid.'" /><input type="hidden" name="page" value="0" /><input type="hidden" name="sort" value="'.$sort.'" /><input type="hidden" name="noback" value="1" /></form></div>
	<div style="float:left;margin-left:10px;"><form action="myExcel2.php" method="post" name="myexceller"><input type="hidden" name="ssid" value="'.$ssid.'" /><input type="hidden" name="bid" value="'.$bid.'" /><input type="hidden" name="page" value="'.$page.'" /><input type="hidden" name="num_rec" value="'.$search_num_of_rows.'" /><input type="hidden" name="sort" value="'.$sort.'" /><input class="submitbutton" type="submit" name="myExcel" value="My Excel" /></form></div><!--search--><!--paging-->';
	*/
	
	$buttons .= '<div style="float:left;margin-left:0px;"><form action="myExcel2.php" method="post" name="myexceller"><input type="hidden" name="ssid" value="'.$ssid.'" /><input type="hidden" name="bid" value="'.$bid.'" /><input type="hidden" name="page" value="'.$page.'" /><input type="hidden" name="num_rec" value="'.$search_num_of_rows.'" /><input type="hidden" name="sort" value="'.$sort.'" /><input class="submitbutton" type="submit" name="myExcel" value="My Excel" /></form></div><!--search--><!--paging-->';
	
	
}
else {
	$buttons .= '<div class="error" style="text-align:center;">No Results Found</div><div>&nbsp;</div>';
}
$buttons .= '<div style="clear:both;height:5px;">&nbsp;</div></div>';
if($search_num_of_rows > 0) {
	if($search_num_of_rows > 3){
		echo $buttons;
	}
	$buttons = str_replace('exceller','exceller2',$buttons);
	$buttons = str_replace('<!--search-->','<div style="float:left;margin-left:10px;"><form action="fullsearch.php" method="get" name="fullsearcher"><input class="submitbutton" type="submit" name="back" value="Go To Search" /><input type="hidden" name="ssid" value="'.$ssid.'" /></form></div>',$buttons);
}


?>


<form name="basketer" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<?php

//echo $countQuery;
//echo"<br><br>";
//echo $selectQuery;
//echo"<br><br>";

if($search_num_of_rows > 0) {
?>
<div style="border:solid 1px #0055E3;">
<table width="100%" cellpadding="4" cellspacing="0" class="sortable">
	<tr>
	<td width="18%" class="toptable">Sort By:</td>
	<td width="40%" class="toptable"><?php 
		sortLinks($sort,1,'Headline');
	?></td>
	<td width="18%" class="toptable"><?php 
		sortLinks($sort,2,'Company');
	?></td>
	<td width="24%" class="toptable"><?php 
		sortLinks($sort,3,'Entry ID');
		echo ' &nbsp; ';
		sortLinks($sort,6,'D');
		echo '&nbsp;';
		sortLinks($sort,7,'V');
		echo '&nbsp;';
		sortLinks($sort,8,'C');
		echo '&nbsp;';
		sortLinks($sort,9,'S');
		if($_SESSION['sess_plevel']>0){
			echo '&nbsp;';
			sortLinks($sort,10,'I');
		}
	?></td>
	</tr>
<?php
	$countbasket = 0;
        
       
        
        
	while($rs = $DRW->fetch_array($query)){
		$productID = $rs['theproductID'];
		$productHeadline = $rs['productHeadline'];
		$productHeadline = highlight($name,$productHeadline);
		$mid = $rs['mChannelID'];
		$sectorName = sectorName($rs['sectorID']);
		$mpannelid = $rs['mPanelID'];
		$category = categoryName($rs['categoryID']);
		if($category == '') {
			$category ='Not Mentioned';
		}
		$subCat = subCategoryName($rs['subCategoryID']);
		$addedToDatabase = $rs['addedToDatabase'];
		$mediaPanel = mediaPanelName($mpannelid);
		$entryID = $rs['entryID'];
		$company = $rs['company'];
		$variantID = $rs['variantID'];
		$isVariant = $rs['isVariant'];
		$isDemographic = $rs['isDemographic'];
		$isInsight = $rs['isInsight'];
		$isSurvey = $rs['isSurvey'];
		$isFICO = $rs['isFICO'];
	
		if($productHeadline=='') {
			if($mid==5 || $mid==7){
				$productHeadline = 'An Online Ad from '.htmlspecialchars($company);
			}
			else{
				$productHeadline = 'See complete product details';
			}
		}
		
		$queryI = "SELECT UNIX_TIMESTAMP(img_document_createddate) FROM cscan_img_document WHERE productID=$productID AND document_id=1 AND img_document_default=1";
		$query_resultI = $DRW->query($queryI,$DRW_read);
		$dataI = $DRW->fetch_row($query_resultI);
		$img_createddate_ts = (float)$dataI[0];
		
		$queryI = "SELECT img_companyID FROM cscan_img WHERE productID=$productID AND img_id=1";
		$query_resultI = $DRW->query($queryI,$DRW_read);
		$dataI = $DRW->fetch_row($query_resultI);
		$img_companyID = (float)$dataI[0];
		if(!empty($img_companyID)){
			$pi = 'cid='.$img_companyID;
		}
		else{
			$pi = 'id='.$productID;
		}
		$is_flash = false;
		$is_image = false;
		$sample_javascript = '';
		if($mid==5 || $mid==7){
			$query2 = "SELECT document_size_byte,document_filename,document_path,document_content_type,document_placement FROM cscan_document WHERE productID=$productID AND document_id=2";
			$query_result2 = $DRW->query($query2,$DRW_read);
			$data2 = $DRW->fetch_row($query_result2);
			$document_size_byte = (int)$data2[0];
			$document_filename = $data2[1];
			$document_path = $data2[2];
			$document_content_type = $data2[3];
			$document_placement = $data2[4];
			if(empty($document_placement)){
				$document_placement = '200x200';
			}
			list($fwidth,$fheight) = explode('x',$document_placement);
			list($fwidth,$fheight) = setWidthHeight($fwidth,$fheight, 400, 200);
			
			$sample_javascript .= " sample_widths['".$productID."'] = $fwidth; sample_heights['".$productID."'] = $fheight; ";
			
			if(preg_match('/flash/i',$document_content_type)){
				$is_flash = true;
				$sample_javascript .= " sample_types['".$productID."'] = 'flash'; ";
			}
			elseif(preg_match('/image/i',$document_content_type)){
				$is_image = true;
				$sample_javascript .= " sample_types['".$productID."'] = 'image'; ";
			}
		}
?>
	<tr>
	<td rowspan="3" class="bodytext" valign="top"><?php 
	if($sample_javascript!=''){
		echo '<script type="text/javascript">'.$sample_javascript.'</script>';
	}
	?><div><img src="productImg.php?<?php echo $pi; ?>" id="<?php echo 'pimg'.$productID; ?>" alt="" title="Preview this Product" class="tableimg" onclick="doPreview('<?php echo $productID; ?>',<?php echo $img_createddate_ts; ?>);" onmouseover="showPreview(<?php echo $productID; ?>,<?php echo $img_createddate_ts; ?>); return true;" onmouseout="hidePreview(<?php echo $productID; ?>); return true;" /></div>
	<div style="float:left;padding:2px;"><label><input type="checkbox" name="basket[]" value="<?php echo $productID; ?>" onclick="doExportBasketSelect(<?php echo $countbasket; ?>);"<?php 
	if(isset($_SESSION['selected_productID']) && in_array($productID,$_SESSION['selected_productID'])) {
		echo ' checked="checked"'; 
	}
	?> /><?php 
	if($bid>=0) {
		$bq = "SELECT basket_note,DATE_FORMAT(basket_date,'%m/%d/%Y') FROM cscan_product_basket WHERE basket_id=$bid AND userID={$_SESSION['sess_userID']} AND productID=$productID";
		$rsb = $DRW->query($bq,$DRW_read);
		$datab = $DRW->fetch_row($rsb);
		$basket_note = $datab[0];
		$basket_date = $datab[1];
		echo $basket_date.'</label><br />Annotation:<br /><textarea name="basket_note_'.$productID.'" rows="3" cols="20" class="input_box">'.htmlspecialchars($basket_note,ENT_QUOTES).'</textarea>';
	}
	else {
		$baskimg = '<img src="images/cart_blue.png" alt="Add" title="Add to Export Basket" border="0" />';
		if($_SESSION['sess_plevel']==1 || $_SESSION['sess_plevel']==2) {
			$bq = "SELECT count(*) FROM cscan_product_basket WHERE userID={$_SESSION['sess_userID']} AND productID=$productID";
			$rsb = $DRW->query($bq,$DRW_read);
			$datab = $DRW->fetch_row($rsb);
			if($datab[0]>0){
				/*echo '<span style="font-style:italic;font-size:smaller;"><br /> &nbsp; '.$datab[0].' basket';
				if($datab[0]>1){
					echo 's';
				}
				echo '</span>';*/
				$baskimg = '<img src="images/cart_blue_full.png" alt="Add" title="Add to Export Basket" border="0" />';
			}
		}
		echo '</label><a href="#" onclick="doCheck('.$countbasket.'); return false;">'.$baskimg.'</a>';
	}
	$countbasket++;
	?></div>
	<?php
	if($mid!=5 && $mid!=7){
		$query2 = "SELECT document_size_byte FROM cscan_document WHERE productID=$productID AND document_id=1";
		//,document_filename,document_path,document_content_type,document_placement
		$query_result2 = $DRW->query($query2,$DRW_read);
		$data2 = $DRW->fetch_row($query_result2);
		$document_size_byte = (int)$data2[0];
		//$document_filename = $data2[1];
		//$document_path = $data2[2];
		//$document_content_type = $data2[3];
		//$document_placement = $data2[4];
		
		$sizeofPDFinKB=$document_size_byte/1024;
		$sizeofPDFinMB=$sizeofPDFinKB/1024;
		if($sizeofPDFinMB<1) {
			$DisplaySize=round($sizeofPDFinKB,2)." KB";  
		}
		else {
			$DisplaySize=round($sizeofPDFinMB,2)." MB";  
		}
		if($document_size_byte>0){
			?>
			<div style="float:left;padding:2px 0px 0px 14px;"><a class="bluelink" href="<?php echo 'productDocuments.php?id='.$productID.$pdfsearchKeyword; ?>" onclick="pdfWin(<?php echo $productID; ?>); return false;" title="PDF Content"><img src="images/pdf.jpg" border="0" style="vertical-align:top;" /> <?php echo $DisplaySize; ?></a><br />
			<?php 
			if($_SESSION['sess_plevel']>0){
				?>
				<a class="bluelink" href="<?php echo 'productImages.php?pp=1&amp;id='.$productID; ?>" target="_blank" title="PowerPoint Content"><img src="images/ppt.jpg" border="0" style="vertical-align:top;" /></a>
				<?php
			}
			if(!in_array('jpeg',$_SESSION['sess_search_exclude'])){
				?>
				<a class="bluelink" href="<?php echo 'productImages.php?id='.$productID; ?>" target="_blank" title="JPEG Content"><img src="images/jpg.jpg" border="0" style="vertical-align:top;" /></a>
				<?php 
			} 
			?>
			</div>
			<?php 
		}
	}
	?>
	</td>
	<td class="bodytext" valign="top" style="border-bottom:none;"><strong>Headline:</strong><br /><a href="productDetail.php?id=<?php echo $productID.$pdf_key.'&amp;ssid='.$ssid; ?>" onclick="productDescription(<?php echo $productID; ?>,'<?php echo singleQuoteSafe($pdf_key.'&amp;ssid='.$ssid); ?>'); return false;" class="HyperLink" title="See complete product details"><?php echo $productHeadline; ?></a></td>
	<td class="bodytext" valign="top" style="border-bottom:none;"><strong>Company:</strong><br /><?php echo htmlspecialchars($company); ?></td>
	<td class="bodytext" valign="top" style="border-bottom:none;"><strong>EntryID:</strong><br /><?php 
	if($_SESSION['sess_plevel']>0){
		echo '<a href="admin/addproduct.php?id='.$productID.'" target="_blank" class="bluelink">'.$entryID.'</a>';
	}
	else{
		echo $entryID; 
	}
	if($isVariant==1 || $isDemographic!=0 || $isInsight==1 || $isFICO!=0 || $isSurvey==1){
		echo '<br /><span style="color:#cccccc;">';
		if($isDemographic!=0){
			echo 'D';
		}
		if($isVariant==1){
			echo 'V';
		}
		if($isSurvey==1 && $_SESSION['sess_plevel']>0) {
			echo '<a href="survey_questions_html.php?productID='.$productID.'" target="_blank" class="bluelink" id="Inslink'.$productID.'">I</a>';
		}
		$insighttext = '';
		if($isInsight==1){
			if($isVariant==1){
				$variantArray = array();
				getAllVariantsArray((int)$productID,$variantArray);
				$productID_list = implode(',',array_keys($variantArray));
			}
			else{
				$productID_list = $productID;
			}
			$checkV = "SELECT ci_date,r_order,a_order,productID FROM cscan_insight WHERE productID IN($productID_list) ORDER BY ci_date DESC LIMIT 1";
			$checkV = $DRW->query($checkV,$DRW_read);
			$vcounta = $DRW->fetch_row($checkV);
			$ci_date = $vcounta[0];
			$Response = $vcounta[1];
			$OverallAverage = $vcounta[2];
			$ci_productID = $vcounta[3];
			
			if($ci_date!=''){
				echo '<a href="#" onclick="showIns('.$productID.'); return false;" class="bluelink" id="Inslink'.$productID.'">C</a>';
				$barh = 15;
				$barw = 80;
				$barR = round($barw * $Response);
				$barO = round($barw * $OverallAverage);
				
				$insighttext = '<div style="display:none;position:absolute;background:#ffffff;padding:4px;border:solid 2px #000000;z-index:100;width:200px;" id="Ins'.$productID.'">
				<div><a href="#" onclick="hideIns('.$productID.'); return false;" class="bluelink">close</a> &nbsp; <a href="graph_spider_html.php?productID='.$ci_productID.'&amp;avg=1" target="_blank" class="bluelink">More</a></div>
				<table border="0" cellspacing="2" cellpadding="4" class="bodytext" width="100%">
				<tr><td colspan="3" align="center"><strong>Consumer Insight Scores</strong></td></tr>
				<tr><td><strong>Overall</strong></td><td><div style="width:'.$barw.'px;height:'.$barh.'px;border:solid 1px #000000;"><div style="width:'.$barO.'px;height:'.$barh.'px;background:#313694;">&nbsp;</div></div></td><td>'.round($OverallAverage*100).'</td></tr>
				<tr><td><strong>Response</strong></td><td><div style="width:'.$barw.'px;height:'.$barh.'px;border:solid 1px #000000;"><div style="width:'.$barR.'px;height:'.$barh.'px;background:#97cf00;">&nbsp;</div></div></td><td>'.round($Response*100).'</td></tr>
				</table></div>';
			}
		}
		if($isFICO!=0){
			echo 'S';
		}
		echo '</span>'.$insighttext;
	}
	?></td>
	</tr>
	<tr>
	<td class="bodytext" valign="top" style="border-bottom:none;"><strong>Media Channel:</strong><br /><?php echo htmlspecialchars(mediaChannelName($mid)); ?></td>
	<td class="bodytext" valign="top" style="border-bottom:none;"><strong>Sector:</strong><br /><?php echo htmlspecialchars($sectorName); ?></td>
	<td class="bodytext" valign="top" style="border-bottom:none;"><strong>Category:</strong><br /><?php echo htmlspecialchars($category); ?></td>
	</tr>
	<tr>
	<td class="bodytext" valign="top"><strong>Audience:</strong><br /><?php echo htmlspecialchars($mediaPanel); ?></td>
	<td class="bodytext" valign="top">&nbsp;</td>
	<td class="bodytext" valign="top"><strong>Sub Category:</strong><br /><?php echo htmlspecialchars($subCat); ?></td>
	</tr>
<?php
	}
?>
</table>
</div>
<?php 
}
if($bid>=0) {
	?>
	<table border="0" cellspacing="4" cellpadding="0">
	<tr>
		<td class="bodytext" valign="top"><strong>Your Basket: <?php echo $basket_name; ?></strong>
		<?php 
		if(isset($_GET['updated'])) {
			echo ' &nbsp; <span class="error">UPDATED</span>';
		}
		?>
		</td>
	</tr>
	</table>
<?php 
}
if($search_num_of_rows>0 || $bid>0){
	?>
	<table border="0" cellspacing="0" cellpadding="0"><tr><td class="bodytext" valign="bottom"><a href="baskets.php" title="Export Baskets"><img src="images/cart_blue.png" alt="Export Basket" border="0" style="margin-right:4px;" /></a></td>
		<td class="bodytext" valign="bottom"><select name="basket_action" size="1" style="font-family: verdana, Helvetica, sans-serif;font-size: 11px;color: #000000;border: 1px #000000 solid;margin-right:4px;" onchange="checkAction();"><option value="0" selected="selected">&nbsp;</option>
		<?php 
		foreach($selectVals as $k=>$val){
			echo '<option value="'.$k.'">'.htmlspecialchars($val).'</option>';
		}
		?>
		</select></td>
		<?php 
		if($bid>0) {
			echo '<td class="bodytext" valign="bottom"><input type="text" class="input_box" size="20" maxlength="200" name="curr_basket_name" style="display:none;margin-right:4px;" value="'.htmlspecialchars($basket_name,ENT_QUOTES).'" /></td>';
		}
		?>
		<td class="bodytext" valign="bottom"><select name="basketid" size="1" style="display:none;font-family: verdana, Helvetica, sans-serif;font-size: 11px;color: #000000;border: 1px #000000 solid;margin-right:4px;" onchange="checkNew();">
		<?php 
		echo $basket_action_text;
		?>
		</select></td>
		<td class="bodytext" valign="bottom"><input type="text" class="input_box" size="20" maxlength="200" name="basket_name" style="display:none;margin-right:4px;" /></td>
		<td class="bodytext" valign="bottom"><input class="submitbutton" type="submit" name="basketbutton_submit" value="Submit" style="display:none;" onclick="return checkChex();" /></td>
	</tr>
	</table>
	<div>&nbsp;</div>
	<?php
} 
if($search_num_of_rows > 0) {
	ob_start();
	$a->previousNext();
	$tmp = ob_get_clean();
	$buttons = str_replace('<!--paging-->','<div style="float:left;margin-left:10px;">'.$tmp.'</div>',$buttons);
}
?>
<input type="hidden" name="ssid" value="<?php echo $ssid; ?>" />
<input type="hidden" name="bid" value="<?php echo $bid; ?>" />
<input type="hidden" name="page" value="<?php echo $page; ?>" />
<input type="hidden" name="sort" value="<?php echo $sort; ?>" />
</form>
<?php
echo $buttons;
$out = ob_get_contents();
ob_clean();
if($search_num_of_rows > 0) {
	echo '<div class="error" style="float:left;">'.$search_num_of_rows .' Result';
	if($search_num_of_rows!=1) echo 's';
	echo ' Found in ('.number_format((microtime(true) - $start_time),3).' Seconds)</div>';
}
echo $out;
include 'footer_bottom.php';

function sortLinks($sel,$dis,$text){
	$gets = $GLOBALS['gets'];
	
	if($sel==$dis){
		echo '<a href="'.$_SERVER['PHP_SELF'].'?sort=-'.$dis.$gets.'" class="topLinks">'.$text.'<img src="images/down.gif" border="0" style="vertical-align:bottom;" width="15" height="15" /></a>';//&darr;
	}
	else{
		echo '<a href="'.$_SERVER['PHP_SELF'].'?sort='.$dis.$gets.'" class="topLinks">'.$text;
		if(abs($sel)==$dis){
			echo '<img src="images/up.gif" border="0" style="vertical-align:bottom;" width="15" height="15" /></a>';//&uarr;
		}
		else {
			echo '</a><img src="images/spacer.gif" border="0" style="vertical-align:bottom;" width="15" height="15" />';
		}
	}
}
function setWidthHeight($width, $height, $maxWidth, $maxHeight){
	$ret = array($width, $height);
	$ratio = $width / $height;
	if($width>$maxWidth || $height>$maxHeight){
		$ret[0] = $maxWidth;
		$ret[1] = ceil($ret[0] / $ratio);
		
		if($ret[1]>$maxHeight){
			$ret[1] = $maxHeight;
			$ret[0] = ceil($ret[1]*$ratio);
		}
	}
	return $ret;	
}
?>
