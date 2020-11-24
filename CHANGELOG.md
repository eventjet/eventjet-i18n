# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

File automatically generated with [phly/keep-a-changelog](https://github.com/phly/keep-a-changelog)

## 2.0.0 - 2020-11-24

### Added

- Nothing.

### Changed

- [#24](https://github.com/eventjet/eventjet-i18n/pull/24) marks the following classes as `final`.
  They cannot be extended anymore:
  - `\Eventjet\I18n\Language\Language`
  - `\Eventjet\I18n\Language\LanguagePriority`
  - `\Eventjet\I18n\Translate\Translation`
  - `\Eventjet\I18n\Translate\TranslationMap`

- [#24](https://github.com/eventjet/eventjet-i18n/pull/24) adds type hints and return types in all methods.

- [#24](https://github.com/eventjet/eventjet-i18n/pull/24) changes the signature of
  `\Eventjet\I18n\Translate\TranslatorInterface::translate` to consume a `LanguagePriority` as its second parameter,
  instead of the now removed `LanguagePriorityInterface`.

### Deprecated

- Nothing.

### Removed

- [#24](https://github.com/eventjet/eventjet-i18n/pull/24) removes the following classes/interfaces:
  - `\Eventjet\I18n\Language\LanguageInterface`
  - `\Eventjet\I18n\Language\LanguagePrioriry` (typo)
  - `\Eventjet\I18n\Language\LanguagePriorityInterface`
  - `\Eventjet\I18n\Translate\TranslationExtractor`
  - `\Eventjet\I18n\Translate\TranslationExtractorInterface`
  - `\Eventjet\I18n\Translate\TranslationInterface`
  - `\Eventjet\I18n\Translate\TranslationMapInterface`
  - `\Eventjet\I18n\Translate\Factory\TranslationMapFactory`
  - `\Eventjet\I18n\Translate\Factory\TranslationMapFactoryInterface`

### Fixed

- Nothing.

## 1.7.0 - 2020-11-23

### Added

- [#23](https://github.com/eventjet/eventjet-i18n/pull/23) adds `TranslationMap::create` as replacement for `TranslationMapFactory`
- [#23](https://github.com/eventjet/eventjet-i18n/pull/23) adds `TranslationMap::pick` as replacement for `TranslationExtractor`

### Changed

- [#23](https://github.com/eventjet/eventjet-i18n/pull/23) changes minimum PHP version to 7.4 and adds support for PHP 8.0
- [#23](https://github.com/eventjet/eventjet-i18n/pull/23) marks the following classes as `final` in the docblock:
  - `\Eventjet\I18n\Language\Language`
  - `\Eventjet\I18n\Language\LanguagePriority`
  - `\Eventjet\I18n\Translate\Translation`
  - `\Eventjet\I18n\Translate\TranslationMap`
  
  They will be `final` in the next major version. 

### Deprecated

- [#23](https://github.com/eventjet/eventjet-i18n/pull/23) deprecates `TranslationMapFactoryInterface` and `TranslationMapFactory`.
  Use `TranslationMap::create` as replacement.
- [#23](https://github.com/eventjet/eventjet-i18n/pull/23) deprecates `TranslationExtractorInterface` and `TranslationExtractor`.
  Use `TranslationMap::pick` as replacement.

### Removed

- Nothing.

### Fixed

- [#23](https://github.com/eventjet/eventjet-i18n/pull/23) fixes some minor glitches in doctypes
