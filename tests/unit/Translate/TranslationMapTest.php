<?php declare(strict_types=1);

namespace EventjetTest\I18n\Translate;

use Eventjet\I18n\Language\Language;
use Eventjet\I18n\Translate\Factory\TranslationMapFactory;
use Eventjet\I18n\Translate\Translation;
use Eventjet\I18n\Translate\TranslationInterface;
use Eventjet\I18n\Translate\TranslationMap;
use Eventjet\I18n\Translate\TranslationMapInterface;
use PHPUnit\Framework\TestCase;

class TranslationMapTest extends TestCase
{
    public function testHasReturnsFalseIfTranslationDoesNotExist(): void
    {
        $map = new TranslationMap([new Translation(Language::get('de'), 'Test')]);

        $this->assertFalse($map->has(Language::get('en')));
    }

    public function testWithTranslation(): void
    {
        $map = new TranslationMap([new Translation(Language::get('de'), 'Deutsch')]);

        $en = Language::get('en');
        $english = 'English';
        $newMap = $map->withTranslation(new Translation($en, $english));

        $this->assertEquals($english, $newMap->get($en));
        $this->assertFalse($map->has($en));
    }

    public function testWithTranslationOverridesExistingTranslation(): void
    {
        $de = Language::get('de');
        $map = new TranslationMap([new Translation($de, 'Original')]);

        $newMap = $map->withTranslation(new Translation($de, 'Overridden'));

        $this->assertEquals('Overridden', $newMap->get($de));
        $this->assertEquals('Original', $map->get($de));
    }

    public function testGetAllReturnsArrayOfTranslations(): void
    {
        $map = new TranslationMap([
            new Translation(Language::get('de'), 'Deutsch'),
            new Translation(Language::get('en'), 'English'),
            new Translation(Language::get('it'), 'Italiano'),
        ]);

        $translations = $map->getAll();

        $this->assertContainsOnlyInstancesOf(TranslationInterface::class, $translations);
    }

    public function testEmptyMapThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new TranslationMap([]);
    }

    public function testGetReturnsNullIfNoTranslationsExistsForTheGivenLanguage(): void
    {
        $map = new TranslationMap([new Translation(Language::get('de'), 'Test')]);

        $this->assertNull($map->get(Language::get('en')));
    }

    public function testJsonSerialize(): void
    {
        $map = new TranslationMap([
            new Translation(Language::get('en'), 'My Test'),
            new Translation(Language::get('de'), 'Mein Test'),
        ]);

        $json = $map->jsonSerialize();

        $this->assertCount(2, $json);
        $this->assertEquals($json['en'], 'My Test');
        $this->assertEquals($json['de'], 'Mein Test');
    }

    public function equalsData()
    {
        $data = [
            [['de' => 'DE'], ['de' => 'DE'], true],
            [['de' => 'DE'], ['de' => 'EN'], false],
            [['de' => 'DE'], ['en' => 'DE'], false],
            [['de' => 'DE'], ['de' => 'DE', 'en' => 'EN'], false],
            [['de' => 'DE'], ['de' => 'DE', 'en' => 'DE'], false],
            [['de' => 'DE', 'en' => 'EN'], ['en' => 'EN', 'de' => 'DE'], true],
        ];
        $factory = new TranslationMapFactory;
        $data = array_map(function ($d) use ($factory) {
            return [$factory->create($d[0]), $factory->create($d[1]), $d[2]];
        }, $data);
        $data['same object'] = [$data[0][0], $data[0][0], true];
        return $data;
    }

    /**
     * @dataProvider equalsData
     */
    public function testEquals(TranslationMapInterface $a, TranslationMapInterface $b, bool $equal): void
    {
        $this->assertEquals($equal, $a->equals($b));
        $this->assertEquals($equal, $b->equals($a));
    }
}
