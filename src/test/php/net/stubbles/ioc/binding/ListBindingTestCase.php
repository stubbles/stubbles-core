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
        $this->mockInjector = $this->getMock('net\\stubbles\\ioc\\Injector');
        $this->listBinding  = new ListBinding($this->mockInjector);
    }

    /**
     * @test
     */
    public function getKeyReturnsUniqueListKey()
    {
        $this->assertEquals(ListBinding::TYPE . '#foo',
                            $this->listBinding->named('foo')->getKey()
        );
    }

    /**
     * @test
     */
    public function returnsEmptyListIfNothingAdded()
    {
        $this->assertEquals(array(),
                            $this->listBinding->getInstance('int', 'foo')
        );
    }

    /**
     * @test
     */
    public function returnsTypedEmptyListIfNothingAdded()
    {
        $this->assertEquals(array(),
                            $this->listBinding->getInstance(new ReflectionClass('\\stdClass'),
                                                            'foo'
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
                                              ->getInstance('int', 'foo')
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
                                              ->getInstance(new ReflectionClass('\\stdClass'),
                                                            'foo'
                            )
        );
    }

    /**
     * @test
     * @expectedException  net\stubbles\ioc\BindingException
     */
    public function invalidValueAddedToTypedListThrowsBindingException()
    {
        $this->listBinding->withValue(303)
                          ->getInstance(new ReflectionClass('\stdClass'),
                                        'foo'
        );
    }

    /**
     * @test
     * @expectedException  net\stubbles\ioc\BindingException
     */
    public function invalidObjectAddedToTypedListThrowsBindingException()
    {
        $this->listBinding->withValue(new \stdClass())
                          ->getInstance(new ReflectionClass('net\\stubbles\\ioc\\InjectionProvider'),
                                        'foo'
        );
    }

    /**
     * @test
     */
    public function valueFromProviderIsAddedToList()
    {
        $mockProvider = $this->getMock('net\\stubbles\\ioc\\InjectionProvider');
        $mockProvider->expects($this->once())
                     ->method('get')
                     ->will($this->returnValue(303));
        $this->assertEquals(array(303),
                            $this->listBinding->withValueFromProvider($mockProvider)
                                              ->getInstance('int', 'foo')
        );
    }

    /**
     * @test
     */
    public function valueFromProviderIsAddedToTypedList()
    {
        $value        = new \stdClass();
        $mockProvider = $this->getMock('net\\stubbles\\ioc\\InjectionProvider');
        $mockProvider->expects($this->once())
                     ->method('get')
                     ->will($this->returnValue($value));
        $this->assertEquals(array($value),
                            $this->listBinding->withValueFromProvider($mockProvider)
                                              ->getInstance(new ReflectionClass('\\stdClass'),
                                                            'foo'
                            )
        );
    }

    /**
     * @test
     * @expectedException  net\stubbles\ioc\BindingException
     */
    public function invalidValueFromProviderAddedToTypedListThrowsBindingException()
    {
        $mockProvider = $this->getMock('net\\stubbles\\ioc\\InjectionProvider');
        $mockProvider->expects($this->once())
                     ->method('get')
                     ->will($this->returnValue(303));
        $this->listBinding->withValueFromProvider($mockProvider)
                          ->getInstance(new ReflectionClass('\\stdClass'),
                                        'foo'
        );
    }

    /**
     * @test
     * @expectedException  net\stubbles\ioc\BindingException
     */
    public function invalidObjectFromProviderAddedToTypedListThrowsBindingException()
    {
        $mockProvider = $this->getMock('net\\stubbles\\ioc\\InjectionProvider');
        $mockProvider->expects($this->once())
                     ->method('get')
                     ->will($this->returnValue(new \stdClass()));
        $this->listBinding->withValueFromProvider($mockProvider)
                          ->getInstance(new ReflectionClass('net\\stubbles\\ioc\\InjectionProvider'),
                                        'foo'
        );
    }

    /**
     * @test
     */
    public function valueFromProviderClassIsAddedToList()
    {
        $mockProvider = $this->getMock('net\\stubbles\\ioc\\InjectionProvider');
        $mockProvider->expects($this->once())
                     ->method('get')
                     ->will($this->returnValue(303));
        $mockProviderClass = $this->getMockClass('net\\stubbles\\ioc\\InjectionProvider');
        $this->prepareInjector($mockProvider, $mockProviderClass);
        $this->assertEquals(array(303),
                            $this->listBinding->withValueFromProvider($mockProviderClass)
                                              ->getInstance('int', 'foo')
        );
    }

    /**
     * @test
     */
    public function valueFromProviderClassIsAddedToTypedList()
    {
        $value        = new \stdClass();
        $mockProvider = $this->getMock('net\\stubbles\\ioc\\InjectionProvider');
        $mockProvider->expects($this->once())
                     ->method('get')
                     ->will($this->returnValue($value));
        $mockProviderClass = $this->getMockClass('net\\stubbles\\ioc\\InjectionProvider');
        $this->prepareInjector($mockProvider, $mockProviderClass);
        $this->assertEquals(array($value),
                            $this->listBinding->withValueFromProvider($mockProviderClass)
                                              ->getInstance(new ReflectionClass('\\stdClass'),
                                                            'foo'
                            )
        );
    }

    /**
     * @test
     * @expectedException  net\stubbles\ioc\BindingException
     */
    public function invalidValueFromProviderClassAddedToTypedListThrowsBindingException()
    {
        $mockProvider = $this->getMock('net\\stubbles\\ioc\\InjectionProvider');
        $mockProvider->expects($this->once())
                     ->method('get')
                     ->will($this->returnValue(303));
        $mockProviderClass = $this->getMockClass('net\\stubbles\\ioc\\InjectionProvider');
        $this->prepareInjector($mockProvider, $mockProviderClass);
        $this->listBinding->withValueFromProvider($mockProviderClass)
                          ->getInstance(new ReflectionClass('\\stdClass'),
                                        'foo'
        );
    }

    /**
     * @test
     * @expectedException  net\stubbles\ioc\BindingException
     */
    public function invalidObjectFromProviderClassAddedToTypedListThrowsBindingException()
    {
        $mockProvider = $this->getMock('net\\stubbles\\ioc\\InjectionProvider');
        $mockProvider->expects($this->once())
                     ->method('get')
                     ->will($this->returnValue(new \stdClass()));
        $mockProviderClass = $this->getMockClass('net\\stubbles\\ioc\\InjectionProvider');
        $this->prepareInjector($mockProvider, $mockProviderClass);
        $this->listBinding->withValueFromProvider($mockProviderClass)
                          ->getInstance(new ReflectionClass('net\\stubbles\\ioc\\InjectionProvider'),
                                        'foo'
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
     * @expectedException  net\stubbles\ioc\BindingException
     */
    public function addInvalidProviderClassThrowsBindingException()
    {
        $mockProviderClass = $this->getMockClass('net\\stubbles\\ioc\\InjectionProvider');
        $this->mockInjector->expects($this->once())
                           ->method('getInstance')
                           ->with($this->equalTo($mockProviderClass))
                           ->will($this->returnValue('\\stdClass'));
        $this->listBinding->withValueFromProvider($mockProviderClass)
                          ->getInstance(new ReflectionClass('net\\stubbles\\ioc\\InjectionProvider'),
                                        'foo'
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
}
?>