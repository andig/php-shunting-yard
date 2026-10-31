<?php

namespace tests\Parser;

use PHPUnit\Framework\Attributes\DataProvider;
use RR\Shunt\Parser;
use RR\Shunt\Exception\RuntimeError;

class SimpleParserTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @param $equation
     * @param $expected
     */
    #[DataProvider('simpleEquations')]
    public function testParserWithSimpleEquations($equation, $expected)
    {
        $actual = Parser::parse($equation);

        $this->assertEquals($expected, $actual);
    }

    public static function simpleEquations()
    {
        return array(
            array(
                '2+3',
                5.0,
            ),
            array(
                '2-3',
                -1.0,
            ),
            array(
                '2*3',
                6.0,
            ),
            array(
                '2/3',
                (2/3),
            ),
            array(
                '3 + 4 * 2 / ( 1 - 5 ) ^ 2 ^ 3',
                3.0001220703125,
            ),
            array(
                '3*(3+4)^(1+2)',
                1029.0,
            ),
            array(
                '4^(-2)',
                0.0625,
            ),
            array(
                '4^-2',
                0.0625,
            ),

            // exclamation / not
            array(
                '!1',
                0,
            ),
            array(
                '!0',
                1,
            ),
        );
    }

    public function testDivisionFromZero()
    {
        $this->expectException(\RR\Shunt\Exception\RuntimeError::class);

        $equation = '100/0';

        Parser::parse($equation);
    }

    public function testModulusFromZero()
    {
        $this->expectException(\RR\Shunt\Exception\RuntimeError::class);

        $equation = '100%0';

        Parser::parse($equation);
    }
}
