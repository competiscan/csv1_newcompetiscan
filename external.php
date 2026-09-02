<?php 
header('Content-Type: text/html; charset=iso-8859-1');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Latest Articles</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="includes/competiscan_stylesheet.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="includes/external.js"></script>
</head>
<body onload="initializeScroller();">
<div id="datacontainer" style="position:absolute;left:1px;top:5px;width:100%;" onmouseover="stop(); return true;" onmouseout="go(); return true;">
<?php
require_once('includes/dbcon.php');

$scrollContent = "";
$articleArray = array();

$apc_site = $_SERVER['HTTP_HOST'];
if(apc_exists($apc_site.'articles')){
	$articleArray = apc_fetch($apc_site.'articles',$success);
	if(!$success){
		$articleArray = array();
	}
}
if(count($articleArray)==0){
	$articleQuery = "select SQL_NO_CACHE * from cscan_article order by postingDate DESC";
	$articleQuery = $DRW->query($articleQuery,$DRW_read);
	while($rs2 = $DRW->fetch_assoc($articleQuery)) {
		$articleArray[] = $rs2;
	}
	apc_store($apc_site.'articles',$articleArray);
}
foreach($articleArray as $rs2){
	$search = array ("'<script[^>]*?>.*?</script>'si",  // Strip out javascript
					"'([\\r\\n])[\\s]+'",                 // Strip out white space
					"'&(quot|#34);'i",                 // Replace html entities
					"'&(amp|#38);'i",
					"'&(lt|#60);'i",
					"'&(gt|#62);'i",
					"'&(nbsp|#160);'i",
					"'&(iexcl|#161);'i",
					"'&(cent|#162);'i",
					"'&(pound|#163);'i",
					"'&(copy|#169);'i",
					"'&#(\\d+);'e");
	
	$scrollContent .= "<table border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"4\" class=\"bodytext\">";
	$scrollContent .= "<tr><td><strong>".$rs2['articleTitle']."</strong></td></tr>";
	$scrollContent .= "<tr><td>[ ".$rs2['postingDate']."]</td></tr>";
	$scrollContent .= "<tr><td>".preg_replace($search," ",$rs2['articleDescription'])."</td></tr>";
	if($rs2['articleThumbImage']!='') {
		$scrollContent .= "<tr><td><img src=\"articleImage/".$rs2['articleThumbImage']."\" style=\"border:solid 1px #000000;\" /></td></tr>";
	}
	if(!empty($rs2['articlePDF'])) {
		$scrollContent .= "<tr><td>&raquo;&raquo;<a class=\"default\" href=\"articlePDF/".$rs2['articlePDF']."\" target=\"_blank\">Click here for more Detail</a></td></tr>";
	}
	$scrollContent .= "<tr><td>......................................................................................................................................................................................................................................................................................</td></tr>";
	$scrollContent .= "</table>";
}
echo $scrollContent;
?>
</div>
</body>
</html>