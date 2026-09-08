<?php
require_once("includes/competi_def.php");
require_once('includes/dbcon.php');
$sql = "SELECT com1.companyID,com1.companyName,com1.isWorksiteVoluntary,com1.isApprovedCo, com2.companyName AS parentCompanyName,com1.isRetailMarketer FROM cscan_company com1 LEFT JOIN cscan_company com2 ON com1.parentCompanyID = com2.companyID WHERE 1=1 order by com1.companyName ";
	
$rs = $DRW->query($sql,$DRW_read);
$resultCount = $DRW->num_rows($rs);
$coIDs = array();
$num=1;
if($resultCount > 0) {
	$className='';
	while($row = $DRW->fetch_row($rs)) {
            
		$ID = $row[0];
		echo $num.'       &nbsp;&nbsp;&nbsp;&nbsp;'.$categoryName = $row[1];
                echo"<br>";
		$num++;
	}
}
?>
	
