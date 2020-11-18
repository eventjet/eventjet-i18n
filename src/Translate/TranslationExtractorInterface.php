<?php

declare(strict_types=1);

namespace Eventjet\I18n\Translate;

use Eventjet\I18n\Language\LanguagePriorityInterface;

/**
 * @deprecated use {@see TranslationMap::pick} instead
 */
interface TranslationExtractorInterface
{
    /**
     * @return string
     */
    public function extract(TranslationMapInterface $map, LanguagePriorityInterface $priorities);
}
