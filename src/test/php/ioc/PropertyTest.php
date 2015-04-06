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
use stubbles\lang\Properties;
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
     * @type  \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockMode;

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
        $this->mockMode = $this->getMock('stubbles\lang\Mode');
        $binder = new Binder();
        $binder->bindProperties($this->properties, $this->mockMode);
        $this->injector = $binder->getInjector();
    }

    /**
     * @test
     */
    public function setsCorrectPropertiesInRuntimeModeWithSpecificProperties()
    {
        $this->mockMode->method('name')->will(returnValue('PROD'));
        $propertyReceiver = $this->injector->getInstance('stubbles\test\ioc\PropertyReceiver');
        assertEquals('baz', $propertyReceiver->foo);
        assertEquals('someValue', $propertyReceiver->bar);
    }

    /**
     * @test
     */
    public function setsCorrectPropertiesInRuntimeModeWithDefaultProperties()
    {
        $this->mockMode->method('name')->will(returnValue('DEV'));
        $propertyReceiver = $this->injector->getInstance('stubbles\test\ioc\PropertyReceiver');
        assertEquals('default', $propertyReceiver->foo);
        assertEquals('someValue', $propertyReceiver->bar);
    }

    /**
     * @test
     * @expectedException  stubbles\ioc\binding\BindingException
     * @expectedExceptionMessage  Can not inject into stubbles\test\ioc\PropertyReceiver::__construct($foo). No binding for type __PROPERTY__ (named "example.foo") specified.
     */
    public function instanceCreationThrowsBindingExceptionWhenNoPropertiesBound()
    {
        $binder = new Binder();
        $binder->getInjector()->getInstance('stubbles\test\ioc\PropertyReceiver');
    }

    /**
     * @test
     * @since  5.1.0
     */
    public function propertyInstanceIsBound()
    {
        assertSame(
                $this->properties,
                $this->injector->getInstance('stubbles\lang\Properties', 'config.ini')
        );
    }
}
