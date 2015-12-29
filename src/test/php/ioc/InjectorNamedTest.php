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

use function bovigo\assert\assert;
use function bovigo\assert\predicate\equals;
use function bovigo\assert\predicate\isFalse;
use function bovigo\assert\predicate\isTrue;
/**
 * Test for stubbles\ioc with @Named annotation.
 *
 * @group  ioc
 */
class InjectorNamedTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function namedBindingIsKnownWhenSpecified()
    {
        $binder = new Binder();
        $binder->bind(Employee::class)->named('schst')->to(Boss::class);
        $injector = $binder->getInjector();
        assert($injector->hasBinding(Employee::class, 'schst'), isTrue());
    }
    
    /**
     * @test
     */
    public function namedBindingIsNotUsedWhenNoGenericBindingSpecified()
    {
        $binder = new Binder();
        $binder->bind(Employee::class)->named('schst')->to(Boss::class);
        $injector = $binder->getInjector();
        assert($injector->hasBinding(Employee::class), isFalse());
    }

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
        $group = $injector->getInstance(DevelopersMultipleConstructorParams::class);
        assert(
                $group,
                equals(new DevelopersMultipleConstructorParams(
                        new Boss(),
                        new TeamMember()
                ))
        );
    }

    /**
     * @test
     */
    public function namedConstructorInjectionWithMultipleParamAndOneNamedConstantParam()
    {
        $binder = new Binder();
        $binder->bindConstant('boss')->to('role:boss');
        $binder->bind(Employee::class)->to(TeamMember::class);
        $injector = $binder->getInjector();
        $group = $injector->getInstance(DevelopersMultipleConstructorParamsWithConstant::class);
        assert(
                $group,
                equals(new DevelopersMultipleConstructorParamsWithConstant(
                        new TeamMember(),
                        'role:boss'
                ))
        );
    }

    /**
     * @test
     */
    public function namedConstructorInjectionWithMultipleParamAndNamedParamGroup()
    {
        $binder = new Binder();
        $binder->bind(Employee::class)->named('schst')->to(Boss::class);
        $binder->bind(Employee::class)->to(TeamMember::class);
        $injector = $binder->getInjector();
        $group = $injector->getInstance(DevelopersMultipleConstructorParamsGroupedName::class);
        assert(
                $group,
                equals(new DevelopersMultipleConstructorParamsGroupedName(
                        new Boss(),
                        new Boss()
                ))
        );
    }
}
