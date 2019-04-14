<?php

class Api
{
    public $requestUri;
    public $requestMethod;
    public $action;
    protected $db;

    public function __construct()
    {
        $this->requestUri = $_SERVER['REQUEST_URI'];
        $this->requestMethod = $_SERVER['REQUEST_METHOD'];
        $this->action = $this->getAction();
    }

    public function connectDb()
    {
        $host = 'localhost';
        $user = 'user';
        $password = '123';
        $db = 'bonus_service';

        $dsn = "mysql:host=$host;dbname=$db";
        try {
            $this->db = new PDO($dsn, $user, $password);
        } catch (PDOException $e) {
            echo 'Подключение не удалось; ' . $e->getMessage();
        }
    }

    public function getAction()
    {
        $action = null;
        switch ($this->requestMethod) {
            case 'GET':
                $action = 'find';
                break;
            case 'PUT':
                $action = 'edit';
                break;
            case 'DELETE':
                $action = 'delete';
                break;
            case 'POST':
                $action = 'add';
                break;
        }
        return $action;
    }
}