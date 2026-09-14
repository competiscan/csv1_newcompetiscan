<?php ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set("memory_limit", "-1");
set_time_limit(0);
require_once("includes/dbcon.php");

//ALTER TABLE `cscan_product_detail` ADD `is_panelist_score` TINYINT NOT NULL DEFAULT '0' AFTER `packageTypeId`;

$sql_update="Update cscan_product_detail set is_panelist_score='0' where is_panelist_score='1'";
$result=$DRW->query($sql_update, $DRW_main);
//$sql_sel="SELECT DISTINCT pd.productID FROM cscan_product_detail pd LEFT JOIN cscan_panelists_product pp ON (pd.productID=pp.productID) JOIN cscan_panelists_additional_score cpads ON (cpads.panelist_id=pp.panelist_id) WHERE (productStatus=1) AND (cpads.fico_score!='' OR cpads.credit_vision!='' OR cpads.vantage_score!='') AND LEFT(cpads.score_date, 7)=LEFT(pp.ppdate, 7)";
$sql_sel="SELECT DISTINCT pp.productID FROM cscan_panelists_product pp JOIN cscan_panelists_additional_score cpads ON (cpads.panelist_id=pp.panelist_id) WHERE (cpads.fico_score!='' OR cpads.credit_vision!='' OR cpads.vantage_score!='') AND LEFT(cpads.score_date, 7)=LEFT(pp.ppdate, 7)";
$query = $DRW->query($sql_sel,$DRW_read2);
$num = $DRW->num_rows( $query );
if($num > 0){    
    while( $row = $DRW->fetch_assoc($query)){
        $productID = $row['productID'];
        $sql_update2="Update cscan_product_detail set is_panelist_score='1' where productID='".$productID."' AND productStatus=1";
        $result=$DRW->query($sql_update2, $DRW_main);
    }
}
echo 'Completed panelist score status';
?>