<?php
//next eample will change status of specific conversation to resolve
//$first_name = $_POST['first_name'];
//$middle_name = $_POST['middle_name'];
//$last_name = $_POST['last_name'];
//$phone = $_POST['phone'];
//$birthday = $_POST['birthday'];
//$card_number = $_POST['card_number'];
//$data = [
//    'first_name' => $first_name,
//    'middle_name' => $middle_name,
//    'last_name' => $last_name,
//    'phone' => $phone,
//    'birthday' => $birthday,
//    'card_number' => $card_number,
//];
//$data_json = json_encode($data);
//$service_url = 'http://localhost/final_task';
//
//$service_url .= "/$first_name";
//$ch = curl_init();
//curl_setopt($ch, CURLOPT_URL, $service_url);
//curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($data_json)));
//curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
//curl_setopt($ch, CURLOPT_POSTFIELDS,$data_json);
//curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//$response  = curl_exec($ch);
//curl_close($ch);

$better_token = md5(uniqid(rand(),1));
//echo $better_token;

$str = "{\"name\":\"\u0412\u044b\u043f\u0443\u0441\u043a \u043a\u0430\u0440\u0442\u044b\",\"datetime\":\"2019-04-15 14:39:39\",\"old_value\":\"\",\"new_value\":\"\"}][{\"name\":\"\u0412\u044b\u043f\u0443\u0441\u043a \u043a\u0430\u0440\u0442\u044b\",\"datetime\":\"2019-04-15 14:39:39\",\"old_value\":\"\",\"new_value\":\"\"}";
print_r(json_decode($str));
//echo $str;