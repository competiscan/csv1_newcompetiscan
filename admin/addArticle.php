<?php
$ALLOW_GROUPS = array(8);
require_once("../auth_auth.php");
include 'top.php';
require_once '../includes/functions.php';
require_once '../includes/thumb.php';

$page_heading = 'ADD NEW NEWS ARTICLE';
$page_message = 'Please fill following details to add new News article';

if(isset($_GET['id'])) $updID = $_GET['id'];
else $updID = '';
$articleID  =  '';
$articleTitle = '';
$articleDescription = '';
$articlePDF = '';
$articleThumbImage = '';
$articleImage = '';
$postingDate = '';
$showToAll = '';
$articleSector = '';
$sectorArray = array();

if($updID != '') {
	$page_heading = 'UPDATE NEWS ARTICLE';
	$page_message = 'Please fill following details to update this news article';
	
	# RETRIVING  EXISTING INFORMATION ABOUT SPECIFIED ARTICLE
	
	$sql = "select * from cscan_article where articleID ='$updID'";
	$result = $DRW->query( $sql,$DRW_read );
	
	if( $DRW->num_rows( $result ) > 0 ) {
		$data = $DRW->fetch_array($result);
		$articleID  =  $data['articleID'];
		$articleTitle = $data['articleTitle'];
		$articleDescription = $data['articleDescription'];
		$articlePDF = $data['articlePDF'];
		$articleThumbImage = $data['articleThumbImage'];
		$articleImage = $data['articleImage'];
		$postingDate = $data['postingDate'];
		$showToAll = $data['showToAll'];
		$articleSector = $data['articleSector'];
		$sectorArray = explode(",",$articleSector);
	}
}
?>
<table width='100%' border="0" align='center' cellspacing='0' cellpadding='5' class="text">
  <tr><td class="adminhead" align='center' colspan='2'><?php echo $page_heading; ?></td></tr>
  <tr>
    <td>
      <table width='70%' border="0" align='center' cellspacing='0' cellpadding='5' class="text">
        <tr><td colspan='2' align='right'><b><span class='error'>* required field</span></b></td></tr>
<?php
		if( isset($_POST['save']) || isset($_POST['saveAndAdd']) ) {
			$articleTitle = $_POST['articleTitle'];
			$articleDescription = $_POST['articleDescription'];
			$postingDate = $_POST['postingDate'];
			$postingDate = str_replace('/','-',$postingDate);
			if(isset($_POST['delImage'])) {
				$delImage = $_POST['delImage'];
				$delImageName = $_POST['delImageName'];
			}
			else {
				$delImage = '';
				$delImageName = '';
			}
			if(isset($_POST['sector'])) $sector = implode(",",$_POST['sector']);
			else $sector = '';
			if(isset($_POST['showToAll'])) $showToAll = $_POST['showToAll'];
			else $showToAll = 0;
			if(isset($_POST['delPDF'])) {
				$delPDF = $_POST['delPDF'];
				$oldPDFName = $_POST['oldPDFName'];
			}
			else {
				$delPDF = '';
				$oldPDFName = '';
			}
			
			$sql = "select articleTitle from cscan_article where articleTitle='".$DRW->real_escape_string($articleTitle)."' and articleID !='$updID'";
			$result = $DRW->query( $sql,$DRW_read );
			if( $DRW->num_rows($result) == 0 )
			{
				if( $updID == '' ) {
					$sql = "insert into cscan_article set articleTitle='".$DRW->real_escape_string($articleTitle)."', articleDescription='".$DRW->real_escape_string($articleDescription)."', postingDate='$postingDate', articleSector = '$sector',showToAll = '$showToAll'";
					$actMsg = 'added';
					$DRW->query($sql,$DRW_main);
					$ID = $DRW->insert_id($DRW_main);

					#################################### Start S3 Implementation Code ####################################
					$root = dirname(__FILE__);
					if(strpos($root,'/admin')!==false){
						$root = substr($root,0,strpos($root,'/admin'));
					}

					$articlePDF = $ID.'.pdf';
					//$uploadPath = "../articlePDF/$articlePDF";
					if($_FILES['articleImage']['name']!='') {
						$imageName = $_FILES['articleImage']['name'];
						$ext = substr($imageName,strpos($imageName,'.'));
						$articleImage = $ID.$ext;
						$articleThumbImage = 'thumb'.$ID.$ext;
						//$uploadImagePath ="../articleImage/$articleImage";
						//$uploadThumbImagePath = "../articleImage/$articleThumbImage";
					}
					if($_FILES['articlePDF']['tmp_name']!='' ) {
						/*if(move_uploaded_file($_FILES['articlePDF']['tmp_name'] , "$uploadPath")) {
							$pdfsql = "update cscan_article set articlePDF = '$articlePDF' where articleID = '$ID'";
							$DRW->query($pdfsql,$DRW_main);
						}*/
						$result = $s3->putObject([
					        'Bucket' => $bucket_name,
					        'Key'    => 'articlePDF/' . $articlePDF,
					        'SourceFile' => $_FILES['articlePDF']['tmp_name'],
					        'ACL'    => 'public-read',
					        'ContentType'   => $_FILES['articlePDF']['type'],
					        'Metadata'      => array(
					           'string'        => 'string'
					         )
					    ]);
					    if(isset($result['@metadata']['statusCode']) && $result['@metadata']['statusCode'] == 200){
					    	$pdfsql = "update cscan_article set articlePDF = '$articlePDF' where articleID = '$ID'";
							$DRW->query($pdfsql,$DRW_main);
						}
					}
					
					if($_FILES['articleImage']['tmp_name']!='') {
						//if(file_exists($uploadThumbImagePath)) unlink($uploadThumbImagePath);
						
						/*if(move_uploaded_file($_FILES['articleImage']['tmp_name'],"$uploadImagePath")) {
							createthumb("../articleImage/".$articleImage,"../articleImage/".$articleThumbImage,150,100);
							$imagesql= "update cscan_article set articleImage = '$articleImage',articleThumbImage = '$articleThumbImage'  where articleID = '$ID'";
							$DRW->query($imagesql,$DRW_main);
						}*/
						$result = $s3->putObject([
					        'Bucket' => $bucket_name,
					        'Key'    => 'articleImage/' . $articleImage,
					        'SourceFile' => $_FILES['articleImage']['tmp_name'],
					        'ACL'    => 'public-read',
					        'ContentType'   => $_FILES['articleImage']['type'],
					        'Metadata'      => array(
					           'string'        => 'string'
					         )
					    ]);
					    if(isset($result['@metadata']['statusCode']) && $result['@metadata']['statusCode'] == 200){
					    	$ifile = $root.'/articleImage/'.$articleImage;
							$ifileThumb = $root.'/articleImage/'.$articleThumbImage;
					    	if(move_uploaded_file($_FILES['articleImage']['tmp_name'],$ifile)) {
								createthumb($ifile,$ifileThumb,150,100);
							    $result1 = $s3->putObject([
							        'Bucket' => $bucket_name,
							        'Key'    => 'articleImage/' . $articleThumbImage,
							        'SourceFile' => $ifileThumb,
							        'ACL'    => 'public-read',
							        'ContentType'   => $_FILES['articleImage']['type'],
							        'Metadata'      => array(
							           'string'        => 'string'
							         )
							    ]);
							    if(isset($result1['@metadata']['statusCode']) && $result1['@metadata']['statusCode'] == 200){
							    	unlink($ifile);
							    	unlink($ifileThumb);
							    	$imagesql= "update cscan_article set articleImage = '$articleImage',articleThumbImage = '$articleThumbImage'  where articleID = '$ID'";
									$DRW->query($imagesql,$DRW_main);
							    }
							}
						}
					}
					#################################### End S3 Implementation Code ####################################
				}
				else {
					$sql = "update cscan_article set articleTitle='".$DRW->real_escape_string($articleTitle)."', articleDescription='".$DRW->real_escape_string($articleDescription)."', articlePDF='".$DRW->real_escape_string($articlePDF)."', postingDate='$postingDate', articleSector = '$sector',showToAll = '$showToAll' where articleID ='$updID'";
					$actMsg = 'updated';
					$DRW->query($sql,$DRW_main);
					$articlePDF = $updID.'.pdf';

					#################################### Start S3 Implementation Code ####################################
					$root = dirname(__FILE__);
					if(strpos($root,'/admin')!==false){
						$root = substr($root,0,strpos($root,'/admin'));
					}

					//$uploadPath = "../articlePDF/$articlePDF";
					//$deletePath ="../articlePDF/$oldPDFName";
					if($delPDF!='' && $oldPDFName!='') {
						/*if(file_exists($deletePath)) {
							unlink($deletePath);
							$sql = "update cscan_article set articlePDF = '' where articleID = '$updID'";
							$DRW->query($sql,$DRW_main);
						}*/
						$result = $s3->deleteObject([
							'Bucket' => $bucket_name,
							'Key' => 'articlePDF/'.$oldPDFName,
			            ]);
			            $sql = "update cscan_article set articlePDF = '' where articleID = '$updID'";
						$DRW->query($sql,$DRW_main);
					}
					if($delImage!='' && $delImageName!='') {
						/*$delThumb = "../articleImage/$delImageName";
						$delImage = "../articleImage/thumb$delImageName";
						if(file_exists($delImage)) {
							unlink($delImage);			
						}
						if(file_exists($delThumb)) {
							unlink($delThumb);			
						}*/
						$result = $s3->deleteObject([
							'Bucket' => $bucket_name,
							'Key' => 'articleImage/'.$delImageName,
			            ]);
			            $result1 = $s3->deleteObject([
							'Bucket' => $bucket_name,
							'Key' => 'articleImage/'.'thumb'.$delImageName,
			            ]);
						$sql = "update cscan_article set articleImage = '',articleThumbImage = '' where articleID = '$updID'";
						$DRW->query($sql,$DRW_main);
					}
					if($_FILES['articleImage']['name']!='') {
						$imageName = $_FILES['articleImage']['name'];
						$ext = substr($imageName,strpos($imageName,'.'));
						$articleImage = $updID.$ext;
						$articleThumbImage = 'thumb'.$updID.$ext;
						//$uploadImagePath ="../articleImage/$articleImage";
						//$uploadThumbImagePath = "../articleImage/$articleThumbImage";
					}
					if($_FILES['articlePDF']['tmp_name']!='' ) {
						/*if(file_exists($uploadPath)) unlink($uploadPath);
						if(move_uploaded_file($_FILES['articlePDF']['tmp_name'] , "$uploadPath")) {
							$pdfsql = "update cscan_article set articlePDF = '$articlePDF' where articleID = '$updID'";
							$DRW->query($pdfsql,$DRW_main);
						}*/
						$result = $s3->putObject([
					        'Bucket' => $bucket_name,
					        'Key'    => 'articlePDF/' . $articlePDF,
					        'SourceFile' => $_FILES['articlePDF']['tmp_name'],
					        'ACL'    => 'public-read',
					        'ContentType'   => $_FILES['articlePDF']['type'],
					        'Metadata'      => array(
					           'string'        => 'string'
					         )
					    ]);
					    if(isset($result['@metadata']['statusCode']) && $result['@metadata']['statusCode'] == 200){
					    	$pdfsql = "update cscan_article set articlePDF = '$articlePDF' where articleID = '$updID'";
							$DRW->query($pdfsql,$DRW_main);
						}
					}
					if($_FILES['articleImage']['tmp_name']!='') {
						/*if(file_exists($uploadThumbImagePath))
						unlink($uploadThumbImagePath);
						
						if(move_uploaded_file($_FILES['articleImage']['tmp_name'],"$uploadImagePath")) {
							createthumb("../articleImage/".$articleImage,"../articleImage/".$articleThumbImage,150,100);
							$imagesql= "update cscan_article set articleImage = '$articleImage',articleThumbImage = '$articleThumbImage'  where articleID = '$updID'";
							$DRW->query($imagesql,$DRW_main);
						}*/
						$result = $s3->putObject([
					        'Bucket' => $bucket_name,
					        'Key'    => 'articleImage/' . $articleImage,
					        'SourceFile' => $_FILES['articleImage']['tmp_name'],
					        'ACL'    => 'public-read',
					        'ContentType'   => $_FILES['articleImage']['type'],
					        'Metadata'      => array(
					           'string'        => 'string'
					         )
					    ]);
					    if(isset($result['@metadata']['statusCode']) && $result['@metadata']['statusCode'] == 200){
					    	$ifile = $root.'/articleImage/'.$articleImage;
							$ifileThumb = $root.'/articleImage/'.$articleThumbImage;
					    	if(move_uploaded_file($_FILES['articleImage']['tmp_name'],$ifile)) {
								createthumb($ifile,$ifileThumb,150,100);
							    $result1 = $s3->putObject([
							        'Bucket' => $bucket_name,
							        'Key'    => 'articleImage/' . $articleThumbImage,
							        'SourceFile' => $ifileThumb,
							        'ACL'    => 'public-read',
							        'ContentType'   => $_FILES['articleImage']['type'],
							        'Metadata'      => array(
							           'string'        => 'string'
							         )
							    ]);
							    if(isset($result1['@metadata']['statusCode']) && $result1['@metadata']['statusCode'] == 200){
							    	unlink($ifile);
							    	unlink($ifileThumb);
							    	$imagesql= "update cscan_article set articleImage = '$articleImage',articleThumbImage = '$articleThumbImage'  where articleID = '$updID'";
									$DRW->query($imagesql,$DRW_main);
							    }
							}
						}
					}
					#################################### End S3 Implementation Code ####################################
				}
?>
      <tr><td align='center' colspan='2'>News Article has been <?php echo $actMsg;?> sucessfully.</td></tr>
<?php
				$apc_site = $_SERVER['HTTP_HOST'];
				if(apc_exists($apc_site.'articles')){
					apc_delete($apc_site.'articles');
				}
				if( isset($_POST['saveAndAdd']) ){
					ob_end_clean();
					header("Location: addArticle.php");
					exit;
				}
				elseif( isset($_POST['save']) ){
					ob_end_clean();
					header("Location: manageArticle.php");
					exit;
				}
			}
			else{
?>
      <tr>
    <td class='error' align='center' colspan='2'>Article title already exist. <br>Please change the Article Title.</td>
  </tr>
<?php
    		}
  		}
?>
    <tr>
      <td>
        <table border='0' width='100%' align='center' cellpadding="5" cellspacing="0">
        <form method='post' name='articleForm' action='' onsubmit='return validate();' enctype="multipart/form-data">
    
          <!-- Article Title -->
          <tr>
            <td width='40%' class="bodytext" align='right' valign='top'>Article Title<font class='error'>*</font>:</td>
            <td><input type="text" name="articleTitle" size='40' class="input_box" value="<?php echo htmlspecialchars($articleTitle,ENT_QUOTES);?>"></td>
          </tr>
          <!-- Article Description -->
          <tr>
            <td class="bodytext" align='right' valign='top'>Article Description :</td>
            <td><textarea name='articleDescription' class = 'input_box' rows = '5'  cols ='39'><?php echo htmlspecialchars($articleDescription,ENT_QUOTES); ?></textarea>
      <br><a href='#' onclick='doautolink(); return false;'>[click here to create website as link.]</td>
          </tr>
          <!-- Posting Date -->
          <tr>
            <td class="bodytext" align='right' valign='top'>Posting Date <font class='error'>*</font> :</td>
            <td><input type="text" name="postingDate" size='20' class="input_box" value="<?php echo htmlspecialchars($postingDate,ENT_QUOTES);?>" readonly>
      <a href='#' onclick="displayCalendar(document.articleForm.postingDate,'yyyy-mm-dd',this); return false;"><img name="popcal" src="js_calendar/images/getcal.gif" border="0" alt="" style='vertical-align:bottom;'></a></td>
          </tr>
          <!-- Current PDF -->
          <tr>
            <td class="bodytext" align='right' valign='top'>Article PDF:</td>
            <td class="bodytext"><input type="file" name="articlePDF" size='40' maxlength='255' class="input_box"><br>
<?php
	#################################### Start S3 Implementation Code ###########################################
		if($articlePDF!='') {
?>
          Current PDF: <a href="<?= $displays3URL.'articlePDF/'.$articlePDF; ?>" target='_blank'><?php echo htmlspecialchars($articlePDF,ENT_QUOTES);?></a>
          <!--a href="../articlePDF/<?php //echo htmlspecialchars($articlePDF,ENT_QUOTES);?>" target='_blank'><?php //echo htmlspecialchars($articlePDF,ENT_QUOTES);?></a-->
          &nbsp;&nbsp;Delete PDF :<input type = 'checkbox'  name='delPDF' value='1'><input type = 'hidden' name ='oldPDFName' value ='<?php echo htmlspecialchars($articlePDF,ENT_QUOTES);?>'> 
<?php 
		}
?>
        </td>
          </tr>
      <!-- Current Image -->
      <tr>
       <td class="bodytext" align='right' valign='top'>Article Image:</td>
       <td class="bodytext"><input type="file" name="articleImage" size ='40' maxlength='255' class='input_box'>
<?php 
		if($articleThumbImage!='') {
?>
        <a class= 'hlinks' href="#" onclick="showHideImage('img'); return false;" ><b><span id="label">SHOW</span></b></a>
		&nbsp;&nbsp;<input type ='checkbox' name ='delImage' value ='1'>Delete Image<input type = 'hidden' name ='delImageName' value ='<?php echo $articleImage;?>'> 
        <div id ="img" style='display:none' >
        <!--img src= '../articleImage/<?php echo $articleThumbImage; ?>'-->
        <img src="<?= $displays3URL.'articleImage/'.$articleThumbImage; ?>" border="0" style="border:solid 1px #000000;" />
        </div>
<?php 
		} 
		#################################### End S3 Implementation Code ###########################################
?>
       </td>
          <!-- Show To All -->
          <tr valign='top'>
            <td class="bodytext" align='right'></td>
      <?php if($showToAll == 1)  $check=' checked'; 
      else $check = '';?>
            <td class="bodytext"><input type="checkbox" name="showToAll"  value='1'<?php echo $check; ?>>Display To All</td>
          </tr>
          <!-- Sector -->
      <tr>
      <td>&nbsp;</td>
      <td class='bodytext'>
        <fieldset style='margin-right:70;border-color:black;border-width:1px;'>
        <legend> Sector</legend>
<?php
			$sector = getSector();
			foreach($sector as $sectorID => $sectorName) {
				if($updID!='' && in_array($sectorID,$sectorArray)) $checked = 'checked';
				else $checked ='';
				echo "<input type = 'checkbox' id='sector' name='sector[]' value='$sectorID' $checked>$sectorName<br>";
			}
?>
        </fieldset>
      </td>
      </tr>
          <!-- Button -->
          <tr>
<?php
			if( $updID == '' ) {
?>
              <td align='right'><input class='button' type='submit' name='save' value='Save' onClick='return validate();'>&nbsp;</td>
              <td><input class='button' style='width: 120' type='submit' name='saveAndAdd' value='Save &amp; Add More' style='width:110' onClick='return validate();'>
              <input class='button' type='reset' value='Reset'></td>
<?php
			}
			else {
?>
              <td align='right'><input class='button' type='submit' name='save' value='Update' onClick='return validate();'>&nbsp;</td>
              <td><input type='hidden' name='updID' value="<?php echo $updID;?>">
              <input class='button' type='button' value='Cancel' onclick="location.href='manageArticle.php'; return false;"></td>
<?php
			}
?>
          </tr>
        </form>
        </table>
      </td>
    </tr>
</table>
      </td>
    </tr>
</table>
<script type="text/javascript" src="js_calendar/calendar.js"></script>

<script type="text/javascript">
<!--
function validate()
{
  articleTitle = document.articleForm.articleTitle.value = trimspace(document.articleForm.articleTitle.value);
  date = document.articleForm.postingDate.value = trimspace(document.articleForm.postingDate.value);
  if( articleTitle == '' )
  {
    alert('Please enter the article title.');
    document.articleForm.articleTitle.focus();
    return false;
  }
  if( date == '' )
  {
    alert('Please enter the posting date');
    document.articleForm.postingDate.focus();
    return false;
  }
  for(i=0; i<document.articleForm.elements.length; i++)
  {
    if(document.articleForm.elements[i].checked == true)   
    {
      var flag = 1;
      break;
    }
  }
  if( flag != 1)
  {
    alert('please select at least on sector ');
    return false;
  }
  /*for(i=0; i<document.articleForm.sector.length; i++)
  {
    if(document.articleForm.sector[i].checked == true)   
    {
      var flag = 1;
      break;
    }
  }
  if( flag != 1)
  {
    alert('please select at least on sector ');
    return false;
  }*/
 }
 

function showHideImage(id)
 {
   var img = MM_findObj(id);
   var showHideLabel = MM_findObj('label');
   if(showHideLabel.innerHTML == "HIDE")
   {
     showHideLabel.innerHTML = "SHOW";
     img.style.display = "none";
   }
   else
   {
    showHideLabel.innerHTML = "HIDE";
    img.style.display = "";
   }
 }

 function autolink(s) 
{   
   var hlink = /\s(ht|f)tp:\/\/([^ \,\;\:\!\)\(\"\'\<\>\f\n\r\t\v])+/g;
   return (s.replace (hlink, function ($0,$1,$2) { s = $0.substring(1,$0.length); 
               // remove trailing dots, if any
               while (s.length>0 && s.charAt(s.length-1)=='.') 
                s=s.substring(0,s.length-1);
			    a = s.split('/');
				a = a.join('/ ');
				a = a.split('&');
				a = a.join('& ');
               // add hlink
               //alert(s);
               return ' ' + "<a href = '" + s + "' target = '_blank' class = 'default'>" + a + "</a>";
               //return ' ' + s.link(s); 
                                                 }
                     ) 
           );

}

function doautolink()
{
   var bodycontent = document.articleForm.articleDescription.value;
   //alert(document.articleForm.articleDescription.value);
   bodycontent = autolink(bodycontent);
   document.articleForm.elements.articleDescription.value = bodycontent;
}
//-->
</script>
<?php
  include 'bottom.php';
?>
