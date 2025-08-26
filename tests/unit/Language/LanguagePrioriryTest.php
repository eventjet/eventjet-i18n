<?php

declare(strict_types=1);

namespace EventjetTest\I18n\Language;

use Eventjet\I18n\Language\LanguagePrioriry;
use PHPUnit\Framework\TestCase;

use function class_exists;

final class LanguagePrioriryTest extends TestCase
{
    public function testExists(): void
    {
        self::assertTrue(class_exists(LanguagePrioriry::class));
    }
}
