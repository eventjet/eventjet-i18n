<?php

namespace Eventjet\I18n;

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
}
