<?php
// сделать автозагрузку
require_once 'Api.php';
require_once 'UserApi.php';
require_once 'ClientApi.php';
require_once 'CardOperationApi.php';
require_once 'CalculatorApi.php';
require_once 'ConfiguratorApi.php';

//try {
    $api = new Api();
    $obj = new $api->objName();
    $result = $obj->{$api->objMethodName}();
    file_put_contents('18.txt', $api->objName . ' ' . $api->objMethodName, FILE_APPEND);
    print_r($result);
/*} catch (Exception $e) {
    echo $e->getMessage();
}*/

//$better_token = md5(uniqid(rand(),1));
//echo $better_token;
