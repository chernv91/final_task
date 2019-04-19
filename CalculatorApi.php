<?php

require_once 'Api.php';
require_once 'ClientApi.php';
require_once 'CardOperationApi.php';

class CalculatorApi extends Api
{
    public function getBonuses()
    {
        $purchaseSum = array_pop($this->requestUri);
        $id = array_pop($this->requestUri);

        return $this->calculateBonuses($id, $purchaseSum);
    }

    protected function calculateBonuses(float $purchaseSum, int $id): string
    {
        $bonuses = 0;

        foreach ($this->sumBonus as $sum => $bonus) {

            if ($purchaseSum > $sum) {
                $bonuses = $bonus;
                break;
            }

        }

        $isHoliday = $this->checkIsHoliday();
        $isBirthday = $this->checkIsBirthday($id);

        if ($isHoliday || $isBirthday) {
            $bonuses *= 2;
        }

        return $bonuses;
    }

    /**
     * Метод проверяет является ли сегодняшний день праздником, согласно производственному календарю
     *
     * @return bool
     */
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

    /**
     * Метод проверят является ли сегодняшний день днем рождения клиента
     *
     * @param int $id
     *
     * @return bool
     */
    protected function checkIsBirthday(int $id): bool
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

    /**
     * Метод возвращает максимально возможную сумму для оплаты бонусами
     *
     * @return string
     */
    public function getMaxPossibleSum(): string
    {
        $purchaseSum = array_pop($this->requestUri);
        $maxPercent = $this->config['bonus_payment_percent'];
        return $this->calculateMaxPossibleSum($purchaseSum, $maxPercent);
    }

    /**
     * Метод рассчитывает максимально возможную сумму для оплаты бонусами
     *
     * @param float $purchaseSum
     * @param float $maxPercent
     *
     * @return string
     */
    protected function calculateMaxPossibleSum(float $purchaseSum, float $maxPercent): string
    {
        $maxSum = $purchaseSum * $maxPercent / 100;
        return json_encode(['maxSum' => $maxSum]);
    }
}

