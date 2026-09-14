<?php
require_once('includes/globalSession.php');
require_once('includes/checklogin.php');

if(isset($_REQUEST['selected_productID'])) $selected_productID = (float)$_REQUEST['selected_productID'];
else $selected_productID = 0;
if(isset($_REQUEST['remove'])) $remove = (int)$_REQUEST['remove'];
else $remove = 0;

if($selected_productID!=0 && isset($_SESSION['selected_productID'])){
	if($remove==1){
		$ind = array_search($selected_productID,$_SESSION['selected_productID']);
		if($ind!==false){
			unset($_SESSION['selected_productID'][$ind]);
		}
	}
	elseif(!in_array($selected_productID,$_SESSION['selected_productID'])){
		$_SESSION['selected_productID'][] = $selected_productID;
	}
}
?>