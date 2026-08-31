<?php 
ini_set('max_execution_time', 0);
ini_set("memory_limit", "-1");
set_time_limit(0);
$ALLOW_GROUPS = array(4);
require_once("../auth_auth.php");
$ONLOAD = 'checkAllDeps();';
/*####### START NEW PROMOTION FIELD #########*/
if(isset($_GET['id'])){
	$updID = $_GET['id'];
        $ONLOAD .= 'doPromotionRetailCompany('.$_GET['id'].')';
}
if(isset($_GET['muid'])){
        $ONLOAD .= 'doPromotionRetailCompany('.$_GET['muid'].',isTmp=1)';
	
}
/*####### END NEW PROMOTION FIELD #########*/
include 'top.php';
require_once '../includes/functions.php';

require_once('../vendor/autoload.php');
$process_pdf_html='';
$error_msg = '';
$url = "addproduct.php";
if(isset($_GET['muid'])){
	$disabled = ' disabled="disabled"';
	$url = "addproduct.php?muid=".$_GET['muid'];
	if(isset($_REQUEST['isTmp'])) $url .= '&isTmp=1';
}
else{
	$disabled = '';
}
if(isset($_GET['id'])){
	$updID = $_GET['id'];
	$url = "addproduct.php?id=$updID";
}
elseif(isset($_GET['pid'])) {
	$updID = $_GET['pid'];
}
elseif(isset($_REQUEST['updID'])){
	$updID = $_REQUEST['updID'];
}
else $updID = '';

$page_heading = 'ADD NEW PRODUCT';

if($updID != ''){
	$page_heading = 'UPDATE PRODUCT';
}

if(isset($_REQUEST['new']) && $_REQUEST['new']!="") {
	$page_heading = 'ADD NEW PRODUCT';
	$updID = '';
}

 /* START ADD PRODUCT CONTENT FOR DIGITAL*/
$PDFContent='';
if(isset($updID) and $updID !=''){
    $sqlQ = "SELECT digital_text FROM cscan_digital_od_ads_text WHERE productID ='".$updID."'";       
    $rss = $DRW->query($sqlQ, $DRW_read);
    if($DRW->num_rows($rss)>0) {
        $dataC = $DRW->fetch_row($rss);
        $PDFContent = $dataC[0];
    }
}    
 /* START ADD PRODUCT CONTENT FOR DIGITAL*/  






$fromtemp = false;
include 'addProductPersistenceAndLogic.php';
 // Restrict media channel email case
$disabledButton='';
if($mChannelID==3 ||$mChannelID==1 || $mChannelID==2 || $mChannelID==6 || $mChannelID==14){
  $disabledButton ='disabled'; 
}
include 'addProductFormBuilder.php';

?>
<table width="100%" border="0" cellspacing="0" cellpadding="5" class="text">
	<tr><td align="center" class="adminhead"><?php echo $page_heading; ?></td></tr>
	<tr><td align="right" class="text"><strong><span class="error">* required field</span></strong></td></tr>
	<tr><td align="right" class="text"><strong>* required for approval</strong></td></tr>
	<?php
	if(isset($_GET['more'])){
		echo '<tr><td align="center" class="text">New product has been added successfully.</td></tr>';
	}
	?>
	<tr><td align="center" class="error"><strong><?php 
        // Start Server Side Validation Approved Product Case
        if(isset($_GET['error_req']) && $_GET['error_req']==1){
		echo "Some required field missing, Please enter the mandatory field.";	
	}
        // End Server Side Validation Approved Product Case
	if(isset($_GET['error_pdf'])){
		echo "The Product PDF was not uploaded.<br />Please check that the file is .pdf format less than 32MB in size.";	
	}
	if(isset($_GET['error_pdf2'])){
		echo "The Product PDF was not uploaded.<br />Please review process PDF and if available then try once again.";	
	}
	if(isset($_GET['error_media'])){
		echo "The Product Media was not uploaded.<br />Please check that the file is .swf, .gif, .jpg, .jpeg or .png format less than 32MB in size.";	
	}
	elseif(isset($_GET['img_err'])) {
		echo "Product Image was not uploaded.<br />Please check that the file is of .jpg, .jpeg, .png, or .gif format less than 2MB in size.";
	}
	elseif(isset($_GET['headline_err'])){
		echo "Product Headline already exists.";
	}
	elseif($error_msg!='') {
		echo $error_msg;
	}
	else {
		echo '&nbsp;';
	} 
	?></strong></td></tr>
	<tr>
	<td>
	<form method="post" name="prodForm" action="<?php echo $url; ?>" onsubmit="return validate();" enctype="multipart/form-data"><input type="hidden" name="MAX_FILE_SIZE" value="64000000" />
	<?php 
	foreach($displayKeys as $s=>$title){
		$style = '';
		$part = 1;
                /*####### START NEW PROMOTION FIELD #########*/
		if($s!='top' && $s!='bottom' && $s!='promotion' && $s!='advertiser'){
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
			echo '<table border="0" width="100%" cellpadding="5" cellspacing="0">';
			echo '<tr><td class="bodytext" align="right" width="30%"><strong>'.$title.'</strong></td><td width="70%">&nbsp;</td></tr>';
			echo '</table>';
		}

        foreach ($displayArray[$s] as $d => $display) {
            if($display['title']=='Digital Source :'){
                echo '<div id="div_digital_device" style="overflow:hidden;"><table border="0" width="100%" cellpadding="5" cellspacing="0">';
         
                
            }else{
                echo '<div id="div_'.$s.'_'.$d.'" style="overflow:hidden;"><table border="0" width="100%" cellpadding="5" cellspacing="0">';
            }
            

            if ($display['value'] == '') {
                echo '<tr><td class="bodytext" align="right" width="30%"><strong><em>';

                if ($title != '') {
                    echo $title.' - ';
                }

                echo $display['title'].'</em></strong></td><td width="70%">&nbsp;</td></tr>';
            } else {
                echo '<tr><td class="bodytext" align="right" valign="top" width="30%">'.$display['title'].'</td><td class="bodytext" valign="top" width="70%">'.$display['value'].'</td></tr>';
            }

            echo '</table></div>'."\n";
        }

		if($title!=''){
			echo '<table border="0" width="100%" cellpadding="5" cellspacing="0">';
			echo '<tr><td colspan="2">&nbsp;</td></tr>';
			echo '</table>';
		}
		echo '</div><input type="hidden" name="part_'.$s.'" value="'.$part.'" />';
	}
	?>
	<input type="hidden" name="pcopy_pop" value="" /><input type="hidden" name="save" value="1" /><input type="hidden" name="updID" value="<?php echo $updID;?>" /><input type="hidden" name="curpdf" value="<?php echo $curpdf; ?>" /><input type="hidden" name="curimg" value="<?php echo $curimg; ?>" /><input type="hidden" name="muid" value="<?php echo $muid; ?>" /><input type="hidden" name="old_addedToDatabase" value="<?php echo $addedToDatabase; ?>" /><input type="hidden" name="productStatus" value="1" /><input type="hidden" name="old_productStatus" value="<?php echo $productStatus; ?>" /><input type="hidden" name="old_productStatusDesc" value="<?php echo $productStatusDesc; ?>" /></form>
	</td>
	</tr>
</table>
<?php
include 'addProductJSandPopups.php';
//include 'bottom.php';

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