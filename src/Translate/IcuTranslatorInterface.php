<?php

declare(strict_types=1);

namespace Eventjet\I18n\Translate;

use Eventjet\I18n\Language\LanguagePriority;

interface IcuTranslatorInterface
{
    /**
     * @param array<string, mixed> $arguments
     */
    public function translate(string $messageId, LanguagePriority $languages, array $arguments): ?string;
}
