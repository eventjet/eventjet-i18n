<?php

namespace EventjetTest\I18n;

use Eventjet\I18n\Language\Language;
use PHPUnit\Framework\TestCase;

class LanguageTest extends TestCase
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

        $this->assertTrue(true);
    }

    public function invalidLanguageFormats()
    {
        return [['DE'], ['de_DE'], ['deu'], ['']];
    }

    public function validLanguageFormats()
    {
        return [['de'], ['en-UK']];
    }

    public function testToString()
    {
        $language = Language::get('de');

        $this->assertEquals('de', $language->__toString());
    }
}
