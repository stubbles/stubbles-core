<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\ioc\binding;
use bovigo\callmap\NewInstance;
use stubbles\ioc\InjectionProvider;
use stubbles\ioc\Injector;

use function bovigo\assert\assert;
use function bovigo\assert\assertEmptyArray;
use function bovigo\assert\predicate\equals;
/**
 * Test for stubbles\ioc\binding\ListBinding.
 *
 * @since  2.0.0
 * @group  ioc
 * @group  ioc_binding
 */
class ListBindingTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  \stubbles\ioc\binding\ListBinding
     */
    private $listBinding;
    /**
     * mocked injector
     *
     * @type  \bovigo\callmap\Proxy
     */
    private $injector;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->injector    = NewInstance::of(Injector::class);
        $this->listBinding = new ListBinding('foo');
    }

    /**
     * @test
     */
    public function getKeyReturnsUniqueListKey()
    {
        assert($this->listBinding->getKey(), equals(ListBinding::TYPE . '#foo'));
    }

    /**
     * @test
     */
    public function returnsEmptyListIfNothingAdded()
    {
        assertEmptyArray($this->listBinding->getInstance($this->injector, 'int'));
    }

    /**
     * @test
     */
    public function returnsTypedEmptyListIfNothingAdded()
    {
        assertEmptyArray(
                $this->listBinding->getInstance(
                        $this->injector,
                        new \ReflectionClass(\stdClass::class)
                )
        );
    }

    /**
     * @test
     */
    public function valueIsAddedToList()
    {
        assert(
                $this->listBinding->withValue(303)
                        ->getInstance($this->injector, 'int'),
                equals([303])
        );
    }

    /**
     * @test
     */
    public function valueIsAddedToTypedList()
    {
        $value = new \stdClass();
        assert(
                $this->listBinding->withValue($value)
                        ->getInstance(
                                $this->injector,
                                new \ReflectionClass(\stdClass::class)
                        ),
                equals([$value])
        );
    }

    /**
     * @test
     */
    public function classNameIsAddedToTypedList()
    {
        $value = new \stdClass();
        $this->injector->mapCalls(['getInstance' => $value]);
        assert(
                $this->listBinding->withValue(\stdClass::class)
                        ->getInstance(
                                $this->injector,
                                new \ReflectionClass(\stdClass::class)
                        ),
                equals([$value])
        );
    }

    /**
     * @test
     * @expectedException  stubbles\ioc\binding\BindingException
     */
    public function invalidValueAddedToTypedListThrowsBindingException()
    {
        $this->listBinding->withValue(303)->getInstance(
                $this->injector,
                new \ReflectionClass(\stdClass::class)
        );
    }

    /**
     * @test
     * @expectedException  stubbles\ioc\binding\BindingException
     */
    public function invalidObjectAddedToTypedListThrowsBindingException()
    {
        $this->listBinding->withValue(new \stdClass())->getInstance(
                $this->injector,
                new \ReflectionClass(InjectionProvider::class)
        );
    }

    /**
     * creates mocked injection provider which returns given value
     *
     * @param   mixed  $value
     * @return  \stubbles\ioc\InjectionProvider
     */
    private function createInjectionProvider($value)
    {
        return NewInstance::of(InjectionProvider::class)
                ->mapCalls(['get' => $value]);
    }

    /**
     * @test
     */
    public function valueFromProviderIsAddedToList()
    {
        assert(
                $this->listBinding
                        ->withValueFromProvider($this->createInjectionProvider(303))
                        ->getInstance($this->injector, 'int'),
                equals([303])
        );
    }

    /**
     * @test
     */
    public function valueFromProviderIsAddedToTypedList()
    {
        $value = new \stdClass();
        assert(
                $this->listBinding
                        ->withValueFromProvider($this->createInjectionProvider($value))
                        ->getInstance(
                                $this->injector,
                                new \ReflectionClass(\stdClass::class)
                        ),
                equals([$value])
        );
    }

    /**
     * @test
     * @expectedException  stubbles\ioc\binding\BindingException
     */
    public function invalidValueFromProviderAddedToTypedListThrowsBindingException()
    {
        $this->listBinding->withValueFromProvider(
                $this->createInjectionProvider(303)
        )->getInstance(
                $this->injector,
                new \ReflectionClass(\stdClass::class)
        );
    }

    /**
     * @test
     * @expectedException  stubbles\ioc\binding\BindingException
     */
    public function invalidObjectFromProviderAddedToTypedListThrowsBindingException()
    {
        $this->listBinding->withValueFromProvider(
                $this->createInjectionProvider(new \stdClass())
        )->getInstance(
                $this->injector,
                new \ReflectionClass(InjectionProvider::class)
        );
    }

    /**
     * @test
     */
    public function valueFromProviderClassIsAddedToList()
    {
        $provider = $this->createInjectionProvider(303);
        $this->prepareInjector($provider);
        assert(
                $this->listBinding->withValueFromProvider(get_class($provider))
                        ->getInstance($this->injector, 'int'),
                equals([303])
        );
    }

    /**
     * @test
     */
    public function valueFromProviderClassIsAddedToTypedList()
    {
        $value    = new \stdClass();
        $provider = $this->createInjectionProvider($value);
        $this->prepareInjector($provider);
        assert(
                $this->listBinding->withValueFromProvider(get_class($provider))
                        ->getInstance(
                                $this->injector,
                                new \ReflectionClass(\stdClass::class)
                        ),
                equals([$value])
        );
    }

    /**
     * @test
     * @expectedException  stubbles\ioc\binding\BindingException
     */
    public function invalidValueFromProviderClassAddedToTypedListThrowsBindingException()
    {
        $provider = $this->createInjectionProvider(303);
        $this->prepareInjector($provider);
        $this->listBinding->withValueFromProvider(get_class($provider))
                ->getInstance(
                        $this->injector,
                        new \ReflectionClass(\stdClass::class)
        );
    }

    /**
     * @test
     * @expectedException  stubbles\ioc\binding\BindingException
     */
    public function invalidObjectFromProviderClassAddedToTypedListThrowsBindingException()
    {
        $provider = $this->createInjectionProvider(new \stdClass());
        $this->prepareInjector($provider);
        $this->listBinding->withValueFromProvider(get_class($provider))
                ->getInstance(
                        $this->injector,
                        new \ReflectionClass(InjectionProvider::class)
        );
    }

    /**
     * prepares injector to return mock provider instance
     *
     * @param  InjectionProvider  $provider
     */
    private function prepareInjector(InjectionProvider $provider)
    {
        $this->injector->mapCalls(['getInstance' => $provider]);

    }

    /**
     * @test
     * @expectedException  stubbles\ioc\binding\BindingException
     */
    public function addInvalidProviderClassThrowsBindingException()
    {
        $providerClass = get_class(NewInstance::of(InjectionProvider::class));
        $this->injector->mapCalls(['getInstance' => \stdClass::class]);
        $this->listBinding->withValueFromProvider($providerClass)
                ->getInstance(
                        $this->injector,
                        new \ReflectionClass(InjectionProvider::class)
        );
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     */
    public function addInvalidProviderValueThrowsIlegalArgumentException()
    {
        $this->listBinding->withValueFromProvider(new \stdClass());
    }

    /**
     * @since  2.1.0
     * @test
     * @group  issue_31
     */
    public function valueFromClosureIsAddedToList()
    {
        assert(
                $this->listBinding->withValueFromClosure(function() { return 303; })
                        ->getInstance($this->injector, 'int'),
                equals([303])
        );
    }

    /**
     * @since  2.1.0
     * @test
     * @group  issue_31
     */
    public function valueFromClosureIsAddedToTypedList()
    {
        $value = new \stdClass();
        assert(
                $this->listBinding->withValueFromClosure(
                                function() use($value) { return $value; }
                        )->getInstance(
                                $this->injector,
                                new \ReflectionClass(\stdClass::class)
                        ),
                equals([$value])
        );
    }

    /**
     * @since  2.1.0
     * @test
     * @group  issue_31
     * @expectedException  stubbles\ioc\binding\BindingException
     */
    public function invalidValueFromClosureAddedToTypedListThrowsBindingException()
    {
        $this->listBinding->withValueFromClosure(function() { return 303; })
                ->getInstance(
                        $this->injector,
                        new \ReflectionClass(\stdClass::class)
        );
    }

    /**
     * @since  2.1.0
     * @test
     * @group  issue_31
     * @expectedException  stubbles\ioc\binding\BindingException
     */
    public function invalidObjectFromClosureAddedToTypedListThrowsBindingException()
    {
        $this->listBinding->withValueFromClosure(function() { return new \stdClass(); })
                ->getInstance(
                        $this->injector,
                        new \ReflectionClass(InjectionProvider::class)
        );
    }
}
