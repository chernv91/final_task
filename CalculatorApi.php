<?php

require_once 'Api.php';

class CalculatorApi extends Api
{
    //protected $sumBonus;

    public function getBonuses($purchase_sum, $id)
    {
        return $this->calculateBonuses($purchase_sum, $id);
    }

    protected function calculateBonuses($purchase_sum, $id)
    {
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

        return $bonuses;
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

$calc = new CalculatorApi($config);
echo $calc->getBonuses(1000, 2);

