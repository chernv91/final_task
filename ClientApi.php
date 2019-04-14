<?php

require_once 'Api.php';

class ClientApi extends Api
{
    private function getClient()
    {
    }

    private function updateClient()
    {
    }

    private function createClient($first_name, $middle_name, $last_name, $birthday, $phone, $card_number)
    {
        $sql = "INSERT INTO client(first_name, middle_name, last_name, birthday, phone, card_number) VALUE 
()";
    }
    private function deleteClient()
    {

    }
}