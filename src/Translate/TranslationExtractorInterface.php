<?php

declare(strict_types=1);

namespace Eventjet\I18n\Translate;

use Eventjet\I18n\Language\LanguageInterface;
use Eventjet\I18n\Language\LanguagePriorityInterface;

interface TranslationExtractorInterface
{
    /**
     * @param TranslationMapInterface                       $map
     * @param LanguagePriorityInterface|LanguageInterface[] $priorities
     * @return string
     */
    public function extract(TranslationMapInterface $map, LanguagePriorityInterface $priorities);
}
