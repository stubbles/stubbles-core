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
 * Test for net\stubbles\ioc with @Named annotation.
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
        $binder->bind('org\\stubbles\\test\\ioc\\Employee')->named('schst')->to('org\\stubbles\\test\\ioc\\Boss');
        $binder->bind('org\\stubbles\\test\\ioc\\Employee')->to('org\\stubbles\\test\\ioc\\TeamMember');

        $injector = $binder->getInjector();

        $this->assertTrue($injector->hasBinding('org\\stubbles\\test\\ioc\\Employee', 'schst'));
        $this->assertTrue($injector->hasBinding('org\\stubbles\\test\\ioc\\Employee'));

        $group = $injector->getInstance('org\\stubbles\\test\\ioc\\Developers');

        $this->assertInstanceOf('org\\stubbles\\test\\ioc\\Developers', $group);
        $this->assertInstanceOf('org\\stubbles\\test\\ioc\\Employee', $group->mikey);
        $this->assertInstanceOf('org\\stubbles\\test\\ioc\\TeamMember', $group->mikey);
        $this->assertInstanceOf('org\\stubbles\\test\\ioc\\Employee', $group->schst);
        $this->assertInstanceOf('org\\stubbles\\test\\ioc\\Boss', $group->schst);
    }

    /**
     * name based setter injection with multiple params and one of them is name based
     *
     * @test
     */
    public function namedSetterInjectionWithMultipleParamAndOneNamedParam()
    {
        $binder = new Binder();
        $binder->bind('org\\stubbles\\test\\ioc\\Employee')->named('schst')->to('org\\stubbles\\test\\ioc\\Boss');
        $binder->bind('org\\stubbles\\test\\ioc\\Employee')->to('org\\stubbles\\test\\ioc\\TeamMember');

        $injector = $binder->getInjector();

        $this->assertTrue($injector->hasBinding('org\\stubbles\\test\\ioc\\Employee', 'schst'));
        $this->assertTrue($injector->hasBinding('org\\stubbles\\test\\ioc\\Employee'));

        $group = $injector->getInstance('org\\stubbles\\test\\ioc\\DevelopersMultipleSetterMethodParams');

        $this->assertInstanceOf('org\\stubbles\\test\\ioc\\DevelopersMultipleSetterMethodParams', $group);
        $this->assertInstanceOf('org\\stubbles\\test\\ioc\\Employee', $group->mikey);
        $this->assertInstanceOf('org\\stubbles\\test\\ioc\\TeamMember', $group->mikey);
        $this->assertInstanceOf('org\\stubbles\\test\\ioc\\Employee', $group->schst);
        $this->assertInstanceOf('org\\stubbles\\test\\ioc\\Boss', $group->schst);
    }

    /**
     * name based constructor injection with multiple params and one of them is name based
     *
     * @test
     */
    public function namedConstructorInjectionWithMultipleParamAndOneNamedParam()
    {
        $binder = new Binder();
        $binder->bind('org\\stubbles\\test\\ioc\\Employee')->named('schst')->to('org\\stubbles\\test\\ioc\\Boss');
        $binder->bind('org\\stubbles\\test\\ioc\\Employee')->to('org\\stubbles\\test\\ioc\\TeamMember');

        $injector = $binder->getInjector();

        $this->assertTrue($injector->hasBinding('org\\stubbles\\test\\ioc\\Employee', 'schst'));
        $this->assertTrue($injector->hasBinding('org\\stubbles\\test\\ioc\\Employee'));

        $group = $injector->getInstance('org\\stubbles\\test\\ioc\\DevelopersMultipleConstructorParams');

        $this->assertInstanceOf('org\\stubbles\\test\\ioc\\DevelopersMultipleConstructorParams', $group);
        $this->assertInstanceOf('org\\stubbles\\test\\ioc\\Employee', $group->mikey);
        $this->assertInstanceOf('org\\stubbles\\test\\ioc\\TeamMember', $group->mikey);
        $this->assertInstanceOf('org\\stubbles\\test\\ioc\\Employee', $group->schst);
        $this->assertInstanceOf('org\\stubbles\\test\\ioc\\Boss', $group->schst);
    }

    /**
     * name based setter injection with multiple params and one of them is a named constant
     *
     * @test
     */
    public function namedSetterInjectionWithMultipleParamAndOneNamedConstantParam()
    {
        $binder = new Binder();
        $binder->bindConstant('boss')->to('role:boss');
        $binder->bind('org\\stubbles\\test\\ioc\\Employee')->to('org\\stubbles\\test\\ioc\\TeamMember');

        $injector = $binder->getInjector();

        $this->assertTrue($injector->hasBinding('org\\stubbles\\test\\ioc\\Employee', 'schst'));
        $this->assertTrue($injector->hasBinding('org\\stubbles\\test\\ioc\\Employee'));

        $group = $injector->getInstance('org\\stubbles\\test\\ioc\\DevelopersMultipleSetterMethodParamsWithConstant');

        $this->assertInstanceOf('org\\stubbles\\test\\ioc\\DevelopersMultipleSetterMethodParamsWithConstant', $group);
        $this->assertInstanceOf('org\\stubbles\\test\\ioc\\Employee', $group->schst);
        $this->assertInstanceOf('org\\stubbles\\test\\ioc\\TeamMember', $group->schst);
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
        $binder->bind('org\\stubbles\\test\\ioc\\Employee')->to('org\\stubbles\\test\\ioc\\TeamMember');

        $injector = $binder->getInjector();

        $this->assertTrue($injector->hasBinding('org\\stubbles\\test\\ioc\\Employee', 'schst'));
        $this->assertTrue($injector->hasBinding('org\\stubbles\\test\\ioc\\Employee'));

        $group = $injector->getInstance('org\\stubbles\\test\\ioc\\DevelopersMultipleConstructorParamsWithConstant');

        $this->assertInstanceOf('org\\stubbles\\test\\ioc\\DevelopersMultipleConstructorParamsWithConstant', $group);
        $this->assertInstanceOf('org\\stubbles\\test\\ioc\\Employee', $group->schst);
        $this->assertInstanceOf('org\\stubbles\\test\\ioc\\TeamMember', $group->schst);
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
        $binder->bind('org\\stubbles\\test\\ioc\\Employee')->named('schst')->to('org\\stubbles\\test\\ioc\\Boss');
        $binder->bind('org\\stubbles\\test\\ioc\\Employee')->to('org\\stubbles\\test\\ioc\\TeamMember');

        $injector = $binder->getInjector();

        $this->assertTrue($injector->hasBinding('org\\stubbles\\test\\ioc\\Employee', 'schst'));
        $this->assertTrue($injector->hasBinding('org\\stubbles\\test\\ioc\\Employee'));

        $group = $injector->getInstance('org\\stubbles\\test\\ioc\\DevelopersMultipleSetterMethodParamsGroupedName');

        $this->assertInstanceOf('org\\stubbles\\test\\ioc\\DevelopersMultipleSetterMethodParamsGroupedName', $group);
        $this->assertInstanceOf('org\\stubbles\\test\\ioc\\Employee', $group->mikey);
        $this->assertInstanceOf('org\\stubbles\\test\\ioc\\Boss', $group->mikey);
        $this->assertInstanceOf('org\\stubbles\\test\\ioc\\Employee', $group->schst);
        $this->assertInstanceOf('org\\stubbles\\test\\ioc\\Boss', $group->schst);
    }

    /**
     * name based constructor injection with multiple params and both are named
     *
     * @test
     */
    public function namedConstructorInjectionWithMultipleParamAndNamedParamGroup()
    {
        $binder = new Binder();
        $binder->bind('org\\stubbles\\test\\ioc\\Employee')->named('schst')->to('org\\stubbles\\test\\ioc\\Boss');
        $binder->bind('org\\stubbles\\test\\ioc\\Employee')->to('org\\stubbles\\test\\ioc\\TeamMember');

        $injector = $binder->getInjector();

        $this->assertTrue($injector->hasBinding('org\\stubbles\\test\\ioc\\Employee', 'schst'));
        $this->assertTrue($injector->hasBinding('org\\stubbles\\test\\ioc\\Employee'));

        $group = $injector->getInstance('org\\stubbles\\test\\ioc\\DevelopersMultipleConstructorParamsGroupedName');

        $this->assertInstanceOf('org\\stubbles\\test\\ioc\\DevelopersMultipleConstructorParamsGroupedName', $group);
        $this->assertInstanceOf('org\\stubbles\\test\\ioc\\Employee', $group->mikey);
        $this->assertInstanceOf('org\\stubbles\\test\\ioc\\Boss', $group->mikey);
        $this->assertInstanceOf('org\\stubbles\\test\\ioc\\Employee', $group->schst);
        $this->assertInstanceOf('org\\stubbles\\test\\ioc\\Boss', $group->schst);
    }
}
?>