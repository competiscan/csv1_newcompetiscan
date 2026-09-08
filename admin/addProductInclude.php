<?php
require_once('additionalDetails.php');

include_once __DIR__.'/../includes/thumb.php';
require_once __DIR__.'/../includes/Document.php';

$javascript = "var categoryArray = new Array();\nvar sectorArray = new Array();\nvar subcategoryArray = new Array();\nvar subsubcategoryArray = new Array();\n";
$curpdf = 'a';
$curimg = 'a';
$button = 'Add';

if(isset($_REQUEST['entryID'])) $entryID = $_REQUEST['entryID'];
else $entryID = '';
if(isset($_POST['productName'])) $productName = $_POST['productName'];
else $productName = '';
if(isset($_POST['company'])) $company = trim($_POST['company']);
else $company = '';
if(isset($_POST['secondCompany'])) $secondCompany = trim($_POST['secondCompany']);
else $secondCompany = '';
if(isset($_POST['affinityAssociationVal'])) $affinityAssociationVal = $_POST['affinityAssociationVal'];
else $affinityAssociationVal = '';
if(isset($_POST['productHeadline'])) $productHeadline = trim(preg_replace('/\\s+/',' ',$_POST['productHeadline']));
else $productHeadline = '';
if(isset($_POST['traffic_sources'])) $traffic_sources = trim($_POST['traffic_sources']);
else $traffic_sources = '';
if(isset($_POST['scsc_comboIDs'])) $comboIDs = $_POST['scsc_comboIDs'];
else $comboIDs = '';
if(isset($_POST['sectorID'])) {
	$sectorID = $_POST['sectorID'];
	$sectorID = implode(',',$sectorID);
}
else $sectorID = '';
if(isset($_POST['cardStatus'])) $cardStatus = $_POST['cardStatus'];
else $cardStatus = '';
if(isset($_POST['categoryID'])) {
	$categoryID = $_POST['categoryID'];
	$categoryID = implode(',',$categoryID);
}
else $categoryID = '';
if(isset($_POST['subCategoryID'])) {
	$subCategoryID = $_POST['subCategoryID'];
	$subCategoryID = implode(',',$subCategoryID);
}
else $subCategoryID = '';
if(isset($_POST['subSubCategoryID'])) {
	$subSubCategoryID = $_POST['subSubCategoryID'];
	$subSubCategoryID = implode(',',$subSubCategoryID);
}
else $subSubCategoryID = '';
if(!empty($comboIDs)){
	$sectorIDA = array();
	$categoryIDA = array();
	$subCategoryIDA = array();
	$subSubCategoryIDA = array();
	$comboIDsA = explode('|',$comboIDs);
	foreach($comboIDsA as $combo){
		list($s,$c,$sc,$ssc) = explode('_',$combo);
		$ssc = (int)$ssc;////remove
		if(!in_array($s,$sectorIDA) && $s!='0'){
			$sectorIDA[] = $s;
		}
		if(!in_array($c,$categoryIDA) && $c!='0'){
			$categoryIDA[] = $c;
		}
		if(!in_array($sc,$subCategoryIDA) && $sc!='0'){
			$subCategoryIDA[] = $sc;
		}
		if(!in_array($ssc,$subSubCategoryIDA) && $ssc!='0'){
			$subSubCategoryIDA[] = $ssc;
		}
	}
	$sectorID = implode(',',$sectorIDA);
	$categoryID = implode(',',$categoryIDA);
	$subCategoryID = implode(',',$subCategoryIDA);
	$subSubCategoryID = implode(',',$subSubCategoryIDA);
}
if(isset($_POST['mChannelID'])) $mChannelID = $_POST['mChannelID'];
else $mChannelID = 0;
if(isset($_POST['mPanelID'])) $mPanelID = $_POST['mPanelID'];
else $mPanelID = 0;
if(isset($_POST['responseMechID'])) $responseMechID = implode(",",$_POST['responseMechID']);
else $responseMechID = '';
if(isset($_POST['mTypeID'])) $mTypeID = $_POST['mTypeID'];
else $mTypeID = '';
if(isset($_POST['IssueTypeID'])) $IssueTypeID = implode(",",$_POST['IssueTypeID']);
else $IssueTypeID = '';
if(isset($_POST['mPackItemID'])) $mPackItemID = $_POST['mPackItemID'];
else $mPackItemID = '';
if(isset($_POST['agentCommunicationID'])) {
	$agentCommunicationID = $_POST['agentCommunicationID'];
	$agentCommunicationID = implode(",",$agentCommunicationID);
}
else $agentCommunicationID = '';
if(isset($_POST['delmethid'])) $delmethid = (int)$_POST['delmethid'];
else $delmethid = 0;
if(isset($_POST['incentive'])) $incentive = $_POST['incentive'];
else $incentive = '';
if(isset($_POST['incentive_ongoing'])) $incentive_ongoing = $_POST['incentive_ongoing'];
else $incentive_ongoing = '';
if(isset($_POST['compaignLanguage'])) {
	$compaignLanguage = $_POST['compaignLanguage'];
	//$compaignLanguage = implode(",",$compaignLanguage);
}
else $compaignLanguage = 'English';//EN
if(isset($_POST['firstSeen'])) {
	$firstSeen = $_POST['firstSeen'];
	$firstSeen = str_replace('/','-',$firstSeen);
}
else $firstSeen = '0000-00-00';
if(isset($_POST['lastSeen'])) {
	$lastSeen = $_POST['lastSeen'];
	$lastSeen = str_replace('/','-',$lastSeen);
}
else $lastSeen = '0000-00-00';
if(isset($_POST['personalization'])) $personalization = $_POST['personalization'];
else $personalization = '2';
if(isset($_POST['gender'])) $gender = $_POST['gender'];
else $gender = 'N';
if(isset($_POST['age'])) {
	$age = $_POST['age'];
	$age = implode(',',$age);
}
else $age = '0';
if(isset($_POST['offerOrigin'])) $offerOrigin = $_POST['offerOrigin'];
else $offerOrigin = '';
if(isset($_POST['state'])) {
	$state = $_POST['state'];
	if(is_array($state)) $state = implode(',',$state);
}
else $state = '0';
if(isset($_POST['OfferExpiryDate']) && trim($_POST['OfferExpiryDate'])!='') {
	$OfferExpiryDate = $_POST['OfferExpiryDate'];
	$OfferExpiryDate = str_replace('/','-',$OfferExpiryDate);
}
else $OfferExpiryDate = '0000-00-00';
if(isset($_POST['groupSize'])) {
	$groupSize = $_POST['groupSize'];
	$groupSize = implode(',',$groupSize);
}
else $groupSize = '0';
if(isset($_POST['FeeProduct'])) $FeeProduct = $_POST['FeeProduct'];
else $FeeProduct = 0;
if(isset($_POST['FeeProductType'])) {
	$FeeProductType = $_POST['FeeProductType'];
	$FeeProductType = implode(",",$FeeProductType);
}
else $FeeProductType = '';
if(isset($_POST['worksiteVoluntary'])) $worksiteVoluntary = $_POST['worksiteVoluntary'];
else $worksiteVoluntary = 0;
if(isset($_POST['affinityAssociation'])) $affinityAssociation = $_POST['affinityAssociation'];
elseif(!empty($_POST['aff_ids'])) $affinityAssociation = 1;
else $affinityAssociation = 0;
if(isset($_POST['is_military'])) $is_military = (int)$_POST['is_military'];
else $is_military = 0;
//if(isset($_POST['is_multicultural'])) $is_multicultural = (int)$_POST['is_multicultural'];
//else $is_multicultural = 0;
if(isset($_POST['multiculturalmarkets'])) {
	$multiculturalmarkets = $_POST['multiculturalmarkets'];
	$multiculturalmarkets = implode(',',$multiculturalmarkets);
}
else $multiculturalmarkets = '';
if(isset($_POST['riders'])) {
	$riders = $_POST['riders'];
	$riders = implode(',',$riders);
}
else $riders = '';
if(isset($_POST['prescription'])) $prescription = (int)$_POST['prescription'];
else $prescription = 0;
if(isset($_FILES)) $fileArray = $_FILES;
else $fileArray = array();
if(isset($_POST['PDFContent'])) $PDFContent = $_POST['PDFContent'];
else $PDFContent = '';
if(isset($_POST['homePageFlag'])) $homePageFlag = $_POST['homePageFlag'];
else $homePageFlag = '1';
if(isset($_POST['is_affinion'])) $is_affinion = $_POST['is_affinion'];
else $is_affinion = 0;
if(isset($_POST['is_citi'])) $is_citi = $_POST['is_citi'];
else $is_citi = 0;
if(isset($_POST['variant'])) $variant = trim($_POST['variant']);
else $variant = '';
if(isset($_POST['variant_desc'])) $variant_desc = $_POST['variant_desc'];
else $variant_desc = '';
if(isset($_POST['vid'])) {
	$vid = $_POST['vid'];
	$vid = implode(',',$vid);
}
else $vid = '0';
$variantID = 0;
if(isset($_POST['incomeID'])) {
	$incomeID = $_POST['incomeID'];
	$incomeID = implode(',',$incomeID);
}
else $incomeID = '0';
if(isset($_POST['fa_id'])) {
	$fa_id = $_POST['fa_id'];
	$fa_id = implode(',',$fa_id);
}
else $fa_id = '0';
if(isset($_POST['tl_id'])) {
	$tl_id = $_POST['tl_id'];
	$tl_id = implode(',',$tl_id);
}
else $tl_id = '0';
if(isset($_POST['productComment'])) $productComment = $_POST['productComment'];
else $productComment = '';
$competi_ids_tmp = '';
$invitation_ids_tmp = '';
$tracking_ids_tmp = '';
$fico_ids_tmp = '';
$pub_ids_tmp = '';
$cmp_ids_tmp = '';
$aff_ids_tmp = '';

if(isset($_POST['electronicID'])) {
	$electronicID = $_POST['electronicID'];
	$electronicID = implode(",",$electronicID);
}
else $electronicID = '';
if(isset($_POST['consumer_insights'])) $consumer_insights = (int)$_POST['consumer_insights'];
else $consumer_insights = 0;
if(isset($_POST['is_subp'])) $is_subp = (int)$_POST['is_subp'];
else $is_subp = 0;
if(isset($_POST['DMSource'])) {
	$DMSource = $_POST['DMSource'];
	if(!empty($DMSource)){
		if($consumer_insights==0 && strpos($DMSource,'_CI_')!==false){
			$consumer_insights = 1;
		}
		if($is_subp==0 && preg_match('/^(\\d+)_(\\d+)_(\\d+)/',$DMSource,$matches)){
			$competi_id = $matches[1].'-'.$matches[2].'-'.$matches[3];
			$defs = "SELECT panelist_id,parent_panelist_id FROM cscan_panelists WHERE competi_id='$competi_id'";
			if(preg_match('/^\\d{3}\\-/',$competi_id)){
				$defs .= " OR competi_id='0$competi_id' ORDER BY competi_id LIMIT 1";
			}
			$resultD = $DRW->query($defs,$DRW_read);
			$dataD = $DRW->fetch_row($resultD);
			$p_id = (int)$dataD[0];
			$parent_panelist_id = (int)$dataD[1];
			if($parent_panelist_id>0){
				$is_subp = 1;
			}
		}
	}
}
else $DMSource = '';
if(isset($_POST['external_link'])) {
	$external_link = trim($_POST['external_link']);
	if(!empty($external_link) && !preg_match('/^https?:\\/\\//i',$external_link)){
		$external_link = 'http://'.$external_link;
	}
}
else $external_link = '';
if(isset($_POST['social_media_name'])) $social_media_name = $_POST['social_media_name'];
else $social_media_name = '';
if(isset($_POST['external_updates'])) $external_updates = (int)$_POST['external_updates'];
else $external_updates = 0;
if(isset($_POST['external_fans'])) $external_fans = (int)$_POST['external_fans'];
else $external_fans = 0;
if(isset($_POST['publication'])) $publication = $_POST['publication'];
else $publication = '';
if(isset($_POST['businessContent'])) $businessContent = $_POST['businessContent'];
else $businessContent = 0;
$muid = '';
$addedToDatabase = '';
if(isset($_POST['productStatus'])) $productStatus = (int)$_POST['productStatus'];
else $productStatus = 2;
if(isset($_POST['old_productStatus'])) $old_productStatus = (int)$_POST['old_productStatus'];
else $old_productStatus = 0;
$productStatusDesc = 0;
$admin_userID = 0;
if(isset($_POST['assigned_admin_userID'])) $assigned_admin_userID = (int)$_POST['assigned_admin_userID'];
else $assigned_admin_userID = 0;
if(isset($_POST['img_companyID'])) $img_companyID = (int)$_POST['img_companyID'];
else $img_companyID = 0;
if(isset($_POST['is_mover'])) $is_mover = (int)$_POST['is_mover'];
else $is_mover = 0;
if(isset($_POST['is_hphsa'])) $is_hphsa = (int)$_POST['is_hphsa'];
else $is_hphsa = 0;
if(isset($_POST['is_prescreen'])) $is_prescreen = (int)$_POST['is_prescreen'];
else $is_prescreen = 0;
if(isset($_POST['is_state_specific'])) $is_state_specific = (int)$_POST['is_state_specific'];
else $is_state_specific = 0;
if(isset($_POST['primary_country'])) $primary_country = $_POST['primary_country'];
else $primary_country = '';
if($fromtemp){
	if(isset($_GET['muid'])) $muid = $_GET['muid'];
	else $muid = '';
	if(isset($_REQUEST['isTmp'])) $isTmp = '1';
	else $isTmp = '0';
	if(isset($_POST['tmp_priority'])) $tmp_priority = (int)$_POST['tmp_priority'];
	else $tmp_priority = 0;
	if(isset($_POST['competi_ids'])) $competi_ids = $_POST['competi_ids'];
	else $competi_ids = '';
	if(isset($_POST['invitation_ids'])) $invitation_ids = $_POST['invitation_ids'];
	else $invitation_ids = '';
	if(isset($_POST['tracking_ids'])) $tracking_ids = $_POST['tracking_ids'];
	else $tracking_ids = '';
	if(isset($_POST['fico_ids'])) $fico_ids = $_POST['fico_ids'];
	else $fico_ids = '';
	if(isset($_POST['pub_ids'])) $pub_ids = $_POST['pub_ids'];
	else $pub_ids = '';
	if(isset($_POST['cmp_ids'])) {
		$cmp_ids = $_POST['cmp_ids'];
	}
	else {
		$cmp_ids = '';
	}
	if(isset($_POST['aff_ids'])) $aff_ids = $_POST['aff_ids'];
	else $aff_ids = '';
}

foreach($addlArray as $o){
	while($o->getNext()){
		$field = $o->getField();
		if($field!=''){
			if(isset($_POST[$field])){
				$temp = $_POST[$field];
			}
			else{
				$temp = '';
			}
			$$field = $o->doPrepare($temp);
		}
	}
}
$new_preview = false;
if(isset($_POST['save'])) {
	if($fromtemp){
	    if(($mChannelID==1 || $mChannelID==2) && !checkGroup(21)){ // no Direct Mail || Trade Mag
			$addText = "tmp_productStatus=2,";
		}
		else $addText = '';
		
		if($muid=='') {
			$sql = "INSERT INTO `cscan_tmp_inc` SET `ctt`=NOW()";
			$DRW->query($sql,$DRW_main);
			$muid = $DRW->insert_id($DRW_main);
			$isTmp = '1';
		}
		elseif(isset($_REQUEST['isTmp'])) $isTmp = '1';
		else $isTmp = '0';
		
		$sql = "REPLACE INTO `cscan_admin_log` SET userID={$AUTH_DATA['userID']},logDate=NOW(),muid='".$DRW->real_escape_string($muid)."',isTmp=$isTmp";
		$DRW->query($sql,$DRW_main);
		
		$coArray = explode(',',$cmp_ids);
		foreach($coArray as $co){
			$q = "SELECT companyName FROM cscan_company WHERE companyID=".(float)$co;
			$resultC = $DRW->query($q,$DRW_read);
			$dataC = $DRW->fetch_row($resultC);
			if($company==''){
				$company = $dataC[0];
			}
			else{
				if($secondCompany!=''){
					$secondCompany .= '; ';
				}
				$secondCompany .= $dataC[0];
			}
		}
		if(!empty($hy)){
			$history_year = $hy;
		}
		else{
			$history_year = 0;
			$hy = '';
		}
		$sql = "REPLACE INTO `cscan_product_email` SET 
			$addText
			tmp_admin_userID={$AUTH_DATA['userID']},
			tmp_priority=$tmp_priority,
			productName='".$DRW->real_escape_string($productName)."',
			company='".$DRW->real_escape_string($company)."',
			secondCompany='".$DRW->real_escape_string($secondCompany)."',
			productHeadline='".$DRW->real_escape_string($productHeadline)."',
			traffic_sources='".$DRW->real_escape_string($traffic_sources)."',
			sectorID='$sectorID',
			categoryID='$categoryID',
			subCategoryID= '$subCategoryID',
			subSubCategoryID= '$subSubCategoryID',
			combo_ids='$comboIDs',
			mChannelID='$mChannelID',
			mPanelID='$mPanelID',
			responseMechID='$responseMechID',
			agentCommunicationID='$agentCommunicationID',
			delmethid=$delmethid,
			incentive='".$DRW->real_escape_string($incentive)."',
			incentive_ongoing='".$DRW->real_escape_string($incentive_ongoing)."',
			compaignLanguage='".$DRW->real_escape_string($compaignLanguage)."',
			offerOrigin = '$offerOrigin',
			state = '$state',
			OfferExpiryDate='$OfferExpiryDate',
			groupSize = '$groupSize',
			FeeProduct ='$FeeProduct',
			FeeProductType='$FeeProductType',
			worksiteVoluntary ='$worksiteVoluntary',
			affinityAssociation ='$affinityAssociation',
			is_military=$is_military,
			multiculturalmarkets='$multiculturalmarkets',
			riders='$riders',
			prescription=$prescription,
			is_mover=$is_mover,
			is_hphsa=$is_hphsa,
			is_prescreen=$is_prescreen,
			is_state_specific=$is_state_specific,
			primary_country='$primary_country',
			addedToDatabase = NOW(),
			firstSeen = '$firstSeen',
			lastSeen ='$lastSeen',
			homePageFlag='$homePageFlag',
			is_affinion='$is_affinion',
			is_citi='$is_citi',
			mTypeID = '$mTypeID',
			mPackItemID = '$mPackItemID',
			gender = '$gender',
			personalization = '$personalization',
			age = '$age',
			muid='".$DRW->real_escape_string($muid)."',
			isTmp=$isTmp,
			variant_entryID='".$DRW->real_escape_string($variant)."',
			variant_desc='".$DRW->real_escape_string($variant_desc)."',
			vid='".$DRW->real_escape_string($vid)."',
			incomeID='".$DRW->real_escape_string($incomeID)."',
			DMSource='".$DRW->real_escape_string($DMSource)."',
			external_link='".$DRW->real_escape_string($external_link)."',
			social_media_name='".$DRW->real_escape_string($social_media_name)."',
			external_updates='".$DRW->real_escape_string($external_updates)."',
			external_fans='".$DRW->real_escape_string($external_fans)."',
			publication='".$DRW->real_escape_string($publication)."',
			competi_ids='".$DRW->real_escape_string($competi_ids)."',
			fa_ids='".$DRW->real_escape_string($fa_id)."',
			tl_ids='".$DRW->real_escape_string($tl_id)."',
			affinityAssociationVal='".$DRW->real_escape_string($affinityAssociationVal)."',
			pub_ids='".$DRW->real_escape_string($pub_ids)."',
			aff_ids='".$DRW->real_escape_string($aff_ids)."',
			cmp_ids='".$DRW->real_escape_string($cmp_ids)."',
			invitation_ids='".$DRW->real_escape_string($invitation_ids)."',
			tracking_ids='".$DRW->real_escape_string($tracking_ids)."',
			fico_ids='".$DRW->real_escape_string($fico_ids)."',
			electronicID='".$DRW->real_escape_string($electronicID)."',
			businessContent='".$DRW->real_escape_string($businessContent)."',
			IssueTypeID='$IssueTypeID',
			history_year=$history_year";
		$DRW->query($sql,$DRW_main);
		$query = "UPDATE `cscan_email$hy` SET `deleted`='2' WHERE `muid`='".$DRW->real_escape_string($muid)."'";
		$DRW->query($query,$DRW_main);
		
		foreach($addlArray as $o){
			if(isset($_POST['part_'.$o->id]) && $_POST['part_'.$o->id]==1){
				$fields = $o->fields;
			}
			else{
				$fields = array();
			}
			saveExtraData($fields,'muid,isTmp',"$muid,$isTmp",$o->table.'_temp',"muid=$muid AND isTmp=$isTmp");
		}
	} else {
		$name = array();
		$name[] = sectorName($sectorID);
		if($categoryID == '') $categoryID = '0';
		else $name[] = categoryName($categoryID);
		if($subCategoryID =='') $subCategoryID = '0';
		else $name[] = subCategoryName($subCategoryID);
		if($subSubCategoryID =='') $subSubCategoryID = '0';
		else $name[] = subCategoryName($subSubCategoryID);
		if($mChannelID != 0) $name[] = mediaChannelName($mChannelID);
		if($mPanelID != 0) $name[] = mediaPanelName($mPanelID);
		if($agentCommunicationID!= '') $name[] = agentName($agentCommunicationID);
		$name = implode(",",$name);
		if($state=='') $state = '0';
		if($groupSize=='') $groupSize = '0';
		
		# Starting of code to check existing of the Product Name
		$checkQuery = "select SQL_NO_CACHE productName From cscan_product_detail where productHeadline = '".$DRW->real_escape_string($productHeadline)."' AND productID<>'$updID'";
		$checkQuery = $DRW->query($checkQuery,$DRW_read);
		$rowcount = $DRW->num_rows($checkQuery);
		if($productHeadline!='' && $rowcount!=0) {
			$productStatus = 2;
		}
		# Starting of block to Update product 
		if($variant!=''){
			$checkV = "SELECT SQL_NO_CACHE productID FROM cscan_product_detail WHERE entryID='".$DRW->real_escape_string($variant)."'";
			$checkV = $DRW->query($checkV,$DRW_read);
			$dataV = $DRW->fetch_row($checkV);
			if($dataV[0]!='' && $dataV[0]!=$updID) $variantID = $dataV[0];
		}
		
		if(isset($_POST['cmp_ids'])) {
			$cmp_ids = explode(',',$_POST['cmp_ids']);
			foreach($cmp_ids as $co){
				$q = "SELECT companyName FROM cscan_company WHERE companyID=".(float)$co;
				$resultC = $DRW->query($q,$DRW_read);
				$dataC = $DRW->fetch_row($resultC);
				if($company==''){
					$company = $dataC[0];
					if(isset($_REQUEST['defaultCoImg']) && $img_companyID==0){
						$img_companyID = (float)$co;
					}
				}
				else{
					if($secondCompany!=''){
						$secondCompany .= '; ';
					}
					$secondCompany .= $dataC[0];
				}
			}
		}
		$pids = array();
		$competi_ids = array();
		$invitation_ids = array();
		$tracking_ids = array();
		$fico_ids = array();
		$old_competi_ids = array();
		$old_invitation_ids = array();
		$old_tracking_ids = array();
		$old_fico_ids = array();
		$parr = array();
		if(isset($_POST['competi_ids'])){
			$competi_ids = explode(',',$_POST['competi_ids']);
			$invitation_ids = explode('|',$_POST['invitation_ids']);
			$tracking_ids = explode('|',$_POST['tracking_ids']);
			$fico_ids = explode('|',$_POST['fico_ids']);
			$sql = "SELECT age_pID,age_pmin FROM cscan_age_product ORDER BY age_psort";
			$result = $DRW->query( $sql,$DRW_read );
			while( $row = $DRW->fetch_row( $result ) ){
				$parr[$row[0]] = $row[1];
			}
			if(!empty($updID)){
				$resultP = $DRW->query("SELECT SQL_NO_CACHE panelist_id,ppdate,invitationID,trackingID,pproductFICO FROM cscan_panelists_product WHERE productID=$updID",$DRW_read);
				while($dataP = $DRW->fetch_row($resultP)){
					$old_competi_ids[] = $dataP[0].'|'.$dataP[1];
					$old_invitation_ids[] = $dataP[2];
					$old_tracking_ids[] = $dataP[3];
					$old_fico_ids[] = $dataP[4];
				}
			}
		}
		if(isset($_POST['pub_ids'])) {
			$pub_ids = explode(',',$_POST['pub_ids']);
		}
		else{
			$pub_ids = array();
		}
		$log_productStatus = 0;
		$new_entryID = '';
		$days = 7;
		$currtime = time();
		$currdate = date('Y-m-d',$currtime);
		if(isset($_REQUEST['old_entryID'])) $old_entryID = $_REQUEST['old_entryID'];
		else $old_entryID = '';
		$addText = '';
		if(isset($_REQUEST['isTmp'])) $isTmp = '1';
		else $isTmp = '0';
		if(isset($_REQUEST['muid'])) $muid = $_REQUEST['muid'];
		if(($productStatus==1 || $productStatus==2) && ($mChannelID==1 || $mChannelID==2) && !checkGroup(21) && $muid==''){ // no Direct Mail
			$addText .= ", productStatus=2,productStatusDesc=1";
			$productStatus = 2;
		}
		elseif($productStatus==1 && $old_productStatus!=1) {
			$approve = true;
			if($_REQUEST['old_productStatusDesc']==1 && !checkGroup(21)) {
				$approve = false;//Direct Mail
			}
			if($_REQUEST['old_productStatusDesc']==2 && !checkGroup(6)) {
				$approve = false;//Company
			}
			if($approve){
				$addText .= ', productStatus=1,productStatusDesc=0,approved_date=NOW()';
			}
			else{
				$productStatus = $old_productStatus;
			}
		}
		else{
			$addText .= ", productStatus=$productStatus";
		}
		if($productStatus==2 && $assigned_admin_userID==0){
			$addText .= ", assigned_admin_userID=".getAssignment($muid,$isTmp,$sectorID);
		}
		else{
			$addText .= ", assigned_admin_userID=".$assigned_admin_userID;
		}
		if($productStatus==1){
			foreach($competi_ids as $k=>$p_id_date){
				if($p_id_date!=''){
					list($p_id,$ppdate) = explode('|',$p_id_date);
					$ppdate_part = substr($ppdate,0,10);
					if($ppdate_part<$firstSeen || $firstSeen=='0000-00-00'){
						$firstSeen = $ppdate_part;
					}
					if($ppdate_part>$lastSeen || $lastSeen=='0000-00-00'){
						$lastSeen = $ppdate_part;
					}
				}
			}
			foreach($pub_ids as $p_id_date){
				if($p_id_date!=''){
					list($p_id,$ppdate) = explode('|',$p_id_date);
					$ppdate_part = substr($ppdate,0,4).'-'.substr($ppdate,4,2).'-'.substr($ppdate,6,2);
					if($ppdate_part<$firstSeen || $firstSeen=='0000-00-00'){
						$firstSeen = $ppdate_part;
					}
					if($ppdate_part>$lastSeen || $lastSeen=='0000-00-00'){
						$lastSeen = $ppdate_part;
					}
				}
			}
			if(substr($old_entryID,0,10)!=$entryID && preg_match('/^\\d{4}\\-\\d{2}\\-\\d{2}$/',$entryID)){
				$new_entryID = generate_entryID(true,$entryID);
			}
			elseif(empty($old_entryID)){
				$new_entryID = generate_entryID(true);
			}
			elseif($firstSeen=='0000-00-00'){
				$lastSeen = $firstSeen = substr($old_entryID,0,10);
			}
			if(!empty($new_entryID)){
				$changedate = substr($new_entryID,0,10);
				if($firstSeen=='0000-00-00'){
					$lastSeen = $firstSeen = $changedate;
				}
				$entryID_sort1 = intval(preg_replace('/[^0-9]+/','',substr($new_entryID,0,10)));
				$entryID_sort2 = intval(substr($new_entryID,11));
				$addText .= ", entryID='$new_entryID', entryID_sort1=$entryID_sort1, entryID_sort2=$entryID_sort2";
				$changedatetime = strtotime($changedate.' 00:00:00');
				if($changedatetime<=$currtime && ($currtime-$changedatetime)<=(86400*$days) && $old_productStatus!=1) {
					$addText .= ", addedToDatabase=NOW()";
				}
				else{
					$addText .= ", addedToDatabase='$changedate ".date('H:i:s')."'";
				}
			}
			$addText .= ", firstSeen='$firstSeen', lastSeen='$lastSeen'";	
		}
		if($productStatus==1 && $variantID!=0){
			$addText .= ',isVariant=1';
		}
		
		if($updID !='') {
			$sql = "update cscan_product_detail set 
				productName='".$DRW->real_escape_string($productName)."'$addText,
				company='".$DRW->real_escape_string($company)."',
				secondCompany='".$DRW->real_escape_string($secondCompany)."',
				productHeadline = '".$DRW->real_escape_string($productHeadline)."',
				traffic_sources = '".$DRW->real_escape_string($traffic_sources)."',
				sectorID='$sectorID',
				categoryID='$categoryID',
				subCategoryID='$subCategoryID',
				subSubCategoryID='$subSubCategoryID',
				mChannelID='$mChannelID',
				mPanelID='$mPanelID',
				responseMechID='$responseMechID',
				incentive='".$DRW->real_escape_string($incentive)."',
				incentive_ongoing='".$DRW->real_escape_string($incentive_ongoing)."',
				compaignLanguage='".$DRW->real_escape_string($compaignLanguage)."',
				homePageFlag='$homePageFlag',
				is_affinion='$is_affinion',
				is_citi='$is_citi',
				mTypeID='$mTypeID',
				mPackItemID='$mPackItemID',
				offerOrigin='$offerOrigin',
				state='$state',
				OfferExpiryDate='$OfferExpiryDate',
				groupSize='$groupSize',
				FeeProduct='$FeeProduct',
				FeeProductType='$FeeProductType',
				worksiteVoluntary='$worksiteVoluntary',
				affinityAssociation='$affinityAssociation',
				is_military=$is_military,
				multiculturalmarkets='$multiculturalmarkets',
				riders='$riders',
				prescription=$prescription,
				is_mover=$is_mover,
				is_hphsa=$is_hphsa,
				is_prescreen=$is_prescreen,
				is_state_specific=$is_state_specific,
				primary_country='$primary_country',
				searchText='".$DRW->real_escape_string($name)."',
				gender='$gender',
				personalization='$personalization',
				age='$age',
				cardStatus='$cardStatus',
				agentCommunicationID='".$agentCommunicationID."',
				delmethid=$delmethid,
				actual_addedToDatabase=NOW(),
				admin_userID={$AUTH_DATA['userID']},
				vid='".$DRW->real_escape_string($vid)."',
				variantID=$variantID,
				variant_desc='".$DRW->real_escape_string($variant_desc)."',
				incomeID='".$DRW->real_escape_string($incomeID)."',
				DMSource='".$DRW->real_escape_string($DMSource)."',
				external_link='".$DRW->real_escape_string($external_link)."',
				social_media_name='".$DRW->real_escape_string($social_media_name)."',
				external_updates='".$DRW->real_escape_string($external_updates)."',
				external_fans='".$DRW->real_escape_string($external_fans)."',
				productComment='".$DRW->real_escape_string($productComment)."',
				publication='".$DRW->real_escape_string($publication)."',
				fa_ids='".$DRW->real_escape_string($fa_id)."',
				tl_ids='".$DRW->real_escape_string($tl_id)."',
				affinityAssociationVal='".$DRW->real_escape_string($affinityAssociationVal)."',
				electronicID='".$DRW->real_escape_string($electronicID)."',
				businessContent='".$DRW->real_escape_string($businessContent)."',
				IssueTypeID='$IssueTypeID',
				consumer_insights=$consumer_insights,
				is_subp=$is_subp
				where productID='$updID'"; 
			$DRW->query($sql,$DRW_main);
		}
		else {
			$sql = "insert into cscan_product_detail set 
				productName='".$DRW->real_escape_string($productName)."'$addText,
				company='".$DRW->real_escape_string($company)."',
				secondCompany='".$DRW->real_escape_string($secondCompany)."',
				productHeadline='".$DRW->real_escape_string($productHeadline)."',
				traffic_sources='".$DRW->real_escape_string($traffic_sources)."',
				sectorID='$sectorID',
				categoryID='$categoryID',
				subCategoryID= '$subCategoryID',
				subSubCategoryID= '$subSubCategoryID',
				mChannelID='$mChannelID',
				mPanelID='$mPanelID',
				responseMechID='$responseMechID',
				incentive='".$DRW->real_escape_string($incentive)."',
				incentive_ongoing='".$DRW->real_escape_string($incentive_ongoing)."',
				compaignLanguage='".$DRW->real_escape_string($compaignLanguage)."',
				homePageFlag='$homePageFlag',
				is_affinion='$is_affinion',
				is_citi='$is_citi',
				mTypeID='$mTypeID',
				mPackItemID='$mPackItemID',
				offerOrigin='$offerOrigin',
				state='$state',
				OfferExpiryDate='$OfferExpiryDate',
				groupSize='$groupSize',
				FeeProduct='$FeeProduct',
				FeeProductType='$FeeProductType',
				worksiteVoluntary='$worksiteVoluntary',
				affinityAssociation='$affinityAssociation',
				is_military=$is_military,
				multiculturalmarkets='$multiculturalmarkets',
				riders='$riders',
				prescription=$prescription,
				is_mover=$is_mover,
				is_hphsa=$is_hphsa,
				is_prescreen=$is_prescreen,
				is_state_specific=$is_state_specific,
				primary_country='$primary_country',
				searchText='".$DRW->real_escape_string($name)."',
				gender='$gender',
				personalization='$personalization',
				age='$age',
				cardStatus='$cardStatus',
				agentCommunicationID='$agentCommunicationID',
				delmethid=$delmethid,
				actual_addedToDatabase=NOW(),
				admin_userID={$AUTH_DATA['userID']},
				vid='".$DRW->real_escape_string($vid)."',
				variantID=$variantID,
				variant_desc='".$DRW->real_escape_string($variant_desc)."',
				incomeID='".$DRW->real_escape_string($incomeID)."',
				DMSource='".$DRW->real_escape_string($DMSource)."',
				external_link='".$DRW->real_escape_string($external_link)."',
				social_media_name='".$DRW->real_escape_string($social_media_name)."',
				external_updates='".$DRW->real_escape_string($external_updates)."',
				external_fans='".$DRW->real_escape_string($external_fans)."',
				productComment='".$DRW->real_escape_string($productComment)."',
				publication='".$DRW->real_escape_string($publication)."',
				fa_ids='".$DRW->real_escape_string($fa_id)."',
				tl_ids='".$DRW->real_escape_string($tl_id)."',
				affinityAssociationVal='".$DRW->real_escape_string($affinityAssociationVal)."',
				electronicID='".$DRW->real_escape_string($electronicID)."',
				businessContent='".$DRW->real_escape_string($businessContent)."',
				IssueTypeID='$IssueTypeID',
				consumer_insights=$consumer_insights,
				is_subp=$is_subp";
			$DRW->query($sql,$DRW_main);
			$updID = $DRW->insert_id($DRW_main);
			
			if($muid!=''){
				$sqlU = "UPDATE `cscan_product_email` SET `productID`='".$DRW->real_escape_string($updID)."' WHERE `muid`='".$DRW->real_escape_string($muid)."' AND isTmp=$isTmp";
				$DRW->query($sqlU,$DRW_main);
			}
		}

        if(empty($fileArray['PDFFILE']['name'])) {
            $document = new Document($DRW, $DRW_main);
            $document->get_document($updID);

            // Add a default PDF for Mobile (7) or Online Media Channel (5)
            if ($document->document === false && ($mChannelID == 5 || $mChannelID == 7)) {
                $default_doc = $document->get_default();
                $default_doc->productID = $updID;
                $document->set_document($default_doc);

                $default_img = $document->get_default(false);
                $default_img->productID = $updID;
                $document->set_image_document($default_img);
            }
        }
        else {
            $error_pdf = uploadpdfnew($updID,$fileArray,$PDFContent,$productStatus);
        }

		$error_media = uploadpdfnew($updID,$fileArray,'',$productStatus,'mediafile');
		$img_error = uploadImagenew($updID,$fileArray,$img_companyID);
		$log_productStatus = $productStatus;

		if ($productStatus == 1 && $variantID != 0) {
			$sqlu = "UPDATE cscan_product_detail SET isVariant=1 WHERE productID=$variantID";
			$DRW->query($sqlu,$DRW_main);
		}
		
		$sqlU = "DELETE FROM cscan_scsc_product WHERE productID=$updID";
		$DRW->query($sqlU,$DRW_main);
		$one = false;
		
		if(!empty($comboIDs)){
			$scsc_sort = 0;
			foreach($comboIDsA as $combo){
				list($s,$c,$sc,$ssc) = explode('_',$combo);
				$ssc = (int)$ssc;////remove
				if(!empty($s) || !empty($c) || !empty($sc) || !empty($ssc)){
					$scsc_sort++;
					$sqlU = "INSERT IGNORE INTO cscan_scsc_product (productID,scsc_sectorID,scsc_categoryID,scsc_subCategoryID,scsc_subSubCategoryID,scsc_sort) VALUES ($updID,$s,$c,$sc,$ssc,$scsc_sort)";
					$DRW->query($sqlU,$DRW_main);
					$one = true;
                                        
                                        if($scsc_sort==1){
                                           // $last_prd_sql = "UPDATE cscan_product_detail SET scsc_sort=1 WHERE productID='$updID'";
                                            $last_prd_sql = "UPDATE cscan_product_detail SET scsc_sort=1,scsc_sectorID_sort='".$s."',scsc_categoryID_sort='".$c."',scsc_subCategoryID_sort='".$sc."',scsc_subsubCategoryID_sort='".$ssc."' WHERE productID='$updID'";
                                            
                                            $DRW->query($last_prd_sql,$DRW_main);
                                        }
				}
			}
		}
		
		if(!$one){
			$sqlU = "INSERT IGNORE INTO cscan_scsc_product (productID,scsc_sectorID,scsc_categoryID,scsc_subCategoryID,scsc_subSubCategoryID) VALUES ($updID,0,0,0,0)";
			$DRW->query($sqlU,$DRW_main);
		}
		
		$sql = "INSERT IGNORE INTO `cscan_admin_log` SET userID={$AUTH_DATA['userID']},logDate=NOW(),productID=$updID,productStatus=$log_productStatus";
		$DRW->query($sql,$DRW_main);
		
		foreach($competi_ids as $k=>$p_id_date){
			if($p_id_date!=''){
				$del = array_search($p_id_date,$old_competi_ids);
				if($del!==false){
					unset($old_competi_ids[$del]);
					list($p_id,$ppdate) = explode('|',$p_id_date);
					if($p_id!=0){
						$pids[] = $p_id;
						if($invitation_ids[$k]!=$old_invitation_ids[$k] || $tracking_ids[$k]!=$old_tracking_ids[$k] || $fico_ids[$k]!=$old_fico_ids[$k]){
							$sqlU = "UPDATE cscan_panelists_product SET invitationID='".$DRW->real_escape_string($invitation_ids[$k])."',trackingID='".$DRW->real_escape_string($tracking_ids[$k])."',pproductFICO='".$DRW->real_escape_string($fico_ids[$k])."' WHERE productID=$updID AND panelist_id=".(float)$p_id." AND ppdate='".$DRW->real_escape_string($ppdate)."'";
							$DRW->query($sqlU,$DRW_main);
						}
					}
				}
				else{
					list($p_id,$ppdate) = explode('|',$p_id_date);
					if($p_id!=0){
						$pids[] = $p_id;
						$defs = "SELECT DATEDIFF(CURDATE(),birthdate) as agedays,stateID,gender,homeownershipID,incomeID,fico_score,ownbiz,postalcode,parent_panelist_id
							FROM cscan_panelists WHERE panelist_id=".(float)$p_id;
						$resultD = $DRW->query($defs,$DRW_read);
						$dataD = $DRW->fetch_row($resultD);
						$ppage = floor($dataD[0]/365);
						$ppstateID = (int)$dataD[1];
						$pgender = strtoupper(substr(trim($dataD[2]),0,1));
						$homeownershipID = (int)$dataD[3];
						$pincomeID = (int)$dataD[4];
						$ppfico_score = (int)$dataD[5];
						$ownbiz = (int)$dataD[6];
						$pppostalcode = trim($dataD[7]);
						$parent_panelist_id = $dataD[8];
						if($parent_panelist_id>0){
							$pprimary = 0;
						}
						else{
							$pprimary = 1;
						}
						$ppageID = 0;
						foreach($parr as $pid=>$min){
							if($ppage>=$min){
								$ppageID = $pid;
							}
							else{
								break;
							}
						}
						
						$sqlU = "INSERT IGNORE INTO cscan_panelists_product (productID,panelist_id,ppdate,ppage,ppstateID,pgender,homeownershipID,pincomeID,ppageID,ppfico_score,invitationID,isBiz,pppostalcode,ppaddeddate,trackingID,pproductFICO,pprimary) 
							VALUES ($updID,".(float)$p_id.",'".$DRW->real_escape_string($ppdate)."',$ppage,$ppstateID,'$pgender',$homeownershipID,$pincomeID,$ppageID,$ppfico_score,'".$DRW->real_escape_string($invitation_ids[$k])."',$ownbiz,'".$DRW->real_escape_string($pppostalcode)."',NOW(),'".$DRW->real_escape_string($tracking_ids[$k])."','".$DRW->real_escape_string($fico_ids[$k])."',$pprimary)";
						$DRW->query($sqlU,$DRW_main);
					}
				}
			}
		}
		foreach($old_competi_ids as $p_id_date){
			if($p_id_date!=''){
				list($p_id,$ppdate) = explode('|',$p_id_date);
			
				$sqlU = "DELETE FROM cscan_panelists_product WHERE productID=$updID AND panelist_id=".(float)$p_id." AND ppdate='".$DRW->real_escape_string($ppdate)."'";
				$DRW->query($sqlU,$DRW_main);
			}
		}
		updateStateLookup($updID);
		if(count($pub_ids)>0){
			$sqlU = "DELETE FROM cscan_publication_product WHERE productID=$updID";
			$DRW->query($sqlU,$DRW_main);
			
			foreach($pub_ids as $p_id_date){
				if($p_id_date!=''){
					list($p_id,$ppdate) = explode('|',$p_id_date);
					$ppdate = substr($ppdate,0,4).'-'.substr($ppdate,4,2).'-'.substr($ppdate,6,2);
					$sqlU = "INSERT IGNORE INTO cscan_publication_product (productID,publicationID,monthYear) 
						VALUES ($updID,".(float)$p_id.",'".$DRW->real_escape_string($ppdate)."')";
					$DRW->query($sqlU,$DRW_main);
				}
			}
		}
		if(isset($_POST['cmp_ids'])) {
			$cmp_ids = explode(',',$_POST['cmp_ids']);
			
			$sqlU = "DELETE FROM cscan_company_product WHERE productID=$updID";
			$DRW->query($sqlU,$DRW_main);
			
			$primary_co = 1;
			foreach($cmp_ids as $p_id){
				if($p_id!=''){
					$sqlU = "INSERT IGNORE INTO cscan_company_product (productID,companyID,primary_co) 
						VALUES ($updID,".(float)$p_id.",$primary_co)";
					$DRW->query($sqlU,$DRW_main);
					$primary_co++;//$primary_co = 0;
				}
			}
		}
		if(isset($_POST['aff_ids'])) {
			$aff_ids = explode(',',$_POST['aff_ids']);
			
			$sqlU = "DELETE FROM cscan_affinity_product WHERE productID=$updID";
			$DRW->query($sqlU,$DRW_main);
			
			foreach($aff_ids as $p_id){
				if($p_id!=''){
					$sqlU = "INSERT IGNORE INTO cscan_affinity_product (productID,affinityID) 
						VALUES ($updID,".(float)$p_id.")";
					$DRW->query($sqlU,$DRW_main);
				}
			}
		}
		if(count($pids)>0){
			foreach($pids as $pid){
				
				//same as panelist_cron.php				
				$sqlu = "DELETE FROM cscan_panelist_affinity WHERE panelist_id=$pid";
				$DRW->query($sqlu,$DRW_main);
				$resultC = $DRW->query("SELECT DISTINCT pp.affinityID FROM cscan_affinity_product pp JOIN cscan_panelists_product as pa ON (pp.productID=pa.productID) JOIN cscan_product_detail pd ON (pp.productID=pd.productID)
					WHERE pa.panelist_id=$pid AND pd.sectorID NOT LIKE '%219%' AND pd.sectorID NOT REGEXP '[[:<:]]219[[:>:]]'",$DRW_read);// AND pd.sectorID NOT LIKE '%266%' AND pd.sectorID NOT REGEXP '[[:<:]]266[[:>:]]'
				while($dataC = $DRW->fetch_row($resultC)){
					$sqlu = "REPLACE INTO cscan_panelist_affinity (panelist_id,affinityID) VALUES ($pid,$dataC[0])";
					$DRW->query($sqlu,$DRW_main);
				}
				
				$sqlu = "DELETE FROM cscan_panelist_company WHERE panelist_id=$pid";
				$DRW->query($sqlu,$DRW_main);
				$resultC = $DRW->query("SELECT DISTINCT pp.companyID FROM cscan_company_product pp JOIN cscan_panelists_product as pa ON (pp.productID=pa.productID) JOIN cscan_product_detail pd ON (pa.productID=pd.productID) 
					WHERE pa.panelist_id=$pid AND primary_co=1 AND ((mChannelID=1 AND delmethid=1) OR mChannelID=3) AND (mTypeID='1' OR mTypeID='3') AND pd.sectorID NOT LIKE '%219%' AND pd.sectorID NOT REGEXP '[[:<:]]219[[:>:]]'",$DRW_read);// AND pd.sectorID NOT LIKE '%266%' AND pd.sectorID NOT REGEXP '[[:<:]]266[[:>:]]'
				while($dataC = $DRW->fetch_row($resultC)){
					$sqlu = "REPLACE INTO cscan_panelist_company (panelist_id,companyID) VALUES ($pid,$dataC[0])";
					$DRW->query($sqlu,$DRW_main);
				}
			}
		}
		foreach($addlArray as $o){
			if(isset($_POST['part_'.$o->id]) && $_POST['part_'.$o->id]==1){
				$fields = $o->fields;
			}
			else{
				$fields = array();
			}
			saveExtraData($fields,'productID',$updID,$o->table,"productID=$updID");
		}
		
		if($productHeadline!='' && $rowcount!=0) {
			$redirect_url = "addproduct.php?id=$updID&headline_err=1";
		}
		elseif($error_pdf>0) {
			$redirect_url = "addproduct.php?id=$updID&error_pdf=$error_pdf";
		}
		elseif($error_media>0) {
			$redirect_url = "addproduct.php?id=$updID&error_media=$error_media";
		}
		elseif($img_error>0) {
			$redirect_url = "addproduct.php?id=$updID&img_err=1";
		}
		elseif( isset($_POST['saveAndAdd']) ) {
			$redirect_url = "addproduct.php?new=1&more=1";
		}
		else{
			if($productStatus==1 && ($productStatus!=$old_productStatus || $new_preview)){
				$redirect_url = "managepdfSample.php?productID=$updID&next=manageproduct.php";
				if ($mChannelID == 5 || $mChannelID == 7) {
					$redirect_url .= '&did=2';
				}
			}
			else{
				$redirect_url = "manageproduct.php";//?pstat=$productStatus
				if(!empty($_REQUEST['pcopy_pop'])){
					$redirect_url .= '?pcopy_pop='.urlencode($_REQUEST['pcopy_pop']);
				}
			}
		}
		ob_end_clean();
		header("Location: $redirect_url");
		exit;
	}
}
elseif(!$fromtemp && $updID != ''){
	if(!isset($_POST['productName'])) {
		$sql = "SELECT SQL_NO_CACHE * FROM cscan_product_detail WHERE productID='$updID'";
		$result = $DRW->query( $sql,$DRW_read );
		if( $DRW->num_rows( $result ) > 0 ) {
			$dataAssoc = $DRW->fetch_assoc($result);
			
			$productName = $dataAssoc['productName'];
			$company = $dataAssoc['company'];
			$secondCompany = $dataAssoc['secondCompany'];
			$productHeadline = $dataAssoc['productHeadline'];
			$traffic_sources = $dataAssoc['traffic_sources'];
			$entryID = $dataAssoc['entryID'];
			$sectorID = $dataAssoc['sectorID'];
			$categoryID = $dataAssoc['categoryID'];
			$subCategoryID = $dataAssoc['subCategoryID'];
			$subSubCategoryID = $dataAssoc['subCategoryID'];
			$mChannelID = $dataAssoc['mChannelID'];
			$mPanelID = $dataAssoc['mPanelID'];
			$responseMechID = $dataAssoc['responseMechID'];
			$incentive = $dataAssoc['incentive'];
			$incentive_ongoing = $dataAssoc['incentive_ongoing'];
			$compaignLanguage = $dataAssoc['compaignLanguage'];
			$homePageFlag = $dataAssoc['homePageFlag'];
			$is_affinion = $dataAssoc['is_affinion'];
			$is_citi = $dataAssoc['is_citi'];
			$firstSeen = $dataAssoc['firstSeen']; 
			$lastSeen = $dataAssoc['lastSeen'];
			$mTypeID = $dataAssoc['mTypeID'];
			$mPackItemID = $dataAssoc['mPackItemID'];
			$agentCommunicationID = $dataAssoc['agentCommunicationID'];
			$delmethid = $dataAssoc['delmethid'];
			$offerOrigin = $dataAssoc['offerOrigin'];
			$state = $dataAssoc['state'];
			$OfferExpiryDate = $dataAssoc['OfferExpiryDate'];
			$groupSize = $dataAssoc['groupSize'];
			$FeeProduct = $dataAssoc['FeeProduct'];
			$FeeProductType = $dataAssoc['FeeProductType'];
			$worksiteVoluntary = $dataAssoc['worksiteVoluntary'];
			$affinityAssociation = $dataAssoc['affinityAssociation'];
			$is_military = $dataAssoc['is_military'];
			$multiculturalmarkets = $dataAssoc['multiculturalmarkets'];
			$riders = $dataAssoc['riders'];
			$prescription = $dataAssoc['prescription'];
			$is_mover = $dataAssoc['is_mover'];
			$is_hphsa = $dataAssoc['is_hphsa'];
			$is_prescreen = $dataAssoc['is_prescreen'];
			$is_state_specific = $dataAssoc['is_state_specific'];
			$primary_country = $dataAssoc['primary_country'];
			$gender = $dataAssoc['gender'];
			$personalization = $dataAssoc['personalization'];
			$age = $dataAssoc['age'];
			$cardStatus = $dataAssoc['cardStatus'];
			$addedToDatabase = $dataAssoc['addedToDatabase'];
			$productStatus = $dataAssoc['productStatus'];
			$productStatusDesc = $dataAssoc['productStatusDesc'];
			$variantID = $dataAssoc['variantID'];
			$variant_desc = $dataAssoc['variant_desc'];
			$vid = $dataAssoc['vid'];
			$incomeID = $dataAssoc['incomeID'];
			$DMSource = $dataAssoc['DMSource'];
			$external_link = $dataAssoc['external_link'];
			$social_media_name = $dataAssoc['social_media_name'];
			$external_updates = $dataAssoc['external_updates'];
			$external_fans = $dataAssoc['external_fans'];
			$publication = $dataAssoc['publication'];
			$admin_userID = $dataAssoc['admin_userID'];
			$assigned_admin_userID = $dataAssoc['assigned_admin_userID'];
			$productComment = $dataAssoc['productComment'];
			$fa_id = $dataAssoc['fa_ids'];
			$tl_id = $dataAssoc['tl_ids'];
			$affinityAssociationVal = $dataAssoc['affinityAssociationVal'];
			$electronicID = $dataAssoc['electronicID'];
			$businessContent = $dataAssoc['businessContent'];
			$IssueTypeID = $dataAssoc['IssueTypeID'];
			$consumer_insights = $dataAssoc['consumer_insights'];
			$is_subp = $dataAssoc['is_subp'];
		}
		
		foreach($addlArray as $o){
			$sql = "SELECT SQL_NO_CACHE * FROM ".$o->table." WHERE productID='$updID'";
			$result = $DRW->query( $sql,$DRW_read );
			if( $DRW->num_rows( $result ) > 0 ) {
				$dataAssoc = $DRW->fetch_assoc($result);
				
				foreach($o->fields as $f){
					if($f!='' && isset($dataAssoc[$f])){
						$$f = $dataAssoc[$f];
					}
				}
			}
		}
		$comboIDsA = array();
		$sqlU = "SELECT scsc_sectorID,scsc_categoryID,scsc_subCategoryID,scsc_subSubCategoryID FROM cscan_scsc_product WHERE productID='$updID' ORDER BY scsc_sort";
		$result = $DRW->query( $sqlU,$DRW_read );
		while($row = $DRW->fetch_row($result)) {
			$comboIDsA[] = implode('_',$row);
		}
		$comboIDs = implode('|',$comboIDsA);
	}
}
elseif($fromtemp || isset($_GET['muid'])){
	if(!$fromtemp){
		$muid = $_GET['muid'];
		if(isset($_REQUEST['isTmp'])) $isTmp = '1';
		else $isTmp = '0';
	}
	
	$sql = "SELECT SQL_NO_CACHE * FROM `cscan_product_email` WHERE `muid`='".$DRW->real_escape_string($muid)."' AND isTmp=$isTmp";
	$result = $DRW->query( $sql,$DRW_read );
	if( $DRW->num_rows( $result ) > 0 ) {
		$dataAssoc = $DRW->fetch_assoc($result);
		
		$productName = $dataAssoc['productName'];
		$company = $dataAssoc['company'];
		$secondCompany = $dataAssoc['secondCompany'];
		$productHeadline = $dataAssoc['productHeadline'];
		$traffic_sources = $dataAssoc['traffic_sources'];
		$entryID = $dataAssoc['entryID'];
		$sectorID = $dataAssoc['sectorID'];
		$categoryID = $dataAssoc['categoryID'];
		$subCategoryID = $dataAssoc['subCategoryID'];
		$subSubCategoryID = $dataAssoc['subSubCategoryID'];
		$mChannelID = $dataAssoc['mChannelID'];
		$mPanelID = $dataAssoc['mPanelID'];
		$responseMechID = $dataAssoc['responseMechID'];
		$incentive = $dataAssoc['incentive'];
		$incentive_ongoing = $dataAssoc['incentive_ongoing'];
		$compaignLanguage = $dataAssoc['compaignLanguage'];
		$homePageFlag = $dataAssoc['homePageFlag'];
		$is_affinion = $dataAssoc['is_affinion'];
		$is_citi = $dataAssoc['is_citi'];
		$firstSeen = $dataAssoc['firstSeen']; 
		$lastSeen = $dataAssoc['lastSeen'];
		$mTypeID = $dataAssoc['mTypeID'];
		$mPackItemID = $dataAssoc['mPackItemID'];
		$agentCommunicationID = $dataAssoc['agentCommunicationID'];
		$delmethid = $dataAssoc['delmethid'];
		$offerOrigin = $dataAssoc['offerOrigin'];
		$state = $dataAssoc['state'];
		$OfferExpiryDate = $dataAssoc['OfferExpiryDate'];
		$groupSize = $dataAssoc['groupSize'];
		$FeeProduct = $dataAssoc['FeeProduct'];
		$FeeProductType = $dataAssoc['FeeProductType'];
		$worksiteVoluntary = $dataAssoc['worksiteVoluntary'];
		$affinityAssociation = $dataAssoc['affinityAssociation'];
		$is_military = $dataAssoc['is_military'];
		$multiculturalmarkets = $dataAssoc['multiculturalmarkets'];
		$riders = $dataAssoc['riders'];
		$prescription = $dataAssoc['prescription'];
		$is_mover = $dataAssoc['is_mover'];
		$is_hphsa = $dataAssoc['is_hphsa'];
		$is_prescreen = $dataAssoc['is_prescreen'];
		$is_state_specific = $dataAssoc['is_state_specific'];
		$primary_country = $dataAssoc['primary_country'];
		$gender = $dataAssoc['gender'];
		$personalization = $dataAssoc['personalization'];
		$age = $dataAssoc['age'];
		$cardStatus = $dataAssoc['cardStatus'];
		$addedToDatabase = $dataAssoc['addedToDatabase'];
		$tmp_priority = $dataAssoc['tmp_priority'];
		$variant = $dataAssoc['variant_entryID'];
		$variant_desc = $dataAssoc['variant_desc'];
		$vid = $dataAssoc['vid'];
		$incomeID = $dataAssoc['incomeID'];
		$DMSource = $dataAssoc['DMSource'];
		$external_link = $dataAssoc['external_link'];
		$social_media_name = $dataAssoc['social_media_name'];
		$external_updates = $dataAssoc['external_updates'];
		$external_fans = $dataAssoc['external_fans'];
		$publication = $dataAssoc['publication'];
		$fa_id = $dataAssoc['fa_ids'];
		$tl_id = $dataAssoc['tl_ids'];
		$affinityAssociationVal = $dataAssoc['affinityAssociationVal'];
		$electronicID = $dataAssoc['electronicID'];
		$businessContent = $dataAssoc['businessContent'];
		$IssueTypeID = $dataAssoc['IssueTypeID'];
		$comboIDs = $dataAssoc['combo_ids'];
		
		if($fromtemp){
			$invitation_ids = $dataAssoc['invitation_ids'];
			$tracking_ids = $dataAssoc['tracking_ids'];
			$fico_ids = $dataAssoc['fico_ids'];
			$competi_ids = $dataAssoc['competi_ids'];
			$pub_ids = $dataAssoc['pub_ids'];
			$aff_ids = $dataAssoc['aff_ids'];
			$cmp_ids = $dataAssoc['cmp_ids'];
			$admin_userID = $dataAssoc['tmp_admin_userID'];
			
			$button = 'Update';
		}
		else{
			$invitation_ids_tmp = $dataAssoc['invitation_ids'];
			$tracking_ids_tmp = $dataAssoc['tracking_ids'];
			$fico_ids_tmp = $dataAssoc['fico_ids'];
			$competi_ids_tmp = $dataAssoc['competi_ids'];
			$pub_ids_tmp = $dataAssoc['pub_ids'];
			$cmp_ids_tmp = $dataAssoc['cmp_ids'];
			$aff_ids_tmp = $dataAssoc['aff_ids'];
		}
		
		foreach($addlArray as $o){
			$sql = "SELECT SQL_NO_CACHE * FROM ".$o->table."_temp WHERE muid='".$DRW->real_escape_string($muid)."' AND isTmp=$isTmp";
			$result = $DRW->query( $sql,$DRW_read );
			if( $DRW->num_rows( $result ) > 0 ) {
				$dataAssoc = $DRW->fetch_assoc($result);
				
				foreach($o->fields as $f){
					if($f!='' && isset($dataAssoc[$f])){
						$$f = $dataAssoc[$f];
					}
				}
			}
		}
	}
	elseif($fromtemp){
		if(isset($_GET['p_id'])){
			$competi_ids = $_GET['p_id'];
			if($competi_ids!=''){
				$invitation_ids = '|';
				$tracking_ids = '|';
				$fico_ids = '|';
			}
			$mPanelID = $_GET['p_panel'];
			$mChannelID = $_GET['p_channel'];
			$state = $_GET['p_state'];
			if($mPanelID==1 || $mPanelID==2){
				$age = $_GET['p_age'];
				$gender = $_GET['p_gender'];
				$incomeID = $_GET['p_income'];
			}
			if($mChannelID==3) {
				$electronicID = '1';
			}
		}
	}
}

//These functions was made overly complicated by Suntec. I have modified it a bit and added the chunk logic
//(originally from uploadPdf.php)
function uploadpdfnew($productID,$fileArray,$pdfContent,$productStatus=0,$filekey='PDFFILE'){
	$AUTH_DATA = $GLOBALS['AUTH_DATA'];
	if(isset($fileArray[$filekey])){
		$pdfNameArr = preg_replace('/[^a-zA-Z0-9_\\.\\-]/','_',$fileArray[$filekey]['name']);
		$pdfTypeArr = $fileArray[$filekey]['type'];
		$pdfTempNameArr = $fileArray[$filekey]['tmp_name'];
		$pdfSizeArr = $fileArray[$filekey]['size'];
	}
	else{
		$pdfNameArr = '';
		$pdfTypeArr = '';
		$pdfTempNameArr = '';
		$pdfSizeArr = 0;
	}
	$message = 0;
	$yearpath = date('Y/');
	$monthpath = date('m/');
	$datepath = $yearpath.$monthpath;
	
	$root = dirname(__FILE__);
	$root = substr($root,0,strpos($root,'/admin'));
	$pdfpart = $root.'/PDF/';
	$pdfPath = "$pdfpart$datepath$productID/";
	
	if($pdfNameArr!='') {
		if($filekey=='PDFFILE'){
			$valid_types = array("pdf");
		}
		else{
			$valid_types = array("swf","gif","jpg","png");
		}
		$name_arr = explode(".",$pdfNameArr);
		if(!empty($name_arr[1])){
			$ext_name = strtolower($name_arr[1]);
		}
		else{
			$ext_name = '';
		}
		$ext_type = strtolower(substr($pdfTypeArr,-3));
		if(in_array($ext_type, $valid_types) || in_array($ext_name, $valid_types)) {
			if(is_uploaded_file($pdfTempNameArr)) {
				if(!is_dir($pdfpart.$yearpath)){
					mkdir($pdfpart.$yearpath,02755);
				}
				if(!is_dir($pdfpart.$datepath)){
					mkdir($pdfpart.$datepath,02755);
				}
				if(!is_dir($pdfPath)){
					mkdir($pdfPath,02755);
				}
				$pdfName = substr($pdfNameArr,0,-4);
				$pdfName = $pdfName."_".$productID.".".$ext_name;
				
				if(move_uploaded_file($pdfTempNameArr, $pdfPath.$pdfName)) {
					if($productStatus==1){
						$back = false;	
					}
					else{
						$back = true;
					}
					if($filekey=='PDFFILE'){
						createPreviewJPG($pdfPath,$pdfName,$productID,$back);
						$content_type = "application/pdf";
					}
					else{
						if ($ext_name == "swf"){
							$content_type = "application/x-shockwave-flash";
						}
						elseif ($ext_name == "gif"){
							$content_type = "image/gif";
						}
						elseif (($ext_name == "jpg") || ($ext_name == "jpeg")){
							$content_type = "image/jpeg";
						}
						else{
							$content_type = "image/png";
						}
					}
					$document_id = savePDFData($productID,$pdfPath,$pdfName,$pdfContent,'/PDF/'.$datepath.$productID.'/',false,$content_type);
					$GLOBALS['new_preview'] = true;
				}
				else $message = 1;
			}
			else $message = 2;
		}
		else $message = 3;
	}
	elseif($pdfContent!="") {
		savePDFText($productID,1,$pdfContent);
	}
	return $message;
}

//(originally from uploadPdf.php)
function uploadImagenew($productID,$fileArray,$img_companyID=0){
	$AUTH_DATA = $GLOBALS['AUTH_DATA'];
	if(isset($fileArray['imgFile'])){
		$imgNameArr = preg_replace('/[^a-zA-Z0-9_\\.\\-]/','_',$fileArray['imgFile']['name']);
		$imgTypeArr = $fileArray['imgFile']['type'];
		$imgTempNameArr = $fileArray['imgFile']['tmp_name'];
		$imgSizeArr = $fileArray['imgFile']['size'];
	}
	else{
		$imgNameArr = '';
		$imgTypeArr = '';
		$imgTempNameArr = '';
		$imgSizeArr = 0;
	}
	
	$yearpath = date('Y/');
	$monthpath = date('m/');
	$root = dirname(__FILE__);
	$root = substr($root,0,strpos($root,'/admin'));
	$pathpart = $root.'/productImages/';
	$datepath = $yearpath.$monthpath;
	
	$message = 0;
	$imagePath = "$pathpart$datepath$productID/";
	if($imgNameArr!='') {
		if($imgSizeArr <=2000000 && substr($imgTypeArr,0,5)=="image" && substr($imgNameArr,-3)!="bmp") {
			if(is_uploaded_file($imgTempNameArr)) {
				
				if(!is_dir($pathpart.$yearpath)){
					mkdir($pathpart.$yearpath,02755);
				}
				if(!is_dir($pathpart.$datepath)){
					mkdir($pathpart.$datepath,02755);
				}
				if(!is_dir($imagePath)){
					mkdir($imagePath,02755);
				}
				
				$ext = explode('.',$imgNameArr);
				$ext = $ext[count($ext)-1];
				$imageName = $productID.".".$ext;
				$thumbimageName = "thumb".$imageName;
				
				if (move_uploaded_file($imgTempNameArr, $imagePath.$imageName)) {
					$complete = createthumb($imagePath.$imageName,$imagePath.$thumbimageName,150,100);
					if($complete){
						if(isset($_REQUEST['defaultCoImg']) && $img_companyID!=0){
							saveCompanyImgDB($img_companyID,$imagePath.$thumbimageName,$imgTypeArr,$imgSizeArr);
							unlink($imagePath.$thumbimageName);
							saveImageData($productID,'','','',$img_companyID);
						}
						else{
							saveImageData($productID,$imagePath,$thumbimageName,'/productImages/'.$datepath.$productID.'/');
						}
						unlink($imagePath.$imageName);
					}
					else $message=1;
					
					$GLOBALS['new_preview'] = true;
				}
				else $message=2;
			}
			else $message=3;
		}
		else $message = 4;
	}
	elseif($img_companyID!=0){
		saveImageData($productID,'','','',$img_companyID);
	}
	
	return $message;
}

function getAssignment($mid,$istmp,$sectorID=''){
	global $DRW,$DRW_read,$DRW_main,$DRW_crm;
	$mid = (float)$mid;
	$assigned_admin_userID = 0;
	if($mid!=''){
		$sql2 = "SELECT SQL_NO_CACHE userID FROM cscan_admin_log WHERE productID=0 AND muid=$mid AND isTmp=$istmp ORDER BY logDate DESC LIMIT 1";
		$rs2 = $DRW->query($sql2,$DRW_read);
		$row2 = $DRW->fetch_row($rs2);
		$assigned_admin_userID = (int)$row2[0];
	}
	if($assigned_admin_userID==0){
		$core_id = 'N';
		$is_assign_queue = 2;
		$sIDs = explode(',',$sectorID);
		$where_core = '';
		$coreArray = array();
		$noncoreArray = array();
		$sql = "SELECT sectorID,sectorName,is_core FROM cscan_sector WHERE sectorSearchActive=1 AND parentID=0";
		$rs = $DRW->query($sql,$DRW_read);
		while($row = $DRW->fetch_array($rs)) {
			if($row[2]){
				$coreArray[] = $row[0];
				if(in_array($row[0],$sIDs)){
					$core_id = 'C';
					$is_assign_queue = 1;
				}
			}
			else{
				$noncoreArray[] = $row[0];
			}
		}
		if($core_id=='N'){
			$where_core = ' AND ((scsc_sectorID=0 AND (DMSource LIKE \'%non%\' OR DMSource LIKE \'%\\_NC\\_%\' OR DMSource LIKE \'%telecom%\' OR DMSource LIKE \'%\\_TC\\_%\' OR DMSource LIKE \'%\\_TL\\_%\'))';
			foreach($noncoreArray as $id){
				if($id!=9){
					$where_core .= ' OR scsc_sectorID='.$id;
				}
			}
			$where_core .= ')';
		}
		elseif($core_id=='C'){
			$where_core = ' AND ((scsc_sectorID=0 AND DMSource NOT LIKE \'%non%\' AND DMSource NOT LIKE \'%\\_NC\\_%\' AND DMSource NOT LIKE \'%telecom%\' AND DMSource NOT LIKE \'%\\_TC\\_%\' AND DMSource NOT LIKE \'%\\_TL\\_%\')';
			foreach($coreArray as $id){
				$where_core .= ' OR scsc_sectorID='.$id;
			}
			$where_core .= ')';
		}
		
		$sql2 = "SELECT SQL_NO_CACHE userID,COUNT(assigned_admin_userID) AS products FROM 
			cscan_admin_users LEFT JOIN 
			(SELECT DISTINCT pd.productID,assigned_admin_userID FROM cscan_product_detail pd JOIN cscan_scsc_product as scsc ON (pd.productID=scsc.productID) WHERE actual_addedToDatabase>=CONCAT(CURDATE(),' 00:00:00') AND actual_addedToDatabase<=CONCAT(CURDATE(),' 23:59:59') AND assigned_admin_userID<>0 AND productStatus=2$where_core) AS cpd 
			ON(userID=assigned_admin_userID) 
			WHERE is_assign_queue=$is_assign_queue AND user_status=1 GROUP BY userID order by products,RAND() LIMIT 1";
		
		$rs2 = $DRW->query($sql2,$DRW_read);
		$row2 = $DRW->fetch_row($rs2);
		$assigned_admin_userID = (int)$row2[0];
	}
	return $assigned_admin_userID;
}

function saveExtraData($fields,$key,$keyval,$table,$where){
	global $DRW,$DRW_read,$DRW_main,$DRW_crm;
	$values = '';
	$inserts = '';
	foreach($fields as $f){
		if($f!='' && trim($GLOBALS[$f])!==''){
			$values .= ','.$f;
			$inserts .= ",'".$DRW->real_escape_string($GLOBALS[$f])."'";
		}
	}
	if($values!=''){
		$sqlU = "REPLACE INTO $table ($key$values) VALUES ($keyval$inserts)";
		$DRW->query($sqlU,$DRW_main);
	}
	else{
		$sqlU = "DELETE FROM $table WHERE $where";
		$DRW->query($sqlU,$DRW_main);
	}
}
?>
