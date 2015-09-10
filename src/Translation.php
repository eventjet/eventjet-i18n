<?php

namespace Eventjet\I18n;

use InvalidArgumentException;

class Translation implements TranslationInterface
{
    /** @var Language */
    private $language;
    /** @var string */
    private $text;

    /**
     * Translation constructor.
     *
     * @param Language $language
     * @param string   $string
     */
    public function __construct(Language $language, $string)
    {
        if (!is_string($string)) {
            throw new InvalidArgumentException(sprintf(
                'The constructor of %s expected a string as its second argument, but got %s.',
                __CLASS__,
                gettype($string)
            ));
        }
        $this->language = $language;
        $this->text = $string;
    }

    /**
     * @return Language
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }
}
