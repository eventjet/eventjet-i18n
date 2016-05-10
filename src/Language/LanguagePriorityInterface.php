<?php

namespace Eventjet\I18n\Language;

use Iterator;

interface LanguagePriorityInterface extends Iterator
{
    /**
     * @return LanguageInterface[]
     */
    public function getAll();

    /**
     * @return LanguageInterface
     */
    public function primary();
}
