<?php

/*!
 * PHP Shunting-yard Implementation
 * Copyright 2012 - droptable <murdoc@raidrush.org>
 *
 * PHP 5.3 required
 *
 * Reference: <http://en.wikipedia.org/wiki/Shunting-yard_algorithm>
 *
 * ----------------------------------------------------------------
 *
 * Permission is hereby granted, free of charge, to any person obtaining a
 * copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without
 * limitation the rights to use, copy, modify, merge, publish, distribute,
 * sublicense, and/or sell copies of the Software, and to permit persons to
 * whom the Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included
 * in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
 * INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR
 * PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS
 * BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT,
 * TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
 * OTHER DEALINGS IN THE SOFTWARE.
 *
 * <http://opensource.org/licenses/mit-license.php>
 */

namespace RR\Shunt;

use Exception;
use RR\Shunt\Exception\RuntimeError;

class Context
{
    protected $functions = array();
    protected $constants = array('PI' => M_PI, 'π' => M_PI);
    protected $operatorHandlers = array();

    /**
     * Call a user-defined custom function and returns the result
     *
     * @param $name The name of the function
     * @param array $args The arguments to pass to the function
     * @return float The result returned from the function
     * @throws RuntimeError
     */
    public function fn($name, array $args)
    {
        if (!isset($this->functions[$name])) {
            throw new RuntimeError('run-time error: undefined function "' . $name . '"');
        }

        return (float) call_user_func_array($this->functions[$name], $args);
    }

    /**
     * Returns the value of a custom-defined constant
     *
     * @param $name
     * @return mixed
     * @throws RuntimeError
     */
    public function cs($name)
    {
        if (!isset($this->constants[$name])) {
            throw new RuntimeError('run-time error: undefined constant "' . $name . '"');
        }

        return $this->constants[$name];
    }

    /**
     * @param $op Operator integer value (as defined in Token)
     * @param $lhsValue The left-hand side operand
     * @param $rhsValue The right-hand side operand
     * @return float
     * @throws RuntimeError
     */
    public function execCustomOperatorHandler($op, $lhsValue, $rhsValue)
    {
        if (!isset($this->operatorHandlers[$op])) {
            throw new RuntimeError('run-time error: undefined operator handler "' . $op . '"');
        }

        return call_user_func_array($this->operatorHandlers[$op], array($lhsValue, $rhsValue));
    }

    /**
     * Define a custom constant or a function
     *
     * @param $name name of the constant or function
     * @param mixed $value
     * @param string $type
     * @throws \Exception
     */
    public function def($name, $value = null, $type = 'float')
    {
        // wrapper for simple PHP functions
        if ($value === null) {
            $value = $name;
        }

        if (is_callable($value) && $type == 'float') {
            $this->functions[$name] = $value;
        } elseif (is_numeric($value) && $type == 'float') {
            $this->constants[$name] = (float) $value;
        } elseif (is_string($value) && $type == 'string') {
            $this->constants[$name] = $value;
        } else {
            throw new Exception('function or number expected');
        }
    }

    /**
     * Register custom handler function for an operator
     */
    public function defOperator($operator, callable $func)
    {
        if ($operator & Token::T_OPERATOR == 0) {
            throw new Exception('unsupported operator');
        }
        $this->operatorHandlers[$operator] = $func;
    }

    /**
     * Check whether there is a custom handler defined for an operator
     *
     * @param int $operator
     * @return bool
     */
    public function hasCustomOperatorHandler($operator)
    {
        return isset($this->operatorHandlers[$operator]);
    }
}
