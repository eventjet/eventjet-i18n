<?php

declare(strict_types=1);

namespace EventjetTest\I18n\Translate\Factory;

use Eventjet\I18n\Language\Language;
use Eventjet\I18n\Translate\Factory\TranslationMapFactory;
use PHPUnit\Framework\TestCase;

class TranslationMapFactoryTest extends TestCase
{
    /** @var TranslationMapFactory */
    private $factory;

    protected function setUp(): void
    {
        $this->factory = new TranslationMapFactory();
    }

    /**
     * @param array<string, string> $mapData
     * @dataProvider validMapData
     */
    public function testCreate(array $mapData): void
    {
        $map = $this->factory->create($mapData);
        self::assertNotNull($map);
        foreach ($mapData as $lang => $text) {
            self::assertEquals($text, $map->get(Language::get($lang)));
        }
    }

    /**
     * @return array<array<array<string, string>>>
     */
    public function validMapData(): array
    {
        return [
            [
                ['de' => 'Ein Test'],
                ['de' => 'Ein Test', 'en' => 'A test'],
                ['en' => 'A test', 'de' => 'Ein Test'],
            ],
        ];
    }

    public function testTextsAreTrimmed(): void
    {
        $map = $this->factory->create(['de' => ' de', 'en' => 'en ', 'es' => ' es ', 'it' => "it\n"]);
        self::assertNotNull($map);
        foreach ($map->getAll() as $translation) {
            self::assertEquals((string)$translation->getLanguage(), $translation->getText());
        }
    }

    public function testEmptyTextsAreRemoved(): void
    {
        $map = $this->factory->create(['de' => 'Test', 'en' => '', 'es' => ' ', 'it' => "\n"]);
        self::assertNotNull($map);
        self::assertCount(1, $map->getAll());
    }

    /**
     * @dataProvider emptyMapData
     * @param array<string, string> $mapData
     */
    public function testCreateReturnsNullifMapDataIsEmpty(array $mapData): void
    {
        $translationMap = $this->factory->create($mapData);

        self::assertNull($translationMap);
    }

    /**
     * @return array<array<array<string, string>>>
     */
    public function emptyMapData(): array
    {
        return [
            [[]],
            [['de' => '']],
            [['de' => '', 'en' => ' ', 'es' => "\n"]],
        ];
    }
}