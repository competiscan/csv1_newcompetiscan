<?php die;
require_once 'config.php';
if($_GET['kid'] && $_GET['kid']!=''){
	$id=$_GET['kid'];
	$sql = "SELECT id FROM cscan_youtube_search_keywords where id='".$id."'";        
    $checkS = $DRW->query($sql, $DRW_read);
    $countS = $DRW->num_rows($checkS);        
    if ($countS > 0) {
		$del_query="Delete from cscan_youtube_search_keywords where id='".$id."'";
		$checkS = $DRW->query($del_query, $DRW_main);
		$msg =  'Search Keyword has been deleted successfully!';
        header('Location: keywords.php?msg='.$msg);
	}else{
		$msg =  'Invalid id provided, Record does not exist for that id!';
        header('Location: keywords.php?msg='.$msg);
	}
}else{
	$msg = 'You can not directly access this page.';
    header('Location: keywords.php?msg='.$msg);
	
}
?>
