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
use net\stubbles\lang;
/**
 * Test for net\stubbles\ioc\RuntimeModePropertiesProvider.
 *
 * @group  ioc
 * @since  3.4.0
 */
class RuntimeModePropertiesProviderTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  RuntimeModePropertiesProvider
     */
    private $runtimeModePropertiesProvider;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->runtimeModePropertiesProvider = new RuntimeModePropertiesProvider(new Properties(array('prod'   => array('foo.bar' => 'baz'),
                                                                                                      'common' => array('foo.bar' => 'dummy',
                                                                                                                        'other'   => 'someValue'
                                                                                                                  )
                                                                                                )
                                                                                 )
                                               );
    }

    /**
     * @test
     */
    public function annotationsPresentOnConstructor()
    {
        $constructor = lang\reflectConstructor($this->runtimeModePropertiesProvider);
        $this->assertTrue($constructor->hasAnnotation('Inject'));
        $this->assertTrue($constructor->hasAnnotation('Named'));
        $this->assertEquals('config', $constructor->getAnnotation('Named')->getName());
    }

    /**
     * @test
     */
    public function annotationsPresentOnSetModeMethod()
    {
        $method = lang\reflect($this->runtimeModePropertiesProvider, 'setMode');
        $this->assertTrue($method->hasAnnotation('Inject'));
        $this->assertTrue($method->getAnnotation('Inject')->isOptional());
    }

    /**
     * created runtime mode with given name
     *
     * @param   string  $name
     * @return  \PHPUnit_Framework_MockObject_MockObject
     */
    private function createMockMode($name)
    {
        $mockMode = $this->getMock('net\stubbles\lang\Mode');
        $mockMode->expects($this->any())
                 ->method('name')
                 ->will($this->returnValue($name));
        return $mockMode;
    }

    /**
     * @test
     */
    public function returnsCommonValueWhenNoRuntimeModeSet()
    {
        $this->assertEquals('dummy', $this->runtimeModePropertiesProvider->get('foo.bar'));
    }

    /**
     * @test
     */
    public function returnsCorrectValueWhenRuntimeModeSet()
    {
        $this->assertEquals('baz',
                            $this->runtimeModePropertiesProvider->setMode($this->createMockMode('PROD'))
                                                                ->get('foo.bar')
        );
    }

    /**
     * @test
     */
    public function returnsCommonValueWhenOtherRuntimeModeSet()
    {
        $this->assertEquals('dummy',
                            $this->runtimeModePropertiesProvider->setMode($this->createMockMode('DEV'))
                                                                ->get('foo.bar')
        );
    }

    /**
     * @test
     */
    public function returnsCommonValueWhenRuntimeModeSetButNoSpecificValue()
    {
        $this->assertEquals('someValue',
                            $this->runtimeModePropertiesProvider->setMode($this->createMockMode('PROD'))
                                                                ->get('other')
        );
    }

    /**
     * @test
     * @expectedException  net\stubbles\ioc\binding\BindingException
     * @expectedExceptionMessage  Missing property doesNotExist
     */
    public function throwsBindingExceptionWhenPropertyNotSet()
    {
        $this->runtimeModePropertiesProvider->get('doesNotExist');
    }

    /**
     * @test
     * @expectedException  net\stubbles\ioc\binding\BindingException
     * @expectedExceptionMessage  Missing property doesNotExist in runtime mode PROD
     */
    public function throwsBindingExceptionWhenPropertyNotSetWithRuntimeModeSet()
    {
        $this->runtimeModePropertiesProvider->setMode($this->createMockMode('PROD'))
                                            ->get('doesNotExist');
    }
}
