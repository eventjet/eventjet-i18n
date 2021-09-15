<?php

declare(strict_types=1);

namespace Eventjet\I18n\Language;

use Iterator;

/**
 * @extends Iterator<LanguageInterface>
 * @deprecated Use LanguagePriority directly
 */
interface LanguagePriorityInterface extends Iterator
{
    /**
     * @return array<LanguageInterface>
     */
    public function getAll();

    /**
     * @return LanguageInterface
     */
    public function primary();
}
