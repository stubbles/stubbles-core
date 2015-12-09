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
/**
 * Test for stubbles\ioc\Injector.
 *
 * @group  ioc
 */
class InjectorBasicTest extends \PHPUnit_Framework_TestCase
{
    /**
     * test constructor injections
     *
     * @test
     */
    public function constructorInjection()
    {
        $binder = new Binder();
        $binder->bind(Tire::class)->to(Goodyear::class);
        $binder->bind(Vehicle::class)->to(Car::class);

        $injector = $binder->getInjector();

        assertTrue($injector->hasBinding(Vehicle::class));
        assertTrue($injector->hasBinding(Tire::class));

        $vehicle = $injector->getInstance(Vehicle::class);

        assertInstanceOf(Vehicle::class, $vehicle);
        assertInstanceOf(Car::class, $vehicle);
        assertInstanceOf(Tire::class, $vehicle->tire);
        assertInstanceOf(Goodyear::class, $vehicle->tire);
    }

    /**
     * test implicit bindings
     *
     * @test
     */
    public function implicitBinding()
    {
        $binder   = new Binder();
        $injector = $binder->getInjector();
        assertFalse($injector->hasExplicitBinding(Goodyear::class));
        $goodyear = $injector->getInstance(Goodyear::class);
        assertInstanceOf(Goodyear::class, $goodyear);
        assertTrue($injector->hasExplicitBinding(Goodyear::class));
    }

    /**
     * test implicit bindings as a dependency
     *
     * @test
     */
    public function implicitBindingAsDependency()
    {
        $binder   = new Binder();
        $injector = $binder->getInjector();
        assertFalse($injector->hasExplicitBinding(ImplicitDependency::class));
        $obj      = $injector->getInstance(ImplicitDependency::class);
        assertInstanceOf(ImplicitDependency::class, $obj);
        assertInstanceOf(Goodyear::class, $obj->getGoodyearByConstructor());
        assertTrue($injector->hasExplicitBinding(ImplicitDependency::class));
    }

    /**
     * @test
     */
    public function optionalImplicitDependencyWillNotBeSetIfNotBound()
    {
        $binder   = new Binder();
        $injector = $binder->getInjector();
        $obj      = $injector->getInstance(ImplicitOptionalDependency::class);
        assertNull($obj->getGoodyear());
    }

    /**
     * @test
     */
    public function optionalImplicitDependencyWillBeSetIfBound()
    {
        $binder = new Binder();
        $binder->bind(Goodyear::class)->to(Goodyear::class);
        $injector = $binder->getInjector();
        $obj      = $injector->getInstance(ImplicitOptionalDependency::class);
        assertInstanceOf(Goodyear::class, $obj->getGoodyear());
    }

    /**
     * @test
     * @expectedException  stubbles\ioc\binding\BindingException
     */
    public function missingBindingThrowsBindingException()
    {
        $binder   = new Binder();
        $injector = $binder->getInjector();
        $injector->getInstance(Vehicle::class);
    }

    /**
     * @test
     * @expectedException  stubbles\ioc\binding\BindingException
     */
    public function missingBindingOnInjectionHandlingThrowsBindingException()
    {
        $binder   = new Binder();
        $injector = $binder->getInjector();
        $injector->getInstance(Bike::class);
    }

    /**
     * @test
     * @expectedException  stubbles\ioc\binding\BindingException
     */
    public function missingConstantBindingOnInjectionHandlingThrowsBindingException()
    {
        $binder   = new Binder();
        $injector = $binder->getInjector();
        $injector->getInstance(MissingArrayInjection::class);
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function optionalConstructorInjection()
    {
        $binder   = new Binder();
        $injector = $binder->getInjector();
        $bike     = $injector->getInstance(BikeWithOptionalTire::class);
        assertInstanceOf(Goodyear::class, $bike->tire);
    }

    /**
     * @test
     * @since  5.1.0
     */
    public function constructorInjectionWithOptionalSecondParam()
    {
        $binder   = new Binder();
        $binder->bind(Tire::class)->to(Goodyear::class);
        $injector = $binder->getInjector();
        $bike     = $injector->getInstance(BikeWithOptionalOtherParam::class);
        assertEquals('foo', $bike->other);
    }
}
