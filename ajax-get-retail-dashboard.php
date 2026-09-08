<?php
require_once('includes/globalSession.php');

 function callAPI($method, $url, $data){
    $curl = curl_init();
    switch ($method){
       case "POST":
          curl_setopt($curl, CURLOPT_POST, 1);
          if ($data)
             curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
          break;
       case "PUT":
          curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
          if ($data)
             curl_setopt($curl, CURLOPT_POSTFIELDS, $data);			 					
          break;
        case "DELETE":
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
        if ($data) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        break;
        case "GET":
            curl_setopt($curl, CURLOPT_URL, $url);
            break;
       default:
          if ($data)
             $url = sprintf("%s?%s", $url, http_build_query($data));
    }
    // OPTIONS:
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json','User-Agent:'.$_SERVER['HTTP_USER_AGENT'], 'X-Forwarded-For:'.$_SERVER['REMOTE_ADDR']));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    // EXECUTE:
    $result = curl_exec($curl);
    if(!$result){die("Connection Failure");}
    curl_close($curl);
    return $result;
 } 

if(isset($_GET['q'])  AND $_GET['q']!=''){
$q = $_GET['q'];
$url = "https://api1-uat.competiscan.com/energy-dashboard-client/v1/retail-energy-marketer";

$data = json_encode([
    "search" => $q
]);

$ch = curl_init($url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);

curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);

$output = [];

if(!empty($result['Data'])){
    foreach($result['Data'] as $row){
        $output[] = [
            "id" => $row['id'],
            "name" => $row['name']
        ];
    }
}

echo json_encode($output);
}

if (!empty($_POST['energy_type'])) {

    $energy_type = $_POST['energy_type'];

    // If array convert to comma separated
    if (is_array($energy_type)) {
        $energy_type = implode(',', $energy_type);
    }

    $energy_type = trim($energy_type);

    $API_DASHBOARD_EENRGY = RETAIL_DASHBOARD_UAT . 'electricity-natural-gas?id=' . urlencode($energy_type);

    $out = '<option value="" selected="selected">Any</option>';

    if (!empty($energy_type)) {

        $getedc_data = callAPI('GET', $API_DASHBOARD_EENRGY, false);
        $response_edc = json_decode($getedc_data, true);

        if (!empty($response_edc['data'])) {

            $sessionEdc = array();
            if (!empty($_SESSION['edc_id'])) {
                $sessionEdc = $_SESSION['edc_id'];
            }

            foreach ($response_edc['data'] as $row_edc_data) {

                if (empty($row_edc_data['edc_name']) || empty($row_edc_data['edc_id'])) {
                    continue;
                }

                $edcName = trim($row_edc_data['edc_name']);
                $edc_id  = trim($row_edc_data['edc_id']);

                if ($edcName == '') continue;

                $selected = '';
                if (in_array($edc_id, $sessionEdc)) {
                    $selected = ' selected="selected"';
                }

                $out .= '<option value="' . htmlspecialchars($edc_id, ENT_QUOTES) . '"' . $selected . '>'
                      . htmlspecialchars($edcName) . '</option>';
            }
        }
    }

    echo $out;
    exit;
}
?>