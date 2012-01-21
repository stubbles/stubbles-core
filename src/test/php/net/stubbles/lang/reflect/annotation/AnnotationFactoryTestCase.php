<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles
 */
namespace net\stubbles\lang\reflect\annotation;
use net\stubbles\lang\reflect\ReflectionClass;
/**
 * Simple test class with an annotation
 *
 * @MyAnnotation(
 *     foo='bar',
 *     argh=true,
 *     veggie='cucumber'
 * )
 */
class AnyTestClass {
}

/**
 * Annotation with class parameter
 *
 * @MyAnnotation(
 *     foo=net\stubbles\lang\reflect\annotation\AnyTestClass.class
 * )
 */
class AnotherTestClass {
}

/**
 * Annotation without "annotation" in its name
 *
 * @My(foo='bar')
 */
class OneMoreTestClass { }
/**
 * Test for net\stubbles\lang\reflect\annotation\stubAnnotationFactory.
 *
 * @group  lang
 * @group  lang_reflect
 * @group  lang_reflect_annotation
 */
class AnnotationFactoryTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * example comment used for tests
     *
     * @type  string
     */
    protected $comment = "/**\n * a test docblock\n * \n * \n * @param  string  \$foo\n * @StubAnnotation(foo = 'blub');\n */";

    protected $commentComplex = '/**
 * Foobar bla
 *
 * @access public
 * @MyAnnotation(
 *     foo="bar",
 *     argh=45, veggie="tomato"
 * )
 * @AnotherAnnotation(true)
 * @CastedAnnotation[AnotherAnnotation](false)
 * @EmptyAnnotation
 * @AnnotationWithoutClassWithoutValue
 * @AnnotationWithoutClassWithSingleValue(foo)
 * @AnnotationWithoutClassWithMultipleValues(foo="bar", optional=true)
 * @AnnotationWithoutClassWithoutValueCasted[CastedWithoutClass]
 */';

    protected $commentWithClass = '/**
 * Foobar bla
 *
 * @access public
 * @MyAnnotation(
 *     foo=net\stubbles\lang\reflect\annotation\AnyTestClass.class
 * )
 */';
protected $commentComplexForArgument = '/**
 * Foobar bla
 *
 * @access public
 * @MyAnnotation{foo}(
 *     foo="bar",
 *     argh=45, veggie="tomato"
 * )
 * @AnotherAnnotation{foo}(true)
 * @CastedAnnotation{foo}[AnotherAnnotation](false)
 * @AnotherAnnotation{bar}
 * @MyAnnotation{bar}(
 *     foo=net\stubbles\lang\reflect\annotation\AnyTestClass.class
 * )
 */';

    /**
     * test that checking if an annotation is present works as expected
     *
     * @test
     */
    public function has()
    {
        $this->assertFalse(AnnotationFactory::has($this->comment, 'ExampleAnnotation', Annotation::TARGET_CLASS, 'MyClass'));
        $this->assertFalse(AnnotationFactory::has($this->comment, 'StubAnno', Annotation::TARGET_CLASS, 'MyClass'));

        $this->assertTrue(AnnotationFactory::has($this->commentComplex, 'MyAnnotation', Annotation::TARGET_CLASS, 'MyClass'));
        $this->assertTrue(AnnotationFactory::has($this->commentComplex, 'AnotherAnnotation', Annotation::TARGET_CLASS, 'MyClass'));
        $this->assertTrue(AnnotationFactory::has($this->commentComplex, 'EmptyAnnotation', Annotation::TARGET_CLASS, 'MyClass'));
    }

    /**
     * test that creating an annotation works as expected
     *
     * @test
     */
    public function create()
    {
        $myAnnotation = AnnotationFactory::create($this->commentComplex, 'MyAnnotation', Annotation::TARGET_CLASS, 'MyClass');
        $this->assertInstanceOf('net\\stubbles\\lang\\reflect\\annotation\\Annotation',
                                $myAnnotation
        );
        $this->assertEquals('MyAnnotation', $myAnnotation->getAnnotationName());
        $this->assertEquals('bar', $myAnnotation->getFoo());
        $this->assertEquals('45', $myAnnotation->getArgh());
        $this->assertEquals('tomato', $myAnnotation->getVeggie());

        $anotherAnnotation = AnnotationFactory::create($this->commentComplex, 'AnotherAnnotation', Annotation::TARGET_CLASS, 'MyClass');
        $this->assertInstanceOf('net\\stubbles\\lang\\reflect\\annotation\\Annotation',
                                $anotherAnnotation
        );
        $this->assertEquals('AnotherAnnotation', $anotherAnnotation->getAnnotationName());
        $this->assertTrue($anotherAnnotation->getValue());

        $emptyAnnotation = AnnotationFactory::create($this->commentComplex, 'EmptyAnnotation', Annotation::TARGET_CLASS, 'MyClass');
        $this->assertInstanceOf('net\\stubbles\\lang\\reflect\\annotation\\Annotation',
                                $emptyAnnotation
        );
        $this->assertEquals('EmptyAnnotation', $emptyAnnotation->getAnnotationName());

        $castedAnnotation = AnnotationFactory::create($this->commentComplex, 'CastedAnnotation', Annotation::TARGET_CLASS, 'MyClass');
        $this->assertInstanceOf('net\\stubbles\\lang\\reflect\\annotation\\Annotation',
                                $castedAnnotation
        );
        $this->assertEquals('AnotherAnnotation', $castedAnnotation->getAnnotationName());
        $this->assertFalse($castedAnnotation->getValue());

        $myAnnotation = AnnotationFactory::create($this->commentWithClass, 'MyAnnotation', Annotation::TARGET_CLASS, 'AnotherClass');
        $this->assertInstanceOf('net\\stubbles\\lang\\reflect\\annotation\\Annotation',
                                $myAnnotation
        );
        $this->assertEquals('MyAnnotation', $myAnnotation->getAnnotationName());
        $this->assertInstanceOf('net\\stubbles\\lang\\reflect\\ReflectionClass',
                                $myAnnotation->getFoo()
        );
    }

    /**
     * test that creating an annotation works as expected
     *
     * @test
     * @expectedException  \ReflectionException
     */
    public function createShouldFail()
    {
        AnnotationFactory::create($this->commentComplex, 'NonExisting', Annotation::TARGET_CLASS, 'MyClass');
    }

    /**
     * test that creating an annotation works as expected
     *
     * @test
     */
    public function createForArgument()
    {
        $myAnnotation = AnnotationFactory::create($this->commentComplexForArgument, 'MyAnnotation#foo', Annotation::TARGET_PARAM, 'MyClass::baz()');
        $this->assertInstanceOf('net\\stubbles\\lang\\reflect\\annotation\\Annotation',
                                $myAnnotation
        );
        $this->assertEquals('bar', $myAnnotation->getFoo());
        $this->assertEquals('45', $myAnnotation->getArgh());
        $this->assertEquals('tomato', $myAnnotation->getVeggie());

        $anotherAnnotation = AnnotationFactory::create($this->commentComplexForArgument, 'AnotherAnnotation#foo', Annotation::TARGET_PARAM, 'MyClass::baz()');
        $this->assertInstanceOf('net\\stubbles\\lang\\reflect\\annotation\\Annotation',
                                $anotherAnnotation
        );
        $this->assertTrue($anotherAnnotation->getValue());

        $castedAnnotation = AnnotationFactory::create($this->commentComplexForArgument, 'CastedAnnotation#foo', Annotation::TARGET_PARAM, 'MyClass::baz()');
        $this->assertInstanceOf('net\\stubbles\\lang\\reflect\\annotation\\Annotation',
                                $castedAnnotation
        );
        $this->assertFalse($castedAnnotation->getValue());

        $emptyAnnotation = AnnotationFactory::create($this->commentComplexForArgument, 'AnotherAnnotation#bar', Annotation::TARGET_PARAM, 'MyClass::baz()');
        $this->assertInstanceOf('net\\stubbles\\lang\\reflect\\annotation\\Annotation',
                                $emptyAnnotation
        );

        $myAnnotation = AnnotationFactory::create($this->commentComplexForArgument, 'MyAnnotation#bar', Annotation::TARGET_PARAM, 'MyClass::baz()');
        $this->assertInstanceOf('net\\stubbles\\lang\\reflect\\annotation\\Annotation',
                                $myAnnotation
        );
        $this->assertInstanceOf('net\\stubbles\\lang\\reflect\\ReflectionClass',
                                $myAnnotation->getFoo()
        );
    }

    /**
     * test that creating an annotation works as expected
     *
     * @test
     * @expectedException  \ReflectionException
     */
    public function createForArgumentShouldFail()
    {
        AnnotationFactory::create($this->commentComplexForArgument, 'MyAnnotation#baz', Annotation::TARGET_PARAM, 'MyClass::baz()');
    }

    /**
     * test that the cache works as expected
     *
     * @test
     */
    public function reflectionClass()
    {
        $class = new ReflectionClass('net\\stubbles\\lang\\reflect\\annotation\\AnotherTestClass');
        $anno  = $class->getAnnotation('MyAnnotation');
    }

    /**
     * test that the cache works as expected
     *
     * @test
     */
    public function cache()
    {
        $class = new ReflectionClass('net\\stubbles\\lang\\reflect\\annotation\\AnyTestClass');
        $anno  = $class->getAnnotation('MyAnnotation');

        // assert the values read from the annotation
        $this->assertInstanceOf('net\\stubbles\\lang\\reflect\\annotation\\Annotation',
                                $anno
        );
        $this->assertEquals('bar', $anno->getFoo());
        $this->assertTrue($anno->getArgh());
        $this->assertEquals('cucumber', $anno->getVeggie());

        // change the value
        $anno->veggie = 'tomato';
        $this->assertEquals('tomato', $anno->getVeggie());

        // re-fetch
        $anno2  = $class->getAnnotation('MyAnnotation');
        $this->assertInstanceOf('net\\stubbles\\lang\\reflect\\annotation\\Annotation',
                                $anno2
        );
        $this->assertEquals('bar', $anno2->getFoo());
        $this->assertTrue($anno2->getArgh());
        $this->assertEquals('cucumber', $anno2->getVeggie());

        // fetch with a new stubAnnotationClass instance
        $class2 = new ReflectionClass('net\\stubbles\\lang\\reflect\\annotation\\AnyTestClass');
        $anno3  = $class2->getAnnotation('MyAnnotation');

        $this->assertInstanceOf('net\\stubbles\\lang\\reflect\\annotation\\Annotation',
                                $anno
        );
        $this->assertEquals('bar', $anno3->getFoo());
        $this->assertTrue($anno3->getArgh());
        $this->assertEquals('cucumber', $anno3->getVeggie());
    }

    /**
     * ensure that information about a non-existing annotation is cached as well
     *
     * @test
     * @expectedException  \ReflectionException
     */
    public function cachingOfNonExistingAnnotations()
    {
        // make sure that the information is really not in the cache
        AnnotationCache::flush();
        $this->assertFalse(AnnotationCache::has(Annotation::TARGET_CLASS, 'MyClass', 'NonExistingAnnotation'));
        $this->assertFalse(AnnotationCache::hasNot(Annotation::TARGET_CLASS, 'MyClass', 'NonExistingAnnotation'));
        try {
            $annotation = AnnotationFactory::create($this->commentComplex, 'NonExistingAnnotation', Annotation::TARGET_CLASS, 'MyClass');
        } catch (Exception $e) {
            $this->assertFalse(AnnotationCache::has(Annotation::TARGET_CLASS,'MyClass', 'NonExistingAnnotation'));
            $this->assertTrue(AnnotationCache::hasNot(Annotation::TARGET_CLASS, 'MyClass', 'NonExistingAnnotation'));
            // now the exception will be thrown
            $annotation = AnnotationFactory::create($this->commentComplex, 'NonExistingAnnotation', Annotation::TARGET_CLASS, 'MyClass');
            return;
        }

        $this->fail('Found NonExistingAnnotation while this annotation should not be present.');
    }

    /**
     * use the annotation but without any prefix and without the postfix "Annotation"
     *
     * @test
     */
    public function noPrefixNoAnnotationInAnnotation()
    {
        $class = new ReflectionClass('net\\stubbles\\lang\\reflect\\annotation\\OneMoreTestClass');
        $this->assertTrue($class->hasAnnotation('My'));
        $anno  = $class->getAnnotation('My');
        $this->assertInstanceOf('net\\stubbles\\lang\\reflect\\annotation\\Annotation',
                                $anno
        );
        $this->assertEquals('bar', $anno->getFoo());
    }
}
?>