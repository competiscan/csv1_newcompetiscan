<?php

require_once __DIR__ . '/../vendor/autoload.php';

function doQuerytestsphinx($search_id, $docount = false, $addedtodatabaseover = '', $dograph = false, $bid = -1, $relev = false, $expans = false, $unapproved = false, $clear_ps = false, $search_panelist_date_over = -1, $search_values = array(), $sess_userID = 0) {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm, $SPHINX_name;
    $totalfetchid = '';
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
    $_SESSION['totalfetchid'] = 0;
    $productidsarray = array();
    $forceIndexaddedToDatabase = '';
    //$forceIndexaddedToDatabase	=	" force index(idx_comp2) ";
    $forceindex = '';

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
        $otherArray = array('company' => $company, 'affinity_association' => $affinity_association, 'addedToDatabase' => $addedToDatabase, 'month' => $month, 'pd.approved_date' => $approved_date, 'pd.search_competi_id' => $search_competi_id, 'pd.ApplicationType_mult' => $ApplicationType_mult, 'pd.cg_id' => $cg_id, 'rstate' => $rstate, 'pcountry' => $pcountry);
        $panelistArray = array('ppageID' => $age, 'pincomeID' => $income_mult, 'isBiz' => $isBiz, 'dmap.code' => $DMA_ID_mult, 'ppdate' => $ppdate, 'ppdate_month' => $ppdate_month, 'ppstateID' => $ppstateID, 'pgender' => $pgender, 'Income_Producing_Assets_Segment_Code' => $Income_Producing_Assets_Segment_Code, 'edc_id' => $edc_id_mult);
        if ($productName != '') {
            $keyArray = preg_split('/"\\s+or\\s+"/i', $productName, -1, PREG_SPLIT_NO_EMPTY);
            if (count($keyArray) > 1 || preg_match('/^"([^"]+)"$/', $productName)) {
                foreach ($keyArray as $k => $val) {
                    if (preg_match('/^"([^"]+)"$/', $val, $match) || preg_match('/^([^"]+)"$/', $val, $match) || preg_match('/^"([^"]+)$/', $val, $match)) {
                        $keyArray[$k] = $match[1];
                    }
                }
                $multExactArray['pd.productName'] = $keyArray;
            } else {
                $likeArray['pd.productName'] = $productName;
            }
        }
        if ($enhance) {
            $exactArray = array_merge($exactArray, array('pd.cardStatus' => $cardStatus, 'pd.personalization' => $personalization, 'pd.gender' => $gender, 'pd.offerOrigin' => $offerOrigin, 'pd.compaignLanguage' => $compaignLanguage));
            $multExactArray = array_merge($multExactArray, array('pd.mTypeID' => $mTypeID, 'pd.delmethid' => $delmethid_mult));
            $likeArray = array_merge($likeArray, array('pd.external_link' => $external_link));
            $multArray = array_merge($multArray, array('pd.state' => $state, 'pd.agentCommunicationID' => $agentCommunicationID, 'pd.groupSize' => $groupSize, 'pd.fa_ids' => $fa_id_mult, 'pd.tl_ids' => $tl_id_mult, 'pd.electronicID' => $electronicID_mult, 'pd.businessContent' => $businessContent_mult, 'pd.multiculturalmarkets' => $multiculturalmarkets_mult, 'pd.responseMechID' => $responseMechID_mult, 'pd.FeeProductType' => $FeeProductType, 'pd.riders' => $riders_mult, 'pd.IssueTypeID' => $IssueTypeID_mult));
            $otherArray = array_merge($otherArray, array('pd.AffinityCategoryID' => $AffinityCategoryID_mult, 'pd.worksiteVoluntary' => $worksiteVoluntary, 'pd.affinityAssociation' => $affinityAssociation, 'pd.siteCatID' => $siteCatID_mult, 'pd.pubTypeID' => $pubTypeID_mult, 'pd.prescription' => $prescription, 'pd.is_affinion' => $is_affinion, 'pd.is_military' => $is_military, 'pd.is_multicultural' => $is_multicultural, 'pd.IntroPricing_mult' => $IntroPricing_mult, 'pd.is_rewards' => $is_rewards, 'pd.RewardsProgramEmphasis_mult' => $RewardsProgramEmphasis_mult, 'pd.is_incentive' => $is_incentive, 'pd.CardNetwork_mult' => $CardNetwork_mult, 'pd.FeeProduct' => $FeeProduct, 'pd.is_mover' => $is_mover, 'pd.OptOutFirmOffer' => $OptOutFirmOffer, 'pd.is_hphsa' => $is_hphsa, 'pd.is_citi' => $is_citi, 'pd.is_CreditCardMentioned' => $is_CreditCardMentioned, 'pd.ERateType' => $ERateType_mult, 'pd.EOfferPrice' => $EOfferPrice_mult, 'pd.ETermLength' => $ETermLength_mult, 'pd.ECancelFee' => $is_ECancelFee, 'Reloadable' => $is_Reloadable, 'isCreditUnion' => $creditUnion));
            //$otherArray = array_merge($otherArray, array('pd.AffinityCategoryID' => $AffinityCategoryID_mult, 'pd.worksiteVoluntary' => $worksiteVoluntary, 'pd.affinityAssociation' => $affinityAssociation, 'pd.siteCatID' => $siteCatID_mult, 'pd.pubTypeID' => $pubTypeID_mult, 'pd.prescription' => $prescription, 'pd.is_affinion' => $is_affinion, 'pd.is_military' => $is_military, 'pd.is_multicultural' => $is_multicultural, 'pd.IntroPricing_mult' => $IntroPricing_mult, 'pd.is_rewards' => $is_rewards, 'pd.RewardsProgramEmphasis_mult' => $RewardsProgramEmphasis_mult, 'pd.is_incentive' => $is_incentive, 'pd.CardNetwork_mult' => $CardNetwork_mult, 'pd.FeeProduct' => $FeeProduct, 'pd.is_mover' => $is_mover, 'pd.OptOutFirmOffer' => $OptOutFirmOffer, 'pd.is_hphsa' => $is_hphsa, 'pd.is_citi' => $is_citi, 'pd.is_CreditCardMentioned' => $is_CreditCardMentioned, 'pd.ERateType' => $ERateType_mult, 'pd.EOfferPrice' => $EOfferPrice_mult, 'pd.ETermLength' => $ETermLength_mult, 'pd.ECancelFee' => $is_ECancelFee,  'pd.isCreditUnion' => $creditUnion));
        }
        //print_r($multExactArray);exit;


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
                if ($field == 'pd.mPanelID') {
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
                    //$alias = "scsc$j";
                    $alias = "pd";
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
                           // $alias = "scsc$j";
                            $alias = "pd";
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
                                   // $alias = "scsc$j";
                                    $alias = "pd";
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
                                       // $alias = "scsc$j";
                                         $alias = "pd";
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

        //echo $where;exit;
       print_r($exactervalsArray);
        foreach ($exactervalsArray as $f => $a) {
            $combos = array();
            if (count($a) <= 6) {
                pc_permute($a, $combos);
            }
            $wherec = '';
            
            foreach ($combos as $c) {
                $v = implode(',', $c);
                if($f=='sectorID'){
                    $f='pd.sectorID';
                }
                if($f=='categoryID'){
                    $f='pd.categoryID';
                }
                if($f=='subCategoryID'){
                    $f='pd.subCategoryID';
                }
               // $f="pd.".$f;
                $wherec .= " $f='$v' OR ";
            }
            if ($wherec != '') {
                $where .= '(' . substr($wherec, 0, -4) . ') AND ';
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
                
                if ($field == 'ppdate') {
                    //$forceIndexaddedToDatabase = " force index(idx_comp8) ";
                    $forceIndexaddedToDatabase = " force index(idx_addedToDatabase) ";
                    //$forceindex = 1;
                    //$forceIndexaddedToDatabase	=	" force index(idx_addedToDatabase) ";
                    if ($val == 'week')
                        $where .= ' ( pd.addedToDatabase>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 7 DAY),\' 00:00:00\')) AND ';
                    elseif ($val == '2week')
                        $where .= ' ( pd.addedToDatabase>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 14 DAY),\' 00:00:00\')) AND ';
                    elseif ($val == '1month')
                        $where .= ' ( pd.addedToDatabase>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 1 MONTH),\' 00:00:00\')) AND ';
                    elseif ($val == '3month')
                        $where .= ' (pd.addedToDatabase>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 3 MONTH),\' 00:00:00\')) AND ';
                    elseif ($val == '6month')
                        $where .= ' ( pd.addedToDatabase>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 6 MONTH),\' 00:00:00\')) AND ';
                    elseif ($val == '1year')
                        $where .= ' ( pd.addedToDatabase>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 1 YEAR),\' 00:00:00\')) AND ';
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
                    //$where .= " ((pp.ppdate BETWEEN '$month_1-01 00:00:00' AND '$month_2-31 23:59:59') OR (pd.addedToDatabase BETWEEN '$month_1-01 00:00:00' AND '$month_2-31 23:59:59')) AND ";
                    $where .= " (pd.addedToDatabase BETWEEN '$month_1-01 00:00:00' AND '$month_2-31 23:59:59' ) AND ";
                    
                    ##### removing force index idx_comp8 and use idx_addedToDatabase ####
                    //$forceIndexaddedToDatabase = " force index(idx_comp8) ";
                    $forceindex = 1;
                    $forceIndexaddedToDatabase	=	" force index(idx_addedToDatabase) ";
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
             ##### removing force index idx_comp8 and use idx_addedToDatabase ####
            //$forceIndexaddedToDatabase = " force index(idx_comp8) ";
            $forceindex = 1;
            $forceIndexaddedToDatabase	=	" force index(idx_addedToDatabase) ";
            $where .= " pd.addedToDatabase>='$addedtodatabaseover' AND ";
            $filter_range[] = array('dts_date', strtotime($addedtodatabaseover), time());
        }
        //arvind
        //$where .= " pd.addedToDatabase<=NOW() AND (pd.productStatus=1";


        foreach ($otherArray as $field => $val) {
            if ($val != '') {



                if ($field == 'addedToDatabase' && $addedtodatabaseover == '') {
                    //$forceIndexaddedToDatabase = " force index(idx_comp8) ";
                     $forceIndexaddedToDatabase = " force index(idx_addedToDatabase) ";
                    //$forceindex = 1;
                    //$forceIndexaddedToDatabase	=	" force index(idx_addedToDatabase) ";
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
                     ##### removing force index idx_comp8 and use idx_addedToDatabase ####
                    //$forceIndexaddedToDatabase = " force index(idx_comp8) ";
                    $forceIndexaddedToDatabase	=	" force index(idx_addedToDatabase) ";

                    $forceindex = '1';
                    $monthArray = explode(',', $val);
                    $month_1 = $monthArray[0];
                    $month_2 = $monthArray[1];
                    if ($month_1 == '') {
                        $month_1 = $month_2;
                    } elseif ($month_2 == '') {
                        $month_2 = $month_1;
                    }
                    $where .= " (pd.addedToDatabase BETWEEN '$month_1-01 00:00:00' AND '$month_2-31 23:59:59') AND ";

                    //$forceIndexaddedToDatabase = " force index(idx_comp8) ";
                    // $forceIndexaddedToDatabase	=	" force index(idx_addedToDatabase) ";
                    //$forceindex = 1;
                    $filter_range[] = array('pd.dts_date', strtotime("$month_1-01 00:00:00"), strtotime("$month_2-31 23:59:59"));
                } elseif ($field == 'pd.worksiteVoluntary' || $field == 'pd.affinityAssociation' || $field == 'pd.prescription' || $field == 'pd.is_affinion' || $field == 'pd.is_military' || $field == 'pd.FeeProduct' || $field == 'pd.is_mover' || $field == 'pd.OptOutFirmOffer' || $field == 'pd.is_hphsa' || $field == 'pd.is_citi' || $field == 'pd.ECancelFee' || $field == 'Reloadable' || $field == 'isCreditUnion') {
                    //	elseif ($field == 'pd.worksiteVoluntary' || $field == 'pd.affinityAssociation' || $field == 'pd.prescription' || $field == 'pd.is_affinion' || $field == 'pd.is_military' || $field == 'pd.FeeProduct' || $field == 'pd.is_mover' || $field == 'pd.OptOutFirmOffer' || $field == 'pd.is_hphsa' || $field == 'pd.is_citi' || $field == 'pd.ECancelFee' || $field == 'isCreditUnion') {
                    if (!empty($val)) {
                        if ($val == 1) {
                            $fieldval = 1;
                        } elseif ($val == 2) {
                            $fieldval = 0;
                        }
                        if ($field == 'pd.OptOutFirmOffer') {
                           
                            $where .= " pd.is_prescreen=$fieldval AND ";
                            //}
                        } else {
                            $where .= " $field=$fieldval AND ";
                        }
                        if ($field == 'pd.ECancelFee') {
                            $ejoin = ' JOIN pd_cscan_energy_vw ev ON (pd.productID=ev.productID)';
                        } elseif ($field == 'Reloadable') {
                            $pcjoin = ' JOIN pd_cscan_payment_cards_vw  ON (pd.productID=pd_cscan_payment_cards_vw.productID)';
                        } elseif ($field == 'isCreditUnion') {
                            $cjoin = " JOIN pd_product_company_vw pcmv ON (pcmv.productID=pd.productID) ";
                            //$ccjoin = " JOIN cscan_company ON (cscan_company_product.companyID=cscan_company.companyID) ";
                        }
                    }
                } elseif ($field == 'pd.is_multicultural') {
                    if ($val == 1) {
                        $where .= " pd.multiculturalmarkets<>'' AND ";
                    }
                } elseif ($field == 'pd.is_incentive') {
                    if ($val == 1) {
                        $where .= " pd.incentive<>'' AND ";
                    }
                } elseif ($field == 'pd.is_rewards') {
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
                } elseif ($field == 'pd.is_CreditCardMentioned') {
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
                } elseif ($field == 'pd.siteCatID' || $field == 'pd.pubTypeID' || $field == 'pd.ApplicationType_mult') {
                    $field2 = '';
                    if ($field == 'pd.siteCatID') {
                        $field = 'cscan_sites.sites_category_id';
                        $ojoin .= ',cscan_sites_product,cscan_sites';
                        $owhere .= ' AND pd.productID=cscan_sites_product.productID AND cscan_sites_product.sites_id=cscan_sites.sites_id';
                    } elseif ($field == 'pd.pubTypeID') {
                        $field = 'cscan_publication.print_typeID';
                        $ojoin .= ',cscan_publication_product,cscan_publication';
                        $owhere .= ' AND pd.productID=cscan_publication_product.productID AND cscan_publication_product.publicationID=cscan_publication.publicationID';
                    } elseif ($field == 'pd.ApplicationType_mult') {
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
                } elseif ($field == 'pd.IntroPricing_mult') {
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
                } elseif ($field == 'pd.approved_date') {
                    $splitArray = explode(',', $val);
                    $split_1 = $splitArray[0];
                    $split_2 = $splitArray[1];
                    if ($split_1 == '') {
                        $split_1 = $split_2;
                    } elseif ($split_2 == '') {
                        $split_2 = $split_1;
                    }
                    $where .= " ($field BETWEEN '$split_1' AND '$split_2') AND ";
                }

                if ($field == 'pd.search_competi_id' || $field == 'pd.cg_id') {

                    $panelist_ids = array();
                    if ($field == 'pd.cg_id') {
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
                    //for remove the panaelist condition             
                    if ($pjoin_vw != '') {
                        $pjoin = $pjoin_vw . ' ON (pd.productID=pp.productID' . $pjoin_filter . ')';

                        $where .= " pp.panelist_id IN (" . implode(',', $panelist_ids) . ") AND ";
                    }
                    // end for remove the panaelist condition 
                    
                } elseif ($field == 'pd.CardNetwork_mult' || $field == 'pd.RewardsProgramEmphasis_mult') {
                    $sectorIDArray = explode(',', $sectorID);
                    $in = false;
                    $field2 = '';
                    if ($field == 'pd.CardNetwork_mult') {
                        if (in_array(90, $sectorIDArray)) {
                            $in = true;
                            $field = 'pd_cscan_payment_cards_vw.CardNetwork';
                            $pcjoin = ' JOIN pd_cscan_payment_cards_vw ON (pd.productID=pd_cscan_payment_cards_vw.productID)';
                        }
                        if (in_array(87, $sectorIDArray)) {
                            if ($in) {
                                //  $field2 = 'pd_banking_vw.BankingCardNetwork';
                            } else {
                                //  $field = 'pd_banking_vw.BankingCardNetwork';
                            }
                            $bjoin = ' JOIN pd_banking_vw ON (pd.productID=pd_banking_vw.productID)';
                        }
                    } elseif ($field == 'pd.RewardsProgramEmphasis_mult') {
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
                } elseif ($field == 'pd.ERateType' || $field == 'pd.EOfferPrice' || $field == 'pd.ETermLength') {
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
                      //  echo"hhh";exit;
                    } else {
                        if ($field == 'affinity_association') {
                            $caat = 'affinity';
                            $vw_where = "pd_affinity_product_vw";
                            
                            ################ remove the force index idx_comp7 ##########
                           // $forceIndexaddedToDatabase = "  force index(idx_comp7) ";
                            
                            // $forceindex = 1;
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
                } elseif ($field == 'pd.AffinityCategoryID') {
                    $cos = explode(',', $val);
                    if (empty($cos[0])) {
                        unset($cos[0]);
                    }
                    $afjoin = " JOIN pd_affinity_product_vw ON (pd_affinity_product_vw.productID=pd.productID) ";
                    $affjoin = " JOIN cscan_affinity_vw ";

                    // $afjoin = " JOIN cscan_affinity_product ON (cscan_affinity_product.productID=pd.productID) ";
                    $affjoin = " JOIN cscan_affinity_vw ON (cscan_affinity_vw.affinityID=pd_affinity_product_vw.affinityID ) JOIN cscan_aff_cat ON (cscan_affinity_vw.affinityID=cscan_aff_cat.affinityID)  ";
                     ################ remove the force index idx_comp7 ##########
                    //$forceIndexaddedToDatabase = "  force index(idx_comp7) ";
                    $where .= " cscan_aff_cat.AffinityCategoryID IN (" . implode(',', $cos) . ") AND ";
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

                // if ($numrow == 0 && !empty($SPHINX_name)) {
                if (!empty($SPHINX_name)) {
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

                            ############ remove this for search orc with date betwwen ###########  
                            /* foreach ($filter_range as $fr) {
                              if (!$s->setFilterRange($fr[0], $fr[1], $fr[2])) {
                              sphinxErr(__LINE__, $s, 'setFilterRange', $fr[0] . ', ' . $fr[1] . ', ' . $fr[2]);
                              }
                              }
                             */
                            ############ end remove this for search orc with date betwwen ########### 
                            if (!$result = $s->query($ps, $inds)) {
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
                                // $_SESSION['totalfetchid']    =   $result['total_found'];

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

                                    ############ remove this for search orc with date betwwen ########### 
                                    /*
                                      foreach ($filter_range as $fr) {
                                      if (!$s->setFilterRange($fr[0], $fr[1], $fr[2])) {
                                      sphinxErr(__LINE__, $s, 'setFilterRange', $fr[0] . ', ' . $fr[1] . ', ' . $fr[2]);
                                      }
                                      }

                                     */
                                    ############ end remove this for search orc with date betwwen ########### 
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


                }

                if ($search_id != '') {
                    //  $_SESSION['totalfetchid']   =  count(array_unique($productidsarray));
                    if (!empty($productidsarray)) {
                        $forceIndexaddedToDatabase = "  force index(idx_productID) ";
                        $forceindex = 1;

                        $andUnion = '';
                        $newarray = array_chunk($productidsarray, 10000);
                        // echo count($productidsarray).'===='.count($newarray); exit;
                        for ($u = 2; $u < 100; $u++) {
                            if (count($newarray) >= $u) {

                                $andUnion.="union ( SELECT dd.productID   FROM  pd_scsc_state_vw dd  WHERE dd.productID IN(" . implode(',', ($newarray[$u - 1])) . "))";
                            }
                        }
                        //$wheresearchproduct =   " AND pd.productID in (".$productidsstring.") ";

                        $andcond = " select B.productID FROM  (SELECT dd.productID FROM  pd_scsc_state_vw dd  WHERE dd.productID IN(" . implode(',', $newarray[0]) . ") " . $andUnion . ")B";
                        $wheresearchproduct = " AND pd.productID in (" . $andcond . ") ";
                    }

                    //print_r($result);echo"hhhh";exit;
                    //if(empty($productidsarray) && isset($result['total_found']) && $result['total_found']=='0'){
                    if (empty($productidsarray)) {
                        $andcond = '-1';
                        $wheresearchproduct = " AND pd.productID in (" . $andcond . ") ";
                    }
                }
                //$ocrtext .= ' JOIN cscan_search_product sp ON(  pd.productID=sp.productID)';
                //$ocrtext = ' ';
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

    // SQL_NO_CACHE
    if ($docount) {
        $selectQuery = " SELECT ";
        $selectQuery .= "COUNT(DISTINCT pd.productID)";
        $sortby = '';
        if ($forceindex != '')
            $forceIndexaddedToDatabase = '';
    } else {
        //$selectQuery = "select D.* from ( SELECT ";
        $selectQuery = " SELECT ";
        if ($dograph != 0) {
            $sortby = '';
        }

        $matchtext = ($matchtext != '') ? ",$matchtext AS relevancy" : ",1 AS relevancy";

        $mintel = new \HS\Mintel();
        $mintel_set = $mintel->getFields();
        $mintel_set_2 = $mintel->getFieldSet('incentive_set_2');
        $mintel_set_3 = $mintel->getFieldSet('incentive_set_3');

        $incentive_set = implode(',pd.', array_merge(array_keys($mintel_set), array_keys($mintel_set_2), array_keys($mintel_set_3)));

        $selectQuery .= "DISTINCT pd.productID AS theproductID,pd.entryID_sort1,
      pd.entryID_sort2,pd.mChannelID, pd.mPanelID,pd.productHeadline,pd.sectorID, pd.categoryID,pd.subCategoryID,pd.entryID,
            pd.addedToDatabase, pd.company,pd.productName, pd.compaignLanguage, pd.firstSeen,pd.lastSeen, pd.mTypeID,pd.state,pd.agentCommunicationID,pd.secondCompany, pd.variantID,pd.affinityAssociation,pd.age,pd.gender,pd.incomeID,pd.publication,pd.isVariant,pd.isDemographic,pd.isInsight,pd.fa_ids, pd.tl_ids,pd.isFICO,pd.incentive_ongoing,pd.incentive,pd.$incentive_set,pd.delmethid,pd.responseMechID,pd.FeeProductType,pd.external_updates,pd.external_fans, pd.external_link,pd.prescription,pd.is_hphsa,pd.subSubCategoryID,
                pd.OfferExpiryDate,pd.is_citi, pd.riders,pd.is_prescreen,pd.isSurvey,pd.IssueTypeID, pd.traffic_sources,pd.social_media_name,pd.worksiteVoluntary,pd.groupSize$matchtext";
    }

    $selecttable = 'pd_scsc_vw';
    if ($statevwtable != '') {
        $selecttable = $statevwtable;
    }

    $selectQuery .= " FROM " . $selecttable . " pd $forceIndexaddedToDatabase$ocrtext$pjoin$cjoin$ccjoin$afjoin$affjoin$dmajoin$edcjoin$pcjoin$bjoin$ejoin$mljoin$rjoin$tljoin$ajoin$sjoin$ojoin $where$owhere$wheresearchproduct ";

    if ($dograph != 0) {
        $field = getDoGraph($dograph);
        $selectQuery = "SELECT COUNT($field) AS field_count,$field AS field_name FROM ($selectQuery) as t1 GROUP BY $field"; // SQL_NO_CACHE ORDER BY field_count DESC,field_name ASC
    }

     //echo $selectQuery;exit;
    return array($selectQuery, $saved, $sortby);
}

function doQuerySort_test3($sort) {
    $dorelev = false;
    $doexpans = false;
    switch ($sort) {
        case 1:
            $orderby = ' ORDER BY D.productHeadline ASC, D.entryID_sort1 DESC, D.entryID_sort2 DESC';
            break;
        case -1:
            $orderby = ' ORDER BY D.productHeadline DESC, D.entryID_sort1 ASC, D.entryID_sort2 ASC';
            break;
        case 2:
            $orderby = ' ORDER BY D.company ASC, D.entryID_sort1 DESC, D.entryID_sort2 DESC';
            break;
        case -2:
            $orderby = ' ORDER BY D.company DESC, D.entryID_sort1 ASC, D.entryID_sort2 ASC';
            break;
        case 3:
            $orderby = ' ORDER BY D.entryID_sort1 ASC, D.entryID_sort2 ASC';
            break;
        case -3:
            $orderby = ' ORDER BY D.entryID_sort1 DESC, D.entryID_sort2 DESC';
            break;
        case 4:
            $orderby = ' ORDER BY D.relevancy ASC, D.entryID_sort1 ASC, D.entryID_sort2 ASC';
            $dorelev = true;
            break;
        case -4:
            $orderby = ' ORDER BY D.relevancy DESC, D.entryID_sort1 DESC, D.entryID_sort2 DESC';
            $dorelev = true;
        case 5:
            $orderby = ' ORDER BY D.relevancy ASC, D.entryID_sort1 ASC, D.entryID_sort2 ASC';
            $dorelev = true;
            $doexpans = true;
            break;
        case -5:
            $orderby = ' ORDER BY D.relevancy DESC, D.entryID_sort1 DESC, D.entryID_sort2 DESC';
            $dorelev = true;
            $doexpans = true;
            break;
        case 6:
            $orderby = ' ORDER BY D.isDemographic DESC,D.entryID_sort1 DESC, D.entryID_sort2 DESC';
            break;
        case -6:
            $orderby = ' ORDER BY D.isDemographic ASC,D.entryID_sort1 ASC, D.entryID_sort2 ASC';
            break;
        case 7:
            $orderby = ' ORDER BY D.isVariant DESC, D.entryID_sort1 DESC, D.entryID_sort2 DESC';
            break;
        case -7:
            $orderby = ' ORDER BY D.isVariant ASC, D.entryID_sort1 ASC, D.entryID_sort2 ASC';
            break;
        case 8:
            $orderby = ' ORDER BY D.isInsight DESC, D.entryID_sort1 DESC, D.entryID_sort2 DESC';
            break;
        case -8:
            $orderby = ' ORDER BY D.isInsight ASC, D.entryID_sort1 ASC, D.entryID_sort2 ASC';
            break;
        case 9:
            $orderby = ' ORDER BY D.isFICO DESC, D.entryID_sort1 DESC, D.entryID_sort2 DESC';
            break;
        case -9:
            $orderby = ' ORDER BY D.isFICO ASC, D.entryID_sort1 ASC, D.entryID_sort2 ASC';
            break;
        case 10:
            $orderby = ' ORDER BY D.isSurvey DESC, D.entryID_sort1 DESC, D.entryID_sort2 DESC';
            break;
        case -10:
            $orderby = ' ORDER BY D.isSurvey ASC, D.entryID_sort1 ASC, D.entryID_sort2 ASC';
            break;
        default:
            $orderby = '';
    }

    return array($orderby, $dorelev, $doexpans);
}

function addForceIndex($indexiname = '', $requireindex = '') {
    $returnindex = '';
    if ($indexiname != '' && $requireindex != '') {
        $returnindex = ' force index (' . $indexiname . ')';
    }

    return $returnindex;
}

function addForceIndexCount($countindex = '', $requireindexcount = '') {
    $returnindex = '';

    if ($countindex != '' && $requireindexcount != '') {
        $returnindex = ' force index (' . $countindex . ')';
    }
    return $returnindex;
}
