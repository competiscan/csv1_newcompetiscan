<?php 
$ALLOW_GROUPS = array(4);
require_once("../auth_auth.php");
include 'top.php';
require_once '../includes/functions.php';

//2. Master email a colleague profile, ability to sort by email alert profile sub-categories. 
//IE, If I have a bunch of clients that have signed up for a long term care sub-cat email alert, I want to be able to communicate to this group easily (Pull list [First, Last, Company] of Alert that have Longterm Care)

?>
<table width="100%" border="0" cellspacing="0" cellpadding="5" class="text">
<tr><td class="adminhead" align="center">PROFILE MANAGEMENT</td></tr>
</table>
<form method="post" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<?php 
$sectortext = '';
$ctext = '';
$sctext = '';
$sector = getSector();
foreach($sector as $id=>$name){
	if(checkSector($id)){
		$sectortext .= "<option value=\"$id\"";
		if(isset($_REQUEST['sectorID']) && in_array($id,$_REQUEST['sectorID'])) {
			$sectortext .= " selected=\"selected\"";
		}
		$sectortext .= ">".htmlspecialchars($name)."</option>";
		$category = getCategory($id);
		if($category!==0){
			foreach( $category as $cid=>$cname ) {
				if(checkCategory($cid)){
					$ctext .= "<option value=\"$cid\"";
					if(isset($_REQUEST['categoryID']) && in_array($cid,$_REQUEST['categoryID'])) {
						$ctext .= " selected=\"selected\"";
					}
					$ctext .= ">".htmlspecialchars($cname)."</option>";
					$scats = getSubCategory($cid);
					if($scats!==0){
						foreach( $scats as $scid=>$scname ) {
							if(checkSubCategory($scid)){
								$sctext .= "<option value=\"$scid\"";
								if(isset($_REQUEST['subCategoryID']) && in_array($scid,$_REQUEST['subCategoryID'])) {
									$sctext .= " selected=\"selected\"";
								}
								$sctext .= ">".htmlspecialchars($scname)."</option>";
							}
						}
					}
				}
			}
		}
	}
}

echo '<div class="text" style="margin-left:10px;float:left;"><strong>Sector</strong><br /><select class="combo_box" id="sector" name="sectorID[]" size="4" multiple="multiple">'.$sectortext.'</select><br />[Hold ctrl key for multiple selection]</div>';
echo '<div class="text" style="margin-left:10px;float:left;"><strong>Category</strong><br /><select id="categoryID" name="categoryID[]" class="combo_box" multiple="multiple" size="4">'.$ctext.'</select><br />[Hold ctrl key for multiple selection]</div>';
echo '<div class="text" style="margin-left:10px;float:left;"><strong>Sub Category</strong><br /><select id="subCategoryID" name="subCategoryID[]" class="combo_box" size="4" multiple="multiple">'.$sctext.'</select><br />[Hold ctrl key for multiple selection]</div>';
?>
<div style="clear:left;padding:10px;"><input class="button" type="submit" name="submit1" value="Show" /> &nbsp; <input class="button" type="submit" name="clear1" value="Clear" onclick="document.location='<?php echo $_SERVER['PHP_SELF']; ?>'; return false;" /> &nbsp; &nbsp; <input class="button" type="submit" name="export" value="Export" /></div>
</form>
<?php
if(isset($_POST['sectorID']) || isset($_POST['categoryID']) || isset($_POST['subCategoryID'])){
	?>
	<table width="100%" border="0" cellspacing="0" cellpadding="5" class="text">
	<tr class="head1">
	<td class="adminhead"><strong>Company</strong></td>
	<td class="adminhead"><strong>Last Name</strong></td>
	<td class="adminhead"><strong>First Name</strong></td>
	<td class="adminhead"><strong>Email</strong></td>
	</tr>
	<?php
	$where = '';
	$noterArray = array();
	$multArray = array();
	if(isset($_POST['sectorID'])){
		$multArray['sectorID'] = implode(',',$_POST['sectorID']);
	}
	if(isset($_POST['categoryID'])){
		$multArray['categoryID'] = implode(',',$_POST['categoryID']);
	}
	if(isset($_POST['subCategoryID'])){
		$multArray['subCategoryID'] = implode(',',$_POST['subCategoryID']);
	}
	foreach($multArray as $field=>$val){
		if($val!=''){
			$tmpArray = explode(',',$val);
			$where .= " AND (";
			foreach($tmpArray as $v){
				if($v!='') {
					$where .= " ($field ";
					if(in_array($field,$noterArray)){
						$where .= 'NOT ';
					}
					else{
						$where .= "LIKE '%{$v}%' AND $field ";
					}
					$where .= "REGEXP '[[:<:]]{$v}[[:>:]]')";
					if(in_array($field,$noterArray)){
						$where .= ' AND ';
					}
					else{
						$where .= ' OR ';
					}
				}
			}
			$where = substr($where,0,-4);
			$where .= ")";
		}
	}
	if(isset($_REQUEST['export'])){
		ob_end_clean();
		header("Content-Type: application/vnd.ms-excel");
		header("Content-Disposition: attachment; filename=profiles_".date('Ymd').".csv");
		header("Pragma: no-cache");
		header("Expires: 0");
		echo "Profile Management Export ".date('m/d/y')."\n";
	}
	$className='';
	$q = "SELECT DISTINCT companyName,lastName,firstName,emailAddress FROM cscan_search s join cscan_users u on (s.userID=u.userID) WHERE emailAlert=1 AND active='y' $where ORDER BY companyName,lastName,firstName,emailAddress";
	$result = $DRW->query($q,$DRW_read);
	while($data = $DRW->fetch_row($result)){
		if(isset($_REQUEST['export'])){
			echo csvExcape($data[0]).','.csvExcape($data[1]).','.csvExcape($data[2]).','.csvExcape($data[3])."\n";
		}
		else{
			if($className=='selected-bg') {
				$className='white-bg';
			}
			else {
				$className='selected-bg';
			}
			echo "<tr class=\"$className\"><td>$data[0]</td><td>$data[1]</td><td>$data[2]</td><td>$data[3]</td></tr>";
		}
	}
	if(isset($_REQUEST['export'])){
		exit;
	}
	echo '</table>';
}
include 'bottom.php';

function csvExcape($in,$delim = ','){
	$out = $in;
	if(strpos($out, $delim)!==false || strpos($out, '"')!==false || strpos($out, "\r\n")!==false || strpos($out, "\n")!==false || strpos($out, "\r")!==false || preg_match('/^0+\\d+$/',$out)>0){
		$out = '"'.str_replace('"', '""', $out).'"';
	}
	return $out;
}
?>