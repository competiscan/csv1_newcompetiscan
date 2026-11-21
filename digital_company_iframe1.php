<?php  
require_once('includes/globalSession.php');
require_once('includes/checklogin.php');
if(isset($_REQUEST['parent_field'])){
	$parent_field = $_REQUEST['parent_field'];
}
else{
	$parent_field = '';
}
ob_clean();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Competiscan General Search</title>
<script src="includes/ajax.js" type="text/javascript"></script>
<link href="includes/competiscan_stylesheet.css" rel="stylesheet" type="text/css" />
<script src="includes/digital_company_iframe1.js?v=20110608" type="text/javascript"></script>
</head>
<body style="margin:0;padding:0;background-color:#E8E8FF;" onload="<?php if(!empty($parent_field)) echo "parent_object_field='$parent_field'; "; ?>showCoSel(0);">
<form name="companyForm" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" onsubmit="return false;">
<div id="colook"><input type="text" name="companylook" size="36" class="input_box" autocomplete="off" onkeyup="delay=600;startTimer('showCoSel(0)');" />
<img src="images/searching.gif" border="0" style="vertical-align:bottom;visibility:hidden;" id="waitimg" /></div>
<div class="bodytext">[<a href="#" onclick="doSel(); return false;" class="HyperLink">Clear</a>] &nbsp; [<a href="#" onclick="doClear(); return false;" class="HyperLink">Clear All</a>] &nbsp; [<a href="#" onclick="doSelAll(); return false;" class="HyperLink">Select All</a>]</div>
<div id="seltext" style="display:none;"></div>
</form>
</body>
</html>