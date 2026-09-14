<?php 
$ALLOW_GROUPS = array(5,25);
require_once("../auth_auth.php");
$HEAD = '<link rel="stylesheet" href="../includes/jquery/jquery-ui.css" /><script type="text/javascript" src="../includes/jquery/jquery.min.js"></script><script type="text/javascript" src="../includes/jquery/jquery-ui.min.js"></script>
<style type="text/css">.no-close .ui-dialog-titlebar {display: none;}</style>';
include 'top.php';
require_once '../includes/functions.php';

$limit = 20 ;
if(isset($_REQUEST['p'])) $p = $_SESSION['manage_tmp_product_p'] = $_REQUEST['p'];
elseif(isset($_SESSION['manage_tmp_product_p'])) $p = $_SESSION['manage_tmp_product_p'];
else $p = 0;

if(isset($_REQUEST['sort'])) $sort = $_SESSION['manage_tmp_product_sort'] = (int)$_REQUEST['sort'];
elseif(isset($_SESSION['manage_tmp_product_sort'])) $sort = $_SESSION['manage_tmp_product_sort'];
else $sort = 2;

if(isset($_POST['tmpcomment'])) {
	$isTmp = (int)$_POST['isTmp'];
	$muid = $_POST['muid'];
	$sql = "UPDATE cscan_product_email SET tmp_productComment='".$DRW->real_escape_string($_POST['tmpcomment'])."' WHERE muid='".$DRW->real_escape_string($muid)."' AND isTmp=$isTmp";
	$DRW->query($sql,$DRW_main);
	
	ob_end_clean();
	header("Location: {$_SERVER['PHP_SELF']}?done=2");
	exit;
}

if(isset($_POST['delID']) ) {
	foreach($_POST['delID'] as $delID){
		list($id,$isTmp) = explode(':',$delID);
		$sql = "DELETE FROM `cscan_product_email` WHERE `muid`='".$DRW->real_escape_string($id)."' AND productID<=0  AND isTmp=$isTmp";
		if($DRW->query($sql,$DRW_main)){                
                         $data = [
                             'auth_id' => $GLOBALS['AUTH_DATA']['userID'],
                             'auth_name'=>authUserName($GLOBALS['AUTH_DATA']['userID']),
                             'deleted_id' => (int) $id,
                             'sql_query' => $sql,
                             'ip_address' => ipAddress(),
                             'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                             'delete_type' => 'Manage Temp Product',
                             'is_mobile' => isMobile(),
                             'insert_date' => date("Y-m-d H:i:s")
                         ];
                         trackDelete($data);
                         $emailData[] = $data;
                        $sql_payment = "DELETE FROM `cscan_payment_cards_temp` WHERE `muid`='".$DRW->real_escape_string($id)."' AND isTmp=$isTmp";
                        $DRW->query($sql_payment,$DRW_main);
                        $sql_banking = "DELETE FROM `cscan_banking_temp` WHERE `muid`='".$DRW->real_escape_string($id)."' AND isTmp=$isTmp";
                        $DRW->query($sql_banking,$DRW_main);
                        $sql_access_check = "DELETE FROM `cscan_credit_access_checks_temp` WHERE `muid`='".$DRW->real_escape_string($id)."' AND isTmp=$isTmp";
                        $DRW->query($sql_access_check,$DRW_main);
                        $sql_energy = "DELETE FROM `cscan_energy_temp` WHERE `muid`='".$DRW->real_escape_string($id)."' AND isTmp=$isTmp";
                        $DRW->query($sql_energy,$DRW_main);
                        $sql_retail = "DELETE FROM `cscan_retail_temp` WHERE `muid`='".$DRW->real_escape_string($id)."' AND isTmp=$isTmp";
                        $DRW->query($sql_retail,$DRW_main);
                        $sql_mortgage = "DELETE FROM `cscan_mortgage_loan_temp` WHERE `muid`='".$DRW->real_escape_string($id)."' AND isTmp=$isTmp";
                        $DRW->query($sql_mortgage,$DRW_main);
                        $sql_telecom = "DELETE FROM `cscan_telecom_temp` WHERE `muid`='".$DRW->real_escape_string($id)."' AND isTmp=$isTmp";
                        $DRW->query($sql_telecom,$DRW_main);
                        $sql_leisure_temp = "DELETE FROM `cscan_travel_leisure_temp` WHERE `muid`='".$DRW->real_escape_string($id)."' AND isTmp=$isTmp";
                        $DRW->query($sql_leisure_temp,$DRW_main);
                }
		############## Remove it later ############
//                if (!empty($_SERVER['HTTP_CLIENT_IP'])){
//                        $ip=$_SERVER['HTTP_CLIENT_IP'];
//                //Is it a proxy address
//                }elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
//                        $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
//                }else{
//                        $ip=$_SERVER['REMOTE_ADDR'];
//                }
//                sendDevAlert('Delete cscan_product_email by '.$AUTH_DATA['userID'].'-'.$ip.': ',$sql);
//                ###########################################
		if($isTmp==1){
			$sql = "SELECT `ceafpath` FROM `cscan_email_attach_file` WHERE `muid`='".$DRW->real_escape_string($id)."' AND isTmp=1";
			$rs = $DRW->query( $sql,$DRW_read );
			while($row = $DRW->fetch_array($rs) ) {
				if(is_file('../'.$row[0])){
					@unlink('../'.$row[0]);
				}
			}
			$sql = "DELETE FROM `cscan_email_attach_file` WHERE `muid`='".$DRW->real_escape_string($id)."' AND isTmp=1";
			$DRW->query($sql,$DRW_main);
		}
	}
        
                if (count($emailData) > 0) {
                    $html = '<table width="100%" border="1">';
                    $html .= '<tr><td>Auth ID</td><td>User Name</td><td>Deleted ID</td><td>SQL Query</td><td>IP Address</td><td>User Agent</td><td>Module</td><td>Is Mobile</td><td>Deleted Date (CST)</td></tr>';
                    foreach ($emailData as $tr) {
                        if (is_array($tr) && count($tr) > 0) {
                            $html .= '<tr>';
                            foreach ($tr as $td) {
                                $html .= '<td>' . $td . '</td>';
                            }
                            $html .= '</tr>';
                        }
                    }
                    $html .= '</table>';

                    sendDevAlert('Caution! Data Deleted From Manage Temp Product', $html);
                }
        
	ob_end_clean();
	header("Location: {$_SERVER['PHP_SELF']}?done=1");
	exit;
}

if(isset($_REQUEST['assigned_admin_userID'])) {
	$_SESSION['assigned_admin_userID'] = (int) $_REQUEST['assigned_admin_userID'];
}

if(isset($_REQUEST['nont'])) {
	$_SESSION['nont'] = (int) $_REQUEST['nont'];
	$p = $_SESSION['manage_tmp_product_p'] = 0;
}
if(isset($_REQUEST['showdel'])) {
	$_SESSION['showdel'] = (int) $_REQUEST['showdel'];
	$p = $_SESSION['manage_tmp_product_p'] = 0;
}

$addq = '';
$addq_pre = '';
if(isset($_SESSION['nont']) && $_SESSION['nont']==1) {
	$addq_pre .= "pe.productID=0";
	$newnont = 0;
}
else {
	$newnont = 1;
}
if(isset($_SESSION['showdel']) && $_SESSION['showdel']==1) {
	if($addq_pre!=''){
		$addq_pre .= ' OR ';
	}
	$addq_pre .= "pe.productID=-1";
	$newshowdel = 0;
}
else {
	$addq .= " AND pe.productID<>-1";
	$newshowdel = 1;
}
if($addq_pre!=''){
	$addq .= " AND ($addq_pre)";
}

if(!isset($_SESSION['tmp_product_searchText']) || isset($_REQUEST['show_All'])){
	$_SESSION['tmp_product_searchText'] = '';
	$_SESSION['tmp_company_search_text'] = '';
	$_SESSION['tmp_state_search_id'] = 0;
	$_SESSION['tmp_country_search_id'] = '';
}

if(!isset($_REQUEST['show_All'])){
	if(isset($_REQUEST['search_text'])) {
		$_SESSION['tmp_product_searchText'] = trim($_REQUEST['search_text']);
	}
	if(isset($_REQUEST['company'])) {
		$_SESSION['tmp_company_search_text'] = trim($_REQUEST['company']);
	}
	if(isset($_REQUEST['state'])) {
		$_SESSION['tmp_state_search_id'] = (int)$_REQUEST['state'];
	}
	if(isset($_REQUEST['country'])) {
		$_SESSION['tmp_country_search_id'] = $_REQUEST['country'];
	}
}
if(!isset($_SESSION['tmp_country_search_id'])) $_SESSION['tmp_country_search_id'] = 'US';

$javascript = '';
?>
<table width="100%" border="0" cellspacing="0" cellpadding="5" class="text">
  <tr><td class="adminhead" align="center">TEMP PRODUCT MANAGEMENT</td></tr>
</table>
<?php
	$inor = false;
	if($_SESSION['manageproducts_sector']==0){
		$or = '';
                $partArray  =array();
		foreach($AUTH_DATA['SID'] as $s){
                        //$or .= " OR (pe.sectorID like '%{$s}%' AND pe.sectorID regexp '[[:<:]]{$s}[[:>:]]')";
			//$or .= " OR (pe.sectorID like '%{$s}%' AND pe.sectorID regexp '[[:<:]]{$s}[[:>:]]')";
                       ########### for concat comma seprated data ################# 
                       $or .= "  OR (CONCAT(',',pe.sectorID,',') REGEXP ',$s,' )";
                        
		}
//                if(!empty($partArray)){
//                        $addq .= " AND (pe.sectorID='0' OR pe.sectorID=''$or)";
//			$inor = true; 
//                }
                
		if($or!=''){
			$addq .= " AND (pe.sectorID='0' OR pe.sectorID=''$or)";
			$inor = true;
		}
	}
	else {
		$addq .= " AND (pe.sectorID like '%{$_SESSION['manageproducts_sector']}%' AND pe.sectorID regexp '[[:<:]]{$_SESSION['manageproducts_sector']}[[:>:]]')";
		$inor = true;
	}
	$or = '';
	foreach($AUTH_DATA['CID'] as $s){
		//$or .= " OR (pe.categoryID like '%{$s}%' AND pe.categoryID regexp '[[:<:]]{$s}[[:>:]]')";
                ########### for concat comma seprated data ################# 
               $or .= "  OR (CONCAT(',',pe.categoryID,',') REGEXP ',$s,' )";
	}
	if($or!=''){
		$addq .= " AND (pe.categoryID='0' OR pe.categoryID=''$or)";
		$inor = true;
	}
	$or = '';
	foreach($AUTH_DATA['SCID'] as $s){
		//$or .= " OR (pe.subCategoryID like '%{$s}%' AND pe.subCategoryID regexp '[[:<:]]{$s}[[:>:]]')";
                 ########### for concat comma seprated data #################
                $or .= "  OR (CONCAT(',',pe.subCategoryID,',') REGEXP ',$s,' )";
                
	}
	if($or!=''){
		$addq .= " AND (pe.subCategoryID='0' OR pe.subCategoryID=''$or)";
		$inor = true;
	}
	if(!$inor){
		$addq .= " AND 1<>1";
	}

	if(isset($_SESSION['assigned_admin_userID']) && $_SESSION['assigned_admin_userID']!=0) {
		$assigned_admin_userID = $_SESSION['assigned_admin_userID'];
		
		$addq .= " AND tmp_admin_userID=".$_SESSION['assigned_admin_userID'];
	}
	else {
		$assigned_admin_userID = 0;
	}

	if($_SESSION['tmp_product_searchText']!='')  { 
		if(strpos($_SESSION['tmp_product_searchText'],'tmp')!==false){
			$searchtext = preg_replace('/tmp$/','',$_SESSION['tmp_product_searchText']);
			$addq .= " AND isTmp=1";
		}
		else $searchtext = $_SESSION['tmp_product_searchText'];
		$search_key = mysqlLike($searchtext);
		$addq .= " AND (productName like '%$search_key%' OR muid like '%$search_key%')";
	}
	if($_SESSION['tmp_company_search_text']!='')  { 
		$search_key = mysqlLike($_SESSION['tmp_company_search_text']);
		$addq .= " AND (company LIKE '$search_key%' OR secondCompany LIKE '%$search_key%')";
	}
	if($_SESSION['tmp_state_search_id']!=0)  { 
		$addq .= " AND (state like '%{$_SESSION['tmp_state_search_id']}%' AND state REGEXP '[[:<:]]{$_SESSION['tmp_state_search_id']}[[:>:]]')";
	}
	if(!empty($_SESSION['tmp_country_search_id'])) {
		$countryStates = '';
		$sqlc = "SELECT stateID FROM cscan_state WHERE countryCode='".$DRW->real_escape_string($_SESSION['tmp_country_search_id'])."'";
		$rsc = $DRW->query( $sqlc,$DRW_read );
		while($rowc = $DRW->fetch_row($rsc) ) {
			$countryStates .= " OR (state like '%".$rowc[0]."%' AND state REGEXP '[[:<:]]".$rowc[0]."[[:>:]]')";
		}
		if($_SESSION['tmp_country_search_id']=='US'){
			$countryStates .= " OR (state='0')";
		}
		if($countryStates!=''){
			$addq .= " AND (".substr($countryStates,4).")";
		}
	}
	
	$sql = "SELECT muid,productName,company,pe.sectorID,pe.categoryID,DATE_FORMAT(addedToDatabase,'%m/%d/%Y<br />%h:%i %p') as addedToDatabase_f,pe.productID,pe.mChannelID,tmp_priority,isTmp,tmp_admin_userID,pe.mPanelID,tmp_productComment,history_year 
		FROM cscan_product_email pe 
		LEFT JOIN cscan_mpanel mp ON(pe.mPanelID=mp.mPanelID) 
		LEFT JOIN cscan_mchannel mc ON(pe.mChannelID=mc.mChannelID) 
		LEFT JOIN cscan_sector cs ON(pe.sectorID=cs.sectorID) 
		WHERE 1=1$addq";
		//LEFT JOIN cscan_company cc ON(cmp_ids=cc.companyID) 
	
	$numquery = "SELECT count(*) as numrows FROM cscan_product_email pe WHERE 1=1$addq";
	
	$numquery = $DRW->query($numquery,$DRW_read);
	$nrow = $DRW->fetch_row($numquery);
	$numrows = $nrow[0];	
	if($sort<0) {
		$ascdesc = 'DESC';
		$ascdesc2 = 'ASC';
	}
	else {
		$ascdesc = 'ASC';
		$ascdesc2 = 'DESC';
	}
	switch(abs($sort)){
		case 1:
			$sql .= " ORDER BY muid $ascdesc2, isTmp $ascdesc2";
			break;
		case 2:
			$sql .= " ORDER BY addedToDatabase $ascdesc2";
			break;
		case 3:
			$sql .= " ORDER BY productName $ascdesc, muid $ascdesc2, isTmp $ascdesc2";
			break;
		case 4:
			$sql .= " ORDER BY company $ascdesc, muid $ascdesc2, isTmp $ascdesc2";
			break;
		case 5:
			$sql .= " ORDER BY mChannelName $ascdesc,sectorName $ascdesc,mPanelName $ascdesc, muid $ascdesc2, isTmp $ascdesc2";
			break;
		case 6:
			$sql .= " ORDER BY sectorName $ascdesc,mChannelName $ascdesc,mPanelName $ascdesc, muid $ascdesc2, isTmp $ascdesc2";
			break;
		case 7:
			$sql .= " ORDER BY mPanelName $ascdesc,mChannelName $ascdesc,sectorName $ascdesc, muid $ascdesc2, isTmp $ascdesc2";
			break;
		default:
			$sql .= " ORDER BY addedToDatabase $ascdesc2";
	}
	
	$sql .= " limit $p,$limit";
	//echo $sql;
	$rs = $DRW->query( $sql,$DRW_read );
	$resultCount = $DRW->num_rows( $rs );
	$count = 1 + $p ;
	$currPage = (($p/$limit) + 1);
	//if(checkGroup(5)){
?>
<form method="post" name="prodForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" style="display:inline;">
<table border="0" cellspacing="0" cellpadding="1" class="text">
	<tr>
	<td><strong>Search Product by Name or Temp ID:</strong></td>
	<td><input type="text" name="search_text" size="40" maxlength="255" class="input_box" value="<?php echo htmlspecialchars($_SESSION['tmp_product_searchText'],ENT_QUOTES); ?>" /></td>
	<td>&nbsp;</td>
	</tr>
	<tr>
	<td align="right"><strong>Company:</strong></td>
	<td><input type="text" name="company" size="40" maxlength="255" class="input_box" autocomplete="off" onkeyup="startTimer('showMatch(\'checkcos.php\',document.forms.prodForm.company)');" onblur="setTimeout('hideCos()',1000);" value="<?php echo htmlspecialchars($_SESSION['tmp_company_search_text'],ENT_QUOTES); ?>" /></td>
	<td>&nbsp;</td>
	</tr>
	<tr>
	<td align="right"><strong>State/Province:</strong></td>
	<td><select name="state" size="1" class="input_box"><option value="0">&nbsp;</option>
	<?php
	getStates($_SESSION['tmp_state_search_id']);
	?>
	</select></td>
	<td>&nbsp;</td>
	</tr>
	<tr>
	<td align="right"><strong>Country:</strong></td>
	<td><?php
	echo '<label><input type="radio" name="country" value=""';
	if(empty($_SESSION['tmp_country_search_id'])) {
		echo " checked=\"checked\"";
	}
	print ' />All</label>';
	$sqlc = "SELECT DISTINCT countryCode,country FROM cscan_state JOIN ISO31661_alpha2code ON (code=countryCode) ORDER BY country";
	$rsc = $DRW->query( $sqlc,$DRW_read );
	while($rowc = $DRW->fetch_row($rsc) ) {
		print ' <label><input type="radio" name="country" value="'.$rowc[0].'"';
		if($_SESSION['tmp_country_search_id']==$rowc[0]) {
			echo " checked=\"checked\"";
		}
		print ' />'.htmlspecialchars($rowc[1]).'</label>';
	}
	?></td>
	<td><input class="button" style="width:60px;" type="submit" name="search_Submit" value="Search" onclick="return check_searchform();" />
		&nbsp;&nbsp;&nbsp;&nbsp;
		<input class="button" style="width:70px" type="submit" name="show_All" value="Show All" /></td>
	</tr>
</table>
<input type="hidden" name="p" value="0" />
</form>
<div>&nbsp;</div>

<table border="0" cellspacing="0" cellpadding="0" class="text" width="100%">
<tr>
<td class="bodyText" colspan="2">
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="changeadmin"><strong>Last User:</strong> <select class="combo_box" name="assigned_admin_userID" onchange="document.changeadmin.submit();"><option value="0">All</option>
	<?php 
	$sqla = "select userID,userName,is_assign_queue from cscan_admin_users WHERE user_status=1 ORDER BY userName";
	$rsa = $DRW->query($sqla,$DRW_read);
	while($rowa = $DRW->fetch_row($rsa)) {
		print "<option value = \"$rowa[0]\"";
		if($rowa[0]==$assigned_admin_userID) print " selected=\"selected\"";
		print ">";
		if($rowa[2]) print '*';
		print "$rowa[1]</option>";
	}
	?></select>
	</form>	
</td>
</tr>
<tr>
<td><form method="get" name="nonbox" action="<?php echo $_SERVER['PHP_SELF']; ?>"><label><input type="checkbox" name="nont" value="<?php echo $newnont; ?>" onclick="document.location = '<?php echo $_SERVER['PHP_SELF'].'?nont='.$newnont; ?>';" <?php if(!$newnont) print ' checked="checked"'; ?> />Show Non Products Only</label>
<br /><label><input type="checkbox" name="showdel" value="<?php echo $newshowdel; ?>" onclick="document.location = '<?php echo $_SERVER['PHP_SELF'].'?showdel='.$newshowdel; ?>';" <?php if(!$newshowdel) print ' checked="checked"'; ?> /><span style="color:#666666;">Show Deleted</span></label>
</form></td>
<td align="right" valign="bottom">
<form method="post" name="delForm_but" style="display:inline;" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<?php if(checkGroup(5)){ ?>
    <input class="button" style="width:180px;" type="button" value="Add Temp Product" onclick="winPopProduct('../temp_product.php?isTmp=1'); return false;" />
&nbsp;
<?php }?>
<?php if(checkGroup(59)){ ?>
<input class="button" style="width:60px;" type="submit" name="submit1" ID="delBt" value="Delete" onclick="confirmDel(); return false;" />
<?php }?>
</form>
</td></tr>
</table>
<?php
	
	
	function doSort($sort,$dosort,$spacer='<br />'){
		if($sort==($dosort*-1) || $sort!=$dosort) {
			print "$spacer<a href=\"".$_SERVER['PHP_SELF']."?sort=$dosort&p=0\" class=\"blue\">sort</a>";
		}
		else{
			print "$spacer<a href=\"".$_SERVER['PHP_SELF']."?sort=-$dosort&p=0\" class=\"blue\">sort</a>";
		}
	}
?>
<div>&nbsp;</div>
<form method="post" name="delForm" action="<?php print $_SERVER['PHP_SELF']; ?>">
<table width="100%" border="0" cellspacing="0" cellpadding="5" class="text">
  <tr class="head1">
    <td width="5%" class="adminhead"><?php 
    if(checkGroup(59)) print '<input type="checkbox" name="setUnset" onclick="setAll();" />';
    else print '&nbsp;';
    ?></td>
		<td class="adminhead"><strong>ID</strong><?php doSort($sort,1); ?></td>
    <td class="adminhead"><strong>Date</strong><?php doSort($sort,2); ?></td>
		<td class="adminhead"><strong>Product</strong><?php doSort($sort,3); ?></td>
    <td class="adminhead"><strong>Company</strong><?php doSort($sort,4); ?></td>
		<td class="adminhead"><strong>Media Channel<?php doSort($sort,5,'&nbsp;'); ?> / Sector<?php doSort($sort,6,'&nbsp;'); ?> / Audience<?php doSort($sort,7,'&nbsp;'); ?></strong></td>
    <td class="adminhead"><strong>Last User</strong></td>
    <td class="adminhead"><strong>Attachments</strong></td>
  </tr>
<?php
	if( $resultCount > 0 ) {
		$className='';
		while( $row = $DRW->fetch_array($rs) ) {
			$muid = $row['muid'];
			$productName = $row['productName'];
			$company = $row['company'];
			$categoryID = $row['categoryID'];
			$sectorID = $row['sectorID'];
			$addedToDatabase = $row['addedToDatabase_f'];
			$productID = $row['productID'];
			$mChannelID = $row['mChannelID'];
			$tmp_priority = $row['tmp_priority'];
			$isTmp = $row['isTmp'];
			$admin_userID = $row['tmp_admin_userID'];
			$mPanelID = $row['mPanelID'];
			$mChannelID = $row['mChannelID'];
			$tmp_productComment = $row['tmp_productComment'];
			$hy = $row['history_year'];
			if(empty($hy)){
				$hy = '';
			}
			$commid = "comm_{$muid}_$isTmp";
			$javascript .= "commentArray['$commid'] = '".preg_replace('/(\\r?\\n|\\r)/','\\n',singleQuoteSafe($tmp_productComment))."';\n";
			
			$sectorName = sectorName($sectorID);  	
			$categoryName = sectorName($categoryID);
			$mediaPanel = mediaPanelName($mPanelID);
			$mediaChannel = mediaChannelName($mChannelID);
			
			if($productName == "") $productName ="(none)";
			if($sectorName == "") $sectorName ="N/A";
			if($categoryName == "") $categoryName ="N/A";
			if($mediaPanel=='') $mediaPanel = 'N/A';
			if($mediaChannel == '') $mediaChannel = 'N/A';
			
			if ($className=='selected-bg') $className='white-bg';
			else $className='selected-bg';
?>
	 <tr class="<?php echo $className;?>"<?php 
	 if($productID<=0 && checkGroup(4)){
	 	echo ' style="background-color:#E8E8FF;';
	 	if($productID<0){
	 		echo 'color:#999999;';
	 	}
	 	echo '"';
	 }
	 ?>>
        <td valign="top"><?php 
        if(checkGroup(59)) print "<input type=\"checkbox\" name=\"delID[]\" value=\"$muid:$isTmp\" />";
        else print '&nbsp;';
        ?></td>
        <td valign="top"><?php 
	if(!$isTmp){
		echo '<div id="showmessage_dialog_'.$muid.'" style="display:none;">
		<div><img style="border:none; vertical-align:bottom;cursor:pointer;" src="../images/drop.png" id="showmessage_close_'.$muid.'" /></div>
		<div><iframe id="showmessage_iframe_'.$muid.'" src="../blank.html" width="600" height="300"></iframe></div>
		</div><script type="text/javascript">
		var is_showmessage_dialog_'.$muid.' = false;
		var is_showmessage_timer_'.$muid.' = false;
		function do_showmessage_'.$muid.'(){
			if(!is_showmessage_dialog_'.$muid.'){
				is_showmessage_dialog_'.$muid.' = true;
				$( "#showmessage_dialog_'.$muid.'" ).dialog({
					closeOnEscape: false,
					dialogClass: "no-close",
					width: "auto",
					position: { my: "left top", at: "left bottom", of: "#showmessage_'.$muid.'" },
					open: function( event, ui ) {
						var old_src = $( "#showmessage_iframe_'.$muid.'" ).attr("src");
						if(old_src.indexOf("blank.html")!=-1){
							$( "#showmessage_iframe_'.$muid.'" ).attr("src", "https://html-pdf.competiscan.com/html/'.$muid.'");
						}
					}
				});
			}
		}
		$(document).ready(function() {
			$("#showmessage_'.$muid.'").mouseover(function(event) {
				if(!is_showmessage_dialog_'.$muid.'){
					is_showmessage_timer_'.$muid.' = setTimeout("do_showmessage_'.$muid.'()", 500);
				}
			});
			$("#showmessage_'.$muid.'").mouseout(function(event) {
				if(!is_showmessage_dialog_'.$muid.' && is_showmessage_timer_'.$muid.'){
					clearTimeout(is_showmessage_timer_'.$muid.');
					is_showmessage_timer_'.$muid.' = false;
				}
			});
			$("#showmessage_'.$muid.'").click(function(event) {
				$("#showmessage_'.$muid.'").trigger("mouseout");
				do_showmessage_'.$muid.'();
			});
			$("#showmessage_close_'.$muid.'").click(function(event) {
				if(is_showmessage_dialog_'.$muid.'){
					is_showmessage_dialog_'.$muid.' = false;
					$( "#showmessage_dialog_'.$muid.'" ).toggle( { 
						effect: "fade",
						duration: 500,
						complete: function( event, ui ) {
							try {
								$( "#showmessage_dialog_'.$muid.'" ).dialog( "destroy" );
							}
							catch(err) {
							}
						}
					});
				}
			});
		});
		</script><img src="../images/arrow.gif" id="showmessage_'.$muid.'" alt="" title="Preview this Email" style="cursor:pointer;" /> ';
	}
        print '<span style="font-weight:bold;';
        if($productID<=0 && $tmp_priority) echo 'color:#B5364B;font-size: 12px;';
	 	echo '">';
        echo $muid; 
        if($isTmp==1) echo 'tmp';
        ?></span>
        <?php 
        if($productID<=0) {
        	print "<br />";
        	if(checkGroup(5)) {
	        	print "[<a class=\"hlinks\" href=\"../temp_product.php?muid=$muid&amp;hy=$hy";
	        	if($isTmp==1) print '&isTmp=1';
	        	print "\" onclick=\"winPopProduct('../temp_product.php?muid=$muid&amp;hy=$hy";
	        	if($isTmp==1) print '&isTmp=1';
	        	print "'); return false;\">Edit&nbsp;Content</a>]<br />";
		        if($isTmp==1) {
		        	print "[<a href=\"#\" onclick=\"addAttach('{$muid}'); return false;\" id=\"div_{$muid}_id\">Add File</a>]<br />";
		        }
        	}
        	if(checkGroup(4)) {
	       		print " [<a class='hlinks' href='addproduct.php?new=1&muid=$muid";
	       		if($isTmp==1) print '&isTmp=1';
	       		print "'>Add&nbsp;Product</a>]";
        	}
        }
        ?>
        </td>
        <td valign="top"><?php echo $addedToDatabase; ?></td>
		<td valign="top"><?php echo $productName;
		if($productID>0 && checkGroup(4)) print "<br />[<a href=\"addproduct.php?id=$productID\">View/Edit</a>]";
        elseif(checkGroup(5)) {
        	if($isTmp==1) print "<br />[<a href=\"tmp_email.php?muid=$muid&amp;hy=$hy\" onclick=\"emailPop('tmp_email.php?muid=$muid&amp;hy=$hy'); return false;\" id=\"div_{$muid}_eid\">Forward</a>]";
        	else print "<br />[<a href=\"../email.php?muid=$muid&amp;hy=$hy\" onclick=\"winPopProduct('../email.php?muid=$muid&amp;hy=$hy'); return false;\" id=\"div_{$muid}_eid\">Forward</a>]";
        	$query2 = "SELECT COUNT(*) FROM `cscan_email_forward` WHERE `muid`='".$DRW->real_escape_string($muid)."' AND isTmp=$isTmp";
			$query_result2 = $DRW->query($query2,$DRW_read);
			$count = $DRW->fetch_row($query_result2);
			if($count[0]>0) print "<br /><span class=\"error\">Sent ($count[0])</span>";
        }
        ?></td>
        <td valign="top"><?php echo 
        	$company;//getCompanyName($company); 
        ?></td>
		<td valign="top"><?php echo $mediaChannel.' / '.$sectorName.' / '.$mediaPanel; ?></td>
        <td valign="top"><?php 
       		$userquery = "SELECT userName FROM cscan_admin_users WHERE userID=$admin_userID";
			$userquery = $DRW->query($userquery,$DRW_read);
			if($DRW->num_rows($userquery)>0) {
				$urow = $DRW->fetch_row($userquery);
				$userName = $urow[0];
			}
			else $userName = '';
			if($userName!='') print "<a href=\"#\" onclick=\"logPop($muid,0,$isTmp); return false;\">$userName</a><br />";
			?>[<a href="#" id="<?php echo $commid; ?>" onclick="addComment(<?php echo "$muid,$isTmp"; ?>); return false;">Comment</a>]</td>
        <td valign="top"><?php
        if($isTmp==1) print "<div id=\"div_{$muid}_files\">";
        $savedFiles = array();
        
		$qf = "SELECT `ceafpath`,`ceaftype`,`ceafid` FROM `cscan_email_attach_file` WHERE `muid`='".$DRW->real_escape_string($muid)."' AND isTmp=$isTmp";
		$query_resultf = $DRW->query($qf,$DRW_read);
		while($dataf = $DRW->fetch_row($query_resultf)){
			$bname = wordwrap(basename($dataf[0]),30,"<br />",true);
			$tmp = "<a href=\"../$dataf[0]\" target=\"_blank\">".$bname."</a>";
			if($productID<=0 && checkGroup(5) && empty($hy)) $tmp .= " [<a href=\"#\" onclick=\"removeAttach($dataf[2]); return false;\">Remove</a>]";// ($dataf[1])
			$savedFiles[] = $tmp;
		}
        
		if($isTmp!=1) {
	        $query2 = "SELECT `cefid`,`cefname`,`ceftype` FROM `cscan_email_file` WHERE `muid`='".$DRW->real_escape_string($muid)."' ORDER BY `cefpart` ASC";
			$query_result2 = $DRW->query($query2,$DRW_read);
			while($data2 = $DRW->fetch_row($query_result2)){
				$cefid = $data2[0];
				$cefname = $data2[1];
				$ceftype = $data2[2];
				if($cefname=='') $cefname = 'file';
				else $cefname = wordwrap($cefname,30,"<br />",true);
				$savedFiles[] =  "<a href=\"../attachment.php?cefid=$cefid&amp;hy=$hy\" onclick=\"winPop('../attachment.php?cefid=$cefid&amp;hy=$hy'); return false;\">$cefname</a>";// ($ceftype)
			}
		}
		if(count($savedFiles)>0) print implode(',<br />',$savedFiles);
		else print '&nbsp;';
		if($isTmp==1) print '</div>';
        ?></td>
    </tr>
<?php
		}
	}
	else {
?>
    <tr><td colspan="8" class="error" align="center">No Products Found.</td></tr>
<?php
	}
?>
  <tr>
	<td colspan="8">
		<table border="0" width="100%" cellspacing = "0"  cellpadding ="5">
			<tr>
				<td colspan = "2"> &nbsp;</td>
			</tr>
<?php
		/*if($resultCount > 0) {
			echo "<tr>";
			if ($p >= 1)     # HIDE PREV link if p is 0
			{
				$prevs=($p-$limit);
				print "<td align=\"right\" style= \"margin-right:5px;\"><a href=\"manage_tmp_product.php?p=$prevs\" class=\"sidehead\">&laquo; Prev $limit</a></td>";
			}
			else
			{
				echo "<td width=\"50%\">&nbsp;</td>";
			}
			## Calculate number of pages needing links
			
			$pages = intval($numrows/$limit);
			
			## $pages now contains int of pages needed unless there is a remainder from division
			
			if ($numrows%$limit)
			{    
				$pages++; ##has remainder so add one page
			}	
			##check to see if last page
			if (!((($p+$limit)/$limit) == $pages) && $pages!=1)
			{
				$news=$p+$limit; ##not last page so give NEXT link
				echo "<td  style=\"margin-left:10px;\"><a href=\"manage_tmp_product.php?p=$news\" class=\"sidehead\">Next $limit &raquo;</a></td>";
			}
			else
			{
				echo "<td width=\"50%\">&nbsp;</td>";
			}
			echo "</tr>";
			$a=$p+$limit;
			if($a>=$numrows)
			$a=$numrows;
			echo "<tr><td class=\"bodytext\" colspan=\"2\" align=\"center\">Showing results ".($p+1)." to $a of $numrows</td></tr>";
		}*/
		
		//if($sort>0) $sorttext = '&sort='.$_GET['sort'];
		//else 
		$sorttext = '';
		$firstlink = '[First]';
		$prevlink = '[Prev]';
		$nextlink = '[Next]';
		$lastlink = '[Last]';
		$middlelinks = '';
		$limstart = $p;
		$limiter = $limit;
		$rowcnt = $numrows;
		$show = 10;
		if($rowcnt>0){
			//first and previous only if not on first
			if($limstart>0){
				if($limstart>=$limiter) $prev = $limstart - $limiter;
				else $prev = 0;
				$firstlink = "[<a href=\"{$_SERVER['PHP_SELF']}?p=0$sorttext\">First</a>]";
				$prevlink = "<a href=\"{$_SERVER['PHP_SELF']}?p=$prev$sorttext\">&laquo; Prev $limiter</a>";
			}
			// middle loop through total results
			$numbers = ceil($rowcnt/$limiter);
			$loopstart = ceil($limstart/$limiter);
			if($loopstart<($show-1)) $loopstart = 0; // begin, do not move until 4
			if($numbers<$show) $loopend = $numbers; // loopend is less than $show
			else $loopend = $loopstart+$show;
			if($loopend>$numbers && $loopstart!=0) { // end, show last $show
				$loopstart = $numbers - $show;
				$loopend = $numbers;
			}
			for($i=$loopstart; $i<$loopend; $i++){
				$startnum = $limiter * $i;
				if($startnum!=$limstart) {
					$middlelinks .= "<a href=\"{$_SERVER['PHP_SELF']}?p=$startnum$sorttext\">".($i+1)."</a> ";
				}
				else $middlelinks .= ($i+1).' ';
			}
			//next and last if not on last
			if($limstart<$rowcnt && (($limstart+($limiter*2))<$rowcnt || ($rowcnt - ($limstart + $limiter))>0)){
				$next = $limstart + $limiter;
				$nextlink = "<a href=\"{$_SERVER['PHP_SELF']}?p=$next$sorttext\">Next $limiter &raquo;</a>";
				$lastlink = "[<a href=\"{$_SERVER['PHP_SELF']}?p=".(($numbers-1)*$limiter)."$sorttext\">Last</a>]";
			}
			
			if($middlelinks!='') $middlelinks = "[ $middlelinks ] &nbsp;";
			print "<tr><td align=\"center\" class=\"bodytext\">$firstlink &nbsp; $prevlink &nbsp; $middlelinks $nextlink &nbsp; $lastlink</td></tr>";
			print "<tr><td align=\"center\" class=\"bodytext\">Showing results ".($limstart+1)." to ";
			if($limstart+$limiter < $rowcnt) print ($limstart+$limiter);
			else print $rowcnt;
			print " of $rowcnt</td></tr>";
		}
?>
	</table></td></tr>
</table>
<input type="hidden" name="submiter" value="1" /></form>

<div id="upload_div" style="display:none;position:absolute;border:solid 1px #000000;padding:4px;background:#E8E8FF;color:#ffffff;z-index:100;"><form id="uploadform" name="uploadform" action="file_up.php" method="post" enctype="multipart/form-data" target="uploadframe" onsubmit="upload(); return false;">
<input name="updata" type="file" size="40" class="input_box" /><br /><input type="submit" name="subby" value="Upload" class="button" /> &nbsp; <input type="submit" name="canceler" value="Cancel" class="button" onclick="cancelForm(); return false;" />
<input type="hidden" name="muid" value="" /><input type="hidden" name="isTmp" value="1" /></form></div>

<iframe id="uploadframe" name="uploadframe" src="file_up.php" style="display:none;"></iframe>

<script type="text/javascript">
<!--
var commentArray = new Array();
<?php echo $javascript; ?>
function upload(){
	if(document.uploadform.updata.value!=''){
		document.uploadform.subby.disabled = true;
		document.uploadform.canceler.disabled = true;
		
		document.uploadform.submit();
	}
}

function removeAttach(ceafid){
	if(confirm('Remove?') && doDelete(ceafid)==1){
		var samehref = document.location.href;
		document.location.href = samehref;
	}
}

function cancelForm(){
	hideBlock(document.getElementById('upload_div'));
	document.uploadform.reset();
}

function addAttach(id){
	document.uploadform.muid.value = id;
	var divid = 'div_'+id+'_id';
	var posobj = document.getElementById(divid);
	var obj = document.getElementById('upload_div');
	obj.style.left = (findPosX(posobj))+'px';
	obj.style.top = (findPosY(posobj))+'px';
	obj.style.display = 'block';
	document.uploadform.updata.focus();
}

function saveComment(){
	document.commform.tmpsave.disabled = true;
	document.commform.tmpcancel.disabled = true;
	
	document.commform.submit();
}
function cancelComment(){
	hideBlock(document.getElementById('showbox_comment'));
	document.commform.reset();
}
function addComment(muid,isTmp){
	document.commform.muid.value = muid;
	document.commform.isTmp.value = isTmp;
	var divid = 'comm_'+muid+'_'+isTmp;
	var posobj = document.getElementById(divid);
	var obj = document.getElementById('showbox_comment');
	obj.style.left = (findPosX(posobj)-200)+'px';
	obj.style.top = (findPosY(posobj))+'px';
	if(commentArray[divid]){
		document.commform.tmpcomment.value = commentArray[divid];
	}
	else {
		document.commform.tmpcomment.value = '';
	}
	obj.style.display = 'block';
	document.commform.tmpcomment.focus();
}

function doDelete(ceafid){
	return processajax('file_del.php', false, 'POST', 'ceafid='+escape(ceafid), '', '');
}
function winPopProduct(winloc) {
	var wind = window.open(winloc,"winpop3","left=0, top=0, scrollbars=yes, resizable=yes");
	wind.focus();
}
function winPop(winloc) {
	var wind = window.open(winloc,"winpop","left=0, top=0, scrollbars=yes, resizable=yes");
	wind.focus();
}
function emailPop(winloc) {
	var wind = window.open(winloc,"winpop","left=0, top=0, scrollbars=yes, resizable=yes, width=550, height=400");
	wind.focus();
}

function confirmDel() {
	var goAheadFlag = 0;
	for(var i=0;i<document.delForm.elements.length;i++ ) {
		if( document.delForm.elements[i].checked == true ) {
			goAheadFlag = 1;
		}
	}
	if( goAheadFlag ) {
		if( confirm("Are you sure you want to delete?") ) {
			document.delForm.submit();
		}
		else {
			return false;
		}
	}
	else {
		alert( "Please select at least one record to delete !!!" );
		return false;
	}
	return true;
}

function setAll() {
	if( document.delForm.setUnset.value == 'on' ) {
		for(var i=1; i<document.delForm.elements.length; i++ ) {
			document.delForm.elements[i].checked = true;
		}
		document.delForm.setUnset.value = '';
	}
	else {
		for(var i=1;i<document.delForm.elements.length;i++ ) {
			document.delForm.elements[i].checked = false;
		}
		document.delForm.setUnset.value = 'on';
	}
}

function check_searchform() {
	var search = document.prodForm.search_text.value = trimspace(document.prodForm.search_text.value);
	var search2 = document.prodForm.company.value = trimspace(document.prodForm.company.value);
	var search3 = document.prodForm.state.selectedIndex;
	var search4 = '';
	for(var i=0;i<document.prodForm.country.length;i++){
		if(document.prodForm.country[i].value!='' && document.prodForm.country[i].checked){
			search4 = 'yes';
			break;
		}
	}
	if(search=='' && search2=='' && search3<1 && search4=='') {
		alert("Please enter some value to search");
		document.prodForm.search_text.focus();
		return false;
	}
	return true;
}

function logPop(mid,pid,istmp) {
	var wind = window.open('admin_log.php?mid='+mid+'&pid='+pid+'&istmp='+istmp,"winpop","left=0, top=0, scrollbars=yes, resizable=yes, width=400, height=300");
	wind.focus();
}
//-->
</script>
<div id="showbox_cos" style="display:none;position:absolute;border:solid 1px #ffffff;background:#14734F;padding:4px;color:#ffffff;z-index:100;"></div>
<div id="showbox_comment" style="display:none;position:absolute;border:solid 1px #000000;padding:4px;background:#E8E8FF;color:#ffffff;z-index:100;">
<form name="commform" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" onsubmit="saveComment(); return false;">
<table border="0" cellpadding="0" cellspacing="2" class="bodytext">
<tr><td><strong>Comment <em>(internal)</em>:</strong></td></tr>
<tr><td><textarea name="tmpcomment" rows="4" cols="40" class="input_box" id="tmpcomment"></textarea></td></tr>
<tr><td><input class="button" type="submit" name="tmpsave" value="Save" /> &nbsp; <input class="button" type="submit" name="tmpcancel" value="Cancel" onclick="cancelComment(); return false;" /><input type="hidden" name="muid" value="0" /><input type="hidden" name="isTmp" value="0" /></td></tr>
</table>
</form>
</div>

<?php include 'bottom.php'; ?>
