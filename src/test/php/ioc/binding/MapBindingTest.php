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
use stubbles\lang\reflect\ReflectionClass;
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
     * @type  MapBinding
     */
    private $mapBinding;
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
        $this->mapBinding   = new MapBinding('foo');
    }

    /**
     * @test
     */
    public function getKeyReturnsUniqueListKey()
    {
        $this->assertEquals(MapBinding::TYPE . '#foo',
                            $this->mapBinding->getKey()
        );
    }

    /**
     * @test
     */
    public function returnsEmptyListIfNothingAdded()
    {
        $this->assertEquals([],
                            $this->mapBinding->getInstance($this->mockInjector,
                                                           'int'
                                               )
        );
    }

    /**
     * @test
     */
    public function returnsTypedEmptyListIfNothingAdded()
    {
        $this->assertEquals([],
                            $this->mapBinding->getInstance($this->mockInjector,
                                                           new ReflectionClass('\stdClass')
                                               )
        );
    }

    /**
     * @test
     */
    public function valueIsAddedToList()
    {
        $this->assertEquals(['x' => 303],
                            $this->mapBinding->withEntry('x', 303)
                                             ->getInstance($this->mockInjector,
                                                           'int'
                                               )
        );
    }

    /**
     * @test
     */
    public function valueIsAddedToTypedList()
    {
        $value = new \stdClass();
        $this->assertEquals(['x' => $value],
                            $this->mapBinding->withEntry('x', $value)
                                             ->getInstance($this->mockInjector,
                                                           new ReflectionClass('\stdClass')
                                               )
        );
    }

    /**
     * @test
     */
    public function classNameIsAddedToTypedList()
    {
        $value = new \stdClass();
        $this->mockInjector->expects($this->once())
                           ->method('getInstance')
                           ->with($this->equalTo('\stdClass'))
                           ->will($this->returnValue($value));
        $this->assertEquals(['x' => $value],
                            $this->mapBinding->withEntry('x', '\stdClass')
                                             ->getInstance($this->mockInjector,
                                                           new ReflectionClass('\stdClass')
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
                         ->getInstance($this->mockInjector,
                                       new ReflectionClass('\stdClass')
        );
    }

    /**
     * @test
     * @expectedException  stubbles\ioc\binding\BindingException
     */
    public function invalidObjectAddedToTypedListThrowsBindingException()
    {
        $this->mapBinding->withEntry('x', new \stdClass())
                         ->getInstance($this->mockInjector,
                                       new ReflectionClass('stubbles\ioc\InjectionProvider')
        );
    }

    /**
     * creates mocked injection provider which returns given value
     *
     * @param   mixed  $value
     * @return  \PHPUnit_Framework_MockObject_MockObject
     */
    private function getMockInjectionProvider($value)
    {
        $mockProvider = $this->getMock('stubbles\ioc\InjectionProvider');
        $mockProvider->expects($this->once())
                     ->method('get')
                     ->will($this->returnValue($value));
        return $mockProvider;
    }

    /**
     * @test
     */
    public function valueFromProviderIsAddedToList()
    {
        $this->assertEquals(['x' => 303],
                            $this->mapBinding->withEntryFromProvider('x', $this->getMockInjectionProvider(303))
                                             ->getInstance($this->mockInjector,
                                                           'int'
                                               )
        );
    }

    /**
     * @test
     */
    public function valueFromProviderIsAddedToTypedList()
    {
        $value = new \stdClass();
        $this->assertEquals(['x' => $value],
                            $this->mapBinding->withEntryFromProvider('x', $this->getMockInjectionProvider($value))
                                             ->getInstance($this->mockInjector,
                                                           new ReflectionClass('\stdClass')
                                               )
        );
    }

    /**
     * @test
     * @expectedException  stubbles\ioc\binding\BindingException
     */
    public function invalidValueFromProviderAddedToTypedListThrowsBindingException()
    {
        $this->mapBinding->withEntryFromProvider('x', $this->getMockInjectionProvider(303))
                         ->getInstance($this->mockInjector,
                                       new ReflectionClass('\\stdClass')
        );
    }

    /**
     * @test
     * @expectedException  stubbles\ioc\binding\BindingException
     */
    public function invalidObjectFromProviderAddedToTypedListThrowsBindingException()
    {
        $this->mapBinding->withEntryFromProvider('x', $this->getMockInjectionProvider(new \stdClass()))
                         ->getInstance($this->mockInjector,
                                       new ReflectionClass('stubbles\ioc\InjectionProvider')
        );
    }

    /**
     * @test
     */
    public function valueFromProviderClassIsAddedToList()
    {
        $mockProvider      = $this->getMockInjectionProvider(303);
        $mockProviderClass = $this->getMockClass('stubbles\ioc\InjectionProvider');
        $this->prepareInjector($mockProvider, $mockProviderClass);
        $this->assertEquals(['x' => 303],
                            $this->mapBinding->withEntryFromProvider('x', $mockProviderClass)
                                             ->getInstance($this->mockInjector,
                                                           'int'
                                               )
        );
    }

    /**
     * @test
     */
    public function valueFromProviderClassIsAddedToTypedList()
    {
        $value             = new \stdClass();
        $mockProvider      = $this->getMockInjectionProvider($value);
        $mockProviderClass = $this->getMockClass('stubbles\ioc\InjectionProvider');
        $this->prepareInjector($mockProvider, $mockProviderClass);
        $this->assertEquals(['x' => $value],
                            $this->mapBinding->withEntryFromProvider('x', $mockProviderClass)
                                             ->getInstance($this->mockInjector,
                                                           new ReflectionClass('\stdClass')
                                               )
        );
    }

    /**
     * @test
     * @expectedException  stubbles\ioc\binding\BindingException
     */
    public function invalidValueFromProviderClassAddedToTypedListThrowsBindingException()
    {
        $mockProvider = $this->getMockInjectionProvider(303);
        $mockProviderClass = $this->getMockClass('stubbles\ioc\InjectionProvider');
        $this->prepareInjector($mockProvider, $mockProviderClass);
        $this->mapBinding->withEntryFromProvider('x', $mockProviderClass)
                         ->getInstance($this->mockInjector,
                                       new ReflectionClass('\stdClass')
        );
    }

    /**
     * @test
     * @expectedException  stubbles\ioc\binding\BindingException
     */
    public function invalidObjectFromProviderClassAddedToTypedListThrowsBindingException()
    {
        $mockProvider = $this->getMockInjectionProvider(new \stdClass());
        $mockProviderClass = $this->getMockClass('stubbles\ioc\InjectionProvider');
        $this->prepareInjector($mockProvider, $mockProviderClass);
        $this->mapBinding->withEntryFromProvider('x', $mockProviderClass)
                         ->getInstance($this->mockInjector,
                                       new ReflectionClass('stubbles\ioc\InjectionProvider')
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
        $this->mockInjector->expects($this->once())
                           ->method('getInstance')
                           ->with($this->equalTo($mockProviderClass))
                           ->will($this->returnValue($mockProvider));

    }

    /**
     * @test
     * @expectedException  stubbles\ioc\binding\BindingException
     */
    public function addInvalidProviderClassThrowsBindingException()
    {
        $mockProviderClass = $this->getMockClass('stubbles\ioc\InjectionProvider');
        $this->mockInjector->expects($this->once())
                           ->method('getInstance')
                           ->with($this->equalTo($mockProviderClass))
                           ->will($this->returnValue('\stdClass'));
        $this->mapBinding->withEntryFromProvider('x', $mockProviderClass)
                         ->getInstance($this->mockInjector,
                                       new ReflectionClass('stubbles\ioc\InjectionProvider')
        );
    }

    /**
     * @test
     * @expectedException  stubbles\lang\exception\IllegalArgumentException
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
        $this->assertEquals(['x' => 303],
                            $this->mapBinding->withEntryFromClosure('x', function() { return 303; })
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
        $this->assertEquals(['x' => $value],
                            $this->mapBinding->withEntryFromClosure('x', function() use($value) { return $value; })
                                             ->getInstance($this->mockInjector,
                                                           new ReflectionClass('\stdClass')
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
                         ->getInstance($this->mockInjector,
                                       new ReflectionClass('\stdClass')
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
                         ->getInstance($this->mockInjector,
                                       new ReflectionClass('stubbles\ioc\InjectionProvider')
        );
    }
}
