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
use function stubbles\lang\__toString;
/**
 * Helper class for the test.
 *
 * @since  3.1.0
 * @deprecated  since 7.0.0, will be removed with 8.0.0
 */
class SomeObject2
{
    /**
     * a property
     *
     * @type  stubObject
     */
    public $baseObject;
    /**
     * a property
     *
     * @type  string
     */
    private $foo = 'bar';

    /**
     * constructor
     *
     * @since  2.0.0
     */
    public function __construct()
    {
        // intentionally empty
    }

    /**
     * Returns a string representation of itself.
     *
     * @return  string
     */
    public function __toString()
    {
        return __toString($this);
    }
}
