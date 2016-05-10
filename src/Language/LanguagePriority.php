<?php

namespace Eventjet\I18n\Language;

use InvalidArgumentException;

class LanguagePriority implements LanguagePriorityInterface
{
    /**
     * @var LanguageInterface[]
     */
    private $languages;

    /**
     * LanguagePriority constructor.
     *
     * @param LanguageInterface[] $languages
     */
    public function __construct(array $languages)
    {
        if (count($languages) === 0) {
            throw new InvalidArgumentException('Language priorities need at least one language.');
        }
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

    /**
     * @return LanguageInterface
     */
    public function primary()
    {
        return reset($this->languages);
    }
}
