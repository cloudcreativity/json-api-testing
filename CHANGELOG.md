# Change Log

All notable changes to this project will be documented in this file. This project adheres to
[Semantic Versioning](http://semver.org/) and [this changelog format](http://keepachangelog.com/).

## [0.3.0] - 2018-02-08

### Added
- Now supports PHP 5.6 to 7.2.
- Now supports PHPUnit 5.7 to 7.0.

## [0.2.0] - 2017-09-02

### Removed
- This package no longer supports PHP 5.6.
- Updated to PHPUnit v6.

## [0.1.1] - 2017-09-02

### Added
- Can now assert that a resource object is one of multiple types using `assertTypeIs()`.
- Can now assert that the `data` member of a document is `null`.
- Can now assert that a resource object matches an expected structure.
- Can now assert that the `data` member of a document is a resource identifier.
- Can now assert that a document does not contain an `errors` member.

### Changed
- Added a generic JSON API object tester class containing common assertions.

### Fixed
- Resource object type assertion caused a PHP error.

## [0.1.0] - 2017-09-02

Initial commit of classes and tests brought in from `cloudcreativity/json-api` at `0.10.1`.
