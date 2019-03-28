# PDFCalendarBuilder
Generate pdf month calendars with autoscaling/sizing

## Unique features
- The class cann tyr to put everything on one page
- In an normal calendar, all rows have the same height
- This library can shrink/expand rows, so everything fits on one page
See setResizeRowHeightsIfNeeded(true/false);

- If this is not enough, it can reduce the font size until everything fits on one page
See setShrinkFontSizeIfNeeded(true/false);

##Usage:

$cal = new PDFCalendarBuilder\CalendarBuilder(1, 2019, "Calendar title", true, 'mm', 'A4');
$cal->startPDF();
$cal->addEntry(10, $startDate, $endDate, "Entry 1", "#000000", "#fffff");
$cal->buildCalendar();
$cal->Output("calendar.pdf", "I");

