<?php 
$ALLOW_GROUPS = array(6);
require_once("../auth_auth.php");
header('Content-Type: text/html; charset=iso-8859-1');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<title>Competiscan: Enhance your competitive skill</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../includes/styleSheet.css" rel="stylesheet" type="text/css" />
</head>
<body>
<?php
if(isset($_REQUEST['cid'])) {
	$cid = (int)$_REQUEST['cid'];
}
else {
	$cid = 0;
}
if(isset($_REQUEST['name'])) {
	$name= $_REQUEST['name'];
}
else {
	$name = '';
}
if(isset($_POST['send']) && $_POST['productName']!=''){
	if(empty($name)) {
		$sql = "INSERT IGNORE INTO cscan_company_productname (productName,companyID) VALUES ('".$DRW->real_escape_string($_POST['productName'])."',$cid)";
		$DRW->query($sql,$DRW_main);
	}
	elseif($name!=$_POST['productName']) {
		$sql = "SELECT count(*) FROM cscan_company_productname WHERE companyID=$cid AND productName='".$DRW->real_escape_string($_POST['productName'])."'";
		$editRS = $DRW->query($sql,$DRW_read);
		$data = $DRW->fetch_array($editRS);
		if(!empty($data[0])){
			$sql = "DELETE FROM cscan_company_productname WHERE companyID=$cid AND productName='".$DRW->real_escape_string($name)."'";
			$DRW->query($sql,$DRW_main);
		}
		else{
			$sql = "UPDATE cscan_company_productname SET productName='".$DRW->real_escape_string($_POST['productName'])."' WHERE companyID=$cid AND productName='".$DRW->real_escape_string($name)."'";
			$DRW->query($sql,$DRW_main);
		}
		
		$sql = "UPDATE cscan_product_detail,cscan_company_product SET cscan_product_detail.productName='".$DRW->real_escape_string($_POST['productName'])."' WHERE cscan_company_product.companyID=$cid AND cscan_company_product.primary_co=1 AND cscan_company_product.productID=cscan_product_detail.productID AND cscan_product_detail.productName='".$DRW->real_escape_string($name)."'";
		$DRW->query($sql,$DRW_main);
	}
	
	ob_end_clean();
	header("Location: ".$_SERVER['PHP_SELF']."?cid=$cid");
	exit;
}
elseif(isset($_GET['del'])){
	$sql = "DELETE FROM cscan_company_productname WHERE companyID=$cid AND productName='".$DRW->real_escape_string($name)."'";
	$DRW->query($sql,$DRW_main);
	
	ob_end_clean();
	header("Location: ".$_SERVER['PHP_SELF']."?cid=$cid");
	exit;
}
if($cid!=0) {
	if(empty($name) && !isset($_REQUEST['new'])){
		$className='white-bg';
		echo '<div style="margin:5px;"><a href="'.$_SERVER['PHP_SELF']."?cid=$cid&amp;new=1".'">Add New Product Name</a></div><table width="100%" border="0" cellspacing="0" cellpadding="5" class="text">';
		$sql = "SELECT productName FROM cscan_company_productname WHERE companyID=$cid";
		$editRS = $DRW->query($sql,$DRW_read);
		while($data = $DRW->fetch_array($editRS)){
			if($className=='selected-bg') {
				$className='white-bg';
			}
			else {
				$className='selected-bg';
			}
			echo '<tr class="'.$className.'"><td valign="top"><a href="'.$_SERVER['PHP_SELF']."?cid=$cid&amp;name=".urlencode($data['productName']).'">'.$data['productName'].'</a></td><td valign="top"><a href="'.$_SERVER['PHP_SELF']."?cid=$cid&amp;name=".urlencode($data['productName']).'&amp;del=1" onclick="return confirm(\'Delete?\');">Delete</a></td></tr>';
		}
		echo '</table>';
	}
	else{
		?>
		<form method="post" name="frm1" action="<?php echo $_SERVER['PHP_SELF']."?cid=$cid"; ?>">
		<table width="100%" border="0" cellspacing="0" cellpadding="4">
		<tr>
		<td class="bodytext" align="right">Product Name:</td>
		<td><input type="text" name="productName" size="20" class="combo_box" maxlength="255" value="<?php echo htmlspecialchars($name,ENT_QUOTES); ?>" /></td>
		</tr>
		<tr>
		<td>&nbsp;</td>
		<td>
		<input class="button" type="submit" name="submit1" value="Save" />
		<input class="button" type="button" value="Cancel" onclick="location.href='<?php echo $_SERVER['PHP_SELF']."?cid=$cid"; ?>'; return false;" />
		</td>
		</tr>
		</table>
		<input type="hidden" name="name" value="<?php echo htmlspecialchars($name,ENT_QUOTES); ?>" /><input type="hidden" name="send" value="1" /></form>
		<?php 
	}
}
?>
</body>
</html>