<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles
 */
namespace net\stubbles\ioc;
/**
 * Helper interface for the test.
 */
interface Employee
{
    /**
     * says hello
     *
     * @return  string
     */
    public function sayHello();
}
/**
 * Helper class for the test.
 */
class Boss implements Employee
{
    /**
     * says hello
     *
     * @return  string
     */
    public function sayHello()
    {
        return "hello team member";
    }
}
/**
 * Helper class for the test.
 */
class TeamMember implements Employee
{
    /**
     * says hello
     *
     * @return  string
     */
    public function sayHello()
    {
        return "hello boss";
    }
}
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
/**
 * Test for net\stubbles\ioc\ with @Named annotation.
 *
 * @group  ioc
 */
class InjectorNamedTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * name based setter injection with single param
     *
     * @test
     */
    public function namedSetterInjectionWithSingleParam()
    {
        $binder = new Binder();
        $binder->bind('net\\stubbles\\ioc\\Employee')->named('schst')->to('net\\stubbles\\ioc\\Boss');
        $binder->bind('net\\stubbles\\ioc\\Employee')->to('net\\stubbles\\ioc\\TeamMember');

        $injector = $binder->getInjector();

        $this->assertTrue($injector->hasBinding('net\\stubbles\\ioc\\Employee', 'schst'));
        $this->assertTrue($injector->hasBinding('net\\stubbles\\ioc\\Employee'));

        $group = $injector->getInstance('net\\stubbles\\ioc\\Developers');

        $this->assertInstanceOf('net\\stubbles\\ioc\\Developers', $group);
        $this->assertInstanceOf('net\\stubbles\\ioc\\Employee', $group->mikey);
        $this->assertInstanceOf('net\\stubbles\\ioc\\TeamMember', $group->mikey);
        $this->assertInstanceOf('net\\stubbles\\ioc\\Employee', $group->schst);
        $this->assertInstanceOf('net\\stubbles\\ioc\\Boss', $group->schst);
    }

    /**
     * name based setter injection with multiple params and one of them is name based
     *
     * @test
     */
    public function namedSetterInjectionWithMultipleParamAndOneNamedParam()
    {
        $binder = new Binder();
        $binder->bind('net\\stubbles\\ioc\\Employee')->named('schst')->to('net\\stubbles\\ioc\\Boss');
        $binder->bind('net\\stubbles\\ioc\\Employee')->to('net\\stubbles\\ioc\\TeamMember');

        $injector = $binder->getInjector();

        $this->assertTrue($injector->hasBinding('net\\stubbles\\ioc\\Employee', 'schst'));
        $this->assertTrue($injector->hasBinding('net\\stubbles\\ioc\\Employee'));

        $group = $injector->getInstance('net\\stubbles\\ioc\\DevelopersMultipleSetterMethodParams');

        $this->assertInstanceOf('net\\stubbles\\ioc\\DevelopersMultipleSetterMethodParams', $group);
        $this->assertInstanceOf('net\\stubbles\\ioc\\Employee', $group->mikey);
        $this->assertInstanceOf('net\\stubbles\\ioc\\TeamMember', $group->mikey);
        $this->assertInstanceOf('net\\stubbles\\ioc\\Employee', $group->schst);
        $this->assertInstanceOf('net\\stubbles\\ioc\\Boss', $group->schst);
    }

    /**
     * name based constructor injection with multiple params and one of them is name based
     *
     * @test
     */
    public function namedConstructorInjectionWithMultipleParamAndOneNamedParam()
    {
        $binder = new Binder();
        $binder->bind('net\\stubbles\\ioc\\Employee')->named('schst')->to('net\\stubbles\\ioc\\Boss');
        $binder->bind('net\\stubbles\\ioc\\Employee')->to('net\\stubbles\\ioc\\TeamMember');

        $injector = $binder->getInjector();

        $this->assertTrue($injector->hasBinding('net\\stubbles\\ioc\\Employee', 'schst'));
        $this->assertTrue($injector->hasBinding('net\\stubbles\\ioc\\Employee'));

        $group = $injector->getInstance('net\\stubbles\\ioc\\DevelopersMultipleConstructorParams');

        $this->assertInstanceOf('net\\stubbles\\ioc\\DevelopersMultipleConstructorParams', $group);
        $this->assertInstanceOf('net\\stubbles\\ioc\\Employee', $group->mikey);
        $this->assertInstanceOf('net\\stubbles\\ioc\\TeamMember', $group->mikey);
        $this->assertInstanceOf('net\\stubbles\\ioc\\Employee', $group->schst);
        $this->assertInstanceOf('net\\stubbles\\ioc\\Boss', $group->schst);
    }

    /**
     * name based setter injection with multiple params and one of them is a named constant
     *
     * @test
     */
    public function namedSetterInjectionWithMultipleParamAndOneNamedConstantParam()
    {
        $binder = new Binder();
        $binder->bindConstant()->named('boss')->to('role:boss');
        $binder->bind('net\\stubbles\\ioc\\Employee')->to('net\\stubbles\\ioc\\TeamMember');

        $injector = $binder->getInjector();

        $this->assertTrue($injector->hasBinding('net\\stubbles\\ioc\\Employee', 'schst'));
        $this->assertTrue($injector->hasBinding('net\\stubbles\\ioc\\Employee'));

        $group = $injector->getInstance('net\\stubbles\\ioc\\DevelopersMultipleSetterMethodParamsWithConstant');

        $this->assertInstanceOf('net\\stubbles\\ioc\\DevelopersMultipleSetterMethodParamsWithConstant', $group);
        $this->assertInstanceOf('net\\stubbles\\ioc\\Employee', $group->schst);
        $this->assertInstanceOf('net\\stubbles\\ioc\\TeamMember', $group->schst);
        $this->assertEquals('role:boss', $group->role);
    }

    /**
     * name based constructor injection with multiple params and one of them is a named constant
     *
     * @test
     */
    public function namedConstructorInjectionWithMultipleParamAndOneNamedConstantParam()
    {
        $binder = new Binder();
        $binder->bindConstant()->named('boss')->to('role:boss');
        $binder->bind('net\\stubbles\\ioc\\Employee')->to('net\\stubbles\\ioc\\TeamMember');

        $injector = $binder->getInjector();

        $this->assertTrue($injector->hasBinding('net\\stubbles\\ioc\\Employee', 'schst'));
        $this->assertTrue($injector->hasBinding('net\\stubbles\\ioc\\Employee'));

        $group = $injector->getInstance('net\\stubbles\\ioc\\DevelopersMultipleConstructorParamsWithConstant');

        $this->assertInstanceOf('net\\stubbles\\ioc\\DevelopersMultipleConstructorParamsWithConstant', $group);
        $this->assertInstanceOf('net\\stubbles\\ioc\\Employee', $group->schst);
        $this->assertInstanceOf('net\\stubbles\\ioc\\TeamMember', $group->schst);
        $this->assertEquals('role:boss', $group->role);
    }

    /**
     * name based setter injection with multiple params and both are named
     *
     * @test
     */
    public function namedSetterInjectionWithMultipleParamAndNamedParamGroup()
    {
        $binder = new Binder();
        $binder->bind('net\\stubbles\\ioc\\Employee')->named('schst')->to('net\\stubbles\\ioc\\Boss');
        $binder->bind('net\\stubbles\\ioc\\Employee')->to('net\\stubbles\\ioc\\TeamMember');

        $injector = $binder->getInjector();

        $this->assertTrue($injector->hasBinding('net\\stubbles\\ioc\\Employee', 'schst'));
        $this->assertTrue($injector->hasBinding('net\\stubbles\\ioc\\Employee'));

        $group = $injector->getInstance('net\\stubbles\\ioc\\DevelopersMultipleSetterMethodParamsGroupedName');

        $this->assertInstanceOf('net\\stubbles\\ioc\\DevelopersMultipleSetterMethodParamsGroupedName', $group);
        $this->assertInstanceOf('net\\stubbles\\ioc\\Employee', $group->mikey);
        $this->assertInstanceOf('net\\stubbles\\ioc\\Boss', $group->mikey);
        $this->assertInstanceOf('net\\stubbles\\ioc\\Employee', $group->schst);
        $this->assertInstanceOf('net\\stubbles\\ioc\\Boss', $group->schst);
    }

    /**
     * name based constructor injection with multiple params and both are named
     *
     * @test
     */
    public function namedConstructorInjectionWithMultipleParamAndNamedParamGroup()
    {
        $binder = new Binder();
        $binder->bind('net\\stubbles\\ioc\\Employee')->named('schst')->to('net\\stubbles\\ioc\\Boss');
        $binder->bind('net\\stubbles\\ioc\\Employee')->to('net\\stubbles\\ioc\\TeamMember');

        $injector = $binder->getInjector();

        $this->assertTrue($injector->hasBinding('net\\stubbles\\ioc\\Employee', 'schst'));
        $this->assertTrue($injector->hasBinding('net\\stubbles\\ioc\\Employee'));

        $group = $injector->getInstance('net\\stubbles\\ioc\\DevelopersMultipleConstructorParamsGroupedName');

        $this->assertInstanceOf('net\\stubbles\\ioc\\DevelopersMultipleConstructorParamsGroupedName', $group);
        $this->assertInstanceOf('net\\stubbles\\ioc\\Employee', $group->mikey);
        $this->assertInstanceOf('net\\stubbles\\ioc\\Boss', $group->mikey);
        $this->assertInstanceOf('net\\stubbles\\ioc\\Employee', $group->schst);
        $this->assertInstanceOf('net\\stubbles\\ioc\\Boss', $group->schst);
    }
}
?>