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
class DevelopersMultipleSetterMethodParams
{
    public $mikey;
    public $schst;

    /**
     * setter method with Named() annotation on a specific param
     *
     * @param  Employee  $boss
     * @param  Employee  $employee
     * @Inject
     * @Named{boss}('schst')
     */
    public function setDevelopers(Employee $boss, Employee $employee)
    {
        $this->schst = $boss;
        $this->mikey = $employee;
    }
}
