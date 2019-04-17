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
            $sql = "SELECT id, $loyaltyProgramField, total_sum FROM client WHERE $cardNumberField = :phone";
            $data = $this->db->prepare($sql);
            $data->bindParam(':phone', $phone, PDO::PARAM_INT);
        } else {
            $sql = "SELECT id, $loyaltyProgramField, total_sum FROM client WHERE $cardNumberField = :cardNumber";
            $data = $this->db->prepare($sql);
            $data->bindParam(':cardNumber', $cardNumber, PDO::PARAM_INT);
        }

        try {
            $data->execute();
        } catch (PDOException $e) {
            echo 'Ошибка; ' . $e->getMessage();
        }

        $result = $data->fetchAll();
        $id = $result[0]['id'];
        $value = $result[0][$loyaltyProgramField];
        $totalSum = $result[0]['total_sum'];

        return json_encode(['id' => $id, $loyaltyProgramField => $value, 'total_sum' => $totalSum]);
    }

    //public function updateClient($params = ['bonus_balance' => 200, 'total_sum' => 1000.25])
    public function updateClient()
    {
        $input = explode('&', file_get_contents('php://input'));
        $params = [];

        foreach ($input as $param) {
            $param = explode('=', $param);
            $params[$param[0]] = $param[1];
        }

        if (array_key_exists('operation', $params) && 'add_bonuses' === $params['operation']) {
            $sql = "UPDATE client SET bonus_balance = :bonusBalance,  total_sum = :totalSum WHERE id = :id";
            $data = $this->db->prepare($sql);

            $data->bindParam(':bonusBalance', $params['bonus_balance']);
            $data->bindParam(':totalSum', $params['total_sum']);
            $data->bindParam(':id', $params['id']);
        }


        /*$sql = 'UPDATE client SET ';

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
*/
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
            //перепроверить
            if (is_numeric($key)) {
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

        $client_id = (int)$this->db->lastInsertId();

        return $client_id;
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

/*$api = new Api();
$client_api = new ClientApi();
$client_api->createClient();
print_r($client_api->getClient());
//var_dump($user_api->getUser('5550d565b6f28a76f1c94ff87e8d9cd9'));
//var_dump($user_api->deleteUser('9828a24b71c7d916ba97b267730ab57a'));
//var_dump($client_api->createClient('Степан', 'Иванович', 'Иванов', '1966-03-09', 79787951471, 126, 8));
//var_dump($client_api->getClient('79787951475'));
var_dump($client_api->updateClient(2));*/
