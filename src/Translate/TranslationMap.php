<?php

namespace Eventjet\I18n\Translate;

use Eventjet\I18n\Language\LanguageInterface;
use InvalidArgumentException;

class TranslationMap implements TranslationMapInterface
{
    /**
     * @var Translation[]
     */
    private $translations = [];

    /**
     * @param TranslationInterface[] $translations
     */
    public function __construct(array $translations)
    {
        if (count($translations) === 0) {
            throw new InvalidArgumentException('Empty translation maps are not allowed.');
        }
        foreach ($translations as $translation) {
            $this->translations[(string)$translation->getLanguage()] = $translation;
        }
    }

    /**
     * @param LanguageInterface $language
     * @return bool
     */
    public function has(LanguageInterface $language)
    {
        return isset($this->translations[(string)$language]);
    }

    /**
     * @param LanguageInterface $language
     * @return string|null
     */
    public function get(LanguageInterface $language)
    {
        if (!isset($this->translations[(string)$language])) {
            return null;
        }
        return $this->translations[(string)$language]->getText();
    }

    /**
     * @return TranslationInterface[]
     */
    public function getAll()
    {
        return $this->translations;
    }

    /**
     * @param TranslationInterface $translation
     * @return TranslationMapInterface
     */
    public function withTranslation(TranslationInterface $translation)
    {
        $newMap = clone $this;
        $newMap->translations[(string)$translation->getLanguage()] = $translation;
        return $newMap;
    }
}
