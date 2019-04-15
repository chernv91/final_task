<?php

require_once 'Api.php';

class qCardOperationApi extends Api
{
    private function getCardOperation()
    {
    }

    public function createCardOperation($name, $client_id, $user_api_key, $old_value='', $new_value='')
    {
        $sql = "INSERT INTO card_operation(name, client_id, user_api_key, old_value, new_value) VALUE(:name, :client_id, :user_api_key, :old_value, :new_value)";
        $data = $this->db->prepare($sql);
        $data->bindParam(':name', $name);
        $data->bindParam(':client_id', $client_id, PDO::PARAM_INT);
        $data->bindParam(':user_api_key', $user_api_key);
        $data->bindParam(':old_value', $old_value);
        $data->bindParam(':new_value', $new_value);
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