<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles
 */
namespace net\stubbles\lang\reflect;
use org\stubbles\test\lang\reflect\TestWithMethodsAndProperties;
use org\stubbles\test\lang\reflect\TestWithOutMethodsAndProperties;
/**
 * Test for net\stubbles\lang\reflect\ReflectionObject.
 *
 * @group  lang
 * @group  lang_reflect
 */
class ReflectionObjectTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * instance 1 to test
     *
     * @type  ReflectionObject
     */
    protected $refClass1;
    /**
     * instance 2 to test
     *
     * @type  ReflectionObject
     */
    protected $refClass2;
    /**
     * the first reflected object
     *
     * @type  stubTestWithMethodsAndProperties
     */
    protected $reflectedObject1;
    /**
     * the second reflected object
     *
     * @type  stubTestWithOutMethodsAndProperties
     */
    protected $reflectedObject2;

    /**
     * set up the test environment
     */
    public function setUp()
    {
        $this->reflectedObject1 = new TestWithMethodsAndProperties();
        $this->refClass1        = new ReflectionObject($this->reflectedObject1);
        $this->reflectedObject2 = new TestWithOutMethodsAndProperties();
        $this->refClass2        = new ReflectionObject($this->reflectedObject2);
    }

    /**
     * @test
     */
    public function reflectionClassIsEqualToItself()
    {
        $this->assertTrue($this->refClass1->equals($this->refClass1));
        $this->assertTrue($this->refClass2->equals($this->refClass2));
    }

    /**
     * @test
     */
    public function reflectionClassIsEqualToAnotherInstanceForSameReflectedObject()
    {
        $refClass = new ReflectionObject($this->reflectedObject1);
        $this->assertTrue($this->refClass1->equals($refClass));
        $this->assertTrue($refClass->equals($this->refClass1));
    }

    /**
     * @test
     */
    public function reflectionClassIsNotEqualToAnotherInstanceForSameReflectedClass()
    {
        $refClass = new ReflectionObject(new TestWithMethodsAndProperties());
        $this->assertFalse($this->refClass1->equals($refClass));
        $this->assertFalse($refClass->equals($this->refClass1));
    }

    /**
     * @test
     */
    public function reflectionClassIsNotEqualForAnythingElse()
    {
        $this->assertFalse($this->refClass1->equals($this->refClass2));
        $this->assertFalse($this->refClass1->equals('foo'));
        $this->assertFalse($this->refClass2->equals($this->refClass1));
    }

    /**
     * @test
     */
    public function toStringReturnsStringRepresentationWithClassNameOfReflectedClass()
    {
        $this->assertEquals("net\\stubbles\\lang\\reflect\\ReflectionObject[org\\stubbles\\test\\lang\\reflect\\TestWithMethodsAndProperties] {\n}\n",
                            (string) $this->refClass1
        );
        $this->assertEquals("net\\stubbles\\lang\\reflect\\ReflectionObject[org\\stubbles\\test\\lang\\reflect\\TestWithOutMethodsAndProperties] {\n}\n",
                            (string) $this->refClass2
        );
    }

    /**
     * @test
     */
    public function getObjectInstanceReturnsOriginalInstance()
    {
        $this->assertSame($this->reflectedObject1, $this->refClass1->getObjectInstance());
        $this->assertSame($this->reflectedObject2, $this->refClass2->getObjectInstance());
    }

    /**
     * @test
     */
    public function getConstructorReturnsNullForNonExistingConstructor()
    {
        $this->assertNull($this->refClass2->getConstructor());
    }

    /**
     * @test
     */
    public function getConstructorReturnsReflectionMethodForExistingConstructor()
    {
        $refMethod = $this->refClass1->getConstructor();
        $this->assertInstanceOf('net\\stubbles\\lang\\reflect\\ReflectionMethod',
                                $refMethod
        );
        $this->assertEquals('__construct', $refMethod->getName());
    }

    /**
     * @test
     */
    public function getMethodReturnsNullForNonExistingMethod()
    {
        $this->assertNull($this->refClass1->getMethod('doesNotExist'));
    }

    /**
     * @test
     */
    public function getMethodReturnsReflectionMethodForExistingMethod()
    {
        $refMethod = $this->refClass1->getMethod('methodA');
        $this->assertInstanceOf('net\\stubbles\\lang\\reflect\\ReflectionMethod',
                                $refMethod
        );
        $this->assertEquals('methodA', $refMethod->getName());
    }

    /**
     * @test
     */
    public function getMethodsReturnsEmptyListIfClassHasNoMethods()
    {
        $this->assertEquals(array(), $this->refClass2->getMethods());
    }

    /**
     * @test
     */
    public function getMethodsReturnsListOfReflectionMethodForAllClassMethods()
    {
        $refMethods = $this->refClass1->getMethods();
        $this->assertEquals(4, count($refMethods));
        foreach ($refMethods as $refMethod) {
            $this->assertInstanceOf('net\\stubbles\\lang\\reflect\\ReflectionMethod',
                                    $refMethod
            );
        }
    }

    /**
     * @test
     */
    public function getMethodsByMatcherReturnsListOfReflectionMethodForMatchingMethods()
    {
        $mockMethodMatcher = $this->getMock('net\\stubbles\\lang\\reflect\\matcher\\MethodMatcher');
        $mockMethodMatcher->expects($this->exactly(4))
                          ->method('matchesMethod')
                          ->will($this->onConsecutiveCalls(true, true, false, false));
        $mockMethodMatcher->expects($this->exactly(2))
                          ->method('matchesAnnotatableMethod')
                          ->will($this->onConsecutiveCalls(false, true));
        $refMethods = $this->refClass1->getMethodsByMatcher($mockMethodMatcher);
        $this->assertEquals(1, count($refMethods));
        $this->assertInstanceOf('net\\stubbles\\lang\\reflect\\ReflectionMethod',
                                $refMethods[0]
        );
    }

    /**
     * @test
     */
    public function getPropertyReturnsNullForNonExistingProperty()
    {
        $this->assertNull($this->refClass1->getProperty('doesNotExist'));
    }

    /**
     * @test
     */
    public function getPropertyReturnsReflectionPropertyForExistingProperty()
    {
        $refProperty = $this->refClass1->getProperty('property1');
        $this->assertInstanceOf('net\\stubbles\\lang\\reflect\\ReflectionProperty',
                                $refProperty
        );
        $this->assertEquals('property1', $refProperty->getName());
    }

    /**
     * @test
     */
    public function getPropertiesReturnsEmptyListIfClassHasNoProperties()
    {
        $this->assertEquals(array(), $this->refClass2->getProperties());
    }

    /**
     * @test
     */
    public function getPropertiesReturnsListOfReflectionPropertyForAllClassProperties()
    {
        $refProperties = $this->refClass1->getProperties();
        $this->assertEquals(3, count($refProperties));
        foreach ($refProperties as $refProperty) {
            $this->assertInstanceOf('net\\stubbles\\lang\\reflect\\ReflectionProperty',
                                    $refProperty
            );
        }
    }

    /**
     * @test
     */
    public function getPropertiesByMatcherReturnsListOfReflectionPropertyForMatchingProperties()
    {
        $mockPropertyMatcher = $this->getMock('net\\stubbles\\lang\\reflect\\matcher\\PropertyMatcher');
        $mockPropertyMatcher->expects($this->exactly(3))
                            ->method('matchesProperty')
                            ->will($this->onConsecutiveCalls(true, false, true));
        $mockPropertyMatcher->expects($this->exactly(2))
                            ->method('matchesAnnotatableProperty')
                            ->will($this->onConsecutiveCalls(true, false));
        $refProperties = $this->refClass1->getPropertiesByMatcher($mockPropertyMatcher);
        $this->assertEquals(1, count($refProperties));
        $this->assertInstanceOf('net\\stubbles\\lang\\reflect\\ReflectionProperty',
                                $refProperties[0]
        );
    }

    /**
     * @test
     */
    public function getInterfacesReturnsEmptyArrayIfClassDoesNotImplementInterfaces()
    {
        $this->assertEquals(array(), $this->refClass2->getInterfaces());
    }

    /**
     * @test
     */
    public function getInterfacesReturnsListOfReflectionClassWithAllImplementedInterfaces()
    {
        $refClasses = $this->refClass1->getInterfaces();
        $this->assertEquals(1, count($refClasses));
        foreach ($refClasses as $refClass) {
            $this->assertInstanceOf('net\\stubbles\\lang\\reflect\\ReflectionClass',
                                    $refClass
            );
            $this->assertEquals('org\\stubbles\\test\\lang\\reflect\\TestInterface',
                                $refClass->getName()
            );
        }
    }

    /**
     * @test
     */
    public function getParentClassReturnsNullIfThereIsNoParentClass()
    {
        $this->assertNull($this->refClass2->getParentClass());
    }

    /**
     * @test
     */
    public function getParentClassReturnsReflectionClassInstanceForParentClass()
    {
        $refClass = $this->refClass1->getParentClass();
        $this->assertInstanceOf('net\\stubbles\\lang\\reflect\\ReflectionClass',
                                $refClass
        );
        $this->assertEquals('org\\stubbles\\test\\lang\\reflect\\TestWithOutMethodsAndProperties',
                            $refClass->getName()
        );
    }

    /**
     * @test
     */
    public function getExtensionReturnsNullIfClassIsNotPartOfAnExtension()
    {
        $this->assertNull($this->refClass1->getExtension());
    }

    /**
     * @test
     */
    public function getExtensionReturnsReflectionExtensionIfClassIsPartOfAnExtension()
    {
        $refClass = new ReflectionClass('\ArrayIterator');
        $this->assertInstanceOf('net\\stubbles\\lang\\reflect\\ReflectionExtension',
                                $refClass->getExtension()
        );
    }

    /**
     * @test
     */
    public function reflectedClassesAreAlwaysObjects()
    {
        $this->assertTrue($this->refClass1->isObject());
    }

    /**
     * @test
     */
    public function reflectedClassesAreNeverPrimitiveTypes()
    {
        $this->assertFalse($this->refClass1->isPrimitive());
    }
}
?>