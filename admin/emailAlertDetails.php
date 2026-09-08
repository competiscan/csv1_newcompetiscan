<?php
$ALLOW_GROUPS = array(16);
require_once("../auth_auth.php");
include("top.php");
require_once("../includes/functions.php");
$userID = $_REQUEST['id'];

$weekArray = array(7=>'&nbsp;',0=>'Sun',1=>'Mon',2=>'Tue',3=>'Wed',4=>'Thu',5=>'Fri',6=>'Sat');
?>
<table border="0" cellspacing='0' cellpadding='0' width="100%" style="border-collapse:collapse" class='text' bordercolor="#14734F">
<?php
	$query = "SELECT emailAddress FROM cscan_users WHERE userID='".$userID."'";

	$result = $DRW->query($query,$DRW_read);
	if(!$DRW->num_rows($result))
	{
?>
		<tr> 
			<td width="100%" align="center">
				<table border="0" cellspacing='0' cellpadding='4' width="50%" class='text' align="center">
					<tr>
						<td width="100%" align="center">
							<b>The record for this user doesn't exist</b>				
						</td>
					</tr>
				</table>
			</td>
		</tr>
	<table>
	</td>
	</tr>
	</table>
<?php
	exit;
	}

	$row_user = $DRW->fetch_array($result);
?>

	<tr class="adminhead"> 
		<td width="100%" align="center" height="25">
			Email alert details of user : <?php echo $row_user['emailAddress'];?>
		</td>
	</tr>
	<tr>
		<td width="100%" align="center">
<?php

	$savedQuery = "SELECT ID,searchName,emailAlert,notify,sendTo,mail_format,weekday,DATE_FORMAT(lastSentDate,'%m/%d/%Y %r') as lastSentDate_f FROM cscan_search where userID ='".$userID."' AND emailAlert='1' ORDER BY searchName ASC";

	$savedQuery = $DRW->query($savedQuery,$DRW_read);
?> 
		<br><table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" >
<?php
						if($DRW->num_rows($savedQuery) > 0)
						{
?>
						<tr>
							<td class='bodytext' width="100%" align="center"> 
								
								<table width="100%" border="0" bordercolor='#000000' cellspacing="0" cellpadding="4" style ='border-collapse:collapse;' class='bodytext' rules="1">
									<tr class='adminhead'>
										<td align='left'><b>Search Name</b></td>
										<td align='left'><b>Search keywords</b></td>
										
										<td align='left'><b>Send To</b></td>
										<td align='left'><b>Notify</b></td>
										<td align='left'><b>Every</b></td>
										<td align='left'><b>Format</b></td>
										<td align='left'><b>Last Sent</b></td>
									</tr>
									
									<?php
										$className='';
										$bgcolor='';
										while($row = $DRW->fetch_assoc($savedQuery))
										{
											if ($className=='selected-bg1') $className='white-bg';
											else $className='selected-bg1';

											if($bgcolor=="#DDF9EE") $bgcolor="white";
											else $bgcolor="#DDF9EE";
									?>
									<tr bgcolor="<?php echo $bgcolor; ?>">
										<td class='bodytext' valign="top">
											<?php echo htmlspecialchars($row['searchName']); ?>
										</td>
										<td class='bodytext' valign="top">
											<?php 
											list($displayKeywords,$wordArray) = getKeywords($row['ID']);
											print $displayKeywords;
											 ?>
										</td>
										<td class="bodytext" valign="top">
											<?php echo $row['sendTo']; ?>
										</td>
										<td class='bodytext' valign="top">
											<?php echo $row['notify']; ?>
										</td>
										<td class='bodytext' valign="top">
											<?php 
											if(isset($weekArray[$row['weekday']])) echo $weekArray[$row['weekday']];
											else echo '&nbsp;'; 
											?>
										</td>
										<td class='bodytext' valign="top">
											<?php echo $row['mail_format']; ?>
										</td>
										<td class='bodytext' valign="top">
											<?php echo $row['lastSentDate_f']; ?>
										</td>
									</tr>

								<?php
									
									}
								?>
									
								</table>
								
							</td>
						</tr>
						<?php
						}
						else
						{
						?>
						<tr>
							<td colspan = '7' align='center' class='error'>No Record Found</td>
						</tr>
						<?php
						}
						?>
					</table>
				</td>
			</tr>
</table>
<?php 
include("bottom.php");
?>