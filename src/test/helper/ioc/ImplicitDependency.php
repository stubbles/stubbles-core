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
 * Helper class to test implicit binding with concrete class names.
 */
class ImplicitDependency
{
    /**
     * instance from constructor injection
     *
     * @type  Goodyear
     */
    protected $goodyearByConstructor;
    /**
     * instance from setter injection
     *
     * @type  Goodyear
     */
    protected $goodyearBySetter;

    /**
     * constructor
     *
     * @param  Goodyear  $goodyear
     * @Inject
     */
    public function __construct(Goodyear $goodyear)
    {
        $this->goodyearByConstructor = $goodyear;
    }

    /**
     * setter
     *
     * @param  Goodyear  $goodyear
     * @Inject
     */
    public function setGoodyear(Goodyear $goodyear)
    {
        $this->goodyearBySetter = $goodyear;
    }

    /**
     * returns the instance from constructor injection
     *
     * @return  Goodyear
     */
    public function getGoodyearByConstructor()
    {
        return $this->goodyearByConstructor;
    }

    /**
     * returns the instance from setter injection
     *
     * @return  Goodyear
     */
    public function getGoodyearBySetter()
    {
        return $this->goodyearBySetter;
    }
}
