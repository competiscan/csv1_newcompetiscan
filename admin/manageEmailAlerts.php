<?php
$ALLOW_GROUPS = array(16);
require_once("../auth_auth.php");
include("top.php");
require_once("../includes/functions.php");

if(!isset($_REQUEST['page']) || $_REQUEST['page']=="" || $_REQUEST['page']<0) $page = 0;		
else $page = (int)$_REQUEST['page'];

if(!isset($_REQUEST['nor']) || $_REQUEST['nor']=="" || $_REQUEST['nor']<0) $no_of_records = 20;
else $no_of_records = (int)$_REQUEST['nor'];

if(isset($_REQUEST['show_All'])){
	$_SESSION['alert_searchText'] = '';
}
elseif(isset($_REQUEST['search_text']) && trim($_REQUEST['search_text'])!='') { 
	$_SESSION['alert_searchText'] = trim($_REQUEST['search_text']);
}

if(isset($_SESSION['alert_searchText']) && $_SESSION['alert_searchText']!=''){
	$search_key = mysqlLike($_SESSION['alert_searchText']);
	$addq = " AND emailAddress LIKE '%$search_key%'";
}
else $addq = '';

//select firstName,lastName,emailAddress,COUNT(emailAlert) as counts from cscan_users cu,cscan_search cs where (companyName='health net' OR emailAddress like '%healthnet%') AND cu.userID=cs.userID AND saved=1 group by cu.userID having counts>0 order by lastName,firstName,emailAddress
$query = "SELECT cscan_users.userID,firstName,lastName,emailAddress,COUNT(emailAlert) FROM cscan_users,cscan_search 
WHERE cscan_users.userID=cscan_search.userID AND emailAlert='1'$addq
GROUP BY cscan_search.userID";

$total_records = $DRW->num_rows($DRW->query($query,$DRW_read));

$max_page = ($total_records/$no_of_records);

$query = "$query ORDER BY emailAddress ASC LIMIT ".($page*$no_of_records).",".$no_of_records;

$result = $DRW->query($query,$DRW_read);
?>
<!-- bgcolor='#DDF9EE' -->
<table border="0" cellspacing='0' cellpadding='0' width="100%" bgcolor="white" style="border-collapse:collapse" class='text' bordercolor="#14734F" align="center">
	<tr class="adminhead"> 
		<td width="100%" align="center" height="25">
			MANAGE EMAIL ALERTS
		</td>
	</tr>
	<tr>
		<td width="100%" align="center">
			<table border="0" cellspacing='0' cellpadding='4' width="100%" bgcolor="white" style="border-collapse:collapse" class='text' bordercolor="#14734F">
				<tr>
					<td width="100%" align="left">
						<strong>Note:</strong> Click on any of the links under 'Login Email Address' column to view the email alert details of the user.
					</td>
				</tr>
				<tr><td>
				<form method="post" name="searchForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" style="display:inline;">
					<strong>Search by Email Address:</strong>
					<input type="text" name="search_text" class="input_box" />&nbsp;&nbsp;&nbsp;&nbsp;
					<input class="button" style="width:60px;" type="submit" name="search_Submit" value="Search" />
				</form>
				&nbsp;&nbsp;&nbsp;&nbsp;
				<form style="display:inline;" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post"><input class="button" style="width:70px" type="submit" name="show_All_but" value="Show All" /><input type="hidden" name="show_All" value="1" /></form>
			   	</td></tr>
				<tr>
					<td width="100%" align="center">
						
						<form name="form1" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
							Show
							<select name="nor" onchange="document.form1.submit();" class="input_box" style="width:50px">
								 <!-- <option value="5" <?php //if($no_of_records=="5") echo "selected"; ?>>5</option> -->
								<option value="10" <?php if($no_of_records=="10") echo "selected"; ?>>10</option>
								<option value="20" <?php if($no_of_records=="20") echo "selected"; ?>>20</option>
								<option value="30" <?php if($no_of_records=="30") echo "selected"; ?>>30</option>
								<option value="40" <?php if($no_of_records=="40") echo "selected"; ?>>40</option>
								<option value="50" <?php if($no_of_records=="50") echo "selected"; ?>>50</option>
								<option value="60" <?php if($no_of_records=="60") echo "selected"; ?>>60</option>
								<option value="70" <?php if($no_of_records=="70") echo "selected"; ?>>70</option>
								<option value="80" <?php if($no_of_records=="80") echo "selected"; ?>>80</option>
								<option value="90" <?php if($no_of_records=="90") echo "selected"; ?>>90</option>
								<option value="100" <?php if($no_of_records=="100") echo "selected"; ?>>100</option>
							</select>
							Records in a page.
						</form>
						
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td width="100%" align="center">
			<table border="0" cellspacing='0' cellpadding='8' width="100%" bgcolor="white" style="border-collapse:collapse" class='text' bordercolor="#14734F" align="center">
				<tr class="adminhead">
					<th align="left">
						Login Email Address
					</th>
					<th align="left">
						Full Name
					</th>
					<th align="left">
						Email Alerts
					</th>
					<th align="left">
						Last Sent/Changed
					</th>
				</tr>
<?php			
			$bgcolor = '';
			while($row = $DRW->fetch_array($result)) {
					if($bgcolor=="#DDF9EE") $bgcolor = "white";
					else $bgcolor = "#DDF9EE";
?>
					<tr bgcolor ="<?php echo $bgcolor; ?>">
						<td>
							<a href="<?php echo "emailAlertDetails.php?id=".$row[0]; ?>" class="hlinks" title="Click here to view the email alert details of this user">
								<strong><?php echo $row[3]; ?></strong>
							</a>
						</td>
						<td>
							<?php echo $row[1]." ".$row[2]; ?>
						</td>
						<td>
							<?php 
							echo $row[4]." Alert";
							if($row[4]!=1) print 's';
							?>
						</td>
						<td>
						<?php 
						//then get inner dates
						//then add sorting
						$query2 = "SELECT DATE_FORMAT(MAX(lastSentDate),'%m/%d/%Y %r') FROM cscan_search WHERE userID={$row[0]} AND emailAlert='1'";
						$rs2 = $DRW->query($query2,$DRW_read);
						$data = $DRW->fetch_row($rs2);
						if($data[0]!='00/00/0000 12:00:00 AM') print $data[0];
						else print '&nbsp;';
						?>
						</td>
					</tr>
<?php
			}
?>
			</table>
		</td>
	</tr>

<tr>
	<td width="100%" bgcolor="#14734F"  height="1">
		
	</td>
</tr>

<tr>
	<td width="100%" align="center">
		<table border="0" cellspacing='0' cellpadding='4' width="100%" bgcolor="white" style="border-collapse:collapse" class='text' bordercolor="#14734F" align="center">
			<tr>
				<td width="50%" align="right">
<?php				
					if($page<=0)
					{
?>						&laquo;&laquo; Previous
<?php				}
					else
					{
?>						<a href="<?php echo "manageEmailAlerts.php?page=".($page-1)."&nor=".$no_of_records; ?>" class="hlinks">
							&laquo;&laquo; Previous
						</a>
<?php				} ?>
				</td>
				<td width="50%" align="left">
<?php				if(($page+1)>=$max_page)
					{
?>
						Next &raquo;&raquo;
<?php				}
					else
					{
?>						<a href="<?php echo "manageEmailAlerts.php?page=".($page+1)."&nor=".$no_of_records; ?>" class="hlinks">
						Next &raquo;&raquo;
						</a>
<?php				}
?>
				</td>
			</tr>
		</table>
	</td>
</tr>
<tr>
	<td width="100%" align="center">
		<?php echo "Showing Records ".(($no_of_records*$page)+1)." to ".($no_of_records*($page+1))." of ".$total_records; ?>
	</td>
</tr>
</table>
<?php include 'bottom.php'; ?>