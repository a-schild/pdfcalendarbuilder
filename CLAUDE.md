# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

PDFCalendarBuilder is a PHP library (`a-schild/pdfcalendarbuilder`) that generates PDF monthly calendars using TCPDF. It supports auto-scaling row heights, font size shrinking, day-spanning events, categories with colored legends, and multi-month PDFs.

## Build & Test Commands

```bash
# Install dependencies
composer install

# Run all unit tests
./vendor/bin/codecept run unit

# Run a single test file
./vendor/bin/codecept run unit aschild/PDFCalendarBuilder/CalendarBuilderTest

# Run a specific test method
./vendor/bin/codecept run unit aschild/PDFCalendarBuilder/CalendarBuilderTest:testEmptyCalendar
```

There is no linter or static analysis configured.

## Architecture

Three classes in `src/PDFCalendarBuilder/`, namespace `aschild\PDFCalendarBuilder`:

- **CalendarBuilder** — Main class. Creates the TCPDF instance, manages grid layout, renders the calendar. Key flow: `__construct()` → `startPDF()` → `addEntry()`/`addEntryCategory()` → `buildCalendar()` → `Output()`. For multi-month PDFs, call `addMonth()` + `buildCalendar()` for each additional month before `Output()`.

- **CalendarEntry** — Data model for a single event. Holds start/end dates, message, text/background colors. Tracks whether it's a continuation of a multi-day event (`continuationEntry`, `hideStartTime`/`hideEndTime`).

- **ColorNames** — Static utility for color conversion. Maps 130+ named colors to RGB, converts HTML hex (`#RRGGBB`) to RGB arrays.

### Layout Algorithm

The calendar uses a transaction-based layout optimization in `buildCalendar()`:

1. Draw grid with uniform row heights
2. `checkContentInsideDayBoxes()` detects overflow
3. If overflow and `resizeRowHeightsIfNeeded` is enabled: `adaptRowHeights()` redistributes space from minimal-height rows to overflowing ones (recursive, uses epsilon 0.0001 for float comparison)
4. If still overflowing and `shrinkFontSizeIfNeeded` is enabled: reduce event font by 95% (`shrinkFontSizeFactor`) and retry
5. Uses TCPDF transactions to rollback failed layout attempts

### Day-Spanning Events

When an entry spans multiple days, `CalendarBuilder` duplicates the `CalendarEntry` for each day, setting `continuationEntry=true` and controlling time display via `hideStartTime`/`hideEndTime`. Entries starting in the previous month are handled by checking the prior month's days.

## Key Conventions

- PHP 8.2+ with `declare(strict_types=1)` in all source files
- PSR-4 autoloading: `aschild\` → `src/`
- Tests use Codeception (`Codeception\Test\Unit`) with PHPUnit assertions
- Test output goes to `tests/_output/`
- Single production dependency: `tecnickcom/tcpdf ^6.10.1`
- Week start is configurable: 0=Sunday (default), 1=Monday
- Colors can be specified as named colors (e.g., "red") or HTML hex (e.g., "#ff0000")
