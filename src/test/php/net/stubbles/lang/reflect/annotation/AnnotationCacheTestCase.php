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
use org\bovigo\vfs\vfsStream;
/**
 * Test for net\stubbles\lang\reflect\annotation\AnnotationCache.
 *
 * @group  lang
 * @group  lang_reflect
 * @group  lang_reflect_annotation
 */
class AnnotationCacheTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * set up test environment
     */
    public function setUp()
    {
        AnnotationCache::flush();
        vfsStream::setup();
        AnnotationCache::startFromFileCache(vfsStream::url('root/annotations.cache'));
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
     */
    public function noAnnotationAddedDoesNotWriteCacheFile()
    {
        AnnotationCache::__shutdown();
        $this->assertFalse(file_exists(vfsStream::url('root/annotations.cache')));
    }

    /**
     * @test
     */
    public function addingAnnotationWritesCacheFile()
    {
        $annotation = new Annotation('annotationName');
        AnnotationCache::put(Annotation::TARGET_CLASS, 'foo', 'annotationName', $annotation);
        AnnotationCache::__shutdown();
        $this->assertTrue(file_exists(vfsStream::url('root/annotations.cache')));
        $data = unserialize(file_get_contents(vfsStream::url('root/annotations.cache')));
        $this->assertTrue(isset($data[Annotation::TARGET_CLASS]));
        $this->assertTrue(isset($data[Annotation::TARGET_CLASS]['foo']));
        $this->assertTrue(isset($data[Annotation::TARGET_CLASS]['foo']['annotationName']));
        $this->assertEquals($annotation, unserialize($data[Annotation::TARGET_CLASS]['foo']['annotationName']));
    }

    /**
     * @since  3.0.0
     * @group  issue_58
     * @test
     */
    public function stoppingAnnotationPersistenceDoesNotWriteCacheFileOnShutdown()
    {
        $annotation = new Annotation('annotationName');
        AnnotationCache::put(Annotation::TARGET_CLASS, 'foo', 'annotationName', $annotation);
        AnnotationCache::stop();
        AnnotationCache::__shutdown();
        $this->assertFalse(file_exists(vfsStream::url('root/annotations.cache')));
    }

    /**
     * @test
     */
    public function retrieveUncachedAnnotationReturnsNull()
    {
        $this->assertNull(AnnotationCache::get(Annotation::TARGET_CLASS, 'DoesNotExist', 'annotationName'));
    }

    /**
     * @since  3.0.0
     * @group  issue_58
     * @test
     * @expectedException  \net\stubbles\lang\exception\RuntimeException
     */
    public function startAnnotationCacheWithInvalidCacheDataThrowsRuntimeException()
    {
        AnnotationCache::start(function() { return serialize('foo'); }, function() {});
    }

    /**
     * @since  3.0.0
     * @group  issue_58
     * @test
     * @expectedException  \net\stubbles\lang\exception\RuntimeException
     */
    public function startAnnotationCacheWithNonSerializedCacheDataThrowsRuntimeException()
    {
        AnnotationCache::start(function() { return 'foo'; }, function() {});
    }
}
?>