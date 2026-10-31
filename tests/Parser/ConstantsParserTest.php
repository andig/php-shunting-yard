<?php

namespace tests\Parser;

use PHPUnit\Framework\Attributes\DataProvider;
use RR\Shunt\Context;
use RR\Shunt\Parser;

class ConstantsParserTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @param $equation
     * @param array $constants
     *                         @param $expected
     */
    #[DataProvider('equationAndConstantsProvider')]
    public function testParserWithConstants($equation, array $constants, $expected)
    {
        $context = new Context();

        foreach ($constants as $key => $val) {
            $context->def($key, $val);
        }

        $actual = Parser::parse($equation, $context);

        $this->assertEquals($expected, $actual);
    }

    public static function equationAndConstantsProvider()
    {
        return array(
            array(
                'a+b',
                array(
                    'a' => 4,
                    'b' => 3,
                ),
                (4+3),
            ),
            array(
                '-a',
                array(
                    'a' => 1,
                ),
                (-1),
            ),
            array(
                'a-b',
                array(
                    'a' => 4,
                    'b' => 3,
                ),
                (4-3),
            ),
            array(
                'a-b',
                array(
                    'a' => 3,
                    'b' => 4,
                ),
                (3-4),
            ),
            array(
                'cc*dr',
                array(
                    'cc' => 3,
                    'dr' => 4,
                ),
                (3*4),
            ),
            array(
                'cc/dr',
                array(
                    'cc' => 3,
                    'dr' => 4,
                ),
                (3/4),
            ),
            array(
                'cc^dr',
                array(
                    'cc' => 3,
                    'dr' => 4,
                ),
                pow(3, 4),
            ),
            array(
                'a+b-c^d',
                array(
                    'a' => 1,
                    'b' => 3,
                    'c' => 8,
                    'd' => 4,
                ),
                (1+3-pow(8, 4))
            ),
            array(
                '2(a+b)^c',
                array(
                    'a' => 3,
                    'b' => 1,
                    'c' => -2,
                ),
                0.125
            ),
        );
    }

    public function testParserWithStringConstants()
    {
        $context = new Context();
        $const = 'string constant';
        $context->def('const', $const, 'string');

        $actual = Parser::parse('const', $context);

        $this->assertEquals($const, $actual);
    }
}
