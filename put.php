<?php
//next eample will change status of specific conversation to resolve
$first_name = $_POST['first_name'];
$middle_name = $_POST['middle_name'];
$last_name = $_POST['last_name'];
$phone = $_POST['phone'];
$birthday = $_POST['birthday'];
$card_number = $_POST['card_number'];

$data = [$first_name, $middle_name, $last_name, $phone, $birthday, $card_number];
$service_url = 'http://localhost/final_task';

foreach ($data as $item) {
    if (!empty($item)) {
        $service_url .= "/$item";
    }
}

$ch = curl_init($service_url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
$data = array("status" => 'R');
curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($data));
$response = curl_exec($ch);
if ($response === false) {
    $info = curl_getinfo($ch);
    curl_close($ch);
    die('error occured during curl exec. Additioanl info: ' . var_export($info));
}
curl_close($ch);
$decoded = json_decode($response);
if (isset($decoded->response->status) && $decoded->response->status == 'ERROR') {
    die('error occured: ' . $decoded->response->errormessage);
}
echo 'response ok!';
var_export($decoded->response);

