<?php

declare(strict_types=1);

namespace Eventjet\I18n\Translate;

use Eventjet\I18n\Language\Language;
use Eventjet\I18n\Language\LanguageInterface;
use InvalidArgumentException;

use function gettype;
use function is_string;
use function sprintf;

class Translation implements TranslationInterface
{
    private const LANGUAGE = 'language';
    private const TEXT = 'text';
    private LanguageInterface $language;
    private string $text;

    /**
     * @param LanguageInterface $language
     * @param string $string
     */
    public function __construct(LanguageInterface $language, $string)
    {
        if (!is_string($string)) {
            throw new InvalidArgumentException(
                sprintf(
                    'The constructor of %s expected a string as its second argument, but got %s.',
                    __CLASS__,
                    gettype($string)
                )
            );
        }
        $this->language = $language;
        $this->text = $string;
    }

    /**
     * @param array<string, string> $serialized
     */
    public static function deserialize(array $serialized): self
    {
        return new self(Language::get($serialized[self::LANGUAGE]), $serialized[self::TEXT]);
    }

    /**
     * @return LanguageInterface
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @return mixed[]
     */
    public function serialize(): array
    {
        return [
            self::LANGUAGE => (string)$this->language,
            self::TEXT => $this->text,
        ];
    }
}
