<?php

namespace Eventjet\I18n;

use SplObjectStorage;

class TranslationMap implements TranslationMapInterface
{
    /**
     * @var Translation[]
     */
    private $translations;

    /**
     * @param Translation[] $translations
     */
    public function __construct(array $translations)
    {
        $this->translations = new SplObjectStorage();
        foreach ($translations as $translation) {
            $this->translations->offsetSet($translation->getLanguage(), $translation->getText());
        }
    }

    /**
     * @param Language $language
     * @return bool
     */
    public function has(Language $language)
    {
        return $this->translations->offsetExists($language);
    }

    /**
     * @param Language $language
     * @return string
     */
    public function get(Language $language)
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
}
