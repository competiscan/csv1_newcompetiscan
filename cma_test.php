<?php
require_once('includes/globalSession.php');
if(!isset($_SESSION['public_admin_access'])){
	require_once('includes/checklogin.php');
}
 function callAPI($method, $url, $data){
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    curl_setopt($curl, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
		'User-Agent: Mozilla/5.0',
        'Content-Length: ' . strlen($data)
    ]);
    $result = curl_exec($curl);
    curl_close($curl);
    return $result;
}

    $post_data = array();
    if(isset($_GET['id']) && $_GET['id']!=''){
        $post_data['maID'] = (int)$_GET['id'];
    }
    $postdatajson=json_encode($post_data);
    $APIMESSAGEURL=ALERT_API_URL_UAT.'alertContent';
	$get_message_data = callAPI('POST', $APIMESSAGEURL, $postdatajson);
	$response_message = json_decode($get_message_data, true);
    echo $response_message['content'];
	die;
?>