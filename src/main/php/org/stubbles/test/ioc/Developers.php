<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles
 */
namespace org\stubbles\test\ioc;
/**
 * Helper class for the test.
 */
class Developers
{
    public $mikey;
    public $schst;

    /**
     * Setter method with Named() annotation
     *
     * @param  Employee  $schst
     * @Inject
     * @Named('schst')
     */
    public function setSchst(Employee $schst)
    {
        $this->schst = $schst;
    }

    /**
     * Setter method without Named() annotation
     *
     * @param  Employee  $schst
     * @Inject
     */
    public function setMikey(Employee $mikey)
    {
        $this->mikey = $mikey;
    }
}
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
     * @Inject
     * @Named{boss}('schst')
     */
    public function __construct(Employee $boss, Employee $employee)
    {
        $this->schst = $boss;
        $this->mikey = $employee;
    }
}
/**
 * Helper class for the test.
 */
class DevelopersMultipleSetterMethodParamsWithConstant
{
    public $role;
    public $schst;

    /**
     * setter method with Named() annotation on a specific param
     *
     * @param  Employee  $schst
     * @param  string                            $role
     * @Inject
     * @Named{role}('boss')
     */
    public function setDevelopers(Employee $schst, $role)
    {
        $this->schst = $schst;
        $this->role  = $role;
    }
}
/**
 * Helper class for the test.
 */
class DevelopersMultipleConstructorParamsWithConstant
{
    public $role;
    public $schst;

    /**
     * constructor method with Named() annotation on a specific param
     *
     * @param  Employee  $schst
     * @param  string                            $role
     * @Inject
     * @Named{role}('boss')
     */
    public function __construct(Employee $schst, $role)
    {
        $this->schst = $schst;
        $this->role  = $role;
    }
}
/**
 * Helper class for the test.
 */
class DevelopersMultipleSetterMethodParamsGroupedName
{
    public $mikey;
    public $schst;

    /**
     * setter method with Named() annotation on a specific param
     *
     * @param  Employee  $schst
     * @param  Employee  $employee
     * @Inject
     * @Named('schst')
     */
    public function setDevelopers(Employee $boss, Employee $employee)
    {
        $this->schst = $boss;
        $this->mikey = $employee;
    }
}
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
?>