<?php

require_once 'Api.php';

class CalculatorApi extends Api
{
    public function getBonuses($purchase_sum, $id)
    {
        return $this->calculateBonuses($purchase_sum, $id);
    }

    protected function calculateBonuses($purchase_sum, $id)
    {
        $isHoliday = $this->checkIsHoliday();
        $isBirthday = $this->checkIsBirthday($id);
        var_dump($isBirthday);
    }

    protected function checkIsHoliday()
    {
        $today = date('Y-m-d');
        //$sql =
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
            echo 'Ошибка; ' . $e->getMessage();
        }

        $birthday = $data->fetchAll()[0]['birthday'];
        $birthday = date('m-d', strtotime($birthday));

        return $today === $birthday;
        
        
    }
}

$calc = new CalculatorApi($config);
echo $calc->getBonuses(1000, 2);

