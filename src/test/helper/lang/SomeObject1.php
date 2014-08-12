<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\test\lang;
/**
 * Helper class for the test.
 *
 * @since  3.1.0
 */
class SomeObject1
{
    /**
     * a property
     *
     * @type  int
     */
    protected $bar = 5;

    /**
     * Returns a string representation of itself.
     *
     * @return  string
     */
    public function __toString()
    {
        return \stubbles\lang\__toString($this);
    }
}
