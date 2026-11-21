<?php 
ini_set( "memory_limit", "70M" );//33554432
$TITLE = 'Competiscan Email';
if(isset($_GET['muid'])) $muid = (int)$_GET['muid'];
else $muid = 0;

$ONLOAD = 'doUPOnload()';
require_once('panelist_top.php');
require_once('includes/clean.php');
require_once($FCKEDITORNAME.'/fckeditor.php');


if(isset($_GET['cetid'])) $edit_cetid = (int)$_GET['cetid'];
else $edit_cetid = 0;

if(isset($_REQUEST['hy'])){
	$hy = (int)$_REQUEST['hy'];
}
if(empty($hy)){
	$hy = '';
	if(!empty($muid)){
		$hycount = 0;
		$y = (int)date('Y');
		do{
			$moreArray = array();
			if(!empty($hy) && $hy>=2013){
				$hm = '01';
				$moreArray[] = intval($hy.$hm);
				$hm = '07';
				$moreArray[] = intval($hy.$hm);
			}
			else{
				$moreArray[] = $hy;
			}
			$yyy = '';
			foreach($moreArray as $yy){
				$query = "SELECT count(*) FROM `cscan_email$yy` WHERE `muid`='".$DRW->real_escape_string($muid)."'";
				$query_result = $DRW->query($query,$DRW_read2);
				$data = $DRW->fetch_row($query_result);
				$hycount = $data[0];
				if($hycount!=0){
					$yyy = $yy;
					break;
				}
			}
			$y--;
			if($hycount==0){
				$hy = $y;
			}
			else{
				$hy = $yyy;
			}
		} while($hycount==0 && $y>=2007);
		if($hy<2007){
			$hy = '';
		}
	}
}

if(checkGroup(20) && isset($_POST['sendemail'])){
	if(isset($_POST['saver'])){ 
            //############################ SHOW SAVE HTML DATA#######################//
                if (strlen(stristr($_POST['productmessage'],"utf-8"))>0) {
                    $setUt8=$_POST['productmessage'];
                 }  else {
		    $setUt8='<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/></meta>'.$_POST['productmessage'];
                 }
                  $sql = "REPLACE INTO `cscan_email_save$hy` SET `muid`='".$DRW->real_escape_string($muid)."',
			`esproduct`='".$DRW->real_escape_string($setUt8)."',`esmessage`='".$DRW->real_escape_string($_POST['messageto'])."'";
            //############################ SHOW SAVE HTML DATA#######################//  
                 /* $sql = "REPLACE INTO `cscan_email_save$hy` SET `muid`='".$DRW->real_escape_string($muid)."',
			`esproduct`='".$DRW->real_escape_string($_POST['productmessage'])."',`esmessage`='".$DRW->real_escape_string($_POST['messageto'])."'";*/
		$DRW->query($sql,$DRW_main);
		
		
		ob_end_clean();
		//header("Location: email.php?muid=$muid&saved=1&hy=$hy");
                header("Location: email_test.php?muid=$muid&saved=1&hy=$hy");
		exit;
	}
	else{
		include('Mail.php');
		include('Mail/mime.php');
		
		if(isset($_POST['attach'])) $attach = $_POST['attach'];
		else $attach = array();
		$forwardto = $_POST['forwardto'];
		$subjectto = $_POST['subjectto'];
		if(isset($_SESSION['ctype'])){
			if($_SESSION['ctype']==1) $fromaddress = 'producers@sbkcenter.com';
			else $fromaddress = 'consumers@sbkcenter.com';
		}
		else $fromaddress = $_POST['email_to'];
		$messageto = $_POST['messageto'];
		$productmessage = $_POST['productmessage'];
		$attachments = '';
		$crlf = "\n"; //if you want to send the generated MIME message using Mail  then you have to set $crlf to "\n"
		$hdrs = array('From'=>$fromaddress,'Subject'=>$subjectto);
		if(isset($_POST['cc_me']) && $AUTH_DATA['user_email']!='') {
			$hdrs['Cc'] = $AUTH_DATA['user_email'];
			//$forwardto .= ','.$AUTH_DATA['user_email'];
		}
		if(isset($_POST['cc_to_alt'])) {
			foreach($_POST['cc_to_alt'] as $cc2){
				if(isset($hdrs['Cc'])) $hdrs['Cc'] .= ','.$cc2;
				else $hdrs['Cc'] = $cc2;
				//$forwardto .= ','.$_POST['cc_to_alt'];
			}
		}
		
		$mime = new Mail_mime($crlf);
		
		$sql = "SELECT COUNT(*) FROM `cscan_product_email` WHERE `muid`='".$DRW->real_escape_string($muid)."' AND isTmp=0";
		$result = $DRW->query($sql,$DRW_read2);
		$count = $DRW->fetch_row($result);
		$DRW->free_result($result);
		if($count[0]>0){
			$link = "https://{$_SERVER['HTTP_HOST']}/admin/addproduct.php?new=1&muid=$muid&hy=$hy";
			
			$mime->setTXTBody("\n$link\n");
			$reg = '/((<\\/body>)?\\s*<\\/html>)/';
			if(preg_match($reg,$messageto)){
				$messageto = preg_replace($reg,'<p><a href="'.$link.'">'.$link.'</a></p>$1',$messageto,1);
			}
			else $messageto .= '<p><a href="'.$link.'">'.$link.'</a></p>';
		}
		if($productmessage!=''){
			$mime->addAttachment($productmessage, 'text/html', 'product'.$muid.'.html', false);
		}
		
		$qf = "SELECT `ceafpath`,`ceaftype` FROM `cscan_email_attach_file$hy` WHERE `muid`='".$DRW->real_escape_string($muid)."' AND isTmp=0";
		$query_resultf = $DRW->query($qf,$DRW_read2);
		if($DRW->num_rows($query_resultf)>0 || count($attach)>0){
			$messageto .= "<p>Files:</p>";
		}
		while($dataf = $DRW->fetch_row($query_resultf)){
			//$mime->addAttachment($dataf[0],$dataf[1]);
			$bname = basename($dataf[0]);
			$messageto .= '<p><a href="'.$displays3URL.$dataf[0].'">'.$bname.'</a></p>';
			
			if($attachments!='') $attachments .= ', ';
			$attachments .= $bname;
		}
		$DRW->free_result($query_resultf);
		
		foreach($attach as $cefid){
			$query2 = "SELECT `cefname`,`ceftype`,`cefdata`,`cefencoding` FROM `cscan_email_file$hy` WHERE `muid`='".$DRW->real_escape_string($muid)."' AND `cefid`='".$DRW->real_escape_string($cefid)."'";
			$query_result2 = $DRW->query($query2,$DRW_read2);
			$data2 = $DRW->fetch_row($query_result2);
			$cefname = $data2[0];
			$ceftype = $data2[1];
			$cefdata = $data2[2];
			$cefencoding = $data2[3];
			$DRW->free_result($query_result2);
			if($cefname=='') $cefname = $ceftype;
			
			$messageto .= '<p><a href="attachment.php?cefid='.$cefid.'&hy='.$hy.'">'.$cefname.'</a></p>';
			
			if($attachments!='') $attachments .= ', ';
			$attachments .= $cefname;
		}
		
		$mime->setHTMLBody($messageto);
		
		$body = $mime->get();
		$headers = $mime->headers($hdrs);
		
		$mail =& Mail::factory('mail','-f'.$fromaddress);
		$send = $mail->send($forwardto, $headers, $body);
		
		if(!PEAR::isError($send)) {
			$sql = "REPLACE INTO `cscan_email_forward$hy` SET `muid`='".$DRW->real_escape_string($muid)."',
				`forward_to`='".$DRW->real_escape_string($forwardto)."',`forward_from`='".$DRW->real_escape_string($fromaddress)."',
				`forward_subject`='".$DRW->real_escape_string($subjectto)."',`forward_message`='".$DRW->real_escape_string($messageto)."',
				`forward_attachments`='".$DRW->real_escape_string($attachments)."',`forward_product`='".$DRW->real_escape_string($productmessage)."'";
			$DRW->query($sql,$DRW_main);
			
			$sql = "DELETE FROM `cscan_email_save$hy` WHERE `muid`='".$DRW->real_escape_string($muid)."'";
			$DRW->query($sql,$DRW_main);
		}
		
		ob_end_clean();
		header("Location: imap.php#muid$muid");
		exit;
	}
}

$query = "SELECT DATE_FORMAT(`email_date`,'%m/%d/%y %l:%i %p'),`email_to`,`email_from`,`email_subject`,`contact_type_m_c`,`panelist_score`,`deleted`,`email_read`,DATE_FORMAT(`email_date`,'%Y-%m-%d'),panelist_id FROM `cscan_email$hy` WHERE `muid`='".$DRW->real_escape_string($muid)."'";
$query_result = $DRW->query($query,$DRW_read2);
$data = $DRW->fetch_row($query_result);
$email_date = $data[0];
$email_to = $data[1];
$email_from = $data[2];
$email_subject = $data[3];
$contact_type_m_c = $data[4];
$panelist_score = $data[5];
$deleted = $data[6];
$email_read = $data[7];
$firstseen = $data[8];
$panelist_id = $data[9];
if($email_read==0){
	$sql = "UPDATE `cscan_email$hy` SET `email_read`=1 WHERE `muid`='".$DRW->real_escape_string($muid)."'";
	$DRW->query($sql,$DRW_main);
}

$query2 = "SELECT `forward_to`,`forward_to_alt` FROM `cscan_filter` ORDER BY `filterdate` DESC LIMIT 1";
$query_result2 = $DRW->query($query2,$DRW_read2);
$data2 = $DRW->fetch_row($query_result2);
$forward_to = $data2[0];
$forward_to_alt = $data2[1];
$forward_to_altArray = explode(',',$forward_to_alt);
$skey = array_search($AUTH_DATA['user_email'],$forward_to_altArray);
if($skey!==false){
	unset($forward_to_altArray[$skey]);
	$forward_to_alt = implode(',',$forward_to_altArray);
}

if(preg_match('/<([^<>]+)>/',$email_from,$match)){
	$email_from_email = $match[1];
}
else $email_from_email = $email_from;

$primary_address_state = -1;
$mChannelID = -1;
$mPanelID = -1;
$incomeID = 0;
$ageID = 0;
$gender = 'N';

$result = $DRW->query("SELECT sugar_id,first_name,last_name,competi_id,email,alt_email,stateID,stateID2,FLOOR(DATEDIFF(CURDATE(),birthdate)/365) as alt_age,gender,incomeID,contactTypeID,birthdate
	FROM cscan_panelists
	WHERE panelist_id=$panelist_id",$DRW_read2);
$data2 = $DRW->fetch_row($result);
if($data2[0]!=''){
	$id = $data2[0];
	$first_name = $data2[1];
	$last_name = $data2[2];
	$competi_id = $data2[3];
	$email1 = trim($data2[4]);
	$email2 = trim($data2[5]);
	$primary_address_state = $data2[6];
	$alt_address_state = $data2[7];
	$alt_age = $data2[8];
	$gender = strtoupper(substr($data2[9],0,1)); // radio M, F
	$incomeID = $data2[10];
	$contactTypeID = $data2[11];
	$birthdate = $data2[12];
	
	$email_from = $first_name.' '.$last_name;
	if($competi_id!='') $email_from .= ' ('.$competi_id.')';
	if(checkGroup(20)){
		$email1 = "<a href=\"https://crm.competiscan.com/index.php?action=DetailView&amp;module=Contacts&amp;record=$id\" class=\"bluelink\" target=\"_blank\">$email1</a>";
	}
	$email_from .= ' &lt;'.$email1.'&gt;';
	
	if($primary_address_state) {
		$email_from .= '<br />State: '.stateName($primary_address_state,true);
		if($alt_address_state) $email_from .= ', '.stateName($alt_address_state,true);
	}
	if($alt_age) {
		if($primary_address_state) $email_from .= ', ';
		else $email_from .= '<br />';
		$email_from .= 'Age: '.$alt_age;
	}
	
	$ageArray = array();
	$sql = "SELECT age_pID,age_pmin,age_pmax FROM cscan_age_product ORDER BY age_psort";
	$result = $DRW->query($sql,$DRW_read2);
	while( $row = $DRW->fetch_row( $result ) ) {
		$ageArray[$row[0]] = array($row[1],$row[2]);
	}
	
	$mChannelID = 3;
	if($contactTypeID==1){
		$mPanelID = 4;
	}
	elseif($contactTypeID==2){
		$mPanelID = 1;
	}
	if($birthdate!='0000-00-00'){
		foreach($ageArray as $aID=>$a_array){
			if($alt_age>=$a_array[0] && $alt_age<=$a_array[1]){
				$ageID = $aID;
				break;
			}
		}
	}
	if($gender!='M' && $gender!='F'){
		$gender = 'N';
	}
	
}
else $email_from = htmlspecialchars($email_from);
?>
<script src="includes/ajax.js?new=200801" type="text/JavaScript"></script>
<script type="text/javascript">
<!--
function doUPOnload(){
	showFiles('files_list.php?muid=<?php echo $muid; ?>&view=1&hy=<?php echo $hy; ?>',document.getElementById('attachment_inputs'));	
}
function insertEdit(cetid){
	var hidname = 'text'+cetid;
	var inserttext = document.mailForm[hidname].value;
	var oEditor = FCKeditorAPI.GetInstance('productmessage');
	oEditor.InsertHtml(inserttext);
	//oEditor.SetHTML(inserttext);
}
function insertBoth(){
	var hidname1 = 'saveprod';
	var hidname2 = 'savemess';
	
	var inserttext1 = document.mailForm[hidname1].value;
	var oEditor = FCKeditorAPI.GetInstance('productmessage');
	oEditor.SetHTML(inserttext1);
	
	var inserttext2 = document.mailForm[hidname2].value;
	var oEditor = FCKeditorAPI.GetInstance('messageto');
	oEditor.SetHTML(inserttext2);
}


function insertCustomSize(){ 
          var html = document.mailForm.productmessage.value;
        var oEditor = FCKeditorAPI.GetInstance('productmessage'); 
        var addcustom = '<style type="text/css">body{height: 842px;width: 595px;margin-left: auto;margin-right: auto;font-size: x-small;}</style></</span>';
            oEditor.InsertHtml(' ');
            oEditor.InsertHtml(addcustom);
            document.getElementById("productmessage").value = addcustom+html;
        
       /* oEditor.SetHTML('');       
            oEditor.SetHTML(addcustom1);
            document.getElementById("productmessage").value = addcustom1;*/
        	
}

    function removeAllLink(){
            var html = document.mailForm.productmessage.value;
            var html = html.replace(/href="([^"]+)/g, 'href="#');        
            var Editor1 = FCKeditorAPI.GetInstance('productmessage');
            Editor1.SetHTML('');       
            Editor1.SetHTML(html);
            document.getElementById("productmessage").value = html;
    }

function doWinSize(posr,bottom){
	var wintext = '';
	var screenH = 0;
	var screenW = 0;
	
	if (screen){
		if (screen.width) {
			screenW = screen.width;
		}
		if (screen.height) {
			screenH = screen.height;
		}
	}
	if(posr){
		var leftr = screenW/2;
		wintext = wintext+', left='+leftr+', top=0';
	}
	else{
		wintext = wintext+', left=0, top=0';
	}
	if(screenH>0 && screenW>0){
		screenW = screenW/2 - 20;
		screenH = screenH - bottom;
		wintext = wintext+', width='+screenW+', height='+screenH;	
	}
	return wintext;
}
function winPop(winloc) {
	var addtext = doWinSize(true,170);
	var wind = window.open(winloc,"winpop","scrollbars=yes, resizable=yes,menubar=yes"+addtext);
	wind.focus();
}
function winPopMessage(winloc) {
	var addtext = doWinSize(true,200);
	var wind = window.open(winloc,"winpop2","scrollbars=yes, resizable=yes, toolbar=yes,location=yes,menubar=yes,status=yes"+addtext);
	wind.focus();
}
function winPopTextMessage(winloc) {
	var addtext = doWinSize(true,170);
	var wind = window.open(winloc,"winpop5","scrollbars=yes, resizable=yes,menubar=yes"+addtext);
	wind.focus();
}
function winPopProduct(winloc) {
	var addtext = doWinSize(false,150);
	var wind = window.open(winloc,"winpop3","scrollbars=yes, resizable=yes,toolbar=no,location=no,menubar=no,status=no"+addtext);
	wind.focus();
}
function winPopEmailLink(winloc) {
        //alert(winloc);
	var addtext = doWinSize(false,150);
	var wind = window.open(winloc,"winpop3","scrollbars=yes, resizable=yes,toolbar=no,location=no,menubar=no,status=no"+addtext);
	wind.focus();
}
function winPopScore(winloc) {
	var wind = window.open(winloc,"winpop4","left=20, top=20, scrollbars=yes, resizable=yes, width=500, height=250,toolbar=no,location=no,menubar=no,status=no");
	wind.focus();
}

function checkSend(){
	if(document.mailForm.clicked.value==2){
		return confirm('Save?');
	}
	else{
		return confirm('Send?');
	}
}
//-->
</script>
<?php
print "<a href=\"imap.php#muid$muid\" class=\"bluelink\">Back to List</a>";
print "<form method=\"post\" name=\"mailForm\" action=\"{$_SERVER['PHP_SELF']}?muid=$muid\" enctype=\"multipart/form-data\" onsubmit=\"return checkSend();\">";
print '<div style="margin:0px;padding:0px;border:solid 1px #0055E3;">';
print "<table width=\"100%\" border=\"0\" cellpadding=\"4\" cellspacing=\"0\" class=\"likeresults\">";
print "<tr><td style=\"background:#0055E3;color:#ffffff;\" class=\"text\">ID</td><td style=\"background:#0055E3;color:#ffffff;\" class=\"text\">Status</td><td style=\"background:#0055E3;color:#ffffff;\" class=\"text\">Subject</td><td style=\"background:#0055E3;color:#ffffff;\" class=\"text\">Sender</td><td style=\"background:#0055E3;color:#ffffff;\" class=\"text\">Point&nbsp;Status</td><td style=\"background:#0055E3;color:#ffffff;\" class=\"text\">Date</td></tr>";

if(isset($messageTypes[$deleted]) && $messageTypes[$deleted]!='') $status = $messageTypes[$deleted];
else $status = '&nbsp;';
if($panelist_score=='') $panelist_score = 'Score';
if(checkGroup(20)){
	$panelist_score = "<a href=\"panelist_score.php?muid=$muid&amp;hy=$hy\" onclick=\"winPopScore('panelist_score.php?muid=$muid&amp;hy=$hy'); return false;\" class=\"bluelink\" id=\"score_link\">$panelist_score</a>";
}
print "<tr><td valign=\"top\" class=\"bodytext\">$muid</td><td valign=\"top\" class=\"bodytext\">$status</td><td valign=\"top\" class=\"bodytext\">".htmlspecialchars($email_subject)."</td><td valign=\"top\" class=\"bodytext\">$email_from</td><td valign=\"top\" class=\"bodytext\">$panelist_score</td><td valign=\"top\" class=\"bodytext\">$email_date</td></tr>";
print "</table>";
print '</div>';

$plink = "temp_product.php?muid=$muid&amp;firstSeen=".urlencode($firstseen)."&amp;p_id=".urlencode($panelist_id)."&amp;p_age=".urlencode($ageID)."&amp;p_state=".urlencode($primary_address_state)."&amp;p_gender=".urlencode($gender)."&amp;p_income=".urlencode($incomeID)."&amp;p_panel=".urlencode($mPanelID)."&amp;p_channel=".urlencode($mChannelID)."&amp;hy=$hy";
if(checkGroup(20) || checkGroup(5)){
	print "<div class=\"section\"><a href=\"$plink\" onclick=\"winPopProduct('$plink'); return false;\" class=\"bluelink\">Manage Product</a></div>";
}

$query2 = "SELECT `efid`,DATE_FORMAT(`efdate`,'%m/%d/%Y'),`forward_to`,`forward_from`,`forward_subject`,`forward_attachments`	
	FROM `cscan_email_forward$hy` WHERE `muid`='".$DRW->real_escape_string($muid)."' AND isTmp=0 ORDER BY `efdate` ASC";
$query_result2 = $DRW->query($query2,$DRW_read2);
if($DRW->num_rows($query_result2)>0){
	print "<div class=\"section\"><table border=\"0\" cellpadding=\"4\" cellspacing=\"2\">
	<tr><td colspan=\"6\">History</td></tr>";
	print "<tr><td valign=\"top\" class=\"bodytext\"><strong>Date</strong></td>
		<td valign=\"top\" class=\"bodytext\"><strong>Forward To</strong></td>
		<td valign=\"top\" class=\"bodytext\"><strong>Subject</strong></td>
		<td valign=\"top\" class=\"bodytext\"><strong>Product Info</strong></td>
		<td valign=\"top\" class=\"bodytext\"><strong>Message</strong></td>
		<td valign=\"top\" class=\"bodytext\"><strong>Attachments</strong></td></tr>";
	while($data2 = $DRW->fetch_row($query_result2)){
		$efid = $data2[0];
		$efdate = $data2[1];
		$forward_to_ef = $data2[2];
		$forward_from_ef = $data2[3];
		$forward_subject_ef = $data2[4];
		$forward_attachments_ef = $data2[5];
		if($forward_attachments_ef=='') $forward_attachments_ef = '&nbsp;';
		print "<tr><td valign=\"top\" class=\"bodytext\">$efdate</td><td valign=\"top\" class=\"bodytext\">$forward_to_ef</td><td valign=\"top\" class=\"bodytext\">$forward_subject_ef</td>
		<td valign=\"top\" class=\"bodytext\"><a href=\"#\" onclick=\"winPopTextMessage('message.php?efid=$efid&amp;product=1&amp;hy=$hy'); return false;\" class=\"bluelink\">View</a></td>
		<td valign=\"top\" class=\"bodytext\"><a href=\"#\" onclick=\"winPopTextMessage('message.php?efid=$efid&amp;hy=$hy'); return false;\" class=\"bluelink\">View</a></td>
		<td valign=\"top\" class=\"bodytext\">$forward_attachments_ef</td></tr>";
	}
	print "</table></div>";
}

$query2 = "SELECT `esproduct`,`esmessage` FROM `cscan_email_save$hy` WHERE `muid`='".$DRW->real_escape_string($muid)."'";
$query_result2 = $DRW->query($query2,$DRW_read2);
if($DRW->num_rows($query_result2)>0){
	print "<div class=\"section\">";
	$data2 = $DRW->fetch_row($query_result2);
	$esproduct = $data2[0];
	$esmessage = $data2[1];
	print "<a href=\"{$_SERVER['PHP_SELF']}?muid=$muid&amp;hy=$hy\" onclick=\"insertBoth(); return false;\" class=\"bluelink\">Insert Saved Product Info/Message</a><input type=\"hidden\" name=\"saveprod\" value=\"".htmlspecialchars($esproduct,ENT_QUOTES)."\" /><input type=\"hidden\" name=\"savemess\" value=\"".htmlspecialchars($esmessage,ENT_QUOTES)."\" />";
	if(isset($_GET['saved'])) print " &nbsp; Saved!";
	print "</div>";
}

$cidArray = array();
$attachmenttext = '';
$query2 = "SELECT `cefid`,`cefname`,`ceftype`,`cefidentification`,`cefsplit` FROM `cscan_email_file$hy` WHERE `muid`='".$DRW->real_escape_string($muid)."' ORDER BY ABS(`cefpart`) ASC";
$query_result2 = $DRW->query($query2,$DRW_read2);
if($DRW->num_rows($query_result2)>0){
	while($data2 = $DRW->fetch_row($query_result2)){
		$cefid = $data2[0];
		$cefname = $data2[1];
		$ceftype = $data2[2];
		$cefidentification = $data2[3];
		$cefsplit = $data2[4];
		
		if($cefidentification!=''){
			if(preg_match('/^<([^<].+[^>])>$/',$cefidentification,$match)){
				$cefidentification = $match[1];
			}
			$cidArray[$cefid] = $cefidentification;
		}
		$attachmenttext .= "<div class=\"section\"><a href=\"attachment.php?cefid=$cefid&amp;hy=$hy\" target=\"_blank\" class=\"bluelink\">$cefname ($ceftype)</a>";// onclick=\"winPop('attachment.php?cefid=$cefid'); return false;\"
		if(preg_match('/\\.(eml|email)$/',$cefname) && !$cefsplit) $attachmenttext .= " &nbsp; [<a href=\"newmessage.php?cefid=$cefid&amp;muid=$muid&amp;hy=$hy\" class=\"bluelink\">Create New Message</a>]";
		$attachmenttext .= " &nbsp; <label><input type=\"checkbox\" name=\"attach[]\" value=\"$cefid\"><span class=\"bodytext\">Include with Forward</span></label></div>";
	}
}

/*$html = 0;
$query2 = "SELECT `cettext`,`cetid` FROM `cscan_email_text$hy` WHERE `cettype`='text/html' AND `muid`='".$DRW->real_escape_string($muid)."' ORDER BY ABS(`cetpart`) ASC";
$query_result2 = $DRW->query($query2,$DRW_read2);
$messagetext = '';
while($data2 = $DRW->fetch_row($query_result2)){
        ######### latest encode message data #################
    	$cettext = $data2[0];
	//$cettext = htmlspecialchars_decode($data2[0]); //latest encode message data
	$cetid = $data2[1];
	foreach($cidArray as $cefid=>$cefidentification){
		$cettext = str_replace("cid:$cefidentification","http://{$_SERVER['HTTP_HOST']}/attachment.php?cefid=$cefid&hy=$hy",$cettext);
	}
	$html++;
        
	print "<div class=\"section\"><a href=\"#\" onclick=\"winPopMessage('showmessage.php?cetid=$cetid&amp;hy=$hy'); return false;\" class=\"bluelink\">HTML message #$html</a> | <a href=\"{$_SERVER['PHP_SELF']}?muid=$muid&amp;cetid=$cetid&amp;hy=$hy\" onclick=\"insertEdit($cetid); return false;\" class=\"bluelink\">Insert Into Product Info</a><input type=\"hidden\" name=\"text$cetid\" value=\"".htmlspecialchars(cleanHTML($cettext),ENT_QUOTES)."\" /></div>";
	if($edit_cetid==$cetid || $edit_cetid==0){
		$messagetext = cleanHTML($cettext);
	}
}
 * 
 */
//############################ SHOW SAVE DATA#######################//
$html = 0;
$query = "SELECT `muid` FROM `cscan_email_save$hy` WHERE `muid`='".$DRW->real_escape_string($muid)."'"; 
$result = $DRW->query($query, $DRW_read2);
    if ($DRW->num_rows($result) > 0) {
       // echo "shhshs"; die;
        $query2 = "SELECT `esproduct`,`muid`,`esmessage` FROM `cscan_email_save$hy` WHERE `muid`='".$DRW->real_escape_string($muid)."'";    
        $is_saved=0;
    
    }else{
        //$query2 = "SELECT `cettext`,`cetid` FROM `cscan_email_text$hy` WHERE `cettype`='text/html' AND `muid`='".$DRW->real_escape_string($muid)."' ORDER BY ABS(`cetpart`) ASC";
        $query2 = "SELECT `cettext`,`cetid` FROM `cscan_email_text$hy` WHERE `cettype`='text/html' AND `muid`='".$DRW->real_escape_string($muid)."' ORDER BY cetid DESC limit 0,1";
        $is_saved=1;
    }
    $query_result2 = $DRW->query($query2,$DRW_read2);
$messagetext = '';
while($data2 = $DRW->fetch_row($query_result2)){ 
     // echo "ofofo"; die;
    	$cettext = $data2[0];
	$cetid = $data2[1];
        
        if($is_saved==1){
            $htmlmsg_link='cetid='.$cetid;
        }else{
             $htmlmsg_link='muid='.$muid;
        }
	//$esmessage = $data2[2];
        foreach($cidArray as $cefid=>$cefidentification){
		$cettext = str_replace("cid:$cefidentification","http://{$_SERVER['HTTP_HOST']}/attachment.php?cefid=$cefid&hy=$hy",$cettext);
	}
	$html++;
	print "<div class=\"section\"><a href=\"#\" onclick=\"winPopMessage('showmessage.php?$htmlmsg_link&amp;hy=$hy'); return false;\" class=\"bluelink\">HTML message #$html</a> | <a href=\"{$_SERVER['PHP_SELF']}?muid=$muid&amp;cetid=$cetid&amp;hy=$hy\" onclick=\"insertEdit($cetid); return false;\" class=\"bluelink\">Insert Into Product Info</a><input type=\"hidden\" name=\"text$cetid\" value=\"".htmlspecialchars(cleanHTML($cettext),ENT_QUOTES)."\" /></div>";
	if($edit_cetid==$cetid || $edit_cetid==0){
		$messagetext = cleanHTML($cettext);
	}
}
//############################ END SHOW SAVE DATA#######################//





//$query2 = "SELECT CONVERT(cettext USING utf8),`cettype`,`cetid` FROM `cscan_email_text$hy` WHERE `cettype`='text/plain' AND `muid`='".$DRW->real_escape_string($muid)."' ORDER BY ABS(`cetpart`) ASC";
$query2 = "SELECT CONVERT(cettext USING utf8),`cettype`,`cetid` FROM `cscan_email_text$hy` WHERE `cettype`='text/plain' AND `muid`='".$DRW->real_escape_string($muid)."' ORDER BY cetid DESC limit 0,1";

$query_result2 = $DRW->query($query2,$DRW_read2);
$textnum = 0;
while($data2 = $DRW->fetch_row($query_result2)){
     ################### latest encode message data #################
        $cettext = $data2[0];
        //$cettext = htmlspecialchars_decode($data2[0]); //latest encode message data
        $cettype = $data2[1];
        $cetid   = $data2[2];
	$cettext = nl2br($cettext);
	$textnum++;
	print "<div class=\"section\"><a href=\"#\" onclick=\"winPopTextMessage('showmessage.php?cetid=$cetid&amp;hy=$hy'); return false;\" class=\"bluelink\">Text message #$textnum</a> | <a href=\"{$_SERVER['PHP_SELF']}?muid=$muid&amp;cetid=$cetid&amp;hy=$hy\" onclick=\"insertEdit($cetid); return false;\" class=\"bluelink\">Insert Into Product Info</a><input type=\"hidden\" name=\"text$cetid\" value=\"".htmlspecialchars($cettext,ENT_QUOTES)."\" /></div>";
	if($edit_cetid==$cetid || $messagetext==''){
		$messagetext = $cettext;
	}
}
//echo $messagetext; die;
print $attachmenttext;

print "<div class=\"section\"><table border=\"0\" cellpadding=\"4\" cellspacing=\"2\" width=\"100%\">";

if(checkGroup(20)){
	print "<tr><td valign=\"top\">Forward To:</td><td><input type=\"text\" name=\"forwardto\" size=\"50\" class=\"input_box\" value=\"".htmlspecialchars($forward_to,ENT_QUOTES)."\" />";
	if($AUTH_DATA['user_email']!='') print "<br /><label><input type=\"checkbox\" name=\"cc_me\" value=\"1\" />Cc: ".htmlspecialchars($AUTH_DATA['user_email'])."</label>";
	//if($forward_to_alt!=$AUTH_DATA['user_email'] && $forward_to_alt!='') {
	foreach($forward_to_altArray as $ft){
		if(!empty($ft)){
			print "<br /><label><input type=\"checkbox\" name=\"cc_to_alt[]\" value=\"".htmlspecialchars($ft,ENT_QUOTES)."\" />Cc: ".htmlspecialchars($ft)."</label>";
		}
	}
	print "</td></tr>";
	print "<tr><td valign=\"top\">Subject:</td><td><input type=\"text\" name=\"subjectto\" size=\"50\" class=\"input_box\" value=\"Info for Temp Product #$muid; ".htmlspecialchars($email_subject,ENT_QUOTES)."\" /></td></tr>";
}
print "<tr><td valign=\"top\">Product Info:</td><td width=\"90%\">";
///print "<textarea name=\"messageto\" rows=\"10\" cols=\"60\" class=\"input_box\">".$messagetext."</textarea>";
//echo $messagetext; die;

/*
if(strstr($messagetext,"<html><head><meta http-equiv='Content-Type' content='text/html; charset=UTF-8'></head>")){
$messagetext = str_replace("<html><head><meta http-equiv='Content-Type' content='text/html; charset=UTF-8'></head>","",$messagetext);

$messagetext = str_replace("<body>","",$messagetext);
$messagetext = str_replace("</body>","",$messagetext);
$messagetext = str_replace("</html>","",$messagetext);
}

$messagetext=iconv('UTF-8', 'ISO-8859-1//IGNORE', trim($messagetext));
$messagetext = preg_replace('/<\\/?zzz[^>]*>/i','',$messagetext);
$messagetext = preg_replace('/<([^>\\s]*@[^>\\s]*)>/','&lt;$1&gt;',$messagetext);
*/



$messagetext = preg_replace('/<\\/?zzz[^>]*>/i','',$messagetext);
$messagetext = preg_replace('/<([^>\\s]*@[^>\\s]*)>/','&lt;$1&gt;',$messagetext);
$srchd=array("â","¢","Â","Ã","Â","Â","€","Â€","");
$repstr=array("","","","","","","","","");

$messagetext =  str_replace($srchd, $repstr , $messagetext);

//$messagetext = htmlentities($messagetext);
//$messagetext = htmlspecialchars_decode($messagetext);
//$messagetext =stripslashes($messagetext);
######### latest encode message data #################
//$messagetext = htmlspecialchars_decode($messagetext); //latest encode message data
//echo $messagetext;
$oFCKeditor = new FCKeditor('productmessage');
$oFCKeditor->BasePath = $FCKEDITORNAME.'/';
$oFCKeditor->Value = trim($messagetext);
$oFCKeditor->Config["CustomConfigurationsPath"] = "../competi_fckconfig.js";
$oFCKeditor->Width  = '100%';
$oFCKeditor->Height = '500';
$oFCKeditor->Create();
print "</td></tr>";
// ################################ Start Show link #########################//
if($contact_type_m_c=='cons_panelist') {
$show_link_data = "show_email_link.php?muid=".$DRW->real_escape_string($muid)."&amp;hy=$hy";
print "<tr><td valign=\"top\"></td><td width=\"40%\">";
print "<div style=\"padding-top:0px;text-align:left;\"><a href=\"javascript:void(0)\" onclick=\"removeAllLink(); return false;\" class=\"bluelink\">Remove All Link</a>";
print "</div><div style=\"padding-top:0px;margin-top:-10px;text-align:right;\"><a class=\"bluelink set_size\" href=\"javascript:void(0)\" onclick=\"insertCustomSize(); return false;\" title=\"Add Custom Size\" value=\"\">Add Custom Size</a></div></td></tr>";
}
/*<a href=\"$show_link_data\" onclick=\"winPopEmailLink('$show_link_data'); return false;\" class=\"bluelink\">Show All Link</a> &nbsp;&nbsp; */
// ################################ End show link #########################//

if(checkGroup(20)){
	print "<tr><td valign=\"top\">Message:</td><td>";
	
	$oFCKeditor = new FCKeditor('messageto');
	$oFCKeditor->BasePath = $FCKEDITORNAME.'/';
	$oFCKeditor->Value = '';
	$oFCKeditor->Config["CustomConfigurationsPath"] = "../competi_fckconfig2.js";
	$oFCKeditor->Width  = '80%';
	$oFCKeditor->Height = '150';
	$oFCKeditor->Create();
	
	print "</td></tr>";
	print "<tr><td valign=\"top\">Attachments:</td><td valign=\"top\">
	<div id=\"attachment_inputs\"></div>
	<div style=\"padding-top:4px;font-weight:bold;\">Please manage files using the link at the bottom of the <a href=\"$plink#attatch_id\" onclick=\"winPopProduct('$plink#attatch_id'); return false;\" class=\"bluelink\">Manage Product</a> window</div>
	</td></tr>";
	print "<tr><td colspan=\"2\">&nbsp;</td></tr>";
	
	print "<tr><td>&nbsp;</td><td><input class=\"button\" type=\"submit\" name=\"sender\" value=\"Send\" onclick=\"document.mailForm.clicked.value = 1;\" /> &nbsp; <input class=\"button\" type=\"submit\" name=\"saver\" value=\"Save Product Info/Message\" onclick=\"document.mailForm.clicked.value = 2;\" /> &nbsp; <input class=\"button\" type=\"submit\" name=\"cancel\" value=\"Cancel\" onclick=\"document.location.href='imap.php#muid$muid'; return false;\" /><input type=\"hidden\" name=\"sendemail\" value=\"1\" /></td></tr>";
	print "</table></div>
	<input type=\"hidden\" name=\"email_to\" value=\"".htmlspecialchars($email_to,ENT_QUOTES)."\" />
	<input type=\"hidden\" name=\"clicked\" value=\"1\" />";
}
else{
	print "</table></div>";
}
print '<input type="hidden" name="hy" value="'.$hy.'" /></form>';

require_once('panelist_bottom.php');
?>
    

