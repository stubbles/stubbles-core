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
 * Interface with annotation.
 *
 * @ImplementedBy(stubbles\test\ioc\Schst.class)
 */
interface Person
{
    /**
     * a method
     */
    public function sayHello();
}
