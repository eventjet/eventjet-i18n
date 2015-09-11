<?php

namespace Eventjet\I18n\Language;

class LanguagePrioriry implements LanguagePriorityInterface
{
    /**
     * @var LanguageInterface[]
     */
    private $languages;

    /**
     * LanguagePrioriry constructor.
     *
     * @param LanguageInterface[] $languages
     */
    public function __construct(array $languages)
    {
        $this->languages = $languages;
    }

    /**
     * @return LanguageInterface[]
     */
    public function getAll()
    {
        return $this->languages;
    }

    public function current()
    {
        return current($this->languages);
    }

    public function next()
    {
        return next($this->languages);
    }

    public function key()
    {
        return key($this->languages);
    }

    public function valid()
    {
        $key = key($this->languages);
        return ($key !== null && $key !== false);

    }

    public function rewind()
    {
        reset($this->languages);
    }
}
