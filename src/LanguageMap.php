<?php

namespace Eventjet\I18n;

class LanguageMap implements LanguageMapInterface
{
    /**
     * @var string[]
     */
    private $map;

    /**
     * @param string[] $map
     */
    public function __construct(array $map)
    {
        $this->map = $map;
    }
}
