<?php 
$ALLOW_GROUPS = array(6,35,36);
require_once("../auth_auth.php");
require_once '../includes/functions.php';
require_once '../includes/thumb.php';
$ONLOAD = 'addCategoryOnload();';
$JQUERY = true;
include 'top.php';

if(isset($_GET['id'])) 
	$updID = (int)$_GET['id'];
else 
	$updID = 0;

$fieldvalue ='';
$printID = 0;
$AffinityCategoryID = array();
$audienceID = 0;
$p_stateID = 0;
$p_primary_country = '';
$error_msg = '';
$isWorksiteVoluntary = 0;
$isCreditUnion = 0;
$isInsuranceExchange = 0;
$isMilitaryCo = 0;
$isRetailMarketer = 0;
$isApprovedCo = 0;
$co_states = '';
$comboIDs = '';
$addCategoryOnload = '';
$parentCompanyFieldText = '';
$parentCompanyIDFieldValue = '';

define('COMPANY', 	0);
define('PUBLICATION', 1);
define('AFFINITY', 	2);

function getCompanyNameByID($id) {
	global $DRW,$DRW_read,$DRW_main,$DRW_crm;
	$sql = "SELECT * FROM cscan_company WHERE companyID = '$id'";
	$result = $DRW->query($sql,$DRW_read);
	$row = $DRW->fetch_array($result);

	return $row;
}

if(isset($_GET['type'])) {
	$title = "Company";
	$category = "company";
	$category_relation = "company";
	$type = intval($_GET['type']);
	if($type == PUBLICATION && checkGroup(36)) { 
		$title = "Publication";
		$category = "publication";
		$category_relation = "publication";
	}
	elseif($type == AFFINITY && checkGroup(35)) {
		$title = "Affinity";
		$category = "affinity";
		$category_relation = "affinityAssociationVal";
	} 
	else {
		$type	= COMPANY;
	}
} 
else {
	$title = "Company";
	$type	= COMPANY;
	$category = "company";
	$category_relation = "company";
}

if(isset($_POST['send'])){
	$fieldname = trim($_POST['fieldname']);
	if(isset($_POST['fieldname_old'])){
		$fieldname_old = trim($_POST['fieldname_old']);
	}
	else{
		$fieldname_old = '';
	}
	$fieldvalue = $DRW->real_escape_string($fieldname);
	$changeProducts = array();

	// parent company name
	$parentCompanyID = (!empty($_POST['parentCompanyID'])) ? $_POST['parentCompanyID'] : 0;

	if(isset($_POST['isWorksiteVoluntary'])) 
		$isWorksiteVoluntary = (int)$_POST['isWorksiteVoluntary'];
		
	if(isset($_POST['isCreditUnion'])) 
		$isCreditUnion = (int)$_POST['isCreditUnion'];
		
	if(isset($_POST['isInsuranceExchange'])) 
		$isInsuranceExchange = (int)$_POST['isInsuranceExchange'];
		
	if(isset($_POST['isMilitaryCo'])) 
		$isMilitaryCo = (int)$_POST['isMilitaryCo'];
		
	if(isset($_POST['isRetailMarketer'])) 
		$isRetailMarketer = (int)$_POST['isRetailMarketer'];
		
	if(isset($_POST['isApprovedCo'])) 
		$isApprovedCo = (int)$_POST['isApprovedCo'];
		
	if(isset($_POST['co_states'])) 
		$co_states = implode(',',$_POST['co_states']);
		
	if(isset($_POST['scsc_comboIDs'])) 
		$comboIDs = $_POST['scsc_comboIDs'];
	
	$chk = "SELECT {$category}ID FROM cscan_$category WHERE {$category}Name='$fieldvalue' AND {$category}ID<>$updID";	
	$rs = $DRW->query($chk,$DRW_read);
	
	if($DRW->num_rows($rs) == 0) { //check any publications with the same name entered, if none go here
		if($updID == 0) {		
		 	switch($type) {
		 		case COMPANY:
					$sql = "INSERT INTO cscan_company SET parentCompanyID='$parentCompanyID',companyName='$fieldvalue',isWorksiteVoluntary=$isWorksiteVoluntary,isCreditUnion=$isCreditUnion,isInsuranceExchange=$isInsuranceExchange,isMilitaryCo=$isMilitaryCo,isRetailMarketer=$isRetailMarketer,isApprovedCo=$isApprovedCo,co_states='$co_states',comboIDs='".$DRW->real_escape_string($comboIDs)."'";
					break;
		 		case PUBLICATION:
		 			$audience_type = $DRW->real_escape_string($_POST['audience_type']);
		 			$print_type = $DRW->real_escape_string($_POST['print_type']);
		 			$p_stateID = $DRW->real_escape_string($_POST['p_stateID']);
		 			$p_primary_country = $DRW->real_escape_string($_POST['p_primary_country']);
					$sql = "INSERT INTO cscan_publication (publicationName, audience_id, print_typeID, p_stateID, p_primary_country)
								VALUES('$fieldvalue', '$audience_type', '$print_type', '$p_stateID', '$p_primary_country')";		 
		 			break;
				case AFFINITY:
					$sql = "INSERT INTO cscan_affinity (affinityName)
								VALUES('$fieldvalue')";	
					break;
		 		default:
				 	$sql = "INSERT INTO cscan_{$category} ({$category}Name) VALUES ('$fieldvalue')";		 			
		 			break;
		 	}
		} else {
			switch($type) {
				case COMPANY:
					$sql = "UPDATE cscan_company SET parentCompanyID='$parentCompanyID',companyName='$fieldvalue',isWorksiteVoluntary=$isWorksiteVoluntary,isCreditUnion=$isCreditUnion,isInsuranceExchange=$isInsuranceExchange,isMilitaryCo=$isMilitaryCo,isRetailMarketer=$isRetailMarketer,isApprovedCo=$isApprovedCo,co_states='$co_states',comboIDs='".$DRW->real_escape_string($comboIDs)."' WHERE companyID=$updID";
					if($fieldname_old!=$fieldname){
						$sql2 = "SELECT pd.productID FROM cscan_product_detail pd, cscan_company_product cp WHERE companyID=$updID AND pd.productID=cp.productID";
						$rs2 = $DRW->query($sql2,$DRW_read);
						while($row2 = $DRW->fetch_array($rs2)) {
							$changeProducts[] = $row2[0];
						}
					}
					companySearchText($fieldname,$fieldname_old);
					break;
				case PUBLICATION:
		 			$audience_type = $DRW->real_escape_string($_POST['audience_type']);
		 			$print_type = $DRW->real_escape_string($_POST['print_type']);
		 			$p_stateID = $DRW->real_escape_string($_POST['p_stateID']);
		 			$p_primary_country = $DRW->real_escape_string($_POST['p_primary_country']);
		 			$sql = "UPDATE cscan_publication SET 
		 						publicationName = '$fieldvalue',
		 						audience_id = '$audience_type',
		 						print_typeID = '$print_type',
		 						p_stateID = '$p_stateID',
		 						p_primary_country = '$p_primary_country'
		 					WHERE
		 						publicationID = '$updID'
		 					LIMIT 1";
		 																
					break;
				case AFFINITY:
		 			$sql = "UPDATE cscan_affinity SET 
		 						affinityName = '$fieldvalue'
		 					WHERE
		 						affinityID = '$updID'
		 					LIMIT 1";
					break;
				default:
					$sql = "UPDATE cscan_{$category} SET {$category}Name='$fieldvalue' WHERE {$category}ID=$updID";
					break;
			}
		}
		$DRW->query($sql,$DRW_main);
		
		if($type==COMPANY){
			if($updID == 0) {
				$updID = $DRW->insert_id($DRW_main);
			}
			companyText($changeProducts,$updID,$fieldname);
			$root = dirname(__FILE__);
			if(strpos($root,'/admin')!==false){
				$root = substr($root,0,strpos($root,'/admin'));
			}
			if(isset($_FILES['imgFile']['tmp_name']) && !empty($_FILES['imgFile']['tmp_name'])){				
				$ifile = $root.'/tmp_upload/'.$_FILES['imgFile']['name'];
				$img_co_content_type = $_FILES['imgFile']['type'];
				if(strtolower($img_co_content_type)=='image/jpeg' || strtolower($img_co_content_type)=='image/jpg' || strtolower($img_co_content_type)=='image/png'){

					if(move_uploaded_file($_FILES['imgFile']['tmp_name'], $ifile)) {						
						$img_co_size_byte = $_FILES['imgFile']['size'];
						$complete = createthumb($ifile,$ifile,150,100);
						saveCompanyImgDB($updID,$ifile,$img_co_content_type,$img_co_size_byte);
						unlink($ifile);
					}
				}
			}
			if(isset($_REQUEST['changeall'])){
				$companyID = $updID;
				$query2 = "SELECT COUNT(*) FROM cscan_img_company WHERE companyID=$companyID";
				$query_result2 = $DRW->query($query2,$DRW_read);
				$data2 = $DRW->fetch_row($query_result2);
				if($data2[0]>0){
					$query_p = "SELECT pd.productID FROM cscan_product_detail pd,cscan_company_product cp WHERE pd.productID=cp.productID AND primary_co=1 AND companyID=$updID";
					$query_result_p = $DRW->query($query_p,$DRW_read);
					while($data_p = $DRW->fetch_row($query_result_p)){
						$productID = $data_p[0];
						
						saveImageData($productID,'','','',$companyID);
					}
				}
			}
		}
		elseif($type==AFFINITY){
			if($updID == 0) {
				$updID = $DRW->insert_id($DRW_main);
			}
			if(isset($_POST['AffinityCategoryID'])){
				$AffinityCategoryID = $_POST['AffinityCategoryID'];
			}
			else{
				$AffinityCategoryID = array();
			}

			if (isset($_POST['AffinitySubCategoryID'])) {
				$AffinityCategoryID = array_merge($AffinityCategoryID, $_POST['AffinitySubCategoryID']);
			}
			$sql = "DELETE FROM cscan_aff_cat WHERE affinityID=".$updID;			
			$DRW->query($sql,$DRW_main);
			foreach($AffinityCategoryID as $aid){
				$sql = "REPLACE INTO cscan_aff_cat (affinityID,AffinityCategoryID) VALUES ($updID,$aid)";			
				$DRW->query($sql,$DRW_main);
			}
		}
		ob_end_clean();
		if(isset($_POST['submit']) && $_POST['submit'] == 'Save & Add More')
			header("Location: addCategory.php?a=1&type=$type");
		else
			header("Location: manageCategory.php?type=$type");
		exit;
		
	} else {
		$row = $DRW->fetch_row($rs);
		$newID = $row[0];
		if($updID!=0){
			$numquery2 = "SELECT COUNT(*) as numrows FROM cscan_{$category}_product WHERE {$category}ID=$updID";
			$numquery2 = $DRW->query($numquery2,$DRW_read);
			$row2 = $DRW->fetch_row($numquery2);
			$numrows2 = $row2[0];
			
			if($numrows2>0){
				if($type == COMPANY){
					if($fieldname_old!=$fieldname){
						$sql2 = "SELECT pd.productID FROM cscan_product_detail pd, cscan_company_product cp WHERE companyID=$updID AND pd.productID=cp.productID";
						$rs2 = $DRW->query($sql2,$DRW_read);
						while($row2 = $DRW->fetch_array($rs2)) {
							$changeProducts[] = $row2[0];
						}
					}
					companySearchText($fieldname,$fieldname_old);
				}
				$sql = "UPDATE IGNORE cscan_{$category}_product SET {$category}ID=$newID WHERE {$category}ID=$updID";
				$DRW->query($sql,$DRW_main);
			
				$sql = "DELETE FROM cscan_{$category} WHERE {$category}ID=$updID";			
				$DRW->query($sql,$DRW_main);
				
				if($type == COMPANY){
					companyText($changeProducts,$updID,$fieldname);
				}
				
				ob_end_clean();
				header("Location: manageCategory.php?type=$type");
				exit;
			}
		}
		$error_msg = $title.' Name already exists';
	}	
}
elseif($updID!=0) {
	if($type == PUBLICATION) {
		$sql_sel = "publicationID,publicationName,audience_id,print_typeID,p_stateID,p_primary_country";
	}
	elseif($type == AFFINITY) {
		$sql_sel = "affinityID,affinityName,AffinityCategoryID";
	} 
	elseif($type == COMPANY) {
		$sql_sel = "companyID,companyName,isWorksiteVoluntary,isMilitaryCo,isApprovedCo,co_states,comboIDs,parentCompanyID,isRetailMarketer,isCreditUnion,isInsuranceExchange";
	}
	else{
		$sql_sel = '*';
	}
	$sql = "SELECT $sql_sel FROM cscan_{$category} WHERE {$category}ID=$updID";
	$editRS = $DRW->query($sql,$DRW_read);
	 while($row = $DRW->fetch_row($editRS))
	 {		 	
		switch($type) {
			case COMPANY:
				$fieldvalue = $row[1];
				$parentCompany = getCompanyNameByID($row[7]);
				$parentCompanyFieldText = $parentCompany['companyName'];
				$parentCompanyIDFieldValue = $row[7];
				$isWorksiteVoluntary = $row[2];
				$isCreditUnion = $row[9];
				$isInsuranceExchange = $row[10];
				$isMilitaryCo = $row[3];
				$isRetailMarketer = $row[8];
				$isApprovedCo = $row[4];
				$co_states = $row[5];
				$comboIDs = $row[6];
				$comboIDs_split = explode('|',$comboIDs);
				foreach($comboIDs_split as $scsc_combo){
					if(!empty($scsc_combo)){
						list($s,$c,$sc) = explode('_',$scsc_combo);
						$scsc_combo_text = sectorName($s).' / '.sectorName($c).' / '.sectorName($sc);
						$addCategoryOnload .= " displaySCSC('$scsc_combo','".singleQuoteSafe($scsc_combo_text)."'); ";
					}
				}
				break;
			case PUBLICATION:
	 			$fieldvalue = $row[1];
	 			$audienceID = $row[2];
	 			$printID = $row[3];
	 			$p_stateID = $row[4];
	 			$p_primary_country = $row[5];	
				break;
			case AFFINITY:
				$fieldvalue = $row[1];
				$z = $DRW->query("SELECT AffinityCategoryID FROM cscan_aff_cat WHERE affinityID=$updID",$DRW_read);
				while($z && $zz = $DRW->fetch_assoc($z)) {
					$AffinityCategoryID[] = $zz['AffinityCategoryID'];
				}
				break;
			default:
	 			$fieldvalue = $row[1]; 				
				break;
		}
	 }
}

function acategoryTypes($AffinityCategoryID) {
	global $DRW,$DRW_read,$DRW_main,$DRW_crm;
	if(!is_array($AffinityCategoryID)){
		$AffinityCategoryID = array($AffinityCategoryID);
	}

	$affinity_subcats = array();	
	$z = $DRW->query("SELECT AffinityCategoryID,AffinityCategoryName FROM cscan_affinity_category WHERE parentID=0 ORDER BY AffinityCategorySort",$DRW_read);
	
	echo '<select id="AffinityCategoryID" name="AffinityCategoryID[]" class="combo_box" size="4" multiple="multiple" onChange="populateSubCategory(\'AffinityCategoryID\');">';
	while($z && $zz = $DRW->fetch_assoc($z)) {
		echo '<option value="'.$zz['AffinityCategoryID'].'"'.((in_array($zz['AffinityCategoryID'],$AffinityCategoryID))?'selected':'').'>'.$zz['AffinityCategoryName'].'</option>';
	}
	echo '</select>';

	$z = $DRW->query("SELECT AffinityCategoryID,AffinityCategoryName,parentID FROM cscan_affinity_category WHERE parentID > 0 ORDER BY AffinityCategorySort",$DRW_read);
	while($z && $zz = $DRW->fetch_assoc($z)) {
		$parentID = $zz['parentID'];
		$subcat_name = $zz['AffinityCategoryName'];
		$subcat_id = $zz['AffinityCategoryID'];

		if (!array_key_exists($parentID, $affinity_subcats))
			$affinity_subcats[$parentID] = array(); //initialize an array, to hold subcategories for this parent
	
		$subcat_selected_text = (in_array($subcat_id, $AffinityCategoryID) ? "SELECTED" : '');
		$affinity_subcats[$parentID][] = array("id"=>$subcat_id, "name"=>$subcat_name, "selected"=>$subcat_selected_text);
	}
	$affinity_subcats = json_encode($affinity_subcats);
	echo "<script type='text/javascript'> var subCategories = $affinity_subcats;</script>";
}

function printTypes($printID) {
	global $DRW,$DRW_read,$DRW_main,$DRW_crm;
	$z = $DRW->query("SELECT * FROM cscan_print_type",$DRW_read);
	
	echo '<select name="print_type" class="combo_box"><option value="0">&nbsp;</option>';
	while($z && $zz = $DRW->fetch_assoc($z)) {
		echo '<option value="'.$zz['print_typeID'].'"'.(($printID==$zz['print_typeID'])?'selected':'').'>'.$zz['print_typeName'].'</option>';
	}
	echo '</select>';
}

function audienceOptions($audienceID) {
	global $DRW,$DRW_read,$DRW_main,$DRW_crm;
	$z = $DRW->query("SELECT * FROM cscan_mpanel",$DRW_read);
	
	echo '<select name="audience_type" class="combo_box"><option value="0">&nbsp;</option>';
	while($z && $zz = $DRW->fetch_assoc($z)) {
		echo '<option value="'.$zz['mPanelID'].'"'.(($audienceID==$zz['mPanelID'])?'selected':'').'>'.$zz['mPanelName'].'</option>';
	}
	echo '</select>';
}
function companySearchText($fieldname,$fieldname_old){
	global $DRW,$DRW_read,$DRW_main,$DRW_crm;
	if($fieldname_old!=$fieldname){
		$sql2 = "SELECT ID FROM cscan_search WHERE company LIKE '%\"".$DRW->real_escape_string($fieldname_old)."\"%'";
		$rs2 = $DRW->query($sql2,$DRW_read);
		while($row2 = $DRW->fetch_array($rs2)) {
			$sql = "UPDATE cscan_search SET company=REPLACE(company,'\"".$DRW->real_escape_string($fieldname_old)."\"','\"".$DRW->real_escape_string($fieldname)."\"') WHERE ID=$row2[0]";
			$DRW->query($sql,$DRW_main);
		}
	}
}
function companyText($changeProducts,$updID,$fieldname){
	global $DRW,$DRW_read,$DRW_main,$DRW_crm;
	foreach($changeProducts as $productID) {
		$sql2 = "SELECT companyName,cc.companyID FROM cscan_company_product cp,cscan_company cc WHERE cp.productID=$productID AND cc.companyID=cp.companyID and primary_co=1";
		$rs2 = $DRW->query($sql2,$DRW_read);
		$row2 = $DRW->fetch_array($rs2);
		$company = $row2[0];
		$cid = $row2[1];
		if($cid==$updID){
			$company = $fieldname;
		}
		
		$coArray2 = array();
		$sql2 = "SELECT companyName,cc.companyID FROM cscan_company_product cp,cscan_company cc WHERE cp.productID=$productID AND cc.companyID=cp.companyID and primary_co<>1 ORDER BY primary_co ASC,companyName ASC";
		$rs2 = $DRW->query($sql2,$DRW_read);
		while($row2 = $DRW->fetch_array($rs2)) {
			$companyName = $row2[0];
			$cid = $row2[1];
			if($cid==$updID){
				$companyName = $fieldname;
			}
			$coArray2[] = $companyName;
		}
		
		$insert_query = "update cscan_product_detail set company='".$DRW->real_escape_string($company)."',secondCompany='".$DRW->real_escape_string(implode('; ',$coArray2))."' where productID=$productID";	
		$DRW->query($insert_query,$DRW_main);
	}
}
?>
<script type="text/JavaScript">
<!--
function addCategoryOnload(){
	<?php echo $addCategoryOnload; ?>
	return true;
}
function validate(){
	var fieldname=document.frm1.fieldname.value=trimspace(document.frm1.fieldname.value);
	if(fieldname == '') {
		alert('Please enter the <?php echo $category; ?> name.');
		document.frm1.fieldname.focus();
		return false;
	}
	return true;
}
function validate_CatSubcat(){    
    var fieldname=document.frm1.fieldname.value=trimspace(document.frm1.fieldname.value);
    var catname=trimspace(document.frm1.AffinityCategoryID.value);   
    if(fieldname == '') {
            alert('Please enter the <?php echo $category; ?> name.');
            document.frm1.fieldname.focus();
            return false;
    }else if(catname == '') {
        alert('Please select affinity category.');
        document.frm1.AffinityCategoryID.focus();
        return false;
    }else if(document.getElementById("hiddenSubCatRow").style.display!='none'){
      var subcatname=trimspace(document.frm1.SubCategorySelect.value);
      if(subcatname==''){
          alert('Please select affinity sub-category.');
            document.frm1.SubCategorySelect.focus();
            return false;
      }else{
       return true;
      }
     
    }else{
    return true;
    }
}
function do_SCSC(obj,type,obj_to){
	var tid = 0;
	for(var j=0;j<obj.options.length;j++){
		if(obj.options[j].selected){
			tid = obj.options[j].value;
			break;
		}
	}
	obj_to.selectedIndex = 0;
	obj_to.options.length = 1;
	obj_to.style.display = 'none';
	processajax('scsc_info.php', true, 'POST', type+'='+tid, obj_to, 'doInnerSelect');
}
function doInnerSelect(response, obj){
	if(response.length>0){
		//obj.innerHTML = response;
		var opt = 1;
		var lines = response.split("\n");
		for(var i=0;i<lines.length;i++){
			var line = lines[i].split("\t");
			if(line.length==2){
				obj.options[opt] = new Option(line[1], line[0], false, false);
				opt = opt + 1;
			}
		}
		obj.style.display = 'block';
	}
}
function add_SCSC(){
	var scsc_names = new Array('sectorID','categoryID','subCategoryID');
	var scsc_values = new Array('0','0','0');
	var scsc_text = new Array('','','');
	for(var j=0;j<scsc_names.length;j++){
		var obj = document.frm1[scsc_names[j]];
		for(var k=0;k<obj.options.length;k++){
			if(obj.options[k].selected && obj.options[k].value.length>0){
				scsc_values[j] = obj.options[k].value;
				scsc_text[j] = obj.options[k].text;
				break;
			}
		}
		obj.selectedIndex = 0;
		if(scsc_names[j]!='sectorID'){
			obj.options.length = 1;
			obj.style.display = 'none';
		}
	}
	var scsc_combo = '';
	var scsc_combo_text = '';
	for(var j=0;j<scsc_values.length;j++){
		if(scsc_combo.length>0){
			scsc_combo = scsc_combo + '_';
			scsc_combo_text = scsc_combo_text + ' / ';
		}
		scsc_combo = scsc_combo + scsc_values[j];
		scsc_combo_text = scsc_combo_text + scsc_text[j];
	}
	if(scsc_combo!='0_0_0'){
		var exists = 0;
		var scsc_comboIDs_val = document.frm1.scsc_comboIDs.value;
		var valArray = scsc_comboIDs_val.split('|');
		for(var i=0;i<valArray.length;i++){
			if(valArray[i]==scsc_combo){
				exists = 1;
			}
		}
		if(!exists){
			if(scsc_comboIDs_val.length>0){
				scsc_comboIDs_val = scsc_comboIDs_val + '|';
			}
			scsc_comboIDs_val = scsc_comboIDs_val + scsc_combo;
			document.frm1.scsc_comboIDs.value = scsc_comboIDs_val;
			displaySCSC(scsc_combo,scsc_combo_text);
		}
	}
}
function displaySCSC(scsc_combo,scsc_combo_text){
	var newobj = document.getElementById('scsc_combos');
	if(newobj){
		var newnode = document.createElement('div');
		newnode.id = 'combo'+scsc_combo;
		newnode.style.fontWeight = 'bold';
		newnode.style.marginBottom = '2px';
		newnode.appendChild(document.createTextNode(scsc_combo_text+' '));
		var newnode2 = document.createElement('a');
		newnode2.href = '#';
		newnode2.onclick = new Function("sortSCSC('"+scsc_combo+"',-1); return false;");
		newnode2.appendChild(document.createTextNode('Up'));
		newnode.appendChild(newnode2);
		newnode.appendChild(document.createTextNode(' '));
		newnode2 = document.createElement('a');
		newnode2.href = '#';
		newnode2.onclick = new Function("sortSCSC('"+scsc_combo+"',1); return false;");
		newnode2.appendChild(document.createTextNode('Down'));
		newnode.appendChild(newnode2);
		newnode.appendChild(document.createTextNode(' '));
		newnode2 = document.createElement('a');
		newnode2.href = '#';
		newnode2.onclick = new Function("removeSCSC('"+scsc_combo+"'); return false;");
		newnode2.appendChild(document.createTextNode('Remove'));
		newnode.appendChild(newnode2);
		newobj.appendChild(newnode);
	}
}
function removeSCSC(scsc_combo){
	var obj = document.getElementById('combo'+scsc_combo);
	if(obj){
		obj.parentNode.removeChild(obj);
		var scsc_comboIDs_val = document.frm1.scsc_comboIDs.value;
		var scsc_comboIDs_val_new = '';
		var valArray = scsc_comboIDs_val.split('|');
		for(var i=0;i<valArray.length;i++){
			if(valArray[i]!=scsc_combo){
				if(scsc_comboIDs_val_new.length>0){
					scsc_comboIDs_val_new = scsc_comboIDs_val_new + '|';
				}
				scsc_comboIDs_val_new = scsc_comboIDs_val_new + valArray[i];
			}
		}
		document.frm1.scsc_comboIDs.value = scsc_comboIDs_val_new;
	}
}
function sortSCSC(idval,sort){
	var newval = '';
	var obj = document.getElementById('scsc_combos');
	var obj2 = document.frm1.scsc_comboIDs;
	var idsval = obj2.value;
	var valArray = idsval.split('|');
	for(var i=0;i<valArray.length;i++){
		if(valArray[i]==idval){
			if(valArray[i+sort]){
				var tmpidval = valArray[i];
				valArray[i] = valArray[i+sort];
				valArray[i+sort] = tmpidval;
				var movenode = obj.removeChild(obj.childNodes[i]);
				if(i+sort>=obj.childNodes.length){
					obj.appendChild(movenode);
				}
				else{
					obj.insertBefore(movenode,obj.childNodes[i+sort]);
				}
				break;
			}
		}
	}
	for(var i=0;i<valArray.length;i++){
		if(newval.length>0){
			newval = newval + '|';
		}
		newval = newval + valArray[i];
	}
	obj2.value = newval;
}
//-->

function suggestCompany() {
	var company = $("#parentCompany").val();
	
	$.ajax({
	  url: 'companies_ajax.php?company='+company,
	  success: function(data) {
		if (data != "") {
			$('#parentCompanySelection').html(data);
		} else {
			$('#parentCompanySelection').html("");
			$("#parentCompanyID").val("");
		}
	  }
	});
}

function autoFillParentCompany(selectedCompany) {
	var parentCompanyID = $(selectedCompany).attr("id");
	$("#parentCompanyID").val(parentCompanyID);
	$("#parentCompany").val($(selectedCompany).text());
	$('#parentCompanySelection').html("");
	//alert($("#parentCompanyID").val());
}

function populateSubCategory(parentSelectID) {
	$('#hiddenSubCatRow').hide();
	$('#SubCategorySelect').html("<option></option>");

	var selectedParents = $('#'+parentSelectID).val();
	if (selectedParents === null) { //prob won't ever come back null..
		return false;
	}

	var validSubCats = [];
	for (var parentID in subCategories) {
		if (!subCategories.hasOwnProperty(parentID)) {
			continue; //skip bad props (IE8?)
		}
		
		for(var i=0; i < selectedParents.length; i++) {
			var this_parentID = selectedParents[i];
			if (this_parentID == parentID) { //selected has a match in our master subcat list
				validSubCats = validSubCats.concat(subCategories[parentID]);
			}
		}
	}

	if (validSubCats.length > 0) {
		$('#hiddenSubCatRow').show();
		var select_options_html = '';
		for(var i=0; i < validSubCats.length; i++) {
			select_options_html += "<option value='"+validSubCats[i]['id']+"' "+validSubCats[i]['selected']+">"+validSubCats[i]['name']+"</option>";
		}
		$('#SubCategorySelect').html(select_options_html);
	}
}
$(document).ready(function() {
	populateSubCategory('AffinityCategoryID'); //you'll have to tweak this if you port to other list types
});

</script>
<table width="100%" border="0" cellspacing="0" cellpadding="5" class="text">
  <tr><td class="adminhead" align="center" colspan="2"><?php if($updID!=0) echo 'UPDATE '; else echo 'ADD '; echo $title; ?> </td></tr>
  <tr><td>&nbsp;</td></tr>
  <tr>
    <td align="center">
   		<form method="post" name="frm1" onsubmit="return validate();" action="<?php echo $_SERVER['PHP_SELF']."?id=$updID&amp;type=$type"; ?>" enctype="multipart/form-data">
   		<table width="60%" border="0" cellspacing="0" cellpadding="0"><tr><td style="border:solid 1px #14734F;">
      <table width="100%" border="0" cellspacing="0" cellpadding="4">
  <tr>
	<td class="subhead" align="center" colspan="2">
		<?php 
			if($updID!=0) echo 'UPDATE ';
			else echo 'ADD ';
		
		echo strtoupper($title);
		?> 
	</td>
  </tr>
  <tr>
	<td align="right" class="bodytext" colspan="2">
		<strong><span class="error">* required field<br /><br /></span></strong>
	</td>
  </tr>
  <?php if($error_msg!='') echo '<tr><td align = "center" class="error" colspan="2">'.$error_msg.'</td></tr>'; ?>
  <tr>
	<td class="bodytext" align="right"><?php echo $title; ?> Name<span class="error">*</span>:</td>
	<td><input type="text" name="fieldname" size="20" class="combo_box" maxlength="255" value="<?php echo htmlspecialchars($fieldvalue,ENT_QUOTES); ?>" /><input type="hidden" name="fieldname_old" value="<?php echo htmlspecialchars($fieldvalue,ENT_QUOTES); ?>" /></td>
  </tr>
<?php if ($type == COMPANY) { ?>
  <tr>
        <td class="bodytext" valign="top" align="right">Parent Company:</td>
        <td><input type="text" id="parentCompany" autocomplete="off" name="parentCompany" onkeyup="suggestCompany()" size="20" class="combo_box" maxlength="255" value="<?php echo htmlspecialchars($parentCompanyFieldText,ENT_QUOTES); ?>" />
        <input type = "hidden" id = "parentCompanyID" name = "parentCompanyID" value="<?php echo $parentCompanyIDFieldValue; ?>">
	<div id="parentCompanySelection"></div>
	<input type="hidden"
name="fieldname_old" value="<?php echo htmlspecialchars($fieldvalue,ENT_QUOTES); ?>" /></td>
  </tr>
<?php } ?>
  <?php if($type==AFFINITY) { ?>
	  <tr>
		<td class="bodytext" align="right" valign="top"><?php echo $title; ?> Category:</td>
		<td><?php echo acategoryTypes($AffinityCategoryID); ?></td>
	  </tr>
	  <tr id="hiddenSubCatRow">
		<td class="bodytext" align="right" valign="top"><?php echo $title; ?> Sub-Category:</td>
                <td>
			<select name="AffinitySubCategoryID[]" class="combo_box" size="4" multiple="multiple" id="SubCategorySelect"></select>
		</td>
	  </tr>
  <?php } 
  elseif($type==PUBLICATION) { ?>
	  <tr>
		<td class="bodytext" align="right"><?php echo $title; ?> Print Type:</td>
		<td><?php echo printTypes($printID); ?></td>
	  </tr>
	  <tr>
		<td class="bodytext" align="right"><?php echo $title; ?> Audience:</td>
		<td><?php echo audienceOptions($audienceID); ?></td>
	  </tr>
	  <tr>
		<td class="bodytext" align="right"><?php echo $title; ?> State/Province:</td>
		<td><select name="p_stateID" class="combo_box">
		<option value="0">&nbsp;</option>
		<?php getStates($p_stateID); ?>
		</select></td>
	  </tr>
	  <tr>
		<td class="bodytext" align="right"><?php echo $title; ?> Primary Country:</td>
		<td><select name="p_primary_country" class="combo_box">
		<option value="">&nbsp;</option>
		<?php
		$sqlc = "SELECT DISTINCT countryCode,country FROM cscan_state JOIN ISO31661_alpha2code ON (code=countryCode) ORDER BY country";
		$rsc = $DRW->query( $sqlc,$DRW_read );
		while($rowc = $DRW->fetch_row($rsc) ) {
			$id = $rowc[0];
			$name = $rowc[1];
			echo "<option value=\"$id\"";
			if($id==$p_primary_country) {
				echo " selected=\"selected\"";
			}
			echo ">".htmlspecialchars($name)."</option>";
		}
		?>
		</select></td>
	  </tr>
  <?php } 
  elseif($type ==COMPANY) { ?> 
  <tr>
	<td class="bodytext" align="right"><label for="isApprovedCo">Approved:</label></td>
	<td>
	<input type="checkbox" name="isApprovedCo" id="isApprovedCo" value="1" <?php if($isApprovedCo==1) print ' checked="checked"'; ?> />
	 </td>
  </tr>
  <tr>
	<td class="bodytext" align="right"><label for="isWorksiteVoluntary">Worksite/Voluntary:</label></td>
	<td>
	<input type="checkbox" name="isWorksiteVoluntary" id="isWorksiteVoluntary" value="1" <?php if($isWorksiteVoluntary==1) print ' checked="checked"'; ?> />
	 </td>
  </tr>
  <tr>
	<td class="bodytext" align="right"><label for="isCreditUnion">Credit Union:</label></td>
	<td>
	<input type="checkbox" name="isCreditUnion" id="isCreditUnion" value="1" <?php if($isCreditUnion==1) print ' checked="checked"'; ?> />
	 </td>
  </tr>
  <tr>
	<td class="bodytext" align="right"><label for="isInsuranceExchange">Insurance Exchange:</label></td>
	<td>
	<input type="checkbox" name="isInsuranceExchange" id="isInsuranceExchange" value="1" <?php if($isInsuranceExchange==1) print ' checked="checked"'; ?> />
	 </td>
  </tr>
  <tr>
	<td class="bodytext" align="right"><label for="isMilitaryCo">Military:</label></td>
	<td>
	<input type="checkbox" name="isMilitaryCo" id="isMilitaryCo" value="1" <?php if($isMilitaryCo==1) print ' checked="checked"'; ?> />
	 </td>
  </tr>
  <tr>
	<td class="bodytext" align="right"><label for="isRetailMarketer">Retail Marketer:</label></td>
	<td>
	<input type="checkbox" name="isRetailMarketer" id="isRetailMarketer" value="1" <?php if($isRetailMarketer==1) print ' checked="checked"'; ?> />
	 </td>
  </tr>
  <tr>
	<td class="bodytext" align="right" valign="top">Product Image:</td>
	<td><input type="file" class="input_box" name="imgFile" size="40" accept="image/png, image/jpeg" />
	<div class="bodytext">[Only .jpg and .png files less than 2MB in size can be uploaded]</div>
	<div id="prod_img_div" style="padding-top:6px;">
	<?php #################################### Start S3 Implementation Code ###########################################
		if(!empty($_GET['id']) && $_GET['type'] == 0){ 
			$query = "SELECT img_co_path, img_co_filename FROM cscan_img_company WHERE companyID='".$_GET['id']."'";
			$query_result = $DRW->query($query,$DRW_read);
			$data = $DRW->fetch_row($query_result);
			$img_co_path = $data[0];
			$img_co_filename = $data[1];
	?>
	<img src="<?= $displays3URL.substr($img_co_path,1).$img_co_filename; ?>" border="0" style="border:solid 1px #000000;" id="prod_img" />	
	<?php }else{ ?>
	<img src="../productImg.php?cid=<?php echo $updID.'&amp;new='.date('YmdHis'); ?>" border="0" style="border:solid 1px #000000;" id="prod_img" />	
	<?php } 
		#################################### End S3 Implementation Code ###########################################
	?>		
	</div>
	<div class="bodytext">
	<label><input type="checkbox" name="changeall" value="1" />Update Product Images</label>
	</div>
	 </td>
  </tr>
  <tr>
	<td class="bodytext" align="right" valign="top">Associated<br />Data:</td>
	<td><iframe id="dataframe" name="dataframe" src="addData.php?cid=<?php echo $updID; ?>" frameborder="0" marginheight="0" marginwidth="0" style="width:100%;height:200px;"></iframe></td>
  </tr>
  <tr>
	<td class="bodytext" align="right" valign="top">Associated<br />States/Provinces:</td>
	<td class="bodytext"><select name="co_states[]" class="combo_box" multiple="multiple" size="5">
	<?php 
	getStates($co_states);
	?>
	</select><br />[Hold ctrl key for multiple selection]</td>
  </tr>
  <tr>
	<td class="bodytext" align="right" valign="top">Associated:</td>
	<td class="bodytext" valign="top">
	<div id="scsc_combos"></div>
	<div style="margin:4px;">
	<div style="padding:4px;border: dashed 1px #000000;">
	<div id="sectorID_div">
	Sector
	<select name="sectorID" id="sectorID" class="combo_box" onchange="do_SCSC(document.frm1.sectorID,'sid',document.frm1.categoryID);do_SCSC(document.frm1.categoryID,'cid',document.frm1.subCategoryID);" style="display:block;"><option value="0">&nbsp;</option>
	<?php 
		$sector = getSector();
		foreach($sector as $id=>$name){
			if(checkSector($id)){
				echo "<option value=\"$id\">".htmlspecialchars($name)."</option>";
			}
		}
	?>
	</select>
	</div>
	<div id="categoryID_div" style="margin-top:5px;">
	Category
	<select name="categoryID" id="categoryID" class="combo_box" onchange="do_SCSC(document.frm1.categoryID,'cid',document.frm1.subCategoryID);" style="display:none;"><option value="0">&nbsp;</option></select>
	</div>
	<div id="subCategoryID_div" style="margin-top:5px;">
	Sub Category
	<select name="subCategoryID" id="subCategoryID" class="combo_box" style="display:none;"><option value="0">&nbsp;</option></select>
	</div>
	</div>
	<div style="padding:4px;">
	<a href="#" onclick="add_SCSC(); return false;">Add</a>
	</div>
	</div>
	<input type="hidden" name="scsc_comboIDs" id="scsc_comboIDs" value="<?php echo $comboIDs; ?>" />
	</td>
  </tr>
  <?php } ?>
  <tr><td colspan="2">&nbsp;</td></tr>
  <tr>
     <td>&nbsp;</td>
     <td>
     <?php 
	if($updID == 0){
	?>
          <input class="button" type="submit" name="submit1" value="Save" onclick="<?php if($type==AFFINITY){?>return validate_CatSubcat();<?php }else{ ?>return validate(); <?php }?>" disabled="disabled"/>
          <input class="button" type="submit" name="submit" value="Save &amp; Add More" style="width:120px;" onclick="<?php if($type==AFFINITY){?>return validate_CatSubcat();<?php }else{ ?>return validate(); <?php }?>" disabled="disabled"/>
  <?php }else{ ?>
          <input class="button" type="submit" name="submit1" value="Update" onclick="<?php if($type==AFFINITY){?>return validate_CatSubcat();<?php }else{ ?>return validate(); <?php }?>" disabled="disabled"/>
  <?php }?>
          <input class="button" type="button" value="Cancel" onclick="location.href='manageCategory.php?type=<?php echo $type; ?>'; return false;" />
     </td>
   </tr>
  </table>
  </td></tr></table>
       <input type="hidden" name="send" value="1" />
       <input type="hidden" name="id" value="<?php echo $updID; ?>"/>
       </form>
   </td>
  </tr>
  <?php //} ?>
</table>
<?php 
include 'bottom.php';
?>
