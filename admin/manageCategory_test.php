<?php 
$ALLOW_GROUPS = array(6,35,36);
require_once("../auth_auth.php");
require_once '../includes/functions.php';
// include 'top.php';
$limit = 50;
/*$queryProductName = "SELECT productName FROM cscan_company_productname WHERE companyID=18610";
			
$query_result_product = $DRW->query($queryProductName,$DRW_read);
$assoicateData='';
While($dataProduct = $DRW->fetch_array($query_result_product)){
       $assoicateData.=$dataProduct['productName']."<br/>";

}
echo $assoicateData; die;*/
//echo $displays3URL; exit;
//$sql ="SELECT com1.companyID,com1.companyName,com1.isWorksiteVoluntary,com1.isApprovedCo, com2.companyName AS parentCompanyName,com1.isRetailMarketer FROM cscan_company com1 LEFT JOIN cscan_company com2 ON com1.parentCompanyID = com2.companyID LEFT JOIN cscan_company_product cp ON (cp.companyID=com1.companyID) WHERE cp.companyID IS NULL order by com1.companyName ASC ";
//$sql="SELECT com1.companyID,com1.companyName,com2.companyName AS parentCompanyName,com1.isApprovedCo,com1.isWorksiteVoluntary,com1.isCreditUnion,com1.isInsuranceExchange, com1.isMilitaryCo,com1.isRetailMarketer,com1.co_states,com1.comboIDs FROM cscan_company com1 LEFT JOIN cscan_company com2 ON com1.parentCompanyID = com2.companyID WHERE 1=1 AND com1.companyID in (18610,493,14995,15293,12777) order by com1.companyName ASC Limit 100";
$sql="SELECT com1.companyID,com1.companyName,com2.companyName AS parentCompanyName,com1.isApprovedCo,com1.isWorksiteVoluntary,com1.isCreditUnion,com1.isInsuranceExchange, com1.isMilitaryCo,com1.isRetailMarketer,com1.co_states,com1.comboIDs FROM cscan_company com1 LEFT JOIN cscan_company com2 ON com1.parentCompanyID = com2.companyID WHERE 1=1 order by com1.companyName ASC";
//$sql="SELECT com1.companyID,com1.companyName,com1.isWorksiteVoluntary,com1.isApprovedCo, com2.companyName AS parentCompanyName,com1.isRetailMarketer FROM cscan_company com1 LEFT JOIN cscan_company com2 ON com1.parentCompanyID = com2.companyID WHERE 1=1 order by com1.companyName ASC Limit 25000,26026";
$rs = $DRW->query($sql,$DRW_read2);
$resultCount = $DRW->num_rows($rs);
$arrExport = array();

if($resultCount > 0) {
	$className='';
        //$arrExport['data'][] = array("companyID", "companyName", "Image","primary","secondary","temp");
	//$arrExport['data'][] = array("CompanyID", "Company Name","Parent Company","Worksite/Voluntary","Image","Count Primary(Secondary)[Temp]","Approved Company");
        $arrExport['data'][] = array("Company Name","Parent Company","Approved","Worksite/Voluntary","Credit Union","Insurance Exchange","Military","Retail Marketer","Has Product Image","Associated Data","Associated States/provinces","Associated Sector/Category/Sub category");
	//echo '<table border="1" style="border:1px solid;">
	//<tr><th>companyID</th><th>companyName</th><th>Image</th><th>primary</th><th>secondary</th><th>temp</th></tr>';
	while($row = $DRW->fetch_row($rs)) {
			$ID 			= strtoupper($row[0]);
			$companyName 	= strtoupper($row[1]);
			$parentCompanyName=strtoupper($row[2]);
                        
                        if($row[3]==1){
                            $isApprovedCo="Yes"; 
                        }else{
                            $isApprovedCo="No";
                        }
                        if($row[4]==1){
                           $worksiteVoluntary="Yes"; 
                        }else{
                            $worksiteVoluntary="No";
                        }
                        if($row[5]==1){
                           $isCreditUnion="Yes"; 
                        }else{
                            $isCreditUnion="No";
                        }
                        if($row[6]==1){
                           $isInsuranceExchange="Yes"; 
                        }else{
                            $isInsuranceExchange="No";
                        }
                        if($row[7]==1){
                           $isMilitaryCo="Yes"; 
                        }else{
                            $isMilitaryCo="No";
                        }
                        if($row[8]==1){
                           $isRetailMarketer="Yes"; 
                        }else{
                            $isRetailMarketer="No";
                        }
                        $co_state=$row[9];
                        $comboIDs=$row[10];
			/*$numquery2 = "SELECT COUNT(*) as numrows FROM cscan_company_product WHERE companyID=$ID AND primary_co=1";
			$numquery2 = $DRW->query($numquery2,$DRW_read);
			$nrow = $DRW->fetch_row($numquery2);
			$numrows2 = $nrow[0];
			
			$numquery3 = "SELECT COUNT(*) as numrows FROM cscan_company_product WHERE companyID=$ID AND primary_co<>1";
			$numquery3 = $DRW->query($numquery3,$DRW_read);
			$nrow = $DRW->fetch_row($numquery3);
			$numrows3 = $nrow[0];
			
			
			$numquery4 = "SELECT COUNT(*) as numrows FROM cscan_product_email WHERE cmp_ids REGEXP '[[:<:]]{$ID}[[:>:]]' AND productID=0";
			$numquery4 = $DRW->query($numquery4,$DRW_read);
			$nrow = $DRW->fetch_row($numquery4);
			$numrows4 = $nrow[0];
                        $tempcount='';
                        if($numrows4>0) {
			    $tempcount=	'['.number_format($numrows4).']';
			}
			$countprimarysecondry=number_format($numrows2).' ('.number_format($numrows3).')'.$tempcount;
			*/
			//$query2 = "SELECT COUNT(*) FROM cscan_img_company WHERE companyID=$ID";
                        $query2 = "SELECT img_co_path,img_co_filename FROM cscan_img_company WHERE companyID=$ID";
			$query_result2 = $DRW->query($query2,$DRW_read2);
			$data2 = $DRW->fetch_row($query_result2);
			$img_co_path = $data2[0];
			$img_co_filename = $data2[1];
			//echo"<tr><td>".$ID."</td><td>".$companyName."</td>";
			if($img_co_path!='' && $img_co_filename!=''){
				//$img='Yes';
				$img=$displays3URL.substr($img_co_path,1).$img_co_filename;
			}
			else{
				$img='';
				//echo '<td>No</td>';
			}
                        $queryProductName = "SELECT productName FROM cscan_company_productname WHERE companyID=$ID";
			$query_result_product = $DRW->query($queryProductName,$DRW_read2);
                        $assoicateData='';
                        if($DRW->num_rows($query_result_product)>0){
                            While($dataProduct = $DRW->fetch_array($query_result_product)){
                                   $assoicateData.=$dataProduct['productName']."\r\n";

                            }
                        }
                        $stateName='';
                        if(!empty($co_state) AND $co_state!=''){
                            $queryState = "select GROUP_CONCAT(DISTINCT stateName SEPARATOR '; ') AS stateName from cscan_state WHERE stateID in ($co_state)  ORDER BY stateName";
                            $query_result_state = $DRW->query($queryState, $DRW_read2);
                            $stateName='';
                            if($DRW->num_rows($query_result_state)>0){
                                $dataState = $DRW->fetch_row($query_result_state);
                                $stateName=$dataState[0];

                            }
                        }
                        $comboIDs_split = explode('|',$comboIDs);
                        $scsc_combo_text='';
                        foreach($comboIDs_split as $scsc_combo){
                                if(!empty($scsc_combo)){
                                        list($s,$c,$sc) = explode('_',$scsc_combo);
                                        $scsc_combo_text.= sectorName($s).' / '.sectorName($c).' / '.sectorName($sc)."\r\n";
                                        //$addCategoryOnload .= " displaySCSC('$scsc_combo','".singleQuoteSafe($scsc_combo_text)."'); ";
                                }
                        }
			//echo $img;
			//echo"<td>".$numrows2."</td><td>".$numrows3."</td><td>".$numrows4."</td></tr>";
			//$arrExport['data'][] = array($ID, $companyName,$img,$numrows2,$numrows3,$numrows4);
                        //$arrExport['data'][] = array($ID, $companyName,$parentCompanyName,$worksiteVoluntary,$img,$countprimarysecondry,$isApprovedCo);
                        $arrExport['data'][] = array($companyName,$parentCompanyName,$isApprovedCo,$worksiteVoluntary,$isCreditUnion,$isInsuranceExchange,$isMilitaryCo,$isRetailMarketer,$img,$assoicateData,$stateName,$scsc_combo_text);
	}
	download_send_headers("company_" . date("Y-m-d") .rand(). ".csv");
	echo array2csv($arrExport);
	die();
}

function array2csv(array &$array){
	if (count($array) == 0) {
	  return null;
	}
	ob_start();
	$df = fopen("php://output", 'w');
	foreach ($array['data'] as $row) {
	   fputcsv($df, $row);
	}
	fclose($df);
	return ob_get_clean();
 }
function download_send_headers($filename) {
    // disable caching
    $now = gmdate("D, d M Y H:i:s");
    header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
    header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
    header("Last-Modified: {$now} GMT");

    // force download  
    header("Content-Type: application/force-download");
    header("Content-Type: application/octet-stream");
    header("Content-Type: application/download");

    // disposition / encoding on response body
    header("Content-Disposition: attachment;filename={$filename}");
    header("Content-Transfer-Encoding: binary");
}
exit;
die;

if(isset($_REQUEST['p'])) $p = $_SESSION['manageCategory_p'] = $_REQUEST['p'];
elseif(isset($_SESSION['manageCategory_p'])) $p = $_SESSION['manageCategory_p'];
else $p = 0;

$permi = 68;
if(isset($_GET['type'])){
	$title = "Company";
	$category = "company";
	$categories="companies";
	$category_relation = "company";
	$type = intval($_GET['type']);
        
	if($type == 1 && checkGroup(36)) { 
		$title = "Publication";
		$category = "publication";
		$categories ="publications";
		$category_relation = "publication";
                $permi = 63;
	}
	elseif($type == 2 && checkGroup(35)) {
		$title = "Affinity";
		$category = "affinity";
		$categories="affinities";
		$category_relation = "affinityAssociationVal";
                $permi = 67;
	}
	else{
		$type	= 0;
	}
}
else  { 
	$title = "Company";
	$type = 0;
	$category = "company";
	$categories="companies";
	$category_relation = "company";
}

if(isset($_SESSION['manageCategory_type'])){
	$old_type = $_SESSION['manageCategory_type'];
}
else {
	$old_type = -1;
}
$_SESSION['manageCategory_type'] = $type;

if($old_type==$type && (isset($_GET['sort']) || isset($_SESSION['manageCategory_sort']))) {
	if(isset($_GET['sort'])){
		if(!isset($_SESSION['manageCategory_sort']) || $_GET['sort']!=$_SESSION['manageCategory_sort']){
			$p = $_SESSION['manageCategory_p'] = 0;
		}
		$sort = $_SESSION['manageCategory_sort'] = $_GET['sort'];
	}
	else{
		$sort = $_SESSION['manageCategory_sort'];
	}
	$sorttext = '&amp;sort='.$sort;
	$sorttext2 = '?sort='.$sort;
}
else {
	$p = $_SESSION['manageCategory_p'] = 0;
	$sort = $_SESSION['manageCategory_sort'] = '';
	$sorttext = '';
	$sorttext2 = '';
}

if(isset($_REQUEST['noproducts'])) $noproducts = $_REQUEST['noproducts'];
else $noproducts = 0;

if($type==0){
	$colspan= '6';
}
elseif($type == 1 || $type == 2) { 
	$colspan= '4';
}
else{
	$colspan= '3';
}
?>
<table width="100%" border="0" cellspacing="0" cellpadding="5" class="text">
  <tr><td class="adminhead" align="center" colspan="<?php echo $colspan; ?>"><?php echo strtoupper($title); ?> MANAGEMENT</td></tr>
  <!-- search and right buttons start-->
  <tr>
    <td colspan="<?php echo $colspan; ?>">
      <table border="0" width="100%" cellspacing="0" cellpadding="0">
        <tr>
          <td>
	<form method="post" name="frm1" action="<?php echo $_SERVER['PHP_SELF'].$sorttext2;
	if($sorttext2!='') echo "&amp;type=$type";
	else echo "?type=$type"; 
	?>" onsubmit="return false;">
            <table width="100%" border="0" cellspacing="0" cellpadding="0" class="text">
              <tr>
                <td><strong>Note</strong>: Click any of the following to modify the <?php echo strtolower($title); ?>.</td>
                <td align="right"><input class="button" style="width:130px" type="button" value="Add <?php $title; ?>" onclick="location.href='addCategory.php?type=<?php echo $type; ?>'; return false;" /></td>
                <td align="right" width="10%"><?php if(checkGroup($permi)){?><input class="button" style="width:60px" type="submit" name="submit1" ID="delBt" value="Delete" onclick="return confirmDel();" /><?php }?></td>
              </tr>
            </table>
	</form>
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td colspan="<?php echo $colspan; ?>"><?php
	$sql = "SELECT DISTINCT(LEFT({$category}Name,1)) FROM cscan_{$category} order by {$category}Name";
	
	$rs = $DRW->query($sql,$DRW_read);
	if($sort!='') print "<a href=\"{$_SERVER['PHP_SELF']}?noproducts=$noproducts&amp;type=$type&amp;sort=\">All</a>";
	else print 'All';
	
	while($row = $DRW->fetch_row($rs)){
		$row[0] = strtoupper($row[0]);
		if($row[0]!=$sort) print " &nbsp; <a href=\"{$_SERVER['PHP_SELF']}?sort=$row[0]&amp;noproducts=$noproducts&amp;type=$type\">$row[0]</a> ";
		else print " &nbsp; $row[0] ";
	}  
	?> &nbsp; &nbsp; <form method="post" name="frm4" action="<?php echo $_SERVER['PHP_SELF'].$sorttext2;
	if($sorttext2!='') echo "&amp;type=$type";
	else echo "?type=$type"; 
	?>"style="display:inline;" onsubmit="return false;"><label><input type="checkbox" name="noproducts" value="1" onclick="emptycompanies();" <?php if($noproducts == 1) echo 'checked="checked"';?> />Show <?php echo $categories; ?> without products</label></form>
    </td>
  </tr>
	<tr>
	<td colspan="<?php echo $colspan; ?>">
	<form method="post" name="frm2" action="<?php echo $_SERVER['PHP_SELF'].$sorttext2;
	if($sorttext2!='') echo "&amp;type=$type";
	else echo "?type=$type"; 
	?>">
	Search <input type="text" name="search_name" size="40" maxlength="100" class="input_box" value="<?php echo $sort; ?>" /> <input class="button" style="width:60px;" type="submit" name="search_Submit" value="Search" onclick="document.location.href='<?php echo "{$_SERVER['PHP_SELF']}?noproducts=$noproducts&amp;type=$type&amp;sort="; ?>'+encodeURIComponent(document.frm2.search_name.value); return false;" />
	&nbsp;&nbsp;&nbsp;&nbsp;
	<input class="button" style="width:70px" type="submit" name="show_All" value="Show All" onclick="document.location.href='<?php echo "{$_SERVER['PHP_SELF']}?noproducts=$noproducts&amp;type=$type&amp;sort="; ?>'; return false;" />
	</form></td>
	</tr>
</table>
<form method="post" name="frm3" action="<?php echo $_SERVER['PHP_SELF'].$sorttext2;
if($sorttext2!='') echo "&amp;type=$type";
else echo "?type=$type"; 
?>">
<table width="100%" border="0" cellspacing="0" cellpadding="5" class="text">
  <!-- search and right buttons close-->
<?php 

$message = '';
$cc = 0;
if(isset($_POST['submiter'])) {
	if(isset($_POST['delID'])){
		$delID = $_POST['delID'];
        $emailData = [];
		for($i=0;$i<count($delID);$i++) {                    
                    
                    $cc++;
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
                    
                    sendDevAlert('Caution! Data Deleted From '.$title,$html);
                }
	}
	if($cc > 0) {
		if($cc == 1){ 
			$message = "<strong>$cc</strong> $category updated.";
		}
		else{ 
			$message = "<strong>$cc</strong> $categories updated.";
		}
	}
}
if($sort!=''){
	$sortq = " WHERE {$category}Name LIKE '".mysqlLike($sort)."%' AND ";
}
else {
	$sortq = ' WHERE ';
}
if($noproducts ==0){
	$numquery = "select COUNT({$category}ID) as numrows from cscan_{$category} $sortq 1=1";
}
else {
	$numquery = "Select COUNT(cscan_{$category}.{$category}ID) as numrows FROM cscan_{$category} LEFT JOIN cscan_{$category}_product USING ({$category}ID) 
		$sortq cscan_{$category}_product.{$category}ID IS NULL";
}
// #echo $numquery;
$numquery = $DRW->query($numquery,$DRW_read);
$nrow = $DRW->fetch_row($numquery);
$numrows = $nrow[0];
if($type == 0){
	if($sort!=''){
		$sortq = " WHERE com1.{$category}Name LIKE '".mysqlLike($sort)."%' AND ";
	}
	else {
		$sortq = ' WHERE ';
	}
}
if($noproducts == 0){
	if($type == 0){
		//$sql = "SELECT companyID,companyName,isWorksiteVoluntary,isApprovedCo FROM cscan_company$sortq 1=1 ";
		$sql = "SELECT com1.companyID,com1.companyName,com1.isWorksiteVoluntary,com1.isApprovedCo, com2.companyName AS parentCompanyName,com1.isRetailMarketer FROM cscan_company com1
			LEFT JOIN cscan_company com2 ON com1.parentCompanyID = com2.companyID $sortq 1=1";
	}
	elseif($type == 1) { 
		$sql = "SELECT {$category}ID,{$category}Name,print_typeName FROM cscan_{$category} LEFT JOIN cscan_print_type USING (print_typeID) $sortq 1=1 ";
	}
	elseif($type == 2) {
		$sql = "SELECT {$category}ID,{$category}Name FROM cscan_{$category} $sortq 1=1 ";
	}
	else{
		$sql = "SELECT {$category}ID,{$category}Name FROM cscan_{$category} $sortq 1=1 ";
	}
	if ($type == 0) { 
		$sql .= " order by com1.companyName";
	} 
	else { 
		$sql .= " order by {$category}Name";
	}
	$sql .= " limit $p,$limit";
} 
else {
	if($type == 0){
		$sql = "SELECT com1.companyID,com1.companyName,com1.isWorksiteVoluntary,com1.isApprovedCo, com2.companyName AS parentCompanyName,com1.isRetailMarketer FROM cscan_company com1
			LEFT JOIN cscan_company com2 ON com1.parentCompanyID = com2.companyID 
			LEFT JOIN cscan_company_product cp ON (cp.companyID=com1.companyID)
			$sortq cp.companyID IS NULL order by com1.companyName ASC LIMIT $p, $limit";
	} 
	else {
		$sql = "SELECT cscan_{$category}.{$category}ID,cscan_{$category}.{$category}Name FROM cscan_{$category} 
			LEFT JOIN cscan_{$category}_product USING ({$category}ID)
			$sortq cscan_{$category}_product.{$category}ID IS NULL ORDER BY cscan_{$category}.{$category}Name ASC LIMIT $p, $limit";
	}
}
echo $sql;
$rs = $DRW->query($sql,$DRW_read);
$resultCount = $DRW->num_rows($rs);
$coIDs = array();
?>
    <tr><td class="adminhead" valign="bottom"><?php if(checkGroup($permi)){?><label><input type="checkbox" name="setUnset" onclick="setAll('delID[]');" /><strong>Delete</strong></label><?php }?></td>
    <td class="adminhead" valign="bottom"><strong><?php echo $title; ?> Name</strong></td>
    
    <?php 
	if($type==0){
		echo '<td class= "adminhead" valign = "bottom"><strong>Parent Company</strong></td>';
		echo "<td class=\"adminhead\" valign=\"bottom\"><strong>Worksite/Voluntary</strong></td>";
		echo "<td class=\"adminhead\" valign=\"bottom\"><strong>Image</strong></td>";
	}
	elseif($type == 1 || $type == 2) { 
		echo "<td class=\"adminhead\" valign=\"bottom\">&nbsp;</td>";
	}
	?><td class="adminhead" valign="bottom"><strong>Count <?php if($type==0) echo '<br />Primary<br />(Secondary)<br />[Temp]'; ?></strong></td></tr>
    <tr><td colspan="<?php echo $colspan; ?>" align="center" class="error"><?php echo $message; ?></td></tr>
<?php
if($resultCount > 0) {
	$className='';
	while($row = $DRW->fetch_row($rs)) {
		$ID = $row[0];
		$categoryName = $row[1];
		if(isset($row[2])) $extrafield = $row[2];
		else $extrafield = '';
		if(isset($row[3])) $extrafield2 = $row[3];
		else $extrafield2 = '';
		$coIDs[] = $ID;
		$numrows5 = $numrows4 = $numrows3 = 0;
		
		if($type == 0){
			echo $numquery2 = "SELECT COUNT(*) as numrows FROM cscan_{$category}_product WHERE {$category}ID=$ID AND primary_co=1";
			$numquery2 = $DRW->query($numquery2,$DRW_read);
			$nrow = $DRW->fetch_row($numquery2);
			$numrows2 = $nrow[0];
			
			$numquery3 = "SELECT COUNT(*) as numrows FROM cscan_{$category}_product WHERE {$category}ID=$ID AND primary_co<>1";
			$numquery3 = $DRW->query($numquery3,$DRW_read);
			$nrow = $DRW->fetch_row($numquery3);
			$numrows3 = $nrow[0];
			
			if($noproducts!=0){
				$numquery4 = "SELECT COUNT(*) as numrows FROM cscan_product_email WHERE cmp_ids REGEXP '[[:<:]]{$ID}[[:>:]]' AND productID=0";
				$numquery4 = $DRW->query($numquery4,$DRW_read);
				$nrow = $DRW->fetch_row($numquery4);
				$numrows4 = $nrow[0];
			}
			if(!empty($row[5])){
				$numquery5 = "SELECT COUNT(*) as numrows FROM dashboard_retail_energy_pricing WHERE {$category}ID=$ID";
				$numquery5 = $DRW->query($numquery5,$DRW_read);
				$nrow = $DRW->fetch_row($numquery5);
				$numrows5 = $nrow[0];
			}
		}
		else{
			$numquery2 = "SELECT COUNT(*) as numrows FROM cscan_{$category}_product WHERE {$category}ID=$ID";
			$numquery2 = $DRW->query($numquery2,$DRW_read);
			$nrow = $DRW->fetch_row($numquery2);
			$numrows2 = $nrow[0];
		}
		
		if ($className =='selected-bg') $className ='white-bg';
		else $className ='selected-bg';
?>
        <tr valign="top" class="<?php echo $className;?>"><td><?php 
        if(($numrows2+$numrows3+$numrows5)==0 && checkGroup($permi)) print "<input type=\"checkbox\" name=\"delID[]\" value=\"$ID\" />";
        else print '&nbsp;';
        ?></td>
		<td><?php 
		print "<a class=\"hlinks\" href=\"addCategory.php?id=$ID&amp;type=$type\" title=\"Click here to edit.\"><strong>$categoryName</strong></a>";
		if($numrows2>0 || !empty($extrafield2)) {
			echo ' <strong>*</strong>';
		}
		?></td><?php 
		if($type==0){
			if($row[4]==''){
				$row[4] = '&nbsp;';
			}
			print "<td>".$row[4]."</td>";
			print "<td><input type=\"checkbox\" name=\"isWorksiteVoluntary[]\" value=\"$ID\" id=\"isWorksiteVoluntary$ID\" onclick=\"doChexChange('isWorksiteVoluntary',$ID);\"";	       
			if($extrafield==1) print ' checked="checked"';
			print " /></td><td>";
			$query2 = "SELECT COUNT(*) FROM cscan_img_company WHERE companyID=$ID";
			$query_result2 = $DRW->query($query2,$DRW_read);
			$data2 = $DRW->fetch_row($query_result2);
			if($data2[0]>0){
				echo 'Yes';
			}
			else{
				echo '&nbsp;';
			}
			print '</td>';
		}
		elseif($type == 2) {
			$aff_cids = array();
			$resultC2 = $DRW->query("SELECT catmap.AffinityCategoryID FROM cscan_aff_cat catmap, cscan_affinity_category catmaster WHERE catmap.affinityID=$ID AND catmap.AffinityCategoryID=catmaster.AffinityCategoryID and catmaster.parentID=0",$DRW_read);
			while($dataC2 = $DRW->fetch_row($resultC2)){
				if(!in_array($dataC2[0],$aff_cids) && !empty($dataC2[0])){
					$aff_cids[] = $dataC2[0];
				}
			}
			if(count($aff_cids)==0){
				echo "<td>&nbsp;</td>";
			}
			else{
				echo "<td>".htmlspecialchars(getAffinityCategoryName(implode(', ',$aff_cids)))."</td>";
			}
		}
		elseif($type == 1) { 
			if(empty($extrafield)){
				echo "<td>&nbsp;</td>";
			}
			else{
				echo "<td>$extrafield</td>";
			}
		}
		?><td><?php 
		if($type == 0){
			print number_format($numrows2).' ('.number_format($numrows3).')';
			if($numrows4>0) {
				echo ' ['.number_format($numrows4).']';
			}
		}
		else{
			print number_format($numrows2);
		} 
		?></td>
          </tr>
<?php
	}
?>
	<tr>
	<td colspan="<?php echo $colspan; ?>">
		<table border="0" width="100%" cellspacing = "0"  cellpadding ="5">
			<tr>
				<td>&nbsp;</td>
			</tr>
<?php
$firstlink = '[First]';
$prevlink = '[Prev]';
$nextlink = '[Next]';
$lastlink = '[Last]';
$middlelinks = '';
$limstart = $p;
$limiter = $limit;
$rowcnt = $numrows;
$show = 10;
//first and previous only if not on first
if($limstart>0){
	if($limstart>=$limiter) $prev = $limstart - $limiter;
	else $prev = 0;
	$firstlink = "[<a href=\"{$_SERVER['PHP_SELF']}?p=0$sorttext&noproducts=$noproducts&type=$type\">First</a>]";
	$prevlink = "<a href=\"{$_SERVER['PHP_SELF']}?p=$prev$sorttext&noproducts=$noproducts&type=$type\">&laquo; Prev $limiter</a>";
}
// middle loop through total results
$numbers = ceil($rowcnt/$limiter);
$loopstart = ceil($limstart/$limiter);
if($loopstart<($show-1)) $loopstart = 0; // begin, do not move until 4
if($numbers<$show) $loopend = $numbers; // loopend is less than $show
else $loopend = $loopstart+$show;
if($loopend>$numbers && $loopstart!=0) { // end, show last $show
	$loopstart = $numbers - $show;
	$loopend = $numbers;
}
for($i=$loopstart; $i<$loopend; $i++){
	$startnum = $limiter * $i;
	if($startnum!=$limstart) {
		$middlelinks .= "<a href=\"{$_SERVER['PHP_SELF']}?p=$startnum$sorttext&noproducts=$noproducts&type=$type\">".($i+1)."</a> ";
	}
	else $middlelinks .= ($i+1).' ';
}
//next and last if not on last
if($limstart<$rowcnt && (($limstart+($limiter*2))<$rowcnt || ($rowcnt - ($limstart + $limiter))>0)){
	$next = $limstart + $limiter;
	$nextlink = "<a href=\"{$_SERVER['PHP_SELF']}?p=$next$sorttext&noproducts=$noproducts&type=$type\">Next $limiter &raquo;</a>";
	$lastlink = "[<a href=\"{$_SERVER['PHP_SELF']}?p=".(($numbers-1)*$limiter)."$sorttext&noproducts=$noproducts&type=$type\">Last</a>]";
}

if($middlelinks!='') $middlelinks = "[ $middlelinks ] &nbsp;";
print "<tr><td align=\"center\" class=\"bodytext\">$firstlink &nbsp; $prevlink &nbsp; $middlelinks $nextlink &nbsp; $lastlink</td></tr>";
print "<tr><td align=\"center\" class=\"bodytext\">Showing results ".($limstart+1)." to ";
if($limstart+$limiter < $rowcnt) print ($limstart+$limiter);
else print $rowcnt;
print " of $rowcnt</td></tr>";
?>
		</table>
	</td></tr>
<?php
}
else
{
	echo "<tr><td colspan=\"$colspan\" class=\"error\" align=\"center\">No $category found.</td></tr>";
	echo "<script type=\"text/JavaScript\">
		<!--
      	var el = document.getElementById('delBt');
      	el.style.display='none';
		//-->
      </script>";
}
?>
</table>
<input type="hidden" name="noproducts" value="<?php echo $noproducts; ?>" />
<input type="hidden" name="companyIDs" value="<?php echo implode(',',$coIDs); ?>" />
<input type="hidden" name="submiter" value="1" /></form>
<script type="text/JavaScript">
<!--
function confirmDel()
{
	var goAheadFlag = 0;
	var obj = document.getElementsByName('delID[]');
	for(var i=0;i<obj.length;i++)
	{
		if(obj[i].checked == true)
		{
			goAheadFlag = 1;
		}
	}
	if(goAheadFlag)
	{
		if(confirm("Are you sure?"))
		{
			document.frm3.submit();
			//return true;
		}
		//else
		//{
			return false;
		//}
	}
	else
	{
		alert('Please select at least one record to delete !!!');
		return false;
	}
}

function setAll(nameval)
{
	var obj = document.getElementsByName(nameval);
	if(document.frm3.setUnset.value == 'on')
	{
		for(var i=0;i<obj.length;i++)
		{
			if(obj[i].name != 'noproducts'){
				obj[i].checked = true;
			}
		}
		document.frm3.setUnset.value = '';
	}
	else
	{
		for(var i=0;i<obj.length;i++)
		{
			if(obj[i].name != 'noproducts'){
				obj[i].checked = false;
			}
		}
		document.frm3.setUnset.value = 'on';
	}
}
function emptycompanies(){
	if(document.frm4.noproducts.checked){
		document.frm3.noproducts.value = 1;
	}
	else{
		document.frm3.noproducts.value = 0;
	}
	document.frm3.submit();
}
function doChexChange(field,value){
	var obj = document.getElementById(field+value);
	var chex = '0';
	if(obj.checked){
		chex = '1';
	}
	processajax('addMCfield.php', true, 'POST', 'field='+field+'&value='+obj.value+'&chex='+chex, false, false);
}
//-->
</script>
<?php include 'bottom.php'; ?>umber_format($numrows2