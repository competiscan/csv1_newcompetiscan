<?php 
$ALLOW_GROUPS = array(43);
require_once("../auth_auth.php");
include 'top.php';
?>
<table width="100%" border="0" cellspacing="0" cellpadding="5" class="text">
<tr>
<td valign="top" class="adminhead" align="center">Missing Zip/Postal Code DMA</td>
</tr>
</table>
<div>&nbsp;</div>
<table width="100%" border="0" cellspacing="0" cellpadding="5" class="text">
<tr class="head1">
<td class="adminhead"><strong>Zip/Postal Code</strong></td>
<td class="adminhead"><strong>State/Province</strong></td>
<td class="adminhead"><strong>City</strong></td>
<td class="adminhead"><strong>Panelist</strong></td>
</tr>
<?php 
$className='selected-bg';
$q = "SELECT pp.panelist_id,left(postalcode,5),competi_id,pp.state,pp.city FROM cscan_panelists pp LEFT JOIN cscan_panelists_appends pa ON (pa.panelist_id=pp.panelist_id) 
	where (DMA_CODE='' OR DMA_CODE is null or DMA_CODE='0000') and postalcode<>'' and postalcode is not null AND contactTypeID=2 AND active=1 AND parent_panelist_id=0 ORDER BY left(postalcode,5),pp.state,pp.city,competi_id";
$rs = $DRW->query($q,$DRW_read);
while($data = $DRW->fetch_row($rs)){
	echo '<tr class="'.$className.'"><td>'.htmlspecialchars($data[1]).'</td><td>'.htmlspecialchars($data[3]).'</td><td>'.htmlspecialchars($data[4]).'</td><td>'.htmlspecialchars($data[2]).'</td></tr>';
	if($className=='selected-bg') {
		$className='white-bg';
	}
	else {
		$className='selected-bg';
	}
}
?>
</table>
<?php 
include 'bottom.php';
?>