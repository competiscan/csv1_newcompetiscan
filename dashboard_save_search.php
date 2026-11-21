<?php 
require_once('includes/globalSession.php');
require_once('includes/checklogin.php');

require_once('includes/dashboardData.php');
$DASH = new dashboardData();
//move this into dashboardData?

$out = '';

if(isset($_REQUEST['dsave_search_name'])){
	$out = '-1';
	$dsave_search_name = trim($_REQUEST['dsave_search_name']);
	if(!empty($dsave_search_name)){
		if(!empty($_REQUEST['dsave_search_id'])){
			$dsave_search_id = (float)$_REQUEST['dsave_search_id'];
		}
		else{
			$dsave_search_id = 0;
		}
		
		if(isset($_REQUEST['delete']) && !empty($dsave_search_id) && $dsave_search_name!='') {
			$last_template_sql = "DELETE FROM dashboard_save_search WHERE dsave_search_id=$dsave_search_id AND userID='".$_SESSION['sess_userID']."'";
			$DRW->query($last_template_sql,$DRW_main);
			$out = '1';
		}
		elseif($dsave_search_name!='') {
			$count_save_sql = "SELECT COUNT(*) FROM dashboard_save_search where userID =".$_SESSION['sess_userID'];
			$rs = $DRW->query($count_save_sql,$DRW_read);
			$data = $DRW->fetch_row($rs);
			$numrow = (int) $data[0];
			if($numrow < 100 || !empty($dsave_search_id) || $_SESSION['sess_plevel']==1 || $_SESSION['sess_plevel']==2) {
				$dsave_search_data = array();
				parse_str($_REQUEST['dsave_search_data'],$dsave_search_data);
				if(!empty($dsave_search_data['start_entries'])){
					$dsave_search_data['start_entries'] = '';
				}
				foreach($dsave_search_data as $map_key=>$v){
					if(isset($DASH->maps[$map_key]) && !empty($DASH->maps[$map_key]['search_type']) && $DASH->maps[$map_key]['search_type']=='lookup' && !empty($v)){
						$temp = array();
						$split = explode(',',$v);
						foreach($split as $kid){
							$temp[] = array('id'=>$kid,'name'=>$DASH->format_field($kid,$map_key,'key'));
						}
						$dsave_search_data[$map_key] = $temp;
					}
				}
				$dsave_search_data = json_encode($dsave_search_data);
				if(empty($dsave_search_id)){
					$last_template_sql = "INSERT INTO dashboard_save_search (dsave_search_name,userID,dsave_search_data) VALUES ('".$DRW->real_escape_string($dsave_search_name)."','".$_SESSION['sess_userID']."','".$DRW->real_escape_string($dsave_search_data)."')";
					$DRW->query($last_template_sql,$DRW_main);
					$dsave_search_id = $DRW->insert_id($DRW_main);
				}
				else{
					$last_template_sql = "UPDATE dashboard_save_search SET dsave_search_name='".$DRW->real_escape_string($dsave_search_name)."',dsave_search_data='".$DRW->real_escape_string($dsave_search_data)."' WHERE dsave_search_id=$dsave_search_id AND userID='".$_SESSION['sess_userID']."'";
					$DRW->query($last_template_sql,$DRW_main);
				}
				$out = (string)$dsave_search_id;
			}
		}
	}
}
else{
	$where = '';
	if(!empty($_REQUEST['term'])){
		$val = $DASH->mysqlLike($_REQUEST['term']);
		$regx = '';
		if(strlen($val)>2) {
			$firstpct = '%';
		}
		else {
			$firstpct = '';
		}
		$where .= " AND dsave_search_name LIKE '$firstpct$val%'";
	}
	$out = array();
	$Q = "SELECT dsave_search_id,dsave_search_name,dsave_search_data FROM dashboard_save_search WHERE userID=".$_SESSION['sess_userID']."$where ORDER BY dsave_search_name";
	$rs = $DRW->query($Q,$DRW_read);
	while($datat = $DRW->fetch_row($rs)){
		$out[] = array('id'=>$datat[0],'value'=>$datat[1],'data'=>$datat[2]);
	}
}

ob_end_clean();
echo json_encode($out);
?>