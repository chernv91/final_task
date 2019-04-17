<?php

require_once 'Api.php';

class CardOperationApi extends Api
{
    private function getCardOperation()
    {
    }

    public function createCardOperation()
    {
        $user_api_key = '7828a24b71c7d916ba97b267730ab57a';
        $sql = "INSERT INTO card_operation(user_api_key, ";

        $params = [];
        foreach ($_POST as $key => $value) {
            if (!empty($value)) {
                $params[$key] = $value;
                $sql .= $key . ', ';
            }
        }

        $sql = substr($sql, 0, -2) . ') VALUE(:user_api_key, ';

        foreach ($params as $key => $value) {
            $sql .= ":$key, ";
        }

        $sql = substr($sql, 0, -2) . ')';
        file_put_contents('16.txt', $sql);
        $data = $this->db->prepare($sql);

        foreach ($params as $key => $value) {
            //перепроверить
            if (is_numeric($key)) {
                $data->bindParam(":$key", $params[$key], PDO::PARAM_INT);
            } else {
                $data->bindParam(":$key", $params[$key]);
            }

        }
        $data->bindParam(':user_api_key', $user_api_key);
        try {
            $data->execute();
        } catch (PDOException $e) {
            echo 'Ошибка; ' . $e->getMessage();
        }

        return true;
    }
}
/*
$api = new Api();
//var_dump($user_api->getUser('5550d565b6f28a76f1c94ff87e8d9cd9'));
//var_dump($user_api->deleteUser('9828a24b71c7d916ba97b267730ab57a'));
//var_dump($client_api->createClient('Руслан', 'Иванович', 'Иванов', '1966-03-09', 79787951477, 124, 8));
$user_api_key = '7828a24b71c7d916ba97b267730ab57a';
$card_operation = new CardOperationApi();

try {
    $card_operation->createCardOperation('Выпуск карты', 1, $user_api_key);
} catch (Exception $e) {
    echo $e->getMessage();
}*/