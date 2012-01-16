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
use net\stubbles\Bootstrap;
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
        AnnotationCache::refresh();
        vfsStream::setup();
        AnnotationCache::setCacheFile(vfsStream::url('root/annotations.cache'));
    }

    /**
     * clean up test environment
     */
    public function tearDown()
    {
        AnnotationCache::setCacheFile(Bootstrap::getRootPath() . '/cache/annotations.cache');
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
}
?>