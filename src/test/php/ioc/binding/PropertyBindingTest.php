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
use bovigo\callmap\NewInstance;
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
    private $injector;
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
        $this->injector = NewInstance::of('stubbles\ioc\Injector');
        $this->mode     = NewInstance::of('stubbles\lang\Mode')
                ->mapCalls(['name' => 'PROD']);
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
                $this->mode

        );
    }

    /**
     * @test
     */
    public function hasValueForRuntimeMode()
    {
        assertTrue($this->propertyBinding->hasProperty('foo.bar'));
    }

    /**
     * @test
     */
    public function returnsProdValueForRuntimeMode()
    {
        assertEquals(
                'baz',
                $this->propertyBinding->getInstance($this->injector, 'foo.bar')
        );
    }

    /**
     * @test
     */
    public function hasValueForDifferentRuntimeMode()
    {
        $this->mode->mapCalls(['name' => 'DEV']);
        assertTrue($this->propertyBinding->hasProperty('foo.bar'));
    }

    /**
     * @test
     */
    public function returnsConfigValueForDifferentRuntimeMode()
    {
        $this->mode->mapCalls(['name' => 'DEV']);
        assertEquals(
                'default',
                $this->propertyBinding->getInstance($this->injector, 'foo.bar')
        );
    }

    /**
     * @test
     */
    public function hasValueWhenNoSpecificForRuntimeModeSet()
    {
        assertTrue($this->propertyBinding->hasProperty('other'));
    }

    /**
     * @test
     */
    public function returnsConfigValueWhenNoSpecificForRuntimeModeSet()
    {
        assertEquals(
                'someValue',
                $this->propertyBinding->getInstance($this->injector, 'other')
        );
    }

    /**
     * @test
     */
    public function doesNotHaveValueWhenPropertyNotSet()
    {
        assertFalse($this->propertyBinding->hasProperty('does.not.exist'));
    }

    /**
     * @test
     * @expectedException  stubbles\ioc\binding\BindingException
     * @expectedExceptionMessage  Missing property does.not.exist in runtime mode PROD
     */
    public function throwsBindingExceptionWhenPropertyNotSet()
    {
        $this->propertyBinding->getInstance($this->injector, 'does.not.exist');
    }

    /**
     * @test
     * @since  4.1.0
     */
    public function returnsParsedValuesForModeSpecificProperties()
    {
        assertEquals(
                lang\reflect(__CLASS__),
                $this->propertyBinding->getInstance($this->injector, 'baz')
        );
    }

    /**
     * @test
     * @since  4.1.0
     */
    public function returnsParsedValuesForCommonProperties()
    {
        $this->mode->mapCalls(['name' => 'DEV']);
        assertEquals(
                lang\reflect('stubbles\lang\Properties'),
                $this->propertyBinding->getInstance($this->injector, 'baz')
        );
    }

    /**
     * @test
     * @since  4.1.3
     */
    public function propertyBindingUsedWhenParamHasTypeHintButIsAnnotated()
    {
        $binder     = new Binder();
        $properties = new Properties(
                    ['config' => ['example.password' => 'somePassword']]
                );
        $binder->bindProperties($properties, $this->mode);
        $example = $binder->getInjector()->getInstance('stubbles\ioc\binding\Example');
        assertInstanceOf('stubbles\lang\SecureString', $example->password);
        // ensure all references are removed to clean up environment
        // otherwise all *SecureStringTests will fail
        $properties = null;
        $example->password = null;
        $binder = null;
        gc_collect_cycles();
    }
}
