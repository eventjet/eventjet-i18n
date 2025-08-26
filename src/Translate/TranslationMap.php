<?php

declare(strict_types=1);

namespace Eventjet\I18n\Translate;

use Eventjet\I18n\Language\Language;
use Eventjet\I18n\Language\LanguageInterface;
use Eventjet\I18n\Language\LanguagePriority;
use Eventjet\I18n\Translate\Exception\InvalidTranslationMapDataException;
use Eventjet\I18n\Translate\Factory\TranslationMapFactory;
use InvalidArgumentException;
use Override;

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
    /** @var array<string, Translation> */
    private array $translations;

    /**
     * @param array<array-key, Translation> $translations
     */
    public function __construct(array $translations)
    {
        if (count($translations) === 0) {
            throw new InvalidArgumentException('Empty translation maps are not allowed.');
        }
        $keyed = [];
        foreach ($translations as $translation) {
            $keyed[(string)$translation->getLanguage()] = $translation;
        }
        $this->translations = $keyed;
    }

    /**
     * @param array<string, string> $mapData
     */
    public static function create(array $mapData): self
    {
        $map = (new TranslationMapFactory())->create($mapData);
        if ($map === null) {
            throw new InvalidTranslationMapDataException('Given translation map data is invalid');
        }
        assert($map instanceof self);
        return $map;
    }

    /**
     * Checks whether the given value can be used as an argument for {@see self::create()}.
     *
     * @psalm-assert-if-true array<literal-string, string> $mapData
     */
    public static function canCreate(mixed $mapData): bool
    {
        if (!is_array($mapData)) {
            return false;
        }
        $filtered = [];
        /**
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
        /** @phpstan-ignore-next-line possiblyImpure.methodCall */
        return $translation->serialize();
    }

    /**
     * @return bool
     */
    #[Override]
    public function has(LanguageInterface $language)
    {
        return isset($this->translations[(string)$language]);
    }

    /**
     * @return string|null
     */
    #[Override]
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
    #[Override]
    public function getAll()
    {
        return $this->translations;
    }

    /**
     * @return TranslationMapInterface
     */
    #[Override]
    public function withTranslation(TranslationInterface $translation)
    {
        assert($translation instanceof Translation);
        $translations = $this->translations;
        $translations[(string)$translation->getLanguage()] = $translation;
        return new self($translations);
    }

    /**
     * @return array<string, string>
     */
    #[Override]
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
    #[Override]
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
        $translations = [];
        foreach ($this->translations as $key => $translation) {
            $language = $translation->getLanguage();
            assert($language instanceof Language);
            $translations[$key] = new Translation($language, $modifier($translation->getText(), $language));
        }
        return new self($translations);
    }

    public function pick(LanguagePriority $languages): string
    {
        $extractor = self::getExtractor();
        return $extractor->extract($this, $languages);
    }
}
