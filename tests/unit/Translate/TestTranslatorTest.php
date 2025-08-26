<?php

declare(strict_types=1);

namespace EventjetTest\I18n\Translate;

use Eventjet\I18n\Language\Language;
use Eventjet\I18n\Language\LanguagePriority;
use Eventjet\I18n\Translate\TestTranslator;
use LogicException;
use Override;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class TestTranslatorTest extends TestCase
{
    private TestTranslator $translator;

    /**
     * @return iterable<string, array{string, LanguagePriority, string, array<string, array<string, string>>}>
     */
    public static function provideTranslateCases(): iterable
    {
        yield 'Simple' => [
            'my-message',
            LanguagePriority::fromLocale('en'),
            'My message',
            ['my-message' => ['en' => 'My message']],
        ];
    }

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

        $this->expectNotToPerformAssertions();

        $this->translator->translate('my-message', LanguagePriority::fromLocale('en'));
    }

    /**
     * @param array<string, array<string, string>> $translations
     */
    #[DataProvider('provideTranslateCases')]
    public function testTranslate(
        string $message,
        LanguagePriority $languages,
        string $expected,
        array $translations = []
    ): void {
        foreach ($translations as $msg => $msgTranslations) {
            foreach ($msgTranslations as $locale => $translation) {
                $this->translator->add($msg, $locale, $translation);
            }
        }

        $actual = $this->translator->translate($message, $languages);

        self::assertSame($expected, $actual);
    }

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->translator = new TestTranslator();
    }
}
