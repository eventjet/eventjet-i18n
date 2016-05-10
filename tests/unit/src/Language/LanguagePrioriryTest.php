<?php

namespace EventjetTest\I18n;

use Eventjet\I18n\Language\Language;
use Eventjet\I18n\Language\LanguagePrioriry;
use Eventjet\I18n\Language\LanguagePriority;
use PHPUnit_Framework_TestCase;

class LanguagePrioriryTest extends PHPUnit_Framework_TestCase
{
    public function testExtendsLanguagePriority()
    {
        /** @noinspection PhpDeprecationInspection */
        $prioriry = new LanguagePrioriry([Language::get('de')]);

        $this->assertInstanceOf(LanguagePriority::class, $prioriry);
    }
}
