<?php

namespace Eventjet\I18n;

/**
 * checks a language key if it is syntactically correct
 *
 * Class LanguageFormatValidator
 * @package Eventjet\I18n
 */
class LanguageFormatValidator
{
    /**
     * @var bool
     */
    private static $valid;

    /**
     * @var string|null
     */
    private static $error;

    /**
     * @param $key
     * @return bool
     */
    public static function isValid($key)
    {
        static::$valid = true;
        static::$error = null;

        static::checkHyphen($key);
        static::checkShortFormat($key);
        static::checkLongFormat($key);

        return static::$valid;
    }

    /**
     * @return null|string
     */
    public static function getError()
    {
        return static::$error;
    }

    /**
     * @param $key
     * @return bool
     */
    private static function checkHyphen($key)
    {
        if (strlen($key) > 2 && strpos($key, '-') === false) {
            static::$valid = false;
            static::$error = 'The language-country separator has to be a hyphen.';
            return false;
        }
        return true;
    }

    /**
     * @param $key
     * @return bool
     */
    private static function checkShortFormat($key)
    {
        if (!static::isLongFormat($key) && !preg_match('([a-z]{2})', $key)) {
            static::$valid = false;
            static::$error = sprintf('"%s" is an invalid short format. It is case sensitive, e.g. "de"', $key);
            return false;
        }
        return true;
    }

    /**
     * @param $key
     * @return bool
     */
    private static function checkLongFormat($key)
    {
        if (static::isLongFormat($key) && !preg_match('([a-z]{2}-[A-Z]{2})', $key)) {
            static::$error = sprintf('"%s" is an invalid long format. It is case sensitive, e.g. "de-DE"', $key);
            static::$valid = false;
            return false;
        }
        return true;
    }

    /**
     * @param $key
     * @return bool
     */
    private static function isLongFormat($key)
    {
        if (strlen($key) > 2 && strpos($key, '-') !== false) {
            return true;
        }
        return false;
    }
}