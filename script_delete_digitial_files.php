<?php
require_once('includes/globalSession.php');
$day_10 = date('Y-m-d', strtotime('-26 day'));
//echo $day_10; die;
$sql= "SELECT file_name,file_path,insert_date FROM cscan_digital_files where status=2 AND DATE_FORMAT(insert_date,'%Y-%m-%d')='".$day_10."'";
$query = $DRW->query($sql,$DRW_read);
 if($DRW->num_rows($query) >0) {
    while($rowData = $DRW->fetch_assoc($query)) { 
        $file_name=$rowData['file_name'];
        $file_path=$rowData['file_path'];
        $fullPath=$file_path.'/'.$file_name;
	if (file_exists($fullPath) ){ 
		unlink($fullPath);
	   }
    }
 }
echo "SUCCESS"; die;
?>