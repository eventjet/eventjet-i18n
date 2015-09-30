<?php

namespace Eventjet\I18n\Translate\Factory;

use Eventjet\I18n\Translate\TranslationMapInterface;

interface TranslationMapFactoryInterface
{
    /**
     * @param array $mapData
     * @return TranslationMapInterface
     */
    public function create(array $mapData);
}
