<?php
require_once('includes/globalSession.php');
$PAGE_HEADING = "Reset Password";
$TITLE = "Competiscan $PAGE_HEADING";
include 'header_top_test.php';
$loc = "fullsearch.php?searchview=2";
if(isset($_SESSION['sess_userID'])){
	ob_end_clean();
	header("Location: $loc");
	exit;
}
$user="";
if (isset($_GET['user'])) {
    $user_email = $_GET['user'];
}else{
    $user_email='';
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

$msg = "";
if (isset($_POST['reset_password']) && $_POST['reset_password'] === 'Reset Password') {
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $code = trim($_POST['code']);
    $newPassword = trim($_POST['new_password']);

    $postuserdata = [
        'email' => $email,
        'code' => $code,
        'new_password' => $newPassword
    ];

    $postdata = json_encode($postuserdata);
    $apiuserurl = USER_LOGIN_API_URL_PROD.'reset-password';
    $getuserdata = callAPI('POST', $apiuserurl, $postdata);
    $resuserdata = json_decode($getuserdata, true);

    if (isset($resuserdata['code']) && $resuserdata['code'] == 200) {
        header("Location: login.php?msg=1");
        exit;
    } else {
        $msg = $resuserdata['message'];
    }
}
?>
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
    h3 {
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
    .message {
        text-align: center;
        color: red;
        margin-bottom: 10px;
        display: none;
    }
</style>

<script type="text/javascript">
function validateresetpasswordForm() {
    const email = document.getElementById("email").value.trim();
    const code = document.getElementById("code").value.trim();
    const newPassword = document.getElementById("new_password").value.trim();

    const emailMessage = document.getElementById("send-unknown-email-message");
    const generalMessage = document.getElementById("email-not-send-message");

    emailMessage.style.display = "none";
    generalMessage.style.display = "none";

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const password_pattern=/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z\d$;"'_)]).{8,20}$/;


    if (email === "" || !emailRegex.test(email)) {
        emailMessage.style.display = "block";
        return false;
    }

    if (code === "" || newPassword === "") {
        generalMessage.textContent = "All fields are required.";
        generalMessage.style.display = "block";
        return false;
    }
    if (newPassword.length < 8 || !password_pattern.test(newPassword)) {
        generalMessage.textContent = "The new password must contain 8+ characters, uppercase, lowercase, a digit, and a special character (excluding: $ ; \" ' _ ))";
        generalMessage.style.display = "block";
        return false;
    }

    return true;
}
</script>
<div class="form-container">
    <h3>Please reset your password</h3>
    <form method="post" name="reset_password" onsubmit="return validateresetpasswordForm()" autocomplete="off">
        <?php if (!empty($msg)): ?>
            <div id="send-success-message" class="message" style="display: block;"><?php echo htmlspecialchars($msg); ?></div>
        <?php endif; ?>
        <div id="send-unknown-email-message" class="message">Invalid email address.</div>
        <div id="email-not-send-message" class="message">Email has not been sent. Please try again!</div>

        <div class="form-group">
            <label for="email"><span class="star">*</span>Email Address:</label>
            <input type="email" name="email" id="email" class="input_box" maxlength="255" autocomplete="username" readonly value="<?php if($user_email!=""){echo base64_decode($user_email);} ?>" />
        </div>

        <div class="form-group">
            <label for="code"><span class="star">*</span>Code:</label>
            <input type="text" name="code" id="code" class="input_box" maxlength="255" autocomplete="one-time-code" />
        </div>

        <div class="form-group">
            <label for="new_password"><span class="star">*</span>New Password:</label>
            <input type="password" name="new_password" id="new_password" class="input_box" maxlength="255" autocomplete="new-password" />
            <span style="display: flex; font-size:11px;float:left;"><b>Note:</b>The new password must contain 8+ characters, uppercase, lowercase, a digit, and a special character (excluding: $ ; \" ' _ ))</span>
        </div>

        <div class="form-group" style="text-align:right;">
        <div align="center">
        <input type="submit" name="reset_password" value="Reset Password" class="submitbutton" />
            <a href="login.php" class="submitbutton" >Click here to Sign In</a>
            </div>
            
        </div>
    </form>
</div>

<?php include 'footer_bottom.php'; ?>
