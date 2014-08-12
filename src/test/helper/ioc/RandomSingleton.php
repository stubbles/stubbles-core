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
 * Class that is marked as Singleton
 *
 * @Singleton
 */
class RandomSingleton implements Number
{
    /**
     * value of the number
     *
     * @type  int
     */
    private $value;

    /**
     * constructor
     */
    public function __construct()
    {
        srand();
        $this->value = rand(0, 5000);
    }

    /**
     * display a number
     */
    public function display() {
        echo $this->value . "\n";
    }
}
