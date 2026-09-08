<?php
$ALLOW_GROUPS = array(1);
require_once("../auth_auth.php");
include 'top.php';
require_once("../includes/functions.php");

function callAPI($method, $url, $data){
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    curl_setopt($curl, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'User-Agent: Mozilla/5.0'
    ]);
    $result = curl_exec($curl);
    curl_close($curl);
    return $result;
}
if(isset($_REQUEST['deletebut']) && $_REQUEST['deletebut']==1 && isset($_REQUEST['delID'])) {
	$delID = $_REQUEST['delID'];
	$count = count($delID);
        $track_delete_data=array();
        $emailData = [];
	foreach ($delID as $id) {
		###add cscan_users_sync
		// $sql_syn_user_log = "insert INTO cscan_users_sync (userID) VALUES ('".$id."')";
		// $DRW->query($sql_syn_user_log,$DRW_main);
		$sql = "DELETE FROM cscan_users where userID =$id";
		/* Added for track on delete operation */
		
                        
                $track_delete_data=array();       

                $track_delete_data = [
                        'auth_id' => $GLOBALS['AUTH_DATA']['userID'],
                        'auth_name' => authUserName($GLOBALS['AUTH_DATA']['userID']),
                        'deleted_id' => (int)$id,
                        'sql_query' => $sql,
                        'ip_address' => ipAddress(),
                        'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                        'delete_type' => 'Client Profiles',
                        'is_mobile' => isMobile(),
                        'insert_date' => date("Y-m-d H:i:s")
                    ];
                trackDelete($track_delete_data);
                $emailData[] = $track_delete_data;

                /*END  Added for track on delete operation*/
                
		$DRW->query($sql,$DRW_main);
		$query="DELETE FROM cscan_sub_users WHERE parentID =$id";
		$DRW->query($query,$DRW_main);
		$query="DELETE FROM cscan_search WHERE userID=$id";
		$DRW->query($query,$DRW_main);
		$query="DELETE FROM cscan_sector_users WHERE userID=$id";
		$DRW->query($query,$DRW_main);
		$query="DELETE FROM cscan_user_code WHERE userID=$id";
		$DRW->query($query,$DRW_main);
		$query="DELETE FROM cscan_sector_users_allow WHERE userID=$id";
		$DRW->query($query,$DRW_main);
		$query="DELETE FROM cscan_mc_users_allow WHERE userID=$id";
		$DRW->query($query,$DRW_main);
		$query="DELETE FROM cscan_search_exclude WHERE userID=$id";
		$DRW->query($query,$DRW_main);

		############ START REALTIME USER added AND UPDATED API#############
		if($id!= '') {
			$payload = json_encode([
				"user_id" => $id
			]);
			$apiuserurl= USER_LOGIN_API_URL_PROD.'delete-user-sync';
			$getuserdata= callAPI('POST', $apiuserurl, $payload);
			$resuserdata = json_decode($getuserdata, true);
			// echo "<pre>";
			// print_r($resuserdata);
			// echo "</pre>";
			// die;
			// If the API call was successful, you can handle the response as need
			if($resuserdata['code']==200){
			}
		}
		############END REALTIME USER added AND UPDATED API#############
	}
        /* Added for track on delete */
            if(count($emailData)>0){
                $html = '<table width="100%" border="1">';
                $html .= '<tr><td>Auth ID</td><td>User Name</td><td>Deleted ID</td><td>SQL Query</td><td>IP Address</td><td>User Agent</td><td>Module</td><td>Is Mobile</td><td>Deleted Date (CST)</td></tr>';

                foreach($emailData as $tr){
                    if(is_array($tr) && count($tr)>0){
                       $html .= '<tr>';
                       foreach($tr as $td){
                           $html .= '<td>'.$td.'</td>'; 
                       }
                       $html .= '</tr>';
                    }
                }                    
                $html .= '</table>';
                sendDevAlert('Caution! Data Deleted From Client Profiles',$html);
            }
        /*END  Added for track on delete */
	ob_end_clean();
	header("Location: managemember.php?msg=3&count=$count");
	exit;
}
if(isset($_REQUEST['active']) && $_REQUEST['active']==1 && isset($_REQUEST['delID'])) {
	$delID = $_REQUEST['delID'];
	$count = count($delID);
	foreach ($delID as $id) {
	
		$query="SELECT active,userID FROM cscan_users WHERE userID =$id";
		
		$result=$DRW->query($query,$DRW_read);
		
		while($row=$DRW->fetch_array($result)) {
			####addcscan_users_sync
			// $sql_syn_user_log = "insert INTO cscan_users_sync (userID) VALUES ('".$row['userID']."')";
			// $DRW->query($sql_syn_user_log,$DRW_main);
			if($row['active']=='y') $query="UPDATE cscan_users SET active='n' WHERE userID='".$row['userID']."'";
			else $query="UPDATE cscan_users SET active='y' WHERE userID='".$row['userID']."'";
			
			$DRW->query($query,$DRW_main);

			############ START REALTIME USER added AND UPDATED API#############
			if($id!= '') {
				$payload = json_encode([
					"user_id" => $row['userID']
				]);
				$apiuserurl= USER_LOGIN_API_URL_PROD.'change-status';
				$getuserdata= callAPI('POST', $apiuserurl, $payload);
				$resuserdata = json_decode($getuserdata, true);
					// echo "<pre>";
					// print_r($resuserdata);
					// echo "</pre>";
					// die;
				if($resuserdata['code']==200){
				}
			}
		}
	}
	ob_end_clean();
	header("Location: managemember.php?msg=4&count=$count");
	exit;
}

$limit = 20 ;

if(isset($_REQUEST['p'])) $p = $_SESSION['managemember_p'] = $_REQUEST['p'];
elseif(isset($_SESSION['managemember_p'])) $p = $_SESSION['managemember_p'];
else $p = 0;

if(isset($_REQUEST['msg'])){
	if(isset($_REQUEST['count'])) $countxt = (int)$_REQUEST['count'];
	else $countxt = '';
	if($_REQUEST['msg']==3) $msg = "$countxt member(s) deleted.";
	if($_REQUEST['msg']==4) $msg = "$countxt member(s) Activated/Blocked.";
}
else $msg = '';

if(isset($_REQUEST['search_text'])) {
	$_SESSION['search_text'] = $_REQUEST['search_text'];
} 
elseif(isset($_REQUEST['show_All']) || !isset($_SESSION['search_text'])) {
	$_SESSION['search_text'] = '';
}

if(isset($_GET['sort'])) $sort = (int) $_GET['sort'];
else $sort = 0;

?>

<table width="100%" border="0" cellspacing="0" cellpadding="5" class="text">
	<tr><td class="adminhead" align="center">MEMBER MANAGEMENT</td></tr>
	<tr><td>
		<form method="post" name="searchForm" action="managemember.php" onsubmit="return check_searchform();" style="display:inline;">
		<strong>Search Member:</strong>
		<input type="text" name="search_text" class="input_box" value="<?php echo $_SESSION['search_text']; ?>" />
		<input class="button" style="width:60px" type="submit" name="search_Submit1" value="Search" />
		<input type="hidden" name="search_Submit" value="1" /><input type="hidden" name="p" value="0" /></form>
		&nbsp;&nbsp;
		<form action="managemember.php" method="post" style="display:inline;">
		<input class="button" style="width:70px" type="submit" name="show_All1" value="Show All" />
		<input type="hidden" name="show_All" value="1" /><input type="hidden" name="p" value="0" /></form>
	</td></tr>
	<tr><td>
		<form method="post" name="memberForm" action="managemember.php">
		<table width="100%" border="0" cellspacing="0" cellpadding="0" class="text">
		<tr>
		<td><strong>Note:</strong> Click on Edit Member to modify the details.</td>
		<td align="right">
		<input type="submit" name="import" value="Import" class="button" onclick="document.location='<?php print 'memberimport.php'; ?>'; return false;" />
		<input type="submit" name="export" value="Export" class="button" onclick="document.location='<?php print $_SERVER['PHP_SELF'].'?export=1'; ?>'; return false;" />
		&nbsp; &nbsp;
		<input class="button" style="width:90px" type="button" value="Add Member" onclick="location.href='addmember.php'; return false;" />
		&nbsp;
		<input class="button" name="activate" value="Activate/Block" style="width:105px" type="button" id="actBt" onclick="activeCheck(); return false;" />
		&nbsp;
                <?php if(checkGroup(75)){?>
		<input class="button" style="width:60px" type="button" name="delete1" value="Delete" id="delBt" onclick="deleteCheck(); return false;" />
                <?php }?>
                </td>
		</tr></table>
		</form>
	</td></tr>
</table>
  
<form action="managemember.php" method="post" name="deleteform">
<table width="100%" border="0" cellspacing="0" cellpadding="5" class="text">
  <tr>
    <td width="5%" class="adminhead" height="15"><input type="checkbox" name="setUnset" onclick="setAll();" /></td>
    <td width="35%" class="adminhead" height="15"><strong>Member Email</strong><?php if($sort!=1) print " <a href=\"".$_SERVER['PHP_SELF']."?sort=1&p=0\" class=\"blue\">sort</a>"; ?></td>
	<td width="35%" class="adminhead" height="15"><strong>Company Name</strong><?php if($sort!=2) print " <a href=\"".$_SERVER['PHP_SELF']."?sort=2&p=0\" class=\"blue\">sort</a>"; ?></td>
	<td width="10%" class="adminhead" height="15"><strong>Active</strong><?php if($sort!=3) print " <a href=\"".$_SERVER['PHP_SELF']."?sort=3&p=0\" class=\"blue\">sort</a>"; ?></td>
	<td width="15%" class="adminhead" height="15" align="center"><strong>Edit Member</strong></td> 
  </tr>
  <tr>
	<td colspan="5" class="error" align="center"><?php echo $msg; ?></td>
  </tr> 
<?php
	$sql = "SELECT userID, emailAddress, companyName, country, loginType,active,password, firstName, lastName,clientName, streetAddress, city, state,  zipCode, phone, fax,interestArea FROM cscan_users WHERE loginType<>'A'";
	$numquery = "SELECT COUNT(userID) as numrows FROM cscan_users WHERE loginType<>'A'";
	
	if($_SESSION['search_text']!='') { 
		$search_key = mysqlLike($_SESSION['search_text']);
		$and = " AND (emailAddress LIKE '%$search_key%' OR companyName LIKE '$search_key%' OR firstName like '$search_key%' OR lastName LIKE '$search_key%')";
		$sql .= $and;
		$numquery .= $and;
	}
	
	if(isset($_GET['export'])){
		ob_end_clean();
		header("Content-Type: application/vnd.ms-excel");
		header("Content-Disposition: attachment; filename=members_".date('Ymd').".csv");
		header("Pragma: no-cache");
		header("Expires: 0");
		print "Member Management Export ".date('m/d/y')."\n";
		
		print "Email Address,Password,First Name,Last Name,Company Name,Client Name (Agencies Only),Street Address,City,State/Province,Division,Zip/Postal Code,Phone,Fax,Area of Interest,Active\n";
		$rs = $DRW->query($sql,$DRW_read);
		while($row = $DRW->fetch_array($rs)) {
			$ID = $row['userID'];
			$emailAddress = $row['emailAddress'];
			$companyName=$row['companyName'];
			$active=$row['active'];
			if($active=='y') $active="Yes";
			else $active="No";
			$firstName=$row['firstName'];
			$lastName=$row['lastName'];
			$password=$row['password'];
			
			$clientName=$row['clientName'];
			$streetAddress=$row['streetAddress'];
			$city=$row['city'];
			$state=$row['state'];
			$country=$row['country'];
			$zipCode=$row['zipCode'];
			if($zipCode=='0') $zipCode = '';
			$phone=$row['phone'];
			if($phone=='0') $phone = '';
			$fax=$row['fax'];
			//$interestArea=$row['interestArea'];
			//if($interestArea!='' && $interestArea!='None') $interestArea=sectorName($interestArea);
			$interestArea = array();
			
			$sql2 = "SELECT sectorName FROM cscan_sector_users_allow sa,cscan_sector cs WHERE sa.sectorID=cs.sectorID AND parentID=0 AND userID=$ID ORDER BY sectorName";
			$rs2 = $DRW->query($sql2,$DRW_read);
			while($row2 = $DRW->fetch_row($rs2)) {
				$interestArea[] = $row2[0];
			}
			
			print csvExcape($emailAddress).','.csvExcape($password).','.csvExcape($firstName).','.csvExcape($lastName).','.csvExcape($companyName)
			.','.csvExcape($clientName)
			.','.csvExcape($streetAddress)
			.','.csvExcape($city)
			.','.csvExcape($state)
			.','.csvExcape($country)
			.','.csvExcape($zipCode)
			.','.csvExcape($phone)
			.','.csvExcape($fax)
			.','.csvExcape(implode(', ',$interestArea))
			.','.csvExcape($active)
			."\n";
		}
		exit;
	}
	
	$numquery = $DRW->query($numquery,$DRW_read);
	$nrow = $DRW->fetch_array($numquery);
	$numrows = $nrow[0];
	
	switch($sort){
		case 1:
			$sql .= " ORDER BY emailAddress ";
			break;
		case 2:
			$sql .= " ORDER BY companyName ";
			break;
		case 3:
			$sql .= " ORDER BY active ";
			break;
		default:
			$sql .= " ORDER BY companyName,emailAddress ";
	}
	$sql .= " LIMIT $p,$limit";
	
	$rs = $DRW->query($sql,$DRW_read);
	$resultCount = $DRW->num_rows($rs);
	
	if( $resultCount > 0 ) {
		$className='';
		while($row = $DRW->fetch_array($rs)) {
			$ID = $row['userID'];
			$emailAddress = $row['emailAddress'];
			$companyName=$row['companyName'];
			$active=$row['active'];
			
			if($active=='y') $active="Yes";
			else $active="No";
			if ($className=='selected-bg') $className='white-bg';
			else  $className='selected-bg';
?>
      <tr valign="top" class="<?php echo $className;?>">
        <td><input type="checkbox" name="delID[]" value="<?php echo $ID;?>" /></td>
        <td><a href="mailto:<?php echo $emailAddress;?>" title="Send mail to member"><?php echo $emailAddress;?></a></td>
		<td><?php echo $companyName;?></td>
		<td><?php echo $active; ?></td>
		<td align="center"><a class="hlinks" href="addmember.php?id=<?php echo $ID;?>" title="Click here to edit."><img src="../images/edit.png" border="0" /></a></td>
	  </tr>
      <?php
		}
	}
	else {
    ?>
    <tr><td colspan="6" class="error" align="center">No user found.
    <script type="text/javascript">
	<!--
      var el = document.getElementById('delBt');
      var el2 = document.getElementById('actBt');
      if(el) {
      	el.style.display='none';
      }
      if(el2) {
      	el2.style.display='none';
      }
    //-->
    </script></td></tr>
<?php
	}
?>
  <tr>
	<td colspan="5">
		<table border="0" width="100%" cellspacing = "0"  cellpadding ="5">
			<tr>
				<td>&nbsp; </td>
			</tr>
<?php
			if($sort>0) $sorttext = '&sort='.$_GET['sort'];
			else $sorttext = '';
			$firstlink = '[First]';
			$prevlink = '[Prev]';
			$nextlink = '[Next]';
			$lastlink = '[Last]';
			$middlelinks = '';
			$limstart = $p;
			$limiter = $limit;
			$rowcnt = $numrows;
			$show = 10;
			//first and previous only if not on first
			if($limstart>0){
				if($limstart>=$limiter) $prev = $limstart - $limiter;
				else $prev = 0;
				$firstlink = "[<a href=\"{$_SERVER['PHP_SELF']}?p=0$sorttext\">First</a>]";
				$prevlink = "<a href=\"{$_SERVER['PHP_SELF']}?p=$prev$sorttext\">&laquo; Prev $limiter</a>";
			}
			// middle loop through total results
			$numbers = ceil($rowcnt/$limiter);
			$loopstart = ceil($limstart/$limiter);
			if($loopstart<($show-1)) $loopstart = 0; // begin, do not move until 4
			if($numbers<$show) $loopend = $numbers; // loopend is less than $show
			else $loopend = $loopstart+$show;
			if($loopend>$numbers && $loopstart!=0) { // end, show last $show
				$loopstart = $numbers - $show;
				$loopend = $numbers;
			}
			for($i=$loopstart; $i<$loopend; $i++){
				$startnum = $limiter * $i;
				if($startnum!=$limstart) {
					$middlelinks .= "<a href=\"{$_SERVER['PHP_SELF']}?p=$startnum$sorttext\">".($i+1)."</a> ";
				}
				else $middlelinks .= ($i+1).' ';
			}
			//next and last if not on last
			if($limstart<$rowcnt && (($limstart+($limiter*2))<$rowcnt || ($rowcnt - ($limstart + $limiter))>0)){
				$next = $limstart + $limiter;
				$nextlink = "<a href=\"{$_SERVER['PHP_SELF']}?p=$next$sorttext\">Next $limiter &raquo;</a>";
				$lastlink = "[<a href=\"{$_SERVER['PHP_SELF']}?p=".(($numbers-1)*$limiter)."$sorttext\">Last</a>]";
			}
			
			if($middlelinks!='') $middlelinks = "[ $middlelinks ] &nbsp;";
			print "<tr><td align=\"center\" class=\"bodytext\">$firstlink &nbsp; $prevlink &nbsp; $middlelinks $nextlink &nbsp; $lastlink</td></tr>";
			print "<tr><td align=\"center\" class=\"bodytext\">Showing results ".($limstart+1)." to ";
			if($limstart+$limiter < $rowcnt) print ($limstart+$limiter);
			else print $rowcnt;
			print " of $rowcnt</td></tr>";
?>
		</table>
	</td>
	</tr>
</table>
<input type="hidden" name="active" value="0" /><input type="hidden" name="deletebut" value="0" /></form>
<script type="text/javascript">
<!--
function check_searchform(){
	var search = document.searchForm.search_text.value = trimspace(document.searchForm.search_text.value);
	if(search == "") {
		alert("Please enter some value to search");
		document.searchForm.search_text.focus();
		return false;
	}
	return true;
}
function deleteCheck(){
	var x = 0;
	for(var i=0; i<document.deleteform.elements.length;i++) {
		if(document.deleteform.elements[i].checked) {
			x = 1;
			break;
		}
	}
	if(x==0) {
		alert("Please select at least one record to delete.");
	}
	else {
		if(confirm('Are you sure you want to delete?')){
			document.deleteform.deletebut.value = 1;
			document.deleteform.submit();
		}
	}
}
function activeCheck(){
	var x = 0;
	for(var i=0; i<document.deleteform.elements.length;i++) {
		if(document.deleteform.elements[i].checked) {
			x = 1;
			break;
		}
	}
	if(x==0) {
		alert("Please select at least one record to Activate/Block.");
	}
	else {
		if(confirm('Are you sure you want to Activate/Block?')){
			document.deleteform.active.value = 1;
			document.deleteform.submit();
		}
	}
}
function setAll(){
	if(document.deleteform.setUnset.value == 'on') {
		for(var i=1;i<document.deleteform.elements.length;i++) {
			document.deleteform.elements[i].checked = true;
		}
		document.deleteform.setUnset.value = '';
	}
	else {
		for(var i=1;i<document.deleteform.elements.length;i++) {
			document.deleteform.elements[i].checked = false;
		}
		document.deleteform.setUnset.value = 'on';
	}
}
//-->
</script>
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