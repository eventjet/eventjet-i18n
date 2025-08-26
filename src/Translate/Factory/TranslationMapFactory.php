<?php

declare(strict_types=1);

namespace Eventjet\I18n\Translate\Factory;

use Eventjet\I18n\Language\Language;
use Eventjet\I18n\Translate\Exception\InvalidTranslationMapDataException;
use Eventjet\I18n\Translate\Translation;
use Eventjet\I18n\Translate\TranslationMap;
use Eventjet\I18n\Translate\TranslationMapInterface;
use Override;

use function array_filter;
use function array_map;
use function count;

/**
 * @deprecated use {@see TranslationMap::create} instead
 * @final
 */
class TranslationMapFactory implements TranslationMapFactoryInterface
{
    /**
     * @param array<string, string> $mapData
     * @return TranslationMapInterface|null Returns null if the map data doesn't contain any translations
     */
    #[Override]
    public function create(array $mapData)
    {
        $mapData = array_map('trim', $mapData);
        $mapData = array_filter($mapData, [$this, 'isTextNotEmpty']);
        if (count($mapData) === 0) {
            return null;
        }
        $translations = [];
        foreach ($mapData as $lang => $text) {
            if (!Language::isValid($lang)) {
                throw new InvalidTranslationMapDataException('Given translation map data is invalid');
            }
            $translations[] = new Translation(Language::get($lang), $text);
        }
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
