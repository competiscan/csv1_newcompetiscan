<?php 
require_once("../auth_auth.php");

@ob_clean();

$field = $value= '';
$chex = 0;
if(isset($_REQUEST['field'])){
	$field = $_REQUEST['field'];
}
if(isset($_REQUEST['value'])){
	$value= $_REQUEST['value'];
}
if(isset($_REQUEST['chex'])){
	$chex = (int)$_REQUEST['chex'];
}
if($field!='' && $value!=''){
	if($field=='isWorksiteVoluntary'){
		$sql = "UPDATE cscan_company SET isWorksiteVoluntary=$chex WHERE companyID=".(int)$value;
		$DRW->query($sql,$DRW_main);
	}
}
echo '1';
?>