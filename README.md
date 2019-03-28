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
```
$cal = new PDFCalendarBuilder\CalendarBuilder(1, 2019, "Calendar title", true, 'mm', 'A4');
$cal->startPDF();
$cal->addEntry($startDate, $endDate, "Entry 1", "#000000", "#fffff");
$cal->buildCalendar();
$cal->Output("calendar.pdf", "I");
```

## Examples
- Empty calendar
  ![Empty calendar ](doc/img/calendar-empty.png)
- Overflowing boxes in normal libraries
  ![Box overflow in normal calendars](doc/img/calendar-overflow.png)
- Resize row heights to adapt space usage
  ![Resize rows height](doc/img/calendar-resize-row2.png)
- Resize row heights and shrink font size if needed
  ![Resize rows and schrink font](calendar-resize-rows-shrink-fontsize.png)


