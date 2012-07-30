<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles
 */
namespace net\stubbles\ioc\binding;
use net\stubbles\ioc\InjectionProvider;
use net\stubbles\lang\reflect\ReflectionClass;
/**
 * Test for net\stubbles\ioc\binding\ListBinding.
 *
 * @since  2.0.0
 * @group  ioc
 * @group  ioc_binding
 */
class ListBindingTestCase extends \PHPUnit_Framework_TestCase
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
        $this->mockInjector = $this->getMockBuilder('net\\stubbles\\ioc\\Injector')
                                   ->disableOriginalConstructor()
                                   ->getMock();
        $this->listBinding  = new ListBinding('foo');
    }

    /**
     * @test
     */
    public function getKeyReturnsUniqueListKey()
    {
        $this->assertEquals(ListBinding::TYPE . '#foo',
                            $this->listBinding->getKey()
        );
    }

    /**
     * @test
     */
    public function returnsEmptyListIfNothingAdded()
    {
        $this->assertEquals(array(),
                            $this->listBinding->getInstance($this->mockInjector, 'int')
        );
    }

    /**
     * @test
     */
    public function returnsTypedEmptyListIfNothingAdded()
    {
        $this->assertEquals(array(),
                            $this->listBinding->getInstance($this->mockInjector,
                                                            new ReflectionClass('\stdClass')
                                                )
        );
    }

    /**
     * @test
     */
    public function valueIsAddedToList()
    {
        $this->assertEquals(array(303),
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
        $this->assertEquals(array($value),
                            $this->listBinding->withValue($value)
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
        $this->assertEquals(array($value),
                            $this->listBinding->withValue('\stdClass')
                                              ->getInstance($this->mockInjector,
                                                            new ReflectionClass('\stdClass')
                                                )
        );
    }

    /**
     * @test
     * @expectedException  net\stubbles\ioc\binding\BindingException
     */
    public function invalidValueAddedToTypedListThrowsBindingException()
    {
        $this->listBinding->withValue(303)
                          ->getInstance($this->mockInjector,
                                        new ReflectionClass('\stdClass')
        );
    }

    /**
     * @test
     * @expectedException  net\stubbles\ioc\binding\BindingException
     */
    public function invalidObjectAddedToTypedListThrowsBindingException()
    {
        $this->listBinding->withValue(new \stdClass())
                          ->getInstance($this->mockInjector,
                                        new ReflectionClass('net\stubbles\ioc\InjectionProvider')
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
        $mockProvider = $this->getMock('net\stubbles\ioc\InjectionProvider');
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
        $this->assertEquals(array(303),
                            $this->listBinding->withValueFromProvider($this->getMockInjectionProvider(303))
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
        $this->assertEquals(array($value),
                            $this->listBinding->withValueFromProvider($this->getMockInjectionProvider($value))
                                              ->getInstance($this->mockInjector,
                                                            new ReflectionClass('\stdClass')
                                                )
        );
    }

    /**
     * @test
     * @expectedException  net\stubbles\ioc\binding\BindingException
     */
    public function invalidValueFromProviderAddedToTypedListThrowsBindingException()
    {
        $this->listBinding->withValueFromProvider($this->getMockInjectionProvider(303))
                          ->getInstance($this->mockInjector,
                                        new ReflectionClass('\stdClass')
        );
    }

    /**
     * @test
     * @expectedException  net\stubbles\ioc\binding\BindingException
     */
    public function invalidObjectFromProviderAddedToTypedListThrowsBindingException()
    {
        $this->listBinding->withValueFromProvider($this->getMockInjectionProvider(new \stdClass()))
                          ->getInstance($this->mockInjector,
                                        new ReflectionClass('net\stubbles\ioc\InjectionProvider')
        );
    }

    /**
     * @test
     */
    public function valueFromProviderClassIsAddedToList()
    {
        $mockProvider      = $this->getMockInjectionProvider(303);
        $mockProviderClass = $this->getMockClass('net\stubbles\ioc\InjectionProvider');
        $this->prepareInjector($mockProvider, $mockProviderClass);
        $this->assertEquals(array(303),
                            $this->listBinding->withValueFromProvider($mockProviderClass)
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
        $mockProviderClass = $this->getMockClass('net\stubbles\ioc\InjectionProvider');
        $this->prepareInjector($mockProvider, $mockProviderClass);
        $this->assertEquals(array($value),
                            $this->listBinding->withValueFromProvider($mockProviderClass)
                                              ->getInstance($this->mockInjector,
                                                            new ReflectionClass('\stdClass')
                                                )
        );
    }

    /**
     * @test
     * @expectedException  net\stubbles\ioc\binding\BindingException
     */
    public function invalidValueFromProviderClassAddedToTypedListThrowsBindingException()
    {
        $mockProvider      = $this->getMockInjectionProvider(303);
        $mockProviderClass = $this->getMockClass('net\stubbles\ioc\InjectionProvider');
        $this->prepareInjector($mockProvider, $mockProviderClass);
        $this->listBinding->withValueFromProvider($mockProviderClass)
                          ->getInstance($this->mockInjector,
                                        new ReflectionClass('\stdClass')
        );
    }

    /**
     * @test
     * @expectedException  net\stubbles\ioc\binding\BindingException
     */
    public function invalidObjectFromProviderClassAddedToTypedListThrowsBindingException()
    {
        $mockProvider = $this->getMockInjectionProvider(new \stdClass());
        $mockProviderClass = $this->getMockClass('net\stubbles\ioc\InjectionProvider');
        $this->prepareInjector($mockProvider, $mockProviderClass);
        $this->listBinding->withValueFromProvider($mockProviderClass)
                          ->getInstance($this->mockInjector,
                                        new ReflectionClass('net\stubbles\ioc\InjectionProvider')
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
     * @expectedException  net\stubbles\ioc\binding\BindingException
     */
    public function addInvalidProviderClassThrowsBindingException()
    {
        $mockProviderClass = $this->getMockClass('net\stubbles\ioc\InjectionProvider');
        $this->mockInjector->expects($this->once())
                           ->method('getInstance')
                           ->with($this->equalTo($mockProviderClass))
                           ->will($this->returnValue('\stdClass'));
        $this->listBinding->withValueFromProvider($mockProviderClass)
                          ->getInstance($this->mockInjector,
                                        new ReflectionClass('net\stubbles\ioc\InjectionProvider')
        );
    }

    /**
     * @test
     * @expectedException  net\stubbles\lang\exception\IllegalArgumentException
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
        $this->assertEquals(array(303),
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
        $this->assertEquals(array($value),
                            $this->listBinding->withValueFromClosure(function() use($value) { return $value; })
                                              ->getInstance($this->mockInjector,
                                                            new ReflectionClass('\stdClass')
                                                )
        );
    }

    /**
     * @since  2.1.0
     * @test
     * @group  issue_31
     * @expectedException  net\stubbles\ioc\binding\BindingException
     */
    public function invalidValueFromClosureAddedToTypedListThrowsBindingException()
    {
        $this->listBinding->withValueFromClosure(function() { return 303; })
                          ->getInstance($this->mockInjector,
                                        new ReflectionClass('\stdClass')
        );
    }

    /**
     * @since  2.1.0
     * @test
     * @group  issue_31
     * @expectedException  net\stubbles\ioc\binding\BindingException
     */
    public function invalidObjectFromClosureAddedToTypedListThrowsBindingException()
    {
        $this->listBinding->withValueFromClosure(function() { return new \stdClass(); })
                          ->getInstance($this->mockInjector,
                                        new ReflectionClass('net\stubbles\ioc\InjectionProvider')
        );
    }
}
?>