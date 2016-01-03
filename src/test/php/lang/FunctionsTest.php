<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\lang;
use stubbles\Environment;
use stubbles\lang\reflect\annotation\Annotation;
use stubbles\lang\reflect\annotation\AnnotationCache;
use org\bovigo\vfs\vfsStream;

use function bovigo\assert\assert;
use function bovigo\assert\assertTrue;
use function bovigo\assert\predicate\isInstanceOf;
/**
 * Tests for stubbles\lang\*().
 *
 * @since  3.1.0
 * @group  lang
 * @group  lang_core
 */
class FunctionsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @since  3.0.0
     * @group  issue_58
     * @test
     */
    public function canEnableFileAnnotationCache()
    {
        $root = vfsStream::setup();
        $file = vfsStream::newFile('annotations.cache')
                         ->withContent(serialize($this->createdCachedAnnotation()))
                         ->at($root);
        persistAnnotationsInFile($file->url());
        assertTrue(AnnotationCache::has('foo', 'bar'));
    }

    /**
     * @since  3.1.0
     * @group  issue_58
     * @test
     */
    public function canEnableOtherAnnotationCache()
    {
        $annotationData = $this->createdCachedAnnotation();
        persistAnnotations(function() use($annotationData)
                           {
                               return $annotationData;
                           },
                           function($data) {}
        );
        assertTrue(AnnotationCache::has('foo', 'bar'));
    }

    /**
     * creates a annotation cache with one annotation
     *
     * @return  string
     */
    private function createdCachedAnnotation()
    {
        return ['foo' => ['bar' => new Annotation('bar', 'someFunction()')]];
    }

    /**
     * clean up test environment
     */
    public function tearDown()
    {
        AnnotationCache::stop();
    }

    /**
     * @test
     * @since  4.0.0
     */
    public function reflectWithMethodNameReturnsReflectionMethod()
    {
        assert(
                reflect(__CLASS__, __FUNCTION__),
                isInstanceOf(\ReflectionMethod::class)
        );
    }

    /**
     * @test
     * @since  4.0.0
     */
    public function reflectWithClassNameReturnsReflectionClass()
    {
        assert(reflect(__CLASS__), isInstanceOf(\ReflectionClass::class));
    }

    /**
     * @test
     * @since  4.0.0
     */
    public function reflectWithClassInstanceReturnsReflectionObject()
    {
        assert(reflect($this), isInstanceOf(\ReflectionObject::class));
    }

    /**
     * @test
     * @since  4.0.0
     */
    public function reflectWithFunctionNameReturnsReflectionFunction()
    {
        assert(
                reflect('stubbles\lang\reflect'),
                isInstanceOf(\ReflectionFunction::class)
        );
    }

    /**
     * @test
     * @expectedException  ReflectionException
     * @since  4.0.0
     */
    public function reflectWithUnknownClassAndFunctionNameThrowsReflectionException()
    {
        reflect('doesNotExist');
    }

    /**
     * @test
     * @since  4.0.0
     */
    public function reflectInterface()
    {
        assert(reflect(Environment::class), isInstanceOf(\ReflectionClass::class));
    }

    /**
     * @return  array
     */
    public static function invalidValues()
    {
        return [[404], [true], [4.04]];
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     * @dataProvider  invalidValues
     * @since  4.0.0
     */
    public function reflectInvalidValueThrowsIllegalArgumentException($invalidValue)
    {
        reflect($invalidValue);
    }

    /**
     * @test
     * @since  4.1.4
     */
    public function reflectCallbackWithInstanceReturnsReflectionMethod()
    {
        assert(
                reflect([$this, __FUNCTION__]),
                isInstanceOf(\ReflectionMethod::class)
        );
    }

    /**
     * @test
     * @since  4.1.4
     */
    public function reflectCallbackWithClassnameReturnsReflectionMethod()
    {
        assert(
                reflect([__CLASS__, __FUNCTION__]),
                isInstanceOf(\ReflectionMethod::class)
        );
    }

    /**
     * @test
     * @since  4.1.4
     */
    public function reflectClosureReturnsReflectionObject()
    {
        assert(
                reflect(function() { }),
                isInstanceOf(\ReflectionObject::class)
        );
    }
}
