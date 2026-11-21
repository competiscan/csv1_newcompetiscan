<?php 

function manageUserTracker_report($StartDate='', $EndDate='', $companyName='', $report_type=0) {
	global $DRW,$DRW_read,$DRW_main,$DRW_crm;
	$out = '';
	$where = "logoutTime>'00:00:00'"; //active='y' AND 
	if(!empty($companyName)){
		if($where!=''){
			$where .= ' AND ';
		}
		$where .= "companyName='".$DRW->real_escape_string($companyName)."'";
	}
	$daterange = '';
	If(!empty($StartDate)){
		if($where!=''){
			$where .= ' AND ';
		}
		$where .= "date>='".$DRW->real_escape_string(date('Y-m-d',strtotime($StartDate)))."'";
		$daterange .= date('m/d/Y',strtotime($StartDate));
	}
	If(!empty($EndDate)){
		if($where!=''){
			$where .= ' AND ';
		}
		$where .= "date<='".$DRW->real_escape_string(date('Y-m-d',strtotime($EndDate)))."'";
		if($daterange!=''){
			$daterange .= ' - ';
		}
		$daterange .= date('m/d/Y',strtotime($EndDate));
	}
	if($where!=''){
		$where = ' WHERE '.$where;
	}
	if($report_type==1){
		$out .= '<div><em>'.$daterange.'</em></div><table border="0" width="100%" cellspacing="0" cellpadding="4">
		<tr><td><strong>Name</strong></td><td><strong>Logins</strong></td><td><strong>Login Hours</strong></td><td><strong>Login Minutes</strong></td><td><strong>Login Seconds</strong></td></tr>';
	}
	elseif($report_type==2){
		if($daterange!=''){
			$out .= $daterange."\n";
		}
		$out .= "Name,Logins,Login Hours,Login Minutes,Login Seconds\n";
	}
	else{
		$out .= '<table border="0" width="100%" cellspacing="0" cellpadding="4" class="text">
		<tr><td class="adminhead"><strong>Name</strong></td><td class="adminhead"><strong>Logins</strong></td><td class="adminhead"><strong>Login Hours</strong></td><td class="adminhead"><strong>Login Minutes</strong></td><td class="adminhead"><strong>Login Seconds</strong></td></tr>';
	}
	if(!empty($companyName)){
		$sql = "SELECT c_u.userID,COUNT(ID) as lcount,
			SUM(IF(logoutTime<loginTime,TIME_TO_SEC(logoutTime) - TIME_TO_SEC(loginTime)+86400, TIME_TO_SEC(logoutTime) - TIME_TO_SEC(loginTime))) as timediff
			FROM cscan_users c_u JOIN cscan_user_tracker c_u_t ON (c_u.userID=c_u_t.userID)
			$where GROUP BY c_u.userID";
		$rs  = $DRW->query($sql,$DRW_read);
		while($data = $DRW->fetch_row( $rs )) {
			$sql2 = "select firstName,lastName,emailAddress from cscan_users where userID='".$data[0]."'";
			$rs2  = $DRW->query($sql2,$DRW_read);
			$data2 = $DRW->fetch_row( $rs2 );
			$data[0] = $data2[0].' '.$data2[1]. ' ('.$data2[2].')';
			$ltimes = get_user_time(0,$data[2], true);
			if($report_type==2){
				$out .= csvExcape($data[0]).",$data[1],$ltimes[0],$ltimes[1],$ltimes[2]\n";
			}
			else{
				$out .= '<tr><td>'.$data[0].'</td><td>'.$data[1].'</td><td>'.$ltimes[0].'</td><td>'.$ltimes[1].'</td><td>'.$ltimes[2].'</td></tr>';
			}
		}
		
		if($report_type==2){
			$out .= "\n";
		}
		else{
			$out .= '<tr><td colspan="5" style="border-top:solid 1px #000000;">&nbsp;</td></tr>';
		}
	}
	$sql = "SELECT companyName,COUNT(ID) as lcount,
		SUM(IF(logoutTime<loginTime,TIME_TO_SEC(logoutTime) - TIME_TO_SEC(loginTime)+86400, TIME_TO_SEC(logoutTime) - TIME_TO_SEC(loginTime))) as timediff
		FROM cscan_users c_u JOIN cscan_user_tracker c_u_t ON (c_u.userID=c_u_t.userID)
		$where GROUP BY companyName";
	$rs  = $DRW->query($sql,$DRW_read);
	while($data = $DRW->fetch_row( $rs )) {
		$ltimes = get_user_time(0,$data[2], true);
		$ltimes = get_user_time(0,$data[2], true);
		if($report_type==2){
			$out .= csvExcape($data[0]).",$data[1],$ltimes[0],$ltimes[1],$ltimes[2]\n";
		}
		else{
			$out .= '<tr><td>'.$data[0].'</td><td>'.$data[1].'</td><td>'.$ltimes[0].'</td><td>'.$ltimes[1].'</td><td>'.$ltimes[2].'</td></tr>';
		}
	}
	if($report_type!=2){
		$out .= '</table>';
	}
	return $out;
}
function get_user_time($mins,$secs,$split=false){
	$hrs=0;
	$t_time='';
	if($secs>=60){
		$mod_s=$secs%60;
		$mins=$mins+(($secs-$mod_s)/60);
		$secs=$mod_s;
	}
	if($mins>=60){
		$mod_h=$mins%60;
		$hrs=$hrs+(($mins-$mod_h)/60);
		$mins=$mod_h;
		$t_time="$hrs Hrs ".str_pad($mins,2, "0", STR_PAD_LEFT)." Mins ".str_pad($secs,2, "0", STR_PAD_LEFT)." Secs";
	}
	else{
		$t_time="$mins Mins ".str_pad($secs,2, "0", STR_PAD_LEFT)." Secs"; 
	}
	if($split){
		$t_time = array($hrs, $mins, $secs);
	}
	return  $t_time;
}
function csvExcape($in,$delim = ','){
	$out = $in;
	if(strpos($out, $delim)!==false || strpos($out, '"')!==false || strpos($out, "\r\n")!==false || strpos($out, "\n")!==false || strpos($out, "\r")!==false || preg_match('/^0+\\d+$/',$out)>0){
		$out = '"'.str_replace('"', '""', $out).'"';
	}
	return $out;
}
?>