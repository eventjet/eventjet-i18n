<?php

namespace Eventjet\I18n;

use SplObjectStorage;

interface TranslationMapInterface
{
    /**
     * @param Language $language
     * @return bool
     */
    public function has(Language $language);

    /**
     * @param Language $language
     * @return string
     */
    public function get(Language $language);

    /**
     * @return Translation[]|SplObjectStorage
     */
    public function getAll();
}
