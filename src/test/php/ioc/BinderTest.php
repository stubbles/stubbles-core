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
use stubbles\lang\reflect\NewInstance;
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
     * @since  2.0.0
     * @test
     */
    public function addBindingReturnsAddedBinding()
    {
        $binding = NewInstance::of('stubbles\ioc\binding\Binding');
        assertSame(
                $binding,
                $this->binder->addBinding($binding)
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function bindCreatesClassBinding()
    {
        assertInstanceOf(
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
        assertInstanceOf(
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
        assertInstanceOf(
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
        assertInstanceOf(
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
        assertSame($injector, $injector->getInstance('stubbles\ioc\Injector'));
    }

    /**
     * @since  3.4.0
     * @test
     */
    public function bindPropertiesCreatesBinding()
    {
        $properties = new Properties([]);
        assertSame(
                $properties,
                $this->binder->bindProperties(
                        $properties,
                        NewInstance::of('stubbles\lang\Mode')
                )
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
        $properties = new Properties(['config' => ['foo' => 'bar']]);
        assertEquals(
                $properties,
                $this->binder->bindPropertiesFromFile(
                        $file->url(),
                        NewInstance::of('stubbles\lang\Mode')
                )
        );
    }
}
