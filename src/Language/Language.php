<?php

namespace Eventjet\I18n\Language;

use Eventjet\I18n\Exception\InvalidLanguageFormatException;

class Language implements LanguageInterface
{
    /** @var Language[] */
    private static $pool = [];
    /** @var string */
    private $language;
    /** @var bool */
    private $hasRegion;

    /**
     * @param string $language
     */
    private function __construct($language)
    {
        if (!self::isValid($language)) {
            throw new InvalidLanguageFormatException(sprintf('Invalid language "%s".', $language));
        }
        $this->language = $language;
    }

    /**
     * @param string $language
     * @return bool
     */
    public static function isValid($language)
    {
        return preg_match('/^([a-z]{2}(\-[A-Z]{2})?)$/', $language) === 1;
    }

    /**
     * @return bool
     */
    public function hasRegion()
    {
        if ($this->hasRegion === null) {
            $this->hasRegion = strstr($this->language, '-') !== false;
        }
        return $this->hasRegion;
    }

    /**
     * @return Language
     */
    public function getBaseLanguage()
    {
        return self::get(substr($this->language, 0, 2));
    }

    /**
     * @param string $language
     * @return Language
     */
    public static function get($language)
    {
        if (!isset(self::$pool[$language])) {
            self::$pool[$language] = new self($language);
        }
        return self::$pool[$language];
    }
}
