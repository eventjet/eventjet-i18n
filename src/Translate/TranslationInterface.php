<?php

namespace Eventjet\I18n\Translate;

use Eventjet\I18n\Language\LanguageInterface;

interface TranslationInterface
{
    /**
     * @return LanguageInterface
     */
    public function getLanguage();

    /**
     * @return string
     */
    public function getText();
}
