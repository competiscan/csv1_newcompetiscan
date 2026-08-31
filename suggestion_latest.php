<?php
require_once('includes/globalSession.php');
require_once('includes/checklogin.php');
$PAGE_HEADING = "Suggestions";
$TITLE = "Competiscan $PAGE_HEADING";
$HEAD = '<script src="includes/jquery.js" type="text/javascript"></script><script src="includes/jquery.validate.min.js" type="text/javascript"></script>';
include 'header_top.php';
// require_once('Mail.php');
// require_once('Mail/mime.php');
// $crlf = "\n";

// $params = array(
//     'username'=>'',
//     'password'=>'',
//     'persist'=>true,
// );
// $mail =& Mail::factory('smtp',$params);

$msg = '';
$success = 0;
$timestamp = time();
$sendkey_prev = date('Ymd',$timestamp - (24 * 60 * 60))."<br/>";
$sendkey = date('Ymd',$timestamp); 
$error='';
//echo $_SESSION['sess_access_token'];
function callAPI($method, $url, $data = null){
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);

    if (!empty($data)) {
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    }

    curl_setopt($curl, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer '.$_SESSION['sess_access_token'],
        'User-Agent: Mozilla/5.0'
    ]);
//echo $_SESSION['sess_access_token'];die;
    $result = curl_exec($curl);
    if (curl_errno($curl)) {
        echo 'cURL Error: ' . curl_error($curl);
    }
    curl_close($curl);
    return $result;
}
if(isset($_SESSION['sess_username'])) {
	$user_email = $_SESSION['sess_username'];
}
else {
	$user_email = '';
}
 if ( isset($_POST['captcha']) && ($_POST['captcha']!="") ){
    if(strcasecmp($_SESSION['captcha'], $_POST['captcha']) != 0){
        $error .= "<br />Entered captcha code does not match!.";  
    }
}
 if ($error) {
    echo $result = '<div class="alert alert-danger"><strong>There were error(s) in your form:</strong>'.$error.'</div>';
}
if(isset($_POST['sendbutton']) && $_POST['sendbutton']=='Send' && strcasecmp($_SESSION['captcha'], $_POST['captcha'])== 0){
    $email = $user_email;
    $name = $_POST['name'];
    $save_name  = $_POST['name'];
    $save_phone = $_POST['phone'];
    $save_message    = $_POST['suggestion'];
    $postsuggesstiondata = [
        'name' => $name,
        'phone' => $save_phone,
        'suggestion' => $save_message
    ];
    //echo "dsdsdsd".$_SESSION['sess_access_token']; die;
    $postdata = json_encode($postsuggesstiondata);
    $apisuggesstionurl = SUGGESTION_API_URL_UAT.'suggestion_mail';
    $getsuggesstiondata = callAPI('POST', $apisuggesstionurl, $postdata);
    $ressuggesstiondata = json_decode($getsuggesstiondata, true);
    if(isset($ressuggesstiondata['status']) && $ressuggesstiondata['status'] == 'success' && isset($ressuggesstiondata['statusCode']) && $ressuggesstiondata['statusCode'] == 200 ) {
        $success = 1;
        $msg = 'Your suggestion has been sent successfully.';
        $_POST = array();
    } else {
        $success = 0;
        $apimsg = isset($ressuggesstiondata['message']) ? $ressuggesstiondata['message'] : $ressuggesstiondata['message'];
        $msg=$apimsg;
        //$msg = 'Your suggestion has not been submitted due to some temporary error';
    }
    // echo "<pre>";
    // print_r($ressuggesstiondata);
    // echo "</pre>";
}

if($success == 0) {
    if($msg!='') {
           echo '<div id ="msg_failed" class="alert alert-danger">'.$msg.'</div>';    
    }
    
}
elseif($msg!='') {
        echo '<div id ="success_msg" class="alert alert-success">'.$msg.'</div>';
}
?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" name="frm1" id="frm1" method="post">
<table width="100%" border="0" cellpadding="5" cellspacing="0" class="bodytext">
<tr><td style="padding:7px;" colspan="2"><strong>Please complete the following information and click send.</strong></td></tr>

	<tr>
	<td style="padding:7px;" class="bodytext" align="right" valign="top" width="30%"><span class="star">*</span> <strong>Name :</strong></td>
        <td style="padding:7px;"><input type="text" name="name" class="input_box" size="40" maxlength="200" value="<?php if(isset($_POST['name']) and $_POST['name']!=''){ echo $_POST['name']; } ?>" /></td>
	</tr>
	<!--<tr>
	<td class="bodytext" align="right"><span class="star">*</span> <strong>E-mail Address :</strong></td>
	<td><input type="text"  name="email" class="input_box" size="40" maxlength="200" value="<?php echo htmlspecialchars($email,ENT_QUOTES) ?>" /></td>
	</tr>-->
	<tr>
	<td style="padding:7px;" class="bodytext" align="right" valign="top" width="30%"><span class="star">*</span> <strong>Phone :</strong></td>
	<td style="padding:7px;"><input type="text" name="phone" class="input_box" size="40" maxlength="15" value="<?php if(isset($_POST['phone'])){ echo $_POST['phone']; } ?>" /></td>
	</tr>
	<tr>
	<td style="padding:7px;" colspan="1" class="bodytext" align="right" valign="top"><span class="star">*</span> <strong>Suggestion :</strong></td>
	<td style="padding:7px;"><textarea name="suggestion" cols="39" rows="10" class="input_box"><?php if(isset($_POST['suggestion'])){ echo $_POST['suggestion']; } ?></textarea></td>
	</tr>
        <tr>
         <td style="padding:7px;" class="bodytext" align="right" valign="top"><span class="star">*</span> <strong>Enter Captcha :</strong></td>
         <td><input type="text" name="captcha" class="input_box" size="15"/>
           &nbsp;&nbsp;<img src="captcha.php?rand=<?php echo rand(); ?>" id='captcha_image' height="35px">
         <td>
        </tr>
        <tr><td>&nbsp;</td><td class="bodytext" align="left" valign="top"><p>Can't read the image?
            <a href='javascript: refreshCaptcha();'>click here</a>
            to refresh</p>
            </td>
        </tr>
	<tr>
	<td>&nbsp;</td>
	<td>
	<input type="submit" name="sendbutton" style="width:53px;height:21px;" id="submit_button" value="Send" class="button" />
	</td>
	</tr>
	
</table>
<input type="hidden" name="send" value="1" /></form>
<?php
include 'footer_bottom.php';
?>
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
        return this.optional(element) || /^[a-z]+$/i.test(value);
    }, "Letters only please");
    $('#frm1').validate({ // initialize the plugin
        errorClass: "invalid",
        rules: {
            name: {
                required: true,
                //lettersonly: true,
                minlength: 3,
                maxlength:50
            },
            phone: {
                required: true,
                phonenu:true
                //number: true,
                //max:12
                //range:[6, 15]
            },
            suggestion: {
                required: true,
                minlength:10,
                maxlength:2000
            },
            captcha: {
                required: true,
                
            }
        },
        /*messages: {
                email: {
                        required: "Please enter an e-mail address" //, email: "This is not a valid e-mail address"
                },
                suggestion: {
                        required: "Please enter your suggestion" //, minWords: "Please enter at least 4 words"
                },
                name: {
                        required: "Please enter your name" //, minlength: "Please enter at least 3 letters for your name"
                },
                phone: {
                        required: "Please enter your phone number" //, phoneUS: "Phone number is not valid"
                    }
        },
        submitHandler: function (form) { // for demo
           // alert('valid form submitted'); // for demo
            //return true; // for demo
            $("#msg_failed").fadeOut(3000);
            $("#success_msg").fadeOut(3000);
            return true;
        }*/
    });

});
</script>
<script>
//Refresh Captcha
function refreshCaptcha(){
    var img = document.images['captcha_image'];
    img.src = img.src.substring(0,img.src.lastIndexOf("?"))+"?rand="+Math.random()*1000;
}
</script>
