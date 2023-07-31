<?php

declare(strict_types=1);

namespace Eventjet\I18n\Language;

use InvalidArgumentException;
use Iterator;
use RuntimeException;

use function assert;
use function count;
use function current;
use function key;
use function next;
use function reset;

/**
 * @implements Iterator<Language>
 */
final class LanguagePriority implements Iterator
{
    /** @var list<Language> */
    private array $languages;

    /**
     * @param list<Language> $languages
     */
    public function __construct(array $languages)
    {
        if (count($languages) === 0) {
            throw new InvalidArgumentException('Language priorities need at least one language.');
        }
        $this->languages = $languages;
    }

    public static function fromLocale(string $locale): self
    {
        return new self([Language::get($locale)]);
    }

    /**
     * @return list<Language>
     */
    public function getAll(): array
    {
        return $this->languages;
    }

    public function current(): Language
    {
        $current = current($this->languages);
        if ($current === false) {
            throw new RuntimeException('Pointer is beyond the end of the elements');
        }
        return $current;
    }

    public function next(): void
    {
        next($this->languages);
    }

    public function key(): int
    {
        $key = key($this->languages);
        assert($key !== null);
        return $key;
    }

    public function valid(): bool
    {
        $key = key($this->languages);
        return ($key !== null);
    }

    public function rewind(): void
    {
        reset($this->languages);
    }

    public function primary(): Language
    {
        /** @var Language $primary */
        $primary = reset($this->languages);
        return $primary;
    }
}
