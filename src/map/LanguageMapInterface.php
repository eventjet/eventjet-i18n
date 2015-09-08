<?php

namespace Eventjet\I18n;

interface LanguageMapInterface
{
    /**
     * @param string $locale
     * @return string
     */
    public function get($locale);

    /**
     * @return string[]
     */
    public function getAll();
}
