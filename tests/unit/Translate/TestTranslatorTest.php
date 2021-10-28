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

        $this->translator->translate('bar', new LanguagePriority([Language::get('en')]));
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->translator = new TestTranslator();
    }
}
