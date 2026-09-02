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
/*
 Curl callAPI()
*/
function callAPI($method, $url, $data){
    $curl = curl_init();
    switch ($method){
       case "POST":
          curl_setopt($curl, CURLOPT_POST, 1);
          if ($data)
             curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
          break;
       case "PUT":
          curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
          if ($data)
             curl_setopt($curl, CURLOPT_POSTFIELDS, $data);			 					
          break;
        case "DELETE":
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
        if ($data) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        break;
        case "GET":
            curl_setopt($curl, CURLOPT_URL, $url);
            break;
       default:
          if ($data)
             $url = sprintf("%s?%s", $url, http_build_query($data));
    }
    // OPTIONS:
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json','User-Agent:'.$_SERVER['HTTP_USER_AGENT'], 'X-Forwarded-For:'.$_SERVER['REMOTE_ADDR']));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    // EXECUTE:
    $result = curl_exec($curl);
    if(!$result){die("Connection Failure");}
    curl_close($curl);
    return $result;
 } 
 function getSector1() {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
	$APISECTORURL=RPVAPIURL_UAT.'data/sectors';
	$get_data = callAPI('GET', $APISECTORURL, false);
	$response = json_decode($get_data, true);
	$rows_sector_data=$response['data'];

    $arr = array();
	foreach($rows_sector_data['Sectordetails'] as $row_sector){
		$sectorID = $row_sector['sectorID'];
		$sectorName = $row_sector['sectorName'];
		$arr[$sectorID] = $sectorName;

	}
    return $arr;
}

function getCategory1($sectorID) {
    $arr = array();
	$arrayValue[] = $sectorID;
	$postSector['sectors']=$arrayValue;
	$posted_data=json_encode($postSector);
	$APISECTORURL=RPVAPIURL_UAT.'data/sectors';
	if(!empty($posted_data)){
	$getSector_data = callAPI('POST', $APISECTORURL, $posted_data);
	$response_category = json_decode($getSector_data, true);
	if(!empty($response_category)){
		foreach($response_category as $row_category){
			$sectorID = $row_category['sectorID'];
			$sectorName = $row_category['sectorName'];
			$arr[$sectorID] = $sectorName;
	
		}
		return $arr;
	}} else {
        return 0;
    }
}


function getSubCategory1($categoryID) {
    $arr = array();
	$arrayValue[] = $categoryID;
	$postSector['sectors']=$arrayValue;
	$posted_data=json_encode($postSector);
	$APISECTORURL=RPVAPIURL_UAT.'data/sectors';
	if(!empty($posted_data)){
	$getSector_data = callAPI('POST', $APISECTORURL, $posted_data);
	$response_category = json_decode($getSector_data, true);
	if(!empty($response_category)){
		foreach($response_category as $row_category){
			$sectorID = $row_category['sectorID'];
			$sectorName = $row_category['sectorName'];
			$arr[$sectorID] = $sectorName;
	
		}
		return $arr;
	}} else {
        return 0;
    }
}

?>