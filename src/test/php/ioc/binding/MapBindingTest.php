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
/**
 * Test for stubbles\ioc\binding\MapBinding.
 *
 * @since  2.0.0
 * @group  ioc
 * @group  ioc_binding
 */
class MapBindingTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  \stubbles\ioc\binding\MapBinding
     */
    private $mapBinding;
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
        $this->injector   = NewInstance::of(Injector::class);
        $this->mapBinding = new MapBinding('foo');
    }

    /**
     * @test
     */
    public function getKeyReturnsUniqueListKey()
    {
        assertEquals(
                MapBinding::TYPE . '#foo',
                $this->mapBinding->getKey()
        );
    }

    /**
     * @test
     */
    public function returnsEmptyListIfNothingAdded()
    {
        assertEquals(
            [],
            $this->mapBinding->getInstance($this->injector, 'int')
        );
    }

    /**
     * @test
     */
    public function returnsTypedEmptyListIfNothingAdded()
    {
        assertEquals(
            [],
            $this->mapBinding->getInstance(
                    $this->injector,
                    new \ReflectionClass('\stdClass')
            )
        );
    }

    /**
     * @test
     */
    public function valueIsAddedToList()
    {
        assertEquals(
            ['x' => 303],
            $this->mapBinding->withEntry('x', 303)
                    ->getInstance($this->injector, 'int')
        );
    }

    /**
     * @test
     */
    public function valueIsAddedToTypedList()
    {
        $value = new \stdClass();
        assertEquals(
            ['x' => $value],
            $this->mapBinding->withEntry('x', $value)
                    ->getInstance(
                            $this->injector,
                            new \ReflectionClass('\stdClass')
            )
        );
    }

    /**
     * @test
     */
    public function classNameIsAddedToTypedList()
    {
        $value = new \stdClass();
        $this->injector->mapCalls(['getInstance' => $value]);
        assertEquals(
            ['x' => $value],
            $this->mapBinding->withEntry('x', '\stdClass')
                    ->getInstance(
                            $this->injector,
                            new \ReflectionClass('\stdClass')
            )
        );
    }

    /**
     * @test
     * @expectedException  stubbles\ioc\binding\BindingException
     */
    public function invalidValueAddedToTypedListThrowsBindingException()
    {
        $this->mapBinding->withEntry('x', 303)
                ->getInstance(
                        $this->injector,
                        new \ReflectionClass('\stdClass')
        );
    }

    /**
     * @test
     * @expectedException  stubbles\ioc\binding\BindingException
     */
    public function invalidObjectAddedToTypedListThrowsBindingException()
    {
        $this->mapBinding->withEntry('x', new \stdClass())
                ->getInstance(
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
        assertEquals(
                ['x' => 303],
                $this->mapBinding->withEntryFromProvider(
                        'x',
                        $this->createInjectionProvider(303)
                )->getInstance($this->injector,'int')
        );
    }

    /**
     * @test
     */
    public function valueFromProviderIsAddedToTypedList()
    {
        $value = new \stdClass();
        assertEquals(
                ['x' => $value],
                $this->mapBinding->withEntryFromProvider(
                        'x',
                        $this->createInjectionProvider($value)
                )->getInstance(
                        $this->injector,
                        new \ReflectionClass('\stdClass')
                )
        );
    }

    /**
     * @test
     * @expectedException  stubbles\ioc\binding\BindingException
     */
    public function invalidValueFromProviderAddedToTypedListThrowsBindingException()
    {
        $this->mapBinding->withEntryFromProvider(
                        'x',
                        $this->createInjectionProvider(303)
                )->getInstance(
                        $this->injector,
                        new \ReflectionClass('\\stdClass')
        );
    }

    /**
     * @test
     * @expectedException  stubbles\ioc\binding\BindingException
     */
    public function invalidObjectFromProviderAddedToTypedListThrowsBindingException()
    {
        $this->mapBinding->withEntryFromProvider(
                        'x',
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
        assertEquals(
                ['x' => 303],
                $this->mapBinding->withEntryFromProvider('x', get_class($provider))
                        ->getInstance($this->injector, 'int')
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
        assertEquals(
                ['x' => $value],
                $this->mapBinding->withEntryFromProvider('x', get_class($provider))
                        ->getInstance(
                                $this->injector,
                                new \ReflectionClass('\stdClass')
                )
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
        $this->mapBinding->withEntryFromProvider('x', get_class($provider))
                ->getInstance(
                        $this->injector,
                        new \ReflectionClass('\stdClass')
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
        $this->mapBinding->withEntryFromProvider('x', get_class($provider))
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
        $this->injector->mapCalls(['getInstance' => '\stdClass']);
        $this->mapBinding->withEntryFromProvider('x', $providerClass)
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
        $this->mapBinding->withEntryFromProvider('x', new \stdClass());
    }

    /**
     * @since  2.1.0
     * @test
     * @group  issue_31
     */
    public function valueFromClosureIsAddedToList()
    {
        assertEquals(
                ['x' => 303],
                $this->mapBinding->withEntryFromClosure('x', function() { return 303; })
                        ->getInstance($this->injector, 'int')
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
        assertEquals(
                ['x' => $value],
                $this->mapBinding->withEntryFromClosure(
                        'x',
                        function() use($value) { return $value; }
                )->getInstance(
                        $this->injector,
                        new \ReflectionClass('\stdClass')
                )
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
        $this->mapBinding->withEntryFromClosure('x', function() { return 303; })
                ->getInstance(
                        $this->injector,
                        new \ReflectionClass('\stdClass')
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
        $this->mapBinding->withEntryFromClosure('x', function() { return new \stdClass(); })
                ->getInstance(
                        $this->injector,
                        new \ReflectionClass(InjectionProvider::class)
        );
    }
}
