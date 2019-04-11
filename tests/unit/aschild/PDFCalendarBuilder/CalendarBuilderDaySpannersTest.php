<?php

namespace aschild\PDFCalendarBuilder;

class CalendarBuilderDaySpannersTest extends \Codeception\Test\Unit {

    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before() {
        
    }

    protected function _after() {
        
    }

    // tests
    public function testCalendarSpanner1Entry() {
        $outFile = codecept_output_dir() . "CalendarEntryDaySpanner1.pdf";
        if (file_exists($outFile)) {
            unlink($outFile);
        }

        $cal = new CalendarBuilder(1, 2019, "Spanning 10-12 and 10-15", true, 'mm', "A4");
        $cal->setPrintEndTime(true);
        $cal->startPDF();
        $startDate = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-01-10 11:30:00");
        $endDate = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-01-12 12:30:00");
        $cal->addEntry($startDate, $endDate, "Entry 1", "white", "red");
        $startDate2 = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-01-12 11:15:00");
        $endDate2 = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-01-15 12:30:00");
        $cal->addEntry($startDate2, $endDate2, "Entry 2", "white", "blue");
        $startDate3 = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-01-11 11:15:00");
        $endDate3 = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-01-13 12:30:00");
        $cal->addEntry($startDate3, $endDate3, "Entry 3", "black", "yellow");
        $startDate4 = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-01-8 11:15:00");
        $endDate4 = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-01-12 12:30:00");
        $cal->addEntry($startDate4, $endDate4, "Entry 4", "yellow", "black");
        $cal->buildCalendar();
        $cal->Output($outFile, "F");
        \PHPUnit\Framework\Assert::assertTrue(file_exists($outFile), "Output file missing");
    }

    public function testCalendarSpanner2Entry() {
        $outFile = codecept_output_dir() . "CalendarEntryDaySpanner2.pdf";
        if (file_exists($outFile)) {
            unlink($outFile);
        }

        $cal = new CalendarBuilder(1, 2019, "Spanning 25-31", true, 'mm', "A4");
        $cal->setPrintEndTime(true);
        $cal->startPDF();
        $startDate = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-01-25 11:30:00");
        $endDate = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-01-31 12:30:00");
        $cal->addEntry($startDate, $endDate, "Until end of month", "white", "red");
        $startDate = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-01-28 11:30:00");
        $endDate = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-02-2 12:30:00");
        $cal->addEntry($startDate, $endDate, "Until 2019-02-02", "white", "blue");
        $cal->buildCalendar();
        $cal->Output($outFile, "F");
        \PHPUnit\Framework\Assert::assertTrue(file_exists($outFile), "Output file missing");
    }

    public function testCalendarSpanner3Entry() {
        $outFile = codecept_output_dir() . "CalendarEntryDaySpanner3.pdf";
        if (file_exists($outFile)) {
            unlink($outFile);
        }

        $cal = new CalendarBuilder(1, 2019, "Spanning from prev month", true, 'mm', "A4");
        $cal->setPrintEndTime(true);
        $cal->startPDF();
        $startDate = \DateTime::createFromFormat("Y-m-d H:i:s", "2018-12-25 11:30:00");
        $endDate = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-01-4 12:30:00");
        $cal->addEntry($startDate, $endDate, "From prev month", "white", "red");
        $startDate = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-01-28 11:30:00");
        $endDate = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-02-2 12:30:00");
        $cal->addEntry($startDate, $endDate, "Until 2019-02-02", "white", "blue");
        $cal->buildCalendar();
        $cal->Output($outFile, "F");
        \PHPUnit\Framework\Assert::assertTrue(file_exists($outFile), "Output file missing");
    }
    
}
