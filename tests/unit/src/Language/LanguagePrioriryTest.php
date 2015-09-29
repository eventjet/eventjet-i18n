<?php

namespace EventjetTest\I18n;

use Eventjet\I18n\Language\Language;
use Eventjet\I18n\Language\LanguagePrioriry;
use PHPUnit_Framework_TestCase;

/**
 * Class LanguagePrioriryTest
 *
 * @package EventjetTest\I18n
 */
class LanguagePrioriryTest extends PHPUnit_Framework_TestCase
{
    public function testGetAllReturnsAllLanguages()
    {
        $languages = [Language::get('de'), Language::get('en')];
        $prioriry = new LanguagePrioriry($languages);

        $all = $prioriry->getAll();

        $this->assertCount(2, $all);
        $this->assertEquals($all[0], $languages[0]);
        $this->assertEquals($all[1], $languages[1]);
    }
}
