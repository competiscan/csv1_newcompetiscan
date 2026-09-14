<?php
$LOGOUT_PAGE = 'content/index.php';
require_once("auth_auth.php");
require_once('includes/functions.php');

if(isset($_REQUEST['hy'])){
	$hy = (int)$_REQUEST['hy'];
}
if(empty($hy)){
	$hy = '';
}

echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\">";
if(isset($_REQUEST['muid']) && !empty($_REQUEST['muid'])){
	if(!empty($_REQUEST['isTmp'])) {
		$isTmp = '1';
	}
	else {
		$isTmp = '0';
	}
	if(isset($_REQUEST['view'])) {
		$view = (int)$_REQUEST['view'];
	}
	else {
		$view = 0;
	}
	$q = "SELECT `ceafid`,`ceafpath` FROM `cscan_email_attach_file$hy` WHERE `muid`='".(int)$_REQUEST['muid']."' AND isTmp=$isTmp ORDER BY `ceafdate` ASC";
	$query_result = $DRW->query($q,$DRW_read);
	while($data = $DRW->fetch_row($query_result)){
		$ceafid = $data[0];
		$ceafpath = $data[1];
		#################################### Start S3 Implementation Code ###########################################
		$img_view_link= $displays3URL.$ceafpath;
		/*if(is_file($ceafpath)){
			echo "<tr><td><span class=\"bodytext\">".basename($ceafpath).' ('.File_Size(filesize($ceafpath)).")</span> &nbsp; </td><td> &nbsp; </td><td> &nbsp; <a href=\"$ceafpath\" target=\"_blank\" class=\"bluelink\">View</a>";
			if($view==0){
				echo " &nbsp; | &nbsp; <a href=\"#\" onclick=\"removeAttach($ceafid); return false;\" class=\"bluelink\">Remove</a>";
			}
			echo "</td></tr>";
		}*/
		if(!empty($ceafpath)){
			echo "<tr><td><span class=\"bodytext\">".basename($ceafpath).")</span> &nbsp; </td><td> &nbsp; </td><td> &nbsp; <a href=\"$img_view_link\" target=\"_blank\" class=\"bluelink\">View</a>";
			if($view==0){
				echo " &nbsp; | &nbsp; <a href=\"#\" onclick=\"removeAttach($ceafid); return false;\" class=\"bluelink\">Remove</a>";
			}
			echo "</td></tr>";
		}
		#################################### End S3 Implementation Code ###########################################
	}
}
echo "</table>";

function File_Size($size){
    if($size > 1048576){
        return $return_size=sprintf("%01.2f",$size / 1048576)." Mb";
    }
    elseif($size > 1024){
        return $return_size=sprintf("%01.2f",$size / 1024)." Kb";
    }
    else{
        return $return_size=$size." Bytes";
    }
}
?>