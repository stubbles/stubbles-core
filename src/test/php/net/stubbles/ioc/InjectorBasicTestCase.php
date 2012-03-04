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
use net\stubbles\lang\reflect\ReflectionClass;
use org\stubbles\test\ioc\Bike;
use org\stubbles\test\ioc\Goodyear;
use org\stubbles\test\ioc\ImplicitDependencyBug102;
use org\stubbles\test\ioc\ImplicitOptionalDependency;
use org\stubbles\test\ioc\MissingArrayInjection;
/**
 * Test for net\stubbles\ioc\Injector.
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
        $binder->bind('org\\stubbles\\test\\ioc\\Tire')->to('org\\stubbles\\test\\ioc\\Goodyear');
        $binder->bind('org\\stubbles\\test\\ioc\\Vehicle')->to('org\\stubbles\\test\\ioc\\Car');

        $injector = $binder->getInjector();

        $this->assertTrue($injector->hasBinding('org\\stubbles\\test\\ioc\\Vehicle'));
        $this->assertTrue($injector->hasBinding('org\\stubbles\\test\\ioc\\Tire'));

        $vehicle = $injector->getInstance('org\\stubbles\\test\\ioc\\Vehicle');

        $this->assertInstanceOf('org\\stubbles\\test\\ioc\\Vehicle', $vehicle);
        $this->assertInstanceOf('org\\stubbles\\test\\ioc\\Car', $vehicle);
        $this->assertInstanceOf('org\\stubbles\\test\\ioc\\Tire', $vehicle->tire);
        $this->assertInstanceOf('org\\stubbles\\test\\ioc\\Goodyear', $vehicle->tire);
    }

    /**
     * test setter injections
     *
     * @test
     */
    public function setterInjection()
    {
        $binder = new Binder();
        $binder->bind('org\\stubbles\\test\\ioc\\Tire')->to('org\\stubbles\\test\\ioc\\Goodyear');
        $binder->bind('org\\stubbles\\test\\ioc\\Vehicle')->to('org\\stubbles\\test\\ioc\\Bike');

        $injector = $binder->getInjector();

        $this->assertTrue($injector->hasBinding('org\\stubbles\\test\\ioc\\Vehicle'));
        $this->assertTrue($injector->hasBinding('org\\stubbles\\test\\ioc\\Tire'));

        $vehicle = $injector->getInstance('org\\stubbles\\test\\ioc\\Vehicle');

        $this->assertInstanceOf('org\\stubbles\\test\\ioc\\Vehicle', $vehicle);
        $this->assertInstanceOf('org\\stubbles\\test\\ioc\\Bike', $vehicle);
        $this->assertInstanceOf('org\\stubbles\\test\\ioc\\Tire', $vehicle->tire);
        $this->assertInstanceOf('org\\stubbles\\test\\ioc\\Goodyear', $vehicle->tire);
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
        $binder->bind('org\\stubbles\\test\\ioc\\Tire')->to(new ReflectionClass('org\\stubbles\\test\\ioc\\Goodyear'));
        $binder->bind('org\\stubbles\\test\\ioc\\Vehicle')->to(new ReflectionClass('org\\stubbles\\test\\ioc\\Bike'));

        $injector = $binder->getInjector();

        $this->assertTrue($injector->hasBinding('org\\stubbles\\test\\ioc\\Vehicle'));
        $this->assertTrue($injector->hasBinding('org\\stubbles\\test\\ioc\\Tire'));

        $vehicle = $injector->getInstance('org\\stubbles\\test\\ioc\\Vehicle');

        $this->assertInstanceOf('org\\stubbles\\test\\ioc\\Vehicle', $vehicle);
        $this->assertInstanceOf('org\\stubbles\\test\\ioc\\Bike', $vehicle);
        $this->assertInstanceOf('org\\stubbles\\test\\ioc\\Tire', $vehicle->tire);
        $this->assertInstanceOf('org\\stubbles\\test\\ioc\\Goodyear', $vehicle->tire);
    }

    /**
     * test bindings to an invalid type
     *
     * @test
     * @expectedException  net\stubbles\lang\exception\IllegalArgumentException
     */
    public function setterInjectionWithInvalidArgument()
    {
        $binder = new Binder();
        $binder->bind('org\\stubbles\\test\\ioc\\Vehicle')->to(313);
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
        $binder->bind('org\\stubbles\\test\\ioc\\Tire')->toInstance($tire);
        $binder->bind('org\\stubbles\\test\\ioc\\Vehicle')->to('org\\stubbles\\test\\ioc\\Bike');

        $injector = $binder->getInjector();

        $this->assertTrue($injector->hasBinding('org\\stubbles\\test\\ioc\\Vehicle'));
        $this->assertTrue($injector->hasBinding('org\\stubbles\\test\\ioc\\Tire'));

        $vehicle = $injector->getInstance('org\\stubbles\\test\\ioc\\Vehicle');

        $this->assertInstanceOf('org\\stubbles\\test\\ioc\\Vehicle', $vehicle);
        $this->assertInstanceOf('org\\stubbles\\test\\ioc\\Bike', $vehicle);
        $this->assertInstanceOf('org\\stubbles\\test\\ioc\\Tire', $vehicle->tire);
        $this->assertInstanceOf('org\\stubbles\\test\\ioc\\Goodyear', $vehicle->tire);
        $this->identicalTo($vehicle->tire, $tire);
    }

    /**
     * test bindings to an instance with an invalid type
     *
     * @test
     * @expectedException  net\stubbles\lang\exception\IllegalArgumentException
     */
    public function setterInjectionByInvalidInstance()
    {
        $tire = new Goodyear();

        $binder = new Binder();
        $binder->bind('org\\stubbles\\test\\ioc\\Vehicle')->toInstance($tire);
    }

    /**
     * test setter injections
     *
     * @test
     */
    public function optionalSetterInjection()
    {
        $tire = new Goodyear();

        $binder = new Binder();
        $binder->bind('org\\stubbles\\test\\ioc\\Tire')->to('org\\stubbles\\test\\ioc\\Goodyear');
        $binder->bind('org\\stubbles\\test\\ioc\\Vehicle')->to('org\\stubbles\\test\\ioc\\Convertible');

        $injector = $binder->getInjector();

        $vehicle = $injector->getInstance('org\\stubbles\\test\\ioc\\Vehicle');

        $this->assertInstanceOf('org\\stubbles\\test\\ioc\\Vehicle', $vehicle);
        $this->assertInstanceOf('org\\stubbles\\test\\ioc\\Convertible', $vehicle);

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
        $this->assertFalse($injector->hasExplicitBinding('org\\stubbles\\test\\ioc\\Goodyear'));
        $goodyear = $injector->getInstance('org\\stubbles\\test\\ioc\\Goodyear');
        $this->assertInstanceOf('org\\stubbles\\test\\ioc\\Goodyear', $goodyear);
        $this->assertTrue($injector->hasExplicitBinding('org\\stubbles\\test\\ioc\\Goodyear'));
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
        $this->assertFalse($injector->hasExplicitBinding('org\\stubbles\\test\\ioc\\ImplicitDependency'));
        $obj      = $injector->getInstance('org\\stubbles\\test\\ioc\\ImplicitDependency');
        $this->assertInstanceOf('org\\stubbles\\test\\ioc\\ImplicitDependency', $obj);
        $this->assertInstanceOf('org\\stubbles\\test\\ioc\\Goodyear', $obj->getGoodyearByConstructor());
        $this->assertInstanceOf('org\\stubbles\\test\\ioc\\Goodyear', $obj->getGoodyearBySetter());
        $this->assertTrue($injector->hasExplicitBinding('org\\stubbles\\test\\ioc\\ImplicitDependency'));
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
        $this->assertInstanceOf('org\\stubbles\\test\\ioc\\Goodyear', $obj->getGoodyearBySetter());
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
        $binder->bind('org\\stubbles\\test\\ioc\\Goodyear')->to('org\\stubbles\\test\\ioc\\Goodyear');
        $injector = $binder->getInjector();
        $injector->handleInjections($obj);
        $this->assertInstanceOf('org\\stubbles\\test\\ioc\\Goodyear', $obj->getGoodyearBySetter());
    }

    /**
     * @test
     */
    public function injectedInjectorIsUsed()
    {
        $injector = new Injector();
        $binder   = new Binder($injector);
        $this->assertSame($injector, $binder->getInjector());
    }

    /**
     * @test
     * @expectedException  net\stubbles\ioc\BindingException
     */
    public function missingBindingThrowsBindingException()
    {
        $injector = new Injector();
        $injector->getInstance('org\\stubbles\\test\\ioc\\Vehicle');
    }

    /**
     * @test
     * @expectedException  net\stubbles\ioc\BindingException
     */
    public function missingBindingOnInjectionHandlingThrowsBindingException()
    {
        $injector = new Injector();
        $class    = new Bike();
        $injector->handleInjections($class);
    }

    /**
     * @test
     * @expectedException  net\stubbles\ioc\BindingException
     */
    public function missingConstantBindingOnInjectionHandlingThrowsBindingException()
    {
        $injector = new Injector();
        $injector->handleInjections(new MissingArrayInjection());
    }

    /**
     * @since  1.5.0
     * @test
     */
    public function addBindingReturnsAddedBinding()
    {
        $injector    = new Injector();
        $mockBinding = $this->getMock('net\\stubbles\\ioc\\binding\\Binding');
        $this->assertSame($mockBinding, $injector->addBinding($mockBinding));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function optionalConstructorInjection()
    {
        $injector = new Injector();
        $bike     = $injector->getInstance('org\\stubbles\\test\\ioc\\BikeWithOptionalTire');
        $this->assertInstanceOf('org\\stubbles\\test\\ioc\\Goodyear', $bike->tire);

    }
}
?>