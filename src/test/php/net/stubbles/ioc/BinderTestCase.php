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
/**
 * Test for net\stubbles\ioc\Binder
 *
 * @group  ioc
 */
class BinderTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  Binder
     */
    private $binder;
    /**
     * mocked binding index
     *
     * @type  \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockIndex;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->mockIndex = $this->getMock('net\stubbles\ioc\binding\BindingIndex');
        $this->binder    = new Binder($this->mockIndex);
    }

    /**
     * @test
     */
    public function passesSessionScopeToBindingIndex()
    {
        $mockSessionScope = $this->getMock('net\stubbles\ioc\binding\BindingScope');
        $this->mockIndex->expects($this->once())
                        ->method('setSessionScope')
                        ->with($this->equalTo($mockSessionScope));
        $this->assertSame($this->binder,
                          $this->binder->setSessionScope($mockSessionScope));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function addBindingReturnsAddedBinding()
    {
        $mockBinding = $this->getMock('net\stubbles\ioc\binding\Binding');
        $this->mockIndex->expects($this->once())
                        ->method('addBinding')
                        ->with($this->equalTo($mockBinding))
                        ->will($this->returnValue($mockBinding));
        $this->assertSame($mockBinding,
                          $this->binder->addBinding($mockBinding)
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function bindCreatesBinding()
    {
        $mockBinding = $this->getMock('net\stubbles\ioc\binding\Binding');
        $this->mockIndex->expects($this->once())
                        ->method('bind')
                        ->with($this->equalTo('example\MyInterface'))
                        ->will($this->returnValue($mockBinding));
        $this->assertSame($mockBinding,
                          $this->binder->bind('example\MyInterface')
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function bindConstantCreatesBinding()
    {
        $mockBinding = $this->getMock('net\stubbles\ioc\binding\Binding');
        $this->mockIndex->expects($this->once())
                        ->method('bindConstant')
                        ->with($this->equalTo('foo'))
                        ->will($this->returnValue($mockBinding));
        $this->assertSame($mockBinding,
                          $this->binder->bindConstant('foo')
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function bindListCreatesBinding()
    {
        $mockBinding = $this->getMock('net\stubbles\ioc\binding\Binding');
        $this->mockIndex->expects($this->once())
                        ->method('bindList')
                        ->with($this->equalTo('foo'))
                        ->will($this->returnValue($mockBinding));
        $this->assertSame($mockBinding,
                          $this->binder->bindList('foo')
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function bindMapCreatesBinding()
    {
        $mockBinding = $this->getMock('net\stubbles\ioc\binding\Binding');
        $this->mockIndex->expects($this->once())
                        ->method('bindMap')
                        ->with($this->equalTo('foo'))
                        ->will($this->returnValue($mockBinding));
        $this->assertSame($mockBinding,
                          $this->binder->bindMap('foo')
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function hasBindingChecksIndex()
    {
        $this->mockIndex->expects($this->once())
                        ->method('hasBinding')
                        ->with($this->equalTo('\\stdClass'), $this->equalTo('bar'))
                        ->will($this->returnValue(true));
        $this->assertTrue($this->binder->hasBinding('\stdClass', 'bar'));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function hasExplicitBindingChecksIndex()
    {
        $this->mockIndex->expects($this->once())
                        ->method('hasExplicitBinding')
                        ->with($this->equalTo('\\stdClass'), $this->equalTo('bar'))
                        ->will($this->returnValue(true));
        $this->assertTrue($this->binder->hasExplicitBinding('\stdClass', 'bar'));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function hasConstantChecksIndex()
    {
        $this->mockIndex->expects($this->once())
                        ->method('hasConstant')
                        ->with($this->equalTo('foo'))
                        ->will($this->returnValue(true));
        $this->assertTrue($this->binder->hasConstant('foo'));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function addsCreatedInjectorToIndex()
    {
        $mockClassBinding = $this->getMockBuilder('net\stubbles\ioc\binding\ClassBinding')
                                 ->disableOriginalConstructor()
                                 ->getMock();
        $this->mockIndex->expects($this->once())
                        ->method('bind')
                        ->with($this->equalTo('net\stubbles\ioc\Injector'))
                        ->will($this->returnValue($mockClassBinding));
        $mockClassBinding->expects($this->once())
                         ->method('toInstance');
        $this->assertInstanceOf('net\stubbles\ioc\Injector',
                                $this->binder->getInjector()
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function createdInjectorCanRetrieveItself()
    {
        $binder = new Binder();
        $injector = $binder->getInjector();
        $this->assertSame($injector, $injector->getInstance('net\stubbles\ioc\Injector'));
    }
}
?>