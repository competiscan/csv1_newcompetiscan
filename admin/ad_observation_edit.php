<?php
$ALLOW_GROUPS = array(41);
require_once("../auth_auth.php");
include 'top.php';
require_once("File/PDF.php");
require_once("../includes/functions.php");
require_once("../includes/thumb.php");

$message = "";
if (isset($_POST['submit'])) {
	$observationID = $_POST['observationID'];
        $query = "select simple_domain, site_url, local_path, ad_src_url,date_observed, ad_name from cscan_observation where observationID=$observationID";
	$res = $DRW->query($query,$DRW_read);
        $row = $DRW->fetch_array($res);
        $addedDate      = $row['date_observed'];
        $siteName       = $row['simple_domain'];
        $local_path     = $row['local_path'];
	$full_url	= $row['site_url'];
	$ad_src_url	= $row['ad_src_url'];
	$ad_path = $local_path.$row['ad_name'];
        $ad_path_arr = explode("/",$ad_path);
        $ad_name_arr = explode(".",$ad_path_arr[4]);
	$site_info = getSite($full_url);
        $siteID = $site_info['id'];
        $siteName = $DRW->real_escape_string($site_info['name']);
	$approved_or_not = $_POST['process'];
        if (($approved_or_not == "Move to Online Product") && ($_POST['entry_id'] == '')) {
		if(!empty($ad_path_arr[1]) && !empty($ad_path_arr[2])){
			$addText1 = '';
			$addText2 = '';
			if(preg_match('/^(\\d{4}\\-\\d{2}\\-\\d{2})/',$addedDate,$matches)){
				$firstSeen = $lastSeen = $matches[1];
				$new_entryID = generate_entryID(true,$firstSeen);
				$entryID_sort1 = intval(preg_replace('/[^0-9]+/','',substr($new_entryID,0,10)));
				$entryID_sort2 = intval(substr($new_entryID,11));
				$addText1 = ",entryID,entryID_sort1,entryID_sort2,firstSeen,lastSeen";
				$addText2 = ",'$new_entryID',$entryID_sort1,$entryID_sort2,'$firstSeen','$lastSeen'";
			}
			
			$DRW->query("insert into cscan_product_detail (productName,mChannelID,sectorID,mPanelID,addedToDatabase,actual_addedToDatabase,productStatus$addText1) values('$siteName',5,0,1,'$addedDate',NOW(),2$addText2)",$DRW_main);
			$new_id = $DRW->insert_id($DRW_main);
			$DRW->query("INSERT IGNORE INTO cscan_scsc_product (productID,scsc_sectorID,scsc_categoryID,scsc_subCategoryID) VALUES ($new_id,0,0,0)",$DRW_main);
			updateStateLookup($new_id);

			shell_exec("mkdir ../PDF/".$ad_path_arr[1]."/".$ad_path_arr[2]."/$new_id");
			shell_exec("mv ../".$ad_path_arr[0]."/".$ad_path_arr[1]."/".$ad_path_arr[2]."/".$ad_path_arr[3]."/* ../PDF/".$ad_path_arr[1]."/".$ad_path_arr[2]."/$new_id/");
			
			//drop the image into some HTML, then build a pdf
			$html_out = "
			<html>
				<head><title>Screenshot</title></head>
				<body>
					<div>
					<img src='screenshot.png'>
					</div>
				</body>
			</html>";
			
			$imageObject = imagecreatefrompng(getcwd()."/../PDF/".$ad_path_arr[1]."/".$ad_path_arr[2]."/$new_id/screenshot.png");
			imagejpeg($imageObject, getcwd()."/../PDF/".$ad_path_arr[1]."/".$ad_path_arr[2]."/$new_id/screenshot.jpg");

			list($img_width, $img_height, $img_type, $img_attr) = getimagesize(getcwd()."/../PDF/".$ad_path_arr[1]."/".$ad_path_arr[2]."/$new_id/screenshot.jpg");
			$pdf_width = 215;
			$pdf_height = ($img_height / $img_width * $pdf_width) + 10;
			$pdf = &File_PDF::factory("P","mm",array("$pdf_width","$pdf_height"));
			$pdf->open();
			$pdf->addPage();
			$pdf->setMargins(10,10);
			$pdf->image(getcwd()."/../PDF/".$ad_path_arr[1]."/".$ad_path_arr[2]."/$new_id/screenshot.jpg", 10, 10, 190, 0, 'jpg');
			$pdf->save(getcwd()."/../PDF/".$ad_path_arr[1]."/".$ad_path_arr[2]."/$new_id/screen_pdf.pdf");
			$pdf->close();

			$GLOBALS['AUTH_DATA']['userID'] = 0;
			createPreviewJPG("PDF/".$ad_path_arr[1]."/".$ad_path_arr[2]."/$new_id/", "screen_pdf.pdf",$new_id);

			$ad_name_arr = explode(".",$ad_path_arr[4]);
			if ($ad_name_arr[1] == "swf")
				$content_type = "application/x-shockwave-flash";
			elseif ($ad_name_arr[1] == "gif")
				$content_type = "image/gif";
			elseif (($ad_name_arr[1] == "jpg") || ($ad_name_arr[1] == "jpeg"))
				$content_type = "image/jpeg";
			else
				$content_type = "image/png";

			$root = dirname(__FILE__); 
			$root_arr = explode("/",$root); 
			$root = implode("/",array_slice($root_arr,0,sizeof($root_arr) - 1));
			$GLOBALS['AUTH_DATA']['userID'] = 0;
			
			//echo "new id: $new_id - root: $root - path1: ".$root."/PDF/".$ad_path_arr[1]."/".$ad_path_arr[2]."/$new_id/   path2: ".$ad_path_arr[1]."/".$ad_path_arr[2]."/  4: ".$ad_path_arr[4]."  ctype: $content_type <br /><br />";
			savePDFData($new_id, $root."/PDF/".$ad_path_arr[1]."/".$ad_path_arr[2]."/$new_id/", $ad_path_arr[4],"","/PDF/".$ad_path_arr[1]."/".$ad_path_arr[2]."/$new_id/",false,$content_type);
			createPreviewJPG($root."/PDF/".$ad_path_arr[1]."/".$ad_path_arr[2]."/$new_id/","screen_pdf.pdf",$new_id,true);
			savePDFData($new_id, $root."/PDF/".$ad_path_arr[1]."/".$ad_path_arr[2]."/$new_id/", "screen_pdf.pdf","","/PDF/".$ad_path_arr[1]."/".$ad_path_arr[2]."/$new_id/",false,"application/pdf");

			$DRW->query("delete from cscan_observation where observationID=$observationID",$DRW_main);

			$query = "INSERT INTO cscan_sites_product (sites_id, productID, sp_observation, sp_date, sp_url, sp_image, sp_image_path, ad_url) values(
			$siteID,$new_id,'$addedDate',NOW(),'$full_url', 'screenshot.png', 'PDF/".$ad_path_arr[1]."/".$ad_path_arr[2]."/".$new_id."/', '$ad_src_url')";
			$DRW->query($query,$DRW_main);
			unlink("../PDF/".$ad_path_arr[1]."/".$ad_path_arr[2]."/$new_id/screenshot.jpg");
			header("Location: addproduct.php?id=$new_id");
		}
        } elseif ($approved_or_not == "Move to Online Product" && !empty($local_path)) {
		$entryID = $_POST['entry_id'];
		$res = $DRW->query("SELECT productID,lastSeen from cscan_product_detail where entryID='$entryID'",$DRW_read);
		list($productID,$lastSeen) = $DRW->fetch_array($res);
		if ($productID > 0) {
			$res = $DRW->query("SELECT document_path from cscan_document where productID=$productID",$DRW_read);
			list($screen_path) = $DRW->fetch_array($res);
			$sp_image = 'screenshot_'.$siteID.'_'.time().'.png';

			$query = "INSERT INTO cscan_sites_product (sites_id, productID, sp_observation, sp_date, sp_url, sp_image, sp_image_path, ad_url) values(
        	        $siteID,$productID,'$addedDate',NOW(),'$full_url', '$sp_image', '".substr($screen_path,1)."', '$ad_src_url')";
                	$DRW->query($query,$DRW_main);
			
			$seen = substr($addedDate,0,10);
			if($seen>$lastSeen){
				$DRW->query("update cscan_product_detail set lastSeen='$seen' where productID=$productID",$DRW_main);
			}
			
			shell_exec("mv ../".$local_path."screenshot.png ..".$screen_path."$sp_image");
		
			$DRW->query("delete from cscan_observation where observationID=$observationID",$DRW_main);
			shell_exec("rm -rf ../$local_path");
			header("Location: ad_observations.php?added=1");
		} else {
			$message = "Entry ID ($entryID) not found...";
		}
	}
}
?>
<script type="text/javascript" src="../includes/swfobject.js"></script>
<table width='100%' border='0' cellspacing='0' cellpadding='5' class='text' rules='none' bordercolor='#B9B9B9' align='center' style='border-collapse:collapse'>
<tr><td class="adminhead" align='center' colspan='4'>UPDATE ONLINE PRODUCT</td></tr>
<!-- search and right buttons start-->
<tr>
<td colspan='4'>
<table border='0' width='100%' cellspacing="0" cellpadding="0">
<tr valign='top'>
<td align='right' colspan='2'>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="text">
<form method='post' name='frm1'>
<tr>
<td align='right' width="100%">
<span class='error'> * required field</span>
</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>
<!-- search and right buttons close-->
<?php
$observationID = $_GET['oid'];
$query = "select DATE_FORMAT(date_observed,'%m/%d/%Y - %h:%i %p') AS date_observed, simple_domain, local_path, ad_name from cscan_observation where observationID=$observationID";
$rs = $DRW->query($query,$DRW_read);
$resultCount = $DRW->num_rows($rs);
$row = $DRW->fetch_array($DRW->query($query,$DRW_read));
$addedDate      = $row['date_observed'];
$siteName       = $row['simple_domain'];
$local_path     = $row['local_path'];
$ad_name	= $row['ad_name'];
$ad_path = $local_path.$ad_name;
$screenshot_path = $local_path."screenshot.png";
$ad_name_arr = explode(".",$ad_name);
if ($ad_name_arr[sizeof($ad_name_arr) - 1] == "swf") {
	$ad = "<div style=''><div id='flashContent'>
	<a href='http://www.adobe.com/go/getflashplayer' target='_blank'>
	<img src='http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif' alt='Get Adobe Flash player' border='0' /></a>
	</div></div>
	<script type='text/javascript'>
	<!--
		var flashvars = {};
		var params = {
			loop: 'true',
			menu: 'false',
			quality: 'high',
			wmode: 'transparent'
		};
		var attributes = {};
		swfobject.embedSWF('../$ad_path', 'flashContent', '300', '300', '9.0.0', '../includes/expressInstall.swf', flashvars, params, attributes);
	//-->
	</script>";
} else {
	$ad = "<img src='../$ad_path' style='max-width: 400px;' />";
}
$res = $DRW->query("select s.productID, d.entryID, s.sites_id, s.sp_observation from cscan_sites_product s,cscan_product_detail d where s.productID=d.productID and s.ad_url like '%$ad_name%'",$DRW_read);
if ($DRW->num_rows($res) > 0) {
	list($pid, $already_captured_entryID, $sid, $date) = $DRW->fetch_array($res);
	$already_captured_message = "Not yet reviewed.  <br />This ad appears to have been captured before (<a href='../productDetail.php?id=$pid' target='_blank'>see product details</a>).";
} else {
	$already_captured_message = "Not yet reviewed";
	$already_captured_entryID = "";
}
?>
<tr><td colspan='4' align='center' class='error'><?php echo $message; ?></td></tr>
<?php
if($resultCount > 0) {
	?> 
	<tr>
	<td valign="top" style="width: 15%;">&nbsp;</td>
	<td valign="top" style="width: 170px; text-align: right; padding-right: 10px;">Site Name</td>
	<td valign="top" style="width: 450px; text-align: left;"><?php echo $siteName; ?></td>
	<td>&nbsp;</td>
	</tr>
	<tr>
	<td>&nbsp;</td>
	<td valign="top" style="text-align: right; padding-right: 10px;">Date Captured</td>
	<td valign="top" style="text-align: left;"><?php echo $addedDate; ?></td>
	<td>&nbsp;</td>
	</tr>
	<tr>
	<td>&nbsp;</td>
	<td valign="top" style="text-align: right; padding-right: 10px;">Selected Banner Ad</td>
	<td valign="top" style="text-align: left;"><?php echo $ad; ?></td>
	<td>&nbsp;</td>
	</tr>
	<tr>
	<td>&nbsp;</td>
	<td valign="top" style="text-align: right; padding-right: 10px;">Screenshot</td>
	<td valign="top" style="text-align: left;"><img src="../<?php echo $screenshot_path; ?>" style="max-width: 400px; max-height: 300px; padding: 5px; border: 1px solid #000;"><br /><br /></td>
	<td>&nbsp;</td>
	</tr>
	<tr>
	<td>&nbsp;</td>
	<td valign="top" style="text-align: right; padding-right: 10px;">Entry ID</td>
	<td valign="top" style="text-align: left;">
	<input type="text" class="input_box" size="20" name="entry_id" />
	</td>
	<td>&nbsp;</td>
	</tr>
	<tr>
	<td>&nbsp;</td>
	<td valign="top" style="text-align: right; padding-right: 10px;">Current Status</td>
	<td valign="top" style="text-align: left;"><?php echo $already_captured_message; ?></td>
	<td>&nbsp;</td>
	</tr>
	<tr>
	<td>&nbsp;</td>
	<td valign="top" style="text-align: right; padding-right: 10px;">&nbsp;</td>
	<td valign="top" style="text-align: left;">
	<input type="submit" class="button" name="process" value="Move to Online Product" />&nbsp;
	<input type="submit" class="button" name="error_button" value="Error" onClick="location.href='ad_observations.php?error=<?php echo $observationID; ?>'; return false;"/>&nbsp; 
	<input type="submit" class="button" name="cancel_button" value="Cancel" onClick="location.href='ad_observations.php'; return false;">&nbsp; 
	<input type="submit" class="button" name="delete_button" value="Delete" onClick="doProdDelete(<?php echo $observationID; ?>); return false;"></td>
	<td>&nbsp;</td>
	</tr>
	<input type="hidden" name="observationID" value="<?php echo $observationID; ?>">
	<?php
}
else{
	echo "<tr><td colspan='4' class='error' align=center>No observation found.</td></tr>";
	echo "<script>el = document.getElementById('delBt'); el.style.display='none';</script>";
}
echo "<input type='hidden' name='submit' value='1' /></form>";
?>
</table>
<script type="text/JavaScript">
<!--
function doProdDelete(oid){
	if(confirm("Are you sure you want to delete?") ) {
		document.location.href = 'ad_observations.php?delID='+oid;
	}
}
function setAll()
{
  if(document.frm1.setUnset.value == 'on')
  {
    for(i=1;i<document.frm1.elements.length;i++)
    {
      document.frm1.elements[i].checked = true;
    }
    document.frm1.setUnset.value = '';
  }
  else
  {
    for(i=1;i<document.frm1.elements.length;i++)
    {
      document.frm1.elements[i].checked = false;
    }
    document.frm1.setUnset.value = 'on';
  }
}
//-->
</script>
<?php 
function getCleanDomainName($unclean) {
	global $DRW,$DRW_read,$DRW_main,$DRW_crm;                                                                                                                                                                   
	$ad_hosturl     = strtolower($DRW->real_escape_string($unclean));
	$url_arr = explode(".",$ad_hosturl);

	$url_hostname   = $url_arr[sizeof($url_arr) - 2];
	$url_dotcom     = $url_arr[sizeof($url_arr) - 1];

	$url_return_array = array($url_hostname, $url_dotcom, $ad_hosturl);
	return $url_return_array;
}
//<http://www.bankrate.com/articles/one.htm>  <http://news.google.com>
function getSite($full_url) {
	global $DRW,$DRW_read,$DRW_main,$DRW_crm;
	$return_array = array();

	$temp_url_array = parse_url($full_url);
	$clean_domain_array = getCleanDomainName($temp_url_array['host']);
	$site_url       = $clean_domain_array[0].".".$clean_domain_array[1]; //<bankrate.com>, <google.com>
	$site_name      = $clean_domain_array[0]; //<bankrate>, <google>
	$site_dotcom    = $clean_domain_array[1]; //<com>
	$site_fullurl   = $clean_domain_array[2]; //<www.bankrate.com>, <news.google.com>

	$res  = $DRW->query("SELECT sites_id, sites_name from cscan_sites where sites_url='$site_fullurl'",$DRW_read);
	$res2 = $DRW->query("SELECT sites_id, sites_name from cscan_sites where sites_url='$site_url'",$DRW_read);
	if ($DRW->num_rows($res) == 0) { //no matches at all, insert a new row
		if ($DRW->num_rows($res2) == 0) {
			$DRW->query("INSERT into cscan_sites (sites_name, sites_category_id, sites_url, sites_active) values('$site_name',0,'$site_url',1)",$DRW_main);
			$return_array['id'] = $DRW->insert_id($DRW_main);
			$return_array['name'] = $site_name;
		} else { //no exact match, but <bankrate.com>, <google.com> exists
			$data = $DRW->fetch_row($res2);
			$return_array['id'] = $data[0];
			$return_array['name'] = $data[1];
		}
	} else { //exact match for <www.bankrate.com> (not going to happen for www's), <news.google.com> section found
		$data = $DRW->fetch_row($res);
		$return_array['id'] = $data[0];
		$return_array['name'] = $data[1];
	}
	return $return_array;
}
include 'bottom.php';
?>