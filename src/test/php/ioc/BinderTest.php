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

use function bovigo\assert\assert;
use function bovigo\assert\predicate\equals;
use function bovigo\assert\predicate\isInstanceOf;
use function bovigo\assert\predicate\isSameAs;
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
        assert($this->binder->addBinding($binding), isSameAs($binding));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function bindCreatesClassBinding()
    {
        assert(
                $this->binder->bind('example\MyInterface'),
                isInstanceOf(ClassBinding::class)
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function bindConstantCreatesBinding()
    {
        assert(
                $this->binder->bindConstant('foo'),
                isInstanceOf(ConstantBinding::class)
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function bindListCreatesBinding()
    {
        assert($this->binder->bindList('foo'), isInstanceOf(ListBinding::class));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function bindMapCreatesBinding()
    {
        assert($this->binder->bindMap('foo'), isInstanceOf(MapBinding::class));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function createdInjectorCanRetrieveItself()
    {
        $binder = new Binder();
        $injector = $binder->getInjector();
        assert($injector->getInstance(Injector::class), isSameAs($injector));
    }

    /**
     * @since  3.4.0
     * @test
     */
    public function bindPropertiesCreatesBinding()
    {
        $properties = new Properties([]);
        assert(
                $this->binder->bindProperties($properties, 'PROD'),
                isSameAs($properties)
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
        assert(
                $this->binder->bindPropertiesFromFile($file->url(), 'PROD'),
                equals($properties)
        );
    }
}
