<?php

declare(strict_types=1);

namespace Eventjet\I18n\Translate;

use Eventjet\I18n\Language\Language;
use Eventjet\I18n\Language\LanguageInterface;
use Eventjet\I18n\Language\LanguagePriority;
use Eventjet\I18n\Translate\Exception\InvalidTranslationMapDataException;
use Eventjet\I18n\Translate\Factory\TranslationMapFactory;
use InvalidArgumentException;

use function array_map;
use function assert;
use function count;
use function is_array;
use function is_string;
use function trim;

/**
 * @final will be marked final with the next version
 * @psalm-immutable
 */
class TranslationMap implements TranslationMapInterface
{
    private static ?TranslationMapFactory $factory = null;
    /** @var array<string, Translation> */
    private array $translations = [];

    /**
     * @param array<array-key, Translation> $translations
     */
    public function __construct(array $translations)
    {
        if (count($translations) === 0) {
            throw new InvalidArgumentException('Empty translation maps are not allowed.');
        }
        foreach ($translations as $translation) {
            $this->translations[(string)$translation->getLanguage()] = $translation;
        }
    }

    /**
     * @param array<string, string> $mapData
     */
    public static function create(array $mapData): self
    {
        $factory = self::getFactory();
        $map = $factory->create($mapData);
        if ($map === null) {
            throw new InvalidTranslationMapDataException('Given translation map data is invalid');
        }
        assert($map instanceof self);
        return $map;
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
        $translations = array_map([self::class, 'deserializeTranslation'], $serialized);
        return new self($translations);
    }

    /**
     * @param array{language: string, text: string} $translationData
     */
    private static function deserializeTranslation(array $translationData): Translation
    {
        return Translation::deserialize($translationData);
    }

    private static function getFactory(): TranslationMapFactory
    {
        if (self::$factory !== null) {
            return self::$factory;
        }
        $factory = new TranslationMapFactory();
        self::$factory = $factory;
        return $factory;
    }

    /**
     * @psalm-pure
     */
    private static function getExtractor(): TranslationExtractor
    {
        return new TranslationExtractor();
    }

    /**
     * @return array{language: string, text: string}
     * @psalm-pure
     */
    private static function serializeTranslation(Translation $translation): array
    {
        return $translation->serialize();
    }

    /**
     * @return bool
     */
    public function has(LanguageInterface $language)
    {
        return isset($this->translations[(string)$language]);
    }

    /**
     * @return string|null
     */
    public function get(LanguageInterface $language)
    {
        if (!isset($this->translations[(string)$language])) {
            return null;
        }
        return $this->translations[(string)$language]->getText();
    }

    /**
     * @return array<string, Translation>
     */
    public function getAll()
    {
        return $this->translations;
    }

    /**
     * @return TranslationMapInterface
     */
    public function withTranslation(TranslationInterface $translation)
    {
        $newMap = clone $this;
        assert($translation instanceof Translation);
        $newMap->translations[(string)$translation->getLanguage()] = $translation;
        return $newMap;
    }

    /**
     * @return array<string, string>
     */
    public function jsonSerialize(): array
    {
        $json = [];
        foreach ($this->translations as $translation) {
            $json[(string)$translation->getLanguage()] = $translation->getText();
        }
        return $json;
    }

    /**
     * @return bool
     */
    public function equals(TranslationMapInterface $other)
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
     * @return array<string, array{language: string, text: string}>
     */
    public function serialize(): array
    {
        return array_map(self::serializeTranslation(...), $this->translations);
    }

    /**
     * Takes a callable with the following signature:
     * function (string $translation, Language $language): string
     *
     * @param pure-callable(string, Language): string $modifier
     */
    public function withEachModified(callable $modifier): self
    {
        $modified = clone $this;
        $translations = [];
        foreach ($this->translations as $key => $translation) {
            $language = $translation->getLanguage();
            assert($language instanceof Language);
            $translations[$key] = new Translation($language, $modifier($translation->getText(), $language));
        }
        $modified->translations = $translations;
        return $modified;
    }

    public function pick(LanguagePriority $languages): string
    {
        $extractor = self::getExtractor();
        return $extractor->extract($this, $languages);
    }
}
