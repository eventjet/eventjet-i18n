<?php

declare(strict_types=1);

use Eventjet\I18n\Translate\TranslationMap;

/**
* @param mixed $mapData
 */
function createMap($mapData): ?TranslationMap
{
    if (!TranslationMap::canCreate($mapData)) {
        return null;
    }
    return TranslationMap::create($mapData);
}
