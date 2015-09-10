<?php

namespace Eventjet\I18n;

interface TranslationExtractorInterface
{
    /**
     * @param TranslationMapInterface              $map
     * @param LanguagePriorityInterface|Language[] $priorities
     * @return string
     */
    public function extract(TranslationMapInterface $map, LanguagePriorityInterface $priorities);
}
