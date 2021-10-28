<?php

declare(strict_types=1);

namespace Eventjet\I18n\Translate;

use Eventjet\I18n\Language\LanguagePriorityInterface;
use LogicException;

/**
 * This implementation is intended to only be used in tests.
 *
 * It is mutable and translations can be added using {@see TestTranslator::add()}.
 *
 * When translating, it _only_ uses the primary locale.
 */
final class TestTranslator implements TranslatorInterface
{
    /** @var array<string, array<string, string>> */
    private array $translations = [];

    public function translate(string $message, LanguagePriorityInterface $languages): string
    {
        $message = $this->translations[$message][(string)$languages->primary()] ?? null;
        if ($message === null) {
            throw new LogicException('There is no translation for message ID "%s" in locale "%s" mapped!');
        }
        return $message;
    }

    public function add(string $message, string $locale, string $translation): void
    {
        $this->translations[$message][$locale] = $translation;
    }
}
