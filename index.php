<?php
// сделать автозагрузку
require_once 'Api.php';
require_once 'UserApi.php';
require_once 'ClientApi.php';
require_once 'CardOperationApi.php';
require_once 'CalculatorApi.php';
require_once 'ConfiguratorApi.php';

try {
    $api = new Api();
    $obj = new $api->objName();
    $result = $obj->{$api->objMethodName}();
} catch (Exception $e) {
    echo $e->getMessage();
}

