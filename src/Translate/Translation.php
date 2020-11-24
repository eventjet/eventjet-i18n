<?php

declare(strict_types=1);

namespace Eventjet\I18n\Translate;

use Eventjet\I18n\Language\Language;

final class Translation
{
    private const LANGUAGE = 'language';
    private const TEXT = 'text';
    private Language $language;
    private string $text;

    public function __construct(Language $language, string $string)
    {
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

    public function getLanguage(): Language
    {
        return $this->language;
    }

    public function getText(): string
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
