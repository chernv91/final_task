<?php

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
    private $routers = ['users', 'clients', 'card_operations', 'calculators', 'configurators'];
    public $objName;
    public $objMethodName;
    public $config;

    public function __construct()
    {
        header("Access-Control-Allow-Orgin: *");
        header("Access-Control-Allow-Methods: *");
        $this->requestUri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
        if ($this->requestUri[1] !== 'api' || !in_array($this->requestUri[2], $this->routers, true)) {
            header('HTTP/1.0 400 Bad Request');
            echo json_encode(['code' => '400', 'message' => 'API Not Found']);
            exit();
        } else {
            require_once 'config.php';
            global $config;
            $this->config = $config;
            unset($config);
            //Раскоментить только после того как в скрипте везде будет джейсон!
            //header("Content-Type: application/json");

            $this->requestMethod = $_SERVER['REQUEST_METHOD'];
            $this->requestParams = $_REQUEST;
            $this->action = $this->getAction();
            $this->connectDb();
            $this->loyaltyProgram = array_search(true, $this->config['loyalty_program'], true);
            $this->cardNumberType = array_search(true, $this->config['card_number_type'], true);
            $this->sumBonus = array_reverse($this->config['sum_bonus'], true);

            $obj_name = explode('_', substr($this->requestUri[2], 0, -1));
            foreach ($obj_name as &$part) {
                $part = ucfirst($part);
            }
            unset($part);
            $obj_name = implode($obj_name);
            $this->objName = $obj_name . 'Api';

            if ('CalculatorApi' === $this->objName) {
                $param = $this->requestUri[3];
                $this->objMethodName = $param === 'bonuses' ? 'getBonuses' : 'getMaxPossibleSum';
            } else {
                if ('cards_count' === $this->requestUri[3]) {
                    $this->objMethodName = 'getCardsCount';
                } else {
                    $this->objMethodName = $this->action . $obj_name;
                }

            }
            file_put_contents('20.txt', $this->objName . '  ' . $this->objMethodName, FILE_APPEND);
        }

    }

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

