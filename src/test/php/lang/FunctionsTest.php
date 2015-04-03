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
use stubbles\lang\reflect\annotation\Annotation;
use stubbles\lang\reflect\annotation\AnnotationCache;
use org\bovigo\vfs\vfsStream;
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
        $this->assertTrue(AnnotationCache::has('foo', 'bar'));
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
        $this->assertTrue(AnnotationCache::has('foo', 'bar'));
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
        $this->assertNull(exception\lastErrorMessage());
    }

    /**
     * @test
     * @since  3.4.2
     */
    public function lastErrorMessageShouldContainLastError()
    {
        @file_get_contents(__DIR__ . '/doesNotExist.txt');
        $this->assertEquals(
                'file_get_contents(' . __DIR__ . '/doesNotExist.txt): failed to open stream: No such file or directory',
                exception\lastErrorMessage()
        );
    }

    /**
     * @test
     * @since  4.0.0
     */
    public function reflectWithMethodNameReturnsReflectionMethod()
    {
        $this->assertInstanceOf(
                'ReflectionMethod',
                reflect(__CLASS__, __FUNCTION__)
        );
    }

    /**
     * @test
     * @since  4.0.0
     */
    public function reflectWithClassNameReturnsReflectionClass()
    {
        $this->assertInstanceOf(
                'ReflectionClass',
                reflect(__CLASS__)
        );
    }

    /**
     * @test
     * @since  4.0.0
     */
    public function reflectWithClassInstanceReturnsReflectionObject()
    {
        $this->assertInstanceOf(
                'ReflectionObject',
                reflect($this)
        );
    }

    /**
     * @test
     * @since  4.0.0
     */
    public function reflectWithFunctionNameReturnsReflectionFunction()
    {
        $this->assertInstanceOf(
                'ReflectionFunction',
                reflect('stubbles\lang\reflect')
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
        $this->assertInstanceOf(
                'ReflectionClass',
                reflect('stubbles\lang\Mode')
        );
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
     * @expectedException  stubbles\lang\exception\IllegalArgumentException
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
        $this->assertInstanceOf(
                'ReflectionMethod',
                reflect([$this, __FUNCTION__])
        );
    }

    /**
     * @test
     * @since  4.1.4
     */
    public function reflectCallbackWithClassnameReturnsReflectionMethod()
    {
        $this->assertInstanceOf(
                'ReflectionMethod',
                reflect([__CLASS__, __FUNCTION__])
        );
    }

    /**
     * @test
     * @since  4.1.4
     */
    public function reflectClosureReturnsReflectionObject()
    {
        $this->assertInstanceOf(
                'ReflectionObject',
                reflect(function() { })
        );
    }

    /**
     * @test
     * @since  4.0.0
     */
    public function ensureCallableDoesNotChangeClosures()
    {
        $closure = function() { return true; };
        $this->assertSame($closure, ensureCallable($closure));
    }

    /**
     * @test
     * @since  4.0.0
     */
    public function ensureCallableDoesNotChangeCallbackWithInstance()
    {
        $callback = [$this, __FUNCTION__];
        $this->assertSame($callback, ensureCallable($callback));
    }

    /**
     * @test
     * @since  4.0.0
     */
    public function ensureCallableDoesNotChangeCallbackWithStaticMethod()
    {
        $callback = [__CLASS__, 'invalidValues'];
        $this->assertSame($callback, ensureCallable($callback));
    }

    /**
     * @test
     * @since  4.0.0
     */
    public function ensureCallableDoesNotWrapUserlandFunction()
    {
        $this->assertSame(
                'stubbles\lang\ensureCallable',
                ensureCallable('stubbles\lang\ensureCallable')
        );
    }

    /**
     * @test
     * @since  4.0.0
     */
    public function ensureCallableWrapsInternalFunction()
    {
        $this->assertInstanceOf('\Closure', ensureCallable('strlen'));
    }

    /**
     * @test
     * @since  4.0.0
     */
    public function ensureCallableAlwaysReturnsSameClosureForSameFunction()
    {
        $this->assertSame(ensureCallable('strlen'), ensureCallable('strlen'));
    }

    /**
     * @test
     * @since  4.0.0
     */
    public function ensureCallableReturnsClosureThatPassesArgumentsAndReturnsValue()
    {
        $strlen = ensureCallable('strlen');
        $this->assertEquals(3, $strlen('foo'));
    }
}
