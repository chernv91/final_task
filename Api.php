<?php

require_once 'config.php';

class Api
{
    public $requestUri;
    public $requestMethod;
    public $action;
    public $loyaltyProgram;
    public $cardNumberType;
    public $sumBonus;
    public $db;
    public $userApiKey = '';

    public function __construct($config)
    {
        //$this->requestUri = $_SERVER['REQUEST_URI'];
        //$this->requestMethod = $_SERVER['REQUEST_METHOD'];
        $this->action = $this->getAction();
        $this->connectDb();
        $this->loyaltyProgram = array_search(true, $config['loyalty_program'], true);
        $this->cardNumberType = array_search(true, $config['card_number_type'], true);
        $this->sumBonus = array_reverse($config['sum_bonus'], true);
    }

    public function connectDb()
    {
        $host = 'localhost';
        $user = 'user';
        $password = '123';
        $db = 'bonus_service';
        $options = array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        );

        $dsn = "mysql:host=$host;dbname=$db;charset=utf8";
        try {
            $this->db = new PDO($dsn, $user, $password, $options);
        } catch (PDOException $e) {
            echo 'Подключение не удалось; ' . $e->getMessage();
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

$api = new Api($config);
$api->connectDb();