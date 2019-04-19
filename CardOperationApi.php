<?php

require_once 'Api.php';

class CardOperationApi extends Api
{
    public function getCardOperation()
    {
        $operation = $this->requestUri[2];

        if ('subtracted_bonuses_sum' === $operation || 'card_bonuses_sum' === $operation) {
            $name = $operation === 'subtracted_bonuses_sum' ? 'Списание бонусов' : 'Начисление бонусов';
            $sql = "SELECT SUM(new_value) AS sum_bonuses FROM card_operation WHERE name = '$name'";
            $data = $this->db->prepare($sql);

            try {
                $data->execute();
            } catch (PDOException $e) {
                echo 'Ошибка; ' . $e->getMessage();
            }

            $result = $data->fetchAll()[0]['sum_bonuses'];
        } elseif ('card_history' === $operation) {
            $cardNumber = array_pop($this->requestUri);
            $cardNumberField = 'Номер телефона' === $this->cardNumberType ? 'phone' : 'card_number';
            $sql = "SELECT name, datetime, old_value, new_value FROM card_operation WHERE client_id = (SELECT id FROM client WHERE $cardNumberField = :cardNumber)";
            $data = $this->db->prepare($sql);
            $data->bindParam(':cardNumber', $cardNumber);
            try {
                $data->execute();
            } catch (PDOException $e) {
                echo 'Ошибка; ' . $e->getMessage();
            }

            $result = json_encode($data->fetchAll());
            //$result = $data->fetchAll();
            //print_r($result);
        }

        return $result;
    }

    public function createCardOperation()
    {
        $sql = "INSERT INTO card_operation(";

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

        return 'ok';
    }
}