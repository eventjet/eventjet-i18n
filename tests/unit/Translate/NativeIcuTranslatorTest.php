<?php

declare(strict_types=1);

namespace EventjetTest\I18n\Translate;

use Eventjet\I18n\Language\Language;
use Eventjet\I18n\Language\LanguagePriority;
use Eventjet\I18n\Translate\NativeIcuTranslator;
use Eventjet\I18n\Translate\TestTranslator;
use Override;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;

use function microtime;

final class NativeIcuTranslatorTest extends TestCase
{
    private NativeIcuTranslator $translator;
    private TestTranslator $translations;

    /**
     * @param array<string, array<string, string>> $translations
     * @param array<string, mixed> $arguments
     */
    #[DataProvider('translateCases')]
    public function testTranslate(
        array $translations,
        string $messageId,
        LanguagePriority $languages,
        array $arguments,
        ?string $expected
    ): void {
        foreach ($translations as $id => $localeTranslations) {
            foreach ($localeTranslations as $locale => $translation) {
                $this->translations->add($id, $locale, $translation);
            }
        }

        $formatted = $this->translator->translate($messageId, $languages, $arguments);

        self::assertSame($expected, $formatted);
    }

    /**
     * @return iterable<string, array{
     *     array<string, array<string, string>>,
     *     string,
     *     LanguagePriority,
     *     array<string, mixed>,
     *     string|null,
     * }>
     */
    public static function translateCases(): iterable
    {
        yield 'No arguments' => [
            ['my-message' => ['de' => 'Die Übersetzung']],
            'my-message',
            new LanguagePriority([Language::get('de')]),
            [],
            'Die Übersetzung',
        ];
        yield 'Simple placeholder' => [
            ['my-message' => ['en' => 'My first name is {firstName}.']],
            'my-message',
            new LanguagePriority([Language::get('en')]),
            ['firstName' => 'Rudolph'],
            'My first name is Rudolph.',
        ];
        $newMessagePattern = '
            {messages, plural,
                =0 {No messages}
                =1 {New message}
                other {# new messages}
            }
        ';
        yield 'Plural, zero' => [
            ['my-message' => ['en' => $newMessagePattern]],
            'my-message',
            new LanguagePriority([Language::get('en')]),
            ['messages' => 0],
            'No messages',
        ];
        yield 'Plural, one' => [
            ['my-message' => ['en' => $newMessagePattern]],
            'my-message',
            new LanguagePriority([Language::get('en')]),
            ['messages' => 1],
            'New message',
        ];
        yield 'Plural, many' => [
            ['my-message' => ['en' => $newMessagePattern]],
            'my-message',
            new LanguagePriority([Language::get('en')]),
            ['messages' => 23],
            '23 new messages',
        ];
        yield 'Invalid pattern' => [
            ['invalid' => ['fr' => '{messages, plural, =0 {No']],
            'invalid',
            new LanguagePriority([Language::get('fr')]),
            ['messages' => 23],
            null,
        ];
        yield 'Invalid argument' => [
            ['invalid' => ['fr' => '{foo}']],
            'invalid',
            new LanguagePriority([Language::get('fr')]),
            ['foo' => new stdClass()],
            null,
        ];
    }

    public function testSameParametersPerformance(): void
    {
        $pattern = '
            {messages, plural,
                =0 {Keine neuen Nachrichten}
                =1 {Neue Nachricht}
                other {# neue Nachrichten}}
        ';
        $this->translations->add('new-messages', 'de', $pattern);

        $languages = LanguagePriority::fromLocale('de');
        $start = microtime(true);
        for ($i = 0; $i < 100_000; $i++) {
            $this->translator->translate('new-messages', $languages, ['messages' => 3]);
        }
        $end = microtime(true);

        self::assertLessThan(1, $end - $start);
    }

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->translations = new TestTranslator();
        $this->translator = new NativeIcuTranslator($this->translations);
    }
}
