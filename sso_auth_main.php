<?php
require_once('includes/globalSession.php');
/*if (!isset($_SESSION)) {
   echo "Session not started or session data is missing.";
} else {
   echo "Session started successfully.";
   // print_r($_SESSION);
}
echo "<pre>";
print_r($_SESSION);
echo "<pre>";
*/
//echo $_GET['state'];
$sso_cleint_id="";
$sso_cleint_secret="";
$sso_domain_name="";
if(isset($_GET['state']) and $_GET['state']!=''){
	$sql_query="SELECT * FROM cscan_sso_authorisation WHERE AWS_COGNITO_USER_POOL_CLIENT_ID='".$_REQUEST['state']."'";
	$result_sso = $DRW->query($sql_query,$DRW_read);
	$count    = $DRW->num_rows($result_sso);
	if($count>0){
		$data_sso =   $DRW->fetch_row($result_sso);
		$sso_cleint_id=$data_sso[3];
		$sso_cleint_secret=$data_sso[4];
		$sso_domain_name=$data_sso[5];
	}	

} 
if(isset($_GET['code']) and $_GET['code']!="" && $sso_cleint_id!="" && $sso_domain_name!="" && $sso_cleint_secret!=""){
   $get_request_code=$_GET['code'];
   $url = $sso_domain_name."/login??"."response_type=code"
      ."&client_id=". urlencode($sso_cleint_id)
      ."&scope=". urlencode(SCOPE_MAIN)
      ."&redirect_uri=". urlencode(CALLBACK_URL_MAIN);
   $curl = curl_init();
   $params = array(
   CURLOPT_URL =>  $sso_domain_name."/oauth2/token?"
   ."code=".$get_request_code
   ."&grant_type=authorization_code"
   ."&client_id=". $sso_cleint_id
   ."&client_secret=". $sso_cleint_secret
   ."&redirect_uri=". CALLBACK_URL_MAIN,
   CURLOPT_RETURNTRANSFER => true,
   CURLOPT_MAXREDIRS => 10,
   CURLOPT_TIMEOUT => 30,
   CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
   CURLOPT_CUSTOMREQUEST => "POST",
   CURLOPT_NOBODY => false, 
   CURLOPT_HTTPHEADER => array(
   "cache-control: no-cache",
   "content-type: application/x-www-form-urlencoded",
   "accept: *",
   "accept-encoding: gzip, deflate",
   ),
   );
   curl_setopt_array($curl, $params);
   $response = curl_exec($curl);
   $err = curl_error($curl);
   curl_close($curl);
   if($err) {
      header("Location:https://competiscan.com/login_test.php"); exit;
      //echo "cURL Error #01: " . $err; die;
   }else {
      // echo "<pre>";
      // print_r($response);
      // echo "</pre>";
      $response = json_decode($response, true);
      //echo"okkk".$response['access_token'];
      if(array_key_exists("access_token", $response)) {
         if($response['access_token']){
            $access_token=$response['access_token'];
            $headers = ['Authorization:'.$access_token];
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, ACCESS_UAT_API_URL);
            curl_setopt($ch, CURLOPT_HTTPGET, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // This line captures response data
            $response_user_data = curl_exec($ch);
            $response_data=json_decode($response_user_data);
         //    echo "<pre>";
         //    print_r($response_data);
         //    echo "<pre>";
         //   die;
            foreach ($response_data as $res_data) {
               $user_email=$res_data->email;
               $cognito_id=$res_data->cognito_id;
               $username=$res_data->username;
             }
            if($response_user_data === false){
                echo 'Curl error: ' . curl_error($ch);
            }
            curl_close($ch);
            $_SESSION['sess_username']=$user_email;
            $sql="SELECT userID,number_machines,bypass,companyName,plevel FROM cscan_users WHERE active='y' AND emailAddress='".$user_email."'";
            $result = $DRW->query($sql,$DRW_read);
            $rs        = $DRW->fetch_assoc($result);
            $userID    = $rs['userID'];
            $_SESSION['sess_userID']    = $userID; 
            unset($_SESSION['sess_client_id']);
            unset($_SESSION['sso_cleint_secret']);  
            unset($_SESSION['sso_domain_name']);  
            #header("http://localhost/competiscan.com/login_test.php"); exit;
            header("Location:https://competiscan.com/login_test.php"); exit;
         }
      }
      if($response['error']=='invalid_grant'){
         unset($_SESSION['sess_client_id']);
         unset($_SESSION['sso_cleint_secret']);  
         unset($_SESSION['sso_domain_name']); 
         echo "Invaild grant";
      } 

   }
}
?>
