<?php 
require_once('includes/globalSession.php');
require_once('includes/checklogin.php');
require_once 'product_doc_tracker.php';
echo "<pre>";
print_r($_REQUEST);
echo "<pre>"; 
/*######## Start for Page permission ########*/ 
if(!defined('ENV')){
    define('ENV',getenv('SERVER_NAME'));
   }
  function callAPI($method, $url, $data){
    //print_r($data);die;
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION,true);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    curl_setopt($curl, CURLINFO_HEADER_OUT, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, [
        'User-Agent: Mozilla/5.0'
    ]);
    $result = curl_exec($curl);
   // Debug request headers
    $info = curl_getinfo($curl, CURLINFO_HEADER_OUT);
    //echo "<pre>Request Headers:\n" . $info . "</pre>";
    curl_close($curl);
    return $result;
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
            //echo $redirect_page; die;
            header("Location: $redirect_page");
            die;
        }
        
    // }    
    /*######## End for Page permission ########*/
//exit;

$_SESSION['selected_productID'] = array();
$currentpage_url='http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

if(isset($_REQUEST['ssid']) && $_REQUEST['ssid']!='') $search_id = (int)$_REQUEST['ssid'];
else $search_id = 0;

if(isset($_REQUEST['searchview'])) $searchview = (int)$_REQUEST['searchview'];
else $searchview = 0;

if(isset($_REQUEST['key'])) $skey = trim($_REQUEST['key']);
else $skey = '';
if(isset($_REQUEST['key2'])) $skey2 = trim($_REQUEST['key2']);
else $skey2 = '';
if(isset($_REQUEST['search_option'])) $search_option = $_REQUEST['search_option'];
else $search_option = 'like';
if(isset($_REQUEST['search_type'])) $search_type = $_REQUEST['search_type'];
else $search_type = 'ocr2';
if(isset($_REQUEST['search_type_and'])) $search_type_and = (int)$_REQUEST['search_type_and'];
else $search_type_and = 1;

if(isset($_REQUEST['mChannelID'])) $mChannelID = $_REQUEST['mChannelID'];
else $mChannelID = array();
if(isset($_REQUEST['sectorID'])) $sectorID = $_REQUEST['sectorID'];
else $sectorID = array();
if(isset($_REQUEST['mPanelID'])) $mPanelID = $_REQUEST['mPanelID'];
else $mPanelID = array();

if(isset($_REQUEST['addedToDatabase'])) $addedToDatabase = $_REQUEST['addedToDatabase'];
else $addedToDatabase = '';
if(isset($_REQUEST['month1'])) $month1 = $_REQUEST['month1'];
else $month1 = '';
if(isset($_REQUEST['month2'])) $month2 = $_REQUEST['month2'];
else $month2 = '';
if(isset($_REQUEST['sort'])) $sort = $_REQUEST['sort'];
else $sort = 'desc';
if(isset($_REQUEST['company'])) $company = trim($_REQUEST['company']);
else $company = '';
if(isset($_REQUEST['affinity_association'])) $affinity_association = trim($_REQUEST['affinity_association']);
else $affinity_association = '';

// Added for publication name
if(isset($_REQUEST['publication_name']) && $_REQUEST['is_publication_name']==1) $publication_name = trim($_REQUEST['publication_name']);
else $publication_name = '';

if(isset($_POST['enhance'])) $enhance = (int)$_POST['enhance'];
else $enhance = 0;

if(isset($_REQUEST['productName']) && in_array(90,$sectorID)) $productName = $_REQUEST['productName'];
else $productName = '';
if(isset($_REQUEST['incentive']) && $enhance==1) $incentive = $_REQUEST['incentive'];
else $incentive = '';
if(isset($_REQUEST['categoryID']) && $enhance==1) $categoryID = $_REQUEST['categoryID'];
else $categoryID = array();
if(isset($_REQUEST['mTypeID']) && $enhance==1 && (in_array(1,$mPanelID) || in_array(2,$mPanelID) || (in_array(4,$mPanelID) && ($_SESSION['sess_plevel']==1 || $_SESSION['sess_plevel']==2)))) $mTypeIDval = $_REQUEST['mTypeID'];
else $mTypeIDval = array();
if(isset($_REQUEST['subCategoryID']) && $enhance==1) $subCategoryID = $_REQUEST['subCategoryID'];
else $subCategoryID = array();
if(isset($_REQUEST['subSubCategoryID']) && $enhance==1) $subSubCategoryID = $_REQUEST['subSubCategoryID'];
else $subSubCategoryID = array();
if(isset($_POST['scsc_primary']) && $enhance==1) $scsc_primary = (int)$_POST['scsc_primary'];
else $scsc_primary = 0;
if(isset($_REQUEST['cardStatus']) && $enhance==1) $cardStatusval = $_REQUEST['cardStatus'];
else $cardStatusval = '';
if(isset($_REQUEST['personalization']) && $enhance==1 && ((in_array(2,$mPanelID)) || (in_array(1,$mPanelID)) || (in_array(4,$mPanelID)) || (in_array(5,$mPanelID))|| (in_array(6,$mPanelID)) || (in_array(9,$mPanelID)))) $personalizationval = $_REQUEST['personalization'];
else $personalizationval = '';//2
if(isset($_REQUEST['gender']) && $enhance==1 && (in_array(1,$mPanelID) || in_array(2,$mPanelID))) $genderval = $_REQUEST['gender'];
else $genderval = '';//M
if(isset($_REQUEST['age']) && $enhance==1 && (in_array(1,$mPanelID) || in_array(2,$mPanelID))) $ageval = $_REQUEST['age'];
else $ageval = array();
if(isset($_REQUEST['income']) && $enhance==1 && (in_array(1,$mPanelID) || in_array(2,$mPanelID))) $incomeval = $_REQUEST['income'];
else $incomeval = array();
if(isset($_REQUEST['state']) && $enhance==1) $state = $_REQUEST['state'];
else $state = array();
if(isset($_REQUEST['worksiteVoluntary']) && $enhance==1) $worksiteVoluntaryval = (int)$_REQUEST['worksiteVoluntary'];
else $worksiteVoluntaryval = 0;
if(isset($_REQUEST['affinityAssociation']) && $enhance==1) $affinityAssociationval = (int)$_REQUEST['affinityAssociation'];
else $affinityAssociationval = 0;
if(isset($_REQUEST['AffinityCategoryID']) && $affinityAssociationval==1) $AffinityCategoryIDval = $_REQUEST['AffinityCategoryID'];
else $AffinityCategoryIDval = array();
if(isset($_REQUEST['AffinitySubCategoryID']) && $affinityAssociationval==1) $AffinitySubCategoryIDval = $_REQUEST['AffinitySubCategoryID'];
else $AffinitySubCategoryIDval = array();
if(isset($_REQUEST['prescription']) && $enhance==1) $prescriptionval = (int)$_REQUEST['prescription'];
else $prescriptionval = 0;
if(isset($_REQUEST['is_affinion']) && $enhance==1) $is_affinion = (int)$_REQUEST['is_affinion'];
else $is_affinion = 0;
if(isset($_REQUEST['is_military']) && $enhance==1) $is_military = (int)$_REQUEST['is_military'];
else $is_military = 0;
if(isset($_REQUEST['is_mover']) && $enhance==1) $is_mover = (int)$_REQUEST['is_mover'];
else $is_mover = 0;
if(isset($_REQUEST['is_hphsa']) && $enhance==1) $is_hphsa = (int)$_REQUEST['is_hphsa'];
else $is_hphsa = 0;
if(isset($_REQUEST['is_citi']) && $enhance==1) $is_citi = (int)$_REQUEST['is_citi'];
else $is_citi = 0;
if(isset($_REQUEST['is_CreditCardMentioned']) && $enhance==1) $is_CreditCardMentioned = (int)$_REQUEST['is_CreditCardMentioned'];
else $is_CreditCardMentioned = 0;
if(isset($_REQUEST['OptOutFirmOffer']) && $enhance==1) $OptOutFirmOffer = (int)$_REQUEST['OptOutFirmOffer'];
else $OptOutFirmOffer = 0;
if(isset($_REQUEST['is_multicultural']) && $enhance==1) $is_multicultural = (int)$_REQUEST['is_multicultural'];
else $is_multicultural = 0;
if(isset($_REQUEST['multiculturalmarkets_mult']) && $enhance==1 && $is_multicultural) $multiculturalmarkets_mult = $_REQUEST['multiculturalmarkets_mult'];
else $multiculturalmarkets_mult = array();
if(isset($_REQUEST['FeeProduct']) && $enhance==1) $FeeProduct = (int)$_REQUEST['FeeProduct'];
else $FeeProduct = 0;

############# for the Faux Credit Checkbox ##############
if(isset($_REQUEST['faux_check'])) $faux_check = (int)$_REQUEST['faux_check'];
else $faux_check = 0;

############# end for the Faux Credit Checkbox ##############


if(isset($_REQUEST['FeeProductType']) && $enhance==1) $FeeProductType = $_REQUEST['FeeProductType'];
else $FeeProductType = array();
if(isset($_REQUEST['agentCommunicationID']) && $enhance==1) $agentCommunicationIDval = $_REQUEST['agentCommunicationID'];
else $agentCommunicationIDval = array();
if(isset($_REQUEST['groupSize']) && $enhance==1) $groupSizeval = $_REQUEST['groupSize'];
else $groupSizeval = array();
if(isset($_REQUEST['offerOrigin']) && $enhance==1) $offerOriginval = $_REQUEST['offerOrigin'];
else $offerOriginval = '';
if(isset($_REQUEST['compaignLanguage']) && $enhance==1) $compaignLanguageval = $_REQUEST['compaignLanguage'];
else $compaignLanguageval = '';
if(isset($_REQUEST['fa_id']) && $enhance==1 && (in_array(1,$mPanelID) || in_array(2,$mPanelID))) $fa_idval = $_REQUEST['fa_id'];
else $fa_idval = array();
if(isset($_REQUEST['tl_id']) && $enhance==1 && (in_array(1,$mPanelID) || in_array(2,$mPanelID))) $tl_idval = $_REQUEST['tl_id'];
else $tl_idval = array();
if(isset($_REQUEST['ApplicationType_mult']) && $enhance==1 && (in_array(90,$sectorID) || in_array(6,$sectorID))) $ApplicationType_mult = $_REQUEST['ApplicationType_mult'];
else $ApplicationType_mult = array();
if(isset($_REQUEST['siteCatID']) && $enhance==1 && in_array(5,$mChannelID)) $siteCatID = $_REQUEST['siteCatID'];
else $siteCatID = array();
if(isset($_REQUEST['pubTypeID']) && $enhance==1 && in_array(2,$mChannelID)) $pubTypeID = $_REQUEST['pubTypeID'];
else $pubTypeID = array();
if(isset($_REQUEST['Income_Producing_Assets_Segment_Code']) && $enhance==1 && (in_array(1,$mPanelID) || in_array(2,$mPanelID))) $Income_Producing_Assets_Segment_Code = $_REQUEST['Income_Producing_Assets_Segment_Code'];
else $Income_Producing_Assets_Segment_Code = array();
$approved_date = '0000-00-00 00:00:00';
$approved_date_to = '0000-00-00 00:00:00';
if(isset($_REQUEST['approved_date']) && ($_SESSION['sess_plevel']==1 || $_SESSION['sess_plevel']==2)) {
	$approved_ymd = trim($_REQUEST['approved_date']); 
	if(!empty($approved_ymd)){
		$approved_time = trim($_REQUEST['approved_time']);
		$approved_time_to = trim($_REQUEST['approved_time_to']);
		if(empty($approved_time)){
			$approved_time = '00:00:00';
		}
		$approved_date = $approved_ymd.' '.$approved_time;
		if(empty($approved_time_to)){
			$approved_time_to = '23:59:59';
		}
		$approved_date_to = $approved_ymd.' '.$approved_time_to;
	}
}
// added for approved date
if(isset($_REQUEST['approved_date_to']) && ($_SESSION['sess_plevel']==1 || $_SESSION['sess_plevel']==2)) {
    $approved_to_ymd = trim($_REQUEST['approved_date_to']);
    if(!empty($approved_to_ymd)){
        if(isset($_REQUEST['approved_time_to'])){
            $approved_time_to = trim($_REQUEST['approved_time_to']);
        }else{
            $approved_time_to='';
        }

        if(empty($approved_time_to)){
                $approved_time_to = '23:59:59';
        }
        $approved_date_to = $approved_to_ymd.' '.$approved_time_to;
    }
}
//echo $approved_date.' aproved date2: '.$approved_date_to;

if(isset($_REQUEST['search_competi_id'])) $search_competi_id = trim($_REQUEST['search_competi_id']);
else $search_competi_id = '';
if(isset($_REQUEST['search_panelist_date'])) $search_panelist_date = (int)$_REQUEST['search_panelist_date'];
else $search_panelist_date = 0;
if(isset($_REQUEST['electronicID']) && $enhance==1 && in_array(3,$mChannelID)) $electronicID = $_REQUEST['electronicID'];
else $electronicID = array();
if(isset($_REQUEST['DMA_ID']) && $enhance==1 && (in_array(1,$mPanelID) || in_array(2,$mPanelID))) $DMA_ID = $_REQUEST['DMA_ID'];
else $DMA_ID = array();
if(isset($_REQUEST['edc_id']) && $enhance==1 && in_array(315,$sectorID)) $edc_id = $_REQUEST['edc_id'];//(in_array(1,$mPanelID) || in_array(2,$mPanelID)) &&
else $edc_id = array();
if(isset($_REQUEST['businessContent']) && $enhance==1 && (in_array(1,$mPanelID) || in_array(2,$mPanelID))) $businessContent = $_REQUEST['businessContent'];
else $businessContent = array();
if(isset($_REQUEST['delmethid_mult']) && $enhance==1 && in_array(1,$mChannelID)) $delmethid_mult = array_filter($_REQUEST['delmethid_mult']);
else $delmethid_mult = array();

############################## Start Envelope/Postage Data Fields##################
if(isset($_REQUEST['deliveryTypeId']) && $enhance==1 && (in_array(1,$delmethid_mult) || in_array(3,$delmethid_mult) || in_array(7,$delmethid_mult))) $deliveryTypeId = array_filter($_REQUEST['deliveryTypeId']);
else $deliveryTypeId = array();
if(isset($_REQUEST['postageId']) && $enhance==1 && (in_array(1,$delmethid_mult) || in_array(3,$delmethid_mult) || in_array(7,$delmethid_mult))) $postageId = array_filter($_REQUEST['postageId']);
else $postageId = array();
if(isset($_REQUEST['presortedId']) && $enhance==1 && (in_array(1,$delmethid_mult) || in_array(3,$delmethid_mult) || in_array(7,$delmethid_mult))) $presortedId = array_filter($_REQUEST['presortedId']);
else $presortedId = array();
if(isset($_REQUEST['packageTypeId']) && $enhance==1 && (in_array(1,$delmethid_mult) || in_array(3,$delmethid_mult) || in_array(7,$delmethid_mult))) $packageTypeId = array_filter($_REQUEST['packageTypeId']);
else $packageTypeId = array();
############################## End Envelope/Postage Data Fields##################

if(isset($_REQUEST['IntroPricing_mult']) && $enhance==1 && in_array(90,$sectorID)) $IntroPricing_mult = $_REQUEST['IntroPricing_mult'];
else $IntroPricing_mult = array();
if(isset($_REQUEST['is_rewards']) && $enhance==1 && (in_array(90,$sectorID) || in_array(87,$sectorID))) $is_rewards = (int)$_REQUEST['is_rewards'];
else $is_rewards = 0;
if(isset($_REQUEST['RewardsProgramEmphasis_mult']) && $enhance==1 && $is_rewards) $RewardsProgramEmphasis_mult = $_REQUEST['RewardsProgramEmphasis_mult'];
else $RewardsProgramEmphasis_mult = array();
if(isset($_REQUEST['is_incentive']) && $enhance==1) $is_incentive = (int)$_REQUEST['is_incentive'];
else $is_incentive = 0;
if(isset($_REQUEST['responseMechID_mult']) && $enhance==1) $responseMechID_mult = $_REQUEST['responseMechID_mult'];
else $responseMechID_mult = array();
if(isset($_REQUEST['riders_mult']) && $enhance==1) $riders_mult = $_REQUEST['riders_mult'];
else $riders_mult = array();
if(isset($_REQUEST['CardNetwork_mult']) && $enhance==1 && (in_array(90,$sectorID) || in_array(87,$sectorID))) $CardNetwork_mult = $_REQUEST['CardNetwork_mult'];
else $CardNetwork_mult = array();
if(isset($_REQUEST['external_link']) && $enhance==1 && in_array(6,$mChannelID)) $external_link = trim($_REQUEST['external_link']);
else $external_link = '';

#################### for the social media sponsered #############################
if(isset($_REQUEST['socialmedia_adtype']) && $enhance==1 && in_array(6,$mChannelID)) $socialmedia_adtype = trim($_REQUEST['socialmedia_adtype']);
else $socialmedia_adtype = '';
#################### end for the social media sponsered #############################
if(isset($_REQUEST['ca_related'])) $ca_related = (int)$_REQUEST['ca_related'];
else $ca_related = 0;
if(isset($_REQUEST['cg_id'])) $cg_id = $_REQUEST['cg_id'];
else $cg_id = '';
if(isset($_REQUEST['pcountry'])) $pcountry = $_REQUEST['pcountry'];
else {
	if($_SESSION['sess_plevel']>0 && $_SESSION['sess_userID']!=7541){ //sarahp@competiscan.com
		$pcountry = '';
	}
	else{
		$pcountry = 'US';
	}
}

##################### for add country permission ###################
if($pcountry=='BOTH'){
    $pcountry='';
}
################### end for add country permission ###################

if(isset($_REQUEST['spanelist_filter']) && ($_SESSION['sess_plevel']==1 || $_SESSION['sess_plevel']==2)) $spanelist_filter = $_REQUEST['spanelist_filter'];
else $spanelist_filter = '';
if(isset($_REQUEST['ERateType_mult']) && $enhance==1 && in_array(315,$sectorID)) $ERateType_mult = $_REQUEST['ERateType_mult'];
else $ERateType_mult = array();
if(isset($_REQUEST['EOfferPrice_mult']) && $enhance==1 && in_array(315,$sectorID)) $EOfferPrice_mult = $_REQUEST['EOfferPrice_mult'];
else $EOfferPrice_mult = array();
if(isset($_REQUEST['ETermLength_mult']) && $enhance==1 && in_array(315,$sectorID)) $ETermLength_mult = $_REQUEST['ETermLength_mult'];
else $ETermLength_mult = array();
if(isset($_REQUEST['is_ECancelFee']) && $enhance==1 && in_array(315,$sectorID)) (int)$is_ECancelFee = $_REQUEST['is_ECancelFee'];
else $is_ECancelFee = 0;
if(isset($_REQUEST['IssueTypeID_mult']) && $enhance==1 && in_array(4,$sectorID)) $IssueTypeID_mult = $_REQUEST['IssueTypeID_mult'];
else $IssueTypeID_mult = array();
if(isset($_REQUEST['is_Reloadable']) && $enhance==1 && in_array(90,$sectorID)) (int)$is_Reloadable = $_REQUEST['is_Reloadable'];
else $is_Reloadable = 0;
if(isset($_REQUEST['creditUnion']) && $enhance==1) $creditUnion = (int)$_REQUEST['creditUnion'];
else $creditUnion = 0;

if(isset($_POST['saved'])) $saved = (int)$_POST['saved'];
else $saved = 0;

$searchName = '';
$search_rulesArray = array();
$search_rulesArrayf = array();

/* For digital media channel like online dispaly, online video and SEM */

if(isset($_REQUEST['ismobile'])) $ismobile = $_REQUEST['ismobile'];
else $ismobile = '0';
if(!in_array('5',$mChannelID) AND (!in_array('9',$mChannelID)) AND (!in_array('10',$mChannelID))){
$ismobile=0;
}
//Add due to remove digital source
$ismobile=0;

/* End For digital media channel like online dispaly, online video and SEM */

/* ####  For Value Score #### */

if(isset($_REQUEST['value_score']) && $enhance==1 && (in_array(1,$mPanelID) || in_array(2,$mPanelID))) $value_score = array_filter($_REQUEST['value_score']);
else $value_score = array();

/* ####  End for value score #### */
######## Start:  Mortgage & Loan - General Mortgage & Loan Details #########
$refinance = (!empty($_POST['refinance']))?trim($_POST['refinance']):0;
$jumbo_ncnfg = (!empty($_POST['jumbo_ncnfg']))?trim($_POST['jumbo_ncnfg']):0;
$va = (!empty($_POST['va']))?trim($_POST['va']):0;
$fha = (!empty($_POST['fha']))?trim($_POST['fha']):0;
$conventional = (!empty($_POST['conventional']))?trim($_POST['conventional']):0;
$usda = (!empty($_POST['usda']))?trim($_POST['usda']):0;
$correspondent_lending = (!empty($_POST['correspondent_lending']))?trim($_POST['correspondent_lending']):0;
$insertMortgageInto = $insertMortgageValues = $updateMortgage = $searchMortgageKey = '';
$searchMortgageKey = ',refinance,jumbo_ncnfg,va,fha,conventional,usda,correspondent_lending';
################ for minmax mortgage #####################

$minmax = (!empty($_POST['minmax']))?trim($_POST['minmax']):'0-2000000';
$minloanamount  =   0;
$maxloanamount  =   2000000;
if(!empty($minmax)){
    if($minmax!='0-2000000'|| !in_array(6, $sectorID)){
        $minmaxarray    =explode("-",$minmax);
        $minloanamount  =  $minmaxarray[0];
        $maxloanamount  =  $minmaxarray[1];
    }
    
}
if(!in_array(6, $sectorID)){
    $minmax     =   '0-2000000';
}
//echo $minmax;exit;

################ for minmax mortgage #####################
############## for faux checkbox & min max mortgage ############
$faux_checkInto =   ',faux_check';
$faux_checkValues=",$faux_check";
$minmaxmortgageinfo =   ',minmaxmortgage';
$updateminmaxmortgageval =   ",minmaxmortgage='".$minmax."' ";
$minmaxmortgageval =   ",'".$minmax."' ";
############## end for faux checkbox & min max mortgage ############

#################### for the social media sponsered #############################
$socialmedia_adtypeinfo=',socialmedia_adtype';

#################### for the social media sponsered #############################

$updateFauxvalues=",faux_check='".$faux_check."'";
if(in_array(6, $sectorID)){    
    $insertMortgageInto = ',refinance,jumbo_ncnfg,va,fha,conventional,usda,correspondent_lending';
    $insertMortgageValues = ",$refinance,$jumbo_ncnfg,$va,$fha,$conventional,$usda,$correspondent_lending";    
}else{
    $refinance = $jumbo_ncnfg = $va = $fha = $conventional = $usda = $correspondent_lending= 0;
}
$updateMortgage = ",refinance='".$refinance."',jumbo_ncnfg='".$jumbo_ncnfg."', va='".$va."',fha='".$fha."',conventional='".$conventional."',usda='".$usda."' ,correspondent_lending='".$correspondent_lending."'";
######## End:  Mortgage & Loan - General Mortgage & Loan Details #########

/* ####  For FICO, Vantage, CreditVision Score #### */

if(isset($_REQUEST['fico_score']) && $enhance==1 ) $fico_score = array_filter($_REQUEST['fico_score']);
else $fico_score = array();
if(isset($_REQUEST['credit_vision_score']) && $enhance==1 ) $credit_vision_score = array_filter($_REQUEST['credit_vision_score']);
else $credit_vision_score = array();
if(isset($_REQUEST['vantage_score']) && $enhance==1 ) $vantage_score = array_filter($_REQUEST['vantage_score']);
else $vantage_score = array();
if(isset($_REQUEST['domainName'])) $sender_domain_name = $_REQUEST['domainName'];
else $sender_domain_name = array();
//echo $sender_domain_name."##############################3";
/* ####  EndFor FICO, Vantage, CreditVision Score #### */

if(isset($_POST['send'])){
    /*$apiURL='https://dev02.competiscan.com:5406/search';
    $data_value='dts_val=credit card&sortby=added_to_database, desc&tiebreaker=product_id, asc';
    $resultApiData=CallAPI('POST',$apiURL,$data=$data_value);
    print_r($resultApiData);
    echo "<pre>";
    print_r($_POST);
	echo "</pre>";*/
	#exit;
	if(isset($_POST['new_search'])){
		$search_id = 0;
	}
	if($search_id==0){
		$last_search_sql = "SELECT ID FROM cscan_search WHERE userID='".$_SESSION['sess_userID']."' AND saved<>1" ; 			
		$rs = $DRW->query($last_search_sql,$DRW_read);
		$data = $DRW->fetch_row($rs);
		if(!empty($data[0])){
			$search_id = (int) $data[0];
		}
	}
	$sri = (int) $_POST['sri'];
	for($i=0;$i<$sri;$i++){
		if(isset($_POST['srao'.$i])){
			$srao = (int)$_POST['srao'.$i];
		}
		else{
			$srao = 0;
		}
		if(isset($_POST['srex'.$i])){
			$srex = (int)$_POST['srex'.$i];
		}
		else{
			$srex = 0;
		}
		if(isset($_POST['srno'.$i])){
			$srno = (int)$_POST['srno'.$i];
		}
		else{
			$srno = 0;
		}
		if($srao || $srex || $srno){
			$search_rulesArray[] = $_POST['srf'.$i].":$srao:$srex:$srno";
		}
	}
	
	if($search_id==0) { //,is_mobile
		$last_search_sql = "INSERT INTO cscan_search (userID,userType,searchKey,searchType,searchOption,mChannelID,sectorID,mPanelID,
		addedToDatabase, month1,month2,sort,company,productName,incentive,categoryID,mTypeID,subCategoryID,cardStatus,personalization,
		gender,age,income_mult,state,worksiteVoluntary,agentCommunicationID,groupSize,offerOrigin,enhance,searchview,compaignLanguage,affinityAssociation,
		fa_id_mult,tl_id_mult,siteCatID_mult,pubTypeID_mult,approved_date,electronicID_mult,DMA_ID_mult,businessContent_mult,delmethid_mult,deliveryTypeId,postageId,presortedId,packageTypeId,affinity_association,prescription,AffinityCategoryID_mult,search_panelist_date,is_affinion,is_military,search_competi_id,
		ApplicationType_mult,is_multicultural,search_rules,IntroPricing_mult,is_rewards,RewardsProgramEmphasis_mult,is_incentive,responseMechID_mult,multiculturalmarkets_mult,CardNetwork_mult,FeeProduct,external_link,socialmedia_adtype,FeeProductType,approved_date_to,
		ca_related,is_mover,scsc_primary,OptOutFirmOffer,searchKey2,search_type_and,riders_mult,is_hphsa,subSubCategoryID,Income_Producing_Assets_Segment_Code_mult,cg_id,is_citi,is_CreditCardMentioned,spanelist_filter,edc_id_mult,AffinitySubCategoryID_mult,
		ERateType_mult,EOfferPrice_mult,ETermLength_mult,is_ECancelFee,IssueTypeID_mult,pcountry,is_Reloadable,creditUnion,is_mobile,value_score,publication_name,fico_score,credit_vision_score,vantage_score $insertMortgageInto$faux_checkInto$minmaxmortgageinfo) 
		VALUES ('".$_SESSION['sess_userID']."','".$_SESSION['sess_userType']."',
		'".$DRW->real_escape_string($skey)."','".$DRW->real_escape_string($search_type)."','".$DRW->real_escape_string($search_option)."',
		'".$DRW->real_escape_string(implode(',',$mChannelID))."','".$DRW->real_escape_string(implode(',',$sectorID))."','".$DRW->real_escape_string(implode(',',$mPanelID))."',
		'".$DRW->real_escape_string($addedToDatabase)."','".$DRW->real_escape_string($month1)."','".$DRW->real_escape_string($month2)."','".$DRW->real_escape_string($sort)."',
		'".$DRW->real_escape_string($company)."','".$DRW->real_escape_string($productName)."','".$DRW->real_escape_string($incentive)."',
		'".$DRW->real_escape_string(implode(',',$categoryID))."','".$DRW->real_escape_string(implode(',',$mTypeIDval))."','".$DRW->real_escape_string(implode(',',$subCategoryID))."',
		'".$DRW->real_escape_string($cardStatusval)."',
		'".$DRW->real_escape_string($personalizationval)."','".$DRW->real_escape_string($genderval)."','".$DRW->real_escape_string(implode(',',$ageval))."','".$DRW->real_escape_string(implode(',',$incomeval))."',
		'".$DRW->real_escape_string(implode(',',$state))."',
		'".$worksiteVoluntaryval."','".$DRW->real_escape_string(implode(',',$agentCommunicationIDval))."',
		'".$DRW->real_escape_string(implode(',',$groupSizeval))."','".$DRW->real_escape_string($offerOriginval)."','$enhance','$searchview','".$DRW->real_escape_string($compaignLanguageval)."','".$affinityAssociationval."',
		'".$DRW->real_escape_string(implode(',',$fa_idval))."','".$DRW->real_escape_string(implode(',',$tl_idval))."',
		'".$DRW->real_escape_string(implode(',',$siteCatID))."','".$DRW->real_escape_string(implode(',',$pubTypeID))."','".$DRW->real_escape_string($approved_date)."',
		'".$DRW->real_escape_string(implode(',',$electronicID))."','".$DRW->real_escape_string(implode(',',$DMA_ID))."','".$DRW->real_escape_string(implode(',',$businessContent))."','".$DRW->real_escape_string(implode(',',$delmethid_mult))."','".$DRW->real_escape_string(implode(',',$deliveryTypeId))."','".$DRW->real_escape_string(implode(',',$postageId))."','".$DRW->real_escape_string(implode(',',$presortedId))."','".$DRW->real_escape_string(implode(',',$packageTypeId))."',
		'".$DRW->real_escape_string($affinity_association)."',$prescriptionval,'".$DRW->real_escape_string(implode(',',$AffinityCategoryIDval))."',$search_panelist_date,$is_affinion,$is_military,'".$DRW->real_escape_string($search_competi_id)."',
		'".$DRW->real_escape_string(implode(',',$ApplicationType_mult))."',$is_multicultural,'".$DRW->real_escape_string(implode(',',$search_rulesArray))."','".$DRW->real_escape_string(implode(',',$IntroPricing_mult))."',$is_rewards,'".$DRW->real_escape_string(implode(',',$RewardsProgramEmphasis_mult))."',$is_incentive,'".$DRW->real_escape_string(implode(',',$responseMechID_mult))."','".$DRW->real_escape_string(implode(',',$multiculturalmarkets_mult))."','".$DRW->real_escape_string(implode(',',$CardNetwork_mult))."',$FeeProduct,'".$DRW->real_escape_string($external_link)."','".$DRW->real_escape_string($socialmedia_adtype)."','".$DRW->real_escape_string(implode(',',$FeeProductType))."','".$DRW->real_escape_string($approved_date_to)."',
		$ca_related,$is_mover,$scsc_primary,$OptOutFirmOffer,'".$DRW->real_escape_string($skey2)."',$search_type_and,'".$DRW->real_escape_string(implode(',',$riders_mult))."',$is_hphsa,'".$DRW->real_escape_string(implode(',',$subSubCategoryID))."','".$DRW->real_escape_string(implode(',',$Income_Producing_Assets_Segment_Code))."','".$DRW->real_escape_string($cg_id)."',$is_citi,$is_CreditCardMentioned,'".$DRW->real_escape_string($spanelist_filter)."','".$DRW->real_escape_string(implode(',',$edc_id))."','".$DRW->real_escape_string(implode(',',$AffinitySubCategoryIDval))."',
		'".$DRW->real_escape_string(implode(',',$ERateType_mult))."','".$DRW->real_escape_string(implode(',',$EOfferPrice_mult))."','".$DRW->real_escape_string(implode(',',$ETermLength_mult))."',$is_ECancelFee,'".$DRW->real_escape_string(implode(',',$IssueTypeID_mult))."','".$DRW->real_escape_string($pcountry)."',$is_Reloadable,$creditUnion,$ismobile,'".$DRW->real_escape_string(implode(',',$value_score))."','".$DRW->real_escape_string($publication_name)."','".$DRW->real_escape_string(implode(',',$fico_score))."','".$DRW->real_escape_string(implode(',',$credit_vision_score))."','".$DRW->real_escape_string(implode(',',$vantage_score))."',sender_domain_name='".$DRW->real_escape_string(implode(',',$sender_domain_name))."' $insertMortgageValues$faux_checkValues$minmaxmortgageval)";
		//echo "EEEEEEEEEEEEEEEEEEEEEEEEEEEEE".$last_search_sql;exit;
                $DRW->query($last_search_sql,$DRW_main);
		$search_id = (float)$DRW->insert_id($DRW_main);
	}
	else {
		$last_search_sql = "UPDATE cscan_search SET userType='".$_SESSION['sess_userType']."',
		searchKey='".$DRW->real_escape_string($skey)."',searchType='".$DRW->real_escape_string($search_type)."',searchOption='".$DRW->real_escape_string($search_option)."',
		mChannelID='".$DRW->real_escape_string(implode(',',$mChannelID))."',sectorID='".$DRW->real_escape_string(implode(',',$sectorID))."',mPanelID='".$DRW->real_escape_string(implode(',',$mPanelID))."',
		addedToDatabase='".$DRW->real_escape_string($addedToDatabase)."', month1='".$DRW->real_escape_string($month1)."',month2='".$DRW->real_escape_string($month2)."',sort='".$DRW->real_escape_string($sort)."',
		company='".$DRW->real_escape_string($company)."',productName='".$DRW->real_escape_string($productName)."',incentive='".$DRW->real_escape_string($incentive)."',
		categoryID='".$DRW->real_escape_string(implode(',',$categoryID))."',mTypeID='".$DRW->real_escape_string(implode(',',$mTypeIDval))."',subCategoryID='".$DRW->real_escape_string(implode(',',$subCategoryID))."',
		cardStatus='".$DRW->real_escape_string($cardStatusval)."',
		personalization='".$DRW->real_escape_string($personalizationval)."',gender='".$DRW->real_escape_string($genderval)."',age='".$DRW->real_escape_string(implode(',',$ageval))."',income_mult='".$DRW->real_escape_string(implode(',',$incomeval))."',
		state='".$DRW->real_escape_string(implode(',',$state))."',
		worksiteVoluntary='".$worksiteVoluntaryval."',agentCommunicationID='".$DRW->real_escape_string(implode(',',$agentCommunicationIDval))."',
		groupSize='".$DRW->real_escape_string(implode(',',$groupSizeval))."',offerOrigin='".$DRW->real_escape_string($offerOriginval)."',enhance='$enhance',searchview='$searchview',compaignLanguage='".$DRW->real_escape_string($compaignLanguageval)."',affinityAssociation='".$affinityAssociationval."',
		fa_id_mult='".$DRW->real_escape_string(implode(',',$fa_idval))."',tl_id_mult='".$DRW->real_escape_string(implode(',',$tl_idval))."',
		siteCatID_mult='".$DRW->real_escape_string(implode(',',$siteCatID))."',pubTypeID_mult='".$DRW->real_escape_string(implode(',',$pubTypeID))."',approved_date='".$DRW->real_escape_string($approved_date)."',
		electronicID_mult='".$DRW->real_escape_string(implode(',',$electronicID))."',DMA_ID_mult='".$DRW->real_escape_string(implode(',',$DMA_ID))."',businessContent_mult='".$DRW->real_escape_string(implode(',',$businessContent))."',delmethid_mult='".$DRW->real_escape_string(implode(',',$delmethid_mult))."',deliveryTypeId='".$DRW->real_escape_string(implode(',',$deliveryTypeId))."',postageId='".$DRW->real_escape_string(implode(',',$postageId))."',presortedId='".$DRW->real_escape_string(implode(',',$presortedId))."',packageTypeId='".$DRW->real_escape_string(implode(',',$packageTypeId))."',
		affinity_association='".$DRW->real_escape_string($affinity_association)."',prescription=$prescriptionval,AffinityCategoryID_mult='".$DRW->real_escape_string(implode(',',$AffinityCategoryIDval))."',search_panelist_date=$search_panelist_date,is_affinion=$is_affinion,is_military=$is_military,search_competi_id='".$DRW->real_escape_string($search_competi_id)."',
		ApplicationType_mult='".$DRW->real_escape_string(implode(',',$ApplicationType_mult))."',is_multicultural=$is_multicultural,search_rules='".$DRW->real_escape_string(implode(',',$search_rulesArray))."',IntroPricing_mult='".$DRW->real_escape_string(implode(',',$IntroPricing_mult))."',is_rewards=$is_rewards,RewardsProgramEmphasis_mult='".$DRW->real_escape_string(implode(',',$RewardsProgramEmphasis_mult))."',is_incentive=$is_incentive,responseMechID_mult='".$DRW->real_escape_string(implode(',',$responseMechID_mult))."',
		multiculturalmarkets_mult='".$DRW->real_escape_string(implode(',',$multiculturalmarkets_mult))."',CardNetwork_mult='".$DRW->real_escape_string(implode(',',$CardNetwork_mult))."',FeeProduct=$FeeProduct,external_link='".$DRW->real_escape_string($external_link)."',socialmedia_adtype='".$DRW->real_escape_string($socialmedia_adtype)."',FeeProductType='".$DRW->real_escape_string(implode(',',$FeeProductType))."',approved_date_to='".$DRW->real_escape_string($approved_date_to)."',
		ca_related=$ca_related,is_mover=$is_mover,scsc_primary=$scsc_primary,OptOutFirmOffer=$OptOutFirmOffer,searchKey2='".$DRW->real_escape_string($skey2)."',search_type_and=$search_type_and,riders_mult='".$DRW->real_escape_string(implode(',',$riders_mult))."',is_hphsa=$is_hphsa,subSubCategoryID='".$DRW->real_escape_string(implode(',',$subSubCategoryID))."',Income_Producing_Assets_Segment_Code_mult='".$DRW->real_escape_string(implode(',',$Income_Producing_Assets_Segment_Code))."',cg_id='".$DRW->real_escape_string($cg_id)."',is_citi=$is_citi,is_CreditCardMentioned=$is_CreditCardMentioned,
		spanelist_filter='".$DRW->real_escape_string($spanelist_filter)."',edc_id_mult='".$DRW->real_escape_string(implode(',',$edc_id))."',AffinitySubCategoryID_mult='".$DRW->real_escape_string(implode(',',$AffinitySubCategoryIDval))."',ERateType_mult='".$DRW->real_escape_string(implode(',',$ERateType_mult))."',EOfferPrice_mult='".$DRW->real_escape_string(implode(',',$EOfferPrice_mult))."',ETermLength_mult='".$DRW->real_escape_string(implode(',',$ETermLength_mult))."',is_ECancelFee=$is_ECancelFee,
		IssueTypeID_mult='".$DRW->real_escape_string(implode(',',$IssueTypeID_mult))."',pcountry='".$DRW->real_escape_string($pcountry)."',is_Reloadable=$is_Reloadable,creditUnion=$creditUnion,is_mobile=$ismobile,value_score='".$DRW->real_escape_string(implode(',',$value_score))."',publication_name='".$DRW->real_escape_string($publication_name)."',fico_score='".$DRW->real_escape_string(implode(',',$fico_score))."',credit_vision_score='".$DRW->real_escape_string(implode(',',$credit_vision_score))."',vantage_score='".$DRW->real_escape_string(implode(',',$vantage_score))."',sender_domain_name='".$DRW->real_escape_string($sender_domain_name)."' ".$updateMortgage.$updateFauxvalues.$updateminmaxmortgageval.
		"WHERE ID='$search_id' AND userID='".$_SESSION['sess_userID']."'";
		//echo $last_search_sql;
                
                $DRW->query($last_search_sql,$DRW_main);
                 //,is_mobile=$ismobile       
	}
	$query = "DELETE FROM cscan_search_product WHERE ID=$search_id";
	$DRW->query($query,$DRW_main);
	######################START ELASTIC SEARCH###################
	$sectorOnlyFlag='';
	$search_rulesArray = array();
	$search_rulesArrayf = array();
	$check_search_sql = "SELECT * FROM cscan_search where userID={$_SESSION['sess_userID']} AND ID='".$search_id."'";
	$resultCheck = $DRW->query($check_search_sql,$DRW_read);
	if($DRW->num_rows($resultCheck) > 0){
		$dataSearchCheck = $DRW->fetch_array($resultCheck);
			//print_r($dataSearchCheck); die;
			//echo $dataSearchCheck['IntroPricing_mult'];
		if($dataSearchCheck['userID']!=''){
			$postdata['userID']=$dataSearchCheck['userID'];
			}
		if($dataSearchCheck['userType']!=''){
			$postdata['userType']=$dataSearchCheck['userType'];
			}
		if($dataSearchCheck['search_rules']!='' && $dataSearchCheck['search_rules']!='NULL'){
			$nots='';
			$search_rulesArray = explode(',',$dataSearchCheck['search_rules']);
			foreach($search_rulesArray as $sr){
				if(!empty($sr)){
						list($f,$ao,$ex,$no) = explode(':',$sr);
						$search_rulesArrayf[$f] = array($ao,$ex,$no);
				}
			}
			//echo "<pre>";
			//print_r($search_rulesArrayf); 
			$sectorExactFlag='';
			if($search_rulesArrayf['sectorID'][0]=='1' AND $search_rulesArrayf['sectorID'][1]=='1' AND $search_rulesArrayf['sectorID'][2]=='0'){
				$postdata['multi_match_op'] ="sector_id,exact";
				$sectorExactFlag='1'; 
				
			}
			$sectorOnlyFlag=''; 
			if($search_rulesArrayf['sectorID'][0]=='1' AND $search_rulesArrayf['sectorID'][1]=='0' AND $search_rulesArrayf['sectorID'][2]=='0'){
				$postdata['multi_match_op'] ='sector_id,only'; 
				$sectorOnlyFlag='1';
			}
			if($search_rulesArrayf['mTypeID'][0]=='0' AND $search_rulesArrayf['mTypeID'][1]=='0' AND $search_rulesArrayf['mTypeID'][2]=='1'){
				$nots='mtype_id'; 
				
			}
			if($search_rulesArrayf['agentCommunicationID'][0]=='0' AND $search_rulesArrayf['agentCommunicationID'][1]=='0' AND $search_rulesArrayf['agentCommunicationID'][2]=='1'){
					$nots.=',agent_communication_id';
					if($dataSearchCheck['agentCommunicationID']==''){
					$nots.=',agent_communication_id(null)';
					$postdata['agent_communication_id']="1";
					}
	
			}
			if($nots!=''){
				$postdata['nots']=trim($nots,',');
			}
			
		}
		
		if($dataSearchCheck['sectorID']!=''){
			$postdata['sector_id']=$dataSearchCheck['sectorID']; 
		}
		if($sectorOnlyFlag==1){
		//if($sectorExactFlag==1 || $sectorOnlyFlag==1){
			if($dataSearchCheck['categoryID']!=''){
				$postdata['sector_id'].=",".$dataSearchCheck['categoryID'];
			}
			if($dataSearchCheck['subCategoryID']!=''){
				$postdata['sector_id'].=",".$dataSearchCheck['subCategoryID'];
			}
	
			if($dataSearchCheck['scsc_primary']!='0' AND $postdata['sector_id']!=''){
				$postdata['primary_only']="sector_id";
			}   
		}else{
			$categoryID='';
			if($dataSearchCheck['categoryID']!=''){
				
				$categoryID=$dataSearchCheck['categoryID'];
			}
			$subcateID='';
			if($dataSearchCheck['subCategoryID']!=''){
				$subcateID=$dataSearchCheck['subCategoryID'];
			}
			$subtosubcateID='';
			if($dataSearchCheck['subSubCategoryID']!=''){
				$subtosubcateID=$dataSearchCheck['subSubCategoryID'];
			}
			$appendSectCategory='';
			if($categoryID!=''){
				$appendSectCategory.='cat_:'.$categoryID.";";
			}if($subcateID!=''){
				$appendSectCategory.='cat_sub:'.$subcateID.";";
			}if($subtosubcateID!=''){
				$appendSectCategory.='cat_ssub:'.$subtosubcateID.";";
			}
			if($appendSectCategory!=''){
				$postdata['categories']='field:sector_id;'.rtrim($appendSectCategory,';');
			}
			if($dataSearchCheck['scsc_primary']!='0' AND $postdata['sector_id']!=''){
				$postdata['primary_only']="sector_id";
			}
		}
		
		$searchKey=$dataSearchCheck['searchKey'];
		//$searchKey = str_replace ("'","\"",$searchKey); 
		$searchType=$dataSearchCheck['searchType'];
		$searchKey2=$dataSearchCheck['searchKey2'];
		//$searchKey2 = str_replace ("'","\"",$searchKey2);
		$search_type_and=$dataSearchCheck['search_type_and'];
		if($searchKey!='' && $searchKey!='NULL' && $searchType=='fulltext2'){
			if (strpos ($searchKey, '"') !== false || strpos ($searchKey, "'") !== false) {
				$postdata['product_headline'] =strtoupper($searchKey); 
	
			}else{
				$postdata['product_headline'] =strtoupper($searchKey);
				$postdata["text_match_op"]="product_headline,and";
			}
		}elseif($searchKey!=''  && $searchKey!='NULL' && $searchType=='ocr2'){
			if (strpos ($searchKey, '"') !== false || strpos ($searchKey, "'") !== false) {
				$postdata['dts_val'] = strtoupper($searchKey);
				//$postdata['product_headline'] =strtoupper($searchKey); 
				}else{
					$postdata['dts_val'] = strtoupper($searchKey);
					$postdata["text_match_op"]="dts_val,and";
				}
		}elseif(($searchKey!='' && $searchKey!='NULL' && $searchType=='ocr_fulltext2') AND ($searchKey2!='' && $searchKey2!='NULL' && $search_type_and=='1')){
			//$postdata['product_headline'] =strtoupper($searchKey); 
			if (strpos ($searchKey, '"') !== false || strpos ($searchKey, "'") !== false || strpos ($searchKey2, '"') !== false || strpos ($searchKey2, "'") !== false) {
				$postdata['dts_val'] = strtoupper($searchKey);
				$postdata['product_headline'] =$searchKey2;
				}else{
					$postdata['dts_val'] = strtoupper($searchKey);
					$postdata['product_headline'] =$searchKey2;
					$postdata["text_match_op"]="dts_val,and;product_headline,and";
				}
			
		}elseif(($searchKey!='' && $searchKey!='NULL' && $searchType=='fulltext2') OR ($searchKey2!='' && $searchKey2!='NULL' && $search_type_and=='0')){
			$postdata['dts_val'] = strtoupper($searchKey);
			$postdata['product_headline'] =strtoupper($searchKey2);
			$postdata['text_search_join']="or";
		}
		//echo addslashes($searchKey); die;
		if($dataSearchCheck['mChannelID']!=''){
			$postdata['mchanne_id']=$dataSearchCheck['mChannelID'];
		 }
		 if($dataSearchCheck['mPanelID']!=''){
			 $postdata['mpanel_id']=$dataSearchCheck['mPanelID'];
		 }
		if($dataSearchCheck['ApplicationType_mult']!=''){
			$postdata['application_type']=$dataSearchCheck['ApplicationType_mult'];
		}
		if($dataSearchCheck['mTypeID']!=''){
			$postdata['mtype_id']=$dataSearchCheck['mTypeID'];
		}
		if($dataSearchCheck['IssueTypeID_mult']!=''){
			$postdata['issue_type_id']=$dataSearchCheck['IssueTypeID_mult'];
		}
		if($dataSearchCheck['personalization']!=''){
			$postdata['personalization']=$dataSearchCheck['personalization'];
		}
		
		if($dataSearchCheck['worksiteVoluntary']!='0' AND $dataSearchCheck['worksiteVoluntary']!=''){
			$postdata['worksite_voluntary']=$dataSearchCheck['worksiteVoluntary']==2 ? "0": $dataSearchCheck['worksiteVoluntary'];
		}
		if($dataSearchCheck['siteCatID_mult']!=''){
			$postdata['sites_category_id']=$dataSearchCheck['siteCatID_mult'];
		}
		
		if($dataSearchCheck['is_incentive']!='0'){
			$postdata['is_incentive']=$dataSearchCheck['is_incentive'];
		}
		if($dataSearchCheck['electronicID_mult']!=''){
			$postdata['electronic_id']=$dataSearchCheck['electronicID_mult'];
		}
		if($dataSearchCheck['creditUnion']!='0'){
			$postdata['is_credit_union']=$dataSearchCheck['creditUnion']==2 ? "0":$dataSearchCheck['creditUnion'];
		}
		if($dataSearchCheck['usda']!='0'){
			$postdata['usda']=$dataSearchCheck['usda'];
		}
		if($dataSearchCheck['conventional']!='0'){
			$postdata['conventional']=$dataSearchCheck['conventional'];
		}
		
		if($dataSearchCheck['fha']!='0'){
			$postdata['fha']=$dataSearchCheck['fha'];
		}
		if($dataSearchCheck['va']!='0'){
			$postdata['va']=$dataSearchCheck['va'];
		}
		if($dataSearchCheck['refinance']!='0'){
			//$postdata['refinance']=$dataSearchCheck['refinance'];
			$postdata['refinance']= $dataSearchCheck['refinance']==2 ? "0": $dataSearchCheck['refinance'];
		}
		if($dataSearchCheck['jumbo_ncnfg']!='0'){
			$postdata['jumbo_ncnfg']=$dataSearchCheck['jumbo_ncnfg'];
		}
		if($dataSearchCheck['faux_check']!='0'){
			$postdata['faux_check']=$dataSearchCheck['faux_check'];
		}
		//  faux_check
		if(trim($dataSearchCheck['fa_id_mult'])!=''){
			$postdata['face_id']=trim($dataSearchCheck['fa_id_mult'],',');
		}
		if($dataSearchCheck['is_hphsa']!='0'){
			$postdata['is_hphsa']=$dataSearchCheck['is_hphsa'];
		}
		if($dataSearchCheck['delmethid_mult']!=''){
			$postdata['delivery_method_id']=$dataSearchCheck['delmethid_mult'];
		}
		if($dataSearchCheck['deliveryTypeId']!=''){
			$postdata['delivery_type_id']=$dataSearchCheck['deliveryTypeId'];
		}
		if($dataSearchCheck['postageId']!=''){
			$postdata['postage_id']=$dataSearchCheck['postageId'];
		}
		if($dataSearchCheck['presortedId']!=''){
			$postdata['presorted_id']=$dataSearchCheck['presortedId'];
		}
		if($dataSearchCheck['packageTypeId']!=''){
			$postdata['package_type_id']=$dataSearchCheck['packageTypeId'];
		}
		if($dataSearchCheck['agentCommunicationID']!=''){
			$postdata['agent_communication_id']=$dataSearchCheck['agentCommunicationID'];
		}
		
		if($dataSearchCheck['productName']!='' && $dataSearchCheck['productName']!='NULL'){
			$postdata['product_name']= str_replace (" or ",",",$dataSearchCheck['productName']);
			
		}
		
		if($dataSearchCheck['socialmedia_adtype']!=0 && $dataSearchCheck['socialmedia_adtype']!=NULL){
			$postdata['socialmedia_adtype']=$dataSearchCheck['socialmedia_adtype'];
		}
		if($dataSearchCheck['is_CreditCardMentioned']!='0'){
			$postdata['r_credit_card_mentioned']=$dataSearchCheck['is_CreditCardMentioned'];
		}
		if($dataSearchCheck['groupSize']!=''){
			$postdata['group_size_id']=$dataSearchCheck['groupSize'];
		}
		if($dataSearchCheck['offerOrigin']!=''){
			$postdata['offer_origin_id']=$dataSearchCheck['offerOrigin'];
		}
		if($dataSearchCheck['income_mult']!=''){
			$postdata['income_id']=$dataSearchCheck['income_mult'];
		}
		
		if($dataSearchCheck['CardNetwork_mult']!=''){
			$postdata['card_network']=$dataSearchCheck['CardNetwork_mult'];
		}
		if($dataSearchCheck['FeeProduct']!='0'){
			$postdata['Fee_Product']=$dataSearchCheck['FeeProduct']==2?"0":$dataSearchCheck['FeeProduct'];
		}
		
		if($dataSearchCheck['IntroPricing_mult']!=''){
			$IntroductoryPriceMultiArray = explode(',',$dataSearchCheck['IntroPricing_mult']);
			#print_r($IntroductoryPriceMultiArray); die;
			
			$IntroductoryPricing=IntroductoryAPR($dataSearchCheck['IntroPricing_mult']);
			if (in_array("1", $IntroductoryPriceMultiArray) || in_array("2", $IntroductoryPriceMultiArray)){
			$postdata['purchase_introductory_apr'] ="1";
			}
			if (in_array("3", $IntroductoryPriceMultiArray) || in_array("4", $IntroductoryPriceMultiArray)){
			$postdata['balance_transfer_introductory_apr'] ="1";
			}
			$postdata['null_search'] =$IntroductoryPricing;
		}
		if($dataSearchCheck['Income_Producing_Assets_Segment_Code_mult']!=''){
			$postdata['income_producing_assets_segment_code']=$dataSearchCheck['Income_Producing_Assets_Segment_Code_mult'];
		}
		if($dataSearchCheck['is_rewards']!='0'){
			$postdata['rewards_program']=$dataSearchCheck['is_rewards'];
		}
		if($dataSearchCheck['RewardsProgramEmphasis_mult']!=''){
			$postdata['rewards_program_emphasis']=$dataSearchCheck['RewardsProgramEmphasis_mult'];
		}
		if($dataSearchCheck['edc_id_mult']!=''){
			$postdata['edc_id']=$dataSearchCheck['edc_id_mult'];
		}
		
		if($dataSearchCheck['DMA_ID_mult']!=''){
			$postdata['dma_code']=$dataSearchCheck['DMA_ID_mult'];
		}
		if($dataSearchCheck['riders_mult']!=''){
			$postdata['rider_id']=$dataSearchCheck['riders_mult'];
		}
		if($dataSearchCheck['ERateType_mult']!=''){
			$postdata['e_rate_type']=$dataSearchCheck['ERateType_mult'];
		}
		if($dataSearchCheck['ETermLength_mult']!=''){
			$postdata['e_term_length']=$dataSearchCheck['ETermLength_mult'];
			
		}
		if($dataSearchCheck['EOfferPrice_mult']!=''){
			$expoEOfferPriceArray= explode(',', $dataSearchCheck['EOfferPrice_mult']);
			$expoEOfferPriceData='';
			if(!empty($expoEOfferPriceArray)){
				if(in_array(1,$expoEOfferPriceArray)){
					$expoEOfferPriceData.='0.0,5.0;'; 
				}if(in_array(2,$expoEOfferPriceArray)){
					$expoEOfferPriceData.='5.01,8.50;'; 
				}if(in_array(3,$expoEOfferPriceArray)){
					$expoEOfferPriceData.='8.51,10.0;'; 
				}if(in_array(4,$expoEOfferPriceArray)){
					$expoEOfferPriceData.='10.01,12.50;'; 
				}if(in_array(5,$expoEOfferPriceArray)){
					$expoEOfferPriceData.='12.51,'; 
				}
			}
			if($expoEOfferPriceData!=''){
				$postdata['e_offer_price']=trim($expoEOfferPriceData,';');
				$postdata['range_search']= "e_offer_price";
			}
	
		}
		if($dataSearchCheck['is_ECancelFee']!='0'){
			$postdata['e_cancel_fee']=$dataSearchCheck['is_ECancelFee'];
		}
		if($dataSearchCheck['tl_id_mult']!=''){
			$postdata['term_id']=$dataSearchCheck['tl_id_mult'];
		}
		if($dataSearchCheck['correspondent_lending']!='0'){
			$postdata['correspondent_lending']=$dataSearchCheck['correspondent_lending'];
		}
		if($dataSearchCheck['responseMechID_mult']!=''){
			$postdata['response_mechanism_id']=$dataSearchCheck['responseMechID_mult'];
		}
		
		if($dataSearchCheck['FeeProductType']!=''){
			$postdata['ancillary_product_id']=$dataSearchCheck['FeeProductType'];
		}
		
		if($dataSearchCheck['multiculturalmarkets_mult']!=''){
			$postdata['target_market_id']=$dataSearchCheck['multiculturalmarkets_mult'];
			
			
		}elseif($dataSearchCheck['multiculturalmarkets_mult']=='' AND $dataSearchCheck['is_multicultural']==1) {
				$postdata['target_market_id']='1';
				$postdata['null_search']='target_market_id,notnull';
		}
		
		if($dataSearchCheck['OptOutFirmOffer']!='0'){
			$postdata['is_prescreen']=$dataSearchCheck['OptOutFirmOffer']==2 ? "0":$dataSearchCheck['OptOutFirmOffer'] ;
			
		}
		if($dataSearchCheck['is_affinion']!='0'){
			$postdata['is_affinion']=$dataSearchCheck['is_affinion'];
		}
		
		if($dataSearchCheck['is_military']!='0'){
			$postdata['is_military']=$dataSearchCheck['is_military'];
		}
		if($dataSearchCheck['prescription']!='0'){
			$postdata['prescription']=$dataSearchCheck['prescription'];
		}
		if($dataSearchCheck['compaignLanguage']!=''){
			if($dataSearchCheck['compaignLanguage']=='English'){
				$postdata['language_id']="1";   
			}
			if($dataSearchCheck['compaignLanguage']=='Bilingual'){
				$postdata['language_id']="2";    
			}
			
		}
		if($dataSearchCheck['businessContent_mult']!=''){
			$postdata['business_content']=$dataSearchCheck['businessContent_mult'];
		}
		if($dataSearchCheck['is_citi']!='0'){
			$postdata['is_citi']=$dataSearchCheck['is_citi']==2?"0":$dataSearchCheck['is_citi'];
		}
		
		if($dataSearchCheck['minmaxmortgage']!='' && $dataSearchCheck['minmaxmortgage']!='0-2000000' && $dataSearchCheck['minmaxmortgage']!='NULL'){
			$exp_loanAmount=explode('-',$dataSearchCheck['minmaxmortgage']);
		}
		//print_r($exp_loanAmount);die;
		if(!empty($exp_loanAmount)){
			$postdata['minimum_loan_amount']=$exp_loanAmount[0].",";
			$postdata['maximum_loan_amount']="0,".$exp_loanAmount[1];
			$postdata['offered_loan_amount']=$exp_loanAmount[0].",".$exp_loanAmount[1];
			$postdata['range_search']="minimum_loan_amount, maximum_loan_amount,offered_loan_amount;rel: (minimum_loan_amount,maximum_loan_amount)|(offered_loan_amount)";
		}
		
		
		if($dataSearchCheck['addedToDatabase']!=''){
			if($dataSearchCheck['addedToDatabase']=='week'){
				$new_date='1 week';
			}
			if($dataSearchCheck['addedToDatabase']=='2week'){
				$new_date='2 week';
			}
			if($dataSearchCheck['addedToDatabase']=='1month'){
				$new_date='';
			}
			if($dataSearchCheck['addedToDatabase']=='3month'){
				$new_date='3 month';
			}
			if($dataSearchCheck['addedToDatabase']=='6month'){
				$new_date='6 month';
			}
			if($dataSearchCheck['addedToDatabase']=='1year'){
				$new_date='1 year';
			}
			$date = date('Y-m-d');
			$newAddedToDatabase = date('Y-m-d', strtotime($date. ' - '.$new_date));
			if($new_date==''){
				$newAddedToDatabase = get_python_like_previous_month(date('Y-m-d'));
			}
			$end_add_to_db="E".$date."T23:59:59";
			$st_add_to_db="S".$newAddedToDatabase."T00:00:00";
			$postdata['added_to_database']=$st_add_to_db.$end_add_to_db;
		 }
		if($dataSearchCheck['month1']!='' && $dataSearchCheck['month2']!=''){
			$st_month="S".$dataSearchCheck['month1']."-01T00:00:00";
			
			$end_month=$dataSearchCheck['month2'];
			$lastDateOfMonth = date("Y-m-t", strtotime($end_month));
			$end_month="E".$lastDateOfMonth."T23:59:59";
			//"S2022-01-01T00:00:00E2022-01-31T59:59:59"
			$postdata['added_to_database']=$st_month.$end_month;
		}
		if($dataSearchCheck['approved_date']!='0000-00-00 00:00:00' && $dataSearchCheck['approved_date_to']!='0000-00-00 00:00:00'){
			$exp_from_approved_date= explode(' ', $dataSearchCheck['approved_date']);
			$from_approved_date="S".$exp_from_approved_date[0]."T".$exp_from_approved_date[1];
			$exp_to_approved_date= explode(' ', $dataSearchCheck['approved_date_to']);
			$to_approved_date="E".$exp_to_approved_date[0]."T".$exp_to_approved_date[1];
			$postdata['approved_date']=$from_approved_date.$to_approved_date;
		}
		
		if($dataSearchCheck['state']!=''){
			$postdata['state_id']=trim($dataSearchCheck['state']); 
			
		}
		if($dataSearchCheck['pcountry']!=''){
			$postdata['country']= $dataSearchCheck['pcountry'];
		}
		if(!empty($dataSearchCheck['company']) && $dataSearchCheck['company']!='NULL') {
			$companySearch =trim($dataSearchCheck['company']);
			$cmpidArray=array();
			$sql_comp = "select companyID,companyName from cscan_company Where " . doMultCompany($companySearch, true, 'company');
			$result_comp = $DRW->query($sql_comp, $DRW_read); 
			while($row_company = $DRW->fetch_assoc($result_comp)){
				$comp_id=$row_company['companyID'];
				$cmpidArray[]=$comp_id;
			}
			if(!empty($cmpidArray)){
			$postdata['company_id'] = @implode(",",$cmpidArray); 
			} 
		}
		if($dataSearchCheck['affinityAssociation']!='0'){
			$postdata['affinity_association']=$dataSearchCheck['affinityAssociation']==2 ? "0":$dataSearchCheck['affinityAssociation'];
			
		}
		if($dataSearchCheck['AffinityCategoryID_mult']!=''){
			$postdata['affinity_category']=$dataSearchCheck['AffinityCategoryID_mult'];
		}
		if($dataSearchCheck['AffinitySubCategoryID_mult']!=''){
			$postdata['affinity_sub_category']=$dataSearchCheck['AffinitySubCategoryID_mult'];
		}
		
		//echo $dataSearchCheck['affinity_association']; 
		if(!empty($dataSearchCheck['affinity_association']) && $dataSearchCheck['affinity_association']!='NULL') {
			$affninityAssocationSearch =trim($dataSearchCheck['affinity_association']);
			$affinityIdArray=array();
			$sql_affinity = "select affinityID,affinityName from cscan_affinity Where " . doMultCompany($affninityAssocationSearch, true, 'affinity');
			$result_affinity = $DRW->query($sql_affinity, $DRW_read); 
			while($row_affinity = $DRW->fetch_assoc($result_affinity)){
				$affinityId=$row_affinity['affinityID'];
				$affinityIdArray[]=$affinityId;
			}
			if(!empty($affinityIdArray)){
			$postdata['afinity_id'] = @implode(",",$affinityIdArray); 
			} 
		}
		
		if(trim($dataSearchCheck['pubTypeID_mult'])!=''){
			$postdata['publication_type_id']=$dataSearchCheck['pubTypeID_mult'];
		}
			
		if(!empty($dataSearchCheck['publication_name']) && $dataSearchCheck['publication_name']!='NULL') {
			$publicationSearch =trim($dataSearchCheck['publication_name']);
			$publicationIdArray=array();
			$sql_publication = "select * from cscan_publication Where " . doMultCompany($publicationSearch, true, 'publication');
			$result_publication = $DRW->query($sql_publication, $DRW_read); 
			while($row_publication = $DRW->fetch_assoc($result_publication)){
				$publicationID=$row_publication['publicationID'];
				$publicationIdArray[]=$publicationID;
			}
			if(!empty($publicationIdArray)){
			$postdata['publication_id'] = @implode(",",$publicationIdArray); 
			} 
		}
		// fico and creditVision,vantageScore credit_vision_score
		if($dataSearchCheck['fico_score']!='' && $dataSearchCheck['fico_score']!='NULL'){
			$postdata['fico_range_id']=$dataSearchCheck['fico_score'];
			
		}
		if($dataSearchCheck['credit_vision_score']!='' && $dataSearchCheck['credit_vision_score']!='NULL'){
			$postdata['creditvision_range_id']=$dataSearchCheck['credit_vision_score'];
			
		}
		if($dataSearchCheck['vantage_score']!='' && $dataSearchCheck['vantage_score']!='NULL'){
			$postdata['vantage_range_id']=$dataSearchCheck['vantage_score'];
			
		}
		if($dataSearchCheck['is_Reloadable']!='0'){
			$postdata['is_reloadable']=$dataSearchCheck['is_Reloadable'];
			
		}
		if($dataSearchCheck['spanelist_filter']=='1' && $dataSearchCheck['search_competi_id']!='' && $dataSearchCheck['search_competi_id']!='NULL'){
			$postdata['competi_id']=$dataSearchCheck['search_competi_id'];                           
			$postdata['competi_id_filter']='primary'; 
		}
		if($dataSearchCheck['spanelist_filter']=='2' && $dataSearchCheck['search_competi_id']!='' && $dataSearchCheck['search_competi_id']!='NULL'){
			$postdata['competi_id']=$dataSearchCheck['search_competi_id'];
			$postdata['competi_id_filter']='non-primary'; 
			
		}
		if($dataSearchCheck['spanelist_filter']=='' && $dataSearchCheck['search_competi_id']!='' && $dataSearchCheck['search_competi_id']!='NULL'){
			
			$postdata['competi_id']=$dataSearchCheck['search_competi_id']; 
		}
		if($dataSearchCheck['external_link']!=''){
			$postdata['external_link']=$dataSearchCheck['external_link'];
			
		}
		if($dataSearchCheck['value_score']!=''){
			$postdata['valuescore_for_household']=$dataSearchCheck['value_score'];
			
		}
		if($dataSearchCheck['age']!=''){
			$postdata['age_id']=$dataSearchCheck['age'];
			
		}
		if($dataSearchCheck['sender_domain_name']!='' && $dataSearchCheck['sender_domain_name']!='NULL'){
			$postdata['sender_domain']= str_replace (" or ",",",$dataSearchCheck['sender_domain_name']);
			
		}
		$postdata['product_status']="1";
		$API_URL_SAVE_SEARCH=ELASTIC_SAVE_SEARCH_UAT;
		$chcsv2 = curl_init($API_URL_SAVE_SEARCH);
		$postdata['sortby'] ='entry_id_sort, desc';
		$postdata['tiebreaker'] ='product_id, desc';
		$posteddata = json_encode($postdata);
		echo $posteddata; 
		curl_setopt($chcsv2, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($chcsv2, CURLOPT_FOLLOWLOCATION, TRUE);
		curl_setopt($chcsv2, CURLOPT_POST, 1);
		curl_setopt($chcsv2, CURLOPT_POSTFIELDS, $posteddata);
		curl_setopt($chcsv2, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($chcsv2, CURLOPT_HTTPHEADER,array('Content-Type: application/json','User-Agent:'.$_SERVER['HTTP_USER_AGENT'], 'X-Forwarded-For:'.$_SERVER['REMOTE_ADDR']));
		curl_setopt($chcsv2, CURLOPT_TIMEOUT, 80);
		$response_api = curl_exec($chcsv2);
		// echo "<pre>";
		// print_r($response_api); 
		// echo "</pre>";
		if(curl_error($chcsv2)){
				echo 'Request Error:' . curl_error($chcsv2);
		}else{
				$jsonResponseAPIData = json_decode($response_api, JSON_UNESCAPED_UNICODE);
				// echo "<pre>";
				// print_r($jsonResponseAPIData); 
				if($jsonResponseAPIData['statusMsg']=='OK' && $jsonResponseAPIData['statusCode']=='200'){
				    $sess_api_searchID=$jsonResponseAPIData['payload']['0']; 
					$_SESSION["sess_api_searchID"] = $sess_api_searchID;
				}
		}
		curl_close($chcsv2);
		###################IMPLEMENT SAVE SEARCH#########################
		$API_URL_SAVE_SEARCH=ELASTIC_SAVE_SEARCH_UAT;
		if($dataSearchCheck['userID']!=''){
		$postdata['userID']=$dataSearchCheck['userID'];
		}
		$posteddata_save = json_encode($postdata);
		$chcsv2_save = curl_init($API_URL_SAVE_SEARCH);
		curl_setopt($chcsv2_save, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($chcsv2_save, CURLOPT_FOLLOWLOCATION, TRUE);
		curl_setopt($chcsv2_save, CURLOPT_POST, 1);
		curl_setopt($chcsv2_save, CURLOPT_POSTFIELDS, $posteddata_save);
		curl_setopt($chcsv2_save, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($chcsv2, CURLOPT_HTTPHEADER,array('Content-Type: application/json','User-Agent:'.$_SERVER['HTTP_USER_AGENT'], 'X-Forwarded-For:'.$_SERVER['REMOTE_ADDR']));
		curl_setopt($chcsv2_save, CURLOPT_TIMEOUT, 80);
		$response_api_save = curl_exec($chcsv2_save);
		//print_r($response_api); die;
		if(curl_error($chcsv2_save)){
				echo 'Request Error:' . curl_error($chcsv2_save);
		}else{
			$jsonResponseAPIDataSave = json_decode($response_api_save, JSON_UNESCAPED_UNICODE);
			// echo "<pre>";
			// print_r($jsonResponseAPIDataSave); 
			//die;
			if($jsonResponseAPIDataSave['statusMsg']=='OK' && $jsonResponseAPIDataSave['statusCode']=='200'){
				$sess_api_searchID=$jsonResponseAPIDataSave['payload']['0']; 
				$_SESSION["sess_api_searchID"] = $sess_api_searchID;
			}
		}
		curl_close($chcsv2_save);
	}
################END OF ELSTIC SEARCH CRITERIA#############################




	############## track search activity of each user ##########################
        if(!defined('ENV')){
            define('ENV',getenv('SERVER_NAME'));
        }
        //if(ENV == 'localhost' || ENV == 'demo.competiscan.com'){
            track_search_activity($_SESSION['sess_userID'],$search_id);
        //}
	############ end track search activity of each user ########################
	ob_end_clean();
	header("Location: fullresults_testing.php?ssid=$search_id");
	exit;
}
elseif($search_id!=0){
	$savedQ = "SELECT searchKey,searchType,searchOption,mChannelID,sectorID,mPanelID,
			addedToDatabase,month1,month2,sort,company,productName,incentive,categoryID,mTypeID,subCategoryID,cardStatus,personalization,
			gender,age,state,worksiteVoluntary,agentCommunicationID,groupSize,offerOrigin,enhance,saved,searchview,compaignLanguage,affinityAssociation,searchName,income_mult,fa_id_mult,tl_id_mult,siteCatID_mult,pubTypeID_mult,approved_date,electronicID_mult,DMA_ID_mult,businessContent_mult,delmethid_mult,
			affinity_association,prescription,AffinityCategoryID_mult,search_panelist_date,is_affinion,is_military,search_competi_id,ApplicationType_mult,is_multicultural,search_rules,IntroPricing_mult,is_rewards,RewardsProgramEmphasis_mult,is_incentive,responseMechID_mult,multiculturalmarkets_mult,CardNetwork_mult,FeeProduct,external_link,FeeProductType,approved_date_to,
			ca_related,is_mover,scsc_primary,OptOutFirmOffer,searchKey2,search_type_and,riders_mult,is_hphsa,subSubCategoryID,Income_Producing_Assets_Segment_Code_mult,cg_id,is_citi,is_CreditCardMentioned,spanelist_filter,edc_id_mult,AffinitySubCategoryID_mult,ERateType_mult,EOfferPrice_mult,ETermLength_mult,is_ECancelFee,IssueTypeID_mult,pcountry,is_Reloadable,creditUnion,is_mobile,value_score$searchMortgageKey$faux_checkInto$minmaxmortgageinfo$socialmedia_adtypeinfo,publication_name,deliveryTypeId,postageId,presortedId,packageTypeId,fico_score,credit_vision_score,vantage_score,sender_domain_name
			FROM cscan_search WHERE ID='".$search_id."' AND userID='".$_SESSION['sess_userID']."'";
	//,is_mobile
       // exit;
        $rs = $DRW->query($savedQ,$DRW_read);
	$data = $DRW->fetch_row($rs);
        //print_r($data);exit;
	$skey = $data[0];
	$search_type = $data[1];
	$search_option = $data[2];
	$mChannelID = explode(',',$data[3]);
	$sectorID = explode(',',$data[4]);
	$mPanelID = explode(',',$data[5]);
	$addedToDatabase = $data[6];
	$month1 = $data[7];
	$month2 = $data[8];
	$sort = $data[9];
	$company = $data[10];
	$productName = $data[11];
	$incentive = $data[12];
	$categoryID = explode(',',$data[13]);
	$mTypeIDval = explode(',',$data[14]);
	$subCategoryID = explode(',',$data[15]);
	$cardStatusval = $data[16];
	$personalizationval = $data[17];
	$genderval = $data[18];
	$ageval = explode(',',$data[19]);
	$state = explode(',',$data[20]);
	$worksiteVoluntaryval = $data[21];
	$agentCommunicationIDval = explode(',',$data[22]);
	$groupSizeval = explode(',',$data[23]);
	$offerOriginval = $data[24];
	$enhance = $data[25];
	$saved = $data[26];
	$searchview = $data[27];
	$compaignLanguageval = $data[28];
	$affinityAssociationval = $data[29];
	$searchName = $data[30];
	$incomeval = explode(',',$data[31]);
	$fa_idval = explode(',',$data[32]);
	$tl_idval = explode(',',$data[33]);
	$siteCatID = explode(',',$data[34]);
	$pubTypeID = explode(',',$data[35]);
	$approved_date = $data[36];
	$electronicID = explode(',',$data[37]);
	$DMA_ID = explode(',',$data[38]);
	$businessContent = explode(',',$data[39]);
	$delmethid_mult = explode(',',$data[40]);
	$affinity_association = $data[41];
	$prescriptionval = $data[42];
	$AffinityCategoryIDval = explode(',',$data[43]);
	$search_panelist_date = $data[44];
	$is_affinion = $data[45];
	$is_military = $data[46];
	$search_competi_id = $data[47];
	$ApplicationType_mult = explode(',',$data[48]);
	$is_multicultural = $data[49];
	$search_rulesArray = explode(',',$data[50]);
	$IntroPricing_mult = explode(',',$data[51]);
	$is_rewards = $data[52];
	$RewardsProgramEmphasis_mult = explode(',',$data[53]);
	$is_incentive = $data[54];
	$responseMechID_mult = explode(',',$data[55]);
	$multiculturalmarkets_mult = explode(',',$data[56]);
	$CardNetwork_mult = explode(',',$data[57]);
	$FeeProduct = $data[58];
	$external_link = $data[59];
	$FeeProductType = explode(',',$data[60]);
	$approved_date_to = $data[61];
	$ca_related = $data[62];
	$is_mover = $data[63];
	$scsc_primary = $data[64];
	$OptOutFirmOffer = $data[65];
	$skey2 = $data[66];
	$search_type_and = $data[67];
	$riders_mult = explode(',',$data[68]);
	$is_hphsa = $data[69];
	$subSubCategoryID = explode(',',$data[70]);
	$Income_Producing_Assets_Segment_Code = explode(',',$data[71]);
	$cg_id = $data[72];
	$is_citi = $data[73];
	$is_CreditCardMentioned = $data[74];
	$spanelist_filter = $data[75];
	$edc_id = explode(',',$data[76]);
	$AffinitySubCategoryIDval = explode(',',$data[77]);
	$ERateType_mult = explode(',',$data[78]);
	$EOfferPrice_mult = explode(',',$data[79]);
	$ETermLength_mult = explode(',',$data[80]);
	$is_ECancelFee = $data[81];
	$IssueTypeID_mult = explode(',',$data[82]);
	$pcountry = $data[83];
	$is_Reloadable = $data[84];
	$creditUnion = $data[85];
        $is_mobile= $data[86];
        $value_score=explode(',',$data[87]);
        $refinance = $data[88];
        $jumbo_ncnfg = $data[89];
        $va = $data[90];
        $fha = $data[91];
        $conventional = $data[92];
        $usda = $data[93];
        $correspondent_lending=$data[94];
        $faux_check=$data['95'];
        //$minmax=$data['95'];       
        
        ################ for minmax mortgage #####################
        $minmax=$data['96'];
        $minloanamount  =   0;
        $maxloanamount  =   2000000;
        if(!empty($minmax)){
            $minmaxarray    =explode("-",$minmax);
            $minloanamount  =  $minmaxarray[0];
            $maxloanamount  =  $minmaxarray[1];
        }
        ################ for minmax mortgage #####################
        
        ################ for social media sponsered  #####################
        $socialmedia_adtype =   $data[97];
        
        ################ end for social media sponsered  #####################
        
        ############### For publication name ######################
        $publication_name=$data[98];
        ############### End For publication name ######################
        ##############################Start Envelope/Postage Data Fields##############
        $deliveryTypeId = explode(',',$data[99]);
        $postageId = explode(',',$data[100]);
        $presortedId = explode(',',$data[101]);
        $packageTypeId = explode(',',$data[102]);
        ##############################End Envelope/Postage Data Fields##############
        
        ############################## Start Fico/Credit Vision, Vantage Score fields ##############
        
        $fico_score = explode(',',$data[103]);
        $credit_vision_score = explode(',',$data[104]);
        $vantage_score = explode(',',$data[105]);
		$sender_domain_name = $data[106];        
        
        ############################## End Fico/Credit Vision, Vantage Score fields ##############
        
	foreach($search_rulesArray as $sr){
		if(!empty($sr)){
			list($f,$ao,$ex,$no) = explode(':',$sr);
			$search_rulesArrayf[$f] = array($ao,$ex,$no);
		}
	}        
}
elseif($search_id!=0){
	$savedQ = "SELECT searchName
			FROM cscan_search WHERE ID='".$search_id."' AND userID='".$_SESSION['sess_userID']."'";
	$rs = $DRW->query($savedQ,$DRW_read);
	$data = $DRW->fetch_row($rs);
	$searchName = $data[0];
}

$search_combination = false;
$javascript = '';

$addedToDatabasedis = '';
$monthdis = '';
if($addedToDatabase!='') {
	$monthdis = ' disabled="disabled"';
}
elseif($month1!='' || $month1!=''){
	$addedToDatabasedis = ' disabled="disabled"';
}

//jm build affSubCategoryArray here
$affcats = getAffinityCategory(0);
foreach($affcats as $affcat_id=>$affcat_name) {
	$javascript .= "affSubCategoryArray['$affcat_id'] = new Array();\n";
	$affsubcats = getAffinityCategory($affcat_id);
	foreach($affsubcats as $affsubcat_id=>$affsubcat_name) {
		$javascript .= "affSubCategoryArray['$affcat_id']['$affsubcat_id'] = '".singleQuoteSafe($affsubcat_name)."';\n";
	}
}

$javascript .= "depsArrayName['offerOrigin'] = new Array();\ndepsArrayID['offerOrigin'] = new Array();\ndepsArrayS['offerOrigin'] = new Array();\ndepsArrayM['offerOrigin'] = new Array();\n";
$javascript .= "depsArrayName['agentCommunicationID[]'] = new Array();\ndepsArrayID['agentCommunicationID[]'] = new Array();\ndepsArrayS['agentCommunicationID[]'] = new Array();\ndepsArray['agentCommunicationID[]'] = new Array();\n";
$javascript .= "depsArrayName['responseMechID_mult[]'] = new Array();\ndepsArrayID['responseMechID_mult[]'] = new Array();\ndepsArrayS['responseMechID_mult[]'] = new Array();\n";
$javascript .= "depsArrayName['riders_mult[]'] = new Array();\ndepsArrayID['riders_mult[]'] = new Array();\ndepsArrayS['riders_mult[]'] = new Array();\n";
$javascript .= "depsArrayName['multiculturalmarkets_mult[]'] = new Array();\ndepsArrayID['multiculturalmarkets_mult[]'] = new Array();\ndepsArrayS['multiculturalmarkets_mult[]'] = new Array();\n";
$sri = 0;
$dap = 0;
$dai = 0;
$displayArray = array();

$displayArray[$dap] = array();
$displayArray[$dap][$dai] = array();

$displayArray[$dap][$dai]['show'] = true;
$displayArray[$dap][$dai]['title'] = 'Media Channel :';
$displayArray[$dap][$dai]['value'] = '<select class="combo_box" id = "mChannelID" name="mChannelID[]" size="3" multiple="multiple" onchange="getEnh(1);doEnvelopePostageData();getDeps(\'offerOrigin\');showPublicationName();">';
//showDigitalSource();
//$displayArray[$dap][$dai]['value'] = '<select class="combo_box" id = "mChannelID" name="mChannelID[]" size="3" multiple="multiple" onchange="getEnh(1);getDeps(\'offerOrigin\');">';

$media_channel = getMediaChannel();
foreach($media_channel as $id=>$name){
	$javascript .= "depsArrayM['offerOrigin']['$id'] = new Array();\n";
	if(!in_array($id,$_SESSION['sess_mchannel'])){
		continue;
	}
	$displayArray[$dap][$dai]['value'] .= "<option value=\"$id\"";
	if(in_array($id,$mChannelID)) {
		$displayArray[$dap][$dai]['value'] .= " selected=\"selected\"";
	}
	$displayArray[$dap][$dai]['value'] .= ">".htmlspecialchars($name)."</option>";
}
$displayArray[$dap][$dai]['value'] .= '</select><br />[Hold ctrl key for multiple selection]';
$dai++;


/* Add for Digital Media chanel like online display, video and sem  */

$showhide=false;
$selected='';
$selected2='';
$selected3='';
if(in_array('5',$mChannelID) || in_array('10',$mChannelID) || in_array('9',$mChannelID)) {
   $showhide=true;
   
    if($is_mobile=='1'){
        $selected='checked="checked"';
        
    }elseif($is_mobile=='2'){
        $selected2='checked="checked"';
        
    }else{
        $selected3='checked="checked"';
        
    }
}
if($selected3=='' AND $selected2=='' AND $selected==''){
    $selected3='checked="checked"';
}
//$displayArray[$dap][$dai]['show'] = $showhide;
//$displayArray[$dap][$dai]['title'] = 'Digital Source :';
//$displayArray[$dap][$dai]['value'] = '<input type="radio" value="1" name="ismobile" id="ismobile1"'.$selected.'><label for="ismobile1">Only Desktop </label>&nbsp;&nbsp;<input type="radio" value="2" name="ismobile" id="ismobile2"'.$selected2.'><label for="ismobile2">Only Mobile</label>&nbsp;&nbsp;<input type="radio" value="0" name="ismobile" id="ismobile3"'.$selected3.'><label for="ismobile3">Both</label>';$dai++;


/* End for digital Media chanel */ 

if(in_array('2',$mChannelID)) {
    $is_publication_name=1;
}else{
    $is_publication_name=0;
}


$coreArray = array();
$sql = "SELECT sectorID FROM cscan_sector WHERE sectorSearchActive=1 AND parentID=0 AND is_core=1";
$rs = $DRW->query($sql,$DRW_read);
while($row = $DRW->fetch_array($rs)) {
	$coreArray[] = $row[0];
}
$displayArray[$dap][$dai]['show'] = true;
$displayArray[$dap][$dai]['title'] = 'Sector :';
###########  Communication Type Implementation ############
//if(ENV=='demo.competiscan.com' || ENV=='localhost'){
	$displayArray[$dap][$dai]['value'] = '<input type="hidden" value="" id="agentCommunicationIDS" /><input type="hidden" value="" id="sectorIDs" /><input type="hidden" value="" id="categoryIDs" /><input type="hidden" value="" id="subCategoryIDs" /><input type="hidden" value="" id="subsubCategoryIDs" /><select class="combo_box" id="sectorID" name="sectorID[]" size="3" multiple="multiple" onchange="getEnh(2);getCat();getDeps(\'offerOrigin\');getDeps(\'agentCommunicationID[]\');getDeps(\'responseMechID_mult[]\');getDeps(\'riders_mult[]\');getDeps(\'multiculturalmarkets_mult[]\');">';
/*}else{
	$displayArray[$dap][$dai]['value'] = '<select class="combo_box" id="sectorID" name="sectorID[]" size="3" multiple="multiple" onchange="getEnh(2);getCat();getDeps(\'offerOrigin\');getDeps(\'agentCommunicationID[]\');getDeps(\'responseMechID_mult[]\');getDeps(\'riders_mult[]\');getDeps(\'multiculturalmarkets_mult[]\');">';
}*/
###########  Communication Type Implementation ############
$ctextArray = array();
$sctextArray = array();
$ssctextArray = array();
$sector = getSector();
$scsc = array();
foreach($sector as $id=>$name){
	$javascript .= "depsArrayS['offerOrigin']['$id'] = new Array();\n";
	$javascript .= "depsArrayS['agentCommunicationID[]']['$id'] = new Array();\n";
	$javascript .= "depsArrayS['responseMechID_mult[]']['$id'] = new Array();\n";
	$javascript .= "depsArrayS['riders_mult[]']['$id'] = new Array();\n";
	$javascript .= "depsArrayS['multiculturalmarkets_mult[]']['$id'] = new Array();\n";
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
			$javascript .= "depsArrayS['offerOrigin']['$cid'] = new Array();\n";
			$javascript .= "depsArrayS['agentCommunicationID[]']['$cid'] = new Array();\n";
			$javascript .= "depsArrayS['responseMechID_mult[]']['$cid'] = new Array();\n";
			$javascript .= "depsArrayS['riders_mult[]']['$cid'] = new Array();\n";
			$javascript .= "depsArrayS['multiculturalmarkets_mult[]']['$cid'] = new Array();\n";
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
						$cname = 'Credit Cards'.$ind;
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
					$javascript .= "depsArrayS['offerOrigin']['$scid'] = new Array();\n";
					$javascript .= "depsArrayS['agentCommunicationID[]']['$scid'] = new Array();\n";
					$javascript .= "depsArrayS['responseMechID_mult[]']['$scid'] = new Array();\n";
					$javascript .= "depsArrayS['riders_mult[]']['$scid'] = new Array();\n";
					$javascript .= "depsArrayS['multiculturalmarkets_mult[]']['$scid'] = new Array();\n";
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
							93: Payment Cards  Credit Cards
							92: Payment Cards  Charge Cards
							212: Payment Cards  Business Cards
							102: Payment Cards  Private Label Cards
							103: Payment Cards - Prepaid Cards
							91: Payment Cards  Corporate Cards
							*/
							$reordermap = array('93','92','212','102','103','91');
							$ind = array_search($scid,$reordermap);
							if($ind!==false){
								$scname = 'Payment Cards'.$ind;
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
//if($_SESSION['sess_plevel']==1 || $_SESSION['sess_plevel']==2){
	$displayArray[$dap][$dai]['value'] .= '<br />'.addSearchRules('sectorID');
//}
$dai++;

function addSearchRules($field,$doNot=false){
	$out = '';
	if(isset($GLOBALS['search_rulesArrayf'][$field])){
		list($ao,$ex,$no) = $GLOBALS['search_rulesArrayf'][$field];
	}
	else{
		$ao = $ex = $no = 0;
	}
	if($doNot){
		$out .= '<label><input type="checkbox" name="srno'.$GLOBALS['sri'].'" value="1"';
		if($no){
			$out .= ' checked="checked"';
		}
		$out .= ' />Not</label>';
	}
	else{
		$out = '<label><input type="radio" name="srao'.$GLOBALS['sri'].'" value="0" onclick="document.searchForm.srex'.$GLOBALS['sri'].'.disabled=true;document.searchForm.srex'.$GLOBALS['sri'].'.checked=false;"';
		if(!$ao){
			$out .= ' checked="checked"';
		}
		$out .= ' />Any</label> <label><input type="radio" name="srao'.$GLOBALS['sri'].'" value="1" onclick="document.searchForm.srex'.$GLOBALS['sri'].'.disabled=false;"';
		if($ao){
			$out .= ' checked="checked"';
		}
		$out .= ' />Only</label> <label><input type="checkbox" name="srex'.$GLOBALS['sri'].'" value="1"';
		if($ex){
			$out .= ' checked="checked"';
		}
		if(!$ao){
			$out .= ' disabled="disabled"';
		}
		$out .= ' />Exact</label>';
	}
	$out .= '<input type="hidden" name="srf'.$GLOBALS['sri'].'" value="'.htmlspecialchars($field, ENT_QUOTES).'" />';
	$GLOBALS['sri']++;
	return $out;
}

$displayArray[$dap][$dai]['show'] = true;
$displayArray[$dap][$dai]['title'] = 'Audience :';
###########  Communication Type Implementation ############
//if(ENV=='demo.competiscan.com' || ENV=='localhost'){
$displayArray[$dap][$dai]['value'] = '<input type="hidden" value="" id="audienceIDs" /><select class="combo_box" id="mPanelID" name="mPanelID[]" size="3" multiple="multiple" onchange="getEnh(3);getDeps(\'agentCommunicationID[]\');">';
/*}else{
	$displayArray[$dap][$dai]['value'] = '<select class="combo_box" id="mPanelID" name="mPanelID[]" size="3" multiple="multiple" onchange="getEnh(3);getDeps(\'agentCommunicationID[]\');">';
	continue;
}*/
###########  Communication Type Implementation ############
$mailing_panel = getMailingPanel();
foreach($mailing_panel as $id=>$name){
	$javascript .= "depsArray['agentCommunicationID[]']['$id'] = new Array();\n";
	if(!in_array($id,$_SESSION['sess_mpanel'])){
	}
	$displayArray[$dap][$dai]['value'] .= "<option value=\"$id\"";
	if(in_array($id,$mPanelID)) {
		$displayArray[$dap][$dai]['value'] .= " selected=\"selected\"";
	}
	$displayArray[$dap][$dai]['value'] .= ">".htmlspecialchars($name)."</option>";
}
$displayArray[$dap][$dai]['value'] .= '</select><br />[Hold ctrl key for multiple selection]';
$dai++;

$displayArray[$dap][$dai]['show'] = true;
$displayArray[$dap][$dai]['title'] = 'Search Date :';
$displayArray[$dap][$dai]['value'] = '<select name="addedToDatabase" class="combo_box" onchange="validateMonth();"'.$addedToDatabasedis.'><option value="">Select Period</option>';
$ad_to_db_opt = get_addedToDatabaseArray();
foreach($ad_to_db_opt as $key => $value) {
	$displayArray[$dap][$dai]['value'] .= "<option value=\"$key\"";
	if($key == $addedToDatabase) {
		$displayArray[$dap][$dai]['value'] .= " selected=\"selected\"";
	}
	$displayArray[$dap][$dai]['value'] .= ">".htmlspecialchars($value)."</option>";
}
$displayArray[$dap][$dai]['value'] .= '</select>';
$monthArray = get_monthArray();
$end_year = date('Y');
$end_month = date('m');
/*$result = $DRW->query("SELECT MIN(addedToDatabase) FROM cscan_product_detail WHERE productStatus=1 AND addedToDatabase>'1900-01-01 00:00:00'",$DRW_read);
$data = $DRW->fetch_row($result);
if($data[0]!='') {
	$start_month = (int)substr($data[0],5,2);
	$start_year = (int)substr($data[0],0,4);
}
else {*/
	$start_month = 1;
	$start_year = 2002;
//}
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
			//$GLOBALS['displayArray'][$GLOBALS['dap']][$GLOBALS['dai']]['value'] .= "<option value=\"$value\"";
			if($month<10) {
				$m = '0'.$month;
			}
			else {
				$m = $month ;
			}
			$value = $year."-".$m;
			
			$GLOBALS['displayArray'][$GLOBALS['dap']][$GLOBALS['dai']]['value'] .= "<option value=\"$value\"";
			if($month_flag == $value) {
				$GLOBALS['displayArray'][$GLOBALS['dap']][$GLOBALS['dai']]['value'] .= " selected=\"selected\"";
			}
			$GLOBALS['displayArray'][$GLOBALS['dap']][$GLOBALS['dai']]['value'] .= ">".$monthArray[$month]."  ".$year."</option>";
		}
		//$start_month = 1;
	}
}
$displayArray[$dap][$dai]['value'] .= '<div style="margin-top:4px;">Between <select name="month1" class="combo_box" id="month1" style="width:130px;" onchange="validateMonth();"'.$monthdis.'><option value="">Select Month</option>';
generate_month($monthArray,$end_month,$end_year,$start_month,$start_year,$month1);
$displayArray[$dap][$dai]['value'] .= '</select> and <select name="month2" class="combo_box" id="month2" style="width:130px;" onchange="validateMonth();"'.$monthdis.'><option value="">Select Month</option>';
generate_month($monthArray,$end_month,$end_year,$start_month,$start_year,$month2);
$displayArray[$dap][$dai]['value'] .= '</select></div>';
if($_SESSION['sess_plevel']==1 || $_SESSION['sess_plevel']==2){
	/*$displayArray[$dap][$dai]['value'] .= '<div style="margin-top:4px;"><label><input type="checkbox" id="search_panelist_date" name="search_panelist_date" value="1"';
	if($search_panelist_date==1) {
		$displayArray[$dap][$dai]['value'] .= ' checked="checked"';
	}
	$displayArray[$dap][$dai]['value'] .= ' />Include Copies</label></div>';*/
	if($approved_date=='0000-00-00' || $approved_date=='0000-00-00 00:00:00'){
		$approved_date = '';
		$approved_time = '';
	}
	else{
		list($approved_date,$approved_time) = explode(' ',$approved_date);
	}
	if($approved_date_to=='0000-00-00' || $approved_date_to=='0000-00-00 00:00:00'){
		$approved_date_to = '';
		$approved_time_to = '';
	}
	else{
		list($approved_date_to,$approved_time_to) = explode(' ',$approved_date_to);
	}
        // Changes for approved date to select between date range
        //if(strstr($currentpage_url,'localhost') || strstr($currentpage_url,'demo.competiscan.com')){
            $displayArray[$dap][$dai]['value'] .= '<div style="margin-top:4px;padding:2px 0px;font-style:italic;background-color:#E8E8FF;">Approved Date:  From <input placeholder="YYYY-MM-DD" type="text" name="approved_date" id="approved_date" size="15" maxlength="20" class="input_box" value="'.htmlspecialchars($approved_date, ENT_QUOTES).'" /> <a href="#" onclick="displayCalendar(document.searchForm.approved_date,\'yyyy-mm-dd\',this); return false;"><img name="popcal3" src="js_calendar/images/getcal.gif" border="0" alt="" style="vertical-align:bottom;" /></a>&nbsp;&nbsp;';
            $displayArray[$dap][$dai]['value'] .= 'To <input placeholder="YYYY-MM-DD" type="text" name="approved_date_to" id="approved_date_to" size="15" maxlength="20" class="input_box" value="'.htmlspecialchars($approved_date_to, ENT_QUOTES).'" /> <a href="#" onclick="displayCalendar(document.searchForm.approved_date_to,\'yyyy-mm-dd\',this); return false;"><img name="popcal3" src="js_calendar/images/getcal.gif" border="0" alt="" style="vertical-align:bottom;" /></a>';
       // }else{          
        
           // $displayArray[$dap][$dai]['value'] .= '<div style="margin-top:4px;padding:2px 0px;font-style:italic;background-color:#E8E8FF;">Approved Date <input type="text" name="approved_date" id="approved_date" size="15" maxlength="20" class="input_box" value="'.htmlspecialchars($approved_date, ENT_QUOTES).'" /> <a href="#" onclick="displayCalendar(document.searchForm.approved_date,\'yyyy-mm-dd\',this); return false;"><img name="popcal3" src="js_calendar/images/getcal.gif" border="0" alt="" style="vertical-align:bottom;" /></a> (YYYY-MM-DD)';
       // }
	$displayArray[$dap][$dai]['value'] .= '<br />Between <select name="approved_time" class="combo_box" id="approved_time" style="width:130px;"><option value="">&nbsp;</option>';
	$timeArray = getTimes();
	foreach($timeArray as $tk=>$time){
		$displayArray[$dap][$dai]['value'] .= "<option value=\"$tk\"";
		if($approved_time==$tk) {
			$displayArray[$dap][$dai]['value'] .= ' selected="selected"';
		}
		$displayArray[$dap][$dai]['value'] .= ">$time</option>";
	}
	$displayArray[$dap][$dai]['value'] .= '</select> and <select name="approved_time_to" class="combo_box" id="approved_time_to" style="width:130px;"><option value="">&nbsp;</option>';
	foreach($timeArray as $tk=>$time){
		$displayArray[$dap][$dai]['value'] .= "<option value=\"$tk\"";
		if($approved_time_to==$tk) {
			$displayArray[$dap][$dai]['value'] .= ' selected="selected"';
		}
		$displayArray[$dap][$dai]['value'] .= ">$time</option>";
	}
	$displayArray[$dap][$dai]['value'] .= '</select></div>';
}
$displayArray[$dap][$dai]['value'] .= '<div style="margin-top:4px; padding: 7px 8px 2px; position: relative; left: -7px;font-style:italic;background-color:#E8E8FF;"><table border="0" cellpadding="0" cellspacing="0"><tr><td>Panelist ID &nbsp;</td><td><input type="text" name="search_competi_id" id="search_competi_id" size="35" class="input_box" value="'.htmlspecialchars($search_competi_id, ENT_QUOTES).'" /></td></tr>';
if($_SESSION['sess_plevel']==1 || $_SESSION['sess_plevel']==2){
	$displayArray[$dap][$dai]['value'] .= '<tr><td>&nbsp;</td><td><label><input type="radio" name="spanelist_filter" value="1"';
	if($spanelist_filter=='1') {
		$displayArray[$dap][$dai]['value'] .= ' checked="checked"';
	}
	$displayArray[$dap][$dai]['value'] .= ' />Primary</label> &nbsp; <label><input type="radio" name="spanelist_filter" value="2"';
	if($spanelist_filter=='0') {
		$displayArray[$dap][$dai]['value'] .= ' checked="checked"';
	}
	$displayArray[$dap][$dai]['value'] .= ' />Non</label> &nbsp; <label><input type="radio" name="spanelist_filter" value=""';
	if($spanelist_filter=='') {
		$displayArray[$dap][$dai]['value'] .= ' checked="checked"';
	}
	$displayArray[$dap][$dai]['value'] .= ' />All</label></td></tr>';
}

####################### for add country permission     #########################
    $countrydataArray = array('US'=>'US Only','CA'=>'Canada Only','BOTH'=>'US and Canada Both');
    $sql = "SELECT DISTINCT country_id FROM cscan_country_users_allow where userID='".$_SESSION['sess_userID']."'";
    $rs  = $DRW->query( $sql,$DRW_read );
    
    $row = $DRW->fetch_array($rs);
    if(empty($row)){
        $row[0] =   'BOTH';
    }
    if($row[0]!='BOTH'){
    $displayArray[$dap][$dai]['value'] .= '<tr><td>Country &nbsp;</td><td><label>';
    $displayArray[$dap][$dai]['value'] .= ' &nbsp; <label><input type="radio" name="pcountry" value="'.$row[0].'"';
        foreach( $countrydataArray as $cid=>$cname ) {
            if(($row[0]==$cid)) {
                $displayArray[$dap][$dai]['value'] .= " checked=\"checked\"";
                 $displayArray[$dap][$dai]['value'] .= ' />'.htmlspecialchars($cname).'</label>';
            }
        }
    }else{
        $displayArray[$dap][$dai]['value'] .= '<tr><td>Country &nbsp;</td><td><label><input type="radio" name="pcountry" value=""';
	if(empty($pcountry)) {
		$displayArray[$dap][$dai]['value'] .= " checked=\"checked\"";
	}
	$displayArray[$dap][$dai]['value'] .= ' />All</label>';
	$sql = "SELECT DISTINCT countryCode,country FROM cscan_state JOIN ISO31661_alpha2code ON (code=countryCode) ORDER BY country";
	$rs = $DRW->query( $sql,$DRW_read );
	while($row = $DRW->fetch_row($rs) ) {
		$displayArray[$dap][$dai]['value'] .= ' &nbsp; <label><input type="radio" name="pcountry" value="'.$row[0].'"';
		if($pcountry==$row[0]) {
			$displayArray[$dap][$dai]['value'] .= " checked=\"checked\"";
		}
		$displayArray[$dap][$dai]['value'] .= ' />'.htmlspecialchars($row[1]).'</label>';
	}
	$displayArray[$dap][$dai]['value'] .= '</td></tr>';
    
    }
 ###################### end for add country permission   ########################

//if(!in_array('canada',$_SESSION['sess_search_exclude'])){
//	$displayArray[$dap][$dai]['value'] .= '<tr><td>Country &nbsp;</td><td><label><input type="radio" name="pcountry" value=""';
//	if(empty($pcountry)) {
//		$displayArray[$dap][$dai]['value'] .= " checked=\"checked\"";
//	}
//	$displayArray[$dap][$dai]['value'] .= ' />All</label>';
//	$sql = "SELECT DISTINCT countryCode,country FROM cscan_state JOIN ISO31661_alpha2code ON (code=countryCode) ORDER BY country";
//	$rs = $DRW->query( $sql,$DRW_read );
//	while($row = $DRW->fetch_row($rs) ) {
//		$displayArray[$dap][$dai]['value'] .= ' &nbsp; <label><input type="radio" name="pcountry" value="'.$row[0].'"';
//		if($pcountry==$row[0]) {
//			$displayArray[$dap][$dai]['value'] .= " checked=\"checked\"";
//		}
//		$displayArray[$dap][$dai]['value'] .= ' />'.htmlspecialchars($row[1]).'</label>';
//	}
//	$displayArray[$dap][$dai]['value'] .= '</td></tr>';
//}
/*if(!in_array('citi',$_SESSION['sess_search_exclude'])){
	$displayArray[$dap][$dai]['value'] .= '<tr><td>Panelist Filter &nbsp;</td><td><select name="cg_id" class="combo_box" style="width:230px;"><option value="">All</option>';
	$query_ac ="select cg_id,cg_name from cscan_competi_group order by cg_sort";
	$result_ac = $DRW->query($query_ac,$DRW_read);
	while($row_ac = $DRW->fetch_row($result_ac)){
		$selvalue = $row_ac[0];
		$seltext = $row_ac[1];
		
		$displayArray[$dap][$dai]['value'] .= "<option value=\"$selvalue\"";
		if($selvalue==$cg_id) {
			$displayArray[$dap][$dai]['value'] .= " selected=\"selected\"";
		}
		$displayArray[$dap][$dai]['value'] .= ">".htmlspecialchars($seltext)."</option>";
	}
	$displayArray[$dap][$dai]['value'] .= '</select></td></tr>';
}*/
$displayArray[$dap][$dai]['value'] .= '</table></div>';
$dai++;

$displayArray[$dap][$dai]['show'] = true;
$displayArray[$dap][$dai]['title'] = 'Company :<br />[<a href="CompanySearchTips.html" class="HyperLink" name="enh" onclick="showHelp2(\'CompanySearchTips.html\'); return false;">Company Search Tips</a>]';
$displayArray[$dap][$dai]['value'] = '<div id="cotext"><input type="text" name="company" size="45" class="input_box" value="'.htmlspecialchars($company, ENT_QUOTES).'" onchange="checkLookup(\'clist\');" /><br />
[<a href="#" onclick="showLook(\'seltext\',\'showhide\',\'clist\',document.forms.searchForm.company); return false;" id="showhide" class="HyperLink">Show Lookup</a>]</div>
<div id="seltext" style="border:solid 1px #000000;padding:4px;display:none;float:left;background-color:#E8E8FF;"><iframe name="clist" src="company_iframe.php?parent_field=company" width="290" height="100" frameborder="0" marginheight="0" marginwidth="0" scrolling="auto"></iframe></div>';
$dai++;
$javascript .= "enhArray[2]['{$dap}_{$dai}'] = '90';\n";
if(in_array(90,$sectorID)){
	$displayArray[$dap][$dai]['show'] = true;
}
else{
	$displayArray[$dap][$dai]['show'] = false;
}
$displayArray[$dap][$dai]['title'] = 'Product Name :';
$displayArray[$dap][$dai]['value'] = '<div id="prntext"><input type="text" name="productName" size="45" class="input_box" value="'.htmlspecialchars($productName, ENT_QUOTES).'" onchange="checkLookup(\'prnlist\');" /><br />
[<a href="#" onclick="showLook(\'seltext3\',\'showhide3\',\'prnlist\',document.forms.searchForm.productName); return false;" id="showhide3" class="HyperLink">Show Lookup</a>]</div>
<div id="seltext3" style="border:solid 1px #000000;padding:4px;display:none;float:left;background-color:#E8E8FF;"><iframe name="prnlist" src="company_iframe.php?parent_field=productName" width="290" height="100" frameborder="0" marginheight="0" marginwidth="0" scrolling="auto"></iframe></div>';
$dai++;
###########################Start SENDAR DOMAIN NAME#########################
if(in_array('sender_domain_permission',$_SESSION['sess_sender_domain'])){
$javascript .= "enhArray[1]['{$dap}_{$dai}'] = '3';\n";
if(in_array(3,$mChannelID)){
	$displayArray[$dap][$dai]['show'] = true;
}
else{
	$displayArray[$dap][$dai]['show'] = false;
}
//$displayArray[$dap][$dai]['show'] = true;
$displayArray[$dap][$dai]['title'] = 'Sender Domain Name :';
$displayArray[$dap][$dai]['value'] = '<div id="domaintext">

<input type="text" name="domainName" size="45" class="input_box" value="'.htmlspecialchars(is_array($sender_domain_name), ENT_QUOTES).'" onchange="checkLookup(\'domainlist\');" /><br />
[<a href="#" onclick="showLook(\'seltext4\',\'showhide4\',\'domainlist\',document.forms.searchForm.domainName); return false;" id="showhide4" class="HyperLink">Show Lookup</a>]</div>
<div id="seltext4" style="border:solid 1px #000000;padding:4px;display:none;float:left;background-color:#E8E8FF;"><iframe name="domainlist" src="sender_domain_iframe.php?parent_field=domainName" width="290" height="100" frameborder="0" marginheight="0" marginwidth="0" scrolling="auto"></iframe>
</div>';
$dai++;
}
###########################END SENDAR DOMAIN NAME#########################
if(!in_array('affinity_association',$_SESSION['sess_search_exclude'])){
	$displayArray[$dap][$dai]['show'] = true;
	$displayArray[$dap][$dai]['title'] = 'Affinity/Association :';
	$displayArray[$dap][$dai]['value'] = '<div id="afastext"><input type="text" name="affinity_association" size="45" class="input_box" value="'.htmlspecialchars($affinity_association, ENT_QUOTES).'" onchange="checkLookup(\'afaslist\');" /><br />
	[<a href="#" onclick="showLook(\'seltext2\',\'showhide2\',\'afaslist\',document.forms.searchForm.affinity_association); return false;" id="showhide2" class="HyperLink">Show Lookup</a>]</div>
	<div id="seltext2" style="border:solid 1px #000000;padding:4px;display:none;float:left;background-color:#E8E8FF;"><iframe name="afaslist" src="company_iframe.php?parent_field=affinity_association" width="290" height="100" frameborder="0" marginheight="0" marginwidth="0" scrolling="auto"></iframe></div>';
	$dai++;
	$displayArray[$dap][$dai]['show'] = true;
	$displayArray[$dap][$dai]['title'] = 'Related :';
	$displayArray[$dap][$dai]['value'] = '<label><input type="checkbox" id="ca_related" name="ca_related" value="1"';
	if($ca_related>0) {
		$displayArray[$dap][$dai]['value'] .= ' checked="checked"';
	}
	$displayArray[$dap][$dai]['value'] .= ' />Affinities, Loyalty/Retention, Statement Companies</label>';
	$dai++;
}

$dap++;
$dai = 0;

//$categoryOption
$displayArray[$dap][$dai]['show'] = true;
$displayArray[$dap][$dai]['title'] = 'Category :';
$displayArray[$dap][$dai]['value'] = '<select id="categoryID" name = "categoryID[]" class="combo_box" multiple="multiple" size ="3" onchange="getSubCat();getDeps(\'offerOrigin\');getDeps(\'agentCommunicationID[]\');getDeps(\'responseMechID_mult[]\');getDeps(\'riders_mult[]\');getDeps(\'multiculturalmarkets_mult[]\');">';
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

//$subCategoryOption $categoryOption
$displayArray[$dap][$dai]['show'] = true;
$displayArray[$dap][$dai]['title'] = 'Sub Category :';
$displayArray[$dap][$dai]['value'] = '<select id="subCategoryID" name = "subCategoryID[]" class="combo_box" size ="3" multiple="multiple" onchange="getSubSubCat();getDeps(\'offerOrigin\');getDeps(\'agentCommunicationID[]\');getDeps(\'responseMechID_mult[]\');getDeps(\'riders_mult[]\');getDeps(\'multiculturalmarkets_mult[]\');">';
$displayArray[$dap][$dai]['value'] .= "<option value=\"\"";
$o_count = count($subCategoryID);
if($o_count==0 || ($o_count==1 && $subCategoryID[0]=='')) {
	$displayArray[$dap][$dai]['value'] .= " selected=\"selected\"";
}
$displayArray[$dap][$dai]['value'] .= ">Any</option>";
ksort($sctextArray);
$displayArray[$dap][$dai]['value'] .= implode('',$sctextArray);
$displayArray[$dap][$dai]['value'] .= '</select><br />[Hold ctrl key for multiple selection]';
$displayArray[$dap][$dai]['value'] .= '<br /><label><input type="checkbox" name="scsc_primary" value="1"';
if($scsc_primary){
	$displayArray[$dap][$dai]['value'] .= ' checked="checked"';
}
$displayArray[$dap][$dai]['value'] .= ' />Primary</label>';
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
$javascript .= "enhArray[2]['{$dap}_{$dai}'] = '90';\n";
if(in_array(90,$sectorID)){
	$displayArray[$dap][$dai]['show'] = true;
}
else{
	$displayArray[$dap][$dai]['show'] = false;
}
$displayArray[$dap][$dai]['title'] = 'Reloadable :';
$displayArray[$dap][$dai]['value'] = '<input type="checkbox" id="is_Reloadable" name="is_Reloadable" value="1"';
if($is_Reloadable>0) {
	$displayArray[$dap][$dai]['value'] .= ' checked="checked"';
}
$displayArray[$dap][$dai]['value'] .= ' />';
$dai++;
$javascript .= "enhArray[2]['{$dap}_{$dai}'] = '90,6';\n";
if(in_array(90,$sectorID) || in_array(6,$sectorID)){
	$displayArray[$dap][$dai]['show'] = true;
}
else{
	$displayArray[$dap][$dai]['show'] = false;
}
$displayArray[$dap][$dai]['title'] = 'Application Type :';
$displayArray[$dap][$dai]['value'] = '<select name="ApplicationType_mult[]" multiple="multiple" size="3" class="combo_box">';
$displayArray[$dap][$dai]['value'] .= "<option value=\"\"";
$o_count = count($ApplicationType_mult);
if($o_count==0 || ($o_count==1 && $ApplicationType_mult[0]=='')) {
	$displayArray[$dap][$dai]['value'] .= " selected=\"selected\"";
}
$displayArray[$dap][$dai]['value'] .= ">Any</option>";
$query_ac ="SELECT ApplicationTypeID,ApplicationTypeName FROM cscan_application_type ORDER BY ApplicationTypeSort";
$result_ac = $DRW->query($query_ac,$DRW_read);
while($row_ac = $DRW->fetch_row($result_ac)){
	$id = $row_ac[0];
	$name = $row_ac[1];
	$displayArray[$dap][$dai]['value'] .= "<option value = \"$id\"";
	if(in_array($id,$ApplicationType_mult)) {
		$displayArray[$dap][$dai]['value'] .= " selected=\"selected\"";
	}
	$displayArray[$dap][$dai]['value'] .= ">".htmlspecialchars($name)."</option>";
}
$displayArray[$dap][$dai]['value'] .= '</select><br />[Hold ctrl key for multiple selection]';
$dai++;
$javascript .= "enhArray[2]['{$dap}_{$dai}'] = '90';\n";
if(in_array(90,$sectorID)){
	$displayArray[$dap][$dai]['show'] = true;
}
else{
	$displayArray[$dap][$dai]['show'] = false;
}
$displayArray[$dap][$dai]['title'] = 'Introductory Pricing :';
$displayArray[$dap][$dai]['value'] = '<select name="IntroPricing_mult[]" multiple="multiple" size="3" class="combo_box">';
$selArray = get_IntroductoryPricingArray();
$displayArray[$dap][$dai]['value'] .= "<option value=\"\"";
$o_count = count($IntroPricing_mult);
if($o_count==0 || ($o_count==1 && $IntroPricing_mult[0]=='')) {
	$displayArray[$dap][$dai]['value'] .= " selected=\"selected\"";
}
$displayArray[$dap][$dai]['value'] .= ">Any</option>";
foreach($selArray as $selvalue=>$seltext){
	$displayArray[$dap][$dai]['value'] .= "<option value=\"$selvalue\"";
	if(in_array($selvalue,$IntroPricing_mult)) {
		$displayArray[$dap][$dai]['value'] .= " selected=\"selected\"";
	}
	$displayArray[$dap][$dai]['value'] .= ">".htmlspecialchars($seltext)."</option>";
}
$displayArray[$dap][$dai]['value'] .= '</select><br />[Hold ctrl key for multiple selection]';
$dai++;
if(!in_array('IssueType',$_SESSION['sess_search_exclude'])){
	$javascript .= "enhArray[2]['{$dap}_{$dai}'] = '4';\n";
	if(in_array(4,$sectorID)){
		$displayArray[$dap][$dai]['show'] = true;
	}
	else{
		$displayArray[$dap][$dai]['show'] = false;
	}
	$displayArray[$dap][$dai]['title'] = 'Issue Type :';
	$displayArray[$dap][$dai]['value'] = '<select name="IssueTypeID_mult[]" multiple="multiple" size="3" class="combo_box">';
	$displayArray[$dap][$dai]['value'] .= "<option value=\"\"";
	$o_count = count($IssueTypeID_mult);
	if($o_count==0 || ($o_count==1 && $IssueTypeID_mult[0]=='')) {
		$displayArray[$dap][$dai]['value'] .= " selected=\"selected\"";
	}
	$displayArray[$dap][$dai]['value'] .= ">Any</option>";
	$query_ac ="SELECT IssueTypeID,IssueTypeName FROM cscan_issue_type ORDER BY IssueTypeSort";
	$result_ac = $DRW->query($query_ac,$DRW_read);
	while($row_ac = $DRW->fetch_row($result_ac)){
		$id = $row_ac[0];
		$name = $row_ac[1];
		if(in_array(13,$categoryID)){//Life
			$displayArray[$dap][$dai]['value'] .= "<option value = \"$id\"";
			if(in_array($id,$IssueTypeID_mult)) {
				$displayArray[$dap][$dai]['value'] .= " selected=\"selected\"";
			}
			$displayArray[$dap][$dai]['value'] .= ">".htmlspecialchars($name)."</option>";
		}
		$javascript .= "issueArrayID[issueArrayID.length] = '$id';\nissueArray[issueArray.length] = '".singleQuoteSafe($name)."';\n";
	}
	$displayArray[$dap][$dai]['value'] .= '</select>';
	$dai++;
}
//$mailingTypeOption
$javascript .= "enhArray[3]['{$dap}_{$dai}'] = '1,2";
if($_SESSION['sess_plevel']==1 || $_SESSION['sess_plevel']==2){
	$javascript .= ",4";
}
$javascript .= "';\n";
if(in_array(1,$mPanelID) || in_array(2,$mPanelID) || (in_array(4,$mPanelID) && ($_SESSION['sess_plevel']==1 || $_SESSION['sess_plevel']==2))){
	$displayArray[$dap][$dai]['show'] = true;
}
else{
	$displayArray[$dap][$dai]['show'] = false;
}
$displayArray[$dap][$dai]['title'] = 'Mailing Type :';
$displayArray[$dap][$dai]['value'] = '<select name="mTypeID[]" multiple="multiple" size="3" class="combo_box">';
$displayArray[$dap][$dai]['value'] .= "<option value=\"\"";
$o_count = count($mTypeIDval);
if($o_count==0 || ($o_count==1 && $mTypeIDval[0]=='')) {
	$displayArray[$dap][$dai]['value'] .= " selected=\"selected\"";
}
$displayArray[$dap][$dai]['value'] .= ">Any</option>";
$mailingType = getMailingType();
foreach($mailingType as $id=>$name) {
	$displayArray[$dap][$dai]['value'] .= "<option value=\"$id\"";
	if(in_array($id,$mTypeIDval)) {
		$displayArray[$dap][$dai]['value'] .= " selected=\"selected\"";
	}
	$displayArray[$dap][$dai]['value'] .= ">".htmlspecialchars($name)."</option>";
}
$displayArray[$dap][$dai]['value'] .= '</select><br />[Hold ctrl key for multiple selection]';
if($_SESSION['sess_plevel']==1 || $_SESSION['sess_plevel']==2){
	$displayArray[$dap][$dai]['value'] .= '<br />'.addSearchRules('mTypeID',true);
}
$dai++;

//$personalizationOption
$javascript .= "enhArray[3]['{$dap}_{$dai}'] = '1,2,4,6,5,9';\n";
if((in_array(1,$mPanelID)) || (in_array(2,$mPanelID)) || (in_array(4,$mPanelID) || (in_array(6,$mPanelID)) || (in_array(5,$mPanelID)) || (in_array(9,$mPanelID)))){
	$displayArray[$dap][$dai]['show'] = true;
}
else{
	$displayArray[$dap][$dai]['show'] = false;
}
$displayArray[$dap][$dai]['title'] = 'Personalization :';
$displayArray[$dap][$dai]['value'] = '<label><input type="radio" name="personalization" value="1"';
if($personalizationval=='1') {
	$displayArray[$dap][$dai]['value'] .= ' checked="checked"';
}
$displayArray[$dap][$dai]['value'] .= ' />Personalized</label> &nbsp; <label><input type="radio" name="personalization" value="2"';
if($personalizationval=='2') {
	$displayArray[$dap][$dai]['value'] .= ' checked="checked"';
}
$displayArray[$dap][$dai]['value'] .= ' />Non-Personalized</label> &nbsp; <label><input type="radio" name="personalization" value=""';
if($personalizationval=='') {
	$displayArray[$dap][$dai]['value'] .= ' checked="checked"';
}
$displayArray[$dap][$dai]['value'] .= ' />Both</label>';
$dai++;

if(!in_array('businessContent',$_SESSION['sess_search_exclude'])){
	$javascript .= "enhArray[3]['{$dap}_{$dai}'] = '1,2';\n";
	if(in_array(1,$mPanelID) || in_array(2,$mPanelID)){
		$displayArray[$dap][$dai]['show'] = true;
	}
	else{
		$displayArray[$dap][$dai]['show'] = false;
	}
	$displayArray[$dap][$dai]['title'] = 'Business Content :';
	$displayArray[$dap][$dai]['value'] = '<select name="businessContent[]" class="combo_box" multiple="multiple" size="3">';
	$displayArray[$dap][$dai]['value'] .= "<option value=\"\"";
	$o_count = count($businessContent);
	if($o_count==0 || ($o_count==1 && $businessContent[0]=='')) {
		$displayArray[$dap][$dai]['value'] .= " selected=\"selected\"";
	}
	$displayArray[$dap][$dai]['value'] .= ">Any</option>";
	$bcArray = get_businessContentArray();
	foreach($bcArray as $selvalue=>$seltext){
		$displayArray[$dap][$dai]['value'] .= "<option value=\"$selvalue\"";
		if(in_array((string)$selvalue,$businessContent,true)) {
			$displayArray[$dap][$dai]['value'] .= " selected=\"selected\"";
		}
		$displayArray[$dap][$dai]['value'] .= ">".htmlspecialchars($seltext)."</option>";
	}   
	$displayArray[$dap][$dai]['value'] .= '</select><br />[Hold ctrl key for multiple selection]';
	if($_SESSION['sess_plevel']==1 || $_SESSION['sess_plevel']==2){
		//$displayArray[$dap][$dai]['value'] .= '<br />'.addSearchRules('businessContent',true);
	}
	$dai++;
}

if(!in_array('DMA_CODE',$_SESSION['sess_search_exclude'])){
	$javascript .= "enhArray[3]['{$dap}_{$dai}'] = '1,2';\n";
	if((in_array(1,$mPanelID) || in_array(2,$mPanelID))){
		$displayArray[$dap][$dai]['show'] = true;
	}
	else{
		$displayArray[$dap][$dai]['show'] = false;
	}
	$displayArray[$dap][$dai]['title'] = 'DMA&reg; :';
	$displayArray[$dap][$dai]['value'] = '<select name="DMA_ID[]" class="combo_box" multiple="multiple" size="3">';
	$displayArray[$dap][$dai]['value'] .= "<option value=\"\"";
	$o_count = count($DMA_ID);
	if($o_count==0 || ($o_count==1 && $DMA_ID[0]=='')) {
		$displayArray[$dap][$dai]['value'] .= " selected=\"selected\"";
	}
	$displayArray[$dap][$dai]['value'] .= ">Any</option>";
	$javascript .= "\n";
	$dmacodeArray = array();
	$query_ac ="select code,description from cscan_dma_code ORDER BY code";
	$result_ac = $DRW->query($query_ac,$DRW_read);
	while($row_ac = $DRW->fetch_row($result_ac)){
		$selvalue = $row_ac[0];
		$seltext = $row_ac[1];
		$selshow = $selvalue.' - '.$seltext;
		$dmacodeArray[$selvalue] = $seltext;
		$javascript .= "dmaCs['$selvalue'] = '".singleQuoteSafe($selvalue.' - '.$seltext)."';\n";
		$javascript .= "dmaNs['$selvalue'] = '".singleQuoteSafe($seltext.' - '.$selvalue)."';\n";
		$displayArray[$dap][$dai]['value'] .= "<option value=\"$selvalue\"";
		if(in_array($selvalue,$DMA_ID)) {
			$displayArray[$dap][$dai]['value'] .= " selected=\"selected\"";
		}
		$displayArray[$dap][$dai]['value'] .= ">".htmlspecialchars($selvalue.' - '.$seltext)."</option>";
	}   
	$displayArray[$dap][$dai]['value'] .= '</select><br />[Hold ctrl key for multiple selection] &nbsp; <a href="#" onclick="sortDMA(); return false;" class="HyperLink">sort</a>';
	$javascript .= "dmaCodes = new Array('".implode("','",array_keys($dmacodeArray))."');\n";
	asort($dmacodeArray);
	$javascript .= "dmaNames = new Array('".implode("','",array_keys($dmacodeArray))."');\n";
	$dai++;
}

$javascript .= "enhArray[2]['{$dap}_{$dai}'] = '315';\n";//enhArray[3]['{$dap}_{$dai}'] = '1,2';
if(in_array(315,$sectorID)){//(in_array(1,$mPanelID) || in_array(2,$mPanelID)) && 
	$displayArray[$dap][$dai]['show'] = true;
}
else{
	$displayArray[$dap][$dai]['show'] = false;
}
$displayArray[$dap][$dai]['title'] = 'EDC / LDC / TDSP :';
$displayArray[$dap][$dai]['value'] = '<select name="edc_id[]" class="combo_box" multiple="multiple" size="3">';
$displayArray[$dap][$dai]['value'] .= "<option value=\"\"";
$o_count = count($edc_id);
if($o_count==0 || ($o_count==1 && $edc_id[0]=='')) {
	$displayArray[$dap][$dai]['value'] .= " selected=\"selected\"";
}
$displayArray[$dap][$dai]['value'] .= ">Any</option>";
$query_ac ="select edc_id,edc_name from cscan_edc WHERE edc_panelists<>0 ORDER BY edc_name";//DISTINCT cscan_edc.edc_id JOIN cscan_edc_postalcode ON(cscan_edc.edc_id=cscan_edc_postalcode.edc_id)
$result_ac = $DRW->query($query_ac,$DRW_read);
while($row_ac = $DRW->fetch_row($result_ac)){
	$selvalue = $row_ac[0];
	$seltext = $row_ac[1];
	$displayArray[$dap][$dai]['value'] .= "<option value=\"$selvalue\"";
	if(in_array($selvalue,$edc_id)) {
		$displayArray[$dap][$dai]['value'] .= " selected=\"selected\"";
	}
	$displayArray[$dap][$dai]['value'] .= ">".htmlspecialchars($seltext)."</option>";
}   
$displayArray[$dap][$dai]['value'] .= '</select><br />[Hold ctrl key for multiple selection]';
$dai++;

//$genderOption
/*$javascript .= "enhArray[3]['{$dap}_{$dai}'] = '1,2';\n";
if((in_array(1,$mPanelID) || in_array(2,$mPanelID))){
	$displayArray[$dap][$dai]['show'] = true;
}
else{
	$displayArray[$dap][$dai]['show'] = false;
}
$displayArray[$dap][$dai]['title'] = 'Gender :';
$displayArray[$dap][$dai]['value'] = '<label><input type="radio" name="gender" value="M"';
if($genderval=='M') {
	$displayArray[$dap][$dai]['value'] .= ' checked="checked"';
}
$displayArray[$dap][$dai]['value'] .= ' />Male</label> &nbsp; <label><input type="radio" name="gender" value="F"';
if($genderval=='F') {
	$displayArray[$dap][$dai]['value'] .= ' checked="checked"';
}
$displayArray[$dap][$dai]['value'] .= ' />Female</label> &nbsp; <label><input type="radio" name="gender" value=""';
if($genderval=='') {
	$displayArray[$dap][$dai]['value'] .= ' checked="checked"';
}
$displayArray[$dap][$dai]['value'] .= ' />Both</label>';
$dai++;*/

//$stateOption
$displayArray[$dap][$dai]['show'] = true;
$displayArray[$dap][$dai]['title'] = 'State/Province :';
$displayArray[$dap][$dai]['value'] = '<select name="state[]" multiple="multiple" size="3" class="combo_box">';
$displayArray[$dap][$dai]['value'] .= "<option value=\"\"";
$o_count = count($state);
if($o_count==0 || ($o_count==1 && $state[0]=='')) {
	$displayArray[$dap][$dai]['value'] .= " selected=\"selected\"";
}
$displayArray[$dap][$dai]['value'] .= ">Any</option>";
$selstates = implode(',',$state);
ob_start();
$countries = array('US');
if(!in_array('canada',$_SESSION['sess_search_exclude'])){
	$countries[] = 'CA';
}
getStates($selstates,false,$countries);
$displayArray[$dap][$dai]['value'] .= ob_get_clean();
$displayArray[$dap][$dai]['value'] .= '</select><br />[Hold ctrl key for multiple selection]';
$dai++;

//$ageOption
$javascript .= "enhArray[3]['{$dap}_{$dai}'] = '1,2';\n";
if((in_array(1,$mPanelID) || in_array(2,$mPanelID))){
	$displayArray[$dap][$dai]['show'] = true;
}
else{
	$displayArray[$dap][$dai]['show'] = false;
}

if (!is21FilterOn()) {
    $displayArray[$dap][$dai]['title'] = 'Age :';
    $displayArray[$dap][$dai]['value'] = '<select name="age[]" class="combo_box" multiple="multiple" size="3">';
    $selArray = getAgeTypes(false);
    $displayArray[$dap][$dai]['value'] .= "<option value=\"\"";
    $o_count = count($ageval);
    if ($o_count == 0 || ($o_count == 1 && $ageval[0] == '')) {
        $displayArray[$dap][$dai]['value'] .= " selected=\"selected\"";
    }
    $displayArray[$dap][$dai]['value'] .= ">Any</option>";
    foreach ($selArray as $selvalue => $seltext) {
        $displayArray[$dap][$dai]['value'] .= "<option value=\"$selvalue\"";
        if (in_array($selvalue, $ageval)) {
            $displayArray[$dap][$dai]['value'] .= " selected=\"selected\"";
        }
        $displayArray[$dap][$dai]['value'] .= ">" . htmlspecialchars($seltext) . "</option>";
    }
    $displayArray[$dap][$dai]['value'] .= '</select><br />[Hold ctrl key for multiple selection]';
    $dai++;

//$incomeOption
    $javascript .= "enhArray[3]['{$dap}_{$dai}'] = '1,2';\n";
    if ((in_array(1, $mPanelID) || in_array(2, $mPanelID))) {
        $displayArray[$dap][$dai]['show'] = true;
    } else {
        $displayArray[$dap][$dai]['show'] = false;
    }
    $displayArray[$dap][$dai]['title'] = 'Income :';
    $displayArray[$dap][$dai]['value'] = '<select name="income[]" multiple="multiple" size="3" class="combo_box">';
    $selArray = getIncomeTypes();
    $displayArray[$dap][$dai]['value'] .= "<option value=\"\"";
    $o_count = count($incomeval);
    if ($o_count == 0 || ($o_count == 1 && $incomeval[0] == '')) {
        $displayArray[$dap][$dai]['value'] .= " selected=\"selected\"";
    }
    $displayArray[$dap][$dai]['value'] .= ">Any</option>";
    foreach ($selArray as $selvalue => $seltext) {
        $displayArray[$dap][$dai]['value'] .= "<option value=\"$selvalue\"";
        if (in_array($selvalue, $incomeval)) {
            $displayArray[$dap][$dai]['value'] .= " selected=\"selected\"";
        }
        $displayArray[$dap][$dai]['value'] .= ">" . htmlspecialchars($seltext) . "</option>";
    }
    $displayArray[$dap][$dai]['value'] .= '</select><br />[Hold ctrl key for multiple selection]';
    $dai++;
    if ($_SESSION['sess_plevel'] == 1 || $_SESSION['sess_plevel'] == 2) {
        $javascript .= "enhArray[3]['{$dap}_{$dai}'] = '1,2';\n";
        if ((in_array(1, $mPanelID) || in_array(2, $mPanelID))) {
            $displayArray[$dap][$dai]['show'] = true;
        } else {
            $displayArray[$dap][$dai]['show'] = false;
        }
        $displayArray[$dap][$dai]['title'] = 'Income Producing Assets Segment Code *R* :';
        $displayArray[$dap][$dai]['value'] = '<select name="Income_Producing_Assets_Segment_Code[]" class="combo_box" multiple="multiple" size="3">';
        $displayArray[$dap][$dai]['value'] .= "<option value=\"\"";
        $o_count = count($Income_Producing_Assets_Segment_Code);
        if ($o_count == 0 || ($o_count == 1 && $Income_Producing_Assets_Segment_Code[0] == '')) {
            $displayArray[$dap][$dai]['value'] .= " selected=\"selected\"";
        }
        $displayArray[$dap][$dai]['value'] .= ">Any</option>";
        $query_ac = "select code,description from cscan_income_producing_assets_segment_code order by code";
        $result_ac = $DRW->query($query_ac, $DRW_read);
        while ($row_ac = $DRW->fetch_row($result_ac)) {
            $selvalue = $row_ac[0];
            $seltext = $row_ac[1];

            $displayArray[$dap][$dai]['value'] .= "<option value=\"$selvalue\"";
            if (in_array($selvalue, $Income_Producing_Assets_Segment_Code)) {
                $displayArray[$dap][$dai]['value'] .= " selected=\"selected\"";
            }
            $displayArray[$dap][$dai]['value'] .= ">" . htmlspecialchars($seltext) . "</option>";
        }
        $displayArray[$dap][$dai]['value'] .= '</select><br />[Hold ctrl key for multiple selection]';
        $dai++;
    }
    
    
    
    /* ######### For Value Score ######### */
    
 
    
    if(!in_array('ValueScore',$_SESSION['sess_search_exclude'])){ 
        
       $javascript .= "enhArray[3]['{$dap}_{$dai}'] = '1,2';\n";
        if ((in_array(1, $mPanelID) || in_array(2, $mPanelID))) {
            $displayArray[$dap][$dai]['show'] = true;
        } else {
            $displayArray[$dap][$dai]['show'] = false;
        }
        $displayArray[$dap][$dai]['title'] = 'Value Score :';
        $displayArray[$dap][$dai]['value'] = '<select name="value_score[]" class="combo_box" multiple="multiple" size="3">';
        $displayArray[$dap][$dai]['value'] .= "<option value=\"\"";
        $o_count = count($value_score);
        if ($o_count == 0 || ($o_count == 1 && $value_score[0] == '')) {
            $displayArray[$dap][$dai]['value'] .= " selected=\"selected\"";
        }
        $displayArray[$dap][$dai]['value'] .= ">Any</option>";
        $query_ac = "select code,VSfH_average from cscan_valuescore_for_household";
        $result_ac = $DRW->query($query_ac, $DRW_read);
        while ($row_ac = $DRW->fetch_row($result_ac)) {
            $selvalue = $row_ac[0];
            $seltext = $row_ac[1];

            $displayArray[$dap][$dai]['value'] .= "<option value=\"$selvalue\"";
            if (in_array($selvalue, $value_score)) {
                $displayArray[$dap][$dai]['value'] .= " selected=\"selected\"";
            }
            $displayArray[$dap][$dai]['value'] .= ">" . htmlspecialchars($selvalue).' ('. htmlspecialchars($seltext) .')'. "</option>";
        }
        $displayArray[$dap][$dai]['value'] .= '</select><br />[Hold ctrl key for multiple selection]';
        $dai++;
    
    } 
    
/* ######### End For Value Score ######### */
    
}

/* ########## For FICO, CreditVision and Vantage Score ######### */

//print_r($_SESSION['sess_search_additional_field']); die;
if(!defined('ENV')){
    define('ENV',getenv('SERVER_NAME'));
} 

if(isset($_SESSION['sess_search_additional_field']) AND in_array('fico',$_SESSION['sess_search_additional_field'])){ 


    $displayArray[$dap][$dai]['show'] = true;

    $displayArray[$dap][$dai]['title'] = 'FICO Score :';
    $displayArray[$dap][$dai]['value'] = '<select name="fico_score[]" class="combo_box" multiple="multiple" size="3">';
    $displayArray[$dap][$dai]['value'] .= "<option value=\"\"";
    $o_count = count($fico_score);
    if ($o_count == 0 || ($o_count == 1 && $fico_score[0] == '')) {
        $displayArray[$dap][$dai]['value'] .= " selected=\"selected\"";
    }
    $displayArray[$dap][$dai]['value'] .= ">Any</option>";
    $query_score = "select id,score_range from cscan_score_range order by id desc";
        $result_score = $DRW->query($query_score, $DRW_read);
        while ($row_score = $DRW->fetch_row($result_score)) {
            $score_id = $row_score[0];
            $score_range = $row_score[1];

            $displayArray[$dap][$dai]['value'] .= "<option value=\"$score_id\"";
            if (in_array($score_id, $fico_score)) {
                $displayArray[$dap][$dai]['value'] .= " selected=\"selected\"";
            }
            $displayArray[$dap][$dai]['value'] .= ">" . htmlspecialchars($score_range)."</option>";
        }   
    
    $displayArray[$dap][$dai]['value'] .= '</select><br />[Hold ctrl key for multiple selection]';
    $dai++;
}

if(isset($_SESSION['sess_search_additional_field']) AND in_array('credit_vision',$_SESSION['sess_search_additional_field'])){ 
    $displayArray[$dap][$dai]['show'] = true;      
    $displayArray[$dap][$dai]['title'] = 'CreditVision:';
    $displayArray[$dap][$dai]['value'] = '<select name="credit_vision_score[]" class="combo_box" multiple="multiple" size="3">';
    $displayArray[$dap][$dai]['value'] .= "<option value=\"\"";
    $o_count = count($credit_vision_score);
    if ($o_count == 0 || ($o_count == 1 && $credit_vision_score[0] == '')) {
        $displayArray[$dap][$dai]['value'] .= " selected=\"selected\"";
    }
    $displayArray[$dap][$dai]['value'] .= ">Any</option>";
    $query_score = "select id,score_range from cscan_score_range order by id desc";
    $result_score = $DRW->query($query_score, $DRW_read);
    while ($row_score = $DRW->fetch_row($result_score)) {
        $score_id = $row_score[0];
        $score_range = $row_score[1];

        $displayArray[$dap][$dai]['value'] .= "<option value=\"$score_id\"";
        if (in_array($score_id, $credit_vision_score)) {
            $displayArray[$dap][$dai]['value'] .= " selected=\"selected\"";
        }
        $displayArray[$dap][$dai]['value'] .= ">" . htmlspecialchars($score_range)."</option>";
    }
    $displayArray[$dap][$dai]['value'] .= '</select><br />[Hold ctrl key for multiple selection]';
    $dai++; 
}

    
if(isset($_SESSION['sess_search_additional_field']) AND in_array('vantage_score',$_SESSION['sess_search_additional_field'])){ 
    $displayArray[$dap][$dai]['show'] = true;
    $displayArray[$dap][$dai]['title'] = 'VantageScore:';
    $displayArray[$dap][$dai]['value'] = '<select name="vantage_score[]" class="combo_box" multiple="multiple" size="3">';
    $displayArray[$dap][$dai]['value'] .= "<option value=\"\"";
    $o_count = count($vantage_score);
    if ($o_count == 0 || ($o_count == 1 && $vantage_score[0] == '')) {
        $displayArray[$dap][$dai]['value'] .= " selected=\"selected\"";
    }
    $displayArray[$dap][$dai]['value'] .= ">Any</option>";
    $query_score = "select id,score_range from cscan_score_range order by id desc";
    $result_score = $DRW->query($query_score, $DRW_read);
    while ($row_score = $DRW->fetch_row($result_score)) {
        $score_id = $row_score[0];
        $score_range = $row_score[1];

        $displayArray[$dap][$dai]['value'] .= "<option value=\"$score_id\"";
        if (in_array($score_id, $vantage_score)) {
            $displayArray[$dap][$dai]['value'] .= " selected=\"selected\"";
        }
        $displayArray[$dap][$dai]['value'] .= ">" . htmlspecialchars($score_range)."</option>";
    }
    $displayArray[$dap][$dai]['value'] .= '</select><br />[Hold ctrl key for multiple selection]';
    $dai++;

} 

    
/* ######### End For FICO, CreditVision and Vantage Score ######### */


$wstext = 'Worksite/Voluntary :';
//$worksiteOption
if($worksiteVoluntaryval=='2'){
	$wstext = 'Non-Worksite/Voluntary :';
	$wsval = '2';
}
else{
	$wsval = '1';
}
$displayArray[$dap][$dai]['show'] = true;
$displayArray[$dap][$dai]['title'] = '<span id="ws_id" onclick="changeWorksite(); return true;" style="cursor: pointer;">'.$wstext.'</span>';
$displayArray[$dap][$dai]['value'] = '<input type="checkbox" id="worksiteVoluntary" name="worksiteVoluntary" value="'.$wsval.'"';
if($worksiteVoluntaryval>0) {
	$displayArray[$dap][$dai]['value'] .= ' checked="checked"';
}
$displayArray[$dap][$dai]['value'] .= ' />';
$dai++;
$wstext = 'Credit Union :';
if($creditUnion=='2'){
	$wstext = 'Non-Credit Union :';
	$wsval = '2';
}
else{
	$wsval = '1';
}
$displayArray[$dap][$dai]['show'] = true;
$displayArray[$dap][$dai]['title'] = '<span id="cu_id" onclick="changeCreditUnion(); return true;" style="cursor: pointer;">'.$wstext.'</span>';
$displayArray[$dap][$dai]['value'] = '<input type="checkbox" id="creditUnion" name="creditUnion" value="'.$wsval.'"';
if($creditUnion>0) {
	$displayArray[$dap][$dai]['value'] .= ' checked="checked"';
}
$displayArray[$dap][$dai]['value'] .= ' />';
$dai++;

/* jm start aff cats */


$aatext = 'Affinity/Association :';
//$affinityAssociationOption!=0
if($affinityAssociationval=='2'){
	$aatext = 'Non-Affinity/Association :';
	$aaval = '2';
}
else{
	$aaval = '1';
}
$displayArray[$dap][$dai]['show'] = true;
$displayArray[$dap][$dai]['title'] = '<span id="aa_id" onclick="changeCheckbox(\'aa_id\',document.forms.searchForm.affinityAssociation,\'Affinity/Association :\',\'Non-Affinity/Association :\'); checkAffCat(); checkAffSubCat(); return true;" style="cursor: pointer;">'.$aatext.'</span>';
$displayArray[$dap][$dai]['value'] = '<input type="checkbox" id="affinityAssociation" name="affinityAssociation" value="'.$aaval.'" onclick="checkAffCat(); checkAffSubCat();"';
if($affinityAssociationval>0) {
	$displayArray[$dap][$dai]['value'] .= ' checked="checked"';
}
$displayArray[$dap][$dai]['value'] .= ' />';
$dai++;

$javascript .= "affcat_id = '{$dap}_{$dai}';\n";
if($affinityAssociationval>0 && $aaval=='1') {
	$displayArray[$dap][$dai]['show'] = true;
}
else{
	$displayArray[$dap][$dai]['show'] = false;
}

/* jm start aff cat html */

$displayArray[$dap][$dai]['title'] = 'Affinity/Association Category :';
$displayArray[$dap][$dai]['value'] = '<select class="combo_box" id="AffinityCategoryID" name="AffinityCategoryID[]" size="3" multiple="multiple" onchange="getAffSubCats();">';
$displayArray[$dap][$dai]['value'] .= "<option value=\"\"";
$o_count = count($AffinityCategoryIDval);
if($o_count==0 || ($o_count==1 && $AffinityCategoryIDval[0]=='')) {
	$displayArray[$dap][$dai]['value'] .= " selected=\"selected\"";
	$AffinitySubCategoryParents = array(-1);
}
else{
	$AffinitySubCategoryParents = $AffinityCategoryIDval;
}
$displayArray[$dap][$dai]['value'] .= ">Any</option>";
$affinity_category = getAffinityCategory(0);
foreach($affinity_category as $id=>$name){
	$displayArray[$dap][$dai]['value'] .= "<option value=\"$id\"";
	if(in_array($id,$AffinityCategoryIDval)) {
		$displayArray[$dap][$dai]['value'] .= " selected=\"selected\"";
	}
	$displayArray[$dap][$dai]['value'] .= ">".htmlspecialchars($name)."</option>";
}
$displayArray[$dap][$dai]['value'] .= '</select><br />[Hold ctrl key for multiple selection]';
$dai++;

/* jm start aff subcat html */

$javascript .= "affsubcat_id = '{$dap}_{$dai}';\n";
if($affinityAssociationval>0 && $aaval=='1') {
        $displayArray[$dap][$dai]['show'] = true;
}
else{
        $displayArray[$dap][$dai]['show'] = false;
}

$displayArray[$dap][$dai]['title'] = 'Affinity/Association Sub-Category :';
$displayArray[$dap][$dai]['value'] = '<select class="combo_box" id="AffinitySubCategoryID" name="AffinitySubCategoryID[]" size="3" multiple="multiple">';
$displayArray[$dap][$dai]['value'] .= "<option value=\"\"";
$o_count = count($AffinitySubCategoryIDval);
if($o_count==0 || ($o_count==1 && $AffinitySubCategoryIDval[0]=='')) {
        $displayArray[$dap][$dai]['value'] .= " selected=\"selected\"";
}
$displayArray[$dap][$dai]['value'] .= ">Any</option>";
$affinity_sub_category = getAffinityCategory($AffinitySubCategoryParents);
foreach($affinity_sub_category as $id=>$name){
        $displayArray[$dap][$dai]['value'] .= "<option value=\"$id\"";
        if(in_array($id,$AffinitySubCategoryIDval)) {
                $displayArray[$dap][$dai]['value'] .= " selected=\"selected\"";
        }
        $displayArray[$dap][$dai]['value'] .= ">".htmlspecialchars($name)."</option>";
}
$displayArray[$dap][$dai]['value'] .= '</select><br />[Hold ctrl key for multiple selection]';
$dai++;


$javascript .= "enhArray[2]['{$dap}_{$dai}'] = '87,90';\nreward_id = '{$dap}_{$dai}';\n";
if(in_array(90,$sectorID) || in_array(87,$sectorID)){
	$displayArray[$dap][$dai]['show'] = true;
}
else{
	$displayArray[$dap][$dai]['show'] = false;
}
$displayArray[$dap][$dai]['title'] = 'Rewards :';
$displayArray[$dap][$dai]['value'] = '<input type="checkbox" id="is_rewards" name="is_rewards" value="1" onclick="checkRewards();"';
if($is_rewards>0) {
	$displayArray[$dap][$dai]['value'] .= ' checked="checked"';
}
$displayArray[$dap][$dai]['value'] .= ' />';
$dai++;
$javascript .= "rewarde_id = '{$dap}_{$dai}';\n";
if($is_rewards>0){
	$displayArray[$dap][$dai]['show'] = true;
}
else{
	$displayArray[$dap][$dai]['show'] = false;
}
$displayArray[$dap][$dai]['title'] = 'Rewards Program Emphasis :';
$displayArray[$dap][$dai]['value'] = '<select name="RewardsProgramEmphasis_mult[]" id="RewardsProgramEmphasis_mult" multiple="multiple" size="3" class="combo_box">';
$displayArray[$dap][$dai]['value'] .= "<option value=\"\"";
$o_count = count($RewardsProgramEmphasis_mult);
if($o_count==0 || ($o_count==1 && $RewardsProgramEmphasis_mult[0]=='')) {
	$displayArray[$dap][$dai]['value'] .= " selected=\"selected\"";
}
$displayArray[$dap][$dai]['value'] .= ">Any</option>";
$query_ac ="SELECT RewardTypeID,RewardTypeName FROM cscan_reward_type ORDER BY RewardTypeSort";
$result_ac = $DRW->query($query_ac,$DRW_read);
while($row_ac = $DRW->fetch_row($result_ac)){
	$id = $row_ac[0];
	$name = $row_ac[1];
	$displayArray[$dap][$dai]['value'] .= "<option value = \"$id\"";
	if(in_array($id,$RewardsProgramEmphasis_mult)) {
		$displayArray[$dap][$dai]['value'] .= " selected=\"selected\"";
	}
	$displayArray[$dap][$dai]['value'] .= ">".htmlspecialchars($name)."</option>";
}
$displayArray[$dap][$dai]['value'] .= '</select><br />[Hold ctrl key for multiple selection]';
$dai++;
$displayArray[$dap][$dai]['show'] = true;
$displayArray[$dap][$dai]['title'] = 'Incentive :';
$displayArray[$dap][$dai]['value'] = '<input type="checkbox" id="is_incentive" name="is_incentive" value="1"';
if($is_incentive>0) {
	$displayArray[$dap][$dai]['value'] .= ' checked="checked"';
}
$displayArray[$dap][$dai]['value'] .= ' />';
$dai++;
/*if(!in_array('military',$_SESSION['sess_search_exclude'])){
	$displayArray[$dap][$dai]['show'] = true;
	$displayArray[$dap][$dai]['title'] = 'Military :';
	$displayArray[$dap][$dai]['value'] = '<input type="checkbox" id="is_military" name="is_military" value="1"';
	if($is_military>0) {
		$displayArray[$dap][$dai]['value'] .= ' checked="checked"';
	}
	$displayArray[$dap][$dai]['value'] .= ' />';
	$dai++;
}*/
if(!in_array('prescription',$_SESSION['sess_search_exclude'])){
	$displayArray[$dap][$dai]['show'] = true;
	$displayArray[$dap][$dai]['title'] = 'Rx :';
	$displayArray[$dap][$dai]['value'] = '<input type="checkbox" id="prescription" name="prescription" value="1"';
	if($prescriptionval>0) {
		$displayArray[$dap][$dai]['value'] .= ' checked="checked"';
	}
	$displayArray[$dap][$dai]['value'] .= ' />';
	$dai++;
}
if(!in_array('affinion',$_SESSION['sess_search_exclude'])){
	$displayArray[$dap][$dai]['show'] = true;
	$displayArray[$dap][$dai]['title'] = 'Affinion :';
	$displayArray[$dap][$dai]['value'] = '<input type="checkbox" id="is_affinion" name="is_affinion" value="1"';
	if($is_affinion>0) {
		$displayArray[$dap][$dai]['value'] .= ' checked="checked"';
	}
	$displayArray[$dap][$dai]['value'] .= ' />';
	$dai++;
}
if(!in_array('is_hphsa',$_SESSION['sess_search_exclude'])){
	$javascript .= "enhArray[2]['{$dap}_{$dai}'] = '4';\n";
	if(in_array(4,$sectorID)){
		$displayArray[$dap][$dai]['show'] = true;
	}
	else{
		$displayArray[$dap][$dai]['show'] = false;
	}
	$displayArray[$dap][$dai]['title'] = 'CDHP/HDHP/HSA :';
	$displayArray[$dap][$dai]['value'] = '<input type="checkbox" id="is_hphsa" name="is_hphsa" value="1"';
	if($is_hphsa>0) {
		$displayArray[$dap][$dai]['value'] .= ' checked="checked"';
	}
	$displayArray[$dap][$dai]['value'] .= ' />';
	$dai++;
}
/*$displayArray[$dap][$dai]['show'] = true;
$displayArray[$dap][$dai]['title'] = 'Mover :';
$displayArray[$dap][$dai]['value'] = '<input type="checkbox" id="is_mover" name="is_mover" value="1"';
if($is_mover>0) {
	$displayArray[$dap][$dai]['value'] .= ' checked="checked"';
}
$displayArray[$dap][$dai]['value'] .= ' />';
$dai++;*/
if(!in_array('prescreen',$_SESSION['sess_search_exclude'])){
	$aatext = 'Pre-Screen/Opt-Out :';
	if($OptOutFirmOffer=='2'){
		$aatext = 'Non-Pre-Screen/Opt-Out :';
		$aaval = '2';
	}
	else{
		$aaval = '1';
	}
	$displayArray[$dap][$dai]['show'] = true;
	$displayArray[$dap][$dai]['title'] = '<span id="OptOutFirmOffer_id" onclick="changeCheckbox(\'OptOutFirmOffer_id\',document.forms.searchForm.OptOutFirmOffer,\'Pre-Screen/Opt-Out :\',\'Non-Pre-Screen/Opt-Out :\'); return true;" style="cursor: pointer;">'.$aatext.'</span>';
	$displayArray[$dap][$dai]['value'] = '<input type="checkbox" id="OptOutFirmOffer" name="OptOutFirmOffer" value="'.$aaval.'"';
	if($OptOutFirmOffer>0) {
		$displayArray[$dap][$dai]['value'] .= ' checked="checked"';
	}
	$displayArray[$dap][$dai]['value'] .= ' />';
	$dai++;
}
if(!in_array('citi',$_SESSION['sess_search_exclude'])){
	$aatext = 'Retail Card Study :';
	if($is_citi==2){
		$aatext = 'Non-Retail Card Study :';
		$aaval = '2';
	}
	else{
		$aaval = '1';
	}
	$displayArray[$dap][$dai]['show'] = true;
	$displayArray[$dap][$dai]['title'] = '<span id="is_citi_id" onclick="changeCheckbox(\'is_citi_id\',document.forms.searchForm.is_citi,\'Retail Card Study :\',\'Non-Retail Card Study :\'); return true;" style="cursor: pointer;">'.$aatext.'</span>';
	$displayArray[$dap][$dai]['value'] = '<input type="checkbox" id="is_citi" name="is_citi" value="'.$aaval.'"';
	if($is_citi>0) {
		$displayArray[$dap][$dai]['value'] .= ' checked="checked"';
	}
	$displayArray[$dap][$dai]['value'] .= ' />';
	$dai++;
}
if($_SESSION['sess_plevel']==1 || $_SESSION['sess_plevel']==2){
	$javascript .= "enhArray[2]['{$dap}_{$dai}'] = '219,266,559,560';\n";
	if(in_array(219,$sectorID) || in_array(266,$sectorID) || in_array(559,$sectorID) || in_array(560,$sectorID)){ 
		$displayArray[$dap][$dai]['show'] = true;
	}
	else{
		$displayArray[$dap][$dai]['show'] = false;
	}
	$displayArray[$dap][$dai]['title'] = 'Credit Card Mentioned :';
	$displayArray[$dap][$dai]['value'] = '<input type="checkbox" id="is_CreditCardMentioned" name="is_CreditCardMentioned" value="1"';
	if($is_CreditCardMentioned>0) {
		$displayArray[$dap][$dai]['value'] .= ' checked="checked"';
	}
	$displayArray[$dap][$dai]['value'] .= ' />';
	$dai++;
}
$displayArray[$dap][$dai]['show'] = true;
$displayArray[$dap][$dai]['title'] = 'Target Markets :';
$displayArray[$dap][$dai]['value'] = '<input type="checkbox" id="is_multicultural" name="is_multicultural" value="1" onclick="checkMultic();"';
if($is_multicultural>0) {
	$displayArray[$dap][$dai]['value'] .= ' checked="checked"';
}
$displayArray[$dap][$dai]['value'] .= ' />';
$dai++;
$javascript .= "multic_id = '{$dap}_{$dai}';\n";
if($is_multicultural>0){
	$displayArray[$dap][$dai]['show'] = true;
}
else{
	$displayArray[$dap][$dai]['show'] = false;
}
$displayArray[$dap][$dai]['title'] = '';
$displayArray[$dap][$dai]['value'] = '<select name="multiculturalmarkets_mult[]" id="multiculturalmarkets_mult" multiple="multiple" size="3" class="combo_box">';
$displayArray[$dap][$dai]['value'] .= "<option value=\"\"";
$o_count = count($multiculturalmarkets_mult);
if($o_count==0 || ($o_count==1 && $multiculturalmarkets_mult[0]=='')) {
	$displayArray[$dap][$dai]['value'] .= " selected=\"selected\"";
}
$displayArray[$dap][$dai]['value'] .= ">Any</option>";
$query_ac ="SELECT tm_id,tm_name,tm_sectorID FROM cscan_target_market ORDER BY tm_name";
$result_ac = $DRW->query($query_ac,$DRW_read);
while($row_ac = $DRW->fetch_row($result_ac)){
	$id = $row_ac[0];
	$name = $row_ac[1];
	$ac_sectorID_not = array();
	if($row_ac[2]!=''){
		$ac_sectorID = explode(',',$row_ac[2]);
		foreach($ac_sectorID as $k=>$v){
			if(strpos($v,'-')!==false){
				$ac_sectorID_not[] = substr($v,1);
				unset($ac_sectorID[$k]);
			}
		}
	}
	else{
		$ac_sectorID = array();
	}
	$show2 = false;
	$javascript .= "depsArrayName['multiculturalmarkets_mult[]'][depsArrayName['multiculturalmarkets_mult[]'].length] = '".singleQuoteSafe($name)."';\n";
	$javascript .= "depsArrayID['multiculturalmarkets_mult[]'][depsArrayID['multiculturalmarkets_mult[]'].length] = '$id';\n";
	foreach($scsc as $sid=>$name2){
		if(!in_array($sid,$ac_sectorID_not) && (count($ac_sectorID)==0 || (in_array('C',$ac_sectorID) && in_array($sid,$coreArray)) || (in_array('N',$ac_sectorID) && !in_array($sid,$coreArray)) || in_array($sid,$ac_sectorID))) {
			$javascript .= "depsArrayS['multiculturalmarkets_mult[]']['$sid']['$id'] = true;\n";
			if(count($ac_sectorID)==0 || in_array($sid,$sectorID) || in_array($sid,$categoryID) || in_array($sid,$subCategoryID)) {
				$show2 = true;
			}
		}
	}
	if($show2){
		$displayArray[$dap][$dai]['value'] .= "<option value = \"$id\"";
		if(in_array($id,$multiculturalmarkets_mult)) {
			$displayArray[$dap][$dai]['value'] .= " selected=\"selected\"";
		}
		$displayArray[$dap][$dai]['value'] .= ">".htmlspecialchars($name)."</option>";
	}
}
$displayArray[$dap][$dai]['value'] .= '</select><br />[Hold ctrl key for multiple selection]';
if($_SESSION['sess_plevel']==1 || $_SESSION['sess_plevel']==2){
	//$displayArray[$dap][$dai]['value'] .= '<br />'.addSearchRules('multiculturalmarkets',true);
}
$dai++;
if(!in_array('FeeProduct',$_SESSION['sess_search_exclude'])){
	$aatext = 'Fee Product :';
	if($FeeProduct==2){
		$aatext = 'Ancillary Service - No Fee :';
		$aaval = '0';
	}
	else{
		$aaval = '1';
	}
	$displayArray[$dap][$dai]['show'] = true;
	$displayArray[$dap][$dai]['title'] = '<span id="fp_id" onclick="changeCheckbox(\'fp_id\',document.forms.searchForm.FeeProduct,\'Fee Product :\',\'Ancillary Service - No Fee :\'); return true;" style="cursor: pointer;">'.$aatext.'</span>';
	$displayArray[$dap][$dai]['value'] = '<input type="checkbox" id="FeeProduct" name="FeeProduct" value="'.$aaval.'"';
	if($FeeProduct>0) {
		$displayArray[$dap][$dai]['value'] .= ' checked="checked"';
	}
	$displayArray[$dap][$dai]['value'] .= ' />';
	$dai++;
        
        ############# for the Faux Credit Checkbox ##############
        $fauxtext = 'Faux Check :';
	

        
        $displayArray[$dap][$dai]['show'] = true;
	$displayArray[$dap][$dai]['title'] = '<span id="fp_id" onclick="changeCheckbox(\'faux_check\',document.forms.searchForm.FeeProduct,\'Fee Product :\',\'Ancillary Service - No Fee :\'); return true;" style="cursor: pointer;">'.$fauxtext.'</span>';
	$displayArray[$dap][$dai]['value'] = '<input type="checkbox" id="faux_check" name="faux_check" value="1"';
	if($faux_check>0) {
		$displayArray[$dap][$dai]['value'] .= ' checked="checked"';
	}
	$displayArray[$dap][$dai]['value'] .= ' />';
	$dai++;
        ############# end for the Faux Credit Checkbox ##############
        
	$displayArray[$dap][$dai]['show'] = true;
	$displayArray[$dap][$dai]['title'] = 'Ancillary Products :';
	$displayArray[$dap][$dai]['value'] = '<select name="FeeProductType[]" multiple="multiple" size="3" class="combo_box">';
	$selArray = getFeeProductType();
	$displayArray[$dap][$dai]['value'] .= "<option value=\"\"";
	$o_count = count($FeeProductType);
	if($o_count==0 || ($o_count==1 && $FeeProductType[0]=='')) {
		$displayArray[$dap][$dai]['value'] .= " selected=\"selected\"";
	}
	$displayArray[$dap][$dai]['value'] .= ">Any</option>";
	foreach($selArray as $selvalue=>$seltext) {
		$displayArray[$dap][$dai]['value'] .= "<option value=\"$selvalue\"";
		if(in_array($selvalue,$FeeProductType)) {
			$displayArray[$dap][$dai]['value'] .= " selected=\"selected\"";
		}
		$displayArray[$dap][$dai]['value'] .= ">".htmlspecialchars($seltext)."</option>";
	}
	$displayArray[$dap][$dai]['value'] .= '</select><br />[Hold ctrl key for multiple selection]';
	if($_SESSION['sess_plevel']==1 || $_SESSION['sess_plevel']==2){
		//$displayArray[$dap][$dai]['value'] .= '<br />'.addSearchRules('FeeProductType',true);
	}
	$dai++;
}
######### Start:  Mortgage & Loan - General Mortgage & Loan Details###########
if(!in_array('Refinance',$_SESSION['sess_search_exclude'])){
//    if(!defined('ENV')){
//        define('ENV',getenv('SERVER_NAME'));
//    }
//    if(ENV == 'localhost' || ENV == 'demo.competiscan.com'){
	$aatext = 'Refinance :';
        if($refinance=='2'){
            $aaval = 2;
            $aatext = 'Non-Refinance :';
        }else{
            $aaval = 1;
        }
        
	$javascript .= "mortgage_arr.push('{$dap}_{$dai}'.toString());\n";
        $javascript .= "mortgage_id = '{$dap}_{$dai}';\n";
        if($refinance>0  || in_array(6,$sectorID)){
            $displayArray[$dap][$dai]['show'] = true;
        }else{
            $displayArray[$dap][$dai]['show'] = false;
        }
        //$displayArray[$dap][$dai]['show'] = true;
	$displayArray[$dap][$dai]['title'] = '<span id="refinance_id" onclick="changeCheckbox(\'refinance_id\',document.forms.searchForm.refinance,\'Refinance :\',\'Non-Refinance :\'); return true;" style="cursor: pointer;">'.$aatext.'</span>';
	$displayArray[$dap][$dai]['value'] = '<input type="checkbox" id="refinance" name="refinance" value="'.$aaval.'" onclick="mortgageCheckList();"';
	if($refinance>0) {
		$displayArray[$dap][$dai]['value'] .= ' checked="checked"';
	}
	$displayArray[$dap][$dai]['value'] .= ' />';
	$dai++;
        
        
        $aatext = 'Jumbo/Non-Conforming :';
        $aaval = 1;
        $javascript .= "mortgage_arr.push('{$dap}_{$dai}'.toString());\n";
	$javascript .= "mortgage_id = '{$dap}_{$dai}';\n";
        if($jumbo_ncnfg>0  || in_array(6,$sectorID)){
            $displayArray[$dap][$dai]['show'] = true;
        }else{
            $displayArray[$dap][$dai]['show'] = false;
        }
        //$displayArray[$dap][$dai]['show'] = true;
	$displayArray[$dap][$dai]['title'] = '<span id="jumbo_ncnfg" style="cursor: pointer;">'.$aatext.'</span>';
	$displayArray[$dap][$dai]['value'] = '<input type="checkbox" id="jumbo_ncnfg" name="jumbo_ncnfg" value="'.$aaval.'"';
	if($jumbo_ncnfg>0) {
		$displayArray[$dap][$dai]['value'] .= ' checked="checked"';
	}
	$displayArray[$dap][$dai]['value'] .= ' />';
	$dai++;
        
        $aatext = 'VA :';
        $aaval = 1;
        $javascript .= "mortgage_arr.push('{$dap}_{$dai}'.toString());\n";
	$javascript .= "mortgage_id = '{$dap}_{$dai}';\n";
        if($va>0  || in_array(6,$sectorID)){
            $displayArray[$dap][$dai]['show'] = true;
        }else{
            $displayArray[$dap][$dai]['show'] = false;
        }
        //$displayArray[$dap][$dai]['show'] = true;
	$displayArray[$dap][$dai]['title'] = '<span id="va" style="cursor: pointer;">'.$aatext.'</span>';
	$displayArray[$dap][$dai]['value'] = '<input type="checkbox" id="va" name="va" value="'.$aaval.'"';
	if($va>0) {
		$displayArray[$dap][$dai]['value'] .= ' checked="checked"';
	}
	$displayArray[$dap][$dai]['value'] .= ' />';
	$dai++;
        
        $aatext = 'FHA :';
        $aaval = 1;
        $javascript .= "mortgage_arr.push('{$dap}_{$dai}'.toString());\n";
	$javascript .= "mortgage_id = '{$dap}_{$dai}';\n";
        if($fha>0  || in_array(6,$sectorID)){
            $displayArray[$dap][$dai]['show'] = true;
        }else{
            $displayArray[$dap][$dai]['show'] = false;
        }
        //$displayArray[$dap][$dai]['show'] = true;
	$displayArray[$dap][$dai]['title'] = '<span id="fha" style="cursor: pointer;">'.$aatext.'</span>';
	$displayArray[$dap][$dai]['value'] = '<input type="checkbox" id="fha" name="fha" value="'.$aaval.'"';
	if($fha>0) {
		$displayArray[$dap][$dai]['value'] .= ' checked="checked"';
	}
	$displayArray[$dap][$dai]['value'] .= ' />';
	$dai++;
        
        $aatext = 'Conventional :';
        $aaval = 1;
        $javascript .= "mortgage_arr.push('{$dap}_{$dai}'.toString());\n";
	$javascript .= "mortgage_id = '{$dap}_{$dai}';\n";
        if($conventional>0  || in_array(6,$sectorID)){
            $displayArray[$dap][$dai]['show'] = true;
        }else{
            $displayArray[$dap][$dai]['show'] = false;
        }
        //$displayArray[$dap][$dai]['show'] = true;
	$displayArray[$dap][$dai]['title'] = '<span id="conventional" style="cursor: pointer;">'.$aatext.'</span>';
	$displayArray[$dap][$dai]['value'] = '<input type="checkbox" id="conventional" name="conventional" value="'.$aaval.'"';
	if($conventional>0) {
		$displayArray[$dap][$dai]['value'] .= ' checked="checked"';
	}
	$displayArray[$dap][$dai]['value'] .= ' />';
	$dai++;
        
        $aatext = 'USDA :';
        $aaval = 1;
        $javascript .= "mortgage_arr.push('{$dap}_{$dai}'.toString());\n";
	$javascript .= "mortgage_id = '{$dap}_{$dai}';\n";
        if($usda>0  || in_array(6,$sectorID)){
            $displayArray[$dap][$dai]['show'] = true;
        }else{
            $displayArray[$dap][$dai]['show'] = false;
        }
        //$displayArray[$dap][$dai]['show'] = true;
	$displayArray[$dap][$dai]['title'] = '<span id="usda" style="cursor: pointer;">'.$aatext.'</span>';
	$displayArray[$dap][$dai]['value'] = '<input type="checkbox" id="usda" name="usda" value="'.$aaval.'"';
	if($usda>0) {
		$displayArray[$dap][$dai]['value'] .= ' checked="checked"';
	}
	$displayArray[$dap][$dai]['value'] .= ' />';
	$dai++;
        
        
         $aatext = 'Correspondent Lending :';
        $aaval = 1;
        $javascript .= "mortgage_arr.push('{$dap}_{$dai}'.toString());\n";
	$javascript .= "mortgage_id = '{$dap}_{$dai}';\n";
        if($correspondent_lending>0  || in_array(6,$sectorID)){
            $displayArray[$dap][$dai]['show'] = true;
        }else{
            $displayArray[$dap][$dai]['show'] = false;
        }
        //$displayArray[$dap][$dai]['show'] = true;
	$displayArray[$dap][$dai]['title'] = '<span id="correspondent_lending" style="cursor: pointer;">'.$aatext.'</span>';
	$displayArray[$dap][$dai]['value'] = '<input type="checkbox" id="correspondent_lending" name="correspondent_lending" value="'.$aaval.'"';
	if($correspondent_lending>0) {
		$displayArray[$dap][$dai]['value'] .= ' checked="checked"';
	}
	$displayArray[$dap][$dai]['value'] .= ' />';
	$dai++;
        
        
        ################ for Loan Amount Sliding Scale ########################
        $minmaxval='';
         $aatext = 'Minimum/Maximum Loan Amount ($) :';
        $aaval = 1;
        $javascript .= "mortgage_arr.push('{$dap}_{$dai}'.toString());\n";
	$javascript .= "mortgage_id = '{$dap}_{$dai}';\n";
        if($usda>0  || in_array(6,$sectorID)){
            $displayArray[$dap][$dai]['show'] = true;
            $minmaxval=$minmax;
        }else{
            $displayArray[$dap][$dai]['show'] = false;
        }
        //$displayArray[$dap][$dai]['show'] = true;
	$displayArray[$dap][$dai]['title'] = '<span id="usda" style="cursor: pointer;">'.$aatext.'</span>';
	
        
//        $displayArray[$dap][$dai]['value'] = '<input type="checkbox" id="usda" name="usda" value="'.$aaval.'"';
//	if($usda>0) {
//		$displayArray[$dap][$dai]['value'] .= ' checked="checked"';
//	}
//	$displayArray[$dap][$dai]['value'] .= ' />';
        /*
        $displayArray[$dap][$dai]['value']='<div style="margin-top:7px;"><div><div style="float:left;margin-right:15px;width:40px;text-align:right;" id="minmax-slider-range-min">'.$minloanamount.'</div>
                                            <div style="float:left;width:225px;" id="minmax-slider-range" class="ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all" aria-disabled="false">
                                            <div class="ui-slider-range ui-widget-header ui-corner-all" style="left: 0%; width: 100%;"></div><a class="ui-slider-handle ui-state-default ui-corner-all" href="#" style="left: 0%;">
                                            </a><a class="ui-slider-handle ui-state-default ui-corner-all" href="#" style="left: 100%;"></a></div>
                                            <div style="float:left;margin-left:15px;" id="minmax-slider-range-max">'.$maxloanamount.'</div><div style="clear:left;height:1px;"></div></div>
                                            <input name="minmax" id="minmax" value="'.$minmaxval.'" type="hidden">
                                            <script type="text/javascript">
                                            $(function() {
						$( "#minmax-slider-range" ).slider({
							range: true,
							step: 1,
							min: 0,
							max: 2000000,
							values: [ '.$minloanamount.', '.$maxloanamount.' ],
							change: function( event, ui ) {
								var step_val = $( "#minmax-slider-range" ).slider( "option", "step" );
								var val1 = ui.values[ 0 ];
								var val2 = ui.values[ 1 ];
								if(step_val<1 && typeof ui.values[ 0 ] === "number"){
									val1 = val1.toFixed(2);
									val2 = val2.toFixed(2);
								}
								$( "#minmax-slider-range-min" ).html( val1 );
								$( "#minmax-slider-range-max" ).html( val2 );
								$( "#minmax" ).val( ui.values[ 0 ] + "-" + ui.values[ 1 ] );
							},
							slide: function( event, ui ) {
								var step_val = $( "#minmax-slider-range" ).slider( "option", "step" );
								var val1 = ui.values[ 0 ];
								var val2 = ui.values[ 1 ];
								if(step_val<1 && typeof ui.values[ 0 ] === "number"){
									val1 = val1.toFixed(2);
									val2 = val2.toFixed(2);
								}
								$( "#minmax-slider-range-min" ).html( val1 );
								$( "#minmax-slider-range-max" ).html( val2 );
							}
						});
					});
					</script></div>';
        
        */
        $displayArray[$dap][$dai]['value']='<div style="margin-top:7px;"><div><div style="float:left;margin-right:15px;width:40px;text-align:right;" id="minmax-slider-range-min">
            </div>
                                            <div style="float:left;width:300px;" id="minmax-slider-range" class="ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all" aria-disabled="false">
                                            <div class="ui-slider-range ui-widget-header ui-corner-all" style="left: 0%; width: 100%;"></div><a class="ui-slider-handle ui-state-default ui-corner-all" href="#" style="left: 0%;">
                                            </a><a class="ui-slider-handle ui-state-default ui-corner-all" href="#" style="left: 100%;"></a></div>
                                            <div style="float:left;margin-left:15px;" id="minmax-slider-range-max"></div><div style="clear:left;height:1px;"></div></div>
                                            <div style="float:left;margin-top:15px;">
                                            <div style="float:left;">Min ($) <input type="text" style="width:80px;" id="minrangevalue" maxlength="7" value="'.$minloanamount.'">
                                           </div> <div style="float:left;margin-left:52px;">Max ($) <input type="text" style="width:80px;" maxlength="7" id="maxrangevalue" value="'.$maxloanamount.'">    
                                            <input name="minmax" id="minmax" value="'.$minmaxval.'" type="hidden">
                                            </div>
                                            </div>
                                            <script type="text/javascript">
                                            $(function() {
						$( "#minmax-slider-range" ).slider({
							range: true,
							step: 1,
							min: 0,
							max: 2000000,
							values: [ '.$minloanamount.', '.$maxloanamount.' ],
							change: function( event, ui ) {
								var step_val = $( "#minmax-slider-range" ).slider( "option", "step" );
								var val1 = ui.values[ 0 ];
								var val2 = ui.values[ 1 ];
								if(step_val<1 && typeof ui.values[ 0 ] === "number"){
									val1 = val1.toFixed(2);
									val2 = val2.toFixed(2);
								}
								//$( "#minmax-slider-range-min" ).html( val1 );
								//$( "#minmax-slider-range-max" ).html( val2 );
                                                                $("#minrangevalue").val(val1);
                                                                $("#maxrangevalue").val(val2);
								$( "#minmax" ).val( ui.values[ 0 ] + "-" + ui.values[ 1 ] );
							},
							slide: function( event, ui ) {
								var step_val = $( "#minmax-slider-range" ).slider( "option", "step" );
								var val1 = ui.values[ 0 ];
								var val2 = ui.values[ 1 ];
								if(step_val<1 && typeof ui.values[ 0 ] === "number"){
									val1 = val1.toFixed(2);
									val2 = val2.toFixed(2);
								}
								//$( "#minmax-slider-range-min" ).html( val1 );
								//$( "#minmax-slider-range-max" ).html( val2 );
                                                                $("#minrangevalue").val(val1);
                                                                $("#maxrangevalue").val(val2);
							}
						});
                                                
                                                $("#minrangevalue").change(function(){
                                                   if (/^\d+$/.test($(this).val())) {
                                                    var maxrangeval =   parseInt($("#maxrangevalue").val());
                                                    if(parseInt(this.value)>maxrangeval){
                                                        alert("Minimum amount should be less than Maximum amount.");
                                                        $("#minrangevalue").val("0");                                                        
                                                    }
                                                    $( "#minmax-slider-range" ).slider({
							range: true,
							step: 1,
							min: 0,    
							max: 2000000,
							values: [ parseInt(this.value),parseInt(maxrangeval) ]
                                                     });                                                     
                                                     }else{
                                                         alert("Minimum amount should be numeric value.");
                                                         $("#minrangevalue").val("0");   
                                                     }                                                        
                                                }); 
                                                
                                                $("#maxrangevalue").change(function(){
                                                    if (/^\d+$/.test($(this).val())) {
                                                    var minrangeval =  parseInt($("#minrangevalue").val());
                                                    if(parseInt(this.value)< parseInt(minrangeval)){
                                                        alert("Maximum amount should be greater than Minimum amount.");
                                                        $("#maxrangevalue").val(2000000);                                                       
                                                    }
                                                    $( "#minmax-slider-range" ).slider({
							range: true,
							step: 1,
							min: 0,
							max: 2000000,
							values: [parseInt(minrangeval), parseInt(this.value) ]
                                                     });     
                                                    }else{
                                                         alert("Maximum amount should be numeric value.");
                                                          $("#maxrangevalue").val(2000000);   
                                                     }   
                                                        
                                                });       
					});
					</script></div>';
        
        
        
        
        
	$dai++;
        ################ end for Loan Amount Sliding Scale ########################
        
//    }
}
######### end:  Mortgage & Loan - General Mortgage & Loan Details###########

$javascript .= "enhArray[2]['{$dap}_{$dai}'] = '4,5';\n";
if(in_array(4,$sectorID) || in_array(5,$sectorID)){
	$displayArray[$dap][$dai]['show'] = true;
}
else{
	$displayArray[$dap][$dai]['show'] = false;
}
$displayArray[$dap][$dai]['title'] = 'Riders :';
$displayArray[$dap][$dai]['value'] = '<select name="riders_mult[]" multiple="multiple" size="3" class="combo_box">';
$displayArray[$dap][$dai]['value'] .= "<option value=\"\"";
$o_count = count($riders_mult);
if($o_count==0 || ($o_count==1 && $riders_mult[0]=='')) {
	$displayArray[$dap][$dai]['value'] .= " selected=\"selected\"";
}
$displayArray[$dap][$dai]['value'] .= ">Any</option>";
$query_ac ="SELECT ridersID,ridersName,riders_sectorID FROM cscan_riders ORDER BY ridersSort";
$result_ac = $DRW->query($query_ac,$DRW_read);
while($row_ac = $DRW->fetch_row($result_ac)){
	$id = $row_ac[0];
	$name = $row_ac[1];
	$ac_sectorID_not = array();
	if($row_ac[2]!=''){
		$ac_sectorID = explode(',',$row_ac[2]);
		foreach($ac_sectorID as $k=>$v){
			if(strpos($v,'-')!==false){
				$ac_sectorID_not[] = substr($v,1);
				unset($ac_sectorID[$k]);
			}
		}
	}
	else{
		$ac_sectorID = array();
	}
	$show2 = false;
	$javascript .= "depsArrayName['riders_mult[]'][depsArrayName['riders_mult[]'].length] = '".singleQuoteSafe($name)."';\n";
	$javascript .= "depsArrayID['riders_mult[]'][depsArrayID['riders_mult[]'].length] = '$id';\n";
	foreach($scsc as $sid=>$name2){
		if(!in_array($sid,$ac_sectorID_not) && (count($ac_sectorID)==0 || (in_array('C',$ac_sectorID) && in_array($sid,$coreArray)) || (in_array('N',$ac_sectorID) && !in_array($sid,$coreArray)) || in_array($sid,$ac_sectorID))) {
			$javascript .= "depsArrayS['riders_mult[]']['$sid']['$id'] = true;\n";
			if(in_array($sid,$sectorID) || in_array($sid,$categoryID) || in_array($sid,$subCategoryID)) {
				$show2 = true;
			}
		}
	}
	if($show2){
		$displayArray[$dap][$dai]['value'] .= "<option value = \"$id\"";
		if(in_array($id,$riders_mult)) {
			$displayArray[$dap][$dai]['value'] .= " selected=\"selected\"";
		}
		$displayArray[$dap][$dai]['value'] .= ">".htmlspecialchars($name)."</option>";
	}
}
$displayArray[$dap][$dai]['value'] .= '</select><br />[Hold ctrl key for multiple selection]';
if($_SESSION['sess_plevel']==1 || $_SESSION['sess_plevel']==2){
	//$displayArray[$dap][$dai]['value'] .= '<br />'.addSearchRules('riders_mult',true);
}
$dai++;
//$producerCommunicationOption
$displayArray[$dap][$dai]['show'] = true;
$displayArray[$dap][$dai]['title'] = 'Communication Type :';
$displayArray[$dap][$dai]['value'] = '<select name="agentCommunicationID[]" id="agentCommunicationID" multiple="multiple" size="3" class="combo_box">';
//<br />[<a href="CommunicationTypeHelp.html" class="HyperLink" onclick ="showHelp('CommunicationTypeHelp.html'); return false;">Communication Type Tips</a>]
$displayArray[$dap][$dai]['value'] .= "<option value=\"\"";
$o_count = count($agentCommunicationIDval);
if($o_count==0 || ($o_count==1 && $agentCommunicationIDval[0]=='')) {
	$displayArray[$dap][$dai]['value'] .= " selected=\"selected\"";
}
$displayArray[$dap][$dai]['value'] .= ">Any</option>";
$query_ac ="SELECT ID,type,ac_mPanelID,ac_sectorID FROM cscan_agent_communication ORDER BY type";
$result_ac = $DRW->query($query_ac,$DRW_read);
while($row_ac = $DRW->fetch_row($result_ac)){
	$id = $row_ac[0];
	$name = $row_ac[1];
	
	if($id==22) continue;////Charity/Fundraiser
	if(in_array('citi',$_SESSION['sess_search_exclude']) && ($id==36 || $id==37 || $id==38 || $id==39)) continue;
	
	if($row_ac[2]!=''){
		$ac_mPanelIDArray = explode(',',$row_ac[2]);
	}
	else{
		$ac_mPanelIDArray = array();
	}
	$ac_sectorID_not = array();
	if($row_ac[3]!=''){
		$ac_sectorID = explode(',',$row_ac[3]);
		foreach($ac_sectorID as $k=>$v){
			if(strpos($v,'-')!==false){
				$ac_sectorID_not[] = substr($v,1);
				unset($ac_sectorID[$k]);
			}
		}
	}
	else{
		$ac_sectorID = array();
	}
       
	$show1 = false;
	$show2 = false;
	//if(count($ac_mPanelIDArray)==1) {
	//	$name = $name.' ('.$mailing_panel[$row_ac[2]].')';
	//}
	$javascript .= "depsArrayName['agentCommunicationID[]'][depsArrayName['agentCommunicationID[]'].length] = '".singleQuoteSafe($name)."';\n";
	$javascript .= "depsArrayID['agentCommunicationID[]'][depsArrayID['agentCommunicationID[]'].length] = '$id';\n";
	foreach($mailing_panel as $mpid=>$mpname){
		if(count($ac_mPanelIDArray)==0 || in_array($mpid,$ac_mPanelIDArray)) {
                     ################## update for hide Audience Mortgage Broker #############
//                    if($row_ac[2]=='' && $row_ac[3]=='' && $mpid=='6'){
//                        continue;
//                    }
                    ################## end update for hide Audience Mortgage Broker #############
                    $javascript .= "depsArray['agentCommunicationID[]']['$mpid']['$id'] = true;\n";
			if(in_array($mpid,$mPanelID)) {
				$show1 = true;
			}
		}
	}
	foreach($scsc as $sid=>$name2){
		if(!in_array($sid,$ac_sectorID_not) && (count($ac_sectorID)==0 || (in_array('C',$ac_sectorID) && in_array($sid,$coreArray)) || (in_array('N',$ac_sectorID) && !in_array($sid,$coreArray)) || in_array($sid,$ac_sectorID))) {
			$javascript .= "depsArrayS['agentCommunicationID[]']['$sid']['$id'] = true;\n";
			if(in_array($sid,$sectorID) || in_array($sid,$categoryID) || in_array($sid,$subCategoryID)) {
				$show2 = true;
			}
		}
	}

	###########  Communication Type Implementation ############
    /*if((in_array(5,$agentCommunicationIDval) || in_array(4,$ac_sectorID)  || in_array(5,$ac_sectorID) || in_array(315,$ac_sectorID)) && $id==5){
        $show1 = true;
        $show2 = true;
    }
	if($show1 && $show2){
		$displayArray[$dap][$dai]['value'] .= "<option value = \"$id\"";
		if(in_array($id,$agentCommunicationIDval)) {
			$displayArray[$dap][$dai]['value'] .= " selected=\"selected\"";
		}
		$displayArray[$dap][$dai]['value'] .= ">".htmlspecialchars($name)."</option>";
	}*/
	###########  Communication Type Implementation ############
}
$displayArray[$dap][$dai]['value'] .= '</select><br />[Hold ctrl key for multiple selection]';
//if($_SESSION['sess_plevel']==1 || $_SESSION['sess_plevel']==2){
	$displayArray[$dap][$dai]['value'] .= '<br />'.addSearchRules('agentCommunicationID',true);
//}
$dai++;

$javascript .= "enhArray[1]['{$dap}_{$dai}'] = '1';\n";//enhArray[3]['{$dap}_{$dai}'] = '1,2';
if(in_array(1,$mChannelID)){// && (in_array(1,$mPanelID) || in_array(2,$mPanelID))
	$displayArray[$dap][$dai]['show'] = true;
}
else{
	$displayArray[$dap][$dai]['show'] = false;
}
$displayArray[$dap][$dai]['title'] = 'Delivery Method :';
$displayArray[$dap][$dai]['value'] = '<select name="delmethid_mult[]" multiple="multiple" size="3" onchange="doEnvelopePostageData();" class="combo_box" id="delivery_mehod">';
$selArray = getDeliveryMethod();
$displayArray[$dap][$dai]['value'] .= "<option value=\"\"";
$o_count = count($delmethid_mult);
if($o_count==0 || ($o_count==1 && $delmethid_mult[0]=='')) {
	$displayArray[$dap][$dai]['value'] .= " selected=\"selected\"";
}
$displayArray[$dap][$dai]['value'] .= ">Any</option>";
foreach($selArray as $selvalue=>$seltext) {
	
	if($selvalue==5 && in_array('citi',$_SESSION['sess_search_exclude'])) continue;
	
	$displayArray[$dap][$dai]['value'] .= "<option value=\"$selvalue\"";
	if(in_array($selvalue,$delmethid_mult)) {
		$displayArray[$dap][$dai]['value'] .= " selected=\"selected\"";
	}
	$displayArray[$dap][$dai]['value'] .= ">".htmlspecialchars($seltext)."</option>";
}
$displayArray[$dap][$dai]['value'] .= '</select><br />[Hold ctrl key for multiple selection]';
if($_SESSION['sess_plevel']==1 || $_SESSION['sess_plevel']==2){
	//$displayArray[$dap][$dai]['value'] .= '<br />'.addSearchRules('delmethid',true);
}
$dai++;
############################## Start Envelope/Postage Data Fields################
$javascript .= "enhArray[1]['{$dap}_{$dai}'] = '1';\n";
$javascript .= "enhArray[4]['{$dap}_{$dai}'] = '1,3,7';\n";
if((in_array(1,$mChannelID)) && (in_array(1,$delmethid_mult) || in_array(3,$delmethid_mult) || in_array(7,$delmethid_mult))){
   
    $displayArray[$dap][$dai]['show'] = true;
}
else{
    
    $displayArray[$dap][$dai]['show'] = false;
}
$displayArray[$dap][$dai]['title'] = 'Delivery Type :';
$displayArray[$dap][$dai]['value'] = '<select name="deliveryTypeId[]" multiple="multiple" size="3" class="combo_box">';
$deliveryTypeArray = getDeliveryType();
$displayArray[$dap][$dai]['value'] .= "<option value=\"\"";
$o_count = count($deliveryTypeId);
if($o_count==0 || ($o_count==1 && $deliveryTypeId[0]=='')) {
	$displayArray[$dap][$dai]['value'] .= " selected=\"selected\"";
}
$displayArray[$dap][$dai]['value'] .= ">Any</option>";
foreach($deliveryTypeArray as $selvalue=>$seltext) {
        
	$displayArray[$dap][$dai]['value'] .= "<option value=\"$selvalue\"";
	//if($selvalue==$deliveryTypeId) {
        if(in_array($selvalue,$deliveryTypeId)) {
                
		$displayArray[$dap][$dai]['value'] .= " selected=\"selected\"";
	}
        
	$displayArray[$dap][$dai]['value'] .= ">".htmlspecialchars($seltext)."</option>";
}
$displayArray[$dap][$dai]['value'] .= '</select>';
if($_SESSION['sess_plevel']==1 || $_SESSION['sess_plevel']==2){
	//$displayArray[$dap][$dai]['value'] .= '<br />'.addSearchRules('delmethid',true);
}
$dai++;


//add postage
$javascript .= "enhArray[1]['{$dap}_{$dai}'] = '1';\n";
$javascript .= "enhArray[4]['{$dap}_{$dai}'] = '1,3,7';\n";
if(in_array(1,$mChannelID) && (in_array(1,$delmethid_mult) || in_array(3,$delmethid_mult) || in_array(7,$delmethid_mult))){

	$displayArray[$dap][$dai]['show'] = true;
}
else{
	$displayArray[$dap][$dai]['show'] = false;
}
$displayArray[$dap][$dai]['title'] = 'Postage :';
$displayArray[$dap][$dai]['value'] = '<select name="postageId[]" multiple="multiple" size="3" class="combo_box">';
$postageArray = getPostage();
$displayArray[$dap][$dai]['value'] .= "<option value=\"\"";
$o_count = count($postageId);
if($o_count==0 || ($o_count==1 && $postageId[0]=='')) {
	$displayArray[$dap][$dai]['value'] .= " selected=\"selected\"";
}
$displayArray[$dap][$dai]['value'] .= ">Any</option>";
foreach($postageArray as $selvalue=>$seltext) {
	$displayArray[$dap][$dai]['value'] .= "<option value=\"$selvalue\"";
	//if($selvalue==$postageId) {
        if(in_array($selvalue,$postageId)) {
		$displayArray[$dap][$dai]['value'] .= " selected=\"selected\"";
	}
	$displayArray[$dap][$dai]['value'] .= ">".htmlspecialchars($seltext)."</option>";
}
$displayArray[$dap][$dai]['value'] .= '</select>';
if($_SESSION['sess_plevel']==1 || $_SESSION['sess_plevel']==2){
	//$displayArray[$dap][$dai]['value'] .= '<br />'.addSearchRules('compaignLanguage',true);
}
$dai++;


//add presorted
$javascript .= "enhArray[1]['{$dap}_{$dai}'] = '1';\n";
$javascript .= "enhArray[4]['{$dap}_{$dai}'] = '1,3,7';\n";
if(in_array(1,$mChannelID) && (in_array(1,$delmethid_mult) || in_array(3,$delmethid_mult) || in_array(7,$delmethid_mult))){

	$displayArray[$dap][$dai]['show'] = true;
}
else{
	$displayArray[$dap][$dai]['show'] = false;
}
$displayArray[$dap][$dai]['title'] = 'Pre-Sorted :';
$displayArray[$dap][$dai]['value'] = '<select name="presortedId[]" multiple="multiple" size="3" class="combo_box">';
$presortedArray = getPresorted();
$displayArray[$dap][$dai]['value'] .= "<option value=\"\"";
$o_count = count($presortedId);
if($o_count==0 || ($o_count==1 && $presortedId[0]=='')) {
	$displayArray[$dap][$dai]['value'] .= " selected=\"selected\"";
}
$displayArray[$dap][$dai]['value'] .= ">Any</option>";
foreach($presortedArray as $selvalue=>$seltext) {
	$displayArray[$dap][$dai]['value'] .= "<option value=\"$selvalue\"";
	//if($selvalue==$presortedId) {
        if(in_array($selvalue,$postageId)) {
		$displayArray[$dap][$dai]['value'] .= " selected=\"selected\"";
	}
	$displayArray[$dap][$dai]['value'] .= ">".htmlspecialchars($seltext)."</option>";
}
$displayArray[$dap][$dai]['value'] .= '</select>';
if($_SESSION['sess_plevel']==1 || $_SESSION['sess_plevel']==2){
	//$displayArray[$dap][$dai]['value'] .= '<br />'.addSearchRules('compaignLanguage',true);
}
$dai++;



//add pakagetype
$javascript .= "enhArray[1]['{$dap}_{$dai}'] = '1';\n";
$javascript .= "enhArray[4]['{$dap}_{$dai}'] = '1,3,7';\n";
if(in_array(1,$mChannelID) && (in_array(1,$delmethid_mult) || in_array(3,$delmethid_mult) || in_array(7,$delmethid_mult))){

	$displayArray[$dap][$dai]['show'] = true;
}
else{
	$displayArray[$dap][$dai]['show'] = false;
}
$displayArray[$dap][$dai]['title'] = 'Package Type :';
$displayArray[$dap][$dai]['value'] = '<select name="packageTypeId[]" multiple="multiple" size="3" class="combo_box">';
$packageArray = getPackageType();
$displayArray[$dap][$dai]['value'] .= "<option value=\"\"";
$o_count = count($packageTypeId);
if($o_count==0 || ($o_count==1 && $packageTypeId[0]=='')) {
	$displayArray[$dap][$dai]['value'] .= " selected=\"selected\"";
}
$displayArray[$dap][$dai]['value'] .= ">Any</option>";

foreach($packageArray as $selvalue=>$seltext) {
	$displayArray[$dap][$dai]['value'] .= "<option value=\"$selvalue\"";
	//if($selvalue==$packageTypeId) {
        if(in_array($selvalue,$packageTypeId)) {
		$displayArray[$dap][$dai]['value'] .= " selected=\"selected\"";
	}
	$displayArray[$dap][$dai]['value'] .= ">".htmlspecialchars($seltext)."</option>";
}
$displayArray[$dap][$dai]['value'] .= '</select>';
if($_SESSION['sess_plevel']==1 || $_SESSION['sess_plevel']==2){
	//$displayArray[$dap][$dai]['value'] .= '<br />'.addSearchRules('compaignLanguage',true);
}
$dai++;
############################## End Envelope/Postage Data Fields##################
if(!in_array('electronicID',$_SESSION['sess_search_exclude'])){
	$javascript .= "enhArray[1]['{$dap}_{$dai}'] = '3';\n";
	if(in_array(3,$mChannelID)){
		$displayArray[$dap][$dai]['show'] = true;
	}
	else{
		$displayArray[$dap][$dai]['show'] = false;
	}
	$displayArray[$dap][$dai]['title'] = 'Electronic Type :';
	$displayArray[$dap][$dai]['value'] = '<select class="combo_box" id="electronicID" name="electronicID[]" size="3" multiple="multiple">';
	$displayArray[$dap][$dai]['value'] .= "<option value=\"\"";
	$o_count = count($electronicID);
	if($o_count==0 || ($o_count==1 && $electronicID[0]=='')) {
		$displayArray[$dap][$dai]['value'] .= " selected=\"selected\"";
	}
	$displayArray[$dap][$dai]['value'] .= ">Any</option>";
	$query_ac ="SELECT electronic_id,electronic_name FROM cscan_electronic ORDER BY electronic_name";
	$result_ac = $DRW->query($query_ac,$DRW_read);
	while($row_ac = $DRW->fetch_row($result_ac)){
		$id = $row_ac[0];
		$name = $row_ac[1];
		$displayArray[$dap][$dai]['value'] .= "<option value=\"$id\"";
		if(in_array($id,$electronicID)) {
			$displayArray[$dap][$dai]['value'] .= " selected=\"selected\"";
		}
		$displayArray[$dap][$dai]['value'] .= ">".htmlspecialchars($name)."</option>";
	}
	$displayArray[$dap][$dai]['value'] .= '</select><br />[Hold ctrl key for multiple selection]';
	if($_SESSION['sess_plevel']==1 || $_SESSION['sess_plevel']==2){
		//$displayArray[$dap][$dai]['value'] .= '<br />'.addSearchRules('electronicID',true);
	}
	$dai++;
}
$javascript .= "enhArray[1]['{$dap}_{$dai}'] = '2';\n";
if(in_array(2,$mChannelID)){
	$displayArray[$dap][$dai]['show'] = true;
}
else{
	$displayArray[$dap][$dai]['show'] = false;
}
$displayArray[$dap][$dai]['title'] = 'Publication Type :';
$displayArray[$dap][$dai]['value'] = '<select class="combo_box" id="pubTypeID" name="pubTypeID[]" size="3" multiple="multiple">';
$displayArray[$dap][$dai]['value'] .= "<option value=\"\"";
$o_count = count($pubTypeID);
if($o_count==0 || ($o_count==1 && $pubTypeID[0]=='')) {
	$displayArray[$dap][$dai]['value'] .= " selected=\"selected\"";
}
$displayArray[$dap][$dai]['value'] .= ">Any</option>";
$query_ac ="SELECT print_typeID,print_typeName FROM cscan_print_type ORDER BY print_typeName";
$result_ac = $DRW->query($query_ac,$DRW_read);
while($row_ac = $DRW->fetch_row($result_ac)){
	$id = $row_ac[0];
	$name = $row_ac[1];
	
	$displayArray[$dap][$dai]['value'] .= "<option value=\"$id\"";
	if(in_array($id,$pubTypeID)) {
		$displayArray[$dap][$dai]['value'] .= " selected=\"selected\"";
	}
	$displayArray[$dap][$dai]['value'] .= ">".htmlspecialchars($name)."</option>";
}
$displayArray[$dap][$dai]['value'] .= '</select><br />[Hold ctrl key for multiple selection]';
$dai++;

$javascript .= "enhArray[1]['{$dap}_{$dai}'] = '5';\n";

// Added By Pradeep C For publication name
    if(in_array(2,$mChannelID)){
            $displayArray[$dap][$dai]['show'] = true;
    }
    else{
            $displayArray[$dap][$dai]['show'] = false;
    }


    $displayArray[$dap][$dai]['title'] = 'Publication Name :';
    $displayArray[$dap][$dai]['value'] = '<div id="pubtext"><input type="text" name="publication_name" size="45" class="input_box" value="'.htmlspecialchars($publication_name, ENT_QUOTES).'" onchange="checkLookup(\'publist\');" />
        <input type="hidden" id="is_publication_name" name="is_publication_name" value="'.$is_publication_name.'"><br />
    [<a href="#" onclick="showLook(\'seltext5\',\'showhide5\',\'publist\',document.forms.searchForm.publication_name); return false;" id="showhide5" class="HyperLink">Show Lookup</a>]</div>
    <div id="seltext5" style="border:solid 1px #000000;padding:4px;display:none;float:left;background-color:#E8E8FF;"><iframe name="publist" src="company_iframe.php?parent_field=publication_name" width="290" height="100" frameborder="0" marginheight="0" marginwidth="0" scrolling="auto"></iframe></div>';
    $dai++; 

//End for publication name


if(in_array(5,$mChannelID)){
	$displayArray[$dap][$dai]['show'] = true;
}
else{
	$displayArray[$dap][$dai]['show'] = false;
}
$displayArray[$dap][$dai]['title'] = 'Site Category :';
$displayArray[$dap][$dai]['value'] = '<select class="combo_box" id="siteCatID" name="siteCatID[]" size="3" multiple="multiple">';
$displayArray[$dap][$dai]['value'] .= "<option value=\"\"";
$o_count = count($siteCatID);
if($o_count==0 || ($o_count==1 && $siteCatID[0]=='')) {
	$displayArray[$dap][$dai]['value'] .= " selected=\"selected\"";
}
$displayArray[$dap][$dai]['value'] .= ">Any</option>";
$query_ac ="SELECT sites_category_id,sites_category_name FROM cscan_sites_category ORDER BY sites_category_name";
$result_ac = $DRW->query($query_ac,$DRW_read);
while($row_ac = $DRW->fetch_row($result_ac)){
	$id = $row_ac[0];
	$name = $row_ac[1];
	$displayArray[$dap][$dai]['value'] .= "<option value=\"$id\"";
	if(in_array($id,$siteCatID)) {
		$displayArray[$dap][$dai]['value'] .= " selected=\"selected\"";
	}
	$displayArray[$dap][$dai]['value'] .= ">".htmlspecialchars($name)."</option>";
}
$displayArray[$dap][$dai]['value'] .= '</select><br />[Hold ctrl key for multiple selection]';
$dai++;

//$groupSizeOption
$displayArray[$dap][$dai]['show'] = true;
$displayArray[$dap][$dai]['title'] = 'Group Size :';
$displayArray[$dap][$dai]['value'] = '<select name="groupSize[]" multiple="multiple" size="3" class="combo_box">';
$selArray = get_groupSizeArray();
$displayArray[$dap][$dai]['value'] .= "<option value=\"\"";
$o_count = count($groupSizeval);
if($o_count==0 || ($o_count==1 && $groupSizeval[0]=='')) {
	$displayArray[$dap][$dai]['value'] .= " selected=\"selected\"";
}
$displayArray[$dap][$dai]['value'] .= ">Any</option>";
foreach($selArray as $selvalue=>$seltext){
	$displayArray[$dap][$dai]['value'] .= "<option value=\"$selvalue\"";
	if(in_array($selvalue,$groupSizeval)) {
		$displayArray[$dap][$dai]['value'] .= " selected=\"selected\"";
	}
	$displayArray[$dap][$dai]['value'] .= ">".htmlspecialchars($seltext)."</option>";
}
$displayArray[$dap][$dai]['value'] .= '</select><br />[Hold ctrl key for multiple selection]';
if($_SESSION['sess_plevel']==1 || $_SESSION['sess_plevel']==2){
	//$displayArray[$dap][$dai]['value'] .= '<br />'.addSearchRules('groupSize',true);
}
$dai++;

//$offerOriginOption
$displayArray[$dap][$dai]['show'] = true;
$displayArray[$dap][$dai]['title'] = 'Offer Origin :';
$displayArray[$dap][$dai]['value'] = '<select name="offerOrigin" class="combo_box">';
$displayArray[$dap][$dai]['value'] .= "<option value=\"\"";
if($offerOriginval=='') {
	$displayArray[$dap][$dai]['value'] .= " selected=\"selected\"";
}
$displayArray[$dap][$dai]['value'] .= ">Any</option>";
//$javascript .= "depsArrayName['offerOrigin'][depsArrayName['offerOrigin'].length] = 'Any';\n";
//$javascript .= "depsArrayID['offerOrigin'][depsArrayID['offerOrigin'].length] = '';\n";
$query_ac ="SELECT oo_id,oo_name,oo_mChannelID,oo_sectorID FROM cscan_offer_origin ORDER BY oo_name";
$result_ac = $DRW->query($query_ac,$DRW_read);
while($row_ac = $DRW->fetch_row($result_ac)){
	$id = $row_ac[0];
	$name = $row_ac[1];
	
	if($row_ac[2]!=''){
		$ac_mChannelIDArray = explode(',',$row_ac[2]);
	}
	else{
		$ac_mChannelIDArray = array();
	}
	$ac_sectorID_not = array();
	if($row_ac[3]!=''){
		$ac_sectorID = explode(',',$row_ac[3]);
		foreach($ac_sectorID as $k=>$v){
			if(strpos($v,'-')!==false){
				$ac_sectorID_not[] = substr($v,1);
				unset($ac_sectorID[$k]);
			}
		}
	}
	else{
		$ac_sectorID = array();
	}
	$show1 = false;
	$show2 = false;
	$javascript .= "depsArrayName['offerOrigin'][depsArrayName['offerOrigin'].length] = '".singleQuoteSafe($name)."';\n";
	$javascript .= "depsArrayID['offerOrigin'][depsArrayID['offerOrigin'].length] = '$id';\n";
	foreach($media_channel as $mcid=>$mcname){
		if(count($ac_mChannelIDArray)==0 || in_array($mcid,$ac_mChannelIDArray)) {
			$javascript .= "depsArrayM['offerOrigin']['$mcid']['$id'] = true;\n";
			if(in_array($mcid,$mChannelID)) {
				$show1 = true;
			}
		}
	}
	foreach($scsc as $sid=>$name2){
		if(!in_array($sid,$ac_sectorID_not) && (count($ac_sectorID)==0 || (in_array('C',$ac_sectorID) && in_array($sid,$coreArray)) || (in_array('N',$ac_sectorID) && !in_array($sid,$coreArray)) || in_array($sid,$ac_sectorID))) {
			$javascript .= "depsArrayS['offerOrigin']['$sid']['$id'] = true;\n";
			if(in_array($sid,$sectorID) || in_array($sid,$categoryID) || in_array($sid,$subCategoryID)) {
				$show2 = true;
			}
		}
	}
	if($show1 && $show2){
		$displayArray[$dap][$dai]['value'] .= "<option value = \"$id\"";
		if($offerOriginval==$id) {
			$displayArray[$dap][$dai]['value'] .= " selected=\"selected\"";
		}
		$displayArray[$dap][$dai]['value'] .= ">".htmlspecialchars($name)."</option>";
	}
}
$displayArray[$dap][$dai]['value'] .= '</select>';
if($_SESSION['sess_plevel']==1 || $_SESSION['sess_plevel']==2){
	//$displayArray[$dap][$dai]['value'] .= '<br />'.addSearchRules('offerOrigin',true);
}
$dai++;

//$compaignLanguageOption
$displayArray[$dap][$dai]['show'] = true;
$displayArray[$dap][$dai]['title'] = 'Campaign Language :';
$displayArray[$dap][$dai]['value'] = '<select name="compaignLanguage" class="combo_box"><option value="">Any</option>';
$compaignLanguageArray = get_campaignLanguageArray();
foreach($compaignLanguageArray as $cl){
	$displayArray[$dap][$dai]['value'] .= "<option value=\"$cl\"";
	if($cl==$compaignLanguageval) {
		$displayArray[$dap][$dai]['value'] .= " selected=\"selected\"";
	}
	$displayArray[$dap][$dai]['value'] .= ">".htmlspecialchars($cl)."</option>";
}
$displayArray[$dap][$dai]['value'] .= '</select>';
if($_SESSION['sess_plevel']==1 || $_SESSION['sess_plevel']==2){
	//$displayArray[$dap][$dai]['value'] .= '<br />'.addSearchRules('compaignLanguage',true);
}
$dai++;

$javascript .= "enhArray[2]['{$dap}_{$dai}'] = '315';\n";
if(in_array(315,$sectorID)){
	$displayArray[$dap][$dai]['show'] = true;
}
else{
	$displayArray[$dap][$dai]['show'] = false;
}
$displayArray[$dap][$dai]['title'] = 'Energy - Rate Type :';
$displayArray[$dap][$dai]['value'] = '<select name="ERateType_mult[]" multiple="multiple" size="3" class="combo_box">';
$displayArray[$dap][$dai]['value'] .= "<option value=\"\"";
$o_count = count($ERateType_mult);
if($o_count==0 || ($o_count==1 && $ERateType_mult[0]=='')) {
	$displayArray[$dap][$dai]['value'] .= " selected=\"selected\"";
}
$displayArray[$dap][$dai]['value'] .= ">Any</option>";
$query_ac ="SELECT RateTypeID,RateTypeName FROM cscan_erate_type ORDER BY RateTypeSort";
$result_ac = $DRW->query($query_ac,$DRW_read);
while($row_ac = $DRW->fetch_row($result_ac)){
	$id = $row_ac[0];
	$name = $row_ac[1];
	
	$displayArray[$dap][$dai]['value'] .= "<option value=\"$id\"";
	if(in_array($id,$ERateType_mult)) {
		$displayArray[$dap][$dai]['value'] .= " selected=\"selected\"";
	}
	$displayArray[$dap][$dai]['value'] .= ">".htmlspecialchars($name)."</option>";
}
$displayArray[$dap][$dai]['value'] .= '</select>';
$dai++;
$javascript .= "enhArray[2]['{$dap}_{$dai}'] = '315';\n";
if(in_array(315,$sectorID)){
	$displayArray[$dap][$dai]['show'] = true;
}
else{
	$displayArray[$dap][$dai]['show'] = false;
}
$displayArray[$dap][$dai]['title'] = utf8_decode('Offer Price (¢ per kWh) :');
$displayArray[$dap][$dai]['value'] = '<select name="EOfferPrice_mult[]" multiple="multiple" size="3" class="combo_box">';
$displayArray[$dap][$dai]['value'] .= "<option value=\"\"";
$o_count = count($EOfferPrice_mult);
if($o_count==0 || ($o_count==1 && $EOfferPrice_mult[0]=='')) {
	$displayArray[$dap][$dai]['value'] .= " selected=\"selected\"";
}
$displayArray[$dap][$dai]['value'] .= ">Any</option>";
$query_ac ="SELECT OfferPriceID,OfferPriceName FROM cscan_offer_price ORDER BY OfferPriceSort";
$result_ac = $DRW->query($query_ac,$DRW_read);
while($row_ac = $DRW->fetch_row($result_ac)){
	$id = $row_ac[0];
	$name = $row_ac[1];
	
	$displayArray[$dap][$dai]['value'] .= "<option value=\"$id\"";
	if(in_array($id,$EOfferPrice_mult)) {
		$displayArray[$dap][$dai]['value'] .= " selected=\"selected\"";
	}
	$displayArray[$dap][$dai]['value'] .= ">".utf8_decode(htmlspecialchars($name))."</option>";
}
$displayArray[$dap][$dai]['value'] .= '</select>';
$dai++;
$javascript .= "enhArray[2]['{$dap}_{$dai}'] = '315';\n";
if(in_array(315,$sectorID)){
	$displayArray[$dap][$dai]['show'] = true;
}
else{
	$displayArray[$dap][$dai]['show'] = false;
}
$displayArray[$dap][$dai]['title'] = 'Energy - Term Length :';
$displayArray[$dap][$dai]['value'] = '<select name="ETermLength_mult[]" multiple="multiple" size="3" class="combo_box">';
$displayArray[$dap][$dai]['value'] .= "<option value=\"\"";
$o_count = count($ETermLength_mult);
if($o_count==0 || ($o_count==1 && $ETermLength_mult[0]=='')) {
	$displayArray[$dap][$dai]['value'] .= " selected=\"selected\"";
}
$displayArray[$dap][$dai]['value'] .= ">Any</option>";
$query_ac ="SELECT TermLengthID,TermLengthName FROM cscan_eterm_length ORDER BY TermLengthSort";
$result_ac = $DRW->query($query_ac,$DRW_read);
while($row_ac = $DRW->fetch_row($result_ac)){
	$id = $row_ac[0];
	$name = $row_ac[1];
	
	$displayArray[$dap][$dai]['value'] .= "<option value=\"$id\"";
	if(in_array($id,$ETermLength_mult)) {
		$displayArray[$dap][$dai]['value'] .= " selected=\"selected\"";
	}
	$displayArray[$dap][$dai]['value'] .= ">".htmlspecialchars($name)."</option>";
}
$displayArray[$dap][$dai]['value'] .= '</select>';
$dai++;
$javascript .= "enhArray[2]['{$dap}_{$dai}'] = '315';\n";
if(in_array(315,$sectorID)){
	$displayArray[$dap][$dai]['show'] = true;
}
else{
	$displayArray[$dap][$dai]['show'] = false;
}
$displayArray[$dap][$dai]['title'] = 'Cancel Fee :';
$displayArray[$dap][$dai]['value'] = '<input type="checkbox" id="is_ECancelFee" name="is_ECancelFee" value="1"';
if($is_ECancelFee>0) {
	$displayArray[$dap][$dai]['value'] .= ' checked="checked"';
}
$displayArray[$dap][$dai]['value'] .= ' />';
$dai++;

//$faceAmountOption
$javascript .= "enhArray[3]['{$dap}_{$dai}'] = '1,2';\n";
if((in_array(1,$mPanelID) || in_array(2,$mPanelID))){
	$displayArray[$dap][$dai]['show'] = true;
}
else{
	$displayArray[$dap][$dai]['show'] = false;
}
$displayArray[$dap][$dai]['title'] = 'Face Amount :';
$displayArray[$dap][$dai]['value'] = '<select name="fa_id[]" multiple="multiple" size="3" class="combo_box">';
$displayArray[$dap][$dai]['value'] .= "<option value=\"\"";
$o_count = count($fa_idval);
if($o_count==0 || ($o_count==1 && $fa_idval[0]=='')) {
	$displayArray[$dap][$dai]['value'] .= " selected=\"selected\"";
}
$displayArray[$dap][$dai]['value'] .= ">Any</option>";
$query_ac ="SELECT fa_id,fa_name FROM cscan_face_amount ORDER BY fa_sort";
$result_ac = $DRW->query($query_ac,$DRW_read);
while($row_ac = $DRW->fetch_row($result_ac)){
	$id = $row_ac[0];
	$name = $row_ac[1];
	if(in_array(13,$categoryID)){//Life
		$displayArray[$dap][$dai]['value'] .= "<option value = \"$id\"";
		if(in_array($id,$fa_idval)) {
			$displayArray[$dap][$dai]['value'] .= " selected=\"selected\"";
		}
		$displayArray[$dap][$dai]['value'] .= ">".htmlspecialchars($name)."</option>";
	}
	$javascript .= "faceArrayID[faceArrayID.length] = '$id';\nfaceArray[faceArray.length] = '".singleQuoteSafe($name)."';\n";
}
$displayArray[$dap][$dai]['value'] .= '</select><br />[Hold ctrl key for multiple selection]';
if($_SESSION['sess_plevel']==1 || $_SESSION['sess_plevel']==2){
	//$displayArray[$dap][$dai]['value'] .= '<br />'.addSearchRules('fa_ids',true);
}
$dai++;

//$termLengthOption
$javascript .= "enhArray[3]['{$dap}_{$dai}'] = '1,2';\n";
if((in_array(1,$mPanelID) || in_array(2,$mPanelID))){
	$displayArray[$dap][$dai]['show'] = true;
}
else{
	$displayArray[$dap][$dai]['show'] = false;
}
$displayArray[$dap][$dai]['title'] = 'Term Length :';
$displayArray[$dap][$dai]['value'] = '<select name="tl_id[]" multiple="multiple" size="3" class="combo_box">';
$displayArray[$dap][$dai]['value'] .= "<option value=\"\"";
$o_count = count($tl_idval);
if($o_count==0 || ($o_count==1 && $tl_idval[0]=='')) {
	$displayArray[$dap][$dai]['value'] .= " selected=\"selected\"";
}
$displayArray[$dap][$dai]['value'] .= ">Any</option>";
$query_ac ="SELECT tl_id,tl_name FROM cscan_term_length ORDER BY tl_sort";
$result_ac = $DRW->query($query_ac,$DRW_read);
while($row_ac = $DRW->fetch_row($result_ac)){
	$id = $row_ac[0];
	$name = $row_ac[1];
	if(in_array(13,$categoryID)){//Life
		$displayArray[$dap][$dai]['value'] .= "<option value = \"$id\"";
		if(in_array($id,$tl_idval)) {
			$displayArray[$dap][$dai]['value'] .= " selected=\"selected\"";
		}
		$displayArray[$dap][$dai]['value'] .= ">".htmlspecialchars($name)."</option>";
	}
	$javascript .= "termArrayID[termArrayID.length] = '$id';\ntermArray[termArray.length] = '".singleQuoteSafe($name)."';\n";
}
$displayArray[$dap][$dai]['value'] .= '</select><br />[Hold ctrl key for multiple selection]';
if($_SESSION['sess_plevel']==1 || $_SESSION['sess_plevel']==2){
	//$displayArray[$dap][$dai]['value'] .= '<br />'.addSearchRules('tl_ids',true);
}
$dai++;
$displayArray[$dap][$dai]['show'] = true;
$displayArray[$dap][$dai]['title'] = 'Response Mechanism :';
$displayArray[$dap][$dai]['value'] = '<select name="responseMechID_mult[]" multiple="multiple" size="3" class="combo_box">';
$displayArray[$dap][$dai]['value'] .= "<option value=\"\"";
$o_count = count($responseMechID_mult);
if($o_count==0 || ($o_count==1 && $responseMechID_mult[0]=='')) {
	$displayArray[$dap][$dai]['value'] .= " selected=\"selected\"";
}
$displayArray[$dap][$dai]['value'] .= ">Any</option>";
$query_ac ="SELECT responseMechID,responseMechName,rm_sectorID FROM cscan_response_mechanism ORDER BY responseMechSort";
$result_ac = $DRW->query($query_ac,$DRW_read);
while($row_ac = $DRW->fetch_row($result_ac)){
	$id = $row_ac[0];
	$name = $row_ac[1];
	$ac_sectorID_not = array();
	if($row_ac[2]!=''){
		$ac_sectorID = explode(',',$row_ac[2]);
		foreach($ac_sectorID as $k=>$v){
			if(strpos($v,'-')!==false){
				$ac_sectorID_not[] = substr($v,1);
				unset($ac_sectorID[$k]);
			}
		}
	}
	else{
		$ac_sectorID = array();
	}
	$show2 = false;
	$javascript .= "depsArrayName['responseMechID_mult[]'][depsArrayName['responseMechID_mult[]'].length] = '".singleQuoteSafe($name)."';\n";
	$javascript .= "depsArrayID['responseMechID_mult[]'][depsArrayID['responseMechID_mult[]'].length] = '$id';\n";
	foreach($scsc as $sid=>$name2){
		if(!in_array($sid,$ac_sectorID_not) && (count($ac_sectorID)==0 || (in_array('C',$ac_sectorID) && in_array($sid,$coreArray)) || (in_array('N',$ac_sectorID) && !in_array($sid,$coreArray)) || in_array($sid,$ac_sectorID))) {
			$javascript .= "depsArrayS['responseMechID_mult[]']['$sid']['$id'] = true;\n";
			if(in_array($sid,$sectorID) || in_array($sid,$categoryID) || in_array($sid,$subCategoryID)) {
				$show2 = true;
			}
		}
	}
	if($show2){
		$displayArray[$dap][$dai]['value'] .= "<option value = \"$id\"";
		if(in_array($id,$responseMechID_mult)) {
			$displayArray[$dap][$dai]['value'] .= " selected=\"selected\"";
		}
		$displayArray[$dap][$dai]['value'] .= ">".htmlspecialchars($name)."</option>";
	}
}
$displayArray[$dap][$dai]['value'] .= '</select><br />[Hold ctrl key for multiple selection]';
if($_SESSION['sess_plevel']==1 || $_SESSION['sess_plevel']==2){
	//$displayArray[$dap][$dai]['value'] .= '<br />'.addSearchRules('responseMechID',true);
}
$dai++;
$javascript .= "enhArray[2]['{$dap}_{$dai}'] = '87,90';\n";
if(in_array(90,$sectorID) || in_array(87,$sectorID)){
	$displayArray[$dap][$dai]['show'] = true;
}
else{
	$displayArray[$dap][$dai]['show'] = false;
}
$displayArray[$dap][$dai]['title'] = 'Card Network :';
$displayArray[$dap][$dai]['value'] = '<select name="CardNetwork_mult[]" multiple="multiple" size="3" class="combo_box">';
$displayArray[$dap][$dai]['value'] .= "<option value=\"\"";
$o_count = count($CardNetwork_mult);
if($o_count==0 || ($o_count==1 && $CardNetwork_mult[0]=='')) {
	$displayArray[$dap][$dai]['value'] .= " selected=\"selected\"";
}
$displayArray[$dap][$dai]['value'] .= ">Any</option>";
$query_ac ="SELECT CardTypeName,CardTypeID FROM cscan_card_type WHERE is_network=1 ORDER BY CardTypeSort";
$result_ac = $DRW->query($query_ac,$DRW_read);
while($row_ac = $DRW->fetch_row($result_ac)){
	$seltext = $row_ac[0];
	$selvalue = $row_ac[1];
	$displayArray[$dap][$dai]['value'] .= "<option value=\"$selvalue\"";
	if(in_array($selvalue,$CardNetwork_mult)) {
		$displayArray[$dap][$dai]['value'] .= " selected=\"selected\"";
	}
	$displayArray[$dap][$dai]['value'] .= ">".htmlspecialchars($seltext)."</option>";
}
$displayArray[$dap][$dai]['value'] .= '</select><br />[Hold ctrl key for multiple selection]';
$dai++;
$javascript .= "enhArray[1]['{$dap}_{$dai}'] = '6';\n";
if(in_array(6,$mChannelID)){
	$displayArray[$dap][$dai]['show'] = true;
}
else{
	$displayArray[$dap][$dai]['show'] = false;
}
$displayArray[$dap][$dai]['title'] = 'Network Name :';
$displayArray[$dap][$dai]['value'] = '<select name="external_link" size="1" class="combo_box">';
$displayArray[$dap][$dai]['value'] .= "<option value=\"\">Any</option>";
$nets = array('facebook.com'=>'Facebook','instagram.com'=>'Instagram','linkedin.com'=>'LinkedIn','x.com'=>'X');
foreach($nets as $selvalue=>$seltext) {
	$displayArray[$dap][$dai]['value'] .= "<option value=\"$selvalue\"";
	if($selvalue==$external_link) {
		$displayArray[$dap][$dai]['value'] .= " selected=\"selected\"";
	}
	$displayArray[$dap][$dai]['value'] .= ">".htmlspecialchars($seltext)."</option>";
}
$displayArray[$dap][$dai]['value'] .= '</select>';
if($_SESSION['sess_plevel']==1 || $_SESSION['sess_plevel']==2){
	//$displayArray[$dap][$dai]['value'] .= '<br />'.addSearchRules('external_link',true);
}
$dai++;



#################### for the social media sponsered #############################

//if(ENV == 'localhost' || ENV == 'demo.competiscan.com'){
$javascript .= "enhArray[1]['{$dap}_{$dai}'] = '6';\n";
if(in_array(6,$mChannelID)){
	$displayArray[$dap][$dai]['show'] = true;
}
else{
	$displayArray[$dap][$dai]['show'] = false;
}
$displayArray[$dap][$dai]['title'] = 'Social Media Ad Type :';
$displayArray[$dap][$dai]['value'] = '<select name="socialmedia_adtype" size="1" class="combo_box">';
$displayArray[$dap][$dai]['value'] .= "<option value=\"\">Any</option>";
$socialmedia_adtypeArray = array(1=>'Sponsored',2=>'Corporate');
foreach($socialmedia_adtypeArray as $selvalue=>$seltext) {
	$displayArray[$dap][$dai]['value'] .= "<option value=\"$selvalue\"";
	if($selvalue==$socialmedia_adtype) {
		$displayArray[$dap][$dai]['value'] .= " selected=\"selected\"";
	}
	$displayArray[$dap][$dai]['value'] .= ">".htmlspecialchars($seltext)."</option>";
}
$displayArray[$dap][$dai]['value'] .= '</select>';

$dai++;
/*
$displayArray[$dap][$dai]['show'] = $showhide;
$displayArray[$dap][$dai]['title'] = 'Digital Source :';
$displayArray[$dap][$dai]['value'] = '<input type="radio" value="1" name="ismobile" id="ismobile1"'.$selected.'><label for="ismobile1">Only Desktop </label>&nbsp;&nbsp;<input type="radio" value="2" name="ismobile" id="ismobile2"'.$selected2.'><label for="ismobile2">Only Mobile</label>&nbsp;&nbsp;<input type="radio" value="0" name="ismobile" id="ismobile3"'.$selected3.'><label for="ismobile3">Both</label>';
$dai++;
*/
//}

#################### end for the social media sponsered #############################





if($searchview==2) {
	$alert = 'enter a keyword or ';
}
else{
	$alert = '';
}

$PAGE_HEADING = "Power Search";
$TITLE = "Competiscan $PAGE_HEADING";
$HEAD = '<script src="includes/fullsearch.js?v=20140801" type="text/JavaScript"></script>
<script type="text/JavaScript">
<!--
function intitalizeVars(){
	'.$javascript.'
	//doEnhance('.$enhance.');
}
//-->
</script>';
if($_SESSION['sess_plevel']==1 || $_SESSION['sess_plevel']==2){
	$HEAD .= '<link rel="stylesheet" href="js_calendar/calendar.css" media="screen" type="text/css" />
	<script type="text/javascript" src="js_calendar/calendar.js"></script>';
}
$BODYTAG = ' onload="intitalizeVars();"';
include('header_top.php');
?>
<link rel="stylesheet" href="includes/jquery/jquery-ui.css" /><script type="text/javascript" src="includes/jquery/jquery.min.js"></script><script type="text/javascript" src="includes/jquery/jquery-ui.min.js"></script><script type="text/javascript" src="includes/google/jsapi.js"></script>

<div class="headings"><strong>Welcome: <?php echo $_SESSION['sess_username']; ?></strong></div>
<hr />
<form name="searchForm" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" onsubmit="return validateSearch('<?php echo $alert; ?>');">
<?php

//echo $minloanamount.'=='.$maxloanamount;exit;
#### for remove hide option of OCR keyword ##########
//if($searchview==2) {
#### end for remove hide option of OCR keyword ##########
	if($search_type=='ocr_fulltext2' && ($_SESSION['sess_plevel']==1 || $_SESSION['sess_plevel']==2)) {
		$ocr_fulltext_a = '';
		$ocr_fulltext_b = '';
		$ocr_fulltext_c = '';
		$fulltext_a = 'display:none;';
		$ocr_a = 'display:none;';
	}
	else{
		$ocr_fulltext_a = 'display:none;';
		$ocr_fulltext_b = 'display:none;';
		$ocr_fulltext_c = 'display:none;';
		$fulltext_a = '';
		$ocr_a = '';
	}
?>  
<div class="bodytext" style="float:left;width:520px;">
	<fieldset>
	<legend>Search by keywords</legend>
	<div>
		<div class="row">
			<div style="width:90px;float:left;text-align:right;padding-right:6px;<?php echo $ocr_fulltext_a; ?>" id="ocr_fulltext_a">OCR<br /><span style="font-size:xx-small;">(all text)</span></div>
			<div style="width:90px;float:left;text-align:right;padding-right:6px;<?php echo $fulltext_a; ?>" id="fulltext_a"><label><input type="radio" name="search_type_set" value="fulltext2"<?php if($search_type=='fulltext2') echo ' checked="checked"'; ?> onclick="document.searchForm.search_type.value='fulltext2';" />Full Text<br /><span style="font-size:xx-small;">(headlines only)</span></label></div>
			<div style="float:left;">
				<input class="form-control" type="text" name="key" id="key" value="<?php echo htmlspecialchars($skey, ENT_QUOTES);?>" style="width:300px;" /></td>
			</div>
			<div style="padding-left:4px;float:left;<?php echo $ocr_a; ?>" id="ocr_a"><label><input type="radio" name="search_type_set" value="ocr2"<?php if($search_type=='ocr2') echo ' checked="checked"'; ?> onclick="document.searchForm.search_type.value='ocr2';" />OCR<br /><span style="font-size:xx-small;">&nbsp; &nbsp; &nbsp; &nbsp; (all text)</span></label></div>
		</div>
		
		<div style="clear:left;<?php echo $ocr_fulltext_b; ?>" id="ocr_fulltext_b">
			<div style="width:90px;float:left;padding-right:6px;">&nbsp;</div>
			<div style="float:left;"><label><input type="radio" name="search_type_and" value="1"<?php if($search_type_and==1) echo ' checked="checked"'; ?> />AND</label> &nbsp; <label><input type="radio" name="search_type_and" value="0"<?php if($search_type_and==0) echo ' checked="checked"'; ?> />OR</label></div>
		</div>
		
		<div style="clear:left;<?php echo $ocr_fulltext_c; ?>" id="ocr_fulltext_c">
			<div style="width:90px;float:left;text-align:right;padding-right:6px;">Full Text<br /><span style="font-size:xx-small;">(headlines only)</span></div>
			<div style="float:left;">
				<input class="form-control" type="text" name="key2" id="key2" value="<?php echo htmlspecialchars($skey2, ENT_QUOTES);?>" style="width:300px;" /></td>
			</div>
		</div>
		
		<br>
		
		<div style="clear:left;">
			<div style="width:81px;float:left;text-align:right;">
			<?php
			//if($_SESSION['sess_plevel']==1 || $_SESSION['sess_plevel']==2){?>
                           [<a href="#" onclick="switchSearch(); return false;" class="HyperLink" >OCR/Full Text</a>]<?php
			/*}
			else{
				echo '&nbsp;';
			}*/
			?></div>
			<div style="width:333px;float:left;">&nbsp;</div>
			<div style="float:left;">[<a href = "fulltexthelp.html" onclick="showHelp('fulltexthelp.html'); return false;" class="HyperLink" >Search Tips</a>]<input type="hidden" name = "search_type" value = "<?php echo $search_type; ?>" /><input type="hidden" name = "search_option" value = "boolean" /></div>
		</div>
	</div>
	</fieldset>
</div>
<div style="float:right;width:350px;"><?php 
//if(!isset($_SESSION['cmaIDs'])){
	$_SESSION['cmaIDs'] = array();
	$_SESSION['cmaDate'] = array();
	$_SESSION['maTitle'] = array();
	
	$APIMESSAGEURL=ALERT_API_URL_UAT.'alertDetails';
	$get_message_data = callAPI('POST', $APIMESSAGEURL, false);
	$response_message = json_decode($get_message_data, true);
	$rows_message_data=$response_message['data'];
	// echo "<pre>";
	// print_r($rows_message_data);
	// echo "</pre>";
	//die;
	if(!empty($rows_message_data)){
	foreach($rows_message_data as $getMsgData ){ 
		$_SESSION['cmaIDs'][] = $getMsgData[0];
		$_SESSION['cmaDate'][$getMsgData[0]] = $getMsgData[1];
		$_SESSION['maTitle'][$getMsgData[0]] = $getMsgData[2];
	}
	// echo "<pre>";
	// print_r($_SESSION['cmaIDs']);
	// print_r ($_SESSION['cmaDate']);
	// print_r($_SESSION['maTitle']);
	// echo "</pre>";
	
	//die;
//}
	/*$sql_ma = "SELECT maID,DATE_FORMAT(maDate,'%m/%d/%Y'),maTitle FROM cscan_message_alert WHERE maActive=1 AND maDate<=CURDATE() ORDER BY maDate DESC";
	$rs_ma = $DRW->query($sql_ma,$DRW_read);
	while($row_ma = $DRW->fetch_row($rs_ma)){
		$_SESSION['cmaIDs'][] = $row_ma[0];
		$_SESSION['cmaDate'][$row_ma[0]] = $row_ma[1];
		$_SESSION['maTitle'][$row_ma[0]] = $row_ma[2];
	}*/
}

if(count($_SESSION['cmaIDs'])>0){
	$first = true;
	foreach($_SESSION['cmaIDs'] as $cmaID){
		if(!$first){
			echo '<hr />';
		}
		else{
			$first = false;
		}
		echo '<div class="headings" style="padding:0px 10px 0px 10px;text-align:left;">'.$_SESSION['maTitle'][$cmaID].'</div><div style="padding:10px 10px 0px 10px;text-align:left;"><a href="cma_test.php?id='.$cmaID.'" onclick="showHelp(\'cma_test.php?id='.$cmaID.'\'); return false;" class="HyperLink competiscan-alert" >Competiscan Alert</a></div>';
	}
}
else {
	echo '&nbsp;';
}
?>
</div>
<?php
#### for remove hide option of OCR keyword ##########
//}
//else {
//	echo '<input type="hidden" name="key" value="" />';
//}
#### end for remove hide option of OCR keyword ##########

//$w1 = 520;
$w1 = 560;
//if(strstr($currentpage_url,'localhost') || strstr($currentpage_url,'demo.competiscan.com')){
  //$w1 = 560;
//}
$partblock = array();
foreach($displayArray as $part=>$displayPart){
	if(!isset($partblock[$part])){
		if($part>0){
			$w1 = 620;
			echo '</div>';
		}
		echo '<div class="bodytext" style="float:left;width:520px;">';
	}
	echo '<div id="div'.$part.'" style="width:'.$w1.'px;margin-top:8px;';
	if($part==1 && $enhance==0){
		echo 'display:none;';
	}
	echo '">';
	foreach($displayPart as $order=>$display){
                if (stripos($display['title'], "Publication Name")!== false) {
                    $class_added_pub=" pub_name_class";
                }else if (stripos($display['title'], "Digital Source")!== false) {
                    $class_added_pub=" digital_source_class";
                }
                ##############################Start Envelope/Postage Data Fields################
                else if (stripos($display['title'], "Delivery Type")!== false || stripos($display['title'], "Postage")!== false || stripos($display['title'], "Pre-Sorted")!== false || stripos($display['title'], "Package Type")!== false) {
                    $class_added_pub=" deliverytype_class";
                }
                ##############################End Envelope/Postage Data Fields################
                else{
                    $class_added_pub='';
                }    
		echo '<div id="div'.$part.'_'.$order.'" style="clear:left;width:'.$w1.'px;';
		if(!$display['show']){
			echo 'display:none;';
		}
		echo '" class="bodytext'.$class_added_pub.'"><div style="width:150px;float:left;padding:4px;">'.$display['title'].'</div><div style="float:left;padding:4px;">'.$display['value'].'</div></div>';
	}
	if($part==1){
		?>
		<div class="bodytext" style="clear:left;">
		[<a href="refineSearchHelp.html" class="HyperLink" onclick ="showHelp('refineSearchHelp.html'); return false;">Enhanced Search Tips</a>]
		</div>	
		<?php
	}
	echo '</div>';
}
?>
</div>
<div style="clear:both;height:1px;">&nbsp;</div>
<div id="display_submit" style="float:left;" class="bodytext">
<?php 
if($enhance!=0){
	$enhance_text = 'Close Enhanced Search';
}
else{
	$enhance_text = 'Enhance Search';	
}
?>
	<div style="width:150px;float:left;padding:4px;">&nbsp;</div>
	<div style="float:left;padding:4px;"><?php 
		if($saved==0){
			?><input class="submitbutton" type="submit" name="search" value="Search" /><?php
		}
		else{
			?><input class="submitbutton" type="submit" name="new_search" value="New Search" /><?php
		}
		?>
		<input class="submitbutton" type="submit" id="enhance_button" name="refine_search" value="<?php echo $enhance_text; ?>" onclick="setEnhance(); return false;" />
		<input class="submitbutton" type="submit" name="clear_search" value="Clear Search" onclick="unset('<?php echo $_SERVER['PHP_SELF']."?searchview=$searchview"; ?>'); return false;" /><?php 
		if($saved!=0){
			if($searchName=='') {
				$searchName = ' Search';
			}
			else {
				$searchName = ': '.$searchName;
			}
			echo '<br /><input class="submitbutton" type="submit" name="search" value="Save/Edit'.htmlspecialchars($searchName,ENT_QUOTES).'" style="margin-top:4px;" />';
		}
	?></div>
</div>
<div style="clear:both;height:1px;">&nbsp;</div>
<input type="hidden" name="send" value="1" />
<input type="hidden" name="saved" value="<?php echo $saved; ?>" />
<input type="hidden" name="searchview" value="<?php echo $searchview; ?>" />
<input type="hidden" name="ssid" value="<?php echo $search_id; ?>" />
<input type="hidden" name="enhance" value="<?php 
if($enhance!=0) {
	echo '1';
}
else {
	echo '0';
}
?>" />
<input type="hidden" name="sri" value="<?php echo $sri; ?>" />
</form>
<?php
include 'footer_bottom.php';
?>
<script> 
$("#enhance_button").on("click", function() {
        $(window).scrollTop(700); 
    });
</script>