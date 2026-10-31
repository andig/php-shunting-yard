<?php

namespace tests;

use PHPUnit\Framework\Attributes\DataProvider;
use RR\Shunt\Scanner;
use RR\Shunt\Token;
use RR\Shunt\Exception\SyntaxError;

class ScannerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @param $term
     * @param $expected
     */
    #[DataProvider('terms')]
    public function testScanner($term, $expected)
    {
        $scanner = new Scanner($term);

        $tokens = array();

        while (($token = $scanner->next()) !== false) {
            $tokens[] = $token;
        }

        $this->assertEquals($expected, $tokens);
    }

    public static function terms()
    {
        return array(
            // Simple math
            array(
                '2+3',
                array(
                    new Token(1, 2.0),
                    new Token(65, '+'),
                    new Token(1, 3.0),
                ),
            ),
            array(
                '2-3',
                array(
                    new Token(1, 2.0),
                    new Token(66, '-'),
                    new Token(1, 3.0),
                ),
            ),
            array(
                '2*3',
                array(
                    new Token(1, 2.0),
                    new Token(67, '*'),
                    new Token(1, 3.0),
                ),
            ),
            array(
                '2/3',
                array(
                    new Token(1, 2.0),
                    new Token(68, '/'),
                    new Token(1, 3.0),
                ),
            ),
            array(
                '2%3',
                array(
                    new Token(1, 2.0),
                    new Token(69, '%'),
                    new Token(1, 3.0),
                ),
            ),
            array(
                '2^3',
                array(
                    new Token(1, 2.0),
                    new Token(70, '^'),
                    new Token(1, 3.0),
                ),
            ),

            // floats
            array(
                '2.13+3.78',
                array(
                    new Token(1, 2.13),
                    new Token(65, '+'),
                    new Token(1, 3.78),
                ),
            ),
            array(
                '2.13-3.78',
                array(
                    new Token(1, 2.13),
                    new Token(66, '-'),
                    new Token(1, 3.78),
                ),
            ),
            array(
                '2.13*3.78',
                array(
                    new Token(1, 2.13),
                    new Token(67, '*'),
                    new Token(1, 3.78),
                ),
            ),
            array(
                '2.13/3.78',
                array(
                    new Token(1, 2.13),
                    new Token(68, '/'),
                    new Token(1, 3.78),
                ),
            ),
            array(
                '2.13%3.78',
                array(
                    new Token(1, 2.13),
                    new Token(69, '%'),
                    new Token(1, 3.78),
                ),
            ),
            array(
                '2.13^3.78',
                array(
                    new Token(1, 2.13),
                    new Token(70, '^'),
                    new Token(1, 3.78),
                ),
            ),
            array(
                '2.^.78',
                array(
                    new Token(1, 2.0),
                    new Token(70, '^'),
                    new Token(1, 0.78),
                ),
            ),

            // parenthesis
            array(
                '3*(2+3)',
                array(
                    new Token(1, 3.0),
                    new Token(67, '*'),
                    new Token(8, '('),
                    new Token(1, 2.0),
                    new Token(65, '+'),
                    new Token(1, 3.0),
                    new Token(16, ')'),
                ),
            ),
            array(
                '(2+3)*3',
                array(
                    new Token(8, '('),
                    new Token(1, 2.0),
                    new Token(65, '+'),
                    new Token(1, 3.0),
                    new Token(16, ')'),
                    new Token(67, '*'),
                    new Token(1, 3.0),
                ),
            ),
            array(
                '(2+3)^3',
                array(
                    new Token(8, '('),
                    new Token(1, 2.0),
                    new Token(65, '+'),
                    new Token(1, 3.0),
                    new Token(16, ')'),
                    new Token(70, '^'),
                    new Token(1, 3.0),
                ),
            ),
            array(
                '(2+3)*3^3',
                array(
                    new Token(8, '('),
                    new Token(1, 2.0),
                    new Token(65, '+'),
                    new Token(1, 3.0),
                    new Token(16, ')'),
                    new Token(67, '*'),
                    new Token(1, 3.0),
                    new Token(70, '^'),
                    new Token(1, 3.0),
                ),
            ),

            // sign
            array(
                '+2+3',
                array(
                    new Token(71, '+'),
                    new Token(1, 2.0),
                    new Token(65, '+'),
                    new Token(1, 3.0),
                ),
            ),
            array(
                '-2+3',
                array(
                    new Token(72, '-'),
                    new Token(1, 2.0),
                    new Token(65, '+'),
                    new Token(1, 3.0),
                ),
            ),

            // sign in parenthesis
            array(
                '2+(+3)',
                array(
                    new Token(1, 2.0),
                    new Token(65, '+'),
                    new Token(8, '('),
                    new Token(71, '+'),
                    new Token(1, 3.0),
                    new Token(16, ')'),
                ),
            ),
            array(
                '2+(-3)',
                array(
                    new Token(1, 2.0),
                    new Token(65, '+'),
                    new Token(8, '('),
                    new Token(72, '-'),
                    new Token(1, 3.0),
                    new Token(16, ')'),
                ),
            ),

            // variables
            array(
                'a+3',
                array(
                    new Token(2, 'a'),
                    new Token(65, '+'),
                    new Token(1, 3.0),
                ),
            ),
            array(
                'aa+3',
                array(
                    new Token(2, 'aa'),
                    new Token(65, '+'),
                    new Token(1, 3.0),
                ),
            ),

            // no operator sign
            array(
                '2(2)',
                array(
                    new Token(1, 2.0),
                    new Token(67, '*'),
                    new Token(8, '('),
                    new Token(1, 2.0),
                    new Token(16, ')'),
                ),
            ),
            array(
                '(2)(2)',
                array(
                    new Token(8, '('),
                    new Token(1, 2.0),
                    new Token(16, ')'),
                    new Token(67, '*'),
                    new Token(8, '('),
                    new Token(1, 2.0),
                    new Token(16, ')'),
                ),
            ),
            array(
                '((((2))))(2)',
                array(
                    new Token(8, '('),
                    new Token(8, '('),
                    new Token(8, '('),
                    new Token(8, '('),
                    new Token(1, 2.0),
                    new Token(16, ')'),
                    new Token(16, ')'),
                    new Token(16, ')'),
                    new Token(16, ')'),
                    new Token(67, '*'),
                    new Token(8, '('),
                    new Token(1, 2.0),
                    new Token(16, ')'),
                ),
            ),
            array(
                '()',
                array(
                    new Token(8, '('),
                    new Token(16, ')'),
                ),
            ),

            // functions
            array(
                '3+func(-6)',
                array(
                    new Token(1, 3.0),
                    new Token(65, '+'),
                    new Token(4, 'func'),
                    new Token(8, '('),
                    new Token(72, '-'),
                    new Token(1, 6.0),
                    new Token(16, ')'),
                ),
            ),
            array(
                '3+func(-6,6)',
                array(
                    new Token(1, 3.0),
                    new Token(65, '+'),
                    new Token(4, 'func'),
                    new Token(8, '('),
                    new Token(72, '-'),
                    new Token(1, 6.0),
                    new Token(32, ','),
                    new Token(1, 6.0),
                    new Token(16, ')'),
                ),
            ),

            // functions and variables
            array(
                'var1+func(var2,var3)',
                array(
                    new Token(2, 'var1'),
                    new Token(65, '+'),
                    new Token(4, 'func'),
                    new Token(8, '('),
                    new Token(2, 'var2'),
                    new Token(32, ','),
                    new Token(2, 'var3'),
                    new Token(16, ')'),
                ),
            ),
        );
    }

    public function testForSyntaxErrorExceptionWithWrongInput()
    {
        $this->expectException(\RR\Shunt\Exception\SyntaxError::class);

        $term = '2~2';

        $scanner = new Scanner($term);
    }
}
