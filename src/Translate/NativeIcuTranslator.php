<?php

declare(strict_types=1);

namespace Eventjet\I18n\Translate;

use Eventjet\I18n\Language\LanguagePriority;
use MessageFormatter;
use Throwable;

use function assert;
use function trim;

final class NativeIcuTranslator implements IcuTranslatorInterface
{
    /** @var array<string, MessageFormatter> */
    private static array $formatters = [];
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    private static function formatter(string $locale, string $pattern): MessageFormatter
    {
        $formatterKey = $locale . '-' . $pattern;
        $formatter = self::$formatters[$formatterKey] ?? null;
        if ($formatter !== null) {
            return $formatter;
        }
        $formatter = new MessageFormatter($locale, $pattern);
        self::$formatters[$formatterKey] = $formatter;
        return $formatter;
    }

    public function translate(string $messageId, LanguagePriority $languages, array $arguments): ?string
    {
        try {
            $pattern = $this->translator->translate($messageId, $languages);
            $message = self::formatter((string)$languages->primary(), trim($pattern))->format($arguments);
            // I couldn't find out when it returns false.
            assert($message !== false);
            return $message;
        } catch (Throwable $error) {
            return null;
        }
    }
}
