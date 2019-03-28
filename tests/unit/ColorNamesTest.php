<?php

namespace PDFCalendarBuilder;

class ExampleTest extends \Codeception\Test\Unit {

    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before() {
        
    }

    protected function _after() {
        
    }

    public function testGetColor() {
        $expectedResult1 = array("red" => 0xff, "green" => 0xff, "blue" => 0xff);
        $result1= ColorNames::getColor("white");
        \PHPUnit\Framework\Assert::assertTrue(arrays_are_similar($expectedResult1, $result1), "white arrays not identical, expected: ".var_export($expectedResult1, true).' got: '. var_export($result1, true));
        
        $expectedResult2 = array(0xff, 0xff, 0xff);
        $result2= ColorNames::html2rgb("#FFFFFF");
        \PHPUnit\Framework\Assert::assertTrue(arrays_are_similar($expectedResult2, $result2), "#ffffff arrays not identical, expected: ".var_export($expectedResult2, true).' got: '. var_export($result2, true));

        
        $expectedResult3 = array("red" => 0x00, "green" => 0x00, "blue" => 0x00);
        $result3 = ColorNames::getColor("black");
        \PHPUnit\Framework\Assert::assertTrue(arrays_are_similar($expectedResult3, $result3), "black arrays not identical, expected: ".var_export($expectedResult3, true).' got: '. var_export($result3, true));
        
        $expectedResult4 = array(0x0, 0x0, 0x0);
        $result4 = ColorNames::html2rgb("#000000");
        \PHPUnit\Framework\Assert::assertTrue(arrays_are_similar($expectedResult4, $result4), "#000000 arrays not identical, expected: ".var_export($expectedResult4, true).' got: '. var_export($result4, true));

    }

}

/**
 * Determine if two associative arrays are similar
 *
 * Both arrays must have the same indexes with identical values
 * without respect to key ordering 
 * 
 * @param array $a
 * @param array $b
 * @return bool
 */
function arrays_are_similar($a, $b) {
    // if the indexes don't match, return immediately
    if (count(array_diff_assoc($a, $b))) {
        return false;
    }
    // we know that the indexes, but maybe not values, match.
    // compare the values between the two arrays
    foreach ($a as $k => $v) {
        if ($v !== $b[$k]) {
            return false;
        }
    }
    // we have identical indexes, and no unequal values
    return true;
}
