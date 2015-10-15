<?php

namespace EventjetTest\I18n;

use Eventjet\I18n\Language\Language;
use Eventjet\I18n\Translate\Translation;
use Eventjet\I18n\Translate\TranslationMap;
use PHPUnit_Framework_TestCase;

class TranslationMapTest extends PHPUnit_Framework_TestCase
{
    public function testHasReturnsFalseIfTranslationDoesNotExist()
    {
        $map = new TranslationMap([new Translation(Language::get('de'), 'Test')]);

        $this->assertFalse($map->has(Language::get('en')));
    }

    public function testWithTranslation()
    {
        $map = new TranslationMap([new Translation(Language::get('de'), 'Deutsch')]);

        $en = Language::get('en');
        $english = 'English';
        $newMap = $map->withTranslation(new Translation($en, $english));

        $this->assertEquals($english, $newMap->get($en));
        $this->assertFalse($map->has($en));
    }

    public function testWithTranslationOverridesExistingTranslation()
    {
        $de = Language::get('de');
        $map = new TranslationMap([new Translation($de, 'Original')]);

        $newMap = $map->withTranslation(new Translation($de, 'Overridden'));

        $this->assertEquals('Overridden', $newMap->get($de));
        $this->assertEquals('Original', $map->get($de));
    }
}
