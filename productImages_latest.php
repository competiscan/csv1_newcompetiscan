<?php
$start_time = microtime(true);
require_once('includes/globalSession.php');
require_once('includes/checklogin.php');
require_once('includes/thumb.php');
/*############START CONVERT EMAIL IMAGE############*/
if(isset($_SESSION['sess_userID'])){
    $sess_userID=$_SESSION['sess_userID'];
} else{
   $sess_userID=''; 
}
/*############END CONVERT EMAIL IMAGE############*/
if(isset($_REQUEST['id'])) {
	$productID = (float)$_REQUEST['id'];
}
else {
	$productID = 0;
}
if(isset($_REQUEST['did'])) {
	$document_id = (int)$_REQUEST['did'];
}
else {
	$document_id = 1;
}
if(isset($_REQUEST['sort'])) {
	$sort = (int)$_REQUEST['sort'];
}
else {
	$sort = -3;
}
$ptext = '';
if(isset($_REQUEST['pp'])) {
	$pp = (int)$_REQUEST['pp'];
	$ptext = 'PowerPoint';
}
else {
	$pp = 0;
}
if(isset($_REQUEST['pdf'])) {
	$pdf = (int)$_REQUEST['pdf'];
	$ptext = 'PDF';
	$pp = 0;
}
else {
	$pdf = 0;
}
$productIDArray = array();
if(isset($_REQUEST['bid'])) {
	$bid = (int)$_REQUEST['bid'];
	if($bid>=0) {
		list($orderby,$dorelev,$doexpans) = doQuerySort($sort);
		list($sql) = doQuery(0, false, '', false, $bid);
		$sql .= $orderby;
		$sql .= " Limit 0,50";
		$rs = $DRW->query($sql,$DRW_read);
		while($row = $DRW->fetch_assoc($rs)) {
			$productIDArray[] = $row['theproductID'];
		}
	}
}
else {
	$bid = -1;
}
if(!empty($productID)){
	$productIDArray[] = $productID;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Product Images</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<script type="text/javascript" src="includes/jsFunctions.js"></script>
<script type="text/javascript" src="includes/ajax.js"></script>
<link href="includes/competiscan_stylesheet.css" rel="stylesheet" type="text/css" />
<style type="text/css">
<!--
.image {border: 1px solid #ccc;height: 52em;overflow: auto;width: 16em;}
.collapsed { display: none; }
.expanded { font-family: verdana; font-size: 11px; text-decoration: none;  color : #000000;}
table.likeresults td { border-width: 1px; padding: 4px; border-style: dotted; border-bottom-color:#D80000; border-left:none; border-right:none; border-top:none; }
.bodytext_small { font-family: arial; font-size: 10px; color: #505050; text-decoration: none; line-height: 18px; }
-->
</style>
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script type="text/javascript">
function select_all_checkbox(){
	var guess_select_all = true;
	var select_all_val = true;
	for(var i=0;i<document.ppForm.elements.length;i++) {
		if(document.ppForm.elements[i].type == 'checkbox'){
			if(guess_select_all){
				if(document.ppForm.elements[i].checked){
					select_all_val = false;
				}
				else{
					select_all_val = true;
				}
				guess_select_all = false;
			}
			document.ppForm.elements[i].checked = select_all_val;
		}
	}
}
function select_column_checkbox(colid){
	var guess_select_all = true;
	var select_all_val = true;
	for(var i=0;i<document.ppForm.elements.length;i++) {
		if(document.ppForm.elements[i].type == 'checkbox'){
			var idparts = document.ppForm.elements[i].id.split('_');
			if(idparts.length>=3 && idparts[1]==colid){
				if(guess_select_all){
					if(document.ppForm.elements[i].checked){
						select_all_val = false;
					}
					else{
						select_all_val = true;
					}
					guess_select_all = false;
				}
				document.ppForm.elements[i].checked = select_all_val;
			}
		}
	}
}
// CONVERT EMAIL IMAGE
function convert_image(pid,dops){
  //alert(pid); 
  //alert(dops); 
   $.ajax({          
         type: "POST",
         url: "ajax_convert_image.php",
         data: {pid:pid,dops:dops,action:'convert_image'},
         success: function(data){
         //alert(data);
             if(data==1){
              location.reload();
            }
        }           
    });
}
</script>
</head>
<body style="background-color:#cccccc;">
<?php
include_once("includes/analyticstracking.php");
if($pp || $pdf){
	echo '<form name="ppForm" method="post" action="exportDocument_latest.php" target="_blank">
	<div style="margin:5px;"><input class="submitbutton" type="submit" name="submit1" value="Export To '.$ptext.'" /></div></div><div style="margin-left:7px;"><a href="#" class="bluelink" onclick="select_all_checkbox(); return false;">Select All</a></div><div>&nbsp;</div>';
	$full = '';
}
else{
	$full = '&amp;full=1';
}
echo '<table><tr>';
$td = false;
$products = count($productIDArray);
if($products > 0) {
	foreach($productIDArray as $pid){
		$productQuery = "SELECT productHeadline,entryID,mChannelID FROM cscan_product_detail WHERE productID=$pid";
		$productQuery = $DRW->query($productQuery,$DRW_read);
		$productRs = $DRW->fetch_array($productQuery);
		$mChannelID=$productRs['mChannelID'];
		if(empty($productRs['entryID'])){
			continue;
		}
		$td = true;
		echo '<td valign="top"><div class="bodytext"><label><input type="checkbox" name="headlines[]" id="headlines_'.$pid.'" value="'.$pid.'" />Product Headline</label><br><strong>'.$productRs['entryID'].'</strong>';
		if($pp || $pdf){
			echo ' &nbsp; <a href="#" class="bluelink" onclick="select_column_checkbox(\''.$pid.'\'); return false;">Select All</a>';
                        /*############START CONVERT EMAIL IMAGE############*/
                        if($mChannelID==3){
                           
                            $pptSql = "SELECT ppt_status FROM cscan_user_single_image_ppt WHERE productID='".$pid."' AND userID='".$sess_userID."'";
                            $pptQuery = $DRW->query($pptSql,$DRW_read);
                            if($DRW->num_rows($pptQuery) > 0)
                            {
                                $pptResult = $DRW->fetch_array($pptQuery);
                                $pppImageStatus=$pptResult['ppt_status'];
                                if($pppImageStatus==1){
                                    echo '<br/><a href="javascript:void(0);" onclick="convert_image('.$pid.',2);" class="bluelink" >Convert In Multiple Image<a/>';
                                }else{
                                  echo '<br/><a href="javascript:void(0);" onclick="convert_image('.$pid.',1)"; class="bluelink" >Convert In Single Image<a/>';      
                                }
                            }else{
                              echo '<br/><a href="javascript:void(0);" onclick="convert_image('.$pid.',1)"; class="bluelink" >Convert In Single Image<a/>';    
                            }       
                        }
                        /*############END CONVERT EMAIL IMAGE############*/
                }
		echo '</div>';
		$page = 0;
		$is_full_one = false;
                $condQuery='';
                if($mChannelID=='5'){
                   $condQuery='';
                } else {
                   $condQuery ='AND document_id='.$document_id;  
                }
                /*############START CONVERT EMAIL IMAGE############*/
                $tablename=' cscan_img_document';
                $pptSql1 = "SELECT ppt_status FROM cscan_user_single_image_ppt WHERE productID='".$pid."' AND userID='".$sess_userID."'";
                $pptQuery1 = $DRW->query($pptSql1,$DRW_read);
                if($DRW->num_rows($pptQuery1) > 0)
                {
                    $pptResult1 = $DRW->fetch_array($pptQuery1);
                    $pppImageStatus1=$pptResult1['ppt_status'];
                    if($pppImageStatus1==1 || $pppImageStatus1==2){
                        $tablename=' cscan_img_document_ppt';
                     }
                }
                /*############END CONVERT EMAIL IMAGE############*/
                 
		 $query2 = "SELECT img_document_sort,UNIX_TIMESTAMP(img_document_createddate),img_document_path,img_document_filename,img_document_content_type FROM {$tablename} WHERE productID=$pid $condQuery ORDER BY img_document_sort";
                 $query_result2 = $DRW->query($query2,$DRW_read);
		while($data2 = $DRW->fetch_row($query_result2)){
			$page = $data2[0];
			$new = $data2[1];
			$img_document_path = $data2[2];
			$img_document_filename = $data2[3];
                        $img_document_content_type=$data2[4];
                       // if($mChannelID='5' || $mChannelID='9' || $mChannelID='10'){
                        if($img_document_content_type=='video/mp4' || empty($img_document_path) ||  empty($img_document_filename)){ 
                           $fname='productImg_latest.php';                             
                        } else{
                             $fname='pdfSample_latest.php';
                        }                       
                    /*	$src = FULL_sample_img_path($img_document_path,$img_document_filename);
			if($page==1 && is_file($src)){
				$is_full_one = true;
			}
			elseif($page>1 && $is_full_one && !is_file($src)){
				break;
			} */
			echo '<div style="margin:5px;"><img src="'.$fname.'?id='.$pid.'&amp;page='.$page.'&amp;new='.$new.$full.'" style="border:none;" /></div>';
			if($pp || $pdf){
				echo '<div class="bodytext">Page '.$page.'<label><input type="checkbox" name="pages[]" id="pages_'.$pid.'_'.$page.'" value="'.$pid.'_'.$page.'" />Include in '.$ptext.'</label></div>';// checked="checked"
			}
			echo '<div>&nbsp;</div>';
		}
		echo '</td>';
	}
}
if(!$td){
	echo '<td>&nbsp;</td>';
}
echo '</tr></table>';
if($pp || $pdf){
	echo '<input type="hidden" name="bid" value="'.$bid.'" /><input type="hidden" name="did" value="'.$document_id.'" /><input type="hidden" name="id" value="'.$productID.'" /><input type="hidden" name="pp" value="'.$pp.'" /><input type="hidden" name="pdf" value="'.$pdf.'" />
	<div style="margin:5px;"><input class="submitbutton" type="submit" name="submit2" value="Export To '.$ptext.'" /></div>
	</form>';
}
?>
</body>
</html>
