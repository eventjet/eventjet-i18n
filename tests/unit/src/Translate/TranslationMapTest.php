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
}
