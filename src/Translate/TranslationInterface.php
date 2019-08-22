<?php declare(strict_types=1);

namespace Eventjet\I18n\Translate;

use Eventjet\I18n\Language\LanguageInterface;

/**
 * @deprecated Use Translation directly
 */
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
