<?php

namespace Eventjet\I18n\Validation;

/**
 * Validates language keys
 *
 * Class LanguageFormatValidator
 *
 * @package Eventjet\I18n
 */
class LanguageFormatValidator
{
    /**
     * @param string $key
     * @return bool
     */
    public static function isValid($key)
    {
        return preg_match('/^([a-z]{2}(\-[A-Z]{2})?)$/', $key) === 1;
    }
}
