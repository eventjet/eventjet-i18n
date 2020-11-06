<?php

declare(strict_types=1);

namespace Eventjet\I18n\Language;

use InvalidArgumentException;

use function assert;
use function count;
use function current;
use function key;
use function next;
use function reset;

class LanguagePriority implements LanguagePriorityInterface
{
    /** @var array<int, LanguageInterface> */
    private array $languages;

    /**
     * @param array<int, LanguageInterface> $languages
     */
    public function __construct(array $languages)
    {
        if (count($languages) === 0) {
            throw new InvalidArgumentException('Language priorities need at least one language.');
        }
        $this->languages = $languages;
    }

    /**
     * @return LanguageInterface[]
     */
    public function getAll()
    {
        return $this->languages;
    }

    public function current(): LanguageInterface
    {
        return current($this->languages);
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

    /**
     * @return LanguageInterface
     */
    public function primary()
    {
        $primary = reset($this->languages);
        assert($primary !== false);
        return $primary;
    }
}
