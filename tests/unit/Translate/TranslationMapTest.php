<?php

declare(strict_types=1);

namespace EventjetTest\I18n\Translate;

use Eventjet\I18n\Language\Language;
use Eventjet\I18n\Language\LanguagePriority;
use Eventjet\I18n\Translate\Exception\InvalidTranslationMapDataException;
use Eventjet\I18n\Translate\Factory\TranslationMapFactory;
use Eventjet\I18n\Translate\Translation;
use Eventjet\I18n\Translate\TranslationInterface;
use Eventjet\I18n\Translate\TranslationMap;
use Eventjet\I18n\Translate\TranslationMapInterface;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use function array_map;
use function assert;
use function count;
use function memory_get_usage;
use function sprintf;
use function uniqid;

final class TranslationMapTest extends TestCase
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

        self::assertContainsOnlyInstancesOf(TranslationInterface::class, $translations);
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
     *     TranslationMapInterface,
     *     TranslationMapInterface,
     *     bool,
     * }>
     */
    public static function equalsData(): array
    {
        $data = [
            [['de' => 'DE'], ['de' => 'DE'], true],
            [['de' => 'DE'], ['de' => 'EN'], false],
            [['de' => 'DE'], ['en' => 'DE'], false],
            [['de' => 'DE'], ['de' => 'DE', 'en' => 'EN'], false],
            [['de' => 'DE'], ['de' => 'DE', 'en' => 'DE'], false],
            [['de' => 'DE', 'en' => 'EN'], ['en' => 'EN', 'de' => 'DE'], true],
        ];
        $factory = new TranslationMapFactory();
        $data = array_map(
            static function ($d) use ($factory) {
                $a = $factory->create($d[0]);
                $b = $factory->create($d[1]);
                assert($a !== null);
                assert($b !== null);
                return [$a, $b, $d[2]];
            },
            $data
        );
        $data['same object'] = [$data[0][0], $data[0][0], true];
        return $data;
    }

    #[DataProvider('equalsData')]
    public function testEquals(TranslationMapInterface $a, TranslationMapInterface $b, bool $equal): void
    {
        self::assertEquals($equal, $a->equals($b));
        self::assertEquals($equal, $b->equals($a));
    }

    #[DataProvider('serializationData')]
    public function testSerialization(TranslationMap $map): void
    {
        $serialized = $map->serialize();

        $deserialized = TranslationMap::deserialize($serialized);

        self::assertTrue($deserialized->equals($map));
    }

    /**
     * @return iterable<string, array{TranslationMap}>
     */
    public static function serializationData(): iterable
    {
        yield 'Single translation' => [new TranslationMap([new Translation(Language::get('en'), 'Foo')])];
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

    public function testCreateMap(): void
    {
        $mapData = ['de' => 'Foo'];

        $map = TranslationMap::create($mapData);

        self::assertSame('Foo', $map->get(Language::get('de')));
        self::assertCount(1, $map->getAll());
    }

    public function testCreateMapThrowsExceptionWhenMapDataIsInvalid(): void
    {
        $this->expectException(InvalidTranslationMapDataException::class);

        TranslationMap::create([]);
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
     */
    #[DataProvider('validMapData')]
    public function testCreate(array $mapData): void
    {
        $map = TranslationMap::create($mapData);
        foreach ($mapData as $lang => $text) {
            self::assertEquals($text, $map->get(Language::get($lang)));
        }
    }

    /**
     * @param array<string, string> $mapData
     */
    #[DataProvider('validMapData')]
    public function testCanCreate(array $mapData): void
    {
        /** @psalm-suppress RedundantCondition */
        self::assertTrue(TranslationMap::canCreate($mapData));
    }

    public function testCanNotCreateWithNonArray(): void
    {
        /** @phpstan-ignore-next-line staticMethod.impossibleType */
        self::assertFalse(TranslationMap::canCreate('foo'));
        /** @phpstan-ignore-next-line staticMethod.impossibleType */
        self::assertFalse(TranslationMap::canCreate(42));
        /** @phpstan-ignore-next-line staticMethod.impossibleType */
        self::assertFalse(TranslationMap::canCreate(true));
        /** @phpstan-ignore-next-line staticMethod.impossibleType */
        self::assertFalse(TranslationMap::canCreate(false));
        /** @phpstan-ignore-next-line staticMethod.impossibleType */
        self::assertFalse(TranslationMap::canCreate(null));
        /** @phpstan-ignore-next-line staticMethod.impossibleType */
        self::assertFalse(TranslationMap::canCreate(new class {
        }));
    }

    #[DataProvider('invalidMapData')]
    public function testCanNotCreate(mixed $mapData): void
    {
        self::assertFalse(TranslationMap::canCreate($mapData));
    }

    /**
     * @return iterable<string, array{array<string, string>}>
     */
    public static function validMapData(): iterable
    {
        yield 'Single language' => [['de' => 'Ein Test']];
        yield 'Multiple languages' => [['de' => 'Ein Test', 'en' => 'A test']];
        yield 'Multiple languages with keys in non-ascending order' => [['en' => 'A test', 'de' => 'Ein Test']];
        yield 'Empty text in the middle' => [['de' => 'Ein Test', 'en' => '', 'fr' => 'Un test']];
    }

    /**
     * @param array<string, string> $mapData
     */
    #[DataProvider('invalidMapData')]
    public function testCreateFailsWithInvalidData(array $mapData): void
    {
        $this->expectException(InvalidTranslationMapDataException::class);

        TranslationMap::create($mapData);
    }

    /**
     * @return iterable<string, array{array<string, string>}>
     */
    public static function invalidMapData(): iterable
    {
        yield 'array{}' => [[]];
        yield 'invalid locale' => [['foo' => 'bar']];
        yield 'Invalid element in the middle' => [['de' => 'foo', 'foo' => 'bar', 'en' => 'baz']];
        yield 'all texts are empty' => [['de' => '', 'en' => ' ', 'es' => "\n"]];
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
     * @param array<string, string> $mapData
     */
    #[DataProvider('emptyMapData')]
    public function testCreateThrowsExceptionIfMapDataIsInvalid(array $mapData): void
    {
        $this->expectException(InvalidTranslationMapDataException::class);

        TranslationMap::create($mapData);
    }

    /**
     * @return array<array<array<string, string>>>
     */
    public static function emptyMapData(): array
    {
        return [
            [[]],
            [['de' => '']],
            [['de' => '', 'en' => ' ', 'es' => "\n"]],
        ];
    }

    #[DataProvider('pickData')]
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
    public static function pickData(): array
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
                    self::createTranslationMap($item[0]),
                    self::createPriority($item[1]),
                    $item[2],
                ];
            },
            $data
        );
        return $data;
    }

    public function testMemoryUsage(): void
    {
        // Under Infection, the memory usage is higher. Probably because of coverage collection. If you can find a way
        // to detect Infection (I couldn't find an environment variable), this can be conditionally set to 0.
        $infectionOffset = 35_040;

        $before = memory_get_usage();
        $maps = [];
        for ($i = 0; $i < 1000; $i++) {
            $maps[] = TranslationMap::create(['en' => uniqid(), 'de' => uniqid()]);
        }
        $after = memory_get_usage();
        $diff = $after - $before;

        // array<string, Translation>                       741,168 bytes
        // array<string, string>                            564,784 bytes
        // SplFixedArray<string> + SplFixedArray<string>    477,168 bytes <-- What we are currently using
        // SplFixedArray<int> + SplFixedArray<string>       477,152 bytes <-- Not worth the complexity
        $expectedMaximum = 477_168 + $infectionOffset;
        self::assertLessThanOrEqual($expectedMaximum, $diff, sprintf('%d maps used %d bytes', count($maps), $diff));
        // It's important that we *use* `$maps` so PHP doesn't optimize it away -----------^
    }

    /**
     * @param array<string, string> $mapData
     */
    private static function createTranslationMap(array $mapData): TranslationMap
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
    private static function createPriority(array $priorityData): LanguagePriority
    {
        return new LanguagePriority(
            array_map(static fn(string $language): Language => Language::get($language), $priorityData)
        );
    }
}
