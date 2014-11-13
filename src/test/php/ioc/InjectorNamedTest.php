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
/**
 * Test for stubbles\ioc with @Named annotation.
 *
 * @group  ioc
 */
class InjectorNamedTest extends \PHPUnit_Framework_TestCase
{
    /**
     * clean up test environment
     */
    public function tearDown()
    {
        Binder::disableSetterInjection();
    }

    /**
     * name based setter injection with single param
     *
     * @test
     */
    public function namedSetterInjectionWithSingleParam()
    {
        $binder = new Binder();
        $binder->bind('stubbles\test\ioc\Employee')->named('schst')->to('stubbles\test\ioc\Boss');
        $binder->bind('stubbles\test\ioc\Employee')->to('stubbles\test\ioc\TeamMember');

        $injector = $binder->getInjector();

        $this->assertTrue($injector->hasBinding('stubbles\test\ioc\Employee', 'schst'));
        $this->assertTrue($injector->hasBinding('stubbles\test\ioc\Employee'));

        $group = $injector->getInstance('stubbles\\test\\ioc\\Developers');

        $this->assertInstanceOf('stubbles\\test\\ioc\\Developers', $group);
        $this->assertInstanceOf('stubbles\test\ioc\Employee', $group->mikey);
        $this->assertInstanceOf('stubbles\test\ioc\TeamMember', $group->mikey);
        $this->assertInstanceOf('stubbles\test\ioc\Employee', $group->schst);
        $this->assertInstanceOf('stubbles\test\ioc\Boss', $group->schst);
    }

    /**
     * name based setter injection with multiple params and one of them is name based
     *
     * @test
     */
    public function namedSetterInjectionWithMultipleParamAndOneNamedParam()
    {
        Binder::enableSetterInjection();
        $binder = new Binder();
        $binder->bind('stubbles\test\ioc\Employee')->named('schst')->to('stubbles\test\ioc\Boss');
        $binder->bind('stubbles\test\ioc\Employee')->to('stubbles\test\ioc\TeamMember');

        $injector = $binder->getInjector();

        $this->assertTrue($injector->hasBinding('stubbles\test\ioc\Employee', 'schst'));
        $this->assertTrue($injector->hasBinding('stubbles\test\ioc\Employee'));

        $group = $injector->getInstance('stubbles\test\ioc\DevelopersMultipleSetterMethodParams');

        $this->assertInstanceOf('stubbles\test\ioc\DevelopersMultipleSetterMethodParams', $group);
        $this->assertInstanceOf('stubbles\test\ioc\Employee', $group->mikey);
        $this->assertInstanceOf('stubbles\test\ioc\TeamMember', $group->mikey);
        $this->assertInstanceOf('stubbles\test\ioc\Employee', $group->schst);
        $this->assertInstanceOf('stubbles\test\ioc\Boss', $group->schst);
    }

    /**
     * name based constructor injection with multiple params and one of them is name based
     *
     * @test
     */
    public function namedConstructorInjectionWithMultipleParamAndOneNamedParam()
    {
        $binder = new Binder();
        $binder->bind('stubbles\test\ioc\Employee')->named('schst')->to('stubbles\test\ioc\Boss');
        $binder->bind('stubbles\test\ioc\Employee')->to('stubbles\test\ioc\TeamMember');

        $injector = $binder->getInjector();

        $this->assertTrue($injector->hasBinding('stubbles\test\ioc\Employee', 'schst'));
        $this->assertTrue($injector->hasBinding('stubbles\test\ioc\Employee'));

        $group = $injector->getInstance('stubbles\test\ioc\DevelopersMultipleConstructorParams');

        $this->assertInstanceOf('stubbles\test\ioc\DevelopersMultipleConstructorParams', $group);
        $this->assertInstanceOf('stubbles\test\ioc\Employee', $group->mikey);
        $this->assertInstanceOf('stubbles\test\ioc\TeamMember', $group->mikey);
        $this->assertInstanceOf('stubbles\test\ioc\Employee', $group->schst);
        $this->assertInstanceOf('stubbles\test\ioc\Boss', $group->schst);
    }

    /**
     * name based setter injection with multiple params and one of them is a named constant
     *
     * @test
     */
    public function namedSetterInjectionWithMultipleParamAndOneNamedConstantParam()
    {
        Binder::enableSetterInjection();
        $binder = new Binder();
        $binder->bindConstant('boss')->to('role:boss');
        $binder->bind('stubbles\test\ioc\Employee')->to('stubbles\test\ioc\TeamMember');

        $injector = $binder->getInjector();

        $this->assertTrue($injector->hasBinding('stubbles\test\ioc\Employee', 'schst'));
        $this->assertTrue($injector->hasBinding('stubbles\test\ioc\Employee'));

        $group = $injector->getInstance('stubbles\test\ioc\DevelopersMultipleSetterMethodParamsWithConstant');

        $this->assertInstanceOf('stubbles\test\ioc\DevelopersMultipleSetterMethodParamsWithConstant', $group);
        $this->assertInstanceOf('stubbles\test\ioc\Employee', $group->schst);
        $this->assertInstanceOf('stubbles\test\ioc\TeamMember', $group->schst);
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
        $binder->bindConstant('boss')->to('role:boss');
        $binder->bind('stubbles\test\ioc\Employee')->to('stubbles\test\ioc\TeamMember');

        $injector = $binder->getInjector();

        $this->assertTrue($injector->hasBinding('stubbles\test\ioc\Employee', 'schst'));
        $this->assertTrue($injector->hasBinding('stubbles\test\ioc\Employee'));

        $group = $injector->getInstance('stubbles\test\ioc\DevelopersMultipleConstructorParamsWithConstant');

        $this->assertInstanceOf('stubbles\test\ioc\DevelopersMultipleConstructorParamsWithConstant', $group);
        $this->assertInstanceOf('stubbles\test\ioc\Employee', $group->schst);
        $this->assertInstanceOf('stubbles\test\ioc\TeamMember', $group->schst);
        $this->assertEquals('role:boss', $group->role);
    }

    /**
     * name based setter injection with multiple params and both are named
     *
     * @test
     */
    public function namedSetterInjectionWithMultipleParamAndNamedParamGroup()
    {
        Binder::enableSetterInjection();
        $binder = new Binder();
        $binder->bind('stubbles\test\ioc\Employee')->named('schst')->to('stubbles\test\ioc\Boss');
        $binder->bind('stubbles\test\ioc\Employee')->to('stubbles\test\ioc\TeamMember');

        $injector = $binder->getInjector();

        $this->assertTrue($injector->hasBinding('stubbles\test\ioc\Employee', 'schst'));
        $this->assertTrue($injector->hasBinding('stubbles\test\ioc\Employee'));

        $group = $injector->getInstance('stubbles\test\ioc\DevelopersMultipleSetterMethodParamsGroupedName');

        $this->assertInstanceOf('stubbles\test\ioc\DevelopersMultipleSetterMethodParamsGroupedName', $group);
        $this->assertInstanceOf('stubbles\test\ioc\Employee', $group->mikey);
        $this->assertInstanceOf('stubbles\test\ioc\Boss', $group->mikey);
        $this->assertInstanceOf('stubbles\test\ioc\Employee', $group->schst);
        $this->assertInstanceOf('stubbles\test\ioc\Boss', $group->schst);
    }

    /**
     * name based constructor injection with multiple params and both are named
     *
     * @test
     */
    public function namedConstructorInjectionWithMultipleParamAndNamedParamGroup()
    {
        $binder = new Binder();
        $binder->bind('stubbles\test\ioc\Employee')->named('schst')->to('stubbles\test\ioc\Boss');
        $binder->bind('stubbles\test\ioc\Employee')->to('stubbles\test\ioc\TeamMember');

        $injector = $binder->getInjector();

        $this->assertTrue($injector->hasBinding('stubbles\test\ioc\Employee', 'schst'));
        $this->assertTrue($injector->hasBinding('stubbles\test\ioc\Employee'));

        $group = $injector->getInstance('stubbles\test\ioc\DevelopersMultipleConstructorParamsGroupedName');

        $this->assertInstanceOf('stubbles\test\ioc\DevelopersMultipleConstructorParamsGroupedName', $group);
        $this->assertInstanceOf('stubbles\test\ioc\Employee', $group->mikey);
        $this->assertInstanceOf('stubbles\test\ioc\Boss', $group->mikey);
        $this->assertInstanceOf('stubbles\test\ioc\Employee', $group->schst);
        $this->assertInstanceOf('stubbles\test\ioc\Boss', $group->schst);
    }
}
