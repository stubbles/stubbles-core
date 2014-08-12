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
 * An alternative implementation.
 */
class Mikey implements Person, Person2
{
    /**
     * a method
     */
    public function sayHello()
    {
        return "My name is mikey.";
    }

    public function sayHello2()
    {
        return 'My name is still mikey.';
    }
}
