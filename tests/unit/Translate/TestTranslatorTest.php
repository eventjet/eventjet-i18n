<?php

declare(strict_types=1);

namespace EventjetTest\I18n\Translate;

use Eventjet\I18n\Language\Language;
use Eventjet\I18n\Language\LanguagePriority;
use Eventjet\I18n\Translate\TestTranslator;
use LogicException;
use PHPUnit\Framework\TestCase;

final class TestTranslatorTest extends TestCase
{
    private TestTranslator $translator;

    public function testThrowsIfNoTranslationWasAddedForTheGivenIdInTheGivenLocale(): void
    {
        $this->translator->add('foo', 'de', 'Foo, De');
        $this->translator->add('foo', 'en', 'Foo, En');
        $this->translator->add('bar', 'de', 'Bar, De');

        $this->expectException(LogicException::class);
        $expectedMessage = 'A translation of "bar" in "en" was requested, but no translation was added for this ';
        $expectedMessage .= 'combination. Use $translator->add(\'bar\', \'en\', \'Your translation\') to add one or ';
        $expectedMessage .= '$translator->setLenient() to enable lenient mode and make this error go away.';
        $this->expectExceptionMessage($expectedMessage);

        $this->translator->translate('bar', new LanguagePriority([Language::get('en')]));
    }

    public function testDoesNotThrowInLenientMode(): void
    {
        $this->translator->setLenient();

        $translated = $this->translator->translate('my-message', LanguagePriority::fromLocale('en'));

        // A smoke test using $this->expectNotToPerformAssertions() results in no coverage.
        /** @psalm-suppress RedundantCondition */
        self::assertIsString($translated);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->translator = new TestTranslator();
    }
}
