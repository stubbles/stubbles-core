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
use function bovigo\assert\predicate\equals;
use function bovigo\assert\predicate\isFalse;
use function bovigo\assert\predicate\isInstanceOf;
use function bovigo\assert\predicate\isNull;
use function bovigo\assert\predicate\isTrue;
/**
 * Test for stubbles\ioc\Injector.
 *
 * @group  ioc
 */
class InjectorBasicTest extends \PHPUnit_Framework_TestCase
{
    /**
     * creates injector instance with bindings
     *
     * @param   callable  $applyBindings  optional
     * @return  \stubbles\ioc\Injector
     */
    private function createInjector(callable $applyBindings = null)
    {
        $binder = new Binder();
        if (null !== $applyBindings) {
            $applyBindings($binder);
        }

        return $binder->getInjector();
    }

    /**
     * @test
     */
    public function injectorHasBindingsWhenSpecified()
    {
        $injector = $this->createInjector(
                function(Binder $binder)
                {
                    $binder->bind(Tire::class)->to(Goodyear::class);
                    $binder->bind(Vehicle::class)->to(Car::class);
                }
        );
        assert($injector->hasBinding(Vehicle::class), isTrue());
        assert($injector->hasBinding(Tire::class), isTrue());
    }

    /**
     * @test
     */
    public function constructorInjection()
    {
        $injector = $this->createInjector(
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
        $injector = $this->createInjector();
        assert($injector->hasExplicitBinding(Goodyear::class), isFalse());
    }

    /**
     * @test
     */
    public function usesImplicitBindingViaTypehints()
    {
        $goodyear = $this->createInjector()->getInstance(Goodyear::class);
        assert($goodyear, isInstanceOf(Goodyear::class));
    }

    /**
     * @test
     */
    public function implicitBindingTurnsIntoExplicitBindingAfterFirstUsage()
    {
        $injector = $this->createInjector();
        $injector->getInstance(Goodyear::class);
        assert($injector->hasExplicitBinding(Goodyear::class), isTrue());
    }

    /**
     * @test
     */
    public function implicitBindingAsDependency()
    {
        $injector = $this->createInjector();
        $obj      = $injector->getInstance(ImplicitDependency::class);
        assert($obj->getGoodyearByConstructor(), isInstanceOf(Goodyear::class));
    }

    /**
     * @test
     */
    public function optionalImplicitDependencyWillNotBeSetIfNotBound()
    {
        $injector = $this->createInjector();
        $obj      = $injector->getInstance(ImplicitOptionalDependency::class);
        assert($obj->getGoodyear(), isNull());
    }

    /**
     * @test
     */
    public function optionalImplicitDependencyWillBeSetIfBound()
    {
        $injector = $this->createInjector(
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
        $injector = $this->createInjector();
        $injector->getInstance(Vehicle::class);
    }

    /**
     * @test
     * @expectedException  stubbles\ioc\binding\BindingException
     */
    public function missingBindingOnInjectionHandlingThrowsBindingException()
    {
        $injector = $this->createInjector();
        $injector->getInstance(Bike::class);
    }

    /**
     * @test
     * @expectedException  stubbles\ioc\binding\BindingException
     */
    public function missingConstantBindingOnInjectionHandlingThrowsBindingException()
    {
        $injector = $this->createInjector();
        $injector->getInstance(MissingArrayInjection::class);
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function optionalConstructorInjection()
    {
        $injector = $this->createInjector();
        $bike     = $injector->getInstance(BikeWithOptionalTire::class);
        assert($bike->tire, isInstanceOf(Goodyear::class));
    }

    /**
     * @test
     * @since  5.1.0
     */
    public function constructorInjectionWithOptionalSecondParam()
    {
        $injector = $this->createInjector(
                function(Binder $binder)
                {
                    $binder->bind(Tire::class)->to(Goodyear::class);
                }
        );
        $bike = $injector->getInstance(BikeWithOptionalOtherParam::class);
        assert($bike->other, equals('foo'));
    }
}
