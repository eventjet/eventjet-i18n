<?php

declare(strict_types=1);

namespace Eventjet\I18n\Language;

use Eventjet\I18n\Exception\InvalidLanguageFormatException;

use function preg_match;
use function sprintf;
use function strpos;
use function substr;

class Language implements LanguageInterface
{
    /** @var Language[] */
    private static array $pool = [];
    private string $language;
    private ?bool $hasRegion = null;

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
        return preg_match('/^([a-z]{2}(-[A-Z]{2})?)$/', $language) === 1;
    }

    /**
     * @return bool
     */
    public function hasRegion()
    {
        if ($this->hasRegion === null) {
            $this->hasRegion = strpos($this->language, '-') !== false;
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

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->language;
    }
}
