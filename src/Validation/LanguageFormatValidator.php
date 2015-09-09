<?php

namespace Eventjet\I18n\Validation;

/**
 * Validates language keys
 *
 * Valid formats are:
 * * de
 * * en-UK
 *
 * Examples of invalid languages:
 * * en_UK
 * * en-us
 * * eng
 *
 * More formally, language formats must satisfy the following regular expression:
 * ^([a-z]{2}(\-[A-Z]{2})?)$
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
