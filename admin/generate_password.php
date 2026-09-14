<?php 
require_once("../auth_auth.php"); 
//require_once '../includes/functions.php';
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

function generateRandomPassword($length = 12) {
    // Define character sets
    $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $lowercase = 'abcdefghijklmnopqrstuvwxyz';
    $digits = '0123456789';
    $specialChars = '!@#%&*+-=';

    // Ensure at least one character from each required set
    $password = '';
    $password .= $uppercase[rand(0, strlen($uppercase) - 1)];
    $password .= $lowercase[rand(0, strlen($lowercase) - 1)];
    $password .= $digits[rand(0, strlen($digits) - 1)];
    $password .= $specialChars[rand(0, strlen($specialChars) - 1)];

    // Fill the rest of the password with a mix of all character sets
    $allChars = $uppercase . $lowercase . $digits . $specialChars;
    for ($i = 4; $i < $length; $i++) {
        $password .= $allChars[rand(0, strlen($allChars) - 1)];
    }

    // Shuffle to prevent predictable order
    return str_shuffle($password);
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = strtolower(trim($_POST['email']));

    $password= generateRandomPassword(); 
    $payload = json_encode([
        "email" => $email,
        "password" => $password
    ]);
	$apiuserurl=USER_LOGIN_API_URL_PROD.'admin-change-password';
    $getuserdata= callAPI('POST', $apiuserurl, $payload);
    $resuserdata = json_decode($getuserdata, true);
    if($resuserdata['code']==200){
        if($resuserdata['message']=='Password changed successfully by admin'){
            $message='Your password has been changed successfully.';
        }
        echo $password."##".$message; exit;

    }else{
        echo $resuserdata['message']; exit;
    }
    // echo "<pre>";
    // print_r($resuserdata);
    // echo "<pre>";
    // die;
} 
?>