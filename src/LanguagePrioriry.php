<?php

namespace Eventjet\I18n;

class LanguagePrioriry implements LanguagePriorityInterface
{
    /**
     * @var Language[]
     */
    private $languages;

    /**
     * LanguagePrioriry constructor.
     *
     * @param Language[] $languages
     */
    public function __construct(array $languages)
    {
        $this->languages = $languages;
    }

    /**
     * @return Language[]
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
