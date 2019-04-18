<?php

require_once 'Api.php';

class ConfiguratorApi extends Api
{
    public function getConfigurator(): string
    {
        return json_encode($this->config);
    }

    private function updateConfigurator()
    {
    }
}
