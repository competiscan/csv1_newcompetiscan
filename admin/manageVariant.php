<?php 
$ALLOW_GROUPS = array(4);
require_once("../auth_auth.php");
include 'top.php';
require_once '../includes/functions.php';
require_once('../includes/ehLog.php');

if(isset($_GET['gtype'])){
	$gtype = (int)$_GET['gtype'];
}
else{
	$gtype = 1;
}
if(isset($_REQUEST['upv'])){
	exec("cd ../; /usr/bin/php update_variants.php > /dev/null 2>&1 &");
	ob_end_clean();
	header("Location: ".$_SERVER['PHP_SELF']."?gtype=$gtype&refreshed=1");
	exit;
}

if(isset($_REQUEST['pid']) && isset($_REQUEST['gid'])){
	$pid = (int) $_REQUEST['pid'];
	$gid = (int) $_REQUEST['gid'];
	$qU = "SELECT sectorID,categoryID,subCategoryID,company,secondCompany FROM cscan_product_detail WHERE productID=$pid";
	$resultU = $DRW->query($qU,$DRW_read);
	$dataU = $DRW->fetch_row($resultU);
	$sectorID = explode(',',$dataU[0]);
	sort($sectorID);
	$sectorID = implode(',',$sectorID);
	$categoryID = explode(',',$dataU[1]);
	sort($categoryID);
	$categoryID = implode(',',$categoryID);
	$subCategoryID = explode(',',$dataU[2]);
	sort($subCategoryID);
	$subCategoryID = implode(',',$subCategoryID);
	$company = $dataU[3];
	$secondCompany = $dataU[4];
	$companyID = 0;
	$secondCompanyID = '';
	$combos = array();
	
	if($gtype==2){
		$cos = array();
		$resultC = $DRW->query("SELECT companyID,primary_co FROM cscan_company_product WHERE productID=$pid ORDER BY companyID",$DRW_read);
		while($dataC = $DRW->fetch_row($resultC)){
			if($dataC[1]==1){
				$companyID = $dataC[0];
			}
			else{
				$cos[] = $dataC[0];
			}
		}
		$secondCompanyID = implode(',',$cos);
		
		$qU = "SELECT productID FROM cscan_variant_check WHERE groupID=$gid AND productID<>$pid AND (companyID<>$companyID OR secondCompanyID<>'$secondCompanyID')";
	}
	else{
		$resultC = $DRW->query("SELECT scsc_sectorID,scsc_categoryID,scsc_subCategoryID,scsc_sort FROM cscan_scsc_product WHERE productID=$pid",$DRW_read);
		while($dataC = $DRW->fetch_row($resultC)){
			$combos[] = $dataC;
		}
		$qU = "SELECT productID FROM cscan_variant_check WHERE groupID=$gid AND productID<>$pid AND (sectorID<>'$sectorID' OR categoryID<>'$categoryID' OR subCategoryID<>'$subCategoryID')";
	}
	$resultU = $DRW->query($qU,$DRW_read);
	while($dataU = $DRW->fetch_row($resultU)){
		$updID = $dataU[0];
		
		if($gtype==2){
			$query2 = "SELECT COUNT(*) FROM cscan_img_company WHERE companyID=$companyID";
			$query_result2 = $DRW->query($query2,$DRW_read);
			$data2 = $DRW->fetch_row($query_result2);
			if($data2[0]>0){
				saveImageData($updID,'','','',$companyID);
			}
			
			$sqlU = "DELETE FROM cscan_company_product WHERE productID=$updID";
			$DRW->query($sqlU,$DRW_main);
			
			array_unshift($cos,$companyID);
			$primary_co = 1;
			foreach($cos as $p_id){
				if($p_id!=''){
					$sqlU = "INSERT IGNORE INTO cscan_company_product (productID,companyID,primary_co) 
						VALUES ($updID,".(float)$p_id.",$primary_co)";
					$DRW->query($sqlU,$DRW_main);
					$primary_co++;
				}
			}
			
			$query = "update cscan_variant_check set companyID=".$companyID.",secondCompanyID='".$secondCompanyID."' where productID=$updID";
			$DRW->query($query,$DRW_main);
			
			$query = "update cscan_product_detail set company='".$DRW->real_escape_string($company)."',secondCompany='".$DRW->real_escape_string($secondCompany)."' where productID=$updID";
			$DRW->query($query,$DRW_main);
		}
		else{
			$query = "update cscan_variant_check set sectorID='".$sectorID."',categoryID='".$categoryID."',subCategoryID='".$subCategoryID."' where productID=$updID";
			$DRW->query($query,$DRW_main);
			
			$query = "update cscan_product_detail set sectorID='".$sectorID."',categoryID='".$categoryID."',subCategoryID='".$subCategoryID."' where productID=$updID";
			$DRW->query($query,$DRW_main);
			
			$sqlU = "DELETE FROM cscan_scsc_product WHERE productID=$updID";
			$DRW->query($sqlU,$DRW_main);
			$one = false;
			foreach($combos as $combo){
				list($s,$c,$sc,$scsc_sort) = $combo;
				if(!empty($s) || !empty($c) || !empty($sc)){
					$sqlU = "INSERT IGNORE INTO cscan_scsc_product (productID,scsc_sectorID,scsc_categoryID,scsc_subCategoryID,scsc_sort) VALUES ($updID,$s,$c,$sc,$scsc_sort)";
					$DRW->query($sqlU,$DRW_main);
					$one = true;
                                        if($scsc_sort==1){
                                            $last_prd_sql = "UPDATE cscan_product_detail SET scsc_sort=1 WHERE productID='$updID'";
                                            $DRW->query($last_prd_sql,$DRW_main);
                                        }
				}
			}
			if(!$one){
				$sqlU = "INSERT IGNORE INTO cscan_scsc_product (productID,scsc_sectorID,scsc_categoryID,scsc_subCategoryID) VALUES ($updID,0,0,0)";
				$DRW->query($sqlU,$DRW_main);
			}
		}
	}
	ob_end_clean();
	header("Location: {$_SERVER['PHP_SELF']}?gtype=$gtype&updated=1");
	exit;
}
if($gtype==2){
	$label = 'Company';
	$groupby = "companyID,secondCompanyID";
}
else{
	$label = 'Sector/Category/Subcategory';
	$groupby = "sectorID,categoryID,subCategoryID";
}
?>
<table width="100%" border="0" cellspacing="0" cellpadding="5" class="text">
<tr><td class="adminhead" align="center"><div id="pcontainer">VARIANT MANAGEMENT</div></td></tr>
</table>
<div style="margin:10px;">
<?php
if(running_php_cmd('update_variants.php')){
	echo '<em>Update In Process . . .</em>';
}
else{
	?>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" name="upper"><input class="button" type="submit" name="go" value="Update Variants" /><input type="hidden" name="upv" value="1" /><input type="hidden" name="gtype" value="<?php echo $gtype; ?>" /> <em>Note: This update may take a while depending on the last update.</em></form>
	<?php 
}
?>
</div>
<table width="100%" border="0" cellspacing="0" cellpadding="5" class="text">
<tr class="head1">
<td class="adminhead"><strong>Entry ID</strong></td>
<td class="adminhead"><strong><?php echo $label; ?></strong></td>
</tr>
<?php
$tot = 0;
$tot_max = 100;
$className='';
$currgroup = 0;
$q = "SELECT groupID,count(*),MAX(actual_addedToDatabase) AS max_date FROM cscan_variant_check GROUP BY groupID ORDER BY max_date DESC";
$result = $DRW->query($q,$DRW_read);
while($data = $DRW->fetch_row($result)){
	$groupID = $data[0];
	$count = $data[1];
	
	$qU = "SELECT count(*),$groupby FROM cscan_variant_check WHERE groupID=$groupID GROUP BY $groupby";
	$resultU = $DRW->query($qU,$DRW_read);
	$dataU = $DRW->fetch_row($resultU);
	$ccount = $dataU[0];
	
	if($count!=$ccount){
		$tot++;
		if($className=='selected-bg') {
			$className='white-bg';
		}
		else {
			$className='selected-bg';
		}
		$qU = "SELECT d.productID,d.sectorID,d.categoryID,d.subCategoryID,v.companyID,v.secondCompanyID,d.vid,d.entryID
			FROM cscan_variant_check v join cscan_product_detail d on (v.productID=d.productID) WHERE groupID=$groupID ORDER BY d.actual_addedToDatabase";
		$resultU = $DRW->query($qU,$DRW_read);
		while($dataU = $DRW->fetch_row($resultU)){
			$productID = $dataU[0];
			$sectorID = sectorName($dataU[1]);
			$categoryID = categoryName($dataU[2]);
			$subCategoryID = subCategoryName($dataU[3]);
			$companyID = getCompanyName($dataU[4]);
			$secondCompanyID = getCompanyName($dataU[5]);
			$vid = explode(',',$dataU[6]);
			$entryID = $dataU[7];
			
			if($tot<$tot_max){
				$queryI = "SELECT UNIX_TIMESTAMP(img_document_createddate) FROM cscan_img_document WHERE productID=$productID AND document_id=1 AND img_document_default=1";
				$query_resultI = $DRW->query($queryI,$DRW_read);
				$dataI = $DRW->fetch_row($query_resultI);
				$img_createddate_ts = (float)$dataI[0];
				
				echo "<tr class=\"$className\"><td><img src=\"../images/arrow.gif\" id=\"pimg$productID\" alt=\"\" title=\"Preview this Product\" style=\"cursor:pointer;\" onclick=\"doPreview('$productID',$img_createddate_ts);\" onmouseover=\"showPreview($productID,$img_createddate_ts); return true;\" onmouseout=\"hidePreview($productID); return true;\" />
				<a href=\"addproduct.php?id=$productID\" target=\"_blank\">$entryID</a></td><td>";
				if($gtype==2){
					if(empty($secondCompanyID)){
						$secondCompanyID = 'none';
					}
					echo "$companyID/$secondCompanyID";
				}
				else{
					echo "$sectorID/$categoryID/$subCategoryID";
				}
				echo " &nbsp; [<a href=\"".$_SERVER['PHP_SELF']."?gtype=$gtype&amp;pid=$productID&amp;gid=$groupID\">Use</a>]</td></tr>";
			}
		}
	}
}
if($tot<$tot_max){
	$tot_max = $tot;
}
echo "<tr><td colspan=\"2\"><hr />".number_format($tot_max)." of ".number_format($tot)." Total Groupings</td></tr>";
echo '</table>';

include 'bottom.php';
?>