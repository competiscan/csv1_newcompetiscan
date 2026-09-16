<?php
require_once('includes/globalSession.php');
require_once 'includes/digital-dashboard-function.php';
$country = '';
$state = '';
$city='';
$country = $_REQUEST['country'];
$state =$_REQUEST['state'];
$city = $_REQUEST['city'];
/*if(isset($_REQUEST['country']) and $_REQUEST['action']=="getState") {
    $country = $_REQUEST['country'];
    $state =$_REQUEST['state'];
}else if(isset($_REQUEST['state']) and $_REQUEST['action']=="getCity") {
    
}*/
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
       default:
          if ($data)
             $url = sprintf("%s?%s", $url, http_build_query($data));
    }
    // OPTIONS:
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
       'APIKEY: 111111111111111111111',
       'Content-Type: application/json',
    ));
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
@ob_clean();
header("Content-Type: text/plain");
$out = '';
if(!empty($country)  and $_REQUEST['action']=="getState"){
	$state_list = getStateMulti($country,false);
        /*echo "<pre>";
        print_r($state_list);
        echo "</pre>"; die;*/
        $out .= "<option selected value=\"\">Any</option>";
	if(!empty($state_list)){
                foreach($state_list as $id=>$name ) {
                        //echo $id."=======".$name."<br/>";
                        $selectd="";
			//if(!empty($id)){
                            /*if(!in_array($id,$state_list)){
                                         continue;
                                 }*/
                           if(!empty($state)){
                            $state_list_array=explode(',',$state);
                            if(in_array($id,$state_list_array)){
                                $selectd=" selected=selected ";
                            }
                        }
			$out .= "<option ".$selectd." value=\"$id\">".htmlspecialchars($name)."</option>";
                    //}
		}
	}
}
elseif(!empty($state) and $_REQUEST['action']=="getCity"){
	$city_list = getCityMulti($state,$country);
         $out .= "<option selected value=\"\">Any</option>";
	if(!empty($city_list)){
		foreach( $city_list as $id=>$name ) {
                         $selectd="";
                         if($id){
                           /*if(!in_array($id,$_SESSION['sess_category'])){
				continue;
                            }*/
                            if(!empty($city)){
                                $city_array=explode(',',$city);
                                if(count($city_array)>0){
                                    if(in_array($id,$city_array)){
                                        $selectd=" selected=selected ";
                                    }
                                }
                            }
                            $out .= "<option ".$selectd." value=\"$id\">".htmlspecialchars($name)."</option>";
                
                    }
                }
	}
}
if($out!=''){
	echo $out;
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

?>