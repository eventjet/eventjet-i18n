<?php

declare(strict_types=1);

namespace Eventjet\I18n\Translate;

use Eventjet\I18n\Language\Language;
use Eventjet\I18n\Language\LanguageInterface;
use InvalidArgumentException;
use Override;

use function gettype;
use function is_string;
use function sprintf;

/**
 * @final will be marked final with the next version
 * @psalm-immutable
 */
class Translation implements TranslationInterface
{
    private const LANGUAGE = 'language';
    private const TEXT = 'text';
    private LanguageInterface $language;
    private string $text;

    public function __construct(LanguageInterface $language, mixed $string)
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
     * @param array{language: string, text: string} $serialized
     */
    public static function deserialize(array $serialized): self
    {
        return new self(Language::get($serialized[self::LANGUAGE]), $serialized[self::TEXT]);
    }

    /**
     * @return LanguageInterface
     */
    #[Override]
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @return string
     */
    #[Override]
    public function getText()
    {
        return $this->text;
    }

    /**
     * @return array{language: string, text: string}
     */
    public function serialize(): array
    {
        return [
            self::LANGUAGE => (string)$this->language,
            self::TEXT => $this->text,
        ];
    }
}
