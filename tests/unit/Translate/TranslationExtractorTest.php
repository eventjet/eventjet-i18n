<?php

declare(strict_types=1);

namespace EventjetTest\I18n\Translate;

use Eventjet\I18n\Language\Language;
use Eventjet\I18n\Language\LanguagePriority;
use Eventjet\I18n\Translate\Translation;
use Eventjet\I18n\Translate\TranslationExtractor;
use Eventjet\I18n\Translate\TranslationMap;
use PHPUnit\Framework\TestCase;

use function array_map;

class TranslationExtractorTest extends TestCase
{
    private TranslationExtractor $languageExtractor;

    /**
     * @dataProvider extractData
     */
    public function testExtract(
        TranslationMap $map,
        LanguagePriority $priority,
        string $expectedReturn
    ): void {
        self::assertEquals($expectedReturn, $this->languageExtractor->extract($map, $priority));
    }

    /**
     * @return list<array{TranslationMap, LanguagePriority, string}>
     */
    public function extractData(): array
    {
        $data = [
            [['de' => 'Deutsch'], ['de'], 'Deutsch'],
            [['de' => 'Deutsch', 'en' => 'English'], ['en', 'de'], 'English'],
            [['de' => 'Deutsch'], ['en'], 'Deutsch'],
            [['es' => 'Espanol', 'de' => 'Deutsch'], ['de-AT'], 'Deutsch'],
            [['de' => 'Deutsch', 'en' => 'English'], ['es'], 'English'],
            [['de' => 'Deutsch'], ['es'], 'Deutsch'],
        ];
        $data = array_map(
            function (array $item) {
                return [
                    $this->createTranslationMap($item[0]),
                    $this->createPriority($item[1]),
                    $item[2],
                ];
            },
            $data
        );
        return $data;
    }

    /**
     * @param array<string, string> $mapData
     */
    private function createTranslationMap(array $mapData): TranslationMap
    {
        $translations = [];
        foreach ($mapData as $language => $string) {
            $translations[] = new Translation(Language::get($language), $string);
        }
        return new TranslationMap($translations);
    }

    /**
     * @param list<string> $priorityData
     */
    private function createPriority(array $priorityData): LanguagePriority
    {
        return new LanguagePriority(
            array_map(static fn(string $language): Language => Language::get($language), $priorityData)
        );
    }

    protected function setUp(): void
    {
        $this->languageExtractor = new TranslationExtractor();
    }
}
