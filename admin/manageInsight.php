<?php 
/*
alter table cscan_insight DROP PRIMARY KEY
alter table cscan_insight add PRIMARY KEY (ci_id)
create index product_date_insight_index on cscan_insight (productID,ci_date)
*/

$ALLOW_GROUPS = array(33);
require_once("../auth_auth.php");
require_once("../includes/functions.php");
include 'top.php';
$values = array();
$keptvalue = false;
$newcount =0;
if(isset($_REQUEST['id'])){
	$ci_id = (int)$_REQUEST['id'];
	if(isset($_POST['send'])){
		$productID	= $_POST['productID'];
		$date = $_POST['date'];
		$ci_title = $_POST['ci_title'];
		$entryID = trim($_POST['entryID']);
		$val = array(
			floatval($_POST['value'][0]),
			floatval($_POST['value'][1]),
			floatval($_POST['value'][2]),
			floatval($_POST['value'][3]),
			floatval($_POST['value'][4]),
			floatval($_POST['value'][5]),
			floatval($_POST['value'][6]),
			floatval($_POST['value'][7]),
			floatval($_POST['value'][8]),
			floatval($_POST['value'][9]),
			floatval($_POST['value'][10]),
			floatval($_POST['value'][11]),
			floatval($_POST['value'][12]),
			floatval($_POST['value'][13]),
			floatval($_POST['value'][14]),
			floatval($_POST['value'][15]),
			floatval($_POST['value'][16])
		);
		$isInsight = 0;
		$newcount = 0;
		$NEWproductID = 0;
		if(!empty($entryID)){
			$q = "SELECT productID FROM cscan_product_detail pd WHERE entryID='".$DRW->real_escape_string($entryID)."'";
			$row = $DRW->query($q,$DRW_read);
			if($DRW->num_rows( $row ) > 0){
				$rs = $DRW->fetch_row($row);
				$NEWproductID = $rs[0];
				if($NEWproductID!=0){
					$q2 = "SELECT COUNT(*) AS num FROM cscan_insight WHERE productID = $NEWproductID AND ci_date = '".$DRW->real_escape_string($date)."' AND ci_id<>$ci_id";
					$result = $DRW->fetch_array($DRW->query($q2,$DRW_read));
					$newcount = $result['num'];
				}
			}
		}
		if($newcount == 0){
			if($ci_id == 0 ){
				$insert_query = "INSERT INTO cscan_insight
					(value1,value2,value3,value4,value5,value6,value7,value8,value9,value10,value11,value12,value13,value14,value15,value16,value17, productID, ci_date,ci_title) 
					VALUES ({$val[0]},{$val[1]},{$val[2]},{$val[3]},{$val[4]},{$val[5]},{$val[6]},{$val[7]},{$val[8]},{$val[9]},
					{$val[10]},{$val[11]},{$val[12]}, {$val[13]}, {$val[14]},{$val[15]},{$val[16]}, $NEWproductID,'".$DRW->real_escape_string($date)."','".$DRW->real_escape_string($ci_title)."')";
				$DRW->query($insert_query,$DRW_main);
			}
			else {
				$insert_query = "UPDATE cscan_insight SET
					productID = $NEWproductID , value1 ={$val[0]},value2 = {$val[1]},value3 ={$val[2]},value4 = {$val[3]},value5 = {$val[4]},value6 = {$val[5]},value7 = {$val[6]},value8 = {$val[7]},value9 = {$val[8]},
					value10 = {$val[9]},value11 = {$val[10]},value12 = {$val[11]},value13 = {$val[12]},value14 = {$val[13]},value15 = {$val[14]},value16 = {$val[15]},value17 = {$val[16]}, ci_date = '".$DRW->real_escape_string($date)."',
					ci_title='".$DRW->real_escape_string($ci_title)."'
					WHERE ci_id=$ci_id";
				$DRW->query($insert_query,$DRW_main);
			}
			if($NEWproductID!=0){
				$variantArray = array();
				getAllVariantsArray((int)$NEWproductID,$variantArray);
				foreach($variantArray as $vid=>$varray){
					$insert_query = "UPDATE cscan_product_detail SET isInsight = 1 WHERE productID = $vid";
					$DRW->query($insert_query,$DRW_main);
				}
			}
			if($productID!=0 && $NEWproductID!=$productID){
				$variantArray = array();
				getAllVariantsArray((int)$productID,$variantArray);
				
				foreach($variantArray as $vid=>$varray){
					$checkV = "SELECT COUNT(*) FROM cscan_insight WHERE productID=$vid";
					$checkV = $DRW->query($checkV,$DRW_read);
					$vcounta = $DRW->fetch_row($checkV);
					if($vcounta[0]==0){
						$insert_query = "UPDATE cscan_product_detail SET isInsight=0 WHERE productID=$vid";
						$DRW->query($insert_query,$DRW_main);
					}
				}
			}
			
			calculateCI();
			
			@ob_end_clean();
			header("Location: {$_SERVER['PHP_SELF']}");
			exit;
		}
		$keptvalue = true;
		echo "<strong><span class=\"error\">That EntryId/Date combination already exist</span></strong><br />";
	}
	if($ci_id!=0){
		$q = "SELECT value1,value2,value3,value4,value5,value6,value7,value8,value9,value10,value11,value12,value13,value14,value15,value16,value17,
			ci_title,productID,DATE_FORMAT(ci_date,'%m/%d/%Y'),ci_date FROM cscan_insight ci WHERE ci_id=$ci_id";
		$row = $DRW->query($q,$DRW_read);
		$rs = $DRW->fetch_row($row);

		for($i=0;$i<=16;$i++){
			$values[$i]=$rs[$i];
		}
		$ci_title = $rs[$i++];
		$productID = $rs[$i++];
		$ci_datef = $rs[$i++];
		$ci_date = $rs[$i++];
		$button = 'Update';
		
		$q = "SELECT entryID FROM cscan_product_detail WHERE productID=$productID";
		$row = $DRW->query($q,$DRW_read);
		$rs = $DRW->fetch_row($row);
		$entryID = $rs[0];
	}
	else{
		$ci_title = '';
		$ci_date = '';
		$productID = 0;
		$entryID = '';
		$ci_datef = '';
		for($i=0;$i<=16;$i++){
			$values[$i]='';
		}
		$button = 'Save';
	}
	$total=0;
	for($i=0;$i<14;$i++){
		$total += $values[$i];
	}
	
	$category = array("Friendly","Personal","Trustworthy","Experienced","Innovative","Comfortable","Engaging","Professional",
	"Accommodating","Down to Earth","Honest","Easy to Understand","Contemporary","Confident","Good Value","Interested in Learning More","Likelihood to Respond");
	
	echo "<form method=\"POST\" name=\"frm1\" action=\"{$_SERVER['PHP_SELF']}\">
	<script type=\"text/javascript\" src=\"js_calendar/calendar.js\"></script>
	<table>";
	if($keptvalue){
		$entryID = $_POST['entryID'];
		$ci_date = $_POST['date'];
		$values = $val;
	}
	if(empty($ci_date) || $ci_date=="0000-00-00"){ 
		$ci_date = date("Y-m-d");
	}
	echo "<tr><td class=\"bodytext\" valign=\"top\">Title</td><td class=\"bodytext\"><input type=\"text\" class=\"input_box\" name=\"ci_title\" size =\"50\" value=\"".htmlspecialchars($ci_title,ENT_QUOTES)."\" /><br /><em>Leave blank to use Company, Entry ID</em></td></tr> ";
	echo "<tr><td class=\"bodytext\" >Entry ID</td><td><input type=\"text\" class=\"input_box\" name=\"entryID\" size =\"28\" value=\"".htmlspecialchars($entryID,ENT_QUOTES)."\" /></td></tr> ";
	echo "<tr><td class=\"bodytext\" >Date</td><td><input type=\"text\" class=\"input_box\" name=\"date\" size =\"28\" readonly=\"readonly\" value=\"".htmlspecialchars($ci_date,ENT_QUOTES)."\" />
	<a href=\"#\" onclick=\"displayCalendar(document.frm1.date,'yyyy-mm-dd',this); return false;\"><img name=\"popcal2\" src=\"js_calendar/images/getcal.gif\" border=\"0\" alt=\"\" style=\"vertical-align:bottom;\" /></a>
	</td></tr> ";
	for($i=0;$i<=16;$i++){
		echo "<tr><td class=\"bodytext\" >{$category[$i]}</td><td><input type=\"text\"  class=\"input_box\" name=\"value[]\" size =\"4\" value=\"".doubleval($values[$i])."\" /></td></tr>";
	}
	print '<tr><td>&nbsp;</td><td><input type="submit" class="button" name="subby" value="'.$button.'" /></td></tr>';
	echo "</table>";
	print '<input type="hidden" name="send" value="1" />';
	echo "<input type=\"hidden\" name=\"id\" value=\"$ci_id\" />";
	echo "<input type=\"hidden\" name=\"productID\" value=\"$productID\" />";
	print '</form>';
}
else {
	if(isset($_REQUEST['delID'])){
		$delID = $_REQUEST['delID'];
                $track_delete_data=array();
                $emailData = [];
		$count = count($delID);
		foreach ($delID as $ci_id) {
			$q = "SELECT productID FROM cscan_insight WHERE ci_id=$ci_id";
			$row = $DRW->query($q,$DRW_read);
			$rs = $DRW->fetch_row($row);
			$productID = $rs[0];
			
			if(!empty($productID)){
				$variantArray = array();
				getAllVariantsArray((int)$productID,$variantArray);
				
				foreach($variantArray as $vid=>$varray){
					$checkV = "SELECT COUNT(*) FROM cscan_insight WHERE productID=$vid";
					$checkV = $DRW->query($checkV,$DRW_read);
					$vcounta = $DRW->fetch_row($checkV);
					if($vcounta[0]==0){
						$insert_query = "UPDATE cscan_product_detail SET isInsight=0 WHERE productID=$vid";
						$DRW->query($insert_query,$DRW_main);
					}
				}
			}
			$query="DELETE FROM cscan_insight WHERE ci_id=$ci_id";
                        /* Added for track on delete operation */
                        
                        $track_delete_data=array();       
        
                        $track_delete_data = [
                                'auth_id' => $GLOBALS['AUTH_DATA']['userID'],
                                'auth_name' => authUserName($GLOBALS['AUTH_DATA']['userID']),
                                'deleted_id' => (int)$ci_id,
                                'sql_query' => $query,
                                'ip_address' => ipAddress(),
                                'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                                'delete_type' => 'Consumer Insight',
                                'is_mobile' => isMobile(),
                                'insert_date' => date("Y-m-d H:i:s")
                            ];
                        trackDelete($track_delete_data);
                        $emailData[] = $track_delete_data;
                        
                        /*END  Added for track on delete operation*/
			$DRW->query($query,$DRW_main);
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
                        sendDevAlert('Caution! Data Deleted From Consumer Insight',$html);
                    }
                /*END  Added for track on delete */
		calculateCI();

		@ob_end_clean();
		header("Location: {$_SERVER['PHP_SELF']}?del=$count");
		exit;
	}
	?>
	<form method="post" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<table width="100%" border="0" cellspacing="0" cellpadding="5" class="text" rules="none" style="border-color:#B9B9B9;border-collapse:collapse">
	  <tr><td class="adminhead" align="center" colspan="7">INSIGHT MANAGEMENT</td></tr>
	  <tr>
	    <td colspan="7">
	      <table border="0" width="100%" cellspacing="0" cellpadding="0">
	        <tr valign="top">
	          <td align="right">
	            <table width="100%" border="0" cellspacing="0" cellpadding="0" class="text">
	              <tr>
	                <td><strong>Note</strong>: Click any of the following to modify the trend.</td>
	                <td align="right"><input class="button" type="button" value="Add" onclick="location.href='<?php echo $_SERVER['PHP_SELF'].'?id=0'; ?>'; return false;" /></td>
	                <td align="right" width="10%">
                            <?php  if(checkGroup(71)){?> 
                                <input class="button" type="submit" name="submit1" ID="delBt" value="Delete" onclick="return confirmDel();" />
                            <?php } ?>
                        </td>
	              </tr>
	            </table>
	          </td>
	        </tr>
	      </table>
	    </td>
	  </tr>
	    <tr><td width="1%" class="adminhead">
                <?php if(checkGroup(71)){?> 
                    <input type="checkbox" name="setUnset" onclick="setAll();" />
                <?php } ?>    
                </td>
	    <td class="adminhead">Date</td><td class="adminhead">Entry ID</td><td class="adminhead">Company/Title</td><td class="adminhead">Average Order</td><td class="adminhead">Response Order</td><td class="adminhead">&nbsp;</td></tr>
	<?php

	$q = "SELECT pd.productID,company,entryID,DATE_FORMAT(ci_date,'%m/%d/%Y'),ci_date,r_order,a_order,ci_id,ci_title FROM cscan_insight ci LEFT JOIN cscan_product_detail pd ON(ci.productID=pd.productID) order by ci_date,entryID_sort1, entryID_sort2";
	$rows = $DRW->query($q,$DRW_read);
	while($rs = $DRW->fetch_row($rows)){
		$productID = $rs[0];
		$company = $rs[1];
		$entryID = $rs[2];
		$ci_datef = $rs[3];
		$ci_date = $rs[4];
		$r_order = $rs[5];
		$a_order = $rs[6];
		$ci_id = $rs[7];
		$ci_title = $rs[8];
		if(empty($productID)){
			$productID = 0;
			$company = $entryID = '&nbsp;';
		}
		if(!empty($ci_title)){
			$company = $ci_title;
		}
                if(checkGroup(71)){
                    echo '<tr><td><input type="checkbox" name="delID[]" value="'.$ci_id.'" /></td><td>'.$ci_datef.'</td><td>'.$entryID.'</td><td>'.$company.'</td><td>'.number_format($a_order*100).'</td><td>'.number_format($r_order*100).'</td><td><a href="'.$_SERVER['PHP_SELF'].'?id='.$ci_id.'">Edit</a> &nbsp; <a href="../graph_spider_html.php?';
                }else{
                    echo '<tr><td></td><td>'.$ci_datef.'</td><td>'.$entryID.'</td><td>'.$company.'</td><td>'.number_format($a_order*100).'</td><td>'.number_format($r_order*100).'</td><td><a href="'.$_SERVER['PHP_SELF'].'?id='.$ci_id.'">Edit</a> &nbsp; <a href="../graph_spider_html.php?';
                }
		
		if(empty($productID)){
			echo 'ci_id='.$ci_id;
		}
		else{
			echo 'productID='.$rs[0];
		}
		echo '&amp;avg=1" target="_blank">View</a></td></tr>';
	}

	?>
	</table>
	<input type="hidden" name="submity" value="1" /></form>
	<script type="text/JavaScript">
	<!--
	function confirmDel()
	{
		var goAheadFlag = 0;
		for(var i=0;i<document.frm1.elements.length;i++)
		{
			if(document.frm1.elements[i].checked == true)
			{
				goAheadFlag = 1;
			}
		}
		if(goAheadFlag)
		{
			return confirm("Are you sure to delete ?");
		}
		else
		{
			alert('Please select at least one record to delete !!!');
			return false;
		}
	}

	function setAll()
	{
		if(document.frm1.setUnset.value == 'on')
		{
			for(i=1;i<document.frm1.elements.length;i++)
			{
				document.frm1.elements[i].checked = true;
			}
			document.frm1.setUnset.value = '';
		}
		else
		{
			for(i=1;i<document.frm1.elements.length;i++)
			{
				document.frm1.elements[i].checked = false;
			}
			document.frm1.setUnset.value = 'on';
		}
	}
	//-->
	</script>
	<?php 
}

function calculateCI(){
	global $DRW,$DRW_read,$DRW_main,$DRW_crm;
	$i = 0;
	$lastval = false;
	$q = "SELECT COUNT(DISTINCT value17) FROM cscan_insight";
	$row = $DRW->query($q,$DRW_read);
	$rs = $DRW->fetch_row($row);
	$total = $rs[0];
	$q = "SELECT value17,ci_id FROM cscan_insight ORDER BY value17 ASC";
	$row = $DRW->query($q,$DRW_read);
	while($rs = $DRW->fetch_row($row)){
		$value = $rs[0];
		$ci_id = $rs[1];
		if($lastval===false || $value!=$lastval){
			$lastval = $value;
			$i++;
		}
		
		$insert_query = "UPDATE cscan_insight SET r_order=".($i/$total)." WHERE ci_id=$ci_id";
		$DRW->query($insert_query,$DRW_main);
	}
	$i = 0;
	$lastval = false;
	$q = "SELECT COUNT(DISTINCT (value1+value2+value3+value4+value5+value6+value7+value8+value9+value10+value11+value12+value13+value14)/14) FROM cscan_insight";
	$row = $DRW->query($q,$DRW_read);
	$rs = $DRW->fetch_row($row);
	$total = $rs[0];
	$q = "SELECT (value1+value2+value3+value4+value5+value6+value7+value8+value9+value10+value11+value12+value13+value14)/14 as avg,ci_id FROM cscan_insight ORDER BY avg ASC";
	$row = $DRW->query($q,$DRW_read);
	while($rs = $DRW->fetch_row($row)){
		$value = $rs[0];
		$ci_id = $rs[1];
		if($lastval===false || $value!=$lastval){
			$lastval = $value;
			$i++;
		}
		
		$insert_query = "UPDATE cscan_insight SET a_order=".($i/$total)." WHERE ci_id=$ci_id";
		$DRW->query($insert_query,$DRW_main);
	}
}

include 'bottom.php';
?>
