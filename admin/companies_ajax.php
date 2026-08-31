<?php
$ALLOW_GROUPS = array(6);
require_once("../auth_auth.php");

$company = $_GET['company'];
if ($company != "") {
	$sql = "SELECT companyID, companyName FROM cscan_company WHERE companyName LIKE '".$DRW->real_escape_string($company)."%' ORDER BY companyName LIMIT 5";
	$result = $DRW->query($sql,$DRW_read);
	if ($DRW->num_rows($result) > 0) {
		echo '<span style = "font-size:8px;">click on one the suggestions...</span><br>';
		while ($row = $DRW->fetch_array($result)) {
			echo '<span id="'.$row['companyID'].'" style = "font-size:10px" onmouseover="this.style.fontWeight=\'bold\';this.style.cursor=\'pointer\'" onmouseout="this.style.fontWeight=\'normal\'" onclick = "autoFillParentCompany(this)">'.$row['companyName'].'</span><br>';
		}
	}
}
?>