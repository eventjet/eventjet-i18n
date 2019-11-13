<?php declare(strict_types=1);

namespace Eventjet\I18n\Translate;

use Eventjet\I18n\Language\LanguageInterface;
use InvalidArgumentException;
use function array_map;

class TranslationMap implements TranslationMapInterface
{
    /**
     * @var TranslationInterface[]
     */
    private $translations = [];

    /**
     * @param TranslationInterface[] $translations
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
     * @param mixed[] $serialized
     * @return TranslationMap
     */
    public static function deserialize(array $serialized): self
    {
        $translations = array_map(static function (array $translationData): Translation {
            return Translation::deserialize($translationData);
        }, $serialized);
        return new self($translations);
    }

    /**
     * @param LanguageInterface $language
     * @return bool
     */
    public function has(LanguageInterface $language)
    {
        return isset($this->translations[(string)$language]);
    }

    /**
     * @param LanguageInterface $language
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
     * @return TranslationInterface[]
     */
    public function getAll()
    {
        return $this->translations;
    }

    /**
     * @param TranslationInterface $translation
     * @return TranslationMapInterface
     */
    public function withTranslation(TranslationInterface $translation)
    {
        $newMap = clone $this;
        $newMap->translations[(string)$translation->getLanguage()] = $translation;
        return $newMap;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        $json = [];
        foreach ($this->translations as $translation) {
            $json[(string)$translation->getLanguage()] = $translation->getText();
        }
        return $json;
    }

    /**
     * @param TranslationMapInterface $other
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
     * @return mixed[]
     */
    public function serialize(): array
    {
        return array_map(static function (Translation $translation) {
            return $translation->serialize();
        }, $this->translations);
    }

    public function withEachModified(callable $modifier): self
    {
        $modified = clone $this;
        $modified->translations = array_map(
            static function (Translation $translation) use ($modifier): Translation {
                return new Translation(
                    $translation->getLanguage(),
                    $modifier($translation->getText(), $translation->getLanguage())
                );
            },
            $this->translations
        );
        return $modified;
    }
}
