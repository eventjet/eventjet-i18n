<?php

namespace Eventjet\I18n\Translate;

use Eventjet\I18n\Language\LanguageInterface;
use SplObjectStorage;

interface TranslationMapInterface
{
    /**
     * @param LanguageInterface $language
     * @return bool
     */
    public function has(LanguageInterface $language);

    /**
     * @param LanguageInterface $language
     * @return string
     */
    public function get(LanguageInterface $language);

    /**
     * @return Translation[]|SplObjectStorage
     */
    public function getAll();

    /**
     * @param TranslationInterface $translation
     * @return TranslationMapInterface
     */
    public function withTranslation(TranslationInterface $translation);
}
