<?php 
$PAGE_HEADING = "Change Password";
$TITLE = "Competiscan $PAGE_HEADING";  
$HEAD = '<link rel="stylesheet" href="includes/jquery/jquery-ui.css" />
        <script type="text/javascript" src="includes/jquery/jquery-ui.min.js"></script>
        <script type="text/javascript" src="includes/jquery/jquery.min.js"></script>
        <script type="text/javascript" src="includes/jquery.validate.min.js"></script>';

include 'header_top.php';
require_once('includes/checklogin.php');
$successMessage = 0;
?>

<script>
    $(document).ready(function () {
        jQuery.validator.addMethod("notEqualTo", function(value, element, param) {
             return this.optional(element) || value != $(param).val();
        }, "New Password must not be same as old password");

        $('#changePasswordForm').validate({
            rules: {
                /*oldPassword: {
                    required: true,
                    remote: {
                        url: "password-match.php",
                        data: {'value': $("input[name$='oldPassword']").val()},
                        async: false
                    }
                },*/
                newPassword: {
                    required: true,
                    minlength: 6,
                    maxlength: 20,
                    //notEqualTo: "#oldPassword"
                },
                confPassword: {
                    required: true,
                    minlength: 6,
                    maxlength: 20,
                    equalTo: "#newPassword"
                },
            },
            messages: {
                /*oldPassword: {
                    required: "Old password is required",
                    remote : "Invalid Old Password"
                },*/
                newPassword: {
                    required: "New password is required",
                    minlength: "Password must be minimum 6 characters and maximum 20 characters",
                    maxlength: "Password must be minimum 6 characters and maximum 20 characters",
                    //notEqualTo: "New Password must not be same as old password"
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

        setTimeout(function() {
            $('#success-message').hide();
        }, 6000);

    });

</script>
<?php 
    if(isset($_POST) && !empty($_POST)){
        $query = "UPDATE `cscan_users` SET password='" . $_POST['newPassword'] . "' WHERE userID='".$_SESSION['sess_userID']."'";
        if($DRW->query($query, $DRW_main)){
            $successMessage = 1;
        }
    }

    
?>
<form action='' method='post' id="changePasswordForm">
    <table width='60%' border="0" cellspacing='0' cellpadding="6" align="center">
    <?php if($successMessage == 1){ ?>
        <tr id="success-message" style="height: 50px;">
            <td colspan="2" style="text-align: center;color:green;">Password has been changed successfully.</td>
        </tr>
    <?php } ?>
        <!--tr style="height: 50px;">
            <td align='right'>
                <b>Old Password</b>
                <span class="error">*</span> :
            </td>
            <td>
                <input type='password' name='oldPassword' size='35' id="oldPassword">
            </td>
        </tr-->
        <tr><td>&nbsp;</td><td>
        <tr style="height: 50px;">
            <td align='right'>
                <b>New Password</b>
                <span class="error">*</span> :
            </td>
            <td>
                <table>
                    <tr>
                        <td >
                            <input type="password" name="newPassword" size="35" id="newPassword">
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
} */
</script>
<?php include 'footer_bottom.php'; ?>