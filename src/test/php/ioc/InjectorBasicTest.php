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
        $binder->bind('stubbles\test\ioc\Tire')->to('stubbles\test\ioc\Goodyear');
        $binder->bind('stubbles\test\ioc\Vehicle')->to('stubbles\test\ioc\Car');

        $injector = $binder->getInjector();

        assertTrue($injector->hasBinding('stubbles\test\ioc\Vehicle'));
        assertTrue($injector->hasBinding('stubbles\test\ioc\Tire'));

        $vehicle = $injector->getInstance('stubbles\test\ioc\Vehicle');

        assertInstanceOf('stubbles\test\ioc\Vehicle', $vehicle);
        assertInstanceOf('stubbles\test\ioc\Car', $vehicle);
        assertInstanceOf('stubbles\test\ioc\Tire', $vehicle->tire);
        assertInstanceOf('stubbles\test\ioc\Goodyear', $vehicle->tire);
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
        assertFalse($injector->hasExplicitBinding('stubbles\test\ioc\Goodyear'));
        $goodyear = $injector->getInstance('stubbles\test\ioc\Goodyear');
        assertInstanceOf('stubbles\test\ioc\Goodyear', $goodyear);
        assertTrue($injector->hasExplicitBinding('stubbles\test\ioc\Goodyear'));
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
        assertFalse($injector->hasExplicitBinding('stubbles\test\ioc\ImplicitDependency'));
        $obj      = $injector->getInstance('stubbles\test\ioc\ImplicitDependency');
        assertInstanceOf('stubbles\test\ioc\ImplicitDependency', $obj);
        assertInstanceOf('stubbles\test\ioc\Goodyear', $obj->getGoodyearByConstructor());
        assertTrue($injector->hasExplicitBinding('stubbles\test\ioc\ImplicitDependency'));
    }

    /**
     * @test
     */
    public function optionalImplicitDependencyWillNotBeSetIfNotBound()
    {
        $binder   = new Binder();
        $injector = $binder->getInjector();
        $obj      = $injector->getInstance('stubbles\test\ioc\ImplicitOptionalDependency');
        assertNull($obj->getGoodyear());
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
        assertInstanceOf('stubbles\test\ioc\Goodyear', $obj->getGoodyear());
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
        assertInstanceOf('stubbles\test\ioc\Goodyear', $bike->tire);
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
        assertEquals('foo', $bike->other);
    }
}
