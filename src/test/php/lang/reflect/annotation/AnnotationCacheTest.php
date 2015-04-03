<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\lang\reflect\annotation;
use org\bovigo\vfs\vfsStream;
/**
 * Test for stubbles\lang\reflect\annotation\AnnotationCache.
 *
 * @group  lang
 * @group  lang_reflect
 * @group  lang_reflect_annotation
 */
class AnnotationCacheTest extends \PHPUnit_Framework_TestCase
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
        $annotations = new Annotations('someTarget');
        AnnotationCache::put($annotations);
        AnnotationCache::__shutdown();
        $this->assertTrue(file_exists(vfsStream::url('root/annotations.cache')));
        $data = unserialize(file_get_contents(vfsStream::url('root/annotations.cache')));
        $this->assertTrue(isset($data['someTarget']));
        $this->assertEquals($annotations, unserialize($data['someTarget']));
    }

    /**
     * @since  3.0.0
     * @group  issue_58
     * @test
     */
    public function stoppingAnnotationPersistenceDoesNotWriteCacheFileOnShutdown()
    {
        AnnotationCache::put(new Annotations('someTarget'));
        AnnotationCache::stop();
        AnnotationCache::__shutdown();
        $this->assertFalse(file_exists(vfsStream::url('root/annotations.cache')));
    }

    /**
     * @test
     */
    public function retrieveAnnotationsForUncachedTargetReturnsNull()
    {
        $this->assertNull(AnnotationCache::get('DoesNotExist'));
    }

    /**
     * @since  3.0.0
     * @group  issue_58
     * @test
     * @expectedException  RuntimeException
     */
    public function startAnnotationCacheWithInvalidCacheDataThrowsRuntimeException()
    {
        AnnotationCache::start(function() { return serialize('foo'); }, function() {});
    }

    /**
     * @since  3.0.0
     * @group  issue_58
     * @test
     * @expectedException  RuntimeException
     */
    public function startAnnotationCacheWithNonSerializedCacheDataThrowsRuntimeException()
    {
        AnnotationCache::start(function() { return 'foo'; }, function() {});
    }
}
