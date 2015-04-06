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
use stubbles\ioc\InjectionProvider;
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
     * @type  ListBinding
     */
    private $listBinding;
    /**
     * mocked injector
     *
     * @type  \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockInjector;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->mockInjector = $this->getMockBuilder('stubbles\ioc\Injector')
                ->disableOriginalConstructor()
                ->getMock();
        $this->listBinding = new ListBinding('foo');
    }

    /**
     * @test
     */
    public function getKeyReturnsUniqueListKey()
    {
        assertEquals(
                ListBinding::TYPE . '#foo',
                $this->listBinding->getKey()
        );
    }

    /**
     * @test
     */
    public function returnsEmptyListIfNothingAdded()
    {
        assertEquals(
                [],
                $this->listBinding->getInstance($this->mockInjector, 'int')
        );
    }

    /**
     * @test
     */
    public function returnsTypedEmptyListIfNothingAdded()
    {
        assertEquals(
                [],
                $this->listBinding->getInstance(
                        $this->mockInjector,
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
            [303],
            $this->listBinding->withValue(303)
                    ->getInstance($this->mockInjector, 'int')
        );
    }

    /**
     * @test
     */
    public function valueIsAddedToTypedList()
    {
        $value = new \stdClass();
        assertEquals(
            [$value],
            $this->listBinding->withValue($value)
                    ->getInstance(
                            $this->mockInjector,
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
        $this->mockInjector->method('getInstance')
                ->with(equalTo('\stdClass'))
                ->will(returnValue($value));
        assertEquals(
            [$value],
            $this->listBinding->withValue('\stdClass')
                    ->getInstance(
                            $this->mockInjector,
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
        $this->listBinding->withValue(303)->getInstance(
                $this->mockInjector,
                new \ReflectionClass('\stdClass')
        );
    }

    /**
     * @test
     * @expectedException  stubbles\ioc\binding\BindingException
     */
    public function invalidObjectAddedToTypedListThrowsBindingException()
    {
        $this->listBinding->withValue(new \stdClass())->getInstance(
                $this->mockInjector,
                new \ReflectionClass('stubbles\ioc\InjectionProvider')
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
        $mockProvider = $this->getMock('stubbles\ioc\InjectionProvider');
        $mockProvider->method('get')->will(returnValue($value));
        return $mockProvider;
    }

    /**
     * @test
     */
    public function valueFromProviderIsAddedToList()
    {
        assertEquals(
            [303],
            $this->listBinding->withValueFromProvider($this->createInjectionProvider(303))
                    ->getInstance($this->mockInjector, 'int')
        );
    }

    /**
     * @test
     */
    public function valueFromProviderIsAddedToTypedList()
    {
        $value = new \stdClass();
        assertEquals(
            [$value],
            $this->listBinding->withValueFromProvider($this->createInjectionProvider($value))
                    ->getInstance(
                            $this->mockInjector,
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
        $this->listBinding->withValueFromProvider(
                $this->createInjectionProvider(303)
        )->getInstance(
                $this->mockInjector,
                new \ReflectionClass('\stdClass')
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
                $this->mockInjector,
                new \ReflectionClass('stubbles\ioc\InjectionProvider')
        );
    }

    /**
     * @test
     */
    public function valueFromProviderClassIsAddedToList()
    {
        $mockProvider      = $this->createInjectionProvider(303);
        $mockProviderClass = $this->getMockClass('stubbles\ioc\InjectionProvider');
        $this->prepareInjector($mockProvider, $mockProviderClass);
        assertEquals(
            [303],
            $this->listBinding->withValueFromProvider($mockProviderClass)
                    ->getInstance($this->mockInjector, 'int')
        );
    }

    /**
     * @test
     */
    public function valueFromProviderClassIsAddedToTypedList()
    {
        $value             = new \stdClass();
        $mockProvider      = $this->createInjectionProvider($value);
        $mockProviderClass = $this->getMockClass('stubbles\ioc\InjectionProvider');
        $this->prepareInjector($mockProvider, $mockProviderClass);
        assertEquals(
            [$value],
            $this->listBinding->withValueFromProvider($mockProviderClass)
                    ->getInstance(
                            $this->mockInjector,
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
        $mockProvider      = $this->createInjectionProvider(303);
        $mockProviderClass = $this->getMockClass('stubbles\ioc\InjectionProvider');
        $this->prepareInjector($mockProvider, $mockProviderClass);
        $this->listBinding->withValueFromProvider($mockProviderClass)
                ->getInstance(
                        $this->mockInjector,
                        new \ReflectionClass('\stdClass')
        );
    }

    /**
     * @test
     * @expectedException  stubbles\ioc\binding\BindingException
     */
    public function invalidObjectFromProviderClassAddedToTypedListThrowsBindingException()
    {
        $mockProvider = $this->createInjectionProvider(new \stdClass());
        $mockProviderClass = $this->getMockClass('stubbles\ioc\InjectionProvider');
        $this->prepareInjector($mockProvider, $mockProviderClass);
        $this->listBinding->withValueFromProvider($mockProviderClass)
                ->getInstance(
                        $this->mockInjector,
                        new \ReflectionClass('stubbles\ioc\InjectionProvider')
        );
    }

    /**
     * prepares injector to return mock provider instance
     *
     * @param  InjectionProvider  $mockProvider
     * @param  string             $mockProviderClass
     */
    private function prepareInjector(InjectionProvider $mockProvider, $mockProviderClass)
    {
        $this->mockInjector->method('getInstance')
                ->with(equalTo($mockProviderClass))
                ->will(returnValue($mockProvider));

    }

    /**
     * @test
     * @expectedException  stubbles\ioc\binding\BindingException
     */
    public function addInvalidProviderClassThrowsBindingException()
    {
        $mockProviderClass = $this->getMockClass('stubbles\ioc\InjectionProvider');
        $this->mockInjector->method('getInstance')
                ->with(equalTo($mockProviderClass))
                ->will(returnValue('\stdClass'));
        $this->listBinding->withValueFromProvider($mockProviderClass)
                ->getInstance(
                        $this->mockInjector,
                        new \ReflectionClass('stubbles\ioc\InjectionProvider')
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
        assertEquals(
            [303],
            $this->listBinding->withValueFromClosure(function() { return 303; })
                    ->getInstance($this->mockInjector, 'int')
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
            [$value],
            $this->listBinding->withValueFromClosure(
                            function() use($value) { return $value; }
                    )->getInstance(
                            $this->mockInjector,
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
        $this->listBinding->withValueFromClosure(function() { return 303; })
                ->getInstance(
                        $this->mockInjector,
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
        $this->listBinding->withValueFromClosure(function() { return new \stdClass(); })
                ->getInstance(
                        $this->mockInjector,
                        new \ReflectionClass('stubbles\ioc\InjectionProvider')
        );
    }
}
