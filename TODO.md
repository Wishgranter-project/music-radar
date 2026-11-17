# TODO

- [ ] Separation of concerns regarding `Search`.
  - [ ] Separate the hard coded logic into a `DeployProbeStrategy::shouldContinue(SearchResults)` 
    or something.
  - [ ] Separate the criteria in a separated configuration object.
  - [ ] Merge, dedupe, sort, threshold counting. Move them to `SearchResults` or
    to a dedicated `ResultProcessor`.
  - [ ] Scoring: `generateRelevance()` could be moved to `RelevanceCalculator` 
    or something.
- [ ] Inconsistent magic numbers. Need to define a predictable standard for the 
  points individual criterion return. Also need one for the weights.
- [ ] `LiveEventDateCriteria`
  - [ ] Need to account for false positives. Some times resources include 
  copyright notices, upload dates and others that can be confused for live event
  dates.
  - [ ] Need to revise those regex patterns, consider word boundaries. For 
  example, currently `March` can match `Marching`, a false positive.
- [ ] **Add PHPUnit and a `composer test` script.**
  Add `require-dev: phpunit/phpunit`, `autoload-dev`, `phpunit.xml.dist` and a
  `scripts.test` entry.
- [ ] **Write down unit tests.**
- [ ] **`UndesirableCriteria`**
  - [ ] `::isTermInDescription()` is invoked for every Resource.
    And it does not even depend on the resource. Maybe move the logic to 
    `Search`, filter the terms out before the search.
  - [ ] Need to account for word boundaries.
