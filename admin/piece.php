<?php 
require_once("../auth_auth.php");
require_once '../includes/functions.php';

@ob_clean();

$show = 20;
if((isset($_REQUEST['findval']) && trim($_REQUEST['findval'])!='') || isset($_REQUEST['check'])){
	$ids_only = array();
	$ids = array();
	$addlink = '';
	if(isset($_REQUEST['pub_ids'])){
		$ids = explode(',',$_REQUEST['pub_ids']);
		foreach($ids as $cid_date){
			if($cid_date!=''){
				list($cid) = explode('|',$cid_date);
				if(!in_array($cid,$ids_only)) $ids_only[] = $cid;
			}
		}
		$q = "SELECT publicationID,publicationName,audience_id,p_stateID,p_primary_country FROM cscan_publication 
			WHERE publicationName LIKE '".mysqlLike(trim($_REQUEST['findval']))."%' ORDER BY publicationName LIMIT ".($show+1);
		if(checkGroup(36)){
			$addlink = '<br />&nbsp;[<a href="#" style="color: #FFFFFF;font-weight:bold;" onclick="addPub(0,\'\',\'\',-1,\'\'); return false;">Create New</a>]';
		}
		$onclick = "addPub";
	}
	elseif(isset($_REQUEST['cmp_ids'])){
		$ids = explode(',',$_REQUEST['cmp_ids']);
		foreach($ids as $cid_date){
			if($cid_date!=''){
				list($cid) = explode('|',$cid_date);
				if(!in_array($cid,$ids_only)) $ids_only[] = $cid;
			}
		}
		if(checkGroup(6)){
			$q = "SELECT companyID,companyName,isMilitaryCo,co_states,comboIDs,isInsuranceExchange FROM cscan_company  
				WHERE companyName LIKE '".mysqlLike(trim($_REQUEST['findval']))."%' ORDER BY companyName LIMIT ".($show+1);
			$addlink = '<br />&nbsp;[<a href="#" style="color: #FFFFFF;font-weight:bold;" onclick="addCmp(0,\'\',0,0,\'\',\'\',0); return false;">Create New</a>]';
		}
		//else{
			//$q = "SELECT DISTINCT cc.companyID,companyName,isMilitaryCo,co_states,comboIDs,isInsuranceExchange FROM cscan_company cc, cscan_company_product cp  
			//	WHERE companyName LIKE '".mysqlLike(trim($_REQUEST['findval']))."%' AND cc.companyID=cp.companyID AND primary_co=1 ORDER BY companyName LIMIT ".($show+1);
			//$q = "SELECT DISTINCT cc.companyID,companyName,isMilitaryCo,co_states,comboIDs,isInsuranceExchange FROM cscan_company cc LEFT JOIN cscan_company_product cp ON (cc.companyID=cp.companyID AND primary_co=1)
			//	WHERE companyName LIKE '".mysqlLike(trim($_REQUEST['findval']))."%' AND (isApprovedCo=1 OR cp.companyID IS NOT NULL) ORDER BY companyName LIMIT ".($show+1);
			$q = "(SELECT cc.companyID,companyName,isMilitaryCo,co_states,comboIDs,isInsuranceExchange FROM cscan_company cc JOIN cscan_company_product cp ON (cc.companyID=cp.companyID AND primary_co=1) WHERE companyName LIKE '".mysqlLike(trim($_REQUEST['findval']))."%')
				UNION DISTINCT
				(SELECT cc.companyID,companyName,isMilitaryCo,co_states,comboIDs,isInsuranceExchange FROM cscan_company cc WHERE companyName LIKE '".mysqlLike(trim($_REQUEST['findval']))."%' AND isApprovedCo=1)
				ORDER BY companyName LIMIT ".($show+1);
		//}
		$onclick = "addCmp";
	}
	elseif(isset($_REQUEST['aff_ids'])){
		$ids = explode(',',$_REQUEST['aff_ids']);
		foreach($ids as $cid_date){
			if($cid_date!=''){
				list($cid) = explode('|',$cid_date);
				if(!in_array($cid,$ids_only)) $ids_only[] = $cid;
			}
		}
		$q = "SELECT affinityID,affinityName FROM cscan_affinity 
			WHERE affinityName LIKE '".mysqlLike(trim($_REQUEST['findval']))."%' ORDER BY affinityName LIMIT ".($show+1);
		if(checkGroup(35)){
			$addlink = '<br />&nbsp;[<a href="#" style="color: #FFFFFF;font-weight:bold;" onclick="addAff(0,\'\',0); return false;">Create New</a>]';
		}
		$onclick = "addAff";
	}
	elseif(isset($_REQUEST['cpn_ids'])){
		$cids = explode(',',$_REQUEST['cpn_ids']);
		$cids_only = array();
		foreach($cids as $cid_date){
			if($cid_date!=''){
				list($cid) = explode('|',$cid_date);
				if(!in_array($cid,$cids_only)) $cids_only[] = $cid;
			}
		}
		if(isset($_REQUEST['check'])){
			$out = 0;
			if(count($cids_only)>0){
				$q = "SELECT COUNT(*) FROM cscan_company_productname WHERE companyID IN (".implode(',',$cids_only).")";
				$resultC = $DRW->query($q,$DRW_read);
				$dataC = $DRW->fetch_row($resultC);
				if($dataC[0]>0){
					$out = 1;
				}
			}
			@ob_clean();
			echo $out;
			exit;
		}
		else{
			if(count($cids_only)>0){
				$ctext = ' AND companyID IN ('.implode(',',$cids_only).')';
			}
			else{
				$ctext = '';
			}
			$val = mysqlLike(trim($_REQUEST['findval']));
			if(strlen($val)>2) {
				$firstpct = '%';
			}
			else {
				$firstpct = '';
			}
			$like = "'$firstpct$val%'";
			$q = "SELECT companyID,productName FROM cscan_company_productname 
				WHERE productName LIKE $like$ctext ORDER BY productName LIMIT ".($show+1);
			$addlink = '<br />&nbsp;[<a href="#" style="color: #FFFFFF;font-weight:bold;" onclick="addCPN(\'0\',\'Product Name Not Mentioned\',0); return false;">Product Name Not Mentioned</a>]';
			if(checkGroup(42) && count($cids_only)>0){
				$addlink .= '<br />&nbsp;[<a href="#" style="color: #FFFFFF;font-weight:bold;" onclick="addCPN(\''.$cids_only[0].'\',\'\',1); return false;">Create New</a>]';
			}
		}
		$onclick = "addCPN";
	}
	else {
		exit;
	}
	$resultC = $DRW->query($q,$DRW_read);
	if($DRW->num_rows($resultC)>0){
		$i = 0;
		while($dataC = $DRW->fetch_row($resultC)){
			$ID = $dataC[0];
			$Name = $dataC[1];
			$military = 0;
			if(isset($_REQUEST['pub_ids'])){
				$audience = mediaPanelName($dataC[2]);
				$state = $dataC[3];//stateName
				$primary_country = $dataC[4];
			}
			elseif(isset($_REQUEST['aff_ids'])){
				$aff_cids = array();
				$resultC2 = $DRW->query("SELECT AffinityCategoryID FROM cscan_aff_cat WHERE affinityID=$ID",$DRW_read);
				while($dataC2 = $DRW->fetch_row($resultC2)){
					if(!in_array($dataC2[0],$aff_cids) && !empty($dataC2[0])){
						$aff_cids[] = $dataC2[0];
						if($dataC2[0]==10){
							$military = 1;
						}
					}
				}
				if(count($aff_cids)>0){
					$Name .= ' ('.htmlspecialchars(getAffinityCategoryName(implode(',',$aff_cids))).')';
				}
			}
			elseif(isset($_REQUEST['cmp_ids']) && $dataC[2]){
				$military = 1;
			}
			if($i>=$show){
				echo "...";
			}
			else{
				if(isset($_REQUEST['pub_ids']) || !in_array($ID,$ids)){
					echo "<a href=\"#\" style=\"color: #FFFFFF;\" onclick=\"$onclick($ID,'".htmlspecialchars(singleQuoteSafe($Name),ENT_QUOTES)."'";
					if(isset($_REQUEST['pub_ids'])){
						echo ",'".htmlspecialchars(singleQuoteSafe($audience),ENT_QUOTES)."','".htmlspecialchars(singleQuoteSafe($state),ENT_QUOTES)."','".htmlspecialchars(singleQuoteSafe($primary_country),ENT_QUOTES)."'";
					}
					elseif(isset($_REQUEST['cmp_ids'])){
						$query2 = "SELECT COUNT(*) FROM cscan_img_company WHERE companyID=$ID";
						$query_result2 = $DRW->query($query2,$DRW_read);
						$data2 = $DRW->fetch_row($query_result2);
						if($data2[0]>0){
							echo ',1';
						}
						else{
							echo ',0';
						}
					}
					elseif(isset($_REQUEST['cpn_ids'])){
						echo ',0';
					}
					if(isset($_REQUEST['aff_ids']) || isset($_REQUEST['cmp_ids']) ){
						echo ','.$military;
					}
					if(isset($_REQUEST['cmp_ids'])){
						echo ",'".$dataC[3]."','".$dataC[4]."',".$dataC[5];
					}
					echo "); return false;\">";
				}
				if(in_array($ID,$ids_only)) {
					echo '* ';
				}
				echo htmlspecialchars($Name);
				if(isset($_REQUEST['pub_ids']) || !in_array($ID,$ids)){	
					echo "</a>";
				}
				echo "<br />";
				$i++;
			}
		}
	}
	else{
		echo '<em>Unavailable</em>';
	}
	echo $addlink;
}
elseif(isset($_REQUEST['pub_name']) && checkGroup(36)){
	$pub_name = trim($_REQUEST['pub_name']);
	$chk = "SELECT publicationID FROM cscan_publication WHERE publicationName='".$DRW->real_escape_string($pub_name)."'";	
	$rs = $DRW->query($chk,$DRW_read);
	$row = $DRW->fetch_row($rs);
	$publicationID = (int)$row[0];
	if($publicationID==0) {	
	 	$sql = "INSERT INTO cscan_publication SET publicationName='".$DRW->real_escape_string($pub_name)."'";	 
		$DRW->query($sql,$DRW_main);
		$publicationID = $DRW->insert_id($DRW_main);
	}
	@ob_clean();
	echo $publicationID;
	exit;
}
elseif(isset($_REQUEST['aff_name']) && checkGroup(35)){
	$name = trim($_REQUEST['aff_name']);
	$chk = "SELECT affinityID FROM cscan_affinity WHERE affinityName='".$DRW->real_escape_string($name)."'";	
	$rs = $DRW->query($chk,$DRW_read);
	$row = $DRW->fetch_row($rs);
	$ID = (int)$row[0];
	if($ID==0) {	
	 	$sql = "INSERT INTO cscan_affinity SET affinityName='".$DRW->real_escape_string($name)."'";	 
		$DRW->query($sql,$DRW_main);
		$ID = $DRW->insert_id($DRW_main);
	}
	@ob_clean();
	echo $ID;
	exit;
}
elseif(isset($_REQUEST['cmp_name']) && checkGroup(6)){
	$name = trim($_REQUEST['cmp_name']);
	$chk = "SELECT companyID FROM cscan_company WHERE companyName='".$DRW->real_escape_string($name)."'";	
	$rs = $DRW->query($chk,$DRW_read);
	$row = $DRW->fetch_row($rs);
	$ID = (int)$row[0];
	if($ID==0) {	
	 	$sql = "INSERT INTO cscan_company SET companyName='".$DRW->real_escape_string($name)."'";	 
		$DRW->query($sql,$DRW_main);
		$ID = $DRW->insert_id($DRW_main);
	}
	@ob_clean();
	echo $ID;
	exit;
}
elseif(isset($_REQUEST['cpn_name']) && checkGroup(42)){
	$name = trim($_REQUEST['cpn_name']);
	$ID = (int)$_REQUEST['cpn_id'];
	$chk = "SELECT count(*) FROM cscan_company_productname WHERE companyID=$ID AND productName='".$DRW->real_escape_string($name)."'";	
	$rs = $DRW->query($chk,$DRW_read);
	$row = $DRW->fetch_row($rs);
	if((int)$row[0]==0) {
	 	$sql = "INSERT INTO cscan_company_productname (companyID,productName) VALUES ($ID,'".$DRW->real_escape_string($name)."')";	 
		$DRW->query($sql,$DRW_main);
	}
	@ob_clean();
	echo $ID;
	exit;
}
?>