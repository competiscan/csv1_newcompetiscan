#!/usr/bin/php
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once("includes/ehLog_set.php");
$ehL->start(__FILE__);
ini_set("default_charset", "utf-8");
//ini_set("memory_limit", "512M");
ini_set("memory_limit", "-1");
set_time_limit(0);

include_once('includes/dbcon.php');
$DRW->databaseReadWrite_die = 1;
require_once('includes/clean.php');
require_once('includes/functions.php');

$pArray	=[4893748,3497529,4890248,4891343,3578500,4894209,4893791,4892567,4893793,4892566,4057844,4892565,4893796,4048000,4063676,4127383,4893797,4893801,4165858,4238072,4266465,4278078,4290053,4596364,4399729,4399765,4445353,4463673,4462193,4467060,4891395,4891465,4890825,4890854,4890797,4890768,4891124,4890649,4891017,4532603,4890251,4891637,4568497,4891639,4891523,4890244,4891640,4891641,4890848,4616130,4890842,4891317,4627824,4636903,4891328,4653028,4659933,4893836,4715157,4689125,4892570,4720363,4698976,4698470,4734169,4723138,4893802,4726741,4890840,4892564,4795057,4736865,4893741,4754131,4892563,4893740,4814075,4816452,4890834,4893737,4893803,4892562,4825121,4893744,4893804,4789388,4807537,4890829,4821519,4807428,4807539,4861395,4864625,4890845,4890868,4820224,4893807,4844168,4844964,4864572,4879060,4882043,4891642,4853101,4856253,4890247,4861225,4890852,4892569,4892571,4892568,4896944,4919040];


foreach ($pArray as $productID) {
    $sql1 = "select sectorID,categoryID, SubCategoryID from cscan_product_detail where productID='" . $productID . "'";
    $query1 = $DRW->query($sql1, $DRW_read);
    if ($DRW->num_rows($query1) > 0) {
        $row1 = $DRW->fetch_assoc($query1);
        
        $arr_sec	=	explode(",", $row1['sectorID']);
        $arr_cat	=	explode(",", $row1['categoryID']);
        if(!in_array(4, $arr_sec)){
            array_push($arr_sec, 4);
            array_push($arr_cat, 14);
       }
       else if(!in_array(14, $arr_cat)){
            array_push($arr_cat, 14);
       }
        
        
        $str_sec = implode(",", $arr_sec);
        $str_cat = implode(",", $arr_cat);
        
        
        
        $arr_sub_cat = explode(",", $row1['SubCategoryID']);
        array_push($arr_sub_cat, 500);
        $str_sub_cat = implode(",", $arr_sub_cat);
        $sql2 = "update cscan_product_detail set sectorID='".$str_sec."',categoryID='".$str_cat."', subCategoryID='" . $str_sub_cat . "' where productID=$productID " ; //AND FIND_IN_SET(97, CategoryID)";

        echo $sql2 . ';</br>';
    }
}
die;
die;



/*
//die;
sendDevAlert('Conetent site','I am testing');
die;

######################### for user country permission #####################################

$uscompany   = array('AARP','Adrea Rubin','Aetna','AFBA and 5Star Life Insurance','Affinion Group','Affinity Health Plan','AFLAC','Allianz Global Corporate','Agent Pipeline','AGIA','AIG - Direct Marketing','AIG - Individual Retirement','AIG - Life & Health','AIG - VALIC','Alfa Insurance','Allianz Life','AllRisks','Allstate','Allstate Financial','Altice','Altice - OTI','American Electric Power','American Equity Investment Life Insurance','American Family','American Fidelity Assurance','American Financial Group','American Greetings','American Hallmark General Agency','American Integrity Insurance','American National','American Traditions Insurance / Modern USA','American-Amicable Life Insurance Company of Texas','AmericanEnterprise','Americo','Americor Funding','AmeriHealth','AmeriLife Marketing Administrative Services','Amerisure Insurance','Ameritas','Amica','AmTrust Group','Annexus','Aon','Arkansas BlueCross BlueShield','Arvest Bank','Aspire Health Plan','Assurance Insurance','Athene','Athene Life Re','Auriemma Consulting Group','Avera Health Plans','AvMed','AXA-Equitable','Axcess Financial','Baltimore Life','Bank of Luxemburg','Bankers Fidelity','Bankers Healthcare Group','Bankers Insurance Group','BB&T','BCS Financial','Big Picture Loans','BlueCross BlueShield Association','BlueCross BlueShield of Arizona','BlueCross BlueShield of Florida','BlueCross BlueShield of Kansas','BlueCross BlueShield of Kansas City','BlueCross BlueShield of Louisiana','BlueCross BlueShield of Massachusetts','BlueCross BlueShield of Michigan','BlueCross BlueShield of Minnesota','BlueCross BlueShield of Nebraska','BlueCross BlueShield of North Carolina','BlueCross BlueShield of Rhode Island','BlueCross BlueShield of South Carolina','BlueCross BlueShield of Tennessee','BlueCross of Idaho','BlueShield of California','BMC HealthNet','Boeing Employees Credit Union','Bright Health','Brighthouse Financial','California Casualty','Capital BlueCross','Capital Insurance Group','CareFirst','CareSource','CarShield','Catholic Financial Life','CDPHP','Centene','Central Mutual Insurance Company','Check City','Chubb','CIGNA','Cincinnati Financial','Citibank','Citibank1','City National Bank','City National Bank1','ClearOne Advantage','Clover Health','CNO Financial','CNO Financial-Bankers Life','Colonial Penn','Colorado Bankers Life Insurance','Columbian Financial Group','Commerce Bank','Commonwealth Financial Network','Companion Life','Conduent','Constellation','Copperline USA','CopperPoint Insurance','Country Financial','Cross Country Home Services','CSAA Insurance Group','Cummings Creative Group','CUNA Mutual Group','CVS Caremark','CVS Caremark PBM','Cypress Insurance','Darien Rowayton Bank','Darwill','Davis Vision','Dealer Tire','Dean Health Plan','Delaware Life','Delta Dental','Dental HealthX','Direct Energy','Discover','Ditech','DST Output','E*Trade','ELCO Mutual','Elephant Insurance','EMC Insurance','EMC National Life','EMI','EMPLOYERS','Empower Retirement','EquiTrust','Ethical Electric','Everest','Excellus BlueCross BlueShield','Express Scripts','EyeMed','Fairmont Specialty','Fairwinds Credit Union','Farm Bureau Insurance','Farmers Insurance Group','Fidelity & Guaranty Life','Fidelity Life Association','Fidelity Security Life','Fifth Third Bank','First Citizens Bank','Fiserv','Flagstar Bank','Foremost Insurance','Forethought','Freedom Mortgage','Funding Circle','Gateway Health Plan','GEBA','GEHA','Geisinger','Genworth','GeoVera Holdings Inc','Gleaner Life','Government Personnel Mutual Life','Grange Insurance','Grinnell Mutual Reinsurance','Guarantee Trust Life Insurance Company','Guardian Life','Guggenheim Insurance','Harris Bank','Harvard Pilgrim Health Care','Health Alliance Plan of Michigan','Health Care Service Corporation','Health New England','Health Partners Plans','HealthEquity','Healthfirst','HealthNow','HealthPartners','Healthplex','Hedgeye Risk Management','Highmark','HomeServe USA','Horace Mann','Horizon BlueCross BlueShield of NJ','HSA Bank','HSBC Bank','Humana','ICG Home','ICW Group','Illinois Mutual','Independence Blue Cross','Independent Health','InnerWorkings','iptiQ / SwissRe','ISO Innovative Analytics','IWCO Direct','Jackson National','Jewelers Mutual Insurance Company','John Hancock','Johnson & Quin, Inc.','Kabbage','Kaiser Permanente','KeyBank','Knights of Columbus','KSKJ Life','Landmark Management','LendingPoint','LendingTree','LexisNexis Risk Solutions','Liberty Mutual','LifeSecure','LightStream','LightStream1','LIMRA','Lincoln National','Luxottica','Marlette Funding','MassMutual','McGraw Insurance / Pacific Specialty','Medica','Medical Mutual','MediGold / Mount Carmel','MedImpact','Meemic Insurance Company','Merchants Insurance Group','Merrick Bank','MetLife','MetLife Auto and Home','Metromile Inc.','Modern Woodmen','Mr. Cooper','MRM/McCANN','MSP - Mailing Services of Pittsburgh','Munich Re','Municipal Credit Union','Mutual of Omaha','Mutual Trust Financial Group','MVP Health Care','National Debt Relief','National Guardian Life','National Life Group','National Mail Marketing','National Western Life','Nationwide','Navient','Neighborhood Health Plan','Network Health','New York Life','NJM Insurance Group','North American Communications','North American Power','Northwestern Mutual','Ohio National','Olympus Insurance','OneAmerica','OppLoans','Optima Health','Optima Health1','Oregon Mutual','Oxford Life','Pacific Guardian Life','Pekin Insurance','Penn Mutual','Phoenix','Physicians Mutual','Pinnacle Actuarial','Plymouth Rock','Premera BlueCross','Principal Financial','Priority Health','Progressive','Prosper Funding','Protective Life','Prudential Financial','PSCU','Quattro Direct','Quicken Loans','Raymond James','Regence','Reinsurance Group of America','Reliance Standard Life','Reliant Energy Retail','Reliant Funding','Renaissance Dental','Republic Finance','Rite Aid','RiverSource','Royal Neighbors of America','S&N Debt Solutions','Safety Insurance','Sagicor Life','Sammons Financial Group','SBLI','Scott & White Health Plan','SECURA Insurance','Securian Financial Group','Security Benefit Life','SelectHealth','Sentinel Security Life','ServiceMaster','Sharp Health Plan','Shelter Insurance','ShelterPoint Life Insurance','SingleCare','Smart Energy','Solstice Benefits','Sons of Norway','SourceLink','Southern Farm Bureau Life Insurance','Southern Guaranty Insurance','State Auto Insurance','State Farm','Strategic Financial Solutions','Sun Life Financial','TCF Bank','TD Ameritrade','Texas Life Insurance','The Brand Squad','The Hartford','The Hartford1','The Legal & General America Companies','The Members Group','The Motorists Insurance Group','The Richards Group','The Standard','Thrivent Financial','TIAA - Banking+Lending','TIAA - Insurance+Investments','Torchmark','Tower Hill Insurance','Transamerica','Trustmark','Tufts Health Plan','U.S. Bank','UCare','Ulta Beauty','UMB','Unified Analytics','Unison','UnitedHealthcare','Universal North America','Unum','UPC Insurance','UPMC Health Plan','USAA','USAA (+P&C)','USAble Life','Velapoint','Verizon','Virginia Farm Bureau','Virginia Premier Health Plan','Voya Financial / ING U.S.','Walgreens','WebBank','WellCare','Wellmark BlueCross BlueShield','WellPoint','Wells Fargo','West Bend Mutual Insurance','Western and Southern','Westfield Group','Westfield Group - Westfield Bank','WestGUARD','WEX Health','Woodmen of the World','WPS Health Insurance','Wunderman Health','Zions Bancorporation','Zurich');
$cacompany   = array('Allstate Insurance Company of Canada','Assumption Life','Co-operators','Economical Insurance','Empire Life','iA Financial Group','ivari','RSA Group','TD Bank Group');
$bothcompany = array('Canadian Tire Bank','CANNEX','CIGNA (Canada)','Combined Insurance','Foresters','Great-West / London Life','HackerAgency','Hagerty Management','La Capitale','LoyaltyOne','Markel Corporation','Marriott International','Valeyo','Valeyo1','Wawanesa Insurance');
$uscountry_id   =   'US';
$cacountry_id   =   'CA';
$bothcountry_id =   'BOTH';
foreach($uscompany as $company){
    $sql1 = "SELECT userID FROM `cscan_users` WHERE `companyName` = '" . $DRW->real_escape_string($company) . "'";
    $query1 = $DRW->query($sql1, $DRW_read);
    if ($DRW->num_rows($query1) > 0) {
        while ($row1 = $DRW->fetch_assoc($query1)) {
            $userID = $row1['userID'];
            if ($userID) {
               $sql = "insert INTO cscan_country_users_allow (country_id,userID) VALUES ('" . $uscountry_id . "',$userID)";
                $DRW->query($sql, $DRW_main);
                //echo"<br>";
            }
        }
}
}

foreach($cacompany as $company){
    $sql1 = "SELECT userID FROM `cscan_users` WHERE `companyName` = '" . $DRW->real_escape_string($company) . "'";
    $query1 = $DRW->query($sql1, $DRW_read);
    if ($DRW->num_rows($query1) > 0) {
        while ($row1 = $DRW->fetch_assoc($query1)) {
            $userID = $row1['userID'];
            if ($userID) {
               $sql = "insert INTO cscan_country_users_allow (country_id,userID) VALUES ('" . $cacountry_id . "',$userID)";
                $DRW->query($sql, $DRW_main);
                //echo"<br>";
            }
        }
}
}

foreach($bothcompany as $company){
    $sql1 = "SELECT userID FROM `cscan_users` WHERE `companyName` = '" . $DRW->real_escape_string($company) . "'";
    $query1 = $DRW->query($sql1, $DRW_read);
    if ($DRW->num_rows($query1) > 0) {
        while ($row1 = $DRW->fetch_assoc($query1)) {
            $userID = $row1['userID'];
            if ($userID) {
               $sql = "insert INTO cscan_country_users_allow (country_id,userID) VALUES ('" . $bothcountry_id . "',$userID)";
                $DRW->query($sql, $DRW_main);
                //echo"<br>";
            }
        }
}
}




echo"success";
exit;

######################### for user country permission #####################################
die('kill here');

################ Update admin permission #########
$cArray = ["AARP", "Aetna", "Affinity Health Plan", "Aflac", "AmericanEnterprise", "Americo", "AmeriHealth", "Ameritas", "Arkansas BlueCross BlueShield", "Aspire Health Plan", "Avera Health Plans", "AvMed", "BlueCross BlueShield Association", "BlueCross BlueShield of Arizona", "BlueCross BlueShield of Florida", "BlueCross BlueShield of Kansas", "BlueCross BlueShield of Kansas City", "BlueCross BlueShield of Louisiana", "BlueCross BlueShield of Massachusetts", "BlueCross BlueShield of Michigan", "BlueCross BlueShield of Minnesota", "BlueCross BlueShield of Nebraska", "BlueCross BlueShield of North Carolina", "BlueCross BlueShield of Rhode Island", "BlueCross BlueShield of South Carolina", "BlueCross BlueShield of Tennessee", "BlueCross of Idaho", "BlueShield of California", "BMC HealthNet", "Bright Health", "Capital BlueCross", "CareFirst", "CareSource", "CDPHP", "Centene", "CIGNA", "Cincinnati Financial", "Clover Health", "CVS Caremark PBM", "Davis Vision", "Dean Health Plan", "Delta Dental", "Dental HealthX", "Excellus BlueCross BlueShield", "Express Scripts", "EyeMed", "Forethought", "Gateway Health Plan", "GEBA", "GEHA", "Geisinger", "Genworth Financial", "Guardian Life", "Harvard Pilgrim Health Care", "Health Alliance Plan of Michigan (HAP)", "Health Care Service Corporation", "Health New England", "Health Partners Plans", "HealthEquity", "Healthfirst", "HealthNow", "HealthPartners", "Healthplex", "Highmark", "Horizon BlueCross BlueShield of NJ", "HSA Bank", "Humana", "Independence Blue Cross", "Independent Health", "Kaiser Permanente", "Martin's Point Health Care", "MassMutual", "Medica", "Medical Mutual of Ohio", "MediGold / Mount Carmel", "MedImpact", "MetLife", "Mutual of Omaha", "MVP Health Care", "Neighborhood Health Plan", "Network Health", "OneAmerica", "Optima Health", "Premera BlueCross", "Priority Health", "Regence", "Renaissance Dental", "Rite Aid", "Royal Neighbors of America", "Scott & White Health Plan", "SelectHealth", "Seton Healthcare", "Sharp Health Plan", "SingleCare", "Solstice Benefits", "Torchmark", "Transamerica", "Trustmark", "Tufts Health Plan", "UCare", "UMB Financial", "UnitedHealthcare", "Unum", "UPMC Health Plan", "USAA", "USAble", "USAble Life", "Virginia Premier Health Plan", "Voya Financial / ING U.S.", "Walgreens", "Wellcare", "Wellmark BlueCross BlueShield", "WellPoint", "WEX Health", "WPS Health Insurance"];
$i = 0;
$arrUsers = [];
foreach ($cArray as $company) {
    $sql1 = "SELECT userID FROM `cscan_users` WHERE `companyName` = '" . $DRW->real_escape_string($company) . "'";
    $query1 = $DRW->query($sql1, $DRW_read);
    if ($DRW->num_rows($query1) > 0) {
        while ($row1 = $DRW->fetch_assoc($query1)) {
            $userID = $$userIDrow1['userID'];
            if ($userID) {
//                $sql_chk = "SELECT * FROM cscan_sector_users_allow WHERE userID='".$userID."' AND sectorID='482'";
//                $query_chk = $DRW->query($sql_chk ,$DRW_read);
//                echo $sql_chk.'</br>';
//                if($DRW->num_rows($query_chk)>0){
//                    continue;
//                }else{
//                    echo $sql2.'</br>';
//                    $sql2 = "INSERT INTO cscan_sector_users_allow (sectorID,userID) VALUES (482, $userID)";
//                    $DRW->query($sql2, $DRW_main);
//                    
//                    $i++;
//                }  
                $sql2 = "DELETE FROM `cscan_sector_users_allow` WHERE `userID` = '" . $userID . "' AND `sectorID` IN (266,328,456)";
                //echo $sql2.'</br>';
                $DRW->query($sql2, $DRW_main);
                $arrUsers[$i] = $userID;
                $i++;
            }
        }
    }
}
$total = count($arrUsers);
$strUsers = implode(",", $arrUsers);
$backup = "SELECT * FROM `cscan_sector_users_allow` WHERE `userID` IN ($strUsers) AND `sectorID` IN (266,328)";
echo '</br></br>total: ' . $total . '</br>' . $backup . "</br>";
die;
die;

*/
################## update Retail/Consumer Services >>> Other >>>Hearing to Insurance>>>Health Insurance>>>Hearing
# 266,328,456 to 4,14,482 ###########################
$pArray = [4893748,3497529,4890248,4891343,3578500,4894209,4893791,4892567,4893793,4892566,4057844,4892565,4893796,4048000,4063676,4127383,4893797,4893801,4165858,4238072,4266465,4278078,4290053,4596364,4399729,4399765,4445353,4463673,4462193,4467060,4891395,4891465,4890825,4890854,4890797,4890768,4891124,4890649,4891017,4532603,4890251,4891637,4568497,4891639,4891523,4890244,4891640,4891641,4890848,4616130,4890842,4891317,4627824,4636903,4891328,4653028,4659933,4893836,4715157,4689125,4892570,4720363,4698976,4698470,4734169,4723138,4893802,4726741,4890840,4892564,4795057,4736865,4893741,4754131,4892563,4893740,4814075,4816452,4890834,4893737,4893803,4892562,4825121,4893744,4893804,4789388,4807537,4890829,4821519,4807428,4807539,4861395,4864625,4890845,4890868,4820224,4893807,4844168,4844964,4864572,4879060,4882043,4891642,4853101,4856253,4890247,4861225,4890852,4892569,4892571,4892568,4896944,4919040];
foreach ($pArray as $productID) {
    $sql1 = "select sectorID,categoryID,subCategoryID from cscan_product_detail where productID='" . $productID . "'";
    $query1 = $DRW->query($sql1, $DRW_read);
    if ($DRW->num_rows($query1) > 0) {
        $row1 = $DRW->fetch_assoc($query1);

        $arr_sectorID = explode(",", $row1['sectorID']);
        $pos = array_search(266, $arr_sectorID);
        unset($arr_sectorID[$pos]);
        array_push($arr_sectorID, 4);
        $arr_sectorID = array_unique($arr_sectorID);
        $str_sectorID = implode(",", $arr_sectorID);

        $arr_categoryID = explode(",", $row1['categoryID']);
        $pos = array_search(328, $arr_categoryID);
        unset($arr_categoryID[$pos]);
        array_push($arr_categoryID, 14);
        $arr_categoryID = array_unique($arr_categoryID);
        $str_categoryID = implode(",", $arr_categoryID);

        $arr_subCategoryID = explode(",", $row1['subCategoryID']);
        $pos = array_search(456, $arr_subCategoryID);
        unset($arr_subCategoryID[$pos]);
        array_push($arr_subCategoryID, 482);
        $arr_subCategoryID = array_unique($arr_subCategoryID);
        $str_subCategoryID = implode(",", $arr_subCategoryID);

        $sql3 = '';
        $sql_chk = "SELECT scsc_sort FROM cscan_scsc_product WHERE productID='" . $productID . "' AND scsc_sectorID='266' AND scsc_categoryID='328' AND scsc_subCategoryID='456'";
        $query_chk = $DRW->query($sql_chk, $DRW_read);
        if ($DRW->num_rows($query_chk) > 0) {
            $sql3 = "update cscan_scsc_product set scsc_sectorID='4', scsc_categoryID='14',scsc_subCategoryID='482' where productID='" . $productID . "' AND scsc_sectorID='266' AND scsc_categoryID='328' AND scsc_subCategoryID='456'";
        } else {
            $scsc_sort = 0;
            $m_sql = "SELECT max(scsc_sort) as scsc_sort FROM cscan_scsc_product WHERE productID='" . $productID . "'";
            $m_q = $DRW->query($m_sql, $DRW_read);
            $row_m = $DRW->fetch_assoc($m_q);
            $scsc_sort = (!empty($row_m['scsc_sort'])) ? $row_m['scsc_sort'] : 0;
            $chk_1 = "SELECT scsc_sort FROM cscan_scsc_product where productID='" . $productID . "' AND scsc_sectorID='4' AND  scsc_categoryID='14' AND scsc_subCategoryID='482'";
            $q_chk = $DRW->query($chk_1, $DRW_read);
            if ($DRW->num_rows($q_chk) == 0) {
                $sql3 = "insert into cscan_scsc_product set productID='" . $productID . "',scsc_sectorID='4', scsc_categoryID='14',scsc_subCategoryID='482',scsc_sort='" . $scsc_sort . "'";
            }
        }
        if ($sql3) {
            //$DRW->query($sql3, $DRW_main);
        }
        $sql2 = "update cscan_product_detail set sectorID='" . $str_sectorID . "',categoryID='" . $str_categoryID . "',subCategoryID='" . $str_subCategoryID . "' where productID='" . $productID . "'";
        //$DRW->query($sql2, $DRW_main);
        echo $sql3 . ';</br>';
        echo $sql2 . ';</br></br>';
    }
}
die;
die;


##############Issue comes again ######################
$mid = '';
$where = '';
if (isset($_GET['muid']) && $_GET['muid'] != '') {
    $mid = $_GET['muid'];
    if ($mid) {
        $where = ' AND muid>' . $mid;
    }
}

if ($_SERVER['argc'] > 0) {
    $mid = $_SERVER['argv'][1];
    if (!empty($mid)) {
        $where = ' AND muid>' . $mid;
    }
}
if ($mid == '') {
    $query_track = "SELECT muid FROM `cscan_email_update_track2` order by id desc limit 0,1";
    $query_track_res = $DRW->query($query_track, $DRW_read);
    $data_track = $DRW->fetch_row($query_track_res);
    $muid_track = $data_track[0];
    if (!empty($muid_track)) {
        $where = ' AND muid>' . $muid_track;
    }
}
//echo $query = "SELECT muid FROM `cscan_email` WHERE `email_from_one`='consumers@sbkcenter.com' ".$where." limit 0,2000";
$query = "SELECT muid FROM `cscan_email` WHERE `email_from_one`='consumers@sbkcenter.com' " . $where . " limit 0,60000";
//echo '</br>';
$query_result = $DRW->query($query, $DRW_read);
while ($data_e = $DRW->fetch_row($query_result)) {
    $muid = (int) $data_e[0];
    if ($muid) {
        $query_t = "SELECT cettext FROM `cscan_email_text` WHERE `muid`='" . $muid . "' AND `cettype`='text/plain' order by cetid desc limit 0,1";

        $query_result_t = $DRW->query($query_t, $DRW_read);
        $data_t = $DRW->fetch_row($query_result_t);

        $cettext = strstr($data_t[0], 'Subject:', true);
        if (empty($cettext)) {
            //$cettext = htmlentities($cettext);
            $cettext = strstr(strip_tags($data_t[0]), 'Subject:', true);
        }
        //echo $cettext;die;
        if (strstr($cettext, 'To:')) {
            $cettext = strstr($cettext, 'To:');
            $cettext = str_replace('To:', '', $cettext);
            $string = $cettext;
            //echo $string;die;
            $pattern = '/[a-z0-9_\-\+\.]+@[a-z0-9\-]+\.([a-z]{2,4})(?:\.[a-z]{2})?/i';
            preg_match_all($pattern, $string, $matches);
            if (!empty($matches[0][0])) {
                $cettext_email = $matches[0][0];
                //echo $cettext_email;die;
            } else {
                $cettext = str_replace('&lt;', '', $cettext);
                $cettext_email = strtolower(trim(str_replace('&gt;', '', $cettext)));
                if (strstr($cettext_email, 'sent:')) {
                    $cettext_email = strstr($cettext_email, 'sent:', true);
                }
                $cettext_email = trim(str_replace('"', '', $cettext_email));
            }
        } else {
            $string = $cettext;
            //echo $string;die;
            $pattern = '/[a-z0-9_\-\+\.]+@[a-z0-9\-]+\.([a-z]{2,4})(?:\.[a-z]{2})?/i';
            preg_match_all($pattern, $string, $matches);
            if (!empty($matches[0][0])) {
                $cettext_email = $matches[0][0];
                //echo $cettext_email;die;
            } else {
                $cettext = str_replace('&lt;', '', $cettext);
                $cettext_email = strtolower(trim(str_replace('&gt;', '', $cettext)));
                if (strstr($cettext_email, 'sent:')) {
                    $cettext_email = strstr($cettext_email, 'sent:', true);
                }
                $cettext_email = trim(str_replace('"', '', $cettext_email));
            }
        }
        if ($cettext_email) {
            $result_c_p = $DRW->query("SELECT first_name,last_name,panelist_id,stateID FROM cscan_panelists WHERE active=1 AND (email='" . $DRW->real_escape_string($cettext_email) . "' OR alt_email='" . $DRW->real_escape_string($cettext_email) . "' OR more_email LIKE '%" . mysqlLike($cettext_email) . "%') LIMIT 1", $DRW_read);
            if ($DRW->num_rows($result_c_p) > 0) {
                $data_c_p = $DRW->fetch_row($result_c_p);
                $first_name = $data_c_p[0];
                $last_name = $data_c_p[1];
                $full_name = $first_name . ' ' . $last_name;
                $email_from = '"' . $full_name . '"' . ' &lt;' . $cettext_email . '&gt;';
                $panelist_id = (int) $data_c_p[2];
                $email_stateID = (int) $data_c_p[3];
                $DRW->free_result($result_c_p);
                if ($panelist_id) {
                    $query = "UPDATE `cscan_email` SET email_from='" . addslashes($email_from) . "',email_from_one='" . $DRW->real_escape_string($cettext_email) . "',panelist_id='" . $panelist_id . "', email_stateID='" . $email_stateID . "' WHERE muid=$muid";
                    //echo $query.'<br>';
                    $DRW->query($query, $DRW_main);

                    $query_ins = "INSERT INTO `cscan_email_update_track2` (muid) VALUES (" . $muid . ")";
                    //echo $query_ins.'<br>';
                    $DRW->query($query_ins, $DRW_main);
                }
            }
        }
    }
}
echo 'END ' . $muid;
die;
die;

######################## used for stateID of cscan_email ###############################
$tables = ['cscan_email', 'cscan_email201707'];
foreach ($tables as $table) {
    echo $sql1 = "SELECT DISTINCT(email_from_one),panelist_id FROM " . $table . " where email_from_one not like '%@yahoo%'";
    $query1 = $DRW->query($sql1, $DRW_read);
    if ($DRW->num_rows($query1) > 0) {
        while ($row1 = $DRW->fetch_assoc($query1)) {
            $email = $row1['email_from_one'];
            $panelist_id = $row1['panelist_id'];
            if ($panelist_id) {
                $sql2 = "SELECT stateID FROM cscan_panelists WHERE panelist_id = '" . $panelist_id . "'";
                $query2 = $DRW->query($sql2, $DRW_read);
                if ($DRW->num_rows($query2) > 0) {
                    $row2 = $DRW->fetch_assoc($query2);
                    $stateID = $row2['stateID'];
                    if ($stateID) {
                        $sql3 = "SELECT muid FROM " . $table . " WHERE panelist_id = '" . $panelist_id . "' AND email_stateID!='" . $stateID . "' AND email_from_one='" . $email . "'";
                        $query3 = $DRW->query($sql3, $DRW_read);
                        if ($DRW->num_rows($query3) > 0) {
                            while ($row3 = $DRW->fetch_assoc($query3)) {
                                $muid = $row3['muid'];
                                if ($muid) {
                                    $sql4 = "UPDATE " . $table . " SET email_stateID='" . $stateID . "' WHERE muid='" . $muid . "'";
                                    //echo $sql4.';</br>';
                                    $DRW->query($sql4, $DRW_main);
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}
echo 'done';
die;

################## used to add sub sub category ###########################
die;
$pArray = [3878982, 3838683, 3796730, 3791890, 3825189, 3825834, 3807604, 3868860, 3812943, 3754526, 3745311, 3793467, 3660201, 3690827, 3719641, 3593187, 3616249, 3809634, 3685912, 3583424, 3583423, 3586031, 3583428, 3528409, 3676996, 3553876, 3583969, 3561974, 3436832, 3815687, 3561846, 3389715, 3389584, 3386249, 3485608, 3453712, 3360948, 3659849, 3376937, 3414453, 3316499, 3316448, 3324431, 3281545, 3269709, 3311524, 3292961, 3222234, 3733269, 3733267, 3733264, 3218226, 3205874, 3546519, 3199084, 3183487, 3177629, 3259231, 3122611, 3100237, 3098449, 3135267, 3727494, 3081782, 3727466, 3055615, 3046298, 3014115, 3727480, 3035758, 3727465, 3727458, 2973608, 3727762, 3142098, 3287281, 3091034, 3630677, 3035347, 2938623, 3727760, 3644953, 3633273, 2914567, 3041114, 2912842, 2984629, 2917114, 2878882, 2989864, 3468949, 2863625, 2864898, 3730867, 2813346, 3461557, 3730706, 3730421, 2852398, 2869857, 2853040, 3730427, 2759362, 2720985, 2694418, 3730442, 3095243, 3730458, 3817311, 2694354, 2748588, 2655012, 2928041, 2927985, 2782660, 2781992, 2787195, 3201909, 2635204, 2657291, 3168637, 2656430, 2560105, 2688129, 2657849, 2712332, 2626292, 2508042, 2497097, 2451159, 2656498, 2656507, 2355895, 2350243, 2343476, 2449202, 2303532, 2327468, 2687769, 2212193, 2255614, 2204741, 2211630, 2344000, 2460547, 2252805, 2274465, 3238902, 3238905, 3170118, 2220354, 2691142, 2687773, 2359808, 2222920, 2251852, 2170199, 2656333, 2460546, 2148292, 2139513, 2236887, 2124374, 2927838, 2255470, 2328696, 2102053, 2085834, 2128897, 2061383, 2224083, 2687764, 2110945, 2929943, 2123026, 2694582, 2657196, 2060951, 2256386, 2256387, 1941331, 2687774, 1933744, 1933730, 3899682, 1941322, 2448985, 2618950, 2695118, 1971931, 1951269, 2061120, 2083224, 1966719, 2521333, 2083223, 1825728, 1824388, 2083209, 2185769, 1876859, 1776064, 1876858, 2064837, 1728984, 1819182, 1694970, 1986364, 1664275, 2621249, 1669915, 1628937, 1910837, 1620423, 2692404, 1598257, 3167030, 3167008, 1592601, 1599995, 1588652, 1642750, 1604682, 1582127, 2695120, 1569639, 1617348, 2928099, 2692383, 1525924, 1485882, 1474496, 1537612, 2927799, 1499432, 2928053, 1539055, 1394125, 1394046, 1378370, 1376398, 2610275, 1369930, 1355410, 1367419, 1361171, 1446325, 1339295, 1341461, 2656342, 1321600, 2656336, 1307043, 1304451, 1299138, 2694617, 1305548, 1304222, 1279419, 1273095, 1335526, 1336472, 1258900, 1304301, 1232809, 1228996, 1217568, 1217292, 1218763, 3645220, 1194819, 1194813, 1206672, 1140384, 1362337, 1200182, 1099985, 1078131, 1061381, 1143858, 1098550, 1002303, 1050595, 1001661, 983894, 978704, 954955, 1086054, 953949, 953293, 947447, 940085, 933369, 900836, 900470, 900464, 900014, 893485, 878262, 866879, 896005, 890130, 2610319, 831178, 828786, 778417, 742008, 829817, 747853, 727656, 724259, 740379, 738753, 2928322, 796162, 761687, 697509, 656563, 655314, 647739, 637313, 628902, 621962, 770291, 1240429, 581427, 577033, 577686, 572243, 572224, 581424, 526391, 524265, 496687, 495216, 484054, 479830, 478892, 458374, 439380, 418902, 398451, 386122, 378622, 372591, 370586, 369292, 369828, 366695, 336514, 315501, 312319, 311680, 307756, 299060, 301816, 294443, 290736, 285242, 281587, 441035, 260252, 236328, 203986, 200245, 199006, 163111, 492111, 3095310, 120753, 118487, 117744, 117712, 97230, 247881, 2619097, 76449, 74660, 2726456, 547255, 45146, 58060, 2454597, 3684637, 25040, 3095304, 34329];
foreach ($pArray as $productID) {
    $sql1 = "select subSubCategoryID from cscan_product_detail where productID='" . $productID . "'";
    $query1 = $DRW->query($sql1, $DRW_read);
    if ($DRW->num_rows($query1) > 0) {
        $row1 = $DRW->fetch_assoc($query1);
        $arr_sub_sub_cat = explode(",", $row1['subSubCategoryID']);
        array_push($arr_sub_sub_cat, 481);
        $str_sub_sub_cat = implode(",", $arr_sub_sub_cat);
        $sql2 = "update cscan_product_detail set subSubCategoryID='" . $str_sub_sub_cat . "' where productID=$productID AND FIND_IN_SET(171, subCategoryID)";

        echo $sql2 . ';</br>';
    }
}
die;
die;




#############################################
$mid = '';
$where = '';
if (isset($_GET['muid']) && $_GET['muid'] != '') {
    $mid = $_GET['muid'];
    if ($mid) {
        $where = ' AND muid>' . $mid;
    }
}

if ($_SERVER['argc'] > 0) {
    $mid = $_SERVER['argv'][1];
    if (!empty($mid)) {
        $where = ' AND muid>' . $mid;
    }
}
if ($mid == '') {
    $query_track = "SELECT muid FROM `cscan_email_update_track2` order by id desc limit 0,1";
    $query_track_res = $DRW->query($query_track, $DRW_read);
    $data_track = $DRW->fetch_row($query_track_res);
    $muid_track = $data_track[0];
    if (!empty($muid_track)) {
        $where = ' AND muid>' . $muid_track;
    }
}
$query = "SELECT muid FROM `cscan_email` WHERE `email_from_one`='consumers@sbkcenter.com' " . $where . " limit 0,2000";

$query_result = $DRW->query($query, $DRW_read);
while ($data_e = $DRW->fetch_row($query_result)) {
    $muid = (int) $data_e[0];
    if ($muid) {
        $query_t = "SELECT cettext FROM `cscan_email_text` WHERE `muid`='" . $muid . "' AND `cettype`='text/plain' order by cetid desc limit 0,1";

        $query_result_t = $DRW->query($query_t, $DRW_read);
        $data_t = $DRW->fetch_row($query_result_t);
        $cettext = strstr(strip_tags($data_t[0]), 'Subject:', true);

        if (strstr($cettext, 'To:')) {
            $cettext = strstr($cettext, 'To:');
            $cettext = str_replace('To:', '', $cettext);
            $cettext = str_replace('&lt;', '', $cettext);
            $cettext_email = strtolower(trim(str_replace('&gt;', '', $cettext)));

            if (strstr($cettext_email, 'sent:')) {
                $cettext_email = strstr($cettext_email, 'sent:', true);
            }
            $cettext_email = trim(str_replace('"', '', $cettext_email));

            if ($cettext_email) {
                $result_c_p = $DRW->query("SELECT first_name,last_name,panelist_id,stateID FROM cscan_panelists WHERE active=1 AND (email='" . $DRW->real_escape_string($cettext_email) . "' OR alt_email='" . $DRW->real_escape_string($cettext_email) . "' OR more_email LIKE '%" . mysqlLike($cettext_email) . "%') LIMIT 1", $DRW_read);
                if ($DRW->num_rows($result_c_p) > 0) {
                    $data_c_p = $DRW->fetch_row($result_c_p);
                    $first_name = $data_c_p[0];
                    $last_name = $data_c_p[1];
                    $full_name = $first_name . ' ' . $last_name;
                    $email_from = '"' . $full_name . '"' . ' &lt;' . $cettext_email . '&gt;';
                    $panelist_id = (int) $data_c_p[2];
                    $email_stateID = (int) $data_c_p[3];
                    $DRW->free_result($result_c_p);
                    if ($panelist_id) {
                        $query = "UPDATE `cscan_email` SET email_from='" . addslashes($email_from) . "',email_from_one='" . $DRW->real_escape_string($cettext_email) . "',panelist_id='" . $panelist_id . "', email_stateID='" . $email_stateID . "' WHERE muid=$muid";
                        //echo '<br><br>';
                        $DRW->query($query, $DRW_main);
                    }
                }
            }
        }

        $query_ins = "INSERT INTO `cscan_email_update_track2` (muid) VALUES (" . $muid . ")";
        $DRW->query($query_ins, $DRW_main);
    }
}
echo 'END ' . $muid;
die;
?>
