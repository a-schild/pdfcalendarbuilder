<?php

namespace aschild\PDFCalendarBuilder;

class CalendarBuilderMultiMonthTest extends \Codeception\Test\Unit {

    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before() {
        
    }

    protected function _after() {
        
    }

    // tests
    public function testEmptyCalendarUS() {
        $outFile = codecept_output_dir() . "EmptyCalendar_US-2Months.pdf";
        if (file_exists($outFile)) {
            unlink($outFile);
        }

        $cal = new CalendarBuilder(1, 2019, "Title", true, 'mm', "A4");
        $cal->setWeekStarts(0); //US
        $cal->startPDF();
        $cal->buildCalendar();
        $cal->addMonth(2, 2019, "Title");
        $cal->buildCalendar();
        $cal->Output($outFile, "F");
        \PHPUnit\Framework\Assert::assertTrue(file_exists($outFile), "Output file missing");
    }

    public function testEmptyCalendarEU() {
        $outFile = codecept_output_dir() . "EmptyCalendar_EU-2Months.pdf";
        if (file_exists($outFile)) {
            unlink($outFile);
        }

        $cal = new CalendarBuilder(1, 2019, "Title", true, 'mm', "A4");
        $cal->setWeekStarts(1); //EU
        $cal->startPDF();
        $cal->buildCalendar();
        $cal->addMonth(2, 2019, "Title");
        $cal->buildCalendar();
        $cal->Output($outFile, "F");
        \PHPUnit\Framework\Assert::assertTrue(file_exists($outFile), "Output file missing");
    }

    public function testEmptyNoMargingsCalendar() {
        $outFile = codecept_output_dir() . "EmptyNoMargingsCalendar-2Months.pdf";
        if (file_exists($outFile)) {
            unlink($outFile);
        }

        $cal = new CalendarBuilder(1, 2019, "Title", true, 'mm', "A4");
        $cal->setMargins(0, 0, 0, 0);
        $cal->startPDF();
        $cal->buildCalendar();
        $cal->addMonth(2, 2019, "Title");
        $cal->buildCalendar();
        $cal->Output($outFile, "F");
        \PHPUnit\Framework\Assert::assertTrue(file_exists($outFile), "Output file missing");
    }

    public function testCalendar1Entry() {
        $outFile = codecept_output_dir() . "Calendar1Entry-2Months.pdf";
        if (file_exists($outFile)) {
            unlink($outFile);
        }

        $cal = new CalendarBuilder(1, 2019, "Title", true, 'mm', "A4");
        $cal->startPDF();
        $startDate = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-01-10 11:00:00");
        $endDate = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-01-10 12:00:00");
        $cal->addEntry($startDate, $endDate, "Only hours", "white", "red");
        $cal->buildCalendar();
        $cal->addMonth(2, 2019, "Title");
        $startDate = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-02-10 11:00:00");
        $endDate = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-02-10 12:00:00");
        $cal->addEntry($startDate, $endDate, "Only hours", "white", "red");
        $cal->buildCalendar();
        $cal->Output($outFile, "F");
        \PHPUnit\Framework\Assert::assertTrue(file_exists($outFile), "Output file missing");
    }
    
    public function testCalendar1EntryPortrait() {
        $outFile = codecept_output_dir() . "Calendar1EntryPortrait-2Months.pdf";
        if (file_exists($outFile)) {
            unlink($outFile);
        }

        $cal = new CalendarBuilder(1, 2019, "Title", false, 'mm', "A4");
        $cal->startPDF();
        $startDate = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-01-10 11:00:00");
        $endDate = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-01-10 12:00:00");
        $cal->addEntry($startDate, $endDate, "Only hours", "white", "red");
        $cal->buildCalendar();
        $cal->addMonth(2, 2019, "Title");
        $startDate = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-02-10 11:00:00");
        $endDate = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-02-10 12:00:00");
        $cal->addEntry($startDate, $endDate, "Only hours", "white", "red");
        $cal->buildCalendar();
        $cal->Output($outFile, "F");
        \PHPUnit\Framework\Assert::assertTrue(file_exists($outFile), "Output file missing");
    }

    public function testExpandOneRowEntry() {
        $outFile = codecept_output_dir() . "ExpandOneRowEntry-2Months.pdf";
        if (file_exists($outFile)) {
            unlink($outFile);
        }

        $cal = new CalendarBuilder(1, 2019, "ExpandOneRowEntry", true, 'mm', "A4");
        $cal->setPrintEndTime(true);
        $cal->setResizeRowHeightsIfNeeded(true);
        $cal->setShrinkFontSizeIfNeeded(false);
        $cal->startPDF();
        $startDate = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-01-10 11:00:00");
        $endDate = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-01-10 12:00:00");
        $cal->addEntry($startDate, $endDate, "Only hours long text", "white", "red");
        $startDate2 = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-01-10 11:00:00");
        $endDate2 = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-01-10 12:10:00");
        $cal->addEntry($startDate2, $endDate2, "With minutes long text", "white", "blue");
        $startDate3 = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-01-10 13:10:00");
        $endDate3 = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-01-10 14:00:00");
        $cal->addEntry($startDate3, $endDate3, "With minutes long text", "white", "blue");
        $startDate4 = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-01-10 14:00:00");
        $endDate4 = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-01-10 14:30:00");
        $cal->addEntry($startDate4, $endDate4, "With minutes long text", "white", "blue");
        $cal->buildCalendar();
        $cal->addMonth(2, 2019, "ExpandOneRowEntry");
        $startDate = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-02-10 11:00:00");
        $endDate = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-02-10 12:00:00");
        $cal->addEntry($startDate, $endDate, "Only hours long text", "white", "red");
        $startDate2 = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-02-10 11:00:00");
        $endDate2 = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-02-10 12:10:00");
        $cal->addEntry($startDate2, $endDate2, "With minutes long text", "white", "blue");
        $startDate3 = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-02-10 13:10:00");
        $endDate3 = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-02-10 14:00:00");
        $cal->addEntry($startDate3, $endDate3, "With minutes long text", "white", "blue");
        $startDate4 = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-02-10 14:00:00");
        $endDate4 = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-02-10 14:30:00");
        $cal->addEntry($startDate4, $endDate4, "With minutes long text", "white", "blue");
        $cal->buildCalendar();
        $cal->Output($outFile, "F");
        \PHPUnit\Framework\Assert::assertTrue(file_exists($outFile), "Output file missing");
    }

    public function testResizeRowsAndShrinkFontSize() {
        $outFile = codecept_output_dir() . "ResizeRowAndShrinkFontSize-2Months.pdf";
        if (file_exists($outFile)) {
            unlink($outFile);
        }

        $cal = new CalendarBuilder(1, 2019, "ResizeRowAndShrinkFontSize", true, 'mm', "A4");
        $cal->setPrintEndTime(true);
        $cal->setResizeRowHeightsIfNeeded(true);
        $cal->setShrinkFontSizeIfNeeded(true);
        $cal->startPDF();

        $hours = array(2, 4, 8, 12, 16);
        foreach ($hours as $hour) {
            $startDate = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-01-10 $hour:00:00");
            $endDate = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-01-10 $hour:15:00");
            $cal->addEntry($startDate, $endDate, "Entry with very long long long long text", "white", "red");
        }

        $days = array(2, 9, 16, 23, 30);
        foreach ($days as $day) {
            $startDate = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-01-$day 11:00:00");
            $endDate = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-01-$day 12:00:00");
            $cal->addEntry($startDate, $endDate, "Only hours", "white", "red");
            $startDate2 = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-01-$day 11:15:00");
            $endDate2 = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-01-$day 12:30:00");
            $cal->addEntry($startDate2, $endDate2, "With minutes", "white", "blue");
            $startDate3 = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-01-$day 13:15:00");
            $endDate3 = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-01-$day 13:30:00");
            $cal->addEntry($startDate3, $endDate3, "With minutes", "white", "blue");
            $startDate4 = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-01-$day 14:15:00");
            $endDate4 = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-01-$day 14:30:00");
            $cal->addEntry($startDate4, $endDate4, "With minutes", "white", "blue");
        }
        $cal->buildCalendar();
        $cal->addMonth(2, 2019, "ResizeRowAndShrinkFontSize");
        $days = array(2, 9, 16, 23, 28);
        foreach ($hours as $hour) {
            $startDate = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-02-10 $hour:00:00");
            $endDate = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-02-10 $hour:15:00");
            $cal->addEntry($startDate, $endDate, "Entry with very long long long long text", "white", "red");
        }

        foreach ($days as $day) {
            $startDate = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-02-$day 11:00:00");
            $endDate = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-02-$day 12:00:00");
            $cal->addEntry($startDate, $endDate, "Only hours", "white", "red");
            $startDate2 = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-02-$day 11:15:00");
            $endDate2 = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-02-$day 12:30:00");
            $cal->addEntry($startDate2, $endDate2, "With minutes", "white", "blue");
            $startDate3 = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-02-$day 13:15:00");
            $endDate3 = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-02-$day 13:30:00");
            $cal->addEntry($startDate3, $endDate3, "With minutes", "white", "blue");
            $startDate4 = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-02-$day 14:15:00");
            $endDate4 = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-02-$day 14:30:00");
            $cal->addEntry($startDate4, $endDate4, "With minutes", "white", "blue");
        }
        $cal->buildCalendar();
        $cal->Output($outFile, "F");
        \PHPUnit\Framework\Assert::assertTrue(file_exists($outFile), "Output file missing");
    }

    public function testResizeRowsPortrait() {
        $outFile = codecept_output_dir() . "ResizeRowsPortrait-2Months.pdf";
        if (file_exists($outFile)) {
            unlink($outFile);
        }

        $cal = new CalendarBuilder(1, 2019, "ResizeRowsPortrait", false, 'mm', "A4");
        $cal->setPrintEndTime(true);
        $cal->setResizeRowHeightsIfNeeded(true);
        $cal->setShrinkFontSizeIfNeeded(true);
        $cal->startPDF();

        $hours = array( 4, 8, 12, 16);
        foreach ($hours as $hour) {
            $startDate = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-01-10 $hour:00:00");
            $endDate = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-01-10 $hour:15:00");
            $cal->addEntry($startDate, $endDate, "Entry with very long long long long text", "white", "red");
        }

        $days = array(2, 9, 16, 23, 30);
        foreach ($days as $day) {
            $startDate = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-01-$day 11:00:00");
            $endDate = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-01-$day 12:00:00");
            $cal->addEntry($startDate, $endDate, "Only hours", "white", "red");
            $startDate2 = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-01-$day 11:15:00");
            $endDate2 = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-01-$day 12:30:00");
            $cal->addEntry($startDate2, $endDate2, "With minutes", "white", "blue");
        }
        $days = array(1, 2, 23, 30);
        foreach ($days as $day) {
            $startDate = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-01-$day 11:00:00");
            $endDate = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-01-$day 12:00:00");
            $cal->addEntry($startDate, $endDate, "Only hours", "white", "red");
        }
        $cal->buildCalendar();
        $cal->addMonth(2, 2019, "ResizeRowsPortrait");
        foreach ($hours as $hour) {
            $startDate = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-02-10 $hour:00:00");
            $endDate = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-02-10 $hour:15:00");
            $cal->addEntry($startDate, $endDate, "Entry with very long long long long text", "white", "red");
        }

        $days = array(2, 9, 16, 23, 28);
        foreach ($days as $day) {
            $startDate = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-02-$day 11:00:00");
            $endDate = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-02-$day 12:00:00");
            $cal->addEntry($startDate, $endDate, "Only hours", "white", "red");
            $startDate2 = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-02-$day 11:15:00");
            $endDate2 = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-02-$day 12:30:00");
            $cal->addEntry($startDate2, $endDate2, "With minutes", "white", "blue");
        }
        $days = array(1, 2, 23, 28);
        foreach ($days as $day) {
            $startDate = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-02-$day 11:00:00");
            $endDate = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-02-$day 12:00:00");
            $cal->addEntry($startDate, $endDate, "Only hours", "white", "red");
        }
        $cal->buildCalendar();
        $cal->Output($outFile, "F");
        \PHPUnit\Framework\Assert::assertTrue(file_exists($outFile), "Output file missing");
    }
    
    public function testShrinkFontSize() {
        $outFile = codecept_output_dir() . "ShrinkFontSize-2Months.pdf";
        if (file_exists($outFile)) {
            unlink($outFile);
        }

        $cal = new CalendarBuilder(1, 2019, "Shrink font size", true, 'mm', "A4");
        $cal->setPrintEndTime(true);
        $cal->setResizeRowHeightsIfNeeded(false);
        $cal->setShrinkFontSizeIfNeeded(true);
        $cal->startPDF();

        $hours = array(2, 4, 8, 12, 16);
        foreach ($hours as $hour) {
            $startDate = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-01-10 $hour:00:00");
            $endDate = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-01-10 $hour:15:00");
            $cal->addEntry($startDate, $endDate, "Entry with very long long long long text", "white", "red");
        }

        $days = array(2, 9, 16, 23, 30);
        foreach ($days as $day) {
            $startDate = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-01-$day 11:00:00");
            $endDate = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-01-$day 12:00:00");
            $cal->addEntry($startDate, $endDate, "Only hours", "white", "red");
            $startDate2 = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-01-$day 11:15:00");
            $endDate2 = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-01-$day 12:30:00");
            $cal->addEntry($startDate2, $endDate2, "With minutes", "white", "blue");
            $startDate3 = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-01-$day 13:15:00");
            $endDate3 = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-01-$day 13:30:00");
            $cal->addEntry($startDate3, $endDate3, "With minutes", "white", "blue");
            $startDate4 = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-01-$day 14:15:00");
            $endDate4 = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-01-$day 14:30:00");
            $cal->addEntry($startDate4, $endDate4, "With minutes", "white", "blue");
        }
        $cal->buildCalendar();
        $cal->addMonth(2, 2019, "Shrink font size ");
        $hours = array(2, 4, 8, 12, 16);
        foreach ($hours as $hour) {
            $startDate = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-02-10 $hour:00:00");
            $endDate = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-02-10 $hour:15:00");
            $cal->addEntry($startDate, $endDate, "Entry with very long long long long text", "white", "red");
        }

        $days = array(2, 9, 16, 23, 28);
        foreach ($days as $day) {
            $startDate = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-02-$day 11:00:00");
            $endDate = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-02-$day 12:00:00");
            $cal->addEntry($startDate, $endDate, "Only hours", "white", "red");
            $startDate2 = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-02-$day 11:15:00");
            $endDate2 = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-02-$day 12:30:00");
            $cal->addEntry($startDate2, $endDate2, "With minutes", "white", "blue");
            $startDate3 = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-02-$day 13:15:00");
            $endDate3 = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-02-$day 13:30:00");
            $cal->addEntry($startDate3, $endDate3, "With minutes", "white", "blue");
            $startDate4 = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-02-$day 14:15:00");
            $endDate4 = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-02-$day 14:30:00");
            $cal->addEntry($startDate4, $endDate4, "With minutes", "white", "blue");
        }
        $cal->buildCalendar();
        $cal->Output($outFile, "F");
        \PHPUnit\Framework\Assert::assertTrue(file_exists($outFile), "Output file missing");
    }

    public function testBoxOverflows() {
        $outFile = codecept_output_dir() . "BoxOverflow-2Months.pdf";
        if (file_exists($outFile)) {
            unlink($outFile);
        }

        $cal = new CalendarBuilder(1, 2019, "Boxes overflow since no resize allowed", true, 'mm', "A4");
        $cal->setPrintEndTime(true);
        $cal->setResizeRowHeightsIfNeeded(false);
        $cal->setShrinkFontSizeIfNeeded(false);
        $cal->startPDF();

        $hours = array(2, 4, 8, 12, 16);
        foreach ($hours as $hour) {
            $startDate = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-01-10 $hour:00:00");
            $endDate = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-01-10 $hour:15:00");
            $cal->addEntry($startDate, $endDate, "Entry with very long long long long text", "white", "red");
        }

        $days = array(2, 9, 16, 23, 30);
        foreach ($days as $day) {
            $startDate = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-01-$day 11:00:00");
            $endDate = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-01-$day 12:00:00");
            $cal->addEntry($startDate, $endDate, "Only hours", "white", "red");
            $startDate2 = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-01-$day 11:15:00");
            $endDate2 = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-01-$day 12:30:00");
            $cal->addEntry($startDate2, $endDate2, "With minutes", "white", "blue");
            $startDate3 = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-01-$day 13:15:00");
            $endDate3 = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-01-$day 13:30:00");
            $cal->addEntry($startDate3, $endDate3, "With minutes", "white", "blue");
            $startDate4 = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-01-$day 14:15:00");
            $endDate4 = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-01-$day 14:30:00");
            $cal->addEntry($startDate4, $endDate4, "With minutes", "white", "blue");
        }
        $cal->buildCalendar();
        $cal->addMonth(2, 2019, "Boxes overflow since no resize allowed");
        $hours = array(2, 4, 8, 12, 16);
        foreach ($hours as $hour) {
            $startDate = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-02-10 $hour:00:00");
            $endDate = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-02-10 $hour:15:00");
            $cal->addEntry($startDate, $endDate, "Entry with very long long long long text", "white", "red");
        }

        $days = array(2, 9, 16, 23, 28);
        foreach ($days as $day) {
            $startDate = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-02-$day 11:00:00");
            $endDate = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-02-$day 12:00:00");
            $cal->addEntry($startDate, $endDate, "Only hours", "white", "red");
            $startDate2 = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-02-$day 11:15:00");
            $endDate2 = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-02-$day 12:30:00");
            $cal->addEntry($startDate2, $endDate2, "With minutes", "white", "blue");
            $startDate3 = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-02-$day 13:15:00");
            $endDate3 = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-02-$day 13:30:00");
            $cal->addEntry($startDate3, $endDate3, "With minutes", "white", "blue");
            $startDate4 = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-02-$day 14:15:00");
            $endDate4 = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-02-$day 14:30:00");
            $cal->addEntry($startDate4, $endDate4, "With minutes", "white", "blue");
        }
        $cal->buildCalendar();
        $cal->Output($outFile, "F");
        \PHPUnit\Framework\Assert::assertTrue(file_exists($outFile), "Output file missing");
    }
    
    public function testCalendarCategories() {
        $outFile = codecept_output_dir() . "CalendarCategories-2Months.pdf";
        if (file_exists($outFile)) {
            unlink($outFile);
        }

        $cal = new CalendarBuilder(1, 2019, "Calendar with categories", true, 'mm', "A4");
        $cal->setPrintEndTime(true);
        $cal->startPDF();
        
        $cal->addCategory("IDby", "Blue-Yellow category", "blue", "yellow");
        $cal->addCategory("IDyb", "Yellow-Blue category", "yellow", "blue");
        $cal->printCategories();
        
        $startDate = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-01-10 11:30:00");
        $endDate = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-01-10 12:30:00");
        $cal->addEntryCategory($startDate, $endDate, "Only hours", "IDby");
        $startDate2 = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-01-10 11:15:00");
        $endDate2 = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-01-10 12:30:00");
        $cal->addEntryCategory($startDate2, $endDate2, "With minutes", "IDyb");
        $cal->buildCalendar();
        $cal->addMonth(2, 2019, "Calendar with categories");
        $cal->printCategories();
        $startDate = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-02-10 11:30:00");
        $endDate = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-02-10 12:30:00");
        $cal->addEntryCategory($startDate, $endDate, "Only hours", "IDby");
        $startDate2 = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-02-10 11:15:00");
        $endDate2 = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-02-10 12:30:00");
        $cal->addEntryCategory($startDate2, $endDate2, "With minutes", "IDyb");
        $cal->buildCalendar();
        $cal->Output($outFile, "F");
        \PHPUnit\Framework\Assert::assertTrue(file_exists($outFile), "Output file missing");
    }

    public function testResizeRowsAndShrinkFontSizeDaySpanners() {
        $outFile = codecept_output_dir() . "ResizeRowAndShrinkFontSizeDaySpanners-2Months.pdf";
        if (file_exists($outFile)) {
            unlink($outFile);
        }

        $cal = new CalendarBuilder(1, 2019, "Resize rows and shrink font size", true, 'mm', "A4");
        $cal->setPrintEndTime(true);
        $cal->setResizeRowHeightsIfNeeded(true);
        $cal->setShrinkFontSizeIfNeeded(true);
        $cal->startPDF();

        $hours = array(2, 4, 8, 12, 16);
        foreach ($hours as $hour) {
            $startDate = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-01-10 $hour:00:00");
            $endDate = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-01-18 $hour:15:00");
            $cal->addEntry($startDate, $endDate, "Entry with very long long long long text", "black", "yellow");
        }

        $days = array(2, 9, 16, 23, 30);
        foreach ($days as $day) {
            $startDate = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-01-$day 11:00:00");
            $endDate = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-01-$day 12:00:00");
            $cal->addEntry($startDate, $endDate, "Only hours", "white", "red");
            $startDate2 = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-01-$day 11:15:00");
            $endDate2 = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-01-$day 12:30:00");
            $cal->addEntry($startDate2, $endDate2, "With minutes", "white", "blue");
            $startDate3 = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-01-$day 13:15:00");
            $endDate3 = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-01-$day 13:30:00");
            $cal->addEntry($startDate3, $endDate3, "With minutes", "white", "blue");
            $startDate4 = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-01-$day 14:15:00");
            $endDate4 = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-01-$day 14:30:00");
            $cal->addEntry($startDate4, $endDate4, "With minutes", "white", "blue");
        }
        $cal->buildCalendar();
        $cal->addMonth(2, 2019, "Resize rows and shrink font size");
        $hours = array(2, 4, 8, 12, 16);
        foreach ($hours as $hour) {
            $startDate = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-02-10 $hour:00:00");
            $endDate = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-02-18 $hour:15:00");
            $cal->addEntry($startDate, $endDate, "Entry with very long long long long text", "black", "yellow");
        }

        $days = array(2, 9, 16, 23, 28);
        foreach ($days as $day) {
            $startDate = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-02-$day 11:00:00");
            $endDate = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-02-$day 12:00:00");
            $cal->addEntry($startDate, $endDate, "Only hours", "white", "red");
            $startDate2 = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-02-$day 11:15:00");
            $endDate2 = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-02-$day 12:30:00");
            $cal->addEntry($startDate2, $endDate2, "With minutes", "white", "blue");
            $startDate3 = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-02-$day 13:15:00");
            $endDate3 = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-02-$day 13:30:00");
            $cal->addEntry($startDate3, $endDate3, "With minutes", "white", "blue");
            $startDate4 = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-02-$day 14:15:00");
            $endDate4 = \DateTime::createFromFormat("Y-m-d H:i:s", "2019-02-$day 14:30:00");
            $cal->addEntry($startDate4, $endDate4, "With minutes", "white", "blue");
        }
        $cal->buildCalendar();
        $cal->Output($outFile, "F");
        \PHPUnit\Framework\Assert::assertTrue(file_exists($outFile), "Output file missing");
    }
}
