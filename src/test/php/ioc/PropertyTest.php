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
use bovigo\callmap\NewInstance;
use stubbles\lang\Mode;
use stubbles\lang\Properties;
use stubbles\test\ioc\PropertyReceiver;

use function bovigo\assert\assert;
use function bovigo\assert\predicate\equals;
use function bovigo\assert\predicate\isSameAs;
/**
 * Test for property bindings.
 *
 * @group  ioc
 * @since  3.4.0
 */
class PropertyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * injector to create instance
     *
     * @type  Injector
     */
    private $injector;
    /**
     * properties to be bound
     *
     * @type  \stubbles\lang\Properties
     */
    private $properties;
    /**
     * mocked runtime mode
     *
     * @type  \stubbles\lang\Mode
     */
    private $mode;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->properties = new Properties(
                ['PROD'   => ['example.foo' => 'baz'],
                 'config' => ['example.foo' => 'default',
                              'example.bar' => 'someValue'
                             ]
                ]
        );
        $this->mode = NewInstance::of(Mode::class);
        $binder = new Binder();
        $binder->bindProperties($this->properties, $this->mode);
        $this->injector = $binder->getInjector();
    }

    /**
     * @test
     */
    public function setsCorrectPropertiesInRuntimeModeWithSpecificProperties()
    {
        $this->mode->mapCalls(['name' => 'PROD']);
        $propertyReceiver = $this->injector->getInstance(PropertyReceiver::class);
        assert($propertyReceiver->foo, equals('baz'));
        assert($propertyReceiver->bar, equals('someValue'));
    }

    /**
     * @test
     */
    public function setsCorrectPropertiesInRuntimeModeWithDefaultProperties()
    {
        $this->mode->mapCalls(['name' => 'DEV']);
        $propertyReceiver = $this->injector->getInstance(PropertyReceiver::class);
        assert($propertyReceiver->foo, equals('default'));
        assert($propertyReceiver->bar, equals('someValue'));
    }

    /**
     * @test
     * @expectedException  stubbles\ioc\binding\BindingException
     * @expectedExceptionMessage  Can not inject into stubbles\test\ioc\PropertyReceiver::__construct($foo). No binding for type __PROPERTY__ (named "example.foo") specified.
     */
    public function instanceCreationThrowsBindingExceptionWhenNoPropertiesBound()
    {
        $binder = new Binder();
        $binder->getInjector()->getInstance(PropertyReceiver::class);
    }

    /**
     * @test
     * @since  5.1.0
     */
    public function propertyInstanceIsBound()
    {
        assert(
                $this->injector->getInstance(Properties::class, 'config.ini'),
                isSameAs($this->properties)
        );
    }
}
