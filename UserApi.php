<?php

require_once 'Api.php';

class UserApi extends Api
{
    public function getUser()
    {
        if (!empty($_SERVER['PHP_AUTH_PW'])) {
            $apiKey = $_SERVER['PHP_AUTH_PW'];
        }

        $sql = "SELECT first_name, last_name, role FROM user WHERE api_key =:apiKey";
        $data = $this->db->prepare($sql);
        $data->bindParam(':apiKey', $apiKey, PDO::PARAM_STR);

        try {
            $data->execute();
        } catch (PDOException $e) {
            echo 'Ошибка; ' . $e->getMessage();
        }

        $result = $data->fetchAll();
        $message = count($result) === 1 ? "Связь установлена, пользователь {$result[0]['last_name']} {$result[0]['first_name']}" : 'Ошибка авторизации';

        return json_encode(['message' => $message, 'role' => $result[0]['role']]);
    }

    private function updateUser()
    {
    }

    public function createUser($api_key, $first_name, $last_name, $role)
    {
        $sql = "INSERT INTO user(api_key, first_name, last_name, role) VALUE(:api_key, :first_name, :last_name, :role)";
        $data = $this->db->prepare($sql);
        $data->bindParam(':api_key', $api_key);
        $data->bindParam(':first_name', $first_name);
        $data->bindParam(':last_name', $last_name);
        $data->bindParam(':role', $role);
        try {
            $data->execute();
        } catch (PDOException $e) {
            echo 'Ошибка; ' . $e->getMessage();
        }

        return 'Пользователь успешно добавлен';
    }

    public function deleteUser($api_key)
    {
        $sql = "DELETE FROM user WHERE api_key =:api_key";
        $data = $this->db->prepare($sql);
        $data->bindParam(':api_key', $api_key, PDO::PARAM_STR);

        try {
            $data->execute();
        } catch (PDOException $e) {
            echo 'Ошибка; ' . $e->getMessage();
        }

        return 'Пользователь удален';
    }
}
/*
$api = new Api();
$user_api = new UserApi();
//var_dump($user_api->getUser('5550d565b6f28a76f1c94ff87e8d9cd9'));
//var_dump($user_api->deleteUser('9828a24b71c7d916ba97b267730ab57a'));
var_dump($user_api->createUser('7828a24b71c7d916ba97b267730ab57a', 'Ирина', 'Чернякова', 1));*/