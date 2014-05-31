<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\lang\reflect;
/**
 * class for testing purposes
 */
class TestProperty1
{
    /**
     * a public property
     *
     * @type  mixed
     * @SomeAnnotation
     */
    public $property;
    /**
     * a protected property
     *
     * @type  mixed
     */
    protected $protectedProperty;
    /**
     * a private property
     *
     * @type  mixed
     */
    private $privateProperty;

    /**
     * returns protected property
     *
     * @return  mixed
     */
    public function getProtectedProperty()
    {
        return $this->protectedProperty;
    }

    /**
     * returns private property
     *
     * @return  mixed
     */
    public function getPrivateProperty()
    {
        return $this->privateProperty;
    }
}
/**
 * another class for testing purposes
 */
class TestProperty2 extends TestProperty1
{
    // intentionally empty
}
/**
 * Test for stubbles\lang\reflect\ReflectionProperty.
 *
 * @group  lang
 * @group  lang_reflect
 */
class ReflectionPropertyTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  ReflectionProperty
     */
    protected $refProperty;

    /**
     * set up the test environment
     */
    public function setUp()
    {
        $this->refProperty = new ReflectionProperty('stubbles\lang\\reflect\TestProperty1', 'property');
    }

    /**
     * @test
     */
    public function reflectionPropertyIsEqualToSelf()
    {
        $this->assertTrue($this->refProperty->equals($this->refProperty));
    }

    /**
     * @test
     */
    public function reflectionPropertyIsEqualToOtherInstanceDenotingSameProperty()
    {
        $otherProperty = new ReflectionProperty('stubbles\lang\\reflect\TestProperty1', 'property');
        $this->assertTrue($this->refProperty->equals($otherProperty));
        $this->assertTrue($otherProperty->equals($this->refProperty));
    }

    /**
     * @test
     */
    public function reflectionPropertyIsNotEqualToOtherProperty()
    {
        $otherProperty = new ReflectionProperty('stubbles\lang\\reflect\TestProperty1', 'privateProperty');
        $this->assertFalse($this->refProperty->equals($otherProperty));
        $this->assertFalse($this->refProperty->equals('foo'));
        $this->assertFalse($otherProperty->equals($this->refProperty));
    }

    /**
     * @test
     */
    public function reflectionPropertyIsNotEqualToAnyOtherType()
    {
        $this->assertFalse($this->refProperty->equals(new \stdClass()));
    }

    /**
     * @test
     */
    public function toStringReturnsStringRepresentationOfClassInstance()
    {
        $this->assertEquals("stubbles\\lang\\reflect\\ReflectionProperty[stubbles\\lang\\reflect\\TestProperty1::property] {\n}\n",
                            (string) $this->refProperty
        );
    }

    /**
     * @test
     */
    public function getDeclaringClassReturnsStubReflectionClassOfDeclaringClass()
    {
        $declaringClass = $this->refProperty->getDeclaringClass();
        $this->assertInstanceOf('stubbles\lang\\reflect\ReflectionClass', $declaringClass);
        $this->assertEquals('stubbles\lang\\reflect\TestProperty1', $declaringClass->getName());
    }

    /**
     * @test
     */
    public function getDeclaringClassReturnsClassInstanceInitiallyProvidedAsArgument()
    {
        $refClass    = new ReflectionClass('stubbles\lang\\reflect\TestProperty1');
        $refProperty = new ReflectionProperty($refClass, 'property');
        $this->assertSame($refClass, $refProperty->getDeclaringClass());

    }

    /**
     * @test
     */
    public function getDeclaringClassIsDeclaringClassIfPropertyNotDefinedInClassInitiallyProvidedAsArgument()
    {
        $refClass    = new ReflectionClass('stubbles\lang\\reflect\TestProperty2');
        $refProperty = new ReflectionProperty($refClass, 'property');
        $this->assertEquals('stubbles\lang\\reflect\TestProperty1', $refProperty->getDeclaringClass()->getName());
    }

    /**
     * @test
     * @since  2.0.0
     * @group  bug164
     */
    public function protectedPropertyIsAccessibleByDefault()
    {
        $instance    = new TestProperty1();
        $refProperty = new ReflectionProperty('stubbles\lang\\reflect\TestProperty1', 'protectedProperty');
        $refProperty->setValue($instance, 303);
        $this->assertEquals(303, $instance->getProtectedProperty());
    }

    /**
     * @test
     * @since  2.0.0
     * @group  bug164
     */
    public function privatePropertyIsAccessibleByDefault()
    {
        $instance    = new TestProperty1();
        $refProperty = new ReflectionProperty('stubbles\lang\\reflect\TestProperty1', 'privateProperty');
        $refProperty->setValue($instance, 303);
        $this->assertEquals(303, $instance->getPrivateProperty());
    }

    /**
     * @test
     */
    public function hasAnnotationReturnsFalseIfAnnotationIsNotSet()
    {
        $refProperty = new ReflectionProperty('stubbles\lang\\reflect\TestProperty1', 'privateProperty');
        $this->assertFalse($refProperty->hasAnnotation('SomeAnnotation'));
    }

    /**
     * @test
     */
    public function hasAnnotationReturnsTrueIfAnnotationIsSet()
    {
        $this->assertTrue($this->refProperty->hasAnnotation('SomeAnnotation'));
    }

    /**
     * @test
     * @expectedException  \ReflectionException
     */
    public function getAnnotationThrowsReflectionExceptionIfAnnotationNotSet()
    {
        $refProperty = new ReflectionProperty('stubbles\lang\\reflect\TestProperty1', 'privateProperty');
        $refProperty->getAnnotation('SomeAnnotation');
    }

    /**
     * @test
     */
    public function getAnnotationReturnsInstanceOfAnnotationIfAnnotationSet()
    {
        $this->assertInstanceOf('stubbles\lang\\reflect\annotation\Annotation',
                                $this->refProperty->getAnnotation('SomeAnnotation')
        );
    }
}
