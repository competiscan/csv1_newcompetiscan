<?php
require_once('includes/globalSession.php');

$resultPID = $DRW->query("SELECT panelist_id, competi_id FROM cscan_panelists WHERE active = 1 ",$DRW_read);
while($dataPID = $DRW->fetch_assoc($resultPID)){
	$pid = $dataPID['panelist_id'];
	$compti_id = $dataPID['competi_id'];

	$PAName = array();
	$company = array();
	$affinityName='';
	$companyName='';

	$resultAN = $DRW->query("SELECT DISTINCT affinityName FROM cscan_affinity pa,cscan_panelist_affinity pp
	WHERE pp.panelist_id = $pid AND pa.affinityID=pp.affinityID ORDER BY affinityName ASC",$DRW_read);
	while($dataAN = $DRW->fetch_row($resultAN)){
		$PAName[] = $dataAN[0];
	}
	$affinityName = implode(', ',$PAName);

	$resultC = $DRW->query("SELECT DISTINCT companyName FROM cscan_company pa,cscan_panelist_company pp
		WHERE pp.panelist_id = $pid AND pa.companyID=pp.companyID ORDER BY companyName ASC",$DRW_read);
	while($dataC = $DRW->fetch_row($resultC)){
		$company[] = $dataC[0];
	}
	$companyName = implode(', ',$company);

	$insert_sql = "INSERT INTO cscan_penalist_affinity_company (competi_id, affinityName, company)
				VALUES ('".$DRW->real_escape_string($compti_id)."','".$DRW->real_escape_string($affinityName)."','".$DRW->real_escape_string($companyName)."')"; 
	$DRW->query($insert_sql,$DRW_main);
}
echo "Data Inserted";die;
?> 