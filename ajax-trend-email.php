<?php
require_once('includes/globalSession.php');
require_once 'includes/functions.php';
//echo "dsfhshfshfhshfhs";
$searchQuery='';
if(isset($_REQUEST['h_ocr_search'])  && $_REQUEST['action']=='save_search') {
	$h_ocr_search = $_REQUEST['h_ocr_search'];
        if($h_ocr_search==''){
            $searchQuery.= " and (searchKey='' OR searchKey IS NULL) ";
        }else{
            $searchQuery.= " and searchKey='$h_ocr_search'";
        }
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
   
    $chkQuery="Select ID,emailAlert from cscan_trend_report_search where userID='$sess_userID' $searchQuery";
    $chk_rs = $DRW->query($chkQuery,$DRW_read);
    if($DRW->num_rows($chk_rs) == 0)
    {
        $sql = "INSERT INTO  cscan_trend_report_search SET userID='$sess_userID',userType='$sess_userType',searchKey='$h_ocr_search',searchType='$h_search_type',mPanelID='$h_audience',sectorID='$h_sector',categoryID='$h_category',subCategoryID='$h_subcategory',subSubCategoryID='$h_subtosubcategory',country='$h_country',from_date='$h_fdt',to_date='$h_tdt',emailAlert='1',alert_type=0";
        $save_data=$DRW->query($sql,$DRW_main);
        if($save_data){
            echo $Msg = 'Your search has been saved.';
        }
    } else{
        $row_records_dup = $DRW->fetch_array($chk_rs);
        $mail_alert=$row_records_dup[1];
        $alertID=$row_records_dup[0];
        if($mail_alert==0){
            $sqlupdt = "Update cscan_trend_report_search SET emailAlert=1 where ID='$alertID'";
            $save_data_updt=$DRW->query($sqlupdt,$DRW_main);
            if($save_data_updt){
                echo $Msg = 'Your search has been saved.';
            }else{
                echo $Msg='Your search already saved, Please search another criteria.';
            }
        }else{
            echo $Msg='Your search already saved, Please search another criteria.';
        }
        
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
    $savedmergeArray=array();
    $saved_array=array();
    $updated_array=array();
    $sql = "SELECT sectorID,categoryID,subCategoryID,subSubCategoryID,ID FROM cscan_trend_report_search WHERE userID='{$_SESSION['sess_userID']}' AND emailAlert=1 AND country='".$country_id."' AND (searchKey='' OR searchKey IS NULL)";
    $savedQuery = $DRW->query($sql,$DRW_read);
    $impolde_check_id='';
    while($row = $DRW->fetch_row($savedQuery)) {
        $check_array=array();
        $remove_array=array();
        if(strstr($row['0'],',')){
            $expl_sector=explode(',',$row['0']);            
            foreach ($expl_sector as $exp_sect_id){
                if(in_array($exp_sect_id,$alertArray)){
                   $check_array[]= $exp_sect_id;
                }else{
                    $remove_array[]= $exp_sect_id;
                }
            }
            //print_r($check_array); die;
            if(count($check_array)>0){
                if(count($check_array)>1){
                    $impolde_check_id=implode(',',$check_array);
                    foreach($check_array as $chek_delete){
                        $key2 = array_search($chek_delete, $alertArray); 
                        if (false !== $key2) {
                            unset($alertArray[$key2]);
                        }  
                    } 
                }else{
                    $impolde_check_id=$check_array[0]; 
                    $key = array_search($impolde_check_id, $alertArray); 
                    if (false !== $key) {
                        unset($alertArray[$key]); 

                    } 
                } 
                $sqlUpdate = "Update cscan_trend_report_search set sectorID='".$impolde_check_id."' where ID='".$row[4]."' AND (searchKey='' OR searchKey IS NULL)";
                $resp=$DRW->query($sqlUpdate, $DRW_main);
                
                $sql_dup = "SELECT sectorID,categoryID,subCategoryID,subSubCategoryID,ID,searchKey,country,emailAlert FROM cscan_trend_report_search WHERE userID='{$_SESSION['sess_userID']}' AND emailAlert=1 AND country='".$country_id."' AND ID='".$row[4]."'";
                $rs_dup  = $DRW->query($sql_dup,$DRW_read);
                $row_dup_records = $DRW->fetch_array($rs_dup);
                $sqlDelete_dup = "Delete From cscan_trend_report_search where ID!='".$row[4]."' AND sectorID='".$row_dup_records[0]."' AND categoryID='".$row_dup_records[1]."' AND subCategoryID='".$row_dup_records[2]."' AND subSubCategoryID='".$row_dup_records[3]."' AND (searchKey='' OR searchKey IS NULL) AND country='".$row_dup_records[6]."' AND emailAlert='".$row_dup_records[7]."' AND userID='{$_SESSION['sess_userID']}'";
                $resp_dup=$DRW->query($sqlDelete_dup, $DRW_main);                
                $updated_array[]=$row[4];

            }else{
                $sql_dup = "SELECT sectorID,categoryID,subCategoryID,subSubCategoryID,ID,searchKey,country,emailAlert FROM cscan_trend_report_search WHERE userID='{$_SESSION['sess_userID']}' AND emailAlert=1 AND country='".$country_id."' AND ID='".$row[4]."'";
                $rs_dup  = $DRW->query($sql_dup,$DRW_read);
                $row_dup_records = $DRW->fetch_array($rs_dup);
                $sqlDelete_dup = "Delete From cscan_trend_report_search where ID!='".$row[4]."' AND sectorID='".$row_dup_records[0]."' AND categoryID='".$row_dup_records[1]."' AND subCategoryID='".$row_dup_records[2]."' AND subSubCategoryID='".$row_dup_records[3]."' AND (searchKey='' OR searchKey IS NULL) AND country='".$row_dup_records[6]."' AND emailAlert='".$row_dup_records[7]."' AND userID='{$_SESSION['sess_userID']}'";
                $resp_dup=$DRW->query($sqlDelete_dup, $DRW_main);                
                $updated_array[]=$row[4];
                
                $sqlDelete = "Delete From cscan_trend_report_search where ID='".$row[4]."' AND (searchKey='' OR searchKey IS NULL)";
                $resp1=$DRW->query($sqlDelete, $DRW_main);    
            }
            if(count($remove_array)>0){
                foreach($remove_array as $delete_sec){                    
                    $sqlDelete2 = "Delete From cscan_trend_report_search where ID!='".$row[4]."' AND userID='{$_SESSION['sess_userID']}' AND emailAlert=1 AND country='".$country_id."' AND (searchKey='' OR searchKey IS NULL) AND sectorID='".$delete_sec."' AND categoryID='' AND subCategoryID='' AND subSubCategoryID=''";
                    $resp2=$DRW->query($sqlDelete2, $DRW_main);                    
                }
            }
            
          
        } 
	
        //$expl_sector=array();
            //$savedArray[$row[0]] = $row[0];
        //$savedArray[] = $row[1];
        //$savedArray[] = $row[2];
        //$savedArray[] = $row[3];
        //$savedmergeArray[]=array_merge($check_array,$savedArray);
        /*if($impolde_check_id!=''){
            if(strstr($impolde_check_id,',')){
                $expl_sector1=explode(',',$impolde_check_id);
                $check_array=array();
                foreach ($expl_sector1 as $exp_sect_id1){
                    if(in_array($exp_sect_id1,$savedmergeArray)){
                      $key3 = array_search($exp_sect_id1, $savedmergeArray); 
                        if (false !== $key3) {
                            unset($savedmergeArray[$key3]); 

                        }  
                    }
                }
            }else{
                $key4 = array_search($impolde_check_id, $savedmergeArray); 
                    if (false !== $key4) {
                        unset($savedmergeArray[$key4]); 

                    }   
            }
        }*/
    }
    
    $sql2 = "SELECT sectorID,categoryID,subCategoryID,subSubCategoryID,ID FROM cscan_trend_report_search WHERE userID='{$_SESSION['sess_userID']}' AND emailAlert=1 AND country='".$country_id."' AND (searchKey='' OR searchKey IS NULL)";
    $savedQuery2 = $DRW->query($sql2,$DRW_read);    
    while($row2 = $DRW->fetch_row($savedQuery2)) {
        $check_array2=array();
        if(strstr($row2['0'],',')){
            $expl_sector=explode(',',$row2['0']);            
            foreach ($expl_sector as $exp_sect_id){
                if(in_array($exp_sect_id,$alertArray)){
                   $check_array2[]= $exp_sect_id;
                }
            }    
          
        }else{
            $check_array2[]=$row2[0];            
        }
        
        $savedArray2[] = $row2[1];
        $savedArray2[] = $row2[2];
        $savedArray2[] = $row2[3];
        $savedmergeArray[]=array_merge($check_array2,$savedArray2);     
    }    
    
    
    $save_data="";
    //$currArray = $savedmergeArray;
    $currArray = array_unique(array_filter(call_user_func_array('array_merge', $savedmergeArray)));
    foreach ($alertArray as $chkcatid){
        if(!in_array($chkcatid,$currArray)){  
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
                $chkQuery="Select ID,emailAlert from cscan_trend_report_search where userID='$sess_userID' AND (searchKey='' OR searchKey IS NULL) AND country='".$country_id."' $searchQuery";
                $chk_rs = $DRW->query($chkQuery,$DRW_read);
                $chkrow= $DRW->num_rows($chk_rs);
                //echo "PPPPPP".$chkrow."DDDDD";
                if($chkrow<1)
                {
                  $sql = "INSERT IGNORE INTO  cscan_trend_report_search SET userID='".$sess_userID."',searchKey='',userType='".$sess_userType."',sectorID='".$sector_id."',categoryID='".$category_id."',subCategoryID='".$subcategory_id."',subSubCategoryID='".$subtosubcategory_id."',country='".$country_id."',emailAlert='1',alert_type=1";
                  $save_data=$DRW->query($sql,$DRW_main);
                }else{
                    $row_records_dup = $DRW->fetch_array($chk_rs);
                    $mail_alert=$row_records_dup[1];
                    $alertID=$row_records_dup[0];
                    if($mail_alert==0){
                        $sqlupdt = "Update cscan_trend_report_search SET emailAlert=1 where ID='$alertID' AND (searchKey='' OR searchKey IS NULL)";
                        $save_data_updt=$DRW->query($sqlupdt,$DRW_main);                        
                    }
                    //echo "2"; exit;
                }
            }

        }else{
            if (($key = array_search($chkcatid, $currArray)) !== false) {
                unset($currArray[$key]);
            }
            //unset($currArray[$chkcatid]);
        }
    }
    //print_r($currArray);
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
                }elseif ($num==3) {
                    $subcategory_id =$sectorID;
                    $searchQuery=" AND subCategoryID=".$subcategory_id;
                }elseif ($num==4) {
                    $subtosubcategory_id =$sectorID;
                    $searchQuery=" AND subSubCategoryID=".$subtosubcategory_id;
                }
                $num++;
            }
            if(count($updated_array)>0){
                $updated_id=implode(',',$updated_array);
                $sqlDelete = "delete from cscan_trend_report_search where userID='".$sess_userID."' AND country='".$country_id."' AND (searchKey='' OR searchKey IS NULL) AND ID NOT IN(".$updated_id.")  $searchQuery";
            }else{
                $sqlDelete = "delete from cscan_trend_report_search where userID='".$sess_userID."' AND country='".$country_id."' AND (searchKey='' OR searchKey IS NULL) $searchQuery";
            }            
            $resp=$DRW->query($sqlDelete, $DRW_main);   
        }
    } 
    //if($save_data){
        echo "1";exit;
    //}  
}
?>