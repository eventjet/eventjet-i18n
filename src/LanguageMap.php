<?php

namespace Eventjet\I18n;

use Eventjet\I18n\Exception\InvalidLanguageFormatException;

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
        foreach ($map as $key => $value) {
            $valid = LanguageFormatValidator::isValid($key);
            if (!$valid) {
                throw new InvalidLanguageFormatException(LanguageFormatValidator::getError());
            }
        }
        $this->map = $map;
    }

    /**
     * @return string[]
     */
    public function getAll()
    {
        return $this->map;
    }
}
