<?php

namespace aschild\PDFCalendarBuilder;

class CalendarEntryTest extends \Codeception\Test\Unit {

    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before() {
        
    }

    protected function _after() {
        
    }

    // tests
    public function testConstructor() {
        
        $now= new \DateTime();
        $e= new CalendarEntry($now, $now, "Message", ColorNames::getColor("black"), ColorNames::html2rgb("white"));
        
    }

}
