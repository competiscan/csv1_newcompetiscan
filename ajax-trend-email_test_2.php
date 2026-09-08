<?php
require_once('includes/globalSession.php');
require_once 'includes/functions.php';
//echo "dsfhshfshfhshfhs";
$searchQuery='';
if(isset($_REQUEST['h_ocr_search']) && $_REQUEST['action']=='save_search') {
	$h_ocr_search = $_REQUEST['h_ocr_search'];
        $searchQuery.= " and searchKey='$h_ocr_search'";
}
else {
	$h_ocr_search = '';
}
if(isset($_REQUEST['h_search_type']) && $_REQUEST['action']=='save_search') {
	$h_search_type = $_REQUEST['h_search_type'];
        $searchQuery.= " and searchType='$h_search_type'";
}
else {
	$h_search_type = '';
}
if(isset($_REQUEST['h_audience']) and $_REQUEST['action']=='save_search') {
       
	$h_audience = $_REQUEST['h_audience']; 
        $searchQuery.= " and mPanelID='$h_audience'";
}
else {
	$h_audience = '';
}
if(isset($_REQUEST['h_sector']) and $_REQUEST['action']=='save_search') {
       
	$h_sector = $_REQUEST['h_sector'];
        $searchQuery.= " and sectorID='$h_sector'";
}
else {
	$h_sector = '';
}
if(isset($_REQUEST['h_category']) and $_REQUEST['action']=='save_search') {
	$h_category = $_REQUEST['h_category'];
        $searchQuery.= " and categoryID='$h_category'";
}
else {
	$h_category = '';
}
if(isset($_REQUEST['h_subcategory']) and $_REQUEST['action']=='save_search') {
	$h_subcategory = $_REQUEST['h_subcategory'];
        $searchQuery.= " and subCategoryID='$h_subcategory'";
}
else {
	$h_subcategory = '';
}
if(isset($_REQUEST['h_subtosubcategory']) and $_REQUEST['action']=='save_search') {
	$h_subtosubcategory = $_REQUEST['h_subtosubcategory'];
        $searchQuery.= " and subSubCategoryID='$h_subtosubcategory'";
}
else {
	$h_subtosubcategory = '';
}
if(isset($_REQUEST['h_fdt']) and $_REQUEST['action']=='save_search') {
	$h_fdt = $_REQUEST['h_fdt'];
}
else {
	$h_fdt = '';
}
if(isset($_REQUEST['h_tdt']) and $_REQUEST['action']=='save_search') {
	$h_tdt = $_REQUEST['h_tdt'];
}
else {
	$h_tdt = '';
}
if(isset($_REQUEST['h_country']) and $_REQUEST['action']=='save_search') {
	$h_country = $_REQUEST['h_country'];
        $searchQuery.= " and country='$h_country'";
}
else {
	$h_country = '';
}
if(isset($_SESSION['sess_userID'])){
    $sess_userID=$_SESSION['sess_userID'];
} else{
   $sess_userID=''; 
}
if(isset($_SESSION['sess_userType'])){
    $sess_userType=$_SESSION['sess_userType'];
} else{
   $sess_userType=''; 
}
if($_REQUEST['action']=='save_search'){
   
    $chkQuery="Select * from cscan_trend_report_search where userID='$sess_userID' and userType='$sess_userType' $searchQuery";
    $chk_rs = $DRW->query($chkQuery,$DRW_read);
    if($DRW->num_rows($chk_rs) == 0)
    {
        $sql = "INSERT INTO  cscan_trend_report_search SET userID='$sess_userID',userType='$sess_userType',searchKey='$h_ocr_search',searchType='$h_search_type',mPanelID='$h_audience',sectorID='$h_sector',categoryID='$h_category',subCategoryID='$h_subcategory',subSubCategoryID='$h_subtosubcategory',country='$h_country',from_date='$h_fdt',to_date='$h_tdt',emailAlert='1',alert_type=0";
        $save_data=$DRW->query($sql,$DRW_main);
        if($save_data){
            echo $Msg = 'Your search has been saved.';
        }
    } else{
        echo $Msg='Your search already saved, Please search another criteria.';
    }
}
 //===================Trend email alert Delete========================//
    if(isset($_REQUEST['trendsearch_id']) && $_REQUEST['action']=='search_alert_delete') {
	$trendsearch_id = $_REQUEST['trendsearch_id'];
        $sqlDelete = "delete from cscan_trend_report_search where ID='$trendsearch_id'";
        $resp=$DRW->query($sqlDelete, $DRW_main);
        if($resp){
            echo "1"; exit;
        }else{
            echo "0"; exit;
        }
    }
  //====================================Save Category Checkbox==============
    if(isset($_REQUEST['action']) && $_REQUEST['action']=='save_industry_alert') {
    $sql_country = "SELECT DISTINCT country_id FROM cscan_country_users_allow where userID='".$_SESSION['sess_userID']."'";
    $rs_country  = $DRW->query( $sql_country,$DRW_read );
    $row_country = $DRW->fetch_array($rs_country);
    $country_id='';
    if($row_country[0]=='US'){
       $country_id='1';
    }elseif($row_country[0]=='CA'){
        $country_id='3';
    }
    if(isset($_REQUEST['check_category'])){
      $alertArray =$_REQUEST['check_category'];
    }else{
       $alertArray=array(); 
    }
    $savedArray = array();
    $sql = "SELECT sectorID,categoryID,subCategoryID,subSubCategoryID FROM cscan_trend_report_search WHERE userID='{$_SESSION['sess_userID']}' AND emailAlert=1 AND country='".$country_id."'";
    $savedQuery = $DRW->query($sql,$DRW_read);
    while($row = $DRW->fetch_row($savedQuery)) {
            $savedArray[$row[0]] = $row[0];
            $savedArray[$row[1]] = $row[1];
            $savedArray[$row[2]] = $row[2];
            $savedArray[$row[3]] = $row[3];
    }
    $save_data="";
    $currArray = $savedArray;
    foreach ($alertArray as $chkcatid){
        if(!key_exists($chkcatid,$savedArray)){  
          $sql ="SELECT level,c.* FROM ( SELECT @r AS _id, (SELECT @r := parentID FROM cscan_sector WHERE sectorID = _id) AS parent_id, @l := @l + 1 AS level FROM (SELECT @r := '".$chkcatid."', @l := 0) vars, cscan_sector m WHERE @r <> 0) d JOIN cscan_sector c ON d._id = c.sectorID  order By level DESC";
          $rs = $DRW->query($sql,$DRW_read);
          $resultCount = $DRW->num_rows($rs);
          $num=1;
          if($resultCount > 0) {
              while($row = $DRW->fetch_assoc($rs)) {
                  $sectorID = $row['sectorID'];
                  $level=$row['level'];
                  $sector_id='';
                  $category_id='';
                  $subcategory_id='';
                  $subtosubcategory_id='';
                  $searchQuery='';
                  if($num==1){
                    $sector_id =$sectorID;
                    $searchQuery=" AND sectorID=".$sector_id;
                  }elseif ($num==2) {
                    $category_id =$sectorID;
                    $searchQuery=" AND categoryID=".$category_id;
                   }
                   elseif ($num==3) {
                    $subcategory_id =$sectorID;
                    $searchQuery=" AND subCategoryID=".$subcategory_id;
                   }elseif ($num==4) {
                       $subtosubcategory_id =$sectorID;
                       $searchQuery=" AND subSubCategoryID=".$subtosubcategory_id;
                   }
            $num++;
            }
            $chkQuery="Select * from cscan_trend_report_search where userID='$sess_userID' and userType='$sess_userType' $searchQuery";
            $chk_rs = $DRW->query($chkQuery,$DRW_read);
                if($DRW->num_rows($chk_rs) == 0)
                {
                  $sql = "INSERT IGNORE INTO  cscan_trend_report_search SET userID='".$sess_userID."',searchKey='',userType='".$sess_userType."',sectorID='".$sector_id."',categoryID='".$category_id."',subCategoryID='".$subcategory_id."',subSubCategoryID='".$subtosubcategory_id."',country='".$country_id."',emailAlert='1',alert_type=1";
                  $save_data=$DRW->query($sql,$DRW_main);
                }else{
                    echo "2"; exit;
                }
            }

        }else{
            unset($currArray[$chkcatid]);
        }
    }
    
    foreach($currArray as $catID){
          $sql ="SELECT level,c.* FROM ( SELECT @r AS _id, (SELECT @r := parentID FROM cscan_sector WHERE sectorID = _id) AS parent_id, @l := @l + 1 AS level FROM (SELECT @r := '".$catID."', @l := 0) vars, cscan_sector m WHERE @r <> 0) d JOIN cscan_sector c ON d._id = c.sectorID  order By level DESC";
          $rs = $DRW->query($sql,$DRW_read);
          $resultCount = $DRW->num_rows($rs);
          $num=1;
          if($resultCount > 0) {
              while($row = $DRW->fetch_assoc($rs)) {
                  $sectorID = $row['sectorID'];
                  $level=$row['level'];
                  $sector_id='';
                  $category_id='';
                  $subcategory_id='';
                  $subtosubcategory_id='';
                  $searchQuery='';
                  if($num==1){
                      $sector_id =$sectorID;
                      $searchQuery=" AND sectorID=".$sector_id;
                  }elseif ($num==2) {
                    $category_id =$sectorID;
                     $searchQuery=" AND categoryID=".$category_id;
                   }
                   elseif ($num==3) {
                    $subcategory_id =$sectorID;
                    $searchQuery=" AND subCategoryID=".$subcategory_id;
                   }elseif ($num==4) {
                       $subtosubcategory_id =$sectorID;
                       $searchQuery=" AND subSubCategoryID=".$subtosubcategory_id;
                    }
            $num++;
            }
            $sqlDelete = "delete from cscan_trend_report_search where userID='".$sess_userID."' AND country='".$country_id."' and userType='".$sess_userType."' $searchQuery";
            $resp=$DRW->query($sqlDelete, $DRW_main);   
            }
        } 
    //if($save_data){
        echo "1";exit;
    //}  
}
?>