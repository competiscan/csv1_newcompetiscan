<?php 
require_once("../auth_auth.php"); 
include 'top.php';
?>
<script type="text/javascript" src="jquery.min.js"></script>
<table width='100%' border="0" cellspacing='0' cellpadding="6" class="text">
  <tr><td class="adminhead" align='center' colspan='2'>CHANGE PASSWORD</td></tr>
  <tr>
    <td>
      <table width='60%' border="0" cellspacing='0' cellpadding="6" class="text" align="center">
      <tr><td class='error' align='right' colspan='2'>*fields required</td></tr>
<?php
if(isset($_POST['submit'])){
	$newPass = $_POST['newPassword'];
	$oldPass = $_POST['oldPassword'];
	$chk = "SELECT * FROM cscan_admin_users WHERE userID='{$AUTH_DATA['userID']}' AND password=md5('$oldPass')";
	$rs = $DRW->query($chk,$DRW_read);
	if($DRW->num_rows($rs) > 0) {
		$update = "UPDATE cscan_admin_users SET password=md5('$newPass') WHERE userID='{$AUTH_DATA['userID']}' AND password=md5('$oldPass')";
		$DRW->query($update,$DRW_main);
		echo "<tr><td class=error align=center colspan=2> Your password has been changed sucessfully.</td></tr>";
	}
	else {
		echo "<tr><td class=error align=center colspan=2> Invalid password provided !!! </td></tr>";
	}
}
?>
        <form action='' method='post' name='frm1' onsubmit='return validate()'>
        <tr><td align='right'><b>Old Password</b><span class='error'>*</span> :</td><td><input class='text' type='password' name='oldPassword' size='35' id="oldPassword"> </td></tr>
        <tr><td align='right'><b>New Password</b><span class='error'>*</span> :</td><td class="bodytext"><input class='text' type='password' name='newPassword' size='35' id="newPassword"></td></tr>
        <tr><td align='right'><b>Confirm Password</b><span class='error'>*</span> :</td><td ><input class='text' type='password' name='confPassword' size='35' id="confPassword"> </td></tr>
        <tr><td>&nbsp;</td><td><input class='button' type='submit' name='submit1' value='Change'> <input class='button' type='reset' value='Reset'></td></tr>
        <input type="hidden" name="submit" value="1"></form>
      </table>
    </td>
  </tr>
</table>
<?php include 'bottom.php'; ?>
<script type="text/JavaScript">
<!--
function validate()
{
    oldPassword = document.frm1.oldPassword.value = trimspace(document.frm1.oldPassword.value);
    newPassword = document.frm1.newPassword.value = trimspace(document.frm1.newPassword.value);
    confPassword = document.frm1.confPassword.value = trimspace(document.frm1.confPassword.value);
    if(oldPassword == ''){
        alert('Please enter your old password first');
        document.frm1.oldPassword.focus();
        return false;
    }

    if(newPassword == ''){
        alert('Please enter your new password first');
        document.frm1.newPassword.focus();
        return false;
    }

    if(confPassword == ''){
        alert('Please enter your confirm password first');
        document.frm1.confPassword.focus();
        return false;
    }
    if((confPassword != newPassword) && (newPassword != '')){
        alert('Your confirm password field doesn\'t match with new password field');
        document.frm1.confPassword.focus();
        return false;
    }
    return true;
}
//-->
</script>
<script>
  /*$("input#oldPassword").keypress(
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

$("input#newPassword").keypress(
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
$("input#confPassword").keypress(
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
 const myInput = document.getElementById('oldPassword');
 const myInput1 = document.getElementById('newPassword');
 const myInput2 = document.getElementById('confPassword');
 myInput.onpaste = e => e.preventDefault();
 myInput1.onpaste = e => e.preventDefault();
 myInput2.onpaste = e => e.preventDefault();
}*/
</script>