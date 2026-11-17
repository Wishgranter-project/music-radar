# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [3.0.0] - 2026-11-17

### Fixed
- Separation of concerns, dismembered the `Search` class into smaller ones.

### Changed
- `Radar::searchFor()` was replaced with `::newSearch()`.

<br><br><br>

## [2.0.0] - 2026-11-17

### Added
- `Relevance` now caches the total, as to not calculate it repeatedly.
- `Search::setMinimumRelevantResults()`.
- Added composer dev requirements.
- Now using `strict_types=1`.

### Changed
- Renamed the classes from "*Criteria" to "*Criterion", singular.
- Renamed `Search::addCriteria()` to `::addCriterion()`.
- Renamed `Search::setAverageRelevance()` to `::setMinimumRelevanceScore()`.
- `SearchResults::getTotal()` and `::getAverage()` are now public.
- Replaced the `English` class for `doctrine/inflector`.
- Renamed `ArtistCriterion::unpluralize()` to `::singularize()`.
- Refactored `LiveEventDateCriterion`.

### Fixed
- Added several type hints, type checks, conditional statements, initial values,
  ternary, spaceship and nullsafe operators.
- `Search::getAverage()` always returned 0, fixed.
- `Search::removeDuplicates()`: added fallbacks in case resource lacks an id.
- Now relying on interface declared methods instead of magic properties.
- Typos, incorrect namespaces, redundant includes and errors in documentation.
- `SearchResults` now implements `\Countable` as it was supposed to.
- Minor issues with the ddev setup.
- No longer tracking `composer.lock`.
- `LeftOverCriterion` now takes featuring, album, cover and genre in
  consideration as well.
- `Search::addDefaultCriterion()`:
  - Updated the terms to the indifferent list.
  - Updated the terms to the undesirable list.
- Added php:^8.4 as a composer requirement.
- `UndesirableCriterion` now takes featuring, cover and genre in consideration as
  well.
- `TitleCriterion` and `ArtistCriterion` now accounts for word boundaries.

<br><br><br>

## [1.0.1] - 2026-09-22
### Fixed
- Now the algorithm uses the featuring property in consideration when sorting 
search results.

<br><br><br>

## [1.0.0] - 2026-07-20

First release. Dismembered from the original library [Wishgranter-project/aether-music](https://github.com/Wishgranter-project/aether-music).
