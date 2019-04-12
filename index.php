<?php

$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];
$url = $_GET['q'];
$exploded = explode('&', file_get_contents('php://input'));
file_put_contents('13.txt', "$method $uri $url " . $exploded[0]);
if ($method === 'PUT') {
    echo 'ne ok';
}
