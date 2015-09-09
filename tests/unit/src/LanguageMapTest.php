<?php

namespace Eventjet\I18nTest;

use Eventjet\I18n\LanguageMap;
use PHPUnit_Framework_TestCase;

class LanguageMapTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider validData
     * @param array $mapData
     */
    public function testCreateValidMap(array $mapData)
    {
        $this->assertInstanceOf('Eventjet\I18n\LanguageMap', new LanguageMap($mapData));
    }

    public function validData()
    {
        $data = [
            [['de' => 'Deutsch']],
            [['de' => 'Deutsch', 'en' => 'English']],
            [['de-DE' => 'Deutsch-DE']],
            [['en-US' => 'English-US', 'de' => 'Deutsch']],
            [['en' => 'English', 'en-US' => 'English-US', 'en-GB' => 'English-GB']],
        ];

        return $data;
    }

    /**
     * @dataProvider invalidData
     * @param array $mapData
     */
    public function testCreateInvalidMap(array $mapData)
    {
        $this->setExpectedException('Eventjet\I18n\Exception\InvalidLanguageFormatException');
        new LanguageMap($mapData);
    }

    public function invalidData()
    {
        $data = [
            [['De' => 'De']],
            [['dE' => 'dE']],
            [['de-De' => 'de-De']],
            [['De-de' => 'De-de']],
            [['dE-de' => 'dE-de']],
            [['de-dE' => 'de-dE']],
            [['DE' => 'DE', 'en' => 'en']],
            [['de_DE' => 'de_DE']],
            [['en-us' => 'en-us', 'de' => 'de']],
            [['en' => 'en', 'en-US' => 'en-US', 'en_gb' => 'en_gb']],
        ];

        return $data;
    }
}
