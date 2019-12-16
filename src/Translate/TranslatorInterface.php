<?php

declare(strict_types=1);

namespace Eventjet\I18n\Translate;

use Eventjet\I18n\Language\LanguagePriorityInterface;

interface TranslatorInterface
{
    public function translate(string $message, LanguagePriorityInterface $languages): string;
}
