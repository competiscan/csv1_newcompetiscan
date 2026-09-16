<?php
require_once("includes/competi_def.php");
require_once('includes/dbcon.php');
require_once('includes/functions.php'); 
date_default_timezone_set('America/Chicago');
$today_date = date('Y-m-d');
echo 'Start Time: '.date("Y-m-d H:i:s").'<br/><br/>';
$sect_consumer_array=array(559,561,562,563,564,565,566,567,728,903);
$sect_non_profit_array=array(560,568,569,570,571,572,573,574,575,577,729);
//$sect_array=array(266,267,269,270,271,272,405,406,407,408,409,410,434,535,578,579,580,581,583,584,586,587,588,589,590,591,592,593,594,595,596,597,598,599,600,601,602,603,604,605,606,607,608,609,610,611,612,613,614,615,616,617,618,619,620,621,622,623,624,625,626,627,628,629,630,631,632,633,634,635,636,637,638,640,641,642,643,644,645,646,647,648,650,653,654,655,656,657,658,659,660,661,662,663,664,665,666,667,668,672,674,733,734,735,737,739,811,818);


/*$sql_sect_allow="SELECT userID FROM cscan_temp_retail_user WHERE id >0 and id <100";
$rs_sect_query = $DRW->query($sql_sect_allow,$DRW_read);
while($rowData_sect = $DRW->fetch_array($rs_sect_query)) {
       $userID=$rowData_sect['userID'];
       foreach($sect_array as $sectorID){
        $sqlInsert_query="REPLACE into cscan_sector_users_allow set sectorID='$sectorID',	userID='$userID'";
        $resp_query=$DRW->query($sqlInsert_query,$DRW_main);

       }

}

$sql_sect_allow_consumer="SELECT DISTINCT userID
FROM cscan_sector_users_allow
WHERE sectorID=328";
$rs_sect_query_consumer = $DRW->query($sql_sect_allow_consumer,$DRW_read);
while($rowData_sectCons = $DRW->fetch_array($rs_sect_query_consumer)) {
       $userID=$rowData_sectCons['userID'];
       foreach($sect_consumer_array as $sectorID){
        $sqlInsert_query_cons="REPLACE into cscan_sector_users_allow set sectorID='$sectorID',	userID='$userID'";
        $resp_query_cons=$DRW->query($sqlInsert_query_cons,$DRW_main);

       }

}
echo "Done"; die;*/

$sql_sect_no_profit="SELECT DISTINCT userID
FROM cscan_sector_users_allow
WHERE sectorID =541";
$rs_sect_query_non = $DRW->query($sql_sect_no_profit,$DRW_read);
while($rowData_sectNon = $DRW->fetch_array($rs_sect_query_non)) {
       $userID=$rowData_sectNon['userID'];
       foreach($sect_non_profit_array as $sectorID){
        $sqlInsert_query_non="REPLACE into cscan_sector_users_allow set sectorID='$sectorID',	userID='$userID'";
        $resp_query_non=$DRW->query($sqlInsert_query_non,$DRW_main);

       }

}
echo "Done"; die;
/*#########################ADMIN USERS#################################*/
/*$sql_sect_allow="SELECT userID
FROM cscan_sector_admin_users_allow
WHERE sectorID=266";
$rs_sect_query = $DRW->query($sql_sect_allow,$DRW_read);
while($rowData_sect = $DRW->fetch_array($rs_sect_query)) {
       $userID=$rowData_sect['userID'];
       foreach($sect_array as $sectorID){
        $sqlInsert_query="REPLACE into cscan_sector_admin_users_allow set sectorID='$sectorID',	userID='$userID'";
        $resp_query=$DRW->query($sqlInsert_query,$DRW_main);

       }

}

$sql_sect_allow_consumer="SELECT userID
FROM cscan_sector_admin_users_allow
WHERE sectorID=559";
$rs_sect_query_consumer = $DRW->query($sql_sect_allow_consumer,$DRW_read);
while($rowData_sectCons = $DRW->fetch_array($rs_sect_query_consumer)) {
       $userID=$rowData_sectCons['userID'];
       foreach($sect_consumer_array as $sectorID){
        $sqlInsert_query_cons="REPLACE into cscan_sector_admin_users_allow set sectorID='$sectorID',	userID='$userID'";
        $resp_query_cons=$DRW->query($sqlInsert_query_cons,$DRW_main);

       }

}

$sql_sect_no_profit="SELECT userID
FROM cscan_sector_admin_users_allow
WHERE sectorID =560";
$rs_sect_query_non = $DRW->query($sql_sect_no_profit,$DRW_read);
while($rowData_sectNon = $DRW->fetch_array($rs_sect_query_non)) {
       $userID=$rowData_sectNon['userID'];
       foreach($sect_non_profit_array as $sectorID){
        $sqlInsert_query_non="REPLACE into cscan_sector_admin_users_allow set sectorID='$sectorID',	userID='$userID'";
        $resp_query_non=$DRW->query($sqlInsert_query_non,$DRW_main);

       }

}*/

//echo "Allowed Sector categry"; exit;
/*#####################################################
function trim_array($Array)
{
    foreach ($Array as $value) {
        if(trim($value) === '') {
            $index = array_search($value, $Array);
            unset($Array[$index]);
        }
    }
    return $Array;
}
$sql_query="SELECT  ID,s.userID,CONCAT_WS(',',sectorID,categoryID, subCategoryID, subSubCategoryID) categoryID
FROM cscan_search as s
JOIN cscan_users as u on (u.userID=s.userID)
WHERE sectorID LIKE '%266%' AND u.userID=45037";
$rs_query = $DRW->query($sql_query,$DRW_read);
$sectorNamePrint='';
$sectorName='';
$subsubcatName='';
$catName ='';
$subcatName='';
$subsubcatName='';
while($rowData = $DRW->fetch_array($rs_query)) {
       $s_id=$rowData['ID'];
       $user_id=$rowData['userID'];
       $sector_id=$rowData['categoryID'];
       if($sector_id!='' and !empty($sector_id)){
          $exp_sect_array=explode(',',$sector_id);
          $exp_sect_farray=trim_array($exp_sect_array);
          if(!empty($exp_sect_farray)){
            $num_data=1;
            foreach($exp_sect_farray as $sect_data_id){
              $category_id=$sect_data_id;
              $sql ="SELECT level,c.* FROM ( SELECT @r AS _id, (SELECT @r := parentID FROM cscan_sector_bk_20230908 WHERE sectorID = _id) AS parent_id, @l := @l + 1 AS level FROM (SELECT @r := '$category_id', @l := 0) vars, cscan_sector_bk_20230908 m WHERE @r <> 0) d JOIN cscan_sector_bk_20230908 c ON d._id = c.sectorID  order By level DESC";
              $rs = $DRW->query($sql,$DRW_read);
              $resultCount = $DRW->num_rows($rs);
                $num=1;
                if($resultCount > 0) {
                    while($row = $DRW->fetch_assoc($rs)) {
                      $sectorID = $row['sectorID'];
                      $level=$row['level'];
                      $sectorNamePrint = $sectorID;
                          if($num==1){
                              $sectorName =$sectorID;
                              //echo "Sector=====".$sectorNamePrint."<br/>";
                          }elseif ($num==2) {
                            $catName =$sectorID;
                            //echo "Category=====".$sectorNamePrint."<br/>";
                          }
                          elseif ($num==3) {
                            $subcatName =$sectorID;
                            //echo "SUBCategory=====".$sectorNamePrint."<br/>";
                          }elseif ($num==4) {
                              $subsubcatName =$sectorID;
                              //echo "SUBSUBCategory=====".$sectorNamePrint."<br/>";
                              }
                              
                        
                    $num++;}
                 
                              
                }

                $chkquery="select * from cscan_search_save_cat_sequence where s_id='$s_id' AND user_id='$user_id' And sector_id ='$sectorName'";
                $sqlInsert="Insert into cscan_search_save_cat_sequence set s_id='$s_id',	user_id='$user_id',sector_id ='$sectorName',category_id='$catName',subcategory_id='$subcatName',subtosubcategory_id='$subsubcatName',	sequence_no='$num_data'";
                $resp=$DRW->query($sqlInsert,$DRW_main);
                $sectorName='';
                $catName='';
                $subcatName='';
                $subsubcatName='';
                $num_data++;
              }
          }
      }
}
echo '</br></br>End TIME: '.date("Y-m-d H:i:s");
die;
/*
$sql_query = "SELECT  ID,s.userID,sectorID,categoryID,subCategoryID,subSubCategoryID
FROM cscan_search as s
JOIN cscan_users as u on (u.userID=s.userID)
WHERE 
sectorID LIKE '%266%'  
AND categoryID!='' 
AND subCategoryID='' 
AND subSubCategoryID=''  
AND active='y'";
$rs_query = $DRW->query($sql_query,$DRW_read);
$sectorNamePrint='';
$sectorName='';
$subsubcatName='';
$catName ='';
$subcatName='';
$subsubcatName='';
while($rowData = $DRW->fetch_array($rs_query)) {
       $s_id=$rowData['ID'];
       $user_id=$rowData['userID'];
       $sector_id=$rowData['categoryID'];
       if($sector_id!='' and !empty($sector_id)){
          $exp_sect_array=explode(',',$sector_id);
          if(!empty($exp_sect_array)){
            $num_data=1;
            foreach($exp_sect_array as $sect_data_id){
              $category_id=$sect_data_id;
              $sql ="SELECT level,c.* FROM ( SELECT @r AS _id, (SELECT @r := parentID FROM cscan_sector_bk_20230908 WHERE sectorID = _id) AS parent_id, @l := @l + 1 AS level FROM (SELECT @r := '$category_id', @l := 0) vars, cscan_sector_bk_20230908 m WHERE @r <> 0) d JOIN cscan_sector_bk_20230908 c ON d._id = c.sectorID  order By level DESC";
              $rs = $DRW->query($sql,$DRW_read);
              $resultCount = $DRW->num_rows($rs);
                $num=1;
                if($resultCount > 0) {
                    while($row = $DRW->fetch_assoc($rs)) {
                      $sectorID = $row['sectorID'];
                      $level=$row['level'];
                      $sectorNamePrint = $sectorID;
                          if($num==1){
                              $sectorName =$sectorID;
                              //echo "Sector=====".$sectorNamePrint."<br/>";
                          }elseif ($num==2) {
                            $catName =$sectorID;
                            //echo "Category=====".$sectorNamePrint."<br/>";
                          }
                          elseif ($num==3) {
                            $subcatName =$sectorID;
                            //echo "SUBCategory=====".$sectorNamePrint."<br/>";
                          }elseif ($num==4) {
                              $subsubcatName =$sectorID;
                              //echo "SUBSUBCategory=====".$sectorNamePrint."<br/>";
                              }
                              
                        
                    $num++;}
                              
                }
                $sqlInsert="Insert into cscan_search_save_cat_sequence set s_id='$s_id',	user_id='$user_id',sector_id ='$sectorName',category_id='$catName',subcategory_id='$subcatName',subtosubcategory_id='$subsubcatName',	sequence_no='$num_data'";
                $resp=$DRW->query($sqlInsert,$DRW_main);
                $sectorName='';
                $catName='';
                $subcatName='';
                $subsubcatName='';
                $num_data++;
              }
          }
      }
}

$sql_query = "SELECT  ID,s.userID,sectorID,categoryID,subCategoryID,subSubCategoryID
FROM cscan_search as s
JOIN cscan_users as u on (u.userID=s.userID)
WHERE 
sectorID LIKE '%266%'  
AND categoryID!='' 
AND subCategoryID!='' 
AND subSubCategoryID=''  
AND active='y'";
$rs_query = $DRW->query($sql_query,$DRW_read);
$sectorNamePrint='';
$sectorName='';
$subsubcatName='';
$catName ='';
$subcatName='';
$subsubcatName='';
while($rowData = $DRW->fetch_array($rs_query)) {
       $s_id=$rowData['ID'];
       $user_id=$rowData['userID'];
       $sector_id=$rowData['subCategoryID'];
       if($sector_id!='' and !empty($sector_id)){
          $exp_sect_array=explode(',',$sector_id);
          if(!empty($exp_sect_array)){
            $num_data=1;
            foreach($exp_sect_array as $sect_data_id){
              $category_id=$sect_data_id;
              $sql ="SELECT level,c.* FROM ( SELECT @r AS _id, (SELECT @r := parentID FROM cscan_sector_bk_20230908 WHERE sectorID = _id) AS parent_id, @l := @l + 1 AS level FROM (SELECT @r := '$category_id', @l := 0) vars, cscan_sector_bk_20230908 m WHERE @r <> 0) d JOIN cscan_sector_bk_20230908 c ON d._id = c.sectorID  order By level DESC";
              $rs = $DRW->query($sql,$DRW_read);
              $resultCount = $DRW->num_rows($rs);
                $num=1;
                if($resultCount > 0) {
                    while($row = $DRW->fetch_assoc($rs)) {
                      $sectorID = $row['sectorID'];
                      $level=$row['level'];
                      $sectorNamePrint = $sectorID;
                          if($num==1){
                              $sectorName =$sectorID;
                              //echo "Sector=====".$sectorNamePrint."<br/>";
                          }elseif ($num==2) {
                            $catName =$sectorID;
                            //echo "Category=====".$sectorNamePrint."<br/>";
                          }
                          elseif ($num==3) {
                            $subcatName =$sectorID;
                            //echo "SUBCategory=====".$sectorNamePrint."<br/>";
                          }elseif ($num==4) {
                              $subsubcatName =$sectorID;
                              //echo "SUBSUBCategory=====".$sectorNamePrint."<br/>";
                              }
                              
                        
                    $num++;}
                              
                }
                
                $sqlInsert="Insert into cscan_search_save_cat_sequence set s_id='$s_id',	user_id='$user_id',sector_id ='$sectorName',category_id='$catName',subcategory_id='$subcatName',subtosubcategory_id='$subsubcatName',	sequence_no='$num_data'";
                $resp=$DRW->query($sqlInsert,$DRW_main);
                $sectorName='';
                $catName='';
                $subcatName='';
                $subsubcatName='';
                $num_data++;
              }
          }
      }
}

/*$sql_query = "SELECT  ID,s.userID,sectorID,categoryID,subCategoryID,subSubCategoryID
FROM cscan_search as s
JOIN cscan_users as u on (u.userID=s.userID)
WHERE 
sectorID LIKE '%266%'
AND categoryID!='' 
AND subCategoryID!='' 
AND subSubCategoryID!=''  
AND active='y'";
$rs_query = $DRW->query($sql_query,$DRW_read);
$sectorNamePrint='';
$sectorName='';
$subsubcatName='';
$catName ='';
$subcatName='';
$subsubcatName='';
while($rowData = $DRW->fetch_array($rs_query)) {
       $s_id=$rowData['ID'];
       $user_id=$rowData['userID'];
       $sector_id=$rowData['subSubCategoryID'];
       if($sector_id!='' and !empty($sector_id)){
          $exp_sect_array=explode(',',$sector_id);
          if(!empty($exp_sect_array)){
            $num_data=1;
            foreach($exp_sect_array as $sect_data_id){
              $category_id=$sect_data_id;
              $sql ="SELECT level,c.* FROM ( SELECT @r AS _id, (SELECT @r := parentID FROM cscan_sector_bk_20230908 WHERE sectorID = _id) AS parent_id, @l := @l + 1 AS level FROM (SELECT @r := '$category_id', @l := 0) vars, cscan_sector_bk_20230908 m WHERE @r <> 0) d JOIN cscan_sector_bk_20230908 c ON d._id = c.sectorID  order By level DESC";
              $rs = $DRW->query($sql,$DRW_read);
              $resultCount = $DRW->num_rows($rs);
                $num=1;
                if($resultCount > 0) {
                    while($row = $DRW->fetch_assoc($rs)) {
                      $sectorID = $row['sectorID'];
                      $level=$row['level'];
                      $sectorNamePrint = $sectorID;
                          if($num==1){
                              $sectorName =$sectorID;
                              //echo "Sector=====".$sectorNamePrint."<br/>";
                          }elseif ($num==2) {
                            $catName =$sectorID;
                            //echo "Category=====".$sectorNamePrint."<br/>";
                          }
                          elseif ($num==3) {
                            $subcatName =$sectorID;
                            //echo "SUBCategory=====".$sectorNamePrint."<br/>";
                          }elseif ($num==4) {
                              $subsubcatName =$sectorID;
                              //echo "SUBSUBCategory=====".$sectorNamePrint."<br/>";
                              }
                              
                        
                    $num++;}
                              
                }
                $sqlInsert="Insert into cscan_search_save_cat_sequence set s_id='$s_id',	user_id='$user_id',sector_id ='$sectorName',category_id='$catName',subcategory_id='$subcatName',subtosubcategory_id='$subsubcatName',	sequence_no='$num_data'";
                $resp=$DRW->query($sqlInsert,$DRW_main);
                $sectorName='';
                $catName='';
                $subcatName='';
                $subsubcatName='';
                $num_data++;
              }
          }
      }
}
echo '</br></br>End TIME: '.date("Y-m-d H:i:s");
die;
*/
/*$sql_query = "SELECT trend_id,rndtrend_id,trend_name,file_path,file_name,audience_id,country_id,trend_date FROM cscan_trend_report";	
$rs_query = $DRW->query($sql_query,$DRW_read);
$sectorName='';
$categoryName='';
$subCategoryName='';
$subsubCategoryName='';
$arrExport = array();
$resultCount = $DRW->num_rows($rs_query);
if($resultCount > 0) {
    $arrExport['data'][] = array("Trend ID","Trend Name", "Upload Date","Sector","Category","Sub Category","Sub Sub Category","URL Link");
    while($rowData = $DRW->fetch_array($rs_query)) {
      $trend_id=$rowData['trend_id'];
      $rndtrend_id=$rowData['rndtrend_id'];
      $trend_name = $DRW->real_escape_string($rowData['trend_name']);
      $file_path=$rowData['file_path'];
      $file_name=$rowData['file_name'];
      $full_path=$displays3URL.$file_path.$file_name;
      $trend_date=$rowData['trend_date'];
      $sql_cat = "SELECT sector_id,category_id,subcategory_id,subtosubcategory_id FROM cscan_trends_category WHERE trend_id='".$trend_id."'";
      $rs_cat_query = $DRW->query($sql_cat,$DRW_read);
      $sectorName='';
      $categoryName='';
      $subCategoryName='';
      $subsubCategoryName='';
      while($rowDataCat = $DRW->fetch_array($rs_cat_query)) {
            if(!empty($rowDataCat['sector_id']) && $rowDataCat['sector_id']!=''){
             $sectorName .= sectorName($rowDataCat['sector_id']).", ";   
            }
            if(!empty($rowDataCat['category_id']) && $rowDataCat['category_id']!=''){
             $categoryName .= categoryName($rowDataCat['category_id']).", ";   
            }
            if(!empty($rowDataCat['subcategory_id']) && $rowDataCat['subcategory_id']!=''){
             $subCategoryName .= subCategoryName($rowDataCat['subcategory_id']).", ";  
            }
            if(!empty($rowDataCat['subtosubcategory_id']) && $rowDataCat['subtosubcategory_id']!=''){
             $subsubCategoryName .= subCategoryName($rowDataCat['subtosubcategory_id']).", ";  
            }
            
            

        }
        $sectorName = implode(", ",array_unique(explode(", ",$sectorName)));
        $sectorName=rtrim($sectorName, ", ");
        
        $categoryName = implode(", ",array_unique(explode(", ",$categoryName)));
        $categoryName=rtrim($categoryName, ", ");
        
        $subCategoryName = implode(", ",array_unique(explode(", ",$subCategoryName)));
        $subCategoryName=rtrim($subCategoryName, ", ");
        
        $subsubCategoryName = implode(", ",array_unique(explode(", ",$subsubCategoryName)));
        $subsubCategoryName=rtrim($subsubCategoryName, ", ");
            
       
        $arrExport['data'][] = array($trend_id, $trend_name,$trend_date,$sectorName,$categoryName,$subCategoryName,$subsubCategoryName,$full_path);
    }
    download_send_headers("trend_report_" . date("Y-m-d") .rand(). ".csv");
    echo array2csv($arrExport);
    die();
}
function array2csv(array &$array){
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
exit;
*/
/*SELECT  ID,sectorID,categoryID,subCategoryID,subSubCategoryID
    FROM cscan_search as s
    JOIN cscan_users as u on (u.userID=s.userID)
    WHERE 
u.userID = '45037' AND
    	    sectorID LIKE '%266%' AND
          active='y' * 
$sql_query = "SELECT trend_id,category_id,trend_name,trend_link,trend_date FROM cscan_trend_report where category_id!=0 order by trend_id  ASC";	
$rs_query = $DRW->query($sql_query,$DRW_read);
$sectorNamePrint='';
$sectorName='';
$subsubcatName='';
$catName ='';
$subcatName='';
$subsubcatName='';
while($rowData = $DRW->fetch_array($rs_query)) {
       $trend_id=$rowData['trend_id'];
       $category_id=$rowData['category_id'];
       $trend_name = $DRW->real_escape_string($rowData['trend_name']);
       $trend_link=$rowData['trend_link'];
       $trend_date=$rowData['trend_date'];
       $sql ="SELECT level,c.* FROM ( SELECT @r AS _id, (SELECT @r := parentID FROM cscan_sector WHERE sectorID = _id) AS parent_id, @l := @l + 1 AS level FROM (SELECT @r := '$category_id', @l := 0) vars, cscan_sector m WHERE @r <> 0) d JOIN cscan_sector c ON d._id = c.sectorID  order By level DESC";
       //$sql = "SELECT sectorID,sectorName FROM cscan_sector WHERE parentID='$category_id' ORDER BY sectorName ASC";	
       $rs = $DRW->query($sql,$DRW_read);
       $resultCount = $DRW->num_rows($rs);
        $num=1;
        if($resultCount > 0) {
                while($row = $DRW->fetch_assoc($rs)) {
                    $sectorID = $row['sectorID'];
                    $level=$row['level'];
			$sectorNamePrint = $sectorID;
                        if($num==1){
                            $sectorName =$sectorID;
                            //echo "Sector=====".$sectorNamePrint."<br/>";
                        }elseif ($num==2) {
                          $catName =$sectorID;
                          //echo "Category=====".$sectorNamePrint."<br/>";
                         }
                         elseif ($num==3) {
                          $subcatName =$sectorID;
                           //echo "SUBCategory=====".$sectorNamePrint."<br/>";
                         }elseif ($num==4) {
                             $subsubcatName =$sectorID;
                            //echo "SUBSUBCategory=====".$sectorNamePrint."<br/>";
                            }
                      
                
            $num++;}
                       
        }
        $sqlchk = "SELECT trend_id FROM cscan_trends_category where trend_id='$trend_id'";
        $rs_chk = $DRW->query($sqlchk,$DRW_read);
        $rowCount = $DRW->num_rows($rs_chk);
        if($rowCount==0){
        $sqlInsert="Insert into cscan_trends_category set sector_id ='$sectorName',category_id='$catName',subcategory_id='$subcatName',	subtosubcategory_id='$subsubcatName',trend_id='$trend_id'";
        $resp=$DRW->query($sqlInsert,$DRW_main);
        }
        $sectorName='';
        $catName='';
        $subcatName='';
        $subsubcatName='';
      }
   */ 

?>