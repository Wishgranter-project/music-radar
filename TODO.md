# TODO

Review of the current implementation. Items are grouped by severity. Line
references point at the state of the code at the time of writing.

---

## Bugs (correctness)

- [X] **`SearchResults::getAverage()` always returns `0`.**
  `src/SearchResults.php:74-82` sets `$count = 0` and never assigns the real
  count. It should divide by `$this->count()`. This makes the `average` field in
  `SearchResults::toArray()` always wrong.

- [X] **`SearchResultItem::toArray()` crashes when relevance is `null`.**
  `src/SearchResultItem.php:59-65` calls `$this->relevance->toArray()`
  unconditionally, but `getRelevance()` is declared nullable and the constructor
  defaults it to `null`. `SearchResults::getTotal()` (`src/SearchResults.php:60-66`)
  has the same problem. Either make relevance non-nullable or guard the calls.

- [X] **`Search::countResultsScoringAtLeast()` can throw `TypeError`.**
  `src/Search.php:226-232` calls `array_reduce()` without an initial value. On an
  empty item list it returns `null`, violating the `: int` return type. Pass `0`
  as the initial value.

- [X] **Duplicate removal collapses distinct resources with a `null` id.**
  `src/Search.php:244-259` keys items by `getSourceId() . ':' . getId()`. Since
  `ResourceInterface::getId()` is nullable, every resource without an id from the
  same source maps to the same key (`sourceId:`) and is silently dropped. Only
  de-duplicate when the id is non-null (or fall back to title/href).

- [X] **`SoundtrackCriteria` relies on magic properties.**
  `src/Sorting/Criteria/SoundtrackCriteria.php:34-37` accesses
  `$forResource->{$property}`. This works with the bundled `Resource` because it
  implements `__get()`, but `ResourceInterface` only guarantees getters. Use
  `getTitle()` / `getDescription()` explicitly.

- [ ] **`English::singular()` over-strips words.**
  `src/Helper/English.php:34-49` applies naive suffix rules to every word.
  Verified: `"Glass Animals"` becomes `"Glas Animal"` and `"The Beatles"`
  becomes `"The Beatl"`. This feeds `ArtistCriteria`'s "lenient" fallback
  (`src/Sorting/Criteria/ArtistCriteria.php:40-43`), so it can produce wrong
  matches. Consider only unpluralizing words that look like plurals, or drop the
  heuristic.

---

## API and design

- [X] **`setAverageRelevance()` is misleadingly named/documented.**
  `src/Search.php:154` compares each result's *total* score against `$averaging`,
  not an average. Rename to something like `setMinimumScore()` / update docs.

- [X] **`SearchResultsInterface extends \Iterator` instead of `\Countable` /
  `\IteratorAggregate`.** `src/SearchResultsInterface.php:5` exposes `count()`
  but `count($results)` does not work. Consider implementing `\Countable` and
  `\IteratorAggregate` (cleaner than manual array-pointer iteration).

- [X] **`SearchResults::getTotal()` / `getAverage()` are `protected`** but are
  exposed through `toArray()`. If callers need them, make them public; otherwise
  keep them private implementation details.

- [X] **`Radar::addProbe()` requires an explicit priority.**
  `src/Radar.php:29` could default `$priority = 0` for a friendlier API.

- [X] **`Radar::sortProbes()` is more verbose than necessary.**
  `src/Radar.php:60-71` can use `$s2->priority <=> $s1->priority`.

- [X] **`Radar` docblock points at the wrong namespace.**
  `src/Radar.php:14` says `WishgranterProject\MusicProbe\ProbeSlot[]`; the class
  lives in `WishgranterProject\MusicRadar`.

- [X] **Redundant same-namespace import.**
  `src/Radar.php:7` imports `SearchInterface` although `Radar` is already in
  `WishgranterProject\MusicRadar`.

- [X] **`UndesirableCriteria` does not call `parent::__construct()`** while
  `LeftOverCriteria` does (redundantly). Both re-declare the promoted `$weight`
  property. Pick one pattern and use it consistently; as written, future
  constructor logic in `BaseCriteria` would silently not run.

- [X] **`Relevance::getTotal()` recomputes on every call.**
  `src/Sorting/Relevance.php:35-45` is called repeatedly by sorting and
  `toArray()`. Cache the sum or compute once.

---

## Robustness and edge cases

- [ ] **`LiveEventDateCriteria` regexes are loose.**
  `src/Sorting/Criteria/LiveEventDateCriteria.php:32-33`: the short pattern
  `\d+[/.]\d+[/.]\d+` matches version numbers / dotted titles, and the long
  pattern requires a trailing separator after the year. Add tests and tighten.

- [ ] **`TitleCriteria` / `SoundtrackCriteria` use raw substring matching.**
  `src/Sorting/Criteria/TitleCriteria.php:34` matches `"Sonne"` inside
  `"Sonnenschein"`. Consider word-boundary matching or normalisation.

- [ ] **`UndesirableCriteria` ignores `featuring`, `genre` and `cover`.**
  `src/Sorting/Criteria/UndesirableCriteria.php:70-85` only checks title, artist
  and soundtrack when deciding whether a term is part of the description.

- [X] **`LeftOverCriteria` only subtracts title/artist/soundtrack.**
  `src/Sorting/Criteria/LeftOverCriteria.php:45-57` does not strip `featuring`,
  `album`, `cover` or `genre`, so legitimate description terms can be counted as
  leftovers.

- [ ] **`Search::find()` early-exit can miss better results.**
  `src/Search.php:141-159` stops deploying lower-priority probes as soon as one
  result reaches the threshold. This is intentional, but document it; it means
  result completeness depends on probe priority.

---

## Security / repository hygiene

- [X] **A private key is committed to the repository.**
  `.ddev/traefik/certs/aether-music.key` (with its `.crt`) is tracked. It is a
  local `mkcert` dev key, but private keys should never be committed. Remove both
  files from git (`git rm --cached`) and let DDEV regenerate them.

- [X] **Stale `aether-music` DDEV artifacts are tracked.**
  `.ddev/traefik/config/aether-music.yaml` and the certs above use the old
  project name; current DDEV generates `music-radar.*` files that are correctly
  ignored. Remove the leftovers.

- [X] **`examples/.youtube-api-key` handling is fine but fragile.**
  It is gitignored, good. Keep it that way and never echo its contents in logs.

---

## Testing and tooling

- [ ] **Write down those unit tests.** (existing item)
  No test suite exists. Minimum coverage should target:
  - `SearchResults::getAverage()` / `getTotal()` (including empty results).
  - `SearchResultItem::toArray()` with and without relevance.
  - `Search::find()` merging, duplicate removal, early exit and sorting.
  - Each `Sorting/Criteria/*` with matching / non-matching / empty descriptions.
  - `English::unpluralize()` including false positives.

- [ ] **Add PHPUnit and a `composer test` script.**
  Add `require-dev: phpunit/phpunit`, `autoload-dev`, `phpunit.xml.dist` and a
  `scripts.test` entry.

- [ ] **Add static analysis / coding standards.**
  PHPStan (or Psalm) plus PHP-CS-Fixer would catch several items above
  (nullable dereferences, missing return types, the unused `$count`).

- [ ] **Add CI.** Run syntax check, PHPUnit and static analysis on push.

---

## Documentation and packaging

- [ ] **`composer.json` lists required packages under `suggest`.**
  `wishgranter-project/youtube-probe` and `local-files-probe` are both in
  `require` and `suggest`. `suggest` is for optional packages only; remove them
  from one side.

- [ ] **`adinan-cenci/file-cache` is a runtime requirement but only used in
  examples.** `composer.json:42` — move it to `require-dev` (or make the caching
  example optional).

- [ ] **Declare the PHP platform requirement.** The dependency tree already
  requires PHP `>= 8.4` (DDEV and vendor enforce it), but `composer.json` does not
  say so. Add `"php": ">=8.4"`.

- [ ] **README is stale.**
  `README.md` says "Currenty only youtube" although `local-files-probe` is
  supported. Fix that and the typos below.

- [ ] **Fix typos in code/docs.**
  - `src/Search.php:82` `$excelent` -> `$excellent`
  - `src/Search.php:153` `avarege` -> `average`
  - `src/Sorting/Criteria/ArtistCriteria.php:126` `relevante` -> `relevant`
  - `src/Sorting/Criteria/UndesirableCriteria.php:45` `contraproductive` ->
    `counterproductive`
  - `CHANGESLOG.md` filename -> `CHANGELOG.md`

- [ ] **Consider `declare(strict_types=1);` in `src/`.**
  The bundled `music-probe` package uses it; adding it here keeps type behaviour
  consistent and surfaces coercion bugs early.

- [ ] **Complete type hints on remaining public/protected methods.**
  Examples: `SearchResultItem::toArray()` has no `: array`; `Search::mergeSearchResults()`
  has no parameter/return types and relies on `func_get_args()`.
  `ArtistCriteria::getVariation()`, `unpluralize()` and `getRelevantName()` are
  untyped.


# Notes

Overall it's a solid, pragmatic design for a small library. The layering is clean: Radar is a probe registry, Search orchestrates, SearchResults/SearchResultItem are value objects, and Sorting is a separate scoring engine. The criteria-as-pluggable-objects pattern is the right call - it's extensible, composable, and the weights make tuning approachable. Depending only on the music-probe interfaces (not concrete probes) is good dependency inversion, and probe priority + early exit is a sensible cost-saving strategy for remote APIs. The fluent API reads well.
My main architectural concerns:
Search is becoming a god object. It does orchestration, merging, de-duplication, scoring, sorting, and owns the default criteria preset ( src/Search.php:77). The preset is domain configuration, not search logic - extract it (e.g. a Criteria Preset/factory). The missing addDefaultCriteria() on the interface is a symptom of this.
The scoring model is semantically loose. Criteria emit points on inconsistent scales (title = 1, artist = 2, undesirable = occurrence count) and weights multiply them, with negative weights used as penalties. It works, but scores are hard to reason about and hard to test. A clearer contract (e.g. normalized match contribution + explicit penalty) would age better.
The real value is the heuristics, and they're the least engineered part. Substring matching, naive pluralization, loose date regexes that's where result quality lives, yet it's ad hoc and untested. Normalization/tokenization would pay off more than most refactors.
- Correctness is tangled with optimization. Early-exit plus "de-dupe only after the first probe" is subtle; I'd separate the strategy from the mechanics and pin it down with tests.
- Two configuration paths. Constructor args and setters on Search, plus criteria keyed by id with no way to clear them. Pick one style.
Verdict: good bones, easy to extend, but the ranking engine is informal and untested and Search carries too much. If ranking quality matters, that's the area I'd invest in first.