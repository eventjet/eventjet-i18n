<?php

namespace EventjetTest\I18n;

use Eventjet\I18n\Language\Language;
use PHPUnit_Framework_TestCase;

class LanguageTest extends PHPUnit_Framework_TestCase
{
    public function testGetReturnsTheSameInstanceForTheSameString()
    {
        $this->assertSame(Language::get('de'), Language::get('de'));
    }

    /**
     * @expectedException \Eventjet\I18n\Exception\InvalidLanguageFormatException
     * @dataProvider invalidLanguageFormats
     * @param string $language
     */
    public function testInvalidLanguageThrowsException($language)
    {
        Language::get($language);
    }

    /**
     * @dataProvider validLanguageFormats
     * @param string $language
     */
    public function testValidLanguage($language)
    {
        Language::get($language);
    }

    public function invalidLanguageFormats()
    {
        return [['DE'], ['de_DE'], ['deu'], ['']];
    }

    public function validLanguageFormats()
    {
        return [['de'], ['en-UK']];
    }
}
