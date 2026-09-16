<?php
require_once('includes/globalSession.php');
require_once('includes/checklogin.php');

if(isset($_REQUEST['pid'])) {
	$pid = (float)$_REQUEST['pid'];
}
else {
	$pid = 0;
}

@ob_clean();
if($pid!=0){
	$cos = array();
	$resultC = $DRW->query("SELECT DISTINCT affinityName FROM cscan_affinity pa,cscan_panelist_affinity pp
		WHERE pp.panelist_id=$pid AND pa.affinityID=pp.affinityID ORDER BY affinityName ASC",$DRW_read);
	while($dataC = $DRW->fetch_row($resultC)){
		$cos[] = $dataC[0];
	}
	echo "<div><strong>Affinities:</strong> ".implode(', ',$cos)."</div>";
	$cos = array();
	$resultC = $DRW->query("SELECT DISTINCT companyName FROM cscan_company pa,cscan_panelist_company pp
		WHERE pp.panelist_id=$pid AND pa.companyID=pp.companyID ORDER BY companyName ASC",$DRW_read);
	while($dataC = $DRW->fetch_row($resultC)){
		$cos[] = $dataC[0];
	}
	echo "<hr /><div><strong>Loyalty/Retention, Statement Companies:</strong> ".implode(', ',$cos)."</div>";
	if($_SESSION['sess_plevel']==1 || $_SESSION['sess_plevel']==2){
		$cos = array();
		$resultC = $DRW->query("SELECT pm_date,stateID1,stateID2,postalcode1,postalcode2 FROM cscan_panelists_mover WHERE panelist_id=$pid ORDER BY pm_date ASC",$DRW_read);
		while($dataC = $DRW->fetch_row($resultC)){
			$cos[] = $dataC[0].' ['.stateName($dataC[1]).' '.$dataC[3].' &gt; '.stateName($dataC[2]).' '.$dataC[4].']';
		}
		if(count($cos)>0){
			echo "<hr /><div><strong>Address Change History:</strong> ".implode(', ',$cos)."</div>";
		}
	}
}
?>