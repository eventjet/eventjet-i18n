<?php

namespace EventjetTest\I18n;

use Eventjet\I18n\Language;
use Eventjet\I18n\LanguagePrioriry;
use Eventjet\I18n\LanguagePriorityInterface;
use Eventjet\I18n\Translation;
use Eventjet\I18n\TranslationExtractor;
use Eventjet\I18n\TranslationMap;
use Eventjet\I18n\TranslationMapInterface;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;

class TranslationExtractorTest extends PHPUnit_Framework_TestCase
{
    /** @var TranslationExtractor */
    private $languageExtractor;

    /**
     * @dataProvider extractData
     * @param TranslationMapInterface   $map
     * @param LanguagePriorityInterface $priority
     * @param string                    $expectedReturn
     */
    public function testExtract(TranslationMapInterface $map, LanguagePriorityInterface $priority, $expectedReturn)
    {
        $this->assertEquals($expectedReturn, $this->languageExtractor->extract($map, $priority));
    }

    public function extractData()
    {
        $data = [
            [['de' => 'Deutsch'], ['de'], 'Deutsch'],
            [['de' => 'Deutsch', 'en' => 'English'], ['en', 'de'], 'English'],
            [['de' => 'Deutsch'], ['en'], 'Deutsch'],
            [['es' => 'Espanol', 'de' => 'Deutsch'], ['de-AT'], 'Deutsch'],
            [['de' => 'Deutsch', 'en' => 'English'], ['es'], 'English'],
            [['de' => 'Deutsch'], ['es'], 'Deutsch'],
        ];
        $data = array_map(function (array $item) {
            return [
                $this->createTranslationMap($item[0]),
                $this->createPriority($item[1]),
                $item[2],
            ];
        }, $data);
        return $data;
    }

    /**
     * @param array $mapData
     * @return PHPUnit_Framework_MockObject_MockObject|TranslationMap
     */
    private function createTranslationMap(array $mapData)
    {
        $translations = [];
        foreach ($mapData as $language => $string) {
            $translations[] = new Translation(Language::get($language), $string);
        }
        return new TranslationMap($translations);
    }

    private function createPriority(array $priorityData)
    {
        return new LanguagePrioriry(array_map(function ($language) {
            return Language::get($language);
        }, $priorityData));
    }

    protected function setUp()
    {
        $this->languageExtractor = new TranslationExtractor();
    }
}
