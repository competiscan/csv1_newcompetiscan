<?php
require_once('includes/globalSession.php');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json');
//require_once('includes/checklogin.php');
// echo "<pre>";
// print_r($_SESSION);
// echo "</pre>";
// die;
if(!isset($_SESSION['sess_username']) || $_SESSION['sess_username']=='' || $_SESSION['sess_userType']=='u'){
       $login_user['success']=false;
}else {
    if($_SESSION['sess_userType']=='a' && isset($_SESSION['sess_userType'])){
        $login_user['success']=true;
        $login_user['user_email']=$_SESSION['sess_username'];
        $login_user['user_id']=$_SESSION['sess_userID'];
        
        //echo "Session started successfully.";
    }
}
echo json_encode($login_user);
die;
?>