<?php
$ALLOW_GROUPS = array(83);
require_once("../auth_auth.php");
include 'top.php';
require_once '../includes/functions.php';
require_once 'TemplateMailer.php';


$page_heading = 'ADD Communication Type';
$page_message = 'Please fill following details to add new user';
$comID = '';
$type = '';
$mpAllow = array();
$sectorAllow = array();
$msg = '';
$javascript = '';

if(isset($_REQUEST['id'])) {
	$comID = $_REQUEST['id'];
	if( $comID != '' ) {
		$page_heading = 'UPDATE Communication Type';
		$page_message = 'Please fill following details to update this communication type';
		
		// fetch existing product information
		$sql = "SELECT type FROM cscan_agent_communication WHERE ID='$comID'";
		$result = $DRW->query( $sql,$DRW_read );
		
		if( $DRW->num_rows( $result ) > 0 ) {
			$row = $DRW->fetch_array($result);
			$type = $row['type'];
		}
	}
}

if(isset($_POST['save'])) {
	$type = $_POST['type'];
	
	if(isset($_POST['mp_allow'])){
		$mpAllow = $_POST['mp_allow'];
	}
	$sql = "SELECT sectorID FROM cscan_sector";
	$rs = $DRW->query($sql,$DRW_read);
	while($row = $DRW->fetch_array($rs)) {
		if(isset($_POST['sectorid_'.$row[0]]) && $_POST['sectorid_'.$row[0]]==1){
			$sectorAllow[] = $row[0];
		}
	}

	
		
	if($comID!='')  $sql = "SELECT type FROM cscan_agent_communication WHERE type='".$DRW->real_escape_string($type)."' AND ID<>'$comID'";
	else $sql = "SELECT ID FROM cscan_agent_communication WHERE type='".$DRW->real_escape_string($type)."'";  
	
	$result = $DRW->query($sql,$DRW_read);

	if( $DRW->num_rows($result) == 0 ) {
		if($comID == '') {
			$insert_sql = "INSERT INTO cscan_agent_communication (type)
				VALUES ('".$DRW->real_escape_string($type)."')"; 
			$DRW->query($insert_sql,$DRW_main);
			$comID = $DRW->insert_id($DRW_main);
		}
		else {
			$update_sql = "UPDATE cscan_agent_communication SET type='".$DRW->real_escape_string($type)."'
				WHERE ID='$comID'";  
			$DRW->query($update_sql,$DRW_main);
		}
		
		$communicationArray = array($comID);
		foreach($communicationArray as $u){
			$sql = "DELETE FROM cscan_communication_audience WHERE communicationID=$u";
			$DRW->query($sql,$DRW_main);
			if(isset($mpAllow) && !empty($mpAllow)){
				foreach($mpAllow as $m){
					$sql = "INSERT INTO cscan_communication_audience (communicationID,audienceID) VALUES ($u,$m)";
					$DRW->query($sql,$DRW_main);
				}
			}
			
			$sql = "DELETE FROM cscan_communication_sector WHERE communicationID=$u";
			$DRW->query($sql,$DRW_main);
			if(isset($sectorAllow) && !empty($sectorAllow)){
				foreach($sectorAllow as $s){
					$sql = "INSERT INTO cscan_communication_sector (communicationID,sectorID) VALUES ($u,$s)";
					$DRW->query($sql,$DRW_main);
				}
			}
		}
		ob_end_clean();
		header("Location: communication-type.php");
		exit; 
	}
	if(isset($_POST['comID'])){
		$msg = "Communication type already exists";
	}
	else {
		$msg = "Communication type already exists";
	}
}
?>
<script type="text/javascript" src="jquery.min.js"></script>
<table width="100%" border="0" cellspacing="0" cellpadding="5" class="text">
  <tr><td class="adminhead" align="center"><?php echo $page_heading; ?></td></tr>
  <tr><td align="right"><strong><span class="error">* required field</span></strong></td></tr>
   
    <tr>
      <td align="center">
		<form method="post" name="communicationForm" onsubmit="return validate();" action="<?php echo isset($_REQUEST['id']) ? $_SERVER['PHP_SELF'].'?id='.$_REQUEST['id'] : $_SERVER['PHP_SELF']; ?>">
        <table border="0" cellpadding="5" cellspacing="0">
        <tr><td class="error" align="center" colspan="2">
		<?php echo $msg; ?>
		</td></tr>
		    <!-- Email Address -->
          <tr>
            <td class="bodytext" align="right">Communication Type:<span class="error">*</span></td>
            <td><input type="text" name="type" size="40" maxlength="255" class="input_box" value="<?php echo htmlspecialchars($type,ENT_QUOTES);?>" /></td>
          </tr>

		  <tr>
			<td>&nbsp;</td>
			<td class="bodytext">
				<fieldset style="border-color:#000000;border-width:1px;">
				<legend><strong>Audience</strong></legend>
				<div>
				<?php 
				if($comID!=''){
					$sql = "SELECT audienceID FROM cscan_communication_audience WHERE communicationID=$comID";
					$rs = $DRW->query($sql,$DRW_read);
					while($row = $DRW->fetch_array($rs)) {
						$mpAllow[] = $row[0];
					}
				}
				$mailing_panel = getMailingPanel();
				foreach( $mailing_panel as $id=>$name ) {
					echo "<div><label><input type=\"checkbox\" name=\"mp_allow[]\" value=\"$id\"";
					if(in_array($id,$mpAllow)){
						echo ' checked="checked"';	
					}
					echo " />".htmlspecialchars($name).'</label></div>';
				}
				?>
				</div>
				</fieldset>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td class="bodytext">
				<fieldset style="border-color:#000000;border-width:1px;">
				<legend><strong>Sector/Category/Sub Category</strong></legend>
				<div style="height:300px;overflow-y:scroll;">
				<?php 
				if($comID!=''){
					$sql = "SELECT sectorID FROM cscan_communication_sector WHERE communicationID=$comID";
					$rs = $DRW->query($sql,$DRW_read);
					while($row = $DRW->fetch_array($rs)) {
						$sectorAllow[] = $row[0];
					}
				}
				getSects();
				?>
				</div>
				</fieldset>
			</td>
		</tr>
	</tr>
          <!-- Button -->
		<?php
		if( $comID == '' ) {
		?>
		<tr><td colspan="2">&nbsp;</td></tr>
          <tr>
			<td>&nbsp;</td><td><input class="button" type="submit" name="saveb" value="Save" onClick="return validate();" disabled="disabled"/> 
			</td>
          </tr>
		<?php
		}else{
		?>
          <tr>
			<td>&nbsp;</td><td><input class="button" type="submit" name="saveb" value="Update" onclick="return validate();" disabled="disabled"/> &nbsp; <input class="button" type="button" value="Cancel" onclick="location.href='communication-type.php';return false;" /><input type="hidden" name="comID" value="<?php echo $comID;?>" /></td>
          </tr>
		<?php
		}
		?>
        </table>
        <input type="hidden" name="save" value="1" />
       </form>
        
      </td>
    </tr>
</table>
<style>
.loader{
    background: rgba(255,255,255,0.9) url(../images/loader.gif) no-repeat center 50% ;  
    opacity: 0.9;
    z-index: 1000001;
    width:100%; 
    height:100%; 
    position: fixed; 
    top:0; 
    left:0;
}
</style>
<script type="text/javascript">
<!--
function validate(){
	var type = document.communicationForm.type.value = trimspace(document.communicationForm.type.value);
	
	if( type == '' ){
		alert("Please enter communication type");
		document.communicationForm.type.focus();
		return false;
	}
	/*var chexm = false;
	for(var j=0;j<document.communicationForm['mp_allow[]'].length;j++){
		if(document.communicationForm['mp_allow[]'][j].checked){
			chexm = true;
			break;
		}
	}
	if(!chexm){
		alert("Please enter Audience");
		return false;
	}
	var chex = false;
	for(var k in pidArray){
		if(document.communicationForm['sectorid_'+k].checked){
			chex = true;
			break;
		}
	}
	if(!chex){
		alert("Please enter Area of Interest");
		return false;
	}*/
	
	return true;
}
var pidArray = new Array();
var cidArray = new Array();
<?php echo $javascript; ?>
function checkParent(sid,pid){
	if(pid!=0){
		var obj1 = document.communicationForm['sectorid_'+sid];
		var obj2 = document.communicationForm['sectorid_'+pid];
		
		if(obj1.checked && !obj2.checked){
			obj2.checked = true;
		}
		checkParent(pid,pidArray[pid]);
	}
}
function checkChildren(sid,chex){
	if(cidArray[sid]){
		for(var i in cidArray[sid]){
			var obj = document.communicationForm['sectorid_'+cidArray[sid][i]];
			if(obj){
				obj.checked = chex;
				checkChildren(cidArray[sid][i],chex);
			}
		}
	}
}
function checkP_C(sid,pid){
	checkParent(sid,pid);
	var obj = document.communicationForm['sectorid_'+sid];
	if(cidArray[sid]){
		if(obj.checked){
			if(confirm('Select All?')){
				checkChildren(sid,true);
			}
		}
		else if(confirm('Remove All?')){
			checkChildren(sid,false);
		}
	}
}

//-->
</script>
<?php
function getSects($parentID=0,$level=0){
	global $DRW,$DRW_read,$DRW_main,$DRW_crm;
	if($level<3){
		$sql = "SELECT sectorID,sectorName,sectorSearchActive FROM cscan_sector WHERE parentID=$parentID ORDER BY sectorName";
		$rs = $DRW->query($sql,$DRW_read);
		$resultCount = $DRW->num_rows($rs);
		if($resultCount>0){
			$GLOBALS['javascript'] .= "cidArray[$parentID] = new Array();\n";
			while($row = $DRW->fetch_array($rs)) {
				$ID = $row['sectorID'];
				$sectorName = $row['sectorName'];
				$sectorSearchActive = $row['sectorSearchActive'];
				echo "<div>";
				for($i=0;$i<$level;$i++){
					echo ' &nbsp; &nbsp; ';
				}
				echo "<label><input type=\"checkbox\" name=\"sectorid_$ID\" value=\"1\" onclick=\"checkP_C($ID,$parentID);\"";
				if(in_array($ID,$GLOBALS['sectorAllow'])){
					echo ' checked="checked"';	
				}
				echo " />".htmlspecialchars($sectorName).'</label>';
				if(!$sectorSearchActive) echo ' <em>[non-search]</em>';
				echo '</div>';
				$GLOBALS['javascript'] .= "pidArray[$ID] = $parentID;\n";
				$GLOBALS['javascript'] .= "cidArray[$parentID][cidArray[$parentID].length] = $ID;\n";
				getSects($ID,$level+1);
			}
		}
	}
}
include 'bottom.php';
?>
