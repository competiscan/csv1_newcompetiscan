<?php
// ini_set('display_startup_errors', 1);
// ini_set('display_errors', 1);
// error_reporting(-1);
$TITLE = 'Competiscan Saved Searches';
require_once('includes/paginator.php');       //paginator class.
require_once('includes/paginator_html.php');  //paginator_html class.
require_once('panelist_top.php');

$sql = "SELECT *,DATE_FORMAT(createdAt,'%m/%d/%Y') AS createdAt_ FROM cscan_content_site_search WHERE userID='{$AUTH_DATA['userID']}' AND searchSave=1";
$LinkToPage = 'imap.php';
$result = $DRW->query($sql,$DRW_read);


if(isset($_REQUEST['page'])) {
	$page = (int)$_REQUEST['page'];
}
else {
	$page = 1;
}
if(isset($_POST['send'])) {
	//echo '<pre>';print_r($_POST);die;
	while($row = $DRW->fetch_assoc($result)) {
		if(isset($_POST['name'.$row['ID']])){
			$name = trim($_POST['name'.$row['ID']]);
			$emailAlert = isset($_POST['emailAlert'.$row['ID']])?1:0;
			$updateQuery = "UPDATE cscan_content_site_search SET searchName='".$DRW->real_escape_string($name)."', emailAlert=".$DRW->real_escape_string($emailAlert)." WHERE ID='".$row['ID']."'";
			$DRW->query($updateQuery,$DRW_main);
		}
	}
	ob_end_clean();
	header("Location: {$_SERVER['PHP_SELF']}?save=1&page=$page");
	exit;
}

if(isset($_GET['delID'])) {
	$delID = $_GET['delID'];
	$sql = "SELECT searchName,userID FROM cscan_content_site_search WHERE ID='".$DRW->real_escape_string($delID)."' AND searchSave='1'";
	$result = $DRW->query($sql,$DRW_read);
	$rs = $DRW->fetch_array($result);
	$count_result = $DRW->query("SELECT COUNT(*) FROM cscan_content_site_search WHERE userID='{$rs['userID']}' AND searchSave='1'",$DRW_read);
	$count = $DRW->fetch_row($count_result);
	if($count[0]>1){
		$sql = "DELETE FROM cscan_content_site_search WHERE ID IN(".$DRW->real_escape_string($delID).")";
		$DRW->query($sql,$DRW_main);
	}else{
		$sql = "DELETE FROM cscan_content_site_search WHERE ID='".$DRW->real_escape_string($delID)."'";
		$DRW->query($sql,$DRW_main);
	}
	ob_end_clean();
	header("Location: {$_SERVER['PHP_SELF']}?del=".urlencode($rs['searchName']));
	exit;
}
if (isset($_POST['delIDs'])) {
	$delIDs = explode(',', $_POST['delIDs']);
	foreach ($delIDs as $delID) {
		$sql = "DELETE FROM cscan_content_site_search WHERE ID='".$DRW->real_escape_string($delID)."'";
		$DRW->query($sql,$DRW_main);
	}
	ob_end_clean(); //where would rogue output even come from?
	header("Location: {$_SERVER['PHP_SELF']}?numdel=".count($delIDs));
        exit;
}

if(checkGroup(20)){
	print "<div style=\"margin-bottom:4px;\"><form action=\"{$_SERVER['PHP_SELF']}\" style=\"display:inline;\" method=\"get\"><input class=\"button\" type=\"submit\" name=\"checkmailsub\" value=\"Check Mail\" /> &nbsp; <input class=\"button\" type=\"submit\" name=\"report\" value=\"Get Report\" onclick=\"document.location.href='panelist_report_month.php'; return false;\" /> ";
	print "<input type=\"hidden\" name=\"checkmail\" value=\"1\" /></form>";

	print "</div>";
}

echo '<div><div style="float:left;">';
$n = 0;
$last  = count($contactType) - 1;
foreach($contactType as $contact_type_m_c=>$contact_type_m_cTitle){
	if(checkGroup(20) || $n==2){
		print "<a href=\"{$LinkToPage}?ctype=$n\" class=\"bluelink\">$contact_type_m_cTitle</a>";
		if($n!=$last) print ' &nbsp; | &nbsp; ';
	}
	$n++;
}
echo '</div>';
?>
<div style="float:right;">
</div>
</div>
<div style="clear:both;height:5px;">&nbsp;</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script type="text/JavaScript">
<!--
function delConfirm() {
	return confirm('Delete?');
}

function chk_search_name(searchName) {
	if(searchName=='') {
		alert('Search Name cannot be blank');
		return false;
	}
	return true;
}
function select_all_checks(check_on) {
	if (check_on) {
		$('.del_checkboxes').prop('checked', true);
	} else {
		$('.del_checkboxes').prop('checked', false);
	}
	return false;
}
function delConfirmAll() {
	var num_checked = 0;
	var delID_arr = [];
	$('.del_checkboxes').each(function() {
		if ($(this).prop('checked')) {
			num_checked++;
			delID_arr.push($(this).attr('id'));
		}
	});
	if (num_checked == 0) {
		alert('None selected');
		return false;
	} else {
		if (confirm('Delete '+num_checked+ ' search(es)?')) {
			var delIDs = delID_arr.join();
			$('#delIDs').val(delIDs);
			$('#delSelectedForm').submit();
		}
	}
}
//-->
</script>

<form method="post" name="delSelectedForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" id="delSelectedForm">
	<input type='hidden' name='delIDs' id='delIDs' value='' />
</form>


<form method="post" name="delForm" action="<?php echo $_SERVER['PHP_SELF'].'?page='.$page; ?>">
<table width="100%" border="0" cellpadding="5" cellspacing="0" class="bodytext">
  <?php
 $num_of_rows = $DRW->num_rows($result);
if($num_of_rows > 0){
	$pagelimit = 10;
	$a = new Paginator_html($page,$num_of_rows);
  	$a->set_Limit($pagelimit);
  	$a->set_Links(3);
	$limit1 = $a->getRange1();
	$limit2 = $a->getRange2();
	$sql .= " ORDER BY priority DESC LIMIT $limit1,$limit2";

	$result = $DRW->query($sql,$DRW_read);
?>
    <tr>
      <td class="subHead" width="12%"><strong>Search Name</strong></td>
			<td class="subHead" width="8%"><strong>Email Alert</strong></td>
			<td class="subHead" width="11%"><strong>Panelist</strong></td>
			<td class="subHead" width="11%"><strong>Search Date</strong></td>
      <td class="subHead" width="36%"><strong>Search Criteria</strong></td>
      <td width="15%" align="center" class="subHead"><strong>Options</strong></td>
      <td class="subHead" width="7%" align="center"><strong>&#10004;</strong></td>
    </tr>
    <tr>
      <td colspan="6" class="error"><?php echo isset($message)?$message:''; ?></td>
      <td colspan="1" style="text-align: right;">
	<a href='#' class="HyperLink" onClick="return select_all_checks(true);">Select All</a><br />
	<a href='#' class="HyperLink" onClick="return select_all_checks(false);">Un-Select All</a>
      </td>
    </tr>
    <?php
	$className = 'white-bg';
  $sql = "SELECT DISTINCT countryCode,country FROM cscan_state JOIN ISO31661_alpha2code ON (code=countryCode) ORDER BY country";
  $rs = $DRW->query( $sql,$DRW_read2 );
  $proviences = [];

	while($row = $DRW->fetch_row($rs) ) {
		//$proviences[$row[0]] = htmlspecialchars($row[1]);
    $sql1 = "select stateID,stateName,stateCode from cscan_state WHERE countryCode='" . $row[0] . "'  ORDER BY stateName";
    $rs1 = $DRW->query( $sql1,$DRW_read2 );
    while($row1 = $DRW->fetch_assoc($rs1) ) {
      //can use when we want country code wise states
      //$proviences[$row[0]][$row1['stateID']] = $row1['stateName'];
      $proviences[$row1['stateID']] = $row1['stateName'];
    }
	}
  //echo '<pre>';print_r($proviences[61]);die;
  function getDisplayTextForImap($recordSet){
    $displayText = [];
    global $proviences;
    $cases = [
      'Search Keyword' => 'searchKey',
      'Search IN' => ['CUID', 'Subject', 'Body', 'SenderEmail'],
      'From' => ['FromDateM', 'FromDateD', 'FromDateY'],
      'To' => ['ToDateM', 'ToDateD', 'ToDateY'],
      'Status' => 'pTypes',
      'Hide Marked Read' => 'HideMarkedRead',
      'Flag' => 'Flag',
      'Panelists' => 'Panelists',
      'State' => 'StateProvince',
      'Country' => 'SelectionCountry',
      'Owner' => [
        '0' => 'Non-Business Owner',
        '1' => 'Business Owner',
        '-1' => 'Both'
      ],
      'Partition' => 'searchPartition'
    ];
    $panelist_core_options = array('C'=>'C','EN'=>'EN','N'=>'N','RL'=>'RL','TC'=>'TC','TL'=>'TL');
    if($recordSet['cType']==1) {
      $panelist_core_options = array('ID'=>'ID','PT'=>'PT','PN'=>'PN');
    }
    $panelistTypes = $statusTypeUsed = [];
    if($recordSet['Flag'] != ''){
      $Flags = explode(',', $recordSet['Flag']);
      if(count($Flags)){
        foreach ($Flags as $flag) {
            $panelistTypes[] = $panelist_core_options[$flag];
        }
      }
    }
    global $messageTypes;
    $messageTypes[0] = 'Blank';
    $status = explode(';', $recordSet['pTypes']);
    if(count($status)){
      foreach ($status as $type) {
          $statusTypeUsed[] = $messageTypes[$type];
      }
    }
    foreach ($cases as $caseKey => $caseValue) {
      if(is_array($caseValue)){
        $texts = [];
        if($caseKey == 'Owner'){
          $displayText[] = '<strong>'.$caseKey.': </strong>'.$caseValue[$recordSet[$caseKey]];
        }else if($caseKey == 'From'){
          foreach($caseValue as $key =>$value){
            if(isset($recordSet[$value]) && $recordSet[$value]){
                $texts[] = $recordSet[$value];
            }
          }
          if(implode('/', $texts) != '00/00/0000'){
            $displayText[] = '<strong>'.$caseKey.': </strong>'.implode('/', $texts);
          }
        }else if($caseKey == 'To'){
          foreach($caseValue as $key =>$value){
            if(isset($recordSet[$value]) && $recordSet[$value]){
                $texts[] = $recordSet[$value];
            }
          }
          if(implode('/', $texts) != '00/00/0000'){
            $displayText[] = '<strong>'.$caseKey.': </strong>'.implode('/', $texts);
          }
        }else{
          foreach($caseValue as $key =>$value){
            if(isset($recordSet[$value]) && $recordSet[$value]){
                $texts[] = $value;
            }
          }
					if(count($texts)){
	          $displayText[] = '<strong>'.$caseKey.': </strong>'.implode(', ', $texts);
					}
        }

      }else{
        // echo '----';
        // echo $caseValue;
        // echo '----<br />';
        switch($caseValue){
          case 'Flag': $displayText[] = '<strong>'.$caseKey.': </strong>'.implode(', ', $panelistTypes);break;
          case 'pTypes':$displayText[] = '<strong>'.$caseKey.': </strong>'.implode(', ', $statusTypeUsed);break;
          case 'HideMarkedRead':$displayText[] = '<strong>'.$caseKey.':</strong>'.($recordSet[$caseValue]?'True':'False');break;
          case 'StateProvince':
            if(isset($recordSet[$caseValue]) && $recordSet[$caseValue] > 0){
              $displayText[] = '<strong>'.$caseKey.': </strong>'.$proviences[$recordSet[$caseValue]];
            }
            break;
          default:
            if($recordSet[$caseValue] != ''){
              $displayText[] = '<strong>'.$caseKey.': </strong>'.$recordSet[$caseValue];
            }
        }

      }
    }
    return implode(' &nbsp; ', $displayText);
    //htmlspecialchars
  }
	$contentTypes = array_values($contactType);
	while($rs = $DRW->fetch_array($result)) {
		if($className=='selected-bg1') $className = 'white-bg';
		else $className = 'selected-bg1';

?>
    <tr class = "<?php echo $className; ?>">
      <td valign="top" class="bodytext">
          <input class="input_box" type="text" name="<?php echo 'name'.$rs['ID']; ?>" size = "20" value= "<?php echo htmlspecialchars($rs['searchName'], ENT_QUOTES);?>" />

      </td>
			<td valign="top" class="bodytext"><input type="checkbox" name="emailAlert<?php echo $rs['ID']; ?>" <?php echo $rs['emailAlert']?'checked':''; ?> /></td>
      <td valign="top" class="bodytext"><?php echo $contentTypes[$rs['cType']]; ?></td>
			<td valign="top" class="bodytext"><?php echo $rs['createdAt_']; ?></td>
      <td class="bodytext" style="margin-bottom:8px;" valign="top">
      <?php
      	echo getDisplayTextForImap($rs);
      ?>
      </td>
      <td class="bodytext" valign="top" align="center" style="margin-bottom:8px;">
      <table border="0" cellpadding="0" cellspacing="4" class="bodytext" style="margin-top: -4px;">
        <tr><td><a href="<?php echo $LinkToPage; ?>?ssid=<?php echo $rs['ID'];?>"><img src="images/edit.png" border="0" /></a></td><td><a class="HyperLink" href ="<?php echo $LinkToPage ?>?ssid=<?php echo $rs['ID'];?>&search=1&ctype=<?php echo $rs['cType'];?>">Edit</a></td></tr>
        <tr><td><a href="<?php echo $LinkToPage; ?>?ssid=<?php echo $rs['ID'];?>"><img src="images/yes.gif" border="0" /></a></td><td><a class="HyperLink" href="<?php echo $LinkToPage ?>?rid=<?php echo $rs['ID'];?>&page=0&ctype=<?php echo $rs['cType'];?>">Run</a></td></tr>
        <tr><td><a href="<?php echo $_SERVER['PHP_SELF'];?>?delID=<?php echo $rs['ID'];?>" onclick="return delConfirm();"><img src = "images/drop.png" border="0" /></a></td><td><a class="HyperLink" href="<?php echo $_SERVER['PHP_SELF'];?>?delID=<?php echo $rs['ID'];?>" onclick="return delConfirm();">Delete</a></td></tr>
       </table>
      </td>
      <td style="text-align: center;">
	<input type="checkbox" class="del_checkboxes" id="<?php echo $rs['ID']; ?>" />
      </td>
    </tr>
<?php
	}
	?>
	<tr>
      <td colspan="6">
	<input type="submit" name="submit" value="Update" style="cursor:pointer;" class="submitbutton" />
	<input type="button" name="delete_selected" value="Delete Selected" class="submitbutton" style="cursor:pointer;" onClick="return delConfirmAll();" />
	<input type="hidden" name="send" value="1" />
      </td>
    </tr>
	<?php
	if($num_of_rows > $pagelimit){
		echo '<tr><td colspan="6" align="center"><strong>';
		$a->previousNext();
		echo '</strong></td></tr>';
	}
}
else{
?>
  <tr><td class="error" align="center">No Saved Searches Found</td></tr>
<?php
}
?>
</table>
</form>
<?php
require_once('panelist_bottom.php');
