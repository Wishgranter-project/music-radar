# TODO

- [ ] `ArtistCriterion` and `FeaturingCriterion`: Given a resource with 2 artists,
  should a resource featuring both artists score higher than a resource 
  featuring a single one ?
- [ ] Inconsistent magic numbers. Need to define a predictable standard for the 
  points individual criterion return. Also need one for the weights.
- [x] `LiveEventDateCriterion`
  - [x] Need to account for false positives. Some times resources include 
  copyright notices, upload dates and others that can be confused for live event
  dates.
  - [x] Need to revise those regex patterns, consider word boundaries. For 
  example, currently `March` can match `Marching`, a false positive.
- [ ] **Add PHPUnit and a `composer test` script.**
  Add `require-dev: phpunit/phpunit`, `autoload-dev`, `phpunit.xml.dist` and a
  `scripts.test` entry.
- [ ] **Write down unit tests.**
- [ ] **`UndesirableCriterion`**
  - [ ] `::isTermInDescription()` is invoked for every Resource.
    And it does not even depend on the resource. Maybe move the logic to 
    `Search`, filter the terms out before the search.
  - [ ] Need to account for word boundaries.
- [ ] Fix documentation: `Radar::sortProbes()` sorts priorities ascending (
  lower values deployed first), while `README.md` and the docblocks say higher 
  priority is deployed first.
- [ ] Penalize more keywords:
    - "Album Trailer"
    - "Album Review"
