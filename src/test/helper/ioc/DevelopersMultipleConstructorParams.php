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
 * Helper class for the test.
 */
class DevelopersMultipleConstructorParams
{
    public $mikey;
    public $schst;

    /**
     * constructor with Named() annotation on a specific param
     *
     * @param  Employee  $boss
     * @param  Employee  $employee
     * @Named{boss}('schst')
     */
    public function __construct(Employee $boss, Employee $employee)
    {
        $this->schst = $boss;
        $this->mikey = $employee;
    }
}
