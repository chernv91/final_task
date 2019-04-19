<?php

require_once 'Api.php';

class ConfiguratorApi extends Api
{
    /**
     * Метод получает настройки приложения из файла config.txt
     *
     * @return string
     */
    public function getConfigurator(): string
    {
        if (empty($this->config) || !is_array($this->config)) {
            header('HTTP/1.0  500 Internal Server Error');
            exit;
        }

        return json_encode($this->config, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Метод обновляет настройки приложения и записывает их в файл config.txt
     *
     * @return string
     */
    public function updateConfigurator(): string
    {
        $input = explode('&', file_get_contents('php://input'));
        $params = [];

        foreach ($input as $param) {
            $param = explode('=', $param);
            $params[$param[0]] = $param[1];
        }

        if (array_key_exists('setting', $params)) {

            if ('loyalty_program' === $params['setting'] || 'card_number_type' === $params['setting']) {

                foreach ($this->config[$params['setting']] as $loyaltyProgram => $value) {

                    if (urldecode($params['checked']) === $loyaltyProgram) {
                        $this->config[$params['setting']][$loyaltyProgram] = true;
                    } else {
                        $this->config[$params['setting']][$loyaltyProgram] = false;
                    }

                }

                $f = fopen('config.txt', 'w');
                $result = fwrite($f, json_encode($this->config));
                fclose($f);

                $message = $result === false ? 'Ошибка при записи настроек в файл' : 'Настройки успешно обновлены';
                $code = $result === false ? '500' : '201';
                header("HTTP/1.0  $code  $message");

                return json_encode(['code' => $code, 'message' => $message], JSON_UNESCAPED_UNICODE);
            }

        }

    }
}
