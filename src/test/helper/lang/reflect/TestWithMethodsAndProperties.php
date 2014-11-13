<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\test\lang\reflect;
/**
 * Class to test stubbles\lang\reflect.
 */
class TestWithMethodsAndProperties extends TestWithOutMethodsAndProperties implements TestInterface
{
    /**
     * some public property
     *
     * @type  mixed
     */
    public $property1;
    /**
     * some protected property
     *
     * @type  mixed
     */
    protected $property2;
    /**
     * some private property
     *
     * @type  mixed
     */
    private $property3;

    /**
     * constructor
     */
    public function __construct()
    {
        // intentionally empty
    }

    /**
     * a public method
     */
    public function methodA()
    {
        // intentionally empty
    }

    /**
     * a protected method
     */
    protected function methodB()
    {
        // intentionally empty
    }

    /**
     * a private method
     */
    private function methodC()
    {
        // intentionally empty
    }
}
