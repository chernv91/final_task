<?php

require_once 'Api.php';
require_once 'CardOperationApi.php';
require_once 'config.php';

class ClientApi extends Api
{
    public function getClient()
    {
        $cardNumber = array_pop($this->requestUri);
        $loyaltyProgramField = 'discount' === $this->loyaltyProgram ? 'discount' : 'bonus_balance';
        $cardNumberField = 'phone' === $this->cardNumberType ? 'phone' : 'card_number';

        if ('phone' === $cardNumberField) {
            $phone = $cardNumber;
            $sql = "SELECT $loyaltyProgramField FROM client WHERE $cardNumberField = :phone";
            $data = $this->db->prepare($sql);
            $data->bindParam(':phone', $phone, PDO::PARAM_INT);
        } else {
            $sql = "SELECT $loyaltyProgramField FROM client WHERE $cardNumberField = :cardNumber";
            $data = $this->db->prepare($sql);
            $data->bindParam(':cardNumber', $cardNumber, PDO::PARAM_INT);
        }

        try {
            $data->execute();
        } catch (PDOException $e) {
            echo 'Ошибка; ' . $e->getMessage();
        }

        $value = $data->fetchAll()[0][$loyaltyProgramField];

        return json_encode([$loyaltyProgramField => $value]);
    }

    public function updateClient($id, $params = ['bonus_balance' => 200, 'total_sum' => 1000.25])
    {
        $sql = 'UPDATE client SET ';

        foreach ($params as $key => $value) {
            $sql .= "$key = :$key, ";
        }

        $sql = substr($sql, 0, -2);
        $sql .= ' WHERE id = :id';

        $data = $this->db->prepare($sql);

        foreach ($params as $key => $value) {
            $data->bindParam(":$key", $params[$key]);
        }

        $data->bindParam(':id', $id);

        try {
            $data->execute();
        } catch (PDOException $e) {
            echo 'Ошибка: ' . $e->getMessage();
        }
    }

    public function createClient()
    {
        $sql = "INSERT INTO client(";
        $params = [];
        foreach ($_POST as $key => $value) {
            if (!empty($value)) {
                $params[$key] = $value;
                $sql .= $key . ', ';
            }
        }
        $sql = substr($sql, 0, -2) . ') VALUE(';

        foreach ($params as $key => $value) {
            $sql .= ":$key, ";
        }

        $sql = substr($sql, 0, -2) . ')';

        $data = $this->db->prepare($sql);

        foreach ($params as $key => $value) {
            if ($key === 'phone' || $key === 'discount' || $key === 'card_number') {
                $data->bindParam(":$key", $params[$key], PDO::PARAM_INT);
            } else {
                $data->bindParam(":$key", $params[$key]);
            }

        }

        try {
            $data->execute();
        } catch (PDOException $e) {
            echo 'Ошибка; ' . $e->getMessage();
        }
        //убрать
        $config = [];
        //Выпуск карты
        if (array_key_exists('card_number', $params)) {
            $client_id = (int)$this->db->lastInsertId();
            $user_api_key = '7828a24b71c7d916ba97b267730ab57a';
            $card_operation = new CardOperationApi($config);

            try {
                $card_operation->createCardOperation('Выпуск карты', $client_id, $user_api_key);
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        }

        return 'Клиент успешно добавлен';
    }

    public function deleteClient($id)
    {
        $sql = "DELETE FROM client WHERE id =:id";
        $data = $this->db->prepare($sql);
        $data->bindParam(':id', $id, PDO::PARAM_STR);

        try {
            $data->execute();
        } catch (PDOException $e) {
            echo 'Ошибка; ' . $e->getMessage();
        }

        return 'Клиент удален';
    }
}

/*$api = new Api($config);
$client_api = new ClientApi($config);
$client_api->createClient();
print_r($client_api->getClient());
//var_dump($user_api->getUser('5550d565b6f28a76f1c94ff87e8d9cd9'));
//var_dump($user_api->deleteUser('9828a24b71c7d916ba97b267730ab57a'));
//var_dump($client_api->createClient('Степан', 'Иванович', 'Иванов', '1966-03-09', 79787951471, 126, 8));
//var_dump($client_api->getClient('79787951475'));
var_dump($client_api->updateClient(2));*/
