<?php 

//testing w/ these entryID/productIDs
// 2012-12-12-01 / 439694 - test hs (chase logo)
// 2012-12-10-452 / 751219 - AT&T Home Phone and Internet

$ALLOW_GROUPS = array(33);
require_once("../auth_auth.php");
require_once("../includes/functions.php");
include 'top.php';
$values = array();
$keptvalue = false;
$newcount =0;
if(isset($_REQUEST['id'])){
	$cs_id = (int)$_REQUEST['id'];
	if(isset($_POST['send'])){
		$panelistID 	= $_POST['panelistID'];
		$productID	= $_POST['productID'];
		$questions	= '';
		$date 		= $_POST['date'];
		$cs_title 	= $_POST['cs_title'];
		$entryID 	= trim($_POST['entryID']);

		//attempt to move file for processing
		if (sizeof($_FILES) > 0 && $_FILES['survey_questions']['error'] == 0) {
			$filename   = $_FILES['survey_questions']['name'];
	        	$filetype   = $_FILES['survey_questions']['type']; //my test gave me "application/vnd.ms-excel"
		        $filesize   = $_FILES['survey_questions']['size'];
        		$filetmp    = $_FILES['survey_questions']['tmp_name'];
		        $test       = explode(".", $filename);

			if ($test[1] == "csv") {
				//not sure if I need to move the file.. prob don't want to save them anyway.. how concerned are we w/ validation?
                        	//if (!move_uploaded_file($filetmp, <whatev>)) {}
				
				//could check for filesize here too
				//if ($filesize > 1024 * 1000 * 5) {} //5mb
				$questions = file_get_contents($filetmp);
			}
		}

		$newcount = 0;
		$NEWproductID = 0;
		if(!empty($entryID)){
			$q = "SELECT productID FROM cscan_product_detail pd WHERE entryID='".$DRW->real_escape_string($entryID)."'";
			$row = $DRW->query($q,$DRW_read);
			if($DRW->num_rows( $row ) > 0){
				$rs = $DRW->fetch_row($row);
				$NEWproductID = $rs[0];
				if($NEWproductID!=0){
					//$q2 = "SELECT COUNT(*) AS num FROM cscan_insight WHERE productID = $NEWproductID AND cs_date = '".$DRW->real_escape_string($date)."' AND cs_id<>$cs_id";
					$q2 = "SELECT COUNT(*) AS num FROM cscan_survey WHERE productID = $NEWproductID AND cs_date = '".$DRW->real_escape_string($date)."' AND cs_id<>$cs_id";
					$result = $DRW->fetch_array($DRW->query($q2,$DRW_read));
					$newcount = $result['num'];
				}
			}
		}
		if($newcount == 0){ //does this one/day rule still apply for surveys?
			/*
				add/update survey w/ data from the form
			*/
			$q = "SELECT panelist_id FROM cscan_panelists WHERE competi_id='".$DRW->real_escape_string($panelistID)."'";
			$row = $DRW->query($q,$DRW_read);
			$panelistID = 0;
			if($DRW->num_rows($row) > 0) {
				$panelist_row = $DRW->fetch_row($row);
				$panelistID = $panelist_row[0];
			}

			if($cs_id == 0 ){
				$insert_query = "INSERT INTO cscan_survey
					(productID, panelistID, cs_date, cs_title, questions) 
					VALUES ($NEWproductID,$panelistID,'".$DRW->real_escape_string($date)."','".$DRW->real_escape_string($cs_title)."','".$DRW->real_escape_string($questions)."')";
				$DRW->query($insert_query,$DRW_main);
			}
			else {
				$questions_clause = ($questions == '' ? '' :", questions='".$DRW->real_escape_string($questions)."'");
				$insert_query = "UPDATE cscan_survey SET
					productID = $NEWproductID, panelistID = $panelistID, cs_date = '".$DRW->real_escape_string($date)."',
					cs_title='".$DRW->real_escape_string($cs_title)."' $questions_clause
					WHERE cs_id=$cs_id";
				$DRW->query($insert_query,$DRW_main);
			}

			if($NEWproductID!=0){ //set for the first time
				$insert_query = "UPDATE cscan_product_detail SET isSurvey = 1 WHERE productID = $NEWproductID";
				$DRW->query($insert_query,$DRW_main);
			}
			if($productID!=0 && $NEWproductID!=$productID){
				$checkV = "SELECT COUNT(*) FROM cscan_survey WHERE productID=$productID";
				$checkV = $DRW->query($checkV,$DRW_read);
				$vcounta = $DRW->fetch_row($checkV);
				if($vcounta[0]==0){
					$insert_query = "UPDATE cscan_product_detail SET isSurvey=0 WHERE productID=$productID";
					$DRW->query($insert_query,$DRW_main);
				}
			}
			// jm -- not sure what this is all about (getAllVariantsArray) 
			/*
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
			*/

			//calculateCI();
			
			@ob_end_clean();
			header("Location: {$_SERVER['PHP_SELF']}");
			exit;
		}
		$keptvalue = true; //jm - you can get rid of this if there's no restriction on entryId/day
		echo "<strong><span class=\"error\">That EntryId/Date combination already exist</span></strong><br />";
	}
	if($cs_id!=0){
		$q = "SELECT cs_title,cp.competi_id,productID,DATE_FORMAT(cs_date,'%m/%d/%Y'),cs_date,questions FROM cscan_survey cs LEFT JOIN cscan_panelists cp ON (cp.panelist_id=cs.panelistID) WHERE cs_id=$cs_id";
		$row = $DRW->query($q,$DRW_read);
		$rs = $DRW->fetch_row($row);

		$cs_title = $rs[0];
		$panelistID = $rs[1];
		$productID = $rs[2];
		$cs_datef = $rs[3];
		$cs_date = $rs[4];
		$questions = $rs[5];
		$button = 'Update';
		
		$q = "SELECT entryID FROM cscan_product_detail WHERE productID=$productID";
		$row = $DRW->query($q,$DRW_read);
		$rs = $DRW->fetch_row($row);
		$entryID = $rs[0];
	}
	else{
		$cs_title = '';
		$cs_date = '';
		$panelistID = '';
		$productID = 0;
		$entryID = '';
		$cs_datef = '';
		$questions = '';
		$button = 'Save';
	}
	$questions = (strlen($questions) > 400 ? substr($questions,0,400)."..." : $questions);
	$questions = str_replace("\n", "<br><br>", $questions);
	
	echo "<form method=\"POST\" name=\"frm1\" action=\"{$_SERVER['PHP_SELF']}\" enctype='multipart/form-data'>
	<script type=\"text/javascript\" src=\"js_calendar/calendar.js\"></script>
	<table>";
	if($keptvalue){
		$entryID = $_POST['entryID'];
		$cs_date = $_POST['date'];
		$values = $val;
	}
	if(empty($cs_date) || $cs_date=="0000-00-00"){ 
		$cs_date = date("Y-m-d");
	}
	echo "<tr><td class=\"bodytext\" valign=\"top\">Title</td><td class=\"bodytext\"><input type=\"text\" class=\"input_box\" name=\"cs_title\" size =\"50\" value=\"".htmlspecialchars($cs_title,ENT_QUOTES)."\" /></td></tr> ";
	echo "<tr><td class=\"bodytext\" >Entry ID</td><td><input type=\"text\" class=\"input_box\" name=\"entryID\" size =\"28\" value=\"".htmlspecialchars($entryID,ENT_QUOTES)."\" /></td></tr> ";
	echo "<tr><td class=\"bodytext\" >Panelist ID</td><td><input type=\"text\" class=\"input_box\" name=\"panelistID\" size =\"28\" value=\"".htmlspecialchars($panelistID,ENT_QUOTES)."\" /></td></tr> ";
	echo "<tr><td class=\"bodytext\" >Date</td><td><input type=\"text\" class=\"input_box\" name=\"date\" size =\"28\" readonly=\"readonly\" value=\"".htmlspecialchars($cs_date,ENT_QUOTES)."\" />
	<a href=\"#\" onclick=\"displayCalendar(document.frm1.date,'yyyy-mm-dd',this); return false;\"><img name=\"popcal2\" src=\"js_calendar/images/getcal.gif\" border=\"0\" alt=\"\" style=\"vertical-align:bottom;\" /></a>
	</td></tr> ";
	echo "<tr><td valign=\"top\" class=\"bodytext\" >Survey Questions</td><td><input type='file' name='survey_questions' id='survey_questions'></td></tr>";
	echo "<tr><td colspan=2></td></tr>";
	if ($questions != '') {
		echo "<tr>
		<td valign=\"top\" class=\"bodytext\" >Survey Questions<br>(preview)</td>
		<td><div style='background-color: #FAFAFA; padding: 10px; width: 300px; font-size: 11px; font-family: verdana, Helvetica, sans-serif;'>".$questions."</textarea></td>
		</tr>";
	}
	print '<tr><td>&nbsp;</td><td><input type="submit" class="button" name="subby" value="'.$button.'" /></td></tr>';
	echo "</table>";
	print '<input type="hidden" name="send" value="1" />';
	echo "<input type=\"hidden\" name=\"id\" value=\"$cs_id\" />";
	echo "<input type=\"hidden\" name=\"productID\" value=\"$productID\" />";
	print '</form>';
}
else {
	if(isset($_REQUEST['delID'])){
		$delID = $_REQUEST['delID'];
		$count = count($delID);
                $track_delete_data=array();
                $emailData = [];
		foreach ($delID as $cs_id) {
			$q = "SELECT productID FROM cscan_survey WHERE cs_id=$cs_id";
			$row = $DRW->query($q,$DRW_read);
			$rs = $DRW->fetch_row($row);
			$productID = $rs[0];
			
			if (!empty($productID)) {
				$checkV = "SELECT COUNT(*) FROM cscan_survey WHERE productID=$productID";
				$checkV = $DRW->query($checkV,$DRW_read);
				$vcounta = $DRW->fetch_row($checkV);
				if($vcounta[0]==0){
					$insert_query = "UPDATE cscan_product_detail SET isSurvey=0 WHERE productID=$productID";
					$DRW->query($insert_query,$DRW_main);
				}
			}

			/* jm
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
			*/
			$query="DELETE FROM cscan_survey WHERE cs_id=$cs_id";
                       
                        /* Added for track on delete operation */
                        
                        $track_delete_data=array();       
        
                        $track_delete_data = [
                                'auth_id' => $GLOBALS['AUTH_DATA']['userID'],
                                'auth_name' => authUserName($GLOBALS['AUTH_DATA']['userID']),
                                'deleted_id' => (int)$cs_id,
                                'sql_query' => $query,
                                'ip_address' => ipAddress(),
                                'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                                'delete_type' => 'Consumer Surveys',
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
                        sendDevAlert('Caution! Data Deleted From Consumer Surveys',$html);
                    }
                /*END  Added for track on delete */
		//calculateCI();

		@ob_end_clean();
		header("Location: {$_SERVER['PHP_SELF']}?del=$count");
		exit;
	}
	?>
	<form method="post" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<table width="100%" border="0" cellspacing="0" cellpadding="5" class="text" rules="none" style="border-color:#B9B9B9;border-collapse:collapse">
	  <tr><td class="adminhead" align="center" colspan="7">SURVEY MANAGEMENT</td></tr>
	  <tr>
	    <td colspan="7">
	      <table border="0" width="100%" cellspacing="0" cellpadding="0">
	        <tr valign="top">
	          <td align="right">
	            <table width="100%" border="0" cellspacing="0" cellpadding="0" class="text">
	              <tr>
	                <td>&nbsp;</td>
	                <td align="right"><input class="button" type="button" value="Add" onclick="location.href='<?php echo $_SERVER['PHP_SELF'].'?id=0'; ?>'; return false;" /></td>
	                <td align="right" width="10%">
                            <?php  if(checkGroup(70)){?>   
                            <input class="button" type="submit" name="submit1" ID="delBt" value="Delete" onclick="return confirmDel();" />
                            <?php }?>
                        </td>
	              </tr>
	            </table>
	          </td>
	        </tr>
	      </table>
	    </td>
	  </tr>
	    <tr><td width="1%" class="adminhead">
                 <?php  if(checkGroup(70)){?>   
                    <input type="checkbox" name="setUnset" onclick="setAll();" />
                 <?php }?>
                </td>
	    <td class="adminhead">Date</td><td class="adminhead">Panelist ID</td><td class="adminhead">Entry ID</td><td class="adminhead">Company/Title</td><td class="adminhead">&nbsp;</td></tr>
	<?php

	$q = "SELECT pd.productID,pd.company,cp.competi_id,pd.entryID,DATE_FORMAT(cs_date,'%m/%d/%Y'),cs_date,cs_id,cs_title FROM cscan_survey cs LEFT JOIN cscan_product_detail pd ON(cs.productID=pd.productID) LEFT JOIN cscan_panelists cp ON (cp.panelist_id=cs.panelistID) order by cs_date,entryID_sort1, entryID_sort2"; //get an explanation of these last two sort criteria
	$rows = $DRW->query($q,$DRW_read);
	while($rs = $DRW->fetch_row($rows)){
		$productID 	= $rs[0];
		$company 	= $rs[1];
		$panelistID	= $rs[2];
		$entryID 	= $rs[3];
		$cs_datef 	= $rs[4];
		$cs_date 	= $rs[5];
		$cs_id 		= $rs[6];
		$cs_title 	= $rs[7];
		if(empty($productID)){
			$productID = 0;
			$company = $entryID = '&nbsp;';
		}
		if(!empty($cs_title)){
			$company = $cs_title;
		}
                if(checkGroup(70)){
                    echo '<tr><td><input type="checkbox" name="delID[]" value="'.$cs_id.'" /></td><td>'.$cs_datef.'</td><td>'.$panelistID.'</td><td>'.$entryID.'</td><td>'.$company.'</td><td><a href="'.$_SERVER['PHP_SELF'].'?id='.$cs_id.'">Edit</a> &nbsp; <a href="../survey_questions_html.php?';
                }else{
                    echo '<tr><td></td><td>'.$cs_datef.'</td><td>'.$panelistID.'</td><td>'.$entryID.'</td><td>'.$company.'</td><td><a href="'.$_SERVER['PHP_SELF'].'?id='.$cs_id.'">Edit</a> &nbsp; <a href="../survey_questions_html.php?';
                }
		
		//if(empty($productID)){ not sure this applies here
			echo 'cs_id='.$cs_id;
		/*}
		else{
			echo 'productID='.$rs[0];
		}*/
		echo '" target="_blank">View</a></td></tr>';
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

include 'bottom.php';
?>
