<?php

require_once 'Api.php';
require_once 'CardOperationApi.php';

class ClientApi extends Api
{
    public function getClient()
    {
        $cardNumber = array_pop($this->requestUri);
        $loyaltyProgramField = 'Скидка' === $this->loyaltyProgram ? 'discount' : 'bonus_balance';
        $cardNumberField = 'Номер телефона' === $this->cardNumberType ? 'phone' : 'card_number';

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

    public function getCardsCount()
    {
        $cardNumberField = 'Номер телефона' === $this->cardNumberType ? 'phone' : 'card_number';

        if ('phone' === $cardNumberField) {
            $sql = "SELECT COUNT(*) AS cnt from client";
        } else {
            $sql = "SELECT COUNT(*) AS cnt from client WHERE card_status = 'Активна'";
        }

        $data = $this->db->prepare($sql);

        try {
            $data->execute();
        } catch (PDOException $e) {
            echo 'Ошибка; ' . $e->getMessage();
        }

        return $data->fetchAll()[0]['cnt'];
    }

    public function updateClient()
    {
        $input = explode('&', file_get_contents('php://input'));
        $params = [];

        foreach ($input as $param) {
            $param = explode('=', $param);
            $params[$param[0]] = $param[1];
        }
// Добавить INT
        if (array_key_exists('operation', $params)) {
            if ('add_bonuses' === $params['operation'] || 'subtract_bonuses' === $params['operation']) {
                $sql = "UPDATE client SET bonus_balance = :bonusBalance,  total_sum = :totalSum WHERE id = :id";
                $data = $this->db->prepare($sql);

                $data->bindParam(':bonusBalance', $params['bonus_balance']);
                $data->bindParam(':totalSum', $params['total_sum']);
                $data->bindParam(':id', $params['id']);
            } elseif ('block_card' === $params['operation'] || 'unblock_card' === $params['operation']) {
                $sql = "UPDATE client SET card_status = :cardStatus WHERE card_number = :cardNumber";
                $data = $this->db->prepare($sql);

                $data->bindParam(':cardStatus', urldecode($params['card_status']));
                $data->bindParam(':cardNumber', $params['card_number']);
            } elseif ('change_percent' === $params['operation']) {
                $cardNumberField = 'Номер телефона' === $this->cardNumberType ? 'phone' : 'card_number';
                $sql = "UPDATE client SET discount = :discount WHERE $cardNumberField = :cardNumber";
                $data = $this->db->prepare($sql);

                $data->bindParam(':discount', $params['discount']);
                $data->bindParam(':cardNumber', $params['card_number']);
            }

        } else {
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

        }


        try {
            $data->execute();
        } catch (PDOException $e) {
            echo 'Ошибка: ' . $e->getMessage();
        }

        return 'ok';
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
            exit;
        }

        $id = (int)$this->db->lastInsertId();

        $message = $id >0 ? 'Клиент создан' : 'Ошибка при создании клиента, операция не выполнена';
        $code = $id > 0 ? '201' : '500';
        header("HTTP/1.0  $code  $message");

        return json_encode(['code' => $code, 'message' => $message, 'id' => $id]);
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
