# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

File automatically generated with [phly/keep-a-changelog](https://github.com/phly/keep-a-changelog)

## 1.10.0 - 2022-01-11

### Added

- [#29](https://github.com/eventjet/eventjet-i18n/pull/29) adds support for PHP 8.1

### Changed

- [#29](https://github.com/eventjet/eventjet-i18n/pull/29) adds return type hint to TranslationMap::jsonSerialize

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 1.9.0 - 2021-10-30

### Added

- [#27](https://github.com/eventjet/eventjet-i18n/pull/27) adds support for an ICU Translator

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 1.8.0 - 2021-09-15

### Added

- [#26](https://github.com/eventjet/eventjet-i18n/pull/26) adds `LanguagePriority::fromLocale` to shortcut the creation
  with a single language

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 1.7.0 - 2020-11-23

### Added

- [#23](https://github.com/eventjet/eventjet-i18n/pull/23) adds `TranslationMap::create` as replacement
  for `TranslationMapFactory`
- [#23](https://github.com/eventjet/eventjet-i18n/pull/23) adds `TranslationMap::pick` as replacement
  for `TranslationExtractor`

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
