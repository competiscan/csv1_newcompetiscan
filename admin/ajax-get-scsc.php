<?php
require_once("../auth_auth.php");
require_once '../includes/functions.php';
require_once '../class/Trend.php';
$sid = 0;
$cate_id = 0;
$cid = 0;
$subcat_id =0;
$scid = 0;
$subsubcat_id = 0;

if(isset($_REQUEST['sid']) and $_REQUEST['action']=="sector") {
    //print_r($_REQUEST); die;
    $sid = $_REQUEST['sid'];
    $cate_id =$_REQUEST['cate_id'];
}else if(isset($_REQUEST['cid']) and $_REQUEST['action']=="cat") {
    //print_r($_REQUEST); die;
    $cid = $_REQUEST['cid'];
    $subcat_id = $_REQUEST['subcat_id'];
}else if(isset($_REQUEST['scid']) and $_REQUEST['action']=="subcat") {
    $scid = $_REQUEST['scid'];
    $subsubcat_id=$_REQUEST['subsubcat_id'];
}

@ob_clean();
header("Content-Type: text/plain");
$out = '';
if(!empty($scid)){
    $category = getSubCategoryMulti($scid,false);
     $out .= "<option selected value=\"\">--Any--</option>";
    if($category!=0){
        foreach( $category as $id=>$name ) {
            $selectd="";
            if(!empty($id)){
                if(!empty($subsubcat_id)){
                    $subsubcat_array=explode(',',$subsubcat_id);
                    if(in_array($id,$subsubcat_array)){
                        $selectd=" selected=selected ";
                    }
                }              
                $out .= "<option ".$selectd." value=\"$id\">".htmlspecialchars($name)."</option>";
                       
            }
        }
    }
}
elseif(!empty($cid)){
    $subcategory = getSubCategoryMulti($cid,false);
    $out .= "<option selected value=\"\">--Any--</option>";
    if($subcategory!=0){
        foreach( $subcategory as $id=>$name ) {
            $selectd="";
            if(!empty($id)){
                if(!empty($subcat_id)){
                  $subcat_array=explode(',',$subcat_id);
                    if(in_array($id,$subcat_array)){
                        $selectd=" selected=selected ";
                    }
                }
                if(checkSubCategory($id)){
                    $out .= "<option ".$selectd." value=\"$id\">".htmlspecialchars($name)."</option>";
                }
            }
        }
    }
}
elseif(!empty($sid)){        
    $category = getCategoryMulti($sid);
    $out .= "<option selected value=\"\">--Any--</option>";
    if($category!=0){
        foreach( $category as $id=>$name ) {
            $selectd="";
            if(checkCategory($id)){
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
}
if($out!=''){	
    echo $out;
}
?>