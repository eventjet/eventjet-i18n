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
        $mapData = array_map('trim', $mapData);
        $mapData = array_filter($mapData, [$this, 'isTextNotEmpty']);
        $translations = array_map(function ($text, $lang) {
            return new Translation(Language::get($lang), $text);
        }, $mapData, array_keys($mapData));
        return new TranslationMap($translations);
    }

    /**
     * @param string $text
     * @return bool
     */
    private function isTextNotEmpty($text)
    {
        return $text !== '';
    }
}
