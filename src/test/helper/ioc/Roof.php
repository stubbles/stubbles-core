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
 * Another helper interface for injection and binding tests.
 */
interface Roof
{
    /**
     * method to open the roof
     */
    public function open();
    /**
     * method to close the roof
     */
    public function close();
}
