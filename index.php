<?php
// сделать автозагрузку
require_once 'Api.php';
require_once 'UserApi.php';
require_once 'ClientApi.php';
require_once 'CardOperationApi.php';

//try {
    $api = new Api();
    $obj = new $api->objName();
    $result = $obj->{$api->objMethodName}();
    //file_put_contents('15.txt', $api->objName . ' ' . $api->objMethodName);
    print_r($result);
/*} catch (Exception $e) {
    echo $e->getMessage();
}*/
