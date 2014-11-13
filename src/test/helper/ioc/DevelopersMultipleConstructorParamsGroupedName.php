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
class DevelopersMultipleConstructorParamsGroupedName
{
    public $mikey;
    public $schst;

    /**
     * constructor method with Named() annotation on a specific param
     *
     * @param  Employee  $schst
     * @param  Employee  $employee
     * @Inject
     * @Named('schst')
     */
    public function __construct(Employee $boss, Employee $employee)
    {
        $this->schst = $boss;
        $this->mikey = $employee;
    }
}
