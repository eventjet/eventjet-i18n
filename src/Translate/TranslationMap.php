<?php

declare(strict_types=1);

namespace Eventjet\I18n\Translate;

use Eventjet\I18n\Language\Language;
use Eventjet\I18n\Language\LanguagePriority;
use Eventjet\I18n\Translate\Exception\InvalidTranslationMapDataException;
use InvalidArgumentException;
use JsonSerializable;

use function array_filter;
use function array_map;
use function assert;
use function count;
use function is_array;
use function is_string;
use function reset;
use function sprintf;
use function trim;

/**
 * @psalm-immutable
 */
final class TranslationMap implements JsonSerializable
{
    /** @var non-empty-array<string, Translation> */
    private array $translations;

    /**
     * @param array<array-key, Translation> $translations
     */
    public function __construct(array $translations)
    {
        if (count($translations) === 0) {
            throw new InvalidArgumentException('Empty translation maps are not allowed.');
        }
        $translationMap = [];
        foreach ($translations as $translation) {
            $translationMap[(string)$translation->getLanguage()] = $translation;
        }
        $this->translations = $translationMap;
    }

    /**
     * @param array<string, string> $mapData
     */
    public static function create(array $mapData): self
    {
        $mapData = array_map('trim', $mapData);
        $mapData = array_filter($mapData, static fn(string $text) => $text !== '');
        if (count($mapData) === 0) {
            throw new InvalidTranslationMapDataException('Given translation map data is invalid');
        }
        $translations = [];
        foreach ($mapData as $lang => $text) {
            if (!Language::isValid($lang)) {
                throw new InvalidTranslationMapDataException(sprintf('Invalid language "%s".', $lang));
            }
            $translations[] = new Translation(Language::get($lang), $text);
        }
        return new TranslationMap($translations);
    }

    /**
     * Checks whether the given value can be used as an argument for {@see self::create()}.
     *
     * @psalm-assert-if-true array<string, string> $mapData
     */
    public static function canCreate(mixed $mapData): bool
    {
        if (!is_array($mapData)) {
            return false;
        }
        $filtered = [];
        /**
         * @var mixed $lang
         * @var mixed $text
         */
        foreach ($mapData as $lang => $text) {
            if (!is_string($lang) || !is_string($text) || !Language::isValid($lang)) {
                return false;
            }
            if (trim($text) === '') {
                continue;
            }
            $filtered[$lang] = $text;
        }
        return count($filtered) !== 0;
    }

    /**
     * @param array<array-key, array{language: string, text: string}> $serialized
     */
    public static function deserialize(array $serialized): self
    {
        $translations = array_map(self::deserializeTranslation(...), $serialized);
        return new self($translations);
    }

    /**
     * @param array{language: string, text: string} $translationData
     */
    private static function deserializeTranslation(array $translationData): Translation
    {
        return Translation::deserialize($translationData);
    }

    /**
     * @return array{language: string, text: string}
     * @psalm-pure
     */
    private static function serializeTranslation(Translation $translation): array
    {
        return $translation->serialize();
    }

    public function has(Language $language): bool
    {
        return isset($this->translations[(string)$language]);
    }

    public function get(Language $language): ?string
    {
        if (!isset($this->translations[(string)$language])) {
            return null;
        }
        return $this->translations[(string)$language]->getText();
    }

    /**
     * @return non-empty-array<string, Translation>
     */
    public function getAll(): array
    {
        return $this->translations;
    }

    public function withTranslation(Translation $translation): self
    {
        $newMap = clone $this;
        $newMap->translations[(string)$translation->getLanguage()] = $translation;
        return $newMap;
    }

    /**
     * @return non-empty-array<string, string>
     */
    public function jsonSerialize(): array
    {
        $json = [];
        foreach ($this->translations as $translation) {
            $json[(string)$translation->getLanguage()] = $translation->getText();
        }
        return $json;
    }

    public function equals(self $other): bool
    {
        if ($this === $other) {
            return true;
        }
        $otherData = $other->getAll();
        if (count($this->translations) !== count($otherData)) {
            return false;
        }
        foreach ($this->translations as $translation) {
            if (!$other->has($translation->getLanguage())) {
                return false;
            }
            if ($other->get($translation->getLanguage()) !== $translation->getText()) {
                return false;
            }
        }
        return true;
    }

    /**
     * @return non-empty-array<string, array{language: string, text: string}>
     */
    public function serialize(): array
    {
        return array_map(self::serializeTranslation(...), $this->translations);
    }

    /**
     * Takes a callable with the following signature:
     * function (string $translation, Language $language): string
     *
     * @param pure-callable(string, \Eventjet\I18n\Language\Language): string $modifier
     */
    public function withEachModified(callable $modifier): self
    {
        $modified = clone $this;
        $translations = [];
        foreach ($this->translations as $key => $translation) {
            $language = $translation->getLanguage();
            $translations[$key] = new Translation($language, $modifier($translation->getText(), $language));
        }
        $modified->translations = $translations;
        return $modified;
    }

    public function pick(LanguagePriority $languages): string
    {
        $string = $this->pickFromPriority($languages);
        return $string ?? $this->pickFromFallbacks();
    }

    private function pickFromPriority(LanguagePriority $priorities): ?string
    {
        foreach ($priorities as $language) {
            if ($this->has($language)) {
                return $this->get($language);
            }
            if (!$language->hasRegion()) {
                continue;
            }
            $baseLanguage = $language->getBaseLanguage();
            if ($this->has($baseLanguage)) {
                return $this->get($baseLanguage);
            }
        }
        return null;
    }

    private function pickFromFallbacks(): string
    {
        $english = Language::get('en');
        if ($this->has($english)) {
            $return = $this->get($english);
            assert($return !== null);
            return $return;
        }
        $translations = $this->getAll();
        $translation = reset($translations);
        $return = $this->get($translation->getLanguage());
        assert($return !== null);
        return $return;
    }
}
