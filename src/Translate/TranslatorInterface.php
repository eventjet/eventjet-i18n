<?php

declare(strict_types=1);

namespace Eventjet\I18n\Translate;

use Eventjet\I18n\Language\LanguagePriority;

interface TranslatorInterface
{
    public function translate(string $message, LanguagePriority $languages): string;
}
