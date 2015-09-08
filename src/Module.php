<?php

namespace Eventjet\I18n;

class Module
{
    /**
     * @return array
     */
    public function getConfig()
    {
        return include dirname(__DIR__) . '/config/module.config.php';
    }
}
