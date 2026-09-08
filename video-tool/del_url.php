<?php die;
require_once 'config.php';
if($_GET['uid'] && $_GET['uid']!=''){
	$id=$_GET['uid'];
	$sql = "SELECT id FROM cscan_youtube_video where id='".$id."'";        
    $checkS = $DRW->query($sql, $DRW_read);
    $countS = $DRW->num_rows($checkS);        
    if ($countS > 0) {
		$del_query="Delete from cscan_youtube_video where id='".$id."'";
		$checkS = $DRW->query($del_query, $DRW_main);
		$msg =  'YouTube url has been deleted successfully!';
        header('Location: index.php?msg='.$msg);
	}else{
		$msg =  'Invalid id provided, Record does not exist for that id!';
        header('Location: index.php?msg='.$msg);
	}
}else{
	$msg = 'You can not directly access this page.';
    header('Location: index.php?msg='.$msg);
	
}
?>
