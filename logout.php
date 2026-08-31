<?php
require_once('includes/globalSession.php');
require_once('includes/checklogin.php');

require_once 'product_doc_tracker.php';
track_user();
//echo $_SESSION['sess_access_token'];

function callAPI($method, $url, $data = null){
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);

    if (!empty($data)) {
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    }

    curl_setopt($curl, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer '.$_SESSION['sess_access_token'],
        'User-Agent: Mozilla/5.0'
    ]);

    $result = curl_exec($curl);
    if (curl_errno($curl)) {
        echo 'cURL Error: ' . curl_error($curl);
    }
    curl_close($curl);
    return $result;
}
$apiuserurl = USER_LOGIN_API_URL_PROD.'sign-out-aws';
$getuserdata = callAPI('POST', $apiuserurl, null);
$resuserdata = json_decode($getuserdata, true);
// echo "<pre>";
// print_r($resuserdata);
// echo "</pre>";
// die;

@session_unset();
@session_destroy();

if(isset($_GET['auth'])) $auth = '?auth=1';
else $auth = '';
if (isset($_COOKIE['competiscaner'])) {
    $COOKIEDOMAIN = '.competiscan.com';
    $COOKIEPATH = '/';
    setcookie('competiscaner', '', time() - 3600, $COOKIEPATH, $COOKIEDOMAIN);
    //setcookie("competiscaner", "", time() - 3600, "/");
}
ob_end_clean();


?>
<script>
    // Clear all client-side cookies
    // document.cookie.split(":").forEach(function(cookie) {
    //     var name = cookie.split("=")[0].trim();
    //     document.cookie = name + "=;expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
    // });
</script>
<?php 
header("Location: index.php$auth");
exit;
?>
