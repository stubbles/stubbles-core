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
use net\stubbles\lang\Properties;
/**
 * Test for net\stubbles\ioc\binding\PropertyBinding.
 *
 * @since  3.4.0
 * @group  ioc
 * @group  ioc_binding
 */
class PropertyBindingTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  PropertyBinding
     */
    private $propertyBinding;
    /**
     * mocked injector
     *
     * @type  \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockInjector;
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
        $this->mockInjector     = $this->getMockBuilder('net\stubbles\ioc\Injector')
                                       ->disableOriginalConstructor()
                                       ->getMock();
        $this->mockMode         = $this->getMock('net\stubbles\lang\Mode');
        $this->propertyBinding  = new PropertyBinding(new Properties(array('PROD'   => array('foo.bar' => 'baz'),
                                                                           'config' => array('foo.bar' => 'default',
                                                                                             'other'   => 'someValue'
                                                                                       )
                                                                     )
                                                      ),
                                                      $this->mockMode

                                  );
    }

    /**
     * @test
     */
    public function hasValueForRuntimeMode()
    {
        $this->mockMode->expects($this->any())
                       ->method('name')
                       ->will($this->returnValue('PROD'));
        $this->assertTrue($this->propertyBinding->hasProperty('foo.bar'));
    }

    /**
     * @test
     */
    public function returnsProdValueForRuntimeMode()
    {
        $this->mockMode->expects($this->any())
                       ->method('name')
                       ->will($this->returnValue('PROD'));
        $this->assertEquals('baz',
                            $this->propertyBinding->getInstance($this->mockInjector, 'foo.bar')
        );
    }

    /**
     * @test
     */
    public function hasValueForDifferentRuntimeMode()
    {
        $this->mockMode->expects($this->any())
                       ->method('name')
                       ->will($this->returnValue('DEV'));
        $this->assertTrue($this->propertyBinding->hasProperty('foo.bar'));
    }

    /**
     * @test
     */
    public function returnsConfigValueForDifferentRuntimeMode()
    {
        $this->mockMode->expects($this->any())
                       ->method('name')
                       ->will($this->returnValue('DEV'));
        $this->assertEquals('default',
                            $this->propertyBinding->getInstance($this->mockInjector, 'foo.bar')
        );
    }

    /**
     * @test
     */
    public function hasValueWhenNoSpecificForRuntimeModeSet()
    {
        $this->mockMode->expects($this->any())
                       ->method('name')
                       ->will($this->returnValue('PROD'));
        $this->assertTrue($this->propertyBinding->hasProperty('other'));
    }

    /**
     * @test
     */
    public function returnsConfigValueWhenNoSpecificForRuntimeModeSet()
    {
        $this->mockMode->expects($this->any())
                       ->method('name')
                       ->will($this->returnValue('PROD'));
        $this->assertEquals('someValue',
                            $this->propertyBinding->getInstance($this->mockInjector, 'other')
        );
    }

    /**
     * @test
     */
    public function doesNotHaveValueWhenPropertyNotSet()
    {
        $this->mockMode->expects($this->any())
                       ->method('name')
                       ->will($this->returnValue('PROD'));
        $this->assertFalse($this->propertyBinding->hasProperty('does.not.exist'));
    }

    /**
     * @test
     * @expectedException  net\stubbles\ioc\binding\BindingException
     * @expectedExceptionMessage  Missing property does.not.exist in runtime mode PROD
     */
    public function throwsBindingExceptionWhenPropertyNotSet()
    {
        $this->mockMode->expects($this->any())
                       ->method('name')
                       ->will($this->returnValue('PROD'));
        $this->propertyBinding->getInstance($this->mockInjector, 'does.not.exist');
    }
}
