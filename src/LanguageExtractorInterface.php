<?php

namespace Eventjet\I18n;

interface LanguageExtractorInterface
{
    /**
     * @param LanguageMapInterface      $map
     * @param LanguagePriorityInterface $priorities
     * @return string
     */
    public function extract(LanguageMapInterface $map, LanguagePriorityInterface $priorities);
}
