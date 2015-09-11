<?php

namespace Eventjet\I18n\Translate;

use Eventjet\I18n\Language\Language;
use Eventjet\I18n\Language\LanguageInterface;
use Eventjet\I18n\Language\LanguagePriorityInterface;

class TranslationExtractor implements TranslationExtractorInterface
{
    /**
     * @param TranslationMapInterface                       $map
     * @param LanguagePriorityInterface|LanguageInterface[] $priorities
     * @return string
     */
    public function extract(TranslationMapInterface $map, LanguagePriorityInterface $priorities)
    {
        foreach ($priorities as $language) {
            /** @var LanguageInterface $language */
            if ($map->has($language)) {
                return $map->get($language);
            }
            if ($language->hasRegion()) {
                $baseLanguage = $language->getBaseLanguage();
                if ($map->has($baseLanguage)) {
                    return $map->get($baseLanguage);
                }
            }
        }
        $english = Language::get('en');
        if ($map->has($english)) {
            return $map->get($english);
        }
        $translations = $map->getAll();
        $translations->rewind();
        /** @var LanguageInterface $language */
        $language = $translations->current();
        return $map->get($language);
    }
}
