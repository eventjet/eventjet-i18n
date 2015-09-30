<?php

namespace EventjetTest\I18n\Translate\Factory;


use Eventjet\I18n\Language\Language;
use Eventjet\I18n\Translate\Factory\TranslationMapFactory;
use PHPUnit_Framework_TestCase;

class TranslationMapFactoryTest extends PHPUnit_Framework_TestCase
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
}
