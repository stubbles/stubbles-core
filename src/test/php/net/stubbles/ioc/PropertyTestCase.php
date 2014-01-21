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
use net\stubbles\lang\Properties;
/**
 * Test for property bindings.
 *
 * @group  ioc
 * @since  3.4.0
 */
class PropertyTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * injector to create instance
     *
     * @type  Injector
     */
    private $injector;
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
        $this->mockMode = $this->getMock('net\stubbles\lang\Mode');
        $binder = new Binder();
        $binder->bindProperties(new Properties(array('PROD'   => array('example.foo' => 'baz'),
                                                     'config' => array('example.foo' => 'default',
                                                                       'example.bar' => 'someValue'
                                                                 )
                                               )
                                ),
                                $this->mockMode
        );
        $this->injector = $binder->getInjector();
    }

    /**
     * @test
     */
    public function setsCorrectPropertiesInRuntimeModeWithSpecificProperties()
    {
        $this->mockMode->expects($this->any())
                       ->method('name')
                       ->will($this->returnValue('PROD'));
        $propertyReceiver = $this->injector->getInstance('org\stubbles\test\ioc\PropertyReceiver');
        $this->assertEquals('baz', $propertyReceiver->foo);
        $this->assertEquals('someValue', $propertyReceiver->bar);
    }

    /**
     * @test
     */
    public function setsCorrectPropertiesInRuntimeModeWithDefaultProperties()
    {
        $this->mockMode->expects($this->any())
                       ->method('name')
                       ->will($this->returnValue('DEV'));
        $propertyReceiver = $this->injector->getInstance('org\stubbles\test\ioc\PropertyReceiver');
        $this->assertEquals('default', $propertyReceiver->foo);
        $this->assertEquals('someValue', $propertyReceiver->bar);
    }

    /**
     * @test
     * @expectedException  net\stubbles\ioc\binding\BindingException
     * @expectedExceptionMessage  Can not inject into org\stubbles\test\ioc\PropertyReceiver::setFoo($foo). No binding for type __PROPERTY__ (named "example.foo") specified.
     */
    public function instanceCreationThrowsBindingExceptionWhenNoPropertiesBound()
    {
        $binder = new Binder();
        $binder->getInjector()->getInstance('org\stubbles\test\ioc\PropertyReceiver');
    }
}
