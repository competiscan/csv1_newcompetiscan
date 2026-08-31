<?php

require_once __DIR__ . '/../vendor/autoload.php';

function doQuery_test5($search_id, $docount = false, $addedtodatabaseover = '', $dograph = false, $bid = -1, $relev = false, $expans = false, $unapproved = false, $clear_ps = false, $search_panelist_date_over = -1, $search_values = array(), $sess_userID = 0) {
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
    $productidsarray    =   array();  
    $wheresearchproduct ='';
    ################### for latest changes use in ######################
    $wherecondition = '';
    $where .= " (productStatus=1";
    if ($unapproved) {
        $where .= " OR productStatus=2";
    }
    $where .= ") AND addedToDatabase<=NOW() AND ";
    ################### end for latest changes use in ######################   



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



        $partsArray2 = array();
        $j = 0;
        if ($andorArray['sectorID'] == 'AND') {
            $alias = "scsc$j";
        } else {
            $alias = "scsc";
        }
        $seccatsubArray = get_seccatsub($sectorID, $categoryID, $subCategoryID, $subSubCategoryID);

        foreach ($seccatsubArray as $sid => $cArray) {
            //$part1 = $alias . ".scsc_sectorID=$sid";
            $part1 = "CONCAT(',',sectorID,',') REGEXP ',$sid,'";
            if ($scsc_primary) {
                // $part1 .= ' AND ' . $alias . ".scsc_sort=1";
            }
            if (in_array('sectorID', $exacterArray)) {
                $exactervalsArray['sectorID'][] = $sid;
            }
            if (count($cArray) == 0) {
                $partsArray2[] = '(' . $part1 . ')';
                if ($andorArray['sectorID'] == 'AND') {
                    ###################  remove cscan_scsc_product table  ######################
                    //$sect_j .= ' JOIN cscan_scsc_product as ' . $alias . ' ON (pd.productID=' . $alias . '.productID)';
                    $j++;
                    $alias = "scsc$j";
                }
            } else {
                foreach ($cArray as $cid => $scArray) {
                    //$part2 = $alias . ".scsc_categoryID=$cid";
                    $part2 = "CONCAT(',',categoryID,',') REGEXP ',$cid,'";
                    if (in_array('sectorID', $exacterArray)) {
                        $exactervalsArray['categoryID'][] = $cid;
                    }
                    if (count($scArray) == 0) {
                        $partsArray2[] = '(' . $part1 . ' AND ' . $part2 . ')';
                        if ($andorArray['sectorID'] == 'AND') {
                            ###################  remove cscan_scsc_product table  ######################
                            //$sect_j .= ' JOIN cscan_scsc_product as ' . $alias . ' ON (pd.productID=' . $alias . '.productID)';
                            $j++;
                            $alias = "scsc$j";
                        }
                    } else {
                        foreach ($scArray as $scid => $sscArray) {
                            //$part3 = $alias . ".scsc_subCategoryID=$scid";
                            $part3 = "CONCAT(',',subCategoryID,',') REGEXP ',$scid,'";
                            if (in_array('sectorID', $exacterArray)) {
                                $exactervalsArray['subCategoryID'][] = $scid;
                            }
                            if (count($sscArray) == 0) {
                                $partsArray2[] = '(' . $part1 . ' AND ' . $part2 . ' AND ' . $part3 . ')';
                                if ($andorArray['sectorID'] == 'AND') {
                                    ###################  remove cscan_scsc_product table  ######################
                                    //$sect_j .= ' JOIN cscan_scsc_product as ' . $alias . ' ON (pd.productID=' . $alias . '.productID)';
                                    $j++;
                                    $alias = "scsc$j";
                                }
                            } else {
                                foreach ($sscArray as $sscid => $ssscArray) {
                                    // $part4 = $alias . ".scsc_subSubCategoryID=$sscid";
                                    $part4 = "CONCAT(',',subSubCategoryID,',') REGEXP ',$sscid,'";
                                    if (in_array('sectorID', $exacterArray)) {
                                        $exactervalsArray['subSubCategoryID'][] = $sscid;
                                    }
                                    $partsArray2[] = '(' . $part1 . ' AND ' . $part2 . ' AND ' . $part3 . ' AND ' . $part4 . ')';
                                    if ($andorArray['sectorID'] == 'AND') {
                                        ###################  remove cscan_scsc_product table  ######################
                                        //$sect_j .= ' JOIN cscan_scsc_product as ' . $alias . ' ON (pd.productID=' . $alias . '.productID)';
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
                        if (in_array(266, $sectorIDArray)) {
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

                    if ($val != '') {

                        if (!empty($field2)) {
                            $where .= "( $field in (" . $DRW->real_escape_string($val) . " ) OR ";
                            $where .= " $field2 in(" . $DRW->real_escape_string($val) . ")) AND  ";
                        } else {
                            $where .= " $field in (" . $DRW->real_escape_string($val) . " ) AND ";
                        }
                    }




//                    $where .= " (";
//                    foreach ($tmpArray as $v) {
//                        if ($v != '') {
//                            $where .= " $field='" . $DRW->real_escape_string($v) . "' OR ";
//                            if (!empty($field2)) {
//                                $where .= " $field2='" . $DRW->real_escape_string($v) . "' OR ";
//                            }
//                        }
//                    }
//                    $where = substr($where, 0, -4);
//                    $where .= ") AND ";
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
                        $sjoin = " JOIN cscan_product_detail_state ignore index(pds_state_index) ON (cscan_product_detail_state.productID=pd.productID)";
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
                    //echo $field;
                    $tmpwhere = '';
                    $tmpArray = explode(',', $val);


                    if ($val != '') {
                        if ($field == 'Income_Producing_Assets_Segment_Code') {
                            $where .= $field . " in (" . $val . ") AND ";
                        } else if ($field == 'dmap.code') {
                            $where .= $field . " in (" . substr($val, 0, -1) . ") AND ";
                        } else {

                            $where .= " $field in(" . $val . " ) AND ";
                        }
                    }



                    foreach ($tmpArray as $v) {
                        if ($v != '') {

                            if ($field == 'ppstateID') {
                                $tmpwhere .= " pp.ppstateID=" . (int) $v . " OR (state like '%{$v}%' AND state REGEXP '[[:<:]]{$v}[[:>:]]') OR ";
                            }


//                            if ($field == 'Income_Producing_Assets_Segment_Code' || $field == 'dmap.code') {
//                                $tmpwhere .= " $field='" . $v . "' OR ";
//                            } elseif ($field == 'ppstateID') {
//                                $tmpwhere .= " pp.ppstateID=" . (int) $v . " OR (state like '%{$v}%' AND state REGEXP '[[:<:]]{$v}[[:>:]]') OR ";
//                            } else {
//                                $tmpwhere .= " $field=" . (int) $v . " OR ";
//                            }
                        }
                    }
                    if ($field == 'isBiz') {
                        $awhere .= $tmpwhere;
                    } else {
                        // $where .= " (" . substr($tmpwhere, 0, -4) . ") AND ";
                    }
                }


//                else {
//                    $tmpwhere = '';
//                    $tmpArray = explode(',', $val);
//                    foreach ($tmpArray as $v) {
//                        if ($v != '') {
//                            if ($field == 'Income_Producing_Assets_Segment_Code' || $field == 'dmap.code') {
//                                $tmpwhere .= " $field='" . $v . "' OR ";
//                            } elseif ($field == 'ppstateID') {
//                                $tmpwhere .= " pp.ppstateID=" . (int) $v . " OR (state like '%{$v}%' AND state REGEXP '[[:<:]]{$v}[[:>:]]') OR ";
//                            } else {
//                                $tmpwhere .= " $field=" . (int) $v . " OR ";
//                            }
//                        }
//                    }
//                    if ($field == 'isBiz') {
//                        $awhere .= $tmpwhere;
//                    } else {
//                        $where .= " (" . substr($tmpwhere, 0, -4) . ") AND ";
//                    }
//                }
            }
        }
        if ($awhere != '') {
            $where .= " (" . substr($awhere, 0, -4) . ") AND ";
        }
        if ($addedtodatabaseover != '') {
            $where .= " addedToDatabase>='$addedtodatabaseover' AND ";
            $filter_range[] = array('dts_date', strtotime($addedtodatabaseover), time());
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
                if(!is_array($val)){
                     $where .= $field . " in (" . $val . ") AND ";
                }else{
                    
                ################ for the new code changes ###########################
                $tmpwhere = '';
                if (is_array($val)) {
                    $tmpArray = $val;
                } else {
                    $tmpArray = explode(',', $val);
                }
                foreach ($tmpArray as $v) {
                    if ($v != '') {
                        //$tmpwhere .= " $field";
                        if (in_array($field, $noterArray)) {
                            $tmpwhere .= '<>';
                        } else {
                           // $tmpwhere .= '=';
                        }
                        $tmpwhere .= "'" . $DRW->real_escape_string($v) . "'";
                        if (in_array($field, $noterArray)) {
                            $tmpwhere .= ' AND ';
                        } else {
                            $tmpwhere .= ' , ';
                        }
                    }
                }
                if ($field == 'mPanelID') {
                    $awhere .= $tmpwhere;
                } else {
                    $where .= $field." in  (" . substr($tmpwhere, 0, -2) . ") AND ";
                }
                }
                ################ for the new code changes ###########################
            }
        }
//echo $where;exit;
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

        ###################  remove cscan_scsc_product table  ######################
        // $sect_j .= ' JOIN cscan_scsc_product as scsc ON (pd.productID=scsc.productID)';
        $partsArray = array();
        $seccatsubArray = get_seccatsub(implode(',', $_SESSION['sess_sector']), implode(',', $_SESSION['sess_category']), implode(',', $_SESSION['sess_subcategory']));


        foreach ($seccatsubArray as $sid => $cArray) {
            //$part1 = "scsc.scsc_sectorID=$sid";
            $part1 = "CONCAT(',',sectorID,',') REGEXP ',$sid,'";
            //$partsArray[] = '(' . $part1 . ' AND scsc.scsc_categoryID=0 AND scsc.scsc_subCategoryID=0)';

            $partsArray[] = '(' . $part1 . " AND CONCAT(',',categoryID,',') REGEXP  ',0,'  AND CONCAT(',',subCategoryID,',') REGEXP  ',0,')";
            foreach ($cArray as $cid => $scArray) {
                // $part2 = "scsc.scsc_categoryID=$cid";
                $part2 = "CONCAT(',',categoryID,',') REGEXP ',$cid,'";
                //$partsArray[] = '(' . $part1 . ' AND ' . $part2 . ' AND scsc.scsc_subCategoryID=0)';
                $partsArray[] = '(' . $part1 . ' AND ' . $part2 . " AND CONCAT(',',subCategoryID,',') REGEXP  ',0,')";
                foreach ($scArray as $scid => $a) {
                    //$part3 = "scsc.scsc_subCategoryID=$scid";
                    //$part3 = "scsc.scsc_subCategoryID=$scid";
                    $part3 = "CONCAT(',',subCategoryID,',') REGEXP ',$scid,'";
                    $partsArray[] = '(' . $part1 . ' AND ' . $part2 . ' AND ' . $part3 . ')';
                }
            }
        }



        if (count($partsArray) > 0) {
            $where .= '(' . implode(' OR ', $partsArray) . ') AND ';
        }

        ################ for the new code changes ###########################
//        $partsArray2 = array();
//        $j = 0;
//        if ($andorArray['sectorID'] == 'AND') {
//            $alias = "scsc$j";
//        } else {
//            $alias = "scsc";
//        }
//        $seccatsubArray = get_seccatsub($sectorID, $categoryID, $subCategoryID, $subSubCategoryID);
//
//        foreach ($seccatsubArray as $sid => $cArray) {
//            //$part1 = $alias . ".scsc_sectorID=$sid";
//            $part1 = "CONCAT(',',sectorID,',') REGEXP ',$sid,'";
//            if ($scsc_primary) {
//               // $part1 .= ' AND ' . $alias . ".scsc_sort=1";
//            }
//            if (in_array('sectorID', $exacterArray)) {
//                $exactervalsArray['sectorID'][] = $sid;
//            }
//            if (count($cArray) == 0) {
//                $partsArray2[] = '(' . $part1 . ')';
//                if ($andorArray['sectorID'] == 'AND') {
//                    ###################  remove cscan_scsc_product table  ######################
//                    //$sect_j .= ' JOIN cscan_scsc_product as ' . $alias . ' ON (pd.productID=' . $alias . '.productID)';
//                    $j++;
//                    $alias = "scsc$j";
//                }
//            } else {
//                foreach ($cArray as $cid => $scArray) {
//                    //$part2 = $alias . ".scsc_categoryID=$cid";
//                    $part2 = "CONCAT(',',categoryID,',') REGEXP ',$cid,'";
//                    if (in_array('sectorID', $exacterArray)) {
//                        $exactervalsArray['categoryID'][] = $cid;
//                    }
//                    if (count($scArray) == 0) {
//                        $partsArray2[] = '(' . $part1 . ' AND ' . $part2 . ')';
//                        if ($andorArray['sectorID'] == 'AND') {
//                              ###################  remove cscan_scsc_product table  ######################
//                            //$sect_j .= ' JOIN cscan_scsc_product as ' . $alias . ' ON (pd.productID=' . $alias . '.productID)';
//                            $j++;
//                            $alias = "scsc$j";
//                        }
//                    } else {
//                        foreach ($scArray as $scid => $sscArray) {
//                            //$part3 = $alias . ".scsc_subCategoryID=$scid";
//                            $part3 = "CONCAT(',',subCategoryID,',') REGEXP ',$scid,'";
//                            if (in_array('sectorID', $exacterArray)) {
//                                $exactervalsArray['subCategoryID'][] = $scid;
//                            }
//                            if (count($sscArray) == 0) {
//                                $partsArray2[] = '(' . $part1 . ' AND ' . $part2 . ' AND ' . $part3 . ')';
//                                if ($andorArray['sectorID'] == 'AND') {
//                                    ###################  remove cscan_scsc_product table  ######################
//                                    //$sect_j .= ' JOIN cscan_scsc_product as ' . $alias . ' ON (pd.productID=' . $alias . '.productID)';
//                                    $j++;
//                                    $alias = "scsc$j";
//                                }
//                            } else {
//                                foreach ($sscArray as $sscid => $ssscArray) {
//                                   // $part4 = $alias . ".scsc_subSubCategoryID=$sscid";
//                                    $part4 = "CONCAT(',',subSubCategoryID,',') REGEXP ',$sscid,'";
//                                    if (in_array('sectorID', $exacterArray)) {
//                                        $exactervalsArray['subSubCategoryID'][] = $sscid;
//                                    }
//                                    $partsArray2[] = '(' . $part1 . ' AND ' . $part2 . ' AND ' . $part3 . ' AND ' . $part4 . ')';
//                                    if ($andorArray['sectorID'] == 'AND') {
//                                        ###################  remove cscan_scsc_product table  ######################
//                                        //$sect_j .= ' JOIN cscan_scsc_product as ' . $alias . ' ON (pd.productID=' . $alias . '.productID)';
//                                        $j++;
//                                        $alias = "scsc$j";
//                                    }
//                                }
//                            }
//                        }
//                    }
//                }
//            }
//        }
//        if (count($partsArray2) > 0) {
//            $where .= '(' . implode(' ' . $andorArray['sectorID'] . ' ', $partsArray2) . ') AND ';
//        }
        ################ end for the new code changes ###########################

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






//        $where .= " addedToDatabase<=NOW() AND (productStatus=1";
//        if ($unapproved) {
//            $where .= " OR productStatus=2";
//        }
//        $where .= ") AND ";
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
            }
            
            elseif ($searchType == 'ocr2' || $searchType == 'fulltext2' || $searchType == 'ocr_fulltext2') {
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
                       // $inds = 'base_index_prod';
                        if (strpos($sk, '*') !== false) {
                            $inds .= ',base_index_' . $SPHINX_name . 'star' . $add . ',delta_index_' . $SPHINX_name . 'star' . $add;
                        }
                        
                        
                        //echo $inds;exit; 
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
                        //$forceIndexaddedToDatabase = "  force index(idx_productID) ";
                        //$forceindex = 1;

                        $andUnion = '';
                        $newarray = array_chunk($productidsarray, 10000);
                        // echo count($productidsarray).'===='.count($newarray); exit;
                        for ($u = 2; $u < 100; $u++) {
                            if (count($newarray) >= $u) {

                                $andUnion.="union ( SELECT dd.productID   FROM  cscan_product_detail dd  WHERE dd.productID IN(" . implode(',', ($newarray[$u - 1])) . "))";
                            }
                        }
                        //$wheresearchproduct =   " AND pd.productID in (".$productidsstring.") ";

                        $andcond = " select B.productID FROM  (SELECT dd.productID FROM  cscan_product_detail dd  WHERE dd.productID IN(" . implode(',', $newarray[0]) . ") " . $andUnion . ")B";
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
            } 
            
            
            
//            elseif ($searchType == 'ocr2' || $searchType == 'fulltext2' || $searchType == 'ocr_fulltext2') {
//                if ($clear_ps) {
//                    $DRW->query("DELETE FROM cscan_search_product WHERE ID=$search_id", $DRW_main);
//                    $numrow = 0;
//                } else {
//                    $count_save_sql = "SELECT COUNT(*) FROM cscan_search_product WHERE ID=$search_id";
//                    $rs = $DRW->query($count_save_sql, $DRW_read);
//                    $data = $DRW->fetch_row($rs);
//                    $numrow = (int) $data[0];
//                }
//                if ($numrow == 0 && !empty($SPHINX_name)) {
//                    $searchi = 0;
//                    $searches = array();
//                    $searchkeys = array();
//                    if ($searchType == 'ocr_fulltext2') {
//                        if ($searchKey2 != '') {
//                            $searches['fulltext2'] = '2';
//                            $searchkeys['fulltext2'] = $searchKey2;
//                        }
//                        if ($searchKey != '') {
//                            $searches['ocr2'] = '';
//                            $searchkeys['ocr2'] = $searchKey;
//                        }
//                    } elseif ($searchKey != '') {
//                        if ($searchType == 'fulltext2') {
//                            $searches[$searchType] = '2';
//                        } else {
//                            $searches[$searchType] = '';
//                        }
//                        $searchkeys[$searchType] = $searchKey;
//                    }
//                    $searches_count = count($searches);
//                    foreach ($searches as $st => $add) {
//                        if ($search_type_and == 1 && $searches_count > 1) {
//                            $searchi++;
//                        }
//                        $sk = $searchkeys[$st];
//                        $s = startSphinx();
//                        $inds = 'base_index_' . $SPHINX_name . $add . ',delta_index_' . $SPHINX_name . $add; //,base_index_'.$SPHINX_name.'stemmed'.$add.',delta_index_'.$SPHINX_name.'stemmed'.$add.'
//                        if (strpos($sk, '*') !== false) {
//                            $inds .= ',base_index_' . $SPHINX_name . 'star' . $add . ',delta_index_' . $SPHINX_name . 'star' . $add;
//                        }
//                        $ps = parseSphinx($s, $sk);
//                        if (trim($ps) != '') {
//                            $currcount = 0;
//                            $step = $total = 1000;
//                            if (!$s->setLimits(0, 1, 1)) {
//                                sphinxErr(__LINE__, $s, 'setLimits');
//                            }
//                            foreach ($filter_range as $fr) {
//                                if (!$s->setFilterRange($fr[0], $fr[1], $fr[2])) {
//                                    sphinxErr(__LINE__, $s, 'setFilterRange', $fr[0] . ', ' . $fr[1] . ', ' . $fr[2]);
//                                }
//                            }
//                            if (!$result = $s->query($ps, $inds)) {
//                                sphinxErr(__LINE__, $s, 'query', $ps);
//                            }
//                            if (isset($result['matches'])) {
//                                $total = (float) $result['total_found'];
//                                $count = 0;
//                                $minID = 0;
//                                if ($add == '2') {
//                                    $count_save_sql = "SELECT MAX(productID) FROM cscan_product_detail";
//                                } else {
//                                    $count_save_sql = "SELECT MAX(dts_id) FROM cscan_document_text_search";
//                                }
//                                $rs = $DRW->query($count_save_sql, $DRW_read);
//                                $data = $DRW->fetch_row($rs);
//                                $maxID = $data[0];
//                                $DRW->query('START TRANSACTION', $DRW_main); //$DRW->connection($DRW_main); $DRW->begin_transaction();
//                                for ($offset = 0; $offset <= $maxID; $offset += $step) {
//                                    $s = startSphinx();
//                                    if (!$s->setLimits(0, $step, $step)) {
//                                        sphinxErr(__LINE__, $s, 'setLimits');
//                                    }
//                                    foreach ($filter_range as $fr) {
//                                        if (!$s->setFilterRange($fr[0], $fr[1], $fr[2])) {
//                                            sphinxErr(__LINE__, $s, 'setFilterRange', $fr[0] . ', ' . $fr[1] . ', ' . $fr[2]);
//                                        }
//                                    }
//                                    if ($minID < $maxID) {
//                                        if (!$s->setIDRange($minID + 1, $maxID)) {
//                                            sphinxErr(__LINE__, $s, 'setIDRange');
//                                        }
//                                    }
//                                    if (!$result = $s->query($ps, $inds)) {
//                                        sphinxErr(__LINE__, $s, 'query', $ps);
//                                    }
//                                    if (isset($result['matches'])) {
//                                        foreach ($result['matches'] as $dts_id => $match) {
//                                            if ($add == '2') {
//                                                $productid = $dts_id;
//                                            } else {
//                                                $productid = $match['attrs']['productid'];
//                                            }
//                                            $query = "INSERT IGNORE INTO cscan_search_product (ID,productID,spID) VALUES ($search_id,$productid,$searchi)";
//                                            $DRW->query($query, $DRW_main);
//                                            $minID = $dts_id;
//                                            $currcount++;
//                                        }
//                                        if ($currcount >= $total) {
//                                            break;
//                                        }
//                                    }
//                                    $err = $s->getLastError();
//                                    $war = $s->getLastWarning();
//                                    if (!empty($err) || !empty($war)) {
//                                        //echo "$err | $war"; exit;
//                                        break;
//                                    }
//                                    // note that total_found using setLimits(0,1,1) is not always the same as without limits (bug in Sphinx?)
//                                    if (!isset($result['matches'])) {
//                                        break;
//                                    }
//                                }
//                                $DRW->query('COMMIT', $DRW_main); //$DRW->commit();
//                            }
//                        }
//                    }
//                    if ($search_type_and == 1 && $searches_count > 1) {
//                        $sqlc = "SELECT productID,COUNT(*) AS cnt FROM cscan_search_product WHERE ID=$search_id GROUP BY productID HAVING cnt<>$searches_count";
//                        $rsc = $DRW->query($sqlc, $DRW_read);
//                        while ($rowc = $DRW->fetch_row($rsc)) {
//                            $query = "DELETE FROM cscan_search_product WHERE ID=$search_id AND productID=$rowc[0]";
//                            $DRW->query($query, $DRW_main);
//                        }
//                    }
//                }
//                $ocrtext .= ' JOIN cscan_search_product sp ON(sp.ID=' . $search_id . ' AND pd.productID=sp.productID)';
//            } 
            
            else {
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

    // $selectQuery .= " FROM cscan_product_detail pd$ocrtext$pjoin$cjoin$ccjoin$afjoin$affjoin$dmajoin$edcjoin$sect_j$pcjoin$bjoin$ejoin$mljoin$rjoin$tljoin$ajoin$sjoin$ojoin $where$owhere $sortby";
    //exit;
    // $ignoreindex	=	" ignore index(c_p_d_mChannelID_index,entryID_sort_index,c_p_d_mPanelID_index,c_p_d_productStatus_index,addedToDatabase_c_p_d_index) ";
    $ignoreindex = " ignore index(cscan_product_company_index,c_p_d_productName_index,c_p_d_entryID_index,c_p_d_productStatus_index,c_p_d_productHeadline_index,c_p_d_actual_addedToDatabase_index,  c_p_d_variantID_index,c_p_d_DMSource_index,c_p_d_mPanelID_index,c_p_d_mChannelID_index,c_p_d_sectorID_index,addedToDatabase_c_p_d_index)";
    $selectQuery .= " FROM cscan_product_detail pd $ignoreindex$ocrtext$pjoin$cjoin$ccjoin$afjoin$affjoin$dmajoin$edcjoin$pcjoin$bjoin$ejoin$mljoin$rjoin$tljoin$ajoin$sjoin$ojoin $where$owhere$wheresearchproduct $sortby";

    if ($dograph != 0) {
        $field = getDoGraph($dograph);
        $selectQuery = "SELECT COUNT($field) AS field_count,$field AS field_name FROM ($selectQuery) as t1 GROUP BY $field"; // SQL_NO_CACHE ORDER BY field_count DESC,field_name ASC
    }


    return array($selectQuery, $saved);
}
