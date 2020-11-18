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
use PHPUnit\Framework\TestCase;

use function array_map;

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
     * @return mixed[]
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
        $factory = new TranslationMapFactory();
        $data = array_map(
            static function ($d) use ($factory) {
                return [$factory->create($d[0]), $factory->create($d[1]), $d[2]];
            },
            $data
        );
        $data['same object'] = [$data[0][0], $data[0][0], true];
        return $data;
    }

    /**
     * @dataProvider equalsData
     */
    public function testEquals(TranslationMapInterface $a, TranslationMapInterface $b, bool $equal): void
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
     * @return iterable<array<TranslationMap>>
     */
    public function serializationData(): iterable
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

        $modified = $original->withEachModified(
            static function (string $translation, Language $language): string {
                if ($language === Language::get('de')) {
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
}
