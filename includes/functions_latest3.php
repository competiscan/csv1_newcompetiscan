<?php

require_once __DIR__ . '/../vendor/autoload.php';

function doQuery_latest2($search_id, $docount = false, $addedtodatabaseover = '', $dograph = false, $bid = -1, $relev = false, $expans = false, $unapproved = false, $clear_ps = false, $search_panelist_date_over = -1, $search_values = array(), $sess_userID = 0) {
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
    $cpadsjoin='';
    $score_range_field='';
    $filter_range = array();
    $productidsarray = array();
    $wheresearchproduct = '';
    ################### for latest changes use in ######################
    $wherecondition = '';
    $where .= " (productStatus=1";
    if ($unapproved) {
        $where .= " OR productStatus=2";
    }
    //  $where .= ") AND addedToDatabase<=NOW() AND ";
    $where .= ") AND  ";
    ################### end for latest changes use in search ######################   


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
        
        $_SESSION['sess_search_additional_field']= array();
        $result5 = $DRW->query("SELECT field_name FROM cscan_users_additional_fields_allow WHERE userID=$sess_userID",$DRW_read);
        while($data5 = $DRW->fetch_row($result5)){
            $_SESSION['sess_search_additional_field'][] = $data5[0];
        }
        @$DRW->free_result($result5);
    } elseif (!isset($_SESSION)) {
        $_SESSION = array();
        $_SESSION['sess_mchannel'] = array();
        $_SESSION['sess_mpanel'] = array();
        $_SESSION['sess_sector'] = array();
        $_SESSION['sess_category'] = array();
        $_SESSION['sess_subcategory'] = array();
        $_SESSION['sess_userID'] = 0;
        $_SESSION['sess_search_exclude'] = array();
        $_SESSION['sess_search_additional_field']= array();
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
            ##############################Start Envelope/Postage Data Fields##############
            $deliveryTypeId = '';
            $postageId = '';
            $presortedId = '';
            $packageTypeId = '';
            ##############################Start Envelope/Postage Data Fields##############
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
            $faux_check = 0;
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
            $is_mobile = 0;
            $value_score = '';
            $minmaxmortgage = '';
            $publication_name = '';
            ###################### For Social Media Ad Type selection #################### 
            $socialmedia_adtype = '';
            ###################### For Social Media Ad Type selection #################### 
            ############################## Start FICO/CreditVision/Vantage Score Fields ##############
            $fico_score = '';
            $credit_vision_score= '';
            $vantage_score = '';
            ############################## End FICO/CreditVision/Vantage Score Fields ############## 
            foreach ($search_values as $var => $val) {
                $$var = $val;
            }
            $refinance = 0;
            $jumbo_ncnfg = 0;
            $va = 0;
            $fha = 0;
            $conventional = 0;
            $usda = 0;
            $correspondent_lending = 0;
        } else {
            $savedQ = "SELECT searchKey,searchType,searchOption,mChannelID,sectorID,mPanelID,
                        addedToDatabase,month1,month2,sort,company,productName,incentive,categoryID,mTypeID,subCategoryID,cardStatus,personalization,
                        gender,age,state,worksiteVoluntary,agentCommunicationID,groupSize,offerOrigin,enhance,saved,compaignLanguage,affinityAssociation,income_mult,fa_id_mult,tl_id_mult,
                        siteCatID_mult,pubTypeID_mult,approved_date,electronicID_mult,DMA_ID_mult,businessContent_mult,delmethid_mult,affinity_association,prescription,AffinityCategoryID_mult,
                        search_panelist_date,is_affinion,is_military,search_competi_id,ApplicationType_mult,is_multicultural,search_rules,IntroPricing_mult,is_rewards,RewardsProgramEmphasis_mult,is_incentive,responseMechID_mult,
                        multiculturalmarkets_mult,CardNetwork_mult,FeeProduct,external_link,FeeProductType,approved_date_to,ca_related,is_mover,scsc_primary,OptOutFirmOffer,searchKey2,search_type_and,riders_mult,is_hphsa,subSubCategoryID,Income_Producing_Assets_Segment_Code_mult,cg_id,is_citi,is_CreditCardMentioned,
                        spanelist_filter,edc_id_mult,AffinitySubCategoryID_mult,ERateType_mult,EOfferPrice_mult,ETermLength_mult,is_ECancelFee,IssueTypeID_mult,pcountry,is_Reloadable,creditUnion,is_mobile,value_score,refinance,jumbo_ncnfg,va,fha,conventional,usda,correspondent_lending,faux_check,minmaxmortgage,socialmedia_adtype,publication_name,deliveryTypeId,postageId,presortedId,packageTypeId,fico_score,credit_vision_score,vantage_score 
                        FROM cscan_search WHERE ID='" . $search_id . "'";
            $rs = $DRW->query($savedQ, $DRW_read);
            $data = $DRW->fetch_row($rs);
            //print_r($data );
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
            $is_mobile = $data[84];
            if ($data[85] != '') {
                $value_score = str_replace(",", "','", $data[85]);

                $value_score = "'" . $value_score . "'";
            } else {
                $value_score = "";
            }
            $refinance = $data[86];
            $jumbo_ncnfg = $data[87];
            $va = $data[88];
            $fha = $data[89];
            $conventional = $data[90];
            $usda = $data[91];
            $correspondent_lending = $data[92];
            $faux_check = $data[93];
            $minmaxmortgage = $data[94];
            $socialmedia_adtype = $data[95];
            $publication_name = $data[96];
            ##############################Start Envelope/Postage Data Fields##############
            $deliveryTypeId = $data[97];
            $postageId = $data[98];
            $presortedId = $data[99];
            $packageTypeId = $data[100];
            ##############################End Envelope/Postage Data Fields##############
            
            ############################## Start FICO/CreditVision/Vantage Score Fields ##############
            
            $fico_score = $data[101];
            $credit_vision_score = $data[102];
            $vantage_score = $data[103];
            
            ############################## End FICO/CreditVision/Vantage Score Fields ##############         
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
        if (empty($sectorID)) {
            $sectorID = implode(',', $_SESSION['sess_sector']);
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
        ################# for add country permission #################                    
//        if (empty($state) && in_array('canada', $_SESSION['sess_search_exclude'])) {
//            $pcountry = 'US';
//        } elseif (!empty($state)) {
//            $pcountry = '';
//        }
        ################# end for add country permission #################

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

        /* ########## For Digital media channel online display, online video, sem */

        $mChannelID_mod = array();
        if (strstr($mChannelID, ',')) {
            $mChannelID_mod = explode(',', $mChannelID);
        } else {
            $mChannelID_mod[] = $mChannelID;
        }
        // print_r($mChannelID_mod); die;
        //echo $is_mobile; die;
        /*  if(!empty($mChannelID_mod) AND ($is_mobile>0)){          

          $where.=' (';
          $pp='1';
          foreach($mChannelID_mod as $findchannel){
          if($pp!=1){
          $where.=' OR ';
          }
          if(($findchannel=='5' || $findchannel=='9' || $findchannel=='10') AND $is_mobile>0 ){
          $where.='(mChannelID='.$findchannel.' AND is_mobile='.$is_mobile.')';
          }else{
          $where.='mChannelID='.$findchannel;
          }

          $pp++;
          }
          $where.=' ) AND';
          } */


        if (!empty($mChannelID_mod) AND ( ($is_mobile > 0) || (in_array('digital_access', $_SESSION['sess_search_exclude']) ))) {

            $where .= ' (';
            $pp = '1';
            foreach ($mChannelID_mod as $findchannel) {
                if ($pp != 1) {
                    $where .= ' OR ';
                }
                if (($findchannel == '9' || $findchannel == '10') AND $is_mobile > 0) {
                    $where .= '(mChannelID=' . $findchannel . ' AND is_mobile=' . $is_mobile . ')';
                } else if (($findchannel == '5') AND $is_mobile > 0 AND in_array('digital_access', $_SESSION['sess_search_exclude'])) {
                    $where .= '(mChannelID=' . $findchannel . ' AND is_mobile=' . $is_mobile . ' AND (pd.panelist_id="" OR pd.panelist_id is NULL))';
                } else if (($findchannel == '5') AND $is_mobile <= 0 AND in_array('digital_access', $_SESSION['sess_search_exclude'])) {
                    $where .= '(mChannelID=' . $findchannel . ' AND (pd.panelist_id="" OR pd.panelist_id is NULL))';
                } else if (($findchannel == '5') AND $is_mobile > 0) {
                    $where .= '(mChannelID=' . $findchannel . ' AND is_mobile=' . $is_mobile . ')';
                } else if (($findchannel == '6') AND in_array('digital_access', $_SESSION['sess_search_exclude'])) {
                    $where .= '(mChannelID=' . $findchannel . ' AND (pd.panelist_id="" OR pd.panelist_id is NULL))';
                } else {
                    $where .= 'mChannelID=' . $findchannel;
                }

                $pp++;
            }
            $where .= ' ) AND ';
        }



        /* ##########  End for digital Module  ##########  */


        $exactArray = array();
        $envPostageArray = array();
        /* For Digital Media Channel */

        if ($is_mobile > 0 || in_array('digital_access', $_SESSION['sess_search_exclude'])) {
            $multExactArray = array('mPanelID' => $mPanelID);
        } else {
            $multExactArray = array('mChannelID' => $mChannelID, 'mPanelID' => $mPanelID);
        }
        /* ####### End For digital Media */

        //$multExactArray = array('mChannelID' => $mChannelID,'mPanelID' => $mPanelID);

        $likeArray = array('incentive' => $incentive);
        $multArray = array();
        $otherArray = array('company' => $company, 'affinity_association' => $affinity_association, 'addedToDatabase' => $addedToDatabase, 'month' => $month, 'approved_date' => $approved_date, 'search_competi_id' => $search_competi_id, 'ApplicationType_mult' => $ApplicationType_mult, 'cg_id' => $cg_id, 'rstate' => $rstate, 'pcountry' => $pcountry);
        ############################## Start FICO/CreditVision/Vantage Score Fields ##############
        $panelistArray = array('ppageID' => $age, 'pincomeID' => $income_mult, 'isBiz' => $isBiz, 'dmap.code' => $DMA_ID_mult, 'ppdate' => $ppdate, 'ppdate_month' => $ppdate_month, 'ppstateID' => $ppstateID, 'pgender' => $pgender, 'Income_Producing_Assets_Segment_Code' => $Income_Producing_Assets_Segment_Code, 'edc_id' => $edc_id_mult, 'ValueScore' => $value_score,'fico_score'=>$fico_score,'credit_vision_score'=>$credit_vision_score,'vantage_score'=>$vantage_score);
        ############################## End FICO/CreditVision/Vantage Score Fields ##############
        
        //$panelistArray = array('ppageID' => $age, 'pincomeID' => $income_mult, 'isBiz' => $isBiz, 'dmap.code' => $DMA_ID_mult, 'ppdate' => $ppdate, 'ppdate_month' => $ppdate_month, 'ppstateID' => $ppstateID, 'pgender' => $pgender, 'Income_Producing_Assets_Segment_Code' => $Income_Producing_Assets_Segment_Code, 'edc_id' => $edc_id_mult, 'ValueScore' => $value_score);
        //$panelistArray = array('ppageID' => $age, 'pincomeID' => $income_mult, 'isBiz' => $isBiz, 'dmap.code' => $DMA_ID_mult,  'ppdate_month' => $ppdate_month, 'ppstateID' => $ppstateID, 'pgender' => $pgender, 'Income_Producing_Assets_Segment_Code' => $Income_Producing_Assets_Segment_Code, 'edc_id' => $edc_id_mult,'ValueScore'=>$value_score);

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
        $scoreRangeArray=array();
        if ($enhance) {
            $scoreRangeArray = array('fico_range_id' => $fico_score, 'vantage_range_id' => $vantage_score, 'creditVision_range_id' => $credit_vision_score);
            $envPostageArray = array('deliveryTypeId' => $deliveryTypeId, 'postageId' => $postageId, 'presortedId' => $presortedId, 'packageTypeId' => $packageTypeId);
            $exactArray = array_merge($exactArray, array('cardStatus' => $cardStatus, 'personalization' => $personalization, 'gender' => $gender, 'offerOrigin' => $offerOrigin, 'compaignLanguage' => $compaignLanguage));
            $multExactArray = array_merge($multExactArray, array('mTypeID' => $mTypeID, 'delmethid' => $delmethid_mult));
            $likeArray = array_merge($likeArray, array('external_link' => $external_link));

            $multArray = array_merge($multArray, array('state' => $state, 'agentCommunicationID' => $agentCommunicationID, 'groupSize' => $groupSize, 'fa_ids' => $fa_id_mult, 'tl_ids' => $tl_id_mult, 'electronicID' => $electronicID_mult, 'businessContent' => $businessContent_mult, 'multiculturalmarkets' => $multiculturalmarkets_mult, 'responseMechID' => $responseMechID_mult, 'FeeProductType' => $FeeProductType, 'riders' => $riders_mult, 'IssueTypeID' => $IssueTypeID_mult));
            $otherArray = array_merge($otherArray, array('AffinityCategoryID' => $AffinityCategoryID_mult, 'worksiteVoluntary' => $worksiteVoluntary, 'affinityAssociation' => $affinityAssociation, 'siteCatID' => $siteCatID_mult, 'pubTypeID' => $pubTypeID_mult, 'prescription' => $prescription, 'is_affinion' => $is_affinion, 'is_military' => $is_military, 'is_multicultural' => $is_multicultural, 'IntroPricing_mult' => $IntroPricing_mult, 'is_rewards' => $is_rewards, 'RewardsProgramEmphasis_mult' => $RewardsProgramEmphasis_mult, 'is_incentive' => $is_incentive, 'CardNetwork_mult' => $CardNetwork_mult, 'FeeProduct' => $FeeProduct, 'is_mover' => $is_mover, 'OptOutFirmOffer' => $OptOutFirmOffer, 'is_hphsa' => $is_hphsa, 'is_citi' => $is_citi, 'is_CreditCardMentioned' => $is_CreditCardMentioned, 'ERateType' => $ERateType_mult, 'EOfferPrice' => $EOfferPrice_mult, 'ETermLength' => $ETermLength_mult, 'ECancelFee' => $is_ECancelFee, 'Reloadable' => $is_Reloadable, 'isCreditUnion' => $creditUnion, 'faux_check' => $faux_check, 'socialmedia_adtype' => $socialmedia_adtype, 'minmaxmortgage' => $minmaxmortgage, 'publicationName' => $publication_name));
        }

        // Added for publication name
        $out_pub = '';
        $pubID = array();
        if (!empty($publication_name)) {
            $out_pub .= '(';

            $keyArray_publication = preg_split('/"\\s+or\\s+"/i', $publication_name, -1, PREG_SPLIT_NO_EMPTY);
            if (count($keyArray_publication) == 1) {
                $keyArray_publication = preg_split('/"\\s+and\\s+"/i', $publication_name, -1, PREG_SPLIT_NO_EMPTY);
            }
            foreach ($keyArray_publication as $val) {
                $val = trim($val);
                if (count($keyArray_publication) > 1 || preg_match('/^"([^"]+)"$/', $val)) {
                    if (preg_match('/^"([^"]+)"$/', $val, $match) || preg_match('/^([^"]+)"$/', $val, $match) || preg_match('/^"([^"]+)$/', $val, $match)) {
                        $val = $match[1];
                    }
                    $val = $DRW->real_escape_string($val);
                }

                $out_pub .= "(publicationName LIKE '%$val%' ) OR ";
            }

            $out_pub = substr($out_pub, 0, -4);
            $out_pub .= ')';
            $sqlc = "SELECT publicationID FROM cscan_publication WHERE " . $out_pub;
            $rsp = $DRW->query($sqlc, $DRW_read);
            if ($DRW->num_rows($rsp) > 0) {
                while ($data = $DRW->fetch_row($rsp)) {
                    $pubID[] = $data[0];
                }
            } else {
                $pubID[] = 0;
            }
        }
        // End for publication name

        $partsArray2 = array();
        $partsArray2_sort = array();
        $j = 0;
        if ($andorArray['sectorID'] == 'AND') {
            $alias = "scsc$j";
        } else {
            $alias = "scsc";
        }
        $part1_sort = '';
        $seccatsubArray = get_seccatsub($sectorID, $categoryID, $subCategoryID, $subSubCategoryID);
        $whereandcond = '';
        foreach ($seccatsubArray as $sid => $cArray) {
            //$part1 = $alias . ".scsc_sectorID=$sid";
            $part1 = "CONCAT(',',sectorID,',') REGEXP ',$sid,'";

            if ($scsc_primary) {
                // $part1 .= ' AND ' . $alias . ".scsc_sort=1";
                $part1_sort = "CONCAT(',',scsc_sectorID_sort,',') REGEXP ',$sid,' AND pd.scsc_sort=1";
                // $whereandcond = "  pd.scsc_sort=1 AND ";
            }
            if (in_array('sectorID', $exacterArray)) {
                $exactervalsArray['sectorID'][] = $sid;
            }
            if (count($cArray) == 0) {
                $partsArray2[] = '(' . $part1 . ')';

                if ($scsc_primary) {
                    $partsArray2_sort[] = '(' . $part1_sort . ')';
                }


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
                    if ($scsc_primary) {
                        $part2_sort = "CONCAT(',',scsc_categoryID_sort,',') REGEXP ',$cid,'";
                        $partsArray2_sort[] = '(' . $part1_sort . ' AND ' . $part2_sort . ')';
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
                            if ($scsc_primary) {
                                $part3_sort = " CONCAT(',',scsc_subCategoryID_sort,',') REGEXP ',$scid,'";
                                $partsArray2_sort[] = '(' . $part1_sort . ' AND ' . $part2_sort . ' AND ' . $part3_sort . ')';
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

                                    if ($scsc_primary) {
                                        $part4_sort = "CONCAT(',',scsc_subSubCategoryID_sort,',') REGEXP ',$sscid,'";
                                        $partsArray2_sort[] = '(' . $part1_sort . ' AND ' . $part2_sort . ' AND ' . $part3_sort . ' AND ' . $part4_sort . ')';
                                    }

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

        if (count($partsArray2_sort) > 0) {
            // $where .= '(' . implode(' ' . $andorArray['scsc_sectorID_sort'] . ' ', $partsArray2_sort) . ') AND ';
            $where .= '(' . implode(' OR ', $partsArray2_sort) . ') AND ';
        }
//echo $where;
//print_r($partsArray2_sort);exit;
        // print_r($otherArray);exit;
        foreach ($otherArray as $field => $val) {
            if ($val != '') {
                $field_pubName = '';
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
                } elseif ($field == 'worksiteVoluntary' || $field == 'affinityAssociation' || $field == 'prescription' || $field == 'is_affinion' || $field == 'is_military' || $field == 'FeeProduct' || $field == 'is_mover' || $field == 'OptOutFirmOffer' || $field == 'is_hphsa' || $field == 'is_citi' || $field == 'ECancelFee' || $field == 'Reloadable' || $field == 'isCreditUnion' || $field == 'faux_check') {
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
                } elseif ($field == 'socialmedia_adtype' && $val > 0) {
                    $where .= " $field=$val AND ";
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
                } elseif ($field == 'siteCatID' || $field == 'pubTypeID' || $field == 'ApplicationType_mult' || $field == 'publicationName') {
                    $field2 = '';
                    if ($field == 'siteCatID') {
                        $field = 'cscan_sites.sites_category_id';
                        $ojoin .= ',cscan_sites_product,cscan_sites';
                        $owhere .= ' AND pd.productID=cscan_sites_product.productID AND cscan_sites_product.sites_id=cscan_sites.sites_id';
                    } elseif ($field == 'pubTypeID' || $field == 'publicationName') {
                        if ($field == 'publicationName') {
                            $field_pubName = 1;
                            $field = '';
                        } else {
                            $field = 'cscan_publication.print_typeID';
                        }
                        // Added for publication name
                        $pos_pub = strpos($ojoin, 'cscan_publication_product,cscan_publication');

                        if ($pos_pub === false) {
                            $ojoin .= ',cscan_publication_product,cscan_publication';
                            $owhere .= ' AND pd.productID=cscan_publication_product.productID AND cscan_publication_product.publicationID=cscan_publication.publicationID';
                        }

                        if (!empty($pubID) AND $field_pubName == 1) {
                            $owhere .= " AND  cscan_publication_product.publicationID IN (" . implode(',', $pubID) . ")";
                        }
                    } elseif ($field == 'ApplicationType_mult') {
                        $sectorIDArray = explode(',', $sectorID);
                        $in = false;
                        if (in_array(90, $sectorIDArray)) {
                            $in = true;
                            $field = 'cscan_payment_cards.ApplicationType';
                            $pcjoin = ' LEFT JOIN cscan_payment_cards ON (pd.productID=cscan_payment_cards.productID)';
                        }
                        if (in_array(6, $sectorIDArray)) {
                            if ($in) {
                                $field2 = 'cscan_mortgage_loan.MLApplicationType';
                            } else {
                                $field = 'cscan_mortgage_loan.MLApplicationType';
                            }
                            $mljoin = ' LEFT JOIN cscan_mortgage_loan ON (pd.productID=cscan_mortgage_loan.productID)';
                        }
                    }

                    $tmpArray = explode(',', $val);

                    if ($val != '') {

                        if (!empty($field2)) {
                            $where .= "( $field in (" . $DRW->real_escape_string($val) . " ) OR ";
                            $where .= " $field2 in(" . $DRW->real_escape_string($val) . ")) AND  ";
                        } else {
                            if (!empty($field)) {
                                $where .= " $field in (" . $DRW->real_escape_string($val) . " ) AND ";
                            }
                            //$where .= " $field in (" . $DRW->real_escape_string($val) . " ) AND ";
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
                } elseif ($field == 'minmaxmortgage' && $val != '0-2000000') {

                    $minmaxarray = explode("-", $val);
                    $minloanamount = $minmaxarray[0];
                    $maxloanamount = $minmaxarray[1];
                    //print_r($minmaxarray);

                    if ($mljoin == '') {
                        $mljoin = ' JOIN cscan_mortgage_loan ON (pd.productID=cscan_mortgage_loan.productID)';
                    }
                    $where .= " ((cscan_mortgage_loan.MinimumLoanAmount>='" . $minloanamount . "' AND cscan_mortgage_loan.MaximumLoanAmount<='" . $maxloanamount . "') OR (cscan_mortgage_loan.OfferedLoanAmount>='" . $minloanamount . "' AND cscan_mortgage_loan.OfferedLoanAmount<='" . $maxloanamount . "')) AND ";
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
                        ############ for remove the cscan_product_detail_state condition ########
                        // $sjoin = " JOIN cscan_product_detail_state ignore index(pds_state_index) ON (cscan_product_detail_state.productID=pd.productID)";
                        $sjoin = '';
                    }
                    if ($field == 'pcountry') {
                        //$sjoin .= " JOIN cscan_state ON (cscan_product_detail_state.stateID=cscan_state.stateID) ";
                        //$where .= " (cscan_state.countryCode='".$DRW->real_escape_string($val)."' OR cscan_state.countryCode='') AND ";
                        ############ for remove the cscan_product_detail_state condition ########
                        // $where .= " (cscan_product_detail_state.countryCode_copy='" . $DRW->real_escape_string($val) . "' OR cscan_product_detail_state.countryCode_copy='') AND ";
                        // $where .= " (pd.countryCode_copy='" . $DRW->real_escape_string($val) . "' OR pd.countryCode_copy='') AND ";
                        $where .= " (pd.countryCode_copy='" . $DRW->real_escape_string($val) . "' ) AND ";
                    } else {
                        $cos = explode(',', $val);
                        if (empty($cos[0])) {
                            unset($cos[0]);
                        }
                        ############ for remove the cscan_product_detail_state condition ########
                        // $where .= " cscan_product_detail_state.stateID IN (" . implode(',', $cos) . ") AND ";

                        $statecondition = '';
                        foreach ($cos as $state_ids) {
                            $statecondition .= " (CONCAT(',',state,',') REGEXP ',$state_ids,')";
                            $statecondition .= " OR ";
                        }


                        // $where .= " pd.state IN (" . implode(',', $cos) . ") AND ";

                        $where .= "  (" . substr($statecondition, 0, -3) . ") AND ";
                        //echo $where;exit; 
                    }
                    if (empty($is_panelist)) {
                        ############ for remove the cscan_product_detail_state condition ########
                        // $where .= " cscan_product_detail_state.is_panelist=0 AND ";
                        ########## remove for the result not set properly after ec2 #########
                        //  $where .= " pd.is_panelist=0 AND ";
                    }
                }
            }
        }
        #### Start:  Mortgage & Loan - General Mortgage & Loan Details ####
        $arrMort = [];
        if (!empty($refinance) && $refinance == 1) {
            if ($mljoin == '') {
                $mljoin = ' JOIN cscan_mortgage_loan ON (pd.productID=cscan_mortgage_loan.productID)';
            }
            $arrMort[] = 'cscan_mortgage_loan.refinance=1';
        } else if (!empty($refinance) && $refinance == 2) {
            if ($mljoin == '') {
                $mljoin = ' JOIN cscan_mortgage_loan ON (pd.productID=cscan_mortgage_loan.productID)';
            }
            $arrMort[] = 'cscan_mortgage_loan.refinance=0';
        }
        if (!empty($jumbo_ncnfg)) {
            if ($mljoin == '') {
                $mljoin = ' JOIN cscan_mortgage_loan ON (pd.productID=cscan_mortgage_loan.productID)';
            }
            $arrMort[] = 'cscan_mortgage_loan.jumbo_ncnfg=1';
        }
        if (!empty($va)) {
            if ($mljoin == '') {
                $mljoin = ' JOIN cscan_mortgage_loan ON (pd.productID=cscan_mortgage_loan.productID)';
            }
            $arrMort[] = 'cscan_mortgage_loan.va=1';
        }
        if (!empty($fha)) {
            if ($mljoin == '') {
                $mljoin = ' JOIN cscan_mortgage_loan ON (pd.productID=cscan_mortgage_loan.productID)';
            }
            $arrMort[] = 'cscan_mortgage_loan.fha=1';
        }
        if (!empty($conventional)) {
            if ($mljoin == '') {
                $mljoin = ' JOIN cscan_mortgage_loan ON (pd.productID=cscan_mortgage_loan.productID)';
            }
            $arrMort[] = 'cscan_mortgage_loan.conventional=1';
        }
        if (!empty($usda)) {
            if ($mljoin == '') {
                $mljoin = ' JOIN cscan_mortgage_loan ON (pd.productID=cscan_mortgage_loan.productID)';
            }
            $arrMort[] = 'cscan_mortgage_loan.usda=1';
        }
        if (!empty($correspondent_lending)) {
            if ($mljoin == '') {
                $mljoin = ' JOIN cscan_mortgage_loan ON (pd.productID=cscan_mortgage_loan.productID)';
            }
            $arrMort[] = 'cscan_mortgage_loan.correspondent_lending=1';
        }
        if (count($arrMort) > 0) {
            $where .= implode(" AND ", $arrMort) . ' AND ';
        }
        ##########3 End:  Mortgage & Loan - General Mortgage & Loan Details ###########
        // echo $where  ;exit;

        foreach ($panelistArray as $field => $val) {

            if ($val != '') {
                // $pjoin = $pjoin_left . ' JOIN cscan_panelists_product pp ON (pd.productID=pp.productID' . $pjoin_filter . ')';
                if ($field == 'Income_Producing_Assets_Segment_Code' || $field == 'ValueScore') {
                    $pjoin = $pjoin_left . ' JOIN cscan_panelists_product pp ON (pd.productID=pp.productID' . $pjoin_filter . ')';

                    $ajoin = " JOIN cscan_panelists_appends cpas ON (cpas.panelist_id=pp.panelist_id) ";
                } elseif ($field == 'edc_id') {
                    $pjoin = $pjoin_left . ' JOIN cscan_panelists_product pp ON (pd.productID=pp.productID' . $pjoin_filter . ')';

                    $edcjoin = ' JOIN cscan_edc_postalcode edc ON(pp.pppostalcode=edc.pppostalcode)';
                } elseif ($field == 'dmap.code') {
                    $pjoin = $pjoin_left . ' JOIN cscan_panelists_product pp ON (pd.productID=pp.productID' . $pjoin_filter . ')';

                    $dmajoin = ' JOIN cscan_dma_code_postalcode dmap ON (pp.pppostalcode=dmap.pppostalcode)';
                } elseif ($field == 'pincomeID') {
                    $pjoin = $pjoin_left . ' JOIN cscan_panelists_product pp ON (pd.productID=pp.productID' . $pjoin_filter . ')';
                }
                ############################## Start FICO/CreditVision/Vantage Score Fields ##############
                elseif ($field == 'fico_score' || $field == 'credit_vision_score' ||$field == 'vantage_score') {
                    $pjoin = $pjoin_left . ' JOIN cscan_panelists_product pp ON (pd.productID=pp.productID' . $pjoin_filter . ')';
                    //$cpadsjoin = " JOIN cscan_panelists_additional_score cpads ON (cpads.panelist_id=pp.panelist_id) ";
                }
                ############################## End FICO/CreditVision/Vantage Score Fields ##############




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
                    $pjoin = $pjoin_left . ' JOIN cscan_panelists_product pp ON (pd.productID=pp.productID' . $pjoin_filter . ')';

                    $where .= " (pp.pgender='$val' OR gender='$val') AND ";
                } elseif ($field == 'ppageID') {
                    $pjoin = $pjoin_left . ' JOIN cscan_panelists_product pp ON (pd.productID=pp.productID' . $pjoin_filter . ')';

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
                            // $where .= $field . " in (" . substr($val, 0, -1) . ") AND ";
                            //$where .= $field . " in (" .$val. ") AND ";
                            $val = str_replace(",", "','", $val);
                            $where .= $field . " in ('" . $val . "') AND ";
                        
                        /* ######### For FICO, CreditVision and Vantage Score ######### */
                        } else if ($field == 'ValueScore' || $field == 'fico_score' || $field == 'credit_vision_score'||$field == 'vantage_score') {
                            $where .= '';
                        } else {

                            $where .= " $field in(" . $val . " ) AND ";
                        }
                    }



                    foreach ($tmpArray as $v) {
                        if ($v != '') {

                            if ($field == 'ppstateID') {
                                $pjoin = $pjoin_left . ' JOIN cscan_panelists_product pp ON (pd.productID=pp.productID' . $pjoin_filter . ')';

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
        if ($value_score != '') {
            $where .= "  cpas.ValueScore_for_Household IN (" . $value_score . ") AND ";
        }
        
        ############################## Start FICO/CreditVision/Vantage Score Fields ##############
        /*
        if ($fico_score != '') {
            $fico_score_array=explode(",", $fico_score);
            if(count($fico_score_array)>0){	
                $AndORQuery =" (";
                $AndORQuery2='';	
                for($i=0; $i < count($fico_score_array); $i++){		
                    if(strstr($fico_score_array[$i],'+')){			
                        $fico_score_array[$i]=trim(str_replace('+','',$fico_score_array[$i])); 			
                        if($AndORQuery2==''){
                            $AndORQuery2 .= " (CAST(cpads.fico_score AS SIGNED)>='$fico_score_array[$i]' )";	
                        }else{
                            $AndORQuery2 .= " (AND  CAST(cpads.fico_score AS SIGNED)>='$fico_score_array[$i]') ";	
                        }
                    }else{ 
                        $ficoArrayData=explode("-",$fico_score_array[$i]);
                        if(!empty($ficoArrayData[0]) && !empty($ficoArrayData[1])){
                            if($AndORQuery2==''){
                                $AndORQuery2 .= "  (CAST(cpads.fico_score AS SIGNED)>='$ficoArrayData[0]' AND CAST(cpads.fico_score AS SIGNED)<='$ficoArrayData[1]')";
                            }else{
                                $AndORQuery2 .= " OR (CAST(cpads.fico_score AS SIGNED)>='$ficoArrayData[0]' AND CAST(cpads.fico_score AS SIGNED)<='$ficoArrayData[1]')";
                            }
                        }
                    }
                }	
            }
            if($AndORQuery2!=''){
                $AndORQuery .=$AndORQuery2.") AND ";
                $where .= $AndORQuery;
            }
            
        }
        if ($credit_vision_score != '') {
            $credit_vision_array=explode(",", $credit_vision_score);
            if(count($credit_vision_array)>0){
                $AndORQuery =" (";
                $AndORQuery2='';	
                for($i=0; $i < count($credit_vision_array); $i++){		
                    if(strstr($credit_vision_array[$i],'+')){			
                        $credit_vision_array[$i]=trim(str_replace('+','',$credit_vision_array[$i])); 			
                        if($AndORQuery2==''){
                            $AndORQuery2 .= " (CAST(cpads.credit_vision AS SIGNED)>='$credit_vision_array[$i]' )";	
                        }else{
                            $AndORQuery2 .= " (AND CAST(cpads.credit_vision AS SIGNED)>='$credit_vision_array[$i]') ";	
                        }
                    }else{ 
                        $creditVisionData=explode("-",$credit_vision_array[$i]);
                        if(!empty($creditVisionData[0]) && !empty($creditVisionData[1])){
                            if($AndORQuery2==''){
                                $AndORQuery2 .= "  (CAST(cpads.credit_vision AS SIGNED)>='$creditVisionData[0]' AND CAST(cpads.credit_vision AS SIGNED)<='$creditVisionData[1]')";
                            }else{
                                $AndORQuery2 .= " OR (CAST(cpads.credit_vision AS SIGNED)>='$creditVisionData[0]' AND CAST(cpads.credit_vision AS SIGNED)<='$creditVisionData[1]')";
                            }
                        }
                    }
                }
                
                if($AndORQuery2!=''){
                    $AndORQuery .=$AndORQuery2.") AND ";
                    $where .= $AndORQuery;
                }
            }
        }
        if ($vantage_score != '') {
            $vantage_score_array=explode(",", $vantage_score);
            if(count($vantage_score_array)>0){
                $AndORQuery =" (";
                $AndORQuery2='';	
                for($i=0; $i < count($vantage_score_array); $i++){		
                    if(strstr($vantage_score_array[$i],'+')){			
                        $vantage_score_array[$i]=trim(str_replace('+','',$vantage_score_array[$i])); 			
                        if($AndORQuery2==''){
                            $AndORQuery2 .= " (CAST(cpads.vantage_score AS SIGNED)>='$vantage_score_array[$i]' )";	
                        }else{
                            $AndORQuery2 .= " (AND CAST(cpads.vantage_score AS SIGNED)>='$vantage_score_array[$i]') ";	
                        }
                    }else{ 
                        $vantageScoreData=explode("-",$vantage_score_array[$i]);
                        if(!empty($vantageScoreData[0]) && !empty($vantageScoreData[1])){
                            if($AndORQuery2==''){
                                $AndORQuery2 .= "  (CAST(cpads.vantage_score AS SIGNED)>='$vantageScoreData[0]' AND CAST(cpads.vantage_score AS SIGNED)<='$vantageScoreData[1]')";
                            }else{
                                $AndORQuery2 .= " OR (CAST(cpads.vantage_score AS SIGNED)>='$vantageScoreData[0]' AND CAST(cpads.vantage_score AS SIGNED)<='$vantageScoreData[1]')";
                            }
                        }
                    }
                }
                
                if($AndORQuery2!=''){
                    $AndORQuery .=$AndORQuery2.") AND ";
                    $where .= $AndORQuery;
                }
            }            
            
        }
        if($fico_score!='' || $vantage_score != '' || $credit_vision_score != ''){
           $where .= " LEFT(cpads.score_date, 7)=LEFT(pp.ppdate, 7) AND ";
        }
         */            
        ############################## End FICO/CreditVision/Vantage Score Fields ##############


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
        ##############################Start Envelope/Postage Data Fields##############
        foreach ($envPostageArray as $field => $val) {
            if ($val != '0' && $val != '') {
                if (!is_array($val)) {
                    $where .= $field . " in (" . $val . ") AND ";
                } else {
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
                   
                }
            }
        }
        ##############################End Envelope/Postage Data Fields##############    
        /* ######### For FICO, CreditVision and Vantage Score ######### */
        foreach ($scoreRangeArray as $field => $val) {
            if ($val != '0' && $val != '') {
                if (!is_array($val)) {
                    $where .= $field . " in (" . $val . ") AND ";
                } else {
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
                   
                }
            }
        }      
       /* ######### End FICO, CreditVision and Vantage Score ######### */
        
        foreach ($multExactArray as $field => $val) {
            if ($val != '') {
                if (!is_array($val)) {
                    $where .= $field . " in (" . $val . ") AND ";
                } else {

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
                        $where .= $field . " in  (" . substr($tmpwhere, 0, -2) . ") AND ";
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
            //$where .= '(' . implode(' OR ', $partsArray) . ') AND ';
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


        $where .= $whereandcond;
        
        ######################START ELASTIC SEARCH###################
        $sectorOnlyFlag='';
        if (($searchKey != '' && $searchKey != 'NULL') || ($searchKey2 != '' && $searchKey2 != 'NULL')) {
            //echo "HEEEEEEEEEEEEEEEEEEEEEEEEE"."<br/>"; die;
            $search_rulesArray = array();
            $search_rulesArrayf = array();
            $check_search_sql = "SELECT * FROM cscan_search where userID={$_SESSION['sess_userID']} AND ID='".$search_id."'";
            $resultCheck = $DRW->query($check_search_sql,$DRW_read);
            if($DRW->num_rows($resultCheck) > 0){
                $dataSearchCheck = $DRW->fetch_array($resultCheck);
                  //print_r($dataSearchCheck); die;
                    //echo $dataSearchCheck['IntroPricing_mult'];
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
                        /*if($dataSearchCheck['mTypeID']==''){
                          $nots.=',mtype_id(null)';
                          $postdata['mtype_id']="1";
                        }*/
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
                   if (strpos ($searchKey, '"') !== false || strpos ($searchKey, "'") !== false || strpos ($searchKey2, '"') !== false || strpos ($searchKey2, "'") !== false) {
                        $postdata['dts_val'] = strtoupper($searchKey);
                        $postdata['product_headline'] =$searchKey2;
                        //$postdata['product_headline'] =strtoupper($searchKey); 
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
                        $new_date='1 month';
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
                    /*$nots.=",primary_competi_id";
                    if($nots!=''){
                     $postdata['nots']=trim($nots,',');    
                    }*/
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
                
                $postdata['product_status']="1";
                $API_URL_EMAIL_ALERT=API_URL_EMAIL_ALERT;
                //$API_URL="https://api7.competiscan.com/elasticsearch-service/v1/search/onlypids";
                $chcsv2 = curl_init($API_URL_EMAIL_ALERT);
                $postdata['sortby'] ='entry_id_sort, desc';
                $postdata['tiebreaker'] ='product_id, desc';
                $posteddata = json_encode($postdata);
                //echo $posteddata; 
                curl_setopt($chcsv2, CURLOPT_SSL_VERIFYPEER, FALSE);
                curl_setopt($chcsv2, CURLOPT_FOLLOWLOCATION, TRUE);
                curl_setopt($chcsv2, CURLOPT_POST, 1);
                curl_setopt($chcsv2, CURLOPT_POSTFIELDS, $posteddata);
                curl_setopt($chcsv2, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($chcsv2, CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
                curl_setopt($chcsv2, CURLOPT_TIMEOUT, 80);
                $response_api = curl_exec($chcsv2);
                if(curl_error($chcsv2)){
                        echo 'Request Error:' . curl_error($chcsv2);
                }else{
                        //$curl_status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                        $jsonResponseAPIData = json_decode($response_api, JSON_UNESCAPED_UNICODE);
                        //echo "<pre>";
                        //print_r($jsonResponseAPIData); 
                        if(!empty($jsonResponseAPIData)){
                            $product_in_jsn=implode(',',$jsonResponseAPIData['product_ids']);
                            $wheresearchproduct = " AND pd.productID in($product_in_jsn)";
                        }
                }
                curl_close($chcsv2);

            }
            
        }
        
        /*echo $searchType.'#############'.$searchKey.'###########'.$searchKey2;
        if ($searchKey != '' || $searchKey != '') {
            ############ENTRYID COMMA SEPTATED CONVERT INTO OR
            if($searchType=='fulltext2' && $searchKey!=''){
                $chkEntryIDPattern = preg_match("\"[\d]{4}-[\d]{2}-[\d]{2}-[\d]+\"", $searchKey,$matches);
                if($chkEntryIDPattern==1){
                    $searchKey  =str_replace(","," or ",$searchKey);
                }
            }
            if ($searchType == 'ocr') {
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
                    //echo $search_id;exit;   
                    $count_save_sql = "SELECT COUNT(*) FROM cscan_search_product WHERE ID=$search_id";
                    $rs = $DRW->query($count_save_sql, $DRW_read);
                    $data = $DRW->fetch_row($rs);
                    $numrow = (int) $data[0];
                    //$numrow=2;
                }

                // if ($numrow == 0 && !empty($SPHINX_name)) {
                //COMMMENT Spnix Name
                if (!empty($SPHINX_name)) {
                    $searchi = 0;
                    $searches = array();
                    $searchkeys = array();
                    $digital_index=',base_index_prod_digital,base_index_prod_digital_od,base_index_prod_digital_sem';
                    if ($searchType == 'ocr_fulltext2') {
                        if ($searchKey2 != '') {
                            $searches['fulltext2'] = '2';
                            $searchkeys['fulltext2'] = $searchKey2;
                            //$digital_index='';
                        }
                        if ($searchKey != '') {
                            $searches['ocr2'] = '';
                            $searchkeys['ocr2'] = $searchKey;                            
                        }
                    } elseif ($searchKey != '') {
                        if ($searchType == 'fulltext2') {
                            $searches[$searchType] = '2';
                             //$digital_index='';
                            
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

                        //$inds = 'base_index_' . $SPHINX_name . $add . ',delta_index_' . $SPHINX_name . $add; 
                        $inds = 'base_index_' . $SPHINX_name . $add . ',delta_index_' . $SPHINX_name . $add . $digital_index;

                        //,base_index_'.$SPHINX_name.'stemmed'.$add.',delta_index_'.$SPHINX_name.'stemmed'.$add.'
                        // $inds = 'base_index_prod';
//                        if (strpos($sk, '*') !== false) {
//                            $inds .= ',base_index_' . $SPHINX_name . 'star' . $add . ',delta_index_' . $SPHINX_name . 'star' . $add;
//                        }
//                        
//                        $inds    =  'base_index_' . $SPHINX_name . $add;
                        //echo $inds;
                        $ps = parseSphinx($s, $sk);



                        if (trim($ps) != '') {
                            $currcount = 0;
                            $step = $total = 50000;
                            if (!$s->setLimits(0, 1, 1)) {
                                sphinxErr(__LINE__, $s, 'setLimits');
                            }

                            ############ remove this for search orc with date betwwen ###########  
                            // foreach ($filter_range as $fr) {
                              //if (!$s->setFilterRange($fr[0], $fr[1], $fr[2])) {
                              //sphinxErr(__LINE__, $s, 'setFilterRange', $fr[0] . ', ' . $fr[1] . ', ' . $fr[2]);
                              //}
                              //}
                             //
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
                                for ($offset = 0; $offset <= $maxID; $offset += $step) {
                                    $s = startSphinx();
                                    if (!$s->setLimits(0, $step, $step)) {
                                        sphinxErr(__LINE__, $s, 'setLimits');
                                    }

                                    ############ remove this for search orc with date betwwen ########### 
                                    
                                      //foreach ($filter_range as $fr) {
                                      //if (!$s->setFilterRange($fr[0], $fr[1], $fr[2])) {
                                      //sphinxErr(__LINE__, $s, 'setFilterRange', $fr[0] . ', ' . $fr[1] . ', ' . $fr[2]);
                                      //}
                                      //}

                                     
                                    ############ end remove this for search orc with date betwwen ########### 
                                    if ($minID < $maxID) {
                                        if (!$s->setIDRange($minID + 1, $maxID)) {
                                            sphinxErr(__LINE__, $s, 'setIDRange');
                                        }
                                    }
                                    if (!$result = $s->query($ps, $inds)) {
                                        sphinxErr(__LINE__, $s, 'query', $ps);
                                    }
                                    //echo "<pre>";
                                    //print_r($result);
                                    //echo "</pre>";
                                    //exit;
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
                    if (!empty($productidsarray)) {
                        $arr_unique = array_unique($productidsarray);
                        if ($search_type_and == 1 && $searches_count > 1) {
                            $productidsarray = array_diff_assoc($productidsarray, $arr_unique);
                        } else {
                            $productidsarray = $arr_unique;
                        }
                        
                        $andUnion = '';
                        $chunkdata = 10000;
                        //$newarray = array_chunk($productidsarray, 10000);
                        if ($total > 600000) {
                            $chunkdata = 50000;
                        } /*else if ($total > 1200000) {
                            $chunkdata = 30000;
                            //$chunkdata = 50000;
                        }
                       
                        $newarray = array_chunk($productidsarray, $chunkdata);
                        //ceil($total/$chunkdata);
                        //echo count($productidsarray).'===='.count($newarray); exit;
                        for ($u = 2; $u < 60; $u++) {
                            if (count($newarray) >= $u) {

                                $andUnion .= "union ( SELECT dd.productID   FROM  cscan_product_detail dd  WHERE dd.productID IN(" . implode(',', ($newarray[$u - 1])) . "))";
                            }else{
                                continue;
                            }
                        }

                        $andcond = " select B.productID FROM  (SELECT dd.productID FROM  cscan_product_detail dd  WHERE dd.productID IN(" . implode(',', $newarray[0]) . ") " . $andUnion . ")B";
                        $wheresearchproduct = " AND pd.productID in (" . $andcond . ") ";
                    }
                    if (empty($productidsarray)) {
                        $andcond = '-1';
                        $wheresearchproduct = " AND pd.productID in (" . $andcond . ") ";
                    }
                }
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
        }*/
        if ($where != '') {
            $where = ' WHERE ' . substr($where, 0, -5);
        }
    } else {
        $ojoin .= ',cscan_product_basket cb';
        $where = ' WHERE basket_id=' . $bid . ' AND userID=' . $_SESSION['sess_userID'] . ' AND cb.productID=pd.productID ';
        $saved = 1;
    }


    $ignoreindex = "";
    // $ignoreindex = " ignore index(entryID_sort_index)";
    //$ignoreindex = " ignore index(entryID_sort_index,cscan_product_company_index,c_p_d_productName_index,c_p_d_entryID_index,c_p_d_productHeadline_index,c_p_d_actual_addedToDatabase_index,  c_p_d_variantID_index,c_p_d_DMSource_index,c_p_d_mPanelID_index,c_p_d_mChannelID_index,c_p_d_sectorID_index,addedToDatabase_c_p_d_index,idx_comp,idx_comp2,idx_comp3)";
     //$ignoreindex = " ignore index(entryID_sort_index,cscan_product_company_index,c_p_d_productName_index,c_p_d_entryID_index,c_p_d_productHeadline_index,c_p_d_actual_addedToDatabase_index,  c_p_d_variantID_index,c_p_d_DMSource_index,c_p_d_mPanelID_index,c_p_d_mChannelID_index,c_p_d_sectorID_index,idx_comp,idx_comp2,idx_comp3)";
     $ignoreindex = " ignore index(entryID_sort_index,cscan_product_company_index,c_p_d_productName_index,c_p_d_entryID_index,c_p_d_productHeadline_index,c_p_d_actual_addedToDatabase_index,  c_p_d_variantID_index,c_p_d_DMSource_index,c_p_d_mPanelID_index,c_p_d_mChannelID_index,c_p_d_sectorID_index)";


    $selectQuery = "SELECT "; // SQL_NO_CACHE
    if ($docount) {
        //$ignoreindex = " ignore index(cscan_product_company_index,c_p_d_productName_index,c_p_d_entryID_index,c_p_d_productHeadline_index,c_p_d_actual_addedToDatabase_index,  c_p_d_variantID_index,c_p_d_DMSource_index,c_p_d_mPanelID_index,c_p_d_mChannelID_index,c_p_d_sectorID_index,addedToDatabase_c_p_d_index)";
        //$ignoreindex = " ignore index(cscan_product_company_index,c_p_d_productName_index,c_p_d_entryID_index,c_p_d_productHeadline_index,c_p_d_actual_addedToDatabase_index,  c_p_d_variantID_index,c_p_d_DMSource_index,c_p_d_mPanelID_index,c_p_d_mChannelID_index,c_p_d_sectorID_index,addedToDatabase_c_p_d_index,idx_comp,idx_comp2,idx_comp3)";
        //$ignoreindex = " ignore index(entryID_sort_index,cscan_product_company_index,c_p_d_productName_index,c_p_d_entryID_index,c_p_d_productHeadline_index,c_p_d_actual_addedToDatabase_index,  c_p_d_variantID_index,c_p_d_DMSource_index,c_p_d_mPanelID_index,c_p_d_mChannelID_index,c_p_d_sectorID_index,idx_comp,idx_comp2,idx_comp3)";
        $ignoreindex = " ignore index(entryID_sort_index,cscan_product_company_index,c_p_d_productName_index,c_p_d_entryID_index,c_p_d_productHeadline_index,c_p_d_actual_addedToDatabase_index,  c_p_d_variantID_index,c_p_d_DMSource_index,c_p_d_mPanelID_index,c_p_d_mChannelID_index,c_p_d_sectorID_index)";


        $selectQuery .= "COUNT(DISTINCT pd.productID)";
        $sortby = '';
    } else {
        if ($dograph != 0) {
            $sortby = '';
            /*######### For FICO, CreditVision and Vantage Score #########*/
            if($dograph==32 || $dograph==33 || $dograph==34){
             $score_range_field=',fico_range_id,vantage_range_id,creditVision_range_id';
            }
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
            delmethid,deliveryTypeId,postageId,presortedId,packageTypeId,responseMechID,FeeProductType,external_updates,external_fans,external_link,prescription,is_hphsa,subSubCategoryID,
            OfferExpiryDate,is_citi,riders,is_prescreen,isSurvey,IssueTypeID,traffic_sources,social_media_name,worksiteVoluntary,is_digital,panelist_sort,is_mobile,simple_domain,faux_check,socialmedia_adtype,personalization,offerOrigin,is_multicultural,FeeProduct,multiculturalmarkets,businessContent,is_affinion,groupSize,is_panelist_score$matchtext$score_range_field";
    }

    // $selectQuery .= " FROM cscan_product_detail pd$ocrtext$pjoin$cjoin$ccjoin$afjoin$affjoin$dmajoin$edcjoin$sect_j$pcjoin$bjoin$ejoin$mljoin$rjoin$tljoin$ajoin$sjoin$ojoin $where$owhere $sortby";
    //exit;
    // $ignoreindex	=	" ignore index(c_p_d_mChannelID_index,entryID_sort_index,c_p_d_mPanelID_index,c_p_d_productStatus_index,addedToDatabase_c_p_d_index) ";
    //print_r($_SESSION['sess_search_exclude']); die;
    if (in_array('digital_access', $_SESSION['sess_search_exclude'])) {
        // $pjoin=" JOIN cscan_panelists_product pp ON (pd.productID=pp.productID) ";
        //$where.=' AND pd.productID NOT IN (select productID from cscan_panelists_product) ';
    }
    //$ignoreindex=' force index (idx_comp2)';
    //$ignoreindex='';
    $selectQuery .= " FROM cscan_product_detail pd $ignoreindex$ocrtext$pjoin$cjoin$ccjoin$afjoin$affjoin$dmajoin$edcjoin$pcjoin$bjoin$ejoin$mljoin$rjoin$tljoin$ajoin$sjoin$ojoin$cpadsjoin $where$owhere$wheresearchproduct $sortby";
  //  echo $selectQuery; die;

    if ($dograph != 0) {
        $field = getDoGraph($dograph);
        /*######### For FICO, CreditVision and Vantage Score #########*/
        if(($dograph==32 && $fico_score=='') || ($dograph==33 && $credit_vision_score=='') ||($dograph==34 && $vantage_score=='')){
            $queryArrayData=explode("WHERE",$selectQuery);
            if(strstr($queryArrayData[0],'cscan_panelists_product')){
                $sql_join = " "; 
            }
            else
            {
                $sql_join = " LEFT JOIN cscan_panelists_product pp ON (pd.productID=pp.productID) ";
            }  
            $select_Query=$queryArrayData[0].$sql_join." WHERE ".$queryArrayData[1];
            $selectQuery=$select_Query;
        }
        $selectQuery = "SELECT COUNT($field) AS field_count,$field AS field_name FROM ($selectQuery) as t1 GROUP BY $field"; // SQL_NO_CACHE ORDER BY field_count DESC,field_name ASC
    }
//echo $selectQuery;exit;
    return array($selectQuery, $saved);
}
