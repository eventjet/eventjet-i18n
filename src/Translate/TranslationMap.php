<?php

namespace Eventjet\I18n\Translate;

use Eventjet\I18n\Language\LanguageInterface;
use SplObjectStorage;

class TranslationMap implements TranslationMapInterface
{
    /**
     * @var Translation[]|SplObjectStorage
     */
    private $translations;

    /**
     * @param TranslationInterface[] $translations
     */
    public function __construct(array $translations)
    {
        $this->translations = new SplObjectStorage();
        foreach ($translations as $translation) {
            $this->translations->offsetSet($translation->getLanguage(), $translation->getText());
        }
    }

    /**
     * @param LanguageInterface $language
     * @return bool
     */
    public function has(LanguageInterface $language)
    {
        return $this->translations->offsetExists($language);
    }

    /**
     * @param LanguageInterface $language
     * @return string
     */
    public function get(LanguageInterface $language)
    {
        return $this->translations->offsetGet($language);
    }

    /**
     * @return Translation[]
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
        $translations = clone $this->translations;
        $translations->offsetSet($translation->getLanguage(), $translation->getText());
        $newMap->translations = $translations;
        return $newMap;
    }
}
