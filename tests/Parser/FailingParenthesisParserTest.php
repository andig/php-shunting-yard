<?php

namespace tests\Parser;

use RR\Shunt\Parser;
use RR\Shunt\Exception\RuntimeError;

class FailingParenthesisParserTest extends \PHPUnit\Framework\TestCase
{
    public function testParenthesisThrowsError()
    {
        $this->expectOutputString('');
        $equation = '()';
        return Parser::parse($equation);
    }
}
