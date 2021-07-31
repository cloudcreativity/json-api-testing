# Change Log

All notable changes to this project will be documented in this file. This project adheres to
[Semantic Versioning](http://semver.org/) and [this changelog format](http://keepachangelog.com/).

## [3.3.0] - 2021-07-31

### Changed

- Minimum PHP version is now `7.4`.
- Minimum Laravel version is now `8.0`.
- Minimum PHPUnit version is now `9.0`.

### Fixed

- [#19](https://github.com/cloudcreativity/json-api-testing/issues/19) The expected location for the
  `assertCreatedWithServerId()` and `assertCreatedWithClientId()` can now be `null`, indicating that the `Location`
  header is not expected.

### Deprecated

- The following methods are deprecated and will be removed in `4.0`:
  - `assertDeleted()` - use `assertNoContent()` or `assertMetaWithoutData()` depending on your expected response.
  - `assertUpdated()` - use `assertNoContent()` or `assertFetchedOne()` depending on your expected response.

## [3.2.0] - 2020-11-25

### Added
- Package now supports PHP 8.

## [3.1.0] - 2020-09-09

### Added
- Package now supports Laravel 8.

## [3.0.0] - 2020-04-12

### Added
- Package now supports PHP Unit 9.

### Changed
- Dropped support for PHP Unit 7.
- Dropped support for Laravel 5.

## [2.1.0] - 2020-03-04

### Added
- Package now supports Laravel 7.
- [#9](https://github.com/cloudcreativity/json-api-testing/issues/9)
Can now assert that there are no resources included - i.e. that the `included` member is not present
or is an empty list.

### Fixed
- [#10](https://github.com/cloudcreativity/json-api-testing/pull/10)
Pass expected and actual values to PHPUnit's assertion in correct order.

## [2.0.0] - 2019-10-14

### Changed
- Minimum `illuminate/support` version is now `5.8` (previously `5.5`).
- Minimum PHPUnit version is now `7.5` (previously `6.0`).

### Removed
- Removed the `assertNoContent()` method from the `Concerns\HasHttpAssertions` trait. Unlike other
assertion methods in that trait, this method refers to a HTTP status description which means it is
likely to collide with assertions provided by frameworks. For example, Laravel `6.1.0` introduced
an `assertNoContent()` method to its test response which is not compatible with the implementation
provided by this package.

## [1.2.0] - 2019-09-04

### Added
- Package now supports Laravel 6.

## [1.1.0] - 2019-05-23

### Added
- Package now supports PHPUnit 8.

## [1.0.0] - 2019-02-27

### Added
- [#3](https://github.com/cloudcreativity/json-api-testing/issues/3)
Can now assert exact errors on a document and HTTP message.
- [#5](https://github.com/cloudcreativity/json-api-testing/issues/5)
Can now assert a server generated id with a known id.

### Fixed
- [#6](https://github.com/cloudcreativity/json-api-testing/issues/6)
Fixed asserting that an error exists with only an integer HTTP status code.
- [#4](https://github.com/cloudcreativity/json-api-testing/issues/4)
Fixed incorrect diff when asserting a resource identifier on a document that contains a resource object.

## [1.0.0-rc.1] - 2019-01-03

### Added
- New implementation using constraint classes. Assertions are now provided via the `Assert` and `HttpAssert`
classes, with traits in the `Concerns` namespace for adding these to test classes.

### Changed
- Minimum PHP version is now `7.1`.

### Removed
- Package no longer supports PHPUnit 5.
- The previous implementation was deleted, removing these classes:
  - `AbstractTraversableTester`
  - `DocumentTester`
  - `ErrorsTester`
  - `ErrorTester`
  - `ObjectTester`
  - `ResourceIdentifierTester`
  - `ResourceObjectsTester`
  - `ResourceObjectTester`

## [0.4.0] - 2018-04-29

### Added
- Object tester now had meta test helpers.
- Can now assert the order of resources within the a resource object collection.

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
