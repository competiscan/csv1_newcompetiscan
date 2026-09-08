<?php 
$ALLOW_GROUPS = array(18);
require_once("../auth_auth.php");
include 'top.php';
require_once '../includes/functions.php';

$desc = "Date: ".date('m/d/y');
$lines = '';
$headings = '';

if(isset($_REQUEST['minutes'])){
	$minutes = (int)$_REQUEST['minutes'];
}
else{
	$minutes = 20;
}
$minutesecs = $minutes * 60;

$link = "{$_SERVER['PHP_SELF']}?send=1&export=1&minutes=$minutes";

?>
<table width="100%" border="0" cellspacing="0" cellpadding="5" class="text">
  <tr>
  <td class="adminhead" align="center">USER TRACKER</td>
  </tr>
</table>
<br />
<div class="text" style="padding-bottom:6px;">List of time spent by different users on this site.</div>
  <form name="searchForm" method="get" action="<?php $_SERVER['PHP_SELF']; ?>">
  <table border="0" width="100%" cellspacing="0" cellpadding="4" class="text">
					
		<tr>
		<td>Email Address:</td><td><input type="text" name="loginID" class="input_box" size="30" value="<?php 
		$link .= '&loginID=';
		if(isset($_REQUEST['loginID'])) {
			$link .= urlencode($_REQUEST['loginID']);
			if($_REQUEST['loginID']!='') $desc .= ", Email Address: {$_REQUEST['loginID']}";
			print htmlspecialchars($_REQUEST['loginID'],ENT_QUOTES);
		} 
		?>" /></td>
	    </tr>	
		
	    <tr>
		<td>First Name:</td><td><input type="text" name="firstName" class="input_box" size="30" value="<?php 
		$link .= '&firstName=';
		if(isset($_REQUEST['firstName']))  {
			if($_REQUEST['firstName']!='') $desc .= ", First Name: {$_REQUEST['firstName']}";
			$link .= urlencode($_REQUEST['firstName']);
			print htmlspecialchars($_REQUEST['firstName'],ENT_QUOTES);
		}
		?>" /></td>
		</tr>
							
		<tr>
		<td>Last Name:</td><td><input type="text" name="lastName" class="input_box" size="30" value="<?php 
		$link .= '&lastName=';
		if(isset($_REQUEST['lastName']))  {
			if($_REQUEST['lastName']!='') $desc .= ", Last Name: {$_REQUEST['lastName']}";
			$link .= urlencode($_REQUEST['lastName']);
			print htmlspecialchars($_REQUEST['lastName'],ENT_QUOTES);
		}
		?>" /></td>
		</tr>
					
		<tr>
		<td>Company Name:</td><td><input type="text" name="companyName" class="input_box" size="30" value="<?php 
		$link .= '&companyName=';
		if(isset($_REQUEST['companyName']))  {
			if($_REQUEST['companyName']!='') $desc .= ", Company Name: {$_REQUEST['companyName']}";
			$link .= urlencode($_REQUEST['companyName']);
			print htmlspecialchars($_REQUEST['companyName'],ENT_QUOTES);
		}
		?>" /></td>
		</tr>

		<tr>
        <td>State/Province:</td>
          <td>
          <select name= "state" class ="combo_box">
		  <option value ="0"> --Select One --</option>
		<?php
		$link .= '&state=';
	    if(isset($_REQUEST['state'])) {
			if($_REQUEST['state']!='0') $desc .= ", State/Province: {$_REQUEST['state']}";
	    	$link .= urlencode($_REQUEST['state']);
	    	$state=$_REQUEST['state'];
	    }
		else $state=0;
		getStates($state,true); ?>
		</select>
	   </td>
        </tr>

	    <tr>
		<td >Start Date </td>
		<td><input type="text" name="StartDate" size="20" class="input_box" value="<?php 
		$link .= '&StartDate=';
	    if(isset($_REQUEST['StartDate'])) {
			if($_REQUEST['StartDate']!='') $desc .= ", Start Date: {$_REQUEST['StartDate']}";
			$link .= urlencode($_REQUEST['StartDate']);
	    	print htmlspecialchars($_REQUEST['StartDate'],ENT_QUOTES); 
		}
		?>" />
        <a href="#" onclick="displayCalendar(document.searchForm.StartDate,'yyyy-mm-dd',this); return false;"><img name="popcal1" src="js_calendar/images/getcal.gif" border="0" alt="" style="vertical-align:bottom;"></a></td>
        </tr>
							
        <tr>
		<td >End Date </td>
		<td><input type="text" name="EndDate" size="20" class="input_box" value="<?php 
		$link .= '&EndDate=';
	    if(isset($_REQUEST['EndDate'])){
			if($_REQUEST['EndDate']!='') $desc .= ", End Date: {$_REQUEST['EndDate']}";
			$link .= urlencode($_REQUEST['EndDate']);
	    	print htmlspecialchars($_REQUEST['EndDate'],ENT_QUOTES); 
		}
		?>" />
      <a href="#" onclick="displayCalendar(document.searchForm.EndDate,'yyyy-mm-dd',this); return false;"><img name="popcal2" src="js_calendar/images/getcal.gif" border="0" alt="" style="vertical-align:bottom;" /></a></td>
        </tr>

        <tr>
		<td><strong>Email Alert Minutes</strong></td>
		<td><input type="text" name="minutes" size="10" class="input_box" value="<?php echo $minutes; ?>" />
        </tr>
        	
        <tr>	
		<td align="right"><input type="submit" name="submit" value="Search" class="button" /></td><td><input type="submit" name="clear" value="Clear" class="button" onclick="location.href='<?php print $_SERVER['PHP_SELF']; ?>'; return false;" /></td>
		</tr>
		</table>			
	<input type="hidden" name="send" value="send" /></form>
 <br />
      
       
 <table border="0" width="100%" cellspacing="0" cellpadding="4" class="text">

 
    
  
<?php
$msg = '';
$count = 0;
if(isset($_REQUEST['send'])) {
	$loginID = trim($_REQUEST['loginID']);
	$ID = '';
	$type = '';
	$sql = '';
	$rs  = '';
	$Users = array();
   $emailId='';
   $datetext = '';
   $startdate='';
   $enddate='';
    
	if($_REQUEST['firstName']!=''){
		$val=$DRW->real_escape_string($_REQUEST['firstName']);
		$Users["firstName"]=$val;
		}
		
	if($_REQUEST['lastName']!='') {
		$val=$DRW->real_escape_string($_REQUEST['lastName']);
		$Users["lastName"]=$val;
		}

	if($_REQUEST['companyName']!=''){
	    $val=$DRW->real_escape_string($_REQUEST['companyName']);
		$Users["companyName"]=$val;	
		
		}
	
 	if($_REQUEST['loginID']!=''){
	$val=$DRW->real_escape_string($_REQUEST['loginID']);
		$Users["emailAddress"]=$val;		
	}
     If(isset($_REQUEST['StartDate']) && $_REQUEST['StartDate']!=''){
        $val=$DRW->real_escape_string($_REQUEST['StartDate']);
        $startdate=$DRW->real_escape_string($_REQUEST['StartDate']);
		$datetext .= " AND date>='$val'";
	 	
     }
       If(isset($_REQUEST['EndDate']) && $_REQUEST['EndDate']!=''){
        $val=$DRW->real_escape_string($_REQUEST['EndDate']);
        $enddate=$DRW->real_escape_string($_REQUEST['EndDate']);
		$datetext .= " AND date<='$val'";
     }
	
     	if($_REQUEST['state']!=''&&$_REQUEST['state']!='0'){
         $val=$DRW->real_escape_string($_REQUEST['state']); 
         $Users["state"]=$val;	
		 
	    
	} 
    
	
	if($_REQUEST['firstName']=='' && $_REQUEST['lastName']=='' && $_REQUEST['companyName']=='' && $_REQUEST['loginID']==''&& $_REQUEST['state']=='0'){
      ?>
      
		<tr>
			<td class="error">Please enter at least one value</td>
		</tr>	
		<?php
	}
	
else{   //if atleast one of the search parameters is entered by the user

	$tablename="cscan_users";
	$sql=get_sqlStatement($Users,$tablename,$datetext);

	$rs  = $DRW->query($sql,$DRW_read);

	// if the matching records are found in table cscan_users
	if($DRW->num_rows($rs) > 0){
		$type = 'A';
		$data = $DRW->fetch_row($rs);
		$ID  = $data[0]; 
		$rs  = $DRW->query($sql,$DRW_read);// was data_seek (  $rs, 0);
	}
	else{ // if not search in table cscan_sub_users
		$str= '';
		$cnt=0;
		$arrayCount=0;
		foreach( $Users as $key => $value){
			if($key!="lastName"&&$key!="companyName"&&$key!="state") {
				foreach( $Users as $key1 => $value1){
					if($key1!="lastName"&&$key1!="companyName"&&$key1!="state"){
						$arrayCount++;
					}
				}
				if($key!="emailAddress"){
					$value=$value."%";
				}
				else{
					$value="%".$value."%";	     			
				}
				if(sizeof($Users)==1){
					$sql = "SELECT c_s_u.ID ,COUNT(c_u_t.ID) as lcount ,SUM(IF(c_u_t.logoutTime='00:00:00',$minutesecs,IF(c_u_t.logoutTime<c_u_t.loginTime,TIME_TO_SEC(c_u_t.logoutTime) - TIME_TO_SEC(c_u_t.loginTime)+86400, TIME_TO_SEC(c_u_t.logoutTime) - TIME_TO_SEC(c_u_t.loginTime)))) as timediff FROM cscan_sub_users as c_s_u ,cscan_user_tracker as c_u_t WHERE $key LIKE '".$value."' AND c_s_u.ID=c_u_t.subUserID  GROUP BY emailAddress";
				}
				if(sizeof($Users)>1){
					$cnt++;
					if($cnt<sizeof($Users)){
						$str=$key." LIKE '".$value."'". " AND ".$str;
					}
					if ($cnt==sizeof($arrayCount)){
						$str =$str." ".$key." LIKE '".$value."'";
					}
					$sql = "SELECT c_s_u.ID ,COUNT(c_u_t.ID) as lcount ,SUM(IF(c_u_t.logoutTime='00:00:00',$minutesecs,IF(c_u_t.logoutTime<c_u_t.loginTime,TIME_TO_SEC(c_u_t.logoutTime) - TIME_TO_SEC(c_u_t.loginTime)+86400, TIME_TO_SEC(c_u_t.logoutTime) - TIME_TO_SEC(c_u_t.loginTime)))) as timediff FROM cscan_sub_users as c_s_u ,cscan_user_tracker as c_u_t WHERE " .$str." AND c_s_u.ID=c_u_t.subUserID  GROUP BY emailAddress";
				}
			}
		}
		$rs  = $DRW->query($sql,$DRW_read);
		if($DRW->num_rows($rs) > 0){
			$type = 'C';
			$data = $DRW->fetch_row($rs);
			$ID  = $data[0];
			$rs  = $DRW->query($sql,$DRW_read);// was data_seek (  $rs, 0);
		}
	}

	// if more than 1 record is found 
	
		$num=$DRW->num_rows($rs);
    if($num > 1){ 
      
    	?>

    	<tr>
			<td width="20%"  class="adminhead"><b>Email</b></td>
			<td width="15%" class="adminhead"><b>Company</b></td>
			<td class="adminhead"><b>State</b></td>
			<td width="15%" class="adminhead"><b>First Name</b></td>
			<td width="15%" class="adminhead"><b>Last Name</b></td>
			<td class="adminhead"><b>Alerts</b></td>
			<td class="adminhead"><b>Logins</b></td>
			<td class="adminhead"><b>Login Time</b></td>
		</tr>
		<?php
		$headings = "Email,Company,State,First Name,Last Name,Division,Alerts,Total,Login Time\n";
		
		$i=0;
		$total=0;
		$totalalerts=0;
		$tot_mins=0;
		$tot_secs=0;
    	while($i < $num){
    	
    	list($id,$lcount,$timediff,$emailId,$fname,$lname,$company,$state,$country) = $DRW->fetch_row( $rs );  
  		?>
         <tr>
    	<td>
    	<?php
    	$email =urlencode($emailId);
    	$companyname=urlencode($company);
    	$state=urlencode($state);
		$t_time=get_user_time(0,$timediff);
	    
		$query = "SELECT COUNT(*) FROM cscan_search WHERE userID='$id' AND emailAlert='1'";
		$rsE = $DRW->query($query,$DRW_read);
		$dataE = $DRW->fetch_row($rsE);
		$alerts = $dataE[0];
		
       	print "<a href=\"{$_SERVER['PHP_SELF']}?loginID=$email&send=1&firstName=&lastName=&companyName=&state=&StartDate=$startdate&EndDate=$enddate\">$emailId</a>";?>
    	</td>
    	<td>
    	<?php
    	print "<a href=\"{$_SERVER['PHP_SELF']}?loginID=&send=1&firstName=&lastName=&companyName=$companyname&state=&StartDate=$startdate&EndDate=$enddate\">$company</a>";?>
    	</td>
    	<td>
    	<?php
    	print "<a href=\"{$_SERVER['PHP_SELF']}?loginID=&send=1&firstName=&lastName=&companyName=&state=$state&StartDate=$startdate&EndDate=$enddate\">$state</a>";?>
    	</td>
    	   	
    	
    	<td><?php echo $fname; ?></td>	
    	<td><?php echo $lname; ?></td>
    	<td><?php echo $alerts; ?></td> 
    	<td><?php echo $lcount; ?></td> 
    	<td><?php echo $t_time;?></td></tr>
    	<?php
          $i++;
          $total=$total+$lcount;
          $tot_secs=$tot_secs+$timediff;
          $totalalerts+=$alerts;
          
          if(isset($_GET['export'])) $lines .= csvExcape($emailId).','.csvExcape($company).','.csvExcape($state).','.csvExcape($fname).','.csvExcape($lname).','.csvExcape($country).','."$alerts,$lcount,$t_time\n";
    	//print "<tr><td><a href='{$_SERVER['PHP_SELF']}?loginID=$emailId &send=1&firstName=&lastName=&companyName='>USER $id : $fname  $lname</a></td></tr>";
    	} ?>
    <?php
    //echo "mins: $tot_mins  secs:$tot_secs";
    
          
	$tot_time=get_user_time(0,$tot_secs);
	
?>
			<tr><td colspan="8">&nbsp;</td></tr>
          <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td align="right"><b>Totals:</b></td><td align="left"><b><?php echo $totalalerts;?></b></td><td align="left"><b><?php echo $total;?></b></td><td align="left"><b><?php echo $tot_time;?></b></td></tr>
		  <tr><td colspan="8"><form onsubmit="return false;" method="get" action="<?php $_SERVER['PHP_SELF']; ?>"><input type="submit" name="submit" value="Export" class="button" onclick="document.location='<?php print $link; ?>'; return false;" /></form></td></tr>
    	<?php
          if(isset($_GET['export'])) $lines .= ",,,,,,,,\n,,,,,Totals:,$totalalerts,$total,$tot_time\n";
    }
	
    else{ // if($DRW->num_rows($rs)  not > 1)
    if($ID!=''){
		if($type== 'A'){
			$sql = "SELECT loginTime,logoutTime,TIME_TO_SEC(logoutTime) - TIME_TO_SEC(loginTime) as timediff,date,DATE_FORMAT(loginTime,'%h:%i %p') AS loginTimef,DATE_FORMAT(logoutTime,'%h:%i %p') AS logoutTimef,IPAddress FROM cscan_user_tracker WHERE userID = '".$ID."'$datetext ORDER BY date DESC,loginTime DESC";
		}
		else{
			$sql = "SELECT loginTime,logoutTime,TIME_TO_SEC(logoutTime) - TIME_TO_SEC(loginTime) as timediff,date,DATE_FORMAT(loginTime,'%h:%i %p') AS loginTimef,DATE_FORMAT(logoutTime,'%h:%i %p') AS logoutTimef,IPAddress FROM cscan_user_tracker WHERE subUserID = '".$ID."'$datetext ORDER BY date DESC,loginTime DESC";
		}
		$rows  = $DRW->query($sql,$DRW_read);
		$count = $DRW->num_rows($rows);
	}
	else $msg = "Login ID doesn't exists"; 

	if($count <= 0) $msg = "Login detail for User doesn't exists";
}
}
}
if($count > 0) {
	 $msql = "SELECT emailAddress,firstName,lastName,companyName FROM cscan_users WHERE userID = $ID";
	 $myrs  = $DRW->query($msql,$DRW_read);
	 
	 list($emailId,$fname,$lname,$company) = $DRW->fetch_row( $myrs );
	 
?>
			<tr>
			<td colspan='6' ><b><?php echo $emailId ;?><br />
			<?php echo $company ;?><br />
			<?php echo $fname ;?>&nbsp; &nbsp; <?php echo $lname ;?></b><br /></td>
		</tr>	
		<tr>
			<td width="2%"  class="adminhead"><b>Sno.</b></td>
			<td width="18%" class="adminhead"><b>Date</b></td>
			<td width="18%" class="adminhead"><b>IP</b></td>
			<td width="25%" class="adminhead"><b>Login Time</b></td>
			<td width="25%" class="adminhead"><b>Logout Time</b></td>
			<td width="30%" class="adminhead"><b>Duration</b></td>
		</tr>
<?php
	$headings = "Sno.,Date,IP,Login Time,Logout Time,Duration\n";
	$i = 1;
	$className = '';
	$new_min=0;
	$new_sec=0;
	$totalmins=0;
	$totalsecs=0;
	        
	while( list($loginTime,$logoutTime,$duration,$date,$loginTimef,$logoutTimef,$IPAddress) = $DRW->fetch_row( $rows ) ) {
		if($className=='selected-bg') $className='white-bg';
		else $className='selected-bg';
?> 
        <tr valign="top" class="<?php echo $className; ?>">
					<td><?php echo $i."."; ?></td>
					<td><?php echo $date;?></td>
					<td><?php echo '<a href="#" onclick="var win = window.open(\'ip_location.php?ip='.urlencode($IPAddress).'\',\'ipdetail\',\'toolbar=no, menubar=no, location=no, status=no, scrollbars=yes, resizable=yes, width=500, height=400\'); win.focus(); return false;">'.$IPAddress.'</a>'; ?></td>
					<td><?php echo $loginTimef; ?></td>
          <td>
<?php 

		if($logoutTime=='00:00:00'){
			/*list($Time1,$Time2,$Time3)=split(":",$loginTime);
			$timeinsec=$Time1*60*60+$Time2*60+$Time3+20*60;
			$hours=round($timeinsec/3600);
			$hours1=round($timeinsec%3600);
			$miniut=round($hours1/60);
			$second=round($hours1%60);
			$hours=str_pad($hours,2, "0", STR_PAD_LEFT);
			$miniut=str_pad($miniut,2, "0", STR_PAD_LEFT);
			$second=str_pad($second,2, "0", STR_PAD_LEFT);
			echo $hours.":".$miniut.":".$second;*/
			echo 'n/a';
			$logoutTimef = 'n/a';
		}
		else {
			echo $logoutTimef;
		}
?>
		</td>
					<td>
<?php 
		
			
         	if($logoutTime=='00:00:00') {
		        $duration=$minutesecs;
			}
			elseif($loginTime>$logoutTime){
				$duration += 86400;
			}
			$t_time=get_user_time(0,$duration);
			echo $t_time;
			$totalsecs=$totalsecs+$duration;
		?>
					</td>
        </tr>
<?php
         if(isset($_GET['export'])) $lines .= "$i.,$date,$IPAddress,$loginTimef,$logoutTimef,$t_time\n";
		$i++;	
	} //while ends ?>
	<?php

	$tot_time=get_user_time(0,$totalsecs);
	
	?>
	<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td align="right"><b>Total :</b></td><td><b><?php echo $tot_time;?></b></td>
	</tr>
	<tr><td colspan="6"><form onsubmit="return false;" method="get" action="<?php $_SERVER['PHP_SELF']; ?>"><input type="submit" name="submit" value="Export" class="button" onclick="document.location='<?php print $link; ?>'; return false;" /></form></td></tr>
	<?php
     if(isset($_GET['export'])) $lines .= ",,,,\n,,,Total:,$tot_time\n";
}
else {
	echo "<tr><td class=\"error\">$msg</td></tr>";
}

if(isset($_GET['export'])){
	ob_end_clean();
	header("Content-Type: application/vnd.ms-excel");
	header("Content-Disposition: attachment; filename=usage.csv");
	header("Pragma: no-cache");
	header("Expires: 0");
	print "$desc\n";
	print $headings;
	print $lines;
	exit;
}

//forming the sql query
function get_sqlStatement($Users,$table,$datetext='')
{
	$str= '';
    $cnt=0;
    $sql='';
 
foreach( $Users as $key => $value){
   	if($key!="emailAddress"){
     			$value=$value."%";
     			     		}
     	   else{
     		  $value="%".$value."%";	     			
     			     		}
	if(sizeof($Users)==1){
		
		
		$sql = "SELECT c_u.userID,COUNT(c_u_t.ID) as lcount ,SUM(IF(c_u_t.logoutTime='00:00:00',{$GLOBALS['minutesecs']},IF(c_u_t.logoutTime<c_u_t.loginTime,TIME_TO_SEC(c_u_t.logoutTime) - TIME_TO_SEC(c_u_t.loginTime)+86400, TIME_TO_SEC(c_u_t.logoutTime) - TIME_TO_SEC(c_u_t.loginTime)))) as timediff,
 				  c_u.emailAddress,c_u.firstName,c_u.lastName,c_u.companyName,c_u.state,c_u.country  FROM cscan_users as c_u ,cscan_user_tracker as c_u_t
	             WHERE $key LIKE '".$value."' AND c_u.userID=c_u_t.userID$datetext GROUP BY emailAddress";	

		}
		
		if(sizeof($Users)>1){
			
			$cnt++;
			if($cnt<sizeof($Users)){
			$str=$key." LIKE '".$value."'". " AND ".$str;}
			if ($cnt==sizeof($Users)){
				$str =$str." ".$key." LIKE '".$value."'";
				
			}
				
               
			$sql = "SELECT c_u.userID,COUNT(c_u_t.ID) as lcount , SUM(IF(c_u_t.logoutTime='00:00:00',{$GLOBALS['minutesecs']},IF(c_u_t.logoutTime<c_u_t.loginTime,TIME_TO_SEC(c_u_t.logoutTime) - TIME_TO_SEC(c_u_t.loginTime)+86400, TIME_TO_SEC(c_u_t.logoutTime) - TIME_TO_SEC(c_u_t.loginTime)))) as timediff,
					c_u.emailAddress,c_u.firstName,c_u.lastName,c_u.companyName,c_u.state,c_u.country  FROM cscan_users as c_u ,cscan_user_tracker as c_u_t
			        WHERE " .$str."  AND c_u.userID=c_u_t.userID$datetext GROUP BY emailAddress";
		
		}		
	}	
	
	return $sql;
}

function get_user_time($mins,$secs){
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
		return  $t_time;
}

?>
</table>
<script type="text/javascript" src="js_calendar/calendar.js"></script>
<?php 
include 'bottom.php';

function csvExcape($in,$delim = ','){
	$out = $in;
	if(strpos($out, $delim)!==false || strpos($out, '"')!==false || strpos($out, "\r\n")!==false || strpos($out, "\n")!==false || strpos($out, "\r")!==false || preg_match('/^0+\\d+$/',$out)>0){
		$out = '"'.str_replace('"', '""', $out).'"';
	}
	return $out;
}
?>