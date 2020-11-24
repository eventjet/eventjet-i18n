<?php

declare(strict_types=1);

namespace EventjetTest\I18n\Translate;

use Eventjet\I18n\Language\Language;
use Eventjet\I18n\Language\LanguagePriority;
use Eventjet\I18n\Translate\Exception\InvalidTranslationMapDataException;
use Eventjet\I18n\Translate\Translation;
use Eventjet\I18n\Translate\TranslationMap;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

use function array_map;
use function assert;
use function reset;
use function spl_object_id;

class TranslationMapTest extends TestCase
{
    public function testHasReturnsFalseIfTranslationDoesNotExist(): void
    {
        $map = new TranslationMap([new Translation(Language::get('de'), 'Test')]);

        self::assertFalse($map->has(Language::get('en')));
    }

    public function testWithTranslation(): void
    {
        $map = new TranslationMap([new Translation(Language::get('de'), 'Deutsch')]);

        $en = Language::get('en');
        $english = 'English';
        $newMap = $map->withTranslation(new Translation($en, $english));

        self::assertEquals($english, $newMap->get($en));
        self::assertFalse($map->has($en));
    }

    public function testWithTranslationOverridesExistingTranslation(): void
    {
        $de = Language::get('de');
        $map = new TranslationMap([new Translation($de, 'Original')]);

        $newMap = $map->withTranslation(new Translation($de, 'Overridden'));

        self::assertEquals('Overridden', $newMap->get($de));
        self::assertEquals('Original', $map->get($de));
    }

    public function testGetAllReturnsArrayOfTranslations(): void
    {
        $map = new TranslationMap(
            [
                new Translation(Language::get('de'), 'Deutsch'),
                new Translation(Language::get('en'), 'English'),
                new Translation(Language::get('it'), 'Italiano'),
            ]
        );

        $translations = $map->getAll();

        self::assertContainsOnlyInstancesOf(Translation::class, $translations);
    }

    public function testEmptyMapThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new TranslationMap([]);
    }

    public function testGetReturnsNullIfNoTranslationsExistsForTheGivenLanguage(): void
    {
        $map = new TranslationMap([new Translation(Language::get('de'), 'Test')]);

        self::assertNull($map->get(Language::get('en')));
    }

    public function testJsonSerialize(): void
    {
        $map = new TranslationMap(
            [
                new Translation(Language::get('en'), 'My Test'),
                new Translation(Language::get('de'), 'Mein Test'),
            ]
        );

        $json = $map->jsonSerialize();

        self::assertCount(2, $json);
        self::assertEquals($json['en'], 'My Test');
        self::assertEquals($json['de'], 'Mein Test');
    }

    /**
     * @return array<string|int, array{
     *     TranslationMap,
     *     TranslationMap,
     *     bool,
     * }>
     */
    public function equalsData(): array
    {
        $data = [
            [['de' => 'DE'], ['de' => 'DE'], true],
            [['de' => 'DE'], ['de' => 'EN'], false],
            [['de' => 'DE'], ['en' => 'DE'], false],
            [['de' => 'DE'], ['de' => 'DE', 'en' => 'EN'], false],
            [['de' => 'DE'], ['de' => 'DE', 'en' => 'DE'], false],
            [['de' => 'DE', 'en' => 'EN'], ['en' => 'EN', 'de' => 'DE'], true],
        ];
        $data = array_map(
            static function ($d) {
                $a = TranslationMap::create($d[0]);
                $b = TranslationMap::create($d[1]);
                return [$a, $b, $d[2]];
            },
            $data
        );
        $data['same object'] = [$data[0][0], $data[0][0], true];
        return $data;
    }

    /**
     * @dataProvider equalsData
     */
    public function testEquals(TranslationMap $a, TranslationMap $b, bool $equal): void
    {
        self::assertEquals($equal, $a->equals($b));
        self::assertEquals($equal, $b->equals($a));
    }

    /**
     * @dataProvider serializationData
     */
    public function testSerialization(TranslationMap $map): void
    {
        $serialized = $map->serialize();

        $deserialized = TranslationMap::deserialize($serialized);

        self::assertTrue($deserialized->equals($map));
    }

    /**
     * @return iterable<string, array{TranslationMap}>
     */
    public function serializationData(): iterable
    {
        yield 'Single translation' => [new TranslationMap([new Translation(Language::get('en'), 'Foo')])];
        yield 'Multiple translation' => [TranslationMap::create(['de' => 'Foo', 'en' => 'EnFoo'])];
    }

    public function testDeserializeIgnoresWrongLanguageInArrayKey(): void
    {
        $serialized = ['de' => ['language' => 'en', 'text' => 'Foo']];

        $deserialized = TranslationMap::deserialize($serialized);

        $onlyTranslation = $deserialized->getAll();
        self::assertCount(1, $onlyTranslation);
        $onlyTranslation = reset($onlyTranslation);
        assert($onlyTranslation instanceof Translation);
        self::assertSame('en', (string)$onlyTranslation->getLanguage());
    }

    public function testWithEachModified(): void
    {
        $original = new TranslationMap(
            [
                new Translation(Language::get('en'), 'My String'),
                new Translation(Language::get('de'), 'Mein String'),
            ]
        );

        $german = Language::get('de');
        $modified = $original->withEachModified(
            static function (string $translation, Language $language) use ($german): string {
                if ($language === $german) {
                    return $translation . ' (Kopie)';
                }
                return $translation . ' (copy)';
            }
        );

        self::assertEquals('Mein String (Kopie)', $modified->get(Language::get('de')));
        self::assertEquals('My String (copy)', $modified->get(Language::get('en')));
    }

    public function testWithEachModifiedIsImmutable(): void
    {
        $original = new TranslationMap(
            [
                new Translation(Language::get('en'), 'My String'),
                new Translation(Language::get('de'), 'Mein String'),
            ]
        );

        $modified = $original->withEachModified(fn(string $text) => $text);

        self::assertNotSame(spl_object_id($original), spl_object_id($modified));
    }

    public function testCreateMap(): void
    {
        $mapData = ['de' => 'Foo'];

        $map = TranslationMap::create($mapData);

        self::assertSame('Foo', $map->get(Language::get('de')));
        self::assertCount(1, $map->getAll());
    }

    public function testPickTranslation(): void
    {
        $priority = new LanguagePriority([Language::get('de'), Language::get('en')]);
        $map = TranslationMap::create(['en' => 'English', 'de' => 'Deutsch']);

        $result = $map->pick($priority);

        self::assertSame('Deutsch', $result);
    }

    public function testPickMultipleTranslations(): void
    {
        $map = TranslationMap::create(['en' => 'English', 'de' => 'German', 'es' => 'Spanish', 'fr' => 'French']);
        $priorityA = new LanguagePriority([Language::get('de'), Language::get('en')]);
        $priorityB = new LanguagePriority([Language::get('fr'), Language::get('es')]);

        $resultA = $map->pick($priorityA);
        $resultB = $map->pick($priorityB);

        self::assertSame('German', $resultA);
        self::assertSame('French', $resultB);
    }

    /**
     * @param array<string, string> $mapData
     * @dataProvider validMapData
     */
    public function testCreate(array $mapData): void
    {
        $map = TranslationMap::create($mapData);
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
        $map = TranslationMap::create(['de' => ' de', 'en' => 'en ', 'es' => ' es ', 'it' => "it\n"]);

        foreach ($map->getAll() as $translation) {
            self::assertEquals((string)$translation->getLanguage(), $translation->getText());
        }
    }

    public function testEmptyTextsAreRemoved(): void
    {
        $map = TranslationMap::create(['de' => 'Test', 'en' => '', 'es' => ' ', 'it' => "\n"]);

        self::assertCount(1, $map->getAll());
    }

    /**
     * @dataProvider emptyMapData
     * @param array<string, string> $mapData
     */
    public function testCreateThrowsExceptionIfMapDataIsInvalid(array $mapData): void
    {
        $this->expectException(InvalidTranslationMapDataException::class);

        TranslationMap::create($mapData);
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

    /**
     * @dataProvider pickData
     */
    public function testPick(
        TranslationMap $map,
        LanguagePriority $priority,
        string $expectedReturn
    ): void {
        self::assertEquals($expectedReturn, $map->pick($priority));
    }

    /**
     * @return list<array{TranslationMap, LanguagePriority, string}>
     */
    public function pickData(): array
    {
        $data = [
            [['de' => 'Deutsch'], ['de'], 'Deutsch'],
            [['de' => 'Deutsch', 'en' => 'English'], ['en', 'de'], 'English'],
            [['de' => 'Deutsch'], ['en'], 'Deutsch'],
            [['es' => 'Espanol', 'de' => 'Deutsch'], ['de-AT'], 'Deutsch'],
            [['de' => 'Deutsch', 'en' => 'English'], ['es'], 'English'],
            [['de' => 'Deutsch'], ['es'], 'Deutsch'],
            [['es' => 'Spanish', 'de' => 'Deutsch'], ['fr', 'de-DE', 'es'], 'Deutsch'],
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
}
