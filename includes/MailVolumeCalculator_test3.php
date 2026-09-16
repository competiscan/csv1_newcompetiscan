<?php 
require_once 'dbcon.php';
require_once 'functions.php';

class MailVolumeCalculator
{
    private $regionArray;
    private $income_rArray;
    private $incomeArray;
    private $ageArray;
    private $countryArray;
    private $multArray;

    const DEFAULT_MAIL_VOLUME = 122375;
    const MAX_MAIL_VOLUME = 1200000;

    public function __construct() {
        $this->regionArray = $this->getRegionArray();
        $this->income_rArray = $this->getIncomeReportArray();
        $this->incomeArray = $this->getIncomeArray();
        $this->ageArray = $this->getAgeArray();
        $this->countryArray = $this->getCountryArray();
        $this->multArray = $this->getMultipliersArray();
    }

    function doPreMailVolume()
    {
        global $DRW, $DRW_read2, $DRW_main, $DRW_crm;
        $sqlu = "UPDATE cscan_product_detail SET isDemographic=2 WHERE isDemographic=1";
        $DRW->query($sqlu, $DRW_main);
        $sqlu = "UPDATE cscan_product_detail SET isFICO=2 WHERE isFICO=1";
        $DRW->query($sqlu, $DRW_main);
    }

    function doPostMailVolume()
    {
        global $DRW, $DRW_read2, $DRW_main, $DRW_crm;

        $check = "SELECT DISTINCT pd.productID
		FROM cscan_product_detail pd JOIN cscan_panelists_product pp ON (pd.productID=pp.productID) JOIN cscan_panelists pa ON (pp.panelist_id=pa.panelist_id AND contactTypeID=2)
		WHERE productStatus=1 AND (mPanelID=1 OR mPanelID=2) AND isDemographic=2 AND (ppmv>0 OR mChannelID<>1 OR delmethid=4 OR delmethid=5)";
        $result = $DRW->query($check, $DRW_read2);
        while ($data = $DRW->fetch_row($result)) {
            $productID = $data[0];
            $sqlu = "UPDATE cscan_product_detail SET isDemographic=1 WHERE productID=$productID";
            $DRW->query($sqlu, $DRW_main);
        }
        $sqlu = "UPDATE cscan_product_detail SET isDemographic=0 WHERE isDemographic=2";
        $DRW->query($sqlu, $DRW_main);

        $check = "SELECT DISTINCT panelist_id FROM cscan_panelists_product WHERE pproductFICO<>''";
        $result = $DRW->query($check, $DRW_read2);
        while ($data = $DRW->fetch_row($result)) {
            $panelist_id = $data[0];

            $check2 = "SELECT DISTINCT ppdate FROM cscan_panelists_product WHERE panelist_id=$panelist_id AND pproductFICO='' ORDER BY ppdate";
            $result2 = $DRW->query($check2, $DRW_read2);
            while ($data2 = $DRW->fetch_row($result2)) {
                $ppdate = $data2[0];

                $check3 = "SELECT pproductFICO FROM cscan_panelists_product WHERE panelist_id=$panelist_id AND pproductFICO<>'' AND ppdate<='" . $ppdate . "' ORDER BY ppdate DESC LIMIT 1";
                $result3 = $DRW->query($check3, $DRW_read2);
                $data3 = $DRW->fetch_row($result3);
                $pproductFICO = $data3[0];
                if (empty($pproductFICO)) {
                    $check3 = "SELECT pproductFICO FROM cscan_panelists_product WHERE panelist_id=$panelist_id AND pproductFICO<>'' AND ppdate>'" . $ppdate . "' ORDER BY ppdate ASC LIMIT 1";
                    $result3 = $DRW->query($check3, $DRW_read2);
                    $data3 = $DRW->fetch_row($result3);
                    $pproductFICO = $data3[0];
                }
                if (!empty($pproductFICO)) {
                    $sqlu = "UPDATE cscan_panelists_product SET pproductFICO='" . $DRW->real_escape_string($pproductFICO) . "' WHERE panelist_id=$panelist_id AND pproductFICO='' AND ppdate='" . $ppdate . "'";
                    $DRW->query($sqlu, $DRW_main);
                }
            }
        }

        $check = "SELECT DISTINCT pd.productID
		FROM cscan_product_detail pd JOIN cscan_panelists_product pp ON (pd.productID=pp.productID) JOIN cscan_panelists pa ON (pp.panelist_id=pa.panelist_id AND contactTypeID=2)
		WHERE productStatus=1 AND (mPanelID=1 OR mPanelID=2) AND isFICO=2 AND (ppfico_score>0 OR pproductFICO<>'')";
        $result = $DRW->query($check, $DRW_read2);
        while ($data = $DRW->fetch_row($result)) {
            $productID = $data[0];
            $sqlu = "UPDATE cscan_product_detail SET isFICO=1 WHERE productID=$productID";
            $DRW->query($sqlu, $DRW_main);
        }
        $sqlu = "UPDATE cscan_product_detail SET isFICO=0 WHERE isFICO=2";
        $DRW->query($sqlu, $DRW_main);
    }

    //get multipliers from the db
    function getMultipliersArray()
    {
        global $DRW, $DRW_read2;
        $multArray = array();
        $check = "SELECT multiplier,m_year,m_month,m_sectorID,m_categoryID,m_companyID,m_countryID FROM cscan_mv_multiplier";
        $result = $DRW->query($check, $DRW_read2);
        while ($data = $DRW->fetch_row($result)) {
            $m_countryID = (int)$data[6];
            $m_year = (int)$data[1];
            $m_month = (int)$data[2];
            $m_sectorID = (int)$data[3];
            $m_categoryID = (int)$data[4];
            $m_companyID = (int)$data[5];

            if (!isset($multArray[$m_countryID])) {
                $multArray[$m_countryID] = array();
            }

            if (!isset($multArray[$m_countryID][$m_year])) {
                $multArray[$m_countryID][$m_year] = array();
            }
            if (!isset($multArray[$m_countryID][$m_year][$m_month])) {
                $multArray[$m_countryID][$m_year][$m_month] = array();
            }
            if (!isset($multArray[$m_countryID][$m_year][$m_month][$m_sectorID])) {
                $multArray[$m_countryID][$m_year][$m_month][$m_sectorID] = array();
            }
            if (!isset($multArray[$m_countryID][$m_year][$m_month][$m_sectorID][$m_categoryID])) {
                $multArray[$m_countryID][$m_year][$m_month][$m_sectorID][$m_categoryID] = array();
            }
            $multArray[$m_countryID][$m_year][$m_month][$m_sectorID][$m_categoryID][$m_companyID] = $data[0];
        }
        @$DRW->free_result($result);
        return $multArray;
    }

    function getRegionArray()
    {
        global $DRW, $DRW_read2;
        $regionArray = array();
        $check = "SELECT SQL_NO_CACHE stateID,regionID FROM cscan_region_state";
        $result = $DRW->query($check, $DRW_read2);
        while ($data = $DRW->fetch_row($result)) {
            $stateID = $data[0];
            $regionID = $data[1];
            $regionArray[$stateID] = $regionID;
        }
        @$DRW->free_result($result);
        return $regionArray;
    }

    function getIncomeReportArray()
    {
        global $DRW, $DRW_read2;
        $income_rArray = array();
        $check = "SELECT SQL_NO_CACHE ir_ID,ir_max,ir_min FROM cscan_income_report";
        $result = $DRW->query($check, $DRW_read2);
        while ($data = $DRW->fetch_row($result)) {
            $ir_ID = $data[0];
            $ir_max = $data[1];
            $ir_min = $data[2];

            $income_rArray[$ir_ID] = array($ir_min, $ir_max);
        }
        @$DRW->free_result($result);
        return $income_rArray;
    }

    function getIncomeArray()
    {
        global $DRW, $DRW_read2;
        $incomeArray = array();
        $check = "SELECT SQL_NO_CACHE incomeID,income_max,income_min FROM cscan_incometype";
        $result = $DRW->query($check, $DRW_read2);
        while ($data = $DRW->fetch_row($result)) {
            $income_ID = $data[0];
            $income_max = $data[1];
            $income_min = $data[2];

            $incomeArray[$income_ID] = array($income_min, $income_max);
        }
        @$DRW->free_result($result);
        return $incomeArray;
    }

    function getAgeArray()
    {
        global $DRW, $DRW_read2;
        $ageArray = array();
        $check = "SELECT SQL_NO_CACHE ageID,age_max,age_min FROM cscan_age";
        $result = $DRW->query($check, $DRW_read2);
        while ($data = $DRW->fetch_row($result)) {
            $ageID = $data[0];
            $age_max = $data[1];
            $age_min = $data[2];

            $ageArray[$ageID] = array($age_min, $age_max);
        }
        @$DRW->free_result($result);
        return $ageArray;
    }

    function getCountryArray()
    {
        global $DRW, $DRW_read2;
        $countryArray = array();
        $check = "SELECT SQL_NO_CACHE stateID,countryID FROM cscan_state as state JOIN cscan_country as country ON state.countryCode=country.countryCode";
        $result = $DRW->query($check, $DRW_read2);
        while ($data = $DRW->fetch_row($result)) {
            $stateID = $data[0];
            $countryID = $data[1];

            $countryArray[$stateID] = $countryID;
        }
        @$DRW->free_result($result);
        return $countryArray;
    }

    function doMailVolume_old($year, $month, $factor = 1.88, $doprint = false)
    {
        global $DRW, $DRW_read2, $DRW_main, $DRW_crm;
        $curr_date = date('Y-m');
        $calc_date = $year . '-' . $month;
        $lessthan = false;
        if ($curr_date == $calc_date) {
            $day = (int)date('j');
            if ($day < 7) {
                $lessthan = true;
            }
        }
        $currentdatetime=date('Y-m-d h:i:s');
        $calc_date_range1 = $calc_date . '-01 00:00:00';
        $ctime = strtotime($calc_date_range1);
        $ctime += 2851200; //33 days
        $calc_date_range2 = date('Y-m', $ctime) . '-01 00:00:00';

        $all_total_panelists = 0;
        $all_total_panelists_w = 0;
        $comboArray = array();
        $noComboArray = array();
//        $sql_P = "SELECT SQL_NO_CACHE ppage,pincomeID,ppstateID,pp.homeownershipID,ppdate,pp.panelist_id,pp.productID,pgender,ppfico_score,isBiz,mChannelID,delmethid,pweight,mTypeID,parent_panelist_id,document_size_byte
//            FROM cscan_product_detail pd JOIN cscan_panelists_product pp ON (pd.productID=pp.productID) JOIN cscan_panelists pa ON (pp.panelist_id=pa.panelist_id AND contactTypeID=2) LEFT JOIN cscan_document d ON (pp.productID=d.productID AND d.document_id=1)
//            WHERE productStatus=1 AND (mPanelID=1 OR mPanelID=2) AND countryCode_copy = 'CA' AND ppdate>='$calc_date_range1' AND ppdate<'$calc_date_range2'";
//        
        ########### Auto Insurance and Property Insurance for the company "Progressive" for all products ############
        /*
        echo  $sql_P = "SELECT SQL_NO_CACHE ppage,pincomeID,ppstateID,pp.homeownershipID,ppdate,pp.panelist_id,pp.productID,pgender,ppfico_score,isBiz,mChannelID,delmethid,pweight,mTypeID,parent_panelist_id,document_size_byte
                       FROM cscan_product_detail pd 
                       JOIN cscan_panelists_product pp ON (pd.productID=pp.productID) 
                       JOIN cscan_panelists pa ON (pp.panelist_id=pa.panelist_id AND contactTypeID=2) 
                       LEFT JOIN cscan_document d ON (pp.productID=d.productID AND d.document_id=1)
                       JOIN cscan_company_product ccp ON (ccp.productID = pp.productID AND ccp.companyID=814)
                      WHERE productStatus=1 
                        AND (mPanelID=1 OR mPanelID=2) 
                        AND ppdate>='$calc_date_range1' AND ppdate<'$calc_date_range2'
                        AND ((CONCAT(',', pd.sectorID, ',') REGEXP ',4,')) 
                        AND ((CONCAT(',', pd.categoryID, ',') REGEXP ',12,') OR (CONCAT(',', pd.categoryID, ',') REGEXP ',15,')  )    
                ";
        
        */
        ########### END Auto Insurance and Property Insurance for the company "Progressive" for all products ############
        $canadaArray=array('AB','BC','MB','NB','NL','NS','NT','NU','ON','PW','QC','SK','YT'); 
        $usaArray=array('AL','AK','AZ','AR','CA','CO','CT','DE','FL','GA','HI','ID','IL','IN','IA','KS','KY','LA','ME','MD','MA','MI','MN','MS','MO','MT','NE','NV','NH','NJ','NM','NY','NC','ND','OH','OK','OR','PA','RI','SC','SD','TN','TX','UT','VT','VA','WA','WV','WI','WY','DC');
        
        ##### Canada Credit card for all companies and products  ##################
        /*
        $sql_P = "SELECT SQL_NO_CACHE ppage,pincomeID,ppstateID,pp.homeownershipID,ppdate,pp.panelist_id,pp.productID,pgender,ppfico_score,isBiz,mChannelID,delmethid,pweight,mTypeID,parent_panelist_id,document_size_byte
                       FROM cscan_product_detail pd 
                       JOIN cscan_panelists_product pp ON (pd.productID=pp.productID) 
                       JOIN cscan_panelists pa ON (pp.panelist_id=pa.panelist_id AND contactTypeID=2) 
                       LEFT JOIN cscan_document d ON (pp.productID=d.productID AND d.document_id=1)
                       
                      WHERE productStatus=1 
                        AND (mPanelID=1 OR mPanelID=2) 
                        AND ppdate>='$calc_date_range1' AND ppdate<'$calc_date_range2'
                        AND ((CONCAT(',', pd.sectorID, ',') REGEXP ',90,')) 
                        AND pa.state in('".implode("','",$canadaArray)."' )
                        
                ";
        */
        ##### end for Canada Credit card for all companies and products  ##################
        ##### For For Auto Insurance and Property Insurance in the United States ##########
        
      echo  $sql_P = "SELECT SQL_NO_CACHE ppage,pincomeID,ppstateID,pp.homeownershipID,ppdate,pp.panelist_id,pp.productID,pgender,ppfico_score,isBiz,mChannelID,delmethid,pweight,mTypeID,parent_panelist_id,document_size_byte
                       FROM cscan_product_detail pd 
                       JOIN cscan_panelists_product pp ON (pd.productID=pp.productID) 
                       JOIN cscan_panelists pa ON (pp.panelist_id=pa.panelist_id AND contactTypeID=2) 
                       LEFT JOIN cscan_document d ON (pp.productID=d.productID AND d.document_id=1)
                       
                      WHERE productStatus=1 
                        AND (mPanelID=1 OR mPanelID=2) 
                        AND ppdate>='$calc_date_range1' AND ppdate<'$calc_date_range2'
                        AND ((CONCAT(',', pd.sectorID, ',') REGEXP ',4,')) 
                        AND ((CONCAT(',', pd.categoryID, ',') REGEXP ',13,') ) 
                        AND pa.state in('".implode("','",$usaArray)."' )
                        
                ";
      
        /* added for running march month data */
        /*
        $sql_P = "SELECT SQL_NO_CACHE ppage,pincomeID,ppstateID,pp.homeownershipID,ppdate,pp.panelist_id,pp.productID,pgender,ppfico_score,isBiz,mChannelID,delmethid,pweight,mTypeID,parent_panelist_id,document_size_byte
            FROM cscan_product_detail pd JOIN cscan_panelists_product pp ON (pd.productID=pp.productID) JOIN cscan_panelists pa ON (pp.panelist_id=pa.panelist_id AND contactTypeID=2) LEFT JOIN cscan_document d ON (pp.productID=d.productID AND d.document_id=1)
            WHERE productStatus=1 AND (mPanelID=1 OR mPanelID=2) AND ppdate>='2018-02-01 00:00:00' AND ppdate<'2018-03-01 00:00:00'";
        */
        /* $sql_P = "SELECT SQL_NO_CACHE ppage,pincomeID,ppstateID,pp.homeownershipID,ppdate,pp.panelist_id,pp.productID,pgender,ppfico_score,isBiz,mChannelID,delmethid,pweight,mTypeID,parent_panelist_id,document_size_byte
            FROM cscan_product_detail pd JOIN cscan_panelists_product pp ON (pd.productID=pp.productID) JOIN cscan_panelists pa ON (pp.panelist_id=pa.panelist_id AND contactTypeID=2) LEFT JOIN cscan_document d ON (pp.productID=d.productID AND d.document_id=1)
            WHERE productStatus=1 AND (mPanelID=1 OR mPanelID=2) AND ppdate>='2016-08-01 00:00:00' AND ppdate<'2016-09-01 00:00:00' AND pd.productID=2317449";
        */ 
        //AND pd.productID=2317449
         
         /* End to added for running march month data */        
       
        $result_P = $DRW->query($sql_P, $DRW_read2);
        while ($row = $DRW->fetch_row($result_P)) {
            $p_age = $row[0];
            $p_incomeID = $row[1];
            $p_stateID = $row[2];
            $homeownershipID = $row[3];
            $ppdate = $row[4];
            $panelist_id = $row[5];
            $productID = $row[6];
            $gender = strtoupper(substr(trim($row[7]), 0, 1));
            $ppfico_score = $row[8];
            $isBiz = $row[9];
            $mChannelID = $row[10];
            $delmethid = $row[11];
            $pweight = $row[12];
            $mTypeID = $row[13];
            $parent_panelist_id = $row[14];
            $document_size_byte = (float)$row[15];

            $query_pt = "SELECT ValueScore_for_Household FROM cscan_panelists_appends WHERE panelist_id=$panelist_id";
            $result_pt = $DRW->query($query_pt, $DRW_read2);
            $row_pt = $DRW->fetch_row($result_pt);
            if (!empty($row_pt[0])) {
                $ppfico_score = 1;
            } else {
                $ppfico_score = 0;
            }

            if ($mChannelID != 1 || $delmethid == 4 || $delmethid == 5) { //only direct mail for demographics and no FSI (also in productDetail.php display) // || $parent_panelist_id>0 //and no subpanelists (only here)
                if ($ppfico_score > 0) {
                    $ficosql = ',isFICO=1';
                } else {
                    $ficosql = '';
                }
                $sqlu = "UPDATE cscan_product_detail SET isDemographic=1$ficosql WHERE productID=$productID";
                $DRW->query($sqlu, $DRW_main);
                
                //$sqlu = "UPDATE cscan_panelists_product SET ppmv=0,actual_ppmv=0,ppmv_w=0,ppmv_m=0,ppspend=0,ppmv_onupdate='".$currentdatetime."' WHERE panelist_id=$panelist_id AND productID=$productID AND ppdate='$ppdate'";
                //$DRW->query($sqlu, $DRW_main);

                continue;
            } elseif ($ppfico_score > 0) {
                $sqlu = "UPDATE cscan_product_detail SET isFICO=1 WHERE productID=$productID";
                $DRW->query($sqlu, $DRW_main);
            }

            $regionID = 0;
            if (isset($this->regionArray[$p_stateID])) $regionID = $this->regionArray[$p_stateID];

            $ir_ID = 0;
            if (isset($this->incomeArray[$p_incomeID])) {
                foreach ($this->income_rArray as $id => $arry) {
                    if ($this->incomeArray[$p_incomeID][0] >= $arry[0] && $this->incomeArray[$p_incomeID][1] <= $arry[1]) {
                        $ir_ID = $id;
                        break;
                    }
                }
            }
            $ageID = 0;
            foreach ($this->ageArray as $id => $arry) {
                if ($p_age >= $arry[0] && $p_age <= $arry[1]) {
                    $ageID = $id;
                    break;
                }
            }
            $doin = true;
            foreach ($comboArray as $key => $arry) {
                if ($arry[0] == $ageID && $arry[1] == $ir_ID && $arry[2] == $regionID && $arry[3] == $homeownershipID) {
                    $doin = false;
                    $all_total_panelists++;
                    $all_total_panelists_w += $pweight;
                    $comboArray[$key][10][] = array($productID, $panelist_id, $ppdate, $document_size_byte, $p_stateID);
                    $comboArray[$key][11] += $pweight;
                    break;
                }
            }
            if ($doin) {
                $defs = "SELECT SQL_NO_CACHE us_individuals,population_share,age_name,ir_name,region_name,homeownership_name,panelist_share,panelist_count FROM cscan_mv_defs cd, cscan_age ca, cscan_income_report ci, cscan_region cr, cscan_homeownership ch WHERE
                    cd.ageID=$ageID AND cd.ir_ID=$ir_ID AND cd.regionID=$regionID AND cd.homeownershipID=$homeownershipID AND
                    cd.ageID=ca.ageID AND cd.ir_ID=ci.ir_ID AND cd.regionID=cr.regionID AND cd.homeownershipID=ch.homeownershipID";
                $resultD = $DRW->query($defs, $DRW_read2);
                $dataD = $DRW->fetch_row($resultD);
                @$DRW->free_result($resultD);

                if ($dataD[0] != '') {
                    $all_total_panelists++;
                    $comboArray[] = array($ageID, $ir_ID, $regionID, $homeownershipID, $dataD[0], $dataD[1], $dataD[2], $dataD[3], $dataD[4], $dataD[5], array(array($productID, $panelist_id, $ppdate, $document_size_byte, $p_stateID)), $pweight,$dataD[6],$dataD[7]);
                } else {
                    if (!in_array($productID, $noComboArray)) {
                        $noComboArray[] = $productID;
                    }
                }
            }
        }
        @$DRW->free_result($result_P);
        $productArray = array(); 
        //echo '<pre>';
        //print_r($comboArray);
        //die;
        //echo 'processing..';
        $panelist_share='';
        foreach ($comboArray as $key => $arry) {
            $ageID = $arry[0];
            $ir_ID = $arry[1];
            $regionID = $arry[2];
            $homeownershipID = $arry[3];
            $us_individuals = $arry[4];
            $population_share = $arry[5];
            $age_name = $arry[6];
            $ir_name = $arry[7];
            $region_name = $arry[8];
            $homeownership_name = $arry[9];
            $ppArray = $arry[10];
            $panelists_w = $arry[11];
            //$panelists = count($ppArray);
            $panelist_share = $arry[12];
            $panelists = $arry[13];
            
            
            /*
            if(empty($panelist_share)){
                if ($all_total_panelists > 0) $panelist_share = $panelists / $all_total_panelists;
                else $panelist_share = 0;
            } */
            
            if ($panelist_share > 0) $weight = $population_share / $panelist_share;
            else $weight = 0;
            if ($panelists > 0) $representation = ($us_individuals / $panelists) * $weight;
            else $representation = 0;
            $actual_mail_volume = $mail_volume = $representation * $factor;

            if ($all_total_panelists_w > 0) $panelist_share_w = $panelists_w / $all_total_panelists_w;
            else $panelist_share_w = 0;
            $ppmv_w = ($panelist_share_w * $us_individuals) / .08;

            if ($lessthan) { //if today is within the first 7 days of the month, use default value
                $mail_volume = self::DEFAULT_MAIL_VOLUME;
            } elseif ($mail_volume > self::MAX_MAIL_VOLUME) {
                $mail_volume = self::MAX_MAIL_VOLUME;
            }
            //echo '<pre>';            
            //print_r($this->countryArray); die;
            foreach ($ppArray as $pp) {
                list($productID, $panelist_id, $ppdate, $document_size_byte, $pp_state) = $pp;
                $pp_country = 1;
                
                $sql_chk_ppmv = "SELECT panelist_id FROM cscan_panelists_product where panelist_id='".$panelist_id."' AND productID='".$productID."' AND ppdate='".$ppdate."' AND ppmv<=0";
                $result_ppmv = $DRW->query($sql_chk_ppmv, $DRW_read2);
                $dataD_ppmv = $DRW->fetch_row($result_ppmv);             
             // Comment for override emv calculation 
                //if (!empty($dataD_ppmv[0])) { 
                    //echo $sql_chk_ppmv;
                    //echo '<br><br><br><br>';

                  if (isset($this->countryArray[$pp_state])) $pp_country = $this->countryArray[$pp_state];

                  $ppspend = doSpend($mail_volume, $document_size_byte);
                  /*
                  $upd = "UPDATE cscan_panelists_product SET ppmv=$mail_volume,ageID=$ageID,ir_ID=$ir_ID,regionID=$regionID,actual_ppmv=$actual_mail_volume,ppmv_w=$ppmv_w,ppmv_m=$mail_volume,ppspend=$ppspend,ppmv_onupdate='".$currentdatetime."'
                      WHERE panelist_id=$panelist_id AND productID=$productID AND ppdate='$ppdate'";
                  $DRW->query($upd, $DRW_main);
                  */
                  list($ppy, $ppm) = explode('-', $ppdate);
                  $ppy = (int)$ppy;
                  $ppm = (int)$ppm;
                  $multAs = array();

                  /*
                  if (isset($this->multArray[$pp_country][0][0])) { //all dates
                      $multAs[] = $this->multArray[$pp_country][0][0];
                  }
                  if (isset($this->multArray[$pp_country][0][$ppm])) { //all years this particular month
                      $multAs[] = $this->multArray[$pp_country][0][$ppm];
                  }
                  if (isset($this->multArray[$pp_country][$ppy][0])) { //all months this particular year
                      $multAs[] = $this->multArray[$pp_country][$ppy][0];
                  }
                  if (isset($this->multArray[$pp_country][$ppy][$ppm])) { //this month of this year
                      $multAs[] = $this->multArray[$pp_country][$ppy][$ppm];
                  }
                  */

                    $sector_chk_id='';
                    $cat_chk_id='';
                    $company_chk_id='';
                    $sql_chk_comp = "SELECT SQL_NO_CACHE companyID FROM cscan_company_product where primary_co=1 AND productID='".$productID."'";
                    $result_comp = $DRW->query($sql_chk_comp, $DRW_read2);
                    $data_comp = $DRW->fetch_row($result_comp); 
                    if (!empty($data_comp[0])) {                    
                        $company_chk_id = $data_comp[0];
                    }

                    $sql_chk_sc = "SELECT SQL_NO_CACHE scsc_sectorID,scsc_categoryID,scsc_subCategoryID FROM cscan_scsc_product where scsc_sort=1 AND productID='".$productID."'";
                    $result_scsc = $DRW->query($sql_chk_sc, $DRW_read2);
                     $data_scsc = $DRW->fetch_assoc($result_scsc);
                    if (!empty($data_scsc['scsc_sectorID'])) {
                        $sector_chk_id = $data_scsc['scsc_sectorID'];
                        $cat_chk_id = $data_scsc['scsc_categoryID'];
                        $subcat_chk_id = $data_scsc['scsc_subCategoryID'];

                    }
                    $multiplier='';
                    if($company_chk_id>0 && $sector_chk_id>0 && $cat_chk_id>0 && $subcat_chk_id>0 && $pp_country>0){ 
                        $sql_chk_mv = "SELECT SQL_NO_CACHE multiplier FROM cscan_mv_multiplier where m_sectorID='".$sector_chk_id."' AND m_categoryID='".$cat_chk_id."' AND m_subcategoryID='".$subcat_chk_id."' AND m_companyID='".$company_chk_id."' AND m_countryID='".$pp_country."' limit 0,1";
                        $result_mv = $DRW->query($sql_chk_mv, $DRW_read2);
                        $data_multiplier = $DRW->fetch_assoc($result_mv);
                        if (!empty($data_multiplier['multiplier'])) {                        
                            $multiplier = $data_multiplier['multiplier'];                        
                        }
                    }
                    if($multiplier=='' && $company_chk_id>0 && $sector_chk_id>0 && $cat_chk_id>0 && $pp_country>0){ 
                        $sql_chk_mv = "SELECT SQL_NO_CACHE multiplier FROM cscan_mv_multiplier where m_sectorID='".$sector_chk_id."' AND m_categoryID='".$cat_chk_id."' AND m_companyID='".$company_chk_id."' AND m_countryID='".$pp_country."' limit 0,1";
                        $result_mv = $DRW->query($sql_chk_mv, $DRW_read2);
                        $data_multiplier2 = $DRW->fetch_assoc($result_mv);
                        if (!empty($data_multiplier2['multiplier'])) {                        
                            $multiplier = $data_multiplier2['multiplier'];                        
                        }
                    }
                    if($multiplier=='' && $company_chk_id>0 && $sector_chk_id>0 && $pp_country>0){ 
                        $sql_chk_mv = "SELECT SQL_NO_CACHE multiplier FROM cscan_mv_multiplier where m_sectorID='".$sector_chk_id."' AND m_companyID='".$company_chk_id."' AND m_countryID='".$pp_country."' limit 0,1";
                        $result_mv = $DRW->query($sql_chk_mv, $DRW_read2);
                        $data_multiplier3 = $DRW->fetch_assoc($result_mv);
                        if (!empty($data_multiplier3['multiplier'])) { 

                            $multiplier = $data_multiplier3['multiplier'];                        
                        }
                    }

                    if($multiplier=='' && $sector_chk_id>0 && $cat_chk_id>0 && $subcat_chk_id>0 && $pp_country>0){
                        $sql_chk_mv = "SELECT SQL_NO_CACHE multiplier FROM cscan_mv_multiplier where m_sectorID='".$sector_chk_id."' AND m_categoryID='".$cat_chk_id."' AND m_subcategoryID='".$subcat_chk_id."' AND m_countryID='".$pp_country."' limit 0,1";
                        $result_mv = $DRW->query($sql_chk_mv, $DRW_read2);
                        $data_multiplier4 = $DRW->fetch_assoc($result_mv);
                        if (!empty($data_multiplier4['multiplier'])) {

                            $multiplier = $data_multiplier4['multiplier'];                        
                        }                    
                    }

                    if($multiplier=='' && $sector_chk_id>0 && $cat_chk_id>0 && $pp_country>0){
                        $sql_chk_mv = "SELECT SQL_NO_CACHE multiplier FROM cscan_mv_multiplier where m_sectorID='".$sector_chk_id."' AND m_categoryID='".$cat_chk_id."' AND m_countryID='".$pp_country."' limit 0,1";
                        $result_mv = $DRW->query($sql_chk_mv, $DRW_read2);
                        $data_multiplier5 = $DRW->fetch_assoc($result_mv);
                        if (!empty($data_multiplier5['multiplier'])) {

                            $multiplier = $data_multiplier5['multiplier'];                        
                        }                    
                    }
                    if($multiplier=='' && $cat_chk_id>0 && $subcat_chk_id>0 && $pp_country>0){
                        $sql_chk_mv = "SELECT SQL_NO_CACHE multiplier FROM cscan_mv_multiplier where m_categoryID='".$cat_chk_id."' AND m_subcategoryID='".$subcat_chk_id."' AND m_countryID='".$pp_country."' limit 0,1";
                        $result_mv = $DRW->query($sql_chk_mv, $DRW_read2);
                        $data_multiplier8 = $DRW->fetch_assoc($result_mv);
                        if (!empty($data_multiplier8['multiplier'])) {                        
                            $multiplier = $data_multiplier8['multiplier'];                        
                        }

                    }
                    if($multiplier=='' && $cat_chk_id>0 && $pp_country>0){
                        $sql_chk_mv = "SELECT SQL_NO_CACHE multiplier FROM cscan_mv_multiplier where m_categoryID='".$cat_chk_id."' AND m_countryID='".$pp_country."' limit 0,1";
                        $result_mv = $DRW->query($sql_chk_mv, $DRW_read2);
                        $data_multiplier6 = $DRW->fetch_assoc($result_mv);
                        if (!empty($data_multiplier6['multiplier'])) {

                            $multiplier = $data_multiplier6['multiplier'];                        
                        }                    

                    }
                    if($multiplier=='' && $sector_chk_id>0 && $pp_country>0){
                        $sql_chk_mv = "SELECT SQL_NO_CACHE multiplier FROM cscan_mv_multiplier where m_sectorID='".$sector_chk_id."' AND m_countryID='".$pp_country."' limit 0,1";
                        $result_mv = $DRW->query($sql_chk_mv, $DRW_read2);
                        $data_multiplier7 = $DRW->fetch_assoc($result_mv);
                        if (!empty($data_multiplier7['multiplier'])) {                        
                            $multiplier = $data_multiplier7['multiplier'];                        
                        }

                    }
                    if($multiplier==''){
                        $multiplier=1;
                    }
                    
  //                if($panelist_id=='6898' AND $productID=='3901444'){
  //                    echo 'mail_volume: '.$mail_volume.' multiplier: '.$multiplier;
  //                    die;
  //                }
                    $mail_volume_save = $mail_volume * $multiplier;
                    $mail_volume_save=round($mail_volume_save);

                  echo  $upd = "UPDATE cscan_panelists_product SET ppmv=$mail_volume_save,ageID=$ageID,ir_ID=$ir_ID,regionID=$regionID,actual_ppmv=$actual_mail_volume,ppmv_w=$ppmv_w,ppmv_m=$mail_volume_save,ppspend=$ppspend,ppmv_onupdate='".$currentdatetime."'
                      WHERE panelist_id=$panelist_id AND productID=$productID AND ppdate='".$ppdate."'";

                    $DRW->query($upd, $DRW_main);

                    //echo '<br />';
                    /*
                    foreach ($multAs as $multA) {
                        foreach ($multA as $pp_sector => $a) {
                            foreach ($a as $pp_category => $a2) {
                                if ($pp_category == 0) {
                                    $ppctext = '';
                                } else {
                                    $ppctext = " AND cscan_scsc_product.scsc_categoryID=$pp_category";
                                }

                                foreach ($a2 as $pp_company => $multiplier) {

                                    $ppctables = 'cscan_panelists_product,cscan_scsc_product';
                                    if ($pp_company != 0) {
                                        $ppctables .= ',cscan_company_product';
                                        $ppctext .= " AND cscan_panelists_product.productID=cscan_company_product.productID AND primary_co=1 AND cscan_company_product.companyID=$pp_company";
                                    }

                                    $ppmv_m = $mail_volume * $multiplier;

                                    ////$ppspend = doSpend($ppmv_m,$document_size_byte);
                                    //echo 'multiplier: '.$multiplier.'<br>';
                                   echo $upd = "UPDATE $ppctables SET ppmv_m=$ppmv_m,ppmv=$ppmv_m,ppmv_onupdate='".$currentdatetime."'
                                    WHERE panelist_id=$panelist_id AND cscan_panelists_product.productID=$productID AND ppdate='$ppdate' AND cscan_panelists_product.productID=cscan_scsc_product.productID AND cscan_scsc_product.scsc_sectorID=$pp_sector AND cscan_scsc_product.scsc_sort=1$ppctext";                               
                                   echo '<br><br>';
                                    //$DRW->query($upd, $DRW_main);
                                }
                            }
                        }
                    }
                    echo 'comp'; die;
                   */
                    if ($mail_volume > 0) {
                        $sqlu = "UPDATE cscan_product_detail SET isDemographic=1 WHERE productID=$productID";
                        $DRW->query($sqlu, $DRW_main);
                    }

                    if (!isset($productArray[$productID])) $productArray[$productID] = 0;
                    $productArray[$productID] += $mail_volume;

                    $s = array_search($productID, $noComboArray);
                    if ($s !== false) {
                        unset($noComboArray[$s]);
                    }
                
                //comment for override emv    
                //}
               
                $multiplier='';
            }
        }
        
        if ($doprint) {
            echo '<div><em>Values Without Multiplier</em></div>';
            foreach ($productArray as $productID => $mail_volume_tot) {
                if ($mail_volume_tot > 0) {
                    $defs = "SELECT SQL_NO_CACHE entryID FROM cscan_product_detail WHERE productID=$productID";
                    $resultD = $DRW->query($defs, $DRW_read2);
                    $dataD = $DRW->fetch_row($resultD);
                    @$DRW->free_result($resultD);

                    echo '<div><strong>' . $dataD[0] . ' (' . $calc_date . ') Mail Volume:</strong> ' . number_format($mail_volume_tot) . '</div>';
                }
            }
        }
    }
    
    function doMailVolume($year, $month, $factor = 1.88, $doprint = false) {
        global $DRW, $DRW_read2, $DRW_main, $DRW_crm;
        $curr_date = date('Y-m');
        $calc_date = $year . '-' . $month;
        $lessthan = false;
        if ($curr_date == $calc_date) {
            $day = (int) date('j');
            if ($day < 7) {
                $lessthan = true;
            }
        }
        $currentdatetime = date('Y-m-d h:i:s');
        $calc_date_range1 = $calc_date . '-01 00:00:00';
        $ctime = strtotime($calc_date_range1);
        $ctime += 2851200; //33 days
        $calc_date_range2 = date('Y-m', $ctime) . '-01 00:00:00';

        $all_total_panelists = 0;
        $all_total_panelists_w = 0;
        $comboArray = array();
        $noComboArray = array();
        $sql_P = "SELECT SQL_NO_CACHE ppage,pincomeID,ppstateID,pp.homeownershipID,ppdate,pp.panelist_id,pp.productID,pgender,ppfico_score,isBiz,mChannelID,delmethid,pweight,mTypeID,parent_panelist_id,document_size_byte
            FROM cscan_product_detail pd JOIN cscan_panelists_product pp ON (pd.productID=pp.productID) JOIN cscan_panelists pa ON (pp.panelist_id=pa.panelist_id AND contactTypeID=2) LEFT JOIN cscan_document d ON (pp.productID=d.productID AND d.document_id=1)
            WHERE productStatus=1 AND (mPanelID=1 OR mPanelID=2) AND ppdate>='$calc_date_range1' AND ppdate<'$calc_date_range2'";

        /* added for running march month data */
        /*
          $sql_P = "SELECT SQL_NO_CACHE ppage,pincomeID,ppstateID,pp.homeownershipID,ppdate,pp.panelist_id,pp.productID,pgender,ppfico_score,isBiz,mChannelID,delmethid,pweight,mTypeID,parent_panelist_id,document_size_byte
          FROM cscan_product_detail pd JOIN cscan_panelists_product pp ON (pd.productID=pp.productID) JOIN cscan_panelists pa ON (pp.panelist_id=pa.panelist_id AND contactTypeID=2) LEFT JOIN cscan_document d ON (pp.productID=d.productID AND d.document_id=1)
          WHERE productStatus=1 AND (mPanelID=1 OR mPanelID=2) AND ppdate>='2018-02-01 00:00:00' AND ppdate<'2018-03-01 00:00:00'";
         */
        /* $sql_P = "SELECT SQL_NO_CACHE ppage,pincomeID,ppstateID,pp.homeownershipID,ppdate,pp.panelist_id,pp.productID,pgender,ppfico_score,isBiz,mChannelID,delmethid,pweight,mTypeID,parent_panelist_id,document_size_byte
          FROM cscan_product_detail pd JOIN cscan_panelists_product pp ON (pd.productID=pp.productID) JOIN cscan_panelists pa ON (pp.panelist_id=pa.panelist_id AND contactTypeID=2) LEFT JOIN cscan_document d ON (pp.productID=d.productID AND d.document_id=1)
          WHERE productStatus=1 AND (mPanelID=1 OR mPanelID=2) AND ppdate>='2016-08-01 00:00:00' AND ppdate<'2016-09-01 00:00:00' AND pd.productID=2317449";
         */
        //AND pd.productID=2317449

        /* End to added for running march month data */

        $result_P = $DRW->query($sql_P, $DRW_read2);
        while ($row = $DRW->fetch_row($result_P)) {
            $p_age = $row[0];
            $p_incomeID = $row[1];
            $p_stateID = $row[2];
            $homeownershipID = $row[3];
            $ppdate = $row[4];
            $panelist_id = $row[5];
            $productID = $row[6];
            $gender = strtoupper(substr(trim($row[7]), 0, 1));
            $ppfico_score = $row[8];
            $isBiz = $row[9];
            $mChannelID = $row[10];
            $delmethid = $row[11];
            $pweight = $row[12];
            $mTypeID = $row[13];
            $parent_panelist_id = $row[14];
            $document_size_byte = (float) $row[15];

            $query_pt = "SELECT ValueScore_for_Household FROM cscan_panelists_appends WHERE panelist_id=$panelist_id";
            $result_pt = $DRW->query($query_pt, $DRW_read2);
            $row_pt = $DRW->fetch_row($result_pt);
            if (!empty($row_pt[0])) {
                $ppfico_score = 1;
            } else {
                $ppfico_score = 0;
            }

            if ($mChannelID != 1 || $delmethid == 4 || $delmethid == 5) { //only direct mail for demographics and no FSI (also in productDetail.php display) // || $parent_panelist_id>0 //and no subpanelists (only here)
                if ($ppfico_score > 0) {
                    $ficosql = ',isFICO=1';
                } else {
                    $ficosql = '';
                }
                $sqlu = "UPDATE cscan_product_detail SET isDemographic=1$ficosql WHERE productID=$productID";
                //$DRW->query($sqlu, $DRW_main);

                //$sqlu = "UPDATE cscan_panelists_product SET ppmv=0,actual_ppmv=0,ppmv_w=0,ppmv_m=0,ppspend=0,ppmv_onupdate='".$currentdatetime."' WHERE panelist_id=$panelist_id AND productID=$productID AND ppdate='$ppdate'";
                //$DRW->query($sqlu, $DRW_main);

                continue;
            } elseif ($ppfico_score > 0) {
                $sqlu = "UPDATE cscan_product_detail SET isFICO=1 WHERE productID=$productID";
               // $DRW->query($sqlu, $DRW_main);
            }

            $regionID = 0;
            if (isset($this->regionArray[$p_stateID]))
                $regionID = $this->regionArray[$p_stateID];

            $ir_ID = 0;
            if (isset($this->incomeArray[$p_incomeID])) {
                foreach ($this->income_rArray as $id => $arry) {
                    if ($this->incomeArray[$p_incomeID][0] >= $arry[0] && $this->incomeArray[$p_incomeID][1] <= $arry[1]) {
                        $ir_ID = $id;
                        break;
                    }
                }
            }
            $ageID = 0;
            foreach ($this->ageArray as $id => $arry) {
                if ($p_age >= $arry[0] && $p_age <= $arry[1]) {
                    $ageID = $id;
                    break;
                }
            }
            $doin = true;
            foreach ($comboArray as $key => $arry) {
                if ($arry[0] == $ageID && $arry[1] == $ir_ID && $arry[2] == $regionID && $arry[3] == $homeownershipID) {
                    $doin = false;
                    $all_total_panelists++;
                    $all_total_panelists_w += $pweight;
                    $comboArray[$key][10][] = array($productID, $panelist_id, $ppdate, $document_size_byte, $p_stateID);
                    $comboArray[$key][11] += $pweight;
                    break;
                }
            }
            if ($doin) {
                $defs = "SELECT SQL_NO_CACHE us_individuals,population_share,age_name,ir_name,region_name,homeownership_name,panelist_share,panelist_count FROM cscan_mv_defs cd, cscan_age ca, cscan_income_report ci, cscan_region cr, cscan_homeownership ch WHERE
                    cd.ageID=$ageID AND cd.ir_ID=$ir_ID AND cd.regionID=$regionID AND cd.homeownershipID=$homeownershipID AND
                    cd.ageID=ca.ageID AND cd.ir_ID=ci.ir_ID AND cd.regionID=cr.regionID AND cd.homeownershipID=ch.homeownershipID";
                $resultD = $DRW->query($defs, $DRW_read2);
                $dataD = $DRW->fetch_row($resultD);
                @$DRW->free_result($resultD);

                if ($dataD[0] != '') {
                    $all_total_panelists++;
                    $comboArray[] = array($ageID, $ir_ID, $regionID, $homeownershipID, $dataD[0], $dataD[1], $dataD[2], $dataD[3], $dataD[4], $dataD[5], array(array($productID, $panelist_id, $ppdate, $document_size_byte, $p_stateID)), $pweight, $dataD[6], $dataD[7]);
                } else {
                    if (!in_array($productID, $noComboArray)) {
                        $noComboArray[] = $productID;
                    }
                }
            }
        }
        @$DRW->free_result($result_P);
        $productArray = array();
        //echo '<pre>';
        //print_r($comboArray);
        //die;
        //echo 'processing..';
        $panelist_share = '';
        foreach ($comboArray as $key => $arry) {
            $ageID = $arry[0];
            $ir_ID = $arry[1];
            $regionID = $arry[2];
            $homeownershipID = $arry[3];
            $us_individuals = $arry[4];
            $population_share = $arry[5];
            $age_name = $arry[6];
            $ir_name = $arry[7];
            $region_name = $arry[8];
            $homeownership_name = $arry[9];
            $ppArray = $arry[10];
            $panelists_w = $arry[11];
            //$panelists = count($ppArray);
            $panelist_share = $arry[12];
            $panelists = $arry[13];


            /*
              if(empty($panelist_share)){
              if ($all_total_panelists > 0) $panelist_share = $panelists / $all_total_panelists;
              else $panelist_share = 0;
              } */

            if ($panelist_share > 0)
                $weight = $population_share / $panelist_share;
            else
                $weight = 0;
            if ($panelists > 0)
                $representation = ($us_individuals / $panelists) * $weight;
            else
                $representation = 0;
            $actual_mail_volume = $mail_volume = $representation * $factor;

            if ($all_total_panelists_w > 0)
                $panelist_share_w = $panelists_w / $all_total_panelists_w;
            else
                $panelist_share_w = 0;
            $ppmv_w = ($panelist_share_w * $us_individuals) / .08;

            if ($lessthan) { //if today is within the first 7 days of the month, use default value
                $mail_volume = self::DEFAULT_MAIL_VOLUME;
            } elseif ($mail_volume > self::MAX_MAIL_VOLUME) {
                $mail_volume = self::MAX_MAIL_VOLUME;
            }
            //echo '<pre>';            
            //print_r($this->countryArray); die;
            foreach ($ppArray as $pp) {
                list($productID, $panelist_id, $ppdate, $document_size_byte, $pp_state) = $pp;
                $pp_country = 1;

                $sql_chk_ppmv = "SELECT panelist_id FROM cscan_panelists_product where panelist_id='" . $panelist_id . "' AND productID='" . $productID . "' AND ppdate='" . $ppdate . "' AND ppmv<=0";
                $result_ppmv = $DRW->query($sql_chk_ppmv, $DRW_read2);
                $dataD_ppmv = $DRW->fetch_row($result_ppmv);
                // Comment for override emv calculation 
                //if (!empty($dataD_ppmv[0])) {
                    //echo $sql_chk_ppmv;
                    //echo '<br><br><br><br>';

                    if (isset($this->countryArray[$pp_state]))
                        $pp_country = $this->countryArray[$pp_state];

                    $ppspend = doSpend($mail_volume, $document_size_byte);
                    /*
                      $upd = "UPDATE cscan_panelists_product SET ppmv=$mail_volume,ageID=$ageID,ir_ID=$ir_ID,regionID=$regionID,actual_ppmv=$actual_mail_volume,ppmv_w=$ppmv_w,ppmv_m=$mail_volume,ppspend=$ppspend,ppmv_onupdate='".$currentdatetime."'
                      WHERE panelist_id=$panelist_id AND productID=$productID AND ppdate='$ppdate'";
                      $DRW->query($upd, $DRW_main);
                     */
                    list($ppy, $ppm) = explode('-', $ppdate);
                    $ppy = (int) $ppy;
                    $ppm = (int) $ppm;
                    $multAs = array();

                    /*
                      if (isset($this->multArray[$pp_country][0][0])) { //all dates
                      $multAs[] = $this->multArray[$pp_country][0][0];
                      }
                      if (isset($this->multArray[$pp_country][0][$ppm])) { //all years this particular month
                      $multAs[] = $this->multArray[$pp_country][0][$ppm];
                      }
                      if (isset($this->multArray[$pp_country][$ppy][0])) { //all months this particular year
                      $multAs[] = $this->multArray[$pp_country][$ppy][0];
                      }
                      if (isset($this->multArray[$pp_country][$ppy][$ppm])) { //this month of this year
                      $multAs[] = $this->multArray[$pp_country][$ppy][$ppm];
                      }
                     */

                    $sector_chk_id = '';
                    $cat_chk_id = '';
                    $company_chk_id = '';
                    $sql_chk_comp = "SELECT SQL_NO_CACHE companyID FROM cscan_company_product where primary_co=1 AND productID='" . $productID . "'";
                    $result_comp = $DRW->query($sql_chk_comp, $DRW_read2);
                    $data_comp = $DRW->fetch_row($result_comp);
                    if (!empty($data_comp[0])) {
                        $company_chk_id = $data_comp[0];
                    }

                    $sql_chk_sc = "SELECT SQL_NO_CACHE scsc_sectorID,scsc_categoryID,scsc_subCategoryID FROM cscan_scsc_product where scsc_sort=1 AND productID='" . $productID . "'";
                    $result_scsc = $DRW->query($sql_chk_sc, $DRW_read2);
                    $data_scsc = $DRW->fetch_assoc($result_scsc);
                    if (!empty($data_scsc['scsc_sectorID'])) {
                        $sector_chk_id = $data_scsc['scsc_sectorID'];
                        $cat_chk_id = $data_scsc['scsc_categoryID'];
                        $subcat_chk_id = $data_scsc['scsc_subCategoryID'];
                    }
                    $multiplier = '';
                    if ($company_chk_id > 0 && $sector_chk_id > 0 && $cat_chk_id > 0 && $subcat_chk_id > 0 && $pp_country > 0) {
                        $sql_chk_mv = "SELECT SQL_NO_CACHE multiplier FROM cscan_mv_multiplier where m_sectorID='" . $sector_chk_id . "' AND m_categoryID='" . $cat_chk_id . "' AND m_subcategoryID='" . $subcat_chk_id . "' AND m_companyID='" . $company_chk_id . "' AND m_countryID='" . $pp_country . "' limit 0,1";
                        $result_mv = $DRW->query($sql_chk_mv, $DRW_read2);
                        $data_multiplier = $DRW->fetch_assoc($result_mv);
                        if (!empty($data_multiplier['multiplier'])) {
                            $multiplier = $data_multiplier['multiplier'];
                        }
                    }
                    if ($multiplier == '' && $company_chk_id > 0 && $sector_chk_id > 0 && $cat_chk_id > 0 && $pp_country > 0) {
                        $sql_chk_mv = "SELECT SQL_NO_CACHE multiplier FROM cscan_mv_multiplier where m_sectorID='" . $sector_chk_id . "' AND m_categoryID='" . $cat_chk_id . "' AND m_companyID='" . $company_chk_id . "' AND m_countryID='" . $pp_country . "' limit 0,1";
                        $result_mv = $DRW->query($sql_chk_mv, $DRW_read2);
                        $data_multiplier2 = $DRW->fetch_assoc($result_mv);
                        if (!empty($data_multiplier2['multiplier'])) {
                            $multiplier = $data_multiplier2['multiplier'];
                        }
                    }
                    if ($multiplier == '' && $company_chk_id > 0 && $sector_chk_id > 0 && $pp_country > 0) {
                        $sql_chk_mv = "SELECT SQL_NO_CACHE multiplier FROM cscan_mv_multiplier where m_sectorID='" . $sector_chk_id . "' AND m_companyID='" . $company_chk_id . "' AND m_countryID='" . $pp_country . "' limit 0,1";
                        $result_mv = $DRW->query($sql_chk_mv, $DRW_read2);
                        $data_multiplier3 = $DRW->fetch_assoc($result_mv);
                        if (!empty($data_multiplier3['multiplier'])) {

                            $multiplier = $data_multiplier3['multiplier'];
                        }
                    }

                    if ($multiplier == '' && $sector_chk_id > 0 && $cat_chk_id > 0 && $subcat_chk_id > 0 && $pp_country > 0) {
                        $sql_chk_mv = "SELECT SQL_NO_CACHE multiplier FROM cscan_mv_multiplier where m_sectorID='" . $sector_chk_id . "' AND m_categoryID='" . $cat_chk_id . "' AND m_subcategoryID='" . $subcat_chk_id . "' AND m_countryID='" . $pp_country . "' limit 0,1";
                        $result_mv = $DRW->query($sql_chk_mv, $DRW_read2);
                        $data_multiplier4 = $DRW->fetch_assoc($result_mv);
                        if (!empty($data_multiplier4['multiplier'])) {

                            $multiplier = $data_multiplier4['multiplier'];
                        }
                    }

                    if ($multiplier == '' && $sector_chk_id > 0 && $cat_chk_id > 0 && $pp_country > 0) {
                        $sql_chk_mv = "SELECT SQL_NO_CACHE multiplier FROM cscan_mv_multiplier where m_sectorID='" . $sector_chk_id . "' AND m_categoryID='" . $cat_chk_id . "' AND m_countryID='" . $pp_country . "' limit 0,1";
                        $result_mv = $DRW->query($sql_chk_mv, $DRW_read2);
                        $data_multiplier5 = $DRW->fetch_assoc($result_mv);
                        if (!empty($data_multiplier5['multiplier'])) {

                            $multiplier = $data_multiplier5['multiplier'];
                        }
                    }
                    if ($multiplier == '' && $cat_chk_id > 0 && $subcat_chk_id > 0 && $pp_country > 0) {
                        $sql_chk_mv = "SELECT SQL_NO_CACHE multiplier FROM cscan_mv_multiplier where m_categoryID='" . $cat_chk_id . "' AND m_subcategoryID='" . $subcat_chk_id . "' AND m_countryID='" . $pp_country . "' limit 0,1";
                        $result_mv = $DRW->query($sql_chk_mv, $DRW_read2);
                        $data_multiplier8 = $DRW->fetch_assoc($result_mv);
                        if (!empty($data_multiplier8['multiplier'])) {
                            $multiplier = $data_multiplier8['multiplier'];
                        }
                    }
                    if ($multiplier == '' && $cat_chk_id > 0 && $pp_country > 0) {
                        $sql_chk_mv = "SELECT SQL_NO_CACHE multiplier FROM cscan_mv_multiplier where m_categoryID='" . $cat_chk_id . "' AND m_countryID='" . $pp_country . "' limit 0,1";
                        $result_mv = $DRW->query($sql_chk_mv, $DRW_read2);
                        $data_multiplier6 = $DRW->fetch_assoc($result_mv);
                        if (!empty($data_multiplier6['multiplier'])) {

                            $multiplier = $data_multiplier6['multiplier'];
                        }
                    }
                    if ($multiplier == '' && $sector_chk_id > 0 && $pp_country > 0) {
                        $sql_chk_mv = "SELECT SQL_NO_CACHE multiplier FROM cscan_mv_multiplier where m_sectorID='" . $sector_chk_id . "' AND m_countryID='" . $pp_country . "' limit 0,1";
                        $result_mv = $DRW->query($sql_chk_mv, $DRW_read2);
                        $data_multiplier7 = $DRW->fetch_assoc($result_mv);
                        if (!empty($data_multiplier7['multiplier'])) {
                            $multiplier = $data_multiplier7['multiplier'];
                        }
                    }
                    if ($multiplier == '') {
                        $multiplier = 1;
                    }

                    //                if($panelist_id=='6898' AND $productID=='3901444'){
                    //                    echo 'mail_volume: '.$mail_volume.' multiplier: '.$multiplier;
                    //                    die;
                    //                }
                    $mail_volume_save = $mail_volume * $multiplier;
                    $mail_volume_save = round($mail_volume_save);                    
                    
                    $allproduct=array(6370142,6736592,6736596,6155395,6421218,6195683,6313959,6327693,6223842,6462429,6364669,6364667,6391654,6661237,6738003,6737962,6736658,6360723,6439677,6391898,6453964,6397843,6471325,6535690,6593050,6613990,6617396,6680323,6680179,6711635,6715188,6612709,6728340,6711292,6602724,6453317,6454039,6458285,6458048,6462397,6462425,6461542,6467711,6476148,6476413,6475404,6480381,6484511,6410944,6415516,6454549,6453673,6453285,6462426,6479773,6484162,6484685,6484766,6490475,6521255,6641669,6675367,6680582,6680488,6697959,6706871,6715874,6723999,6728549,6728132,6736573,6736506,6743799,6487016,6488475,6486541,6696999,6491121,6543248,6552067,6597834,6491105,6499556,6560373,6593083,6706699,6494652,6499105,6498677,6503134,6503129,6614322,6673759,6536262,6494524,6528852,6528674,6688637,6706936,6631347,6673015,6693421,6471739,6491643,6543598,6532456,6669060,6665133,6688731,6651054,6532400,6576448,6424814,6536311,6692629,6698001,6696969,6737152,6457638,6728320,6673877,6689214,6697758,6571645,6587952,6622879,6673864,6680771,6684247,6697998,6471341,6471949,6471639,6471281,6560281,6571775,6579694,6592910,6673256,6674526,6665251,6697000,6697295,6697592,6707175,6480804,6480139,6480866,6479896,6480110,6480162,6484150,6485206,6483758,6480802,6468008,6486459,6471627,6498641,6499266,6503781,6512445,6532064,6539771,6556385,6560710,6593575,6592853,6612588,6614185,6626972,6631896,6685183,6689183,6689380,6693209,6703075,6702563,6490754,6484525,6525217,6486505,6486620,6486488,6743671,6512254,6580564,6580555,6579784,6580169,6587982,6673259,6491286,6490443,6491569,6490566,6490499,6490963,6491375,6490543,6491247,6607779,6673310,6680494,6696997,6728930,6593367,6597542,6636787,6646144,6524705,6543090,6560835,6572189,6572221,6575918,6637138,6641120,6641875,6680735,6688929,6688931,6692361,6693456,6697917,6698716,6703515,6707176,6479938,6480821,6476579,6458564,6517102,6512969,6603030,6622838,6637005,6702537,6707426,6512427,6597552,6597944,6602420,6607351,6606349,6607995,6614098,6602340,6627815,6627374,6631051,6654084,6673252,6673319,6673326,6675019,6743486,6494478,6626662,6532335,6607647,6612533,6736555,6571907,6618322,6618631,6641972,6736564,6738086,6602967,6669127,6685112,6702889,6702879,6684265,6715889,6707572,6685270,6697839,6728534,6738097,6680021,6684954,6696828,6602122,6627521,6641104,6685023,6674808,6641996,6646433,6564640,6660867,6732715,6737115,6669464,6669610,6744118,6660284,6674137,6674885,6680887,6702511,6732839,6743996,6743676,6744045,6762328,6689228,6744003,6668845,6680280,6688890,6689657,6737846,6744126,6744304,6737939,6680640,6688684,6743527,6641773,6723929,6728853,6743635,6744129,6744002,6744211,6743201,6744236,6661135,6669469,6673273,6680686,6743350,6736947,6697451,6743936,6744127,6743934,6697649,6723927,6743802,6743712,6743695,6680241,6697351,6696947,6703447,6707523,6711887,6711598,6723983,6728802,6743626,6743451,6732736,6688926,6715892,6723856,6743821);
                    if(in_array($productID, $allproduct)){
                        $upd = "UPDATE cscan_panelists_product SET ppmv=$mail_volume_save,ageID=$ageID,ir_ID=$ir_ID,regionID=$regionID,actual_ppmv=$actual_mail_volume,ppmv_w=$ppmv_w,ppmv_m=$mail_volume_save,ppspend=$ppspend,ppmv_onupdate='" . $currentdatetime . "'
                          WHERE panelist_id=$panelist_id AND productID=$productID AND ppdate='" . $ppdate . "'";

                        $DRW->query($upd, $DRW_main);
                    

                    //echo '<br />';
                    /*
                      foreach ($multAs as $multA) {
                      foreach ($multA as $pp_sector => $a) {
                      foreach ($a as $pp_category => $a2) {
                      if ($pp_category == 0) {
                      $ppctext = '';
                      } else {
                      $ppctext = " AND cscan_scsc_product.scsc_categoryID=$pp_category";
                      }

                      foreach ($a2 as $pp_company => $multiplier) {

                      $ppctables = 'cscan_panelists_product,cscan_scsc_product';
                      if ($pp_company != 0) {
                      $ppctables .= ',cscan_company_product';
                      $ppctext .= " AND cscan_panelists_product.productID=cscan_company_product.productID AND primary_co=1 AND cscan_company_product.companyID=$pp_company";
                      }

                      $ppmv_m = $mail_volume * $multiplier;

                      ////$ppspend = doSpend($ppmv_m,$document_size_byte);
                      //echo 'multiplier: '.$multiplier.'<br>';
                      echo $upd = "UPDATE $ppctables SET ppmv_m=$ppmv_m,ppmv=$ppmv_m,ppmv_onupdate='".$currentdatetime."'
                      WHERE panelist_id=$panelist_id AND cscan_panelists_product.productID=$productID AND ppdate='$ppdate' AND cscan_panelists_product.productID=cscan_scsc_product.productID AND cscan_scsc_product.scsc_sectorID=$pp_sector AND cscan_scsc_product.scsc_sort=1$ppctext";
                      echo '<br><br>';
                      //$DRW->query($upd, $DRW_main);
                      }
                      }
                      }
                      }
                      echo 'comp'; die;
                     */
                        if ($mail_volume > 0) {
                            $sqlu = "UPDATE cscan_product_detail SET isDemographic=1 WHERE productID=$productID";
                            $DRW->query($sqlu, $DRW_main);
                        }
                    }

                    if (!isset($productArray[$productID]))
                        $productArray[$productID] = 0;
                    $productArray[$productID] += $mail_volume;

                    $s = array_search($productID, $noComboArray);
                    if ($s !== false) {
                        unset($noComboArray[$s]);
                    }
                //}

                $multiplier = '';
            }
        }

        if ($doprint) {
            echo '<div><em>Values Without Multiplier</em></div>';
            foreach ($productArray as $productID => $mail_volume_tot) {
                if ($mail_volume_tot > 0) {
                    $defs = "SELECT SQL_NO_CACHE entryID FROM cscan_product_detail WHERE productID=$productID";
                    $resultD = $DRW->query($defs, $DRW_read2);
                    $dataD = $DRW->fetch_row($resultD);
                    @$DRW->free_result($resultD);

                    echo '<div><strong>' . $dataD[0] . ' (' . $calc_date . ') Mail Volume:</strong> ' . number_format($mail_volume_tot) . '</div>';
                }
            }
        }
    }

}
?>