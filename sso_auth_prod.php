<?php
require_once('includes/globalSession.php');
if(isset($_GET['code']) and $_GET['code']!=""){
   $get_request_code=$_GET['code'];
   $url = AUTH_URL."?"."response_type=code"
      ."&client_id=". urlencode(CLIENT_ID_PROD)
      ."&scope=". urlencode(SCOPE_PROD)
      ."&redirect_uri=". urlencode(CALLBACK_URL_PROD);
   $curl = curl_init();
   $params = array(
   CURLOPT_URL =>  ACCESS_TOKEN_URL_PROD."?"
   ."code=".$get_request_code
   ."&grant_type=authorization_code"
   ."&client_id=". CLIENT_ID_PROD
   ."&client_secret=". CLIENT_SECRET_PROD
   ."&redirect_uri=". CALLBACK_URL_PROD,
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
      echo "cURL Error #01: " . $err; die;
   }else {
      // echo "<pre>";
      // print_r($response);
      // echo "</pre>";
      // die;
      $response = json_decode($response, true);
      //echo"okkk".$response['access_token'];
      if(array_key_exists("access_token", $response)) {
         if($response['access_token']){
            $access_token=$response['access_token'];
            $headers = ['Authorization:'.$access_token];
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, ACCESS_API_URL_PROD);
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
            #header("http://localhost/competiscan.com/login_latest.php"); exit;
            header("Location:https://competiscan.com/login_prod.php"); exit;
         }
      }
      if($response['error']=='invalid_grant'){
         echo "Invaild grant";
      } 

   }
}
?>
