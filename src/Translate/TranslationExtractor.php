<?php declare(strict_types=1);

namespace Eventjet\I18n\Translate;

use Eventjet\I18n\Language\Language;
use Eventjet\I18n\Language\LanguageInterface;
use Eventjet\I18n\Language\LanguagePriorityInterface;

class TranslationExtractor implements TranslationExtractorInterface
{
    /**
     * @param TranslationMapInterface $map
     * @param LanguagePriorityInterface|LanguageInterface[] $priorities
     * @return string
     */
    public function extract(TranslationMapInterface $map, LanguagePriorityInterface $priorities)
    {
        $string = $this->extractFromPriority($map, $priorities);
        return $string ?? $this->extractFromFallbacks($map);
    }

    private function extractFromPriority(TranslationMapInterface $map, LanguagePriorityInterface $priorities): ?string
    {
        foreach ($priorities as $language) {
            /** @var LanguageInterface $language */
            if ($map->has($language)) {
                return $map->get($language);
            }
            if (!$language->hasRegion()) {
                continue;
            }
            $baseLanguage = $language->getBaseLanguage();
            if ($map->has($baseLanguage)) {
                return $map->get($baseLanguage);
            }
        }
        return null;
    }

    private function extractFromFallbacks(TranslationMapInterface $map): string
    {
        $english = Language::get('en');
        if ($map->has($english)) {
            $return = $map->get($english);
            \assert($return !== null);
            return $return;
        }
        $translations = $map->getAll();
        reset($translations);
        /** @var TranslationInterface $translation */
        $translation = current($translations);
        $return = $map->get($translation->getLanguage());
        \assert($return !== null);
        return $return;
    }
}
