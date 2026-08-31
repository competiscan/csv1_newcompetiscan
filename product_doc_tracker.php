<?php
/**
 * <description>
 *
 * @name tracker.php
 * @verision <version>
 * @package <package>
 * @author HS
 * @since 00/00/0000
 * @license 
 */
function track_user() {
	global $DRW,$DRW_read,$DRW_main,$DRW_crm;
	if(isset($_SESSION['trackerID'])) $trackerID = $_SESSION['trackerID'];
	else $trackerID = '';
	if($trackerID) {
		$sql = "UPDATE cscan_user_tracker SET logoutTime = ADDTIME(curtime(),'00:01:00') WHERE ID = $trackerID";
		$DRW->query($sql,$DRW_main);
	}
}
function track_product($userID, $productID) {
	global $DRW,$DRW_read,$DRW_main,$DRW_crm;
	$sql = "REPLACE INTO cscan_product_track (userID,productID,time_accessed,IPAddress) VALUES('$userID', '$productID', NOW(),'".$_SERVER['REMOTE_ADDR']."')";
	$DRW->query($sql,$DRW_main);
}
function track_document($productID, $documentID, $orig=0) {
	global $DRW,$DRW_read,$DRW_main,$DRW_crm;
	
	$userID = 0;
	$admin_userID = 0;
	if(!empty($_SESSION)){
		if(!empty($_SESSION['sess_userID'])){
			$userID = $_SESSION['sess_userID'];
		}
		foreach($_SESSION as $k=>$v){
			if(preg_match('/^_auth_/',$k) && !empty($_SESSION[$k]['data']['userID'])){
				$admin_userID = $_SESSION[$k]['data']['userID'];
				break;
			}
		}
	}
	$repeater = false;
	if(!empty($_SESSION['last_track_document'])){
		list($lastproductID,$lasttime) = $_SESSION['last_track_document'];
		if($productID==$lastproductID && (time()-$lasttime)<5){
			if(!isset($_SERVER['HTTP_RANGE']) || preg_match('/\\b0\\b/',$_SERVER['HTTP_RANGE'])){
				$sql = "REPLACE INTO cscan_document_track_log (userID,productID,documentID,time_accessed,IPAddress,admin_userID,orig,log_info) VALUES('$userID', '$productID', '$documentID', NOW(),'".$_SERVER['REMOTE_ADDR']."','$admin_userID','$orig','".$DRW->real_escape_string(print_r($_SERVER,true))."')";
				//$DRW->query($sql,$DRW_main);
			}
			$repeater = true;
		}
	}
	$_SESSION['last_track_document'] = array($productID,time());
	
	if(!$repeater && (!isset($_SERVER['HTTP_RANGE']) || preg_match('/\\b0\\b/',$_SERVER['HTTP_RANGE']))) {
		$sql = "REPLACE INTO cscan_document_track (userID,productID,documentID,time_accessed,IPAddress,admin_userID,orig) VALUES('$userID', '$productID', '$documentID', NOW(),'".$_SERVER['REMOTE_ADDR']."','$admin_userID','$orig')";
		$DRW->query($sql,$DRW_main);
	}
}
function track_search($userID,$ID) {
	global $DRW,$DRW_read,$DRW_main,$DRW_crm;
	
	require_once('includes/search_copy.php');//$copyArray
	
	$sql = "REPLACE INTO cscan_search_track (userID,queryDate,IPAddress,".implode(',',$copyArray).") (SELECT '$userID',NOW(),'".$_SERVER['REMOTE_ADDR']."',".implode(',',$copyArray)." FROM cscan_search WHERE ID=$ID)";
	$DRW->query($sql,$DRW_main);
}
function track_trend($trend_id) {
	global $DRW,$DRW_read,$DRW_main,$DRW_crm;
	
	$userID = 0;
	$admin_userID = 0;
	if(!empty($_SESSION)){
		if(!empty($_SESSION['sess_userID'])){
			$userID = $_SESSION['sess_userID'];
		}
		foreach($_SESSION as $k=>$v){
			if(preg_match('/^_auth_/',$k) && !empty($_SESSION[$k]['data']['userID'])){
				$admin_userID = $_SESSION[$k]['data']['userID'];
				break;
			}
		}
	}
	$repeater = false;
	if(!empty($_SESSION['last_track_trend'])){
		list($lasttrend_id,$lasttime) = $_SESSION['last_track_trend'];
		if($trend_id==$lasttrend_id && (time()-$lasttime)<5){
			$repeater = true;
		}
	}
	$_SESSION['last_track_trend'] = array($trend_id,time());
	
	if(!$repeater && (!isset($_SERVER['HTTP_RANGE']) || preg_match('/\\b0\\b/',$_SERVER['HTTP_RANGE']))) {
		$sql = "REPLACE INTO cscan_trend_track (userID,admin_userID,trend_id,time_accessed,IPAddress) VALUES('$userID','$admin_userID','$trend_id', NOW(),'".$_SERVER['REMOTE_ADDR']."')";
		$DRW->query($sql,$DRW_main);
	}
}

############## track search activity of each user ##########################
function track_search_activity($userID,$ID) {
	global $DRW,$DRW_read,$DRW_main,$DRW_crm;	
	$copyArray = array(
            'searchKey','searchType','searchOption','mChannelID','sectorID','mPanelID','addedToDatabase','month1','month2','sort','company','productName','incentive','categoryID','mTypeID','subCategoryID','cardStatus','personalization','gender','age','state','worksiteVoluntary','agentCommunicationID','groupSize','offerOrigin','enhance','searchview','compaignLanguage','affinityAssociation','income_mult','fa_id_mult','tl_id_mult','siteCatID_mult','pubTypeID_mult','approved_date','electronicID_mult','DMA_ID_mult','businessContent_mult','delmethid_mult','affinity_association','prescription','AffinityCategoryID_mult','search_panelist_date','is_affinion','is_military','search_competi_id','ApplicationType_mult','is_multicultural','search_rules','IntroPricing_mult','is_rewards','RewardsProgramEmphasis_mult','is_incentive','responseMechID_mult','multiculturalmarkets_mult','CardNetwork_mult','FeeProduct','external_link','FeeProductType','approved_date_to','ca_related','searchKey2','search_type_and','is_mover','scsc_primary','OptOutFirmOffer','riders_mult','is_hphsa','subSubCategoryID','Income_Producing_Assets_Segment_Code_mult','cg_id','is_citi','is_CreditCardMentioned','is_prescreen','spanelist_filter','edc_id_mult','AffinitySubCategoryID_mult','ERateType_mult','EOfferPrice_mult','ETermLength_mult','is_ECancelFee','IssueTypeID_mult','pcountry','is_Reloadable','creditUnion',
            'is_mobile','value_score','refinance','jumbo_ncnfg','va','fha','conventional','usda','correspondent_lending','faux_check','minmaxmortgage','socialmedia_adtype','publication_name','deliveryTypeId','postageId','presortedId','packageTypeId','fico_score','credit_vision_score','vantage_score','sender_domain_name',
        );
	
	$sql = "insert INTO cscan_search_activity (ID,userID,queryDate,IPAddress,".implode(',',$copyArray).") (SELECT '$ID', '$userID',NOW(),'".$_SERVER['REMOTE_ADDR']."',".implode(',',$copyArray)." FROM cscan_search WHERE ID=$ID order by ID desc limit 1)";
	$DRW->query($sql,$DRW_main);
}
############## end track search activity of each user ##########################

?>