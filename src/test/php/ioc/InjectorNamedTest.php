<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\ioc;
use stubbles\test\ioc\Boss;
use stubbles\test\ioc\DevelopersMultipleConstructorParams;
use stubbles\test\ioc\DevelopersMultipleConstructorParamsGroupedName;
use stubbles\test\ioc\DevelopersMultipleConstructorParamsWithConstant;
use stubbles\test\ioc\Employee;
use stubbles\test\ioc\TeamMember;
/**
 * Test for stubbles\ioc with @Named annotation.
 *
 * @group  ioc
 */
class InjectorNamedTest extends \PHPUnit_Framework_TestCase
{
    /**
     * name based constructor injection with multiple params and one of them is name based
     *
     * @test
     */
    public function namedConstructorInjectionWithMultipleParamAndOneNamedParam()
    {
        $binder = new Binder();
        $binder->bind(Employee::class)->named('schst')->to(Boss::class);
        $binder->bind(Employee::class)->to(TeamMember::class);

        $injector = $binder->getInjector();

        assertTrue($injector->hasBinding(Employee::class, 'schst'));
        assertTrue($injector->hasBinding(Employee::class));

        $group = $injector->getInstance(DevelopersMultipleConstructorParams::class);

        assertInstanceOf(DevelopersMultipleConstructorParams::class, $group);
        assertInstanceOf(Employee::class, $group->mikey);
        assertInstanceOf(TeamMember::class, $group->mikey);
        assertInstanceOf(Employee::class, $group->schst);
        assertInstanceOf(Boss::class, $group->schst);
    }

    /**
     * name based constructor injection with multiple params and one of them is a named constant
     *
     * @test
     */
    public function namedConstructorInjectionWithMultipleParamAndOneNamedConstantParam()
    {
        $binder = new Binder();
        $binder->bindConstant('boss')->to('role:boss');
        $binder->bind(Employee::class)->to(TeamMember::class);

        $injector = $binder->getInjector();

        assertTrue($injector->hasBinding(Employee::class, 'schst'));
        assertTrue($injector->hasBinding(Employee::class));

        $group = $injector->getInstance(DevelopersMultipleConstructorParamsWithConstant::class);

        assertInstanceOf(DevelopersMultipleConstructorParamsWithConstant::class, $group);
        assertInstanceOf(Employee::class, $group->schst);
        assertInstanceOf(TeamMember::class, $group->schst);
        assertEquals('role:boss', $group->role);
    }

    /**
     * name based constructor injection with multiple params and both are named
     *
     * @test
     */
    public function namedConstructorInjectionWithMultipleParamAndNamedParamGroup()
    {
        $binder = new Binder();
        $binder->bind(Employee::class)->named('schst')->to(Boss::class);
        $binder->bind(Employee::class)->to(TeamMember::class);

        $injector = $binder->getInjector();

        assertTrue($injector->hasBinding(Employee::class, 'schst'));
        assertTrue($injector->hasBinding(Employee::class));

        $group = $injector->getInstance(DevelopersMultipleConstructorParamsGroupedName::class);

        assertInstanceOf(DevelopersMultipleConstructorParamsGroupedName::class, $group);
        assertInstanceOf(Employee::class, $group->mikey);
        assertInstanceOf(Boss::class, $group->mikey);
        assertInstanceOf(Employee::class, $group->schst);
        assertInstanceOf(Boss::class, $group->schst);
    }
}
