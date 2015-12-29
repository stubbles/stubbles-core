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
use stubbles\test\ioc\Bike;
use stubbles\test\ioc\BikeWithOptionalOtherParam;
use stubbles\test\ioc\BikeWithOptionalTire;
use stubbles\test\ioc\Car;
use stubbles\test\ioc\Goodyear;
use stubbles\test\ioc\ImplicitDependency;
use stubbles\test\ioc\ImplicitOptionalDependency;
use stubbles\test\ioc\MissingArrayInjection;
use stubbles\test\ioc\Tire;
use stubbles\test\ioc\Vehicle;

use function bovigo\assert\assert;
use function bovigo\assert\assertFalse;
use function bovigo\assert\assertNull;
use function bovigo\assert\assertTrue;
use function bovigo\assert\predicate\equals;
use function bovigo\assert\predicate\isInstanceOf;
/**
 * Test for stubbles\ioc\Injector.
 *
 * @group  ioc
 */
class InjectorBasicTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function injectorHasBindingsWhenSpecified()
    {
        $injector = Binder::createInjector(
                function(Binder $binder)
                {
                    $binder->bind(Tire::class)->to(Goodyear::class);
                    $binder->bind(Vehicle::class)->to(Car::class);
                }
        );
        assertTrue($injector->hasBinding(Vehicle::class));
        assertTrue($injector->hasBinding(Tire::class));
    }

    /**
     * @test
     */
    public function constructorInjection()
    {
        $injector = Binder::createInjector(
                function(Binder $binder)
                {
                    $binder->bind(Tire::class)->to(Goodyear::class);
                    $binder->bind(Vehicle::class)->to(Car::class);
                }
        );
        assert(
                $injector->getInstance(Vehicle::class),
                equals(new Car(new Goodyear()))
        );
    }

    /**
     * @test
     */
    public function doesNotHaveExplicitBindingWhenNotDefined()
    {
        $injector = Binder::createInjector();
        assertFalse($injector->hasExplicitBinding(Goodyear::class));
    }

    /**
     * @test
     */
    public function usesImplicitBindingViaTypehints()
    {
        $goodyear = Binder::createInjector()->getInstance(Goodyear::class);
        assert($goodyear, isInstanceOf(Goodyear::class));
    }

    /**
     * @test
     */
    public function implicitBindingTurnsIntoExplicitBindingAfterFirstUsage()
    {
        $injector = Binder::createInjector();
        $injector->getInstance(Goodyear::class);
        assertTrue($injector->hasExplicitBinding(Goodyear::class));
    }

    /**
     * @test
     */
    public function implicitBindingAsDependency()
    {
        $injector = Binder::createInjector();
        $obj      = $injector->getInstance(ImplicitDependency::class);
        assert($obj->getGoodyearByConstructor(), isInstanceOf(Goodyear::class));
    }

    /**
     * @test
     */
    public function optionalImplicitDependencyWillNotBeSetIfNotBound()
    {
        $injector = Binder::createInjector();
        $obj      = $injector->getInstance(ImplicitOptionalDependency::class);
        assertNull($obj->getGoodyear());
    }

    /**
     * @test
     */
    public function optionalImplicitDependencyWillBeSetIfBound()
    {
        $injector = Binder::createInjector(
                function(Binder $binder)
                {
                    $binder->bind(Goodyear::class)->to(Goodyear::class);
                }
        );
        $obj      = $injector->getInstance(ImplicitOptionalDependency::class);
        assert($obj->getGoodyear(), isInstanceOf(Goodyear::class));
    }

    /**
     * @test
     * @expectedException  stubbles\ioc\binding\BindingException
     */
    public function missingBindingThrowsBindingException()
    {
        $injector = Binder::createInjector();
        $injector->getInstance(Vehicle::class);
    }

    /**
     * @test
     * @expectedException  stubbles\ioc\binding\BindingException
     */
    public function missingBindingOnInjectionHandlingThrowsBindingException()
    {
        $injector = Binder::createInjector();
        $injector->getInstance(Bike::class);
    }

    /**
     * @test
     * @expectedException  stubbles\ioc\binding\BindingException
     */
    public function missingConstantBindingOnInjectionHandlingThrowsBindingException()
    {
        $injector = Binder::createInjector();
        $injector->getInstance(MissingArrayInjection::class);
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function optionalConstructorInjection()
    {
        $injector = Binder::createInjector();
        $bike     = $injector->getInstance(BikeWithOptionalTire::class);
        assert($bike->tire, isInstanceOf(Goodyear::class));
    }

    /**
     * @test
     * @since  5.1.0
     */
    public function constructorInjectionWithOptionalSecondParam()
    {
        $injector = Binder::createInjector(
                function(Binder $binder)
                {
                    $binder->bind(Tire::class)->to(Goodyear::class);
                }
        );
        $bike = $injector->getInstance(BikeWithOptionalOtherParam::class);
        assert($bike->other, equals('foo'));
    }
}
