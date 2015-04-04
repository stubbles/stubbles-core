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
 * Helper class to test implicit binding related to bug #102.
 */
class ImplicitOptionalDependency
{
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
    public function __construct(Goodyear $goodyear = null)
    {
        $this->goodyearBySetter = $goodyear;
    }

    /**
     * returns the instance from setter injection
     *
     * @return  Goodyear
     */
    public function getGoodyear()
    {
        return $this->goodyearBySetter;
    }
}
