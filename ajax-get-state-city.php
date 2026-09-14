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

?>