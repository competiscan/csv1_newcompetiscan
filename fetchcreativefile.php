<?php 
if(!empty($_REQUEST['path'])){
require_once("includes/dbcon.php");
$creativepath=$_REQUEST['path'];
$sqls = "SELECT count(*) FROM cscan_digital_creative where creative_path like'%".$creativepath."%'";
$rss = $DRW->query($sqls, $DRW_digital);

$row = $DRW->fetch_row($rss);
//print_r($row);
echo $dbpath= $row[0];
}
exit;
?>