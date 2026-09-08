<?php
require_once('includes/globalSession.php');
require_once ("includes/dbcon.php");
require_once('includes/rpv-dashboard-function.php');
if($_REQUEST['action']=='load_data'){
    $productids_array=trim($_REQUEST['productids_array'],"[]");
    $expo_data=explode(',',$productids_array);
    $posted_jsondata=$_REQUEST['post_data'];
    if(!empty($posted_jsondata)){
        $APIURLRPV=RPVAPIURL."products-rpv"; 
        $getRPV = callAPI('POST', $APIURLRPV, $posted_jsondata);
        $responseSearchRPV = json_decode($getRPV, true);
        /*echo "<pre>";
        print_r($responseSearchRPV);
        echo "</pre>"; exit;*/
        $allbrand="";
        $sumRPV="";
        $sumEVE="";
        $sumRPV="";
        $rpv_primary="";
        $all_data="";
        if($responseSearchRPV['msg']=='Successfully Retrieved'){
            foreach($responseSearchRPV['report_data'] as $item){
                $keyproductID = key($item); 
                if(in_array($keyproductID,$expo_data)){
                //if($ProductID===$keyproductID){
                   $rpv_primary=$item[$keyproductID]['primary_company'];
                if($item[$keyproductID]['eve_sum']!=''){
                    $sumEVE=$item[$keyproductID]['eve_sum'];
                }
                if($item[$keyproductID]['rpv_sum']!=''){
                    $sumRPV=$item[$keyproductID]['rpv_sum'];
                }
                  $allbrand = $item[$keyproductID]['brand_company']; 
                  $all_data.=$keyproductID."#".$rpv_primary."#".$sumEVE."#".$sumRPV."#".$allbrand."##"; 
                }else{
                    $all_data="";
                }
                
            }
            echo trim($all_data,"##");exit;
        }
    }  
}

/*#######################EXPORT TO FILE#####################*/
if($_REQUEST['action']=='express_download'){
    //$APIURLRPV=RPVAPIURL."es-rpv-download4";
    $APIURLRPV="https://vat-api2.competiscan.com/es-rpv-download4";
    //es-rpv-download
    //$API_DOWNLOADURL = "https://api.competiscan.com/elasticsearch-service/v1/search/download";
     $posted_jsondata=$_REQUEST['post_data'];
    if(!empty($posted_jsondata)){
    $ch_download = curl_init($APIURLRPV); 
    curl_setopt($ch_download, CURLOPT_POST, 1);
    curl_setopt($ch_download, CURLOPT_POSTFIELDS, $posted_jsondata);
    curl_setopt($ch_download, CURLOPT_RETURNTRANSFER, 1); 
    curl_setopt($ch_download, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    $result_download = curl_exec($ch_download);
    echo $result_download; exit;
    }else{
        echo json_last_error_msg();exit;
    } 
}
/*#######################GET STATE#####################*/
@ob_clean();
header("Content-Type: text/plain");
$out = '';
if($_REQUEST['action']=='getStateData'){
    $country=$_REQUEST['country'];
    if($country=='1'){
        $country_data='US';
    }elseif($country=='3'){
        $country_data='CA';
    }else{
        $country_data=$country;
    }
    $state_list=$_REQUEST['state_list'];
    $postCountry['countryid']=$country_data;
    $posted_data=json_encode($postCountry);
    //echo $posted_data;
    $APIStateURL=RPVAPIURL.'state';
    if(!empty($posted_data)){
    //echo $posted_jsondata;
    if($country_data=='0'){
        $getState_data = callAPI('GET', $APIStateURL, false); 
    }else{
        $getState_data = callAPI('POST', $APIStateURL, $posted_data); 
    }
    
    $response_state = json_decode($getState_data, true);
    $rowstate_data=$response_state['data'];
    $selected='';
    if($state_list=="0" || $state_list==""){
        $selected='selected'; 
    }
    $out .= "<option $selected value=\"0\">Any</option>";
	if(!empty($rowstate_data)){
    foreach($rowstate_data as $getStateData) {
        $id=$getStateData['stateID'];
        $name=$getStateData['stateName'];
            $selectd="";
            if($id){
                if(!empty($state_list)){
                    $state_array=explode(',',$state_list);
                    if(count($state_array)>0){
                        if(in_array($id,$state_array)){
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
elseif($_REQUEST['action']=='getSubSubCat'){
    $subcate_id=$_REQUEST['subcat_id'];
    $subsubcat_list=$_REQUEST['subsubcat_id'];
    $arrayValue = explode(",", $subcate_id);
    $postSector['sectors']=$arrayValue;
    $posted_data=json_encode($postSector);
    //echo $posted_data;
    $APISECTORURL=RPVAPIURL.'data/sectors';
    if(!empty($posted_data)){
    $getSector_data = callAPI('POST', $APISECTORURL, $posted_data);
    $response_category = json_decode($getSector_data, true);
    $selected='selected';
    if($subsubcat_list!=""){
        $selected='';
    }

    $out .= "<option $selected value=\"0\">Any</option>";
	if(!empty($response_category)){
    foreach($response_category as $getSUBCateName) {
        $id=$getSUBCateName['sectorID'];
        $name=$getSUBCateName['sectorName'];
                        $selectd="";
                        if($id){
                        /*if(!in_array($id,$_SESSION['sess_category'])){
                            continue;
                        }*/
                        if(!empty($subsubcat_list)){
                            $subsubcat_array=explode(',',$subsubcat_list);
                            if(count($subcat_array)>0){
                                if(in_array($id,$subsubcat_array)){
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