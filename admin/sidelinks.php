<?php
require_once("../auth_auth.php");
require_once('../includes/functions.php');

if(isset($_GET['s_id'])){
	$s_id = (int)$_GET['s_id'];
	if(checkSector($s_id)){
		$_SESSION['manageproducts_sector'] = $s_id;
	}
	else{
		$_SESSION['manageproducts_sector'] = 0;
	}
	ob_end_clean();
	header("Location: {$_SERVER['PHP_SELF']}");
	exit;
}
elseif(!isset($_SESSION['manageproducts_sector'])){
	$_SESSION['manageproducts_sector'] = 0;
}

$adminArray = array();

$adminArray['Direct Mail Content'] = array();
$adminArray['Electronic Content'] = array(
	array(25,'Temp Products','manage_tmp_product.php'),
	array(25,'0')
);
$adminArray['Banner Content'] = array(
	array(41,'Banner Products Approved', 'manageproduct.php?pstat=1&amp;cstat=7'),
	array(41,'Banner Products Problem', 'manageproduct.php?pstat=4&amp;cstat=7'),
	array(41,'Banner Products Reprocessed', 'manageproduct.php?pstat=3&amp;cstat=7'),
	array(41,'Banner Products Unapproved', 'manageproduct.php?pstat=2&amp;cstat=7'),
	array(41,'Banner/Mobile Capture', 'ad_observations.php'),
	array(41,'Banner/Mobile Sites','manageSites.php'),
	array(41,'Firefox Extension', 'ffoxExtensionHelp.php'),
);
$adminArray['Mobile Content'] = array(
	array(41,'Mobile Products Approved', 'manageproduct.php?pstat=1&amp;cstat=12'),
	array(41,'Mobile Products Problem', 'manageproduct.php?pstat=4&amp;cstat=12'),
	array(41,'Mobile Products Reprocessed', 'manageproduct.php?pstat=3&amp;cstat=12'),
	array(41,'Mobile Products Unapproved', 'manageproduct.php?pstat=2&amp;cstat=12'),
	array(41,'0'),
);


/* Added by pradeep for Digital Module */ 

    /* Added by pradeep for Digital Module */ 

    $checkA = "SELECT COUNT(DISTINCT pd.productID) as numrows FROM cscan_product_detail pd JOIN cscan_scsc_product as scsc ON (pd.productID=scsc.productID) WHERE productStatus=1 AND (scsc_sectorID=0 OR scsc_sectorID=4 OR scsc_sectorID=5 OR scsc_sectorID=6 OR scsc_sectorID=9 OR scsc_sectorID=87 OR scsc_sectorID=90 OR scsc_sectorID=219 OR scsc_sectorID=266 OR scsc_sectorID=315 OR scsc_sectorID=372) AND pd.mChannelID=5 And pd.is_digital='1'";

    $checkP = "SELECT COUNT(DISTINCT pd.productID) as numrows FROM cscan_product_detail pd JOIN cscan_scsc_product as scsc ON (pd.productID=scsc.productID) WHERE productStatus=4 AND (scsc_sectorID=0 OR scsc_sectorID=4 OR scsc_sectorID=5 OR scsc_sectorID=6 OR scsc_sectorID=9 OR scsc_sectorID=87 OR scsc_sectorID=90 OR scsc_sectorID=219 OR scsc_sectorID=266 OR scsc_sectorID=315 OR scsc_sectorID=372) AND pd.mChannelID=5 And pd.is_digital='1'";

    $checkR = "SELECT COUNT(DISTINCT pd.productID) as numrows FROM cscan_product_detail pd JOIN cscan_scsc_product as scsc ON (pd.productID=scsc.productID) WHERE productStatus=3 AND (scsc_sectorID=0 OR scsc_sectorID=4 OR scsc_sectorID=5 OR scsc_sectorID=6 OR scsc_sectorID=9 OR scsc_sectorID=87 OR scsc_sectorID=90 OR scsc_sectorID=219 OR scsc_sectorID=266 OR scsc_sectorID=315 OR scsc_sectorID=372) AND pd.mChannelID=5 And pd.is_digital='1'";
    
     

    $checkU = "SELECT COUNT(DISTINCT pd.productID) as numrows FROM cscan_product_detail pd JOIN cscan_scsc_product as scsc ON (pd.productID=scsc.productID) WHERE productStatus=2 AND (scsc_sectorID=0 OR scsc_sectorID=4 OR scsc_sectorID=5 OR scsc_sectorID=6 OR scsc_sectorID=9 OR scsc_sectorID=87 OR scsc_sectorID=90 OR scsc_sectorID=219 OR scsc_sectorID=266 OR scsc_sectorID=315 OR scsc_sectorID=372) AND pd.mChannelID=5 And pd.is_digital='1'";

    $checkA = $DRW->query($checkA,$DRW_read);
    $dataA = $DRW->fetch_row($checkA);
    $odan = $dataA[0];

    $checkP = $DRW->query($checkP,$DRW_read);
    $dataP = $DRW->fetch_row($checkP);
    $odpn = $dataP[0];

    $checkR = $DRW->query($checkR,$DRW_read);
    $dataR = $DRW->fetch_row($checkR);
    $odrn = $dataR[0];

    $checkU = $DRW->query($checkU,$DRW_read);
    $dataU = $DRW->fetch_row($checkU);
    $odun = $dataU[0];
    
     

$adminArray['Online Display Content'] = array(
	array(49,'Online Display Products Approved ('.number_format($odan).')', 'manageproduct-digital.php?pstat=1&amp;cstat=14'),
	array(49,'Online Display Products Problem ('.number_format($odpn).')', 'manageproduct-digital.php?pstat=4&amp;cstat=14'),
	array(49,'Online Display Products Reprocessed ('.number_format($odrn).')', 'manageproduct-digital.php?pstat=3&amp;cstat=14'),
	array(49,'Online Display Products Unapproved ('.number_format($odun).')', 'manageproduct-digital.php?pstat=2&amp;cstat=14'),
	array(49,'Online Display Capture', 'ad_online_capture.php'),
        array(49,'Online Display Filter', 'ad_online_display.php'),
);

   
    //$checkAs = "SELECT COUNT(DISTINCT pd.productID) as numrows FROM cscan_product_detail pd JOIN cscan_scsc_product as scsc ON (pd.productID=scsc.productID) WHERE productStatus=1 AND (scsc_sectorID=0 OR scsc_sectorID=4 OR scsc_sectorID=5 OR scsc_sectorID=6 OR scsc_sectorID=9 OR scsc_sectorID=87 OR scsc_sectorID=90 OR scsc_sectorID=219 OR scsc_sectorID=266 OR scsc_sectorID=315 OR scsc_sectorID=372) AND pd.mChannelID=9 AND pd.is_digital='1'";
    $checkAs="SELECT count(*) FROM ( SELECT distinct pd.productID FROM cscan_product_detail pd JOIN cscan_scsc_product as scsc ON (pd.productID=scsc.productID) WHERE productStatus=1 AND (scsc_sectorID=0 OR scsc_sectorID=4 OR scsc_sectorID=5 OR scsc_sectorID=6 OR scsc_sectorID=9 OR scsc_sectorID=87 OR scsc_sectorID=90 OR scsc_sectorID=219 OR scsc_sectorID=262 OR scsc_sectorID=266 OR scsc_sectorID=315 OR scsc_sectorID=372) AND pd.mChannelID=9 And pd.is_digital='1') A, cscan_product_detail pd WHERE A.productID = pd.productID";

    $checkP = "SELECT COUNT(DISTINCT pd.productID) as numrows FROM cscan_product_detail pd JOIN cscan_scsc_product as scsc ON (pd.productID=scsc.productID) WHERE productStatus=4 AND (scsc_sectorID=0 OR scsc_sectorID=4 OR scsc_sectorID=5 OR scsc_sectorID=6 OR scsc_sectorID=9 OR scsc_sectorID=87 OR scsc_sectorID=90 OR scsc_sectorID=219 OR scsc_sectorID=266 OR scsc_sectorID=315 OR scsc_sectorID=372) AND pd.mChannelID=9 And pd.is_digital='1'";

    $checkR = "SELECT COUNT(DISTINCT pd.productID) as numrows FROM cscan_product_detail pd JOIN cscan_scsc_product as scsc ON (pd.productID=scsc.productID) WHERE productStatus=3 AND (scsc_sectorID=0 OR scsc_sectorID=4 OR scsc_sectorID=5 OR scsc_sectorID=6 OR scsc_sectorID=9 OR scsc_sectorID=87 OR scsc_sectorID=90 OR scsc_sectorID=219 OR scsc_sectorID=266 OR scsc_sectorID=315 OR scsc_sectorID=372) AND pd.mChannelID=9 And pd.is_digital='1'";

    $checkU = "SELECT COUNT(DISTINCT pd.productID) as numrows FROM cscan_product_detail pd JOIN cscan_scsc_product as scsc ON (pd.productID=scsc.productID) WHERE productStatus=2 AND (scsc_sectorID=0 OR scsc_sectorID=4 OR scsc_sectorID=5 OR scsc_sectorID=6 OR scsc_sectorID=9 OR scsc_sectorID=87 OR scsc_sectorID=90 OR scsc_sectorID=219 OR scsc_sectorID=266 OR scsc_sectorID=315 OR scsc_sectorID=372) AND pd.mChannelID=9 And pd.is_digital='1'";
//echo $checkAs;
    $checkAs = $DRW->query($checkAs,$DRW_read);
    $dataAs = $DRW->fetch_row($checkAs);
    //print_r($dataAs);
    $seman = $dataAs[0];

    $checkP = $DRW->query($checkP,$DRW_read);
    $dataP = $DRW->fetch_row($checkP);
    $sempn = $dataP[0];

    $checkR = $DRW->query($checkR,$DRW_read);
    $dataR = $DRW->fetch_row($checkR);
    $semrn = $dataR[0];

    $checkU = $DRW->query($checkU,$DRW_read);
    $dataU = $DRW->fetch_row($checkU);
    $semun = $dataU[0];       

$adminArray['Search Engine Marketing Content'] = array(
	array(50,'Search Engine Marketing Products Approved ('.number_format($seman).')', 'manageproduct-digital.php?pstat=1&amp;cstat=15'),
	array(50,'Search Engine Marketing Products Problem ('.number_format($sempn).')', 'manageproduct-digital.php?pstat=4&amp;cstat=15'),
	array(50,'Search Engine Marketing Products Reprocessed ('.number_format($semrn).')', 'manageproduct-digital.php?pstat=3&amp;cstat=15'),
	array(50,'Search Engine Marketing Products Unapproved ('.number_format($semun).')', 'manageproduct-digital.php?pstat=2&amp;cstat=15'),
	array(50,'Search Engine Marketing Capture', 'ad_sem_capture.php'),
        array(50,'Search Engine Marketing Filter', 'ad_sem_display.php'),
);



$checkAv = "SELECT COUNT(DISTINCT pd.productID) as numrows FROM cscan_product_detail pd JOIN cscan_scsc_product as scsc ON (pd.productID=scsc.productID) WHERE productStatus=1 AND (scsc_sectorID=0 OR scsc_sectorID=4 OR scsc_sectorID=5 OR scsc_sectorID=6 OR scsc_sectorID=9 OR scsc_sectorID=87 OR scsc_sectorID=90 OR scsc_sectorID=219 OR scsc_sectorID=266 OR scsc_sectorID=315 OR scsc_sectorID=372) AND pd.mChannelID=10 And pd.is_digital='1'";

$checkPv = "SELECT COUNT(DISTINCT pd.productID) as numrows FROM cscan_product_detail pd JOIN cscan_scsc_product as scsc ON (pd.productID=scsc.productID) WHERE productStatus=4 AND (scsc_sectorID=0 OR scsc_sectorID=4 OR scsc_sectorID=5 OR scsc_sectorID=6 OR scsc_sectorID=9 OR scsc_sectorID=87 OR scsc_sectorID=90 OR scsc_sectorID=219 OR scsc_sectorID=266 OR scsc_sectorID=315 OR scsc_sectorID=372) AND pd.mChannelID=10 And pd.is_digital='1'";


$checkRv = "SELECT COUNT(DISTINCT pd.productID) as numrows FROM cscan_product_detail pd JOIN cscan_scsc_product as scsc ON (pd.productID=scsc.productID) WHERE productStatus=3 AND (scsc_sectorID=0 OR scsc_sectorID=4 OR scsc_sectorID=5 OR scsc_sectorID=6 OR scsc_sectorID=9 OR scsc_sectorID=87 OR scsc_sectorID=90 OR scsc_sectorID=219 OR scsc_sectorID=266 OR scsc_sectorID=315 OR scsc_sectorID=372) AND pd.mChannelID=10 And pd.is_digital='1'";
 

$checkUv = "SELECT COUNT(DISTINCT pd.productID) as numrows FROM cscan_product_detail pd JOIN cscan_scsc_product as scsc ON (pd.productID=scsc.productID) WHERE productStatus=2 AND (scsc_sectorID=0 OR scsc_sectorID=4 OR scsc_sectorID=5 OR scsc_sectorID=6 OR scsc_sectorID=9 OR scsc_sectorID=87 OR scsc_sectorID=90 OR scsc_sectorID=219 OR scsc_sectorID=266 OR scsc_sectorID=315 OR scsc_sectorID=372) AND pd.mChannelID=10 And pd.is_digital='1'"; 

    $checkAv = $DRW->query($checkAv,$DRW_read);
    $dataAv = $DRW->fetch_row($checkAv);
    $semanv = $dataAv[0];

    $checkPvv = $DRW->query($checkPv,$DRW_read);
    $dataPv = $DRW->fetch_row($checkPvv);
    $sempnv = $dataPv[0];

    $checkRvv = $DRW->query($checkRv,$DRW_read);
    $dataRv = $DRW->fetch_row($checkRvv);
    $semrnv = $dataRv[0];

    $checkUvv = $DRW->query($checkUv,$DRW_read);
    $dataUv = $DRW->fetch_row($checkUvv);
    $semunv = $dataUv[0];       

$adminArray['Online Video Content'] = array(
	array(51,'Online Video Products Approved ('.number_format($semanv).')', 'manageproduct-digital.php?pstat=1&amp;cstat=16'),
	array(51,'Online Video Products Problem ('.number_format($sempnv).')', 'manageproduct-digital.php?pstat=4&amp;cstat=16'),
	array(51,'Online Video Products Reprocessed ('.number_format($semrnv).')', 'manageproduct-digital.php?pstat=3&amp;cstat=16'),
	array(51,'Online Video Products Unapproved ('.number_format($semunv).')', 'manageproduct-digital.php?pstat=2&amp;cstat=16'),
	array(51,'Online Video Capture', 'ad_video_capture.php'),
        array(51,'Online Video Filter', 'ad_video_display.php'),
);



/* End Added by pradeep for Digital Module */ 




if(checkGroup(37)){
	$checkV = "SELECT COUNT(*) FROM cscan_product_detail WHERE productStatus=2 AND consumer_insights=1";
	$checkV = $DRW->query($checkV,$DRW_read);
	$dataV = $DRW->fetch_row($checkV);
	$aun = $dataV[0];
	$checkV = "SELECT COUNT(*) FROM cscan_product_detail WHERE (productStatus>0 AND productStatus<>1 AND productStatus<>2 AND productStatus<>3 AND productStatus<>4) AND consumer_insights=1";
	$checkV = $DRW->query($checkV,$DRW_read);
	$dataV = $DRW->fetch_row($checkV);
	$anew = $dataV[0];
	
	$adminArray['Consumer Insight'] = array(
		array(33,'CI Unapproved ('.number_format($aun).')','manageproduct.php?pstat=2&amp;cstat=10'),
		array(33,'CI FTP ('.number_format($anew).')','manageproduct.php?pstat=6&amp;cstat=10'),
		array(33,'0')
	);
	
	/*$checkV = "SELECT COUNT(*) FROM cscan_product_detail WHERE productStatus=2 AND is_affinion=1";
	$checkV = $DRW->query($checkV,$DRW_read);
	$dataV = $DRW->fetch_row($checkV);
	$aun = $dataV[0];
	$checkV = "SELECT COUNT(*) FROM cscan_product_detail WHERE (productStatus<>1 AND productStatus<>2 AND productStatus<>3 AND productStatus<>4) AND is_affinion=1";
	$checkV = $DRW->query($checkV,$DRW_read);
	$dataV = $DRW->fetch_row($checkV);
	$anew = $dataV[0];
	
	$adminArray['Affinion Content'] = array(
		array(37,'Affinion Unapproved ('.number_format($aun).')','manageproduct.php?pstat=2&amp;cstat=8'),
		array(37,'Affinion FTP ('.number_format($anew).')','manageproduct.php?pstat=6&amp;cstat=8'),
		array(37,'0')
	);*/
	
	$checkV = "SELECT COUNT(*) FROM cscan_product_detail WHERE productStatus=1 AND is_citi=1";
	$checkV = $DRW->query($checkV,$DRW_read);
	$dataV = $DRW->fetch_row($checkV);
	$app = $dataV[0];
	$checkV = "SELECT COUNT(*) FROM cscan_product_detail WHERE productStatus=2 AND is_citi=1";
	$checkV = $DRW->query($checkV,$DRW_read);
	$dataV = $DRW->fetch_row($checkV);
	$aun = $dataV[0];
	$checkV = "SELECT COUNT(*) FROM cscan_product_detail WHERE productStatus=4 AND is_citi=1";
	$checkV = $DRW->query($checkV,$DRW_read);
	$dataV = $DRW->fetch_row($checkV);
	$apr = $dataV[0];
	$checkV = "SELECT COUNT(*) FROM cscan_product_detail WHERE productStatus=3 AND is_citi=1";
	$checkV = $DRW->query($checkV,$DRW_read);
	$dataV = $DRW->fetch_row($checkV);
	$are = $dataV[0];
	$checkV = "SELECT COUNT(*) FROM cscan_product_detail WHERE productStatus=-1 AND is_citi=1";
	$checkV = $DRW->query($checkV,$DRW_read);
	$dataV = $DRW->fetch_row($checkV);
	$unu = $dataV[0];
	$checkV = "SELECT COUNT(*) FROM cscan_product_detail WHERE (productStatus>0 AND productStatus<>1 AND productStatus<>2 AND productStatus<>3 AND productStatus<>4) AND is_citi=1";
	$checkV = $DRW->query($checkV,$DRW_read);
	$dataV = $DRW->fetch_row($checkV);
	$anew = $dataV[0];
	
	$adminArray['CITI Content'] = array(
		array(37,'CITI Approved ('.number_format($app).')','manageproduct.php?pstat=1&amp;cstat=11'),
		array(37,'CITI Unapproved ('.number_format($aun).')','manageproduct.php?pstat=2&amp;cstat=11'),
		array(37,'CITI Problem ('.number_format($apr).')','manageproduct.php?pstat=4&amp;cstat=11'),
		array(37,'CITI Reprocessed ('.number_format($are).')','manageproduct.php?pstat=3&amp;cstat=11'),
		array(37,'CITI Unused ('.number_format($unu).')','manageproduct.php?pstat=-1&amp;cstat=11'),
		array(37,'CITI FTP ('.number_format($anew).')','manageproduct.php?pstat=6&amp;cstat=11'),
		array(37,'0')
	);
}

/* Added by Pradeep */
 $checkV = "SELECT COUNT(*) FROM cscan_product_detail WHERE productStatus=10";
    $checkV = $DRW->query($checkV,$DRW_read);
    $dataV = $DRW->fetch_row($checkV);
    $junk = $dataV[0];
    $adminArray['JUNK'] = array(
	array(48,'JUNK ('.number_format($junk).')','manageproduct.php?pstat=10&amp;cstat=13')       
);

/* End Added by Pradeep */


$adminArray['Reporting'] = array(
	array(39,'FTP','ftpReport.php'),
	array(39,'QA','qaReport.php'),
	array(39,'Record Processing','productPerformance.php'),
	array(5,'Temp Products','tmp_product_report.php'),
	array(39,'Variant Problems','manageVariant.php'),
        array(52,'Record Counts','recordcount.php')
);
$adminArray['Access'] = array(
	array(3,'Admin Profiles','manageAdminMember.php'),
	array(1,'Client Profiles','managemember.php'),
	array(0,'Change Password','changePassword.php')
);
$adminArray['Client Reports'] = array(
	array(16,'Email Alerts','manageEmailAlerts.php'),
	array(17,'Email Alert Search','manageProfiles.php'),
	array(19,'IP Usage','manageUserAccounts.php'),
	array(18,'Usage','manageUserTracker.php')
);
$adminArray['Content Uploader'] = array(
	array(30,'Alerts','manageCAlert.php'),
	array(8,'Articles','manageArticle.php'),
	array(33,'Consumer Insights','manageInsight.php'),
	array(33,'Consumer Surveys','manageSurveys.php'),
	array(43,'Consumer Panelists','managePanelists.php'),
	array(40,'Client Reports','manageReports.php'),
	array(22,'Global Reports','manageTrends.php'),
	array(44,'Dashboard','manageDashboard.php'),
	array(22,'Downloads','manageDownloads.php'),
	array(32,'Mail Volume Projection','mail_volume.php')
);
$adminArray['Search Management'] = array(
	array(35,'Affinity/Associations','manageCategory.php?type=2'),
	array(10,'Audiences','manageMailingPnl.php'),
	array(6,'Companies','manageCategory.php'),
	array(11,'Mailing Types','manageMailingType.php'),
	array(9,'Media Channels','manageMedia.php'),
	array(36,'Publications','manageCategory.php?type=1'),
	array(7,'Sector/Cat/Subs','managesector.php'),
	array(14,'States/Provinces','manageState.php')
);
$adminArray['File Upload'] = array(
	array(46,'Manage Files','manageFileupload.php'),
        array(47,'Old Downloads','manageoldDownloads.php')
);
$adminArray['Mail'] = array(
	array(3,'Suggestions Mail','managesuggestionmail.php'),
	array(3,'Retrieval Services Mail','manageretrievalmail.php')
	
);
?>
<table border="0" cellspacing="0" cellpadding="5" width="100%">
<tr><td colspan="2" align="center" class="adminhead">Administrative Section</td></tr>
<?php 
foreach($adminArray as $title=>$vals){
	$print = '';
	$select = '';
	if($title=='Direct Mail Content' && count($AUTH_DATA['SID'])>0){
		$coreArray = array();
		$noncoreArray = array();
		$sql = "SELECT sectorID,sectorName,is_core FROM cscan_sector WHERE sectorSearchActive=1 AND parentID=0 ORDER BY sectorName";
		$rs = $DRW->query($sql,$DRW_read);
		while($row = $DRW->fetch_array($rs)) {
			if($row[2]){
				$coreArray[] = $row[0];
			}
			else{
				$noncoreArray[] = $row[0];
			}
			/*if(checkSector($row[0])){
				if(!$row[2] && !checkGroup(37)){
					continue;
				}
				$select .= "<option value=\"$row[0]\"";
				if($_SESSION['manageproducts_sector']==$row[0]){
					$select .= " selected=\"selected\"";
				}
				$select .= ">".htmlspecialchars($row[1])."</option>";
			}*/
		}
		
		$sect_j = '';
		$where_j = '';
		
		$partsArray = array();
		$partsArray[] = "scsc_sectorID=0";
		foreach($AUTH_DATA['SID'] as $sid){
			$partsArray[] = "scsc_sectorID=$sid";
		}
		/*$seccatsubArray = get_seccatsub(implode(',',$AUTH_DATA['SID']),implode(',',$AUTH_DATA['CID']),implode(',',$AUTH_DATA['SCID']));
		$partsArray[] = '(scsc_sectorID=0 AND scsc_categoryID=0 AND scsc_subCategoryID=0)';
		foreach($seccatsubArray as $sid=>$cArray){
			$part1 = "scsc_sectorID=$sid";
			$partsArray[] = '('.$part1.' AND scsc_categoryID=0 AND scsc_subCategoryID=0)';
			foreach($cArray as $cid=>$scArray){
				$part2 = "scsc_categoryID=$cid";
				$partsArray[] = '('.$part1.' AND '.$part2.' AND scsc_subCategoryID=0)';
				foreach($scArray as $scid=>$a){
					$part3 = "scsc_subCategoryID=$scid";
					$partsArray[] = '('.$part1.' AND '.$part2.' AND '.$part3.')';
				}
			}
		}*/
		if(count($partsArray)>0){
			$where_j .= ' AND ('.implode(' OR ',$partsArray).')';
		}
		
		$sect_j = ' JOIN cscan_scsc_product as scsc ON (pd.productID=scsc.productID)';
		
		$ctypeArray = array();
		if(checkGroup(37)){
			$ctypeArray[315] = 'Energy';
			$ctypeArray[266] = 'Retail';
			$ctypeArray[219] = 'Travel &amp; Leisure';
			$ctypeArray[9] = 'Telecom';
			$ctypeArray[6] = 'NonCore';
		}
		$ctypeArray[5] = 'Core';
		
		foreach($ctypeArray as $ckey=>$core){
			$where = '';
			if(!empty($_SESSION['manageproducts_sector'])){
				$where .= ' AND (scsc_sectorID='.$_SESSION['manageproducts_sector'].')';
			}
			if($ckey==315){
				$where .= ' AND ((scsc_sectorID=0 AND (DMSource LIKE \'%\\_EN\\_%\')) OR scsc_sectorID=315)';
			}
			elseif($ckey==266){
				$where .= ' AND ((scsc_sectorID=0 AND (DMSource LIKE \'%\\_RL\\_%\')) OR scsc_sectorID=266)';
			}
			elseif($ckey==219){
				$where .= ' AND ((scsc_sectorID=0 AND (DMSource LIKE \'%\\_TL\\_%\')) OR scsc_sectorID=219)';
			}
			elseif($ckey==9){
				$where .= ' AND ((scsc_sectorID=0 AND (DMSource LIKE \'%telecom%\' OR DMSource LIKE \'%\\_TC\\_%\')) OR scsc_sectorID=9)';
			}
			elseif($ckey==6){
				$where .= ' AND ((scsc_sectorID=0 AND (DMSource LIKE \'%non%\' OR DMSource LIKE \'%\\_NC\\_%\'))';
				foreach($noncoreArray as $id){
					if($id!=9 && $id!=219 && $id!=266 && $id!=315){
						$where .= ' OR scsc_sectorID='.$id;
					}
				}
				$where .= ')';
			}
			elseif($ckey==5){
				$where .= ' AND ((scsc_sectorID=0 AND DMSource NOT LIKE \'%non%\' AND DMSource NOT LIKE \'%\\_NC\\_%\' AND DMSource NOT LIKE \'%telecom%\' AND DMSource NOT LIKE \'%\\_TC\\_%\' AND DMSource NOT LIKE \'%\\_TL\\_%\')';
				foreach($coreArray as $id){
					$where .= ' OR scsc_sectorID='.$id;
				}
				$where .= ')';
			}
			$checkV = "SELECT COUNT(DISTINCT pd.productID) FROM cscan_product_detail pd$sect_j WHERE productStatus=4 AND mChannelID<>5 AND is_citi<>1$where";
			$checkV = $DRW->query($checkV,$DRW_read);
			$dataV = $DRW->fetch_row($checkV);
			$prob = $dataV[0];
			$checkV = "SELECT COUNT(DISTINCT pd.productID) FROM cscan_product_detail pd$sect_j WHERE productStatus=3 AND mChannelID<>5 AND is_citi<>1$where";
			$checkV = $DRW->query($checkV,$DRW_read);
			$dataV = $DRW->fetch_row($checkV);
			$rep = $dataV[0];
			$checkV = "SELECT COUNT(DISTINCT pd.productID) FROM cscan_product_detail pd$sect_j WHERE productStatus=2 AND mChannelID<>5 AND consumer_insights<>1 AND is_citi<>1 AND is_subp<>1$where";
			$checkV = $DRW->query($checkV,$DRW_read);
			$dataV = $DRW->fetch_row($checkV);
			$un = $dataV[0];
			$checkV = "SELECT COUNT(*) FROM cscan_product_detail WHERE productStatus=$ckey AND mChannelID<>5 AND consumer_insights<>1 AND is_citi<>1";
			$checkV = $DRW->query($checkV,$DRW_read);
			$dataV = $DRW->fetch_row($checkV);
			$new = $dataV[0];
			
			array_unshift($vals,array(4,'0'));
			array_unshift($vals,array(4,$core.' FTP ('.number_format($new).')','manageproduct.php?pstat='.$ckey.'&amp;cstat=0'));
			if($ckey==266 || $ckey==315){
				$checkV = "SELECT COUNT(DISTINCT pd.productID) FROM cscan_product_detail pd$sect_j WHERE productStatus=-1 AND mChannelID<>5 AND is_citi<>1$where";
				$checkV = $DRW->query($checkV,$DRW_read);
				$dataV = $DRW->fetch_row($checkV);
				$unu = $dataV[0];
				array_unshift($vals,array(4,$core.' Unused ('.number_format($unu).')','manageproduct.php?pstat=-1&amp;cstat='.$ckey));
			}
			if($ckey==9 || $ckey==6 || $ckey==5){
				$checkV = "SELECT COUNT(DISTINCT pd.productID) FROM cscan_product_detail pd$sect_j WHERE productStatus=2 AND mChannelID<>5 AND is_subp=1$where";
				$checkV = $DRW->query($checkV,$DRW_read);
				$dataV = $DRW->fetch_row($checkV);
				$uns = $dataV[0];
				array_unshift($vals,array(4,$core.' Non-Panelist Unapproved ('.number_format($uns).')','manageproduct.php?pstat=-2&amp;cstat='.$ckey));
			}
			array_unshift($vals,array(4,$core.' Unapproved ('.number_format($un).')','manageproduct.php?pstat=2&amp;cstat='.$ckey));
			array_unshift($vals,array(4,$core.' Reprocessed ('.number_format($rep).')','manageproduct.php?pstat=3&amp;cstat='.$ckey));
			array_unshift($vals,array(29,$core.' Problem ('.number_format($prob).')','manageproduct.php?pstat=4&amp;cstat='.$ckey));
			array_unshift($vals,array(4,$core.' Approved','manageproduct.php?pstat=1&amp;cstat='.$ckey));
		}
	}
	
	foreach($vals as $key=>$val){
		if (empty($val[0]) || checkGroup($val[0])){
			if($val[1]=='0'){
				$print .= "<tr><td colspan=\"2\">&nbsp;</td></tr>";
			}
			elseif($val[1]=='-1'){
				$print .= "<tr><td colspan=\"2\"><hr /></td></tr>";
			}
			else{
				$print .= "<tr onmouseover=\"this.className='bg_selected'\" onmouseout=\"this.className=''\">
					<td width=\"5%\" valign=\"top\"><img src=\"../images/bullet.jpg\" border=\"0\" style=\"padding-top:4px;\" /></td>
					<td valign=\"top\"><a class=\"topMenuLink\" href=\"$val[2]\" ><strong>$val[1]</strong></a></td></tr>";
			}
		}  
//		 else{
//				$print .= "<tr onmouseover=\"this.className='bg_selected'\" onmouseout=\"this.className=''\">
//					<td width=\"5%\" valign=\"top\"><img src=\"../images/bullet.jpg\" border=\"0\" style=\"padding-top:4px;\" /></td>
//					<td valign=\"top\"><a class=\"topMenuLink\" onclick=\"return removeNewTab(this)\" data-href=\"$val[2]\" ><strong>$val[1]</strong></a></td></tr>";
//			}
	}
	if($print!='') {
		if($title!='Direct Mail Content' && $title!='Electronic Content' && $title!='Banner Content' && $title!='Affinion Content' && $title!='CITI Content' && $title!='Mobile Content'){
			echo "<tr><td colspan=\"2\"><hr /></td></tr>";
		}
		echo "<tr><td colspan=\"2\" class=\"bodytext\"><strong>$title</strong></td></tr>";
		/*if($select!=''){
			echo "<tr><td colspan=\"2\" class=\"bodytext\"><form method=\"get\" name=\"sectorForm\" onsubmit=\"return false;\" action=\"{$_SERVER['PHP_SELF']}\"><select class=\"input_box\" name=\"s_id\" size=\"1\" onchange=\"changeSID();\"><option value=\"0\">All</option>$select</select></form></td></tr>";
		}*/
		echo $print;
	}
}
?>
<tr><td colspan="2"><hr /></td></tr>
<tr onmouseover="this.className='bg_selected'" onmouseout="this.className=''"><td width="5%"><img src="../images/bullet.jpg" border="0" /></td><td><a class="topMenuLink" href="logout.php"><strong>Logout</strong></a></td></tr>
</table>
<script type="text/javascript">
<!--
function changeSID(){
	document.sectorForm.submit();
}
//-->
</script>
