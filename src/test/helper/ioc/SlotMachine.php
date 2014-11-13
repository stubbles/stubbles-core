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
     * @param  Number  $number1
     * @param  Number  $number2
     * @Inject
     */
    public function __construct(Number $number1, Number $number2)
    {
        $this->number1 = $number1;
        $this->number2 = $number2;
    }
}
