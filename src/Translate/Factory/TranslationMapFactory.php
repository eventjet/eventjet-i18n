<?php

namespace Eventjet\I18n\Translate\Factory;

use Eventjet\I18n\Language\Language;
use Eventjet\I18n\Translate\Translation;
use Eventjet\I18n\Translate\TranslationMap;
use Eventjet\I18n\Translate\TranslationMapInterface;

class TranslationMapFactory implements TranslationMapFactoryInterface
{
    /**
     * @param array $mapData
     * @return TranslationMapInterface
     */
    public function create(array $mapData)
    {
        $translations = array_map(function ($text, $lang) {
            return new Translation(Language::get($lang), $text);
        }, $mapData, array_keys($mapData));
        return new TranslationMap($translations);
    }
}
