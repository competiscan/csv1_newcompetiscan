<script type="text/javascript">
function changeView(view) {
	window.location = 'enhancedTracker.php?view='+view;
}
</script>

<?php
$ALLOW_GROUPS = array(43);
require_once("../auth_auth.php");
//include('../includes/pagination.php');
$JQUERY = true;
include 'top.php';

require_once '../includes/paginate.php';


if (isset($_GET['view'])) {
	$view = $_GET['view'];
} else {
	$view = 'product';
}

$paginate = new Paginate();


$product_sql = "
SELECT
pt.productID,
pd.productName,
COUNT(*) as num,
pt.time_accessed
FROM cscan_product_track pt
LEFT JOIN cscan_product_detail pd
ON pt.productID = pd.productID
GROUP BY productID
ORDER BY num DESC
";

$user_sql = "

";


$paginate->paginate($product_sql, 25);

?>
<table width="100%" border="0" cellspacing="0" cellpadding="5" class="text">
  <tr><td class="adminhead" align="center"><div id="pcontainer">ENHANCED PRODUCT TRACKER</div></td></tr>

<tr>
<td class="bodyText">
<input type ="button" value = "View Popular By Product" onclick = "changeView('product')" /> <input type ="button" value = "View Popular By User" onclick = "changeView('user')" /><br /><br />
<?php $paginate->print_page_links(); ?>
<br>
<br>
<table width ="100%" class="text">
	<tr class="head1">
		<td width="5%" class="adminhead">Product ID</td>
		<td width="50%" class="adminhead">Product Name</td>
		<td width="20%" class="adminhead"># of Times Accessed</td>
		<!--<td width="20%" class="adminhead">Last Accessed</td>-->
	</tr>
<?php
$back = '#ffffff';

$rows = $paginate->get_data();

foreach ($rows as $row) {
?>
	<tr style="background-color: <?php echo $back; ?>;" valign="top">
		<td>
			<?php print $row['productID'] ?>
		</td>
		<td>
			<?php print $row['productName'] ?>
		</td>
		<td>
			<?php print $row['num']  ?>
		</td>
		<!--<td>
			<?php print $row['time_accessed']  ?>
		</td>-->
	</tr>
	<?php
		if($back=='#ffffff'){
			$back='#E8E8FF';
		}
		else{
			$back='#ffffff';
		}
	}
?>
</table>
<div>&nbsp;</div>
<?php
$paginate->print_page_links();
?>

</td>
</tr>
</table>

<?php
include 'bottom.php';
?>
