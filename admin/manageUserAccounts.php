<?php
$ALLOW_GROUPS = array(19);
require_once("../auth_auth.php");
include 'top.php';
require_once '../includes/functions.php';
?>
<table width="100%" border="0" cellspacing="0" cellpadding="5" class="text">
  <tr>
  <td class="adminhead" align="center">USER ACCOUNT
  </td>
  </tr>
</table>
[<a href="manageUserLog.php">Manage IP Addresses by Date</a>] &nbsp; [<a href="manageUserLog2.php">Logins by IP Address</a>] &nbsp; [<a href="manageUserLog3.php">Logins by Browser</a>] &nbsp; [<a href="manageUserLog4.php">Logins by IP Address and Browser</a>] &nbsp; [<a href="manageUserLog5.php">Logins by IP Location</a>]
<?php 
print '<div style="margin:6px 0px 6px 0px;">Current IP Address: '.$_SERVER['REMOTE_ADDR'].'</div>';
?>
  <form name = "searchForm"  method = "get" action = "<?php echo $_SERVER['PHP_SELF']; ?>" >
  <table border="0" cellspacing="0" cellpadding="4" class="text">
   <tr>
  <td align="center">
   <tr>
		<td>Start Date</td>
		<td><input type="text" name="StartDate" size="20" class="input_box" value="<?php 
		    if(isset($_REQUEST['StartDate'])) {
				print htmlspecialchars($_REQUEST['StartDate'],ENT_QUOTES); 
		}
		?>" />
        <a href="#" onclick="displayCalendar(document.searchForm.StartDate,'yyyy-mm-dd',this); return false;"><img name="popcal1" src="js_calendar/images/getcal.gif" border="0" alt="" style="vertical-align:bottom;" /></a></td>
        </tr>
        
        <tr>
		<td >End Date </td>
		<td><input type="text" name="EndDate" size='20' class="input_box" value="<?php 
		   if(isset($_REQUEST['EndDate'])){
			print htmlspecialchars($_REQUEST['EndDate'],ENT_QUOTES); 
		}
		?>">
      <a href="#" onclick="displayCalendar(document.searchForm.EndDate,'yyyy-mm-dd',this); return false;"><img name="popcal2" src="js_calendar/images/getcal.gif" border="0" alt="" style="vertical-align:bottom;" /></a></td>
        </tr>
	<tr>
	<td>Company</td>
	<td><input type="text" name="companyName" size='30' class="input_box" value="<?php 
	if(isset($_REQUEST['companyName'])){
		print htmlspecialchars($_REQUEST['companyName'],ENT_QUOTES); 
	}
	?>"></td>
	</tr>
        <tr>	
		<td align="right"><input type="submit" name="submit" value="Search" class="button" /></td> <td> <input type="submit" name="clear" value="Clear" class="button" onclick="location.href='<?php print $_SERVER['PHP_SELF']; ?>'; return false;" /></td>
		</tr>
</table>
	<input type="hidden" name="send" value="send" /></form>
<br><br>
 
<?php
$msg = '';
$s_val = '';
$e_val='';
$datetext='';
$limit = 20 ;
$usercount='';
 $ipcount=0;
if(isset($_REQUEST['ipsend'])) {
	  $emailId ='';
      $fname = '';
      $lname='';
	  $company='';
	  $userid='';
	  $sdate='';
	  $edate='';
      if(isset($_REQUEST['p'])) $p = $_REQUEST['p'];  
	else $p = 0;
	
	  
	  if($_REQUEST['id']!=''){
		$userid=$DRW->real_escape_string($_REQUEST['id']);
	} 
	 
	if($_REQUEST['firstName']!=''){
		$fname=$DRW->real_escape_string($_REQUEST['firstName']);
	}
		
	if($_REQUEST['lastName']!='') {
		$lname=$DRW->real_escape_string($_REQUEST['lastName']);
		
		}

	if($_REQUEST['companyName']!=''){
	    $company=$DRW->real_escape_string($_REQUEST['companyName']);
		}
	
 	if($_REQUEST['emailAddress']!=''){
	$emailId=$DRW->real_escape_string($_REQUEST['emailAddress']);
		
	}
	$dateget = '';
	If(isset($_REQUEST['StartDate']) && $_REQUEST['StartDate']!=''){
      
		$sdate=$DRW->real_escape_string($_REQUEST['StartDate']);
      	$datetext .= " AND date>='$sdate'";
      	$dateget .= '&StartDate='.urlencode($_REQUEST['StartDate']);
	 	
     }
       If(isset($_REQUEST['EndDate']) && $_REQUEST['EndDate']!=''){
      
       	$edate=$DRW->real_escape_string($_REQUEST['EndDate']);
      	$datetext .= " AND date<='$edate'";
      	$dateget .= '&EndDate='.urlencode($_REQUEST['EndDate']);
     }
	?>
	<table border="0" width="100%" cellspacing="0" cellpadding="4" class="text">
			<tr align="justify">
			<td colspan="5"><strong><?php echo $emailId ;?><br />
			<?php echo $company ;?><br />
			<?php echo $fname ;?>&nbsp; &nbsp; 
			<?php echo $lname ;?></strong><br /> </td>
		</tr>	
		<tr >
			<td width="20%" class="adminhead"><strong>IP Address</strong></td>
			<td width="20%" class="adminhead"><strong>Last Access</strong></td>
			
		</tr>
<?php
$sql="Select IPAddress ,max(date) as max_date from cscan_user_tracker where userID=$userid $datetext GROUP BY IPAddress ORDER BY max_date desc";

$numquery = $DRW->query($sql,$DRW_read);
$numrows = $DRW->num_rows($numquery);   
$sql .= " limit $p,$limit";
$rs  = $DRW->query($sql,$DRW_read);
$resultCount = $DRW->num_rows($rs);
 if( $resultCount > 0 )
  {
  	  $ipaddress ='';
      $lastaccess = '';
     
  	while($row = $DRW->fetch_array($rs))
  	
    {
      
      $ipaddress=$row['IPAddress'];
      $lastaccess = $row['max_date'];
      ?>
      <tr valign="top">
        <td><?php echo $ipaddress;?></td>
		<td><?php echo $lastaccess;?></td>
		</tr>
      <?php
    	
     }
     
     	}
	
	
	?>
</table><br />
	<table border="0" width="100%" cellspacing = "0"  cellpadding ="5">
			<tr>
				<td colspan = "2">&nbsp; </td>
			</tr>
			<?php
				if($resultCount > 0)
				{
					print '<tr>';
					if ($p >= 1)     # HIDE PREV link if p is 0
					{
						$prevs=($p-$limit);
						print "<td align=\"right\" style= \"margin-right:5px;\"><a href=\"manageUserAccounts.php?p=$prevs&emailAddress=".urlencode($emailId)."&firstName=".urlencode($fname)."&lastName=".urlencode($lname)."&companyName=".urlencode($company)."&ipsend=1&id=$userid$dateget\" class=\"sidehead\">&laquo; Prev $limit</a></td>";
					}
					else
					{
						echo "<td width=\"50%\">&nbsp;</td>";
					}
					## Calculate number of pages needing links
        
					$pages = intval($numrows/$limit);
        
					## $pages now contains int of pages needed unless there is a remainder from division
   
					if ($numrows%$limit)
					{    
						$pages++; ##has remainder so add one page
					}	
					##check to see if last page
					if (!((($p+$limit)/$limit) == $pages) && $pages!=1)
					{
						$news=$p+$limit; ##not last page so give NEXT link
						echo "<td  style=\"margin-left:10px;\"><a href=\"manageUserAccounts.php?p=$news&emailAddress=".urlencode($emailId)."&firstName=".urlencode($fname)."&lastName=".urlencode($lname)."&companyName=".urlencode($company)."&ipsend=1&id=$userid$dateget\" class=\"sidehead\">Next $limit &raquo;</a></td>";
					}
					else
					{
						echo "<td width=\"50%\">&nbsp;</td>";
					}
					echo "</tr>";
					/*
					$a = $s + ($limit);
					if ($a > $numrows)
					{
						$a = $numrows ;
					}
					*/

					$a=$p+$limit;
					if($a>=$numrows)
					$a=$numrows;
					//$b = $s + 1 ;
					echo "<tr><td class=\"bodytext\" colspan=\"2\" align=\"center\">Showing results ".($p+1)." to $a of $numrows</td></tr>";
				}
				?>
		</table>
<?php	
}
else {
	
	
	if(isset($_REQUEST['p'])) $p = $_REQUEST['p'];  
	else $p = 0;
	
	If(isset($_REQUEST['StartDate']) && $_REQUEST['StartDate']!=''){
      
		$s_val=$DRW->real_escape_string($_REQUEST['StartDate']);
      	$datetext .= " AND date>='$s_val'";
	 	
     }
       If(isset($_REQUEST['EndDate']) && $_REQUEST['EndDate']!=''){
      
       	$e_val=$DRW->real_escape_string($_REQUEST['EndDate']);
      	$datetext .= " AND date<='$e_val'";
     }
     $ctext = '';
	if(!empty($_REQUEST['companyName'])){
		$ctext .= " AND companyName='".$DRW->real_escape_string($_REQUEST['companyName'])."'";
	}
     
    $sql="SELECT distinct c_u_t.userID as userID,c_u.emailAddress,c_u.firstName,c_u.lastName,c_u.companyName,c_u_t.IPAddress ,count(distinct c_u_t.IPAddress) as Count_ip FROM
          cscan_users as c_u ,cscan_user_tracker as c_u_t WHERE c_u.userID=c_u_t.userID $datetext$ctext GROUP BY c_u_t.userID ORDER BY Count_ip desc";
    
    $sql_totalip="SELECT  count(distinct c_u_t.IPAddress) as countip FROM
         cscan_users as c_u ,cscan_user_tracker as c_u_t WHERE c_u.userID=c_u_t.userID   $datetext$ctext GROUP BY c_u_t.userID ";
    
    $sql2 = "select count(distinct c_u_t.userID) as numrows  ,count(distinct c_u_t.IPAddress) as total_ip FROM
          cscan_users as c_u ,cscan_user_tracker as c_u_t WHERE c_u.userID=c_u_t.userID   $datetext$ctext ";

    $sql_ipcount="SELECT distinct c_u_t.userID as userID,c_u_t.IPAddress as ip FROM
          cscan_users as c_u ,cscan_user_tracker as c_u_t WHERE c_u.userID=c_u_t.userID $datetext$ctext order by c_u_t.userID,c_u_t.IPAddress ";
    
	$numquery = $DRW->query($sql2,$DRW_read);
	$nrow = $DRW->fetch_row($numquery);
	$numrows = $nrow[0];
    
    $sql .= " limit $p,$limit";
    $rs  = $DRW->query($sql,$DRW_read);
    $resultCount = $DRW->num_rows($rs);
    $usercount = $numrows;
    $rs_totalip  = $DRW->query($sql_totalip,$DRW_read);
    $rscount_totalip = $DRW->num_rows($rs_totalip);
    if( $rscount_totalip > 0 )
  { 
  	while($row = $DRW->fetch_array( $rs_totalip))
  	
    {
    	$ipcount= $ipcount+$row['countip'];
    }
  }
   $user_ipArray= array();
   $user_ipRepeatCheck= array();
   $prev_userid='';
   $prev_ip='';
   $rs_ipcount=$DRW->query($sql_ipcount,$DRW_read);
   $rscount_ipcount = $DRW->num_rows($rs_ipcount);
   if( $rscount_ipcount > 0 )
  { 
  	while($row = $DRW->fetch_array( $rs_ipcount))
  	
    {    $cur_userid=$row['userID'];
         $cur_ip=$row['ip'];
         if($prev_userid==$cur_userid)	{
         	
         	        	
         	$previp_pos=strrpos($prev_ip,'.');
         	$curip_pos=strrpos($cur_ip,'.');
         	if(substr($prev_ip,0,$previp_pos)==substr($cur_ip,0,$curip_pos)){
         		if((array_key_exists($cur_userid, $user_ipRepeatCheck))){
         	    	//$user_ipArray[$cur_userid]=1;
         		}
         		else{
         			$user_ipRepeatCheck[$cur_userid]=true;
                  		}
           	}
         	 else{
         	$prev_userid=$cur_userid;
         	$prev_ip=$cur_ip;
         	$user_ipArray[$cur_userid]=get_ipcount($cur_userid,$user_ipArray);

         }
         }
         else{
         	$prev_userid=$cur_userid;
         	$prev_ip=$cur_ip;
         		$user_ipArray[$cur_userid]=get_ipcount($cur_userid,$user_ipArray);

         }
    }
  }
 

    	?>

    	
    <table border="0" width="50%" cellspacing="0" cellpadding="4" class="text">
    <tr>
			<td><strong>Total distinct Users : <?php echo $usercount ?></strong></td>
			<td><strong>Total distinct IPAddresses : <?php echo $ipcount?></strong></td>
			</tr></table><br/><br/>		
<table border="0" width="100%" cellspacing="0" cellpadding="4" class="text">

			<?php
    if( $resultCount > 0 )
  {
  	?>
  	
	<tr>
			<td width="20%"  class="adminhead"><strong>Email</strong></td>
			<td width="15%" class="adminhead"><strong>First Name</strong></td>
			<td width="15%" class="adminhead"><strong>Last Name</strong></td>
			<td width="15%" class="adminhead"><strong>Company</strong></td>
			<td width="10%" class="adminhead"><strong>IP Address Count</strong></td>
			</tr>
<?php
  	  $emailId ='';
      $fname = '';
      $lname='';
	  $company='';
	  $Ip_Count='';

  	while($row = $DRW->fetch_array($rs))
  	
    {
      $userid=$row['userID'];
      $emailId = $row['emailAddress'];
      $fname = $row['firstName'];
      $lname=$row['lastName'];
	  $company=$row['companyName'];
	  $Ip_Count=$row['Count_ip'];
	  $Actual_Ip_Count=$user_ipArray[$userid];
	  ?>
      <tr valign="top">
        <td><?php echo $emailId ;?></td>
		<td><?php echo $fname;?></td>
		<td ><?php echo $lname; ?></td>
		<td ><?php echo $company; ?></td>
		<td ><?php print "<a href=\"{$_SERVER['PHP_SELF']}?emailAddress=".urlencode($emailId)."&firstName=".urlencode($fname)."&lastName=".urlencode($lname)."&companyName=".urlencode($company)."&StartDate=".urlencode($s_val)."&EndDate=".urlencode($e_val)."&ipsend=1&id=$userid\">$Ip_Count";
		if($Actual_Ip_Count!=$Ip_Count) print " ($Actual_Ip_Count)";
		print "</a>";?></td>
	  </tr>
      <?php

    }
  }
  else{
  	$msg="No records found";
  	echo "<tr><td class=\"error\">$msg</td></tr>";
  }
?>
</table><br/>
	<table border="0" width="100%" cellspacing = "0"  cellpadding ="5">
			<tr>
				<td colspan = "2">&nbsp; </td>
			</tr>
			<?php
				if($resultCount > 0)
				{
					print '<tr>';
					if ($p >= 1)     # HIDE PREV link if p is 0
					{
						$prevs=($p-$limit);
						print "<td align=\"right\" style=\"margin-right:5px;\"><a href=\"manageUserAccounts.php?p=$prevs&StartDate=".urlencode($s_val)."&EndDate=".urlencode($e_val)."&send=send\" class=\"sidehead\">&laquo; Prev $limit</a></td>";
					}
					else
					{
						echo "<td width=\"50%\">&nbsp;</td>";
					}
					## Calculate number of pages needing links
        
					$pages = intval($numrows/$limit);
        
					## $pages now contains int of pages needed unless there is a remainder from division
   
					if ($numrows%$limit)
					{    
						$pages++; ##has remainder so add one page
					}	
					##check to see if last page
					if (!((($p+$limit)/$limit) == $pages) && $pages!=1)
					{
						$news=$p+$limit; ##not last page so give NEXT link
						echo "<td  style=\"margin-left:10px;\"><a href=\"manageUserAccounts.php?p=$news&StartDate=".urlencode($s_val)."&EndDate=".urlencode($e_val)."&send=send\" class=\"sidehead\">Next $limit &raquo;</a></td>";
					}
					else
					{
						echo "<td width=\"50%\">&nbsp;</td>";
					}
					echo "</tr>";
					/*
					$a = $s + ($limit);
					if ($a > $numrows)
					{
						$a = $numrows ;
					}
					*/

					$a=$p+$limit;
					if($a>=$numrows)
					$a=$numrows;
					//$b = $s + 1 ;
					echo "<tr><td class=\"bodytext\" colspan=\"2\" align=\"center\">Showing results ".($p+1)." to $a of $numrows</td></tr>";
				}
				?>
		</table>
<?php	
}

function get_ipcount($cur_userid,&$user_ipArray){
	 if(array_key_exists($cur_userid, $user_ipArray)){
         		    	$count=$user_ipArray[$cur_userid];
         		    
         		    	$count++;
         		    	
         		    }
         		    else{
         		    	$count=1;
         		    	
         		    }
         		    
         		    return $count;
}
?>
       <br>
<script type="text/javascript" src="js_calendar/calendar.js"></script>
    <?php 
include 'bottom.php';
?> 