<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\test\ioc;
/**
 * Helper class for the test.
 */
class Boss implements Employee
{
    /**
     * says hello
     *
     * @return  string
     */
    public function sayHello()
    {
        return "hello team member";
    }
}
