<?php

require_once 'config.php';
require_once 'UserApi.php';

class Api
{
    public $requestUri;
    public $requestMethod;
    public $requestParams;
    public $action;
    public $loyaltyProgram;
    public $cardNumberType;
    public $sumBonus;
    public $db;
    public $userApiKey = '';
    private $routers = ['users', 'clients'];
    public $objName;
    public $objMethodName;

    public function __construct($config)
    {
        header("Content-Type: application/json");
        //header ("Content-Disposition: inline; filename = ajax.json");

        $this->requestUri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
        // Убрать эту строчку потом!!!
        //array_shift($this->requestUri);
        $this->requestMethod = $_SERVER['REQUEST_METHOD'];
        $this->requestParams = $_REQUEST;
        $this->action = $this->getAction();
        $this->connectDb();
        $this->loyaltyProgram = array_search(true, $config['loyalty_program'], true);
        $this->cardNumberType = array_search(true, $config['card_number_type'], true);
        $this->sumBonus = array_reverse($config['sum_bonus'], true);
        //if (array_shift($this->requestUri) !== 'api' || !in_array(array_shift($this->requestUri), $this->routers,
               // true)) {
            /*//throw new Exception('API Not Found', 404);
            //header('HTTP/1.0 400 Bad Request');
            echo json_encode([
                'code'    => '400',
                'message' => 'Bad Request',
            ]);*/

       // }
        //else {
            $obj_name = ucfirst(substr($this->requestUri[2], 0, -1));
            $this->objName = $obj_name . 'Api';
            $this->objMethodName = $this->action . $obj_name;
        //}
    }

    /*public function run()
    {

    }*/


    public function connectDb()
    {
        $host = 'localhost';
        $user = 'user';
        $password = '123';
        $db = 'bonus_service';
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];

        $dsn = "mysql:host=$host;dbname=$db;charset=utf8";
        try {
            $this->db = new PDO($dsn, $user, $password, $options);
        } catch (PDOException $e) {
            //echo 'Подключение не удалось; ' . $e->getMessage();
        }
    }

    public function getAction()
    {
        $action = null;
        switch ($this->requestMethod) {
            case 'GET':
                $action = 'get';
                break;
            case 'PUT':
                $action = 'update';
                break;
            case 'DELETE':
                $action = 'delete';
                break;
            case 'POST':
                $action = 'create';
                break;
        }
        return $action;
    }
}
/*
$api = new Api($config);
$api->connectDb();*/