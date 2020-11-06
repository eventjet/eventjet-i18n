<?php

declare(strict_types=1);

namespace Eventjet\I18n\Translate\Factory;

use Eventjet\I18n\Language\Language;
use Eventjet\I18n\Translate\Translation;
use Eventjet\I18n\Translate\TranslationMap;
use Eventjet\I18n\Translate\TranslationMapInterface;

use function array_filter;
use function array_keys;
use function array_map;
use function count;

class TranslationMapFactory implements TranslationMapFactoryInterface
{
    /**
     * @param array<string, string> $mapData
     * @return TranslationMapInterface|null Returns null if the map data doesn't contain any translations
     */
    public function create(array $mapData)
    {
        $mapData = array_map('trim', $mapData);
        $mapData = array_filter($mapData, [$this, 'isTextNotEmpty']);
        if (count($mapData) === 0) {
            return null;
        }
        $translations = array_map(function ($text, $lang) {
            return new Translation(Language::get($lang), $text);
        }, $mapData, array_keys($mapData));
        return new TranslationMap($translations);
    }

    /**
     * @param string $text
     */
    private function isTextNotEmpty($text): bool
    {
        return $text !== '';
    }
}
