<?php

require_once 'Api.php';
require_once 'CardOperationApi.php';

class ClientApi extends Api
{
    public function getClient($card_number)
    {
        $loyaltyProgramField = 'discount' === $this->loyaltyProgram ? 'discount' : 'bonus_balance';
        $cardNumberField = 'phone' === $this->cardNumberType ? 'phone' : 'card_number';


        if ('phone' === $cardNumberField) {
            $phone = $card_number;
            $sql = "SELECT $loyaltyProgramField FROM client WHERE $cardNumberField = :phone";
            $data = $this->db->prepare($sql);
            $data->bindParam(':phone', $phone, PDO::PARAM_INT);
        } else {
            $sql = "SELECT $loyaltyProgramField FROM client WHERE $cardNumberField = :card_number";
            $data = $this->db->prepare($sql);
            $data->bindParam(':card_number', $card_number, PDO::PARAM_INT);
        }

        try {
            $data->execute();
        } catch (PDOException $e) {
            echo 'Ошибка; ' . $e->getMessage();
        }

        return $data->fetchAll();
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

    public function createClient($first_name, $middle_name, $last_name, $birthday, $phone, $card_number, $discount)
    {
        $sql = "INSERT INTO client(first_name, middle_name, last_name, birthday, phone, card_number, discount)
 VALUE(:first_name, :middle_name, :last_name, :birthday, :phone, :card_number, :discount)";
        $data = $this->db->prepare($sql);
        $data->bindParam(':first_name', $first_name);
        $data->bindParam(':middle_name', $middle_name);
        $data->bindParam(':last_name', $last_name);
        $data->bindParam(':birthday', $birthday);
        $data->bindParam(':phone', $phone, PDO::PARAM_INT);
        $data->bindParam(':card_number', $card_number, PDO::PARAM_INT);
        $data->bindParam(':discount', $discount, PDO::PARAM_INT);
        try {
            $data->execute();
        } catch (PDOException $e) {
            echo 'Ошибка; ' . $e->getMessage();
        }

        $client_id = (int)$this->db->lastInsertId();
        $user_api_key = '7828a24b71c7d916ba97b267730ab57a';
        $card_operation = new CardOperationApi();

        try {
            $card_operation->createCardOperation('Выпуск карты', $client_id, $user_api_key);
        } catch (Exception $e) {
            echo $e->getMessage();
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

$api = new Api($config);
$client_api = new ClientApi($config);
//var_dump($user_api->getUser('5550d565b6f28a76f1c94ff87e8d9cd9'));
//var_dump($user_api->deleteUser('9828a24b71c7d916ba97b267730ab57a'));
//var_dump($client_api->createClient('Степан', 'Иванович', 'Иванов', '1966-03-09', 79787951471, 126, 8));
//var_dump($client_api->getClient('79787951475'));
var_dump($client_api->updateClient(2));
