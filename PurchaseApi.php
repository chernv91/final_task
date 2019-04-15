<?php

require_once 'Api.php';
require_once 'ClientApi.php';
require_once 'CardOperationApi.php';

class PurchaseApi extends Api
{
    //protected $sumBonus;

    public function createPurchase($operation, $id, $purchase_sum, $bonus_balance, $total_sum, $subtracted_bonuses)
    {
        $client = new ClientApi();
        $cardOperation = new CardOperationApi();
        if ('discount' === $this->loyaltyProgram) {
            $params = ['total_sum' => $total_sum];
        } elseif ('bonus_type1' === $this->loyaltyProgram) {
            $params = ['bonus_balance' => $bonus_balance, 'total_sum' => $total_sum];
        } else {
            $bonus_balance = $this->getBonuses($operation, $purchase_sum, $id);
            $total_sum = $this->getTotalSum($purchase_sum, $id);
            $params = ['bonus_balance' => $bonus_balance, 'total_sum' => $total_sum];
        }

        $client->updateClient($id, $params);
        //рег-я оборота по карте
        $cardOperation->createCardOperation(4, $id, $user_api_key='5550d565b6f28a76f1c94ff87e8d9cd9');
        if ('subrtact_bonuses' === $operation) {
            $cardOperation->createCardOperation(1, $id, $user_api_key='5550d565b6f28a76f1c94ff87e8d9cd9');
        } elseif ('add_bonuses' === $operation) {
            $cardOperation->createCardOperation(2, $id, $user_api_key='5550d565b6f28a76f1c94ff87e8d9cd9');
        }
    }

    public function getBonuses($operation, $subtracted_bonuses, $purchase_sum, $id)
    {
        return $this->calculateBonuses($operation, $subtracted_bonuses, $purchase_sum, $id);
    }

    public function getTotalSum($purchase_sum, $id)
    {
        return $this->calculateTotalSum($purchase_sum, $id);
    }

    protected function calculateBonuses($operation, $subtracted_bonuses, $purchase_sum, $id)
    {
        if ('subrtact_bonuses' === $operation) {
            $sql = "SELECT bonus_balance FROM client WHERE id=:id";
            $data = $this->db->prepare($sql);
            $data->bindParam(':id', $id);
            try {
                $data->execute();
            } catch (PDOException $e) {
                echo 'Ошибка: ' . $e->getMessage() . "\n";
                exit();
            }
            $oldBonusBalance = $data->fetchAll()[0]['bonus_balance'];
            $bonuses = $oldBonusBalance - $subtracted_bonuses;
        } elseif ('add_bonuses' === $operation) {
            $bonuses = 0;
            foreach ($this->sumBonus as $sum => $bonus) {
                if ($purchase_sum > $sum) {
                    $bonuses = $bonus;
                    break;
                }
            }

            $isHoliday = $this->checkIsHoliday();
            $isBirthday = $this->checkIsBirthday($id);
            //var_dump($isHoliday);
            //var_dump($isBirthday);

            if ($isHoliday || $isBirthday) {
                $bonuses *= 2;
            }
        }

        return $bonuses;
    }

    protected function calculateTotalSum($purchase_sum, $id)
    {
        $sql = "SELECT total_sum FROM client WHERE id=:id";
        $data = $this->db->prepare($sql);
        $data->bindParam(':id', $id);
        try {
            $data->execute();
        } catch (PDOException $e) {
            echo 'Ошибка: ' . $e->getMessage() . "\n";
            exit();
        }
        $oldTotalSum = $data->fetchAll()[0]['total_sum'];
        $newTotalSum = $oldTotalSum + $purchase_sum;

        return $newTotalSum;
    }

    protected function checkIsHoliday(): bool
    {
        $today = date('Y-m-d');
        $sql = "SELECT id FROM holiday WHERE date=:today";
        $data = $this->db->prepare($sql);
        $data->bindParam(':today', $today);
        try {
            $data->execute();
        } catch (PDOException $e) {
            echo 'Ошибка: ' . $e->getMessage() . "\n";
            exit();
        }
        return count($data->fetchAll()) === 1;
    }

    protected function checkIsBirthday($id): bool
    {
        $today = date('m-d');

        $sql = "SELECT birthday FROM client WHERE id=:id";
        $data = $this->db->prepare($sql);
        $data->bindParam(':id', $id, PDO::PARAM_INT);

        try {
            $data->execute();
        } catch (PDOException $e) {
            echo 'Ошибка: ' . $e->getMessage() . "\n";
            exit();
        }

        $birthday = $data->fetchAll()[0]['birthday'];
        $birthday = date('m-d', strtotime($birthday));

        return $today === $birthday;
    }
}

$calc = new PurchaseApi($config);
echo $calc->getBonuses(1000, 2);

