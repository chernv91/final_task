<?php
//next eample will change status of specific conversation to resolve
$first_name = $_POST['first_name'];
$middle_name = $_POST['middle_name'];
$last_name = $_POST['last_name'];
$phone = $_POST['phone'];
$birthday = $_POST['birthday'];
$card_number = $_POST['card_number'];
$data = [
    'first_name' => $first_name,
    'middle_name' => $middle_name,
    'last_name' => $last_name,
    'phone' => $phone,
    'birthday' => $birthday,
    'card_number' => $card_number,
];
$data_json = json_encode($data);
$service_url = 'http://localhost/final_task';

$service_url .= "/$first_name";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $service_url);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($data_json)));
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
curl_setopt($ch, CURLOPT_POSTFIELDS,$data_json);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response  = curl_exec($ch);
curl_close($ch);
