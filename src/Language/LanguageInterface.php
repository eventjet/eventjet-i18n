<?php declare(strict_types=1);

namespace Eventjet\I18n\Language;

interface LanguageInterface
{
    /**
     * @return bool
     */
    public function hasRegion();

    /**
     * @return LanguageInterface
     */
    public function getBaseLanguage();

    /**
     * @return string
     */
    public function __toString();
}
