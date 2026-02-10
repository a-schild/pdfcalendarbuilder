# Change Log

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [1.0.15 2026-02-10]

### Added
- Test dev dependencies (codeception/codeception, codeception/module-asserts)
- Test for >7 categories with two-line long category names

### Changed
- Nothing

### Fixed
- Category legend boxes now have equal heights per row when some category names wrap to two lines
- Removed white space gap between calendar grid and category legend

## [1.0.14 2026-02-10]

### Added
- Nothing

### Changed
- Nothing

### Fixed
- Legend overlapping last row of calendar days when more than 7 categories are displayed [#6](https://github.com/a-schild/pdfcalendarbuilder/issues/6)
- Missing $ prefix on variable in ColorNames::html2html() causing fatal error when withDash=true
- Hex color values not zero-padded in ColorNames::html2html() (e.g. rgb(0,0,255) produced "00ff" instead of "0000ff")
- usort() comparator in sortEntries() returning bool instead of int (PHP 8.0+ compliance)

## [1.0.13 2026-01-15]

### Added
- Nothing

### Changed
- Upgraded php to 8.2+
- Upgrade tcpdf dependency to 6.10.1+
- Added categories variable for 8.2 syntax compliance

## [1.0.12 2025-09-17]

### Added
- Nothing

### Changed
- Upgrade tcpdf dependency to 6.10+

### Fixed

- None