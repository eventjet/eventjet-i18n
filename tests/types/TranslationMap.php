<?php

declare(strict_types=1);

use Eventjet\I18n\Translate\TranslationMap;

function createMap(mixed $mapData): ?TranslationMap
{
    if (!TranslationMap::canCreate($mapData)) {
        return null;
    }
    return TranslationMap::create($mapData);
}
