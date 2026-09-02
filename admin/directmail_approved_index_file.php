<?php
date_default_timezone_set('America/Chicago');

$root = dirname(__FILE__);
if (strpos($root, '/admin') !== false) {
    $root = substr($root, 0, strpos($root, '/admin'));
}
require_once($root."/includes/ehLog_set.php");
$ehL->start(__FILE__);
ini_set("memory_limit","-1");
set_time_limit(0);
require_once($root."/includes/dbcon.php");
require_once $root.'/includes/functions.php';
$current_date=date('Y-m-d');
?>
<?php 
//$sql="SELECT productID FROM cscan_product_detail WHERE (productStatus=1) AND addedToDatabase>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 6 MONTH),' 00:00:00') AND mChannelID in (1)";
//$sql="SELECT productID FROM cscan_product_detail WHERE (productStatus=1) AND addedToDatabase>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 12 MONTH),' 00:00:00') AND mChannelID in (1) AND  NOT FIND_IN_SET('87',`sectorID`) AND  NOT FIND_IN_SET('90',`sectorID`) AND  NOT FIND_IN_SET('6',`sectorID`) AND mTypeID != 3";
//$sql="SELECT productID FROM cscan_product_detail WHERE (productStatus=1) AND addedToDatabase>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 12 MONTH),' 00:00:00') AND actual_addedToDatabase > '2018-02-21 00:00:00' AND mChannelID in (1) AND  NOT FIND_IN_SET('87',`sectorID`) AND  NOT FIND_IN_SET('90',`sectorID`) AND  NOT FIND_IN_SET('6',`sectorID`)";
//$sql = "SELECT productID, addedToDatabase FROM cscan_product_detail WHERE (productStatus=1) AND addedToDatabase>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 36 MONTH),' 00:00:00') AND addedToDatabase < '2017-02-09 00:09:41' AND mChannelID in (1) AND  NOT FIND_IN_SET('87',`sectorID`) AND  NOT FIND_IN_SET('90',`sectorID`) AND  NOT FIND_IN_SET('6',`sectorID`) ORDER BY addedToDatabase ASC";
//echo $sql;die;
//$resultC = $DRW->query($sql, $DRW_read); 
//while ($dataC = $DRW->fetch_row($resultC)) {
//    if($dataC[0]){
//        $productid=$dataC[0];
//        $chk_sql = "SELECT id FROM cscan_dmapprovedpdf WHERE product_id '".$productid."'";
//        $q_chk = $DRW->query($chk_sql, $DRW_read); 
//        if($DRW->num_rows( $q_chk ) == 0){
//            copydmApprovedPdf($productid, '2018-02-01_index_input.csv');
//        }
//    }
//}
//echo 'Successfully Done';die;



//### Enable below code when maxmail indexing need to be performed ####
//$sql = "SELECT productID FROM cscan_product_detail WHERE productStatus=1 AND addedToDatabase>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 3 MONTH),' 00:00:00') AND mChannelID in (3)";
//echo $sql;
//$resultC = $DRW->query($sql, $DRW_read2); 
//if($DRW->num_rows($resultC)>0){
//    while ($dataC = $DRW->fetch_assoc($resultC)) {
//        if(!empty($dataC['productID'])){
//            $productid = $dataC['productID'];
//            copydaMaxmailApprovedPdf($productid, 'onetime_index_input.csv');
//        }
//    }
//    echo 'done';die;
//}
//

## Enable below code when Chicago FTP indexing needed ##
$sql = "SELECT p.productID FROM cscan_product_detail p
INNER JOIN cscan_document d ON (p.productID = d.productID)
WHERE p.productStatus=1
AND p.actual_addedToDatabase>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 3 MONTH),' 00:00:00')
AND p.mChannelID = 1
AND p.mTypeID != 3
AND p.subCategoryID NOT IN (415,432,127,71)
AND NOT FIND_IN_SET (87, p.sectorID) AND NOT FIND_IN_SET (90, p.sectorID) AND NOT FIND_IN_SET (6, p.sectorID)
AND ((NOT FIND_IN_SET('Liberty Mutual', p.company) && p.affinityAssociation=0) OR (NOT FIND_IN_SET('Liberty Mutual', p.secondCompany) && p.affinityAssociation=0) OR (NOT FIND_IN_SET('Nationwide', p.company) && p.affinityAssociation=0) OR (NOT FIND_IN_SET('Nationwide', p.secondCompany) && p.affinityAssociation=0))
AND d.document_id = 1
AND approved_date<'".$current_date."'
ORDER BY p.actual_addedToDatabase ASC";

//AND p.actual_addedToDatabase < '2018-08-13 04:30:30'
//
//echo $sql;
$resultC = $DRW->query($sql, $DRW_read); 
while ($dataC = $DRW->fetch_assoc($resultC)) {
    if(!empty($dataC['productID'])){
        $productid = $dataC['productID'];
        daApprovedCsv($productid, 'citi_onetime_index_input.csv');
    }
}

?>