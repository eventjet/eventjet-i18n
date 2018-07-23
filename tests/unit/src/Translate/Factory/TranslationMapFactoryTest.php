<?php

namespace EventjetTest\I18n\Translate\Factory;


use Eventjet\I18n\Language\Language;
use Eventjet\I18n\Translate\Factory\TranslationMapFactory;
use PHPUnit\Framework\TestCase;

class TranslationMapFactoryTest extends TestCase
{
    /** @var TranslationMapFactory */
    private $factory;

    public function setUp()
    {
        $this->factory = new TranslationMapFactory();
    }

    /**
     * @param array $mapData
     * @dataProvider validMapData
     */
    public function testCreate(array $mapData)
    {
        $map = $this->factory->create($mapData);
        foreach ($mapData as $lang => $text) {
            $this->assertEquals($text, $map->get(Language::get($lang)));
        }
    }

    public function validMapData()
    {
        return [
            [
                ['de' => 'Ein Test'],
                ['de' => 'Ein Test', 'en' => 'A test'],
                ['en' => 'A test', 'de' => 'Ein Test'],
            ]
        ];
    }

    public function testTextsAreTrimmed()
    {
        $map = $this->factory->create(['de' => ' de', 'en' => 'en ', 'es' => ' es ', 'it' => "it\n"]);
        foreach ($map->getAll() as $translation) {
            $this->assertEquals((string)$translation->getLanguage(), $translation->getText());
        }
    }

    public function testEmptyTextsAreRemoved()
    {
        $map = $this->factory->create(['de' => 'Test', 'en' => '', 'es' => ' ', 'it' => "\n"]);
        $this->assertCount(1, $map->getAll());
    }

    /**
     * @dataProvider emptyMapData
     * @param array $mapData
     */
    public function testCreateReturnsNullifMapDataIsEmpty(array $mapData)
    {
        $translationMap = $this->factory->create($mapData);

        $this->assertNull($translationMap);
    }

    public function emptyMapData()
    {
        return [
            [[]],
            [['de' => '']],
            [['de' => '', 'en' => ' ', 'es' => "\n"]],
        ];
    }
}
