<?php

namespace EventjetTest\I18n;

use Eventjet\I18n\Language\Language;
use Eventjet\I18n\Translate\Translation;
use PHPUnit\Framework\TestCase;

class TranslationTest extends TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testExceptionIsThrownIfTextIsNotAString()
    {
        new Translation(Language::get('de'), true);
    }

    public function testGetLanguage()
    {
        $language = Language::get('de');
        $translation = new Translation($language, 'Test');

        $returnedLanguage = $translation->getLanguage();

        $this->assertSame($language, $returnedLanguage);
    }

    public function testGetText()
    {
        $text = 'Test';
        $translation = new Translation(Language::get('de'), $text);

        $returnedText = $translation->getText();

        $this->assertEquals($text, $returnedText);
    }
}
