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
use stubbles\ioc\binding\Binding;
use stubbles\ioc\binding\ClassBinding;
use stubbles\ioc\binding\ConstantBinding;
use stubbles\ioc\binding\ListBinding;
use stubbles\ioc\binding\MapBinding;
use stubbles\lang\Mode;
use stubbles\lang\Properties;
use bovigo\callmap\NewInstance;
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
        $binding = NewInstance::of(Binding::class);
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
                ClassBinding::class,
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
                ConstantBinding::class,
                $this->binder->bindConstant('foo')
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function bindListCreatesBinding()
    {
        assertInstanceOf(ListBinding::class, $this->binder->bindList('foo'));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function bindMapCreatesBinding()
    {
        assertInstanceOf(MapBinding::class, $this->binder->bindMap('foo'));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function createdInjectorCanRetrieveItself()
    {
        $binder = new Binder();
        $injector = $binder->getInjector();
        assertSame($injector, $injector->getInstance(Injector::class));
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
                        NewInstance::of(Mode::class)
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
                        NewInstance::of(Mode::class)
                )
        );
    }
}
