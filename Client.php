<?php

class ClientApi
{
    private function addClient($first_name, $middle_name, $last_name, $birthday, $phone, $card_number)
    {
        $sql = "INSERT INTO client(first_name, middle_name, last_name, birthday, phone, card_number) VALUE 
()";
    }

    private function editClient()
    {
    }

    private function findClient()
    {
    }
}