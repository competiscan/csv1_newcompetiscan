<?php 
$PAGE_HEADING = "Change Password";
$TITLE = "Competiscan $PAGE_HEADING";  
$HEAD = '<link rel="stylesheet" href="includes/jquery/jquery-ui.css" />
        <script type="text/javascript" src="includes/jquery/jquery-ui.min.js"></script>
        <script type="text/javascript" src="includes/jquery/jquery.min.js"></script>
        <script type="text/javascript" src="includes/jquery.validate.min.js"></script>';

include 'header_top_test.php';
require_once('includes/checklogin.php');
$successMessage = 0;
// echo "<pre>";
// print_r($_SESSION);
// echo "</pre>";
// die;
?>

<script>
    $(document).ready(function () {
    $.validator.addMethod("strongPassword", function(value, element) {
        return this.optional(element) || /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z\d$;"'_)]).{8,20}$/.test(value);
    }, "The new password must contain 8+ characters, uppercase, lowercase, a digit, and a special character (excluding: $ ; \" ' _ ))");

        $('#changePasswordForm').validate({
            rules: {
                newPassword: {
                   required: true,
                   strongPassword: true
                },
                confPassword: {
                    required: true,
                    strongPassword: true
                },
            },
            messages: {
                /*oldPassword: {
                    required: "Old password is required",
                    remote : "Invalid Old Password"
                },*/
                newPassword: {
                    required: "Old password is required",
                    strongPassword:"The old password must contain 8+ characters, uppercase, lowercase, a digit, and a special character (excluding: $ ; \" ' _ ))"
                    
                    //maxlength: "Password must be minimum 8 characters and maximum 20 characters"
                    //notEqualTo: "New Password must not be same as old password"
                    //pattern: "The new password must contain uppercase, lowercase, a digit, and a special character (excluding: $ ; \" ' _ ))"
                },
                confPassword: {
                    required: "New password is required",
                    strongPassword:"The new password must contain 8+ characters, uppercase, lowercase, a digit, and a special character (excluding: $ ; \" ' _ ))"
                    
                    //minlength: "Password must be minimum 8 characters and maximum 20 characters",
                   // maxlength: "Password must be minimum 8 characters and maximum 20 characters"
                    //pattern: "The new password must contain uppercase, lowercase, a digit, and a special character (excluding: $ ; \" ' _ ))"

                },
            },

            errorElement : 'div',
            errorLabelContainer: '.errorTxt'

        });

        $('.reset').on('click', function(){
            location.reload();
        });

        setTimeout(function() {
            $('#success-message').hide();
        }, 6000);

    });

</script>
<?php 
     function callAPI($method, $url, $data){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization:'.$_SESSION['sess_access_token'],
            'User-Agent: Mozilla/5.0'
        ]);
        $result = curl_exec($curl);
        curl_close($curl);
        return $result;
    }
    $msg="";
    if(isset($_POST) && !empty($_POST) && $_POST['change_password']='Update'){
        $postuserdata=array();
        // print_r($_POST);
            $postuserdata['old_password']  = trim($_POST['newPassword']);
            $postuserdata['new_password']  = trim($_POST['confPassword']);
            $postdata=json_encode($postuserdata);
            $apiuserurl=USER_LOGIN_API_URL_PROD.'change-password';
            $getuserdata= callAPI('POST', $apiuserurl, $postdata);
            $resuserdata = json_decode($getuserdata, true);
            // echo "<pre>";
            // print_r($resuserdata);
            // echo "<pre>";
            // die;
            if (isset($resuserdata['code']) && $resuserdata['code'] == 200){
                $msg=$resuserdata['message'];
            }else{
                $msg=$resuserdata['message'];
            }
        }
?>
<form action='' method='post' id="changePasswordForm">
    <table width='60%' border="0" cellspacing='0' cellpadding="6" align="center">
    <?php if($msg!=''){ ?>
        <tr id="success-message" style="height: 50px;">
            <td colspan="2" style="text-align: center;color:green;"><?php echo $msg; ?></td>
        </tr>
    <?php } ?>
        <tr><td>&nbsp;</td><td>
        <tr style="height: 50px;">
            <td align="right">
                <b style="margin-left:-50px;">Old Password:<span class="error">*</span>&nbsp;</b>
                
            </td>
            <td>
                <input type="password" name="newPassword" size="35" id="newPassword" class="form-control">
                <!--<span style="display: flex; font-size:11px;float:left;"><b>Note : &nbsp;</b>The new password must contain 8 characters, uppercase, lowercase, a digit, and a special character (excluding: $ ; \" ' _ ))</span>-->
            </td>
        </tr>
        <tr><td>&nbsp;</td><td>
        <tr style="height: 50px;">
            <td align="right">
                <b style="margin-left:-60px;">New Password: <span class="error">*</span>&nbsp;</b>
                
            </td>
            <td>
                <input type="password" name="confPassword" size="35" id="confpassword" class="form-control"> 
                <span style="display: flex; font-size:11px;float:left;"><b>Note:</b>The new password must contain 8+ characters, uppercase, lowercase, a digit, and a special character (excluding: $ ; \" ' _ ))</span>
            </td>
        </tr>
         <tr>
            <td>&nbsp;</td>
            <td>
                <input class="submitbutton" type="submit" value="Update" name="change_password">
                <input class="submitbutton reset" type="reset" value="Clear" style="background-image: none; background-color: #666666;">
            </td>
        </tr>
    </table> 
</form>
<script>
</script>
<?php include 'footer_bottom.php'; ?>