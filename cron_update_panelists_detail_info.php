#!/usr/bin/php
<?php
require_once ("includes/dbcon.php");
$DRW->databaseReadWrite_die = 1;
include_once 'includes/functions.php';
ini_set("memory_limit", "-1");
set_time_limit(0);
date_default_timezone_set('America/Chicago');
echo "Start Date Time : ".date('Y-m-d h:i:s');

$sql_Panelists_Product= "Select panelist_id,productID from cscan_panelists_product where ppageID = '0' AND productID > '0' AND ppage = '0' AND ppstateID = '0' AND panelist_id > '0' AND pincomeID = '0' Limit 500";
$result_panelist_product_sql=$DRW->query($sql_Panelists_Product,$DRW_read2);
if ($DRW->num_rows($result_panelist_product_sql) > 0) { 
     while ($rowPanelistProductData = $DRW->fetch_assoc($result_panelist_product_sql)) {
        $panelistID  = $rowPanelistProductData['panelist_id'];
        $productID   = $rowPanelistProductData['productID'];
        //$ppdate      = $rowPanelistProductData['ppdate'];
        $sql_Panelists= "Select DATEDIFF(CURDATE(),birthdate) as agedays,gender,incomeID,stateID,homeownershipID,fico_score,ownbiz,postalcode,parent_panelist_id from cscan_panelists where panelist_id = '".$panelistID."'";
        $result_panelists_sql=$DRW->query($sql_Panelists,$DRW_read2);
        if ($DRW->num_rows($result_panelists_sql) > 0) { 
             $rowPanelistsData = $DRW->fetch_row($result_panelists_sql);
             $ppage = floor($rowPanelistsData[0] / 365);
             $ageObj = new \HS\Age($DRW);
             $ageObj->setAge($ppage);
             $ppageID = $ageObj->getGroupsAsCommaDelimitedString($ppage);
             $ppageID = str_replace("'", '', $ppageID);
             $pgender = strtoupper(substr(trim($rowPanelistsData[1]), 0, 1));
             $pincomeID = (int) $rowPanelistsData[2];
             $ppstateID = (int) $rowPanelistsData[3];
             $phomeownershipID = (int) $rowPanelistsData[4];
             $ppfico_score = (int) $rowPanelistsData[5];
             $ownbiz = (int) $rowPanelistsData[6];
             $pppostalcode = trim($rowPanelistsData[7]);
             $parent_panelist_id = $rowPanelistsData[8];
             $pprimary = ($parent_panelist_id > 0) ? 0 : 1;
             $sql_updt_panelist_product= "UPDATE cscan_panelists_product set ppage='".$ppage."',ppstateID='".$ppstateID."',pgender='".$pgender."',pincomeID='".$pincomeID."',ppageID='".$ppageID."',homeownershipID='".$phomeownershipID."',ppfico_score='".$ppfico_score."',isBiz='".$ownbiz."',pppostalcode='".$DRW->real_escape_string($pppostalcode)."',pprimary='".$pprimary."' where panelist_id='".$panelistID."' AND productID='".$productID."'";
             $DRW->query($sql_updt_panelist_product,$DRW_main); 
             $sql_Product= "Select productID,age,incomeID from cscan_product_detail where productID = '".$productID."'";
             $result_product_sql=$DRW->query($sql_Product,$DRW_read2);
             if ($DRW->num_rows($result_product_sql) > 0) {
                 $rowProductData = $DRW->fetch_row($result_product_sql);
                 $age = $rowProductData[1];
                 $expAge = explode(',',$age);
                 if($ppageID!='' && !empty($expAge)){
                    if (!in_array($ppageID, $expAge)){
                         $age = $age.','.$ppageID;
                        } 
                 }
                 $age=ltrim($age,',');
                 $age=rtrim($age,',');
                 $incomeID = $rowProductData[2];
                 $expIncome = explode(',',$incomeID);
                 if($pincomeID!='' && !empty($expIncome)){
                    if (!in_array($pincomeID, $expIncome)){
                        $incomeID= $incomeID.','.$pincomeID; 
                    } 
                   
                 }
                 $incomeID=ltrim($incomeID,',');
                 $incomeID=rtrim($incomeID,',');
                 $sql_updt_product_detail= "UPDATE cscan_product_detail set age='".$age."',incomeID='".$incomeID."' where productID='".$productID."'";
                 $DRW->query($sql_updt_product_detail,$DRW_main); 
                 
             } 
             
        }
    }
}
echo 'Completed';  
echo "End Date Time : ".date('Y-m-d h:i:s');
?>
