<?php
require_once('includes/globalSession.php');
$PAGE_HEADING = "Un-Subscribe Email Alert";
$TITLE = "Competiscan $PAGE_HEADING";
include 'header_top.php';
$loc = "fullsearch.php?searchview=2";
if(isset($_SESSION['sess_userID'])){
	ob_end_clean();
	header("Location: $loc");
	exit;
}
$getuserID="";
if(isset($_REQUEST['u'])){
$getuserID = (int)$_REQUEST['u'];
}
//echo $getuserID;
//echo "<pre>";
//print_r($_POST);
if(isset($_POST['submit']) && $_POST['submit']='Un-subscribe to Email Alert'){
    $postuserID=$_POST['subs_userID'];
    $confirmYes=$_POST['submit_confirm'];
    if($confirmYes=="Yes" && $postuserID!=""){
        $query2 = "SELECT COUNT(*) FROM cscan_search Where userID='".$postuserID."' AND  emailAlert='1'";
	$query_result2 = $DRW->query($query2,$DRW_read);
            if($DRW->num_rows($query_result2)>0){ 
              $sql_update = "Update cscan_search set emailAlert='0' where userID='".$postuserID."'";
              //$resposeUpdate = $DRW->query($sql_update,$DRW_main); 
               //$resposeUpdate=1; 
                if($resposeUpdate){
                    $sql_ins = "Insert into cscan_search_unsubscribe_user (userID) 
                                        values('".$postuserID."')"; die;
                     //$result_ins = $DRW->query($sql_ins,$DRW_main);
                      ob_end_clean();
                      header("Location:index.php");
                      exit;
                 }
            }
        
    }else{
        ob_end_clean();
        header("Location:index.php");
        exit; 
    }
    
}
//http://localhost/competiscan.com/user_unsubscribe_email.php?u=26437
?>
<script type="text/javascript">
function myFunction() {
  let text;
  if (confirm("Are you sure want to un-subscribe email alert?") == true) {
    text = "Yes";
  } else {
    text = "No";
  }
  document.getElementById("submit_confirm_value").value = text;
  document.getElementById("frm_subscribe").submit();
}
</script>
<style>
.loader{
    background: rgba(255,255,255,0.9) url(images/loader.gif) no-repeat center 50% ;  
    opacity: 0.9;
    z-index: 1000001;
    width:100%; 
    height:100%; 
    position: fixed; 
    top:0; 
    left:0;
}
</style>
<div style="height:300px;">
<form action="" name="frm_subscribe" id="frm_subscribe" method="post">
<table width="100%" border="0" cellpadding="5" cellspacing="0" class="bodytext">
        <tr>
            <td colspan="2" class="bodytext" align="center">
                    <strong>You are requesting to unsubscribe competiscan email alert, after unsubscribe your email alert will be deactivate and you will not receive email alert.<br/>If you want subscribe again please select email alert option under email alert section. </strong>
            </td>
	</tr>
        <tr>
            <td colspan="2">&nbsp;</td>
            
	</tr>
	<tr>
            <td>&nbsp;</td>
            <td align="center">
                    <input type="hidden" id="subs_userID" name="subs_userID" value="<?php echo $getuserID; ?>"/>
                    <input type="submit" name="submit" value="Unsubscribe" class="submitbutton" onclick="myFunction();"/>
                    <input type="hidden" id="submit_confirm_value" name="submit_confirm" value=""/>
            </td>
	</tr>
</table>
</form>
</div>
<?php
include 'footer_bottom.php';
?>