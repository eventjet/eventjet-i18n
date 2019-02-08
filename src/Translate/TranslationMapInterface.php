<?php declare(strict_types=1);

namespace Eventjet\I18n\Translate;

use Eventjet\I18n\Language\LanguageInterface;
use JsonSerializable;

interface TranslationMapInterface extends JsonSerializable
{
    /**
     * @param LanguageInterface $language
     * @return bool
     */
    public function has(LanguageInterface $language);

    /**
     * @param LanguageInterface $language
     * @return string|null
     */
    public function get(LanguageInterface $language);

    /**
     * @return Translation[]
     */
    public function getAll();

    /**
     * @param TranslationInterface $translation
     * @return TranslationMapInterface
     */
    public function withTranslation(TranslationInterface $translation);

    /**
     * @param TranslationMapInterface $other
     * @return bool
     */
    public function equals(TranslationMapInterface $other);
}
