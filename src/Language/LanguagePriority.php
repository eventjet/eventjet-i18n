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
 * @psalm-immutable
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

    /**
     * @psalm-pure
     */
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

    /**
     * @psalm-mutation-free
     * @psalm-allow-private-mutation
     */
    public function current(): Language
    {
        $current = current($this->languages);
        if ($current === false) {
            throw new RuntimeException('Pointer is beyond the end of the elements');
        }
        return $current;
    }

    /**
     * @psalm-external-mutation-free
     */
    public function next(): void
    {
        /** @psalm-suppress InaccessibleProperty I don't know why external-mutation-free isn't working */
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

    /**
     * @psalm-external-mutation-free
     */
    public function rewind(): void
    {
        /** @psalm-suppress InaccessibleProperty I don't know why external-mutation-free isn't working */
        reset($this->languages);
    }

    public function primary(): Language
    {
        return $this->languages[0];
    }
}
