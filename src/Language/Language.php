<?php

declare(strict_types=1);

namespace Eventjet\I18n\Language;

use Eventjet\I18n\Exception\InvalidLanguageFormatException;
use Override;

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
    /** @readonly */
    private string $language;
    /** @readonly */
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
     * @psalm-pure
     */
    public static function isValid($language)
    {
        /** @phpstan-ignore-next-line possiblyImpure.functionCall */
        return preg_match('/^([a-z]{2}(-[A-Z]{2})?)$/', $language) === 1;
    }

    /**
     * @return bool
     * @psalm-allow-private-mutation
     */
    #[Override]
    public function hasRegion()
    {
        return $this->hasRegion;
    }

    /**
     * @return Language
     */
    #[Override]
    public function getBaseLanguage()
    {
        return self::get(substr($this->language, 0, 2));
    }

    /**
     * @param string $language
     * @return Language
     * @psalm-pure
     * @psalm-suppress ImpureStaticProperty It's fine
     * @psalm-external-mutation-free
     */
    public static function get($language)
    {
        /**
         * @var array<string, self> $pool
         * @psalm-suppress ImpureStaticVariable
         * @phpstan-ignore-next-line impure.static
         */
        static $pool = [];
        /** @phpstan-ignore-next-line possiblyImpure.new */
        return $pool[$language] ??= new self($language);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->language;
    }
}
