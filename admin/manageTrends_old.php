<?php 
$ALLOW_GROUPS = array(22);
require_once("../auth_auth.php");
include 'top.php'; 
?>
<form method="post" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<table width="100%" border="0" cellspacing="0" cellpadding="5" class="text" rules="none" style="border-color:#B9B9B9;border-collapse:collapse">
  <tr><td class="adminhead" align="center" colspan="4">TREND REPORT MANAGEMENT</td></tr>
  <!-- search and right buttons start-->
  <tr>
    <td colspan="4">
      <table border="0" width="100%" cellspacing="0" cellpadding="0">
        <tr valign="top">
          <td align="right" colspan="2">
            <table width="100%" border="0" cellspacing="0" cellpadding="0" class="text">
              <tr>
                <td><strong>Note</strong>: Click any of the following to modify the trend.</td>
                <td align="right"><input class="button" type="button" value="Add Trend" onclick="location.href='addTrend.php'; return false;" /></td>
                <td align="right" width="10%"><?php if(checkGroup(73)){?><input class="button" type="submit" name="submit1" ID="delBt" value="Delete" onclick="return confirmDel();" /><?php }?></td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <!-- search and right buttons close-->
<?php
	$message='';
	if(isset($_POST['submity'])) {
		$delID = $_POST['delID'];
                $emailData = [];
		for($i=0;$i<count($delID);$i++) {
                    $delThis = $delID[$i];
                    $sql = "DELETE FROM cscan_trend_report WHERE trend_id = '$delThis'";
                    if($DRW->query($sql,$DRW_main)){
                        $data = [
                            'auth_id' => $GLOBALS['AUTH_DATA']['userID'],
                            'auth_name' => authUserName($GLOBALS['AUTH_DATA']['userID']),
                            'deleted_id' => $delThis,
                            'sql_query' => $sql,
                            'ip_address' => ipAddress(),
                            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                            'delete_type' => 'Global Reports',
                            'is_mobile' => isMobile(),
                            'insert_date' => date("Y-m-d H:i:s")
                        ];
                        trackDelete($data);
                        $emailData[] = $data;
                    }
		}
		if($i > 0) {
			$message="<strong>$i</strong> Trend(s) deleted.";
		}
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

                    sendDevAlert('Caution! Data Deleted From Global Reports',$html);
                }
	}
?>
    <tr><td width="1%" class="adminhead"><input type="checkbox" name="setUnset" onclick="setAll()" /></td>
    <td class="adminhead">Trend</td><td class="adminhead">Link</td><td class="adminhead">Date Sort</td></tr>
    <tr><td colspan="4" align="center" class="error"><?php echo $message; ?></td></tr>
<?php

displayCategory(0);

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
					$sectorNamePrint = " &nbsp; &nbsp; $sectorNamePrint";
				}
			}
					
			echo "<tr class=\"$className\"><td colspan=\"4\" style=\"padding-top:10px;\"><strong>".$sectorNamePrint."</strong></td> </tr>";
			
			$sql = "SELECT trend_id,trend_name,DATE_FORMAT(trend_date, '%m/%d/%Y') AS trend_datef FROM cscan_trend_report LEFT JOIN cscan_sector ON (cscan_trend_report.category_id = cscan_sector.sectorID) WHERE cscan_sector.sectorID=$sectorID ORDER BY trend_date DESC";
			$rs = $DRW->query($sql,$DRW_read);
			$resultCount = $DRW->num_rows($rs);
			if($resultCount > 0) {
				while($row = $DRW->fetch_array($rs)) {
					$ID = $row['trend_id'];
					$trendName = $row['trend_name'];
					$trenddate = $row['trend_datef'];
					
					if ($className=='selected-bg') $className='white-bg';
					else $className='selected-bg';
?>
        <tr valign="top" class="<?php echo $className; ?>" >
			<td><input type="checkbox" name="delID[]" value="<?php echo $ID; ?>" /></td>
            <td><a class="hlinks" href="addTrend.php?id=<?php echo $ID; ?>" title="Click here to edit."><strong><?php echo $trendName; ?></strong></a></td>
            <td>competiscan.com/trend_report.php?trend_id=<?php echo $ID; ?></td>
            <td><?php echo $trenddate; ?></td>
          </tr>
<?php  
				}
			}
			else print "<tr><td class=\"selected-bg\">&nbsp;</td><td colspan=\"3\" class=\"selected-bg\"><em>None</em></td></tr>";
			displayCategory($sectorID,$sectorName);
		}
	}	 		
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
<?php include 'bottom.php'; ?>