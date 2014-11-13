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
 * Another helper class for injection and binding tests.
 */
class Car implements Vehicle
{
    /**
     * injected tire instance
     *
     * @type  Tire
     */
    public $tire;

    /**
     * Create a new car
     *
     * @param  Tire  $tire
     * @Inject
     */
    public function __construct(Tire $tire)
    {
        $this->tire = $tire;
    }

    /**
     * moves the vehicle forward
     *
     * @return  string
     */
    public function moveForward()
    {
        return $this->tire->rotate();
    }
}
