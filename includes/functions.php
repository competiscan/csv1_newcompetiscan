<?php
require_once __DIR__ . '/../vendor/autoload.php';
function updateStateLookup($productID, $delete = false) {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    if (!empty($productID)) {
        $sqlu = "DELETE FROM cscan_product_detail_state WHERE productID=$productID";
        $DRW->query($sqlu, $DRW_main);
        if (!$delete) {
            $q = "SELECT state,primary_country from cscan_product_detail WHERE productID=$productID";
            $result = $DRW->query($q, $DRW_read);
            $row = $DRW->fetch_row($result);
            $state = $row[0];
            $primary_country = $row[1];
            if (!empty($state)) {
                $state = explode(',', $state);
            } else {
                $state = array();
            }
            $zero = true;
            foreach ($state as $s) {
                if (!empty($s)) {
                    $countryCode_copy = getstateCountryCode($s);
                    $sqlu = "REPLACE INTO cscan_product_detail_state (productID,stateID,is_panelist,countryCode_copy) VALUES ($productID,$s,0,'$countryCode_copy')";
                    $DRW->query($sqlu, $DRW_main);
                    
                     $sqlus = "update cscan_product_detail set countryCode_copy='$countryCode_copy' where productID='$productID'";
                    $DRW->query($sqlus, $DRW_main);
                    
                    
                    
                    
                    $zero = false;
                }
            }
            $q2 = "SELECT DISTINCT ppstateID from cscan_panelists_product WHERE productID=$productID";
            $result2 = $DRW->query($q2, $DRW_read);
            while ($row2 = $DRW->fetch_row($result2)) {
                $stateid = $row2[0];
                if (!empty($stateid)) {
                    $countryCode_copy = getstateCountryCode($stateid);
                    $sqlu = "REPLACE INTO cscan_product_detail_state (productID,stateID,is_panelist,countryCode_copy) VALUES ($productID,$stateid,1,'$countryCode_copy')";
                    $DRW->query($sqlu, $DRW_main);
                    
                   // $sqlusel = "update cscan_product_detail set countryCode_copy='$countryCode_copy',is_panelist=1 where productID='$productID'";
                    $sqlusel = "update cscan_product_detail set is_panelist=1 where productID='$productID'";
                    
                    $DRW->query($sqlusel, $DRW_main);
                }
            }
            if ($zero) {
                $sqlu = "REPLACE INTO cscan_product_detail_state (productID,stateID,is_panelist,countryCode_copy) VALUES ($productID,99,0,'$primary_country')";
                $DRW->query($sqlu, $DRW_main);
                $sqlusel = "update cscan_product_detail set countryCode_copy='$primary_country' where productID='$productID'";
                $DRW->query($sqlusel, $DRW_main);
            }
        }
    }
}

function getstateCountryCode($stateID = 0) {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;

    $sqlc = "SELECT countryCode FROM cscan_state WHERE stateID=$stateID";
    $rsc = $DRW->query($sqlc, $DRW_read);
    $rowc = $DRW->fetch_row($rsc);
    return (string) $rowc[0];
}

function getDoGraph($dograph) {
    switch ($dograph) {
        case 1:
            $field = 'company';
            break;
        case 2:
            $field = 'categoryID';
            break;
        case 3:
            $field = 'mChannelID';
            break;
        case 4:
            $field = 'sectorID';
            break;
        case 5:
            $field = 'mTypeID';
            break;
        case 6:
            $field = 'state';
            break;
        case 7:
            $field = 'subCategoryID';
            break;
        case 8:
            $field = 'mPanelID';
            break;
        case 9:
            $field = 'agentCommunicationID';
            break;
        case 10:
            $field = 'age';
            break;
        case 11:
            $field = 'gender';
            break;
        case 12:
            $field = 'incomeID';
            break;
        case 13:
            $field = 'mTypeID';
            break;
        case 14:
            $field = "DATE_FORMAT(addedToDatabase,'%m/%Y')";
            break;
        case 16:
            $field = "incentive";
            break;
        case 28:
            $field = "productName";
            break;
        case 29:
            $field = "riders";
            break;
        case 32:
            $field = "fico_range_id";
            break;
        case 33:
            $field = "creditVision_range_id";
            break;
        case 34:
            $field = "vantage_range_id";
            break;
        default:
            $field = "theproductID";
    }
    return $field;
}

function doQuerySort($sort) {
    $dorelev = false;
    $doexpans = false;
    switch ($sort) {
        case 1:
            $orderby = ' ORDER BY pd.productHeadline ASC, pd.entryID_sort1 DESC, pd.entryID_sort2 DESC';
            break;
        case -1:
            $orderby = ' ORDER BY pd.productHeadline DESC, pd.entryID_sort1 ASC, pd.entryID_sort2 ASC';
            break;
        case 2:
            $orderby = ' ORDER BY pd.company ASC, pd.entryID_sort1 DESC, pd.entryID_sort2 DESC';
            break;
        case -2:
            $orderby = ' ORDER BY pd.company DESC, pd.entryID_sort1 ASC, pd.entryID_sort2 ASC';
            break;
        case 3:
            $orderby = ' ORDER BY pd.entryID_sort1 ASC, pd.entryID_sort2 ASC';
            break;
        case -3:
            $orderby = ' ORDER BY pd.entryID_sort1 DESC, pd.entryID_sort2 DESC';
            break;
        case 4:
            $orderby = ' ORDER BY pd.relevancy ASC, pd.entryID_sort1 ASC, pd.entryID_sort2 ASC';
            $dorelev = true;
            break;
        case -4:
            $orderby = ' ORDER BY pd.relevancy DESC, pd.entryID_sort1 DESC, pd.entryID_sort2 DESC';
            $dorelev = true;
        case 5:
            $orderby = ' ORDER BY pd.relevancy ASC, pd.entryID_sort1 ASC, pd.entryID_sort2 ASC';
            $dorelev = true;
            $doexpans = true;
            break;
        case -5:
            $orderby = ' ORDER BY pd.relevancy DESC, pd.entryID_sort1 DESC, pd.entryID_sort2 DESC';
            $dorelev = true;
            $doexpans = true;
            break;
        case 6:
            $orderby = ' ORDER BY pd.isDemographic DESC,pd.entryID_sort1 DESC, pd.entryID_sort2 DESC';
            break;
        case -6:
            $orderby = ' ORDER BY pd.isDemographic ASC,pd.entryID_sort1 ASC, pd.entryID_sort2 ASC';
            break;
        case 7:
            $orderby = ' ORDER BY pd.isVariant DESC, pd.entryID_sort1 DESC, pd.entryID_sort2 DESC';
            break;
        case -7:
            $orderby = ' ORDER BY pd.isVariant ASC, pd.entryID_sort1 ASC, pd.entryID_sort2 ASC';
            break;
        case 8:
            $orderby = ' ORDER BY pd.isInsight DESC, pd.entryID_sort1 DESC, pd.entryID_sort2 DESC';
            break;
        case -8:
            $orderby = ' ORDER BY pd.isInsight ASC, pd.entryID_sort1 ASC, pd.entryID_sort2 ASC';
            break;
        case 9:
            $orderby = ' ORDER BY pd.isFICO DESC, pd.entryID_sort1 DESC, pd.entryID_sort2 DESC';
            break;
        case -9:
            $orderby = ' ORDER BY pd.isFICO ASC, pd.entryID_sort1 ASC, pd.entryID_sort2 ASC';
            break;
        case 10:
            $orderby = ' ORDER BY pd.isSurvey DESC, pd.entryID_sort1 DESC, pd.entryID_sort2 DESC';
            break;
        case -10:
            $orderby = ' ORDER BY pd.isSurvey ASC, pd.entryID_sort1 ASC, pd.entryID_sort2 ASC';
            break;
        case 11:
            $orderby = ' ORDER BY pd.panelist_sort DESC, pd.entryID_sort1 DESC,pd.entryID_sort2 DESC';
            //$orderby = ' ORDER BY pd.entryID_sort1 DESC,pd.panelist_id DESC,pd.entryID_sort2 DESC';
            break;
        case -11:
            $orderby = ' ORDER BY pd.panelist_sort ASC, pd.entryID_sort1 ASC,pd.entryID_sort2 ASC';
            //$orderby = ' ORDER BY pd.entryID_sort1 ASC,pd.panelist_id ASC,pd.entryID_sort2 ASC';
            break;
        case 12:
            $orderby = ' ORDER BY pd.is_panelist_score DESC, pd.entryID_sort1 DESC,pd.entryID_sort2 DESC';            
            break;
        case -12:
            $orderby = ' ORDER BY pd.is_panelist_score ASC, pd.entryID_sort1 ASC,pd.entryID_sort2 ASC';            
            break;
        case 13:
            $orderby = ' ORDER BY pd.is_animation DESC, pd.entryID_sort1 DESC,pd.entryID_sort2 DESC';            
            break;
        case -13:
            $orderby = ' ORDER BY pd.is_animation ASC, pd.entryID_sort1 ASC,pd.entryID_sort2 ASC';            
            break;
        default:
            $orderby = '';
    }
    return array($orderby, $dorelev, $doexpans);
}

function get_businessContentArray() {
    return array('0' => 'No Business Content', '1' => 'Primarily Business Content', '2' => 'Secondary Business Content');
}

function get_groupSizeArray() {
    return array('1' => '2-50', '2' => '51-99', '3' => '100-499', '4' => '500 +');
}

function get_offerOriginArray() {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    $out_Array = array();
    $query = "SELECT oo_id,oo_name FROM cscan_offer_origin ORDER BY oo_name";
    $result = $DRW->query($query, $DRW_read);
    while ($row = $DRW->fetch_row($result)) {
        $id = $row[0];
        $name = $row[1];
        $out_Array[$id] = $name;
    }
    return $out_Array;
}

function get_campaignLanguageArray() {
    return array('English' => 'English', 'Bilingual' => 'Bilingual');
}

function get_addedToDatabaseArray() {
    return array('week' => 'Less than one week ago', '2week' => 'Less than two weeks ago', '1month' => 'Less than one month ago', '3month' => 'Less than 3 months ago', '6month' => 'Less than 6 months ago', '1year' => 'Less than one year ago');
}

function get_monthArray() {
    return array("1" => 'January', "2" => 'February', "3" => 'March', "4" => 'April', "5" => 'May', "6" => 'June', "7" => 'July', "8" => 'August', "9" => 'September', "10" => 'October', "11" => 'November', "12" => 'December');
}

function getFeeProductType() {
    return array('1' => 'Debt Cancellation/Payment Protection', '10' => 'Home Protection Programs', '2' => 'ID Theft/Credit Monitoring', '8' => 'Legal Services', '7' => 'Lost Wallet Protection', '3' => 'Purchase Assistance Programs', '4' => 'Purchase Protection/Warranty Services', '5' => 'Savings/Discount Member Programs', '9' => 'Total Loss Debt Cancellation', '6' => 'Travel Protection/Benefits');
}

function get_IntroductoryPricingArray() {
    return array('1' => 'Purch Intro', '3' => 'BT Intro', '2' => 'No Purch Intro', '4' => 'No BT Intro');
}

function get_personalizedName($id) {
    $a = array(1 => 'Personalized', 2 => 'Non-Personalized');
    if (isset($a[$id])) {
        return $a[$id];
    }
    return '';
}

function get_worksiteName($id) {
    $a = array(1 => 'Worksite/Voluntary', 2 => 'Non-Worksite/Voluntary');
    if (isset($a[$id])) {
        return $a[$id];
    }
    return '';
}

function get_creditUnionName($id) {
    $a = array(1 => 'Credit Union', 2 => 'Non-Credit Union');
    if (isset($a[$id])) {
        return $a[$id];
    }
    return '';
}

function get_affinityName($id) {
    $a = array(1 => 'Affinity/Association', 2 => 'Non-Affinity/Association');
    if (isset($a[$id])) {
        return $a[$id];
    }
    return '';
}

function get_FeeProductName($id) {
    $a = array(1 => 'Fee Product', 2 => 'Ancillary Service - No Fee');
    if (isset($a[$id])) {
        return $a[$id];
    }
    return '';
}

function get_isCitiName($id) {
    $a = array(1 => 'Retail Card Study', 2 => 'Non-Retail Card Study');
    if (isset($a[$id])) {
        return $a[$id];
    }
    return '';
}

function subPanelistFilterName($id) {
    $spfn = array('1' => 'Primary', '2' => 'Non');
    if (isset($spfn[$id])) {
        return $spfn[$id];
    }
    return '';
}

function get_businessContentName($id) {
    $a = get_businessContentArray();
    if (isset($a[$id])) {
        return $a[$id];
    }
    return '';
}

function getFeeProductTypeName($id) {
    $a = getFeeProductType();
    if (isset($a[$id])) {
        return $a[$id];
    }
    return '';
}
function gMortgageLoan($input){//  echo 'here';print_r($arr);die;  
    $arr = explode(",",$input);
    $return = [];
    $options = [1=>'Refinance',2=>'Jumbo/Non-Conforming',3=>'VA',4=>'FHA',5=>'Conventional',6=>'USDA',7=>'Correspondent Lending'];
    for($i=0;$i<count($arr);$i++){
        $j= $i+1;
        if($arr[$i]==1){
            $return[$i]=$options[$j];
        }else if($arr[$i]==2){
            $return[$i]='Non-Refinance';
        }
    }
    return implode(", ",$return);    
}
function get_groupSizeName($id) {
    $a = get_groupSizeArray();
    if (isset($a[$id])) {
        return $a[$id];
    }
    return '';
}

function get_offerOriginName($id) {
    $a = get_offerOriginArray();
    if (isset($a[$id])) {
        return $a[$id];
    }
    return '';
}

function getIntroPricing($id) {
    $a = get_IntroductoryPricingArray();
    if (isset($a[$id])) {
        return $a[$id];
    }
    return '';
}

function get_TMName($ids) {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    if (trim($ids) != '') {
        $sql = "SELECT tm_name FROM cscan_target_market WHERE tm_id IN($ids) ORDER BY tm_name";
        $rs = $DRW->query($sql, $DRW_read);
        $types = array();
        while (list($type) = $DRW->fetch_row($rs)) {
            $types[] = $type;
        }
        $types = @implode(', ', $types);
    } else {
        $types = '';
    }
    return $types;
}

function get_DMAName($ids) {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    if (trim($ids) != '') {
        $ids=ltrim($ids,',') ;
        $ids=rtrim($ids,',') ;
        $sql = "SELECT dmaname FROM cscan_dma WHERE dmaid IN($ids) ORDER BY dmaname";
        $rs = $DRW->query($sql, $DRW_read);
        $types = array();
        while (list($type) = $DRW->fetch_row($rs)) {
            $types[] = $type;
        }
        $types = @implode(', ', $types);
    } else {
        $types = '';
    }
    return $types;
}

function get_EDCName($ids) {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    if (trim($ids) != '') {
        $ids=ltrim($ids,',') ;
        $ids=rtrim($ids,',') ;
        $sql = "SELECT edc_name FROM cscan_edc WHERE edc_id IN($ids) ORDER BY edc_name";
        $rs = $DRW->query($sql, $DRW_read);
        $types = array();
        while (list($type) = $DRW->fetch_row($rs)) {
            $types[] = $type;
        }
        $types = @implode(', ', $types);
    } else {
        $types = '';
    }
    return $types;
}

function get_EleName($ids) {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    if (trim($ids) != '') {
        $ids=ltrim($ids,',') ;
        $ids=rtrim($ids,',') ;
        $sql = "SELECT electronic_name FROM cscan_electronic WHERE electronic_id IN($ids) ORDER BY electronic_name";
        $rs = $DRW->query($sql, $DRW_read);
        $types = array();
        while (list($type) = $DRW->fetch_row($rs)) {
            $types[] = $type;
        }
        $types = @implode(', ', $types);
    } else {
        $types = '';
    }
    return $types;
}

function get_PubName($ids) {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    if (trim($ids) != '') {
        $ids=ltrim($ids,',') ;
        $ids=rtrim($ids,',') ;
        $sql = "SELECT print_typeName FROM cscan_print_type WHERE print_typeID IN($ids) ORDER BY print_typeName";
        $rs = $DRW->query($sql, $DRW_read);
        $types = array();
        while (list($type) = $DRW->fetch_row($rs)) {
            $types[] = $type;
        }
        $types = @implode(', ', $types);
    } else {
        $types = '';
    }
    return $types;
}

function get_SiteName($ids) {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    if (trim($ids) != '') {
        $ids=ltrim($ids,',') ;
        $ids=rtrim($ids,',') ;
        $sql = "SELECT sites_category_name FROM cscan_sites_category WHERE sites_category_id IN($ids) ORDER BY sites_category_name";
        $rs = $DRW->query($sql, $DRW_read);
        $types = array();
        while (list($type) = $DRW->fetch_row($rs)) {
            $types[] = $type;
        }
        $types = @implode(', ', $types);
    } else {
        $types = '';
    }
    return $types;
}

function getFaceAmountName($fa_ids) {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    if (trim($fa_ids) != '') {
        $fa_ids=ltrim($fa_ids,',') ;
        $fa_ids=rtrim($fa_ids,',') ;
        $sql = "SELECT fa_name FROM cscan_face_amount WHERE fa_id IN($fa_ids) ORDER BY fa_sort";
        $rs = $DRW->query($sql, $DRW_read);
        $types = array();
        while (list($type) = $DRW->fetch_row($rs)) {
            $types[] = $type;
        }
        $types = @implode(', ', $types);
    } else {
        $types = '';
    }
    return $types;
}

function getTermLengthName($tl_ids) {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    if (trim($tl_ids) != '') {
        $tl_ids=ltrim($tl_ids,',') ;
        $tl_ids=rtrim($tl_ids,',') ;
        $sql = "SELECT tl_name FROM cscan_term_length WHERE tl_id IN($tl_ids) ORDER BY tl_sort";
        $rs = $DRW->query($sql, $DRW_read);
        $types = array();
        while (list($type) = $DRW->fetch_row($rs)) {
            $types[] = $type;
        }
        $types = @implode(', ', $types);
    } else {
        $types = '';
    }
    return $types;
}

function getApplicationType($ids) {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    if (trim($ids) != '') {
        $ids=ltrim($ids,',') ;
        $ids=rtrim($ids,',') ;
        $sql = "SELECT ApplicationTypeName FROM cscan_application_type WHERE ApplicationTypeID IN($ids) ORDER BY ApplicationTypeSort";
        $rs = $DRW->query($sql, $DRW_read);
        $types = array();
        while (list($type) = $DRW->fetch_row($rs)) {
            $types[] = $type;
        }
        $types = @implode(', ', $types);
    } else {
        $types = '';
    }
    return $types;
}

function getCardNetwork($ids) {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    if (trim($ids) != '') {
        $ids=ltrim($ids,',') ;
        $ids=rtrim($ids,',') ;
        $sql = "SELECT CardTypeName FROM cscan_card_type WHERE CardTypeID IN($ids) ORDER BY CardTypeSort";
        $rs = $DRW->query($sql, $DRW_read);
        $types = array();
        while (list($type) = $DRW->fetch_row($rs)) {
            $types[] = $type;
        }
        $types = @implode(', ', $types);
    } else {
        $types = '';
    }
    return $types;
}

function getERateType($ids) {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    if (trim($ids) != '') {
        $ids=ltrim($ids,',') ;
        $ids=rtrim($ids,',') ;
        $sql = "SELECT RateTypeName FROM cscan_erate_type WHERE RateTypeID IN($ids) ORDER BY RateTypeSort";
        $rs = $DRW->query($sql, $DRW_read);
        $types = array();
        while (list($type) = $DRW->fetch_row($rs)) {
            $types[] = $type;
        }
        $types = @implode(', ', $types);
    } else {
        $types = '';
    }
    return $types;
}

function getEOfferPrice($ids) {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    if (trim($ids) != '') {
        $ids=ltrim($ids,',') ;
        $ids=rtrim($ids,',') ;
        $sql = "SELECT OfferPriceName FROM cscan_offer_price WHERE OfferPriceID IN($ids) ORDER BY OfferPriceSort";
        $rs = $DRW->query($sql, $DRW_read);
        $types = array();
        while (list($type) = $DRW->fetch_row($rs)) {
            $types[] = $type;
        }
        $types = @implode(', ', $types);
    } else {
        $types = '';
    }
    return $types;
}

function getIssueType($ids) {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    if (trim($ids) != '') {
        $ids=ltrim($ids,',') ;
        $ids=rtrim($ids,',') ;
        $sql = "SELECT IssueTypeName FROM cscan_issue_type WHERE IssueTypeID IN ($ids) ORDER BY IssueTypeSort";
        $rs = $DRW->query($sql, $DRW_read);
        $types = array();
        while (list($type) = $DRW->fetch_row($rs)) {
            $types[] = $type;
        }
        $types = @implode(', ', $types);
    } else {
        $types = '';
    }
    return $types;
}

function getETermLength($ids) {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    if (trim($ids) != '') {
        $ids=ltrim($ids,',') ;
        $ids=rtrim($ids,',') ;
        $sql = "SELECT TermLengthName FROM cscan_eterm_length WHERE TermLengthID IN($ids) ORDER BY TermLengthSort";
        $rs = $DRW->query($sql, $DRW_read);
        $types = array();
        while (list($type) = $DRW->fetch_row($rs)) {
            $types[] = $type;
        }
        $types = @implode(', ', $types);
    } else {
        $types = '';
    }
    return $types;
}

function getresponseMechID($ids) {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    if (trim($ids) != '') {
        $ids=ltrim($ids,',') ;
        $ids=rtrim($ids,',') ;
        $sql = "SELECT responseMechName FROM cscan_response_mechanism WHERE responseMechID IN($ids) ORDER BY responseMechSort";
        $rs = $DRW->query($sql, $DRW_read);
        $types = array();
        while (list($type) = $DRW->fetch_row($rs)) {
            $types[] = $type;
        }
        $types = @implode(', ', $types);
    } else {
        $types = '';
    }
    return $types;
}

function getriders($ids) {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    if (trim($ids) != '') {
        $ids=ltrim($ids,',') ;
        $ids=rtrim($ids,',') ;
        $sql = "SELECT ridersName FROM cscan_riders WHERE ridersID IN($ids) ORDER BY ridersSort";
        $rs = $DRW->query($sql, $DRW_read);
        $types = array();
        while (list($type) = $DRW->fetch_row($rs)) {
            $types[] = $type;
        }
        $types = @implode(', ', $types);
    } else {
        $types = '';
    }
    return $types;
}

function getRewardsProgramEmphasis($ids) {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    if (trim($ids) != '') {
        $ids=ltrim($ids,',') ;
        $ids=rtrim($ids,',') ;
        $sql = "SELECT RewardTypeName FROM cscan_reward_type WHERE RewardTypeID IN($ids) ORDER BY RewardTypeSort";
        $rs = $DRW->query($sql, $DRW_read);
        $types = array();
        while (list($type) = $DRW->fetch_row($rs)) {
            $types[] = $type;
        }
        $types = @implode(', ', $types);
    } else {
        $types = '';
    }
    return $types;
}

function getAgeName($ageID) {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    if (trim($ageID) != '') {
        $ageID=ltrim($ageID,',') ;
        $ageID=rtrim($ageID,',') ;
        $sql = "SELECT age_pname FROM cscan_age_product WHERE age_pID IN($ageID) ORDER BY age_psort";
        $rs = $DRW->query($sql, $DRW_read);
        $types = array();
        while (list($type) = $DRW->fetch_row($rs)) {
            $types[] = $type;
        }
        $types = @implode(', ', $types);
    } else {
        $types = '';
    }
    return $types;
}

function is21FilterOn($user_id = false) {
    global $DRW;
    if (!$user_id)
        $user_id = $_SESSION['sess_userID'];

    $sql = "SELECT count(*) as num from cscan_search_exclude WHERE userID =" . intval($user_id) . " and search_field='21agefilter'";

    return intval(($DRW->query($sql)->fetch_object()->num)) == TRUE;
}

function getAgeTypes($doAll = true) {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    $arr = array();
    if ($doAll) {
        $arr[0] = 'Any';
    }

    $sql = "SELECT age_pID,age_pname FROM cscan_age_product";
    if (isset($is21AgeFilterOn) && $is21AgeFilterOn) {
        $sql .= " WHERE age_pmin >= 21";
    } else {
        // this is the special 21+ option
        // it is an arbitrary business rule
        // hence the relatively inelegant design
        $sql .= " WHERE age_pmin != 21";
    }
    $sql .= " ORDER BY age_psort";

    $result = $DRW->query($sql, $DRW_read);
    if ($DRW->num_rows($result) > 0) {
        while ($row = $DRW->fetch_row($result)) {
            $arr[$row[0]] = $row[1];
        }
    }
    return $arr;
}

function is21AgeFilterOn($userId) {
    global $DRW, $DRW_read;
    $sql = "SELECT * FROM cscan_search_exclude WHERE userID = $userId and search_field = '21agefilter'";
    $result = $DRW->query($sql, $DRW_read);
    if ($DRW->num_rows($result) > 0) {
        return true;
    }
    return false;
}

function panelistFilterName($cg_id) {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    if (trim($cg_id) != '') {
        $cg_id=ltrim($cg_id,',') ;
        $cg_id=rtrim($cg_id,',') ;
        $sql = "SELECT cg_name FROM cscan_competi_group WHERE cg_id IN($cg_id) ORDER BY cg_sort";
        $rs = $DRW->query($sql, $DRW_read);
        $types = array();
        while (list($type) = $DRW->fetch_row($rs)) {
            $types[] = $type;
        }
        $types = @implode(', ', $types);
    } else {
        $types = '';
    }
    return $types;
}

function getIncomeName($incomeID) {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    if (trim($incomeID) != '') {
        $incomeID=ltrim($incomeID,',') ;
        $incomeID=rtrim($incomeID,',') ;
        $sql = "SELECT incomeName FROM cscan_incometype WHERE incomeID IN($incomeID) ORDER BY incomeSort";
        $rs = $DRW->query($sql, $DRW_read);
        $types = array();
        while (list($type) = $DRW->fetch_row($rs)) {
            $types[] = $type;
        }
        $types = @implode(', ', $types);
    } else {
        $types = '';
    }
    return $types;
}

function getIncomeIPASC($IPASC) {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    if (trim($IPASC) != '') {
        $tmpArray = explode(',', $IPASC);
        $sql = "SELECT description FROM cscan_income_producing_assets_segment_code WHERE code IN('" . implode("','", $tmpArray) . "') ORDER BY code";
        $rs = $DRW->query($sql, $DRW_read);
        $types = array();
        while (list($type) = $DRW->fetch_row($rs)) {
            $types[] = $type;
        }
        $types = @implode(', ', $types);
    } else {
        $types = '';
    }
    return $types;
}

function getIncomeTypes() {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    $arr = array();
    $sql = "SELECT incomeID, incomeName FROM cscan_incometype ORDER BY incomeSort";
    $result = $DRW->query($sql, $DRW_read);
    if ($DRW->num_rows($result) > 0) {
        while ($row = $DRW->fetch_row($result)) {
            $arr[$row[0]] = $row[1];
        }
    }
    return $arr;
}

function getDeliveryMethod() {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    $query = "SELECT delmethid,delmethname FROM cscan_delivery_method ORDER BY delmethsort";
    $result = $DRW->query($query, $DRW_read);
    $array = array();
    while ($row = $DRW->fetch_array($result)) {
        $array[$row['delmethid']] = $row['delmethname'];
    }
    return $array;
}

function getstates($stateID = 0, $usecode = false, $countries = array()) {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;

    getstatesCountry($stateID, $usecode);

    $sqlc = "SELECT DISTINCT countryCode,country FROM cscan_state JOIN ISO31661_alpha2code ON (code=countryCode) WHERE countryCode<>'US' ORDER BY country";
    $rsc = $DRW->query($sqlc, $DRW_read);
    while ($rowc = $DRW->fetch_row($rsc)) {
        $countries = is_string($countries)? explode(',', $countries): (array) $countries;

        $countries = array_filter($countries);

        if (empty($countries) || in_array($rowc[0], $countries, true)) {
            echo '<optgroup>';
            getstatesCountry($stateID, $usecode, $rowc[0]);
            echo '</optgroup>';
        }
    }
}

function getstatesCountry($stateID = 0, $usecode = false, $country = 'US') {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;

    $sql = "select stateID,stateName,stateCode from cscan_state WHERE countryCode='" . $country . "'  ORDER BY stateName";
    $result = $DRW->query($sql, $DRW_read);
        if (!empty($stateID)) {
        if (is_string($stateID)) {
            $stateID = explode(',', $stateID);
        }

        if (!is_array($stateID)) {
            $stateID = array();
        }

    } else {
        $stateID = array();
    }
    if ($DRW->num_rows($result) > 0) {
        while ($row = $DRW->fetch_array($result)) {
            if ($usecode) {
                $code = $row['stateCode'];
            } else {
                $code = $row['stateID'];
            }
            if (in_array($code, $stateID)) {
                echo "<option value=\"" . $code . "\" selected=\"selected\">" . $row['stateName'] . "</option>";
            } else {
                echo "<option value=\"" . $code . "\">" . $row['stateName'] . "</option>";
            }
        }
    }
}

function getAffinityCategory($parent_id = 0) {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;

    if (is_array($parent_id) && empty($parent_id[0])) {
        unset($parent_id[0]);
    }
    if (is_array($parent_id) && count($parent_id) == 0)
        $id_q = "<> 0"; //all children for parent ID == 'empty array'
    else
        $id_q = (is_array($parent_id) ? "IN (" . implode(',', $parent_id) . ")" : '=' . $parent_id); //in or equals, based on input type

    $tmp = array();
    $z = $DRW->query("SELECT AffinityCategoryID,AffinityCategoryName FROM cscan_affinity_category WHERE parentID $id_q ORDER BY AffinityCategorySort", $DRW_read);
    while ($z && $zz = $DRW->fetch_assoc($z)) {
        $tmp[$zz['AffinityCategoryID']] = $zz['AffinityCategoryName'];
    }
    return $tmp;
}

function getAffinityCategoryName($ids) {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    if (trim($ids) != '') {
        $ids=ltrim($ids,',') ;
        $ids=rtrim($ids,',') ;
        $sql = "SELECT AffinityCategoryName FROM cscan_affinity_category WHERE AffinityCategoryID IN($ids) ORDER BY AffinityCategorySort";
        $rs = $DRW->query($sql, $DRW_read);
        $types = array();
        while (list($type) = $DRW->fetch_row($rs)) {
            $types[] = $type;
        }
        $types = @implode(', ', $types);
    } else {
        $types = '';
    }
    return $types;
}

function getMediaChannel() {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    $arr = array();
    $sql = "select mChannelID, mChannelName from cscan_mchannel ORDER BY mChannelName";
    $result = $DRW->query($sql, $DRW_read);
    if ($DRW->num_rows($result) > 0) {
        while ($row = $DRW->fetch_array($result)) {
            $mChannelID = $row['mChannelID'];
            $mChannelName = $row['mChannelName'];
            $arr[$mChannelID] = $mChannelName;
        }
    }
    return $arr;
}

function getSector() {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    $arr = array();
    $sql = "select sectorID, sectorName from cscan_sector where parentID=0 AND sectorSearchActive=1 ORDER BY sectorName";
    $result = $DRW->query($sql, $DRW_read);
    if ($DRW->num_rows($result) > 0) {
        while ($row = $DRW->fetch_array($result)) {
            $sectorID = $row['sectorID'];
            $sectorName = $row['sectorName'];
            $arr[$sectorID] = $sectorName;
        }
    }
    return $arr;
}

function getCategory($sectorID,$is_trend = true) {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    $arr = array();
    if($is_trend){
     $sql = "select sectorID, sectorName  from cscan_sector where parentID='$sectorID' AND sectorSearchActive=1 ORDER BY sectorName"; //'$sectorID'  
     
    }else{
      $sql = "select sectorID, sectorName  from cscan_sector where parentID='$sectorID' ORDER BY sectorName"; //'$sectorID'
    }
    $result = $DRW->query($sql, $DRW_read);
    if ($DRW->num_rows($result) > 0) {
        while ($row = $DRW->fetch_array($result)) {
            $sectorID = $row['sectorID'];
            $sectorName = $row['sectorName'];
            $arr[$sectorID] = $sectorName;
        }
        return $arr;
    } else {
        return 0;
    }
}

function getSubCategory($categoryID, $withcat = true,$is_trend = true) {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    $categoryQuery = "select sectorID,sectorName from cscan_sector where sectorID ='$categoryID'";
    $categoryQuery = $DRW->query($categoryQuery, $DRW_read);
    $result1 = $DRW->fetch_array($categoryQuery);
    $categoryName = $result1['sectorName'];
    //$categoryID = $result1[sectorID];
    //echo $categoryID;
    $arr = array();
    if($is_trend){
     $sql = "select sectorID, sectorName from cscan_sector where parentID='$categoryID' AND sectorSearchActive=1 ORDER BY sectorName"; //'$sectorID'   
    }else{
     $sql = "select sectorID, sectorName from cscan_sector where parentID='$categoryID' ORDER BY sectorName"; //'$sectorID'
    }
    //echo $sql;
    $result = $DRW->query($sql, $DRW_read);
    $count = $DRW->num_rows($result);
    if ($count > 0) {
        while ($row = $DRW->fetch_array($result)) {
            $sectorID = $row['sectorID'];
            $sectorName = $row['sectorName'];
            if ($withcat) {
                $sectorName = $categoryName . "-" . $sectorName;
            }
            $arr[$sectorID] = $sectorName;
        }
        return $arr;
    } else {
        return 0;
    }
}

function getMailingPanel() {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    $arr = array();
    $sql = "select mPanelID, mPanelName from cscan_mpanel ORDER BY mPanelName";
    $result = $DRW->query($sql, $DRW_read);
    if ($DRW->num_rows($result) > 0) {
        while ($row = $DRW->fetch_array($result)) {
            $mPanelID = $row['mPanelID'];
            $mPanelName = $row['mPanelName'];
            $arr[$mPanelID] = $mPanelName;
        }
    }
    return $arr;
}

function getMailingType() {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    $arr = array();
    $sql = "select mTypeID, mTypeName from cscan_mtype ORDER BY mTypeName";
    $result = $DRW->query($sql, $DRW_read);
    if ($DRW->num_rows($result) > 0) {
        while ($row = $DRW->fetch_array($result)) {
            $mTypeID = $row['mTypeID'];
            $mTypeName = $row['mTypeName'];
            $arr[$mTypeID] = $mTypeName;
        }
    }
    return $arr;
}

function sectorName($sectorID) {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    if ($sectorID == '') {
        return '';
    }
    $sectorID=ltrim($sectorID,',') ;
    $sectorID=rtrim($sectorID,',') ;
    $sectorQuery = "SELECT sectorName from cscan_sector where sectorID IN ($sectorID) ORDER BY sectorName";
    $sectorQuery = $DRW->query($sectorQuery, $DRW_read);
    $sectorName = array();
    while ($sectorRow = $DRW->fetch_assoc($sectorQuery)) {
        $sectorName[] = $sectorRow['sectorName'];
    }
    $sectorName = implode(", ", $sectorName);
    return $sectorName;
}

function categoryName($categoryID) {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    if ($categoryID == '') {
        return '';
    }
    $categoryID=ltrim($categoryID,',') ;
    $categoryID=rtrim($categoryID,',') ;
    $categoryQuery = "SELECT cs1.sectorName from cscan_sector cs1, cscan_sector cs2 where cs1.parentID=cs2.sectorID AND cs1.sectorID IN ($categoryID) ORDER BY cs2.sectorName,cs1.sectorName";
    $categoryQuery = $DRW->query($categoryQuery, $DRW_read);
    $sectorName = array();
    while ($sectorRow = $DRW->fetch_assoc($categoryQuery)) {
        $sectorName[] = $sectorRow['sectorName'];
    }
    $categoryName = implode(", ", $sectorName);
    return $categoryName;
}

function subCategoryName($subCategoryID, $showparent = false) {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    //echo $subCategoryID;
    if ($subCategoryID != '' && $subCategoryID != '-1' && $subCategoryID != '0') {
        $subCategoryQuery = "SELECT cs1.sectorName as sectorName,cs2.sectorName as sectorParent from cscan_sector cs1, cscan_sector cs2, cscan_sector cs3 where cs1.parentID=cs2.sectorID AND cs2.parentID=cs3.sectorID AND cs1.sectorID IN ($subCategoryID) ORDER BY cs3.sectorName,cs2.sectorName,cs1.sectorName";
        $subCategoryQuery = $DRW->query($subCategoryQuery, $DRW_read);
        $name = array();
        if (!$showparent && $DRW->num_rows($subCategoryQuery) > 1) {
            $showparent = false; //true;
        }
        while ($row = $DRW->fetch_assoc($subCategoryQuery)) {
            $tmp = '';
            if ($showparent || $row['sectorName'] == 'Commercial' || $row['sectorName'] == 'Personal') {
                $tmp .= $row['sectorParent'] . '-';
            }
            $tmp .= $row['sectorName'];
            $name[] = $tmp;
        }
        $name = implode(', ', $name);
        return $name;
    }
    return 'Not mentioned';
}

function mediaChannelName($mChannelID) {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    if ($mChannelID == '') {
        return '';
    }
    $mChannelID=ltrim($mChannelID,',') ;
    $mChannelID=rtrim($mChannelID,',') ;
    $mediaChannelQuery = "SELECT mChannelName from cscan_mchannel where mChannelID IN ($mChannelID) ORDER BY mChannelName";
    $mediaChannelQuery = $DRW->query($mediaChannelQuery, $DRW_read);
    $mediaChannelName = array();
    while ($mChannelRow = $DRW->fetch_assoc($mediaChannelQuery)) {
        $mediaChannelName[] = $mChannelRow['mChannelName'];
    }
    $mediaChannel = implode(", ", $mediaChannelName);
    return $mediaChannel;
}

function mediaPanelName($mPanelID) {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    if ($mPanelID == '') {
        return '';
    }
    $mPanelID=ltrim($mPanelID,',') ;
    $mPanelID=rtrim($mPanelID,',') ;
    $mediaPanelQuery = "SELECT mPanelName from cscan_mpanel where mPanelID IN ($mPanelID) ORDER BY mPanelName";
    $mediaPanelQuery = $DRW->query($mediaPanelQuery, $DRW_read);
    $mediaPanelName = array();
    while ($mPanelRow = $DRW->fetch_assoc($mediaPanelQuery)) {
        $mediaPanelName[] = $mPanelRow['mPanelName'];
    }
    $mediaPanel = implode(", ", $mediaPanelName);
    return $mediaPanel;
}

function mediaType($mTypeID) {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    if (getType($mTypeID) != 'array') {
        if ($mTypeID == '') {
            return '';
        }
        $mTypeID=ltrim($mTypeID,',') ;
        $mTypeID=rtrim($mTypeID,',') ;
        $mediaTypeQuery = "SELECT mTypeName from cscan_mtype where mTypeID IN ($mTypeID) ORDER BY mTypeName";
        $mediaTypeQuery = $DRW->query($mediaTypeQuery, $DRW_read);
        $mediaTypeName = array();
        while ($mTypeRow = $DRW->fetch_assoc($mediaTypeQuery)) {
            $mediaTypeName[] = $mTypeRow['mTypeName'];
        }
        $mediaTypeName = implode(", ", $mediaTypeName);
    } else {
        $mediaTypeName = 'Any';
    }
    return $mediaTypeName;
}

function stateName($stateID, $usecode = false) {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    if (getType($stateID) != 'array' && trim($stateID) != '' && $stateID != '0') {
        if ($stateID == '') {
            return '';
        }
        $stateID=ltrim($stateID,',') ;
        $stateID=rtrim($stateID,',') ;
        $stateQuery = "SELECT stateName,stateCode from cscan_state where stateID IN ($stateID) ORDER BY stateName";
        $stateQuery = $DRW->query($stateQuery, $DRW_read);
        $stateName = array();
        while ($stateRow = $DRW->fetch_assoc($stateQuery)) {
            if ($usecode) {
                $stateName[] = $stateRow['stateCode'];
            } else {
                $stateName[] = $stateRow['stateName'];
            }
        }
        $stateName = implode(", ", $stateName);
    } else {
        $stateName = 'Any';
    }
    return $stateName;
}

function languageName($lang) {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    $langarray = explode(',', $lang);
    foreach ($langarray as $key => $val) {
        $langarray[$key] = "'$val'";
    }
    $langtext = implode(',', $langarray);
    if ($langtext != '') {
        $langQuery = "SELECT Language from ISO639Language where ISO639_1Code IN ($langtext) ORDER BY Language";
        $langQuery = $DRW->query($langQuery, $DRW_read);
        $langName = array();
        while ($langRow = $DRW->fetch_assoc($langQuery)) {
            $langName[] = $langRow['Language'];
        }
        if (count($langName) > 0) {
            $lang = implode(", ", $langName);
        }
    }
    return $lang;
}

function agentName($agentCommunicationID) {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    if ($agentCommunicationID != '') {
        $agentCommunicationID=ltrim($agentCommunicationID,',') ;
        $agentCommunicationID=rtrim($agentCommunicationID,',') ;
        $sql = "SELECT type FROM cscan_agent_communication WHERE ID IN($agentCommunicationID) ORDER BY type";
        $rs = $DRW->query($sql, $DRW_read);
        $agent_type = array();
        while (list($type) = $DRW->fetch_row($rs)) {
            //$type = str_replace('/',' ',$type);
            $agent_type[] = $type;
        }
        $agent_type = @implode(', ', $agent_type);
    } else {
        $agent_type = 'N/A';
    }
    return $agent_type;
}

function getDelMeth($ID) {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    if ($ID != '') {
        $ID=ltrim($ID,',') ;
        $ID=rtrim($ID,',') ;
        $sql = "SELECT delmethname FROM cscan_delivery_method WHERE delmethid IN ($ID) ORDER BY delmethsort";
        $rs = $DRW->query($sql, $DRW_read);
        $types = array();
        while (list($type) = $DRW->fetch_row($rs)) {
            $types[] = $type;
        }
        $types = @implode(', ', $types);
    } else {
        $types = 'N/A';
    }
    return $types;
}
############################## Start Envelope/Postage Data Fields################

function getDeliveryType() {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    $query = "SELECT deliveryTypeId,deliveryTypeName FROM cscan_delivery_type ORDER BY deliveryTypeName";
    $result = $DRW->query($query, $DRW_read);
    $array = array();
    while ($row = $DRW->fetch_array($result)) {
        $array[$row['deliveryTypeId']] = $row['deliveryTypeName'];
    }
    return $array;
} 

function getDelType($ID) {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    if ($ID != '') {
       
        $sql = "SELECT deliveryTypeName FROM cscan_delivery_type WHERE deliveryTypeId = $ID ORDER BY deliveryTypeName";
        $rs = $DRW->query($sql, $DRW_read);
        $types = array();
        while (list($type) = $DRW->fetch_row($rs)) {
            $types[] = $type;
        }
        $types = @implode(', ', $types);
    } else {
        $types = 'N/A';
    }
    return $types;
}

function getPostage() {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    $query = "SELECT postageId,postageName FROM cscan_postage ORDER BY 	postageName";
    $result = $DRW->query($query, $DRW_read);
    $array = array();
    while ($row = $DRW->fetch_array($result)) {
        $array[$row['postageId']] = $row['postageName'];
    }
    return $array;
} 

function getPostageName($ID) {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    if ($ID != '') {
        $sql = "SELECT postageName FROM cscan_postage WHERE postageId = $ID ORDER BY postageName";
        $rs = $DRW->query($sql, $DRW_read);
        $types = array();
        while (list($type) = $DRW->fetch_row($rs)) {
            $types[] = $type;
        }
        $types = @implode(', ', $types);
    } else {
        $types = 'N/A';
    }
    return $types;
}

function getPresorted() {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    $query = "SELECT presortedId,presortedName FROM cscan_presorted ORDER BY presortedName";
    $result = $DRW->query($query, $DRW_read);
    $array = array();
    while ($row = $DRW->fetch_array($result)) {
        $array[$row['presortedId']] = $row['presortedName'];
    }
    return $array;
} 

function getPresortedName($ID) {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    if ($ID != '') {
        $sql = "SELECT presortedName FROM cscan_presorted WHERE presortedId = $ID ORDER BY presortedName";
        $rs = $DRW->query($sql, $DRW_read);
        $types = array();
        while (list($type) = $DRW->fetch_row($rs)) {
            $types[] = $type;
        }
        $types = @implode(', ', $types);
    } else {
        $types = 'N/A';
    }
    return $types;
}

function getPackageType() {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    $query = "SELECT packageTypeId,packageName FROM cscan_package_type ORDER BY packageName";
    $result = $DRW->query($query, $DRW_read);
    $array = array();
    while ($row = $DRW->fetch_array($result)) {
        $array[$row['packageTypeId']] = $row['packageName'];
    }
    return $array;
} 

function getPackageName($ID) {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    if ($ID != '') {
        $sql = "SELECT packageName FROM cscan_package_type WHERE packageTypeId = $ID ORDER BY packageName";
        $rs = $DRW->query($sql, $DRW_read);
        $types = array();
        while (list($type) = $DRW->fetch_row($rs)) {
            $types[] = $type;
        }
        $types = @implode(', ', $types);
    } else {
        $types = 'N/A';
    }
    return $types;
}
############################## Start Envelope/Postage Data Fields################
//not used?
function getAgentCommunicationType() {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    $query = "SELECT * FROM cscan_agent_communication order by type";
    $result = $DRW->query($query, $DRW_read);
    $array = array();
    while ($row = $DRW->fetch_array($result)) {
        $array[$row['ID']] = $row['type'];
    }
    return $array;
}

//not used?
function getMailPackItem() {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    $arr = array();
    $sql = "select mPackItemID, mPackItemName from cscan_mpack_item ORDER BY mPackItemName";
    $result = $DRW->query($sql, $DRW_read);
    if ($DRW->num_rows($result) > 0) {
        while ($row = $DRW->fetch_array($result)) {
            $mPackItemID = $row['mPackItemID'];
            $mPackItemName = $row['mPackItemName'];
            $arr[$mPackItemID] = $mPackItemName;
        }
    }
    return $arr;
}

//not used?
function countryName($countryID) {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    $countryQuery = "SELECT countryName from cscan_country where countryID = '$countryID'";
    $countryQuery = $DRW->query($countryQuery, $DRW_read);
    $rs = $DRW->fetch_assoc($countryQuery);
    $countryName = $rs['countryName'];
    return $countryName;
}

//not used?
function mediaPackItem($mPackItemID) {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    $mediaPackItemQuery = "SELECT mPackItemName from cscan_mpack_item where mPackItemID = '$mPackItemID'";
    $mediaPackItemQuery = $DRW->query($mediaPackItemQuery, $DRW_read);
    $rs = $DRW->fetch_assoc($mediaPackItemQuery);
    $mediaPackItemName = $rs['mChannelName'];
    return $mediaPackItemName;
}

//not used?
function getSingleParentUserName($userID) {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    $sql = "select emailAddress from cscan_users where userID='$userID'";
    $query = $DRW->query($sql, $DRW_read);
    while ($result = $DRW->fetch_assoc($query)) {
        $str = $result['emailAddress'];
    }
    return $str;
}

//not used?
function getUser() {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    $sql = "select userID,emailAddress from cscan_users ORDER BY emailAddress";
    $query = $DRW->query($sql, $DRW_read);
    while ($result = $DRW->fetch_assoc($query)) {
        $val = $result['emailAddress'];
        $key = $result['userID'];
        $str[$key] = $val;
    }
    return $str;
}

function getCompanyName($companyID) {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    $tmpCompany = $companyID;
    $checkArray = explode(',', $companyID);
    foreach ($checkArray as $key => $check) {
        $checkArray[$key] = intval($check);
    }
    $companyID = implode(",", $checkArray);
    if ($companyID == 0) {
        return $tmpCompany;
    }
    $companyQuery = "SELECT companyName from cscan_company where companyID IN ($companyID) ORDER BY companyName";
    $companyQuery = $DRW->query($companyQuery, $DRW_read);
    $companyName = array();
    while ($companyRow = $DRW->fetch_assoc($companyQuery)) {
        $companyName[] = $companyRow['companyName'];
    }
    $companyName = implode(", ", $companyName);
    return $companyName;
}

function generate_entryID($insert = false, $currentDate = '') {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    if ($currentDate == '') {
        $currentDate = date('Y-m-d');
    } else {
        $currentDate = substr($currentDate, 0, 10);
    }
    
    ############ remove the lock table condition #################
//    if ($insert) {
//        $sql = "LOCK TABLE cscan_entry_inc WRITE, cscan_entry_inc AS cei READ";
//        $DRW->query($sql, $DRW_main);
//    }
     ############ end remove the lock table condition #################
    $entryQuery = "SELECT incID FROM cscan_entry_inc AS cei WHERE entryDate='$currentDate'";
    $entryQuery = $DRW->query($entryQuery, $DRW_main);
    $rs = $DRW->fetch_row($entryQuery);
    $incID = (int) $rs[0] + 1;
    if ($insert) {
        $sql = "REPLACE INTO cscan_entry_inc (entryDate,incID) VALUES ('$currentDate',$incID)";
        $DRW->query($sql, $DRW_main);
    ############ remove the lock table condition #################
//        $sql = "UNLOCK TABLES";
//        $DRW->query($sql, $DRW_main);
         ############ remove the lock table condition #################
    }
    return $currentDate . "-" . str_pad($incID, 2, '0', STR_PAD_LEFT);
}

function getVariant($vid, $variant_desc = '') {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    if ($vid == '0' && $vid == '') {
        return 'Other';
    }
    $tmpArray = array();
    $vidArray = explode(',', $vid);
    foreach ($vidArray as $v) {
        if ($v == '0') {
            if ($variant_desc != '') {
                $tmpArray[] = 'Other: ' . $variant_desc;
            } else {
                $tmpArray[] = 'Other';
            }
        } elseif ($v != '') {
            $sql = "SELECT vname FROM cscan_varianttype WHERE vid=" . (int) $v;
            $result = $DRW->query($sql, $DRW_read);
            while ($row = $DRW->fetch_row($result)) {
                $tmpArray[] = $row[0];
            }
        }
    }
    return implode(', ', $tmpArray);
}

//you need to add $variantArray = array(); before call
function getAllVariantsArray($productID, &$variantArray) {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    if ($productID != 0 && !key_exists($productID, $variantArray)) {
        $checkV = "SELECT variantID,entryID,vid,variant_desc FROM cscan_product_detail WHERE productID='" . $DRW->real_escape_string($productID) . "' AND productStatus=1";
        $checkV = $DRW->query($checkV, $DRW_read);
        $dataV = $DRW->fetch_row($checkV);
        $variantID = (int) $dataV[0];
        $entryID = $dataV[1];
        $vid = $dataV[2];
        $variant_desc = $dataV[3];
        if (!empty($entryID)) {
            $variantArray[$productID] = array();
            $variantArray[$productID]['entryID'] = $entryID;
            if ($vid == '0' && $variant_desc == '')
                $variantArray[$productID]['desc'] = '';
            else
                $variantArray[$productID]['desc'] = getVariant($vid, $variant_desc);
        }
        getAllVariantsArray($variantID, $variantArray);
        $checkV = "SELECT productID FROM cscan_product_detail WHERE variantID='" . $DRW->real_escape_string($productID) . "' AND productStatus=1";
        $checkV = $DRW->query($checkV, $DRW_read);
        while ($dataV = $DRW->fetch_row($checkV)) {
            $newproductID = (int) $dataV[0];
            getAllVariantsArray($newproductID, $variantArray);
        }
    }
}

function get_seccatsub($sectorID = array(), $categoryID = array(), $subCategoryID = array(), $subSubCategoryID = array()) {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    $seccatsubArray = array();
    $tmpArray = explode(',', $sectorID);
    foreach ($tmpArray as $v) {
        if (!empty($v)) {
            $seccatsubArray[$v] = array();
        }
    }
    if (!empty($categoryID)) {
        $tmpArray = explode(',', $categoryID);
        foreach ($tmpArray as $cid) {
            if (!empty($cid)) {
                $sqlc = "SELECT parentID FROM cscan_sector WHERE sectorID=$cid";
                $rsc = $DRW->query($sqlc, $DRW_read);
                $rowc = $DRW->fetch_row($rsc);
                if (!empty($rowc[0])) {
                    if (!isset($seccatsubArray[$rowc[0]])) {
                        continue; //$seccatsubArray[$rowc[0]] = array();
                    }
                    $seccatsubArray[$rowc[0]][$cid] = array();
                }
            }
        }
    }
    if (!empty($subCategoryID)) {
        $tmpArray = explode(',', $subCategoryID);
        foreach ($tmpArray as $scid) {
            if (!empty($scid)) {
                $sqlc = "SELECT s.parentID,c.parentID FROM cscan_sector s,cscan_sector c WHERE s.sectorID=$scid AND s.parentID=c.sectorID";
                $rsc = $DRW->query($sqlc, $DRW_read);
                $rowc = $DRW->fetch_row($rsc);
                if (!empty($rowc[1])) {
                    if (!isset($seccatsubArray[$rowc[1]])) {
                        continue; //$seccatsubArray[$rowc[1]] = array();
                    }
                    if (!isset($seccatsubArray[$rowc[1]][$rowc[0]])) {
                        continue; //$seccatsubArray[$rowc[1]][$rowc[0]] = array();
                    }
                    $seccatsubArray[$rowc[1]][$rowc[0]][$scid] = array();
                }
            }
        }
    }
    if (!empty($subSubCategoryID)) {
        $tmpArray = explode(',', $subSubCategoryID);
        foreach ($tmpArray as $scid) {
            if (!empty($scid)) {
                $sqlc = "SELECT s.parentID,c.parentID,sc.parentID FROM cscan_sector s,cscan_sector c,cscan_sector sc WHERE s.sectorID=$scid AND s.parentID=c.sectorID AND c.parentID=sc.sectorID";
                $rsc = $DRW->query($sqlc, $DRW_read);
                $rowc = $DRW->fetch_row($rsc);
                if (!empty($rowc[1])) {
                    if (!isset($seccatsubArray[$rowc[2]])) {
                        continue; //$seccatsubArray[$rowc[2]] = array();
                    }
                    if (!isset($seccatsubArray[$rowc[2]][$rowc[1]])) {
                        continue; //$seccatsubArray[$rowc[2]][$rowc[1]] = array();
                    }
                    if (!isset($seccatsubArray[$rowc[2]][$rowc[1]][$rowc[0]])) {
                        continue; //$seccatsubArray[$rowc[2]][$rowc[1]][$rowc[0]] = array();
                    }
                    $seccatsubArray[$rowc[2]][$rowc[1]][$rowc[0]][$scid] = array();
                }
            }
        }
    }
    return $seccatsubArray;
}

function saveImageData($productID, $imagePath, $thumbimageName, $img_path = '', $img_companyID = 0) {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    $sql2 = "SELECT img_id,img_path,img_filename FROM cscan_img WHERE productID=$productID";
    $rs2 = $DRW->query($sql2, $DRW_read);
    $row2 = $DRW->fetch_row($rs2);
    $img_id = (int) $row2[0];
    $img_path_o = $row2[1];
    $img_filename_o = $row2[2];
    $maxbytes = 500000;
    if ($img_companyID == 0) {
        $src = $imagePath . $thumbimageName;
        $ext = 'jpeg';
        $tmpext = strtolower(substr($thumbimageName, -3));
        if ($tmpext != 'jpg' && $tmpext != 'peg')
            $ext = $tmpext;
        $img_size_byte = filesize($src);
        $img_content_type = "image/$ext";
    }
    else {
        $img_size_byte = 0;
        $img_content_type = '';
    }
    if ($img_id != 0) {
        $root = dirname(__FILE__);
        if (strpos($root, '/includes') !== false) {
            $root = substr($root, 0, strpos($root, '/includes'));
        }
        $oldpath = $root . $img_path_o . $img_filename_o;
        if (is_file($oldpath) && $oldpath != $root . $img_path . $thumbimageName) {
            unlink($oldpath);
        }
        $sql = "UPDATE cscan_img SET img_filename='" . $DRW->real_escape_string($thumbimageName) . "',img_createddate=NOW(),img_content_type='$img_content_type',img_size_byte=$img_size_byte,img_createdby={$GLOBALS['AUTH_DATA']['userID']},img_path='" . $DRW->real_escape_string($img_path) . "',img_companyID=$img_companyID
			WHERE productID=$productID AND img_id=$img_id";
        $DRW->query($sql, $DRW_main);
    } else {
        $img_id = 1;
        $sql = "REPLACE INTO cscan_img (productID,img_id,img_filename,img_createddate,img_content_type,img_size_byte,img_createdby,img_path,img_companyID)
			VALUES ($productID,$img_id,'" . $DRW->real_escape_string($thumbimageName) . "',NOW(),'$img_content_type',$img_size_byte,{$GLOBALS['AUTH_DATA']['userID']},'" . $DRW->real_escape_string($img_path) . "',$img_companyID)";
        $DRW->query($sql, $DRW_main);
    }
}

function savePDFData($productID, $pdfPath, $pdfName, $pdfContent = "", $document_path = '', $do_original = false, $document_content_type = 'application/pdf') {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    global $s3,$bucket_name,$serverbaseurl;
    $maxbytes = 500000;
    $file = $pdfPath . $pdfName;
    $document_size_byte = filesize($file);
    if ($document_content_type == 'application/pdf') {
        $document_id = 1;
        $document_placement = '';
    } else {
        $document_id = 2;
        list($obj_width, $obj_height) = getimagesize($file);
        if ($obj_width == 0) { //can't determine size
            $document_placement = "200x200";
        } else {
            $document_placement = $obj_width . "x" . $obj_height;
        }
    }
    $sql2 = "SELECT document_id,document_path,document_filename FROM cscan_document WHERE productID=$productID AND document_id=" . $document_id; //primary document
    $rs2 = $DRW->query($sql2, $DRW_read);
    $row2 = $DRW->fetch_row($rs2);
    $did = (int) $row2[0];
    $document_id_o = 0;
    $document_path_o = $row2[1];
    $document_filename_o = $row2[2];
    if ($did != 0) {
        $root = dirname(__FILE__);
        if (strpos($root, '/includes') !== false) {
            $root = substr($root, 0, strpos($root, '/includes'));
        }
        $oldpath = $root . $document_path_o . $document_filename_o;
       // try {
                /*
                $result = $s3->deleteObject([
                    'Bucket' => $bucket_name,
                    'Key' => substr($document_path_o,1) . $document_filename_o,
                ]);
                */
                
       // } catch (S3Exception $e) {
      //      echo $e->getMessage() . "\n";
        //}
        
        
        if (is_file($oldpath) && $oldpath != $root . $document_path . $pdfName) {
            unlink($oldpath);
            $info = $s3->doesObjectExist($bucket_name,substr($document_path_o,1) . $document_filename_o);
            if($info){
                $result = $s3->deleteObject([
                    'Bucket' => $bucket_name,
                    'Key' => substr($document_path_o,1) . $document_filename_o,
                ]);
            }
        }
        $sql = "UPDATE cscan_document SET document_placement='$document_placement', document_filename='" . $DRW->real_escape_string($pdfName) . "',document_createddate=NOW(),document_content_type='" . $DRW->real_escape_string($document_content_type) . "',document_size_byte=$document_size_byte,document_createdby={$GLOBALS['AUTH_DATA']['userID']},document_path='" . $DRW->real_escape_string($document_path) . "',bucket_name='NULL'
			WHERE productID=$productID AND document_id=$document_id";
        $DRW->query($sql, $DRW_main);
    } else {
        $sql = "REPLACE INTO cscan_document (productID,document_id,document_filename,document_createddate,document_content_type,document_size_byte,document_createdby,document_path,document_placement)
			VALUES ($productID,$document_id,'" . $DRW->real_escape_string($pdfName) . "',NOW(),'" . $DRW->real_escape_string($document_content_type) . "','".$document_size_byte."','".$GLOBALS['AUTH_DATA']['userID']."','" . $DRW->real_escape_string($document_path) . "','$document_placement')";
        $DRW->query($sql, $DRW_main);
        if ($do_original) {
            $document_path_o = $document_path . 'orig/';
            $file_o = $pdfPath . 'orig/' . $pdfName;
            if (!is_dir($pdfPath . 'orig/')) {
                mkdir($pdfPath . 'orig/', 02755);
                @chmod($pdfPath . 'orig/', 02755);
                @chown($pdfPath . 'orig/', 'apache');
                //@chgrp($pdfPath.'orig/','competiscan_web');
            }
            if (is_file($file) && copy($file, $file_o)) {
                @chmod($file_o, 02755);
                @chown($file_o, 'apache');
                //@chgrp($file_o,'competiscan_web');
                
            /* ####### Added for S3 Implementation #######*/                
            try{    
                $result = $s3->putObject([
                    'Bucket' => $bucket_name,
                    'Key'    => ltrim($document_path_o,"/"). $pdfName,
                    'SourceFile' => $file,
                    'ACL'    => 'public-read',
                    'ContentType'	=> $document_content_type,
                    'Metadata'      => array(
                       'string'        => 'string'
                     )
                ]);
                $result2 = $s3->putObject([
                    'Bucket' => $bucket_name,
                    'Key'    => ltrim($document_path,"/"). $pdfName,
                    'SourceFile' => $file,
                    'ACL'    => 'public-read',
                    'ContentType'	=> $document_content_type,
                    'Metadata'      => array(
                       'string'        => 'string'
                     )
                ]);
//                echo $file_o;
//                echo '<br> ';
//                 echo $file;
//                 echo '<br><br><br><br> ';
                if (is_file($file_o)) {
                   // echo 'pppppp ';
                   // echo '<br> ';
                    unlink($file_o);
                }
                if (is_file($file)) {
                    //echo ' pppppp22';
                   // echo '<br><br>';
                   // unlink($file);
                }                
            } catch (S3Exception $e) {
                echo 'error found';
                echo $e->getMessage() . PHP_EOL;
            }    
                
            /* ####### End Added for S3 Implementation #######*/
            
                $sql = "REPLACE INTO cscan_document_orig (productID,document_filename,document_createddate,document_content_type,document_size_byte,document_createdby,document_path,document_placement)
					VALUES ($productID,'" . $DRW->real_escape_string($pdfName) . "',NOW(),'" . $DRW->real_escape_string($document_content_type) . "','".$document_size_byte."','".$GLOBALS['AUTH_DATA']['userID']."','" . $DRW->real_escape_string($document_path_o) . "','$document_placement')";
                $DRW->query($sql, $DRW_main);
            }
        }
    }
    if ($document_content_type == "application/pdf") {
        if ($pdfContent == '') {
            $pdfContent_tmp = shell_exec('/usr/bin/pdftotext -q ' . escapeshellarg($file) . ' -');
            if ($pdfContent_tmp != '') {
                $pdfContent = $pdfContent_tmp;
            }
        }
        savePDFText($productID, $document_id, $pdfContent, $maxbytes);
        if (is_file($file)) {
            //echo ' pppppp22';
           // echo '<br><br>';
           unlink($file);
        }   
    }
    //extractPDFData($productID, $document_id);
    return $document_id;
}

function savePDFText($productID, $document_id, $pdfContent = '', $maxbytes = 500000) {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    if (empty($document_id)) {
        $document_id = 1;
    }
    //echo "pdfcontent====>".$pdfContent;
    $query2 = "SELECT SQL_NO_CACHE COUNT(*) FROM cscan_document_text_search WHERE productID=$productID AND document_id=$document_id";
    $query_result2 = $DRW->query($query2, $DRW_read);
    $data2 = $DRW->fetch_row($query_result2);
    $count = (int) $data2[0];
    if ($count > 0) {
        deleteSphinx(array($productID), array($document_id));
        $sql = "DELETE FROM cscan_document_text_search WHERE productID=$productID AND document_id=$document_id";
        $DRW->query($sql, $DRW_main);
    }
    $pdfContent = clean_pdfContent($pdfContent);
    $wrap = wordwrap($pdfContent, $maxbytes, "\n");
    $stringArray = preg_split('/\\n/', $wrap, -1, PREG_SPLIT_NO_EMPTY);
    $document_text_part = 1;
    //echo"<br>stringarray===>";
    //print_r($stringArray);
    
    foreach ($stringArray as $val) {
        $sql = "INSERT INTO cscan_document_text_search (productID,document_id,dts_val,dts_part) values ($productID,$document_id,'" . $DRW->real_escape_string($val) . "',$document_text_part)";
        $DRW->query($sql, $DRW_main);
        $document_text_part++;
    }
}

function savePDFImage($productID, $document_id, $pdfPath, $maxbytes = 500000) {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    global $s3,$bucket_name,$serverbaseurl;
    $root = dirname(__FILE__);
    if (strpos($root, '/includes') !== false) {
        $root = substr($root, 0, strpos($root, '/includes'));
    }
    $checkV = "SELECT img_document_path,img_document_filename FROM cscan_img_document WHERE productID=$productID AND document_id=$document_id";
    $checkV = $DRW->query($checkV, $DRW_read);
    while ($dataV = $DRW->fetch_row($checkV)) {
        $path = $root . $dataV[0] . $dataV[1];
        $info = $s3->doesObjectExist($bucket_name,$dataV[0] . $dataV[1]);
        if($info){
                $result = $s3->deleteObject([
                    'Bucket' => $bucket_name,
                    'Key' => $dataV[0] . $dataV[1],
                ]);
        }
        
        
        if (is_file($path) && $path != $pdfPath . $dataV[1]) {
           // @unlink($path);
        }
    }
    $sql = "DELETE FROM cscan_img_document WHERE productID=$productID AND document_id=$document_id";
    $DRW->query($sql, $DRW_main);
    $img_document_default = 1;
    $img_document_sort = 0;
    $image = $pdfPath . $productID . $img_document_sort . '.jpg';
    $img_document_path = substr($pdfPath, strlen($root));
    while (is_file($image)) {
        $img_size_byte = filesize($image);
        $img_document_sort_ins = $img_document_sort + 1;
        $sql = "REPLACE INTO cscan_img_document (productID,document_id,img_document_sort,img_document_filename,img_document_createddate,img_document_content_type,img_document_size_byte,img_document_createdby,img_document_default,img_document_path)
			VALUES ($productID,$document_id,$img_document_sort_ins,'" . $DRW->real_escape_string($productID . $img_document_sort . '.jpg') . "',NOW(),'image/jpeg',$img_size_byte,{$GLOBALS['AUTH_DATA']['userID']},$img_document_default,'" . $DRW->real_escape_string($img_document_path) . "')";
        $DRW->query($sql, $DRW_main);
         sendthumbimageons3($img_document_path , $DRW->real_escape_string($productID . $img_document_sort . '.jpg') );
        
        $img_document_sort++;
        $image = $pdfPath . $productID . $img_document_sort . '.jpg';
        $img_document_default = 0;
        
    }
}
// for using social media image conversion to old process
function savePDFImage1($productID, $document_id, $pdfPath, $maxbytes = 500000) {
   global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    global $s3,$bucket_name,$serverbaseurl;
    $root = dirname(__FILE__);
    if (strpos($root, '/includes') !== false) {
        $root = substr($root, 0, strpos($root, '/includes'));
    }
    $checkV = "SELECT img_document_path,img_document_filename FROM cscan_img_document WHERE productID=$productID AND document_id=$document_id";
    $checkV = $DRW->query($checkV, $DRW_read);
    while ($dataV = $DRW->fetch_row($checkV)) {
        $path = $root . $dataV[0] . $dataV[1];
        if(strpos($dataV[0],'/')=='0'){
         $dataV[0]  = substr($dataV[0],1);
        } 
        $info = $s3->doesObjectExist($bucket_name,$dataV[0] . $dataV[1]);
        if($info){
                $result = $s3->deleteObject([
                    'Bucket' => $bucket_name,
                    'Key' => $dataV[0] . $dataV[1],
                ]);
        }
        
        
        if (is_file($path) && $path != $pdfPath . $dataV[1]) {
           // @unlink($path);
        }
    }
    $sql = "DELETE FROM cscan_img_document WHERE productID=$productID AND document_id=$document_id";
    $DRW->query($sql, $DRW_main);
    $img_document_default = 1;
    $img_document_sort = 0;
    $img_document_path = substr($pdfPath, strlen($root));
    $images = glob($pdfPath."*.jpg");
    foreach($images as $image) {
        $image_name=end(explode('/', $image));
        $img_size_byte = filesize($image);
        $img_document_sort_ins = $img_document_sort + 1;
        $sql = "REPLACE INTO cscan_img_document (productID,document_id,img_document_sort,img_document_filename,img_document_createddate,img_document_content_type,img_document_size_byte,img_document_createdby,img_document_default,img_document_path)
			VALUES ($productID,$document_id,$img_document_sort_ins,'" . $DRW->real_escape_string($image_name) . "',NOW(),'image/jpeg',$img_size_byte,{$GLOBALS['AUTH_DATA']['userID']},$img_document_default,'" . $DRW->real_escape_string($img_document_path) . "')";
        $DRW->query($sql, $DRW_main);
         sendthumbimageons3($img_document_path , $DRW->real_escape_string($image_name));
        $img_document_sort++;
        $img_document_default = 0;
        
    }
}

function clean_pdfContent($pdfContent) {
    //$pdfContent = preg_replace("/'{2,}/",' ',$pdfContent);
    //$pdfContent = str_replace(chr(145), "'", $pdfContent);// left single quote
    //$pdfContent = str_replace(chr(146), "'", $pdfContent);// right single quote
    $pdfContent = preg_replace('/[^a-zA-Z0-9_]+/', ' ', $pdfContent); // \'
    $pdfContent = preg_replace('/\\s+/', ' ', $pdfContent);
    return $pdfContent;
}

function extractPDFData($productID, $document_id) {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    $working_text = $DRW->query("SELECT dts_val FROM cscan_document_text_search WHERE productID = '$productID' AND document_id = '$document_id'", $DRW_read);
    $sectors = array();
    $categories = array();
    $subcategories = array();
    $states = array();
    $companies = array();
    if ($DRW->num_rows($working_text)) {
        $working_text = strtolower(array_shift($DRW->fetch_array($working_text)));
        //Try to find sectors->categories->subcategories based on OCR'd text
        $mSectors = getSector();
        foreach ($mSectors as $sectorID => $sectorName) {
            if (eregi(" $sectorName ", $working_text)) {
                $mCategories = getCategory($sectorID);
                $sectors[] = $sectorID;
                foreach ($mCategories as $categoryID => $categoryName) {
                    if (eregi(" $categoryName ", $working_text)) {
                        $categories[] = $categoryID;
                        $mSubCategories = getSubCategory($categoryID);
                        if ($mSubCategories)
                            foreach ($mSubCategories as $subCategoryID => $subCategoryName) {
                                if (eregi(" $subCategoryName ", $working_text))
                                    $subcategories[] = $subCategoryID;
                            }
                    }
                }
            }
        }
        $stateOpts = getStatesArray();
        foreach ($stateOpts as $stateID => $stateName) {
            if (eregi(" $stateName ", $working_text)) {
                $states[] = $stateID;
            }
        }
        $mCompanies = getCompanies();
        foreach ($mCompanies as $companyID => $companyName) {
            if (eregi(" $companyName ", $working_text)) {
                $companies[] = $companyID;
            }
        }
    }
    $DRW->query("UPDATE cscan_product_detail SET 
					sectorID = '" . implode(', ', $sectors) . "', 
					categoryID='" . implode(', ', $categories) . "',
					subCategoryID='" . implode(', ', $subcategories) . "',
					state='" . implode(', ', $states) . "' 
				WHERE productID = '$productID'
				LIMIT 1", $DRW_main);
    foreach ($companies as $k => $company) {
        $DRW->query("INSERT IGNORE INTO cscan_company_product (companyID, productID, primary_co) VALUES('$company', '$productID', '0')", $DRW_main);
    }
}

function getStatesArray() {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    $tmp = array();
    $z = $DRW->query("SELECT * FROM cscan_state WHERE stateID<>99", $DRW_read);
    while ($z && $zz = $DRW->fetch_assoc($z)) {
        $tmp[$zz['stateID']] = $zz['stateName'];
    }
    return $tmp;
}

function getCountriesArray() {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    $countries = array();
    $countryQuery = "SELECT * from cscan_country";
    $countryQuery = $DRW->query($countryQuery, $DRW_read);
    while ($row = $DRW->fetch_assoc($countryQuery)) {
        $countries[$row['countryID']] = $row['countryName'];
    }
    return $countries;
}

function getCompanies() {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    $tmp = array();
    $z = $DRW->query("SELECT * FROM cscan_company", $DRW_read);
    while ($z && $zz = $DRW->fetch_assoc($z)) {
        $tmp[$zz['companyID']] = $zz['companyName'];
    }
    return $tmp;
}

function saveCompanyImgDB($companyID, $ifile, $img_co_content_type, $img_co_size_byte) {
    #################################### Start S3 Implementation Code ###########################################
    global $DRW, $DRW_read, $DRW_main, $DRW_crm, $s3, $bucket_name;
    #################################### End S3 Implementation Code ###########################################
    $yearpath = date('Y/');
    $monthpath = date('m/');
    $datepath = $yearpath . $monthpath;
    $root = dirname(__FILE__);
    if (strpos($root, '/includes') !== false) {
        $root = substr($root, 0, strpos($root, '/includes'));
    }
    $cpath = '/coImages/';
    $docpart = $root . $cpath;
    if (!is_dir($docpart . $yearpath)) {
        mkdir($docpart . $yearpath, 02755);
    }
    if (!is_dir($docpart . $datepath)) {
        mkdir($docpart . $datepath, 02755);
    }
    $ext = explode('.', $ifile);
    $ext = $ext[count($ext) - 1];
    $img_co_filename = $companyID . "." . $ext;
    #################################### Start S3 Implementation Code ###########################################
    $result = $s3->putObject([
        'Bucket' => $bucket_name,
        'Key'    => 'coImages/' . $datepath . $img_co_filename,
        'SourceFile' => $ifile,
        'ACL'    => 'public-read',
        'ContentType'   => $img_co_content_type,
        'Metadata'      => array(
           'string'        => 'string'
         )
    ]);
    //if (copy($ifile, $docpart . $datepath . $img_co_filename)) {
    if(isset($result['@metadata']['statusCode']) && $result['@metadata']['statusCode'] == 200){
        #################################### End S3 Implementation Code ###########################################
        $checkV = "SELECT img_co_path,img_co_filename FROM cscan_img_company WHERE companyID=$companyID";
        $checkV = $DRW->query($checkV, $DRW_read);
        $dataV = $DRW->fetch_row($checkV);
        if (!empty($dataV[1])) {
            $oldpath = $root . $dataV[0] . $dataV[1];
            if (is_file($oldpath) && $oldpath != $docpart . $datepath . $img_co_filename) {
                unlink($oldpath);
            }
        }
        $sql = "REPLACE INTO cscan_img_company (companyID,img_co_createddate,img_co_content_type,img_co_size_byte,img_co_path,img_co_filename)
			VALUES ($companyID,NOW(),'$img_co_content_type',$img_co_size_byte,'" . $DRW->real_escape_string($cpath . $datepath) . "','" . $DRW->real_escape_string($img_co_filename) . "')";
        $DRW->query($sql, $DRW_main);
    }
}

function deleteProduct($productID_IN) {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm,$s3,$bucket_name;
    $message = '';
    $delID = $productID_IN;
    $delThis = implode(",", $delID);
    $count = count($delID);
    for ($i = 0; $i < $count; $i++) {
        $variantArray = array();
        getAllVariantsArray((int) $delID[$i], $variantArray);
        if (count($variantArray) > 1) {
            unset($variantArray[(int) $delID[$i]]);
            $ids = array_keys($variantArray);
            $firstid = array_pop($ids);
            if (count($ids) > 0) {
                $sqlu = "UPDATE cscan_product_detail SET isVariant=1,variantID=0 WHERE productID=$firstid";
                $DRW->query($sqlu, $DRW_main);
                foreach ($ids as $v) {
                    $sqlu = "UPDATE cscan_product_detail SET isVariant=1,variantID=$firstid WHERE productID=$v";
                    $DRW->query($sqlu, $DRW_main);
                }
            } else {
                $sqlu = "UPDATE cscan_product_detail SET isVariant=0,variantID=0,vid='' WHERE productID=$firstid";
                $DRW->query($sqlu, $DRW_main);
            }
        }
    }
    $root = dirname(__FILE__);
    if (strpos($root, '/includes') !== false) {
        $root = substr($root, 0, strpos($root, '/includes'));
    }
    $checkV = "SELECT img_path,productID FROM cscan_img WHERE productID IN($delThis)";
    $checkV = $DRW->query($checkV, $DRW_read);
    while ($dataV = $DRW->fetch_row($checkV)) {
        $path = $root . $dataV[0];
        if (is_dir($path)) {
            rmDirFiles($path);
        }
    }
    $checkV = "SELECT document_path,productID FROM cscan_document_orig WHERE productID IN($delThis)";
    $checkV = $DRW->query($checkV, $DRW_read);
    while ($dataV = $DRW->fetch_row($checkV)) {
        $path = $root . $dataV[0];
        if (is_dir($path) && !empty($dataV[0]) && $dataV[0] != '/') {
            rmDirFiles($path);
        }
    }
    $checkV = "SELECT document_path,productID FROM cscan_document WHERE productID IN($delThis)";
    $checkV = $DRW->query($checkV, $DRW_read);
    while ($dataV = $DRW->fetch_row($checkV)) {
        $path = $root . $dataV[0];
        ########## for delete all the folder from s3 #############
        $result= $s3->deleteMatchingObjects($bucket_name,substr($dataV[0],1));
        ########## end for delete all the folder from s3 #############
        if (is_dir($path)) {
            rmDirFiles($path);
        }
    }
    $sql = "DELETE FROM cscan_product_detail where productID IN ($delThis)";
    $DRW->query($sql, $DRW_main);
    $delTmp = "UPDATE cscan_product_email SET productID=-1 WHERE productID IN($delThis)";
    $DRW->query($delTmp, $DRW_main);
    $delImg = "DELETE FROM cscan_panelists_product WHERE productID IN($delThis)";
    $DRW->query($delImg, $DRW_main);
    $sql = "DELETE FROM cscan_img where productID IN ($delThis)";
    $DRW->query($sql, $DRW_main);
    $sql = "DELETE FROM cscan_img_document where productID IN ($delThis)";
    $DRW->query($sql, $DRW_main);
    $sql = "DELETE FROM cscan_document_orig where productID IN ($delThis)";
    $DRW->query($sql, $DRW_main);
    $sql = "DELETE FROM cscan_document where productID IN ($delThis)";
    $DRW->query($sql, $DRW_main);
    deleteSphinx($productID_IN);
    deleteSphinx2($productID_IN);
    $sql = "DELETE FROM cscan_document_text_search where productID IN ($delThis)";
    $DRW->query($sql, $DRW_main);
    $sql = "DELETE FROM cscan_company_product where productID IN ($delThis)";
    $DRW->query($sql, $DRW_main);
    $sql = "DELETE FROM cscan_publication_product  where productID IN ($delThis)";
    $DRW->query($sql, $DRW_main);
    $sql = "DELETE FROM cscan_affinity_product where productID IN ($delThis)";
    $DRW->query($sql, $DRW_main);
    $sql = "DELETE FROM cscan_scsc_product where productID IN ($delThis)";
    $DRW->query($sql, $DRW_main);
    $sql = "DELETE FROM cscan_sites_product where productID IN ($delThis)";
    $DRW->query($sql, $DRW_main);
    for ($i = 0; $i < $count; $i++) {
        updateStateLookup((int) $delID[$i], true);
    }
    if ($count > 0) {
        $message = $count . " Product(s) deleted";
    }
    return $message;
}

function doSpend($mail_volume_tot, $document_size_byte = 0) {
    if ($document_size_byte >= 2000000) {
        $dmspend = $mail_volume_tot * (1.1593 * (pow($mail_volume_tot, -0.0382)));
    } elseif ($document_size_byte >= 500000) {// && $document_size_byte<2000000
        $dmspend = $mail_volume_tot * (1.3648 * (pow($mail_volume_tot, -0.0783)));
    } else { //if($document_size_byte<500000){
        $dmspend = $mail_volume_tot * (1.2204 * (pow($mail_volume_tot, -0.1236)));
    }
    return $dmspend;
}

function mysqlLike($string) {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    $searchtext_like = $DRW->real_escape_string($string);
    $searchtext_like = str_replace('%', '\\%', $searchtext_like);
    $searchtext_like = str_replace('_', '\\_', $searchtext_like);
    return $searchtext_like;
}

function singleQuoteSafe($in) {
    $out = str_replace("\\", "\\\\", $in);
    $out = str_replace("'", "\\'", $out);
    return $out;
}

function getEmailParse($in, $host = 'competiscan.com') {
    $emailArray = array();
    $i = 0;
    //not working with ; as separator
    //can count how many change and then replace back in after mail is split if necessary?
    $to = str_replace(';', ',', $in);
    $address_array = imap_rfc822_parse_adrlist($to, $host);
    if (is_array($address_array) && count($address_array) > 0) {
        foreach ($address_array as $id => $val) {
            if (isset($val->mailbox) && $val->mailbox != '' && isset($val->host) && $val->host != '') {
                if ($val->host == '.SYNTAX-ERROR.')
                    continue;
                $emailArray[$i]['address'] = $val->mailbox . '@' . $val->host;
                if (isset($val->personal)) {
                    $emailArray[$i]['name'] = $val->personal;
                } else
                    $emailArray[$i]['name'] = '';
            }
            $i++;
        }
    }
    return $emailArray;
}

function pc_permute($items, &$finalarray, $perms = array()) {
    if (empty($items)) {
        $finalarray[] = $perms;
    } else {
        for ($i = count($items) - 1; $i >= 0; --$i) {
            $newitems = $items;
            $newperms = $perms;
            list($foo) = array_splice($newitems, $i, 1);
            array_unshift($newperms, $foo);
            pc_permute($newitems, $finalarray, $newperms);
        }
    }
}

//use preg_quote instead
function escapeRegex($string) {
    $regchars = array('\\', '.', '*', '?', '+', '[', ']', '(', ')', '{', '}', '^', '$', '|', '/'); //escape regex chars
    $regcharsesc = array("\\\\", "\\.", "\\*", "\\?", "\\+", "\\[", "\\]", "\\(", "\\)", "\\{", "\\}", "\\^", "\\$", "\\|", "\\/");
    $string = str_replace($regchars, $regcharsesc, $string);
    return $string;
}

function escapeBool($string) {
    $boolchars = array('\\', '+', '-', '<', '>', '~', "'", '$'); //,'*','(',')','"'
    $boolcharsesc = array('\\\\', '\\+', '\\-', '\\<', '\\>', '\\~', "\\'", ''); //,'\\*','\\(','\\)','\\"'
    $string = str_replace($boolchars, $boolcharsesc, $string);
    return $string;
}

function highlight($high, $text) {
    $dummytag1 = '__s_c_h_start__';
    $dummytag2 = '__s_c_h_end__';
    foreach ($high as $hightext) {
        $hightext = preg_quote($hightext, '/');
        if ($hightext != '') {
            $text = preg_replace("/\\b($hightext)\\b/i", $dummytag1 . '$1' . $dummytag2, $text);
        }
    }
    $text = htmlspecialchars($text);
    $text = str_replace($dummytag1, '<span class="highlighter">', $text);
    $text = str_replace($dummytag2, '</span>', $text);
    return $text;
}

function unBool($searchKey) {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    $searchKey = preg_replace('/[\\(\\)\\*"]+/i', '', $searchKey);
    $searchKey = trim($DRW->real_escape_string($searchKey));
    $searchKey = preg_replace('/\\s+(or|and)\\s+/i', ' ', $searchKey);
    $searchKey = preg_replace('/\\s*not\\s+/i', ' ', $searchKey);
    return $searchKey;
}

function isMysqlBool($searchKey) {
    if (preg_match('/\\s+(or|and)\\s+/i', $searchKey) || preg_match('/\\s*not\\s+/i', $searchKey) || preg_match('/[\\(\\)\\*"]+/i', $searchKey)) {
        return true;
    }
    return false;
}

function parseBool($searchKey) {
    $searchKey = preg_replace('/\'+/', ' ', $searchKey);
    $searchKey = trim(escapeBool($searchKey));
    $keyArray = preg_split('/\\s+/', $searchKey, -1, PREG_SPLIT_NO_EMPTY);
    $splitArray = array();
    $spliti = -1;
    $inquote = false;
    foreach ($keyArray as $value) {
        if ($inquote) {
            $quotpos = strpos($value, '"');
            if ($quotpos !== false) {
                $splitArray[$spliti] .= ' ' . substr($value, 0, $quotpos + 1);
                $value = substr($value, $quotpos + 1);
                $inquote = false;
            } else {
                $splitArray[$spliti] .= ' ' . $value;
                continue;
            }
        }
        $oneword = false;
        $quotpos = strpos($value, '"');
        if ($quotpos !== false) {
            $quotpos2 = strpos($value, '"', $quotpos + 1);
            if ($quotpos2 !== false) {
                $oneword = true;
            }
            $newquote = substr($value, $quotpos);
            $value = substr($value, 0, $quotpos);
        } else {
            $newquote = '';
        }

        if (strtolower($value) != 'or') {
            if (!$inquote) {
                $value = preg_replace('/(\\(|\\))/', ' $1 ', $value);
                $tmpArray = preg_split('/\\s+/', $value, -1, PREG_SPLIT_NO_EMPTY);
            } else {
                $tmpArray = array($value);
            }
            foreach ($tmpArray as $v) {
                $spliti++;
                $splitArray[$spliti] = $v;
            }
        }
        if ($newquote != '') {
            $spliti++;
            $splitArray[$spliti] = $newquote;
            if ($oneword) {
                $inquote = false;
            } else {
                $inquote = true;
            }
        }
    }
    $keyArray = $splitArray;
    $count = count($keyArray);
    for ($i = 0; $i < $count; $i++) {
        $low = strtolower($keyArray[$i]);
        if ($low == 'and') {
            if (isset($keyArray[($i - 1)])) {
                $char = strtolower(substr($keyArray[($i - 1)], 0, 1));
                if ($char == ')') {
                    $n = $i - 2;
                    $skip = 0;
                    while ($n >= 0) {
                        if (isset($keyArray[$n])) {
                            if ($keyArray[$n] == '(') {
                                if ($skip > 0) {
                                    $skip--;
                                } else {
                                    if ($keyArray[$n] == '(') {
                                        $keyArray[$n] = '+(';
                                    }
                                    break;
                                }
                            } elseif ($keyArray[$n] == ')') {
                                $skip++;
                            }
                        }
                        $n--;
                    }
                } elseif ($char != '-' && $char != '(' && $char != '+') {
                    $keyArray[($i - 1)] = '+' . $keyArray[($i - 1)];
                }
            }
            if (isset($keyArray[($i + 1)])) {
                $chars = strtolower($keyArray[($i + 1)]);
                $char = substr($chars, 0, 1);
                if ($chars != 'not' && $char != ')' && $char != '+') {
                    $keyArray[($i + 1)] = '+' . $keyArray[($i + 1)];
                }
            }
            unset($keyArray[$i]);
        } elseif ($low == 'not' && isset($keyArray[($i + 1)])) {
            $char = strtolower(substr($keyArray[($i + 1)], 0, 1));
            if ($char != '-' && $char != '+') {
                $keyArray[($i + 1)] = '-' . $keyArray[($i + 1)];
            }
            unset($keyArray[$i]);
        }
    }

    $boolsearchKey = implode(' ', $keyArray);
    return $boolsearchKey;
}

function doMultCompany($searchtext, $companytable = false, $tablename = 'company') {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    $and_or = 'OR';
    $keyArray = preg_split('/"\\s+or\\s+"/i', $searchtext, -1, PREG_SPLIT_NO_EMPTY);
    if (count($keyArray) == 1) {
        $keyArray = preg_split('/"\\s+and\\s+"/i', $searchtext, -1, PREG_SPLIT_NO_EMPTY);
        $and_or = 'AND';
    }
    $out = '';
    $count = count($keyArray);
    if ($count > 0) {
        $out .= '(';
        foreach ($keyArray as $val) {
            $val = trim($val);
            if ($count > 1 || preg_match('/^"([^"]+)"$/', $val)) {
                if (preg_match('/^"([^"]+)"$/', $val, $match) || preg_match('/^([^"]+)"$/', $val, $match) || preg_match('/^"([^"]+)$/', $val, $match)) {
                    $val = $match[1];
                }
                $val = $DRW->real_escape_string($val);
                if ($companytable) {
                    $out .= "({$tablename}Name='$val') OR "; //only OR for now
                } else {
                    $out .= "(company='$val' OR (secondCompany like '%{$val}%' AND secondCompany REGEXP '[[:<:]]{$val}[[:>:]]')) $and_or ";
                }
            } else {
                $val = mysqlLike($val);
                if ($companytable) {
                    $out .= "({$tablename}Name LIKE '%$val%') OR "; //only OR for now
                } else {
                    $out .= "(company LIKE '%$val%' OR secondCompany LIKE '%$val%') $and_or ";
                }
            }
        }
        $out = substr($out, 0, -4);
        $out .= ')';
    }
    return $out;
}

function doQuery($search_id, $docount = false, $addedtodatabaseover = '', $dograph = false, $bid = -1, $relev = false, $expans = false, $unapproved = false, $clear_ps = false, $search_panelist_date_over = -1, $search_values = array(), $sess_userID = 0) {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm, $SPHINX_name;
    $ocrtext = '';
    $where = '';
    $sortby = '';
    $pjoin = '';
    $matchtext = '';
    $ojoin = '';
    $owhere = '';
    $awhere = '';
    $dmajoin = '';
    $edcjoin = '';
    $cjoin = '';
    $ccjoin = '';
    $afjoin = '';
    $affjoin = '';
    $sjoin = '';
    $sect_j = '';
    $pcjoin = '';
    $bjoin = '';
    $ejoin = '';
    $mljoin = '';
    $ajoin = '';
    $rjoin = '';
    $tljoin = '';
    $filter_range = array();
    if (!empty($sess_userID)) {
        $_SESSION = array();
        $_SESSION['sess_mchannel'] = array();
        $result4 = $DRW->query("SELECT mu.mChannelID FROM cscan_mc_users_allow mu,cscan_mchannel mc WHERE userID=$sess_userID AND mu.mChannelID=mc.mChannelID", $DRW_read);
        while ($data4 = $DRW->fetch_row($result4)) {
            $_SESSION['sess_mchannel'][] = $data4[0];
        }
        @$DRW->free_result($result4);
        $_SESSION['sess_mpanel'] = array();
        $result4 = $DRW->query("SELECT mu.mPanelID FROM cscan_mp_users_allow mu,cscan_mpanel mc WHERE userID=$sess_userID AND mu.mPanelID=mc.mPanelID", $DRW_read);
        while ($data4 = $DRW->fetch_row($result4)) {
            $_SESSION['sess_mpanel'][] = $data4[0];
        }
        @$DRW->free_result($result4);
        $_SESSION['sess_sector'] = array();
        $_SESSION['sess_category'] = array();
        $_SESSION['sess_subcategory'] = array();
        $result2 = $DRW->query("SELECT su.sectorID,parentID FROM cscan_sector_users_allow su,cscan_sector cs WHERE userID=$sess_userID AND su.sectorID=cs.sectorID", $DRW_read);
        while ($data2 = $DRW->fetch_row($result2)) {
            if ($data2[1] == 0) {
                $_SESSION['sess_sector'][] = $data2[0];
            } else {
                $result3 = $DRW->query("SELECT parentID FROM cscan_sector WHERE sectorID=$data2[1]", $DRW_read);
                $data3 = $DRW->fetch_row($result3);
                if ($data3[0] == 0) {
                    $_SESSION['sess_category'][] = $data2[0];
                } else {
                    $_SESSION['sess_subcategory'][] = $data2[0];
                }
                @$DRW->free_result($result3);
            }
        }
        @$DRW->free_result($result2);
        $_SESSION['sess_userID'] = $sess_userID;
        $_SESSION['sess_search_exclude'] = array();
        $result2 = $DRW->query("SELECT search_field FROM cscan_search_exclude WHERE userID=$sess_userID", $DRW_read);
        while ($data2 = $DRW->fetch_row($result2)) {
            $_SESSION['sess_search_exclude'][] = $data2[0];
        }
        @$DRW->free_result($result2);
    } elseif (!isset($_SESSION)) {
        $_SESSION = array();
        $_SESSION['sess_mchannel'] = array();
        $_SESSION['sess_mpanel'] = array();
        $_SESSION['sess_sector'] = array();
        $_SESSION['sess_category'] = array();
        $_SESSION['sess_subcategory'] = array();
        $_SESSION['sess_userID'] = 0;
        $_SESSION['sess_search_exclude'] = array();
    }
    if (is_bool($dograph)) {
        $dograph = (int) $dograph;
    }
    if ($bid < 0) {
        if (count($search_values) > 0) {
            $searchKey = '';
            $searchType = 'ocr2';
            $searchOption = 'boolean';
            $mChannelID = '';
            $sectorID = '';
            $mPanelID = '';
            $addedToDatabase = '';
            $month1 = '';
            $month2 = '';
            $sort = 'desc';
            $company = '';
            $productName = '';
            $incentive = '';
            $categoryID = '';
            $mTypeID = '';
            $subCategoryID = '';
            $cardStatus = '';
            $personalization = '';
            $gender = '';
            $age = '';
            $state = '';
            $worksiteVoluntary = 0;
            $agentCommunicationID = '';
            $groupSize = '';
            $offerOrigin = '';
            $enhance = 1;
            $saved = 0;
            $compaignLanguage = '';
            $affinityAssociation = '';
            $income_mult = '';
            $fa_id_mult = '';
            $tl_id_mult = '';
            $siteCatID_mult = '';
            $pubTypeID_mult = '';
            $approved_date = '0000-00-00 00:00:00';
            $approved_date_to = '0000-00-00 00:00:00';
            $electronicID_mult = '';
            $DMA_ID_mult = '';
            $businessContent_mult = '';
            $delmethid_mult = '';
            $affinity_association = '';
            $prescription = 0;
            $AffinityCategoryID_mult = '';
            $search_panelist_date = 0;
            $is_affinion = 0;
            $is_military = 0;
            $search_competi_id = '';
            $ApplicationType_mult = '';
            $is_multicultural = 0;
            $search_rules = '';
            $IntroPricing_mult = '';
            $is_rewards = 0;
            $RewardsProgramEmphasis_mult = '';
            $is_incentive = 0;
            $responseMechID_mult = '';
            $multiculturalmarkets_mult = '';
            $CardNetwork_mult = '';
            $FeeProduct = 0;
            $external_link = '';
            $FeeProductType = '';
            $ca_related = 0;
            $is_mover = 0;
            $scsc_primary = 0;
            $OptOutFirmOffer = 0;
            $searchKey2 = '';
            $search_type_and = 0;
            $riders_mult = '';
            $is_hphsa = 0;
            $subSubCategoryID = '';
            $Income_Producing_Assets_Segment_Code = '';
            $cg_id = '';
            $is_citi = 0;
            $is_CreditCardMentioned = 0;
            $spanelist_filter = '';
            $edc_id_mult = '';
            $AffinitySubCategoryID_mult = '';
            $ERateType_mult = '';
            $EOfferPrice_mult = '';
            $ETermLength_mult = '';
            $is_ECancelFee = 0;
            $IssueTypeID_mult = '';
            $pcountry = '';
            $is_Reloadable = 0;
            $creditUnion = 0;
            foreach ($search_values as $var => $val) {
                $$var = $val;
            }
        } else {
            $savedQ = "SELECT searchKey,searchType,searchOption,mChannelID,sectorID,mPanelID,
                        addedToDatabase,month1,month2,sort,company,productName,incentive,categoryID,mTypeID,subCategoryID,cardStatus,personalization,
                        gender,age,state,worksiteVoluntary,agentCommunicationID,groupSize,offerOrigin,enhance,saved,compaignLanguage,affinityAssociation,income_mult,fa_id_mult,tl_id_mult,
                        siteCatID_mult,pubTypeID_mult,approved_date,electronicID_mult,DMA_ID_mult,businessContent_mult,delmethid_mult,affinity_association,prescription,AffinityCategoryID_mult,
                        search_panelist_date,is_affinion,is_military,search_competi_id,ApplicationType_mult,is_multicultural,search_rules,IntroPricing_mult,is_rewards,RewardsProgramEmphasis_mult,is_incentive,responseMechID_mult,
                        multiculturalmarkets_mult,CardNetwork_mult,FeeProduct,external_link,FeeProductType,approved_date_to,ca_related,is_mover,scsc_primary,OptOutFirmOffer,searchKey2,search_type_and,riders_mult,is_hphsa,subSubCategoryID,Income_Producing_Assets_Segment_Code_mult,cg_id,is_citi,is_CreditCardMentioned,
                        spanelist_filter,edc_id_mult,AffinitySubCategoryID_mult,ERateType_mult,EOfferPrice_mult,ETermLength_mult,is_ECancelFee,IssueTypeID_mult,pcountry,is_Reloadable,creditUnion
                        FROM cscan_search WHERE ID='" . $search_id . "'";
            $rs = $DRW->query($savedQ, $DRW_read);
            $data = $DRW->fetch_row($rs);
            $searchKey = trim($data[0]);
            $searchType = $data[1];
            $searchOption = $data[2];
            $mChannelID = $data[3];
            $sectorID = $data[4];
            $mPanelID = $data[5];
            $addedToDatabase = $data[6];
            $month1 = $data[7];
            $month2 = $data[8];
            $sort = $data[9];
            $company = $data[10];
            $productName = $data[11];
            $incentive = $data[12];
            $categoryID = $data[13];
            $mTypeID = $data[14];
            $subCategoryID = $data[15];
            $cardStatus = $data[16];
            $personalization = $data[17];
            $gender = $data[18];
            $age = $data[19];
            $state = $data[20];
            $worksiteVoluntary = $data[21];
            $agentCommunicationID = $data[22];
            $groupSize = $data[23];
            $offerOrigin = $data[24];
            $enhance = $data[25];
            $saved = $data[26];
            $compaignLanguage = $data[27];
            $affinityAssociation = $data[28];
            $income_mult = $data[29];
            $fa_id_mult = $data[30];
            $tl_id_mult = $data[31];
            $siteCatID_mult = $data[32];
            $pubTypeID_mult = $data[33];
            $approved_date = $data[34];
            $electronicID_mult = $data[35];
            $DMA_ID_mult = $data[36];
            $businessContent_mult = $data[37];
            $delmethid_mult = $data[38];
            $affinity_association = $data[39];
            $prescription = $data[40];
            $AffinityCategoryID_mult = $data[41];
            $search_panelist_date = $data[42];
            $is_affinion = $data[43];
            $is_military = $data[44];
            $search_competi_id = $data[45];
            $ApplicationType_mult = $data[46];
            $is_multicultural = $data[47];
            $search_rules = $data[48];
            $IntroPricing_mult = $data[49];
            $is_rewards = $data[50];
            $RewardsProgramEmphasis_mult = $data[51];
            $is_incentive = $data[52];
            $responseMechID_mult = $data[53];
            $multiculturalmarkets_mult = $data[54];
            $CardNetwork_mult = $data[55];
            $FeeProduct = $data[56];
            $external_link = $data[57];
            $FeeProductType = $data[58];
            $approved_date_to = $data[59];
            $ca_related = $data[60];
            $is_mover = $data[61];
            $scsc_primary = $data[62];
            $OptOutFirmOffer = $data[63];
            $searchKey2 = $data[64];
            $search_type_and = $data[65];
            $riders_mult = $data[66];
            $is_hphsa = $data[67];
            $subSubCategoryID = $data[68];
            $Income_Producing_Assets_Segment_Code = $data[69];
            $cg_id = $data[70];
            $is_citi = $data[71];
            $is_CreditCardMentioned = $data[72];
            $spanelist_filter = $data[73];
            $edc_id_mult = $data[74];
            $AffinitySubCategoryID_mult = $data[75];
            $ERateType_mult = $data[76];
            $EOfferPrice_mult = $data[77];
            $ETermLength_mult = $data[78];
            $is_ECancelFee = $data[79];
            $IssueTypeID_mult = $data[80];
            $pcountry = $data[81];
            $is_Reloadable = $data[82];
            $creditUnion = $data[83];
        }
        if (!empty($AffinitySubCategoryID_mult)) {
            $AffinityCategoryID_mult = $AffinitySubCategoryID_mult;
        }
        if ($search_panelist_date_over != -1) {
            $search_panelist_date = $search_panelist_date_over;
        }
        if ($approved_date == '0000-00-00' || $approved_date == '0000-00-00 00:00:00') {
            $approved_date = '';
        }
        if ($approved_date_to == '0000-00-00' || $approved_date_to == '0000-00-00 00:00:00') {
            $approved_date_to = '';
        }
        if (!empty($approved_date) && !empty($approved_date_to)) {
            $approved_date = "$approved_date,$approved_date_to";
        }
        if ($month1 != '' || $month2 != '') {
            $month = "$month1,$month2";
        } else {
            $month = '';
        }
        if (empty($mChannelID)) {
            $mChannelID = implode(',', $_SESSION['sess_mchannel']);
        }
        if (empty($mPanelID)) {
            $mPanelID = implode(',', $_SESSION['sess_mpanel']);
        }
        $isBiz = '';
        //business owner check when Business Owner is a Panel (6)
        /* $tmpArray = explode(',',$mPanelID);
          $tmpArray2 = array();
          foreach($tmpArray as $v){
          if($v==6) {
          $isBiz = 1;
          }
          else{
          $tmpArray2[] = $v;
          }
          }
          $mPanelID = implode(',',$tmpArray2); */

        if (empty($state) && in_array('canada', $_SESSION['sess_search_exclude'])) {
            $pcountry = 'US';
        } elseif (!empty($state)) {
            $pcountry = '';
        }
        if ($mPanelID == '1' || $mPanelID == '2' || $mPanelID == '1,2') {
            $consumer_only = true;
        } else {
            $consumer_only = false;
        }
        $ppdate = '';
        $ppdate_month = '';
        $ppstateID = '';
        $pgender = '';
        $is_panelist = 0;
        if (($search_panelist_date || $consumer_only) && $search_panelist_date_over != 0) {
            $ppdate = $addedToDatabase;
            $addedToDatabase = '';
            $ppdate_month = $month;
            $month = '';
            $ppstateID = $state;
            $state = '';
            $pgender = $gender;
            $gender = '';
            $is_panelist = 1;
        }

        //state relationship instead
        if ($ppstateID != '') {
            $rstate = $ppstateID;
        } else {
            $rstate = $state;
        }
        $ppstateID = '';
        $state = '';

        $andorArray = array();
        $andorArray['sectorID'] = 'OR';
        $exacterArray = array();
        $exactervalsArray = array();
        $noterArray = array();
        $search_rulesArray = explode(',', $search_rules);
        foreach ($search_rulesArray as $sr) {
            if (!empty($sr)) {
                list($f, $ao, $ex, $no) = explode(':', $sr);
                if ($ao) {
                    $andorArray[$f] = 'AND';
                }
                if ($ex) {
                    $exacterArray[] = $f;
                    $exactervalsArray[$f] = array();
                }
                if ($no) {
                    $noterArray[] = $f;
                }
            }
        }
        if ($spanelist_filter == '1') {
            $pjoin_filter = ' AND pp.pprimary=1';
            $pjoin_left = '';
        } elseif ($spanelist_filter == '2') {
            $pjoin_filter = ' AND pp.pprimary=0';
            $pjoin_left = '';
        } else {
            $pjoin_filter = '';
            $pjoin_left = ' LEFT';
        }
        $exactArray = array();
        $multExactArray = array('mChannelID' => $mChannelID, 'mPanelID' => $mPanelID);
        $likeArray = array('incentive' => $incentive);
        $multArray = array();
        $otherArray = array('company' => $company, 'affinity_association' => $affinity_association, 'addedToDatabase' => $addedToDatabase, 'month' => $month, 'approved_date' => $approved_date, 'search_competi_id' => $search_competi_id, 'ApplicationType_mult' => $ApplicationType_mult, 'cg_id' => $cg_id, 'rstate' => $rstate, 'pcountry' => $pcountry);
        $panelistArray = array('ppageID' => $age, 'pincomeID' => $income_mult, 'isBiz' => $isBiz, 'dmap.code' => $DMA_ID_mult, 'ppdate' => $ppdate, 'ppdate_month' => $ppdate_month, 'ppstateID' => $ppstateID, 'pgender' => $pgender, 'Income_Producing_Assets_Segment_Code' => $Income_Producing_Assets_Segment_Code, 'edc_id' => $edc_id_mult);
        if ($productName != '') {
            $keyArray = preg_split('/"\\s+or\\s+"/i', $productName, -1, PREG_SPLIT_NO_EMPTY);
            if (count($keyArray) > 1 || preg_match('/^"([^"]+)"$/', $productName)) {
                foreach ($keyArray as $k => $val) {
                    if (preg_match('/^"([^"]+)"$/', $val, $match) || preg_match('/^([^"]+)"$/', $val, $match) || preg_match('/^"([^"]+)$/', $val, $match)) {
                        $keyArray[$k] = $match[1];
                    }
                }
                $multExactArray['productName'] = $keyArray;
            } else {
                $likeArray['productName'] = $productName;
            }
        }
        if ($enhance) {
            $exactArray = array_merge($exactArray, array('cardStatus' => $cardStatus, 'personalization' => $personalization, 'gender' => $gender, 'offerOrigin' => $offerOrigin, 'compaignLanguage' => $compaignLanguage));
            $multExactArray = array_merge($multExactArray, array('mTypeID' => $mTypeID, 'delmethid' => $delmethid_mult));
            $likeArray = array_merge($likeArray, array('external_link' => $external_link));
            $multArray = array_merge($multArray, array('state' => $state, 'agentCommunicationID' => $agentCommunicationID, 'groupSize' => $groupSize, 'fa_ids' => $fa_id_mult, 'tl_ids' => $tl_id_mult, 'electronicID' => $electronicID_mult, 'businessContent' => $businessContent_mult, 'multiculturalmarkets' => $multiculturalmarkets_mult, 'responseMechID' => $responseMechID_mult, 'FeeProductType' => $FeeProductType, 'riders' => $riders_mult, 'IssueTypeID' => $IssueTypeID_mult));
            $otherArray = array_merge($otherArray, array('AffinityCategoryID' => $AffinityCategoryID_mult, 'worksiteVoluntary' => $worksiteVoluntary, 'affinityAssociation' => $affinityAssociation, 'siteCatID' => $siteCatID_mult, 'pubTypeID' => $pubTypeID_mult, 'prescription' => $prescription, 'is_affinion' => $is_affinion, 'is_military' => $is_military, 'is_multicultural' => $is_multicultural, 'IntroPricing_mult' => $IntroPricing_mult, 'is_rewards' => $is_rewards, 'RewardsProgramEmphasis_mult' => $RewardsProgramEmphasis_mult, 'is_incentive' => $is_incentive, 'CardNetwork_mult' => $CardNetwork_mult, 'FeeProduct' => $FeeProduct, 'is_mover' => $is_mover, 'OptOutFirmOffer' => $OptOutFirmOffer, 'is_hphsa' => $is_hphsa, 'is_citi' => $is_citi, 'is_CreditCardMentioned' => $is_CreditCardMentioned, 'ERateType' => $ERateType_mult, 'EOfferPrice' => $EOfferPrice_mult, 'ETermLength' => $ETermLength_mult, 'ECancelFee' => $is_ECancelFee, 'Reloadable' => $is_Reloadable, 'isCreditUnion' => $creditUnion));
        }


        foreach ($exactArray as $field => $val) {
            if ($val != '') {
                $where .= " $field";
                if (in_array($field, $noterArray)) {
                    $where .= '<>';
                } else {
                    $where .= '=';
                }
                $where .= "'" . $DRW->real_escape_string($val) . "' AND ";
            }
        }
        foreach ($multExactArray as $field => $val) {
            if ($val != '') {
                $tmpwhere = '';
                if (is_array($val)) {
                    $tmpArray = $val;
                } else {
                    $tmpArray = explode(',', $val);
                }
                foreach ($tmpArray as $v) {
                    if ($v != '') {
                        $tmpwhere .= " $field";
                        if (in_array($field, $noterArray)) {
                            $tmpwhere .= '<>';
                        } else {
                            $tmpwhere .= '=';
                        }
                        $tmpwhere .= "'" . $DRW->real_escape_string($v) . "'";
                        if (in_array($field, $noterArray)) {
                            $tmpwhere .= ' AND ';
                        } else {
                            $tmpwhere .= ' OR ';
                        }
                    }
                }
                if ($field == 'mPanelID') {
                    $awhere .= $tmpwhere;
                } else {
                    $where .= " (" . substr($tmpwhere, 0, -4) . ") AND ";
                }
            }
        }
        foreach ($likeArray as $field => $val) {
            if ($val != '') {
                $val = mysqlLike($val);
                $where .= " $field ";
                if (in_array($field, $noterArray)) {
                    $where .= 'NOT ';
                }
                $where .= "LIKE '%$val%' AND ";
            }
        }
        foreach ($multArray as $field => $val) {
            if ($val != '') {
                $tmpArray = explode(',', $val);
                $where .= " (";
                foreach ($tmpArray as $v) {
                    if ($v != '') {
                        $where .= " ($field ";
                        if (in_array($field, $noterArray)) {
                            $where .= 'NOT ';
                        } else {
                            $where .= "LIKE '%{$v}%' AND $field ";
                        }
                        $where .= "REGEXP '[[:<:]]{$v}[[:>:]]')";
                        if (in_array($field, $noterArray)) {
                            $where .= ' AND ';
                        } else {
                            $where .= ' OR ';
                        }
                    }
                }
                $where = substr($where, 0, -4);
                $where .= ") AND ";
            }
        }
        $sect_j .= ' JOIN cscan_scsc_product as scsc ON (pd.productID=scsc.productID)';
        $partsArray = array();
        $seccatsubArray = get_seccatsub(implode(',', $_SESSION['sess_sector']), implode(',', $_SESSION['sess_category']), implode(',', $_SESSION['sess_subcategory']));


        foreach ($seccatsubArray as $sid => $cArray) {
            $part1 = "scsc.scsc_sectorID=$sid";
            $partsArray[] = '(' . $part1 . ' AND scsc.scsc_categoryID=0 AND scsc.scsc_subCategoryID=0)';
            foreach ($cArray as $cid => $scArray) {
                $part2 = "scsc.scsc_categoryID=$cid";
                $partsArray[] = '(' . $part1 . ' AND ' . $part2 . ' AND scsc.scsc_subCategoryID=0)';
                foreach ($scArray as $scid => $a) {
                    $part3 = "scsc.scsc_subCategoryID=$scid";
                    $partsArray[] = '(' . $part1 . ' AND ' . $part2 . ' AND ' . $part3 . ')';
                }
            }
        }
        if (count($partsArray) > 0) {
            $where .= '(' . implode(' OR ', $partsArray) . ') AND ';
        }
        $partsArray2 = array();
        $j = 0;
        if ($andorArray['sectorID'] == 'AND') {
            $alias = "scsc$j";
        } else {
            $alias = "scsc";
        }
        $seccatsubArray = get_seccatsub($sectorID, $categoryID, $subCategoryID, $subSubCategoryID);

        foreach ($seccatsubArray as $sid => $cArray) {
            $part1 = $alias . ".scsc_sectorID=$sid";
            if ($scsc_primary) {
                $part1 .= ' AND ' . $alias . ".scsc_sort=1";
            }
            if (in_array('sectorID', $exacterArray)) {
                $exactervalsArray['sectorID'][] = $sid;
            }
            if (count($cArray) == 0) {
                $partsArray2[] = '(' . $part1 . ')';
                if ($andorArray['sectorID'] == 'AND') {
                    $sect_j .= ' JOIN cscan_scsc_product as ' . $alias . ' ON (pd.productID=' . $alias . '.productID)';
                    $j++;
                    $alias = "scsc$j";
                }
            } else {
                foreach ($cArray as $cid => $scArray) {
                    $part2 = $alias . ".scsc_categoryID=$cid";
                    if (in_array('sectorID', $exacterArray)) {
                        $exactervalsArray['categoryID'][] = $cid;
                    }
                    if (count($scArray) == 0) {
                        $partsArray2[] = '(' . $part1 . ' AND ' . $part2 . ')';
                        if ($andorArray['sectorID'] == 'AND') {
                            $sect_j .= ' JOIN cscan_scsc_product as ' . $alias . ' ON (pd.productID=' . $alias . '.productID)';
                            $j++;
                            $alias = "scsc$j";
                        }
                    } else {
                        foreach ($scArray as $scid => $sscArray) {
                            $part3 = $alias . ".scsc_subCategoryID=$scid";
                            if (in_array('sectorID', $exacterArray)) {
                                $exactervalsArray['subCategoryID'][] = $scid;
                            }
                            if (count($sscArray) == 0) {
                                $partsArray2[] = '(' . $part1 . ' AND ' . $part2 . ' AND ' . $part3 . ')';
                                if ($andorArray['sectorID'] == 'AND') {
                                    $sect_j .= ' JOIN cscan_scsc_product as ' . $alias . ' ON (pd.productID=' . $alias . '.productID)';
                                    $j++;
                                    $alias = "scsc$j";
                                }
                            } else {
                                foreach ($sscArray as $sscid => $ssscArray) {
                                    $part4 = $alias . ".scsc_subSubCategoryID=$sscid";
                                    if (in_array('sectorID', $exacterArray)) {
                                        $exactervalsArray['subSubCategoryID'][] = $sscid;
                                    }
                                    $partsArray2[] = '(' . $part1 . ' AND ' . $part2 . ' AND ' . $part3 . ' AND ' . $part4 . ')';
                                    if ($andorArray['sectorID'] == 'AND') {
                                        $sect_j .= ' JOIN cscan_scsc_product as ' . $alias . ' ON (pd.productID=' . $alias . '.productID)';
                                        $j++;
                                        $alias = "scsc$j";
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        if (count($partsArray2) > 0) {
            $where .= '(' . implode(' ' . $andorArray['sectorID'] . ' ', $partsArray2) . ') AND ';
        }
        foreach ($exactervalsArray as $f => $a) {
            $combos = array();
            if (count($a) <= 6) {
                pc_permute($a, $combos);
            }
            $wherec = '';
            foreach ($combos as $c) {
                $v = implode(',', $c);
                $wherec .= " $f='$v' OR ";
            }
            if ($wherec != '') {
                $where .= '(' . substr($wherec, 0, -4) . ') AND ';
            }
        }
        // print_r($otherArray);exit;
        foreach ($otherArray as $field => $val) {
            if ($val != '') {

                if ($field == 'addedToDatabase' && $addedtodatabaseover == '') {
                    if ($val == 'week') {
                        $where .= ' addedToDatabase>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 7 DAY),\' 00:00:00\') AND ';
                        $filter_range[] = array('dts_date', strtotime('-7 days', strtotime(date('Y-m-d') . ' 00:00:00')), time());
                    } elseif ($val == '2week') {
                        $where .= ' addedToDatabase>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 14 DAY),\' 00:00:00\') AND ';
                        $filter_range[] = array('dts_date', strtotime('-14 days', strtotime(date('Y-m-d') . ' 00:00:00')), time());
                    } elseif ($val == '1month') {
                        $where .= ' addedToDatabase>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 1 MONTH),\' 00:00:00\') AND ';
                        $filter_range[] = array('dts_date', strtotime('-1 month', strtotime(date('Y-m-d') . ' 00:00:00')), time());
                    } elseif ($val == '3month') {
                        $where .= ' addedToDatabase>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 3 MONTH),\' 00:00:00\') AND ';
                        $filter_range[] = array('dts_date', strtotime('-3 months', strtotime(date('Y-m-d') . ' 00:00:00')), time());
                    } elseif ($val == '6month') {
                        $where .= ' addedToDatabase>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 6 MONTH),\' 00:00:00\') AND ';
                        $filter_range[] = array('dts_date', strtotime('-6 months', strtotime(date('Y-m-d') . ' 00:00:00')), time());
                    } elseif ($val == '1year') {
                        $where .= ' addedToDatabase>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 1 YEAR),\' 00:00:00\') AND ';
                        $filter_range[] = array('dts_date', strtotime('-1 year', strtotime(date('Y-m-d') . ' 00:00:00')), time());
                    }
                } elseif ($field == 'month' && $addedtodatabaseover == '') {
                    $monthArray = explode(',', $val);
                    $month_1 = $monthArray[0];
                    $month_2 = $monthArray[1];
                    if ($month_1 == '') {
                        $month_1 = $month_2;
                    } elseif ($month_2 == '') {
                        $month_2 = $month_1;
                    }
                    $where .= " (addedToDatabase BETWEEN '$month_1-01 00:00:00' AND '$month_2-31 23:59:59') AND ";
                    $filter_range[] = array('dts_date', strtotime("$month_1-01 00:00:00"), strtotime("$month_2-31 23:59:59"));
                } elseif ($field == 'worksiteVoluntary' || $field == 'affinityAssociation' || $field == 'prescription' || $field == 'is_affinion' || $field == 'is_military' || $field == 'FeeProduct' || $field == 'is_mover' || $field == 'OptOutFirmOffer' || $field == 'is_hphsa' || $field == 'is_citi' || $field == 'ECancelFee' || $field == 'Reloadable' || $field == 'isCreditUnion') {
                    //echo"hhhhhh";

                    if (!empty($val)) {
                        if ($val == 1) {
                            $fieldval = 1;
                        } elseif ($val == 2) {
                            $fieldval = 0;
                        }
                        if ($field == 'OptOutFirmOffer') {
                            /* $sectorIDArray = explode(',',$sectorID);
                              if(!in_array(4,$sectorIDArray) && (in_array(90,$sectorIDArray) || in_array(6,$sectorIDArray))){
                              $tmp = '';
                              $in = false;
                              if(in_array(90,$sectorIDArray)){
                              $in = true;
                              $tmp .= "cscan_payment_cards.OptOutFirmOffer=$fieldval";
                              $pcjoin = ' JOIN cscan_payment_cards ON (pd.productID=cscan_payment_cards.productID)';
                              }
                              if(in_array(6,$sectorIDArray)){
                              if($in){
                              $tmp .= ' OR ';
                              }
                              $tmp .= "cscan_mortgage_loan.MLOptOutFirmOffer=$fieldval";
                              $mljoin = ' JOIN cscan_mortgage_loan ON (pd.productID=cscan_mortgage_loan.productID)';
                              }
                              $where .= " ($tmp) AND ";
                              }
                              else{ */
                            $where .= " is_prescreen=$fieldval AND ";
                            //}
                        } else {
                            $where .= " $field=$fieldval AND ";
                        }
                        if ($field == 'ECancelFee') {
                            $ejoin = ' JOIN cscan_energy ON (pd.productID=cscan_energy.productID)';
                        } elseif ($field == 'Reloadable') {
                            $pcjoin = ' JOIN cscan_payment_cards ON (pd.productID=cscan_payment_cards.productID)';
                        } elseif ($field == 'isCreditUnion') {
                            $cjoin = " JOIN cscan_company_product ON (cscan_company_product.productID=pd.productID) ";
                            $ccjoin = " JOIN cscan_company ON (cscan_company_product.companyID=cscan_company.companyID) ";
                        }
                    }
                } elseif ($field == 'is_multicultural') {
                    if ($val == 1) {
                        $where .= " multiculturalmarkets<>'' AND ";
                    }
                } elseif ($field == 'is_incentive') {
                    if ($val == 1) {
                        $where .= " incentive<>'' AND ";
                    }
                } elseif ($field == 'is_rewards') {
                    if ($val == 1) {
                        $tmp = '';
                        $in = false;
                        $sectorIDArray = explode(',', $sectorID);
                        if (in_array(90, $sectorIDArray)) {
                            $in = true;
                            $tmp .= "cscan_payment_cards.RewardsProgram=1";
                            $pcjoin = ' JOIN cscan_payment_cards ON (pd.productID=cscan_payment_cards.productID)';
                        }
                        if (in_array(87, $sectorIDArray)) {
                            if ($in) {
                                $tmp .= ' OR ';
                            }
                            $tmp .= "cscan_banking.BankingRewardsProgram=1";
                            $bjoin = ' JOIN cscan_banking ON (pd.productID=cscan_banking.productID)';
                        }
                        $where .= " ($tmp) AND ";
                    }
                } elseif ($field == 'is_CreditCardMentioned') {
                    if ($val == 1) {
                        $tmp = '';
                        $in = false;
                        $sectorIDArray = explode(',', $sectorID);
                        if (in_array(219, $sectorIDArray)) {
                            $in = true;
                            $tmp .= "cscan_travel_leisure.TLCreditCardMentioned=1";
                            $tljoin = ' JOIN cscan_travel_leisure ON (pd.productID=cscan_travel_leisure.productID)';
                        }
                        if (in_array(266, $sectorIDArray) || in_array(559, $sectorIDArray) || in_array(560, $sectorIDArray)) {
                            if ($in) {
                                $tmp .= ' OR ';
                            }
                            $tmp .= "cscan_retail.RCreditCardMentioned=1";
                            $rjoin = ' JOIN cscan_retail ON (pd.productID=cscan_retail.productID)';
                        }
                        $where .= " ($tmp) AND ";
                    }
                } elseif ($field == 'siteCatID' || $field == 'pubTypeID' || $field == 'ApplicationType_mult') {
                    $field2 = '';
                    if ($field == 'siteCatID') {
                        $field = 'cscan_sites.sites_category_id';
                        $ojoin .= ',cscan_sites_product,cscan_sites';
                        $owhere .= ' AND pd.productID=cscan_sites_product.productID AND cscan_sites_product.sites_id=cscan_sites.sites_id';
                    } elseif ($field == 'pubTypeID') {
                        $field = 'cscan_publication.print_typeID';
                        $ojoin .= ',cscan_publication_product,cscan_publication';
                        $owhere .= ' AND pd.productID=cscan_publication_product.productID AND cscan_publication_product.publicationID=cscan_publication.publicationID';
                    } elseif ($field == 'ApplicationType_mult') {
                        $sectorIDArray = explode(',', $sectorID);
                        $in = false;
                        if (in_array(90, $sectorIDArray)) {
                            $in = true;
                            $field = 'cscan_payment_cards.ApplicationType';
                            $pcjoin = ' JOIN cscan_payment_cards ON (pd.productID=cscan_payment_cards.productID)';
                        }
                        if (in_array(6, $sectorIDArray)) {
                            if ($in) {
                                $field2 = 'cscan_mortgage_loan.MLApplicationType';
                            } else {
                                $field = 'cscan_mortgage_loan.MLApplicationType';
                            }
                            $mljoin = ' JOIN cscan_mortgage_loan ON (pd.productID=cscan_mortgage_loan.productID)';
                        }
                    }
                    $tmpArray = explode(',', $val);
                    $where .= " (";
                    foreach ($tmpArray as $v) {
                        if ($v != '') {
                            $where .= " $field='" . $DRW->real_escape_string($v) . "' OR ";
                            if (!empty($field2)) {
                                $where .= " $field2='" . $DRW->real_escape_string($v) . "' OR ";
                            }
                        }
                    }
                    $where = substr($where, 0, -4);
                    $where .= ") AND ";
                } elseif ($field == 'IntroPricing_mult') {
                    $tmpArray = explode(',', $val);
                    $where .= " (";
                    foreach ($tmpArray as $v) {
                        switch ($v) {
                            case 1:
                                $where .= " cscan_payment_cards.PurchaseIntroductoryAPR is not null OR ";
                                break;
                            case 2:
                                $where .= " cscan_payment_cards.PurchaseIntroductoryAPR is null OR ";
                                break;
                            case 3:
                                $where .= " cscan_payment_cards.BalanceTransferIntroductoryAPR is not null OR ";
                                break;
                            case 4:
                                $where .= " cscan_payment_cards.BalanceTransferIntroductoryAPR is null OR ";
                                break;
                        }
                    }
                    $where = substr($where, 0, -4);
                    $where .= ") AND ";
                    $pcjoin = ' JOIN cscan_payment_cards ON (pd.productID=cscan_payment_cards.productID)';
                } elseif ($field == 'approved_date') {
                    $splitArray = explode(',', $val);
                    $split_1 = $splitArray[0];
                    $split_2 = $splitArray[1];
                    if ($split_1 == '') {
                        $split_1 = $split_2;
                    } elseif ($split_2 == '') {
                        $split_2 = $split_1;
                    }
                    $where .= " ($field BETWEEN '$split_1' AND '$split_2') AND ";
                } elseif ($field == 'search_competi_id' || $field == 'cg_id') {
                    $panelist_ids = array();
                    if ($field == 'cg_id') {
                        $sqlc = "SELECT DISTINCT panelist_id FROM cscan_panelists_product_group WHERE cg_id IN (" . $val . ")";
                        $rsc = $DRW->query($sqlc, $DRW_read);
                        while ($rowc = $DRW->fetch_row($rsc)) {
                            $panelist_ids[] = $rowc[0];
                        }
                    } else {
                        $vs = explode(',', $val);
                        $competi_ids = array();
                        foreach ($vs as $v) {
                            $competi_ids[] = "'" . $DRW->real_escape_string(trim($v)) . "'";
                        }
                        $sqlc = "SELECT panelist_id FROM cscan_panelists WHERE competi_id IN (" . implode(',', $competi_ids) . ")";
                        $rsc = $DRW->query($sqlc, $DRW_read);
                        while ($rowc = $DRW->fetch_row($rsc)) {
                            $panelist_ids[] = $rowc[0];
                        }
                    }
                    if (count($panelist_ids) == 0) {
                        $panelist_ids[] = '-1';
                    }
                    $pjoin = ' JOIN cscan_panelists_product pp ON (pd.productID=pp.productID' . $pjoin_filter . ')';
                    $where .= " pp.panelist_id IN (" . implode(',', $panelist_ids) . ") AND ";
                } elseif ($field == 'CardNetwork_mult' || $field == 'RewardsProgramEmphasis_mult') {
                    $sectorIDArray = explode(',', $sectorID);
                    $in = false;
                    $field2 = '';
                    if ($field == 'CardNetwork_mult') {
                        if (in_array(90, $sectorIDArray)) {
                            $in = true;
                            $field = 'cscan_payment_cards.CardNetwork';
                            $pcjoin = ' JOIN cscan_payment_cards ON (pd.productID=cscan_payment_cards.productID)';
                        }
                        if (in_array(87, $sectorIDArray)) {
                            if ($in) {
                                $field2 = 'cscan_banking.BankingCardNetwork';
                            } else {
                                $field = 'cscan_banking.BankingCardNetwork';
                            }
                            $bjoin = ' JOIN cscan_banking ON (pd.productID=cscan_banking.productID)';
                        }
                    } elseif ($field == 'RewardsProgramEmphasis_mult') {
                        $in = false;
                        if (in_array(90, $sectorIDArray)) {
                            $in = true;
                            $field = 'cscan_payment_cards.RewardsProgramEmphasis';
                            $pcjoin = ' JOIN cscan_payment_cards ON (pd.productID=cscan_payment_cards.productID)';
                        }
                        if (in_array(87, $sectorIDArray)) {
                            if ($in) {
                                $field2 = 'cscan_banking.BankingRewardsProgramEmphasis';
                            } else {
                                $field = 'cscan_banking.BankingRewardsProgramEmphasis';
                            }
                            $bjoin = ' JOIN cscan_banking ON (pd.productID=cscan_banking.productID)';
                        }
                    }
                    $tmpArray = explode(',', $val);
                    $where .= " (";
                    foreach ($tmpArray as $v) {
                        if ($v != '') {
                            $where .= " ($field like '%{$v}%' AND $field REGEXP '[[:<:]]{$v}[[:>:]]') OR ";
                            if (!empty($field2)) {
                                $where .= " ($field2 like '%{$v}%' AND $field2 REGEXP '[[:<:]]{$v}[[:>:]]') OR ";
                            }
                        }
                    }
                    $where = substr($where, 0, -4);
                    $where .= ") AND ";
                } elseif ($field == 'ERateType' || $field == 'EOfferPrice' || $field == 'ETermLength') {
                    $ejoin = ' JOIN cscan_energy ON (pd.productID=cscan_energy.productID)';

                    $efields_array = array('', '2', '3', '4');
                    $tmpArray = explode(',', $val);
                    $where .= " (";
                    if ($field == 'EOfferPrice') {
                        if (empty($tmpArray[0])) {
                            unset($tmpArray[0]);
                        }
                        $sql = "SELECT OfferPriceMin,OfferPriceMax FROM cscan_offer_price WHERE OfferPriceID IN(" . implode(',', $tmpArray) . ") ORDER BY OfferPriceSort";
                        $rs = $DRW->query($sql, $DRW_read);
                        $types = array();
                        while ($op = $DRW->fetch_row($rs)) {
                            foreach ($efields_array as $field_concat) {
                                $where .= " ($field$field_concat>=" . $op[0] . " AND $field$field_concat<=" . $op[1] . ") OR ";
                            }
                        }
                    } else {
                        foreach ($tmpArray as $v) {
                            if ($v != '') {
                                foreach ($efields_array as $field_concat) {
                                    $where .= " $field$field_concat='" . $DRW->real_escape_string($v) . "' OR ";
                                }
                            }
                        }
                    }
                    $where = substr($where, 0, -4);
                    $where .= ") AND ";
                } elseif ($field == 'company' || $field == 'affinity_association') {
                    $cos = array();
                    if ($ca_related == 1) {
                        if ($field == 'affinity_association') {
                            $sqlc = "SELECT DISTINCT pp.panelist_id FROM cscan_affinity pa,cscan_panelist_affinity pp
                                WHERE pa.affinityID=pp.affinityID AND " . doMultCompany($val, true, 'affinity');
                        } else {
                            $sqlc = "SELECT DISTINCT pp.panelist_id FROM cscan_company pa,cscan_panelist_company pp
                                WHERE pa.companyID=pp.companyID AND " . doMultCompany($val, true, 'company');
                        }
                        $rsc = $DRW->query($sqlc, $DRW_read);
                        while ($rowc = $DRW->fetch_row($rsc)) {
                            $cos[] = $rowc[0];
                        }
                        if (count($cos) == 0) {
                            $cos[] = '0';
                        }
                        $pjoin = ' JOIN cscan_panelists_product pp ON (pd.productID=pp.productID' . $pjoin_filter . ')';
                        $where .= " pp.panelist_id IN (" . implode(',', $cos) . ") AND ";
                    } else {
                        if ($field == 'affinity_association') {
                            $caat = 'affinity';
                            $afjoin = " JOIN cscan_affinity_product ON (cscan_affinity_product.productID=pd.productID) ";
                        } else {
                            $caat = 'company';
                            $cjoin = " JOIN cscan_company_product ON (cscan_company_product.productID=pd.productID) ";
                        }
                        $sqlc = "SELECT {$caat}ID FROM cscan_$caat WHERE " . doMultCompany($val, true, $caat);
                        $rsc = $DRW->query($sqlc, $DRW_read);
                        while ($rowc = $DRW->fetch_row($rsc)) {
                            $cos[] = $rowc[0];
                        }
                        if (count($cos) == 0) {
                            $cos[] = '0';
                        }
                        $where .= " cscan_{$caat}_product.{$caat}ID IN (" . implode(',', $cos) . ") AND ";
                    }
                } elseif ($field == 'AffinityCategoryID') {
                    $cos = explode(',', $val);
                    if (empty($cos[0])) {
                        unset($cos[0]);
                    }
                    $afjoin = " JOIN cscan_affinity_product ON (cscan_affinity_product.productID=pd.productID) ";
                    $affjoin = " JOIN cscan_affinity ON (cscan_affinity.affinityID=cscan_affinity_product.affinityID) JOIN cscan_aff_cat ON (cscan_affinity.affinityID=cscan_aff_cat.affinityID) ";
                    $where .= " cscan_aff_cat.AffinityCategoryID IN (" . implode(',', $cos) . ") AND ";
                } elseif ($field == 'pcountry' || $field == 'rstate') {
                    if (empty($sjoin)) {
                        $sjoin = " JOIN cscan_product_detail_state ON (cscan_product_detail_state.productID=pd.productID)";
                    }
                    if ($field == 'pcountry') {
                        //$sjoin .= " JOIN cscan_state ON (cscan_product_detail_state.stateID=cscan_state.stateID) ";
                        //$where .= " (cscan_state.countryCode='".$DRW->real_escape_string($val)."' OR cscan_state.countryCode='') AND ";
                        $where .= " (cscan_product_detail_state.countryCode_copy='" . $DRW->real_escape_string($val) . "' OR cscan_product_detail_state.countryCode_copy='') AND ";
                    } else {
                        $cos = explode(',', $val);
                        if (empty($cos[0])) {
                            unset($cos[0]);
                        }
                        $where .= " cscan_product_detail_state.stateID IN (" . implode(',', $cos) . ") AND ";
                    }
                    if (empty($is_panelist)) {
                        $where .= " cscan_product_detail_state.is_panelist=0 AND ";
                    }
                }
            }
        }

        // echo $where  ;exit;
        foreach ($panelistArray as $field => $val) {
            if ($val != '') {
                $pjoin = $pjoin_left . ' JOIN cscan_panelists_product pp ON (pd.productID=pp.productID' . $pjoin_filter . ')';
                if ($field == 'Income_Producing_Assets_Segment_Code') {
                    $ajoin = " JOIN cscan_panelists_appends ON (cscan_panelists_appends.panelist_id=pp.panelist_id) ";
                } elseif ($field == 'edc_id') {
                    $edcjoin = ' JOIN cscan_edc_postalcode edc ON(pp.pppostalcode=edc.pppostalcode)';
                } elseif ($field == 'dmap.code') {
                    $dmajoin = ' JOIN cscan_dma_code_postalcode dmap ON (pp.pppostalcode=dmap.pppostalcode)';
                }
                /* changes for remove pdate condition
                  if ($field == 'ppdate') {
                  if ($val == 'week')
                  $where .= ' (pp.ppdate>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 7 DAY),\' 00:00:00\') OR addedToDatabase>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 7 DAY),\' 00:00:00\')) AND ';
                  elseif ($val == '2week')
                  $where .= ' (pp.ppdate>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 14 DAY),\' 00:00:00\') OR addedToDatabase>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 14 DAY),\' 00:00:00\')) AND ';
                  elseif ($val == '1month')
                  $where .= ' (pp.ppdate>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 1 MONTH),\' 00:00:00\') OR addedToDatabase>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 1 MONTH),\' 00:00:00\')) AND ';
                  elseif ($val == '3month')
                  $where .= ' (pp.ppdate>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 3 MONTH),\' 00:00:00\') OR addedToDatabase>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 3 MONTH),\' 00:00:00\')) AND ';
                  elseif ($val == '6month')
                  $where .= ' (pp.ppdate>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 6 MONTH),\' 00:00:00\') OR addedToDatabase>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 6 MONTH),\' 00:00:00\')) AND ';
                  elseif ($val == '1year')
                  $where .= ' (pp.ppdate>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 1 YEAR),\' 00:00:00\') OR addedToDatabase>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 1 YEAR),\' 00:00:00\')) AND ';
                  } */
                if ($field == 'ppdate') {
                    if ($val == 'week')
                        $where .= '  addedToDatabase>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 7 DAY),\' 00:00:00\') AND ';
                    elseif ($val == '2week')
                        $where .= '  addedToDatabase>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 14 DAY),\' 00:00:00\') AND ';
                    elseif ($val == '1month')
                        $where .= '  addedToDatabase>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 1 MONTH),\' 00:00:00\') AND ';
                    elseif ($val == '3month')
                        $where .= '  addedToDatabase>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 3 MONTH),\' 00:00:00\') AND ';
                    elseif ($val == '6month')
                        $where .= '  addedToDatabase>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 6 MONTH),\' 00:00:00\') AND ';
                    elseif ($val == '1year')
                        $where .= '  addedToDatabase>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 1 YEAR),\' 00:00:00\') AND ';
                }
                elseif ($field == 'ppdate_month') {
                    $monthArray = explode(',', $val);
                    $month_1 = $monthArray[0];
                    $month_2 = $monthArray[1];
                    if ($month_1 == '') {
                        $month_1 = $month_2;
                    } elseif ($month_2 == '') {
                        $month_2 = $month_1;
                    }

                    //changes for remove pdate condition
                    // $where .= " ((pp.ppdate BETWEEN '$month_1-01 00:00:00' AND '$month_2-31 23:59:59') OR (addedToDatabase BETWEEN '$month_1-01 00:00:00' AND '$month_2-31 23:59:59')) AND ";
                    $where .= " (addedToDatabase BETWEEN '$month_1-01 00:00:00' AND '$month_2-31 23:59:59') AND ";
                } elseif ($field == 'pgender') {
                    $where .= " (pp.pgender='$val' OR gender='$val') AND ";
                } elseif ($field == 'ppageID') {
                    $ageIds = explode(',', $val);
                    $where .= "(";

                    foreach ($ageIds as $id) {
                        $where .= "pp.ppageID LIKE '%$id%' ";

                        if ($id === end($ageIds)) {
                            $where .= ') AND ';
                        } else {
                            $where .= ' OR ';
                        }
                    }
                } else {
                    $tmpwhere = '';
                    $tmpArray = explode(',', $val);
                    foreach ($tmpArray as $v) {
                        if ($v != '') {
                            if ($field == 'Income_Producing_Assets_Segment_Code' || $field == 'dmap.code') {
                                $tmpwhere .= " $field='" . $v . "' OR ";
                            } elseif ($field == 'ppstateID') {
                                $tmpwhere .= " pp.ppstateID=" . (int) $v . " OR (state like '%{$v}%' AND state REGEXP '[[:<:]]{$v}[[:>:]]') OR ";
                            } else {
                                $tmpwhere .= " $field=" . (int) $v . " OR ";
                            }
                        }
                    }
                    if ($field == 'isBiz') {
                        $awhere .= $tmpwhere;
                    } else {
                        $where .= " (" . substr($tmpwhere, 0, -4) . ") AND ";
                    }
                }
            }
        }
        if ($awhere != '') {
            $where .= " (" . substr($awhere, 0, -4) . ") AND ";
        }
        if ($addedtodatabaseover != '') {
            $where .= " addedToDatabase>='$addedtodatabaseover' AND ";
            $filter_range[] = array('dts_date', strtotime($addedtodatabaseover), time());
        }
        $where .= " addedToDatabase<=NOW() AND (productStatus=1";
        if ($unapproved) {
            $where .= " OR productStatus=2";
        }
        $where .= ") AND ";
        if ($searchKey != '' || $searchKey2 != '') {
            if ($searchType == 'ocr') {
                //updateOCR_soundex();
                $matchagainst = 'MATCH(dts_val) AGAINST';
                //if(isMysqlBool($searchKey)){
                $matchtext = "$matchagainst ('" . parseBool($searchKey) . "' IN BOOLEAN MODE)";
                if ($relev) {
                    $ocrtext .= " RIGHT JOIN (SELECT productID FROM cscan_document_text_search WHERE $matchtext) AS t1 USING(productID)";
                    $matchtext = "$matchagainst ('" . unBool($searchKey) . "'";
                    if ($expans)
                        $matchtext .= ' WITH QUERY EXPANSION';
                    $matchtext .= ')';
                }
                else {
                    $where .= " $matchtext AND ";
                }
                //}
                //else{
                //    $matchtext = "$matchagainst ('".$DRW->real_escape_string($searchKey)."')";
                //    $where .= " $matchtext AND ";
                //}
                $ocrtext .= ' JOIN cscan_document_text_search dt ON(pd.productID=dt.productID)';
            } elseif ($searchType == 'ocr2' || $searchType == 'fulltext2' || $searchType == 'ocr_fulltext2') {
                if ($clear_ps) {
                    $DRW->query("DELETE FROM cscan_search_product WHERE ID=$search_id", $DRW_main);
                    $numrow = 0;
                } else {
                    $count_save_sql = "SELECT COUNT(*) FROM cscan_search_product WHERE ID=$search_id";
                    $rs = $DRW->query($count_save_sql, $DRW_read);
                    $data = $DRW->fetch_row($rs);
                    $numrow = (int) $data[0];
                }
                if ($numrow == 0 && !empty($SPHINX_name)) {
                    $searchi = 0;
                    $searches = array();
                    $searchkeys = array();
                    if ($searchType == 'ocr_fulltext2') {
                        if ($searchKey2 != '') {
                            $searches['fulltext2'] = '2';
                            $searchkeys['fulltext2'] = $searchKey2;
                        }
                        if ($searchKey != '') {
                            $searches['ocr2'] = '';
                            $searchkeys['ocr2'] = $searchKey;
                        }
                    } elseif ($searchKey != '') {
                        if ($searchType == 'fulltext2') {
                            $searches[$searchType] = '2';
                        } else {
                            $searches[$searchType] = '';
                        }
                        $searchkeys[$searchType] = $searchKey;
                    }
                    $searches_count = count($searches);
                    foreach ($searches as $st => $add) {
                        if ($search_type_and == 1 && $searches_count > 1) {
                            $searchi++;
                        }
                        $sk = $searchkeys[$st];
                        $s = startSphinx();
                        $inds = 'base_index_' . $SPHINX_name . $add . ',delta_index_' . $SPHINX_name . $add; //,base_index_'.$SPHINX_name.'stemmed'.$add.',delta_index_'.$SPHINX_name.'stemmed'.$add.'
                        if (strpos($sk, '*') !== false) {
                            $inds .= ',base_index_' . $SPHINX_name . 'star' . $add . ',delta_index_' . $SPHINX_name . 'star' . $add;
                        }
                        $ps = parseSphinx($s, $sk);
                        if (trim($ps) != '') {
                            $currcount = 0;
                            $step = $total = 1000;
                            if (!$s->setLimits(0, 1, 1)) {
                                sphinxErr(__LINE__, $s, 'setLimits');
                            }
                            foreach ($filter_range as $fr) {
                                if (!$s->setFilterRange($fr[0], $fr[1], $fr[2])) {
                                    sphinxErr(__LINE__, $s, 'setFilterRange', $fr[0] . ', ' . $fr[1] . ', ' . $fr[2]);
                                }
                            }
                            if (!$result = $s->query($ps, $inds)) {
                                sphinxErr(__LINE__, $s, 'query', $ps);
                            }
                            if (isset($result['matches'])) {
                                $total = (float) $result['total_found'];
                                $count = 0;
                                $minID = 0;
                                if ($add == '2') {
                                    $count_save_sql = "SELECT MAX(productID) FROM cscan_product_detail";
                                } else {
                                    $count_save_sql = "SELECT MAX(dts_id) FROM cscan_document_text_search";
                                }
                                $rs = $DRW->query($count_save_sql, $DRW_read);
                                $data = $DRW->fetch_row($rs);
                                $maxID = $data[0];
                                $DRW->query('START TRANSACTION', $DRW_main); //$DRW->connection($DRW_main); $DRW->begin_transaction();
                                for ($offset = 0; $offset <= $maxID; $offset+=$step) {
                                    $s = startSphinx();
                                    if (!$s->setLimits(0, $step, $step)) {
                                        sphinxErr(__LINE__, $s, 'setLimits');
                                    }
                                    foreach ($filter_range as $fr) {
                                        if (!$s->setFilterRange($fr[0], $fr[1], $fr[2])) {
                                            sphinxErr(__LINE__, $s, 'setFilterRange', $fr[0] . ', ' . $fr[1] . ', ' . $fr[2]);
                                        }
                                    }
                                    if ($minID < $maxID) {
                                        if (!$s->setIDRange($minID + 1, $maxID)) {
                                            sphinxErr(__LINE__, $s, 'setIDRange');
                                        }
                                    }
                                    if (!$result = $s->query($ps, $inds)) {
                                        sphinxErr(__LINE__, $s, 'query', $ps);
                                    }
                                    if (isset($result['matches'])) {
                                        foreach ($result['matches'] as $dts_id => $match) {
                                            if ($add == '2') {
                                                $productid = $dts_id;
                                            } else {
                                                $productid = $match['attrs']['productid'];
                                            }
                                            $query = "INSERT IGNORE INTO cscan_search_product (ID,productID,spID) VALUES ($search_id,$productid,$searchi)";
                                            $DRW->query($query, $DRW_main);
                                            $minID = $dts_id;
                                            $currcount++;
                                        }
                                        if ($currcount >= $total) {
                                            break;
                                        }
                                    }
                                    $err = $s->getLastError();
                                    $war = $s->getLastWarning();
                                    if (!empty($err) || !empty($war)) {
                                        //echo "$err | $war"; exit;
                                        break;
                                    }
                                    // note that total_found using setLimits(0,1,1) is not always the same as without limits (bug in Sphinx?)
                                    if (!isset($result['matches'])) {
                                        break;
                                    }
                                }
                                $DRW->query('COMMIT', $DRW_main); //$DRW->commit();
                            }
                        }
                    }
                    if ($search_type_and == 1 && $searches_count > 1) {
                        $sqlc = "SELECT productID,COUNT(*) AS cnt FROM cscan_search_product WHERE ID=$search_id GROUP BY productID HAVING cnt<>$searches_count";
                        $rsc = $DRW->query($sqlc, $DRW_read);
                        while ($rowc = $DRW->fetch_row($rsc)) {
                            $query = "DELETE FROM cscan_search_product WHERE ID=$search_id AND productID=$rowc[0]";
                            $DRW->query($query, $DRW_main);
                        }
                    }
                }
                $ocrtext .= ' JOIN cscan_search_product sp ON(sp.ID=' . $search_id . ' AND pd.productID=sp.productID)';
            } else {
                $matchagainst = 'MATCH(productHeadline,entryID) AGAINST'; //productName,company,secondCompany,productHeadline,entryID,incentive,searchText
                //if(isMysqlBool($searchKey)){
                $matchtext = "$matchagainst ('" . parseBool($searchKey) . "' IN BOOLEAN MODE)";
                if ($relev) {
                    $ocrtext .= " RIGHT JOIN (SELECT productID FROM cscan_product_detail WHERE $matchtext) AS t1 USING(productID)";
                    $matchtext = "$matchagainst ('" . unBool($searchKey) . "'";
                    if ($expans)
                        $matchtext .= ' WITH QUERY EXPANSION';
                    $matchtext .= ')';
                }
                else {
                    $where .= " $matchtext AND ";
                }
                //}
                //else{
                //    $matchtext = "$matchagainst ('".$DRW->real_escape_string($searchKey)."')";
                //    $where .= " $matchtext AND ";
                //}
            }
        }
        if ($where != '') {
            $where = ' WHERE ' . substr($where, 0, -5);
        }
    } else {
        $ojoin .= ',cscan_product_basket cb';
        $where = ' WHERE basket_id=' . $bid . ' AND userID=' . $_SESSION['sess_userID'] . ' AND cb.productID=pd.productID ';
        $saved = 1;
    }
    $selectQuery = "SELECT "; // SQL_NO_CACHE
    if ($docount) {
        $selectQuery .= "COUNT(DISTINCT pd.productID)";
        $sortby = '';
    } else {
        if ($dograph != 0) {
            $sortby = '';
        }

        $matchtext = ($matchtext != '') ? ",$matchtext AS relevancy" : ",1 AS relevancy";

        $mintel = new \HS\Mintel();
        $mintel_set = $mintel->getFields();
        $mintel_set_2 = $mintel->getFieldSet('incentive_set_2');
        $mintel_set_3 = $mintel->getFieldSet('incentive_set_3');
        $incentive_set = implode(',', array_merge(array_keys($mintel_set), array_keys($mintel_set_2), array_keys($mintel_set_3)));

        $selectQuery .= " DISTINCT pd.productID AS theproductID,mChannelID,mPanelID,productHeadline,sectorID,categoryID,subCategoryID,entryID,
            addedToDatabase,company,productName,compaignLanguage,firstSeen,lastSeen,mTypeID,state,agentCommunicationID,secondCompany,
            variantID,affinityAssociation,age,gender,incomeID,publication,isVariant,isDemographic,isInsight,fa_ids,tl_ids,isFICO,
            incentive_ongoing,incentive,$incentive_set,
            delmethid,responseMechID,FeeProductType,external_updates,external_fans,external_link,prescription,is_hphsa,subSubCategoryID,
            OfferExpiryDate,is_citi,riders,is_prescreen,isSurvey,IssueTypeID,traffic_sources,social_media_name,worksiteVoluntary,groupSize$matchtext";
    }

    $selectQuery .= " FROM cscan_product_detail pd$ocrtext$pjoin$cjoin$ccjoin$afjoin$affjoin$dmajoin$edcjoin$sect_j$pcjoin$bjoin$ejoin$mljoin$rjoin$tljoin$ajoin$sjoin$ojoin $where$owhere $sortby";
    //exit;
    if ($dograph != 0) {
        $field = getDoGraph($dograph);
        $selectQuery = "SELECT COUNT($field) AS field_count,$field AS field_name FROM ($selectQuery) as t1 GROUP BY $field"; // SQL_NO_CACHE ORDER BY field_count DESC,field_name ASC
    }


    return array($selectQuery, $saved);
}

//NEw QUERY EXCUTION
function doQueryNew($search_id, $docount = false, $addedtodatabaseover = '', $dograph = false, $bid = -1, $relev = false, $expans = false, $unapproved = false, $clear_ps = false, $search_panelist_date_over = -1, $search_values = array(), $sess_userID = 0, $orderby = false, $limit1 = 0, $limit2 = 300) {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm, $SPHINX_name;

    $ocrtext = '';
    $where = '';
    $sortby = '';
    $pjoin = '';
    $matchtext = '';
    $ojoin = '';
    $owhere = '';
    $awhere = '';
    $dmajoin = '';
    $edcjoin = '';
    $cjoin = '';
    $ccjoin = '';
    $afjoin = '';
    $affjoin = '';
    $sjoin = '';
    $sect_j = '';
    $pcjoin = '';
    $bjoin = '';
    $ejoin = '';
    $mljoin = '';
    $ajoin = '';
    $rjoin = '';
    $tljoin = '';
    $filter_range = array();
    if (!empty($sess_userID)) {
        $_SESSION = array();
        $_SESSION['sess_mchannel'] = array();
        $result4 = $DRW->query("SELECT mu.mChannelID FROM cscan_mc_users_allow mu,cscan_mchannel mc WHERE userID=$sess_userID AND mu.mChannelID=mc.mChannelID", $DRW_read);
        while ($data4 = $DRW->fetch_row($result4)) {
            $_SESSION['sess_mchannel'][] = $data4[0];
        }
        @$DRW->free_result($result4);
        $_SESSION['sess_mpanel'] = array();
        $result4 = $DRW->query("SELECT mu.mPanelID FROM cscan_mp_users_allow mu,cscan_mpanel mc WHERE userID=$sess_userID AND mu.mPanelID=mc.mPanelID", $DRW_read);
        while ($data4 = $DRW->fetch_row($result4)) {
            $_SESSION['sess_mpanel'][] = $data4[0];
        }
        @$DRW->free_result($result4);
        $_SESSION['sess_sector'] = array();
        $_SESSION['sess_category'] = array();
        $_SESSION['sess_subcategory'] = array();
        $result2 = $DRW->query("SELECT su.sectorID,parentID FROM cscan_sector_users_allow su,cscan_sector cs WHERE userID=$sess_userID AND su.sectorID=cs.sectorID", $DRW_read);
        while ($data2 = $DRW->fetch_row($result2)) {
            if ($data2[1] == 0) {
                $_SESSION['sess_sector'][] = $data2[0];
            } else {
                $result3 = $DRW->query("SELECT parentID FROM cscan_sector WHERE sectorID=$data2[1]", $DRW_read);
                $data3 = $DRW->fetch_row($result3);
                if ($data3[0] == 0) {
                    $_SESSION['sess_category'][] = $data2[0];
                } else {
                    $_SESSION['sess_subcategory'][] = $data2[0];
                }
                @$DRW->free_result($result3);
            }
        }
        @$DRW->free_result($result2);
        $_SESSION['sess_userID'] = $sess_userID;
        $_SESSION['sess_search_exclude'] = array();
        $result2 = $DRW->query("SELECT search_field FROM cscan_search_exclude WHERE userID=$sess_userID", $DRW_read);
        while ($data2 = $DRW->fetch_row($result2)) {
            $_SESSION['sess_search_exclude'][] = $data2[0];
        }
        @$DRW->free_result($result2);
    } elseif (!isset($_SESSION)) {
        $_SESSION = array();
        $_SESSION['sess_mchannel'] = array();
        $_SESSION['sess_mpanel'] = array();
        $_SESSION['sess_sector'] = array();
        $_SESSION['sess_category'] = array();
        $_SESSION['sess_subcategory'] = array();
        $_SESSION['sess_userID'] = 0;
        $_SESSION['sess_search_exclude'] = array();
    }
    if (is_bool($dograph)) {
        $dograph = (int) $dograph;
    }
    if ($bid < 0) {
        if (count($search_values) > 0) {
            $searchKey = '';
            $searchType = 'ocr2';
            $searchOption = 'boolean';
            $mChannelID = '';
            $sectorID = '';
            $mPanelID = '';
            $addedToDatabase = '';
            $month1 = '';
            $month2 = '';
            $sort = 'desc';
            $company = '';
            $productName = '';
            $incentive = '';
            $categoryID = '';
            $mTypeID = '';
            $subCategoryID = '';
            $cardStatus = '';
            $personalization = '';
            $gender = '';
            $age = '';
            $state = '';
            $worksiteVoluntary = 0;
            $agentCommunicationID = '';
            $groupSize = '';
            $offerOrigin = '';
            $enhance = 1;
            $saved = 0;
            $compaignLanguage = '';
            $affinityAssociation = '';
            $income_mult = '';
            $fa_id_mult = '';
            $tl_id_mult = '';
            $siteCatID_mult = '';
            $pubTypeID_mult = '';
            $approved_date = '0000-00-00 00:00:00';
            $approved_date_to = '0000-00-00 00:00:00';
            $electronicID_mult = '';
            $DMA_ID_mult = '';
            $businessContent_mult = '';
            $delmethid_mult = '';
            $affinity_association = '';
            $prescription = 0;
            $AffinityCategoryID_mult = '';
            $search_panelist_date = 0;
            $is_affinion = 0;
            $is_military = 0;
            $search_competi_id = '';
            $ApplicationType_mult = '';
            $is_multicultural = 0;
            $search_rules = '';
            $IntroPricing_mult = '';
            $is_rewards = 0;
            $RewardsProgramEmphasis_mult = '';
            $is_incentive = 0;
            $responseMechID_mult = '';
            $multiculturalmarkets_mult = '';
            $CardNetwork_mult = '';
            $FeeProduct = 0;
            $external_link = '';
            $FeeProductType = '';
            $ca_related = 0;
            $is_mover = 0;
            $scsc_primary = 0;
            $OptOutFirmOffer = 0;
            $searchKey2 = '';
            $search_type_and = 0;
            $riders_mult = '';
            $is_hphsa = 0;
            $subSubCategoryID = '';
            $Income_Producing_Assets_Segment_Code = '';
            $cg_id = '';
            $is_citi = 0;
            $is_CreditCardMentioned = 0;
            $spanelist_filter = '';
            $edc_id_mult = '';
            $AffinitySubCategoryID_mult = '';
            $ERateType_mult = '';
            $EOfferPrice_mult = '';
            $ETermLength_mult = '';
            $is_ECancelFee = 0;
            $IssueTypeID_mult = '';
            $pcountry = '';
            $is_Reloadable = 0;
            $creditUnion = 0;
            foreach ($search_values as $var => $val) {
                $$var = $val;
            }
        } else {
            $savedQ = "SELECT searchKey,searchType,searchOption,mChannelID,sectorID,mPanelID,
						addedToDatabase,month1,month2,sort,company,productName,incentive,categoryID,mTypeID,subCategoryID,cardStatus,personalization,
						gender,age,state,worksiteVoluntary,agentCommunicationID,groupSize,offerOrigin,enhance,saved,compaignLanguage,affinityAssociation,income_mult,fa_id_mult,tl_id_mult,
						siteCatID_mult,pubTypeID_mult,approved_date,electronicID_mult,DMA_ID_mult,businessContent_mult,delmethid_mult,affinity_association,prescription,AffinityCategoryID_mult,
						search_panelist_date,is_affinion,is_military,search_competi_id,ApplicationType_mult,is_multicultural,search_rules,IntroPricing_mult,is_rewards,RewardsProgramEmphasis_mult,is_incentive,responseMechID_mult,
						multiculturalmarkets_mult,CardNetwork_mult,FeeProduct,external_link,FeeProductType,approved_date_to,ca_related,is_mover,scsc_primary,OptOutFirmOffer,searchKey2,search_type_and,riders_mult,is_hphsa,subSubCategoryID,Income_Producing_Assets_Segment_Code_mult,cg_id,is_citi,is_CreditCardMentioned,
						spanelist_filter,edc_id_mult,AffinitySubCategoryID_mult,ERateType_mult,EOfferPrice_mult,ETermLength_mult,is_ECancelFee,IssueTypeID_mult,pcountry,is_Reloadable,creditUnion 
						FROM cscan_search WHERE ID='" . $search_id . "'";
            $rs = $DRW->query($savedQ, $DRW_read);
            $data = $DRW->fetch_row($rs);
            $searchKey = trim($data[0]);
            $searchType = $data[1];
            $searchOption = $data[2];
            $mChannelID = $data[3];
            $sectorID = $data[4];
            $mPanelID = $data[5];
            $addedToDatabase = $data[6];
            $month1 = $data[7];
            $month2 = $data[8];
            $sort = $data[9];
            $company = $data[10];
            $productName = $data[11];
            $incentive = $data[12];
            $categoryID = $data[13];
            $mTypeID = $data[14];
            $subCategoryID = $data[15];
            $cardStatus = $data[16];
            $personalization = $data[17];
            $gender = $data[18];
            $age = $data[19];
            $state = $data[20];
            $worksiteVoluntary = $data[21];
            $agentCommunicationID = $data[22];
            $groupSize = $data[23];
            $offerOrigin = $data[24];
            $enhance = $data[25];
            $saved = $data[26];
            $compaignLanguage = $data[27];
            $affinityAssociation = $data[28];
            $income_mult = $data[29];
            $fa_id_mult = $data[30];
            $tl_id_mult = $data[31];
            $siteCatID_mult = $data[32];
            $pubTypeID_mult = $data[33];
            $approved_date = $data[34];
            $electronicID_mult = $data[35];
            $DMA_ID_mult = $data[36];
            $businessContent_mult = $data[37];
            $delmethid_mult = $data[38];
            $affinity_association = $data[39];
            $prescription = $data[40];
            $AffinityCategoryID_mult = $data[41];
            $search_panelist_date = $data[42];
            $is_affinion = $data[43];
            $is_military = $data[44];
            $search_competi_id = $data[45];
            $ApplicationType_mult = $data[46];
            $is_multicultural = $data[47];
            $search_rules = $data[48];
            $IntroPricing_mult = $data[49];
            $is_rewards = $data[50];
            $RewardsProgramEmphasis_mult = $data[51];
            $is_incentive = $data[52];
            $responseMechID_mult = $data[53];
            $multiculturalmarkets_mult = $data[54];
            $CardNetwork_mult = $data[55];
            $FeeProduct = $data[56];
            $external_link = $data[57];
            $FeeProductType = $data[58];
            $approved_date_to = $data[59];
            $ca_related = $data[60];
            $is_mover = $data[61];
            $scsc_primary = $data[62];
            $OptOutFirmOffer = $data[63];
            $searchKey2 = $data[64];
            $search_type_and = $data[65];
            $riders_mult = $data[66];
            $is_hphsa = $data[67];
            $subSubCategoryID = $data[68];
            $Income_Producing_Assets_Segment_Code = $data[69];
            $cg_id = $data[70];
            $is_citi = $data[71];
            $is_CreditCardMentioned = $data[72];
            $spanelist_filter = $data[73];
            $edc_id_mult = $data[74];
            $AffinitySubCategoryID_mult = $data[75];
            $ERateType_mult = $data[76];
            $EOfferPrice_mult = $data[77];
            $ETermLength_mult = $data[78];
            $is_ECancelFee = $data[79];
            $IssueTypeID_mult = $data[80];
            $pcountry = $data[81];
            $is_Reloadable = $data[82];
            $creditUnion = $data[83];
        }
        if (!empty($AffinitySubCategoryID_mult)) {
            $AffinityCategoryID_mult = $AffinitySubCategoryID_mult;
        }
        if ($search_panelist_date_over != -1) {
            $search_panelist_date = $search_panelist_date_over;
        }
        if ($approved_date == '0000-00-00' || $approved_date == '0000-00-00 00:00:00') {
            $approved_date = '';
        }
        if ($approved_date_to == '0000-00-00' || $approved_date_to == '0000-00-00 00:00:00') {
            $approved_date_to = '';
        }
        if (!empty($approved_date) && !empty($approved_date_to)) {
            $approved_date = "$approved_date,$approved_date_to";
        }
        if ($month1 != '' || $month2 != '') {
            $month = "$month1,$month2";
        } else {
            $month = '';
        }
        if (empty($mChannelID)) {
            $mChannelID = implode(',', $_SESSION['sess_mchannel']);
        }
        if (empty($mPanelID)) {
            $mPanelID = implode(',', $_SESSION['sess_mpanel']);
        }
        $isBiz = '';
        //business owner check when Business Owner is a Panel (6)
        /* $tmpArray = explode(',',$mPanelID);
          $tmpArray2 = array();
          foreach($tmpArray as $v){
          if($v==6) {
          $isBiz = 1;
          }
          else{
          $tmpArray2[] = $v;
          }
          }
          $mPanelID = implode(',',$tmpArray2); */

        if (empty($state) && in_array('canada', $_SESSION['sess_search_exclude'])) {
            $pcountry = 'US';
        } elseif (!empty($state)) {
            $pcountry = '';
        }
        if ($mPanelID == '1' || $mPanelID == '2' || $mPanelID == '1,2') {
            $consumer_only = true;
        } else {
            $consumer_only = false;
        }
        $ppdate = '';
        $ppdate_month = '';
        $ppstateID = '';
        $pgender = '';
        $is_panelist = 0;
        if (($search_panelist_date || $consumer_only) && $search_panelist_date_over != 0) {
            $ppdate = $addedToDatabase;
            $addedToDatabase = '';
            $ppdate_month = $month;
            $month = '';
            $ppstateID = $state;
            $state = '';
            $pgender = $gender;
            $gender = '';
            $is_panelist = 1;
        }

        //state relationship instead
        if ($ppstateID != '') {
            $rstate = $ppstateID;
        } else {
            $rstate = $state;
        }
        $ppstateID = '';
        $state = '';

        $andorArray = array();
        $andorArray['sectorID'] = 'OR';
        $exacterArray = array();
        $exactervalsArray = array();
        $noterArray = array();
        $search_rulesArray = explode(',', $search_rules);
        foreach ($search_rulesArray as $sr) {
            if (!empty($sr)) {
                list($f, $ao, $ex, $no) = explode(':', $sr);
                if ($ao) {
                    $andorArray[$f] = 'AND';
                }
                if ($ex) {
                    $exacterArray[] = $f;
                    $exactervalsArray[$f] = array();
                }
                if ($no) {
                    $noterArray[] = $f;
                }
            }
        }
        if ($spanelist_filter == '1') {
            $pjoin_filter = ' AND pp.pprimary=1';
            $pjoin_left = '';
        } elseif ($spanelist_filter == '2') {
            $pjoin_filter = ' AND pp.pprimary=0';
            $pjoin_left = '';
        } else {
            $pjoin_filter = '';
            $pjoin_left = ' LEFT';
        }
        $exactArray = array();
        $multExactArray = array('mChannelID' => $mChannelID, 'mPanelID' => $mPanelID);
        $likeArray = array('incentive' => $incentive);
        $multArray = array();
        $otherArray = array('company' => $company, 'affinity_association' => $affinity_association, 'addedToDatabase' => $addedToDatabase, 'month' => $month, 'approved_date' => $approved_date, 'search_competi_id' => $search_competi_id, 'ApplicationType_mult' => $ApplicationType_mult, 'cg_id' => $cg_id, 'rstate' => $rstate, 'pcountry' => $pcountry);
        $panelistArray = array('ppageID' => $age, 'pincomeID' => $income_mult, 'isBiz' => $isBiz, 'dmap.code' => $DMA_ID_mult, 'ppdate' => $ppdate, 'ppdate_month' => $ppdate_month, 'ppstateID' => $ppstateID, 'pgender' => $pgender, 'Income_Producing_Assets_Segment_Code' => $Income_Producing_Assets_Segment_Code, 'edc_id' => $edc_id_mult);
        if ($productName != '') {
            $keyArray = preg_split('/"\\s+or\\s+"/i', $productName, -1, PREG_SPLIT_NO_EMPTY);
            if (count($keyArray) > 1 || preg_match('/^"([^"]+)"$/', $productName)) {
                foreach ($keyArray as $k => $val) {
                    if (preg_match('/^"([^"]+)"$/', $val, $match) || preg_match('/^([^"]+)"$/', $val, $match) || preg_match('/^"([^"]+)$/', $val, $match)) {
                        $keyArray[$k] = $match[1];
                    }
                }
                $multExactArray['productName'] = $keyArray;
            } else {
                $likeArray['productName'] = $productName;
            }
        }
        if ($enhance) {
            $exactArray = array_merge($exactArray, array('cardStatus' => $cardStatus, 'personalization' => $personalization, 'gender' => $gender, 'offerOrigin' => $offerOrigin, 'compaignLanguage' => $compaignLanguage));
            $multExactArray = array_merge($multExactArray, array('mTypeID' => $mTypeID, 'delmethid' => $delmethid_mult));
            $likeArray = array_merge($likeArray, array('external_link' => $external_link));
            $multArray = array_merge($multArray, array('state' => $state, 'agentCommunicationID' => $agentCommunicationID, 'groupSize' => $groupSize, 'fa_ids' => $fa_id_mult, 'tl_ids' => $tl_id_mult, 'electronicID' => $electronicID_mult, 'businessContent' => $businessContent_mult, 'multiculturalmarkets' => $multiculturalmarkets_mult, 'responseMechID' => $responseMechID_mult, 'FeeProductType' => $FeeProductType, 'riders' => $riders_mult, 'IssueTypeID' => $IssueTypeID_mult));
            $otherArray = array_merge($otherArray, array('AffinityCategoryID' => $AffinityCategoryID_mult, 'worksiteVoluntary' => $worksiteVoluntary, 'affinityAssociation' => $affinityAssociation, 'siteCatID' => $siteCatID_mult, 'pubTypeID' => $pubTypeID_mult, 'prescription' => $prescription, 'is_affinion' => $is_affinion, 'is_military' => $is_military, 'is_multicultural' => $is_multicultural, 'IntroPricing_mult' => $IntroPricing_mult, 'is_rewards' => $is_rewards, 'RewardsProgramEmphasis_mult' => $RewardsProgramEmphasis_mult, 'is_incentive' => $is_incentive, 'CardNetwork_mult' => $CardNetwork_mult, 'FeeProduct' => $FeeProduct, 'is_mover' => $is_mover, 'OptOutFirmOffer' => $OptOutFirmOffer, 'is_hphsa' => $is_hphsa, 'is_citi' => $is_citi, 'is_CreditCardMentioned' => $is_CreditCardMentioned, 'ERateType' => $ERateType_mult, 'EOfferPrice' => $EOfferPrice_mult, 'ETermLength' => $ETermLength_mult, 'ECancelFee' => $is_ECancelFee, 'Reloadable' => $is_Reloadable, 'isCreditUnion' => $creditUnion));
        }
        foreach ($exactArray as $field => $val) {
            if ($val != '') {
                $where .= " $field";
                if (in_array($field, $noterArray)) {
                    $where .= '<>';
                } else {
                    $where .= '=';
                }
                $where .= "'" . $DRW->real_escape_string($val) . "' AND ";
            }
        }
        foreach ($multExactArray as $field => $val) {
            if ($val != '') {
                $tmpwhere = '';
                if (is_array($val)) {
                    $tmpArray = $val;
                } else {
                    $tmpArray = explode(',', $val);
                }
                foreach ($tmpArray as $v) {
                    if ($v != '') {
                        $tmpwhere .= " $field";
                        if (in_array($field, $noterArray)) {
                            $tmpwhere .= '<>';
                        } else {
                            $tmpwhere .= '=';
                        }
                        $tmpwhere .= "'" . $DRW->real_escape_string($v) . "'";
                        if (in_array($field, $noterArray)) {
                            $tmpwhere .= ' AND ';
                        } else {
                            $tmpwhere .= ' OR ';
                        }
                    }
                }
                if ($field == 'mPanelID') {
                    $awhere .= $tmpwhere;
                } else {
                    $where .= " (" . substr($tmpwhere, 0, -4) . ") AND ";
                }
            }
        }
        foreach ($likeArray as $field => $val) {
            if ($val != '') {
                $val = mysqlLike($val);
                $where .= " $field ";
                if (in_array($field, $noterArray)) {
                    $where .= 'NOT ';
                }
                $where .= "LIKE '%$val%' AND ";
            }
        }
        foreach ($multArray as $field => $val) {
            if ($val != '') {
                $tmpArray = explode(',', $val);
                $where .= " (";
                foreach ($tmpArray as $v) {
                    if ($v != '') {
                        $where .= " ($field ";
                        if (in_array($field, $noterArray)) {
                            $where .= 'NOT ';
                        } else {
                            $where .= "LIKE '%{$v}%' AND $field ";
                        }
                        $where .= "REGEXP '[[:<:]]{$v}[[:>:]]')";
                        if (in_array($field, $noterArray)) {
                            $where .= ' AND ';
                        } else {
                            $where .= ' OR ';
                        }
                    }
                }
                $where = substr($where, 0, -4);
                $where .= ") AND ";
            }
        }
        $sect_j .= ' JOIN cscan_scsc_product as scsc ON (pd.productID=scsc.productID)';
        $partsArray = array();
        $seccatsubArray = get_seccatsub(implode(',', $_SESSION['sess_sector']), implode(',', $_SESSION['sess_category']), implode(',', $_SESSION['sess_subcategory']));
        foreach ($seccatsubArray as $sid => $cArray) {
            $part1 = "scsc.scsc_sectorID=$sid";
            $partsArray[] = '(' . $part1 . ' AND scsc.scsc_categoryID=0 AND scsc.scsc_subCategoryID=0)';
            foreach ($cArray as $cid => $scArray) {
                $part2 = "scsc.scsc_categoryID=$cid";
                $partsArray[] = '(' . $part1 . ' AND ' . $part2 . ' AND scsc.scsc_subCategoryID=0)';
                foreach ($scArray as $scid => $a) {
                    $part3 = "scsc.scsc_subCategoryID=$scid";
                    $partsArray[] = '(' . $part1 . ' AND ' . $part2 . ' AND ' . $part3 . ')';
                }
            }
        }
        if (count($partsArray) > 0) {
            $where .= '(' . implode(' OR ', $partsArray) . ') AND ';
        }
        $partsArray2 = array();
        $j = 0;
        if ($andorArray['sectorID'] == 'AND') {
            $alias = "scsc$j";
        } else {
            $alias = "scsc";
        }
        $seccatsubArray = get_seccatsub($sectorID, $categoryID, $subCategoryID, $subSubCategoryID);
        foreach ($seccatsubArray as $sid => $cArray) {
            $part1 = $alias . ".scsc_sectorID=$sid";
            if ($scsc_primary) {
                $part1 .= ' AND ' . $alias . ".scsc_sort=1";
            }
            if (in_array('sectorID', $exacterArray)) {
                $exactervalsArray['sectorID'][] = $sid;
            }
            if (count($cArray) == 0) {
                $partsArray2[] = '(' . $part1 . ')';
                if ($andorArray['sectorID'] == 'AND') {
                    $sect_j .= ' JOIN cscan_scsc_product as ' . $alias . ' ON (pd.productID=' . $alias . '.productID)';
                    $j++;
                    $alias = "scsc$j";
                }
            } else {
                foreach ($cArray as $cid => $scArray) {
                    $part2 = $alias . ".scsc_categoryID=$cid";
                    if (in_array('sectorID', $exacterArray)) {
                        $exactervalsArray['categoryID'][] = $cid;
                    }
                    if (count($scArray) == 0) {
                        $partsArray2[] = '(' . $part1 . ' AND ' . $part2 . ')';
                        if ($andorArray['sectorID'] == 'AND') {
                            $sect_j .= ' JOIN cscan_scsc_product as ' . $alias . ' ON (pd.productID=' . $alias . '.productID)';
                            $j++;
                            $alias = "scsc$j";
                        }
                    } else {
                        foreach ($scArray as $scid => $sscArray) {
                            $part3 = $alias . ".scsc_subCategoryID=$scid";
                            if (in_array('sectorID', $exacterArray)) {
                                $exactervalsArray['subCategoryID'][] = $scid;
                            }
                            if (count($sscArray) == 0) {
                                $partsArray2[] = '(' . $part1 . ' AND ' . $part2 . ' AND ' . $part3 . ')';
                                if ($andorArray['sectorID'] == 'AND') {
                                    $sect_j .= ' JOIN cscan_scsc_product as ' . $alias . ' ON (pd.productID=' . $alias . '.productID)';
                                    $j++;
                                    $alias = "scsc$j";
                                }
                            } else {
                                foreach ($sscArray as $sscid => $ssscArray) {
                                    $part4 = $alias . ".scsc_subSubCategoryID=$sscid";
                                    if (in_array('sectorID', $exacterArray)) {
                                        $exactervalsArray['subSubCategoryID'][] = $sscid;
                                    }
                                    $partsArray2[] = '(' . $part1 . ' AND ' . $part2 . ' AND ' . $part3 . ' AND ' . $part4 . ')';
                                    if ($andorArray['sectorID'] == 'AND') {
                                        $sect_j .= ' JOIN cscan_scsc_product as ' . $alias . ' ON (pd.productID=' . $alias . '.productID)';
                                        $j++;
                                        $alias = "scsc$j";
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        if (count($partsArray2) > 0) {
            $where .= '(' . implode(' ' . $andorArray['sectorID'] . ' ', $partsArray2) . ') AND ';
        }
        foreach ($exactervalsArray as $f => $a) {
            $combos = array();
            if (count($a) <= 6) {
                pc_permute($a, $combos);
            }
            $wherec = '';
            foreach ($combos as $c) {
                $v = implode(',', $c);
                $wherec .= " $f='$v' OR ";
            }
            if ($wherec != '') {
                $where .= '(' . substr($wherec, 0, -4) . ') AND ';
            }
        }
        foreach ($otherArray as $field => $val) {
            if ($val != '') {
                if ($field == 'addedToDatabase' && $addedtodatabaseover == '') {
                    if ($val == 'week') {
                        $where .= ' addedToDatabase>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 7 DAY),\' 00:00:00\') AND ';
                        $filter_range[] = array('dts_date', strtotime('-7 days', strtotime(date('Y-m-d') . ' 00:00:00')), time());
                    } elseif ($val == '2week') {
                        $where .= ' addedToDatabase>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 14 DAY),\' 00:00:00\') AND ';
                        $filter_range[] = array('dts_date', strtotime('-14 days', strtotime(date('Y-m-d') . ' 00:00:00')), time());
                    } elseif ($val == '1month') {
                        $where .= ' addedToDatabase>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 1 MONTH),\' 00:00:00\') AND ';
                        $filter_range[] = array('dts_date', strtotime('-1 month', strtotime(date('Y-m-d') . ' 00:00:00')), time());
                    } elseif ($val == '3month') {
                        $where .= ' addedToDatabase>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 3 MONTH),\' 00:00:00\') AND ';
                        $filter_range[] = array('dts_date', strtotime('-3 months', strtotime(date('Y-m-d') . ' 00:00:00')), time());
                    } elseif ($val == '6month') {
                        $where .= ' addedToDatabase>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 6 MONTH),\' 00:00:00\') AND ';
                        $filter_range[] = array('dts_date', strtotime('-6 months', strtotime(date('Y-m-d') . ' 00:00:00')), time());
                    } elseif ($val == '1year') {
                        $where .= ' addedToDatabase>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 1 YEAR),\' 00:00:00\') AND ';
                        $filter_range[] = array('dts_date', strtotime('-1 year', strtotime(date('Y-m-d') . ' 00:00:00')), time());
                    }
                } elseif ($field == 'month' && $addedtodatabaseover == '') {
                    $monthArray = explode(',', $val);
                    $month_1 = $monthArray[0];
                    $month_2 = $monthArray[1];
                    if ($month_1 == '') {
                        $month_1 = $month_2;
                    } elseif ($month_2 == '') {
                        $month_2 = $month_1;
                    }
                    $where .= " (addedToDatabase BETWEEN '$month_1-01 00:00:00' AND '$month_2-31 23:59:59') AND ";
                    $filter_range[] = array('dts_date', strtotime("$month_1-01 00:00:00"), strtotime("$month_2-31 23:59:59"));
                } elseif ($field == 'worksiteVoluntary' || $field == 'affinityAssociation' || $field == 'prescription' || $field == 'is_affinion' || $field == 'is_military' || $field == 'FeeProduct' || $field == 'is_mover' || $field == 'OptOutFirmOffer' || $field == 'is_hphsa' || $field == 'is_citi' || $field == 'ECancelFee' || $field == 'Reloadable' || $field == 'isCreditUnion') {
                    if (!empty($val)) {
                        if ($val == 1) {
                            $fieldval = 1;
                        } elseif ($val == 2) {
                            $fieldval = 0;
                        }
                        if ($field == 'OptOutFirmOffer') {
                            /* $sectorIDArray = explode(',',$sectorID);
                              if(!in_array(4,$sectorIDArray) && (in_array(90,$sectorIDArray) || in_array(6,$sectorIDArray))){
                              $tmp = '';
                              $in = false;
                              if(in_array(90,$sectorIDArray)){
                              $in = true;
                              $tmp .= "cscan_payment_cards.OptOutFirmOffer=$fieldval";
                              $pcjoin = ' JOIN cscan_payment_cards ON (pd.productID=cscan_payment_cards.productID)';
                              }
                              if(in_array(6,$sectorIDArray)){
                              if($in){
                              $tmp .= ' OR ';
                              }
                              $tmp .= "cscan_mortgage_loan.MLOptOutFirmOffer=$fieldval";
                              $mljoin = ' JOIN cscan_mortgage_loan ON (pd.productID=cscan_mortgage_loan.productID)';
                              }
                              $where .= " ($tmp) AND ";
                              }
                              else{ */
                            $where .= " is_prescreen=$fieldval AND ";
                            //}
                        } else {
                            $where .= " $field=$fieldval AND ";
                        }
                        if ($field == 'ECancelFee') {
                            $ejoin = ' JOIN cscan_energy ON (pd.productID=cscan_energy.productID)';
                        } elseif ($field == 'Reloadable') {
                            $pcjoin = ' JOIN cscan_payment_cards ON (pd.productID=cscan_payment_cards.productID)';
                        } elseif ($field == 'isCreditUnion') {
                            $cjoin = " JOIN cscan_company_product ON (cscan_company_product.productID=pd.productID) ";
                            $ccjoin = " JOIN cscan_company ON (cscan_company_product.companyID=cscan_company.companyID) ";
                        }
                    }
                } elseif ($field == 'is_multicultural') {
                    if ($val == 1) {
                        $where .= " multiculturalmarkets<>'' AND ";
                    }
                } elseif ($field == 'is_incentive') {
                    if ($val == 1) {
                        $where .= " incentive<>'' AND ";
                    }
                } elseif ($field == 'is_rewards') {
                    if ($val == 1) {
                        $tmp = '';
                        $in = false;
                        $sectorIDArray = explode(',', $sectorID);
                        if (in_array(90, $sectorIDArray)) {
                            $in = true;
                            $tmp .= "cscan_payment_cards.RewardsProgram=1";
                            $pcjoin = ' JOIN cscan_payment_cards ON (pd.productID=cscan_payment_cards.productID)';
                        }
                        if (in_array(87, $sectorIDArray)) {
                            if ($in) {
                                $tmp .= ' OR ';
                            }
                            $tmp .= "cscan_banking.BankingRewardsProgram=1";
                            $bjoin = ' JOIN cscan_banking ON (pd.productID=cscan_banking.productID)';
                        }
                        $where .= " ($tmp) AND ";
                    }
                } elseif ($field == 'is_CreditCardMentioned') {
                    if ($val == 1) {
                        $tmp = '';
                        $in = false;
                        $sectorIDArray = explode(',', $sectorID);
                        if (in_array(219, $sectorIDArray)) {
                            $in = true;
                            $tmp .= "cscan_travel_leisure.TLCreditCardMentioned=1";
                            $tljoin = ' JOIN cscan_travel_leisure ON (pd.productID=cscan_travel_leisure.productID)';
                        }
                        if (in_array(266, $sectorIDArray) || in_array(559, $sectorIDArray) || in_array(560, $sectorIDArray)) {
                            if ($in) {
                                $tmp .= ' OR ';
                            }
                            $tmp .= "cscan_retail.RCreditCardMentioned=1";
                            $rjoin = ' JOIN cscan_retail ON (pd.productID=cscan_retail.productID)';
                        }
                        $where .= " ($tmp) AND ";
                    }
                } elseif ($field == 'siteCatID' || $field == 'pubTypeID' || $field == 'ApplicationType_mult') {
                    $field2 = '';
                    if ($field == 'siteCatID') {
                        $field = 'cscan_sites.sites_category_id';
                        $ojoin .= ',cscan_sites_product,cscan_sites';
                        $owhere .= ' AND pd.productID=cscan_sites_product.productID AND cscan_sites_product.sites_id=cscan_sites.sites_id';
                    } elseif ($field == 'pubTypeID') {
                        $field = 'cscan_publication.print_typeID';
                        $ojoin .= ',cscan_publication_product,cscan_publication';
                        $owhere .= ' AND pd.productID=cscan_publication_product.productID AND cscan_publication_product.publicationID=cscan_publication.publicationID';
                    } elseif ($field == 'ApplicationType_mult') {
                        $sectorIDArray = explode(',', $sectorID);
                        $in = false;
                        if (in_array(90, $sectorIDArray)) {
                            $in = true;
                            $field = 'cscan_payment_cards.ApplicationType';
                            $pcjoin = ' JOIN cscan_payment_cards ON (pd.productID=cscan_payment_cards.productID)';
                        }
                        if (in_array(6, $sectorIDArray)) {
                            if ($in) {
                                $field2 = 'cscan_mortgage_loan.MLApplicationType';
                            } else {
                                $field = 'cscan_mortgage_loan.MLApplicationType';
                            }
                            $mljoin = ' JOIN cscan_mortgage_loan ON (pd.productID=cscan_mortgage_loan.productID)';
                        }
                    }
                    $tmpArray = explode(',', $val);
                    $where .= " (";
                    foreach ($tmpArray as $v) {
                        if ($v != '') {
                            $where .= " $field='" . $DRW->real_escape_string($v) . "' OR ";
                            if (!empty($field2)) {
                                $where .= " $field2='" . $DRW->real_escape_string($v) . "' OR ";
                            }
                        }
                    }
                    $where = substr($where, 0, -4);
                    $where .= ") AND ";
                } elseif ($field == 'IntroPricing_mult') {
                    $tmpArray = explode(',', $val);
                    $where .= " (";
                    foreach ($tmpArray as $v) {
                        switch ($v) {
                            case 1:
                                $where .= " cscan_payment_cards.PurchaseIntroductoryAPR is not null OR ";
                                break;
                            case 2:
                                $where .= " cscan_payment_cards.PurchaseIntroductoryAPR is null OR ";
                                break;
                            case 3:
                                $where .= " cscan_payment_cards.BalanceTransferIntroductoryAPR is not null OR ";
                                break;
                            case 4:
                                $where .= " cscan_payment_cards.BalanceTransferIntroductoryAPR is null OR ";
                                break;
                        }
                    }
                    $where = substr($where, 0, -4);
                    $where .= ") AND ";
                    $pcjoin = ' JOIN cscan_payment_cards ON (pd.productID=cscan_payment_cards.productID)';
                } elseif ($field == 'approved_date') {
                    $splitArray = explode(',', $val);
                    $split_1 = $splitArray[0];
                    $split_2 = $splitArray[1];
                    if ($split_1 == '') {
                        $split_1 = $split_2;
                    } elseif ($split_2 == '') {
                        $split_2 = $split_1;
                    }
                    $where .= " ($field BETWEEN '$split_1' AND '$split_2') AND ";
                } elseif ($field == 'search_competi_id' || $field == 'cg_id') {
                    $panelist_ids = array();
                    if ($field == 'cg_id') {
                        $sqlc = "SELECT DISTINCT panelist_id FROM cscan_panelists_product_group WHERE cg_id IN (" . $val . ")";
                        $rsc = $DRW->query($sqlc, $DRW_read);
                        while ($rowc = $DRW->fetch_row($rsc)) {
                            $panelist_ids[] = $rowc[0];
                        }
                    } else {
                        $vs = explode(',', $val);
                        $competi_ids = array();
                        foreach ($vs as $v) {
                            $competi_ids[] = "'" . $DRW->real_escape_string(trim($v)) . "'";
                        }
                        $sqlc = "SELECT panelist_id FROM cscan_panelists WHERE competi_id IN (" . implode(',', $competi_ids) . ")";
                        $rsc = $DRW->query($sqlc, $DRW_read);
                        while ($rowc = $DRW->fetch_row($rsc)) {
                            $panelist_ids[] = $rowc[0];
                        }
                    }
                    if (count($panelist_ids) == 0) {
                        $panelist_ids[] = '-1';
                    }
                    $pjoin = ' JOIN cscan_panelists_product pp ON (pd.productID=pp.productID' . $pjoin_filter . ')';
                    $where .= " pp.panelist_id IN (" . implode(',', $panelist_ids) . ") AND ";
                } elseif ($field == 'CardNetwork_mult' || $field == 'RewardsProgramEmphasis_mult') {
                    $sectorIDArray = explode(',', $sectorID);
                    $in = false;
                    $field2 = '';
                    if ($field == 'CardNetwork_mult') {
                        if (in_array(90, $sectorIDArray)) {
                            $in = true;
                            $field = 'cscan_payment_cards.CardNetwork';
                            $pcjoin = ' JOIN cscan_payment_cards ON (pd.productID=cscan_payment_cards.productID)';
                        }
                        if (in_array(87, $sectorIDArray)) {
                            if ($in) {
                                $field2 = 'cscan_banking.BankingCardNetwork';
                            } else {
                                $field = 'cscan_banking.BankingCardNetwork';
                            }
                            $bjoin = ' JOIN cscan_banking ON (pd.productID=cscan_banking.productID)';
                        }
                    } elseif ($field == 'RewardsProgramEmphasis_mult') {
                        $in = false;
                        if (in_array(90, $sectorIDArray)) {
                            $in = true;
                            $field = 'cscan_payment_cards.RewardsProgramEmphasis';
                            $pcjoin = ' JOIN cscan_payment_cards ON (pd.productID=cscan_payment_cards.productID)';
                        }
                        if (in_array(87, $sectorIDArray)) {
                            if ($in) {
                                $field2 = 'cscan_banking.BankingRewardsProgramEmphasis';
                            } else {
                                $field = 'cscan_banking.BankingRewardsProgramEmphasis';
                            }
                            $bjoin = ' JOIN cscan_banking ON (pd.productID=cscan_banking.productID)';
                        }
                    }
                    $tmpArray = explode(',', $val);
                    $where .= " (";
                    foreach ($tmpArray as $v) {
                        if ($v != '') {
                            $where .= " ($field like '%{$v}%' AND $field REGEXP '[[:<:]]{$v}[[:>:]]') OR ";
                            if (!empty($field2)) {
                                $where .= " ($field2 like '%{$v}%' AND $field2 REGEXP '[[:<:]]{$v}[[:>:]]') OR ";
                            }
                        }
                    }
                    $where = substr($where, 0, -4);
                    $where .= ") AND ";
                } elseif ($field == 'ERateType' || $field == 'EOfferPrice' || $field == 'ETermLength') {
                    $ejoin = ' JOIN cscan_energy ON (pd.productID=cscan_energy.productID)';

                    $efields_array = array('', '2', '3', '4');
                    $tmpArray = explode(',', $val);
                    $where .= " (";
                    if ($field == 'EOfferPrice') {
                        if (empty($tmpArray[0])) {
                            unset($tmpArray[0]);
                        }
                        $sql = "SELECT OfferPriceMin,OfferPriceMax FROM cscan_offer_price WHERE OfferPriceID IN(" . implode(',', $tmpArray) . ") ORDER BY OfferPriceSort";
                        $rs = $DRW->query($sql, $DRW_read);
                        $types = array();
                        while ($op = $DRW->fetch_row($rs)) {
                            foreach ($efields_array as $field_concat) {
                                $where .= " ($field$field_concat>=" . $op[0] . " AND $field$field_concat<=" . $op[1] . ") OR ";
                            }
                        }
                    } else {
                        foreach ($tmpArray as $v) {
                            if ($v != '') {
                                foreach ($efields_array as $field_concat) {
                                    $where .= " $field$field_concat='" . $DRW->real_escape_string($v) . "' OR ";
                                }
                            }
                        }
                    }
                    $where = substr($where, 0, -4);
                    $where .= ") AND ";
                } elseif ($field == 'company' || $field == 'affinity_association') {
                    $cos = array();
                    if ($ca_related == 1) {
                        if ($field == 'affinity_association') {
                            $sqlc = "SELECT DISTINCT pp.panelist_id FROM cscan_affinity pa,cscan_panelist_affinity pp
								WHERE pa.affinityID=pp.affinityID AND " . doMultCompany($val, true, 'affinity');
                        } else {
                            $sqlc = "SELECT DISTINCT pp.panelist_id FROM cscan_company pa,cscan_panelist_company pp
								WHERE pa.companyID=pp.companyID AND " . doMultCompany($val, true, 'company');
                        }
                        $rsc = $DRW->query($sqlc, $DRW_read);
                        while ($rowc = $DRW->fetch_row($rsc)) {
                            $cos[] = $rowc[0];
                        }
                        if (count($cos) == 0) {
                            $cos[] = '0';
                        }
                        $pjoin = ' JOIN cscan_panelists_product pp ON (pd.productID=pp.productID' . $pjoin_filter . ')';
                        $where .= " pp.panelist_id IN (" . implode(',', $cos) . ") AND ";
                    } else {
                        if ($field == 'affinity_association') {
                            $caat = 'affinity';
                            $afjoin = " JOIN cscan_affinity_product ON (cscan_affinity_product.productID=pd.productID) ";
                        } else {
                            $caat = 'company';
                            $cjoin = " JOIN cscan_company_product ON (cscan_company_product.productID=pd.productID) ";
                        }
                        $sqlc = "SELECT {$caat}ID FROM cscan_$caat WHERE " . doMultCompany($val, true, $caat);
                        $rsc = $DRW->query($sqlc, $DRW_read);
                        while ($rowc = $DRW->fetch_row($rsc)) {
                            $cos[] = $rowc[0];
                        }
                        if (count($cos) == 0) {
                            $cos[] = '0';
                        }
                        $where .= " cscan_{$caat}_product.{$caat}ID IN (" . implode(',', $cos) . ") AND ";
                    }
                } elseif ($field == 'AffinityCategoryID') {
                    $cos = explode(',', $val);
                    if (empty($cos[0])) {
                        unset($cos[0]);
                    }
                    $afjoin = " JOIN cscan_affinity_product ON (cscan_affinity_product.productID=pd.productID) ";
                    $affjoin = " JOIN cscan_affinity ON (cscan_affinity.affinityID=cscan_affinity_product.affinityID) JOIN cscan_aff_cat ON (cscan_affinity.affinityID=cscan_aff_cat.affinityID) ";
                    $where .= " cscan_aff_cat.AffinityCategoryID IN (" . implode(',', $cos) . ") AND ";
                } elseif ($field == 'pcountry' || $field == 'rstate') {
                    if (empty($sjoin)) {
                        $sjoin = " JOIN cscan_product_detail_state ON (cscan_product_detail_state.productID=pd.productID)";
                    }
                    if ($field == 'pcountry') {
                        //$sjoin .= " JOIN cscan_state ON (cscan_product_detail_state.stateID=cscan_state.stateID) ";
                        //$where .= " (cscan_state.countryCode='".$DRW->real_escape_string($val)."' OR cscan_state.countryCode='') AND ";
                        $where .= " (cscan_product_detail_state.countryCode_copy='" . $DRW->real_escape_string($val) . "' OR cscan_product_detail_state.countryCode_copy='') AND ";
                    } else {
                        $cos = explode(',', $val);
                        if (empty($cos[0])) {
                            unset($cos[0]);
                        }
                        $where .= " cscan_product_detail_state.stateID IN (" . implode(',', $cos) . ") AND ";
                    }
                    if (empty($is_panelist)) {
                        $where .= " cscan_product_detail_state.is_panelist=0 AND ";
                    }
                }
            }
        }
        foreach ($panelistArray as $field => $val) {
            if ($val != '') {
                $pjoin = $pjoin_left . ' JOIN cscan_panelists_product pp ON (pd.productID=pp.productID' . $pjoin_filter . ')';
                if ($field == 'Income_Producing_Assets_Segment_Code') {
                    $ajoin = " JOIN cscan_panelists_appends ON (cscan_panelists_appends.panelist_id=pp.panelist_id) ";
                } elseif ($field == 'edc_id') {
                    $edcjoin = ' JOIN cscan_edc_postalcode edc ON(pp.pppostalcode=edc.pppostalcode)';
                } elseif ($field == 'dmap.code') {
                    $dmajoin = ' JOIN cscan_dma_code_postalcode dmap ON (pp.pppostalcode=dmap.pppostalcode)';
                }
                if ($field == 'ppdate') {
                    if ($val == 'week')
                        $where .= ' (pp.ppdate>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 7 DAY),\' 00:00:00\') OR addedToDatabase>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 7 DAY),\' 00:00:00\')) AND ';
                    elseif ($val == '2week')
                        $where .= ' (pp.ppdate>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 14 DAY),\' 00:00:00\') OR addedToDatabase>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 14 DAY),\' 00:00:00\')) AND ';
                    elseif ($val == '1month')
                        $where .= ' (pp.ppdate>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 1 MONTH),\' 00:00:00\') OR addedToDatabase>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 1 MONTH),\' 00:00:00\')) AND ';
                    elseif ($val == '3month')
                        $where .= ' (pp.ppdate>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 3 MONTH),\' 00:00:00\') OR addedToDatabase>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 3 MONTH),\' 00:00:00\')) AND ';
                    elseif ($val == '6month')
                        $where .= ' (pp.ppdate>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 6 MONTH),\' 00:00:00\') OR addedToDatabase>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 6 MONTH),\' 00:00:00\')) AND ';
                    elseif ($val == '1year')
                        $where .= ' (pp.ppdate>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 1 YEAR),\' 00:00:00\') OR addedToDatabase>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 1 YEAR),\' 00:00:00\')) AND ';
                }
                elseif ($field == 'ppdate_month') {
                    $monthArray = explode(',', $val);
                    $month_1 = $monthArray[0];
                    $month_2 = $monthArray[1];
                    if ($month_1 == '') {
                        $month_1 = $month_2;
                    } elseif ($month_2 == '') {
                        $month_2 = $month_1;
                    }
                    $where .= " ((pp.ppdate BETWEEN '$month_1-01 00:00:00' AND '$month_2-31 23:59:59') OR (addedToDatabase BETWEEN '$month_1-01 00:00:00' AND '$month_2-31 23:59:59')) AND ";
                } elseif ($field == 'pgender') {
                    $where .= " (pp.pgender='$val' OR gender='$val') AND ";
                } elseif ($field == 'ppageID') {
                    $ageIds = explode(',', $val);
                    $where .= "(";

                    foreach ($ageIds as $id) {
                        $where .= "pp.ppageID LIKE '%$id%' ";

                        if ($id === end($ageIds)) {
                            $where .= ') AND ';
                        } else {
                            $where .= ' OR ';
                        }
                    }
                } else {
                    $tmpwhere = '';
                    $tmpArray = explode(',', $val);
                    foreach ($tmpArray as $v) {
                        if ($v != '') {
                            if ($field == 'Income_Producing_Assets_Segment_Code' || $field == 'dmap.code') {
                                $tmpwhere .= " $field='" . $v . "' OR ";
                            } elseif ($field == 'ppstateID') {
                                $tmpwhere .= " pp.ppstateID=" . (int) $v . " OR (state like '%{$v}%' AND state REGEXP '[[:<:]]{$v}[[:>:]]') OR ";
                            } else {
                                $tmpwhere .= " $field=" . (int) $v . " OR ";
                            }
                        }
                    }
                    if ($field == 'isBiz') {
                        $awhere .= $tmpwhere;
                    } else {
                        $where .= " (" . substr($tmpwhere, 0, -4) . ") AND ";
                    }
                }
            }
        }
        if ($awhere != '') {
            $where .= " (" . substr($awhere, 0, -4) . ") AND ";
        }
        if ($addedtodatabaseover != '') {
            $where .= " addedToDatabase>='$addedtodatabaseover' AND ";
            $filter_range[] = array('dts_date', strtotime($addedtodatabaseover), time());
        }
        $where .= " addedToDatabase<=NOW() AND (productStatus=1";
        if ($unapproved) {
            $where .= " OR productStatus=2";
        }
        $where .= ") AND ";
        if ($searchKey != '' || $searchKey2 != '') {
            if ($searchType == 'ocr') {
                //updateOCR_soundex();
                $matchagainst = 'MATCH(dts_val) AGAINST';
                //if(isMysqlBool($searchKey)){
                $matchtext = "$matchagainst ('" . parseBool($searchKey) . "' IN BOOLEAN MODE)";
                if ($relev) {
                    $ocrtext .= " RIGHT JOIN (SELECT productID FROM cscan_document_text_search WHERE $matchtext) AS t1 USING(productID)";
                    $matchtext = "$matchagainst ('" . unBool($searchKey) . "'";
                    if ($expans)
                        $matchtext .= ' WITH QUERY EXPANSION';
                    $matchtext .= ')';
                }
                else {
                    $where .= " $matchtext AND ";
                }
                //}
                //else{
                //	$matchtext = "$matchagainst ('".$DRW->real_escape_string($searchKey)."')";
                //	$where .= " $matchtext AND ";
                //}
                $ocrtext .= ' JOIN cscan_document_text_search dt ON(pd.productID=dt.productID)';
            } elseif ($searchType == 'ocr2' || $searchType == 'fulltext2' || $searchType == 'ocr_fulltext2') {
                if ($clear_ps) {
                    $DRW->query("DELETE FROM cscan_search_product WHERE ID=$search_id", $DRW_main);
                    $numrow = 0;
                } else {
                    $count_save_sql = "SELECT COUNT(*) FROM cscan_search_product WHERE ID=$search_id";
                    $rs = $DRW->query($count_save_sql, $DRW_read);
                    $data = $DRW->fetch_row($rs);
                    $numrow = (int) $data[0];
                }
                if ($numrow == 0 && !empty($SPHINX_name)) {
                    $searchi = 0;
                    $searches = array();
                    $searchkeys = array();
                    if ($searchType == 'ocr_fulltext2') {
                        if ($searchKey2 != '') {
                            $searches['fulltext2'] = '2';
                            $searchkeys['fulltext2'] = $searchKey2;
                        }
                        if ($searchKey != '') {
                            $searches['ocr2'] = '';
                            $searchkeys['ocr2'] = $searchKey;
                        }
                    } elseif ($searchKey != '') {
                        if ($searchType == 'fulltext2') {
                            $searches[$searchType] = '2';
                        } else {
                            $searches[$searchType] = '';
                        }
                        $searchkeys[$searchType] = $searchKey;
                    }
                    $searches_count = count($searches);
                    foreach ($searches as $st => $add) {
                        if ($search_type_and == 1 && $searches_count > 1) {
                            $searchi++;
                        }
                        $sk = $searchkeys[$st];
                        $s = startSphinx();
                        $inds = 'base_index_' . $SPHINX_name . $add . ',delta_index_' . $SPHINX_name . $add; //,base_index_'.$SPHINX_name.'stemmed'.$add.',delta_index_'.$SPHINX_name.'stemmed'.$add.'
                        if (strpos($sk, '*') !== false) {
                            $inds .= ',base_index_' . $SPHINX_name . 'star' . $add . ',delta_index_' . $SPHINX_name . 'star' . $add;
                        }
                        $ps = parseSphinx($s, $sk);
                        if (trim($ps) != '') {
                            $currcount = 0;
                            $step = $total = 1000;
                            if (!$s->setLimits(0, 1, 1)) {
                                sphinxErr(__LINE__, $s, 'setLimits');
                            }
                            foreach ($filter_range as $fr) {
                                if (!$s->setFilterRange($fr[0], $fr[1], $fr[2])) {
                                    sphinxErr(__LINE__, $s, 'setFilterRange', $fr[0] . ', ' . $fr[1] . ', ' . $fr[2]);
                                }
                            }
                            if (!$result = $s->query($ps, $inds)) {
                                sphinxErr(__LINE__, $s, 'query', $ps);
                            }
                            if (isset($result['matches'])) {
                                $total = (float) $result['total_found'];
                                $count = 0;
                                $minID = 0;
                                if ($add == '2') {
                                    $count_save_sql = "SELECT MAX(productID) FROM cscan_product_detail";
                                } else {
                                    $count_save_sql = "SELECT MAX(dts_id) FROM cscan_document_text_search";
                                }
                                $rs = $DRW->query($count_save_sql, $DRW_read);
                                $data = $DRW->fetch_row($rs);
                                $maxID = $data[0];
                                $DRW->query('START TRANSACTION', $DRW_main); //$DRW->connection($DRW_main); $DRW->begin_transaction();
                                for ($offset = 0; $offset <= $maxID; $offset+=$step) {
                                    $s = startSphinx();
                                    if (!$s->setLimits(0, $step, $step)) {
                                        sphinxErr(__LINE__, $s, 'setLimits');
                                    }
                                    foreach ($filter_range as $fr) {
                                        if (!$s->setFilterRange($fr[0], $fr[1], $fr[2])) {
                                            sphinxErr(__LINE__, $s, 'setFilterRange', $fr[0] . ', ' . $fr[1] . ', ' . $fr[2]);
                                        }
                                    }
                                    if ($minID < $maxID) {
                                        if (!$s->setIDRange($minID + 1, $maxID)) {
                                            sphinxErr(__LINE__, $s, 'setIDRange');
                                        }
                                    }
                                    if (!$result = $s->query($ps, $inds)) {
                                        sphinxErr(__LINE__, $s, 'query', $ps);
                                    }
                                    if (isset($result['matches'])) {
                                        foreach ($result['matches'] as $dts_id => $match) {
                                            if ($add == '2') {
                                                $productid = $dts_id;
                                            } else {
                                                $productid = $match['attrs']['productid'];
                                            }
                                            $query = "INSERT IGNORE INTO cscan_search_product (ID,productID,spID) VALUES ($search_id,$productid,$searchi)";
                                            $DRW->query($query, $DRW_main);
                                            $minID = $dts_id;
                                            $currcount++;
                                        }
                                        if ($currcount >= $total) {
                                            break;
                                        }
                                    }
                                    $err = $s->getLastError();
                                    $war = $s->getLastWarning();
                                    if (!empty($err) || !empty($war)) {
                                        //echo "$err | $war"; exit;
                                        break;
                                    }
                                    // note that total_found using setLimits(0,1,1) is not always the same as without limits (bug in Sphinx?)
                                    if (!isset($result['matches'])) {
                                        break;
                                    }
                                }
                                $DRW->query('COMMIT', $DRW_main); //$DRW->commit();
                            }
                        }
                    }
                    if ($search_type_and == 1 && $searches_count > 1) {
                        $sqlc = "SELECT productID,COUNT(*) AS cnt FROM cscan_search_product WHERE ID=$search_id GROUP BY productID HAVING cnt<>$searches_count";
                        $rsc = $DRW->query($sqlc, $DRW_read);
                        while ($rowc = $DRW->fetch_row($rsc)) {
                            $query = "DELETE FROM cscan_search_product WHERE ID=$search_id AND productID=$rowc[0]";
                            $DRW->query($query, $DRW_main);
                        }
                    }
                }
                $ocrtext .= ' JOIN cscan_search_product sp ON(sp.ID=' . $search_id . ' AND pd.productID=sp.productID)';
            } else {
                $matchagainst = 'MATCH(productHeadline,entryID) AGAINST'; //productName,company,secondCompany,productHeadline,entryID,incentive,searchText
                //if(isMysqlBool($searchKey)){
                $matchtext = "$matchagainst ('" . parseBool($searchKey) . "' IN BOOLEAN MODE)";
                if ($relev) {
                    $ocrtext .= " RIGHT JOIN (SELECT productID FROM cscan_product_detail WHERE $matchtext) AS t1 USING(productID)";
                    $matchtext = "$matchagainst ('" . unBool($searchKey) . "'";
                    if ($expans)
                        $matchtext .= ' WITH QUERY EXPANSION';
                    $matchtext .= ')';
                }
                else {
                    $where .= " $matchtext AND ";
                }
                //}
                //else{
                //	$matchtext = "$matchagainst ('".$DRW->real_escape_string($searchKey)."')";
                //	$where .= " $matchtext AND ";
                //}
            }
        }
        if ($where != '') {
            $where = ' WHERE ' . substr($where, 0, -5);
        }
    } else {
        $ojoin .= ',cscan_product_basket cb';
        $where = ' WHERE basket_id=' . $bid . ' AND userID=' . $_SESSION['sess_userID'] . ' AND cb.productID=pd.productID ';
        $saved = 1;
    }
    $selectQuery = "SELECT "; // SQL_NO_CACHE
    if ($docount) {
        $selectQuery .= "COUNT(DISTINCT pd.productID)";
        $sortby = '';
    } else {
        if ($dograph != 0) {
            $sortby = '';
        }

        $matchtext = ($matchtext != '') ? ",$matchtext AS relevancy" : ",1 AS relevancy";

        $mintel = new \HS\Mintel();
        $mintel_set = $mintel->getFields();
        $mintel_set_2 = $mintel->getFieldSet('incentive_set_2');
        $mintel_set_3 = $mintel->getFieldSet('incentive_set_3');
        $incentive_set = implode(',', array_merge(array_keys($mintel_set), array_keys($mintel_set_2), array_keys($mintel_set_3)));
        // Old Query 27-jan-2017
        /* 	$selectQuery .= "DISTINCT pd.productID AS theproductID,mChannelID,mPanelID,productHeadline,sectorID,categoryID,subCategoryID,entryID,
          addedToDatabase,company,productName,compaignLanguage,firstSeen,lastSeen,mTypeID,state,agentCommunicationID,secondCompany,
          variantID,affinityAssociation,age,gender,incomeID,publication,isVariant,isDemographic,isInsight,fa_ids,tl_ids,isFICO,
          incentive_ongoing,incentive,$incentive_set,
          delmethid,responseMechID,FeeProductType,external_updates,external_fans,external_link,prescription,is_hphsa,subSubCategoryID,
          OfferExpiryDate,is_citi,riders,is_prescreen,isSurvey,IssueTypeID,traffic_sources,social_media_name,worksiteVoluntary,groupSize$matchtext";

         */

        // New Query
        $selectQuery .= "distinct pd.productID AS theproductID,mChannelID,mPanelID,productHeadline,sectorID,categoryID,subCategoryID,entryID,
            addedToDatabase,company,productName,compaignLanguage,firstSeen,lastSeen,mTypeID,state,agentCommunicationID,secondCompany,
            variantID,affinityAssociation,age,gender,incomeID,publication,isVariant,isDemographic,isInsight,fa_ids,tl_ids,isFICO,
            incentive_ongoing,incentive,$incentive_set,
            delmethid,responseMechID,FeeProductType,external_updates,external_fans,external_link,prescription,is_hphsa,subSubCategoryID,
            OfferExpiryDate,is_citi,riders,is_prescreen,isSurvey,IssueTypeID,traffic_sources,social_media_name,worksiteVoluntary,groupSize$matchtext" . " from (select distinct pd.productID,pd.entryID_sort1,pd.entryID_sort2 ";
    }
    // Old Query 27-jan-2017
    /* $selectQuery .= " FROM cscan_product_detail pd$ocrtext$pjoin$cjoin$ccjoin$afjoin$affjoin$dmajoin$edcjoin$sect_j$pcjoin$bjoin$ejoin$mljoin$rjoin$tljoin$ajoin$sjoin$ojoin $where$owhere $sortby"; */
    // New Query 



    if ($docount) {
        $selectQuery .= " FROM cscan_product_detail pd$ocrtext$pjoin$cjoin$ccjoin$afjoin$affjoin$dmajoin$edcjoin$sect_j$pcjoin$bjoin$ejoin$mljoin$rjoin$tljoin$ajoin$sjoin$ojoin $where$owhere $sortby";
        //echo $countQuery;exit;
    } else {
        if ($orderby != false) {
            $selectQuery .= " FROM cscan_product_detail pd$ocrtext$pjoin$cjoin$ccjoin$afjoin$affjoin$dmajoin$edcjoin$sect_j$pcjoin$bjoin$ejoin$mljoin$rjoin$tljoin$ajoin$sjoin$ojoin $where$owhere $sortby" . " $orderby  limit $limit1,$limit2)A,cscan_product_detail pd,cscan_scsc_product as scsc where A.productID=pd.productID and pd.productID=scsc.productID";
        } else {
            $selectQuery .= " FROM cscan_product_detail pd$ocrtext$pjoin$cjoin$ccjoin$afjoin$affjoin$dmajoin$edcjoin$sect_j$pcjoin$bjoin$ejoin$mljoin$rjoin$tljoin$ajoin$sjoin$ojoin $where$owhere $sortby" . " limit $limit1,$limit2)A,cscan_product_detail pd,cscan_scsc_product as scsc where A.productID=pd.productID and pd.productID=scsc.productID";
        }
    }



    if ($dograph != 0) {
        $field = getDoGraph($dograph);
        $selectQuery = "SELECT COUNT($field) AS field_count,$field AS field_name FROM ($selectQuery) as t1 GROUP BY $field"; // SQL_NO_CACHE ORDER BY field_count DESC,field_name ASC
    }

    return array($selectQuery, $saved);
}

function doQuerySortNew($sort) {
    $dorelev = false;
    $doexpans = false;
    switch ($sort) {
        case 1:
            $orderby = ' ORDER BY productHeadline ASC, entryID_sort1 DESC, entryID_sort2 DESC';
            break;
        case -1:
            $orderby = ' ORDER BY productHeadline DESC, entryID_sort1 ASC, entryID_sort2 ASC';
            break;
        case 2:
            $orderby = ' ORDER BY company ASC, entryID_sort1 DESC, entryID_sort2 DESC';
            break;
        case -2:
            $orderby = ' ORDER BY company DESC, entryID_sort1 ASC, entryID_sort2 ASC';
            break;
        case 3:
            $orderby = ' ORDER BY pd.entryID_sort1 ASC, pd.entryID_sort2 ASC';
            break;
        case -3:
            $orderby = ' ORDER BY pd.entryID_sort1 DESC, pd.entryID_sort2 DESC';
            break;
        case 4:
            $orderby = ' ORDER BY relevancy ASC,entryID_sort1 ASC, entryID_sort2 ASC';
            $dorelev = true;
            break;
        case -4:
            $orderby = ' ORDER BY relevancy DESC,entryID_sort1 DESC, entryID_sort2 DESC';
            $dorelev = true;
        case 5:
            $orderby = ' ORDER BY relevancy ASC,entryID_sort1 ASC, entryID_sort2 ASC';
            $dorelev = true;
            $doexpans = true;
            break;
        case -5:
            $orderby = ' ORDER BY relevancy DESC,entryID_sort1 DESC, entryID_sort2 DESC';
            $dorelev = true;
            $doexpans = true;
            break;
        case 6:
            $orderby = ' ORDER BY isDemographic DESC,entryID_sort1 DESC, entryID_sort2 DESC';
            break;
        case -6:
            $orderby = ' ORDER BY isDemographic ASC,entryID_sort1 ASC, entryID_sort2 ASC';
            break;
        case 7:
            $orderby = ' ORDER BY isVariant DESC,entryID_sort1 DESC, entryID_sort2 DESC';
            break;
        case -7:
            $orderby = ' ORDER BY isVariant ASC,entryID_sort1 ASC, entryID_sort2 ASC';
            break;
        case 8:
            $orderby = ' ORDER BY isInsight DESC,entryID_sort1 DESC, entryID_sort2 DESC';
            break;
        case -8:
            $orderby = ' ORDER BY isInsight ASC,entryID_sort1 ASC, entryID_sort2 ASC';
            break;
        case 9:
            $orderby = ' ORDER BY isFICO DESC,entryID_sort1 DESC, entryID_sort2 DESC';
            break;
        case -9:
            $orderby = ' ORDER BY isFICO ASC,entryID_sort1 ASC, entryID_sort2 ASC';
            break;
        case 10:
            $orderby = ' ORDER BY isSurvey DESC,entryID_sort1 DESC, entryID_sort2 DESC';
            break;
        case -10:
            $orderby = ' ORDER BY isSurvey ASC,entryID_sort1 ASC, entryID_sort2 ASC';
            break;
        default:
            $orderby = '';
    }
    return array($orderby, $dorelev, $doexpans);
}

function getKeywords($search_id,$searchTable='') {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    $searchcondition=  "cscan_search WHERE ID ";
    if($searchTable!=''){
        $searchcondition=  " cscan_search_activity WHERE activity_id ";
    }
    $savedQ = "SELECT searchKey,searchType,searchOption,mChannelID,sectorID,mPanelID,
				addedToDatabase,month1,month2,sort,company,productName,incentive,categoryID,mTypeID,subCategoryID,cardStatus,personalization,
				gender,age,state,worksiteVoluntary,agentCommunicationID,groupSize,offerOrigin,enhance,saved,compaignLanguage,affinityAssociation,income_mult,fa_id_mult,tl_id_mult,
				siteCatID_mult,pubTypeID_mult,approved_date,electronicID_mult,DMA_ID_mult,businessContent_mult,delmethid_mult,affinity_association,prescription,AffinityCategoryID_mult,is_affinion,is_military,
				ApplicationType_mult,is_multicultural,IntroPricing_mult,is_rewards,RewardsProgramEmphasis_mult,is_incentive,responseMechID_mult,multiculturalmarkets_mult,CardNetwork_mult,FeeProduct,external_link,FeeProductType,approved_date_to,
				search_competi_id,ca_related,searchKey2,search_type_and,search_rules,is_mover,scsc_primary,OptOutFirmOffer,riders_mult,is_hphsa,subSubCategoryID,Income_Producing_Assets_Segment_Code_mult,cg_id,is_citi,is_CreditCardMentioned,
				spanelist_filter,edc_id_mult,AffinitySubCategoryID_mult,ERateType_mult,EOfferPrice_mult,ETermLength_mult,is_ECancelFee,IssueTypeID_mult,pcountry,is_Reloadable,creditUnion,is_mobile,value_score,refinance,jumbo_ncnfg,va,fha,conventional,usda,correspondent_lending,faux_check,minmaxmortgage,socialmedia_adtype,publication_name,deliveryTypeId,postageId,presortedId,packageTypeId,fico_score,credit_vision_score,vantage_score,sender_domain_name 
				FROM $searchcondition ='" . $search_id . "'";
  
    /* $savedQ = "SELECT searchKey,searchType,searchOption,mChannelID,sectorID,mPanelID,
				addedToDatabase,month1,month2,sort,company,productName,incentive,categoryID,mTypeID,subCategoryID,cardStatus,personalization,
				gender,age,state,worksiteVoluntary,agentCommunicationID,groupSize,offerOrigin,enhance,saved,compaignLanguage,affinityAssociation,income_mult,fa_id_mult,tl_id_mult,
				siteCatID_mult,pubTypeID_mult,approved_date,electronicID_mult,DMA_ID_mult,businessContent_mult,delmethid_mult,affinity_association,prescription,AffinityCategoryID_mult,is_affinion,is_military,
				ApplicationType_mult,is_multicultural,IntroPricing_mult,is_rewards,RewardsProgramEmphasis_mult,is_incentive,responseMechID_mult,multiculturalmarkets_mult,CardNetwork_mult,FeeProduct,external_link,FeeProductType,approved_date_to,
				search_competi_id,ca_related,searchKey2,search_type_and,search_rules,is_mover,scsc_primary,OptOutFirmOffer,riders_mult,is_hphsa,subSubCategoryID,Income_Producing_Assets_Segment_Code_mult,cg_id,is_citi,is_CreditCardMentioned,
				spanelist_filter,edc_id_mult,AffinitySubCategoryID_mult,ERateType_mult,EOfferPrice_mult,ETermLength_mult,is_ECancelFee,IssueTypeID_mult,pcountry,is_Reloadable,creditUnion
				FROM cscan_search WHERE ID='" . $search_id . "'";
     
     */
    $rs = $DRW->query($savedQ, $DRW_read);
    $data = $DRW->fetch_row($rs);
    $searchKey = trim($data[0]);
    $searchType = $data[1];
    $searchOption = $data[2];
    $mChannelID = $data[3];
    $sectorID = $data[4];
    $mPanelID = $data[5];
    $addedToDatabase = $data[6];
    $month1 = $data[7];
    $month2 = $data[8];
    $sort = $data[9];
    $company = $data[10];
    $productName = $data[11];
    $incentive = $data[12];
    $categoryID = $data[13];
    $mTypeID = $data[14];
    $subCategoryID = $data[15];
    $cardStatus = $data[16];
    $personalization = $data[17];
    $gender = $data[18];
    $age = $data[19];
    $state = $data[20];
    $worksiteVoluntary = $data[21];
    $agentCommunicationID = $data[22];
    $groupSize = $data[23];
    $offerOrigin = $data[24];
    $enhance = $data[25];
    $saved = $data[26];
    $compaignLanguage = $data[27];
    $affinityAssociation = $data[28];
    $income_mult = $data[29];
    $fa_id_mult = $data[30];
    $tl_id_mult = $data[31];
    $siteCatID_mult = $data[32];
    $pubTypeID_mult = $data[33];
    $approved_date = $data[34];
    $electronicID_mult = $data[35];
    $DMA_ID_mult = $data[36];
    $businessContent_mult = $data[37];
    $delmethid_mult = $data[38];
    $affinity_association = $data[39];
    $prescription = $data[40];
    $AffinityCategoryID_mult = $data[41];
    $is_affinion = $data[42];
    $is_military = $data[43];
    $ApplicationType_mult = $data[44];
    $is_multicultural = $data[45];
    $IntroPricing_mult = $data[46];
    $is_rewards = $data[47];
    $RewardsProgramEmphasis_mult = $data[48];
    $is_incentive = $data[49];
    $responseMechID_mult = $data[50];
    $multiculturalmarkets_mult = $data[51];
    $CardNetwork_mult = $data[52];
    $FeeProduct = $data[53];
    $external_link = $data[54];
    $FeeProductType = $data[55];
    $approved_date_to = $data[56];
    $search_competi_id = $data[57];
    $ca_related = $data[58];
    $searchKey2 = $data[59];
    $search_type_and = $data[60];
    $search_rules = $data[61];
    $is_mover = $data[62];
    $scsc_primary = $data[63];
    $OptOutFirmOffer = $data[64];
    $riders_mult = $data[65];
    $is_hphsa = $data[66];
    $subSubCategoryID = $data[67];
    $Income_Producing_Assets_Segment_Code = $data[68];
    $cg_id = $data[69];
    $is_citi = $data[70];
    $is_CreditCardMentioned = $data[71];
    $spanelist_filter = $data[72];
    $edc_id_mult = $data[73];
    $AffinitySubCategoryID_mult = $data[74];
    $ERateType_mult = $data[75];
    $EOfferPrice_mult = $data[76];
    $ETermLength_mult = $data[77];
    $is_ECancelFee = $data[78];
    $IssueTypeID_mult = $data[79];
    $pcountry = $data[80];
    $is_Reloadable = $data[81];
    $creditUnion = $data[82];
     $is_mobile= $data[83];
    $value_score= $data[84];
    $refinance = $data[85];
    $jumbo_ncnfg = $data[86];
    $va = $data[87];
    $fha = $data[88];
    $conventional = $data[89];
    $usda = $data[90];
    $correspondent_lending=$data[91];
    $faux_check=$data[92];
    $minmaxmortgage=$data[93];
    $socialmedia_adtype=$data[94];
    $publication_name=$data[95];
     ##############################Start Envelope/Postage Data Fields##############
    $deliveryTypeId=$data[96];
    $postageId=$data[97];
    $presortedId=$data[98];
    $packageTypeId=$data[99];
     ############################## End Envelope/Postage Data Fields##############
     
    ############################## Start FICO/Vantage/Credit Vision Score Fields ##############
    
    $fico_score=$data[100];
    $credit_vision_score=$data[101];
    $vantage_score=$data[102];
    
    //if(ENV == 'localhost' || ENV == 'demo.competiscan.com'){
			$sender_domain_name=$data[103];
   // }
    
    ############################## End FICO/Vantage/Credit Vision Score Fields ##############
    
    $displayKeywords = '';
    if ($searchKey != '') {
        $displayKeywords .= ' ' . htmlspecialchars($searchKey) . ' ';
    }
    if ($searchKey2 != '') {
        $displayKeywords .= ' ' . htmlspecialchars($searchKey2) . ' '; //search_type_and
    }
    $nameField = array();
    $nameField[] = array('Media Channel', explode(',', $mChannelID), 'mediaChannelName');
    $nameField[] = array('Sector', explode(',', $sectorID), 'sectorName');
    $nameField[] = array('Audience', explode(',', $mPanelID), 'mediaPanelName');
    if ($month1 != '' || $month2 != '') {
        $monthArray = get_monthArray();
        $month1 = $monthArray[(int) substr($month1, 5)] . ' ' . substr($month1, 0, 4);
        $month2 = $monthArray[(int) substr($month2, 5)] . ' ' . substr($month2, 0, 4);
        $nameField[] = array('Added to database', array('Between ' . $month1 . ' and ' . $month2), '');
    } else {
        $atd = get_addedToDatabaseArray();
        if (isset($atd[$addedToDatabase])) {
            $addedToDatabase = $atd[$addedToDatabase];
        }
        $nameField[] = array('Added to database', array($addedToDatabase), '');
    }
    if ($approved_date == '0000-00-00' || $approved_date == '0000-00-00 00:00:00') {
        $approved_date = '';
    }
    if ($approved_date_to == '0000-00-00' || $approved_date_to == '0000-00-00 00:00:00') {
        $approved_date_to = '';
    }
    if (!empty($approved_date) && !empty($approved_date_to)) {
        $nameField[] = array('Approved Date', array('Between ' . date('Y-m-d g:i A', strtotime($approved_date)) . ' and ' . date('Y-m-d g:i A', strtotime($approved_date_to))), '');
    }
    $nameField[] = array('Panelists', array($spanelist_filter), 'subPanelistFilterName');
    if (!empty($search_competi_id)) {
        $vs = explode(',', $search_competi_id);
        $competi_ids = array();
        foreach ($vs as $v) {
            $competi_ids[] = "'" . $DRW->real_escape_string(trim($v)) . "'";
        }
        $cos = array();
        $resultC = $DRW->query("SELECT pm_date,pm.stateID1,pm.stateID2,pm.postalcode1,pm.postalcode2 FROM cscan_panelists_mover pm join cscan_panelists pa on (pm.panelist_id=pa.panelist_id) WHERE competi_id IN (" . implode(',', $competi_ids) . ") ORDER BY competi_id ASC,pm_date ASC", $DRW_read);
        while ($dataC = $DRW->fetch_row($resultC)) {
            $cos[] = $dataC[0] . ' [' . stateName($dataC[1]) . ' ' . $dataC[3] . ' > ' . stateName($dataC[2]) . ' ' . $dataC[4] . ']';
        }
        $nameField[] = array('Panelist ID', array(preg_replace('/\\s*,\\s*/', ', ', $search_competi_id)), '');
        $nameField[] = array('Move History', $cos, '');
    }
    if (!empty($pcountry)) {
        $nameField[] = array('Country', array($pcountry), '');
    }
    if (!empty($cg_id)) {
        $nameField[] = array('Panelist Filter', explode(',', $cg_id), 'panelistFilterName');
    }
    $nameField[] = array('Company', array($company), '');
    $nameField[] = array('Affinity/Association', array($affinity_association), '');
    if ($ca_related) {
        $nameField[] = array('Related', array('Related'), '');
    }
    $nameField[] = array('Product', array($productName), '');
    $nameField[] = array('Category', explode(',', $categoryID), 'categoryName');
    $nameField[] = array('Sub Category', explode(',', $subCategoryID), 'subCategoryName');
    if ($scsc_primary) {
        $nameField[] = array('Primary', array('Primary'), '');
    }
    $nameField[] = array('Sub Sub Category', explode(',', $subSubCategoryID), 'subCategoryName');
    $nameField[] = array('Application Type', explode(',', $ApplicationType_mult), 'getApplicationType');
    $nameField[] = array('Introductory Pricing', explode(',', $IntroPricing_mult), 'getIntroPricing');
    $nameField[] = array('Issue Type', explode(',', $IssueTypeID_mult), 'getIssueType');
    $nameField[] = array('Mailing Type', explode(',', $mTypeID), 'mediaType');
    $nameField[] = array('Personalization', array($personalization), 'get_personalizedName');
    $nameField[] = array('Business Content', explode(',', $businessContent_mult), 'get_businessContentName');
    $nameField[] = array('DMA', explode(',', $DMA_ID_mult), ''); //get_DMAName
    $nameField[] = array('EDC / LDC / TDSP', explode(',', $edc_id_mult), 'get_EDCName');
    $nameField[] = array('Gender', array($gender), '');
    $nameField[] = array('State/Province', explode(',', $state), 'stateName');
    $nameField[] = array('Age', explode(',', $age), 'getAgeName');
    $nameField[] = array('Income', explode(',', $income_mult), 'getIncomeName');
    $nameField[] = array('Income Producing Assets Segment Code', explode(',', $Income_Producing_Assets_Segment_Code), 'getIncomeIPASC');
    $nameField[] = array('Worksite/Voluntary', array($worksiteVoluntary), 'get_worksiteName');
    $nameField[] = array('Credit Union', array($creditUnion), 'get_creditUnionName');
    $nameField[] = array('Affinity/Association', array($affinityAssociation), 'get_affinityName');
    $nameField[] = array('Affinity/Association Category', explode(',', $AffinityCategoryID_mult), 'getAffinityCategoryName');
    $nameField[] = array('Affinity/Association Sub-Category', explode(',', $AffinitySubCategoryID_mult), 'getAffinityCategoryName');
    if ($is_rewards) {
        $nameField[] = array('Rewards', array('Rewards'), '');
        $nameField[] = array('Rewards Program Emphasis', explode(',', $RewardsProgramEmphasis_mult), 'getRewardsProgramEmphasis');
    }
    if ($is_incentive) {
        $nameField[] = array('Incentive', array('Incentive'), '');
    }
    if ($is_military) {
        $nameField[] = array('Military', array('Military'), '');
    }
    if ($prescription) {
        $nameField[] = array('Rx', array('Rx'), '');
    }
    if ($is_affinion) {
        $nameField[] = array('Affinion', array('Affinion'), '');
    }
    if ($is_mover) {
        $nameField[] = array('Mover', array('Mover'), '');
    }
    if ($is_CreditCardMentioned) {
        $nameField[] = array('Credit Card Mentioned', array('Credit Card Mentioned'), '');
    }
    if ($is_hphsa) {
        $nameField[] = array('CDHP/HDHP/HSA', array('CDHP/HDHP/HSA'), '');
    }
    if ($OptOutFirmOffer) {
        $nameField[] = array('Pre-Screen/Opt-Out', array('Pre-Screen/Opt-Out'), '');
    }
    if ($is_citi) {
        $nameField[] = array('Retail Card Study', array($is_citi), 'get_isCitiName');
    }
    if ($is_multicultural) {
        if ($multiculturalmarkets_mult == '') {
            $nameField[] = array('Target Markets', array('Target Markets'), '');
        } else {
            $nameField[] = array('Target Markets', array($multiculturalmarkets_mult), 'get_TMName');
        }
    }
    if ($FeeProduct) {
        $nameField[] = array('Fee Product', array($FeeProduct), 'get_FeeProductName');
    }
    if($faux_check){
        $nameField[] = array('Faux Check', array('Faux Check'), ''); 
    }
    ###################### For Social Media Ad Type selection #################### 
    if($socialmedia_adtype){
        if($socialmedia_adtype=='1'){
            $socialmedia_adtypeval='Sponsored';
        }else if($socialmedia_adtype=='2'){
            $socialmedia_adtypeval='Corporate';
        }
        $nameField[] = array('Social Media Ad Type', array($socialmedia_adtypeval), ''); 
    }
   ###################### For Social Media Ad Type selection #################### 
    if(!empty($minmaxmortgage) && $minmaxmortgage!='0-2000000'){
        $minmaxarray    =  explode("-",$minmaxmortgage);
        $minloanamount  =  $minmaxarray[0];
        $maxloanamount  =  $minmaxarray[1];
        $nameField[] = array('Minimum Loan Amount ($)', array($minloanamount), ''); 
        $nameField[] = array('Maximum Loan Amount ($)', array($maxloanamount), ''); 
    }
    
    $nameField[] = array('Ancillary Products', explode(',', $FeeProductType), 'getFeeProductTypeName');
    $mortgageVal = implode(",", [$refinance,$jumbo_ncnfg,$va,$fha,$conventional,$usda,$correspondent_lending]);
    $nameField[] = [0=>'General Mortgage & Loan Details',1=>[$mortgageVal],2=>'gMortgageLoan'];
    $nameField[] = array('Riders', explode(',', $riders_mult), 'getriders');
    $nameField[] = array('Communication Type', explode(',', $agentCommunicationID), 'agentName');
    $nameField[] = array('Delivery Method', explode(',', $delmethid_mult), 'getDelMeth');
    ##############################Start Envelope/Postage Data Fields##############
    $nameField[] = array('Delivery Type', explode(',', $deliveryTypeId), 'getDelType');
    $nameField[] = array('Postage', explode(',', $postageId), 'getPostageName');
    $nameField[] = array('Pre-Sorted', explode(',', $presortedId), 'getPresortedName');
    $nameField[] = array('Package Type', explode(',', $packageTypeId), 'getPackageName');
    ##############################End Envelope/Postage Data Fields##############
    $nameField[] = array('Electronic Type', explode(',', $electronicID_mult), 'get_EleName');
    $nameField[] = array('Publication Type', explode(',', $pubTypeID_mult), 'get_PubName');
    // Added for publication name
    $nameField[] = array('Publication Name', array($publication_name), '');
    // End for publication name
    
    $nameField[] = array('Site Category', explode(',', $siteCatID_mult), 'get_SiteName');
    $nameField[] = array('Group Size', explode(',', $groupSize), 'get_groupSizeName');
    $nameField[] = array('Offer Origin', array($offerOrigin), 'get_offerOriginName');
    $nameField[] = array('Campaign Language', array($compaignLanguage), '');
    $nameField[] = array('Energy - Rate Type', explode(',', $ERateType_mult), 'getERateType');
    $nameField[] = array('Offer Price (¢ per kWh)', explode(',', $EOfferPrice_mult), 'getEOfferPrice');
    $nameField[] = array('Energy - Term Length', explode(',', $ETermLength_mult), 'getETermLength');
    if ($is_ECancelFee) {
        $nameField[] = array('Cancel Fee', array('Cancel Fee'), '');
    }
    $nameField[] = array('Face Amount', explode(',', $fa_id_mult), 'getFaceAmountName');
    $nameField[] = array('Term Length', explode(',', $tl_id_mult), 'getTermLengthName');
    $nameField[] = array('Response Mechanism', explode(',', $responseMechID_mult), 'getresponseMechID');
    $nameField[] = array('Card Network', explode(',', $CardNetwork_mult), 'getCardNetwork');
    if ($is_Reloadable) {
        $nameField[] = array('Reloadable', array('Reloadable'), '');
    }
    $nameField[] = array('Network Name', array($external_link), '');
    
    
     if($is_mobile==1){
         $nameField[] = array('Device', array('Only Desktop'), '');
    }if($is_mobile==2){
         $nameField[] = array('Device', array('Only Mobile'), '');
    }
    
    if($value_score!=''){
        $nameField[] = array('Value Score', array($value_score), '');
    }
    
    ############################## Start FICO/CreditVision/Vantage Score Fields ##############
    
    if($fico_score!='' && !empty($fico_score)){
       //echo  $check_host=siteMode(); die;
        //if($check_host='demo'){
            
        //}
        //$nameField[] = array('FICO Score', array($fico_score), '');
        $nameField[] = array('FICO Score', explode(',', $fico_score), 'getScoreRange');
    }
    if($credit_vision_score!='' && !empty($credit_vision_score)){
        //$nameField[] = array('CreditVision Score', array($credit_vision_score), '');
        $nameField[] = array('CreditVision Score', explode(',', $credit_vision_score), 'getScoreRange');
    }
    if($vantage_score!='' && !empty($vantage_score)){
        //$nameField[] = array('Vantage Score', array($vantage_score), '');
        $nameField[] = array('Vantage Score', explode(',', $vantage_score), 'getScoreRange');
    }
    //if(ENV == 'localhost' || ENV == 'demo.competiscan.com'){
        if($sender_domain_name!='' && !empty($sender_domain_name)){
            $nameField[] = array('Sender Domain Name', explode(',', $sender_domain_name), '');
        } 
   // }
          
    
    ############################## End FICO/CreditVision/Vantage Score Fields ##############
    
    foreach ($nameField as $a) {
        list($title, $vals, $func) = $a;
        $skipcomma = true;
        $temptext = '';
        foreach ($vals as $v) {
            $v = trim($v);
            if ($v != '') {
                if ($func != '') {
                    $tmp = $func($v);
                } else {
                    $tmp = $v;
                }
                if ($tmp != '') {
                    if (!$skipcomma) {
                        $temptext .= ', ';
                    } else {
                        $skipcomma = false;
                    }
                    $temptext .= htmlspecialchars($tmp);
                }
            }
        }
        if ($temptext != '') {
            $displayKeywords .= ' <strong>' . $title . ':</strong> ' . $temptext;
        }
    }
    $wordArray = array();
    $keyArray = preg_split('/\\s+/', $searchKey, -1, PREG_SPLIT_NO_EMPTY);
    foreach ($keyArray as $value) {
        $tmp = preg_replace('/\\W/', '', strtolower($value));
        if ($tmp != 'and' && $tmp != 'or' && $tmp != 'not' && strlen($tmp) > 2) {
            $wordArray[] = $tmp;
        }
    }
    return array($displayKeywords, $wordArray);
}


function sphinxErr($ln = null, $s = null, $str = null, $str2 = null) {
    print "<!--sphinxErr";
    if ($ln)
        print "; on line $ln";
    if ($str)
        print "; $str";
    if ($str2)
        print "; $str2";
    if ($s)
        print "; err=" . $s->getLastError() . "; war=" . $s->getLastWarning();
    print "-->\n";
    //die;
}

function startSphinx($filter = 'dts_active', $filterval = array(1)) {
    global $SPHINX_server, $SPHINX_port;
    //echo "okname".$SPHINX_server."oktestnow".$SPHINX_port;
    if (empty($SPHINX_server)) {
        $SPHINX_server = 'localhost';
    }
    if (empty($SPHINX_port)) {
        $SPHINX_port = 9312;
    }
    if (!$s = new SphinxClient()) {
        sphinxErr(__LINE__, $s, 'SphinxClient');
    }
    if (!$s->setServer($SPHINX_server, $SPHINX_port)) {
        sphinxErr(__LINE__, $s, 'setServer');
    }
    if (!$s->setMatchMode(SPH_MATCH_EXTENDED2)) {
        sphinxErr(__LINE__, $s, 'setMatchMode');
    }
    if (!$s->setRankingMode(SPH_RANK_NONE)) {
        sphinxErr(__LINE__, $s, 'setRankingMode');
    }
    if (!$s->setFilter($filter, $filterval)) {
        sphinxErr(__LINE__, $s, 'setFilter');
    }
    if (!$s->setSortMode(SPH_SORT_EXTENDED, '@id ASC')) {
        sphinxErr(__LINE__, $s, 'setSortMode');
    }
    #echo $s->setGroupBy('productID',SPH_GROUPBY_ATTR);
    return $s;
}

function parseSphinx(&$s, $searchKey) {
    $searchKey = trim($searchKey);
    $searchKey = preg_replace('/(\\(|\\)|")/', ' $1 ', $searchKey);
    $keyArray = preg_split('/\\s+/', $searchKey, -1, PREG_SPLIT_NO_EMPTY);
    $spliti = -1;
    $inquote = 0;
    $innot = false;
    $splitArray = array();
    for ($i = 0; $i < count($keyArray); $i++) {
        $value = $keyArray[$i];
        $count = count($splitArray);
        $evalue = '';
        if ($value == '"') {
            if ($inquote > 0) {
                $inquote = 0;
            } else {
                $inquote = 1;
                if ($count > 0 && !$innot) {
                    $evalue .= ' ';
                }
                $innot = false;
            }
            $evalue .= $value;
        } else {
            if ($count > 0 && $inquote != 1 && !$innot) {
                $evalue .= ' ';
            }
            $innot = false;
            if ($inquote == 0) {
                $lvalue = strtolower($value);
                if ($value == '(' || $value == ')') {
                    $evalue .= $value;
                } elseif ($lvalue == 'not') {
                    $evalue .= '-';
                    $innot = true;
                } elseif ($lvalue == 'and') {
                    //$evalue .= '&';
                } elseif ($lvalue == 'or') {
                    $evalue .= '|';
                } elseif ($lvalue == 'within' && $count > 0 && $keyArray[$i - 1] == '"' && isset($keyArray[$i + 1]) && is_numeric($keyArray[$i + 1])) {
                    //remove space
                    $evalue = substr($evalue, 0, -1) . '~' . $keyArray[$i + 1];
                    $i++;
                } else {
                    $evalue .= $s->escapeString(preg_replace('/[^a-zA-Z0-9_*]+/', ' ', $value));
                }
            } else {
                $inquote++;
                $evalue .= $s->escapeString(preg_replace('/[^a-zA-Z0-9_*]+/', ' ', $value));
            }
        }
        $splitArray[] = $evalue;
    }
    $keyArray = $splitArray;
    $searchKey = implode('', $keyArray);
    return $searchKey;
}

function deleteSphinx($productArray, $documentArray = array()) {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    global $SPHINX_ids;

    $dts_ids = array();
    foreach ($productArray as $k => $productID) {
        if (is_array($documentArray) && isset($documentArray[$k])) {
            $addtext = ' AND document_id=' . $documentArray[$k];
        } else {
            $addtext = '';
        }
        $query2 = "SELECT dts_id FROM cscan_document_text_search WHERE productID=$productID$addtext";
        $query_result2 = $DRW->query($query2, $DRW_read);
        while ($data2 = $DRW->fetch_row($query_result2)) {
            $dts_ids[] = $data2[0];
        }
    }
    if (count($SPHINX_ids) == 0) {
        deleteSphinxAll($productArray, $documentArray, 1, 0, implode(',', $dts_ids));
    } else {
        foreach ($SPHINX_ids as $s => $a) {
            foreach ($a as $k => $v) {
                if ($k == 'src') {
                    deleteSphinxAll($productArray, $documentArray, 1, $v, implode(',', $dts_ids));
                    break;
                }
            }
        }
    }
}

function deleteSphinx2($productArray) {
    global $SPHINX_ids;
    if (count($SPHINX_ids) == 0) {
        deleteSphinxAll($productArray, array(), 2, 0);
    } else {
        foreach ($SPHINX_ids as $s => $a) {
            foreach ($a as $k => $v) {
                if ($k == 'src2') {
                    deleteSphinxAll($productArray, array(), 2, $v);
                    break;
                }
            }
        }
    }
}

function deleteSphinxAll($productArray, $documentArray = false, $delete_sphinx_type = 1, $counter_id = 0, $dts_ids = '') {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;

    foreach ($productArray as $k => $productID) {
        if (isset($documentArray[$k])) {
            $document_id = $documentArray[$k];
        } else {
            $document_id = 0;
        }
        $query2 = "SELECT productID,dts_ids FROM cscan_sphinx_delete WHERE productID=$productID AND document_id=$document_id AND counter_id=$counter_id";
        $query_result2 = $DRW->query($query2, $DRW_read);
        $data2 = $DRW->fetch_row($query_result2);

        if (empty($data2[0])) {
            if ($delete_sphinx_type == 2) {
                $delete_sphinx = 0;
                $delete_sphinx2 = 1;
            } else {
                $delete_sphinx = 1;
                $delete_sphinx2 = 0;
            }
            $query = "INSERT INTO cscan_sphinx_delete (productID,document_id,delete_sphinx,delete_sphinx2,counter_id,dts_ids) VALUES ($productID,$document_id,$delete_sphinx,$delete_sphinx2,$counter_id,'" . $DRW->real_escape_string($dts_ids) . "')";
            $DRW->query($query, $DRW_main);
        } else {
            $old_dts_ids = $data2[1];
            if ($delete_sphinx_type == 2) {
                $delete_sphinx_txt = 'delete_sphinx2=1';
            } else {
                $delete_sphinx_txt = 'delete_sphinx=1';
            }
            if (!empty($dts_ids)) {
                if (!empty($old_dts_ids)) {
                    $dts_ids .= ',' . $old_dts_ids;
                }
                $delete_sphinx_txt .= ",dts_ids='" . $DRW->real_escape_string($dts_ids) . "'";
            }
            $query = "UPDATE cscan_sphinx_delete SET $delete_sphinx_txt WHERE productID=$productID AND document_id=$document_id AND counter_id=$counter_id";
            $DRW->query($query, $DRW_main);
        }
    }
}

function getTimes($start = 0, $end = 23) {
    $timeArray = array();
    for ($i = $start; $i <= $end; $i++) {
        $t = str_pad($i, 2, '0', STR_PAD_LEFT);
        $t3 = 'AM';
        if ($i < 1) {
            $t1 = 12;
        } elseif ($i > 12) {
            $t1 = $i - 12;
            $t3 = 'PM';
        } else {
            if ($i == 12) {
                $t3 = 'PM';
            }
            $t1 = $i;
        }
        for ($j = 0; $j < 60; $j+=15) {
            $t2 = str_pad($j, 2, '0', STR_PAD_LEFT);
            $timeArray[$t . ':' . $t2 . ':00'] = $t1 . ':' . $t2 . ' ' . $t3;
        }
    }
    return $timeArray;
}

function rmDirFiles($dirPath) {
    if (file_exists($dirPath)) {
        if ($handle = opendir($dirPath)) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != "..") {
                    if (is_dir($dirPath . '/' . $file)) {
                        //rmDirFiles($dirPath.'/'.$file);
                    } elseif (is_file($dirPath . '/' . $file)) {
                        @unlink($dirPath . '/' . $file);
                    }
                }
            }
            closedir($handle);
        }
    }
    @rmdir($dirPath);
}

function makeCacheable($time, $expire_days = 1) {
    $gmt = gmdate('D, d M Y H:i:s \G\M\T', $time);
    header('Cache-Control: public');
    header('Pragma: public');
    header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + (86400 * $expire_days)));
    $headers = apache_request_headers();
    if (isset($headers['If-Modified-Since'])) {
        $if_modified_since = preg_replace('/;.*$/', '', $headers['If-Modified-Since']);
        if ($if_modified_since == $gmt) {
            header("HTTP/1.1 304 Not Modified");
            exit;
        }
    }
    header('Last-Modified: ' . $gmt);
}

function updateOCR_soundex() {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    $t = $DRW->query("SELECT * FROM cscan_document_text WHERE dts_val <> '' AND soundex_text_val IS NULL", $DRW_read);
    while ($t && $z = $DRW->fetch_assoc($t)) {
        $newwords = array();
        $words = split(' ', $z['dts_val']);
        foreach ($words as $k => $v) {
            if (strlen($v) > 3)
                $newwords[] = soundex($v);
        }
        $DRW->query("UPDATE cscan_document_text 
						SET soundex_text_val = '" . implode(' ', $newwords) . "' 
						WHERE productID='$z[productID]' 
							AND document_id='$z[document_id]' 
							AND document_text_part='$z[document_text_part]' 
						LIMIT 1", $DRW_main);
    }
}

//not used?
function lessThanThree($searchKey, $ortext) {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    $underthree = '';
    $searchKey = preg_replace('/\\W/', ' ', $searchKey);
    $keyArray = preg_split('/\\s+/', $searchKey, -1, PREG_SPLIT_NO_EMPTY);
    foreach ($keyArray as $tmp) {
        if ($tmp != 'and' && $tmp != 'or' && $tmp != 'not' && strlen($tmp) < 3) {
            $underthree = " dts_val REGEXP '[[:<:]]" . $DRW->real_escape_string($tmp) . "[[:>:]]' AND";
            /* $tmp = mysqlLike($tmp);
              $underthree = " dts_val LIKE '%$tmp%' AND"; */
        }
    }
    if ($underthree != '') {
        $underthree = '(' . $ortext . ' OR (' . substr($underthree, 0, -4) . '))';
    } else
        $underthree = $ortext;
    return $underthree;
}

//not used?
function findCategories($cat, $subcat, $fuzzy = true) {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    if ($fuzzy) {
        $arr = array();
        $query = ($subcat != '') ? split(' ', trim($subcat)) : split(' ', trim($cat));
        $query = "sectorName LIKE '%" . implode("%' OR sectorName LIKE '%", $query) . "%'";
        $r = $DRW->query("SELECT sectorID FROM cscan_sector WHERE $query " . (($subcat != '') ? "AND parentID = '$cat'" : ""), $DRW_read);
        while ($r && $tmp = array_shift($DRW->fetch_array($r))) {
            $arr[] = $tmp;
        }
        return $arr;
    } else {
        $query = ($subcat != '') ? split(' ', trim($subcat)) : split(' ', trim($cat));
        $query = "sectorName LIKE '%" . implode("%' OR sectorName LIKE '%", $query) . "%'";
        $r = $DRW->query("SELECT sectorID FROM cscan_sector WHERE $query" . (($subcat != '') ? " AND parentID = '$cat'" : ""), $DRW_read);
        if ($r)
            return array(array_shift($DRW->fetch_array($r)));
    }
}

//not used?
function execute_query($query) {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    $result = $DRW->query($query, $DRW_main);
    return($result);
}

//not used?
function error($er_string = "") {
    global $er_msg;
    if (isset($er_msg))
        $er_msg.="<br>" . $er_string;
    else
        $er_msg = $er_string;
    return $er_msg;
}

//not used?
function age($age) {
    $age = array('Any', '<20', '21-29', '30-39', '40-49', '50-59', '60-69', '70-79', '80+');
}

function doQuerytest($search_id, $docount = false, $addedtodatabaseover = '', $dograph = false, $bid = -1, $relev = false, $expans = false, $unapproved = false, $clear_ps = false, $search_panelist_date_over = -1, $search_values = array(), $sess_userID = 0) {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm, $SPHINX_name;

    $ocrtext = '';
    $where = '';
    $sortby = '';
    $pjoin = '';
    $matchtext = '';
    $ojoin = '';
    $owhere = '';
    $awhere = '';
    $dmajoin = '';
    $edcjoin = '';
    $cjoin = '';
    $ccjoin = '';
    $afjoin = '';
    $affjoin = '';
    $sjoin = '';
    $sect_j = '';
    $pcjoin = '';
    $bjoin = '';
    $ejoin = '';
    $mljoin = '';
    $ajoin = '';
    $rjoin = '';
    $tljoin = '';
    $pjoin_vw = '';
    $filter_range = array();
    $statevwtable = '';
    $wheresearchproduct = '';

    $productidsarray = array();


    if (!empty($sess_userID)) {
        $_SESSION = array();
        $_SESSION['sess_mchannel'] = array();
        $result4 = $DRW->query("SELECT mu.mChannelID FROM cscan_mc_users_allow mu,cscan_mchannel mc WHERE userID=$sess_userID AND mu.mChannelID=mc.mChannelID", $DRW_read);


        while ($data4 = $DRW->fetch_row($result4)) {
            $_SESSION['sess_mchannel'][] = $data4[0];
        }

        @$DRW->free_result($result4);
        $_SESSION['sess_mpanel'] = array();
        $result4 = $DRW->query("SELECT mu.mPanelID FROM cscan_mp_users_allow mu,cscan_mpanel mc WHERE userID=$sess_userID AND mu.mPanelID=mc.mPanelID", $DRW_read);
        while ($data4 = $DRW->fetch_row($result4)) {
            $_SESSION['sess_mpanel'][] = $data4[0];
        }
        @$DRW->free_result($result4);
        $_SESSION['sess_sector'] = array();
        $_SESSION['sess_category'] = array();
        $_SESSION['sess_subcategory'] = array();
        $result2 = $DRW->query("SELECT su.sectorID,parentID FROM cscan_sector_users_allow su,cscan_sector cs WHERE userID=$sess_userID AND su.sectorID=cs.sectorID", $DRW_read);
        while ($data2 = $DRW->fetch_row($result2)) {
            if ($data2[1] == 0) {
                $_SESSION['sess_sector'][] = $data2[0];
            } else {
                $result3 = $DRW->query("SELECT parentID FROM cscan_sector WHERE sectorID=$data2[1]", $DRW_read);
                $data3 = $DRW->fetch_row($result3);
                if ($data3[0] == 0) {
                    $_SESSION['sess_category'][] = $data2[0];
                } else {
                    $_SESSION['sess_subcategory'][] = $data2[0];
                }
                @$DRW->free_result($result3);
            }
        }
        @$DRW->free_result($result2);
        $_SESSION['sess_userID'] = $sess_userID;
        $_SESSION['sess_search_exclude'] = array();
        $result2 = $DRW->query("SELECT search_field FROM cscan_search_exclude WHERE userID=$sess_userID", $DRW_read);
        while ($data2 = $DRW->fetch_row($result2)) {
            $_SESSION['sess_search_exclude'][] = $data2[0];
        }
        @$DRW->free_result($result2);
    } elseif (!isset($_SESSION)) {
        $_SESSION = array();
        $_SESSION['sess_mchannel'] = array();
        $_SESSION['sess_mpanel'] = array();
        $_SESSION['sess_sector'] = array();
        $_SESSION['sess_category'] = array();
        $_SESSION['sess_subcategory'] = array();
        $_SESSION['sess_userID'] = 0;
        $_SESSION['sess_search_exclude'] = array();
    }
    if (is_bool($dograph)) {
        $dograph = (int) $dograph;
    }
    if ($bid < 0) {

        if (count($search_values) > 0) {
            $searchKey = '';
            $searchType = 'ocr2';
            $searchOption = 'boolean';
            $mChannelID = '';
            $sectorID = '';
            $mPanelID = '';
            $addedToDatabase = '';
            $month1 = '';
            $month2 = '';
            $sort = 'desc';
            $company = '';
            $productName = '';
            $incentive = '';
            $categoryID = '';
            $mTypeID = '';
            $subCategoryID = '';
            $cardStatus = '';
            $personalization = '';
            $gender = '';
            $age = '';
            $state = '';
            $worksiteVoluntary = 0;
            $agentCommunicationID = '';
            $groupSize = '';
            $offerOrigin = '';
            $enhance = 1;
            $saved = 0;
            $compaignLanguage = '';
            $affinityAssociation = '';
            $income_mult = '';
            $fa_id_mult = '';
            $tl_id_mult = '';
            $siteCatID_mult = '';
            $pubTypeID_mult = '';
            $approved_date = '0000-00-00 00:00:00';
            $approved_date_to = '0000-00-00 00:00:00';
            $electronicID_mult = '';
            $DMA_ID_mult = '';
            $businessContent_mult = '';
            $delmethid_mult = '';
            $affinity_association = '';
            $prescription = 0;
            $AffinityCategoryID_mult = '';
            $search_panelist_date = 0;
            $is_affinion = 0;
            $is_military = 0;
            $search_competi_id = '';
            $ApplicationType_mult = '';
            $is_multicultural = 0;
            $search_rules = '';
            $IntroPricing_mult = '';
            $is_rewards = 0;
            $RewardsProgramEmphasis_mult = '';
            $is_incentive = 0;
            $responseMechID_mult = '';
            $multiculturalmarkets_mult = '';
            $CardNetwork_mult = '';
            $FeeProduct = 0;
            $external_link = '';
            $FeeProductType = '';
            $ca_related = 0;
            $is_mover = 0;
            $scsc_primary = 0;
            $OptOutFirmOffer = 0;
            $searchKey2 = '';
            $search_type_and = 0;
            $riders_mult = '';
            $is_hphsa = 0;
            $subSubCategoryID = '';
            $Income_Producing_Assets_Segment_Code = '';
            $cg_id = '';
            $is_citi = 0;
            $is_CreditCardMentioned = 0;
            $spanelist_filter = '';
            $edc_id_mult = '';
            $AffinitySubCategoryID_mult = '';
            $ERateType_mult = '';
            $EOfferPrice_mult = '';
            $ETermLength_mult = '';
            $is_ECancelFee = 0;
            $IssueTypeID_mult = '';
            $pcountry = '';
            $is_Reloadable = 0;
            $creditUnion = 0;
            foreach ($search_values as $var => $val) {
                $$var = $val;
            }
        } else {
            $savedQ = "SELECT searchKey,searchType,searchOption,mChannelID,sectorID,mPanelID,
						addedToDatabase,month1,month2,sort,company,productName,incentive,categoryID,mTypeID,subCategoryID,cardStatus,personalization,
						gender,age,state,worksiteVoluntary,agentCommunicationID,groupSize,offerOrigin,enhance,saved,compaignLanguage,affinityAssociation,income_mult,fa_id_mult,tl_id_mult,
						siteCatID_mult,pubTypeID_mult,approved_date,electronicID_mult,DMA_ID_mult,businessContent_mult,delmethid_mult,affinity_association,prescription,AffinityCategoryID_mult,
						search_panelist_date,is_affinion,is_military,search_competi_id,ApplicationType_mult,is_multicultural,search_rules,IntroPricing_mult,is_rewards,RewardsProgramEmphasis_mult,is_incentive,responseMechID_mult,
						multiculturalmarkets_mult,CardNetwork_mult,FeeProduct,external_link,FeeProductType,approved_date_to,ca_related,is_mover,scsc_primary,OptOutFirmOffer,searchKey2,search_type_and,riders_mult,is_hphsa,subSubCategoryID,Income_Producing_Assets_Segment_Code_mult,cg_id,is_citi,is_CreditCardMentioned,
						spanelist_filter,edc_id_mult,AffinitySubCategoryID_mult,ERateType_mult,EOfferPrice_mult,ETermLength_mult,is_ECancelFee,IssueTypeID_mult,pcountry,is_Reloadable,creditUnion 
						FROM cscan_search WHERE ID='" . $search_id . "'";
            $rs = $DRW->query($savedQ, $DRW_read);
            $data = $DRW->fetch_row($rs);
            $searchKey = trim($data[0]);
            $searchType = $data[1];
            $searchOption = $data[2];
            $mChannelID = $data[3];
            $sectorID = $data[4];
            $mPanelID = $data[5];
            $addedToDatabase = $data[6];
            $month1 = $data[7];
            $month2 = $data[8];
            $sort = $data[9];
            $company = $data[10];
            $productName = $data[11];
            $incentive = $data[12];
            $categoryID = $data[13];
            $mTypeID = $data[14];
            $subCategoryID = $data[15];
            $cardStatus = $data[16];
            $personalization = $data[17];
            $gender = $data[18];
            $age = $data[19];
            $state = $data[20];
            $worksiteVoluntary = $data[21];
            $agentCommunicationID = $data[22];
            $groupSize = $data[23];
            $offerOrigin = $data[24];
            $enhance = $data[25];
            $saved = $data[26];
            $compaignLanguage = $data[27];
            $affinityAssociation = $data[28];
            $income_mult = $data[29];
            $fa_id_mult = $data[30];
            $tl_id_mult = $data[31];
            $siteCatID_mult = $data[32];
            $pubTypeID_mult = $data[33];
            $approved_date = $data[34];
            $electronicID_mult = $data[35];
            $DMA_ID_mult = $data[36];
            $businessContent_mult = $data[37];
            $delmethid_mult = $data[38];
            $affinity_association = $data[39];
            $prescription = $data[40];
            $AffinityCategoryID_mult = $data[41];
            $search_panelist_date = $data[42];
            $is_affinion = $data[43];
            $is_military = $data[44];
            $search_competi_id = $data[45];
            $ApplicationType_mult = $data[46];
            $is_multicultural = $data[47];
            $search_rules = $data[48];
            $IntroPricing_mult = $data[49];
            $is_rewards = $data[50];
            $RewardsProgramEmphasis_mult = $data[51];
            $is_incentive = $data[52];
            $responseMechID_mult = $data[53];
            $multiculturalmarkets_mult = $data[54];
            $CardNetwork_mult = $data[55];
            $FeeProduct = $data[56];
            $external_link = $data[57];
            $FeeProductType = $data[58];
            $approved_date_to = $data[59];
            $ca_related = $data[60];
            $is_mover = $data[61];
            $scsc_primary = $data[62];
            $OptOutFirmOffer = $data[63];
            $searchKey2 = $data[64];
            $search_type_and = $data[65];
            $riders_mult = $data[66];
            $is_hphsa = $data[67];
            $subSubCategoryID = $data[68];
            $Income_Producing_Assets_Segment_Code = $data[69];
            $cg_id = $data[70];
            $is_citi = $data[71];
            $is_CreditCardMentioned = $data[72];
            $spanelist_filter = $data[73];
            $edc_id_mult = $data[74];
            $AffinitySubCategoryID_mult = $data[75];
            $ERateType_mult = $data[76];
            $EOfferPrice_mult = $data[77];
            $ETermLength_mult = $data[78];
            $is_ECancelFee = $data[79];
            $IssueTypeID_mult = $data[80];
            $pcountry = $data[81];
            $is_Reloadable = $data[82];
            $creditUnion = $data[83];
        }
        if (!empty($AffinitySubCategoryID_mult)) {
            $AffinityCategoryID_mult = $AffinitySubCategoryID_mult;
        }
        if ($search_panelist_date_over != -1) {
            $search_panelist_date = $search_panelist_date_over;
        }
        if ($approved_date == '0000-00-00' || $approved_date == '0000-00-00 00:00:00') {
            $approved_date = '';
        }
        if ($approved_date_to == '0000-00-00' || $approved_date_to == '0000-00-00 00:00:00') {
            $approved_date_to = '';
        }
        if (!empty($approved_date) && !empty($approved_date_to)) {
            $approved_date = "$approved_date,$approved_date_to";
        }
        if ($month1 != '' || $month2 != '') {
            $month = "$month1,$month2";
        } else {
            $month = '';
        }
        if (empty($mChannelID)) {
            $mChannelID = implode(',', $_SESSION['sess_mchannel']);
        }
        if (empty($mPanelID)) {
            $mPanelID = implode(',', $_SESSION['sess_mpanel']);
        }
        $isBiz = '';
        //business owner check when Business Owner is a Panel (6)
        /* $tmpArray = explode(',',$mPanelID);
          $tmpArray2 = array();
          foreach($tmpArray as $v){
          if($v==6) {
          $isBiz = 1;
          }
          else{
          $tmpArray2[] = $v;
          }
          }
          $mPanelID = implode(',',$tmpArray2); */

        if (empty($state) && in_array('canada', $_SESSION['sess_search_exclude'])) {
            $pcountry = 'US';
        } elseif (!empty($state)) {
            $pcountry = '';
        }
        if ($mPanelID == '1' || $mPanelID == '2' || $mPanelID == '1,2') {
            $consumer_only = true;
        } else {
            $consumer_only = false;
        }
        $ppdate = '';
        $ppdate_month = '';
        $ppstateID = '';
        $pgender = '';
        $is_panelist = 0;
        if (($search_panelist_date || $consumer_only) && $search_panelist_date_over != 0) {
            $ppdate = $addedToDatabase;
            $addedToDatabase = '';
            $ppdate_month = $month;
            $month = '';
            $ppstateID = $state;
            $state = '';
            $pgender = $gender;
            $gender = '';
            $is_panelist = 1;
        }

        //state relationship instead
        if ($ppstateID != '') {
            $rstate = $ppstateID;
        } else {
            $rstate = $state;
        }
        $ppstateID = '';
        $state = '';

        $andorArray = array();
        $andorArray['sectorID'] = 'OR';
        $exacterArray = array();
        $exactervalsArray = array();
        $noterArray = array();
        $search_rulesArray = explode(',', $search_rules);

        foreach ($search_rulesArray as $sr) {
            if (!empty($sr)) {
                list($f, $ao, $ex, $no) = explode(':', $sr);
                if ($ao) {
                    $andorArray[$f] = 'AND';
                }
                if ($ex) {
                    $exacterArray[] = $f;
                    $exactervalsArray[$f] = array();
                }
                if ($no) {
                    $noterArray[] = $f;
                }
            }
        }
        if ($spanelist_filter == '1') {
            $pjoin_filter = ' AND pp.pprimary=1';
            $pjoin_left = '';
            $pjoin_vw = ' JOIN pd_panelists_product_pp1_vw as pp';
        } elseif ($spanelist_filter == '2') {
            $pjoin_filter = ' AND pp.pprimary=0';
            $pjoin_left = '';
            $pjoin_vw = ' JOIN pd_panelists_product_pp0_vw as pp';
        } else {
            $pjoin_filter = '';
            $pjoin_left = ' LEFT';
            //  $pjoin_vw   = ' JOIN cscan_panelists_product as pp';
            $pjoin_vw = " JOIN pd_panelists_product_vw as pp";
        }

        $exactArray = array();
        $multExactArray = array('pd.mChannelID' => $mChannelID, 'pd.mPanelID' => $mPanelID);
        $likeArray = array('pd.incentive' => $incentive);
        $multArray = array();
        $otherArray = array('company' => $company, 'affinity_association' => $affinity_association, 'addedToDatabase' => $addedToDatabase, 'month' => $month, 'approved_date' => $approved_date, 'search_competi_id' => $search_competi_id, 'ApplicationType_mult' => $ApplicationType_mult, 'cg_id' => $cg_id, 'rstate' => $rstate, 'pcountry' => $pcountry);
        $panelistArray = array('ppageID' => $age, 'pincomeID' => $income_mult, 'isBiz' => $isBiz, 'dmap.code' => $DMA_ID_mult, 'ppdate' => $ppdate, 'ppdate_month' => $ppdate_month, 'ppstateID' => $ppstateID, 'pgender' => $pgender, 'Income_Producing_Assets_Segment_Code' => $Income_Producing_Assets_Segment_Code, 'edc_id' => $edc_id_mult);
        if ($productName != '') {
            $keyArray = preg_split('/"\\s+or\\s+"/i', $productName, -1, PREG_SPLIT_NO_EMPTY);
            if (count($keyArray) > 1 || preg_match('/^"([^"]+)"$/', $productName)) {
                foreach ($keyArray as $k => $val) {
                    if (preg_match('/^"([^"]+)"$/', $val, $match) || preg_match('/^([^"]+)"$/', $val, $match) || preg_match('/^"([^"]+)$/', $val, $match)) {
                        $keyArray[$k] = $match[1];
                    }
                }
                $multExactArray['productName'] = $keyArray;
            } else {
                $likeArray['productName'] = $productName;
            }
        }
        if ($enhance) {
            $exactArray = array_merge($exactArray, array('pd.cardStatus' => $cardStatus, 'pd.personalization' => $personalization, 'pd.gender' => $gender, 'pd.offerOrigin' => $offerOrigin, 'pd.compaignLanguage' => $compaignLanguage));
            $multExactArray = array_merge($multExactArray, array('pd.mTypeID' => $mTypeID, 'pd.delmethid' => $delmethid_mult));
            $likeArray = array_merge($likeArray, array('pd.external_link' => $external_link));
            $multArray = array_merge($multArray, array('pd.state' => $state, 'pd.agentCommunicationID' => $agentCommunicationID, 'pd.groupSize' => $groupSize, 'pd.fa_ids' => $fa_id_mult, 'pd.tl_ids' => $tl_id_mult, 'pd.electronicID' => $electronicID_mult, 'pd.businessContent' => $businessContent_mult, 'pd.multiculturalmarkets' => $multiculturalmarkets_mult, 'pd.responseMechID' => $responseMechID_mult, 'pd.FeeProductType' => $FeeProductType, 'pd.riders' => $riders_mult, 'pd.IssueTypeID' => $IssueTypeID_mult));
            $otherArray = array_merge($otherArray, array('AffinityCategoryID' => $AffinityCategoryID_mult, 'worksiteVoluntary' => $worksiteVoluntary, 'affinityAssociation' => $affinityAssociation, 'siteCatID' => $siteCatID_mult, 'pubTypeID' => $pubTypeID_mult, 'prescription' => $prescription, 'is_affinion' => $is_affinion, 'is_military' => $is_military, 'is_multicultural' => $is_multicultural, 'IntroPricing_mult' => $IntroPricing_mult, 'is_rewards' => $is_rewards, 'RewardsProgramEmphasis_mult' => $RewardsProgramEmphasis_mult, 'is_incentive' => $is_incentive, 'pd.CardNetwork_mult' => $CardNetwork_mult, 'FeeProduct' => $FeeProduct, 'is_mover' => $is_mover, 'OptOutFirmOffer' => $OptOutFirmOffer, 'is_hphsa' => $is_hphsa, 'is_citi' => $is_citi, 'is_CreditCardMentioned' => $is_CreditCardMentioned, 'pd.ERateType' => $ERateType_mult, 'EOfferPrice' => $EOfferPrice_mult, 'ETermLength' => $ETermLength_mult, 'ECancelFee' => $is_ECancelFee, 'Reloadable' => $is_Reloadable, 'isCreditUnion' => $creditUnion));
        }


        foreach ($exactArray as $field => $val) {
            if ($val != '') {
                $where .= " $field";
                if (in_array($field, $noterArray)) {
                    $where .= '<>';
                } else {
                    $where .= '=';
                }
                $where .= "'" . $DRW->real_escape_string($val) . "' AND ";
            }
        }
        foreach ($multExactArray as $field => $val) {
            if ($val != '') {
                $tmpwhere = '';
                if (is_array($val)) {
                    $tmpArray = $val;
                } else {
                    $tmpArray = explode(',', $val);
                }
                foreach ($tmpArray as $v) {
                    if ($v != '') {
                        $tmpwhere .= " $field";
                        if (in_array($field, $noterArray)) {
                            $tmpwhere .= '<>';
                        } else {
                            $tmpwhere .= '=';
                        }
                        $tmpwhere .= "'" . $DRW->real_escape_string($v) . "'";
                        if (in_array($field, $noterArray)) {
                            $tmpwhere .= ' AND ';
                        } else {
                            $tmpwhere .= ' OR ';
                        }
                    }
                }
                if ($field == 'mPanelID') {
                    $awhere .= $tmpwhere;
                } else {
                    $where .= " (" . substr($tmpwhere, 0, -4) . ") AND ";
                }
            }
        }
        foreach ($likeArray as $field => $val) {
            if ($val != '') {
                $val = mysqlLike($val);
                $where .= " $field ";
                if (in_array($field, $noterArray)) {
                    $where .= 'NOT ';
                }
                $where .= "LIKE '%$val%' AND ";
            }
        }
        foreach ($multArray as $field => $val) {
            if ($val != '') {
                $tmpArray = explode(',', $val);
                $where .= " (";
                foreach ($tmpArray as $v) {
                    if ($v != '') {
                        $where .= " ($field ";
                        if (in_array($field, $noterArray)) {
                            $where .= 'NOT ';
                        } else {
                            $where .= "LIKE '%{$v}%' AND $field ";
                        }
                        $where .= "REGEXP '[[:<:]]{$v}[[:>:]]')";
                        if (in_array($field, $noterArray)) {
                            $where .= ' AND ';
                        } else {
                            $where .= ' OR ';
                        }
                    }
                }
                $where = substr($where, 0, -4);
                $where .= ") AND ";
            }
        }

        // remove the JOIN cscan_scsc_product because use view in place of it.
        //$sect_j .= ' JOIN cscan_scsc_product as scsc ON (pd.productID=scsc.productID)';
        $partsArray = array();
        $seccatsubArray = get_seccatsub(implode(',', $_SESSION['sess_sector']), implode(',', $_SESSION['sess_category']), implode(',', $_SESSION['sess_subcategory']));



        foreach ($seccatsubArray as $sid => $cArray) {
            $part1 = "pd.scsc_sectorID=$sid";
            $partsArray[] = '(' . $part1 . ' AND pd.scsc_categoryID=0 AND pd.scsc_subCategoryID=0)';
            foreach ($cArray as $cid => $scArray) {
                $part2 = "pd.scsc_categoryID=$cid";
                $partsArray[] = '(' . $part1 . ' AND ' . $part2 . ' AND pd.scsc_subCategoryID=0)';
                foreach ($scArray as $scid => $a) {
                    $part3 = "pd.scsc_subCategoryID=$scid";
                    $partsArray[] = '(' . $part1 . ' AND ' . $part2 . ' AND ' . $part3 . ')';
                }
            }
        }
        if (count($partsArray) > 0) {
            $where .= '(' . implode(' OR ', $partsArray) . ') AND ';
        }
        $partsArray2 = array();
        $j = 0;
        if ($andorArray['sectorID'] == 'AND') {
            $alias = "scsc$j";
            $alias = "pd";
        } else {
            $alias = "scsc";
            $alias = "pd";
        }
        $seccatsubArray = get_seccatsub($sectorID, $categoryID, $subCategoryID, $subSubCategoryID);
        foreach ($seccatsubArray as $sid => $cArray) {
            $part1 = $alias . ".scsc_sectorID=$sid";
            if ($scsc_primary) {
                $part1 .= ' AND ' . $alias . ".scsc_sort=1";
            }
            if (in_array('sectorID', $exacterArray)) {
                $exactervalsArray['sectorID'][] = $sid;
            }
            if (count($cArray) == 0) {
                $partsArray2[] = '(' . $part1 . ')';
                if ($andorArray['sectorID'] == 'AND') {
                    // remove the JOIN cscan_scsc_product because use view in place of it.
                    //$sect_j .= ' JOIN cscan_scsc_product as '.$alias.' ON (pd.productID='.$alias.'.productID)';
                    $j++;
                    $alias = "scsc$j";
                }
            } else {
                foreach ($cArray as $cid => $scArray) {
                    $part2 = $alias . ".scsc_categoryID=$cid";
                    if (in_array('sectorID', $exacterArray)) {
                        $exactervalsArray['categoryID'][] = $cid;
                    }
                    if (count($scArray) == 0) {
                        $partsArray2[] = '(' . $part1 . ' AND ' . $part2 . ')';
                        if ($andorArray['sectorID'] == 'AND') {
                            // remove the JOIN cscan_scsc_product because use view in place of it.
                            //$sect_j .= ' JOIN cscan_scsc_product as '.$alias.' ON (pd.productID='.$alias.'.productID)';
                            $j++;
                            $alias = "scsc$j";
                        }
                    } else {
                        foreach ($scArray as $scid => $sscArray) {
                            $part3 = $alias . ".scsc_subCategoryID=$scid";
                            if (in_array('sectorID', $exacterArray)) {
                                $exactervalsArray['subCategoryID'][] = $scid;
                            }
                            if (count($sscArray) == 0) {
                                $partsArray2[] = '(' . $part1 . ' AND ' . $part2 . ' AND ' . $part3 . ')';
                                if ($andorArray['sectorID'] == 'AND') {
                                    // remove the JOIN cscan_scsc_product because use view in place of it.
                                    //$sect_j .= ' JOIN cscan_scsc_product as '.$alias.' ON (pd.productID='.$alias.'.productID)';
                                    $j++;
                                    $alias = "scsc$j";
                                }
                            } else {
                                foreach ($sscArray as $sscid => $ssscArray) {
                                    $part4 = $alias . ".scsc_subSubCategoryID=$sscid";
                                    if (in_array('sectorID', $exacterArray)) {
                                        $exactervalsArray['subSubCategoryID'][] = $sscid;
                                    }
                                    $partsArray2[] = '(' . $part1 . ' AND ' . $part2 . ' AND ' . $part3 . ' AND ' . $part4 . ')';
                                    if ($andorArray['sectorID'] == 'AND') {
                                        // remove the JOIN cscan_scsc_product because use view in place of it.
                                        //$sect_j .= ' JOIN cscan_scsc_product as '.$alias.' ON (pd.productID='.$alias.'.productID)';
                                        $j++;
                                        $alias = "scsc$j";
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        if (count($partsArray2) > 0) {
            $where .= '(' . implode(' ' . $andorArray['sectorID'] . ' ', $partsArray2) . ') AND ';
        }
        foreach ($exactervalsArray as $f => $a) {
            $combos = array();
            if (count($a) <= 6) {
                pc_permute($a, $combos);
            }
            $wherec = '';
            foreach ($combos as $c) {
                $v = implode(',', $c);
                $wherec .= " $f='$v' OR ";
            }
            if ($wherec != '') {
                $where .= '(' . substr($wherec, 0, -4) . ') AND ';
            }
        }


        // print_r($otherArray);exit;

        foreach ($otherArray as $field => $val) {
            if ($val != '') {
                if ($field == 'addedToDatabase' && $addedtodatabaseover == '') {
                    if ($val == 'week') {
                        $where .= ' pd.addedToDatabase>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 7 DAY),\' 00:00:00\') AND ';
                        $filter_range[] = array('dts_date', strtotime('-7 days', strtotime(date('Y-m-d') . ' 00:00:00')), time());
                    } elseif ($val == '2week') {
                        $where .= ' pd.addedToDatabase>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 14 DAY),\' 00:00:00\') AND ';
                        $filter_range[] = array('dts_date', strtotime('-14 days', strtotime(date('Y-m-d') . ' 00:00:00')), time());
                    } elseif ($val == '1month') {
                        $where .= ' pd.addedToDatabase>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 1 MONTH),\' 00:00:00\') AND ';
                        $filter_range[] = array('dts_date', strtotime('-1 month', strtotime(date('Y-m-d') . ' 00:00:00')), time());
                    } elseif ($val == '3month') {
                        $where .= ' pd.addedToDatabase>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 3 MONTH),\' 00:00:00\') AND ';
                        $filter_range[] = array('dts_date', strtotime('-3 months', strtotime(date('Y-m-d') . ' 00:00:00')), time());
                    } elseif ($val == '6month') {
                        $where .= ' pd.addedToDatabase>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 6 MONTH),\' 00:00:00\') AND ';
                        $filter_range[] = array('dts_date', strtotime('-6 months', strtotime(date('Y-m-d') . ' 00:00:00')), time());
                    } elseif ($val == '1year') {
                        $where .= ' pd.addedToDatabase>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 1 YEAR),\' 00:00:00\') AND ';
                        $filter_range[] = array('dts_date', strtotime('-1 year', strtotime(date('Y-m-d') . ' 00:00:00')), time());
                    }
                } elseif ($field == 'month' && $addedtodatabaseover == '') {
                    $monthArray = explode(',', $val);
                    $month_1 = $monthArray[0];
                    $month_2 = $monthArray[1];
                    if ($month_1 == '') {
                        $month_1 = $month_2;
                    } elseif ($month_2 == '') {
                        $month_2 = $month_1;
                    }
                    $where .= " (pd.addedToDatabase BETWEEN '$month_1-01 00:00:00' AND '$month_2-31 23:59:59') AND ";
                    $filter_range[] = array('pd.dts_date', strtotime("$month_1-01 00:00:00"), strtotime("$month_2-31 23:59:59"));
                } elseif ($field == 'worksiteVoluntary' || $field == 'affinityAssociation' || $field == 'prescription' || $field == 'is_affinion' || $field == 'is_military' || $field == 'FeeProduct' || $field == 'is_mover' || $field == 'OptOutFirmOffer' || $field == 'is_hphsa' || $field == 'is_citi' || $field == 'ECancelFee' || $field == 'Reloadable' || $field == 'isCreditUnion') {
                    if (!empty($val)) {
                        if ($val == 1) {
                            $fieldval = 1;
                        } elseif ($val == 2) {
                            $fieldval = 0;
                        }
                        if ($field == 'OptOutFirmOffer') {
                            /* $sectorIDArray = explode(',',$sectorID);
                              if(!in_array(4,$sectorIDArray) && (in_array(90,$sectorIDArray) || in_array(6,$sectorIDArray))){
                              $tmp = '';
                              $in = false;
                              if(in_array(90,$sectorIDArray)){
                              $in = true;
                              $tmp .= "cscan_payment_cards.OptOutFirmOffer=$fieldval";
                              $pcjoin = ' JOIN cscan_payment_cards ON (pd.productID=cscan_payment_cards.productID)';
                              }
                              if(in_array(6,$sectorIDArray)){
                              if($in){
                              $tmp .= ' OR ';
                              }
                              $tmp .= "cscan_mortgage_loan.MLOptOutFirmOffer=$fieldval";
                              $mljoin = ' JOIN cscan_mortgage_loan ON (pd.productID=cscan_mortgage_loan.productID)';
                              }
                              $where .= " ($tmp) AND ";
                              }
                              else{ */
                            $where .= " pd.is_prescreen=$fieldval AND ";
                            //}
                        } else {
                            $where .= " $field=$fieldval AND ";
                        }
                        if ($field == 'ECancelFee') {
                            $ejoin = ' JOIN pd_cscan_energy_vw ev ON (pd.productID=ev.productID)';
                        } elseif ($field == 'Reloadable') {
                            $pcjoin = ' JOIN pd_cscan_payment_cards_vw pcv ON (pd.productID=pcv.productID)';
                        } elseif ($field == 'isCreditUnion') {
                            $cjoin = " JOIN pd_product_company_vw pcmv ON (pcmv.productID=pd.productID) ";
                            //$ccjoin = " JOIN cscan_company ON (cscan_company_product.companyID=cscan_company.companyID) ";
                        }
                    }
                } elseif ($field == 'is_multicultural') {
                    if ($val == 1) {
                        $where .= " pd.multiculturalmarkets<>'' AND ";
                    }
                } elseif ($field == 'is_incentive') {
                    if ($val == 1) {
                        $where .= " pd.incentive<>'' AND ";
                    }
                } elseif ($field == 'is_rewards') {
                    if ($val == 1) {
                        $tmp = '';
                        $in = false;
                        $sectorIDArray = explode(',', $sectorID);
                        if (in_array(90, $sectorIDArray)) {
                            $in = true;
                            $tmp .= "pd_cscan_payment_cards_vw.RewardsProgram=1";
                            $pcjoin = ' JOIN pd_cscan_payment_cards_vw ON (pd.productID=pd_cscan_payment_cards_vw.productID)';
                        }
                        if (in_array(87, $sectorIDArray)) {
                            if ($in) {
                                $tmp .= ' OR ';
                            }
                            $tmp .= "pd_banking_vw.BankingRewardsProgram=1";
                            $bjoin = ' JOIN pd_banking_vw ON (pd.productID=pd_banking_vw.productID)';
                        }
                        $where .= " ($tmp) AND ";
                    }
                } elseif ($field == 'is_CreditCardMentioned') {
                    if ($val == 1) {
                        $tmp = '';
                        $in = false;
                        $sectorIDArray = explode(',', $sectorID);
                        if (in_array(219, $sectorIDArray)) {
                            $in = true;
                            $tmp .= "pd_travel_leisure_vw.TLCreditCardMentioned=1";
                            $tljoin = ' JOIN pd_travel_leisure_vw ON (pd.productID=pd_travel_leisure_vw.productID)';
                        }
                        if (in_array(266, $sectorIDArray)) {
                            if ($in) {
                                $tmp .= ' OR ';
                            }
                            $tmp .= "pd_retail_vw.RCreditCardMentioned=1";
                            $rjoin = ' JOIN pd_retail_vw ON (pd.productID=pd_retail_vw.productID)';
                        }
                        $where .= " ($tmp) AND ";
                    }
                } elseif ($field == 'siteCatID' || $field == 'pubTypeID' || $field == 'ApplicationType_mult') {
                    $field2 = '';
                    if ($field == 'siteCatID') {
                        $field = 'cscan_sites.sites_category_id';
                        $ojoin .= ',cscan_sites_product,cscan_sites';
                        $owhere .= ' AND pd.productID=cscan_sites_product.productID AND cscan_sites_product.sites_id=cscan_sites.sites_id';
                    } elseif ($field == 'pubTypeID') {
                        $field = 'cscan_publication.print_typeID';
                        $ojoin .= ',cscan_publication_product,cscan_publication';
                        $owhere .= ' AND pd.productID=cscan_publication_product.productID AND cscan_publication_product.publicationID=cscan_publication.publicationID';
                    } elseif ($field == 'ApplicationType_mult') {
                        $sectorIDArray = explode(',', $sectorID);
                        $in = false;
                        if (in_array(90, $sectorIDArray)) {
                            $in = true;
                            $field = 'pd_cscan_payment_cards_vw.ApplicationType';
                            $pcjoin = ' JOIN pd_cscan_payment_cards_vw ON (pd.productID=pd_cscan_payment_cards_vw.productID)';
                        }
                        if (in_array(6, $sectorIDArray)) {
                            if ($in) {
                                $field2 = 'pd_mortgage_loan_vw.MLApplicationType';
                            } else {
                                $field = 'pd_mortgage_loan_vw.MLApplicationType';
                            }
                            $mljoin = ' JOIN pd_mortgage_loan_vw ON (pd.productID=pd_mortgage_loan_vw.productID)';
                        }
                    }
                    $tmpArray = explode(',', $val);
                    $where .= " (";
                    foreach ($tmpArray as $v) {
                        if ($v != '') {
                            $where .= " $field='" . $DRW->real_escape_string($v) . "' OR ";
                            if (!empty($field2)) {
                                $where .= " $field2='" . $DRW->real_escape_string($v) . "' OR ";
                            }
                        }
                    }
                    $where = substr($where, 0, -4);
                    $where .= ") AND ";
                } elseif ($field == 'IntroPricing_mult') {
                    $tmpArray = explode(',', $val);
                    $where .= " (";
                    foreach ($tmpArray as $v) {
                        switch ($v) {
                            case 1:
                                $where .= " pd_cscan_payment_cards_vw.PurchaseIntroductoryAPR is not null OR ";
                                break;
                            case 2:
                                $where .= " pd_cscan_payment_cards_vw.PurchaseIntroductoryAPR is null OR ";
                                break;
                            case 3:
                                $where .= " pd_cscan_payment_cards_vw.BalanceTransferIntroductoryAPR is not null OR ";
                                break;
                            case 4:
                                $where .= " pd_cscan_payment_cards_vw.BalanceTransferIntroductoryAPR is null OR ";
                                break;
                        }
                    }
                    $where = substr($where, 0, -4);
                    $where .= ") AND ";
                    $pcjoin = ' JOIN pd_cscan_payment_cards_vw ON (pd.productID=pd_cscan_payment_cards_vw.productID)';
                } elseif ($field == 'approved_date') {
                    $splitArray = explode(',', $val);
                    $split_1 = $splitArray[0];
                    $split_2 = $splitArray[1];
                    if ($split_1 == '') {
                        $split_1 = $split_2;
                    } elseif ($split_2 == '') {
                        $split_2 = $split_1;
                    }
                    $where .= " ($field BETWEEN '$split_1' AND '$split_2') AND ";
                } elseif ($field == 'search_competi_id' || $field == 'cg_id') {
                    $panelist_ids = array();
                    if ($field == 'cg_id') {
                        $sqlc = "SELECT DISTINCT panelist_id FROM cscan_panelists_product_group WHERE cg_id IN (" . $val . ")";
                        $rsc = $DRW->query($sqlc, $DRW_read);
                        while ($rowc = $DRW->fetch_row($rsc)) {
                            $panelist_ids[] = $rowc[0];
                        }
                    } else {
                        $vs = explode(',', $val);
                        $competi_ids = array();
                        foreach ($vs as $v) {
                            $competi_ids[] = "'" . $DRW->real_escape_string(trim($v)) . "'";
                        }
                        $sqlc = "SELECT panelist_id FROM cscan_panelists WHERE competi_id IN (" . implode(',', $competi_ids) . ")";
                        $rsc = $DRW->query($sqlc, $DRW_read);
                        while ($rowc = $DRW->fetch_row($rsc)) {
                            $panelist_ids[] = $rowc[0];
                        }
                    }
                    if (count($panelist_ids) == 0) {
                        $panelist_ids[] = '-1';
                    }

                    $pjoin = $pjoin_vw . ' ON (pd.productID=pp.productID' . $pjoin_filter . ')';
                    $where .= " pp.panelist_id IN (" . implode(',', $panelist_ids) . ") AND ";
                } elseif ($field == 'CardNetwork_mult' || $field == 'RewardsProgramEmphasis_mult') {
                    $sectorIDArray = explode(',', $sectorID);
                    $in = false;
                    $field2 = '';
                    if ($field == 'CardNetwork_mult') {
                        if (in_array(90, $sectorIDArray)) {
                            $in = true;
                            $field = 'pd_cscan_payment_cards_vw.CardNetwork';
                            $pcjoin = ' JOIN pd_cscan_payment_cards_vw ON (pd.productID=pd_cscan_payment_cards_vw.productID)';
                        }
                        if (in_array(87, $sectorIDArray)) {
                            if ($in) {
                                $field2 = 'pd_banking_vw.BankingCardNetwork';
                            } else {
                                $field = 'pd_banking_vw.BankingCardNetwork';
                            }
                            $bjoin = ' JOIN pd_banking_vw ON (pd.productID=pd_banking_vw.productID)';
                        }
                    } elseif ($field == 'RewardsProgramEmphasis_mult') {
                        $in = false;
                        if (in_array(90, $sectorIDArray)) {
                            $in = true;
                            $field = 'pd_cscan_payment_cards_vw.RewardsProgramEmphasis';
                            $pcjoin = ' JOIN pd_cscan_payment_cards_vw ON (pd.productID=pd_cscan_payment_cards_vw.productID)';
                        }
                        if (in_array(87, $sectorIDArray)) {
                            if ($in) {
                                $field2 = 'pd_banking_vw.BankingRewardsProgramEmphasis';
                            } else {
                                $field = 'pd_banking_vw.BankingRewardsProgramEmphasis';
                            }
                            $bjoin = ' JOIN pd_banking_vw ON (pd.productID=pd_banking_vw.productID)';
                        }
                    }
                    $tmpArray = explode(',', $val);
                    $where .= " (";
                    foreach ($tmpArray as $v) {
                        if ($v != '') {
                            $where .= " ($field like '%{$v}%' AND $field REGEXP '[[:<:]]{$v}[[:>:]]') OR ";
                            if (!empty($field2)) {
                                $where .= " ($field2 like '%{$v}%' AND $field2 REGEXP '[[:<:]]{$v}[[:>:]]') OR ";
                            }
                        }
                    }
                    $where = substr($where, 0, -4);
                    $where .= ") AND ";
                } elseif ($field == 'ERateType' || $field == 'EOfferPrice' || $field == 'ETermLength') {
                    $ejoin = ' JOIN pd_cscan_energy_vw ON (pd.productID=pd_cscan_energy_vw.productID)';

                    $efields_array = array('', '2', '3', '4');
                    $tmpArray = explode(',', $val);
                    $where .= " (";
                    if ($field == 'EOfferPrice') {
                        if (empty($tmpArray[0])) {
                            unset($tmpArray[0]);
                        }
                        $sql = "SELECT OfferPriceMin,OfferPriceMax FROM cscan_offer_price WHERE OfferPriceID IN(" . implode(',', $tmpArray) . ") ORDER BY OfferPriceSort";
                        $rs = $DRW->query($sql, $DRW_read);
                        $types = array();
                        while ($op = $DRW->fetch_row($rs)) {
                            foreach ($efields_array as $field_concat) {
                                $where .= " ($field$field_concat>=" . $op[0] . " AND $field$field_concat<=" . $op[1] . ") OR ";
                            }
                        }
                    } else {
                        foreach ($tmpArray as $v) {
                            if ($v != '') {
                                foreach ($efields_array as $field_concat) {
                                    $where .= " $field$field_concat='" . $DRW->real_escape_string($v) . "' OR ";
                                }
                            }
                        }
                    }
                    $where = substr($where, 0, -4);
                    $where .= ") AND ";
                } elseif ($field == 'company' || $field == 'affinity_association') {
                    $cos = array();
                    if ($ca_related == 1) {
                        if ($field == 'affinity_association') {
                            $sqlc = "SELECT DISTINCT pp.panelist_id FROM cscan_affinity pa,cscan_panelist_affinity pp
								WHERE pa.affinityID=pp.affinityID AND " . doMultCompany($val, true, 'affinity');
                        } else {
                            $sqlc = "SELECT DISTINCT pp.panelist_id FROM cscan_company pa,cscan_panelist_company pp
								WHERE pa.companyID=pp.companyID AND " . doMultCompany($val, true, 'company');
                        }
                        $rsc = $DRW->query($sqlc, $DRW_read);
                        while ($rowc = $DRW->fetch_row($rsc)) {
                            $cos[] = $rowc[0];
                        }
                        if (count($cos) == 0) {
                            $cos[] = '0';
                        }
                        $pjoin = $pjoin_vw . ' ON (pd.productID=pp.productID' . $pjoin_filter . ')';
                        $where .= " pp.panelist_id IN (" . implode(',', $cos) . ") AND ";
                    } else {
                        if ($field == 'affinity_association') {
                            $caat = 'affinity';
                            $vw_where = "pd_affinity_product_vw";
                            $afjoin = " JOIN pd_affinity_product_vw ON (pd_affinity_product_vw.productID=pd.productID) ";
                        } else {
                            $caat = 'company';
                            $vw_where = "pd_product_company_vw";
                            $cjoin = " JOIN pd_product_company_vw ON (pd_product_company_vw.productID=pd.productID) ";
                        }
                        $sqlc = "SELECT {$caat}ID FROM cscan_$caat WHERE " . doMultCompany($val, true, $caat);
                        $rsc = $DRW->query($sqlc, $DRW_read);
                        while ($rowc = $DRW->fetch_row($rsc)) {
                            $cos[] = $rowc[0];
                        }
                        if (count($cos) == 0) {
                            $cos[] = '0';
                        }
                        $where .= " {$vw_where}.{$caat}ID IN (" . implode(',', $cos) . ") AND ";
                    }
                } elseif ($field == 'AffinityCategoryID') {
                    $cos = explode(',', $val);
                    if (empty($cos[0])) {
                        unset($cos[0]);
                    }
                    $afjoin = " JOIN pd_affinity_product_vw ON (pd_affinity_product_vw.productID=pd.productID) ";
                    $affjoin = " JOIN cscan_affinity_vw ";
                    $where .= " cscan_affinity_vw.AffinityCategoryID IN (" . implode(',', $cos) . ") AND ";
                } elseif ($field == 'pcountry' || $field == 'rstate') {
                    if (empty($sjoin)) {
                        $statevwtable = 'pd_scsc_state_vw';
                        $sjoin = '';
                        //$sjoin = " JOIN cscan_product_detail_state ON (cscan_product_detail_state.productID=pd.productID)";
                    }
                    if ($field == 'pcountry') {
                        //$sjoin .= " JOIN cscan_state ON (cscan_product_detail_state.stateID=cscan_state.stateID) ";
                        //$where .= " (cscan_state.countryCode='".$DRW->real_escape_string($val)."' OR cscan_state.countryCode='') AND ";
                        $where .= " (pd.countryCode_copy='" . $DRW->real_escape_string($val) . "' OR pd.countryCode_copy='') AND ";
                    } else {
                        $cos = explode(',', $val);
                        if (empty($cos[0])) {
                            unset($cos[0]);
                        }
                        $where .= " pd.stateID IN (" . implode(',', $cos) . ") AND ";
                    }
                    if (empty($is_panelist)) {
                        $where .= " pd.is_panelist=0 AND ";
                    }
                }
            }
        }

        //echo $where;exit;
        foreach ($panelistArray as $field => $val) {
            if ($val != '') {
                $pjoin = $pjoin_left . $pjoin_vw . ' ON (pd.productID=pp.productID' . $pjoin_filter . ')';
                if ($field == 'Income_Producing_Assets_Segment_Code') {
                    $ajoin = " JOIN cscan_panelists_appends ON (cscan_panelists_appends.panelist_id=pp.panelist_id) ";
                } elseif ($field == 'edc_id') {
                    $edcjoin = ' JOIN cscan_edc_postalcode edc ON(pp.pppostalcode=edc.pppostalcode)';
                } elseif ($field == 'dmap.code') {
                    $dmajoin = ' JOIN cscan_dma_code_postalcode dmap ON (pp.pppostalcode=dmap.pppostalcode)';
                }
                /* arvind
                  if ($field == 'ppdate') {
                  if ($val == 'week')
                  $where .= ' (pp.ppdate>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 7 DAY),\' 00:00:00\') OR pd.addedToDatabase>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 7 DAY),\' 00:00:00\')) AND ';
                  elseif ($val == '2week')
                  $where .= ' (pp.ppdate>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 14 DAY),\' 00:00:00\') OR pd.addedToDatabase>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 14 DAY),\' 00:00:00\')) AND ';
                  elseif ($val == '1month')
                  $where .= ' (pp.ppdate>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 1 MONTH),\' 00:00:00\') OR pd.addedToDatabase>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 1 MONTH),\' 00:00:00\')) AND ';
                  elseif ($val == '3month')
                  $where .= ' (pp.ppdate>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 3 MONTH),\' 00:00:00\') OR pd.addedToDatabase>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 3 MONTH),\' 00:00:00\')) AND ';
                  elseif ($val == '6month')
                  $where .= ' (pp.ppdate>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 6 MONTH),\' 00:00:00\') OR pd.addedToDatabase>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 6 MONTH),\' 00:00:00\')) AND ';
                  elseif ($val == '1year')
                  $where .= ' (pp.ppdate>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 1 YEAR),\' 00:00:00\') OR pd.addedToDatabase>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 1 YEAR),\' 00:00:00\')) AND ';
                  }
                 */
                if ($field == 'ppdate') {
                    if ($val == 'week')
                        $where .= ' (pp.ppdate>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 7 DAY),\' 00:00:00\') ) AND ';
                    elseif ($val == '2week')
                        $where .= ' (pp.ppdate>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 14 DAY),\' 00:00:00\') ) AND ';
                    elseif ($val == '1month')
                        $where .= ' (pp.ppdate>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 1 MONTH),\' 00:00:00\') ) AND ';
                    elseif ($val == '3month')
                        $where .= ' (pp.ppdate>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 3 MONTH),\' 00:00:00\') ) AND ';
                    elseif ($val == '6month')
                        $where .= ' (pp.ppdate>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 6 MONTH),\' 00:00:00\') ) AND ';
                    elseif ($val == '1year')
                        $where .= ' (pp.ppdate>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 1 YEAR),\' 00:00:00\') ) AND ';
                }
                elseif ($field == 'ppdate_month') {
                    $monthArray = explode(',', $val);
                    $month_1 = $monthArray[0];
                    $month_2 = $monthArray[1];
                    if ($month_1 == '') {
                        $month_1 = $month_2;
                    } elseif ($month_2 == '') {
                        $month_2 = $month_1;
                    }
                    //arvind
                    // $where .= " ((pp.ppdate BETWEEN '$month_1-01 00:00:00' AND '$month_2-31 23:59:59') OR (pd.addedToDatabase BETWEEN '$month_1-01 00:00:00' AND '$month_2-31 23:59:59')) AND ";
                    $where .= " (pp.ppdate BETWEEN '$month_1-01 00:00:00' AND '$month_2-31 23:59:59' ) AND ";
                } elseif ($field == 'pgender') {
                    $where .= " (pp.pgender='$val' OR gender='$val') AND ";
                } elseif ($field == 'ppageID') {
                    $ageIds = explode(',', $val);
                    $where .= "(";

                    foreach ($ageIds as $id) {
                        $where .= "pp.ppageID LIKE '%$id%' ";

                        if ($id === end($ageIds)) {
                            $where .= ') AND ';
                        } else {
                            $where .= ' OR ';
                        }
                    }
                } else {
                    $tmpwhere = '';
                    $tmpArray = explode(',', $val);
                    foreach ($tmpArray as $v) {
                        if ($v != '') {
                            if ($field == 'Income_Producing_Assets_Segment_Code' || $field == 'dmap.code') {
                                $tmpwhere .= " $field='" . $v . "' OR ";
                            } elseif ($field == 'ppstateID') {
                                $tmpwhere .= " pp.ppstateID=" . (int) $v . " OR (state like '%{$v}%' AND state REGEXP '[[:<:]]{$v}[[:>:]]') OR ";
                            } else {
                                $tmpwhere .= " $field=" . (int) $v . " OR ";
                            }
                        }
                    }
                    if ($field == 'isBiz') {
                        $awhere .= $tmpwhere;
                    } else {
                        $where .= " (" . substr($tmpwhere, 0, -4) . ") AND ";
                    }
                }
            }
        }
        if ($awhere != '') {
            $where .= " (" . substr($awhere, 0, -4) . ") AND ";
        }
        if ($addedtodatabaseover != '') {
            $where .= " pd.addedToDatabase>='$addedtodatabaseover' AND ";
            $filter_range[] = array('dts_date', strtotime($addedtodatabaseover), time());
        }
        //arvind
        //$where .= " pd.addedToDatabase<=NOW() AND (pd.productStatus=1";
        $where .= "  (pd.productStatus=1";
        if ($unapproved) {
            $where .= " OR pd.productStatus=2";
        }
        $where .= ") AND ";





        if ($searchKey != '' || $searchKey2 != '') {

            if ($searchType == 'ocr') {
                //updateOCR_soundex();
                $matchagainst = 'MATCH(dts_val) AGAINST';
                //if(isMysqlBool($searchKey)){
                $matchtext = "$matchagainst ('" . parseBool($searchKey) . "' IN BOOLEAN MODE)";
                if ($relev) {
                    $ocrtext .= " RIGHT JOIN (SELECT productID FROM cscan_document_text_search WHERE $matchtext) AS t1 USING(productID)";
                    $matchtext = "$matchagainst ('" . unBool($searchKey) . "'";
                    if ($expans)
                        $matchtext .= ' WITH QUERY EXPANSION';
                    $matchtext .= ')';
                }
                else {
                    $where .= " $matchtext AND ";
                }
                //}
                //else{
                //	$matchtext = "$matchagainst ('".$DRW->real_escape_string($searchKey)."')";
                //	$where .= " $matchtext AND ";
                //}
                $ocrtext .= ' JOIN pd_text_search_vw ';
            } elseif ($searchType == 'ocr2' || $searchType == 'fulltext2' || $searchType == 'ocr_fulltext2') {
                if ($clear_ps) {

                    $DRW->query("DELETE FROM cscan_search_product WHERE ID=$search_id", $DRW_main);
                    $numrow = 0;
                } else {
                    //echo $search_id;exit;   
                    $count_save_sql = "SELECT COUNT(*) FROM cscan_search_product WHERE ID=$search_id";
                    $rs = $DRW->query($count_save_sql, $DRW_read);
                    $data = $DRW->fetch_row($rs);
                    $numrow = (int) $data[0];
                    //$numrow=2;
                }





                if ($numrow == 0 && !empty($SPHINX_name)) {
                    $searchi = 0;
                    $searches = array();
                    $searchkeys = array();
                    if ($searchType == 'ocr_fulltext2') {
                        if ($searchKey2 != '') {
                            $searches['fulltext2'] = '2';
                            $searchkeys['fulltext2'] = $searchKey2;
                        }
                        if ($searchKey != '') {
                            $searches['ocr2'] = '';
                            $searchkeys['ocr2'] = $searchKey;
                        }
                    } elseif ($searchKey != '') {
                        if ($searchType == 'fulltext2') {
                            $searches[$searchType] = '2';
                        } else {
                            $searches[$searchType] = '';
                        }
                        $searchkeys[$searchType] = $searchKey;
                    }

                    $searches_count = count($searches);

                    foreach ($searches as $st => $add) {


                        if ($search_type_and == 1 && $searches_count > 1) {
                            $searchi++;
                        }
                        $sk = $searchkeys[$st];
                        $s = startSphinx();

                        $inds = 'base_index_' . $SPHINX_name . $add . ',delta_index_' . $SPHINX_name . $add; //,base_index_'.$SPHINX_name.'stemmed'.$add.',delta_index_'.$SPHINX_name.'stemmed'.$add.'
                        if (strpos($sk, '*') !== false) {
                            $inds .= ',base_index_' . $SPHINX_name . 'star' . $add . ',delta_index_' . $SPHINX_name . 'star' . $add;
                        }
                        $ps = parseSphinx($s, $sk);



                        if (trim($ps) != '') {
                            $currcount = 0;
                            $step = $total = 50000;
                            if (!$s->setLimits(0, 1, 1)) {
                                sphinxErr(__LINE__, $s, 'setLimits');
                            }
                            foreach ($filter_range as $fr) {
                                if (!$s->setFilterRange($fr[0], $fr[1], $fr[2])) {
                                    sphinxErr(__LINE__, $s, 'setFilterRange', $fr[0] . ', ' . $fr[1] . ', ' . $fr[2]);
                                }
                            }

                            //echo $ps,$inds.'pppp'; 
                            //$inds='test';
                            //exit;
                            //  echo  $ps,$inds.'pppp'; 
                            if (!$result = $s->query($ps)) {
                                sphinxErr(__LINE__, $s, 'query', $ps);
                                // echo 'kkkk';
                            }

                            if (isset($result['matches'])) {
//                                foreach ($result['matches'] as $dts_id => $match) {
//                                    if ($add == '2') {
//                                        $productid = $dts_id;
//                                    } else {
//                                        $productid = $match['attrs']['productid'];
//                                    }
//                                    //$query = "INSERT IGNORE INTO cscan_search_product (ID,productID,spID) VALUES ($search_id,$productid,$searchi)";
//                                    //$DRW->query($query,$DRW_main);
//                                    //  $minID = $dts_id;
//                                    //  $currcount++;
//                                    $productidsarray[] = $productid;
//                                }




                                $total = (float) $result['total_found'];
                                $count = 0;
                                $minID = 0;
                                if ($add == '2') {
                                    $count_save_sql = "SELECT MAX(productID) FROM cscan_product_detail";
                                } else {
                                    $count_save_sql = "SELECT MAX(dts_id) FROM cscan_document_text_search";
                                }
                                $rs = $DRW->query($count_save_sql, $DRW_read);
                                $data = $DRW->fetch_row($rs);
                                $maxID = $data[0];

                                $DRW->query('START TRANSACTION', $DRW_main); //$DRW->connection($DRW_main); $DRW->begin_transaction();
                                for ($offset = 0; $offset <= $maxID; $offset+=$step) {
                                    $s = startSphinx();
                                    if (!$s->setLimits(0, $step, $step)) {
                                        sphinxErr(__LINE__, $s, 'setLimits');
                                    }
                                    foreach ($filter_range as $fr) {
                                        if (!$s->setFilterRange($fr[0], $fr[1], $fr[2])) {
                                            sphinxErr(__LINE__, $s, 'setFilterRange', $fr[0] . ', ' . $fr[1] . ', ' . $fr[2]);
                                        }
                                    }
                                    if ($minID < $maxID) {
                                        if (!$s->setIDRange($minID + 1, $maxID)) {
                                            sphinxErr(__LINE__, $s, 'setIDRange');
                                        }
                                    }
                                    if (!$result = $s->query($ps, $inds)) {
                                        sphinxErr(__LINE__, $s, 'query', $ps);
                                    }
                                    // print_r($result);exit;
                                    if (isset($result['matches'])) {
                                        foreach ($result['matches'] as $dts_id => $match) {
                                            if ($add == '2') {
                                                $productid = $dts_id;
                                            } else {
                                                $productid = $match['attrs']['productid'];
                                            }
                                            // $query = "INSERT IGNORE INTO cscan_search_product (ID,productID,spID) VALUES ($search_id,$productid,$searchi)";
                                            // $DRW->query($query, $DRW_main);
//                                            $queryselects =" Select ID cscan_search_product_latest  where ID='".$search_id."' AND productID='".$productid."'";
//                                            $resresults=$DRW->query($queryselects, $DRW_read);
//                                           echo  $numres = $DRW->num_rows($resresults);
//                                           if($numres=0){
//                                               
//                                            $query = "INSERT INTO cscan_search_product_latest (ID,productID) VALUES ($search_id,$productid)";
//                                             $DRW->query($query, $DRW_main);
//                                           }
                                            $minID = $dts_id;
                                            $currcount++;
                                            $productidsarray[] = $productid;
                                        }
                                        if ($currcount >= $total) {
                                            break;
                                        }
                                    }
                                    $err = $s->getLastError();
                                    $war = $s->getLastWarning();
                                    if (!empty($err) || !empty($war)) {
                                        //echo "$err | $war"; exit;
                                        break;
                                    }
                                    // note that total_found using setLimits(0,1,1) is not always the same as without limits (bug in Sphinx?)
                                    if (!isset($result['matches'])) {
                                        break;
                                    }
                                }
                                $DRW->query('COMMIT', $DRW_main); //$DRW->commit();
                            }
                        }
                    }
                    if ($search_type_and == 1 && $searches_count > 1) {
                        $sqlc = "SELECT productID,COUNT(*) AS cnt FROM cscan_search_product WHERE ID=$search_id GROUP BY productID HAVING cnt<>$searches_count";
                        $rsc = $DRW->query($sqlc, $DRW_read);
                        while ($rowc = $DRW->fetch_row($rsc)) {
                            $query = "DELETE FROM cscan_search_product WHERE ID=$search_id AND productID=$rowc[0]";
                            $DRW->query($query, $DRW_main);
                        }
                    }
                }
                if ($search_id != '') {
                    // $productidsstring=implode(',',$productidsarray);
                    //$wheresearchproduct =   " AND pd.productID in (".$productidsstring.") ";
                    // $wheresearchproduct =   " AND sp.productID in (".$productidsstring.") ";
                    // $wheresearchproduct = " AND sp.ID=" . $search_id;
                }
                $ocrtext .= ' JOIN cscan_search_product sp ON(  pd.productID=sp.productID)';
            } else {


                $matchagainst = 'MATCH(productHeadline,entryID) AGAINST'; //productName,company,secondCompany,productHeadline,entryID,incentive,searchText
                //if(isMysqlBool($searchKey)){
                $matchtext = "$matchagainst ('" . parseBool($searchKey) . "' IN BOOLEAN MODE)";
                if ($relev) {
                    $ocrtext .= " RIGHT JOIN (SELECT productID FROM cscan_product_detail WHERE $matchtext) AS t1 USING(productID)";
                    $matchtext = "$matchagainst ('" . unBool($searchKey) . "'";
                    if ($expans)
                        $matchtext .= ' WITH QUERY EXPANSION';
                    $matchtext .= ')';
                }
                else {
                    $where .= " $matchtext AND ";
                }
                //}
                //else{
                //	$matchtext = "$matchagainst ('".$DRW->real_escape_string($searchKey)."')";
                //	$where .= " $matchtext AND ";
                //}
            }
        }
        if ($where != '') {
            $where = ' WHERE ' . substr($where, 0, -5);
        }
    } else {
        $ojoin .= ',cscan_product_basket cb';
        $where = ' WHERE basket_id=' . $bid . ' AND userID=' . $_SESSION['sess_userID'] . ' AND cb.productID=pd.productID ';
        $saved = 1;
    }


    $productidsstring = implode(',', $productidsarray);
    $wheresearchproduct = " AND pd.productID in (" . $productidsstring . ") ";
    //echo count(array_unique($productidsarray))."hhhh".date('H:i:s');exit;

    $selectQuery = "SELECT "; // SQL_NO_CACHE
    if ($docount) {
        $selectQuery .= "COUNT(DISTINCT pd.productID)";
        $sortby = '';
    } else {
        if ($dograph != 0) {
            $sortby = '';
        }

        $matchtext = ($matchtext != '') ? ",$matchtext AS relevancy" : ",1 AS relevancy";

        $mintel = new \HS\Mintel();
        $mintel_set = $mintel->getFields();
        $mintel_set_2 = $mintel->getFieldSet('incentive_set_2');
        $mintel_set_3 = $mintel->getFieldSet('incentive_set_3');

        $incentive_set = implode(',pd.', array_merge(array_keys($mintel_set), array_keys($mintel_set_2), array_keys($mintel_set_3)));

        $selectQuery .= "DISTINCT pd.productID AS theproductID,pd.mChannelID, pd.mPanelID,pd.productHeadline,pd.sectorID, pd.categoryID,pd.subCategoryID,pd.entryID,
            pd.addedToDatabase, pd.company,pd.productName, pd.compaignLanguage, pd.firstSeen,pd.lastSeen, pd.mTypeID,pd.state,pd.agentCommunicationID,pd.secondCompany, pd.variantID,pd.affinityAssociation,pd.age,pd.gender,pd.incomeID,pd.publication,pd.isVariant,pd.isDemographic,pd.isInsight,pd.fa_ids, pd.tl_ids,pd.isFICO,pd.incentive_ongoing,pd.incentive,pd.$incentive_set,pd.delmethid,pd.responseMechID,pd.FeeProductType,pd.external_updates,pd.external_fans, pd.external_link,pd.prescription,pd.is_hphsa,pd.subSubCategoryID,
                pd.OfferExpiryDate,pd.is_citi, pd.riders,pd.is_prescreen,pd.isSurvey,pd.IssueTypeID, pd.traffic_sources,pd.social_media_name,pd.worksiteVoluntary,pd.groupSize$matchtext";
    }

    //$selectQuery .= " FROM cscan_product_detail pd$ocrtext$pjoin$cjoin$ccjoin$afjoin$affjoin$dmajoin$edcjoin$sect_j$pcjoin$bjoin$ejoin$mljoin$rjoin$tljoin$ajoin$sjoin$ojoin $where$owhere $sortby";
    //$selectQuery .= " FROM pd_scsc_vw pd $where$owhere $sortby";
    // echo $ocrtext.'text'.$cjoin .'cjoin'.$ccjoin.'ccjoin'.$afjoin.'afjoin'.$affjoin.'dmajoin'.$dmajoin.'edcjoin'.$edcjoin.'sectjoin'.$sect_j.'pcjoin'.$pcjoin.'bjoin'.$bjoin.'ejoin'.$ejoin.'mljoin'.$mljoin.'rjoin'.$rjoin.'tljoin'.$tljoin.'ajoin'.$ajoin.'sjoin'.$sjoin.'ojoin'.$ojoin;
    //  echo $pjoin.'aaa';exit;


    $selecttable = 'pd_scsc_vw';
    if ($statevwtable != '') {
        $selecttable = $statevwtable;
    }



    $selectQuery .= " FROM " . $selecttable . " pd$ocrtext$pjoin$cjoin$ccjoin$afjoin$affjoin$dmajoin$edcjoin$pcjoin$bjoin$ejoin$mljoin$rjoin$tljoin$ajoin$sjoin$ojoin $where$owhere$wheresearchproduct $sortby";

    if ($dograph != 0) {
        $field = getDoGraph($dograph);
        $selectQuery = "SELECT COUNT($field) AS field_count,$field AS field_name FROM ($selectQuery) as t1 GROUP BY $field"; // SQL_NO_CACHE ORDER BY field_count DESC,field_name ASC
    }

    // echo $selectQuery;exit;
    return array($selectQuery, $saved);
}

/* Added by Pradeep for isciti selection of radio button in edit popup section */

function checkProductStatus($is_citi, $combo_sid, $final_sector_check, $pstats, $cstats) {
    if(!defined('ENV')){
    define('ENV',getenv('SERVER_NAME'));
    }
    
    if ($is_citi == '0' AND ( $combo_sid == 4 OR $combo_sid == 5 OR $final_sector_check == 4 OR $final_sector_check == 5)) {
        $productstatus = 5;
       /* if((ENV == 'localhost' || ENV == 'demo.competiscan.com') AND ($combo_sid == 530 OR $final_sector_check == 530)) {
         $productstatus = 522;   
        }*/
        
    } else if ($is_citi == '0' AND ( $combo_sid ==87 OR $combo_sid ==90 OR $combo_sid ==372 OR $combo_sid ==6 OR $combo_sid ==368 OR $final_sector_check ==87 OR $final_sector_check ==90 OR $final_sector_check ==372 OR $final_sector_check ==6 OR $final_sector_check ==368)) {
        $productstatus = 6;
        /*if((ENV == 'localhost' || ENV == 'demo.competiscan.com') AND ($combo_sid == 372 OR $final_sector_check == 372)) {
         $productstatus = 522;   
        } */
    }
     /* Start Emerging Section */
    else if ($is_citi == '0' AND ( $combo_sid ==522 OR $combo_sid ==525 OR $combo_sid ==530 OR $final_sector_check ==522 OR $final_sector_check ==525 OR $final_sector_check ==530)) {
        $productstatus = 522;
    } 
     /* End Emerging Section */
    else if ($is_citi == '0' AND ( $combo_sid == 315 OR $final_sector_check == 315)) {
        $productstatus = 315;
    } else if ($is_citi == '0' AND ( $combo_sid == 266 OR $combo_sid == 262 OR $final_sector_check == 262 OR $final_sector_check == 266)) {
        if ($cstats == 266 AND ( $pstats == '-1' || $pstats == '2' || $pstats == '3' || $pstats == '4' || $pstats == '1' )) {
            $productstatus = '';
        } else {
            $productstatus = 266;
        }
        /*if((ENV == 'localhost' || ENV == 'demo.competiscan.com') AND ($combo_sid == 525 OR $final_sector_check == 525)) {
         $productstatus = 522;   
        }*/
    } else if ($is_citi == '0' AND ( $combo_sid == 9 OR $final_sector_check == 9))   {
        $productstatus = 9;
    } else if ($is_citi == '0' AND ( $combo_sid == 219 OR $final_sector_check == 219)) {
        if ($cstats == 219 AND ( $pstats == '2' || $pstats == '3' || $pstats == '4' || $pstats == '1' )) {
            $productstatus = '';
            
        } else {
            $productstatus = 219;
        }
    } else {
        $productstatus = '';
    }
    return $productstatus;
}

function ShowDeviceBymd5($ad_md5,$simpledomain=''){
    global $DRW, $DRW_read, $DRW_digital;    
    
    $sqls = "select table_name from cscan_digital_observation_tables";
    $results = $DRW->query($sqls, $DRW_digital);
    while ($rows = $DRW->fetch_array($results)) {
        $tblsname=$rows['table_name'];    
    
    $device = '';
        $sql = "select device,simple_domain from ".$tblsname." where ad_md5='".$ad_md5."' and device!='' AND device is NOT NULL order by device DESC limit 0,1";
        $result = $DRW->query($sql, $DRW_digital);
        if ($DRW->num_rows($result) > 0) {
            $row = $DRW->fetch_array($result);
            $device = trim($row['device']);
            $simple_domain = trim($row['simple_domain']);

            if($simpledomain!=''){
                if(empty($simple_domain) || is_null($simple_domain)){
                  $sqls = "select simple_domain from ".$tblsname." where ad_md5='".$ad_md5."' and simple_domain!='' AND simple_domain is NOT NULL order by observationID DESC limit 0,1";
                  $results = $DRW->query($sqls, $DRW_digital);
                    if ($DRW->num_rows($results) > 0) {
                        $rows = $DRW->fetch_array($results);                        
                        $simple_domain = trim($rows['simple_domain']);
                    }
                }           
                
                return $simple_domain;
            }else{
                return $device;
            }
            break;
        }
    
    }    
}

function CheckPanelistDigital($pdid){
      global $DRW, $DRW_read;    
      $sql = "select distinct panelist_id from cscan_panelists_product where productID='".$pdid."'";
    $result = $DRW->query($sql, $DRW_read);
    $isdigital=0;
    if ($DRW->num_rows($result)>0){
        $row = $DRW->fetch_array($result);
        $panelist_id = trim($row['panelist_id']);
        
        $panelist_mod=array();        
        if(strstr($panelist_id,',')){
           $panelist_mod=explode(',',$panelist_id);
        }else{ 
           $panelist_mod[]=$panelist_id;
        }
        //print_r($panelist_mod); die;
        if(!empty($panelist_mod)){     
            
            foreach($panelist_mod as $findpanelist){
                 $sql = "select productID  from cscan_product_detail where FIND_IN_SET('".$findpanelist."',panelist_id) AND is_digital='1' limit 0,1"; 
                $result = $DRW->query($sql, $DRW_read);
                if ($DRW->num_rows($result)>0){
                    $isdigital=1;        
                }
        
            }
        } 
    
    }
    return $isdigital;
}
function UpdateAllForDigital($save_pd_pan){
    global $DRW, $DRW_read, $DRW_main;
    if(!empty($save_pd_pan)){
        //$panvalues = implode(',', $save_pd_pan);
        $productIDArray=array();
	foreach ($save_pd_pan as $spdp) {
                if (!empty($spdp)) {
                    $sqlsel    = " select productID from cscan_product_detail where ((CONCAT(',',panelist_id,',') REGEXP ',".$spdp.",')) ";
                    $resultsel = $DRW->query($sqlsel, $DRW_read);        
                    if ($DRW->num_rows($resultsel)>0){
                       while($row = $DRW->fetch_array($resultsel)){
                            $productIDArray[] = trim($row['productID']);
                       }
                    }
                //$sqlUPDT="UPDATE cscan_product_detail set panelist_sort='1' where FIND_IN_SET('".$spdp."',panelist_id)";
		//$DRW->query($sqlUPDT, $DRW_main);   
		}
	}
        
        if(!empty($productIDArray)){
            $productIDstr= implode(',', array_unique($productIDArray));
            $sqlUPDT="UPDATE cscan_product_detail set panelist_sort='1' where productID in(".$productIDstr.")";
	    $DRW->query($sqlUPDT, $DRW_main);
        }
      
    }
}

#######################  for display the product image under temp ###############
function uploadCompanyTempImg($muid, $fileArray, $companyID,$temp_img_companyID='') {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    if (!empty($fileArray['imgFile']['name'])) { 
        $imgNameArr = preg_replace('/[^a-zA-Z0-9_\\.\\-]/', '_', $fileArray['imgFile']['name']);
        $imgTypeArr = $fileArray['imgFile']['type'];
        $imgTempNameArr = $fileArray['imgFile']['tmp_name'];
        $imgSizeArr = $fileArray['imgFile']['size'];

        $root = dirname(__FILE__);
        if (strpos($root, '/includes') !== false) {
            $root = substr($root, 0, strpos($root, '/includes'));
        }
        $cpath = '/company_tempImages/';
        $docpart = $root . $cpath;
       // $ext = explode('.', $imgNameArr);
       // $ext = $ext[count($ext) - 1];
        $img_co_filename = $muid.$imgNameArr;
        if (move_uploaded_file($imgTempNameArr, $docpart . $img_co_filename)) {
            $sql = "REPLACE INTO cscan_temp_company_img (muid,img_co_path,company_id)
                VALUES ('" . $muid . "', '" . $DRW->real_escape_string($img_co_filename) . "','" . $companyID . "')";
            ############## Remove it later ############
            $instsql='insert into cscan_product_email_history set dt="'.$DRW->real_escape_string($sql).'"';
            $DRW->query($instsql, $DRW_main);
            $fid1 = $DRW->insert_id($DRW_main);
            ###########################################
            if($DRW->query($sql, $DRW_main)){
                $instsql='update cscan_product_email_history set exexuted=1 where id="'.$fid1.'"';
                $DRW->query($instsql, $DRW_main);
            }else{
                sendDevAlert('Conetent site func- unable to execute query',$sql);
            }
        }
    }else if($temp_img_companyID>0){ 
        
        $checkV = "SELECT img_co_path,img_co_filename FROM cscan_img_company WHERE companyID=$temp_img_companyID";
        $checkV = $DRW->query($checkV, $DRW_read);
        //exit;
        $dataV = $DRW->fetch_row($checkV);
        if (!empty($dataV[1])) {
            
            $root = dirname(__FILE__);
            if (strpos($root, '/includes') !== false) {
                $root = substr($root, 0, strpos($root, '/includes'));
            }
            
            $img_co_path=$dataV[0];
            //$img_co_filename=$muid.$dataV[1];
            $img_co_filename=$dataV[1];
            $img_co_filename_save=$muid.$dataV[1];
            
            $comp_img = $root.$img_co_path.$img_co_filename;
            
            $temp_img_folder = '/company_tempImages/';
            //$temp_img_path   = $root . $temp_img_folder.$img_co_filename;
            $temp_img_path   = $root . $temp_img_folder.$img_co_filename_save;            
            //if(file_exists($comp_img)){
            //    if (copy($comp_img, $temp_img_path)) {
                    $sql = "REPLACE INTO cscan_temp_company_img (muid,img_co_path,company_id)
                    VALUES ('" . $muid . "', '','" . $temp_img_companyID . "')";
                ############## Remove it later ############
                $instsql='insert into cscan_product_email_history set dt="'.$DRW->real_escape_string($sql).'"';
                $DRW->query($instsql, $DRW_main);
                $fid2 = $DRW->insert_id($DRW_main);
                ###########################################
                   if($DRW->query($sql, $DRW_main)){
                       $instsql='update cscan_product_email_history set exexuted=1 where id="'.$fid2.'"';
                       $DRW->query($instsql, $DRW_main);
                   }else{
                       sendDevAlert('Conetent site func- unable to execute query',$sql);
                   }

               // }
           // }    
        }
        
    }
}

#################################### Start S3 Implementation Code ###########################################
function temp_to_productimg($url,$productID,$imgtempcompanyfile){ 
    global $s3, $bucket_name;
    $yearpath = date('Y/');
    $monthpath = date('m/');
    $message=0;
    $root = dirname(__FILE__);
    $root = substr($root,0,strpos($root,'/includes'));
    $pathpart = $root.'/productImages/';
    $companyimagepath   =   $root.'/company_tempImages/'.$imgtempcompanyfile;
    $datepath = $yearpath.$monthpath;
    $imagePath = "$pathpart$datepath$productID/";
    
    if(!is_dir($pathpart.$yearpath)){
            mkdir($pathpart.$yearpath,02755);
    }
    if(!is_dir($pathpart.$datepath)){
        mkdir($pathpart.$datepath,02755);
    }
    if(!is_dir($imagePath)){
        mkdir($imagePath,02755);
    }

    $ext = explode('.',$url);
    $ext = $ext[count($ext)-1];
    $imageName = $productID.".".$ext;
    $thumbimageName = "thumb".$imageName;
    $images=    $imagePath.$imageName;


    $mod_ext='';
    $ch = curl_init ($url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
    $raw=curl_exec($ch);            
    curl_close ($ch);
                
    if(file_exists($images)){
        unlink($images);
    }
    
    $fp = fopen($images,'x');
    fwrite($fp, $raw);
    fclose($fp);
    
    $complete = createthumb($imagePath.$imageName,$imagePath.$thumbimageName,150,100);

    if($complete){

        if($ext == 'png'){
            $content_type = 'image/png';
        }elseif($ext == 'jpeg' || $ext == 'jpg' || $ext == 'gif'){
            $content_type = 'image/jpeg';
        }

        $result = $s3->putObject([
            'Bucket'        => $bucket_name,
            'Key'           => 'productImages/'.$datepath.$productID.'/'.$thumbimageName,
            'SourceFile'    => $imagePath .'thumb'. $imageName,
            'ACL'           => 'public-read',
            'ContentType'   => $content_type,
            'Metadata'      => array(
               'string'     => 'string'
             )
        ]);    

        if(isset($result['@metadata']['statusCode']) && $result['@metadata']['statusCode'] == 200){
            if(isset($_REQUEST['defaultCoImg']) && $img_companyID!=0){
                saveCompanyImgDB($img_companyID,$imagePath.$thumbimageName,$imgTypeArr,$imgSizeArr);
                unlink($imagePath.$thumbimageName);
                saveImageData($productID,'','','',$img_companyID);
            }
            else{
                saveImageData($productID,$imagePath,$thumbimageName,'/productImages/'.$datepath.$productID.'/');
            }
        }

        unlink($imagePath.$imageName);
        unlink($imagePath.$thumbimageName);
        
        if(file_exists($companyimagepath)){
            unlink($companyimagepath);
        }   
    }else{
        $message=1;
    }
    
    return $message;
}
#################################### End S3 Implementation Code ###########################################
####################### DA's functions ###############
//products approved from admin
function copydmApprovedPdf($updID, $inputName='') {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm,$s3,$bucket_name,$s3URL,$displays3URL;
    if(!empty($updID)){
        $sql   = "SELECT SQL_NO_CACHE document_filename,document_path FROM cscan_document WHERE productID=$updID AND document_id=1";
        $rs    = $DRW->query($sql,$DRW_read);
        $row   = $DRW->fetch_row($rs);
        $inputFiles = [];
        $i = 0;
       // print_r( $row);exit;
        if(count($row)>0 && $row[0]!='' && $row[1]!=''){
            $document_filename  = $row[0];
            $document_path      = $row[1];
            $root = dirname(__FILE__);
            if (strpos($root, '/includes') !== false) {
                        $root = substr($root, 0, strpos($root, '/includes'));
                    }
            $source_img_path = $root.$document_path.$document_filename;
           $source_img_path =   $displays3URL.substr($document_path,1).$document_filename;
            $info = $s3->doesObjectExist($bucket_name,substr($document_path.$document_filename,1));
           
            if ($info){
                $dmapprovedpdf_folder = '/dmapprovedpdf/';
                $detination_path   = $root.$dmapprovedpdf_folder.$document_filename;
                $csvfile = "z:\\dmapprovedpdf\\".$document_filename;
                $created_by = $GLOBALS['AUTH_DATA']['userID'];
                //echo $source_img_path.'==='. $detination_path;
                if (copy($source_img_path, $detination_path)) {
                    $sql = "REPLACE INTO cscan_dmapprovedpdf (product_id,document_path,created_by)
                    VALUES ('" . $updID . "', '" . $DRW->real_escape_string($detination_path) . "','" . $created_by . "')";
                    if($DRW->query($sql, $DRW_main)){
                        $inputFiles = array();
                        
                        $inputFiles[$i]['filepath'] = $csvfile;
                        $inputFiles[$i]['date'] = date('Y-m-d H:i');
                        $inputFiles[$i]['status'] = 0;
                        
                        if(!empty($inputFiles)){
                            dmaApprovedCsv($inputFiles, $inputName);
                        }
                        $inputFiles = array();
                    }
                }
            }
        }
//        if(!empty($inputFiles)){
//            dmaApprovedCsv($inputFiles);
//        }
    }
}

function daApprovedCsv($updID, $inputName='') {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    if(!empty($updID)){
        $sql   = "SELECT SQL_NO_CACHE document_filename,document_path FROM cscan_document WHERE productID=$updID AND document_id=1";
        $rs    = $DRW->query($sql,$DRW_read);
        $row   = $DRW->fetch_row($rs);
        $inputFiles = [];
        $i = 0;
       // print_r( $row);exit;
        if(count($row)>0 && $row[0]!='' && $row[1]!=''){
            $document_filename  = $row[0];
            $document_path      = $row[1];
            $root = dirname(__FILE__);
            if (strpos($root, '/includes') !== false) {
                $root = substr($root, 0, strpos($root, '/includes'));
            }
            $source_img_path = $root.$document_path.$document_filename;
           // if (file_exists($source_img_path) && is_file($source_img_path)){
            if (!empty($document_filename)){    
                $dmapprovedpdf_folder = '/dmapprovedpdf/';
                $detination_path   = $root.$dmapprovedpdf_folder.$document_filename;
                $csvfile = "z:\\dmapprovedpdf\\".$document_filename;
                
                $created_by = $GLOBALS['AUTH_DATA']['userID'];
                //$csvfile = 'z:'.'\\'.$document_path.$document_filename;
                //$csvfile = str_replace('/', '\\', $csvfile);
                //$csvfile = str_replace('\\\\', '\\', $csvfile);
//                if (copy($source_img_path, $detination_path)) {
                    $sql = "REPLACE INTO cscan_dmapprovedpdf (product_id,document_path,created_by)
                    VALUES ('" . $updID . "', '" . $DRW->real_escape_string($detination_path) . "','" . $created_by . "')";
                    if($DRW->query($sql, $DRW_main)){
                        $inputFiles = array();
                        
                        $inputFiles[$i]['filepath'] = $csvfile;
                        $inputFiles[$i]['date'] = date('Y-m-d H:i');
                        $inputFiles[$i]['status'] = 0;
                        
                        if(!empty($inputFiles)){
                            dmaApprovedCsv($inputFiles, $inputName);
                        }
                        $inputFiles = array();
                    }
//                }
            }
        }
//        if(!empty($inputFiles)){
//            dmaApprovedCsv($inputFiles);
//        }
    }
}
//to create index_input.csv after products approved from admin
function dmaApprovedCsv($array = array(), $inputName = ''){
    if (count($array) == 0) {
      return null;
    }
    //ob_start();
    $root = dirname(__FILE__);
    if (strpos($root, '/includes') !== false) {
        $root = substr($root, 0, strpos($root, '/includes'));
    }
    $dir = $root.'/dacsv/'.date('Y-m-d');
    if(!is_dir($dir)){
        if(mkdir($dir,0777,true)){
        }else{
            echo $dir;die;
        }
        @chmod($dir,0777);
        @chown($dir,'apache');
    }   
    if(!empty($inputName)){
        $filename = $dir.'/'.$inputName;
    }else{
        $filename = $dir.'/'.date('Y-m-d').'_index_input.csv';
    }    
    
    if (!file_exists($filename)){      
        $df = fopen($filename, 'w');
        //fputcsv($df, ['Input File', 'Date', 'Status']);
        my_fputcsv($df, ['Input File', 'Date', 'Status']);
        fclose($df);
    }   
    $df = fopen($filename, 'a');
    foreach ($array as $row) {
       //fputcsv($df, $row);
       my_fputcsv($df, $row);
    }
    fclose($df);
    //return ob_get_clean();
}
function dmaUpdateCsv(array &$array, $filename){
    if (count($array) == 0) {
      return false;
    }
    if(empty($filename)){
        return false;
    }
    ob_start();
    if (!file_exists($filename)){      
        $df = fopen($filename, 'w');
        //fputcsv($df, ['Input File', 'Date', 'Status']);
        my_fputcsv($df, ['Input File', 'Date', 'Status', 'Matched(%)', 'Matched File', "DA Id"]);
        fclose($df);
    }   
    $df = fopen($filename, 'a');
    foreach ($array as $row) {
       //fputcsv($df, $row);
       my_fputcsv($df, $row);
    }
    fclose($df);
    return ob_get_clean();
}
//to append new line on csv
function my_fputcsv($handle, $fieldsarray, $delimiter = ",", $enclosure ='"'){
    $glue = $enclosure . $delimiter . $enclosure;
    return fwrite($handle, $enclosure . implode($glue,$fieldsarray) . $enclosure."\r\n");
}
//create/append log.csv file
function updateLog(array &$array, $filename){//echo '<pre>';print_r($array);die;
    if (count($array) == 0) {
      return false;
    }
    if(empty($filename)){
        return false;
    }
    ob_start();
    if (!file_exists($filename)){
        $df = fopen($filename, 'w');
        my_fputcsv($df, ["Batch / Cron","Picked File","Start Time","End Time","Status","ErrorLevel"]);
        fclose($df);    
    }elseif(!is_writable($filename)){//echo 'not writable';die;
        $basename = basename($filename);
        $backupname = date("Y-m-d H:i:s")."_".$basename;
        $prev = $filename;
        $new = str_replace($basename, $backupname, $filename);
        //echo '=>'.$new.'<=';die;
        if(rename($prev,$new)){
            
            @chmod($new,0777);
            @chown($new,'apache');
            if (($index_input = fopen($new, "r")) !== FALSE) {
                $a = 1;
                while (($index_data = fgetcsv($index_input, 1000, ",")) !== FALSE) {
                    //excude headlines first & grab other rows
                    if($a > 1){
                        if (!file_exists($filename)){
                            $df = fopen($filename, 'w');
                            my_fputcsv($df, ["Batch / Cron","Picked File","Start Time","End Time","Status","ErrorLevel"]);
                            fclose($df);        
                        }
                        $df = fopen($filename, 'a');  
                        my_fputcsv($df, $index_data);
                        fclose($df);
                        @chmod($filename,0777);
                        @chown($filename,'apache');
                    }
                    $a++;
                }
                fclose($index_input);
            }
        }
    }
    $df = fopen($filename, 'a');  
    my_fputcsv($df, $array);
    fclose($df);
    @chmod($filename,0777);
    @chown($filename,'apache');
    return ob_get_clean();
}
//create search_input.csv after read files from chicagoftp
function chicagoftpCsv(array &$array){
    if (count($array) == 0) {
      return null;
    }
    ob_start();
    $root = dirname(__FILE__);
    if (strpos($root, '/includes') !== false) {
        $root = substr($root, 0, strpos($root, '/includes'));
    }
    $dir = $root.'/dacsv/'.date('Y-m-d');
    $filename = $dir.'/'.date('Y-m-d').'_search_input.csv';
//    $dir = $root.'/dacsv/2018-03-30';
//    $filename = $dir.'/2018-03-30_search_input.csv';
    if(!is_dir($dir)){
        if(mkdir($dir,0777,true)){
        }else{
            echo $dir;die;
        }
        @chmod($dir,0777);
        @chown($dir,'apache');
    }    
    if (!file_exists($filename)){      
        $df = fopen($filename, 'w');
        //fputcsv($df, ['Input File', 'Date', 'Status', 'Matched(%)', 'Matched File']);
        my_fputcsv($df, ['Input File', 'Date', 'Status', 'Matched(%)', 'Matched File', 'DA Id']);
        fclose($df);
        @chmod($filename,0777);
        @chown($filename,'apache');
    }   
    @chmod($filename,0777);
    @chown($filename,'apache');
    $df = fopen($filename, 'a');
    foreach ($array as $row) {
       //fputcsv($df, $row);
       my_fputcsv($df, $row);
    }
    fclose($df);
    return ob_get_clean();
}

### MaxMail Indexing ###

function copydaMaxmailHtml($muid, $html='',$panelist_id) {//echo '$muid => '.$muid.'</br>';
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    if(!empty($muid)){
        if(empty($html)){
            $sql = "SELECT cettext FROM cscan_email_text_tmp WHERE cettype='text/html' AND muid = '".$muid."'";        
            $rs    = $DRW->query($sql,$DRW_read);
            if ($DRW->num_rows($rs) > 0){
                $row   = $DRW->fetch_assoc($rs);
                $html = trim($row['cettext']);
            }
        }
        $root = dirname(__FILE__);
        if (strpos($root, '/includes') !== false) {
            $root = substr($root, 0, strpos($root, '/includes'));
        }
        $dmtemphtml_folder = 'damaxmailhtml';
        $dir = $root.'/'.$dmtemphtml_folder.'/'.date('Y-m-d');
        //echo '$dir => '.$dir.'</br>';
        if(!is_dir($dir)){
            if(mkdir($dir,0777,true)){
            }else{
                echo $dir;die;
            }
            @chmod($dir,0777);
            @chown($dir,'apache');
        }
        $html_filename = $muid.'_'.$panelist_id.'.html';
        $source_html_path = $dir.'/'.$html_filename;
       // echo '$source_html_path => '.$source_html_path.'</br>';
        $myfile = fopen($source_html_path, "w") or die("Unable to open file!");
        fwrite($myfile, $html);
        fclose($myfile);

        $csvfile = "z:\\".$dmtemphtml_folder."\\".date("Y-m-d").'\\'.$html_filename;
        $created_by = $GLOBALS['AUTH_DATA']['userID'];
        //echo '$csvfile => '.$csvfile.'</br>';
        if (file_exists($source_html_path) && is_file($source_html_path)){
            $sql_chk = "SELECT id FROM cscan_da_maxmail_html WHERE muid='".$muid."'";
            $rs = $DRW->query($sql_chk, $DRW_read);
            if ($DRW->num_rows($rs) == 0){
                $sql = "INSERT INTO cscan_da_maxmail_html SET filename='".$DRW->real_escape_string($source_html_path)."', muid='".$muid."', date_created='".date('Y-m-d H:i')."'";
                //echo '$sql => '.$sql.'</br>';
                if($DRW->query($sql, $DRW_main)){
                    $inputFiles = array();

                    $inputFiles[$i]['filepath'] = $csvfile;
                    $inputFiles[$i]['date'] = date('Y-m-d H:i');
                    $inputFiles[$i]['status'] = 0;

                    if(!empty($inputFiles)){
                        $inputName = date('Y-m-d').'_search_input.csv';
                        dmMaxmailCsv($inputFiles, $inputName);                    
                    }
                    $inputFiles = array();
                }     
            }                    
                       
        }else{
            echo $source_html_path;die;
        }
    }
}
function damaxmailUpdateCsv(array &$array, $filename){
    if (count($array) == 0) {
      return false;
    }
    if(empty($filename)){
        return false;
    }
    ob_start();
    if (!file_exists($filename)){      
        $df = fopen($filename, 'w');
        //fputcsv($df, ['Input File', 'Date', 'Status']);
        my_fputcsv($df, ['Input File', 'Date', 'Status', 'Matched(%)', 'Matched File']);
        fclose($df);
    }   
    $df = fopen($filename, 'a');
    foreach ($array as $row) {
       //fputcsv($df, $row);
       my_fputcsv($df, $row);
    }
    fclose($df);
    return ob_get_clean();
}
function sendDevAlert($subject='',$message='', $to=''){
    require_once "Mail.php";
    require_once "Mail/mime.php";
    
    if(empty($to)){
        $to = "manas@nmgtechnologies.com,devendra.tiwari@nmgtechnologies.com,arvind.chaurasia@nmgtechnologies.com";
    }
    $html = '
    <html>
        <head><title>'.$subject.'</title></head>
        <body>
            <table width="90%">
                <tr><td colspan="3" align="center"><h3>'.$subject.'</h3><small>(Automated Email - '.date("Y-m-d H:i:s").')</small></td></tr>
                <tr><td colspan="3" align="center">&nbsp;</td></tr>
                <tr>
                    <td colspan="3">'.$message.'</td>
                </tr>
            </table>
        </body>
    </html>>';
    $params = array(
        'username' => '',
        'password' => '',
        'persist' => true,
    );
    $mail = Mail::factory('smtp', $params);
    $crlf = "\n";
    $hdrs = array('From' => "\"Competiscan\" <share@competiscan.com>", 'To' => $to, 'Subject' => $subject);
    $mime = new Mail_mime($crlf);
    $mime->setHTMLBody($html);
    $body = $mime->get();
    $headers = $mime->headers($hdrs);
    $send = $mail->send($to, $headers, $body);
}
######################################### 2018-07-12 ####
function copydaMaxmailApprovedPdf($updID, $inputName='') {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm,$s3,$bucket_name,$s3URL,$displays3URL;
    if(!empty($updID)){
        $uploadedfolder='damaxmail_approved';
         //$uploadedfolder='dacsv';
        $sql   = "SELECT document_filename,document_path FROM cscan_document WHERE productID=$updID AND document_id=1";
        $rs    = $DRW->query($sql,$DRW_read);
        if ($DRW->num_rows($rs) > 0){
            $row   = $DRW->fetch_assoc($rs);
            $root = dirname(__FILE__);
            if (strpos($root, '/includes') !== false) {
                $root = substr($root, 0, strpos($root, '/includes'));
            }
           $document_path = $root.$row['document_path'].$row['document_filename'];
           
           $newdocumentpath     =   $row['document_path'].$row['document_filename'];
          
            $csvfile = 'z:'.'\\'.'/'.$uploadedfolder.'/'.$row['document_filename'];
            $csvfile = str_replace('/', '\\', $csvfile);
            $csvfile = str_replace('\\\\', '\\', $csvfile);
            $created_by = $GLOBALS['AUTH_DATA']['userID'];
            $info = $s3->doesObjectExist($bucket_name,substr($newdocumentpath,1));
            // Where the files will be source from
            $dest = $root.'/'.$uploadedfolder.'/'.$row['document_filename'];
            // Where the files will be transferred to
            $source = $displays3URL.substr($newdocumentpath,1);
              
            //if (file_exists($document_path) && is_file($document_path)){
            if($info && $newdocumentpath!='' && copy($source, $dest)){
                
               
                $sql = "SELECT id FROM cscan_damaxmail_approved WHERE product_id='".$updID."' AND document_path='".$DRW->real_escape_string($dest)."'";
                $rs    = $DRW->query($sql,$DRW_read);
                if ($DRW->num_rows($rs) == 0){
                    $sql = "REPLACE INTO cscan_damaxmail_approved set product_id='".$updID."', document_path='".$DRW->real_escape_string($dest)."', created_by='".$created_by."'";
                    if($DRW->query($sql, $DRW_main)){
                        $inputFiles = array();
                            
                        $inputFiles[$i]['filepath'] = $csvfile;
                        $inputFiles[$i]['date'] = date('Y-m-d H:i');
                        $inputFiles[$i]['status'] = 0;

                        if(!empty($inputFiles)){
                            dmMaxmailApprovedCsv($inputFiles, $inputName);
                        }
                        $inputFiles = array();
                    }                
                } 
               // die("kill here");
            }else{
                echo $document_path.' => '.$csvfile;echo '</br>';
                //echo $document_path;die;
            }
        }
    }
}
function dmMaxmailApprovedCsv($array = array(), $inputName = ''){
    if (count($array) == 0) {
      return null;
    }
    //ob_start();
    $root = dirname(__FILE__);
    if (strpos($root, '/includes') !== false) {
        $root = substr($root, 0, strpos($root, '/includes'));
    }
    $dir = $root.'/damaxmailcsv/'.date('Y-m-d');
    if(!is_dir($dir)){
        if(mkdir($dir,0777,true)){
        }else{
            echo "Unable to create directory: ".$dir;//die;
        }
        @chmod($dir,0777);
        @chown($dir,'apache');
    }   
    if(!empty($inputName)){
        $filename = $dir.'/'.$inputName;
    }else{
        $filename = $dir.'/'.date('Y-m-d').'_index_input.csv';
    }    
    
    if (!file_exists($filename)){      
        $df = fopen($filename, 'w');
        //fputcsv($df, ['Input File', 'Date', 'Status']);
        my_fputcsv($df, ['Input File', 'Date', 'Status']);
        fclose($df);
    }   
    $df = fopen($filename, 'a');
    foreach ($array as $row) {
       //fputcsv($df, $row);
       my_fputcsv($df, $row);
    }
    fclose($df);
    //return ob_get_clean();
}
//sameday
function copydaMaxmailHtmlSameday($muid,$mailbox_uid,$html='',$panelist_id, $csvdate=''){
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    $success = true;
    if(!empty($muid) && !empty($mailbox_uid)){
        $sql = "SELECT id FROM cscan_da_maxmail_html_sameday_search WHERE mailbox_uid='".$mailbox_uid."'";
        $rs = $DRW->query($sql, $DRW_read);
        //echo $sql.'|';
        if ($DRW->num_rows($rs) == 0){
            if(empty($html)){
                $sql = "SELECT cettext FROM cscan_email_text_sameday_search WHERE cettype='text/html' AND muid = '".$muid."'";        
                $rs    = $DRW->query($sql,$DRW_read);
                if ($DRW->num_rows($rs) > 0){
                    $row   = $DRW->fetch_assoc($rs);
                    $html = trim($row['cettext']);
                }
            }
            if(!empty($html)){
                $root = dirname(__FILE__);
                if (strpos($root, '/includes') !== false) {
                    $root = substr($root, 0, strpos($root, '/includes'));
                }
                //$dmtemphtml_folder = 'damaxmailhtml_search';
                $dmtemphtml_folder = 'damaxmailhtml';
                if(!empty($csvdate)){
                    $dir = $root.'/'.$dmtemphtml_folder.'/'.$csvdate;
                }else{
                    $dir = $root.'/'.$dmtemphtml_folder.'/'.date('Y-m-d');
                }        
                //echo '$dir => '.$dir.'</br>';
                if(!is_dir($dir)){
                    if(mkdir($dir,0777,true)){
                    }else{
                        //echo $dir;die;
                    }
                    @chmod($dir,0777);
                    @chown($dir,'apache');
                }
                $html_filename = $muid.'_'.$mailbox_uid.'_'.$panelist_id.'.html';
                $source_html_path = $dir.'/'.$html_filename;
                //echo '$source_html_path => '.$source_html_path.'</br>';
                $myfile = fopen($source_html_path, "w") or die("Unable to open file: $source_html_path");
                fwrite($myfile, $html);
                fclose($myfile);
        
                if(!empty($csvdate)){
                    $csvfile = "z:\\".$dmtemphtml_folder."\\".$csvdate.'\\'.$html_filename;
                }else{
                    $csvfile = "z:\\".$dmtemphtml_folder."\\".date("Y-m-d").'\\'.$html_filename;
                }        
                $created_by = $GLOBALS['AUTH_DATA']['userID'];

                //echo '$csvfile => '.$csvfile.'</br>';
                if (file_exists($source_html_path) && is_file($source_html_path)){
                    $sql = "INSERT INTO cscan_da_maxmail_html_sameday_search SET filename='".$DRW->real_escape_string($source_html_path)."', muid='".$muid."', mailbox_uid = '".$mailbox_uid."', date_created='".date('Y-m-d H:i')."'";
                    //echo $sql.'|';
                    if($DRW->query($sql, $DRW_main)){
                        $inputFiles = array();

                        $inputFiles[$i]['filepath'] = $csvfile;
                        $inputFiles[$i]['date'] = date('Y-m-d H:i');
                        $inputFiles[$i]['status'] = 0;
                        $inputFiles[$i]['muid'] = $muid;

                        if(!empty($inputFiles)){
                            if(!empty($csvdate)){
                                $inputName = $csvdate.'_sameday_maxmail_input.csv';
                            }else{
                                $inputName = date('Y-m-d').'_sameday_maxmail_input.csv';
                            }
                            //echo $inputName.'|';
                            maxmailCsv($inputFiles, $inputName, $csvdate);                    
                        }
                        $inputFiles = array();
                    }else{
                        $success = false;
                    }                                      
                }else{
                    $success = false;
                }
            }else{
                $success = false;
            }
        }else{
            $success = false;
        }        
    }else{
        $success = false;
    }
    /* if($remove){
        //remove sameday data
        $DRW->query("DELETE FROM cscan_email_sameday_search WHERE muid = '".$muid."'",$DRW_main);
        $DRW->query("DELETE FROM cscan_email_text_sameday_search WHERE muid = '".$muid."'",$DRW_main);
        $DRW->query("DELETE FROM cscan_email_file_sameday_search WHERE muid = '".$muid."'",$DRW_main);
    } */
    if($success){
        return $source_html_path;
    }else {
        return false;
    }
}

function copystoredaMaxmailHtmlSameday($csvdate){
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    $success = true;
    $sql = "SELECT e.mailbox_uid,e.panelist_id,e.muid,et.cettext FROM cscan_email_text_sameday_search as et
            join cscan_email_sameday_search as e on(e.muid=et.muid)
            WHERE et.cettype='text/html'";        
    $rs    = $DRW->query($sql,$DRW_read);
    if ($DRW->num_rows($rs) > 0){
       while($row   = $DRW->fetch_assoc($rs)){
            $html           =   trim($row['cettext']);
            $mailbox_uid    =   trim($row['mailbox_uid']);
            $panelist_id    =   trim($row['panelist_id']);
            $muid           =   trim($row['muid']);
            if(!empty($html)){
                $root = dirname(__FILE__);
                if (strpos($root, '/includes') !== false) {
                    $root = substr($root, 0, strpos($root, '/includes'));
                }
                //$dmtemphtml_folder = 'damaxmailhtml_search';
                $dmtemphtml_folder = 'damaxmailhtml';
                if(!empty($csvdate)){
                    $dir = $root.'/'.$dmtemphtml_folder.'/'.$csvdate;
                }else{
                    $dir = $root.'/'.$dmtemphtml_folder.'/'.date('Y-m-d');
                }        
                //echo '$dir => '.$dir.'</br>';
                if(!is_dir($dir)){
                    if(mkdir($dir,0777,true)){
                    }else{
                        //echo $dir;die;
                    }
                    @chmod($dir,0777);
                    @chown($dir,'apache');
                }
                $html_filename = $muid.'_'.$mailbox_uid.'_'.$panelist_id.'.html';
                $source_html_path = $dir.'/'.$html_filename;
                //echo '$source_html_path => '.$source_html_path.'</br>';
                $myfile = fopen($source_html_path, "w") or die("Unable to open file: $source_html_path");
                fwrite($myfile, $html);
                fclose($myfile);
        
                if(!empty($csvdate)){
                    $csvfile = "z:\\".$dmtemphtml_folder."\\".$csvdate.'\\'.$html_filename;
                }else{
                    $csvfile = "z:\\".$dmtemphtml_folder."\\".date("Y-m-d").'\\'.$html_filename;
                }        
                $created_by = $GLOBALS['AUTH_DATA']['userID'];

                //echo '$csvfile => '.$csvfile.'</br>';
                if (file_exists($source_html_path) && is_file($source_html_path)){
                    $sql = "INSERT INTO cscan_da_maxmail_html_sameday_search SET filename='".$DRW->real_escape_string($source_html_path)."', muid='".$muid."', mailbox_uid = '".$mailbox_uid."', date_created='".date('Y-m-d H:i')."'";
                    //echo $sql.'|';
                    if($DRW->query($sql, $DRW_main)){
                        $inputFiles = array();

                        $inputFiles[$i]['filepath'] = $csvfile;
                        $inputFiles[$i]['date'] = date('Y-m-d H:i');
                        $inputFiles[$i]['status'] = 0;
                        $inputFiles[$i]['muid'] = $muid;

                        if(!empty($inputFiles)){
                            if(!empty($csvdate)){
                                $inputName = $csvdate.'_sameday_maxmail_input.csv';
                            }else{
                                $inputName = date('Y-m-d').'_sameday_maxmail_input.csv';
                            }
                            //echo $inputName.'|';
                            maxmailCsv($inputFiles, $inputName, $csvdate);                    
                        }
                        $inputFiles = array();
                    }else{
                        $success = false;
                    }                                      
                }else{
                    $success = false;
                }
            }else{
                $success = false;
            }
        
       }
    }
    
 }

function maxmailCsv($array = array(), $inputName = '', $csvdate= ''){
    if (count($array) == 0) {
      return null;
    }
    //ob_start();
    $root = dirname(__FILE__);
    if (strpos($root, '/includes') !== false) {
        $root = substr($root, 0, strpos($root, '/includes'));
    }
    if(!empty($csvdate)){
        $dir = $root.'/damaxmailcsv/'.$csvdate;
    }else{
        $dir = $root.'/damaxmailcsv/'.date('Y-m-d');
    }
    
    if(!is_dir($dir)){
        if(mkdir($dir,0777,true)){
        }else{
            echo $dir;die;
        }
        @chmod($dir,0777);
        @chown($dir,'apache');
    }   
    if(!empty($inputName)){
        $filename = $dir.'/'.$inputName;
    }else{
        $filename = $dir.'/'.date('Y-m-d').'_index_input.csv';
    }    
    //echo '$filename => '.$filename.'</br>';
    if (!file_exists($filename)){      
        $df = fopen($filename, 'w');
        //fputcsv($df, ['Input File', 'Date', 'Status']);
        my_fputcsv($df, ['Input File', 'Date', 'Status', 'Muid']);
        fclose($df);
    }   
    $df = fopen($filename, 'a');
    foreach ($array as $row) {
       //fputcsv($df, $row);
       my_fputcsv($df, $row);
    }
    fclose($df);
    return $filename;
    //return ob_get_clean();
}
function dmMaxmailCsv($array = array(), $inputName = '', $csvdate = ''){
    if (count($array) == 0) {
      return null;
    }
    //ob_start();
    $root = dirname(__FILE__);
    if (strpos($root, '/includes') !== false) {
        $root = substr($root, 0, strpos($root, '/includes'));
    }
    if(!empty($csvdate)){
        $dir = $root.'/damaxmailcsv/'.$csvdate.'/';
    }else{
        $dir = $root.'/damaxmailcsv/'.date('Y-m-d').'/';
    }
    //echo '$dir => '.$dir.'</br>';
    if(!is_dir($dir)){
        if(mkdir($dir,0777,true)){
        }else{
            //echo $dir;die;
        }
        @chmod($dir,0777);
        @chown($dir,'apache');
    }   
    if(!empty($inputName)){
        $filename = $dir.$inputName;
    }else{
        $filename = $dir.date('Y-m-d').'_index_input.csv';
    }    
    //echo '$filename => '.$filename.'</br>';
    if (!file_exists($filename)){      
        $df = fopen($filename, 'w');
        //fputcsv($df, ['Input File', 'Date', 'Status']);
        my_fputcsv($df, ['Input File', 'Date', 'Status']);
        fclose($df);
    }   
    $df = fopen($filename, 'a');
    foreach ($array as $row) {
       //fputcsv($df, $row);
       my_fputcsv($df, $row);
    }
    fclose($df);
    return $filename;
    //return ob_get_clean();
}
###############Tracking Functions ########
function ipAddress()
{
    $ipaddress = '';
    if (!empty($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if(!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if(!empty($_SERVER['HTTP_X_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if(!empty($_SERVER['HTTP_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if(!empty($_SERVER['HTTP_FORWARDED']))
       $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if(!empty($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'Unknown IP Address';

    return $ipaddress;
}
function isMobile() 
{
    $agent = $_SERVER['HTTP_USER_AGENT'];
    return (preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$agent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($agent,0,4))) ? true : false ;
}
function trackDelete($data = array()){
    global $DRW, $DRW_read, $DRW_main, $DRW_crm; 
    if(is_array($data) && count($data)>0){
        $sql = "INSERT INTO cscan_delete_log SET ";
        $row = [];
        foreach($data as $key=>$value){
            if(!empty($key) && !empty($value)){
                $row[] = "`".$key."`".' = '."'".$DRW->real_escape_string($value)."'"; 
            }
        }
        if(count($row)>0){
            $sql .= implode(",", $row);
        }
        if(!empty($sql)){
            if($DRW->query($sql, $DRW_main)){
                return true;
            }
        }
    }
    return false;
    
}
function authUserName($id = NULL){
    global $DRW, $DRW_read;
    if(empty($id)){
        return false;
    }
    $sql = "SELECT userName FROM cscan_admin_users WHERE userID = '".$id."'";
    $query = $DRW->query($sql, $DRW_read);
    if ($DRW->num_rows($query) > 0) {
        $row = $DRW->fetch_assoc($query);
        return $row['userName'];
    }
    return false;
}
function serverName(){
    $host = $domain = $ip = '';
    if (php_sapi_name() == "cli") {
        $host = gethostname();
        $domain = php_uname("n");
        $ip = gethostbyname($host);

    } else {
        $host = isset($_SERVER['HTTP_X_FORWARDED_HOST']) ?  
        $_SERVER['HTTP_X_FORWARDED_HOST'] : $_SERVER["HTTP_HOST"];  

        $domain = isset($_SERVER['HTTP_X_FORWARDED_SERVER']) ?   
        $_SERVER['HTTP_X_FORWARDED_SERVER'] : $_SERVER["SERVER_NAME"];  

        $ip = isset($_SERVER['SERVER_ADDR'])?$_SERVER['SERVER_ADDR']:$_SERVER['SERVER_PORT'];
    }
    

    return array('host'=>$host, 'domain' => $domain, 'ip'=>$ip);
}
function siteMode(){
    $mode = false;
    $server = serverName();
    /* echo '<pre>';print_r($server);
    echo php_sapi_name(); */
    if (php_sapi_name() == "cli") {
        if($server['host']=='ip-172-19-41-192' || $server['domain']=='ip-172-19-41-192' || $server['ip']=='172.19.33.94' || $server['ip']=='172.19.41.192'){
            $mode = 'live';
        }elseif($server['host']=='ip-172-18-43-38' || $server['domain']=='ip-172-18-43-38' || $server['ip']=='172.18.43.38'){
            $mode = 'demo';
        }
    }else{
        if($server['host']=='www.competiscan.com' || $server['domain']=='www.competiscan.com' || $server['ip']=='172.19.33.94' || $server['ip']=='172.19.41.192'){
            $mode = 'live';
        }elseif($server['host']=='demo.competiscan.com' || $server['domain']=='demo.competiscan.com' || $server['ip']=='172.18.43.38'){
            $mode = 'demo';
        }
    }
    return $mode;
}

function sendthumbimageons3($imgPath , $imgname){
     global $s3,$bucket_name,$serverbaseurl;
     $localImage = $serverbaseurl.$imgPath.$imgname;
    if(strpos($imgPath,'/')=='0'){
       $imgPath  = substr($imgPath,1);
    }
    $mimeType='image/jpg';
    $result = $s3->putObject(
            array(
                'Bucket' => $bucket_name,
                'Key' => $imgPath.$imgname,
                'SourceFile' => $localImage,
                // 'Body' => fopen($tempFilePath, 'rb'),
               // 'StorageClass' => 'REDUCED_REDUNDANCY',
                'ContentType' => $mimeType,
                'ACL' => 'public-read',
                'Metadata' => array(
                    'string' => 'string'
                )
            )
        );
    if (file_exists($localImage)) {
            @unlink($localImage);
        }
   
}

function severityScore($video_id){
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    $sql = "SELECT id,youtube_url,audio_text_status FROM cscan_youtube_video where id='".$video_id."'";        
    $checkV = $DRW->query($sql, $DRW_read);
    $countV = $DRW->num_rows($checkV);
    $totalPoints=0;
    if($countV>0){
        $sql_match_logo = "SELECT l.id,lm.logo_match_time FROM cscan_youtube_search_logos as l join cscan_youtube_logos_match as lm on((lm.logo_id=l.id) AND (lm.video_id='" .$video_id. "') AND lm.logo_match_time IS NOT NULL) ";
        $checkLM = $DRW->query($sql_match_logo, $DRW_read);
        $countLM = $DRW->num_rows($checkLM);
        $p = 1;
        $keywordsMatch=array();
        $logoMatch=array();
        $sql_match = "SELECT k.id,km.keyword_match_time,km.audio_match_time FROM cscan_youtube_search_keywords as k join cscan_youtube_keywords_match as km on((km.keyword_id=k.id) AND (km.video_id='" .$video_id . "') AND (km.keyword_match_time IS NOT NULL OR km.audio_match_time IS NOT NULL)) ";
        $checkVM = $DRW->query($sql_match, $DRW_read);
        $countVM = $DRW->num_rows($checkVM);       
        if($countLM>0){
            while ($row_match = $DRW->fetch_array($checkLM)){                
                $logomatch_arr=array();
                $logo_match_time= $row_match['logo_match_time'];
                if(!empty($logo_match_time)){
                    if(strstr($logo_match_time,',')){
                        $logomatch_arr=explode(',',$logo_match_time);
                    }else{
                        $logomatch_arr[]=$logo_match_time;
                    }
                    if(!empty($logomatch_arr)){
                        $totalPoints=$totalPoints+(count($logomatch_arr)*50);
                    }
                }
            }
        }
        
        if($countVM>0){
            while ($row_match = $DRW->fetch_array($checkVM)){
                $keywordsMatch[]=$row_match;
                $keywords_match_time= $row_match['keyword_match_time'];
                $keywordmatch_arr=array();
                $audiomatch_arr=array();
                $audio_match_time= $row_match['audio_match_time'];
                if(!empty($keywords_match_time)){
                    if(strstr($keywords_match_time,',')){
                        $keywordmatch_arr=explode(',',$keywords_match_time);
                    }else{
                        $keywordmatch_arr[]=$keywords_match_time;
                    }                   
                    
                    if(!empty($keywordmatch_arr)){
                        $totalPoints=$totalPoints+count($keywordmatch_arr);
                    }
                }                
                if(!empty($audio_match_time)){
                    if(strstr($audio_match_time,',')){
                        $audiomatch_arr=explode(',',$audio_match_time);
                    }else{
                        $audiomatch_arr[]=$audio_match_time;
                    }
                    if(!empty($audiomatch_arr)){
                        $totalPoints=$totalPoints+count($audiomatch_arr);
                    }
                }
            }
           
        }        
       
    }
    
    return $totalPoints;
   
}
############################## Start FICO/CreditVision/Vantage Score Fields ##############
function getScoreRange($ID) {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    if (trim($ID) != '') {
        $sql = "SELECT score_range FROM cscan_score_range WHERE id = $ID ORDER BY id";
        $rs = $DRW->query($sql, $DRW_read);
        $types = array();
        while (list($type) = $DRW->fetch_row($rs)) {
            $types[] = $type;
        }
        $types = @implode(', ', $types);
    } else {
        $types = '';
    }
    return $types;
}


function generateRandomPassword() {
    $alphabet = 'abcdefghijklmnpqrstuvwxyz@#%ABCDEFGHIJKLMNPQRSTUVWXYZ123456789';
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < 10; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string
}

function getPagePermission() {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    $arr = array();
    $page_sql = "SELECT field_name FROM cscan_users_page_permission WHERE userID='".$_SESSION['sess_userID']."'" ; 			
    $rs_page = $DRW->query($page_sql,$DRW_read);
    if ($DRW->num_rows($rs_page) > 0) {
        while ($row = $DRW->fetch_array($rs_page)) {
            $field_name = $row['field_name'];            
            $arr[] = $field_name;
        }        
        
    }   
    return $arr;
}
//Add more data field
function getPromotionType() {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    $query = "SELECT id,name FROM cscan_promotion_types ORDER BY name";
    $result = $DRW->query($query, $DRW_read);
    $array = array();
    while ($row = $DRW->fetch_array($result)) {
        $array[$row['id']] = $row['name'];
    }
    return $array;
} 

function getPromotionTypeById($ID) {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    if ($ID != '') {
       
        $sql = "SELECT name FROM cscan_promotion_types WHERE id = $ID ORDER BY name";
        $rs = $DRW->query($sql, $DRW_read);
        $types = array();
        while (list($type) = $DRW->fetch_row($rs)) {
            $types[] = $type;
        }
        $types = @implode(', ', $types);
    } else {
        $types = 'N/A';
    }
    return $types;
}

function getPromotionShippingDetail() {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    $query = "SELECT id,name FROM cscan_promotion_shipping_details ORDER BY name";
    $result = $DRW->query($query, $DRW_read);
    $array = array();
    while ($row = $DRW->fetch_array($result)) {
        $array[$row['id']] = $row['name'];
    }
    return $array;
} 

function getPromotionShippingDetailById($ID) {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    if ($ID != '') {
       
        $sql = "SELECT name FROM cscan_promotion_shipping_details WHERE id = $ID ORDER BY name";
        $rs = $DRW->query($sql, $DRW_read);
        $types = array();
        while (list($type) = $DRW->fetch_row($rs)) {
            $types[] = $type;
        }
        $types = @implode(', ', $types);
    } else {
        $types = 'N/A';
    }
    return $types;
}

function getPromotionOnlineIStore() {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    $query = "SELECT id,name FROM cscan_promotion_online_instores ORDER BY name";
    $result = $DRW->query($query, $DRW_read);
    $array = array();
    while ($row = $DRW->fetch_array($result)) {
        $array[$row['id']] = $row['name'];
    }
    return $array;
} 

function getPromotionOnlineIStoreById($ID) {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    if ($ID != '') {
       
        $sql = "SELECT name FROM cscan_promotion_online_instores WHERE id = $ID ORDER BY name";
        $rs = $DRW->query($sql, $DRW_read);
        $types = array();
        while (list($type) = $DRW->fetch_row($rs)) {
            $types[] = $type;
        }
        $types = @implode(', ', $types);
    } else {
        $types = 'N/A';
    }
    return $types;
}

function getPromotionQualifiers() {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    $query = "SELECT id,name FROM cscan_promotion_qualifiers ORDER BY name";
    $result = $DRW->query($query, $DRW_read);
    $array = array();
    while ($row = $DRW->fetch_array($result)) {
        $array[$row['id']] = $row['name'];
    }
    return $array;
} 

function getPromotionQualifiersById($ID) {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    if ($ID != '') {
       
        $sql = "SELECT name FROM cscan_promotion_qualifiers WHERE id = $ID ORDER BY name";
        $rs = $DRW->query($sql, $DRW_read);
        $types = array();
        while (list($type) = $DRW->fetch_row($rs)) {
            $types[] = $type;
        }
        $types = @implode(', ', $types);
    } else {
        $types = 'N/A';
    }
    return $types;
}

function getPromotionSaleType() {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    $query = "SELECT id,name FROM cscan_promotion_sale_types ORDER BY name";
    $result = $DRW->query($query, $DRW_read);
    $array = array();
    while ($row = $DRW->fetch_array($result)) {
        $array[$row['id']] = $row['name'];
    }
    return $array;
} 

function getPromotionSaleTypeById($ID) {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    if ($ID != '') {
       
        $sql = "SELECT name FROM cscan_promotion_sale_types WHERE id = $ID ORDER BY name";
        $rs = $DRW->query($sql, $DRW_read);
        $types = array();
        while (list($type) = $DRW->fetch_row($rs)) {
            $types[] = $type;
        }
        $types = @implode(', ', $types);
    } else {
        $types = 'N/A';
    }
    return $types;
}

function getPromotionHolidays() {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    $query = "SELECT id,name FROM cscan_promotion_holidays ORDER BY name";
    $result = $DRW->query($query, $DRW_read);
    $array = array();
    while ($row = $DRW->fetch_array($result)) {
        $array[$row['id']] = $row['name'];
    }
    return $array;
} 

function getPromotionHolidaysById($ID) {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    if ($ID != '') {
       
        $sql = "SELECT name FROM cscan_promotion_holidays WHERE id = $ID ORDER BY name";
        $rs = $DRW->query($sql, $DRW_read);
        $types = array();
        while (list($type) = $DRW->fetch_row($rs)) {
            $types[] = $type;
        }
        $types = @implode(', ', $types);
    } else {
        $types = 'N/A';
    }
    return $types;
}
// for sinking purpose
function InsertAndUpdateProductID($productID) {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    if (trim($productID) != '') {
        $sqlDelete="delete from csv1_sync_product where product_id='".$productID."'";
        $DRW->query($sqlDelete, $DRW_main);
        $sql = "SELECT product_id FROM csv1_sync_product WHERE product_id ='".$productID."'";
        $rs = $DRW->query($sql, $DRW_read);
        if($DRW->num_rows($rs) < 1) {
         $sql_query = "insert into csv1_sync_product set 
				product_id='" . $productID."'";
          $DRW->query($sql_query, $DRW_main);
        }  
    } 
    
}

// for deleted product log
function DeletedProductIdLog($deletedproductID) {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    if (!empty($deletedproductID)) {
        foreach ($deletedproductID as $productid) {
           $sql = "SELECT product_id FROM csv1_deleted_product_log WHERE product_id ='".$productid."'";
            $rs = $DRW->query($sql, $DRW_read);
            if($DRW->num_rows($rs) < 1) {
             $sql_query = "insert into csv1_deleted_product_log set 
                                    product_id='" . $productid."'";
              $DRW->query($sql_query, $DRW_main);
            } 
        }  
    } 
    
}

function IntroductoryAPR($IntroductoryPricing)  
{ 
    if($IntroductoryPricing!=''){
         $IntroductoryPricingArray = explode(',',$IntroductoryPricing);
         //print_r($IntroductoryPricingArray); 
         if(!empty($IntroductoryPricingArray)){
                        $IntroAPR_PARM='';
            foreach($IntroductoryPricingArray as $introApr){

                if($introApr==1){
                  $IntroAPR_PARM.=';purchase_introductory_apr,notnull';  
                }if($introApr==2){
                     $IntroAPR_PARM.=';purchase_introductory_apr,null'; 
                }
                if($introApr==3){
                     $IntroAPR_PARM.=';balance_transfer_introductory_apr,notnull'; 
                }
                if($introApr==4){
                     $IntroAPR_PARM.=';balance_transfer_introductory_apr,null'; 
                }
            }
            if($IntroAPR_PARM!=''){
                                $IntroAPR_PARM_All=trim($IntroAPR_PARM,';');

                                }
         }
     }
    return $IntroAPR_PARM_All; 
}

function get_python_like_previous_month($date_str) {
    $input_date = new DateTime($date_str);
    // Get current day, month, and year
    $current_day = (int) $input_date->format('d');
    $current_month = (int) $input_date->format('m');
    $current_year = (int) $input_date->format('Y');
    $prev_month = ($current_month == 1) ? 12 : $current_month - 1;
    $prev_year = ($current_month == 1) ? $current_year - 1 : $current_year;
    $last_day_prev_month = (int) date('t', strtotime("$prev_year-$prev_month-01"));
    $prev_day = min($current_day, $last_day_prev_month);
    $prev_month_date = new DateTime();
    $prev_month_date->setDate($prev_year, $prev_month, $prev_day);
 
    return $prev_month_date->format('Y-m-d');
}
