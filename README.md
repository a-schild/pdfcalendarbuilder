# PDFCalendarBuilder
Generate pdf month calendars with autoscaling/sizing

## Unique features
- The class cann tyr to put everything on one page
- In an normal calendar, all rows have the same height
- This library can shrink/expand rows, so everything fits on one page.
See setResizeRowHeightsIfNeeded(true/false);

- If this is not enough, it can reduce the font size until everything fits on one page.
See setShrinkFontSizeIfNeeded(true/false);

## Usage:
In your composer.json add the dependency:

```
    "require": {
        "php": "^7.3",
        "a-schild/pdfcalendarbuilder": ">=1.0.3",
    }
```
### Creating the class and generate calendar
```
$cal = new aschild\PDFCalendarBuilder\CalendarBuilder(1, 2019, "Calendar title", true, 'mm', 'A4');
$cal->startPDF();
$cal->addEntry($startDate, $endDate, "Entry 1", "#000000", "#fffff");
$cal->buildCalendar();
$cal->Output("calendar.pdf", "I");
```

## Examples
- Empty calendar, no entries, just a month grid
  ![Empty calendar ](doc/img/calendar-empty.png)
- Overflowing boxes in normal libraries
  ![Box overflow in normal calendars](doc/img/calendar-overflow.png)
- Resize row heights to adapt space usage
  ![Resize rows height](doc/img/calendar-resize-row2.png)
- Resize row heights and shrink font size if needed
  ![Resize rows and shrink font](doc/img/calendar-resize-rows-shrink-fontsize.png)
- Day spanning events
  ![Events which span days](doc/img/calendar-day-spanning.png)

(C) 2019 A.Schild

