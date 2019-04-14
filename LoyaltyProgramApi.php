<?php

require_once 'Api.php';

class LoyaltyProgramApi extends Api
{
    private function getLoyaltyProgram()
    {
    }

    private function updateLoyaltyProgram()
    {
    }
}

$config = [
    'loyalty_program' => [
        'discount' =>  true,
        'bonus_type1' =>  false,
        'bonus_type2' =>  false
    ],
    'card_number' => [
        'phone' => true,
        'card_number' => false
    ],
    'bonus_payment_percent' => '100'
];