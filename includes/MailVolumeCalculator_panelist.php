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

    function doMailVolume($calc_date_range1,$calc_date_range2,$i)
    {
        global $DRW, $DRW_read2, $DRW_main, $DRW_crm;
                
        $currentdatetime=date('Y-m-d h:i:s');        
        $all_total_panelists = 0;
        $all_total_panelists_w = 0;
        $comboArray = array();
        $noComboArray = array();        
        $total_panelists_count=array();
        
        $sql_P = "SELECT SQL_NO_CACHE ppage,pincomeID,ppstateID,pp.homeownershipID,ppdate,pp.panelist_id,pp.productID,pgender,ppfico_score,isBiz,mChannelID,delmethid,pweight,mTypeID,parent_panelist_id,document_size_byte
            FROM cscan_product_detail pd JOIN cscan_panelists_product pp ON (pd.productID=pp.productID) JOIN cscan_panelists pa ON (pp.panelist_id=pa.panelist_id AND contactTypeID=2) LEFT JOIN cscan_document d ON (pp.productID=d.productID AND d.document_id=1)
            WHERE productStatus=1 AND (mPanelID=1 OR mPanelID=2) AND ppdate>='$calc_date_range1' AND ppdate<'$calc_date_range2'";
        
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
            if ($mChannelID != 1 || $delmethid == 4 || $delmethid == 5) { //only direct mail for demographics and no FSI (also in productDetail.php display) // || $parent_panelist_id>0 //and no subpanelists (only here)
                
                continue;
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
                    //$total_panelists_count[]=$panelist_id;
                    $all_total_panelists_w += $pweight;
                    //$comboArray[$key][4][] = array($productID);
                    $comboArray[$key][4][] = array($panelist_id);
                    
                    $comboArray[$key][5] += $pweight;
                    break;
                }
            }
            if ($doin) {
                $defs = "SELECT SQL_NO_CACHE us_individuals,population_share,age_name,ir_name,region_name,homeownership_name,panelist_share FROM cscan_mv_defs cd, cscan_age ca, cscan_income_report ci, cscan_region cr, cscan_homeownership ch WHERE
                    cd.ageID=$ageID AND cd.ir_ID=$ir_ID AND cd.regionID=$regionID AND cd.homeownershipID=$homeownershipID AND
                    cd.ageID=ca.ageID AND cd.ir_ID=ci.ir_ID AND cd.regionID=cr.regionID AND cd.homeownershipID=ch.homeownershipID";
                $resultD = $DRW->query($defs, $DRW_read2);
                $dataD = $DRW->fetch_row($resultD);
                @$DRW->free_result($resultD);

                if ($dataD[0] != '') {
                    $all_total_panelists++;
                    //$total_panelists_count[]=$panelist_id;
                    $comboArray[] = array($ageID, $ir_ID, $regionID, $homeownershipID, array(array($panelist_id)), $pweight);
                } 
            }
        }
        
        $productArray = array(); 
        //echo '<pre>';
       //print_r($comboArray);        
        //die;       
        $panelist_share='';        
        $panelists_average='';
        $panelists='';
        $p=1;
        foreach ($comboArray as $key => $arry) {
            $ageID = $arry[0];
            $ir_ID = $arry[1];
            $regionID = $arry[2];
            $homeownershipID = $arry[3];
            
            
            $ppArray = $arry[4];
            $panelists_w = $arry[5];
            //$panelists = count($ppArray);
            //print_r($ppArray);
            $panelists=count(array_unique(array_reduce($ppArray, 'array_merge', array())));
            //$all_total_panelists=count(array_unique($total_panelists_count));            

            if($i==3){                
                $upd="UPDATE cscan_mv_defs SET first_month_panelist='".$panelists."'
                    WHERE ageID=$ageID AND ir_ID=$ir_ID AND regionID=$regionID AND homeownershipID=$homeownershipID";
            }else if($i==2){
                $upd="UPDATE cscan_mv_defs SET second_month_panelist='".$panelists."'
                    WHERE ageID=$ageID AND ir_ID=$ir_ID AND regionID=$regionID AND homeownershipID=$homeownershipID";
            }else{
              $upd="UPDATE cscan_mv_defs SET third_month_panelist='".$panelists."'
                    WHERE ageID=$ageID AND ir_ID=$ir_ID AND regionID=$regionID AND homeownershipID=$homeownershipID";  
            }         
           // echo $upd = "UPDATE cscan_mv_defs SET panelist_share='".$panelist_share."',panelist_count='".$panelists_average."'
            //        WHERE ageID=$ageID AND ir_ID=$ir_ID AND regionID=$regionID AND homeownershipID=$homeownershipID";            
            //echo $upd; die;
            $DRW->query($upd, $DRW_main);
            
            $panelists_average='';
            $panelists='';
            $all_total_panelists='';
            //die;
            
        }        
      
    }

    function doPanelistAvgCount(){
        global $DRW, $DRW_read2, $DRW_main, $DRW_crm;
        
        // $sql_P = "SELECT SQL_NO_CACHE ageID,ir_ID,regionID,homeownershipID,first_month_panelist,first_month_total,second_month_panelist,second_month_total,third_month_panelist,third_month_total
        //  FROM cscan_mv_defs WHERE first_month_panelist>0 OR second_month_panelist>0 OR third_month_panelist>0";       
        $upd_avg_panelist = "UPDATE cscan_mv_defs SET panelist_count='1' WHERE panelist_count<=0";            
            $DRW->query($upd_avg_panelist, $DRW_main);
                
        $sql_P = "SELECT SQL_NO_CACHE ageID,ir_ID,regionID,homeownershipID,first_month_panelist,second_month_panelist,third_month_panelist
            FROM cscan_mv_defs";
        
        $result_P = $DRW->query($sql_P, $DRW_read2);
        $panelists_average='';
        //$all_total_panelists_average='';
         $panelist_share='';
        while ($row = $DRW->fetch_row($result_P)) {
            $ageID= $row[0];
            $ir_ID= $row[1];
            $regionID=$row[2];
            $homeownershipID=$row[3];
            $first_month_panelist=$row[4];            
            $second_month_panelist=$row[5];            
            $third_month_panelist=$row[6];
            
            $panelists_average = ceil(($first_month_panelist+$second_month_panelist+$third_month_panelist)/3);
            //$all_total_panelists_average = ceil(($first_month_total+$second_month_total+$third_month_total)/3);
            
//            if ($all_total_panelists_average > 0) $panelist_share = $panelists_average / $all_total_panelists_average;
//             else $panelist_share = 0;
            
            //$upd = "UPDATE cscan_mv_defs SET panelist_share='".$panelist_share."',panelist_count='".$panelists_average."'
            //        WHERE ageID=$ageID AND ir_ID=$ir_ID AND regionID=$regionID AND homeownershipID=$homeownershipID";            
            if($panelists_average>0){
                $upd = "UPDATE cscan_mv_defs SET panelist_count='".$panelists_average."'
                       WHERE ageID=$ageID AND ir_ID=$ir_ID AND regionID=$regionID AND homeownershipID=$homeownershipID";            
                $DRW->query($upd, $DRW_main);
                $panelists_average='';
                //$all_total_panelists_average='';
                //$panelist_share='';
            }
             
        }
    }
    
    function doPanelistShare(){
        global $DRW, $DRW_read2, $DRW_main, $DRW_crm;        
                     
        $sql_pan_count = "SELECT SQL_NO_CACHE SUM(panelist_count) FROM cscan_mv_defs where regionID<=4"; 
        $resultC = $DRW->query($sql_pan_count, $DRW_read2);
        $dataC = $DRW->fetch_row($resultC);
        $us_total_pan=$dataC[0];
        
        $sql_pan_count2 = "SELECT SQL_NO_CACHE SUM(panelist_count) FROM cscan_mv_defs where regionID>4"; 
        $resultC2 = $DRW->query($sql_pan_count2, $DRW_read2);
        $dataC2 = $DRW->fetch_row($resultC2);
        $canada_total_pan=$dataC2[0];
        
        $sql_us_P = "SELECT SQL_NO_CACHE ageID,ir_ID,regionID,homeownershipID,panelist_count FROM cscan_mv_defs where regionID<=4";
        $result_us_P = $DRW->query($sql_us_P, $DRW_read2);        
        $panelist_count=''; 
        $panelist_share='';
        
        while ($row = $DRW->fetch_row($result_us_P)) {
            $ageID= $row[0];
            $ir_ID= $row[1];
            $regionID=$row[2];
            $homeownershipID=$row[3];
            $panelist_count=$row[4];
            $panelist_share = $panelist_count / $us_total_pan;
                        
            $upd = "UPDATE cscan_mv_defs SET panelist_share='".$panelist_share."'
                   WHERE ageID=$ageID AND ir_ID=$ir_ID AND regionID=$regionID AND homeownershipID=$homeownershipID";            
            $DRW->query($upd, $DRW_main);
            $panelist_count=''; 
            $panelist_share='';            
             
        }
        
        $sql_canada_P = "SELECT SQL_NO_CACHE ageID,ir_ID,regionID,homeownershipID,panelist_count FROM cscan_mv_defs where regionID>4";
        $result_canada_P = $DRW->query($sql_canada_P, $DRW_read2);        
        $panelist_count_canada=''; 
        $panelist_share_canada='';
        
        while ($row = $DRW->fetch_row($result_canada_P)) {
            $ageID= $row[0];
            $ir_ID= $row[1];
            $regionID=$row[2];
            $homeownershipID=$row[3];
            $panelist_count_canada=$row[4];
            $panelist_share_canada = $panelist_count_canada / $canada_total_pan;
                        
            $upd = "UPDATE cscan_mv_defs SET panelist_share='".$panelist_share_canada."'
                   WHERE ageID=$ageID AND ir_ID=$ir_ID AND regionID=$regionID AND homeownershipID=$homeownershipID";            
            $DRW->query($upd, $DRW_main);
            $panelist_count_canada=''; 
            $panelist_share_canada='';            
             
        }
    }
}
?>