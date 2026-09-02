<?php
require_once("../auth_auth.php");
require_once('../includes/functions.php');

if(!defined('ENV')){
    define('ENV',getenv('SERVER_NAME'));
}




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


$adminArray['Online Display Content'] = array(
	array(49,'Online Display Products Approved', 'manageproduct-digital.php?pstat=1&amp;cstat=14'),
	array(49,'Online Display Products Problem', 'manageproduct-digital.php?pstat=4&amp;cstat=14'),
	array(49,'Online Display Products Reprocessed', 'manageproduct-digital.php?pstat=3&amp;cstat=14'),
	array(49,'Online Display Products Unapproved', 'manageproduct-digital.php?pstat=2&amp;cstat=14'),
	array(49,'Online Display Capture', 'ad_online_capture.php'),
        array(49,'Online Display Filter', 'ad_online_display.php'),
);

   
   
$adminArray['Search Engine Marketing Content'] = array(
	array(50,'Search Engine Marketing Products Approved', 'manageproduct-digital.php?pstat=1&amp;cstat=15'),
	array(50,'Search Engine Marketing Products Problem', 'manageproduct-digital.php?pstat=4&amp;cstat=15'),
	array(50,'Search Engine Marketing Products Reprocessed', 'manageproduct-digital.php?pstat=3&amp;cstat=15'),
	array(50,'Search Engine Marketing Products Unapproved', 'manageproduct-digital.php?pstat=2&amp;cstat=15'),
	array(50,'Search Engine Marketing Capture', 'ad_sem_capture.php'),
        array(50,'Search Engine Marketing Filter', 'ad_sem_display.php'),
);


$adminArray['Online Video Content'] = array(
	array(51,'Online Video Products Approved', 'manageproduct-digital.php?pstat=1&amp;cstat=16'),
	array(51,'Online Video Products Problem', 'manageproduct-digital.php?pstat=4&amp;cstat=16'),
	array(51,'Online Video Products Reprocessed', 'manageproduct-digital.php?pstat=3&amp;cstat=16'),
	array(51,'Online Video Products Unapproved', 'manageproduct-digital.php?pstat=2&amp;cstat=16'),
	array(51,'Online Video Capture', 'ad_video_capture.php'),
        array(51,'Online Video Filter', 'ad_video_display.php'),
);
if(ENV == 'localhost' || ENV == 'demo.competiscan.com'){
    $adminArray['Digital Content'] = array(
            array(103,'Digital Temp Products','manage_tmp_digital_product.php'),
            array(103,'0')
    );
}

/* End Added by pradeep for Digital Module */ 




if(checkGroup(37)){
//	$checkV = "SELECT COUNT(*) FROM cscan_product_detail WHERE productStatus=2 AND consumer_insights=1";
//	$checkV = $DRW->query($checkV,$DRW_read);
//	$dataV = $DRW->fetch_row($checkV);
//	$aun = $dataV[0];
//	$checkV = "SELECT COUNT(*) FROM cscan_product_detail WHERE (productStatus>0 AND productStatus<>1 AND productStatus<>2 AND productStatus<>3 AND productStatus<>4) AND consumer_insights=1";
//	$checkV = $DRW->query($checkV,$DRW_read);
//	$dataV = $DRW->fetch_row($checkV);
//	$anew = $dataV[0];
	
	$adminArray['Consumer Insight'] = array(
		array(33,'CI Unapproved','manageproduct.php?pstat=2&amp;cstat=10'),
		array(33,'CI FTP','manageproduct.php?pstat=6&amp;cstat=10'),
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
	
//	$checkV = "SELECT COUNT(*) FROM cscan_product_detail WHERE productStatus=1 AND is_citi=1";
//	$checkV = $DRW->query($checkV,$DRW_read);
//	$dataV = $DRW->fetch_row($checkV);
//	$app = $dataV[0];
//	$checkV = "SELECT COUNT(*) FROM cscan_product_detail WHERE productStatus=2 AND is_citi=1";
//	$checkV = $DRW->query($checkV,$DRW_read);
//	$dataV = $DRW->fetch_row($checkV);
//	$aun = $dataV[0];
//	$checkV = "SELECT COUNT(*) FROM cscan_product_detail WHERE productStatus=4 AND is_citi=1";
//	$checkV = $DRW->query($checkV,$DRW_read);
//	$dataV = $DRW->fetch_row($checkV);
//	$apr = $dataV[0];
//	$checkV = "SELECT COUNT(*) FROM cscan_product_detail WHERE productStatus=3 AND is_citi=1";
//	$checkV = $DRW->query($checkV,$DRW_read);
//	$dataV = $DRW->fetch_row($checkV);
//	$are = $dataV[0];
//	$checkV = "SELECT COUNT(*) FROM cscan_product_detail WHERE productStatus=-1 AND is_citi=1";
//	$checkV = $DRW->query($checkV,$DRW_read);
//	$dataV = $DRW->fetch_row($checkV);
//	$unu = $dataV[0];
//	$checkV = "SELECT COUNT(*) FROM cscan_product_detail WHERE (productStatus>0 AND productStatus<>1 AND productStatus<>2 AND productStatus<>3 AND productStatus<>4) AND is_citi=1";
//	$checkV = $DRW->query($checkV,$DRW_read);
//	$dataV = $DRW->fetch_row($checkV);
//	$anew = $dataV[0];
//	
	$adminArray['CITI Content'] = array(
		array(37,'CITI Approved','manageproduct.php?pstat=1&amp;cstat=11'),
		array(37,'CITI Unapproved','manageproduct.php?pstat=2&amp;cstat=11'),
		array(37,'CITI Problem','manageproduct.php?pstat=4&amp;cstat=11'),
		array(37,'CITI Reprocessed','manageproduct.php?pstat=3&amp;cstat=11'),
		array(37,'CITI Unused','manageproduct.php?pstat=-1&amp;cstat=11'),
		array(37,'CITI FTP','manageproduct.php?pstat=6&amp;cstat=11'),
		array(37,'0')
	);
}

/* Added by Pradeep */
// $checkV = "SELECT COUNT(*) FROM cscan_product_detail WHERE productStatus=10";
//    $checkV = $DRW->query($checkV,$DRW_read);
//    $dataV = $DRW->fetch_row($checkV);
//    $junk = $dataV[0];
    $adminArray['GLACIER'] = array(
	array(48,'Glacier','manageproduct.php?pstat=10&amp;cstat=13')       
);

/* End Added by Pradeep */
if(ENV == 'localhost' || ENV == 'demo.competiscan.com'){
$adminArray['Reporting'] = array(
	array(39,'FTP','ftpReport.php'),
	array(39,'QA','qaReport.php'),
	array(39,'Record Processing','productPerformance.php'),
	array(5,'Temp Products','tmp_product_report.php'),
	array(39,'Variant Problems','manageVariant.php'),
	array(52,'Record Counts','recordcount.php'),
	array(82,'Consumer Score','consumer_scoring_report.php'),
	array(104,'Producer Score','producer_scoring_monthly_reports.php'),
        array(106,'Mortgage Broker Score','mortgage_broker_scoring_monthly_reports.php'),
        array(107,'Provider Score','provider_scoring_monthly_reports.php'),
	array(105,'Digital Records','digital_records.php')
        
);
}else{
$adminArray['Reporting'] = array(
        array(39,'FTP','ftpReport.php'),
	array(39,'QA','qaReport.php'),
	array(39,'Record Processing','productPerformance.php'),
	array(5,'Temp Products','tmp_product_report.php'),
	array(39,'Variant Problems','manageVariant.php'),
	array(52,'Record Counts','recordcount.php'),
	array(82,'Consumer Score','consumer_scoring_report.php'),
	array(104,'Producer Score','producer_scoring_monthly_reports.php'),
        array(106,'Mortgage Broker Score','mortgage_broker_scoring_monthly_reports.php'),
        array(107,'Provider Score','provider_scoring_monthly_reports.php'),
	array(105,'Digital Records','digital_records.php') 
    );
}

$adminArray['Access'] = array(
	array(3,'Admin Profiles','manageAdminMember.php'),
	array(1,'Client Profiles','managemember.php'),
	array(0,'Change Password','changePassword.php')
);
$adminArray['Client Reports'] = array(
	array(16,'Email Alerts','manageEmailAlerts.php'),
	array(17,'Email Alert Search','manageProfiles.php'),
	array(19,'IP Usage','manageUserAccounts.php'),
	array(18,'Usage','manageUserTracker.php'),
        array(53,'E-mail Track Report','email_track_report.php'),
        array(54,'Digital Panelist Participation Report','panelist_participation_records.php'),
        array(55,'Panelist Direct Mail Report','panelist_dmp_report.php'),
        array(56,'DA&apos;s Log','dalogs.php'),
        array(57,'DA&apos;s Duplicates','da_duplicates.php'),
        array(58,'User Search Activity','user_search_activity.php'),
        array(86,'Daily Status Reports','recordcount_daily.php')
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
	array(32,'Mail Volume Projection','mail_volume.php'),
        array(96,'Panelist Score','consumer_panelist_scoring_range.php')
);
$adminArray['Search Management'] = array(
	array(35,'Affinity/Associations','manageCategory.php?type=2'),
	array(10,'Audiences','manageMailingPnl.php'),
	array(6,'Companies','manageCategory.php'),
	array(11,'Mailing Types','manageMailingType.php'),
	array(9,'Media Channels','manageMedia.php'),
	array(36,'Publications','manageCategory.php?type=1'),
	array(7,'Sector/Cat/Subs','managesector.php'),
	array(14,'States/Provinces','manageState.php'),
	array(83,'Communication Type','communication-type.php'),
        array(84,'Manage Presentation','manage-presentation.php'),
        array(85,'Manage Imap Client Email','manage-imap-csv.php')
       
        
);
$adminArray['File Upload'] = array(
	array(46,'Manage Files','manageFileupload.php'),
        array(47,'Old Downloads','manageoldDownloads.php')
);
$adminArray['Mail'] = array(
	array(3,'Suggestions Mail','managesuggestionmail.php'),
	array(3,'Retrieval Services Mail','manageretrievalmail.php')
	
);
//if(ENV == 'localhost' || ENV == 'demo.competiscan.com'){
    $adminArray['Video tool'] = array(
        array(91,'Manage YouTube Urls','manageYoutubeUrls.php'),
        array(93,'Manage YouTube Projects','manageYoutubeProjects.php'),
	array(89,'Manage Search Logos','manageSearchLogos.php'),
	array(87,'Manage Search Keywords','manageKeywords.php')        
    );
//}
$adminArray['SBKC Inquiry'] = array(
        array(97,'Producer/Advisor Mail','manageProducerEmail.php'),
        array(98,'Mortgage Broker Mail','manageBrokerEmail.php'),
	array(99,'Contact Us Mail','manageContactEmail.php')	       
    );    
?>
<table border="0" cellspacing="0" cellpadding="5" width="100%">
<tr><td colspan="2" align="center" class="adminhead">Administrative Section</td></tr>
<?php 
foreach($adminArray as $title=>$vals){
	$print = '';
	$select = '';
	if ($title == 'Direct Mail Content' && is_array($AUTH_DATA['SID']) && count($AUTH_DATA['SID']) > 0) {
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
                    /* Start Emerging Section*/
                        //if(ENV == 'localhost' || ENV == 'demo.competiscan.com'){
                        $ctypeArray[522] = 'Emerging';
                        //} 
                    /* END Emerging Section*/
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
                        /* Start Emerging Section*/
                       // if(ENV == 'localhost' || ENV == 'demo.competiscan.com'){
                        elseif($ckey==522){
                                $where .= ' AND (scsc_sectorID=0 OR scsc_sectorID=522)';
                        }
                        //}
                        /* End Emerging Section*/
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
					/* Start Emerging Section 522*/
                                        //if(ENV == 'localhost' || ENV == 'demo.competiscan.com'){
                                            if($id!=9 && $id!=219 && $id!=266 && $id!=315 && $id != 522 && $id != 525 && $id != 372){
                                                    $where .= ' OR scsc_sectorID='.$id;
                                            }
                                        /*}else{
                                          if($id!=9 && $id!=219 && $id!=266 && $id!=315){
                                                    $where .= ' OR scsc_sectorID='.$id;
                                            }  
                                        }*/
				}
				$where .= ')';
			}
			elseif($ckey==5){
				$where .= ' AND ((scsc_sectorID=0 AND DMSource NOT LIKE \'%non%\' AND DMSource NOT LIKE \'%\\_NC\\_%\' AND DMSource NOT LIKE \'%telecom%\' AND DMSource NOT LIKE \'%\\_TC\\_%\' AND DMSource NOT LIKE \'%\\_TL\\_%\')';
				foreach($coreArray as $id){
					/* Start Emerging Section 522*/
                                        //if(ENV == 'localhost' || ENV == 'demo.competiscan.com'){
                                            if($id!=530){
                                                $where .= ' OR scsc_sectorID='.$id;
                                            }
                                        /*}else{
                                            $where .= ' OR scsc_sectorID='.$id;
                                        }*/
				}
				$where .= ')';
			}
//			$checkV = "SELECT COUNT(DISTINCT pd.productID) FROM cscan_product_detail pd$sect_j WHERE productStatus=4 AND mChannelID<>5 AND is_citi<>1$where";
//			$checkV = $DRW->query($checkV,$DRW_read);
//			$dataV = $DRW->fetch_row($checkV);
//			$prob = $dataV[0];
//			$checkV = "SELECT COUNT(DISTINCT pd.productID) FROM cscan_product_detail pd$sect_j WHERE productStatus=3 AND mChannelID<>5 AND is_citi<>1$where";
//			$checkV = $DRW->query($checkV,$DRW_read);
//			$dataV = $DRW->fetch_row($checkV);
//			$rep = $dataV[0];
//			$checkV = "SELECT COUNT(DISTINCT pd.productID) FROM cscan_product_detail pd$sect_j WHERE productStatus=2 AND mChannelID<>5 AND consumer_insights<>1 AND is_citi<>1 AND is_subp<>1$where";
//			$checkV = $DRW->query($checkV,$DRW_read);
//			$dataV = $DRW->fetch_row($checkV);
//			$un = $dataV[0];
//			$checkV = "SELECT COUNT(*) FROM cscan_product_detail WHERE productStatus=$ckey AND mChannelID<>5 AND consumer_insights<>1 AND is_citi<>1";
//			$checkV = $DRW->query($checkV,$DRW_read);
//			$dataV = $DRW->fetch_row($checkV);
//			$new = $dataV[0];
			
			array_unshift($vals,array(4,'0'));
			array_unshift($vals,array(4,$core.' FTP','manageproduct.php?pstat='.$ckey.'&amp;cstat=0'));
			if($ckey==266 || $ckey==315){
//				$checkV = "SELECT COUNT(DISTINCT pd.productID) FROM cscan_product_detail pd$sect_j WHERE productStatus=-1 AND mChannelID<>5 AND is_citi<>1$where";
//				$checkV = $DRW->query($checkV,$DRW_read);
//				$dataV = $DRW->fetch_row($checkV);
//				$unu = $dataV[0];
				array_unshift($vals,array(4,$core.' Unused','manageproduct.php?pstat=-1&amp;cstat='.$ckey));
			}
			if($ckey==9 || $ckey==6 || $ckey==5){
//				$checkV = "SELECT COUNT(DISTINCT pd.productID) FROM cscan_product_detail pd$sect_j WHERE productStatus=2 AND mChannelID<>5 AND is_subp=1$where";
//				$checkV = $DRW->query($checkV,$DRW_read);
//				$dataV = $DRW->fetch_row($checkV);
//				$uns = $dataV[0];
				array_unshift($vals,array(4,$core.' Non-Panelist Unapproved','manageproduct.php?pstat=-2&amp;cstat='.$ckey));
			}
			array_unshift($vals,array(4,$core.' Unapproved','manageproduct.php?pstat=2&amp;cstat='.$ckey));
			array_unshift($vals,array(4,$core.' Reprocessed','manageproduct.php?pstat=3&amp;cstat='.$ckey));
			array_unshift($vals,array(29,$core.' Problem','manageproduct.php?pstat=4&amp;cstat='.$ckey));
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
