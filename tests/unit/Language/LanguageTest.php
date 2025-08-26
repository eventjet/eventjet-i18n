<?php

declare(strict_types=1);

namespace EventjetTest\I18n\Language;

use Eventjet\I18n\Exception\InvalidLanguageFormatException;
use Eventjet\I18n\Language\Language;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class LanguageTest extends TestCase
{
    public function testGetReturnsTheSameInstanceForTheSameString(): void
    {
        self::assertSame(Language::get('de'), Language::get('de'));
    }

    #[DataProvider('invalidLanguageFormats')]
    public function testInvalidLanguageThrowsException(string $language): void
    {
        $this->expectException(InvalidLanguageFormatException::class);

        $_language = Language::get($language);
    }

    #[DataProvider('validLanguageFormats')]
    public function testValidLanguage(string $language): void
    {
        $this->expectNotToPerformAssertions();

        $_language = Language::get($language);
    }

    #[DataProvider('validLanguageFormats')]
    public function testIsValid(string $language): void
    {
        self::assertTrue(Language::isValid($language));
    }

    #[DataProvider('invalidLanguageFormats')]
    public function testIsInvalid(string $language): void
    {
        self::assertFalse(Language::isValid($language));
    }

    /**
     * @return array<array<string>>
     */
    public static function invalidLanguageFormats(): array
    {
        return [['DE'], ['de_DE'], ['deu'], ['']];
    }

    /**
     * @return array<array<string>>
     */
    public static function validLanguageFormats(): array
    {
        return [['de'], ['en-UK']];
    }

    public function testToString(): void
    {
        $language = Language::get('de');

        self::assertEquals('de', $language->__toString());
    }
}
