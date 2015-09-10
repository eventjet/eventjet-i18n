<?php

namespace EventjetTest\I18n;

use Eventjet\I18n\Language;
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
     */
    public function testInvalidLanguageThrowsException($language)
    {
        Language::get($language);
    }

    /**
     * @dataProvider validLanguageFormats
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
