#!/usr/bin/php
<?php

//error_reporting(E_ALL);
//ini_set('display_errors',1);
set_time_limit(0);
//ini_set('include_path', '/usr/lib/php5/pear');
//ini_set( "memory_limit", "320M" );
require_once('includes/dbcon.php');
//$DRW->databaseReadWrite_die = 1;
require_once('includes/functions.php');
require_once('includes/paginator.php');       //paginator class. 
require_once('includes/paginator_html.php');  //paginator_html class.

require_once __DIR__ . '/vendor/autoload.php';

//require_once('includes/sphinx_function2.php');
require_once('includes/functions_latest3.php');  //latest function


$export = new \HS\Export();
$mintel = new \HS\Mintel();
$time = time();

function getNameFromNumber($num) {
    $numeric = ($num - 1) % 26;
    $letter = chr(65 + $numeric);
    $num2 = intval(($num - 1) / 26);
    if ($num2 > 0) {
        return getNameFromNumber($num2) . $letter;
    } else {
        return $letter;
    }
}

if ($_SERVER['argc'] < 3) {
    print "exportExcel_back3.php sessionid serialized_array\n";
    exit;
}

$sess_userID = $_SERVER['argv'][1];
list($filepath, $file_choice, $bid, $ssid, $sort, $more, $eb_date1, $eb_date2, $eb_date3, $eb_gender, $eb_state, $eb_age, $eb_income, $eb_DMA_ID, $post_field, $sess_sector, $sess_search_exclude, $sess_plevel, $sess_userID, $page, $topCompany, $userID) = unserialize($_SERVER['argv'][2]);
$do_bid = false;
$rs = $row = $objPHPExcel = false;
$unix_pid = getmypid();
$sql = "REPLACE INTO cscan_progress (userID,pct,file_choice,unix_pid) VALUES ($sess_userID,0,$file_choice,$unix_pid)";
$DRW->query($sql, $DRW_main);

if ($file_choice == 1 || $file_choice == 3) {
    if ($file_choice == 1) {
        $is_excel = 2;
    } else {
        $is_excel = 1;
    }
} else {
    $is_excel = 0;
}
$mPanelIDArray = array();
$mChannelIDArray = array();
$sectorIDArray = array();
$delmethidArray = array();
$targetMulticultureArray=array();
if ($ssid > 0) {
    $sql = "SELECT mPanelID,mChannelID,sectorID,userID,delmethid_mult,multiculturalmarkets_mult FROM cscan_search WHERE ID=$ssid";
    $rs = $DRW->query($sql, $DRW_read);
    $data = $DRW->fetch_row($rs);
    $mPanelID = $data[0];
    $mChannelID = $data[1];
    $sectorID = $data[2];
    $userID = $data[3];
    $delmethid = $data[4];
    $multiculturalmarkets_mult = $data[5];
    @$DRW->free_result($rs);
    $mPanelIDArray = explode(',', $mPanelID);
    $mChannelIDArray = explode(',', $mChannelID);
    $sectorIDArray = explode(',', $sectorID);
    $delmethidArray =explode(' ,',$delmethid);
    $targetMulticultureArray=explode(' ,',$multiculturalmarkets_mult);
} elseif (!empty($sess_userID) && $bid >= 0) {
    $sql = "SELECT DISTINCT b_mPanelID FROM cscan_product_basket WHERE userID=" . $sess_userID . " AND basket_id=$bid";
    $rs = $DRW->query($sql, $DRW_read);
    while ($data = $DRW->fetch_row($rs)) {
        $as = explode(',', $data[0]);
        foreach ($as as $a) {
            $a = (string) $a;
            if ($a != '' && !in_array($a, $mPanelIDArray)) {
                $mPanelIDArray[] = $a;
            }
        }
    }
    $sql = "SELECT DISTINCT b_mChannelID FROM cscan_product_basket WHERE userID=" . $sess_userID . " AND basket_id=$bid";
    $rs = $DRW->query($sql, $DRW_read);
    while ($data = $DRW->fetch_row($rs)) {
        $as = explode(',', $data[0]);
        foreach ($as as $a) {
            $a = (string) $a;
            if ($a != '' && !in_array($a, $mChannelIDArray)) {
                $mChannelIDArray[] = $a;
            }
        }
    }
    $sql = "SELECT DISTINCT b_sectorID FROM cscan_product_basket WHERE userID=" . $sess_userID . " AND basket_id=$bid";
    $rs = $DRW->query($sql, $DRW_read);
    while ($data = $DRW->fetch_row($rs)) {
        $as = explode(',', $data[0]);
        foreach ($as as $a) {
            $a = (string) $a;
            if ($a != '' && !in_array($a, $sectorIDArray)) {
                $sectorIDArray[] = $a;
            }
        }
    }
}


if (empty($sess_userID) && !empty($userID)) {
    $sess_userID = $userID;
}
if ((in_array(1, $mPanelIDArray) || in_array(2, $mPanelIDArray)) && (in_array(1, $mChannelIDArray) || in_array(3, $mChannelIDArray) || in_array(5, $mChannelIDArray) || in_array(9, $mChannelIDArray) || in_array(10, $mChannelIDArray)))
    $consumer = true;
else
    $consumer = false;

if ($consumer && ((count($mPanelIDArray) == 1 && (in_array(1, $mPanelIDArray) || in_array(2, $mPanelIDArray))) || (count($mPanelIDArray) == 2 && in_array(1, $mPanelIDArray) && in_array(2, $mPanelIDArray))))
    $consumer_only = true;
else
    $consumer_only = false;

if (in_array(2, $mChannelIDArray)) {
    $is_print = true;
} else {
    $is_print = false;
}


$heading = array();
$heading["company"] = 'Primary Company';
$heading["secondCompany"] = 'Additional Companies';
$heading["sectorID"] = 'Sector';
$heading["PrimarysectorID"] = 'Primary Sector';
$heading["categoryID"] = 'Category';
$heading["PrimarycategoryID"] = 'Primary Category';
$heading["subCategoryID"] = 'Sub Category';
$heading["PrimarysubCategoryID"] = 'Primary Sub Category';
$heading["subSubCategoryID"] = 'Sub Sub Category';
$heading["PrimarysubSubCategoryID"] = 'Primary Sub Sub Category';
$heading["entryID"] = 'EntryID';
/*############### Start For Quarter Filed #############*/
$heading["quarter"] = 'Quarter';
/* ############### End For Quarter Filed #############*/
$heading["productHeadline"] = 'Headline';
$heading["agentCommunicationID"] = 'Communications Type';
$heading["mChannelID"] = 'Media Channel';

/* ###### For digital source and simple domain ###### */

$heading["digital_source"] = 'Digital Source';
$heading["simple_domain"] = 'Simple Domain';

/* ###### End for digital source and simple domain ###### */

/* ############   For SEM Details     ###### */

$heading["sem_search_key"] = 'SEM Search Key';
$heading["sem_url"] = 'SEM Url';
$heading["sem_headline"] = 'SEM Headline';
$heading["sem_description"] = 'SEM Description';


/* ###### End For SEM Details  ###### */


$heading["mPanelID"] = 'Audience';
$heading["state"] = 'State/Province';
$heading["country"] = 'Country';
if ($consumer) {
    $heading["age"] = 'Age';
    //$heading["ficos"] = 'Risk Score';
    //$heading["gender"] = 'Gender';
    $heading["income"] = 'Income';
    $heading["mailpieces"] = 'Mail Pieces';
    $heading["mailvolume"] = 'Estimated Mail Volume';
    $heading["mailspend"] = 'Estimated Mail Spend';
    $heading["realtime_mailvolume"] = 'Real Time Mail Volume';
   $heading["ppeve"] = 'Email Volume Estimates'; // for the eve calculation                     
    $heading["ppdate"] = 'Mail Piece Months';
    //$heading["DMA_ID"] = 'Metropolitan Area';
    $heading["edc_id"] = 'EDC / LDC / TDSP';
}

//if(in_array(5,$mChannelIDArray) || in_array(9,$mChannelIDArray) || in_array(10,$mChannelIDArray)){
//        $heading["age"] = 'Age';
//        $heading["income"] = 'Income';
//}


if ($is_print) {
    $heading["Publication"] = 'Publication';
    $heading["PublicationDate"] = 'Publication Date';
}
$heading["compaignLanguage"] = 'Campaign Language';
$heading["mTypeID"] = 'Mailing Type';
$heading["IssueTypeID"] = 'Issue Type';
$heading["affinityAssociation"] = 'Affinity/Association';
$heading["affinityAssociationName"] = 'Affinity/Association Name';
$heading["AffinityCategoryID"] = 'Affinity/Association Category';
$heading["AffinitySubCategoryID"] = 'Affinity/Association Sub-Category';
$heading["firstSeen"] = 'First Seen';
$heading["lastSeen"] = 'Last Seen';
$heading["productName"] = 'Product';
$heading["filesize"] = 'File Size';
$heading["delmethid"] = 'Delivery Method';
$heading["deliveryTypeId"] = 'Delivery Type';
$heading["postageId"] = 'Postage';
$heading["presortedId"] = 'Pre-Sorted';
$heading["packageTypeId"] = 'Package Type';
$heading['incentive'] = 'Sign-on Incentive';
$heading['incentive_ongoing'] = 'Ongoing Incentive';
$heading['fa'] = 'Face Amount';
$heading['tl'] = 'Term Length';
$heading['riders'] = 'Riders';
$heading['responseMechID'] = 'Response Mechanism';
$heading['FeeProductType'] = 'Ancillary Products';
$heading['external_link_network'] = 'Network Name';
//$heading['social_media_name'] = 'Facebook Page Name/Twitter Handle';
$heading['external_updates'] = 'Number of Updates/Tweets';
$heading['external_fans'] = 'Number of Fans/Followers';
$heading['external_link'] = 'External Link';
$heading['traffic_sources'] = 'Observed Traffic Sources';
$heading['doclink'] = 'PDF Content';
$heading['prescription'] = 'Rx';
$heading['is_hphsa'] = 'CDHP/HDHP/HSA';
$heading['is_prescreen'] = 'Pre-Screen';
$heading['is_citi'] = 'Retail Card Study';
$heading['OfferExpiryDate'] = 'Offer Expiry Date';
$heading['pi'] = 'Panelist Info';
$heading['pi2'] = 'Panelist Affinities';
$heading['pi3'] = 'Panelist Loyalty/Retention, Statement Companies';
$heading['worksiteVoluntary'] = 'Worksite/Voluntary';
$heading['groupSize'] = 'Group Size';
$heading['creditUnion'] = 'Credit Union';

// for digital spend/impressions
//$heading['spend_impression'] = 'Spend ($) / Impression';
$heading['estimated_spend'] = 'Estimated Digital Spend ($)';
$heading['estimated_impression'] = 'Estimated Digital Impression';

// For Mortgage & Loan - General Mortgage & Loan Details
$heading['refinance'] = 'Refinance';
$heading['jumbo_ncnfg'] = 'Jumbo/Non-Conforming';
$heading['va'] = 'VA';
$heading['fha'] = 'FHA';
$heading['conventional'] = 'Conventional';
$heading['usda'] = 'USDA';
$heading['correspondent_lending'] = 'Correspondent Lending';

$heading['incentive'] = 'Sign-on Incentive';
$mintel_set = $mintel->getFields();
$mintel_set_2 = $mintel->getFieldSet('incentive_set_2');
$mintel_set_3 = $mintel->getFieldSet('incentive_set_3');
###################### For Faux Check selection ####################  

$heading["faux_check"] = 'Faux Check';

###################### End For Faux Check selection ####################
###################### For Social Media Ad Type selection ####################  

$heading["socialmedia_adtype"] = 'Social Media Ad Type';
###################### End For Social Media Ad Type selection ####################
######################### Start personalization########################
$heading["personalization"] = 'Personalization';
######################### End personalization##########################
###############add New field at myexcel#########
$heading["is_multicultural"] = 'Target Markets';
$heading["businessContent_mult"] = 'Business Content';
$heading["offerOrigin"] = 'Offer Origin';
$heading["FeeProduct"] = 'Fee Product';
$heading["is_affinion"] = 'Affinion';
###############add New field at myexcel#########
$heading = array_merge($heading, $export->convertToHeaders($mintel_set), $export->convertToHeaders($mintel_set_2), $export->convertToHeaders($mintel_set_3));

$showheading = array(
    "company" => false,
    "secondCompany" => false,
    "categoryID" => false,
    "PrimarycategoryID" => false,
    "compaignLanguage" => false,
    "entryID" => false,
    "quarter"=>false,
    "firstSeen" => false,
    "lastSeen" => false,
    "mChannelID" => false,
    "digital_source" => false,
    "simple_domain" => false,
    /* ##### Start for SEM Details ##### */
    "sem_search_key" => false,
    "sem_url" => false,
    "sem_headline" => false,
    "sem_description" => false,
    /* ##### End for SEM Details ##### */
    "mPanelID" => false,
    "mTypeID" => false,
    "IssueTypeID" => false,
    "productName" => false,
    "productHeadline" => false,
    "sectorID" => false,
    "PrimarysectorID" => false,
    "subCategoryID" => false,
    "PrimarysubCategoryID" => false,
    "subSubCategoryID" => false,
    "PrimarysubSubCategoryID" => false,
    "state" => false,
    "country" => false,
    'agentCommunicationID' => false,
    'delmethid' => false,
    'deliveryTypeId' => false,
    'postageId' => false,
    'presortedId' => false,
    'packageTypeId' => false,
    'affinityAssociation' => false,
    'affinityAssociationName' => false,
    'AffinityCategoryID' => false,
    'AffinitySubCategoryID' => false,
    'filesize' => false,
    'incentive' => false,
    'incentive_ongoing' => false,
    'incentive_type' => false,
    'fa' => false,
    'tl' => false,
    'riders' => false,
    'pi' => false,
    'responseMechID' => false,
    'pi2' => false,
    'pi3' => false,
    'FeeProductType' => false,
    'social_media_name' => false,
    'external_updates' => false,
    'external_fans' => false,
    'external_link' => false,
    'external_link_network' => false,
    'traffic_sources' => false,
    'doclink' => false,
    'prescription' => false,
    'is_hphsa' => false,
    'is_prescreen' => false,
    'is_citi' => false,
    'OfferExpiryDate' => false,
    'worksiteVoluntary' => false,
    'groupSize' => false,
    'creditUnion' => false,
    'estimated_spend' => false,
    'estimated_impression' => false,
    'refinance' => false,
    'jumbo_ncnfg' => false,
    'va' => false,
    'fha' => false,
    'conventional' => false,
    'usda' => false,
    'correspondent_lending'=>false,
    'age' => false,
    'income' => false,
    ###################### For Faux Check selection #################### 
    'faux_check' => false,
    ###################### end For Faux Check selection #################### 
    ###################### For Social Media Ad Type selection #################### 
    'socialmedia_adtype' => false,
    ###################### end For Social Media Ad Type selection #################### 
    ######################### Start personalization########################
    'personalization' => false,
    ######################### End personalization##########################
    ###############add New field at myexcel#########
    'is_multicultural' => false,
    'businessContent_mult' => false,
    'offerOrigin' => false,
    'FeeProduct' => false,
    'is_affinion' => false
    ###############add New field at myexcel#########
);
if ($consumer) {
    // this is weird we set it to false but the output checks if it's !empty
    // so basically false is true and null is false => simple? (PK)
    //$showheading['ficos'] = false;
    $showheading['gender'] = false;
    $showheading['mailpieces'] = false;
    $showheading['mailvolume'] = false;
    $showheading['realtime_mailvolume'] = false;
    $showheading['mailspend'] = false;
    $showheading['ppdate'] = false;
    $showheading['ppeve'] = false; // for the eve calculation
    $showheading['DMA_ID'] = false;
    $showheading['edc_id'] = false;
}

if ($more > 2) {
    $showheading['incentive'] = true;
    $showheading['incentive_ongoing'] = true;

    foreach ($mintel_set as $field_name => $field_specs) {
        $showheading[$field_name] = true;
    }

    foreach ($mintel_set_2 as $field_name => $field_specs) {
        $showheading[$field_name] = true;
    }

    foreach ($mintel_set_3 as $field_name => $field_specs) {
        $showheading[$field_name] = true;
    }
}

if ($more == 2) {
    $showheading['pi'] = true;
}
if ($more > 2 || $more == 1) {
    $showheading['productName'] = true;
}
//print_r($post_field); die;
if (count($post_field) > 0) {
    $fromExportAll = false;
    foreach ($post_field as $value) {
        if ($value == 'spend_impression') {
            $showheading['estimated_spend'] = true;
            $showheading['estimated_impression'] = true;
        } elseif ($value == 'mortgage_details') {
            $showheading['refinance'] = true;
            $showheading['jumbo_ncnfg'] = true;
            $showheading['va'] = true;
            $showheading['fha'] = true;
            $showheading['conventional'] = true;
            $showheading['usda'] = true;
            $showheading['correspondent_lending']=true;
        } elseif ($value == 'age') {
            if (!is21FilterOn($sess_userID))
                $showheading['age'] = true;
        }elseif ($value == 'income') {
            if (!is21FilterOn($sess_userID))
                $showheading['income'] = true;
        }elseif ($value == 'faux_check') {
            $showheading['faux_check'] = true;
        } elseif ($value == 'socialmedia_adtype') {
            $showheading['socialmedia_adtype'] = true; 
        }
        ################## Start Personalized ###################
        elseif ($value == 'personalization') {
            $showheading['personalization'] = true; 
        }
        ################## End Personalized #####################
        ###############add New field at myexcel#########
        elseif ($value == 'is_multicultural') {
            $showheading['is_multicultural'] = true; 
        }
        elseif ($value == 'businessContent_mult') {
            $showheading['businessContent_mult'] = true; 
        }
        elseif ($value == 'offerOrigin') {
            $showheading['offerOrigin'] = true; 
        }
        elseif ($value == 'FeeProduct') {
            $showheading['FeeProduct'] = true; 
        }
        elseif ($value == 'is_affinion') {
            $showheading['is_affinion'] = true; 
        }
        ###############End New field at myexcel#########
        else {
            $showheading[$value] = true;
        }
    }
} else {
    $fromExportAll = true;
    foreach ($showheading as $key => $value) {
        $showheading[$key] = true;
    }
    $showheading['pi'] = false;
    $showheading['pi2'] = false;
    $showheading['pi3'] = false;
    if (!in_array(90, $sectorIDArray) && !in_array(87, $sectorIDArray) && !in_array(6, $sectorIDArray)) {
        $showheading['productName'] = false;
    }
    if (in_array('DMA_ID', $sess_search_exclude)) {
        $showheading["DMA_ID"] = false;
    }
    if (in_array('me_filesize', $sess_search_exclude)) {
        $showheading["filesize"] = false;
    }
    if (!in_array(4, $sectorIDArray) || !in_array(4, $sess_sector) || in_array('prescription', $sess_search_exclude)) {
        $showheading["prescription"] = false;
    }
    if (!in_array(4, $sectorIDArray) || !in_array(4, $sess_sector) || in_array('is_hphsa', $sess_search_exclude)) {
        $showheading["is_hphsa"] = false;
    }
    //if(in_array('citi',$sess_search_exclude)){
    $showheading["is_citi"] = false;
    //}
    if (!in_array(4, $sectorIDArray) || !in_array(4, $sess_sector)) {
        $showheading['fa'] = false;
        $showheading['tl'] = false;
        $showheading['IssueTypeID'] = false;
    }
    if ((!in_array(4, $sectorIDArray) && !in_array(5, $sectorIDArray)) || (!in_array(4, $sess_sector) && !in_array(5, $sess_sector))) {
        $showheading['riders'] = false;
    }
    if (!in_array(1, $mChannelIDArray)) {
        $showheading['delmethid'] = false;
    } 
    ############################## Start Envelope/Postage Data Fields################
    if (!in_array(1, $delmethidArray) || !in_array(3, $delmethidArray) || !in_array(7, $delmethidArray)) {
        $showheading['deliveryTypeId'] = false;
    }
    if (!in_array(1, $delmethidArray) || !in_array(3, $delmethidArray) || !in_array(7, $delmethidArray)) {
        $showheading['postageId'] = false;
    }
    if (!in_array(1, $delmethidArray) || !in_array(3, $delmethidArray) || !in_array(7, $delmethidArray)) {
        $showheading['presortedId'] = false;
    }
    if (!in_array(1, $delmethidArray) || !in_array(3, $delmethidArray) || !in_array(7, $delmethidArray)) {
        $showheading['packageTypeId'] = false;
    }
    ############################## End Envelope/Postage Data Fields################
    if (!in_array(4, $sectorIDArray) && !in_array(90, $sectorIDArray) && !in_array(87, $sectorIDArray) && !in_array(6, $sectorIDArray) && !in_array(9, $sectorIDArray) && !in_array(219, $sectorIDArray)) {
        $showheading['FeeProductType'] = false;
    }
    if (!in_array(6, $mChannelIDArray)) {
        $showheading['social_media_name'] = false;
        $showheading['external_updates'] = false;
        $showheading['external_fans'] = false;
        $showheading['external_link'] = false;
        $showheading['external_link_network'] = false;
    }
    if (!in_array(5, $mChannelIDArray) && !in_array(7, $mChannelIDArray)) {
        $showheading['traffic_sources'] = false;
    }


    /* ######  for digital source ###### */

    if (!in_array(5, $mChannelIDArray) && !in_array(9, $mChannelIDArray) && !in_array(10, $mChannelIDArray)) {
        $showheading['digital_source'] = false;
        $showheading['simple_domain'] = false;
    }

    /* ###### End for digital source ###### */

    /* ######  for SEM Search Details ###### */

    if (!in_array(9, $mChannelIDArray)) {
        $showheading['sem_search_key'] = false;
        $showheading['sem_url'] = false;
        $showheading['sem_headline'] = false;
        $showheading['sem_description'] = false;
    }

    /* ###### End for digital source ###### */


    if (in_array('me_doclink', $sess_search_exclude)) {
        $showheading["doclink"] = false;
    }
    if (in_array('sub_sub', $sess_search_exclude)) {
        $showheading["subSubCategoryID"] = false;
        $showheading["PrimarysubSubCategoryID"] = false;
    }
    if ($consumer && !$consumer_only) {
        $showheading['mailvolume'] = false;
        $showheading['mailspend'] = false;
        /* ##### For Real Time EMV ##### */
        $showheading['realtime_mailvolume'] = false;
        $showheading['ppeve'] = false; // for the eve calculation
    }
    if (!in_array(315, $sectorIDArray)) {
        $showheading['edc_id'] = false;
    }
    if ((!in_array(4, $sectorIDArray) || !in_array(4, $sess_sector)) && (!in_array(5, $sectorIDArray) || !in_array(5, $sess_sector))) {
        $showheading['worksiteVoluntary'] = false;
        $showheading['groupSize'] = false;
    }
    ###################### For Faux Check selection #################### 
    if (!in_array(90, $sectorIDArray) && !in_array(87, $sectorIDArray) && !in_array(6, $sectorIDArray)) {
        $showheading['faux_check'] = false;
    }
    ###################### For Faux Check selection #################### 
    ###################### For Social Media Ad Type selection #################### 
    if (!in_array(6, $mChannelIDArray)) {
        $showheading['socialmedia_adtype'] = false;
    }
    ###################### End For Social Media Ad Type selection ################# 
    ################################# Start Personalized#####################
    if(in_array(1,$mPanelIDArray) || in_array(2,$mPanelIDArray) || in_array(4,$mPanelIDArray) || in_array(5,$mPanelIDArray) || in_array(6,$mPanelIDArray) || in_array(9,$mPanelIDArray)){
            $showheading['personalization'] = false;
    }
    #################################End Personalized#####################
}
if ($more > 2 || $more == 1) {
    $showheading['ppdate'] = false;
} else {
    $showheading['pi2'] = false;
    $showheading['pi3'] = false;
}
if (!$is_print) {
    $showheading["Publication"] = false;
    $showheading["PublicationDate"] = false;
}
//As per requested by nate removed group permission
//if (($sess_plevel != 1 && $sess_plevel != 2) || $fromExportAll) {
    //$showheading['PrimarysectorID'] = false;
    //$showheading['PrimarycategoryID'] = false;
    //$showheading['PrimarysubCategoryID'] = false;
    //$showheading['PrimarysubSubCategoryID'] = false;
if (($sess_plevel != 1 && $sess_plevel != 2) || $fromExportAll) {
    $showheading['IssueTypeID'] = false; ////
}

$searchtitle = '';
if ($ssid > 0 || $bid >= 0) {
    if ($bid < 0) {
        list($displayKeywords) = getKeywords($ssid);
        $displayKeywords = preg_replace('/(.)(<strong>)/', '$1| $2', trim($displayKeywords));
        $searchtitle = html_entity_decode(strip_tags($displayKeywords));
        unset($displayKeywords);
    } else {
        if ($bid == 0) {
            $basket_name = 'Default Basket';
        } else {
            $sql = "SELECT basket_name FROM cscan_basket WHERE userID=" . $sess_userID . " AND basket_id=$bid";
            $rs = $DRW->query($sql, $DRW_read);
            $data = $DRW->fetch_row($rs);
            $basket_name = $data[0];
            @$DRW->free_result($rs);
        }

        $searchtitle = 'Your Basket: ' . $basket_name;
        unset($basket_name);
    }
}
$max_cell_len = 5000; //32,767; 16,368
if (strlen($searchtitle) > $max_cell_len) {
    $searchtitle = substr($searchtitle, 0, $max_cell_len) . '...';
}
$header = '';
$erow = 1;
$ecol = 0;
if ($is_excel > 0) {
    if ($is_excel == 2) {
        error_reporting(E_ALL ^ E_DEPRECATED);
        require_once 'Spreadsheet/Excel/Writer.php';
        // Creating a workbook
        $workbook = new Spreadsheet_Excel_Writer($filepath);
        $workbook->setVersion(8);

        // Creating a worksheet
        //$worksheet =& $workbook->addWorksheet('Competiscan');
        $worksheet = $workbook->addWorksheet('Competiscan');
        //$worksheet->setInputEncoding('Windows-1252');
        $worksheet->setInputEncoding('UTF-8');

        //$format_head =& $workbook->addFormat();
        $format_head = $workbook->addFormat();
        $format_head->setBold();
        $format_head->setUnderline(1);

        //$format_percent =& $workbook->addFormat();
        $format_percent = $workbook->addFormat();
        $format_percent->setNumFormat('0.00%');
        //$format_number =& $workbook->addFormat();
        $format_number = $workbook->addFormat();
        $format_number->setNumFormat('#,##0');
        //$format_dec =& $workbook->addFormat();
        $format_dec = $workbook->addFormat();
        $format_dec->setNumFormat('#,##0.00');

        //$format_title =& $workbook->addFormat();
        $format_title = $workbook->addFormat();
        $format_title->setItalic();

        $worksheet->writeString($erow, $ecol++, $searchtitle, $format_title);
    } else {
        require_once 'PHPExcel/PHPExcel.php';

        PHPExcel_Settings::setCacheStorageMethod(PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp, array('memoryCacheSize' => '8MB'));

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("Competiscan")
                ->setLastModifiedBy("Competiscan")
                ->setTitle("Competiscan")
                ->setSubject("Competiscan")
                ->setDescription("Competiscan")
                ->setKeywords("Competiscan")
                ->setCategory("Competiscan");

        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->setTitle('Competiscan');

        $format_percent = array(
            'code' => '0.00%',
        );
        $format_number = array(
            'code' => '#,##0',
        );
        $format_dec = array(
            'code' => '#,##0.00',
        );
        $format_title = array(
            'font' => array(
                'italic' => true,
            ),
        );
        $format_head = array(
            'font' => array(
                'bold' => true,
                'underline' => PHPExcel_Style_Font::UNDERLINE_SINGLE,
            ),
        );
        $format_array = array();
        $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($ecol, $erow)->applyFromArray($format_title);
        $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol, $erow)->setValueExplicit(makeUTF($searchtitle), PHPExcel_Cell_DataType::TYPE_STRING);
    }
    $erow++;
    $ecol = 0;
}
$dma_note = "*DMA is a registered service mark of The Nielsen Company, all rights reserved. The DMA boundaries and DMA Data contained herein are owned solely and exclusively by, and are used herein pursuant to a license from The Nielsen Company. Any use and/or reproduction of these materials without the express written consent of the Nielsen Company is strictly prohibited. The DMA boundaries and DMA Data are effective for a period 2010 - 2011.";
$appendsArray = array();
if ($more >= 1) {
    if ($sess_plevel == 1 || $sess_plevel == 2 || !in_array('me_conp', $sess_search_exclude) || !in_array('me_prizm', $sess_search_exclude) || !in_array('DMA_CODE',$_SESSION['sess_search_exclude'])) {
        if ($sess_plevel == 1 || $sess_plevel == 2 || !in_array('me_conp', $sess_search_exclude)) {
            $appendsArray['postalcode'] = array('Zip/Postal Code' => array());
        }
        if ($sess_plevel == 1 || $sess_plevel == 2) {
            $appendsArray['ATP'] = array('ATP' => array());
            $appendsArray['Income360'] = array('Income360' => array());
            $appendsArray['DSDollar'] = array('DSDollar' => array());
            $appendsArray['DSI'] = array('DSI' => array());           
           
            $appendsArray['ECohort_Code'] = array('Financial Cohorts Code' => array(), 'Financial Cohorts Descriptor' => array('ECohort_Code', 0), 'Financial Cohorts Group' => array('ECohort_Code', 3)); //'ECohort_Desc'=>array('ECohort_Desc'=>array()), 'ECohort_Flag'=>array('ECohort_Flag'=>array())
            $appendsArray['PSY_FLAG'] = array('PSY_FLAG' => array());
            $appendsArray['PSY_CODE'] = array('PSY_CODE' => array(), 'PSY_CODE_DESC' => array('PSY_CODE', 0), 'PSY_GR' => array('PSY_CODE', 1), 'PSY_GR#' => array('PSY_CODE', 2));
        }
        if ($sess_plevel == 1 || $sess_plevel == 2 || !in_array('me_prizm', $sess_search_exclude)) {
            $appendsArray['PZM_FLAG'] = array('PZM_FLAG' => array());
            $appendsArray['PZM_CODE'] = array('PZM_CODE' => array(), 'PZM_CODE_DESC' => array('PZM_CODE', 0), 'PZM_GR' => array('PZM_CODE', 1), 'PZM_GR#' => array('PZM_CODE', 2));
        }
        if ($sess_plevel == 1 || $sess_plevel == 2) {
            $appendsArray['CNX_FLAG'] = array('CNX_FLAG' => array());
            $appendsArray['CNX_CODE'] = array('CNX_CODE' => array(), 'CNX_CODE_DESC' => array('CNX_CODE', 0), 'CNX_GR' => array('CNX_CODE', 1), 'CNX_GR#' => array('CNX_CODE', 2));
            $appendsArray['WC_Annuities'] = array('WealthComplete Annuities' => array());
            $appendsArray['WC_Stocks'] = array('WealthComplete Stocks' => array());
            $appendsArray['WC_Bonds'] = array('WealthComplete Bonds' => array());
            $appendsArray['WC_Deposits'] = array('WealthComplete Deposits' => array());
            $appendsArray['WC_MutualFunds'] = array('WealthComplete Mutual Funds' => array());
            $appendsArray['WC_Other'] = array('WealthComplete Other' => array());
            $appendsArray['WC_TotalAssets'] = array('WealthComplete Total Assets' => array());
            $appendsArray['WC_CD'] = array('WealthComplete CD' => array());
            $appendsArray['WC_InterestChecking'] = array('WealthComplete Interest Checking' => array());
            $appendsArray['WC_MoneyMarketDepositAccounts'] = array('WealthComplete Money Market Deposit Accounts' => array());
            $appendsArray['WC_NonInterestChecking'] = array('WealthComplete Non-Interest Checking' => array());
            $appendsArray['WC_OtherCheckingAccounts'] = array('WealthComplete Other Checking Accounts' => array());
            $appendsArray['WC_Savings'] = array('WealthComplete Savings' => array());
            $appendsArray['InvestylesAdviceOrientedAssets'] = array('Investyles Advice-Oriented Assets' => array());
            $appendsArray['InvestylesRetirementProductAssets'] = array('Investyles Retirement Product Assets' => array());
            $appendsArray['InvestylesSelfDirectedAssets'] = array('Investyles Self-Directed Assets' => array());
            $appendsArray['eSpectrum'] = array('eSpectrum' => array());
            $appendsArray['ET_ETHNICITY'] = array('ETHNICITY_DESC' => array('ETHNICITY_CODE', 0));
            $appendsArray['ET_RELIGION'] = array('RELIGION_DESC' => array('RELIGION_CODE', 0));
            $appendsArray['ET_LANGUAGE'] = array('LANGUAGE_DESC' => array('LANGUAGE_CODE', 0));
            $appendsArray['ET_GROUP'] = array('GROUP_DESC' => array('GROUP_CODE', 0));
            $appendsArray['ET_COUNTRY'] = array('COUNTRY_DESC' => array('COUNTRY_CODE', 0));
            $appendsArray['ET_ASSIMILATION'] = array('ASSIMILATION_DESC' => array('ASSIMILATION_CODE', 0));
            $appendsArray['ValueScore_for_Household'] = array('ValueScore for Household Code' => array(), 'ValueScore for Household' => array('ValueScore_for_Household', 0));
            $appendsArray['HH_Income_Index'] = array('HH Income Index' => array());
            $appendsArray['Birth_date_of_person_for_first_person_in_household'] = array('Birth date of person for first person in household' => array());
            $appendsArray['Income_Producing_Assets_Segment_Code'] = array('Income Producing Assets Segment Code      *R*' => array('Income_Producing_Assets_Segment_Code', 0));
            $appendsArray['Household_Income_Identifier_Narrow_Band'] = array('Household Income Identifier Narrow Band Code' => array(), 'Household Income Identifier Narrow Band' => array('Household_Income_Identifier_Narrow_Band', 0));
            $appendsArray['Advantage_Home__Owner_Renter_Code'] = array('Advantage Home  Owner / Renter Code' => array('Advantage_Home__Owner_Renter_Code', 0));
            $appendsArray['Advantage_Home_Owner_Renter_Level'] = array('Advantage Home Owner / Renter Level' => array());
        }
       if (!in_array('DMA_CODE', $sess_search_exclude)) {
            $appendsArray['DMA_CODE'] = array('DMA �*' => array(), 'DMA Name' => array('DMA_CODE', 0));
       }  
       if ($sess_plevel == 1 || $sess_plevel == 2) {
            $appendsArray['Gender_code'] = array('Gender code' => array('Gender_code', 0));
            $appendsArray['Occupation_code'] = array('Occupation code' => array());
            $appendsArray['MSA_CODE'] = array('MSA CODE' => array());
            $appendsArray['inq_win_past_6_mnths_except_promo_and_eval'] = array('# inq w/in past 6 mnths except promo and eval' => array());
            $appendsArray['Age_of_oldest_account_months'] = array('Age of oldest account (months)' => array());
            $appendsArray['Age_of_newest_account_months'] = array('Age of newest account (months)' => array());
            $appendsArray['of_accounts_opened_in_the_last_6_months'] = array('# of accounts opened in the last 6 months' => array());
            $appendsArray['of_accounts_opened_in_the_last_12_months'] = array('# of accounts opened in the last 12 months' => array());
            $appendsArray['of_accounts_opened_in_the_last_24_months'] = array('# of accounts opened in the last 24 months' => array());
            $appendsArray['of_accounts'] = array('# of accounts' => array());
            $appendsArray['of_active_accounts'] = array('# of active accounts' => array());
            $appendsArray['Total_credit_limit_for_active_accounts'] = array('Total credit limit for active accounts' => array());
            $appendsArray['of_accounts_currently_rated_satisfactory'] = array('# of accounts currently rated satisfactory' => array());
            $appendsArray['of_accounts_currently_bad_debt'] = array('# of accounts currently bad debt' => array());
            $appendsArray['Average_of_months_opened'] = array('Average # of months opened' => array());
            $appendsArray['of_active_accts_with_balance_50_limit'] = array('# of active accts with balance >= 50% limit' => array());
            $appendsArray['of_bank_revolving_accounts'] = array('# of bank revolving accounts' => array());
            $appendsArray['of_department_store_accounts'] = array('# of department store accounts' => array());
            $appendsArray['of_active_bank_revolving_accounts'] = array('# of active bank revolving accounts' => array());
            $appendsArray['active_dept_store_accts_wo_closed_narratives'] = array('# active dept store accts w/o closed narratives' => array());
            $appendsArray['Total_limit_for_active_bank_revolving_accts'] = array('Total limit for active bank revolving accts' => array());
            $appendsArray['Total_credit_limit_for_active_dept_store_accounts'] = array('Total credit limit for active dept store accounts' => array());
            $appendsArray['of_total_credit_union_accounts'] = array('# of total credit union accounts' => array());
            $appendsArray['Presence_of_Bankruptcy'] = array('Presence of Bankruptcy' => array());
            $appendsArray['accts_rated_bad_debt_of_derogatory24_mnths'] = array('# accts rated bad debt + # of derogatory-24 mnths' => array());
            $appendsArray['Age_of_oldest_active_mortgage'] = array('Age of oldest active mortgage' => array());
            $appendsArray['Balance_for_active_mortgage_accounts'] = array('Balance for active mortgage accounts' => array());
            $appendsArray['High_credit_for_active_mortgage_accounts'] = array('High credit for active mortgage accounts' => array());
            $appendsArray['Number_of_active_mortgage_accounts'] = array('Number of active mortgage accounts' => array());
            $appendsArray['RAPA_EMLC_ZIP_REL'] = array('ISO� Risk Quality Index Auto:  BG/ZIP' => array());
            $appendsArray['RAPA_EMLC_COUNTY_REL'] = array('ISO� Risk Quality Index Auto:  BG/County' => array());
            $appendsArray['RAPA_EMLC_STATE_REL'] = array('ISO� Risk Quality Index Auto:  BG/State' => array());
            $appendsArray['RAHO_HOMLC_ZIP_REL'] = array('ISO� Risk Quality Index Home:  BG/ZIP' => array());
            $appendsArray['RAHO_HOMLC_COUNTY_REL'] = array('ISO� Risk Quality Index Home:  BG/County' => array());
            $appendsArray['RAHO_HOMLC_STATE_REL'] = array('ISO� Risk Quality Index Home:  BG/State' => array());
        }
    }
    if ($is_excel > 0) {
        if ($more > 2 || $more == 1) {
            if ($is_excel == 2) {
                //add ppdate
                $worksheet->writeString($erow, $ecol++, 'Panelist Date', $format_head);
                $worksheet->writeString($erow, $ecol++, 'Month', $format_head);
                $worksheet->writeString($erow, $ecol++, 'Panelist ID', $format_head);              
                
                
            } else {
                //add ppdate
                $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit('Panelist Date', PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit('Month', PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit('Panelist ID', PHPExcel_Cell_DataType::TYPE_STRING);
            }
            
            /* ####  For FICO, Vantage, CreditVision Score #### */
            
            if ($is_excel == 2) {
                if (!empty($showheading['fico_score'])) {
                    $worksheet->writeString($erow, $ecol++, 'FICO Score', $format_head);
                }
                if (!empty($showheading['fico_range'])) {
                    $worksheet->writeString($erow, $ecol++, 'FICO Range', $format_head);
                }
                if (!empty($showheading['vantage_score'])) {
                    $worksheet->writeString($erow, $ecol++, 'VantageScore', $format_head);
                }
                if (!empty($showheading['vantage_range'])) {
                    $worksheet->writeString($erow, $ecol++, 'VantageScore Range', $format_head);
                }
                if (!empty($showheading['credit_vision'])) {
                    $worksheet->writeString($erow, $ecol++, 'CreditVision', $format_head);
                }
                if (!empty($showheading['credit_vision_range'])) {
                    $worksheet->writeString($erow, $ecol++, 'CreditVision Range', $format_head);
                }
            } else {
                if (!empty($showheading['fico_score'])) {
                    $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit('FICO Score', PHPExcel_Cell_DataType::TYPE_STRING);                
                }
                if (!empty($showheading['fico_range'])) {
                    $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit('FICO Range', PHPExcel_Cell_DataType::TYPE_STRING);                
                }
                if (!empty($showheading['vantage_score'])) {
                    $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit('VantageScore', PHPExcel_Cell_DataType::TYPE_STRING);                
                }
                if (!empty($showheading['vantage_range'])) {
                    $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit('VantageScore Range', PHPExcel_Cell_DataType::TYPE_STRING);                
                }
                if (!empty($showheading['credit_vision'])) {
                    $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit('CreditVision', PHPExcel_Cell_DataType::TYPE_STRING);                
                }
                if (!empty($showheading['credit_vision_range'])) {
                    $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit('CreditVision Range', PHPExcel_Cell_DataType::TYPE_STRING);                
                }
            }
           /* #### End For FICO, Vantage, CreditVision Score #### */ 
            
        }
        if ($sess_plevel == 1 || $sess_plevel == 2) {
            if ($more > 2 || $more == 1) {
                if ($is_excel == 2) {                    
                    
                    if (!empty($showheading['invitationID'])) {
                        $worksheet->writeString($erow, $ecol++, 'Invitation ID', $format_head);
                    }
                    if (!empty($showheading['trackingID'])) {
                        $worksheet->writeString($erow, $ecol++, 'Last 4 Digits', $format_head);
                    }
                    if (!empty($showheading['pproductFICO'])) {
                        $worksheet->writeString($erow, $ecol++, 'FICO', $format_head);
                    }
                } else {                    
                    
                    if (!empty($showheading['invitationID'])) {
                        $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit('Invitation ID', PHPExcel_Cell_DataType::TYPE_STRING);
                    }
                    if (!empty($showheading['trackingID'])) {
                        $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit('Last 4 Digits', PHPExcel_Cell_DataType::TYPE_STRING);
                    }
                    if (!empty($showheading['pproductFICO'])) {
                        $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit('FICO', PHPExcel_Cell_DataType::TYPE_STRING);
                    }
                }
            }
        }
        
        foreach ($appendsArray as $app => $appA) {
            if (!empty($showheading[$app])) {
                foreach ($appA as $ap => $aA) {
                    if ($is_excel == 2) {
                        if ($app == 'DMA_CODE') {
                            $ap .= ' (' . $dma_note . ')';
                            //$worksheet->writeNote($erow, $ecol, $dma_note); //only when not setVersion(8)
                        }
                        $worksheet->writeString($erow, $ecol, $ap, $format_head);
                    } else {
                        $ap = makeUTF($ap);
                        if ($app == 'DMA_CODE') {
                            $comment = $objPHPExcel->getActiveSheet()->getCommentByColumnAndRow($ecol, $erow);
                            $comment->setWidth('200pt'); // '96pt'
                            $comment->setHeight('175pt'); // '55.5pt'
                            $comment->setVisible(true);
                            $objCommentRichText = $comment->getText()->createText(makeUTF($dma_note));
                        }
                        $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol, $erow)->setValueExplicit(makeUTF($ap), PHPExcel_Cell_DataType::TYPE_STRING);
                    }
                    $ecol++;
                }
            }
        }
    } else {
        if ($more > 2 || $more == 1) {
            //add ppdate
            $header .= csvExcape('Panelist Date') . ",";
            $header .= csvExcape('Month') . ",";
            $header .= csvExcape('Panelist ID') . ",";
           
            /* ### For FICO, Vantage, CreditVision Score #### */
            if (!empty($showheading['fico_score'])) {
                $header .= csvExcape('FICO Score') . ",";
            }
             if (!empty($showheading['fico_range'])) {
                $header .= csvExcape('FICO Range') . ",";
            }
            if (!empty($showheading['vantage_score'])) {
                $header .= csvExcape('VantageScore') . ",";
            }
            if (!empty($showheading['vantage_range'])) {
                $header .= csvExcape('VantageScore Range') . ",";
            }
            if (!empty($showheading['credit_vision'])) {
                $header .= csvExcape('CreditVision') . ",";
            }
            if (!empty($showheading['credit_vision_range'])) {
                $header .= csvExcape('CreditVision Range') . ",";
            }
            /* ### End For FICO, Vantage, CreditVision Score #### */
            
        }
        if ($sess_plevel == 1 || $sess_plevel == 2) {
            if ($more > 2 || $more == 1) {               
                if (!empty($showheading['invitationID'])) {
                    $header .= csvExcape('InvitationID') . ",";
                }
                if (!empty($showheading['trackingID'])) {
                    $header .= csvExcape('Last 4 Digits') . ",";
                }
                if (!empty($showheading['pproductFICO'])) {
                    $header .= csvExcape('FICO') . ",";
                }
            }
        }
        foreach ($appendsArray as $app => $appA) {
            if (!empty($showheading[$app])) {
                foreach ($appA as $ap => $aA) {
                    if ($app == 'DMA_CODE') {
                        $ap .= ' (' . $dma_note . ')';
                    }
                    $header .= csvExcape($ap) . ",";
                }
            }
        }
        
       
    }
}
foreach ($heading as $k => $h) {
    if (!empty($showheading[$k])) {
        if ($is_excel > 0) {
            if ($is_excel == 2) {
                $worksheet->writeString($erow, $ecol++, $h, $format_head);
            } else {
                $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($h), PHPExcel_Cell_DataType::TYPE_STRING);
            }
        } else {
            $header .= csvExcape($h) . ",";
            if ($k == 'entryID') {
                $header .= csvExcape($heading["entryID"] . ' Link') . ",";
            }
        }
    }
}
require_once('admin/additionalDetails_latest.php');
foreach ($addlArray as $o) {
    while ($o->getNext()) {
        $field = $o->getField();
        if ($field != '' && (($more == 90 && ($o->id == 178 || $o->id == 179)) || ($more == 87 && $o->id == 87) || isset($showheading[$field . '_' . $o->id]))) {
            if ($is_excel > 0) {
                if ($is_excel == 2) {
                    $worksheet->writeString($erow, $ecol++, $o->label . ' - ' . $o->getTitle(), $format_head);
                } else {
                    $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($o->label . ' - ' . $o->getTitle()), PHPExcel_Cell_DataType::TYPE_STRING);
                }
            } else {
                $header .= csvExcape($o->label . ' - ' . $o->getTitle()) . ",";
            }
        }
    }
    $o->doReset();
}
/*###############START Direct Mail advertised##############*/
if (!empty($showheading['advertiser_address'])) {
    if($is_excel > 0){
        if ($is_excel == 2) {
            $worksheet->writeString($erow, $ecol++, 'Advertiser Address', $format_head);
            
        }else {
            $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit('Advertiser Address', PHPExcel_Cell_DataType::TYPE_STRING); 
        }   
    }else{
        $header .= csvExcape('Advertiser Address') . ","; 
    }
}
if (!empty($showheading['advertiser_city'])) {
    if($is_excel > 0){
        if ($is_excel == 2) {
            $worksheet->writeString($erow, $ecol++, 'Advertiser City', $format_head);
            
        }else {
            $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit('Advertiser City', PHPExcel_Cell_DataType::TYPE_STRING); 
        }   
    }else{
        $header .= csvExcape('Advertiser City') . ","; 
    }
}
if (!empty($showheading['advertiser_state'])) {
    if($is_excel > 0){
        if ($is_excel == 2) {
            $worksheet->writeString($erow, $ecol++, 'Advertiser State/Province', $format_head);
            
        }else {
            $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit('Advertiser State/Province', PHPExcel_Cell_DataType::TYPE_STRING); 
        }   
    }else{
        $header .= csvExcape('Advertiser State/Province') . ","; 
    }
}
if (!empty($showheading['advertiser_zipcode'])) {
    if($is_excel > 0){
        if ($is_excel == 2) {
            $worksheet->writeString($erow, $ecol++, 'Advertiser Zip/Postal Code', $format_head);
            
        }else {
            $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit('Advertiser Zip/Postal Code', PHPExcel_Cell_DataType::TYPE_STRING); 
        }   
    }else{
        $header .= csvExcape('Advertiser Zip/Postal Code') . ","; 
    }
}
if (!empty($showheading['advertiser_phone_number'])) {
    if($is_excel > 0){
        if ($is_excel == 2) {
            $worksheet->writeString($erow, $ecol++, 'Advertiser Phone Number', $format_head);
            
        }else {
            $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit('Advertiser Phone Number', PHPExcel_Cell_DataType::TYPE_STRING); 
        }   
    }else{
        $header .= csvExcape('Advertiser Phone Number') . ","; 
    }
}
if (!empty($showheading['advertiser_url'])) {
    if($is_excel > 0){
        if ($is_excel == 2) {
            $worksheet->writeString($erow, $ecol++, 'Advertiser URL', $format_head);
            
        }else {
            $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit('Advertiser URL', PHPExcel_Cell_DataType::TYPE_STRING); 
        }   
    }else{
        $header .= csvExcape('Advertiser URL') . ","; 
    }
}
/*###############END Direct Mail advertised##############*/
/*####### START NEW PROMOTION FIELD #########*/
if (!empty($showheading['promotional_field'])) {
    $arraypromotion=array('Company','Product Type','Promotion Type','Coupon/Discount Value','Ad Price($)','Regular Price ($)','Shipping Detail','Online/In-Store','Qualifier','Qualifier (Minimum Purchase Value $)','Code Required','BOGO','Buy[X]','Get[X]');
    if($is_excel > 0){
        if ($is_excel == 2) {
            //$worksheet->writeString($erow, $ecol++, 'Promotion Company', $format_head);
            $worksheet->writeString($erow, $ecol++, 'Holiday', $format_head);
            $worksheet->writeString($erow, $ecol++, 'Sale Type', $format_head);
        } else {
            //$objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit('Promotion Company', PHPExcel_Cell_DataType::TYPE_STRING);
            $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit('Holiday', PHPExcel_Cell_DataType::TYPE_STRING); 
            $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit('Sale Type', PHPExcel_Cell_DataType::TYPE_STRING); 
        }
        
        if ($is_excel == 2) {
            for($i=1; $i <=10; $i++){
                for($j=0;$j<count($arraypromotion); $j++){
                    if($i<=10){
                        $promo_colomn="Promotion #".$i." - ".$arraypromotion[$j];
                        $worksheet->writeString($erow, $ecol++, $promo_colomn, $format_head);
                    }  
                }
            }
        }else{
            for($i=1; $i <=10; $i++){
                for($j=0;$j<count($arraypromotion); $j++){
                    if($i<=10){
                        $promo_colomn="Promotion #".$i." - ".$arraypromotion[$j];
                        $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit($promo_colomn, PHPExcel_Cell_DataType::TYPE_STRING); 
                    }  
                }
            }
        }
    }else{
        //$header .= csvExcape('Promotion Company') . ",";
        $header .= csvExcape('Holiday') . ","; 
        $header .= csvExcape('Sale Type') . ",";
        for($i=1; $i <=10; $i++){
            for($j=0;$j<count($arraypromotion); $j++){
                if($i<=10){
                    $promo_colomn="Promotion #".$i." - ".$arraypromotion[$j];
                    $header .= csvExcape($promo_colomn) . ",";
                }  
            }
        }
    }
}
/*####### END NEW PROMOTION FIELD #########*/

$header = substr($header, 0, -1) . "\n";
if ($is_excel == 1) {
    $objPHPExcel->getActiveSheet()->getStyle('A' . $erow . ':' . PHPExcel_Cell::stringFromColumnIndex($ecol - 1) . $erow)->applyFromArray($format_head);
}
$erow++;

@ob_end_clean();
if ($is_excel > 0) {
    if ($file_choice == 3) {
        $exceltype = 'Excel2007';
        $filename = 'Competiscan_Export_' . date('Y-m-d') . '.xlsx';
    } else {
        $exceltype = 'Excel5';
        $filename = 'Competiscan_Export_' . date('Y-m-d') . '.xls';
    }
    /* header('Content-Type: application/vnd.ms-excel'); //Excel2007 application/vnd.openxmlformats-officedocument.spreadsheetml.sheet
      header('Content-Disposition: attachment;filename="$filename"');
      header('Cache-Control: max-age=0');
      header("Pragma: no-cache");
      header("Expires: 0"); */
} else {
    $exceltype = 'csv';
    $filename = "Competiscan_Export_" . date('Y-m-d') . ".csv";

    $handle = fopen($filepath, 'w');
    if ($handle) {
        fwrite($handle, $header);
    }
}

if ($ssid > 0 || $bid >= 0) {

    //list($orderby,$dorelev,$doexpans) = doQuerySort_test3($sort);
    list($orderby, $dorelev, $doexpans) = doQuerySort($sort);
    if ($bid >= 0) {
        //list($sql) = doQuerytestsphinx(0, false, '', false, $bid, false,false,false,false,-1,array(),$sess_userID);

        list($sql) = doQuery_latest2(0, false, '', false, $bid, false, false, false, false, -1, array(), $sess_userID);
    } else {
        //pd.productID as theproductID,mChannelID,mPanelID,productHeadline,sectorID,categoryID,subCategoryID,entryID,addedToDatabase,company,productName,incentive,compaignLanguage,firstSeen,lastSeen,mTypeID,state,agentCommunicationID
        //list($sql) = doQuerytestsphinx($ssid,false,'',false,-1,$dorelev,$doexpans, false,false,-1,array(),$sess_userID);
        list($sql) = doQuery_latest2($ssid, false, '', false, -1, $dorelev, $doexpans, false, false, -1, array(), $sess_userID);
    }

    $sql .= $orderby;
    $search_num_of_rows = 0;
    $curr_num_of_rows = 0;

    if ($page > 0) {

        if ($bid >= 0) {
            //list($countQuery) = doQuerytestsphinx(0, true, '', false, $bid, false,false,false,false,-1,array(),$sess_userID);
            list($countQuery) = doQuery_latest2(0, true, '', false, $bid, false, false, false, false, -1, array(), $sess_userID);
        } else {
            // list($countQuery) = doQuerytestsphinx($ssid,true, '', false,-1, false,false,false,false,-1,array(),$sess_userID);

            list($countQuery) = doQuery_latest2($ssid, true, '', false, -1, false, false, false, false, -1, array(), $sess_userID);
        }

        $count_result = $DRW->query($countQuery, $DRW_read);
        $count = $DRW->fetch_row($count_result);
        $search_num_of_rows = $count[0];
        @$DRW->free_result($count_result);
        $curr_num_of_rows = 0;
        $change = 10;
        $curr_change_row = 0;

        $a = new Paginator_html($page, $search_num_of_rows);

        #set limit on the current page.
        $a->set_Limit(30);

        $limit1 = $a->getRange1();
        #Get the number of items displayed on page.
        $limit2 = $a->getRange2();

        if ($topCompany > 0) {
            $limit2 = $topCompany;
        }

        $sql .= " Limit $limit1 , $limit2";
    } elseif ($topCompany > 0) {
        $sql .= " Limit 0," . $topCompany;
    } elseif ($file_choice == 1) {
        $sql .= " Limit 0,65000";
    } elseif ($file_choice == 3) {
        $sql .= " Limit 0,1048574";
    }

    //$sql.=") D".$sortby;
    //$sql .= " ) D ".$orderby; 

    $state = '';
    $gender = '';
    $age = '';
    $income_mult = '';
    $DMA_ID_mult = '';
    $edc_id_mult = '';
    $search_competi_id = '';
    $ppdatetext = '';
    $dmajoin = '';
    $edcjoin = '';
    $awhere = '';

    if ($ssid > 0) {
        $sqlc = "SELECT addedToDatabase,month1,month2,search_panelist_date,state,gender,age,income_mult,DMA_ID_mult,search_competi_id,edc_id_mult FROM cscan_search WHERE ID='" . $ssid . "'";
        $rsc = $DRW->query($sqlc, $DRW_read);
        $dataC = $DRW->fetch_row($rsc);
        $addedToDatabase = $dataC[0];
        $month1 = $dataC[1];
        $month2 = $dataC[2];
        $search_panelist_date = $dataC[3];
        $state = $dataC[4];
        $gender = $dataC[5];
        $age = $dataC[6];
        $income_mult = $dataC[7];
        $DMA_ID_mult = $dataC[8];
        $search_competi_id = $dataC[9];
        $edc_id_mult = $dataC[10];
        @$DRW->free_result($rsc);
    } else {
        $addedToDatabase = $eb_date1;
        $month1 = $eb_date2;
        $month2 = $eb_date3;
        $search_panelist_date = 0;
        $state = $eb_state;
        $gender = $eb_gender;
        $age = $eb_age;
        $income_mult = $eb_income;
        $DMA_ID_mult = $eb_DMA_ID;
        if (!empty($addedToDatabase) || !empty($month1) || !empty($month2) || !empty($state) || !empty($gender) || !empty($age) || !empty($income_mult) || !empty($DMA_ID_mult)) {
            $do_bid = true;
        }
    }
    if ($month1 != '' || $month2 != '') {
        $month = "$month1,$month2";
    } else {
        $month = '';
    }
    if ($search_panelist_date || $consumer_only || $do_bid) {
        // comment show ppeve as ppdate
        /*if ($addedToDatabase != '') {
            if ($addedToDatabase == 'week')
                $ppdatetext .= ' pp.ppdate>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 7 DAY),\' 00:00:00\') AND ';
            elseif ($addedToDatabase == '2week')
                $ppdatetext .= ' pp.ppdate>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 14 DAY),\' 00:00:00\') AND ';
            elseif ($addedToDatabase == '1month')
                $ppdatetext .= ' pp.ppdate>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 1 MONTH),\' 00:00:00\') AND ';
            elseif ($addedToDatabase == '3month')
                $ppdatetext .= ' pp.ppdate>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 3 MONTH),\' 00:00:00\') AND ';
            elseif ($addedToDatabase == '6month')
                $ppdatetext .= ' pp.ppdate>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 6 MONTH),\' 00:00:00\') AND ';
            elseif ($addedToDatabase == '1year')
                $ppdatetext .= ' pp.ppdate>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 1 YEAR),\' 00:00:00\') AND ';
        }
        elseif ($month != '') {
            $monthArray = explode(',', $month);
            $month_1 = $monthArray[0];
            $month_2 = $monthArray[1];
            if ($month_1 == '') {
                $month_1 = $month_2;
            } elseif ($month_2 == '') {
                $month_2 = $month_1;
            }
            $ppdatetext .= " (pp.ppdate BETWEEN '$month_1-01 00:00:00' AND '$month_2-31 23:59:59') AND ";
        } */
        if (!empty($state)) {
            $tmpArray = explode(',', $state);
            $ppdatetext .= " (";
            foreach ($tmpArray as $v) {
                if ($v != '') {
                    $ppdatetext .= " pp.ppstateID=" . (int) $v . " OR ";
                }
            }
            $ppdatetext = substr($ppdatetext, 0, -4);
            $ppdatetext .= ") AND ";
        }
        if (!empty($gender)) {
            $ppdatetext .= " pp.pgender='$gender' AND ";
        }
        $mult = array('ppageID' => $age, 'pincomeID' => $income_mult, 'dmap.code' => $DMA_ID_mult, 'edc_id' => $edc_id_mult);
        foreach ($mult as $field => $val) {
            if ($val != '') {
                $tmpwhere = '';
                $tmpArray = explode(',', $val);
                foreach ($tmpArray as $v) {
                    if ($v != '') {
                        if ($field == 'dmap.code') {
                            $tmpwhere .= " $field='" . $v . "' OR ";
                        } else {
                            $tmpwhere .= " $field=" . (int) $v . " OR ";
                        }
                    }
                }
                if ($field == 'isBiz') {
                    $awhere .= $tmpwhere;
                } else {
                    if ($field == 'dmap.code') {
                        $dmajoin = ' JOIN cscan_dma_code_postalcode dmap ON (pp.pppostalcode=dmap.pppostalcode)';
                        $ppdatetext .= " (" . substr($tmpwhere, 0, -4) . ") AND ";
                    } elseif ($field == 'edc_id') {
                        $temptable = "CREATE TEMPORARY TABLE `EDCTempTable` (
							panelist_id int(10) unsigned NOT NULL DEFAULT '0',
							PRIMARY KEY (panelist_id)
						)";
                        $DRW->query($temptable, $DRW_main);
                        $edcq = 'SELECT DISTINCT panelist_id as distinct_panelist_id FROM cscan_panelists_product JOIN cscan_edc_postalcode ON(cscan_panelists_product.pppostalcode=cscan_edc_postalcode.pppostalcode) WHERE (' . substr($tmpwhere, 0, -4) . ')';
                        $edcrows = $DRW->query($edcq, $DRW_read);
                        while ($edcrs = $DRW->fetch_row($edcrows)) {
                            $DRW->query("INSERT INTO EDCTempTable (panelist_id) VALUES ('" . $DRW->real_escape_string($edcrs[0]) . "')", $DRW_main);
                        }
                        $edcjoin = ' JOIN EDCTempTable ON (pp.panelist_id=EDCTempTable.panelist_id)';
                    }
                }
            }
        }
        if ($awhere != '') {
            $ppdatetext .= " (" . substr($awhere, 0, -4) . ") AND ";
        }
        if ($search_competi_id != '') {
            $vs = explode(',', $search_competi_id);
            $competi_ids = array();
            foreach ($vs as $v) {
                $competi_ids[] = "'" . $DRW->real_escape_string(trim($v)) . "'";
            }
            $panelist_ids = array();
            $sqlc = "SELECT panelist_id FROM cscan_panelists WHERE competi_id IN (" . implode(',', $competi_ids) . ")";
            $rsc = $DRW->query($sqlc, $DRW_read);
            while ($rowc = $DRW->fetch_row($rsc)) {
                $panelist_ids[] = $rowc[0];
            }
            @$DRW->free_result($rsc);
            unset($sqlc);
            unset($rowc);
            unset($rsc);
            if (count($panelist_ids) == 0) {
                $panelist_ids[] = '-1';
            }
            $ppdatetext .= " pp.panelist_id IN (" . implode(',', $panelist_ids) . ") AND ";
        }
    }

    /* $output = shell_exec('mysql -h10.0.0.190 -uroot -p"root@20165" competi_competidblatest -e"'.$sql.'" > /tmp/directdb.csv');

      $file = fopen("/tmp/directdb.csv","r");
      $data=fgetcsv($file);
      print_r($data);
      print_r($heading);
      die;
      fclose($file);

     */
    //echo $sql; die;
    $rs = $DRW->query($sql, $DRW_read);
    $updcnt = -1;
    if ($page <= 0) {
        $search_num_of_rows = $DRW->num_rows($rs);
    }
    while ($row = $DRW->fetch_assoc($rs)) {
        $pct_complete = round(($curr_num_of_rows / $search_num_of_rows) * 100);
        //echo $pct_complete."===".$curr_num_of_rows."==".$search_num_of_rows;
        //echo "<br/>";
        if ($updcnt != $pct_complete) {
            $sqlu = "UPDATE cscan_progress SET pct=$pct_complete where userID=$sess_userID";
            $DRW->query($sqlu, $DRW_main);
            $updcnt = $pct_complete;
        }
        $curr_num_of_rows++;
        $is_mv = true;
        $panelistCheck = array('pid' => array(), 'ppstateID' => array(), 'pgender' => array(), 'ppageID' => array(), 'pincomeID' => array(), 'pppostalcode' => array());
        $mult = array();
        $panelist_info = '';
        if ($more > 0 || (($search_panelist_date || $consumer_only || $do_bid) && ($showheading['state'] || $showheading['gender'] || $showheading['age'] || $showheading['income'] || $showheading['DMA_ID']))) {
            $is_mv = false;
            //add ppdate
            /*$sql_P = "SELECT DISTINCT DATE_FORMAT(ppdate,'%Y-%m'),competi_id,invitationID,pp.panelist_id,cp.stateID as ppstateID,pp.pgender,pp.ppageID,pp.pincomeID,pp.pppostalcode,pp.ppmv,trackingID,pp.ppmv_m,dmap.code,pproductFICO,DATEDIFF(CURDATE(),cp.birthdate) as agedays,cp.stateID,cp.incomeID,DATE_FORMAT(ppdate,'%Y-%m-%d') as ppadddate
				FROM cscan_panelists_product pp LEFT JOIN cscan_dma_code_postalcode dmap ON (pp.pppostalcode=dmap.pppostalcode)$edcjoin, cscan_panelists cp WHERE pp.panelist_id=cp.panelist_id AND {$ppdatetext}productID=" . $row['theproductID'];*/
            $sql_P = "SELECT DISTINCT DATE_FORMAT(ppdate,'%Y-%m'),competi_id,invitationID,pp.panelist_id,cp.stateID as ppstateID,pp.pgender,pp.ppageID,pp.pincomeID,pp.pppostalcode,SUM(pp.ppmv),trackingID,SUM(pp.ppmv_m),dmap.code,pproductFICO,DATEDIFF(CURDATE(),cp.birthdate) as agedays,cp.stateID,cp.incomeID,DATE_FORMAT(ppdate,'%Y-%m-%d') as ppadddate
				FROM cscan_panelists_product pp LEFT JOIN cscan_dma_code_postalcode dmap ON (pp.pppostalcode=dmap.pppostalcode)$edcjoin, cscan_panelists cp WHERE pp.panelist_id=cp.panelist_id AND {$ppdatetext}productID=" . $row['theproductID']." GROUP BY pp.panelist_id";
            $resultC = $DRW->query($sql_P, $DRW_read);
            while ($dataC = $DRW->fetch_row($resultC)) {
                if ($more > 0) {
                    if ($more == 2) {
                        $panelist_info .= "$dataC[0],$dataC[1];";
                    } else {
                        $mult[] = $dataC;
                    }
                }
                $panelistCheck['pid'][] = $dataC[3];
                $panelistCheck['ppstateID'][] = $dataC[4];
                $panelistCheck['pgender'][] = $dataC[5];
                $panelistCheck['ppageID'][] = $dataC[6];
                $panelistCheck['pincomeID'][] = $dataC[7];
                $panelistCheck['pppostalcode'][] = str_replace("'", '', $dataC[8]);
                $panelistCheck['p_dma_code'][] = $dataC[12];
                if (!empty($dataC[9]) || (!empty($dataC[11]) && $sess_userID == 8089)) {
                    $is_mv = true;
                }
            }
            @$DRW->free_result($resultC);
        }
        ########### for export data from the empty panaleist condition ##############
        //if(count($mult)==0 && (!empty($showheading['mailvolume']) || $is_mv)){
        if (count($mult) == 0) {

            $mult[] = array('', '', '', 0, '', '', '', '', '', '', '', '', '', '','','','');
        }
        //echo "<pre>";
        //print_r($panelistCheck['ppstateID']); die;
        foreach ($mult as $pdata) {
            $ecol = 0;
            $line = '';
            $appendsData = array();
            $afields = implode(',', array_keys($appendsArray));
            if (!empty($pdata[3])) {
                $p_pdate = $pdata[0];
                $competi_id = $pdata[1];
                $invitationID = $pdata[2];
                $pid = $pdata[3];
                $ppstateID = $pdata[4];
                $pgender = $pdata[5];
                $ppageID = $pdata[6];
                $pincomeID = $pdata[7];
                $pppostalcode = str_replace("'", '', $pdata[8]);
                $panelist_idtext = 'pp.panelist_id=' . $pid . ' AND ';
                $p_mailvolume = $pdata[9];
                $trackingID = $pdata[10];
                if (isset($pdata[11]) && $sess_userID == 8089) {
                    $p_mailvolume = $pdata[11];
                }
                $p_dma_code = $pdata[12];
                $pproductFICO = $pdata[13];
                if ($p_mailvolume > 0) {
                    $p_mailpieces = 1;
                } else {
                    $p_mailpieces = 0;
                }
                if($ppageID<=0){
                $ppage=floor($pdata[14] / 365);
                $ageObj = new \HS\Age($DRW);
                $ageObj->setAge($ppage);
                $ppageID = $ageObj->getGroupsAsCommaDelimitedString($ppage);
                } 
                if($ppstateID<=0){
                    $ppstateID=(int) $pdata[15];
                }
                if($pincomeID<=0){
                    $pincomeID=(int) $pdata[16];
                }
                $ppadddate=$pdata[17];
                /* ####  For FICO, Vantage, CreditVision Score #### */
                $sql_additional = $DRW->query("SELECT fico_score,vantage_score,credit_vision FROM cscan_panelists_additional_score where LEFT(score_date,7)='".$p_pdate."' and panelist_id='".$pid."' AND (fico_score!='' OR vantage_score!='' OR credit_vision!='') order by created_date desc limit 1", $DRW_read);
                //$sql_additional = $DRW->query("SELECT fico_score,vantage_score,credit_vision FROM cscan_panelists_additional_score where LEFT(score_date,7)='".$p_pdate."' and panelist_id='".$pid."' order by created_date desc limit 1", $DRW_read);
                if ($DRW->num_rows($sql_additional) > 0) {
                    $additional_result = $DRW->fetch_row($sql_additional);
                    $fico_score = $additional_result[0];
                    $vantage_score = $additional_result[1];
                    $credit_vision = $additional_result[2];
                    if($fico_score>=300 && $fico_score <=324){
                        $fico_score=$fico_score;	
                        $fico_range='300-324';	
                    }elseif($fico_score>=325 && $fico_score<=349){
                        $fico_score=$fico_score;	
                        $fico_range='325-349';	
                    }elseif($fico_score>=350 && $fico_score<=374){
                        $fico_score=$fico_score;	
                        $fico_range='350-374';	
                    }elseif($fico_score>=375 && $fico_score<=399){
                        $fico_score=$fico_score;	
                        $fico_range='375-399';	
                    }elseif($fico_score>=400 && $fico_score<=424){
                        $fico_score=$fico_score;	
                        $fico_range='400-424';	
                    }elseif($fico_score>=425 && $fico_score<=449){
                        $fico_score=$fico_score;	
                        $fico_range='425-449';	
                    }elseif($fico_score>=450 && $fico_score <=474){
                        $fico_score=$fico_score;	
                        $fico_range='450-474';	
                    }elseif($fico_score>=475 && $fico_score <=499){
                        $fico_score=$fico_score;	
                        $fico_range='475-499';	
                    }elseif($fico_score>=500 && $fico_score <=524){
                        $fico_score=$fico_score;	
                        $fico_range='500-524';	
                    }elseif($fico_score>=525 && $fico_score <=549){
                        $fico_score=$fico_score;	
                        $fico_range='525-549';	
                    }elseif($fico_score>=550 && $fico_score <=574){
                        $fico_score=$fico_score;	
                        $fico_range='550-574';	
                    }elseif($fico_score>=575 && $fico_score <=599){
                        $fico_score=$fico_score;	
                        $fico_range='575-599';	
                    }elseif($fico_score>=600 && $fico_score <=624){
                        $fico_score=$fico_score;	
                        $fico_range='600-624';	
                    }elseif($fico_score>=625 && $fico_score <=649){
                        $fico_score=$fico_score;	
                        $fico_range='625-649';	
                    }elseif($fico_score>=650 && $fico_score <=674){
                        $fico_score=$fico_score;	
                        $fico_range='650-674';	
                    }elseif($fico_score>=675 && $fico_score <=699){
                        $fico_score=$fico_score;	
                        $fico_range='675-699';	
                    }elseif($fico_score>=700 && $fico_score <=724){
                        $fico_score=$fico_score;	
                        $fico_range='700-724';	
                    }elseif($fico_score>=725 && $fico_score <=749){
                        $fico_score=$fico_score;	
                        $fico_range='725-749';	
                    }elseif($fico_score>=750 && $fico_score <=774){
                        $fico_score=$fico_score;	
                        $fico_range='750-774';	
                    }elseif($fico_score>=775 && $fico_score <=799){
                        $fico_score=$fico_score;	
                        $fico_range='775-799';	
                    }elseif($fico_score>=800 && $fico_score <=824){
                        $fico_score=$fico_score;	
                        $fico_range='800-824';	
                    }elseif($fico_score>=825 && $fico_score <=850){
                        $fico_score=$fico_score;	
                        $fico_range='825-850';	
                    }else{
                        $fico_score='';	
                        $fico_range='';
                    }
                    //vantage
                    if($vantage_score>=300 && $vantage_score <=324){
                        $vantage_score=$vantage_score;	
                        $vantage_range='300-324';	
                    }elseif($vantage_score>=325 && $vantage_score<=349){
                        $vantage_score=$vantage_score;	
                        $vantage_range='325-349';	
                    }elseif($vantage_score>=350 && $vantage_score<=374){
                        $vantage_score=$vantage_score;	
                        $vantage_range='350-374';	
                    }elseif($vantage_score>=375 && $vantage_score<=399){
                        $vantage_score=$vantage_score;	
                        $vantage_range='375-399';	
                    }elseif($vantage_score>=400 && $vantage_score<=424){
                        $vantage_score=$vantage_score;	
                        $vantage_range='400-424';	
                    }elseif($vantage_score>=425 && $vantage_score<=449){
                        $vantage_score=$vantage_score;	
                        $vantage_range='425-449';	
                    }elseif($vantage_score>=450 && $vantage_score <=474){
                        $vantage_score=$vantage_score;	
                        $vantage_range='450-474';	
                    }elseif($vantage_score>=475 && $vantage_score <=499){
                        $vantage_score=$vantage_score;	
                        $vantage_range='475-499';	
                    }elseif($vantage_score>=500 && $vantage_score <=524){
                        $vantage_score=$vantage_score;	
                        $vantage_range='500-524';	
                    }elseif($vantage_score>=525 && $vantage_score <=549){
                        $vantage_score=$vantage_score;	
                        $vantage_range='525-549';	
                    }elseif($vantage_score>=550 && $vantage_score <=574){
                        $vantage_score=$vantage_score;	
                        $vantage_range='550-574';	
                    }elseif($vantage_score>=575 && $vantage_score <=599){
                        $vantage_score=$vantage_score;	
                        $vantage_range='575-599';	
                    }elseif($vantage_score>=600 && $vantage_score <=624){
                        $vantage_score=$vantage_score;	
                        $vantage_range='600-624';	
                    }elseif($vantage_score>=625 && $vantage_score <=649){
                        $vantage_score=$vantage_score;	
                        $vantage_range='625-649';	
                    }elseif($vantage_score>=650 && $vantage_score <=674){
                        $vantage_score=$vantage_score;	
                        $vantage_range='650-674';	
                    }elseif($vantage_score>=675 && $vantage_score <=699){
                        $vantage_score=$vantage_score;	
                        $vantage_range='675-699';	
                    }elseif($vantage_score>=700 && $vantage_score <=724){
                        $vantage_score=$vantage_score;	
                        $vantage_range='700-724';	
                    }elseif($vantage_score>=725 && $vantage_score <=749){
                        $vantage_score=$vantage_score;	
                        $vantage_range='725-749';	
                    }elseif($vantage_score>=750 && $vantage_score <=774){
                        $vantage_score=$vantage_score;	
                        $vantage_range='750-774';	
                    }elseif($vantage_score>=775 && $vantage_score <=799){
                        $vantage_score=$vantage_score;	
                        $vantage_range='775-799';	
                    }elseif($vantage_score>=800 && $vantage_score <=824){
                        $vantage_score=$vantage_score;	
                        $vantage_range='800-824';	
                    }elseif($vantage_score>=825 && $vantage_score <=850){
                        $vantage_score=$vantage_score;	
                        $vantage_range='825-850';	
                    }else{
                        $vantage_score='';	
                        $vantage_range='';
                    }
                    //creditvision
                    if($credit_vision>=300 && $credit_vision <=324){
                        $credit_vision=$credit_vision;	
                        $credit_range='300-324';	
                    }elseif($credit_vision>=325 && $credit_vision<=349){
                        $credit_vision=$credit_vision;	
                        $credit_range='325-349';	
                    }elseif($credit_vision>=350 && $credit_vision<=374){
                        $credit_vision=$credit_vision;	
                        $credit_range='350-374';	
                    }elseif($credit_vision>=375 && $credit_vision<=399){
                        $credit_vision=$credit_vision;	
                        $credit_range='375-399';	
                    }elseif($credit_vision>=400 && $credit_vision<=424){
                        $credit_vision=$credit_vision;	
                        $credit_range='400-424';	
                    }elseif($credit_vision>=425 && $credit_vision<=449){
                        $credit_vision=$credit_vision;	
                        $credit_range='425-449';	
                    }elseif($credit_vision>=450 && $credit_vision <=474){
                        $credit_vision=$credit_vision;	
                        $credit_range='450-474';	
                    }elseif($credit_vision>=475 && $credit_vision <=499){
                        $credit_vision=$credit_vision;	
                        $credit_range='475-499';	
                    }elseif($credit_vision>=500 && $credit_vision <=524){
                        $credit_vision=$credit_vision;	
                        $credit_range='500-524';	
                    }elseif($credit_vision>=525 && $credit_vision <=549){
                        $credit_vision=$credit_vision;	
                        $credit_range='525-549';	
                    }elseif($credit_vision>=550 && $credit_vision <=574){
                        $credit_vision=$credit_vision;	
                        $credit_range='550-574';	
                    }elseif($credit_vision>=575 && $credit_vision <=599){
                        $credit_vision=$credit_vision;	
                        $credit_range='575-599';	
                    }elseif($credit_vision>=600 && $credit_vision <=624){
                        $credit_vision=$credit_vision;	
                        $credit_range='600-624';	
                    }elseif($credit_vision>=625 && $credit_vision <=649){
                        $credit_vision=$credit_vision;	
                        $credit_range='625-649';	
                    }elseif($credit_vision>=650 && $credit_vision <=674){
                        $credit_vision=$credit_vision;	
                        $credit_range='650-674';	
                    }elseif($credit_vision>=675 && $credit_vision <=699){
                        $credit_vision=$credit_vision;	
                        $credit_range='675-699';	
                    }elseif($credit_vision>=700 && $credit_vision <=724){
                        $credit_vision=$credit_vision;	
                        $credit_range='700-724';	
                    }elseif($credit_vision>=725 && $credit_vision <=749){
                        $credit_vision=$credit_vision;	
                        $credit_range='725-749';	
                    }elseif($credit_vision>=750 && $credit_vision <=774){
                        $credit_vision=$credit_vision;	
                        $credit_range='750-774';	
                    }elseif($credit_vision>=775 && $credit_vision <=799){
                        $credit_vision=$credit_vision;	
                        $credit_range='775-799';	
                    }elseif($credit_vision>=800 && $credit_vision <=824){
                        $credit_vision=$credit_vision;	
                        $credit_range='800-824';	
                    }elseif($credit_vision>=825 && $credit_vision <=850){
                        $credit_vision=$credit_vision;	
                        $credit_range='825-850';	
                    }else{
                        $credit_vision='';	
                        $credit_range='';
                    }
                }else{
                    $fico_score = '';
                    $vantage_score = '';
                    $credit_vision = '';
                    $fico_range = '';
                    $vantage_range = '';
                    $credit_range = '';
                }
                
                /* #### End For FICO, Vantage, CreditVision Score #### */
                
                if (count($appendsArray) > 0) {
                    $sql_P = "SELECT $afields FROM cscan_panelists cp LEFT JOIN cscan_panelists_appends pa ON (cp.panelist_id=pa.panelist_id) WHERE cp.panelist_id=$pid ";
                    $resultC = $DRW->query($sql_P, $DRW_read);
                    $dataC = $DRW->fetch_row($resultC);
                    $dataCi = 0;
                    foreach ($appendsArray as $app => $appA) {
                        $val = $dataC[$dataCi++];
                        if (!empty($showheading[$app])) {
                            if ($app == 'DMA_CODE' && !empty($p_dma_code)) {
                                $val = $p_dma_code;
                            }
                            foreach ($appA as $ap => $aA) {
                                if (count($aA) == 0) {
                                    $appendsData[] = $val;
                                } else {
                                    $appendsData[] = getAppendedDescrition($aA[0], $val, $aA[1]);
                                }
                            }
                        }
                    }
                    @$DRW->free_result($resultC);
                }
            } else {
                $ppadddate="";
                $p_pdate = '';
                $competi_id = '';
                $invitationID = '';
                $pid = 0;
                $ppstateID = 0;
                $pgender = '';
                $ppageID = 0;
                $pincomeID = 0;
                $pppostalcode = '';
                $panelist_idtext = '';
                $p_mailvolume = 0;
                $p_mailpieces = 0;
                $trackingID = '';
                $pproductFICO = '';
                $p_dma_codeArray = array();
                /* ####  For Real Time Mail Volume #### */
                $p_realtime_mailvolume = 0;
                $mail_volume_tot_real_time=0;
                /* ####  End For Real Time Mail Volume #### */
                
                
                /* ####  For FICO, Vantage, CreditVision Score #### */
                
                $fico_score='';
                $vantage_score ='';
                $credit_vision ='';
                $fico_range='';
                $credit_range='';
                $vantage_range='';
                
                /* #### End For FICO, Vantage, CreditVision Score #### */
                
                if (count($panelistCheck['pid']) > 0) {
                    $mult_vals = array();
                    if (count($appendsArray) > 0) {
                        $ps = '';
                        foreach ($panelistCheck['pid'] as $p) {
                            if ($ps != '') {
                                $ps .= ',';
                            }
                            $ps .= "'" . $DRW->real_escape_string($p) . "'";
                        }
                        $sql_P = "SELECT $afields FROM cscan_panelists cp LEFT JOIN cscan_panelists_appends pa ON (cp.panelist_id=pa.panelist_id) WHERE cp.panelist_id  IN ($ps)";
                        $resultC = $DRW->query($sql_P, $DRW_read);
                        while ($dataC = $DRW->fetch_row($resultC)) {
                            $mult_vals[] = $dataC;
                        }
                        @$DRW->free_result($resultC);
                        foreach ($panelistCheck['p_dma_code'] as $p) {
                            if (!in_array($p, $p_dma_codeArray) && (string) $p !== '') {
                                $p_dma_codeArray[] = $p;
                            }
                        }
                    }
                    $dataCi = 0;

                    foreach ($appendsArray as $app => $appA) {

                        $valArray = array();
                        foreach ($mult_vals as $mv) {
                            if (!in_array($mv[$dataCi], $valArray) && (string) $mv[$dataCi] !== '') {
                                $valArray[] = $mv[$dataCi];
                            }
                        }
                        $dataCi++;
                        if (!empty($showheading[$app])) {
                            if ($app == 'DMA_CODE' && count($p_dma_codeArray) > 0) {
                                $valArray = $p_dma_codeArray;
                            }
                            foreach ($appA as $ap => $aA) {
                                if (count($aA) == 0) {
                                    $appendsData[] = implode(', ', $valArray);
                                } else {
                                    $descArray = array();
                                    foreach ($valArray as $val) {
                                        $descArray[] = getAppendedDescrition($aA[0], $val, $aA[1]);
                                    }
                                    $appendsData[] = implode(', ', $descArray);
                                }
                            }
                        }
                    }
                } else {
                    foreach ($appendsArray as $app => $appA) {
                        if (!empty($showheading[$app])) {
                            foreach ($appA as $ap => $aA) {
                                $appendsData[] = '';
                            }
                        }
                    }
                }
            }
            if ($more >= 1) {
                if ($is_excel > 0) {
                    if ($more > 2 || $more == 1) {
                        if ($is_excel == 2) {
                            //add ppdate
                            $worksheet->writeString($erow, $ecol++, $ppadddate);
                            $worksheet->writeString($erow, $ecol++, $p_pdate);
                            $worksheet->writeString($erow, $ecol++, $competi_id);
                        } else {
                            //add ppdate
                            $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($ppadddate), PHPExcel_Cell_DataType::TYPE_STRING);
                            $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($p_pdate), PHPExcel_Cell_DataType::TYPE_STRING);
                            $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($competi_id), PHPExcel_Cell_DataType::TYPE_STRING);
                        }
                        
                        /* ####  For FICO, Vantage, CreditVision Score #### */
                        
                        if ($is_excel == 2) {
                            if (!empty($showheading['fico_score'])) {
                                $worksheet->writeString($erow, $ecol++, $fico_score);                              
                            }
                            if (!empty($showheading['fico_range'])) {
                                $worksheet->writeString($erow, $ecol++, $fico_range);                              
                            }
                            if (!empty($showheading['vantage_score'])) {
                                $worksheet->writeString($erow, $ecol++, $vantage_score);                              
                            }
                            if (!empty($showheading['vantage_range'])) {
                                $worksheet->writeString($erow, $ecol++, $vantage_range);                              
                            }
                            if (!empty($showheading['credit_vision'])) {
                                $worksheet->writeString($erow, $ecol++, $credit_vision);                              
                            }
                            if (!empty($showheading['credit_vision_range'])) {
                                $worksheet->writeString($erow, $ecol++, $credit_range);                              
                            }
                        }else{
                            if (!empty($showheading['fico_score'])) {
                                $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($fico_score), PHPExcel_Cell_DataType::TYPE_STRING);
                            }
                            if (!empty($showheading['fico_range'])) {
                                $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($fico_range), PHPExcel_Cell_DataType::TYPE_STRING);
                            }
                            if (!empty($showheading['vantage_score'])) {
                                $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($vantage_score), PHPExcel_Cell_DataType::TYPE_STRING);
                            }
                            if (!empty($showheading['vantage_range'])) {
                                $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($vantage_range), PHPExcel_Cell_DataType::TYPE_STRING);
                            }
                            if (!empty($showheading['credit_vision'])) {
                                $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($credit_vision), PHPExcel_Cell_DataType::TYPE_STRING);
                            }
                            if (!empty($showheading['credit_vision_range'])) {
                                $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($credit_range), PHPExcel_Cell_DataType::TYPE_STRING);
                            }
                            
                        }
                        
                        /* #### End For FICO, Vantage, CreditVision Score #### */
                        
                        
                    }
                    if ($sess_plevel == 1 || $sess_plevel == 2) {
                        if ($more > 2 || $more == 1) {
                            if ($is_excel == 2) {                                
                                if (!empty($showheading['invitationID'])) {
                                    $worksheet->writeString($erow, $ecol++, $invitationID);
                                }
                                if (!empty($showheading['trackingID'])) {
                                    $worksheet->writeString($erow, $ecol++, $trackingID);
                                }
                                if (!empty($showheading['pproductFICO'])) {
                                    $worksheet->writeString($erow, $ecol++, $pproductFICO);
                                }
                            } else {
                                
                                if (!empty($showheading['invitationID'])) {
                                    $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($invitationID), PHPExcel_Cell_DataType::TYPE_STRING);
                                }
                                if (!empty($showheading['trackingID'])) {
                                    $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($trackingID), PHPExcel_Cell_DataType::TYPE_STRING);
                                }
                                if (!empty($showheading['pproductFICO'])) {
                                    $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($pproductFICO), PHPExcel_Cell_DataType::TYPE_STRING);
                                }
                            }
                        }
                    }
                    foreach ($appendsData as $a) {
                        if ($is_excel == 2) {
                            $worksheet->writeString($erow, $ecol++, $a);
                        } else {
                            $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($a), PHPExcel_Cell_DataType::TYPE_STRING);
                        }
                    }
                } else {
                    if ($more > 2 || $more == 1) {
                        //add ppdate
                        $line .= csvExcape($ppadddate) . ",";
                        $line .= csvExcape($p_pdate) . ",";
                        $line .= csvExcape($competi_id) . ",";
                        
                        
                        if (!empty($showheading['fico_score'])) {
                            $line .= csvExcape($fico_score) . ",";
                        }
                        if (!empty($showheading['fico_range'])) {
                            $line .= csvExcape($fico_range) . ",";
                        }
                        if (!empty($showheading['vantage_score'])) {
                            $line .= csvExcape($vantage_score) . ",";
                        }
                        if (!empty($showheading['vantage_range'])) {
                            $line .= csvExcape($vantage_range) . ",";
                        }
                        if (!empty($showheading['credit_vision'])) {
                           $line .= csvExcape($credit_vision) . ",";
                        }
                        if (!empty($showheading['credit_vision_range'])) {
                           $line .= csvExcape($credit_range) . ",";
                        }
                        
                        /* #### End For FICO, Vantage, CreditVision Score #### */
                    }
                    
                    
                    if ($sess_plevel == 1 || $sess_plevel == 2) {
                        if ($more > 2 || $more == 1) {                            
                            if (!empty($showheading['invitationID'])) {
                                $line .= csvExcape($invitationID) . ",";
                            }
                            if (!empty($showheading['trackingID'])) {
                                $line .= csvExcape($trackingID) . ",";
                            }
                            if (!empty($showheading['pproductFICO'])) {
                                $line .= csvExcape($pproductFICO) . ",";
                            }
                        }
                    }
                    foreach ($appendsData as $a) {
                        $line .= csvExcape($a) . ",";
                    }
                }
            } else {
                $ppstateID = '';
                $pgender = '';
                $ppageID = '';
                $pincomeID = '';
                $pppostalcode = '';
            }
           //echo "<pre>";
            ///print_r($heading);
            //echo "</pre>"; die;
            foreach ($heading as $k => $h) {
                if (!empty($showheading[$k])) {
                    switch ($k) {
                        case 'productName':
                            if ($is_excel > 0) {
                                if ($is_excel == 2) {
                                    $worksheet->writeString($erow, $ecol++, $row['productName']);
                                } else {
                                    $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($row['productName']), PHPExcel_Cell_DataType::TYPE_STRING);
                                }
                            } else {
                                $line .= csvExcape($row['productName']) . ",";
                            }
                            break;
                        case 'company':
                            $company = $row['company'];
                            //if($company == '') $company = "N/A";
                            if ($is_excel > 0) {
                                if ($is_excel == 2) {
                                    $worksheet->writeString($erow, $ecol++, $company);
                                } else {
                                    $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($company), PHPExcel_Cell_DataType::TYPE_STRING);
                                }
                            } else {
                                $line .= csvExcape($company) . ",";
                            }
                            break;
                        case 'secondCompany':
                            $secondCompany = '';


                            $resultC = $DRW->query("SELECT companyName FROM cscan_company pa,cscan_company_product pp 
								WHERE pa.companyID=pp.companyID AND pp.productID={$row['theproductID']} AND primary_co<>1 ORDER BY primary_co ASC,companyName ASC", $DRW_read);
                            while ($dataC = $DRW->fetch_row($resultC)) {
                                if ($secondCompany != '') {
                                    $secondCompany .= '; ';
                                }
                                $secondCompany .= $dataC[0];
                            }
                            @$DRW->free_result($resultC);
                            if ($secondCompany == "") {
                                $secondCompany = 'N/A';
                            }
                            //$secondCompany = $row['secondCompany'];
                            //if($secondCompany == '') $secondCompany = "N/A";
                            if ($is_excel > 0) {
                                if ($is_excel == 2) {
                                    $worksheet->writeString($erow, $ecol++, $secondCompany);
                                } else {
                                    $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($secondCompany), PHPExcel_Cell_DataType::TYPE_STRING);
                                }
                            } else {
                                $line .= csvExcape($secondCompany) . ",";
                            }
                            break;
                        case 'productHeadline':
                            $row['productHeadline'] = preg_replace('/[\x00-\x1F\x7F]/u', '', $row['productHeadline']);
                            $productHeadline = preg_replace('/\\s+/', ' ', $row['productHeadline']);
                            if ($is_excel > 0) {
                                if ($is_excel == 2) {
                                    $worksheet->writeString($erow, $ecol++, $productHeadline); //,$format_wrap
                                } else {
                                    //$objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($productHeadline),PHPExcel_Cell_DataType::TYPE_STRING);//,$format_wrap
                                    $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit($productHeadline, PHPExcel_Cell_DataType::TYPE_STRING); //,$format_wrap
                                }
                            } else {
                                $line .= csvExcape($productHeadline) . ",";
                            }
                            break;
                        case 'entryID':
                            $url = 'https://www.competiscan.com/index.php?product=' . $row['theproductID'];
                            if ($is_excel > 0) {
                                if ($is_excel == 2) {
                                    $worksheet->writeUrl($erow, $ecol++, $url, $row['entryID']);
                                } else {
                                    /* $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($row['entryID']), PHPExcel_Cell_DataType::TYPE_STRING)
                                            ->getHyperlink()->setUrl($url); */
                                    if(!empty($url)){
                                        $column = $ecol++;
                                        $cell = getNameFromNumber($column+1);
                                        $styleArray = array(
                                            'font' => array(
                                                'underline' => PHPExcel_Style_Font::UNDERLINE_SINGLE,
                                                'color' => array('rgb' => '0000FF'),
                                                'size'  => 10,
                                                'name'  => 'Arial'
                                            )
                                        );                                    
                                        $objPHPExcel->getActiveSheet()
                                        ->getCellByColumnAndRow($column, $erow)->setValueExplicit(makeUTF($row['entryID']), PHPExcel_Cell_DataType::TYPE_STRING)
                                        ->getHyperlink()->setUrl($url);
                                        $objPHPExcel->getActiveSheet()->getStyle($cell.$erow)->applyFromArray($styleArray);
                                        unset($styleArray);
                                    }else{
                                        $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(' ', PHPExcel_Cell_DataType::TYPE_STRING);
                                    }                                    
                                }
                            } else {
                                $line .= csvExcape($row['entryID']) . "," . csvExcape($url) . ",";
                            }
                            break;
                        /*############### Start For Quarter Filed #############*/
                        case 'quarter':
                        $q1=array('01','02','03');
                        $q2=array('04','05','06');
                        $q3=array('07','08','09');
                        $q4=array('10','11','12');
                        $Quater='';
                        if ($more == 87 || $more == 1 || $more ==90) {
                            if($p_pdate!=''){
                                $exp_month=explode('-',$p_pdate);
                                $yearQuarter=$exp_month[0];
                                $monthQuarter=$exp_month[1];
                                if (in_array($monthQuarter,$q1)){
                                        $Quater=$yearQuarter.'Q1';
                                }else if (in_array($monthQuarter,$q2)){
                                        $Quater=$yearQuarter.'Q2';	
                                }else if(in_array($monthQuarter,$q3)){
                                        $Quater=$yearQuarter.'Q3';
                                }else if(in_array($monthQuarter,$q4)){
                                        $Quater=$yearQuarter.'Q4';
                                }
                            }
                        }else{
                            if($row['entryID']!=''){
                                $exp_month=explode('-',$row['entryID']);
                                $yearQuarter=$exp_month[0];
                                $monthQuarter=$exp_month[1];
                                if (in_array($monthQuarter,$q1)){
                                        $Quater=$yearQuarter.'Q1';
                                }else if (in_array($monthQuarter,$q2)){
                                        $Quater=$yearQuarter.'Q2';	
                                }else if(in_array($monthQuarter,$q3)){
                                        $Quater=$yearQuarter.'Q3';
                                }else if(in_array($monthQuarter,$q4)){
                                        $Quater=$yearQuarter.'Q4';
                                }
                            }
                        }
                        if ($is_excel > 0) {
                                if ($is_excel == 2) {
                                    $worksheet->writeString($erow, $ecol++, $Quater); //,$format_wrap
                                } else {
                                    $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit($Quater, PHPExcel_Cell_DataType::TYPE_STRING); //,$format_wrap
                                }
                            } else {
                                $line .= csvExcape($Quater) . ",";
                            }
                            break;
                        
                        /*############### End For Quarter Filed #############*/
                        /* ####  For FICO, Vantage, CreditVision Score #### */
                        case 'sectorID':
                        case 'PrimarysectorID':
                            //As per requested by nate removed group permission
                            //if ($sess_plevel == 1 || $sess_plevel == 2) {

                                $resultC = $DRW->query("SELECT scsc_sectorID FROM cscan_scsc_product WHERE productID={$row['theproductID']} AND scsc_sort=1", $DRW_read);
                                $dataC = $DRW->fetch_row($resultC);
                                if (!empty($dataC[0])) {
                                    $primary = sectorName($dataC[0]);
                                } else {
                                    $primary = '';
                                }
                                @$DRW->free_result($resultC);
                            //}
                            if ($is_excel > 0) {
                                if ($k == 'PrimarysectorID') {
                                    if ($is_excel == 2) {
                                        $worksheet->writeString($erow, $ecol++, $primary);
                                    } else {
                                        $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($primary), PHPExcel_Cell_DataType::TYPE_STRING);
                                    }
                                } else {
                                    if ($is_excel == 2) {
                                        $worksheet->writeString($erow, $ecol++, sectorName($row['sectorID']));
                                    } else {
                                        $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF(sectorName($row['sectorID'])), PHPExcel_Cell_DataType::TYPE_STRING);
                                    }
                                }
                            } else {
                                if ($k == 'PrimarysectorID') {
                                    $line .= csvExcape($primary) . ",";
                                } else {
                                    $line .= csvExcape(sectorName($row['sectorID'])) . ",";
                                }
                            }
                            break;
                        case 'categoryID':
                        case 'PrimarycategoryID':
                            //As per requested by nate removed group permission
                            //if ($sess_plevel == 1 || $sess_plevel == 2) {


                                $resultC = $DRW->query("SELECT scsc_categoryID FROM cscan_scsc_product WHERE productID={$row['theproductID']} AND scsc_sort=1", $DRW_read);
                                $dataC = $DRW->fetch_row($resultC);
                                if (!empty($dataC[0])) {
                                    $primary = categoryName($dataC[0]);
                                } else {
                                    $primary = '';
                                }
                                @$DRW->free_result($resultC);
                            //}
                            $category = categoryName($row['categoryID']);
                            //if($category == '') $category = 'N/A';
                            if ($is_excel > 0) {
                                if ($k == 'PrimarycategoryID') {
                                    if ($is_excel == 2) {
                                        $worksheet->writeString($erow, $ecol++, $primary);
                                    } else {
                                        $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($primary), PHPExcel_Cell_DataType::TYPE_STRING);
                                    }
                                } else {
                                    if ($is_excel == 2) {
                                        $worksheet->writeString($erow, $ecol++, $category);
                                    } else {
                                        $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($category), PHPExcel_Cell_DataType::TYPE_STRING);
                                    }
                                }
                            } else {
                                if ($k == 'PrimarycategoryID') {
                                    $line .= csvExcape($primary) . ",";
                                } else {
                                    $line .= csvExcape($category) . ",";
                                }
                            }
                            break;
                        case 'subCategoryID':
                        case 'PrimarysubCategoryID':
                            //As per requested by nate removed group permission
                            //if ($sess_plevel == 1 || $sess_plevel == 2) {
                                $resultC = $DRW->query("SELECT scsc_subCategoryID FROM cscan_scsc_product WHERE productID={$row['theproductID']} AND scsc_sort=1", $DRW_read);
                                $dataC = $DRW->fetch_row($resultC);
                                if (!empty($dataC[0])) {
                                    $primary = subCategoryName($dataC[0]);
                                } else {
                                    $primary = '';
                                }
                                @$DRW->free_result($resultC);
                            //}
                            $subCategory = subCategoryName($row['subCategoryID']);
                            //if($subCategory == '') $subCategory = 'N/A';
                            if ($is_excel > 0) {
                                if ($k == 'PrimarysubCategoryID') {
                                    if ($is_excel == 2) {
                                        $worksheet->writeString($erow, $ecol++, $primary);
                                    } else {
                                        $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($primary), PHPExcel_Cell_DataType::TYPE_STRING);
                                    }
                                } else {
                                    if ($is_excel == 2) {
                                        $worksheet->writeString($erow, $ecol++, $subCategory);
                                    } else {
                                        $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($subCategory), PHPExcel_Cell_DataType::TYPE_STRING);
                                    }
                                }
                            } else {
                                if ($k == 'PrimarysubCategoryID') {
                                    $line .= csvExcape($primary) . ",";
                                } else {
                                    $line .= csvExcape($subCategory) . ",";
                                }
                            }
                            break;
                        case 'subSubCategoryID':
                        case 'PrimarysubSubCategoryID':
                            //As per requested by nate removed group permission
                            //if ($sess_plevel == 1 || $sess_plevel == 2) {
                                $resultC = $DRW->query("SELECT scsc_subSubCategoryID FROM cscan_scsc_product WHERE productID={$row['theproductID']} AND scsc_sort=1", $DRW_read);
                                $dataC = $DRW->fetch_row($resultC);
                                if (!empty($dataC[0])) {
                                    $primary = subCategoryName($dataC[0]);
                                } else {
                                    $primary = '';
                                }
                                @$DRW->free_result($resultC);
                            //}
                            $subCategory = subCategoryName($row['subSubCategoryID']);
                            //if($subCategory == '') $subCategory = 'N/A';
                            if ($is_excel > 0) {
                                if ($k == 'PrimarysubSubCategoryID') {
                                    if ($is_excel == 2) {
                                        $worksheet->writeString($erow, $ecol++, $primary);
                                    } else {
                                        $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($primary), PHPExcel_Cell_DataType::TYPE_STRING);
                                    }
                                } else {
                                    if ($is_excel == 2) {
                                        $worksheet->writeString($erow, $ecol++, $subCategory);
                                    } else {
                                        $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($subCategory), PHPExcel_Cell_DataType::TYPE_STRING);
                                    }
                                }
                            } else {
                                if ($k == 'PrimarysubSubCategoryID') {
                                    $line .= csvExcape($primary) . ",";
                                } else {
                                    $line .= csvExcape($subCategory) . ",";
                                }
                            }
                            break;
                        case 'compaignLanguage':
                            $comp_lan = languageName($row['compaignLanguage']);
                            //if($comp_lan == '') $comp_lan = 'N/A';
                            if ($is_excel > 0) {
                                if ($is_excel == 2) {
                                    $worksheet->writeString($erow, $ecol++, $comp_lan);
                                } else {
                                    $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($comp_lan), PHPExcel_Cell_DataType::TYPE_STRING);
                                }
                            } else {
                                $line .= csvExcape($comp_lan) . ",";
                            }
                            break;
                        case 'firstSeen':
                            if ($is_excel > 0) {
                                if ($is_excel == 2) {
                                    $worksheet->write($erow, $ecol++, $row['firstSeen']);
                                } else {
                                    $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($row['firstSeen']), PHPExcel_Cell_DataType::TYPE_STRING);
                                }
                            } else {
                                $line .= csvExcape($row['firstSeen']) . ",";
                            }
                            break;
                        case 'lastSeen':
                            if ($is_excel > 0) {
                                if ($is_excel == 2) {
                                    $worksheet->write($erow, $ecol++, $row['lastSeen']);
                                } else {
                                    $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($row['lastSeen']), PHPExcel_Cell_DataType::TYPE_STRING);
                                }
                            } else {
                                $line .= csvExcape($row['lastSeen']) . ",";
                            }
                            break;
                        case 'mChannelID':
                            if ($is_excel > 0) {
                                if ($is_excel == 2) {
                                    $worksheet->writeString($erow, $ecol++, mediaChannelName($row['mChannelID']));
                                } else {
                                    $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF(mediaChannelName($row['mChannelID'])), PHPExcel_Cell_DataType::TYPE_STRING);
                                }
                            } else {
                                $line .= csvExcape(mediaChannelName($row['mChannelID'])) . ",";
                            }
                            break;

                        /* #### For digital source and simple domain #### */
                        case 'digital_source':

                            if ($row['is_mobile'] == 2) {
                                $devices = 'Mobile';
                            } else if ($row['is_mobile'] == 1) {
                                $devices = 'Desktop';
                            } else {
                                $devices = '';
                            }
                            if ($is_excel > 0) {
                                if ($is_excel == 2) {
                                    $worksheet->writeString($erow, $ecol++, $devices);
                                } else {
                                    $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($devices), PHPExcel_Cell_DataType::TYPE_STRING);
                                }
                            } else {
                                $line .= csvExcape($devices) . ",";
                            }
                            break;

                        case 'simple_domain':


                            if ($is_excel > 0) {
                                if ($is_excel == 2) {
                                    $worksheet->writeString($erow, $ecol++, $row['simple_domain']);
                                } else {
                                    $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($row['simple_domain']), PHPExcel_Cell_DataType::TYPE_STRING);
                                }
                            } else {
                                $line .= csvExcape($row['simple_domain']) . ",";
                            }
                            break;

                        /* #### End for digital source and simple domain #### */

                        #########Start: SEM Details #######

                        case 'sem_search_key':
                            $productID = $row['theproductID'];
                            $sem_search_key = '';
                            if (!empty($productID)) {
                                $sql_md5_search_key = "SELECT sem_search_key FROM cscan_semdetails WHERE product_id = '" .$productID."'";
                                $query_md5_search_key = $DRW->query($sql_md5_search_key, $DRW_read);
                                if ($DRW->num_rows($query_md5_search_key) > 0) {
                                    $data_sem_det_key= $DRW->fetch_assoc($query_md5_search_key);
                                    $sem_search_key = $data_sem_det_key['sem_search_key']; 
                                }
                            }
                           if ($is_excel > 0) {
                                if ($is_excel == 2) {
                                    $worksheet->writeString($erow, $ecol++, $sem_search_key);
                                } else {
                                    $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($sem_search_key), PHPExcel_Cell_DataType::TYPE_STRING);
                                }
                            } else {
                                $line .= csvExcape($sem_search_key) . ",";
                            }
                            break;

                        case 'sem_url':
                            $productID = $row['theproductID'];
                            $sem_url = '';
                            if (!empty($productID)) {
                                   $sql_md5_sem_url = "SELECT sem_url FROM cscan_semdetails WHERE product_id = '" .$productID."'";
                                    $query_md5_sem_url = $DRW->query($sql_md5_sem_url, $DRW_read);
                                    if ($DRW->num_rows($query_md5_sem_url) > 0) {
                                        $data_sem_det_url= $DRW->fetch_assoc($query_md5_sem_url);
                                        $sem_url = $data_sem_det_url['sem_url'];
                                        
                                    }
                            }
                           if ($is_excel > 0) {
                                if ($is_excel == 2) {
                                    $worksheet->writeString($erow, $ecol++, $sem_url);
                                } else {
                                    $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($sem_url), PHPExcel_Cell_DataType::TYPE_STRING);
                                }
                            } else {
                                $line .= csvExcape($sem_url) . ",";
                            }
                            break;
                        case 'sem_headline':
                            $productID = $row['theproductID'];
                            $sem_headline = '';
                            if (!empty($productID)) {
                                $sql_md5_sem_headline = "SELECT sem_headline FROM cscan_semdetails WHERE product_id = '" .$productID."'";
                                $query_md5_sem_headline = $DRW->query($sql_md5_sem_headline, $DRW_read);
                                if ($DRW->num_rows($query_md5_sem_headline) > 0) {
                                    $data_sem_det_head= $DRW->fetch_assoc($query_md5_sem_headline);
                                    $sem_headline = $data_sem_det_head['sem_headline'];
                                    
                                }
                                
                            }
                            
                            if ($is_excel > 0) {
                                if ($is_excel == 2) {
                                    $worksheet->writeString($erow, $ecol++, $sem_headline);
                                } else {
                                    $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($sem_headline), PHPExcel_Cell_DataType::TYPE_STRING);
                                }
                            } else {
                                $line .= csvExcape($sem_headline) . ",";
                            }
                           break;
                        case 'sem_description':
                            $productID = $row['theproductID'];
                            $sem_description = '';
                            if (!empty($productID)) {
                                $sql_md5_sem_desc = "SELECT sem_description FROM cscan_semdetails WHERE product_id = '" .$productID."'";
                                $query_md5_sem_desc = $DRW->query($sql_md5_sem_desc, $DRW_read);
                                if ($DRW->num_rows($query_md5_sem_desc) > 0) {
                                    $data_sem_det_desc= $DRW->fetch_assoc($query_md5_sem_desc);
                                    $sem_description = $data_sem_det_desc['sem_description'];
                                    
                                }

                            }
                            if ($is_excel > 0) {
                                if ($is_excel == 2) {
                                    $worksheet->writeString($erow, $ecol++, $sem_description);
                                } else {
                                    $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($sem_description), PHPExcel_Cell_DataType::TYPE_STRING);
                                }
                            } else {
                                $line .= csvExcape($sem_description) . ",";
                            }
                            break;

                        #########End: SEM Details #######        

                        case 'mPanelID':
                            if ($is_excel > 0) {
                                if ($is_excel == 2) {
                                    $worksheet->writeString($erow, $ecol++, mediaPanelName($row['mPanelID']));
                                } else {
                                    $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF(mediaPanelName($row['mPanelID'])), PHPExcel_Cell_DataType::TYPE_STRING);
                                }
                            } else {
                                $line .= csvExcape(mediaPanelName($row['mPanelID'])) . ",";
                            }
                            break;
                        case 'state':

//                                                    if(($more>2 || $more==1) && !empty($ppstateID)){
//								$name = stateName($ppstateID);
//							}
//							else{
//								if($search_panelist_date || $consumer_only || $do_bid){
//									if($state!=''){
//										$tmp1 = explode(',',$state);
//										$tmp2 = explode(',',$row['state']);
//										foreach($tmp2 as $k=>$t){
//											if(!in_array($t,$tmp1)){
//												unset($tmp2[$k]);
//											}
//										}
//										$row['state'] = implode(',',$tmp2);
//									}
//								}
//								if(count($panelistCheck['ppstateID'])>0){
//									$tmp1 = $panelistCheck['ppstateID'];
//									$tmp2 = explode(',',$row['state']);
//									foreach($tmp2 as $k=>$t){
//										if(!in_array($t,$tmp1)){
//											unset($tmp2[$k]);
//										}
//									}
//									if(count($tmp2)==0){
//										$tmp2 = $panelistCheck['ppstateID'];
//									}
//									$row['state'] = implode(',',$tmp2);
//								}
//								$name = stateName($row['state']);
//							}
                            /*if ($more == '1') {
                                if (($more > 2 || $more == 1) && !empty($ppstateID)) {
                                    $name = stateName($ppstateID);
                                } else {
                                    if ($search_panelist_date || $consumer_only || $do_bid) {
                                        if ($state != '') {
                                            $tmp1 = explode(',', $state);
                                            $tmp2 = explode(',', $row['state']);
                                            foreach ($tmp2 as $k => $t) {
                                                if (!in_array($t, $tmp1)) {
                                                    unset($tmp2[$k]);
                                                }
                                            }
                                            $row['state'] = implode(',', $tmp2);
                                        }
                                    }
                                    if (count($panelistCheck['ppstateID']) > 0) {
                                        $tmp1 = $panelistCheck['ppstateID'];
                                        $tmp2 = explode(',', $row['state']);
                                        foreach ($tmp2 as $k => $t) {
                                            if (!in_array($t, $tmp1)) {
                                                unset($tmp2[$k]);
                                            }
                                        }
                                        if (count($tmp2) == 0) {
                                            $tmp2 = $panelistCheck['ppstateID'];
                                        }
                                        $row['state'] = implode(',', $tmp2);
                                    }
                                    $name = stateName($row['state']);
                                }
                            } else {

                                $sn = 0;
                                if ($search_panelist_date || $consumer_only || $do_bid) {
                                    if ($state != '') {
                                        $tmp1 = explode(',', $state);
                                        $tmp2 = explode(',', $row['state']);
                                        foreach ($tmp2 as $k => $t) {
                                            if (!in_array($t, $tmp1)) {
                                                unset($tmp2[$k]);
                                            }
                                        }
                                        $row['state'] = implode(',', $tmp2);
                                    }
                                   $name = stateName($ppstateID);
                                    $sn = 1;
                                } else if (count($panelistCheck['ppstateID']) > 0) {
                                    $tmp1 = $panelistCheck['ppstateID'];
                                    $tmp2 = explode(',', $row['state']);
                                    foreach ($tmp2 as $k => $t) {
                                        if (!in_array($t, $tmp1)) {
                                            unset($tmp2[$k]);
                                        }
                                    }
                                    if (count($tmp2) == 0) {
                                        $tmp2 = $panelistCheck['ppstateID'];
                                    }
                                    $row['state'] = implode(',', $tmp2);
                                    $name = stateName($row['state']);
                                    $sn = 1;
                                } else if (($more > 2 || $more == 1) && !empty($ppstateID)) {
                                    $name = stateName($ppstateID);
                                }
                                if ($sn == 0 && isset($row['state'])) {
                                   $name = stateName($row['state']);
                                }
                            }*/
                            if (($more > 2 || $more == 1) && !empty($ppstateID)) {
                                $name = stateName($ppstateID);
                            } else {
                                if ($search_panelist_date || $consumer_only || $do_bid) {
                                    if ($state != '') {
                                        $tmp1 = explode(',', $state);
                                        $tmp2 = explode(',', $row['state']);
                                        foreach ($tmp2 as $k => $t) {
                                            if (!in_array($t, $tmp1)) {
                                                unset($tmp2[$k]);
                                            }
                                        }
                                        $row['state'] = implode(',', $tmp2);
                                    }
                                }
                                if (count($panelistCheck['ppstateID']) > 0) {
                                    $tmp1 = $panelistCheck['ppstateID'];
                                    $tmp2 = explode(',', $row['state']);
                                    foreach ($tmp2 as $k => $t) {
                                        if (!in_array($t, $tmp1)) {
                                            unset($tmp2[$k]);
                                        }
                                    }
                                    if (count($tmp2) == 0) {
                                        $tmp2 = $panelistCheck['ppstateID'];
                                    }
                                    $row['state'] = implode(',', $tmp2);
                                }
                                $name = stateName($row['state']);
                            }


                            if ($name == 'Any') {
                                $name = '';
                            }
                            if ($is_excel > 0) {
                                if ($is_excel == 2) {
                                    $worksheet->writeString($erow, $ecol++, $name);
                                } else {
                                    $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($name), PHPExcel_Cell_DataType::TYPE_STRING);
                                }
                            } else {
                                $line .= csvExcape($name) . ",";
                            }
                            break;
                        case 'country':
//							if(($more>2 || $more==1) && !empty($ppstateID)){
//								$country_stateID = $ppstateID;
//							}
//							else{
//								if($search_panelist_date || $consumer_only || $do_bid){
//									if($state!=''){
//										$tmp1 = explode(',',$state);
//										$tmp2 = explode(',',$row['state']);
//										foreach($tmp2 as $k=>$t){
//											if(!in_array($t,$tmp1)){
//												unset($tmp2[$k]);
//											}
//										}
//										$row['state'] = implode(',',$tmp2);
//									}
//								}
//								if(count($panelistCheck['ppstateID'])>0){
//									$tmp1 = $panelistCheck['ppstateID'];
//									$tmp2 = explode(',',$row['state']);
//									foreach($tmp2 as $k=>$t){
//										if(!in_array($t,$tmp1)){
//											unset($tmp2[$k]);
//										}
//									}
//									if(count($tmp2)==0){
//										$tmp2 = $panelistCheck['ppstateID'];
//									}
//									$row['state'] = implode(',',$tmp2);
//								}
//								$country_stateID = $row['state'];
//							}

                            if ($more == '1') {

                                if (($more > 2 || $more == 1) && !empty($ppstateID)) {
                                    $country_stateID = $ppstateID;
                                } else {
                                    if ($search_panelist_date || $consumer_only || $do_bid) {
                                        if ($state != '') {
                                            $tmp1 = explode(',', $state);
                                            $tmp2 = explode(',', $row['state']);
                                            foreach ($tmp2 as $k => $t) {
                                                if (!in_array($t, $tmp1)) {
                                                    unset($tmp2[$k]);
                                                }
                                            }
                                            $row['state'] = implode(',', $tmp2);
                                        }
                                    }
                                    if (count($panelistCheck['ppstateID']) > 0) {
                                        $tmp1 = $panelistCheck['ppstateID'];
                                        $tmp2 = explode(',', $row['state']);
                                        foreach ($tmp2 as $k => $t) {
                                            if (!in_array($t, $tmp1)) {
                                                unset($tmp2[$k]);
                                            }
                                        }
                                        if (count($tmp2) == 0) {
                                            $tmp2 = $panelistCheck['ppstateID'];
                                        }
                                        $row['state'] = implode(',', $tmp2);
                                    }
                                    $country_stateID = $row['state'];
                                }
                            } else {
                                $scc = 0;
                                if ($search_panelist_date || $consumer_only || $do_bid) {
                                    if ($state != '') {
                                        $tmp1 = explode(',', $state);
                                        $tmp2 = explode(',', $row['state']);
                                        foreach ($tmp2 as $k => $t) {
                                            if (!in_array($t, $tmp1)) {
                                                unset($tmp2[$k]);
                                            }
                                        }
                                        $row['state'] = implode(',', $tmp2);
                                    }
                                    $country_stateID = $row['state'];
                                    $scc = 1;
                                } else if (count($panelistCheck['ppstateID']) > 0) {
                                    $tmp1 = $panelistCheck['ppstateID'];
                                    $tmp2 = explode(',', $row['state']);
                                    foreach ($tmp2 as $k => $t) {
                                        if (!in_array($t, $tmp1)) {
                                            unset($tmp2[$k]);
                                        }
                                    }
                                    if (count($tmp2) == 0) {
                                        $tmp2 = $panelistCheck['ppstateID'];
                                    }
                                    $row['state'] = implode(',', $tmp2);
                                    $country_stateID = $row['state'];
                                    $scc = 1;
                                } else if (($more > 2 || $more == 1) && !empty($ppstateID)) {
                                    $country_stateID = $ppstateID;
                                }
                                if ($scc == 0 && isset($row['state'])) {
                                    $country_stateID = $row['state'];
                                }
                            }

                            $countryName = array();
                            $sqlc = "SELECT DISTINCT country FROM cscan_state JOIN ISO31661_alpha2code ON (code=countryCode)
								WHERE stateID IN (" . $country_stateID . ") ORDER BY country";
                            $rsc = $DRW->query($sqlc, $DRW_read);
                            while ($rowc = $DRW->fetch_row($rsc)) {
                                $countryName[] = $rowc[0];
                            }
                            if (count($countryName) == 0) {
                                $sqlc = "SELECT country FROM cscan_product_detail_state JOIN ISO31661_alpha2code ON (code=countryCode_copy) where productID={$row['theproductID']} AND stateID=99";

                                $sqlc = "SELECT country FROM cscan_product_detail JOIN ISO31661_alpha2code ON (code=countryCode_copy) where productID={$row['theproductID']} ";

                                $rsc = $DRW->query($sqlc, $DRW_read);
                                $rowc = $DRW->fetch_row($rsc);
                                if (!empty($rowc[0])) {
                                    $countryName[] = $rowc[0];
                                }
                            }
                            $name = implode(", ", $countryName);
                            if ($is_excel > 0) {
                                if ($is_excel == 2) {
                                    $worksheet->writeString($erow, $ecol++, $name);
                                } else {
                                    $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($name), PHPExcel_Cell_DataType::TYPE_STRING);
                                }
                            } else {
                                $line .= csvExcape($name) . ",";
                            }
                            break;
                        case 'agentCommunicationID':
                            if (in_array('citi', $sess_search_exclude)) {
                                $temp = explode(',', $row['agentCommunicationID']);
                                $hides = array(36, 37, 38, 39);
                                foreach ($hides as $hide) {
                                    $ind = array_search($hide, $temp);
                                    if ($ind !== false) {
                                        unset($temp[$ind]);
                                    }
                                }
                                $row['agentCommunicationID'] = implode(',', $temp);
                            }
                            $agname = agentName($row['agentCommunicationID']);
                            if ($agname == 'NA')
                                $agname = '';
                            if ($is_excel > 0) {
                                if ($is_excel == 2) {
                                    $worksheet->writeString($erow, $ecol++, $agname);
                                } else {
                                    $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($agname), PHPExcel_Cell_DataType::TYPE_STRING);
                                }
                            } else {
                                $line .= csvExcape($agname) . ",";
                            }
                            break;
                        case 'mTypeID':
                            $name = mediaType($row['mTypeID']);
                            if ($name == 'Any')
                                $name = '';
                            if ($is_excel > 0) {
                                if ($is_excel == 2) {
                                    $worksheet->writeString($erow, $ecol++, $name);
                                } else {
                                    $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($name), PHPExcel_Cell_DataType::TYPE_STRING);
                                }
                            } else {
                                $line .= csvExcape($name) . ",";
                            }
                            break;
                        case 'IssueTypeID':
                            $name = getIssueType($row['IssueTypeID']);
                            if ($name == 'Any')
                                $name = '';
                            if ($is_excel > 0) {
                                if ($is_excel == 2) {
                                    $worksheet->writeString($erow, $ecol++, $name);
                                } else {
                                    $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($name), PHPExcel_Cell_DataType::TYPE_STRING);
                                }
                            } else {
                                $line .= csvExcape($name) . ",";
                            }
                            break;
                        case 'affinityAssociation':
                        case 'affinityAssociationName':
                        case 'AffinityCategoryID':
                        case 'AffinitySubCategoryID':
                            if ($row['affinityAssociation'] == 1)
                                $name = 'Yes';
                            else
                                $name = 'No';

                            $aff_ids = '';
                            $aff_cids = array();

                            $resultC = $DRW->query("SELECT pa.affinityID,affinityName FROM cscan_affinity pa,cscan_affinity_product pp 
								WHERE pa.affinityID=pp.affinityID AND pp.productID={$row['theproductID']} ORDER BY affinityName", $DRW_read);
                            while ($dataC = $DRW->fetch_row($resultC)) {
                                if ($aff_ids != '') {
                                    $aff_ids .= ', ';
                                }
                                $aff_ids .= $dataC[1];
                                if ($k == 'AffinitySubCategoryID') {
                                    $op = '<>';
                                } else {
                                    $op = '=';
                                }

                                $resultC2 = $DRW->query("SELECT catmap.AffinityCategoryID FROM cscan_aff_cat catmap, cscan_affinity_category catmaster WHERE catmap.affinityID=$dataC[0] AND catmap.AffinityCategoryID=catmaster.AffinityCategoryID and catmaster.parentID{$op}0", $DRW_read);
                                while ($dataC2 = $DRW->fetch_row($resultC2)) {
                                    if (!in_array($dataC2[0], $aff_cids) && !empty($dataC2[0])) {
                                        $aff_cids[] = $dataC2[0];
                                    }
                                }
                                @$DRW->free_result($resultC2);
                            }
                            @$DRW->free_result($resultC);
                            if ($k == 'AffinityCategoryID' || $k == 'AffinitySubCategoryID') {
                                $name = getAffinityCategoryName(implode(',', $aff_cids));
                                if ($is_excel > 0) {
                                    if ($is_excel == 2) {
                                        $worksheet->writeString($erow, $ecol++, $name);
                                    } else {
                                        $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($name), PHPExcel_Cell_DataType::TYPE_STRING);
                                    }
                                } else {
                                    $line .= csvExcape($name) . ",";
                                }
                            } elseif ($k == 'affinityAssociationName') {
                                if ($is_excel > 0) {
                                    if ($is_excel == 2) {
                                        $worksheet->writeString($erow, $ecol++, $aff_ids);
                                    } else {
                                        $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($aff_ids), PHPExcel_Cell_DataType::TYPE_STRING);
                                    }
                                } else {
                                    $line .= csvExcape($aff_ids) . ",";
                                }
                            } else {
                                if ($is_excel > 0) {
                                    if ($is_excel == 2) {
                                        $worksheet->writeString($erow, $ecol++, $name);
                                    } else {
                                        $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($name), PHPExcel_Cell_DataType::TYPE_STRING);
                                    }
                                } else {
                                    $line .= csvExcape($name) . ",";
                                }
                            }
                            break;
                        case 'age':
                            if (($more > 2 || $more == 1) && !empty($ppageID)) {
                                $name = getAgeName($ppageID);
                            } else {
                                if ($search_panelist_date || $consumer_only || $do_bid) {
                                    if ($age != '') {
                                        $tmp1 = explode(',', $age);
                                        $tmp2 = explode(',', $row['age']);
                                        foreach ($tmp2 as $k => $t) {
                                            if (!in_array($t, $tmp1)) {
                                                unset($tmp2[$k]);
                                            }
                                        }
                                        $row['age'] = implode(',', $tmp2);
                                    }
                                }
                                if (count($panelistCheck['ppageID']) > 0) {
                                    $tmp1 = $panelistCheck['ppageID'];
                                    $tmp2 = explode(',', $row['age']);
                                    foreach ($tmp2 as $k => $t) {
                                        if (!in_array($t, $tmp1)) {
                                            unset($tmp2[$k]);
                                        }
                                    }
                                    if (count($tmp2) == 0) {
                                        $tmp2 = $panelistCheck['ppageID'];
                                    }
                                    $row['age'] = implode(',', $tmp2);
                                }
                                $name = getAgeName($row['age']);
                            }
                            if ($is_excel > 0) {
                                if ($is_excel == 2) {
                                    $worksheet->writeString($erow, $ecol++, $name);
                                } else {
                                    $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($name), PHPExcel_Cell_DataType::TYPE_STRING);
                                }
                            } else {
                                $line .= csvExcape($name) . ",";
                            }
                            break;
                        case 'gender':
                            if (($more > 2 || $more == 1) && !empty($pgender)) {
                                $row['gender'] = $pgender;
                            } else {
                                if ($search_panelist_date || $consumer_only || $do_bid) {
                                    if ($gender != '') {
                                        $row['gender'] = $gender;
                                    }
                                }
                                if (count($panelistCheck['pgender']) > 0) {
                                    $row['gender'] = implode(',', $panelistCheck['pgender']);
                                }
                            }
                            if ($row['gender'] == 'M')
                                $name = 'Male';
                            elseif ($row['gender'] == 'F')
                                $name = 'Female';
                            else
                                $name = 'Male, Female';
                            if ($is_excel > 0) {
                                if ($is_excel == 2) {
                                    $worksheet->writeString($erow, $ecol++, $name);
                                } else {
                                    $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($name), PHPExcel_Cell_DataType::TYPE_STRING);
                                }
                            } else {
                                $line .= csvExcape($name) . ",";
                            }
                            break;
                        case 'income':
                            if (($more > 2 || $more == 1) && !empty($pincomeID)) {
                                $name = getIncomeName($pincomeID);
                            } else {
                                if ($search_panelist_date || $consumer_only || $do_bid) {
                                    if ($income_mult != '') {
                                        $tmp1 = explode(',', $income_mult);
                                        $tmp2 = explode(',', $row['incomeID']);
                                        foreach ($tmp2 as $k => $t) {
                                            if (!in_array($t, $tmp1)) {
                                                unset($tmp2[$k]);
                                            }
                                        }
                                        $row['incomeID'] = implode(',', $tmp2);
                                    }
                                }
                                if (count($panelistCheck['pincomeID']) > 0) {
                                    $tmp1 = $panelistCheck['pincomeID'];
                                    $tmp2 = explode(',', $row['incomeID']);
                                    foreach ($tmp2 as $k => $t) {
                                        if (!in_array($t, $tmp1)) {
                                            unset($tmp2[$k]);
                                        }
                                    }
                                    if (count($tmp2) == 0) {
                                        $tmp2 = $panelistCheck['pincomeID'];
                                    }
                                    $row['incomeID'] = implode(',', $tmp2);
                                }
                                $name = getIncomeName($row['incomeID']);
                            }
                            if ($is_excel > 0) {
                                if ($is_excel == 2) {
                                    $worksheet->writeString($erow, $ecol++, $name);
                                } else {
                                    $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($name), PHPExcel_Cell_DataType::TYPE_STRING);
                                }
                            } else {
                                $line .= csvExcape($name) . ",";
                            }
                            break;
                        case 'mailpieces':
                            if (!empty($p_mailpieces)) {
                                $name = $p_mailpieces;
                            } else {
                                $sql_P = "SELECT COUNT(*) as pieces FROM cscan_panelists_product pp$dmajoin$edcjoin WHERE {$panelist_idtext}{$ppdatetext}productID=" . $row['theproductID'];
                                if ($more > 0) {
                                    $sql_P .= ' AND ppmv>0';
                                }
                                $resultC = $DRW->query($sql_P, $DRW_read);
                                $dataC = $DRW->fetch_row($resultC);
                                $name = $dataC[0];
                                @$DRW->free_result($resultC);
                            }
                            if ($is_excel > 0) {
                                if ($is_excel == 2) {
                                    $worksheet->write($erow, $ecol++, intval($name), $format_number);
                                } else {
                                    if (!isset($format_array[$k])) {
                                        $format_array[$k] = array($format_number, PHPExcel_Cell::stringFromColumnIndex($ecol), $erow, $erow);
                                    } else {
                                        $format_array[$k][3] = $erow;
                                    }
                                    $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(intval($name), PHPExcel_Cell_DataType::TYPE_NUMERIC);
                                }
                            } else {
                                $line .= csvExcape(intval($name)) . ",";
                            }
                            break;
                        case 'mailvolume':
                        case 'mailspend':
                            if (!empty($p_mailvolume)) {
                                $mail_volume_tot = $p_mailvolume;
                            } else {
                                $sql_MV = "SELECT SUM(ppmv),SUM(ppmv_w),SUM(ppmv_m) FROM cscan_panelists_product pp$dmajoin$edcjoin WHERE {$panelist_idtext}{$ppdatetext}productID=" . $row['theproductID'];
                                $resultC = $DRW->query($sql_MV, $DRW_read);
                                $dataC = $DRW->fetch_row($resultC);
                                $mail_volume_tot = $dataC[0];
                                if ($sess_userID == 9480 || $sess_userID == 8270) {
                                    $mail_volume_tot = $dataC[1];
                                } elseif ($sess_userID == 8089) {
                                    $mail_volume_tot = $dataC[2];
                                }
                                @$DRW->free_result($resultC);
                            }
                            $name = round($mail_volume_tot);
                            if ($mail_volume_tot > 0) {
                                $query2 = "SELECT document_size_byte FROM cscan_document WHERE productID={$row['theproductID']} AND document_id=1";
                                $resultC = $DRW->query($query2, $DRW_read);
                                $data2 = $DRW->fetch_row($resultC);
                                $document_size_byte = (int) $data2[0];
                                @$DRW->free_result($resultC);
                                $dmspend = doSpend($mail_volume_tot, $document_size_byte);
                                $name2 = round($dmspend);
                            } else {
                                $dmspend = $name2 = 0;
                            }
                            if ($is_excel > 0) {
                                if ($k == 'mailspend') {
                                    if ($is_excel == 2) {
                                        $worksheet->write($erow, $ecol++, intval($name2), $format_number);
                                    } else {
                                        if (!isset($format_array[$k . '_2'])) {
                                            $format_array[$k . '_2'] = array($format_number, PHPExcel_Cell::stringFromColumnIndex($ecol), $erow, $erow);
                                        } else {
                                            $format_array[$k . '_2'][3] = $erow;
                                        }
                                        $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(intval($name2), PHPExcel_Cell_DataType::TYPE_NUMERIC);
                                    }
                                } else {
                                    if ($is_excel == 2) {
                                        $worksheet->write($erow, $ecol++, intval($name), $format_number);
                                    } else {
                                        if (!isset($format_array[$k . '_1'])) {
                                            $format_array[$k . '_1'] = array($format_number, PHPExcel_Cell::stringFromColumnIndex($ecol), $erow, $erow);
                                        } else {
                                            $format_array[$k . '_1'][3] = $erow;
                                        }
                                        $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(intval($name), PHPExcel_Cell_DataType::TYPE_NUMERIC);
                                    }
                                }
                            } else {
                                if ($k == 'mailspend') {
                                    $line .= csvExcape(intval($name2)) . ",";
                                } else {
                                    $line .= csvExcape(intval($name)) . ",";
                                }
                            }
                            break;
                        
                        case 'realtime_mailvolume':
                            /* ####  For Real Time Mail Volume #### */
                            $mail_volume_tot_real_time=0;
                            if ($more > 2 || $more == 1) {
                                $sql_MV = "SELECT rpp.real_time_ppmv FROM cscan_real_time_emv rpp left join cscan_panelists_product pp on(pp.panelist_id=rpp.panelist_id) WHERE {$panelist_idtext}{$ppdatetext}rpp.productID=" . $row['theproductID']." AND pp.productID=" . $row['theproductID'];
                                $resultC = $DRW->query($sql_MV, $DRW_read);
                                $dataC = $DRW->fetch_row($resultC);
                                $mail_volume_tot_real_time = $dataC[0];
                                @$DRW->free_result($resultC);
                                
                            }else{
                                $sql_MV = "SELECT SUM(rpp.real_time_ppmv) FROM cscan_real_time_emv rpp left join cscan_panelists_product pp on(pp.panelist_id=rpp.panelist_id) WHERE {$panelist_idtext}{$ppdatetext}rpp.productID=" . $row['theproductID']." AND pp.productID=" . $row['theproductID'];
                                $resultC = $DRW->query($sql_MV, $DRW_read);
                                $dataC = $DRW->fetch_row($resultC);
                                $mail_volume_tot_real_time = $dataC[0];
                                @$DRW->free_result($resultC);
                            }
                            if($mail_volume_tot_real_time<=0){
                                if (!empty($p_mailvolume)) {
                                    $mail_volume_tot_real_time = $p_mailvolume;
                                }else{
                                    $sql_MV = "SELECT SUM(ppmv) FROM cscan_panelists_product pp$dmajoin$edcjoin WHERE {$panelist_idtext}{$ppdatetext}productID=" . $row['theproductID'];
                                    $resultR = $DRW->query($sql_MV, $DRW_read);
                                    $dataR = $DRW->fetch_row($resultR);
                                    $mail_volume_tot_real_time = $dataR[0];                                
                                    @$DRW->free_result($resultR);
                                }
                            }                          
                            
                            
                            $name = round($mail_volume_tot_real_time);
                            if ($is_excel > 0) {
                                if ($is_excel == 2) {
                                    $worksheet->write($erow, $ecol++, intval($name), $format_number);
                                } else {
                                    if (!isset($format_array[$k . '_1'])) {
                                        $format_array[$k . '_1'] = array($format_number, PHPExcel_Cell::stringFromColumnIndex($ecol), $erow, $erow);
                                    } else {
                                        $format_array[$k . '_1'][3] = $erow;
                                    }
                                    $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(intval($name), PHPExcel_Cell_DataType::TYPE_NUMERIC);
                                }
                            } else {
                                $line .= csvExcape(intval($name)) . ",";
                            }                            
                            break;
                            
                        case 'ppeve':
                            //if (!empty($p_mailvolume)) {
                            //    echo $mail_volume_tot = $p_mailvolume;
                           // } else {
                                $sql_MV = "SELECT SUM(ppeve) FROM cscan_panelists_product pp$dmajoin$edcjoin WHERE {$panelist_idtext}{$ppdatetext}productID=" . $row['theproductID'];
                                $resultC = $DRW->query($sql_MV, $DRW_read);
                                $dataC = $DRW->fetch_row($resultC);
                                $mail_volume_tot = $dataC[0];

                                @$DRW->free_result($resultC);
                           // }
                            $name = round($mail_volume_tot);
                            
                            if ($is_excel > 0) {
                                if ($is_excel == 2) {
                                    $worksheet->write($erow, $ecol++, intval($name), $format_number);
                                } else {
                                    if (!isset($format_array[$k . '_1'])) {
                                        $format_array[$k . '_1'] = array($format_number, PHPExcel_Cell::stringFromColumnIndex($ecol), $erow, $erow);
                                    } else {
                                        $format_array[$k . '_1'][3] = $erow;
                                    }
                                    $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(intval($name), PHPExcel_Cell_DataType::TYPE_NUMERIC);
                                }
                            } else {
                                $line .= csvExcape(intval($name)) . ",";
                            }
                            
                            break;


                        case 'ficos':
                            /* $fico_tot = 0;
                              $fico_count = 0;
                              $fico_min = 0;
                              $fico_max = 0;
                              $sql_P = "SELECT ppfico_score FROM cscan_panelists_product WHERE productID=$productID";
                              $resultC = $DRW->query( $sql_P,$DRW_read );
                              while($row = $DRW->fetch_row( $resultC )){
                              $ppfico_score = $row[0];
                              if($ppfico_score!=0){
                              if($ppfico_score>$fico_max){
                              $fico_max = $ppfico_score;
                              }
                              if($fico_min==0 || $ppfico_score<$fico_min){
                              $fico_min = $ppfico_score;
                              }
                              $fico_tot += $ppfico_score;
                              $fico_count++;
                              }
                              }
                              if($fico_count>0){
                              $fico_average = $fico_tot/$fico_count;
                              }
                              else{
                              $fico_average = 0;
                              } */

                            $sql_P = "SELECT AVG(ppfico_score),MAX(ppfico_score),MIN(ppfico_score) as ficos FROM cscan_panelists_product pp$dmajoin$edcjoin WHERE {$panelist_idtext}{$ppdatetext}productID={$row['theproductID']} AND ppfico_score>0";
                            $resultC = $DRW->query($sql_P, $DRW_read);
                            $dataC = $DRW->fetch_row($resultC);
                            $fico_average = round($dataC[0]);
                            $fico_max = round($dataC[1]);
                            $fico_min = round($dataC[2]);
                            @$DRW->free_result($resultC);
                            if ($fico_average == 0) {
                                $name = '';
                            } elseif ($fico_max == $fico_average) {
                                $name = '';
                                if (!($more > 2 || $more == 1)) {
                                    $name .= 'AVG: ';
                                }
                                $name .= $fico_average;
                            } else {
                                $name = "MIN: $fico_min, MAX: $fico_max, AVG: $fico_average";
                            }
                            if ($is_excel > 0) {
                                if ($is_excel == 2) {
                                    $worksheet->writeString($erow, $ecol++, $name);
                                } else {
                                    $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($name), PHPExcel_Cell_DataType::TYPE_STRING);
                                }
                            } else {
                                $line .= csvExcape($name) . ",";
                            }
                            break;
                        case 'filesize':
                            $query2 = "SELECT document_size_byte FROM cscan_document WHERE productID={$row['theproductID']} AND document_id=1";
                            $resultC = $DRW->query($query2, $DRW_read);
                            $data2 = $DRW->fetch_row($resultC);
                            $document_size_byte = (int) $data2[0];
                            @$DRW->free_result($resultC);
                            $sizeofPDFinKB = $document_size_byte / 1024;
                            $sizeofPDFinMB = $sizeofPDFinKB / 1024;
                            if ($sizeofPDFinMB < 1) {
                                $DisplaySize = round($sizeofPDFinKB, 2) . " KB";
                            } else {
                                $DisplaySize = round($sizeofPDFinMB, 2) . " MB";
                            }
                            if ($is_excel > 0) {
                                if ($is_excel == 2) {
                                    $worksheet->writeString($erow, $ecol++, $DisplaySize);
                                } else {
                                    $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($DisplaySize), PHPExcel_Cell_DataType::TYPE_STRING);
                                }
                            } else {
                                $line .= csvExcape($DisplaySize) . ",";
                            }
                            break;
                        case 'DMA_ID':
                            if (($more > 2 || $more == 1) && !empty($pppostalcode)) {
                                $dtext = " AND pp.pppostalcode='" . $pppostalcode . "'";
                            } elseif (count($panelistCheck['pppostalcode']) > 0) {
                                $dtext = " AND pp.pppostalcode IN ('" . implode("','", $panelistCheck['pppostalcode']) . "')";
                            } else {
                                $dtext = '';
                            }
                            $dmaids = array();
                            $sql_P = "SELECT DISTINCT dmaid FROM cscan_panelists_product pp JOIN cscan_dma_postalcode dmap ON(pp.pppostalcode=dmap.pppostalcode) WHERE productID={$row['theproductID']}$dtext";
                            $resultC = $DRW->query($sql_P, $DRW_read);
                            while ($dataC = $DRW->fetch_row($resultC)) {
                                $dmaids[] = $dataC[0];
                            }
                            @$DRW->free_result($resultC);
                            $ids = implode(',', $dmaids);
                            if ($search_panelist_date || $consumer_only || $do_bid) {
                                if ($DMA_ID_mult != '') {
                                    $tmp1 = explode(',', $DMA_ID_mult);
                                    $tmp2 = $dmaids;
                                    foreach ($tmp2 as $k => $t) {
                                        if (!in_array($t, $tmp1)) {
                                            unset($tmp2[$k]);
                                        }
                                    }
                                    $ids = implode(',', $tmp2);
                                }
                            }
                            if ($is_excel > 0) {
                                if ($is_excel == 2) {
                                    $worksheet->writeString($erow, $ecol++, get_DMAName($ids));
                                } else {
                                    $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF(get_DMAName($ids)), PHPExcel_Cell_DataType::TYPE_STRING);
                                }
                            } else {
                                $line .= csvExcape(get_DMAName($ids)) . ",";
                            }
                            break;
                        case 'edc_id':
                            if (($more > 2 || $more == 1) && !empty($pppostalcode)) {
                                $dtext = " AND pp.pppostalcode='" . $pppostalcode . "'";
                            } elseif (count($panelistCheck['pppostalcode']) > 0) {
                                $dtext = " AND pp.pppostalcode IN ('" . implode("','", $panelistCheck['pppostalcode']) . "')";
                            } else {
                                $dtext = '';
                            }
                            $edcids = array();
                            $sql_P = "SELECT DISTINCT edc_id FROM cscan_panelists_product pp JOIN cscan_edc_postalcode edc ON(pp.pppostalcode=edc.pppostalcode) WHERE productID={$row['theproductID']}$dtext";
                            $resultC = $DRW->query($sql_P, $DRW_read);
                            while ($dataC = $DRW->fetch_row($resultC)) {
                                $edcids[] = $dataC[0];
                            }
                            @$DRW->free_result($resultC);
                            $ids = implode(',', $edcids);
                            if ($search_panelist_date || $consumer_only || $do_bid) {
                                if ($edc_id_mult != '') {
                                    $tmp1 = explode(',', $edc_id_mult);
                                    $tmp2 = $edcids;
                                    foreach ($tmp2 as $k => $t) {
                                        if (!in_array($t, $tmp1)) {
                                            unset($tmp2[$k]);
                                        }
                                    }
                                    $ids = implode(',', $tmp2);
                                }
                            }
                            if ($is_excel > 0) {
                                if ($is_excel == 2) {
                                    $worksheet->writeString($erow, $ecol++, get_EDCName($ids));
                                } else {
                                    $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF(get_EDCName($ids)), PHPExcel_Cell_DataType::TYPE_STRING);
                                }
                            } else {
                                $line .= csvExcape(get_EDCName($ids)) . ",";
                            }
                            break;

                        case 'fa':
                            $fa = getFaceAmountName($row['fa_ids']);
                            if ($is_excel > 0) {
                                if ($is_excel == 2) {
                                    $worksheet->writeString($erow, $ecol++, $fa);
                                } else {
                                    $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($fa), PHPExcel_Cell_DataType::TYPE_STRING);
                                }
                            } else {
                                $line .= csvExcape($fa) . ",";
                            }
                            break;
                        case 'tl':
                            $tl = getTermLengthName($row['tl_ids']);
                            if ($is_excel > 0) {
                                if ($is_excel == 2) {
                                    $worksheet->writeString($erow, $ecol++, $tl);
                                } else {
                                    $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($tl), PHPExcel_Cell_DataType::TYPE_STRING);
                                }
                            } else {
                                $line .= csvExcape($tl) . ",";
                            }
                            break;
                        case 'riders':
                            $tl = getriders($row['riders']);
                            if ($is_excel > 0) {
                                if ($is_excel == 2) {
                                    $worksheet->writeString($erow, $ecol++, $tl);
                                } else {
                                    $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($tl), PHPExcel_Cell_DataType::TYPE_STRING);
                                }
                            } else {
                                $line .= csvExcape($tl) . ",";
                            }
                            break;
                        case 'pi':
                            if ($is_excel > 0) {
                                if ($is_excel == 2) {
                                    $worksheet->writeString($erow, $ecol++, $panelist_info);
                                } else {
                                    $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($panelist_info), PHPExcel_Cell_DataType::TYPE_STRING);
                                }
                            } else {
                                $line .= csvExcape($panelist_info) . ",";
                            }
                            break;
                        case 'pi2':
                            $cos = array();
                            if (!empty($pid)) {
                                $resultC = $DRW->query("SELECT DISTINCT affinityName FROM cscan_affinity pa,cscan_panelist_affinity pp
									WHERE pp.panelist_id=$pid AND pa.affinityID=pp.affinityID ORDER BY affinityName ASC", $DRW_read);
                                while ($dataC = $DRW->fetch_row($resultC)) {
                                    $cos[] = $dataC[0];
                                }
                                @$DRW->free_result($resultC);
                            }
                            $name = implode(', ', $cos);
                            if ($is_excel > 0) {
                                if ($is_excel == 2) {
                                    $worksheet->writeString($erow, $ecol++, $name);
                                } else {
                                    $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($name), PHPExcel_Cell_DataType::TYPE_STRING);
                                }
                            } else {
                                $line .= csvExcape($name) . ",";
                            }
                            break;
                        case 'pi3':
                            $cos = array();
                            if (!empty($pid)) {
                                $resultC = $DRW->query("SELECT DISTINCT companyName FROM cscan_company pa,cscan_panelist_company pp
									WHERE pp.panelist_id=$pid AND pa.companyID=pp.companyID ORDER BY companyName ASC", $DRW_read);
                                while ($dataC = $DRW->fetch_row($resultC)) {
                                    $cos[] = $dataC[0];
                                }
                                @$DRW->free_result($resultC);
                            }
                            $name = implode(', ', $cos);
                            if ($is_excel > 0) {
                                if ($is_excel == 2) {
                                    $worksheet->writeString($erow, $ecol++, $name);
                                } else {
                                    $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($name), PHPExcel_Cell_DataType::TYPE_STRING);
                                }
                            } else {
                                $line .= csvExcape($name) . ",";
                            }
                            break;
                        case 'delmethid':
                            if (in_array('citi', $sess_search_exclude) && $row['delmethid'] == 5) {
                                $name = '';
                            } else {
                                $name = getDelMeth($row['delmethid']);
                            }
                            if (empty($name)) {
                                $name = mediaChannelName($row['mChannelID']);
                            }
                            if ($is_excel > 0) {
                                if ($is_excel == 2) {
                                    $worksheet->writeString($erow, $ecol++, $name);
                                } else {
                                    $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($name), PHPExcel_Cell_DataType::TYPE_STRING);
                                }
                            } else {
                                $line .= csvExcape($name) . ",";
                            }
                            break;
                         ############################## Start Envelope/Postage Data Fields################
                         case 'deliveryTypeId':
                            if ($row['deliveryTypeId']) {
                                $name = getDelType($row['deliveryTypeId']);
                            } else {
                                $name='';
                            }
                            if ($is_excel > 0) {
                                if ($is_excel == 2) {
                                    $worksheet->writeString($erow, $ecol++, $name);
                                } else {
                                    $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($name), PHPExcel_Cell_DataType::TYPE_STRING);
                                }
                            } else {
                                $line .= csvExcape($name) . ",";
                            }
                            break;
                        case 'postageId':
                            if ($row['postageId']) {
                                $name = getPostageName($row['postageId']);
                            } else {
                                $name = '';
                            }
                            if ($is_excel > 0) {
                                if ($is_excel == 2) {
                                    $worksheet->writeString($erow, $ecol++, $name);
                                } else {
                                    $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($name), PHPExcel_Cell_DataType::TYPE_STRING);
                                }
                            } else {
                                $line .= csvExcape($name) . ",";
                            }
                            break;
                         case 'presortedId':
                            if ($row['presortedId']) {
                                $name = getPresortedName($row['presortedId']);
                            } else {
                                $name = '';
                            }
                            if ($is_excel > 0) {
                                if ($is_excel == 2) {
                                    $worksheet->writeString($erow, $ecol++, $name);
                                } else {
                                    $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($name), PHPExcel_Cell_DataType::TYPE_STRING);
                                }
                            } else {
                                $line .= csvExcape($name) . ",";
                            }
                            break;
                        case 'packageTypeId':
                            if ($row['packageTypeId']) {
                                $name = getPackageName($row['packageTypeId']);
                            } else {
                                $name = '';
                            }
                            if ($is_excel > 0) {
                                if ($is_excel == 2) {
                                    $worksheet->writeString($erow, $ecol++, $name);
                                } else {
                                    $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($name), PHPExcel_Cell_DataType::TYPE_STRING);
                                }
                            } else {
                                $line .= csvExcape($name) . ",";
                            }
                            break;
                     ############################## End Envelope/Postage Data Fields################
                        case 'responseMechID':
                            $name = getresponseMechID($row['responseMechID']);
                            if ($is_excel > 0) {
                                if ($is_excel == 2) {
                                    $worksheet->writeString($erow, $ecol++, $name);
                                } else {
                                    $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($name), PHPExcel_Cell_DataType::TYPE_STRING);
                                }
                            } else {
                                $line .= csvExcape($name) . ",";
                            }
                            break;
                        case 'FeeProductType':
                            $tmp = array();
                            $FeeProductTypes = explode(',', $row['FeeProductType']);
                            foreach ($FeeProductTypes as $f) {
                                $name = getFeeProductTypeName($f);
                                if (!empty($name) && !in_array($name, $tmp)) {
                                    $tmp[] = $name;
                                }
                            }
                            $name = implode(', ', $tmp);
                            if ($is_excel > 0) {
                                if ($is_excel == 2) {
                                    $worksheet->writeString($erow, $ecol++, $name);
                                } else {
                                    $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($name), PHPExcel_Cell_DataType::TYPE_STRING);
                                }
                            } else {
                                $line .= csvExcape($name) . ",";
                            }
                            break;
                        case 'social_media_name':
                            $name = $row['social_media_name'];
                            if ($is_excel > 0) {
                                if ($is_excel == 2) {
                                    $worksheet->writeString($erow, $ecol++, $name);
                                } else {
                                    $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($name), PHPExcel_Cell_DataType::TYPE_STRING);
                                }
                            } else {
                                $line .= csvExcape($name) . ",";
                            }
                            break;
                        case 'external_updates':
                            $name = $row['external_updates'];
                            if ($is_excel > 0) {
                                if ($is_excel == 2) {
                                    $worksheet->writeString($erow, $ecol++, $name);
                                } else {
                                    $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($name), PHPExcel_Cell_DataType::TYPE_STRING);
                                }
                            } else {
                                $line .= csvExcape($name) . ",";
                            }
                            break;
                        case 'external_fans':
                            $name = $row['external_fans'];
                            if ($is_excel > 0) {
                                if ($is_excel == 2) {
                                    $worksheet->writeString($erow, $ecol++, $name);
                                } else {
                                    $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($name), PHPExcel_Cell_DataType::TYPE_STRING);
                                }
                            } else {
                                $line .= csvExcape($name) . ",";
                            }
                            break;
                        case 'external_link_network':
                            $name = $row['external_link'];
                            if (preg_match('/facebook\\.com/i', $name)) {
                                $name = 'Facebook';
                            } elseif (preg_match('/twitter\\.com/i', $name)) {
                                $name = 'Twitter';
                            } elseif (preg_match('/instagram\\.com/i', $name)) {
                                $name = 'Instagram';
                            }elseif (preg_match('/linkedin\\.com/i', $name)) {
                                $name = 'LinkedIn';
                            }
                            if ($is_excel > 0) {
                                if ($is_excel == 2) {
                                    $worksheet->writeString($erow, $ecol++, $name);
                                } else {
                                    $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($name), PHPExcel_Cell_DataType::TYPE_STRING);
                                }
                            } else {
                                $line .= csvExcape($name) . ",";
                            }
                            break;
                        case 'external_link':
                            $url = $row['external_link'];
                            if ($is_excel > 0) {
                                if ($is_excel == 2) {
                                    $worksheet->writeUrl($erow, $ecol++, $url, $url);
                                } else {
                                    /* $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit($url, PHPExcel_Cell_DataType::TYPE_STRING)
                                            ->getHyperlink()->setUrl($url); */
                                    if(!empty($url)){
                                        $column = $ecol++;
                                        $cell = getNameFromNumber($column+1);
                                        $styleArray = array(
                                            'font' => array(
                                                'underline' => PHPExcel_Style_Font::UNDERLINE_SINGLE,
                                                'color' => array('rgb' => '0000FF'),
                                                'size'  => 10,
                                                'name'  => 'Arial'
                                            )
                                        );                                    
                                        $objPHPExcel->getActiveSheet()
                                        ->getCellByColumnAndRow($column, $erow)->setValueExplicit($url, PHPExcel_Cell_DataType::TYPE_STRING)
                                        ->getHyperlink()->setUrl($url);
                                        $objPHPExcel->getActiveSheet()->getStyle($cell.$erow)->applyFromArray($styleArray);
                                        unset($styleArray);
                                    }else{
                                        $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(' ', PHPExcel_Cell_DataType::TYPE_STRING);
                                    }
                                }
                            } else {
                                $line .= csvExcape($url) . ",";
                            }

                            break;
                        case 'traffic_sources':
                            $name = $row['traffic_sources'];
                            if ($is_excel > 0) {
                                if ($is_excel == 2) {
                                    $worksheet->writeString($erow, $ecol++, $name);
                                } else {
                                    $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($name), PHPExcel_Cell_DataType::TYPE_STRING);
                                }
                            } else {
                                $line .= csvExcape($name) . ",";
                            }
                            break;
                        case 'ppdate':
                            $mArray = array();
                            $sql_P = "SELECT DISTINCT DATE_FORMAT(ppdate,'%Y-%m') FROM cscan_panelists_product pp$dmajoin$edcjoin WHERE {$panelist_idtext}{$ppdatetext}productID=" . $row['theproductID'];
                            if ($more > 0) {
                                $sql_P .= ' AND ppmv>0';
                            }
                            $sql_P .= ' ORDER BY ppdate ASC';
                            $resultC = $DRW->query($sql_P, $DRW_read);
                            while ($dataC = $DRW->fetch_row($resultC)) {
                                $mArray[] = $dataC[0];
                            }
                            $name = implode(', ', $mArray);
                            @$DRW->free_result($resultC);
                            if ($is_excel > 0) {
                                if ($is_excel == 2) {
                                    $worksheet->writeString($erow, $ecol++, $name);
                                } else {
                                    $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($name), PHPExcel_Cell_DataType::TYPE_STRING);
                                }
                            } else {
                                $line .= csvExcape($name) . ",";
                            }
                            break;
                        case 'doclink':
                            if (in_array($row['mChannelID'], array(5, 7))) {
                                $url = 'https://www.competiscan.com/productPdf.php?did=2&id=' . $row['theproductID'];
                            } else {
                                $url = 'https://www.competiscan.com/productPdf.php?did=1&id=' . $row['theproductID'];
                            }

                            if ($is_excel > 0) {
                                if ($is_excel == 2) {
                                    $worksheet->writeUrl($erow, $ecol++, $url, 'PDF Content');
                                } else {
                                    /* $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit('PDF Content', PHPExcel_Cell_DataType::TYPE_STRING)
                                            ->getHyperlink()->setUrl($url); */
                                    if(!empty($url)){
                                        $column = $ecol++;
                                        $cell = getNameFromNumber($column+1);
                                        $styleArray = array(
                                            'font' => array(
                                                'underline' => PHPExcel_Style_Font::UNDERLINE_SINGLE,
                                                'color' => array('rgb' => '0000FF'),
                                                'size'  => 10,
                                                'name'  => 'Arial'
                                            )
                                        );                                    
                                        $objPHPExcel->getActiveSheet()
                                        ->getCellByColumnAndRow($column, $erow)->setValueExplicit('PDF Content', PHPExcel_Cell_DataType::TYPE_STRING)
                                        ->getHyperlink()->setUrl($url);
                                        $objPHPExcel->getActiveSheet()->getStyle($cell.$erow)->applyFromArray($styleArray);
                                        unset($styleArray);
                                        /* $phpColor = new PHPExcel_Style_Color();
                                        $phpColor->setRGB('0000FF');  
                                        $objPHPExcel->getActiveSheet()->getStyle($erow)->getFont()->setColor( $phpColor ); */
                                    }else{
                                        $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(' ', PHPExcel_Cell_DataType::TYPE_STRING);
                                    }
                                }
                            } else {
                                $line .= csvExcape($url) . ",";
                            }
                            break;
                        case 'prescription':
                            if (!empty($row['prescription'])) {
                                $name = 'Yes';
                            } else {
                                $name = '';
                            }
                            if ($is_excel > 0) {
                                if ($is_excel == 2) {
                                    $worksheet->write($erow, $ecol++, $name);
                                } else {
                                    $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($name), PHPExcel_Cell_DataType::TYPE_STRING);
                                }
                            } else {
                                $line .= csvExcape($name) . ",";
                            }
                            break;
                        case 'is_hphsa':
                            if (!empty($row['is_hphsa'])) {
                                $name = 'Yes';
                            } else {
                                $name = '';
                            }
                            if ($is_excel > 0) {
                                if ($is_excel == 2) {
                                    $worksheet->write($erow, $ecol++, $name);
                                } else {
                                    $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($name), PHPExcel_Cell_DataType::TYPE_STRING);
                                }
                            } else {
                                $line .= csvExcape($name) . ",";
                            }
                            break;
                        case 'is_prescreen':
                            if (!empty($row['is_prescreen'])) {
                                $name = 'Yes';
                            } else {
                                $name = '';
                            }
                            if ($is_excel > 0) {
                                if ($is_excel == 2) {
                                    $worksheet->write($erow, $ecol++, $name);
                                } else {
                                    $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($name), PHPExcel_Cell_DataType::TYPE_STRING);
                                }
                            } else {
                                $line .= csvExcape($name) . ",";
                            }
                            break;
                        case 'is_citi':
                            if (!empty($row['is_citi'])) {
                                $name = 'Yes';
                            } else {
                                $name = 'No';
                            }
                            if ($is_excel > 0) {
                                if ($is_excel == 2) {
                                    $worksheet->write($erow, $ecol++, $name);
                                } else {
                                    $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($name), PHPExcel_Cell_DataType::TYPE_STRING);
                                }
                            } else {
                                $line .= csvExcape($name) . ",";
                            }
                            break;
                        case 'OfferExpiryDate':
                            if ($row['OfferExpiryDate'] == '0000-00-00') {
                                $row['OfferExpiryDate'] = '';
                            }
                            if ($is_excel > 0) {
                                if ($is_excel == 2) {
                                    $worksheet->write($erow, $ecol++, $row['OfferExpiryDate']);
                                } else {
                                    $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($row['OfferExpiryDate']), PHPExcel_Cell_DataType::TYPE_STRING);
                                }
                            } else {
                                $line .= csvExcape($row['OfferExpiryDate']) . ",";
                            }
                            break;
                        case 'Publication':
                        case 'PublicationDate':
                            $pubArray = array();
                            $pubDateArray = array();
                            $print_typeArray = array();

                            $resultC = $DRW->query("SELECT publicationName,DATE_FORMAT(monthYear,'%m/%d/%Y'),print_typeID FROM cscan_publication pa,cscan_publication_product pp 
								WHERE pa.publicationID=pp.publicationID AND pp.productID=" . $row['theproductID'] . " ORDER BY monthYear DESC,publicationName", $DRW_read);
                            while ($dataC = $DRW->fetch_row($resultC)) {
                                $publicationName = $dataC[0];
                                $monthYear = $dataC[1];
                                $print_typeID = $dataC[2];

                                $pubArray[] = $publicationName;
                                $pubDateArray[] = $monthYear;
                                /* $query_pt ="SELECT print_typeName FROM cscan_print_type WHERE print_typeID=$print_typeID";
                                  $result_pt = $DRW->query($query_pt,$DRW_read);
                                  $row_pt = $DRW->fetch_row($result_pt);
                                  if($row_pt[0]!='' && !in_array($row_pt[0],$print_typeArray)){
                                  $print_typeArray[] = $row_pt[0];
                                  } */
                            }
                            $pubtext = implode(', ', $pubArray);
                            $pubdatetext = implode(', ', $pubDateArray);
                            if ($is_excel > 0) {
                                if ($is_excel == 2) {
                                    if ($k == 'PublicationDate') {
                                        $worksheet->write($erow, $ecol++, $pubdatetext);
                                    } else {
                                        $worksheet->write($erow, $ecol++, $pubtext);
                                    }
                                } else {
                                    if ($k == 'PublicationDate') {
                                        $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($pubdatetext), PHPExcel_Cell_DataType::TYPE_STRING);
                                    } else {
                                        $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($pubtext), PHPExcel_Cell_DataType::TYPE_STRING);
                                    }
                                }
                            } else {
                                if ($k == 'PublicationDate') {
                                    $line .= csvExcape($pubdatetext) . ",";
                                } else {
                                    $line .= csvExcape($pubtext) . ",";
                                }
                            }
                            break;
                        case 'worksiteVoluntary':
                            if (empty($row['worksiteVoluntary'])) {
                                $worksite = 'No';
                            } else {
                                $worksite = 'Yes';
                            }
                            if ($is_excel > 0) {
                                if ($is_excel == 2) {
                                    $worksheet->writeString($erow, $ecol++, $worksite);
                                } else {
                                    $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($worksite), PHPExcel_Cell_DataType::TYPE_STRING);
                                }
                            } else {
                                $line .= csvExcape($worksite) . ",";
                            }
                            break;
                        case 'groupSize':
                            $gname = '';
                            $groupArray = get_groupSizeArray();
                            $groupArray['0'] = 'N/A';
                            $gsizeArray = explode(',', $row['groupSize']);
                            $groupCount = count($gsizeArray);
                            $docomma = false;
                            foreach ($gsizeArray as $gsize) {
                                if ($gsize == 0 && $groupCount > 1) {
                                    continue;
                                } elseif (isset($groupArray[$gsize])) {
                                    if ($docomma) {
                                        $gname .= ', ';
                                    } else {
                                        $docomma = true;
                                    }
                                    $gname .= $groupArray[$gsize];
                                }
                            }
                            if ($is_excel > 0) {
                                if ($is_excel == 2) {
                                    $worksheet->writeString($erow, $ecol++, $gname);
                                } else {
                                    $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($gname), PHPExcel_Cell_DataType::TYPE_STRING);
                                }
                            } else {
                                $line .= csvExcape($gname) . ",";
                            }
                            break;
                        case 'creditUnion':
                            $yesno = 'No';

                            $resultC = $DRW->query("SELECT count(*) FROM cscan_company pa,cscan_company_product pp 
								WHERE pa.companyID=pp.companyID AND pp.productID={$row['theproductID']} AND isCreditUnion=1 limit 1", $DRW_read);
                            $dataC = $DRW->fetch_row($resultC);
                            if (!empty($dataC[0])) {
                                $yesno = 'Yes';
                            }
                            @$DRW->free_result($resultC);
                            if ($is_excel > 0) {
                                if ($is_excel == 2) {
                                    $worksheet->writeString($erow, $ecol++, $yesno);
                                } else {
                                    $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($yesno), PHPExcel_Cell_DataType::TYPE_STRING);
                                }
                            } else {
                                $line .= csvExcape($yesno) . ",";
                            }
                            break;

                        #########Start: Digital Spend/ Impressions #######
                        case 'estimated_spend':
                            $productID = $row['theproductID'];
                            $esti_spend = $esti_impressions = '';
                            if (!empty($productID)) {
                                $sql_mobile_tbl = "SELECT SUM(estimated_spend) as estimated_spend,SUM(estimated_impressions) as estimated_impressions FROM cscan_mobile_digital_spend_impressions where product_id='" . $productID . "'";
                                $res_mobile_tbl = $DRW->query($sql_mobile_tbl, $DRW_read);
                                if ($DRW->num_rows($res_mobile_tbl) > 0) {
                                    $res_mobile_data = $DRW->fetch_assoc($res_mobile_tbl);
                                    $total_spend=round($res_mobile_data['estimated_spend']);
                                    $total_impressions=round($res_mobile_data['estimated_impressions']);
                                    $esti_spend=number_format($total_spend);
                                    $esti_impressions = number_format($total_impressions);
                                }
                                if (in_array($more, array(1, 87, 90))) {
                                    $sql_mobile_tbl = "SELECT estimated_spend,estimated_impressions FROM cscan_mobile_digital_spend_impressions where product_id='" . $productID . "' AND panelist_id='".$pid."'";
                                    $res_mobile_tbl = $DRW->query($sql_mobile_tbl, $DRW_read);
                                    if ($DRW->num_rows($res_mobile_tbl) > 0) {
                                        while($res_mobile_data = $DRW->fetch_assoc($res_mobile_tbl)){
                                            $total_spend=round($res_mobile_data['estimated_spend']);
                                            $total_impressions=round($res_mobile_data['estimated_impressions']);
                                            $esti_spend=number_format($total_spend);
                                            $esti_impressions = number_format($total_impressions);
                                        }
                                    }
                                }
                               
                            }
                            if ($is_excel > 0) {
                                if ($is_excel == 2) {
                                    $worksheet->writeString($erow, $ecol++, $esti_spend);
                                    $worksheet->writeString($erow, $ecol++, $esti_impressions);
                                } else {
                                    $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($esti_spend), PHPExcel_Cell_DataType::TYPE_STRING);
                                    $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($esti_impressions), PHPExcel_Cell_DataType::TYPE_STRING);
                                }
                            } else {
                                $line .= csvExcape($esti_spend) . ",";
                                $line .= csvExcape($esti_impressions) . ",";
                            }
                            break;
                        #########End: Digital Spend/ Impressions #######
                        ###################### For Faux Check selection ####################     
                        case 'faux_check':
                            if ($is_excel > 0) {
                                $fauxval = 'No';
                                if ($row['faux_check'] == '1') {
                                    $fauxval = 'Yes';
                                }
                                if ($is_excel == 2) {

                                    $worksheet->writeString($erow, $ecol++, $fauxval);
                                } else {
                                    $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($fauxval), PHPExcel_Cell_DataType::TYPE_STRING);
                                }
                            } else {
                                $fauxval = 'No';
                                if ($row['faux_check'] == '1') {
                                    $fauxval = 'Yes';
                                }
                                $line .= csvExcape($fauxval) . ",";
                            }
                            break;
                        ###################### End For Faux Check selection ####################            
                        ###################### For Social Media Ad Type selection #################### 
                        case 'socialmedia_adtype':
                            $socialmedia_adtypeArray = array(1 => 'Sponsored', 2 => 'Corporate');
                            if ($is_excel > 0) {
                                $socialmedia_adtypevalue = '';
                                foreach ($socialmedia_adtypeArray as $key => $keyval) {
                                    if ($row['socialmedia_adtype'] == $key) {
                                        $socialmedia_adtypevalue = $keyval;
                                    }
                                }
                                if ($is_excel == 2) {
                                    $worksheet->writeString($erow, $ecol++, $socialmedia_adtypevalue);
                                } else {
                                    $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($socialmedia_adtypevalue), PHPExcel_Cell_DataType::TYPE_STRING);
                                }
                            } else {
                                $socialmedia_adtypevalue = '';
                                foreach ($socialmedia_adtypeArray as $key => $keyval) {
                                    if ($row['socialmedia_adtype'] == $key) {
                                        $socialmedia_adtypevalue = $keyval;
                                    }
                                }
                                $line .= csvExcape($socialmedia_adtypevalue) . ",";
                            }
                            break;

                        ###################### end For Social Media Ad Type selection ####################     
                        ###################### Start Personalization ####################     
                        case 'personalization':
                            if ($is_excel > 0) {
                               
                                if ($row['personalization'] == '1') {
                                    $personalizationValue = 'Personalized';
                                } elseif ($row['personalization'] == '2') {
                                    $personalizationValue = 'Non-Personalized';
                                }elseif ($row['personalization'] == '3') {
                                    $personalizationValue = 'Both';
                                }
                                if ($is_excel == 2) {

                                    $worksheet->writeString($erow, $ecol++, $personalizationValue);
                                } else {
                                    $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($personalizationValue), PHPExcel_Cell_DataType::TYPE_STRING);
                                }
                            } else { 
                                
                               if ($row['personalization'] == '1') {
                                    $personalizationValue = 'Personalized';
                                } elseif ($row['personalization'] == '2') {
                                    $personalizationValue = 'Non-Personalized';
                                }elseif ($row['personalization'] == '3') {
                                    $personalizationValue = 'Both';
                                }
                                $line .= csvExcape($personalizationValue) . ",";
                            }
                            break;
                        ###################### End Personalization ####################  
                        ###############add New field at myexcel#########
                        case 'is_multicultural':
                            if ($row['multiculturalmarkets']!='') {
                                $tmp = array();
                                $target_market = explode(',', $row['multiculturalmarkets']);
                                foreach ($target_market as $f) {
                                    $name = get_TMName($f);
                                    if (!empty($name) && !in_array($name, $tmp)) {
                                        $tmp[] = $name;
                                    }
                                }
                                $name = implode(', ', $tmp);
                             }
                            if ($is_excel > 0) {
                                if ($is_excel == 2) {

                                    $worksheet->writeString($erow, $ecol++, $name);
                                } else {
                                    $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($name), PHPExcel_Cell_DataType::TYPE_STRING);
                                }
                            } else { 
                                $line .= csvExcape($name) . ",";
                            }
                            break; 
                            case 'businessContent_mult':
                                if ($is_excel > 0) {
                                    $businessContent_val = '';
                                    if ($row['businessContent']!='') {
                                        $businessContent_val = get_businessContentName($row['businessContent']);
                                    }
                                    if ($is_excel == 2) {
    
                                        $worksheet->writeString($erow, $ecol++, $businessContent_val);
                                    } else {
                                        $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($businessContent_val), PHPExcel_Cell_DataType::TYPE_STRING);
                                    }
                                } else {
                                    $businessContent_val = '';
                                    if ($row['businessContent']!='') {
                                        $businessContent_val = get_businessContentName($row['businessContent']);
                                    }
                                    $line .= csvExcape($businessContent_val) . ",";
                                }
                            break;  
                            case 'offerOrigin':
                                if ($is_excel > 0) {
                                    $offer_origin_val = '';
                                    if ($row['offerOrigin']!='') {
                                        $offer_origin_val =  get_offerOriginName($row['offerOrigin']);
                                    }
                                    if ($is_excel == 2) {
    
                                        $worksheet->writeString($erow, $ecol++, $offer_origin_val);
                                    } else {
                                        $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($offer_origin_val), PHPExcel_Cell_DataType::TYPE_STRING);
                                    }
                                } else {
                                    $offer_origin_val = '';
                                    if ($row['offerOrigin']!='') {
                                        
                                        $offer_origin_val =  get_offerOriginName($row['offerOrigin']);
                                    }
                                    $line .= csvExcape($offer_origin_val) . ",";
                                }
                                break;
                        case 'FeeProduct':
                            if ($is_excel > 0) {
                                $fee_product_val = '';
                                if ($row['FeeProduct']=='1') {
                                    $fee_product_val = 'Fee Product';
                                }
                                if ($row['FeeProduct'] =='2') {
                                    $fee_product_val = 'Ancillary Service - No Fee';
                                }
                                if ($is_excel == 2) {
                                    $worksheet->writeString($erow, $ecol++, $fee_product_val);
                                } else {
                                    $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($fee_product_val), PHPExcel_Cell_DataType::TYPE_STRING);
                                }
                            } else {
                                $fee_product_val = '';
                                if ($row['FeeProduct'] == '1') {
                                    $fee_product_val = 'Fee Product';
                                }
                                if ($row['FeeProduct'] == '2') {
                                    $fee_product_val = 'Ancillary Service - No Fee';
                                }
                                $line .= csvExcape($fee_product_val) . ",";
                            }
                            break;
                            case 'is_affinion':
                                if ($is_excel > 0) {
                                    $is_affinion_val = 'No';
                                    if ($row['is_affinion'] == '1') {
                                        $is_affinion_val = 'Yes';
                                    }
                                    if ($is_excel == 2) {
    
                                        $worksheet->writeString($erow, $ecol++, $is_affinion_val);
                                    } else {
                                        $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($is_affinion_val), PHPExcel_Cell_DataType::TYPE_STRING);
                                    }
                                } else {
                                    $is_affinion_val = 'No';
                                    if ($row['is_affinion'] == '1') {
                                        $is_affinion_val = 'Yes';
                                    }
                                    $line .= csvExcape($is_affinion_val) . ",";
                                }
                                break;
                        ###############End New field at myexcel#########
                        case 'refinance':
                            $productID = $row['theproductID'];
                            $refinance = $jumbo_ncnfg = $va = $fha = $conventional = $usda =$correspondent_lending = 0;
                            if (!empty($productID)) {
                                $sql = "SELECT refinance,jumbo_ncnfg,va,fha,conventional,usda,correspondent_lending FROM cscan_product_detail WHERE productID='" . $productID . "'";
                                $query_sql = $DRW->query($sql, $DRW_read);
                                if ($DRW->num_rows($query_sql) > 0) {
                                    $resM = $DRW->fetch_assoc($query_sql);
                                    $refinance = (!empty($resM['refinance'])) ? 'Yes' : 'No';
                                    $jumbo_ncnfg = (!empty($resM['jumbo_ncnfg'])) ? 'Yes' : 'No';
                                    $va = (!empty($resM['va'])) ? 'Yes' : 'No';
                                    $fha = (!empty($resM['fha'])) ? 'Yes' : 'No';
                                    $conventional = (!empty($resM['conventional'])) ? 'Yes' : 'No';
                                    $usda = (!empty($resM['usda'])) ? 'Yes' : 'No';
                                    $correspondent_lending = (!empty($resM['correspondent_lending'])) ? 'Yes' : 'No';
                                    
                                }
                            }
                            if ($is_excel > 0) {
                                if ($is_excel == 2) {
                                    $worksheet->writeString($erow, $ecol++, $refinance);
                                    $worksheet->writeString($erow, $ecol++, $jumbo_ncnfg);
                                    $worksheet->writeString($erow, $ecol++, $va);
                                    $worksheet->writeString($erow, $ecol++, $fha);
                                    $worksheet->writeString($erow, $ecol++, $conventional);
                                    $worksheet->writeString($erow, $ecol++, $usda);                                    
                                    $worksheet->writeString($erow, $ecol++, $correspondent_lending);
                                } else {
                                    $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($refinance), PHPExcel_Cell_DataType::TYPE_STRING);
                                    $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($jumbo_ncnfg), PHPExcel_Cell_DataType::TYPE_STRING);
                                    $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($va), PHPExcel_Cell_DataType::TYPE_STRING);
                                    $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($fha), PHPExcel_Cell_DataType::TYPE_STRING);
                                    $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($conventional), PHPExcel_Cell_DataType::TYPE_STRING);
                                    $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($usda), PHPExcel_Cell_DataType::TYPE_STRING);
                                    $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($correspondent_lending), PHPExcel_Cell_DataType::TYPE_STRING);
                                    
                                }
                            } else {
                                $line .= csvExcape($refinance) . ",";
                                $line .= csvExcape($jumbo_ncnfg) . ",";
                                $line .= csvExcape($va) . ",";
                                $line .= csvExcape($fha) . ",";
                                $line .= csvExcape($conventional) . ",";
                                $line .= csvExcape($usda) . ",";
                                $line .= csvExcape($correspondent_lending) . ",";
                                
                            }
                            break;
                    }
                }

                $keys = array_merge(array('incentive', 'incentive_ongoing'), array_keys($mintel_set), array_keys($mintel_set_2), array_keys($mintel_set_3));

                if (in_array($k, $keys)) {
                    $value = $export->convertToYesNo($k, @$row[$k]);
                    if (!empty($showheading[$k]))
                        exportToFile($showheading[$k], $value);
                }
            }

            foreach ($addlArray as $o) {
                $sqlA = "SELECT * FROM " . $o->table . " WHERE productID=" . $row['theproductID'];
                $resultC = $DRW->query($sqlA, $DRW_read);
                if ($DRW->num_rows($resultC) > 0) {
                    $dataC = $DRW->fetch_assoc($resultC);
                } else {
                    $dataC = array();
                }
                @$DRW->free_result($resultC);
                while ($o->getNext()) {
                    $field = $o->getField();
                    if ($field != '' && (($more == 90 && ($o->id == 178 || $o->id == 179)) || ($more == 87 && $o->id == 87) || isset($showheading[$field . '_' . $o->id]))) {
                        if (isset($dataC[$field])) {
                            $val = $o->doProcess($dataC[$field]);
                        } else {
                            $val = '';
                        }
                        if ($is_excel > 0) {
                            if ($o->getType() == 4 && !is_null($val) && $val !== '') {
                                if (strpos($val, '.') !== false) {
                                    if ($is_excel == 2) {
                                        $worksheet->write($erow, $ecol++, floatval(preg_replace('/[^0-9\\.]+/', '', $val)), $format_dec);
                                    } else {
                                        if (!isset($format_array[$field])) {
                                            $format_array[$field] = array($format_dec, PHPExcel_Cell::stringFromColumnIndex($ecol), $erow, $erow);
                                        } else {
                                            $format_array[$field][3] = $erow;
                                        }
                                        $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(floatval(preg_replace('/[^0-9\\.]+/', '', $val)), PHPExcel_Cell_DataType::TYPE_NUMERIC);
                                    }
                                } else {
                                    if ($is_excel == 2) {
                                        $worksheet->write($erow, $ecol++, intval(preg_replace('/[^0-9\\.]+/', '', $val)), $format_number);
                                    } else {
                                        if (!isset($format_array[$field])) {
                                            $format_array[$field] = array($format_number, PHPExcel_Cell::stringFromColumnIndex($ecol), $erow, $erow);
                                        } else {
                                            $format_array[$field][3] = $erow;
                                        }
                                        $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(intval(preg_replace('/[^0-9\\.]+/', '', $val)), PHPExcel_Cell_DataType::TYPE_NUMERIC);
                                    }
                                }
                            } elseif ($o->getType() == 2 && !is_null($val) && $val !== '') {
                                if ($is_excel == 2) {
                                    $worksheet->write($erow, $ecol++, floatval(preg_replace('/[^0-9\\.]+/', '', $val)) / 100, $format_percent);
                                } else {
                                    if (!isset($format_array[$field])) {
                                        $format_array[$field] = array($format_percent, PHPExcel_Cell::stringFromColumnIndex($ecol), $erow, $erow);
                                    } else {
                                        $format_array[$field][3] = $erow;
                                    }
                                    $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(floatval(preg_replace('/[^0-9\\.]+/', '', $val)) / 100, PHPExcel_Cell_DataType::TYPE_NUMERIC);
                                }
                            } else {
                                if ($is_excel == 2) {
                                    $worksheet->writeString($erow, $ecol++, $val);
                                } else {
                                    $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($val), PHPExcel_Cell_DataType::TYPE_STRING);
                                }
                            }
                        } else {
                            $line .= csvExcape($val) . ",";
                        }
                    }
                }
                @$DRW->free_result($resultC);
                $o->doReset();
            }
            
            /*###############START Direct Mail advertised##############*/
            $advertiser_address='';
            $advertiser_city='';
            $advertiser_state='';
            $advertiser_zipcode='';
            $advertiser_phone_number='';
            $advertiser_url='';
            $sql_advertiser_select = "SELECT address,city,stateID,zip_code,phone_number,url FROM cscan_advertisers WHERE productID='".$row['theproductID']."'";
            $result_advertiser = $DRW->query($sql_advertiser_select, $DRW_read);
            $row_advertiser = $DRW->fetch_row($result_advertiser);
            $advertiser_address=$row_advertiser[0];
            $advertiser_city=$row_advertiser[1];
            if($row_advertiser[2]!='' && $row_advertiser[2]!='0'){
              $advertiser_state=stateName($row_advertiser[2]);
            }
            $advertiser_zipcode=$row_advertiser[3];
            $advertiser_phone_number=$row_advertiser[4];
            $advertiser_url=$row_advertiser[5];
            if (!empty($showheading['advertiser_address'])) {
                if($is_excel > 0) {
                    if ($is_excel == 2) {
                            $worksheet->writeString($erow, $ecol++, $advertiser_address);
                        } else {
                            $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($advertiser_address), PHPExcel_Cell_DataType::TYPE_STRING);
                        }
                }else{
                    $line .= csvExcape($advertiser_address) . ",";
                }
            }
            if (!empty($showheading['advertiser_city'])) {
                if($is_excel > 0) {
                    if ($is_excel == 2) {
                            $worksheet->writeString($erow, $ecol++, $advertiser_city);
                        } else {
                            $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($advertiser_city), PHPExcel_Cell_DataType::TYPE_STRING);
                        }
                }else{
                    $line .= csvExcape($advertiser_city) . ",";
                }
            }
            if (!empty($showheading['advertiser_state'])) {
                if($is_excel > 0) {
                    if ($is_excel == 2) {
                            $worksheet->writeString($erow, $ecol++, $advertiser_state);
                        } else {
                            $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($advertiser_state), PHPExcel_Cell_DataType::TYPE_STRING);
                        }
                }else{
                    $line .= csvExcape($advertiser_state) . ",";
                }
            }
            if (!empty($showheading['advertiser_zipcode'])) {
                if($is_excel > 0) {
                    if ($is_excel == 2) {
                            $worksheet->writeString($erow, $ecol++, $advertiser_zipcode);
                        } else {
                            $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($advertiser_zipcode), PHPExcel_Cell_DataType::TYPE_STRING);
                        }
                }else{
                    $line .= csvExcape($advertiser_zipcode) . ",";
                }
            }
            if (!empty($showheading['advertiser_phone_number'])) {
                if($is_excel > 0) {
                    if ($is_excel == 2) {
                            $worksheet->writeString($erow, $ecol++, $advertiser_phone_number);
                        } else {
                            $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($advertiser_phone_number), PHPExcel_Cell_DataType::TYPE_STRING);
                        }
                }else{
                    $line .= csvExcape($advertiser_phone_number) . ",";
                }
            }
            if (!empty($showheading['advertiser_url'])) {
                if($is_excel > 0) {
                    if ($is_excel == 2) {
                            $worksheet->writeString($erow, $ecol++, $advertiser_url);
                        } else {
                            $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($advertiser_url), PHPExcel_Cell_DataType::TYPE_STRING);
                        }
                }else{
                    $line .= csvExcape($advertiser_url) . ",";
                }
            }
            /*###############END Direct Mail advertised##############*/
            /*####### START NEW PROMOTION FIELD #########*/
            $promo_companyID='';
            $promo_product_type_name='';
            $promotion_type_id='';
            $promo_coupan_discount_value='';
            $promo_ad_price='';
            $promo_regular_price='';
            $promo_shipping_detail_id='';
            $promo_online_in_store_id='';
            $promo_qualifier_id='';
            $promo_qualifier_min_purchase_value='';
            $promo_code_required='';
            $promo_bogo='';
            $promo_bogo_buy_value='';
            $promo_bogo_get_value='';
            $sql_promotion_other = $DRW->query("SELECT * FROM cscan_promotion_other_fields where productID='".$row['theproductID']."'", $DRW_read);
            if ($DRW->num_rows($sql_promotion_other) > 0) {
                $rowPromotionOtherData = $DRW->fetch_assoc($sql_promotion_other);
                $promo_other_holiday_id=getPromotionHolidaysById($rowPromotionOtherData['holiday_id']);
                $promo_other_sale_type_id=getPromotionSaleTypeById($rowPromotionOtherData['sale_type_id']);
            }else{
                $promo_other_holiday_id='';
                $promo_other_sale_type_id='';
            } 
            if (!empty($showheading['promotional_field'])) {
                if($is_excel > 0) {
                    if ($is_excel == 2) {
                            $worksheet->writeString($erow, $ecol++, $promo_other_holiday_id);
                            $worksheet->writeString($erow, $ecol++, $promo_other_sale_type_id);
                        } else {
                            $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($promo_other_holiday_id), PHPExcel_Cell_DataType::TYPE_STRING);
                            $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($promo_other_sale_type_id), PHPExcel_Cell_DataType::TYPE_STRING);
                        }
                }else{
                    $line .= csvExcape($promo_other_holiday_id) . ",";
                    $line .= csvExcape($promo_other_sale_type_id) . ",";
                }
            }
            
            $cmparray=array();
            $cmpidarray=array();
            $promotionArray=array();
            $total_promotion=10;
            $sql="SELECT companyID FROM cscan_promotions WHERE productID='".$row['theproductID']."' GROUP by companyID,categoryID order by insert_date";
            $sqlpromotion_comp = $DRW->query($sql, $DRW_read); 
            if ($DRW->num_rows($sqlpromotion_comp) > 0) {
                while ($rowPromotionComData = $DRW->fetch_assoc($sqlpromotion_comp)) {
                    $cmpidarray[]=$rowPromotionComData['companyID'];
                    $sqlComp = "SELECT companyName FROM cscan_company WHERE companyID ='".$rowPromotionComData['companyID']."'";
                    $result_query = $DRW->query($sqlComp,$DRW_read);
                    $row_data = $DRW->fetch_assoc($result_query);
                }
                if(!empty($cmpidarray)){
                    $cmpidstring=implode(',', $cmpidarray);
                    $sql_comp ="SELECT companyID FROM cscan_company_product where productID='".$row['theproductID']."' AND primary_co=1";
                    $sql_primary_comp= $DRW->query($sql_comp, $DRW_read);
                    if ($DRW->num_rows($sql_primary_comp) > 0) {
                        $rowprimaryCompData = $DRW->fetch_assoc($sql_primary_comp); 
                        $primaryCompanyID=$rowprimaryCompData['companyID'];
                        $num_row_count=0;
                        if(in_array($primaryCompanyID,$cmpidarray)){
                            $sql_promotion_p = $DRW->query("SELECT * FROM cscan_promotions where productID='".$row['theproductID']."' AND companyID ='".$primaryCompanyID."' LIMIT 0,10", $DRW_read);
                            $num_row_count=$DRW->num_rows($sql_promotion_p);
                            if($num_row_count>0){
                                while ($rowPrimaryPromotionData = $DRW->fetch_assoc($sql_promotion_p)) {
                                   $promotionArray[]= $rowPrimaryPromotionData;
                                }
                            }
                        }
                        $remaininng_promotion=$total_promotion-$num_row_count;
                        $sql_promotion = $DRW->query("SELECT * FROM cscan_promotions where productID='".$row['theproductID']."' AND companyID !='".$primaryCompanyID."'  LIMIT 0,$remaininng_promotion", $DRW_read);
                        if ($DRW->num_rows($sql_promotion) > 0) {
                            while ($rowPromotionData = $DRW->fetch_assoc($sql_promotion)) {
                               $promotionArray[]= $rowPromotionData;
                            }
                        }
                    }else{
                        $sql_promotion = $DRW->query("SELECT * FROM cscan_promotions where productID='".$row['theproductID']."' AND companyID IN($cmpidstring)  LIMIT 0,10", $DRW_read);
                        if ($DRW->num_rows($sql_promotion) > 0) {
                            while ($rowPromotionData = $DRW->fetch_assoc($sql_promotion)) {
                               $promotionArray[]= $rowPromotionData;
                            }
                        }
                    }
                    if(!empty($promotionArray)){
                        foreach ($promotionArray as $rowPromotionData){
                            $sqlComp = "SELECT companyName FROM cscan_company WHERE companyID ='".$rowPromotionData['companyID']."'";
                            $result_query = $DRW->query($sqlComp,$DRW_read);
                            $row_data_comp = $DRW->fetch_assoc($result_query);
                            $promo_company_name=$row_data_comp['companyName'];
                            $sql_product_type = "SELECT sectorName FROM cscan_sector WHERE sectorID ='".$rowPromotionData['categoryID']."'";
                            $result_query_product_type = $DRW->query($sql_product_type,$DRW_read);
                            $row_data_product_type = $DRW->fetch_assoc($result_query_product_type);
                            $promo_product_type_name=$row_data_product_type['sectorName'];
                            $promotion_type_id=getPromotionTypeById($rowPromotionData['promotion_type_id']);
                            $promo_coupan_discount_value=$rowPromotionData['coupan_discount_value'];
                            if($promo_coupan_discount_value==0){
                                $promo_coupan_discount_value='';
                            }
                            $promo_ad_price=$rowPromotionData['ad_price'];
                            if($promo_ad_price==0){
                                $promo_ad_price='';
                            }
                            $promo_regular_price=$rowPromotionData['regular_price'];
                            if($promo_regular_price==0){
                                $promo_regular_price='';
                            }
                            $promo_shipping_detail_id=getPromotionShippingDetailById($rowPromotionData['shipping_detail_id']);
                            $promo_online_in_store_id=getPromotionOnlineIStoreById($rowPromotionData['online_in_store_id']);
                            $promo_qualifier_id=getPromotionQualifiersById($rowPromotionData['qualifier_id']);
                            $promo_qualifier_min_purchase_value=$rowPromotionData['qualifier_min_purchase_value'];
                            if($promo_qualifier_min_purchase_value==0){
                              $promo_qualifier_min_purchase_value='';  
                            }
                            if($rowPromotionData['code_required']==1){
                                $promo_code_required="Yes";
                            }else{
                                $promo_code_required="No";
                            }

                            if($rowPromotionData['bogo']==1){
                              $promo_bogo="Yes";  
                            }else{
                              $promo_bogo="No";   
                            }
                            $promo_bogo_buy_value=$rowPromotionData['bogo_buy_value'];
                            if($promo_bogo_buy_value==0){
                             $promo_bogo_buy_value='';   
                            }

                            $promo_bogo_get_value=$rowPromotionData['bogo_get_value'];
                            if($promo_bogo_get_value==0){
                               $promo_bogo_get_value=''; 
                            }
                            if (!empty($showheading['promotional_field'])) {
                                if($is_excel > 0) {
                                    if ($is_excel == 2) {
                                    $worksheet->writeString($erow, $ecol++, $promo_company_name);
                                    $worksheet->writeString($erow, $ecol++, $promo_product_type_name);
                                    $worksheet->writeString($erow, $ecol++, $promotion_type_id);
                                    $worksheet->writeString($erow, $ecol++, $promo_coupan_discount_value);
                                    $worksheet->writeString($erow, $ecol++, $promo_ad_price);
                                    $worksheet->writeString($erow, $ecol++, $promo_regular_price);
                                    $worksheet->writeString($erow, $ecol++, $promo_shipping_detail_id);
                                    $worksheet->writeString($erow, $ecol++, $promo_online_in_store_id);
                                    $worksheet->writeString($erow, $ecol++, $promo_qualifier_id);
                                    $worksheet->writeString($erow, $ecol++, $promo_qualifier_min_purchase_value);
                                    $worksheet->writeString($erow, $ecol++, $promo_code_required);
                                    $worksheet->writeString($erow, $ecol++, $promo_bogo);
                                    $worksheet->writeString($erow, $ecol++, $promo_bogo_buy_value);
                                    $worksheet->writeString($erow, $ecol++, $promo_bogo_get_value);
                                    } else {
                                        $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($promo_company_name), PHPExcel_Cell_DataType::TYPE_STRING);
                                        $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($promo_product_type_name), PHPExcel_Cell_DataType::TYPE_STRING);
                                        $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($promotion_type_id), PHPExcel_Cell_DataType::TYPE_STRING);
                                        $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($promo_coupan_discount_value), PHPExcel_Cell_DataType::TYPE_STRING);
                                        $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($promo_ad_price), PHPExcel_Cell_DataType::TYPE_STRING);
                                        $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($promo_regular_price), PHPExcel_Cell_DataType::TYPE_STRING);
                                        $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($promo_shipping_detail_id), PHPExcel_Cell_DataType::TYPE_STRING);
                                        $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($promo_online_in_store_id), PHPExcel_Cell_DataType::TYPE_STRING);
                                        $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($promo_qualifier_id), PHPExcel_Cell_DataType::TYPE_STRING);
                                        $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($promo_qualifier_min_purchase_value), PHPExcel_Cell_DataType::TYPE_STRING);
                                        $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($promo_code_required), PHPExcel_Cell_DataType::TYPE_STRING);
                                        $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($promo_bogo), PHPExcel_Cell_DataType::TYPE_STRING);
                                        $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($promo_bogo_buy_value), PHPExcel_Cell_DataType::TYPE_STRING);
                                        $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)->setValueExplicit(makeUTF($promo_bogo_get_value), PHPExcel_Cell_DataType::TYPE_STRING);
                                    }

                                }else{
                                    $line .= csvExcape($promo_company_name) . ",";
                                    $line .= csvExcape($promo_product_type_name) . ",";
                                    $line .= csvExcape($promotion_type_id) . ",";
                                    $line .= csvExcape($promo_coupan_discount_value) . ",";
                                    $line .= csvExcape($promo_ad_price) . ",";
                                    $line .= csvExcape($promo_regular_price) . ",";
                                    $line .= csvExcape($promo_shipping_detail_id) . ",";
                                    $line .= csvExcape($promo_online_in_store_id) . ",";
                                    $line .= csvExcape($promo_qualifier_id) . ",";
                                    $line .= csvExcape($promo_qualifier_min_purchase_value) . ",";
                                    $line .= csvExcape($promo_code_required) . ",";
                                    $line .= csvExcape($promo_bogo) . ",";
                                    $line .= csvExcape($promo_bogo_buy_value) . ",";
                                    $line .= csvExcape($promo_bogo_get_value) . ",";
                                }
                            }
                        }
                    }
                }
            }
            
            /*####### END NEW PROMOTION FIELD #########*/
            
            if ($is_excel > 0) {
                $erow++;
            } else {
                fwrite($handle, substr($line, 0, -1) . "\n");
            }
        }
    }
    @$DRW->free_result($rs);
}
if ($is_excel > 0) {
    if ($is_excel == 1) {
        foreach ($format_array as $a) {
            $objPHPExcel->getActiveSheet()->getStyle($a[1] . $a[2] . ':' . $a[1] . $a[3])->getNumberFormat()->applyFromArray($a[0]);
        }
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, $exceltype);
        if ($file_choice == 3) {
            $objWriter->setUseDiskCaching(true);
        }
    }
    $sql = "UPDATE cscan_progress SET pct=200 where userID=$sess_userID";
    $DRW->query($sql, $DRW_main);

    unset($rs, $row, $objPHPExcel, $file_choice, $bid, $ssid, $sort, $more, $eb_date1, $eb_date2, $eb_date3, $eb_gender, $eb_state, $eb_age, $eb_income, $eb_DMA_ID, $post_field, $sess_sector, $sess_search_exclude, $sess_plevel, $page, $topCompany, $userID);
    $unsetArray = array('do_bid', 'sqlu', 'file_choice', 'mPanelID', 'mChannelID', 'sectorID', 'mPanelIDArray', 'mChannelIDArray', 'sectorIDArray', 'consumer_only', 'consumer', 'heading', 'showheading', 'searchtitle', 'header', 'erow', 'ecol', 'addlArray', 'state', 'gender', 'age', 'income_mult', 'DMA_ID_mult', 'search_competi_id', 'ppdatetext', 'dmajoin', 'awhere', 'data', 'sql', 'value', 'format_percent', 'format_number', 'format_dec', 'format_title', 'format_head', 'format_array', 'appendsArray', 'appA', 'app', 'aA', 'ap', 'h', 'k', 'o', 'field', 'exceltype', 'filename', 'doexpans', 'dorelev', 'orderby', 'countQuery', 'count_result', 'count', 'search_num_of_rows', 'curr_num_of_rows', 'change', 'curr_change_row', 'sqlc', 'rsc', 'dataC', 'addedToDatabase', 'month1', 'month2', 'search_panelist_date', 'month', 'pct_complete', 'is_mv', 'panelistCheck', 'mult', 'panelist_info', 'sql_P', 'resultC', 'pdata', 'line', 'appendsData', 'p_pdate', 'competi_id','fico_score','fico_range','vantage_score','vantage_range','credit_vision','credit_vision_range','invitationID', 'pid', 'ppstateID', 'pgender', 'ppageID', 'pincomeID', 'pppostalcode', 'panelist_idtext', 'p_mailvolume', 'p_mailpieces', 'trackingID', 'a', 'company', 'secondCompany', 'primary', 'category', 'subCategory', 'url', 'productHeadline', 'agname', 'name', 'fico_average', 'fico_max', 'fico_min', 'sql_MV', 'mail_volume_tot', 'name2', 'dmspend', 'dtext', 'dmaids', 'ids', 'comp_lan', 'aff_ids', 'aff_cids', 'query2', 'data2', 'document_size_byte', 'sizeofPDFinKB', 'sizeofPDFinMB', 'DisplaySize', 'incentive', 'incentive_ongoing', 'fa', 'tl', 'riders', 'cos', 'sqlA', 'afields', 'dataCi', 'val', 'tmp1', 'tmp2', 't','p_mailvolume_real_time');
    foreach ($unsetArray as $u) {
        if (isset($$u)) {
            unset($$u);
        }
    }
    unset($unsetArray);
    unset($u);
    if ($is_excel == 2) {
        //$workbook->send(basename($filepath));
        $workbook->close();
    } else {
        $objWriter->save($filepath); // 'php://output');
    }
} else {
    if ($handle) {
        fclose($handle);
    }
}
$sql = "UPDATE cscan_progress SET pct=300 where userID=$sess_userID";
$DRW->query($sql, $DRW_main);

function makeUTF($value) {
    return @iconv('CP1252', 'UTF-8//ignore', $value);
}

function getAppendedDescrition($table, $code, $gr = 0) {
    global $DRW, $DRW_main, $DRW_read;
    $code = trim($code);
    if (!empty($code)) {
        switch ($gr) {
            case 1:
                $field = 'gr';
                break;
            case 2:
                $field = 'gr_num';
                break;
            case 3:
                $field = 'group_description';
                break;
            default:
                $field = 'description';
        }
        $result = $DRW->query("SELECT $field FROM cscan_{$table} WHERE code='" . $DRW->real_escape_string($code) . "'", $DRW_read);
        $data = $DRW->fetch_row($result);
        @$DRW->free_result($result);
        return $data[0];
    }
    return '';
}

function csvExcape($in, $delim = ',') {
    $out = $in;
    if (strpos($out, $delim) !== false || strpos($out, '"') !== false || strpos($out, "\r\n") !== false || strpos($out, "\n") !== false || strpos($out, "\r") !== false || preg_match('/^0+\\d+$/', $out) > 0) {
        $out = '"' . str_replace('"', '""', $out) . '"';
    }
    return $out;
}

/**
 * Format the value as necessary for export display
 *
 * @param string $header
 * @param string $value
 */
function exportToFile($header, $value) {
    global $is_excel, $worksheet, $objPHPExcel, $erow, $ecol, $line;

    if (empty($header))
        return;

    if (empty($value)) {
        $value = 'N/A';
    }

    if ($is_excel > 0) {
        if ($is_excel == 2) {
            $worksheet->writeString($erow, $ecol++, $value);
        } else {
            $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($ecol++, $erow)
                    ->setValueExplicit(makeUTF($value), PHPExcel_Cell_DataType::TYPE_STRING);
        }
    } else {
        $line .= csvExcape($value) . ",";
    }
}
