<?php 
    require_once('includes/globalSession.php');
    require_once('includes/checklogin.php');
    function checkPassword(){
        global $DRW, $DRW_read;
        $query = "SELECT password FROM cscan_users WHERE userID='".$_SESSION['sess_userID']."'" ;
        $rs = $DRW->query($query,$DRW_read);
        $data = $DRW->fetch_row($rs);
        if($data[0] == $_GET['oldPassword']){
            echo "true";
        }else{
            echo "false";
        }
    }

    if(!empty($_GET['oldPassword'])){
        checkPassword();
    }
?>