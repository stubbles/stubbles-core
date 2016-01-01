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
use function bovigo\assert\assertNull;
use function bovigo\assert\assertTrue;
use function bovigo\assert\predicate\equals;
use function bovigo\assert\predicate\isInstanceOf;
use function bovigo\assert\predicate\isSameAs;
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
     * @since  3.4.2
     */
    public function lastErrorMessageShouldBeNullByDefault()
    {
        assertNull(exception\lastErrorMessage());
    }

    /**
     * @test
     * @since  3.4.2
     */
    public function lastErrorMessageShouldContainLastError()
    {
        @file_get_contents(__DIR__ . '/doesNotExist.txt');
        assert(
                exception\lastErrorMessage(),
                equals('file_get_contents(' . __DIR__ . '/doesNotExist.txt): failed to open stream: No such file or directory')
        );
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

    /**
     * @test
     * @since  4.0.0
     */
    public function ensureCallableDoesNotChangeClosures()
    {
        $closure = function() { return true; };
        assert(ensureCallable($closure), isSameAs($closure));
    }

    /**
     * @test
     * @since  4.0.0
     */
    public function ensureCallableDoesNotChangeCallbackWithInstance()
    {
        $callback = [$this, __FUNCTION__];
        assert(ensureCallable($callback), isSameAs($callback));
    }

    /**
     * @test
     * @since  4.0.0
     */
    public function ensureCallableDoesNotChangeCallbackWithStaticMethod()
    {
        $callback = [__CLASS__, 'invalidValues'];
        assert(ensureCallable($callback), isSameAs($callback));
    }

    /**
     * @test
     * @since  4.0.0
     */
    public function ensureCallableDoesNotWrapUserlandFunction()
    {
        assert(
                ensureCallable('stubbles\lang\ensureCallable'),
                isSameAs('stubbles\lang\ensureCallable')
        );
    }

    /**
     * @test
     * @since  4.0.0
     */
    public function ensureCallableWrapsInternalFunction()
    {
        assert(ensureCallable('strlen'), isInstanceOf(\Closure::class));
    }

    /**
     * @test
     * @since  4.0.0
     */
    public function ensureCallableAlwaysReturnsSameClosureForSameFunction()
    {
        assert(ensureCallable('strlen'), isSameAs(ensureCallable('strlen')));
    }

    /**
     * @test
     * @since  4.0.0
     */
    public function ensureCallableReturnsClosureThatPassesArgumentsAndReturnsValue()
    {
        $strlen = ensureCallable('strlen');
        assert($strlen('foo'), equals(3));
    }
}
