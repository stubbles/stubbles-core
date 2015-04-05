<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\ioc\binding;
use stubbles\ioc\Binder;
use stubbles\lang;
use stubbles\lang\Properties;
use stubbles\lang\SecureString;
/**
 * Class used for tests.
 *
 * @since  4.1.3
 */
class Example
{
    public $password;
    /**
     * constructor
     *
     * @param  \stubbles\lang\SecureString  $password
     * @Property('example.password')
     */
    public function __construct(SecureString $password)
    {
        $this->password = $password;
    }
}
/**
 * Test for stubbles\ioc\binding\PropertyBinding.
 *
 * @since  3.4.0
 * @group  ioc
 * @group  ioc_binding
 */
class PropertyBindingTest extends \PHPUnit_Framework_TestCase
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
        $this->mockInjector = $this->getMockBuilder('stubbles\ioc\Injector')
                ->disableOriginalConstructor()
                ->getMock();
        $this->mockMode        = $this->getMock('stubbles\lang\Mode');
        $this->propertyBinding = new PropertyBinding(
                new Properties(['PROD'   => ['foo.bar' => 'baz',
                                             'baz'     => __CLASS__ . '.class'
                                            ],
                                'config' => ['foo.bar'          => 'default',
                                             'other'            => 'someValue',
                                             'baz'              => 'stubbles\lang\Properties.class'
                                            ]
                               ]
                ),
                $this->mockMode

        );
    }

    /**
     * @test
     */
    public function hasValueForRuntimeMode()
    {
        $this->mockMode->method('name')->will($this->returnValue('PROD'));
        $this->assertTrue($this->propertyBinding->hasProperty('foo.bar'));
    }

    /**
     * @test
     */
    public function returnsProdValueForRuntimeMode()
    {
        $this->mockMode->method('name')->will($this->returnValue('PROD'));
        $this->assertEquals(
                'baz',
                $this->propertyBinding->getInstance($this->mockInjector, 'foo.bar')
        );
    }

    /**
     * @test
     */
    public function hasValueForDifferentRuntimeMode()
    {
        $this->mockMode->method('name')->will($this->returnValue('DEV'));
        $this->assertTrue($this->propertyBinding->hasProperty('foo.bar'));
    }

    /**
     * @test
     */
    public function returnsConfigValueForDifferentRuntimeMode()
    {
        $this->mockMode->method('name')->will($this->returnValue('DEV'));
        $this->assertEquals(
                'default',
                $this->propertyBinding->getInstance($this->mockInjector, 'foo.bar')
        );
    }

    /**
     * @test
     */
    public function hasValueWhenNoSpecificForRuntimeModeSet()
    {
        $this->mockMode->method('name')->will($this->returnValue('PROD'));
        $this->assertTrue($this->propertyBinding->hasProperty('other'));
    }

    /**
     * @test
     */
    public function returnsConfigValueWhenNoSpecificForRuntimeModeSet()
    {
        $this->mockMode->method('name')->will($this->returnValue('PROD'));
        $this->assertEquals('someValue',
                            $this->propertyBinding->getInstance($this->mockInjector, 'other')
        );
    }

    /**
     * @test
     */
    public function doesNotHaveValueWhenPropertyNotSet()
    {
        $this->mockMode->method('name')->will($this->returnValue('PROD'));
        $this->assertFalse($this->propertyBinding->hasProperty('does.not.exist'));
    }

    /**
     * @test
     * @expectedException  stubbles\ioc\binding\BindingException
     * @expectedExceptionMessage  Missing property does.not.exist in runtime mode PROD
     */
    public function throwsBindingExceptionWhenPropertyNotSet()
    {
        $this->mockMode->method('name')->will($this->returnValue('PROD'));
        $this->propertyBinding->getInstance($this->mockInjector, 'does.not.exist');
    }

    /**
     * @test
     * @since  4.1.0
     */
    public function returnsParsedValuesForModeSpecificProperties()
    {
        $this->mockMode->method('name')->will($this->returnValue('PROD'));
        $this->assertEquals(
                lang\reflect(__CLASS__),
                $this->propertyBinding->getInstance($this->mockInjector, 'baz')
        );
    }

    /**
     * @test
     * @since  4.1.0
     */
    public function returnsParsedValuesForCommonProperties()
    {
        $this->mockMode->method('name')->will($this->returnValue('DEV'));
        $this->assertEquals(
                lang\reflect('stubbles\lang\Properties'),
                $this->propertyBinding->getInstance($this->mockInjector, 'baz')
        );
    }

    /**
     * @test
     * @since  4.1.3
     */
    public function propertyBindingUsedWhenParamHasTypeHintButIsAnnotated()
    {
        $this->mockMode->method('name')->will($this->returnValue('PROD'));
        $binder     = new Binder();
        $properties = new Properties(
                    ['config' => ['example.password' => 'somePassword']]
                );
        $binder->bindProperties($properties, $this->mockMode);
        $example = $binder->getInjector()->getInstance('stubbles\ioc\binding\Example');
        $this->assertInstanceOf('stubbles\lang\SecureString', $example->password);
        // ensure all references are removed to clean up environment
        // otherwise all *SecureStringTests will fail
        $properties = null;
        $example->password = null;
        $binder = null;
        gc_collect_cycles();
    }
}
