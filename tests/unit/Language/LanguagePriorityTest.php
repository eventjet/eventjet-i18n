<?php

declare(strict_types=1);

namespace EventjetTest\I18n\Language;

use Eventjet\I18n\Language\Language;
use Eventjet\I18n\Language\LanguagePriority;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class LanguagePriorityTest extends TestCase
{
    public function testGetAllReturnsAllLanguages(): void
    {
        $languages = [Language::get('de'), Language::get('en')];
        $priority = new LanguagePriority($languages);

        $all = $priority->getAll();

        self::assertCount(2, $all);
        self::assertEquals($all[0], $languages[0]);
        self::assertEquals($all[1], $languages[1]);
    }

    public function testNoLanguages(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new LanguagePriority([]);
    }

    public function testPrimary(): void
    {
        $priority = new LanguagePriority(
            [
                Language::get('de-AT'),
                Language::get('en-US'),
            ]
        );

        self::assertSame(Language::get('de-AT'), $priority->primary());
    }

    public function testKey(): void
    {
        $priority = new LanguagePriority(
            [
                Language::get('de-AT'),
                Language::get('en-US'),
            ]
        );

        $firstKey = $priority->key();
        $priority->next();
        $nextKey = $priority->key();

        self::assertSame(0, $firstKey);
        self::assertSame(1, $nextKey);
    }

    public function testCurrentThrowsExceptionIfPointerIsBeyondTheElements(): void
    {
        $priority = new LanguagePriority([Language::get('de')]);
        $priority->next();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Pointer is beyond the end of the elements');

        $priority->current();
    }

    public function testRewindResetsInternalPointer(): void
    {
        $languages = new LanguagePriority([Language::get('de'), Language::get('en'), Language::get('fr')]);

        $first = $languages->current();
        $languages->next();
        $second = $languages->current();
        $languages->rewind();
        $firstAgain = $languages->current();

        self::assertSame((string)$first, (string)$firstAgain);
        self::assertNotSame((string)$first, (string)$second);
        self::assertNotSame((string)$second, (string)$firstAgain);
    }

    public function testFromLocaleCreatesLanguagePriorityWithLanguage(): void
    {
        $priority = LanguagePriority::fromLocale('de');

        $languages = $priority->getAll();
        self::assertCount(1, $languages);
    }
}
