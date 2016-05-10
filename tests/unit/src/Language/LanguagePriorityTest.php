<?php

namespace EventjetTest\I18n;

use Eventjet\I18n\Language\Language;
use Eventjet\I18n\Language\LanguagePriority;
use InvalidArgumentException;
use PHPUnit_Framework_TestCase;

/**
 * Class LanguagePriorityTest
 *
 * @package EventjetTest\I18n
 */
class LanguagePriorityTest extends PHPUnit_Framework_TestCase
{
    public function testGetAllReturnsAllLanguages()
    {
        $languages = [Language::get('de'), Language::get('en')];
        $priority = new LanguagePriority($languages);

        $all = $priority->getAll();

        $this->assertCount(2, $all);
        $this->assertEquals($all[0], $languages[0]);
        $this->assertEquals($all[1], $languages[1]);
    }

    public function testNoLanguages()
    {
        $this->expectException(InvalidArgumentException::class);
        new LanguagePriority([]);
    }

    public function testPrimary()
    {
        $priority = new LanguagePriority([
            Language::get('de-AT'),
            Language::get('en-US'),
        ]);

        $this->assertSame(Language::get('de-AT'), $priority->primary());
    }
}
