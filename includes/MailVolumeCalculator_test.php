<?php

require_once 'dbcon.php';
require_once 'functions.php';

class MailVolumeCalculator {

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

    function doPreMailVolume() {
        global $DRW, $DRW_read2, $DRW_main, $DRW_crm;
        $sqlu = "UPDATE cscan_product_detail SET isDemographic=2 WHERE isDemographic=1";
        $DRW->query($sqlu, $DRW_main);
        $sqlu = "UPDATE cscan_product_detail SET isFICO=2 WHERE isFICO=1";
        $DRW->query($sqlu, $DRW_main);
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
        
        $update_productID=array(3946779,3975959,4503901,4785383,4791267,4895411,4847404,4849987,4930728,4949806,4954045,4969716,4969198,4968638,4969995,5004568,5125567,4973607,4978050,4977154,5492295,4981702,4981447,4980944,4984955,4991157,4991742,4991092,5064498,5101360,5002060,5079544,5079546,5509792,4998049,4998514,4997137,4997767,4997303,5082817,5002498,5002133,5001263,5005210,5417125,5004752,5005243,5017307,5319519,5031958,5043978,5043967,5067861,5074748,5013694,5013708,5013744,5018136,5016676,5017418,5017337,5016424,5016620,5017896,5022934,5022486,5022330,5022687,5022681,5021621,5021298,5022157,5026128,5025608,5024609,5024525,5024690,5024682,5032189,5032053,5134835,5037515,5036669,5037476,5037234,5040803,5040808,5040472,5337618,5043540,5043823,5046699,5053188,5053540,5052987,5052706,5056325,5056969,5056968,5057077,5057245,5402373,5402598,5402371,5516039,5516044,5559077,5061310,5061403,5065421,5064046,5067208,5068333,5530530,5075149,5074811,5073919,5074856,5074190,5078957,5079099,5078812,5428045,5082650,5082534,5083413,5097483,5087295,5087092,5086886,5086566,5117916,5125560,5291808,5090108,5091027,5090296,5091031,5090840,5090438,5319501,5379422,5402812,5128453,5078378,5098499,5286048,5097143,5097065,5098159,5096795,5097863,5097878,5097837,5101448,5155203,5306562,5220962,5182532,5104993,5465445,5108043,5108215,5108290,5108251,5107966,5146175,5337302,5119652,5118888,5118385,5119535,5118529,5117903,5117871,5118186,5117525,5119647,5442645,5125617,5124174,5122793,5122834,5122739,5128981,5128435,5128864,5128746,5128270,5128081,5127808,5127162,5343522,5132006,5132341,5132464,5156500,5135913,5135960,5136014,5135648,5134315,5135123,5134790,5134820,5142186,5142450,5142409,5142649,5142579,5147620,5146807,5147972,5441079,5152490,5151368,5151516,5151423,5151422,5151613,5152334,5151739,5150722,5572172,5155211,5155530,5155340,5156186,5179339,5299933,5158425,5158654,5403923,5159353,5166457,5166429,5166050,5165525,5166390,5165582,5166786,5170640,5170430,5171275,5171220,5170434,5303671,5314670,5315043,5315051,5379964,5400988,5412528,5410709,5422296,5175345,5174103,5319640,5178964,5179354,5179110,5304762,5337589,5182265,5182112,5182203,5296665,5295846,5319635,5319731,5379436,5388598,5517328,5188755,5188852,5188317,5188542,5188471,5188673,5323692,5306221,5314998,5326985,5342315,5392973,5412658,5412660,5411647,5409332,5462430,5501711,5195564,5202843,5343170,5199048,5198417,5198672,5198843,5198911,5199028,5202974,5202322,5202417,5202437,5182561,5170435,5174146,5203385,5203609,5318966,5210662,5209257,5209535,5210769,5210693,5209614,5175322,5403332,5214598,5214370,5214745,5214497,5216086,5215401,5215529,5300019,5221154,5221330,5221265,5220694,5303818,5292685,5227738,5227072,5227108,5296404,5304758,5314914,5323092,5403095,5433914,5442772,5231071,5231292,5231455,5328926,5379349,5235664,5235508,5235714,5235248,5235153,5235386,5235390,5235846,5235154,5442453,5520258,5239459,5241165,5239521,5248483,5441956,5241744,5241264,5241616,5241751,5241792,5241478,5241378,5343239,5510938,5210346,5249453,5257151,5261492,5279548,5306917,5307389,5307020);
        $update_panelistID=array(69,152,185,20699,241,9212,9140,304,331,338,1351,57362,9203,9852,1170,1129,1208,1651,503,502,487,523,598,570,57509,1700,1766,17565,1669,1627,10062,1576,833,17866,9820,1403,18399,1479,9699,356,1697,9302,1709,14729,14816,14893,14923,14941,18393,15302,15347,15396,15451,15467,15477,15480,15185,15568,15608,15617,15628,15686,15779,15884,15911,510,16026,16058,16100,16134,16144,16181,16182,16346,17666,16407,16478,17638,16498,17623,16591,16592,16594,16741,16822,16848,16896,55552,16982,17026,17606,17044,17045,20335,17252,17279,17292,17515,17517,17574,17696,17793,17941,17975,18006,18086,18116,18837,18135,20634,18136,18142,18735,18428,18510,18516,54969,18577,18578,18793,18819,9147,19693,19775,19797,19798,19809,19938,19948,19961,20032,20250,20325,20430,20499,20503,20530,20578,20834,20849,20917,21018,21049,21150,21174,2056,21259,21300,21379,21392,21430,9305,21488,21489,21592,21613,21641,26834,21810,21820,56037,21829,21853,21859,21866,21903,55511,57067,22130,22242,22268,22269,22321,22336,22347,22373,22385,22390,22423,22481,22482,9799,22531,22678,22775,22794,22937,22962,22963,23003,23218,23219,23233,23300,23306,23333,23426,23667,23711,23787,23788,23800,23876,23914,24043,24103,24180,24228,24323,24554,24610,24625,24690,57874,24731,2288,24952,2296,56518,25196,25258,25269,25331,25372,25531,25588,57264,25820,25902,25947,25993,26103,66146,26182,26193,26343,2385,2404,26786,9239,2448,2503,2506,2524,28547,28548,28674,16531,9101,9248,29380,29405,2589,17951,18636,18637,30477,30698,2676,31374,31376,31377,9830,9024,32005,2754,2760,12067,32502,2815,18098,2855,3134,60490,34565,34566,34641,34642,3214,35100,35559,35620,35687,37396,37854,3422,38195,3426,3444,9869,3492,3494,39104,39105,3499,39731,39987,40360,3598,41054,41222,58147,3750,3791,3796,42632,42646,3847,3865,3891,45303,4026,4029,10057,45689,45690,46061,48161,48470,49329,49576,4354,4356,49889,10101,4426,51437,51590,4515,52382,52835,4588,55228,55594,55671,55679,55718,5117,5120,10406,56013,56059,56128,56145,56185,57514,56283,56301,56355,56502,56615,56731,56735,56979,57244,57245,57259,5519,57336,58230,58232,57600,57608,57685,58093,57845,57846,57862,57900,57915,57953,58063,58130,58150,58153,58190,58295,58296,58308,58455,58464,58567,58641,58924,59078,59111,59195,59356,70104,59376,59478,59541,59550,59848,60079,60145,5724,60458,60579,60637,60626,61048,60784,61052,60781,5795,5800,5816,61254,61289,61292,61369,61387,5831,10086,61702,61410,9057,62869,61606,61607,61888,62121,62140,62160,5898,5908,62391,62487,62686,62688,62737,5957,17803,62940,63179,63237,63255,63232,9811,63444,6019,63436,63498,6033,64001,63668,63716,63691,63708,63777,63848,63897,63945,63944,64104,64120,6108,6117,64536,6131,64747,64782,64826,65894,66230,65027,6161,65314,65294,65331,65335,65349,65377,65501,65536,65601,65656,65618,65621,6216,65808,66099,6235,66235,6242,66516,66770,6275,66882,66884,66898,9308,18646,10148,67159,67210,67248,67284,67305,67373,67377,6393,67490,67549,67548,67566,67734,67756,67728,67857,67859,68159,68151,68233,68317,6479,68275,68365,68392,68424,68396,68439,68434,6495,9066,68441,68613,68530,68647,68694,68795,68768,68862,68854,68833,68917,68925,68938,6543,69171,69211,17913,69226,69261,69327,69313,69351,69410,18337,6695,9736,10198,9689,6787,6794,6804,6862,6879,10402,6888,6898,17446,6983,7003,10203,7033,7051,7063,7065,7131,56608,10220,7254,58534,7304,9344,18553,9828,7610,17967,7705,9898,7714,7802,7834,7849,7946,8068,8123,9135,66232,20752,8099,9233,9701,8650,8682,8697,19915,8762,18332,8807,8970,18106,12080,17899,10750,10322,10337,10344,10356,10273,10388,10475,20562,10718,10581,10628,17198,10656,10657,10666,17323,17426,10672,10731,10880,10904,10905,10915,18283,10917,11114,11115,11243,11252,11291,12116,11317,11354,11408,65895,11500,11604,11619,11700,11785,11786,11900,12015,12016,12057,12058,17994,18111,12175,12348,20415,12381,12448,12454,12605,12632,17406,12657,12695,12701,12962,13018,13023,13137,13244,13268,13378,13636,18295,13714,13799,13918,13921,13951,14053,14087,14106,14278,14279,14340,14454,14486,14487,69813,14488,14491);
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
                if(in_array($productID, $update_productID)){
                    $sqlu = "UPDATE cscan_product_detail SET isDemographic=1$ficosql WHERE productID=$productID";
                    $DRW->query($sqlu, $DRW_main);
                }

                //$sqlu = "UPDATE cscan_panelists_product SET ppmv=0,actual_ppmv=0,ppmv_w=0,ppmv_m=0,ppspend=0,ppmv_onupdate='".$currentdatetime."' WHERE panelist_id=$panelist_id AND productID=$productID AND ppdate='$ppdate'";
                //$DRW->query($sqlu, $DRW_main);

                continue;
            } elseif ($ppfico_score > 0) {
                 if(in_array($productID, $update_productID)){
                    $sqlu = "UPDATE cscan_product_detail SET isFICO=1 WHERE productID=$productID";
                    $DRW->query($sqlu, $DRW_main);
                 }
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
                    
                    if(in_array($productID, $update_productID) AND (in_array($panelist_id, $update_panelistID))){

                        $upd = "UPDATE cscan_panelists_product SET ppmv=$mail_volume_save,ageID=$ageID,ir_ID=$ir_ID,regionID=$regionID,actual_ppmv=$actual_mail_volume,ppmv_w=$ppmv_w,ppmv_m=$mail_volume_save,ppspend=$ppspend,ppmv_onupdate='" . $currentdatetime . "'
                          WHERE panelist_id=$panelist_id AND productID=$productID AND ppdate='" . $ppdate . "'";

                        $DRW->query($upd, $DRW_main);
                    }

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
                        if(in_array($productID, $update_productID)){
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
               // }

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

    
    
    
    
    function doPostMailVolume() {
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
    function getMultipliersArray() {
        global $DRW, $DRW_read2;
        $multArray = array();
        $check = "SELECT multiplier,m_year,m_month,m_sectorID,m_categoryID,m_companyID,m_countryID FROM cscan_mv_multiplier";
        $result = $DRW->query($check, $DRW_read2);
        while ($data = $DRW->fetch_row($result)) {
            $m_countryID = (int) $data[6];
            $m_year = (int) $data[1];
            $m_month = (int) $data[2];
            $m_sectorID = (int) $data[3];
            $m_categoryID = (int) $data[4];
            $m_companyID = (int) $data[5];

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

    function getRegionArray() {
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

    function getIncomeReportArray() {
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

    function getIncomeArray() {
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

    function getAgeArray() {
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

    function getCountryArray() {
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

    function doMailVolume_test($year, $month, $factor = 1.88, $doprint = false) {
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
        $updt_prod_id=array(4444290,4478316,4510175,4658592,4658791,4657999,4657591,4656606,4585074,4602141,4601521,4666226,4646683,4639456,4639455,4639847,4646957,4658462,4658413,4658185,4658932,4657893,4657861,4657798,4663183,4662655,4663272,4665737,4676777,4658499,4682904,4687375);
        
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
                if (in_array($productID, $updt_prod_id))
                {                
                $sqlu = "UPDATE cscan_product_detail SET isDemographic=1$ficosql WHERE productID=$productID";
                $DRW->query($sqlu, $DRW_main);
               
                }

                //$sqlu = "UPDATE cscan_panelists_product SET ppmv=0,actual_ppmv=0,ppmv_w=0,ppmv_m=0,ppspend=0,ppmv_onupdate='".$currentdatetime."' WHERE panelist_id=$panelist_id AND productID=$productID AND ppdate='$ppdate'";
                //$DRW->query($sqlu, $DRW_main);

                continue;
            } elseif ($ppfico_score > 0) {
                if (in_array($productID, $updt_prod_id))
                { 
                $sqlu = "UPDATE cscan_product_detail SET isFICO=1 WHERE productID=$productID";
                $DRW->query($sqlu, $DRW_main);
                }
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

                //$sql_chk_ppmv = "SELECT panelist_id FROM cscan_panelists_product where panelist_id='" . $panelist_id . "' AND productID='" . $productID . "' AND ppdate='" . $ppdate . "' AND ppmv<=0";
                $sql_chk_ppmv = "SELECT panelist_id FROM cscan_panelists_product where panelist_id='" . $panelist_id . "' AND productID='" . $productID . "' AND ppdate='" . $ppdate . "' AND productID IN (4444290,4478316,4510175,4658592,4658791,4657999,4657591,4656606,4585074,4602141,4601521,4666226,4646683,4639456,4639455,4639847,4646957,4658462,4658413,4658185,4658932,4657893,4657861,4657798,4663183,4662655,4663272,4665737,4676777,4658499,4682904,4687375)";
                
                $result_ppmv = $DRW->query($sql_chk_ppmv, $DRW_read2);
                $dataD_ppmv = $DRW->fetch_row($result_ppmv);
                // Comment for override emv calculation 
                if (!empty($dataD_ppmv[0])) {
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

                  echo  $upd = "UPDATE cscan_panelists_product SET ppmv=$mail_volume_save,ageID=$ageID,ir_ID=$ir_ID,regionID=$regionID,actual_ppmv=$actual_mail_volume,ppmv_w=$ppmv_w,ppmv_m=$mail_volume_save,ppspend=$ppspend,ppmv_onupdate='" . $currentdatetime . "'
                      WHERE panelist_id=$panelist_id AND productID=$productID AND ppdate='" . $ppdate . "'";

                    $DRW->query($upd, $DRW_main);

                    echo '<br /><br />';
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

                    if (!isset($productArray[$productID]))
                        $productArray[$productID] = 0;
                    $productArray[$productID] += $mail_volume;

                    $s = array_search($productID, $noComboArray);
                    if ($s !== false) {
                        unset($noComboArray[$s]);
                    }
                }

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

    ############### for eve (email voolume estimate calculation only for email #############

    function doEMailEstimateVolume($year, $month, $factor = 1.88, $doprint = false) {
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
            WHERE productStatus=1 AND (mChannelID=3) AND (mPanelID=1) AND delmethid not in(4,5)  AND mTypeID in (4,5,6,8) AND ppdate>='$calc_date_range1' AND ppdate<'$calc_date_range2'";
        /* added for running march month data */
        /* End to added for running march month data */
        $result_P = $DRW->query($sql_P, $DRW_read2);
        while ($row = $DRW->fetch_row($result_P)) {
            // print_r($row);
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
            if ($mChannelID != 3 || $delmethid == 4 || $delmethid == 5) { //only direct mail for demographics and no FSI (also in productDetail.php display) // || $parent_panelist_id>0 //and no subpanelists (only here)
                if ($ppfico_score > 0) {
                    $ficosql = ',isFICO=1';
                } else {
                    $ficosql = '';
                }
                $sqlu = "UPDATE cscan_product_detail SET isDemographic=1$ficosql WHERE productID=$productID";
                // $DRW->query($sqlu, $DRW_main);
                //$sqlu = "UPDATE cscan_panelists_product SET ppmv=0,actual_ppmv=0,ppmv_w=0,ppmv_m=0,ppspend=0,ppmv_onupdate='".$currentdatetime."' WHERE panelist_id=$panelist_id AND productID=$productID AND ppdate='$ppdate'";
                //$DRW->query($sqlu, $DRW_main);
                continue;
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
        // echo '<pre>';
        // print_r($comboArray);
        // die;
        //echo 'processing..';
        $panelist_share = '';
        $numcomb=0;
        foreach ($comboArray as $key => $arry) {
            $numcomb++;
           // if($numcomb<=10000){
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
                $sql_chk_ppmv = "SELECT panelist_id FROM cscan_panelists_product where panelist_id='" . $panelist_id . "' AND productID='" . $productID . "' AND ppdate='" . $ppdate . "' AND ppeve<=0";
                $result_ppmv = $DRW->query($sql_chk_ppmv, $DRW_read2);
                $dataD_ppmv = $DRW->fetch_row($result_ppmv);
                // Comment for override emv calculation 
                if (!empty($dataD_ppmv[0])) {
                    //echo $sql_chk_ppmv;
                    //echo '<br><br><br><br>';
                    if (isset($this->countryArray[$pp_state]))
                        $pp_country = $this->countryArray[$pp_state];
                    list($ppy, $ppm) = explode('-', $ppdate);
                    $ppy = (int) $ppy;
                    $ppm = (int) $ppm;
                    $multAs = array();
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
                    $mail_volume_save = $mail_volume * $multiplier;
                    $mail_volume_save = round($mail_volume_save);
                    $upsql='';
                   $myupdate = "UPDATE cscan_panelists_product SET ppeve=$mail_volume_save,ageID=$ageID,ir_ID=$ir_ID,regionID=$regionID,ppeve_onupdate='" . $currentdatetime . "'
                      WHERE panelist_id=$panelist_id AND productID=$productID AND ppdate='" . $ppdate . "'";
                    
                    $DRW->query($myupdate, $DRW_main);
                   // echo 'executed\n\n';
                    if (!isset($productArray[$productID]))
                        $productArray[$productID] = 0;
                    $productArray[$productID] += $mail_volume;
                    $s = array_search($productID, $noComboArray);
                    if ($s !== false) {
                        unset($noComboArray[$s]);
                    }
                   // echo"hello run 1st";
                    ############### Comment for override emv calculation ####################
                }
                $multiplier = '';
           // }
        }
    }
    }
    ############### end for eve (email voolume estimate calculation only for email #############
}
?>