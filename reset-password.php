<?php 
$PAGE_HEADING = "Reset Password";
$TITLE = "Competiscan $PAGE_HEADING";  
$HEAD = '<link rel="stylesheet" href="includes/jquery/jquery-ui.css" />
        <script type="text/javascript" src="includes/jquery/jquery-ui.min.js"></script>
        <script type="text/javascript" src="includes/jquery/jquery.min.js"></script>
        <script type="text/javascript" src="includes/jquery.validate.min.js"></script>';

include 'header_top.php';
$errorMessage = 0;
?>

<script>
    $(document).ready(function () {
        $('#changePasswordForm').validate({
            rules: {
                newPassword: {
                    required: true,
                    minlength: 6,
                    maxlength: 20,
                },
                confPassword: {
                    required: true,
                    minlength: 6,
                    maxlength: 20,
                    equalTo: "#newPassword"
                },
            },
            messages: {
                newPassword: {
                    required: "New password is required",
                    minlength: "Password must be minimum 6 characters and maximum 20 characters",
                    maxlength: "Password must be minimum 6 characters and maximum 20 characters",
                },
                confPassword: {
                    required: "Confirm password is required",
                    equalTo: "New and confirm password must be same",
                    minlength: "Password must be minimum 6 characters and maximum 20 characters",
                    maxlength: "Password must be minimum 6 characters and maximum 20 characters"

                },
            },

            errorElement : 'div',
            errorLabelContainer: '.errorTxt'

        });

        $('.reset').on('click', function(){
            location.reload();
        });

    });
</script>
<?php 
    if(isset($_GET) && !empty($_GET)){
        $decryptedID = convert_uudecode(base64_decode($_GET['us']));
        $query = "SELECT userID FROM cscan_users WHERE userID='".$decryptedID."' AND reset_password_token='".$_GET['token']."'" ;
        $rs = $DRW->query($query,$DRW_read);
        $data = $DRW->fetch_row($rs);
        if(empty($data)){
            $errorMessage = 1;
        }
    }

    
?>
<form action='login.php' method='post' id="changePasswordForm">
    <table width='60%' border="0" cellspacing='0' cellpadding="6" align="center">
    <?php if($errorMessage == 1){ ?>
        <tr id="error-message" style="height: 50px;">
            <td colspan="2" style="text-align: center;color:red;">Your reset password token has been expired.</td>
        </tr>
    <?php } ?>
        <tr style="height: 50px;">
            <td align='right'>
                <b>New Password</b>
                <span class="error">*</span> :
            </td>
            <td>
                <table>
                    <tr>
                        <td>
                            <input type='password' name='newPassword' size='35' id="newPassword"> 
                        </td>
                        <!--<td class="bodytext">
                             &nbsp;(Do not use: $;"')
                        </td>-->
                    </tr>
                </table>
            </td>
        </tr>
        <tr><td>&nbsp;</td><td>
        <tr style="height: 50px;">
            <td align='right'>
                <b>Confirm Password</b>
                <span class="error">*</span> :
            </td>
            <td>
                <input type='password' name='confPassword' size='35' id="confpassword"> 
            </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td style="text-align:right;">
                <a href="login.php" id="forgot-password" class="red pull-right" style="font-size: smaller;margin-right: 80px;font-size:13px;">Back to Login</a>
            </td>
        </tr>
        <input type="hidden" value="<?= $_GET['us']; ?>" name="us" />
        <input type="hidden" value="<?= $_GET['token']; ?>" name="token" />
        <tr><td>&nbsp;</td><td><tr><td>&nbsp;</td><td>
        <input class='submitbutton' type='submit' value='Update'> 
        <input class='submitbutton reset' type='reset' value='Clear' style="background-image: none;background-color: #666666;"></td></tr>
    </table> 
</form>
<script>
  /*$("input#newPassword").keypress(
  function(event){
    if ((event.which == '36') || (event.which == '34') || (event.which == '39') || (event.which == '59')) {
        if(event.which == '36'){
            alert('Dollar($) key is not allowed!');
        }else if(event.which == '34'){
            alert('Double quotation(") key is not allowed!');
        }else if(event.which == '39'){
            alert("Apostrophe(') key is not allowed!");
        }else if(event.which == '59'){
            alert('Semicolon(;) key is not allowed!');
        }
        event.preventDefault();
    }
    
});

$("input#confpassword").keypress(
  function(event){
    if ((event.which == '36') || (event.which == '34') || (event.which == '39') || (event.which == '59')) {
      if(event.which == '36'){
            alert('Dollar($) key is not allowed!');
        }else if(event.which == '34'){
            alert('Double quotation(") key is not allowed!');
        }else if(event.which == '39'){
            alert("Apostrophe(') key is not allowed!");
        }else if(event.which == '59'){
            alert('Semicolon(;) key is not allowed!');
        }
        event.preventDefault();
    }
    
});
window.onload = () => {
 const myInput = document.getElementById('newPassword');
 const myInput1 = document.getElementById('confpassword');
 myInput.onpaste = e => e.preventDefault();
 myInput1.onpaste = e => e.preventDefault();
}*/
</script>
<?php include 'footer_bottom.php'; ?>