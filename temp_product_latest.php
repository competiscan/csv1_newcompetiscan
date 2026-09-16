<?php 
$LOGOUT_PAGE = 'content/index.php';
$ALLOW_GROUPS = array(5,20);
require_once("auth_auth.php");
require_once('includes/functions.php');

header('Content-Type: text/html; charset=utf8');

if(isset($_REQUEST['hy'])){
	$hy = (int)$_REQUEST['hy'];
}
if(empty($hy)){
	$hy = '';
}
$PDFContent='';
$updID = '';
$fromtemp = true;
/*####### START NEW PROMOTION FIELD #########*/
/*$sessionID=session_id();
$query_addproduct_temp_del = "DELETE FROM cscan_promotions_temp WHERE userID='".$AUTH_DATA['userID']."' AND session_id='".$DRW->real_escape_string($sessionID)."' AND formtemp=1 AND muid=0";
$result_temp_del = $DRW->query($query_addproduct_temp_del, $DRW_main);*/
/*####### END NEW PROMOTION FIELD #########*/
include 'admin/addProductPersistenceAndLogic_latest.php';

$url = "temp_product_latest.php?muid=".$muid;
if($isTmp){
	$url .= '&amp;isTmp='.$isTmp;
}
$url .= '&amp;hy='.$hy;
$disabled = '';
include 'admin/addProductFormBuilder_latest.php';

?>
<html>
<head>
<title>Competiscan Temp Product</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="includes/competiscan_stylesheet.css" rel="stylesheet" type="text/css" />
<script src="../includes/jsFunctions.js" type="text/JavaScript"></script> 
<style type="text/css">
<!--
 .likeresults td {
	border-width: 1px;
	padding: 4px;
	border-style: dotted;
	border-bottom-color:#D80000;
	border-left:white;
	border-right:white;
	border-top:white;
}
.section {
	margin-top: 8px;
	padding: 4px;
	border: solid 1px #000000;
}
-->
</style>
<script src="includes/ajax.js?new=200801" type="text/JavaScript"></script>
<script type="text/javascript">
<!--
function doOnload(){
	<?php 
	if($muid=='' || $isTmp==1){
		echo 'if(!window.opener.closed){
			window.opener.location.reload();
		}';
	}
	?>
	self.close();
}
function doSendAlert(){
	<?php 
	if($muid==''){
		//SELECT COUNT(*) FROM `` WHERE `muid`='".$DRW->real_escape_string($muid)."' AND isTmp=$isTmp
		$checkq = "SELECT SQL_NO_CACHE ce.muid,ce.isTmp 
			FROM cscan_product_email ce LEFT JOIN cscan_email_forward cf USING(muid,isTmp) 
			WHERE addedToDatabase>CONCAT(DATE_SUB(CURDATE(),INTERVAL 7 DAY),' 23:59:59') AND tmp_admin_userID={$AUTH_DATA['userID']} AND cf.muid IS NULL AND productID=0 ORDER BY ce.muid";
		$check = $DRW->query($checkq,$DRW_read2);
	    $checkCount = $DRW->num_rows($check);	    
	    if($checkCount>0){
	    	$display = '';
	    	while($datat = $DRW->fetch_row($check)){
	    		$display .= '\\n ';
	    		$display .= $datat[0];
	    		if($datat[1]) $display .= 'tmp';
	    	}
	    	echo 'alert("Please Forward:'.$display.'");';
	    }
	}
	?>
	return true;
}
function fixParent(){
	if(!window.opener.closed && window.opener.document.mailForm){
		window.opener.doUPOnload();
	}
}
//-->
</script>
<link rel="stylesheet" href="js_calendar/calendar.css" media="screen" type="text/css" />
</head>
<?php
if(isset($_POST['save'])) {
	echo '<body style="margin:6px;" onunload="fixParent();" onload="doOnload();">';
				
	//ob_end_clean();
	//header("Location: imap.php");
	//exit;
}
else{
	$temp_getj = $temp_get = '?muid='.$muid;
	if($isTmp==1) {
		$temp_get .= '&amp;isTmp=1';
		$temp_getj .= '&isTmp=1';
	}
	$temp_get .= '&amp;hy='.$hy;
	$temp_getj .= '&hy='.$hy;
        /*####### START NEW PROMOTION FIELD #########*/
        $p_extraPram="0,isTmp=1";
        if($muid!=''){
            $p_extraPram="'".$muid."',isTmp=1";
        }
	echo '<body style="margin:6px;" onunload="fixParent();" onload="checkAllDeps();doSendAlert();doPromotionRetailCompany('.$p_extraPram.');';
	if($muid!=''){
		echo 'doUPOnload();';
	}
	echo '">';
	include_once("includes/analyticstracking.php");
	echo '<div class="section" style="background-color:#E8E8FF;"><form method="post" name="prodForm" onsubmit="return validate();" enctype="multipart/form-data" action="'.$_SERVER['PHP_SELF'].$temp_get.'">';
	foreach($displayKeys as $s=>$title){
		$style = '';
		$part = 1;
                /*####### START NEW PROMOTION FIELD #########*/
		if($s!='top' && $s!='bottom' && $s!='promotion'){
			if(!checkSector($s) && !checkCategory($s)){
				continue;
			}
			if(!in_array($s,$sectorID) && !in_array($s,$categoryID)){
				$part = 0;
				$style = ' style="display:none;"';
			}
		}
		echo '<div id="div_'.$s.'"'.$style.'>';
		if($title!=''){
			echo '<table border="0" width="100%" cellpadding="2" cellspacing="0">';
			echo '<tr><td class="bodytext" align="right" width="30%"><strong>'.$title.'</strong></td><td width="70%">&nbsp;</td></tr>';
			echo '</table>';
		}
		foreach($displayArray[$s] as $d=>$display){
                    if($display['title']=='Digital Source :'){
                        echo '<div id="div_digital_device" style="overflow:hidden;"><table border="0" width="100%" cellpadding="5" cellspacing="0">';
         
                
                    }else{
			echo '<div id="div_'.$s.'_'.$d.'"><table border="0" width="100%" cellpadding="2" cellspacing="0">';
                    }   
			if($display['value']==''){
				echo '<tr><td class="bodytext" align="right" width="30%"><strong><em>';
				if($title!=''){
					echo $title.' - ';
				}
				echo $display['title'].'</em></strong></td><td width="70%">&nbsp;</td></tr>';
			}
			else{
				echo '<tr><td class="bodytext" align="right" valign="top" width="30%">'.$display['title'].'</td><td class="bodytext" valign="top" width="70%">'.$display['value'].'</td></tr>';
			}
			echo '</table></div>';
		}
		if($title!=''){
			echo '<table border="0" width="100%" cellpadding="2" cellspacing="0">';
			echo '<tr><td colspan="2">&nbsp;</td></tr>';
			echo '</table>';
		}
		echo '</div><input type="hidden" name="part_'.$s.'" value="'.$part.'" />';
	}
	echo '<input type="hidden" name="save" value="1" /><input type="hidden" name="hy" value="'.$hy.'" /></form>';
	
	if($muid!=''){
		echo '<hr /><table border="0" cellpadding="4" cellspacing="2"><tr><td valign="top">Attachments:</td><td valign="top">
		<div id="attachment_inputs"></div>
		<div id="wait_images"></div>
		<div style="padding-top:4px;font-weight:bold;"><a href="#" name="attatch_id" onclick="addAttach(document.getElementById(\'attatch_id\')); return false;" class="bluelink" id="attatch_id">Add</a></div>
		</td></tr></table><hr />';
		
		echo '<iframe id="uploadframe" name="uploadframe" src="files_up.php'.$temp_get.'" style="display:none;"></iframe>';
	}
	echo '</div><div>&nbsp;</div><div>&nbsp;</div>';
	
	include 'admin/addProductJSandPopups_latest.php';
	?>
	<script type="text/javascript">
	<!--
	function doUPOnload(){
		showFiles('files_list.php<?php echo $temp_getj; ?>',document.getElementById('attachment_inputs'));	
	}
	
	var submitArray = new Array();
	function upload(uploads_next){
		var uf = document.forms['uploadform'+uploads_next];
		var uf_file = document.getElementById('updata'+uploads_next);
		if(uf && uf_file.value!=''){
			var ud = document.getElementById('upload_div'+uploads_next);
			hideBlock(ud);
			showWait(document.getElementById('wait_images'),uploads_next);
			
			submitArray[submitArray.length] = uploads_next;
			if(submitArray.length==1){
				uf.submit();
			}
		}
	}
	
	var waitimage = new Image();
	waitimage.src = 'images/progress-bar.gif';
	
	function showWait(obj,uploads_next){
		var test = document.getElementById('wait_image'+uploads_next);
		if(!test){
			//var newnode = document.createTextNode('Wait...');
			var newnode = document.createElement('img');
			newnode.src = waitimage.src;
			newnode.id = 'wait_image'+uploads_next;
			newnode.style.display = 'block';
			newnode.style.marginTop = '2px';
			//if(obj.childNodes && obj.childNodes.length>0){
			//	var kid = obj.childNodes;
			//	obj.replaceChild(newnode, kid[0]);
			//}
			//else{
				obj.appendChild(newnode);
			//}
		}
		else{
			test.style.display = 'block';
		}
	}
	function removeAttach(ceafid){
		if(confirm('Remove?') && doDelete(ceafid)==1){
			showFiles('files_list.php<?php echo $temp_getj; ?>',document.getElementById('attachment_inputs'));
		}
	}
	function cancelForm(uploads_next){
		hideBlock(document.getElementById('upload_div'+uploads_next));
	}
	function addAttach(posobj){
		var uploads_next = 1;
		if(submitArray.length>0){
			uploads_next = submitArray[submitArray.length-1] + 1;
		}
		var test = document.getElementById('upload_div'+uploads_next);
		if(!test){
			var base_obj = document.getElementById('wait_images');
			var obj = document.createElement('div');
			obj.id = 'upload_div'+uploads_next;
			obj.style.position = 'absolute';
			obj.style.border = 'solid 1px #000000';
			obj.style.padding = '4px';
			obj.style.background = '#DDF9EE';
			obj.style.color = '#ffffff';
			obj.style.zIndex = '100';
			obj.style.left = (findPosX(posobj))+'px';
			obj.style.top = (findPosY(posobj))+'px';
			obj.style.display = 'block';
			obj.style.width = '300px';
			
			var obj2 = document.createElement('form');
			obj2.name = 'uploadform'+uploads_next;
			obj2.id = 'uploadform'+uploads_next;
			obj2.action = 'files_up.php';
			obj2.method = 'post';
			obj2.enctype = 'multipart/form-data';
			obj2.encoding = 'multipart/form-data'; //ie
			obj2.target = 'uploadframe';
			
			obj2.onsubmit = new Function("upload("+uploads_next+"); return false;");
			
			obj.appendChild(obj2);
			
			var obj3 = document.createElement('input');
			obj3.name = 'updata[]';
			obj3.type = 'file';
			obj3.multiple = 'multiple';
			obj3.size = '40';
			obj3.className = 'input_box';
			obj3.style.display = 'block';
			obj3.id = 'updata'+uploads_next;
			obj2.appendChild(obj3);
			
			var obj4 = document.createElement('input');
			obj4.name = 'subby';
			obj4.type = 'submit';
			obj4.value = 'Upload';
			obj4.className = 'button';
			obj4.style.marginTop = '4px';
			obj2.appendChild(obj4);
			
			var obj5 = document.createElement('input');
			obj5.name = 'canceler';
			obj5.type = 'submit';
			obj5.value = 'Cancel';
			obj5.className = 'button';
			obj5.style.marginTop = '4px';
			obj5.style.marginLeft = '8px';
			obj5.onclick = new Function("cancelForm("+uploads_next+"); return false;");
			obj2.appendChild(obj5);
			
			var obj6 = document.createElement('input');
			obj6.name = 'muid';
			obj6.type = 'hidden';
			obj6.value = '<?php echo $muid; ?>';
			obj6.id = 'muid'+uploads_next;
			obj2.appendChild(obj6);
			
			obj6 = document.createElement('input');
			obj6.name = 'isTmp';
			obj6.type = 'hidden';
			obj6.value = '<?php echo $isTmp; ?>';
			obj6.id = 'isTmp'+uploads_next;
			obj2.appendChild(obj6);
			
			obj6 = document.createElement('input');
			obj6.name = 'hy';
			obj6.type = 'hidden';
			obj6.value = '<?php echo $hy; ?>';
			obj6.id = 'hy'+uploads_next;
			obj2.appendChild(obj6);
			
			base_obj.appendChild(obj);
		}
		else{
			var uf = document.forms['uploadform'+uploads_next];
			uf.reset();
			var muid_id = document.getElementById('muid'+uploads_next);
			muid_id.value = '<?php echo $muid; ?>';
			
			test.style.display = 'block';
			test.style.left = (findPosX(posobj))+'px';
			test.style.top = (findPosY(posobj))+'px';
		}
		
	}
	function doDelete(ceafid){
		return processajax('files_del.php', false, 'POST', 'ceafid='+escape(ceafid)+'&hy=<?php echo $hy; ?>', '', '');
	}
	//-->
	</script>
<?php
}

if($mChannelID=='5' || $mChannelID=='9' || $mChannelID=='10'){
    $showdevice=true;
    
}else{
    $showdevice=false;
}
if(!$showdevice){?>
<script type="text/javascript">
    showDigitalSource();
    </script>
  <?php    
} 




?>
</body>
</html>