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
use stubbles\test\ioc\Goodyear;
/**
 * Test for stubbles\ioc\Injector.
 *
 * @group  ioc
 */
class InjectorBasicTest extends \PHPUnit_Framework_TestCase
{
    /**
     * clean up test environment
     */
    public function tearDown()
    {
        Binder::disableSetterInjection();
    }

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
        Binder::enableSetterInjection();
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
        $binder->bind('stubbles\test\ioc\Tire')
               ->to(new \ReflectionClass('stubbles\test\ioc\Goodyear'));
        $binder->bind('stubbles\test\ioc\Vehicle')
               ->to(new \ReflectionClass('stubbles\test\ioc\Bike'));

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
        Binder::enableSetterInjection();
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
        Binder::enableSetterInjection();
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
        Binder::enableSetterInjection();
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
        $this->assertTrue($injector->hasExplicitBinding('stubbles\test\ioc\ImplicitDependency'));
    }

    /**
     * @test
     */
    public function optionalImplicitDependencyWillNotBeSetIfNotBound()
    {
        $binder   = new Binder();
        $injector = $binder->getInjector();
        $obj      = $injector->getInstance('stubbles\test\ioc\ImplicitOptionalDependency');
        $this->assertNull($obj->getGoodyear());
    }

    /**
     * @test
     */
    public function optionalImplicitDependencyWillBeSetIfBound()
    {
        $binder = new Binder();
        $binder->bind('stubbles\test\ioc\Goodyear')->to('stubbles\test\ioc\Goodyear');
        $injector = $binder->getInjector();
        $obj      = $injector->getInstance('stubbles\test\ioc\ImplicitOptionalDependency');
        $this->assertInstanceOf('stubbles\test\ioc\Goodyear', $obj->getGoodyear());
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
        $injector->getInstance('stubbles\test\ioc\Bike');
    }

    /**
     * @test
     * @expectedException  stubbles\ioc\binding\BindingException
     */
    public function missingConstantBindingOnInjectionHandlingThrowsBindingException()
    {
        $binder   = new Binder();
        $injector = $binder->getInjector();
        $injector->getInstance('stubbles\test\ioc\MissingArrayInjection');
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

    /**
     * @test
     * @since  5.1.0
     */
    public function constructorInjectionWithOptionalSecondParam()
    {
        $binder   = new Binder();
        $binder->bind('stubbles\test\ioc\Tire')->to('stubbles\test\ioc\Goodyear');
        $injector = $binder->getInjector();
        $bike     = $injector->getInstance('stubbles\test\ioc\BikeWithOptionalOtherParam');
        $this->assertEquals('foo', $bike->other);
    }
}
