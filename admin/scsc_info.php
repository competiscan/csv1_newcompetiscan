<?php
require_once("../auth_auth.php");
require_once '../includes/functions.php';

if(isset($_REQUEST['s'])) {
	$s = (float)$_REQUEST['s'];
}
else {
	$s = 0;
}
if(isset($_REQUEST['sid'])) {
	$sid = (float)$_REQUEST['sid'];
}
else {
	$sid = 0;
}
if(isset($_REQUEST['cid'])) {
	$cid = (float)$_REQUEST['cid'];
}
else {
	$cid = 0;
}
if(isset($_REQUEST['scid'])) {
	$scid = (float)$_REQUEST['scid'];
}
else {
	$scid = 0;
}

@ob_clean();
header("Content-Type: text/plain");
$out = '';
if(!empty($scid)){
	$category = getSubCategory($scid,false);
	if($category!==0){
		foreach( $category as $id=>$name ) {
			//if(checkSubCategory($id)){
				//$out .= "<option value=\"$id\">".htmlspecialchars($name)."</option>";
				$out .= "$id\t".str_replace("\t",' ',$name)."\n";
			//}
		}
	}
}
elseif(!empty($cid)){
	$category = getSubCategory($cid,false);
	if($category!==0){
		foreach( $category as $id=>$name ) {
			if(checkSubCategory($id)){
				//$out .= "<option value=\"$id\">".htmlspecialchars($name)."</option>";
				$out .= "$id\t".str_replace("\t",' ',$name)."\n";
			}
		}
	}
}
elseif(!empty($sid)){
	$category = getCategory($sid);
	if($category!==0){
		foreach( $category as $id=>$name ) {
			if(checkCategory($id)){
				//$out .= "<option value=\"$id\">".htmlspecialchars($name)."</option>";
				$out .= "$id\t".str_replace("\t",' ',$name)."\n";
			}
		}
	}
}
elseif(!empty($s)){
	$sector = getSector();
	foreach( $sector as $id=>$name ) {
		if(checkSector($id)){
			//$out .= "<option value=\"$id\">".htmlspecialchars($name)."</option>";
			$out .= "$id\t".str_replace("\t",' ',$name)."\n";
		}
	}
}
if($out!=''){
	//echo "<option value=\"0\">&nbsp;</option>".$out;
	echo $out;
}
?>