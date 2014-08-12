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
 * One more helper class for the test.
 */
class SlotMachine
{
    /**
     * selected number 1
     *
     * @type  Number
     */
    public $number1;
    /**
     * selected number 2
     *
     * @type  Number
     */
    public $number2;

    /**
     * Set number 1
     *
     * @param  Number  $number
     * @Inject
     */
    public function setNumber1(Number $number)
    {
        $this->number1 = $number;
    }

    /**
     * Set number 2
     *
     * @param  Number  $number
     * @Inject
     */
    public function setNumber2(Number $number)
    {
        $this->number2 = $number;
    }
}
