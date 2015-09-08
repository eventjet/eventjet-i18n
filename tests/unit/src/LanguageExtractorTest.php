<?php

namespace Eventjet\I18nTest;

use Eventjet\I18n\LanguageExtractor;
use Eventjet\I18n\LanguageMapInterface;
use Eventjet\I18n\LanguagePriorityInterface;
use PHPUnit_Framework_TestCase;

class LanguageExtractorTest extends PHPUnit_Framework_TestCase
{
    /** @var LanguageExtractor */
    private $languageExtractor;

    /**
     * @dataProvider extractData
     * @param LanguageMapInterface      $map
     * @param LanguagePriorityInterface $priority
     * @param string                    $expectedReturn
     */
    public function testExtract(LanguageMapInterface $map, LanguagePriorityInterface $priority, $expectedReturn)
    {
        $this->assertEquals($expectedReturn, $this->languageExtractor->extract($map, $priority));
    }

    public function extractData()
    {
        $data = [
            [['de' => 'Deutsch'], ['de'], 'Deutsch'],
            [['de' => 'Deutsch', 'en' => 'English'], ['en', 'de'], 'English'],
            [['de' => 'Deutsch'], ['en'], 'Deutsch'],
            [['de' => 'Deutsch'], ['de-AT'], 'Deutsch'],
            [['de' => 'Deutsch', 'en' => 'English'], ['es'], 'English'],
            [['de' => 'Deutsch'], ['es'], 'Deutsch'],
        ];
        $data = array_map(function (array $item) {
            return [
                $this->createLanguageMap($item[0]),
                $this->createPriority($item[1]),
                $item[2],
            ];
        }, $data);
        return $data;
    }

    private function createLanguageMap(array $mapData)
    {
        $map = $this->getMockBuilder(LanguageMapInterface::class)->getMock();
        $map->method('getAll')
            ->will($this->returnValue($mapData));
        return $map;
    }

    private function createPriority(array $priorityData)
    {
        $map = $this->getMockBuilder(LanguagePriorityInterface::class)->getMock();
        $map->method('getAll')
            ->will($this->returnValue($priorityData));
        return $map;
    }

    protected function setUp()
    {
        $this->languageExtractor = new LanguageExtractor();
    }
}
