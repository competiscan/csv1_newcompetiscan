<?php 
require_once("../auth_auth.php");
require_once '../includes/functions.php';

$show = 5;
@ob_clean();
if(isset($_REQUEST['findval']) && trim($_REQUEST['findval'])!=''){
	$company = ltrim($_REQUEST['findval']);
	$co = 'company';
	$field = 'company';
	$table = 'cscan_product_detail';
	$form = 'document.forms.prodForm.';
	if(isset($_GET['two'])) {
		$co = 'secondCompany';
	}
	elseif(isset($_GET['three'])) {
		$co = 'publication';
		$field = 'publication';
	}
	elseif(isset($_GET['four'])) {
		$co = 'affinityAssociationVal';
		$field = 'affinityAssociationVal';
	}
	elseif(isset($_GET['five'])) {
		$co = 'm_company';
		//$field = 'companyName';
		//$table = 'cscan_company';
		$form = 'document.forms.mmvform.';
	}
	$query = "SELECT DISTINCT $field FROM $table WHERE $field LIKE '".mysqlLike($company)."%' ORDER BY $field LIMIT ".($show+1);
	$result = $DRW->query($query,$DRW_read);
	$first = true;
	$i = 0;
	while($data = $DRW->fetch_row($result)){
		if($data[0]!='') {
			if($first) {
				$first = false;
				echo '<form name="newcos" action="'.$_SERVER['PHP_SELF'].'" method="post">';
			}
			else {
				echo '<br />';
			}
			if($i>=$show){
				echo "...";
			}
			else{
				echo '<a href="#" style="color: #FFFFFF;" onclick="'.$form.$co.'.value=document.forms.newcos.coval'.$i.'.value; hideCos(); return false;">';
				echo substr($data[0],0,50);
				if(strlen($data[0])>50) {
					echo '...';
				}
				echo '</a><input type="hidden" name="coval'.$i.'" value="'.htmlspecialchars($data[0], ENT_QUOTES).'" />';
			}
			$i++;
		}
	}
	if($i>0) {
		echo '</form>';
	}
}
?>