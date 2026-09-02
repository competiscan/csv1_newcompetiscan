<style>
        body {
            font-family: Arial, sans-serif;
        }
        .form-container {
            width: 100%;
            max-width: 500px;
            margin: 30px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            background-color: #f9f9f9;
        }
        h2 {
            text-align: center;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .input_box {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }
        .star {
            color: red;
            margin-right: 4px;
        }
        .submitbutton {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        .submitbutton:hover {
            background-color: #0056b3;
        }
        /* .message {
            text-align: center;
            color: red;
            margin-bottom: 10px;
            display: none;
        } */
        .message.success {
            color: green;
            text-align: center;
            margin-bottom: 10px;
        }
        .message.error {
            color: red;
            text-align: center;
            margin-bottom: 10px;
        }
    </style>
<?php
require_once('includes/globalSession.php');
$PAGE_HEADING = "Forgot Password";
$TITLE = "Competiscan $PAGE_HEADING";
include 'header_top_test.php';
$loc = "fullsearch.php?searchview=2";
if(isset($_SESSION['sess_userID'])){
	ob_end_clean();
	header("Location: $loc");
	exit;
}
function callAPI($method, $url, $data){
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    curl_setopt($curl, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
		'User-Agent: Mozilla/5.0'
    ]);
    $result = curl_exec($curl);
    curl_close($curl);
    return $result;
}
$postuserdata=array();
$msg = "";
$msgType = "";
// print_r($_POST);
if (isset($_POST['forgot_password']) && $_POST['forgot_password'] == 'Forgot Password') {
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $msg = "Please enter a valid email address.";
        $msgType = "error";
    } else {
        $postuserdata['email'] = strtolower($email);
        $postdata = json_encode($postuserdata);
        $apiuserurl = USER_LOGIN_API_URL_PROD . 'forgot-password';
        $getuserdata = callAPI('POST', $apiuserurl, $postdata);
        $resuserdata = json_decode($getuserdata, true);

        if (isset($resuserdata['code']) && $resuserdata['code'] == 200) {
            $msg = $resuserdata['message'];
            $msgType = "success";
            header("Location:reset_password.php?user=" .base64_encode($_POST['email']));
			exit;
        } else {
            $msg = $resuserdata['message'];;
            $msgType = "error";
        }
    }
}
?>
<script type="text/javascript">
function validateForgotPasswordForm() {
    const email = document.getElementById("forgot_email").value.trim();
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (email === "" || !emailRegex.test(email)) {
        alert("Please enter a valid email address.");
        return false;
    }
    return true;
}
</script>
<div class="form-container">
    <h3>Forgot Password</h3>
        <?php if($msg!=""){ ?>
        <div class="message <?php echo ($msgType === 'success') ? 'success' : 'error'; ?>">
            <?php  echo htmlspecialchars($msg); ?>
        </div>
        <?php } ?>
    <form action="" name="forgot_password" method="post" onsubmit="return validateForgotPasswordForm()">
    <!--<div id="send-success-message" class="message" style="<?php if($msg!=''){?>display:block;<?php } ?>"><?php echo htmlspecialchars($msg); ?></div>-->
        <!--<div id="send-unknown-email-message" class="message error">
            Invalid email address.
        </div>
        <div id="email-not-send-message" class="message error">
            Email has not been sent. Please try again!
        </div>-->
        <div class="form-group">
            <label for="forgot_email"><span class="star">*</span>Email Address:</label>
            <input type="text" name="email" id="forgot_email" class="input_box" maxlength="255" autofocus />
            <span style="display: flex; font-size:11px;float:left;"><b>Note : &nbsp;</b>A temporary code will be sent to the email address.</span>
        </div>
        <div class="form-group" style="text-align:right;">
            <input type="submit" name="forgot_password" value="Forgot Password" class="submitbutton" />
             
        </div>
         
    </form>
</div>

<?php
include 'footer_bottom.php';
?>