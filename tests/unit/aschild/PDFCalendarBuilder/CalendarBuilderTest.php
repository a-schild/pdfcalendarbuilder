<?php

namespace aschild\PDFCalendarBuilder;

class CalendarBuilderTest extends \Codeception\Test\Unit {

    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before() {
        
    }

    protected function _after() {
        
    }

    // tests
    public function testEmptyCalendar() {
        $outFile= codecept_output_dir()."EmptyCalendar.pdf";
        if (file_exists($outFile))
        {
            unlink($outFile);
        }

        $cal = new CalendarBuilder(1, 2019, "Title", true, 'mm', "A4");
        $cal->startPDF();
        $cal->buildCalendar();
        $cal->Output(codecept_output_dir()."EmptyCalendar.pdf", "F");
        \PHPUnit\Framework\Assert::assertTrue(file_exists($outFile), "Outpt file missing");
    }

}
