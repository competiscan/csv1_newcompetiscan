<?php 
$ALLOW_GROUPS = array(22);
require_once "../auth_auth.php";
include 'top.php';
?>
<table width="100%" border="0" cellspacing="0" cellpadding="5" class="text">
  <tr>
	<td class="adminhead" align="center">CHANGE TREND INFORMATION</td>
  </tr>
  <tr>
	<td align ="right" class="bodytext"><span class="error" style="font-weight:bold;">* required field</span></td>
  </tr>
<?php
	if(isset($_REQUEST['id'])) $updID = $_REQUEST['id'];
	else $updID = '';
	if(isset($_POST['submity'])) {
		$trendname = $DRW->real_escape_string(trim($_POST['trendname']));
		$trendlink = $DRW->real_escape_string(trim($_POST['url']));
		$trendcat = $DRW->real_escape_string(trim($_POST['catdropdown']));
		
		$trend_date = $_POST['trenddate_y'].'-'.$_POST['trenddate_m'].'-'.$_POST['trenddate_d'];;
		
		if($updID == '') {
			$sql = "INSERT INTO cscan_trend_report SET trend_name='$trendname' , trend_link='$trendlink', category_id ='$trendcat', trend_date='$trend_date'";
			$actMsg = 'added';
			$DRW->query($sql,$DRW_main);
		}
		if(($_POST['submit'] == 'Update')) {
			$sql = "UPDATE cscan_trend_report SET trend_name='$trendname' , trend_link='$trendlink', category_id='$trendcat', trend_date='$trend_date' WHERE trend_id='$updID'";
			$actMsg = 'updated';
			$DRW->query($sql,$DRW_main);
		}
		echo "<tr><td align=\"center\">Trend has been $actMsg sucessfully.</td></tr>";
		if($_POST['submit'] == 'Save & Add More'){
			ob_end_clean();
			header("Location: addTrend.php?a=1");
			exit;
		}
		else{
			ob_end_clean();
			header("Location: manageTrends.php");
			exit;
		}
	}
	elseif($updID!='') {
		$sql = "SELECT trend_name, trend_link, category_id,trend_date FROM cscan_trend_report WHERE trend_id='$updID'";
		$editRS = $DRW->query($sql,$DRW_read);
		$editRS = $DRW->fetch_array($editRS);
		$trendname = $editRS['trend_name'];
		$trendlink = $editRS['trend_link'];
		$category = $editRS['category_id'];
		$trenddate_y = substr($editRS['trend_date'],0,4);
		$trenddate_m = substr($editRS['trend_date'],5,2);
		$trenddate_d = substr($editRS['trend_date'],8,2);
	}
	else{
		$trendname = '';
		$trendlink = 'https://files.competiscan.com/downloads';
		$category = 0;
		$trenddate_y = date('Y');
		$trenddate_m = date('m');
		$trenddate_d = date('d');
	}
?>
    <tr>
		<td align="center">
		<form method="post" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" onsubmit="return validate();">
		<table border="0" cellspacing="0" cellpadding="0"> <tr><td style="border:solid 1px #14734F;">
		<table border="0" cellspacing="0" cellpadding="4">
			<tr>
				<td class="subhead" align="center" colspan="2">
				<?php if($updID!='')  echo 'UPDATE'; 
					  else echo 'ADD'; ?> TREND INFO</td>
			</tr>
			<tr>
			<td colspan="2">&nbsp;</td>
			 </tr>
			<tr>
				<td class="bodytext" align="right"><span class='error'>*</span> Trend Title:</td>
				<td><input type="text" name="trendname" size="60" class="input_box" maxlength="200" value="<?php echo htmlspecialchars($trendname,ENT_QUOTES);?>" /></td>
			</tr>
			<tr>
				<td class="bodytext" align="right"><span class="error">*</span> Trend URL:</td>
	 			<td><input type="text" name="url" size="60" class="input_box" maxlength="200" value="<?php echo htmlspecialchars($trendlink, ENT_QUOTES); ?>"/></td>
	 		</tr>
  			<?php
  			echo "<tr><td class=\"bodytext\" align=\"right\"><span class=\"error\">*</span> Category:</td><td class=\"bodytext\"><select name=\"catdropdown\" class=\"input_box\"><option value=\"0\">&nbsp;</option>";
  			
  			displayCategory(0);
  			
  			echo "</select> <a href=\"managesector.php\">Add Category</a></td></tr> ";
  			?>
			<tr>
				<td class="bodytext" align="right">Date Sort:</td>
				<td class="bodytext"><?php 
				$start_year = 2005;
				$to_year = (int)date('Y');
				print "<select name=\"trenddate_y\" size=\"1\" class=\"input_box\"><option value=\"00\"></option>";
				for($i=$start_year;$i<=$to_year;$i++){
					print "<option value=\"$i\"";
					if($i==$trenddate_y) print " selected";
					print ">$i";
				}
				print "</select>
				<select name=\"trenddate_m\" size=\"1\" class=\"input_box\"><option value=\"00\"></option>";
				$month_name = array('01'=>"January",'02'=>"February",'03'=>"March",'04'=>"April",'05'=>"May",'06'=>"June",'07'=>"July",'08'=>"August",'09'=>"September",'10'=>"October",'11'=>"November",'12'=>"December");
				foreach($month_name as $key=>$value){
					print "<option value=\"$key\"";
					if($key==$trenddate_m) print " selected";
					print ">$value ($key)";
				}
				print "</select> <select name=\"trenddate_d\" size=\"1\" class=\"input_box\"><option value=\"00\"></option>";
				for($i=1;$i<=31;$i++){
					$day = str_pad($i,2,'0',STR_PAD_LEFT);
					print "<option value=\"$day\"";
					if($day==$trenddate_d) print " selected";
					print ">$day";
				}
				print "</select>";
				?></td>
			</tr>
			<tr>
			<td colspan="2">&nbsp;</td>
			 </tr>
			<tr>
				<td>&nbsp;</td>
				<td>
				<?php if($updID == ''){?>
				<input class="button" type="submit" name="submit" value="Save" />
				<input class="button" type="submit" name="submit" value="Save &amp; Add More" />
				<?php } else{ ?>
				<input class="button" type="submit" name="submit" value="Update" />
				<input type="hidden" name="id" value="<?php echo $updID; ?>" />
				<?php }?>
				<input class="button" type="button" value="Cancel" onclick="location.href='manageTrends.php'; return false;" />
				</td>
			</tr>
	</table>
	</td></tr>
	</table>
	<input type="hidden" name="submity" value="1" /></form>
    </td></tr>
</table>
<script type="text/JavaScript">
<!--
function validate()
{
	var url=document.frm1.url.value=trimspace(document.frm1.url.value);
	var trendname=document.frm1.trendname.value=trimspace(document.frm1.trendname.value);
	var catdropdownid = document.frm1.catdropdown.selectedIndex;
	
	if(trendname == '')
	{
		alert('Please enter a trend name');
		document.frm1.trendname.focus();
		return false;
	}
	if(url == ''){
		alert('Please enter a URL');
		document.frm1.url.focus();
		return false;
	}
	if(catdropdownid<1){
		alert('Please select a Category');
		document.frm1.url.focus();
		return false;
	}
}
//-->
</script>
<?php 
function displayCategory($ID,$parentName = '') {
	global $DRW,$DRW_read,$DRW_main,$DRW_crm;
	$sqla = "SELECT sectorID,sectorName FROM cscan_sector WHERE parentID = '$ID' ORDER BY sectorName ASC";
	$rsa = $DRW->query($sqla,$DRW_read);
	$resultCounter = $DRW->num_rows($rsa);
	if($resultCounter > 0) {
		while($rowa = $DRW->fetch_array($rsa)) {	
			$sectorID = $rowa['sectorID'];
			if(!checkSector($sectorID) && !checkCategory($sectorID) && !checkSubCategory($sectorID)){
				continue;
			}
			$sectorNamePrint = $sectorName = $rowa['sectorName'];
			$className = 'white-bg';
			if($parentName!='') {
				$sectorNamePrint = $sectorName = "$parentName : $sectorName";
				for($i=0;$i<substr_count($sectorName,':');$i++){
					$sectorNamePrint = " &nbsp; $sectorNamePrint";
				}
			}
			if($sectorID == $GLOBALS['category'] ){
				echo "<option value=\"".$sectorID."\" selected=\"selected\"> ".$sectorNamePrint."</option>";
			}
			else {
				echo "<option value=\"".$sectorID."\">";
				print $sectorNamePrint;
				print "</option>";
			}
			displayCategory($sectorID,$sectorName);
		}
	}	 		
}

include 'bottom.php'; ?>