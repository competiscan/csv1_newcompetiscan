<?php
require_once('includes/globalSession.php');
if(!isset($_SESSION['public_admin_access'])){
	require_once('includes/checklogin.php');
}

if(isset($_REQUEST['id'])) $productID = (int)$_REQUEST['id'];
else $productID = 0;
if(isset($_REQUEST['did'])) $document_id = (int)$_REQUEST['did'];
else $document_id = 1;
/* START ADD PRODUCT CONTENT FOR DIGITAL*/
if(isset($_REQUEST['add'])) $add = (int)$_REQUEST['add'];
else $add = 0;
/* START ADD PRODUCT CONTENT FOR DIGITAL*/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Product Detail</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="includes/competiscan_stylesheet.css" rel="stylesheet" type="text/css" />
</head>
<body>
<table border="0" width="100%" cellpadding="0" cellspacing="0">
<tr><td valign="top"><!--<img src="images/competiscan_logo.jpg" />-->
    <img src="images/competiscan-logo.png" style="max-height: 50px;" border="0" />
    </td></tr>
<tr>
  <td>&nbsp;</td>
</tr>
<tr>
  <td height="1" style="background:#3333FF;"><img src="images/spacer.gif" width="1" height="1" /></td>
</tr>
<tr>
  <td>&nbsp;</td>
</tr>
<tr>
<td class="text" style="text-align:justify;"><?php 
if($document_id!=0){
	$query = "SELECT dts_val FROM cscan_document_text_search WHERE document_id=$document_id AND productID=$productID ORDER BY dts_part ASC";
	$query_result = $DRW->query($query,$DRW_read);
	while($data = $DRW->fetch_row($query_result)){
		echo $data[0];						
	}
} 
/* START ADD PRODUCT CONTENT FOR DIGITAL*/
if($add!=0){
    if($add==1){ 
        $sqlQ = "SELECT digital_text FROM cscan_digital_od_ads_text WHERE productID ='".$productID."'"; 
    }elseif($add==2) { 
        $sqlQ = "SELECT sem_description FROM cscan_digital_sem_ads_text WHERE productID ='".$productID."'";
    } elseif ($add==3){
        $sqlQ = "SELECT digital_text FROM cscan_digital_video_ads_text WHERE productID ='".$productID."'";    
    }
    $rss = $DRW->query($sqlQ, $DRW_read);
    $dataC = $DRW->fetch_row($rss);
   echo $PDFContent = $dataC[0];
	
} 
/* END ADD PRODUCT CONTENT FOR DIGITAL*/
?></td>
</tr>
<tr>
  <td>&nbsp;</td>
</tr>
<tr>
  <td height="1" style="background:#3333FF;"><img src="images/spacer.gif" width="1" height="1" /></td>
</tr>
<tr>
  <td>&nbsp;</td>
</tr>
</table>
</body>
</html>