<?php

declare(strict_types=1);

namespace Eventjet\I18n\Language;

use Eventjet\I18n\Exception\InvalidLanguageFormatException;

use function preg_match;
use function sprintf;
use function strpos;
use function substr;

/**
 * @final will be marked final with the next version
 * @psalm-immutable
 */
class Language implements LanguageInterface
{
    /** @var array<string, Language> */
    private static array $pool = [];
    private string $language;
    private bool $hasRegion;

    /**
     * @param string $language
     */
    private function __construct($language)
    {
        if (!self::isValid($language)) {
            throw new InvalidLanguageFormatException(sprintf('Invalid language "%s".', $language));
        }
        $this->language = $language;
        $this->hasRegion = strpos($this->language, '-') !== false;
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
     * @psalm-allow-private-mutation
     */
    public function hasRegion()
    {
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
     * @psalm-pure
     * @psalm-suppress ImpureStaticProperty It's fine
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
