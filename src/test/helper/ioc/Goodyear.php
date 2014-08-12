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
 * Helper class for injection and binding tests.
 */
class Goodyear implements Tire
{
    /**
     * rotates the tires
     *
     * @return  string
     */
    public function rotate()
    {
        return "I'm driving with Goodyear tires.";
    }
}
