<?php

declare(strict_types=1);

namespace EventjetTest\I18n\Translate;

use Eventjet\I18n\Language\Language;
use Eventjet\I18n\Translate\Translation;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class TranslationTest extends TestCase
{
    public function testExceptionIsThrownIfTextIsNotAString(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Translation(Language::get('de'), true);
    }

    public function testGetLanguage(): void
    {
        $language = Language::get('de');
        $translation = new Translation($language, 'Test');

        $returnedLanguage = $translation->getLanguage();

        self::assertSame($language, $returnedLanguage);
    }

    public function testGetText(): void
    {
        $text = 'Test';
        $translation = new Translation(Language::get('de'), $text);

        $returnedText = $translation->getText();

        self::assertEquals($text, $returnedText);
    }

    #[DataProvider('hasRegionData')]
    public function testHasRegion(string $code, bool $expected): void
    {
        $language = Language::get($code);

        self::assertSame($expected, $language->hasRegion());
    }

    /**
     * @return iterable<string, array{string, bool}>
     */
    public static function hasRegionData(): iterable
    {
        yield 'Language only' => ['de', false];
        yield 'With region' => ['de-CH', true];
    }
}
