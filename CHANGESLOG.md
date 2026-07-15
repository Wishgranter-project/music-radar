# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [5.0.5] - 2026-03-17
### Fixed
- [Issue 1](https://github.com/Wishgranter-project/aether-music/issues/1): Calls to YouTube Api stopped working.

---

## [5.0.4] - 2025-06-22
### Fixed
- Updated dependencies and moved to stable.

---

## [5.0.3-alpha] - 2025-06-22
### Fixed
- Small improvements to the sorting criteria, updated dependencies.

---

## [5.0.2-alpha] - 2025-02-17
### Fixed
- Small error in `Description::toArray()`.

### Added
- A new undesired term ( "remastered" ) to `Search::addDefaultCriteria()`

---

## [5.0.1-alpha] - 2024-11-02
### Fixed
- The local files source.
- Improved documentation.

---

## [5.0.0-alpha] - 2024-10-09
### Changed
- Refactored the undesirable criteria.

### Added
- A lax alternative youtube source that ignores the genre from descriptions.
- And with it: `SearchResults::unique()`.

---

## [4.0.0-alpha] - 2024-07-23
### Changed
- Moved classes around and made tweaks to the sorting code.

---

## [3.0.0-alpha] - 2024-02-27
### Changed
- Renamed the package to `wishgranter-project/aether-music` and namespace to `WishgranterProject\MusicRadar`.

---

## [2.0.0-alpha] - 2023-12-10
### Changed
- Moved classes around and made tweaks to the sorting code.

---

## [1.0.0-alpha] - 2023-11-04
### Changed
- `Aether::search()` no longer returns search results straight away, instead it returns a `Search` object ( more below ).

### Added
- Now criteria to sort search results are modular, allowing for customization and extension.
