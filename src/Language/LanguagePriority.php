<?php

declare(strict_types=1);

namespace Eventjet\I18n\Language;

use InvalidArgumentException;
use Override;
use RuntimeException;

use function assert;
use function count;
use function current;
use function key;
use function next;
use function reset;

/**
 * @final will be marked final with the next version
 * @psalm-immutable
 */
class LanguagePriority implements LanguagePriorityInterface
{
    /** @var list<LanguageInterface> */
    private array $languages;

    /**
     * @param list<LanguageInterface> $languages
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
        /** @phpstan-ignore-next-line possiblyImpure.new */
        return new self([Language::get($locale)]);
    }

    /**
     * @return list<LanguageInterface>
     */
    #[Override]
    public function getAll(): array
    {
        return $this->languages;
    }

    /**
     * @psalm-mutation-free
     * @psalm-allow-private-mutation
     */
    #[Override]
    public function current(): LanguageInterface
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
    #[Override]
    public function next(): void
    {
        /**
         * I don't know why external-mutation-free isn't working
         * @psalm-suppress InaccessibleProperty
         * @phpstan-ignore-next-line argument.byRef
         */
        next($this->languages);
    }

    #[Override]
    public function key(): int
    {
        $key = key($this->languages);
        assert($key !== null);
        return $key;
    }

    #[Override]
    public function valid(): bool
    {
        $key = key($this->languages);
        return ($key !== null);
    }

    /**
     * @psalm-external-mutation-free
     */
    #[Override]
    public function rewind(): void
    {
        /**
         * I don't know why external-mutation-free isn't working
         * @psalm-suppress InaccessibleProperty
         * @phpstan-ignore-next-line argument.byRef
         */
        reset($this->languages);
    }

    /**
     * @return LanguageInterface
     */
    #[Override]
    public function primary()
    {
        return $this->languages[0];
    }
}
