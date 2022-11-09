<?php

declare(strict_types=1);

namespace Eventjet\I18n\Translate;

use Eventjet\I18n\Language\LanguagePriority;
use LogicException;

use function sprintf;
use function str_replace;

/**
 * This implementation is intended to only be used in tests.
 *
 * It is mutable and translations can be added using {@see TestTranslator::add()}.
 *
 * When translating, it _only_ uses the primary locale.
 *
 * By default, the translator is very strict and throws an exception if no translation was added for the given message
 * ID in the given locale. You can switch to a more lenient mode by calling {@see TestTranslator::setLenient()}. In this
 * mode, the translator will return some string, the contents of which are not specified.
 */
final class TestTranslator implements TranslatorInterface
{
    /** @var array<string, array<string, string>> */
    private array $translations = [];
    private bool $strict = true;

    public function translate(string $message, LanguagePriority $languages): string
    {
        $primaryLocale = (string)$languages->primary();
        $translation = $this->translations[$message][$primaryLocale] ?? null;
        if ($translation === null) {
            if ($this->strict) {
                $template = <<<'EOF'
                    A translation of "%s" in "%s" was requested, but no translation was added for this combination. Use 
                    $translator->add('%s', '%s', 'Your translation') to add one or $translator->setLenient() to enable 
                    lenient mode and make this error go away.
                    EOF;
                throw new LogicException(
                    sprintf(
                        str_replace("\n", '', $template),
                        $message,
                        $primaryLocale,
                        $message,
                        $primaryLocale,
                    )
                );
            }
            return $message;
        }
        return $translation;
    }

    public function add(string $message, string $locale, string $translation): void
    {
        $this->translations[$message][$locale] = $translation;
    }

    public function setLenient(): void
    {
        $this->strict = false;
    }
}
