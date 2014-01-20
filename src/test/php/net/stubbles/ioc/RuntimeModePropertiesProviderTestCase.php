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
        $mockMode = $this->getMock('net\stubbles\lang\Mode');
        $mockMode->expects($this->once())
                 ->method('name')
                 ->will($this->returnValue('PROD'));
        $this->assertEquals('baz',
                            $this->runtimeModePropertiesProvider->setMode($mockMode)
                                                                ->get('foo.bar')
        );
    }

    /**
     * @test
     */
    public function returnsCommonValueWhenOtherRuntimeModeSet()
    {
        $mockMode = $this->getMock('net\stubbles\lang\Mode');
        $mockMode->expects($this->once())
                 ->method('name')
                 ->will($this->returnValue('DEV'));
        $this->assertEquals('dummy',
                            $this->runtimeModePropertiesProvider->setMode($mockMode)
                                                                ->get('foo.bar')
        );
    }

    /**
     * @test
     */
    public function returnsCommonValueWhenRuntimeModeSetButNoSpecificValue()
    {
        $mockMode = $this->getMock('net\stubbles\lang\Mode');
        $mockMode->expects($this->once())
                 ->method('name')
                 ->will($this->returnValue('PROD'));
        $this->assertEquals('someValue',
                            $this->runtimeModePropertiesProvider->setMode($mockMode)
                                                                ->get('other')
        );
    }

    /**
     * @test
     */
    public function returnsNullForNonExistingValue()
    {
        $this->assertNull($this->runtimeModePropertiesProvider->get('doesNotExist'));
    }
}
