<?php die;
require_once 'config.php';
if($_GET['lid'] && $_GET['lid']!=''){
	$id=$_GET['lid'];
	$sql = "SELECT id,logo_name,logo_path FROM cscan_youtube_search_logos where id='".$id."'";        
        $checkL = $DRW->query($sql, $DRW_read);
        $countL = $DRW->num_rows($checkL);        
        if ($countL > 0) {
            $row = $DRW->fetch_array($checkL);
            $logo_name=$row['logo_name'];
            $logo_path=$row['logo_path'];
            $del_query="Delete from cscan_youtube_search_logos where id='".$id."'";
            $checkS = $DRW->query($del_query, $DRW_main);
            if (file_exists($logo_path.'/'.$logo_name)) {
               unlink($logo_path.'/'.$logo_name);
            }
            $msg =  'Search logo has been deleted successfully!';
            header('Location: logos.php?msg='.$msg);
        }else{
            $msg =  'Invalid id provided, Record does not exist for that id!';
            header('Location: logos.php?msg='.$msg);
        }
}else{
	$msg = 'You can not directly access this page.';
        header('Location: logos.php?msg=');	
}
?>
