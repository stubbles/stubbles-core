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
 * Helper class for optional constructor injection.
 *
 * @since  2.0.0
 */
class BikeWithOptionalTire implements Vehicle
{
    /**
     * injected tire instance
     *
     * @type  Tire
     */
    public $tire;

    /**
     * sets the tire
     *
     * @param  Tire  $tire
     * @Inject(optional=true)
     */
    public function __construct(Tire $tire = null)
    {
        $this->tire = ((null === $tire) ? (new Goodyear()) : ($tire));
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
