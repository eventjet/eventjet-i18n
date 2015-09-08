<?php

namespace Eventjet\I18n;

class LanguageMap implements LanguageMapInterface
{
    /**
     * @var array
     */
    private $map;

    /**
     * @var array
     */
    private $priorities = ['de-DE', 'de', 'en-US', 'en'];

    /**
     * @param null|string[] $map
     */
    public function __construct(array $map = null)
    {
        if (null === $map) {
            $map = [];
        }
        $this->map = $map;
    }

    /**
     * Returns the string for the specified locale or the best fallback
     *
     * @param string $locale
     * @return string|null
     */
    public function get($locale)
    {
        if (!isset($this->map[$locale])) {
            return $this->priorityGet();
        }
        return $this->map[$locale];
    }

    /**
     * @return string[]
     */
    public function getAll()
    {
        return $this->map;
    }

    /**
     * @return null|string
     */
    private function priorityGet()
    {
        foreach ($this->priorities as $locale) {
            $fallback = $this->get($locale);
            if ($fallback) {
                return $fallback;
            }
        }
        return null;
    }


}
