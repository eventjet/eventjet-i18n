<?php

namespace Eventjet\I18n;

class LanguagePrioriry implements LanguagePriorityInterface
{
    /**
     * @var string[]
     */
    private $languages;

    /**
     * LanguagePrioriry constructor.
     *
     * @param string[] $languages
     */
    public function __construct(array $languages)
    {
        $this->languages = $languages;
    }

    /**
     * @return string[]
     */
    public function getAll()
    {
        return $this->languages;
    }
}
