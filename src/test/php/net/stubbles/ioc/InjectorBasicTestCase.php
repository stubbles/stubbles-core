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
/**
 * Helper interface for injection and binding tests.
 */
interface Tire
{
    /**
     * rotates the tires
     *
     * @return  string
     */
    public function rotate();
}
/**
 * Helper class for injection and binding tests.
 */
class Goodyear implements Tire
{
    /**
     * rotates the tires
     *
     * @return  string
     */
    public function rotate()
    {
        return "I'm driving with Goodyear tires.";
    }
}
/**
 * Helper class to test implicit binding with concrete class names.
 */
class ImplicitDependency
{
    /**
     * instance from constructor injection
     *
     * @type  Goodyear
     */
    protected $goodyearByConstructor;
    /**
     * instance from setter injection
     *
     * @type  Goodyear
     */
    protected $goodyearBySetter;

    /**
     * constructor
     *
     * @param  Goodyear  $goodyear
     * @Inject
     */
    public function __construct(Goodyear $goodyear)
    {
        $this->goodyearByConstructor = $goodyear;
    }

    /**
     * setter
     *
     * @param  Goodyear  $goodyear
     * @Inject
     */
    public function setGoodyear(Goodyear $goodyear)
    {
        $this->goodyearBySetter = $goodyear;
    }

    /**
     * returns the instance from constructor injection
     *
     * @return  Goodyear
     */
    public function getGoodyearByConstructor()
    {
        return $this->goodyearByConstructor;
    }

    /**
     * returns the instance from setter injection
     *
     * @return  Goodyear
     */
    public function getGoodyearBySetter()
    {
        return $this->goodyearBySetter;
    }
}
/**
 * Helper class to test implicit binding related to bug #102.
 *
 * @link  http://stubbles.net/ticket/102
 */
class ImplicitDependencyBug102
{
    /**
     * instance from setter injection
     *
     * @type  Goodyear
     */
    protected $goodyearBySetter;

    /**
     * setter
     *
     * @param  Goodyear  $goodyear
     * @Inject
     */
    public function setGoodyear(Goodyear $goodyear)
    {
        $this->goodyearBySetter = $goodyear;
    }

    /**
     * returns the instance from setter injection
     *
     * @return  Goodyear
     */
    public function getGoodyearBySetter()
    {
        return $this->goodyearBySetter;
    }
}
/**
 * Helper class to test implicit binding related to bug #102.
 */
class ImplicitOptionalDependency
{
    /**
     * instance from setter injection
     *
     * @type  Goodyear
     */
    protected $goodyearBySetter;

    /**
     * setter
     *
     * @param  Goodyear  $goodyear
     * @Inject(optional=true)
     */
    public function setGoodyear(Goodyear $goodyear)
    {
        $this->goodyearBySetter = $goodyear;
    }

    /**
     * returns the instance from setter injection
     *
     * @return  Goodyear
     */
    public function getGoodyearBySetter()
    {
        return $this->goodyearBySetter;
    }
}
/**
 * Another helper interface for injection and binding tests.
 */
interface Vehicle
{
    /**
     * moves the vehicle forward
     *
     * @return  string
     */
    public function moveForward();
}
/**
 * Another helper class for injection and binding tests.
 */
class Car implements Vehicle
{
    /**
     * injected tire instance
     *
     * @type  Tire
     */
    public $tire;

    /**
     * Create a new car
     *
     * @param  Tire  $tire
     * @Inject
     */
    public function __construct(Tire $tire)
    {
        $this->tire = $tire;
    }

    /**
     * moves the vehicle forward
     *
     * @return  string
     */
    public function moveForward()
    {
        return $this->tire->rotate();
    }
}
/**
 * Another helper class for injection and binding tests.
 */
class Bike implements Vehicle
{
    /**
     * injected tire instance
     *
     * @type  Tire
     */
    public $tire;

    /**
     * sets the tire
     *
     * @param  Tire  $tire
     * @Inject
     */
    public function setTire(Tire $tire)
    {
        $this->tire = $tire;
    }

    /**
     * moves the vehicle forward
     *
     * @return  string
     */
    public function moveForward()
    {
        return $this->tire->rotate();
    }
}
/**
 * Another helper interface for injection and binding tests.
 */
interface Roof
{
    /**
     * method to open the roof
     */
    public function open();
    /**
     * method to close the roof
     */
    public function close();
}
/**
 * Another helper class for injection and binding tests.
 */
class Convertible implements Vehicle
{
    /**
     * injected tire instance
     *
     * @type  Tire
     */
    public $tire;
    /**
     * injected roof instance
     *
     * @type   Roof
     */
    public $roof;

    /**
     * sets the tire
     *
     * @param  Tire $tire
     * @Inject
     */
    public function setTire(Tire $tire)
    {
        $this->tire = $tire;
    }

    /**
     * sets the root
     *
     * @param  Roof  $roof
     * @Inject(optional=true)
     */
    public function setRoof(Roof $roof)
    {
        $this->roof = $roof;
    }

    /**
     * moves the vehicle forward
     *
     * @return  string
     */
    public function moveForward()
    {
        return $this->tire->rotate();
    }
}

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
        $binder->bind('net\\stubbles\\ioc\\Tire')->to('net\\stubbles\\ioc\\Goodyear');
        $binder->bind('net\\stubbles\\ioc\\Vehicle')->to('net\\stubbles\\ioc\\Car');

        $injector = $binder->getInjector();

        $this->assertTrue($injector->hasBinding('net\\stubbles\\ioc\\Vehicle'));
        $this->assertTrue($injector->hasBinding('net\\stubbles\\ioc\\Tire'));

        $vehicle = $injector->getInstance('net\\stubbles\\ioc\\Vehicle');

        $this->assertInstanceOf('net\\stubbles\\ioc\\Vehicle', $vehicle);
        $this->assertInstanceOf('net\\stubbles\\ioc\\Car', $vehicle);
        $this->assertInstanceOf('net\\stubbles\\ioc\\Tire', $vehicle->tire);
        $this->assertInstanceOf('net\\stubbles\\ioc\\Goodyear', $vehicle->tire);
    }

    /**
     * test setter injections
     *
     * @test
     */
    public function setterInjection()
    {
        $binder = new Binder();
        $binder->bind('net\\stubbles\\ioc\\Tire')->to('net\\stubbles\\ioc\\Goodyear');
        $binder->bind('net\\stubbles\\ioc\\Vehicle')->to('net\\stubbles\\ioc\\Bike');

        $injector = $binder->getInjector();

        $this->assertTrue($injector->hasBinding('net\\stubbles\\ioc\\Vehicle'));
        $this->assertTrue($injector->hasBinding('net\\stubbles\\ioc\\Tire'));

        $vehicle = $injector->getInstance('net\\stubbles\\ioc\\Vehicle');

        $this->assertInstanceOf('net\\stubbles\\ioc\\Vehicle', $vehicle);
        $this->assertInstanceOf('net\\stubbles\\ioc\\Bike', $vehicle);
        $this->assertInstanceOf('net\\stubbles\\ioc\\Tire', $vehicle->tire);
        $this->assertInstanceOf('net\\stubbles\\ioc\\Goodyear', $vehicle->tire);
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
        $binder->bind('net\\stubbles\\ioc\\Tire')->to(new ReflectionClass('net\\stubbles\\ioc\\Goodyear'));
        $binder->bind('net\\stubbles\\ioc\\Vehicle')->to(new ReflectionClass('net\\stubbles\\ioc\\Bike'));

        $injector = $binder->getInjector();

        $this->assertTrue($injector->hasBinding('net\\stubbles\\ioc\\Vehicle'));
        $this->assertTrue($injector->hasBinding('net\\stubbles\\ioc\\Tire'));

        $vehicle = $injector->getInstance('net\\stubbles\\ioc\\Vehicle');

        $this->assertInstanceOf('net\\stubbles\\ioc\\Vehicle', $vehicle);
        $this->assertInstanceOf('net\\stubbles\\ioc\\Bike', $vehicle);
        $this->assertInstanceOf('net\\stubbles\\ioc\\Tire', $vehicle->tire);
        $this->assertInstanceOf('net\\stubbles\\ioc\\Goodyear', $vehicle->tire);
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
        $binder->bind('net\\stubbles\\ioc\\Vehicle')->to(313);
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
        $binder->bind('net\\stubbles\\ioc\\Tire')->toInstance($tire);
        $binder->bind('net\\stubbles\\ioc\\Vehicle')->to('net\\stubbles\\ioc\\Bike');

        $injector = $binder->getInjector();

        $this->assertTrue($injector->hasBinding('net\\stubbles\\ioc\\Vehicle'));
        $this->assertTrue($injector->hasBinding('net\\stubbles\\ioc\\Tire'));

        $vehicle = $injector->getInstance('net\\stubbles\\ioc\\Vehicle');

        $this->assertInstanceOf('net\\stubbles\\ioc\\Vehicle', $vehicle);
        $this->assertInstanceOf('net\\stubbles\\ioc\\Bike', $vehicle);
        $this->assertInstanceOf('net\\stubbles\\ioc\\Tire', $vehicle->tire);
        $this->assertInstanceOf('net\\stubbles\\ioc\\Goodyear', $vehicle->tire);
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
        $binder->bind('net\\stubbles\\ioc\\Vehicle')->toInstance($tire);
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
        $binder->bind('net\\stubbles\\ioc\\Tire')->to('net\\stubbles\\ioc\\Goodyear');
        $binder->bind('net\\stubbles\\ioc\\Vehicle')->to('net\\stubbles\\ioc\\Convertible');

        $injector = $binder->getInjector();

        $vehicle = $injector->getInstance('net\\stubbles\\ioc\\Vehicle');

        $this->assertInstanceOf('net\\stubbles\\ioc\\Vehicle', $vehicle);
        $this->assertInstanceOf('net\\stubbles\\ioc\\Convertible', $vehicle);

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
        $this->assertFalse($injector->hasExplicitBinding('net\\stubbles\\ioc\\Goodyear'));
        $goodyear = $injector->getInstance('net\\stubbles\\ioc\\Goodyear');
        $this->assertInstanceOf('net\\stubbles\\ioc\\Goodyear', $goodyear);
        $this->assertTrue($injector->hasExplicitBinding('net\\stubbles\\ioc\\Goodyear'));
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
        $this->assertFalse($injector->hasExplicitBinding('net\\stubbles\\ioc\\ImplicitDependency'));
        $obj      = $injector->getInstance('net\\stubbles\\ioc\\ImplicitDependency');
        $this->assertInstanceOf('net\\stubbles\\ioc\\ImplicitDependency', $obj);
        $this->assertInstanceOf('net\\stubbles\\ioc\\Goodyear', $obj->getGoodyearByConstructor());
        $this->assertInstanceOf('net\\stubbles\\ioc\\Goodyear', $obj->getGoodyearBySetter());
        $this->assertTrue($injector->hasExplicitBinding('net\\stubbles\\ioc\\ImplicitDependency'));
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
        $this->assertInstanceOf('net\\stubbles\\ioc\\Goodyear', $obj->getGoodyearBySetter());
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
        $binder->bind('net\\stubbles\\ioc\\Goodyear')->to('net\\stubbles\\ioc\\Goodyear');
        $injector = $binder->getInjector();
        $injector->handleInjections($obj);
        $this->assertInstanceOf('net\\stubbles\\ioc\\Goodyear', $obj->getGoodyearBySetter());
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
        $injector->getInstance('net\\stubbles\\ioc\\Vehicle');
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
     * @since  1.5.0
     */
    public function addBindingReturnsAddedBinding()
    {
        $injector    = new Injector();
        $mockBinding = $this->getMock('net\\stubbles\\ioc\\Binding');
        $this->assertSame($mockBinding, $injector->addBinding($mockBinding));
    }
}
?>