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
use stubbles\lang\reflect\ReflectionClass;
use stubbles\test\ioc\Bike;
use stubbles\test\ioc\Goodyear;
use stubbles\test\ioc\ImplicitDependencyBug102;
use stubbles\test\ioc\ImplicitOptionalDependency;
use stubbles\test\ioc\MissingArrayInjection;
/**
 * Test for stubbles\ioc\Injector.
 *
 * @group  ioc
 */
class InjectorBasicTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * test constructor injections
     *
     * @test
     */
    public function constructorInjection()
    {
        $binder = new Binder();
        $binder->bind('stubbles\test\ioc\Tire')->to('stubbles\test\ioc\Goodyear');
        $binder->bind('stubbles\test\ioc\Vehicle')->to('stubbles\test\ioc\Car');

        $injector = $binder->getInjector();

        $this->assertTrue($injector->hasBinding('stubbles\test\ioc\Vehicle'));
        $this->assertTrue($injector->hasBinding('stubbles\test\ioc\Tire'));

        $vehicle = $injector->getInstance('stubbles\test\ioc\Vehicle');

        $this->assertInstanceOf('stubbles\test\ioc\Vehicle', $vehicle);
        $this->assertInstanceOf('stubbles\test\ioc\Car', $vehicle);
        $this->assertInstanceOf('stubbles\test\ioc\Tire', $vehicle->tire);
        $this->assertInstanceOf('stubbles\test\ioc\Goodyear', $vehicle->tire);
    }

    /**
     * test setter injections
     *
     * @test
     */
    public function setterInjection()
    {
        $binder = new Binder();
        $binder->bind('stubbles\test\ioc\Tire')->to('stubbles\test\ioc\Goodyear');
        $binder->bind('stubbles\test\ioc\Vehicle')->to('stubbles\test\ioc\Bike');

        $injector = $binder->getInjector();

        $this->assertTrue($injector->hasBinding('stubbles\test\ioc\Vehicle'));
        $this->assertTrue($injector->hasBinding('stubbles\test\ioc\Tire'));

        $vehicle = $injector->getInstance('stubbles\test\ioc\Vehicle');

        $this->assertInstanceOf('stubbles\test\ioc\Vehicle', $vehicle);
        $this->assertInstanceOf('stubbles\test\ioc\Bike', $vehicle);
        $this->assertInstanceOf('stubbles\test\ioc\Tire', $vehicle->tire);
        $this->assertInstanceOf('stubbles\test\ioc\Goodyear', $vehicle->tire);
    }

    /**
     * test setter injections while passing stubReflectionClass instances
     * instead of class names
     *
     * @test
     */
    public function setterInjectionWithClass()
    {
        $binder = new Binder();
        $binder->bind('stubbles\test\ioc\Tire')->to(new ReflectionClass('stubbles\test\ioc\Goodyear'));
        $binder->bind('stubbles\test\ioc\Vehicle')->to(new ReflectionClass('stubbles\test\ioc\Bike'));

        $injector = $binder->getInjector();

        $this->assertTrue($injector->hasBinding('stubbles\test\ioc\Vehicle'));
        $this->assertTrue($injector->hasBinding('stubbles\test\ioc\Tire'));

        $vehicle = $injector->getInstance('stubbles\test\ioc\Vehicle');

        $this->assertInstanceOf('stubbles\test\ioc\Vehicle', $vehicle);
        $this->assertInstanceOf('stubbles\test\ioc\Bike', $vehicle);
        $this->assertInstanceOf('stubbles\test\ioc\Tire', $vehicle->tire);
        $this->assertInstanceOf('stubbles\test\ioc\Goodyear', $vehicle->tire);
    }

    /**
     * test bindings to an invalid type
     *
     * @test
     * @expectedException  stubbles\lang\exception\IllegalArgumentException
     */
    public function setterInjectionWithInvalidArgument()
    {
        $binder = new Binder();
        $binder->bind('stubbles\test\ioc\Vehicle')->to(313);
    }

    /**
     * test bindings to an instance
     *
     * @test
     */
    public function setterInjectionByInstance()
    {
        $tire = new Goodyear();

        $binder = new Binder();
        $binder->bind('stubbles\test\ioc\Tire')->toInstance($tire);
        $binder->bind('stubbles\test\ioc\Vehicle')->to('stubbles\test\ioc\Bike');

        $injector = $binder->getInjector();

        $this->assertTrue($injector->hasBinding('stubbles\test\ioc\Vehicle'));
        $this->assertTrue($injector->hasBinding('stubbles\test\ioc\Tire'));

        $vehicle = $injector->getInstance('stubbles\test\ioc\Vehicle');

        $this->assertInstanceOf('stubbles\test\ioc\Vehicle', $vehicle);
        $this->assertInstanceOf('stubbles\test\ioc\Bike', $vehicle);
        $this->assertInstanceOf('stubbles\test\ioc\Tire', $vehicle->tire);
        $this->assertInstanceOf('stubbles\test\ioc\Goodyear', $vehicle->tire);
        $this->identicalTo($vehicle->tire, $tire);
    }

    /**
     * test bindings to an instance with an invalid type
     *
     * @test
     * @expectedException  stubbles\lang\exception\IllegalArgumentException
     */
    public function setterInjectionByInvalidInstance()
    {
        $tire = new Goodyear();

        $binder = new Binder();
        $binder->bind('stubbles\test\ioc\Vehicle')->toInstance($tire);
    }

    /**
     * test setter injections
     *
     * @test
     */
    public function optionalSetterInjection()
    {
        $binder = new Binder();
        $binder->bind('stubbles\test\ioc\Tire')->to('stubbles\test\ioc\Goodyear');
        $binder->bind('stubbles\test\ioc\Vehicle')->to('stubbles\test\ioc\Convertible');

        $injector = $binder->getInjector();

        $vehicle = $injector->getInstance('stubbles\test\ioc\Vehicle');

        $this->assertInstanceOf('stubbles\test\ioc\Vehicle', $vehicle);
        $this->assertInstanceOf('stubbles\test\ioc\Convertible', $vehicle);

        $this->assertNull($vehicle->roof);
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
        $this->assertFalse($injector->hasExplicitBinding('stubbles\test\ioc\Goodyear'));
        $goodyear = $injector->getInstance('stubbles\test\ioc\Goodyear');
        $this->assertInstanceOf('stubbles\test\ioc\Goodyear', $goodyear);
        $this->assertTrue($injector->hasExplicitBinding('stubbles\test\ioc\Goodyear'));
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
        $this->assertFalse($injector->hasExplicitBinding('stubbles\test\ioc\ImplicitDependency'));
        $obj      = $injector->getInstance('stubbles\test\ioc\ImplicitDependency');
        $this->assertInstanceOf('stubbles\test\ioc\ImplicitDependency', $obj);
        $this->assertInstanceOf('stubbles\test\ioc\Goodyear', $obj->getGoodyearByConstructor());
        $this->assertInstanceOf('stubbles\test\ioc\Goodyear', $obj->getGoodyearBySetter());
        $this->assertTrue($injector->hasExplicitBinding('stubbles\test\ioc\ImplicitDependency'));
    }

    /**
     * test method for bug #102
     *
     * @link  http://stubbles.net/ticket/102
     *
     * @test
     * @group  bug102
     */
    public function bug102()
    {
        $obj      = new ImplicitDependencyBug102();
        $binder   = new Binder();
        $injector = $binder->getInjector();
        $injector->handleInjections($obj);
        $this->assertInstanceOf('stubbles\test\ioc\Goodyear', $obj->getGoodyearBySetter());
    }

    /**
     * @test
     */
    public function optionalImplicitDependencyWillNotBeSetIfNotBound()
    {
        $obj      = new ImplicitOptionalDependency();
        $binder   = new Binder();
        $injector = $binder->getInjector();
        $injector->handleInjections($obj);
        $this->assertNull($obj->getGoodyearBySetter());
    }

    /**
     * @test
     */
    public function optionalImplicitDependencyWillBeSetIfBound()
    {
        $obj    = new ImplicitOptionalDependency();
        $binder = new Binder();
        $binder->bind('stubbles\test\ioc\Goodyear')->to('stubbles\test\ioc\Goodyear');
        $injector = $binder->getInjector();
        $injector->handleInjections($obj);
        $this->assertInstanceOf('stubbles\test\ioc\Goodyear', $obj->getGoodyearBySetter());
    }

    /**
     * @test
     * @expectedException  stubbles\ioc\binding\BindingException
     */
    public function missingBindingThrowsBindingException()
    {
        $binder   = new Binder();
        $injector = $binder->getInjector();
        $injector->getInstance('stubbles\test\ioc\Vehicle');
    }

    /**
     * @test
     * @expectedException  stubbles\ioc\binding\BindingException
     */
    public function missingBindingOnInjectionHandlingThrowsBindingException()
    {
        $binder   = new Binder();
        $injector = $binder->getInjector();
        $class    = new Bike();
        $injector->handleInjections($class);
    }

    /**
     * @test
     * @expectedException  stubbles\ioc\binding\BindingException
     */
    public function missingConstantBindingOnInjectionHandlingThrowsBindingException()
    {
        $binder   = new Binder();
        $injector = $binder->getInjector();
        $injector->handleInjections(new MissingArrayInjection());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function optionalConstructorInjection()
    {
        $binder   = new Binder();
        $injector = $binder->getInjector();
        $bike     = $injector->getInstance('stubbles\test\ioc\BikeWithOptionalTire');
        $this->assertInstanceOf('stubbles\test\ioc\Goodyear', $bike->tire);

    }
}
