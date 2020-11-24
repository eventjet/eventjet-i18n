<?php

declare(strict_types=1);

namespace Eventjet\I18n\Language;

use Eventjet\I18n\Exception\InvalidLanguageFormatException;

use function preg_match;
use function sprintf;
use function strpos;
use function substr;

final class Language
{
    /** @var array<string, Language> */
    private static array $pool = [];
    private string $language;
    private ?bool $hasRegion = null;

    private function __construct(string $language)
    {
        if (!self::isValid($language)) {
            throw new InvalidLanguageFormatException(sprintf('Invalid language "%s".', $language));
        }
        $this->language = $language;
    }

    public static function isValid(string $language): bool
    {
        return preg_match('/^([a-z]{2}(-[A-Z]{2})?)$/', $language) === 1;
    }

    public static function get(string $language): Language
    {
        if (!isset(self::$pool[$language])) {
            self::$pool[$language] = new self($language);
        }
        return self::$pool[$language];
    }

    public function hasRegion(): bool
    {
        if ($this->hasRegion === null) {
            $this->hasRegion = strpos($this->language, '-') !== false;
        }
        return $this->hasRegion;
    }

    public function getBaseLanguage(): Language
    {
        return self::get(substr($this->language, 0, 2));
    }

    public function __toString(): string
    {
        return $this->language;
    }
}
