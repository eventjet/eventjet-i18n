<?php

namespace Eventjet\I18n;

use Iterator;

interface LanguagePriorityInterface extends Iterator
{
    /**
     * @return Language[]
     */
    public function getAll();
}
