<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

ini_set("default_charset", "utf-8");
ini_set("memory_limit", "-1");
set_time_limit(0);

include_once('includes/dbcon.php');
$DRW->databaseReadWrite_die = 1;
require_once('includes/clean.php');
require_once('includes/functions.php');

$sql = "SELECT userID,firstName,lastName,emailAddress,companyName from cscan_users where active='y' ORDER BY userID ASC ";
$savedQuery = $DRW->query($sql,$DRW_read);
echo 'Running...';
while($row = $DRW->fetch_row($savedQuery)) {
    
    $userID = $row[0];    
    $sql_check = "select userID from cscan_sector_user_temp where userID='".$userID."'";
    $query_check = $DRW->query($sql_check, $DRW_read);
    $emailAddress=$companyName='';
    if ($DRW->num_rows($query_check) <= 0) {    
    
        $firstName = $row[1];
        $lastName = $row[2];
        $emailAddress=$row[3];
        $companyName=$row[4];
        $subCat_name_arr=$cat_name_arr=$sector_name_arr=array();

        $sql2 = "SELECT ss.sectorID,ss.sectorName FROM cscan_sector ss,cscan_sector_users_allow sa WHERE sa.userID='{$userID}' AND sa.sectorID=ss.sectorID AND ss.parentID=0 ORDER BY ss.sectorName ASC";

        $savedQuery2 = $DRW->query($sql2,$DRW_read);
        while($row2 = $DRW->fetch_row($savedQuery2)) {
                $sectorID=$row2[0];
                $sector_name_arr[]=$row2[1];
                $sql3 = "SELECT ss.sectorID,ss.sectorName FROM cscan_sector ss,cscan_sector_users_allow sa WHERE sa.userID='{$userID}' AND sa.sectorID='{$sectorID}' AND ss.parentID={$sectorID} ORDER BY ss.sectorName ASC";

               $savedQuery3 = $DRW->query($sql3,$DRW_read);

                while($row3 = $DRW->fetch_row($savedQuery3)) {
                    $catID=$row3[0];
                    $cat_name_arr[]=$row3[1];
                    $sql4 = "SELECT ss.sectorID,ss.sectorName FROM cscan_sector ss,cscan_sector_users_allow sa WHERE sa.userID='{$userID}' AND sa.sectorID='{$catID}' AND ss.parentID={$catID}  ORDER BY ss.sectorName ASC";
                    $savedQuery4 = $DRW->query($sql4,$DRW_read);
                    while($row4 = $DRW->fetch_row($savedQuery4)) {
                        $subCatID=$row4[0];
                        $subCat_name_arr[]=$row4[1];
                    }

                }
        }
        $all_sector_name=$all_cat_name=$all_subcat_name='';
        if(!empty($sector_name_arr)){
            $all_sector_name= implode(', ', $sector_name_arr);
        }
        if(!empty($cat_name_arr)){
            $all_cat_name= implode(', ', $cat_name_arr);
        }
        if(!empty($subCat_name_arr)){
            $all_subcat_name= implode(', ', $subCat_name_arr);
        }
        
        $sql = "INSERT IGNORE INTO cscan_sector_user_temp (userID,emailAddress,companyName,sectorName,categoryName,subCategoryName) VALUES ('".(int)$userID."','".$emailAddress."','".addslashes($companyName)."','".addslashes($all_sector_name)."','".addslashes($all_cat_name)."','".addslashes($all_subcat_name)."')";
        $DRW->query($sql,$DRW_main);
        
    }
        
}
echo 'Completed';
?>