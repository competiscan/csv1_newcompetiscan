<?php
require_once __DIR__ . '/../vendor/autoload.php';

function doQueryEmailAlertsphinx($search_id,$alert_dt) {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm, $SPHINX_name;
    $docount = false;
    $addedtodatabaseover=$sql_P2=$more=$sql_P3=$consumer=$ocrtext='';
    $dograph =$relev=$expans=$unapproved=$clear_ps=false;
    $bid = -1;    
    $search_panelist_date_over = -1;
    $search_values = array();
    $sess_userID = 0;   
    
    $searchKey = $mChannelID =$sectorID =$mPanelID = $addedToDatabase =$month1 =$month2 = $company =  $productName = '';
    $searchType = 'ocr2';
    $searchOption = 'boolean';       
    $sort = 'desc';       
    $incentive = $categoryID = $mTypeID =$subCategoryID =$cardStatus =$personalization =$gender =$age =$state ='';       
    $worksiteVoluntary = 0;
    $agentCommunicationID=$groupSize=$offerOrigin=$compaignLanguage=$affinityAssociation =$income_mult='';        
    $enhance = 1;
    $saved = $prescription =$search_panelist_date = $is_affinion =$is_military =0;       
    $fa_id_mult =$tl_id_mult =$siteCatID_mult =$pubTypeID_mult =$electronicID_mult =$DMA_ID_mult ='';       
    $approved_date = '0000-00-00 00:00:00';
    $approved_date_to = '0000-00-00 00:00:00';       
    $businessContent_mult=$delmethid_mult =$affinity_association=$AffinityCategoryID_mult=$search_competi_id =$ApplicationType_mult ='';        
    $is_multicultural =  $is_rewards = $is_incentive =$FeeProduct = 0;
    $search_rules =$IntroPricing_mult=$RewardsProgramEmphasis_mult=$responseMechID_mult=$multiculturalmarkets_mult='';       
    $CardNetwork_mult=$external_link=$FeeProductType = '';       
    $ca_related =$is_mover=$scsc_primary=$OptOutFirmOffer=$search_type_and=$is_hphsa=0;         
    $searchKey2=$riders_mult =$subSubCategoryID =$Income_Producing_Assets_Segment_Code =$cg_id = '';
    $is_citi =$is_CreditCardMentioned = 0;
    $spanelist_filter=$edc_id_mult=$AffinitySubCategoryID_mult=$ERateType_mult=$EOfferPrice_mult= '';
    $ETermLength_mult =$IssueTypeID_mult =$pcountry =$value_score= '';
    $is_ECancelFee =$is_Reloadable =$creditUnion = $is_mobile = 0; 
    
 
    $where = $sortby ='';    
    $pjoin = $matchtext =$ojoin =$owhere =$awhere =$dmajoin =$edcjoin = $cjoin ='';      
    $ccjoin = $afjoin =$affjoin =$sjoin = $sect_j = $pcjoin = $bjoin =$ejoin =$mljoin = '';  
    $ajoin =$rjoin = $tljoin =  '';
    
    $filter_range =$productidsarray= array();   
    $wheresearchproduct = $wherecondition = '';
    
    $savedQ = "SELECT searchKey,searchType,searchOption,mChannelID,sectorID,mPanelID,
                addedToDatabase,month1,month2,sort,company,productName,incentive,categoryID,mTypeID,subCategoryID,cardStatus,personalization,
                gender,age,state,worksiteVoluntary,agentCommunicationID,groupSize,offerOrigin,enhance,saved,compaignLanguage,affinityAssociation,income_mult,fa_id_mult,tl_id_mult,
                siteCatID_mult,pubTypeID_mult,approved_date,electronicID_mult,DMA_ID_mult,businessContent_mult,delmethid_mult,affinity_association,prescription,AffinityCategoryID_mult,
                search_panelist_date,is_affinion,is_military,search_competi_id,ApplicationType_mult,is_multicultural,search_rules,IntroPricing_mult,is_rewards,RewardsProgramEmphasis_mult,is_incentive,responseMechID_mult,
                multiculturalmarkets_mult,CardNetwork_mult,FeeProduct,external_link,FeeProductType,approved_date_to,ca_related,is_mover,scsc_primary,OptOutFirmOffer,searchKey2,search_type_and,riders_mult,is_hphsa,subSubCategoryID,Income_Producing_Assets_Segment_Code_mult,cg_id,is_citi,is_CreditCardMentioned,
                spanelist_filter,edc_id_mult,AffinitySubCategoryID_mult,ERateType_mult,EOfferPrice_mult,ETermLength_mult,is_ECancelFee,IssueTypeID_mult,pcountry,is_Reloadable,creditUnion,is_mobile,value_score,userID,lastSentDate,queryDate
                FROM cscan_search WHERE ID='" . $search_id . "'";
   
    $rs = $DRW->query($savedQ, $DRW_read);
    
    if($DRW->num_rows($rs)>0){ 
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
        $is_mobile = $data[84];
        if($data[85]!=''){
            $value_score = str_replace(",","','",$data[85]);
            $value_score ="'".$value_score."'";
        }else{
            $value_score ="";
        }
        $sess_userID=$data[86];
        $lastSentDate=$data[87];
        $queryDate=$data[88];
    }
    
    if ($lastSentDate != '' && $lastSentDate != '0000-00-00 00:00:00') {
        $addedtodatabaseover = $lastSentDate;
    } else {
        $addedtodatabaseover = $queryDate;
    }

    $weekago = date('Y-m-d', strtotime('-7 days')) . ' 00:00:00';

    if ($addedtodatabaseover < $weekago) {
        $addedtodatabaseover = $weekago;
    }    
    
   
    $mchannel_allow=$mpanel_allow=$sector_allow=$cat_allow=$subcat_allow=array();
    // Check allowed permission for user
    //echo $sess_userID; die;
    if (!empty($sess_userID)) {
        //Check allowed media channel for user
        $result2 = $DRW->query("SELECT mu.mChannelID FROM cscan_mc_users_allow mu,cscan_mchannel mc WHERE userID=$sess_userID AND mu.mChannelID=mc.mChannelID", $DRW_read);
        while ($data2 = $DRW->fetch_row($result2)) {
            $mchannel_allow[] = $data2[0];
        }
        @$DRW->free_result($result2);
        
        //Check allowed audience for user
        $result3 = $DRW->query("SELECT mu.mPanelID FROM cscan_mp_users_allow mu,cscan_mpanel mc WHERE userID=$sess_userID AND mu.mPanelID=mc.mPanelID", $DRW_read);
        while ($data3 = $DRW->fetch_row($result3)) {
            $mpanel_allow[] = $data3[0];
        }
        @$DRW->free_result($result3);   
        
        //Check allowed sector category subcategory for user
        $result4 = $DRW->query("SELECT su.sectorID,parentID FROM cscan_sector_users_allow su,cscan_sector cs WHERE userID=$sess_userID AND su.sectorID=cs.sectorID", $DRW_read);
        while ($data4 = $DRW->fetch_row($result4)) {
            if ($data4[1] == 0) {
               $sector_allow[] = $data4[0];
            } else {
                $result5 = $DRW->query("SELECT parentID FROM cscan_sector WHERE sectorID=$data4[1]", $DRW_read);
                $data5 = $DRW->fetch_row($result5);
                if ($data5[0] == 0) {
                    $cat_allow[]=$data5[0];                    
                } else {                    
                    $subcat_allow[]=$data5[0];
                }
                @$DRW->free_result($result5);
            }
        }
        @$DRW->free_result($result4);
        
        //Check excluded fields for user
        $sess_search_exclude = array();
        $result6 = $DRW->query("SELECT search_field FROM cscan_search_exclude WHERE userID=$sess_userID", $DRW_read);
        while ($data6 = $DRW->fetch_row($result6)) {
            $sess_search_exclude[] = $data6[0];
        }
        @$DRW->free_result($result6);
    }

    $where .= " (productStatus=1";    
    $where .= ") AND  ";     
        
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
        $mchannelID=implode(',',$mchannel_allow);
    }
    if (empty($mPanelID)) {        
        $mPanelID = implode(',',$mpanel_allow);
    }
    $isBiz = '';
    if (empty($state) && in_array('canada', $sess_search_exclude)) {
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
    
    /* ########## For Digital media channel online display, online video, sem */

    $mChannelID_mod=array();        
    if(strstr($mChannelID,',')){
       $mChannelID_mod=explode(',',$mChannelID);
    }else{ 
       $mChannelID_mod[]=$mChannelID;
    }
     
    if(!empty($mChannelID_mod) AND ($is_mobile>0)){          
     
        $where.=' (';
        $pp='1';
        foreach($mChannelID_mod as $findchannel){
            if($pp!=1){
                $where.=' OR ';
            }
            if(($findchannel=='5' || $findchannel=='9' || $findchannel=='10') AND $is_mobile>0 ){
                $where.='(pd.mChannelID='.$findchannel.' AND is_mobile='.$is_mobile.')';
            }else{
                 $where.='pd.mChannelID='.$findchannel;
            }          

            $pp++;
        }
        $where.=' ) AND';
    }       
        
    /* ##########  End for digital Module  ##########  */
    $exactArray = array();
    /* For Digital Media Channel */

    if($is_mobile>0){
        $multExactArray = array('pd.mPanelID' => $mPanelID);
    }else{
       $multExactArray = array('pd.mChannelID' => $mChannelID,'pd.mPanelID' => $mPanelID); 
    }
    /*####### End For digital Media */
        
    //$multExactArray = array('pd.mChannelID' => $mChannelID, 'pd.mPanelID' => $mPanelID);
    $likeArray = array('incentive' => $incentive);
    $multArray = array();
    $otherArray = array('company' => $company, 'affinity_association' => $affinity_association, 'addedToDatabase' => $addedToDatabase, 'month' => $month, 'approved_date' => $approved_date, 'search_competi_id' => $search_competi_id, 'ApplicationType_mult' => $ApplicationType_mult, 'cg_id' => $cg_id, 'rstate' => $rstate, 'pcountry' => $pcountry);
    $panelistArray = array('cspsp.ppageID' => $age, 'cspsp.pincomeID' => $income_mult, 'cspsp.isBiz' => $isBiz, 'dmaps.code' => $DMA_ID_mult, 'cspsp.ppdate' => $ppdate, 'ppdate_month' => $ppdate_month, 'ppstateID' => $ppstateID, 'cspsp.pgender' => $pgender, 'cscwc.Income_Producing_Assets_Segment_Code' => $Income_Producing_Assets_Segment_Code, 'edc_id' => $edc_id_mult,'ValueScore'=>$value_score);
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
        $multExactArray = array_merge($multExactArray, array('pd.mTypeID' => $mTypeID, 'pd.delmethid' => $delmethid_mult));
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
        
        $part1 = "CONCAT(',',pd.sectorID,',') REGEXP ',$sid,'";
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
                $part2 = "CONCAT(',',pd.categoryID,',') REGEXP ',$cid,'";
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
                        $part3 = "CONCAT(',',pd.subCategoryID,',') REGEXP ',$scid,'";
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
                                $part4 = "CONCAT(',',pd.subSubCategoryID,',') REGEXP ',$sscid,'";
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


                if (!empty($val)) {
                    if ($val == 1) {
                        $fieldval = 1;
                    } elseif ($val == 2) {
                        $fieldval = 0;
                    }
                    if ($field == 'OptOutFirmOffer') {

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
            //$pjoin = $pjoin_left . ' JOIN cscan_panelists_product pp ON (pd.productID=pp.productID' . $pjoin_filter . ')';
            if ($field == 'Income_Producing_Assets_Segment_Code' || $field == 'ValueScore') {
               // $ajoin = " JOIN cscan_panelists_appends cpas ON (cpas.panelist_id=pp.panelist_id) ";

            } elseif ($field == 'edc_id') {
                $edcjoin = ' JOIN cscan_edc_postalcode edc ON(pp.pppostalcode=edc.pppostalcode)';
            } elseif ($field == 'dmaps.code') {

                $dmajoin = ' JOIN cscan_panelists_product ppp ON (pd.productID=ppp.productID) JOIN cscan_dma_code_postalcode dmaps ON (ppp.pppostalcode=dmaps.pppostalcode)';
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
                    } else if ($field == 'dmaps.code') {
                        $where .= $field . " in (" . substr($val, 0, -1) . ") AND ";
                    }else if ($field == 'ValueScore') {
                        $where .= '';
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

    if($value_score!=''){
        $where .= "  cscwc.ValueScore_for_Household IN (".$value_score.") AND ";
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
            if ($field == 'pd.mPanelID') {
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
    
    $partsArray = array();
    $seccatsubArray = get_seccatsub(implode(',', $_SESSION['sess_sector']), implode(',', $_SESSION['sess_category']), implode(',', $_SESSION['sess_subcategory']));

    foreach ($seccatsubArray as $sid => $cArray) {
        
        $part1 = "CONCAT(',',pd.sectorID,',') REGEXP ',$sid,'";       

        $partsArray[] = '(' . $part1 . " AND CONCAT(',',pd.categoryID,',') REGEXP  ',0,'  AND CONCAT(',',pd.subCategoryID,',') REGEXP  ',0,')";
        foreach ($cArray as $cid => $scArray) {            
            $part2 = "CONCAT(',',pd.categoryID,',') REGEXP ',$cid,'";            
            $partsArray[] = '(' . $part1 . ' AND ' . $part2 . " AND CONCAT(',',pd.subCategoryID,',') REGEXP  ',0,')";
            foreach ($scArray as $scid => $a) {            
                $part3 = "CONCAT(',',pd.subCategoryID,',') REGEXP ',$scid,'";
                $partsArray[] = '(' . $part1 . ' AND ' . $part2 . ' AND ' . $part3 . ')';
            }
        }
    }

    if (count($partsArray) > 0) {
        $where .= '(' . implode(' OR ', $partsArray) . ') AND ';
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

    $where .= $whereandcond;
    
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
            $ocrtext .= ' JOIN cscan_document_text_search dt ON(pd.productID=dt.productID)';
        }
        elseif ($searchType == 'ocr2' || $searchType == 'fulltext2' || $searchType == 'ocr_fulltext2') 
        {
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

                    $inds = 'base_index_' . $SPHINX_name . $add . ',delta_index_' . $SPHINX_name . $add . $digital_index;

                    $ps = parseSphinx($s, $sk);

                    if (trim($ps) != '') {
                        $currcount = 0;
                        $step = $total = 50000;
                        if (!$s->setLimits(0, 1, 1)) {
                            sphinxErr(__LINE__, $s, 'setLimits');
                        }

                        if (!$result = $s->query($ps, $inds)) {
                            sphinxErr(__LINE__, $s, 'query', $ps);
                            // echo 'kkkk';
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
                            for ($offset = 0; $offset <= $maxID; $offset += $step) {
                                $s = startSphinx();
                                if (!$s->setLimits(0, $step, $step)) {
                                    sphinxErr(__LINE__, $s, 'setLimits');
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
                        $chunkdata = 20000;
                    } else if ($total > 1200000) {
                        $chunkdata = 30000;
                    }
                    $newarray = array_chunk($productidsarray, $chunkdata);
                    // echo count($productidsarray).'===='.count($newarray); exit;
                    for ($u = 2; $u < 60; $u++) {
                        if (count($newarray) >= $u) {

                            $andUnion .= "union ( SELECT dd.productID   FROM  cscan_product_detail dd  WHERE dd.productID IN(" . implode(',', ($newarray[$u - 1])) . "))";
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
        }else{
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
            }
    }
    if ($where != '') {
        $where = ' WHERE ' . substr($where, 0, -5);
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
        
        
        ######################### Added for Excel export ################################
        
        
        $primarysectorjoin= " LEFT JOIN cscan_sector csps on (csps.sectorID=(SELECT scsc_sectorID FROM cscan_scsc_product WHERE productID=pd.productID AND scsc_sort=1)) "; 
    
        $primarycatjoin= " LEFT JOIN cscan_sector cspc on (cspc.sectorID=(SELECT scsc_categoryID FROM cscan_scsc_product WHERE productID=pd.productID AND scsc_sort=1)) ";


        $primarysubcatjoin= " LEFT JOIN cscan_sector cspsc on (cspsc.sectorID=(SELECT scsc_subCategoryID FROM cscan_scsc_product WHERE productID=pd.productID AND scsc_sort=1)) ";

        $primarysubsubcatjoin= " LEFT JOIN cscan_sector cspssc on (cspssc.sectorID=(SELECT scsc_subSubCategoryID FROM cscan_scsc_product WHERE productID=pd.productID AND scsc_sort=1)) ";
      

        $mchanneljoin= " LEFT JOIN cscan_mchannel cmc on (cmc.mChannelID=pd.mChannelID) ";
        $mpanneljoin= " LEFT JOIN cscan_mpanel cmp on (cmp.mPanelID=pd.mPanelID) ";
        $statejoin= " LEFT JOIN cscan_state cst on (cst.stateID=pd.state) ";

        $countryjoin=" LEFT JOIN ISO31661_alpha2code cscount on (cscount.code=cst.countryCode and cst.stateID=pd.state) ";
        $countryjoin2=" LEFT JOIN cscan_product_detail_state cscount2 on (cscount2.productID=pd.productID) LEFT JOIN ISO31661_alpha2code cscount3 on (cscount3.code=cscount2.countryCode_copy) ";

        $agejoin=" LEFT JOIN cscan_age_product cap on (cap.age_pID=pd.age) ";

        $mtypejoin="  LEFT JOIN cscan_mtype cmt on (cmt.mTypeID=pd.mTypeID) ";

        $delmethodjoin="  LEFT JOIN cscan_delivery_method cdm on (cdm.delmethid=pd.delmethid) ";


        $commjoin="  LEFT JOIN cscan_agent_communication cacom on (cacom.ID=pd.agentCommunicationID) ";


        $affcatjoin=" LEFT JOIN cscan_affinity_category csafc on csafc.AffinityCategoryID=(
          SELECT catmap.AffinityCategoryID FROM cscan_aff_cat catmap, cscan_affinity_category catmaster WHERE catmap.affinityID=(SELECT pa.affinityID FROM cscan_affinity pa,cscan_affinity_product pp
                                  WHERE pa.affinityID=pp.affinityID AND pp.productID=pd.productID limit 0,1)  AND catmap.AffinityCategoryID=catmaster.AffinityCategoryID and catmaster.parentID=0 limit 0,1)";

        $affsubcatjoin=" LEFT JOIN cscan_affinity_category csafsc on csafsc.AffinityCategoryID=(
        SELECT catmap2.AffinityCategoryID FROM cscan_aff_cat catmap2, cscan_affinity_category catmaster2 WHERE catmap2.affinityID=(SELECT pa2.affinityID FROM cscan_affinity pa2,cscan_affinity_product pp2
                                WHERE pa2.affinityID=pp2.affinityID AND pp2.productID=pd.productID limit 0,1)  AND catmap2.AffinityCategoryID=catmaster2.AffinityCategoryID and catmaster2.parentID<>0 limit 0,1)";

        $filesizejoin="  LEFT JOIN cscan_document csdoc on (csdoc.productID=pd.productID AND csdoc.document_id=1) ";

        		
		/* For Additional Details field */
		
        $panelistsjoin="  JOIN cscan_panelists_product cspsp on (cspsp.productID=pd.productID) ";
        $panelists_detailjoin="  LEFT JOIN cscan_panelists cspsp_dt on (cspsp_dt.panelist_id=cspsp.panelist_id) ";
        $ecohortcodejoin="  LEFT JOIN cscan_ecohort_code csehc on (csehc.code=cspsp_dt.ECohort_Code) ";
        $psycodejoin="  LEFT JOIN cscan_psy_code cspsyc on (cspsyc.code=cspsp_dt.PSY_CODE) ";
        $pzmcodejoin="  LEFT JOIN cscan_pzm_code cspzm on (cspzm.code=cspsp_dt.PZM_CODE) ";
        $cnxcodejoin="  LEFT JOIN cscan_cnx_code cscnx on (cscnx.code=cspsp_dt.CNX_CODE) ";
        $wealthcompjoin="  LEFT JOIN cscan_panelists_appends cscwc on (cscwc.panelist_id=cspsp.panelist_id) ";
        $ethinicityjoin="  LEFT JOIN cscan_ethnicity_code csethc on (csethc.code=cspsp_dt.ET_ETHNICITY) ";
        $religionjoin="  LEFT JOIN cscan_religion_code csrlc on (csrlc.code=cspsp_dt.ET_RELIGION) ";
        $languagejoin="  LEFT JOIN cscan_religion_code cslnc on (cslnc.code=cspsp_dt.ET_LANGUAGE) ";
        $groupjoin="  LEFT JOIN cscan_group_code csgrpc on (csgrpc.code=cspsp_dt.ET_GROUP) ";
        $countrycjoin="  LEFT JOIN cscan_country_code cscntc on (cscntc.code=cspsp_dt.ET_COUNTRY) ";		
        $assimilationjoin="  LEFT JOIN cscan_assimilation_code csassc on (csassc.code=cspsp_dt.ET_ASSIMILATION) ";
        $incomproducingjoin="  LEFT JOIN cscan_income_producing_assets_segment_code csipasc on (csipasc.code=cscwc.Income_Producing_Assets_Segment_Code) ";

        $valuescorejoin="  LEFT JOIN cscan_valuescore_for_household csvsfhh on (csvsfhh.code=cscwc.ValueScore_for_Household) ";
        $householdincomejoin="  LEFT JOIN cscan_household_income_identifier_narrow_band cshiinb on (cshiinb.code=cscwc.Household_Income_Identifier_Narrow_Band) ";

        $homeownerrenterjoin="  LEFT JOIN cscan_advantage_home__owner_renter_code csahorc on (csahorc.code=cscwc.Advantage_Home__Owner_Renter_Code) ";

        $dmacodejoin="  LEFT JOIN cscan_dma_code csdmac on (csdmac.code=cscwc.DMA_CODE) ";


        $selectQuery .= " DATE_FORMAT(cspsp.ppdate,'%Y-%m') as 'Month',cspsp_dt.competi_id as 'Panelist ID',cspsp.invitationID as 'Invitation ID',cspsp.trackingID as 'Last 4 Digits',cspsp_dt.postalcode as 'Zip Code',cspsp_dt.ATP,cspsp_dt.Income360,cspsp_dt.DSDollar,cspsp_dt.DSI,IF(cspsp_dt.ECohort_Code IS NULL or cspsp_dt.ECohort_Code = '', '', cspsp_dt.ECohort_Code) as 'Financial Cohorts Code',IF(csehc.description IS NULL or csehc.description = '', '', csehc.description) as 'Financial Cohorts Descriptor',IF(csehc.group_description IS NULL or csehc.group_description = '', '', csehc.group_description) as 'Financial Cohorts Group',cspsp_dt.PSY_FLAG,cspsp_dt.PSY_CODE,IF(cspsyc.description IS NULL or cspsyc.description = '', '', cspsyc.description) as 'PSY_CODE_DESC',IF(cspsyc.gr IS NULL or cspsyc.gr = '', '', cspsyc.gr) as 'PSY_GR',IF(cspsyc.gr_num IS NULL or cspsyc.gr_num = '', '', cspsyc.gr_num) as 'PSY_GR#',cspsp_dt.PZM_FLAG,cspsp_dt.PZM_CODE,IF(cspzm.description IS NULL or cspzm.description = '', '', cspzm.description) as 'PZM_CODE_DESC',IF(cspzm.gr IS NULL or cspzm.gr = '', '', cspzm.gr) as 'PZM_GR',IF(cspzm.gr_num IS NULL or cspzm.gr_num = '', '', cspzm.gr_num) as 'PZM_GR#',IF(cspsp_dt.CNX_FLAG IS NULL or cspsp_dt.CNX_FLAG = '', '', cspsp_dt.CNX_FLAG) as 'CNX_FLAG',cspsp_dt.CNX_CODE,IF(cscnx.description IS NULL or cscnx.description = '', '', cscnx.description) as 'CNX_CODE_DESC',IF(cscnx.gr IS NULL or cscnx.gr = '', '', cscnx.gr) as 'CNX_GR',IF(cscnx.gr_num IS NULL or cscnx.gr_num = '', '', cscnx.gr_num) as 'CNX_GR#',IF(cscwc.WC_Annuities IS NULL or cscwc.WC_Annuities = '', '', cscwc.WC_Annuities) as 'WealthComplete Annuities',IF(cscwc.WC_Stocks IS NULL or cscwc.WC_Stocks = '', '', cscwc.WC_Stocks) as 'WealthComplete Stocks',

        IF(cscwc.WC_Bonds IS NULL or cscwc.WC_Bonds = '', '', cscwc.WC_Bonds) as 'WealthComplete Bonds',IF(cscwc.WC_Deposits IS NULL or cscwc.WC_Deposits = '', '', cscwc.WC_Deposits) as 'WealthComplete Deposits',IF(cscwc.WC_MutualFunds IS NULL or cscwc.WC_MutualFunds = '', '', cscwc.WC_MutualFunds) as 'WealthComplete Mutual Funds',IF(cscwc.WC_Other IS NULL or cscwc.WC_Other = '', '', cscwc.WC_Other) as 'WealthComplete Other',IF(cscwc.WC_TotalAssets IS NULL or cscwc.WC_TotalAssets = '', '', cscwc.WC_TotalAssets) as 'WealthComplete Total Assets',IF(cscwc.WC_CD IS NULL or cscwc.WC_CD = '', '', cscwc.WC_CD) as 'WealthComplete CD',IF(cscwc.WC_InterestChecking IS NULL or cscwc.WC_InterestChecking = '', '',cscwc.WC_InterestChecking) as 'WealthComplete Interest Checking',IF(cscwc.WC_MoneyMarketDepositAccounts IS NULL or cscwc.WC_MoneyMarketDepositAccounts = '', '',cscwc.WC_MoneyMarketDepositAccounts) as 'WealthComplete Money Market Deposit Accounts',IF(cscwc.WC_NonInterestChecking IS NULL or cscwc.WC_NonInterestChecking = '', '',cscwc.WC_NonInterestChecking) as 'WealthComplete Non-Interest Checking',IF(cscwc.WC_OtherCheckingAccounts IS NULL or cscwc.WC_OtherCheckingAccounts = '', '', cscwc.WC_OtherCheckingAccounts) as 'WealthComplete Savings',IF(cscwc.InvestylesAdviceOrientedAssets IS NULL or cscwc.InvestylesAdviceOrientedAssets ='', '',cscwc.InvestylesAdviceOrientedAssets) as 'Investyles Advice-Oriented Assets',IF(cscwc.InvestylesRetirementProductAssets IS NULL or cscwc.InvestylesRetirementProductAssets = '', '', cscwc.InvestylesRetirementProductAssets) as 'Investyles Retirement Product Assets',IF(cscwc.InvestylesSelfDirectedAssets IS NULL or cscwc.InvestylesSelfDirectedAssets = '', '', cscwc.InvestylesSelfDirectedAssets) as 'Investyles Self-Directed Assets',IF(cscwc.eSpectrum IS NULL or cscwc.eSpectrum = '', '', cscwc.eSpectrum) as 'eSpectrum',IF(csethc.description IS NULL or csethc.description = '', '', csethc.description) as 'ETHNICITY_DESC',IF(csrlc.description IS NULL or csrlc.description = '', '', csrlc.description) as 'RELIGION_DESC',

        IF(cslnc.description IS NULL or cslnc.description = '', '', cslnc.description) as 'LANGUAGE_DESC'
        ,IF(csgrpc.description IS NULL or csgrpc.description = '', '', csgrpc.description) as 'GROUP_DESC',IF(cscntc.description IS NULL or cscntc.description = '', '', cscntc.description) as 'COUNTRY_DESC',IF(csgrpc.description IS NULL or csgrpc.description = '', '', csgrpc.description) as 'GROUP_DESC',IF(csassc.description IS NULL or csassc.description = '', '', csassc.description) as 'ASSIMILATION_DESC',IF(cscwc.ValueScore_for_Household IS NULL or cscwc.ValueScore_for_Household = '', '', cscwc.ValueScore_for_Household) as 'ValueScore for Household Code',IF(csvsfhh.description IS NULL or csvsfhh.description = '', '', csvsfhh.description) as 'ValueScore for Household',

        IF(cscwc.HH_Income_Index IS NULL or cscwc.HH_Income_Index = '', '', cscwc.HH_Income_Index) as 'HH Income Index',IF(cscwc.Birth_date_of_person_for_first_person_in_household IS NULL or cscwc.Birth_date_of_person_for_first_person_in_household = '', '', cscwc.Birth_date_of_person_for_first_person_in_household) as 'Birth date of person for first person in household',IF(csipasc.description IS NULL or csipasc.description = '', '', csipasc.description) as 'Income Producing Assets Segment Code  *R*',IF(cscwc.Household_Income_Identifier_Narrow_Band IS NULL or cscwc.Household_Income_Identifier_Narrow_Band = '', '', cscwc.Household_Income_Identifier_Narrow_Band) as 'Household Income Identifier Narrow Band Code',IF(cshiinb.description IS NULL or cshiinb.description = '', '', cshiinb.description) as 'Household Income Identifier Narrow Band',

        IF(csahorc.description IS NULL or csahorc.description = '', '', csahorc.description) as 'Advantage Home  Owner / Renter Code',IF(cscwc.Advantage_Home_Owner_Renter_Level IS NULL or cscwc.Advantage_Home_Owner_Renter_Level = '', '', cscwc.Advantage_Home_Owner_Renter_Level) as 'Advantage Home Owner / Renter Level',IF(cscwc.DMA_CODE IS NULL or cscwc.DMA_CODE = '', '', cscwc.DMA_CODE) as 'DMA CODE',
        IF(csdmac.description IS NULL or csdmac.description = '', '', csdmac.description) as 'DMA Name',( CASE WHEN
cscwc.Gender_code='1' THEN 'Male' WHEN cscwc.Gender_code='2' THEN 'Female' ELSE '' END ) AS 'Gender code',IF(cscwc.Occupation_code IS NULL or cscwc.Occupation_code = '', '', cscwc.Occupation_code) as 'Occupation code',IF(cscwc.MSA_CODE IS NULL or cscwc.MSA_CODE = '', '', cscwc.MSA_CODE) as 'MSA CODE',IF(cscwc.inq_win_past_6_mnths_except_promo_and_eval IS NULL or cscwc.inq_win_past_6_mnths_except_promo_and_eval = '', '', cscwc.inq_win_past_6_mnths_except_promo_and_eval) AS '# inq w/in past 6 mnths except promo and eval',IF(cscwc.Age_of_oldest_account_months IS NULL or cscwc.Age_of_oldest_account_months = '', '', cscwc.Age_of_oldest_account_months) AS 'Age of oldest account (months)',IF(cscwc.Age_of_newest_account_months IS NULL or cscwc.Age_of_newest_account_months = '', '', cscwc.Age_of_newest_account_months) AS 'Age of newest account (months)',


        IF(cscwc.of_accounts_opened_in_the_last_6_months IS NULL or cscwc.of_accounts_opened_in_the_last_6_months = '', '', cscwc.of_accounts_opened_in_the_last_6_months) AS '# of accounts opened in the last 6 months',IF(cscwc.of_accounts_opened_in_the_last_12_months IS NULL or cscwc.of_accounts_opened_in_the_last_12_months = '', '', cscwc.of_accounts_opened_in_the_last_12_months) AS '# of accounts opened in the last 12 months',IF(cscwc.of_accounts_opened_in_the_last_12_months IS NULL or cscwc.of_accounts_opened_in_the_last_12_months = '', '', cscwc.of_accounts_opened_in_the_last_12_months) AS '# of accounts opened in the last 12 months',IF(cscwc. 	of_accounts_opened_in_the_last_24_months IS NULL or cscwc.of_accounts_opened_in_the_last_24_months = '', '', cscwc. 	of_accounts_opened_in_the_last_24_months) AS '# of accounts opened in the last 24 months',IF(cscwc.of_accounts IS NULL or cscwc.of_accounts = '', '', cscwc.of_accounts) AS '# of accounts',

        IF(cscwc.of_active_accounts IS NULL or cscwc.of_active_accounts = '', '', cscwc.of_active_accounts) AS '# of active accounts',IF(cscwc.Total_credit_limit_for_active_accounts IS NULL or cscwc.Total_credit_limit_for_active_accounts = '', '', cscwc.Total_credit_limit_for_active_accounts) AS 'Total credit limit for active  accounts',IF(cscwc.of_accounts_currently_rated_satisfactory IS NULL or cscwc.of_accounts_currently_rated_satisfactory = '', '', cscwc.of_accounts_currently_rated_satisfactory) AS '# of accounts currently rated satisfactory',IF(cscwc.of_accounts_currently_bad_debt IS NULL or cscwc.of_accounts_currently_bad_debt = '', '', cscwc.of_accounts_currently_bad_debt) AS '# of accounts currently bad debt',IF(cscwc.Average_of_months_opened IS NULL or cscwc.Average_of_months_opened = '', '',cscwc.Average_of_months_opened) AS 'Average # of months opened',IF(cscwc.of_active_accts_with_balance_50_limit IS NULL or cscwc.of_active_accts_with_balance_50_limit = '', '',cscwc.of_active_accts_with_balance_50_limit) AS '# of active accts with balance >= 50% limit',IF(cscwc.of_bank_revolving_accounts IS NULL or cscwc.of_bank_revolving_accounts= '', '',cscwc.of_bank_revolving_accounts) AS '# of bank revolving accounts',IF(cscwc.of_department_store_accounts IS NULL or cscwc.of_department_store_accounts= '', '',cscwc.of_department_store_accounts) AS '# of department store accounts',IF(cscwc.of_active_bank_revolving_accounts IS NULL or cscwc.of_active_bank_revolving_accounts= '', '',cscwc.of_active_bank_revolving_accounts) AS '# of active bank revolving accounts',

        IF(cscwc.active_dept_store_accts_wo_closed_narratives IS NULL or cscwc.active_dept_store_accts_wo_closed_narratives= '', '',cscwc.active_dept_store_accts_wo_closed_narratives) AS '# active dept store accts w/o closed narratives',IF(cscwc.Total_limit_for_active_bank_revolving_accts IS NULL or cscwc.Total_limit_for_active_bank_revolving_accts= '', '',cscwc.Total_limit_for_active_bank_revolving_accts) AS 'Total limit for active bank revolving accts',IF(cscwc.Total_credit_limit_for_active_dept_store_accounts IS NULL or cscwc.Total_credit_limit_for_active_dept_store_accounts= '', '',cscwc.Total_credit_limit_for_active_dept_store_accounts) AS 'Total credit limit for active dept store accounts',IF(cscwc.of_total_credit_union_accounts IS NULL or cscwc.of_total_credit_union_accounts= '', '',cscwc.of_total_credit_union_accounts) AS '# of total credit union accounts',IF(cscwc.Presence_of_Bankruptcy IS NULL or cscwc.Presence_of_Bankruptcy= '', '',cscwc.Presence_of_Bankruptcy) AS 'Presence of Bankruptcy',IF(cscwc.accts_rated_bad_debt_of_derogatory24_mnths IS NULL or cscwc.accts_rated_bad_debt_of_derogatory24_mnths= '', '',cscwc.accts_rated_bad_debt_of_derogatory24_mnths) AS '# accts rated bad debt + # of derogatory-24 mnths',IF(cscwc.Age_of_oldest_active_mortgage IS NULL or cscwc.Age_of_oldest_active_mortgage= '', '',cscwc.Age_of_oldest_active_mortgage) AS 'Age of oldest active mortgage',IF(cscwc.Balance_for_active_mortgage_accounts IS NULL or cscwc.Balance_for_active_mortgage_accounts= '', '',cscwc.Balance_for_active_mortgage_accounts) AS 'Balance for active mortgage accounts',
        IF(cscwc.High_credit_for_active_mortgage_accounts IS NULL or cscwc.High_credit_for_active_mortgage_accounts= '', '',cscwc.High_credit_for_active_mortgage_accounts) AS 'High credit for active mortgage accounts',IF(cscwc.Number_of_active_mortgage_accounts IS NULL or cscwc.Number_of_active_mortgage_accounts= '', '',cscwc.Number_of_active_mortgage_accounts) AS 'Number of active mortgage accounts',IF(cscwc.RAPA_EMLC_ZIP_REL IS NULL or cscwc.RAPA_EMLC_ZIP_REL= '', '',cscwc.RAPA_EMLC_ZIP_REL) AS 'ISO Risk Quality Index Auto:  BG/ZIP',IF(cscwc.RAPA_EMLC_COUNTY_REL IS NULL or cscwc.RAPA_EMLC_COUNTY_REL= '', '',cscwc.RAPA_EMLC_COUNTY_REL) AS 'ISO Risk Quality Index Auto:  BG/County', 

        IF(cscwc.RAPA_EMLC_STATE_REL IS NULL or cscwc.RAPA_EMLC_STATE_REL= '', '',cscwc.RAPA_EMLC_STATE_REL) AS 'ISO Risk Quality Index Auto:  BG/State',IF(cscwc.RAHO_HOMLC_ZIP_REL IS NULL or cscwc.RAHO_HOMLC_ZIP_REL= '', '',cscwc.RAHO_HOMLC_ZIP_REL) AS 'ISO Risk Quality Index Home:  BG/ZIP',IF(cscwc.RAHO_HOMLC_COUNTY_REL IS NULL or cscwc.RAHO_HOMLC_COUNTY_REL= '', '',cscwc.RAHO_HOMLC_COUNTY_REL) AS 'ISO Risk Quality Index Home:  BG/County',IF(cscwc.RAHO_HOMLC_STATE_REL IS NULL or cscwc.RAHO_HOMLC_STATE_REL= '', '',cscwc.RAHO_HOMLC_STATE_REL) AS 'ISO Risk Quality Index Home:  BG/State',";
	
	$sectrarr=explode(',',$sectorID);
        $mediaarr=explode(',',$mChannelID);
        //print_r($mediaarr); 	
		          
        $selectQuery .= " pd.company as Company,pd.secondCompany as 'Second Company',IF((SELECT GROUP_CONCAT(CS2.sectorName) FROM cscan_sector CS2 WHERE  find_in_set(CS2.sectorID,TRIM(pd.sectorID))) IS NULL, '',(SELECT GROUP_CONCAT(CS2.sectorName) FROM cscan_sector CS2 WHERE find_in_set(CS2.sectorID,TRIM(pd.sectorID))))  AS 'Sector',IF((SELECT GROUP_CONCAT(CS3.sectorName) FROM cscan_sector CS3 WHERE  find_in_set(CS3.sectorID,TRIM(pd.categoryID))) IS NULL, '',(SELECT GROUP_CONCAT(CS3.sectorName) FROM cscan_sector CS3 WHERE find_in_set(CS3.sectorID,TRIM(pd.categoryID))))  AS 'Category',IF((SELECT GROUP_CONCAT(CS4.sectorName) FROM cscan_sector CS4 WHERE  find_in_set(CS4.sectorID,TRIM(pd.subCategoryID))) IS NULL, 'Not mentioned',(SELECT GROUP_CONCAT(CS4.sectorName) FROM cscan_sector CS4 WHERE find_in_set(CS4.sectorID,TRIM(pd.subCategoryID))))  AS 'Sub Category',IF((SELECT GROUP_CONCAT(CS5.sectorName) FROM cscan_sector CS5 WHERE  find_in_set(CS5.sectorID,TRIM(pd.subSubCategoryID))) IS NULL, 'Not mentioned',(SELECT GROUP_CONCAT(CS5.sectorName) FROM cscan_sector CS5 WHERE find_in_set(CS5.sectorID,TRIM(pd.subSubCategoryID))))  AS 'Sub Sub Category',pd.entryID,CONCAT('http://www.competiscan.com/index.php?product=',pd.productID) as 'EntryID Link',pd.productHeadline as Headline, IF((SELECT GROUP_CONCAT(cacom.type) FROM cscan_agent_communication cacom WHERE  find_in_set(cacom.ID,TRIM(pd.agentCommunicationID))) IS NULL, '',(SELECT GROUP_CONCAT(cacom.type) FROM cscan_agent_communication cacom WHERE  find_in_set(cacom.ID,TRIM(pd.agentCommunicationID))))  AS 'Communications Type',cmc.mChannelName as MediaChannel, cmp.mPanelName as Audience";
    
    
        if(in_array('5',$mChannelID_mod) || in_array('9',$mChannelID_mod) || in_array('10',$mChannelID_mod)){			
            $selectQuery .= ",( CASE WHEN pd.is_mobile='1' THEN 'Desktop' WHEN pd.is_mobile='2' THEN 'Mobile' WHEN pd.is_mobile='3' THEN 'In App Android' WHEN pd.is_mobile='4' THEN 'In App IOS' WHEN pd.is_mobile='5' THEN 'Social' ELSE '' END ) AS 'Digital Source',IF(pd.simple_domain IS NULL or pd.simple_domain = '', '',pd.simple_domain) as 'Simple Domain'";  

        } 

        //$selectQuery .= " ,IF(cst.stateName IS NULL or cst.stateName = '', '', cst.stateName) as 'State/Province',IF(cscount.country IS NULL or cscount.country = '', cscount3.country, cscount.country) as 'Country'";

        $selectQuery .= " ,IF((SELECT GROUP_CONCAT(cst.stateName) FROM cscan_state cst WHERE  find_in_set(cst.stateID,TRIM(pd.state))) IS NULL, '',(SELECT GROUP_CONCAT(cst.stateName) FROM cscan_state cst WHERE  find_in_set(cst.stateID,TRIM(pd.state))))  AS 'State/Province'";

        $selectQuery .= " ,IF(cscount.country IS NULL or cscount.country = '', cscount3.country, cscount.country) as 'Country'";

        ####### For Print Media Channel ###############
          $publicationjoin='';					
        if(in_array('2',$mediaarr)){ 

            $selectQuery .= ",IF((SELECT GROUP_CONCAT(cspu.publicationName) FROM cscan_publication cspu, cscan_publication_product cspup WHERE  cspu.publicationID=cspup.publicationID AND cspup.productID=pd.productID) IS NULL, '',(SELECT GROUP_CONCAT(cspu.publicationName) FROM cscan_publication cspu, cscan_publication_product cspup WHERE  cspu.publicationID=cspup.publicationID AND cspup.productID=pd.productID))  AS 'Publication'";

            $selectQuery .= ",IF((SELECT GROUP_CONCAT(DATE_FORMAT(cspup2.monthYear,'%m/%d/%Y')) FROM cscan_publication cspu2, cscan_publication_product cspup2 WHERE  cspu2.publicationID=cspup2.publicationID AND cspup2.productID=pd.productID) IS NULL, '',(SELECT GROUP_CONCAT(DATE_FORMAT(cspup2.monthYear,'%m/%d/%Y')) FROM cscan_publication cspu2, cscan_publication_product cspup2 WHERE  cspu2.publicationID=cspup2.publicationID AND cspup2.productID=pd.productID))  AS 'Publication Date'";
        }

        ####### End For Print Media Channel ###############
      
        if ($consumer) { 

                    //$selectQuery .= ",cit.incomeName as Income";
                    $selectQuery .= ",IF((SELECT GROUP_CONCAT(cap.age_pname) FROM cscan_age_product cap WHERE  find_in_set(cap.age_pID,pd.age) ) IS NULL, '',(SELECT GROUP_CONCAT(cap.age_pname) FROM cscan_age_product cap WHERE  find_in_set(cap.age_pID,pd.age)))  AS 'Age'";

                    $selectQuery .= ",IF((SELECT GROUP_CONCAT(cit.incomeName) FROM cscan_incometype cit WHERE  find_in_set(cit.incomeID,pd.incomeID) ) IS NULL, '',(SELECT GROUP_CONCAT(cit.incomeName) FROM cscan_incometype cit WHERE  find_in_set(cit.incomeID,pd.incomeID)))  AS 'Income'";		



                    $mailvol_query= $sql_P2.'pd.productID ';
                    $selectQuery .= ",IF((".$mailvol_query.")>0, (".$mailvol_query."), (SELECT COUNT(*) as pieces FROM cscan_panelists_product pp WHERE pp.productID=pd.productID)) AS 'Mail Pieces'";

                    $mailvol_query2= str_replace('count(pp.ppmv)','round(sum(pp.ppmv))',$sql_P2).'pd.productID ';

                    $selectQuery .= ",IF((".$mailvol_query2.")>0, (".$mailvol_query2."), (SELECT round(SUM(ppmv)) as 'Estimated Mail Volume' FROM cscan_panelists_product pp WHERE pp.productID=pd.productID)) AS 'Estimated Mail Volume'";       


                    $selectQuery .= ", (select doSpend( IF(
            (SELECT round(sum(pp.ppmv))	FROM cscan_panelists_product pp LEFT JOIN cscan_dma_code_postalcode dmap ON (pp.pppostalcode=dmap.pppostalcode), cscan_panelists cp WHERE pp.panelist_id=cp.panelist_id AND productID=pd.productID)
            >0,
            (SELECT round(sum(pp.ppmv))	FROM cscan_panelists_product pp LEFT JOIN cscan_dma_code_postalcode dmap ON (pp.pppostalcode=dmap.pppostalcode), cscan_panelists cp WHERE pp.panelist_id=cp.panelist_id AND productID=pd.productID )
            ,
            (SELECT round(trim(SUM(pp2.ppmv)),2) FROM cscan_panelists_product pp2 WHERE pp2.productID=pd.productID)

            ) ,(SELECT document_size_byte FROM cscan_document as d WHERE d.productID=pd.productID AND document_id=1))) AS 'Estimated Spend'";


            $mailvolmonth_query= $sql_P3.'pd.productID ';

            if ($more > 0) {
               $mailvolmonth_query .= ' AND ppmv>0';
            }
            $mailvolmonth_query .= ' GROUP BY pd.productID ';
            //$selectQuery .= " ,IF((".$mailvolmonth_query.")IS NULL, '',(".$mailvolmonth_query."))";
            $selectQuery .= " ,(".$mailvolmonth_query.") AS 'Mail Piece Months'";

        }  
		
        ##################  For Energy Sector  ######################
        if(in_array('315',$sectrarr) ){		

            $selectQuery .= ",IF((SELECT GROUP_CONCAT(edcn.edc_name) FROM cscan_edc edcn WHERE  find_in_set(edcn.edc_id,(SELECT GROUP_CONCAT(edc_id) FROM cscan_panelists_product ppp JOIN cscan_edc_postalcode edc2 ON(ppp.pppostalcode=edc2.pppostalcode) WHERE productID=pd.productID)) ) IS NULL, '',(SELECT GROUP_CONCAT(edcn.edc_name) FROM cscan_edc edcn WHERE  find_in_set(edcn.edc_id,(SELECT GROUP_CONCAT(edc_id) FROM cscan_panelists_product ppp JOIN cscan_edc_postalcode edc2 ON(ppp.pppostalcode=edc2.pppostalcode) WHERE productID=pd.productID))))  AS 'EDC'";	
        }
         ################## End For Energy Sector  ######################		

        //$selectQuery .= ",pd.compaignLanguage as 'Compaign Language', cmt.mTypeName as 'Mailing Type', IF(pd.affinityAssociation = 1, 'Yes', 'No') AS 'Affinity/Association'";

        $selectQuery .= ",pd.compaignLanguage as 'Compaign Language', cmt.mTypeName as 'Mailing Type'";

        $issuetypjoin="  LEFT JOIN cscan_issue_type csistyp on (csistyp.IssueTypeID=pd.IssueTypeID) ";
        $selectQuery .= ",IF(csistyp.IssueTypeName IS NULL or csistyp.IssueTypeName = '', '', csistyp.IssueTypeName) as 'Issue Type'";      

        $selectQuery .= " , IF(pd.affinityAssociation = 1, 'Yes', 'No') AS 'Affinity/Association'";

        $selectQuery .= ",IF((SELECT GROUP_CONCAT(pa3.affinityName) FROM cscan_affinity pa3,cscan_affinity_product pp3 WHERE pa3.affinityID=pp3.affinityID AND pp3.productID=pd.productID GROUP BY pp3.productID) IS NULL, '',(SELECT GROUP_CONCAT(pa3.affinityName) FROM cscan_affinity pa3,cscan_affinity_product pp3 WHERE pa3.affinityID=pp3.affinityID AND pp3.productID=pd.productID GROUP BY pp3.productID))  AS 'Affinity/Association Name'";

        $selectQuery .= ", IF(csafc.AffinityCategoryName IS NULL or csafc.AffinityCategoryName = '', '', csafc.AffinityCategoryName) as 'Affinity/Association Category', IF(csafsc.AffinityCategoryName IS NULL or csafsc.AffinityCategoryName = '', '', csafsc.AffinityCategoryName) as 'Affinity/Association Sub Category',pd.firstSeen,pd.lastSeen,pd.productName as 'Product Name',(csdoc.document_size_byte/1024) as 'File Size (KB)',IF(cdm.delmethname IS NULL or cdm.delmethname = '', cmc.mChannelName, cdm.delmethname) as 'Delivery Method', IF(pd.incentive IS NULL or pd.incentive = '', 'N/A', pd.incentive) as 'Sign-on Incentive',IF(pd.incentive_ongoing IS NULL or pd.incentive_ongoing = '', 'N/A', pd.incentive_ongoing) as 'Ongoing Incentive',IF((SELECT GROUP_CONCAT(crm.responseMechName) FROM cscan_response_mechanism crm WHERE  find_in_set(crm.responseMechID,TRIM(pd.responseMechID))) IS NULL, '',(SELECT GROUP_CONCAT(crm.responseMechName) FROM cscan_response_mechanism crm WHERE find_in_set(crm.responseMechID,TRIM(pd.responseMechID))))  AS 'Response Mechanism',";
        // $responsemechjoin="  LEFT JOIN cscan_response_mechanism crm on (crm.responseMechID=pd.responseMechID) ";

        if(in_array('4',$sectrarr) || in_array('90',$sectrarr) || in_array('87',$sectrarr) ||  in_array('6',$sectrarr) || in_array('9',$sectrarr) || in_array('219',$sectrarr)){		  

            $selectQuery .= "IF((SELECT GROUP_CONCAT(csancll.ancillary_name) FROM cscan_ancillary_product csancll WHERE  find_in_set(csancll.ancillary_id,TRIM(pd.FeeProductType))) IS NULL, '',(SELECT GROUP_CONCAT(csancll.ancillary_name) FROM cscan_ancillary_product csancll WHERE  find_in_set(csancll.ancillary_id,TRIM(pd.FeeProductType))))  AS 'Ancillary Products',";				
        } 

        ################## For 'Social Media' Media Channel ##################

        if(in_array('6',$mediaarr)){ 	  

            $selectQuery .= "( CASE WHEN
        pd.external_link REGEXP 'facebook' THEN 'Facebook' WHEN pd.external_link REGEXP 'twitter' THEN 'Twitter' ELSE '' END ) AS 'Network Name',";  

            $selectQuery .= "pd.external_updates as 'Number of Updates/Tweets',pd.external_fans as 'Number of Fans/Followers',pd.external_link as 'External Link',";   

        }      

        ################## End For 'Social Media' Media Channel ##################

        ################## For 'Insurance' Sector ##################

        if(in_array('4',$sectrarr)){	

            $selectQuery .= "IF((SELECT GROUP_CONCAT(csfaceamnt.fa_name) FROM cscan_face_amount csfaceamnt WHERE  find_in_set(csfaceamnt.fa_id,TRIM(pd.fa_ids))) IS NULL, '',(SELECT GROUP_CONCAT(csfaceamnt.fa_name) FROM cscan_face_amount csfaceamnt WHERE find_in_set(csfaceamnt.fa_id,TRIM(pd.fa_ids))))  AS 'Face Amount',";

            $selectQuery .= "IF((SELECT GROUP_CONCAT(cstrmlnth.tl_name) FROM cscan_term_length cstrmlnth WHERE  find_in_set(cstrmlnth.tl_id,TRIM(pd.tl_ids))) IS NULL, '',(SELECT GROUP_CONCAT(cstrmlnth.tl_name) FROM cscan_term_length cstrmlnth WHERE find_in_set(cstrmlnth.tl_id,TRIM(pd.tl_ids))))  AS 'Term Length',";

            $selectQuery .= "IF(pd.prescription = 1, 'Yes', '') AS 'Rx',";
            $selectQuery .= "IF(pd.is_hphsa = 1, 'Yes', '') AS 'CDHP/HDHP/HSA',";
        }
        ################## End For 'Insurance' Sector ##################	

        ################## For 'Insurance' & 'Investments/Annuities' Sector ##################

        if(in_array('4',$sectrarr) || in_array('5',$sectrarr)){	

            $selectQuery .= "IF((SELECT GROUP_CONCAT(csriders.ridersName) FROM cscan_riders csriders WHERE  find_in_set(csriders.ridersID,TRIM(pd.riders))) IS NULL, '',(SELECT GROUP_CONCAT(csriders.ridersName) FROM cscan_riders csriders WHERE find_in_set(csriders.ridersID,TRIM(pd.riders))))  AS 'Riders',";

            $selectQuery .= "IF(pd.worksiteVoluntary = 0, 'No', 'Yes') AS 'Worksite/Voluntary',";
        }

        ################## End For 'Insurance' & 'Investments/Annuities' Sector ##################		   


        $selectQuery .= " ( CASE WHEN
        pd.groupSize='0' THEN 'N/A' WHEN pd.groupSize='1' THEN '2-50' WHEN pd.groupSize='2' THEN '51-99' WHEN pd.groupSize='3' THEN '100-499' WHEN pd.groupSize='4' THEN '500 +' ELSE 'N/A' END ) AS 'Group Size',";


        $selectQuery .= "CONCAT('https://www.competiscan.com/productDocuments.php?did=1&id=',pd.productID) as 'PDF Content',";

        $selectQuery .= " IF(is_prescreen = 1, 'Yes', 'No') AS 'Pre-Screen',pd.OfferExpiryDate as 'Offer Expiry Date'";      

        $selectQuery .= " ,IF((SELECT GROUP_CONCAT(pa5.affinityName) FROM cscan_affinity pa5,cscan_panelist_affinity pp5 WHERE pp5.panelist_id=cspsp_dt.panelist_id  AND pa5.affinityID=pp5.affinityID GROUP BY pp5.panelist_id order by pa5.affinityName) IS NULL or (SELECT GROUP_CONCAT(pa5.affinityName) FROM cscan_affinity pa5,cscan_panelist_affinity pp5 WHERE pp5.panelist_id=cspsp_dt.panelist_id  AND pa5.affinityID=pp5.affinityID GROUP BY pp5.panelist_id order by pa5.affinityName)= '', '',(SELECT GROUP_CONCAT(pa5.affinityName) FROM cscan_affinity pa5,cscan_panelist_affinity pp5 WHERE pp5.panelist_id=cspsp_dt.panelist_id  AND pa5.affinityID=pp5.affinityID GROUP BY pp5.panelist_id order by pa5.affinityName)) AS 'Panelist Affinities'";

        $selectQuery .= " ,IF((SELECT GROUP_CONCAT(pa6.companyName) FROM cscan_company pa6,cscan_panelist_company pp6 WHERE pp6.panelist_id=cspsp_dt.panelist_id  AND pa6.companyID=pp6.companyID GROUP BY pp6.panelist_id order by pa6.companyName) IS NULL or (SELECT GROUP_CONCAT(pa6.companyName) FROM cscan_company pa6,cscan_panelist_company pp6 WHERE pp6.panelist_id=cspsp_dt.panelist_id  AND pa6.companyID=pp6.companyID GROUP BY pp6.panelist_id order by pa6.companyName)= '', '',(SELECT GROUP_CONCAT(pa6.companyName) FROM cscan_company pa6,cscan_panelist_company pp6 WHERE pp6.panelist_id=cspsp_dt.panelist_id  AND pa6.companyID=pp6.companyID GROUP BY pp6.panelist_id order by pa6.companyName)) AS 'Panelist Loyalty/Retention, Statement Companies'";

        $selectQuery .= ",IF((SELECT count(pd.productID) FROM cscan_company pa2,cscan_company_product pp2 WHERE pa2.companyID=pp2.companyID AND pp2.productID=pd.productID AND isCreditUnion=1 limit 1)>= 1, 'Yes', 'No') AS 'Credit Union'";

        ################## For 'Online Display Advertising' & 'Mobile'  ##################

        if(in_array('5',$mediaarr) || in_array('7',$mediaarr)){
            //  $selectQuery .= ",IF(pd.traffic_sources IS NULL or pd.traffic_sources = '', '', pd.traffic_sources) as 'Observed Traffic Sources'";
        }	
        ################## End For 'Mobile' & 'Social Media'  ##################  

        $bankingJoin=$cardtypeJoin=$rewardtypeJoin=$paymentcardsJoin=$apptypeJoin=$cardtypeJoin2='';   
        $cardlevelJoin=$cardlevelJoin2=$rewardJoin=$ratetypJoin=$ratetypJoin2=$ratetypJoin3=$creditaccesJoin='';
        $ratetypJoin4=$ratetypJoin5=$ratetypJoin6=$energyJoin=$erateJoin =$etermlengthJoin=$erateJoin2=$etermlengthJoin2='';	
        $erateJoin3=$etermlengthJoin3=$erateJoin4=$etermlengthJoin4=$mortgageJoin=$appmortJoin=$ratetypJoin7=$retailconsJoin=$telecomJoin=$travelJoin=$cardtypeJoin3='';

        ############### For Banking & Credit Card Sector #####################  

        if(in_array('87',$sectrarr) || in_array('90',$sectrarr)){

            $selectQuery .=",IF(pd.incentive_type IS NULL or pd.incentive_type = '', 'N/A', pd.incentive_type) as 'Sign-on Incentive Type #1',IF(pd.incentive_value IS NULL or pd.incentive_value = '', 'N/A', pd.incentive_value) as 'Sign-on Incentive Value #1',IF(pd.accelerator_per IS NULL or pd.accelerator_per = '', 'N/A', pd.accelerator_per) as 'Sign-on Accelerator Per #1',IF(pd.accelerator_type IS NULL or pd.accelerator_type = '', 'N/A', pd.accelerator_type) as 'Sign-on Accelerator Type #1',IF(pd.max_award IS NULL or pd.max_award = '', 'N/A', pd.max_award) as 'Sign-on Max award #1',IF(pd.max_spend IS NULL or pd.max_spend = '', 'N/A', pd.max_spend) as 'Sign-on Incentive Maximum Spend #1',IF(pd.min_spend IS NULL or pd.min_spend = '', 'N/A', pd.min_spend) as 'Sign-on Incentive Minimum Spend #1',IF(pd.window IS NULL or pd.window = '', 'N/A', pd.window) as 'Sign-on Incentive Window (months) #1',IF(pd.category_limited IS NULL or pd.category_limited = '', 'No', 'Yes') as 'Sign-on Limited to Specific Category #1',IF(pd.window_fixed_date IS NULL or pd.window_fixed_date = '0', 'No', 'Yes') as 'Sign-on Fixed date #1',IF(pd.incentive_signon_2 IS NULL or pd.incentive_signon_2 = '', 'N/A', pd.incentive_signon_2) as 'Sign-on Incentive #2',IF(pd.incentive_type_2 IS NULL or pd.incentive_type_2 = '', 'N/A', pd.incentive_type_2) as 'Sign-on Incentive Type #2',IF(pd.incentive_value_2 IS NULL or pd.incentive_value_2 = '', 'N/A', pd.incentive_value_2) as 'Sign-on Incentive Value #2',IF(pd.accelerator_per_2 IS NULL or pd.accelerator_per_2 = '', 'N/A', pd.accelerator_per_2) as 'Sign-on Accelerator Per #2',IF(pd.accelerator_type_2 IS NULL or pd.accelerator_type_2 = '', 'N/A', pd.accelerator_type_2) as 'Sign-on Accelerator Type #2',IF(pd.max_award_2 IS NULL or pd.max_award_2 = '', 'N/A', pd.max_award_2) as 'Sign-on Max award #2',IF(pd.max_spend_2 IS NULL or pd.max_spend_2 = '', 'N/A', pd.max_spend_2) as 'Sign-on Incentive Maximum Spend #2',IF(pd.min_spend_2 IS NULL or pd.min_spend_2 = '', 'N/A', pd.min_spend_2) as 'Sign-on Incentive Minimum Spend #2',IF(pd.window_2 IS NULL or pd.window_2 = '', 'N/A', pd.window_2) as 'Sign-on Incentive Window (months) #2',IF(pd.category_limited_2 IS NULL or pd.category_limited_2 = '0', 'No', 'Yes') as 'Sign-on Limited to Specific Category #2',IF(pd.window_fixed_date_2 IS NULL or pd.window_fixed_date_2 = '', 'No', 'Yes') as 'Sign-on Fixed date #2',IF(pd.incentive_signon_3 IS NULL or pd.incentive_signon_3 = '', 'N/A', pd.incentive_signon_3) as 'Sign-on Incentive #3',IF(pd.incentive_type_3 IS NULL or pd.incentive_type_3 = '', 'N/A', pd.incentive_type_3) as 'Sign-on Incentive Type #3',IF(pd.incentive_value_3 IS NULL or pd.incentive_value_3 = '', 'N/A', pd.incentive_value_3) as 'Sign-on Incentive Value #3',IF(pd.accelerator_per_3 IS NULL or pd.accelerator_per_3 = '', 'N/A', pd.accelerator_per_3) as 'Sign-on Accelerator Per #3',IF(pd.accelerator_type_3 IS NULL or pd.accelerator_type_3 = '', 'N/A', pd.accelerator_type_3) as 'Sign-on Accelerator Type #3',IF(pd.max_award_3 IS NULL or pd.max_award_3 = '', 'N/A', pd.max_award_3) as 'Sign-on Max award #3',IF(pd.max_spend_3 IS NULL or pd.max_spend_3 = '', 'N/A', pd.max_spend_3) as 'Sign-on Incentive Maximum Spend #3',IF(pd.min_spend_3 IS NULL or pd.min_spend_3 = '', 'N/A', pd.min_spend_3) as 'Sign-on Incentive Minimum Spend #3',IF(pd.window_3 IS NULL or pd.window_3 = '', 'N/A', pd.window_3) as 'Sign-on Incentive Window (months) #3',IF(pd.category_limited_3 IS NULL or pd.category_limited_3 = '0', 'No', 'Yes') as 'Sign-on Limited to Specific Category #3',IF(pd.window_fixed_date_3 IS NULL or pd.window_fixed_date_3 = '0', 'No', 'Yes') as 'Sign-on Fixed date #3'";

            ################ For Banking Sector ###################

            if(in_array('87',$sectrarr) ){    
                $bankingJoin="  LEFT JOIN cscan_banking csbank on (csbank.productID=pd.productID) ";
                $cardtypeJoin=" LEFT JOIN cscan_card_type cscrdtyp on(cscrdtyp.CardTypeID=csbank.BankingCardType) ";
                $rewardtypeJoin=" LEFT JOIN cscan_reward_type cscrwrdtyp on(cscrwrdtyp.RewardTypeID=csbank.BankingRewardsProgramEmphasis) ";

                $selectQuery .=",IF(csbank.MinimumDeposit IS NULL or csbank.MinimumDeposit = '', '', csbank.MinimumDeposit) as 'Banking - Minimum Deposit ($)',IF(csbank.FreeChecking IS NULL or csbank.FreeChecking = '0', '', 'Yes') as 'Banking - Free Checking',IF(csbank.Checking_APR IS NULL or csbank.Checking_APR = '0', '', (round((csbank.Checking_APR * 100),2))) as 'Banking - Checking APR (%)',IF(csbank.Checking_APY IS NULL or csbank.Checking_APY = '0', '', (round((csbank.Checking_APY * 100),2))) as 'Banking - Checking APY (%)',IF(csbank.Savings_APR IS NULL or csbank.Savings_APR = '0', '', (round((csbank.Savings_APR * 100),2))) as 'Banking - Savings APR (%)',IF(csbank.Savings_APY IS NULL or csbank.Savings_APY = '0', '', (round((csbank.Savings_APY * 100),2))) as 'Banking - Savings APY (%)',IF(csbank.MoneyMarket_APR IS NULL or csbank.MoneyMarket_APR = '0', '', (round((csbank.MoneyMarket_APR * 100),2))) as 'Banking - Money Market APR (%)',IF(csbank.MoneyMarket_APY IS NULL or csbank.MoneyMarket_APY = '0', '', (round((csbank.MoneyMarket_APY * 100),2))) as 'Banking - Money Market APY (%)',IF(csbank.CD_APR IS NULL or csbank.CD_APR = '0', '', (round((csbank.CD_APR * 100),2))) as 'Banking - C/D APR (%)',IF(csbank.CD_APY IS NULL or csbank.CD_APY = '0', '', (round((csbank.CD_APY * 100),2))) as 'Banking - C/D APY (%)',IF(csbank.DebitCardMentioned IS NULL or csbank.DebitCardMentioned = '0', 'No', 'Yes') as 'Banking - Debit Card Mentioned',IF(csbank.DebitCardMentioned IS NULL or csbank.DebitCardMentioned = '0', 'No', 'Yes') as 'Banking - Card Type'";

                $selectQuery .=",cscrdtyp.CardTypeName as 'Banking - Card Type',IF(csbank.BankingRewardsProgram IS NULL or csbank.BankingRewardsProgram = '0', 'No', 'Yes') as 'Banking - Rewards Program',IF(cscrwrdtyp.RewardTypeName IS NULL or cscrwrdtyp.RewardTypeName = '0', '', cscrwrdtyp.RewardTypeName) as 'Banking - Rewards Program Emphasis'";

            }

            ############### End Banking Sector #####################	

            ############### For Credit Card Sector ################# 

            if(in_array('90',$sectrarr) ){

                $paymentcardsJoin	=" LEFT JOIN cscan_payment_cards cspayment on (cspayment.productID=pd.productID) ";
                $apptypeJoin		=" LEFT JOIN cscan_application_type csapptype on (csapptype.ApplicationTypeID=cspayment.ApplicationType) ";
                $cardtypeJoin2	=" LEFT JOIN cscan_card_type cscrdtyp2 on(cscrdtyp2.CardTypeID=cspayment.CardType) ";
                $cardlevelJoin	=" LEFT JOIN cscan_cardlevel_type cscrdlvl on(cscrdlvl.CardLevelTypeID=cspayment.CardLevel) ";
                $cardlevelJoin2	=" LEFT JOIN cscan_cardlevel_type cscrdlvl2 on(cscrdlvl2.CardLevelTypeID=cspayment.SecondaryCardLevel) ";

                $rewardJoin	=" LEFT JOIN cscan_reward_type csrwrd on(csrwrd.RewardTypeID=cspayment.RewardsProgramEmphasis) ";
                $ratetypJoin	=" LEFT JOIN cscan_rate_type csrtyp on(csrtyp.RateTypeID=cspayment.PurchaseRateType) ";
                $ratetypJoin2	=" LEFT JOIN cscan_rate_type csrtyp2 on(csrtyp2.RateTypeID=cspayment.BalanceTransferRateType) ";
                $ratetypJoin3	=" LEFT JOIN cscan_rate_type csrtyp3 on(csrtyp3.RateTypeID=cspayment.CashAdvanceRateType) ";


                $selectQuery .=",IF(csapptype.ApplicationTypeName IS NULL or csapptype.ApplicationTypeName = '', '', csapptype.ApplicationTypeName) as 'Payment Cards - Application Type',IF(cscrdtyp2.CardTypeName IS NULL or cscrdtyp2.CardTypeName = '', '', cscrdtyp2.CardTypeName) as 'Payment Cards - Card Network' ,IF(cscrdlvl.CardLevelTypeName IS NULL or cscrdlvl.CardLevelTypeName = '', '', cscrdlvl.CardLevelTypeName) as 'Payment Cards - Primary Card Level',IF(cscrdlvl2.CardLevelTypeName IS NULL or cscrdlvl2.CardLevelTypeName = '', '', cscrdlvl2.CardLevelTypeName) as 'Payment Cards - Secondary Card Level(s)',IF(cspayment.RewardsProgram IS NULL or cspayment.RewardsProgram = '0', 'No', 'Yes') as 'Payment Cards - Rewards Program',IF(csrwrd.RewardTypeName IS NULL or csrwrd.RewardTypeName = '', '', csrwrd.RewardTypeName) as 'Payment Cards - Rewards Program Emphasis',cspayment.RewardsRate as 'Payment Cards - Rewards Rate',IF(cspayment.Reloadable IS NULL or cspayment.Reloadable = '0', '', 'Yes') as 'Payment Cards - Reloadable',IF(cspayment.PurchaseRegularAPR IS NULL or cspayment.PurchaseRegularAPR = '', '', (round((cspayment.PurchaseRegularAPR * 100),2))) as 'Payment Cards - Tier 1 Purchase Regular APR (%)',IF(cspayment.Tier2PurchaseRegularAPR IS NULL or cspayment.Tier2PurchaseRegularAPR = '', '', (round((cspayment.Tier2PurchaseRegularAPR * 100),2))) as 'Payment Cards - Tier 2 Purchase Regular APR (%)',IF(cspayment.Tier3PurchaseRegularAPR IS NULL or cspayment.Tier3PurchaseRegularAPR = '', '', (round((cspayment.Tier3PurchaseRegularAPR * 100),2))) as 'Payment Cards - Tier 3 Purchase Regular APR (%)',IF(cspayment.PurchaseRegularAPRDetail IS NULL or cspayment.PurchaseRegularAPRDetail = '', '', cspayment.PurchaseRegularAPRDetail) as 'Payment Cards - Purchase Regular APR (%) Detail',IF(csrtyp.RateTypeName IS NULL or csrtyp.RateTypeName = '', '', csrtyp.RateTypeName) as 'Payment Cards - Purchase Regular Rate Type',IF(cspayment.BalanceTransferRegularAPR IS NULL or cspayment.BalanceTransferRegularAPR = '', '', (round((cspayment.BalanceTransferRegularAPR * 100),2))) as 'Payment Cards - Tier 1 Balance Transfer Regular APR (%)',IF(cspayment.Tier2BalanceTransferRegularAPR IS NULL or cspayment.Tier2BalanceTransferRegularAPR = '', '', (round((cspayment.Tier2BalanceTransferRegularAPR *100),2))) as 'Payment Cards - Tier 2 Balance Transfer Regular APR (%)',IF(cspayment.Tier3BalanceTransferRegularAPR IS NULL or cspayment.Tier3BalanceTransferRegularAPR = '', '', (round((cspayment.Tier3BalanceTransferRegularAPR * 100),2))) as 'Payment Cards - Tier 3 Balance Transfer Regular APR (%)',IF(cspayment.BalanceTransferRegularAPRDetail IS NULL or cspayment.BalanceTransferRegularAPRDetail = '', '', cspayment.BalanceTransferRegularAPRDetail) as 'Payment Cards - Balance Transfer Regular APR (%) Detail',IF(csrtyp2.RateTypeName IS NULL or csrtyp2.RateTypeName = '', '', csrtyp2.RateTypeName) as 'Payment Cards - Balance Transfer Regular Rate Type',IF(cspayment.CashAdvanceRegularAPR IS NULL or cspayment.CashAdvanceRegularAPR = '', '', (round((cspayment.CashAdvanceRegularAPR *100),2))) as 'Payment Cards - Tier 1 Cash Advance Regular APR (%)',IF(cspayment.Tier2CashAdvanceRegularAPR IS NULL or cspayment.Tier2CashAdvanceRegularAPR = '', '', (round((cspayment.Tier2CashAdvanceRegularAPR * 100),2))) as 'Payment Cards - Tier 2 Cash Advance Regular APR (%)',IF(cspayment.Tier3CashAdvanceRegularAPR IS NULL or cspayment.Tier3CashAdvanceRegularAPR = '', '', (round((cspayment.Tier3CashAdvanceRegularAPR * 100),2))) as 'Payment Cards - Tier 3 Cash Advance Regular APR (%)',IF(cspayment.CashAdvanceRegularAPRDetail IS NULL or cspayment.CashAdvanceRegularAPRDetail = '', '', cspayment.CashAdvanceRegularAPRDetail) as 'Payment Cards - Cash Advance Regular APR (%) Detail',IF(csrtyp3.RateTypeName IS NULL or csrtyp3.RateTypeName = '', '', csrtyp3.RateTypeName) as 'Payment Cards - Cash Advance Regular Rate Type',IF(cspayment.Tier1AnnualFee IS NULL or cspayment.Tier1AnnualFee = '', '', cspayment.Tier1AnnualFee) as 'Payment Cards - Tier 1 Annual Fee ($)',IF(cspayment.Tier2AnnualFee IS NULL or cspayment.Tier2AnnualFee = '', '', cspayment.Tier2AnnualFee) as 'Payment Cards - Tier 2 Annual Fee ($)',IF(cspayment.Tier2AnnualFee IS NULL or cspayment.Tier2AnnualFee = '', '', cspayment.Tier2AnnualFee) as 'Payment Cards - Tier 2 Annual Fee ($)',IF(cspayment.AnnualFee IS NULL or cspayment.AnnualFee = '', '', cspayment.AnnualFee) as 'Payment Cards - Tier 3 Annual Fee ($)',IF(cspayment.AnnualFeeDetail IS NULL or cspayment.AnnualFeeDetail = '', 'No annual fee', cspayment.AnnualFeeDetail) as 'Payment Cards - Annual Fee ($) Detail',IF(cspayment.Tier1LateFee IS NULL or cspayment.Tier1LateFee = '', '', cspayment.Tier1LateFee) as 'Payment Cards - Tier 1 Late Fee ($)',IF(cspayment.Tier2LateFee IS NULL or cspayment.Tier2LateFee = '', '', cspayment.Tier2LateFee) as 'Payment Cards - Tier 2 Late Fee ($)',IF(cspayment.LateFee IS NULL or cspayment.LateFee = '', '', cspayment.LateFee) as 'Payment Cards - Tier 3 Late Fee ($)',IF(cspayment.LateFeeDetail IS NULL or cspayment.LateFeeDetail = '', '', cspayment.LateFeeDetail) as 'Payment Cards - Late Fee ($) Detail',IF(cspayment.Tier1OverlimitFee IS NULL or cspayment.Tier1OverlimitFee = '', '', cspayment.Tier1OverlimitFee) as 'Payment Cards - Tier 1 Overlimit Fee ($)',IF(cspayment.Tier2OverlimitFee IS NULL or cspayment.Tier2OverlimitFee = '', '', cspayment.Tier2OverlimitFee) as 'Payment Cards - Tier 2 Overlimit Fee ($)',IF(cspayment.OverlimitFee IS NULL or cspayment.OverlimitFee = '', '', cspayment.OverlimitFee) as 'Payment Cards - Tier 3 Overlimit Fee ($)',IF(cspayment.OverlimitFeeDetail IS NULL or cspayment.OverlimitFeeDetail = '', '', cspayment.OverlimitFeeDetail) as 'Payment Cards - Overlimit Fee ($) Detail',IF(cspayment.BalanceTransferUsageFee IS NULL or cspayment.BalanceTransferUsageFee = '', '', (round((cspayment.BalanceTransferUsageFee * 100),2))) as 'Payment Cards - Balance Transfer Usage Fee (%)',IF(cspayment.BalanceTransferMinimumFee IS NULL or cspayment.BalanceTransferMinimumFee = '', '', cspayment.BalanceTransferMinimumFee) as 'Payment Cards - Balance Transfer Minimum Fee ($)',IF(cspayment.BalanceTransferMaximumFee IS NULL or cspayment.BalanceTransferMaximumFee = '', '', cspayment.BalanceTransferMaximumFee) as 'Payment Cards - Balance Transfer Maximum Fee ($)',IF(cspayment.CashAdvanceUsageFee IS NULL or cspayment.CashAdvanceUsageFee = '', '', (round((cspayment.CashAdvanceUsageFee * 100),2))) as 'Payment Cards - Cash Advance Usage Fee (%)',IF(cspayment.CashAdvanceMinimumFee IS NULL or cspayment.CashAdvanceMinimumFee = '', '', cspayment.CashAdvanceMinimumFee) as 'Payment Cards - Cash Advance Minimum Fee ($)',IF(cspayment.CashAdvanceMaximumFee IS NULL or cspayment.CashAdvanceMaximumFee = '', '', cspayment.CashAdvanceMaximumFee) as 'Payment Cards - Cash Advance Maximum Fee ($)',IF(cspayment.MinimumCardLimit IS NULL or cspayment.MinimumCardLimit = '', '', cspayment.MinimumCardLimit) as 'Payment Cards - Minimum Card Limit ($)',IF(cspayment.MaximumCardLimit IS NULL or cspayment.MaximumCardLimit = '', '', cspayment.MaximumCardLimit) as 'Payment Cards - Maximum Card Limit ($)',IF(cspayment.PurchaseIntroductoryAPR IS NULL or cspayment.PurchaseIntroductoryAPR = '', '', (round((cspayment.PurchaseIntroductoryAPR * 100),2))) as 'Payment Cards - Purchase Introductory APR (%)',IF(cspayment.PurchaseIntroductoryPeriod IS NULL or cspayment.PurchaseIntroductoryPeriod = '', '', cspayment.PurchaseIntroductoryPeriod) as 'Payment Cards - Purchase Introductory Period (Months)',IF(cspayment.BalanceTransferIntroductoryAPR IS NULL or cspayment.BalanceTransferIntroductoryAPR = '', '', (round((cspayment.BalanceTransferIntroductoryAPR * 100),2))) as 'Payment Cards - Balance Transfer Introductory APR (%)',IF(cspayment.BalanceTransferIntroductoryPeriod IS NULL or cspayment.BalanceTransferIntroductoryPeriod = '', '', cspayment.BalanceTransferIntroductoryPeriod) as 'Payment Cards - Balance Transfer Introductory Period (Months)',IF(cspayment.BalanceTransferIntroductoryUsageFee IS NULL or cspayment.BalanceTransferIntroductoryUsageFee = '', '', (round((cspayment.BalanceTransferIntroductoryUsageFee * 100),2))) as 'Payment Cards - Balance Transfer Introductory Usage Fee (%)',IF(cspayment.BalanceTransferIntroductoryMinimumFee IS NULL or cspayment.BalanceTransferIntroductoryMinimumFee = '', '', cspayment.BalanceTransferIntroductoryMinimumFee) as 'Payment Cards - Balance Transfer Introductory Minimum Fee ($)',IF(cspayment.BalanceTransferIntroductoryMaximumFee IS NULL or cspayment.BalanceTransferIntroductoryMaximumFee = '', '', cspayment.BalanceTransferIntroductoryMaximumFee) as 'Payment Cards - Balance Transfer Introductory Maximum Fee ($)',IF(cspayment.BalanceTransferIntroductoryFeePeriod IS NULL or cspayment.BalanceTransferIntroductoryFeePeriod = '', '', cspayment.BalanceTransferIntroductoryFeePeriod) as 'Payment Cards - Balance Transfer Introductory Fee Period (Months)',IF(cspayment.BalanceTransferIntroductoryFeeDetail IS NULL or cspayment.BalanceTransferIntroductoryFeeDetail = '', '', cspayment.BalanceTransferIntroductoryFeeDetail) as 'Payment Cards - Balance Transfer Introductory Fee Detail',IF(cspayment.CashAdvanceIntroductoryAPR IS NULL or cspayment.CashAdvanceIntroductoryAPR = '', '', (round((cspayment.CashAdvanceIntroductoryAPR * 100),2))) as 'Payment Cards - Cash Advance Introductory APR (%)',IF(cspayment.CashAdvanceIntroductoryPeriod IS NULL or cspayment.CashAdvanceIntroductoryPeriod = '', '', cspayment.CashAdvanceIntroductoryPeriod) as 'Payment Cards - Cash Advance Introductory Period (Months)',IF(cspayment.CashAdvanceIntroductoryUsageFee IS NULL or cspayment.CashAdvanceIntroductoryUsageFee = '', '', (round((cspayment.CashAdvanceIntroductoryUsageFee * 100),2))) as 'Payment Cards - Cash Advance Introductory Usage Fee (%)',IF(cspayment.CashAdvanceIntroductoryMinimumFee IS NULL or cspayment.CashAdvanceIntroductoryMinimumFee = '', '', cspayment.CashAdvanceIntroductoryMinimumFee) as 'Payment Cards - Cash Advance Introductory Minimum Fee ($)',IF(cspayment.CashAdvanceIntroductoryMaximumFee IS NULL or cspayment.CashAdvanceIntroductoryMaximumFee = '', '', cspayment.CashAdvanceIntroductoryMaximumFee) as 'Payment Cards - Cash Advance Introductory Maximum Fee ($)',IF(cspayment.CashAdvanceIntroductoryFeePeriod IS NULL or cspayment.CashAdvanceIntroductoryFeePeriod = '', '', cspayment.CashAdvanceIntroductoryFeePeriod) as 'Payment Cards - Cash Advance Introductory Fee Period (Months)',IF(cspayment.CashAdvanceIntroductoryFeeDetail IS NULL or cspayment.CashAdvanceIntroductoryFeeDetail = '', '', cspayment.CashAdvanceIntroductoryFeeDetail) as 'Payment Cards - Cash Advance Introductory Fee Detail' ";

                $creditaccesJoin	=" LEFT JOIN cscan_credit_access_checks cscrdtaccs on(cscrdtaccs.productID=pd.productID) ";
                $ratetypJoin4	=" LEFT JOIN cscan_rate_type csrtyp4 on(csrtyp4.RateTypeID=cscrdtaccs.BalanceTransferRateType_CAC) ";
                $ratetypJoin5	=" LEFT JOIN cscan_rate_type csrtyp5 on(csrtyp5.RateTypeID=cscrdtaccs.CashAdvanceRateType_CAC) ";
                $ratetypJoin6	=" LEFT JOIN cscan_rate_type csrtyp6 on(csrtyp6.RateTypeID=cscrdtaccs.PurchaseRateType_CAC) ";

               $selectQuery .=",IF(cscrdtaccs.PromotionalOffer IS NULL or cscrdtaccs.PromotionalOffer = '0', '', 'Yes') as 'Credit Access Checks - Promotional Offer',IF(cscrdtaccs.PromotionalOfferAPR IS NULL or cscrdtaccs.PromotionalOfferAPR = '', '', (round((cscrdtaccs.PromotionalOfferAPR * 100),2))) as 'Credit Access Checks - Tier 1 Balance Transfer Introductory APR (%)',IF(cscrdtaccs.BalanceTransferIntroductoryPeriod_CAC1 IS NULL or cscrdtaccs.BalanceTransferIntroductoryPeriod_CAC1 = '', '', cscrdtaccs.BalanceTransferIntroductoryPeriod_CAC1) as 'Credit Access Checks - Tier 1 Balance Transfer Introductory Period (Months)',IF(cscrdtaccs.BalanceTransferIntroductoryAPR_CAC2 IS NULL or cscrdtaccs.BalanceTransferIntroductoryAPR_CAC2 = '', '', (round((cscrdtaccs.BalanceTransferIntroductoryAPR_CAC2 * 100),2))) as 'Credit Access Checks - Tier 2 Balance Transfer Introductory APR (%)',IF(cscrdtaccs.BalanceTransferIntroductoryPeriod_CAC2 IS NULL or cscrdtaccs.BalanceTransferIntroductoryPeriod_CAC2 = '', '', cscrdtaccs.BalanceTransferIntroductoryPeriod_CAC2) as 'Credit Access Checks - Tier 2 Balance Transfer Introductory Period (Months)',IF(cscrdtaccs.PromotionalOfferUsageFee IS NULL or cscrdtaccs.PromotionalOfferUsageFee = '', '', (round((cscrdtaccs.PromotionalOfferUsageFee * 100),2))) as 'Credit Access Checks - Balance Transfer Introductory Usage Fee (%)',IF(cscrdtaccs.PromotionalOfferMinimumFee IS NULL or cscrdtaccs.PromotionalOfferMinimumFee = '', '', cscrdtaccs.PromotionalOfferMinimumFee) as 'Credit Access Checks - Balance Transfer Introductory Minimum Fee ($)',IF(cscrdtaccs.PromotionalOfferMaximumFee IS NULL or cscrdtaccs.PromotionalOfferMaximumFee = '', '', cscrdtaccs.PromotionalOfferMaximumFee) as 'Credit Access Checks - Balance Transfer Introductory Maximum Fee ($)',IF(cscrdtaccs.BalanceTransferIntroductoryFeePeriod_CAC IS NULL or cscrdtaccs.BalanceTransferIntroductoryFeePeriod_CAC = '', '', cscrdtaccs.BalanceTransferIntroductoryFeePeriod_CAC) as 'Credit Access Checks - Balance Transfer Introductory Fee Period (Months)',IF(cscrdtaccs.CashAdvanceIntroductoryAPR_CAC IS NULL or cscrdtaccs.CashAdvanceIntroductoryAPR_CAC = '', '', (round((cscrdtaccs.CashAdvanceIntroductoryAPR_CAC * 100),2))) as 'Credit Access Checks - Tier 1 Cash Advance Introductory APR (%)',IF(cscrdtaccs.CashAdvanceIntroductoryPeriod_CAC IS NULL or cscrdtaccs.CashAdvanceIntroductoryPeriod_CAC = '', '', cscrdtaccs.CashAdvanceIntroductoryPeriod_CAC) as 'Credit Access Checks - Tier 1 Cash Advance Introductory Period (Months)',IF(cscrdtaccs.Tier2CashAdvanceIntroductoryAPR_CAC IS NULL or cscrdtaccs.Tier2CashAdvanceIntroductoryAPR_CAC = '', '', (round((cscrdtaccs.Tier2CashAdvanceIntroductoryAPR_CAC * 100),2))) as 'Credit Access Checks - Tier 2 Cash Advance Introductory APR (%)',IF(cscrdtaccs.Tier2CashAdvanceIntroductoryAPRPeriod_CAC IS NULL or cscrdtaccs.Tier2CashAdvanceIntroductoryAPRPeriod_CAC = '', '', cscrdtaccs.Tier2CashAdvanceIntroductoryAPRPeriod_CAC) as 'Credit Access Checks - Tier 2 Cash Advance Introductory APR Period (Months)',IF(cscrdtaccs.CashAdvanceIntroductoryUsageFee_CAC IS NULL or cscrdtaccs.CashAdvanceIntroductoryUsageFee_CAC = '', '', (round((cscrdtaccs.CashAdvanceIntroductoryUsageFee_CAC * 100),2))) as 'Credit Access Checks - Cash Advance Introductory Usage Fee (%)',IF(cscrdtaccs.CashAdvanceIntroductoryMinimumFee_CAC IS NULL or cscrdtaccs.CashAdvanceIntroductoryMinimumFee_CAC = '', '', cscrdtaccs.CashAdvanceIntroductoryMinimumFee_CAC) as 'Credit Access Checks - Cash Advance Introductory Minimum Fee ($)',IF(cscrdtaccs.CashAdvanceIntroductoryMaximumFee_CAC IS NULL or cscrdtaccs.CashAdvanceIntroductoryMaximumFee_CAC = '', '', cscrdtaccs.CashAdvanceIntroductoryMaximumFee_CAC) as 'Credit Access Checks - Cash Advance Introductory Maximum Fee ($)',IF(cscrdtaccs.CashAdvanceIntroductoryFeePeriod_CAC IS NULL or cscrdtaccs.CashAdvanceIntroductoryFeePeriod_CAC = '', '', cscrdtaccs.CashAdvanceIntroductoryFeePeriod_CAC) as 'Credit Access Checks - Cash Advance Introductory Fee Period (Months)',IF(cscrdtaccs.PurchaseIntroductoryAPR_CAC IS NULL or cscrdtaccs.PurchaseIntroductoryAPR_CAC = '', '', (round((cscrdtaccs.PurchaseIntroductoryAPR_CAC * 100),2))) as 'Credit Access Checks - Tier 1 Purchase Introductory APR (%)',IF(cscrdtaccs.PurchaseIntroductoryPeriod_CAC IS NULL or cscrdtaccs.PurchaseIntroductoryPeriod_CAC = '', '', cscrdtaccs.PurchaseIntroductoryPeriod_CAC) as 'Credit Access Checks - Tier 1 Purchase Introductory Period (Months)',IF(cscrdtaccs.Tier2PurchaseIntroductoryAPR_CAC IS NULL or cscrdtaccs.Tier2PurchaseIntroductoryAPR_CAC = '', '', (round((cscrdtaccs.Tier2PurchaseIntroductoryAPR_CAC * 100),2))) as 'Credit Access Checks - Tier 2 Purchase Introductory APR (%)',IF(cscrdtaccs.Tier2PurchaseIntroductoryPeriod_CAC IS NULL or cscrdtaccs.Tier2PurchaseIntroductoryPeriod_CAC = '', '', cscrdtaccs.Tier2PurchaseIntroductoryPeriod_CAC) as 'Credit Access Checks - Tier 2 Purchase Introductory Period (Months)',IF(cscrdtaccs.PurchaseIntroductoryUsageFee_CAC IS NULL or cscrdtaccs.PurchaseIntroductoryUsageFee_CAC = '', '', (round((cscrdtaccs.PurchaseIntroductoryUsageFee_CAC * 100),2))) as 'Credit Access Checks - Purchase Introductory Usage Fee (%)',IF(cscrdtaccs.PurchaseIntroductoryMinimumFee_CAC IS NULL or cscrdtaccs.PurchaseIntroductoryMinimumFee_CAC = '', '', cscrdtaccs.PurchaseIntroductoryMinimumFee_CAC) as 'Credit Access Checks - Purchase Introductory Minimum Fee ($)',IF(cscrdtaccs.PurchaseIntroductoryMaximumFee_CAC IS NULL or cscrdtaccs.PurchaseIntroductoryMaximumFee_CAC = '', '', cscrdtaccs.PurchaseIntroductoryMaximumFee_CAC) as 'Credit Access Checks - Purchase Introductory Maximum Fee ($)',IF(cscrdtaccs.PurchaseIntroductoryFeePeriod_CAC IS NULL or cscrdtaccs.PurchaseIntroductoryFeePeriod_CAC = '', '', cscrdtaccs.PurchaseIntroductoryFeePeriod_CAC) as 'Credit Access Checks - Purchase Introductory Fee Period (Months)',IF(cscrdtaccs.BalanceTransferRegularAPR_CAC IS NULL or cscrdtaccs.BalanceTransferRegularAPR_CAC = '', '', (round((cscrdtaccs.BalanceTransferRegularAPR_CAC * 100),2))) as 'Credit Access Checks - Balance Transfer Regular APR (%)',IF(csrtyp4.RateTypeName IS NULL or csrtyp4.RateTypeName = '', '', csrtyp4.RateTypeName) as 'Credit Access Checks - Balance Transfer Regular Rate Type',IF(cscrdtaccs.BalanceTransferUsageFee_CAC IS NULL or cscrdtaccs.BalanceTransferUsageFee_CAC = '', '', (round((cscrdtaccs.BalanceTransferUsageFee_CAC * 100),2))) as 'Credit Access Checks - Balance Transfer Usage Fee (%)',IF(cscrdtaccs.BalanceTransferMinimumFee_CAC IS NULL or cscrdtaccs.BalanceTransferMinimumFee_CAC = '', '', cscrdtaccs.BalanceTransferMinimumFee_CAC) as 'Credit Access Checks - Balance Transfer Minimum Fee ($)',IF(cscrdtaccs.BalanceTransferMaximumFee_CAC IS NULL or cscrdtaccs.BalanceTransferMaximumFee_CAC = '', '', cscrdtaccs.BalanceTransferMaximumFee_CAC) as 'Credit Access Checks - Balance Transfer Maximum Fee ($)',IF(cscrdtaccs.CashAdvanceRegularAPR_CAC IS NULL or cscrdtaccs.CashAdvanceRegularAPR_CAC = '', '', (round((cscrdtaccs.CashAdvanceRegularAPR_CAC * 100),2))) as 'Credit Access Checks - Cash Advance Regular APR (%)',IF(csrtyp5.RateTypeName IS NULL or csrtyp5.RateTypeName = '', '', csrtyp5.RateTypeName) as 'Credit Access Checks - Cash Advance Regular Rate Type',IF(cscrdtaccs.CashAdvanceUsageFee_CAC IS NULL or cscrdtaccs.CashAdvanceUsageFee_CAC = '', '', (round((cscrdtaccs.CashAdvanceUsageFee_CAC * 100),2))) as 'Credit Access Checks - Cash Advance Usage Fee (%)',IF(cscrdtaccs.CashAdvanceMinimumFee_CAC IS NULL or cscrdtaccs.CashAdvanceMinimumFee_CAC = '', '', cscrdtaccs.CashAdvanceMinimumFee_CAC) as 'Credit Access Checks - Cash Advance Minimum Fee ($)',IF(cscrdtaccs.CashAdvanceMaximumFee_CAC IS NULL or cscrdtaccs.CashAdvanceMaximumFee_CAC = '', '', cscrdtaccs.CashAdvanceMaximumFee_CAC) as 'Credit Access Checks - Cash Advance Maximum Fee ($)',IF(cscrdtaccs.PurchaseRegularAPR_CAC IS NULL or cscrdtaccs.PurchaseRegularAPR_CAC = '', '', (round((cscrdtaccs.PurchaseRegularAPR_CAC * 100),2))) as 'Credit Access Checks - Purchase Regular APR (%)',IF(csrtyp6.RateTypeName IS NULL or csrtyp6.RateTypeName = '', '', csrtyp6.RateTypeName) as 'Credit Access Checks - Purchase Regular Rate Type'"; 	

            }   

        }
        ################ For Energy Sector ######################## 

        if(in_array('315',$sectrarr) ){

            $energyJoin		=" LEFT JOIN cscan_energy cscenrgy on(cscenrgy.productID=pd.productID) ";
            $erateJoin		=" LEFT JOIN cscan_erate_type csceratetyp on(csceratetyp.RateTypeID=cscenrgy.ERateType) ";
            $etermlengthJoin	=" LEFT JOIN cscan_eterm_length cscetrmlngth on(cscetrmlngth.TermLengthID=cscenrgy.ETermLength) ";
            $erateJoin2		=" LEFT JOIN cscan_erate_type csceratetyp2 on(csceratetyp2.RateTypeID=cscenrgy.ERateType2) ";
            $etermlengthJoin2	=" LEFT JOIN cscan_eterm_length cscetrmlngth2 on(cscetrmlngth2.TermLengthID=cscenrgy.ETermLength2) ";
            $erateJoin3		=" LEFT JOIN cscan_erate_type csceratetyp3 on(csceratetyp3.RateTypeID=cscenrgy.ERateType3) ";
            $etermlengthJoin3	=" LEFT JOIN cscan_eterm_length cscetrmlngth3 on(cscetrmlngth3.TermLengthID=cscenrgy.ETermLength3) ";
            $erateJoin4		=" LEFT JOIN cscan_erate_type csceratetyp4 on(csceratetyp4.RateTypeID=cscenrgy.ERateType4) ";
            $etermlengthJoin4	=" LEFT JOIN cscan_eterm_length cscetrmlngth4 on(cscetrmlngth4.TermLengthID=cscenrgy.ETermLength4) ";

            $selectQuery .=",IF(csceratetyp.RateTypeName IS NULL or csceratetyp.RateTypeName= '', '', csceratetyp.RateTypeName) as 'Energy - Rate Type',IF(cscenrgy.EOfferPrice IS NULL or cscenrgy.EOfferPrice= '', '', cscenrgy.EOfferPrice) as ' Energy - Offer Price (¢ per kWh)',IF(cscetrmlngth.TermLengthName IS NULL or cscetrmlngth.TermLengthName= '', '',cscetrmlngth.TermLengthName) as 'Energy - Term Length',IF(cscenrgy.ECancelFee IS NULL or cscenrgy.ECancelFee= '0', 'No','Yes') as 'Energy - Cancel Fee',IF(cscenrgy.ECancelFeeDetail IS NULL or cscenrgy.ECancelFeeDetail= '', '',cscenrgy.ECancelFeeDetail) as 'Energy - Cancel Fee Detail',IF(csceratetyp2.RateTypeName IS NULL or csceratetyp2.RateTypeName= '', '',csceratetyp2.RateTypeName) as 'Energy - Rate Type',IF(cscenrgy.EOfferPrice2 IS NULL or cscenrgy.EOfferPrice2= '', '',cscenrgy.EOfferPrice2) as 'Energy - Offer Price (¢ per kWh)',IF(cscetrmlngth2.TermLengthName IS NULL or cscetrmlngth2.TermLengthName= '', '',cscetrmlngth2.TermLengthName) as 'Energy - Term Length',IF(csceratetyp3.RateTypeName IS NULL or csceratetyp3.RateTypeName= '', '',csceratetyp3.RateTypeName) as 'Energy - Rate Type',IF(cscenrgy.EOfferPrice3 IS NULL or cscenrgy.EOfferPrice3= '', '', cscenrgy.EOfferPrice3) as ' Energy - Offer Price (¢ per kWh)',IF(cscetrmlngth3.TermLengthName IS NULL or cscetrmlngth3.TermLengthName= '', '',cscetrmlngth3.TermLengthName) as 'Energy - Term Length',IF(csceratetyp4.RateTypeName IS NULL or csceratetyp4.RateTypeName= '', '',csceratetyp4.RateTypeName) as 'Energy - Rate Type',IF(cscenrgy.EOfferPrice4 IS NULL or cscenrgy.EOfferPrice4= '', '', cscenrgy.EOfferPrice4) as ' Energy - Offer Price (¢ per kWh)',IF(cscetrmlngth4.TermLengthName IS NULL or cscetrmlngth4.TermLengthName= '', '',cscetrmlngth4.TermLengthName) as 'Energy - Term Length'  ";

        }

        ############### End Energy Sector ################### 

        ################ For Mortgage & Loan Sector ######################## 

        if(in_array('6',$sectrarr) ){

            $mortgageJoin	=" LEFT JOIN cscan_mortgage_loan cscmortgage on(cscmortgage.productID=pd.productID) ";
            $appmortJoin	=" LEFT JOIN cscan_application_type cscapptyp on(cscapptyp.ApplicationTypeID=cscmortgage.MLApplicationType) ";
            $ratetypJoin7	=" LEFT JOIN cscan_rate_type csrtyp7 on(csrtyp7.RateTypeID=cscmortgage.RateType) ";		

            $selectQuery .=",IF(cscapptyp.ApplicationTypeName IS NULL or cscapptyp.ApplicationTypeName= '', '', cscapptyp.ApplicationTypeName) as 'Mortgage & Loan - Application Type',IF(cscmortgage.OfferedLoanAmount IS NULL or cscmortgage.OfferedLoanAmount= '', '', cscmortgage.OfferedLoanAmount) as 'Mortgage & Loan - Offered Loan Amount ($)',IF(cscmortgage.MaximumLoanAmount IS NULL or cscmortgage.MaximumLoanAmount= '', '', cscmortgage.MaximumLoanAmount) as 'Mortgage & Loan - Maximum Loan Amount ($)',IF(cscmortgage.MinimumLoanAmount IS NULL or cscmortgage.MinimumLoanAmount= '', '', cscmortgage.MinimumLoanAmount) as 'Mortgage & Loan - Minimum Loan Amount ($)',IF(cscmortgage.LoanTerm IS NULL or cscmortgage.LoanTerm= '', '', cscmortgage.LoanTerm) as 'Mortgage & Loan - Loan Term (Months)',IF(cscmortgage.OfferedAPR IS NULL or cscmortgage.OfferedAPR= '', '', (round((cscmortgage.OfferedAPR * 100),2))) as 'Mortgage & Loan - Offered APR (%)',IF(cscmortgage.UpperAPR IS NULL or cscmortgage.UpperAPR= '', '', (round((cscmortgage.UpperAPR * 100),2))) as 'Mortgage & Loan - Upper APR (%)',IF(cscmortgage.LowerAPR IS NULL or cscmortgage.LowerAPR= '', '', (round((cscmortgage.LowerAPR *100),2))) as 'Mortgage & Loan - Lower APR (%)',IF(csrtyp7.RateTypeName IS NULL or csrtyp7.RateTypeName= '', '',csrtyp7.RateTypeName) as 'Mortgage & Loan - Rate Type',IF(cscmortgage.IntroductoryAPR IS NULL or cscmortgage.IntroductoryAPR= '', '', cscmortgage.IntroductoryAPR) as 'Mortgage & Loan - Introductory APR (%)',IF(cscmortgage.IntroductoryPeriod IS NULL or cscmortgage.IntroductoryPeriod= '', '', cscmortgage.IntroductoryPeriod) as 'Mortgage & Loan - Introductory Period (Months)'";

        }

        ################ End For Mortgage & Loan Sector ######################## 


        ################ For Retail/Consumer Services Sector ######################## 

        if(in_array('266',$sectrarr) ){
            $retailconsJoin	=" LEFT JOIN cscan_retail cscretailcon on(cscretailcon.productID=pd.productID) ";
            $selectQuery .=",IF(cscretailcon.RCreditCardMentioned IS NULL or cscretailcon.RCreditCardMentioned= '0', 'No', 'Yes') as 'Retail - Credit Card Mentioned'";

        }

        ################ End For Retail/Consumer Services Sector ######################## 

        ################ For Telecom Sector ######################## 

        if(in_array('9',$sectrarr) ){
            $telecomJoin	=" LEFT JOIN cscan_telecom csctelecom on(csctelecom.productID=pd.productID) ";
            $selectQuery .=",IF(csctelecom.FeaturedPlan IS NULL or csctelecom.FeaturedPlan= '0', 'No', 'Yes') as 'Telecom - Plan Featured',IF(csctelecom.FeaturedPlanName IS NULL or csctelecom.FeaturedPlanName= '', '', csctelecom.FeaturedPlanName) as 'Telecom - Featured Plan Name',IF(csctelecom.ContractRequired IS NULL or csctelecom.ContractRequired= '0', 'No', 'Yes') as 'Telecom - Contract Required',IF(csctelecom.MonthlyCost IS NULL or csctelecom.MonthlyCost= '', '', csctelecom.MonthlyCost) as 'Telecom - Monthly Cost ($)',IF(csctelecom.LowerFeaturedMonthlyCost IS NULL or csctelecom.LowerFeaturedMonthlyCost= '', '', csctelecom.LowerFeaturedMonthlyCost) as 'Telecom - Lower Featured Monthly Cost ($)',IF(csctelecom.UpperFeaturedMonthlyCost IS NULL or csctelecom.UpperFeaturedMonthlyCost= '', '', csctelecom.UpperFeaturedMonthlyCost) as 'Telecom - Upper Featured Monthly Cost ($)',IF(csctelecom.ActivationCharge IS NULL or csctelecom.ActivationCharge= '', '', csctelecom.ActivationCharge) as 'Telecom - Activation Charge ($)',IF(csctelecom.InstallationCharge IS NULL or csctelecom.InstallationCharge= '', '', csctelecom.InstallationCharge) as 'Telecom - Installation Charge ($)',IF(csctelecom.LocalCallingMonthlyCost IS NULL or csctelecom.LocalCallingMonthlyCost= '', '', csctelecom.LocalCallingMonthlyCost) as 'Telecom - Local Calling Monthly Cost ($)',IF(csctelecom.LongDistanceMonthlyCost IS NULL or csctelecom.LongDistanceMonthlyCost= '', '', csctelecom.LongDistanceMonthlyCost) as 'Telecom - Long Distance Monthly Cost ($)',IF(csctelecom.TelecomIntroductoryCost IS NULL or csctelecom.TelecomIntroductoryCost= '', '', csctelecom.TelecomIntroductoryCost) as 'Telecom - Introductory Cost ($)',IF(csctelecom.TelecomIntroductoryPeriod IS NULL or csctelecom.TelecomIntroductoryPeriod= '', '', csctelecom.TelecomIntroductoryPeriod) as 'Telecom - Introductory Period (Months)'";

        }

        ################ End For Telecom Sector ######################## 

        ################ For Travel & Leisure Sector ######################## 

        if(in_array('219',$sectrarr) ){
            $travelJoin	=" LEFT JOIN cscan_travel_leisure csctrvl on(csctrvl.productID=pd.productID) ";
            $cardtypeJoin3	=" LEFT JOIN cscan_card_type cscrdtyp3 on(cscrdtyp3.CardTypeID=csctrvl.TLCardNetwork) ";

            $selectQuery .=",IF(csctrvl.TLDebitCardMentioned IS NULL or csctrvl.TLDebitCardMentioned= '0', 'No', 'Yes') as 'Travel & Leisure - Debit Card Mentioned',IF(csctrvl.TLCreditCardMentioned IS NULL or csctrvl.TLCreditCardMentioned= '0', 'No', 'Yes') as 'Travel & Leisure - Debit Card Mentioned',IF(cscrdtyp3.CardTypeName IS NULL or cscrdtyp3.CardTypeName= '', '', cscrdtyp3.CardTypeName) as 'Travel & Leisure - Card Network'";

        }

        ################ End For Travel & Leisure Sector ########################  

      ######################### End Added for Excel Export ################################
          
    }
    
     $ignoreindex = "";    
    //$ignoreindex = " ignore index(entryID_sort_index,cscan_product_company_index,c_p_d_productName_index,c_p_d_entryID_index,c_p_d_productHeadline_index,c_p_d_actual_addedToDatabase_index,  c_p_d_variantID_index,c_p_d_DMSource_index,c_p_d_mPanelID_index,c_p_d_mChannelID_index,c_p_d_sectorID_index,idx_comp,idx_comp2,idx_comp3)";

      
    if(in_array('digital_access', $sess_search_exclude)){
      // $pjoin=" JOIN cscan_panelists_product pp ON (pd.productID=pp.productID) ";
       $where.=' AND pd.productID NOT IN (select productID from cscan_panelists_product) ';
    }  
    
    $selectQuery .= " FROM cscan_product_detail pd $ignoreindex$ocrtext$pjoin$cjoin$ccjoin$afjoin$affjoin$dmajoin$edcjoin$pcjoin$bjoin$ejoin$mljoin$rjoin $tljoin$ajoin$sjoin$ojoin $mchanneljoin $mpanneljoin $statejoin $countryjoin $agejoin $incomejoin $mtypejoin $issuetypjoin $delmethodjoin $primarysectorjoin  $primarycatjoin $primarysubcatjoin $primarysubsubcatjoin $affcatjoin $affsubcatjoin $filesizejoin $panelistsjoin $panelists_detailjoin $ecohortcodejoin $psycodejoin $pzmcodejoin $cnxcodejoin $wealthcompjoin $ethinicityjoin $religionjoin $languagejoin $groupjoin $countrycjoin $assimilationjoin $incomproducingjoin $valuescorejoin $householdincomejoin $homeownerrenterjoin $dmacodejoin $bankingJoin $cardtypeJoin $rewardtypeJoin $paymentcardsJoin $apptypeJoin  $cardtypeJoin2 $cardlevelJoin  $cardlevelJoin2	$rewardJoin	$ratetypJoin $ratetypJoin2 $ratetypJoin3 $creditaccesJoin $ratetypJoin4 $ratetypJoin5 $ratetypJoin6 $energyJoin $erateJoin $etermlengthJoin $erateJoin2 $etermlengthJoin2 $erateJoin3 $etermlengthJoin3 $erateJoin4 $etermlengthJoin4 $mortgageJoin $appmortJoin $ratetypJoin7 $retailconsJoin $telecomJoin $travelJoin $cardtypeJoin3 $countryjoin2 $publicationjoin $where$owhere ";
    $selectQuery .=" GROUP BY cspsp.panelist_id,cspsp.productID";
    return array($selectQuery, $saved, $sortby);
}
