<?php 
/**
* Get the getStateMulti()
*
* @return array
*/  
function getStateMulti($country) {
        global $DRW, $DRW_read, $DRW_main, $DRW_crm,$DRW_biscience_digital;
        $arr = array();
        $sql = "select DISTINCT state_code, state_province  from cscan_digital_city_state where country IN('".$country."') ORDER BY state_province";
        $result = $DRW->query($sql, $DRW_biscience_digital);
        if ($DRW->num_rows($result) > 0) {
            while ($row = $DRW->fetch_array($result)) {
                $state_code = $row['state_code'];
                $state_province = $row['state_province'];
                $arr[$state_code] = $state_province;
            }
            return $arr;
        } else {
            return 0;
        }
}
/**
 * Get the getCityMulti()
 *
 * @return array
 */ 

function getCityMulti($stateID, $country) {
        global $DRW, $DRW_read, $DRW_main, $DRW_crm,$DRW_biscience_digital;
        $arr = array();
        $sql = "select DISTINCT city from cscan_digital_city_state where country IN('".$country."') AND state_code IN('".$stateID."') ORDER BY city";
        $result = $DRW->query($sql, $DRW_biscience_digital);
        if ($DRW->num_rows($result) > 0) {
            while ($row = $DRW->fetch_array($result)) {
                $city = trim($row['city']);
                $arr[$city] = $city;
            }
            return $arr;
        } else {
            return 0;
        }
    }
    
 /**
 * Get the getCityMulti()
 *
 * @return array
 * 

function getCityName($state_code, $city_name) {
        global $DRW, $DRW_read, $DRW_main, $DRW_crm;
        $arr = array();
        echo $sql = "select DISTINCT city,state_province,country from cscan_digital_city_state where city='".$city_name."' AND state_code='".$state_code."' ORDER BY city";
        $result = $DRW->query($sql, $DRW_read);
        if ($DRW->num_rows($result) > 0) {
            while ($row = $DRW->fetch_array($result)) {
                $city = trim($row['city']);
                $state_province = trim($row['state_province']);
                $country = trim($row['country']);
                $arr[$city] = $city;
            }
            return $arr;
        } else {
            return 0;
        }
    }*/
/**
 * Get the getDisplayDigitalCompanyName()
 *
 * @return array
 */
function getDisplayDigitalCompanyName() {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm,$DRW_biscience_digital;
    $query = "Select DISTINCT(companyID),companyName from cscan_digital_processed_records cdp  LEFT JOIN cscan_company cmp  on cdp.company_id=cmp.companyID ORDER BY companyName";
    $result = $DRW->query($query, $DRW_biscience_digital);
    $array = array();
    while ($row = $DRW->fetch_array($result)) {
        $array[$row['companyID']] = $row['companyName'];
    }
    return $array;
} 
/**
 * Get the getDisplayLocationName()
 *
 * @return array
 */
function getDisplayLocationName() {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm,$DRW_biscience_digital;
    $query = "SELECT DISTINCT(location) as location,location_state_code FROM cscan_digital_processed_location ORDER BY location";
    $result = $DRW->query($query, $DRW_biscience_digital);
    $array = array();
    while ($row = $DRW->fetch_array($result)) {
        $array[$row['location_state_code']] = $row['location'];
    }
    return $array;
} 
/**
 * Get the array2csv()
 *
 * @return array
 */
function array2csv(array $array) {
    if (count($array) == 0) {
        return null;
    }
    ob_start();
    $df = fopen("php://output", 'w');
    foreach ($array['data'] as $row) {
        fputcsv($df, $row);
    }
    fclose($df);
    return ob_get_clean();
}
/**
 * Get the download_send_headers()
 *
 * @return header
 */
function download_send_headers($filename) {
    // disable caching
    $now = gmdate("D, d M Y H:i:s");
    header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
    header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
    header("Last-Modified: {$now} GMT");
    // force download  
    header("Content-Type: application/force-download");
    header("Content-Type: application/octet-stream");
    header("Content-Type: application/download");
    // disposition / encoding on response body
    header("Content-Disposition: attachment;filename={$filename}");
    header("Content-Transfer-Encoding: binary");
}
?>