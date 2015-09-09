<?php

namespace Eventjet\I18n;

use Eventjet\I18n\Exception\InvalidLanguageFormatException;
use Eventjet\I18n\Validation\LanguageFormatValidator;

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
            if (!(LanguageFormatValidator::isValid($key))) {
                throw new InvalidLanguageFormatException(sprintf('Invalid language key "%s."', $key));
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
