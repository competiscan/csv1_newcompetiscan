<?php
require_once('includes/globalSession.php');
//require_once('includes/rpv-dashboard-function.php');
$country = '';
$state = '';
$city='';
$country = $_REQUEST['country'];
$state =$_REQUEST['state'];
$city = $_REQUEST['city'];
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
        }
    } else {
            return 0;
        }
}
@ob_clean();
header("Content-Type: text/plain");
$out = '';
if (!empty($_POST['country']) && isset($_POST['action']) && $_POST['action'] == "getState") {

    $country = trim($_POST['country']);

    $postCountry = array();
    $postCountry['country'] = $country;

    $posted_data = json_encode($postCountry);

    $API_DIGI_COUNTRY = DIGITAL_DASHBOARD_UAT . 'country';

    $out = '<option value="" selected="selected">Any</option>';

    if (!empty($posted_data)) {

        $getcountry_data = callAPI('POST', $API_DIGI_COUNTRY, $posted_data);
        $response_state_p = json_decode($getcountry_data, true);

        if (!empty($response_state_p['data'])) {

            $sessionState = array();
            if (!empty($_SESSION['state'])) {
                $sessionState = $_SESSION['state'];
            }

            foreach ($response_state_p['data'] as $row_state_p) {

                if (!empty($row_state_p['state_province'])) {
                    $stateName = trim($row_state_p['state_province']);
                } else {
                    continue;
                }

                if ($stateName == '') continue;

                $selected = '';
                if (in_array($stateName, $sessionState)) {
                    $selected = ' selected="selected"';
                }

                $out .= '<option value="' . htmlspecialchars($stateName, ENT_QUOTES) . '"' . $selected . '>' 
                      . htmlspecialchars($stateName) . '</option>';
            }
        }
    }

    echo $out;
    exit;
}

elseif(!empty($state) && $_REQUEST['action'] == "getCity") {

    $postCountry = [];
    $postCountry['country'] = $country;
    $postCountry['state'] = explode(",", $state);

    $posted_data = json_encode($postCountry);
    //echo $posted_data;

    $API_DIGI_COUNTRY = DIGITAL_DASHBOARD_UAT . 'country';

    if(!empty($posted_data)) {

        $getcountry_state_data = callAPI('POST', $API_DIGI_COUNTRY, $posted_data);
        $response_state_city = json_decode($getcountry_state_data, true);

        $out = "<option selected value=\"\">Any</option>";

        if(!empty($response_state_city['data'])) {

            foreach($response_state_city['data'] as $row_state_city) {

                if(!empty($row_state_city['city'])) {
                    $city_list_array = explode(',', $row_state_city['city']);

                    foreach($city_list_array as $city) {
                        $city = trim($city);
                        $selectd = "";

                        if(!empty($_REQUEST['city']) && $_REQUEST['city'] == $city) {
                            $selectd = " selected";
                        }

                        $out .= "<option $selectd value=\"" . htmlspecialchars($city) . "\">" 
                                . htmlspecialchars($city) . "</option>";
                    }
                }
            }
        }

        echo $out;
    }
}
/*#######################GET CATEGORY#####################*/
if($_REQUEST['action']=='getCategoryData'){
    $sector_list=$_REQUEST['sector_list'];
    $cate_id=$_REQUEST['cat_list'];
    $arrayValue = explode(",", $sector_list);
    $postSector['sectors']=$arrayValue;
    $posted_data=json_encode($postSector);
    //echo $posted_data;
    $APISECTORURL=RPVAPIURL.'data/sectors';
    if(!empty($posted_data)){
    $getSector_data = callAPI('POST', $APISECTORURL, $posted_data);
    $response_category = json_decode($getSector_data, true);
    $selected='selected';
    if($cate_id!=""){
        $selected='';
    }

    $out .= "<option $selected value=\"0\">Any</option>";
	if(!empty($response_category)){
    foreach($response_category as $getCateName) {
        $id=$getCateName['sectorID'];
        $name=$getCateName['sectorName'];
                        $selectd="";
                        if($id){
                            if(!in_array($id,$_SESSION['sess_category'])){
                                continue;
                            }
                        if(!empty($cate_id)){
                            $cat_array=explode(',',$cate_id);
                            if(count($cat_array)>0){
                                if(in_array($id,$cat_array)){
                                    $selectd=" selected=selected ";
                                }
                            }
                        }
                        $out .= "<option ".$selectd." value=\"$id\">".htmlspecialchars($name)."</option>";
            
                }
            }
	}
    if($out!=''){
        echo $out;
    }
    exit;
    }else{
        echo json_last_error_msg();exit;
    } 
}
elseif($_REQUEST['action']=='getSubCat'){
    $cate_id=$_REQUEST['cat_list'];
    $subcat_list=$_REQUEST['subcat_id'];
    $arrayValue = explode(",", $cate_id);
    $postSector['sectors']=$arrayValue;
    $posted_data=json_encode($postSector);
    //echo $posted_data;
    $APISECTORURL=RPVAPIURL.'data/sectors';
    if(!empty($posted_data)){
    $getSector_data = callAPI('POST', $APISECTORURL, $posted_data);
    $response_category = json_decode($getSector_data, true);
    $selected='selected';
    if($subcat_list!=""){
        $selected='';
    }

    $out .= "<option $selected value=\"0\">Any</option>";
	if(!empty($response_category)){
    foreach($response_category as $getSUBCateName) {
        $id=$getSUBCateName['sectorID'];
        $name=$getSUBCateName['sectorName'];
                        $selectd="";
                        if($id){
                            if(!in_array($id,$_SESSION['sess_subcategory'])){
                                continue;
                             }
                        if(!empty($subcat_list)){
                            $subcat_array=explode(',',$subcat_list);
                            if(count($subcat_array)>0){
                                if(in_array($id,$subcat_array)){
                                    $selectd=" selected=selected ";
                                }
                            }
                        }
                        $out .= "<option ".$selectd." value=\"$id\">".htmlspecialchars($name)."</option>";
            
                }
            }
	}
    if($out!=''){
        echo $out;
    }
    exit;
    }else{
        echo json_last_error_msg();exit;
    } 
}
/*#######################EXPORT TO FILE#####################*/
if($_REQUEST['action']=='express_download'){
    //$APIURLRPV=RPVAPIURL."es-rpv-download4";
    $APIURLDIGITALDASHBOARD=DIGITAL_DASHBOARD_UAT_DOWNLAOD."download_csv_data";
    $posted_jsondata=$_REQUEST['post_data'];
    if(!empty($posted_jsondata)){
    $ch_download = curl_init($APIURLDIGITALDASHBOARD); 
    curl_setopt($ch_download, CURLOPT_POST, 1);
    curl_setopt($ch_download, CURLOPT_POSTFIELDS, $posted_jsondata);
    curl_setopt($ch_download, CURLOPT_RETURNTRANSFER, 1); 
    curl_setopt($ch_download, CURLOPT_HTTPHEADER, array('Content-Type: application/json',
    'Accept: application/json',
    'User-Agent: Competiscan-Export-Service/1.0'));
    $result_download = curl_exec($ch_download);
    //  echo "<pre>";
    //            print_r($result_download);
    //            echo "</pre>";
    //            exit;
        if(!empty($result_download)){
                $data=json_decode($result_download);
            //     echo "<pre>";
            //    print_r($data);
            //    echo "</pre>";
            //    exit;
                $filelink=$data->download_url;
                echo trim($filelink);exit;

        }else{
            echo "Error: File not generated";exit;
        }
    }else{
        echo json_last_error_msg();exit;
    } 
}
?>