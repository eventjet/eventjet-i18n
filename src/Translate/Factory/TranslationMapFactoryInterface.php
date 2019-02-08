<?php declare(strict_types=1);

namespace Eventjet\I18n\Translate\Factory;

use Eventjet\I18n\Translate\TranslationMapInterface;

interface TranslationMapFactoryInterface
{
    /**
     * @param array $mapData
     * @return TranslationMapInterface|null Returns null if the map data doesn't contain any translations
     */
    public function create(array $mapData);
}
