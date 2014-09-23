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
use org\bovigo\vfs\vfsStream;
use stubbles\lang\Properties;
/**
 * Test for stubbles\ioc\Binder
 *
 * @group  ioc
 */
class BinderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  Binder
     */
    private $binder;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->binder = new Binder();
    }

    /**
     * @test
     */
    public function passesSessionScopeToScopes()
    {
        $mockScopes = $this->getMock('stubbles\ioc\binding\BindingScopes');
        $mockSessionScope = $this->getMock('stubbles\ioc\binding\BindingScope');
        $mockScopes->expects($this->once())
                   ->method('setSessionScope')
                   ->with($this->equalTo($mockSessionScope));
        $binder = new Binder($mockScopes);
        $this->assertSame(
                $binder,
                $binder->setSessionScope($mockSessionScope)
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function addBindingReturnsAddedBinding()
    {
        $mockBinding = $this->getMock('stubbles\ioc\binding\Binding');
        $this->assertSame(
                $mockBinding,
                $this->binder->addBinding($mockBinding)
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function bindCreatesClassBinding()
    {
        $this->assertInstanceOf(
                'stubbles\ioc\binding\ClassBinding',
                $this->binder->bind('example\MyInterface')
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function bindConstantCreatesBinding()
    {
        $this->assertInstanceOf(
                'stubbles\ioc\binding\ConstantBinding',
                $this->binder->bindConstant('foo')
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function bindListCreatesBinding()
    {
        $this->assertInstanceOf(
                'stubbles\ioc\binding\ListBinding',
                $this->binder->bindList('foo')
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function bindMapCreatesBinding()
    {
        $this->assertInstanceOf(
                'stubbles\ioc\binding\MapBinding',
                $this->binder->bindMap('foo')
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
        $this->assertSame($injector, $injector->getInstance('stubbles\ioc\Injector'));
    }

    /**
     * @since  3.4.0
     * @test
     */
    public function bindPropertiesCreatesBinding()
    {
        $mockMode   = $this->getMock('stubbles\lang\Mode');
        $properties = new Properties([]);
        $this->assertSame(
                $properties,
                $this->binder->bindProperties($properties, $mockMode)
        );
    }

    /**
     * @since  4.0.0
     * @test
     */
    public function bindPropertiesFromFileCreatesBinding()
    {
        $file = vfsStream::newFile('config.ini')
                         ->withContent("[config]\nfoo=bar")
                         ->at(vfsStream::setup());
        $mockMode   = $this->getMock('stubbles\lang\Mode');
        $properties = new Properties(['config' => ['foo' => 'bar']]);
        $this->assertEquals(
                $properties,
                $this->binder->bindPropertiesFromFile($file->url(), $mockMode)
        );
    }
}
