<?php

namespace EventjetTest\I18n;

use Eventjet\I18n\Language\Language;
use Eventjet\I18n\Language\LanguagePrioriry;
use Eventjet\I18n\Language\LanguagePriority;
use PHPUnit\Framework\TestCase;

class LanguagePrioriryTest extends TestCase
{
    public function testExtendsLanguagePriority()
    {
        /** @noinspection PhpDeprecationInspection */
        $prioriry = new LanguagePrioriry([Language::get('de')]);

        $this->assertInstanceOf(LanguagePriority::class, $prioriry);
    }
}
