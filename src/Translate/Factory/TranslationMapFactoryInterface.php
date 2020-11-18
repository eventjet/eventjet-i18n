<?php

declare(strict_types=1);

namespace Eventjet\I18n\Translate\Factory;

use Eventjet\I18n\Translate\TranslationMapInterface;

/**
 * @deprecated use {@see TranslationMap::create} instead
 */
interface TranslationMapFactoryInterface
{
    /**
     * @param array<string, string> $mapData
     * @return TranslationMapInterface|null Returns null if the map data doesn't contain any translations
     */
    public function create(array $mapData);
}
