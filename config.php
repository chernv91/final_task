<?php
$config = [
    'loyalty_program'       => [
        'Скидка'    => false,
        'Бонусы ("умная касса")' => false,
        'Бонусы ("глупая касса")' => true,
    ],
    'card_number_type'      => [
        'Номер телефона'       => false,
        'Номер пластиковой карты' => true,
    ],
    'bonus_payment_percent' => '50',
    'sum_bonus'             => [
        '0'    => 1,
        '100'  => 5,
        '500'  => 10,
        '1000' => 20,
    ],
];