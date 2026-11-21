<?php 
$PAGE_HEADING = "Retrieval Services";
$TITLE = "Competiscan $PAGE_HEADING";
include 'header_top.php';
require_once('includes/checklogin.php');
require_once('Mail.php');
require_once('Mail/mime.php');
if(!defined('ENV')){
    define('ENV',getenv('SERVER_NAME'));
}
/*######## Start for Page permission ########*/ 
  
//if(ENV == 'localhost' || ENV == 'demo.competiscan.com'){
    if(ENV == 'localhost'){
        $siteUrl='http://localhost/competiscan.com/';
    }elseif(ENV == 'demo.competiscan.com'){
        $siteUrl='http://demo.competiscan.com/';
    }else{
        $siteUrl='https://www.competiscan.com/';
    } 
    $page_permission = getPagePermission();
    if(!empty($_SESSION['sess_search_page_permission'])){
        $page_permission=$_SESSION['sess_search_page_permission'];
    }
    $redirect_page='';
    if(!empty($page_permission)){
        if(!in_array('retrieval_services',$page_permission) AND in_array('power_search',$page_permission)){
            $redirect_page=$siteUrl.'fullsearch.php?searchview=2';

        }else if(!in_array('retrieval_services',$page_permission) AND !in_array('power_search',$page_permission) AND in_array('trend_reports',$page_permission)){
            $redirect_page=$siteUrl.'trend_reports.php';
        }
        if(!in_array('retrieval_services',$page_permission) AND $redirect_page!=''){
           header("Location: $redirect_page");
            die; 
        }           
    }else{
        if(!empty($_SESSION['sess_dashboard'])) {
            $redirect_page=$siteUrl.'dashboard.php';
        }else{
            $redirect_page=$siteUrl.'quickHelp.php';
        }       
        header("Location: $redirect_page");
        die;
    }

//}    
 /*######## End for Page permission ########*/


$message = '';
$success = 0;
$fshowtot = 4;
$uploadedfilename='';
$phone='';
$company='';
$name='';
$error="";
if(isset($_GET['sub']) AND ($_GET['sub']==1)){
    $success = 1;
}

if(isset($_POST['submit']) && $_POST['submit']=='Send') {
    //print_r($_POST);
        if (isset($_POST['name']) && trim($_POST['name'])=="") {
         $error .= "<br />Please enter your name";
        }
        
        if(!empty($_POST['name']) AND !preg_match("/^(?![0-9()]+$)[a-zA-Z0-9() ]{2,}$/",trim($_POST['name']))){
           //$error .= "<br />Please enter a valid name"; 
        }
        if (isset($_POST['email']) && trim($_POST['email'])=="") {
         $error .= "<br />Please enter your email address";
        }
        if (!empty(trim($_POST['email'])) AND !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
          $error .= "<br />Please enter valid email address";  
        }
        $onlyDigits=!preg_match('/^[0-9\-\(\)\/\+\s]*$/',$_POST['phone']);
        $length = strlen($onlyDigits);
        if (!empty($_POST['phone']) AND $onlyDigits) {
            $error .= "<br />Please enter a valid phone number";
        }
        if (isset($_POST['area_of_focus']) && trim($_POST['area_of_focus'])=="") {
             $error .= "<br />Please select area of focus";
        }
        if (isset($_POST['suggestion']) && trim($_POST['suggestion'])=="") {
             $error .= "<br />Please enter your pick-up description.";
        }
        if (isset($_POST['need']) && trim($_POST['need'])=="") {
             $error .= "<br />Please select fulfillment";
        }
        if ($error) {
        echo $result = '<div class="alert alert-danger"><strong>There were error(s) in your form:</strong>'.$error.'</div>';
        }
        //exit();
        if(empty($error) and $error==""){
	$mailTo = $EMAIL_RetrievalService;
	$email = $_POST['email'];
	$company = $_POST['company'];
	$phone = $_POST['phone'];
	$need = $_POST['need'];
        $area_of_focus=$_POST['area_of_focus'];
        $area_of_focus_name=getAreaNameByID($area_of_focus);
	$message = $_POST['suggestion'];
	$name = $_POST['name'];
	$number = date('Ymd');
	
	$sql = "INSERT INTO cscan_retrieval_services (`rs_date`) VALUES (NOW())";
	$DRW->query($sql,$DRW_main);
	$inc = $DRW->insert_id($DRW_main); 
	
	$htmlname = htmlspecialchars($name);
	$htmlemail = htmlspecialchars($email);
	$htmlcompany = htmlspecialchars($company);
	$htmlphone = htmlspecialchars($phone);
	$htmlmessage = nl2br(htmlspecialchars($message));
	$htmlneed = htmlspecialchars($need);
	$htmlneed_area_focus=htmlspecialchars($area_of_focus_name);
	$bodyhtml = <<< MAILBODY
<html>
<body> 
<table border="0" width="80%" cellspacing="0" cellpadding="0" style="border:solid 1px #000000;">
  <tr>
    <td>
      <table border="0" width="100%" cellspacing="0" cellpadding="5" style="font-family: verdana; font-size: 12px; color: #505050; text-decoration: none; line-height: 18px;">
        <tr>
          <td width="30%" valign="top"><strong>Number : </strong></td><td>{$number}_$inc<br /></td> </tr><tr>
          <td width="30%" valign="top"><strong>Name : </strong></td><td>$htmlname<br /></td> </tr><tr>
          <td width="30%" valign="top"><strong>Email Address : </strong></td><td>$htmlemail<br /></td> </tr><tr>
          <td width="30%" valign="top"><strong>Company : </strong></td><td>$htmlcompany<br /></td></tr><tr>
          <td width="30%" valign="top"><strong>Phone Number : </strong></td><td>$htmlphone<br /></td></tr><tr>
          <td width="30%" valign="top"><strong>Area of focus : </strong></td><td>$htmlneed_area_focus<br /></td></tr><tr>
          <td width="30%" valign="top"><strong>Pick-Up Description : </strong></td><td>$htmlmessage<br /></td></tr><tr>
          <td width="30%" valign="top"><strong>When do you need this fulfilled?  : </strong></td><td>$htmlneed<br /></td>
        </tr>
      </table>
    </td>
  </tr>
</table>
</body>
</html>
MAILBODY;
//echo $bodyhtml; die;
	$bodytext = "Number : {$number}_$inc\n\nName : $name\n\nCompany : $company\n\nPhone Number : $phone\n\nArea Of Focus :$area_of_focus_name\n\nPick-Up Description : $message\n\nWhen do you need this fulfilled?  : $need\n\n";

	$subject = "$area_of_focus_name Retrieval Request #{$number}_$inc from $name";
	if($company!='') $subject .= " of $company";
	//echo $subject; die;	
	$crlf = "\n"; //if you want to send the generated MIME message using Mail  then you have to set $crlf to "\n"
	$hdrs = array('From'=>$EMAIL_noreply,'Subject'=>$subject,'Reply-To'=>$email);
	
	$mime = new Mail_mime($crlf);
	$mime->setTXTBody($bodytext);
	$mime->setHTMLBody($bodyhtml);
	$upErrorArray = array();
	$upNameArray = array();
	$uploadnamearray=array();
	for($i=1;$i<=$fshowtot;$i++){
		$error = $_FILES["updata$i"]['error'];
		if($_FILES["updata$i"]['error'] == UPLOAD_ERR_OK) {
			#################################### Start S3 Implementation Code ###########################################
			$uploadName = $_FILES["updata$i"]['name'];
            $uploadName = preg_replace("/[^a-zA-Z0-9.]/", "_", $uploadName);
			$uploadName=rand().$uploadName;
			$uploadnamearray[]=$uploadName;
			$content_type = $_FILES["updata$i"]['type'];
			$result = $s3->putObject([
			    'Bucket' => $bucket_name,
			    'Key'    => 'retrivalservices/' . $uploadName,
			    'SourceFile' => $_FILES["updata$i"]['tmp_name'],
			    'ACL'    => 'public-read',
			    'ContentType'	=> $content_type,
			    'Metadata'      => array(
			       'string'        => 'string'
			     )
			]);   
			$mime->addAttachment(file_get_contents($_FILES["updata$i"]['tmp_name']), $_FILES["updata$i"]['type'], $uploadName, false); 
			#################################### End S3 Implementation Code ###########################################
		}
		elseif($_FILES["updata$i"]['error'] != UPLOAD_ERR_NO_FILE) {
			$upNameArray[] = $_FILES["updata$i"]['name'];
			$upErrorArray[] = $_FILES["updata$i"]['error'];
		}
	}
	$body = $mime->get();
        $headers = $mime->headers($hdrs);
        
    if(count($uploadnamearray)>0){
        $uploadedfilename=implode(',',$uploadnamearray);
    }
    $sql = "INSERT INTO cscan_retrieval_mail (name,email,phone,company,pickup_discription,upload_file,need_fulfill,area_of_focus_id,retid) VALUES ('".$DRW->real_escape_string($name)."','".$DRW->real_escape_string($email)."','".$DRW->real_escape_string($phone)."','".addslashes($company)."','".(htmlentities($message,ENT_QUOTES,'cp1251'))."','".$DRW->real_escape_string($uploadedfilename)."','".$DRW->real_escape_string($need)."','".$DRW->real_escape_string($area_of_focus)."','".$inc."')";
    $DRW->query($sql,$DRW_main);
        
	//$mailTo='devendra.tiwari@nmgtechnologies.com,pradeep.chaurasia@nmgtechnologies.com';
	$mail = Mail::factory('mail','-f'.$EMAIL_error);
	$send = $mail->send($mailTo, $headers, $body);
	//$send=1;
        //if($send){
	if(!PEAR::isError($send)) {
           $success = 1;
            $message = 'Thank you for your request. Our team has received your request. You should receive a confirmation email in your inbox immediately. An analyst will be assigned within one business day. If you do not hear from us, please email our team at contactus@competiscan.com or call us at 1-(312)-488-1810.';
            //$message = 'Thank you for your request. Our team will review your inquiry and will contact you shortly.';
            if(count($upErrorArray)>0){
                $message .= ' <br /> <br />There were errors with the following file(s):<br />';
                foreach($upErrorArray as $key=>$ecode){
                        switch($ecode){
                                case 1: //UPLOAD_ERR_INI_SIZE
                                case 2: //UPLOAD_ERR_FORM_SIZE
                                        $message .= $upNameArray[$key].' exceeded the max file size and was not sent.<br />';
                                        break;
                                case 3: //UPLOAD_ERR_PARTIAL
                                case 4: //UPLOAD_ERR_NO_FILE
                                case 5:
                                case 6: //UPLOAD_ERR_NO_TMP_DIR
                                case 7: //UPLOAD_ERR_CANT_WRITE
                                case 8: //UPLOAD_ERR_EXTENSION
                                        $message .= $upNameArray[$key].' was not sent due to some temporary error.<br />Please try again later.';
                                        break;
                        }
                }
            }else{
                $success_page=$siteUrl.'productPickup.php?sub=1';
                header("Location: $success_page");
                die;
                
            }
	} else {
		$message = 'Your request has not been submitted due to some temporary error.<br />Please try again later.';
        }
    }
    $_POST = array();
}
$query = "SELECT firstName,lastName,companyName FROM cscan_users WHERE userID='".$_SESSION['sess_userID']."'";
$result = $DRW->query($query,$DRW_read);
$data = $DRW->fetch_row($result);
$name = $data[0];
if($name!='' && $data[1]!='') {
	$name .= " $data[1]";
}
else {
	$name .= $data[1];
}
$company = $data[2];

$email = $_SESSION['sess_username'];

function get_area_of_foucs() {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    $query = "SELECT id,area_of_focus FROM cscan_area_of_focus ORDER BY area_of_focus";
    $result = $DRW->query($query, $DRW_read);
    $array = array();
    while ($row = $DRW->fetch_array($result)) {
        $array[$row['id']] = $row['area_of_focus'];
    }
    return $array;
}

function getAreaNameByID($ID) {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    if ($ID != '') {
        $sql = "SELECT area_of_focus FROM cscan_area_of_focus WHERE id = $ID";
        $rs = $DRW->query($sql, $DRW_read);
        $types = array();
        while (list($type) = $DRW->fetch_row($rs)) {
            $types[] = $type;
        }
        $types = @implode(', ', $types);
    } else {
        $types = 'N/A';
    }
    return $types;
}
?>
<!--action="<?php //echo $_SERVER['PHP_SELF']; ?>"-->
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" name="frm1" id="frm1" method="post"  enctype="multipart/form-data" class="form-horizontal">
    <input type="hidden" name="MAX_FILE_SIZE" value="8000000">
<?php
    if ($success == 0) {
        echo '<p><strong>Please complete the following information and click send.</strong></p>';

        if ($message != '') {
                echo '<div class="alert alert-warning" role="alert">'.$message.'</div>';
        }?>
        <div class="form-group">
          <label for="retrieve-name" class="col-md-2 control-label"><span class="star">*</span> Name</label>
            <div class="col-md-10">
                <input type="text"  name="name" class="form-control" maxlength="200" value="<?php if(isset($_POST['name'])){ echo $_POST['name']; } else{echo htmlspecialchars($name, ENT_QUOTES);} ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="retrieve-email" class="col-md-2 control-label"><span class="star">*</span> Email address</label>
            <div class="col-md-10">
                <input type="text"  name="email" class="form-control" maxlength="200" value="<?php if(isset($_POST['email'])){ echo $_POST['email']; } else{echo htmlspecialchars($email, ENT_QUOTES);} ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="retrieve-company" class="col-md-2 control-label">Company</label>
            <div class="col-md-10">
                <input type="text"  name="company" class="form-control" maxlength="200" value="<?php if(isset($_POST['company'])){ echo $_POST['company']; } else{echo htmlspecialchars($company, ENT_QUOTES);} ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="retrieve-phone" class="col-md-2 control-label">Phone</label>
            <div class="col-md-10">
                <input type="text"  name="phone" class="form-control" maxlength="40" value="<?php if(isset($_POST['phone'])){ echo $_POST['phone']; } ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="retrieve-when" class="col-md-2 control-label"><span class="star">*</span> Area of focus</label>
            <div class="col-md-10">
                <select name="area_of_focus" class="form-control" >
                <option value="">Please Select</option>
                <?php 
                $area_of_foucs = get_area_of_foucs();
                foreach($area_of_foucs as $id=>$name ) { ?>
                <option <?php if(isset($_POST['area_of_focus']) && $_POST['area_of_focus']==$id){ echo "Selected"; } ?> value="<?php echo $id;?>"><?php echo $name;?> </option>
                <?php } ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="retrieve-pickup" class="col-md-2 control-label"><span class="star">*</span> Pick-up description</label>
            <div class="col-md-10">
                <textarea  name="suggestion" cols="39" rows="10" class="form-control" ><?php if(isset($_POST['suggestion'])){ echo $_POST['suggestion']; } ?></textarea>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-2 control-label">Files</label>
            <div class="col-md-10">
    <?php 
            for ($i = 1; $i <= $fshowtot; $i++) {
                    echo '<input name="updata'.$i.'" type="file" style="margin-bottom:0.5em;display:';
                    echo ($i == 1) ? 'block' : 'none';
                    echo ';">';
            }?>	
            <a href="#" id="moreid" onclick="moreFiles(); return false;" class="Hyperlink">More files</a>
            </div>
        </div>

        <div class="form-group">
            <label for="retrieve-when" class="col-md-2 control-label"><span class="star">*</span> When do you need this fulfilled?</label>
            <div class="col-md-10">
                <select name="need" class="form-control"  >
                <option value="">Please Select</option>
                <option <?php if(isset($_POST['need']) && $_POST['need']=="ASAP"){ echo "Selected"; } ?> value="ASAP">ASAP</option>
                <option <?php if(isset($_POST['need']) && $_POST['need']=="Within One Week"){ echo "Selected"; } ?> value="Within One Week">Within One Week</option>
                <option <?php if(isset($_POST['need']) && $_POST['need']=="Within Two Weeks"){ echo "Selected"; } ?> value="Within Two Weeks">Within Two Weeks</option>
                <option <?php if(isset($_POST['need']) && $_POST['need']=="Within One Month"){ echo "Selected"; } ?> value="Within One Month">Within One Month</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <div class="col-md-offset-2 col-md-10">
                <input type="submit" name="submit" value="Send" class="submitbutton">
            </div>
        </div>
<?php
    } elseif ($message!='') {
        echo '<div class="alert alert-info" role="alert">'.$message.'</div>';
    }elseif ($success == 1) {
        $message = 'Thank you for your request. Our team has received your request. You should receive a confirmation email in your inbox immediately. An analyst will be assigned within one business day. If you do not hear from us, please email our team at contactus@competiscan.com or call us at 1-(312)-488-1810.';
        //$message = 'Thank you for your request. Our team will review your inquiry and will contact you shortly.';
        echo '<div class="alert alert-info" role="alert">'.$message.'</div>';
    }
?>
</form>
<?php
include 'footer_bottom.php';
?>
<script src="includes/jquery.js" type="text/javascript"></script><script src="includes/jquery.validate.min.js" type="text/javascript"></script>
<script type="text/JavaScript">
<!--
var fshow = 1;
var fshowtot = <?php echo $fshowtot; ?>;
function moreFiles(){
	fshow++;
	var fieldn = 'updata'+fshow;
	document.frm1[fieldn].style.display = 'block';
	if(fshow==fshowtot){
		document.getElementById('moreid').style.display = 'none';	
	}
}
//-->
</script>
<script type="text/JavaScript">
$(document).ready(function () {
     jQuery.validator.addMethod("phonenu", function (value, element) {
        if ( /^\d{3}-?\d{3}-?\d{4}$/g.test(value)) {
            return true;
        } else {
            return false;
        };
    }, "Please enter a valid phone number");
    jQuery.validator.addMethod("lettersonly", function(value, element) {
        return this.optional(element) || /^[a-zA-Z0-9]+$/i.test(value);
    }, "Letters only please");
    $('#frm1').validate({ 
        //alert("sdshdhsdhshd");// initialize the plugin
        errorClass: "invalid",
        rules: {
            name: {
                required: true,
                //lettersonly: true,
                minlength: 3,
                maxlength:50
            },
            email: {
                required: true,
                email:true
                
            },
            area_of_focus:{
              required: true  
            },
            suggestion: {
                required: true,
                minlength:10,
                maxlength:4000
            },
            need:{
              required: true,  
            }
        },
        submitHandler: function(form) {            
            jQuery('.submitbutton').hide();            
            form.submit();
          }
    });   
    

});
</script>