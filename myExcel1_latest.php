<?php
$PAGE_HEADING = "My Excel";
$TITLE = "Competiscan $PAGE_HEADING";
$HEAD ='<script src="includes/myExcel.js?v=20121220" type="text/JavaScript"></script>';
include 'header_top.php';
require_once('includes/checklogin.php');
require_once('includes/ehLog.php');
require_once __DIR__. '/vendor/autoload.php';
if(!defined('ENV')){
        define('ENV',getenv('SERVER_NAME'));
    }
$export = new \HS\Export();

$ssid = (isset($_REQUEST['ssid'])) ? (int)$_REQUEST['ssid'] : 0;
$basket_id = (isset($_REQUEST['bid'])) ? (int)$_REQUEST['bid'] : 0;
$page = (isset($_REQUEST['page'])) ? (int)$_REQUEST['page'] : 1;
$sort = (isset($_REQUEST['sort'])) ? (int)$_REQUEST['sort'] : 0;
$tid = (isset($_REQUEST['tid'])) ? (int)$_REQUEST['tid'] : 0;
$currentpage_url='http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];


if ($basket_id < 0) {
	list($displayKeywords,$name) = getKeywords($ssid);
	echo '<div class="bodytext"><strong>Your Search Criteria:</strong><br />'.utf8_decode($displayKeywords).'</div>';
} else {
    $ssid = 0;

    if ($basket_id == 0) {
        $basket_name = 'Default Basket';
    } else {
		$Q = "SELECT basket_name FROM cscan_basket WHERE userID=".$_SESSION['sess_userID']." AND basket_id=$basket_id";
		$rs = $DRW->query($Q,$DRW_read);
		$dataB = $DRW->fetch_row($rs);
		$basket_name = $dataB[0];
	}
	echo '<div class="bodytext"><strong>Your Basket: '.htmlspecialchars($basket_name).'</strong></div>';
	
	if($_SESSION['sess_plevel']==1 || $_SESSION['sess_plevel']==2){
		echo '<div class="bodytext"><fieldset>
		<legend>Consumer Filter</legend>
		<form name="formb" action="'.$_SERVER['PHP_SELF'].'" method="post" onsubmit="return false;"><table border="0" cellpadding="5" cellspacing="0" class="bodytext">';
		echo '<tr><td valign="top">Search Date :</td><td><select name="addedToDatabase" class="combo_box" onchange="validateMonth();"><option value="">Select Period</option>';
		$ad_to_db_opt = get_addedToDatabaseArray();
		foreach($ad_to_db_opt as $key => $value) {
			echo "<option value=\"$key\">".htmlspecialchars($value)."</option>";
		}
		echo '</select>';
		$monthArray = get_monthArray();
		$end_year = date('Y');
		$end_month = date('m');
        $start_month = 1;
        $start_year = 2002;

		function generate_month($monthArray,$end_month,$end_year,$start_month,$start_year,$month_flag){
			$end_month = intval($end_month);
			$end_year = intval($end_year);
			$start_month = intval($start_month);
			$start_year = intval($start_year);
			
			//for($year=$start_year;$year<=$end_year;$year++) {
			for($year=$end_year;$year>=$start_year;$year--) {
				//if($year < $end_year) $tmp_month = 12;
				//else $tmp_month = $end_month;
				$tmp_month = 12;
				$tmp_month2 = 1;
				if($year==$end_year) {
					$tmp_month = $end_month;
				}
				elseif($year==$start_year) {
					$tmp_month2 = $start_month;
				}
				
				//for($month=$start_month;$month<=$tmp_month;$month++) {
				for($month=$tmp_month;$month>=$tmp_month2;$month--) {
					if($month<10) {
						$m = '0'.$month;
					}
					else {
						$m = $month ;
					}
					$value = $year."-".$m;
					
					echo "<option value=\"$value\"";
					if($month_flag == $value) {
						echo " selected=\"selected\"";
					}
					echo ">".$monthArray[$month]."  ".$year."</option>";
				}
				//$start_month = 1;
			}
		}
		echo '<div style="margin-top:4px;">Between <select name="month1" class="combo_box" id="month1" style="width:130px;" onchange="validateMonth();"><option value="">Select Month</option>';
		generate_month($monthArray,$end_month,$end_year,$start_month,$start_year,'');
		echo '</select> and <select name="month2" class="combo_box" id="month2" style="width:130px;" onchange="validateMonth();"><option value="">Select Month</option>';
		generate_month($monthArray,$end_month,$end_year,$start_month,$start_year,'');
		echo '</select></div></td></tr>';
		
		echo '<tr><td valign="top">Gender :</td><td><label><input type="radio" name="gender" value="M" />Male</label> &nbsp; <label><input type="radio" name="gender" value="F" />Female</label> &nbsp; <label><input type="radio" name="gender" value="" checked="checked" />Both</label></td></tr>';

		echo '<tr><td valign="top">State/Province :</td><td><select name="state[]" multiple="multiple" size="3" class="combo_box">';
		getStates();
		echo '</select><br />[Hold ctrl key for multiple selection]</td></tr>';

		echo '<tr><td valign="top">Age :</td><td><select name="age[]" class="combo_box" multiple="multiple" size="3">';
		$selArray = getAgeTypes(false, is21AgeFilterOn($_SESSION['sess_userID']));
		foreach($selArray as $selvalue=>$seltext){
			echo "<option value=\"$selvalue\">".htmlspecialchars($seltext)."</option>";
		}   
		echo '</select><br />[Hold ctrl key for multiple selection]</td></tr>';

		echo '<tr><td valign="top">Income :</td><td><select name="income[]" multiple="multiple" size="3" class="combo_box">';
		$selArray = getIncomeTypes();
		foreach($selArray as $selvalue=>$seltext) {
			echo "<option value=\"$selvalue\">".htmlspecialchars($seltext)."</option>";
		}
		echo '</select><br />[Hold ctrl key for multiple selection]</td></tr>';
		
		if(!in_array('DMA_ID',$_SESSION['sess_search_exclude'])){
			echo '<tr><td valign="top">Metropolitan Area :</td><td><select name="DMA_ID[]" multiple="multiple" size="3" class="combo_box">';
			$query_ac ="SELECT dmaid,dmaname FROM cscan_dma ORDER BY dmaname";
			$result_ac = $DRW->query($query_ac,$DRW_read);
			while($row_ac = $DRW->fetch_row($result_ac)){
				$selvalue = $row_ac[0];
				$seltext = $row_ac[1];
				
				echo "<option value=\"$selvalue\">".htmlspecialchars($seltext)."</option>";
			}   
			echo '</select><br />[Hold ctrl key for multiple selection]</td></tr>';
		}
		
		echo '</table></form></fieldset></div>';
	}
}

$save_msg = '';
$tname = '';
if(isset($_GET['tname'])) {
	$save_msg = "Your search has been saved";
	$tname = $_GET['tname'];
}
else {
	if(isset($_REQUEST['delt']) && !empty($tid)) {
		$last_template_sql = "DELETE FROM cscan_template WHERE templateID=$tid AND userID='".$_SESSION['sess_userID']."'";
		$DRW->query($last_template_sql,$DRW_main);
		ob_end_clean();
		header("Location: {$_SERVER['PHP_SELF']}?ssid=$ssid&bid=$basket_id&page=$page&sort=$sort");
		exit;
	}
	if(isset($_POST['sendsave']) && trim($_POST['templateName']) != '') {
		$count_save_sql = "SELECT COUNT(*) FROM cscan_template where userID =".$_SESSION['sess_userID'];
		$rs = $DRW->query($count_save_sql,$DRW_read);
		$data = $DRW->fetch_row($rs);
		$numrow = (int) $data[0];
		if($numrow < 100 || !empty($tid) || $_SESSION['sess_plevel']==1 || $_SESSION['sess_plevel']==2) {
			$templateFileType = (int)$_POST['templateFileType'];
			$templateName = trim($_POST['templateName']);
			$templateCoices = trim($_POST['templateCoices']);
			if(empty($tid)){
				$last_template_sql = "INSERT INTO cscan_template (templateName,userID,templateFileType,templateCoices) VALUES ('".$DRW->real_escape_string($templateName)."','".$_SESSION['sess_userID']."',$templateFileType,'".$DRW->real_escape_string($templateCoices)."')";
				$DRW->query($last_template_sql,$DRW_main);
				$tid = $DRW->insert_id($DRW_main);
			}
			else{
				$last_template_sql = "UPDATE cscan_template SET templateName='".$DRW->real_escape_string($templateName)."',templateFileType=$templateFileType,templateCoices='".$DRW->real_escape_string($templateCoices)."' WHERE templateID=$tid AND userID='".$_SESSION['sess_userID']."'";
				$DRW->query($last_template_sql,$DRW_main);
			}
			ob_end_clean();
			header("Location: {$_SERVER['PHP_SELF']}?ssid=$ssid&bid=$basket_id&page=$page&sort=$sort&tid=$tid&tname=".urlencode($templateName));
			exit;
		}
		else {
			$save_msg = "You can save only one-hundred (100) templates";
		}
	}
}
?>
<div class="bodytext" style="float:right;"><?php
$Q = "SELECT templateID,templateName,templateFileType,templateCoices FROM cscan_template WHERE userID=".$_SESSION['sess_userID']." ORDER BY templateName";
$rs = $DRW->query($Q,$DRW_read);
if($DRW->num_rows($rs)>0){
	$hidet = '';
}
else{
	$hidet = ' style="display:none;"';
}
$hidetd = 'display:none;';
$templateCoices_array = array();
$templateFileType = 3;
?>
<form name="tnameForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" onsubmit="getTemplateChoices();">
<table><tr>
<td><strong>Template</strong></td>
<td><select name="tid" class="input_box" onchange="populateTemplate();"<?php echo $hidet; ?>>
	<option value="0"></option>
	<?php 
	$hiddens = '<input type="hidden" name="tft0" value="1" /><input type="hidden" name="tc0" value="" />';
	while($datat = $DRW->fetch_row($rs)){
		echo '<option value="'.$datat[0].'"';
		if($datat[0]==$tid){
			echo ' selected="selected"';
			if($tid!=0){
				$hidetd = '';
				$templateCoices_array = explode(',',$datat[3]);
				$templateFileType = $datat[2];
			}
		}
		echo '>'.htmlspecialchars($datat[1]).'</option>';
		$hiddens .= '<input type="hidden" name="tft'.$datat[0].'" value="'.$datat[2].'" /><input type="hidden" name="tc'.$datat[0].'" value="'.htmlspecialchars($datat[3],ENT_QUOTES).'" />';
	}
	?>
</select></td>
<td><input type="button" name="deletet" value="delete" class="button" style="margin-right:8px;<?php echo $hidetd; ?> " onclick="if(confirm('Delete?')){ document.location.href='<?php echo $_SERVER['PHP_SELF']."?ssid=$ssid&amp;bid=$basket_id&amp;page=$page&amp;sort=$sort&amp;delt=1&amp;tid="; ?>'+document.tnameForm.tid.options[document.tnameForm.tid.selectedIndex].value; } return false;" /></td>
<td><input type="text" class="input_box" size="20" maxlength="40" name="templateName" value="<?php echo htmlspecialchars($tname,ENT_QUOTES); ?>" /></td>
<td><input type="submit" name="submit" value="save" class="submitbutton" /></td>
</tr></table>
<input type="hidden" name="sendsave" value="1" />
<input type="hidden" name="templateFileType" value="0" />
<input type="hidden" name="templateCoices" value="" />
<input type="hidden" name="ssid" value="<?php echo $ssid; ?>" />
<input type="hidden" name="bid" value="<?php echo $basket_id; ?>" />
<input type="hidden" name="page" value="<?php echo $page; ?>" />
<input type="hidden" name="sort" value="<?php echo $sort; ?>" />
<?php echo $hiddens; ?>
</form>
<?php
if($save_msg!='') {
	//echo '<div class="error">'.htmlspecialchars($save_msg).'</div>';
}
?>
</div>
<div style="clear:both;"></div>
<table width="100%" border="0" cellpadding="5" cellspacing="0" class="bodytext">
	<tr>
	<td><span class="headings">Customize Your Excel Report</span><hr />
	<form name="form1" action="exportExcel_out1.php" method="post" target="waiter" onsubmit="doBasketSearchChange('form1'); return validate();">
	<input type="hidden" name="ssid" value="<?php echo $ssid; ?>" />
	<input type="hidden" name="bid" value="<?php echo $basket_id; ?>" />
	<input type="hidden" name="page" value="<?php echo $page; ?>" />
	<input type="hidden" name="sort" value="<?php echo $sort; ?>" />
	<input type="hidden" name="eb_date1" value="" />
	<input type="hidden" name="eb_date2" value="" />
	<input type="hidden" name="eb_date3" value="" />
	<input type="hidden" name="eb_gender" value="" />
	<input type="hidden" name="eb_state" value="" />
	<input type="hidden" name="eb_age" value="" />
	<input type="hidden" name="eb_income" value="" />
	<input type="hidden" name="eb_DMA_ID" value="" />
	<input type="hidden" name="more" value="0" />
	<input type="hidden" name="noback" value="0" />
	<table border="0" class="bodytext" cellpadding="4" cellspacing="0" width="100%">
		<?php 
		$mPanelIDArray = array();
		$sectorIDArray = array();
		$mChannelIDArray = array();
		$categoryIDArray = array();
		$subCategoryIDArray = array();
                ### Start Envelope/Postage Data Fields#####
                $delmethidArray = array();
                $deliveryTypeIdArray = array();
                $postageIdArray = array();
                $presortedIdArray = array();
                $packageTypeIdArray = array();
                 ### End Envelope/Postage Data Fields#####
		if ($basket_id < 0) {
			$savedQ = "SELECT mPanelID,sectorID,mChannelID,categoryID,subCategoryID,delmethid_mult,deliveryTypeId,postageId,presortedId,packageTypeId,offerOrigin,is_multicultural,FeeProduct,businessContent_mult,is_affinion FROM cscan_search WHERE ID=".$ssid;
			$rs = $DRW->query($savedQ,$DRW_read);
			$data = $DRW->fetch_row($rs);
			$mPanelID = (string)$data[0];
			$sectorID = (string)$data[1];
			$mChannelID = (string)$data[2];
			$categoryID = (string)$data[3];
			$subCategoryID = (string)$data[4];
			$mPanelIDArray = explode(',',$mPanelID);
			$sectorIDArray = explode(',',$sectorID);
			$mChannelIDArray = explode(',',$mChannelID);
			$categoryIDArray = explode(',',$categoryID);
			$subCategoryIDArray = explode(',',$subCategoryID);
			### Start Envelope/Postage Data Fields#####
			$delmethid = (string)$data[5];
			$deliveryTypeId = (string)$data[6];
			$postageId = (string)$data[7];
			$presortedId = (string)$data[8];
			$packageTypeId = (string)$data[9];
			$delmethidArray =explode(' ,',$delmethid);
			$deliveryTypeIdArray =explode(' ,',$deliveryTypeId);
			$postageIdArray =explode(' ,',$postageId);
			$presortedIdArray =explode(' ,',$presortedId);
			$packageTypeIdArray =explode(' ,',$packageTypeId);
			### End Envelope/Postage Data Fields#####
			###add New field at myexcel#########
			$offerOrigin = $data[10];
			$is_multicultural = $data[11];
			$FeeProduct =    $data[12];
			$businessContent_mult = $data[13];
			$is_affinion = $data[14];
			$targetMarketArray=explode(' ,',$is_multicultural);
			$BusinessContentArray=explode(' ,',$businessContent_mult);
			###add New field at myexcel#########
        } else {
            $Q = "SELECT DISTINCT b_mPanelID FROM cscan_product_basket WHERE userID=".$_SESSION['sess_userID']." AND basket_id=$basket_id";
            $rs = $DRW->query($Q,$DRW_read);

			while($dataB = $DRW->fetch_row($rs)){
				$as = explode(',',$dataB[0]);
				foreach($as as $a){
					$a = (string)$a;
					if($a!='' && !in_array($a,$mPanelIDArray)){
						$mPanelIDArray[] = $a;
					}
				}
			}
			$Q = "SELECT DISTINCT b_sectorID FROM cscan_product_basket WHERE userID=".$_SESSION['sess_userID']." AND basket_id=$basket_id";
			$rs = $DRW->query($Q,$DRW_read);
			while($dataB = $DRW->fetch_row($rs)){
				$as = explode(',',$dataB[0]);
				foreach($as as $a){
					$a = (string)$a;
					if($a!='' && !in_array($a,$sectorIDArray)){
						$sectorIDArray[] = $a;
					}
				}
			}
			$Q = "SELECT DISTINCT b_mChannelID FROM cscan_product_basket WHERE userID=".$_SESSION['sess_userID']." AND basket_id=$basket_id";
			$rs = $DRW->query($Q,$DRW_read);
			while($dataB = $DRW->fetch_row($rs)){
				$as = explode(',',$dataB[0]);
				foreach($as as $a){
					$a = (string)$a;
					if($a!='' && !in_array($a,$mChannelIDArray)){
						$mChannelIDArray[] = $a;
					}
				}
			}
			$Q = "SELECT DISTINCT b_categoryID FROM cscan_product_basket WHERE userID=".$_SESSION['sess_userID']." AND basket_id=$basket_id";
			$rs = $DRW->query($Q,$DRW_read);
			while($dataB = $DRW->fetch_row($rs)){
				$as = explode(',',$dataB[0]);
				foreach($as as $a){
					$a = (string)$a;
					if($a!='' && !in_array($a,$categoryIDArray)){
						$categoryIDArray[] = $a;
					}
				}
			}
			$Q = "SELECT DISTINCT b_subCategoryID FROM cscan_product_basket WHERE userID=".$_SESSION['sess_userID']." AND basket_id=$basket_id";
			$rs = $DRW->query($Q,$DRW_read);
			while($dataB = $DRW->fetch_row($rs)){
				$as = explode(',',$dataB[0]);
				foreach($as as $a){
					$a = (string)$a;
					if($a!='' && !in_array($a,$subCategoryIDArray)){
						$subCategoryIDArray[] = $a;
					}
				}
			}
		}
		if((in_array(1,$mPanelIDArray) || in_array(2,$mPanelIDArray)) && (in_array(1,$mChannelIDArray) || in_array(3,$mChannelIDArray) || in_array(5,$mChannelIDArray) || in_array(9,$mChannelIDArray) || in_array(10,$mChannelIDArray))) {
			$consumer = true;
		}
		else {
			$consumer = false;
		}
		// $consumer=true; // for the eve calculation
		$showArray = array();
		$showArray[] = array("all","");		
                $showArray[] = array("company","Primary Company");
                $showArray[] = array("secondCompany","Additional Companies");
                
		
		$showArray[] = array("sectorID","Sector");
                //As per requested by nate removed group permission
		//if($_SESSION['sess_plevel']==1 || $_SESSION['sess_plevel']==2){
                $showArray[] = array("PrimarysectorID","Primary Sector");
		//}
		$showArray[] = array("categoryID","Category");
                 //As per requested by nate removed group permission
		//if($_SESSION['sess_plevel']==1 || $_SESSION['sess_plevel']==2){
		$showArray[] = array("PrimarycategoryID","Primary Category");
		//}
		$showArray[] = array("subCategoryID","Sub Category");
                 //As per requested by nate removed group permission
		//if($_SESSION['sess_plevel']==1 || $_SESSION['sess_plevel']==2){
			$showArray[] = array("PrimarysubCategoryID","Primary Sub Category");
		//}
		if(!in_array('sub_sub',$_SESSION['sess_search_exclude'])) {
			$showArray[] = array("subSubCategoryID","Sub Sub Category");
                         //As per requested by nate removed group permission
			//if($_SESSION['sess_plevel']==1 || $_SESSION['sess_plevel']==2){
			$showArray[] = array("PrimarysubSubCategoryID","Primary Sub Sub Category");
			//}
		}
		if(in_array(4,$sectorIDArray) && in_array(4,$_SESSION['sess_sector']) && ($_SESSION['sess_plevel']==1 || $_SESSION['sess_plevel']==2)){////
			$showArray[] = array("IssueTypeID","Issue Type");
		}
		$showArray[] = array("mTypeID","Mailing Type");
		$showArray[] = array("entryID","Entry ID");
                 /*############### Start For Quarter Filed #############*/
                $showArray[] = array("quarter","Quarter");
                 /*############### End For Quarter Filed #############*/
		$showArray[] = array("productHeadline","Headline");
		$showArray[] = array("mChannelID","Media Channel");
                
                /* ###### For Digital Source and Simple domain ###### */
		if((in_array(5,$mChannelIDArray) || in_array(9,$mChannelIDArray) || in_array(10,$mChannelIDArray))) {
			/*###### hide digital source 25-01-2019 As per by Nate ######*/
                        //$showArray[] = array("digital_source","Digital Source");
			$showArray[] = array("simple_domain","Simple Domain");
		}
		/* ###### End For Digital Source and Simple domain ###### */
		
                ###### For Mortgage & Loan - General Mortgage & Loan Details ######                
                if(in_array(6,$sectorIDArray)) {
                    if(!defined('ENV')){
                        define('ENV',getenv('SERVER_NAME'));
                    }
                    if(ENV == 'localhost' || ENV == 'demo.competiscan.com'){
                        $showArray[] = array("mortgage_details","General Mortgage & Loan Details");
    //                    $showArray[] = array("refinance","Refinance")
    //                    $showArray[] = array("jumbo_ncnfg","Jumbo/Non-Conforming");
    //                    $showArray[] = array("va","VA");
    //                    $showArray[] = array("fha","FHA");
    //                    $showArray[] = array("conventional","Conventional");
    //                    $showArray[] = array("usda","USDA");
                    }
		}  		
                ###### END  Mortgage & Loan - General Mortgage & Loan Details ######
                
                /* ###### For SEM Detail ###### */
                
                if(in_array(9,$mChannelIDArray)) {
                    $showArray[] = array("sem_search_key","SEM Search Key");
                    $showArray[] = array("sem_url","SEM Url");
                    $showArray[] = array("sem_headline","SEM Headline");
                    $showArray[] = array("sem_description","SEM Description");
		}  
		
                /* ###### END  For SEM Detail ###### */
                
                
		$showArray[] = array("mPanelID","Audience");
		$showArray[] = array("agentCommunicationID","Communication Type");
		$showArray[] = array("affinityAssociation","Affinity/Association");
		$showArray[] = array("affinityAssociationName","Affinity/Association Name");
		$showArray[] = array("AffinityCategoryID","Affinity/Association Category");
		$showArray[] = array("AffinitySubCategoryID","Affinity/Association Sub-Category");
		$showArray[] = array('productName','Product');
		if(in_array(2,$mChannelIDArray)) {
			$showArray[] = array("Publication","Publication");
			$showArray[] = array("PublicationDate","Publication Date");
		}
		if($consumer) {
			if (!is21FilterOn()) $showArray[] = array("age","Age");
		}
		if($consumer) {
            if (!is21FilterOn()) $showArray[] = array("income","Income");
		}
		/*if($consumer) {
			$showArray[] = array("gender","Gender");
		}*/
		//if($consumer && !in_array('DMA_ID',$_SESSION['sess_search_exclude'])){
		//	$showArray[] = array("DMA_ID","Metropolitan Area");
		//}
		$showArray[] = array("state","State/Province");
		$showArray[] = array("country","Country");
		$showArray[] = array("compaignLanguage","Campaign Language");
		//if($consumer) {
		//	$showArray[] = array("ficos","Risk Score");
		//}
		if($consumer) {
			$showArray[] = array("mailpieces","Mail Pieces");
		}
		if($consumer) {
			$showArray[] = array("mailvolume","Estimated Mail Volume");
			$showArray[] = array("mailspend","Estimated Mail Spend");
                        if(isset($_SESSION['sess_search_additional_field']) AND in_array('real_time_mail_volume',$_SESSION['sess_search_additional_field'])){ 
                            $showArray[] = array("realtime_mailvolume","Real Time Mail Volume");
                        }
                       
		}
		if($consumer) {
			$showArray[] = array("ppdate","Mail Piece Months");
                        $showArray[] = array("ppeve","Email Volume Estimates"); // for the eve calculation
               }
		if($consumer && in_array(315,$sectorIDArray)) {
			$showArray[] = array("edc_id","EDC / LDC / TDSP");
		}
		$showArray[] = array("firstSeen","First Seen");
		$showArray[] = array("lastSeen","Last Seen");
		$showArray[] = array("OfferExpiryDate","Offer Expiry Date");
		if(!in_array('me_filesize',$_SESSION['sess_search_exclude'])) {
			$showArray[] = array("filesize","File Size");
		}
		if(in_array(4,$sectorIDArray) && in_array(4,$_SESSION['sess_sector'])){ //in_array(13,$categoryIDArray) && in_array(13,$_SESSION['sess_category'])
			$showArray[] = array('fa','Face Amount');
		}
		if(in_array(4,$sectorIDArray) && in_array(4,$_SESSION['sess_sector'])){ //in_array(81,$subCategoryIDArray) && in_array(81,$_SESSION['sess_subcategory'])
			$showArray[] = array('tl','Term Length');
		}
		if((in_array(4,$sectorIDArray) || in_array(5,$sectorIDArray)) && (in_array(4,$_SESSION['sess_sector']) || in_array(5,$_SESSION['sess_sector']))){
			$showArray[] = array('riders','Riders');
		}
		if(in_array(1,$mChannelIDArray)){
			$showArray[] = array('delmethid','Delivery Method');
		}
                
                ############################## Start Envelope/Postage Data Fields################
                if((in_array(1,$delmethidArray) || in_array(3,$delmethidArray) || in_array(7,$delmethidArray))){
			$showArray[] = array('deliveryTypeId','Delivery Type');
		} 
                if((in_array(1,$delmethidArray) || in_array(3,$delmethidArray) || in_array(7,$delmethidArray))){
			$showArray[] = array('postageId','Postage');
		} 
                if((in_array(1,$delmethidArray) || in_array(3,$delmethidArray) || in_array(7,$delmethidArray))){
			$showArray[] = array('presortedId','Pre-Sorted');
		} 
                if((in_array(1,$delmethidArray) || in_array(3,$delmethidArray) || in_array(7,$delmethidArray))){
			$showArray[] = array('packageTypeId','Package Type');
		} 
                ############################## End Envelope/Postage Data Fields################
		
		
		$showArray[] = array('responseMechID','Response Mechanism');
		
		if(in_array(4,$sectorIDArray) || in_array(90,$sectorIDArray) || in_array(87,$sectorIDArray) || in_array(6,$sectorIDArray) || in_array(9,$sectorIDArray) || in_array(219,$sectorIDArray)){
			$showArray[] = array('FeeProductType','Ancillary Products');
		}
		if(in_array(6,$mChannelIDArray)){
			$showArray[] = array('external_link_network','Network Name');
			//$showArray[] = array('social_media_name','Facebook Page Name/Twitter Handle');
			$showArray[] = array('external_updates','Number of Updates/Tweets');
			$showArray[] = array('external_fans','Number of Fans/Followers');
			//$showArray[] = array('external_link','External Link');
		}
		if(in_array(5,$mChannelIDArray) || in_array(7,$mChannelIDArray)){
			$showArray[] = array('traffic_sources','Observed Traffic Sources');
		}
		if(!in_array('me_doclink',$_SESSION['sess_search_exclude'])) {
			$showArray[] = array("doclink","PDF Content");
		}
		if(in_array(4,$sectorIDArray) && in_array(4,$_SESSION['sess_sector'])){
			if(!in_array('prescription',$_SESSION['sess_search_exclude'])){
				$showArray[] = array("prescription","Rx");
			}
			if(!in_array('is_hphsa',$_SESSION['sess_search_exclude'])){
				$showArray[] = array("is_hphsa","CDHP/HDHP/HSA");
			}
		}
		$showArray[] = array("is_prescreen","Pre-Screen & Opt-Out Notice");
		//if(!in_array('citi',$_SESSION['sess_search_exclude'])){
		//	$showArray[] = array("is_citi","Retail Card Study");
		//}
		if((in_array(4,$sectorIDArray) && in_array(4,$_SESSION['sess_sector'])) || (in_array(5,$sectorIDArray) && in_array(5,$_SESSION['sess_sector']))){
			$showArray[] = array("worksiteVoluntary","Worksite/Voluntary");
			$showArray[] = array("groupSize","Group Size");
		}
		$showArray[] = array("creditUnion","Credit Union");                
                if(in_array(5,$mChannelIDArray) || in_array(9,$mChannelIDArray) || in_array(10,$mChannelIDArray)) {
                    $showArray[] = array("spend_impression","Estimated Digital Spend ($) / Impressions");
		}                
                ###################### For Faux Check selection ####################  
               
                if(in_array(6,$sectorIDArray)||in_array(87,$sectorIDArray) ||in_array(90,$sectorIDArray)) {
                    $showArray[] = array("faux_check","Faux Check");                   
		}  		
                ####################### END  For Faux Check selection ###############  
                 
                ###################### For Social Media Ad Type selection #################### 
                
               // if(ENV== 'localhost' || ENV == 'demo.competiscan.com'){
                    if(in_array(6,$mChannelIDArray)) {
                        $showArray[] = array("socialmedia_adtype","Social Media Ad Type");                   
                    } 
               // }                
                ####################### END  For Social Media Ad Type selection ###############  
                ################################# Start Personalized#####################
                 if(in_array(1,$mPanelIDArray) || in_array(2,$mPanelIDArray) || in_array(4,$mPanelIDArray) || in_array(5,$mPanelIDArray) || in_array(6,$mPanelIDArray) || in_array(9,$mPanelIDArray)){
			$showArray[] = array('personalization','Personalization');
		}
    	#################################End Personalized#####################
		###############add New field at myexcel#########
		//if(in_array(1,$targetMarketArray)){
			$showArray[] = array('is_multicultural','Target Markets');
		//}
		//print_r($BusinessContentArray);
		//if(!empty($BusinessContentArray[0]) AND (in_array(0,$BusinessContentArray) || in_array(1,$BusinessContentArray) || in_array(2,$BusinessContentArray))){
			$showArray[] = array('businessContent_mult','Business Content');
		//}
		if(in_array(6,$sectorIDArray) || in_array(4,$sectorIDArray) || in_array(9,$sectorIDArray) || in_array(219,$sectorIDArray)) {
		
			$showArray[] = array('offerOrigin','Offer Origin');
		
	   }
		if(in_array(6,$sectorIDArray)||in_array(87,$sectorIDArray) ||in_array(90,$sectorIDArray) || in_array(315,$sectorIDArray) || in_array(4,$sectorIDArray) || in_array(9,$sectorIDArray) || in_array(219,$sectorIDArray)) {
			$showArray[] = array('FeeProduct','Fee Product');
		
	   }
		//if($is_affinion==1){
			$showArray[] = array('is_affinion','Affinion');
		//}
		################add New field at myexcel#########
                
		$cols = 3;
		$tot = count($showArray);
		$rows = ceil($tot/$cols);
		$mod = $tot%$cols;
		$width = floor(100/$cols);
		$arrayi = 0;
		$chex = 0;
		$chexblock = 0;
		
		$printArray = array();
		$printArray = array_pad($printArray, $rows, '');
		if($tot>0){
			for($j=1;$j<=$cols;$j++){
				for($i=0;$i<$rows;$i++){
					if($j==1) $printArray[$i] .= '<tr>';
					
					$printArray[$i] .= '<td width="'.$width.'%">';
					if($arrayi==0){
						$printArray[$i] .= '<input type="hidden" name="chexEnd'.$chexblock.'" value="'.$chex.'" />';
						$chexblock++;
						$printArray[$i] .= '<label><input type="checkbox" name="allField" value="allField" onclick="chexStart('.$chexblock.','.$chex.');" />All</label>';
					}
					elseif(isset($showArray[$arrayi])){
						//if($rows==($i+1) && $mod!=0 && $j>$mod) {
						//	$printArray[$i] .= '&nbsp;';
						//}
						//else{
							list($field,$name) = $showArray[$arrayi];
							$printArray[$i] .= '<label><input type="checkbox" name="field[]" value="'.$field.'" onclick="unset_all();" id="chex'.$chex.'"';
							if(in_array($field,$templateCoices_array)){
								$printArray[$i] .= ' checked="checked"';
							}
							$printArray[$i] .= ' />'.htmlspecialchars($name).'</label>';
							$chex++;
						//}
					}
					else {
						$printArray[$i] .= '&nbsp;';
					}
					$arrayi++;
					$printArray[$i] .= '</td>';
					if($j==$cols) {
						$printArray[$i] .= '</tr>';
					}
				}
			}
		}
		foreach($printArray as $content){
			echo $content;
		}
		echo '</table><div style="display:none;margin-top:5px;padding-top:5px;border-top:dashed 1px #0055E3;" id="additional_a_div">';

		$showArray2 = array();
		$show_add_det = false;

		if (in_array(315,$sectorIDArray) || in_array(90,$sectorIDArray) || in_array(87,$sectorIDArray) || in_array(6,$sectorIDArray) ||
            in_array(9,$sectorIDArray) || in_array(219,$sectorIDArray) || in_array(266,$sectorIDArray) ||
            $_SESSION['sess_plevel']==1 || $_SESSION['sess_plevel']==2) {
			$show_add_det = true;
			echo '<table width="100%" border="0" cellpadding="0" cellspacing="0" class="bodytext">';
			echo '<tr><td style="padding:5px 10px 5px 0px;" colspan="'.$cols.'"><strong>Additional Details</strong> <a href="#" class="bluelink" onclick="chexStart(1000,'.$chex.'); return false;">Select All</a></td></tr>';

			$showArray2[] = array('','Incentives');
            $showArray2[] = array('incentive_ongoing','Ongoing Incentive');

            if ($export->showIncentiveFields($sectorIDArray)) {
                $showArray2[] = array('incentive','Sign-on Incentive #1');

                $mintel = new \HS\Mintel();
                $mintel_set = $mintel->getFields();
                $mintel_set_2 = $mintel->getFieldSet('incentive_set_2');
                $mintel_set_3 = $mintel->getFieldSet('incentive_set_3');

                foreach ($mintel_set as $field_name => $field_specs) {
                    $showArray2[] = array($field_name, $field_specs['display']);
                }

                foreach ($mintel_set_2 as $field_name => $field_specs) {
                    $showArray2[] = array($field_name, $field_specs['display']);
                }

                foreach ($mintel_set_3 as $field_name => $field_specs) {
                    $showArray2[] = array($field_name, $field_specs['display']);
                }
            } else {
                $showArray2[] = array('incentive', 'Incentive');
            }

			require_once('admin/additionalDetails.php');
			foreach($addlArray as $o){
				if((in_array(315,$sectorIDArray) && $o->id==315) || (in_array(90,$sectorIDArray) && ($o->id==178 || $o->id==179)) || (in_array(87,$sectorIDArray) && $o->id==87) || (in_array(6,$sectorIDArray) && $o->id==6) || (in_array(9,$sectorIDArray) && $o->id==9) || (in_array(219,$sectorIDArray) && $o->id==219) || (in_array(266,$sectorIDArray) && $o->id==266) || (in_array(559,$sectorIDArray) && $o->id==266) || (in_array(560,$sectorIDArray) && $o->id==266)){
					while($o->getNext()){
						$tempfieled = $o->getField();
						$temptitle = $o->getTitle();
						if (!empty($tempfieled)) {
							$tempfieled .= '_'.$o->id;
						} else {
							$showArray2[] = array('-1',$o->label);
						}
						$showArray2[] = array($tempfieled,$temptitle);
					}
					$o->doReset();
				}
			}
                         /*###############START Direct Mail advertised##############*/
                         if((in_array(266,$sectorIDArray) || in_array(559,$sectorIDArray) || in_array(560,$sectorIDArray) || (in_array(4,$sectorIDArray) && in_array(454,$categoryIDArray) && in_array(506,$subCategoryIDArray))) AND in_array(1,$mChannelIDArray) AND (in_array(1,$mPanelIDArray) || in_array(2,$mPanelIDArray))){
                          $showArray2[] = array('advertiser_address', 'Advertiser Address');
                          $showArray2[] = array('advertiser_city', 'Advertiser City');
                          $showArray2[] = array('advertiser_state', 'Advertiser State/Province');
                          $showArray2[] = array('advertiser_zipcode', 'Advertiser Zip/Postal Code');
                          $showArray2[] = array('advertiser_phone_number', 'Advertiser Phone Number');
                          $showArray2[] = array('advertiser_url', 'Advertiser URL');
                        }
                        /*###############END Direct Mail advertised##############*/
                        /*####### START NEW PROMOTION FIELD #########*/
                        if(in_array(266,$sectorIDArray) || in_array(559,$sectorIDArray) || in_array(560,$sectorIDArray)){
                          $showArray2[] = array('promotional_field', 'Promotional Fields');  
                        }
                        /*####### END NEW PROMOTION FIELD #########*/
			$arrayi = 0;
			$tot2 = count($showArray2);
			$rows2 = ceil($tot2/$cols);
			$mod = $tot2%$cols;
			$printArray2 = array();
			$printArray2 = array_pad($printArray2, $rows2, '');
			if($tot2>0){
				for($j=1;$j<=$cols;$j++){
					for($i=0;$i<$rows2;$i++){
						if($j==1) $printArray2[$i] .= '<tr>';
						
						$printArray2[$i] .= '<td width="'.$width.'%"';
						if(isset($showArray2[$arrayi])){
							//if($rows2==($i+1) && $mod!=0 && $j>$mod) {
							//	$printArray2[$i] .= '&nbsp;';
							//}
							//else{
								list($field,$name) = $showArray2[$arrayi];
								if($field=='-1'){
									$printArray2[$i] .= ' style="padding:10px 5px 0px 5px;"><strong>'.htmlspecialchars($name).'</strong>';
								}
								elseif($field==''){
									$printArray2[$i] .= ' style="padding:0px 5px 10px 5px;"><strong>'.htmlspecialchars($name).'</strong> <input type="hidden" name="chexEnd'.$chexblock.'" value="'.$chex.'" />';
									$chexblock++;
									$printArray2[$i] .= ' <a href="#" class="bluelink" onclick="chexStart('.$chexblock.','.$chex.'); return false;">Select All</a>';
								}
								else{
									$printArray2[$i] .= ' style="padding:0px 5px 10px 5px;"><label><input type="checkbox" name="field[]" value="'.$field.'" onclick="unset_all();" id="chex'.$chex.'"';
									if(in_array($field,$templateCoices_array)){
										$printArray2[$i] .= ' checked="checked"';
									}
									$printArray2[$i] .= ' />'.htmlspecialchars($name).'</label>';
									$chex++;
								}
							//}
						}
						else {
							$printArray2[$i] .= ' style="padding:5px;">&nbsp;';
						}
						$arrayi++;
						$printArray2[$i] .= '</td>';
						if($j==$cols) {
							$printArray2[$i] .= '</tr>';
						}
					}
				}
			}
			foreach($printArray2 as $content){
				echo $content;
			}
			
			echo '</table>';
		}
		else{
			echo '&nbsp;';
		}
		echo '<input type="hidden" name="chexEnd1000" value="'.$chex.'" />';
		?>
		</div>
		<?php 
		$show_add_var = false;
		if ($consumer && ($_SESSION['sess_plevel']==1 || $_SESSION['sess_plevel']==2 || !in_array('me_conp',$_SESSION['sess_search_exclude']) || !in_array('me_prizm',$_SESSION['sess_search_exclude']) || !in_array('DMA_CODE',$_SESSION['sess_search_exclude']) || (isset($_SESSION['sess_search_additional_field']) AND $_SESSION['sess_search_additional_field']!=''))) {
			$show_add_var = true;
			echo '<div style="display:none;margin-top:5px;padding-top:5px;border-top:dashed 1px #0055E3;" id="additional_v_div"><table width="100%" border="0" cellpadding="0" cellspacing="0" class="bodytext">';
			echo '<tr><td style="padding:5px 10px 5px 0px;" colspan="'.$cols.'"><strong>Additional Variables</strong> <a href="#" class="bluelink" onclick="chexStart(1001,'.$chex.'); return false;">Select All</a></td></tr>';
			//a.       Everything that doesn't fall in the other two groups.  Including:  age, income, sex, geographic location, 
			$showArray2 = array();
			if($_SESSION['sess_plevel']==1 || $_SESSION['sess_plevel']==2){
				$showArray2[] = array('','Consumer Report');
				$showArray2[] = array('pi2','Panelist Affinities');
				$showArray2[] = array('pi3','Panelist Loyalty/Retention, Statement Companies');
				$showArray2[] = array('invitationID','Invitation ID');
				$showArray2[] = array('trackingID','Last 4 Digits');
				//$showArray2[] = array('pproductFICO','FICO');
				$showArray2[] = array('','Demographics');
			}
			if($_SESSION['sess_plevel']==1 || $_SESSION['sess_plevel']==2 || !in_array('me_conp',$_SESSION['sess_search_exclude'])){
				$showArray2[] = array('postalcode','Zip/Postal Code');
			}
			if($_SESSION['sess_plevel']==1 || $_SESSION['sess_plevel']==2){
				$showArray2[] = array('ATP','ATP');
				$showArray2[] = array('Income360','Income360');
				$showArray2[] = array('DSDollar','DSDollar');
				$showArray2[] = array('DSI','DSI');
				$showArray2[] = array('ET_ETHNICITY','ETHNICITY');
				$showArray2[] = array('ET_RELIGION','RELIGION');
				$showArray2[] = array('ET_LANGUAGE','LANGUAGE');
				$showArray2[] = array('ET_GROUP','GROUP');
				$showArray2[] = array('ET_COUNTRY','COUNTRY');
				$showArray2[] = array('ET_ASSIMILATION','ASSIMILATION');
				$showArray2[] = array('HH_Income_Index','HH Income Index');
				$showArray2[] = array('Birth_date_of_person_for_first_person_in_household','Birth date of person for first person in household');
				$showArray2[] = array('Income_Producing_Assets_Segment_Code','Income Producing Assets Segment Code      *R*');
				$showArray2[] = array('Household_Income_Identifier_Narrow_Band','Household Income Identifier Narrow Band');
				$showArray2[] = array('Advantage_Home__Owner_Renter_Code','Advantage Home  Owner / Renter Code');
				$showArray2[] = array('Advantage_Home_Owner_Renter_Level','Advantage Home Owner / Renter Level');
                        }
                        
			if(!in_array('DMA_CODE',$_SESSION['sess_search_exclude'])){
				$showArray2[] = array('DMA_CODE','DMA CODE');
                         }
                        if($_SESSION['sess_plevel']==1 || $_SESSION['sess_plevel']==2){
				$showArray2[] = array('Gender_code','Gender code');
				$showArray2[] = array('Occupation_code','Occupation code');
				$showArray2[] = array('MSA_CODE','MSA CODE');
				$showArray2[] = array('','Segmentation');
                        }
                        /* ######### For FICO, CreditVision and Vantage Score ######### */
                        if(isset($_SESSION['sess_search_additional_field']) AND in_array('fico',$_SESSION['sess_search_additional_field'])){ 
                            $showArray2[] = array('fico_score','FICO Score');
                        }
                        if(isset($_SESSION['sess_search_additional_field']) AND in_array('fico_range',$_SESSION['sess_search_additional_field'])){ 
                            $showArray2[] = array('fico_range','FICO Range');
                        }
                        if(isset($_SESSION['sess_search_additional_field']) AND in_array('credit_vision',$_SESSION['sess_search_additional_field'])){ 
                            $showArray2[] = array('credit_vision','CreditVision');
                        }
                        if(isset($_SESSION['sess_search_additional_field']) AND in_array('credit_vision_range',$_SESSION['sess_search_additional_field'])){ 
                            $showArray2[] = array('credit_vision_range','CreditVision Range');
                        }
                        if(isset($_SESSION['sess_search_additional_field']) AND in_array('vantage_score',$_SESSION['sess_search_additional_field'])){ 
                            $showArray2[] = array('vantage_score','VantageScore');
                         }
                        if(isset($_SESSION['sess_search_additional_field']) AND in_array('vantage_range',$_SESSION['sess_search_additional_field'])){ 
                            $showArray2[] = array('vantage_range','VantageScore Range');
                         } 
                        /* ######### For end FICO, CreditVision and Vantage Score ######### */
                        if($_SESSION['sess_plevel']==1 || $_SESSION['sess_plevel']==2){        
				$showArray2[] = array('ECohort_Code','Financial Cohorts Code');//$showArray2[] = array('ECohort_Desc','ECohort_Desc'); $showArray2[] = array('ECohort_Flag','ECohort_Flag');
				$showArray2[] = array('PSY_FLAG','PSY_FLAG');
				$showArray2[] = array('PSY_CODE','PSY_CODE');
			}
			if($_SESSION['sess_plevel']==1 || $_SESSION['sess_plevel']==2 || !in_array('me_prizm',$_SESSION['sess_search_exclude'])){
				$showArray2[] = array('PZM_FLAG','PZM_FLAG');
				$showArray2[] = array('PZM_CODE','PZM_CODE');
			}
			if($_SESSION['sess_plevel']==1 || $_SESSION['sess_plevel']==2){
				$showArray2[] = array('CNX_FLAG','CNX_FLAG');
				$showArray2[] = array('CNX_CODE','CNX_CODE');
				$showArray2[] = array('WC_Annuities','WealthComplete Annuities');
				$showArray2[] = array('WC_Stocks','WealthComplete Stocks');
				$showArray2[] = array('WC_Bonds','WealthComplete Bonds');
				$showArray2[] = array('WC_Deposits','WealthComplete Deposits');
				$showArray2[] = array('WC_MutualFunds','WealthComplete Mutual Funds');
				$showArray2[] = array('WC_Other','WealthComplete Other');
				$showArray2[] = array('WC_TotalAssets','WealthComplete Total Assets');
				$showArray2[] = array('WC_CD','WealthComplete CD');
				$showArray2[] = array('WC_InterestChecking','WealthComplete Interest Checking');
				$showArray2[] = array('WC_MoneyMarketDepositAccounts','WealthComplete Money Market Deposit Accounts');
				$showArray2[] = array('WC_NonInterestChecking','WealthComplete Non-Interest Checking');
				$showArray2[] = array('WC_OtherCheckingAccounts','WealthComplete Other Checking Accounts');
				$showArray2[] = array('WC_Savings','WealthComplete Savings');
				$showArray2[] = array('InvestylesAdviceOrientedAssets','Investyles Advice-Oriented Assets');
				$showArray2[] = array('InvestylesRetirementProductAssets','Investyles Retirement Product Assets');
				$showArray2[] = array('InvestylesSelfDirectedAssets','Investyles Self-Directed Assets');
				$showArray2[] = array('eSpectrum','eSpectrum');
				$showArray2[] = array('','Credit Proxy variables');
                                if(!in_array('ValueScore',$_SESSION['sess_search_exclude'])){ 
                                    $showArray2[] = array('ValueScore_for_Household','ValueScore for Household');
                                }
				$showArray2[] = array('inq_win_past_6_mnths_except_promo_and_eval','# inq w/in past 6 mnths except promo and eval');
				$showArray2[] = array('Age_of_oldest_account_months','Age of oldest account (months)');
				$showArray2[] = array('Age_of_newest_account_months','Age of newest account (months)');
				$showArray2[] = array('of_accounts_opened_in_the_last_6_months','# of accounts opened in the last 6 months');
				$showArray2[] = array('of_accounts_opened_in_the_last_12_months','# of accounts opened in the last 12 months');
				$showArray2[] = array('of_accounts_opened_in_the_last_24_months','# of accounts opened in the last 24 months');
				$showArray2[] = array('of_accounts','# of accounts');
				$showArray2[] = array('of_active_accounts','# of active accounts');
				$showArray2[] = array('Total_credit_limit_for_active_accounts','Total credit limit for active accounts');
				$showArray2[] = array('of_accounts_currently_rated_satisfactory','# of accounts currently rated satisfactory');
				$showArray2[] = array('of_accounts_currently_bad_debt','# of accounts currently bad debt');
				$showArray2[] = array('Average_of_months_opened','Average # of months opened');
				$showArray2[] = array('of_active_accts_with_balance_50_limit','# of active accts with balance >= 50% limit');
				$showArray2[] = array('of_bank_revolving_accounts','# of bank revolving accounts');
				$showArray2[] = array('of_department_store_accounts','# of department store accounts');
				$showArray2[] = array('of_active_bank_revolving_accounts','# of active bank revolving accounts');
				$showArray2[] = array('active_dept_store_accts_wo_closed_narratives','# active dept store accts w/o closed narratives');
				$showArray2[] = array('Total_limit_for_active_bank_revolving_accts','Total limit for active bank revolving accts');
				$showArray2[] = array('Total_credit_limit_for_active_dept_store_accounts','Total credit limit for active dept store accounts');
				$showArray2[] = array('of_total_credit_union_accounts','# of total credit union accounts');
				$showArray2[] = array('Presence_of_Bankruptcy','Presence of Bankruptcy');
				$showArray2[] = array('accts_rated_bad_debt_of_derogatory24_mnths','# accts rated bad debt + # of derogatory-24 mnths');
				$showArray2[] = array('Age_of_oldest_active_mortgage','Age of oldest active mortgage');
				$showArray2[] = array('Balance_for_active_mortgage_accounts','Balance for active mortgage accounts');
				$showArray2[] = array('High_credit_for_active_mortgage_accounts','High credit for active mortgage accounts');
				$showArray2[] = array('Number_of_active_mortgage_accounts','Number of active mortgage accounts');
				$showArray2[] = array('','Claim Proxy Variables');
				//$showArray2[] = array('RAPA_EMLC','ISO Environmental Relativity Indices');
				$showArray2[] = array('RAPA_EMLC_ZIP_REL','ISO Risk Quality Index Auto:  BG/ZIP');
				$showArray2[] = array('RAPA_EMLC_COUNTY_REL','ISO Risk Quality Index Auto:  BG/County');
				$showArray2[] = array('RAPA_EMLC_STATE_REL','ISO Risk Quality Index Auto:  BG/State');
				$showArray2[] = array('RAHO_HOMLC_ZIP_REL','ISO Risk Quality Index Home:  BG/ZIP');
				$showArray2[] = array('RAHO_HOMLC_COUNTY_REL','ISO Risk Quality Index Home:  BG/County');
				$showArray2[] = array('RAHO_HOMLC_STATE_REL','ISO Risk Quality Index Home:  BG/State');
			}
			$arrayi = 0;
			$tot2 = count($showArray2);
			$rows2 = ceil($tot2/$cols);
			$mod = $tot2%$cols;
			$printArray2 = array();
			$printArray2 = array_pad($printArray2, $rows2, '');
			if($tot2>0){
				for($j=1;$j<=$cols;$j++){
					for($i=0;$i<$rows2;$i++){
						if($j==1) $printArray2[$i] .= '<tr>';
						
						$printArray2[$i] .= '<td width="'.$width.'%"';
						if(isset($showArray2[$arrayi])){
							//if($rows2==($i+1) && $mod!=0 && $j>$mod) {
							//	$printArray2[$i] .= '&nbsp;';
							//}
							//else{
								list($field,$name) = $showArray2[$arrayi];
								if($field=='-1'){
									$printArray2[$i] .= ' style="padding:10px 5px 0px 5px;"><strong>'.htmlspecialchars($name).'</strong>';
								}
								elseif($field==''){
									$printArray2[$i] .= ' style="padding:0px 5px 10px 5px;"><strong>'.htmlspecialchars($name).'</strong> <input type="hidden" name="chexEnd'.$chexblock.'" value="'.$chex.'" />';
									$chexblock++;
									$printArray2[$i] .= ' <a href="#" class="bluelink" onclick="chexStart('.$chexblock.','.$chex.'); return false;">Select All</a>';
								}
								else{
									$printArray2[$i] .= ' style="padding:0px 5px 10px 5px;"><label><input type="checkbox" name="field[]" value="'.$field.'" onclick="unset_all();" id="chex'.$chex.'"';
									if(in_array($field,$templateCoices_array)){
										$printArray2[$i] .= ' checked="checked"';
									}
									$printArray2[$i] .= ' />'.htmlspecialchars($name).'</label>';
									$chex++;
								}
							//}
						}
						else {
							$printArray2[$i] .= ' style="padding:5px;">&nbsp;';
						}
						$arrayi++;
						$printArray2[$i] .= '</td>';
						if($j==$cols) {
							$printArray2[$i] .= '</tr>';
						}
					}
				}
			}
			foreach($printArray2 as $content){
				echo $content;
			}
			echo '</table></div>';
		}
		echo '<input type="hidden" name="chexEnd'.$chexblock.'" value="'.$chex.'" /><input type="hidden" name="chexEnd1001" value="'.$chex.'" />';
		?>
		<table width="100%" border="0" cellpadding="5" cellspacing="0" class="bodytext">
		<tr><td colspan="3"><img src="images/spacer.gif" border="0" width="1" height="1" /></td></tr>
		<tr><td valign="top" width="<?php echo $width.'%'; ?>"><?php 
		if($show_add_det){
			echo '<a href="#" onclick="doAddlDiv(\'additional_a\',\'Additional Details\');return false;" class="bluelink" id="additional_a">Show Additional Details</a>';
		}
		if ($show_add_var) {
			echo '<br /><a href="#" onclick="doAddlDiv(\'additional_v\',\'Additional Variables\');return false;" class="bluelink" id="additional_v">Show Additional Variables</a>';
		}
		?></td><td valign="top" width="<?php echo $width.'%'; ?>"><strong>File Type</strong><br />
		<label><input type="radio" name="file_choice" value="3" <?php if($templateFileType==3) echo ' checked="checked"'; ?> />Excel (.xlsx)</label><br />
		<label><input type="radio" name="file_choice" value="1" <?php if($templateFileType==1) echo ' checked="checked"'; ?> />Excel (.xls)</label><br />
		<label><input type="radio" name="file_choice" value="2" <?php if($templateFileType==2) echo ' checked="checked"'; ?> />Comma Separated Value (.csv)</label></td>
		<td valign="top" width="<?php echo $width.'%'; ?>">
		<div id="loading" style="display:none;">
		<iframe name="waiter" src="<?php 
		if(running_php_cmd('exportExcel_back1.php '.$_SESSION['sess_userID'])){
			$savedQ = "SELECT file_choice FROM cscan_progress WHERE userID={$_SESSION['sess_userID']}";
			$rs = $DRW->query($savedQ,$DRW_read);
			$data = $DRW->fetch_row($rs);
			$file_choice = (int)$data[0];
			echo 'exportExcel_out1.php?file_choice='.$file_choice;
		}
		else{
			echo 'blank.html';
		}
		?>" width="100" height="40" frameborder="0" marginheight="0" marginwidth="0" scrolling="no"></iframe>
		</div>
		<div id="submits">
		<input type="submit" name="submit2" value="Generate File for Current Page" style="width:280px;margin-bottom:4px;" class="submitbutton" onclick="submitPre(<?php echo $page; ?>,0); return true;" />
		<br /><input type="submit" name="submit1" value="Generate File for all Records" style="width:280px;margin-bottom:4px;" class="submitbutton" onclick="submitPre(0,0); return true;" />
		<?php 
		if(in_array(87,$sectorIDArray)){
			echo '<br /><input type="submit" name="submit87" value="Comprehensive Banking Report" style="width:280px;margin-bottom:4px;" class="submitbutton" onclick="document.form1.allField.checked=true;set_field();submitPre(0,87); return true;" />';
		}
		if(in_array(90,$sectorIDArray)){
			echo '<br /><input type="submit" name="submit90" value="Comprehensive Credit Card Report" style="width:280px;margin-bottom:4px;" class="submitbutton" onclick="document.form1.allField.checked=true;set_field();submitPre(0,90); return true;" />';
		}
		if ($_SESSION['sess_plevel']==1 || $_SESSION['sess_plevel']==2 || !in_array('me_conp',$_SESSION['sess_search_exclude'])) {
			echo '<br /><input type="submit" name="submitCons" value="Panelist Report (Panelist ID)" style="width:280px;margin-bottom:4px;" class="submitbutton" onclick="submitPre(0,1); return true;" />';
			if($_SESSION['sess_plevel']==1 || $_SESSION['sess_plevel']==2){
				echo '<br /><input type="submit" name="submitCons" value="Panelist Report (Entry ID)" style="width:280px;margin-bottom:4px;" class="submitbutton" onclick="submitPre(0,2); return true;" />';
			}
		}
		?>
		</div>
		</td>
		</tr>
		</table>
		</form>
		<div>&nbsp;</div>
		<div class="headings">Customize Your Chart</div>
		<hr />
		<form name="form3" action="graph_html.php" method="post" target="_blank" onsubmit="doBasketSearchChange('form3'); return true;">
		<input type="hidden" name="eb_date1" value="" />
		<input type="hidden" name="eb_date2" value="" />
		<input type="hidden" name="eb_date3" value="" />
		<input type="hidden" name="eb_gender" value="" />
		<input type="hidden" name="eb_state" value="" />
		<input type="hidden" name="eb_age" value="" />
		<input type="hidden" name="eb_income" value="" />
		<input type="hidden" name="eb_DMA_ID" value="" />
		<table border="0" class="bodytext" cellpadding="4" cellspacing="0" width="100%">
		<tr><td valign="top" width="33%">
		<div>
		<strong>Chart Type</strong><br />
		<select name="chart_choice" class="input_box">
		<option value="1" selected="selected">Pie</option>
		<option value="2">Bar</option>
		<option value="3">Excel</option>
		<?php
		if ($_SESSION['sess_plevel']==1 || $_SESSION['sess_plevel']==2) {
		?>
			<option value="4">PowerPoint - Pie</option>
			<option value="5">PowerPoint - Bar</option>
		<?php
		}
		?>
		</select>
		</div>
		<div>&nbsp;</div>
        <div>
		<strong>Records</strong>
        <br /><label><input type="radio" name="top_comp_rad" value="0" onclick="doTrendTime();" checked="checked" />All</label>
        <br /><label><input type="radio" name="top_comp_rad" value="1" onclick="doTrendTime();" />Top</label> <input type="text" class="input_box" name="top_comp" value="5" size="6" maxlength="20" disabled="disabled" />
<?php
//if ($consumer && ($_SESSION['sess_plevel']==1 || $_SESSION['sess_plevel']==2)) {
if ($consumer) {
?>
	<div>&nbsp;</div><div><select name="date_choice" class="input_box">
	<option value="0">&nbsp;</option>
	<option value="3">Consumer Trend Over Time (Month)</option>
	<option value="4">Consumer Trend Over Time (Quarter)</option>
	<option value="2">Consumer Trend Over Time (Year)</option>
	</select></div>
<?php
}
?>
		</div>
		</td>
		<td valign="top" width="33%">
		<strong>Data Field</strong><br />
		<select name="graph_choice" class="input_box">
			<?php if($_SESSION['sess_plevel']==1 || $_SESSION['sess_plevel']==2){ ?>
			<option value="25">Affinity/Association</option>
			<?php } ?>
			<option value="18">Affinity Category</option>
            <?php if (!is21FilterOn()): ?>
			<option value="10">Age</option>
            <?php endif ?>
			<option value="19">Annual Fee</option>
			<?php if(in_array(90,$sectorIDArray)){ ?>
			<option value="20">Application Type</option>
			<?php } ?>
			<option value="8">Audience</option>
			<option value="2">Category</option>
			<option value="9">Communications Type</option>
			<option value="1" selected="selected">Company</option>
			<?php /* <option value="11">Gender</option> */ ?>
            <?php if (!is21FilterOn()): ?>
			<option value="12">Income</option>
            <?php endif?>
			<?php if(in_array(90,$sectorIDArray)){ ?>
			<option value="15">Introductory Pricing</option>
			<?php } ?>
			<option value="13">Mailing Type</option>
			<option value="3">Media Channel</option>
			<option value="14">Month</option>
			<option value="26">Pre-Screen/Opt-Out</option>
			<?php if(!in_array('me_prizm',$_SESSION['sess_search_exclude'])){ ?>
			<option value="30">PRIZM</option>
			<?php } ?>
			<?php if(in_array(90,$sectorIDArray) || in_array(87,$sectorIDArray)){ ?>
			<option value="28">Product</option>
			<option value="27">Rewards Program</option>
			<option value="17">Rewards Program Emphasis</option>
			<?php } ?>
			<?php if(in_array(4,$sectorIDArray) || in_array(5,$sectorIDArray)){ ?>
			<option value="29">Riders</option>
			<?php } ?>
			<option value="4">Sector</option>
			<?php if(in_array(90,$sectorIDArray) || in_array(87,$sectorIDArray)){ ?>
			<option value="16">Sign-on Incentive</option>
			<?php } ?>
			<option value="6">State/Province</option>
			<option value="7">Sub-Category</option>
			<?php if($_SESSION['sess_plevel']==1 || $_SESSION['sess_plevel']==2){ ?>
			<option value="24">Sub-Category - Primary</option>
			<?php } ?>
			<?php if(($_SESSION['sess_plevel']==1 || $_SESSION['sess_plevel']==2) AND !in_array('ValueScore',$_SESSION['sess_search_exclude'])){ ?>                        
			<option value="31">ValueScore</option>
			<?php } ?>
                        <!--######### For FICO, CreditVision and Vantage Score ######### -->
                       <?php if(isset($_SESSION['sess_search_additional_field']) AND (in_array('fico',$_SESSION['sess_search_additional_field']) || in_array('fico_range',$_SESSION['sess_search_additional_field'])) ){ ?>
			<option value="32">FICO Score Range</option>
			<?php } ?>
                        <?php if(isset($_SESSION['sess_search_additional_field']) AND (in_array('credit_vision',$_SESSION['sess_search_additional_field']) || in_array('credit_vision_range',$_SESSION['sess_search_additional_field']))){ ?>
			<option value="33">CreditVision Range</option>
			<?php } ?>
                        <?php if(isset($_SESSION['sess_search_additional_field']) AND (in_array('vantage_score',$_SESSION['sess_search_additional_field']) || in_array('vantage_range',$_SESSION['sess_search_additional_field']))){ ?>
			<option value="34">VantageScore Range</option>
			<?php } ?>
                        <!-- ######### For FICO, CreditVision and Vantage Score ######### -->
		</select>
		</td>
		<td valign="top" width="34%">
		<div>
		<strong>Units</strong><br />
		<select name="total_choice" class="input_box">
		<option value="1">Percent Entry ID</option>
		<option value="2">Total Entry ID</option>
		<?php
		if ($consumer) {
			//<option value="6">Average Risk Score</option>
		?>
		<?php if(in_array(1,$mChannelIDArray)){ ?>
			<option value="8">Percent Mail Pieces</option>
			<option value="4">Total Mail Pieces</option>
			<option value="9">Percent Estimated Mail Volume</option>
			<option value="5">Total Estimated Mail Volume</option>
                        <?php
                        if(isset($_SESSION['sess_search_additional_field']) AND in_array('real_time_mail_volume',$_SESSION['sess_search_additional_field'])){ ?>
                            <option value="17">Percent Real Time Mail Volume</option>
                            <option value="18">Total Real Time Mail Volume</option>
                        <?php } ?>
			<?php /* if($_SESSION['sess_plevel']==1 || $_SESSION['sess_plevel']==2){ ?>
			<option value="7">Estimated Spend</option>
			<?php }*/ ?>
		<?php } ?>
		<?php if(in_array(3,$mChannelIDArray) && ($_SESSION['sess_plevel']==1 || $_SESSION['sess_plevel']==2)){ ?>
		<option value="10">Total Email Pieces</option>
                <option value="15">Percent Email Volume Estimates</option>
                <option value="16">Total Email Volume Estimates</option>
		<?php } ?>
		<?php 
		}
		?>
              
		</select>
		</div>
		<div>&nbsp;</div>
		<div>
		<strong>Title</strong><br /><input type="text" class="input_box" name="title_choice" size="30" maxlength="50" autocomplete="off" /><br />
		</div>
		<div>&nbsp;</div>
		<div>
		<input type="submit" name="submit3" value="Generate Chart for all Records" style="width:280px;margin-bottom:4px;" class="submitbutton" />
		</div>
		</td>
		</tr>
	</table>
	<input type="hidden" name="ssid" value="<?php echo $ssid; ?>" />
	<input type="hidden" name="bid" value="<?php echo $basket_id; ?>" />
	<input type="hidden" name="page" value="<?php echo $page; ?>" />
	<input type="hidden" name="sort" value="<?php echo $sort; ?>" />
	</form>
	
<?php 
if(false){//in_array(90,$_SESSION['sess_sector']) || in_array(87,$_SESSION['sess_sector'])){ ?>
	<div>&nbsp;</div>
	<div class="headings">Customize Your Summarized Report</div>
	<hr />
	<form name="form4" action="exportExcel2.php" method="post" onsubmit="return validate2();">
	<table border="0" class="bodytext" cellpadding="4" cellspacing="0" width="100%">
	<tr><td valign="top">
	<?php 
	echo '<table border="0" class="bodytext" cellpadding="4" cellspacing="0" width="100%">';
	
	$showArray[] = array('ppdate','Month');
	
	$tot = count($showArray);
	$rows = ceil($tot/$cols);
	$arrayi = 0;
	$skipArray = array('all','mailpieces','mailvolume');
	$tot-=count($skipArray);
	$rows = ceil($tot/$cols);
	$mod = $tot%$cols;
	$printArray = array();
	$printArray = array_pad($printArray, $rows, '');
	if($tot>0){
		for($j=1;$j<=$cols;$j++){
			for($i=0;$i<$rows;$i++){
				do{
					$skip = false;
					if(isset($showArray[$arrayi])){
						list($field,$name) = $showArray[$arrayi];
						if(in_array($field,$skipArray)){
							$skip = true;
							$arrayi++;
						}
					}
				} while($skip);
				
				if($j==1) {
					$printArray[$i] .= '<tr>';
				}
				$printArray[$i] .= '<td width="'.$width.'%">';
				if(isset($showArray[$arrayi])){
					list($field,$name) = $showArray[$arrayi];
					if($field==''){
						$printArray[$i] .= '<strong>'.htmlspecialchars($name).'</strong>';
					}
					else{
						$printArray[$i] .= '<label><input type="checkbox" name="field[]" value="'.$field.'" />'.htmlspecialchars($name).'</label>';
					}
				}
				else {
					$printArray[$i] .= '&nbsp;';
				}
				$arrayi++;
				$printArray[$i] .= '</td>';
				if($j==$cols) {
					$printArray[$i] .= '</tr>';
				}
			}
		}
	}
	foreach($printArray as $content){
		echo $content;
	}
	
	echo '</table>';
	?>
	</td>
	<td valign="top" width="34%">
		<div>
		<strong>Units</strong><br />
		<label><input type="checkbox" name="units[]" value="1" checked="checked" />Mail Pieces</label><br />
		<label><input type="checkbox" name="units[]" value="2" checked="checked" />Estimated Mail Volume</label><br />
		<label><input type="checkbox" name="units[]" value="3" checked="checked" />Estimated Mail Spend</label><br />
		</div>
		<div>&nbsp;</div>
		<div>
		<input type="submit" name="submit1" value="Generate Report for all Records" style="width:250px;margin-bottom:4px;" class="submitbutton" />
		</div>
		</td>
	</tr>
	</table>
	<?php 
	$showArray2[] = array('','IXI Data');
	//$showArray2[] = array('ppdate','Month');
	$showArray2[] = array('competi_id','Panelist ID');
	if($_SESSION['sess_plevel']==1 || $_SESSION['sess_plevel']==2){
		$showArray2[] = array('invitationID','Invitation ID');
		$showArray2[] = array('ATP','ATP');
		$showArray2[] = array('Income360','Income360');
		$showArray2[] = array('DSDollar','DSDollar');
		$showArray2[] = array('DSI','DSI');
		$showArray2[] = array('ECohort_Code','Financial Cohorts Code');//$showArray2[] = array('ECohort_Desc','ECohort_Desc'); $showArray2[] = array('ECohort_Flag','ECohort_Flag');
	}
	$tot2 = count($showArray2);
	if($tot2>0){
		echo '<a href="#" onclick="doAddlDiv(\'additional_a2\',\'Additional Details\');return false;" class="bluelink" id="additional_a2">Show Additional Details</a><div style="display:none;border-top:dashed 1px #0055E3;" id="additional_a2_div"><table border="0" class="bodytext" cellpadding="4" cellspacing="0" width="100%">';
		
		$arrayi = 0;
		$rows2 = ceil($tot2/$cols);
		$mod = $tot2%$cols;
		$printArray2 = array();
		$printArray2 = array_pad($printArray2, $rows2, '');
		if($tot2>0){
			for($j=1;$j<=$cols;$j++){
				for($i=0;$i<$rows2;$i++){
					if($j==1) {
						$printArray2[$i] .= '<tr>';
					}
					$printArray2[$i] .= '<td width="'.$width.'%">';
					if(isset($showArray2[$arrayi])){
						list($field,$name) = $showArray2[$arrayi];
						if($field==''){
							$printArray2[$i] .= '<strong>'.htmlspecialchars($name).'</strong>';
						}
						else{
							$printArray2[$i] .= '<label><input type="checkbox" name="field[]" value="'.$field.'" />'.htmlspecialchars($name).'</label>';
						}
					}
					else {
						$printArray2[$i] .= '&nbsp;';
					}
					$arrayi++;
					$printArray2[$i] .= '</td>';
					if($j==$cols) {
						$printArray2[$i] .= '</tr>';
					}
				}
			}
		}
		foreach($printArray2 as $content){
			echo $content;
		}
		
		echo '</table></div>';
	}
	?>
	<input type="hidden" name="ssid" value="<?php echo $ssid; ?>" />
	<input type="hidden" name="bid" value="<?php echo $basket_id; ?>" />
	<input type="hidden" name="page" value="0" />
	<input type="hidden" name="sort" value="<?php echo $sort; ?>" />
	</form>
<?php } ?>
    </td>
  </tr>
</table>
<?php
include('footer_bottom.php');
