<?php
//require_once('includes/competi_def.php');
require_once('includes/globalSession.php');
require_once 'product_doc_tracker.php';
track_user();
@ob_clean();
$PAGE_HEADING = "Change temporary password";
$TITLE = "Competiscan $PAGE_HEADING";
$loc = "fullsearch.php?searchview=2";
if(isset($_SESSION['sess_userID'])){
	ob_end_clean();
	header("Location: $loc");
	exit;
}
$user="";
if (isset($_GET['user'])) {
    $user = $_GET['user'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Competiscan">
	<title><?php echo isset($TITLE) && $TITLE ? $TITLE : 'Competiscan' ?></title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/custom.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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
        .message {
            text-align: center;
            color: red;
            margin-bottom: 10px;
            display: none;
        }
    </style>
	<link href='https://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,400,300,600,700|Open+Sans+Condensed:300,700,300italic' rel='stylesheet' type='text/css'>
	<link rel="shortcut icon" href="/favicon.ico" />
	<link href="includes/competiscan_stylesheet.css?v=20100318" rel="stylesheet" type="text/css" />
	<script>window.jQuery || document.write('<script src="js/jquery.js">\x3C/script>')</script>
	<?php echo isset($HEAD) ? $HEAD : '' ?>
</head>
<body <?php echo isset($BODYTAG) ? $BODYTAG : '' ?>>
<!-- Navigation -->
<nav class="navbar navbar-default navbar-fixed-top" role="navigation">
    <div class="container">
        <div class="navbar-header page-scroll">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand page-scroll" id="logo" href="/">
				<img src="/images/competiscan-logo.png" alt="Competiscan logo">
            </a>
        </div>
        <div class="collapse navbar-collapse navbar-ex1-collapse">
            <ul class="nav navbar-nav">
<?php include('./nav-common.php') ?>
            </ul>
        </div>
        <!-- /.navbar-collapse -->
    </div>
    <!-- /.container -->
</nav>
<div id="titlebar">
	<div class="container">
		<h1><?php echo isset($PAGE_HEADING) ? $PAGE_HEADING : '' ?></h1>
		<div id="breadcrumbs">
			<span><a href="/">Competiscan</a></span>
			<span class="separator">/</span>
			<span class="current"><?php echo isset($PAGE_HEADING) ? $PAGE_HEADING : '' ?></span>
		</div>
	</div>
</div>
<div id="content" class="container">
<?php 
if(!empty($_SESSION['sess_username'])) {
        $show_header_top=true;
        $page_permissions = getPagePermission();        
        if(!empty($page_permissions) AND in_array('power_search',$page_permissions)){
            $show_header_top=true;
        }else{
            $show_header_top=false;
        }    
     /*######## End for Page permission ########*/
    if($show_header_top){    
    ?>
	<table cellspacing="0" class="searchMenutab">
            <tr> 
                <td><a href="emailAlerts.php" title="Your Search Settings">Email Alerts</a></td>
                <td><a href="savedsearch.php" title="Your Saved Search">Saved Searches</a></td>
                <td><a href="baskets.php" title="Export Baskets">Export Baskets</a></td>
                <td><a href="fullsearch.php?searchview=2" title="Power Search">Power Search</a></td>
                <td><a href="lastSearch.php" title="View the last search performed by you">Last Search</a></td>
                <td><a href="lastResult.php" title="View results of the last search performed by you">Last Results</a></td>
            </tr>
	</table>
<?php }
 }?>
<div id="page">
<?php
//print_r($_SESSION);
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
// echo "<pre>";
// print_r($_POST);
// echo "</pre>";
$msg="";
if(isset($_POST['change_password']) and $_POST['change_password']=='Update Password'){
    ?>
    <script>
// setTimeout(function() {
//     document.getElementById('Update_temp_pass').disabled = true;
// }, 6000);
</script>
    <?php 
	$postuserdata['email']  = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
	$postuserdata['password']  = trim($_POST['old_password']);
    $postuserdata['new_password']  = trim($_POST['new_password']);
	$postdata=json_encode($postuserdata);
	$apiuserurl=USER_LOGIN_API_URL_PROD.'sign-in-aws';
    $getuserdata= callAPI('POST', $apiuserurl, $postdata);
    $resuserdata = json_decode($getuserdata, true);
    // echo "<pre>";
    // print_r($resuserdata);
    // echo "<pre>";
    // die;
	if (isset($resuserdata['code']) && $resuserdata['code'] == 200){
        //ob_end_clean();
        header("Location:login.php?msg=1");
        exit;
	}else{
       $msg=$resuserdata['message'];
        //echo "Internal server error";

    }
    
}

?>
<div class="form-container">
    <h3>Please change your temporary password.</h3>
    <form action="" name="changed_password" method="post" onsubmit="return validatetemppasswordForm()">
    <div id="send-success-message" class="message" style="<?php if($msg!=''){?>display:block;<?php } ?>"><?php echo htmlspecialchars($msg); ?></div>
        <div id="send-unknown-email-message" class="message">Invalid email address.</div>
        <div id="email-not-send-message" class="message">Email has not been sent. Please try again!</div>

        <div class="form-group">
            <label for="email"><span class="star">*</span>Email Address:</label>
            <input type="text" name="email" id="email" class="input_box" maxlength="255" readonly value="<?php if($user!="" ) {echo base64_decode($user);} ?>" />
        </div>

        <div class="form-group">
            <label for="old_password"><span class="star">*</span>Temporary Password:</label>
            <div style="position: relative;">
                <input type="password" name="old_password" id="old_password" class="input_box" maxlength="255" />
                <span id="togglePassword1" style="position:absolute; right:10px; top:50%; transform:translateY(-50%); cursor:pointer;">
                    <i class="far fa-eye-slash" style="border:none; margin-top: 0px;" id="toggleIcon1"></i>
                </span>
            </div>
            
        </div>
        <div class="form-group">
            <label for="new_password"><span class="star">*</span>New Password:</label>
           <div style="position: relative;">
                <input type="password" name="new_password" id="new_password" class="input_box" maxlength="255" />
                <span id="togglePassword" style="position:absolute; right:10px; top:50%; transform:translateY(-50%); cursor:pointer;">
                    <i class="far fa-eye-slash" style="border:none; margin-top: 0px;" id="toggleIcon"></i>
                </span>
            </div>
            <span style="font-size:13px;color:#818589;"><b>Hint: </b></span>
            <span style="font-size:13px;color:#818589;"><b>Password must be 8+ characters, with uppercase, lowercase, digit, and special character.</b></span>
        </div>
        <div class="form-group">
            <label for="confirm_password"><span class="star">*</span>Confirm Password:</label>
            <div style="position: relative;">
                <input type="password" name="confirm_password" id="confirm_password" class="input_box" maxlength="255" />
                <span id="togglePassword2" style="position:absolute; right:10px; top:50%; transform:translateY(-50%); cursor:pointer;">
                    <i class="far fa-eye-slash" style="border:none; margin-top: 0px;" id="toggleIcon2"></i>
                </span>
            </div>
            <span style="font-size:13px;color:#818589;"><b>Hint: </b></span>
            <span style="font-size:13px;color:#818589;"><b>Password must be 8+ characters, with uppercase, lowercase, digit, and special character.</b></span>
        </div>
        <div class="form-group" style="text-align:right;">
            <input type="submit" name="change_password" value="Update Password" id="Update_temp_pass" class="submitbutton" />
        </div>
    </form>
</div>
<?php
include 'footer_bottom.php';
?>
<script>
const togglePassword = document.getElementById('togglePassword');
const passwordInput = document.getElementById('new_password');
const toggleIcon = document.getElementById('toggleIcon');

togglePassword.addEventListener('click', function () {
    const isPassword = passwordInput.getAttribute('type') === 'password';
    passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
    
    // Toggle icon class
    toggleIcon.classList.toggle('fa-eye');
    toggleIcon.classList.toggle('fa-eye-slash');
});

const togglePassword1 = document.getElementById('togglePassword1');
const passwordInput1 = document.getElementById('old_password');
const toggleIcon1 = document.getElementById('toggleIcon1');

togglePassword1.addEventListener('click', function () {
    const isPassword1 = passwordInput1.getAttribute('type') === 'password';
    passwordInput1.setAttribute('type', isPassword1 ? 'text' : 'password');
    
    // Toggle icon class
    toggleIcon1.classList.toggle('fa-eye');
    toggleIcon1.classList.toggle('fa-eye-slash');
});

const togglePassword2 = document.getElementById('togglePassword2');
const passwordInput2 = document.getElementById('confirm_password');
const toggleIcon2 = document.getElementById('toggleIcon2');

togglePassword2.addEventListener('click', function () {
    const isPassword2 = passwordInput2.getAttribute('type') === 'password';
    passwordInput2.setAttribute('type', isPassword2 ? 'text' : 'password');
    
    // Toggle icon class
    toggleIcon2.classList.toggle('fa-eye');
    toggleIcon2.classList.toggle('fa-eye-slash');
});
</script>
<script>
// Existing toggle password logic...

// Real-time password match checker
const newPassword = document.getElementById('new_password');
const confirmPassword = document.getElementById('confirm_password');

function checkPasswordMatch() {
    const newVal = newPassword.value;
    const confirmVal = confirmPassword.value;

    if (confirmVal === "") {
        confirmPassword.style.border = '';
    } else if (newVal === confirmVal) {
        confirmPassword.style.border = '2px solid green';
        //newPassword.style.border = '2px solid green';
    } else {
        confirmPassword.style.border = '2px solid red';
        //newPassword.style.border = '2px solid red';
    }
}

// Attach event listeners for real-time validation
newPassword.addEventListener('input', checkPasswordMatch);
confirmPassword.addEventListener('input', checkPasswordMatch);
</script>
<script type="text/javascript">
function validatetemppasswordForm() {
    // Hide all messages
    document.getElementById("send-success-message").style.display = "none";
    document.getElementById("send-unknown-email-message").style.display = "none";
    document.getElementById("email-not-send-message").style.display = "none";

    // Get form values
    const email = document.getElementById("email").value.trim();
    const oldPassword = document.getElementById("old_password").value.trim();
    const newPassword = document.getElementById("new_password").value.trim();
    const confirmPassword = document.getElementById("confirm_password").value.trim();

    // Basic email format check
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (email === "" || !emailRegex.test(email)) {
        document.getElementById("send-unknown-email-message").style.display = "block";
        return false;
    }

    if (oldPassword === "" || newPassword === "") {
        document.getElementById("email-not-send-message").innerText = "All fields are required.";
        document.getElementById("email-not-send-message").style.display = "block";
        return false;
    }

    if (newPassword.length < 8) {
        document.getElementById("email-not-send-message").innerText = "New password must be at least 8 characters.";
        document.getElementById("email-not-send-message").style.display = "block";
        return false;
    }
    if (newPassword !== confirmPassword) {
        document.getElementById("email-not-send-message").innerText = "New password and confirm password do not match.";
        document.getElementById("email-not-send-message").style.display = "block";
        return false;
    }

return true;
    // // Simulate success (replace this with actual logic)
    // document.getElementById("send-success-message").style.display = "block";
    // return false; // prevent actual submission for demo
}
</script>
