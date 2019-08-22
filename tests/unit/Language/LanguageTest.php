<?php declare(strict_types=1);

namespace EventjetTest\I18n\Language;

use Eventjet\I18n\Exception\InvalidLanguageFormatException;
use Eventjet\I18n\Language\Language;
use PHPUnit\Framework\TestCase;

class LanguageTest extends TestCase
{
    public function testGetReturnsTheSameInstanceForTheSameString(): void
    {
        $this->assertSame(Language::get('de'), Language::get('de'));
    }

    /**
     * @dataProvider invalidLanguageFormats
     */
    public function testInvalidLanguageThrowsException(string $language): void
    {
        $this->expectException(InvalidLanguageFormatException::class);

        Language::get($language);
    }

    /**
     * @dataProvider validLanguageFormats
     * @param string $language
     */
    public function testValidLanguage($language): void
    {
        Language::get($language);

        $this->assertTrue(true);
    }

    public function invalidLanguageFormats(): array
    {
        return [['DE'], ['de_DE'], ['deu'], ['']];
    }

    public function validLanguageFormats(): array
    {
        return [['de'], ['en-UK']];
    }

    public function testToString(): void
    {
        $language = Language::get('de');

        $this->assertEquals('de', $language->__toString());
    }
}
