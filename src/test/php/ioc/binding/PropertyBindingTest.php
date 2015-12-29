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
use stubbles\ioc\Injector;
use stubbles\lang\Mode;
use stubbles\lang\Properties;
use stubbles\lang\Secret;

use function bovigo\assert\assert;
use function bovigo\assert\assertFalse;
use function bovigo\assert\assertTrue;
use function bovigo\assert\predicate\equals;
use function bovigo\assert\predicate\isInstanceOf;
use function stubbles\lang\reflect;
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
     * @param  \stubbles\lang\Secret  $password
     * @Property('example.password')
     */
    public function __construct(Secret $password)
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
        $this->injector = NewInstance::of(Injector::class);
        $this->mode     = NewInstance::of(Mode::class)
                ->mapCalls(['name' => 'PROD']);
        $this->propertyBinding = new PropertyBinding(
                new Properties(['PROD'   => ['foo.bar' => 'baz',
                                             'baz'     => __CLASS__ . '.class'
                                            ],
                                'config' => ['foo.bar'          => 'default',
                                             'other'            => 'someValue',
                                             'baz'              => Properties::class . '.class'
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
        assert(
                $this->propertyBinding->getInstance($this->injector, 'foo.bar'),
                equals('baz')
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
        assert(
                $this->propertyBinding->getInstance($this->injector, 'foo.bar'),
                equals('default')
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
        assert(
                $this->propertyBinding->getInstance($this->injector, 'other'),
                equals('someValue')
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
        assert(
                $this->propertyBinding->getInstance($this->injector, 'baz'),
                equals(reflect(__CLASS__))
        );
    }

    /**
     * @test
     * @since  4.1.0
     */
    public function returnsParsedValuesForCommonProperties()
    {
        $this->mode->mapCalls(['name' => 'DEV']);
        assert(
                $this->propertyBinding->getInstance($this->injector, 'baz'),
                equals(reflect(Properties::class))
        );
    }

    /**
     * @test
     * @since  4.1.3
     */
    public function propertyBindingUsedWhenParamHasTypeHintButIsAnnotated()
    {
        try {
            $binder     = new Binder();
            $properties = new Properties(
                        ['config' => ['example.password' => 'somePassword']]
                    );
            $binder->bindProperties($properties, $this->mode);
            $example = $binder->getInjector()->getInstance(Example::class);
            assert($example->password, isInstanceOf(Secret::class));
        } finally {
            // ensure all references are removed to clean up environment
            // otherwise all *SecretTests will fail
            unset($properties);
            $example->password = null;
            unset($example);
            unset($binder);
            gc_collect_cycles();
        }
    }
}
