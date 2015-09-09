<?php

namespace Eventjet\I18n;

use Eventjet\I18n\Exception\InvalidLanguageFormatException;

class LanguageFormatValidator
{
    public static function isValid($key)
    {
        static::checkHyphen($key);
        static::checkShortFormat($key);
        static::checkLongFormat($key);
    }

    private static function checkHyphen($key)
    {
        if (strlen($key) > 2 && strpos($key, '-') === false) {
            throw new InvalidLanguageFormatException('The language-country separator has to be a hyphen.');
        }
    }

    private static function checkShortFormat($key)
    {
        if (!static::isLongFormat($key) && !preg_match('([a-z]{2})', $key)) {
            throw new InvalidLanguageFormatException(
                sprintf('"%s" is an invalid short format. It is case sensitive like "de"', $key)
            );
        }
    }

    private static function checkLongFormat($key)
    {
        if (static::isLongFormat($key) && !preg_match('([a-z]{2}-[A-Z]{2})', $key)) {
            throw new InvalidLanguageFormatException(
                sprintf('"%s" is an invalid long format. It is case sensitive like "de-DE"', $key)
            );
        }
    }

    private static function isLongFormat($key)
    {
        if (strlen($key) > 2 && strpos($key, '-') !== false) {
            return true;
        }
        return false;
    }
}