<?php

namespace tests\Parser;

use RR\Shunt\Parser;
use RR\Shunt\Exception\RuntimeError;

class FailingParenthesisParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \RR\Shunt\Exception\RuntimeError
     */
    public function testParenthesisThrowsError()
    {
		$this->expectOutputString(null);
        $equation = '()';
		return Parser::parse($equation);
    }

}
