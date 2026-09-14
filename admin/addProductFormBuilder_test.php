<?php 
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');
mb_http_input('UTF-8');
if(!defined('ENV')){
    define('ENV',getenv('SERVER_NAME'));
} 
if(ENV == 'localhost'){
    $site_urls='http://localhost/competiscan.com/';
}elseif(ENV == 'demo.competiscan.com'){
    $site_urls='http://demo.competiscan.com/';
}else{
    $site_urls='https://competiscan.com/';
}
$javascript .= "var depsArrayID = new Array();\nvar depsArrayName = new Array();\nvar depsArray = new Array();\nvar depsArrayS = new Array();\nvar depsArrayM = new Array();\n";
$javascript .= "var variArray = new Array();\nvar variArrayID = new Array();\nvar variArrayName = new Array();\nvar coreArray = new Array();\nvar panelistInfoArray = new Array();\nvar genderArray = new Array();\ngenderArray['M'] = 0;\ngenderArray['F'] = 0;\n";
if($sectorID!='') {
	$sectorID = explode(',',$sectorID);
}
else {
	$sectorID = array();
}
if($categoryID!='') {
	$categoryID = explode(',',$categoryID);
}
else {
	$categoryID = array();
}
if($subCategoryID!='') {
	$subCategoryID = explode(',',$subCategoryID);
}
else {
	$subCategoryID = array();
}
if($subSubCategoryID!='') {
	$subSubCategoryID = explode(',',$subSubCategoryID);
}
else {
	$subSubCategoryID = array();
} 
if(isset($_REQUEST['hy']) and $_REQUEST['hy']!=""){
    $hy=$_REQUEST['hy'];
} else{
    $hy='';
}
$sector = getSector();
$scsc = array();
$javascript .= "depsArrayName['offerOrigin'] = new Array();\ndepsArrayID['offerOrigin'] = new Array();\ndepsArrayS['offerOrigin'] = new Array();\ndepsArrayM['offerOrigin'] = new Array();\n";
$javascript .= "depsArrayName['agentCommunicationID[]'] = new Array();\ndepsArrayID['agentCommunicationID[]'] = new Array();\ndepsArrayS['agentCommunicationID[]'] = new Array();\ndepsArray['agentCommunicationID[]'] = new Array();\n";
$javascript .= "depsArrayName['responseMechID[]'] = new Array();\ndepsArrayID['responseMechID[]'] = new Array();\ndepsArrayS['responseMechID[]'] = new Array();\n";
$javascript .= "depsArrayName['riders[]'] = new Array();\ndepsArrayID['riders[]'] = new Array();\ndepsArrayS['riders[]'] = new Array();\n";
$javascript .= "depsArrayName['multiculturalmarkets[]'] = new Array();\ndepsArrayID['multiculturalmarkets[]'] = new Array();\ndepsArrayS['multiculturalmarkets[]'] = new Array();\n";
foreach($sector as $id=>$name){
	$javascript .= "depsArrayS['offerOrigin']['$id'] = new Array();\n";
	$javascript .= "depsArrayS['agentCommunicationID[]']['$id'] = new Array();\n";
	$javascript .= "depsArrayS['responseMechID[]']['$id'] = new Array();\n";
	$javascript .= "depsArrayS['riders[]']['$id'] = new Array();\n";
	$javascript .= "depsArrayS['multiculturalmarkets[]']['$id'] = new Array();\n";
	$javascript .= "variArray['$id'] = new Array();\n";
	if(checkSector($id)){
		$scsc[$id] = $name;
		$javascript .= "sectorArray['$id'] = new Array();\n";
		$category = getCategory($id);
		if($category!==0){
			foreach( $category as $cid=>$cname ) {
				if(checkCategory($cid)){
					$scsc[$cid] = $cname;
					$javascript .= "depsArrayS['offerOrigin']['$cid'] = new Array();\n";
					$javascript .= "depsArrayS['agentCommunicationID[]']['$cid'] = new Array();\n";
					$javascript .= "depsArrayS['responseMechID[]']['$cid'] = new Array();\n";
					$javascript .= "depsArrayS['riders[]']['$cid'] = new Array();\n";
					$javascript .= "depsArrayS['multiculturalmarkets[]']['$cid'] = new Array();\n";
					$javascript .= "sectorArray['$id']['$cid'] = '".singleQuoteSafe($cname)."';\n";
					$javascript .= "categoryArray['$cid'] = new Array();\n";
					$scats = getSubCategory($cid);
					if($scats!==0){
						foreach( $scats as $scid=>$scname ) {
							if(checkSubCategory($scid)){
								$scsc[$scid] = $scname;
								$javascript .= "depsArrayS['offerOrigin']['$scid'] = new Array();\n";
								$javascript .= "depsArrayS['agentCommunicationID[]']['$scid'] = new Array();\n";
								$javascript .= "depsArrayS['responseMechID[]']['$scid'] = new Array();\n";
								$javascript .= "depsArrayS['riders[]']['$scid'] = new Array();\n";
								$javascript .= "depsArrayS['multiculturalmarkets[]']['$scid'] = new Array();\n";
								$javascript .= "categoryArray['$cid']['$scid'] = '".singleQuoteSafe($scname)."';\n";
								$javascript .= "subcategoryArray['$scid'] = '".singleQuoteSafe($scname)."';\n";
								$sscats = getSubCategory($scid);
								if($sscats!==0){
									foreach( $sscats as $sscid=>$sscname ) {
										$javascript .= "subcategoryArray['$scid']['$sscid'] = '".singleQuoteSafe($sscname)."';\n";
										$javascript .= "subsubcategoryArray['$sscid'] = '".singleQuoteSafe($sscname)."';\n";
									}
								}
							}
						}
					}
				}
			}
		}
	}
}
$saveDIArray = array();
$dai = 0;
$disfields = array();
$displayArray = array();
$displayKeys = array();
$dak = 'top';
$displayArray[$dak] = array();
$displayKeys[$dak] = '';
if($fromtemp){
	if($muid!=''){
		if($isTmp!=1){
			$t = 'cscan_email';
			if(!empty($hy)){
				$t .= $hy;
			}
			$query = "SELECT DATE_FORMAT(`email_date`,'%m/%d/%y %h:%i %p'),`email_to`,`email_from`,`email_subject` FROM `$t` WHERE `muid`='".$DRW->real_escape_string($muid)."'";
			$query_result = $DRW->query($query,$DRW_read);
			$data = $DRW->fetch_row($query_result);
			$email_date = $data[0];
			$email_to = $data[1];
			$email_from = $data[2];
			$email_subject = $data[3];
		}
		else{
			$email_date = '';
			$email_to = '';
			$email_from = '';
			$email_subject = '';
		}
		$displayArray[$dak][$dai]['title'] = 'Temp Product Number';
		$displayArray[$dak][$dai]['value'] = '<strong>'.$muid;
		if($isTmp==1) {
			$displayArray[$dak][$dai]['value'] .= 'tmp';
		}
		if($email_subject!='') {
			$displayArray[$dak][$dai]['value'] .= "<br />($email_subject)"; 
		}
		$displayArray[$dak][$dai]['value'] .= '</strong>';
		$dai++;
	}
}
else{
	$displayArray[$dak][$dai]['title'] = 'Assigned User';
	$displayArray[$dak][$dai]['value'] = '';
	if($updID!='' && checkGroup(26)){
		$displayArray[$dak][$dai]['value'] .= "<select style=\"font-family: verdana, Helvetica, sans-serif; font-size: 11px; color: #000000; border: 1px black solid;\" name=\"assigned_admin_userID\" size=\"1\">";
		$displayArray[$dak][$dai]['value'] .= "<option value=\"0\">All</option>";
		$sql2 = "select userID,userName,is_assign_queue from cscan_admin_users WHERE user_status=1 ORDER BY userName";
		$rs2 = $DRW->query($sql2,$DRW_read);
		while($row2 = $DRW->fetch_row($rs2)){
			$displayArray[$dak][$dai]['value'] .= "<option value=\"$row2[0]\"";
			if($row2[0]==$assigned_admin_userID) {
				$displayArray[$dak][$dai]['value'] .= " selected=\"selected\"";
			}
			$displayArray[$dak][$dai]['value'] .= ">";
			if($row2[2]) {
				$displayArray[$dak][$dai]['value'] .= '*';
			}
			$displayArray[$dak][$dai]['value'] .= htmlspecialchars($row2[1])."</option>";
		}
		$displayArray[$dak][$dai]['value'] .= '</select>';
	}
	else {
		$sql2 = "SELECT userName FROM cscan_admin_users WHERE userID=$assigned_admin_userID";
		$rs2 = $DRW->query($sql2,$DRW_read);
		$row2 = $DRW->fetch_row($rs2);
		if($row2[0]=='') {
			$displayArray[$dak][$dai]['value'] .= 'All';
		}
		else {
			$displayArray[$dak][$dai]['value'] .= $row2[0];
		}
		$displayArray[$dak][$dai]['value'] .= "<input type=\"hidden\" name=\"assigned_admin_userID\" value=\"$assigned_admin_userID\" />";
	}
	$dai++;
}
if($admin_userID!=0){
	$displayArray[$dak][$dai]['title'] = 'Last User';
	$displayArray[$dak][$dai]['value'] = '';
	$userquery = "SELECT userName FROM cscan_admin_users WHERE userID=$admin_userID";
	$userquery = $DRW->query($userquery,$DRW_read);
	if($DRW->num_rows($userquery)>0) {
		$row = $DRW->fetch_row($userquery);
		$userName = $row[0];
	}
	else {
		$userName = '';
	}
	if($userName!='') {
		if($fromtemp){
			$displayArray[$dak][$dai]['value'] .= "<a href=\"#\" class=\"bluelink\" onclick=\"logPop($muid,0,$isTmp); return false;\">$userName</a>";
		}
		else{
			$displayArray[$dak][$dai]['value'] .= "<a href=\"#\" onclick=\"logPop(0,$updID,0); return false;\">$userName</a>";
		}
	}
	else {
		$displayArray[$dak][$dai]['value'] .= '&nbsp;';
	}
	$dai++;
}
$displayArray[$dak][$dai]['title'] = 'Panelists';
$displayArray[$dak][$dai]['value'] = '';
if(!isset($_GET['muid']) || $fromtemp){
	$displayArray[$dak][$dai]['value'] .= '<a href="#" onclick="showDiv_outer(\'showbox_pans_outer\',\'addpan\'); return false;" id="addpan"';
	if($fromtemp){
		$displayArray[$dak][$dai]['value'] .= ' class="bluelink"';
	}
	$displayArray[$dak][$dai]['value'] .= '>Add Panelist</a>';
}
$displayArray[$dak][$dai]['value'] .= '<div style="height:60px;width:280px;overflow-y:scroll;border:solid 1px #DDF9EE;" id="pans_scroll"><div id="pans">';
$competi_printArray = array();
if($fromtemp){
	$p_idsArray = explode(',',$competi_ids);
	$invitation_idsArray = explode('|',$invitation_ids);
	$tracking_idsArray = explode('|',$tracking_ids);
	$fico_idsArray = explode('|',$fico_ids);
}
else{
	$p_idsArray = explode(',',$competi_ids_tmp);
	$invitation_idsArray = explode('|',$invitation_ids_tmp);
	$tracking_idsArray = explode('|',$tracking_ids_tmp);
	$fico_idsArray = explode('|',$fico_ids_tmp);
}
$currentt = time();
$competi_ids = '';
$invitation_ids = '';
$tracking_ids = '';
$fico_ids = '';
$ageArray = array();
$sql = "SELECT age_pID,age_pmin,age_pmax FROM cscan_age_product ORDER BY age_psort";
$result = $DRW->query( $sql,$DRW_read );
while( $row = $DRW->fetch_row( $result ) ) {
	$ageArray[$row[0]] = array($row[1],$row[2]);
}
if($updID!='' && !$fromtemp) {
	$resultC = $DRW->query("SELECT SQL_NO_CACHE competi_id,pa.panelist_id,ppdate,DATE_FORMAT(ppdate,'%Y-%m-%d'),invitationID,gender,DATEDIFF(CURDATE(),birthdate) as age,incomeID,stateID,birthdate,trackingID,pproductFICO FROM cscan_panelists pa,cscan_panelists_product pp 
		WHERE pa.panelist_id=pp.panelist_id AND pp.productID=$updID ORDER BY ppdate",$DRW_read);
	while($dataC = $DRW->fetch_row($resultC)){
		if($competi_ids!='') {
			$competi_ids .= ',';
		}
		$competi_ids .= $dataC[1].'|'.$dataC[2];
		$show = $dataC[0].' <span id="panInv'.$dataC[1].'_'.preg_replace('/\\D+/','',$dataC[2]).'">';
		if($dataC[4]!=''){
			$show .= "[{$dataC[4]}] ";
			$invitation_ids .= $dataC[4];
		}
		if($dataC[10]!=''){
			$show .= '{'.$dataC[10].'} ';
			$tracking_ids .= $dataC[10];
		}
		if($dataC[11]!=''){
			$show .= '#'.$dataC[11].' ';
			$fico_ids .= $dataC[11];
		}
		$show .= "($dataC[3]) </span>";
		$invitation_ids .= '|';
		$tracking_ids .= '|';
		$fico_ids .= '|';
		$competi_printArray[] = array($dataC[1],$show,$dataC[2],$dataC[0]);
		
		$pgender = strtoupper(substr($dataC[5],0,1));
		$page = floor($dataC[6]/365);
		$pincomeID = $dataC[7];
		$pstate = $dataC[8];
		$birthdate = $dataC[9];
		if($pincomeID==0) {
			$pincomeID = -1;
		}
		if($pstate==0) {
			$pstate = -1;
		}
		$ageID = -1;
		if($birthdate!='0000-00-00'){
			foreach($ageArray as $aID=>$a_array){
				if($page>=$a_array[0] && $page<=$a_array[1]){
					$ageID = $aID;
					break;
				}
			}
		}
		if($pgender!='M' && $pgender!='F'){
			$pgender = 'N';
		}
		$javascript .= "panelistInfoArray['$dataC[1]'] = new Array('$pgender',$ageID,$pincomeID,$pstate);\n";
		if($pgender=='M' || $pgender=='F'){
			$javascript .= "genderArray['$pgender'] = genderArray['$pgender'] + 1;\n";
		}
	}
}
elseif((isset($_GET['muid']) && $competi_ids_tmp!='') || ($fromtemp && $muid!='')){
	
	//if have duplicate emails for sameday
	$fileLike = '';
	if(!empty($_GET['dmuid'])){
		$dmuid = trim($_GET['dmuid']);
		$sql = "SELECT DISTINCT `duplicate_with` FROM `cscan_damaxmail_sameday_duplicate` WHERE `muid`='".$dmuid."'";
		$query22 = $DRW->query($sql,$DRW_read);
		if($DRW->num_rows($query22)>0){
			$res22 = $DRW->fetch_assoc($query22);
			$fileLike = basename($res22['duplicate_with']);
		}
	}elseif(!empty($_GET['muid']) && !empty($_GET['p_id'])){
		
		$o_muid = trim($_GET['muid']);
		$sql = "SELECT mailbox_uid FROM cscan_email WHERE muid='".$o_muid."'";
		$query22 = $DRW->query($sql,$DRW_read);
                if($DRW->num_rows($query22)>0){
                    $res22 = $DRW->fetch_assoc($query22);
                    $d_mailbox_uid = $res22['mailbox_uid'];
                    $fileLike = '_'.trim($d_mailbox_uid).'_'.trim($_GET['p_id']).'.html';
                }
	}
	if($fileLike){
		
		$sql = "SELECT DISTINCT `panelist_id`,`datetime` as p_date FROM `cscan_damaxmail_sameday_duplicate` WHERE `duplicate_with` like '%".$DRW->real_escape_string($fileLike)."%' ORDER BY p_date DESC";
		$query = $DRW->query($sql,$DRW_read2);
		if($DRW->num_rows($query)>0){
			$unique_panelists = [];
			if(count($p_idsArray)>0){
				foreach($p_idsArray as $el){
					$el_panelist = current(explode("|", $el));
					array_push($unique_panelists, $el_panelist);
				}
			}
			$unique_panelists = array_unique($unique_panelists);
			while($data = $DRW->fetch_assoc($query)){
				//$pdata = $data['panelist_id'].'|'.$data['p_date'];
				$pdata = $data['panelist_id'];
				if(!in_array($pdata,$unique_panelists)){
					array_push($p_idsArray, $pdata);
				}				
			}
			$p_idsArray = 	array_unique($p_idsArray);
		}		
	}

	foreach($p_idsArray as $k=>$p_id){
		$tmp = explode('|',$p_id);
		$p_id = trim($tmp[0]);
		if(isset($tmp[1])){
			$p_date = trim($tmp[1]);
		}
		else{
			$p_date = date('Y-m-d H:i:s');//'0000-00-00 00:00:00';
		}
		$p_date_f = substr($p_date,0,10);
		if(!empty($p_id)){
			$resultC = $DRW->query("SELECT competi_id,gender,DATEDIFF(CURDATE(),birthdate) as age,incomeID,stateID,birthdate FROM cscan_panelists WHERE panelist_id=$p_id",$DRW_read);
			$dataC = $DRW->fetch_row($resultC);
			
			if($competi_ids!='') {
				$competi_ids .= ',';
			}
			$competi_ids .= $p_id.'|'.$p_date;
			$show = $dataC[0].' <span id="panInv'.$p_id.'_'.preg_replace('/\\D+/','',$p_date).'">';
			if($invitation_idsArray[$k]!=''){
				$show .= "[{$invitation_idsArray[$k]}] ";
				$invitation_ids .= $invitation_idsArray[$k];
			}
			if($tracking_idsArray[$k]!=''){
				$show .= '{'.$tracking_idsArray[$k].'} ';
				$tracking_ids .= $tracking_idsArray[$k];
			}
			if($fico_idsArray[$k]!=''){
				$show .= '#'.$fico_idsArray[$k].' ';
				$fico_ids .= $fico_idsArray[$k];
			}
			$show .= "($p_date_f) </span>";
			$invitation_ids .= '|';
			$tracking_ids .= '|';
			$fico_ids .= '|';
			$competi_printArray[] = array($p_id,$show,$p_date,$dataC[0]);
			
			//this is the same as above with different offset
			$pgender = strtoupper(substr($dataC[1],0,1));
			$page = floor($dataC[2]/365);
			$pincomeID = $dataC[3];
			$pstate = $dataC[4];
			$birthdate = $dataC[5];
			if($pincomeID==0) {
				$pincomeID = -1;
			}
			if($pstate==0) {
				$pstate = -1;
			}
			$ageID = -1;
			if($birthdate!='0000-00-00'){
				foreach($ageArray as $aID=>$a_array){
					if($page>=$a_array[0] && $page<=$a_array[1]){
						$ageID = $aID;
						break;
					}
				}
			}
			if($pgender!='M' && $pgender!='F'){
				$pgender = 'N';
			}
			$javascript .= "panelistInfoArray['$p_id'] = new Array('$pgender',$ageID,$pincomeID,$pstate);\n";
			if($pgender=='M' || $pgender=='F'){
				$javascript .= "genderArray['$pgender'] = genderArray['$pgender'] + 1;\n";
			}
		}
	}
}
foreach($competi_printArray as $arry){
	$ppdate_part = substr($arry[2],0,10);
	if((preg_match('/^\\d{4}\\-\\d{2}\\-\\d{2}$/',$entryID) && $ppdate_part<$entryID) || $entryID==''){
		$entryID = $ppdate_part;
	}
	$pdid = preg_replace('/\\D+/','',$arry[2]);
	$displayArray[$dak][$dai]['value'] .= "<div id=\"pan$arry[0]_$pdid\">$arry[1]";
	if(!isset($_GET['muid']) || $fromtemp){ 
		$displayArray[$dak][$dai]['value'] .= "<a href=\"#\"";
		if($fromtemp){
			$displayArray[$dak][$dai]['value'] .= " class=\"bluelink\"";
		}
		$displayArray[$dak][$dai]['value'] .= " onclick=\"removePan('$arry[0]','$arry[2]','$pdid',true); return false;\">Remove</a> <a href=\"#\"";
		if($fromtemp){
			$displayArray[$dak][$dai]['value'] .= " class=\"bluelink\"";
		}
		$displayArray[$dak][$dai]['value'] .= " style=\"padding-left:6px;\" onclick=\"editPan('$arry[0]','$arry[2]','$pdid','$arry[3]'); return false;\">Edit</a>";
	}
	$displayArray[$dak][$dai]['value'] .= "</div>";
}
$displayArray[$dak][$dai]['value'] .= '</div></div>';
if($competi_ids!='' && $updID!='') {
	$displayArray[$dak][$dai]['value'] .= '<div id="cpan">[<a href="#" onclick="doPCopy(\''.$updID.'\',false); return false;">Move Panelist';
	if(count($competi_printArray)!=1) {
		$displayArray[$dak][$dai]['value'] .= 's';
	}
	$displayArray[$dak][$dai]['value'] .= '</a> | <a href="#" onclick="doPCopy(\''.$updID.'\',true); return false;">Move Panelist';
	if(count($competi_printArray)!=1) {
		$displayArray[$dak][$dai]['value'] .= 's';
	}
	$displayArray[$dak][$dai]['value'] .= ' &amp; Delete</a>]</div>';
}
$displayArray[$dak][$dai]['value'] .= '<div><label><input type="checkbox" id="is_state_specific" name="is_state_specific" value="1"';
if($is_state_specific==1) {
	$displayArray[$dak][$dai]['value'] .= ' checked="checked"';
}
$displayArray[$dak][$dai]['value'] .= $disabled;
$disfields[] = 'is_state_specific';
$displayArray[$dak][$dai]['value'] .= ' />State Specific</label></div>';
$displayArray[$dak][$dai]['value'] .= '<input type="hidden" name="competi_ids" value="'.htmlspecialchars($competi_ids,ENT_QUOTES).'" /><input type="hidden" name="invitation_ids" value="'.htmlspecialchars($invitation_ids,ENT_QUOTES).'" /><input type="hidden" name="tracking_ids" value="'.htmlspecialchars($tracking_ids,ENT_QUOTES).'" /><input type="hidden" name="fico_ids" value="'.htmlspecialchars($fico_ids,ENT_QUOTES).'" />';
$dai++;
$old_pub_ids = '';
$pub_printArray = array();
if($fromtemp){
	if($muid!=''){
		$publicationIDsArray = explode(',',$pub_ids);
		foreach($publicationIDsArray as $publicationID_date){
			$publicationID_date = trim($publicationID_date);
			if($publicationID_date!=''){
				list($publicationID,$publicationDate) = explode('|',$publicationID_date);
				$publicationDatef = substr($publicationDate,0,4).'-'.substr($publicationDate,4,2).'-'.substr($publicationDate,6,2);
				if($publicationID!=''){
					$resultC = $DRW->query("SELECT publicationName,publicationID FROM cscan_publication WHERE publicationID=$publicationID",$DRW_read);
					$dataC = $DRW->fetch_row($resultC);
					if($dataC[1]=='') {
						continue;
					}
					
					$pub_printArray[] = array($publicationID,"$dataC[0] [$publicationDatef]",$publicationDate);
				}
			}
		}
	}
}
else{
	$pub_ids = '';
	if($updID!='') {
		$resultC = $DRW->query("SELECT SQL_NO_CACHE pa.publicationID,publicationName,DATE_FORMAT(monthYear,'%Y%m%d'),DATE_FORMAT(monthYear,'%Y-%m-%d') FROM cscan_publication pa,cscan_publication_product pp 
			WHERE pa.publicationID=pp.publicationID AND pp.productID=$updID ORDER BY publicationName,monthYear",$DRW_read);
		while($dataC = $DRW->fetch_row($resultC)){
			if($pub_ids!='') {
				$pub_ids .= ',';
			}
			$pub_ids .= $dataC[0].'|'.$dataC[2];
			$pub_printArray[] = array($dataC[0],"$dataC[1] [$dataC[3]]",$dataC[2]);
		}
		$old_pub_ids = $pub_ids;
	}
	elseif(isset($_GET['muid']) && $pub_ids_tmp!=''){
		$pub_idsArray = explode(',',$pub_ids_tmp);
		foreach($pub_idsArray as $pub_id_date){
			$pub_id_date = trim($pub_id_date);
			if($pub_id_date!=''){
				list($pub_id,$pub_date) = explode('|',$pub_id_date);
				$publicationDatef = substr($pub_date,0,4).'-'.substr($pub_date,4,2).'-'.substr($pub_date,6,2);

				$resultC = $DRW->query("SELECT publicationName,publicationID FROM cscan_publication WHERE publicationID=$pub_id",$DRW_read);
				$dataC = $DRW->fetch_row($resultC);
				
				if($dataC[1]=='') {
					continue;
				}
				
				if($pub_ids!='') {
					$pub_ids .= ',';
				}
				$pub_ids .= $pub_id_date;
				$pub_printArray[] = array($pub_id,"$dataC[0] [$publicationDatef]",$pub_date);
			}
		}
	}
}
foreach($pub_printArray as $arry){
	$publicationDatef = substr($arry[2],0,4).'-'.substr($arry[2],4,2).'-'.substr($arry[2],6,2);
	if((preg_match('/^\\d{4}\\-\\d{2}\\-\\d{2}$/',$entryID) && $publicationDatef<$entryID) || $entryID==''){
		$entryID = $publicationDatef;
	}
}				
if(!$fromtemp){	
	if(preg_match('/^\\d{4}\\-\\d{2}\\-\\d{2}$/',$entryID) || $entryID==''){	
		$entrytitle = 'Entry Date';
		if($entryID==''){
			$entryID = date('Y-m-d');
		}
		$old_entryID = '';
	}
	else{
		$entrytitle = 'Entry ID';
		$old_entryID = $entryID;
	}
	if(isset($_GET['muid'])){
		if(!empty($firstSeen) && $firstSeen!='0000-00-00' && $firstSeen<$entryID){
			$entryID = $firstSeen;
		}
	}
	$displayArray[$dak][$dai]['title'] = "<span class=\"error\">*</span>$entrytitle";
	$displayArray[$dak][$dai]['value'] = "<input type=\"text\" name=\"entryID\" size=\"40\" maxlength=\"100\" class=\"input_box\" value=\"$entryID\" readonly=\"readonly\" /><input type=\"hidden\" name=\"old_entryID\" value=\"$old_entryID\" />";
	if(checkGroup(24)) {
		$displayArray[$dak][$dai]['value'] .= " <a href=\"#\" onclick=\"displayCalendar(document.prodForm.entryID,'yyyy-mm-dd',this); return false;\"><img name=\"popcal3\" src=\"js_calendar/images/getcal.gif\" border=\"0\" alt=\"\" style=\"vertical-align:bottom;\" /></a>";
	}
	$dai++;
}
if($firstSeen=='0000-00-00') {
	$firstSeen = date('Y-m-d');
}
if($lastSeen=='0000-00-00') {
	$lastSeen = $firstSeen;
}
if($fromtemp){
	$displayArray[$dak][$dai]['title'] = 'Entry Date';
	$displayArray[$dak][$dai]['value'] = '<input class="input_box" name="firstSeen" type="text" value="'.$firstSeen.'" size="15" readonly="readonly"'.$disabled.' />';
	$disfields[] = 'firstSeen';
	if(!isset($_GET['muid']) || $fromtemp) {
		$displayArray[$dak][$dai]['value'] .= ' <a href="#" onclick="displayCalendar(document.prodForm.firstSeen,\'yyyy-mm-dd\',this); return false;"><img name="popcal1" src="js_calendar/images/getcal.gif" border="0" alt="" style="vertical-align:bottom;" /></a>';
	}
	$dai++;
}

if($OfferExpiryDate=='0000-00-00'){
	$OfferExpiryDate = '';
}
$displayArray[$dak][$dai]['title'] = 'Offer Expiry Date';
$displayArray[$dak][$dai]['value'] = '<input class="input_box" name="OfferExpiryDate" type="text" value="'.$OfferExpiryDate.'" size="15" readonly="readonly"'.$disabled.' />';
$disfields[] = 'OfferExpiryDate';
if(!isset($_GET['muid']) || $fromtemp) {
	$displayArray[$dak][$dai]['value'] .= ' <a href="#" onclick="displayCalendar(document.prodForm.OfferExpiryDate,\'yyyy-mm-dd\',this); return false;"><img name="popcal5" src="js_calendar/images/getcal.gif" border="0" alt="" style="vertical-align:bottom;" /></a> &nbsp; <a href="#" onclick="document.prodForm.OfferExpiryDate.value=\'\'; return false;"';
	if($fromtemp){
		$displayArray[$dak][$dai]['value'] .= ' class="bluelink"';
	}
	$displayArray[$dak][$dai]['value'] .= '>clear</a>';
}
$dai++;
if(!$fromtemp && $updID!=''){
	$checkV = "SELECT SQL_NO_CACHE entryID,productID FROM cscan_product_detail WHERE variantID='".$DRW->real_escape_string($updID)."'";
	$checkV = $DRW->query($checkV,$DRW_read);
	if($DRW->num_rows($checkV)>0){
		$displayArray[$dak][$dai]['title'] = 'Variants';
		$displayArray[$dak][$dai]['value'] = '';
		while($dataV = $DRW->fetch_row($checkV)){
			$displayArray[$dak][$dai]['value'] .= "<a class=\"hlinks\" href=\"addproduct_test.php?id=$dataV[1]\">".$dataV[0].'</a><br />';
		}
		$dai++;
	}
}
$saveDIArray['mtvariant'] = array("!checkMailingType('3')");
$saveDIArray['mtvariant'][] = $dak.'_'.$dai;
$displayArray[$dak][$dai]['title'] = 'Variant of Entry ID';
if(!$fromtemp && $variantID!=0 && $variant==''){
	$checkV = "SELECT SQL_NO_CACHE entryID FROM cscan_product_detail WHERE productID='".$DRW->real_escape_string($variantID)."'";
	$checkV = $DRW->query($checkV,$DRW_read);
	$dataV = $DRW->fetch_row($checkV);
	$variant = $dataV[0];
}
$displayArray[$dak][$dai]['value'] = '<input type="text" name="variant" size="40" maxlength="255" class="input_box" onchange="checkDeps_variant();compare_variant();" value="'.htmlspecialchars($variant,ENT_QUOTES).'"'.$disabled.' />';
$disfields[] = 'variant';
if(!isset($_GET['muid']) || $fromtemp){
	$displayArray[$dak][$dai]['value'] .= " <a href=\"#\" onclick=\"displayCalendar(document.prodForm.variant,'yyyy-mm-dd-',this); return false;\"><img name=\"popcal4\" src=\"js_calendar/images/getcal.gif\" border=\"0\" alt=\"\" style=\"vertical-align:bottom;\" /></a>";
}
$displayArray[$dak][$dai]['value'] .= '<div style="display:none;font-style:italic;font-size:smaller;" id="compare_variant_div">&nbsp;<input type="hidden" name="sectorID_v" value="0" /><input type="hidden" name="categoryID_v" value="0" /><input type="hidden" name="subCategoryID_v" value="0" /><input type="hidden" name="subSubCategoryID_v" value="0" /><input type="hidden" name="companyID_v" value="0" /></div>';
$dai++;
$sector = getSector();
$saveDIArray['variant'] = array("document.prodForm.variant.value!=''");
$saveDIArray['variant'][] = $dak.'_'.$dai;
$displayArray[$dak][$dai]['title'] = 'Variant Type';
$displayArray[$dak][$dai]['value'] = '<select name="vid[]" multiple="multiple" size="5" class="combo_box"'.$disabled.'>';
$disfields[] = 'vid[]';
if($vid!="") {
	$vid = explode(",",$vid);
}
else {
	$vid = array();
}
$vids = $DRW->query("SELECT vid,vname,vnum,vt_sectorID FROM cscan_varianttype ORDER BY vnum",$DRW_read);
while($datav = $DRW->fetch_row($vids)){
	//$datav[1] = $datav[2].'- '.$datav[1];
	
	if($datav[3]!=''){
		$vt_sectorID = explode(',',$datav[3]);
	}
	else{
		$vt_sectorID = array();
	}
	$show = false;
	
	$javascript .= "variArrayName[variArrayName.length] = '".singleQuoteSafe($datav[1])."';\n";
	$javascript .= "variArrayID[variArrayID.length] = '$datav[0]';\n";
	foreach($sector as $sid=>$name){
		if(count($vt_sectorID)==0 || in_array($sid,$vt_sectorID)) {
			$javascript .= "variArray['$sid']['$datav[0]'] = true;\n";
			if(count($sectorID)==0 || count($vt_sectorID)==0 || in_array($sid,$sectorID)){
				$show = true;
			}
		}
	}
	if($show){
		$displayArray[$dak][$dai]['value'] .= "<option value=\"$datav[0]\"";
		if(in_array($datav[0],$vid)) {
			$displayArray[$dak][$dai]['value'] .= " selected=\"selected\"";
		}
		$displayArray[$dak][$dai]['value'] .= ">".htmlspecialchars($datav[1])."</option>";
	}
}
$displayArray[$dak][$dai]['value'] .= '</select><br />[Hold ctrl key for multiple selection]';
$dai++;
$img_companyID_new = 0;
$saveDIArray['mc_mobile'] = array("checkMediaChannel('5,7')");
if(!$fromtemp){
	$saveDIArray['mc_mobile'][] = $dak.'_'.$dai;
	$displayArray[$dak][$dai]['title'] = '*Product Media';
	$displayArray[$dak][$dai]['value'] = '<input type="file" class="input_box" name="mediafile" size="40" id="mediafile" />';
	$dai++;
	if ($mChannelID == 5 || $mChannelID == 7) {
		$sql3 = "SELECT SQL_NO_CACHE document_id,document_filename,document_size_byte FROM cscan_document WHERE productID=$updID AND document_id=2";
                $rs3 = $DRW->query($sql3,$DRW_read);
                $row3 = $DRW->fetch_row($rs3);
                $document_id = (int)$row3[0];
                $document_filename = $row3[1];
                $document_size_byte = (int)$row3[2];

                $sizeofPDFinKB=$document_size_byte/1024;
                $sizeofPDFinMB=$sizeofPDFinKB/1024;
                if($sizeofPDFinMB<1) {
                        $DisplaySize=round($sizeofPDFinKB,2)." KB";
                }
                else {
                        $DisplaySize=round($sizeofPDFinMB,2)." MB";
                }

                if($document_id!=0) {
			$curpdf = 'u';
			$saveDIArray['mc_mobile'][] = $dak.'_'.$dai;
			$displayArray[$dak][$dai]['title'] = '';
			$displayArray[$dak][$dai]['value'] = '[<a href="../productDocuments.php?did=2&id='.$updID.'" target="_blank">'.$document_filename.'</a>] ('.$DisplaySize.')';
			$dai++;
                }
	} 
        //#####################SHOW PREVIEW IN HTML####################//
          $query = "SELECT `esproduct`,`muid` FROM `cscan_email_save$hy` WHERE `muid`='".$DRW->real_escape_string($muid)."'";          
          $result = $DRW->query($query, $DRW_read);
          
          $query2 = "SELECT `muid` FROM `cscan_email$hy` WHERE `muid`='".$DRW->real_escape_string($muid)."' AND contact_type_m_c='cons_panelist'";          
          $result2 = $DRW->query($query2, $DRW_read);
            if (($DRW->num_rows($result) > 0) AND ($DRW->num_rows($result2) > 0)) {
                $displayPreviewHtml = '';
                $data2 = $DRW->fetch_row($result);
                        $cettext = $data2[0];
                        $muid = $data2[1];
                        $displayPreviewHtml= "<div class=\"section\"><a href=\"javascript:void(0);\" onclick=\"winPopMessageHTML('show_preview_html.php?muid=$muid&amp;hy=$hy'); return false;\" class=\"bluelink\"> Show Preview In Html</a><input type=\"hidden\" name=\"is_html_file\" id=\"is_html_file\" value=\"1\" /></div>"; 
                        //$messagetext = cleanHTML($cettext);

                 if(!empty($displayPreviewHtml)){
                    $displayArray[$dak][$dai]['title'] = '';
                    $displayArray[$dak][$dai]['value'] = $displayPreviewHtml;
                    $dai++;
                }
            }
            else{ 
               /* $displayArray[$dak][$dai]['title'] = '<span class="error">*</span>Product PDF';
                 $displayArray[$dak][$dai]['value'] = '<input type="file" class="input_box" name="PDFFILE" size="40" id="PDFFILE" />';
                 $dai++;   
               */
                if($updID!='') { 
                  $sql2 = "SELECT SQL_NO_CACHE document_id,document_filename,document_content_type FROM cscan_document WHERE productID=$updID AND document_id=1";  
                   $rs2 = $DRW->query($sql2,$DRW_read);
                   $row2 = $DRW->fetch_row($rs2);
                   $document_id = (int)$row2[0];
                   $document_filename = $row2[1];
                   $document_content_type = $row2[2];
                  /* if($document_content_type!='text/html'){
                       $displayArray[$dak][$dai]['title'] = '<span class="error">*</span>Product PDF';
                       $displayArray[$dak][$dai]['value'] = '<input type="file" class="input_box" name="PDFFILE" size="40" id="PDFFILE" />';
                       $dai++;  
                   } */
                }   
                 
            
            }
        //#####################SHOW PREVIEW IN HTML####################//
        
	$displayArray[$dak][$dai]['title'] = '<span class="error">*</span>Product PDF';
	$displayArray[$dak][$dai]['value'] = '<input type="file" class="input_box" name="PDFFILE" size="40" id="PDFFILE" />';
	$dai++;
	$document_id = 0;
	if($updID!='') {
		$pdftext = '';
		$sql2 = "SELECT SQL_NO_CACHE document_id,document_filename,document_size_byte FROM cscan_document WHERE productID=$updID AND document_id=1";
		$rs2 = $DRW->query($sql2,$DRW_read);
		$row2 = $DRW->fetch_row($rs2);
		$document_id = (int)$row2[0];
		$document_filename = $row2[1];
		$document_size_byte = (int)$row2[2];
		
		$sizeofPDFinKB=$document_size_byte/1024;
		$sizeofPDFinMB=$sizeofPDFinKB/1024;
		if($sizeofPDFinMB<1) {
			$DisplaySize=round($sizeofPDFinKB,2)." KB";
		}
		else {
			$DisplaySize=round($sizeofPDFinMB,2)." MB";
		}
		
		if($document_id!=0) {
			$curpdf = 'u';
			$pdftext .= 'Current PDF: [<a href="../productDocuments.php?id='.$updID.'" target="_blank">'.$document_filename.'</a>] ('.$DisplaySize.')
			<br /><a href="#" onclick="doPDFSample('.$updID.'); return false;">Manage Preview</a>';
		}
		
		$sql2 = "SELECT SQL_NO_CACHE document_id,document_filename,document_size_byte FROM cscan_document_orig WHERE productID=$updID AND document_id=1";
		$rs2 = $DRW->query($sql2,$DRW_read);
		$row2 = $DRW->fetch_row($rs2);
		$document_id_o = (int)$row2[0];
		$document_filename_o = $row2[1];
		$document_size_byte_o = (int)$row2[2];
		
		$sizeofPDFinKB=$document_size_byte_o/1024;
		$sizeofPDFinMB=$sizeofPDFinKB/1024;
		if($sizeofPDFinMB<1) {
			$DisplaySize=round($sizeofPDFinKB,2)." KB";
		}
		else {
			$DisplaySize=round($sizeofPDFinMB,2)." MB";
		}
		
		if($document_id_o!=0) {
			$pdftext .= '<br />Original PDF: [<a href="../productDocuments.php?id='.$updID.'&amp;orig=1" target="_blank">'.$document_filename_o.'</a>] ('.$DisplaySize.')';
		}
		if(!empty($pdftext)){
			$displayArray[$dak][$dai]['title'] = '';
			$displayArray[$dak][$dai]['value'] = $pdftext;
			$dai++;
		}
	}
	$displayArray[$dak][$dai]['title'] = '';
	$displayArray[$dak][$dai]['value'] = '<strong><a href="#" onclick="if(document.prodForm.managePDF.value==1){document.prodForm.managePDF.value=0;}else{document.prodForm.managePDF.value=1;}checkDeps_managePDF();return false;">Manage Product Content</a></strong><input type="hidden" name="managePDF" value="0" />';
	$dai++;
	$saveDIArray['managePDF'] = array("document.prodForm.managePDF.value==1");
	$saveDIArray['managePDF'][] = $dak.'_'.$dai;
	$displayArray[$dak][$dai]['title'] = 'Product Content';
	$displayArray[$dak][$dai]['value'] = '<em>Leave blank to use content in uploaded file</em>';
	if($updID!='') {
		$displayArray[$dak][$dai]['value'] .= ' [<a href="#" onclick="doPDFText('.$updID.'); return false;">View Current Content</a>]';
	}
	$displayArray[$dak][$dai]['value'] .= '<br /><textarea name="PDFContent" rows="5" cols="60" class="input_box" id="PDFContent">'; 
	if($PDFContent!=''){
		$displayArray[$dak][$dai]['value'] .= htmlspecialchars($PDFContent,ENT_QUOTES);
	}
	$displayArray[$dak][$dai]['value'] .= '</textarea>';
	$dai++;
	if(isset($_GET['muid'])){
		$displayArray[$dak][$dai]['title'] = 'Temp Product ID';
		$displayArray[$dak][$dai]['value'] = $_GET['muid'];
		if(isset($_REQUEST['isTmp'])) {
			$displayArray[$dak][$dai]['value'] .= 'tmp';
		}
		$displayArray[$dak][$dai]['value'] .= " <a href=\"manage_tmp_product.php\">Manage Temp Product</a>";
		$dai++;
	}
}
$displayArray[$dak][$dai]['title'] = '*Company';

$displayArray[$dak][$dai]['value'] = '';
if(!isset($_GET['muid']) || $fromtemp){
	$displayArray[$dak][$dai]['value'] .= '<a href="#" onclick="showDiv_outer(\'showbox_cmps_outer\',\'addcmplink\'); document.forms.cmp_selform.cmp_id.focus(); return false;" id="addcmplink"';
	if($fromtemp){
		$displayArray[$dak][$dai]['value'] .= ' class="bluelink"';
	}
	$displayArray[$dak][$dai]['value'] .= '>Add Company</a>';
}
$displayArray[$dak][$dai]['value'] .= '<div id="cmps">';
$old_cmp_ids = '';
$co_comboIDs = $co_states = '';
$is_insuranceexchange = 0;
if($fromtemp){
	if($muid!=''){
		$companyIDsArray = explode(',',$cmp_ids);
		foreach($companyIDsArray as $companyID){
			$companyID = trim($companyID);
			if($companyID!=''){
				$resultC = $DRW->query("SELECT companyName,companyID,co_states,comboIDs,isInsuranceExchange FROM cscan_company WHERE companyID=$companyID",$DRW_read);
				$dataC = $DRW->fetch_row($resultC);
				if($dataC[1]=='') {
					continue;
				}
				$displayArray[$dak][$dai]['value'] .= "<div id=\"cmp{$companyID}\">$dataC[0] <a href=\"#\"";
				if($fromtemp){
					$displayArray[$dak][$dai]['value'] .= ' class="bluelink"';
				}
				$displayArray[$dak][$dai]['value'] .= " onclick=\"sortCmp($companyID,-1); return false;\">Up</a> <a href=\"#\"";
				if($fromtemp){
					$displayArray[$dak][$dai]['value'] .= ' class="bluelink"';
				}
				$displayArray[$dak][$dai]['value'] .= " onclick=\"sortCmp($companyID,1); return false;\">Down</a> <a href=\"#\"";
				if($fromtemp){
					$displayArray[$dak][$dai]['value'] .= ' class="bluelink"';
				}
				$displayArray[$dak][$dai]['value'] .= " onclick=\"removeCmp($companyID); return false;\">Remove</a></div>";
				if($co_states!=''){
					$co_states .= '|';
				}
				$co_states .= $companyID.':'.$dataC[2];
				if($co_comboIDs!=''){
					$co_comboIDs .= '|';
				}
				$co_comboIDs .= $companyID.':'.$dataC[3];
				if(!empty($dataC[4])){
					$is_insuranceexchange = 2;
				}
			}
		}
	}
}
else{
	$cmp_ids = '';
	$cmp_printArray = array();
	if($updID!='') {
		$resultC = $DRW->query("SELECT SQL_NO_CACHE pa.companyID,companyName,co_states,comboIDs,isInsuranceExchange FROM cscan_company pa,cscan_company_product pp 
			WHERE pa.companyID=pp.companyID AND pp.productID=$updID ORDER BY primary_co ASC,companyName ASC",$DRW_read);
		while($dataC = $DRW->fetch_row($resultC)){
			if($cmp_ids!='') {
				$cmp_ids .= ',';
			}
			$cmp_ids .= $dataC[0];
			$cmp_printArray[] = $dataC;
		}
		$old_cmp_ids = $cmp_ids;
	}
	elseif(isset($_GET['muid']) && $cmp_ids_tmp!=''){
		$cmp_idsArray = explode(',',$cmp_ids_tmp);
		foreach($cmp_idsArray as $cmp_id){
			$cmp_id = trim($cmp_id);
			if($cmp_id!=''){
				$resultC = $DRW->query("SELECT companyID,companyName,co_states,comboIDs,isInsuranceExchange FROM cscan_company WHERE companyID=$cmp_id",$DRW_read);
				$dataC = $DRW->fetch_row($resultC);
				if(empty($dataC[0])) {
					continue;
				}
				if($cmp_ids!='') {
					$cmp_ids .= ',';
				}
				$cmp_ids .= $dataC[0];
				$cmp_printArray[] = $dataC;
			}
		}
	}
	foreach($cmp_printArray as $arry){
		$displayArray[$dak][$dai]['value'] .= "<div id=\"cmp{$arry[0]}\">$arry[1]";
		if(!isset($_GET['muid'])){ 
			$query2 = "SELECT COUNT(*) FROM cscan_img_company WHERE companyID=$arry[0]";
			$query_result2 = $DRW->query($query2,$DRW_read);
			$data2 = $DRW->fetch_row($query_result2);
			if($data2[0]>0){
				$displayArray[$dak][$dai]['value'] .= " (<a href=\"#\" onclick=\"doProductImg($arry[0]); return false;\">Product Image</a>)";
			}
			$displayArray[$dak][$dai]['value'] .= " <a href=\"#\" onclick=\"sortCmp($arry[0],-1); return false;\">Up</a> <a href=\"#\" onclick=\"sortCmp($arry[0],1); return false;\">Down</a> <a href=\"#\" onclick=\"removeCmp($arry[0]); return false;\">Remove</a>";
		}
		$displayArray[$dak][$dai]['value'] .= "</div>";
		
		if($co_states!=''){
			$co_states .= '|';
		}
		$co_states .= $arry[0].':'.$arry[2];
		if($co_comboIDs!=''){
			$co_comboIDs .= '|';
		}
		$co_comboIDs .= $arry[0].':'.$arry[3];
		if(!empty($arry[4])){
			$is_insuranceexchange = 2;
		}
	}
}
$displayArray[$dak][$dai]['value'] .= '</div><input type="hidden" name="cmp_ids" value="'.$cmp_ids.'" /><input type="hidden" name="old_cmp_ids" value="'.$old_cmp_ids.'" /><input type="hidden" name="is_insuranceexchange" value="'.$is_insuranceexchange.'" />';
if($fromtemp){
    if(isset($_GET['muid']) && $_GET['muid']!=''){
    
	$displayArray[$dak][$dai]['value'] .= '<input type="hidden" name="img_companyID2" value="'.$img_companyID_new.'" />';

    }
}
$dai++;
####################### for display the product image under temp ###############
/*
if(!$fromtemp){
	$displayArray[$dak][$dai]['title'] = '*Product Image';
	$img_link = 'productImg.php?id='.$updID;
	if($updID!=''){
		$query_i = "SELECT SQL_NO_CACHE COUNT(*) FROM cscan_img WHERE productID=$updID AND img_id=1";
		$query_result_i = $DRW->query($query_i,$DRW_read);
		$data_i = $DRW->fetch_row($query_result_i);
		if($data_i[0]>0){
			$curimg = 'u';
		}
	}
	if($curimg!='u'){
		if($updID!='') {
			$resultC = $DRW->query("SELECT SQL_NO_CACHE companyID FROM cscan_company_product WHERE productID=$updID AND primary_co=1",$DRW_read);
			$dataC = $DRW->fetch_row($resultC);
			$img_companyID_new = (int)$dataC[0];
		}
		elseif(isset($_GET['muid']) && $cmp_ids_tmp!=''){
			$cmp_idsArray = explode(',',$cmp_ids_tmp);
			$img_companyID_new = (int)array_shift($cmp_idsArray);
		}
		if($img_companyID_new!=0){
			$query2 = "SELECT COUNT(*) FROM cscan_img_company WHERE companyID=$img_companyID_new";
			$query_result2 = $DRW->query($query2,$DRW_read);
			$data2 = $DRW->fetch_row($query_result2);
			if($data2[0]>0){
				$img_link = 'productImg.php?cid='.$img_companyID_new;
			}
			else{
				$img_companyID_new = 0;
			}
		}
	}
	$displayArray[$dak][$dai]['value'] = '<input type="file" class="input_box" name="imgFile" size="40" onchange="document.forms.prodForm.defaultCoImg.disabled = false;" />
		<div class="bodytext">[Only .jpg, .png, and .gif files less than 2MB in size can be uploaded]</div>
		<div class="bodytext"><label><input type="checkbox" name="defaultCoImg" value="1" disabled="disabled" />Save As Default Company Image</label></div>
		<a class="hlinks" href="#" onclick="showHideImage(); return false;" style="display:none;"><span id="showHide">Hide Image</span></a>
		<div id="prod_img_div" style="display:block;">
		<img src="../'.$img_link.'&amp;new='.date('YmdHis').'" border="0" style="border:solid 1px #000000;" id="prod_img" width="150" height="100" />			
		</div><input type="hidden" name="img_companyID" value="'.$img_companyID_new.'" />';
	$dai++;
}
 
 */


$displayArray[$dak][$dai]['title'] = '*Product Image';
	$img_link = 'productImg.php?id='.$updID;
        
	if($updID!=''){
		$query_i = "SELECT SQL_NO_CACHE COUNT(*) FROM cscan_img WHERE productID=$updID AND img_id=1";
		$query_result_i = $DRW->query($query_i,$DRW_read);
		$data_i = $DRW->fetch_row($query_result_i);
		if($data_i[0]>0){
			$curimg = 'u';
		}
	}
	if($curimg!='u'){
           
		if($updID!='') {
			$resultC = $DRW->query("SELECT SQL_NO_CACHE companyID FROM cscan_company_product WHERE productID=$updID AND primary_co=1",$DRW_read);
			$dataC = $DRW->fetch_row($resultC);
			$img_companyID_new = (int)$dataC[0];
		}
		elseif(isset($_GET['muid']) && $cmp_ids_tmp!=''){
                        //print_r($cmp_ids_tmp);
			$cmp_idsArray = explode(',',$cmp_ids_tmp);
			$img_companyID_new = (int)array_shift($cmp_idsArray);
		}
                
               
		if($img_companyID_new!=0){
			$query2 = "SELECT COUNT(*) FROM cscan_img_company WHERE companyID=$img_companyID_new";
			$query_result2 = $DRW->query($query2,$DRW_read);
			$data2 = $DRW->fetch_row($query_result2);
			if($data2[0]>0){
				$img_link = 'productImg.php?cid='.$img_companyID_new;
			}
			else{
				$img_companyID_new = 0;
			}
		}
	}
	$displayArray[$dak][$dai]['value'] = '<input type="file" class="input_box" name="imgFile" size="40" onchange="document.forms.prodForm.defaultCoImg.disabled = false;" />
		<div class="bodytext">[Only .jpg, .png, and .gif files less than 2MB in size can be uploaded]</div>
		';
        if(!$fromtemp){
        $displayArray[$dak][$dai]['value'] .= '<div class="bodytext"><label><input type="checkbox" name="defaultCoImg" value="1" disabled="disabled" />Save As Default Company Image</label></div>
		<a class="hlinks" href="#" onclick="showHideImage(); return false;" style="display:none;"><span id="showHide">Hide Image</span></a>';
	}
        
         if(!empty($muid)){
            
           //  echo "SELECT img_co_path FROM cscan_temp_company_img WHERE muid=$muid";exit;
            $resultimgpath = $DRW->query("SELECT company_id,img_co_path,img_co_filename FROM cscan_temp_company_img WHERE muid=$muid",$DRW_read);
            $dataimgpath = $DRW->fetch_row($resultimgpath);
            if(!empty($dataimgpath)){
                $tempcompid         =   $dataimgpath[0];
                //$tempimg_co_path    =   $dataimgpath[1];
                $img_file           =   $dataimgpath[1];
                if(!empty($img_file)){
                    $img_companyID_new=$tempcompid;
                    $img_link           =   $site_urls.'company_tempImages/'.$img_file;
                }else if(!empty($tempcompid)){
                    $query2 = "SELECT img_co_path,img_co_filename FROM cscan_img_company WHERE companyID=$tempcompid";
			$query_result2 = $DRW->query($query2,$DRW_read);
			$data2 = $DRW->fetch_row($query_result2);
                        $img_companyID_new= $tempcompid;
                        $img_co_path    =   $data2[0];
                        $img_file=   $data2[1];
                         $img_link       =   $displays3URL.substr($img_co_path,1) .$img_file;
                       
                }
                
              
         
          $displayArray[$dak][$dai]['value'] .= '<div id="prod_img_div" style="display:block;">
		<img src="'.$img_link.'" border="0" style="border:solid 1px #000000;" id="prod_img" width="150" height="100" />			
		<input type="hidden" name="imgtempcompany" value="'.$img_link.'">
                 <input type="hidden" name="imgtempcompanyfile" value="'.$img_file.'">
                  <input type="hidden" name="img_companyID" value="'.$img_companyID_new.'" />  
                </div>';
            } else{
                //echo $img_companyID_new."jjj";exit;
//                $displayArray[$dak][$dai]['value'] .= '<div id="prod_img_div" style="display:block;">
//		<img src="" border="0" style="border:solid 1px #000000;" id="prod_img" width="150" height="100" />			
//		<input type="hidden" name="imgtempcompany" value="">
//                 <input type="hidden" name="imgtempcompanyfile" value="">      
//                </div>';
                $displayArray[$dak][$dai]['value'] .= '<div id="prod_img_div" style="display:block;">
		<img src="'.$site_urls.$img_link.'&amp;new='.date('YmdHis').'" border="0" style="border:solid 1px #000000;" id="prod_img" width="150" height="100" />			
		</div><input type="hidden" name="imgtempcompany" value="">
                 <input type="hidden" name="imgtempcompanyfile" value="">  
                 <input type="hidden" name="img_companyID" value="'.$img_companyID_new.'" />';
           
            }
          
            }else{ 
         $displayArray[$dak][$dai]['value'] .= '<div id="prod_img_div" style="display:block;">
		<img src="'.$site_urls.$img_link.'&amp;new='.date('YmdHis').'" border="0" style="border:solid 1px #000000;" id="prod_img" width="150" height="100" />			
		</div><input type="hidden" name="img_companyID" value="'.$img_companyID_new.'" />';
            }
         $dai++;


####################### end for display the product image under temp ###############
$displayArray[$dak][$dai]['title'] = 'Affinity/Association';
$displayArray[$dak][$dai]['value'] = '';
if(!isset($_GET['muid']) || $fromtemp){
	$displayArray[$dak][$dai]['value'] .= '<a href="#" onclick="showDiv_outer(\'showbox_affs_outer\',\'addafflink\'); document.forms.aff_selform.aff_id.focus(); return false;" id="addafflink"';
	if($fromtemp){
		$displayArray[$dak][$dai]['value'] .= ' class="bluelink"';
	}
	$displayArray[$dak][$dai]['value'] .= '>Add Affinity/Association</a>';
}
$displayArray[$dak][$dai]['value'] .= '<div id="affs">';
$old_aff_ids = '';
if($fromtemp){
	if($muid!=''){
		$affinityIDsArray = explode(',',$aff_ids);
		foreach($affinityIDsArray as $affinityID){
			$affinityID = trim($affinityID);
			if($affinityID!=''){
				$resultC = $DRW->query("SELECT affinityName,affinityID FROM cscan_affinity WHERE affinityID=$affinityID",$DRW_read);
				$dataC = $DRW->fetch_row($resultC);
				if($dataC[1]=='') {
					continue;
				}
				$displayArray[$dak][$dai]['value'] .= "<div id=\"aff{$affinityID}\">".htmlspecialchars($dataC[0]);
				$aff_cids = array();
				$resultC2 = $DRW->query("SELECT AffinityCategoryID FROM cscan_aff_cat WHERE affinityID=$affinityID",$DRW_read);
				while($dataC2 = $DRW->fetch_row($resultC2)){
					if(!in_array($dataC2[0],$aff_cids) && !empty($dataC2[0])){
						$aff_cids[] = $dataC2[0];
					}
				}
				if(count($aff_cids)>0){
					$displayArray[$dak][$dai]['value'] .= ' ('.htmlspecialchars(getAffinityCategoryName(implode(',',$aff_cids))).')';
				}
				$displayArray[$dak][$dai]['value'] .= " <a href=\"#\"";
				if($fromtemp){
					$displayArray[$dak][$dai]['value'] .= ' class="bluelink"';
				}
				$displayArray[$dak][$dai]['value'] .= " onclick=\"removeAff($affinityID); return false;\">Remove</a></div>";
			}
		}
	}
}
else{
	$aff_ids = '';
	$aff_printArray = array();
	if($updID!='') {
		$resultC = $DRW->query("SELECT SQL_NO_CACHE pa.affinityID,affinityName FROM cscan_affinity pa,cscan_affinity_product pp 
			WHERE pa.affinityID=pp.affinityID AND pp.productID=$updID ORDER BY affinityName",$DRW_read);
		while($dataC = $DRW->fetch_row($resultC)){
			if($aff_ids!='') {
				$aff_ids .= ',';
			}
			$aff_ids .= $dataC[0];
			$aff_cids = array();
			$resultC2 = $DRW->query("SELECT AffinityCategoryID FROM cscan_aff_cat WHERE affinityID=$dataC[0]",$DRW_read);
			while($dataC2 = $DRW->fetch_row($resultC2)){
				if(!in_array($dataC2[0],$aff_cids) && !empty($dataC2[0])){
					$aff_cids[] = $dataC2[0];
				}
			}
			$aff_printArray[] = array($dataC[0],$dataC[1],implode(',',$aff_cids));
		}
		$old_aff_ids = $aff_ids;
	}
	elseif(isset($_GET['muid']) && $aff_ids_tmp!=''){
		$aff_idsArray = explode(',',$aff_ids_tmp);
		foreach($aff_idsArray as $aff_id){
			$aff_id = trim($aff_id);
			if($aff_id!=''){
				$resultC = $DRW->query("SELECT affinityName,affinityID FROM cscan_affinity WHERE affinityID=$aff_id",$DRW_read);
				$dataC = $DRW->fetch_row($resultC);
				
				if($dataC[1]=='') {
					continue;
				}
				
				if($aff_ids!='') {
					$aff_ids .= ',';
				}
				$aff_ids .= $aff_id;
				$aff_cids = array();
				$resultC2 = $DRW->query("SELECT AffinityCategoryID FROM cscan_aff_cat WHERE affinityID=$aff_id",$DRW_read);
				while($dataC2 = $DRW->fetch_row($resultC2)){
					if(!in_array($dataC2[0],$aff_cids) && !empty($dataC2[0])){
						$aff_cids[] = $dataC2[0];
					}
				}
				$aff_printArray[] = array($aff_id,$dataC[0],implode(',',$aff_cids));
			}
		}
	}
	foreach($aff_printArray as $arry){
		$displayArray[$dak][$dai]['value'] .= "<div id=\"aff{$arry[0]}\">".htmlspecialchars($arry[1]);
		$cat = getAffinityCategoryName($arry[2]);
		if(!empty($cat)){
			$displayArray[$dak][$dai]['value'] .= ' ('.htmlspecialchars($cat).')';
		}
		if(!isset($_GET['muid'])){ 
			$displayArray[$dak][$dai]['value'] .= " <a href=\"#\" onclick=\"removeAff($arry[0]); return false;\">Remove</a>";
		}
		$displayArray[$dak][$dai]['value'] .= "</div>";
	}
}
$displayArray[$dak][$dai]['value'] .= '</div>
	<input type="hidden" name="aff_ids" value="'.$aff_ids.'" /><input type="hidden" name="old_aff_ids" value="'.$old_aff_ids.'" />';
$dai++;

$displayArray[$dak][$dai]['title'] = '<span class="error">*</span>Media Channel';
$displayArray[$dak][$dai]['value'] = '<select class="combo_box" name="mChannelID" onchange="getDeps(\'offerOrigin\');markInsuranceexchange();checkChannel();doDelMeth();checkDeps_mc(); showDigitalSource();"'.$disabled.'><option value="0">-- Select One --</option>';
$disfields[] = 'mChannelID';
$media_channel = getMediaChannel();
foreach( $media_channel as $id=>$name ) {
	$javascript .= "depsArrayM['offerOrigin']['$id'] = new Array();\n";
	$displayArray[$dak][$dai]['value'] .= "<option value=\"$id\"";
	if($mChannelID==$id) {
		$displayArray[$dak][$dai]['value'] .= " selected=\"selected\"";
	}
	$displayArray[$dak][$dai]['value'] .= ">".htmlspecialchars($name)."</option>";
}
$displayArray[$dak][$dai]['value'] .= '</select>';
$dai++;



/* Add for Digital Media chanel like online display, video and sem  */

$showhide=false;
$selected='';
$selected2='';
$selected3='';
if($mChannelID=='5' || $mChannelID=='10' || $mChannelID=='9') {
   $showhide=true;
   
    if($is_mobile=='1'){
        $selected='checked="checked"';
        
    }elseif($is_mobile=='2'){
        $selected2='checked="checked"';
        
    }else{
       // $selected3='checked="checked"';
        $selected='checked="checked"';
    }
}
if( $selected2=='' AND $selected==''){
    //$selected3='checked="checked"';
    $selected='checked="checked"';
}
//$displayArray[$dak][$dai]['value'] = $showhide;
$displayArray[$dak][$dai]['title'] = 'Digital Source :';
$displayArray[$dak][$dai]['value'] = '<input type="radio" value="1" name="ismobile" id="ismobile1"'.$selected.'><label for="ismobile1">Only Desktop </label>&nbsp;&nbsp;<input type="radio" value="2" name="ismobile" id="ismobile2"'.$selected2.'><label for="ismobile2">Only Mobile</label>';$dai++;
//&nbsp;&nbsp;<input type="radio" value="0" name="ismobile" id="ismobile3"'.$selected3.'><label for="ismobile3">Both</label>

/* End for digital Media chanel */ 





if($electronicID!="") {
	$electronicID = explode(",",$electronicID);
}
else {
	$electronicID = array();
}
$saveDIArray['mc_electronicID'] = array("checkMediaChannel('3')");
$saveDIArray['mc_electronicID'][] = $dak.'_'.$dai;
$displayArray[$dak][$dai]['title'] = '<span class="error">*</span>Electronic Type';
$displayArray[$dak][$dai]['value'] = '<select class="combo_box" id="electronicID" name="electronicID[]" multiple="multiple" size="2"';
if($mChannelID!=3) {
	$displayArray[$dak][$dai]['value'] .= ' disabled="disabled"';
}
else {
	$displayArray[$dak][$dai]['value'] .= $disabled;
	$disfields[] = 'electronicID[]';
}
$displayArray[$dak][$dai]['value'] .= '>';
$query_ac ="SELECT electronic_id,electronic_name FROM cscan_electronic ORDER BY electronic_name";
$result_ac = $DRW->query($query_ac,$DRW_read);
while($row_ac = $DRW->fetch_row($result_ac)){
	$id = $row_ac[0];
	$name = $row_ac[1];
	$displayArray[$dak][$dai]['value'] .= "<option value=\"$id\"";
	if(in_array($id,$electronicID)) {
		$displayArray[$dak][$dai]['value'] .= " selected=\"selected\"";
	}
	$displayArray[$dak][$dai]['value'] .= ">".htmlspecialchars($name)."</option>";
}
$displayArray[$dak][$dai]['value'] .= '</select>';
$dai++;
$saveDIArray['mc_external'] = array("checkMediaChannel('6')");

$saveDIArray['mc_external'][] = $dak.'_'.$dai;
$displayArray[$dak][$dai]['title'] = 'Number of Updates/Tweets';
$displayArray[$dak][$dai]['value'] = '<input type="text" name="external_updates" size="8" maxlength="50" class="input_box" value="'.htmlspecialchars($external_updates,ENT_QUOTES).'"';
if($mChannelID!=6) {
	$displayArray[$dak][$dai]['value'] .= ' disabled="disabled"';
}
else {
	$displayArray[$dak][$dai]['value'] .= $disabled;
	$disfields[] = 'external_updates';
}
$displayArray[$dak][$dai]['value'] .= ' />';
$dai++;
$saveDIArray['mc_external'][] = $dak.'_'.$dai;
$displayArray[$dak][$dai]['title'] = 'Number of Fans/Followers';
$displayArray[$dak][$dai]['value'] = '<input type="text" name="external_fans" size="8" maxlength="50" class="input_box" value="'.htmlspecialchars($external_fans,ENT_QUOTES).'"';
if($mChannelID!=6) {
	$displayArray[$dak][$dai]['value'] .= ' disabled="disabled"';
}
else {
	$displayArray[$dak][$dai]['value'] .= $disabled;
	$disfields[] = 'external_fans';
}
$displayArray[$dak][$dai]['value'] .= ' />';
$dai++;
$saveDIArray['mc_external'][] = $dak.'_'.$dai;
$displayArray[$dak][$dai]['title'] = '<span class="error">*</span>External Link';
$displayArray[$dak][$dai]['value'] = '<input type="text" name="external_link" size="40" maxlength="255" class="input_box" value="'.htmlspecialchars($external_link,ENT_QUOTES).'"';
if($mChannelID!=6) {
	$displayArray[$dak][$dai]['value'] .= ' disabled="disabled"';
}
else {
	$displayArray[$dak][$dai]['value'] .= $disabled;
	$disfields[] = 'external_link';
}
$displayArray[$dak][$dai]['value'] .= ' />';
$dai++;

#################### for the social media sponsered #############################
 if(!defined('ENV')){
            define('ENV',getenv('SERVER_NAME'));
        } 
//if(ENV == 'localhost' || ENV == 'demo.competiscan.com'){
$saveDIArray['mc_external'][] = $dak.'_'.$dai;
$displayArray[$dak][$dai]['title'] = '<span class="error">*</span>Social Media Ad Type';
//$displayArray[$dak][$dai]['value'] = '<input type="text" name="external_link" size="40" maxlength="255" class="input_box" value="'.htmlspecialchars($external_link,ENT_QUOTES).'"';
$displayArray[$dak][$dai]['value'] = '<select class="combo_box2" name="socialmedia_adtype"><option value="0">-- Select One --</option>';
$socialmedia_adtypeArray = array(1=>'Sponsored',2=>'Corporate');
foreach( $socialmedia_adtypeArray as $id=>$name ) {
	//$javascript .= "depsArray['agentCommunicationID[]']['$id'] = new Array();\n";
	$displayArray[$dak][$dai]['value'] .= "<option value=\"$id\"";
	if($socialmedia_adtype == $id) {
		$displayArray[$dak][$dai]['value'] .= " selected=\"selected\"";
	}
	$displayArray[$dak][$dai]['value'] .= ">".htmlspecialchars($name)."</option>";
}
$displayArray[$dak][$dai]['value'] .= '</select>';

$dai++;
//}
#################### end for the social media sponsered #############################





$displayArray[$dak][$dai]['title'] = '<span class="error">*</span>Audience';
###########  Communication Type Implementation ############
//if(ENV=='demo.competiscan.com' || ENV=='localhost'){
	!empty($updID) ? $productID = $updID : $productID = '';
	!empty($muid) ? $productTempID = $muid : $productTempID = '';

	$displayArray[$dak][$dai]['value'] = '<input type="hidden" value="'.$productID.'" id="hiddenProductID" /><input type="hidden" value="'.$productTempID.'" id="hiddenProductTempID" /><select class="combo_box audience" name="mPanelID"'.$disabled.'" onchange="doDelMeth();checkDeps_mp();check_BA();"><option value="0">-- Select One --</option>';
/*}else{
	$displayArray[$dak][$dai]['value'] = '<select class="combo_box" name="mPanelID"'.$disabled.' onchange="getDeps(\'agentCommunicationID[]\');doDelMeth();checkDeps_mp();check_BA();"><option value="0">-- Select One --</option>';
}*/
###########  Communication Type Implementation ############
$disfields[] = 'mPanelID';
// onchange="check_insurance();"
$mailing_panel = getMailingPanel();
foreach( $mailing_panel as $id=>$name ) {
	$javascript .= "depsArray['agentCommunicationID[]']['$id'] = new Array();\n";
	$displayArray[$dak][$dai]['value'] .= "<option value=\"$id\"";
	if($mPanelID == $id) {
		$displayArray[$dak][$dai]['value'] .= " selected=\"selected\"";
	}
	$displayArray[$dak][$dai]['value'] .= ">".htmlspecialchars($name)."</option>";
}
$displayArray[$dak][$dai]['value'] .= '</select>';
$dai++;
$coreArray = array();
$sql = "SELECT sectorID FROM cscan_sector WHERE sectorSearchActive=1 AND parentID=0 AND is_core=1";
$rs = $DRW->query($sql,$DRW_read);
while($row = $DRW->fetch_array($rs)) {
	$javascript .= "coreArray[coreArray.length] = $row[0];\n";
	$coreArray[] = $row[0];
}

if($fromtemp){
	$displayArray[$dak][$dai]['title'] = '<span class="error">*</span>';
}
else{
	$displayArray[$dak][$dai]['title'] = '*';
}
$displayArray[$dak][$dai]['title'] .= 'Sector / ';
if($fromtemp){
	$displayArray[$dak][$dai]['title'] .= '<span class="error">*</span>';
}
else{
	$displayArray[$dak][$dai]['title'] .= '*';
}
$displayArray[$dak][$dai]['title'] .= 'Category / Sub Category';
$displayArray[$dak][$dai]['value'] = '<div id="scsc_combos"></div><div style="margin:4px;';
if(isset($_GET['muid']) && !$fromtemp){
	$displayArray[$dak][$dai]['value'] .= 'display:none;';
}
$displayArray[$dak][$dai]['value'] .= '">
<div style="float:left;padding:4px;border: dashed 1px #000000;">
<div id="sectorID_div">
Sector
<select name="combo_sid" id="combo_sid" class="combo_box" onchange="clearSCSC();" style="display:block;"><option value="0">&nbsp;</option>';
foreach($sector as $id=>$name){
	if(checkSector($id)){
		$displayArray[$dak][$dai]['value'] .= "<option value=\"$id\">".htmlspecialchars($name)."</option>";
	}
}
$displayArray[$dak][$dai]['value'] .= '</select>
</div>
<div id="categoryID_div" style="margin-top:5px;">
Category
<select name="combo_cid" id="combo_cid" class="combo_box" onchange="do_SCSC(document.prodForm.combo_cid,\'cid\',document.prodForm.combo_scid,true);" style="display:none;"><option value="0">&nbsp;</option></select>
</div>
<div id="subCategoryID_div" style="margin-top:5px;">
Sub Category
<select name="combo_scid" id="combo_scid" class="combo_box" style="display:none;" onchange="do_SCSC(document.prodForm.combo_scid,\'scid\',document.prodForm.combo_sscid,true);"><option value="0">&nbsp;</option></select>
</div>
<div id="subSubCategoryID_div" style="margin-top:5px;">
Sub Sub Category
<select name="combo_sscid" id="combo_sscid" class="combo_box" style="display:none;"><option value="0">&nbsp;</option></select>
</div>
</div>
<div style="padding:4px;clear:left;">
<a href="#" onclick="add_SCSC(); return false;" id="add_SCSC_link"';
if($fromtemp){
	$displayArray[$dak][$dai]['value'] .= ' class="bluelink"';
}
$displayArray[$dak][$dai]['value'] .= '>Add</a>
</div>
</div>
<input type="hidden" name="scsc_comboIDs" id="scsc_comboIDs" value="';
if($comboIDs!='0_0_0' && $comboIDs!='0_0_0_0'){
	$displayArray[$dak][$dai]['value'] .= $comboIDs;
	
	$c1 = explode('|',$comboIDs);
	foreach($c1 as $c){
		$c2 = explode('_',$c);
		if(count($c2)>=3 && (!checkSector($c2[0]) || !checkCategory($c2[1]) || !checkSubCategory($c2[2]))){
			$nopermission = true;
		}					
	}
}
$displayArray[$dak][$dai]['value'] .='" /><input type="hidden" name="co_comboIDs" value="'.$co_comboIDs.'" /><input type="hidden" name="scsc_combo_edit" value="" />';
$dai++;
if($IssueTypeID!="") {
	$IssueTypeID = explode(",",$IssueTypeID);
}
else {
	$IssueTypeID = array();
}
$saveDIArray['s_issuetype'] = array("checkSector('13')");
$saveDIArray['s_issuetype'][] = $dak.'_'.$dai;
$displayArray[$dak][$dai]['title'] = 'Issue Type';
$displayArray[$dak][$dai]['value'] = '<select class="combo_box" name="IssueTypeID[]" multiple="multiple" id="IssueTypeID" size="3"'.$disabled.'>';
$disfields[] = 'IssueTypeID[]';
$query_ac ="SELECT IssueTypeID,IssueTypeName FROM cscan_issue_type ORDER BY IssueTypeSort";
$result_ac = $DRW->query($query_ac,$DRW_read);
while($row_ac = $DRW->fetch_row($result_ac)){
	$id = $row_ac[0];
	$name = $row_ac[1];
	
	$displayArray[$dak][$dai]['value'] .= "<option value=\"$id\"";
	if(in_array($id,$IssueTypeID)) {
		$displayArray[$dak][$dai]['value'] .= " selected=\"selected\"";
	}
	$displayArray[$dak][$dai]['value'] .= ">".htmlspecialchars($name)."</option>";
}
$displayArray[$dak][$dai]['value'] .= '</select>';
$dai++;
$displayArray[$dak][$dai]['title'] = '*Offer Origin';
$displayArray[$dak][$dai]['value'] = '<select name="offerOrigin" class="combo_box"'.$disabled.' onchange="checkInsuranceexchange();">';
$disfields[] = 'offerOrigin';
$displayArray[$dak][$dai]['value'] .= "<option value=\"0\"";
if($offerOrigin=='0') {
	$displayArray[$dak][$dai]['value'] .= " selected=\"selected\"";
}
$displayArray[$dak][$dai]['value'] .= ">&nbsp;</option>";
$javascript .= "depsArrayName['offerOrigin'][depsArrayName['offerOrigin'].length] = '';\n";
$javascript .= "depsArrayID['offerOrigin'][depsArrayID['offerOrigin'].length] = '0';\n";
$query_ac ="SELECT oo_id,oo_name,oo_mChannelID,oo_sectorID FROM cscan_offer_origin ORDER BY oo_name";
$result_ac = $DRW->query($query_ac,$DRW_read);
while($row_ac = $DRW->fetch_row($result_ac)){
	$id = $row_ac[0];
	$name = $row_ac[1];
	
	if($row_ac[2]!=''){
		$ac_mChannelIDArray = explode(',',$row_ac[2]);
	}
	else{
		$ac_mChannelIDArray = array();
	}
	$ac_sectorID_not = array();
	if($row_ac[3]!=''){
		$ac_sectorID = explode(',',$row_ac[3]);
		foreach($ac_sectorID as $k=>$v){
			if(strpos($v,'-')!==false){
				$ac_sectorID_not[] = substr($v,1);
				unset($ac_sectorID[$k]);
			}
		}
	}
	else{
		$ac_sectorID = array();
	}
	$show1 = false;
	$show2 = false;
	$javascript .= "depsArrayName['offerOrigin'][depsArrayName['offerOrigin'].length] = '".singleQuoteSafe($name)."';\n";
	$javascript .= "depsArrayID['offerOrigin'][depsArrayID['offerOrigin'].length] = '$id';\n";
	foreach($media_channel as $mcid=>$mcname){
		if(count($ac_mChannelIDArray)==0 || in_array($mcid,$ac_mChannelIDArray)) {
			$javascript .= "depsArrayM['offerOrigin']['$mcid']['$id'] = true;\n";
			if($mcid==$mChannelID){
				$show1 = true;
			}
		}
	}
	foreach($scsc as $sid=>$name2){
		if(!in_array($sid,$ac_sectorID_not) && (count($ac_sectorID)==0 || (in_array('C',$ac_sectorID) && in_array($sid,$coreArray)) || (in_array('N',$ac_sectorID) && !in_array($sid,$coreArray)) || in_array($sid,$ac_sectorID))) {
			$javascript .= "depsArrayS['offerOrigin']['$sid']['$id'] = true;\n";
			if(in_array($sid,$sectorID) || in_array($sid,$categoryID) || in_array($sid,$subCategoryID)) {
				$show2 = true;
			}
		}
	}
	if($show1 && $show2){
		$displayArray[$dak][$dai]['value'] .= "<option value=\"$id\"";
		if($id==$offerOrigin) {
			$displayArray[$dak][$dai]['value'] .= " selected=\"selected\"";
		}
		$displayArray[$dak][$dai]['value'] .= ">".htmlspecialchars($name)."</option>";
	}
}
$displayArray[$dak][$dai]['value'] .= '</select>';
$dai++;
if($fromtemp){
	$bcolor = '#0055E3';
	$displayArray[$dak][$dai]['title'] = '<span class="error">*</span>';
}
else{
	$bcolor = '#14734F';
	$displayArray[$dak][$dai]['title'] = '*';
}
$displayArray[$dak][$dai]['title'] .= 'Product Name';
$displayArray[$dak][$dai]['value'] = '<div id="showbox_cpns_outer" style="display:none;margin:0px 0px 4px 4px;"><div><em>Lookup</em><br /><input type="text" name="co_productName" class="input_box" style="border:solid 1px '.$bcolor.';" size="30" maxlength="200" autocomplete="off" onkeyup="startTimer(\'showCPNs()\');" /></div><div id="showbox_cpns" style="border:solid 1px #ffffff;background:'.$bcolor.';padding:4px;color:#ffffff;display:none;float:left;"></div></div>
<div style="clear:left;"><input type="text" name="productName" size="40" maxlength="255" class="input_box" value="'.htmlspecialchars($productName,ENT_QUOTES).'"'.$disabled.' /></div>';
$disfields[] = 'productName';
$dai++;
if($fromtemp){
	$displayArray[$dak][$dai]['title'] = '<span class="error">*</span>';
}
else{
	$displayArray[$dak][$dai]['title'] = '*';
}
$displayArray[$dak][$dai]['title'] .= 'Product Headline';
$displayArray[$dak][$dai]['value'] = '<textarea name="productHeadline" rows="5" cols="60" class="input_box" onkeyup="startTimer(\'showHeads(\\\'';
if($fromtemp){
	$displayArray[$dak][$dai]['value'] .= 'admin/';
}
$displayArray[$dak][$dai]['value'] .= 'checkheads.php\\\')\');" onblur="hideHeads();"'.$disabled.'>'.htmlspecialchars($productHeadline,ENT_QUOTES).'</textarea><input type="hidden" name="old_productHeadline" value="'.htmlspecialchars($productHeadline,ENT_QUOTES).'" />';
$disfields[] = 'productHeadline';
if(!isset($_GET['muid'])) {
        /* Commented for removing duplicate headline restriction */
	//$displayArray[$dak][$dai]['value'] .= '<br /><input type="button" class="button" onclick="checkHeadline(\''.$updID.'\'); return false;" value="Check Headline" />';
}
$dai++;
$saveDIArray['mc_mobile'][] = $dak.'_'.$dai;
$displayArray[$dak][$dai]['title'] = 'Observed Traffic Sources';
$displayArray[$dak][$dai]['value'] = '<textarea name="traffic_sources" rows="5" cols="60" class="input_box"';
if($mChannelID!=5 && $mChannelID!=7) {
	$displayArray[$dak][$dai]['value'] .= ' disabled="disabled"';
}
else {
	$displayArray[$dak][$dai]['value'] .= $disabled;
	$disfields[] = 'traffic_sources';
}
$displayArray[$dak][$dai]['value'] .= ' >'.htmlspecialchars($traffic_sources,ENT_QUOTES).'</textarea>';
$dai++;
//$saveDIArray['s_worksiteVoluntary'] = array("checkSector('4')");
//$saveDIArray['s_worksiteVoluntary'][] = $dak.'_'.$dai;
$displayArray[$dak][$dai]['title'] = 'Worksite/Voluntary';//<label for="worksiteVoluntary"></label>
$displayArray[$dak][$dai]['value'] = '<input type="checkbox" id="worksiteVoluntary" name="worksiteVoluntary" value="1"';
if($worksiteVoluntary==1) {
	$displayArray[$dak][$dai]['value'] .= ' checked="checked"';
}
$displayArray[$dak][$dai]['value'] .= $disabled;
$disfields[] = 'worksiteVoluntary';
$displayArray[$dak][$dai]['value'] .= ' />';
$dai++;
$saveDIArray['s_prescription'] = array("checkSector('119')");
$saveDIArray['s_prescription'][] = $dak.'_'.$dai;
$displayArray[$dak][$dai]['title'] = 'Rx';
$displayArray[$dak][$dai]['value'] = '<input type="checkbox" id="prescription" name="prescription" value="1"';
if($prescription==1) {
	$displayArray[$dak][$dai]['value'] .= ' checked="checked"';
}
$displayArray[$dak][$dai]['value'] .= $disabled;
$disfields[] = 'prescription';
$displayArray[$dak][$dai]['value'] .= ' />';
$dai++;

$saveDIArray['s_is_hphsa'] = array("checkSector('14')");
$saveDIArray['s_is_hphsa'][] = $dak.'_'.$dai;
$displayArray[$dak][$dai]['title'] = 'CDHP/HDHP/HSA';
$displayArray[$dak][$dai]['value'] = '<input type="checkbox" id="is_hphsa" name="is_hphsa" value="1"';
if($is_hphsa==1) {
	$displayArray[$dak][$dai]['value'] .= ' checked="checked"';
}
$displayArray[$dak][$dai]['value'] .= $disabled;
$disfields[] = 'is_hphsa';
$displayArray[$dak][$dai]['value'] .= ' />';
$dai++;

//$saveDIArray['s_is_prescreen'] = array("checkSector('12,15,206')");
//$saveDIArray['s_is_prescreen'][] = $dak.'_'.$dai;
$displayArray[$dak][$dai]['title'] = 'Pre-Screen &amp; Opt-Out Notice';
$displayArray[$dak][$dai]['value'] = '<input type="checkbox" id="is_prescreen" name="is_prescreen" value="1"';
if($is_prescreen==1) {
	$displayArray[$dak][$dai]['value'] .= ' checked="checked"';
}
$displayArray[$dak][$dai]['value'] .= $disabled;
$disfields[] = 'is_prescreen';
$displayArray[$dak][$dai]['value'] .= ' />';
$dai++;

$saveDIArray['s_FeeProduct'] = array("checkSector('4,87,90,6,9,219,315')");
$saveDIArray['s_FeeProduct'][] = $dak.'_'.$dai;
$displayArray[$dak][$dai]['title'] = 'Fee Product';
$displayArray[$dak][$dai]['value'] = '<input type="checkbox" id="FeeProduct" name="FeeProduct" value="1"';
if($FeeProduct==1) {
	$displayArray[$dak][$dai]['value'] .= ' checked="checked"';
}
$displayArray[$dak][$dai]['value'] .= $disabled;
$disfields[] = 'FeeProduct';
$displayArray[$dak][$dai]['value'] .= ' />';
$dai++;

############# for the Faux Credit Checkbox ##############

$saveDIArray['s_FeeProduct'] = array("checkSector('4,87,90,6,9,219,315')");
$saveDIArray['s_FeeProduct'][] = $dak.'_'.$dai;
$displayArray[$dak][$dai]['title'] = 'Faux Check';
$displayArray[$dak][$dai]['value'] = '<input type="checkbox" id="faux_check" name="faux_check" value="1"';
if($faux_check==1) {
	$displayArray[$dak][$dai]['value'] .= ' checked="checked"';
}
$displayArray[$dak][$dai]['value'] .= $disabled;
$disfields[] = 'faux_check';
$displayArray[$dak][$dai]['value'] .= ' />';
$dai++;
############# end for the Faux Credit Checkbox ##############


$saveDIArray['s_FeeProduct'][] = $dak.'_'.$dai;
$displayArray[$dak][$dai]['title'] = 'Ancillary Products';
$displayArray[$dak][$dai]['value'] = '<select name="FeeProductType[]" multiple="multiple" size="5" class="combo_box"'.$disabled.'>';
$disfields[] = 'FeeProductType[]';
if($FeeProductType!="") {
	$FeeProductType = explode(",",$FeeProductType);
}
else {
	$FeeProductType = array();
}
$fptArray = getFeeProductType();
foreach($fptArray as $selvalue=>$seltext){
	$displayArray[$dak][$dai]['value'] .= "<option value=\"$selvalue\"";
	if(in_array($selvalue,$FeeProductType)) {
		$displayArray[$dak][$dai]['value'] .= " selected=\"selected\"";
	}
	$displayArray[$dak][$dai]['value'] .= ">".htmlspecialchars($seltext)."</option>";
}
$displayArray[$dak][$dai]['value'] .= '</select><br />[Hold ctrl key for multiple selection]';
$dai++;
$saveDIArray['s_groupSize'] = array("checkSector('4,5')");
$saveDIArray['s_groupSize'][] = $dak.'_'.$dai;
$displayArray[$dak][$dai]['title'] = 'Group Size';
$displayArray[$dak][$dai]['value'] = '<select name="groupSize[]" multiple="multiple" size="5" class="combo_box"'.$disabled.'>';
$disfields[] = 'groupSize[]';
if($groupSize!="") {
	$groupSize = explode(",",$groupSize);
}
else {
	$groupSize = array();
}
$groupArray = get_groupSizeArray();
$displayArray[$dak][$dai]['value'] .= "<option value=\"0\"";
if(in_array('0',$groupSize)) {
	$displayArray[$dak][$dai]['value'] .= " selected=\"selected\"";
}
$displayArray[$dak][$dai]['value'] .= ">N/A</option>";
foreach($groupArray as $selvalue=>$seltext){
	$displayArray[$dak][$dai]['value'] .= "<option value=\"$selvalue\"";
	if(in_array($selvalue,$groupSize)) {
		$displayArray[$dak][$dai]['value'] .= " selected=\"selected\"";
	}
	$displayArray[$dak][$dai]['value'] .= ">".htmlspecialchars($seltext)."</option>";
}
$displayArray[$dak][$dai]['value'] .= '</select><br />[Hold ctrl key for multiple selection]';
$dai++;
$saveDIArray['s_fa_id'] = array("checkSector('13')");
$saveDIArray['s_fa_id'][] = $dak.'_'.$dai;
$displayArray[$dak][$dai]['title'] = 'Face Amount';
$displayArray[$dak][$dai]['value'] = '<select name="fa_id[]" multiple="multiple" size="5" class="combo_box"'.$disabled.'>';
$disfields[] = 'fa_id[]';
if($fa_id!="") {
	$fa_id = explode(",",$fa_id);
}
else {
	$fa_id = array('0');
}
$displayArray[$dak][$dai]['value'] .= "<option value=\"0\"";
if(count($fa_id)<=1 && in_array('0',$fa_id)) {
	$displayArray[$dak][$dai]['value'] .= " selected=\"selected\"";
}
$displayArray[$dak][$dai]['value'] .= ">N/A</option>";
$query_ac ="SELECT fa_id,fa_name FROM cscan_face_amount ORDER BY fa_sort";
$result_ac = $DRW->query($query_ac,$DRW_read);
while($row_ac = $DRW->fetch_row($result_ac)){
	$id = $row_ac[0];
	$name = $row_ac[1];
	$displayArray[$dak][$dai]['value'] .= "<option value=\"$id\"";
	if(in_array($id,$fa_id)) {
		$displayArray[$dak][$dai]['value'] .= " selected=\"selected\"";
	}
	$displayArray[$dak][$dai]['value'] .= ">".htmlspecialchars($name)."</option>";
}
$displayArray[$dak][$dai]['value'] .= '</select><br />[Hold ctrl key for multiple selection]';
$dai++;
$saveDIArray['s_tl_id'] = array("checkSector('81')");
$saveDIArray['s_tl_id'][] = $dak.'_'.$dai;
$displayArray[$dak][$dai]['title'] = 'Term Length';
$displayArray[$dak][$dai]['value'] = '<select name="tl_id[]" multiple="multiple" size="5" class="combo_box"'.$disabled.'>';
$disfields[] = 'tl_id[]';
if($tl_id!="") {
	$tl_id = explode(",",$tl_id);
}
else {
	$tl_id = array('0');
}
$displayArray[$dak][$dai]['value'] .= "<option value=\"0\"";
if(count($tl_id)<=1 && in_array('0',$tl_id)) {
	$displayArray[$dak][$dai]['value'] .= " selected=\"selected\"";
}
$displayArray[$dak][$dai]['value'] .= ">N/A</option>";
$query_ac ="SELECT tl_id,tl_name FROM cscan_term_length ORDER BY tl_sort";
$result_ac = $DRW->query($query_ac,$DRW_read);
while($row_ac = $DRW->fetch_row($result_ac)){
	$id = $row_ac[0];
	$name = $row_ac[1];
	
	$displayArray[$dak][$dai]['value'] .= "<option value=\"$id\"";
	if(in_array($id,$tl_id)) {
		$displayArray[$dak][$dai]['value'] .= " selected=\"selected\"";
	}
	$displayArray[$dak][$dai]['value'] .= ">".htmlspecialchars($name)."</option>";
}
$displayArray[$dak][$dai]['value'] .= '</select><br />[Hold ctrl key for multiple selection]';
$dai++;
$saveDIArray['mc_DMSource'] = array("checkMediaChannel('1,2')");
$saveDIArray['mc_DMSource'][] = $dak.'_'.$dai;
$displayArray[$dak][$dai]['title'] = '<span class="error">*</span>DM/TM Source';
$displayArray[$dak][$dai]['value'] = '<input type="text" name="DMSource" size="40" maxlength="200" class="input_box" value="'.htmlspecialchars($DMSource,ENT_QUOTES).'"';
if($mChannelID!=1 && $mChannelID!=2) {
	$displayArray[$dak][$dai]['value'] .= ' disabled="disabled"';
}
else {
	$displayArray[$dak][$dai]['value'] .= $disabled;
	if($updID!='' && !$fromtemp && !empty($DMSource)){
		$displayArray[$dak][$dai]['value'] .= ' readonly="readonly"';
	}
	$disfields[] = 'DMSource';
}
$displayArray[$dak][$dai]['value'] .= ' /><input type="hidden" id="dmtmsource" name="dmtmsource" value="'.htmlspecialchars($DMSource,ENT_QUOTES).'" /><input type="hidden" name="consumer_insights" value="'.$consumer_insights.'" /><input type="hidden" name="is_subp" value="'.$is_subp.'" />';
$dai++;
$saveDIArray['mc_pub'] = array("checkMediaChannel('2')");
$saveDIArray['mc_pub'][] = $dak.'_'.$dai;
$displayArray[$dak][$dai]['title'] = '*Publication';

$displayArray[$dak][$dai]['value'] = '';
if(!isset($_GET['muid']) || $fromtemp){
	$displayArray[$dak][$dai]['value'] .= '<a href="#" onclick="showDiv_outer(\'showbox_pubs_outer\',\'addpublink\'); return false;" id="addpublink"';
	if($fromtemp){
		$displayArray[$dak][$dai]['value'] .= ' class="bluelink"';
	}
	$displayArray[$dak][$dai]['value'] .= '>Add Publication</a>';
}
$displayArray[$dak][$dai]['value'] .= '<div id="pubs">';
foreach($pub_printArray as $arry){
	$displayArray[$dak][$dai]['value'] .= "<div id=\"pub{$arry[0]}_$arry[2]\">$arry[1]";
	if(!isset($_GET['muid']) || $fromtemp){ 
		$displayArray[$dak][$dai]['value'] .= " <a href=\"#\"";
		if($fromtemp){
			$displayArray[$dak][$dai]['value'] .= ' class="bluelink"';
		}
		$displayArray[$dak][$dai]['value'] .= " onclick=\"removePub($arry[0],$arry[2]); return false;\">Remove</a>";
	}
	$displayArray[$dak][$dai]['value'] .= "</div>";
}
$displayArray[$dak][$dai]['value'] .= '</div><input type="hidden" name="pub_ids" value="'.$pub_ids.'" /><input type="hidden" name="old_pub_ids" value="'.$old_pub_ids.'" />';
$dai++;
if($responseMechID!="") {
	$responseMechID = explode(",",$responseMechID);
}
else {
	$responseMechID = array();
}
$displayArray[$dak][$dai]['title'] = 'Response Mechanism';
$displayArray[$dak][$dai]['value'] = '<select id="responseMechID" name="responseMechID[]" size="3" multiple="multiple" class="combo_box"'.$disabled.'>';
$disfields[] = 'responseMechID[]';
$query_ac ="SELECT responseMechID,responseMechName,rm_sectorID FROM cscan_response_mechanism ORDER BY responseMechSort";
$result_ac = $DRW->query($query_ac,$DRW_read);
while($row_ac = $DRW->fetch_row($result_ac)){
	$id = $row_ac[0];
	$name = $row_ac[1];
	
	$rm_sectorID_not = array();
	if($row_ac[2]!=''){
		$rm_sectorID = explode(',',$row_ac[2]);
		foreach($rm_sectorID as $k=>$v){
			if(strpos($v,'-')!==false){
				$rm_sectorID_not[] = substr($v,1);
				unset($rm_sectorID[$k]);
			}
		}
	}
	else{
		$rm_sectorID = array();
	}
	
	$show2 = false;
	$javascript .= "depsArrayName['responseMechID[]'][depsArrayName['responseMechID[]'].length] = '".singleQuoteSafe($name)."';\n";
	$javascript .= "depsArrayID['responseMechID[]'][depsArrayID['responseMechID[]'].length] = '$id';\n";
	foreach($scsc as $sid=>$name2){
		if(!in_array($sid,$rm_sectorID_not) && (count($rm_sectorID)==0 || (in_array('C',$rm_sectorID) && in_array($sid,$coreArray)) || (in_array('N',$rm_sectorID) && !in_array($sid,$coreArray)) || in_array($sid,$rm_sectorID))) {
			$javascript .= "depsArrayS['responseMechID[]']['$sid']['$id'] = true;\n";
			if(in_array($sid,$sectorID) || in_array($sid,$categoryID) || in_array($sid,$subCategoryID)) {
				$show2 = true;
			}
		}
	}
	if($show2){
		$displayArray[$dak][$dai]['value'] .= "<option value=\"$id\"";
		if(in_array($id,$responseMechID)) {
			$displayArray[$dak][$dai]['value'] .= " selected=\"selected\"";
		}
		$displayArray[$dak][$dai]['value'] .= ">".htmlspecialchars($name)."</option>";
	}
}
$displayArray[$dak][$dai]['value'] .= '</select>';
$dai++;
$displayArray[$dak][$dai]['title'] = 'Business Content';
$displayArray[$dak][$dai]['value'] = '<select name="businessContent" class="combo_box"'.$disabled.'>';
$disfields[] = 'businessContent';
$valArray = get_businessContentArray();
foreach($valArray as $selvalue=>$seltext){
	$displayArray[$dak][$dai]['value'] .= "<option value=\"$selvalue\"";
	if($businessContent==$selvalue) {
		$displayArray[$dak][$dai]['value'] .= " selected=\"selected\"";
	}
	$displayArray[$dak][$dai]['value'] .= ">".htmlspecialchars($seltext)."</option>";
}
$displayArray[$dak][$dai]['value'] .= '</select>';
$dai++;
$displayArray[$dak][$dai]['title'] = '*Personalization';
$displayArray[$dak][$dai]['value'] = '<label><input type="radio" name="personalization" value="1"';
if($personalization == '1') {
	$displayArray[$dak][$dai]['value'] .= ' checked="checked"';
}
$displayArray[$dak][$dai]['value'] .= $disabled;
$disfields[] = 'personalization';
$displayArray[$dak][$dai]['value'] .= ' />Personalized</label> &nbsp; <label><input type="radio" name="personalization" value="2"';
if($personalization == '2') {
	$displayArray[$dak][$dai]['value'] .= ' checked="checked"';
}
$displayArray[$dak][$dai]['value'] .= $disabled;
$displayArray[$dak][$dai]['value'] .= ' />Non-Personalized</label>';
$dai++;
//$saveDIArray['mp_mTypeID'] = array("checkMediaPanel('1,2')");
//$saveDIArray['mp_mTypeID'][] = $dak.'_'.$dai;
if($fromtemp){
	$displayArray[$dak][$dai]['title'] = '<span class="error">*</span>';
}
else{
	$displayArray[$dak][$dai]['title'] = '*';
}
$displayArray[$dak][$dai]['title'] .= 'Mailing Type';
$displayArray[$dak][$dai]['value'] = '<select class="combo_box" name="mTypeID"'.$disabled.' onchange="checkDeps_mtvariant();"><option value="0"> -- Select One -- </option>';
$disfields[] = 'mTypeID';
$mailing_type = getMailingType();
foreach($mailing_type as $id=>$name ) {
	$displayArray[$dak][$dai]['value'] .= "<option value=\"$id\"";
	if($mTypeID == $id) {
		$displayArray[$dak][$dai]['value'] .= " selected=\"selected\"";
	}
	$displayArray[$dak][$dai]['value'] .= ">".htmlspecialchars($name)."</option>";
}
$displayArray[$dak][$dai]['value'] .= '</select>';
$dai++;
$saveDIArray['mp_delmethid'] = array("checkMediaPanel('1,2') && checkMediaChannel('1')");
$saveDIArray['mp_delmethid'][] = $dak.'_'.$dai;
$saveDIArray['mc_delmethid'] = array("checkMediaPanel('1,2') && checkMediaChannel('1')");
$saveDIArray['mc_delmethid'][] = $dak.'_'.$dai;
if($fromtemp){
	$displayArray[$dak][$dai]['title'] = '<span class="error">*</span>';
}
else{
	$displayArray[$dak][$dai]['title'] = '*';
}
$displayArray[$dak][$dai]['title'] .= 'Delivery Method';
$displayArray[$dak][$dai]['value'] = '<select class="combo_box" name="delmethid"';
//$displayArray[$dak][$dai]['value'] = '<select class="combo_box" name="mChannelID" onchange="getDeps(\'offerOrigin\');markInsuranceexchange();checkChannel();doDelMeth();checkDeps_mc(); showDigitalSource();"'.$disabled.'><option value="0">-- Select One --</option>';
if($mChannelID!=1 || ($mPanelID!=1 && $mPanelID!=2)) {
	$displayArray[$dak][$dai]['value'] .= ' disabled="disabled"';
}
else {
	$displayArray[$dak][$dai]['value'] .= $disabled;
	$disfields[] = 'delmethid';
}
$displayArray[$dak][$dai]['value'] .= 'onchange="doEnvelopePostageData();checkDeps_EnvelopePostageData();"'.$disabled.'><option value="0"> -- Select One -- </option>';
$delmeth = getDeliveryMethod();
foreach($delmeth as $id=>$name ) {
	$displayArray[$dak][$dai]['value'] .= "<option value=\"$id\"";
	if($delmethid == $id) {
		$displayArray[$dak][$dai]['value'] .= " selected=\"selected\"";
	}
	$displayArray[$dak][$dai]['value'] .= ">".htmlspecialchars($name)."</option>";
}
$displayArray[$dak][$dai]['value'] .= '</select>';
$dai++;

############################## Start Envelope/Postage Data Fields##################
$saveDIArray['mp_deliveryTypeId'] = array("checkMediaPanel('1,2') && checkMediaChannel('1') && checkDeliveryMethod('1,3,7')");
$saveDIArray['mp_deliveryTypeId'][] = $dak.'_'.$dai;
$saveDIArray['mc_deliveryTypeId'] = array("checkMediaPanel('1,2') && checkMediaChannel('1') && checkDeliveryMethod('1,3,7')");
$saveDIArray['mc_deliveryTypeId'][] = $dak.'_'.$dai;
$saveDIArray['dm_deliveryTypeId'] = array("checkMediaPanel('1,2') && checkMediaChannel('1') && checkDeliveryMethod('1,3,7')");
$saveDIArray['dm_deliveryTypeId'][] = $dak.'_'.$dai;
if($fromtemp){
	$displayArray[$dak][$dai]['title'] = '<span class="error">*</span>';
}
else{
	$displayArray[$dak][$dai]['title'] = '*';
}
$displayArray[$dak][$dai]['title'] .= 'Delivery Type';
$displayArray[$dak][$dai]['value'] = '<select class="combo_box" name="deliveryTypeId"';
if($mChannelID!=1 || ($mPanelID!=1 && $mPanelID!=2)) {
	$displayArray[$dak][$dai]['value'] .= ' disabled="disabled"';
}
else {
	$displayArray[$dak][$dai]['value'] .= $disabled;
	$disfields[] = 'deliveryTypeId';
}
$displayArray[$dak][$dai]['value'] .= '><option value="0"> -- Select One -- </option>';
$deliveryType = getDeliveryType();
foreach($deliveryType as $id=>$name ) {
	$displayArray[$dak][$dai]['value'] .= "<option value=\"$id\"";
	if($deliveryTypeId == $id) {
		$displayArray[$dak][$dai]['value'] .= " selected=\"selected\"";
	}
	$displayArray[$dak][$dai]['value'] .= ">".htmlspecialchars($name)."</option>";
}
$displayArray[$dak][$dai]['value'] .= '</select>';
$dai++;


// add Postage 
$saveDIArray['mp_postageId'] = array("checkMediaPanel('1,2') && checkMediaChannel('1') && checkDeliveryMethod('1,3,7')");
$saveDIArray['mp_postageId'][] = $dak.'_'.$dai;
$saveDIArray['mc_postageId'] = array("checkMediaPanel('1,2') && checkMediaChannel('1') && checkDeliveryMethod('1,3,7')");
$saveDIArray['mc_postageId'][] = $dak.'_'.$dai;
$saveDIArray['dm_postageId'] = array("checkMediaPanel('1,2') && checkMediaChannel('1') && checkDeliveryMethod('1,3,7')");
$saveDIArray['dm_postageId'][] = $dak.'_'.$dai;
if($fromtemp){
	$displayArray[$dak][$dai]['title'] = '<span class="error">*</span>';
}
else{
	$displayArray[$dak][$dai]['title'] = '*';
}
$displayArray[$dak][$dai]['title'] .= 'Postage';
$displayArray[$dak][$dai]['value'] = '<select class="combo_box" name="postageId"';
if($mChannelID!=1 || ($mPanelID!=1 && $mPanelID!=2)) {
	$displayArray[$dak][$dai]['value'] .= ' disabled="disabled"';
}
else {
	$displayArray[$dak][$dai]['value'] .= $disabled;
	$disfields[] = 'postageId';
}
$displayArray[$dak][$dai]['value'] .= '><option value="0"> -- Select One -- </option>';
$postage = getPostage();
foreach($postage as $id=>$name ) {
	$displayArray[$dak][$dai]['value'] .= "<option value=\"$id\"";
	if($postageId == $id) {
		$displayArray[$dak][$dai]['value'] .= " selected=\"selected\"";
	}
	$displayArray[$dak][$dai]['value'] .= ">".htmlspecialchars($name)."</option>";
}
$displayArray[$dak][$dai]['value'] .= '</select>';
$dai++;


// add Pre-Sorted 
$saveDIArray['mp_presortedId'] = array("checkMediaPanel('1,2') && checkMediaChannel('1') && checkDeliveryMethod('1,3,7')");
$saveDIArray['mp_presortedId'][] = $dak.'_'.$dai;
$saveDIArray['mc_presortedId'] = array("checkMediaPanel('1,2') && checkMediaChannel('1') && checkDeliveryMethod('1,3,7')");
$saveDIArray['mc_presortedId'][] = $dak.'_'.$dai;
$saveDIArray['dm_presortedId'] = array("checkMediaPanel('1,2') && checkMediaChannel('1') && checkDeliveryMethod('1,3,7')");
$saveDIArray['dm_presortedId'][] = $dak.'_'.$dai;
if($fromtemp){
	$displayArray[$dak][$dai]['title'] = '<span class="error">*</span>';
}
else{
	$displayArray[$dak][$dai]['title'] = '*';
}
$displayArray[$dak][$dai]['title'] .= 'Pre-Sorted';
$displayArray[$dak][$dai]['value'] = '<select class="combo_box" name="presortedId"';
if($mChannelID!=1 || ($mPanelID!=1 && $mPanelID!=2)) {
	$displayArray[$dak][$dai]['value'] .= ' disabled="disabled"';
}
else {
	$displayArray[$dak][$dai]['value'] .= $disabled;
	$disfields[] = 'presortedId';
}
$displayArray[$dak][$dai]['value'] .= '><option value="0"> -- Select One -- </option>';
$presorted = getPresorted();
foreach($presorted as $id=>$name ) {
	$displayArray[$dak][$dai]['value'] .= "<option value=\"$id\"";
	if($presortedId == $id) {
		$displayArray[$dak][$dai]['value'] .= " selected=\"selected\"";
	}
	$displayArray[$dak][$dai]['value'] .= ">".htmlspecialchars($name)."</option>";
}
$displayArray[$dak][$dai]['value'] .= '</select>';
$dai++;


// add Package Type 
$saveDIArray['mp_packageTypeId'] = array("checkMediaPanel('1,2') && checkMediaChannel('1') && checkDeliveryMethod('1,3,7')");
$saveDIArray['mp_packageTypeId'][] = $dak.'_'.$dai;
$saveDIArray['mc_packageTypeId'] = array("checkMediaPanel('1,2') && checkMediaChannel('1') && checkDeliveryMethod('1,3,7')");
$saveDIArray['mc_packageTypeId'][] = $dak.'_'.$dai;
$saveDIArray['dm_packageTypeId'] = array("checkMediaPanel('1,2') && checkMediaChannel('1') && checkDeliveryMethod('1,3,7')");
$saveDIArray['dm_packageTypeId'][] = $dak.'_'.$dai;
if($fromtemp){
	$displayArray[$dak][$dai]['title'] = '<span class="error">*</span>';
}
else{
	$displayArray[$dak][$dai]['title'] = '*';
}
$displayArray[$dak][$dai]['title'] .= 'Package Type';
$displayArray[$dak][$dai]['value'] = '<select class="combo_box" name="packageTypeId"';
if($mChannelID!=1 || ($mPanelID!=1 && $mPanelID!=2)) {
	$displayArray[$dak][$dai]['value'] .= ' disabled="disabled"';
}
else {
	$displayArray[$dak][$dai]['value'] .= $disabled;
	$disfields[] = 'packageTypeId';
}
$displayArray[$dak][$dai]['value'] .= '><option value="0"> -- Select One -- </option>';
$packageType = getPackageType();
foreach($packageType as $id=>$name ) {
	$displayArray[$dak][$dai]['value'] .= "<option value=\"$id\"";
	if($packageTypeId == $id) {
		$displayArray[$dak][$dai]['value'] .= " selected=\"selected\"";
	}
	$displayArray[$dak][$dai]['value'] .= ">".htmlspecialchars($name)."</option>";
}
$displayArray[$dak][$dai]['value'] .= '</select>';
$dai++;


############################## End Envelope/Postage Data Fields##################

$saveDIArray['s_riders'] = array("checkSector('4,5')");
$saveDIArray['s_riders'][] = $dak.'_'.$dai;
$displayArray[$dak][$dai]['title'] = 'Riders';
$displayArray[$dak][$dai]['value'] = '<select name="riders[]" multiple="multiple" size="5" class="combo_box"'.$disabled.'>';
$disfields[] = 'riders[]';
if($riders!="") {
	$riders = explode(",",$riders);
}
else {
	$riders = array();
}
$query_ac ="SELECT ridersID,ridersName,riders_sectorID FROM cscan_riders ORDER BY ridersSort";
$result_ac = $DRW->query($query_ac,$DRW_read);
while($row_ac = $DRW->fetch_row($result_ac)){
	$id = $row_ac[0];
	$name = $row_ac[1];
	
	$rm_sectorID_not = array();
	if($row_ac[2]!=''){
		$rm_sectorID = explode(',',$row_ac[2]);
		foreach($rm_sectorID as $k=>$v){
			if(strpos($v,'-')!==false){
				$rm_sectorID_not[] = substr($v,1);
				unset($rm_sectorID[$k]);
			}
		}
	}
	else{
		$rm_sectorID = array();
	}
	
	$show2 = false;
	$javascript .= "depsArrayName['riders[]'][depsArrayName['riders[]'].length] = '".singleQuoteSafe($name)."';\n";
	$javascript .= "depsArrayID['riders[]'][depsArrayID['riders[]'].length] = '$id';\n";
	foreach($scsc as $sid=>$name2){
		if(!in_array($sid,$rm_sectorID_not) && (count($rm_sectorID)==0 || (in_array('C',$rm_sectorID) && in_array($sid,$coreArray)) || (in_array('N',$rm_sectorID) && !in_array($sid,$coreArray)) || in_array($sid,$rm_sectorID))) {
			$javascript .= "depsArrayS['riders[]']['$sid']['$id'] = true;\n";
			if(in_array($sid,$sectorID) || in_array($sid,$categoryID) || in_array($sid,$subCategoryID)) {
				$show2 = true;
			}
		}
	}
	if($show2){
		$displayArray[$dak][$dai]['value'] .= "<option value=\"$id\"";
		if(in_array($id,$riders)) {
			$displayArray[$dak][$dai]['value'] .= " selected=\"selected\"";
		}
		$displayArray[$dak][$dai]['value'] .= ">".htmlspecialchars($name)."</option>";
	}
}
$displayArray[$dak][$dai]['value'] .= '</select><br />[Hold ctrl key for multiple selection]';
$dai++;

$displayArray[$dak][$dai]['title'] = 'Communication Type';
$displayArray[$dak][$dai]['value'] = '<select class="combo_box" name="agentCommunicationID[]" multiple="multiple" id="agentCommunicationID" onchange="checkDeps_ct();" size="5"'.$disabled.'>';
$disfields[] = 'agentCommunicationID[]';
// onchange="check_insurance();"
if($agentCommunicationID!="") {
	$agentCommunicationID = explode(",",$agentCommunicationID);
}
else {
	$agentCommunicationID = array();
}
$query_ac ="SELECT ID,type,ac_mPanelID,ac_sectorID FROM cscan_agent_communication ORDER BY type";
$result_ac = $DRW->query($query_ac,$DRW_read);
while($row_ac = $DRW->fetch_row($result_ac)){
	$id = $row_ac[0];
	$name = $row_ac[1];
	
	if($id==22) continue;////Charity/Fundraiser
	
	if($row_ac[2]!=''){
		$ac_mPanelIDArray = explode(',',$row_ac[2]);
	}
	else{
		$ac_mPanelIDArray = array();
	}
	$ac_sectorID_not = array();
	if($row_ac[3]!=''){
		$ac_sectorID = explode(',',$row_ac[3]);
		foreach($ac_sectorID as $k=>$v){
			if(strpos($v,'-')!==false){
				$ac_sectorID_not[] = substr($v,1);
				unset($ac_sectorID[$k]);
			}
		}
	}
	else{
		$ac_sectorID = array();
	}
	$show1 = false;
	$show2 = false;
	$javascript .= "depsArrayName['agentCommunicationID[]'][depsArrayName['agentCommunicationID[]'].length] = '".singleQuoteSafe($name)."';\n";
	$javascript .= "depsArrayID['agentCommunicationID[]'][depsArrayID['agentCommunicationID[]'].length] = '$id';\n";
	foreach($mailing_panel as $mpid=>$mpname){
		if(count($ac_mPanelIDArray)==0 || in_array($mpid,$ac_mPanelIDArray)) {
                    ################## update for hide Audience Mortgage Broker #############
//                    if($row_ac[2]=='' && $row_ac[3]=='' && $mpid=='6'){
//                        continue;
//                    }
                   ################## end update for hide Audience Mortgage Broker #############
			$javascript .= "depsArray['agentCommunicationID[]']['$mpid']['$id'] = true;\n";
			if($mpid==$mPanelID){
				$show1 = true;
			}
		}
	}
	foreach($scsc as $sid=>$name2){
		if(!in_array($sid,$ac_sectorID_not) && (count($ac_sectorID)==0 || (in_array('C',$ac_sectorID) && in_array($sid,$coreArray)) || (in_array('N',$ac_sectorID) && !in_array($sid,$coreArray)) || in_array($sid,$ac_sectorID))) {
			$javascript .= "depsArrayS['agentCommunicationID[]']['$sid']['$id'] = true;\n";
			if(in_array($sid,$sectorID) || in_array($sid,$categoryID) || in_array($sid,$subCategoryID)) {
				$show2 = true;
			}
		}
	}

	###########  Communication Type Implementation ############
	/*if(ENV=='demo.competiscan.com' || ENV=='localhost'){

	}else{
	    if((in_array(5,$agentCommunicationID)|| in_array(4,$ac_sectorID)  || in_array(5,$ac_sectorID) || in_array(315,$ac_sectorID)) && $id==5){
            $show1 = true;
            $show2 = true;
        }

		if($show1 && $show2){
			$displayArray[$dak][$dai]['value'] .= "<option value=\"$id\"";
			if(in_array($id,$agentCommunicationID)) {
				$displayArray[$dak][$dai]['value'] .= " selected=\"selected\"";
			}
			$displayArray[$dak][$dai]['value'] .= ">".htmlspecialchars($name)."</option>";
		}
	}*/
	###########  Communication Type Implementation ############
}
$displayArray[$dak][$dai]['value'] .= '</select><br />[Hold ctrl key for multiple selection]';
$dai++;
$is_noncore = false;
foreach($sectorID as $sid){
	if(!in_array($sid,$coreArray)){
		$is_noncore = true;
		break;
	}
}
$saveDIArray['ct_incentive'] = array("checkCommunicationType('1')");
$saveDIArray['ct_incentive'][] = $dak.'_'.$dai;
$displayArray[$dak][$dai]['title'] = '<span id="so_incentive"';
if(!$is_noncore){
	$displayArray[$dak][$dai]['title'] .= ' style="display:none;"';
}
$displayArray[$dak][$dai]['title'] .= '>Sign-on </span>Incentive #1';
$displayArray[$dak][$dai]['value'] = '<textarea name="incentive" rows="5" cols="60" class="input_box"'.$disabled.'>'.htmlspecialchars($incentive,ENT_QUOTES).'</textarea>';
$disfields[] = 'incentive';
$dai++;

$saveDIArray['ct_incentive'][] = $dak.'_'.$dai;
$displayArray[$dak][$dai]['title'] = 'Ongoing Incentive';
$displayArray[$dak][$dai]['value'] = '<textarea name="incentive_ongoing" rows="5" cols="60" class="input_box"';
if(!$is_noncore) {
	$displayArray[$dak][$dai]['value'] .= ' disabled="disabled"';
}
else {
	$displayArray[$dak][$dai]['value'] .= $disabled;
	$disfields[] = 'incentive_ongoing';
}
$displayArray[$dak][$dai]['value'] .= '>'.htmlspecialchars($incentive_ongoing,ENT_QUOTES).'</textarea>';
$dai++;

$mintel = new \HS\Mintel();
$form_fields = new \HS\Form();

$mintel_credit_fields = $mintel->getFields();
$yesno_field = $mintel->getYesNo();

foreach ($mintel_credit_fields as $field_name => $field_specs) {
    $saveDIArray['ct_incentive'][] = $dak.'_'.$dai;
    $displayArray[$dak][$dai]['title'] = $field_specs['display'];
    $field_value = (isset(${$field_name})) ? ${$field_name} : '';

    if ($field_specs['type'] == 'boolean') {
        $displayArray[$dak][$dai]['value'] = $form_fields->radio($field_name, (int)$field_value, $yesno_field);
    } elseif ($field_specs['type'] == 'integer') {
        $displayArray[$dak][$dai]['value'] = $form_fields->input($field_name, $field_value);
    } elseif ($field_specs['type'] == 'dropdown') {
        $options = $mintel->getDropdown($field_name);
        $displayArray[$dak][$dai]['value'] = $form_fields->dropdown($field_name, $field_value, $options);
    } else {
        $displayArray[$dak][$dai]['value'] = $form_fields->text($field_name, $field_value);
    }

    $dai++;
}

$displayArray[$dak][$dai]['title'] = '&nbsp;';
$displayArray[$dak][$dai]['value'] = '<span onclick="showIncentiveExtras(2)" class="incentive_extra_set" id="incentive_set_2" style="cursor:pointer;">Add sign-on incentive #2 details</span>';
$dai++;

$mintel_set_2 = $mintel->getFieldSet('incentive_set_2');

foreach ($mintel_set_2 as $field_name => $field_specs) {
    $saveDIArray['ct_incentive'][] = $dak.'_'.$dai;
    $field_value = (isset(${$field_name})) ? ${$field_name} : '';

    $displayArray[$dak][$dai]['title'] = $field_specs['display'];

    if ($field_specs['type'] == 'boolean') {
        $displayArray[$dak][$dai]['value'] = $form_fields->radio($field_name, (int)$field_value, $yesno_field, 'input_box incentive_fields_2');
    } elseif ($field_specs['type'] == 'integer') {
        $displayArray[$dak][$dai]['value'] = $form_fields->input($field_name, $field_value, 'text', 'input_box incentive_fields_2');
    } elseif ($field_specs['type'] == 'dropdown') {
        $options = $mintel->getDropdown($field_name);
        $displayArray[$dak][$dai]['value'] = $form_fields->dropdown($field_name, $field_value, $options, 'input_box incentive_fields_2');
    } else {
        $displayArray[$dak][$dai]['value'] = $form_fields->text($field_name, $field_value, 'input_box incentive_fields_2');
    }

    $dai++;
}

$displayArray[$dak][$dai]['title'] = '&nbsp;';
$displayArray[$dak][$dai]['value'] = '<span onclick="showIncentiveExtras(3)" class="incentive_extra_set" id="incentive_set_3" style="cursor:pointer;">Add sign-on incentive #3 details</span>';
$dai++;

$mintel_set_3 = $mintel->getFieldSet('incentive_set_3');

foreach ($mintel_set_3 as $field_name => $field_specs) {
    $saveDIArray['ct_incentive'][] = $dak.'_'.$dai;
    $field_value = (isset(${$field_name})) ? ${$field_name} : '';

    $displayArray[$dak][$dai]['title'] = $field_specs['display'];

    if ($field_specs['type'] == 'boolean') {
        $displayArray[$dak][$dai]['value'] = $form_fields->radio($field_name, (int)$field_value, $yesno_field, 'input_box incentive_fields_3');
    } elseif ($field_specs['type'] == 'integer') {
        $displayArray[$dak][$dai]['value'] = $form_fields->input($field_name, $field_value, 'text', 'input_box incentive_fields_3');
    } elseif ($field_specs['type'] == 'dropdown') {
        $options = $mintel->getDropdown($field_name);
        $displayArray[$dak][$dai]['value'] = $form_fields->dropdown($field_name, $field_value, $options, 'input_box incentive_fields_3');
    } else {
        $displayArray[$dak][$dai]['value'] = $form_fields->text($field_name, $field_value, 'input_box incentive_fields_3');
    }

    $dai++;
}

$displayArray[$dak][$dai]['title'] = 'Campaign Language';
$displayArray[$dak][$dai]['value'] = '<select class="combo_box" name="compaignLanguage"'.$disabled.'>';
$disfields[] = 'compaignLanguage';
$compaignLanguageArray = array('English'=>'English','Bilingual'=>'Bilingual');
foreach($compaignLanguageArray as $cl){
	$displayArray[$dak][$dai]['value'] .= "<option value=\"$cl\"";
	if($cl==$compaignLanguage) {
		$displayArray[$dak][$dai]['value'] .= " selected=\"selected\"";
	}
	$displayArray[$dak][$dai]['value'] .= ">".htmlspecialchars($cl)."</option>";
}
$displayArray[$dak][$dai]['value'] .= '</select>';
$dai++;
$displayArray[$dak][$dai]['title'] = 'Target Markets';
$displayArray[$dak][$dai]['value'] = '<select name="multiculturalmarkets[]" multiple="multiple" size="5" class="combo_box"'.$disabled.'>';
$disfields[] = 'multiculturalmarkets[]';
if($multiculturalmarkets!="") {
	$multiculturalmarkets = explode(",",$multiculturalmarkets);
}
else {
	$multiculturalmarkets = array();
}
$query_ac ="SELECT tm_id,tm_name,tm_sectorID FROM cscan_target_market ORDER BY tm_name";
$result_ac = $DRW->query($query_ac,$DRW_read);
while($row_ac = $DRW->fetch_row($result_ac)){
	$id = $row_ac[0];
	$name = $row_ac[1];
	$ac_sectorID_not = array();
	if($row_ac[2]!=''){
		$ac_sectorID = explode(',',$row_ac[2]);
		foreach($ac_sectorID as $k=>$v){
			if(strpos($v,'-')!==false){
				$ac_sectorID_not[] = substr($v,1);
				unset($ac_sectorID[$k]);
			}
		}
	}
	else{
		$ac_sectorID = array();
	}
	$show2 = false;
	$javascript .= "depsArrayName['multiculturalmarkets[]'][depsArrayName['multiculturalmarkets[]'].length] = '".singleQuoteSafe($name)."';\n";
	$javascript .= "depsArrayID['multiculturalmarkets[]'][depsArrayID['multiculturalmarkets[]'].length] = '$id';\n";
	foreach($scsc as $sid=>$name2){
		if(!in_array($sid,$ac_sectorID_not) && (count($ac_sectorID)==0 || (in_array('C',$ac_sectorID) && in_array($sid,$coreArray)) || (in_array('N',$ac_sectorID) && !in_array($sid,$coreArray)) || in_array($sid,$ac_sectorID))) {
			$javascript .= "depsArrayS['multiculturalmarkets[]']['$sid']['$id'] = true;\n";
			if(count($ac_sectorID)==0 || in_array($sid,$sectorID) || in_array($sid,$categoryID) || in_array($sid,$subCategoryID)) {
				$show2 = true;
			}
		}
	}
	if($show2){
		$displayArray[$dak][$dai]['value'] .= "<option value=\"$id\"";
		if(in_array($id,$multiculturalmarkets)) {
			$displayArray[$dak][$dai]['value'] .= " selected=\"selected\"";
		}
		$displayArray[$dak][$dai]['value'] .= ">".htmlspecialchars($name)."</option>";
	}
}
$displayArray[$dak][$dai]['value'] .= '</select><br />[Hold ctrl key for multiple selection]';
$dai++;
if($fromtemp){
	$displayArray[$dak][$dai]['title'] = '<span class="error">*</span>';
}
else{
	$displayArray[$dak][$dai]['title'] = '*';
}
$displayArray[$dak][$dai]['title'] .= 'State/Province';
$displayArray[$dak][$dai]['value'] = '<select name="state[]" class="combo_box" multiple="multiple" size="5"'.$disabled.' onchange="checkCompanyFields(\'state\');">';
$disfields[] = 'state[]';
$displayArray[$dak][$dai]['value'] .= '<option value="0"';
if($state=='0') {
	$displayArray[$dak][$dai]['value'] .= " selected=\"selected\"";
}
$displayArray[$dak][$dai]['value'] .= '>Any</option>';
ob_start();
getStates($state);
$displayArray[$dak][$dai]['value'] .= ob_get_clean();
$displayArray[$dak][$dai]['value'] .= '</select><input type="hidden" name="co_states" value="'.$co_states.'" /><br />[Hold ctrl key for multiple selection]';
$dai++;
$saveDIArray['mc_primary_country'] = array("checkMediaChannel('2,5,6,7')");
$saveDIArray['mc_primary_country'][] = $dak.'_'.$dai;
$displayArray[$dak][$dai]['title'] = 'Primary Country';
$displayArray[$dak][$dai]['value'] = '<select name="primary_country" class="combo_box"'.$disabled.'>';
$disfields[] = 'primary_country';
$displayArray[$dak][$dai]['value'] .= '<option value=""';
if($primary_country=='') {
	$displayArray[$dak][$dai]['value'] .= " selected=\"selected\"";
}
$displayArray[$dak][$dai]['value'] .= '>&nbsp;</option>';
$sqlc = "SELECT DISTINCT countryCode,country FROM cscan_state JOIN ISO31661_alpha2code ON (code=countryCode) ORDER BY country";
$rsc = $DRW->query( $sqlc,$DRW_read );
while($rowc = $DRW->fetch_row($rsc) ) {
	$id = $rowc[0];
	$name = $rowc[1];
	$displayArray[$dak][$dai]['value'] .= "<option value=\"$id\"";
	if($id==$primary_country) {
		$displayArray[$dak][$dai]['value'] .= " selected=\"selected\"";
	}
	$displayArray[$dak][$dai]['value'] .= ">".htmlspecialchars($name)."</option>";
}
$displayArray[$dak][$dai]['value'] .= '</select>';
$dai++;
$saveDIArray['manageDemog'] = array("document.prodForm.manageDemog.value==1");
$displayArray[$dak][$dai]['title'] = '';
$displayArray[$dak][$dai]['value'] = '<strong><a href="#" onclick="if(document.prodForm.manageDemog.value==1){document.prodForm.manageDemog.value=0;}else{document.prodForm.manageDemog.value=1;}checkDeps_manageDemog();return false;">Manage Demographics Information</a></strong><input type="hidden" name="manageDemog" value="0" />';
$dai++;
$saveDIArray['manageDemog'][] = $dak.'_'.$dai;
$displayArray[$dak][$dai]['title'] = 'Gender';
$displayArray[$dak][$dai]['value'] = '<label><input type="radio" name="gender" value="M"';
if($gender == 'M') {
	$displayArray[$dak][$dai]['value'] .= ' checked="checked"';
}
$displayArray[$dak][$dai]['value'] .= $disabled;
$disfields[] = 'gender';
$displayArray[$dak][$dai]['value'] .= ' />Male</label> &nbsp; <label><input type="radio" name="gender" value="F"';
if($gender == 'F') {
	$displayArray[$dak][$dai]['value'] .= ' checked="checked"';
}
$displayArray[$dak][$dai]['value'] .= $disabled;
$displayArray[$dak][$dai]['value'] .= ' />Female</label> &nbsp; <label><input type="radio" name="gender" value="B"';
if($gender == 'B') { 
	$displayArray[$dak][$dai]['value'] .= ' checked="checked"';
}
$displayArray[$dak][$dai]['value'] .= $disabled;
$displayArray[$dak][$dai]['value'] .= ' />Both</label> &nbsp; <label><input type="radio" name="gender" value="N"';
if($gender == 'N') { 
	$displayArray[$dak][$dai]['value'] .= ' checked="checked"';
}
$displayArray[$dak][$dai]['value'] .= $disabled;
$displayArray[$dak][$dai]['value'] .= ' />None</label>';
$dai++;
$saveDIArray['manageDemog'][] = $dak.'_'.$dai;
$displayArray[$dak][$dai]['title'] = 'Age';
$displayArray[$dak][$dai]['value'] = '<select name="age[]" multiple="multiple" size="5" class="combo_box"'.$disabled.'>';
$disfields[] = 'age[]';
if($age!="") {
	$age = explode(",",$age);
}
else {
	$age = array();
}
$ageArray = array();
// Administrative users need to have all the groups populate always, hence why I took out the old getAgeTypes() function. - Tyler
$sql = "SELECT age_pID,age_pname FROM cscan_age_product ORDER BY age_psort";
$result = $DRW->query( $sql,$DRW_read );
if( $DRW->num_rows( $result ) > 0 ){
	while( $row = $DRW->fetch_row( $result ) ){
		$ageArray[$row[0]] = $row[1];
	}
}
foreach($ageArray as $i=>$name) {
	$displayArray[$dak][$dai]['value'] .= "<option value=\"$i\"";
	if(in_array($i,$age)){
		$displayArray[$dak][$dai]['value'] .= ' selected="selected"';
	}
	$displayArray[$dak][$dai]['value'] .= '>'.htmlspecialchars($name).'</option>';
}
$displayArray[$dak][$dai]['value'] .= '</select><br />[Hold ctrl key for multiple selection]';
$dai++;
$saveDIArray['manageDemog'][] = $dak.'_'.$dai;
$displayArray[$dak][$dai]['title'] = 'Income';
$displayArray[$dak][$dai]['value'] = '<select name="incomeID[]" multiple="multiple" size="5" class="combo_box"'.$disabled.'>';
$disfields[] = 'incomeID[]';
if($incomeID!="") {
	$incomeID = explode(",",$incomeID);
}
else {
	$incomeID = array();
}
$displayArray[$dak][$dai]['value'] .= '<option value="0"';
if(in_array('0',$incomeID)) {
	$displayArray[$dak][$dai]['value'] .= " selected=\"selected\"";
}
$displayArray[$dak][$dai]['value'] .= '>Any</option>';
$income_type = getIncomeTypes();
foreach($income_type as $id=>$name ) {
	$displayArray[$dak][$dai]['value'] .= "<option value=\"$id\"";
	if(in_array($id,$incomeID)) {
		$displayArray[$dak][$dai]['value'] .= " selected=\"selected\"";
	}
	$displayArray[$dak][$dai]['value'] .= ">".htmlspecialchars($name)."</option>";
}
$displayArray[$dak][$dai]['value'] .= '</select><br />[Hold ctrl key for multiple selection]';
$dai++;

$dependsArray = array(
	'RewardsProgram'=>array('RewardsProgramEmphasis[]','MultipleRedemptionOptions','RedemptionOptionsAvailable','RewardsEarnBasis','RewardsRate','RewardsRate2','RewardsRate3','RewardsRateDetail','BonusRewards','BonusRewardsRate1','BonusRewardsRate2','BonusRewardsRate3','BonusRewardsDetail','BonusRewardsMonthlySpend','BonusCategories','BonusCategoriesTimePeriod','CreditDebitCombinedEarn','RegularOngoingBonuses','RewardsProgram1Emphasis[]','BonusRewardsTimePeriod','OtherAnnualBonuses','FirstUseBonus','OtherSignOnIncentive','LimitedTimeBonusRate','LimitedTimeBonusRateCategories','LimitedTimeBonusRatePeriod','FirstYearAnnualFeeWaiver'),
	'PromotionalOffer'=>array('PromotionalOfferAPR','BalanceTransferIntroductoryAPR_CAC2','BalanceTransferIntroductoryPeriod_CAC1','BalanceTransferIntroductoryPeriod_CAC2','PromotionalOfferUsageFee','PromotionalOfferMinimumFee','PromotionalOfferMaximumFee','BalanceTransferIntroductoryFeePeriod_CAC','CashAdvanceIntroductoryAPR_CAC','CashAdvanceIntroductoryPeriod_CAC','CashAdvanceIntroductoryUsageFee_CAC','CashAdvanceIntroductoryMinimumFee_CAC','CashAdvanceIntroductoryMaximumFee_CAC','CashAdvanceIntroductoryFeePeriod_CAC','PurchaseIntroductoryAPR_CAC','PurchaseIntroductoryPeriod_CAC','Tier2CashAdvanceIntroductoryAPR_CAC','Tier2CashAdvanceIntroductoryAPRPeriod_CAC','Tier2PurchaseIntroductoryAPR_CAC','Tier2PurchaseIntroductoryPeriod_CAC','PurchaseIntroductoryUsageFee_CAC','PurchaseIntroductoryMinimumFee_CAC','PurchaseIntroductoryMaximumFee_CAC','PurchaseIntroductoryFeePeriod_CAC'),
	'DebitCardMentioned'=>array('BankingCardNetwork[]'),
	'BankingRewardsProgram'=>array('BankingRewardsProgramEmphasis[]','BankingMultipleRedemptionOptions','BankingRedemptionOptionsAvailable','BankingRewardsRate','BankingRewardsRate2','BankingRewardsRate3','BankingRewardsRateDetail','BankingBonusRewards','BankingBonusRewardsRate1','BankingBonusRewardsDetail','BankingBonusRewardsMonthlySpend','BankingBonusCategories','BankingBonusCategoriesTimePeriod','BankingCreditDebitCombinedEarn','BankingFirstUseBonus','BankingOtherSignOnIncentive','BankingLimitedTimeBonusRate','BankingLimitedTimeBonusRateCategories','BankingLimitedTimeBonusRatePeriod','BankingBonusRewardsTimePeriod','BankingOtherAnnualBonuses','BankingRewardsProgram1Emphasis[]'),
);
//these are also in dependsSector (addProductInclude3)
$dependsArray2 = array(
	'FreeChecking'=>array($subCategoryID,array(88)),
	'Checking_APR'=>array($subCategoryID,array(88)),
	'Checking_APY'=>array($subCategoryID,array(88)),
	'Savings_APR'=>array($subCategoryID,array(89)),
	'Savings_APY'=>array($subCategoryID,array(89)),
	'MoneyMarket_APR'=>array($subCategoryID,array(100)),
	'MoneyMarket_APY'=>array($subCategoryID,array(100)),
	'CD_APR'=>array($subCategoryID,array(189)),
	'CD_APY'=>array($subCategoryID,array(189)),
	'InstallationCharge'=>array($categoryID,array(186,94,187,185)),
	'LocalCallingMonthlyCost'=>array($categoryID,array(94)),
	'LongDistanceMonthlyCost'=>array($categoryID,array(94)),
	'Reloadable'=>array($subCategoryID,array(103)),
);
foreach($addlArray as $o){
	$o->doReset();
	$dak = (string)$o->id;
	$displayArray[$dak] = array();
	$displayKeys[$dak] = htmlspecialchars($o->label);

	while($o->getNext()){
		$displayArray[$dak][$dai]['title'] = htmlspecialchars($o->getTitle());
		$field = $o->getField();
		if($field==''){
			$displayArray[$dak][$dai]['value'] = '';
		}
		else{
			$type = $o->getType();
			$value = $$field;
			if($type==6){
				$field .= '[]';
			}
			$extra = '';
			if(isset($dependsArray[$field])){
				$extra .= ' onclick="depends(\''.implode(',',$dependsArray[$field]).'\',\'!document.prodForm.'.$field.'.checked\');"';
			}
			$not = false;
			if(isset($dependsArray2[$field])){
				$not = true;
				list($check,$dids) = $dependsArray2[$field];
				foreach($dids as $did){
					if(in_array($did,$check)) {
						$not = false;
						break;
					}
				}
			}
			else{
				foreach($dependsArray as $f=>$checks){
					if(in_array($field,$checks)){
						$not = true;
						if(!empty($$f)){
							$not = false;
							break;
						}
					}
				}
			}
			if($field=='ServicePlanType[]'){
				$extra .= ' onchange="doServicePlanType();"';
			}
			if($field=='InternetMbps' && !in_array(1,explode(',',$ServicePlanType))){
				$not = true;
			}
			if($field=='HDTV' && !in_array(2,explode(',',$ServicePlanType))){
				$not = true;
			}
			if($not) {
				$extra .= ' disabled="disabled"';
			}
			else {
				$extra .= $disabled;
				$disfields[] = $field;
			}
			// CashAdvanceIntroductoryFeeDetail, BalanceTransferIntroductoryFeeDetail = [ACTUAL TEXT FROM PIECE, EXACTLY AS IT APPEARS ON THE PIECE]
			
			$displayArray[$dak][$dai]['value'] = $o->doFormHTML($field,$value,$extra);
		}
		$dai++;
	}
}

$dak = 'bottom';
$displayArray[$dak] = array();
$displayKeys[$dak] = '';

$displayArray[$dak][$dai]['title'] = 'CITI';
$displayArray[$dak][$dai]['value'] = '<label><input type="radio" name="is_citi" value="1"';
if($is_citi=='1') {
	$displayArray[$dak][$dai]['value'] .= ' checked="checked"';
}
$displayArray[$dak][$dai]['value'] .= $disabled.' />Yes</label> &nbsp; <label><input type="radio" name="is_citi" value="0"';
if($is_citi=='0'){
	$displayArray[$dak][$dai]['value'] .= ' checked="checked"';
}
$displayArray[$dak][$dai]['value'] .= $disabled.' />No</label>';
$disfields[] = 'is_citi';
$dai++;
$displayArray[$dak][$dai]['title'] = 'On Home Page';
$displayArray[$dak][$dai]['value'] = '<label><input type="radio" name="homePageFlag" value="1"';
if($homePageFlag=='1') {
	$displayArray[$dak][$dai]['value'] .= ' checked="checked"';
}
$displayArray[$dak][$dai]['value'] .= $disabled.' />Yes</label> &nbsp; <label><input type="radio" name="homePageFlag" value="0"';
if($homePageFlag=='0'){
	$displayArray[$dak][$dai]['value'] .= ' checked="checked"';
}
$displayArray[$dak][$dai]['value'] .= $disabled.' />No</label>';
$disfields[] = 'homePageFlag';
$dai++;
if($fromtemp){
	$displayArray[$dak][$dai]['title'] = 'High Priority';
	$displayArray[$dak][$dai]['value'] = '<input type="checkbox" name="tmp_priority" value="1"';
	if($tmp_priority==1) {
		$displayArray[$dak][$dai]['value'] .= ' checked="checked"';
	}
	//$displayArray[$dak][$dai]['value'] .= $disabled;
	//$disfields[] = 'tmp_priority';
	$displayArray[$dak][$dai]['value'] .= ' />';
	$dai++;
	$displayArray[$dak][$dai]['title'] = '&nbsp;';
	$displayArray[$dak][$dai]['value'] = '<div id="show_buttons" style="display:none;"><input class="button" type="submit" name="save" value="'.$button.'" /> &nbsp; <input class="button" type="submit" name="cancel" value="Cancel" onclick="self.close(); return false;" />
	</div><div id="no_buttons" class="error">Loading error . . . <a href="'.$url.'">Reload</a></div>';
	$dai++;
}
else{
	$displayArray[$dak][$dai]['title'] = 'Product Comment <em>(internal)</em>';
	$displayArray[$dak][$dai]['value'] = '<textarea name="productComment" rows="5" cols="40" class="input_box" style="background:#DDF9EE;">'.htmlspecialchars($productComment,ENT_QUOTES).'</textarea>';
	$dai++;
	if($updID!='') {
		$displayArray[$dak][$dai]['title'] = 'Send';
		$displayArray[$dak][$dai]['value'] = '<a href="../sendLink.php?id='.$updID.'&amp;send_mode=1" title="Click this if you want to send the products details of this product as a link to your colleague" onclick="sendColleagueA('.$updID.',1); return false;">Link to your colleague</a> &nbsp; | &nbsp;
		<a href="../sendLink.php?id='.$updID.'&amp;send_mode=2" style="font-style:italic;" title="Click this if you want to send the products details of this product as a QA link to your colleague" onclick="sendColleagueA('.$updID.',2); return false;">QA link to your colleague</a>';
		$dai++;
		$displayArray[$dak][$dai]['title'] = 'Current Status';
		switch($productStatus){
			case 1:
				$displayArray[$dak][$dai]['value'] = 'Approved';
				break;
			case 2:
				$displayArray[$dak][$dai]['value'] = 'Unapproved';
				break;
			case 3:
				$displayArray[$dak][$dai]['value'] = 'Reprocessed';
				break;
			case 4:
				$displayArray[$dak][$dai]['value'] = 'Problem';
				break;
			case -1:
				$displayArray[$dak][$dai]['value'] = 'Unused';
				break;
			default:
				$displayArray[$dak][$dai]['value'] = '&nbsp;';
		}
		$dai++;
	}

	$displayArray[$dak][$dai]['title'] = 'Mark As';
	$displayArray[$dak][$dai]['value'] = '<div id="show_buttons" style="display:none;"><input class="button" type="submit" name="saver" value="';
	if(checkGroup(27)) {
		$displayArray[$dak][$dai]['value'] .= 'Approved';
	}
	else {
		$displayArray[$dak][$dai]['value'] .= 'Saved';
	}
	$displayArray[$dak][$dai]['value'] .= '"';
	if(checkGroup(27) || $updID=='') {
		$displayArray[$dak][$dai]['value'] .= ' onclick="return doSaved(1);"';
	}
	$displayArray[$dak][$dai]['value'] .= ' /> &nbsp; ';
	if(checkGroup(28) && $updID!=''){
		$displayArray[$dak][$dai]['value'] .= '<input class="button" type="submit" name="rep" value="Reprocessed" onclick="return doSaved(3);" /> &nbsp; ';
	}
	if(checkGroup(27)) {
		$displayArray[$dak][$dai]['value'] .= '<input class="button" type="submit" name="una" value="Unapproved" onclick="return doSaved(2);" /> &nbsp; ';
	}
	$displayArray[$dak][$dai]['value'] .= '<input class="button" type="submit" name="prob" value="Problem" onclick="return doSaved(4);" /> &nbsp; <input class="button" type="button" value="Cancel" onclick="location.href=\'manageproduct.php\'; return false;" />';
	if($is_citi=='1' || in_array(266,$sectorID) || in_array(315,$sectorID)) {
		$displayArray[$dak][$dai]['value'] .= ' &nbsp; <input class="button" type="submit" name="prob" value="Unused" onclick="return doSaved(-1);" />';
	}
	if($updID!='' && checkGroup(23)) {
		$displayArray[$dak][$dai]['value'] .= ' &nbsp; <input class="button" type="button" value="Delete" onclick="doProdDelete('.$updID.'); return false;" />';
	}
	if($updID=='' && !isset($_GET['muid'])) {
		$displayArray[$dak][$dai]['value'] .= '<br /><input class="button" type="submit" name="saveAndAdd" value="';
		if(checkGroup(27)) {
			$displayArray[$dak][$dai]['value'] .= 'Approved';
		}
		else {
			$displayArray[$dak][$dai]['value'] .= 'Saved';
		}
		$displayArray[$dak][$dai]['value'] .= ' &amp; Add More" style="margin-top:4px;"';
		if(checkGroup(27) || $updID=='') {
			$displayArray[$dak][$dai]['value'] .= ' onclick="return doSaved(1);"';
		}
		$displayArray[$dak][$dai]['value'] .= ' />';
	}
	$displayArray[$dak][$dai]['value'] .= '</div><div id="no_buttons" class="error">Loading error . . . <a href="'.$url.'">Reload</a></div>';
	$dai++;
}?>
<!--#####################SHOW PREVIEW IN HTML####################//-->
<script type="text/javascript">
   function doWinSize(posr,bottom){
            var wintext = '';
            var screenH = 0;
            var screenW = 0;

            if (screen){
                    if (screen.width) {
                            screenW = screen.width;
                    }
                    if (screen.height) {
                            screenH = screen.height;
                    }
            }
            if(posr){
                    var leftr = screenW/2;
                    wintext = wintext+', left='+leftr+', top=0';
            }
            else{
                    wintext = wintext+', left=0, top=0';
            }
            if(screenH>0 && screenW>0){
                    screenW = screenW/2 - 20;
                    screenH = screenH - bottom;
                    wintext = wintext+', width='+screenW+', height='+screenH;	
            }
            return wintext;
    }
    function winPopMessageHTML(winloc) {
	var addtext = doWinSize(true,200);
        var wind = window.open(winloc,"winpop3","scrollbars=yes, resizable=yes,toolbar=no,location=no,menubar=no,status=no"+addtext);
	//var wind = window.open(winloc,"winpop2","scrollbars=yes, resizable=yes, toolbar=yes,location=yes,menubar=yes,status=yes"+addtext);
	wind.focus();
}
</script>
